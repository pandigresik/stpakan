$(function(){
	'use strict';
	var tanggal_sistem = $('#tanggal_server').data('tanggal_server');
	var _standart_umur_pakan = Permintaan._standart_umur_pakan;

	$('#link_tambah_pengiriman').click(function(e){
	//	var _tgl_pp = $('#transaksi input[name=tgl_permintaan]').val();
		var _tr_last = $('#tabel_pp tbody tr:last');
		var _template = _tr_last.clone();

		var _error = 0;
		var _message = [];
		var _tglKirimAwal = $('#tabel_pp tbody tr:first input[name=tgl_kirim]').val();
		var _kebutuhan_akhir = _template.find('input[name=tgl_keb_akhir]').val();

		if(empty(_kebutuhan_akhir)){
			_error++;
			_message.push('Kebutuhan akhir belum diisi');
		}


		if(!_error){
			Permintaan.get_tgl_kirim_selanjutnya(_template,_tglKirimAwal);
		}
		else{
			for(var i in _message){
				toastr.error(_message[i]);
			}

		}

		e.stopPropagation();
	});

	/* hapus pengiriman */
	$('#link_hapus_pengiriman').click(function(e){
		var _tr = $('#tabel_pp tbody tr');
		if(_tr.length > 1){
			/* hapus data pengiriman terakhir */
			var _index_tr = _tr.last().index();
			var _tr_last = _tr.last();
			var _ada_di_db = _tr_last.find('span.pilih_btn').hasClass('exist') ? 1 : 0;
			if(_ada_di_db){
				var _tgl_kirim = Config._convertTgl(Config._tanggalDb(_tr_last.find('input[name=tgl_kirim]').val(),' ','-'));
				Permintaan.add_hapus_pengiriman(_tgl_kirim);
			}
			_tr_last.remove();
			var _keb_pakan = $('div#kebutuhan_pakan_internal div#tabel'+_index_tr);
			var _sisa_pakan = $('div#sisa_konsumsi_pakan div#sisa_pakan'+_index_tr);
			if(_keb_pakan.length > 0){
				_keb_pakan.remove();
			}
			if(_sisa_pakan.length > 0){
				_sisa_pakan.remove();
			}
			/* aktifkan kembali datepicker baris terakhir */
			$('#tabel_pp tbody tr:last').find('input.hasDatepicker').datepicker('enable');
		}
		else{
			toastr.warning('Tidak bisa menghapus data pengiriman');
		}
		e.stopPropagation();
	});

	/* isi nilai kebutuhan awal, hanya jika pp baru saja */
	var _nopp = $('#transaksi input[name=no_pp]').val();
	if(empty(_nopp)){
		$('#transaksi input[name=tgl_permintaan]').val(Config._tanggalLocal(Config._getDateStr(new Date(tanggal_sistem)),'-',' '));
		/* periksa apakah lhk sudah diinput semua */
		var r = Permintaan.cek_input_lhk();
		$.when(r).done(function(){
			if(r.status){
				Permintaan.get_kebutuhan_awal(tanggal_sistem);
			}
			else{
				toastr.error(' Lhk untuk kandang <br />' + r.message.join(' <br /> ')+' <br /> belum diinput');
			}
		});

	}
	else{
		/* jika masih draft berarti masih bisa disimpan lagi, sehingga datepicker perlu diaktifkan pada baris terakhir */
		var _adaTombolSimpan = $('#div_tombol_simpan div[data-aksi=simpan]').length;
		if(_adaTombolSimpan){
			var _tglKirimAwal = $('div#tabel_pp table>tbody>tr:first').find('input[name=tgl_kirim]').val();
			var _refKebutuhanAkhir;
			var _tr_tmp = $('div#tabel_pp table>tbody>tr');
			var _last_index = _tr_tmp.length - 1;
			_tr_tmp.each(function(index){
				var _refKebutuhanAkhirDate = new Date(Config._convertTgl(Config._tanggalDb($(this).find('input[name=tgl_keb_awal]').val(),' ','-')));
				_refKebutuhanAkhirDate.setDate(_refKebutuhanAkhirDate.getDate() - 1);
				_refKebutuhanAkhir = Config._tanggalLocal(Config._convertTgl(Config._getDateStr(_refKebutuhanAkhirDate,'-')),'-',' ');
				Permintaan.setDatepickerPP($(this),_tglKirimAwal,_refKebutuhanAkhir);
				if(index != _last_index){
					Permintaan.disableDatepickerPP($(this));
				}
			});
		}

	}



	$('#div_tombol_simpan div.btn').click(function(){
		/* cek apakah sudah tidak ada error */
		var _aksi = $(this).data('aksi');

		var _dataSemua = Permintaan.kumpulkan_data_pp(_aksi);
	//	var _status = $('span.btn.pp_terpilih').hasClass('exist') ? 'update' : 'insert';
		var _error = _dataSemua._error;
		var _berubah = _dataSemua._berubah;
		var _umurTmp, _ketKirim;

		/* pastikan ketika tombol diklik masih dalam timeline PP yang diijinkan */
		var _keb_awal = $('div#tabel_pp table').find('input[name=tgl_keb_awal]').first().val();
		var tgl_keb_awal = Config._tanggalDb(_keb_awal,' ','-');
		var _tmp_tgl_keb_awal = new Date(tgl_keb_awal);
		_tmp_tgl_keb_awal.setDate(_tmp_tgl_keb_awal.getDate() - 1);
		var _hari_ini = new Date(tanggal_sistem);
		var timelinePP = Permintaan.timeline_pp(Config._convertTgl(Config._getDateStr(_tmp_tgl_keb_awal,'-')),'brd');
	//	var timelinePP = Permintaan.timeline_pp(tgl_keb_awal,'brd');
		var tglDO = timelinePP.tglDO;
		var tglMaxPPDate = timelinePP.tglMaxPPDate;
		var minTglBuatPP = timelinePP.minTglBuatPP;
		var _tglKirimDate = timelinePP.tglKirimDate;

		if((_hari_ini > tglMaxPPDate ) || (_hari_ini < minTglBuatPP)){
			_error++;
			toastr.warning('Tidak bisa membuat / approve PP <br />'+'Tgl DO '+Config._tanggalLocal(tglDO,'-',' ')+' <br /> Tanggal Max PP '+Config._tanggalLocal(Config._getDateStr(tglMaxPPDate),'-',' ')+'<br /> Min buat PP '+Config._tanggalLocal(Config._getDateStr(minTglBuatPP),'-',' '));
		}

		/* pastikan semua keterangan sudah terisi jika umur pakan > standart umur pakan */
		$('#tabel_pp table tbody tr').each(function(){
			_umurTmp = $(this).find('td.umur_pakan').text();
			if(_umurTmp > _standart_umur_pakan){
				_ketKirim = $.trim($(this).find('td.keterangan_pp textarea').val());
				if(_ketKirim == '-' || empty(_ketKirim)){
					_error++;
					toastr.warning('Keterangan harus diisi, karena umur pakan > '+_standart_umur_pakan+' hari');
				}
			}
		});
		if(!_error){
			var _tr = $('span.btn.pp_terpilih').closest('tr');
			var _sudah_ada_pp = $('span.btn.exist').length;
			var _no_pp = $('input[name=no_pp]').val() || null;

			if(_aksi == 'simpan' && !_sudah_ada_pp){
				if(_tr.length == 0 ){
					_error++;
					toastr.error('Detail PP belum dipilih');
				};
			};

		}
		/*
		if(!_sudah_ada_pp && _berubah == 0){
			_error++;
			toastr.error('Tidak ada yang berubah');
		}
		*/
		if(!_error){
			bootbox.confirm({
			    title: 'Konfirmasi Perubahan',
			    message: 'Apakah akan melanjutkan perubahan ?',
			    buttons: {
			        'cancel': {
			            label: 'Batal',
			            className: 'btn-default',
			        },
			        'confirm': {
			            label: 'Ya',
			            className: 'btn-danger',
			        }
			    },
			    callback: function(result) {
			        if (result) {
			        	switch (_aksi){
						case 'simpan':
							var _deleted = Permintaan._hapus_pengiriman;
							Permintaan.simpan_permintaan_pakan(_dataSemua._gf,_no_pp,_dataSemua._dh,_dataSemua._dd,_deleted);
							break;
						case 'rilis':
							var _deleted = Permintaan._hapus_pengiriman;
							Permintaan.rilis_permintaan_pakan(_dataSemua._gf,_no_pp,_dataSemua._dh,_dataSemua._dd,_deleted);
							break;
						case 'approve1':
							var _reset_pp = $('#div_ubah_tanggal>div').data('reset_pp') == 1 ? 1 : 0;
							if(_reset_pp){
								if(!_tr.length){
									toastr.warning('Tidak ada data PP yang dipilih, klik tombol pilih dahulu');
								}
								else{
									/* untuk reset data pp disimpan sementara kedalam variabel _tmpData ketika klik tombol tambah pengiriman */
									_dataSemua = Permintaan.kumpulkan_data_pp(_aksi);
									var _ref_id = $('#ref_id').text();
									Permintaan.reset_approve_permintaan_pakan(_dataSemua._gf,_no_pp,_dataSemua._dh,_dataSemua._dd,'A',_ref_id);
								}

							}
							else{
								Permintaan.approve_permintaan_pakan(_dataSemua._gf,_no_pp,_dataSemua._dh,_dataSemua._dd,'A',_dataSemua._pakan_baru);
							}
							break;
						case 'approve2':
							Permintaan.approve_permintaan_pakan(_dataSemua._gf,_no_pp,_dataSemua._dh,_dataSemua._dd,'C');
							break;
			        	}
			        }
			    }
			});

	}


	});

	$('#div_ubah_tanggal>div').click(function(){
		var _reset_pp = $(this).data('reset_pp');
		if(!_reset_pp){
			var _nopp = $('#transaksi input[name=no_pp]').val();
			var _pp_awal = $(this).data('pp_awal');
			$('#transaksi input[name=no_pp]').val('');
			$('#ref_id').text(_nopp);
			/* ubah tanggal permintaan */
			$('#transaksi input[name=tgl_permintaan]').val(Config._tanggalLocal(tanggal_sistem, '-', ' '));

			$(this).data('reset_pp',1);
			/* tampilkan tambah pengiriman */
		//	$('#tabel_pp tfoot tr').removeClass('hide');
			$('#tabel_pp tbody tr:not(:first)').remove();
			var _trFirst = $('#tabel_pp tbody tr:first');
			_trFirst.find('td.umur_pakan,td.kuantitas_pp').text('-');
			_trFirst.find('input').not('input[name=tgl_keb_awal]').val('');
			_trFirst.find('td:last>span').attr('data-reset',1);
			_trFirst.find('span.btn').removeClass('pp_terpilih');
			_trFirst.find('span.btn').removeClass('exist').removeClass('change');
			_trFirst.find('span.btn').addClass('new');
			_trFirst.find('textarea').prop('readonly',0).val('-');
			/* tambahkan datepicker untuk tglkirim dan kebutuhan akhir */
			var _tgl_pp = Config._tanggalDb($('#transaksi input[name=tgl_permintaan]').val(),' ');
			var _tgl_keb_awal = new Date(Config._convertTgl(Config._tanggalDb($('#transaksi input[name=tgl_keb_awal]').val(),' ')));
			/* kurangi 1 karena akan dianggap sebagai kebutuhan akhir pada fungsi get_kebutuhan_awal */
			_tgl_keb_awal.setDate(_tgl_keb_awal.getDate() - 1);
			var _data_keb_awal = {tgl : Config._convertTgl(Config._getDateStr(_tgl_keb_awal)), name : ''};
			Permintaan.get_kebutuhan_awal(_tgl_pp,_data_keb_awal,_pp_awal);
			Permintaan.enableDatepickerPP($('#tabel_pp tbody tr:first'));
			$('div#kebutuhan_pakan_internal').html('');
			$('div#sisa_konsumsi_pakan').html('');
		}
	});

}());
