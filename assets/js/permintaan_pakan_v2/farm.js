$(function(){
	'use strict';
	Permintaan.add_datepicker($('input[name=startDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		}
	});
	Permintaan.add_datepicker($('input[name=endDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		}
	});
	
}());