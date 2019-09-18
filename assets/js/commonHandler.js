'use strict';

var Home = {
	load_main_content : function(event, elm, url, target) {
		var _url = url.split('#')[1] || null;
		if (!empty(_url)) {
			$(target).empty().load(_url);
		}

		event.preventDefault();
	},
	replace_main_content : function(url){
		$('#main_content').empty().load(url);
	},
	redirect_to_url : function(elm){
		var ini = $(elm);
		var context = ini.find('td:first').text();
		var no = ini.find('td:eq(1)').text();
		var tgl = ini.find('td:eq(2)').text();
		var kode_farm = ini.data('kode_farm');
		switch(context){
			case 'Forecast':
				this.to_forecast(kode_farm,tgl,no);
				break;
			case 'Permintaan':
				this.to_pp(kode_farm,tgl,no);
				break;
			default:
				this.to_op(kode_farm,tgl,no);
		}
	},
	to_op : function(kode_farm,tgl_buat,no_op){
		var url = 'permintaan_pakan/pembelian_pakan/order/1';
		$.ajax({
			type : 'post',
			data : { },
			url : url,
			async : false,
			success : function(data){
				$('#main_content').html(data);
			},
		}).done(function(){
			setTimeout(function(){
				var tgl_kirim = new Date(Config._tanggalDb(tgl_buat,' ','-'));
				//* kadaluarsa OP adalah 1 bulan 
				tgl_kirim.setDate(tgl_kirim.getDate() + 30);
				$('input[name=startDate]').val(tgl_buat);
				$('input[name=endDate]').val(Config._tanggalLocal(Config._getDateStr(tgl_kirim,'-'),'-',' '));
				$('tr.search input[name=no_op]').val(no_op);
			//	$('tr.search input[name=no_pp]').val(no_pp);
				$('tr.search span.btn').click();
			},1000);
		});
	},
	to_pp : function(kode_farm,tgl_buat,no_pp){
		var url = 'permintaan_pakan/permintaan_pakan/kepala_farm/'+kode_farm;
		$.ajax({
			type : 'post',
			data : { },
			url : url,
			success : function(data){
				$('#main_content').html(data);
			},
			async : false,
		}).done(function(){
			setTimeout(function(){
				var _td = $('table tbody td:contains(\''+no_pp+'\')');
				_td.closest('tr').find('td:first span.link_span').click();
			},1000);
		});
	},
	to_forecast : function(kode_farm,tgl_doc_in,no_reg){
		var url = 'forecast/forecast/kepalafarm/'+kode_farm;
		$.ajax({
			type : 'post',
			data : { },
			url : url,
			success : function(data){
				$('#main_content').html(data);
			},
			async : false,
		}).done(function(){
			setTimeout(function(){
				var _tgl = tgl_doc_in.split(' ');
				var _tree = $('#div_forecast.css-treeview');
				var _elm_tahun = _tree.find('li:contains(\''+_tgl[2]+'\')').eq(0);
				_elm_tahun.find('input:checkbox').eq(0).click();
/*
				var _elm_bulan = _elm_tahun.find('li:contains(\''+_tgl[1]+'\')').eq(0);
				_elm_bulan.find('input:checkbox').eq(0).click();

				var _elm_tgl = _elm_bulan.find('li:contains(\''+_tgl[0]+'\')').eq(0);
				_elm_tgl.find('input:checkbox').eq(0).click();
				
*/
				var _elm_bulan = _elm_tahun.find('li:contains(\''+_tgl[1]+'\')').eq(0);
				_elm_bulan.find('input:checkbox').eq(0).click();

				var _elm_tgl_label = _elm_bulan.find('li>label:contains(\''+_tgl[0]+'\')');
				var _elm_tgl = _elm_tgl_label.closest('li');
				_elm_tgl.find('input:checkbox').eq(0).click();
				
				var _elm_no_reg = _elm_tgl.find('li:contains(\''+no_reg+'\')').eq(0);
				_elm_no_reg.click();
				
			},1000) 
		});
	},
	changePassword : function (){
		
		var oldPassword = $('#divChangePassword input[name=oldPassword]').val();
		var newPassword = $('#divChangePassword input[name=newPassword]').val();
		var confirmPassword = $('#divChangePassword input[name=confirmPassword]').val();
		var sama = 1;
		if(newPassword != confirmPassword){
			sama = 0;
		}
		if(sama){
			$.ajax({
				url:'user/changePassword',
				data:{oldPassword : oldPassword, newPassword : newPassword},
				type:'POST',
				dataType:'json',
				beforeSend: function(){},
				success: function(data){
					if(data.status){
						toastr.success(data.message);
						bootbox.hideAll();
					}
					else{
						toastr.error(data.message);
					}
				},
				error: function(){}
			});
		}
		else{
			toastr.error('Password belum sama');
		}
		return false;
	},
	openLinkShortcut : function(e){
		$('#main_content').empty().load(e);
	},

	getDataTimbang: function(elm) {
        $.get('api/timbangan/timbang', {}, function(data) {
            if (data.status) {
                $(elm).val(data.content);                              
            } else {
                bootbox.alert(data.message);
            }

        }, 'json');
    },

};
