(function() {
    'use strict';
    /* jika pilihan farm bukan kosong maka load farm yang dipilih */
    var _farm_terpilih = $('#list_farm select');

    //	Config._setCurrentFarm(_farm_terpilih.val());
    if (!empty(_farm_terpilih.val())) {
        Permintaan.load_farm(_farm_terpilih);
        _farm_terpilih.replaceWith('<label data-kode_siklus="' + _farm_terpilih.find('option:selected').data('kode_siklus') + '">' + _farm_terpilih.find('option:selected').text() + '</label>');
    }

    $('#list_farm select').change(function() {
        Permintaan.load_farm($(this));
    });
    /* ambil data hari libur dari database */
    Permintaan.get_hari_libur();
    /** hapus yang tidak aktif, masih pakai versi lama */
    var _farmVersiBaru = JSON.parse($('#farm_versi_baru').text());

    for (var i in _farmVersiBaru) {
        if (_farmVersiBaru[i] == 0) {
            _farm_terpilih.find('option[value=' + i + ']').remove();
        }
    }
}());