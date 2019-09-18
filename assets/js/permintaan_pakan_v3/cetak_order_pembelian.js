(function(){
	'use strict';
	$("input[name=startDate]").datepicker({
			    //  defaultDate: "+1w",
	      dateFormat : 'dd M yy',
	      onClose: function( selectedDate ) {
	        $( "input[name=endDate]" ).datepicker( "option", "minDate", selectedDate );
	      }
	});
	$("input[name=endDate]").datepicker({
	//  defaultDate: "+1w",
	      dateFormat : 'dd M yy',
	      onClose: function( selectedDate ) {
	        $( "input[name=startDate]" ).datepicker( "option", "maxDate", selectedDate );
	    }
	});

}())
