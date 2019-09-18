(function(){
	'use strict';
	/* jika pilihan farm bukan kosong maka load farm yang dipilih */
	var _farm_terpilih = $('#list_farm select');

	if(!empty(_farm_terpilih.val())){
		Forecast.load_farm(_farm_terpilih.val());
		_farm_terpilih.replaceWith('<label>'+_farm_terpilih.find('option:selected').text()+'</label>');
	}

	$('#list_farm select').change(function(){
		Forecast.load_farm($(this).val());
	});

	/* set tanggal server */
	var _tglServer = $('#tanggal_server').data('tanggal_server');
	Config._setTglServer(_tglServer);
}());
