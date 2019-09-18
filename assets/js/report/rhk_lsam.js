$(function(){
	'use strict';
	var tgl_server = $('#tanggal_server').data('tanggal_server');
	$("input[name=startDate]").datepicker({
				//  defaultDate: "+1w",
					dateFormat : 'dd M yy',
					maxDate : 'today',
					onSelect: function( selectedDate ) {
						$( "input[name=endDate]" ).datepicker( "option", "minDate", selectedDate );
					},
					disabled: true,
			 });
	 $("input[name=endDate]").datepicker({
				//  defaultDate: "+1w",
					dateFormat : 'dd M yy',
					maxDate : 'today',
					onSelect: function( selectedDate ) {
						$( "input[name=startDate]" ).datepicker( "option", "maxDate", selectedDate );
				},
				 disabled: true,
			});
$('select[name=status_siklus]').change(function(){
		var _val = $(this).val();
		var _aktif = _val == 'O' ? 1 : 0;
		$( "input[name$=Date]" ).datepicker( "option", "disabled", _aktif );
});

	$('#tampilkan_btn').click();

}());
