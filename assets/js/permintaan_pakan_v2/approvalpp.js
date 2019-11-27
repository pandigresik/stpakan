$(function() {
    'use strict';
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
    $('input:checkbox[name=tindak_lanjut]').click(function() {
        var _status = $(this).is(':checked');
        /* jika true maka disable datepicker, jik false enable */
        if (_status) {
            $('input[name$=Date]').datepicker('option', 'disabled', 1);
            $('select,input:not(:checkbox)').prop('disabled', 1);
            $('span[name=btnCari]').addClass('disabled');
            Permintaan.list_pp_cari($(this));
        } else {
            $('input[name$=Date]').datepicker('option', 'disabled', 0);
            $('select,input:not(:checkbox)').prop('disabled', 0);
            $('span[name=btnCari]').removeClass('disabled');
        }
    });

    /* list flok akan berubah sesuai dengan farm yang dipilih */
    $('div[name=divFarm]>select').change(function() {
        var ini = $(this);
        var jmlflok = ini.find('option:selected').data('jmlflok');
        var flokElm = $('select[name=flok]');
        flokElm.empty();
        if (!empty(jmlflok)) {
            var i = 1;
            var opt = [];
            while (i <= jmlflok) {
                opt.push('<option>' + i + '</option>');
                i++;
            }
            $(opt.join('')).appendTo(flokElm);
        }

    });

    Permintaan.list_pp_cari($(':checked:first'));
    $('select,input:not(:checkbox)').prop('disabled', 1);
    $(window).resize(function() {
        //Permintaan.showHideFloatingArrow();
    });

    //Permintaan.showHideFloatingArrow();
    $('.tu-float-btn').click(function() {
        var w = 0;
        if ($(this).hasClass('tu-table-next')) {
            w = $('#daftar_pp_kafarm').find('table').width();
        }

        $('#daftar_pp_kafarm').scrollLeft(w);
    });
}());