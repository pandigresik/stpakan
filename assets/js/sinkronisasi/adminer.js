'use strict';
var Adminer = {
    urlQuery: 'sinkronisasi/adminer/getQuery',
    urlFormSinkron: 'sinkronisasi/adminer/formSinkron',
    urlSimpanSinkron: 'sinkronisasi/adminer/simpanSinkron',
    keySinkron: null,
    table: null,
    checkAll: function(elm) {
        var _table = $(elm).closest('table');
        var _checked = $(elm).is(':checked') ? 1 : 0;
        _table.find('tbody>tr>td>:checkbox').prop('checked', _checked);
    },
    setTable: function(_table) {
        this.table = _table;
    },
    getTable: function() {
        return this.table;
    },
    setKeySinkron: function(data) {
        this.KeySinkron = data;
    },
    getKeySinkron: function() {
        return this.KeySinkron;
    },
    execute: function(elm) {
        var _panel = $(elm).closest('.panel-body');
        var _query = _panel.find('textarea[name=query]').val();
        var _limit = _panel.find('input[name=limit]').val() || 50;
        var _url = 'sinkronisasi/adminer/run';
        $.ajax({
            url: _url,
            data: { query: _query, limit: _limit },
            type: 'post',
            beforeSend: function() {
                $('#queryResult').html('Menunggu response dari server ....');
            },
            success: function(html) {
                $('#queryResult').html(html);
            },
            dataType: 'html'
        })
    },
    pilih: function(elm) {
        $(elm).siblings().removeClass('terpilih');
        $(elm).addClass('terpilih');
        this.select(elm);
    },
    pilihBaris: function(elm) {
        $(elm).siblings().removeClass('terpilih');
        $(elm).addClass('terpilih');

    },
    select: function(elm) {
        var _terpilih = $('#listTable li.terpilih');
        if (!_terpilih.length) {
            bootbox.alert('Belum ada tabel yang dipilih');
        }
        var _table = $.trim(_terpilih.text());
        this.setTable(_table);
        this.getQuery(_table, 'R');
    },
    getQuery: function(_table, aksi) {
        $.get(this.urlQuery, { tabel: _table, aksi: aksi }, function(data) {
            if (data.status) {
                Adminer.setQuery(data.content);
            } else {
                bootbox.alert(data.message);
            }
        })
    },
    insert: function(elm) {
        var _table = this.getTable();
        if (empty(_table)) {
            bootbox.alert('Belum ada tabel yang dipilih');
            return;
        }
        this.getQuery(_table, 'C');
    },
    update: function(elm) {
        var _table = this.getTable();
        if (empty(_table)) {
            bootbox.alert('Belum ada tabel yang dipilih');
            return;
        }
        this.getQuery(_table, 'U');
    },
    delete: function(elm) {
        var _table = this.getTable();
        if (empty(_table)) {
            bootbox.alert('Belum ada tabel yang dipilih');
            return;
        }
        this.getQuery(_table, 'D');
    },
    setQuery: function(query) {
        $('#command_page textarea[name=query]').val(query);
    },
    generateSinkron: function(elm) {
        var _form_group = $(elm).closest('.form-group');
        var _checked = _form_group.find(':checked');
        var _form = _form_group.closest('form');
        var _preview_div = _form.find('.preview_div');
        var _ck = $('#queryResult tbody :checked');
        var _tr, _keys = [],
            _keyStr = [],
            _key_tr = {};
        var _aksi = _form.find('select[name=aksi]').val();
        var _status_identity = _form.find('input[name=status_identity]').is(':checked') ? 1 : 0;
        var _tabel = this.getTable();
        this.setKeySinkron(null);
        if (_checked.length) {
            var _index, _nilai, _key_label, _elmCk;
            _ck.each(function() {
                _key_tr = { aksi: _aksi, tabel: _tabel, status_identity: _status_identity, kunci: {} };
                _tr = $(this).closest('tr');
                _checked.each(function() {
                    _elmCk = $(this);
                    _key_label = _elmCk.val();
                    _index = _elmCk.data('index') + 1;
                    _nilai = $.trim(_tr.find('td').eq(_index).text());
                    _key_tr.kunci[_key_label] = _nilai;
                });
                _keys.push(_key_tr);
                _keyStr.push(JSON.stringify(_key_tr));
            });
            this.setKeySinkron(_keys);
            _preview_div.html('<div class="badge">' + _keyStr.join('</div><div class="badge">') + '</div>');
        }
    },
    createSinkronisasi: function(_transaksi, _kodefarm, _keySinkron) {
        /** pastikan semua sudah diisi */
        $.post(this.urlSimpanSinkron, { transaksi: _transaksi, kode_farm: _kodefarm, detail_sinkron: _keySinkron }, function(data) {
            if (data.status) {
                bootbox.alert(data.message, function() {
                    $('#queryResult tbody :checked').prop('checked', 0);
                });
            }
        }, 'json');
    },
    sinkron: function() {
        /** pastikan sudah memilih checkbox  */
        var _ck = $('#queryResult tbody :checked');
        if (!_ck.length) {
            bootbox.alert('Belum ada baris yang dipilih');
            return;
        }
        /** user harus memilih farm dan key dari data tersebut */
        var _table = this.getTable();
        var _ini = this;
        $.get(this.urlFormSinkron, { tabel: _table }, function(data) {
            if (data.status) {
                var _options = {
                    title: 'Sinkronisasi ST-Pakan',
                    message: data.content,
                    className: 'largeWidth',
                    buttons: {
                        Ok: {
                            label: 'Buat Sinkron',
                            className: '',
                            callback: function(e) {
                                var _form = $(e.target).closest('.modal-content').find('.modal-body form');
                                var _transaksi = _form.find('input[name=transaksi]').val();
                                var _kodefarm = _form.find('select[name=kode_farm]').val();
                                var _keySinkron = _ini.getKeySinkron();
                                if (empty(_keySinkron)) {
                                    bootbox.alert('Kunci sinkron belum dipilih');
                                    return false;
                                }
                                if (empty(_transaksi)) {
                                    bootbox.alert('Transaksi harus diisi');
                                    return false;
                                }
                                var _pesan = 'Apakah anda akan membuat sinkronisasi dengan tujuan farm ' + _form.find('select[name=kode_farm] option:selected').text() + ' ?';
                                bootbox.confirm(_pesan, function(result) {
                                    if (result) {
                                        _ini.createSinkronisasi(_transaksi, _kodefarm, _keySinkron);
                                    } else {
                                        return false;
                                    }
                                });

                            }
                        },
                        Cancel: {
                            label: 'Batal',
                            className: 'btn-danger',
                            callback: function(e) {

                            }
                        }
                    },
                };
                bootbox.dialog(_options);
            }
        });
    }
};