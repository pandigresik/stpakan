$(function(){
	'use strict';
	var tgl_server = $('#tanggal_server').data('tanggal_server');
	Config._setTglServer(tgl_server);
	Permintaan.add_datepicker($('input[name=startDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		},
		maxDate : Config._tglServer
	});
	Permintaan.add_datepicker($('input[name=endDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		},
		maxDate : Config._tglServer
	});

	var _tahun_sekarang = tgl_server.substring(0,4);
	var _tahun_lalu = _tahun_sekarang - 2;
	var _opt = [];
	while(_tahun_lalu <= _tahun_sekarang){
		_opt.push('<option value="'+_tahun_sekarang+'">'+_tahun_sekarang+'</option>');
		_tahun_sekarang--;
	}
	$('select[name=periode_doc_in]').append(_opt.join(' '));
}());
