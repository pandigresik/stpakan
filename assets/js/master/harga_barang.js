var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_kode_pelanggan = "";
var selected_kode_barang = "";
var selected_uom = "";
var selected_tanggal_berlaku = "";

var json_month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

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

    pelanggan = $('#q_pelanggan').val();
    kode_barang = $('#q_kode_barang').val();
    nama_barang = $('#q_nama_barang').val();
    satuan = $('#q_satuan').val();
    bentuk_pakan = $('#q_bentuk_pakan').val();
    tanggal_berlaku = $('#q_tanggal_berlaku').val();

    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/harga_barang/get_pagination/",
        data : {
            pelanggan : pelanggan,
            kode_barang : kode_barang,
            nama_barang : nama_barang,
            satuan : satuan,
            bentuk_pakan : bentuk_pakan,
            tanggal_berlaku : tanggal_berlaku,
            page_number : page_number,
            search : search
        }
    }).done(function(data) {
        $("tbody", "#master-harga-barang").html("");

        window.mydata = data;

        if (!empty(mydata.length)) {
            if (mydata.length > 0) {
                total_page = mydata[0].TotalRows;
                $("#total_page").text(total_page);
                var record_par_page = mydata[0].Rows;

                $.each(record_par_page, function(key, data) {
                    var number = data.harga;
                    var _harga = number_format(number, 2, ',', '.');
                    $("tbody", "#master-harga-barang").append('<tr data-kode-pelanggan="' + data.kode_pelanggan + '" data-kode-barang="' + data.kode_barang + '" data-uom="' + data.uom + '" data-tanggal-berlaku="' + convert_month(data.tanggal_berlaku) + '"><td>' + data.nama_pelanggan + '</td><td>' + data.kode_barang + '</td><td>' + data.nama_barang + '</td><td>' + data.satuan + '</td><td>' + data.bentuk_pakan_label + '</td><td>' + convert_month(data.tanggal_berlaku) + '</td><td align="right"> Rp ' + _harga + '</td></tr>');


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

$("#inp_tanggal_berlaku").datepicker({
    dateFormat : 'dd M yy',
    minDate : 0
});
$("#q_tanggal_berlaku").datepicker({
    dateFormat : 'dd M yy',
});
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

    $('#inp_harga').maskMoney();
});

$('.q_search').keyup(function() {
    this.value = this.value.toUpperCase();
    goSearch();
});

$('#q_satuan').change(function() {
    goSearch();
});

$('#q_bentuk_pakan').change(function() {
    goSearch();
});

$('#q_tanggal_berlaku').change(function() {
    goSearch();
});

$('.field_input').keyup(function() {
    checkInput();
});

//$('#master-harga-barang').on('click', 'tr', function() {
//	selected_harga_barang = $(this).find('td:nth-child(1)').text();
//});

$('#master-harga-barang > tbody').on('dblclick', 'tr', function() {
    selected_kode_pelanggan = $(this).attr('data-kode-pelanggan');
    selected_kode_barang = $(this).attr('data-kode-barang');
    selected_tanggal_berlaku = $(this).attr('data-tanggal-berlaku');
    selected_uom = $(this).attr('data-uom');
    selected_tanggal_berlaku = $(this).attr('data-tanggal-berlaku');
    form_mode = "ubah";
    search_data_harga(selected_kode_barang,selected_kode_pelanggan,selected_tanggal_berlaku,0);
    $('#modal-harga-barang').modal("show");

    $('#inp_kode_barang').attr("readonly", true);
    /*
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/harga_barang/get_harga_barang/",
        data : {
            pelanggan : selected_kode_pelanggan,
            kode_barang : selected_kode_barang,
            satuan : selected_uom,
            tanggal_berlaku : selected_tanggal_berlaku
        }
    }).done(function(data) {
        $('#inp_pelanggan').val(data.nama_pelanggan);
        $('#inp_pelanggan').attr('data-kode-pelanggan',data.kode_pelanggan);
        $('#inp_kode_barang').val(data.kode_barang);
        $('#inp_nama_barang').text(data.nama_barang);
        $('#inp_bentuk_pakan').text(data.bentuk_pakan_label);
        $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang',data.bentuk_pakan);
        $('#inp_satuan').val(data.uom);
        $('#inp_tanggal_berlaku').val(selected_tanggal_berlaku);
        $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku',selected_tanggal_berlaku);
        var number = data.harga;
        var _harga = number_format(number, 2, ',', '.');
        $('#inp_harga').val(_harga);

        $('#inp_pelanggan').attr("readonly", true);
        $('#inp_kode_barang').attr("readonly", true);
        $('#inp_satuan').attr("disabled", true);
        $('#inp_tanggal_berlaku').attr("readonly", true);

        $('#inp_tanggal_berlaku').datepicker( "option", "minDate", 0);

        $('#btnSimpan').hide();
        $('#btnUbah').show();
        $('#btnUbah').removeClass('disabled');

        $('#modal-harga-barang').modal("show");
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
    });
    */
});

$("#btnTambah").click(function() {
    resetInput();
    form_mode = "tambah";

    $('#inp_tanggal_berlaku').datepicker( "option", "minDate", null);

    $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku','');

    $('#inp_pelanggan').attr("readonly", true);
    $('#inp_kode_barang').attr("readonly", false);
    $('#inp_satuan').removeAttr("disabled");
    $('#inp_tanggal_berlaku').attr("readonly", true);
    $('#btnSimpan').show();
    $('#btnUbah').hide();

    $('#modal-harga-barang').modal("show");
});

$("#btnBatal").click(function() {
    $('#modal-harga-barang').modal("hide");
    resetInput();
});

$(".btnKembali").click(function() {
    $(this).parents('div.modal').modal("hide");
    //resetInput();
});

$("#btnSimpan").click(function() {
	var passed = true;
	
    pelanggan = $('#inp_pelanggan').attr('data-kode-pelanggan');
    bentuk_pakan = $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang');
    //pelanggan = $('#inp_pelanggan').val();
    kode_barang = $('#inp_kode_barang').val();
    nama_barang = $('#inp_nama_barang').text();
    //bentuk_pakan = $('#inp_bentuk_pakan').text();
    satuan = $('#inp_satuan').val();
    tmp_tanggal_berlaku = $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku');
    tanggal_berlaku = $('#inp_tanggal_berlaku').val();
    harga = $('#inp_harga').val();

    //var harga = $('#inp_harga').val();
    //console.log(x)
    harga = harga.replace(/\./g,"");
    //console.log(x)
    harga = harga.replace(/,/g,".");
    console.log(harga)

	if(harga.length==0)
		passed = false;
	
	if(!passed){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu");
		
		return false;
	}
	
    if (harga.length <=9){

    if (pelanggan && kode_barang && nama_barang && bentuk_pakan && satuan && tanggal_berlaku && harga) {

        var data = "Apakah Anda yakin akan Menyimpan data Harga Barang ini?";
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
                                    url : "master/harga_barang/add_harga_barang/",
                                    data : {
                                        pelanggan : pelanggan,
                                        kode_barang : kode_barang,
                                        nama_barang : nama_barang,
                                        bentuk_pakan : bentuk_pakan,
                                        satuan : satuan,
                                        tmp_tanggal_berlaku : tmp_tanggal_berlaku,
                                        tanggal_berlaku : tanggal_berlaku,
                                        harga : harga
                                    }
                                }).done(function(data) {
                                    if (data.result == "success") {
                                        notificationBox("Penyimpanan Harga Barang dengan kode " + kode_barang + " berhasil dilakukan.");
                                        $('#modal-harga-barang').modal("hide");
                                        resetInput();

                                        getReport(page_number);
                                    } else {
                                        if (data.check == "failed")
                                            notificationBox("Harga Barang sudah ada.");
                                        else
                                            notificationBox("Penyimpanan Harga Barang dengan kode " + kode_barang + " gagal dilakukan");
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
    } else {
        notificationBox("Harga maksimal 8 karakter");
    }
});

$("#btnUbah").click(function() {
	var passed = true;
	
    pelanggan = $('#inp_pelanggan').attr('data-kode-pelanggan');
    bentuk_pakan = $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang');
    //pelanggan = $('#inp_pelanggan').val();
    kode_barang = $('#inp_kode_barang').val();
    nama_barang = $('#inp_nama_barang').text();
    //bentuk_pakan = $('#inp_bentuk_pakan').text();
    satuan = $('#inp_satuan').val();
    tanggal_berlaku_baru = $('#inp_tanggal_berlaku').val();
    tanggal_berlaku = $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku');
    harga = $('#inp_harga').val();

    //var harga = $('#inp_harga').val();
    //console.log(x)
    harga = harga.replace(/\./g,"");
    //console.log(x)
    harga = harga.replace(/,/g,".");
    console.log(harga)

	if(harga.length==0)
		passed = false;
	
	if(!passed){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu");
		
		return false;
	}
	
    if(harga.length<=9){
    var data = "Apakah Anda yakin akan Mengubah data Harga Barang ini?";
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
                                    url : "master/harga_barang/update_harga_barang/",
                                    data : {
                                        pelanggan : pelanggan,
                                        kode_barang : kode_barang,
                                        nama_barang : nama_barang,
                                        bentuk_pakan : bentuk_pakan,
                                        satuan : satuan,
                                        tanggal_berlaku : tanggal_berlaku,
                                        tanggal_berlaku_baru : tanggal_berlaku_baru,
                                        harga : harga
                                    }
                                }).done(function(data) {
                                    if (data.result == "success") {
                                        notificationBox("Perubahan data Harga Barang dengan kode " + kode_barang + " berhasil dilakukan.");
                                        $('#modal-harga-barang').modal("hide");
                                        resetInput();

                                        getReport(page_number);
                                    } else {
                                        if (data.check == "failed")
                                            notificationBox("Harga Barang sudah ada.");
                                        else
                                            notificationBox("Perubahan data Harga Barang dengan kode " + kode_barang + " gagal dilakukan");
                                    }
                                }).fail(function(reason) {
                                    console.info(reason);
                                }).then(function(data) {
                                });
                            }
                        }
                    }
                })
    } else {
        notificationBox("Harga maksimal 8 karakter");
    }
});

/*
 * FUNCTION
 */

function resetInput() {
    $('#inp_pelanggan').val('');
    $('#inp_pelanggan').attr('data-kode-pelanggan','');
    $('#inp_kode_barang').val('');
    $('#inp_nama_barang').text('...');
    $('#inp_bentuk_pakan').text('...');
    $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang','');
    $('#inp_satuan').prop('SelectedIndex',0);
    $('#inp_tanggal_berlaku').val('');
    $('#inp_harga').val('');
}

function pilih_pelanggan(e){
    //console.log($(e).find('td.nama_pelanggan').text());
    var kode_barang = $('#inp_kode_barang').val();
    $('#inp_pelanggan').val($(e).find('td.nama_pelanggan').text());
    $('#inp_pelanggan').attr('data-kode-pelanggan',$(e).find('td.kode_pelanggan').text());
    $(e).parents('div.modal').modal('hide');
    search_data_harga(kode_barang,$(e).find('td.kode_pelanggan').text(),'',1);
}

function search_data_harga(kode_barang,pelanggan,tanggal_berlaku,insert){
    if(pelanggan && kode_barang){
        $.ajax({
            type : 'POST',
            url : "master/harga_barang/search_data_harga/",
            dataType : 'json',
            data : {
                pelanggan : pelanggan,
                kode_barang : kode_barang,
                tanggal_berlaku : tanggal_berlaku
            }
        }).done(function(data) {
            console.log(data);
            $('#inp_satuan').prop('selectedIndex',0);
            $('#inp_tanggal_berlaku').val('');
            $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku','');
            $('#inp_harga').val('');
            if(insert==1){
                //$('#btnSimpan').hide();
                //$('#btnSimpan').addClass("disabled");   
            }
            else{
                //$('#btnUbah').hide();
                //$('#btnUbah').addClass("disabled");   
            }
            if(data){
                if(insert==0){


                    $('#inp_pelanggan').val(data.nama_pelanggan);
                    $('#inp_pelanggan').attr('data-kode-pelanggan',data.kode_pelanggan);
                    $('#inp_kode_barang').val(data.kode_barang);
                    $('#inp_nama_barang').text(data.nama_barang);
                    $('#inp_bentuk_pakan').text(data.bentuk_pakan_label);
                    $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang',data.bentuk_pakan);

                    $('#inp_pelanggan').attr("readonly", true);
                    //$('#inp_kode_barang').attr("readonly", true);
                    $('#inp_satuan').attr("disabled", true);
                    $('#inp_tanggal_berlaku').attr("readonly", true);
                }
                $('#inp_satuan').val(data.UOM);
                $('#inp_tanggal_berlaku').val(convert_month(data.TGL_BERLAKU_NEW));
                $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku',convert_month(data.TGL_BERLAKU_NEW));
                var number = data.HARGA;
                var _harga = number_format(number, 2, ',', '.');
                $('#inp_harga').val(_harga);
  
                if(insert==1){
                    $('#btnUbah').hide();
                    $('#btnSimpan').show();
                    //$('#btnSimpan').removeClass("disabled"); 
                }
                else{

                    $('#btnSimpan').hide();
                    $('#btnUbah').show();
                    //$('#btnUbah').removeClass("disabled"); 
                }  
            }
        }).fail(function(reason) {
            console.info(reason);
        }).then(function(data) {
        });
    }
}

function pilih_barang(e){
    var pelanggan = $('#inp_pelanggan').attr('data-kode-pelanggan');
    $('#inp_kode_barang').val($(e).find('td.kode_barang').text());
    $('#inp_nama_barang').text($(e).find('td.nama_barang').text());
    $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang',$(e).find('td.bentuk_barang').attr('data-kode-bentuk-barang'));
    $('#inp_bentuk_pakan').text($(e).find('td.bentuk_barang').text());
    $(e).parents('div.modal').modal('hide');
    search_data_harga($(e).find('td.kode_barang').text(),pelanggan,'',1);
}


function cari_barang(elm) {
    var kode_barang = $(elm).val();
    if(kode_barang){
        $.ajax({
            type : 'POST',
            url : "master/harga_barang/cari_barang/",
            data : {
                kode_barang : kode_barang
            },
            dataType : "json"
        }).done(function(data) {
            if(data != 0){
                $('#inp_kode_barang').val(data.KODE_BARANG);
                $('#inp_nama_barang').text(data.NAMA_BARANG);
                $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang',data.BENTUK_BARANG);
                $('#inp_bentuk_pakan').text(data.BENTUK_BARANG_LABEL);
                var pelanggan = $('#inp_pelanggan').attr('data-kode-pelanggan');
                search_data_harga(data.KODE_BARANG,pelanggan,'',1);
            }
            else{
                $(elm).val('');
                $('#inp_kode_barang').val('');
                $('#inp_nama_barang').text('...');
                $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang','');
                $('#inp_bentuk_pakan').text('...');
                notificationBox("Barang tidak ditemukan.");
            }
        }).fail(function(reason) {
            console.info(reason);
        }).then(function(data) {
        });
    }
}

function kontrol_efektif(elm) {
    var tanggal_berlaku = $(elm).val();
    if(tanggal_berlaku){

        tanggal_berlaku = tanggal_berlaku.split(" ");
        var tanggal_berlaku_baru = tanggal_berlaku[2]+'-'+(json_month.indexOf(tanggal_berlaku[1])+1)+'-'+tanggal_berlaku[0];
        $.ajax({
            type : 'POST',
            url : "master/harga_barang/kontrol_efektif/",
            data : {
                tanggal_berlaku : tanggal_berlaku_baru
            },
            dataType : "json"
        }).done(function(data) {
            if(data.result == 0){
                notificationBox("Tanggal efektif harus lebih besar dari tanggal hari ini.");
                var tmp_tanggal_berlaku = $('#inp_tanggal_berlaku').attr('data-tanggal-berlaku');
                (tmp_tanggal_berlaku) ? $(elm).val(tmp_tanggal_berlaku) : $(elm).val('');
            }
        }).fail(function(reason) {
            console.info(reason);
        }).then(function(data) {
        });
    }
}

function list_pelanggan() {
    var disabled = $('#inp_satuan').attr('disabled');
    if(!disabled){
        $.ajax({
            type : 'POST',
            url : "master/harga_barang/get_master_pelanggan/",
            data : {}
        }).done(function(data) {
            $('#modal-master-pelanggan .modal-body').html(data);
            $('#modal-master-pelanggan').modal('show');
        }).fail(function(reason) {
            console.info(reason);
        }).then(function(data) {
        });
    }
}

function list_barang() {
    var disabled = $('#inp_satuan').attr('disabled');
    if(!disabled){
        $.ajax({
            type : 'POST',
            url : "master/harga_barang/get_master_barang/",
            data : {}
        }).done(function(data) {
            $('#modal-master-barang .modal-body').html(data);
            $('#modal-master-barang').modal('show');
        }).fail(function(reason) {
            console.info(reason);
        }).then(function(data) {
        });
    }
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function checkInput() {

    pelanggan = $('#inp_pelanggan').attr('data-kode-pelanggan');
    bentuk_pakan = $('#inp_bentuk_pakan').attr('data-kode-bentuk-barang');
    //pelanggan = $('#inp_pelanggan').val();
    kode_barang = $('#inp_kode_barang').val();
    nama_barang = $('#inp_nama_barang').text();
    //bentuk_pakan = $('#inp_bentuk_pakan').text();
    satuan = $('#inp_satuan').val();
    tanggal_berlaku = $('#inp_tanggal_berlaku').val();
    harga = $('#inp_harga').val();
    /*
    if (pelanggan != "" && kode_barang != "" && nama_barang != "" && bentuk_pakan != "" && satuan != "" && tanggal_berlaku != "" && harga != "") {
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