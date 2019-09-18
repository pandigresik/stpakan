(function(){
	'use strict';
	/* jika pilihan farm bukan kosong maka load farm yang dipilih */
	$('#filter_farm').click(function(){
		Forecast.modal_filter_farm('checkedAll',function(_form){
			var _filter = {};
			$('div.filter_div :checked').each(function(){
				_filter[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0; 
			});
			/* dapatkan semua farm berdasarkan parameter yang diberikan */
			var _farm_terpilih = [];
			_form.find(':checked').each(function(){
				_farm_terpilih.push($(this).val());
			})
			if(!empty(_farm_terpilih)){
				$.ajax({
					type : 'post',
					url : 'forecast/forecast/list_farm_approval2',
					data : {filter : _filter, farm  : _farm_terpilih},
					success : function(data){
						$('#tabel_forecast').html(data);
					},
				});
			}
			else{
				toastr.warning('Tidak ada farm yang dipilih');
			}
		});
	})
}())