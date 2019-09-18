$(function(){
	'use strict';
	$('input[name=startDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		}
	});
	$('input[name=endDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		}
	});
	$('input:checkbox[name=belumApprove]').click(function(){
		var _status = $(this).is(':checked');
		/* jika true maka disable datepicker, jik false enable */
		if(_status){
			$('input[name$=Date]').datepicker('option','disabled',1);
			$('select,input:not(:checkbox)').prop('disabled',1);
			$('input[name=status]').prop('checked',0);
			$('span[name=btnCari]').addClass('disabled');
			AktivasiKandang.approval_cari($(this));
		}
		else{
			$('input[name$=Date]').datepicker('option','disabled',0);
			$('select,input:not(:checkbox)').prop('disabled',0);
			$('input[name=status]').prop('checked',1);
			$('span[name=btnCari]').removeClass('disabled');
		}
	});

	AktivasiKandang.approval_cari($(':checked:first'));
	$('select,input:not(:checkbox)').prop('disabled',1);
}());
