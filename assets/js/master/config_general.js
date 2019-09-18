'use strict'
var GeneralConfig = {
	prosesServer: 0,
	total_page: 0,
	getReport: function(page_number){
		if(page_number==1){
			$("#previous").prop('disabled', true);}
		else{
			$("#previous").prop('disabled', false);}
			
		if(page_number == this.total_page ){
			$("#next").prop('disabled', true);}
		else{
			$("#next").prop('disabled', false);}

		$("#page_number").text(page_number);
		var _dataCari = {
			'page' : page_number
		}
		var _kode_farm = $('select[name=kode_farm]').val();
		var _context = $('select[name=context]').val();

		if(!empty(_kode_farm)){
			_dataCari['kode_farm'] = _kode_farm;
		}

		if(!empty(_context)){
			_dataCari['context'] = _context;
		}

		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/general_config/get_pagination/",
			data: {
				cari : _dataCari
			}
		})
		.done(function(data){
			var _tbody = $("#master-general-config tbody");
			_tbody.html("");
			if(!empty(data.status)){
				if(data.TotalRows > 0){
					GeneralConfig.total_page= data.TotalRows;
					$("#total_page").text(GeneralConfig.total_page);
					var record_par_page = data.Rows;
					var _tr = [], _status, _nomerAwal = ((page_number - 1) * data.limit) + 1 ;
					$.each(record_par_page, function (key, _data) {
						_status = (_data.STATUS) ? "Aktif" : "Tidak Aktif";
						_tr = [];
						_tr.push('<tr data-kode_farm="'+_data.KODE_FARM+'" data-kode_config="'+_data.KODE_CONFIG+'">');
						_tr.push('<td>'+(_nomerAwal++)+'</td>');
						_tr.push('<td>'+_data.KODE_FARM+'</td>');
						_tr.push('<td>'+_data.CONTEXT+'</td>');
						_tr.push('<td>'+_data.KODE_CONFIG+'</td>');
						_tr.push('<td>'+_data.DESCRIPTION+'</td>');
						_tr.push('<td><span class="nilai">'+_data.VALUE+'</span><span onclick="GeneralConfig.editSimpan(this)" class="pull-right glyphicon glyphicon-edit"></span><div class="checkbox hide"><label><input type="checkbox"><small>Ubah di farm</small></label></div></td>');
						_tr.push('<td>'+_status+'</td>');
						_tr.push('</tr>');

						_tbody.append(_tr.join(''));
					});

					if(total_page == 1)
						$("#next").prop('disabled', true);
				}
			}else{
				$("#page_number").text('0');
				$("#total_page").text('0');
				$("#next").prop('disabled', true);
			}
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
		});
	},
	prev: function(elm){
		var page_number = $('#page_number').text();
		GeneralConfig.getReport(--page_number);
	},
	next: function(elm){
		var page_number = $('#page_number').text();
		GeneralConfig.getReport(++page_number);
	},
	editSimpan: function(elm){
		$(elm).toggleClass('glyphicon-check glyphicon-edit');
		if($(elm).hasClass('glyphicon-check')){
			this.inlineEdit(elm);
		}else{
			this.saveEdit(elm);
		}
	},
	inlineEdit: function(elm){
		var _td = $(elm).closest('td');
		var _nilai = _td.find('span.nilai').text();
		_td.find('div.checkbox').removeClass('hide');
		var _tmp = $('<input type="text" class="form-control nilai" value="'+_nilai+'" style="width:60px" />');
		_tmp.numeric({
			allowPlus : false, // Allow the + sign
			allowMinus : true, // Allow the - sign
			allowThouSep : false, // Allow the
			allowDecSep : false
		});
		_td.find(':checkbox').prop('checked',0);
		_td.find('span.nilai').replaceWith(_tmp);
	},
	saveEdit: function(elm){
		var _td = $(elm).closest('td');
		var _tr = _td.closest('tr');
		var _nilai = _td.find('input.nilai').val();
		var _checkbox = _td.find(':checkbox');
		if(empty(_nilai)){
			bootbox.alert('nilai config harus diisi',function(){
				_td.find('input.nilai').focus();
			});
			return;
		}

		var _data = {
			kode_farm : _tr.data('kode_farm'),
			kode_config : _tr.data('kode_config'),
			value : _nilai,
			sinkron : _checkbox.is(':checked') ? 1 : 0
		};
		/** simpan ke database */
		if(!this.prosesServer){
			var ini = this;
			$.ajax({
				url: 'master/general_config/simpan',
				data: {data : _data},
				type: 'post',
				dataType: 'json',
				beforeSend: function(){
					ini.prosesServer = 1;
				},
				success: function(data){
					if(data.status){
						toastr.success(data.message);
						_td.find('div.checkbox').addClass('hide');
						_td.find('input.nilai').replaceWith('<span class="nilai">'+_nilai+'</span>');
					}
				}
			}).done(function(){
				ini.prosesServer = 0;
			})
		}else{
			bootbox.alert('Masih menunggu response dari server ');
		}
	}
};

$(document).ready(function () {
	GeneralConfig.getReport(1);
})
