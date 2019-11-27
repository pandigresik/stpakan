'use strict';
var Penerimaanreturpakanfarm = {	
	urlBaru : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/alokasi_retur',
	urlMain : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/index',
	urlUpdateStatus : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/updateStatus',
	urlGenerate : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/generate',
	urlTimbang : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/timbang',
	urlSimpanTimbang : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/simpanTimbang',
	urlCetakSJ : 'rekap_retur_pakan/penerimaan_retur_pakan_farm/cetakSJ',
	_lockTimbangan : 0,
	prosesServer: 0,
	
	/*_jmldiretur : 0,
	_returSelesai : 0,
	_idRowPengiriman : 0,
	_noRetur : 0,
	_nopol : 0,
	_sopir : 0,
	_noPallet : 0,
	_palletStok : 0,
	_kode_pallet : null,*/
	
	_id_pakan : null,
	_berat_timbang : 0,					//berat timbang pakan
	//_berat_pallet_timbang : 0,			//berat pallet timbang
	//_no_retur:null,						//nomor retur
	//_kavling_pallet : null,
	_jml_sak_retur : 0,					//jumlah sak yang di retur
	_konfirmasi_jml_aktual_retur : 0,
	_rowid_detail_alokasi : 1,
	_jml_sak_timbang : 0,				//jumlah sak yang di timbang
	_row_alokasi_retur : 1,				//row alokasi retur aktif
	_no_kandang : 0,					//no kandang terpilih
	_no_flok : 0,						//no flok terpilih
	_farm : null,						//farm penerima
	_kode_siklus:null,					//kode siklus farm penerima
	_kode_pakan:null,					//kode pakan diterima
	_select_pakan_row:null,				//list penerimaan pakan
	
	_tr_detail_alokasi : 1,				//id row detail alokasi	<set_alokasi1>	
	
	//baru
	_row_timbang 			: '',
	_nopol_kirim 			: '',
	_sopir 					: '',
	_nopol 					: '',
	_ket_beda_nopol 		: '',
	_no_retur 				: '',
	_select_pallet			: '',
	_select_pallet_berat	: 0,
	_select_berat_timbang	: 0,
	
	//end baru
	
	//filter by SJ
	scan_sj : function(elm){
		
	},
	//end filter by SJ
	
	/*load*/
	cari: function(){ 
		var _url = 'rekap_retur_pakan/penerimaan_retur_pakan_farm/tabel_penerimaan_pakan';
		var _form = $('form');
		var _belumTindakLanjut = _form.find('.ckTindaklanjut :checkbox').is(':checked') ? 1 : 0;
		var _data = {belumTindakLanjut : _belumTindakLanjut};
		var _no_sj = $('input[name=no_sj]').val();
		if(!_belumTindakLanjut){ 
			_data['farmAsal'] = _form.find('.farm_asal select').val();
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
		_data['no_sj'] = _no_sj;
		$.get(_url,_data,function(html){
			$('#div_penerimaan_retur_pakan').html(html);
		},'html');
	},
	/*end load*/
	
	/*load gudang*/
	cariReturTimbang: function(){
		var _url = 'rekap_retur_pakan/penerimaan_retur_pakan_farm/list_retur_timbang';
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
	/*end load gudang*/
	
	//ok
	show_detail_penerimaan:function(elm){
		var noretur = $(elm).data('noretur');
		Penerimaanreturpakanfarm._no_retur = noretur;
		$.get('rekap_retur_pakan/penerimaan_retur_pakan_farm/detail_sj',{no_retur : noretur},function(data){
			$('#panel_daftar_penerimaan').hide();
			$('#div_detail_penerimaan_sj').html(data);
		},'html');
		$('#detail_penerimaan_area').removeClass('hide');
	},
	
	//ok
	set_btn_verifikasi:function(){ 
		var sopir = $('#sopir_datang').val();
		var nopol = $('#nopol_datang').val();
		if(nopol != '' && sopir != ''){
			$('#btn_verifikasi_sj').removeAttr('disabled');
		}else{
			$('#btn_verifikasi_sj').attr('disabled', true);
		}		
	},
	
	//ok
	verifikasi_penerimaan_retur:function(){
		Penerimaanreturpakanfarm._nopol = $('#tabeldetailpenerimaan .nopol select').val();
		Penerimaanreturpakanfarm._nopol_kirim = $('#tabeldetailpenerimaan #tbl_sj').data('nopol');
		
		var _nopol_datang = Penerimaanreturpakanfarm._nopol;
		var _nopol_pengirim = Penerimaanreturpakanfarm._nopol_kirim;
		
		if(_nopol_datang != ''){
			if(_nopol_datang === _nopol_pengirim){
				Penerimaanreturpakanfarm.set_tabel_timbang_penerimaan();
			}else{
				bootbox.dialog({
					title: 'Perbedaan Nopol Terima',
					message : "<div class='form-group'>"
							+ "<div class='col-xs-2'></div>"
							+ "<div class='col-xs-8'>"
							+ "<center>Masukkan alasan <b>Nopol Terima</b><br>berbeda dengan <b>Nopol Kirim</b></center>"
							+ "<input type='text' id='input_ket_beda_nopol' class='form-control' value=''>"
							+ "</div>"
							+ "<div class='col-xs-2'></div>",
					show: false,
					buttons: [
						{
							label: "Ok",
							className: "btn btn-primary pull-center",
							callback: function() {
								Penerimaanreturpakanfarm._ket_beda_nopol = $('#input_ket_beda_nopol').val();
								Penerimaanreturpakanfarm.set_tabel_timbang_penerimaan();
							}
						}
					]
				}).modal('show');
			}
		}
	},
	
	set_tabel_timbang_penerimaan:function(){
		$.get('rekap_retur_pakan/penerimaan_retur_pakan_farm/alokasi_penerimaan_retur',
		{no_retur : Penerimaanreturpakanfarm._no_retur},
		function(data){
			$('#div_detail_alokasi_penerimaan').html(data);
		},'html');
	},
	
	set_alokasi_kandang:function(elm){ 
		var siklus_terima = $(elm).data('siklus');
		Penerimaanreturpakanfarm._kode_siklus = siklus_terima;
		var kodepakan = $(elm).data('kodepakan');
		Penerimaanreturpakanfarm._kode_pakan = kodepakan;
		
		var kandangflok = $(elm).val().split('_');
		var kandang_terima = parseInt(kandangflok[0]);
		var flok_terima = kandangflok[1];
		var tr_id = '#'+$(elm).data('table')+' #'+$(elm).data('rowid'); 
		 
		$.ajax({
			url: 'rekap_retur_pakan/penerimaan_retur_pakan_farm/set_default_kavling',
			method: 'post',
			dataType: 'json',
			data: {
				kode_siklus 	: '251',
				kode_kandang	: '1', 
				kode_flok		: flok_terima,
				kode_pakan		: '1127-10-12'
			},
			success: function(data){ 
				//Penerimaanreturpakanfarm._kavling_pallet = data[0]['kode_kavling'];
				//alert(JSON.stringify(data));
				var beratpallet = 0;
				//Penerimaanreturpakanfarm._berat_pallet_timbang = beratpallet;
				var kodepallet = data[0]['kode_kavling'];
				var nokavling = data[0]['no_kavling'];
				
				$(tr_id+' .no_pallet').html('<a href="javascript:void(0)" onClick="Penerimaanreturpakanfarm.ganti_pallet(this)">'
					+kodepallet+'</a>');
					//+Penerimaanreturpakanfarm._kavling_pallet+'</a>');
				$(tr_id+' .no_pallet').attr('data-nokavling', nokavling);
				
				if(Penerimaanreturpakanfarm._select_pallet == kodepallet){
					var lastBeratTimbang 	= 0;
					var alokasi_numrow 		= 0;
					var thisBerat 			= [];
					$('#'+$(elm).data('table')+' .set_alokasi').each(function(){
						alokasi_numrow++;
						thisBerat.push($('#'+$(elm).data('table')+' #set_alokasi'+alokasi_numrow+' .alokasi_berat_timbang').html());
					});
					lastBeratTimbang = thisBerat[thisBerat.length-2];
					Penerimaanreturpakanfarm._select_berat_timbang = lastBeratTimbang;
					beratpallet = Penerimaanreturpakanfarm._select_berat_timbang;
				}else{
					beratpallet	= parseFloat(data[0]['brt_pallet']) + parseFloat(data[0]['brt_hand_pallet']);
					Penerimaanreturpakanfarm._select_pallet			= data[0]['kode_kavling'];
  					Penerimaanreturpakanfarm._select_pallet_berat 	= beratpallet;
					Penerimaanreturpakanfarm._select_berat_timbang	= beratpallet;
					//Penerimaanreturpakanfarm._select_berat_timbang	= 
				}
				
				$(tr_id+' .alokasi_berat').html('<i class="glyphicon glyphicon-shopping-cart" style="cursor:pointer;font-size:12pt;"' 
					+ ' onClick="Penerimaanreturpakanfarm.ganti_hand_pallet(this)"></i> ' 
					+ parseFloat(beratpallet).toFixed(3)
				);
			},
		});
	},
	
	ganti_pallet :function(elm){
		var status = $(elm).attr('data-status');
		status = 1;
		if(status == 1){
			/*var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
			var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
			var kode_flok = $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok');
			var kode_barang = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.kode-pakan span').text();*/
			$.ajax({
				type : 'POST',
				dataType : 'html',
				url : "rekap_retur_pakan/penerimaan_retur_pakan_farm/ganti_pallet",
				data : {
					/*kode_flok : kode_flok,
					kode_barang : kode_barang,
					data_ke_detail_pakan : data_ke_detail_pakan,
					data_ke_detail : data_ke_detail*/
				}
			}).done(function(data) {
				var box = bootbox.dialog({
						message : data,
						closeButton: true,
						title : "Pilih Pallet",
						onEscape : function(){
							return true;
						}
					});
			});
		}
	},
	
	set_pallet:function(elm){ 
		var farm_terima = $(elm).data('farm');
		var siklus_terima = $(elm).data('siklus');
		var kandang_terima = $(elm).val();
		var rowid = Penerimaanreturpakanfarm._rowid_detail_alokasi;
		$.ajax({
			url: 'rekap_retur_pakan/penerimaan_retur_pakan_farm/getKavlingPallet',
			method: 'post',
			dataType: 'json',
			data: {
				kode_farm 		: farm_terima,
				kode_siklus 	: siklus_terima,
				kode_kandang	: kandang_terima 
			},
			success: function(data){
				//Penerimaanreturpakanfarm._kavling_pallet = data[0]['kode_pallet'];
				Penerimaanreturpakanfarm._select_pallet = data[0]['kode_pallet'];
				$('#alokasi_kavling'+rowid).html('<a href="javascript:void(0)" onClick="Penerimaanreturpakanfarm.ganti_pallet(this)">'
					+Penerimaanreturpakanfarm._select_pallet+'</a>');
				//$('#alokasi_berat'+rowid).html('<i class="glyphicon glyphicon-shopping-cart" onClick="Penerimaanreturpakanfarm.ganti_hand_pallet(this)" style="cursor:pointer;font-size:12pt;"></i> ' + data[0]['berat_pallet']);
				//Penerimaanreturpakanfarm._berat_pallet_timbang = data[0]['berat_pallet'];
				Penerimaanreturpakanfarm._select_pallet_berat = data[0]['berat_pallet'];
				
				var mpallet = $('#alokasi_berat'+rowid).data('mpallet');
				var hpallet = $('#alokasi_berat'+rowid).data('hpallet');
				var beratpallet = parseFloat(mpallet) + parseFloat(hpallet);
				$('#alokasi_berat'+rowid).html(
					'<i class="glyphicon glyphicon-shopping-cart" onClick="Penerimaanreturpakanfarm.ganti_hand_pallet(this)"' 
					+' style="cursor:pointer;font-size:12pt;"></i> ' 
					+ parseFloat(beratpallet).toFixed(3)
				);
				
				$('#alokasi_berat_bersih'+rowid).html('123');
			},
		});
	},
	
	ganti_hand_pallet : function(elm){
		var //status = $(elm).attr('data-status');
		status = 1;
		if(status == 1){
			/*var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
			var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
			var kode_flok = $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok');
			var kode_barang = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.kode-pakan span').text();*/
			$.ajax({
				type : 'POST',
				dataType : 'html',
				url : "rekap_retur_pakan/penerimaan_retur_pakan_farm/ganti_hand_pallet",
				data : {
					/*kode_flok : kode_flok,
					kode_barang : kode_barang,
					data_ke_detail_pakan : data_ke_detail_pakan,
					data_ke_detail : data_ke_detail*/
				}
			}).done(function(data) {
				//messageBox('Pilih Hand Pallet', data);
				var box = bootbox.dialog({
						message : data,
						closeButton: true,
						title : "Pilih Hand Pallet",
						onEscape : function(){
							return true;
						}
					});
			});
		}
	},
	
	set_hand_pallet : function(elm){
		var tr_id = '.set_alokasi' + Penerimaanreturpakanfarm._tr_detail_alokasi; 
		var mpallet = $(tr_id+' .alokasi_berat').data('mpallet');
		var hpallet = $(elm).data('beratpallet');
		var beratpallet = parseFloat(mpallet) + parseFloat(hpallet);
		$(tr_id+' .alokasi_berat').html(
			'<i class="glyphicon glyphicon-shopping-cart" onClick="Penerimaanreturpakanfarm.ganti_hand_pallet(this)" style="cursor:pointer;font-size:12pt;"></i> ' 
			+ parseFloat(beratpallet).toFixed(3)
		);
		$('.bootbox').modal('hide');
		alert(mpallet);
		//Penerimaanreturpakanfarm._berat_pallet_timbang = beratpallet;
		//var beratbersih = parseFloat(Penerimaanreturpakanfarm._berat_timbang) - parseFloat(Penerimaanreturpakanfarm._berat_pallet_timbang);
		Penerimaanreturpakanfarm._select_pallet_berat = beratpallet;
		var beratbersih = parseFloat(Penerimaanreturpakanfarm._berat_timbang) - parseFloat(Penerimaanreturpakanfarm._select_pallet_berat);
		if($(tr_id+' .alokasi_berat_timbang').val() != ''){
			$(tr_id+' .alokasi_berat_bersih').html(parseFloat(beratbersih).toFixed(3));
		}
	},
	
	set_pallet : function(elm){
		alert($(elm).find('.berat').html());
		$('.bootbox').modal('hide');
	},
	
	pilihCheck: function(elm){
		var trID = elm.getAttribute('data-row');
		if(elm.checked){ //checked
			$('#'+trID).addClass('terpilih');
		}else{ //unchecked
			$('#'+trID).removeClass('terpilih');
		}
	},
	
	pilihSemua: function(elm){
		if(elm.checked){ //checked
			$('.TRrow').addClass('terpilih'); 
			$('.list_retur_cb').prop('checked', true);
		}else{ //unchecked
			$('.TRrow').removeClass('terpilih');
			$('.list_retur_cb').prop('checked', false);
		}
	},
	
	tampilUbah: function(elm){
		$('.ubah').removeClass('hide');
		var getID = elm.getAttribute('data-rowID');
		$('#'+getID).addClass('terpilih');
	},
	
	detail: function(elm){		
		var noRetur = $(elm).closest('td').data('retur');						
		this.loadDetail(noRetur);
	},
	
	loadDetail: function(returPakan){
		var _url = 'rekap_retur_pakan/penerimaan_retur_pakan_farm/detail_retur';
		var _data = {no_retur : returPakan};
		$.get(_url,_data,function(html){
			$('#div_detail_pengiriman_retur_pakan').html(html);
		},'html');
	},
	
	timbang: function(elm){
		/*var _bisaTimbang = $(elm).data('timbang');
		if(!_bisaTimbang){
			bootbox.alert('Belum dilakukan proses generate');
			return;
		}
		var _noRetur = $(elm).find('td.no_retur').data('no_retur');
		$.get(this.urlTimbang,{no_retur : _noRetur},function(data){
			$('#div_detail_pengiriman_retur_pakan').html(data);
		},'html');*/
		
		Penerimaanreturpakanfarm._noRetur = $(elm).data('retur');
		var get_kode_pakan = $(elm).find('.jenis_pakan p').data('kodepakan');
		$.get(
			'rekap_retur_pakan/penerimaan_retur_pakan_farm/tabel_timbang', 
			{no_retur : Penerimaanreturpakanfarm._noRetur, kode_pakan : get_kode_pakan, kirim:'0'}, 
			function(data){
				$('#div_detail_pengiriman_retur_pakan').html(data);
			}, 
		'html');
	},
	
	get_data_timbang: function(elm){ 
		var listpakanrow = $(elm).data('rowlist');
		Penerimaanreturpakanfarm._select_pakan_row = listpakanrow;
		var rowid = Penerimaanreturpakanfarm._rowid_detail_alokasi;
		var tr_id = '#'+$(elm).data('table')+' #'+$(elm).data('rowid'); 
		Penerimaanreturpakanfarm._row_timbang = tr_id;
		//Penerimaanreturpakanfarm._jml_sak_retur = $('#tabellistretur #row_penerimaan'+listpakanrow+' .jml_pick').html();
		
		if(Penerimaanreturpakanfarm._lockTimbangan == 0){
			$.get('rekap_retur_pakan/penerimaan_retur_pakan_farm/get_berat_timbang', 
			function(data){
				var dataParse = JSON.parse(data);
				if(dataParse.status == 1){ 
				
					Penerimaanreturpakanfarm._berat_timbang = parseFloat(dataParse.content).toFixed(3);
					var beratTimbang = Penerimaanreturpakanfarm._berat_timbang;
					$(elm).val(beratTimbang);	
					
					//var beratbersih = parseFloat(beratTimbang) - parseFloat(Penerimaanreturpakanfarm._berat_pallet_timbang); 
					var beratbersih = parseFloat(beratTimbang) - parseFloat(Penerimaanreturpakanfarm._select_pallet_berat); 
					$(tr_id+' .alokasi_berat_bersih').html(parseFloat(beratbersih).toFixed(3));
					
					Penerimaanreturpakanfarm.cek_konversi(beratbersih,function(data){ 
						var readonly 	= '';
						var onBlur 		= 'onBlur="Penerimaanreturpakanfarm.kontrol_timbangan(this)"';
						
						if(data.KONFIRMASI_SAK == 0){
							onBlur		= ''; 
							readonly 	= 'readonly';
							Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur = data.JML_SAK;
							$(tr_id+' .alokasi_jml_sak_timbang').html(Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur);
							var sak_timbang = Penerimaanreturpakanfarm._jml_sak_timbang;
							Penerimaanreturpakanfarm._jml_sak_timbang = parseInt(sak_timbang) + parseInt(data.JML_SAK);
						}
						
						/*$(tr_id+' .alokasi_jml_sak_timbang').html('');
						Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur = 0;
						if(data.JML_SAK == Penerimaanreturpakanfarm._jml_sak_retur){
							Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur = data.JML_SAK;
							Penerimaanreturpakanfarm._jml_sak_timbang = parseInt(Penerimaanreturpakanfarm._jml_sak_timbang) + parseInt(Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur);
							$(tr_id+' .alokasi_jml_sak_timbang').html(parseFloat(Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur).toFixed(3));
						}
						
						
						if(Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur > 0){
							onBlur		= ''; 
							readonly 	= 'readonly';
						}*/
						var _message = '<div class="col-xs-1"></div>' 
									+ '<div class="col-xs-10">'
									+ 	'<div class="row">'
									+		'<div class="col-xs-5" style="text-align:right;">Jumlah Satuan Aktual</div>'
									+		'<div class="col-xs-1">:</div>'
									+		'<div class="col-xs-6">'
									+		'<input type="text" class="form-control" value="'+Penerimaanreturpakanfarm._konfirmasi_jml_aktual_retur+'"  '+onBlur+' onkeyup="number_only(this)" '+readonly+'>'
									+		'</div>'
									+	'</div>'
									+ 	'<div class="row" style="margin:10px 0;">'
									+		'<div class="col-xs-5" style="text-align:right;">Scan Barcode Pallet</div>'
									+		'<div class="col-xs-1">:</div>'
									+		'<div class="col-xs-6">'
									+		'<input type="text" class="form-control" onBlur="Penerimaanreturpakanfarm.cek_barcode_pallet(this)">'
									+		'</div>'
									+	'</div>'
									+ 	'<div class="row" style="margin:10px 0;">'
									+		'<div class="col-xs-6" style="margin-bottom:30px;"></div>'
									+		'<div class="col-xs-6"><label id="barcode_alert" style="color:red;"></label></div>'
									+	'</div>'
									+ '</div>'
									+ '<div class="col-xs-1"></div>';
						var box = bootbox.dialog({
							title : 'Konfirmasi Sak',
							message : _message,
							closeButton: true,
							onEscape : function(){
								return true;
							}
						});
						
					});
					
				}else{
					
					var _message = "<center><label>"+dataParse.message+"</label></center>";
					var box = bootbox.dialog({
						message : _message,
						closeButton: true,
						title : "Alert",
						onEscape : function(){
							Penerimaanreturpakanfarm._timer = false;
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
		}
	},
	
	visualisasi_kavling : function(){ 
		var get_alokasi 		= '.tabel_detail_alokasi tbody'; 
		var total_sak 			= 0;
		var numrow 				= 0;
		var select_row_alokasi	= '';
		var select_tbl_timbang	= '';
		
		var kodepakan 			= '';
		var jml_pick 			= 0;
		var jml_sudah_timbang	= 0;
		var alokasi_numrow		= 0;
		
		var row_tbl_penerimaan	= '#table_alokasi .row_data_penerimaan';
		$(row_tbl_penerimaan).each(function(){
			numrow++;
			select_row_alokasi = '#table_alokasi #row_penerimaan' + numrow;
			kodepakan	= $(select_row_alokasi+' .kode_pakan').html();
			jml_pick	= $(select_row_alokasi+' .jml_pick').html();
			
			$('#table_alokasi .tabel_timbang .set_alokasi').each(function(){
				alokasi_numrow++;
				select_tbl_timbang = '#table_alokasi #tabel_timbang'+numrow + ' #set_alokasi'+alokasi_numrow;
				jml_sudah_timbang += parseInt($(select_tbl_timbang + ' .alokasi_jml_sak_timbang').html());
			});
			
			if(parseInt(jml_pick) == parseInt(jml_sudah_timbang)){
				jml_pick 			= 0;
				jml_sudah_timbang 	= 0;
				select_row_alokasi	= '';
				select_tbl_timbang	= '';
				$('#btn_alokasi_simpan').removeClass('hide');
			}else{
				Penerimaanreturpakanfarm.message_sisa_timbang(jml_pick, jml_sudah_timbang, numrow, alokasi_numrow);
			}
			
		});
		
		/*$(get_alokasi+' .set_alokasi').each(function(){
			numrow++;
			var qty_timbang = $(get_alokasi+' #set_alokasi'+numrow+' .alokasi_jml_sak_timbang').html();
			total_sak = parseInt(total_sak) + parseInt(qty_timbang);
		});
		
		if(total_sak > 0){
			if(Penerimaanreturpakanfarm._jml_sak_retur == total_sak){
				$('#btn_alokasi_simpan').removeClass('hide');
			}else{
				var sisaTimbang = parseInt(Penerimaanreturpakanfarm._jml_sak_retur) - parseInt(Penerimaanreturpakanfarm._jml_sak_timbang);
				var _message = '<center>'
							+	'Sisa sak yang belum ditimbang adalah '+sisaTimbang+' sak. Apakah anda'
							+	'<br>'
							+	'ingin melanjutkan proses penimbangan pada pallet selanjutnya ?'
							+	'<br><br>'
							+	'Jika anda pilih "Tidak" maka '+sisaTimbang+' sak akan dianggap bermasalah'
							+	'<br>'
							+	'(Hilang/Rusak)'
							+	'</center>';
				var box = bootbox.dialog({
					message : _message,
					closeButton: true,
					onEscape : function(){
						Penerimaanreturpakanfarm._timer = false;
						return true;
					},
					buttons : {
						OK : {
							label : "Ya",
							className : "btn-success",
							callback : function() {
								Penerimaanreturpakanfarm._jml_sak_timbang = 0;
								Penerimaanreturpakanfarm.tambah_hitung();
							}
						},
						cancel : {
							label : "Tidak",
							className : "btn-danger",
							callback : function() {
								//form pakan bermasalah
								$.get('rekap_retur_pakan/penerimaan_retur_pakan_farm/pakan_rusak_hilang',{},function(data){
									$('#div_pakan_rusak_hilang').html(data);
								},'html');
								//end form pakan bermasalah
							}
						}
					}
				});
			}
		}*/
	},
	
	//ok
	message_sisa_timbang : function(jml_pick, jml_timbang, last_timbang, last_alokasi){
		var sisaTimbang = parseInt(jml_pick) - parseInt(jml_timbang);
		var _message = '<center>'
				+	'Sisa sak yang belum ditimbang adalah '+sisaTimbang+' sak. Apakah anda'
				+	'<br>'
				+	'ingin melanjutkan proses penimbangan pada pallet selanjutnya ?'
				+	'<br><br>'
				+	'Jika anda pilih "Tidak" maka '+sisaTimbang+' sak akan dianggap bermasalah'
				+	'<br>'
				+	'(Hilang/Rusak)'
				+	'</center>';
		var box = bootbox.dialog({
			message : _message,
			closeButton: true,
			onEscape : function(){
				return true;
			},
			buttons : {
				OK : {
					label : "Ya",
					className : "btn-success",
					callback : function() {
						Penerimaanreturpakanfarm.tambah_hitung(last_timbang, parseInt(last_alokasi)+1);
					}
				},
				cancel : {
					label : "Tidak",
					className : "btn-danger",
					callback : function() {
						$.get('rekap_retur_pakan/penerimaan_retur_pakan_farm/pakan_rusak_hilang',{},function(data){
							$('#div_pakan_rusak_hilang').html(data);
						},'html');
					}
				}
			}
		});
	},
	
	//cek
	simpan : function(){
		var numrow 				= 0;
		var alokasi_numrow 		= 0;
		var row_tbl_penerimaan	= '#table_alokasi .row_data_penerimaan';
		var select_row_alokasi	= '';
		var select_tbl_timbang	= '';
		
		$(row_tbl_penerimaan).each(function(){
			numrow++;
			select_row_alokasi = '#table_alokasi #row_penerimaan' + numrow;
			//kodepakan	= $(select_row_alokasi+' .kode_pakan').html();
			//jml_pick	= $(select_row_alokasi+' .jml_pick').html();
			
			$('#table_alokasi .tabel_timbang .set_alokasi').each(function(){
				alokasi_numrow++;
				select_tbl_timbang = '#table_alokasi #tabel_timbang'+numrow + ' #set_alokasi'+alokasi_numrow;
				//jml_sudah_timbang += parseInt($(select_tbl_timbang + ' .alokasi_jml_sak_timbang').html());
				alert(Penerimaanreturpakanfarm._no_retur);
			});
			
		});
		
		/*var numrow = 0;
		var tbl_list_penerimaan = '#tabellistretur tbody';
		
		$(tbl_list_penerimaan+' .row_data_penerimaan').each(function(){
			numrow++;
			
			var rowid = $(tbl_list_penerimaan+' .row_data_penerimaan').data('rowid');
			var this_penerimaan = tbl_list_penerimaan+' #row_penerimaan'+rowid;
			var kodepakan = $(this_penerimaan).find('.kode_pakan').html();
			var jmlsj = $(this_penerimaan).find('.jml_pick').html();
			
			var thisnum = 0;
			var this_timbang = '#tabel_timbang'+rowid+' tbody'; 
			$(this_timbang+' tr').each(function(){
				thisnum++;
				var this_alokasi = this_timbang + ' #set_alokasi' + thisnum;
				alert($(this_alokasi+' .alokasi_kavling').html());
			});
			
		});*/
	},
	
	//cek
	tambah_hitung : function(row_timbang, row_alokasi){
		var kodefarm 	= $('#table_alokasi').data('kodefarm');
		var kodesiklus	= $('#table_alokasi').data('kodesiklus'); 
		var kodepakan	= $('#table_alokasi #row_penerimaan'+row_timbang+' .kode_pakan').html();
		var row_baru 	= '<tr id="set_alokasi'+row_alokasi+'" class="row_timbang set_alokasi" style="cursor:default;">'
				+	'<td>'+row_alokasi+'</td>'
				+	'<td class="alokasi_tgl_kebutuhan">'
				+		'<div class="input-group date">'
				+			'<input type="text" class="form-control parameter input_datepicker" onClick="Penerimaanreturpakanfarm.set_tgl_kebutuhan(this)"/>'
				+			'<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>'
				+			'</span>'
				+		'</div>'
				+	'</td>'
				+	'<td class="alokasi_kandang">'
				+		'<select class="form-control" onChange="Penerimaanreturpakanfarm.set_alokasi_kandang(this)"'
				+		'data-farm="'+kodefarm+'" data-kodepakan="'+kodepakan+'" data-siklus="'+kodesiklus+'"'
				+		'data-rowid="set_alokasi'+row_alokasi+'" data-table="tabel_timbang'+row_timbang+'"'
				+		'></select>'
				+	'</td>'
				+	'<td class="no_pallet"></td>'
				+	'<td class="alokasi_berat"></td>'
				+	'<td class="alokasi_berat_timbang">'
				+		'<input type="text" class="form-control" onCLick="Penerimaanreturpakanfarm.get_data_timbang(this)"'
				+ 		'data-rowlist="" data-rowid="set_alokasi'+row_alokasi+'" data-table="tabel_timbang'+row_timbang+'" readonly>'
				+	'</td>'
				+	'<td class="alokasi_berat_bersih"></td>'
				+	'<td class="alokasi_jml_sak_timbang"></td>'
				+	'<td class="scan_barcode_status"></td>'
				+	'</tr>';
		$('#tabel_timbang'+row_timbang+' tbody').append(row_baru);
		Penerimaanreturpakanfarm.set_list_kandang(row_alokasi);
	},
	
	//cek
	cek_barcode_pallet : function(elm){
		var rowid = Penerimaanreturpakanfarm._rowid_detail_alokasi;
		var barcode = $(elm).val();
		var tr_id = Penerimaanreturpakanfarm._row_timbang;
		if(Penerimaanreturpakanfarm._select_pallet == barcode){
			var tgl_kebutuhan = $(tr_id+' .alokasi_tgl_kebutuhan .input_datepicker').val();
			$(tr_id+' .alokasi_tgl_kebutuhan').html(tgl_kebutuhan);
			
			var selectfarm = $(tr_id+' .alokasi_kandang select').val().split('_');
			//var selectfarm = 'GD';
			$(tr_id+' .alokasi_kandang').html(selectfarm[2]);
			$(tr_id+' .alokasi_kandang').attr('data-kandang', selectfarm[0]);
			$(tr_id+' .alokasi_kandang').attr('data-kodeflok', selectfarm[1]);
			
			var setkavling = $(tr_id+' .no_pallet a').html();
			$(tr_id+' .no_pallet').html(setkavling);
			
			$(tr_id+' .alokasi_berat i').remove();
			$(tr_id+' .alokasi_berat_timbang').html(parseFloat(this._berat_timbang).toFixed(3));
			$(tr_id+' .scan_barcode_status').html('<i style="color:green;" class="glyphicon glyphicon-ok"></i>');
			
			$(elm).css('border', '1px solid #dfdfdf');
			$('#barcode_alert').html('');
			$('.bootbox').modal('hide');
		}else{
			$(elm).css('border', '1px solid red');
			$('#barcode_alert').html('Barcode yang di-scan<br>tidak sesuai');
		}
	},
	
	reset_hitung_timbang:function(elm){
		$('#stok_pallet'+elm).html('');
		$('#jumlah_kirim'+elm).html('');
		$('#sisa_pallet'+elm).html('');
		Penerimaanreturpakanfarm._lockTimbangan = 0;
	},
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
	barcode_pallet_check: function(elm){
		var rowID = $(elm).data('rowid');
		var kodePallet = $('#kode_pallet'+rowID).data('kode-pallet');
		$('#scan_barcode'+rowID).find('p').empty();
		if($(elm).val() == kodePallet){
			//Penerimaanreturpakanfarm.submit();
		}else{
			$('#scan_barcode'+rowID).append("<p style='color:red;'>Barcode yang di-scan tidak sesuai</p>");
		}
	},
	
	inputSelesai: function(){
		if(Penerimaanreturpakanfarm._returSelesai){
			Penerimaanreturpakanfarm._sopir = $('#'+Penerimaanreturpakanfarm._idRowPengiriman).find('td .input_nama_sopir').val(); 
			Penerimaanreturpakanfarm._nopol = $('#'+Penerimaanreturpakanfarm._idRowPengiriman).find('td .select_kendaraan').val(); 
			Penerimaanreturpakanfarm.update_retur_farm();
		}/*else{
			Penerimaanreturpakanfarm.submit();
		}*/
	},	
	
	update_retur_farm:function(){
		$.ajax({
			type : "POST",
			url : "rekap_retur_pakan/penerimaan_retur_pakan_farm/simpan_pengiriman",
			data : {
				'sopir' 		: Penerimaanreturpakanfarm._sopir,
				'nopol' 		: Penerimaanreturpakanfarm._nopol,
				'no_retur' 		: Penerimaanreturpakanfarm._noRetur, 
				'no_pallet' 	: Penerimaanreturpakanfarm._noPallet,
				'stok_pallet' 	: Penerimaanreturpakanfarm._palletStok,
				'jml_retur' 	: Penerimaanreturpakanfarm._jmldiretur,
				'detail_pallet'	: Penerimaanreturpakanfarm._dataPallet,
				'kode_pallet' 	: Penerimaanreturpakanfarm._kode_pallet
			}
		  });
		
		//$('.row_kavling').find('.btn_reset').addClass('hide');
		//$('.row_kavling').find('.scan_barcode').append('<p style="color:green;" class="glyphicon glyphicon-ok"></p>');
		Location.reload();
	},

	//kontrol berat timbang
	/*kontrol_timbangan: function(elm) {
		var berat = Penerimaanreturpakanfarm._berat_timbang;
		berat = parseFloat(berat);
		
		var jumlah = $(elm).val();
		jumlah = parseInt(jumlah);
		
		Penerimaanreturpakanfarm.cek_konversi(berat,function(data){ alert(JSON.stringify(data));
			if(data.JML_SAK == jumlah){
				$('#jml_sak_timbang'+Penerimaanreturpakanfarm._rowid_detail_alokasi).html(data.JML_SAK);
				Penerimaanreturpakanfarm._jml_sak_timbang = parseInt(Penerimaanreturpakanfarm._jml_sak_timbang) + parseInt(data.JML_SAK);
			}
		});
	},*/
	//end kontrol berat timbang
	
	set_list_kandang : function(rowid){
		$.ajax({
			type : "POST",
			url : "rekap_retur_pakan/Penerimaan_retur_pakan_farm/get_kandang_farm",
			data : {},
			dataType : 'json',
			success : function(data) { 
				var select_option = '<option selected disabled>pilih kandang</option>';
				for(var i=0;i<data.length;i++){ 
					select_option += '<option value="'+data[i]['kode_kandang']+'_'+data[i]['no_flok']+'_'+data[i]['nama_kandang']+'">'
							+ data[i]['nama_kandang']
							+ '</option>';
				}
				$('#set_alokasi'+rowid+' .alokasi_kandang select').append(select_option);
			}
		});
	},
	
	cek_konversi : function(berat,callback){
		if (berat && berat > 0 && berat != '') {
			$.ajax({
				type : "POST",
				url : "penerimaan_pakan/Transaksi/cek_konversi",
				data : {
					berat : berat
				},
				dataType : 'json',
				success : function(data) {
					callback(data);
				}
			});
		}
		else{
			callback(2);
		}
	},
	
	/*resetTimbang: function(elm){
		//lama
		/*var _tr = $(elm).closest('tr');
		$(elm).addClass('hide');
		_tr.find('td:gt(2)').text('');
		_tr.find('input').val('');*
		//end lama
		
		var thisRowID = $(elm).data('rowid');
		$(elm).addClass('hide');
		$('tr').find('#berat_timbang'+thisRowID+' .val_berat_timbang').val('');
		$('tr').find('#berat_bersih'+thisRowID).html('');
		$('tr').find('#stok_pallet'+thisRowID).html('');
		$('tr').find('#jumlah_kirim'+thisRowID).html('');
		$('tr').find('#sisa_pallet'+thisRowID).html('');
		$('tr').find('#nama_admin'+thisRowID).html('');
		Penerimaanreturpakanfarm._lockTimbangan = 0;
		$('.row_kavling').find('.inp_barcode').val('');
	},*/
	
	tes_date : function(elm){
		alert($(elm).val());
	},
	
	/*
	simpanTimbang: function(elm){
		/** pastikan sudah melakukan penimbangan semua *
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

		Penerimaanreturpakanfarm.konfirmasi_pengiriman(_data);
	
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
	*/
	
	/*set capital dan huruf*/
	set_huruf : function(elm){		
		elm.value = elm.value.charAt(0).toUpperCase() + elm.value.slice(1);
		elm.value = elm.value.replace(/[^a-zA-Z@]+/, '');
	},
	/*end set capital dan huruf*/
	
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
									Penerimaanreturpakanfarm.simpanTimbangServer(_data,_infoKendaraan);					
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
	cetakSJRetur: function(elm){
		var _url = this.urlCetakSJ;
		var _no_sj = $(elm).data('no_referensi');
		$.redirect(_url,{no_sj : _no_sj},'POST','_blank');
	},
	
	set_tgl_kebutuhan: function(elm){
		$(elm).datepicker({
			dateFormat : 'dd M yy'
		});
		$(elm).datepicker('show');
	}
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
				Penerimaanreturpakanfarm.cariReturTimbang();			
			}else{
				Penerimaanreturpakanfarm.cari();			
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
