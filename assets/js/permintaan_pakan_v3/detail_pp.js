(function(){
	'use strict';
	$('#div_detail_pp table tbody tr').each(function(){
		var _gabung = {};
		$(this).find('.box-kendaraan').each(function(){
			if(_gabung[$(this).data('no_urut')] === undefined){
				_gabung[$(this).data('no_urut')] = [];
			}
			_gabung[$(this).data('no_urut')].push($(this));
		});
		for(var i in _gabung){
			if(_gabung[i].length > 1){
				$.each(_gabung[i],function(){
					$(this).addClass('gabung_do');
				});
			}
		}
	});

}());