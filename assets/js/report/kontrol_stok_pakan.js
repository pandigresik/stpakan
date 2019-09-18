var KSP = {
    listFarm: null,
    loadContent: function(elm) {
        var _href = $(elm).attr('href');
        var _elmTarget = $(_href);

        if (!_elmTarget.children().length) {
            var _url = $(elm).data('url');
            $.get(_url, {}, function(data) {
                if (data.status) {
                    _elmTarget.html(data.content);
                } else {
                    bootbox.alert(data.message);
                }
            }, 'json').done(function() {
                if (_url == 'report/overview') {
                    _elmTarget.find('.div_farm_overview').each(function() {
                        $(this).click();
                    })
                }
            });
        }
    },
    getListFarm: function() {
        var tmp;
        if (empty(KSP.listFarm)) {
            $.ajax({
                type: 'get',
                url: 'report/report/userFarm',
                data: {},
                dataType: 'json',
                async: false,
                cache: true,
            }).done(function(data) {
                if (data.status) {
                    KSP.listFarm = data.content;
                    tmp = KSP.listFarm;
                }
            });
        } else {
            tmp = KSP.listFarm;
        }
        return tmp;
    },
    detail_kandang: function(kode_kandang, kode_farm) {
        var _flock = $('select[name=tgldocin]');
        _flock.find('option:not(:first)').remove();
        $.ajax({
            type: 'post',
            data: { kode_kandang: kode_kandang, kode_farm: kode_farm },
            url: 'report/report/detail_kandang',
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data.status) {
                    var c = data.content;
                    // tampilkan flock yang bisa dipilih
                    var _opt = [],
                        _c;
                    for (var i in c) {
                        _c = c[i];
                        _opt.push('<option data-flokbdy="' + _c.flok_bdy + '" data-jmlpopulasi="' + _c.jml_populasi + '" data-docin="' + _c.tgl_doc_in + '" value="' + _c.no_reg + '">' + Config._tanggalLocal(_c.tgl_doc_in, '-', ' ') + '&nbsp;&nbsp;(&nbsp;' + _c.periode_siklus + ' )</option>');
                    }

                    _flock.append(_opt.join(''));
                    _flock.find('option:eq(1)').prop('selected', 1);
                    KSP.showDetailKandang(_flock);
                } else {
                    toastr.error('Data tidak ditemukan');
                }

            },
        });
    },
    showDetailKandang: function(elm) {
        var ini = $(elm);
        var _error = 0;
        if (empty(ini.val())) {
            _error++;
            toastr.warning('Pilih salah satu tanggal doc in');
        }
        if (!_error) {
            var _terpilih = ini.find('option:selected');
            $('label[name=flock]').text(_terpilih.data('flokbdy'));
            $('label[name=populasi]').text(number_format(_terpilih.data('jmlpopulasi'), 0, ',', '.'));
        }
    },

    showKandang: function(elm, _action) {
        var kodefarm = $(elm).data('kodefarm');
        var awalDocin = $(elm).data('awaldocin');
        var akhirDocin = $(elm).data('akhirdocin');
        var kodeSiklus = $(elm).data('kodesiklus');
        var url = 'home/kertas_kerja/list_kandang_all';
        var _tipe = 'kontrol_pp';

        var where = { where: ' ks.kode_farm=\'' + kodefarm + '\' and ks.kode_siklus=\'' + kodeSiklus + '\'', action: _action, tipe: _tipe };

        /* cek apakah detailnya sudah tampil atau belum */
        var _detailElm = $(elm).next('div.detailkandang');
        if (!_detailElm.length) {
            /* load dari server */
            $.ajax({
                type: 'post',
                dataType: 'html',
                data: where,
                url: url,
                beforeSend: function() {
                    $('<div class="detailkandang">Mohon tunggu .....</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailkandang').remove();
                    $(data).insertAfter($(elm)).promise().done(function() {
                        _detailElm = $(elm).next('div.detailkandang');
                        _detailElm.find('.div_detailkandang').each(function() {
                            //$(this).click();
                        });
                    });
                },
            }).done(function() {
                /** load overview pemakaian glangsing perfarm */
                if (_action == 'KSP.showDetailPemakaianGlangsing(this)') {
                    $.post('report/pemakaian_glangsing/overview_glangsing', { 'kode_siklus': kodeSiklus, 'kode_farm': kodefarm }, function(data) {
                        $(data).insertBefore(_detailElm);
                    }, 'html')
                }
            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    showDetailRencanaRealisasi: function(elm) {
        var noreg = $(elm).data('noreg');
        var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'), ' ', '-');
        var _detailElm = $(elm).next('div.detailrhk');
        if (!_detailElm.length) {
            $.ajax({
                url: 'report/rencana_realisasi_pemakaian/detail',
                type: 'post',
                data: { noreg: noreg, tgl_docin: tgl_docin },
                dataType: 'html',
                async: false,
                beforeSend: function() {
                    $('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailrhk').html(data);
                }
            }).done(function() {
                $(elm).next('div.detailrhk').find('.page').hide();
                $(elm).next('div.detailrhk').find('.screen_1').show();
                //$(elm).next('div.detailrhk').find('a[data-toogle=tooltip]').tooltip();

            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    showDetailRealisasiDoc: function(elm) {
        var noreg = $(elm).data('noreg');
        var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'), ' ', '-');
        var _detailElm = $(elm).next('div.detailrhk');
        if (!_detailElm.length) {
            $.ajax({
                url: 'report/realisasi_doc/detail',
                type: 'post',
                data: { noreg: noreg, tgl_docin: tgl_docin },
                dataType: 'html',
                async: false,
                beforeSend: function() {
                    $('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailrhk').html(data);
                }
            }).done(function() {
                $(elm).next('div.detailrhk').find('.page').hide();
                $(elm).next('div.detailrhk').find('.screen_1').show();

            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    showOverviewApproval: function(elm) {
        var kodefarm = $(elm).data('kodefarm');
        var kodeSiklus = $(elm).data('kodesiklus');
        var url = 'report/overview/detail';

        /* cek apakah detailnya sudah tampil atau belum */
        var _detailElm = $(elm).next('div.detailkandang');
        if (!_detailElm.length) {
            /* load dari server */
            $.ajax({
                type: 'post',
                dataType: 'html',
                data: { kode_farm: kodefarm, kode_siklus: kodeSiklus },
                url: url,
                beforeSend: function() {
                    $('<div class="detailkandang">Mohon tunggu .....</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailkandang').remove();
                    $(data).insertAfter($(elm)).promise().done(function() {
                        _detailElm = $(elm).next('div.detailkandang');

                    });
                },
            }).done(function() {
                // update summary jumlah yang harus diapprove
                var _summaryApproval = 0;
                _detailElm.find('.div_detailkandang').each(function() {
                    _summaryApproval += parseInt($(this).find('span.pull-right').text());
                });
                $('<span class="label label-warning pull-right">' + _summaryApproval + '</span>').appendTo($(elm));
            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    showDetailPerencanaanAwal: function(elm) {
        var noreg = $(elm).data('noreg');
        var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'), ' ', '-');
        var _detailElm = $(elm).next('div.detailrhk');
        if (!_detailElm.length) {
            $.ajax({
                url: 'report/perencanaan_awal/detail',
                type: 'post',
                data: { noreg: noreg, tgl_docin: tgl_docin },
                dataType: 'html',
                async: false,
                beforeSend: function() {
                    $('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailrhk').html(data);
                }
            }).done(function() {
                $(elm).next('div.detailrhk').find('.page').hide();
                $(elm).next('div.detailrhk').find('.screen_1').show();

            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    showDetailRencanaPengiriman: function(elm) {
        $(elm).next().toggle();
    },

    showDetailRealisasiPanen: function(elm) {
        var noreg = $(elm).data('noreg');
        var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'), ' ', '-');
        var _detailElm = $(elm).next('div.detailrhk');
        if (!_detailElm.length) {
            $.ajax({
                url: 'report/realisasi_panen/detail',
                type: 'post',
                data: { noreg: noreg, tgl_docin: tgl_docin },
                dataType: 'html',
                async: false,
                beforeSend: function() {
                    $('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailrhk').html(data);
                }
            }).done(function() {
                $(elm).next('div.detailrhk').find('.page').hide();
                $(elm).next('div.detailrhk').find('.screen_1').show();

            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    showDetailPemakaianGlangsing: function(elm) {
        var noreg = $(elm).data('noreg');
        var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'), ' ', '-');
        var _detailElm = $(elm).next('div.detailrhk');
        if (!_detailElm.length) {
            $.ajax({
                url: 'report/pemakaian_glangsing/detail',
                type: 'post',
                data: { noreg: noreg, tgl_docin: tgl_docin },
                dataType: 'html',
                async: false,
                beforeSend: function() {
                    $('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
                },
                success: function(data) {
                    $(elm).next('div.detailrhk').html(data);
                }
            }).done(function() {
                $(elm).next('div.detailrhk').find('.page').hide();
                $(elm).next('div.detailrhk').find('.screen_1').show();

            });
        } else {
            if (_detailElm.is(':visible')) {
                _detailElm.hide();
            } else {
                _detailElm.show();
            }
        }
    },

    checkAll: function(elm) {
        var _div = $(elm).closest('div.div_detailkandang');
        var _div_next = _div.next('div.detailrhk');
        if ($(elm).is(':checked')) {
            _div_next.find(':checkbox').prop('checked', 1);
        } else {
            _div_next.find(':checkbox').prop('checked', 0);
        }

    },
    next: function(elm) {
        var _domain = $(elm).closest('.detailrhk');
        var _c = $(elm).data('current');
        var _min = $(elm).data('min');
        var _max = $(elm).data('max');
        var _next = parseInt(_c) + 1;

        if (_c < _max) {
            //$('.slider-table').data('current', _next);
            _domain.find('.screen_' + _c).hide();
            _domain.find('.screen_' + _next).show();
            $(elm).data('current', _next);
            $(elm).siblings().data('current', _next);
        } else {
            toastr.warning('Tombol next disable, silakan pilih tombol prev');
        }
    },

    prev: function(elm) {
        var _domain = $(elm).closest('.detailrhk');
        var _c = $(elm).data('current');
        var _min = $(elm).data('min');
        var _max = $(elm).data('max');
        var _prev = parseInt(_c) - 1;

        if (_c > _min) {
            //$('.slider-table').data('current', _prev);
            _domain.find('.screen_' + _c).hide();
            _domain.find('.screen_' + _prev).show();
            $(elm).data('current', _prev);
            $(elm).siblings().data('current', _prev);
        } else {
            toastr.warning('Tombol prev disable, silakan pilih tombol next');
        }
    },

    showPPDetail: function(elm) {
        var _no_pp = $(elm).data('no_pp');
        var _message = ['<div>Performa Kandang</div>'];
        $.ajax({
            url: 'permintaan_pakan_v2/permintaan_pakan/performaKandang',
            data: { no_lpb: _no_pp },
            type: 'get',
            dataType: 'json',
            success: function(data) {
                _message.push(data.content.performa_kandang);
            }
        }).done(function() {
            var _data = { 'no_lpb': _no_pp, 'status': 'A', _grup_farm: 'bdy' };
            $.ajax({
                url: 'permintaan_pakan_v2/permintaan_pakan/list_kebutuhan_pakan',
                data: _data,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    _message.push('<div>Detail Permintaan Pakan</div>');
                    _message.push(data.content.kebutuhan_internal);
                    var _options = {
                        title: 'Data PP ' + _no_pp,
                        message: _message.join(''),
                        className: 'largeWidth',
                    };

                    bootbox.dialog(_options);
                }
            });
        });
    },
    showPanenDetail: function(elm) {
        var _noreg = $(elm).data('noreg');
        var _tgl_panen = $(elm).data('tgl_panen');
        $.ajax({
            beforeSend: function() {

            },
            type: 'post',
            dataType: 'html',
            data: { noreg: _noreg, tgl_panen: _tgl_panen },
            url: 'report/rencana_realisasi_pemakaian/detail_panen',
            success: function(data) {
                var _options = {
                    title: 'Detail Realisasi Panen ' + _noreg + ' Tanggal ' + Config._tanggalLocal(_tgl_panen, '-', ' '),
                    message: data,
                    className: 'largeWidth',
                };

                bootbox.dialog(_options);
            },
        });

    },
    showNextTrHidden: function(elm) {
        var _tr = $(elm).closest('tr');
        var _detailElm = _tr.next('tr.detail_hidden');
        if (_detailElm.is(':hidden')) {
            _detailElm.removeClass('hide');
        } else {
            _detailElm.addClass('hide');
        }
    },
    detailTimbang: function(elm) {
        var _kode_siklus = $(elm).data('kode_siklus');
        var _kode_farm = $(elm).data('kode_farm');
        var _jenis = $(elm).data('jenis');
        $.ajax({
            beforeSend: function() {

            },
            type: 'post',
            dataType: 'html',
            data: { kode_siklus: _kode_siklus, jenis: _jenis, kode_farm: _kode_farm },
            url: 'report/perencanaan_awal/detail_pallet',
            success: function(data) {
                var _options = {
                    title: 'Detail ' + _jenis,
                    message: data,
                    /*className: 'largeWidth',*/
                };

                bootbox.dialog(_options);
            },
        });
    },

    hideShowNextColumn: function(elm) {
        var _tr = $(elm).closest('tr');
        var _th = $(elm).closest('th');
        var _next_th = _th.nextAll().length;
        var _action = 'show';
        if ($(elm).hasClass('glyphicon-minus')) {
            _next_th = -1 * _next_th;
            _action = 'hide';
        }
        var _colspan = parseInt(_tr.prev().find('th:last').attr('colspan')) + _next_th;
        _tr.prev().find('th:last').attr('colspan', _colspan);

        if (_action == 'hide') {
            _tr.closest('table').find('.hide_column').hide();
        } else {
            _tr.closest('table').find('.hide_column').show();
        }

        $(elm).toggleClass('glyphicon-minus glyphicon-plus');
    }

};
var Approval = {
    _prosesServer: 0,
    _keteranganDO: '',
    approveKadiv: function(elm) {
        var _url = $(elm).data('url');
        var _kode_siklus = $(elm).data('kode_siklus');
        var _kode_farm = $(elm).data('kode_farm');
        $.post(_url, { kode_farm: _kode_farm, kode_siklus: _kode_siklus }, function(html) {
            bootbox.dialog({
                title: 'Permintaan Pakan',
                message: html,
                className: 'largeWidth',
                buttons: {
                    'cancel': {
                        label: 'Reject',
                        className: 'btn-danger',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _listApproval = {},
                                _fitur;
                            _checked.each(function() {
                                _fitur = $(this).data('fitur');
                                if (_listApproval[_fitur] == undefined) {
                                    _listApproval[_fitur] = [];
                                }
                                _listApproval[_fitur].push($(this).data('kirim'));
                            });
                            Overview.rejectFitur(_listApproval, elm, function(elm) {
                                $(elm).closest('td').html('Reject');
                            });
                        }
                    },
                    'confirm': {
                        label: 'Approve',
                        className: 'btn-default',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _listPP = [];
                            _checked.each(function() {
                                _listPP.push($(this).data('no_lpb'));
                            });


                            $.ajax({
                                type: 'post',
                                beforeSend: function() {

                                },
                                dataType: 'json',
                                data: { no_pp: _listPP },
                                url: 'permintaan_pakan_v2/permintaan_pakan/approve_pp_budidaya',
                                success: function(data) {
                                    if (data.status) {
                                        toastr.success(data.message);
                                        $(elm).closest('td').html('Approve');

                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                            }).done(function() {

                            });

                        }
                    }
                },
            });
        }, 'html');

    },
    approvePh: function(elm) {
        var _url = $(elm).data('url');
        var _kode_farm = $(elm).data('kode_farm');
        $.post(_url, { kode_farm: _kode_farm }, function(html) {
            bootbox.dialog({
                title: 'Pengajuan Harga',
                message: html,
                className: 'largeWidth',
                buttons: {
                    'cancel': {
                        label: 'Reject',
                        className: 'btn-danger',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _listApproval = {},
                                _fitur;
                            _checked.each(function() {
                                _fitur = $(this).data('fitur');
                                if (_listApproval[_fitur] == undefined) {
                                    _listApproval[_fitur] = [];
                                }
                                _listApproval[_fitur].push($(this).data('kirim'));
                            });
                            Overview.rejectFitur(_listApproval, elm, function(elm) {
                                $(elm).closest('td').html('Reject');
                            });
                        }
                    },
                    'confirm': {
                        label: 'Approve',
                        className: 'btn-default',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _data = [];
                            _checked.each(function() {
                                _data.push($(this).data('kirim'));
                            });

                            $.ajax({
                                type: 'post',
                                beforeSend: function() {

                                },
                                dataType: 'json',
                                data: { 'data': _data, 'nextStatus': 'A' },
                                url: 'sales_order/pengajuan_harga/approval',
                                success: function(data) {
                                    if (data.status) {
                                        toastr.success(data.message);
                                        $(elm).closest('td').html('Approve');

                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                            }).done(function() {

                            });

                        }
                    }
                },
            });
        }, 'html');

    },

    approvePPSK: function(elm) {
        var _url = $(elm).data('url');
        var _kode_farm = $(elm).data('kode_farm');
        $.post(_url, { kode_farm: _kode_farm }, function(html) {
            bootbox.dialog({
                title: 'Permintaan Glangsing Bekas Pakai',
                message: html,
                className: 'largeWidth',
                buttons: {
                    'cancel': {
                        label: 'Reject',
                        className: 'btn-danger',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _listApproval = {},
                                _fitur;
                            _checked.each(function() {
                                _fitur = $(this).data('fitur');
                                if (_listApproval[_fitur] == undefined) {
                                    _listApproval[_fitur] = [];
                                }
                                _listApproval[_fitur].push($(this).data('kirim'));
                            });
                            Overview.rejectFitur(_listApproval, elm, function(elm) {
                                $(elm).closest('td').html('Reject');
                            });
                        }
                    },
                    'confirm': {
                        label: 'Approve',
                        className: 'btn-default',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _data = [];
                            _checked.each(function() {
                                _data.push({ 'no_ppsk': $(this).data('no_ppsk') });
                            });

                            $.ajax({
                                type: 'post',
                                beforeSend: function() {

                                },
                                dataType: 'json',
                                data: { 'data': _data, 'nextStatus': 'A' },
                                url: 'report/kontrol_stok_glangsing/updatePpsk',
                                success: function(data) {
                                    if (data.status) {
                                        toastr.success(data.message);
                                        $(elm).closest('td').html('Approve');

                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                            }).done(function() {

                            });

                        }
                    }
                },
            });
        }, 'html');

    },

    prosesPloting: function(elm) {
        var _url = $(elm).data('url');
        var _kode_siklus = $(elm).data('kode_siklus');
        var _kode_farm = $(elm).data('kode_farm');
        $.post(_url, { kode_farm: _kode_farm, kode_siklus: _kode_siklus }, function(html) {
            bootbox.dialog({
                title: 'Plotting Pelaksana',
                message: html,
                className: 'largeWidth',
                buttons: {
                    'cancel': {
                        label: 'Batal',
                        className: 'btn-default'
                    },
                    'confirm': {
                        label: 'Approve',
                        className: 'btn-danger',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var dataKirim = [];
                            _checked.each(function() {
                                dataKirim.push($(this).data('kirim'));
                            });

                            $.ajax({
                                url: 'kandang/plotting_pelaksana/ack',
                                data: { data: dataKirim },
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
                                            $(elm).closest('td').html('Approve');
                                        });
                                    } else {
                                        bootbox.alert(data.message);
                                    }

                                }
                            });
                        }
                    }
                },
            });
        }, 'html');

    },

    approveDO: function(elm) {
        var _url = $(elm).data('url');
        var _kode_siklus = $(elm).data('kode_siklus');
        var _kode_farm = $(elm).data('kode_farm');
        $.post(_url, { kode_farm: _kode_farm, kode_siklus: _kode_siklus }, function(html) {
            bootbox.dialog({
                title: 'Plotting DO Pakan',
                message: html,
                className: 'largeWidth',
                buttons: {
                    'cancel': {
                        label: 'Reject',
                        className: 'btn-danger',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var _listApproval = {},
                                _fitur;
                            _checked.each(function() {
                                _fitur = $(this).data('fitur');
                                if (_listApproval[_fitur] == undefined) {
                                    _listApproval[_fitur] = [];
                                }
                                _listApproval[_fitur].push($(this).data('kirim'));
                            });
                            Overview.rejectFitur(_listApproval, elm, function(elm) {
                                $(elm).closest('td').html('Reject');
                            });
                        }
                    },
                    'confirm': {
                        label: 'Approve',
                        className: 'btn-default',
                        callback: function(e) {
                            var _botbox = $(e.target).closest('.bootbox');
                            var _checked = _botbox.find(':checked');
                            if (!_checked.length) {
                                bootbox.alert("Belum ada checkbox yang dipilih");
                                return false;
                            }
                            var dataKirim = [],
                                nextstatus;
                            _checked.each(function() {
                                dataKirim.push($(this).data('kirim'));
                            });
                            nextstatus = 'Approve';

                            $.ajax({
                                url: 'permintaan_pakan_v2/pembelian_pakan/approvereject',
                                data: { farmkirim: dataKirim },
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
                                            $(elm).closest('td').html('Approve');
                                        });
                                    } else {
                                        bootbox.alert(data.message);
                                    }

                                }
                            });
                        }
                    }
                },
            });
        }, 'html');

    },

    rejectDO: function(elm) {
        this._keteranganDO = '';
        var _kode_farm = $(elm).data('kode_farm');
        var _tgl_kirim = $(elm).data('tgl_kirim');
        var _content = ['<div class="dialog_reject">',
            '<div class="col-md-12">Mohon entri alasan reject (min. 10 karakter dan max. 100 karakter)</div>',
            '<div class="col-md-12">',
            '<textarea name="keterangan_reject" class="col-md-10" maxlength=100 onkeyup="Approval.aktifkanBtnDO(this)"></textarea>',
            '</div>',
            '<div class="col-md-12 new-line">',
            '<div class="col-md-2">',
            '<div name="simpanRejectBtn" class="btn btn-default disabled" data-kode_farm="' + _kode_farm + '" data-tgl_kirim="' + _tgl_kirim + '" onclick="Approval.approveRejectDO(this)">Simpan</div>',
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
    aktifkanBtnDO: function(elm) {
        var ini = $(elm);
        var _p = ini.closest('.dialog_reject');
        if ($.trim(ini.val()).length >= 10) {
            _p.find('div[name=simpanRejectBtn]').removeClass('disabled');
        } else {
            _p.find('div[name=simpanRejectBtn]').addClass('disabled');
        }
    },
    approveRejectDO: function(elm) {
        if (elm != undefined) {
            var ini = $(elm);
            var _p = ini.closest('.dialog_reject');
            if (_p.length) {
                var _ket = $.trim(_p.find('textarea[name=keterangan_reject]').val());
                this._keteranganDO = _ket;
            }
        }

        nextstatus = empty(this._keteranganDO) ? 'Approve' : 'Reject';
        var _farmkirim = [],
            _kode_farm, _tgl_kirim,
            _url, nextstatus;
        _kode_farm = $(elm).data('kode_farm');
        _tgl_kirim = $(elm).data('tgl_kirim');
        _farmkirim.push({
                kode_farm: _kode_farm,
                tgl_kirim: _tgl_kirim,
            })
            /* simpan ke database */

        _url = 'permintaan_pakan_v2/pembelian_pakan/approvereject';
        $.post(_url, { farmkirim: _farmkirim, keterangan: this._keteranganDO }, function(data) {
            if (data.status) {
                bootbox.alert(data.message, function() {
                    bootbox.hideAll();
                    /** update semua status approval DO berdasarkan tgl_kirim dan kode_farm */
                    $('span.btn[data-kode_farm=' + _kode_farm + '][data-tgl_kirim=' + _tgl_kirim + ']').each(function() {
                        $(this).closest('td').html(nextstatus);
                    })
                });
            } else {
                bootbox.alert(data.message);
            }
        }, 'json');
    },
};
var Overview = {
    approve: function(elm) {
        var _topdiv = $(elm).closest('.detailkandang');
        var _checked = _topdiv.find('table>tbody :checked');
        if (!_checked.length) {
            bootbox.alert('Belum ada yang dipilih');
            return;
        }
        var _listApproval = {},
            _fitur, _dataKirim, _jmlApprove = 0;
        _checked.each(function() {
            _fitur = $(this).data('fitur');
            if (_listApproval[_fitur] == undefined) {
                _listApproval[_fitur] = [];
                _jmlApprove++;
            }
            _dataKirim = $(this).data('kirim');
            _listApproval[_fitur].push(_dataKirim);
        })

        if (_jmlApprove > 1) {
            this.multipleApprove(_listApproval, elm);
        } else {
            this.approveFitur(_listApproval, elm);
        }
    },

    reject: function(elm) {
        var _topdiv = $(elm).closest('.detailkandang');
        var _checked = _topdiv.find('table>tbody :checked');
        if (!_checked.length) {
            bootbox.alert('Belum ada yang dipilih');
            return;
        }
        var _listApproval = {},
            _fitur, _dataKirim, _jmlApprove = 0;
        _checked.each(function() {
            _fitur = $(this).data('fitur');
            if (_listApproval[_fitur] == undefined) {
                _listApproval[_fitur] = [];
                _jmlApprove++;
            }
            _dataKirim = $(this).data('kirim');
            _listApproval[_fitur].push(_dataKirim);
        })

        if (_jmlApprove > 1) {
            this.multipleReject(_listApproval, elm);
        } else {
            this.rejectFitur(_listApproval, elm);
        }
    },
    multipleReject: function(_listApproval, elm) {
        bootbox.alert('multipleReject');
    },

    rejectFitur: function(_listApproval, elm, callback) {
        var _fitur = '',
            _message, _data, _url, _dataKirim, _title;
        for (var i in _listApproval) {
            _fitur = i;
            _data = _listApproval[i];
        }
        _message = ['<div>Mohon mengisi alasan reject', '', '(min.10 karakter)</div><div><textarea  class="form-control" id="keterangan_reject"></textarea></div>'];
        switch (_fitur) {
            case 'plotting_pelaksana':
                _message = 'Mohon maaf fasilitas reject tidak tersedia untuk fitur plotting pelaksana.';
                _title = 'Plotting Pelaksana';
                _dataKirim = { 'data': _data };
                break;
            case 'plotting_do_pakan':
                _message[1] = 'plotting DO pakan';
                _title = 'Plotting DO Pakan';
                _dataKirim = { 'farmkirim': _data, 'keterangan': '' };
                _url = 'permintaan_pakan_v2/pembelian_pakan/approvereject';
                break;
            case 'ppsk':
                _message[1] = 'permintaan glangsing bekas pakai';
                _title = 'Permintaan Glangsing Bekas Pakai';
                _dataKirim = { 'data': [], 'nextStatus': 'RJ', 'keterangan_reject': '' };
                for (var i in _data) {
                    _dataKirim['data'].push({ 'no_ppsk': _data[i]['no_ppsk'] });
                }
                _url = 'report/kontrol_stok_glangsing/updatePpsk';
                break;
            case 'pengajuan_harga':
                _message[1] = 'pengajuan harga glangsing';
                _title = 'Pengajuan Harga Glangsing';
                _dataKirim = { 'data': _data, 'nextStatus': 'RJ', 'keterangan_reject': '' };
                _url = 'sales_order/pengajuan_harga/approval';
                break;
            case 'pp':
                _message[1] = 'permintaan pakan';
                _title = 'Permintaan Pakan';
                _dataKirim = { 'no_pp': [], 'ket': '' };
                for (var i in _data) {
                    _dataKirim['no_pp'].push(_data[i]['no_lpb']);
                }
                _url = 'permintaan_pakan_v2/permintaan_pakan/reject_pp_kadiv';
                break;
            default:
        }
        if (_fitur == 'plotting_pelaksana') {
            bootbox.dialog({
                title: _title,
                message: _message
            });
        } else {
            bootbox.confirm({
                title: _title,
                message: _message.join(' '),
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
                        var _keterangan = $('#keterangan_reject').val();
                        if (_keterangan.length < 10) {
                            bootbox.alert('Keterangan minimal 10 huruf');
                            return false;
                        }

                        if (_fitur == 'pp') {
                            _dataKirim['ket'] = _keterangan;
                        } else if (_fitur == 'plotting_do_pakan') {
                            _dataKirim['keterangan'] = _keterangan;
                        } else {
                            _dataKirim['keterangan_reject'] = _keterangan;
                        }
                        $.ajax({
                            type: 'post',
                            beforeSend: function() {
                                //Approval._prosesServer = 1;

                            },
                            dataType: 'json',
                            data: _dataKirim,
                            url: _url,
                            success: function(data) {
                                if (data.status) {
                                    toastr.success(data.message);
                                    var _topdiv = $(elm).closest('.detailkandang');
                                    var _checked = _topdiv.find('table>tbody :checked');
                                    _checked.remove();
                                    if (callback !== undefined) {
                                        callback(elm);
                                    }
                                } else {
                                    toastr.error(data.message);
                                }
                            },
                        }).done(function() {
                            //Approval._prosesServer = 0;
                        });
                    }
                }
            });
        }

    },

    multipleApprove: function(_listApproval, elm) {
        var _message = 'Apakah Anda yakin akan melanjutkan proses approval dengan rincian fitur berikut ?';
        var _table = ['<table class="table table-bordered">', '</thead><tr><th>Fitur</th><th>Keterangan</th></tr></thead></tbody>'],
            _convertFitur = {
                'plotting_pelaksana': 'PLOTTING PELAKSANA',
                'plotting_do_pakan': 'PLOTTING DO PAKAN',
                'ppsk': 'PERMINTAAN GLANGSING',
                'pp': 'PERMINTAAN PAKAN',
                'pengajuan_harga': 'PENGAJUAN HARGA GL'
            },
            _urlApprove = {
                'plotting_pelaksana': { 'url': 'kandang/plotting_pelaksana/ack' },
                'plotting_do_pakan': { 'url': 'permintaan_pakan_v2/pembelian_pakan/approvereject' },
                'ppsk': { 'url': 'report/kontrol_stok_glangsing/updatePpsk' },
                'pp': { 'url': 'permintaan_pakan_v2/permintaan_pakan/approve_pp_budidaya' },
                'pengajuan_harga': { 'url': 'sales_order/pengajuan_harga/approval' }
            };
        var _ket = '',
            _queejob = {},
            _data, tmp = [],
            _dataKirim = {};
        for (var i in _listApproval) {
            _data = _listApproval[i];
            _queejob[i] = [];
            tmp = [];
            switch (i) {
                case 'plotting_pelaksana':
                    _dataKirim = { 'data': _data };
                    _ket = 'Flok ';
                    for (var j in _data) {
                        tmp.push(_data[j]['flok']);
                    }
                    _ket += tmp.join(', ');
                    break;
                case 'plotting_do_pakan':
                    _ket = '<div>Tgl. Kirim</div>';
                    _dataKirim = { 'farmkirim': _data }
                    for (var j in _data) {
                        tmp.push('<div> - ' + Config._tanggalLocal(_data[j]['tgl_kirim'], '-', ' ') + '</div>');
                    }
                    _ket += tmp.join(', ');
                    break;
                case 'ppsk':
                    _dataKirim = { 'data': [], 'nextStatus': 'A' };
                    _ket = '<div>Tgl. Kebutuhan</div>';
                    for (var j in _data) {
                        _dataKirim['data'].push({ 'no_ppsk': _data[j]['no_ppsk'] });
                        tmp.push('<div> - ' + Config._tanggalLocal(_data[j]['tgl_kebutuhan'], '-', ' ') + '</div>');
                    }
                    _ket += tmp.join('');
                    break;
                case 'pp':
                    _ket = '<div>Tgl. Kirim</div>';
                    _dataKirim = { 'no_pp': [] };

                    for (var j in _data) {
                        _dataKirim['no_pp'].push(_data[j]['no_lpb']);
                        tmp.push('<div> - ' + Config._tanggalLocal(_data[j]['tgl_kirim'], '-', ' ') + '</div>');
                    }
                    _ket += tmp.join('');
                    break;
                case 'pengajuan_harga':
                    _dataKirim = { 'data': _data, 'nextStatus': 'A' };
                    break;
            }
            _queejob[i].push({
                url: _urlApprove[i]['url'],
                dataKirim: _dataKirim
            });
            _table.push('<tr><td>' + _convertFitur[i] + '</td><td>' + _ket + '</td></tr>');
        }
        _table.push('</tbody>');
        _table.push('</table>');
        _message += ' <br />' + _table.join('');

        bootbox.confirm({
            title: 'Lebih dari satu fitur akan diapprove',
            message: _message,
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
                    var _showBootboxWaiting = 0;
                    for (var _fitur in _queejob) {
                        for (var _item in _queejob[_fitur]) {
                            $.ajax({
                                type: 'post',
                                beforeSend: function() {
                                    if (!_showBootboxWaiting) {
                                        bootbox.alert('Mohon tunggu .....');
                                        _showBootboxWaiting = 1;
                                    }

                                },
                                async: false,
                                dataType: 'json',
                                data: _queejob[_fitur][_item]['dataKirim'],
                                url: _queejob[_fitur][_item]['url'],
                                success: function(data) {
                                    if (data.status) {
                                        toastr.success(data.message);
                                        var _topdiv = $(elm).closest('.detailkandang');
                                        var _checked = _topdiv.find('table>tbody :checked[data-fitur=' + _fitur + ']');
                                        _checked.remove();
                                        if (_showBootboxWaiting) {
                                            bootbox.hideAll();
                                        }
                                    } else {
                                        toastr.error(data.message);
                                    }
                                },
                            }).done(function() {

                            });
                        }

                    }

                }
            }
        });
    },

    approveFitur: function(_listApproval, elm) {
        var _fitur = '',
            _message, _data, _url, _dataKirim, _title;
        for (var i in _listApproval) {
            _fitur = i;
            _data = _listApproval[i];
        }
        switch (_fitur) {
            case 'plotting_pelaksana':
                _message = this.buildMessagePlottingPelaksana(_data);
                _title = 'Plotting Pelaksana';
                _dataKirim = { 'data': _data };
                _url = 'kandang/plotting_pelaksana/ack';
                break;
            case 'plotting_do_pakan':
                _message = this.buildMessagePlottingDOPakan(_data);
                _title = 'Plotting DO Pakan';
                _dataKirim = { 'farmkirim': _data };
                _url = 'permintaan_pakan_v2/pembelian_pakan/approvereject';
                break;
            case 'ppsk':
                _message = this.buildMessagePpsk(_data);
                _title = 'Permintaan Glangsing Bekas Pakai';
                _dataKirim = { 'data': [], 'nextStatus': 'A' };
                for (var i in _data) {
                    _dataKirim['data'].push({ 'no_ppsk': _data[i]['no_ppsk'] });
                }
                _url = 'report/kontrol_stok_glangsing/updatePpsk';
                break;
            case 'pengajuan_harga':
                _message = this.buildMessagePengajuanHarga(_data);
                _title = 'Pengajuan Harga Glangsing';
                _dataKirim = { 'data': _data, 'nextStatus': 'A' };
                _url = 'sales_order/pengajuan_harga/approval';
                break;
            case 'pp':
                _message = this.buildMessagePP(_data);
                _title = 'Permintaan Pakan';
                _dataKirim = { 'no_pp': [] };
                for (var i in _data) {
                    _dataKirim['no_pp'].push(_data[i]['no_lpb']);
                }
                _url = 'permintaan_pakan_v2/permintaan_pakan/approve_pp_budidaya';
                break;
            default:
        }
        bootbox.confirm({
            title: _title,
            message: _message,
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
                    $.ajax({
                        type: 'post',
                        beforeSend: function() {
                            //Approval._prosesServer = 1;

                        },
                        dataType: 'json',
                        data: _dataKirim,
                        url: _url,
                        success: function(data) {
                            if (data.status) {
                                toastr.success(data.message);
                                var _topdiv = $(elm).closest('.detailkandang');
                                var _checked = _topdiv.find('table>tbody :checked');
                                _checked.remove();
                            } else {
                                toastr.error(data.message);
                            }
                        },
                    }).done(function() {
                        //Approval._prosesServer = 0;
                    });
                }
            }
        });
    },

    buildMessagePlottingPelaksana: function(_data) {
        var _flok = [],
            _message = 'Apakah Anda yakin akan melanjutkan proses approval plotting pelaksana ';
        for (var i in _data) {
            _flok.push(_data[i]['flok']);
        }
        _message += ' <strong> flok ' + _flok.join(' dan ') + ' ? </strong>';
        return _message;
    },

    buildMessagePP: function(_data) {
        var _pp = [],
            _message = 'Apakah Anda yakin akan melanjutkan proses approval permintaan pakan dengan rincian berikut ?';
        for (var i in _data) {
            _pp.push('<div> - ' + _data[i]['no_lpb'] + ' untuk kandang ' + _data[i]['kandang'] + '</div>');
        }
        _message += ' <br />' + _pp.join('');
        return _message;
    },

    buildMessagePlottingDOPakan: function(_data) {
        var _do_pakan = [],
            _message = 'Apakah Anda yakin akan melanjutkan proses approval plotting DO Pakan dengan tanggal kirim berikut ?';
        for (var i in _data) {
            _do_pakan.push('<div> - ' + Config._tanggalLocal(_data[i]['tgl_kirim'], '-', ' ') + '</div>');
        }
        _message += ' <br />' + _do_pakan.join('');
        return _message;
    },

    buildMessagePengajuanHarga: function(_data) {
        var _message = 'Apakah Anda yakin akan melanjutkan proses approval pengajuan harga glangsing ?';
        return _message;
    },

    buildMessagePpsk: function(_data) {
        var _ppsk = ['<table class="table table-bordered custom_table">', '</thead><tr><th>Kategori</th><th>Tgl Kebutuhan</th><th>Jml Sak</th></tr></thead></tbody>'],
            _message = 'Apakah Anda yakin akan melanjutkan proses approval permintaan glangsing bekas pakai dengan rincian berikut ?';
        for (var i in _data) {
            _ppsk.push('<tr><td>' + _data[i]['kategori'] + '</td><td>' + Config._tanggalLocal(_data[i]['tgl_kebutuhan'], '-', ' ') + '</td><td>' + _data[i]['jml_sak'] + '</td></tr>');
        }
        _ppsk.push('</tbody>');
        _ppsk.push('</table>');
        _message += ' <br />' + _ppsk.join('');
        return _message;
    },
}
$(function() {
    $('#divTab ul>li:first>a').click();
})