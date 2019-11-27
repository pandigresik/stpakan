$(function() {
    'use strict';
    permintaanSak.add_datepicker($('input[name=tanggal_awal]'), {
        dateFormat: 'dd M yy',
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {
                $('input[name=tanggal_akhir]').datepicker('option', 'minDate', date);
            }
        }
    });
    permintaanSak.add_datepicker($('input[name=tanggal_akhir]'), {
        dateFormat: 'dd M yy',
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {
                $('input[name=tanggal_awal]').datepicker('option', 'maxDate', date);
            }
        }
    });

}());