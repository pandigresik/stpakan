'use strict';
var Pengirimanreturpakanfarm = {	
	urlBaru : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/alokasi_retur',
	urlMain : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/index',
	urlUpdateStatus : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/updateStatus',
	urlGenerate : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/generate',
	urlTimbang : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/timbang',
	urlSimpanTimbang : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/simpanTimbang',
	urlCetakSJ : 'rekap_retur_pakan/pengiriman_retur_pakan_farm/cetakSJ',
	_lockTimbangan : 0,
	prosesServer: 0,
	
	_status_kirim : 0,			//status pengiriman row aktif
	_jmldiretur : 0,			//jumlah pakan yang akan di retur
	_sisadiretur:0, 			//jumlah pakan yang belum di timbang <melebihi stok>
	_returSelesai : 0,			
	_row_pengiriman_aktif : 0,	//id row list pengiriman aktif
	_noRetur : 0,				//nomor retur yang aktif
	_select_row_pallet : null,
	_qty_row_pallet : 0, 		//row pallet
	
	//set tabel pengiriman retur pakan farm
	cari: function(){ 
		var _url = 'rekap_retur_pakan/pengiriman_retur_pakan_farm/list_pengiriman_retur';
		var _form = $('form');
		var _belumTindakLanjut = _form.find('.ckTindaklanjut :checkbox').is(':checked') ? 1 : 0;
		var _data = {belumTindakLanjut : _belumTindakLanjut};
		if(!_belumTindakLanjut){ 
			_data['farmAsal'] = _form.find('.farm_asal select').val();
			_data['farmTujuan'] = _form.find('.farm_tujuan select').val();
			var _startDate = $('input[name=startDate]').datepicker('getDate');
			var _endDate = $('input[name=endDate]').datepicker('getDate');
			if(_startDate != null){
				var _tglMulai = [_startDate.getFullYear(),_startDate.getMonth() + 1, _startDate.getDate()];
				_data['startDate'] = _tglMulai.join('-');
			}
			if(_endDate != null){
				var _tglAkhir = [_endDate.getFullYear(),_endDate.getMonth() + 1, _endDate.getDate()];
				_data['endDate'] = _tglAkhir.join('-');
			}
		}
		$.get(_url,_data,function(html){
			$('#div_pengiriman_retur_pakan').html(html);
		},'html');
	},
	cariReturTimbang: function(){
		var _url = 'rekap_retur_pakan/pengiriman_retur_pakan_farm/list_retur_timbang';
		var _form = $('form');
		var _belumTindakLanjut = _form.find('.ckTindaklanjut :checkbox').is(':checked') ? 1 : 0;
		var _data = {belumTindakLanjut : _belumTindakLanjut};
		if(!_belumTindakLanjut){
			_data['farmAsal'] = _form.find('.farm_asal select').val();
			_data['farmTujuan'] = _form.find('.farm_tujuan select').val();
			var _startDate = $('input[name=startDate]').datepicker('getDate');
			var _endDate = $('input[name=endDate]').datepicker('getDate');
			if(_startDate != null){
				var _tglMulai = [_startDate.getFullYear(),_startDate.getMonth() + 1, _startDate.getDate()];
				_data['startDate'] = _tglMulai.join('-');
			}
			if(_endDate != null){
				var _tglAkhir = [_endDate.getFullYear(),_endDate.getMonth() + 1, _endDate.getDate()];
				_data['endDate'] = _tglAkhir.join('-');
			}
		}
		$.get(_url,_data,function(html){
			$('#div_pengiriman_retur_pakan').html(html);
		},'html');
	},
	//end set tabel pengiriman retur pakan farm
	
	/*select row pengiriman*/
	set_pengiriman: function(elm){
		var this_row_id = elm.getAttribute('id');
		Pengirimanreturpakanfarm._status_kirim = 0;
		Pengirimanreturpakanfarm._row_pengiriman_aktif = this_row_id;
		$('#'+this_row_id).css('background','#95BCF2');
		$('#'+this_row_id).addClass('terpilih');
		$('#'+this_row_id).find('.input_nama_sopir').removeClass('hide');
		Pengirimanreturpakanfarm.timbang(elm);
		$('#'+this_row_id).find('.input_nama_sopir').focus();
		Pengirimanreturpakanfarm._jmldiretur = $(elm).data('jmlretur');
	},
	/*end select row pengiriman*/
	
	/*history pengiriman*/
	lihat_detail_kirim : function(elm){
		Pengirimanreturpakanfarm._status_kirim = 1;
		Pengirimanreturpakanfarm._noRetur = $(elm).data('noretur');
		$.get('rekap_retur_pakan/pengiriman_retur_pakan_farm/tabel_timbang', {
		no_retur : Pengirimanreturpakanfarm._noRetur, kirim : '1'}, 
		function(data){
			$('#div_detail_pengiriman_retur_pakan').html(data);
		}, 'html');
	},
	/*end history pengiriman*/
	
	/*get rekomendasi platnomor*/
	get_platnomor: function(elm){ 
		var row_pengiriman = Pengirimanreturpakanfarm._row_pengiriman_aktif; 
		var sopir = $('#'+row_pengiriman).find('.input_nama_sopir').val();
		if(!empty(sopir)){
			var log_nopol=[];
			$.ajax({
				url: 'rekap_retur_pakan/pengiriman_retur_pakan_farm/get_rekomendasi_nopol',
				method: 'post',
				dataType: 'json',
				success: function(data){
					for(var i=0;i<data.length;i++){
						log_nopol.push(data[i]['NOPOL']);
					}
					$('#'+row_pengiriman).find('.select_kendaraan').autocomplete({
						source: log_nopol
					});
				}
			});
			$('#'+row_pengiriman).find('.select_kendaraan').removeClass('hide');
		}else{
			$('#'+row_pengiriman).removeClass('terpilih');
			if(Pengirimanreturpakanfarm._status_kirim == 0){
				$('#'+row_pengiriman).css('background', '#EA9999');
			}
			$('#'+row_pengiriman).find('.input_nama_sopir').addClass('hide');
			$('#div_detail_pengiriman_retur_pakan').empty();
			$('#'+row_pengiriman).find('.select_kendaraan').val('');
			$('#'+row_pengiriman).find('.select_kendaraan').addClass('hide');
		}
	},
	/*end get rekomendasi platnomor*/
	
	/*set capital dan huruf*/
	set_huruf : function(elm){		
		elm.value = elm.value.charAt(0).toUpperCase() + elm.value.slice(1);
		elm.value = elm.value.replace(/[^a-zA-Z@]+/, '');
	},
	/*end set capital dan huruf*/
	
	/*set uppercase*/
	setUppercase : function(elm){
		elm.value = elm.value.toUpperCase();
	},
	/*end set uppercase*/
	
	/*timbang pallet*/
	timbang: function(elm){
		Pengirimanreturpakanfarm._noRetur = $(elm).data('retur');
		var kodepakan = $(elm).find('.jenis_pakan').data('pakan');
		$.get(
			'rekap_retur_pakan/pengiriman_retur_pakan_farm/tabel_timbang', 
			{no_retur : Pengirimanreturpakanfarm._noRetur, kode_pakan : kodepakan, kirim:'0'}, 
			function(data){
				$('#div_detail_pengiriman_retur_pakan').html(data);
			}, 
		'html');
	},
	/*end timbang pallet*/
	
	/*get berat timbang*/
	get_data_timbang: function(elm){
		//if(Pengirimanreturpakanfarm._lockTimbangan == 0){
			$.get('rekap_retur_pakan/pengiriman_retur_pakan_farm/get_berat_timbang', 
			function(data){
				var dataParse = JSON.parse(data); 
				if(dataParse.status == 1){ 
					var beratTimbang = dataParse.content;
					$(elm).val(parseFloat(beratTimbang).toFixed(3));
					var thisRowID = $(elm).data('rowid');
					Pengirimanreturpakanfarm._select_row_pallet = thisRowID;
					
					var beratPallet = $('#'+thisRowID+' .berat_pallet').data('beratpallet');
					
					var hitung_bb = beratTimbang - beratPallet;
					var rata_rata_kavling = $('#'+thisRowID).find('.berat_bersih').data('ratakavling');
					var hitung_sak = parseFloat(hitung_bb)/parseFloat(rata_rata_kavling);
					hitung_sak = Math.round(hitung_sak);
					var jml_onhand = $('#'+thisRowID+' .jml_on_hand').data('onhand');
					//alert(hitung_sak);					
					//if(hitung_sak == jml_onhand){
						var retur = Pengirimanreturpakanfarm._jmldiretur;
						if(retur > jml_onhand){
							Pengirimanreturpakanfarm._sisadiretur = retur - jml_onhand;
							retur = jml_onhand
						}
						
						var sisaPallet = parseInt(jml_onhand)-parseInt(retur);
						$('#'+thisRowID+' .berat_bersih').html(parseFloat(hitung_bb).toFixed(3));
						$('#'+thisRowID+' .jml_on_hand').html(jml_onhand);
						$('#'+thisRowID+' .jml_kirim').html(retur);
						$('#'+thisRowID+' .jml_sisapallet').html(sisaPallet);
						$('#'+thisRowID+' .scan_barcode .input_barcode').removeClass('hide');
						$(elm).parents('tr.tr-sub-detail').next().addClass('hide');  
					//}
				}else{
					var _message = "<center><label>"+dataParse.message+"</label></center>";
					var box = bootbox.dialog({
						message : _message,
						closeButton: true,
						title : "Alert",
						onEscape : function(){
							Pengirimanreturpakanfarm._timer = false;
							return true;
						},
						buttons : {
						  success : {
							label : "<i class='glyphicon glyphicon-refresh'></i> Refresh",
							className : "btn-primary",
							callback : function() {
							  $('.bootbox').modal('hide');	
							  return true;
							}
						  }
						}
					});
				}
			},'html');
		//}
	},
	/*end get berat timbang*/
	
	
	showDetailTimbang: function(elm){
		var _tr = $(elm).closest('tr');
		var _tr_detail = _tr.next('tr.detail_kavling');
		if(_tr_detail.is(':visible')){
			_tr_detail.addClass('hide');
		}else{
			_tr_detail.removeClass('hide');
		}
		$(elm).toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
	},
	
	/*cek barcode pallet*/
	barcode_pallet_check: function(elm){
		var rowpallet = Pengirimanreturpakanfarm._select_row_pallet;
		var kodePallet = $('#'+rowpallet+' .kode_pallet').html();
		$('#'+rowpallet+' .scan_barcode').find('p').empty();
		if($(elm).val() == kodePallet){
			$('#'+rowpallet+' .scan_barcode .input_barcode').addClass('hide');
			$('#'+rowpallet+' .scan_barcode .input_barcode').val('');
			Pengirimanreturpakanfarm.submit();
		}else{
			$('#'+rowpallet+' .scan_barcode').append("<p style='color:red;'>Barcode yang di-scan tidak sesuai</p>");
		}
	},
	/*end cek barcode pallet*/
	
	
	

	//admin gudang finger
	_timer : null,
	_date_transaction : null,
	submit : function(elm){ 
		Pengirimanreturpakanfarm.fingerprint();
    },
	fingerprint : function(){
      Pengirimanreturpakanfarm.simpan_transaksi_verifikasi(function(result){
        if(result.date_transaction){
			var _message = "<center><img src='assets/images/finger.jpg' height='260px' style='filter:invert(100%);'></center>";
			var box = bootbox.dialog({
				message : _message,
				closeButton: true,
				title : "Fingerprint",
				onEscape : function(){
					Pengirimanreturpakanfarm._timer = false;
					var rowpallet = Pengirimanreturpakanfarm._select_row_pallet;
					$('#'+rowpallet+' .scan_barcode .input_barcode').removeClass('hide');
					return true;
				}
			});
			box.bind('shown.bs.modal', function(){
				Pengirimanreturpakanfarm._timer = true;
				Pengirimanreturpakanfarm._date_transaction = result.date_transaction;
				Pengirimanreturpakanfarm.cek_verifikasi(result.date_transaction);
			});
        }
      });
    },
	//end admin gudang finger
	
	//insert verification_fingerprint
	simpan_transaksi_verifikasi : function(callback){
      $.ajax({
        type : "POST",
        url : "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
        data : {
          transaction : 'pengiriman_retur_pakan_farm'
        },
        dataType : 'json',
        success : function(data) {
          callback(data);
        }
      });
    },
	//end insert verification_fingerprint
	
	
	//cetak surat jalan
	cetakSJretur : function(){ 
		var no_retur = Pengirimanreturpakanfarm._noRetur;
		if(!empty(no_retur)){
			var _url = 'rekap_retur_pakan/pengiriman_retur_pakan_farm/cetakSJ';
			$.redirect(_url,{no_retur : no_retur},'POST','_blank');
		}
	},
	//end cetak surat jalan

	//cek verifikasi finger
    cek_verifikasi : function(date_transaction){
		Pengirimanreturpakanfarm._date_transaction = date_transaction;
        if (Pengirimanreturpakanfarm._timer == true) {
            $.ajax({
                type : "POST",
                url : "rekap_retur_pakan/pengiriman_retur_pakan_farm/check_admin_verifikator",
				data : {
                    date_transaction : Pengirimanreturpakanfarm._date_transaction
                },
                dataType : 'json',
                success : function(data) {
					if(data['status'] == 1){
						$('.bootbox').modal('hide');
						if(data['match'] == 1){
							Pengirimanreturpakanfarm._timer = false;
							Pengirimanreturpakanfarm._lockTimbangan = 1; //perlu cek
							
							var rowpallet = Pengirimanreturpakanfarm._select_row_pallet;
							$('#'+rowpallet).addClass('pilih_pallet');
							var qty_pallet = Pengirimanreturpakanfarm._qty_row_pallet;
							Pengirimanreturpakanfarm._qty_row_pallet = parseInt(qty_pallet) + 1;
							$('#'+rowpallet).addClass('retur'+qty_pallet);
							$('#'+rowpallet+' .scan_barcode .input_barcode').addClass('hide');
							$('#'+rowpallet+' .scan_barcode .btn_reset').removeClass('hide');
							$('#'+rowpallet+' .nama_admin').html(data['nama_pegawai']);
							Pengirimanreturpakanfarm._returSelesai = 1;
						}else{
							var _message = 
							"<center><h1 class='glyphicon glyphicon-remove-sign'style='color:red;font-size:20vw;'>"
							+"</h1></center>";
							var box = bootbox.dialog({
							message : _message,
							closeButton: true,
							title : "Fingerprint",
							onEscape : function(){
								Pengirimanreturpakanfarm._timer = false;
								return true;
							},
							buttons : {
							  success : {
								label : "<i class='glyphicon glyphicon-refresh'></i> Refresh",
								className : "btn-primary",
								callback : function() {
								  $('.bootbox').modal('hide');	
									Pengirimanreturpakanfarm.submit();
								  return true;
								}
							  }
							}
							});
						}
						Pengirimanreturpakanfarm._timer = false;
					}else{
						Pengirimanreturpakanfarm._timer = true;
						setTimeout("Pengirimanreturpakanfarm.cek_verifikasi('"+Pengirimanreturpakanfarm._date_transaction+"')", 1000);
					}
				}
            });
        }
    },
	//end cek verifikasi finger
	
	
	//selesai input dan timbang
	inputSelesai:function(){
		var row_pengiriman = Pengirimanreturpakanfarm._row_pengiriman_aktif;
		if(Pengirimanreturpakanfarm._returSelesai){
			var sopir = $('#'+row_pengiriman).find('td .input_nama_sopir').val(); 
			var nopol = $('#'+row_pengiriman).find('td .select_kendaraan').val(); 
			var set_pallet = $('#tabel_list_pallet .pilih_pallet');
			var classid = 0;
			var datapallet = [];
			set_pallet.each(function(){
				var kodepakan = $('#tabel_list_pallet .retur'+classid).data('kodepakan');
				var kodepallet = $('#tabel_list_pallet .retur'+classid).find('.kode_pallet').html();
				var jmlretur = $('#tabel_list_pallet .retur'+classid).find('.jml_kirim').html();
				var berat = $('#tabel_list_pallet .retur'+classid).find('.berat_bersih').html();
				var berat_tersedia = $('#tabel_list_pallet .retur'+classid).find('.berat_timbang').data('beratavailable');
				var berat_sisa = parseFloat(berat_tersedia) - parseFloat(berat);
				
				datapallet.push({kode_pakan:kodepakan, kode_pallet:kodepallet, jml_retur:jmlretur, berat_bersih:berat, berat_available:berat_sisa}); 
				classid++;
			});
			
			Pengirimanreturpakanfarm.update_retur_farm(sopir, nopol, datapallet);
		}
	},
	update_retur_farm:function(sopir, nopol, datapallet){
		$.ajax({
			type : "POST",
			url : "rekap_retur_pakan/pengiriman_retur_pakan_farm/simpan_pengiriman",
			data : {
				'sopir' 		: sopir,
				'nopol' 		: nopol,
				'no_retur' 		: Pengirimanreturpakanfarm._noRetur, 
				'pallet'		: datapallet
			},success : function(data) {
					//alert(data);
					Home.replace_main_content('rekap_retur_pakan/pengiriman_retur_pakan_farm/index');
				}
		  });
	},
	//end selesai input dan timbang

	
	
	kontrol_timbangan: function(elm) {
		var _tr = $(elm).closest('tr');			
		var berat = $(elm).val();
		var berat_bersih = 0, jumlah = 0;	
		if(berat){
			var berat_pallet = _tr.find('td.kode_pallet').data('berat-pallet');
			berat_bersih = parseFloat(berat) - parseFloat(berat_pallet);
			if(berat_bersih < 0){
				toastr.warning('Berat timbang harus lebih besar dari berat pallet.','Informasi');				
			}
			else{
				berat_bersih = berat_bersih.toFixed(3);
				this.cek_konversi(berat_bersih, function(data) {
					var result = 0;					
					if(data){
						jumlah = data.JML_SAK;						
					}	
					_tr.find('td.berat-bersih').text(berat_bersih);							
					Pengirimanreturpakanfarm.berat_diluar_toleransi(elm,jumlah);					
				});
			}			
		}
		
	},	
	berat_diluar_toleransi: function (elm,jumlah) {
		var _tr = $(elm).closest('tr');	
		var _jml_sak_pallet = _tr.find('td.kode_pallet').data('jml-sak');				
		var konfirmasi = 0;
		var keterangan = '';
		var jumlah_aktual = '';
		var sisa_sak;
		var _message = '<div class="form-group form-horizontal new-line">';
			_message += '<div class="form-group">';
			_message += '<label class="col-sm-5 control-label">Konversi Timbangan (Sak)</label>';
			_message += '<div class="col-sm-5">';
			_message += '<label class="control-label">'	+ jumlah + '</label>';
			_message += '</div></div>';
			_message += '<div class="form-group">';
			_message += '<label class="col-sm-5 control-label">Jumlah Sak Aktual</label>';
			_message += '<div class="col-sm-5">';
			_message += '<input type="text" placeholder="Jumlah Sak Aktual" id="jumlah_aktual" class="form-control" onchange="Pengirimanreturpakanfarm.kontrol_sak_aktual('+_jml_sak_pallet+')">';
			_message += '</div></div>';							
			_message += '</div>';
			var box_status = 0;
			var box = bootbox.dialog({
						message : _message,
						title : "Konfirmasi Sak",
						buttons : {
							success : {
								label : "Simpan",
								className : "btn-success",
								callback : function() {
									jumlah_aktual = $('#jumlah_aktual').val();
									if (!jumlah_aktual || jumlah_aktual <= 0) {
										$('#jumlah_aktual').focus().select();
									//	toastr.error('Jumlah Aktual Sak harus diisi.','Peringatan');
										return false;
									} else {
										konfirmasi = 1;
										return true;
										}
								}
							}
						}
					});
			box.bind('shown.bs.modal', function() {
			$('#jumlah_aktual').focus().select();
			$('#jumlah_aktual').numeric({
					allowPlus : false, // Allow the + sign
					allowMinus : false, // Allow the - sign
					allowThouSep : false, // Allow the
					allowDecSep : false
				});
			});
			box.bind('hidden.bs.modal', function() {
						if(konfirmasi == 1){
							if(jumlah == jumlah_aktual){
								keterangan = 'Selesai';
							}else{
								keterangan = '<p>Sak konversi timbang = '+jumlah+' sak</p><p>Sak aktual = '+jumlah_aktual+' sak</p>';
							}	
							sisa_sak = jumlah_aktual - _tr.find('td.jumlah-kirim').data('jumlah-kirim');						
							_tr.find('td.jumlah-sak').text(jumlah_aktual);
							_tr.find('td.jumlah-kirim').text(_tr.find('td.jumlah-kirim').data('jumlah-kirim'));
							_tr.find('td.keterangan').html(keterangan);		
							_tr.find('td.reset').removeClass('hide');	
							_tr.find('td.sisa-sak').text(sisa_sak);															
						}
					});
	},
	cek_konversi: function(berat, callback) {
		if (!empty(berat)) {
			berat = parseFloat(berat);
			$.ajax({
				type : "POST",
				url : "pengambilan_barang/transaksi/cek_konversi",
				data : {
					berat : berat
				},
				dataType : 'json',
				success : function(data) {
					callback(data);
				}
			});
		} else {
			callback(2);
		}
	},
	
	kontrol_sak_aktual: function(_jml_sak_pallet) {
		var jumlah_aktual = $('#jumlah_aktual').val();
		var jumlah_stok_kavling = _jml_sak_pallet;
		if(parseInt(jumlah_aktual) != parseInt(jumlah_stok_kavling)){
			toastr.warning('Jumlah Aktual tidak sama dengan sisa Aktual Kavling.');
			$('#jumlah_aktual').val('');
			$('#jumlah_aktual').focus().select();
		}
	},
	
	
	/*reset hitung timbang*/
	reset_hitung_timbang: function(elm){
		$(elm).addClass('hide');
		var rowpallet = Pengirimanreturpakanfarm._select_row_pallet;
		$('#'+rowpallet+' .berat_bersih').html('');
		$('#'+rowpallet+' .jml_on_hand').html('');
		$('#'+rowpallet+' .jml_kirim').html('');
		$('#'+rowpallet+' .jml_sisapallet').html('');
		$('#'+rowpallet+' .nama_admin').html('');
		$('#'+rowpallet+' .berat_timbang .val_berat_timbang').val('');
		Pengirimanreturpakanfarm._qty_row_pallet  = parseInt(Pengirimanreturpakanfarm._qty_row_pallet) - 1;
		//Pengirimanreturpakanfarm._lockTimbangan = 1;
	},
	/*end reset hitung timbang*/
	
	
	simpanTimbang: function(elm){
		/** pastikan sudah melakukan penimbangan semua */
		var _elm, _val, _error = 0, _data = [], _tmp, _tr;
		$('input[name=berat-timbang]').each(function(){			
			_elm = $(this);
			_val = $.trim(_elm.val());
			_tr = _elm.closest('tr');
			_tmp = {
				no_referensi : _tr.data('no-referensi'),
				berat_bersih : _tr.find('td.berat-bersih').text(),
				no_pallet : _tr.find('td.kode_pallet').data('no-pallet'),
				jumlah_aktual : _tr.find('td.jumlah-sak').text(),
				jml_pick : _tr.find('td.jumlah-kirim').text()
			};
			_data.push(_tmp);
			if(empty(_val)){
				_error++;
			}
			if(_error){
				return false;
			}
		});

		if(_error){
			toastr.warning('Proses penyimpanan berhenti, harap menyelesaikan proses timbang.','Informasi');				
			return;
		}
		
		var _ini = this;
		if(_ini.prosesServer){
			bootbox.alert('Masih menunggu response dari server ...');
			return;
		}

		Pengirimanreturpakanfarm.konfirmasi_pengiriman(_data);
	
	},
	simpanTimbangServer: function(_data,_infoKendaraan){
		var _ini = this;
		$.ajax({
			url: _ini.urlSimpanTimbang,
			beforeSend: function(){
				_ini.prosesServer = 1;
			},
			method: 'post',
			data: {data : _data, kendaraan : _infoKendaraan},
			success: function(data){
				if(data.status){
					bootbox.alert(data.message,function(){
						$('#tabellistretur tr.terpilih').dblclick();
					});					
				}else{
					bootbox.alert(data.message);
				}
			},
			dataType: 'json'
		}).done(function(){
			_ini.prosesServer = 0;
		});
	},
	
	konfirmasi_pengiriman: function (_data) {				
		var keterangan = '';
		var jumlah_aktual = '';
		var sisa_sak;
		var _message = '<div class="form-group form-horizontal new-line">';
			_message += '<div class="form-group">';
			_message += '<label class="col-sm-5 control-label">Nama Sopir</label>';
			_message += '<div class="col-sm-5">';
			_message += '<input type="text" id="nama_sopir" class="form-control">';
			_message += '</div></div>';
			_message += '<div class="form-group">';
			_message += '<label class="col-sm-5 control-label">No. Kendaraan</label>';
			_message += '<div class="col-sm-5">';
			_message += '<input type="text" id="no_kendaraan" class="form-control">';
			_message += '</div></div>';							
			_message += '</div>';
			var box_status = 0;
			var box = bootbox.dialog({
						message : _message,
						title : "Konfirmasi Pengiriman",
						buttons : {
							success : {
								label : "Simpan",
								className : "btn-success disabled btn-konfirmasi-pengiriman",
								callback : function() {
									var _infoKendaraan = {
										'sopir' : $('#nama_sopir').val(),
										'kendaraan' : $('#no_kendaraan').val(),
									};
									Pengirimanreturpakanfarm.simpanTimbangServer(_data,_infoKendaraan);					
								}
							}
						}
					});
			box.bind('shown.bs.modal', function() {
				$('#nama_sopir').focus().select();
				$('#no_kendaraan').alphanum({allowSpace:false});
				$('#nama_sopir').alpha();
				$('#nama_sopir,#no_kendaraan').keyup(function(){
					var _n = $(this).val();
					$(this).val(_n.toUpperCase());
				});
				/** jika sudah diisi nama sopir dan no kendaraan maka enable tombol simpan */
				$('input').change(function(){
					var _form = $(this).closest('form');
					var _sudahInput = 0;
					var _jmlInput = _form.find('input').length;
					_form.find('input').each(function(){
						if(!empty($(this).val())){
							_sudahInput++;
						}
					});	
					if(_sudahInput == _jmlInput){
						$('.btn-konfirmasi-pengiriman').removeClass('disabled');
					}else{
						$('.btn-konfirmasi-pengiriman').addClass('disabled');
					}
				});
			});			
	},	

};

$(function(){		
	$('input[name=startDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		},		
	});
	$('input[name=endDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		},		
	});	

	$('.ckTindaklanjut :checkbox').click(function(){
		var _elm = $(this);
		var _form_group = _elm.closest('.form-group').siblings();
		if(_elm.is(':checked')){
			if(_elm.data('tipe') == "gudang"){
				Pengirimanreturpakanfarm.cariReturTimbang();			
			}else{
				Pengirimanreturpakanfarm.cari();			
			}			
			_form_group.find('input,select').prop('disabled',1);
			_form_group.find('span.btn').addClass('disabled');
		}else{
			_form_group.find('input,select').prop('disabled',0);
			_form_group.find('span.btn').removeClass('disabled');
		}	
	});
	$('.ckTindaklanjut :checkbox').trigger('click');

	var optionFarm = $('#list_farm select>option');
	if(optionFarm.length == 2){
		$('#list_farm h3').html('FARM '+optionFarm.last().text());
	}

	$(document).on('change', '.btn-file :file', function() {
		var input = $(this);
		var _file = input.get(0).files[0];		
		//if($(this).val() != ''){			
			if(_file.type !== 'application/pdf'){				
				bootbox.alert("File yang dapat diunggah hanya dengan format pdf",function(){
					$('#lampirkan-file').val('');
				});
				return false;									
			}else{
				var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
				$('#lampirkan-file').val(label);
			}
		//}				     
    });
}());
