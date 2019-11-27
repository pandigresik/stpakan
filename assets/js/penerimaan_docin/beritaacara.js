(function() {
    'use strict';
    /* jika pilihan farm bukan kosong maka load farm yang dipilih */
    var _farm_terpilih = $('#list_farm select');

    if (!empty(_farm_terpilih.val())) {
        _farm_terpilih.replaceWith('<label data-kode_siklus="' + _farm_terpilih.find('option:selected').data('kode_siklus') + '">' + _farm_terpilih.find('option:selected').text() + '</label>');
    }
    var _disabledate = false;
    if ($(':checkbox[name=tindaklanjut]').is(':checked')) {
        _disabledate = true;
    }

    $(':checkbox[name=tindaklanjut]').click(function() {
        var _f = $(this).closest('form');

        if ($(this).is(':checked')) {
            _f.next('form').find('select').prop('disabled', true);
        } else {
            _f.next('form').find('select').prop('disabled', false);
        }
    });
    $('span.btn_cari').click();
}());