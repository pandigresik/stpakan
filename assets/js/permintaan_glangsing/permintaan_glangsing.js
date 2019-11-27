'use strict';

var permintaanSak = {

    _varNoReg: '',
    _varKodeKandang: '',
    _varNoPPSK: '',
    _varJmlDiminta: 0,
    _varBrtTimbang: 0.00,
    _timer: true,
    _tkode_pegawai: '',
    _tnama_pegawai: '',
    _date_transaction: null,
    _trSelected: null,

    add_datepicker: function(elm, options) {
        elm.datepicker(options);
    },

    submitPenjualan: function(elm, _nextStatus) {
        var _error = 0;
        var _message = [];
        var _form = $(elm).closest('form');
        var _budgetsisa = parseInt(_form.find('input[name=budget_sisa]').unmask());
        var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
        var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
        var _kategori = $.trim(_form.find('radio[name=kategori]').val());
        var _NoDo = $.trim(_form.find('input[name=no_do]').val());
        var _userpenerima = _form.find('input[name=user_penerima]').val();
        var _descketerangan = _form.find('select[name=keterangan]').find('option:selected').text();
        /*if(_sakdiminta > _budgetsisa){
          _error++;
          _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sisa budget sak');
        }*/
        if (_sakdiminta < 1 || isNaN(_sakdiminta)) {
            _error++;
            _message.push('Jumlah yang diminta harus lebih besar dari 0');
        }
        if (empty(_userpenerima)) {
            _error++;
            _message.push('Penerima harus diisi');
        }
        if (!_error) {
            bootbox.confirm({
                message: 'Apakah anda yakin menyimpan transaksi ini ?',
                buttons: {
                    'cancel': {
                        label: 'Tidak',
                        className: 'btn-default'
                    },
                    'confirm': {
                        label: 'Ya',
                        className: 'btn-primary'
                    }
                },
                callback: function(result) {
                    if (result) {
                        var url = 'permintaan_glangsing/pengajuan/simpan';
                        var _prefix_ppsk = _form.find('input[name=sak_tersimpan]').data('prefix_ppsk');
                        var _data = { /*'kategori':_kategori, */ 'jml_sak': _sakdiminta, 'no_do': _NoDo, 'user_peminta': _userpenerima, 'no_ppsk': _prefix_ppsk, 'status': _nextStatus };
                        $.post(url, { data: _data, nextStatus: _nextStatus }, function(data) {
                            bootbox.alert(data.message, function() {
                                $('#main_content').load('permintaan_glangsing/pengajuan');
                            });
                        }, 'json');
                    }
                }
            });
        } else {
            bootbox.alert(_message.join('<br />'));
        }
        return false;
    },

    onchangeSak: function(elm) {
        var jumSak = 0;
        var sak_tersimpan = parseInt($('#sak_tersimpan').text());
        var promises = $('.jmlSak').map(function(i, v) {
            jumSak += parseInt($(v).val());
        });
        var budget_sisa = parseInt($('#budget_sisa').text());
        //alert(budget_sisa);

        promises.promise().done(function(results) {
            //console.log(sak_tersimpan+' = '+jumSak);
            if (sak_tersimpan >= jumSak) {
                var jmlOver = jumSak - budget_sisa;
                if (jmlOver > 0) {
                    $('#jml_over').text(jmlOver);
                    $('#alasan').removeAttr("disabled");
                } else {
                    $('#jml_over').text('0');
                    $('#alasan').attr("disabled", true);
                    $('#alasan').val('-');
                }
                $('#jml_diminta').text(jumSak);
            } else {
                toastr.warning('Jumlah total yang diminta lebih besar dari stok yang tersedia', 'Peringatan');
                $(elm).val(0);
                $('#jml_diminta').text(jumSak);
                permintaanSak.onchangeSak(elm);
            }
        });

    },

    submit: function(elm, _nextStatus) {
        var _error = 0;
        var _message = [];
        var _form = $(elm).closest('form');
        var _noPPSK = $.trim(_form.find('input[name=no_ppsk]').val());
        var _budgetsisa = parseInt(_form.find('#jml_diminta').text());
        var _saktersimpan = parseInt(_form.find('#sak_tersimpan').text());
        var _sakdiminta = parseInt(_form.find('#jml_diminta').text());
        var _jmlOver = parseInt(_form.find('#jml_over').text());
        var _alasanOver = $.trim(_form.find('textarea[name=alasan]').val());
        var _keterangan = $.trim(_form.find('select[name=keterangan]').val());
        var _tglKebutuhan = $.trim(_form.find('input[name=tglKebutuhan]').val());
        var _tglPermintaan = $.trim(_form.find('input[name=tglPermintaan]').val());
        var _kodeSiklus = $.trim(_form.find('input[name=kodeSiklus]').val());
        var _ppsk_d = [];
        //var _userpeminta = _form.find('select[name=user_peminta]').val();
        var _descketerangan = _form.find('select[name=keterangan]').find('option:selected').text();
        _jmlOver = isNaN(_jmlOver) ? '0' : _jmlOver;
        _sakdiminta = isNaN(_sakdiminta) ? '0' : _sakdiminta;

        var promises = $('.permintaan_sak_table tbody tr').map(function(i, v) {
            _ppsk_d.push({
                'noReg': $(v).data('noreg'),
                'value': $(v).find('.jmlSak').val(),
            });
            //  console.log(_ppsk_d);
        });


        //_hrgJual    = isNaN(_hrgJual)?'0':_hrgJual;
        var _confirm = {
            'D': 'Apakah anda yakin menyimpan transaksi ini ?',
            'N': 'Apakah anda yakin untuk menyimpan transaksi ini ?',
        };
        /*if(_sakdiminta > _budgetsisa){
            _error++;
            _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sisa budget sak');
        }*/
        if (_keterangan == '') {
            _error++;
            _message.push('kategori harus dipilih');
        }
        if (empty(_alasanOver) && _jmlOver > 0) {
            _error++;
            _message.push('alasan over budget tidak boleh kosong');
        }
        if (!_error) {
            bootbox.confirm({
                message: _confirm[_nextStatus],
                buttons: {
                    'cancel': {
                        label: 'Tidak',
                        className: 'btn-default'
                    },
                    'confirm': {
                        label: 'Ya',
                        className: 'btn-primary'
                    }
                },
                callback: function(result) {
                    if (result) {
                        var url = 'permintaan_glangsing/pengajuan/simpan';
                        var _prefix_ppsk = _form.find('input[name=sak_tersimpan]').data('prefix_ppsk');

                        var _data = {
                            'jml_sak': _sakdiminta,
                            'kode_budget': _keterangan,
                            'budgetsisa': _budgetsisa,
                            'saktersimpan': _saktersimpan,
                            'sakdiminta': _sakdiminta,
                            'jml_over': _jmlOver,
                            'alasanOver': _alasanOver,
                            'no_ppsk': _prefix_ppsk,
                            'status': _nextStatus,
                            'tglKebutuhan': _tglKebutuhan,
                            'tglPermintaan': _tglPermintaan,
                            'kodeSiklus': _kodeSiklus,
                            'no_ppsk': _noPPSK,
                            'data': _ppsk_d
                        };
                        $.post(url, { data: _data }, function(data) {
                            if (data.status = 1) {
                                toastr.success(data.message, 'Berhasil');
                            } else {
                                toastr.error(data.message, 'Error');
                            }
                            $('#main_content').load('permintaan_glangsing/pengajuan');
                        }, 'json');
                    }
                }
            });
        } else {
            bootbox.alert(_message.join('<br />'));
        }
        return false;
    },

    konfirmasiPengambilan: function(elm) {
        var _tr = $(elm).closest('tr');
        permintaanSak._trSelected = _tr;
        permintaanSak._varNoReg = $(_tr).data('noreg');
        permintaanSak._varKodeKandang = $(_tr).data('kode-kandang');
        permintaanSak._varNoPPSK = $('input[name=no_ppsk]').val();
        permintaanSak._varJmlDiminta = $(_tr).data('jml_diminta');
        permintaanSak._varBrtTimbang = 0.00;
        $('input[name=berat_timbang]').val('0');
        var _tglKebutuhan = new Date($('input[name=tglKebutuhan]').val());
        var _tglSekarang = new Date($('input[name=tglSekarang]').val());

        if (_tglKebutuhan > _tglSekarang) {
            toastr.warning('Anda belum diperbolehkan mengambil glangsing', 'Perhatian');
        } else if (_tglKebutuhan < _tglSekarang) {
            toastr.warning('Anda tidak diperbolehkan mengambil glangsing karena terlambat', 'Perhatian');
        } else {
            bootbox
                .dialog({
                    title: 'Konfirmasi Berat',
                    message: $('#loginForm'),
                    show: false /* We will show it manually later */
                })
                .on('shown.bs.modal', function() {
                    $('#loginForm').show();
                    $('#loginForm').find('input[name=rfid_kandang]').prop('readonly', 0).val('');
                    $('#loginForm').find('input[name=berat_timbang]').closest('.form-group').addClass('hide');
                    /* Show the login form */
                    // .formValidation('resetForm', true); /* Reset form */
                })
                .on('hide.bs.modal', function(e) {
                    /**
                     * Bootbox will remove the modal (including the body which contains the login form)
                     * after hiding the modal
                     * Therefor, we need to backup the form
                     */
                    $('#loginForm').hide().appendTo('body');
                })
                .modal('show');
        }

        //alert(_no_reg + ' => ' + _jml_diminta + ' = ' + _no_ppsk);
    },

    get_berat_timbang: function(elm) {
        $(elm).removeAttr('readonly');
        //console.log('OK');
        setTimeout(function() {
            var berat = $(elm).val();
            $(elm).val(berat);
            //  $(elm).attr('readonly', true);
            permintaanSak.kontrol_timbangan(elm);
        }, 0);
    },

    replace_timbang: function(elm) {
        //console.log($(elm).val());
        $(elm).select().focus().val($(elm).val());
    },

    selected: function(elm) {
        $(elm).select().focus();
    },

    kontrol_timbangan: function(elm) {
        var _berat = $(elm).val();
        var url = 'permintaan_glangsing/pengajuan/getToleransiBerat';
        var _batasAtas = 0;
        var _batasBawah = 0;
        var _jml_diminta = permintaanSak._varJmlDiminta;
        $.post(url, { data: '' }, function(data) {
            if (data.status == '1') {
                _batasAtas = data.BATAS_ATAS;
                _batasBawah = data.BATAS_BAWAH;
            }
            var _brtAtas = _batasAtas * _jml_diminta;
            var _brtBawah = _batasBawah * _jml_diminta;
            if (_brtAtas >= _berat && _brtBawah <= _berat) {
                //alert('ok');
                permintaanSak._varBrtTimbang = _berat;
                permintaanSak.fingerprint(elm);
            } else {
                toastr.warning('Berat tidak sesuai dengan toleransi', 'Peringatan');
            }
            //alert(_brtAtas + ' >= ' + _berat + ' >= ' + _brtBawah);
        }, 'json');
        //var _toleransi =
    },

    fingerprint: function(elm) {
        permintaanSak.simpan_transaksi_verifikasi(function(result) {
            if (result.date_transaction) {
                var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint pengawas..</p></div>';
                var box = bootbox.dialog({
                    message: _message,
                    closeButton: false,
                    title: "Fingerprint",
                    buttons: {
                        success: {
                            label: "Batal",
                            className: "btn-danger",
                            callback: function() {
                                permintaanSak._timer = false;
                                permintaanSak._tkode_pegawai = '';
                                permintaanSak._tnama_pegawai = '';
                                return true;
                            }
                        }
                    }
                });

                box.bind('shown.bs.modal', function() {
                    permintaanSak._timer = true;
                    permintaanSak._tkode_pegawai = '';
                    permintaanSak._tnama_pegawai = '';
                    permintaanSak._date_transaction = result.date_transaction;
                    permintaanSak.cek_verifikasi(result.date_transaction);
                });

                box.bind('hidden.bs.modal', function() {
                    if (permintaanSak._tkode_pegawai && permintaanSak._tnama_pegawai) {
                        var done = permintaanSak.cek_selesai(elm);

                        toastr.success('Verifikasi fingerprint berhasil.', 'Berhasil');
                    }

                });
            }
        });
    },

    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
            data: {
                transaction: 'pengambilan_glangsing',
                kode_flok: $('.permintaan_sak_table').find('tbody tr').attr('data-kode-flok')
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },
    cekRfidKandang: function(elm) {
        $.ajax({
            type: "POST",
            url: "permintaan_glangsing/pengajuan/cek_rfid_kandang",
            data: {
                rfid: $(elm).val(),
                kode_kandang: permintaanSak._varKodeKandang
            },
            dataType: 'json',
            success: function(data) {
                // callback(data);
                if (data.success == true) {
                    $('.cekRfidForm').find('.berat').removeClass('hide');
                    $(elm).attr('readonly', true);
                } else {
                    alert(data.msg);
                }
            }
        });
    },
    cek_verifikasi: function(date_transaction) {
        if (permintaanSak._timer == true) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cek_verifikasi",
                data: {
                    date_transaction: date_transaction,
                    noreg: permintaanSak._varNoReg,
                    level: 'PENGAWAS'
                },
                dataType: 'json',
                success: function(data) {
                    if (data.verificator) {
                        $('.bootbox').modal('hide');
                        if (data.match == 1) {
                            permintaanSak._timer = false;
                            permintaanSak._tkode_pegawai = data.kode_pegawai;
                            permintaanSak._tnama_pegawai = data.nama_pegawai;
                        } else {
                            var _message =
                                "<center><h1 class='glyphicon glyphicon-remove-sign'style='color:red;font-size:20vw;'>" +
                                "</h1></center>";
                            var box = bootbox.dialog({
                                message: _message,
                                closeButton: true,
                                title: "Fingerprint",
                                onEscape: function() {
                                    permintaanSak._timer = false;
                                    permintaanSak._tkode_pegawai = '';
                                    permintaanSak._tnama_pegawai = '';
                                    return true;
                                },
                                buttons: {
                                    success: {
                                        label: "<i class='glyphicon glyphicon-refresh'></i> Refresh",
                                        className: "btn-primary",
                                        callback: function() {
                                            $('.bootbox').modal('hide');
                                            permintaanSak.fingerprint();
                                            return true;
                                        }
                                    }
                                }
                            });
                        }
                    } else {
                        permintaanSak._timer = true;
                        permintaanSak._tkode_pegawai = '';
                        permintaanSak._tnama_pegawai = '';
                        setTimeout("permintaanSak.cek_verifikasi('" + date_transaction + "')", 1000);
                    }
                }
            });
        }
    },

    cek_selesai: function(elm) {
        $.ajax({
            type: "POST",
            url: "permintaan_glangsing/pengajuan/konfirmasiPengambilan",
            data: {
                no_ppsk: permintaanSak._varNoPPSK,
                no_reg: permintaanSak._varNoReg,
                user_penerima: permintaanSak._tkode_pegawai,
                brt_timbang: permintaanSak._varBrtTimbang,
                tgl_terima: permintaanSak._date_transaction
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == '1') {
                    //alert(data);
                    toastr.success('Pengambilan glangsing berhasil dilakukan.', 'Berhasil');
                    permintaanSak.getListKandang();
                } else {
                    toastr.error('Penyimpanan gagal.', 'Gagal');
                }
            }
        });

        return 1;
    },

    getListKandang: function() {
        var _no_ppsk = $('input[name=no_ppsk]').val();
        var _kode_budget = $('select[name=keterangan]').find('option:selected').val();
        var _tglPermintaan = $('input[name=tglPermintaan]').val();
        $.ajax({
            type: "POST",
            url: "permintaan_glangsing/pengajuan/getListPengambilan",
            data: {
                no_ppsk: _no_ppsk,
                kode_budget: _kode_budget,
                tgl_kebutuhan: _tglPermintaan
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == '1') {
                    //alert($('.permintaan_sak_table tbody').text());
                    $('.permintaan_sak_table tbody').html(data.content);
                }

            }
        });

        return 1;
    },

    update: function(elm, _nextStatus) {
        if (_nextStatus == 'RJA') {
            var _error = 0;
            var _panel = $(elm).closest('div.panel-body');
            var _keterangan_reject = _panel.find('textarea').val();
            var _kodefarm = $('select[name=list_farm]').find('option:selected').val();

            str = _keterangan_reject;
            if (_keterangan_reject.length < 10) {
                _error++;
                _message.push('Keterangan reject tidak boleh kurang dari 10 karakter');
            }
            if (empty(_alasanOver) && _jmlOver > 0) {
                _error++;
                _message.push('alasan over budget tidak boleh kosong');
            }

            if (!_error) {
                $.ajax({
                    url: 'permintaan_glangsing/pengajuan/getFormPPSKData',
                    type: 'POST',
                    data: {
                        no_ppsk: T_NO_PPSK
                    },
                    dataType: 'JSON',
                    success: function(result) {
                        if (result) {
                            var url = 'permintaan_glangsing/pengajuan/update';
                            var _data = { 'jml_sak': result[0].JML_SAK, /*'keterangan' : _keterangan,*/ 'kode_budget': result[0].KODE_BUDGET, 'user_peminta': result[0].USER_PEMINTA, 'no_ppsk': result[0].NO_PPSK, 'jml_over': result[0].JML_OVER };
                            $.post(url, { data: _data, nextStatus: 'RJ', keterangan_reject: _keterangan_reject, alasan_over: result[0].KETERANGAN, kodefarm: _kodefarm }, function(data) {
                                bootbox.alert(data.message, function() {
                                    $('#main_content').load('permintaan_glangsing/pengajuan/approvalpsk');
                                });
                            }, 'json');
                        }
                    }
                });
            }
            return false;
        } else if (_nextStatus == 'AA') {
            var _tr = elm.closest('tr');
            var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
            var box = bootbox.confirm({
                message: 'Apakah anda yakin untuk mengkonfirmasi No. Permintaan Sak ' + $(_tr).find('.link_span').data('no_ppsk') + ' ?',
                buttons: {
                    'cancel': {
                        label: 'Tidak',
                        className: 'btn-default'
                    },
                    'confirm': {
                        label: 'Ya',
                        className: 'btn-primary'
                    }
                },
                callback: function(result) {
                    if (result) {
                        $.ajax({
                            url: 'permintaan_glangsing/pengajuan/getFormPPSKData',
                            type: 'POST',
                            data: {
                                no_ppsk: $(_tr).find('.link_span').data('no_ppsk'),
                            },
                            dataType: 'JSON',
                            success: function(result) {
                                if (result) {
                                    var url = 'permintaan_glangsing/pengajuan/update';
                                    var _data = { 'jml_sak': result[0].JML_SAK, /*'keterangan' : _keterangan,*/ 'kode_budget': result[0].KODE_BUDGET, 'user_peminta': result[0].USER_PEMINTA, 'no_ppsk': result[0].NO_PPSK, 'jml_over': result[0].JML_OVER };
                                    $.post(url, { data: _data, nextStatus: 'A', kodefarm: _kodefarm }, function(data) {
                                        bootbox.alert(data.message, function() {
                                            $('#main_content').load('permintaan_glangsing/pengajuan/approvalpsk');
                                        });
                                    }, 'json');
                                }
                            }
                        });
                    }
                }
            });
            return false;
        } else {
            var _error = 0;
            var _message = [];
            var _form = $('.form_permintaan');
            var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
            var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
            var _keterangan = $.trim(_form.find('select[name=keterangan]').val());
            var _userpeminta = _form.find('select[name=user_peminta]').val();
            var _no_ppsk = _form.find('input[name=no_ppsk]').val();
            var _hrgJual = parseInt(_form.find('input[name=hrg_jual]').unmask());
            var _jmlOver = parseInt(_form.find('input[name=jml_over]').unmask());
            var _alasanOver = $.trim(_form.find('textarea[name=alasan_over]').val());
            var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
            var _kategori = $.trim(_form.find('input[name=kategori]:checked').val());
            _jmlOver = isNaN(_jmlOver) ? '0' : _jmlOver;
            _sakdiminta = isNaN(_sakdiminta) ? '0' : _sakdiminta;
            _hrgJual = isNaN(_hrgJual) ? '0' : _hrgJual;
            var str = '';
            var _confirm = {
                'A': 'Apakah anda yakin untuk mengkonfirmasi No. Permintaan Sak ' + _no_ppsk + ' ?',
                'R': 'Apakah anda yakin untuk mengkonfirmasi No. Permintaan Sak ' + _no_ppsk + ' ?',
                'RJ': 'Apakah anda yakin untuk menolak No. Permintaan Sak ' + _no_ppsk + ' ?',
                'D': 'Apakah anda yakin untuk mengubah data dengan No. Permintaan Sak ' + _no_ppsk + ' ?',
                'N': 'Apakah anda yakin untuk merilis transaksi ini ?',
                'V': 'Apakah anda yakin untuk menghapus No. Permintaan Sak ' + _no_ppsk + ' ?',
            };
            /*if(_sakdiminta > _saktersimpan){
              _error++;
              _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sak tersimpan');
            }*/
            if (_kategori == 'E' && _hrgJual < 1) {
                _error++;
                _message.push('Harga Jual harus lebih besar dari 0');
            }
            if (_sakdiminta < 1 && _jmlOver <= 0) {
                _error++;
                _message.push('Jumlah yang diminta harus lebih besar dari 0');
            }
            if (_keterangan == '') {
                _error++;
                _message.push('Keterangan harus dipilih');
            }
            if (empty(_userpeminta)) {
                _error++;
                _message.push('Penerima harus dipilih');
            }
            if (!_error) {
                var reject = 0;
                var box = bootbox.confirm({
                    message: _confirm[_nextStatus],
                    buttons: {
                        'cancel': {
                            label: 'Tidak',
                            className: 'btn-default'
                        },
                        'confirm': {
                            label: 'Ya',
                            className: 'btn-primary'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            if (_nextStatus == 'RJ') {
                                reject++;
                            } else {
                                var url = 'permintaan_glangsing/pengajuan/update';
                                var _data = { 'jml_sak': _sakdiminta, /*'keterangan' : _keterangan,*/ 'harga_sak': _hrgJual, 'kode_budget': _keterangan, 'user_peminta': _userpeminta, 'no_ppsk': _no_ppsk, 'jml_over': _jmlOver };
                                $.post(url, { data: _data, nextStatus: _nextStatus, keterangan_reject: str, alasan_over: _alasanOver, kodefarm: _kodefarm }, function(data) {
                                    bootbox.alert(data.message, function() {
                                        //  $('#main_content').load('permintaan_glangsing/permintaan');
                                        permintaanSak.loadListPermintaan(this);
                                        $('#div_permintaan').html('');
                                        $('#histori').html('');
                                    });
                                }, 'json');
                            }
                        }
                    }
                });
                box.bind('hidden.bs.modal', function() {
                    if (reject > 0) {
                        $('#tooltip-reject').tooltipster({
                            animation: 'fade',
                            delay: 200,
                            theme: 'tooltipster-light',
                            touchDevices: false,
                            contentAsHTML: true,
                            interactive: true,
                            position: 'bottom-left',
                            content: $('.btn-danger').siblings('.tooltipster-span').html()
                        });
                        $('#tooltip-reject').tooltipster('show');
                    }
                });
            } else {
                bootbox.alert(_message.join('<br />'));
            }
            return false;
        }
    },
    reject: function(elm) {
        var _error = 0;
        var _message = [];
        var _form = $('.form_permintaan');
        var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
        var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
        var _keterangan = $.trim(_form.find('select[name=keterangan]').val());
        var _userpeminta = _form.find('select[name=user_peminta]').val();
        var _no_ppsk = _form.find('input[name=no_ppsk]').val();
        var _panel = $(elm).closest('div.panel-body');
        var _keterangan_reject = _panel.find('textarea').val(); //$('#keterangan_reject').val();
        var _jmlOver = parseInt(_form.find('input[name=jml_over]').unmask());
        var _alasanOver = $.trim(_form.find('textarea[name=alasan_over]').val());
        var _kodefarm = $('select[name=list_farm]').find('option:selected').val();

        str = _keterangan_reject;
        if (_keterangan_reject.length < 10) {
            _error++;
            _message.push('Keterangan reject tidak boleh kurang dari 10 karakter');
        }
        if (empty(_alasanOver) && _jmlOver > 0) {
            _error++;
            _message.push('alasan over budget tidak boleh kosong');
        }
        if (!_error) {
            $('#tooltip-reject').tooltipster('hide');
            var url = 'permintaan_glangsing/pengajuan/update';
            var _data = { 'jml_sak': _sakdiminta, /*'keterangan' : _keterangan,*/ 'kode_budget': _keterangan, 'user_peminta': _userpeminta, 'no_ppsk': _no_ppsk, 'jml_over': _jmlOver };
            $.post(url, { data: _data, nextStatus: 'RJ', keterangan_reject: str, alasan_over: _alasanOver, kodefarm: _kodefarm }, function(data) {
                bootbox.alert(data.message, function() {
                    $('#main_content').load('permintaan_glangsing/pengajuan');
                });
            }, 'json');
        }

    },
    updatePenjualan: function(elm, _nextStatus) {
        var _error = 0;
        var _message = [];
        var _form = $(elm).closest('form');
        var _budgetsisa = parseInt(_form.find('input[name=budget_sisa]').unmask());
        var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
        var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
        var _kategori = $.trim(_form.find('radio[name=kategori]').val());
        var _NoDo = $.trim(_form.find('input[name=no_do]').val());
        var _userpenerima = _form.find('input[name=user_penerima]').val();
        var _descketerangan = _form.find('select[name=keterangan]').find('option:selected').text();
        var _no_ppsk = _form.find('input[name=no_ppsk]').val();
        var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
        var _confirm = {
            'A': 'Apakah anda yakin menyimpan transaksi ini ?',
        };
        if (_sakdiminta < 1) {
            _error++;
            _message.push('Jumlah yang diminta harus lebih besar dari 0');
        }
        if (empty(_userpenerima)) {
            _error++;
            _message.push('Penerima harus diisi');
        }
        if (!_error) {
            bootbox.confirm({
                message: _confirm[_nextStatus],
                buttons: {
                    'cancel': {
                        label: 'Tidak',
                        className: 'btn-default'
                    },
                    'confirm': {
                        label: 'Ya',
                        className: 'btn-primary'
                    }
                },
                callback: function(result) {
                    if (result) {
                        var url = 'permintaan_glangsing/pengajuan/update';
                        var _prefix_ppsk = _form.find('input[name=sak_tersimpan]').data('prefix_ppsk');
                        var _data = { /*'kategori':_kategori, */ 'jml_sak': _sakdiminta, 'no_do': _NoDo, 'user_peminta': _userpenerima, 'no_ppsk': _no_ppsk, 'status': _nextStatus };
                        $.post(url, { data: _data, nextStatus: _nextStatus, keterangan_reject: '', type: 'penjualan', kodefarm: _kodefarm }, function(data) {
                            bootbox.alert(data.message, function() {
                                $('#main_content').load('permintaan_glangsing/pengajuan');
                            });
                        }, 'json');
                    }
                }
            });
        } else {
            bootbox.alert(_message.join('<br />'));
        }
        return false;
    },

    editView: function(elm) {
        $('.panel_histori').css('display', 'none');
        var _no_ppsk = $(elm).data('no_ppsk');
        var _status = $(elm).data('status');
        var _kode_siklus = $(elm).data('kode_siklus');
        var _kode_budget = $(elm).data('kode_budget');
        var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
        var _tr = $(elm).closest('tr');
        var _keterangan = $(_tr).find('.keterangan').html();
        //console.log(_keterangan);

        if (_no_ppsk.substring(0, 4) == 'PPSK') {
            var _url = 'permintaan_glangsing/pengajuan/viewForm';
        } else {
            var _url = 'permintaan_glangsing/pengajuan/viewFormPenjualan';
        }

        $.ajax({
            url: _url,
            data: { no_ppsk: _no_ppsk, status: _status, kode_budget: _kode_budget, kodefarm: _kodefarm, kode_siklus: _kode_siklus, keterangan: _keterangan },
            dataType: 'html',
            beforeSend: function() {
                $('#div_content').html('Loading ......');
            },
            success: function(data) {
                $('#div_content').css('display', 'inline');
                $('#div_content').html(data);
            }

        });

        $.ajax({
            url: 'permintaan_glangsing/pengajuan/loadViewHistory',
            data: {
                no_ppsk: _no_ppsk,
                kodefarm: _kodefarm
            },
            dataType: 'html',
            type: 'POST',
            beforeSend: function() {
                $('#histori').html('Loading ......');
            },
            success: function(data) {
                var hasil = $('#histori').html(data);
                if (hasil) {
                    $('.panel_histori').css('display', 'inline');
                }
            }
        });
    },
    baru: function(elm) {
        -$.ajax({
            url: _url,
            data: {},
            dataType: 'html',
            beforeSend: function() {
                $('#div_permintaan').html('Loading ......');
            },
            success: function(data) {
                $('#div_permintaan').html(data);
            }
        });
    },
    showButton: function(elm) {
        var _tr = elm.closest('tr');
        $(_tr).find('.btn_approve').css('display', 'inline');
        $(_tr).find('.btn_reject').css('display', 'inline');
    },
    loadListPermintaan: function(elm) {
        var _url = 'permintaan_glangsing/pengajuan/loadListPermintaan';
        $.ajax({
            url: _url,
            data: {
                kode_farm: $('select[name=list_farm]').find('option:selected').val(),
            },
            dataType: 'html',
            method: 'POST',
            beforeSend: function() {
                $('#div_list_permintaan').html('Loading ......');
            },
            success: function(data) {
                $('#div_list_permintaan').css('display', 'inline');
                $('#div_list_permintaan').html(data);
            }
        });
    },

    cariPermintaan: function(elm) {
        var _url = 'permintaan_glangsing/pengajuan/loadListPermintaan';
        var _form = $(elm).closest('.form');
        var _no_ppsk = _form.find('input[name=no_ppsk]').val();
        var _kode_budget = _form.find('select[name=kode_budget]').val();
        var _tgl_awal = _form.find('input[name=tanggal_awal]').datepicker('getDate');
        var _tgl_akhir = _form.find('input[name=tanggal_akhir]').datepicker('getDate');

        $.ajax({
            url: _url,
            data: {
                no_ppsk: _no_ppsk,
                kode_budget: _kode_budget,
                tgl_awal: _tgl_awal != null ? Config._getDateStr(_tgl_awal) : null,
                tgl_akhir: _tgl_akhir != null ? Config._getDateStr(_tgl_akhir) : null,
                kode_farm: $('select[name=list_farm]').find('option:selected').val(),
            },
            dataType: 'html',
            method: 'POST',
            beforeSend: function() {
                $('#div_list_permintaan').html('Loading ......');
            },
            success: function(data) {
                $('#div_list_permintaan').css('display', 'inline');
                $('#div_list_permintaan').html(data);
            }
        });
    },

    NewForm: function(elm) {
        var _url = 'permintaan_glangsing/Pengajuan/NewForm';
        $.ajax({
            url: _url,
            data: {
                kode_farm: $('select[name=list_farm]').find('option:selected').val(),
            },
            dataType: 'html',
            method: 'POST',
            beforeSend: function() {
                $('#div_content').html('Loading ......');
            },
            success: function(data) {
                $('#div_content').css('display', 'inline');
                $('#div_content').html(data);
            }
        });
    },
    refresh_page: function() {
        $('#main_content').load('permintaan_glangsing/pengajuan');
    }
};
$(function() {
    $('.number').priceFormat({
        prefix: '',
        centsSeparator: ',',
        centsLimit: 0,
        clearOnEmpty: false,
        thousandsSeparator: '.'
    });

});

function load_keterangan(kategori) {
    $.ajax({
        url: 'permintaan_glangsing/pengajuan/getJumlahSak',
        data: {
            prefix_ppsk: $('#prefix_ppsk').val(),
            kategori: kategori.value,
            kode_budget: '',
            kodefarm: $('select[name=list_farm]').find('option:selected').val()
        },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            alert(data.budgetsisa);
            $('#budget_sisa').val(data.budgetsisa);
            $('#budget_sisa_t').val(data.budgetsisa);
            $('#budget_total').val(data.budgettotal);
            $('#jml_sak').val('0');
        }
    });

    $.ajax({
        url: 'permintaan_glangsing/pengajuan/listbudgetglangsing2',
        data: {
            kategori: kategori.value
        },
        type: 'POST',
        dataType: 'html',
        success: function(data) {
            $('#keterangan').html(data);
        }
    });

    if (kategori.value == 'E') {
        $('.ctrl_hrg_jual').removeClass('hide');
    } else {
        $('.ctrl_hrg_jual').addClass('hide');
    }
}

function hitungSisaBudget(str) {
    var total_sak = $('#sak_tersimpan_t').val();
    var total_budget = $('#budget_total').val();
    var budget_sisa = $('#budget_sisa_t').val();

    $('#sak_tersimpan').val(total_sak - str.value);
    $('#budget_sisa').val(budget_sisa - str.value);

    if (budget_sisa - str.value < 0) {
        $('#jml_over').prop('readonly', false);
        $('#alasan_over').prop('readonly', false);
        $('#jml_over').focus();

        $('#jml_sak').val(budget_sisa);
        $('#sak_tersimpan').val(total_sak - budget_sisa);
        $('#budget_sisa').val(0);

        $('#over_warning').css('display', 'inline');
    } else {
        $('#jml_over').prop('readonly', true);
        $('#alasan_over').prop('readonly', true);
        $('#jml_over').val('');
        $('#alasan_over').val('');
        $('#over_warning').css('display', 'none');
    }
}

function loadTotalBudget(keterangan) {
    $.ajax({
        url: 'permintaan_glangsing/pengajuan/getJumlahSak',
        data: {
            prefix_ppsk: $('#prefix_ppsk').val(),
            kategori: $("input[name='kategori']:checked").val(),
            kode_budget: keterangan.value,
            kodefarm: $('select[name=list_farm]').find('option:selected').val(),
            tgl_kebutuhan: $('#tglKebutuhan').val(),
            tgl_permintaan: $('#tglPermintaan').val()
        },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            $('.permintaan_sak_table tbody').html(data.daftarpermintaan);
            $('#budget_sisa').html(data.budgetsisa);
            $('#budget_sisa_t').val(data.budgetsisa);
            $('#budget_total').val(data.budgettotal);
        }
    });
}

function lengthCek(str) {
    if (str.value.length < 10) {
        $('.btn_simpan_reject').prop('disabled', true);
    } else {
        $('.btn_simpan_reject').prop('disabled', false);
    }
}