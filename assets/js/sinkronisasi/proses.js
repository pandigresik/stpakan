'use strict';
var Sinc = {
    sinkron: function(elm) {
        var _tr = $(elm).closest('tr');
        var _id = _tr.find('td.id').text();
        $.ajax({
            url: 'client/csinkronisasi/sinkron',
            beforeSend: function() {
                $('#info_sinkron').html('Sedang proses ......');
            },
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var _info = [];
                for (var i in data) {
                    _info.push('<div class="col-md-3">' + i + '</div><div class="col-md-9">' + data[i]['message'] + '</div>');
                }
                $('#info_sinkron').html('<div class="row col-md-12 alert alert-warning">' + _info.join(' ') + '</div>');
            },
            async: false
        });
    },
    sinkron2: function(elm) {
        var _tr = $(elm).closest('tr');
        var _id = _tr.find('td.id').text();

        $.ajax({
            url: 'client/csinkronisasi/sinkron2',
            beforeSend: function() {
                $('#info_sinkron').html('Sedang proses ......');
            },
            type: 'get',
            dataType: 'json',
            success: function(data) {
                var _info = [];
                for (var i in data) {
                    _info.push('<div class="col-md-3">' + i + '</div><div class="col-md-9">' + data[i]['message'] + '</div>');
                }
                $('#info_sinkron').html('<div class="row col-md-12 alert alert-warning">' + _info.join(' ') + '</div>');
            },
            async: false
        });
    },
    refresh: function(_search) {
        var _limit = $('select[name=limit_data]').val();
        var _url = 'sinkronisasi/sinkronisasi/index?limit=' + _limit;
        if (_search != undefined) {
            _url += '&cari=' + _search;
        }
        Home.replace_main_content(_url);
    },
    search: function(elm) {
        var _div_cari = $(elm).closest('div.div_cari');
        var _search = $.trim(_div_cari.find('input').val());
        if (!empty(_search)) {
            this.refresh(_search);
        } else {
            bootbox.alert('Kata kunci pencarian harus diisi');
        }
    },
    detailSinkron: function(elm) {
        var _tr = $(elm).closest('tr');
        var _id = _tr.find('td.id');
        var _idSinkron = _id.text();
        var _url = 'sinkronisasi/sinkronisasi/detailSinkron';
        $.get(_url, { id: _idSinkron }, function(data) {
            bootbox.confirm({
                title: 'Detail Sinkronisasi',
                message: data,
                className: 'largeWidth',
                buttons: {
                    'cancel': {
                        label: 'Tutup',
                        className: 'btn-default'
                    },
                    'confirm': {
                        label: 'Buat Sinkron Ulang',
                        className: 'btn-danger'
                    }
                },
                callback: function(result) {
                    if (result) {
                        Sinc.sinkronUlang(_idSinkron);
                    }
                }
            })
        }, 'html');
    },
    sinkronUlang: function(idSinkron) {
        bootbox.confirm({
            title: '<div class="abang">Bahaya !!! Jangan Main - Main</div>',
            message: 'Apakah anda yakin akan membuat sinkron ulang untuk id ' + idSinkron + ' ?',
            buttons: {
                'cancel': {
                    label: 'Batal',
                    className: 'btn-default'
                },
                'confirm': {
                    label: 'Ya, Saya Yakin',
                    className: 'btn-danger'
                }
            },
            callback: function(result) {
                if (result) {
                    $.get('sinkronisasi/sinkronisasi/sinkronUlang/' + idSinkron, {}, function(pesan) {
                        bootbox.hideAll();
                        bootbox.alert(pesan);
                    }, 'html');
                }
            }
        });

    }
};

$(function() {
    var _urlResetBudgetOtomatis = 'api/pengembalian_budget_otomatis/resetBudget';
    $.post(_urlResetBudgetOtomatis, {}, function() {
        console.log('Proses reset budget otomatis berjalan .......');
    });
})