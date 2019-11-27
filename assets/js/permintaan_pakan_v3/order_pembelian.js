var Plotting = {
    kode_farm: null,
    tgl_kirim: null,
    ekspedisi: {},
    getEkspedisi: function(kode_farm) {
        if (this.ekspedisi[kode_farm] == undefined) {
            var _ini = this;
            $.ajax({
                type: "POST",
                url: "api/general/ekspedisi",
                data: {
                    kode_farm: kode_farm
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
                        _ini.setEkspedisi(kode_farm, data.content);
                    } else {
                        bootbox.alert('Data tidak ditemukan');
                        return;
                    }
                },
                async: false,
                cache: false,
            });
        }
        return this.ekspedisi[kode_farm];
    },
    setEkspedisi: function(kode_farm, data) {
        this.ekspedisi[kode_farm] = data;
    },

    list_order_pembelian_pakan: function(elm, status) {
        /* status 1 menunjukkan rekap, kalau 0 menunjukkan trasaksi */
        /* cari tanggal pengiriman */
        var _startDate = $("input[name=startDate]").val();
        var _endDate = $("input[name=endDate]").val();
        var _error = 0;

        /* tanggal kirim harus sudah diisi */
        if (empty(_startDate) || empty(_endDate)) {
            _error++;
            toastr.error('Tanggal kirim harus diisi semua');
        }
        if (!_error) {
            /* cari semua parameter pencarian */
            var _paramPencarian = {};
            var _tglKirim = {};
            $(elm).closest('tr.search').find('input').each(function() {
                if (!empty($(this).val())) {
                    _paramPencarian[$(this).attr('name')] = $(this).val();
                }
            });

            _tglKirim['startDate'] = Config._tanggalDb(_startDate, ' ', '-');
            _tglKirim['endDate'] = Config._tanggalDb(_endDate, ' ', '-');
            /* cari list order pembelian */
            $.ajax({
                type: 'post',
                data: { cari: _paramPencarian, tglKirim: _tglKirim, rekap: status },
                url: 'permintaan_pakan_v3/pembelian_pakan/list_order',
                success: function(data) {
                    $('#div_list_order tbody').html(data);
                },
            });

        }
    },

    detail_pp: function(elm) {
        var _kode_farm = $(elm).data('kode_farm');
        var _tgl_kirim = $(elm).data('tgl_kirim');
        var _telat_ploting = [],
            _kandang;
        /** pastikan status_plotting bernilai 1 semua */
        var _tbody = $(elm).closest('tbody');
        _tbody.find('tr[data-kode_farm=' + _kode_farm + '][data-tgl_kirim=' + _tgl_kirim + ']').each(function() {
            if (!$(this).data('status_plotting')) {
                _kandang = $(this).data('noreg').substr(-2);
                _telat_ploting.push('<div>- Kandang ' + _kandang + '</div>');
            }
        });

        if (!empty(_telat_ploting)) {

            toastr.warning('Proses gagal. Plotting DO untukf kandang berikut mengalami keterlambatan timeline : <br >' + _telat_ploting.join(' '), 'Notifikasi');
            return;
        }
        $.ajax({
            type: 'post',
            data: { tgl_kirim: _tgl_kirim, kode_farm: _kode_farm },
            url: 'permintaan_pakan_v3/pembelian_pakan/detail_pp',
            success: function(data) {
                $('#div_transaksi_order_pembelian').html(data);
            },
        }).done(function() {
            /*
            $('#div_list_order').find('input[type=text]').priceFormat({
                prefix: '',
                centsLimit: 0,
                thousandsSeparator: ''
            });*/
            Plotting.kode_farm = _kode_farm;
            Plotting.tgl_kirim = _tgl_kirim;
            //var _sisa_plot = Plotting.get_sisa_plotting();
            var _bisa_plot = $('#div_detail_pp tbody').find('i.glyphicon-resize-horizontal').length;
            if (_bisa_plot >= 1) {
                $('#sisaPlottingPP').text(0);
            } else {
                var _tombolTrBaru = [
                    '<div class="row col-md-12">',
                    '<div class="col-md-2">',
                    '<span class="btn btn-default" onclick="Plotting.download_do(this)">Cetak DO</span>',
                    '</div>',
                    '</div>'
                ];
                $('#sisaPlottingPP').closest('.row').replaceWith(_tombolTrBaru.join(''));
            }

        });
    },
    tambah_do: function(elm) {
        var _sisa_plot = this.get_sisa_plotting();
        if (_sisa_plot <= 0) {
            bootbox.alert('Pengajuan PP telah diplotting');
            return;
        }
        var _no_urut = parseInt($(elm).closest('span.box-kendaraan').data('no_urut')) + 1;
        var _td = $(elm).closest('td');
        this.createBox(_td, _no_urut);
        var _remove_btn = '<span style="position:absolute;right:-20px;top:10px" class="glyphicon glyphicon-remove-sign" onclick="Plotting.remove_do(this)"></span>';
        $(elm).replaceWith(_remove_btn);
        this.update_sisa_plotting();
    },

    download_do: function(elm) {
        var _dos = [];
        $('#div_detail_pp table>tbody>tr').each(function() {
            $(this).find('td:first>div.link_span').each(function() {
                _dos.push($(this).text());
            });
        });
        $.redirect('permintaan_pakan_v3/pembelian_pakan/downloadPdfDO', { 'dos': _dos }, 'POST', '_blank');
    },

    createBox: function(_td, _no_urut) {
        var _nomerop = _td.data('nomerop');
        var _max = _td.data('max');
        var _tr = _td.closest('tr');
        var _tbody = _tr.closest('tbody');
        var _ekspedisi = _td.data('kode_ekspedisi');
        var _tds = _tbody.find('td.do_ekspedisi[data-nomerop="' + _nomerop + '"][data-kode_ekspedisi="' + _ekspedisi + '"]');
        var _index = 1;
        var _jmlPP, _jmlPlot, _sudahPlot = 0,
            _bisaPlot;
        _tds.each(function() {
            _sudahPlot = 0
            $(this).closest('tr').find('td.do_ekspedisi').each(function() {
                $(this).find('input').each(function() {
                    _sudahPlot += parseInt($(this).val());
                })
            })
            _jmlPP = $(this).closest('tr').find('td.jml_pp').text();
            _bisaPlot = _jmlPP - _sudahPlot;
            _jmlPlot = _bisaPlot < _max ? _bisaPlot : _max;
            var _box = ['<span style="margin-right:30px" data-maxpp="' + _jmlPP + '" data-max="' + _max + '" class="box-kendaraan plotting" data-no_urut="' + _no_urut + '">', '<input onchange="Plotting.checkMax(this)" value="' + _jmlPlot + '" type="text">'];
            if (_tds.length == _index) {
                _box.push('<span style="position:absolute;right:-20px;top:10px" class="glyphicon glyphicon-plus-sign" onclick="Plotting.tambah_do(this)"></span>');
            }
            _box.push('</span>');
            $(this).find('input').prop('readonly', 1);
            $(this).append(_box.join(''));
            _index++;
        });
        _tds.find('input[type=text]').priceFormat({
            prefix: '',
            centsLimit: 0,
            thousandsSeparator: ''
        });
    },
    showBox: function(elm) {
        var _no_urut = 1;
        var _td = $(elm);
        var _status_plotting = _td.data('status_plotting');
        if (!_status_plotting) {
            bootbox.alert('Proses gagal. Plotting DO mengalami keterlambatan timeline.');
            return;
        }
        var _nomerop = _td.data('nomerop');
        var _tr = _td.closest('tr');
        var _tbody = _tr.closest('tbody');
        var _ekspedisi = _td.data('kode_ekspedisi');
        var _tds = _tbody.find('td.do_ekspedisi[data-nomerop="' + _nomerop + '"][data-kode_ekspedisi="' + _ekspedisi + '"]');
        var _index = 1;
        var _jmlPP, _jmlPlot, _sudahPlot = 0,
            _bisaPlot;
        if (!_tds.find('input').length) {
            var _sisa_plot = this.get_sisa_plotting();
            if (_sisa_plot <= 0) {
                bootbox.alert('Pengajuan PP telah diplotting');
                return;
            }
            this.createBox(_td, _no_urut);
        } else {
            _tds.each(function() {
                $(this).children().remove();
            })
        }
        this.update_sisa_plotting();

    },
    periksa_kuantitas: function(elm) {
        var _tr = $(elm).closest('tr');
        var _maxSakPerKendaraan = _tr.data('max');
        if ($(elm).siblings('input[type=text]').val() >= _maxSakPerKendaraan) {
            $(elm).prop('checked', 0);
            toastr.warning('Tidak bisa digabung truk sudah penuh');
        }
    },
    checkMax: function(elm) {
        var _max = $(elm).closest('span').data('max');
        var _td = $(elm).closest('td');
        var _tr = _td.closest('tr');
        var _jmlPP = $(elm).closest('span').data('maxpp');
        var _sudahPlot = 0;
        _tr.find('input').not(elm).each(function() {
            _sudahPlot += parseInt($(this).val());
        });
        var _bisaPlot = _jmlPP - _sudahPlot;

        var _nilai = $(elm).val();
        var _batasMax = _bisaPlot < _max ? _bisaPlot : _max;
        if (_nilai > _batasMax) {
            $(elm).val(_batasMax);
        }
        this.update_sisa_plotting();
    },
    remove_do: function(elm) {
        var _no_urut = $(elm).closest('span.box-kendaraan').data('no_urut');
        var _td = $(elm).closest('td');
        var _nomerop = _td.data('nomerop');
        var _tr = _td.closest('tr');
        var _tbody = _tr.closest('tbody');
        var _ekspedisi = _td.data('kode_ekspedisi');
        var _tds = _tbody.find('td.do_ekspedisi[data-nomerop="' + _nomerop + '"][data-kode_ekspedisi="' + _ekspedisi + '"]');
        _tds.find('span.box-kendaraan[data-no_urut="' + _no_urut + '"]').remove();
        this.update_sisa_plotting();
    },
    get_sudah_plotting: function() {
        var _jmlPP = 0;
        $('#div_detail_pp').find('td.nama_pakan[data-sudah_plotting=0]').each(function() {
            $(this).siblings('td.do_ekspedisi').find('span.plotting').each(function() {
                _jmlPP += parseInt($(this).find('input').val());
            });
        });
        return _jmlPP;
    },
    get_total_harus_plotting: function() {
        var _jmlPP = 0;
        $('#div_detail_pp').find('td.nama_pakan[data-sudah_plotting=0]').each(function() {
            _jmlPP += parseInt($(this).siblings('td.jml_pp').text());
        });
        return _jmlPP;
    },
    get_sisa_plotting: function() {
        return this.get_total_harus_plotting() - this.get_sudah_plotting();
    },
    update_sisa_plotting: function() {
        $('#sisaPlottingPP').text(this.get_sisa_plotting());
    },
    simpan_do: function(elm) {
        var _error = 0,
            _minimum_ekspedisi = {},
            _ekspedisi_rit = {};
        var _tr, _kodepj, _do = {},
            _rit, _dibawah_ritase = 0,
            _td, _pesan,
            _tmp, _jml, _ekspedisi, _no_op, _no_urut;
        /*    
        var _sisa_plot = this.get_sisa_plotting();
        if (_sisa_plot > 0) {
            bootbox.alert('Proses penyimpanan dibatalkan. Terdapat PP yang belum diplotting');
            return;
        }*/
        /** periksa apakah ada do yang dibawah ritase miniimum */
        $('#div_detail_pp table>thead>tr:last>th[data-kode_ekspedisi]').each(function() {
            if (_minimum_ekspedisi[$(this).data('kode_ekspedisi')] == undefined) {
                _minimum_ekspedisi[$(this).data('kode_ekspedisi')] = $(this).data('minritase');
            }
        });
        $('#div_detail_pp').find('.box-kendaraan>input').not(':disabled').each(function() {
            _td = $(this).closest('td');
            _tr = _td.closest('tr');
            _kodepj = _tr.find('td.nama_pakan').data('kode_pakan');
            //$(this).siblings('td.do_ekspedisi').each(function() {
            _ekspedisi = _td.data('kode_ekspedisi');
            _no_op = _td.data('nomerop');
            /** grouping berdasarkan no_op, kode_ekspedisi dan nomerurut */
            if (_do[_no_op] == undefined) {
                _do[_no_op] = {};
            }
            if (_do[_no_op][_ekspedisi] == undefined) {
                _do[_no_op][_ekspedisi] = {};
            }
            _rit = _td.data('rit');
            _jml = parseInt($(this).val());
            _tmp = { kodepj: _kodepj, jumlah: _jml, rit: _rit };
            if (_do[_no_op][_ekspedisi][_rit] == undefined) {
                _do[_no_op][_ekspedisi][_rit] = [];
            }
            _do[_no_op][_ekspedisi][_rit].push(_tmp);
            if (_ekspedisi_rit[_ekspedisi] == undefined) {
                _ekspedisi_rit[_ekspedisi] = {};
            }
            if (_ekspedisi_rit[_ekspedisi][_rit] == undefined) {
                _ekspedisi_rit[_ekspedisi][_rit] = 0;
            }
            _ekspedisi_rit[_ekspedisi][_rit] += _jml;
        });
        if (empty(_ekspedisi_rit)) {
            bootbox.alert('Tidak ada DO yang diplotting');
            return false;
        }

        for (var i in _ekspedisi_rit) {
            for (var r in _ekspedisi_rit[i]) {
                if (_ekspedisi_rit[i][r] < _minimum_ekspedisi[i]) {
                    _dibawah_ritase = 1;
                }
            }
        }
        _pesan = 'Apakah Anda yakin melanjutkan proses penyimpanan plotting DO ';
        if (_dibawah_ritase) {
            _pesan = 'Terdapat ritase yang kurang dari min kapasitas muat <br />' + _pesan;
            _pesan = _pesan + '<br />' + '*) Ritase < min kapasitas muat membutuhkan approval VP'
        }
        /* lakukan penyimpanan */
        bootbox.confirm({
            title: 'Konfirmasi Penyimpanan',
            message: _pesan,
            buttons: {
                'cancel': {
                    label: 'Batal',
                    className: 'btn-default'
                },
                'confirm': {
                    label: 'Ya',
                    className: 'btn-danger'
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        type: 'post',
                        beforeSend: function() {
                            $(elm).remove();
                            bootbox.alert('Mohon tunggu, proses penyimpanan ....');
                        },
                        data: { do: _do, tgl_kirim: Plotting.tgl_kirim, kode_farm: Plotting.kode_farm, dibawah_ritase: _dibawah_ritase },
                        url: 'permintaan_pakan_v3/pembelian_pakan/simpan_do',
                        dataType: 'json',
                        success: function(data) {
                            bootbox.hideAll();
                            if (data.status) {
                                bootbox.alert('Proses plotting DO berhasil disimpan');
                                $('#spanCari').click().promise().done(function() {
                                    /** double klik elemen yang mengandung kode_farm dan tgl_kirim terpilih */
                                    $('#div_list_order tbody').find('tr[data-tgl_kirim=' + Plotting.tgl_kirim + '][data-kode_farm=' + Plotting.kode_farm + ']').eq(0).dblclick();
                                });
                            }
                        },
                    });
                }

            },
        });

    },
    do_pdf: function(no_do) {
        window.open('permintaan_pakan_v3/pembelian_pakan/do_pdf?no_do=' + no_do);
    },
    cetak_do: function(no_do, tgl_kirim, kode_ekspedisi) {
        $.ajax({
            type: 'post',
            data: { no_do: no_do, tgl_kirim: tgl_kirim, kode_ekspedisi: kode_ekspedisi },
            url: 'permintaan_pakan_v3/pembelian_pakan/detail_do',
            dataType: 'html',
            async: false,
            success: function(data) {
                var _options = {
                    title: 'Delivery Order <span class="btn btn-default" onclick="Plotting.do_pdf(\'' + no_do + '\',\'' + tgl_kirim + '\',\'' + kode_ekspedisi + '\')">Download PDF</span>',
                    message: data,
                    buttons: {},
                    className: 'largeWidth',
                };
                bootbox.dialog(_options);
            },
        });

    },

    popup_pindah_ritase: function(elm) {
        var _td = $(elm).closest('td');
        var _tr = _td.closest('tr');
        var _jmlrit = parseInt(_td.data('maxrit')) || 0;
        var _rit = parseInt(_td.data('rit')) || 0;
        var _kode_ekspedisi = _td.data('kode_ekspedisi');
        var _kode_farm = _td.data('kodefarm');
        var _select_rit = ['<option value="">- Pilih Ritase -</option>'];
        var _select_rit_ekspedisi = ['<option value="">- Pilih Ritase -</option>'];
        var _select_ekspedisi = ['<option value="">- Pilih Ekspedisi -</option>'];
        var _max_pindah = _td.find('input').val();
        var _i = parseInt(_td.data('awalrit'));
        var _akhir_rit = _jmlrit + _i;
        var _dataEkspedisi = this.getEkspedisi(_kode_farm);
        var _ini = this;

        $.when(_dataEkspedisi).done(function() {
            if (!empty(_dataEkspedisi)) {
                for (var _h in _dataEkspedisi) {
                    if (_kode_ekspedisi != _dataEkspedisi[_h]['kode']) {
                        _select_ekspedisi.push('<option data-max="' + _dataEkspedisi[_h]['max'] + '" data-min="' + _dataEkspedisi[_h]['min'] + '" value="' + _dataEkspedisi[_h]['kode'] + '">' + _dataEkspedisi[_h]['nama'] + '</option>');
                    }
                }
            }
            while (_i < _akhir_rit) {
                if (_i != _rit) {
                    _select_rit.push('<option value="' + _i + '">Rit ' + _i + '</option>');
                }
                _i++;
            }
            var _message = [
                '<div class="row">',
                '<div class="col-md-6"><div class="radio"><label><input type="radio" value="ritase" name="jenis_mutasi" checked>Pindahkan Ritase</label></div></div>',
                '<div class="col-md-6"><div class="radio"><label><input type="radio" value="ekspedisi" name="jenis_mutasi">Pindahkan Ekspedisi</label></div></div>',
                '</div>',
                '<form class="form form-horizontal">',
                '<div class="form-group">',
                '<label class="col-md-6">Nama Ekspedisi</label>',
                '<div class="col-md-6 new-line"><select onchange="Plotting.updateRitase(this)" name="ekspedisi" class="form-control" disabled >' + _select_ekspedisi.join('') + '</select></div>',
                '</div>',
                '<div class="form-group">',
                '<label class="ritase control-label col-md-6">Ritase</label>',
                '<div class="ritase col-md-6 new-line"><select name="ritase" class="form-control" >' + _select_rit.join('') + '</select></div>',
                '</div>',
                '<div class="form-group">',
                '<label class="ekspedisi control-label col-md-6 hide">Ritase</label>',
                '<div class="ekspedisi col-md-6 new-line hide"><select name="ritase" class="form-control" >' + _select_rit_ekspedisi.join('') + '</select></div>',
                '</div>',
                '<div class="form-group">',
                '<label class="control-label col-md-6">Sak yang Dipindah</label>',
                '<div class="col-md-6 new-line"><input name="jumlah_ritase" class="form-control" type="text" /></div>',
                '</div>',
                '</form>'
            ];
            var box = bootbox.dialog({
                message: _message,
                //closeButton: false,
                title: "Atur Ritase",
                buttons: {
                    success: {
                        label: "Atur",
                        className: "btn btn-primary",
                        callback: function(e) {
                            /** pastikan jumlah sak sudah dientry */

                            var _botbox = $(e.target).closest('.bootbox');

                            var _sakpindah = _botbox.find('input[name=jumlah_ritase]').val() || 0;
                            var _message = '',
                                _rittujuan = '',
                                _maxrittujuan = 0,
                                _ekspedisitujuan = '',
                                _jenismutasi = '',
                                _ekspedisiObj = {},
                                _totalplotting = 0;
                            if (_sakpindah <= 0) {
                                _message = 'Jumlah sak yang dipindah harus diisi > 0';
                            }

                            if (empty(_message)) {
                                /** pastikan total plottingnya < max ritase */
                                if (_botbox.find('select[name=ekspedisi]').is(':enabled')) {
                                    var _ekspedisitujuanopt = _botbox.find('select[name=ekspedisi] option:selected');
                                    _ekspedisitujuan = _botbox.find('select[name=ekspedisi]').val();
                                    _ekspedisiObj = { 'kode': _ekspedisitujuan, 'nama': _ekspedisitujuanopt.text(), 'min': _ekspedisitujuanopt.data('min'), 'max': _ekspedisitujuanopt.data('max') };
                                    if (empty(_ekspedisitujuan)) {
                                        _message = 'Ekspedisi harus dipilih';
                                    }
                                    _jenismutasi = 'ekspedisi';
                                } else {
                                    _jenismutasi = 'ritase';
                                }

                                if (_botbox.find('select[name=ritase]:visible').is(':enabled')) {
                                    _rittujuan = _botbox.find('select[name=ritase]:visible').val();
                                    if (empty(_rittujuan)) {
                                        _message = 'Ritase harus dipilih';
                                    }
                                }

                                if (_jenismutasi == 'ritase') {
                                    _maxrittujuan = $('#div_detail_pp table>thead>tr>th[data-rit=' + _rittujuan + ']').data('maxritase');
                                } else {
                                    if (empty(_rittujuan)) {
                                        _maxrittujuan = $('#div_detail_pp table>thead>tr>th[data-rit=' + _rittujuan + ']').data('maxritase');
                                    } else {
                                        _maxrittujuan = _botbox.find('select[name=ekspedisi] option:selected').data('max');
                                    }
                                }
                                if (!empty(_rittujuan)) {
                                    $('#div_detail_pp table>tbody>tr>td[data-rit=' + _rittujuan + '] input').each(function() {
                                        _totalplotting += parseInt($(this).val());
                                    })
                                }
                                var _sisa_plotting = _maxrittujuan - _totalplotting;

                                if (_sisa_plotting < _sakpindah) {
                                    _message = 'Sak yang diplotting melebihi kapasitas maksimal ritase';
                                }
                            }

                            if (!empty(_message)) {
                                toastr.warning(_message, 'Notifikasi');
                                return false;
                            } else {
                                _ini.pindahkan_plotting(elm, _jenismutasi, _rittujuan, _sakpindah, _ekspedisiObj);
                                return true;
                            }
                        }
                    }
                }
            });

            box.bind('shown.bs.modal', function() {
                $(this).find('input[name=jumlah_ritase]').numeric({ max: _max_pindah });
                var _form = $(this);
                $(this).find(':radio').change(function() {
                    if ($(this).val() == 'ritase') {
                        _form.find('select[name=ekspedisi]').prop('disabled', 1);
                        _form.find('select[name=ritase]').prop('disabled', 0);
                        _form.find('.ritase').removeClass('hide');
                        _form.find('.ekspedisi').addClass('hide');
                    }
                    if ($(this).val() == 'ekspedisi') {
                        _form.find('select[name=ekspedisi]').prop('disabled', 0);
                        _form.find('select[name=ritase]').prop('disabled', 1);
                        _form.find('.ritase').addClass('hide');
                        _form.find('.ekspedisi').removeClass('hide');
                    }
                });
            });
        });
    },
    updateRitase: function(elm) {
        var _nilai = $(elm).val();
        var _div_ritase = $(elm).closest('.bootbox-body').find('div.ekspedisi');
        _div_ritase.find('select option:gt(0)').remove();
        _div_ritase.find('select').prop('disabled', 1);
        if (!empty(_nilai)) {
            var _thEkspedisi = $('#div_detail_pp table>thead').find('tr:last').find('th[data-kode_ekspedisi=' + _nilai + ']');
            if (_thEkspedisi.length) {
                _div_ritase.find('select[name=ritase]').prop('disabled', 0);
                _thEkspedisi.each(function() {
                    _div_ritase.find('select').append('<option value="' + $(this).text() + '">' + $(this).text() + '</option>');
                })
            }

        }
    },
    pindahkan_plotting: function(elm, _jenismutasi, _rittujuan, _sakpindah, _ekspedisiObj) {
        var _td = $(elm).closest('td');
        var _tr = _td.closest('tr');
        var _inputasal = _td.find('input');
        var _tdtujuan, _inputtujuan, _plotingtujuan;
        var _ploting_asal = parseInt(_inputasal.val());
        if (_sakpindah >= _ploting_asal) {
            _td.html('');
        } else {
            _inputasal.val(_ploting_asal - _sakpindah);
        }

        if (_jenismutasi != 'ritase') {
            /** cek apakah sudah ada ekspedisinya atau belum */
            var _tbody = _tr.closest('tbody');
            var _table = _tbody.closest('table');
            var _thead = _table.find('thead');
            var _ekspedisiTujuan = _thead.find('tr>th[data-kode_ekspedisi=' + _ekspedisiObj['kode'] + ']');
            if (!_ekspedisiTujuan.length) {
                var _ritterakhir = parseInt(_thead.find('tr:last>th:last').data('rit')) + 1;
                var _tdterakhir, _thterakhir;
                _thterakhir = _thead.find('tr:first>th:last').clone();
                _thterakhir.html(_ekspedisiObj['nama']);
                _thead.find('tr:first').append(_thterakhir);

                _thead.find('tr:last').append($('<th data-minritase="' + _ekspedisiObj['min'] + '" data-maxritase="' + _ekspedisiObj['max'] + '" data-kode_ekspedisi="' + _ekspedisiObj['kode'] + '" data-rit="' + _ritterakhir + '">' + _ritterakhir + '</th>'));
                _tbody.find('tr').each(function() {
                    _tdterakhir = $(this).find('td:last').clone();
                    _tdterakhir.attr('data-maxritase', _ekspedisiObj['max']);
                    _tdterakhir.attr('data-minritase', _ekspedisiObj['min']);
                    _tdterakhir.attr('data-maxrit', 1);
                    _tdterakhir.attr('data-rit', _ritterakhir);
                    _tdterakhir.attr('data-awalrit', _ritterakhir);
                    _tdterakhir.attr('data-kode_ekspedisi', _ekspedisiObj['kode']);
                    _tdterakhir.html('');
                    _tdterakhir.insertAfter($(this).find('td:last'));
                });
                _rittujuan = _ritterakhir;
            }
        }

        _tdtujuan = _tr.find('td[data-rit=' + _rittujuan + ']');
        _inputtujuan = _tdtujuan.find('input');
        if (_inputtujuan.length) {
            _plotingtujuan = parseInt(_inputtujuan.val());
            _inputtujuan.val(_plotingtujuan + parseInt(_sakpindah));
        } else {
            var _content = [
                '<span class="box-kendaraan readonly" data-no_urut=""><input type="text" value="' + _sakpindah + '" readonly></span>',
                '&nbsp;&nbsp;&nbsp;',
                '<i class="glyphicon glyphicon-resize-horizontal" onclick="Plotting.popup_pindah_ritase(this)"></i>'
            ];
            _tdtujuan.html(_content.join(''));
        }
    }
};
(function() {
    'use strict';
    $("input[name=startDate]").datepicker({
        //  defaultDate: "+1w",
        dateFormat: 'dd M yy',
        onClose: function(selectedDate) {
            $("input[name=endDate]").datepicker("option", "minDate", selectedDate);
        }
    });
    $("input[name=endDate]").datepicker({
        //  defaultDate: "+1w",
        dateFormat: 'dd M yy',
        onClose: function(selectedDate) {
            $("input[name=startDate]").datepicker("option", "maxDate", selectedDate);
        }
    });
    $('#spanCari').click();
}())