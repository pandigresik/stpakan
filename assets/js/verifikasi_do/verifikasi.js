'use strict';

var Verifikasi = {
	_do_length : 8,	
	cek_do : function(elm){
		var _no_do = $(elm).val();
		if(_no_do.length >= this._do_length){
			$.ajax({
				type : 'post',
				data : {no_do : _no_do},
				url :'verifikasi_do/verifikasi/cek_do',
				beforeSend : function(){
					$('#detail_do').empty();
				},
				success : function(data){
					$('#detail_do').html(data);
				},
				dataType : 'html'
			});
		}
		else{
			toastr.error('Panjang minimal nomer do adalah 8 karakter');
		}
	},
	verifikasi_do : function(elm){
		var _no_do = $('#nomerdo').val();
		/* jika sudah ada tabelnya maka do itu valid */
		var _valid = $('#detail_do table').length;
		if(_no_do.length >= this._do_length){
			if(_valid){
				var _message = 'Apakah anda yakin untuk konfirmasi proses muat untuk Nomor DO '+_no_do+' ?';
				bootbox.confirm({
				    title: 'Verifikasi DO',
				    message: _message,
				    buttons: {
				        'cancel': {
				            label: 'Tidak',
				            className: 'btn-default'
				        },
				        'confirm': {
				            label: 'Ya',
				            className: 'btn-danger'
				        }
				    },
				    callback : function(result){
				    	if(result){
				    		/* lakukan penyimpanan */
				    		$.ajax({
				    			type : 'post',
				    			data : {no_do : _no_do},
				    			url : 'verifikasi_do/verifikasi/update_status_do',
				    			dataType : 'json',
				    			success : function(data){
				    				if(data.status){
				    					toastr.success('DO sudah diverifikasi');
				    					$('#nomerdo').val('');
				    					$('#detail_do').empty();
				    				}
				    				else{
				    					toastr.error('DO gagal diverifikasi');
				    				}
				    			},
				    		});	
				    	}
				    	
				    },
				});
			}
			else{
				/* toastr.error('Nomer DO yang diinputkan tidak valid'); */
			}
		}
		else{
			toastr.error('Panjang minimal nomer do adalah 8 karakter');
		}
		
		return false;
	},
};

$(function(){
	$('#nomerdo').focus();
});
