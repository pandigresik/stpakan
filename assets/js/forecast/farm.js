(function(){
	'use strict';
	var canCreateForecast = $('#canCreateForecast').text();
	var lockEditPakan = $('#lockEditPakan').text();
	var lockEditDocIn = $('#lockEditDocIn').text();
	$('#canCreateForecast').remove();
	Forecast.canCreateForecast = canCreateForecast;
	Forecast.lockRubahPakan = lockEditPakan.split(',');
	Forecast.setLockEditDocIn(lockEditDocIn.split(','));
	if(Forecast.canCreateForecast == 1){
		Forecast.draggable_tutupsiklus($('#tutup_siklus div.row:not(:first)'));
		
		$('#tutup_siklus').droppable({
			accept : function(elm){
				var r = false;
				var s = $(elm).find('span._status_approval').text();
				if(s == '*'){
					r = true;
				}
			//	return $(elm).find('a').length;
				return r;
			},
			drop : function(e,ui){
				var _w = ui.draggable;
				var _h = ui.helper;
				/* remove elm yang dipilih */
				_w.remove();
				/* tambahkan elm baru data-value="detail_kandang"*/
				var _data_kandang = _h.find('span[data-value="detail_kandang"]').text().split('/');
				var _classDiv = 'col-md-'+Math.ceil(12/_data_kandang.length);
				var _divRow = $('<div class="row"><div class="'+_classDiv+'">'+_data_kandang.join('</div><div class="'+_classDiv+'">')+'</div></div>');
				Forecast.draggable_tutupsiklus(_divRow);
				Forecast.tutup_siklus_row_edit(_divRow.find('div[class^="col"]'));
				$(this).find('div.contentTable').append(_divRow);
				_divRow.find('div:gt(2)').addClass('number');
				Forecast.periksaApproval();
				
			}
		});
		/* fitur inline edit */
	//	Forecast.tutup_siklus_row_edit($('#tutup_siklus .contentTable>.row>div[class^="col"]'));
	}
	
	/* jadikan draggable untuk blok tanggal doc in */
	var _elmDrag = $('#div_forecast ul>li>a').closest('li');
	var _elmDrop = _elmDrag.closest('ul').closest('li');
	Forecast.draggable_forecast_tree(_elmDrag);
	Forecast.droppable_tree(_elmDrop);
	
	/* perbaiki tampilan tree */
	var _text = '';
	
	$('#div_forecast ul>li>a').each(function(){
		_text = $(this).text().split('#');
		$(this).text(_text[0]);
		var _labelClass = '';
		
		switch (_text[2]){
			case 'Baru' :
				_labelClass = 'label-primary';
		
				break;
			case 'Draft':
				_labelClass = 'label-warning';
		
				break;
			case 'Acc1':
				_labelClass = 'label-info';
				break;	
			case 'Acc2':
				_labelClass = 'label-success';
				break;
			default:
				_labelClass = 'label-default';
			
		}
		$('<span class="_status_approval label '+_labelClass+'">'+_text[2]+'</span><span class="hide" data-value="detail_kandang">'+_text[1]+'</span><span class="no_reg hide">'+_text[3]+'</span>').insertAfter($(this));
		
	});
	
	/* sebenarnya bukan context menu, hanya untuk melihat kebutuhan pakan */
	Forecast.add_contextmenu_kandang(_elmDrag);
	/* tambahkan contextmenu untuk tahun dan bulan */
	$('#div_forecast ul>li>label').each(function(){
		if(Forecast.is_tahun($(this).text())){
			Forecast.add_contextmenu_tahun($(this));
		}
		else if(Forecast.is_bulan($(this).text())){
			$(this).addClass('bulan');
			Forecast.add_contextmenu_bulan($(this));
		}
	});
		
	$('#div_tombol_simpan div.btn').click(function(){
		var _aksi = $(this).data('aksi');
		var _error = 0;
		var _tglDOCIn = $('#TglCheckIn').text();
		var _idKandang = $('#infoKandang').text();
		var _kandangDisimpan = [];
		var _tmp =  _tglDOCIn.split(' ');
		var _tahun = _tmp[2], _bulan = _tmp[1], _tgl = _tmp[0]; 
		
		/* cari elemen dari tglDocIn */
		var _elmTahun = $('#div_forecast').find('label:contains('+_tahun+')');
		var _elmBulan = _elmTahun.siblings('ul').find('label:contains('+_bulan+')');
		var _elmTanggal = _elmBulan.siblings('ul').find('label:contains('+_tgl+')');
		var _elmKandang;
		var _docIn = _tahun+'-'+($.datepicker.regional['id'].monthNamesShort.indexOf(_bulan)+1)+'-'+_tgl;
		/* dapatkan semua kodepj untuk umur tertentu berdasarkan jeniskelamin */
		var _umurTmp, _pakanJantan = [], _pakanBetina = [],_kodepj,_elmKodepj;
		/* cek apakah ada pakan yang dirubah atau tidak */
		var _pakanJantanBerubah = [],_pakanBetinaBerubah = [];
		
		var _tglServer = Config._tglServer;
		/* max buat forecast adalah h - 21 */
		var _maxTglDocIn = new Date(Config._convertTgl(_docIn));
		_maxTglDocIn.setDate(_maxTglDocIn.getDate() - Forecast.maxBuatForecast);
		
		if(_maxTglDocIn < _tglServer ){
			_error++;
			toastr.error('Max h-21 dari tanggal DocIn ( '+_tglDOCIn+' ) adalah '+ Config._tanggalLocal(Config._getDateStr(_maxTglDocIn,'-'),'-',' '));
		}
		
		$('#pakan_jantan table tbody tr').each(function(){
			_umurTmp = $(this).find('td:first').text();
			_elmKodepj = $(this).find('td:nth-child(2)');
			if(_elmKodepj.find('select').length){
				_kodepj = _elmKodepj.find('select').val(); 
			}
			else {
				_kodepj = _elmKodepj.text();
			}
			if($(this).hasClass('pakan_dirubah')){
				_pakanJantanBerubah.push({umur : _umurTmp, kodepj : _kodepj});
			}
			_pakanJantan.push({ umur : _umurTmp, kodepj : _kodepj});
		});
		
		$('#pakan_betina table tbody tr').each(function(){
			_umurTmp = $(this).find('td:first').text();
			_elmKodepj = $(this).find('td:nth-child(2)');
			if(_elmKodepj.find('select').length){
				_kodepj = _elmKodepj.find('select').val(); 
			}
			else {
				_kodepj = _elmKodepj.text();
			}
			if($(this).hasClass('pakan_dirubah')){
				_pakanBetinaBerubah.push({umur : _umurTmp, kodepj : _kodepj});
			}
			_pakanBetina.push({ umur : _umurTmp, kodepj : _kodepj});
			
		});
		
		if(empty(_idKandang)){
			/* dapatkan semua informasi kandangnya pada tgl docin tersebut */
			_elmKandang = _elmTanggal.siblings('ul').find('li'); 
		}
		else{
			_elmKandang = _elmTanggal.siblings('ul').find('li:contains('+_idKandang+')');
		}
				
		var _prosesKandang = {}, _insertKandang = [], _updateKandang = [];
		
		if(_aksi == 'simpan'){
			
			/* jika yang dirubah adalah pakannya maka update semuanya */
			if(!empty(_pakanJantanBerubah) || !empty(_pakanBetinaBerubah)){
				/* dapatkan semua kandang dari docIn terpilih yang statusnya * */
				_elmKandang.each(function(){
					/* jika statusnya * lakukan insert , jika Draft maka update */
					if($(this).find('span.label').text() == '*'){
						_insertKandang.push($(this));
					}
					else if($(this).find('span.label').text() == 'Draft'){
						_updateKandang.push($(this));
					}
				});
				
			}
			else{
				/* dapatkan semua kandang dari docIn terpilih yang statusnya * */
				_elmKandang.each(function(){
					/* jika statusnya * lakukan insert , jika Draft maka update */
					if($(this).find('span.label').text() == '*'){
						_insertKandang.push($(this));
					}
					else if($(this).find('span.label').text() == 'Draft' && $(this).hasClass('telahBerubah')){
						_updateKandang.push($(this));
					}
				});
			}
			
			_prosesKandang = {'insert' : _insertKandang, 'update' : _updateKandang};
			
		}
		else if(_aksi == 'rilis'){
			
			/* dapatkan tglDocIn dan semua kandang yang statusnya * dan Draft */
			_elmKandang.each(function(){
				/* jika statusnya * lakukan insert , jika Draft maka update */
				if($(this).find('span.label').text() == '*'){
					_insertKandang.push($(this));
				}
				else if($(this).find('span.label').text() == 'Draft'){
					_updateKandang.push($(this));
				}
			});
			_prosesKandang = {'insert' : _insertKandang, 'update' : _updateKandang};
		}
		else if(_aksi == 'approve'){
			/* dapatkan tglDocIn dan semua kandang yang statusnya Baru */
			_elmKandang.each(function(){
				/* jika statusnya * lakukan insert , jika Draft maka update */
				if($(this).find('span.label').text() == 'Baru'){
					_updateKandang.push($(this));
				}
				_prosesKandang = {'update' : _updateKandang};
			});
		}
		else{
			toastr.error('Aksi tidak ditemukan ');
			_error++;
		}
		if(empty(_tglDOCIn)){
			_error++;
			toastr.error('Tidak ada yang dipilih');
		}
		
		
		/* cek apakah ada yang diupdate atau tidak */
		var _kosong = 0;
		for(var _y in _prosesKandang){
			if(!empty(_prosesKandang[_y])){
				_kosong = 1;
			}
		}
		if(!_kosong){
			_error++;
			toastr.error('Tidak ada yang berubah');
		}
	
		if(!_error){
			switch(_aksi){
			case 'simpan':
				Forecast.simpan_forecast(_prosesKandang,_pakanBetina,_pakanJantan,_docIn,_pakanBetinaBerubah,_pakanJantanBerubah);
				break;
			case 'rilis':
				Forecast.rilis_forecast(_prosesKandang,_pakanBetina,_pakanJantan,_docIn,_pakanBetinaBerubah,_pakanJantanBerubah);
				break;
			case 'approve':
				Forecast.approve_forecast(_prosesKandang,_docIn,_pakanBetinaBerubah,_pakanJantanBerubah);
				break;
			}
		
			
		}
		
	});
	
	$('#tutup_siklus div.row').each(function(){
		$(this).find('div:gt(2)').addClass('number');
	});
	Forecast.reset();
	Forecast.init();
	Forecast.periksaApproval();

}());