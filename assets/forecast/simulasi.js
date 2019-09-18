function showHideNextElm(elm){
	var _nextElm = $(elm).next();
	if(_nextElm.is(':visible')){
		_nextElm.addClass('hide');
	}
	else{
		_nextElm.removeClass('hide');
	}
}
var _dataSimulasi = {};
$(function(){
	'use strict';
	/* set tanggal server */
	var _tglServer = $('#tanggal_server').data('tanggal_server');
	Config._setTglServer(_tglServer);
	$('div[name=divFarm] select').change(function(){
		var _elm = $(this);
		var _val = _elm.val();
		if(!empty(_val)){
			var _url = 'forecast/simulasi/kandang_pending/'+_val;
			$.get(_url,{},function(data){
				$('#div_forecast').html(data);
			}
		).done(function(){
			/* perbaiki tampilan tree */
			var _text = '';
			$('#div_forecast ul>li>a').each(function(){
				_text = $(this).text().split('#');
				$(this).text(_text[0]);
				$('<span class="hide" data-value="detail_kandang">'+_text[1]+'</span><span class="no_reg hide">'+_text[2]+'</span>').insertAfter($(this));
				$('<input type="checkbox" class="checksimulasi" style="opacity:1" />').insertBefore($(this));
				$(this).css({
						'background' : 'none'
				});
			});
		});
		}else{
			$('#div_forecast').html('');
		}
	});

	/* ketika btnSimulasiKirim diklik, tampilkan simulasi rencana kirim */
	$('div#btnSimulasiKirim').click(function(){
		AktivasiKandang.setBisaKonfirmasi(1);
		/* periksa apakah checksimulasi ada yang dicentang */
		var _terpilih = $('input.checksimulasi:checked');
		if(_terpilih.length){
			var _arrdocin = {};
			var _tgldocindb;
			var _tmp,_elmKandang,_tglDocIn,_populasi,_namaFarm,_kodeFarm,_rencanaKirim,_totalPopulasi,_indexrencana = [];
			_namaFarm = $('div[name=divFarm] select>option:selected').text();
			_kodeFarm = $('div[name=divFarm] select').val();
			_terpilih.each(function(n,v){
				_elmKandang = AktivasiKandang.parseElmKandang(v.closest('li'));
				_tglDocIn = [_elmKandang.tanggal,_elmKandang.bulan,_elmKandang.tahun].join(' ');
				_populasi = $(v).siblings('span[data-value=detail_kandang]').text().split('/');

				if(_arrdocin[_tglDocIn] == undefined){
					_arrdocin[_tglDocIn] = [];
					_tgldocindb = Config._convertTgl(Config._tanggalDb(_tglDocIn,' ','-'));
					_dataSimulasi[_tgldocindb] = {'kandang' : [], 'total' : 0};
				}
				_dataSimulasi[_tgldocindb]['kandang'].push(_populasi[1]); // kumpulkan daftar kandang per docin
				_arrdocin[_tglDocIn].push(_populasi[_populasi.length - 1]);
			});
			$('#div_simulasi').html('');
			/* tampilkan rencana kirim */
			_tmp = [];
			for(var i in _arrdocin){
				_totalPopulasi = 0;
				_tmp.push('<div class="alert alert-info col-md-12" onclick="showHideNextElm(this)">Farm '+_namaFarm+' DOC-In '+i+' '+_arrdocin[i].length+' kandang</div>');
				for(var y in _arrdocin[i]){
					_totalPopulasi += parse_number(_arrdocin[i][y],'.',',');
				}
				_tgldocindb = Config._convertTgl(Config._tanggalDb(i,' ','-'));
				_dataSimulasi[_tgldocindb]['total'] = parseInt(_totalPopulasi);
				_rencanaKirim = Forecast.getRencanaKirimBdy(_kodeFarm,_tgldocindb);
				_indexrencana.push(empty(_rencanaKirim) ? 1 : 0);
				var _yy = AktivasiKandang.generateRencanaKirim(_rencanaKirim,_kodeFarm,_tgldocindb,[_totalPopulasi]);
				_tmp.push('<div class="detail_kirim hide">'+_yy+'</div>');
			}

			$('#div_simulasi').html(_tmp.join(''));
			$('#div_simulasi').find('div.div_rencana_kirim').each(function(i){
				AktivasiKandang.setDatepickerTglKirim($(this),_indexrencana[i],function(){
					/* jika ada pergantian tanggal kirim langsung hitung ulang*/
					$('#resumeSimulasiKirim').click();

				});
			});

		}
		else{
			toastr.warning('Kandang Belum Dipilih');
		}
	});
	/* hitung resume pengiriman pakan */
	$('#resumeSimulasiKirim').click(function(){
		var _rencanaKirim = $('#div_simulasi').find('div.div_rencana_kirim table');
		var _resume = [],_tglKirim,_tmpTglKirim,_kodepakan,_tr,_jmlKirim,_namapakan;
		var _totalSimulasi = {'kandang' : [], 'total' : 0};
		if(_rencanaKirim.length){
			_rencanaKirim.each(function(){
				/* set id untuk tabelnya */
				var _idt = $(this).data('sheetname');
				$(this).attr('data-simulasi-kandang',_dataSimulasi[_idt]['kandang'].join(','));
				$(this).attr('data-simulasi-total',number_format(_dataSimulasi[_idt]['total'],0,',','.'));
				_totalSimulasi['kandang'].push(_dataSimulasi[_idt]['kandang'].join(','));
				_totalSimulasi['total'] += parseInt(_dataSimulasi[_idt]['total']);
				$(this).find('tbody>tr').each(function(){
						_tr = $(this);
						_tmpTglKirim = _tr.find('td:first input').val() || _tr.find('td:first').text();
						_jmlKirim =  parse_number(_tr.find('td:last').text(),'.',',');
						_kodepakan = _tr.find('td:eq(2)').text();
						_namapakan = _tr.find('td:eq(3)').text();
						if(!empty(_tmpTglKirim)){
							_tglKirim = _tmpTglKirim;
							if(_resume[_tglKirim] == undefined){
								_resume[_tglKirim] = {};
							}
						}
						if(_resume[_tglKirim][_kodepakan] == undefined){
							_resume[_tglKirim][_kodepakan] = {'jml' : 0, 'namapakan' : _namapakan};
						}
						_resume[_tglKirim][_kodepakan]['jml'] += _jmlKirim;
				});
			});

			/* create tabel dari resume */
			$('#tabelresumeSimulasi').html('');
			var _tabel = [],_tmp = [],_tbody = [],_thead = [],_tmptgl,_tglstr,_tmpTr = [];
			for(var i in _resume){
				for(var j in _resume[i]){
					if(_tmptgl == i){
						_tglstr = '';
					}else{
						_tglstr = i;
					}
					_tmp = {'tgl' : Config._convertTgl(Config._tanggalDb(i,' ','-')), 'data' : [_tglstr,j,_resume[i][j]['namapakan'],Forecast.ceil2(_resume[i][j]['jml'])]};
					_tmpTr.push(_tmp);
				//	_tbody.push('<td>'+_tmp.join('</td><td>')+'</td>');
					_tmptgl = i;
				}
			}
			_tmpTr.sort(function(a,b){
				var x = a.tgl;
				var y = b.tgl;
				return x < y ? -1 : x > y ? 1 : 0;
			})
			for(var i in _tmpTr){
				_tbody.push('<td>'+_tmpTr[i].data.join('</td><td>')+'</td>');
			}
		//	_resume.sort();
			var _namaFarm = $('div[name=divFarm] select>option:selected').text();
			_tbody = '<tbody>'+'<tr>'+_tbody.join('</tr><tr>')+'</tr></tbody>';
			_thead = '<thead><tr><th>Tanggal Kirim</th><th>Kode Pakan</th><th>Nama Pakan</th><th>Kuantitas Simulasi (Sak)</th></tr></thead>';
			_tabel = ['<table data-sheetname="Resume Pengiriman Pakan" class="table table-bordered" id="tabelnyaresumeSimulasi" data-simulasi-kandang="'+_totalSimulasi['kandang'].join(',')+'" data-simulasi-total="'+number_format(_totalSimulasi['total'],0,',','.')+'">'];
			_tabel .push(_thead);
			_tabel .push(_tbody);
			_tabel .push('</table>');
			var _info ='<div class="alert alert-info col-md-12">Simulasi Rencana Pengiriman Pakan <span class="btn btn-primary pull-right" onclick="export_table_to_excel(\'block_simulasi\',\''+_namaFarm+'\')">Export To Excel</span></div>'
			$('#tabelresumeSimulasi').html(_info+_tabel.join(' '));
		}
		else{
			toastr.error('Rencana Kirim Belum Diatur');
		}

	});

	$('#block_simulasi').on('dblclick','tr',function(e){
		$('#tabelresumeSimulasi').html('');
	});
	/*
	$('#block_simulasi').on('click','td>i.glyphicon-ok-circle,td>i.glyphicon-remove-circle',function(e){
		$('#tabelresumeSimulasi').html('');
		alert('i');
	});
*/

}());
