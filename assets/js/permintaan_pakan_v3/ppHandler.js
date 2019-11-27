'use strict';
/* memerlukan file forecast/config.js */
var Permintaan = {
    timer: true,
    _timerAbsensi: false,
    _prosesServer: 0,
    /* variabel untuk konfigurasi pengiriman pakan */
    _max_buat_pp: 1,
    _min_buat_pp: 2,
    /** minimal buat pp, h-2 dari tgl maksimal pp */
    _hari_libur: null,
    _max_kirim: 2,
    /* h-2 dari tgl kebutuhan awal */
    _min_kirim: 3,
    /* h-3 dari tgl kebutuhan awal */
    _max_kirim_bdy: 1,
    /* h-2 dari tgl kebutuhan awal */
    _min_kirim_bdy: 2,
    /* 2 h-2 dari tgl kebutuhan awal */
    _max_umur_pakan: 10,
    /* 10 umur pakan di farm */
    _max_umur_pakan_bdy: 10,
    /* 5 umur pakan di farm */
    _standart_umur_pakan: 5,
    _max_kebutuhan_pakan: 3,
    /* 3 standarnya adalah pp hanya untuk maksimal 3 hari*/
    _max_kebutuhan_pakan_awal: 8,
    /* standarnya adalah pp hanya untuk pp pertama kali */
    _max_kebutuhan_pakan_awal_bdy: 7,
    /* standarnya adalah pp hanya untuk pp pertama kali */
    _max_buat_do: 2,
    /* 2 hari dari tgl kirim */
    _max_buat_do_bdy: 1,
    /** buat do akan panen */
    _maxBuatDoPanen: 0,
    /* 1 hari dari tgl kirim untuk budidaya */
    _max_umur_pakan_awal: 10,
    /* umur pakan di farm untuk pp pertama kali, 7 hari kebutuhan + h-2 dari tglkirim*/
    _min_tgl_kirim_pp: 7,
    /* umur pakan di farm untuk pp pertama kali*/
    _tgl_doc_in_bdy: {},
    /* digunakan oleh budidaya, untuk mengetahui umur ayam */
    _tgl_kirim_forecast: {},
    /* untuk menyimpan data tanggal kirim forecast budidaya */
    _max_reject_approve_bdy: 0,
    /* aslinya 3 */
    _max_kebutuhan_pakan_19: 1,
    _umur_pakan_harian: 19,

    _pengurangHari: 1, // 1
    _ppAkanPanen: 25,
    _minKirimAkanPanen: 0,
    _maxKirimAkanPanen: 0,
    _dataSimpanServer: {},
    _varFlock: 0,
    _sisaBudget: {},
    _levelFingerPrintRilisPP: { 'PENGAWAS': 'KAFARM', 'KAFARM': null },
    _options_kirim_bdy: {
        /* disable hari minggu */
        beforeShowDay: function(date) { return [!Config.is_hari_libur(Config._getDateStr(date), Permintaan.get_hari_libur())]; },
        dateFormat: 'dd M yy',
        onSelect: function(date, lastDate) {
            if (date !== null) {
                if (lastDate.lastVal != date) {
                    var _error = 0;
                    var _newMaxDate = new Date(Config._convertTgl(Config._tanggalDb($("input[name=tgl_keb_akhir]").val(), ' ', '-')));

                    var _kebAwal = $("input[name=tgl_keb_awal]").val();
                    var _flok = $('input[name=flock]').val();
                    var _kebAwalDate = new Date(Config._convertTgl(Config._tanggalDb(_kebAwal, ' ', '-')));
                    var _defKebAkhir = $("input[name=tgl_keb_akhir]").val();
                    var _finalKebAkhir = new Date(Permintaan.tgl_kirim_selanjutnya(Config._convertTgl(Config._tanggalDb(date, ' ', '-')), Config._convertTgl(Config._tanggalDb(_defKebAkhir, ' ', '-')), 'bdy', _flok));
                    if (_finalKebAkhir > _newMaxDate) {
                        _newMaxDate = _finalKebAkhir;
                        _error = 0;
                    }

                    $("input[name=tgl_keb_akhir]").val(Config._tanggalLocal(Config._getDateStr(_newMaxDate), '-', ' '));
                    /* update umur pakan */
                    var _tglKirim = new Date(Config._convertTgl(Config._tanggalDb(date, ' ', '-')));
                    var _jarak_pakan = Config.get_selisih(_tglKirim, _newMaxDate);
                    $("label.umur_pakan").text(_jarak_pakan);
                } else {
                    $(this).val(lastDate.lastVal);
                }
            }
        }
    },
    _options_kebutuhan_akhir_bdy: {
        dateFormat: 'dd M yy',
        //		 disabled : true, // sementara 24-06-2016
        onSelect: function(date, lastDate) {
            if (date !== null) {
                var _newMinDate = new Date(Config._convertTgl(Config._tanggalDb(date, ' ', '-')));
                /* jika ada perubahan tanggal yang dipilih */
                if (lastDate.lastVal != date) {
                    var _kebAwal = $("input[name=tgl_keb_awal]").val();
                    var _tglMinKirim = new Date(Config._convertTgl(Config._tanggalDb(_kebAwal, ' ', '-')));
                    /* update umur pakan */
                    var _tglKirim = new Date(Config._convertTgl(Config._tanggalDb($("input[name=tgl_kirim]").val(), ' ', '-')));
                    var _tglKebutuhanAkhir = new Date(Config._convertTgl(Config._tanggalDb(date, ' ', '-')));
                    var _jarak_pakan = Config.get_selisih(_tglKirim, _tglKebutuhanAkhir);
                    var flok = $('input[name=flock]').val();
                    var _error = 0;
                    var _kebAwalDate = new Date(Config._convertTgl(Config._tanggalDb(_kebAwal, ' ', '-')));
                    var _tmp_keb_pakan = Config.get_selisih(_kebAwalDate, _tglKebutuhanAkhir) + 1;
                    var _reviewKebAkhir = $(this).data('bisa_ubah_keb_akhir');
                    var _ini = $(this);
                    if (_reviewKebAkhir) {
                        bootbox.confirm({
                            message: 'Apakah anda yakin akan mengubah tanggal kebutuhan ? <br /> Jika ya, maka tanggal kebutuhan pada detail PP akan diupdate.',
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
                                    $("label.umur_pakan").text(_jarak_pakan);
                                    _ini.trigger('change');
                                } else {
                                    _ini.val(lastDate.lastVal);
                                }
                            }
                        });
                    } else {
                        $("label.umur_pakan").text(_jarak_pakan);
                        $(this).trigger('change');
                    }

                } else {
                    $(this).val(lastDate.lastVal);
                }
            }

        }

    },

    kembali: function(elm) {
        $('#transaksi').hide();
        $('#main_pp').show();
        /* refresh list pp yang telah dibuat */
        $('#div_permintaan_pakan').find('#span_cari_pp').click();
    },


    set_tgl_doc_in_bdy: function(data) {
        this._tgl_doc_in_bdy = data;
    },
    get_tgl_doc_in_bdy: function(flok) {
        if (flok == undefined) {
            return this._tgl_doc_in_bdy;
        } else {
            return this._tgl_doc_in_bdy[flok];
        }
    },

    load_farm: function(elm) {
        var id = elm.val();
        var kode_siklus = elm.find('option:selected').data('kode_siklus');
        if (!empty(id)) {
            $.ajax({
                url: 'permintaan_pakan_v3/permintaan_pakan/datafarm/' + id + '/' + kode_siklus,
                beforeSend: function() {
                    $('#div_permintaan_pakan').html('');
                },
                async: false,
                dataType: 'html',
                success: function(data) {
                    $('#div_permintaan_pakan').html(data);
                }
            }).done(function() {
                /* override nilai config ppHandlers pada  */
                Permintaan.overrideNilaiConfig(id);
                if ($('input[name=tindak_lanjut]').length) {
                    $('input[name=tindak_lanjut]').trigger('change');
                } else {
                    $('#div_permintaan_pakan').find('#span_cari_pp').click();
                }
            });
        } else {
            toastr.warning('Pilih salah satu farm');
        }
    },
    add_datepicker: function(elm, options) {
        elm.datepicker(options);
    },

    get_last_pp: function(flok) {
        var _result = {};
        var kodeflok = (flok === undefined) ? '' : flok;
        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/get_last_pp/' + kodeflok,
            type: 'get',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    _result = { name: 'tgl_pp', tgl: data.content['tgl_keb_akhir'], pp_pending: data.pp_pending };
                } else {
                    /* kalau ada flok berarti budidaya */
                    if (!empty(kodeflok)) {
                        var _x = new Date(Permintaan.get_tgl_doc_in_bdy(kodeflok));

                        _x.setDate(_x.getDate() - 1);
                        _result = { name: 'doc_in', tgl: Config._getDateStr(_x), pp_pending: data.pp_pending };
                    }
                }
            },
            async: false,
            cache: false,
        });

        return _result;
    },
    get_last_pp_noreg: function(noreg, kodeflok) {
        var _result = {};
        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/get_last_pp_noreg/',
            data: { noreg: noreg },
            type: 'get',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    _result = { name: 'tgl_pp', tgl: data.content['tgl_keb_akhir'], pp_pending: data.pp_pending };
                } else {
                    /* kalau ada flok berarti budidaya */
                    if (!empty(kodeflok)) {
                        var _x = new Date(Permintaan.get_tgl_doc_in_bdy(kodeflok));
                        //_x.setDate(_x.getDate() - 1); // gak perlu diminus 1, karena docIn itu H-1 dari kebutuhan awal pertama kali
                        _result = { name: 'doc_in', tgl: Config._getDateStr(_x), pp_pending: data.pp_pending, ploting_pelaksana: data.ploting_pelaksana };
                    }
                }
            },
            async: false,
            cache: false,
        });
        return _result;
    },
    click_button_footer: function(label) {
        $('.bootbox .modal-footer button[data-bb-handler=' + label + ']').click();
    },

    show_detail_list_kebutuhan_pakan: function(elm) {
        var _pp = $('input[name=no_pp]').val();
        /** jika kosong maka ambil ref_id sebagai no_pp */
        if (empty(_pp)) {
            _pp = $('#transaksi span.span_ref_id').text();
        }
        var _noreg = $('select[name=no_reg]').val();
        var _status = $('input[name=no_pp]').data('status');
        var _new = empty(_pp) ? 1 : 0;
        var _grup_farm = 'bdy';
        var _flock = $('input[name=flock]').val();
        /** jika no_pp sudah pernah disimpan maka keb_akhir_lama akan terisi sesuai dengan data di db */
        var _keb_akhir_lama = $(elm).data('keb_akhir_lama');

        var _tgl_kirim = Config._tanggalDb($('input[name=tgl_kirim]').val(), ' ', '-');
        var _keb_awal = Config._tanggalDb($('input[name=tgl_keb_awal]').val(), ' ', '-');
        var _keb_akhir = Config._tanggalDb($('input[name=tgl_keb_akhir]').val(), ' ', '-');
        var _data = { no_reg: _noreg, no_lpb: _pp, tgl_kirim: _tgl_kirim, tgl_keb_awal: _keb_awal, tgl_keb_akhir: _keb_akhir, no_lpb: _pp, _new: _new, _grup_farm: _grup_farm, status: _status, keb_akhir_lama: _keb_akhir_lama };

        if (!empty(_flock)) {
            _data['_flock'] = _flock;
        }
        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/list_kebutuhan_pakan',
            beforeSend: function() {
                $('div#kebutuhan_pakan_internal').html('');
            },
            data: _data,
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    $('div#kebutuhan_pakan_internal').html(data.content.kebutuhan_internal).promise().done(function() {
                        Permintaan.rencanaTanggalPanen(_noreg, _keb_awal);
                    });
                    $('#transaksi form').find('.showNext').removeClass('hide');
                    $('#btnHistoryInfo').trigger('click');
                }
            },
        }).done(function() {
            $('#kebutuhan_pakan_internal').find('input[name=jml_review]').numeric();
            $('#kebutuhan_pakan_internal').find('input[name=jml_rekomendasi]:not([readonly])').each(function() {
                    $(this).numeric({ max: $(this).data('max'), min: 0 });
                })
                /** get rencana panen */

        });
    },
    rencanaTanggalPanen: function(noreg, tglKebutuhanAwal) {
        var _rencanaPanen = $('#tableRencanaPanen').find('table');
        if (!_rencanaPanen.length) {
            var _url = 'permintaan_pakan_v3/permintaan_pakan/rencanaPanen';
            $.get(_url, { noreg: noreg, tglkebutuhan: tglKebutuhanAwal }, function(data) {
                $('#tableRencanaPanen').html(data);
            }, 'html')

        }
    },
    resetKebutuhanInternal: function() {
        if ($('#btnHistoryInfo>i').hasClass('glyphicon-chevron-down')) {
            $('#btnHistoryInfo').click();
        }
        $('div#kebutuhan_pakan_internal').html('');
        $('div#tableBudgetPakan').html('');
        $('div#tablePerformaKandang').html('');
        $('div#tableRiwayatPP').html('');

    },
    showInfoHistory: function(elm) {
        if (empty($.trim($('div#tableBudgetPakan').html()))) {
            var _noreg = $('select[name=no_reg]').val();
            var _flok = $('select[name=no_reg]>option:selected').data('flok_bdy');
            var _no_lpb = $('input[name=no_pp]').val();
            if (empty(_noreg)) {
                bootbox.alert('Kandang belum dipilih');
                return false;
            }

            $.ajax({
                url: 'permintaan_pakan_v3/permintaan_pakan/historyInfoPP',
                beforeSend: function() {
                    $('div#tableBudgetPakan').html('');
                    $('div#tablePerformaKandang').html('');
                    $('div#tableRiwayatPP').html('');
                },
                data: { no_reg: _noreg, tgldocin: Permintaan.get_tgl_doc_in_bdy(_flok), no_lpb: _no_lpb },
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        /* load tableBudgetPakan */
                        $('div#tableBudgetPakan').html(data.content.budget_pakan);
                        $('div#tablePerformaKandang').html(data.content.performa_kandang);
                        $('div#tableRiwayatPP').html(data.content.riwayat_pp);
                    }

                },
                async: false
            });
        }
        var _divHistory = $(elm).closest('.form-group').siblings('.detailHistory');
        _divHistory.removeClass('hide');
        //$(elm).find('i').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
    },

    showPerformaKandang: function(elm) {
        var _div_performa_kandang = $(elm).next('div.div_performa_kandang');
        var no_pp = $(elm).data('no_pp');

        if (empty(_div_performa_kandang.html())) {
            $.ajax({
                url: 'permintaan_pakan_v3/permintaan_pakan/performaKandang',
                data: { no_lpb: no_pp },
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        _div_performa_kandang.html(data.content.performa_kandang).promise().done(function() {
                            $(document).trigger("stickyTable");
                            /*$(_div_performa_kandang).find('table').scrollabletable({																		
                            	'max_height_scrollable' : 150,
                            	'max_width' : 1150,
                            	
                            	'scroll_horizontal' : 0		
                            });
                            */
                        });
                    }
                },
            });
        } else {
            if (_div_performa_kandang.is(':hidden')) {
                _div_performa_kandang.show();
            } else {
                _div_performa_kandang.hide();
            }
        }
        $(elm).find('i').toggleClass('glyphicon-chevron-right glyphicon-chevron-down');
    },

    get_sisa_budget: function(_noreg) {
        var _result = {};
        if (this._sisaBudget[_noreg] == undefined) {
            $.ajax({
                url: 'permintaan_pakan_v3/permintaan_pakan/getSisaBudget',
                data: { no_reg: _noreg },
                type: 'get',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        _result[_noreg] = data.content;
                        Permintaan._sisaBudget[_noreg] = data.content;
                    }
                },
                async: false,
                cache: false,
            });
        }
        return this._sisaBudget[_noreg];
    },
    exec_simpan_pp: function(_dataSemua, _aksi, _no_pp) {
        /* list pesan */
        var _pesan_perubahan = {
            'rilis_langsung': 'Apakah anda yakin akan melanjutkan merilis permintaan kebutuhan pakan?',
            'rilis_draft': 'Apakah anda yakin akan melanjutkan merilis permintaan kebutuhan pakan dengan no.PP :' + _no_pp + ' ?',
            'draft': 'Apakah akan melanjutkan menyimpan permintaan kebutuhan pakan ?'
        };
        var _pesan;

        switch (_aksi) {
            case 'simpan':
                _pesan = _pesan_perubahan['draft'];
                break;
            case 'rilis':
                _pesan = empty(_no_pp) ? _pesan_perubahan['rilis_langsung'] : _pesan_perubahan['rilis_draft'];
                break;
            case 'review':
                _pesan = 'Apakah anda yakin akan melanjutkan review permintaan kebutuhan pakan ?';
                break;
        }
        bootbox.confirm({
            title: 'Konfirmasi Perubahan',
            message: _pesan,
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
                    var nextStatusLpb;
                    var _statusLama = $('input[name=no_pp').data('status');
                    switch (_aksi) {
                        case 'simpan':
                            nextStatusLpb = 'D';
                            break;
                        case 'rilis':
                            nextStatusLpb = 'N';
                            break;
                        case 'review':
                            nextStatusLpb = 'RV';
                            break;
                    }
                    Permintaan.simpan_permintaan_pakan(_dataSemua._aa, _dataSemua._gf, _no_pp, _dataSemua._dh, _dataSemua._dd, _dataSemua._review, nextStatusLpb, _statusLama);
                }
            }
        });
    },

    simpan_permintaan_pakan: function(autoApprove, grup_farm, no_pp, dataHeader, dataDetail, _review, _nextStatusLpb, _statusLama) {
        /* cek status row_approval apakah masih ada proses di server atau tidak */
        var _prosesServer = this._prosesServer;
        if (_prosesServer) {
            bootbox.alert('masih menunggu proses di server');
            return;

        }
        this._dataSimpanServer = {
            autoApprove: autoApprove,
            grup_farm: grup_farm,
            no_pp: no_pp,
            dataHeader: dataHeader,
            dataDetail: dataDetail,
            _review: _review,
            _nextStatusLpb: _nextStatusLpb,
            _statusLama: _statusLama
        };
        var _noreg = $('select[name=no_reg]').val();
        if (_nextStatusLpb != 'N') {
            this.save_server();
        } else {
            this.fingerprint(_noreg, 'PENGAWAS');
        }
    },
    save_server: function() {
        var grup_farm = this._dataSimpanServer['grup_farm'];
        var no_pp = this._dataSimpanServer['no_pp'];
        var dataHeader = this._dataSimpanServer['dataHeader'];
        var dataDetail = this._dataSimpanServer['dataDetail'];
        var _review = this._dataSimpanServer['_review'];
        var _nextStatusLpb = this._dataSimpanServer['_nextStatusLpb'];
        var _statusLama = this._dataSimpanServer['_statusLama'];
        var autoApprove = this._dataSimpanServer['autoApprove'];
        /* status menunjukkan insert atau update */
        $.ajax({
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                Permintaan._prosesServer = 1;
            },
            data: { autoApprove: autoApprove, no_pp: no_pp, _dh: dataHeader, _dd: dataDetail, statusLpb: _nextStatusLpb, _gf: grup_farm, review: _review, _sl: _statusLama },
            url: 'permintaan_pakan_v3/permintaan_pakan/simpan_pp',
            success: function(data) {
                Permintaan._prosesServer = 0;
                if (data.status) {
                    $('#transaksi input[name=no_pp]').val(data.content);
                    $('#transaksi select[name=no_reg]').find('option').not(':selected').remove();
                    var _tgl_keb_akhir_db = Config._convertTgl(Config._tanggalDb($('#transaksi input[name=tgl_keb_akhir]').val(), ' '));
                    $('#transaksi input[name=tgl_keb_akhir]').data('keb_akhir_lama', _tgl_keb_akhir_db);
                    if (data.lockpp) {
                        $('#div_tombol_simpan').html('');
                        $('#transaksi').find('input,textarea').prop('readonly', 1);
                        $('#transaksi').find('input.hasDatepicker').datepicker('option', 'disabled', 1);
                    }
                    if (data.createop) {
                        $.post('permintaan_pakan_v3/permintaan_pakan/approve_pp_budidaya', { no_pp: [data.content] }, function(data) {
                            if (data.status) {
                                toastr.success(data.message);
                            }
                        }, 'json');
                    }
                    Permintaan._dataSimpanServer = {};
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                }
            },
            async: false,
        });
    },
    transaksi_pp: function(elm, target) {
        var _no_pp = $(elm).data('no_pp');
        var status = $(elm).data('status') || null;
        var no_flok = $(elm).data('flok') || null;

        $.ajax({
            type: 'post',
            data: { no_pp: _no_pp, status: status, no_flok: no_flok },
            url: 'permintaan_pakan_v3/permintaan_pakan/transaksi_pp',
            dataType: 'html',
            async: false,
            success: function(data) {
                $('#transaksi').html(data);
            },
        });

        $(target).click();
    },
    show_lhk: function(elm) {
        var noreg = $(elm).closest('tr').find('td:first').data('noreg');
        var _tmplhk = $(elm).closest('tr').find('td').eq(2).text();
        var _kebawal = null;
        if (!empty(_tmplhk)) {
            var tgllhk = Config._tanggalDb(_tmplhk, ' ');
            var _tmp = new Date(tgllhk);
            _tmp.setDate(_tmp.getDate() - 7);
            _kebawal = Config._getDateStr(_tmp, '-');
        };

        $.ajax({
            beforeSend: function() {

            },
            type: 'post',
            dataType: 'json',
            data: { noreg: noreg, kebutuhan_awal: _kebawal },
            url: 'home/kertas_kerja/list_kertas_kerja',
            success: function(data) {
                //	$(data).insertAfter($(elm));
            },
        }).done(function(data) {
            var _options = {
                title: 'Kertas Kerja',
                message: data.content,
                className: 'largeWidth',
                buttons: {
                    Ok: {
                        label: 'Tutup',
                        className: '',
                        callback: function(e) {

                        }
                    }
                },
            };

            bootbox.dialog(_options).bind('shown.bs.modal', function() {
                var _bb = $(this);
                $(this).find('table').scrollabletable({
                    'max_width': _bb.find('div.bootbox-body').innerWidth(),
                });
            });

        });
    },

    show_lhk_bdy: function(elm) {
        var noreg = $(elm).data('noreg');
        var tgl_transaksi = $(elm).data('tgl_transaksi');
        $.ajax({
            beforeSend: function() {

            },
            type: 'post',
            dataType: 'html',
            data: { noreg: noreg, tgl_transaksi: tgl_transaksi },
            url: 'riwayat_harian_kandang/riwayat_harian_kandang/lihat',
            success: function(data) {
                var _options = {
                    title: 'Data RHK ' + noreg + ' Tanggal ' + Config._tanggalLocal(tgl_transaksi, '-', ' '),
                    message: data,
                    className: 'largeWidth',
                };

                bootbox.dialog(_options);
            },
        });

    },

    tambahPakan: function(elm) {
        var showCekbox = $(elm).data('showcekbox');
        bootbox.confirm({
            title: 'Penambahan Pakan',
            message: 'Apakah anda yakin akan melakukan perubahan jenis pakan ?',
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
            callback: function(result) {
                if (result) {
                    Permintaan.showModalTambahPakan(elm, showCekbox);
                }
            }
        });

    },
    showModalTambahPakan: function(elm, showCekbox) {
        /* cari tanggal kebutuhan awal dan tanggal kebutuhan akhir */
        var _keb_awal = $('#transaksi').find('input[name=tgl_keb_awal]:first').val();
        var _keb_akhir = $('#transaksi').find('input[name=tgl_keb_akhir]:first').val();
        var _data = [
            '<form>',
            '<div>',
            '<div class="form-group col-md-12 hide">',
            '<div class="col-md-6"><label class="control-label">Jenis Perubahan</label></div>',
            '<div class="col-md-6">',
            '<select class="form-control" name="jenis_perubahan">',
            '<option value="kps">Kebutuhan Pakan Sementara</option>',
            '</select>',
            '</div>',
            '</div>',
            '<div class="form-group col-md-12">',
            '<div class="col-md-6"><label class="control-label">Tanggal mulai</label></div>',
            '<div class="col-md-6">',
            '<div class="input-group date">',
            '<input type="text" value="" class="form-control" name="modal_keb_awal" readonly="">',
            '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>',
            '</div></div>',
            '</div>',
            '<div class="form-group col-md-12">',
            '<div class="col-md-6"><label class="control-label">Tanggal akhir</label></div>',
            '<div class="col-md-6">',
            '<div class="input-group date">',
            '<input type="text" value="" class="form-control" name="modal_keb_akhir" readonly="">',
            '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>',
            '</div></div>',
            '</div>',
            '<div class="form-group col-md-12"><div class="col-md-6"><label class="control-label">Jenis pakan</label></div><div class="col-md-6"><select class="form-control" name="modal_kode_pakan"></select></div></div>',
            '<div class="form-group col-md-12"><div class="col-md-6  col-md-offset-6"><span class="col-md-5 btn btn-default" onclick="Permintaan.generateTambahPakan(this)">Generate</span></div></div></div>',
            '</div>',
            '</form>'
        ];

        var _options = {
            title: 'Perubahan Pakan',
            message: _data.join(' '),
        };
        bootbox.dialog(_options).bind('shown.bs.modal', function() {
            var _bb = $(this);
            var _f = $(this).find('form');
            _bb.find('select[name=jenis_perubahan]').change(function() {
                if ($(this).val() == 'kps') {
                    _f.find('input[name=modal_keb_akhir]').datepicker('option', 'disabled', 0);
                    _f.find('input[name=modal_kode_pakan]').addClass('hide');
                    _f.find('select[name=modal_kode_pakan]').removeClass('hide');
                }
                if ($(this).val() == 'tp') {
                    /* disable datepicker */
                    _f.find('input[name=modal_keb_akhir]').datepicker('option', 'disabled', 1);
                    _f.find('input[name=modal_kode_pakan]').removeClass('hide');
                    _f.find('select[name=modal_kode_pakan]').addClass('hide');
                }
            });
            _bb.find('input[name^=modal_keb]').datepicker({
                dateFormat: 'dd M yy',
                minDate: _keb_awal,
                maxDate: _keb_akhir,
                onSelect: function(date) {
                    var _newMinMaxDate = new Date(Config._convertTgl(Config._tanggalDb(date, ' ', '-')));
                    if ($(this).attr('name') == 'modal_keb_awal') {

                        _f.find('input[name=modal_keb_akhir]').datepicker('option', 'minDate', _newMinMaxDate);

                    }
                    if ($(this).attr('name') == 'modal_keb_akhir') {

                        _f.find('input[name=modal_keb_awal]').datepicker('option', 'maxDate', _newMinMaxDate);
                    }
                },
            });

            var _noreg = $('#transaksi select[name=no_reg]').val();
            var _kp = [];
            $('#kebutuhan_pakan_internal tbody>tr>td.kode_barang').each(function() {
                _kp.push($(this).data('kode_barang'));
            });

            $.ajax({
                data: { noreg: _noreg, kodepakan: _kp },
                type: 'post',
                url: 'permintaan_pakan_v3/permintaan_pakan/get_pakan_tambahan',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        //	_bb.find('input[name=modal_kode_pakan]').data('kodepakan',data.content.kodepakan).val(data.content.namapakan);
                        var _lpj = data.list_pakan;
                        var _opt = [];
                        for (var i in _lpj) {
                            _opt.push('<option value="' + _lpj[i]['kode_pakan'] + '">' + _lpj[i]['nama_barang'] + '</option>');

                        }
                        _bb.find('select[name=modal_kode_pakan]').append(_opt.join(''));
                    } else {
                        toastr.error('Data tidak ditemukan');
                    }
                }
            });
        });
    },
    generateTambahPakan: function(elm) {
        var _error = 0;
        var _bb = $(elm).closest('.bootbox-body');
        var _awalGanti = _bb.find('input[name=modal_keb_awal]').val();
        var _akhirTambah = _bb.find('input[name=modal_keb_akhir]').val();
        var _jenisPergantian = _bb.find('select[name=jenis_perubahan]').val();
        var _kodePakan = _bb.find('input[name=modal_kode_pakan]:visible').data('kodepakan') || _bb.find('select[name=modal_kode_pakan]:visible').val();
        var _namaPakan = _bb.find('input[name=modal_kode_pakan]:visible').val() || _bb.find('select[name=modal_kode_pakan]:visible').find('option:selected').text();
        /* pastikan tanggal awal ganti pakan dan jenis pakan terisi */
        if (empty(_awalGanti)) {
            _error++;
            toastr.error('Tanggal awal pergantian pakan harus diisi');
        }
        if (empty(_kodePakan)) {
            _error++;
            toastr.error('Kode pakan harus diisi');
        }
        if (_jenisPergantian == 'kps') {
            if (empty(_akhirTambah)) {
                _error++;
                toastr.error('Tanggal akhir penambahan pakan harus diisi');
            }
        }

        if (!_error) {
            /* generate satu blok pakan tambahan lagi */
            this.set_pakan_tambahan(elm, _awalGanti, _akhirTambah, _kodePakan);
            bootbox.hideAll();
        }
    },

    set_pakan_tambahan: function(elm, _awalGanti, _akhirTambah, _kodePakan, defaultValue) {
        /* generate list pakan tambahan */
        var _noreg = $('#transaksi select[name=no_reg]').val();
        var _pjterpakai = $('#link_tambah_pakan').data('listpj');

        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/pakan_tambahan',
            data: { awal: Config._convertTgl(Config._tanggalDb(_awalGanti, ' ', '-')), akhir: Config._convertTgl(Config._tanggalDb(_akhirTambah, ' ', '-')), kodepj: _kodePakan, noreg: _noreg },
            type: 'post',
            dataType: 'html',
            success: function(data) {
                $('#kebutuhan_pakan_internal').find('table>tbody').append(data);
                if (defaultValue != undefined) {
                    var _semua_baris_kb = $('#kebutuhan_pakan_internal table>tbody').find('tr[data-kode_barang="' + _kodePakan + '"]');
                    var _tgl_kebutuhan;
                    _semua_baris_kb.each(function() {
                        _tgl_kebutuhan = $(this).find('td.tgl_kebutuhan').data('tgl_kebutuhan');
                        $(this).find('input,textarea').each(function() {
                            $(this).val(defaultValue[_tgl_kebutuhan][$(this).attr('name')]);
                        });
                        $(this).find('input[name=jml_rekomendasi], input[name=jml_review]').numeric({});
                    });
                }
            },
        })

    },

    get_hari_libur: function(minDate) {
        if (this._hari_libur == null) {
            $.ajax({
                data: { minDate: minDate },
                type: 'post',
                url: 'permintaan_pakan_v3/permintaan_pakan/get_hari_libur',
                success: function(data) {
                    if (data.status) {
                        Permintaan._hari_libur = data.content;
                    }
                },
                async: false,
                dataType: 'json',
            });
        }
        return this._hari_libur;
    },

    /* jika _refKebutuhanAkhir tidak didefinisikan berarti baris baru, data belum ada di database */
    setDatepickerPP: function(_template, _tglKirimAwal, _refKebutuhanAkhir, _grupFarm) {
        var hari_libur = Permintaan.get_hari_libur();
        var grup_farm = _grupFarm == undefined ? 'brd' : _grupFarm;
        var _kebutuhan_akhir = _refKebutuhanAkhir;
        var _tglKirimAwalDate = new Date(Config._convertTgl(Config._tanggalDb(_tglKirimAwal, ' ', '-')));
        var _newDate = new Date(Config._convertTgl(Config._tanggalDb(_kebutuhan_akhir, ' ', '-')));
        var _kebAkhirDate = new Date(Config._convertTgl(Config._tanggalDb(_kebutuhan_akhir, ' ', '-')));
        var _tglMinKirim = new Date(Config._convertTgl(Config._tanggalDb(_kebutuhan_akhir, ' ', '-')));
        var _tglKirimLalu = _template.find('input[name=tgl_kirim]').val();
        /* kebutuhan_akhir yang lama +1 adalah tgl kebutuhan awal */
        _tglMinKirim.setDate(_tglMinKirim.getDate() + 1);
        _newDate.setDate(_newDate.getDate() + 1);
        _kebAkhirDate.setDate(_kebAkhirDate.getDate() - 0);

        var tglKirim = Config._convertTgl(Config._tanggalDb(_tglKirimLalu, ' ', '-'));
        var _tglKirimDate = new Date(tglKirim);
        var _umur_pakan_tmp = Permintaan._max_umur_pakan_awal - Config.get_selisih(_tglKirimAwalDate, _tglKirimDate);
        _template.find('input').removeAttr('id').removeClass('hasDatepicker');

        /* kebutuhan akhir maximal h+5 dari tgl kirim */
        var _hPlus5 = new Date(tglKirim);
        var _options_kirim = grup_farm == 'bdy' ? Permintaan._options_kirim_bdy : Permintaan._options_kirim;
        Permintaan.add_datepicker(_template.find('input[name=tgl_kirim]'), _options_kirim);
        Permintaan.add_datepicker(_template.find('input[name=tgl_keb_akhir]'), Permintaan._options_kebutuhan_akhir);

        _hPlus5.setDate(_hPlus5.getDate() + _umur_pakan_tmp);
        _template.find('input[name=tgl_keb_akhir]').datepicker('option', 'minDate', _newDate);
        _template.find('input[name=tgl_keb_akhir]').datepicker('option', 'maxDate', _hPlus5);

        /* min kirim adalah h-3 dari tgl keb awal */
        _tglMinKirim.setDate(_tglMinKirim.getDate() - Permintaan._min_kirim);
        /* jika tglKirim < tglMinKirim, set _tglMinKirim_tmp = tglKirim */
        var _tglMinKirim_tmp = (_tglKirimDate < _tglMinKirim) || _umur_pakan_tmp == this._max_kirim ? _tglKirimDate : _tglMinKirim;

        _template.find('input[name=tgl_kirim]').datepicker('option', 'maxDate', _kebAkhirDate);
        _template.find('input[name=tgl_kirim]').datepicker('option', 'minDate', _tglMinKirim_tmp);

        _template.data('max_umur_pakan', _umur_pakan_tmp);
    },
    disableDatepickerPP: function(_template) {
        _template.find('input.hasDatepicker').datepicker('disable');
    },
    enableDatepickerPP: function(_template) {
        _template.find('input.hasDatepicker').datepicker('enable');
    },

    /*fungsi untuk melakukan pengecekan apakah hari ini masih diperbolehkan buat / approve pp
     * tgl_keb_awal disini adalah h - 1 dari kebutuhan awal yang ditampilkan
     * */
    timeline_pp: function(tgl_keb_awal, grup_farm, _tglDocIn) {
        var result = { tglDO: null, tglMaxPPDate: null, minTglBuatPP: null, tglKirimDate: null, tglKirimDefault: null };
        var hari_libur = Permintaan.get_hari_libur();
        var _prevDate = new Date(tgl_keb_awal);
        var _forecastKebAwal = new Date(tgl_keb_awal);
        var _max_buat_do = grup_farm == 'bdy' ? this._max_buat_do_bdy : this._max_buat_do;
        var _min_kirim = grup_farm == 'bdy' ? this._min_kirim_bdy : this._min_kirim;
        var _max_kirim = grup_farm == 'bdy' ? this._max_kirim_bdy : this._max_kirim;
        var _umurAwal = parseInt(Config.get_selisih(new Date(_tglDocIn), _prevDate)) + 1;
        var _pengurangHari = Permintaan._pengurangHari;
        var _disableTglKirim = 0;
        /* set tgl max pembuatan pp */

        /* kebutuhan_akhir yang lama +1 adalah tgl kebutuhan awal */

        if (_umurAwal >= this._ppAkanPanen) {
            _min_kirim = this._minKirimAkanPanen;
            _max_kirim = this._maxKirimAkanPanen;
            _max_buat_do = this._maxBuatDoPanen;
            _disableTglKirim = 1;
        }

        _forecastKebAwal.setDate(_forecastKebAwal.getDate() + 1);
        var _tgl_kirim_forecast = Permintaan.get_tgl_kirim_forecast(Permintaan._varFlock, Config._convertTgl(Config._getDateStr(_forecastKebAwal)));

        _prevDate.setDate(_prevDate.getDate() - _max_kirim);

        var tglKirim = Config.cari_hari_kerja_terdekat(Config._getDateStr(_prevDate), hari_libur);

        if (!empty(_tgl_kirim_forecast)) {
            tglKirim = _tgl_kirim_forecast.tgl_kirim
        }
        var tglKirimDate = new Date(tglKirim);
        var _tglDO = new Date(tglKirim);
        /* DO adalah H-2 dari tglKirim */
        _tglDO.setDate(_tglDO.getDate() - _max_buat_do);
        var tglDO = Config.cari_hari_kerja_terdekat(Config._getDateStr(_tglDO), hari_libur);
        var _maxPP = new Date(tglDO);

        /* max PP adalah H-1 dari tglDO */
        _maxPP.setDate(_maxPP.getDate() - this._max_buat_pp);
        var tglMaxPP = Config._convertTgl(Config._getDateStr(_maxPP));
        var tglMaxPPDate = new Date(tglMaxPP);
        /* min PP H-2 dari maxPP */
        var _minPP = new Date(tglMaxPP);
        _minPP.setDate(_minPP.getDate() - this._min_buat_pp);
        var minTglBuatPP = new Date(Config._convertTgl(Config._getDateStr(_minPP)));

        var tglKirimDefault = new Date(tglKirim);

        tglKirimDefault.setDate(tglKirimDefault.getDate() - _min_kirim);
        result.tglDO = tglDO;
        result.tglMaxPPDate = tglMaxPPDate;
        result.minTglBuatPP = minTglBuatPP;
        result.tglKirimDate = tglKirimDate;
        result.tglKirimDefault = tglKirimDefault;
        result.disableTglKirim = _disableTglKirim;

        return result;
    },

    kumpulkan_data_pp: function(_aksi) {
        var _kodepj, _tglrhk, _rv = [],
            _dd = [],
            _tmp = {},
            _tmp_rv = {},
            _tglkebutuhan, _error = 0;
        /* kuantitasPPAsli adalah jumlah pp yang sesuai standart dikurangi hutang pp dan hutang retur sak
         * kuantitasPP adalah jumlah yang diorder, pembulatan dari kuantitasPPAsli */
        var _noreg, _kodekandangElm, _kuantitasPP, _kuantitasPerformance, _kuantitasForecast, _ckElm, _keterangan, _kuantitasPPAsli, _pengurangPP, _komposisiPakan;
        /* untuk menyimpan data review khusus budidaya */
        var _rekomendasiElm, _sisaGudang, _sisaKandang, _pakanFarmLain, _pengajuanUser = 0,
            _rekomendasiSistem = 0,
            _autoApprove = 0;
        _noreg = $('select[name=no_reg]').val();
        _tglrhk = $('#kebutuhan_pakan_internal table>tbody>tr>td.lhk').data('lhk_terakhir');
        $('#kebutuhan_pakan_internal table>tbody>tr').each(function() {
            _kodepj = $(this).data('kode_barang');
            _tglkebutuhan = $(this).find('td.tgl_kebutuhan').data('tgl_kebutuhan');
            _rekomendasiElm = $(this).find('td.rekomendasi_pp');
            _tmp_rv = { no_reg: _noreg, jml_optimasi: _rekomendasiElm.text(), kode_barang: _kodepj, tgl_kebutuhan: _tglkebutuhan };

            $(this).find('input,textarea').each(function() {
                _tmp_rv[$(this).attr('name')] = $(this).val();
            });

            _kuantitasForecast = _rekomendasiElm.data('forecast');
            _kuantitasPPAsli = _rekomendasiElm.data('jml_pp_asli');
            _pengurangPP = _rekomendasiElm.data('pengurang_pp');
            _komposisiPakan = _rekomendasiElm.data('komposisi');
            _kuantitasPP = _rekomendasiElm.text();
            /**rekomendasi sistem tidak bisa diedit */
            _rekomendasiSistem += parseInt(_kuantitasPP);
            /** jika  review oleh kadept maka kuatitasPP mengikuti jml review kadept */
            if ($(this).find('input[name=jml_review]').length) {
                _kuantitasPP = $(this).find('input[name=jml_review]').val();
                _pengajuanUser += parseInt(_kuantitasPP);
            }
            _tmp = { tk: _tglkebutuhan, rh: _tglrhk, kb: _kodepj, nr: _noreg, jf: _kuantitasForecast, od: _kuantitasPP, jo: _kuantitasPP, jk: 'C', oa: _kuantitasPPAsli, mp: _pengurangPP, kp: _komposisiPakan };
            _dd.push(_tmp);
            _rv.push(_tmp_rv);
        });

        var _dh = {};
        $('#transaksi form').find('input').not('input[name=scan_rfid],input[name=no_pp],input[name=flock],input[name=tgl_permintaan]').each(function() {
            if (!empty($(this).val())) {
                //if($(this).is('input')){
                _dh[$(this).attr('name')] = Config._tanggalDb($(this).val(), ' ');
                //}					
            } else {
                if ($(this).is('input')) {
                    _error++;
                    toastr.error($(this).attr('name') + ' harus diisi');
                }
            }
        });

        if (_rekomendasiSistem >= _pengajuanUser) {
            _autoApprove = 1;
        }
        return { _error: _error, _dh: _dh, _dd: _dd, _gf: 'bdy', _review: _rv, _aa: _autoApprove };
    },

    list_pp_farm: function() {
        /* id farm berdasarkan session yang sedang aktif */
        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/list_pp_farm',
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            success: function(data) {
                if (data.status) {
                    $('#daftarPermintaan .container>.col-md-12').html(data.content);
                }
            },
        });

    },
    /* pastikan semua kandang sudah input lhk */
    cek_input_lhk: function(flok) {
        var kodeflok = (flok === undefined) ? '' : flok;
        var _result = {};
        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/cek_input_lhk/' + kodeflok,
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            success: function(data) {
                if (data.status) {
                    if (!empty(data.content['belumInputRhk'])) {
                        _result['status'] = 0;
                        _result['message'] = data.content['belumInputRhk'].join(' <br >') + '<br >  LHK belum dientri';
                    } else if (!empty(data.content['belumAckRhk'])) {
                        _result['status'] = 0;
                        _result['message'] = data.content['belumAckRhk'].join('<br >') + '<br > Mohon lakukan ack LHK terlebih dahulu';
                    } else {
                        /* lhk sudah diinput semua dan sudah ack */
                        _result['status'] = 1;
                    }
                    /* cek pengembalian sak */
                    if (_result['status']) {
                        var _pesan = 'Mohon entri pengembalian sak kosong terlebih dahulu <br >';
                        $.ajax({
                            url: 'pengembalian_sak/pengembalian/check_pengembalian_hari_ini',
                            data: { flok: kodeflok },
                            success: function(data) {
                                if (!data.status) {
                                    _result['status'] = 0;
                                    _result['message'] = _pesan + data.content.join('<br >');
                                }
                            },
                            dataType: 'json',
                            async: false,
                        });
                    }
                }
            },
        });
        //	_result['status'] = 1;
        return _result;
    },
    cek_input_lhk_noreg: function(noreg) {
        var _result = {};
        $.ajax({
            url: 'permintaan_pakan_v3/permintaan_pakan/cek_input_lhk_noreg/',
            data: { no_reg: noreg },
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            success: function(data) {
                if (data.status) {
                    if (!empty(data.content['belumInputRhk'])) {
                        _result['status'] = 0;
                        _result['message'] = 'Mohon melakukan entry LHK terlebih dahulu';
                    } else if (!empty(data.content['belumAckRhk'])) {
                        _result['status'] = 0;
                        _result['message'] = 'Mohon melakukan cetak LHK terlebih dahulu';
                    } else {
                        /* lhk sudah diinput semua dan sudah ack */
                        _result['status'] = 1;
                    }
                    /* cek pengembalian sak */
                    if (_result['status']) {
                        var _pesan = 'Mohon mengentri pengembalian sak kosong terlebih dahulu <br >';
                        $.ajax({
                            url: 'pengembalian_sak/pengembalian/check_pengembalian_noreg_hari_ini',
                            data: { noreg: noreg },
                            success: function(data) {
                                if (!data.status) {
                                    _result['status'] = 0;
                                    _result['message'] = _pesan + data.content.join('<br >');
                                }
                            },
                            dataType: 'json',
                            async: false,
                        });
                    }
                }
            },
        });
        /** sementara buka dulu */
        //_result['status'] = 1;
        return _result;
    },

    /* tampilkan detail pp di pop up*/
    detail_pp_popup: function(elm) {
        var _no_pp = $(elm).data('no_pp');
        var _status = $(elm).data('status') || null;
        var _flok = $(elm).data('flok') || null;

        var url = 'home/home/view_pp?no_pp=' + _no_pp + '&status_pp=' + _status + '&flok=' + _flok;
        var w = screen.width - 300,
            h = 500;
        var left = (screen.width / 2) - (w / 2);
        var top = (screen.height / 2) - (h / 2);
        window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=" + top + ", left=" + left + ", width=" + w + ", height=" + h);
    },
    /* tampilkan pp yang sudah dibuat */
    list_pp_cari: function(elm) {
        var _tindak_lanjut = 0;
        if ($('input[name=tindak_lanjut]').length) {
            if ($('input[name=tindak_lanjut]').is(':checked')) {
                _tindak_lanjut = 1;
            }
        }

        var _tanggal = {};
        var _no_lpb = $('input[name=no_lpb]').val();
        var _farm = $('div[name=divFarm] select').val() || null;
        var _no_flok = $('select[name=flok]').val() || null;
        var _form = $(elm).closest('form');
        _tanggal['fieldname'] = $('select[name=tanggal_lpb]').val();
        var _tgl = $('input[name$=Date]');
        var _jmltgl = 0;
        if (_tgl.length) {
            _tgl.each(function() {
                if (!empty($(this).val())) {
                    _tanggal[$(this).attr('name')] = Config._tanggalDb($(this).val(), ' ', '-');
                    _jmltgl++;
                }
            });
            if (_jmltgl == 2) {
                _tanggal['operand'] = 'between';
            } else {
                if (_tanggal['startDate'] != undefined) {
                    _tanggal['operand'] = '>=';
                } else if (_tanggal['endDate'] != undefined) {
                    _tanggal['operand'] = '<=';
                }
            }

        }
        $.ajax({
            type: 'post',
            data: { _tindak_lanjut: _tindak_lanjut, _tanggal: _tanggal, _no_lpb: _no_lpb, _farm: _farm, _no_flok: _no_flok },
            url: 'permintaan_pakan_v3/permintaan_pakan/list_pp/ajax',
            dataType: 'html',
            async: false,
            success: function(data) {
                $('#daftar_pp_kafarm').html(data);
                /*
                
					
                $('#daftar_pp_kafarm').find('table').scrollabletable({
                	'max_width' : $('#daftar_pp_kafarm').innerWidth(),
                });*/
            },
        });
    },
    /* tampilkan pp yang sudah dibuat */
    list_monitoring_pp_cari: function(elm) {
        var _tanggal = {};
        var _form = $(elm).closest('form');
        var _periode_doc_in = _form.find('select[name=periode_doc_in]').val();
        var _form = $(elm).closest('form');
        var _kode_farm = $('#list_farm select');
        var _error = 0;

        if (_kode_farm.length) {
            if (empty(_kode_farm.val())) {
                _error++;
                toastr.error('Pilih salah satu farm terlebih dahulu');
            }
        }

        _tanggal['fieldname'] = $('select[name=tanggal_cari]').val();
        /* kumpulkan cekbox yang dipilih */
        _form.find(':checkbox:checked').each(function() {
            _status.push($(this).val());
        });
        var _tgl = $('input[name$=Date]');
        _tanggal['operand'] = null;
        var _jmltgl = 0;
        if (_tgl.length) {
            _tgl.each(function() {
                if (!empty($(this).val())) {
                    _tanggal[$(this).attr('name')] = Config._tanggalDb($(this).val(), ' ', '-');
                    _jmltgl++;
                }
            });
            if (_jmltgl == 2) {
                _tanggal['operand'] = 'between';
            } else {
                if (_tanggal['startDate'] != undefined) {
                    _tanggal['operand'] = '>=';
                } else if (_tanggal['endDate'] != undefined) {
                    _tanggal['operand'] = '<=';
                }
            }

        }
        if (!_error) {
            $.ajax({
                type: 'post',
                data: { _tanggal: _tanggal, _periode_doc_in: _periode_doc_in, kode_farm: _kode_farm.val() },
                url: 'permintaan_pakan_v3/permintaan_pakan/list_monitoring_pp',
                dataType: 'html',
                async: false,
                success: function(data) {
                    $('#div_monitroing_pp').html(data);
                },
            }).done(function(data) {
                $('#div_monitroing_pp table').scrollabletable({
                    'max_width': $('#div_monitroing_pp').innerWidth(),
                });

            });
        }

    },


    kirimemaillagi: function(url_email, content) {
        $.ajax({
            url: url_email + '/' + content,
            type: 'get',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                } else {
                    bootbox.confirm('Email gagal dikirim ( periksa koneksi internet anda ), apakah akan mengirim email lagi ?', function() {
                        var url_email = 'client/email/email_op';
                        Permintaan.kirimemaillagi(url_email, email.content);
                    });
                }
            },
        });
    },

    /* function khusus untuk budidaya */
    transaksi_pp_bdy: function(elm, target) {
        var _no_pp = $(elm).data('no_pp');
        var status = $(elm).data('status') || null;
        var _flok = $(elm).data('flok');
        var _lockpp = $(elm).data('lockpp');

        /** variabel ini nanti digunakan oleh function timeline_pp */
        Permintaan._varFlock = _flok;
        $.ajax({
            type: 'post',
            data: { no_pp: _no_pp, status: status },
            url: 'permintaan_pakan_v3/permintaan_pakan/transaksi_pp',
            dataType: 'html',
            async: false,
            success: function(data) {
                $('#main_pp').hide();
                $('#transaksi').show();
                $('#transaksi').html(data).promise().done(function() {
                    if (!empty(_no_pp)) {
                        var _noreg = $('select[name=no_reg]').val();
                        var _namaKandang = $('select[name=no_reg]').find('option:selected').text();
                        $('#transaksi form').find('input[name=scan_rfid]').val(_namaKandang);
                        $('#transaksi form').find('input[name=scan_rfid]').prop('readonly', 1);
                        var _tgl_keb_awal = new Date(Config._convertTgl(Config._tanggalDb($('#transaksi').find('input[name=tgl_keb_awal]').val(), ' ')));
                        var _tgl_buat_pp = Config._convertTgl(Config._tanggalDb($('#transaksi').find('input[name=tgl_permintaan]').val(), ' '));
                        _tgl_keb_awal.setDate(_tgl_keb_awal.getDate() - 1);
                        var _data_keb_awal = { tgl: Config._convertTgl(Config._getDateStr(_tgl_keb_awal)), name: '' };
                        if (!_lockpp) {
                            _tgl_buat_pp = null;
                        }
                        Permintaan.get_kebutuhan_awal_bdy(_lockpp, _noreg, _flok, _tgl_buat_pp, _data_keb_awal);
                    } else {
                        $('input[name=scan_rfid]').focus();
                    }
                });
            },
        });

    },
    buat_pp_bdy: function(elm) {
        var _error = 0;
        var flok = $(elm).find('option:selected').data('flok_bdy');
        var _noreg = $(elm).val();
        Permintaan._varFlock = flok;
        /* reset data pada tgl kebutuhan akhir dan tgl kirim */
        var _trFirst = $('#tabel_pp tbody tr:first');
        _trFirst.find('td.umur_pakan,td.kuantitas_pp').text('-');
        _trFirst.find('input').not('input[name=tgl_keb_awal]').val('');
        var _tglDocIn = Permintaan.get_tgl_doc_in_bdy(flok);
        if (empty(flok)) {
            toastr.error('Pilih kandang terlebih dahulu');
            _error++;
        }
        if (empty(_tglDocIn)) {
            toastr.error('Tanggal doc in tidak ditemukan');
            _error++;
        }
        if (!_error) {
            $(elm).closest('.form-group').find('input[name=flock]').val(flok);
            Permintaan.resetKebutuhanInternal();
            var r = Permintaan.cek_input_lhk_noreg(_noreg);
            $.when(r).done(function() {
                if (r.status) {
                    var _lockpp = 0;
                    Permintaan.get_kebutuhan_awal_bdy(_lockpp, _noreg, flok);
                } else {
                    toastr.error(r.message);
                }
            });

        }

    },
    get_kebutuhan_awal_bdy: function(_lockpp, noreg, flok, tanggal_buat_pp, _data_keb_awal, _pp_awal) {
        if (empty(tanggal_buat_pp)) {
            tanggal_buat_pp = $('#tanggal_server').data('tanggal_server');
        }
        /** jika sudah lockPP maka gak usah cek timeline */
        var _cekTimeline = _lockpp == undefined ? 1 : !_lockpp;
        var _umur_pakan, _max_kebutuhan_pakan;
        var _hari_ini = new Date(tanggal_buat_pp);
        var _error = 0;
        var _ppAwal = 0;
        var _tglDocIn = Permintaan.get_tgl_doc_in_bdy(flok);

        if (_data_keb_awal === undefined) {
            var _cek_pp = Permintaan.get_last_pp_noreg(noreg, flok);
        } else {
            var _cek_pp = _data_keb_awal;
        }

        if (_pp_awal !== undefined) {
            _cek_pp.name = 'doc_in';
            _ppAwal = 1;
        }

        if (!_error) {
            $.when(_cek_pp).done(function() {
                if (_cek_pp.ploting_pelaksana != undefined && !empty(_cek_pp.ploting_pelaksana)) {
                    toastr.error(_cek_pp.ploting_pelaksana);
                } else if (_cek_pp.pp_pending != undefined && _cek_pp.pp_pending) {
                    toastr.error('Permintaan pakan tidak dapat diajukan. Terdapat pengajuan belum di-approve');
                } else {
                    if (!empty(_cek_pp.tgl)) {
                        var _max_umur_pakan_awal = Permintaan._max_umur_pakan_awal;
                        var _max_umur_pakan = Permintaan._max_umur_pakan_bdy;
                        var hari_libur = Permintaan.get_hari_libur();
                        var _min_kirim = Permintaan._min_kirim_bdy;
                        var _max_kirim = Permintaan._max_kirim_bdy;
                        var _options_kirim = Permintaan._options_kirim_bdy;
                        var _options_kebutuhan_akhir = Permintaan._options_kebutuhan_akhir_bdy;
                        /* cari tanggal pembuatan DO, pembuatan OP
                         * semuanya tidak boleh hari libur
                         * tanggal kirim H-2 dari tanggal kebutuhan akhir
                         * DO adalah H-2 dari tanggal kirim
                         */
                        var tgl_keb_awal = Config._convertTgl(_cek_pp.tgl);
                        //	var _tglMinKirim = new Date(tgl_keb_awal);
                        var _prevDate = new Date(tgl_keb_awal);
                        var _nextDate = new Date(tgl_keb_awal);
                        var _jarak_pakan;
                        if (_cek_pp.name == 'doc_in') {
                            /* ini pp pertama kali boleh lebih dari 7 hari */
                            _max_kebutuhan_pakan = Permintaan._max_kebutuhan_pakan_awal_bdy;
                            //_nextDate.setDate(_nextDate.getDate() + 1); // kebutuhan awal adalah doc_in + 1
                            var timelinePP = Permintaan.timeline_pp(tgl_keb_awal, 'bdy', _tglDocIn);
                            _ppAwal = 1;
                        } else if (Permintaan.isPPAwalBdy(_tglDocIn, tgl_keb_awal, 0)) {
                            var _tmp_keb_awal = new Date(tgl_keb_awal);
                            //_tmp_keb_awal.setDate(_tmp_keb_awal.getDate() - 1);
                            var timelinePP = Permintaan.timeline_pp(Config._convertTgl(Config._getDateStr(_tmp_keb_awal)), 'bdy', _tglDocIn);
                            _ppAwal = 1;
                        } else {
                            _max_kebutuhan_pakan = Permintaan._max_kebutuhan_pakan;
                            var timelinePP = Permintaan.timeline_pp(tgl_keb_awal, 'bdy', _tglDocIn);
                        }
                        /* ambil timeline PP */
                        var tglDocIn = new Date(Permintaan.get_tgl_doc_in_bdy(flok));

                        _nextDate.setDate(_nextDate.getDate() + 1);
                        _prevDate.setDate(_prevDate.getDate() - _max_kirim);
                        /* kebutuhan akhir adalah +2 dari tgl kebutuhan awal */
                        var _hPlus5 = new Date(tgl_keb_awal);

                        var tglDO = timelinePP.tglDO;
                        var tglMaxPPDate = timelinePP.tglMaxPPDate;
                        var minTglBuatPP = timelinePP.minTglBuatPP;
                        var _tglKirimDate = timelinePP.tglKirimDate;
                        var _tglMinKirim = timelinePP.tglKirimDate;
                        var _disableTglKirim = timelinePP.disableTglKirim;

                        /* pp+7 < tglKirim */
                        var _syaratTerpenuhi = 0;
                        if (_cekTimeline) {
                            if ((tglMaxPPDate >= _hari_ini) && (minTglBuatPP <= _hari_ini)) {
                                _syaratTerpenuhi = 1;
                            } else {
                                toastr.warning('Pengajuan melebihi batas timeline. <br /> Timeline PP ' + Config._tanggalLocal(Config._getDateStr(minTglBuatPP), '-', ' ') + ' s/d ' + Config._tanggalLocal(Config._getDateStr(tglMaxPPDate), '-', ' ') + '<br /> Timeline DO ' + Config._tanggalLocal(tglDO, '-', ' '));
                                return;
                            }
                        } else {
                            _syaratTerpenuhi = 1;
                        }

                        if (_syaratTerpenuhi) {
                            _tglDocIn
                            var _umurAwal = parseInt(Config.get_selisih(new Date(_tglDocIn), _nextDate));
                            var _defKebAkhir;

                            if (_umurAwal >= Permintaan._ppAkanPanen) {
                                _min_kirim = Permintaan._minKirimAkanPanen;
                                _max_kirim = Permintaan._maxKirimAkanPanen;
                            }

                            /* min kirim adalah h-3 dari tgl keb awal */
                            _tglMinKirim.setDate(_tglMinKirim.getDate() - (_min_kirim - _max_kirim));

                            /* cari tanggal kirim dan kebutuhan akhir berdasarkan data pada forecast */
                            var _tgl_kirim_forecast = Permintaan.get_tgl_kirim_forecast(flok, Config._convertTgl(Config._getDateStr(_nextDate)));

                            /* jika tglKirim < tglMinKirim, set _tglMinKirim_tmp = tglKirim */
                            //var _tglMinKirim_tmp = (_tglKirimDate < _tglMinKirim) || (Config.is_hari_libur(Config._getDateStr(_tglMinKirim), hari_libur)) ? _tglKirimDate : _tglMinKirim;
                            var _tglMinKirim_tmp = Config.is_hari_libur(Config._getDateStr(_tglMinKirim), hari_libur) ? _tglKirimDate : _tglMinKirim;
                            var _docin = Permintaan.get_tgl_doc_in_bdy(flok);

                            if (Permintaan.isPPAwalBdy(_tglDocIn, tgl_keb_awal, _ppAwal)) {
                                _defKebAkhir = new Date(tgl_keb_awal);
                                _defKebAkhir.setDate(_defKebAkhir.getDate() + Permintaan._max_kebutuhan_pakan_awal_bdy); /* pp awal untuk 8 hari dari doc in */
                                //	$('textarea[name=keterangan]:first').val('Permintaan pakan pertama');
                                _hPlus5.setDate(_hPlus5.getDate() + 8);

                            } else if (_umurAwal <= Permintaan._max_kebutuhan_pakan_awal_bdy) {
                                // yang dikurangi adalah 8 dikarenakan _umurAwal dimulai dengan 1
                                var _tmp_max_pakan = (Permintaan._max_kebutuhan_pakan_awal_bdy + 1) - _umurAwal;
                                _max_kebutuhan_pakan = _tmp_max_pakan > _max_kebutuhan_pakan ? _tmp_max_pakan : _max_kebutuhan_pakan;
                                _hPlus5.setDate(_hPlus5.getDate() + _max_kebutuhan_pakan);
                                var _umurAkhirDate = Config.get_selisih(new Date(_docin), _hPlus5);
                                _defKebAkhir = new Date(Config._convertTgl(Config._getDateStr(_hPlus5)));

                            } else if (_umurAwal < Permintaan._umur_pakan_harian) {
                                _hPlus5.setDate(_hPlus5.getDate() + _max_kebutuhan_pakan);
                                var _umurAkhir = Config.get_selisih(new Date(_docin), _hPlus5);
                                if (_umurAkhir > Permintaan._umur_pakan_harian) {
                                    var _s = _umurAkhir - Permintaan._umur_pakan_harian;
                                    _hPlus5.setDate(_hPlus5.getDate() - _s);
                                    _max_kebutuhan_pakan = _max_kebutuhan_pakan - _s;
                                }
                                _defKebAkhir = new Date(Config._convertTgl(Config._getDateStr(_hPlus5)));
                            } else {
                                _max_kebutuhan_pakan = Permintaan._max_kebutuhan_pakan_19;
                                _hPlus5.setDate(_hPlus5.getDate() + _max_kebutuhan_pakan);
                                _defKebAkhir = new Date(Config._convertTgl(Config._getDateStr(_hPlus5)));
                            }
                            /* jika ditemukan tanggal kirim di forecast maka gunakan sebagai default */
                            if (!empty(_tgl_kirim_forecast)) {
                                var _f_tgl_kirim = new Date(_tgl_kirim_forecast['tgl_kirim']);
                                var _f_tgl_keb_akhir = new Date(_tgl_kirim_forecast['tgl_keb_akhir']);
                                _tglKirimDate = new Date(Config.cari_hari_kerja_terdekat(_tgl_kirim_forecast['tgl_kirim'], Permintaan.get_hari_libur()));
                                _tglMinKirim_tmp = _f_tgl_kirim < _tglMinKirim_tmp ? _f_tgl_kirim : _tglMinKirim_tmp;
                                _defKebAkhir = _f_tgl_keb_akhir;
                                //_hPlus5 = _f_tgl_keb_akhir > _hPlus5 ? _f_tgl_keb_akhir : _hPlus5;
                                /** paksa untuk selalu mengikuti forecast */
                                _hPlus5 = _f_tgl_keb_akhir;
                            }


                            /* periksa apakah tanggal kirim selanjutnya sama dengan tanggal kirim saat ini */
                            var _finalKebAkhir = new Date(Permintaan.tgl_kirim_selanjutnya(Config._convertTgl(Config._getDateStr(_tglKirimDate, '-')), Config._convertTgl(Config._getDateStr(_defKebAkhir, '-')), 'bdy', flok));
                            _defKebAkhir = _finalKebAkhir > _defKebAkhir ? _finalKebAkhir : _defKebAkhir;
                            _hPlus5 = _finalKebAkhir > _hPlus5 ? _finalKebAkhir : _hPlus5;

                            _tglKirimDate = new Date(Config.cari_hari_kerja_terdekat(Config._getDateStr(_tglKirimDate), Permintaan.get_hari_libur()));
                            var tglKirim = Config._getDateStr(_tglKirimDate);
                            /* tambahkan datepicker */
                            Permintaan.add_datepicker($('input[name=tgl_kirim]'), _options_kirim);
                            Permintaan.add_datepicker($('input[name=tgl_keb_akhir]'), _options_kebutuhan_akhir);
                            $('input[name=tgl_keb_akhir]:first').datepicker('option', 'minDate', new Date(_nextDate));
                            /* sementara di matikan dulu */
                            $('input[name=tgl_keb_akhir]:first').datepicker('option', 'maxDate', new Date(_hPlus5));

                            if (_disableTglKirim) {
                                $('input[name=tgl_kirim]:first').datepicker('disable');
                            } else {
                                $('input[name=tgl_kirim]:first').datepicker('enable');
                                $('input[name=tgl_kirim]:first').datepicker('option', 'maxDate', _tglKirimDate).datepicker('option', 'minDate', _tglMinKirim_tmp);
                            }

                            /** jika _data_keb_awal != undefined berarti tglkirim mengikuti data pp yang sudah tersimpan */
                            if (_data_keb_awal === undefined) {
                                $('input[name=tgl_keb_awal]').val(Config._tanggalLocal(Config._getDateStr(_nextDate), '-', ' '));
                                $('input[name=tgl_kirim]').val(Config._tanggalLocal(tglKirim, '-', ' '));
                                $('input[name=tgl_keb_akhir]').val(Config._tanggalLocal(Config._getDateStr(_defKebAkhir), '-', ' '));
                                _jarak_pakan = Config.get_selisih(_tglKirimDate, _defKebAkhir);
                            } else {
                                _jarak_pakan = Config.get_selisih(new Date(Config._convertTgl(Config._tanggalDb($('#transaksi input[name=tgl_kirim]').val(), ' '))), new Date($('#transaksi input[name=tgl_keb_akhir]').data('keb_akhir_lama')));
                            }

                            $('label.umur_pakan').text(_jarak_pakan);
                            $('input[name=tgl_keb_akhir]:first').trigger('change');
                        }
                    } else {
                        toastr.error('Belum ada rencana DOC In');
                    }
                }
            });
        }
    },
    /* tglKebutuhanAwal sebagai parameter selalu tgl kebutuhan awal - 1 */
    isPPAwalBdy: function(tglDocIn, tglKebutuhanAwal, ppAwal) {
        var _result = 0;
        if (ppAwal == undefined) {
            ppAwal = 0;
        }
        if (ppAwal) {
            _result = 1;
            return _result;
        }
        var _doc = new Date(tglDocIn);
        var _kebAwal = new Date(tglKebutuhanAwal);
        if (Config._convertTgl(Config._getDateStr(_doc)) == Config._convertTgl(Config._getDateStr(_kebAwal))) {
            _result = 1;
        }

        return _result;
    },

    /* periksa apakah tanggal kirim selanjutnya sama dengan tanggal kirim saat ini */
    tgl_kirim_selanjutnya: function(tgl_kirim, tgl_kebutuhan_akhir, grup_farm, flok) {
        var _keb_akhir = tgl_kebutuhan_akhir;
        var _ketemu = 0;
        var _selanjutnya, _tmp_tgl_kirim, _tgl_keb_awal_selanjutnya;
        var _tglDocIn = Permintaan.get_tgl_doc_in_bdy(flok)
        while (!_ketemu) {
            _tgl_keb_awal_selanjutnya = new Date(_keb_akhir);

            _selanjutnya = this.timeline_pp(Config._convertTgl(Config._getDateStr(_tgl_keb_awal_selanjutnya, '-')), grup_farm, _tglDocIn);
            _tmp_tgl_kirim = Config._convertTgl(Config._getDateStr(_selanjutnya.tglKirimDate, '-'));

            if (tgl_kirim != _tmp_tgl_kirim) {
                _ketemu = 1;
            } else {
                _tgl_keb_awal_selanjutnya.setDate(_tgl_keb_awal_selanjutnya.getDate() + 1);
                _keb_akhir = Config._convertTgl(Config._getDateStr(_tgl_keb_awal_selanjutnya, '-'));
                toastr.warning('Kebutuhan awal tanggal ' + Config._tanggalLocal(_keb_akhir, '-', ' ') + ' memiliki tanggal kirim yang sama dengan tanggal kebutuhan awal sebelumnya');
            }
        }
        return _keb_akhir;
    },
    get_tgl_kirim_forecast: function(kodeFlokBdy, tglkebAwal) {
        if (Permintaan._tgl_kirim_forecast[kodeFlokBdy] == undefined) {
            Permintaan._tgl_kirim_forecast[kodeFlokBdy] = {};
        }
        if (empty(this._tgl_kirim_forecast[kodeFlokBdy][tglkebAwal])) {
            $.ajax({
                data: { flok: kodeFlokBdy, kebutuhan_awal: tglkebAwal },
                type: 'post',
                dataType: 'json',
                url: 'permintaan_pakan_v3/permintaan_pakan/tanggal_kirim_forecast',
                success: function(data) {
                    if (data.status) {
                        Permintaan._tgl_kirim_forecast[kodeFlokBdy][tglkebAwal] = data.content;
                    } else {
                        Permintaan._tgl_kirim_forecast[kodeFlokBdy][tglkebAwal] = [];
                    }
                },
                async: false
            });
        }
        return this._tgl_kirim_forecast[kodeFlokBdy][tglkebAwal];
    },

    showDetailPPBudidaya: function(elm) {
        var _elmPP = $(elm).closest('tr');
        var _flok = _elmPP.data('flok');
        var _status = _elmPP.data('status');
        var _noPP = _elmPP.data('no_pp');
        var _baris_detail = $(elm).closest('tr').next('tr.detail_pp');
        var _grup_farm = 'bdy';
        //var _data = {no_lpb : _noPP, hitung_ulang : _hitung_ulang, _new : _new, _grup_farm : _grup_farm, status : _status };
        var _data = { no_lpb: _noPP, status: _status, _grup_farm: _grup_farm };
        if (empty(_baris_detail.html())) {
            $.ajax({
                url: 'permintaan_pakan_v3/permintaan_pakan/list_kebutuhan_pakan',
                data: _data,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        var _infoBtn = '<div><span data-no_pp="' + _noPP + '" onclick="Permintaan.showPerformaKandang(this)" class="btn btn-default">Informasi Kandang <i class="glyphicon glyphicon-chevron-right"></i></span><div class="div_performa_kandang new-line" style="width:1150px"></div></div>';
                        _baris_detail.html('<td colspan="13">' + _infoBtn + data.content.kebutuhan_internal + '</td>').promise().done(function() {
                            var _replaceElm;
                            _baris_detail.find('textarea,input').each(function() {
                                _replaceElm = '<span>' + $(this).val() + '</span>';
                                $(this).replaceWith(_replaceElm);
                            });
                            _baris_detail.find('span.btn.btn-default').click();
                        });
                        _baris_detail.show();
                    }
                },
            });
        } else {
            if (_baris_detail.is(':hidden')) {
                _baris_detail.show();
            } else {
                _baris_detail.hide();
            }
        }
        $(elm).toggleClass('glyphicon-plus-sign glyphicon-minus-sign');
    },
    approveKadiv: function(elm) {
        var _error = 0;
        /** pastikan sudah ada checkbox yang telah dipilih */
        var _terpilih = $('#daftar_pp_kafarm tbody').find(':checked');
        if (!_terpilih.length) {
            bootbox.alert("Belum ada PP yang dipilih");
            return;
        }
        if (this._prosesServer) {
            bootbox.alert("Masih menunggu response dari server");
            _error++;
            return;
        }
        if (!_error) {
            bootbox.confirm({
                title: 'Konfirmasi Perubahan',
                message: 'Apakah anda yakin akan melakukan approval permintaan kebutuhan pakan ?',
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
                callback: function(result) {
                    if (result) {
                        var _listPP = [];
                        _terpilih.each(function() {
                            _listPP.push($(this).val());
                        });
                        $.ajax({
                            type: 'post',
                            beforeSend: function() {
                                Permintaan._prosesServer = 1;
                            },
                            dataType: 'json',
                            data: { no_pp: _listPP },
                            url: 'permintaan_pakan_v3/permintaan_pakan/approve_pp_budidaya',
                            success: function(data) {
                                if (data.status) {
                                    toastr.success(data.message);
                                    /** hapus baris yang sudah direject */
                                    var _tr;
                                    _terpilih.each(function() {
                                        _tr = $(this).closest('tr');
                                        _tr.next('tr.detail_pp').remove();
                                        _tr.remove();
                                    });
                                } else {
                                    toastr.error(data.message);
                                }
                            },
                        }).done(function() {
                            Permintaan._prosesServer = 0;
                        });
                    }
                }
            });
        }
    },
    rejectKadiv: function(elm) {
        var _error = 0;
        /** pastikan sudah ada checkbox yang telah dipilih */
        var _terpilih = $('#daftar_pp_kafarm tbody').find(':checked');
        if (!_terpilih.length) {
            bootbox.alert("Belum ada PP yang dipilih");
            return;
        }
        var _content = ['<div class="dialog_reject">',
            '<div class="col-md-12">Mohon mengisi keterangan reject permintaan pakan (min. 10 karakter)</div>',
            '<div class="col-md-12">',
            '<textarea name="keterangan_reject" class="col-md-10" onblur="Permintaan.aktifkanBtn(this)"></textarea>',
            '</div>',
            '<div class="col-md-12 new-line">',
            '<div class="col-md-2">',
            '<div name="simpanRejectBtn" class="btn btn-default disabled" onclick="Permintaan.simpanRejectKadiv(this)">Simpan</div>',
            '</div>',
            '<div class="col-md-2">',
            '<div class="btn btn-default" onclick="bootbox.hideAll()">Batal</div>',
            '</div>',
            '</div>',
            '</div>'
        ];
        var _options = {
            title: 'Konfirmasi',
            message: _content.join(''),
            //	className : 'largeWidth',
        };

        bootbox.dialog(_options);

    },
    aktifkanBtn: function(elm) {
        var ini = $(elm);
        var _p = ini.closest('.dialog_reject');
        if ($.trim(ini.val()).length != 0) {
            _p.find('div[name=simpanRejectBtn]').removeClass('disabled');
        } else {
            _p.find('div[name=simpanRejectBtn]').addClass('disabled');
        }
    },
    simpanRejectKadiv: function(elm) {
        var ini = $(elm);
        var _error = 0;
        var _p = ini.closest('.dialog_reject');
        var _ket = $.trim(_p.find('textarea[name=keterangan_reject]').val());
        if (_ket.length == 0) {
            _error++;
            toastr.error('keterangan harus diisi');
        }
        if (!_error) {
            var _terpilih = $('#daftar_pp_kafarm tbody').find(':checked');
            var _listPP = [];
            _terpilih.each(function() {
                _listPP.push($(this).val());
            });
            if (!empty(_listPP)) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    data: { no_pp: _listPP, ket: _ket },
                    url: 'permintaan_pakan_v3/permintaan_pakan/reject_pp_kadiv',
                    success: function(data) {
                        if (data.status) {
                            bootbox.hideAll();
                            toastr.success(data.message);
                            /** hapus baris yang sudah direject */
                            var _tr;
                            _terpilih.each(function() {
                                _tr = $(this).closest('tr');
                                _tr.next('tr.detail_pp').remove();
                                _tr.remove();
                            });
                        } else {
                            toastr.error(data.message);
                        }
                    },
                });
            }
        }
    },
    pilihPakanTambahan: function(elm) {
        var _tr = $(elm);
        _tr.closest('tbody').find('tr.terpilih').removeClass('terpilih');
        if (_tr.hasClass('pakan_tambahan')) {
            var _kb = _tr.data('kode_barang');
            /* yang bisa dihapus adalah baris awal atau akhir saja */
            var _semua_baris_kb = _tr.closest('tbody').find('tr[data-kode_barang="' + _kb + '"]');
            var _semua_baris_kb_length = _semua_baris_kb.length;
            var _ketemu = 0;
            if (_semua_baris_kb_length <= 2) {
                _tr.addClass('terpilih');
                $('#btnHapusPakan').removeAttr('disabled');
            } else {
                var _index_tr_dipilih = _tr.index();
                var _index_tmp, _index_akhir, _index_awal;
                _semua_baris_kb.each(function() {
                    _index_tmp = $(this).index();
                    if (!_index_awal) {
                        _index_akhir = _index_tmp + _semua_baris_kb_length - 1;
                        _index_awal = _index_tmp;
                    }
                    if (in_array(_index_tr_dipilih, [_index_awal, _index_akhir])) {
                        _tr.addClass('terpilih');
                        $('#btnHapusPakan').removeAttr('disabled');
                        _ketemu = 1;
                        return false;
                    }
                });
                if (!_ketemu) {
                    bootbox.alert('Bukan pakan tambahan pada tanggal kebutuhan awal atau tanggal kebutuhan akhir, tidak bisa dihapus');
                    $('#btnHapusPakan').attr('disabled', 1);
                }
            }
        } else {
            bootbox.alert('Bukan pakan tambahan, tidak bisa dihapus');
            $('#btnHapusPakan').attr('disabled', 1);
        }
    },
    hapusTambahPakan: function(elm) {
        var _tr = $('#kebutuhan_pakan_internal').find('table>tbody>tr.terpilih');
        if (!_tr.length) {
            bootbox.alert('Tidak ada baris pakan tambahan yang dipilih');
            return;
        }
        var _tanggal = _tr.first().find('td.tgl_kebutuhan').text();
        bootbox.confirm({
            title: 'Konfirmasi Hapus Pakan',
            message: 'Apakah anda yakin akan membatalkan penambahan pakan pada tanggal ' + _tanggal + ' ? ',
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
            callback: function(result) {
                if (result) {
                    var _kb = _tr.data('kode_barang');
                    var _textarea = _tr.closest('tbody').find('tr[data-kode_barang="' + _kb + '"]').find('textarea');
                    var _semua_baris_kb = _tr.closest('tbody').find('tr[data-kode_barang="' + _kb + '"]').not(_tr);
                    var _awalGanti = _semua_baris_kb.first().find('td.tgl_kebutuhan').data('tgl_kebutuhan');
                    var _akhirTambah = _semua_baris_kb.last().find('td.tgl_kebutuhan').data('tgl_kebutuhan');
                    var _awalGantiStr = _semua_baris_kb.first().find('td.tgl_kebutuhan').text();
                    var _akhirTambahStr = _semua_baris_kb.last().find('td.tgl_kebutuhan').text();
                    var _defaultValue = {},
                        _tgl_kebutuhan;
                    _semua_baris_kb.each(function() {
                        _tgl_kebutuhan = $(this).find('td.tgl_kebutuhan').data('tgl_kebutuhan');
                        _defaultValue[_tgl_kebutuhan] = {};
                        $(this).find('input').each(function() {
                            _defaultValue[_tgl_kebutuhan][$(this).attr('name')] = $(this).val();
                        });
                    });
                    _textarea.each(function() {
                        if (_defaultValue[_awalGanti] !== undefined) {
                            _defaultValue[_awalGanti][$(this).attr('name')] = $(this).val();
                        }
                    });
                    _tr.closest('tbody').find('tr[data-kode_barang="' + _kb + '"]').remove();
                    /** jika baris terakhir maka hapus saja, gak perlu set pakan tambahan */
                    if (_semua_baris_kb.length) {
                        Permintaan.set_pakan_tambahan(elm, _awalGantiStr, _akhirTambahStr, _kb, _defaultValue);
                    }
                    $(elm).attr('disabled', 1);
                }
            }
        });
    },

    exportExcel: function(elm) {
        var idtabel = $(elm).data('idtabel');
        var _sheet = 'Penerimaan pakan';
        if ($('#' + idtabel).length) {
            export_table(idtabel, null, 'Monitoring PP ', _sheet);
        }
    },
    summaryEntry: function(elm, target) {
        var _name = $(elm).attr('name');
        var _t = $(elm).closest('tbody');
        var _jml = 0;
        _t.find('input[name=' + _name + ']').each(function() {
            if (!empty($.trim($(this).val()))) {
                _jml += parse_number($(this).val(), '.', ',');
            } else {
                _jml += parse_number($(this).closest('td').prev().find('input').val(), '.', ',');
            }
        });
        _t.closest('tr').prev().find('td.' + target).text(_jml);
    },

    date2str: function(x, y) {
        var z = {
            M: x.getMonth() + 1,
            d: x.getDate(),
            h: x.getHours(),
            m: x.getMinutes(),
            s: x.getSeconds()
        };
        y = y.replace(/(M+|d+|h+|m+|s+)/g, function(v) {
            return ((v.length > 1 ? "0" : "") + eval('z.' + v.slice(-1))).slice(-2)
        });

        return y.replace(/(y+)/g, function(v) {
            return x.getFullYear().toString().slice(-v.length)
        });
    },

    overrideNilaiConfig: function(idFarm) {
        var _url = 'api/general/config';
        var _data = { context: 'PP', kodefarm: idFarm };
        $.get(_url, _data, function(data) {
            for (var i in data.content) {
                Permintaan[data.content[i]['kode_config']] = data.content[i]['value'];
            }
        }, 'json');
    },

    enableFilterPP: function(elm) {
        var _f = $(elm).closest('form');
        if ($(elm).is(':checked')) {
            _f.find('input[name=no_lpb]').val('');
            _f.find('input[name=no_lpb]').prop('readonly', 1);
            _f.find('select[name=tanggal_lpb]').prop('disable', 1);
            _f.find('input.hasDatepicker').datepicker('option', 'disabled', 1);
            $('#div_permintaan_pakan').find('#span_cari_pp').click();
        } else {
            _f.find('input[name=no_lpb]').prop('readonly', 0);
            _f.find('select[name=tanggal_lpb]').prop('disable', 0);
            _f.find('input.hasDatepicker').datepicker('option', 'disabled', 0);
        }
    },

    checkAllkadiv: function(elm) {
        var _table = $(elm).closest('tr').closest('table');
        var _tbody = _table.find('tbody');
        var _pilih = $(elm).is(':checked') ? 1 : 0;
        _tbody.find(':checkbox').prop('checked', _pilih);
    },
    cariKandang: function(elm) {
        var _rfID = $(elm).val();
        $.ajax({
            type: "POST",
            url: "api/general/kandang",
            data: {
                rfid: _rfID
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    $('select[name=no_reg]').val(data.content.no_reg);
                    $('select[name=no_reg]').trigger('change');
                    var _namaKandang = $('select[name=no_reg]').find('option:selected').text();
                    $(elm).val(_namaKandang);
                } else {
                    toastr.warning('Data rfid tidak ditemukan', 'Gagal');
                    $(elm).val('');
                }
            }
        });
    },

    fingerprint: function(_noreg, _level) {
        var _ini = this;
        _ini._completeFinger = 0;
        this.simpan_transaksi_verifikasi(function(result) {
            bootbox.hideAll();
            if (result.date_transaction) {
                var _convertLevel = { 'PENGAWAS': 'Pengawas', 'KAFARM': 'Kepala Farm / Koordinator Pengawas' };
                var _message = '<div><p data-kode-pegawai=""></p><p>Silakan Scan Fingerprint ' + _convertLevel[_level] + ' untuk Verifikasi</p></div>';
                var box = bootbox.dialog({
                    message: _message,
                    closeButton: false,
                    title: "Fingerprint",
                    buttons: {
                        success: {
                            label: "Batal",
                            className: "btn-danger",
                            callback: function() {
                                _ini.timer = false;
                                return true;
                            }
                        }
                    }
                });

                box.bind('shown.bs.modal', function() {
                    /** ambil data dari eabsensi data fingernya */
                    _ini._timerAbsensi = true;
                    _ini.set_fingerprint_absensi(result.date_transaction);
                    _ini.timer = true;
                    _ini.cek_verifikasi(result.date_transaction, _noreg, _level);
                });
            }
        });
    },

    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "fingerprint/fingerprint/simpan_transaksi_verifikasi",
            data: {
                transaction: 'rilis_pp',
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    cek_verifikasi: function(date_transaction, _noreg, _level) {
        if (this.timer == true) {
            var _ini = this;
            var _result = { result: 0 };
            $.ajax({
                type: "POST",
                url: "fingerprint/fingerprint/cek_verifikasi",
                data: {
                    date_transaction: date_transaction,
                    noreg: _noreg,
                    level: _level
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        _ini.timer = false;
                        if (data.match) {
                            _ini._timerAbsensi = false;
                            bootbox.hideAll();
                            toastr.success('Verifikasi fingerprint berhasil.', 'Berhasil');
                            var _nextLevel = _ini._levelFingerPrintRilisPP[_level];
                            if (!empty(_nextLevel)) {
                                _ini.fingerprint(_noreg, _nextLevel);
                            } else {
                                _ini.save_server();
                            }

                        } else {
                            var _convertLevel = { 'PENGAWAS': 'Pengawas', 'KAFARM': 'Kepala Farm / Koordinator Pengawas' };
                            var _pesanFinger = '<div class="text-cente"><i class="glyphicon glyphicon-remove-sign"></i> Data user ' + _convertLevel[_level] + ' tidak ditemukan mohon melakukan scan fingerprint ulang.</div>';
                            bootbox.alert(_pesanFinger, function() {
                                _ini.fingerprint(_noreg, _level);
                            });
                        }
                    } else {
                        _ini.timer = true;
                        setTimeout("Permintaan.cek_verifikasi('" + date_transaction + "','" + _noreg + "','" + _level + "')", 1000);
                    }
                }
            });
        }
    },
    /** menggantikan fingerprint delphi */
    set_fingerprint_absensi: function(date_transaction) {
        if (this._timerAbsensi == true) {
            var _ini = this;
            var _result = { result: 0 };
            $.ajax({
                type: "POST",
                url: "api/general/setFingerprint",
                data: {
                    date_transaction: date_transaction
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        _ini._timerAbsensi = false;
                    } else {
                        _ini._timerAbsensi = true;
                        setTimeout("Permintaan.set_fingerprint_absensi('" + date_transaction + "')", 1000);
                    }
                }
            });
        }
    },

    showHideFloatingArrow: function() {
        var _hs = this.hasScroll('daftar_pp_kafarm', 'horizontal');
        if (_hs) {
            $('.tu-float-btn').show();
        } else {
            $('.tu-float-btn').hide();
        }
    },

    hasScroll: function(el, direction) {
        direction = (direction === 'vertical') ? 'scrollTop' : 'scrollLeft';
        var result = !!el[direction];
        var ws = document.getElementById(el).scrollWidth;
        var wi = $('#' + el).innerWidth();

        result = ws > wi ? 0 : 1;
        return result;
    },
    /*
        setMaxInput: function(elm) {
            var _i = $(elm).val();

        }*/
};