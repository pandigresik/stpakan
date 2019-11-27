var Verifikasi = {
    timer_scan_kendaraan: true,
    timer_finger: false,
    jeda_waktu_scan_kendaraan: 3000,
    max_scan_kendaraan_baru: 50,
    num_scan_kendaraan_baru: 0,
    dataFinger: {},
    dataDO: null,
    setFinger: function(finger) {
        this.dataFinger = finger;
    },
    getFinger: function() {
        return this.dataFinger;
    },
    setDO: function(_do) {
        this.dataDO = _do;
    },
    getDO: function() {
        return this.dataDO;
    },
    cari: function(elm, page) {
        var _tindaklanjut = $('input[name=do_tindaklanjut]').is(':checked') ? 1 : 0;
        var _awalpanen = $('input[name=tanggal-panen-awal]').datepicker('getDate');
        var _akhirpanen = $('input[name=tanggal-panen-akhir]').datepicker('getDate');
        var _offset = page == undefined ? 0 : page;
        var _filter = { tindaklanjut: _tindaklanjut };

        if (!empty(_awalpanen)) {
            _filter['awal_panen'] = _awalpanen.toLocaleDateString().split('/').reverse().join('-');
        }

        if (!empty(_akhirpanen)) {
            _filter['akhir_panen'] = _akhirpanen.toLocaleDateString().split('/').reverse().join('-');
        }
        $('#daftar-do-table table>thead>tr.filter>th>input.filter').each(function() {
            if (!empty($.trim($(this).val()))) {
                if ($(this).attr('name') == 'tgl_panen') {
                    _filter[$(this).attr('name')] = $(this).datepicker('getDate').toLocaleDateString().split('/').reverse().join('-');
                } else {
                    _filter[$(this).attr('name')] = $(this).val();
                }
            }
        });
        var _url = 'security/verifikasi_kendaraan/lists/' + _offset;
        var _ini = this;
        $.get(_url, { offset: _offset, filter: _filter }, function(data) {
            if (data.status) {
                var _tbody = data.content.data;
                var _pagination = data.content.pagination;
                var _page, _elmLink;
                $('#daftar-do-table table>tbody').html(_tbody);
                $('div.pagination').html(_pagination).promise().done(function() {
                    $('div.pagination').find('a').each(function() {
                        _elmLink = $(this);
                        _page = $(this).data('ci-pagination-page');
                        if (_page != undefined) {
                            $(this).attr('href', '/' + _page);
                            $(this).attr('onclick', 'return Verifikasi.goPage(this)');
                        }

                    });
                });

            }
        }, 'json');
    },
    goPage: function(_elmLink) {
        var elm = $('#btn-cari');
        this.cari(elm, $(_elmLink).data('ci-pagination-page'));
        return false;
    },

    scan_kendaraan_baru: function() {
        var _ini = this;
        _ini.num_scan_kendaraan_baru++;
        if (this.timer_scan_kendaraan) {
            if (_ini.num_scan_kendaraan_baru >= _ini.max_scan_kendaraan_baru) {
                window.location.reload();
            }
            $.get('fingerprint/panen/scan_kendaraan_baru', {}, function(data) {
                if (data.status) {
                    _ini.timer_scan_kendaraan = false;
                    _ini.showDialogFinger(data.content);
                } else {
                    setTimeout("Verifikasi.scan_kendaraan_baru()", _ini.jeda_waktu_scan_kendaraan);
                }
            }, 'json');
        }
    },

    showDialogFinger: function(_content) {
        var _do = _content.do;
        var _kendaraan = _content.kendaraan;
        var _finger = _content.finger;
        this.setFinger(_finger);
        var _ini = this;
        var _tbody = [];
        for (var i in _do) {
            _tbody.push('<tr><td>' + _do[i]['no_do'] + '</td><td>' + _do[i]['no_sj'] + '</td></tr>');
        }
        var _message = [
            '<form id="form_plot" class="form form-horizontal">',
            '<div class="form-group">',
            '<label class="control-label col-md-3">Nopol</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + _kendaraan['NAMA_SOPIR'] + '"/>',
            '</div>',
            '</div>',

            '<div class="form-group">',
            '<label class="control-label col-md-3">Sopir</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + _kendaraan['NOPOL'] + '"/>',
            '</div>',
            '</div>',

            '<div class="form-group">',
            '<label class="control-label col-md-3">Tanggal Panen</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + _kendaraan['TGL_PANEN'] + '"/>',
            '</div>',
            '</div>',

            '<div class="form-group">',
            '<label class="control-label col-md-3">Jam Masuk</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + _kendaraan['JAM_MASUK'] + '"/>',
            '</div>',
            '</div>',

            '<div class="form-group">',
            '<label class="control-label col-md-3">Jam Keluar</label>',
            '<div class="col-md-6">',
            '<input type="text" disabled class="form-control" value="' + _kendaraan['JAM_KELUAR'] + '"/>',
            '</div>',
            '</div>',

            '</form>',
            '<table class="table table-bordered"><thead><tr><th>No. DO</th><th>No. SJ</th></tr></thead>',
            '<tbody>' + _tbody.join('') + '</tbody></table>',
            '<div class="text-center"><img src="assets/images/finger.jpg" height="150px" style="filter:invert(100%);"></div>',
            '<div class="text-center"><span id="error_message_finger" class="abang"></span></div>',
        ];
        var box = bootbox.dialog({
            message: _message.join(''),
            closeButton: false,
            title: "Finger Sopir",
        });
        box.bind('shown.bs.modal', function() {
            _ini.timer_finger = true;
            var _listDO = [];
            for (var i in _do) {
                _listDO.push(_do[i]['no_do']);
            }
            _ini.setDO(_listDO);
            _ini.cek_verifikasi(_finger.date_transaction);
            var _tr_terpilih = $('#daftar-do-table tbody').find('tr[data-no_do="' + _do['NO_DO'] + '"]');
            _tr_terpilih.removeClass('bg_kuning');
            _tr_terpilih.removeClass('bg_orange');
            _tr_terpilih.addClass('bg_biru');
        });

    },

    fingerprint: function() {
        var _ini = this;
        this.simpan_transaksi_verifikasi(function(result) {
            if (result.content.date_transaction) {
                _ini.timer_finger = true;
                _ini.cek_verifikasi(result.content.date_transaction);
            }
        });

    },

    simpan_transaksi_verifikasi: function(callback) {
        var dataFinger = this.getFinger();
        $.ajax({
            type: "POST",
            url: "fingerprint/panen/simpan_transaksi_verifikasi",
            data: dataFinger,
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    cek_verifikasi: function(date_transaction) {
        if (this.timer_finger == true) {
            var _ini = this;
            var _result = {
                result: 0
            };
            $.ajax({
                type: "POST",
                url: "fingerprint/panen/cek_verifikasi/1",
                data: {
                    date_transaction: date_transaction,
                    do: _ini.getDO()
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        _ini.timer_finger = false;
                        if (data.match) {
                            _ini.timer_scan_kendaraan = true;
                            _ini.num_scan_kendaraan_baru = 0;
                            bootbox.hideAll();
                            _ini.scan_kendaraan_baru();
                            $('#btn-cari').click();
                        } else {
                            $('#error_message_finger').text('Finger Tidak Valid, Harap Finger Ulang');
                            _ini.fingerprint();
                        }
                    } else {
                        _ini.timer_finger = true;
                        setTimeout("Verifikasi.cek_verifikasi('" + date_transaction + "')", 3000);
                    }
                }
            });
        }
    },

}


$(function() {
    'use strict';
    $("input[name=tgl_panen]").datepicker({
        dateFormat: 'dd M yy'
    });
    $("#tanggal-panen-awal").datepicker({
        dateFormat: 'dd M yy',
        onClose: function(selectedDate) {
            $("#tanggal-panen-akhir").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#tanggal-panen-akhir").datepicker({
        dateFormat: 'dd M yy',
        onClose: function(selectedDate) {
            $("#tanggal-panen-awal").datepicker("option", "maxDate", selectedDate);
        }
    });
    $('#btn-cari').click();
    Verifikasi.scan_kendaraan_baru();
}());