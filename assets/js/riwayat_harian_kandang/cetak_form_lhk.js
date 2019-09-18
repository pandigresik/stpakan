var CetakLHK = {
    timer: true,
    _timerAbsensi: false,
    //_levelFinger : {'OPERATOR' : null },
    _levelFinger: { 'OPERATOR': 'PENGAWAS', 'PENGAWAS': null, 'DONE': null },
    _btnTerpilih: null,
    _verificator: null,
    _perubahanDataArr: null,
    _kandangMovementArr: null,
    pilih_kandang: function(elm) {
        var _rfID = $(elm).val();
        if (!empty(_rfID)) {
            $.ajax({
                type: "POST",
                url: "api/general/kandang",
                data: {
                    rfid: _rfID
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        var _noreg = data.content.no_reg;
                        var _btnCetak = $('#tb_rekap tbody>tr>td button.btnCetakLHK[data-no_reg=\'' + _noreg + '\']');
                        if (_btnCetak.length) {
                            _btnCetak.trigger('click');
                        } else {
                            toastr.warning('Data noreg ' + _noreg + ' tidak ditemukan');
                        }
                    } else {
                        toastr.warning('Data rfid tidak ditemukan', 'Gagal');
                    }
                    $(elm).val('');
                    $(elm).focus();
                }
            });
        }
    },
    fingerprint: function(_noreg, _tgllhk, _level) {
        var _ini = this;
        var elm = _ini._btnTerpilih;
        var umur = $(elm).data('umur');
        var bootbox_classname = '';
        this.simpan_transaksi_verifikasi(function(result) {
            //if (!empty(_tgllhk) && _level=='OPERATOR') //apabila ingin dimunculkan terus untuk bootbox yang sebelumnya maka pakai yang ini
            if (!empty(_tgllhk))
                bootbox.hideAll();
            if (result.date_transaction) {
                _ini._timerAbsensi = true;
                _ini.set_fingerprint_absensi(result.date_transaction);
                var _convertLevel = { 'OPERATOR': 'Operator', 'PENGAWAS': 'Pengawas' };
                //var _message = '<div><p data-kode-pegawai=""></p><p>Silakan Scan Fingerprint '+_convertLevel[_level]+' untuk Verifikasi</p></div>';
                if (!empty(_tgllhk)) {
                    if (_convertLevel[_level] == 'Operator') {
                        if (umur > 1)
                            bootbox_classname = 'largeWidth';
                        else
                            bootbox_classname = '';
                        $.ajax({
                            type: "POST",
                            url: "riwayat_harian_kandang/cetak_form_lhk/detail_finger_LHK/",
                            data: {
                                noreg: _noreg,
                                level: _level,
                                tgllhk: _tgllhk,
                            },
                            dataType: 'html',
                            success: function(data) {
                                var _message = data;
                                var box = bootbox.dialog({
                                    message: _message,
                                    closeButton: false,
                                    className: bootbox_classname,
                                    title: "Finger Operator",
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
                                    $('.inp-numeric').numeric({});
                                    _ini.timer = true;
                                    _ini.cek_verifikasi(result.date_transaction, _noreg, _level);
                                });
                            }
                        });
                    } else if (_convertLevel[_level] == 'Pengawas') {
                        var _message = '<div><p data-kode-pegawai=""></p><p>Silakan Scan Fingerprint ' + _convertLevel[_level] + ' untuk Verifikasi</p></div>';
                        var box = bootbox.dialog({
                            message: _message,
                            closeButton: false,
                            title: "Finger Pengawas",
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
                } else { //perulangan akibat data not match
                    _ini.timer = true;
                    _ini.cek_verifikasi(result.date_transaction, _noreg, _level);
                }
            }
        });

    },

    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "fingerprint/fingerprint/simpan_transaksi_verifikasi",
            data: {
                transaction: 'cetak_form_lhk',
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
                            var elm = _ini._btnTerpilih;
                            var tgllhk_sebelum = $(elm).data('tgllhk_sebelum');
                            var formatted_tgllhk = $(elm).data('formatted_tgllhk');
                            var umur = $(elm).data('umur');
                            $.ajax({
                                type: "POST",
                                url: "riwayat_harian_kandang/cetak_form_lhk/simpan_rhk_verificator/",
                                data: {
                                    tgllhk_sebelum: tgllhk_sebelum,
                                    noreg: _noreg,
                                    level: _level,
                                    verificator: data.verificator,
                                },
                                dataType: 'json',
                                success: function(data_return) {
                                    if (data_return.status == 1) {
                                        //toastr.success('Verifikasi fingerprint berhasil.','Berhasil');
                                        $('.fingerprint_verfication_message').text('Fingerprint sukses').addClass('text-success').removeClass('text-danger');
                                        if (_level == 'OPERATOR') {
                                            var status_perubahan_data = false;
                                            _perubahanDataArr = [];
                                            _kandangMovementArr = [];
                                            $("div.detail_finger_LHK > table > tbody > tr > td").find("[data-status_ubah='true']").each(function() {
                                                status_perubahan_data = true;
                                                var search = '';
                                                var data_row = {};
                                                var class_name = '';
                                                if ($(this).parent('td').hasClass("penimbangan"))
                                                    search = 'penimbangan';
                                                else if ($(this).parent('td').hasClass("populasi"))
                                                    search = 'populasi';
                                                else if ($(this).parent('td').hasClass("pakan"))
                                                    search = 'pakan';

                                                $(this).closest('tr').find("td." + search).each(function() {
                                                    class_name = $(this).attr('class');
                                                    var explode_text = class_name.split(' ');
                                                    class_name = explode_text[0];
                                                    if ($(this).find('input').length)
                                                        data_row[class_name] = $(this).find('input').val();
                                                    else
                                                        data_row[class_name] = $.trim($(this).data('row'));
                                                });
                                                if (search == 'penimbangan')
                                                    var _perubahanData = {
                                                        'jenis': search,
                                                        'unique_data': 'sekat',
                                                        'sekat': data_row['sekat'],
                                                        'jumlah': data_row['jumlah'],
                                                        'berat': data_row['berat'],
                                                        'keterangan': data_row['keterangan'],
                                                    };
                                                else if (search == 'populasi')
                                                    var _perubahanData = {
                                                        'jenis': search,
                                                        'unique_data': '',
                                                        'c_mati': data_row['c_mati'],
                                                        'c_afkir': data_row['c_afkir'],
                                                    };
                                                else if (search == 'pakan')
                                                    var _perubahanData = {
                                                        'jenis': search,
                                                        'unique_data': 'kode_barang',
                                                        'jenis_kelamin': data_row['jenis_kelamin'],
                                                        'kode_barang': data_row['kode_barang'],
                                                        'jml_pakai': data_row['jml_pakai'],
                                                        'jml_permintaan': data_row['jml_permintaan'],
                                                    };
                                                _perubahanDataArr.push(_perubahanData);
                                            });

                                            //collect untuk insert data movement
                                            $("div.detail_finger_LHK > table > tbody > tr").each(function() {
                                                var data_row = {};
                                                $(this).find("td.pakan").each(function() {
                                                    var class_name = $(this).attr('class');
                                                    var explode_text = class_name.split(' ');
                                                    class_name = explode_text[0];
                                                    if ($(this).find('input').length)
                                                        data_row[class_name] = $(this).find('input').val();
                                                    else
                                                        data_row[class_name] = $.trim($(this).data('row'));
                                                });
                                                var _dataMovement = {
                                                    'jenis_kelamin': data_row['jenis_kelamin'],
                                                    'kode_barang': data_row['kode_barang'],
                                                    'jml_pakai': data_row['jml_pakai'],
                                                    'jml_permintaan': data_row['jml_permintaan'],
                                                };
                                                _kandangMovementArr.push(_dataMovement);
                                            });

                                            if (status_perubahan_data == false)
                                                _level = 'DONE';

                                            $("div.detail_finger_LHK").attr('data-update_data', status_perubahan_data);
                                            //dipindah disebabkan oleh data cetak yang disimpan pada rhk cetak adalah pada saat finger operator
                                            _ini._verificator = data.verificator;
                                        }
                                        var _nextLevel = _ini._levelFinger[_level];
                                        if (!empty(_nextLevel)) {
                                            //fix tgl hanya untuk mempermudah case, lanjutan dari sebelumnya
                                            _ini.fingerprint(_noreg, '2018-01-01', _nextLevel);
                                        } else {
                                            bootbox.hideAll();
                                            toastr.success('Fingerprint sukses.', 'Berhasil');
                                            if (umur >= 26)
                                                bootbox.dialog({
<<<<<<< HEAD
                                                    message: "Apakah LHK tanggal " + tgllhk_sebelum + " merupakan LHK terakhir (Panen telah selesai)?",
=======
                                                    message: "Apakah LHK tanggal " + formatted_tgllhk + " merupakan LHK terakhir (Panen telah selesai)?",
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
                                                    title: "Konfirmasi",
                                                    buttons: {
                                                        success: {
                                                            label: "Ya",
                                                            className: "btn-primary",
                                                            callback: function() {
                                                                _ini.save_server(0);
                                                            }
                                                        },
                                                        danger: {
                                                            label: "Tidak",
                                                            className: "btn-default",
                                                            callback: function() {
                                                                _ini.save_server(1);
                                                            }
                                                        }
                                                    }
                                                });
                                            else
                                                _ini.save_server(1);
                                        }
                                    }
                                }
                            });
                        } else {
                            var _convertLevel = { 'OPERATOR': 'Operator', 'PENGAWAS': 'Pengawas' };
                            var _pesanFinger = '<div class="text-center"><i class="glyphicon glyphicon-remove-sign"></i> Data user ' + _convertLevel[_level] + ' tidak ditemukan mohon melakukan scan fingerprint ulang.</div>';
                            if (_level == 'OPERATOR') {
                                $('.fingerprint_verfication_message').text('Fingerprint operator tidak sesuai, coba lagi').addClass('text-danger').removeClass('text-success');
                                _ini.fingerprint(_noreg, null, _level);
                            } else {
                                var _pesanFinger = '<div class="text-center"><i class="glyphicon glyphicon-remove-sign"></i> Data user ' + _convertLevel[_level] + ' tidak ditemukan. Mohon scan fingerprint ulang.</div>';
                                bootbox.alert(_pesanFinger, function() {
                                    _ini.fingerprint(_noreg, null, _level);
                                });
                            }
                        }
                    } else {
                        _ini.timer = true;
                        setTimeout("CetakLHK.cek_verifikasi('" + date_transaction + "','" + _noreg + "','" + _level + "')", 1000);
                    }
                }
            });
        }
    },

    save_server: function(cetak) {
        var _ini = this;
        var elm = _ini._btnTerpilih;
        var no_reg = $(elm).data('no_reg');
        var farm = $(elm).data('farm');
        var nama_farm = $(elm).data('nama_farm');
        var kandang = $(elm).data('kandang');
        var tgllhk = $(elm).data('tgllhk');
        var tgllhk_sebelum = $(elm).data('tgllhk_sebelum');
        var umur = $(elm).data('umur');
        var kode_flok = $(elm).data('flock');
        var message = '';
        if (cetak)
            message = 'Persiapan cetak form LHK...';
        else
            message = 'Persiapan data LHK...';
        $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "riwayat_harian_kandang/cetak_form_lhk/insert_rhk_cetak/",
                beforeSend: function() {
                    bootbox.dialog({
                        message: message
                    });
                },
                data: {
                    no_reg: no_reg,
                    farm: farm,
                    nama_farm: nama_farm,
                    kandang: kandang,
                    tgllhk: tgllhk,
                    tgllhk_sebelum: tgllhk_sebelum,
                    umur: umur,
                    user_cetak: _ini._verificator,
                    perubahan_data_lhk: _perubahanDataArr,
                    kandang_movement: _kandangMovementArr,
                    cetak: cetak,
                }
            })
            .done(function(data) {
                bootbox.hideAll();
                if (data.status == 1) {
                    //tambahan generate nomor pengambilan pada saat selesai melakukan cetak LHK
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
                                success: function(generate_data) {
                                    bootbox.hideAll();
                                    bootbox.alert(generate_data.message);

                                    $('.bootbox').modal('hide');
                                    $('tbody').empty();
                                }
                            })
                        }
                    }
                    $.ajax({
                            type: 'POST',
                            dataType: 'html',
                            url: "riwayat_harian_kandang/cetak_form_lhk/get_list_LHK/",
                            beforeSend: function() {
                                $("#loading").removeClass('hide');
                            },
                        })
                        .done(function(result_data) {
                            $("#loading").addClass('hide');
                            _ini._btnTerpilih = null;
                            _ini._verificator = null;
                            _perubahanDataArr = null;
                            _kandangMovementArr = null;
                            setTimeout(function() {
                                $('#id_kandang').focus();
                            }, 500);

                            $('tbody').html(result_data);
                        })
                    if (cetak)
                        $.redirect('riwayat_harian_kandang/cetak_form_lhk/print_lhk', { barcode: data.content }, 'get', '_blank');
                }
            })
            .fail(function(reason) {
                console.info(reason);
            })
            .then(function(data) {

            });
    },
    cetak_form_lhk: function(elm) {
        //lakukan pengecekan fingerprint
        var _noreg, _tglrhk, _level = 'OPERATOR';
        this._btnTerpilih = $(elm);
        _noreg = $(elm).data('no_reg');
        _tgllhk = $(elm).data('tgllhk_sebelum');
        this.fingerprint(_noreg, _tgllhk, _level);
    },
    check_changed_LHK: function(elm) {
        var new_value = $(elm).val();
        var old_value = $(elm).data('prior_value');
        if (new_value != old_value) {
            $(elm).attr('data-status_ubah', "true");
            $(elm).parent().addClass('has-error');
        } else {
            $(elm).attr('data-status_ubah', "false");
            $(elm).parent().removeClass('has-error');
        }
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
        var max = parseInt(elm.getAttribute("max")) || 0;
        var inp_pakai = $(elm).val();
        if (inp_pakai > max) {
            $(elm).val(max);
        }
    },
    validatorMaxPengurang: function(elm) {
        //kembalikan nilai ke max permintaan apabila inputan melebihi nilai maksimum
        var max = parseInt(elm.getAttribute("max")) || 0;
        var _inps = $(elm).closest('tr').find('td.populasi > input');
        var _total_pengurang = 0;
        var _nilai_sendiri = parseInt($(elm).val() || 0);
        _inps.each(function() {
            _total_pengurang += parseInt($(this).val() || 0);
        });

        if (_total_pengurang > max) {
            var _selisih = _total_pengurang - max;
            $(elm).val(_nilai_sendiri - _selisih);
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
                        setTimeout("CetakLHK.set_fingerprint_absensi('" + date_transaction + "')", 1000);
                    }
                }
            });
        }
    },
}
$(document).ready(function() {
    $("#loading").addClass('hide');
    $('#id_kandang').focus();
});