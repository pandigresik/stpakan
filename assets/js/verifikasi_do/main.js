'use strict';

var VerifikasiDO = {

	_do_length : 8,	
	hidden : function(){

		$('#fkendaraan').hide();
		$('#fkendaraan').find('input').val('');
		$('#fkendaraan').find('.span-info').html('');

		$('#fpin').hide();
		$('#fpin').find('input').val('');
		$('#fpin').find('.span-info').html('');
	},

	show : function(){

		$('#fkendaraan').show();
		$('#fkendaraan').find('input').val('');

		$('#fpin').show();
		$('#fpin').find('input').val('');
	},

	get_do : function(elm){
		var _no_do = $(elm).val();
		if(_no_do.length >= this._do_length){
			$.ajax({
				type : 'post',
				data : {no_do : _no_do},
				url :'sales_order/verifikasi_do/getDO',
				beforeSend : function(){
					VerifikasiDO.hidden();
				},
				success : function(data){
					if (data.status == '1') {
						var _p = $(elm).closest('.form-group');						
						_p.find('.span-info').html('<span class="glyphicon glyphicon-ok-sign"></span>');						
						$('#fkendaraan').show();
						$('#fkendaraan').find('input').focus();
					} else {
						var _p = $(elm).closest('.form-group');						
						_p.find('.span-info').html('<span class="text-danger">No. DO ditolak </span><span class="glyphicon glyphicon-remove-sign"></span>');						
						$('#fkendaraan').hide();
					}
				},
				dataType : 'json'
			});
		}
		else{
			toastr.error('Panjang minimal nomer do adalah 8 karakter');
		}
	},

	get_kendaraan : function(elm){
		var _no_kendaraan = $(elm).val();
		var _no_do = $('input[name=no_do]').val();
		// if(_no_kendaraan.length >= this._do_length){
			$.ajax({
				type : 'post',
				data : {no_kendaraan : _no_kendaraan, no_do : _no_do},
				url :'sales_order/verifikasi_do/getKendaraan',
				beforeSend : function(){
					$('#fpin').hide();
					$('#fpin').find('input').val('');
					$('#fpin').find('.span-info').html('');
				},
				success : function(data){
					if (data.status == '1') {
						var _p = $(elm).closest('.form-group');						
						_p.find('.span-info').html('<span class="glyphicon glyphicon-ok-sign"></span>');
						//$(elm).parent().find('.input-group-addon').find('p').html("");
						$('#fpin').show();					
						$('#fpin').find('input').focus();
					} else {
						var _p = $(elm).closest('.form-group');	
						_p.find('.span-info').html('<span class="text-danger">No. Kendaraan ditolak </span><span class="glyphicon glyphicon-remove-sign"></span>');						
						// $(elm).parent().find('.input-group-addon').find('p').html("No. Kendaraan ditolak");
						$('#fpin').hide();
					}
				},
				dataType : 'json'
			});
		// }
		/*else{
			toastr.error('Panjang minimal nomer do adalah 8 karakter');
		}*/
	},

	get_pin : function(elm){
		var _kode_verifikasi = $(elm).val();
		var _no_do = $('input[name=no_do]').val();
		var _no_kendaraan =  $('input[name=no_kendaraan]').val();
		// if(_no_kendaraan.length >= this._do_length){
			$.ajax({
				type : 'post',
				data : {
					kode_verifikasi : _kode_verifikasi
					,no_do : _no_do
					,no_kendaraan : _no_kendaraan					
				},
				url :'sales_order/verifikasi_do/getPin',
				beforeSend : function(){
					$('#fpin').find('.span-info').html('');
				},
				success : function(data){
					if (data.status == '1') {
						//$(elm).parent().find('.input-group-addon').find('p').html("");
						var _p = $(elm).closest('.form-group');						
						_p.find('.span-info').html('<span class="glyphicon glyphicon-ok-sign"></span>');

						var no_do = $('input[name=no_do]').val();
						var no_kendaraan = $('input[name=no_kendaraan]').val();
						var no_pin = _kode_verifikasi;

						VerifikasiDO.verifikasi(no_do, no_kendaraan, no_pin);

					} else {
						var _p = $(elm).closest('.form-group');	
						_p.find('.span-info').html('<span class="text-danger">Pin Otentikasi ditolak </span><span class="glyphicon glyphicon-remove-sign"></span>');
						//$(elm).parent().find('.input-group-addon').find('p').html("No. Pin ditolak");
					}
				},
				dataType : 'json'
			});
		// }
		/*else{
			toastr.error('Panjang minimal nomer do adalah 8 karakter');
		}*/
	},

	verifikasi : function(no_do, no_kendaraan, no_pin){
		$.ajax({
			type : 'post',
			data : {
				kode_verifikasi : no_pin,
				no_kendaraan : no_kendaraan,
				no_do : no_do
			},
			url :'sales_order/verifikasi_do/verifikasiDO',
			beforeSend : function(){

			},
			success : function(data){
				if (data.status == '1') {
					VerifikasiDO.hidden();

					$('input').val('');
					$('#nomerdo').focus();
					toastr.success('Verifikasi DO berhasil', 'Berhasil');

					return;
				}
			},
			dataType : 'json'
		});
	}
};


VerifikasiDO.hidden();

$(function(){
	$('#nomerdo').focus();
});
