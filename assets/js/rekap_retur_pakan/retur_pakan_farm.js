'use strict';

//begin Returpakanfarm
var Returpakanfarm = {	
	urlBaru : 'rekap_retur_pakan/retur_pakan_farm/alokasi_retur',
	urlMain : 'rekap_retur_pakan/retur_pakan_farm/index',
	urlUpdateStatus : 'rekap_retur_pakan/retur_pakan_farm/updateStatus',
	urlGenerate : 'rekap_retur_pakan/retur_pakan_farm/generate',
	urlTimbang : 'rekap_retur_pakan/retur_pakan_farm/timbang',
	urlSimpanTimbang : 'rekap_retur_pakan/retur_pakan_farm/simpanTimbang',
	urlCetakSJ : 'rekap_retur_pakan/retur_pakan_farm/cetakSJ',
	prosesServer: 0,
	
	cari: function(){
		var _url = 'rekap_retur_pakan/retur_pakan_farm/list_retur';
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
			$('#div_retur_pakan').html(html);
		},'html');
	},
	cariReturTimbang: function(){
		var _url = 'rekap_retur_pakan/retur_pakan_farm/list_retur_timbang';
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
			$('#div_retur_pakan').html(html);
		},'html');
	},
	baru: function(elm){
		$.ajax({
			url: 'rekap_retur_pakan/retur_pakan_farm/check_ayam_dan_pakan',
			method: 'post',
			data: {kode_farm : 'BW'},
			success: function(data){
				if(data.notif_check == 'proses_panen'){
					bootbox.alert("Pengajuan pindah pakan antar Farm tidak dapat diproses. Terdapat kandang dalam proses panen");
				}else if(data.notif_check == 'belum_retur'){
					bootbox.alert("Pengajuan pindah pakan antar Farm tidak dapat diproses. Mohon melakukan proses retur pakan ke kandang terlebih dahulu.");
				}else if(data.notif_check == 'pengajuan_baru'){
					Returpakanfarm.formReturBaru();
				}
			},
			dataType: 'json'
		});
		
		/*
		$.get(this.urlBaru,{},function(data){
			$('#main_page_retur').html(data);
		},'html').done(function(){
			$('input[name=tglKirim]').datepicker({
				dateFormat : 'dd M yy',
				minDate : new Date()						
			});
			$('input[name=jmlPakan]').priceFormat({
				prefix: '',
				centsLimit : 0,
				thousandsSeparator: '.',
				clearOnEmpty: true
			});
		});	
		*/
	},
	
	formReturBaru : function(){
		$.get(this.urlBaru,{},function(data){
			$('#main_page_retur').html(data);
		},'html').done(function(){
			$('input[name=tglKirim]').datepicker({
				dateFormat : 'dd M yy',
				minDate : new Date()						
			});
			$('input[name=jmlPakan]').priceFormat({
				prefix: '',
				centsLimit : 0,
				thousandsSeparator: '.',
				clearOnEmpty: true
			});
		});	
	},
	
	//lampiran check
	attachmentCheck : function(elm){	
		var input = $(elm);
		var _file = input.get(0).files[0];		
		
		if(_file.type !== 'application/pdf'){				
			bootbox.alert("File yang dapat diunggah hanya dengan format pdf",function(){
				$('#lampirkan-file').val('');
			});
			return false;									
		}else{
			var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			$('#lampirkan-file').val(label);
		}
	},
	//end lampiran check
	
	ubah: function(elm){
		var _tr = $('#tabellistretur>tbody>tr.terpilih');
		if(!_tr.length){
			bootbox.alert("Belum ada no retur yang dipilih");
			return;
		}
		var _noRetur = _tr.find('td.no_retur').data('retur');
		$.get(this.urlBaru,{no_retur : _noRetur},function(data){ 
			$('#main_page_retur').html(data);
		},'html').done(function(){
			$('input[name=tglKirim]').datepicker({
				dateFormat : 'dd M yy',
				minDate : new Date()						
			});
			$('input[name=jmlPakan]').priceFormat({
				prefix: '',
				centsLimit : 0,
				thousandsSeparator: '.',
				clearOnEmpty: true
			});
		});
	},
	
	review: function(elm){
		/** pastikan sudah ada baris yang dipilih */
		var _tr = $('#tabellistretur>tbody>tr.terpilih');
		if(!_tr.length){
			bootbox.alert("Belum ada no retur yang dipilih");
			return;
		}

		bootbox.confirm({
			message: "Apakah Anda yakin untuk menyetujui pengajuan retur pakan antar farm ?",
			buttons:{
				confirm: {
					label: 'Ya',
					className: 'btn-default',
				},
				cancel: {
					label: 'Tidak',
					className: 'btn-default btn-danger pull-right'
				}
			},
			callback: function (result) {
				if(result){
				   /** update status retur */
				   //var _noRetur = _tr.find('td.no_retur').data('retur');
				   //var _kdStatus = _tr.find('td.status').data('kodestatus');
				   //Returpakanfarm.updateStatus(_noRetur,_kdStatus);
				   
				   var numRows = 1;
				   _tr.each(function (elm){
					   //alert($('#TR'+numRows).find('.no_retur').data('retur'));
					   var _noRetur = $('#TR'+numRows).data('retur');
					   var _kdStatus = $('#TR'+numRows).data('kodestatus');
					   Returpakanfarm.updateStatus(_noRetur, _kdStatus);
					   numRows++;
				   });
				   
				}
			}
		});
	},
	approve: function(elm){
		/** pastikan sudah ada baris yang dipilih */
		var _tr = $('#tabellistretur>tbody>tr.terpilih');
		if(!_tr.length){
			bootbox.alert("Mohon memilih baris retur terlebih dahulu");
			return;
		}

		bootbox.confirm({
			message: "Apakah Anda yakin untuk menyetujui pengajuan retur pakan antar farm ?",
			buttons:{
				confirm: {
					label: 'Ya',
					className: 'btn-default',
				},
				cancel: {
					label: 'Tidak',
					className: 'btn-default btn-danger pull-right'
				}
			},
			callback: function (result) {
				if(result){
				   /** update status retur */
				   //var _noRetur = _tr.find('td.no_retur').data('retur');
				   //var _kdStatus = _tr.find('td.status').data('kodestatus');
				   //Returpakanfarm.updateStatus(_noRetur,_kdStatus);
				   
				   var numRows = 1;
				   _tr.each(function (elm){
					   //alert($('#TR'+numRows).find('.no_retur').data('retur'));
					   var _noRetur = $('#TR'+numRows).data('retur');
					   var _kdStatus = $('#TR'+numRows).data('kodestatus');
					   Returpakanfarm.updateStatus(_noRetur, _kdStatus);
					   numRows++;
				   });
				}
			}
		});
	},
	reject: function(elm){
		/** pastikan sudah ada baris yang dipilih */
		var _tr = $('#tabellistretur>tbody>tr.terpilih');
		if(!_tr.length){
			bootbox.alert("Mohon memilih baris retur terlebih dahulu");
			return;
		}
		var box2 = bootbox.prompt({
			title: "Keterangan",
			inputType: 'textarea',
			 buttons: {
			   confirm: {
				   label: 'Simpan',
				   className: 'btn-primary btn-keterangan-reject',
			   },
			   cancel: {
				   label: 'Batal',
				   className: 'btn-default batal'
			   }
		   },
			callback: function (result) {
			 if(result){
				/** update status retur */
				var _noRetur = _tr.find('td.no_retur').data('retur');
				var _kdStatus = _tr.find('td.status').data('kodestatus');
				//Returpakanfarm.updateStatus(_noRetur,_kdStatus,result);
				
				var numRows = 1;
				_tr.each(function (elm){
					var _noRetur = $('#TR'+numRows).data('retur');
					var _kdStatus = $('#TR'+numRows).data('kodestatus');
					Returpakanfarm.updateStatus(_noRetur, _kdStatus, result);
					numRows++;
				});
			 }
			}
		});

	   box2.bind('shown.bs.modal', function() {		
		  $('.batal').hide(); 
		  $('.btn-keterangan-reject').prop('disabled',true);  
		  $('.bootbox-form textarea').on('keyup',function() {
			 if($(this).val().length >= 10){
				$('.btn-keterangan-reject').prop('disabled',false);
			 }
			 else{
				$('.btn-keterangan-reject').prop('disabled',true);
			 }
		  });
	   });
	},
	updateStatus: function(_noRetur,_kdStatus,_keterangan){
		$.ajax({
			url: Returpakanfarm.urlUpdateStatus,
			method: 'post',
			beforeSend: function(){

			},
			data: {no_retur : _noRetur, kd_status : _kdStatus, keterangan : _keterangan},
			success: function(data){
				if(data.status){
					bootbox.alert(data.message,function(){
						var _tr = $('#tabellistretur>tbody>tr.terpilih');
						_tr.remove();
						$('#div_detail_retur_pakan').html('');
					});					
				}
			},
			dataType: 'json'
		});
	},
	simpan: function(elm){
		var _form = $(elm).closest('form');
		var _pakan = _form.find('input[name=jmlPakan]');
		var _error = 0;
		var _pesan = [];
		var _data = {};
		var _name;
		
		/** pastikan semua sudah diisi */
		_form.find('input,select,textarea').not('input[name=jmlPakan]').each(function(){
			if(empty($(this).val())){
				_error++;				
			}else{
				_name = $(this).closest('.form-group').data('name');				
				_data[_name] = $(this).val();
				if(_name == 'tgl_kirim'){
					var _tglKirim = $(this).datepicker('getDate');
					var _tmpTgl = [_tglKirim.getFullYear(),_tglKirim.getMonth() + 1, _tglKirim.getDate()];
					_data[_name] = _tmpTgl.join('-');
				}
			}
		});
		
		if(_error){
			_pesan.push('Mohon mengisi informasi retur secara lengkap');
		}

		if(!_error){
			var _sudahIsiPakan = 0;
			_pakan.each(function(){
				if(!empty($(this).val())){					
					_sudahIsiPakan++;
					if($(this).val() > $(this).data('max')){
						_error++;
						_pesan.push('Jumlah yang dialokasikan melebihi jumlah stok');
					}
					if(_data['detailPakan'] == undefined){
						_data['detailPakan'] = [];
					}
					_data['detailPakan'].push({kode_pakan : $(this).data('kodepakan'), jumlah : parse_number($(this).val(),'.',',')});
				}
			});
			if(!_sudahIsiPakan){
				_error++;
				_pesan.push('Mohon mengisi informasi retur secara lengkap');
			}
		}
		
		if(!_error){
			if(_data['keterangan'].length < 10){
				_error++;
				_pesan.push('Mohon mengisi keterangan lebih dari 10 karakter dan kurang dari 100 karakter');
			}				
		}
		
		if(_error){
			bootbox.alert(_pesan.join(''));
			return;
		}
		bootbox.dialog({
			message : "Apakah Anda yakin untuk menyimpan retur pakan antar farm ?",
			title : "",
			//className : "largeWidth",
			buttons : {
				OK : {
					label : "Ya",
					className : "btn-success",
					callback : function() {
						Returpakanfarm.save(_data);				
					}
				},
				cancel : {
					label : "Tidak",
					className : "btn-danger",
					callback : function() {
						
					}
				}
		}
		});				
	},
	save: function(_data){
		var attachment = $('#lampiran').get(0).files[0];
		var attachment_name = $('#lampirkan-file').val();
		var formData = new FormData();
        formData.append("attachment", attachment);
        formData.append("attachment_name", attachment_name);
		formData.append("data", JSON.stringify(_data));
		$.ajax({
			type : "POST",
			url : "rekap_retur_pakan/retur_pakan_farm/simpan",
			data : formData,
			cache   : false,
			contentType : false,
			processData : false,
			dataType : 'json',
			async : false,
			success : function(data) {
				if(data.status){					
					bootbox.alert(data.message,function(){
						Returpakanfarm.kembali();
					});					
				}
				else{
					bootbox.alert(data.message);
				}
			}
		});

	},
	kembali: function(elm){
		Home.replace_main_content(this.urlMain);
	},
	
	/*disable sementara*/
	/*pilih: function(elm){
		var rjstatus = $(elm).closest('tr').data('reject');		
		var btnUbah = $('#divTombol').find('.btn.ubah');
		if(rjstatus){
			if(btnUbah.hasClass('hide')){				
				btnUbah.removeClass('hide');
			}
		}else{
			btnUbah.addClass('hide');
		}
		$(elm).siblings().removeClass('terpilih');
		$(elm).addClass('terpilih');
		
	},*/
	/*end disable sementara*/
	
	/*baru*/
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
	/*end baru*/
	
	//script lama
	/*detail: function(elm){		
		var noRetur = $(elm).closest('td').data('retur');						
		this.loadDetail(noRetur);
	},
	loadDetail: function(returPakan){
		var _url = 'rekap_retur_pakan/retur_pakan_farm/detail_retur';
		var _data = {no_retur : returPakan};
		$.get(_url,_data,function(html){
			$('#div_detail_retur_pakan').html(html);
		},'html');
	},*/
	//end script lama
	
	generate: function(elm){
		var _tr = $(elm).closest('tr');
		var _no_retur = _tr.find('td.no_retur').data('no_retur');
		var _ini = this;
		if(_ini.prosesServer){
			bootbox.alert('Masih menunggu response dari server ...');
			return;
		}

		$.ajax({
			url: _ini.urlGenerate,
			beforeSend: function(){
				_ini.prosesServer = 1;
			},
			data: {no_retur : _no_retur},
			success: function(data){				
				if(data.status){
					bootbox.alert(data.message,function(){
						_tr.find('td.no_pengiriman').html('');
						_tr.find('td.jml_kebutuhan').html(data.content.jml_kebutuhan);
					});					
				}else{
					alert('masuk sini');
					bootbox.alert(data.message);
				}
			},
			dataType: 'json'
		}).done(function(){
			_ini.prosesServer = 0;
		});
		
	},
	timbang: function(elm){
		var _bisaTimbang = $(elm).data('timbang');
		if(!_bisaTimbang){
			bootbox.alert('Belum dilakukan proses generate');
			return;
		}
		var _noRetur = $(elm).find('td.no_retur').data('no_retur');
		$(elm).siblings().removeClass('terpilih');
		$(elm).addClass('terpilih');
		$.get(this.urlTimbang,{no_retur : _noRetur},function(data){
			$('#div_detail_retur_pakan').html(data);
		},'html');
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
					Returpakanfarm.berat_diluar_toleransi(elm,jumlah);					
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
			_message += '<input type="text" placeholder="Jumlah Sak Aktual" id="jumlah_aktual" class="form-control" onchange="Returpakanfarm.kontrol_sak_aktual('+_jml_sak_pallet+')">';
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
	resetTimbang: function(elm){
		var _tr = $(elm).closest('tr');
		$(elm).addClass('hide');
		_tr.find('td:gt(2)').text('');
		_tr.find('input').val('');
	},
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

		Returpakanfarm.konfirmasi_pengiriman(_data);
	
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
									Returpakanfarm.simpanTimbangServer(_data,_infoKendaraan);					
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
	}
};
//end Returpakanfarm

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
				Returpakanfarm.cariReturTimbang();			
			}else{
				Returpakanfarm.cari();			
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

	/*$(document).on('change', '.btn-file :file', function() {
		var input = $(this);
		var _file = input.get(0).files[0];		
		//if($(this).val() != ''){
		Returpakanfarm.notifPDF = false;
		if(!Returpakanfarm.notifPDF){
			if(_file.type !== 'application/pdf'){				
				bootbox.alert("File yang dapat diunggah hanya dengan format pdf",function(){
					$('#lampirkan-file').val('');
				});
				return false;									
			}else{
				var label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
				$('#lampirkan-file').val(label);
			}
		}
		//}				     
    });*/
}());
