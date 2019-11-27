'use strict';
/* memerlukan file forecast/config.js */
var AktivasiKandang = {
    statusWarna: { 1: '#9a9090', 0: '#9a9090', P1: 'blue', P2: 'orange', RJ: 'red', RL: '#000000' },
    dataRDIT: {},
    tempRencanaKirim: {},
    bisaKonfirmasi: null,
    maxKonfirmasi: 6, //6
    minKonfirmasi: 9, //9
    maxRejectApprove: 6,
    kandangReject: {},
    tglKirimBisaDiubah : 0,
    //statusBolehGantiTglKirim: [1, 'RJ','RL','P1'],
    statusBolehGantiTglKirim: [1, 'RJ'],
    minKirim: 3, // 3
    maxKirim: 2, //2
    maxKirim25: 1, //1
    /* standarnya adalah pp adalah 3 */
    maxKebutuhanPakan: 3,
    /* standarnya adalah pp hanya untuk pp pertama kali */
    maxKebutuhanPakanDoc: 7,
    maxKebutuhanHarian: 1,
    umurPakanHarian: 19,
    ppAkanPanen: 22,

    rejectedElm: null,
    setKandangReject: function(idFarm, tglDocIn, noreg) {
        if (this.kandangReject[idFarm] == undefined) {
            this.kandangReject[idFarm] = {};
        }
        if (this.kandangReject[idFarm][tglDocIn] == undefined) {
            this.kandangReject[idFarm][tglDocIn] = [];
        }
        this.kandangReject[idFarm][tglDocIn].push(noreg)
    },
    getKandangReject: function(idFarm, tglDocIn) {
        var _result = [];
        if (this.kandangReject[idFarm] != undefined) {
            if (this.kandangReject[idFarm][tglDocIn] != undefined) {
                _result = this.kandangReject[idFarm][tglDocIn];
            }
        }
        return _result;
    },
    setBisaKonfirmasi: function(nilai) {
        this.bisaKonfirmasi = nilai;
    },
    getBisaKonfirmasi: function(nilai) {
        return this.bisaKonfirmasi;
    },
    getTempRencanaKirim: function(tglDocIn) {
        var result;
        if (this.tempRencanaKirim[tglDocIn] == undefined) {
            result = {};
        } else result = this.tempRencanaKirim[tglDocIn];
        return result;
    },
    setTempRencanaKirim: function(kode_farm, nama_farm, tglDocIn, data) {
        this.tempRencanaKirim[tglDocIn] = { 'kode_farm': kode_farm, 'nama_farm': nama_farm, 'data': data }
    },
    getDataRDIT: function(noreg) {
        if (AktivasiKandang.dataRDIT[noreg] === undefined) {
            $.ajax({
                data: { noreg: noreg },
                dataType: 'json',
                type: 'post',
                url: 'forecast/forecast/revisiDOCInKadiv',
                success: function(data) {
                    if (data.status) {
                        AktivasiKandang.dataRDIT[noreg] = data.content;
                    } else {
                        AktivasiKandang.dataRDIT[noreg] = null;
                    }
                },
                async: false
            });
        }
        return AktivasiKandang.dataRDIT[noreg];
    },
    parseElmKandang: function(elm) {
        var _tglElm = $(elm).closest('ul').siblings('label');
        var _tgl = _tglElm.text().substr(0, 2);
        var _bulanElm = _tglElm.closest('ul').siblings('label');
        var _bulan = _bulanElm.text();
        var _tahunElm = _bulanElm.closest('ul').siblings('label');
        var _tahun = _tahunElm.text();
        var _nama_farm = _tahunElm.closest('ul').siblings('label').text();
        return { 'farm': _nama_farm, 'tahun': _tahun, 'bulan': _bulan, 'tanggal': _tgl }
    },
    akanKonfirmasi: function(elm, target) {
        var _dataKandang = this.parseElmKandang(elm);
        var _text = $(elm).siblings('span[data-value=detail_kandang]').text();
        var _noreg = $(elm).siblings('span.no_reg').text();
        var _data = Config.mappingHeader(_text.split('/'));
        if ($(elm).is(':checked')) {
            /* periksa apakah ada perubahan tanggal docin dari kadiv */
            var _asal = this.getDataRDIT(_noreg);
            var _revisi = '';
            $.when(_asal).done(function() {
                if (!empty(_asal)) {
                    var _docin = Config._tanggalLocal(_asal, '-', ' ');
                    var _revisi = [_dataKandang.tanggal, _dataKandang.bulan, _dataKandang.tahun].join(' ');
                } else {
                    var _docin = [_dataKandang.tanggal, _dataKandang.bulan, _dataKandang.tahun].join(' ');
                }
                var _baris = [_dataKandang.farm, _data['kandang'], _docin, _revisi];
                $(target).find('tbody').append('<tr><td>' + _baris.join('</td><td>') + '</td></tr>');
            });
        } else {
            $(target).find('tbody tr>td:contains(' + _dataKandang.farm + ')').each(function() {
                var _tr = $(this).closest('tr');
                if (_tr.find('td:eq(1)').text() == _data['kandang']) {
                    _tr.remove();
                }
            })
        }
        /* cari total jumlah ayam yang akan dilakukan konfirmasi */
        var _totalKonfirmasi = 0;

        $('#div_kandang_pending ul input.kandangKonfirmasi:checked').each(function() {
            _text = $(this).siblings('span[data-value=detail_kandang]').text();
            _data = Config.mappingHeader(_text.split('/'));
            _totalKonfirmasi += parse_number(_data['jantan']);
        });
        $('#totalPopulasiKonfirmasi').text(number_format(_totalKonfirmasi, 0, ',', '.'));
    },
    konfirmasi: function(elm) {
        var _error = 0;
        var _arr = {},
            _tmp, _farm, _kandang, _tahun, _bulan, _tanggal;
        var _adaDipilih = $('#div_kandang_pending ul input.kandangKonfirmasi:checked');
        if (!_adaDipilih.length) {
            _error++;
        }
        if (!_error) {
            /* buat array terlebih dahulu dari */
            $('#div_kandang_pending ul input.kandangKonfirmasi:checked,#div_kandang_konfirmasi ul>li>a').each(function() {
                _tmp = AktivasiKandang.parseElmKandang($(this));
                _farm = _tmp.farm;
                _kandang = $.map($(this).closest('li').children().not('input'), function(n, i) {
                    return $(n).text();
                });
                _tahun = _tmp.tahun;
                _bulan = _tmp.bulan;
                _tanggal = _tmp.tanggal;
                if (_arr[_farm] == undefined) {
                    _arr[_farm] = {};
                }
                if (_arr[_farm][_tahun] == undefined) {
                    _arr[_farm][_tahun] = {};
                }
                if (_arr[_farm][_tahun][_bulan] == undefined) {
                    _arr[_farm][_tahun][_bulan] = {};
                }
                if (_arr[_farm][_tahun][_bulan][_tanggal] == undefined) {
                    _arr[_farm][_tahun][_bulan][_tanggal] = [];
                }
                _arr[_farm][_tahun][_bulan][_tanggal].push(_kandang.join('#'));
                /* yang sudah dikonfirmasi hapus dari daftar akan_konfirmasi*/
                $(this).closest('li').remove();
            });
            /* urutkan kandangnya */
            _arr[_farm][_tahun][_bulan][_tanggal].sort();
            $('#totalPopulasiKonfirmasi').empty();
            $('#tabelAkanKonfirmasi>tbody').empty();
            $('#div_kandang_konfirmasi').html(createTree(_arr));

            /* perbaiki tampilan tree */
            var _text = '';
            $('#div_kandang_konfirmasi ul>li>a').each(function() {
                _text = $(this).text().split('#');
                $(this).text(_text[0]).css({ 'color': AktivasiKandang.statusWarna[_text[2]] });
                $('<span class="hide" data-value="detail_kandang">' + _text[1] + '</span><span class="_status_approval hide">' + _text[2] + '</span><span class="no_reg hide">' + _text[3] + '</span>').insertAfter($(this));
            });

            /* tambahkan contextmenu untuk tahun dan bulan */
            $('#div_kandang_konfirmasi ul>li>label').each(function() {
                if (Forecast.is_bulan($(this).text())) {
                    $(this).addClass('bulan');
                    var _pertanggal = $(this).siblings('ul');
                    _pertanggal.find('li').each(function() {
                        if ($(this).closest('div').attr('id') == 'div_forecast') {
                            Forecast.list_kebutuhan_pakan_pertanggal_bdy($(this).find(':checkbox:first'));
                        }

                        var _tot = 0;
                        var _label = $(this).find('label');
                        var _perkandang = $(this).find('ul>li>a');
                        var _populasi = '';
                        _perkandang.each(function() {
                            _populasi = parse_number(Forecast.getPopulasiKandang($(this).text()));
                            _tot += _populasi;
                        });
                        _label.text(_label.text() + ' ( ' + number_format(_tot, 0, ',', '.') + ' ekor)');
                    });
                }
            });
        } else {
            toastr.error('Belum ada kandang yang dipilih');
        }
    },
    showKonfirmasi: function(elm) {
        if ($('div.block_konfirmasi').is(':hidden')) {
            $('.rencana-pengiriman').hide();
            $('div.rencana-pengiriman.konfirmasi').closest('div.block_rencana_pengiriman').removeClass('col-md-4').addClass('col-md-8');
            $('div.rencana-pengiriman.konfirmasi>.panel-heading').text('Konfirmasi Tanggal DOC-In');
            $('.konfirmasi').show();
            $(elm).closest('li').removeClass('active');
            $(elm).closest('li').siblings().addClass('active');
            $('#div_kandang_konfirmasi').find('label.bulan').each(function() {
                $(this).siblings('ul').find('li>label').unbind('click');
            });
        }
    },
    showRencanaPengiriman: function(elm) {
        if ($('div.block_konfirmasi').is(':visible')) {
            if (!empty($.trim($('#div_kandang_konfirmasi').html()))) {
                $('.konfirmasi').hide();
                $('div.rencana-pengiriman.konfirmasi').closest('div.block_rencana_pengiriman').removeClass('col-md-8').addClass('col-md-4');
                $('div.rencana-pengiriman.konfirmasi>.panel-heading').text('Siklus Aktif dan Siklus yang akan Diaktivasi');
                $('.rencana-pengiriman').show();
                $(elm).closest('li').removeClass('active');
                $(elm).closest('li').siblings().addClass('active');
                $('#div_kandang_konfirmasi').find('label.bulan').each(function() {
                    $(this).siblings('ul').find('li>label').bind('click', AktivasiKandang.showRencanaKirim);
                });
            }
        }
    },
    showRencanaKirim: function(elm) {
        var _t = elm.target;
        var _minimum_konfirmasi = $('#div_kandang_konfirmasi').data('minimum_konfirmasi').split(',');
        $('.terpilih').removeClass('terpilih').css({ 'background-color': '#FFFFFF' });
        $(_t).addClass('terpilih').css({ 'background-color': '#daeaf1' });
        var _k = $(_t).next('ul').find('li:first>span[data-value=detail_kandang]');
        var _d = AktivasiKandang.parseElmKandang(_k);
        var _data = Config.mappingHeader(_k.text().split('/'));
        var _tglDocIn = Config._convertTgl([_d.tahun, Config._indexBulan(_d.bulan), _d.tanggal].join('-'));
        var _rencanaKirim = Forecast.getRencanaKirimBdy(_data['kode_farm'], _tglDocIn);
        var jk = 'j',
            _content = '',
            _footer = '',
            _status_reg = null;
        $.when(_rencanaKirim).done(function() {
            /* map berdasarkan _indexHeader biar mudah */
            var _tm, _populasi = [],
                _kapasitas = [],
                _kandang = [],
                _total_kapasitas = 0,
                _status_noreg;
            /* looping semua kandangnya */
            _k.closest('ul').find('li').each(function() {
                _tm = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
                _tm = Config.mappingHeader(_tm);
                _status_noreg = $(this).find('span._status_approval').text();
                if (empty(_status_reg)) {
                    _status_reg = _status_noreg;
                }

                //	console.log(parse_number(_tm[Config._jenis_kelamin[jk]],'.',','));
                _populasi.push(parse_number(_tm[Config._jenis_kelamin[jk]], '.', ','));
                _kapasitas.push(parse_number(_tm['jantan'], '.', ','));
                _total_kapasitas += parse_number(_tm['jantan'], '.', ',');
                _kandang.push(_tm['kandang']);
            });
            var _header = [
                '<form class="form form-horizontal">',
                '<div class="form-group">',
                '<label class="control-label col-md-6">Farm</label>',
                '<div class="col-md-6">',
                '<label class="control-label">' + _d.farm + '</label>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="control-label col-md-6">DOC In</label>',
                '<div class="col-md-6">',
                '<label class="control-label">' + Config._tanggalLocal(_tglDocIn, '-', ' ') + '</label>',
                '</div>',
                '</div>',
                '<div class="form-group">',
                '<label class="control-label col-md-6">Total Populasi</label>',
                '<div class="col-md-6">',
                '<label class="control-label">' + number_format(_total_kapasitas, 0, ',', '.') + ' (' + _kandang.length + ' kandang)</label>',
                '</div>',
                '</div>',
                '</form>'
            ].join(' ');

            _content += _header;

            if (empty(_rencanaKirim)) {
                var _info = [
                    '<div class="well text-center">',
                    '<h4><p class="abang">Belum ada rencana pengiriman pakan!</p></h4>',
                    '<p>Pastikan tidak ada kandang yang terlewat sebelum <br /> membuat rencana pengiriman pakan</p>',
                    '<span class="btn btn-default" onclick="AktivasiKandang.setRencanaKirim(this,\'' + _data.kode_farm + '\',\'' + _tglDocIn + '\')">Buat Rencana Pengiriman Pakan</span>',
                    '</div>'
                ].join('');
                _content += _info;
            } else {
                var _yy = AktivasiKandang.generateRencanaKirimInput(_rencanaKirim, _data.kode_farm, _tglDocIn, _populasi);
                _content += _yy[0].outerHTML;

                if (AktivasiKandang.getBisaKonfirmasi()) {
                    var _adaRJ = AktivasiKandang.getKandangReject(_data.kode_farm, _tglDocIn);
                    if (_adaRJ.length) {
                        var _tempRencanaKirim = AktivasiKandang.getTempRencanaKirim(_tglDocIn);
                        if (!empty(_tempRencanaKirim)) {
                            _footer = ['<div class="row">',
                                '<div class="col-md-6">',
                                '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.rekatRencanaPengiriman(this,\'' + _tglDocIn + '\')">Paste Tanggal Kirim dari Farm ' + _tempRencanaKirim.nama_farm + '</div>',
                                '</div>',
                                '<div class="col-md-6">',
                                '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.simpanRencanaPengiriman(this,\'' + _data.kode_farm + '\',\'' + _tglDocIn + '\')">Simpan</div>',
                                '</div>',
                                '</div>'
                            ].join('');
                        } else {
                            _footer = ['<div class="row">',
                                '<div class="col-md-6 col-md-offset-6">',
                                '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.simpanRencanaPengiriman(this,\'' + _data.kode_farm + '\',\'' + _tglDocIn + '\')">Simpan</div>',
                                '</div>',
                                '</div>'
                            ].join('');
                        }

                    } else {
                        _footer = ['<div class="row">',
                            '<div class="col-md-6 col-md-offset-6">',
                            '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.copyRencanaPengiriman(this,\'' + _data.kode_farm + '\',\'' + _tglDocIn + '\',\'' + _d.farm + '\')">Copy Rencana Pengiriman Pakan</div>',
                            '</div>',
                            '</div>'
                        ].join('');
                    }

                } else {
                    if (in_array(_status_reg, _minimum_konfirmasi)) {
                        _footer = ['<div class="row">',
                            '<div class="col-md-6">',
                            '<div class="btn btn-danger col-md-12" onclick="AktivasiKandang.reject(this,\'' + _data.kode_farm + '\',\'' + _tglDocIn + '\',\'' + _d.farm + '\',\'KD\')">Reject</div>',
                            '</div>',
                            '<div class="col-md-6">',
                            '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.approve(this,\'' + _data.kode_farm + '\',\'' + _tglDocIn + '\',\'' + _d.farm + '\',\'KD\')">Approve</div>',
                            '</div>',
                            '</div>'
                        ].join('');

                    }

                }

                _content += _footer;
            }
            $('#divTabelRencanaKirim').html(_content);

            var bolehGantiTglKirim = in_array(_status_reg, AktivasiKandang.statusBolehGantiTglKirim) ? 1 : 0;
            AktivasiKandang.setDatepickerTglKirim($('#divTabelRencanaKirim div.div_rencana_kirim'), bolehGantiTglKirim);
        });
    },
    setRencanaKirim: function(elm, idFarm, _tglDocIn) {
        var _k = $('label.terpilih').next('ul').find('li:first');
        var _tm, _populasi = [],
            _footer, _status_reg = null,
            _status_noreg;

        _k.closest('ul').find('li').each(function(i) {
            _tm = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
            _status_noreg = $(this).find('span._status_approval').text();
            if (empty(_status_reg)) {
                _status_reg = _status_noreg;
            }
            _tm = Config.mappingHeader(_tm);
            _populasi.push(parse_number(_tm[Config._jenis_kelamin['j']], '.', ','));
        });

        var _rencanaKirim = $(this.generateRencanaKirimInput({}, idFarm, _tglDocIn, _populasi));
        var _opsi = {
            beforeShowDay: function(date) { return [!Config.is_hari_libur(Config._getDateStr(date), Permintaan.get_hari_libur())]; },
            dateFormat: 'dd M yy',
        };
        var _tglLama, _ini, _adadp, _maxDate, _tmpMaxDate;
        $(elm).closest('div.well.text-center').replaceWith(_rencanaKirim);
        var bolehGantiTglKirim = in_array(_status_reg, AktivasiKandang.statusBolehGantiTglKirim) ? 1 : 0;
        AktivasiKandang.setDatepickerTglKirim($('#divTabelRencanaKirim div.div_rencana_kirim'), bolehGantiTglKirim);
        var _tempRencanaKirim = this.getTempRencanaKirim(_tglDocIn);
        if (!empty(_tempRencanaKirim)) {
            _footer = ['<div class="row">',
                '<div class="col-md-6">',
                '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.rekatRencanaPengiriman(this,\'' + _tglDocIn + '\')">Paste Tanggal Kirim dari Farm ' + _tempRencanaKirim.nama_farm + '</div>',
                '</div>',
                '<div class="col-md-6">',
                '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.simpanRencanaPengiriman(this,\'' + idFarm + '\',\'' + _tglDocIn + '\')">Simpan</div>',
                '</div>',
                '</div>'
            ].join('');
        } else {
            _footer = ['<div class="row">',
                '<div class="col-md-6 col-md-offset-6">',
                '<div class="btn btn-default col-md-12" onclick="AktivasiKandang.simpanRencanaPengiriman(this,\'' + idFarm + '\',\'' + _tglDocIn + '\')">Simpan</div>',
                '</div>',
                '</div>'
            ].join('');
        }

        $('#divTabelRencanaKirim').append(_footer);
    },

    generateRencanaKirim: function(_rencanaKirim, idFarm, _tglDocIn, _populasi) {
        var _tr_arr = Forecast.generateRencanaKirim(_rencanaKirim, idFarm, _tglDocIn, _populasi,_rulePP);
        var _rulePP = {
            maxKebutuhanPakan : this.maxKebutuhanPakan,
            maxKebutuhanPakanAwaBdy: this.maxKebutuhanPakanDoc,
            maxKebutuhanPakanHarian: this.maxKebutuhanHarian,
            umurPakanHarian: this.umurPakanHarian,
        };
        var _tbody = '<tbody><tr>' + _tr_arr['data'].join('</tr><tr>') + '</tr></tbody>';
        var _thead = '<thead><tr><th data-id="tglkirim">Tanggal Kirim</th><th data-id="tglkebutuhan">Tanggal Kebutuhan</th><th data-id="kodepakan">Kode Pakan</th><th data-id="namapakan">Nama Pakan</th><th data-id="bentuk">Bentuk</th><th data-id="kuantitas">Kuantitas <br /> (Sak)</th></tr></thead>';
        var _content = '<div class="div_rencana_kirim"><table data-sheetname="' + _tglDocIn + '" class="table table-bordered breakdown_pakan">' + _thead + _tbody + '</table></div>';

        return _content;
    },

    generateRencanaKirimInput: function(_rencanaKirim, idFarm, _tglDocIn, _populasi) {
        var _rulePP = {
            maxKebutuhanPakan : this.maxKebutuhanPakan,
            maxKebutuhanPakanAwaBdy: this.maxKebutuhanPakanDoc,
            maxKebutuhanPakanHarian: this.maxKebutuhanHarian,
            umurPakanHarian: this.umurPakanHarian,
        };
        var _tr_arr = Forecast.generateRencanaKirimInput(_rencanaKirim, idFarm, _tglDocIn, _populasi,_rulePP);
        //	console.log(_tr_arr);
        var standart_perumur = Forecast.get_standart_budidaya_bdy(idFarm, _tglDocIn);
        var _ganti_pakan = Forecast.getGantiPakan(standart_perumur['j']);
        var _pakanStandart = {},
            _tmpidpakan, _tmpnamapakan, _jmlpakan = 0;
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
        var _theadPakan = [];
        for (var i in _pakanStandart) {
            _theadPakan.push('<th data-id="kodepakan">' + _pakanStandart[i]['nama_barang'] + '<br />' + _pakanStandart[i]['kode_pakan'] + '</th>');
            _jmlpakan++;
        }

        var _tbody = '<tbody><tr>' + _tr_arr['data'].join('</tr><tr>') + '</tr></tbody>';
        var _thead = '<thead><tr><th rowspan="2" data-id="tglkirim">Tanggal Kirim</th><th rowspan="2" data-id="tglkebutuhan">Tanggal Kebutuhan</th><th colspan="' + _jmlpakan + '" data-id="namapakan">Nama Pakan</th></th></tr><tr>' + _theadPakan.join('') + '</tr></thead>';
        var _content = $('<div class="div_rencana_kirim"><table data-sheetname="' + _tglDocIn + '" class="table table-bordered breakdown_pakan">' + _thead + _tbody + '</table></div>');
        _content.find('input.number')
            .priceFormat({
                prefix: '',
                centsLimit: 2,
                centsSeparator: ',',
                thousandsSeparator: '.'
            });
        /* tambahkan tooltip untuk tanggal kirim */
        var _totalPerTglKirim = _tr_arr['resume_tglkirim'];
        _content.find('tbody tr').each(function() {
            var _tdKirim = $(this).find('td:first');
            var _text = _tdKirim.text();
            if (!empty(_text)) {
                _tdKirim.addClass('has-tooltip_bdy').append(
                    '<span class="tooltip_bdy"> Total pakan yang dikirim ' + Math.ceil(_totalPerTglKirim[_text]) + ' sak</span>'
                );
            }
        });


        return _content;
    },

    simpanRencanaPengiriman: function(elm, idFarm, _tglDocIn) {
        var ini = $(elm).closest('div.panel-body');
        var _tglKirim, _tmp, _data = {},
            _tmpKirim, _tmpKebAwal, _tmpKebAwalStr, _tmpKirimStr, _detail = {};
        var _tmpKirimTersimpan = {},
            _noreg = [];
        var _k = $('label.terpilih').next('ul').find('li:first');
        var _tm;
        _k.closest('ul').find('li').each(function(i) {
            _tm = $(this).find('span.no_reg').text();
            _noreg.push(_tm);
        });

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
            /* cari jml kebututuhannya */
            if (_detail[_tmpKirim] == undefined) {
                _detail[_tmpKirim] = [];
            }
            $(this).find('input.jml_forecast').each(function() {
                var _elm = $(this);
                var _jmlforecast = parse_number(_elm.val(), '.', ',');
                if (_elm.data('standart') > 0 || _jmlforecast > 0)
                    _detail[_tmpKirim].push({ "tgl_kebutuhan": _elm.data('tglkebutuhan'), "kode_barang": _elm.data('kodepj'), "jml_standar": _elm.data('standart'), "jml_forecast": _jmlforecast });
            });
        });
        if (!empty(_data)) {
            bootbox.confirm({
                title: 'Konfirmasi',
                message: 'Apakah anda yakin menyimpan aktivasi siklus baru ?',
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
                            data: { data: _data, kode_farm: idFarm, tgl_docin: _tglDocIn, noreg: _noreg, detail: _detail },
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                if (data.status) {
                                    toastr.success(data.message);
                                    Forecast.rencanaKirimBdy[_tglDocIn] = _tmpKirimTersimpan;
                                    /* hidden border dari input jumlah forecast */
                                    $('input.jml_forecast').addClass('no_border').prop('readonly', 1);
                                    var _k = $('label.terpilih').next('ul').find('li:first');
                                    var _tm;
                                    _k.closest('ul').find('li').each(function(i) {
                                        $(this).find('span._status_approval').text('P1');
                                        $(this).find('a').css({ 'color': AktivasiKandang.statusWarna['P1'] });
                                    });
                                    $(elm).closest('div.row').remove();
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
    copyRencanaPengiriman: function(elm, idFarm, _tglDocIn, nama_farm) {
        var _data = Forecast.getRencanaKirimBdy(idFarm, _tglDocIn);
        this.setTempRencanaKirim(idFarm, nama_farm, _tglDocIn, _data);
        toastr.info('Rencana pengiriman berhasil disalin');
    },
    rekatRencanaPengiriman: function(elm, _tglDocIn) {
        var _tmp = this.getTempRencanaKirim(_tglDocIn);
        var _rencanaKirim = _tmp.data;
        var idFarm = _tmp.kode_farm;
        var _k = $('label.terpilih').next('ul').find('li:first');
        var _tm, _populasi = [];
        _k.closest('ul').find('li').each(function(i) {
            _tm = $(this).find('span.hide[data-value=detail_kandang]').text().split('/');
            _tm = Config.mappingHeader(_tm);
            _populasi.push(parse_number(_tm[Config._jenis_kelamin['j']], '.', ','));
        });
        var _tabelRencanaKirim = this.generateRencanaKirim(_rencanaKirim, idFarm, _tglDocIn, _populasi);
        $(elm).closest('.panel-body').find('.div_rencana_kirim').replaceWith(_tabelRencanaKirim);
        toastr.info('Rencana pengiriman berhasil ditimpa');
    },
    approve: function(elm, kode_farm, _tglDocIn, nama_farm, level) {
        var _tglServer = new Date(Config._tglServer);
        var _docInDate = new Date(_tglDocIn);
        var _error = 0;
        _tglServer.setDate(_tglServer.getDate() + this.maxRejectApprove);
        if (_docInDate < _tglServer) {
            _error++;
            toastr.error('Maksimal approve tanggal DOC In adalah H - ' + this.maxRejectApprove + ' dari tanggal DOC In');
        }
        if (!_error) {
            bootbox.confirm({
                title: 'Konfirmasi',
                message: 'Apakah anda yakin melakukan approval pada siklus kandang : Farm ' + nama_farm + ' ?',
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
                            url: 'forecast/forecast/approveRejectKonfirmasiDOCIn',
                            data: { kode_farm: kode_farm, tgl_docin: _tglDocIn, aksi: 'approve' },
                            type: 'post',
                            dataType: 'json',
                            success: function(data) {
                                if (data.status) {
                                    toastr.success(data.message);
                                    bootbox.hideAll();
                                    if (level == 'KD') {
                                        /* update status noreg menjadi P2 */
                                        var _k = $('label.terpilih').next('ul').find('li:first');
                                        var _tm;
                                        _k.closest('ul').find('li').each(function(i) {
                                            $(this).find('span._status_approval').text('P2');
                                            $(this).find('a').css({ 'color': AktivasiKandang.statusWarna['P2'] });
                                        });
                                    }

                                    if (level == 'KDV') {
                                        var _tr = $(elm).closest('tr');
                                        _tr.find('td').eq(5).html('Approve');
                                        $(elm).closest('div.row').html(data.content);

                                    } else {
                                        $(elm).closest('div.row').remove();
                                    }
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
    reject: function(elm, kode_farm, _tglDocIn, nama_farm, level) {
        this.rejectedElm = elm;
        var _tglServer = new Date(Config._tglServer);
        var _docInDate = new Date(_tglDocIn);
        var _error = 0;
        _tglServer.setDate(_tglServer.getDate() + this.maxRejectApprove);
        if (_docInDate < _tglServer) {
            _error++;
            toastr.error('Maksimal reject tanggal DOC In adalah H - ' + this.maxRejectApprove + ' dari tanggal DOC In');
        }
        var _content = ['<div class="container col-md-12">',
            '<div class="text-center header"><div>Perencanaan DOC In</div>',
            '<div>Aktivasi Siklus Farm Budidaya ' + nama_farm + '</div></div>',
            '<fieldset>',
            '<legend>Keterangan Rejected</legend>',
            '<div class="col-md-12">',
            '<textarea name="keterangan_reject" class="col-md-10"></textarea>',
            '</div>',
            '<div class="col-md-12">',
            '<div name="simpanRejectBtn" style="margin-top:5px" class="btn btn-default" onclick="AktivasiKandang.simpanRejectKadiv(this,\'' + kode_farm + '\',\'' + _tglDocIn + '\',\'' + nama_farm + '\',\'' + level + '\')">Simpan</div>',
            '</div>',
            '<fieldset>',
            '</div>'
        ].join('');
        var _options = {
            title: '&nbsp;',
            message: _content,
            className: 'largeWidth',
        };
        if (!_error) {
            bootbox.dialog(_options);
        }
    },
    simpanRejectKadiv: function(elm, kode_farm, _tglDocIn, nama_farm, level) {
        var _max = 50;
        var _min = 10;
        var _ket = $.trim($(elm).closest('fieldset').find('textarea[name=keterangan_reject]').val());
        var _error = 0;
        if (empty(_ket)) {
            _error++;
            toastr.error('Keterangan harus diisi');
        } else {
            if (_ket.length < _min) {
                _error++;
                toastr.error('Keterangan minimal berisi ' + _min + ' karakter');
            }
            if (_ket.length > _max) {
                _error++;
                toastr.error('Keterangan maximal berisi ' + _max + ' karakter');
            }
        }
        if (!_error) {
            $.ajax({
                url: 'forecast/forecast/approveRejectKonfirmasiDOCIn',
                data: { kode_farm: kode_farm, tgl_docin: _tglDocIn, aksi: 'reject', ket: _ket },
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        toastr.success(data.message);
                        bootbox.hideAll();
                        if (level == 'KD') {
                            /* update status noreg menjadi RJ */
                            var _k = $('label.terpilih').next('ul').find('li:first');
                            var _tm;
                            _k.closest('ul').find('li').each(function(i) {
                                $(this).find('span._status_approval').text('RJ');
                                $(this).find('a').css({ 'color': AktivasiKandang.statusWarna['RJ'] });
                            });
                        }
                        $(AktivasiKandang.rejectedElm).closest('div.row').remove();
                    } else {
                        toastr.error(data.message);
                    }
                },
            });
        }
    },
    /* mencari semua konfirmasi docin yang sudah diapprove dan akan diapprove oleh kadiv */
    approval_cari: function(elm) {
        var _status = [];
        var _tanggal = {};

        var _farm = $('div[name=divFarm] select:enabled').val() || null;
        var _form = $(elm).closest('form');

        _tanggal['fieldname'] = $('select[name=tanggal_docin]:enabled').val();
        /* kumpulkan cekbox yang dipilih */
        _form.find(':checkbox:checked').each(function() {
            _status.push($(this).val());
        });
        var _tgl = $('input[name$=Date]:enabled');
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
            data: { _status: _status, _tanggal: _tanggal, _farm: _farm },
            url: 'forecast/forecast/listKonfirmasiDocin',
            dataType: 'html',
            async: false,
            success: function(data) {
                $('#daftar_konfirmasi_docin').html(data);
            },
        });
    },
    setDatepickerTglKirim: function(tabel, bisaRubahTglKirim) {
        var _tglkebutuhan = null;
        var _umur = 0;
        var _opsi = {
            beforeShowDay: function(date) { return [!Config.is_hari_libur(Config._getDateStr(date), Permintaan.get_hari_libur())]; },
            dateFormat: 'dd M yy',
        };
        var bolehRubahTglKirim = (bisaRubahTglKirim == undefined) ? 0 : bisaRubahTglKirim;

        /** tgl kirim gak boleh dirubah, mengikuti settingan database */
		if(!this.tglKirimBisaDiubah){
			bolehRubahTglKirim = 0;
        }
        
        var _tglLama, _ini, _adadp, _maxDate, _tmpMaxDate, _tmpMinDate;
        tabel.find('table tbody>tr').each(function() {
            var _tdKirim = $(this).find('td:first');
            var _tr = $(this);
            var _text = _tdKirim.text();
            _tglkebutuhan = $(_tdKirim).data('tglKeb');
            _umur = $(_tdKirim).data('umur');
            /* tooltip summary kumulatif pengiriman hanya muncul ketika sudah simpan saja */
            if (bisaRubahTglKirim) {
                if (!empty(_text)) {
                    _tdKirim.find('span').remove();
                }
            }


            if (AktivasiKandang.getBisaKonfirmasi() == 1 && bolehRubahTglKirim) {
                _tdKirim.dblclick(function() {
                    _ini = $(this);
                    _adadp = _ini.find('input.hasDatepicker').length > 0 ? 1 : 0;
                    // tambahkan datepicker
                    if (!_adadp) {
                        var _minDateStr, _minDate;
                        // cari minimal tanggal yang aktif untuk datepicker
                        var _ketemu = 0,
                            _kirimSebelumnya, _tr_tmp, _l, _trObj, _tr_next, _kirimSelanjutnya, _k;
                        var _indexTr = _ini.closest('tr').index();
                        _tr_tmp = _tr.prevAll('tr');
                        _tr_next = _tr.nextAll('tr');
                        _l = _tr_tmp.length - 1;
                        _k = _tr_next.length - 1;
                        var _index = 0;
                        if (_l >= 0) {
                            do {
                                _trObj = $(_tr_tmp[_index]);
                                _kirimSebelumnya = $.trim(_trObj.find('td:first').text()) || _trObj.find('td:first input.hasDatepicker').val();
                                if (!empty(_kirimSebelumnya)) {
                                    _ketemu = 1;
                                    _tmpMinDate = new Date(Config._convertTgl(Config._tanggalDb(_kirimSebelumnya, ' ', '-')));
                                    _tmpMinDate.setDate(_tmpMinDate.getDate() + 1);
                                }
                                _index++;

                            } while (!_ketemu && (_index <= _l));
                        } else {
                            _tmpMinDate = new Date(Config._convertTgl(Config._tanggalDb($.trim(_tdKirim.next().text()), ' ', '-')));
                            //	_tmpMinDate.setDate(_tmpMinDate.getDate() - 3);
                            _tmpMinDate.setDate(_tmpMinDate.getDate() - this.minKirim);
                        }
                        _index = 0;
                        _ketemu = 0;
                        if (_k >= 0) {
                            do {
                                _trObj = $(_tr_next[_index]);
                                _kirimSelanjutnya = $.trim(_trObj.find('td:first').text()) || _trObj.find('td:first input.hasDatepicker').val();

                                if (!empty(_kirimSelanjutnya)) {
                                    _ketemu = 1;
                                    _tmpMaxDate = new Date(Config._convertTgl(Config._tanggalDb(_kirimSelanjutnya, ' ', '-')));
                                    _tmpMaxDate.setDate(_tmpMaxDate.getDate() - AktivasiKandang.maxKirim);
                                }
                                _index++;

                            } while (!_ketemu && (_index <= _k));
                        } else {
                            _tmpMaxDate = new Date(Config._convertTgl(Config._tanggalDb($.trim(_tdKirim.next().text()), ' ', '-')));

                            if (_umur >= 25) {
                                _tmpMaxDate.setDate(_tmpMaxDate.getDate() - AktivasiKandang.maxKirim25);
                            } else {
                                _tmpMaxDate.setDate(_tmpMaxDate.getDate() - AktivasiKandang.maxKirim);
                            }
                        }
                        _minDate = new Date(Config._convertTgl(Config._tanggalDb($.trim(_ini.next().text()), ' ', '-')));
                        //console.log('minDate : ' + _minDate);
                        //	_minDate.setDate(_minDate.getDate() - 3);
                        _minDate.setDate(_minDate.getDate() - AktivasiKandang.minKirim);
                        _maxDate = new Date(Config._convertTgl(Config._tanggalDb($.trim(_ini.next().text()), ' ', '-')));

                        //alert('min: '+_minDate+'; max: '+_maxDate);
                        if (_umur >= 25) {
                            _maxDate.setDate(_maxDate.getDate() + 1 - AktivasiKandang.maxKirim25);
                        } else {
                            _maxDate.setDate(_maxDate.getDate() + 1 - AktivasiKandang.maxKirim);
                        }

                        //console.log('minDate : ' + _minDate + '; tmpMinDate : ' + _tmpMinDate + '; maxDate: ' + _maxDate + '; tmpMinDate: ' + _tmpMinDate);

                        _minDate = _minDate > _tmpMinDate ? _minDate : _tmpMinDate;
                        _maxDate = _maxDate < _tmpMaxDate ? _maxDate : _tmpMaxDate;

                        _minDate.setDate(_minDate.getDate() - 1);
                        _tglLama = empty($.trim(_ini.text())) ? (Config._getDateStr(_minDate) > Config._getDateStr(_maxDate) ? '' : Config._tanggalLocal(Config._getDateStr(_minDate), '-', ' ')) : $.trim(_ini.text());
                        /* periksa apakah _tglLama hari libur atau tidak */
                        if (Config.is_hari_libur(Config._tanggalDb(_tglLama, ' ', '-'))) {
                            var _tmpTglLama = Config._tanggalDb(_tglLama, ' ', '-');
                            _tglLama = Config._tanggalLocal(Config.cari_hari_kerja_terdekat(Config._getDateStr(_maxDate), Permintaan.get_hari_libur()), '-', ' ');
                            if (Config._tanggalDb(_tglLama, ' ', '-') <= _tmpTglLama) {
                                _tglLama = '';
                            }
                        }


                        _ini.html($('<input type="text" readonly />')
                            .datepicker(_opsi).val(_tglLama).datepicker('option', 'maxDate', _maxDate).datepicker('option', 'minDate', _minDate));
                        if (_indexTr == 0) {
                            _ini.append('&nbsp;<i onclick="Forecast.removeDp(this)" class="glyphicon glyphicon-ok-circle"></i>');
                        } else {
                            _ini.append('&nbsp;<i onclick="Forecast.removeDp(this)" class="glyphicon glyphicon-ok-circle"></i>&nbsp;<i  onclick="Forecast.removeTgl(this)" class="glyphicon glyphicon-remove-circle"></i>');
                        }

                    }

                });
            }

        });
        /* jadikan inputan jml_forecast no_border dan readonly */
        if (!AktivasiKandang.getBisaKonfirmasi() || !bolehRubahTglKirim) {
            tabel.find('input.jml_forecast').addClass('no_border').prop('readonly', 1);

        } else {

        }
    },

    overrideNilaiConfig: function(callback) {
        var _url = 'api/general/config';
        var _data = { context: 'aktivasiSiklus', kode_farm: '0' };
        $.ajax({
                url: _url,
                data: _data,
                success: function(data) {
                    for (var i in data.content) {
                        AktivasiKandang[data.content[i]['kode_config']] = parseInt(data.content[i]['value']);
                    }
                },
                dataTyepe: 'json'
            })
            .done(function(data) {
                if (callback !== undefined) {
                    callback();
                };
            });
    },

    _pegawaiTags: new Array(),
    _selectPegawai: {
        'kode_pegawai': '',
        'nama_pegawai': '',
    },

};