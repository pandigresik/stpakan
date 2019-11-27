var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_uom = "";

function pilih_uom(e) {
    var id_hand_pallet = $(e).find('td.id_hand_pallet').text();
    var tara = $(e).find('td.tara').text();
    $('#inp_siklus').attr('data-id_hand_pallet', id_hand_pallet);
    $('#inp_siklus').val(tara + ' - ' + id_hand_pallet);
    $('#modal_master_uom').modal("hide");
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

    id_hand_pallet = $('#q_id_hand_pallet').val();
    tanggal_penimbangan = $('#q_tanggal_penimbangan').val();
    var data_date = $.datepicker.regional.id.monthNamesShort;
    var tanggal = '';
    if (tanggal_penimbangan) {
        tanggal_penimbangan = tanggal_penimbangan.split(' ');
        tanggal = tanggal_penimbangan[2] + '-' + (data_date.indexOf(tanggal_penimbangan[1]) + 1) + '-' + tanggal_penimbangan[0];
    }
    var hand_pallet_aktif = $('#hand_pallet_aktif').val();
    var hand_pallet_tidak_aktif = $('#hand_pallet_tidak_aktif').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/hand_pallet/get_pagination/",
        data: {
            id_hand_pallet: id_hand_pallet,
            tanggal_penimbangan: tanggal,
            hand_pallet_aktif: hand_pallet_aktif,
            hand_pallet_tidak_aktif: hand_pallet_tidak_aktif,
            page_number: page_number,
            search: search
        }
    }).done(function(data) {
        $("tbody", "#master-hand-pallet").html("");

        $.each(data, function(key, data) {
            var number = data.BRT_BERSIH_NEW;
            var _berat = (!number) ? '<input data-toggle="tooltip" data-placement="right" title=""  onfocus="Home.getDataTimbang(this)"  readonly onmouseover="view_tooltip(this)" class="text-center input_tara" name="input_tara" style="width:100px;">' : number; //number_format(number, 3, ',', '.');
            var _keterangan = (data.KETERANGAN) ? '' : '<input onkeyup="kontrol_keterangan(this);" class="text-center input_keterangan hide" name="input_keterangan" value="by_system" style="width:150px;" >';
            var _hide_aksi_reset = (!data.KETERANGAN) ? 'hide' : '';
            var _hide_aksi_set = (data.KETERANGAN) ? 'hide' : '';
            var _hide_aksi_edit = (data.STATUS_PALLET == 'C') ? 'hide' : '';
            var _tgl_timbang = (!number) ? '' : data.TGL_TIMBANG;
            var tgl_timbang = '';
            if (number && _tgl_timbang) {
                _tgl_timbang = _tgl_timbang.split('-');
                tgl_timbang = _tgl_timbang[2] + '-' + data_date[parseInt(_tgl_timbang[1] - 1)] + '-' + _tgl_timbang[0];
            }
            var _function = (number) ? "view_history(this)" : "";
            var ischecked = (data._DEFAULT == 1) ? "checked" : "";
            var _html = '<tr ondblclick="' + _function + '">';
            _html += '<td data-default="' + data._DEFAULT + '" class="id_hand_pallet" align="center"><input onclick="ubah_default(this)" class="' + _hide_aksi_edit + '" type="radio" name="default" style="margin-right: 10%;" ' + ischecked + '>' + data.KODE_HAND_PALLET + '</td>';
            _html += '<td class="tanggal" align="center" data-tanggal="' + tgl_timbang + '" data-status="' + data.STATUS_PALLET + '" data-tanggal-db="' + data.TGL_TIMBANG + '">' + tgl_timbang + '</td>';
            _html += '<td class="tara" align="center"><span>' + _berat + '</span>';
            _html += '<span class="tooltips-base tooltips-default hide">';
            _html += '<span class="tooltips-content"></span>';
            _html += '<span class="tooltips-arrow-right tooltips-arrow" style="">';
            _html += '<span class="tooltips-arrow-border" style="margin-left: -3px; border-color: rgb(0, 0, 0);">';
            _html += '</span><span style="border-color:rgb(76, 76, 76);"></span>';
            _html += '</span>';
            _html += '</span>';
            _html += '</td>';
            _html += '<td class="keterangan" align="center">';
            _html += '<div class="form-inline"><span style="width: 160px;" class="col-md-1">' + _keterangan + '</span>';
            _html += '<span style="width: 115px;" class="col-md-1 ' + _hide_aksi_set + ' ' + _hide_aksi_reset + ' reset"><i class="btn-glyphicon glyphicon glyphicon-remove" onclick="reset(this)"></i></span>';
            _html += '<span style="width: 115px;" class="col-md-1 ' + _hide_aksi_set + ' set" data-aksi="I"><i class="btn-glyphicon glyphicon glyphicon-ok" onclick="set_berat_hand_pallet(this)"></i></span>';
            _html += '<span style="width: 115px;" class="col-md-1 ' + _hide_aksi_reset + ' ' + _hide_aksi_edit + ' edit"><i class="btn-glyphicon glyphicon glyphicon-pencil" onclick="edit_berat_hand_pallet(this)"></i></span>';
            _html += '</td></td></tr>';
            $("tbody", "#master-hand-pallet").append(_html);

        });

        var tabel_riwayat = $('table#master-hand-pallet');
        if (tabel_riwayat.length > 0) {
            tabel_riwayat.scrollabletable({
                'max_height_scrollable': 400
            });
        }

        /*
        $("input.input_keterangan").spinner({
            min : 1
        });

        */
        $('td.tara input').numeric({
            allowPlus: false,
            allowMinus: false,
            allowThouSep: false,
            allowDecSep: true
        });

        var tr = $('#master-hand-pallet tbody tr:first');
        tr.find('td.tara input.input_tara').focus().select();


    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function master_uom() {
    $.ajax({
        type: 'POST',
        url: "master/hand_pallet/get_master_uom/",
        data: {}
    }).done(function(data) {
        $('#modal_master_uom .modal-body').html(data);
        $('#modal_master_uom').modal('show');
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}


$(document).ready(function() {
    goSearch();


    $('#q_tanggal_penimbangan').datepicker({
        dateFormat: 'dd M yy'
    });
});

$('input[type="checkbox"]').click(function() {
    goSearch();
});

$('.field_input').keyup(function() {
    checkInput();
});

$('#master-hand-pallet').on('click', 'tr', function() {
    selected_uom = $(this).find('td:nth-child(1)').text();
});

$('#master-hand-pallet > tbody').on('', 'tr', function() {
    selected_uom = $(this).attr('data-id');
    form_mode = "ubah";
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/hand_pallet/get_uom/",
        data: {
            id_hand_pallet: selected_uom,
        }
    }).done(function(data) {
        $('#inp_id_hand_pallet').val(data.UOM);
        $('#inp_tara').val(data.tara);
        $('#inp_siklus').val(data.tara_BASE_UOM + ' - ' + data.UOM_BASE_UOM);
        $('#inp_siklus').attr('data-id_hand_pallet', data.UOM_BASE_UOM);
        $('#inp_konversi').val(data.KONVERSI);

        $('#inp_id_hand_pallet').attr("readonly", true);
        $('#inp_siklus').attr("readonly", true);

        $('#btnSimpan').hide();
        $('#btnUbah').show();
        //$('#btnUbah').removeClass('disabled');

        $('#modal_uom').modal("show");
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
});

$("#btnTambah").click(function() {
    resetInput();
    form_mode = "tambah";

    $('#inp_id_hand_pallet').attr("readonly", false);
    $('#inp_siklus').attr("readonly", true);
    $('#btnSimpan').show();
    $('#btnUbah').hide();

    $('#modal_uom').modal("show");
});

$("#btnBatal").click(function() {
    $('#modal_uom').modal("hide");
    resetInput();
});

$("#btnKembali").click(function() {
    $('#modal_master_uom').modal("hide");
});

$("#btnSimpan").click(function() {
    var passed = true;
    id_hand_pallet = $('#inp_id_hand_pallet').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').attr('data-id_hand_pallet');
    konversi = $('#inp_konversi').val();

    if (id_hand_pallet == siklus)
        passed = false;

    if (!passed) {
        bootbox.alert("Kolom id_hand_pallet Dasar tidak boleh sama dengan kolom id_hand_pallet.");
        return false;
    }

    if (id_hand_pallet && tara && konversi) {

        var data = "Apakah Anda yakin akan Menyimpan data ID Hand Pallet (UOM)?";
        var box = bootbox.dialog({
            message: data,
            buttons: {
                danger: {
                    label: "Tidak",
                    className: "btn-default",
                    callback: function() {
                        return true;
                    }
                },
                success: {
                    label: "Ya",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "master/hand_pallet/add_uom/",
                            data: {
                                id_hand_pallet: id_hand_pallet,
                                tara: tara,
                                siklus: siklus,
                                konversi: konversi
                            }
                        }).done(function(data) {
                            if (data.result == "success") {
                                notificationBox("Penyimpanan data id_hand_pallet " + id_hand_pallet + " berhasil dilakukan.");
                                $('#modal_uom').modal("hide");
                                resetInput();

                                goSearch();
                            } else {
                                if (data.check == "failed")
                                    notificationBox("id_hand_pallet " + id_hand_pallet + " sudah terdaftar.");
                                else
                                    notificationBox("Penyimpanan data id_hand_pallet " + id_hand_pallet + " gagal dilakukan");
                            }
                        }).fail(function(reason) {
                            console.info(reason);
                        }).then(function(data) {});
                    }
                }
            }
        });
    } else {
        notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
    }
});

$("#btnUbah").click(function() {
    var passed = true;

    id_hand_pallet = $('#inp_id_hand_pallet').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').attr('data-id_hand_pallet');
    konversi = $('#inp_konversi').val();

    if (id_hand_pallet == siklus)
        passed = false;

    if (!passed) {
        bootbox.alert("Kolom id_hand_pallet Dasar tidak boleh sama dengan kolom id_hand_pallet.");
        return false;
    }

    if (id_hand_pallet && tara && konversi) {

        var data = "Apakah Anda yakin akan Mengubah data id_hand_pallet ini?";
        var box = bootbox.dialog({
            message: data,
            buttons: {
                danger: {
                    label: "Tidak",
                    className: "btn-default",
                    callback: function() {
                        return true;
                    }
                },
                success: {
                    label: "Ya",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "master/hand_pallet/update_uom/",
                            data: {
                                id_hand_pallet: id_hand_pallet,
                                tara: tara,
                                siklus: siklus,
                                konversi: konversi
                            }
                        }).done(function(data) {
                            if (data.result == "success") {
                                notificationBox("Perubahan data id_hand_pallet " + id_hand_pallet + " berhasil dilakukan.");

                                $('#modal_uom').modal("hide");
                                resetInput();

                                goSearch();
                            } else {
                                if (data.check == "failed")
                                    notificationBox("id_hand_pallet " + id_hand_pallet + " sudah terdaftar.");
                                else
                                    notificationBox("Perubahan data id_hand_pallet " + id_hand_pallet + " gagal dilakukan");
                            }
                        }).fail(function(reason) {
                            console.info(reason);
                        }).then(function(data) {});
                    }
                }
            }
        });
    } else {
        notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
    }
});

/*
 * FUNCTION
 */

function resetInput() {
    $('#inp_id_hand_pallet').val('');
    $('#inp_tara').val('');
    $('#inp_siklus').val('');
    $('#inp_siklus').attr('data-id_hand_pallet', '');
    $('#inp_konversi').val('');
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function checkInput() {

    id_hand_pallet = $('#inp_id_hand_pallet').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').val();
    konversi = $('#inp_konversi').val();
    /*
        if (id_hand_pallet != "" && tara != "" && konversi != "") {
            if (form_mode == "tambah")
                $('#btnSimpan').removeClass("disabled");

            if (form_mode == "ubah")
                $('#btnUbah').removeClass("disabled");
        } else {
            if (form_mode == "tambah")
                $('#btnSimpan').addClass("disabled");

            if (form_mode == "ubah")
                $('#btnUbah').addClass("disabled");
        }*/

}

function notificationBox(message) {
    bootbox.dialog({
        message: message,
        buttons: {
            success: {
                label: "OK",
                className: "btn-primary",
                callback: function() {
                    return true;
                }
            }
        }
    });
}

function set_berat_hand_pallet(elm) {
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var _aksi = tr.find('td.keterangan span.set').attr('data-aksi');
    var id_hand_pallet = tr.find('td.id_hand_pallet').text();
    var _default = tr.find('td.id_hand_pallet input').is(':checked');
    var tara = tr.find('td.tara input.input_tara').val();
    var data_tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var keterangan = tr.find('td.keterangan input.input_keterangan').val();
    var tgl_timbang = (data_tara) ? tr.find('td.tanggal').attr('data-tanggal-db') : '';
    var str_default = (_default) ? " sebagai deafult" : "";
    var msg = (tgl_timbang) ? "" : "Apakah anda yakin akan menyimpan data hand pallet baru" + str_default + "?";
    var kode_farm = $('#kode_farm').val();
    var params = {
        'id_hand_pallet': id_hand_pallet,
        'tara': tara,
        'keterangan': keterangan,
        'tgl_timbang': tgl_timbang,
        'msg': msg,
        '_default': _default,
        'kode_farm': kode_farm
    }
    if (tara && keterangan) {
        if (_aksi == 'U') {
            dialog_set_berat_hand_pallet('U', params);
        } else {
            dialog_set_berat_hand_pallet('I', params);
        }
    } else {

        toastr.warning('Mohon melakukan pengisian secara lengkap.', 'Informasi');
        var i = 0;
        $.each(tr.find('input'), function() {
            var _value = $(this).val();
            //$(this).removeClass('red_border');
            if (!_value) {
                //$(this).focus().select();.addClass('red_border');
                if (i == 0) {
                    $(this).focus().select();
                }

                i++;
            }
        });

    }
}

function dialog_set_berat_hand_pallet(aksi, params) {
    var data = (aksi == 'U') ? "Apakah Anda yakin akan melanjutkan perubahan?" : "Apakah Anda yakin akan menyimpan penimbangan hand pallet?";
    var msg_sukses = (aksi == 'U') ? "Data hand pallet berhasil diubah." : "Penimbangan hand pallet baru berhasil disimpan.";
    var msg_gagal = (aksi == 'U') ? "Data hand pallet gagal diubah." : "Penimbangan hand pallet baru gagal disimpan.";
    var msg = (params.msg) ? params.msg : data;
    var box = bootbox.dialog({
        message: msg,
        buttons: {
            danger: {
                label: "Tidak",
                className: "btn-default",
                callback: function() {
                    return true;
                }
            },
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {

                    simpan_berat_hand_pallet(params, function(result) {

                        if (result == 1) {
                            goSearch();
                            toastr.success(msg_sukses, 'Informasi');

                            return true;
                        } else if (result == 2) {
                            toastr.warning('Tidak bisa ubah data hand pallet pada hari H.', 'Informasi');

                            return false;
                        } else {
                            toastr.error(msg_gagal, 'Informasi');
                            return false;
                        }
                    });
                }
            }
        }
    });
}

function reset(elm) {
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var id_hand_pallet = tr.find('td.id_hand_pallet').text();
    var tanggal = tr.find('td.tanggal').attr('data-tanggal');
    var tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var tara_span_last = tr.find('td.tara span.tooltips-base').html();
    var keterangan = tr.find('td.keterangan input.input_keterangan').attr('data-keterangan');
    //tara = tara.replace('.',',');
    //tara = tara.replace(',','.');
    tr.find('td.tanggal').html(tanggal);
    var _html_tara = '<span>' + tara + '</span>';
    _html_tara += '<span class="tooltips-base tooltips-default hide">' + tara_span_last + '</span>';

    tr.find('td.tara').html(_html_tara);
    var _html_keterangan = '<div class="form-inline"><span style="width:160px" class="col-md-1">' + keterangan + '</span>';
    _html_keterangan += '<span style="width:115px" class="col-md-1 reset">' + tr.find('td.keterangan span.reset').html() + '</span>';
    _html_keterangan += '<span style="width:115px" class="col-md-1 set">' + tr.find('td.keterangan span.set').html() + '</span>';
    _html_keterangan += '<span style="width:115px" class="col-md-1 edit">' + tr.find('td.keterangan span.edit').html() + '</span></div>';
    tr.find('td.keterangan').html(_html_keterangan);
    tr.find('td.keterangan span.reset, td.keterangan span.set').addClass('hide');
    tr.find('td.keterangan span.edit').removeClass('hide');

}

function edit_berat_hand_pallet(elm) {
    var tr = $(elm).closest('tr');
    var _aksi_reset = tr.find('td.keterangan span.reset').hasClass('hide');
    var _aksi_set = tr.find('td.keterangan span.set').hasClass('hide');
    var id_hand_pallet = tr.find('td.id_hand_pallet').text();
    var tara = tr.find('td.tara span:first').text();
    var tara_span_last = tr.find('td.tara span.tooltips-base').html();
    var keterangan = tr.find('td.keterangan').text();
    if (_aksi_reset && _aksi_set) {
        //tara = tara.replace(/[.]/gi, '');
        //tara = tara.replace(',','.');
        tr.find('td.tanggal').text('');
        var _html_tara = '<input data-toggle="tooltip" data-placement="right" title="" onmouseover="view_tooltip(this)" data-tara="' + tara + '" class="text-center input_tara" name="input_tara" style="width:100px;" onfocus="Home.getDataTimbang(this)"  readonly>';
        _html_tara += '<span class="tooltips-base tooltips-default hide">' + tara_span_last + '</span>';
        tr.find('td.tara').html(_html_tara);
        tr.find('td.tara span.tooltips-content').text(tara);
        var _html_keterangan = '<div class="form-inline"><span style="width: 160px;" class="col-md-1 hide"><input onkeyup="kontrol_keterangan(this);" data-keterangan="' + keterangan + '" class="text-center input_keterangan" name="input_keterangan" value="by_system" style="width:150px;"></span>';
        _html_keterangan += '<span style="width: 35px;" class="col-md-1 reset">' + tr.find('td.keterangan span.reset').html() + '</span>';
        _html_keterangan += '<span style="width: 35px;" class="col-md-1 set" data-aksi="U">' + tr.find('td.keterangan span.set').html() + '</span>';
        _html_keterangan += '<span style="width: 35px;" class="col-md-1 edit">' + tr.find('td.keterangan span.edit').html() + '</span></div>';
        tr.find('td.keterangan').html(_html_keterangan);
        tr.find('td.keterangan span.reset, td.keterangan span.set').removeClass('hide');
        tr.find('td.keterangan span.edit').addClass('hide');


        tr.find('td.tara input.input_tara').focus().select();
    }


    $('td.tara input').numeric({
        allowPlus: false,
        allowMinus: false,
        allowThouSep: false,
        allowDecSep: true
    });
}

function simpan_berat_hand_pallet(params, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/hand_pallet/simpan_berat_hand_pallet/",
        data: {
            params: params
        }
    }).done(function(result) {
        callback(result);
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function view_tooltip(elm) {
    /*
    $('td.tara input').removeClass('input_tara');
    var tr = $(elm).closest('tr');
    tr.find('td.tara input').addClass('input_tara');
    var _aksi_reset = tr.find('td.keterangan span.reset').hasClass('hide');
    var id_hand_pallet = tr.find('td.id_hand_pallet').text();
    var siklus = tr.find('td.siklus').attr('data-kode-siklus');
    var tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var new_tara = $(elm).val();
    tr.find('td.tara input.input_tara').attr('title', tara);
    var title = tr.find('td.tara input.input_tara').attr('title');
    var keterangan = tr.find('td.keterangan input.input_keterangan').val();
    var failed = 1;
    if(tara && new_tara){
        if(parseFloat(tara) == parseFloat(new_tara)){
            failed = 0;
        }
    }*/
    /*
    tr.find('td.tara input.input_tara').tooltipster({
        animation: 'fade',
        delay: 500,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'custom',
        hideOnClick: true,
        position: 'right'
    });
    */
    /*
    if(title && failed == 1){
        //tr.find('td.tara input.input_tara').tooltipster('show');
        tr.find('td.tara span.tooltips-base').removeClass('hide').fadeIn(3000);
    }
    else{
        //tr.find('td.tara input.input_tara').tooltipster('hide');
        tr.find('td.tara span.tooltips-base').addClass('hide').fadeOut(3000);
    }
    */

}

function kontrol_keterangan(elm) {
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var keterangan = $(elm).val();
    /*
    tr.find('td.tara input.input_tara').tooltipster({
        animation: 'fade',
        delay: 500,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'custom',
        hideOnClick: true,
        position: 'right'
    });
    */
    if (keterangan) {
        //tr.find('td.tara input.input_tara').tooltipster('hide');
        tr.find('td.tara span.tooltips-base').addClass('hide').fadeOut(3000);
    }
}

function set_class_tara(elm) {
    var tr = $(elm).closest('tr');
    if (!tr.find('td.tara input').hasClass('input_tara')) {
        tr.find('td.tara input').addClass('input_tara');
    }
}

function kontrol_checkbox(elm) {
    var _val = 0;
    if ($(elm).is(':checked')) {
        _val = 1;
    }
    $(elm).val(_val);
}

function view_history(elm) {
    $('#master-hand-pallet tbody tr').removeClass('double_click');
    $(elm).addClass('double_click');
    var kode_hand_pallet = $(elm).find('td.id_hand_pallet').text();
    $.ajax({
        type: 'POST',
        dataType: 'html',
        url: "master/hand_pallet/history_hand_pallet/",
        data: {
            kode_hand_pallet: kode_hand_pallet
        }
    }).done(function(data) {
        dialog_history(data);
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function dialog_history(html) {

    var data = html;
    var box = bootbox.dialog({
        message: data,
        title: 'Riwayat Master Hand Pallet',
        className: "medium-large"
    });
}

function dialog_status_hand_pallet(elm) {
    var kode_hand_pallet = $(elm).closest('tr').find('td.id_hand_pallet').text();
    var tanggal_penimbangan = $(elm).closest('tr').find('td.tanggal').attr('data-tanggal');
    var status_hand_pallet = $(elm).val();
    var label = (status_hand_pallet == 'N') ? "mengaktifkan" : "menonaktifkan";
    var data = "Apakah anda yakin akan " + label + " ID Hand Pallet : " + kode_hand_pallet + " ?";
    var kembali_status = 0;
    var aktifkan = 0;
    var box = bootbox.dialog({
        message: data,
        buttons: {
            danger: {
                label: "Tidak",
                className: "btn-default",
                callback: function() {
                    kembali_status = 1;
                    return true;
                }
            },
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {
                    if (status_hand_pallet == 'N') {
                        aktifkan = 1;
                        $('div.bootbox').modal('hide');

                    } else {
                        dialog_keterangan(kode_hand_pallet, status_hand_pallet, tanggal_penimbangan);
                    }
                    return true;
                }
            },
        },
    });

    box.bind('hidden.bs.modal', function() {
        if (kembali_status == 1) {
            var status = $(elm).attr('data-status');
            $(elm).val(status);

        }
        if (aktifkan == 1) {

            var tr = $('tr.double_click');
            var table = $('tr.double_click').closest('table');
            tr.find('td.keterangan span.edit i').click();
            tr.find('td.keterangan span.reset').addClass('hide');
            tr.find('td.keterangan span.set').attr('data-aksi', 'I');
            table.find('tbody').prepend(tr);
            tr.find('td.tara input').focus().select();
        }
    });

}

function dialog_keterangan(kode_hand_pallet, status_hand_pallet, tanggal_penimbangan) {
    var data = "<div class='col-md-12 form-group'>Keterangan perubahan status hand pallet</div>";
    data += '<div class="col-md-12 form-group"><textarea onkeyup="maks_karakter(this)" class="form-control" id="keterangan" name="keterangan" type="text"></textarea></div>';
    data += '<div class="col-md-12 form-group text-center"><button id="btn-simpan" data-kode-hand_pallet="' + kode_hand_pallet + '" data-status="' + status_hand_pallet + '" data-tanggal="' + tanggal_penimbangan + '" onclick="ubah_status_hand_pallet(this)" class="btn btn-primary" type="button" disabled>Simpan</button></div>';
    var box = bootbox.dialog({
        message: data,
    });

    box.bind('shown.bs.modal', function() {
        box.find("textarea#keterangan").focus();
    });
}

function ubah_status_hand_pallet(elm) {
    var kode_hand_pallet = $(elm).attr('data-kode-hand_pallet');
    var status_hand_pallet = $(elm).attr('data-status');
    var tanggal_penimbangan = $(elm).attr('data-tanggal');
    var keterangan = $('#keterangan').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/hand_pallet/ubah_status_hand_pallet/",
        data: {
            kode_hand_pallet: kode_hand_pallet,
            status_hand_pallet: status_hand_pallet,
            keterangan: keterangan,
            tanggal_penimbangan: tanggal_penimbangan
        }
    }).done(function(data) {
        if (data.status_pallet == status_hand_pallet) {

            goSearch();
            toastr.success('Data hand pallet telah dinonaktifkan.', 'Informasi');
            $('div.bootbox').modal('hide');
        } else {
            toastr.error('Data hand pallet gagal dinonaktifkan.', 'Informasi');
        }
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function maks_karakter(elm) {
    var result = 0;
    var keterangan = $(elm).val();
    keterangan = keterangan.replace(/\s/gi, '');
    $('#btn-simpan').attr('disabled', true);
    if (keterangan.length >= 10) {
        $('#btn-simpan').removeAttr('disabled');
    }
}

function set_tanggal(elm) {


    $('#q_tanggal_penimbangan').datepicker({
        dateFormat: 'dd M yy'
    });
}

function ubah_default(elm) {
    var _default = $(elm).parent().attr('data-default');
    var tgl_timbang = $(elm).closest('tr').find('td.tanggal').attr('data-tanggal-db');
    if (_default != 1 && tgl_timbang) {
        dialog_default();
    }
}

function dialog_default() {
    var data = "Apakah anda yakin akan mengubah hand pallet menjadi default ?";
    var box = bootbox.dialog({
        message: data,
        buttons: {
            danger: {
                label: "Tidak",
                className: "btn-default",
                callback: function() {
                    return true;
                }
            },
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {
                    ubah_default_hand_pallet(function(result) {
                        if (result._default == 1) {
                            goSearch();
                            toastr.success('Default hand pallet berhasil.', 'Informasi');
                            return true;
                        } else {

                            toastr.error('Default hand pallet gagal.', 'Informasi');
                            return false;
                        }
                    });
                }
            },
        },
    });

}

function ubah_default_hand_pallet(callback) {
    var elm = $('input[type="radio"]:checked');
    var tr = elm.closest('tr');
    var kode_hand_pallet = tr.find('td.id_hand_pallet').text();
    var status_hand_pallet = tr.find('td.tanggal').attr('data-status');
    var tanggal_penimbangan = tr.find('td.tanggal').attr('data-tanggal-db');
    var _default = 1;
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/hand_pallet/ubah_default_hand_pallet/",
        data: {
            kode_hand_pallet: kode_hand_pallet,
            status_hand_pallet: status_hand_pallet,
            tanggal_penimbangan: tanggal_penimbangan,
            _default: _default
        }
    }).done(function(data) {
        callback(data);
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function generate_kode_hand_pallet(callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/hand_pallet/generate_kode_hand_pallet/",
        data: {}
    }).done(function(data) {
        callback(data);
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function baru() {
    var kode_hand_pallet = '';
    generate_kode_hand_pallet(function(data) {
        var obj = $('#master-hand-pallet tbody tr td.id_hand_pallet:contains("' + data.kode_hand_pallet + '")');
        if (obj.length > 0) {
            toastr.warning('Hand pallet ' + data.kode_hand_pallet + ' belum dilakukan penimbangan.', 'Informasi');
        } else {
            var tr = '<tr>';
            tr += '<td align="center" class="id_hand_pallet" data-default="0">';
            tr += '<input type="radio" style="margin-right: 10%;" name="default" class="" onclick="ubah_default(this)">' + data.kode_hand_pallet + '</td>';
            tr += '<td align="center" data-tanggal-db="" data-status="N" data-tanggal="" class="tanggal"></td>';
            tr += '<td align="center" class="tara"><input style="width:100px;" name="input_tara" class="text-center input_tara" data-tara="" onmouseover="view_tooltip(this)"  onfocus="Home.getDataTimbang(this)"  readonly title="" data-placement="right" data-toggle="tooltip"><span class="tooltips-base tooltips-default hide"><span class="tooltips-content"></span><span style="" class="tooltips-arrow-right tooltips-arrow"><span style="margin-left: -3px; border-color: rgb(0, 0, 0);" class="tooltips-arrow-border"></span><span style="border-color:rgb(76, 76, 76);"></span></span></span></td>';
            tr += '<td align="center" class="keterangan"><div class="form-inline">';
            tr += '<span class="col-md-1" style="width: 160px;">';
            tr += '<input style="width:150px;" name="input_keterangan" class="text-center input_keterangan hide" value="by_system" data-keterangan="" onkeyup="kontrol_keterangan(this);"></span>';
            tr += '<span class="col-md-1 reset" style="width: 35px;"><i onclick="reset(this)" class="btn-glyphicon glyphicon glyphicon-remove"></i></span>';
            tr += '<span data-aksi="I" class="col-md-1 set" style="width: 35px;"><i onclick="set_berat_hand_pallet(this)" class="btn-glyphicon glyphicon glyphicon-ok"></i></span>';
            tr += '<span class="col-md-1 edit hide" style="width: 35px;"><i onclick="edit_berat_hand_pallet(this)" class="btn-glyphicon glyphicon glyphicon-pencil"></i></span></div>';
            tr += '</td></tr>';
            $('table#master-hand-pallet tbody').prepend(tr);
            $('td.tara input').numeric({
                allowPlus: false,
                allowMinus: false,
                allowThouSep: false,
                allowDecSep: true
            });
        }
    });
}

function get_berat_timbang(elm) {
    $(elm).removeAttr('readonly');
    //console.log('OK');
    setTimeout(function() {
        var berat = $(elm).val();
        $(elm).val(berat);
        $(elm).attr('readonly', true);
    }, 0);
}

function replace_timbang(elm) {
    //console.log($(elm).val());
    $(elm).select().focus().val($(elm).val());

}

function selected(elm) {
    $(elm).select().focus();
}