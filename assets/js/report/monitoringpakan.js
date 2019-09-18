"use strict";
$(function(){
	function monitoring(){
		var _url = 'report/monitoringpakan/listpakan';
		var _docin = $('select[name=docin]').val();
		var _flock = $('input[name=flock]').val();
		var _kodefarm = $('select:first').val();
		$.get(_url,{docin : _docin, farm : _kodefarm, flock : _flock},function(data){
			$('#divmonitoringpakan').html(data).find('table').scrollabletable({
				'max_width' : $(window).width() * .97,
			});
		},'html')
	}
	$('#divtampilkan').click(function(){
		monitoring();
	});
	$('select:first').change(function(){
		var _val = $(this).val();
		if(!empty(_val)){
			/* list docin sesuai dengan farm yang dipilih */
			var _url = 'report/monitoringpakan/listDocin';
			$.get(_url,{farm : _val},function(data){
				var _s = $('select[name=docin]');
				var _opt = [];
				for(var i in data){
					_opt.push('<option data-jmlkandang="'+data[i]['jmlkandang']+'" data-populasi="'+data[i]['populasi']+'" data-flock="'+data[i]['flock']+'" value="'+i+'">'+Config._tanggalLocal(i,'-',' ')+'</option>');
				};
				_s.empty();
				_s.append(_opt.join(' '));
				/* set default populasi dan flock */
				var _op = _s.find('option:first');
				var _f = _s.closest('form');
				_f.find('input[name=populasi]').val(number_format(_op.data('populasi'),0,',','.')+' ('+_op.data('jmlkandang')+' Kandang)');
				_f.find('input[name=flock]').val(_op.data('flock'));
			},'json');
		}
	});
	$('select[name=docin]').change(function(){
		var _val = $(this).val();
		if(!empty(_val)){
			/* isi populasi kandang dan flock */
			var _f = $(this).closest('form');
			var _s = $(this).find('option:selected');
			_f.find('input[name=populasi]').val(number_format(_s.data('populasi'),0,',','.')+' ('+_s.data('jmlkandang')+' Kandang)');
			_f.find('input[name=flock]').val(_s.data('flock'));

		}
	})
});
