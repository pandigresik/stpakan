var ReportBAPD = {
	showListFarm : function(elm){
		var _error = 0;
		var _form = $(elm).closest('form');
		var tanggal = {}, where;
		var _status = _form.find('select[name=status_siklus]').val() || 'O';
		if(_status == 'C'){
			/* tanggal periode chick in harus diisi */
			var _placeholder = {endDate : 'akhir', startDate : 'awal'};
			_form.find('input[name$=Date]').each(function(i,v){
				tanggal[v.name] = v.value;
				if(empty(v.value)){
					_error++;
				}
			});
			if(_error){
				toastr.warning('Harap mengisi parameter tanggal doc in');
			}
		}
		if(!_error){
			switch(_status){
				case 'C':
					var awalDocin = Config._tanggalDb(tanggal['startDate'],' ','-');
					var akhirDocin = Config._tanggalDb(tanggal['endDate'],' ','-');
						where = { where : 'tgl_doc_in between \''+awalDocin+'\' and \''+akhirDocin+'\' and ks.status_siklus = \'C\''};
					break;
				case 'O':
					where = { where : 'ks.status_siklus = \'O\''};
					break;
			}

			/* tampilkan daftar farm */
			$.ajax({
				url : 'home/kertas_kerja/list_farm_all',
				type : 'post',
				data : where,
				dataType : 'json',
				async : false,
				success : function(data){
					if(data.status){
						var _dataFarm = {}, _tmp;
						for(var i in data.content){
							_tmp = data.content[i];
							if(_dataFarm[_tmp['kode_siklus']] == undefined){
								_dataFarm[_tmp['kode_siklus']] = {farm : _tmp['kode_farm'], periode_siklus : _tmp['periode_siklus'], strain : _tmp['kode_strain'], nama_farm : _tmp['nama_farm'], jml_kandang : 0};
							}
							_dataFarm[_tmp['kode_siklus']]['jml_kandang']++;
						}
						var _content = [];
						var _content_str = '';
						if(!empty(_dataFarm)){
							for(var _x in _dataFarm){
								_content.push('<div onclick="ReportBAPD.showBAPD(this)" data-kodefarm="'+_dataFarm[_x]['farm']+'" class="pointer alert alert-info" data-kodesiklus="'+_x+'">');
								_content.push('Farm '+_dataFarm[_x]['nama_farm']+' periode siklus '+_dataFarm[_x]['periode_siklus']);
								_content.push('</div>');
							}
							_content_str = _content.join('');
						}
						else{
							_content_str = 'Data tidak ditemukan';
						}
						$('#list_bapdocin').html(_content_str).children('div').click();
					}
				}
			});
		}
	},
	showBAPD : function(elm){
		var kodefarm = $(elm).data('kodefarm');
		var kodeSiklus = $(elm).data('kodesiklus');
		var where = { where : ' ks.kode_farm=\''+kodefarm+'\' and ks.kode_siklus=\''+kodeSiklus+'\''};

		/* cek apakah detailnya sudah tampil atau belum */
		var _detailElm = $(elm).next('div.listbapd');
		if(!_detailElm.length){
			/* load dari server */
			$.ajax({
				type : 'post',
				dataType : 'html',
				data : where,
				url : 'penerimaan_docin/berita_acara/resumebapd',
				success : function(data){
					$(data).insertAfter($(elm));
				},
			});
		}
		else{
			if(_detailElm.is(':visible')){
				_detailElm.hide();
			}
			else{
				_detailElm.show();
			}
		}
	},
};

(function(){
	'use strict';
	var _disabledate = true;

	$('input[name=startDate]').datepicker({
		dateFormat : 'dd M yy',
		disabled : _disabledate,
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		}
	});
	$('input[name=endDate]').datepicker({
		dateFormat : 'dd M yy',
		disabled : _disabledate,
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		}
	});

	$('select[name=status_siklus]').change(function(){
		var _f = $(this).closest('form');
		if($(this).val() == 'O'){
			_f.find('input[name$=Date]').datepicker('option','disabled',true);
		}
		else{
			_f.find('input[name$=Date]').datepicker('option','disabled',false);
		}
	});
	$('span.btn_cari').click();
}());
