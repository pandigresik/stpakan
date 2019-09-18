var ImportBapd = {
    dataDOCIn: {},
    kandangSiklus: {},
    awalDOCIn: null,
    thnDOCIn: null,
    periodeSiklus: {},
    setKandangSiklus: function(ks) {
        this.kandangSiklus = ks;
    },
    getKandangSiklus: function() {
        return this.kandangSiklus;
    },
    getDataDocIn: function() {
        return this.dataDOCIn;
    },
    setDataDocIn: function(obj) {
        this.dataDOCIn = obj;
    },
    setPeriodeSiklus: function(periodeSiklus) {
        this.periodeSiklus = periodeSiklus;
    },
    getPeriodeSiklus: function() {
        return this.periodeSiklus;
    },
    showDiv: function(target) {
        var t = $(target);
        if (t.is(':hidden')) {
            t.show();
        } else {
            t.hide();
        }
    },
    lihatData: function(elm, simpan) {
        $('#detailkodebox').find('table').remove();
        var files = $('#file-upload').get(0).files;;
        if (empty($('#docinfile').val())) {
            toastr.error('Mohon memilih lampiran terlebih dahulu');
            return;
        }

        if (files == undefined) {
            toastr.error('Mohon memilih lampiran terlebih dahulu');
            return;
        }

        var result = [];
        var i, f;
        for (i = 0, f = files[i]; i != files.length; ++i) {
            var reader = new FileReader();
            var name = f.name;
            reader.onload = function(e) {
                var data = e.target.result;
                var _error = 0;
                var workbook = XLSX.read(data, { type: 'binary' });
                var _y = ImportBapd.to_json(workbook);
                var _k = ImportBapd.periksaNamaKolom(_y);
                _error += _k.err;
                if (_k.err) {
                    toastr.warning(_k.msg.join('</br>'), 'Peringatan');
                }

                if (!_error) {
                    var _dataTabel = ImportBapd.kumpulkanData(_y);
                    var _isiTabel = ImportBapd.buatTabel(_dataTabel);
                    $('#detailkodebox').append(_isiTabel);
                    if (simpan != undefined) {
                        ImportBapd.execSimpanData(elm);
                    }
                }

            };

            reader.readAsBinaryString(f);
        }
    },
    kumpulkanData: function(obj) {
        var _dataBox = [];
        for (var _sn in obj) {
            var _x = obj[_sn];
            for (var _d in _x) {
                _dataBox[_d] = _x[_d];
            }
        }
        return _dataBox;
    },
    validNamaKolom: ['FARM', 'SIKLUS', 'KANDANG', 'NO_SJ', 'KODE_BOX', 'JML_BOX'],
    validFormatKolom: { 'FARM': /^\w{2}/, 'SIKLUS': /^\d{4}\-\d{1}$/, 'KANDANG': /^\d{2}$/, 'NO_SJ': /\w+$/, 'KODE_BOX': /\w+$/, 'JML_BOX': /\d+$/ },
    periksaNamaKolom: function(dataJson) {
        var _result = { err: 1, msg: [] };
        var _i = 0;
        var _tmp, _error = 0;
        for (var _sn in dataJson) {
            var _x = dataJson[_sn];
            for (var i in _x) {
                _tmp = _x[i];
                if (_i > 0) {
                    _result.err = _error;
                    return _result;
                }
                for (var z in _tmp) {
                    if (!in_array(z, this.validNamaKolom)) {
                        _error++;
                        _result.msg.push('Nama kolom <strong>' + z + '</strong> tidak sesuai template');
                    }
                }
                _i++;
            }
        }
    },

    buatTabel: function(obj) {
        var _tbody = [],
            _tr = [],
            _table = [];
        var _tr_head = [];
        for (var _h in this.validNamaKolom) {
            _tr_head.push(this.validNamaKolom[_h]);
        }
        _table.push('<thead><tr class="sticky-header"><th>' + _tr_head.join('</th><th>') + '</th></tr></thead>');
        for (var _i in obj) {
            var _td = [];
            var _tmp_tr = obj[_i];
            for (var _a in _tmp_tr) {
                _td.push(_tmp_tr[_a]);
            }
            _tr.push('<tr><td>' + _td.join('</td><td>') + '</td></tr>');
        }
        _tbody.push(_tr.join(''));
        _table.push('<tbody>' + _tbody + '</tbody>');
        return '<div class="sticky-table"><table class="table table-bordered">' + _table.join('') + '</table>';
    },

    isTanggalValid: function(str) {
        var polaTanggal = /\d{2}\/\d{2}\/\d{4}/;
        var _error = 0;
        if (!polaTanggal.test(str)) {
            _error++;
        }
        if (!_error) {
            var t = this.getValidDate(str, '/', '-');

            if (!empty(t)) {
                if (!this.isValidDate(t)) {
                    _error++;
                }

            }
        }
        return !_error;
    },
    /* format yang diberikan adalah format indonesia DD/MM/YYY*/
    getValidDate: function(str, separatorAsal, separatorTujuan) {
        if (!empty(str)) {
            var _y = str.split('/');
            return _y.reverse().join('-');
        } else {
            return null;
        }
    },
    isValidDate: function(str) {
        if (!empty(str)) {
            var t = new Date(str);
            return t == 'Invalid Date' ? 0 : 1;
        } else {
            return 0;
        }
    },
    to_json: function(workbook) {
        var result = {};
        workbook.SheetNames.forEach(function(sheetName) {
            var roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]);
            if (roa.length > 0) {
                result[sheetName] = roa;
            }
        });
        return result;
    },
    /* simpan rencana doc in */
    simpanData: function(elm) {
        var _error = 0;
        var _table = $('#detailkodebox').find('table');
        if (!_table.length) {
            this.lihatData(elm, 1);
        } else {
            this.execSimpanData(elm);
        }

    },
    execSimpanData: function(elm) {
        var _dataKirim = [],
            _td, _tr;
        var _tableKirim = $('#detailkodebox').find('table');
        if (!_tableKirim.length) {
            toastr.error('Mohon memilih lampiran terlebih dahulu');
            return;
        } else {
            _tableKirim.find('tbody>tr').each(function() {
                _tr = {};
                $(this).find('td').each(function(i, v) {
                    _tr[ImportBapd.validNamaKolom[i]] = $(v).text();
                });
                _tr['NO_REG'] = [_tr['FARM'], _tr['SIKLUS'], _tr['KANDANG']].join('/');
                delete(_tr['FARM']);
                delete(_tr['SIKLUS']);
                delete(_tr['KANDANG']);
                _dataKirim.push(_tr);
            });
        }
        var _farm = $('#tablebapd').find('tbody>tr.terpilih>td.kode_farm').data('kode_farm');
        var _siklus = $('#tablebapd').find('tbody>tr.terpilih>td.periode_siklus').text();
        $.ajax({
            url: 'penerimaan_docin/import_box/simpan_box',
            data: { data: _dataKirim, farm: _farm, siklus: _siklus },
            beforeSend: function() {
                toastr.info('Mohon tunggu proses menyimpan data ...');
            },
            type: 'post',
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    toastr.success(data.message);
                    $('#main_content').empty().load('penerimaan_docin/import_box/index');

                } else {
                    toastr.error(data.message);
                }

            },
        });
    },

    preview: function() {
        var _tr = $('#tablebapd tbody>tr.terpilih');
        var _error = 0;
        var _kode_farm = _tr.find('td.kode_farm').data('kode_farm');
        var _periode_siklus = _tr.find('td.periode_siklus').text();

        if (!_error) {
            /* dapatkan semua farm pada tahun tersebut */
            $.ajax({
                url: 'penerimaan_docin/import_box/list_farm_preview',
                data: { kode_farm: _kode_farm, periode_siklus: _periode_siklus },
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        $('#detailkodebox').html(data.content);
                    } else {
                        toastr.error(data.message);
                    }
                },
            });

        }

    },

    pilihSiklus: function(elm) {
        var _sudahUpload = $(elm).data('upload');
        if (_sudahUpload) {
            $('#divBtn').find('div.sudahupload').removeClass('hide');
            $('#divBtn').find('div.belumupload').addClass('hide');
        } else {
            $('#divBtn').find('div.belumupload').removeClass('hide');
            $('#divBtn').find('div.sudahupload').addClass('hide');
        }
        $(elm).siblings('.terpilih').removeClass('terpilih');
        $(elm).addClass('terpilih');
        $('#detailkodebox').html('');
    },

    import: function() {
        var _template = [
            '<form class="form form-horizontal">',
            '<div class="form-group">',
            '<div class="col-md-9 col-md-offset-3"><span class="btn btn-default"><a href="file_upload/bapd/template_box.xls" ><i class="glyphicon glyphicon-file"></i> Template Lampiran</a></span></div>',
            '</div>',
            '<div class="form-group">',
            '<label class="col-md-3">Pilih Lampiran</label>',
            '<div class="col-md-7">',
            '<div class="input-group">',
            '<input type="text" class="form-control" id="docinfile" name="lampirkan-foto" readonly>',
            '<span class="btn btn-default btn-file input-group-addon">',
            '<b>...</b> <input type="file" id="file-upload" >',
            '</span>',
            '</div>',
            '</div>',
            '*.xls, *.xlsx',
            '</div>',
            '<div class="form-group">',
            '<div class="col-md-9 col-md-offset-3"><span class="btn btn-default" onclick="ImportBapd.lihatData(this)"><i class="glyphicon glyphicon-search"></i> Lihat File</span> &nbsp; <span class="btn btn-default" onclick="ImportBapd.simpanData(this)"><i class="glyphicon glyphicon-open"></i>  Unggah File</span></div>',
            '</div>',
            '</form>'
        ];
        $('#detailkodebox').html(_template.join(''));
    },
    cari: function(elm) {
        var _form = $(elm).closest('form');
        var _periode = _form.find('select[name=kode_siklus] option:selected').text();
        var _nama_farm = _form.find('select[name=kode_farm] option:selected').text();
        $('#tablebapd tbody>tr').show();
        if (_nama_farm != '-- Farm --') {
            $('#tablebapd tbody>tr>td.kode_farm:not(:contains(' + _nama_farm + '))').closest('tr').hide();
        }
        if (_periode != '-- Siklus --') {
            $('#tablebapd tbody>tr>td.periode_siklus:not(:contains(' + _periode + '))').closest('tr').hide();
        }
    },
    laporanBapd: function() {
        var _farm = $('#tablebapd').find('tbody>tr.terpilih>td.kode_farm').data('kode_farm');
        var _siklus = $('#tablebapd').find('tbody>tr.terpilih>td.periode_siklus').text();
        var _url = '#report/laporan_bapd?periode=' + _siklus + '&kodefarm=' + _farm;
        window.open(_url, "_blank")
    }
};
(function() {
    'use strict';
    $('#preview_div,#import_div').hide();
    $(document).on('change', '.btn-file :file', function(e) {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        var _file = input.get(0).files[0];
        var _validType = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        if (!in_array(_file.type, _validType)) {
            bootbox.alert("File yang dapat diunggah hanya dengan format xls atau xlsx", function() {
                $('#docinfile').val('');
                $('#file-upload').val('');
            });
            return false;
        } else {
            $('#docinfile').val(label);
        }
    });

}());