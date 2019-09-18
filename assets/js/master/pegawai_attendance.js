var Attendance = {
    search: false,
    page_number: 1,
    total_page: null,

    cari: function(page) {
        var _ini = this;
        var _offset = page == undefined ? 0 : page;
        var _filter = $('.q_search').serializeArray();

        var _url = 'master/pegawai_attendance/lists/' + _offset;
        $.post(_url, { offset: _offset, filter: _filter }, function(data) {
            if (data.status) {
                var _tbody = data.content.data;
                var _pagination = data.content.pagination;
                var _page, _elmLink;
                $('table#pegawai_attendance tbody').html(_tbody);
                $('div.pagination').html(_pagination).promise().done(function() {
                    $('div.pagination').find('a').each(function() {
                        _elmLink = $(this);
                        _page = $(this).data('ci-pagination-page');
                        if (_page != undefined) {
                            $(this).attr('href', '/' + _page);
                            $(this).attr('onclick', 'return Attendance.goPage(this)');
                        }

                    });
                });

            }
        }, 'json');
    },

    goPage: function(_elmLink) {
        this.cari($(_elmLink).data('ci-pagination-page'));
        return false;
    },

    updateInfoPegawai: function(elm) {
        var _detail = $(elm).find('option:selected').data('detail');
        var _form = $(elm).closest('form');
        var _formAttendance = _form.closest('.row').find('form#form_attendance');

        if (empty(_detail)) {
            _form.find('input').val('');
            _formAttendance.find('select,input').val('');
        } else {
            for (var i in _detail) {
                _form.find('input[name=' + i + ']').val(_detail[i]);
            }
            var _nama_pegawai = _detail['NAMA_PEGAWAI'].toUpperCase();
            var _terpilih = _formAttendance.find('option[data-nama="' + _nama_pegawai + '"]');
            if (_terpilih.length) {
                _terpilih.prop('selected', 1);
                _terpilih.closest('select').trigger('change');
            }
        }
    },

    updateInfoAttendance: function(elm) {
        var _detail = $(elm).find('option:selected').data('detail');
        var _form = $(elm).closest('form');
        if (empty(_detail)) {
            _form.find('input').val('');
        } else {
            for (var i in _detail) {
                _form.find('input[name=' + i + ']').val(_detail[i]);
            }
        }
    },

    add: function(elm) {
        var _url = 'master/pegawai_attendance/add';
        $.get(_url, {}, function(html) {
            var _options = {
                title: 'Mapping Attendance',
                className: 'largeWidth',
                message: html,
                buttons: {
                    cancel: {
                        label: 'Batal',
                        callback: function() {}
                    },
                    confirm: {
                        label: 'Simpan',
                        callback: function() {
                            var _kodepegawai = $(box).find('#form_stpakan input[name=KODE_PEGAWAI]').val();
                            var _badgenumber = $(box).find('#form_attendance input[name=KODE_PEGAWAI]').val();
                            if (empty(_kodepegawai)) { toastr.warning('kode pegawai belum dipilih'); return false; }
                            if (empty(_badgenumber)) { toastr.warning('badge number belum dipilih'); return false; }
                            var _data = { 'KODE_PEGAWAI': _kodepegawai, 'BADGE_NUMBER': _badgenumber };
                            box.bind('hidden.bs.modal', function() {
                                Attendance.executeSave(_data);
                            });
                        }
                    },
                },
            };
            var box = bootbox.dialog(_options);
        })
    },
    executeSave: function(data) {
        var _ini = this;
        $.ajax({
            url: 'master/pegawai_attendance/save',
            data: {
                data: data
            },
            type: 'post',
            dataType: 'json',
            beforeSend: function() {
                bootbox.dialog({
                    message: "Sedang proses simpan..."
                });
            },
            async: false,
            success: function(data) {
                bootbox.hideAll();
                if (data.status) {
                    //bootbox.alert(data.message, function() {
                    toastr.success(data.message);
                    Attendance.cari();
                    //});
                } else {
                    toastr.error(data.message);
                }
            }
        });
    },
}

$(document).ready(function() {
    Attendance.cari();
    $('.q_search').change(function() {
        Attendance.cari();
    });
});