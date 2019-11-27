'use strict';
var Sinc_DO = {
    ambilDO: function(elm, url) {
        var _tglPanen = [];
        var _data = {};
        var _startDate = $('input[name=tglPanen]').datepicker('getDate');
        if (_startDate != null) {
            _tglPanen = [_startDate.getFullYear(), _startDate.getMonth() + 1, _startDate.getDate()];
            _data['tglPanen'] = _tglPanen.join('-');
        }
        $.ajax({
            url: url,
            beforeSend: function() {
                $('#divListDO').html('Loading data ...... Mohon ditunggu.');
            },
            data: _data,
            type: 'get',
            dataType: 'html',
            success: function(data) {
                $('#divListDO').html(data)
            },
            async: false
        });
    },
    simpanDO: function(elm) {
        var _data = {},
            _rcn_mulai_panen, _rcn_selesai_panen,
            _kodeFarm, _no_do, _no_sj, _no_reg, _berat, _jumlah, _tglpanen, _rit, _nopol, _sopir, _id_sopir, _nik_timpanen, _tmp = {};
        $('#divListDO table>tbody>tr').each(function() {
            _no_reg = $(this).find('td.no_reg').text();
            _tglpanen = $(this).find('td.no_reg').data('tglpanen');
            _no_do = $(this).find('td.no_do').text();
            _no_sj = $(this).find('td.no_sj').text();
            _berat = $(this).find('td.berat').data('berat');
            _jumlah = $(this).find('td.jumlah').data('jumlah');
            _rit = $.trim($(this).find('td.rit').text());
            _nopol = $(this).find('td.nopol').text();
            _sopir = $(this).find('td.sopir').text();
            _id_sopir = $(this).find('td.sopir').data('id_sopir');
            _nik_timpanen = $(this).find('td.tim_panen').data('nik_timpanen');
            _rcn_mulai_panen = $(this).find('td.mulai_panen').data('rcn_mulai_panen');
            _rcn_selesai_panen = $(this).find('td.selesai_panen').data('rcn_selesai_panen');
            _kodeFarm = _no_reg.substring(0, 2);
            _tmp = {
                no_do: _no_do,
                berat: _berat,
                jumlah: _jumlah,
                tgl_panen: _tglpanen,
                kode_farm: _kodeFarm,
                no_reg: _no_reg,
                no_sj: _no_sj,
                nopol: _nopol,
                sopir: _sopir,
                mulai_panen: _rcn_mulai_panen,
                selesai_panen: _rcn_selesai_panen,
                nik_timpanen: _nik_timpanen
            };
            if (!empty(_id_sopir)) {
                _tmp['id_sopir'] = _id_sopir;
            }
            if (!empty(_rit)) {
                _tmp['rit'] = _rit;
            }
            if (_data[_kodeFarm] == undefined) {
                _data[_kodeFarm] = [];
            }
            _data[_kodeFarm].push(_tmp);
        });
        if (!empty(_data)) {
            $.ajax({
                url: 'sinkronisasi/panen_do/simpanDO',
                beforeSend: function() {
                    $('#divListDO').html('Proses simpan data ...... Mohon ditunggu.');
                },
                data: { data: _data },
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        $('#divListDO').html(data.message);
                    }
                },
                async: false
            });
        } else {
            bootbox.alert('Tidak ada yang harus disimpan');
        }

    },
};

$(function() {
    $('input[name=tglPanen]').datepicker({
        dateFormat: 'dd M yy',
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {

            }
        },
    })
})