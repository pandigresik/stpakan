var exp_op = {
	
	tampilkan_data : function(){
		$.ajax({
			url : 'report/Laporan_expedisi_op/get_data_table',
			type : 'POST',
			data : {
				bDate	: $('input[name=startDate]').val(),
				eDate	: $('input[name=endDate]').val(),
			},
			dataType : 'html',
			success : function(data){
				$('#table_expedisi_op').html(data);
			}
		});
	},
	
};

$(function(){		
	$('input[name=startDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		},		
	});
	$('input[name=endDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		},		
	});	
}());