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
	$('#tampilkan_flok').click(function(){
		var _filterFlok = $(':checkbox[name=filter_flok]').is(':checked') ? 1 : 0;
		var _startDate = (!empty($("input[name=startDate]").val())) ? Config._tanggalDb($("input[name=startDate]").val(),' ','-') : null;
		var _endDate = (!empty($("input[name=endDate]").val())) ? Config._tanggalDb($("input[name=endDate]").val(),' ','-') : null;
		$.ajax({
			type : 'post',
			dataType : 'html',
			data : {filterFlok : _filterFlok, startDate : _startDate, endDate : _endDate},
			url : 'forecast/forecast/flock',
			success : function(data_html){
				$('#div_tabel_flock').html(data_html);
			},
		}).done(function(){
			$('#div_tabel_flock').on('click',':checkbox',function(){
				var _ck_lain = $('#div_tabel_flock :checked').not($(this)).first();
				if(_ck_lain.length){
					if(_ck_lain.val() != $(this).val()){
						$(this).prop('checked',0);
					}
				}
			});	
		});
	});
	
	$('#flock_btn').click(function(){
		var _ck = $('#div_tabel_flock :checked');
		if(_ck.length){
			Forecast.set_flock(_ck);
		}
		else{
			toastr.warning('Pilih salah satu kandang sebelum melakukan set flock');
		}
	});

}());