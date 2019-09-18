(function(){
	'use strict';
	/* jika pilihan farm bukan kosong maka load farm yang dipilih */
	$('.drop_reject').droppable({
		accept : '.drag_card',
		drop : function(e,ui){
			var _w = ui.draggable;
			/* simpan approval presdir */
			$.ajax({
				type : 'post',
				dataType : 'json',
				url : 'forecast/forecast/reject_presdir',
				data : {no_reg : _w.data('no_reg')},
				success : function(data){
					if(data.status){
						_w.siblings('div').remove();
						_w.remove();
						toastr.success('Proses reject berhasil');
					}
				},
			});
		},
	});
	$('.drop_approve').droppable({
		accept : '.drag_card',
		drop : function(e,ui){
			var _w = ui.draggable;
			/* simpan approval presdir */
			$.ajax({
				type : 'post',
				dataType : 'json',
				url : 'forecast/forecast/approve_presdir',
				data : {no_reg : _w.data('no_reg')},
				success : function(data){
					if(data.status){
						_w.siblings('div').remove();
						_w.parent().append('<i class="pull-right glyphicon glyphicon-ok"></i>');
						_w.remove();
						toastr.success('Proses approval berhasil');
					}
				},
			});
		},
	});
	$('.drag_card').draggable({
		containment : 'parent',
		axis : 'x',
		revert : 'invalid',
		
	});
}());