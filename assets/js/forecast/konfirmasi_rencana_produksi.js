var Konfirmasi_rp = {
	_hari_libur : null,
	_data_tabel_bdy : null,
	_plot_pakan_rp : {}, /* untuk menampung jumlah pakan rencana produksi yang telah diplot di database pada kode rencana produksi tertentu */
	_plot_pakan_lp_awal : {},/* untuk menampung jumlah lolos pakan yang telah diplot di database pada kode rencana produksi tertentu */
	get_hari_libur : function(minDate){
		if(this._hari_libur == null){
			$.ajax({
				data : {minDate : minDate},
				type : 'post',
				url :'permintaan_pakan/permintaan_pakan/get_hari_libur',
				success : function(data){
					if(data.status){
						Konfirmasi_rp._hari_libur = data.content;
					}
				},
				async : false,
				dataType : 'json',
			});
		}
		return this._hari_libur;
	},

	reset : function(){
		this._plot_pakan_rp = {};
		this._plot_pakan_lp = {};
	},

		cari : function(elm,target){
			var _form = $(elm).closest('div.form');
			var _status_realisasi = _form.find('select[name=status_realisasi]').val();
			var _tgl_awal_str = _form.find('input[name=startDate]').val();
			var _tgl_akhir_str = _form.find('input[name=endDate]').val();
			var _tgl_awal = empty(_tgl_awal_str) ? '' : Config._tanggalDb(_tgl_awal_str,' ','-');
			var _tgl_akhir = empty(_tgl_akhir_str) ? '' : Config._tanggalDb(_tgl_akhir_str,' ','-');
			$.ajax({
				type : 'post',
				dataType : 'html',
				data : {realisasi : _status_realisasi, tgl_awal : _tgl_awal, tgl_akhir : _tgl_akhir},
				url : 'forecast/forecast/tabel_konfirmasi_rp',
				beforeSend : function(){
					$(target).html('Silakan tunggu....');
				},
				success: function(data){
					$(target).html(data);
				},
			}).done(function(){
				var _tr, _tgl_ambil, _kodepj,_tgl_kirim;
				var _tglServer = $('#tanggal_server').data('tanggal_server');
				$('input[name=tgl_akhir_rencana_produksi]').each(function(){
					_tr = $(this).closest('tr');
					_tgl_kirim = _tr.find('td.tgl_kirim').text();
					_tgl_ambil = new Date(Config._tanggalDb(_tgl_kirim,' ','-'));
					_tgl_ambil.setDate(_tgl_ambil.getDate() - 1);
					$(this).datepicker({
						dateFormat : 'dd M yy',
						minDate : new Date(_tglServer),
						maxDate : _tgl_ambil,
						yearRange : '+0:+1',
					})
					.change(function(){
						var _new_tr = $(this).closest('tr');
						var _sign_plus = '&nbsp; <span class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.load_rencana_produksi(this)"></span>';
						_new_tr.find('td.koderp').html(_sign_plus);
						$(this).addClass('input_error');
					});

				});
			});

		},
		load_rencana_produksi : function(elm){
			var _new_tr = $(elm).closest('tr');

			$(elm).addClass('input_error');
			_kodepj = _new_tr.find('td.nama_barang').data('kode_barang');
			_new_tr.find('td.koderp').html('');
		//	_tgl_kirim = _new_tr.find('td.tgl_kirim').text();
			var _tgl_kirim = _new_tr.find('td.tgl_kirim').text();
			var _list_rp = Konfirmasi_rp.get_pakanjadi(_kodepj,_tgl_kirim);
			$.when(_list_rp).done(function(){
				if(_list_rp.status){
					// buat list rencana produksi yang bisa dipilih
					var _lr = _list_rp.content.pjs;
					var _table = [], _tmp = '';
					_table.push('<table>');
					_table.push('<tbody><tr><td class="rencana_produksi" style="padding:2px 0px"><select class="input_error">');
					for(var i in _lr){
						_tmp += '<option value="'+_lr[i]['koderencanaproduksi']+'">'+_lr[i]['koderencanaproduksi']+'</option>';
					}
					_table.push(_tmp);
					_table.push('</select></td><td>&nbsp;&nbsp;<span onclick="Konfirmasi_rp.hapus_rencana_produksi(this)" class="glyphicon glyphicon-minus-sign"></span>&nbsp; <span  onclick="Konfirmasi_rp.tambah_rencana_produksi(this)" class="glyphicon glyphicon-plus-sign"></span>');
					_table.push('</td></tr></tbody>');
					_table.push('<tfoot><tr><td colspan=2><div class="checkbox pull-right"><label><input type="checkbox" onclick="Konfirmasi_rp.tandai_berubah(this)"> Selesai</label></div></td></tr></tfoot>');
					_table.push('</table>');

					_new_tr.find('td.koderp').html(_table.join(' '));
				}
				else{
					toastr.error('Serah terima pakan jadi belum ditemukan.');
				}
			});
		},
		reload_rencana_produksi : function(elm){
			var _child_table = $(elm).closest('table');
			var _new_tr = _child_table.closest('tr');

			_kodepj = _new_tr.find('td.nama_barang').data('kode_barang');
			_tgl_kirim = _new_tr.find('td.tgl_kirim').text();
			var _list_rp = Konfirmasi_rp.get_pakanjadi(_kodepj,_tgl_kirim);
			var _koderp_exist = [];
			$.each(_child_table.find('td.rencana_produksi'),function(){
				_koderp_exist.push($(this).html());
			});


			$.when(_list_rp).done(function(){
				if(_list_rp.status){
					/* buat list rencana produksi yang bisa dipilih */
					var _lr = _list_rp.content.pjs;
					var _tr = [], _tmp = '',_jml_option = 0;

					_tr.push('<tr><td class="rencana_produksi" style="padding:2px 0px"><select class="input_error">');
					for(var i in _lr){
						if(!in_array(_lr[i]['koderencanaproduksi'],_koderp_exist)){
							_tmp += '<option value="'+_lr[i]['koderencanaproduksi']+'">'+_lr[i]['koderencanaproduksi']+'</option>';
							_jml_option++;
						}
					}
					_tr.push(_tmp);
					_tr.push('</select></td><td>&nbsp;&nbsp;<span onclick="Konfirmasi_rp.hapus_rencana_produksi(this)" class="glyphicon glyphicon-minus-sign"></span>&nbsp; <span  onclick="Konfirmasi_rp.tambah_rencana_produksi(this)" class="glyphicon glyphicon-plus-sign"></span>');
					_tr.push('</td></tr>');

					if(_jml_option){
						_child_table.find('tbody').append(_tr.join(' '));
						$(elm).hide();
					}
					else{
						toastr.error('Serah terima pakan jadi tidak ditemukan.');
					}

				}
				else{
					toastr.error('Serah terima pakan jadi tidak ditemukan.');
				}
			});
		},
		tandai_berubah : function(elm){
			if($(elm).is(':checked')){
				$(elm).addClass('input_error');
			}
			else{
				$(elm).removeClass('input_error');
			}
			Konfirmasi_rp.hideShowBtnSimpan();
		},
		hapus_rencana_produksi : function(elm){
			var _tbody = $(elm).closest('tbody');
			var _jml_tr = _tbody.find('tr').has('select').length;
			var _new_tr = $(elm).closest('tr');
			var _option_selected = _new_tr.find('select option:selected').text();
			if(_jml_tr == 1){
				var _table = _tbody.closest('table');

				/* jika ada rp yang telah diset sebelumnya maka tampilkan tombol untuk reload_rencana_produksi */
				if(_table.closest('td').hasClass('adarp')){
					_new_tr.remove();
					_tbody.find('span.glyphicon-plus-sign:last').show();
				//	var _plus = '&nbsp; <span class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.reload_rencana_produksi(this)"></span>';
				}
				else{
					var _plus = '&nbsp; <span class="glyphicon glyphicon-plus-sign" onclick="Konfirmasi_rp.load_rencana_produksi(this)"></span>';
					_table.closest('td').html(_plus);
					_table.remove();
				}
			}
			else{
				_new_tr.remove();
				_tbody.find('select option:contains('+_option_selected+')').show();
				_tbody.find('span.glyphicon-plus-sign:last').show();
			}
		},

		tambah_rencana_produksi : function(elm){
			var _tbody = $(elm).closest('tbody');
			var _new_tr = $(elm).closest('tr');

			var _bisa_dipilih = _new_tr.find('select option:visible').length;
			var _error = 0;
			if(_bisa_dipilih == 1){
				_error++;
				toastr.error('Sudah tidak bisa menambah rencana produksi lagi.');
			}
			if(!_error){
				var _clone_tr = _new_tr.clone();
				var _option_selected = _new_tr.find('select option:selected').text();

				/* hide tanda plus */
				_new_tr.find('span.glyphicon-plus-sign').hide();
				/* hide option yang telah dipilih */
				_clone_tr.appendTo(_tbody);
				_tbody.find('tr').not(_new_tr).find('select option:contains('+_option_selected+')').hide();
				_clone_tr.find('select option:visible:first').prop('selected',1);

				if(_bisa_dipilih == 2){
					_tbody.find('tr').not(_clone_tr).find('select option:contains('+_clone_tr.find('option:selected').text()+')').hide();
				}
			}
		},

		get_pakanjadi : function(_kodepj,_tglkirim){
			var tglkirim = Config._tanggalDb(_tglkirim,' ', '-');
			var awaldate = new Date(tglkirim);
			awaldate.setDate(awaldate.getDate() - 7);
			var awal = Config._convertTgl(Config._getDateStr(awaldate));
			var _result = {};
			$.ajax({
				url : 'forecast/forecast/get_pakanjadi',
				type : 'post',
				data : {kodepj : _kodepj, akhir : Config._tanggalDb(_tglkirim,' ','-'), awal : awal },
				dataType : 'json',
				async : false,
				success : function(data){
					_result = data;
				},
				cache : false,
			});
			return _result;
		},
		simpan : function(elm){
			/* periksa apakah ada perubahan dari user */
			var _berubah = $('#tabel_konfirmasi_rencana_produksi .input_error').length;
			if(_berubah){
				var _msg = 'Apakah Anda yakin akan melakukan proses simpan dari inputan yang telah dilakukan ?';
				bootbox.confirm({
					title: 'Konfirmasi Perubahan',
				    message: _msg,
				    buttons: {
				        'cancel': {
				            label: 'Tidak',
				            className: 'btn-default'
				        },
				        'confirm': {
				            label: 'Ya',
				            className: 'btn-danger'
				        }
				    },
				    callback : function(result){
				    	if(result){
							var _no_op,_tr,_kodepj,_tgl_kirim,_tgl_akhir_rencana_produksi,_selesai,_koderp = [],_tr_semua = [],_tmp = {},_kode_konfirmasi;
							$('#tabel_konfirmasi_rencana_produksi table>tbody>tr.header').each(function(){
								_tr = $(this);
								if(_tr.find('.input_error').length){
									_koderp = [];
									_kode_konfirmasi = _tr.data('konfirmasi');
									_no_op = _tr.data('no_op');
									_kodepj = _tr.find('td.nama_barang').data('kode_barang');
									_tgl_kirim = Config._tanggalDb(_tr.find('td.tgl_kirim').text(),' ','-');
									_tgl_akhir_rencana_produksi = empty(_kode_konfirmasi)? Config._tanggalDb(_tr.find('td.tgl_akhir_rencana_produksi input').val(),' ','-') : Config._tanggalDb(_tr.find('td.tgl_akhir_rencana_produksi').html(),' ','-');
									_selesai = _tr.find('td.koderp table>tfoot>tr>td :checkbox').is(':checked') ? 'C':'I';
									_tr.find('td.koderp table>tbody>tr select').each(function(){
										_koderp.push($(this).val());
									});
									_tmp = {no_op : _no_op, kode_pakan : _kodepj, tgl_kirim : _tgl_kirim, tgl_akhir_rencana_produksi : _tgl_akhir_rencana_produksi, realisasi_produksi : _selesai,rencana_produksi : _koderp, kode_konfirmasi : _kode_konfirmasi};
									_tr_semua.push(_tmp);
								}
							});

							$.ajax({
								type : 'post',
								data : {data_konfirmasi : _tr_semua},
								beforeSend : function(){

								},
								url: 'forecast/forecast/simpan_konfirmasi_rp',
								dataType : 'json',
								success : function(data){
									if(data.status){
										toastr.success('Data berhasil disimpan.');
										/* reload daftar konfirmasi pp */
										$('#cari_konfirmasi').click();
									}
								}
							});

						}
				    }
				});
			}
			else{
				toastr.error('Tidak ada data yang disimpan.');
			}
		},
		/* daftar pakan lolos yang sudah diplot didatabase */
		set_plot_pakan_lolos_awal : function(){
			Konfirmasi_rp._plot_pakan_lp_awal = {};
			var _td, _tdRp, _kodepj,_tr,_lolos;
			$('#tabel_konfirmasi_rencana_produksi').find('table>tbody td.tgl_produksi.data_pakan').each(function(){
				_td = $(this);
				_tr = _td.closest('tr');
				_tdRp = _tr.find('td.kode_rencana_produksi').text();
				_kodepj = _td.data('kode_barang');
				_lolos = _tr.find('td.alokasi_lolos_farm input').val() || _tr.find('td.alokasi_lolos_farm').text() || 0;

				if(_tdRp.length == 10){
					if(Konfirmasi_rp._plot_pakan_lp_awal[_tdRp] == undefined){
						Konfirmasi_rp._plot_pakan_lp_awal[_tdRp] = {};
						Konfirmasi_rp._plot_pakan_lp_awal[_tdRp][_kodepj] = 0;
					}
					Konfirmasi_rp._plot_pakan_lp_awal[_tdRp][_kodepj] += parse_number(_lolos,'.',',');
				}
			})
		},
		get_plot_pakan_lolos_awal : function(_rp,_kodepj){
			var _result = 0;
			if(this._plot_pakan_lp_awal[_rp] != undefined){
				if(this._plot_pakan_lp_awal[_rp][_kodepj] != undefined){
						_result = this._plot_pakan_lp_awal[_rp][_kodepj];
				}
			}
			return _result;
		},
		/* method untuk budidaya */
		cari_bdy : function(elm,target){
			this.reset();
			var _form = $(elm).closest('form');
			var _status_realisasi = _form.find('select[name=status_realisasi]').val();
			var _tgl_awal_str = _form.find('input[name=startDate]').val();
			var _tgl_akhir_str = _form.find('input[name=endDate]').val();
			var _kode_pakan = [];
			_form.find('input[name=kode_pakan]:checked').each(function(){
				_kode_pakan.push($(this).val());
			});
			var _error = 0;
			if(empty(_tgl_awal_str)){
				_error++;
				toastr.error('Tanggal kirim awal harus diisi.');
			}
			if(empty(_tgl_akhir_str)){
				_error++;
				toastr.error('Tanggal kirim akhir harus diisi.');
			}

			var _tgl_awal = empty(_tgl_awal_str) ? '' : Config._tanggalDb(_tgl_awal_str,' ','-');
			var _tgl_akhir = empty(_tgl_akhir_str) ? '' : Config._tanggalDb(_tgl_akhir_str,' ','-');
			var _tglServer = $('#tanggal_server').data('tanggal_server');
			if(!_error){
				$.ajax({
					type : 'post',
					dataType : 'html',
					data : {kode_pakan : _kode_pakan, tgl_awal : _tgl_awal, tgl_akhir : _tgl_akhir, tgl_server : _tglServer},
					url : 'forecast/forecast/tabel_konfirmasi_rp_bdy',
					beforeSend : function(){
						$(target).html('Silakan tunggu....');
					},
					success: function(data){
						$(target).html(data);
					},
				}).done(function(){
					var _tr, _tgl_ambil, _kodepj,_tgl_kirim;
					var _tglServer = $('#tanggal_server').data('tanggal_server');
					$('input[name=tglproduksi]').each(function(){
						_tr = $(this).closest('tr');
						_tgl_kirim = _tr.data('tgl_kirim');
						_tgl_ambil = new Date(_tgl_kirim);
						var _minDate = new Date(_tgl_kirim);

						var _hariIniDate = new Date(_tglServer);
						_hariIniDate.setDate(_hariIniDate.getDate() + 2);
						_minDate.setDate(_minDate.getDate() - 7);
						_minDate = _hariIniDate > _minDate ? _hariIniDate : _minDate ;

						_tgl_ambil.setDate(_tgl_ambil.getDate() - 2);
						_tr.data('aksi','estimasi_tanggal_produksi');
						$(this).datepicker({
							beforeShowDay: function(date){ var _t = !Config.is_hari_libur(Config._getDateStr(date), Konfirmasi_rp.get_hari_libur()); var _c = _t ? '' : 'abang'; return [true,_c]; },
						//	onSelect : function(date){ var _t = Config._convertTgl(Config._tanggalDb(date,' ','-')); console.log(_t)},
							dateFormat : 'dd M yy',
							minDate : _minDate ,
							maxDate : _tgl_ambil,
							yearRange : '+0:+1',
						})
						.change(function(){
							$(this).addClass('input_error');
							Konfirmasi_rp.hideShowBtnSimpan();
						});

					});
					$('input.numeric').numeric({
						min : 0
					}).change(function(){
					//	$(this).addClass('input_error');
					});
					if($('span.abang').length > 0){
						toastr.warning('Kebutuhan farm belum terpenuhi.');
					}

					/*kumpulkan data asli dari tabel*/
					var _tmp_data = {};
					var _tgl_kirim, _kode_barang, _jml_keb, _data_pakan,_nama_pakan;
					$(target).find('table tbody tr').each(function(){
						 	_data_pakan = $(this).find('td.nama_pakan');
							_tgl_kirim = _data_pakan.data('tgl_kirim');
							_kode_barang = _data_pakan.data('kode_barang');
							_jml_keb = _data_pakan.next().text();
							_nama_pakan = _data_pakan.html();
							if(_tmp_data[_tgl_kirim] == undefined){
								_tmp_data[_tgl_kirim] = {};
							}
							if(_tmp_data[_tgl_kirim][_kode_barang] == undefined){
								_tmp_data[_tgl_kirim][_kode_barang] = {nama : _nama_pakan, jml_keb : _jml_keb, kode_barang : _kode_barang};
							}
					});
					Konfirmasi_rp.set_plot_pakan_lolos_awal();
					Konfirmasi_rp._data_tabel_bdy = _tmp_data;
					Konfirmasi_rp.hideShowBtnSimpan();
				});
			}

		},
		simpan_bdy : function(elm){
			/* */
			var _table =  $('#tabel_konfirmasi_rencana_produksi table');
			_table.find('tr.estimasi_tglproduksi_baru').each(function(){
					var _tr = $(this);

					_tr.find('input[name=tglproduksi]').each(function(){
						if(empty($(this).val())){
							var _tgl_kirim = $(this).closest('td').data('tgl_kirim');
							var _kode_barang = $(this).closest('td').data('kode_barang');
							var _kirim = _table.find('td.tgl_kirim[data-tgl_kirim=\''+_tgl_kirim+'\']');
							var _pakan = _table.find('td.nama_pakan[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
							var _total_keb = _table.find('td.total_keb[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
							var _total_lolos = _table.find('td.lolos_pakan[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
							var _kirim_rowspan = parseInt(_kirim.attr('rowspan'));
							var _pakan_rowspan = parseInt(_pakan.attr('rowspan'));

							_kirim.attr('rowspan',(_kirim_rowspan - 1)).data('rowspan',(_kirim_rowspan - 1));
							_pakan.attr('rowspan',(_pakan_rowspan - 1)).data('rowspan',(_pakan_rowspan - 1));
							_total_keb.attr('rowspan',(_pakan_rowspan - 1)).data('rowspan',(_pakan_rowspan - 1));
							_total_lolos.attr('rowspan',(_pakan_rowspan - 2) ).data('rowspan',(_pakan_rowspan - 2));

							var _sign_plus = '<span class="glyphicon glyphicon-plus-sign pull-right" onclick="Konfirmasi_rp.addEstimasiRencanaProduksi(this)"></span>';
							$(_sign_plus).appendTo(_tr.prev().find('td.tgl_produksi'));
							_tr.remove();
						}
					});

			});
			/* periksa apakah ada perubahan dari user */
			var _berubah = $('#tabel_konfirmasi_rencana_produksi .input_error').length;
			if(_berubah){
				var _msg = 'Apakah Anda yakin akan melakukan proses simpan dari inputan yang telah dilakukan ?';
				bootbox.confirm({
					title: 'Konfirmasi Perubahan',
				    message: _msg,
				    buttons: {
				        'cancel': {
				            label: 'Tidak',
				            className: 'btn-default'
				        },
				        'confirm': {
				            label: 'Ya',
				            className: 'btn-danger'
				        }
				    },
				    callback : function(result){
				    	if(result){
							var _no_op,_tr,_td,_kodepj,_aksi,_tglkirim,_tr_semua = [],_error = 0;
							var _tidak_boleh_nol = ['alokasi_pakan_untuk_farm'];
							$('#tabel_konfirmasi_rencana_produksi table>tbody>tr').each(function(){
								_tr = $(this);
								if(_tr.find('.input_error').length){
									_aksi = _tr.data('aksi');
									_tglkirim = _tr.data('tgl_kirim');
									_kodepj = _tr.find('td.data_pakan').data('kode_barang');
									_tmp = {aksi : _aksi, kode_pakan : _kodepj, tgl_kirim : _tglkirim};
									_tr.find('.input_error').each(function(){
										if($(this).hasClass('hasDatepicker')){
											_tmp[$(this).attr('name')] = Config._tanggalDb($(this).val(),' ','-');
										}
										else{
											if($(this).hasClass('numeric')){
												if($(this).val() <= 0){
													if(in_array($(this).attr('name'),_tidak_boleh_nol)){
														_error++;
														toastr.error($(this).attr('name')+' harus lebih besar dari nol.');
													}
												}
													_tmp[$(this).attr('name')] = parse_number($(this).val(), '.', ',');
											}
											else{
													_tmp[$(this).attr('name')] = $(this).val();
											}
										}
										if(_aksi == 'rencana_produksi'){
											_tmp['tanggal_produksi'] = Config._tanggalDb($.trim(_tr.find('td.tgl_produksi').text()),' ','-');
											_tmp['rencana_kirim'] = _tr.find('td.tgl_produksi').data('rencana_kirim');
											_tmp['total_produksi'] = _tr.find('td.total_produksi').data('total_produksi');
										}
										if(_aksi == 'kelolosan_pakan'){
											/* pastikan jumlah yang direvisi = jumlah pp fix */
											var _nama_pakan;
											if(_tr.find('td.tgl_produksi').data('fixpp')){
												var _rpplot = _tr.find('td.kode_rencana_produksi').text();
												var _pjplot = _tr.find('td.data_pakan').data('kode_barang');
												var _maxPlot = _tr.find('td.tgl_produksi').data('max-input');
								//			var _totalPlotKirim = Konfirmasi_rp.get_plot_pakan_lolos_aktual(_rpplot,_pjplot);
												var _tmp_alokasi, _sudahdialokasikan = 0, _inputanLolosQC = [];
												if(_nama_pakan == undefined){
													var _nmp = _tr.find('td.nama_pakan');
													_nama_pakan = (_nmp.length) ?_tr.find('td.nama_pakan').html().split('<span')[0] : _tr.find('td.data_pakan').data('nama_pakan');
												}

												_tr.closest('tbody').find('tr[data-tgl_kirim=\''+_tglkirim+'\']').each(function(){
													if($(this).find('td.data_pakan[data-kode_barang=\''+_kodepj+'\']').length > 0 ){
														_tmp_alokasi = parseInt($(this).find('td.alokasi_lolos_farm').text()) || $(this).find('td.alokasi_lolos_farm input').val() || '0';
														_sudahdialokasikan += parse_number(_tmp_alokasi,'.',',');
														_inputanLolosQC.push($(this).find('td.alokasi_lolos_farm input'));
													}

												});
												if(_maxPlot != _sudahdialokasikan){
														for(var i in _inputanLolosQC){
															if(!_inputanLolosQC[i].hasClass('input_error')){
																	_inputanLolosQC[i].addClass('input_error');
															}
															if(_inputanLolosQC[i].hasClass('input_edit')){
																	_inputanLolosQC[i].removeClass('input_edit');
															}
														}
														_error++;
													if(_sudahdialokasikan < _maxPlot){
														toastr.error('Akumulasi plotting Pakan Lolos QC pada tanggal kirim '+Config._tanggalLocal(_tglkirim,'-',' ')+' untuk pakan '+_nama_pakan+' tidak memenuhi kebutuhan farm.');
													}
													else{
														toastr.error('Akumulasi plotting Pakan Lolos QC pada tanggal kirim '+Config._tanggalLocal(_tglkirim,'-',' ')+' untuk pakan '+_nama_pakan+' melebihi kebutuhan farm.');
													}
													return false;
												}

											}
											_tmp['id_hasil_produksi'] = _tr.find('td.tgl_produksi').data('id_hasil_produksi');
										}
										if(_aksi == 'revisi_rencana_produksi'){
											_tmp['rencana_kirim'] = _tr.find('td.tgl_produksi').data('rencana_kirim');
											_tmp['total_produksi'] = _tr.find('td.total_produksi').data('total_produksi');
											_tmp['lolos_pakan'] = _tr.find('td.total_produksi').data('lolospakan');
										}
										if(empty($(this).val())){
											_error++;
											toastr.error($(this).attr('name')+' tidak boleh kosong.');
										}
									});
									_tr_semua.push(_tmp);
								}
								if(_error){
									return false;
								}
							});
							if(!_error){
								$.ajax({
									type : 'post',
									data : {data_konfirmasi : _tr_semua},
									beforeSend : function(){

									},
									url: 'forecast/forecast/simpan_konfirmasi_rp_bdy',
									dataType : 'json',
									success : function(data){
										if(data.status){
											toastr.success('Data berhasil disimpan.');
											/* reload daftar konfirmasi pp */
											$('#cari_konfirmasi_bdy').click();
										}
									}
								});

							}

						}
				    }
				});
			}
			else{
				toastr.error('Tidak ada data yang disimpan.');
			}
		},
		pilih_rencana_produksi : function(elm){
			var _ini = $(elm);
			var _tr = _ini.closest('tr');
			var _td = _ini.closest('td');
			var _rp = _ini.val();
			var _nama_elm = _ini.attr('name');
			if(empty(_rp)){
				_ini.removeClass('input_error');
				_td.next().html('');
				_td.next().next().find('input').removeClass('input_error').val(0);
			}
			else{
				var _jml_produksi = _ini.find('option:selected').data('jml_produksi');
				var _kode_pakan = _ini.find('option:selected').data('kode_barang');
				/* cari yang dalam proses plotting */
				var _sedangPlot = 0, _tmpTr, _nilai;
				_tr.closest('tbody').find('select[name='+_nama_elm+']').not(_ini).each(function(){
					_tmpTr = $(this).closest('tr');
					_nilai = _tmpTr.find('td.alokasi_farm>input').val() || _tmpTr.find('td.alokasi_farm').text();
					_sedangPlot += parse_number(_nilai) || 0;

				});
				/* periksa yang sudah diplot didatabase */
				var _plotDb = this.cari_plot_rp(_rp,_kode_pakan);
				var _belumPlot;
				$.when(_plotDb).done(function(){
					_ini.addClass('input_error');
					_belumPlot = _jml_produksi - (_sedangPlot + parseInt(_plotDb.plotPakan));

					_td.next().html(number_format(_belumPlot,0,',','.'));
					_td.next().data('total_produksi',_jml_produksi);
					_td.next().next().find('input').addClass('input_error');
					_tr.data('aksi','rencana_produksi');
				});
				/* update juga yang lainnya */
				_ini.find('option').not(':selected').each(function(){
						Konfirmasi_rp.update_alokasi_farm($(this).val(),_kode_pakan);
				});
			}
			Konfirmasi_rp.hideShowBtnSimpan();
		},
		cari_plot_rp : function(_rp,_kode_pakan){
			if(Konfirmasi_rp._plot_pakan_rp[_rp] == undefined){
				Konfirmasi_rp._plot_pakan_rp[_rp] = {};
			}
				if(Konfirmasi_rp._plot_pakan_rp[_rp][_kode_pakan] == undefined){
				$.ajax({
					dataType : 'json',
					type : 'post',
					data : {rp : _rp, kode_pakan : _kode_pakan},
					url : 'forecast/plot_pakan_rencana_produksi',
					async : false,
					success : function(data){
						if(data.status){
							Konfirmasi_rp._plot_pakan_rp[_rp][_kode_pakan] = {plotPakan : data.content.plotPakan, plotPakanLolos : data.content.plotPakanLolos };
						}
					}
				});
			}
			return Konfirmasi_rp._plot_pakan_rp[_rp][_kode_pakan];
		},

		periksa_alokasi_farm : function(elm){
			var _ini = $(elm);
			var _tr = _ini.closest('tr');
			var _td = _ini.closest('td');
			var _max_total_alokasi = _tr.find('td.tgl_produksi').data('max-input');
			var _tglkirim = _tr.data('tgl_kirim');

			var _kodepj = _tr.find('td.data_pakan').data('kode_barang');
			var _maxInput = _td.prev().text() == '-' ? 0 : parse_number(_td.prev().text(),'.',',');

			var _nilai = parse_number(_ini.val(),'.',',');
			var _sudahdialokasikan = 0;
			var _tmp_alokasi;
			_tr.closest('tbody').find('tr[data-tgl_kirim=\''+_tglkirim+'\']').not(_tr).each(function(){
				if($(this).find('td.data_pakan[data-kode_barang=\''+_kodepj+'\']').length > 0 ){
					_tmp_alokasi = parseInt($(this).find('td.alokasi_farm').text()) || $(this).find('td.alokasi_farm input').val() || '0';
					_sudahdialokasikan += parse_number(_tmp_alokasi,'.',',');
				}

			});

			if(_sudahdialokasikan <= _max_total_alokasi ){
				var _tmpInput = _max_total_alokasi - _sudahdialokasikan;
				var _defInput = _tmpInput < _maxInput ? _tmpInput : _maxInput;
				var _nilaiTampil = _nilai > _defInput ? _defInput : _nilai;
				_ini.val(_nilaiTampil);
				var _rp_select = _tr.find('td.kode_rencana_produksi>select');
				if(!empty(_rp_select.val())){
					if(!_ini.hasClass('input_error')){
						_ini.addClass('input_error');
					}
					this.update_alokasi_farm(_rp_select.val(),_kodepj);
				}
				else{
					toastr.info('Pilih rencana produksi dulu.');
				}



			}
			else{
				toastr.error('Yang diinput sudah melebihi total kebutuhan farm sebanyak '+(_sudahdialokasikan - _max_total_alokasi)+'.');
			}
			Konfirmasi_rp.hideShowBtnSimpan();
		},
		update_alokasi_farm : function(_rp,_kodepj){
			if(!empty(_rp)){
				var _tdTmp, _berubah,_belumPlotTd, _plotTd, _nilaiBaru, _nilaiAsal, _sedangPlot = 0, _totalProduksi;
				var _plotDb = this.cari_plot_rp(_rp,_kodepj);
				$('select[name=kode_rencana_produksi] option:selected[value='+_rp+'][data-kode_barang='+_kodepj+']').each(function(){
						_tdTmp = $(this).closest('td');
						_belumPlotTd = _tdTmp.next();
						_plotTd = _belumPlotTd.next();
						_totalProduksi = $(this).data('jml_produksi');
						_belumPlotTd.text(number_format(_totalProduksi - (_plotDb.plotPakan + _sedangPlot),0,',','.'));
						_nilaiAsal = parse_number(_plotTd.find('input').val(),'.',',') || 0;
						_sedangPlot += parseInt(_nilaiAsal);
				});
			}
		},
		get_plot_pakan_lolos_aktual : function(_rp,_kodepakan){
			var _td, _tdRp, _kodepj,_tr,_lolos, _total = 0;
			$('#tabel_konfirmasi_rencana_produksi').find('table>tbody td.tgl_produksi.data_pakan').each(function(){
				_td = $(this);
				_tr = _td.closest('tr');
				_tdRp = _tr.find('td.kode_rencana_produksi').text();
				_kodepj = _td.data('kode_barang');
				_lolos = _tr.find('td.alokasi_lolos_farm input').val() || _tr.find('td.alokasi_lolos_farm').text() || 0;
				if(_tdRp == _rp && _kodepj == _kodepakan){
					if(_tdRp.length == 10){
						_total += parse_number(_lolos,'.',',');
					}
				}

			});
			return _total;
		},
		get_plot_pakan_aktual : function(_rp,_kodepakan){
			var _td, _tdRp, _kodepj,_tr,_lolos, _total = 0;
			$('#tabel_konfirmasi_rencana_produksi').find('table>tbody td.tgl_produksi.data_pakan').each(function(){
				_td = $(this);
				_tr = _td.closest('tr');
				_tdRp = _tr.find('td.kode_rencana_produksi').text();
				_kodepj = _td.data('kode_barang');
				_lolos = _tr.find('td.alokasi_farm input').val() || 0;
				if(_tdRp == _rp && _kodepj == _kodepakan){
					if(_tdRp.length == 10){
						_total += parse_number(_lolos,'.',',');
					}
				}

			});
			return _total;
		},
		periksa_alokasi_lolos_farm : function(elm){
			var _ini = $(elm);
			var _tr = _ini.closest('tr');
			var _td = _ini.closest('td');
			var _maxInputPerPlot = _tr.find('td.tgl_produksi').data('max-input') || 0;
			var _maxInput = parse_number(_tr.find('td.alokasi_farm').text(),'.',',');

			var _tglkirim = _tr.data('tgl_kirim');
			var _kodepj = _tr.find('td.data_pakan').data('kode_barang');
			var _rp = _tr.find('td.kode_rencana_produksi').text();
			var _plotDb = this.cari_plot_rp(_rp,_kodepj);
			var _nilaiDb = _ini.data('nilaidb');
			var _nilai = parse_number(_ini.val(),'.',',') || 0;
			_maxInput =  _maxInputPerPlot < _maxInput ?  _maxInputPerPlot : _maxInput;
			if(_ini.data('nilaidb') !== undefined){
				_maxInput =  _ini.data('nilaidb') < _maxInput ?  _ini.data('nilaidb') : _maxInput;
			}

			$.when(_plotDb).done(function(){
//			var _max_total_alokasi = (_td.prev().data('total_lolos_pakan') - _plotDb.plotPakanLolos) + Konfirmasi_rp.get_plot_pakan_lolos_awal(_rp,_kodepj) - Konfirmasi_rp.get_plot_pakan_lolos_aktual(_rp,_kodepj);
				var _max_total_alokasi = (_td.prev().data('total_lolos_pakan') - _plotDb.plotPakanLolos) + Konfirmasi_rp.get_plot_pakan_lolos_awal(_rp,_kodepj) - Konfirmasi_rp.get_plot_pakan_lolos_aktual(_rp,_kodepj);
				var _tmp_alokasi, _sudahdialokasikan = 0;

				_tr.closest('tbody').find('tr[data-tgl_kirim=\''+_tglkirim+'\']').not(_tr).each(function(){
					if($(this).find('td.data_pakan[data-kode_barang=\''+_kodepj+'\']').length > 0 ){
						_tmp_alokasi = $(this).find('td.alokasi_lolos_farm').text() || $(this).find('td.alokasi_lolos_farm input').val() || '0';
						_tmp_alokasi = isNaN(_tmp_alokasi)	? 0 : _tmp_alokasi;
						_sudahdialokasikan += parse_number(_tmp_alokasi,'.',',');
					}
				});

				/* secara total tidak boleh melebihi maxInput */
				if(_sudahdialokasikan < _maxInputPerPlot){
					var _maxSementara = _maxInputPerPlot - _sudahdialokasikan;
					_maxInput = _maxSementara < _maxInput ? _maxSementara : _maxInput ;
				}
				else{
					_maxInput = 0;
				}


				_maxInput = _maxInput == '-' ? 0 : parse_number(_maxInput,',','.');
				_maxInput = _maxInput < _max_total_alokasi ? _maxInput : _max_total_alokasi;

				if(_nilai > _maxInput){
						_ini.val(_maxInput);
				}
				if(_nilai != _nilaiDb){
					if(!_ini.hasClass('input_error')){
						_ini.addClass('input_error');
					}
					if(_ini.hasClass('input_edit')){
						_ini.removeClass('input_edit');
					}
				}
				else{
					if(_ini.hasClass('input_error')){
						_ini.removeClass('input_error');
					}
				}


				_tr.data('aksi','kelolosan_pakan');
				Konfirmasi_rp.hideShowBtnSimpan();

			});
		},
		inputRevisi : function(elm){
			var _ini = $(elm);
			var _tr = _ini.closest('tr');
			var _td = _ini.closest('td');
			var _tglkirim = _tr.data('tgl_kirim');
			var _kodepj = _tr.data('kode_barang');
			var _maxInput = _tr.data('max-input');
			var _sudahPlot = 0, _plot;
			var _tr_pertama;
			_tr.closest('tbody').find('tr[data-tgl_kirim=\''+_tglkirim+'\']').not(_tr).each(function(){
				if($(this).find('td.data_pakan').data('kode_barang') == _kodepj){
					if(_tr_pertama === undefined){
						_tr_pertama = $(this);
					}
					_plot = $(this).find('td.alokasi_lolos_farm>input').val() || $(this).find('td.alokasi_lolos_farm').text() || 0;
					_plot = isNaN(_plot) ? 0 : _plot;
					_sudahPlot += parse_number(_plot,'.',',');
				}
			});

			var _bolehInput = _maxInput - _sudahPlot;
			var bootbox_content ={
					input_str : [

						      	'<div class="row">',
						     			'<div class="col-md-6 text-right">',
						     				'<label class="control-label" for="tglKirim">Tanggal Kirim</label>',
											'</div>',
						     			'<div class="col-md-6">',
							     			'<label class="form-control-label">'+Config._tanggalLocal(_tglkirim,'-',' ')+'</label>',
						     			'</div>',
					     			'</div>',
										'<div class="row">',
						     			'<div class="col-md-6 text-right">',
						     				'<label class="control-label" for="pakan">Pakan</label> ',
											'</div>',
						     			'<div class="col-md-6">',
												'<label name="namapj" class="form-control-label"></label>',
						     			'</div>',
					     			'</div>',
										'<div class="row">',
						     			'<div class="col-md-6  text-right">',
						     				'<label class="control-label" for="kapasitas">Sisa kebutuhan pakan yang belum teralokasi</label> ',
											'</div>',
											'<div class="col-md-6">',
												'<label class="form-control-label" name="sisakebutuhan"></label>',
						     			'</div>',
										'</div>',
						],
					content : function(){
						var _obj = $('<div/>').html(this.input_str.join(''));
						/* buat tabel */
						var _awal = new Date(_tglkirim);
						var _akhir = new Date(_tglkirim);
						_awal.setDate(_awal.getDate() - 7);
						_akhir.setDate(_akhir.getDate() - 2);
						$.ajax({
							url : 'forecast/forecast/get_alokasi_sisa_kebutuhan',
							type : 'post',
							async: false,
							data : {tglawal : Config._convertTgl(Config._getDateStr(_awal)), tglakhir : Config._convertTgl(Config._getDateStr(_akhir)) , kodepj : _kodepj},
							success : function(data){
								_obj.append(data);
								var _berubah, _rp, _plot_rp_berubah;
								_obj.find('table>tbody>tr').each(function(){
									/* update jumlah yang bisa diplot, kemungkinan ada sudah dirubah ( tidak sama dengan di database karena belum disimpan )*/
									_rp = $(this).find('td:eq(1)').text();

								_berubah = Konfirmasi_rp.get_plot_pakan_lolos_awal(_rp,_kodepj) - Konfirmasi_rp.get_plot_pakan_lolos_aktual(_rp,_kodepj);
								_plot_rp_berubah = Konfirmasi_rp.get_plot_pakan_aktual(_rp,_kodepj);

								var _tdKeb = $(this).find('td:eq(3)');
								if(_berubah != 0){
										_tdKeb.attr({
										//	'data-sisa_kebutuhan' : parseInt(_tdKeb.data('sisa_kebutuhan')) + _berubah,
											'data-bisaplotlolospakan' : parseInt(_tdKeb.data('bisaplotlolospakan')) + _berubah
										});
									}
								if(_plot_rp_berubah  != 0){
									_tdKeb.text(number_format(parse_number(_tdKeb.text()) - _plot_rp_berubah,0,',','.'));
								}
									$(this).find('input[name=alokasi_sisa]').numeric({
										min : 1,
										max : parse_number($(this).find('td:eq(3)').text(),'.',','),
									}).change(function(){
										var _total_sementara = 0;
										_obj.find('table>tbody tr.bg_biru input[name=alokasi_sisa][value!=]').each(function(){
											_total_sementara += parse_number($(this).val(),'.',',') || 0;
										});
										_obj.find('input[name=total_plot_tambahan]').val(_total_sementara);
									});
									$(this).click(function(e){
									var _target = e.target;
									if($(_target).prop('tagName') != 'INPUT'){
										var _pilih = $(this).hasClass('bg_biru');
										if(_pilih){
											$(this).removeClass('bg_biru').css("background-color", "");
										}
										else{
											$(this).addClass('bg_biru').css("background-color", "#2ae3d8");
										}
										var _total_sementara = 0;
										_obj.find('table>tbody tr.bg_biru input[name=alokasi_sisa][value!=]').each(function(){
											_total_sementara += parse_number($(this).val(),'.',',') || 0;
										});
										_obj.find('input[name=total_plot_tambahan]').val(_total_sementara);
									}
								}).hover(function(){
										if($(this).hasClass('bg_biru')){
												$(this).css("background-color", "#2ae3d8");
										}
										else{
											$(this).css("background-color", "");
										}
								},function(){
									if($(this).hasClass('bg_biru')){
											$(this).css("background-color", "#2ae3d8");
									}
									else{
										$(this).css("background-color", "");
									}
								});
								/* jika bisaplotlolospakan <= 0 dan lolospakan != 0, maka gak usah ditampilkan hapus saja */
								if(!empty(_tdKeb.data('lolospakan')) &&  _tdKeb.data('lolospakan') > 0){
									if(_tdKeb.data('bisaplotlolospakan') <= 0 ){
										$(this).remove();
									}
								}
						});
							/* update jumlah kebutuhan yang belum teralokasi */
							_obj.find('label[name=sisakebutuhan]').text(number_format(_bolehInput,0,',','.')+' Sak');
							_obj.find('label[name=namapj]').text(_tr_pertama.find('td.nama_pakan').html().split('<span')[0]);
							},
						});

						return _obj;
					}
				};
				var _options = {
					title : 'ALOKASI SISA KEBUTUHAN PAKAN UNTUK FARM',
					message : bootbox_content.content(),
					className : 'largeWidth',
					buttons : {
						set : {
							label : 'OK',
							callback : function(e){
								var _form = $(e.target).closest('.modal-content').find('.modal-body .bootbox-body');
								var jml_produksi = 0;
								var rencana_produksi = _form.find('select[name=revisi_rencana_produksi]').val();
								var tanggal_produksi = _form.find('input[name=revisi_tanggal_produksi]').val();
								var _tmpPlot, _error = 0, _totalPlot = 0;
								var _tr_terpilih = _form.find('table>tbody tr.bg_biru');
								if(_tr_terpilih.length == 0){
									_error++;
									toastr.error('Tidak ada yang diplot, pilih baris terlebih dahulu.');
								}
								if(!_error){
									var _new_tr = [], _new_td;
									_tr_terpilih.each(function(){
											_tmpPlot = $.trim($(this).find('td:last>input[name=alokasi_sisa]').val());
											if(empty(_tmpPlot)){
													_error++;
													toastr.error('Jumlah yang diplot harus lebih besar dari 0.');
											}
											_totalPlot += parseInt(parse_number(_tmpPlot));
											var _rencana_produksi = $(this).find('td:eq(1)').text();
											var _tanggal_produksi = $(this).find('td:eq(0)').text();
											var _tmpBelumPlot = $(this).find('td:eq(3)');
											var _tmpAkanPlot = $(this).find('td:eq(4)>input').val();
											var _rencanakirim = _tr_pertama.find('td.tgl_produksi').data('rencana_kirim');
											var _sisa_kebutuhan = _tmpBelumPlot.data('sisa_kebutuhan') || 0;
											var _total_produksi = _tmpBelumPlot.data('jmlproduksi') || 0;
											var _bebas_plot = _tmpBelumPlot.data('plot_bebas');
											var _akanPlotNilai = parse_number(_tmpAkanPlot);
											var _lolosPakan = _tmpBelumPlot.data('lolospakan') || 0;
											var _bisaPlotLolosPakan = _tmpBelumPlot.attr('data-bisaplotlolospakan') || 0;
											var _plotSekarang;
											var _inputAlokasiUntukFarm = '-', _tmpTotalProduksi, _tmpPlot;
											/* jika sisa_kebutuhan < 0, maka jadikan 0 saja */
										//	_sisa_kebutuhan = _sisa_kebutuhan < 0 ? 0 : _sisa_kebutuhan;
											_tmpTotalProduksi = _total_produksi;
											_tmpPlot = _bisaPlotLolosPakan;
											if(_sisa_kebutuhan > 0){
												var _tmp_tr = _tr.clone();
												_tmp_tr.data('aksi','revisi_rencana_produksi');

												if(_akanPlotNilai > _sisa_kebutuhan){
														_plotSekarang = _sisa_kebutuhan;
														_akanPlotNilai = _akanPlotNilai - _sisa_kebutuhan;
												}
												else{
											//		_plotSekarang = _akanPlotNilai;
													_plotSekarang = _akanPlotNilai > _tmpPlot ? _tmpPlot : _akanPlotNilai;
													_akanPlotNilai = 0;
												}

												_new_td = [
																	 '<td data-kode_barang="'+_kodepj+'" data-max-input="'+_maxInput+'" data-rencana_kirim="'+_rencanakirim+'" class="tgl_produksi data_pakan"><input class="col-md-12 input_error hasDatepicker" type="text" name="tanggal_produksi" value="'+_tanggal_produksi+'" readonly/></td>',
																	 '<td><input class="input_error" type="text" name="kode_rencana_produksi" value="'+_rencana_produksi+'" readonly/></td>',
																	 '<td class="number hasil_produksi">'+number_format(_bebas_plot,0,',','.')+'</td>',
																	 '<td class="number alokasi_farm">-</td>',
																	 '<td class="number total_produksi" data-lolospakan="'+_lolosPakan+'" data-total_produksi="'+parse_number(_total_produksi)+'">'+number_format(_tmpPlot,0,',','.')+'</td>',
																	 '<td class="number alokasi_lolos_farm"><input class="col-md-12 input_error" type="text" name="alokasi_pakan_lolos_untuk_farm" value="'+_plotSekarang+'" readonly/></td>'
															 ];
												_tmp_tr.children().remove();
												_tmp_tr.append(_new_td.join(''));
												_new_tr.push(_tmp_tr);

												_tmpPlot = _tmpPlot - _plotSekarang;
											}
											if(_akanPlotNilai > 0){
												_akanPlotNilai = _akanPlotNilai > _tmpPlot ? _tmpPlot : _akanPlotNilai;
												var _tmp_tr = _tr.clone();
												_tmp_tr.data('aksi','revisi_rencana_produksi');
												if(_lolosPakan > 0){
													_inputAlokasiUntukFarm = '<input class="col-md-12 input_error" type="text" name="alokasi_pakan_lolos_untuk_farm" value="'+_akanPlotNilai+'" readonly/>';
												}
												_new_td = [
																	 '<td data-kode_barang="'+_kodepj+'" data-max-input="'+_maxInput+'" data-rencana_kirim="'+_rencanakirim+'" class="tgl_produksi data_pakan"><input class="col-md-12 input_error hasDatepicker" type="text" name="tanggal_produksi" value="'+_tanggal_produksi+'" readonly/></td>',
																	 '<td><input class="input_error" type="text" name="kode_rencana_produksi" value="'+_rencana_produksi+'" readonly/></td>',
																	 '<td class="number hasil_produksi">'+number_format(_bebas_plot,0,',','.')+'</td>',
																	 '<td class="number alokasi_farm"><input class="col-md-12 input_error" type="text" name="alokasi_pakan_untuk_farm" value="'+_akanPlotNilai+'" readonly/></td>',
																	 '<td class="number total_produksi" data-lolospakan="'+_lolosPakan+'" data-total_produksi="'+parse_number(_total_produksi)+'">'+number_format(_tmpPlot,0,',','.')+'</td>',
																	 '<td class="number alokasi_lolos_farm">'+_inputAlokasiUntukFarm+'</td>'
															 ];
												_tmp_tr.children().remove();
												_tmp_tr.append(_new_td.join(''));
												_new_tr.push(_tmp_tr);
											}

									});
								}

								if(empty(_totalPlot) || _totalPlot <= 0 ){
									_error++;
									toastr.error('Jumlah yang diplot harus lebih besar dari 0.');
								}
								if(_totalPlot > _bolehInput){
									_error++;
									toastr.error('Jumlah yang diplot tidak boleh lebih besar dari maksimal alokasi '+ _bolehInput+'.');
								}

								if(!_error){
									for(var i in _new_tr){
										_new_tr[i].insertBefore(_tr);
									}
									/* update rowspannya*/
									var _tambahrs = parseInt(_new_tr.length) - 1;
									var _rs_pakan = parseInt(_tr_pertama.find('td.nama_pakan').attr('rowspan'));
									var _rs_tglkirim = parseInt(_tr_pertama.find('td:first').attr('rowspan'));
									_tr_pertama.find('td.nama_pakan,td.total_keb,td.total_pp,td:last').attr('rowspan',_rs_pakan + _tambahrs);
									_tr_pertama.find('td:first').attr('rowspan',_rs_tglkirim + _tambahrs);

									_tr.remove();
									Konfirmasi_rp.hideShowBtnSimpan();
								}else{
									return false;
								}
							}
						}
					},
				};

				bootbox.dialog(_options);
		},
		removeTgl : function(elm){
			var _td = $(elm).closest('td');
			_td.find('input').val('').removeClass('input_error');
		},
		filterInput : function(elm,parent){
			var par = $(parent);
			var _tot = par.find(':checkbox').length;
			var _pilih = [];
			par.find(':checked').each(function(){
				_pilih.push($(this).val());
			});
			/* hidden semuanya dulu */
		 $('td.temp').remove();
		 $('#tabel_konfirmasi_rencana_produksi').find('td[data-inputfilter]').closest('tr').hide();
			if(_pilih.length < _tot && _pilih.length != 0){
				for(var i in _pilih){
					var _tmp = $('#tabel_konfirmasi_rencana_produksi').find('td[data-inputfilter~='+_pilih[i]+']');
					_tmp.closest('tr').show();
				}
				/* perbaiki rowspan pd yang visible */
				var _tr;
				$('#tabel_konfirmasi_rencana_produksi').find('td.data_pakan:visible').each(function(){
					_tr = $(this).closest('tr');
					var pj = _tr.find('td.data_pakan').data('kode_barang');
					var kr = _tr.data('tgl_kirim');
					var _rs_kirim = $('#tabel_konfirmasi_rencana_produksi tr:visible[data-tgl_kirim=\''+kr+'\']').length;
					var _rs_pakan_elm = $('#tabel_konfirmasi_rencana_produksi td.data_pakan:visible[data-tgl_kirim=\''+kr+'\'][data-kode_barang=\''+pj+'\']');
					var _rs_pakan = 1;

					_rs_pakan_elm.each(function(){
						if(!empty($.trim($(this).text()))){
							_rs_pakan++;
						}
					});



					/* cek apakah tgl_kirim sudah ada */
					var _tgl_kirim = $('#tabel_konfirmasi_rencana_produksi td.tgl_kirim:visible[data-tgl_kirim=\''+kr+'\']');
					var _nama_pakan = $('#tabel_konfirmasi_rencana_produksi td.nama_pakan:visible[data-tgl_kirim=\''+kr+'\'][data-kode_barang=\''+pj+'\']');

					if(_nama_pakan.length){
							_nama_pakan.attr('rowspan',_rs_pakan);
							_nama_pakan.next().attr('rowspan',_rs_pakan);
							_nama_pakan.next().next().attr('rowspan',_rs_pakan);
							var _rs_lolos = _rs_pakan > 1 ? (_rs_pakan - 1) : _rs_pakan;
							_nama_pakan.closest('tr').find('td.lolos_pakan').attr('rowspan',_rs_lolos);
					}
					else{
						_nama_pakan = $('<td class="temp has-tooltip_bdy nama_pakan"  data-tgl_kirim="'+kr+'" data-kode_barang="'+pj+'">'+Konfirmasi_rp._data_tabel_bdy[kr][pj]['nama']+'</td><td class="temp number total_keb"  data-tgl_kirim="'+kr+'" data-kode_barang="'+pj+'">'+Konfirmasi_rp._data_tabel_bdy[kr][pj]['jml_keb']+'</td>');
						_nama_pakan.attr('rowspan',_rs_pakan);
						_nama_pakan.prependTo(_tr);
						var _rs_lolos = _rs_pakan > 1 ? (_rs_pakan - 1) : _rs_pakan;
						var _jml_lolos_pakan = 0;
						$('#tabel_konfirmasi_rencana_produksi td.nama_pakan:visible[data-tgl_kirim=\''+kr+'\'][data-kode_barang=\''+pj+'\']').each(function(){
							var _y = $(this).closest('tr').find('td.alokasi_lolos_farm');
							var _j = _y.text() || _y.find('input').val() || '0';
							_jml_lolos_pakan  += _j == '-' ? parse_number('0', '.', ',') : parse_number(_j, '.', ',');
						});
						$('<td class="temp number lolos_pakan" rowspan="'+_rs_lolos+'" data-tgl_kirim="'+kr+'" data-kode_barang="'+pj+'">'+_jml_lolos_pakan+'</td>').appendTo(_tr);
					}

					if(_tgl_kirim.length){
							_tgl_kirim.attr('rowspan',_rs_kirim);
					}
					else{
						_tgl_kirim = $('<td class="temp tgl_kirim" data-tgl_kirim="'+kr+'">'+Config._tanggalLocal(kr,'-',' ')+'</td>');
						_tgl_kirim.attr('rowspan',_rs_kirim);
						_tgl_kirim.prependTo(_tr);
					}

				});

			}
			else{
				/* tampilkan semua */
				$('#tabel_konfirmasi_rencana_produksi').find('td[data-inputfilter]').closest('tr').show();
				/* perbaiki rowspan pd yang visible */
				$('#tabel_konfirmasi_rencana_produksi td[rowspan]').each(function(){
					$(this).attr('rowspan',$(this).data('rowspan'));
				});
			}
		},
		addEstimasiRencanaProduksi : function(elm){
			var _ini = $(elm);
			if(!_ini.hasClass('disabled')){
				var _td = _ini.closest('td');
				var _table = _td.closest('table');
				var _tr = _td.closest('tr');
				var _td_new = _td.clone();
				var _tgl_kirim = _td.data('tgl_kirim');
				var _kode_barang = _td.data('kode_barang');
				var _anyar = $('<tr class="estimasi_tglproduksi_baru" data-tgl_kirim="'+_tgl_kirim+'"></tr>');
				_td_new.html('<input class="col-md-10" type="text" name="tglproduksi" readonly/>&nbsp;<i onclick="Konfirmasi_rp.removeTgl(this)" class="glyphicon glyphicon-remove-circle pull-right"></i>');
				_td_new.attr('data-rencana_kirim','');
				_anyar.prepend(_td_new);

				var _kirim = _table.find('td.tgl_kirim[data-tgl_kirim=\''+_tgl_kirim+'\']');
				var _pakan = _table.find('td.nama_pakan[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
				var _total_keb = _table.find('td.total_keb[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
				var _total_pp = _table.find('td.total_pp[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
				var _total_lolos = _table.find('td.lolos_pakan[data-tgl_kirim=\''+_tgl_kirim+'\'][data-kode_barang=\''+_kode_barang+'\']');
				var _kirim_rowspan = parseInt(_kirim.attr('rowspan'));
				var _pakan_rowspan = parseInt(_pakan.attr('rowspan'));

				var _tgl_ambil = new Date(_tgl_kirim);
				var _minDate = new Date(_tgl_kirim);
				var _hariIniDate = new Date(Config._tglServer);
				_hariIniDate.setDate(_hariIniDate.getDate() + 2);
				_minDate.setDate(_minDate.getDate() - 7);
				_minDate = _hariIniDate > _minDate ? _hariIniDate : _minDate ;

				_tgl_ambil.setDate(_tgl_ambil.getDate() - 2);
				if(_minDate <= _tgl_ambil){
					_td.nextUntil('td.lolos_pakan').clone().appendTo(_anyar);
					_anyar.insertAfter(_td.closest('tr'));
					_anyar.data('aksi','estimasi_tanggal_produksi');

					_kirim.attr('rowspan',(_kirim_rowspan + 1)).data('rowspan',(_kirim_rowspan + 1));
					_pakan.attr('rowspan',(_pakan_rowspan + 1)).data('rowspan',(_pakan_rowspan + 1));
					_total_keb.attr('rowspan',(_pakan_rowspan + 1)).data('rowspan',(_pakan_rowspan + 1));
					_total_pp.attr('rowspan',(_pakan_rowspan + 1)).data('rowspan',(_pakan_rowspan + 1));
					_total_lolos.attr('rowspan',_pakan_rowspan ).data('rowspan',_pakan_rowspan );

					var _blockDate = [];

					_anyar.siblings('[data-tgl_kirim=\''+_tgl_kirim+'\']').each(function(){
						if($(this).find('td.data_pakan[data-kode_barang=\''+_kode_barang+'\']').length > 0){
							_blockDate.push($.trim($(this).find('td.tgl_produksi').text()) || $(this).find('td.tgl_produksi input').val());
						}
					});

					_td_new.find('input[name=tglproduksi]').datepicker({
						beforeShowDay: function(date){ var _t = !Config.is_hari_libur(Config._getDateStr(date), Konfirmasi_rp.get_hari_libur()); var _c = _t ? '' : 'abang'; var _s = (!in_array(Config._tanggalLocal(Config._getDateStr(date),'-',' '),_blockDate)) ? true : false; return [_s,_c]; },
						dateFormat : 'dd M yy',
						minDate : _minDate ,
						maxDate : _tgl_ambil,
						yearRange : '+0:+1',
					})
					.change(function(){
						$(this).addClass('input_error');
						Konfirmasi_rp.hideShowBtnSimpan();
					});
				}
				else{
					/* aktifkan tambahRP */
					_tr.next().find('td:first>span.btn').removeClass('disabled');
					toastr.info('Tidak ada Estimasi Tanggal Produksi yang dapat dipilih, gunakan Tambah RP.');
				}
				_ini.remove();
			}
			else{
				toastr.error('Tidak bisa menambah estimasi tanggal produksi karena rencana produksi sudah ada.');
			}
		},
		hideShowBtnSimpan : function(){
			var _berubah = $('#tabel_konfirmasi_rencana_produksi .input_error').length;
			var _elm = $('#btnSimpan');
			if(!_berubah){
				/* hidden tombol simpan */
				if(_elm.is(':visible')){
					_elm.hide();
				}
			}
			else{
				/* hidden tombol simpan */
				if(_elm.is(':hidden')){
					_elm.show();
				}
			}

		},
		riwayatAlokasiPakan : function(elm,tglkirim,pakan){
			var _ini = $(elm);
			var _tr = _ini.closest('tr');
			var _nama_pakan = _tr.find('td.nama_pakan').html().split('<span')[0];
			var _forecast = _tr.find('td.total_keb').text();
			var _pp = _tr.find('td.total_pp').text();

			var bootbox_content ={
					input_str : [
										'<div class="row">',
											'<div class="col-md-6 text-right">',
												'<label class="control-label" for="tglkirim">Tgl Kirim</label> ',
											'</div>',
											'<div class="col-md-6">',
												'<label name="tglkirim" class="form-control-label">'+Config._tanggalLocal(tglkirim,'-',' ')+'</label>',
											'</div>',
										'</div>',
						      	'<div class="row">',
						     			'<div class="col-md-6 text-right">',
						     				'<label class="control-label" for="pakan">Pakan</label>',
											'</div>',
						     			'<div class="col-md-6">',
							     			'<label class="form-control-label">'+_nama_pakan+'</label>',
						     			'</div>',
					     			'</div>',
										'<div class="row">',
						     			'<div class="col-md-6 text-right">',
						     				'<label class="control-label" for="total_keb">Total Kebutuhan Farm (Forecast)</label>',
											'</div>',
						     			'<div class="col-md-6">',
							     			'<label class="form-control-label">'+_forecast+' (sak)</label>',
						     			'</div>',
					     			'</div>',
										'<div class="row">',
						     			'<div class="col-md-6 text-right">',
						     				'<label class="control-label" for="total_pp">Total Kebutuhan Farm (PP)</label>',
											'</div>',
						     			'<div class="col-md-6">',
							     			'<label class="form-control-label">'+_pp+' (sak)</label>',
						     			'</div>',
					     			'</div>'

									],
					content : function(){
						var _obj = $('<div/>').html(this.input_str.join(''));
						/* buat tabel */

						$.ajax({
							url : 'forecast/forecast/detail_riwayat_alokasi_lolos_pakan',
							type : 'post',
							async: false,
							data : {tglkirim : tglkirim, pakan : pakan},
							success : function(data){
								_obj.append(data);
								_obj.find('tr.detail_sisa_lolos_pakan').hide();
							},
						});

						return _obj;
					}
				};
				var _options = {
					title : 'RIWAYAT ALOKASI PAKAN',
					message : bootbox_content.content(),
					className : 'largeWidth',
				};

				bootbox.dialog(_options);
		},
		detailSisaPakanLolos : function(elm,rp,pakan){
			var _ini = $(elm);
			var _td = _ini.closest('td');
			var _tr = _td.closest('tr');
			/* sudah ada atau belum detailnya */
			var _detail = _tr.next('tr.detail_sisa_lolos_pakan');
			var _dt = _detail.find('td:last');
			if(empty(_dt.html())){
				$.ajax({
					type : 'post',
					url : 'forecast/forecast/detail_sisa_lolos_pakan',
					data : {rencana_produksi : rp, pakan : pakan},
					async : false,
					success : function(data){
						_dt.html(data);
						_detail.show();
					},
				});
			}
			else{
				if(_detail.is(':hidden')){
					_detail.show();
				}
				else{
					_detail.hide();
				}
			}
			_ini.toggleClass('glyphicon-minus-sign');
		},
		detail_rencana_produksi : function(elm){
			var _rp = $(elm).text();
			var _tglProduksi = $(elm).closest('td').prev().text();
			var bootbox_content ={
					input_str : [
										'<div class="row">',
											'<div class="col-md-6 text-right">',
												'<label class="control-label" for="koderp">Kode RP</label> ',
											'</div>',
											'<div class="col-md-6">',
												'<label name="namapj" class="form-control-label">'+_rp+'</label>',
											'</div>',
										'</div>',
						      	'<div class="row">',
						     			'<div class="col-md-6 text-right">',
						     				'<label class="control-label" for="tglProduksi">Tanggal Produksi</label>',
											'</div>',
						     			'<div class="col-md-6">',
							     			'<label class="form-control-label">'+_tglProduksi+'</label>',
						     			'</div>',
					     			'</div>'

									],
					content : function(){
						var _obj = $('<div/>').html(this.input_str.join(''));
						/* buat tabel */

						$.ajax({
							url : 'forecast/forecast/detail_rencana_produksi',
							type : 'post',
							async: false,
							data : {rencana_produksi : _rp},
							success : function(data){
								_obj.append(data);
								_obj.find('tr.detail_alokasi_lolos_pakan').hide();
							},
						});

						return _obj;
					}
				};
				var _options = {
					title : 'DETAIL ALOKASI RENCANA PRODUKSI',
					message : bootbox_content.content(),
				//	className : 'largeWidth',
				};

				bootbox.dialog(_options);
		},
		detailAlokasiPakanLolosQC : function(elm,rp,pakan){
			var _ini = $(elm);
			var _td = _ini.closest('td');
			var _tr = _td.closest('tr');
			/* sudah ada atau belum detailnya */
			var _detail = _tr.next('tr.detail_alokasi_lolos_pakan');
			var _dt = _detail.find('td');
			if(empty(_dt.html())){
				$.ajax({
					type : 'post',
					url : 'forecast/forecast/detail_alokasi_lolos_pakan',
					data : {rencana_produksi : rp, pakan : pakan},
					async : false,
					success : function(data){

						_dt.html(data);
						_detail.show();
					},
				});
			}
			else{
				if(_detail.is(':hidden')){
					_detail.show();
				}
				else{
					_detail.hide();
				}
			}
			_ini.toggleClass('glyphicon-minus-sign');
		}
};
$(function(){
	'use strict';
	/* set tanggal server */
	var _tglServer = $('#tanggal_server').data('tanggal_server');
	Config._setTglServer(_tglServer);

	if($("input[name=startDate]").length){
		$("input[name=startDate]").datepicker({
		    //  defaultDate: "+1w",
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		    	var _max = new Date(Config._convertTgl(Config._tanggalDb(selectedDate,' ','-')));
		    	_max.setDate(_max.getDate() + 13);
		        $( "input[name=endDate]" ).datepicker( "option", "minDate", selectedDate ).datepicker( "option", "maxDate", _max );
		      }
		   });
	}
	if($("input[name=endDate]").length){
		 $("input[name=endDate]").datepicker({
		    //  defaultDate: "+1w",
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		    	var _min = new Date(Config._convertTgl(Config._tanggalDb(selectedDate,' ','-')));
			    _min.setDate(_min.getDate() - 13);
		        $( "input[name=startDate]" ).datepicker( "option", "maxDate", selectedDate ).datepicker( "option", "minDate", _min );
		    }
		  });
	}

	$(':checkbox').click(function(){
		if($(this).is(':checked')){
			$(this).closest('div').removeClass('remeng');
		}
		else{
			$(this).closest('div').addClass('remeng');
		}
	});
	$(':checkbox').not(':checked').closest('div').addClass('remeng');
	$('#cari_konfirmasi').click();
	$('#btnSimpan').hide();
}());
