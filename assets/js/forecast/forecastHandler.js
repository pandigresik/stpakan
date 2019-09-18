'use strict';
/* memerlukan file forecast/config.js */
var Forecast = {
    strain: null,
    data_farm: {},
    standart_budidaya: {},
    master_pakan: {},
    tglDocInterpilih: null,
    canCreateForecast: 0,
    lockRubahPakan: [],
    maxBuatForecast: 21,
    /* maksimal h-21 forecast sudah dibuat */
    lockEditDocIn: [],
    aktifFarm: null,
    minEditDocIn: 9,
    maxEditDocIn: 32,
    /* sesuai dengan umur panen */
    setAktifFarm: function(idFarm) {
        this.aktifFarm = idFarm;
    },
    getAktifFarm: function(idFarm) {
        return this.aktifFarm;
    },
    setLockEditDocIn: function(data) {
        this.lockEditDocIn = data;
    },
    getLockEditDocIn: function() {
        return this.lockEditDocIn;
    },
    rencanaKirimBdy: {},
    /*active berarti kandang yang open saja*/
    get_data_farm: function(idFarm, active) {
        if (Forecast.data_farm[idFarm] == undefined) {
            var _a = active == undefined ? true : active;
            /* ambil data dari database */
            var _id = idFarm;
            $.ajax({
                type: 'get',
                url: 'forecast/forecast/master_farm/' + _id + '/' + _a,
                success: function(data) {
                    Forecast.data_farm[idFarm] = data;
                },
                async: false,
                cache: true,
                dataType: 'json',
            });
        }
        return Forecast.data_farm[idFarm];
    },

    get_item_data_farm: function(item, idFarm) {
        if (idFarm == undefined) {
            idFarm = this.getAktifFarm();
        }
        var _df = this.get_data_farm(idFarm);
        return _df[item];
    },
    list_all_farm: {},
    pakan_tersimpan: {},
    grouping_standart: {},
    reset: function() {
        this.pakan_tersimpan = {};
        this.grouping_standart = {};
        this.standart_budidaya = {};
        this.master_pakan = {};
    },
    init: function(active) {
        this.strain = this.get_data_farm(active);
    },
    /* dapatkan semua farm yang dimiliki */
    get_list_farm: function(farm) {
        var result = [],
            tmp;
        if (farm == undefined) farm = 'all';
        if (empty(Forecast.list_all_farm)) {
            $.ajax({
                type: 'get',
                url: 'api/api/farm',
                data: {},
                dataType: 'json',
                async: false,
                cache: true,
            }).done(function(data) {
                if (data.status) {
                    Forecast.list_all_farm = data.content;
                }
            });
            tmp = Forecast.list_all_farm;
        } else {
            tmp = Forecast.list_all_farm;
        }
        if (farm != 'all') {
            var _t;
            for (var i in tmp) {
                _t = tmp[i];
                if (_t.kode_farm == farm) {
                    result.push(_t);
                }
            }
        } else {
            result = tmp;
        }

        return result;
    },
    get_standart_budidaya: function(idFarm, strain, tipe_kandang, tglDocIn) {
        var _error = 0;
        if (empty(strain)) {
            console.log('Kode strain harus diisi');
            _error++;
        }
        if (empty(tipe_kandang)) {
            _error++;
        }
        if (!_error) {
            if (Forecast.standart_budidaya[idFarm] == undefined) {
                Forecast.standart_budidaya[idFarm] = {};
            }
            if (Forecast.standart_budidaya[idFarm][tglDocIn] == undefined) {
                Forecast.standart_budidaya[idFarm][tglDocIn] = {};
                if (empty(Forecast.standart_budidaya[idFarm][tglDocIn][tipe_kandang])) {
                    /* ambil standart budidaya dari server */
                    $.ajax({
                        type: 'post',
                        url: 'forecast/forecast/standart_budidaya/',
                        data: { strain: strain, tipe_kandang: tipe_kandang, tglDocIn: tglDocIn },
                        success: function(data) {
                            var _error = 0;
                            var _jeniskelamin = { 'j': 'Jantan', 'b': 'Betina' };
                            for (var _stdjk in data) {
                                if (empty(data[_stdjk])) {
                                    toastr.error('Standart budidaya jenis kelamin ' + _jeniskelamin[_stdjk] + ' strain ' + strain + ' tipe kandang ' + tipe_kandang + ' dengan tanggal DOC-In ' + Config._tanggalLocal(tglDocIn, '-', ' ') + ' tidak ditemukan');
                                    _error++;
                                }
                            }
                            if (!_error) {
                                Forecast.standart_budidaya[idFarm][tglDocIn][tipe_kandang] = data;
                                /* grouping standartnya berdasarkan */
                                var _jantan = data['j'];
                                var _betina = data['b'];
                                var _group_pakan_jantan = [],
                                    _group_pakan_betina = [];
                                var _index_group = 0;
                                var _pakan_tmp = '';
                                var _group_pakan = {};

                                for (var i in _jantan) {
                                    if (_pakan_tmp != _jantan[i]['grup_barang']) {
                                        _index_group++;
                                        _group_pakan_jantan[_index_group] = {};
                                        _group_pakan_jantan[_index_group]['bentuk'] = _jantan[i]['bentuk'];
                                        _group_pakan_jantan[_index_group]['grup_barang'] = _jantan[i]['grup_barang'];
                                        _group_pakan_jantan[_index_group]['kode_barang'] = _jantan[i]['kode_barang'];
                                        _group_pakan_jantan[_index_group]['nama_barang'] = _jantan[i]['nama_barang'];
                                        _group_pakan_jantan[_index_group]['elemen'] = [];
                                        _pakan_tmp = _jantan[i]['grup_barang'];
                                        if (_group_pakan[_jantan[i]['grup_barang']] == undefined) {
                                            _group_pakan[_jantan[i]['grup_barang']] = {};
                                            _group_pakan[_jantan[i]['grup_barang']]['bentuk'] = [];
                                            _group_pakan[_jantan[i]['grup_barang']]['bentuk'].push(_jantan[i]['bentuk']);
                                        } else {
                                            if (!in_array(_jantan[i]['bentuk'], _group_pakan[_jantan[i]['grup_barang']]['bentuk'])) {
                                                _group_pakan[_jantan[i]['grup_barang']]['bentuk'].push(_jantan[i]['bentuk']);
                                            }
                                        }
                                    }

                                    _group_pakan_jantan[_index_group]['elemen'].push(_jantan[i]['umur']);
                                }
                                _pakan_tmp = '';
                                _index_group = 0;
                                for (var i in _betina) {
                                    if (_pakan_tmp != _betina[i]['grup_barang']) {
                                        _index_group++;
                                        _group_pakan_betina[_index_group] = {};
                                        _group_pakan_betina[_index_group]['bentuk'] = _betina[i]['bentuk'];
                                        _group_pakan_betina[_index_group]['grup_barang'] = _betina[i]['grup_barang'];
                                        _group_pakan_betina[_index_group]['kode_barang'] = _betina[i]['kode_barang'];
                                        _group_pakan_betina[_index_group]['nama_barang'] = _betina[i]['nama_barang'];
                                        _group_pakan_betina[_index_group]['elemen'] = [];
                                        _pakan_tmp = _betina[i]['grup_barang'];
                                        if (_group_pakan[_betina[i]['grup_barang']] == undefined) {
                                            _group_pakan[_betina[i]['grup_barang']] = {};
                                            _group_pakan[_betina[i]['grup_barang']]['bentuk'] = [];
                                            _group_pakan[_betina[i]['grup_barang']]['bentuk'].push(_betina[i]['bentuk']);
                                        } else {
                                            if (!in_array(_betina[i]['bentuk'], _group_pakan[_betina[i]['grup_barang']]['bentuk'])) {
                                                _group_pakan[_betina[i]['grup_barang']]['bentuk'].push(_betina[i]['bentuk']);
                                            }
                                        }
                                    }
                                    _group_pakan_betina[_index_group]['elemen'].push(_betina[i]['umur']);
                                }
                                /* cari master pakan untuk data dropdown pemilihan nama pakan */
                                var _arr_group_pakan = [];
                                for (var _y in _group_pakan) {
                                    for (var _z in _group_pakan[_y]['bentuk']) {
                                        _arr_group_pakan.push({ group: _y, 'bentuk': _group_pakan[_y]['bentuk'][_z] });
                                    }
                                }
                                if (Forecast.grouping_standart[idFarm] == undefined) {
                                    Forecast.grouping_standart[idFarm] = {};
                                }

                                if (Forecast.grouping_standart[idFarm][tglDocIn] == undefined) {
                                    Forecast.grouping_standart[idFarm][tglDocIn] = {};
                                }

                                if (Forecast.grouping_standart[idFarm][tglDocIn] == undefined || empty(Forecast.grouping_standart[idFarm][tglDocIn])) {
                                    Forecast.grouping_standart[idFarm][tglDocIn] = { 'j': _group_pakan_jantan, 'b': _group_pakan_betina };
                                }
                            }
                        },
                        dataType: 'json',
                        async: false
                    });
                }
            }
            return Forecast.standart_budidaya[idFarm][tglDocIn][tipe_kandang];
        }
    },

    get_standart_budidaya_bdy: function(idFarm, tglDocIn) {
        var _error = 0;
        if (empty(tglDocIn)) {
            console.log('Tanggal Doc In harus diisi');
            _error++;
        }
        if (!_error) {
            if (Forecast.standart_budidaya[idFarm] == undefined) {
                Forecast.standart_budidaya[idFarm] = {};
            }
            if (Forecast.standart_budidaya[idFarm][tglDocIn] == undefined) {
                Forecast.standart_budidaya[idFarm][tglDocIn] = {};
                if (empty(Forecast.standart_budidaya[idFarm][tglDocIn])) {
                    /* ambil standart budidaya dari server */
                    $.ajax({
                        type: 'post',
                        url: 'forecast/forecast/standart_budidaya_bdy/',
                        data: { tglDocIn: tglDocIn, kodeFarm: idFarm },
                        success: function(data) {
                            var _error = 0;
                            var _jeniskelamin = { 'j': 'Jantan' };
                            for (var _stdjk in data) {
                                if (empty(data[_stdjk])) {
                                    toastr.error('Standart budidaya jenis kelamin ' + _jeniskelamin[_stdjk] + ' dengan tanggal DOC-In ' + Config._tanggalLocal(tglDocIn, '-', ' ') + ' tidak ditemukan');
                                    _error++;
                                }
                            }
                            if (!_error) {
                                Forecast.standart_budidaya[idFarm][tglDocIn] = data;
                                /* grouping standartnya berdasarkan */
                                var _jantan = data['j'];
                                var _group_pakan_jantan = [];
                                var _index_group = 0;
                                var _pakan_tmp = '';
                                var _group_pakan = {};

                                for (var i in _jantan) {
                                    if (_pakan_tmp != _jantan[i]['kode_barang']) {
                                        _index_group++;
                                        _group_pakan_jantan[_index_group] = {};
                                        _group_pakan_jantan[_index_group]['bentuk'] = _jantan[i]['bentuk'];
                                        _group_pakan_jantan[_index_group]['kode_barang'] = _jantan[i]['kode_barang'];
                                        _group_pakan_jantan[_index_group]['nama_barang'] = _jantan[i]['nama_barang'];
                                        _group_pakan_jantan[_index_group]['elemen'] = [];
                                        _pakan_tmp = _jantan[i]['kode_barang'];
                                        if (_group_pakan[_jantan[i]['kode_barang']] == undefined) {
                                            _group_pakan[_jantan[i]['kode_barang']] = {};
                                            _group_pakan[_jantan[i]['kode_barang']]['bentuk'] = [];
                                            _group_pakan[_jantan[i]['kode_barang']]['bentuk'].push(_jantan[i]['bentuk']);
                                        } else {
                                            if (!in_array(_jantan[i]['bentuk'], _group_pakan[_jantan[i]['kode_barang']]['bentuk'])) {
                                                _group_pakan[_jantan[i]['kode_barang']]['bentuk'].push(_jantan[i]['bentuk']);
                                            }
                                        }
                                    }

                                    _group_pakan_jantan[_index_group]['elemen'].push(_jantan[i]['umur']);
                                }
                                if (Forecast.grouping_standart[idFarm] == undefined) {
                                    Forecast.grouping_standart[idFarm] = {};
                                }

                                if (Forecast.grouping_standart[idFarm][tglDocIn] == undefined) {
                                    Forecast.grouping_standart[idFarm][tglDocIn] = {};
                                }

                                if (Forecast.grouping_standart[idFarm][tglDocIn] == undefined || empty(Forecast.grouping_standart[idFarm][tglDocIn])) {
                                    Forecast.grouping_standart[idFarm][tglDocIn] = { 'j': _group_pakan_jantan };
                                }
                            }
                        },
                        dataType: 'json',
                        async: false
                    });
                }
            }
            return Forecast.standart_budidaya[idFarm][tglDocIn];
        }
    },

    /* format tanggal adalah tahun-bulan-tanggal  2015-06-15 */
    get_pakan_tersimpan: function(tglDocIn, idFarm) {

        if ((Forecast.pakan_tersimpan[tglDocIn] == undefined) || (Forecast.pakan_tersimpan[tglDocIn] == null)) {
            $.ajax({
                type: 'post',
                url: 'forecast/forecast/get_pakan_tersimpan',
                data: { tglDocIn: tglDocIn, idFarm: idFarm },
                dataType: 'json',
                async: false
            }).done(function(data) {
                if (data.status) {
                    Forecast.pakan_tersimpan[tglDocIn] = data.content;
                } else {
                    Forecast.pakan_tersimpan[tglDocIn] = null;
                }
            });
            return Forecast.pakan_tersimpan[tglDocIn];
        } else {
            return Forecast.pakan_tersimpan[tglDocIn];
        }
    },

    inline_edit: function(elm) {
        var _default = '-';
        var _val = elm.text() != '-' ? elm.text() : 0;
        var _elmPengganti = $('<input type="text" value="' + _val + '"/>');
        elm.html(_elmPengganti);
        _elmPengganti.select();
        _elmPengganti.priceFormat({
            prefix: '',
            centsLimit: 0,
            thousandsSeparator: '.'
        });
        _elmPengganti.focusout(function() {
            var _newVal = empty(_elmPengganti.val()) ? _default : _elmPengganti.val();
            elm.html(_newVal);
        });
    },
    droppable_tree: function(_elmDrop) {
        _elmDrop.droppable({
            accept: function(elm) {

                var _w = elm;
                return !$(this).find(_w).length;
            },

            drop: function(e, ui) {
                var _elm = $(this);
                var _tgl = _elm.find('label:first').text();
                var _blnElm = _elm.closest('ul');
                var _thnElm = _blnElm.parent('li').closest('ul');
                var _bln = _blnElm.closest('li').find('label.bulan').text();

                var _thn = _thnElm.prev('label').text();
                var _tglDocIn = Config._tanggalDb([_tgl, _bln, _thn].join(' '), ' ', '-');
                /* diset ketika pertama kali halaman forecast diload, berdasarkan tanggal server */
                var _tglServer = Config._tglServer;
                /* max buat forecast adalah h - 21 */
                var _maxTglDocIn = new Date(_tglDocIn);
                _maxTglDocIn.setDate(_maxTglDocIn.getDate() - Forecast.maxBuatForecast);

                if (_maxTglDocIn >= _tglServer) {
                    var _w = ui.draggable;
                    var _insertElm = '';
                    /* cek apakah memiliki child a atau tidak, jika tidak memiliki berarti berasal dari list kandang tersedia
                     * jika memiliki berarti berasal dari tree perencanaan doc in
                     */
                    if (_w.find('a').length == 0) {
                        /* element list baru untuk ditambahkan kepada list tree */
                        var _objTemp = {};
                        var _spanText = [];
                        _w.children().each(function() {
                            _objTemp[Config._indexHeader[$(this).index()]] = $(this).text();
                            _spanText.push($(this).text());
                        });
                        var _textTampil = 'Kandang ' + _objTemp['kandang'] + ' (J : ' + _objTemp['jantan'] + ', B : ' + _objTemp['betina'] + ' )';
                        var _no_reg = Forecast.get_item_data_farm('kode_farm') + '/' + Forecast.get_item_data_farm('periode_siklus') + '/' + _objTemp['kandang'];
                        _insertElm = $('<li><a href="#">' + _textTampil + '</a><span class="_status_approval label label-default">*</span><span class="hide" data-value="detail_kandang">' + _spanText.join('/') + '</span><span class="no_reg hide">' + _no_reg + '</span></li>');
                        Forecast.draggable_forecast_tree(_insertElm);
                        Forecast.add_contextmenu_kandang(_insertElm);
                        _w.remove();
                    } else {
                        _insertElm = _w;
                    }
                    /* tambahkan elm baru */
                    $(this).children('ul').append(_insertElm);
                    Forecast.periksaApproval();
                } else {
                    toastr.error('Max h-21 dari tanggal DocIn');
                }

            }
        });
        _elmDrop.find(':checkbox:first').each(function() {
            Forecast.list_kebutuhan_pakan_pertanggal($(this));
        });

    },

    draggable_tutupsiklus: function(elm) {
        $(elm).draggable({
            revert: 'invalid',
            helper: 'clone',
            distance: 20,
            zIndex: 99,
            start: function(e, ui) {
                /* cek apakah diperbolehkan didrag atau tidak */
                var _w = ui.helper;
                if (_w.find('a').length == 0) {
                    /* jika jumlah jantan dan betina belum diisi / masih kosong maka tidak diterima */
                    var _objTemp = {};
                    //	var _yangDicek = ['jantan','betina'];
                    var _yangDicek = [];
                    var _totalPopulasi = 0;
                    var _error = 0;
                    var _kapasitas = 0;
                    _w.find('div[class^=col]').each(function() {
                        if (Config._indexHeader[$(this).index()] == 'kapasitas') {
                            _kapasitas += parse_number($(this).text(), '.', ',');
                        }
                        if (in_array(Config._indexHeader[$(this).index()], _yangDicek)) {
                            if ($(this).text() == '-' || empty($(this).text())) {
                                _error = 1;
                                toastr.error(Config._indexHeader[$(this).index()] + ' harus diisi');
                                return false;
                            } else {
                                _totalPopulasi += parse_number($(this).text(), '.', ',');
                            }
                        }
                    });
                    /* cek apakah totalpopulasi > kapasitas kandang */
                    if (_totalPopulasi > _kapasitas) {
                        _error = 1;
                        toastr.error('Populasi melebihi kapasitas maksimal');
                    }
                    if (_error) {
                        return false;
                    } else {
                        return true;
                    }
                };
            },
        });
    },
    tutup_siklus_row_edit: function(elm) {
        $(elm).dblclick(function() {
            /* yang bisa diedit hanya 2 element terakhir saja, jumlah betina dan jantan */
            var _totalAnak = $(this).siblings().length + 1;
            if ($(this).index() >= (_totalAnak - 2)) {
                Forecast.inline_edit($(this));
            }
        });

    },
    draggable_forecast_tree: function(elm) {
        $(elm).draggable({
            revert: 'invalid',
            helper: 'clone',
            distance: 20,
            zIndex: 99,
            start: function(e, ui) {

                /* jika statusnya Acc atau rilis maka gak bisa didrag */
                if (in_array(ui.helper.find('span.label').text(), Forecast.getLockEditDocIn())) {
                    toastr.error('Status sudah dikunci, tidak bisa didrag');
                    return false;
                }
            },

        });
    },
    add_contextmenu: function(elm, target, callback) {
        $(elm).contextmenu({
            target: target,
            onItem: function(context, e) {
                callback(context, e);
            }
        });
    },
    is_bulan: function(str) {
        return Config._regexBulan.test(str);
    },
    is_tahun: function(str) {
        return Config._regexTahun.test(str);
    },
    ubah_populasi_kandang: function(context) {

        var _tmp_context = $(context).find('span.hide[data-value=detail_kandang]').text().split('/');
        /* map berdasarkan _indexHeader biar mudah */
        var _data_context = Config.mappingHeader(_tmp_context);
        /* periksa status dari rencana DOCIn */
        var _status = $(context).find('span._status_approval').text();

        if (!in_array(_status, Forecast.getLockEditDocIn())) {
            var _tgl = $(context).closest('ul').closest('li');
            var _bulan = _tgl.closest('ul').closest('li');
            var _tahun = _bulan.closest('ul').closest('li');
            var _tglDocIn = _tgl.find('label').text() + ' ' + _bulan.find('label:first').text() + ' ' + _tahun.find('label:first').text();

            var bootbox_content = {
                input_str: [
                    '<form class="form-horizontal block_lokal">',
                    '<div class="form-group">',
                    '<label class="col-md-4 control-label" for="kandang">Kandang</label> ',
                    '<div class="col-md-4">',
                    '<div class="input-group">',
                    '<label name="kandang" class="form-control">' + _data_context['kandang'] + '</label>',
                    '</div>',
                    '</div>',
                    '</div>',
                    '<div class="form-group">',
                    '<label class="col-md-4 control-label" for="tglDocIn">Tanggal DOC In</label> ',
                    '<div class="col-md-4">',
                    '<div class="input-group">',
                    '<input name="tglDocIn" type="text" class="form-control input-md" data-original="' + _tglDocIn + '" value="' + _tglDocIn + '" readonly>',
                    '<label for="tglDocIn" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>',
                    '</div>',
                    '</div>',
                    '</div>',
                    '<div class="form-group">',
                    '<label class="col-md-4 control-label" for="kapasitas">Kapasitas</label> ',
                    '<div class="col-md-4">',
                    '<div class="input-group">',
                    '<input name="kapasitas" type="text" class="form-control input-md numeric" value="' + _data_context['kapasitas'] + '" readonly>',
                    '<span class="input-group-addon">ekor</span>',
                    '</div>',
                    '</div>',
                    '</div>',
                    '<div class="form-group">',
                    '<label class="col-md-4 control-label" for="jantan">Jantan</label> ',
                    '<div class="col-md-4">',
                    '<div class="input-group">',
                    '<input name="jantan" type="text" class="form-control input-md numeric" data-original="' + _data_context['jantan'] + '" value="' + _data_context['jantan'] + '" readonly>',
                    '<span class="input-group-addon">ekor</span>',
                    '</div>',
                    '</div>',
                    '</div>',
                    '<div class="form-group">',
                    '<label class="col-md-4 control-label" for="betina">Betina</label> ',
                    '<div class="col-md-4">',
                    '<div class="input-group">',
                    '<input name="betina" type="text" class="form-control input-md numeric" data-orginal="' + _data_context['betina'] + '" value="' + _data_context['betina'] + '" readonly>',
                    '<span class="input-group-addon">ekor</span>',
                    '</div>',
                    '</div>',
                    '</div>',
                    '<div class="form-group">',
                    '<div class="col-md-4 col-md-offset-4">',
                    '<div class="input-group">',
                    '<span class="btn btn-primary" onclick="Forecast.click_button_footer(\'set\')">Set</span>',
                    '</div>',
                    '</div>',
                    '</div>',
                    '</form>'

                ],
                content: function() {
                    var _obj = $('<div/>').html(this.input_str.join(''));
                    var _tglServer = new Date(Config._tglServer);
                    _tglServer.setDate(_tglServer.getDate() + 21);
                    _obj.find('input[name=tglDocIn]').datepicker({
                        dateFormat: 'dd M yy',
                        minDate: _tglServer,
                        yearRange: '+0:+1',
                        //	changeYear : true,
                    });
                    _obj.find('input.numeric').priceFormat({
                        prefix: '',
                        centsLimit: 0,
                        thousandsSeparator: '.'
                    });
                    return _obj;
                }
            };
            var _options = {
                title: 'Perubahan Perencanaan DOC In',
                message: bootbox_content.content(),
                buttons: {
                    set: {
                        label: 'Set',
                        className: 'hide',
                        callback: function(e) {
                            var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                            /* cek apakah jumlah yang diinputkan tidak melebihi kapasitas kandang
                            var _kapasitas = parse_number(_form.find('input[name=kapasitas]').val(),'.',',');
                            var _betina = parse_number(_form.find('input[name=betina]').val(),'.',',');
                            var _jantan = parse_number(_form.find('input[name=jantan]').val(),'.',',');
                            var _total_populasi = _jantan + _betina;
                            if(_kapasitas >= _total_populasi){
                            	Forecast.set_populasi_kandang(_form,context);
                            }
                            else{
                            	toastr.error('jumlah yang diinput '+_total_populasi+' ekor melebihi kapasitas kandang');
                            }
                            */
                            Forecast.set_populasi_kandang(_form, context);
                        }
                    }
                },
            };

            bootbox.dialog(_options);
        } else {
            toastr.error('Status sudah ' + _status + ', tidak bisa dirubah');
        }
    },

    ubah_tanggal_docin_bdy: function(context) {
        var _error = 0;
        var _tgl = $(context).closest('li');

        var _bulan = _tgl.closest('ul').closest('li');
        var _tahun = _bulan.closest('ul').closest('li');
        var _tmp_tgl = _tgl.find('label').text().split('(');

        var _tglDocIn = $.trim(_tmp_tgl[0]) + ' ' + _bulan.find('label:first').text() + ' ' + _tahun.find('label:first').text();
        var _docInDate = new Date(Config._convertTgl(Config._tanggalDb(_tglDocIn, ' ', '-')));
        var _tglServer = new Date(Config._tglServer);
        var _kandang = [],
            _totpopulasi = 0;
        var _kode_farm;
        var _farm = _tgl.closest('li.nama_farm');
        var _tglTerlarang = [];
        _farm.find('label.bulan').each(function() {
            // level tahun
            var _bl = $(this);
            var _th = _bl.closest('ul').siblings('label').text();
            var _blLabel = Config._indexBulan(_bl.text());
            var _tgl;
            _bl.siblings('ul').find('li>label').each(function() {
                _tgl = $(this).text().substr(0, 2);
                _tglTerlarang.push(Config._convertTgl([_th, _blLabel, _tgl].join('-')));
            });
        });

        _tgl.find('ul>li').each(function() {
            var _tmp_context = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
            /* map berdasarkan _indexHeader biar mudah */
            var _data_context = Config.mappingHeader(_tmp_context);
            _kandang.push(_data_context['kandang']);
            _totpopulasi += parse_number(_data_context['jantan'], '.', ',');
            if (_kode_farm === undefined) {
                _kode_farm = _data_context['kode_farm'];
            }
        });
        _tglServer.setDate(_tglServer.getDate() + Forecast.minEditDocIn);
        /*

        if(_docInDate < _tglServer){
        	_error++;
        	toastr.error('Maksimal mengubah tanggal DOC In adalah H - '+Forecast.maxEditDocIn+ ' dari tanggal DOC In');
        }
        */
        var bootbox_content = {
            input_str: [
                '<form class="form-horizontal block_lokal">',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Kandang</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<label name="kandang" class="form-control">' + _kandang.join(',') + '</label>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="tglDocIn">Tanggal DOC In</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<input name="tglDocIn" type="text" class="form-control input-md" data-original="' + _tglDocIn + '" value="' + _tglDocIn + '" readonly>',
                '<label for="tglDocIn" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="populasi">Populasi</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<input name="populasi" type="text" class="form-control input-md numeric" value="' + _totpopulasi + '" readonly>',
                '<span class="input-group-addon">ekor</span>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<div class="col-md-4 col-md-offset-4">',
                '<div class="input-group">',
                '<span class="btn btn-primary" onclick="Forecast.click_button_footer(\'set\')">Set</span>',
                '</div>',
                '</div>',
                '</div>',
                '</form>'

            ],
            content: function() {
                var _obj = $('<div/>').html(this.input_str.join(''));
                var _maxDate = new Date();
                _maxDate.setDate(_maxDate.getDate() + Forecast.maxEditDocIn);
                _obj.find('input[name=tglDocIn]').datepicker({
                    dateFormat: 'dd M yy',
                    beforeShowDay: function(date) { return [!in_array(Config._convertTgl(Config._getDateStr(date)), _tglTerlarang)] },
                    minDate: _tglServer,
                    maxDate: _maxDate,
                    yearRange: '+0:+1',
                    //	changeYear : true,
                });
                _obj.find('input.numeric').priceFormat({
                    prefix: '',
                    centsLimit: 0,
                    thousandsSeparator: '.'
                });
                return _obj;
            }
        };
        var _options = {
            title: 'Perubahan Perencanaan DOC In',
            message: bootbox_content.content(),
            buttons: {
                set: {
                    label: 'Set',
                    className: 'hide',
                    callback: function(e) {
                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                        Forecast.set_tanggal_docin(_form, context, _kode_farm);
                    }
                }
            },
        };
        if (!_error) {
            bootbox.dialog(_options);
        }

    },

    ubah_tanggal_docin_bdy: function(context) {
        var _error = 0;
        var _tgl = $(context).closest('li');

        var _bulan = _tgl.closest('ul').closest('li');
        var _tahun = _bulan.closest('ul').closest('li');
        var _tmp_tgl = _tgl.find('label').text().split('(');

        var _tglDocIn = $.trim(_tmp_tgl[0]) + ' ' + _bulan.find('label:first').text() + ' ' + _tahun.find('label:first').text();
        var _docInDate = new Date(Config._convertTgl(Config._tanggalDb(_tglDocIn, ' ', '-')));
        var _tglServer = new Date(Config._tglServer);
        var _kandang = [],
            _totpopulasi = 0;
        var _kode_farm;
        var _farm = _tgl.closest('li.nama_farm');
        var _tglTerlarang = [];
        _farm.find('label.bulan').each(function() {
            // level tahun
            var _bl = $(this);
            var _th = _bl.closest('ul').siblings('label').text();
            var _blLabel = Config._indexBulan(_bl.text());
            var _tgl;
            _bl.siblings('ul').find('li>label').each(function() {
                _tgl = $(this).text().substr(0, 2);
                _tglTerlarang.push(Config._convertTgl([_th, _blLabel, _tgl].join('-')));
            });
        });

        _tgl.find('ul>li').each(function() {
            var _tmp_context = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
            /* map berdasarkan _indexHeader biar mudah */
            var _data_context = Config.mappingHeader(_tmp_context);
            _kandang.push(_data_context['kandang']);
            _totpopulasi += parse_number(_data_context['jantan'], '.', ',');
            if (_kode_farm === undefined) {
                _kode_farm = _data_context['kode_farm'];
            }
        });
        _tglServer.setDate(_tglServer.getDate() + Forecast.minEditDocIn);
        /*

        if(_docInDate < _tglServer){
        	_error++;
        	toastr.error('Maksimal mengubah tanggal DOC In adalah H - '+Forecast.maxEditDocIn+ ' dari tanggal DOC In');
        }
        */
        var bootbox_content = {
            input_str: [
                '<form class="form-horizontal block_lokal">',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Kandang</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<label name="kandang" class="form-control">' + _kandang.join(',') + '</label>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="tglDocIn">Tanggal DOC In</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<input name="tglDocIn" type="text" class="form-control input-md" data-original="' + _tglDocIn + '" value="' + _tglDocIn + '" readonly>',
                '<label for="tglDocIn" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="populasi">Populasi</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<input name="populasi" type="text" class="form-control input-md numeric" value="' + _totpopulasi + '" readonly>',
                '<span class="input-group-addon">ekor</span>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<div class="col-md-4 col-md-offset-4">',
                '<div class="input-group">',
                '<span class="btn btn-primary" onclick="Forecast.click_button_footer(\'set\')">Set</span>',
                '</div>',
                '</div>',
                '</div>',
                '</form>'

            ],
            content: function() {
                var _obj = $('<div/>').html(this.input_str.join(''));
                var _maxDate = new Date();
                _maxDate.setDate(_maxDate.getDate() + Forecast.maxEditDocIn);
                _obj.find('input[name=tglDocIn]').datepicker({
                    dateFormat: 'dd M yy',
                    beforeShowDay: function(date) { return [!in_array(Config._convertTgl(Config._getDateStr(date)), _tglTerlarang)] },
                    minDate: _tglServer,
                    maxDate: _maxDate,
                    yearRange: '+0:+1',
                    //	changeYear : true,
                });
                _obj.find('input.numeric').priceFormat({
                    prefix: '',
                    centsLimit: 0,
                    thousandsSeparator: '.'
                });
                return _obj;
            }
        };
        var _options = {
            title: 'Perubahan Perencanaan DOC In',
            message: bootbox_content.content(),
            buttons: {
                set: {
                    label: 'Set',
                    className: 'hide',
                    callback: function(e) {
                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                        Forecast.set_tanggal_docin(_form, context, _kode_farm);
                    }
                }
            },
        };
        if (!_error) {
            bootbox.dialog(_options);
        }

    },

    modal_ubah_flok_bdy: function(context) {
        var _url = 'forecast/forecast/getFlokKandang';
        var _noreg = $(context).siblings('.no_reg').text();
        var _kodekandang = _noreg.substr(-2, 2);
        var _namafarm = $(context).closest('li.nama_farm').find('label:first').text()
        var _siklus = _noreg.substr(3, 6);
        $.get(_url, { no_reg: _noreg }, function(data) {
            var _flok = data.content.flok;
            var _flokArr = [];
            for (var _i in _flok) {
                _flokArr.push('<option data-tgl_panen="' + _flok[_i]['tgl_panen'] + '" value="' + _flok[_i]['tgl_doc_in'] + '">Flock ' + _flok[_i]['flok_bdy'] + '</option>');
            }
            var input_str = [
                '<form class="form-horizontal block_lokal">',
                '<center><h4><strong><u>Informasi Kandang</u></strong></h4></center>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Farm</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<label class="form-control">' + _namafarm + '</label>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Kandang</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<label class="form-control">Kandang ' + _kodekandang + '</label>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Siklus</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<label class="form-control">' + _siklus + '</label>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Flock</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<select name="flok_bdy" class="form-control">' + _flokArr.join('') + '</select>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<div class="col-md-4 col-md-offset-4">',
                '<div class="input-group">',
                /*    '<span class="btn btn-primary" onclick="Forecast.click_button_footer(\'set\')">Ubah</span>',*/
                '</div>',
                '</div>',
                '</div>',
                '</form>'
            ];
            var _options = {
                title: 'Ubah Kandang',
                message: input_str.join(''),
                buttons: {
                    ubah: {
                        label: 'Ubah',
                        className: '',
                        callback: function(e) {
                            bootbox.confirm({
                                title: 'Konfirmasi ',
                                message: 'Apakah anda yakin akan mengubah informasi kandang pada siklus baru ?',
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
                                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                                        Forecast.set_flok_kandang(_form, context, _noreg);
                                    }
                                }
                            });
                            return false;
                        }
                    }
                },
            };

            bootbox.dialog(_options);
        }, 'json');


    },
    set_tanggal_docin: function(_form, context, _kode_farm) {
        /* kumpulkan data form */
        var _form = _form;
        var _elmTglDocIn = _form.find('input[name=tglDocIn]');

        /* cek apakah tglDocIn berubah atau tidak, jika berubah maka sesuaikan */
        var _tglBerubah = (_elmTglDocIn.val() == _elmTglDocIn.data('original'));

        /* tempatkan sesuai dengan tanggalnya */
        if (!_tglBerubah) {
            /* simpan di database */
            $.ajax({
                url: 'forecast/forecast/update_tgl_docin',
                data: { tglDocIn: Config._tanggalDb(_elmTglDocIn.val(), ' '), tglDocInAsal: Config._tanggalDb(_elmTglDocIn.data('original'), ' '), kodeFarm: _kode_farm },
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        var _tmpTgl = _elmTglDocIn.val().split(' ');
                        /* cari apakah tahunnya sudah ada */
                        var _tree = context.closest('li.nama_farm');
                        var _elmTahun = _tree.find('ul>li>label:contains("' + _tmpTgl[2] + '")');
                        if (_elmTahun.length) {
                            var _elmBulan = _elmTahun.closest('li').find('ul>li>label:contains("' + _tmpTgl[1] + '")');
                            if (_elmBulan.length) {
                                var _elmTanggal = _elmBulan.closest('li').find('ul>li>label:contains("' + _tmpTgl[0] + '")');

                                if (_elmTanggal.length) {
                                    _elmTanggal.siblings('ul').append(context.closest('li'));
                                }
                                /* buat tanggalnya dulu */
                                else {
                                    var _r = new Date().getTime();
                                    var _newTanggal = context.closest('li');
                                    //	Forecast.droppable_tree(_newTanggal);
                                    var _nextElm = null;
                                    var _tmp = _tmpTgl[0];
                                    _elmBulan.siblings('ul').find('li>label').each(function() {
                                        if (_tmp < $(this).text()) {
                                            _nextElm = $(this).closest('li');
                                            return false;
                                        }
                                    });
                                    if (!empty(_nextElm)) {
                                        _newTanggal.insertBefore(_nextElm);
                                    } else {
                                        _elmBulan.siblings('ul').append(_newTanggal);
                                    }
                                }
                            }
                            /* buat bulannya dulu, bila belum ada */
                            else {
                                var _r = new Date().getTime();
                                var _newBulan = Forecast.buat_tree_bulan(_r, _tmpTgl[1]);
                                _r++;
                                var _newTanggal = Forecast.buat_tree_tanggal(_r, _tmpTgl[0]);
                                //	Forecast.droppable_tree(_newTanggal);
                                _newTanggal.find('ul:first').append(context.closest('li'));
                                _newBulan.find('ul:first').append(_newTanggal);
                                //		Forecast.add_contextmenu_bulan(_newBulan);
                                var _nextElm = null;
                                var _tmp = Config._indexBulan(_tmpTgl[1]);
                                _elmTahun.siblings('ul').find('li>label.bulan').each(function() {
                                    if (_tmp < Config._indexBulan($(this).text())) {
                                        _nextElm = $(this).closest('li');
                                        return false;
                                    }
                                });
                                if (!empty(_nextElm)) {
                                    _newBulan.insertBefore(_nextElm);
                                } else {
                                    /* cek apakah sudah memiliki ul atau belum */
                                    if (_elmTahun.siblings('ul').length) {
                                        _elmTahun.siblings('ul').append(_newBulan);
                                    } else {
                                        var _ul = $('<ul></ul>');
                                        _ul.append(_newBulan);
                                        _elmTahun.parent().append(_ul);
                                    }
                                }
                            }
                            /*update tanggal pada label */
                            var _newText = context.text().split(' (');
                            _newText[0] = _tmpTgl[0] + ' (';
                            context.text(_newText.join(' '));
                            toastr.success('Tanggal Doc In berhasil dirubah');
                        } else {
                            alert('mustahil');
                        }
                    } else {
                        toastr.error(data.message);
                    }
                }
            });

        }
        //	Forecast.periksaApproval();
    },


    set_populasi_kandang: function(_form, context) {
        /* kumpulkan data form */
        var _form = _form;
        var _elmTglDocIn = _form.find('input[name=tglDocIn]');
        var _jantan = _form.find('input[name=jantan]').val();
        var _betina = _form.find('input[name=betina]').val();
        /* cek apakah tglDocIn berubah atau tidak, jika berubah maka sesuaikan */
        var _tglBerubah = (_elmTglDocIn.val() == _elmTglDocIn.data('original'));
        var _tmp_context = $(context).find('span.hide[data-value=detail_kandang]').text().split('/');
        /* map berdasarkan _indexHeader biar mudah */
        var _data_context = Config.mappingHeader(_tmp_context);
        _data_context['jantan'] = _jantan;
        _data_context['betina'] = _betina;
        var _textTampil = 'Kandang ' + _data_context['kandang'] + ' (J : ' + _data_context['jantan'] + ', B : ' + _data_context['betina'] + ' )';
        var _detail_kandang = $.map(_data_context, function(el) { return el; });
        /* tandai telah berubah */
        context.addClass('telahBerubah');

        /* update tampilan pada link kandang */
        context.find('a').text(_textTampil);
        context.find('span[data-value=detail_kandang]').text(_detail_kandang.join('/'));
        /* tempatkan sesuai dengan tanggalnya */
        if (!_tglBerubah) {
            /* format tanggalnya seperti 20-Mar-2015 */
            var _tmpTgl = _elmTglDocIn.val().split(' ');
            /* cari apakah tahunnya sudah ada */
            var _tree = context.closest('.css-treeview');
            var _elmTahun = _tree.find('ul>li>label:contains("' + _tmpTgl[2] + '")');
            if (_elmTahun.length) {
                var _elmBulan = _elmTahun.closest('li').find('ul>li>label:contains("' + _tmpTgl[1] + '")');
                if (_elmBulan.length) {
                    var _elmTanggal = _elmBulan.closest('li').find('ul>li>label:contains("' + _tmpTgl[0] + '")');
                    if (_elmTanggal.length) {
                        _elmTanggal.siblings('ul').append(context);
                    }
                    /* buat tanggalnya dulu */
                    else {
                        var _r = new Date().getTime();
                        var _newTanggal = Forecast.buat_tree_tanggal(_r, _tmpTgl[0]);

                        Forecast.droppable_tree(_newTanggal);
                        _newTanggal.find('ul:first').append(context);
                        var _nextElm = null;
                        var _tmp = _tmpTgl[0];

                        _elmBulan.siblings('ul').find('li>label').each(function() {
                            if (_tmp < $(this).text()) {
                                _nextElm = $(this).closest('li');
                                return false;
                            }
                        });
                        if (!empty(_nextElm)) {
                            _newTanggal.insertBefore(_nextElm);
                        } else {
                            _elmBulan.siblings('ul').append(_newTanggal);
                        }
                    }
                }
                /* buat bulannya dulu, bila belum ada */
                else {
                    var _r = new Date().getTime();
                    var _newBulan = Forecast.buat_tree_bulan(_r, _tmpTgl[1]);
                    _r++;
                    var _newTanggal = Forecast.buat_tree_tanggal(_r, _tmpTgl[0]);
                    Forecast.droppable_tree(_newTanggal);
                    _newTanggal.find('ul:first').append(context);
                    _newBulan.find('ul:first').append(_newTanggal);
                    Forecast.add_contextmenu_bulan(_newBulan);
                    var _nextElm = null;
                    var _tmp = Config._indexBulan(_tmpTgl[1]);
                    _elmTahun.siblings('ul').find('li>label.bulan').each(function() {
                        if (_tmp < Config._indexBulan($(this).text())) {
                            _nextElm = $(this).closest('li');
                            return false;
                        }
                    });
                    if (!empty(_nextElm)) {
                        _newBulan.insertBefore(_nextElm);
                    } else {
                        /* cek apakah sudah memilik ul atau belum */
                        if (_elmTahun.siblings('ul').length) {
                            _elmTahun.siblings('ul').append(_newBulan);
                        } else {
                            var _ul = $('<ul></ul>');
                            _ul.append(_newBulan);
                            _elmTahun.parent().append(_ul);
                        }

                    }


                }

            } else {
                alert('mustahil');
            }
        }
        Forecast.periksaApproval();
    },

    buat_tree_bulan: function(_r, _text) {
        return $('<li><input id="' + _r + '" type="checkbox"><label class="bulan" for="' + _r + '">' + _text + '</label><ul></ul></li>');
    },

    buat_tree_tanggal: function(_r, _text) {
        return $('<li><input id="' + _r + '" type="checkbox"><label for="' + _r + '">' + _text + '</label><ul></ul></li>');
    },

    add_contextmenu_kandang: function(elm) {
        /* matikan context menu */

        $(elm).contextmenu({
            target: '#context-menu-kandang',
            onItem: function(context, e) {
                Forecast.ubah_populasi_kandang(context);
            }
        });

        $(elm).each(function() {
            Forecast.list_kebutuhan_pakan_perkandang($(this), 0);
        });


    },

    add_contextmenu_kandang_bdy: function(elm) {
        $(elm).each(function() {
            Forecast.list_kebutuhan_pakan_perkandang_bdy($(this), 0);
        });


    },


    add_contextmenu_tahun: function(elm) {
        $(elm).contextmenu({
            target: '#context-menu-tahun',
            onItem: function(context, e) {
                Forecast.modal_tambah_bulan(context);
            }
        });
    },
    add_contextmenu_bulan: function(elm) {
        $(elm).contextmenu({
            target: '#context-menu-bulan',
            onItem: function(context, e) {
                Forecast.modal_tambah_tanggal(context);
            }
        });
    },
    add_contextmenu_tanggal: function(elm) {
        $(elm).contextmenu({
            target: '#context-menu-tanggal',
            onItem: function(context, e) {
                Forecast.ubah_tanggal_docin_bdy(context);
            }
        });
    },

    add_contextmenu_gantiflock: function(elm) {
        $(elm).contextmenu({
            target: '#context-menu-gantiflock',
            onItem: function(context, e) {
                Forecast.modal_ubah_flok_bdy(context);
            }
        });
    },

    modal_tambah_tanggal: function(context) {
        var _bulan = $.datepicker.regional['id'].monthNamesShort;
        var _bulanpilih = context.text();
        var _indexbulan = _bulan.indexOf(_bulanpilih);
        var _tahun = context.parent().parent('ul').siblings('label').text();
        var lastDay = new Date(_tahun, _indexbulan + 1, 0);
        var lastDate = lastDay.getDate();
        var _content, _tmp = [],
            _v, _t, _s;
        var i = 1;
        var _tgllama = [];
        var _maxtanggal = new Date(Config._tglServer);
        /* cari maximal tanggal yang bisa dipilih */
        _maxtanggal.setDate(_maxtanggal.getDate() + this.maxBuatForecast);
        context.siblings('ul').find('li>label').each(function() {
            _tgllama.push($(this).text());
        });

        while (i <= lastDate) {
            _t = i;
            if (i < 10) {
                _t = '0' + _t;
            }

            if (in_array(_t, _tgllama)) {
                _s = 'disabled';
            } else if (new Date(_tahun, _indexbulan, i, 23, 59, 59) < _maxtanggal) {
                _s = 'disabled';
            } else {
                _s = '';
            }
            _v = '<div class="col-md-2"><label class="checkbox">' +
                '<input type="checkbox" value="' + _t + '"' + _s + '>' + _t +
                '</label></div>';
            _tmp.push(_v);
            i++;
        }
        _content = _tmp.join('');
        var _options = {
            title: 'Tambah Tanggal',
            message: '<div><form class="form form-horizontal"><div class="form-group">' + _content + '</div></form></div>',
            buttons: {
                tambahTanggal: {
                    label: 'Tambah',
                    className: '',
                    callback: function(e) {
                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                        Forecast.set_tambah_tanggal(_form, context);
                    }
                }
            },
        };

        bootbox.dialog(_options);
    },

    modal_tambah_bulan: function(context) {
        var _bulan = $.datepicker.regional['id'].monthNamesShort;
        var _bulanlengkap = $.datepicker.regional['id'].monthNames;
        /* ambil bulan yang sudah ada */
        var _th = context.text(); /* tahun yang dipilih */
        var _maxtanggal = new Date(Config._tglServer);
        var _bl = context.siblings('ul').find('label.bulan');
        var _bulanlama = [];
        /* cari maximal tanggal yang bisa dipilih */
        _maxtanggal.setDate(_maxtanggal.getDate() + this.maxBuatForecast);
        /* cari minimal bulan yang bisa dipilih */

        _bl.each(function() {
            _bulanlama.push($(this).text());
        });
        var _listbulan = [];
        var _t, _s;
        var _j = 1;
        var _k;
        for (var i in _bulanlengkap) {
            _k = parseInt(i) + 1;
            if (in_array(_bulan[i], _bulanlama)) {
                _s = 'disabled';
            }
            /* new Date(_th,_k,0) untuk mendapatkan tgl terakhir tiap bulan */
            else if (new Date(_th, _k, 0) < _maxtanggal) {
                _s = 'disabled';
            } else {
                _s = '';
            }
            _t = '<div class="col-md-3" >' +
                '<label class="checkbox">' +
                '<input type="checkbox" value="' + _bulan[i] + '"' + _s + '>' + _bulanlengkap[i] +
                '</label>' +
                '</div>';
            _listbulan.push(_t);

        }

        var _content = _listbulan.join('');
        var _options = {
            title: 'Tambah Bulan',
            message: '<div><form class="form form-horizontal"><div class="form-group">' + _content + '</div></form></div>',
            buttons: {
                tambahBulan: {
                    label: 'Tambah',
                    className: '',
                    callback: function(e) {
                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                        Forecast.set_tambah_bulan(_form, context);
                    }
                }
            },
        };

        bootbox.dialog(_options);

    },

    set_tambah_bulan: function(_form, context) {
        var _r = new Date().getTime();
        var _newBulan;
        var _nextElm;
        var _tmp;
        /* cek apakah sudah memilik ul atau belum */
        if (!context.siblings('ul').length) {
            var _ul = $('<ul></ul>');
            context.parent().append(_ul);
        }
        _form.find(':checked').each(function() {
            _r++;
            _tmp = $(this).val();
            _newBulan = Forecast.buat_tree_bulan(_r, _tmp);
            Forecast.add_contextmenu_bulan(_newBulan.find('label'));
            context.siblings('ul').find('li>label.bulan').each(function() {
                if (Config._indexBulan(_tmp) < Config._indexBulan($(this).text())) {
                    _nextElm = $(this).closest('li');
                    return false;
                }
            });
            if (!empty(_nextElm)) {
                _newBulan.insertBefore(_nextElm);
            } else {
                context.siblings('ul').append(_newBulan);
            }
        });
    },

    set_tambah_tanggal: function(_form, context) {
        var _r = new Date().getTime();
        var _newTanggal;
        var _nextElm;
        var _tmp;
        _form.find(':checked').each(function() {
            _r++;
            _tmp = $(this).val();
            _newTanggal = Forecast.buat_tree_tanggal(_r, _tmp);
            Forecast.droppable_tree(_newTanggal);
            _nextElm = null;
            context.siblings('ul').find('li>label').each(function() {
                if (_tmp < $(this).text()) {
                    _nextElm = $(this).closest('li');
                    return false;
                }
            });
            if (!empty(_nextElm)) {
                _newTanggal.insertBefore(_nextElm);
            } else {
                context.siblings('ul').append(_newTanggal);
            }

        });

    },

    click_button_footer: function(label) {
        $('.bootbox .modal-footer button[data-bb-handler=' + label + ']').click();
    },
    /* hitung kebutuhan pakan berdasarkan standart dan jumlah populasi, perhitungan per kandang*/
    hitung_kebutuhan_pakan: function(elm, grup_farm) {
        var _d = $(elm).find('span[data-value=detail_kandang]').text();
        var _data = Config.mappingHeader(_d.split('/'));
        var idFarm = _data['kode_farm'];
        /* tipe kandang O atau C, diambil huruf depannya saja */
        var tipe_kandang = _data['tipe'].substr(0, 1);
        var populasi_jantan = parse_number(_data['jantan'], '.', ',');
        var _tglDocIn = Forecast.tglDocInterpilih;
        var _result = {},
            _sak_perminggu, _tmp_populasi_jantan, _tmp_populasi_betina;
        if (grup_farm == undefined) {
            grup_farm = 'brd';
        }

        switch (grup_farm) {
            case 'brd':
                var strain = Forecast.get_item_data_farm('kode_strain');
                var populasi_betina = parse_number(_data['betina'], '.', ',');
                var standart_perumur = Forecast.get_standart_budidaya(strain, tipe_kandang, _tglDocIn);
                break;
            case 'bdy':
                var standart_perumur = Forecast.get_standart_budidaya_bdy(idFarm, _tglDocIn);
                break;
        }


        $.when(standart_perumur).done(function() {
            /* perhitungan untuk jantan */
            var _standart_jantan = standart_perumur['j'];
            var _standart_betina = standart_perumur['b'];
            var _pakan_jantan = {},
                _pakan_betina = {},
                _umur;
            _tmp_populasi_jantan = populasi_jantan;
            for (var i in _standart_jantan) {
                _umur = _standart_jantan[i]['umur'];
                _pakan_jantan[_umur] = {};
                _sak_perminggu = 0;
                if (grup_farm == 'brd') {
                    /* looping sebanyak 7 kali karena perhari */
                    for (var _y = 1; _y <= 7; _y++) {
                        _sak_perminggu += Forecast.rumus_perhitungan_harian(_standart_jantan[i], _tmp_populasi_jantan);
                        _tmp_populasi_jantan = Forecast.get_populasi_deplesi(_standart_jantan[i], _tmp_populasi_jantan);
                    }
                }
                if (grup_farm == 'bdy') {
                    /* looping sebanyak 7 kali karena perhari */
                    _sak_perminggu += Forecast.rumus_perhitungan_harian(_standart_jantan[i], _tmp_populasi_jantan);
                }
                _pakan_jantan[_umur]['jmlsak'] = _sak_perminggu;
            }
            _tmp_populasi_betina = populasi_betina;
            for (var i in _standart_betina) {
                _umur = _standart_betina[i]['umur'];
                _pakan_betina[_umur] = {};
                _sak_perminggu = 0;
                if (grup_farm == 'brd') {
                    /* looping sebanyak 7 kali karena perhari */
                    for (var _y = 1; _y <= 7; _y++) {
                        _sak_perminggu += Forecast.rumus_perhitungan_harian(_standart_betina[i], _tmp_populasi_betina);
                        _tmp_populasi_betina = Forecast.get_populasi_deplesi(_standart_betina[i], _tmp_populasi_betina);
                    }
                    _pakan_betina[_umur]['jmlsak'] = _sak_perminggu;
                }
                if (grup_farm == 'bdy') {
                    /* looping sebanyak 7 kali karena perhari */
                    _sak_perminggu += Forecast.rumus_perhitungan_harian(_standart_betina[i], _tmp_populasi_betina);
                }
            }
            _result = { 'j': _pakan_jantan, 'b': _pakan_betina };
        });

        return _result;
    },

    rumus_perhitungan: function(standart_perumur, populasi) {
        /* roundup(((daya_hidup/100) * populasi * target_pakan) / (50 * 1000)) * 7 */
        var _dh = standart_perumur['dh'] / 100;
        var _target_pakan = standart_perumur['target_pakan'];
        var _persak = 50000; /* satu sak 50kg, dalam gram berarti 50 * 1000 = 50000 */

        var _jmlhari = 7; /* jumlah hari dalam satu minggu */
        return Math.ceil(_dh * populasi * _target_pakan / _persak) * _jmlhari;
    },
    rumus_perhitungan_harian: function(standart_perumur, populasi, satuan) {
        /* roundup(((daya_hidup/100) * populasi * target_pakan) / (50 * 1000)) */
        if (satuan === undefined) satuan = 'sak';
        var _target_pakan = standart_perumur['target_pakan'];
        var konversi = { 'sak': 50000, 'kg': 1000 };

        //	jadikan 2 decimal dibelakang koma
        return populasi * _target_pakan / konversi[satuan];
    },
    ceil2: function(num, decimal_count) {
        if (decimal_count === undefined) {
            decimal_count = 2;
        }
        return number_format(num, decimal_count, ',', '.');
    },
    get_populasi_deplesi: function(standart_perumur, populasi) {
        //var deplesi = standart_perumur['dh'] / 100 / 7; /* dibagi 7 karena hitung perhari */
        var deplesi = populasi / 100 * standart_perumur['dh'] / 7; /* dibagi 7 karena hitung perhari */
        //console.log(populasi + ' - ' +standart_perumur['dh'] +' - '+ deplesi + ' hasilnya :'+parseInt(populasi - (populasi * deplesi)));
        //	return populasi - (populasi * deplesi);
        return populasi - deplesi;
    },

    list_kebutuhan_pakan_perkandang: function(elm, return_data) {
        if (return_data) {
            var _r = Forecast.hitung_kebutuhan_pakan(elm);

            return _r;
        } else {
            $(elm).click(function(e) {
                var _elm_kandang_checkin = elm.parent('ul');
                var _elm_tanggal_checkin = _elm_kandang_checkin.siblings('label');
                var _elm_bulan_checkin = _elm_tanggal_checkin.parent().parent().siblings('label');
                var _elm_tahun_checkin = _elm_bulan_checkin.parent().parent().siblings('label');
                var _tglDocIn = _elm_tahun_checkin.text() + '-' + Config._indexBulan(_elm_bulan_checkin.text()) + '-' + _elm_tanggal_checkin.text();
                Forecast.tglDocInterpilih = _tglDocIn;
                /* cek apakah sudah ada pakan yang tersimpan dalam database untuk tglDocIn ini */
                var _idFarm = Forecast.data_farm['kode_farm'];
                //	var _pakan_tersimpan = Forecast.get_pakan_tersimpan(_tglDocIn,_idFarm);
                /* hapus semua class sedang_dipilih, sebagai penanda elemen yang sedang dipilih */
                $('.css-treeview li.sedang_dipilih').each(function() {
                    $(this).removeClass('sedang_dipilih');
                });
                $(elm).addClass('sedang_dipilih');

                var _r = Forecast.hitung_kebutuhan_pakan(elm);
                /* tampilkan tabel perhitungan */
                //	var _jantan = Forecast.standart_budidaya['j'];
                //	var _betina = Forecast.standart_budidaya['b'];
                var _baris = [],
                    _tbody_betina = [],
                    _tbody_jantan = [];
                var _thead = '<thead><tr><th>Umur <br />( Minggu )</th><th>Kode Pakan</th><th>Nama Pakan</th><th>Bentuk</th><th>Kuantitas<br /> ( Sak ) </th></tr></thead>';
                var _default_pakan;
                /* untuk jantan */
                var _grouping_pakan_jantan = Forecast.grouping_standart[Forecast.tglDocInterpilih]['j'];
                var _grouping_pakan_betina = Forecast.grouping_standart[Forecast.tglDocInterpilih]['b'];

                var _text_umur = '',
                    _tot_sak = 0,
                    _index_umur, _tmp_umur;
                for (var i in _grouping_pakan_jantan) {
                    _text_umur = _grouping_pakan_jantan[i]['elemen'][0] + ' s.d ' + _grouping_pakan_jantan[i]['elemen'][_grouping_pakan_jantan[i]['elemen'].length - 1];
                    _tot_sak = 0;
                    for (var _x in _grouping_pakan_jantan[i]['elemen']) {
                        _tmp_umur = _grouping_pakan_jantan[i]['elemen'][_x];
                        _tot_sak += +_r['j'][_tmp_umur]['jmlsak'];
                    }
                    _index_umur = _grouping_pakan_jantan[i]['elemen'][0];

                    //		_default_pakan = (!empty(_pakan_tersimpan)) ? { kodepj : _pakan_tersimpan[_index_umur]['kodepjjantan'],namapj : _pakan_tersimpan[_index_umur]['namapjjantan']} : Forecast.master_pakan[_grouping_pakan_jantan[i]['grup_barang']][_grouping_pakan_jantan[i]['bentuk']][0];
                    _baris = [];
                    _baris.push(_text_umur);
                    //		_baris.push(_default_pakan['kodepj']);
                    //		_baris.push(_default_pakan['namapj']);
                    _baris.push(_grouping_pakan_jantan[i]['kode_barang']);
                    _baris.push(_grouping_pakan_jantan[i]['nama_barang']);
                    _baris.push(Config._bentuk_pakan[_grouping_pakan_jantan[i]['bentuk']]);
                    _baris.push(Forecast.ceil2(_tot_sak));
                    //		_baris.push(number_format(_tot_sak,2,',','.'));
                    _tbody_jantan.push('<td>' + _baris.join('</td><td>') + '</td>');
                }

                var _tabel_jantan = '<table class="table">';
                _tabel_jantan += _thead;
                _tabel_jantan += '<tr>' + _tbody_jantan.join('</tr><tr>') + '</tr>';
                _tabel_jantan += '</table>';
                _tabel_jantan += '<div class="col-md-4 col-md-offset-3"><button class="btn btn-default"  data-grup_farm="brd" data-info="perkandang" onclick="Forecast.breakdownPakan(this,\'j\')">Breakdown kebutuhan pakan</button></div>';

                /* untuk betina */

                _text_umur = '', _tot_sak = 0;
                for (var i in _grouping_pakan_betina) {
                    _text_umur = _grouping_pakan_betina[i]['elemen'][0] + ' s.d ' + _grouping_pakan_betina[i]['elemen'][_grouping_pakan_betina[i]['elemen'].length - 1];
                    _tot_sak = 0;

                    for (var _x in _grouping_pakan_betina[i]['elemen']) {
                        _tmp_umur = _grouping_pakan_betina[i]['elemen'][_x];
                        _tot_sak += +_r['b'][_tmp_umur]['jmlsak'];
                    }
                    _index_umur = _grouping_pakan_betina[i]['elemen'][0];

                    //	_default_pakan = (!empty(_pakan_tersimpan)) ? { kodepj : _pakan_tersimpan[_index_umur]['kodepjbetina'],namapj : _pakan_tersimpan[_index_umur]['namapjbetina']} : Forecast.master_pakan[_grouping_pakan_betina[i]['grup_barang']][_grouping_pakan_betina[i]['bentuk']][0];
                    //	_default_pakan = Forecast.master_pakan[_grouping_pakan_betina[i]['grup_barang']][_grouping_pakan_betina[i]['bentuk']][0];
                    _baris = [];
                    _baris.push(_text_umur);
                    //	_baris.push(_default_pakan['kodepj']);
                    //	_baris.push(_default_pakan['namapj']);
                    _baris.push(_grouping_pakan_betina[i]['kode_barang']);
                    _baris.push(_grouping_pakan_betina[i]['nama_barang']);
                    _baris.push(Config._bentuk_pakan[_grouping_pakan_betina[i]['bentuk']]);
                    _baris.push(Forecast.ceil2(_tot_sak));
                    //	_baris.push(number_format(_tot_sak,2,',','.'));
                    _tbody_betina.push('<td>' + _baris.join('</td><td>') + '</td>');
                }

                var _tabel_betina = '<table class="table">';
                _tabel_betina += _thead;
                _tabel_betina += '<tr>' + _tbody_betina.join('</tr><tr>') + '</tr>';
                _tabel_betina += '</table>';
                _tabel_betina += '<div class="col-md-4 col-md-offset-3"><button class="btn btn-default"  data-grup_farm="brd" data-info="perkandang" onclick="Forecast.breakdownPakan(this,\'b\')">Breakdown kebutuhan pakan</button></div>';
                $('#pakan_betina').html(_tabel_betina);
                $('#pakan_jantan').html(_tabel_jantan);
                /* jadikan kolom terakhir rata kanan */
                $('#pakan_betina').find('table tbody tr').find('td:last').addClass('number');
                $('#pakan_jantan').find('table tbody tr').find('td:last').addClass('number');

                /* update informasi pada header */
                $('#TglCheckIn').text(_elm_tanggal_checkin.text() + ' ' + _elm_bulan_checkin.text() + ' ' + _elm_tahun_checkin.text());

                /* informasi dftar kandang dalam satu tanggal doc in */
                var _d = elm.find('span[data-value=detail_kandang]').text();
                var _data = Config.mappingHeader(_d.split('/'));

                var _text_pertama = '<div class="col-md-3">Kandang : ' + _data['kandang'] + ' ' + '<span class="' + elm.find('span.label').attr('class') + '">' + elm.find('span.label').text() + '</span>' + '</div><div class="col-md-3">Tipe Kandang : ' + _data['tipe'] + '</div><div class="col-md-2">Jantan : ' + _data['jantan'] + '</div>';
                var _text_kedua = '<div class="col-md-3 col-md-offset-3">Kapasitas : ' + _data['kapasitas'] + '</div><div class="col-md-2">Betina : ' + _data['betina'] + '</div>';
                /* digunakan untuk mengetahui kandang yang sedang dipilih */
                $('#infoKandang').text(_data['kandang']);
                $('#baris_pertama').html(_text_pertama);
                $('#baris_kedua').html(_text_kedua);
                /* hidden tombol simpan dan rilis
                	var _button_visible = $('#div_tombol_simpan button:visible');
                	if(_button_visible.length){
                		_button_visible.addClass('hide');
                	}
                */
                /* hide show button simpan / rilis / approve */

                //if(in_array(elm.find('span.label').text(),Forecast.lockEditDocIn)){
                if (in_array(elm.find('span.label').text(), Forecast.lockEditDocIn)) {
                    if ($('div#div_tombol_simpan>div.btn').is(':visible')) {
                        $('div#div_tombol_simpan>div.btn').hide();
                    }
                } else {
                    if ($('div#div_tombol_simpan>div.btn').is(':hidden')) {
                        $('div#div_tombol_simpan>div.btn').show();
                    }
                }
                e.stopPropagation();
                e.preventDefault();
            });
        }

    },

    list_kebutuhan_pakan_perkandang_bdy: function(elm, return_data) {
        if (return_data) {
            var _r = Forecast.hitung_kebutuhan_pakan(elm, 'bdy');
            return _r;
        } else {
            $(elm).click(function(e) {
                var _elm_kandang_checkin = elm.parent('ul');
                var _elm_tanggal_checkin = _elm_kandang_checkin.siblings('label');
                var _elm_bulan_checkin = _elm_tanggal_checkin.parent().parent().siblings('label');
                var _elm_tahun_checkin = _elm_bulan_checkin.parent().parent().siblings('label');
                var _tgl_tmp = _elm_tanggal_checkin.text().split('(');

                var _tglDocIn = _elm_tahun_checkin.text() + '-' + Config._indexBulan(_elm_bulan_checkin.text()) + '-' + $.trim(_tgl_tmp[0]);

                Forecast.tglDocInterpilih = _tglDocIn;
                /* informasi dftar kandang dalam satu tanggal doc in */
                var _d = elm.find('span[data-value=detail_kandang]').text();
                var _data = Config.mappingHeader(_d.split('/'));
                /* cek apakah sudah ada pakan yang tersimpan dalam database untuk tglDocIn ini */
                var _idFarm = _data['kode_farm'];
                //	var _pakan_tersimpan = Forecast.get_pakan_tersimpan(_tglDocIn,_idFarm);
                /* hapus semua class sedang_dipilih, sebagai penanda elemen yang sedang dipilih */
                $('.css-treeview li.sedang_dipilih').each(function() {
                    $(this).removeClass('sedang_dipilih');
                });
                $(elm).addClass('sedang_dipilih');

                var _r = Forecast.hitung_kebutuhan_pakan(elm, 'bdy');

                var _baris = [],
                    _tbody = [];
                var _thead = '<thead><tr><th>Umur <br />( Hari )</th><th>Kode Pakan</th><th>Nama Pakan</th><th>Bentuk</th><th>Kuantitas<br /> ( Sak ) </th></tr></thead>';
                var _default_pakan;
                /* untuk jantan */
                var _grouping_pakan = Forecast.grouping_standart[_idFarm][Forecast.tglDocInterpilih]['j'];

                var _text_umur = '',
                    _tot_sak = 0,
                    _index_umur, _tmp_umur;
                for (var i in _grouping_pakan) {
                    _text_umur = _grouping_pakan[i]['elemen'][0] + ' s.d ' + _grouping_pakan[i]['elemen'][_grouping_pakan[i]['elemen'].length - 1];
                    _tot_sak = 0;
                    for (var _x in _grouping_pakan[i]['elemen']) {
                        _tmp_umur = _grouping_pakan[i]['elemen'][_x];
                        _tot_sak += +_r['j'][_tmp_umur]['jmlsak'];

                    }
                    //	console.log(_tot_sak);
                    _index_umur = _grouping_pakan[i]['elemen'][0];

                    _baris = [];
                    _baris.push(_text_umur);
                    //		_baris.push(_default_pakan['kodepj']);
                    //		_baris.push(_default_pakan['namapj']);
                    _baris.push(_grouping_pakan[i]['kode_barang']);
                    _baris.push(_grouping_pakan[i]['nama_barang']);
                    //				_baris.push(Config._bentuk_pakan[_grouping_pakan[i]['bentuk']]);
                    _baris.push(_grouping_pakan[i]['bentuk']);
                    _baris.push(Forecast.ceil2(_tot_sak));
                    //		_baris.push(number_format(_tot_sak,2,',','.'));
                    _tbody.push('<td>' + _baris.join('</td><td>') + '</td>');
                }

                var _tabel = '<table class="table">';
                _tabel += _thead;
                _tabel += '<tr>' + _tbody.join('</tr><tr>') + '</tr>';
                _tabel += '</table>';
                _tabel += '<div class="col-md-4 col-md-offset-3"><button class="btn btn-default" data-grup_farm="bdy" data-info="perkandang" data-kode_farm="' + _idFarm + '" onclick="Forecast.breakdownPakanBdy(this,\'j\')">Breakdown kebutuhan pakan</button></div>';

                $('#pakan_jantan').html(_tabel);
                /* jadikan kuantitas rata kanan */

                $('#pakan_jantan').find('table tbody tr').find('td:last').addClass('number');

                /* update informasi pada header */
                $('#TglCheckIn').text($.trim(_tgl_tmp[0]) + ' ' + _elm_bulan_checkin.text() + ' ' + _elm_tahun_checkin.text());

                var _text_pertama = '<div class="col-md-3">Kandang : ' + _data['kandang'] + ' ' + '<span class="' + elm.find('span.label').attr('class') + '">' + elm.find('span.label').text() + '</span>' + '</div><div class="col-md-3">Tipe Kandang : ' + _data['tipe'] + '</div><div class="col-md-2">Jantan : ' + _data['jantan'] + '</div>';
                var _text_kedua = '<div class="col-md-3 col-md-offset-3">Kapasitas : ' + _data['kapasitas'] + '</div>';
                /* digunakan untuk mengetahui kandang yang sedang dipilih */
                $('#infoKandang').text(_data['kandang']);
                $('#baris_pertama').html(_text_pertama);
                $('#baris_kedua').html(_text_kedua);

                /* hide show button simpan / rilis / approve */

                //if(in_array(elm.find('span.label').text(),Forecast.lockEditDocIn)){
                if (in_array(elm.find('span.label').text(), Forecast.lockEditDocIn)) {
                    if ($('div#div_tombol_simpan>div.btn').is(':visible')) {
                        $('div#div_tombol_simpan>div.btn').hide();
                    }
                } else {
                    if ($('div#div_tombol_simpan>div.btn').is(':hidden')) {
                        $('div#div_tombol_simpan>div.btn').show();
                    }
                }

                //	Forecast.detail_perkandang_bdy(_idFarm,_tglDocIn,_data['kandang']);

                e.stopPropagation();
                e.preventDefault();
            });
        }

    },

    filter_content: function(elm) {
        var _cari = $.trim($(elm).val());
        $('#div_forecast').find('li:contains(Kandang)').closest('ul').siblings('input:checkbox').prop('checked', 0);
        if (!empty(_cari)) {
            $('#div_forecast').find('li:contains(Kandang ' + _cari + ')').closest('ul').siblings('input:checkbox').prop('checked', 1);
        }

    },

    list_kebutuhan_pakan_pertanggal_bdy: function(elm) {
        elm.click(function(e) {
            var _show_simpan = 0;
            /* hidden semua list kandang pada tanggal lainnya */
            $('.css-treeview label.bulan').each(function() {
                $(this).siblings('ul').find(':checked').prop('checked', 0);
            });

            elm.prop('checked', 1);
            $('.css-treeview li.sedang_dipilih').each(function() {
                $(this).removeClass('sedang_dipilih');
            });
            $(elm).closest('li').addClass('sedang_dipilih');
            //elm.siblings('label').addClass('label-success');
            /* jika diklik untuk menampilkan anaknya maka tampilkan kebutuhan kandang */
            var _show_kebutuhan_pakan = elm.siblings('ul:visible').find('li').length;

            /* dapatkan semua id kandang */
            var _semua_kandang = [];
            var _status_kandang = [];
            var _data, _d, _tot = 0;
            var _bisaRubahPakan = 1,
                _status;

            var _elm_tanggal_checkin = elm.siblings('label');
            var _elm_bulan_checkin = _elm_tanggal_checkin.parent().parent().siblings('label');
            var _elm_tahun_checkin = _elm_bulan_checkin.parent().parent().siblings('label');
            var _tgl_tmp = _elm_tanggal_checkin.text().split('(');

            var _tglDocIn = _elm_tahun_checkin.text() + '-' + Config._indexBulan(_elm_bulan_checkin.text()) + '-' + $.trim(_tgl_tmp[0]);
            /* set tglDocIn yang sedang dipilih */
            Forecast.tglDocInterpilih = _tglDocIn;
            /* update informasi pada header */
            $('#TglCheckIn').text($.trim(_tgl_tmp[0]) + ' ' + _elm_bulan_checkin.text() + ' ' + _elm_tahun_checkin.text());
            $('#pakan_betina').empty();
            $('#pakan_jantan').empty();

            /* hapus informasi kandang, digunakan jika yang dipilih adalah bukan pertanggal bukan perkandang */
            $('#infoKandang').empty();
            /* informasi dftar kandang dalam satu tanggal doc in */
            $('#baris_pertama').empty();
            $('#baris_kedua').empty();
            var farm_tmp;
            if (_show_kebutuhan_pakan) {
                $(e.target).siblings('ul').find('li').each(function() {
                    _d = $(this).find('span[data-value=detail_kandang]').text();
                    _data = Config.mappingHeader(_d.split('/'));
                    _semua_kandang.push(Forecast.list_kebutuhan_pakan_perkandang_bdy($(this), 1));
                    if (farm_tmp == undefined) {
                        farm_tmp = _data['kode_farm'];
                    }

                    //_status = $(this).find('span.label');
                    _status_kandang.push([_data['kandang']]);
                    _tot += parse_number(_data['jantan'], '.', ',');
                });

                /* cek apakah sudah ada pakan yang tersimpan dalam database untuk tglDocIn ini */
                var _idFarm = farm_tmp;
                var _baris = [],
                    _tbody = [];
                var _thead = '<thead><tr><th>Umur <br /> ( Hari )</th><th>Kode Pakan</th><th>Nama Pakan</th><th>Bentuk</th><th>Kuantitas <br /> ( Sak ) </th></tr></thead>';
                /* untuk jantan */
                var _grouping_pakan = Forecast.grouping_standart[_idFarm][Forecast.tglDocInterpilih]['j'];

                var _text_umur = '',
                    _tot_sak = 0,
                    _index_umur, _tmp_umur;
                var _default_pakan;
                for (var i in _grouping_pakan) {

                    _text_umur = _grouping_pakan[i]['elemen'][0] + ' s.d ' + _grouping_pakan[i]['elemen'][_grouping_pakan[i]['elemen'].length - 1];
                    _tot_sak = 0;
                    for (var _x in _grouping_pakan[i]['elemen']) {

                        _tmp_umur = _grouping_pakan[i]['elemen'][_x];

                        for (var _y in _semua_kandang) {
                            _tot_sak += +_semua_kandang[_y]['j'][_tmp_umur]['jmlsak'];
                        }

                    }
                    _index_umur = _grouping_pakan[i]['elemen'][0];


                    _baris = [];
                    _baris.push(_text_umur);

                    _baris.push(_grouping_pakan[i]['kode_barang']);
                    _baris.push(_grouping_pakan[i]['nama_barang']);
                    //		_baris.push(Config._bentuk_pakan[_grouping_pakan[i]['bentuk']]);
                    _baris.push(_grouping_pakan[i]['bentuk']);
                    _baris.push(Forecast.ceil2(_tot_sak));
                    //	_baris.push(number_format(_tot_sak,2,',','.'));
                    _tbody.push('<td>' + _baris.join('</td><td>') + '</td>');
                }

                var _tabel = '<table class="table">';
                _tabel += _thead;
                _tabel += '<tr>' + _tbody.join('</tr><tr>') + '</tr>';
                _tabel += '</table>';
                _tabel += '<div class="col-md-4 col-md-offset-3"><button class="btn btn-default"  data-grup_farm="bdy" data-info="pertanggal"  data-kode_farm="' + _idFarm + '" onclick="Forecast.breakdownPakanBdy(this,\'j\')">Breakdown kebutuhan pakan</button></div>';

                $('#pakan_jantan').html(_tabel);
                /* jadikan kuantitas rata kanan */

                $('#pakan_jantan').find('table tbody tr').find('td:last').addClass('number');
                /* informasi dftar kandang dalam satu tanggal doc in */
                var _text = '<div class="col-md-4">Kandang : ' + _status_kandang.join(',') + '</div><div class="col-md-4">Jumlah Populasi : ' + number_format(_tot, 0, ',', '.') + '</div>';
                $('#baris_pertama').html(_text);
                $('#baris_kedua').html('');


                /* hide show button simpan / rilis / approve */

                if (!_show_simpan) {
                    if ($('div#div_tombol_simpan>div.btn').is(':visible')) {
                        $('div#div_tombol_simpan>div.btn').hide();
                    }
                } else {
                    if ($('div#div_tombol_simpan>div.btn').is(':hidden')) {
                        $('div#div_tombol_simpan>div.btn').show();
                    }
                }

                //	Forecast.detail_perkandang_bdy(_idFarm,_tglDocIn);
            } else {
                toastr.error('Tidak ada kandang yang Doc-In dalam tanggal ini');
            }
            e.stopPropagation();
        });
    },
    /* menampilkan data detail kandang pada tree sebelah kanan */
    detail_perkandang_bdy: function(target, kodeFarm, tglDocIn, kandang) {
        $.ajax({
            url: 'forecast/forecast/detail_kandang_bdy',
            data: { tglDocIn: tglDocIn, kodeFarm: kodeFarm, kandang: kandang },
            dataType: 'json',
            type: 'post',
            success: function(data) {
                if (data.status) {
                    $(target).html(data.content);
                } else {
                    toastr.error(data.message);
                }
            },

        });

    },

    list_kebutuhan_pakan_pertanggal: function(elm) {
        elm.click(function(e) {
            var _show_simpan = 0;
            /* hidden semua list kandang pada tanggal lainnya */
            $('.css-treeview label.bulan').each(function() {
                $(this).siblings('ul').find(':checked').prop('checked', 0);
            });
            elm.prop('checked', 1);
            $('.css-treeview li.sedang_dipilih').each(function() {
                $(this).removeClass('sedang_dipilih');
            });
            $(elm).closest('li').addClass('sedang_dipilih');
            //elm.siblings('label').addClass('label-success');
            /* jika diklik untuk menampilkan anaknya maka tampilkan kebutuhan kandang */
            var _show_kebutuhan_pakan = elm.siblings('ul:visible').find('li').length;

            /* dapatkan semua id kandang */
            var _semua_kandang = [];
            var _status_kandang = [];
            var _data, _d, _tot_jantan = 0,
                _tot_betina = 0;
            var _bisaRubahPakan = 1,
                _status;

            var _elm_tanggal_checkin = elm.siblings('label');
            var _elm_bulan_checkin = _elm_tanggal_checkin.parent().parent().siblings('label');
            var _elm_tahun_checkin = _elm_bulan_checkin.parent().parent().siblings('label');
            var _tglDocIn = _elm_tahun_checkin.text() + '-' + Config._indexBulan(_elm_bulan_checkin.text()) + '-' + _elm_tanggal_checkin.text();
            /* set tglDocIn yang sedang dipilih */
            Forecast.tglDocInterpilih = _tglDocIn;
            /* update informasi pada header */
            $('#TglCheckIn').text(_elm_tanggal_checkin.text() + ' ' + _elm_bulan_checkin.text() + ' ' + _elm_tahun_checkin.text());
            $('#pakan_betina').empty();
            $('#pakan_jantan').empty();

            /* hapus informasi kandang, digunakan jika yang dipilih adalah bukan pertanggal bukan perkandang */
            $('#infoKandang').empty();
            /* informasi dftar kandang dalam satu tanggal doc in */
            $('#baris_pertama').empty();
            $('#baris_kedua').empty();
            if (_show_kebutuhan_pakan) {
                $(e.target).siblings('ul').find('li').each(function() {
                    _semua_kandang.push(Forecast.list_kebutuhan_pakan_perkandang($(this), 1));
                    _d = $(this).find('span[data-value=detail_kandang]').text();
                    _data = Config.mappingHeader(_d.split('/'));
                    _status = $(this).find('span.label');
                    _status_kandang.push([_data['kandang']] + ' ' + '<span class="' + _status.attr('class') + '">' + _status.text() + '</span>');
                    _tot_jantan += parse_number(_data['jantan'], '.', ',');
                    _tot_betina += parse_number(_data['betina'], '.', ',');

                    /* cek apakah menmpilkan tobol simpan / rilis /approve atau tidak */
                    if (!in_array(_status.text(), Forecast.lockEditDocIn)) {
                        _show_simpan = 1;

                    }

                });
                /* cek apakah sudah ada pakan yang tersimpan dalam database untuk tglDocIn ini */
                var _idFarm = Forecast.data_farm['kode_farm'];
                //	var _pakan_tersimpan = Forecast.get_pakan_tersimpan(_tglDocIn,_idFarm);

                /* tampilkan tabel perhitungan */
                var _jantan = Forecast.standart_budidaya['j'];
                var _betina = Forecast.standart_budidaya['b'];
                var _baris = [],
                    _tbody_betina = [],
                    _tbody_jantan = [];
                var _thead = '<thead><tr><th>Umur <br /> ( Minggu )</th><th>Kode Pakan</th><th>Nama Pakan</th><th>Bentuk</th><th>Kuantitas <br /> ( Sak ) </th></tr></thead>';
                /* untuk jantan */
                var _grouping_pakan_jantan = Forecast.grouping_standart[Forecast.tglDocInterpilih]['j'];
                var _grouping_pakan_betina = Forecast.grouping_standart[Forecast.tglDocInterpilih]['b'];

                var _text_umur = '',
                    _tot_sak = 0,
                    _index_umur, _tmp_umur;
                var _default_pakan;
                for (var i in _grouping_pakan_jantan) {
                    _text_umur = _grouping_pakan_jantan[i]['elemen'][0] + ' s.d ' + _grouping_pakan_jantan[i]['elemen'][_grouping_pakan_jantan[i]['elemen'].length - 1];
                    _tot_sak = 0;
                    for (var _x in _grouping_pakan_jantan[i]['elemen']) {

                        _tmp_umur = _grouping_pakan_jantan[i]['elemen'][_x];

                        for (var _y in _semua_kandang) {
                            _tot_sak += +_semua_kandang[_y]['j'][_tmp_umur]['jmlsak'];
                        }

                    }
                    //	_default_pakan = Forecast.master_pakan[_grouping_pakan_jantan[i]['grup_barang']][_grouping_pakan_jantan[i]['bentuk']][0];

                    _index_umur = _grouping_pakan_jantan[i]['elemen'][0];
                    //	_default_pakan = (!empty(_pakan_tersimpan)) ? { kodepj : _pakan_tersimpan[_index_umur]['kodepjjantan'],namapj : _pakan_tersimpan[_index_umur]['namapjjantan']} : Forecast.master_pakan[_grouping_pakan_jantan[i]['grup_barang']][_grouping_pakan_jantan[i]['bentuk']][0];

                    _baris = [];
                    _baris.push(_text_umur);

                    _baris.push(_grouping_pakan_jantan[i]['kode_barang']);
                    _baris.push(_grouping_pakan_jantan[i]['nama_barang']);
                    _baris.push(Config._bentuk_pakan[_grouping_pakan_jantan[i]['bentuk']]);
                    _baris.push(Forecast.ceil2(_tot_sak));
                    //	_baris.push(number_format(_tot_sak,2,',','.'));
                    _tbody_jantan.push('<td>' + _baris.join('</td><td>') + '</td>');
                }

                var _tabel_jantan = '<table class="table">';
                _tabel_jantan += _thead;
                _tabel_jantan += '<tr>' + _tbody_jantan.join('</tr><tr>') + '</tr>';
                _tabel_jantan += '</table>';
                _tabel_jantan += '<div class="col-md-4 col-md-offset-3"><button class="btn btn-default"  data-grup_farm="brd" data-info="pertanggal" onclick="Forecast.breakdownPakan(this,\'j\')">Breakdown kebutuhan pakan</button></div>';
                /* untuk betina */

                _text_umur = '', _tot_sak = 0;

                for (var i in _grouping_pakan_betina) {
                    _text_umur = _grouping_pakan_betina[i]['elemen'][0] + ' s.d ' + _grouping_pakan_betina[i]['elemen'][_grouping_pakan_betina[i]['elemen'].length - 1];
                    _tot_sak = 0;
                    for (var _x in _grouping_pakan_betina[i]['elemen']) {
                        _tmp_umur = _grouping_pakan_betina[i]['elemen'][_x];
                        for (var _y in _semua_kandang) {
                            _tot_sak += +_semua_kandang[_y]['b'][_tmp_umur]['jmlsak'];
                        }

                    }
                    //  _default_pakan = Forecast.master_pakan[_grouping_pakan_betina[i]['grup_barang']][_grouping_pakan_betina[i]['bentuk']][0];
                    _index_umur = _grouping_pakan_betina[i]['elemen'][0];

                    //		_default_pakan = (!empty(_pakan_tersimpan)) ? { kodepj : _pakan_tersimpan[_index_umur]['kodepjbetina'],namapj : _pakan_tersimpan[_index_umur]['namapjbetina']} : Forecast.master_pakan[_grouping_pakan_betina[i]['grup_barang']][_grouping_pakan_betina[i]['bentuk']][0];

                    _baris = [];
                    _baris.push(_text_umur);
                    //		_baris.push(_default_pakan['kodepj']);
                    //		_baris.push(_default_pakan['namapj']);
                    _baris.push(_grouping_pakan_betina[i]['kode_barang']);
                    _baris.push(_grouping_pakan_betina[i]['nama_barang']);
                    _baris.push(Config._bentuk_pakan[_grouping_pakan_betina[i]['bentuk']]);
                    _baris.push(Forecast.ceil2(_tot_sak));
                    //		_baris.push(number_format(_tot_sak,2,',','.'));
                    _tbody_betina.push('<td>' + _baris.join('</td><td>') + '</td>');
                }

                var _tabel_betina = '<table class="table">';
                _tabel_betina += _thead;
                _tabel_betina += '<tr>' + _tbody_betina.join('</tr><tr>') + '</tr>';
                _tabel_betina += '</table>';
                _tabel_betina += '<div class="col-md-4 col-md-offset-3"><button class="btn btn-default"  data-grup_farm="brd" data-info="pertanggal" onclick="Forecast.breakdownPakan(this,\'b\')">Breakdown kebutuhan pakan</button></div>';
                $('#pakan_betina').html(_tabel_betina);
                $('#pakan_jantan').html(_tabel_jantan);
                /* jadikan kuantitas rata kanan */
                $('#pakan_betina').find('table tbody tr').find('td:last').addClass('number');
                $('#pakan_jantan').find('table tbody tr').find('td:last').addClass('number');
                /* informasi dftar kandang dalam satu tanggal doc in */
                var _text = '<div class="col-md-4">Kandang : ' + _status_kandang.join(',') + '</div><div class="col-md-2">Jantan : ' + number_format(_tot_jantan, 0, ',', '.') + '</div><div class="col-md-2">Betina : ' + number_format(_tot_betina, 0, ',', '.') + '</div>';
                $('#baris_pertama').html(_text);
                $('#baris_kedua').html('');

                /* hide show button simpan / rilis / approve */

                if (!_show_simpan) {
                    if ($('div#div_tombol_simpan>div.btn').is(':visible')) {
                        $('div#div_tombol_simpan>div.btn').hide();
                    }
                } else {
                    if ($('div#div_tombol_simpan>div.btn').is(':hidden')) {
                        $('div#div_tombol_simpan>div.btn').show();
                    }
                }

            } else {
                toastr.error('Tidak ada kandang yang Doc-In dalam tanggal ini');
            }
            e.stopPropagation();
        });
    },

    inline_selected_edit: function(elm, jk) {
        /* ambil index tr */
        var _index = elm.closest('tr').index() + 1;
        var _standart_budidaya = Forecast.grouping_standart[Forecast.tglDocInterpilih][jk][_index];
        var _master_pakan = Forecast.master_pakan[_standart_budidaya['grup_barang']][_standart_budidaya['bentuk']];
        var _elmPengganti = $('<select></select>');
        var _options = '';
        var _current = elm.text();
        var _selected = '';
        /*	_options += '<option value="">Pilih nama pakan</option>'; */
        for (var i in _master_pakan) {
            if (_current == _master_pakan[i]['kodepj']) {
                _selected = 'selected';
            } else {
                _selected = '';
            }
            _options += '<option value="' + _master_pakan[i]['kodepj'] + '" ' + _selected + '>' + _master_pakan[i]['namapj'] + '</option>';
        }
        $(_options).appendTo(_elmPengganti);
        elm.html(_elmPengganti);

        _elmPengganti.change(function() {
            if (empty(_elmPengganti.val())) {
                elm.html(_current);
            } else {
                elm.html(_elmPengganti.val());
                elm.next().html(_elmPengganti.find('option:selected').text());
                /* tambahkan class pakan_dirubah pada barisnya */
                elm.parents('tr').addClass('pakan_dirubah');
            }
        });



    },
    load_farm: function(id) {
        if (!empty(id)) {
            Forecast.setAktifFarm(id);
            //				Forecast.data_farm['kode_farm'] = id;
            $('#PerencanaanChickin').html('').load('forecast/forecast/datafarm/' + id + '/');
            /* set nilai dari idFarm */
        } else {
            toastr.warning('Pilih salah satu farm');
        }
    },

    simpan_forecast: function(_prosesKandang, _pakanBetina, _pakanJantan, _docIn, _pakanBetinaBerubah, _pakanJantanBerubah) {
        var _elmKandang = _prosesKandang.insert;
        var updateKandang = _prosesKandang.update;
        /* bangun data untuk insert ke tabel kandang siklus */
        var _dataFarm = { kodeSiklus: Forecast.get_item_data_farm('kode_siklus'), periodeSiklus: Forecast.get_item_data_farm('periode_siklus'), kodeFarm: Forecast.get_item_data_farm('kode_farm') };
        var _insertKandang = [],
            _updateKandang = [],
            _tmp, _temp, _nreg, standart_breeding;
        if (!empty(_elmKandang)) {
            $.each(_elmKandang, function() {

                _tmp = $(this).find('span[data-value=detail_kandang]').text().split('/');
                _temp = Config.mappingHeader(_tmp);
                _nreg = $(this).find('span.no_reg').text();
                /* convert jumlah jantan dan betina menjadi number */
                _temp['jantan'] = parse_number(_temp['jantan'], '.', ',');
                _temp['betina'] = parse_number(_temp['betina'], '.', ',');
                _temp['lantai'] = _temp['lantai'].substr(0, 1);
                _temp['tipe'] = _temp['tipe'].substr(0, 1);
                _temp['no_reg'] = _nreg;
                standart_breeding = Forecast.get_standart_budidaya(Forecast.get_item_data_farm('kode_strain'), _temp['tipe'].substr(0, 1), _docIn);
                if (standart_breeding == undefined) {
                    toastr.error('Standart budidaya tidak ditemukan, cek di master standart budidaya');
                }
                _temp['kode_std_breeding_j'] = standart_breeding['j'][0]['kode_std_breeding'];
                _temp['kode_std_breeding_b'] = standart_breeding['b'][0]['kode_std_breeding'];
                _insertKandang.push(_temp);
            });
        }

        /* update yang dirilis karena sudah draft */
        if (!empty(updateKandang)) {
            $.each(updateKandang, function() {
                _tmp = $(this).find('span[data-value=detail_kandang]').text().split('/');
                _nreg = $(this).find('span.no_reg').text();
                _temp = Config.mappingHeader(_tmp);
                /* convert jumlah jantan dan betina menjadi number */
                _temp['jantan'] = parse_number(_temp['jantan'], '.', ',');
                _temp['betina'] = parse_number(_temp['betina'], '.', ',');
                _temp['lantai'] = _temp['lantai'].substr(0, 1);
                _temp['tipe'] = _temp['tipe'].substr(0, 1);
                _temp['no_reg'] = _nreg;
                standart_breeding = Forecast.get_standart_budidaya(Forecast.get_item_data_farm('kode_strain'), _temp['tipe'].substr(0, 1), _docIn);
                if (standart_breeding == undefined) {
                    toastr.error('Standart budidaya tidak ditemukan, cek di master standart budidaya');
                }
                _temp['kode_std_breeding_j'] = standart_breeding['j'][0]['kode_std_breeding'];
                _temp['kode_std_breeding_b'] = standart_breeding['b'][0]['kode_std_breeding'];
                _updateKandang.push(_temp);
            });
        }
        $.ajax({
            type: 'post',
            data: { insertKandang: _insertKandang, updateKandang: _updateKandang, pakanJantan: _pakanJantan, pakanBetina: _pakanBetina, dataFarm: _dataFarm, _docIn: _docIn, pakanBetinaBerubah: _pakanBetinaBerubah, pakanJantanBerubah: _pakanJantanBerubah },
            url: 'forecast/forecast/simpan',
            success: function(data) {
                if (data.status) {
                    $.each(_elmKandang, function() {
                        $(this).find('span.label').removeClass('label-default').addClass('label-warning').text('Draft');

                    });
                    $.each(updateKandang, function() {
                        //$(this).find('span.label').removeClass('label-default').addClass('label-warning').text('Draft');
                        $(this).removeClass('telahBerubah');
                    });
                    if (data.pakan_berubah != undefined && data.pakan_berubah != 0) {
                        Forecast.pakan_tersimpan[_docIn] = null;
                    }
                    toastr.success('Berhasil disimpan');
                }
            },
            dataType: 'json'
        });

    },

    rilis_forecast: function(_prosesKandang, _pakanBetina, _pakanJantan, _docIn, _pakanBetinaBerubah, _pakanJantanBerubah) {
        var _elmKandang = _prosesKandang.insert;
        var updateKandang = _prosesKandang.update;
        /* bangun data untuk insert ke tabel kandang siklus */
        var _dataFarm = { kodeSiklus: Forecast.get_item_data_farm('kode_siklus'), periodeSiklus: Forecast.get_item_data_farm('periode_siklus'), kodeFarm: Forecast.get_item_data_farm('kode_farm') };
        var _insertKandang = [],
            _updateKandang = [],
            _tmp, _temp, _nreg, standart_breeding;

        if (!empty(_elmKandang)) {
            $.each(_elmKandang, function() {
                _tmp = $(this).find('span[data-value=detail_kandang]').text().split('/');
                _temp = Config.mappingHeader(_tmp);
                _nreg = $(this).find('span.no_reg').text();
                /* convert jumlah jantan dan betina menjadi number */
                _temp['jantan'] = parse_number(_temp['jantan'], '.', ',');
                _temp['betina'] = parse_number(_temp['betina'], '.', ',');
                _temp['lantai'] = _temp['lantai'].substr(0, 1);
                _temp['tipe'] = _temp['tipe'].substr(0, 1);
                _temp['no_reg'] = _nreg;
                standart_breeding = Forecast.get_standart_budidaya(Forecast.get_item_data_farm('kode_strain'), _temp['tipe'].substr(0, 1), _docIn);
                if (standart_breeding == undefined) {
                    toastr.error('Standart budidaya tidak ditemukan, cek di master standart budidaya');
                }
                _temp['kode_std_breeding_j'] = standart_breeding['j'][0]['kode_std_breeding'];
                _temp['kode_std_breeding_b'] = standart_breeding['b'][0]['kode_std_breeding'];
                _insertKandang.push(_temp);
            });
        }

        /* update yang dirilis karena sudah draft */
        if (!empty(updateKandang)) {
            $.each(updateKandang, function() {
                _tmp = $(this).find('span[data-value=detail_kandang]').text().split('/');
                _nreg = $(this).find('span.no_reg').text();
                _temp = Config.mappingHeader(_tmp);
                /* convert jumlah jantan dan betina menjadi number */
                _temp['jantan'] = parse_number(_temp['jantan'], '.', ',');
                _temp['betina'] = parse_number(_temp['betina'], '.', ',');
                _temp['lantai'] = _temp['lantai'].substr(0, 1);
                _temp['tipe'] = _temp['tipe'].substr(0, 1);
                _temp['no_reg'] = _nreg;
                standart_breeding = Forecast.get_standart_budidaya(Forecast.get_item_data_farm('kode_strain'), _temp['tipe'].substr(0, 1), _docIn);
                if (standart_breeding == undefined) {
                    toastr.error('Standart budidaya tidak ditemukan, cek di master standart budidaya');
                }
                _temp['kode_std_breeding_j'] = standart_breeding['j'][0]['kode_std_breeding'];
                _temp['kode_std_breeding_b'] = standart_breeding['b'][0]['kode_std_breeding'];
                _updateKandang.push(_temp);

            });

        }
        $.ajax({
            type: 'post',
            data: { insertKandang: _insertKandang, updateKandang: _updateKandang, pakanJantan: _pakanJantan, pakanBetina: _pakanBetina, dataFarm: _dataFarm, _docIn: _docIn, pakanBetinaBerubah: _pakanBetinaBerubah, pakanJantanBerubah: _pakanJantanBerubah },
            url: 'forecast/forecast/rilis',
            success: function(data) {
                if (data.status) {
                    $.each(_prosesKandang.insert, function() {
                        $(this).find('span.label').removeClass('label-default').addClass('label-primary').text('Baru');

                    });
                    $.each(_prosesKandang.update, function() {
                        $(this).find('span.label').removeClass('label-warning').addClass('label-primary').text('Baru');

                    });
                    if (data.pakan_berubah != undefined && data.pakan_berubah != 0) {
                        Forecast.pakan_tersimpan[_docIn] = null;
                    }
                    /* hidden tobol rilis / approve */
                    $('div#div_tombol_simpan>div.btn').hide();
                    toastr.success('Berhasil rilis');
                }

            },
            dataType: 'json'
        });
    },

    approve_forecast: function(_prosesKandang, _docIn, _pakanBetinaBerubah, _pakanJantanBerubah) {
        bootbox.confirm({
            title: 'Konfirmasi Approve',
            message: 'Setelah proses approve, data tidak bisa dirubah, Lanjut ?',
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
                    var _elmKandang = _prosesKandang.update;
                    /* bangun data untuk insert ke tabel kandang siklus */
                    var _dataFarm = { kodeSiklus: Forecast.get_item_data_farm('kode_siklus'), periodeSiklus: Forecast.get_item_data_farm('periode_siklus'), kodeFarm: Forecast.get_item_data_farm('kode_farm') };
                    var _dataKandang = [],
                        _tmp, _temp, _nreg, standart_breeding;

                    $.each(_elmKandang, function() {
                        _tmp = $(this).find('span[data-value=detail_kandang]').text().split('/');
                        _nreg = $(this).find('span.no_reg').text();
                        _temp = Config.mappingHeader(_tmp);
                        /* convert jumlah jantan dan betina menjadi number */
                        _temp['jantan'] = parse_number(_temp['jantan'], '.', ',');
                        _temp['betina'] = parse_number(_temp['betina'], '.', ',');
                        _temp['lantai'] = _temp['lantai'].substr(0, 1);
                        _temp['tipe'] = _temp['tipe'].substr(0, 1);
                        _temp['no_reg'] = _nreg;
                        standart_breeding = Forecast.get_standart_budidaya(Forecast.get_item_data_farm('kode_strain'), _temp['tipe'].substr(0, 1), _docIn);
                        if (standart_breeding == undefined) {
                            toastr.error('Standart budidaya tidak ditemukan, cek di master standart budidaya');
                        }
                        _temp['kode_std_breeding_j'] = standart_breeding['j'][0]['kode_std_breeding'];
                        _temp['kode_std_breeding_b'] = standart_breeding['b'][0]['kode_std_breeding'];
                        _dataKandang.push(_temp);

                    });

                    $.ajax({
                        type: 'post',
                        data: { dataKandang: _dataKandang, _docIn: _docIn, pakanBetinaBerubah: _pakanBetinaBerubah, pakanJantanBerubah: _pakanJantanBerubah, kodeFarm: Forecast.get_item_data_farm('kode_farm') },
                        url: 'forecast/forecast/approve',
                        success: function(data) {
                            if (data.status) {
                                $.each(_elmKandang, function() {
                                    $(this).find('span.label').removeClass('label-primary').addClass('label-info').text('Acc1');
                                    $(this).find('span.abang').remove();
                                });
                                /* hidden tombol rilis / approve */
                                $('div#div_tombol_simpan>div.btn').hide();
                                toastr.success('Berhasil approve');
                                //	var id_farm = data.kode_farm;
                                //	Forecast.load_farm(id_farm);
                                Forecast.periksaApproval();
                            }
                        },
                        dataType: 'json'
                    });
                }
            },
        });

    },
    /* blok untuk penanganan mengeset flock */
    set_flock: function(checked) {
        /* cari kandangnya */
        var _kandang = [],
            _nama_kandang, _no_reg = [];
        checked.each(function() {
            _nama_kandang = $(this).parent().next().next();
            _kandang.push(_nama_kandang.text());
            _no_reg.push($(this).attr('data-no_reg'));
        });
        var bootbox_content = {
            input_str: [
                '<form class="form-horizontal block_lokal">',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="kandang">Kandang</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<label name="kandang" class="form-control">' + _kandang.join(',') + '</label>',
                '<label name="no_reg" class="hide">' + _no_reg.join(',') + '</label>',
                '<label name="tgl_docin" class="hide">' + checked.eq(0).val() + '</label>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="jantan">Flock</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<input name="namaflok" data-kodeflok="" type="text" class="form-control input-md"  style="text-transform:uppercase" onchange="this.value=this.value.toUpperCase()">',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="col-md-4 control-label" for="tglTetas">Tanggal Tetas</label> ',
                '<div class="col-md-4">',
                '<div class="input-group">',
                '<input name="tglTetas" type="text" class="form-control input-md"  value="" readonly>',
                '<label for="tglTetas" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar"></span>',
                '</div>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<div class="col-md-4 col-md-offset-4">',
                '<div class="input-group">',
                '<span class="btn btn-primary" onclick="Forecast.click_button_footer(\'setFlock\')">Set</span>',
                '</div>',
                '</div>',
                '</div>',
                '</form>'

            ],
            content: function() {
                var _obj = $('<div/>').html(this.input_str.join(''));
                var tanggalTetas = _obj.find('input[name=tglTetas]');
                tanggalTetas.datepicker({
                    dateFormat: 'dd M yy',
                    minDate: -9,
                    yearRange: '+0:+1',
                    //	changeYear : true,
                });
                var input_kodeflock = _obj.find('input[name=namaflok]');
                input_kodeflock.autocomplete({
                        minLength: 2,
                        source: function(request, response) {
                            $.ajax({
                                type: 'post',
                                url: "api/api/flock",
                                dataType: "json",
                                data: {
                                    nama_flok: request.term,
                                    kode_farm: Forecast.data_farm['kode_farm'],
                                    tgl_docin: checked.eq(0).val(),
                                },
                                success: function(data) {
                                    response(data);
                                    tanggalTetas.datepicker('option', 'disabled', false);
                                }
                            });
                        },
                        focus: function(event, ui) {
                            input_kodeflock.val(ui.item.NAMA_FLOK);
                            return false;
                        },
                        select: function(event, ui) {
                            input_kodeflock.val(ui.item.NAMA_FLOK);
                            input_kodeflock.attr('data-kodeflok', ui.item.KODE_FLOK);
                            /* convert tanggalnya ke tanggal indonesia */
                            tanggalTetas.val(Config._tanggalLocal(ui.item.TGL_TETAS, '-', ' '));

                            /* disable datepicker */
                            tanggalTetas.datepicker('option', 'disabled', true);
                            return false;
                        }
                    })
                    .autocomplete("instance")._renderItem = function(ul, item) {
                        return $("<li>")
                            .append("<span>" + item.NAMA_FLOK + "</span>&nbsp;&nbsp;<span>" + item.TGL_TETAS + "</span>")
                            .appendTo(ul);
                    };
                return _obj;
            }
        };
        var _options = {
            title: 'Flock dan Tanggal Tetas',
            message: bootbox_content.content(),
            buttons: {
                setFlock: {
                    label: 'Set',
                    className: 'hide',
                    callback: function(e) {
                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                        /* jika data-kodeflok kosong maka buat flok baru, jika sudah ada tinggal update ke kandang siklus saja */
                        var _elmTglTetas = _form.find('input[name=tglTetas]');
                        var elmflok = _form.find('input[name=namaflok]');
                        var _tgltetas = Config._tanggalDb(_elmTglTetas.val(), ' ', '-');
                        var _namaflok = elmflok.val();
                        var _kodeflok = (_elmTglTetas.datepicker('option', 'disabled')) ? elmflok.attr('data-kodeflok') : '';
                        var _tgldocin = _form.find('label[name=tgl_docin]').text();
                        var _noreg = _form.find('label[name=no_reg]').text().split(',');
                        var _error = 0;
                        var _message = [];
                        if (empty(_namaflok)) {
                            _error++;
                            _message.push('Nama flok tidak boleh kosong');
                        }
                        if (empty(_elmTglTetas.val())) {
                            _error++;
                            _message.push('Tanggal tetas tidak boleh kosong');
                        }
                        /* simpan ke database */
                        if (!_error) {
                            $.ajax({
                                type: 'post',
                                url: 'forecast/forecast/update_flok',
                                data: { tgldocin: _tgldocin, tgltetas: _tgltetas, namaflok: _namaflok, kodeflok: _kodeflok, noreg: _noreg, kodefarm: Forecast.data_farm['kode_farm'] },
                                success: function(data) {
                                    if (data.status) {
                                        /* update tampilan kodeflok dan tgltetas */
                                        var _tr;
                                        checked.each(function() {
                                            /* flok pada index 6 dan tgltetas pada index 7*/
                                            _tr = $(this).parents('tr');
                                            _tr.find('td:nth-child(5)').html(_namaflok);
                                            _tr.find('td:nth-child(6)').html(Config._tanggalLocal(_tgltetas, '-', ' '));

                                            $(this).remove();
                                            toastr.success('Proses update flock berhasil');
                                        });
                                    } else {
                                        if (!empty(data.message)) {
                                            toastr.error(data.message);
                                        } else {
                                            toastr.error('Proses penyimpanan gagal');
                                        }

                                    }
                                },
                                dataType: 'json'
                            });
                        } else {
                            for (var i in _message) {
                                toastr.error(_message[i]);
                            }
                            return false;
                        }
                    }
                }
            },
        };

        bootbox.dialog(_options);

    },
    modal_filter_farm: function(checkedAll, farm, callback) {
        /* dapatkan semua farm */
        var _checked = '';

        if (checkedAll == 'checkedAll') {
            _checked = 'checked';
        }

        var list_all_farm = Forecast.get_list_farm(farm);
        var _content = '',
            _tmp = [],
            _t;
        var _tmp_cb = []; /* pastikan satu farm hanya tampil 1 kali saja, bisa jadi 1 farm memiliki periode siklus yang aktif > 1 */
        for (var _x in list_all_farm) {
            if (!in_array(list_all_farm[_x]['kode_farm'], _tmp_cb)) {
                _tmp_cb.push(list_all_farm[_x]['kode_farm']);
                _t = '<div class="col-md-3"><div class="checkbox"><label><input type="checkbox" ' + _checked + ' value="' + list_all_farm[_x]['kode_farm'] + '">' + list_all_farm[_x]['nama_farm'] + '</label></div></div>';
                _tmp.push(_t);
            }
        }
        _content = _tmp.join('');
        var _options = {
            title: 'Filter Farm <div class="checkbox"><label><input type="checkbox" checked onclick="Forecast.cek_uncek(this)"> Check / Uncheck All</label></div>',
            message: '<div><form class="form form-horizontal"><div class="form-group">' + _content + '</div></form></div>',
            buttons: {
                tambahTanggal: {
                    label: 'Set Filter',
                    className: '',
                    callback: function(e) {
                        var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                        callback(_form);
                    }
                }
            },
        };

        bootbox.dialog(_options);
    },
    breakdownPakan: function(elm, jk) {
        var _info = $(elm).data('info');
        var strain = Forecast.data_farm['kode_strain'];
        var _namafarm = Forecast.data_farm['nama_farm'];
        var _elmdipilih = $('.css-treeview li.sedang_dipilih').eq(0);
        var _kapasitas = [];
        var _populasi = [];
        var _kandang = [];
        var _grup_farm = $(elm).data('grup_farm');

        /* dapatkan kandang yang sedang dipilih */
        if (_info == 'perkandang') {
            var _tmp = _elmdipilih.find('span.hide[data-value=detail_kandang]').text().split('/');
            /* map berdasarkan _indexHeader biar mudah */
            var _data = Config.mappingHeader(_tmp);
            var tipe_kandang = _data['tipe'].substr(0, 1);
            _populasi.push(parse_number(_data[Config._jenis_kelamin[jk]], '.', ','));
            _kapasitas.push(parse_number(_data['kapasitas'], '.', ','));
            _kandang.push(_data['kandang']);
        } else {
            var _tmp = _elmdipilih.find('span.hide[data-value=detail_kandang]').eq(0).text().split('/');
            /* map berdasarkan _indexHeader biar mudah */
            var _data = Config.mappingHeader(_tmp);
            var tipe_kandang = _data['tipe'].substr(0, 1);
            var _tm;
            /* looping semua kandangnya */
            _elmdipilih.find('ul>li').each(function() {
                _tm = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
                _tm = Config.mappingHeader(_tm);
                //	console.log(parse_number(_tm[Config._jenis_kelamin[jk]],'.',','));
                _populasi.push(parse_number(_tm[Config._jenis_kelamin[jk]], '.', ','));
                _kapasitas.push(parse_number(_tm['kapasitas'], '.', ','));
                _kandang.push(_tm['kandang']);
            });
        }
        var _tglDocIn = Forecast.tglDocInterpilih;
        var standart_perumur = Forecast.get_standart_budidaya(strain, tipe_kandang, _tglDocIn);


        /* lakukan breakdown berdasarkan tabel yang ditampilkan */
        var _tabel = $(elm).closest('div').siblings('table');
        var _umur, _awal, _akhir, _nextDate, _tglDocIn, _curDate, _dateStr, _tglTampil, _kodepj, _namapj, _bentukpj;
        _tglDocIn = Config._convertTgl(Forecast.tglDocInterpilih);
        _nextDate = new Date(_tglDocIn);

        var standart_pakan = standart_perumur[jk];

        var _td_arr, _tr_arr = [],
            _tbody, _kebutuhan_pakan, _tmp_populasi = {};
        for (var _h in _populasi) {
            _tmp_populasi[_h] = _populasi[_h];
        }
        _tabel.find('tbody tr').each(function() {
            /*bangun tabel */
            _umur = $(this).children('td:first').html().split(' s.d ');
            _kodepj = $(this).children('td:nth-child(2)').html();
            _namapj = $(this).children('td:nth-child(3)').html();
            _bentukpj = $(this).children('td:nth-child(4)').html();;
            _awal = +_umur[0];
            _akhir = +_umur[1];

            do {
                /* looping sebanyak 7 kali karena perhari */
                for (var i = 0; i < 7; i++) {
                    _tglTampil = Config._tanggalLocal(Config._getDateStr(_nextDate), '-', ' ');
                    _kebutuhan_pakan = 0;
                    for (var _h in _tmp_populasi) {
                        _tmp_populasi[_h] = Forecast.get_populasi_deplesi(standart_pakan[_awal], _tmp_populasi[_h]);
                        _kebutuhan_pakan += Forecast.rumus_perhitungan_harian(standart_pakan[_awal], _tmp_populasi[_h], 'kg');
                        //	console.log('UMUR + HARI :'+_awal+' - ' + i + ', kandang' +_h +': populasi _awal '+ _populasi+ ' sekarang : '+ _tmp_populasi[_h]);
                    }
                    _td_arr = [_awal + '+' + i, _tglTampil, _kodepj, _namapj, _bentukpj, standart_pakan[_awal]['target_pakan'], Forecast.ceil2(_kebutuhan_pakan)];

                    _nextDate.setDate(_nextDate.getDate() + 1);
                    _tr_arr.push('<td>' + _td_arr.join('</td><td>') + '</td>');
                }
                _awal++;
            } while (_awal <= _akhir);

        });
        _tbody = '<tbody><tr>' + _tr_arr.join('</tr><tr>') + '</tr></tbody>';

        var _thead = '<thead><tr><th data-id="umur">Minggu+hari</th><th data-id="tgl">Tanggal</th><th data-id="kodepakan">Kode Pakan</th><th data-id="namapakan">Nama Pakan</th><th data-id="bentuk">Bentuk</th><th data-id="keb">Keb. Pakan <br /> /Ekor (gr)<th data-id="kuantitas">Kuantitas <br /> (Kg)</th></th></tr></thead>';
        var _header = '<div>' +
            '<div class="row">' +
            '<div class="col-md-12">' +
            '<div class="pull-left"><span class="btn btn-default" onclick="Forecast.cetakRencana(this)">Print</span></div>' +
            '<div class="text-center">' +
            '<div class="header-info">' +
            '<div>FARM ' + _namafarm + ' ' + strain + '</div>' +
            '<div>Kandang ' + _kandang.join(',') + '</div>' +
            '<div class="row">' +
            '<div class="col-md-3 col-md-offset-2 ">Tipe Kandang : ' + Config._tipe_kandang[tipe_kandang] + '</div>' +
            '<div class="col-md-3">Kapasitas : ' + array_sum(_kapasitas) + '</div>' +
            '<div class="col-md-3">' + Config._jenis_kelamin[jk] + ' : ' + array_sum(_populasi) + '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        var _content = _header + '<div class=""><div><table class="table table-bordered breakdown_pakan">' + _thead + _tbody + '</table></div></div>';

        var _options = {
            title: 'Perencanaan DOC-In',
            message: _content,
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
            $(this).find('table').scrollabletable({
                'scroll_horizontal': 0,
            });
        });

    },

    getRencanaKirimBdy: function(kodeFarm, tglDocIn) {
        if (this.rencanaKirimBdy[kodeFarm] == undefined) {
            this.rencanaKirimBdy[kodeFarm] = {};
        }
        if ((this.rencanaKirimBdy[kodeFarm][tglDocIn] == undefined) || empty(this.rencanaKirimBdy[kodeFarm][tglDocIn])) {
            $.ajax({
                url: 'forecast/forecast/rencanaKirim',
                data: { kodefarm: kodeFarm, tgldocin: tglDocIn },
                type: 'post',
                dataType: 'json',
                async: false,
                success: function(data) {
                    if (data.status) {
                        /* lakukan grouping per tanggal kebutuhan */
                        var _t, _tmp = {},
                            _tglKeb, _kodepj;
                        for (var i in data.content) {
                            _t = data.content[i];
                            _tglKeb = Config._tanggalLocal(_t['tgl_kebutuhan'], '-', ' ');
                            _kodepj = _t['kode_barang'];
                            if (_tmp[_tglKeb] == undefined) {
                                _tmp[_tglKeb] = {};
                            }
                            _tmp[_tglKeb][_kodepj] = _t;
                        }
                        Forecast.rencanaKirimBdy[kodeFarm][tglDocIn] = _tmp;
                    } else {
                        Forecast.rencanaKirimBdy[kodeFarm][tglDocIn] = {};
                    }
                }
            });
            return Forecast.rencanaKirimBdy[kodeFarm][tglDocIn];
        } else {
            return Forecast.rencanaKirimBdy[kodeFarm][tglDocIn];
        }
    },
    generateRencanaKirim: function(_rencanaKirim, idFarm, _tglDocIn, _populasi) {
        _tglDocIn = Config._convertTgl(_tglDocIn);
        var _kebutuhan_awal = new Date(_tglDocIn);
        var _nextDate = new Date(_tglDocIn);
        var _tglDocInDate = new Date(_tglDocIn);
        var _DocInDate = new Date(_tglDocIn);
        var standart_perumur = Forecast.get_standart_budidaya_bdy(idFarm, _tglDocIn);
        var _grup_farm = 'bdy',
            _timeline, _tglKirim, _tglKirimStr, _tglTampil, _umur, _kebutuhan_pakan, _kodepj, _namapj, _bentukpj;
        var _td_arr, _tr_arr = [],
            _content, _tglKirimSebelumnya;
        var _ganti_pakan = Forecast.getGantiPakan(standart_perumur['j']);
        var _totalPerTglKirim = {};
        /* kebutuhan awal dimulai dari DOC In + 1 */
        _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 1);
        _nextDate.setDate(_nextDate.getDate() + 1);
        _DocInDate.setDate(_DocInDate.getDate() + 1);
        $.when(standart_perumur).done(function() {
            var standart_pakan = standart_perumur['j'];
            for (var i in standart_pakan) {
                _umur = i;
                _kebutuhan_pakan = 0;
                _kodepj = standart_pakan[i]['kode_barang'];
                _namapj = standart_pakan[i]['nama_barang'];
                _bentukpj = standart_pakan[i]['bentuk'];
                for (var _h in _populasi) {
                    _kebutuhan_pakan += Forecast.rumus_perhitungan_harian(standart_pakan[_umur], _populasi[_h], 'sak');
                }
                _tglTampil = Config._tanggalLocal(Config._getDateStr(_nextDate), '-', ' ');

                _tglKirimStr = '';
                if (empty(_rencanaKirim)) {
                    if (Config._getDateStr(_kebutuhan_awal) == Config._getDateStr(_nextDate)) {
                        /* untuk umur 0 kebutuhan 7 hari, < 19 kebutuhan per 3 hari, selanjutnya 1 hari */
                        if (Config._getDateStr(_kebutuhan_awal) == Config._getDateStr(_DocInDate)) {
                            var _kebutuhan_pakan_timeline = new Date(Config._convertTgl(Config._getDateStr(_tglDocInDate)));
                        } else {
                            var _kebutuhan_pakan_timeline = new Date(Config._convertTgl(Config._getDateStr(_kebutuhan_awal)));
                        }

                        /* parameter timeline adalah tgl_keb_awal - 1 */
                        _kebutuhan_pakan_timeline.setDate(_kebutuhan_pakan_timeline.getDate() - 1);
                        _timeline = Permintaan.timeline_pp(_kebutuhan_pakan_timeline, _grup_farm, _tglDocIn);

                        if (Config._getDateStr(_kebutuhan_awal) == Config._getDateStr(_DocInDate)) {
                            _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 7);
                        } else if (_umur < 19) {
                            _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 3);
                        } else {
                            _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 1);
                        }
                        //	_tglKirim = _timeline.tglKirimDefault;
                        _tglKirim = _timeline.tglKirimDate;
                        _tglKirimStr = Config._tanggalLocal(Config._getDateStr(_tglKirim), '-', ' ');
                        if (_tglKirimStr == _tglKirimSebelumnya) {
                            _tglKirimStr = '';
                        }
                    }
                } else {
                    if (_rencanaKirim[_tglTampil] != undefined) {
                        _tglKirimStr = _rencanaKirim[_tglTampil];
                    }
                }

                if (!empty(_tglKirimStr)) {
                    _tglKirimSebelumnya = _tglKirimStr;
                }

                if (_ganti_pakan[_umur] !== undefined) {
                    var _gp = 0;
                    for (var _z in _ganti_pakan[_umur]) {
                        if (_gp == 0) {
                            _td_arr = [_tglKirimStr, _tglTampil, _ganti_pakan[_umur][_z]["kode_pakan"], _ganti_pakan[_umur][_z]["nama_barang"], _ganti_pakan[_umur][_z]["bentuk"], Forecast.ceil2(_kebutuhan_pakan * _ganti_pakan[_umur][_z]["komposisi"])];
                        } else {
                            _td_arr = ['', _tglTampil, _ganti_pakan[_umur][_z]["kode_pakan"], _ganti_pakan[_umur][_z]["nama_barang"], _ganti_pakan[_umur][_z]["bentuk"], Forecast.ceil2(_kebutuhan_pakan * _ganti_pakan[_umur][_z]["komposisi"])];
                        }
                        _gp++;
                        _tr_arr.push('<td data-tglKeb="' + _tglTampil + '" data-umur="' + _umur + '">' + _td_arr.join('</td><td>') + '</td>');
                    }
                } else {
                    _td_arr = [_tglKirimStr, _tglTampil, _kodepj, _namapj, _bentukpj, Forecast.ceil2(_kebutuhan_pakan)];
                    _tr_arr.push('<td data-tglKeb="' + _tglTampil + '" data-umur="' + _umur + '">' + _td_arr.join('</td><td>') + '</td>');
                }
                if (_totalPerTglKirim[_tglKirimSebelumnya] == undefined) {
                    _totalPerTglKirim[_tglKirimSebelumnya] = 0;
                }
                _totalPerTglKirim[_tglKirimSebelumnya] += parse_number(Forecast.ceil2(_kebutuhan_pakan));
                _nextDate.setDate(_nextDate.getDate() + 1);
            }

        });
        return { "data": _tr_arr, "resume_tglkirim": _totalPerTglKirim };
    },
    /* user bisa menentukan jumlah pakan */
    generateRencanaKirimInput: function(_rencanaKirim, idFarm, _tglDocIn, _populasi) {
        _tglDocIn = Config._convertTgl(_tglDocIn);
        var _kebutuhan_awal = new Date(_tglDocIn);
        var _nextDate = new Date(_tglDocIn);
        var _tglDocInDate = new Date(_tglDocIn);
        var _DocInDate = new Date(_tglDocIn);
        var standart_perumur = Forecast.get_standart_budidaya_bdy(idFarm, _tglDocIn);
        var _grup_farm = 'bdy',
            _timeline, _tglKirim, _tglKirimStr, _tglTampil, _umur, _kebutuhan_pakan, _kodepj, _namapj, _bentukpj;
        var _td_arr, _tr_arr = [],
            _content, _tglKirimSebelumnya, _tglKeb;
        var _ganti_pakan = Forecast.getGantiPakan(standart_perumur['j']);
        var _totalPerTglKirim = {},
            _pakanStandart = {},
            _tmpidpakan, _tmpnamapakan, _kebperpj = {},
            _forecastperpj = {};
        for (var i in _ganti_pakan) {
            _tmpidpakan = _ganti_pakan[i]['pakanLama']['kode_pakan'];
            if (_pakanStandart[_tmpidpakan] === undefined) {
                _tmpnamapakan = _ganti_pakan[i]['pakanLama']['nama_barang'];
                _pakanStandart[_tmpidpakan] = { "kode_pakan": _tmpidpakan, "nama_barang": _tmpnamapakan };
            }
            _tmpidpakan = _ganti_pakan[i]['pakanBaru']['kode_pakan'];
            if (_pakanStandart[_tmpidpakan] === undefined) {
                _tmpnamapakan = _ganti_pakan[i]['pakanBaru']['nama_barang'];
                _pakanStandart[_tmpidpakan] = { "kode_pakan": _tmpidpakan, "nama_barang": _tmpnamapakan };
            }
        }
        /* kebutuhan awal dimulai dari DOC In + 1 */
        _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 1);
        _nextDate.setDate(_nextDate.getDate() + 1);
        _DocInDate.setDate(_DocInDate.getDate() + 1);
        $.when(standart_perumur).done(function() {
            var standart_pakan = standart_perumur['j'];
            for (var i in standart_pakan) {
                _umur = i;
                _kebutuhan_pakan = 0;
                _kodepj = standart_pakan[i]['kode_barang'];
                _namapj = standart_pakan[i]['nama_barang'];
                _bentukpj = standart_pakan[i]['bentuk'];
                _kebperpj = {};
                _forecastperpj = {};
                for (var _h in _populasi) {
                    _kebutuhan_pakan += Forecast.rumus_perhitungan_harian(standart_pakan[_umur], _populasi[_h], 'sak');
                }
                _tglTampil = Config._tanggalLocal(Config._getDateStr(_nextDate), '-', ' ');
                //	_tglKeb = Config._tanggalDb(_tglTampil,' ','-');
                _tglKirimStr = '';
                if (empty(_rencanaKirim)) {
                    if (Config._getDateStr(_kebutuhan_awal) == Config._getDateStr(_nextDate)) {
                        /* untuk umur 0 kebutuhan 7 hari, < 19 kebutuhan per 3 hari, selanjutnya 1 hari */
                        if (Config._getDateStr(_kebutuhan_awal) == Config._getDateStr(_DocInDate)) {
                            var _kebutuhan_pakan_timeline = new Date(Config._convertTgl(Config._getDateStr(_tglDocInDate)));
                        } else {
                            var _kebutuhan_pakan_timeline = new Date(Config._convertTgl(Config._getDateStr(_kebutuhan_awal)));
                        }

                        /* parameter timeline adalah tgl_keb_awal - 1 */
                        _kebutuhan_pakan_timeline.setDate(_kebutuhan_pakan_timeline.getDate() - 1);
                        _timeline = Permintaan.timeline_pp(_kebutuhan_pakan_timeline, _grup_farm, _tglDocIn);

                        if (Config._getDateStr(_kebutuhan_awal) == Config._getDateStr(_DocInDate)) {
                            _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 7);
                        } else if (_umur < 19) {
                            _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 3);
                        } else {
                            _kebutuhan_awal.setDate(_kebutuhan_awal.getDate() + 1);
                        }
                        //	_tglKirim = _timeline.tglKirimDefault;
                        _tglKirim = _timeline.tglKirimDate;
                        _tglKirimStr = Config._tanggalLocal(Config._getDateStr(_tglKirim), '-', ' ');
                        if (_tglKirimStr == _tglKirimSebelumnya) {
                            _tglKirimStr = '';
                        }
                    }

                    if (_ganti_pakan[_umur] !== undefined) {
                        for (var _z in _ganti_pakan[_umur]) {
                            _kebperpj[_ganti_pakan[_umur][_z]["kode_pakan"]] = _kebutuhan_pakan * _ganti_pakan[_umur][_z]["komposisi"];
                        }
                    } else {
                        for (var _pj in _pakanStandart) {
                            if (_pj == _kodepj) {
                                _kebperpj[_pj] = _kebutuhan_pakan;
                            } else {
                                _kebperpj[_pj] = 0;
                            }
                        }
                    }
                    _forecastperpj = _kebperpj;
                } else {
                    // console.log(_rencanaKirim[_tglTampil]);
                    if (_rencanaKirim[_tglTampil] != undefined) {
                        for (var _pj in _pakanStandart) {
                            _kebperpj[_pj] = 0;
                            _forecastperpj[_pj] = 0;
                            if (_rencanaKirim[_tglTampil][_pj] != undefined) {
                                _tglKirimStr = Config._tanggalLocal(_rencanaKirim[_tglTampil][_pj]['tgl_kirim'], '-', ' ');
                                _kebperpj[_pj] = _rencanaKirim[_tglTampil][_pj]['jml_standar'];
                                _forecastperpj[_pj] = _rencanaKirim[_tglTampil][_pj]['jml_forecast'];
                            }
                        }
                    }
                    if (_tglKirimStr == _tglKirimSebelumnya) {
                        _tglKirimStr = '';
                    }
                }

                if (!empty(_tglKirimStr)) {
                    _tglKirimSebelumnya = _tglKirimStr;
                }
                /*	_td_arr = [ _tglKirimStr,_tglTampil,_kodepj,_namapj,_bentukpj,Forecast.ceil2(_kebutuhan_pakan)];
                	_tr_arr.push('<td>'+_td_arr.join('</td><td>')+'</td>');
                */


                _td_arr = [_tglKirimStr, _tglTampil];
                for (var _pj in _pakanStandart) {
                    _td_arr.push('<input class="number jml_forecast" data-tglkebutuhan="' + Config._convertTgl(Config._getDateStr(_nextDate)) + '" data-kodepj="' + _pj + '" data-standart="' + _kebperpj[_pj] + '" type="text" value="' + Forecast.ceil2(_forecastperpj[_pj]) + '"/><span class="tooltip_bdy"> Nilai standart : ' + Forecast.ceil2(_kebperpj[_pj]) + ' sak</span>');
                }
                _tr_arr.push('<td  data-tglKeb="' + Config._convertTgl(Config._getDateStr(_nextDate)) + '" data-umur="' + _umur + '">' + _td_arr.join('</td><td class="has-tooltip_bdy">') + '</td>');
                if (_totalPerTglKirim[_tglKirimSebelumnya] == undefined) {
                    _totalPerTglKirim[_tglKirimSebelumnya] = 0;
                }
                /* jml forecast per tgl kebutuhan */
                var _totalforecastperhari = 0;
                for (var fj in _forecastperpj) {
                    _totalforecastperhari += parseFloat(_forecastperpj[fj]);
                }
                _totalPerTglKirim[_tglKirimSebelumnya] += _totalforecastperhari;
                _nextDate.setDate(_nextDate.getDate() + 1);
            }

        });
        return { "data": _tr_arr, "resume_tglkirim": _totalPerTglKirim };
    },

    breakdownPakanBdy: function(elm, jk) {
        var _info = $(elm).data('info');
        var idFarm = $(elm).data('kode_farm');

        var strain = Forecast.get_item_data_farm('kode_strain', idFarm);
        var _namafarm = Forecast.get_item_data_farm('nama_farm', idFarm);
        var _elmdipilih = $('.css-treeview li.sedang_dipilih').eq(0);
        var _kapasitas = [];
        var _populasi = [];
        var _kandang = [];
        var _grup_farm = $(elm).data('grup_farm');
        var _tglDocIn = Forecast.tglDocInterpilih;
        var _rencanaKirim = Forecast.getRencanaKirimBdy(idFarm, _tglDocIn);
        var _totalPerTglKirim;
        if (_info == 'perkandang') {
            var _tmp = _elmdipilih.find('span.hide[data-value=detail_kandang]').text().split('/');
            /* map berdasarkan _indexHeader biar mudah */
            var _data = Config.mappingHeader(_tmp);
            _populasi.push(parse_number(_data[Config._jenis_kelamin[jk]], '.', ','));
            _kapasitas.push(parse_number(_data['kapasitas'], '.', ','));
            _kandang.push(_data['kandang']);
        } else {
            var _tmp = _elmdipilih.find('span.hide[data-value=detail_kandang]').eq(0).text().split('/');
            /* map berdasarkan _indexHeader biar mudah */
            var _data = Config.mappingHeader(_tmp);
            var tipe_kandang = _data['tipe'].substr(0, 1);
            var _tm;
            /* looping semua kandangnya */
            _elmdipilih.find('ul>li').each(function() {
                _tm = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
                _tm = Config.mappingHeader(_tm);
                //	console.log(parse_number(_tm[Config._jenis_kelamin[jk]],'.',','));
                _populasi.push(parse_number(_tm[Config._jenis_kelamin[jk]], '.', ','));
                _kapasitas.push(parse_number(_tm['kapasitas'], '.', ','));
                _kandang.push(_tm['kandang']);
            });
        }
        var _tbody = AktivasiKandang.generateRencanaKirimInput(_rencanaKirim, idFarm, _tglDocIn, _populasi);
        //		_totalPerTglKirim = _tr_arr['resume_tglkirim'];
        //		var _tbody = '<tbody><tr>'+_tr_arr['data'].join('</tr><tr>')+'</tr></tbody>';
        //				var _thead = '<thead><tr><th data-id="tglkirim">Tanggal Kirim</th><th data-id="tglkebutuhan">Tanggal Kebutuhan</th><th data-id="kodepakan">Kode Pakan</th><th data-id="namapakan">Nama Pakan</th><th data-id="bentuk">Bentuk</th><th data-id="kuantitas">Kuantitas <br /> (Sak)</th></tr></thead>';
        var _header = '<div>' +
            '<div class="row">' +
            '<div class="col-md-12">' +
            /*						'<div class="pull-left"><span class="btn btn-default" onclick="Forecast.cetakRencana(this)">Print</span></div>'+ */
            '<div class="text-center">' +
            '<div class="header-info">' +
            '<div>FARM ' + _namafarm + ' ' + strain + '</div>' +
            '<div>Kandang ' + _kandang.join(',') + '</div>' +
            '<div class="row">' +
            '<div class="col-md-3 col-md-offset-1 ">Tipe Kandang : ' + Config._tipe_kandang[tipe_kandang] + '</div>' +
            '<div class="col-md-4">Kapasitas : ' + number_format(array_sum(_kapasitas), 0, ',', '.') + '</div>' +
            '<div class="col-md-3">Jumlah  : ' + number_format(array_sum(_populasi), 0, ',', '.') + '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        var _content = _header + '<div class=""><div><table class="table table-bordered breakdown_pakan">' + _tbody[0].outerHTML; + '</table></div></div>';

        var _options = {
            title: 'Perencanaan DOC-In',
            message: _content,
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
            var _opsi = {
                beforeShowDay: function(date) { return [!Config.is_hari_libur(Config._getDateStr(date), Permintaan.get_hari_libur())]; },
                dateFormat: 'dd M yy',
            };
            var _tglLama, _ini, _adadp, _maxDate;
            $(this).find('input.jml_forecast').addClass('no_border').prop('readonly', 1);
            /*
            $(this).find('table tbody>tr').each(function(){
            	var _tdKirim = $(this).find('td:first');
            	var _tr = $(this);
            	var _text = _tdKirim.text();
            	if(!empty(_text)){
            		_tdKirim.addClass('has-tooltip_bdy').append(
            				'<span class="tooltip_bdy"> Total pakan yang dikirim '+Math.ceil(_totalPerTglKirim[_text])+' sak</span>'
            		);
            	}

            });
            */
        });



    },
    cetakRencana: function(elm) {
        var data = [],
            fontSize = 10,
            height = 0,
            doc;
        var _bb = $(elm).closest('.bootbox-body');
        doc = new jsPDF('p', 'pt', 'a4', true);
        doc.setFont("times", "normal");
        doc.setFontSize(fontSize);
        //	doc.text(20, 20, "hi table");
        doc.margins = 1;
        data = [];
        var columns = [],
            _clm, _clm_id = [];
        var rows = [],
            _tmp_r = {};
        var _y = 30;
        var _baris = 12;
        var _rows_header = [];
        var _x = 20;

        /* buat informasi header */
        var _header = _bb.find('div.header-info>div');
        _header.each(function(i) {
            if (i < 2) {
                doc.myText($(this).text(), { align: "center" }, _x, _y);
                _y += _baris;
            } else {
                var _inc_x = 140;
                var _x_tmp = _x + 100;
                $(this).find('div').each(function() {
                    doc.text($(this).text(), _x_tmp, _y);
                    _x_tmp += _inc_x;
                });
            }
        })

        var _table = _bb.find('table:eq(0)');
        _table.find('thead tr th').each(function() {
            _clm = { title: $(this).text(), dataKey: $(this).data('id') };
            _clm_id.push($(this).data('id'));
            columns.push(_clm);
        });
        _table.find('tbody tr').each(function() {
            _tmp_r = {};
            $(this).find('td').each(function(i) {
                _tmp_r[_clm_id[i]] = $(this).text();
            });
            rows.push(_tmp_r);
        });

        _y += _baris;
        doc.autoTable(columns, rows, {
            theme: 'grid',
            startY: _y,
            styles: { fontSize: 9 },

        });
        doc.output('dataurlnewwindow');
    },
    removeDp: function(elm) {
        var _td = $(elm).closest('td');
        _td.html(_td.find('input').val());
    },
    removeTgl: function(elm) {
        var _td = $(elm).closest('td');
        _td.html('');
    },

    simpanRencanaTglKirim: function(elm) {
        var ini = $(elm).closest('.bootbox-body');
        var _tglKirim, _tmp, _data = {},
            _tmpKirim, _tmpKebAwal, _tmpKebAwalStr, _tmpKirimStr;
        var _tglDocIn = Forecast.tglDocInterpilih;
        var _tmpKirimTersimpan = {};
        ini.find('table>tbody>tr').each(function() {
            _tglKirim = $(this).find('td').eq(0);
            _tmpKirimStr = $.trim(_tglKirim.text()) || _tglKirim.find('input.hasDatepicker').val();
            if (!empty(_tmpKirimStr)) {
                _tmpKebAwalStr = $.trim(_tglKirim.next().text());
                _tmpKebAwal = Config._tanggalDb(_tmpKebAwalStr, ' ', '-');
                _tmpKirim = Config._tanggalDb(_tmpKirimStr, ' ', '-');
                _tmp = { tgl_kirim: _tmpKirim, tgl_keb_awal: _tmpKebAwal };

                if (_data[_tmpKirim] == undefined) {
                    _data[_tmpKirim] = _tmp;
                    _tmpKirimTersimpan[_tmpKebAwalStr] = _tmpKirimStr;
                }


            }
        });
        if (!empty(_data)) {
            bootbox.confirm({
                title: 'Konfirmasi Rencana Pengiriman Pakan',
                message: 'Perubahan ini akan berlaku untuk semua kandang dengan tanggal Doc In ' + Config._tanggalLocal(_tglDocIn, '-', ' ') + ' ?',
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
                            url: 'forecast/forecast/simpanRencanaKirim',
                            data: { data: _data, kode_farm: Forecast.data_farm['kode_farm'], tgl_docin: _tglDocIn },
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                if (data.status) {
                                    toastr.success(data.message);
                                    Forecast.rencanaKirimBdy[_tglDocIn] = _tmpKirimTersimpan;
                                    bootbox.hideAll();
                                } else {
                                    toastr.error(data.message);
                                }
                            },
                        });
                    }
                }
            });

        }
    },

    periksaStandardBaru: function(minTglDocIn) {
        var _belumApproveSpan = '<span class="abang">&nbsp;	&#33;</span>';
        $('#div_forecast label.bulan').closest('li').each(function() {
            var _bulan = $(this);
            var _tahun = _bulan.closest('ul').closest('li');
            var _tglElm = _bulan.find('ul>li');
            var _tgl;
            _tglElm.each(function() {
                _tgl = $(this).find('label').text().substr(0, 2);
                var _tglDocIn = _tahun.find('label:first').text() + '-' + _bulan.find('label:first').text() + '-' + _tgl;
                if (_tglDocIn >= minTglDocIn) {
                    $(this).find('li').append(_belumApproveSpan);
                }

            });

        });
    },

    periksaApproval: function() {

        var _belumApproveSpan = '<span class="belum_approval abang">&nbsp;	&#33;</span>';

        var _arrBelumApprove = ['Baru', 'Draft', '*'];
        $('#div_forecast label.bulan').closest('li').each(function() {
            var _belumApprove = [];
            //console.log($(this).find('span._status_approval'));
            $(this).find('span.belum_approval').remove();
            $(this).find('span._status_approval').each(function(i) {
                if (in_array($(this).text(), _arrBelumApprove)) {
                    _belumApprove.push($(this));

                }
            });

            //	_belumApprove = $(this).find('span._status_approval:contains("Baru"),span._status_approval:contains("Draft"),span._status_approval:contains("*")');
            if (_belumApprove.length) {
                //	if(!_belumApprove.next('span.abang').length){
                //		$(_belumApproveSpan).insertAfter($(this).find('label.bulan'));
                //	}

                $.each(_belumApprove, function() {
                    var _tanggal = $(this).closest('ul').siblings('label');

                    if (!_tanggal.next('span.abang').length) {
                        $(_belumApproveSpan).insertAfter(_tanggal);
                    }
                    if (!$(this).next('span.abang').length) {
                        $(_belumApproveSpan).insertAfter($(this));
                    }
                });
            }
        });
    },
    cek_uncek: function(elm) {
        if ($(elm).is(':checked')) {

            $(elm).closest('.modal-header').next('.modal-body').find(':checkbox').prop('checked', 1);
        } else {

            $(elm).closest('.modal-header').next('.modal-body').find(':checkbox').prop('checked', 0);
        }
    },

    get_rencana_produksi: function(_kodepj, _tglkirim) {
        var tglkirim = Config._tanggalDb(_tglkirim, ' ', '-');
        var awaldate = new Date(tglkirim);
        awaldate.setDate(awaldate.getDate() - 7);
        var awal = Config._convertTgl(Config._getDateStr(awaldate));
        var _result = [];
        $.ajax({
            url: 'forecast/forecast/get_rencana_produksi',
            type: 'post',
            data: { kodepj: _kodepj, akhir: Config._tanggalDb(_tglkirim, ' ', '-'), awal: awal },
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data.status) {
                    var _x = [];
                    var _header = [],
                        _body = [];
                    _header.push('<thead>');
                    _header.push('<tr>');
                    _header.push('<th>Kode Rencana Produksi</th>');
                    _header.push('<th>Tanggal Rencana Produksi</th>');
                    _header.push('<th>Jumlah yang Dapat Dialokasikan</th>');
                    _header.push('<th>Jumlah Alokasi</th>');
                    _header.push('<th></th>');
                    _header.push('</tr>');
                    _header.push('</thead>');
                    /* buat dropdown untuk list rencana produksi */
                    var _dd = [];
                    _body.push('<tbody>');
                    _body.push('<tr>');
                    _dd.push('<select name="rencana_produksi" onchange="Forecast.pilih_rencanaproduksi(this)">');
                    var _default = {},
                        j = 0;

                    for (var i in data.content.rps) {
                        var _rp = data.content.rps[i];
                        _dd.push('<option value="' + _rp.rp + '" data-tgl_produksi="' + Config._tanggalLocal(_rp.tgl_produksi, '-', ' ') + '" data-jml_produksi="' + _rp.jml_produksi + '">' + _rp.rp + '</option>');
                        if (j == 0) {
                            _default['tgl_produksi'] = Config._tanggalLocal(_rp.tgl_produksi, '-', ' ');
                            _default['jml_produksi'] = _rp.jml_produksi;
                        }
                        j++;
                    }
                    _dd.push('</select>');

                    _body.push('<td>' + _dd.join('') + '</td>');
                    _body.push('<td><input class="text-right" type="text" name="tgl_produksi" readonly value="' + _default.tgl_produksi + '" /></td>');
                    _body.push('<td><input class="text-right" type="text" name="jml_produksi" readonly value="' + _default.jml_produksi + '" /></td>');
                    _body.push('<td><input class="text-right" type="text" name="jml_alokasi" value="0" onchange="Forecast.check_totalalokasi(this)" /></td>');
                    _body.push('<td><span class="hide glyphicon glyphicon-plus" onclick="Forecast.tambah_hapus_alokasi(this)"></span></td>');
                    _body.push('</tr>');
                    _body.push('</tbody>');

                    _result.push(_header.join(''));
                    _result.push(_body.join(''));

                } else {
                    _result.push('<tbody><tr><td colspan=5>Data tidak ditemukan</td></tr></tbody>');
                }

            },
            cache: false,
        });
        return '<table class="table">' + _result.join('') + '</table>';
    },

    konfirmasi_rencana_produksi: function(elm) {
        var _tr = $(elm).closest('tr');
        var _no_lpb = _tr.find('td.no_lpb').text();
        var _nama_farm = _tr.find('td.nama_farm').text();
        var _nama_barang = _tr.find('td.nama_barang').text();
        var _kodepj = _tr.find('td.nama_barang').data('kode_barang');
        var _tglkirim = _tr.find('td.tgl_kirim').text();
        var _jml_order = _tr.find('td.jml_order').text();
        var _caption = '<div class="text-center"><h3>' + _no_lpb + ' - ' + _nama_farm + ' - ' + _nama_barang + '</h3></div>';
        _caption += '<div class="text-center"><h4>Jumlah Permintaan : <span class="max_konfirmasi">' + _jml_order + '</span></h4></div>';
        var content = this.get_rencana_produksi(_kodepj, _tglkirim);
        $.when(content).done(function() {
            var _options = {
                title: 'Input Rencana Produksi',
                message: _caption + content,
                className: 'mediumWidth',
                buttons: {
                    set: {
                        label: 'Set',
                        className: '',
                        callback: function(e) {
                            var _body = $(e.target).closest('.modal-content');
                            var _max_konfirmasi = _body.find('span.max_konfirmasi').text();

                            var _total_konfirmasi = 0;
                            var _jml_rp = 0;
                            _body.find('input[name=jml_alokasi]').each(function() {
                                _total_konfirmasi += parseInt($(this).val());
                                _jml_rp++;
                            });

                            if (_total_konfirmasi < _max_konfirmasi) {
                                toastr.error('Jumlah total alokasi kurang dari jumlah permintaan');
                                return false;
                            } else if (_total_konfirmasi > _max_konfirmasi) {
                                toastr.error('Jumlah total alokasi melebihi dari jumlah permintaan');
                                return false;
                            } else {
                                bootbox.confirm({
                                    title: 'Konfirmasi Rencana Produksi',
                                    message: 'Apakah anda yakin akan melakukan konfirmasi pada ' + _jml_rp + ' Rencana Produksi',
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
                                            alert('simpan');
                                        } else {
                                            alert('tidak');
                                        }
                                    }
                                });
                            }
                        }
                    }
                },
            };

            bootbox.dialog(_options).bind('shown.bs.modal', function() {
                $(this).find('input[name=jml_alokasi]')
                    .priceFormat({
                        prefix: '',
                        centsLimit: 0,
                        thousandsSeparator: '.'
                    }).focus();

            });
        });

    },
    pilih_rencanaproduksi: function(elm) {
        var _opt = $(elm).find('option:selected');
        var _tr = $(elm).closest('tr');
        _tr.find('input[name=tgl_produksi]').val(_opt.data('tgl_produksi'));
        _tr.find('input[name=jml_produksi]').val(_opt.data('jml_produksi'));
        _tr.find('input[name=jml_alokasi]').val(0).focus();
        _tr.find('input[name=jml_alokasi]').prop('readonly', 0);
        var _span_plus = _tr.find('td:last span');
        if (!_span_plus.hasClass('glyphicon-plus')) {
            _span_plus.addClass('glyphicon-plus').removeClass('glyphicon-minus');
        }
    },
    check_totalalokasi: function(elm) {
        /* pastikan yang diinput tidak > dari jml_produksi */
        var _v = $(elm).val();
        var _tr = $(elm).closest('tr');
        var _jml_produksi = _tr.find('input[name=jml_produksi]').val();
        if (_v > 0) {
            if (_v > _jml_produksi) {
                $(elm).val(_jml_produksi);
            }
            /* cari total yang sudah dialokasikan, pastikan tidak > dari jml_order */
            var _body = $(elm).closest('.modal-content');
            var _max_konfirmasi = _body.find('span.max_konfirmasi').text();

            var _total_konfirmasi = 0;
            _body.find('input[name=jml_alokasi]').each(function() {
                _total_konfirmasi += parseInt($(this).val());

            });

            var _sisa_alokasi = _max_konfirmasi - _total_konfirmasi;
            if (_sisa_alokasi <= 0) {
                $(elm).val(parseInt($(elm).val()) + _sisa_alokasi);
            }

            if (_total_konfirmasi > _max_konfirmasi) {
                toastr.warning('Jumlah total alokasi melebihi dari jumlah permintaan');
            } else {
                _tr.find('td:last span').removeClass('hide');
            }
        } else {
            toastr.warning('Jumlah alokasi tidak boleh kurang dari 1');
        }
    },
    tambah_hapus_alokasi: function(elm) {
        var _tr = $(elm).closest('tr');
        var _table = _tr.closest('table');
        if ($(elm).hasClass('glyphicon-plus')) {
            var _body = _table.closest('.modal-content');
            var _max_konfirmasi = _body.find('span.max_konfirmasi').text();
            var _jml_rp_tersisa = _tr.find('select option:visible').length;
            var _elm_alokasi = _tr.find('input[name="jml_alokasi"]');
            var _jml_alokasi = _elm_alokasi.val();
            var _jml_produksi = _tr.find('input[name=jml_produksi]').val();
            var _new_tr = _tr.clone();
            var _rp = _tr.find('select').val();


            /* pastikan alokasi tidak > jml_produksi */
            if (_jml_alokasi > _jml_produksi) {
                _elm_alokasi.val(_jml_produksi);
            }
            var _total_konfirmasi = 0;
            _table.find('input[name=jml_alokasi]').each(function() {
                _total_konfirmasi += parseInt($(this).val());
            });
            var _sisa_alokasi = _max_konfirmasi - _total_konfirmasi;
            /*
					if(_sisa_alokasi <= 0){
						_elm_alokasi.val(parseInt(_elm_alokasi.val()) +_sisa_alokasi);
					}

					else{
						if(_elm_alokasi.val() > _sisa_alokasi){
							_elm_alokasi.val(_sisa_alokasi);
						}
					}
				*/
            _tr.find('input[name="jml_alokasi"]').attr('readonly', 1);

            if (_jml_rp_tersisa > 1 && (_sisa_alokasi > 0)) {
                _new_tr.insertAfter(_tr);
                _new_tr.find('select option[value=' + _rp + ']').hide();
                _new_tr.find('select option:visible:first').prop('selected', 1);
                _new_tr.find('td:last span').addClass('hide');
                this.pilih_rencanaproduksi(_new_tr.find('select'));
                _tr.find('select').replaceWith('<input type="text" name="rencana_produksi" value="' + _rp + '" readonly />');
            }
            $(elm).removeClass('glyphicon-plus').addClass('glyphicon-minus');


        } else {
            var _tot_tr = _table.find('tbody tr').length;
            var _index_tr = _tr.index() + 1; /* index dimulai dari 0 */
            if (_index_tr < _tot_tr) {
                var _rp = _tr.find('input[name=rencana_produksi]').val();
                _table.find('select option[value=' + _rp + ']').show();
                _tr.remove();

                var _span_plus = _table.find('tr:last>td:last span');
                if (!_span_plus.hasClass('glyphicon-plus')) {
                    _span_plus.addClass('glyphicon-plus').removeClass('glyphicon-minus');
                }
            }
        }
    },
    konfirmasi_kavling: function(elm) {
        var _tr = $(elm).closest('tr');
        var _rp = _tr.find('td.koderp').text();
        var _no_lpb = _tr.find('td.no_lpb').text();
        var _nama_farm = _tr.find('td.nama_farm').text();
        var _nama_barang = _tr.find('td.nama_barang').text();
        var _kodepj = _tr.find('td.nama_barang').data('kode_barang');
        var _jml_order = _tr.find('td.jml_order').text();

        var _list_kavling = this.get_kavling_pakanjadi(_rp, _kodepj);
        $.when(_list_kavling).done(function() {
            var _options = {
                title: 'Input Rencana Produksi',
                message: 'nyoba',
                className: 'mediumWidth',
                buttons: {
                    set: {
                        label: 'Set',
                        className: '',
                        callback: function(e) {
                            var _body = $(e.target).closest('.modal-content');
                            var _max_konfirmasi = _body.find('span.max_konfirmasi').text();

                            var _total_konfirmasi = 0;
                            var _jml_rp = 0;
                            _body.find('input[name=jml_alokasi]').each(function() {
                                _total_konfirmasi += parseInt($(this).val());
                                _jml_rp++;
                            });

                            if (_total_konfirmasi < _max_konfirmasi) {
                                toastr.error('Jumlah total alokasi kurang dari jumlah permintaan');
                                return false;
                            } else if (_total_konfirmasi > _max_konfirmasi) {
                                toastr.error('Jumlah total alokasi melebihi dari jumlah permintaan');
                                return false;
                            } else {
                                bootbox.confirm({
                                    title: 'Konfirmasi Rencana Produksi',
                                    message: 'Apakah anda yakin akan melakukan konfirmasi pada ' + _jml_rp + ' Rencana Produksi',
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
                                            alert('simpan');
                                        } else {
                                            alert('tidak');
                                        }
                                    }
                                });
                            }
                        }
                    }
                },
            };

            bootbox.dialog(_options).bind('shown.bs.modal', function() {
                $(this).find('input[name=jml_alokasi]')
                    .priceFormat({
                        prefix: '',
                        centsLimit: 0,
                        thousandsSeparator: '.'
                    }).focus();

            });
        });
    },

    get_kavling_pakanjadi: function(_rp, _kodepj) {
        var _result = [];
        $.ajax({
            url: 'forecast/forecast/get_serah_terimapj',
            type: 'post',
            data: { kodepj: _kodepj, rp: _rp },
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data.status) {
                    _result = data.content.pjs;
                }
            },
            cache: false,
        });
        return _result;
    },

    getPopulasiKandang: function(t) {
        var regExp = /\(([^)]+)\)/;
        var matches = regExp.exec(t);
        return matches[1];
    },

    getGantiPakan: function(standart_perumur) {
        var _pakanCurrent, _kodeBarang, _namaBarang, _index = 0,
            _result = [];
        for (var i in standart_perumur) {
            _kodeBarang = standart_perumur[i]['kode_barang'];
            if (_pakanCurrent != _kodeBarang) {
                _pakanCurrent = _kodeBarang;
                _namaBarang = standart_perumur[i]['nama_barang'];
                _result.push({ "umur": i, "kode_pakan": _pakanCurrent, "nama_barang": _namaBarang, "bentuk": standart_perumur[i]['bentuk'] });
            }
        }
        /* cari kapan ganti pakannya */
        var _pakanLama, _pakanBaru, _komposisiGantiPakan = {},
            _umur;
        var _maxGantiPakan = _result.length - 1;

        for (var y in _result) {
            if (y == 0) {
                if (_pakanLama == undefined) {
                    _pakanLama = { "kode_pakan": _result[y]["kode_pakan"], "nama_barang": _result[y]["nama_barang"], "bentuk": _result[y]["bentuk"] };
                }
            } else if (y <= _maxGantiPakan) {
                _pakanBaru = { "kode_pakan": _result[y]["kode_pakan"], "nama_barang": _result[y]["nama_barang"], "bentuk": _result[y]["bentuk"] };
                _umur = _result[y]["umur"];
                _komposisiGantiPakan[_umur - 1] = {};
                _komposisiGantiPakan[_umur - 1]["pakanLama"] = { "kode_pakan": _pakanLama["kode_pakan"], "komposisi": .75, "nama_barang": _pakanLama["nama_barang"], "bentuk": _pakanLama["bentuk"] };
                _komposisiGantiPakan[_umur - 1]["pakanBaru"] = { "kode_pakan": _pakanBaru["kode_pakan"], "komposisi": .25, "nama_barang": _pakanBaru["nama_barang"], "bentuk": _pakanBaru["bentuk"] };
                _komposisiGantiPakan[_umur] = {};
                _komposisiGantiPakan[_umur]["pakanLama"] = { "kode_pakan": _pakanLama["kode_pakan"], "komposisi": .5, "nama_barang": _pakanLama["nama_barang"], "bentuk": _pakanLama["bentuk"] };
                _komposisiGantiPakan[_umur]["pakanBaru"] = { "kode_pakan": _pakanBaru["kode_pakan"], "komposisi": .5, "nama_barang": _pakanBaru["nama_barang"], "bentuk": _pakanBaru["bentuk"] };
                _komposisiGantiPakan[parseInt(_umur) + 1] = {};
                _komposisiGantiPakan[parseInt(_umur) + 1]["pakanLama"] = { "kode_pakan": _pakanLama["kode_pakan"], "komposisi": .25, "nama_barang": _pakanLama["nama_barang"], "bentuk": _pakanLama["bentuk"] };
                _komposisiGantiPakan[parseInt(_umur) + 1]["pakanBaru"] = { "kode_pakan": _pakanBaru["kode_pakan"], "komposisi": .75, "nama_barang": _pakanBaru["nama_barang"], "bentuk": _pakanBaru["bentuk"] };
                _pakanLama = { "kode_pakan": _result[y]["kode_pakan"], "nama_barang": _result[y]["nama_barang"] };
            }
        }
        return _komposisiGantiPakan;
    },
    /** simpan ke server */
    set_flok_kandang: function(_form, context, _noreg) {
        var _tgl_doc_in = _form.find('select[name=flok_bdy]').val();
        var _tgl_panen = _form.find('select[name=flok_bdy]').find('option:selected').data('tgl_panen');
        var _flok_bdy = _form.find('select[name=flok_bdy]').find('option:selected').text().substr(-1, 1);
        var _url = 'forecast/forecast/updateFlokNoreg'
        $.post(_url, { no_reg: _noreg, flok_bdy: _flok_bdy, tgl_doc_in: _tgl_doc_in, tgl_panen: _tgl_panen }, function(data) {
            if (data.status) {
                bootbox.hideAll();
                bootbox.alert(data.message, function() {
                    /** pindahkan kandang ke flok terbaru */
                    var _elmKandang = $(context).closest('li');

                    var _tglIndonesiaArr = Config._tanggalLocal(_tgl_doc_in).split('-');
                    var _tahun = _tglIndonesiaArr[2];
                    var _bulan = _tglIndonesiaArr[1];
                    var _tgl = _tglIndonesiaArr[0];
                    /** cari posisi yang tepat untuk element ini */
                    var _farmElm = _elmKandang.closest('li.nama_farm');
                    var _targetElm = _farmElm.find('ul>li>label:contains(' + _tahun + ')').next('ul').find('li>label:contains(' + _bulan + ')').next('ul').find('li>label:contains(' + _tgl + ')').next('ul');
                    _elmKandang.appendTo(_targetElm);
                });
            }
        }, 'json');
    }
};