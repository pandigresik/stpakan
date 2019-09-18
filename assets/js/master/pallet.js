var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_uom = "";

function pilih_uom(e) {
    var id_pallet = $(e).find('td.id_pallet').text();
    var tara = $(e).find('td.tara').text();
    $('#inp_siklus').attr('data-id_pallet', id_pallet);
    $('#inp_siklus').val(tara + ' - ' + id_pallet);
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

    id_pallet = $('#q_id_pallet').val();
    tanggal_penimbangan = $('#q_tanggal_penimbangan').val();
    var data_date = $.datepicker.regional.id.monthNamesShort;
    var tanggal = '';
    if (tanggal_penimbangan) {
        tanggal_penimbangan = tanggal_penimbangan.split(' ');
        tanggal = tanggal_penimbangan[2] + '-' + (data_date.indexOf(tanggal_penimbangan[1]) + 1) + '-' + tanggal_penimbangan[0];
    }
    var pallet_aktif = $('#pallet_aktif').val();
    var pallet_tidak_aktif = $('#pallet_tidak_aktif').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/pallet/get_pagination/",
        data: {
            id_pallet: id_pallet,
            tanggal_penimbangan: tanggal,
            pallet_aktif: pallet_aktif,
            pallet_tidak_aktif: pallet_tidak_aktif,
            page_number: page_number,
            search: search
        }
    }).done(function(data) {
        $("#master-pallet").remove();
        var _tt = '<table id="master-pallet" class="table table-bordered table-striped">' +
            '<thead>' +
            '<tr>' +
            '<th class="text-center id_pallet">ID Pallet</th>' +
            '<th class="text-center tanggal_penimbangan">Tanggal Penimbangan</th>' +
            '<th class="text-center tara">Tara (kg)</th>' +
            '<th class="text-center keterangan">Aksi</th>' +
            '<th class="text-center cetak_barcode">Cetak Id Barcode</th>' +
            '</tr>' +
            '</thead>' +
            '<tbody>' +
            '</tbody>' +
            '</table>';
        _tt = $(_tt);
        _tt.insertAfter('#search_table');
        $.each(data, function(key, data) {
            var number = data.BRT_BERSIH_NEW;
            var _berat = (!number) ? '<input data-toggle="tooltip" data-placement="right" title="" onfocus="Home.getDataTimbang(this)" readonly onmouseover="view_tooltip(this)" class="text-center input_tara" name="input_tara" style="width:100px;">' : number; //number_format(number, 3, ',', '.');
            var _keterangan = (data.KETERANGAN) ? '' : '<input onkeyup="kontrol_keterangan(this);" class="text-center input_keterangan hide" name="input_keterangan" style="width:150px;" value="by_system">';
            var _hide_aksi_reset = (!data.KETERANGAN) ? 'hide' : '';
            var _hide_aksi_set = (data.KETERANGAN) ? 'hide' : '';
            var _hide_aksi_edit = (data.STATUS_PALLET == 'C') ? 'hide' : '';
            var _tgl_timbang = (!number) ? '' : data.TGL_TIMBANG;
            var tgl_timbang = '';
            var _disabled = '';
            if (number && _tgl_timbang) {
                _tgl_timbang = _tgl_timbang.split('-');
                tgl_timbang = _tgl_timbang[2] + '-' + data_date[parseInt(_tgl_timbang[1] - 1)] + '-' + _tgl_timbang[0];
            } else {
                _disabled = 'disabled';
            }
            var _function = (number) ? "view_history(this)" : "";
            var _html = '<tr ondblclick="' + _function + '">';
            _html += '<td class="id_pallet" align="center">' + data.KODE_PALLET + '</td>';
            _html += '<td class="tanggal" align="center" data-tanggal="' + tgl_timbang + '" data-tanggal-db="' + data.TGL_TIMBANG + '">' + tgl_timbang + '</td>';
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
            _html += '<div class="form-inline"><span style="width: 150px;" class="col-md-1">' + _keterangan + '</span>';
            _html += '<span style="width: 115px;" class="col-md-1 ' + _hide_aksi_set + ' ' + _hide_aksi_reset + ' reset"><i class="btn-glyphicon glyphicon glyphicon-remove" onclick="reset(this)"></i></span>';
            _html += '<span style="width: 115px;" class="col-md-1 ' + _hide_aksi_set + ' set" data-aksi="I"><i class="btn-glyphicon glyphicon glyphicon-ok" onclick="set_berat_pallet(this)"></i></span>';
            _html += '<span style="width: 115px;" class="col-md-1 ' + _hide_aksi_reset + ' ' + _hide_aksi_edit + ' edit"><i class="btn-glyphicon glyphicon glyphicon-pencil" onclick="edit_berat_pallet(this)"></i></span>';
            _html += '</td>';
            _html += '<td class="cetak_barcode" align="center"><input type="checkbox" ' + _disabled + ' onclick="check_button(this)" value="0" data-id="' + data.KODE_PALLET + '"></td></tr>';
            _tt.find('tbody').append(_html);
        });

        var tabel_riwayat = $('table#master-pallet');
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

        var tr = $('#master-pallet tbody tr:first');
        // tr.find('td.tara input.input_tara').focus().select();

    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function master_uom() {
    $.ajax({
        type: 'POST',
        url: "master/pallet/get_master_uom/",
        data: {}
    }).done(function(data) {
        $('#modal_master_uom .modal-body').html(data);
        $('#modal_master_uom').modal('show');
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function check_button(elm) {
    var table = $(elm).closest('table');
    var a = table.find('input[type="checkbox"]:checked');
    if (a.length > 0) {
        $('#cetak').attr('disabled', false);
    } else {
        $('#cetak').attr('disabled', true);
    }
}

function cetak_pallet() {
    var _cb = $('#master-pallet').find('input[type="checkbox"]:checked');
    var arr = [];
    $.each(_cb, function(i, v) {
        arr.push($(v).attr('data-id'));
    });
    $.redirect('master/pallet/cetak_pallet', { 'generate_data': arr }, 'POST', '_blank');
}
$(document).ready(function() {
    goSearch();
    $('#q_tanggal_penimbangan').datepicker({
        dateFormat: 'dd M yy'
    });
});

$('#pallet_aktif').click(function() {
    goSearch();
});

$('#pallet_tidak_aktif').click(function() {
    goSearch();
});

$('.field_input').keyup(function() {
    checkInput();
});

$('#master-pallet').on('click', 'tr', function() {
    selected_uom = $(this).find('td:nth-child(1)').text();
});

$('#master-pallet > tbody').on('', 'tr', function() {
    selected_uom = $(this).attr('data-id');
    form_mode = "ubah";
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/pallet/get_uom/",
        data: {
            id_pallet: selected_uom,
        }
    }).done(function(data) {
        $('#inp_id_pallet').val(data.UOM);
        $('#inp_tara').val(data.tara);
        $('#inp_siklus').val(data.tara_BASE_UOM + ' - ' + data.UOM_BASE_UOM);
        $('#inp_siklus').attr('data-id_pallet', data.UOM_BASE_UOM);
        $('#inp_konversi').val(data.KONVERSI);

        $('#inp_id_pallet').attr("readonly", true);
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

    $('#inp_id_pallet').attr("readonly", false);
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
    id_pallet = $('#inp_id_pallet').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').attr('data-id_pallet');
    konversi = $('#inp_konversi').val();

    if (id_pallet == siklus) {
        passed = false;
    }

    if (!passed) {
        bootbox.alert("Kolom id_pallet Dasar tidak boleh sama dengan kolom id_pallet.");
        return false;
    }

    if (id_pallet && tara && konversi) {
        var data = "Apakah Anda yakin akan Menyimpan data id_pallet (UOM)?";
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
                            url: "master/pallet/add_uom/",
                            data: {
                                id_pallet: id_pallet,
                                tara: tara,
                                siklus: siklus,
                                konversi: konversi
                            }
                        }).done(function(data) {
                            if (data.result == "success") {
                                notificationBox("Penyimpanan data id_pallet " + id_pallet + " berhasil dilakukan.");
                                $('#modal_uom').modal("hide");
                                resetInput();

                                goSearch();
                            } else {
                                if (data.check == "failed")
                                    notificationBox("id_pallet " + id_pallet + " sudah terdaftar.");
                                else
                                    notificationBox("Penyimpanan data id_pallet " + id_pallet + " gagal dilakukan");
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
    id_pallet = $('#inp_id_pallet').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').attr('data-id_pallet');
    konversi = $('#inp_konversi').val();

    if (id_pallet == siklus) {
        passed = false;
    }

    if (!passed) {
        bootbox.alert("Kolom id_pallet Dasar tidak boleh sama dengan kolom id_pallet.");
        return false;
    }

    if (id_pallet && tara && konversi) {
        var data = "Apakah Anda yakin akan Mengubah data id_pallet ini?";
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
                            url: "master/pallet/update_uom/",
                            data: {
                                id_pallet: id_pallet,
                                tara: tara,
                                siklus: siklus,
                                konversi: konversi
                            }
                        }).done(function(data) {
                            if (data.result == "success") {
                                notificationBox("Perubahan data id_pallet " + id_pallet + " berhasil dilakukan.");

                                $('#modal_uom').modal("hide");
                                resetInput();

                                goSearch();
                            } else {
                                if (data.check == "failed") {
                                    notificationBox("id_pallet " + id_pallet + " sudah terdaftar.");
                                } else {
                                    notificationBox("Perubahan data id_pallet " + id_pallet + " gagal dilakukan");
                                }
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
    $('#inp_id_pallet').val('');
    $('#inp_tara').val('');
    $('#inp_siklus').val('');
    $('#inp_siklus').attr('data-id_pallet', '');
    $('#inp_konversi').val('');
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function checkInput() {
    id_pallet = $('#inp_id_pallet').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').val();
    konversi = $('#inp_konversi').val();
    /*
        if (id_pallet != "" && tara != "" && konversi != "") {
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

function set_berat_pallet(elm) {
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var _aksi = tr.find('td.keterangan span.set').attr('data-aksi');
    var id_pallet = tr.find('td.id_pallet').text();
    var tara = tr.find('td.tara input.input_tara').val();
    var data_tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var keterangan = tr.find('td.keterangan input.input_keterangan').val();
    var tgl_timbang = (data_tara) ? tr.find('td.tanggal').attr('data-tanggal-db') : '';
    var params = {
        'id_pallet': id_pallet,
        'tara': tara,
        'keterangan': keterangan,
        'tgl_timbang': tgl_timbang
    }
    if (tara && keterangan) {
        if (_aksi == 'U') {
            dialog_set_berat_pallet('U', params);
        } else {
            dialog_set_berat_pallet('I', params);
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

function dialog_set_berat_pallet(aksi, params) {
    var data = (aksi == 'U') ? "Apakah Anda yakin akan melanjutkan perubahan?" : "Apakah Anda yakin akan menyimpan penimbangan pallet?";
    var msg_sukses = (aksi == 'U') ? "Data pallet berhasil diubah." : "Penimbangan pallet baru berhasil disimpan.";
    var msg_gagal = (aksi == 'U') ? "Data pallet gagal diubah." : "Penimbangan pallet baru gagal disimpan.";
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
                    simpan_berat_pallet(params, function(result) {
                        if (result == 1) {
                            goSearch();
                            toastr.success(msg_sukses, 'Informasi');
                            return true;
                        } else if (result == 2) {
                            toastr.warning('Tidak bisa ubah data pallet pada hari H.', 'Informasi');
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
    var id_pallet = tr.find('td.id_pallet').text();
    var tanggal = tr.find('td.tanggal').attr('data-tanggal');
    var tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var tara_span_last = tr.find('td.tara span.tooltips-base').html();
    var keterangan = tr.find('td.keterangan input.input_keterangan').attr('data-keterangan');

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

function edit_berat_pallet(elm) {
    var tr = $(elm).closest('tr');
    var _aksi_reset = tr.find('td.keterangan span.reset').hasClass('hide');
    var _aksi_set = tr.find('td.keterangan span.set').hasClass('hide');
    var id_pallet = tr.find('td.id_pallet').text();
    var tara = tr.find('td.tara span:first').text();
    var tara_span_last = tr.find('td.tara span.tooltips-base').html();
    var keterangan = tr.find('td.keterangan').text();
    /* periksa dulu apakah masih memiliki stok atau tidak */
    $.get('master/pallet/check_stok', { idpallet: id_pallet }, function(data) {
        if (data.stok > 0) {
            bootbox.alert('Terdapat stok pakan pada pallet tersebut');
        } else {
            if (_aksi_reset && _aksi_set) {
                //tara = tara.replace(/[.]/gi, '');
                //tara = tara.replace(',','.');
                tr.find('td.tanggal').text('');
                var _html_tara = '<input data-toggle="tooltip" data-placement="right" title="" onfocus="Home.getDataTimbang(this)" readonly onmouseover="view_tooltip(this)" data-tara="' + tara + '" class="text-center input_tara" name="input_tara" style="width:100px;">';
                _html_tara += '<span class="tooltips-base tooltips-default hide">' + tara_span_last + '</span>';
                tr.find('td.tara').html(_html_tara);
                tr.find('td.tara span.tooltips-content').text(tara);
                var _html_keterangan = '<div class="form-inline"><span style="width: 150px;" class="col-md-1 hide"><input onkeyup="kontrol_keterangan(this);" data-keterangan="' + keterangan + '" class="text-center input_keterangan" name="input_keterangan" value="by_system" style="width:150px;"></span>';
                _html_keterangan += '<span style="width: 35px;" class="col-md-1 reset">' + tr.find('td.keterangan span.reset').html() + '</span>';
                _html_keterangan += '<span style="width: 35px;" class="col-md-1 set" data-aksi="U">' + tr.find('td.keterangan span.set').html() + '</span>';
                _html_keterangan += '<span style="width: 35px;" class="col-md-1 edit">' + tr.find('td.keterangan span.edit').html() + '</span></div>';
                tr.find('td.keterangan').html(_html_keterangan);
                tr.find('td.keterangan span.reset, td.keterangan span.set').removeClass('hide');
                tr.find('td.keterangan span.edit').addClass('hide');
                // tr.find('td.tara input.input_tara').focus().select();
            }
            $('td.tara input').numeric({
                allowPlus: false,
                allowMinus: false,
                allowThouSep: false,
                allowDecSep: true
            });
        }
    }, 'json');
}

function simpan_berat_pallet(params, callback) {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/pallet/simpan_berat_pallet/",
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
    var id_pallet = tr.find('td.id_pallet').text();
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
    $('#master-pallet tbody tr').removeClass('double_click');
    $(elm).addClass('double_click');
    var kode_pallet = $(elm).find('td.id_pallet').text();
    /* periksa dulu apakah masih memiliki stok atau tidak */
    $.get('master/pallet/check_stok', { idpallet: kode_pallet }, function(data) {
        if (data.stok > 0) {
            bootbox.alert('Terdapat stok pakan pada pallet tersebut');
        } else {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "master/pallet/history_pallet/",
                data: {
                    kode_pallet: kode_pallet
                },
                async: false,
            }).done(function(data) {
                dialog_history(data);
            }).fail(function(reason) {
                console.info(reason);
            });
        }
    }, 'json');
}

function dialog_history(html) {
    var data = html;
    var box = bootbox.dialog({
        message: data,
        title: 'Riwayat Master Pallet',
        className: "medium-large"
    });
}

function dialog_status_pallet(elm) {
    var kode_pallet = $(elm).closest('tr').find('td.id_pallet').text();
    var status_pallet = $(elm).val();
    var tanggal_penimbangan = $(elm).closest('tr').find('td.tanggal').attr('data-tanggal');
    var label = (status_pallet == 'N') ? "mengaktifkan" : "menonaktifkan";
    var data = "Apakah anda yakin akan " + label + " ID-Pallet : " + kode_pallet + " ?";
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
                    if (status_pallet == 'N') {
                        aktifkan = 1;
                        $('div.bootbox').modal('hide');

                    } else {
                        //dialog_keterangan(kode_pallet, status_pallet, tanggal_penimbangan);
                        ubah_status_pallet_nonaktif(kode_pallet, status_pallet, tanggal_penimbangan);
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

function dialog_keterangan(kode_pallet, status_pallet, tanggal_penimbangan) {
    var data = "<div class='col-md-12 form-group'>Keterangan perubahan status pallet</div>";
    data += '<div class="col-md-12 form-group"><textarea onkeyup="maks_karakter(this)" class="form-control" id="keterangan" name="keterangan" type="text"></textarea></div>';
    data += '<div class="col-md-12 form-group text-center"><button id="btn-simpan" data-kode-pallet="' + kode_pallet + '" data-status="' + status_pallet + '" data-tanggal="' + tanggal_penimbangan + '" onclick="ubah_status_pallet(this)" class="btn btn-primary" type="button" disabled>Simpan</button></div>';
    var box = bootbox.dialog({
        message: data,
    });
    box.bind('shown.bs.modal', function() {
        box.find("textarea#keterangan").focus();
    });
}

function ubah_status_pallet_nonaktif(kode_pallet, status_pallet, tanggal_penimbangan) {
    var keterangan = ' ';
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/pallet/ubah_status_pallet/",
        data: {
            kode_pallet: kode_pallet,
            status_pallet: status_pallet,
            keterangan: keterangan,
            tanggal_penimbangan: tanggal_penimbangan
        }
    }).done(function(data) {
        if (data.status_pallet == status_pallet) {
            goSearch();
            toastr.success('Data pallet telah dinonaktifkan.', 'Informasi');
            $('div.bootbox').modal('hide');
        } else {
            toastr.error('Data pallet gagal dinonaktifkan.', 'Informasi');
        }
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {});
}

function ubah_status_pallet(elm) {
    var kode_pallet = $(elm).attr('data-kode-pallet');
    var status_pallet = $(elm).attr('data-status');
    var tanggal_penimbangan = $(elm).attr('data-tanggal');
    var keterangan = $('#keterangan').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: "master/pallet/ubah_status_pallet/",
        data: {
            kode_pallet: kode_pallet,
            status_pallet: status_pallet,
            keterangan: keterangan,
            tanggal_penimbangan: tanggal_penimbangan
        }
    }).done(function(data) {
        if (data.status_pallet == status_pallet) {
            goSearch();
            toastr.success('Data pallet telah dinonaktifkan.', 'Informasi');
            $('div.bootbox').modal('hide');
        } else {
            toastr.error('Data pallet gagal dinonaktifkan.', 'Informasi');
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