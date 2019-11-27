'use strict';

var EntriLHK = {
    _keteranganTimeline: null,
    _levelFingerPrintLHK: { 'OPERATOR': 'PENGAWAS', 'PENGAWAS': null },
    _stepEntry: {},
    enableSave: function() {
        var enable = true,
            _min, _val;
        $('[data-mandatory=1]').each(function() {
            _min = $(this).data('min') || 0;
            _val = $.trim($(this).val());
            if (!_val.length) {
                enable = false;
                return false;
            } else {
                if (_val < _min) {
                    enable = false;
                    return false;
                }
            }
        });

        $('button#btnSimpan').prop("disabled", !enable);
    },
    validatorMaxPP: function(elm) {
        //kembalikan nilai ke max permintaan apabila inputan melebihi nilai maksimum
        var max = parseInt(elm.getAttribute("max")) || 0;
        var inp_rekomendasi_permintaan = $(elm).val();
        if (inp_rekomendasi_permintaan > max)
            $(elm).val(max);
    },
    validatorMaxPakai: function(elm) {
        //kembalikan nilai ke max permintaan apabila inputan melebihi nilai maksimum
        var max = $(elm).data("max") || 0;
        var inp_pakai = $(elm).val();
        if (inp_pakai > max) {
            $(elm).val(max);
        }
        this.updateRekomendasiKebutuhan();

    },
    validatorMaxPengurang: function(elm) {
        //kembalikan nilai ke max permintaan apabila inputan melebihi nilai maksimum
        var max = $(elm).data("max") || 0;
        var _inps = $(elm).closest('tr').find('input');
        var _total_pengurang = 0;
        var _nilai_sendiri = parseInt($(elm).val() || 0);
        _inps.each(function() {
            _total_pengurang += parseInt($(this).val() || 0);
        });

        if (_total_pengurang > max) {
            var _selisih = _total_pengurang - max;
            $(elm).val(_nilai_sendiri - _selisih);
        }
        this.updateRekomendasiKebutuhan();

    },
    calcBBRata2: function(elm) {
        var jumlah_sekat = $(elm).closest('tr').find('td.td_jumlah_sekat > input').val() || 0;
        var bb_sekat = $(elm).closest('tr').find('td.td_bb_sekat > input').val() || 0;
        var bb_rata2_sekat = bb_sekat / jumlah_sekat;

        $(elm).closest('tr').find('td.td_bb_rata_sekat > input').val(bb_rata2_sekat);
    },
    updateRekomendasiKebutuhan: function() {
        var _pengurang = 0;
        $('#lhk_populasi').find('input').each(function() {
            _pengurang += parseInt($(this).val() || 0);
        });
        var _rekomendasi_td, _jmlAyam, _standartKebutuhan, _stokPakan, _pakanRekomendasi, _pakanPakai, _sisaPakan, _kodeBarang;
        $('#lhk_permintaan_kandang tbody>tr>td.td_rekomendasi_kebutuhan').each(function() {
            _kodeBarang = $(this).closest('tr').find('td.td_kode_barang').data('kode_barang');
            _jmlAyam = $(this).data('jumlahayam') - _pengurang;
            _standartKebutuhan = $(this).data('standartkebutuhan');
            _stokPakan = $(this).data('stokpakan');
            _pakanPakai = $('#lhk_pakan tbody>tr>td[data-kode_barang=' + _kodeBarang + ']').closest('tr').find('input').val() || 0;
            _sisaPakan = _stokPakan > _pakanPakai ? _stokPakan - _pakanPakai : 0;

            _pakanRekomendasi = Math.ceil((_jmlAyam * _standartKebutuhan) / 50000) - _sisaPakan;
            if (_pakanRekomendasi < 0) {
                _pakanRekomendasi = 0;
            }
            $(this).text(_pakanRekomendasi);
        });

    },
    selectedFile: function(elm) {
        var input = $(elm);
        var files = input.get(0).files;
        var label = input.val();
        $(elm).parent().closest('span.input-group-btn').next().val(label);
    },
    refresh_page: function() {
        $('#entri_lhk_step').steps({
            onChange: function(i, newIndex, stepDirectionF) {
                if (stepDirectionF == 'forward') {
                    var _href = $('#entri_lhk_step').find('li').eq(i).find('a').attr('href');
                    /** periksa apakah sudah entry data-mandatory=1 */
                    var _complete = 1;
                    $(_href).find('[data-mandatory=1]').each(function() {
                        if ($(this).val() == '') {
                            _complete = 0;
                            return false;
                        }
                    });

                    return _complete;
                }
                return true;
            },
            //onFinish: function () { alert('Wizard Completed'); }
        });

        $(".step-content").css("border", "0");
        var html = 'Silahkan scan barcode form LHK ';
        var warning = '<span class="wait_loading hide" style="padding:2px;">Loading...</span><i><span class="text-danger hide">LHK sudah pernah di entri</span></i>';
        html = html + '<input id="scan_form_lhk"  onchange="EntriLHK.checkBarcode(this)" type="text" class="form-control" />' + warning;
        bootbox.dialog({
            closeButton: false,
            className: 'titleCenter',
            message: html,
        }).bind('shown.bs.modal', function() {
            $('#scan_form_lhk').focus();
        });
    },

    checkBarcode: function(elm) {
        var _nilai = $.trim($(elm).val());
        if (!empty(_nilai)) {
            var url = 'riwayat_harian_kandang/riwayat_harian_kandang/check_scan_LHK';
            $.ajax({
                url: url,
                data: {
                    barcode: _nilai,
                },
                dataType: 'json',
                type: 'POST',
                beforeSend: function() {
                    $('span.wait_loading').removeClass('hide');
                },
                async: false,
                success: function(dataObj) {
                    $('span.wait_loading').addClass('hide');
                    $('span.text-danger').addClass('hide');
                    if (dataObj.status) {
                        bootbox.hideAll();

                        //rubah data header sesuai dengan pencarian data pada saat scan LHK
                        var no_reg = dataObj.content['NO_REG'];
                        var kandang = dataObj.content['KODE_KANDANG'];
                        var flok = dataObj.content['FLOK_BDY'];
                        var formatted_tgl_doc_in = dataObj.content['format_tgl_doc_in'];
                        var tgl_doc_in = dataObj.content['TGL_DOC_IN'];
                        var formatted_tgl_lhk = dataObj.content['format_tgl_lhk'];
                        var tgl_lhk = dataObj.content['tgl_lhk'];
                        var umur = dataObj.content['umur'];

                        $('button#btnSimpan').data('no_reg', no_reg);
                        $('#inp_kandang').val(kandang);
                        $('#inp_flock').val(flok);
                        $('#inp_doc_in').val(formatted_tgl_doc_in);
                        $('#inp_tgl_lhk').val(formatted_tgl_lhk);
                        $('#inp_tgl_lhk').data('tgltransaksi', tgl_lhk);
                        $('#inp_umur').val(umur);

                        var url = 'riwayat_harian_kandang/riwayat_harian_kandang/ajax_entri_step';
                        $.ajax({
                            url: url,
                            data: {
                                no_reg: no_reg,
                                kandang: kandang,
                                flok: flok,
                                tgl_doc_in: tgl_doc_in,
                                tgl_lhk: tgl_lhk,
                                umur: umur,
                            },
                            dataType: 'html',
                            type: 'POST',
                            async: false,
                            beforeSend: function() {
                                $('.step-content').html('Loading ......');
                            },
                            success: function(data) {
                                $('.step-content').html(data).promise().done(function() {
                                    $('.inp-numeric').numeric({});
                                    $('[data-mandatory=1]').change(function() {
                                        EntriLHK.enableSave();
                                    });
                                });
                                $('#step1').addClass('active');
                            }
                        });

                    } else {
                        $('span.text-danger').text(dataObj.message);
                        $('span.text-danger').removeClass('hide');
                    }
                }
            });
        }
    },
    akanSimpan: function(elm) {
        var tgltransaksi = $('#inp_tgl_lhk').data('tgltransaksi');
        var _t = this.checkTerlambat(tgltransaksi);
        $.when(_t).done(function() {
            EntriLHK._keteranganTimeline = null;
            if (_t.content) {
                if (_t.block) {
                    //bootbox.alert('Entry LHK hanya bisa dilakukan max jam 09:00');
                    bootbox.alert(_t.message);
                    return false;
                }
                var _content = ['<div class="dialog_reject text-center">',
                    '<div class="col-md-12">Terjadi keterlambatan entry LHK, mohon isi alasan :</div>',
                    '<div class="col-md-12">',
                    '<textarea name="keterangan_timeline" class="col-md-10 form-control"></textarea>',
                    '</div>',
                    '</div>'
                ];
                var _options = {
                    title: 'Terlambat Entry LHK',
                    closeButton: false,
                    message: _content.join(''),
                    buttons: {
                        'OK': {
                            label: 'Simpan',
                            className: 'btn-default text-center',
                            callback: function(e) {
                                var _ket = $.trim($(e.target).closest('.bootbox').find('textarea').val());
                                if (_ket.length < 10 || _ket.length > 60) {
                                    bootbox.alert('Keterangan minimal 10 karakter maksimal 60 karakter');
                                    return false;
                                } else {
                                    EntriLHK._keteranganTimeline = _ket;
                                }
                            }
                        }
                    },
                };
                bootbox.dialog(_options).bind('hidden.bs.modal', function() {
                    EntriLHK.executeSave();
                });
            } else {
                EntriLHK.executeSave();
            }

        });
    },
    checkTerlambat: function(_tglLhk) {
        /* 1 jika terlambat, 0 jika tidak */
        var _result = {};
        $.ajax({
            url: 'riwayat_harian_kandang/riwayat_harian_kandang/checkTimeline',
            dataType: 'json',
            data: { tglTransaksi: _tglLhk },
            async: false,
            success: function(data) {
                if (data.status) {
                    _result = data;
                }
            }
        });
        return _result;
    },
    save: function() {
        var data_lhk = {};
        var detail_lhk_penimbangan_sekat = {};
        var detail_lhk_pakan = {};
        var detail_lhk_permintaan_kandang = [];
        var detail_lhk_penimbangan_sekat_arr = [];
        var detail_lhk_pakan_arr = [];
        var pengurangan_mati = $('#lhk_populasi > tbody > tr').find('.td_pengurangan_mati > input').val();
        var jumlah_ayam_awal = $('#lhk_populasi > tbody > tr').find('.td_pengurangan_mati > input').data('max');
        var pengurangan_afkir = $('#lhk_populasi > tbody > tr').find('.td_pengurangan_afkir > input').val();
        var noreg = $('button#btnSimpan').data('no_reg');
        var c_berat_badan = null;
        var tgltransaksi = $('input[name^=tglLHK]').data('tgltransaksi');
        data_lhk['no_reg'] = noreg;

        data_lhk['tgl_transaksi'] = tgltransaksi;
        data_lhk['c_mati'] = pengurangan_mati;
        data_lhk['c_afkir'] = pengurangan_afkir;

        var _total_berat = 0,
            _total_jml = 0;
        $('table#lhk_penimbangan_sekat > tbody >tr').each(function() {
            var id_sekat = $(this).find('.td_id_sekat').data('sekat');
            var jumlah_sekat = $(this).find('.td_jumlah_sekat > input').val();
            var berat_badan_sekat = $(this).find('.td_bb_sekat > input').val();
            var keterangan_sekat = $(this).find('.td_keterangan_sekat > input').val();
            detail_lhk_penimbangan_sekat = {
                'no_reg': noreg,
                'tgl_transaksi': tgltransaksi,
                'jenis_kelamin': 'C',
                'berat': berat_badan_sekat,
                'jumlah': jumlah_sekat,
                'sekat': id_sekat,
                'keterangan': keterangan_sekat,
            };
            _total_berat += parseInt(berat_badan_sekat);
            _total_jml += parseInt(jumlah_sekat);
            detail_lhk_penimbangan_sekat_arr.push(detail_lhk_penimbangan_sekat);
        });

        if (_total_berat > 0) {
            c_berat_badan = (_total_berat / _total_jml) / 1000;
        }
        $('table#lhk_permintaan_kandang > tbody >  tr').each(function() {
            var kode_pakan = $(this).find('.td_kode_barang').data('kode_barang');
            var tanggal_kebutuhan = $(this).find('.td_tgl_kebutuhan').data('tglkebutuhan');
            var rekomendasi_kebutuhan = $(this).find('.td_rekomendasi_kebutuhan').text();
            var rekomendasi_permintaan = $(this).find('.td_rekomendasi_permintaan > input').val();
            detail_lhk_permintaan_kandang.push({
                'no_reg': noreg,
                'tgl_kebutuhan': tanggal_kebutuhan,
                'kode_barang': kode_pakan,
                'jml_rekomendasi': rekomendasi_kebutuhan,
                'jml_permintaan': rekomendasi_permintaan,
            });
        });

        $('table#lhk_pakan > tbody >  tr').each(function() {
            var kode_pakan = $(this).find('.td_nama_pakan').data('kode_barang');
            var sak_pakai = $(this).find('.td_sak_pakai > input').val();
            detail_lhk_pakan = {
                'no_reg': noreg,
                'tgl_transaksi': tgltransaksi,
                'jenis_kelamin': 'C',
                'kode_barang': kode_pakan,
                'jml_pakai': sak_pakai
            };
            detail_lhk_pakan_arr.push(detail_lhk_pakan);
        });
        if (!empty(c_berat_badan)) {
            data_lhk['c_berat_badan'] = c_berat_badan;
        }
        var data_params = {
            noreg: noreg,
            tgl_lhk: tgltransaksi,
            umur: $('input[name^=umur]').val(),
            lhk: data_lhk,
            timbang_sekat: detail_lhk_penimbangan_sekat_arr,
            lhk_pakan: detail_lhk_pakan_arr,
            rekomendasi_pakan: detail_lhk_permintaan_kandang,
            keterangan_timeline: EntriLHK._keteranganTimeline,
            jumlah_ayam_awal: jumlah_ayam_awal
        }
        var formData = new FormData();
        var attachment = $('#lhkfileupload').get(0).files[0];
        formData.append("attachment", attachment);
        formData.append("data", JSON.stringify(data_params));

        var _url = 'riwayat_harian_kandang/riwayat_harian_kandang/simpan_lhk';
        $.ajax({
            url: _url,
            data: formData,
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                bootbox.dialog({
                    message: "Sedang proses simpan..."
                });
            },
            success: function(data) {
                bootbox.hideAll();
                if (data.status) {
                    bootbox.alert(data.message);
                    $('button#btnSimpan').prop("disabled", 1);
                    $('input').prop('readonly', 1);
                    if (data.generate_order != undefined) {
                        if (data.generate_order.status) {
                            /**generate nomer order */
                            var _kodeFlok = data.generate_order.flok;
                            var _kodeFarm = data.generate_order.kode_farm;
                            var _tglKebutuhan = data.generate_order.tgl_kebutuhan;
                            var _urlGenerate = 'riwayat_harian_kandang/riwayat_harian_kandang/generateOrder/' + _kodeFlok + '/' + _kodeFarm + '/' + _tglKebutuhan;
                            $.ajax({
                                url: _urlGenerate,
                                beforeSend: function() {
                                    bootbox.dialog({
                                        message: "Sedang proses generate order kandang ....."
                                    });
                                },
                                dataType: 'json',
                                async: false,
                                success: function(data) {
                                    bootbox.hideAll();
                                    bootbox.alert(data.message);
                                }
                            })
                        }
                    }
                } else {
                    bootbox.alert(data.message);
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
                var _convertLevel = { 'OPERATOR': 'Opreator', 'PENGAWAS': 'Pengawas' };
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
                transaction: 'entry_lhk',
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
                            bootbox.hideAll();
                            toastr.success('Verifikasi fingerprint berhasil.', 'Berhasil');
                            var _nextLevel = _ini._levelFingerPrintLHK[_level];
                            if (!empty(_nextLevel)) {
                                _ini.fingerprint(_noreg, _nextLevel);
                            } else {
                                _ini.save();
                            }

                        } else {
                            var _convertLevel = { 'PENGAWAS': 'Pengawas', 'OPERATOR': 'Operator' };
                            var _pesanFinger = '<div class="text-cente"><i class="glyphicon glyphicon-remove-sign"></i> Data user ' + _convertLevel[_level] + ' tidak ditemukan mohon melakukan scan fingerprint ulang.</div>';
                            bootbox.alert(_pesanFinger, function() {
                                _ini.fingerprint(_noreg, _level);
                            });
                        }
                    } else {
                        _ini.timer = true;
                        setTimeout("EntriLHK.cek_verifikasi('" + date_transaction + "','" + _noreg + "','" + _level + "')", 1000);
                    }
                }
            });
        }
    },

    executeSave: function(formData) {
        var _ini = this;
        var noreg = $('button#btnSimpan').data('no_reg');
        bootbox.confirm({
            title: 'Konfirmasi Perubahan',
            message: 'Apakah anda yakin akan melakukan penyimpanan data ?',
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
                    //_ini.fingerprint(noreg, 'OPERATOR');
                    _ini.fingerprint(noreg, 'PENGAWAS');
                }
            }
        });
    },
    readFileUpload: function() {
        var formData = new FormData();
        var attachment = $('#lhkfileupload').get(0).files[0];
        var data_params = { 'pakan_pakai': [], 'rekomendasi_pakan': [] };
        $('table#lhk_pakan>tbody>tr>td.td_nama_pakan').each(function() {
            data_params['pakan_pakai'].push($(this).data('kode_barang'));
        });
        $('table#lhk_permintaan_kandang>tbody>tr>td.td_kode_barang').each(function() {
            data_params['rekomendasi_pakan'].push($(this).data('kode_barang'));
        });
        formData.append("attachment", attachment);
        formData.append("data", JSON.stringify(data_params));

        var _url = 'riwayat_harian_kandang/riwayat_harian_kandang/extractImage';
        $.ajax({
            url: _url,
            data: formData,
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                bootbox.dialog({
                    message: "Sedang proses scanning file ...."
                });
            },
            success: function(data) {
                bootbox.hideAll();
                if (data.status) {
                    bootbox.alert(data.message);
                    $('#lhk_penimbangan_sekat input').prop('readonly', 0);
                    /** ekstract hasil scanning kedalam entry-an */
                    var _result = data.content;
                    var _grup, _index, _tmp, _cari;
                    for (var i in _result) {
                        switch (i) {
                            case 'sekat':
                                for (var x in _result[i]) {
                                    _tmp = x.split('_');
                                    _grup = _tmp[0];
                                    _index = _tmp[1];
                                    _cari = _grup == 'jumlah' ? 'td.td_jumlah_sekat' : 'td.td_bb_sekat';
                                    $('#lhk_penimbangan_sekat>tbody>tr').eq(_index).find(_cari).find('input').val(_result[i][x]);
                                }
                                break;
                            case 'populasi':
                                for (var x in _result[i]) {
                                    _tmp = x.split('_');
                                    _grup = _tmp[0];
                                    _index = _tmp[1];
                                    _cari = _grup == 'mati' ? 'td.td_pengurangan_mati' : 'td.td_pengurangan_afkir';
                                    $('#lhk_populasi>tbody>tr').eq(_index).find(_cari).find('input').val(_result[i][x]);
                                }
                                break;
                            case 'pakai':
                                for (var x in _result[i]) {
                                    _tmp = x.split('_');
                                    _grup = _tmp[0];
                                    _index = _tmp[1];
                                    _cari = 'td.td_sak_pakai';
                                    $('#lhk_pakan>tbody>tr>td.td_nama_pakan[data-kode_barang="' + _grup + '"]').siblings(_cari).find('input').val(_result[i][x]);
                                }
                                break;
                            case 'rekom':
                                for (var x in _result[i]) {
                                    _tmp = x.split('_');
                                    _grup = _tmp[0];
                                    _index = _tmp[1];
                                    _cari = 'td.td_rekomendasi_permintaan';
                                    $('#lhk_permintaan_kandang>tbody>tr>td.td_kode_barang[data-kode_barang="' + _grup + '"]').siblings(_cari).find('input').val(_result[i][x]);
                                }
                                break;
                        }
                    }
                    EntriLHK.enableSave();
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    }
};
$(function() {
    EntriLHK.refresh_page();
    $(document).off('change').on('change', '.btn-file :file', function(e) {
        var allowType = ['image/jpeg'];
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            tipe = input.get(0).files[0].type,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        if (in_array(tipe, allowType)) {
            $('#lhkfile').val(label);
            EntriLHK.readFileUpload();
        } else {
            bootbox.alert('Tipe file yang diijinkan hanya ' + allowType.join(' , '));
        }


    });
});