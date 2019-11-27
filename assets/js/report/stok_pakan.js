var StokPakan = {
		list_stok_pakan : function(elm){
			var _form = $(elm).closest('form');
			var _target = _form.next().find('.detail_stok_pakan');
			var _tglTransaksi = Config._tanggalDb(_form.find('input[name=tglTransaksi]').val(),' ','-');
			var _kode_farm = [];
			var _nama_farm = [];
			var _tgl_sekarang = $('#tanggal_server').data('tanggal_server');
			var _list_farm = _form.find('div.list_checkbox :checked');
			var _error = 0;
			if(!_list_farm.length){
				_error++;
				toastr.error('Tidak ada farm yang dipilih');
			}
			else{
				_list_farm.each(function(){
					_kode_farm.push($(this).val());
					_nama_farm.push($(this).closest('label').text());
				});
			}
			if(!_error){
				$.ajax({
					url :'report/report/multi_stok_pakan',
					beforeSend : function(){
						_target.html('Sedang proses, silakan tunggu ....');
					},
					type : 'POST',
					async : false,
					data : {kode_farm : _kode_farm,nama_farm : _nama_farm, tgl_akses : _tgl_sekarang, tgl_transaksi : _tglTransaksi},
					success : function(data){
						_target.html(data);
						_target.find('.number').css('text-align','right');
					},
				}).done(function(){
					if($('div.list_checkbox').is(':visible')){
						$('div.list_checkbox').hide();
					}
					/* tambahkan tooltip pada td yang memiliki class mutasi */
					var _ket = "Pakan yang diterima merupakan pakan hasil mutasi dari kandang";
					_target.find('table.pakan_kavling td.mutasi').each(function(){
						var _span = $(this).find('span');
						var _noref = $(this).data('noref');
						var _td_kandang = _target.find('table.pakan_kandang td.mutasi[data-noref=\''+_noref+'\']');
						$.ajax({
							url : 'report/report/getKandangAsalMutasi',
							data : {noref : _noref},
							async : false,
							dataType : 'json',
							success : function(data){
									_span.append('<span class="tooltip_bdy">'+_ket+' '+data.kandang+'</span>');
									_td_kandang.find('span').append('<span class="tooltip_bdy">'+_ket+' '+data.kandang+'</span>');
							}
						});
					});
				});
			}

		},
		detail_terima_pakan_bdy : function(elm){
			var _tr = $(elm).closest('tr');
			var _table = _tr.closest('table');
			var _tglterima = $(elm).data('tglterima');
			var _kodebarang = $(elm).data('kodebarang');
			var _kodefarm = $(elm).data('kodefarm');
			var _nokavling = $(elm).data('nokavling');
			var _noreg = $(elm).data('noreg') || null;
			var _parent = $(elm).data('parent');
			var _tr_parent = _table.find('td.'+_parent);
			var _terakhir_kavling = _table.find('td.kavling_kandang[data-kode_barang='+_kodebarang+'][data-kavling='+_nokavling+']:last');
			var _tr_detail_terima = _terakhir_kavling.closest('tr').next('tr.detail_terima');

			/* kavling yang posisinya terbuka */
			var kavling = _table.find('tr.breakdown:visible:first').data('kavling');
			var _td_kavling = _table.find('td[data-kavling="'+kavling+'"]');
			var _td_parent_class = _td_kavling.data('parent');
			var _rowspan_asli = _td_kavling.data('rowspan_asli');

			//_table.find('td.'+_td_parent_class).attr('rowspan',_rowspan_asli);
			var _rw_asli;
			_table.find('td[class^=parent]').each(function(){
				_rw_asli = $(this).data('rowspan_asli');
				$(this).attr('rowspan',_rw_asli);
			});

			if(_tr_detail_terima.length){
				if(_tr_detail_terima.is(':hidden')){
					_table.find('tr.breakdown').addClass('hide');
					$('span.plus_sign.glyphicon-minus').not($(elm)).addClass('glyphicon-plus').removeClass('glyphicon-minus');

					_tr_detail_terima.removeClass('hide');
					$(elm).toggleClass('glyphicon-minus');

					/* tambahkan sejumlah 2 rowspannya */
					var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
					_tr_parent.attr('rowspan',parseInt(_rowspan_asli) + 1);
				}
				else{
					_tr_detail_terima.addClass('hide');
					$(elm).toggleClass('glyphicon-minus');

					/* tambahkan sejumlah 2 rowspannya */
					var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
					_tr_parent.attr('rowspan',_rowspan_asli);
				}
			}
			else{
				$.ajax({
					url : 'report/report/detail_terima',
					data : {kode_barang : _kodebarang, tgl_terima : _tglterima, no_kavling : _nokavling, noreg : _noreg, kode_farm : _kodefarm},
					type : 'post',
					dataType : 'json',
					success : function(data){
						if(data.status){
							_table.find('tr.breakdown').addClass('hide');
							var _newTr = '<tr class="detail_terima breakdown">';
							_newTr += '<td></td>';
							_newTr += '<td colspan=9>'+data.content+'</td>';
							_newTr += '<td></td>';
							$(_newTr).insertAfter(_terakhir_kavling.closest('tr'));

							$('span.plus_sign.glyphicon-minus').not($(elm)).addClass('glyphicon-plus').removeClass('glyphicon-minus');
							$(elm).toggleClass('glyphicon-minus');
							var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
							_tr_parent.attr('rowspan',parseInt(_rowspan_asli) + 1);
						}
						else{
							toastr.error(data.message);
						}
					}
				});
			}
		},
		detail_terima_pakan : function(elm){
			var _tr = $(elm).closest('tr');
			var _table = _tr.closest('table');
			var _tglterima = $(elm).data('tglterima');
			var _kodebarang = $(elm).data('kodebarang');
			var _nokavling = $(elm).data('nokavling');
			var _noreg = $(elm).data('noreg') || null;
			var _parent = $(elm).data('parent');
			var _tr_parent = _table.find('td.'+_parent);
			var _tr_detail_terima = _tr.next('tr.detail_terima');

			/* kavling yang posisinya terbuka */
			var kavling = _table.find('tr.breakdown:visible:first').data('kavling');
			var _td_kavling = _table.find('td[data-kavling="'+kavling+'"]');
			var _td_parent_class = _td_kavling.data('parent');
			var _rowspan_asli = _td_kavling.data('rowspan_asli');

			//_table.find('td.'+_td_parent_class).attr('rowspan',_rowspan_asli);
			var _rw_asli;
			_table.find('td[class^=parent]').each(function(){
				_rw_asli = $(this).data('rowspan_asli');
				$(this).attr('rowspan',_rw_asli);
			});

			if(_tr_detail_terima.length){
				if(_tr_detail_terima.is(':hidden')){
					_table.find('tr.breakdown').addClass('hide');
					$('span.plus_sign.glyphicon-minus').not($(elm)).addClass('glyphicon-plus').removeClass('glyphicon-minus');

					_tr_detail_terima.removeClass('hide');
					$(elm).toggleClass('glyphicon-minus');

					/* tambahkan sejumlah 2 rowspannya */
					var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
					_tr_parent.attr('rowspan',parseInt(_rowspan_asli) + 1);
				}
				else{
					_tr_detail_terima.addClass('hide');
					$(elm).toggleClass('glyphicon-minus');

					/* tambahkan sejumlah 2 rowspannya */
					var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
					_tr_parent.attr('rowspan',_rowspan_asli);
				}
			}
			else{
				$.ajax({
					url : 'report/report/detail_terima',
					data : {kode_barang : _kodebarang, tgl_terima : _tglterima, no_kavling : _nokavling, noreg : _noreg},
					type : 'post',
					dataType : 'json',
					success : function(data){
						if(data.status){
							_table.find('tr.breakdown').addClass('hide');
							var _newTr = '<tr class="detail_terima breakdown">';
							_newTr += '<td></td>';
							_newTr += '<td colspan=9>'+data.content+'</td>';
							_newTr += '<td></td>';
							_newTr += '<td></td>';
							$(_newTr).insertAfter(_tr);

							$('span.plus_sign.glyphicon-minus').not($(elm)).addClass('glyphicon-plus').removeClass('glyphicon-minus');
							$(elm).toggleClass('glyphicon-minus');
							var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
							_tr_parent.attr('rowspan',parseInt(_rowspan_asli) + 1);
						}
						else{
							toastr.error(data.message);
						}
					}
				});
			}
		},
		per_jenis_kelamin : function(elm){
			var _tr = $(elm).closest('tr');
			var _table = _tr.closest('table');
			var _parent = $(elm).data('parent');
			var _tr_parent = _table.find('td.'+_parent);

			var _kodebarang = $(elm).data('kodebarang');
			var _nokavling = $(elm).data('kavling');
			var _hiddenElm = _table.find('tr.detail_jenis_kelamin[data-kavling="'+_nokavling+'"][data-kodebarang="'+_kodebarang+'"]');
			/* kavling yang posisinya terbuka */
			var kavling = _table.find('tr.breakdown:visible:first').data('kavling');
			var _td_kavling = _table.find('td[data-kavling="'+kavling+'"]');
			var _td_parent_class = _td_kavling.data('parent');
			var _rowspan_asli = _td_kavling.data('rowspan_asli');
			_table.find('td.'+_td_parent_class).attr('rowspan',_rowspan_asli);

			if(_hiddenElm.is(':hidden')){
				_table.find('tr.breakdown').addClass('hide');
				$('span.plus_sign.glyphicon-minus').not($(elm)).addClass('glyphicon-plus').removeClass('glyphicon-minus');
				_hiddenElm.removeClass('hide');
				$(elm).toggleClass('glyphicon-minus');
				/* tambahkan sejumlah 2 rowspannya */
				var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
				_tr_parent.attr('rowspan',parseInt(_rowspan_asli) + 2);
			}
			else{
				_hiddenElm.addClass('hide');
				$(elm).toggleClass('glyphicon-minus');
				/* kurangi sejumlah 2 rowspannya */
				var _rowspan_asli = _tr_parent.first().data('rowspan_asli');
				_tr_parent.attr('rowspan',_rowspan_asli);
			}


		},

		detail_pakan_kavling_kandang : function(elm){
			var _tr = $(elm).closest('tr');
			var _detail = _tr.next('tr.detail');
			if(_detail.is(':hidden')){
				_detail.show();
			}
			else{
				_detail.hide();
			}
			$(elm).toggleClass('glyphicon-minus');
		},

		show_list_farm : function(elm){

			var _cb = $(elm).next('div.list_checkbox');
			if(_cb.is(':visible')){
				_cb.fadeOut();

			}
			else{
				_cb.fadeIn();

			}
		},

		show_retur_sak : function(elm){
			var _noreg = $(elm).data('noreg');
			var _kandang = $(elm).data('kandang');
			var _tgl_sekarang = $('#tanggal_server').data('tanggal_server');
			var _tgl_transaksi = $(elm).closest('table').data('tgl_transaksi');

			var _options = {
					title : 'Detail Retur Sak Kosong',
					message : ' ',
					className : 'largeWidth',

				};

				bootbox.dialog(_options).bind('shown.bs.modal',function(){

					var _target = $(this).find('div.bootbox-body');
					$.ajax({
						url : 'report/detail_retur_sak',
						type : 'POST',
						beforeSend : function(){
							_target.html('Silakan tunggu ......');
						},
						data : {noreg : _noreg,kandang : _kandang, tgl_akses : _tgl_sekarang, tgl_transaksi : _tgl_transaksi},
						success : function(data){
							_target.html(data);
						},
					});
				});
		},
		rincian_retur : function(elm,jenis){
			var _tgl_transaksi = $(elm).data('tgl_transaksi');
			var _tr = $(elm).closest('tr');
			var _noreg = $(elm).data('noreg');
			var _detail_retur_sak = _tr.next('tr.rinci_retur');
			var _tgl_sekarang = $('#tanggal_server').data('tanggal_server');
			$(elm).toggleClass('glyphicon-minus');
			if(_detail_retur_sak.length){
				if(_detail_retur_sak.is(':hidden')){
					_detail_retur_sak.show();
					_tr.find('td.tmp_td').show();
					_tr.find('td.tmp_td2').hide();
				}
				else{
					_detail_retur_sak.hide();
					_tr.find('td.tmp_td').hide();
					_tr.find('td.tmp_td2').show();
				}
			}
			else{
				var _url = null;
				var _data_tambahan = {};
				switch(jenis){
					case 'rincian' :
						_url = 'report/rinci_retur_sak';
						break;
					case 'pelunasan':
						_url = 'report/pelunasan_retur_sak';
						_tr.find('td.pelunasan').each(function(){
							if(parseInt($(this).text()) > 0){
								_data_tambahan[$(this).data('kode_pakan')] = parseInt($(this).text());
							}
						});

						break;
				}
				$.ajax({
					type : 'POST',
					data : {noreg : _noreg, tgl_akses : _tgl_sekarang, tgl_transaksi : _tgl_transaksi, data_tambahan : _data_tambahan},
					url : _url,
					success : function(data){
						if(data.status){
							var _pk = data.content;
							/* cari list pakan yang ditampilkan */
							var _thead = $(elm).closest('table').find('thead');
							var _arr_pakan = {};
							_thead.find('th.header_pakan').each(function(){
								_arr_pakan[$(this).data('kode_pakan')] = [];
							});

							var _newTr = '<tr class="rinci_retur">';
							var _tot_retur = null;
							_newTr += '<td colspan="2"></td>';
							for(var _z in _pk){
								_newTr += '<td>'+_z+'</td>';
								_newTr += '<td>'+_pk[_z]['jam']+'</td>';
								for(var _kp in _arr_pakan){
									_tot_retur = '-';
									if(_pk[_z]['detail'][_kp] !== undefined){
										if(_pk[_z]['detail'][_kp]['total'] !== undefined){
											_tot_retur = _pk[_z]['detail'][_kp]['total'];
										}
									}
									_newTr += '<td class="number">'+_tot_retur+'</td>';
								}
							}
							$(_newTr).insertAfter(_tr);
							_tr.find('td.tmp_td').show();
							_tr.find('td.tmp_td2').hide();
						}
					},
					dataType : 'json'
				});
			}

		}

};

$(function(){
	'use strict';
	var _tgl_sekarang = $('#tanggal_server').data('tanggal_server');
	$('input[name=tglTransaksi]').datepicker({
		dateFormat : 'dd M yy',
		maxDate : new Date(_tgl_sekarang),
	}).val($('#tanggal_server').text());

	$('#tampilkan_btn').click();
}());
