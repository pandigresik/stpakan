'use strict';
var Forecast_pp = {
	tampilkan : function(elm){
		var _f = $(elm).closest('form');
		var _s = _f.find('select');
		var _kf = _s.val();
		var _error = 0;
		if(empty(_kf)){
			_error++;
			toastr.error('Pilih salah satu farm terlebih dahulu');
		}
		else{
			$.ajax({
				beforeSend : function(){
					$('#div_kebutuhan_pakan').html('');
				},
				url : 'report/forecast_pp/forecast_vs_pp/'+_kf,
				dataType : 'html',
				success : function(data){
					$('#div_kebutuhan_pakan').html(data);
				}
			})
			.done(function(){
					$('#div_kebutuhan_pakan').find('table:first').scrollabletable();

			});

		}
	},
}
