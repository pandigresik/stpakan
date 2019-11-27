$(function(){
	'use strict';
	var tanggal_sistem = $('#tanggal_server').data('tanggal_server');
	var _standart_umur_pakan = Permintaan._standart_umur_pakan;	

	/* isi nilai kebutuhan awal, hanya jika pp baru saja */
	var _nopp = $('input[name=no_pp]').val();
	var _noref = $('#ref_id').text();
	if(empty(_nopp) && empty(_noref)){
		$('input[name=tgl_permintaan]').val(Config._tanggalLocal(Config._getDateStr(new Date(tanggal_sistem)),'-',' '));
	}
	else{
		/* jika masih draft berarti masih bisa disimpan lagi, sehingga datepicker perlu diaktifkan pada baris terakhir */
		var _adaTombolSimpan = $('#div_tombol_simpan div[data-aksi=simpan]').length;
		if(_adaTombolSimpan){
			/*			
			var _tglKirimAwal = $('input[name=tgl_kirim]').val();
			var _refKebutuhanAkhir;						
			
			var _refKebutuhanAkhirDate = new Date(Config._convertTgl(Config._tanggalDb($('input[name=tgl_keb_awal]').val(),' ','-')));
			_refKebutuhanAkhirDate.setDate(_refKebutuhanAkhirDate.getDate() - 1);
			_refKebutuhanAkhir = Config._tanggalLocal(Config._convertTgl(Config._getDateStr(_refKebutuhanAkhirDate,'-')),'-',' ');
			Permintaan.setDatepickerPP($('#transaksi'),_tglKirimAwal,_refKebutuhanAkhir,'bdy');						
			*/
		}

	}


	$('#div_tombol_simpan div.btn').click(function(){
		/* cek apakah sudah tidak ada error */
		var _aksi = $(this).data('aksi');		
		var _dataSemua = Permintaan.kumpulkan_data_pp(_aksi);	
		var _error = _dataSemua._error;		
		var _umurTmp, _ketKirim;

		/* pastikan ketika tombol diklik masih dalam timeline PP yang diijinkan */
		var _keb_awal = $('div#transaksi').find('input[name=tgl_keb_awal]').first().val();
		var tgl_keb_awal = Config._tanggalDb(_keb_awal,' ','-');
		var _tmp_tgl_keb_awal = new Date(tgl_keb_awal);
		_tmp_tgl_keb_awal.setDate(_tmp_tgl_keb_awal.getDate() - 1);
		var _hari_ini = new Date(tanggal_sistem);
		var _flok = $('div#transaksi').find('input[name=flock]').val();
		var _keb_awal_docin = new Date(Permintaan.get_tgl_doc_in_bdy(_flok));
		var _tgldocin = new Date(Permintaan.get_tgl_doc_in_bdy(_flok));
		var _no_pp = $('#transaksi input[name=no_pp]').val();
		/** jika kosong maka ambil ref_id sebagai no_pp */
		if(empty(_no_pp)){
			_no_pp = $('#transaksi span.span_ref_id').text();
		}
		var _noreg = $('#transaksi select[name=no_reg]').val();
		/** pastikan pengajuan sudah dientry */
		var _totalHarusEntry = $('.required').length;
		if(_totalHarusEntry <= 0){
			_error++;
			toastr.warning('Mohon melengkapi entrian pengajuan PP');
			return;
		}else{
			var _nilaiElm,_pesanEntri;
			$('.required').each(function(){
				_nilaiElm = $.trim($(this).val());
				if(_nilaiElm.length <= 0){
					_error++;
					_pesanEntri = 'Mohon melengkapi entrian pengajuan PP';
					return false;
				}else{
					var _minlength = $(this).data('minlength');
					if(_minlength != undefined){
						if(_nilaiElm.length < _minlength){
							_error++;
							_pesanEntri = 'Alasan pengajuan terdiri dari '+_minlength+' - '+$(this).attr('maxlength')+' karakter';
							return false;
						}
					}
				}
			});
			if(_error){
				toastr.warning(_pesanEntri);
				return;
			}
		}		
		/* tambahkan parameter flok untuk pengecekan tgl kirim forecast */
		Permintaan._varFlock = _flok;	
		var timelinePP = Permintaan.timeline_pp(Config._convertTgl(Config._getDateStr(_tmp_tgl_keb_awal,'-')),'bdy',_tgldocin);
		var tglDO = timelinePP.tglDO;
		var tglMaxPPDate = timelinePP.tglMaxPPDate;
		var minTglBuatPP = timelinePP.minTglBuatPP;
		var _tglKirimDate = timelinePP.tglKirimDate;
		
		if((_hari_ini > tglMaxPPDate ) || (_hari_ini < minTglBuatPP)){
			_error++;
			var _pesanTimeline = '';
			switch(_aksi){
				case 'simpan':										
				case 'rilis':
					_pesanTimeline = 'Pengajuan';
					break;
				case 'review':				
					_pesanTimeline = 'Review';
					break;				
			}
			toastr.warning(_pesanTimeline+' melebihi batas timeline. <br /> Timeline PP '+Config._tanggalLocal(Config._getDateStr(minTglBuatPP),'-',' ')+' s/d '+Config._tanggalLocal(Config._getDateStr(tglMaxPPDate),'-',' ')+'<br /> Timeline DO '+Config._tanggalLocal(tglDO,'-',' '));			
		}

		/* pastikan semua keterangan sudah terisi jika umur pakan > standart umur pakan */		
		_umurTmp = $('label.umur_pakan').text();
		if(_umurTmp > _standart_umur_pakan){
			_ketKirim = $.trim($('#transaksi').find('textarea[name=keterangan]').val());
			if(_ketKirim == '-' || empty(_ketKirim)){
				//_error++;
				//toastr.warning('Keterangan harus diisi, karena umur pakan > '+_standart_umur_pakan+' hari');
			}
		}

		if(!_error){
			/** cek apakah melebihi budget atau tidak, hanya untuk kafarm saja */
			if(in_array(_aksi,['simpan','rilis','review'])){
				var cekBudget = Permintaan.get_sisa_budget(_noreg);
				
				$.when(cekBudget).done(function(){
					/** cari total pengajuan, review atau rekomendasi */
					var _review = $('#transaksi input[name=jml_review]');
					var _rekomendasi = $('#transaksi input[name=jml_rekomendasi]');
					var _totalElm = _review.length ? _review : _rekomendasi;
					var _tr_kode_barang, _tmp_kode_barang, _tmp_pengajuan = {};
					var _overBudget = 0;
					_totalElm.each(function(){
						_tr_kode_barang = $(this).closest('tr');
						_tmp_kode_barang = _tr_kode_barang.data('kode_barang');
						if(_tmp_pengajuan[_tmp_kode_barang] == undefined){
							_tmp_pengajuan[_tmp_kode_barang] = 0;
						}
						_tmp_pengajuan[_tmp_kode_barang] += parseInt($(this).val());
					});				
					
					for(var _kb in _tmp_pengajuan){
						var _sisaBudget = cekBudget[_kb] == undefined ? 0 : cekBudget[_kb];											
						if(_tmp_pengajuan[_kb] > _sisaBudget){
							_overBudget = 1;
						}
					}
					if(_overBudget){
						bootbox.confirm({
							title: 'Konfirmasi',
							message: 'Kuantitas pengajuan anda melebihi budget pakan. <br /> Apakah anda yakin akan melanjutkan pengajuan permintaan kebutuhan pakan ?',
							buttons: {
								'cancel': {
									label: 'Tidak',
									className: 'btn-default',
								},
								'confirm': {
									label: 'Ya',
									className: 'btn-danger',
								}
							},
							callback: function(result) {
								if (result) {
									Permintaan.exec_simpan_pp(_dataSemua,_aksi,_no_pp);
								}
							}
						})
					}else{
						Permintaan.exec_simpan_pp(_dataSemua,_aksi,_no_pp);
					}
				});
			}else{
				Permintaan.exec_simpan_pp(_dataSemua,_aksi,_no_pp);
			}						
		}
	});	

}());
