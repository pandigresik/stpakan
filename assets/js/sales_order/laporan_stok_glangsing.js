'use strict';

var laporanStokGlangsing = {
	_kodeFarm : null,
	_kodesiklus : null,
	refresh : function(elm){
		laporanStokGlangsing.loadLaporan(elm);
	},
	rowOnClick : function(elm){
		var _kodefarm = $(elm).data('kode_farm');
		var _kodesiklus = $(elm).data('kode_siklus');
		var _buatso = $(elm).data('buatso');
		if(_buatso){
			this._kodeFarm = _kodefarm;
			this._kodesiklus = _kodesiklus;
			var _tbody = $(elm).closest('tbody');
			_tbody.find('tr').css("font-weight","normal");
			_tbody.find('tr[data-kode_siklus='+_kodesiklus+']').css("font-weight","bold");
			$('.btn').removeAttr('disabled');
		}
		/*
		jQuery.each($('tbody tr'), function( k, v) {
			if($(v).data('kode_farm') == laporanStokGlangsing._kodeFarm){
				$(v).css("font-weight","bold");
			}else{
				$(v).css("font-weight","normal");
			}
		});
		*/			
	},

	openSODOPage : function(){
		$('#main_content').load('sales_order/sales_order/index/'+laporanStokGlangsing._kodeFarm+'/'+laporanStokGlangsing._kodesiklus);
	},

	loadLaporan: function(elm){		
		var _tglAwal = Config._tanggalDb($('#startDate').val(),' ','-');		
		var _farm = $('select[name=farm]').val();
		var _url = 'sales_order/laporan_stok_glangsing/listLaporan';
		var _data = {
			kode_farm : _farm,
			tgl_awal : _tglAwal,
			show_outstanding : $(':checkbox[name=show_outstanding]').is(':checked') ? 1 : 0,
			show_all : $(':checkbox[name=show_all]').is(':checked') ? 1 : 0
		};
		$.get(_url,_data,function(html){
			$('#div_list_laporan').html(html);
			$('#div_detail_so').html('');
		},'html');
		
	},

	detail : function(){		
		var _kodefarm = laporanStokGlangsing._kodeFarm;		
		
		if(!empty(_kodefarm)){
			var _url = 'sales_order/laporan_stok_glangsing/detailSO';
			var _tglAwal = Config._tanggalDb($('#startDate').val(),' ','-');		
			var _data = {
				kode_farm : _kodefarm,
				tgl_awal : _tglAwal
			};
			$.get(_url,_data,function(data){
				$('#div_detail_so').html(data);
			},'html');
		}
	},

	detailSO : function(elm){
		var _next = $(elm).next();
		var _tabel_so = _next.find('table');
		var _data = {
			no_so : $(elm).data('so')
		};
		if(!_tabel_so.length){
			var _url = 'sales_order/laporan_stok_glangsing/detailTabelSO';
			$.get(_url,_data,function(data){
				_next.html(data);
			},'html');
		}else{
			if(_tabel_so.is(':visible')){
				_tabel_so.closest('div').hide();
			}else{
				_tabel_so.closest('div').show();
			}
		}
	},
	detailSJ : function(elm){
		var _tabel_detail_sj = $(elm).next();
		if(_tabel_detail_sj.is(':visible')){
			_tabel_detail_sj.hide();
		}else{
			_tabel_detail_sj.show();
		}
	}
};

$(function(){
	$('select[name=farm]').change(function(){
		laporanStokGlangsing.loadLaporan($(this));
	});

		
	$('input[name=startDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
				laporanStokGlangsing.loadLaporan($(this));
			}
		},
		disabled : 1,
		minDate : Config._tglServer,
		defaultDate: Config._tglServer
	});
	$('input[name=endDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
				laporanStokGlangsing.loadLaporan($(this));
			}
		},
		maxDate : Config._tglServer,
		defaultDate: Config._tglServer
	});	
	
	//_f.find('input[name=modal_keb_akhir]').datepicker('option','disabled',0);
	$(':checkbox[name ^= show]').change(function(){
		var _ck = $(':checkbox');
		if($(this).is(':checked')){
			_ck.not($(this)).prop('checked',0);
			$('input[name=startDate]').datepicker('option','disabled',1);	
			laporanStokGlangsing.loadLaporan($(this));		
		}else{
			
			if(!_ck.not($(this)).is('checked')){
				/* enable datepicker */				
				$('input[name=startDate]').datepicker('option','disabled',0);
			}
		}
	});
})

