var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_uom = "";

function pilih_uom(e) {
    var satuan = $(e).find('td.satuan').text();
    var deskripsi = $(e).find('td.deskripsi').text();
    $('#inp_satuan_dasar').attr('data-satuan', satuan);
    $('#inp_satuan_dasar').val(deskripsi + ' - ' + satuan);
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

    satuan = $('#q_satuan').val();
    deskripsi = $('#q_deskripsi').val();
    satuan_dasar = $('#q_satuan_dasar').val();
    konversi = $('#q_konversi').val();

    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/uom/get_pagination/",
        data : {
            satuan : satuan,
            deskripsi : deskripsi,
            satuan_dasar : satuan_dasar,
            konversi : konversi,
            page_number : page_number,
            search : search
        }
    }).done(function(data) {
        $("tbody", "#master-uom").html("");

        window.mydata = data;

        if (!empty(mydata.length)) {
            if (mydata.length > 0) {
                total_page = mydata[0].TotalRows;
                $("#total_page").text(total_page);
                var record_par_page = mydata[0].Rows;

                $.each(record_par_page, function(key, data) {
                    var dbu = (data.DESKRIPSI_BASE_UOM) ? data.DESKRIPSI_BASE_UOM : "-";
                    var number = data.KONVERSI;
                    var _konversi = number_format(number, 3, ',', '.');
                    $("tbody", "#master-uom").append('<tr data-id="' + data.UOM + '"><td>' + data.UOM + '</td><td>' + data.DESKRIPSI + '</td><td>' + dbu + '</td><td align="right">' + _konversi + '</td></tr>');

                });
            }
            if (total_page == 1 || total_page == 0) {
                $("#next").prop('disabled', true);
            } 
        } else {
            $("#page_number").text('0');
            $("#total_page").text('0');
            $("#next").prop('disabled', true);
        }

    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
}

function master_uom() {
    $.ajax({
        type : 'POST',
        url : "master/uom/get_master_uom/",
        data : {}
    }).done(function(data) {
        $('#modal_master_uom .modal-body').html(data);
        $('#modal_master_uom').modal('show');
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
}


$(document).ready(function() {
    getReport(page_number);

    $("#next").on("click", function() {
        page_number = (page_number + 1);
        getReport(page_number);
    });

    $("#previous").on("click", function() {
        page_number = (page_number - 1);
        getReport(page_number);
    });
});

$('.q_search').keyup(function() {
    this.value = this.value.toUpperCase();
    goSearch();
});

$('#q_konversi').change(function() {
    goSearch();
});

$('.field_input').keyup(function() {
    checkInput();
});

$('#master-uom').on('click', 'tr', function() {
    selected_uom = $(this).find('td:nth-child(1)').text();
});

$('#master-uom > tbody').on('dblclick', 'tr', function() {
    selected_uom = $(this).attr('data-id');
    form_mode = "ubah";
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/uom/get_uom/",
        data : {
            satuan : selected_uom,
        }
    }).done(function(data) {
        $('#inp_satuan').val(data.UOM);
        $('#inp_deskripsi').val(data.DESKRIPSI);
        $('#inp_satuan_dasar').val(data.DESKRIPSI_BASE_UOM + ' - ' + data.UOM_BASE_UOM);
        $('#inp_satuan_dasar').attr('data-satuan', data.UOM_BASE_UOM);
        $('#inp_konversi').val(data.KONVERSI);

        $('#inp_satuan').attr("readonly", true);
        $('#inp_satuan_dasar').attr("readonly", true);

        $('#btnSimpan').hide();
        $('#btnUbah').show();
        //$('#btnUbah').removeClass('disabled');

        $('#modal_uom').modal("show");
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
});

$("#btnTambah").click(function() {
    resetInput();
    form_mode = "tambah";

    $('#inp_satuan').attr("readonly", false);
    $('#inp_satuan_dasar').attr("readonly", true);
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
	satuan = $('#inp_satuan').val();
    deskripsi = $('#inp_deskripsi').val();
    satuan_dasar = $('#inp_satuan_dasar').attr('data-satuan');
    konversi = $('#inp_konversi').val();

	if(satuan == satuan_dasar)
		passed = false;
	
	if(!passed){
		bootbox.alert("Kolom Satuan Dasar tidak boleh sama dengan kolom Satuan.");
		return false;
	}
	
    if (satuan && deskripsi && konversi) {

        var data = "Apakah Anda yakin akan Menyimpan data Satuan (UOM)?";
            var box = bootbox.dialog({
                    message : data,
                    buttons : {
                        danger : {
                            label : "Tidak",
                            className : "btn-default",
                            callback : function() {
                                return true;
                            }
                        },
                        success : {
                            label : "Ya",
                            className : "btn-primary",
                            callback : function() {
                                $.ajax({
                                    type : 'POST',
                                    dataType : 'json',
                                    url : "master/uom/add_uom/",
                                    data : {
                                        satuan : satuan,
                                        deskripsi : deskripsi,
                                        satuan_dasar : satuan_dasar,
                                        konversi : konversi
                                    }
                                }).done(function(data) {
                                    if (data.result == "success") {
                                        notificationBox("Penyimpanan data Satuan " + satuan + " berhasil dilakukan.");
                                        $('#modal_uom').modal("hide");
                                        resetInput();

                                        getReport(page_number);
                                    } else {
                                        if (data.check == "failed")
                                            notificationBox("Satuan " + satuan + " sudah terdaftar.");
                                        else
                                            notificationBox("Penyimpanan data Satuan " + satuan + " gagal dilakukan");
                                    }
                                }).fail(function(reason) {
                                    console.info(reason);
                                }).then(function(data) {
                                });
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
	
	satuan = $('#inp_satuan').val();
    deskripsi = $('#inp_deskripsi').val();
    satuan_dasar = $('#inp_satuan_dasar').attr('data-satuan');
    konversi = $('#inp_konversi').val();

	if(satuan == satuan_dasar)
		passed = false;
	
	if(!passed){
		bootbox.alert("Kolom Satuan Dasar tidak boleh sama dengan kolom Satuan.");
		return false;
	}
    
    if (satuan && deskripsi && konversi) {

   var data = "Apakah Anda yakin akan Mengubah data Satuan ini?";
        var box = bootbox.dialog({
                    message : data,
                    buttons : {
                        danger : {
                            label : "Tidak",
                            className : "btn-default",
                            callback : function() {
                                return true;
                            }
                        },
                        success : {
                            label : "Ya",
                            className : "btn-primary",
                            callback : function() {
                                $.ajax({
                                    type : 'POST',
                                    dataType : 'json',
                                    url : "master/uom/update_uom/",
                                    data : {
                                        satuan : satuan,
                                        deskripsi : deskripsi,
                                        satuan_dasar : satuan_dasar,
                                        konversi : konversi
                                    }
                                }).done(function(data) {
                                    if (data.result == "success") {
                                        notificationBox("Perubahan data Satuan " + satuan + " berhasil dilakukan.");

                                        $('#modal_uom').modal("hide");
                                        resetInput();

                                        getReport(page_number);
                                    } else {
                                        if (data.check == "failed")
                                                notificationBox("Satuan " + satuan + " sudah terdaftar.");
                                        else
                                            notificationBox("Perubahan data Satuan " + satuan + " gagal dilakukan");
                                    }
                                }).fail(function(reason) {
                                    console.info(reason);
                                }).then(function(data) {
                                });
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
    $('#inp_satuan').val('');
    $('#inp_deskripsi').val('');
    $('#inp_satuan_dasar').val('');
    $('#inp_satuan_dasar').attr('data-satuan', '');
    $('#inp_konversi').val('');
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function checkInput() {

    satuan = $('#inp_satuan').val();
    deskripsi = $('#inp_deskripsi').val();
    satuan_dasar = $('#inp_satuan_dasar').val();
    konversi = $('#inp_konversi').val();
/*
    if (satuan != "" && deskripsi != "" && konversi != "") {
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

function notificationBox(message){
    bootbox.dialog({
        message : message,
        buttons : {
            success : {
                label : "OK",
                className : "btn-primary",
                callback : function() {
                    return true;
                }
            }
        }
    });
}