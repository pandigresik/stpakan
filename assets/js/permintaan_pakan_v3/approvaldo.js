var ApproveDO = {
    keterangan: '',
    cari: function() {
        /* status 1 menunjukkan rekap, kalau 0 menunjukkan trasaksi */
        /* cari tanggal pengiriman */
        var _startDate = $("input[name=startDate]").val();
        var _endDate = $("input[name=endDate]").val();
        var _error = 0;
        var _tindaklanjut = $('input:checkbox[name=tindak_lanjut]').is(':checked') ? 1 : 0;

        if (!_error) {
            /* cari semua parameter pencarian */
            var _paramPencarian = {};
            var _tglKirim = {};

            /* cari list order pembelian */
            $.ajax({
                type: 'post',
                data: { cari: _paramPencarian, tglKirim: _tglKirim, tindaklanjut: _tindaklanjut },
                url: 'permintaan_pakan_v3/pembelian_pakan/list_order_approve',
                success: function(data) {
                    $('#div_list_order tbody').html(data);
                },
            });

        }
    },
    approve: function(elm) {
        var _checked = $('#div_list_order :checked');
        this.keterangan = '';
        if (!_checked.length) {
            toastr.error('Mohon memilih kandang terlebih dahulu', 'Notifikasi');
            return;
        }
        var _ini = this;

        bootbox.confirm({
            title: 'Konfirmasi Approval',
            message: '<div class="text-center">Apakah Anda yakin melanjutkan proses approval plotting DO  ? ',
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
                    _ini.approveReject();
                }
            }
        });
    },

    reject: function(elm) {
        var _checked = $('#div_list_order :checked');
        this.keterangan = '';
        if (!_checked.length) {
            toastr.error('Mohon memilih tanggal kirim', 'Notifikasi');
            return;
        }
        var _content = ['<div class="dialog_reject">',
            '<div class="col-md-12">Mohon entri alasan reject (min. 10 karakter dan max. 100 karakter)</div>',
            '<div class="col-md-12">',
            '<textarea name="keterangan_reject" class="col-md-10" maxlength=100 onkeyup="ApproveDO.aktifkanBtn(this)"></textarea>',
            '</div>',
            '<div class="col-md-12 new-line">',
            '<div class="col-md-2">',
            '<div name="simpanRejectBtn" class="btn btn-default disabled" onclick="ApproveDO.approveReject(this)">Simpan</div>',
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
    approveReject: function(elm) {
        if (elm != undefined) {
            var ini = $(elm);
            var _p = ini.closest('.dialog_reject');
            var _ket = $.trim(_p.find('textarea[name=keterangan_reject]').val());
            this.keterangan = _ket;
        }
        var _ini = this;
        var _farmkirim = [],
            _kodefarm, _tglKirim, _tr,
            _url, nextstatus;
        var _checked = $('#div_list_order :checked');
        _checked.each(function() {
                _tr = $(this).closest('tr');
                _farmkirim.push({
                    kode_farm: _tr.data('kode_farm'),
                    tgl_kirim: _tr.data('tgl_kirim'),
                })
            })
            /* simpan ke database */

        _url = 'permintaan_pakan_v3/pembelian_pakan/approvereject';

        $.post(_url, { farmkirim: _farmkirim, keterangan: this.keterangan }, function(data) {
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
};

$(function() {
    'use strict';
    $('input:checkbox[name=tindak_lanjut]').click(function() {
        var _status = $(this).is(':checked');
        /* jika true maka disable datepicker, jik false enable */
        if (_status) {
            $('input[name$=Date]').datepicker('option', 'disabled', 1);
            $('select,input:not(:checkbox)').prop('disabled', 1);
            $('span[name=btnCari]').addClass('disabled');

        } else {
            $('input[name$=Date]').datepicker('option', 'disabled', 0);
            $('select,input:not(:checkbox)').prop('disabled', 0);
            $('span[name=btnCari]').removeClass('disabled');
        }
    });

    //Permintaan.list_pp_cari($(':checked:first'));
    $('select,input:not(:checkbox)').prop('disabled', 1);
    ApproveDO.cari();
}());