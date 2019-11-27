'use strict';
var Logistik = {
	buat_do : function(tglKirim,no_op,no_pp){
		/* cari semua parameter pencarian */
	//	var _paramPencarian = {no_op : no_op , no_pp : no_pp};
	//	var _tglKirim = {};
			
	/*	_tglKirim['startDate'] = Config._tanggalDb(tglKirim,' ','-');
		_tglKirim['endDate'] = Config._tanggalDb(tglKirim,' ','-');
	*/	
		/* cari list order pembelian */
		$.ajax({
			type : 'post',
			data : { },
			url : 'permintaan_pakan/pembelian_pakan/order',
			success : function(data){
				$('#main_content').html(data);
			},
		}).done(function(){
			$('input[name=startDate],input[name=endDate]').val(Config._tanggalLocal(tglKirim,'-',' '));
			$('tr.search input[name=no_op]').val(no_op);
			$('tr.search input[name=no_pp]').val(no_pp);
			$('tr.search span.btn').click();
		});
	},	
		
};