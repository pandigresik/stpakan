var BAPD = {
    kandang: {},
    keterangan: '',
    timer: false,
    list_bapd: function(elm, target) {
        var _f = $(elm).closest('form');
        var _tindaklanjut = $(':checkbox[name=tindaklanjut]');
        var _error = 0;
        var _data = {};
        var _access = '';
        if (_tindaklanjut.length) {
            _access = _tindaklanjut.is(':checked') ? 1 : 0;
        }
        _data['tindak_lanjut'] = _access;

        if (!_access) {
            $('select').each(function() {
                console.log($(this).val());
                if (!empty($(this).val())) {
                    _data[$(this).attr('name')] = $(this).val();
                }
            });
        }
        /* lakukan pencarian */
        if (!_error) {
            var _url = 'penerimaan_docin/berita_acara/list_bapd';
            $.get(_url, _data, function(data) {
                $(target).html(data);
            }, 'html');
        }
    },
    form_bapd: function(elm) {
        var _p = $(elm).closest('.panel-body');
        var _url = 'penerimaan_docin/berita_acara/form_bapd';
        $.get(_url, {}, function(data) {
            _p.html(data);
        }, 'html');
    },
    ubahbapddoc: function(elm) {
        var _p = $(elm).closest('.panel-body');
        var _noreg = $(elm).data('noreg');
        var _url = 'penerimaan_docin/berita_acara/ubahbapdoc';
        $.get(_url, { noreg: _noreg }, function(data) {
            _p.html(data);
        }, 'html');
    },

    ubahformbap: function(elm) {
        var _t = $(elm).closest('.panel-body');
        var _f = $(elm).closest('form');
        var _kandang = _f.find('select[name=kode_kandang]>option:selected');
        var _docindb = _kandang.data('tgldocin');
        var _minDate = new Date(_docindb);
        _minDate.setDate(_minDate.getDate() - 1);
        _minDate.setHours(0, 0, 1);
        var _maxDate = new Date(_docindb);
        _maxDate.setHours(23, 59, 59);
        var _aksi = $(elm).text();
        var _pdiv;
        if (_aksi == 'Ubah') {
            $(elm).text('Batal');
            /*  $(elm).replaceWith(
                '<a href="#penerimaan_docin/berita_acara/index">Batal</a>'
              )
              */
            _pdiv = $(elm).closest('div');
            _pdiv.find('.tmbdraft').removeClass('hide');
            _pdiv.find('.tmbdrilis').data('revisi', 0);
            _f.find('select[name=kode_hatchery]').prop('disabled', false);
            var _bapdoc = _t.find('table[data-table=bapdoc]');
            var _bapdocbox = _t.find('table[data-table=bapdocbox]');
            _t.find('span.lanjut').replaceWith('<span onclick="BAPD.show_performancedocin(this)" class="btn btn-default">Lanjut</span>');
            _bapdoc.closest('.panel').remove();
            var _tmpval, _add, _sub, _tombol, _plushidden, _no = 1;
            var _jmlsj = _bapdocbox.find('tbody tr').length;
            _bapdocbox.find('tbody tr').each(function() {
                $(this).find('td').each(function() {
                    if ($(this).hasClass('sj')) {
                        _tmpval = $(this).text();
                        $(this).html('<input class="form-control" type="text" name="suratjalan" onchange="BAPD.ceksuratjalan(this)" value="' + _tmpval + '" />');
                    }
                    if ($(this).hasClass('tgl_terima')) {
                        _tmpval = $(this).text();
                        var _newelm = $('<div class="input-group date"><input type="text" class="form-control" name="tgl_terima" readonly=""><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div>');
                        $(this).html(_newelm);

                        _newelm.find('input').datetimepicker({
                            timeFormat: 'HH:mm',
                            dateFormat: 'dd M yy',
                            minDate: _minDate,
                            maxDate: _maxDate,
                        }).val(_tmpval);
                    }
                    if ($(this).hasClass('jmlbox')) {
                        _plushidden = 'style="display: none;"';
                        if (_jmlsj == _no) {
                            _plushidden = '';
                        }
                        _add = '&nbsp;<i class="glyphicon glyphicon-plus-sign" ' + _plushidden + ' onclick="BAPD.plusRowSJ(this)" ></i>';
                        _sub = '&nbsp;<i class="glyphicon glyphicon-minus-sign"  onclick="BAPD.minusRowSJ(this)"></i>';
                        _tombol = '<span class="tombol">' + _add + _sub + '</span>';
                        $(this).find('span.jmlbox').addClass('link_span').click(function() {
                            BAPD.show_suratjalan($(this));
                        });
                        $(this).append(_tombol);
                    }
                });
                _no++;
            });
        } else {
            $('#main_content').load('penerimaan_docin/berita_acara/index');
        }

    },

    data_kandang: function(elm) {
        var _f = $(elm).closest('form');
        var _tgldocin = $(elm).find('option:selected').data('tgldocin');
        var _n = _f.next('.div_bapsuratjalan');
        var _noreg = $(elm).val();
        _f.find('input[name=no_reg]').val(_noreg);
        _f.find('input[name=tgl_doc_in]').val(Config._tanggalLocal(_tgldocin, '-', ' '));
        _n.html('');
    },
    list_suratjalan: function(elm) {
        var _f = $(elm).closest('form');
        var _n = _f.next('.div_bapsuratjalan');
        var _url = 'penerimaan_docin/berita_acara/list_suratjalan';
        var _docin = _f.find('input[name=tgl_doc_in]').val();
        var _docindb = Config._convertTgl(Config._tanggalDb(_docin, ' ', '-'));
        var _minDate = new Date(_docindb);
        _minDate.setDate(_minDate.getDate() - 1);
        _minDate.setHours(0, 0, 1);
        var _maxDate = new Date(_docindb);
        _maxDate.setHours(23, 59, 59);
        var _error = 0;
        if (!_error) {
            if (empty($(elm).val())) {
                _n.html('');
            } else {
                $.get(_url, {}, function(data) {
                    _n.html(data).find('div.date>input').datetimepicker({
                        timeFormat: 'HH:mm',
                        dateFormat: 'dd M yy',
                        minDate: _minDate,
                        maxDate: _maxDate,
                        //      timeOnlyShowDate: true
                    });
                }, 'html');
            }
        }
    },

    show_suratjalan: function(elm) {
        var _tr = $(elm).closest('tr');
        var _sj = $.trim(_tr.find('td.sj>input').val());
        var _tglterima = $.trim(_tr.find('td.tgl_terima input').val());
        var _action = $(elm).data('action') || 'update';
        var _error = 0;
        var _sudahRilis = $('span.tmbrilis').length ? 0 : 1;
        if (!$(elm).hasClass('link_span')) {
            return null;
        }
        if (empty(_sj)) {
            _error++;
            toastr.error('Surat jalan harus diisi', 'error');
        }
        if (empty(_tglterima)) {
            _error++;
            toastr.error('Tanggal penerimaan harus diisi', 'error');
        }
        /* tampilkan popup */
        if (!_error) {
            var _listsj = $(elm).data('listsj');
            var _sjstr = [];
            var _jmlbox = 0;
            if (empty(_listsj)) {
                _listsj = [{ kodebox: '', jml: 0 }];
            }

            var _jmlsj = _listsj.length;
            var _no = 1;
            var _add, _sub;

            var _tombol = '',
                _plushidden = '';
            for (var i in _listsj) {
                _plushidden = 'style="display: none;"';
                if (_jmlsj == _no) {
                    _plushidden = '';
                }

                if (!_sudahRilis) {
                    _add = '&nbsp;<i class="glyphicon glyphicon-plus-sign" ' + _plushidden + ' onclick="BAPD.plusRowBox(this)" ></i>';
                    _sub = '&nbsp;<i class="glyphicon glyphicon-minus-sign"  onclick="BAPD.minusRowBox(this)"></i>';
                    _tombol = _add + _sub;
                }


                _sjstr.push('<tr><td>' + _no + '</td><td><input name="kodebox" onchange="BAPD.cekkodebox(this)" type="text" value="' + _listsj[i]['kodebox'] + '" /></td><td class="jumlah"><input name="jml" onchange="BAPD.sumBox(this)" class="number col-md-3" type="text" value="' + _listsj[i]['jml'] + '" />' + _tombol + '</td></tr>');
                _jmlbox += parse_number(_listsj[i]['jml'], '.', ',');
                _no++;
            }

            var bootbox_content = {
                input_str: [
                    '<div class="row">',
                    '<div class="col-md-2">No. SJ</div>',
                    '<div class="col-md-3">' + _sj + '</div>',
                    '</div>',
                    '<table class="table table-striped" data-nomersj="' + _sj + '">',
                    '<thead>',
                    '<tr>',
                    '<th>No</th>',
                    '<th>Kode Box</th>',
                    '<th>Jumlah</th>',
                    '</tr>',
                    '</thead>',
                    '<tbody>',
                    _sjstr.join(''),
                    '</tbody>',
                    '<tfoot>',
                    '<tr>',
                    '<td colspan="2">Total Box</td>',
                    '<td class="jumlah"><input type="text" class="col-md-3 number no_border" value="' + _jmlbox + '" /></td>',
                    '</tr>',
                    '</tfoot>',
                    '</table>'
                ],
                content: function() {
                    var _obj = $('<div/>').html(this.input_str.join(''));
                    _obj.find('input.number').priceFormat({
                        prefix: '',
                        centsLimit: 0,
                        thousandsSeparator: '.'
                    });
                    return _obj;
                }
            };
            var _options = {
                title: 'Entri Kode Box',
                message: bootbox_content.content(),
                buttons: {
                    set: {
                        label: 'Selesai',
                        className: '',
                        callback: function(e) {
                            var _error = 0;
                            var _table = $(e.target).closest('.modal-content').find('.modal-body table');
                            /* pastikan sudah diisi semua input kodebox dan jumlahnya */
                            var _nilai;
                            _table.find('input').each(function() {
                                _nilai = $.trim($(this).val());
                                $(this).removeClass('input_error');
                                if (empty(_nilai) || _nilai == 0) {
                                    $(this).addClass('input_error');
                                    _error++;
                                }
                            });
                            if (!_error) {
                                if (_action != 'readonly') {
                                    BAPD.simpanKodeBox(_table, elm);
                                }
                            } else {
                                toastr.error('Data harus diisi semua', 'error');
                                return false;
                            }

                        }
                    }
                }
            };
            bootbox.dialog(_options);
        }

    },
    show_kodebox: function(elm) {
        var _noreg = $(elm).data('noreg');
        var _kodekandang = _noreg.substr(-2, 2);
        var _tr = $(elm).closest('tr');
        var _hatchery = _tr.prev().find('td:eq(2)').text();
        var _listsj = [],
            _jmlbox = 0;
        $(elm).closest('div').prev().find('table>tbody tr').each(function() {
            _listsj.push('<tr>');
            _listsj.push('<td>' + _hatchery + '</td>');
            _listsj.push('<td>' + $(this).find('td.sj').text() + '</td>');
            var _datasj = $(this).find('td.jmlbox span').data('listsj');
            for (var i in _datasj) {
                _jmlbox += parse_number(_datasj[i]['jml'], '.', ',');
                if (i > 0) {
                    _listsj.push('<tr>');
                    _listsj.push('<td></td><td></td>');
                }
                _listsj.push('<td>' + _datasj[i]['kodebox'] + '</td>');
                _listsj.push('<td>' + _datasj[i]['jml'] + '</td>');
                if (i > 0) {
                    _listsj.push('</tr>');
                }
            }
            _listsj.push('</tr>');
        });
        var bootbox_content = {
            input_str: [
                '<table class="table table-striped">',
                '<thead>',
                '<tr>',
                '<th>Hatchery</th>',
                '<th>No. SJ</th>',
                '<th>Kode Box</th>',
                '<th>Jumlah</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                _listsj.join(''),
                '</tbody>',
                '<tfoot>',
                '<tr>',
                '<td></td>',
                '<td>Total Box</td>',
                '<td></td>',
                '<td class="jumlah">' + _jmlbox + '</td>',
                '</tr>',
                '</tfoot>',
                '</table>'
            ],
            content: function() {
                var _obj = $('<div/>').html(this.input_str.join(''));
                return _obj;
            }
        };
        var _options = {
            title: 'Detail Kode Box - Kandang ' + _kodekandang,
            message: bootbox_content.content(),
            buttons: {
                set: {
                    label: 'Selesai',
                    className: '',
                    callback: function(e) {

                    }
                }
            }
        };
        bootbox.dialog(_options);
    },

    plusRowBox: function(elm) {
        var _tr = $(elm).closest('tr');
        var _tr_c = _tr.clone();
        var _tbody = _tr.closest('tbody');
        $(elm).hide();
        _tbody.append(_tr_c);
        _tr_c.find('.glyphicon-minus-sign').show();
        _tr_c.find('input').val('').removeClass('input_error');
        _tr_c.find('input.number').priceFormat({
            prefix: '',
            centsLimit: 0,
            thousandsSeparator: '.'
        }).val(0);
        /* update kembali nomer urut baris */
        var _no = 1;
        _tbody.children().each(function() {
            $(this).find('td:first').text(_no);
            _no++;
        });
    },
    minusRowBox: function(elm) {
        var _tr = $(elm).closest('tr');
        var _tbody = _tr.closest('tbody');
        var _anak = _tbody.children();
        if (_anak.length > 1) {
            _tr.remove();
            _tbody.find('tr:last>td:last .glyphicon-plus-sign').show();
            this.sumBox(_tbody.find('tr:last>td:last>input'));
            /* update kembali nomer urut baris */
            var _no = 1;
            _tbody.children().each(function() {
                $(this).find('td:first').text(_no);
                _no++;
            });
        } else {
            toastr.error('Baris berjumlah 1, tidak bisa dihapus', 'error');
        }
    },
    sumBox: function(elm) {
        var _tbody = $(elm).closest('tbody');
        var _jml = 0;
        _tbody.find('td.jumlah>input').each(function() {
            _jml += parse_number($(this).val(), '.', ',');
        });
        _tbody.next('tfoot').find('td.jumlah>input').val(_jml);
    },
    simpanKodeBox: function(data, target) {
        var _listkodebox = [];
        var _tmp, _tr, _jml = 0;
        data.find('tbody tr').each(function() {
            _tmp = {};
            _tr = $(this);
            _tr.find('input').each(function() {
                _tmp[$(this).attr('name')] = $(this).val();
                if ($(this).attr('name') == 'jml') {
                    _jml += parse_number($(this).val(), '.', ',');
                }
            });
            _listkodebox.push(_tmp);
        });
        /* rubah text dan tambahkan plus atau minus */
        var _add = '&nbsp;<i class="glyphicon glyphicon-plus-sign" onclick="BAPD.plusRowSJ(this)" ></i>';
        var _sub = '&nbsp;<i class="glyphicon glyphicon-minus-sign"  onclick="BAPD.minusRowSJ(this)"></i>';
        var _tombol = '<span class="tombol">' + _add + _sub + '</span>';
        var _td = $(target).closest('td');
        $(target).data('listsj', _listkodebox).text(_jml);
        if (!_td.find('span.tombol').length) {
            _td.append(_tombol);
        }
    },

    plusRowSJ: function(elm) {
        var _tr = $(elm).closest('tr');
        var _tr_c = _tr.clone();
        var _tbody = _tr.closest('tbody');
        $(elm).hide();
        _tbody.append(_tr_c);
        _tr_c.find('input').val('');
        var link_span = _tr_c.find('td:last>span.link_span');
        link_span.text('Kode Box').data('listsj', []);

        var _tglTmp = _tr.find('div.date>input').datetimepicker();
        var _tglterima = _tr_c.find('div.date>input');
        _tglterima.removeAttr('id').removeClass('hasDatepicker').datetimepicker({
            timeFormat: 'HH:mm',
            dateFormat: 'dd M yy',
            minDate: _tglTmp.datetimepicker('option', 'minDate'),
            maxDate: _tglTmp.datetimepicker('option', 'maxDate'),
        });

    },
    minusRowSJ: function(elm) {
        var _tr = $(elm).closest('tr');
        var _tbody = _tr.closest('tbody');
        var _anak = _tbody.children();
        if (_anak.length > 1) {
            _tr.remove();
            _tbody.find('tr:last>td:last .glyphicon-plus-sign').show();
        } else {
            toastr.error('Baris berjumlah 1, tidak bisa dihapus', 'error');
        }
    },
    /* pastikan surat jalan yang diinput gak boleh kembar */
    ceksuratjalan: function(elm) {
        var _t = $(elm).closest('tbody');
        var _kembar = {},
            _tmp;
        _t.find('input[name=suratjalan]').each(function() {
            _tmp = $.trim($(this).val());
            if (!empty(_tmp)) {
                if (_kembar[_tmp] == undefined) {
                    _kembar[_tmp] = [];
                } else {
                    toastr.warning('Surat jalan ' + $(elm).val() + ' sudah ada ', 'warning');
                    $(elm).val('');
                }
            }
        });
    },

    cekkodebox: function(elm) {
        var _t = $(elm).closest('tbody');
        var _kembar = {},
            _tmp;
        _t.find('input[name=kodebox]').each(function() {
            _tmp = $.trim($(this).val());
            if (!empty(_tmp)) {
                if (_kembar[_tmp] == undefined) {
                    _kembar[_tmp] = [];
                } else {
                    toastr.warning('Kode box ' + $(elm).val() + ' sudah ada ', 'warning');
                    $(elm).val('');
                }
            }
        });
    },
    show_performancedocin: function(elm) {
        var _url = 'penerimaan_docin/berita_acara/performancedocin';
        var _r = $(elm).closest('div').prev().find('table tbody tr');
        var _jmlbox = 0,
            _error = 0,
            _tmpbox;
        var _p = $(elm).closest('.panel');
        _r.each(function() {
            _tmpbox = isNaN($(this).find('span.jmlbox').text()) ? 0 : $(this).find('span.jmlbox').text();
            _jmlbox += parse_number(_tmpbox, '.', ',');
            if (!_tmpbox) {
                _error++;
                toastr.warning('Ada pengisian kodebox yang belum lengkap', 'warning');
            }
        });
        if (!_error) {
            /* remove button glyphicon */
            _r.each(function() {
                $(this).find('span.jmlbox').data('action', 'readonly');
                $(this).find('.glyphicon-plus-sign,.glyphicon-minus-sign').remove();
                $(this).find('.date input').datetimepicker('disable');
                $(this).find('input[name=suratjalan]').attr('readonly', 1);
            });
            $(elm).addClass('disabled');
            if (!_p.next('.panel').length) {
                $.get(_url, { jmlbox: _jmlbox }, function(data) {
                    _p.closest('.div_bapsuratjalan').append(data).find('input.number:not([name=jml_afkir])').priceFormat({
                        prefix: '',
                        centsLimit: 2,
                        centsSeparator: ',',
                        thousandsSeparator: '.'
                    });
                    _p.closest('.div_bapsuratjalan').find('input.number[name=jml_afkir]').priceFormat({
                        prefix: '',
                        centsLimit: 0,
                        centsSeparator: ',',
                        thousandsSeparator: '.'
                    });
                }, 'html');
            }
        }
    },

    ackapprove: function(elm) {
        var _checked = $('#list_bapdocin :checked');
        this.keterangan = '';
        if (!_checked.length) {
            toastr.error('Mohon memilih kandang terlebih dahulu', 'Notifikasi');
            return;
        }
        var _ini = this;
        var _noreg = [],
            _kode_kandang = [];
        _checked.each(function() {
            _noreg.push($(this).val());
            _kode_kandang.push('Kandang ' + $(this).val().substr(-2));
        })
        bootbox.confirm({
            title: 'Konfirmasi Tindak Lanjut',
            message: '<div class="text-center">Apakah Anda yakin melanjutkan proses approve BAPD kandang dibawah ini  ? <div> ' + _kode_kandang.join('</div><div>') + '</div></div>',
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
                    _ini.fingerprint('', 'KAFARM');
                }
            }
        });
    },
    updateriwayat: function(elm, noreg) {
        var _tr = $(elm).closest('tr');
        var _url = 'penerimaan_docin/berita_acara/riwayat';
        $.get(_url, { noreg: noreg }, function(data) {
            _tr.next('.detailbapdoc').find('div.div_riwayat[data-noreg="' + noreg + '"]').replaceWith(data);
            console.log(_tr.find('div.div_riwayat[data-noreg="' + noreg + '"]'));
        }, 'html');

    },
    reject: function(elm) {
        var _checked = $('#list_bapdocin :checked');
        this.keterangan = '';
        if (!_checked.length) {
            toastr.error('Mohon memilih kandang terlebih dahulu', 'Notifikasi');
            return;
        }
        var _content = ['<div class="dialog_reject">',
            '<div class="col-md-12">Mohon entri alasan reject (min. 10 karakter dan max. 100 karakter)</div>',
            '<div class="col-md-12">',
            '<textarea name="keterangan_reject" class="col-md-10" maxlength=100 onkeyup="BAPD.aktifkanBtn(this)"></textarea>',
            '</div>',
            '<div class="col-md-12 new-line">',
            '<div class="col-md-2">',
            '<div name="simpanRejectBtn" class="btn btn-default disabled" onclick="BAPD.simpanKeteranganReject(this)">Simpan</div>',
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
        if ($.trim(ini.val()).length >= 10) {
            _p.find('div[name=simpanRejectBtn]').removeClass('disabled');
        } else {
            _p.find('div[name=simpanRejectBtn]').addClass('disabled');
        }
    },

    checkketerangan: function(elm) {
        var _ini = $(elm);
        var _rj = _ini.closest('td').find('.reject');
        if (_ini.val().length >= 10) {
            if (_rj.hasClass('disabled')) {
                _rj.removeClass('disabled');
            }
        } else {
            _rj.addClass('disabled');
        }
    },
    /* tampilkan detail bapddoc*/
    show_detailbapddoc: function(elm) {
        var _tr = $(elm).closest('tr');
        var _tr_detail = _tr.next('.detailbapdoc');
        var _td_detail = _tr_detail.find('td.tddetail');
        var _noreg = $(elm).text();
        _tr.find('div.div_tindaklanjut').removeClass('hide');
        if (empty(_td_detail.html())) {
            /* ambil dari server */
            var _url = 'penerimaan_docin/berita_acara/detailbapddoc';
            $.get(_url, { noreg: _noreg }, function(data) {
                _td_detail.html(data);
            }, 'html');
        }
        if (_tr_detail.is(':hidden')) {
            _tr_detail.show();
        } else {
            _tr_detail.hide();
        }
    },

    show_detailsj: function(elm) {
        var _tr = $(elm).closest('tr');
        var _tr_detail = _tr.next('.detailbapdoc');
        var _td_detail = _tr_detail.find('td.tddetail');
        var _noreg = $(elm).data('no_reg');
        if (empty(_td_detail.html())) {
            /* ambil dari server */
            var _url = 'penerimaan_docin/berita_acara/detailsj';
            $.get(_url, { noreg: _noreg }, function(data) {
                _td_detail.html(data);
            }, 'html');
        }
        if (_tr_detail.is(':hidden')) {
            _tr_detail.show();
        } else {
            _tr_detail.hide();
        }
    },

    cetakbapddoc: function(elm) {
        var noreg = $(elm).data('noreg');
        var hatchery = $(elm).closest('tr').prev().find('td:eq(2)').text();
        var url = 'penerimaan_docin/berita_acara/cetakbapd?noreg=' + noreg + '&hatchery=' + hatchery;
        var w = screen.width * .9,
            h = 500;
        var left = (screen.width / 2) - (w / 2);
        var top = (screen.height / 2) - (h / 2);
        window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top=" + top + ", left=" + left + ", width=" + w + ", height=" + h);
    },

    updateStokAwal: function(elm) {
        var _td = $(elm).closest('td');
        var _tr = _td.closest('tr');
        var _stokawal = _tr.find('td.stokawal');
        var _jmlekor = parse_number(_tr.find('td.jmlekor').text(), '.', ',');
        var _tmp = parse_number(_stokawal.text(), '.', ',');
        var _afkir = parse_number($(elm).val(), '.', ',');
        var _nstok = _jmlekor - _afkir;
        _stokawal.text(number_format(_nstok, 0, ',', '.'));
    },

    filter_content: function(elm) {
        var _table = $(elm).closest('table');
        var _tbody = _table.find('tbody');
        var _content = $(elm).val();
        var _target = $(elm).data('target');

        _tbody.find('td.' + _target + ':contains(' + _content.toUpperCase() + ')').parent().show();
        _tbody.find('td.' + _target + ':not(:contains(' + _content.toUpperCase() + '))').parent().hide();
        _tbody.find('td.' + _target + ':not(:contains(' + _content.toUpperCase() + '))').parent().next().hide();
    },

    entryPerformance: function(elm) {
        var _status = $(elm).data('status');
        var _tr = $(elm);
        var _elmEntry = ['jml_afkir', 'bb_rata2', 'uniformity', 'status'];
        var _nilai = '',
            _td, _elmInput;
        if (_tr.find('input').length) {
            return;
        }
        for (var i in _elmEntry) {
            _td = _tr.find('td.' + _elmEntry[i]);
            _nilai = _td.text();
            _elmInput = '<input data-entrylama="' + _nilai + '" name="' + _elmEntry[i] + '" type="text" size=3 value="' + _nilai + '" />';
            if (_elmEntry[i] == 'status') {
                _nilai = '';
                _elmInput = '<input placeholder="Scan Kandang" name="' + _elmEntry[i] + '" type="text" size=4 value="' + _nilai + '" onchange="BAPD.scanRFID(this)" />';
            }
            _td.html(_elmInput);
        }
        _tr.find('input[name=bb_rata2]').priceFormat({
            prefix: '',
<<<<<<< HEAD
            centsLimit: 5,
=======
            centsLimit: 3,
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
            centsSeparator: ',',
            thousandsSeparator: '.'
        });
        _tr.find('input[name=uniformity]').priceFormat({
            prefix: '',
            centsLimit: 2,
            centsSeparator: ',',
            thousandsSeparator: '.'
        });
        _tr.find('input[name=jml_afkir]').priceFormat({
            prefix: '',
            centsLimit: 0,
            centsSeparator: ',',
            thousandsSeparator: '.'
        });

        _tr.find('input[name=jml_afkir]').change(function() {
            var _jmlekor = _tr.find('td.jmlbox').data('jmlterima');
            var _stokawal = _jmlekor - parse_number($(this).val(), '.', ',');
            _tr.find('td.stok_awal').html(number_format(_stokawal, 0, ',', '.'));
        });
    },
    scanRFID: function(elm) {
        var _rfID = $(elm).val();
        /** minimal panjang rfid adalah 10 digit */
        if (_rfID.length <= 9) {
            return;
        }
        /** pastikan semuanya sudah dientry */
        var _tr = $(elm).closest('tr');
        var _kode_kandang = _tr.find('td.kandang').data('kode_kandang');
        var _sama = 1;
        var _belumEntry = 0,
            _n, _m, _o, _lama;
        _tr.find('input').not($(elm)).each(function() {
            _n = $(this).val();
            _lama = $(this).data('entrylama');
            if (empty(_n)) {
                _belumEntry++;
                return false;
            } else {
                _m = parse_number(_n, '.', ',');
                _o = parse_number(_lama, '.', ',');
                if (_m != _o) {
                    _sama = 0;
                }
                if ($(this).attr('name') != 'jml_afkir') {
                    if (_m <= 0) {
                        _belumEntry++;
                        return false;
                    }
                }
            }
        });

        if (_belumEntry) {
            toastr.warning('Mohon melengkapi entri performa DOC', 'Notifikasi');
            return;
        }

        var _dataKandang = this.getKandang(_rfID);
        var _ini = this;
        $(elm).removeClass('input_error');
        $.when(_dataKandang).done(function() {
            if (!empty(_dataKandang)) {
                var no_reg = _dataKandang.no_reg;
                var flock = _dataKandang.flok_bdy;
                var kandang = _dataKandang.kode_kandang;
                if (_kode_kandang != kandang) {
                    toastr.error('RFID kandang tidak sesuai', 'Notifikasi');
                    $(elm).addClass('input_error');
                    $(elm).val('');
                    return;
                }
                /** periksa apakah entry-an sama dengan yang lama atau tidak */

                var _pesan = 'Apakah Anda yakin akan merilis BAPD <span class="link_span">Kandang ' + kandang + '</span> ? <br /> *) Data yang telah dientri tidak dapat diubah';
                if (_sama) {
                    _pesan = 'Tidak ada perubahan dari bapd sebelumnya <br />' + _pesan;
                }
                bootbox.confirm({
                    title: 'Konfirmasi Penyimpanan',
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
                            _ini.fingerprint(no_reg, 'PENGAWAS');
                        }
                    }
                });
            } else {
                $(elm).addClass('input_error');
                $(elm).val('');
            }
        });
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

    fingerprint: function(_noreg, _level) {
        var _ini = this;
        _ini.simpan_transaksi_verifikasi(function(result) {
            if (result.date_transaction) {
                var _convertLevel = { 'PENGAWAS': 'Pengawas', 'KAFARM': 'Kepala Farm' };
                var _message = "<center><b id='finger_label' style='font-size:12.5pt;text-align:center;'>Silahkan Melakukan<br>Fingerprint " + _convertLevel[_level] + " untuk verifikasi" +
                    "<center><img src='assets/images/finger.jpg' height='260px' style='filter:invert(100%);'></center>" +
                    "<center><b><p id='alert_finger' style='color:red;'></p></b></center>";
                var box = bootbox.dialog({
                    message: _message,
                    closeButton: false,
                    title: "",
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
                transaction: 'entry_bapd',
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
            var _result = {
                result: 0
            };
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
                            if (_level == 'PENGAWAS') {
                                _ini.simpanBapdSj(_noreg);
                            } else {
                                _ini.approveRejectBapd();
                            }
                        } else {
                            var _convertLevel = { 'PENGAWAS': 'Pengawas', 'KAFARM': 'Kepala Farm' };
                            var _pesanFinger = '<div class="text-center"><center><img src="assets/images/close-circle-red-512.png" height="260px" ></center><br /> Data user ' + _convertLevel[_level] + ' tidak ditemukan mohon melakukan scan fingerprint ulang.</div>';
                            bootbox.hideAll();
                            bootbox.alert(_pesanFinger, function() {
                                _ini.fingerprint(_noreg, _level);
                            });
                        }
                    } else {
                        _ini.timer = true;
                        setTimeout("BAPD.cek_verifikasi('" + date_transaction + "','" + _noreg + "','" + _level + "')", 1000);
                    }
                }
            });
        }
    },
    approveRejectBapd: function() {
        var _checked = $('#list_bapdocin :checked');
        if (!_checked.length) {
            toastr.error('Mohon memilih kandang terlebih dahulu', 'Notifikasi');
            return;
        }
        var _ini = this;
        var _noreg = [],
            _url, nextstatus;
        _checked.each(function() {
                _noreg.push($(this).val());
            })
            /* simpan ke database */
        if (empty(this.keterangan)) {
            _url = 'penerimaan_docin/berita_acara/ackapprove';
            nextstatus = 'A';
        } else {
            _url = 'penerimaan_docin/berita_acara/reject';
            nextstatus = 'RJ';
        }
        $.post(_url, { noreg: _noreg, nextstatus: nextstatus, keterangan: this.keterangan }, function(data) {
            if (data.status) {
                bootbox.alert(data.message, function() {
                    bootbox.hideAll();
                    $('span.btn_cari').click();
                });
            } else {
                bootbox.alert(data.message);
            }
        }, 'json');
    },
    simpanBapdSj: function(noreg) {
        var _tr = $('#list_bapdocin table tr[data-no_reg="' + noreg + '"]');
        var _url = 'penerimaan_docin/berita_acara/rilisbapdsj';
        var _tgldoc = _tr.data('tgldoc');
        var _jmlterima = _tr.find('td.jmlbox').data('jmlterima');
        var _data = { 'no_reg': noreg, 'tgl_doc_in': _tgldoc };
        _tr.find('input').not('input[name=status]').each(function() {
            _data[$(this).attr('name')] = parse_number($(this).val(), '.', ',');
        });
        _data['stok_awal'] = _jmlterima - _data['jml_afkir'];
        $.post(_url, { data: _data }, function(data) {
            if (data.status) {
                bootbox.alert(data.message, function() {
                    bootbox.hideAll();
                    $('span.btn_cari').click();
                });
            } else {
                bootbox.alert(data.message);
            }
        }, 'json');
    },
    simpanKeteranganReject: function(elm) {
        var ini = $(elm);
        var _p = ini.closest('.dialog_reject');
        var _ket = $.trim(_p.find('textarea[name=keterangan_reject]').val());
        this.keterangan = _ket;
        this.fingerprint('', 'KAFARM');
    }

};