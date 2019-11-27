$(function() {
    'use strict';
    Permintaan.set_tgl_doc_in_bdy({});
    Permintaan.add_datepicker($('input[name=startDate]'), {
        dateFormat: 'dd M yy',
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {
                $('input[name=endDate]').datepicker('option', 'minDate', date);
            }
        }
    });
    Permintaan.add_datepicker($('input[name=endDate]'), {
        dateFormat: 'dd M yy',
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {
                $('input[name=startDate]').datepicker('option', 'maxDate', date);
            }
        }
    });

    /* dapatkan semua tgl doc awal untuk siklus yang sedang berjalan pada farm yang dipilih */
    $.ajax({
        url: 'permintaan_pakan_v3/permintaan_pakan/get_first_docin',
        data: { perflok: 1 },
        type: 'get',
        dataType: 'json',
        success: function(data) {
            if (data.status) {
                var _tmp = {};
                var _pf;
                for (var i in data.content) {
                    _pf = data.content[i];
                    if (_tmp[_pf['flok_bdy']] == undefined) {
                        _tmp[_pf['flok_bdy']] = _pf['tgl_doc_in'];
                    }
                }
                Permintaan.set_tgl_doc_in_bdy(_tmp);
            }
        }
    });
}());