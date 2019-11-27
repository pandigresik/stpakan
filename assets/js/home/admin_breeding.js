$(window).load(function(){
	'use strict';
	
	var _max_width = $('#main_content .panel-body').innerWidth();
	$('table:first').scrollabletable({
		max_width : _max_width 
	});
/*	
	$('table>tbody>tr').dblclick(function(){
		var _url = $(this).data('url');
		var _part = _url.split('/');
		var no_op = $(this).find('td:eq(1)').text();
		var tgl_buat = $(this).find('td:eq(2)').text();
	//	console.log(tgl_buat);
		var tgl_kirim = new Date(Config._tanggalDb(tgl_buat,' ','-'));
		//* kadaluarsa OP adalah 1 bulan 
		tgl_kirim.setDate(tgl_kirim.getDate() + 30);
		if(_part[0] == 'forecast'){
			Home.replace_main_content(_url);
		}
		else{
			$.ajax({
				type : 'post',
				data : { },
				url : _url,
				success : function(data){
					$('#main_content').html(data);
				},
			}).done(function(){
				$('input[name=startDate]').val(tgl_buat);
				$('input[name=endDate]').val(Config._tanggalLocal(Config._getDateStr(tgl_kirim,'-'),'-',' '));
				$('tr.search input[name=no_op]').val(no_op);
			//	$('tr.search input[name=no_pp]').val(no_pp);
				$('tr.search span.btn').click();
			});
		}
		
	});
	*/
});
	
