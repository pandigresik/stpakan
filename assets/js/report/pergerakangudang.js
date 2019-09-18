"use strict";
$(function(){
	var _form = $('form');
	var _ks = {};
	var _kodesiklus = _form.find('select[name=kode_siklus]');
	_form.find('select:first').change(function(){
		var _kodefarm = $(this).val();
		if(empty(_kodefarm)){
			_kodesiklus.empty();
		}else{
			/* ambil list kode siklus berdasarkan farm yang dipilih */
			if(_ks[_kodefarm] == undefined){
				$.get('report/pergerakangudang/listPeriode/'+_kodefarm,{},function(data){
					if(data.status){
						_ks[_kodefarm] = {};
						for(var i in data.content){
							_ks[_kodefarm][data.content[i]['kode_siklus']] = data.content[i]['periode_siklus'];
						}
						setOption(_kodesiklus,_ks[_kodefarm]);
					}
				},'json');
			}else{
				setOption(_kodesiklus,_ks[_kodefarm]);
			}
		}
	});

	$('#divtampilkan').click(function(){
		var _form = $('form');
		var _kodesiklus = _form.find('select[name=kode_siklus]').val();

		$.get('report/pergerakangudang/gerakgudang',{kode_siklus : _kodesiklus},function(data){
			$('#divpergerakangudang').html(data);
		},'html');
	});
	function setOption(elm,opt){
		var _tmp = [];
		$(elm).empty();
		for(var i in opt){
			_tmp.push('<option value="'+i+'">'+opt[i]+'</option>');
		}
		_tmp.reverse();
		$(elm).append(_tmp.join(' '));
	}
})
