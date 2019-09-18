(function() {
    'use strict';

    $('input.filter[name="tgl_doc_in"]').datepicker({
        dateFormat: 'dd M yy',
    })

    $("#next").on("click", function() {
        page_number = (page_number + 1);
        getReport(page_number);
    });

    $("#previous").on("click", function() {
        page_number = (page_number - 1);
        getReport(page_number);
    });

    $('input.filter').keyup(function() {
        this.value = this.value.toUpperCase();
        goSearch();
    });

    $('input.filter').change(function() {
        goSearch();
    });

    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        $('#lampirkan-foto').val(label);
    });

    $('a.seru').tooltip();

}());

var search = false;
var page_number = 0;
var total_page = null;


getReport(page_number);

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function kontrol_chekbox(elm) {
    if ($(elm).is(':checked')) {
        $(elm).val('1');
        $('#tanggal-kirim-awal').val('');
        $('#tanggal-kirim-akhir').val('');
    } else {
        $(elm).val('0');
    }
}

function getReport(page_number) {
    if (page_number == 0) {
        $("#previous").prop('disabled', true);
    } else {
        $("#previous").prop('disabled', false);
    }

    if (page_number == (total_page - 1)) {
        $("#next").prop('disabled', true);
    } else {
        $("#next").prop('disabled', false);
    }

    $("#page_number").text(page_number + 1);
    var kode_farm = $('select[name=farm]').val();
    var siklus = $('input.filter[name="siklus"]').val();
    var flock = $('input.filter[name="flock"]').val();
    var kandang = $('input.filter[name="kandang"]').val();
    var koordinator = $('input.filter[name="koordinator"]').val();
    var pengawas = $('input.filter[name="pengawas"]').val();
    var operator = $('input.filter[name="operator"]').val();
    var tgl_doc_in = $('input.filter[name="tgl_doc_in"]').val();
    var periode1 = $('.form_cari').find('select[name=periode1] option:selected').val();
    var periode2 = $('.form_cari').find('select[name=periode2] option:selected').val();

    if (tgl_doc_in) {
        tgl_doc_in = Config._tanggalDb(tgl_doc_in, ' ', '-');
    }

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "kandang/plotting_pelaksana/get_pagination_ack/",
        data: {
            kode_farm: kode_farm,
            siklus: siklus,
            flock: flock,
            kandang: kandang,
            koordinator: koordinator,
            pengawas: pengawas,
            operator: operator,
            tgl_doc_in: tgl_doc_in,
            page_number: page_number,
            search: search,
            periode1: periode1,
            periode2: periode2,
        }
    }).done(function(data) {
        if (data.status) {
            $("#daftar-do-table table tbody").html(data.html);
        }
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}




var plottingPelaksanaAck = {
    kandang: {},
    dataKirim: null,
    ack: function(elm) {
        var _ck = $("#daftar-do-table table tbody").find(':checked');
        if (!_ck.length) {
            bootbox.alert("Mohon memilih baris pelaksana terlebih dahulu");
            return;
        }
        var dataKirim = [];
        var _daftarSiklus = [],
            _daftarFlok = [],
            _farm;
        _farm = $('select[name=farm]').val();
        _ck.each(function() {
            dataKirim.push({ 'kode_siklus': $(this).val(), 'flok': $(this).data('flok') });
            _daftarSiklus.push($(this).data('siklus'));
            _daftarFlok.push($(this).data('flok'));
        })
        bootbox.confirm({
            title: 'Ack Plotting',
            message: '<span style="text-align: center;">Apakah anda yakin ingin melanjutkan proses ack plotting pelaksana farm ' + _farm + ' siklus ' + _daftarSiklus[0] + ' flok ' + _daftarFlok.join(', '),
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
                    plottingPelaksanaAck.executeSave(dataKirim);
                }
            }
        });

    },
    executeSave: function(data) {
        $.ajax({
            url: 'kandang/plotting_pelaksana/ack',
            data: { data: data },
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
                    $("#daftar-do-table table tbody").find(':checked').remove();
                    bootbox.alert(data.message);
                } else {
                    bootbox.alert(data.message);
                }

            }
        });
    },

    loadPlottingFarm: function(e) {
        var kodefarm = $(e).val();
        var url = 'kandang/plotting_pelaksana/main/' + kodefarm;
        $('#main_content').empty().load(url);
    }

}