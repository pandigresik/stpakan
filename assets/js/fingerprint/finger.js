var Finger = {
    maxFingerReload: 10,
    countFinger: 0,
    verifikasi: function(elm) {
        var _url = 'fingerprint/verification';
        var _kode_user = $(elm).data('kode_pegawai');
        var _ini = this;
        $.post(_url, { kode_user: _kode_user }, function(data) {
            bootbox.alert(data.message, function() {
                if (_ini.countFinger >= _ini.maxFingerReload) {
                    $('#main_content').load('fingerprint');
                }
                _ini.countFinger++;
            });
        }, 'json');

    }
}