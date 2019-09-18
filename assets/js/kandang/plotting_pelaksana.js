var plottingPelaksana = {
    timer: true,
    _timerAbsensi: false,
    kandang: {},
    verificator: null,
    grupPegawai: { 'KPPB': 'Koordinator Pengawas Produksi', 'PPB': 'Pengawas Produksi', 'OK': 'Operator Kandang' },
    pegawais: {},
    plotted: {},
    savedPloting: {},
    dataKirim: null,
    getPegawai: function(kode_pegawai) {
        return this.pegawais[kode_pegawai];
    },
    setPegawai: function(kode_pegawai, pegawai) {
        this.pegawais[kode_pegawai] = pegawai;
    },
    getKandang: function(rfid) {
        if (this.kandang[rfid] == undefined) {
            var _ini = this;
            $.ajax({
                type: "POST",
                url: "api/general/kandang",
                data: {
                    rfid: rfid
                },
                dataType: 'json',
                beforeSend: function() {
                    /*bootbox.dialog({
                        message: "Checking Scan RFID..."
                    });*/
                },
                success: function(data) {
                    //bootbox.hideAll();
                    if (data.status) {
                        _ini.setKandang(rfid, data.content);
                    } else {
                        bootbox.alert('RFID tidak ditemukan');
                        return;
                    }
                },
                async: false,
                cache: false,
            });
        }
        return this.kandang[rfid];
    },
    setKandang: function(rfid, data) {
        this.kandang[rfid] = data;
    },
    setRfidToNoreg: function() {
        var _result = {},
            _key;
        if (!empty(this.kandang)) {
            for (var i in this.kandang) {
                _key = this.kandang[i]['kode_kandang'];
                _result[_key] = {
                    flok: this.kandang[i]['flok_bdy'],
                    noreg: this.kandang[i]['no_reg']
                };
            }
        }
        return _result;
    },
    scanRFID: function(elm) {
        var _rfID = $(elm).val();
        /** minimal panjang rfid adalah 10 digit */
        if (_rfID.length <= 9) {
            return;
        }
        var _dataKandang = this.getKandang(_rfID);
        $.when(_dataKandang).done(function() {
            if (!empty(_dataKandang)) {
                var no_reg = _dataKandang.no_reg;
                var flock = _dataKandang.flok_bdy;
                var kandang = _dataKandang.kode_kandang;
                var next = plottingPelaksana.validasiScanRFID(flock, kandang, elm);
                var _form = $(elm).closest('form');
                var _elmKandang = _form.find('input[name=kandang]');
                var _elmFlok = _form.find('input[name=flok]');
                if (next['status']) {
                    var _help_block_kandang = _elmKandang.next('.help-block');
                    if (!_help_block_kandang.find('span.btn:contains(' + kandang + ')').length) {
                        $('<span class="btn new" onclick="plottingPelaksana.removeKandang(this)"> ' + kandang + ' <i class="glyphicon glyphicon-remove-circle"></i></span>').appendTo(_help_block_kandang);
                    }

                    var _help_block_flok = _elmFlok.next('.help-block');
                    if (!_help_block_flok.find('span.btn:contains(' + flock + ')').length) {
                        $('<span class="btn new" onclick="plottingPelaksana.removeFlok(this)"> ' + flock + ' <i class="glyphicon glyphicon-remove-circle"></i></span>').appendTo(_help_block_flok);
                    }

                } else {
                    toastr.error(next['message']);
                }
            }


            //$(elm).select();
            $(elm).val('');
            $(elm).focus();

        });
    },
    removeKandang: function(elm) {
        var _help_block = $(elm).closest('.help-block');
        $(elm).remove();
        /** jika sudah gak ada maka hapus juga floknya */
        if (!_help_block.find('span.btn').length) {
            _help_block.closest('form').find('input[name=flok]').next('.help-block').find('span.btn').remove();
        }
    },
    removeFlok: function(elm) {
        $(elm).remove();
    },
    /** untuk  */
    validasiScanRFID: function(flock, kandang, elm) {
        var jabatan = $(elm).data('grup_pegawai');
        var kode_pegawai = $(elm).data('kode_pegawai');
        var _hasilCek;
        /** jika KPPB, maka bisa diplot untuk semua flok, dengan syarat belum diplot untuk user lain */
        switch (jabatan) {
            case 'KPPB':
                _hasilCek = this.cekKPPB(kode_pegawai, flock, elm);
                break;
            case 'PPB':
                _hasilCek = this.cekPPB(kode_pegawai, flock, kandang, elm);
                break;
            default:
                _hasilCek = this.cekOK(kode_pegawai, kandang, elm);
        }

        return _hasilCek;
    },
    cekKPPB: function(kode_pegawai, flock, elm) {
        var _result = { 'status': 1, 'message': '' };
        var _plottedFlok, _plotKodePegawai = {};
        if (!empty(this.savedPloting[flock]['koordinator'])) {
            _plotKodePegawai['kode'] = this.savedPloting[flock]['koordinator']['kode'];
            _plotKodePegawai['nama'] = this.savedPloting[flock]['koordinator']['nama'];
        }

        if (empty(_plotKodePegawai)) {
            _plottedFlok = this.plotted[flock] != undefined ? this.plotted[flock] : {};
            if (!empty(_plottedFlok['koordinator'])) {
                _plotKodePegawai['kode'] = _plottedFlok['koordinator']['kode'];
                _plotKodePegawai['nama'] = _plottedFlok['koordinator']['nama'];
            }
        }

        if (!empty(_plotKodePegawai)) {
            if (_plotKodePegawai['kode'] != kode_pegawai) {
                _result['status'] = 0;
                _result['message'] = 'Flock ' + flock + ' sudah diploting koordinator pengawas ' + _plotKodePegawai['nama'];
            }
        }


        return _result;
    },
    cekPPB: function(kode_pegawai, flock, kandang, elm) {
        var _result = { 'status': 1, 'message': '' };
        var _flokElm, _form, _plotPengawas, _plotKodePegawai = {};
        _form = $(elm).closest('form');
        _kandangElm = _form.find('input[name=kandang]');
        _flokElm = _form.find('input[name=flok]');
        var _flokPlot = $(_flokElm).next('.help-block').find('span.btn').eq(0);
        if (_flokPlot.length) {
            if ($.trim(_flokPlot.text()) != flock) {
                _result['status'] = 0;
                _result['message'] = '1 Pengawas hanya bisa plotting 1 flock';
            }
        }

        if (this.savedPloting[flock] != undefined) {
            if (this.savedPloting[flock][kandang] != undefined) {
                _plotKodePegawai['kode'] = this.savedPloting[flock][kandang]['pengawas']['kode'];
                _plotKodePegawai['nama'] = this.savedPloting[flock][kandang]['pengawas']['nama'];
            }
        }

        if (empty(_plotKodePegawai['kode'])) {
            if (this.plotted[flock] != undefined) {
                if (this.plotted[flock][kandang] != undefined) {
                    _plotKodePegawai['kode'] = this.plotted[flock][kandang]['pengawas']['kode'];
                    _plotKodePegawai['nama'] = this.plotted[flock][kandang]['pengawas']['nama'];
                }
            }
        }

        if (!empty(_plotKodePegawai['kode'])) {
            if (_plotKodePegawai['kode'] != kode_pegawai) {
                _result['status'] = 0;
                _result['message'] = 'Kandang ' + kandang + ' sudah diploting pengawas ' + _plotKodePegawai['nama'];
            }
        }

        return _result;
    },
    cekOK: function(kode_pegawai, kandang, elm) {
        var _result = { 'status': 1, 'message': '' };
        var _kandangPlot = $(elm).next('.help-block').find('span.btn');
        if (_kandangPlot.length) {
            _result['status'] = 0;
            _result['message'] = '1 Operator hanya bisa diplotting 1 kandang';
        }
        return _result;
    },
    sortmyway: function(data_A, data_B) {
        return (data_A - data_B);
    },
    setNoregToFlok: function(allNoreg) {
        var _result = [],
            _kodeflok, _noreg;
        for (var i in allNoreg) {
            _kodeflok = allNoreg[i]['flok'];
            _noreg = allNoreg[i]['noreg'];
            if (_result[_kodeflok] == undefined) {
                _result[_kodeflok] = [];
            }
            _result[_kodeflok].push(_noreg);
        }
        return _result;
    },
    kumpulkanData: function() {
        var _kandang = {},
            _operatorKandang = {},
            _tmp, _kode_kandang,
            _grup_pegawai, _kode_pegawai, _koordinator = {},
            _pengawas = {};
        $('table.plotting_table>tbody>tr').each(function() {
            _kode_pegawai = $(this).find('td.kode_pegawai').data('kode_pegawai');
            _grup_pegawai = $(this).data('grup_pegawai');

            if (_grup_pegawai == 'KPPB') {
                _tmp = $(this).find('input[name=flok]').val().split(',');
                for (var i in _tmp) {
                    _kode_kandang = $.trim(_tmp[i]);
                    _koordinator[_kode_kandang] = _kode_pegawai;
                }
            } else {
                if ($(this).find('td.plot').length) {
                    _tmp = $(this).find('input[name=kandang]').val().split(',');
                    for (var i in _tmp) {
                        _kode_kandang = $.trim(_tmp[i]);
                        if (_grup_pegawai == 'PPB') {
                            _pengawas[_kode_kandang] = _kode_pegawai;
                        } else {
                            if (_operatorKandang[_kode_kandang] == undefined) {
                                _operatorKandang[_kode_kandang] = [];
                            }
                            _operatorKandang[_kode_kandang].push(_kode_pegawai);
                        }
                        if (_kandang[_kode_kandang] == undefined) {
                            _kandang[_kode_kandang] = _kode_kandang;
                        }
                    }
                }
            }
        });
        return {
            koordinator: _koordinator,
            pengawas: _pengawas,
            operator: _operatorKandang,
            kandang: _kandang
        };
    },

    save: function() {
        //collect data
        var siklus = $('.form_plotting').find('select[name=siklus] option:selected').val();
        var dataPlotting = plottingPelaksana.plotted;
        var detailPlotting = {};
        var dataFlok = {};
        var _kandangHarusPloting = 0;
        var _kandangSudahPloting = 0;
        var _error = 0;
        var _errorMessage = [];
        var _mapKandang = this.setRfidToNoreg();

        if (!empty(dataPlotting)) {
            var _noreg, _cekPloting = 0;
            for (var _flok in dataPlotting) {
                _kandangHarusPloting = 0;
                _kandangSudahPloting = 0;
                _cekPloting = 0;
                for (var _x in this.savedPloting[_flok]) {
                    if (_x == 'koordinator') continue;
                    _kandangHarusPloting++;

                    if (dataPlotting[_flok][_x] != undefined) {
                        if (!empty(dataPlotting[_flok][_x]['pengawas'])) {
                            _cekPloting++;
                        }
                        if (!empty(dataPlotting[_flok][_x]['operator'])) {
                            _cekPloting++;
                        }
                    }
                }
                /** cek jika pengawas dan operator sudah diploting */
                if (_cekPloting) {
                    for (var _kandang in dataPlotting[_flok]) {
                        if (empty(dataPlotting[_flok]['koordinator'])) {
                            console.log('koordinator kosong ' + _error);
                            _error++;
                            break;
                        }
                        if (_kandang == 'koordinator') continue;
                        _kandangSudahPloting++;

                        _noreg = _mapKandang[_kandang]['noreg'];
                        if (empty(dataPlotting[_flok][_kandang])) {
                            _error++;
                            break;
                        }

                        if (empty(dataPlotting[_flok][_kandang]['pengawas'])) {
                            console.log(dataPlotting[_flok][_kandang]);
                            console.log('pengawas kosong ' + _error);
                            _error++;
                            break;
                        }

                        if (empty(dataPlotting[_flok][_kandang]['operator'])) {
                            console.log('operator kosong ' + _error);
                            _error++;
                            break;
                        }

                        if (!_error) {
                            for (var x in dataPlotting[_flok][_kandang]['operator']) {
                                if (detailPlotting[_kandang] == undefined) {
                                    detailPlotting[_kandang] = [];
                                }
                                detailPlotting[_kandang].push({
                                    kode_siklus: siklus,
                                    no_reg: _noreg,
                                    kode_kandang: _kandang,
                                    operator: dataPlotting[_flok][_kandang]['operator'][x]['kode'],
                                    pengawas: dataPlotting[_flok][_kandang]['pengawas']['kode'],
                                    koordinator: dataPlotting[_flok]['koordinator']['kode']
                                });
                            }
                        }
                    }

                    if (_kandangSudahPloting < _kandangHarusPloting) {
                        _error++;
                    }
                }

            }
        } else {
            toastr.error('Belum ada yang diploting');
            return;
        }

        if (empty(detailPlotting)) {
            _error++;
            toastr.error('Belum ada yang diploting');
            return;
        }

        var data_params = {
            'detail_plotting': detailPlotting,
            //'flok': dataFlok,
            'siklus': siklus
        }

        if (!_error) {
            /** periksa apakah masih ada kandang yang belum diploting di flok yang dipilih */
            var message = '<span class="col-sm-12">Apakah anda yakin ingin menyimpan data ini ?</span><span>Data yang sudah di simpan tidak bisa di ubah.</span>';
            var _options = {
                title: 'Simpan Plotting',
                className: 'titleCenter',
                message: message,
                buttons: {
                    cancel: {
                        label: 'Batal',
                        callback: function() {}
                    },
                    confirm: {
                        label: 'Simpan',
                        callback: function() {
                            box.bind('hidden.bs.modal', function() {
                                plottingPelaksana.executeSave(data_params);
                            });
                        }
                    },
                },
            };
            var box = bootbox.dialog(_options);
        } else {
            bootbox.alert('Data tidak valid, kandang pada flock tersebut belum diploting secara lengkap <br />' + _errorMessage.join('<br />'));
        }
    },
    executeSave: function(data) {
        var _ini = this;
        $.ajax({
            url: 'kandang/plotting_pelaksana/save',
            data: {
                data: data
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                bootbox.dialog({
                    message: "Sedang proses simpan..."
                });
            },
            async: false,
            success: function(data) {
                bootbox.hideAll();
                if (data.status) {
                    bootbox.alert(data.message, function() {
                        getReport(0);
                        $('#main_content').empty().load('kandang/plotting_pelaksana/main');
                        _ini.timer = false;
                    });
                } else {
                    bootbox.alert(data.message);
                }
            }
        });
    },

    fingerprint: function(verificator) {
        var _ini = this;
        this.simpan_transaksi_verifikasi(function(result) {
            if (result.date_transaction) {
                _ini._timerAbsensi = true;
                _ini.set_fingerprint_absensi(result.date_transaction);
                _ini.timer = true;
                _ini.cek_verifikasi(result.date_transaction, verificator);
            }
        });

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
                        setTimeout("plottingPelaksana.set_fingerprint_absensi('" + date_transaction + "')", 1000);
                    }
                }
            });
        }
    },


    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "fingerprint/fingerprint/simpan_transaksi_verifikasi",
            data: {
                transaction: 'ploting_pelaksana',
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    cek_verifikasi: function(date_transaction, verificator) {
        if (this.timer == true) {
            var _ini = this;
            var _result = {
                result: 0
            };
            $.ajax({
                type: "POST",
                url: "fingerprint/fingerprint/cek_verifikasi",
                data: {
                    date_transaction: date_transaction,
                    verificator: verificator,
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        _ini.timer = false;
                        if (data.match) {
                            if (empty(verificator)) {
                                /** pastikan yang finger adalah user dengan grup pegawai yang diijinkan */
                                var _gp = data.grup_pegawai;
                                if (_ini.grupPegawai[_gp] !== undefined) {
                                    /** pastikan pegawai ini bisa diset untuk plotting lagi */
                                    var _dpegawai = { "kode_pegawai": data.kode_pegawai, "nama_pegawai": data.nama_pegawai, "grup_pegawai": data.grup_pegawai };
                                    if (_ini.bisaSetPlotting(_dpegawai)) {
                                        _ini.setPegawai(data.verificator, _dpegawai);
                                        _ini.showDialogPLotting(_dpegawai);
                                    } else {
                                        bootbox.alert("Data pegawai sudah diploting", function() {
                                            _ini.fingerprint('');
                                        });
                                    }

                                } else {
                                    bootbox.alert("Grup pegawai tidak diijinkan", function() {
                                        _ini.fingerprint('');
                                    });
                                }
                            } else {
                                _ini.addListPlotting(data.verificator);
                            }
                        } else {
                            if (!empty(verificator)) {
                                $('#error_message_finger').removeClass('glyphicon-time');
                                $('#error_message_finger').addClass('abang glyphicon-remove-circle');
                                _ini.fingerprint(verificator);
                            }
                        }
                    } else {
                        _ini.timer = true;
                        setTimeout("plottingPelaksana.cek_verifikasi('" + date_transaction + "','" + verificator + "')", 1000);
                    }
                }
            });
        }
    },


    triggerFinger: function() {
        if ($('#save').length) {
            this.fingerprint('');
        }
    },

    stopFinger: function() {
        this.timer = false;
        this._timerAbsensi = false;
    },
    getListFlokKandangPegawai: function(kode_pegawai) {
        var _result = { flok: [], kandang: [] };
        var _perflok, _perflokPlotted, _perkandang, _perkandangPlotted;
        for (var _flok in this.savedPloting) {
            _perflok = this.savedPloting[_flok];
            _perflokPlotted = this.plotted[_flok] != undefined ? this.plotted[_flok] : {};
            if (!empty(_perflok['koordinator'])) {
                if (_perflok['koordinator']['kode'] == kode_pegawai) {
                    _result['flok'].push(_flok);
                }
            } else {
                if (!empty(_perflokPlotted['koordinator'])) {
                    if (_perflokPlotted['koordinator']['kode'] == kode_pegawai) {
                        _result['flok'].push(_flok);
                    }
                }
            }

            for (var _kandang in _perflok) {
                _perkandang = _perflok[_kandang];
                _perkandangPlotted = _perflokPlotted[_kandang] != undefined ? _perflokPlotted[_kandang] : {};
                if (!empty(_perkandang['pengawas'])) {
                    if (_perkandang['pengawas']['kode'] == kode_pegawai) {
                        _result['flok'].push(_flok);
                        _result['kandang'].push(_kandang);
                    }
                }

                if (!empty(_perkandangPlotted['pengawas'])) {
                    if (_perkandangPlotted['pengawas']['kode'] == kode_pegawai) {
                        _result['flok'].push(_flok);
                        _result['kandang'].push(_kandang);
                    }
                }

                for (var _op in _perkandang['operator']) {
                    if (_perkandang['operator'][_op]['kode'] == kode_pegawai) {
                        _result['flok'].push(_flok);
                        _result['kandang'].push(_kandang);
                    }
                }

                for (var _op in _perkandangPlotted['operator']) {
                    if (_perkandangPlotted['operator'][_op]['kode'] == kode_pegawai) {
                        _result['flok'].push(_flok);
                        _result['kandang'].push(_kandang);
                    }
                }
            }
        }
        return _result;
    },
    showDialogPLotting: function(pegawai) {
        var _verificator = pegawai.kode_pegawai;
        var _hide_flok = pegawai.grup_pegawai == 'KPPB' ? '' : 'hide';
        var _hide_kandang = pegawai.grup_pegawai == 'KPPB' ? 'hide' : '';
        var _flokKandang = this.getListFlokKandangPegawai(pegawai.kode_pegawai);

        var _flok = [],
            _flokBtn = [],
            _kandangBtn = [],
            _kandang = [];
        _flok = _flokKandang['flok'];
        _kandang = _flokKandang['kandang'];

        for (var i in _flok) {
            _flokBtn.push('<span class="btn" onclick="plottingPelaksana.removeFlok(this)"> ' + _flok[i] + ' <i class="glyphicon glyphicon-remove-circle"></i></span>');
        }

        for (var i in _kandang) {
            _kandangBtn.push('<span class="btn" onclick="plottingPelaksana.removeKandang(this)"> ' + _kandang[i] + ' <i class="glyphicon glyphicon-remove-circle"></i></span>');
        }

        var _message = [
            '<form id="form_plot" class="form form-horizontal">',
            '<div class="form-group">',
            '<label class="control-label col-md-3">Nama</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + pegawai.nama_pegawai + '"/>',
            '</div>',
            '</div>',

            '<div class="form-group">',
            '<label class="control-label col-md-3">Jabatan</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + this.grupPegawai[pegawai.grup_pegawai] + '"/>',
            '</div>',
            '</div>',

            '<div class="form-group ' + _hide_flok + '">',
            '<label class="control-label col-md-3">Flock</label>',
            '<div class="col-md-6">',
            '<input type="text" class="form-control" name="flok" value="" data-kode_pegawai="' + pegawai.kode_pegawai + '" data-grup_pegawai="' + pegawai.grup_pegawai + '" onchange="plottingPelaksana.scanRFID(this)"/>',
            '<span class="help-block">' + _flokBtn.join('') + '</span>',
            '</div>',
            '</div>',

            '<div class="form-group ' + _hide_kandang + '">',
            '<label class="control-label col-md-3">Kandang</label>',
            '<div class="col-md-6">',
            '<input type="text" class="form-control" name="kandang" value="" data-kode_pegawai="' + pegawai.kode_pegawai + '" data-grup_pegawai="' + pegawai.grup_pegawai + '" onchange="plottingPelaksana.scanRFID(this)"/>',
            '<span class="help-block">' + _kandangBtn.join('') + '</span>',
            '</div>',
            '</div>',

            '</form>',
            '<div><span class="abang">Lakukan finger untuk menyimpan data</span> &nbsp; <i id="error_message_finger" class="glyphicon glyphicon-time"></i></div>',
        ];
        var box = bootbox.dialog({
            message: _message.join(''),
            closeButton: false,
            title: "Plotting",
        });
        box.bind('shown.bs.modal', function() {
            box.find("input:visible").focus();
        });
        this.fingerprint(_verificator);
    },

    addListPlotting: function(verificator) {
        var _form = $('#form_plot');
        var _kandangArr = _form.find('input[name=kandang]').next('.help-block').find('span.btn');
        var _flokArr = _form.find('input[name=flok]').next('.help-block').find('span.btn');
        var _dpegawai = this.getPegawai(verificator);
        var _as = '',
            _flok = [],
            _kandang = [],
            dataPloting;
        switch (_dpegawai.grup_pegawai) {
            case 'KPPB':
                _as = 'koordinator';
                break;
            case 'PPB':
                _as = 'pengawas';
                break;
            default:
                _as = 'operator';
        }
        _kandangArr.each(function() {
            _kandang.push($.trim($(this).text()));
        });

        _flokArr.each(function() {
            _flok.push($.trim($(this).text()));
        });
        dataPloting = { flok: _flok, kandang: _kandang, kode: _dpegawai.kode_pegawai, nama: _dpegawai.nama_pegawai, as: _as };

        this.updateDataTable(dataPloting);

        this.buildTable();
        bootbox.hideAll();
        this.fingerprint('');
    },

    bisaSetPlotting: function(pegawai) {
        var _result = 0,
            _flok, _kandang, _perflok, _perkandang, _pengawas, _operator;
        /** selain kppb, jika sudah pernah disimpan gak bisa diplot lagi */
        if (pegawai.grup_pegawai == 'KPPB') {
            _result = 0;
        } else {
            for (var i in this.savedPloting) {
                _flok = i;
                _perflok = this.savedPloting[_flok];
                for (var j in _perflok) {
                    if (j == 'koordinator') continue;
                    _kandang = j;
                    _perkandang = _perflok[_kandang];
                    _pengawas = !empty(_perkandang['pengawas']) ? _perkandang['pengawas'] : {};
                    if (!empty(_pengawas)) {
                        if (_pengawas['kode'] == pegawai.kode_pegawai) {
                            _result++;
                        }
                    }

                    if (!_result) {
                        for (var o in _perkandang['operator']) {
                            if (_perkandang['operator'][o]['kode'] == pegawai.kode_pegawai) {
                                _result++;
                            }
                        }
                    }
                }
            }
        }
        return _result ? 0 : 1;
    },
    /** simpan dalam object javascript */
    setDataTable: function() {
        var _flok, _kandang, _koordinator, _pengawas, _operator, _nama_koordinator, _nama_pengawas, _nama_operator, _result = {};
        $('table.plotting_table>tbody>tr').each(function() {
            _flok = $(this).find('td.flok').text();
            _kandang = $(this).find('td.kandang').text();
            _koordinator = $(this).find('td.koordinator').data('kode_pegawai');
            _pengawas = $(this).find('td.pengawas').data('kode_pegawai');
            _operator = $(this).find('td.operator').data('kode_pegawai');
            _nama_koordinator = $(this).find('td.koordinator').text();
            _nama_pengawas = $(this).find('td.pengawas').text();
            _nama_operator = $(this).find('td.operator').text();

            if (_result[_flok] == undefined) {
                _result[_flok] = { 'koordinator': {} };
            }
            if (!empty(_koordinator)) {
                _result[_flok]['koordinator'] = { 'kode': _koordinator, 'nama': _nama_koordinator };
            }

            if (_result[_flok][_kandang] == undefined) {
                _result[_flok][_kandang] = { 'pengawas': {}, 'operator': [] };
            }

            if (!empty(_pengawas)) {
                _result[_flok][_kandang]['pengawas'] = { 'kode': _pengawas, 'nama': _nama_pengawas };
            }
            if (!empty(_operator)) {
                _result[_flok][_kandang]['operator'].push({ 'kode': _operator, 'nama': _nama_operator });
            }

        });
        this.savedPloting = _result;
        this.setRowspan();
    },

    updateDataTable: function(dataPloting) {
        var _flokArr, _kandangArr, _as, _kode, _nama, _dataPegawai, _perkandang, _perflok;
        _as = dataPloting['as'];
        _flokArr = dataPloting['flok'];
        _kandangArr = dataPloting['kandang'];
        _kode = dataPloting['kode'];
        _nama = dataPloting['nama'];
        _dataPegawai = { 'kode': _kode, 'nama': _nama };
        this.removeDataTable(dataPloting);

        if (_as == 'koordinator') {
            for (var _flok in this.savedPloting) {
                if (in_array(_flok, _flokArr)) {
                    if (this.plotted[_flok] == undefined) {
                        this.plotted[_flok] = { 'koordinator': {} };
                    }
                    this.plotted[_flok]['koordinator'] = _dataPegawai;
                }
            }
        } else {
            for (var _flok in this.savedPloting) {
                if (in_array(_flok, _flokArr)) {
                    if (this.plotted[_flok] == undefined) {
                        this.plotted[_flok] = { 'koordinator': {} };
                    }

                    _perflok = this.savedPloting[_flok];
                    for (var _kandang in _perflok) {
                        if (in_array(_kandang, _kandangArr)) {
                            if (this.plotted[_flok][_kandang] == undefined) {
                                this.plotted[_flok][_kandang] = { 'pengawas': {}, 'operator': [] };
                            }
                            if (_as == 'pengawas') {
                                this.plotted[_flok][_kandang]['pengawas'] = _dataPegawai;
                            } else {
                                this.plotted[_flok][_kandang]['operator'].push(_dataPegawai);
                            }
                        }
                    }

                }
            }
        }



    },

    removeDataTable: function(dataPloting) {
        var _as, _kode, _nama, _dataPegawai, _tmpOperator, _perflok, _perkandang;
        _as = dataPloting['as'];
        _kode = dataPloting['kode'];
        _nama = dataPloting['nama'];
        _dataPegawai = { 'kode': _kode, 'nama': _nama };
        if (_as == 'koordinator') {
            for (var _flok in this.savedPloting) {
                if (this.plotted[_flok] != undefined) {
                    if (!empty(this.plotted[_flok]['koordinator'])) {
                        if (this.plotted[_flok]['koordinator']['kode'] == _kode) {
                            this.plotted[_flok]['koordinator'] = {};
                        }
                    }
                }
            }
        } else {
            for (var _flok in this.savedPloting) {
                _perflok = this.savedPloting[_flok];
                for (var _kandang in _perflok) {
                    if (this.plotted[_flok] != undefined) {
                        if (this.plotted[_flok][_kandang] != undefined) {
                            if (_as == 'pengawas') {
                                if (!empty(this.plotted[_flok][_kandang]['pengawas'])) {
                                    if (this.plotted[_flok][_kandang]['pengawas']['kode'] == _kode) {
                                        this.plotted[_flok][_kandang]['pengawas'] = {};
                                    }
                                }
                            } else {
                                if (!empty(this.plotted[_flok][_kandang]['operator'])) {
                                    _tmpOperator = this.plotted[_flok][_kandang]['operator'];
                                    this.plotted[_flok][_kandang]['operator'] = [];
                                    for (var op in _tmpOperator) {
                                        if (_tmpOperator[op]['kode'] != _kode) {
                                            this.plotted[_flok][_kandang]['operator'].push(_tmpOperator[op]);
                                        }
                                    }
                                }
                            }
                        }

                    }
                }

            }

        }




    },
    bestCopyEver: function(src) {
        return JSON.parse(JSON.stringify(src))
    },
    mergeRecursive: function(obj1, obj2) {
        for (var p in obj2) {
            try {
                // Property in destination object set; update its value.
                if (obj2[p].constructor == Object) {
                    obj1[p] = this.mergeRecursive(obj1[p], obj2[p]);

                } else {
                    obj1[p] = obj2[p];

                }

            } catch (e) {
                // Property in destination object not set; create it and set its value.
                obj1[p] = obj2[p];
            }
        }

        return obj1;
    },
    buildTable: function() {
        var _table = [],
            _tr, _perflok, _perkandang, _plotedFlok;
        var _flok, _kandang, _koordinator, _pengawas, _operator, _nama_koordinator, _nama_pengawas, _nama_operator, _operators;
        var _tmpObject = this.bestCopyEver(this.savedPloting)
        this.mergeRecursive(_tmpObject, this.plotted);
        console.log(_tmpObject);
        for (var i in _tmpObject) {
            _flok = i;
            _koordinator = _pengawas = _operator = _nama_koordinator = _nama_pengawas = _nama_operator = '';
            _perflok = _tmpObject[_flok];

            _koordinator = !empty(_perflok['koordinator']) ? _perflok['koordinator']['kode'] : '';
            _nama_koordinator = !empty(_perflok['koordinator']) ? _perflok['koordinator']['nama'] : '';
            for (var j in _perflok) {
                if (j == 'koordinator') continue;
                _operators = [];
                _nama_pengawas = '';
                _pengawas = '';
                _kandang = j;
                _perkandang = _perflok[_kandang];

                if (!empty(_perkandang['pengawas'])) {
                    _pengawas = _perkandang['pengawas']['kode'];
                    _nama_pengawas = _perkandang['pengawas']['nama'];
                }

                _operators = _perkandang['operator'];

                if (!empty(_operators)) {
                    for (var k in _operators) {
                        _operator = _operators[k]['kode'];
                        _nama_operator = _operators[k]['nama'];
                        _tr = ['<tr>',
                            '<td class="flok">' + _flok + '</td>',
                            '<td class="koordinator" data-kode_pegawai="' + _koordinator + '">' + _nama_koordinator + '</td>',
                            '<td class="pengawas" data-kode_pegawai="' + _pengawas + '">' + _nama_pengawas + '</td>',
                            '<td class="kandang">' + _kandang + '</td>',
                            '<td class="operator" data-kode_pegawai="' + _operator + '">' + _nama_operator + '</td>',
                            '</tr>'
                        ];
                        _table.push(_tr.join(''));
                    }
                } else {
                    _tr = ['<tr>',
                        '<td class="flok">' + _flok + '</td>',
                        '<td class="koordinator" data-kode_pegawai="' + _koordinator + '">' + _nama_koordinator + '</td>',
                        '<td class="pengawas" data-kode_pegawai="' + _pengawas + '">' + _nama_pengawas + '</td>',
                        '<td class="kandang">' + _kandang + '</td>',
                        '<td class="operator" data-kode_pegawai=""></td>',
                        '</tr>'
                    ];
                    _table.push(_tr.join(''));
                }
            }
        }
        $('table.plotting_table>tbody').empty();

        $('table.plotting_table>tbody').append(_table.join(''));
        this.setRowspan();

    },

    setRowspan: function() {
        $('table.plotting_table').rowspanizer({ columns: [0, 1, 2, 3] });
    }
}

var search = false;
var page_number = 0;
var total_page = null;



function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function getReport(page_number) {
    if (page_number == 0) {
        $("#previous").prop('disabled', true);
    } else {
        $("#previous").prop('disabled', false);
    }

    if (page_number == (total_page - 1)) {
        $("#next").prop('disabled', true);
    } else {
        $("#next").prop('disabled', false);
    }

    $("#page_number").text(page_number + 1);
    var siklus = $('input.filter[name="siklus"]').val();
    var flock = $('input.filter[name="flock"]').val();
    var kandang = $('input.filter[name="kandang"]').val();
    var koordinator = $('input.filter[name="koordinator"]').val();
    var pengawas = $('input.filter[name="pengawas"]').val();
    var operator = $('input.filter[name="operator"]').val();
    var tgl_doc_in = $('input.filter[name="tgl_doc_in"]').val();
    var periode1 = $('.form_cari').find('select[name=periode1] option:selected').val();
    var periode2 = $('.form_cari').find('select[name=periode2] option:selected').val();

    if (tgl_doc_in) {
        tgl_doc_in = Config._tanggalDb(tgl_doc_in, ' ', '-');
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "kandang/plotting_pelaksana/get_pagination/",
        data: {
            siklus: siklus,
            flock: flock,
            kandang: kandang,
            koordinator: koordinator,
            pengawas: pengawas,
            operator: operator,
            tgl_doc_in: tgl_doc_in,
            page_number: page_number,
            search: search,
            periode1: periode1,
            periode2: periode2,
        }
    }).done(function(data) {
        $("#daftar-do-table table tbody").html("");
        window.mydata = data;
        if (!empty(mydata.length)) {
            if (mydata.length > 0) {
                total_page = mydata[0].TotalRows;
                $("#total_page").text(total_page);
                var record_par_page = mydata[0].Rows;

                $.each(record_par_page, function(key, data) {
                    var _tgl_doc_in = (data.tgl_doc_in) ? Config._tanggalLocal(Config._getDateStr(new Date(data.tgl_doc_in)), '-', ' ') : '';

                    $("#daftar-do-table table tbody").append('<tr data-no-reg="' + data.no_reg + '" ><td>' + data.periode + '</td><td>' + _tgl_doc_in + '</td><td> Flock ' + data.flok_bdy + '</td><td> Kandang ' + data.kode_kandang + '</td><td>' + data.nama_koordinator + '</td><td>' + data.nama_pengawas + '</td><td>' + data.nama_operator + '</td></tr>');
                });
            }
            if (total_page == 1 || total_page == 0) {
                $("#next").prop('disabled', true);
            }
        } else {
            $("#page_number").text('0');
            $("#total_page").text('0');
            $("#next").prop('disabled', true);
        }
    }).fail(function(reason) {

    }).then(function(data) {});
}

(function() {
    'use strict';

    $('input.filter[name="tgl_doc_in"]').datepicker({
        dateFormat: 'dd M yy',
    })

    $("#next").on("click", function() {
        page_number = (page_number + 1);
        getReport(page_number);
    });

    $("#previous").on("click", function() {
        page_number = (page_number - 1);
        getReport(page_number);
    });

    $('input.filter').keyup(function() {
        this.value = this.value.toUpperCase();
        goSearch();
    });

    $('input.filter').change(function() {
        goSearch();
    });
    plottingPelaksana.setDataTable();
    //plottingPelaksana.setRowspan();
    getReport(page_number);
}());