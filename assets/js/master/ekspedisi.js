var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_ekspedisi = "";

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

    nama_ekspedisi = $('#q_nama_ekspedisi').val();
    alamat = $('#q_alamat').val();
    kota = $('#q_kota').val();
    jumlah_kendaraan = $('#q_jumlah_kendaraan').val();

    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/ekspedisi/get_pagination/",
        data : {
            nama_ekspedisi : nama_ekspedisi,
            alamat : alamat,
            kota : kota,
            jumlah_kendaraan : jumlah_kendaraan,
            page_number : page_number,
            search : search
        }
    }).done(function(data) {
        $("tbody", "#master-ekspedisi").html("");

        window.mydata = data;

        if (!empty(mydata.length)) {
            if (mydata.length > 0) {
                total_page = mydata[0].TotalRows;
                $("#total_page").text(total_page);
                var record_par_page = mydata[0].Rows;

                $.each(record_par_page, function(key, data) {
                    $("tbody", "#master-ekspedisi").append('<tr data-id="' + data.KODE_EKSPEDISI + '"><td>' + data.NAMA_EKSPEDISI + '</td><td>' + data.ALAMAT + '</td><td>' + data.KOTA + '</td><td>' + data.VEHICLE_COUNT + '</td></tr>');

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

function master_ekspedisi() {
    $.ajax({
        type : 'POST',
        url : "master/ekspedisi/get_master_ekspedisi/",
        data : {}
    }).done(function(data) {
        $('#modal_master_ekspedisi .modal-body').html(data);
        $('#modal_master_ekspedisi').modal('show');
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

$('#q_jumlah_kendaraan').change(function() {
    goSearch();
});

$('.field_input').keyup(function() {
    checkInput();
});

$('.field_input').change(function() {
    checkInput();
});

$('#master-ekspedisi').on('click', 'tr', function() {
    selected_ekspedisi = $(this).find('td:nth-child(1)').text();
});






















$('#master-ekspedisi > tbody').on('dblclick', 'tr', function() {
    selected_ekspedisi = $(this).attr('data-id');
    form_mode = "ubah";
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/ekspedisi/get_ekspedisi/",
        data : {
            kode_ekspedisi : selected_ekspedisi,
        }
    }).done(function(data) {
        console.log(data['data_farm']);
        $('#inp_kode_ekspedisi').val(data['data_ekspedisi'][0]['KODE_EKSPEDISI']);
        $('#inp_nama_ekspedisi').val(data['data_ekspedisi'][0]['NAMA_EKSPEDISI']);
        $('#inp_alamat').val(data['data_ekspedisi'][0]['ALAMAT']);
        $('#inp_kota').val(data['data_ekspedisi'][0]['KOTA']);
        $('#daftar-kendaraan tbody').html('');
        var data_ke = 1;
        var append_text = '';
        $.each(data['data_ekspedisi_vehicle'], function(key, value) {
        	var _disabled = (value.NO_KENDARAAN) ? 'disabled' : '';
            append_text += '<tr data-ke="' + data_ke + '">';
            append_text += '<td>';
            append_text += '<input onkeyup="kontrol_nopol(this)" onchange="checkInput()" '+_disabled+' value="' + value.NO_KENDARAAN + '" type="text" placeholder="No Polisi" name="no_pol" class="form-control field_input inp_no_pol">';
            append_text += '</td>';
            
            append_text += '<td>';
            append_text += '<select onchange="checkInput()" placeholder="Tipe Kendaraan" name="tipe_kendaraan" class="form-control field_input inp_tipe_kendaraan">';
            append_text += '<option value="">Pilih Tipe Kendaraan</option>';
            var tb = (value.TIPE_KENDARAAN == 'TB') ? 'selected' : '';
            var te = (value.TIPE_KENDARAAN == 'TE') ? 'selected' : '';
            var tg = (value.TIPE_KENDARAAN == 'TG') ? 'selected' : '';
            append_text += '<option value="TB" ' + tb + '>TRUK BAK</option>';
            append_text += '<option value="TE" ' + te + '>TRUK ENGKEL</option>';
            append_text += '<option value="TG" ' + tg + '>TRUK GANDENG</option>';
            append_text += '</select>';
            append_text += '</td>';

            append_text += '<td>';
            append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="' + value.MAX_KUANTITAS + '" type="text" placeholder="Kuantitas Maksimal (Zak)" name="kuantitas_maksimal" class="form-control field_input inp_kuantitas_maksimal">';
            append_text += '</td>';
            append_text += '<td>';
            append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="' + value.MAX_BERAT + '" type="text" placeholder="Berat Maksimal (KG)" name="berat_maksimal" class="form-control field_input inp_berat_maksimal">';
            append_text += '</td>';


            append_text += '<td>';

            $.each(value.DETIL, function(i,v){
                var KODE_FARM = v.KODE_FARM;
                var MAX_RIT = v.MAX_RIT;

                append_text += '<div class="row pkf">';
                append_text += '<div class="col-md-9">';
                append_text += '<select onchange="checkInput()" placeholder="Kode farm" name="kode_farm" class="form-control field_input inp_kode_farm">';

                $.each(data['data_farm'], function(i1, v1){
                    if (KODE_FARM == v1.kode_farm) {
                        append_text += '<option selected value="'+ v1.kode_farm +'">'+ v1.nama_farm +'</option>';
                    } else {
                        append_text += '<option value="'+ v1.kode_farm +'">'+ v1.nama_farm +'</option>';
                    }
                                    
                });

                append_text += '</select>';

                append_text += '</div>';

                append_text += '<div class="col-md-3">';                

                if ((i+1) == value.DETIL.length) {
                    append_text += '<div id="btn-grup-action">';
                    append_text += '<div id="addkf" onclick="tambahKf(this, 1)"><span class="glyphicon glyphicon-plus">&nbsp;</span></div>';
                    append_text += '<div id="delkf" onclick="deleteKf(this, 1)"><span class="glyphicon glyphicon-minus">&nbsp;</span></div>';
                    append_text += '</div>';
                }                
                append_text += '</div>';    
                append_text += '</div>';
            });
            append_text += '</td>';




            append_text += '<td>';
            $.each(value.DETIL, function(i,v){
                var KODE_FARM = v.KODE_FARM;
                var MAX_RIT = v.MAX_RIT;

                append_text += '<div class="row">';
                append_text += '<div class="col-md-12">';
                append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="'+ MAX_RIT +'" type="text" placeholder="Max ritase (zak)" name="max_rit" class="form-control field_input inp_max_rit">';
                append_text += '</div>';
                append_text += '</div>';
            });
            append_text += '</td>';

			append_text += '<td>';
            $.each(value.DETIL, function(i,v){
                var KODE_FARM = v.KODE_FARM;
                var MIN_RIT = v.MIN_RIT;

                append_text += '<div class="row">';
                append_text += '<div class="col-md-12">';
                append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="'+ MIN_RIT +'" type="text" placeholder="Min ritase (zak)" name="min_rit" class="form-control field_input inp_min_rit">';
                append_text += '</div>';
                append_text += '</div>';
            });
            append_text += '</td>';





            /*append_text += '<td>';
            append_text += '<select onchange="checkInput()" placeholder="Kode farm" name="kode_farm" class="form-control field_input inp_kode_farm">';

            $.each(data['data_farm'], function(i, v){
                if (value.KODE_FARM == v.kode_farm) {
                    append_text += '<option selected value="'+ v.kode_farm +'">'+ v.nama_farm +'</option>';
                } else {
                    append_text += '<option value="'+ v.kode_farm +'">'+ v.nama_farm +'</option>';
                }
                
            });

            append_text += '</select>';
            append_text += '</td>';

            append_text += '<td>';
            append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="'+ value.MAX_RIT +'" type="text" placeholder="Max ritase (zak)" name="max_rit" class="form-control field_input inp_max_rit">';
            append_text += '</td>';*/

            append_text += '<td>';
            append_text += '<div data-no-pol="' + value.NO_KENDARAAN + '" class="deleted" onclick="hapus_kendaraan(this)"><span class="glyphicon glyphicon-minus"></span></div>';
            append_text += '</td>';
            append_text += '</tr>';
            data_ke++;
        });
        append_text += '<tr>';
        append_text += '<td><div onclick="tambah_kendaraan(this)"><span class="glyphicon glyphicon-plus">Tambah</span></div></td>';
        append_text += '</tr>';

        $('#daftar-kendaraan tbody').append(append_text);

        // $('tr[data-ke="'+(data_ke-1)+'"] .inp_no_pol').removeAttr('disabled');
        $('tr[data-ke="' + (data_ke - 1) + '"] .inp_tipe_kendaraan').removeAttr('disabled');
        $('tr[data-ke="' + (data_ke - 1) + '"] .inp_kuantitas_maksimal').removeAttr('disabled');
        $('tr[data-ke="' + (data_ke - 1) + '"] .inp_berat_maksimal').removeAttr('disabled');

        $('#inp_kode_ekspedisi').attr("disabled", true);

        $('#btnSimpan').hide();
        $('#btnUbah').show();
        //$('#btnUbah').removeClass('disabled');

        $('#modal_ekspedisi').modal("show");
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
});

$("#btnTambah").click(function() {
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/ekspedisi/get_farm"
    }).done(function(data) {
        resetInput();
        form_mode = "tambah";

        $('#inp_kode_ekspedisi').val($('#tmp_inp_kode_ekspedisi').val());

        $('#inp_kode_ekspedisi').attr("disabled", true);

        $('#daftar-kendaraan tbody').html('');
        var append_text = '';
        append_text += '<tr data-ke="1">';
        append_text += '<td>';
        append_text += '<input onchange="checkInput()" onkeyup="kontrol_nopol(this)" type="text" placeholder="No Polisi" name="no_pol" class="form-control field_input inp_no_pol">';
        append_text += '</td>';
        append_text += '<td>';
        append_text += '<select onchange="checkInput()" placeholder="Tipe Kendaraan" name="tipe_kendaraan" class="form-control field_input inp_tipe_kendaraan">';
        append_text += '<option value="">Pilih Tipe Kendaraan</option>';
        append_text += '<option value="TB">TRUK BAK</option>';
        append_text += '<option value="TE">TRUK ENGKEL</option>';
        append_text += '<option value="TG">TRUK GANDENG</option>';
        append_text += '</select>';
        append_text += '</td>';
        append_text += '<td>';
        append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" type="text" placeholder="Kuantitas Maksimal (Zak)" name="kuantitas_maksimal" class="form-control field_input inp_kuantitas_maksimal">';
        append_text += '</td>';
        append_text += '<td>';
        append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" type="text" placeholder="Berat Maksimal (KG)" name="berat_maksimal" class="form-control field_input inp_berat_maksimal">';
        append_text += '</td>';

        append_text += '<td>';

        append_text += '<div class="row pkf">';
        append_text += '<div class="col-md-9">';
        append_text += '<select onchange="checkInput()" placeholder="Kode farm" name="kode_farm" class="form-control field_input inp_kode_farm">';

        $.each(data['data_farm'], function(i, v){
            append_text += '<option value="'+ v.kode_farm +'">'+ v.nama_farm +'</option>';                
        });

        append_text += '</select>';

        append_text += '</div>';

        append_text += '<div class="col-md-3">';

        append_text += '<div id="btn-grup-action">';

        append_text += '<div id="addkf" onclick="tambahKf(this, 1)"><span class="glyphicon glyphicon-plus">&nbsp;</span></div>';
        append_text += '<div id="delkf" onclick="deleteKf(this, 1)"><span class="glyphicon glyphicon-minus">&nbsp;</span></div>';

        append_text += '</div>';
        append_text += '</div>';

        append_text += '</td>';

        append_text += '<td>';
        append_text += '<div class="row">';
        append_text += '<div class="col-md-12">';
        append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="" type="text" placeholder="Max ritase (zak)" name="max_rit" class="form-control field_input inp_max_rit">';
        append_text += '</div>';
        append_text += '</div>';
        append_text += '</td>';
		
		append_text += '<td>';
        append_text += '<div class="row">';
        append_text += '<div class="col-md-12">';
        append_text += '<input onchange="checkInput()" onkeyup="number_only(this)" value="" type="text" placeholder="Min ritase (zak)" name="min_rit" class="form-control field_input inp_min_rit">';
        append_text += '</div>';
        append_text += '</div>';
        append_text += '</td>';

        append_text += '<td>';
        append_text += '<div data-no-pol="" class="deleted" onclick="hapus_kendaraan(this)"><span class="glyphicon glyphicon-minus"></span></div>';
        append_text += '</td>';
        append_text += '</tr>';
        append_text += '<tr>';
        append_text += '<td><div onclick="tambah_kendaraan(this)"><span class="glyphicon glyphicon-plus">Tambah</span></div></td>';
        append_text += '</tr>';

        $('.inp_max_rit').val('');

        $('#daftar-kendaraan tbody').append(append_text);

        $('#btnSimpan').show();
        $('#btnUbah').hide();

        $('#modal_ekspedisi').modal("show"); 

        $('.inp_tipe_kendaraan').on("change", function(){
            var ths = $(this);
            var tp_kendaraan = ths.val();
            $.ajax({
                type : 'POST',
                dataType : 'json',
                url : "master/ekspedisi/get_last_tpkendaraan",
                data : {
                    tp_kendaraan : tp_kendaraan
                }
            }).done(function(data) {
                ths.closest('tr').find('td:eq(2)').find('.inp_kuantitas_maksimal').val(data.MAX_KUANTITAS);
                ths.closest('tr').find('td:eq(3)').find('.inp_berat_maksimal').val(data.MAX_BERAT);
            });
        });
    });
});





function tambahKf(elm)
{ 
    var tr_elm = $(elm).closest('tr');
    var cout = tr_elm.find('.pkf').length;
    var datake = tr_elm.attr('data-ke');

    var htmlpaste = '';
    tr_elm.find('.pkf').each(function(i, v){
        if ((i+1) == cout) {
            var kfavail = []; 
            tr_elm.find('.inp_kode_farm').each(function(i,v){ 
                if (i < (tr_elm.find('.inp_kode_farm').length)) {
                    kfavail.push($(v).val());
                }
            });

            $.ajax({
                type : 'POST',
                dataType : 'json',
                data : {
                    kfavail : kfavail
                },
                url : "master/ekspedisi/get_kode_farm"
            }).done(function(data) {
                console.log(data);

                var htmlfarm = '';
                $.each(data, function(i,v){
                    htmlfarm += '<option value="'+ v.KODE_FARM +'">'+ v.NAMA_FARM +'</option>';
                });

                if (data.length > 0) {
                    $(v).find('#btn-grup-action').remove();

                    tr_elm.find('td:eq(4)').append('<div class="row pkf">' + $(v).html() + '</div>');
                    tr_elm.find('.inp_kode_farm:last').html(htmlfarm);
				
                    tr_elm.find('.pkf').closest('tr').find('td:eq(5)').find('.row').each(function(i,v){
                        if ((i+1) == cout) {
							tr_elm.find('.pkf').closest('tr').find('td:eq(5)').append('<div class="row">' + $(v).html() + '</div>');
                        }
                    });
					tr_elm.find('.pkf').closest('tr').find('td:eq(6)').find('.row').each(function(i,v){
                        if ((i+1) == cout) {
							tr_elm.find('.pkf').closest('tr').find('td:eq(6)').append('<div class="row">' + $(v).html() + '</div>');
                        }
                    });

                    tr_elm.find('td:eq(4)').find('.pkf:last').find('.col-md-3').html('<div id="btn-grup-action"><div id="addkf" onclick="tambahKf(this, '+ datake +')"><span class="glyphicon glyphicon-plus">&nbsp;</span></div><div id="delkf" onclick="deleteKf(this, '+ datake +')"><span class="glyphicon glyphicon-minus">&nbsp;</span></div></div>');
                }
            });
        }
    });
}



function deleteKf(elm)
{ 
    var tr_elm = $(elm).closest('tr');
    var cout = tr_elm.find('.pkf').length;

    if (cout > 1) {
        tr_elm.find('.pkf').closest('tr').find('td:eq(5)').find('.row').each(function(i,v){
            if ((i+1) == cout) {
                $(v).remove();
            }
        });
		
		tr_elm.find('.pkf').closest('tr').find('td:eq(6)').find('.row').each(function(i,v){
            if ((i+1) == cout) {
                $(v).remove();
            }
        });

        tr_elm.find('.pkf').each(function(i, v){
            if ((i+1) == cout) {
                $(v).remove();
            }

            if ((i+1) == (cout-1)) {
                var append_text = '<div id="btn-grup-action">';
                append_text += '<div id="addkf" onclick="tambahKf(this, 1)"><span class="glyphicon glyphicon-plus">&nbsp;</span></div>';
                append_text += '<div id="delkf" onclick="deleteKf(this, 1)"><span class="glyphicon glyphicon-minus">&nbsp;</span></div>';
                append_text += '</div>';

                $(v).find('.col-md-3').html(append_text);
            }
        });
    }
}


$("#btnBatal").click(function() {
    $('#modal_ekspedisi').modal("hide");
    resetInput();
});

$("#btnKembali").click(function() {
    $('#modal_master_ekspedisi').modal("hide");
});

$("#btnSimpan").click(function() {
    var nopollen = $('input[name=no_pol]').length;

    var nopoldata = {};

    $('input[name=no_pol]').each(function(i,v){
        nopoldata[$(v).val()] = $(v).val();
    });

    if (Object.keys(nopoldata).length != nopollen) {
        notificationBox("NoPol tidak boleh sama.");
    } else {
        var params = [];
        kode_ekspedisi = $('#inp_kode_ekspedisi').val();
        nama_ekspedisi = $('#inp_nama_ekspedisi').val();
        alamat = $('#inp_alamat').val();
        kota = $('#inp_kota').val();
        var param_ekspedisi = [];
        param_ekspedisi.push({
            'kode_ekspedisi' : kode_ekspedisi,
            'nama_ekspedisi' : nama_ekspedisi,
            'alamat' : alamat,
            'kota' : kota
        });



        var coutselect = $('table#daftar-kendaraan tbody').find('select').length;
        var coutinput = $('table#daftar-kendaraan tbody').find('input[type=text]').length;

        var calcselectval = 0;
        var calcinputval  = 0;

        $('table#daftar-kendaraan tbody').find('select').each(function(i,v){
            if ($.trim($(v).val()).length > 0) {
                calcselectval += 1;
            }
        });

        $('table#daftar-kendaraan tbody').find('input[type=text]').each(function(i,v){
            if ($.trim($(v).val()).length > 0) {
                calcinputval += 1;
            }
        });


        var param_kendaraan = [];
        var hasil = 0;
        $.each($('table#daftar-kendaraan tbody').find('tr'), function() {
            var data_ke = $(this).attr('data-ke');
            if (data_ke) {

                var no_pol = $(this).find('.inp_no_pol').val();
                var tipe_kendaraan = $(this).find('.inp_tipe_kendaraan').val();
                var kuantitas_maksimal = $(this).find('.inp_kuantitas_maksimal').val();
                var berat_maksimal = $(this).find('.inp_berat_maksimal').val();

                var kode_farm = [];
                $(this).find('.inp_kode_farm').each(function(i,v){
                    kode_farm.push($(v).val());
                });

                var max_rit = [];
                $(this).find('.inp_max_rit').each(function(i,v){
                    max_rit.push($(v).val());
                });
				
				var min_rit = [];
                $(this).find('.inp_min_rit').each(function(i,v){
                    min_rit.push($(v).val());
                });

                if(no_pol && tipe_kendaraan && kuantitas_maksimal && berat_maksimal){
                    hasil = hasil;
                }
                else{
                    hasil = hasil + 1;
                }

                param_kendaraan.push({
                    'no_pol' : $(this).find('td .inp_no_pol').val(),
                    'tipe_kendaraan' : $(this).find('td .inp_tipe_kendaraan').val(),
                    'kuantitas_maksimal' : $(this).find('td .inp_kuantitas_maksimal').val(),
                    'berat_maksimal' : $(this).find('td .inp_berat_maksimal').val(),
                    'kode_farm' : kode_farm,
                    'max_rit' : max_rit,
					'min_rit' : min_rit,
                });
            }
        });

        params.push({
            'data_ekspedisi' : param_ekspedisi,
            'data_kendaraan' : param_kendaraan
        });

        // console.log(params);

        if (kode_ekspedisi != "" && nama_ekspedisi != "" && alamat != "" && kota != "" && hasil==0 && coutselect == calcselectval && coutinput == calcinputval){

        //if (nama_ekspedisi && alamat && kota && params[0]['data_kendaraan'].length > 0) {
            if (cek_no_pol() == 0) {

                var data = "Apakah Anda yakin akan Menyimpan data Ekspedisi ini?";

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
                                        url : "master/ekspedisi/add_ekspedisi/",
                                        data : {
                                            data : params
                                        }
                                    }).done(function(data) {
                                        if (data.success == 1) {
                                            $('#tmp_inp_kode_ekspedisi').val(data.gen_kode_ekspedisi);
                                            toastr.success("Penyimpanan Ekspedisi " + nama_ekspedisi + " berhasil dilakukan.", 'Informasi');

                                            $('#modal_ekspedisi').modal("hide");
                                            resetInput();

                                            getReport(page_number);


                                            return true;
                                        } else if (data.success == 2) {
                                            notificationBox("NoPol " + data.no_pol + " sudah terdaftar.");
                                            
                                            return false;
                                        } else {
                                            
                                            notificationBox("Penyimpanan Ekspedisi " + nama_ekspedisi + " gagal dilakukan");
                                            return false;
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
                notificationBox("Nopol sudah ada.");
            }

        } else {
            notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
        }
    }   
});


$("#btnUbah").click(function() {

    var nopollen = $('input[name=no_pol]').length;

    var nopoldata = {};

    $('input[name=no_pol]').each(function(i,v){
        nopoldata[$(v).val()] = $(v).val();
    });

    if (Object.keys(nopoldata).length != nopollen) {
        notificationBox("NoPol tidak boleh sama.");
    } else {
        var params = [];
        kode_ekspedisi = $('#inp_kode_ekspedisi').val();
        nama_ekspedisi = $('#inp_nama_ekspedisi').val();
        alamat = $('#inp_alamat').val();
        kota = $('#inp_kota').val();
        var param_ekspedisi = [];
        param_ekspedisi.push({
            'kode_ekspedisi' : kode_ekspedisi,
            'nama_ekspedisi' : nama_ekspedisi,
            'alamat' : alamat,
            'kota' : kota
        });

        var coutselect = $('table#daftar-kendaraan tbody').find('select').length;
        var coutinput = $('table#daftar-kendaraan tbody').find('input[type=text]').length;

        var calcselectval = 0;
        var calcinputval  = 0;

        $('table#daftar-kendaraan tbody').find('select').each(function(i,v){
            if ($.trim($(v).val()).length > 0) {
                calcselectval += 1;
            }
        });

        $('table#daftar-kendaraan tbody').find('input[type=text]').each(function(i,v){
            if ($.trim($(v).val()).length > 0) {
                calcinputval += 1;
            }
        });

        var param_kendaraan = [];
        var hasil = 0;
        $.each($('table#daftar-kendaraan tbody').find('tr'), function() {
            var data_ke = $(this).attr('data-ke');
            if (data_ke) {

                var no_pol = $(this).find('.inp_no_pol').val();
                var tipe_kendaraan = $(this).find('.inp_tipe_kendaraan').val();
                var kuantitas_maksimal = $(this).find('.inp_kuantitas_maksimal').val();
                var berat_maksimal = $(this).find('.inp_berat_maksimal').val();

                var kode_farm = [];
                $(this).find('.inp_kode_farm').each(function(i,v){
                    kode_farm.push($(v).val());
                });

                var max_rit = [];
                $(this).find('.inp_max_rit').each(function(i,v){
                    max_rit.push($(v).val());
                });
				
				var min_rit = [];
                $(this).find('.inp_min_rit').each(function(i,v){
                    min_rit.push($(v).val());
                });

                if(no_pol && tipe_kendaraan && kuantitas_maksimal && berat_maksimal){
                    hasil = hasil;
                }
                else{
                    hasil = hasil + 1;
                }

                param_kendaraan.push({
                    'no_pol' : $(this).find('td .inp_no_pol').val(),
                    'tipe_kendaraan' : $(this).find('td .inp_tipe_kendaraan').val(),
                    'kuantitas_maksimal' : $(this).find('td .inp_kuantitas_maksimal').val(),
                    'berat_maksimal' : $(this).find('td .inp_berat_maksimal').val(),
                    'kode_farm' : kode_farm,
                    'max_rit' : max_rit,
					'min_rit' : min_rit,
                });
            }
        });
        params.push({
            'data_ekspedisi' : param_ekspedisi,
            'data_kendaraan' : param_kendaraan
        });
        // console.log(params);
        if (kode_ekspedisi != "" && nama_ekspedisi != "" && alamat != "" && kota != "" && hasil==0 && calcselectval == coutselect && calcinputval == coutinput){
            if (cek_no_pol() == 0) {

                var data = "Apakah Anda yakin akan Mengubah data Ekspedisi ini?";
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
                                        url : "master/ekspedisi/update_ekspedisi/",
                                        data : {
                                            data : params
                                        }
                                    }).done(function(data) {
                                        if (data.success == 1) {

                                            notificationBox("Perubahan data Ekspedisi " + nama_ekspedisi + " berhasil dilakukan.");
                                            $('#modal_ekspedisi').modal("hide");
                                            resetInput();

                                            getReport(page_number);
                                    return true;
                                        } else if (data.success == 2) {
                                        notificationBox("NoPol " + data.no_pol + " sudah terdaftar.");
                                    return false;
                                        } else {
                                        notificationBox("Perubahan data Ekspedisi " + nama_ekspedisi + " gagal dilakukan");
                                    return false;
                                        }
                                    }).fail(function(reason) {
                                        console.info(reason);
                                    }).then(function(data) {
                                        console.log(data);
                                    });
                                }
                            }
                        }
                    });
            } else {
            notificationBox("Nopol sudah ada.");
            }

        } else {
            notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
        }      
    }

});

/*
 * FUNCTION
 */

 function kontrol_nopol(elm) {
    var pola = "^";
    pola += "[a-zA-Z0-9]*";
    pola += "$";
    rx = new RegExp(pola);

    var nopol = $(elm).val();

    nopol.replace(/\s/g,'');

    if (!nopol.match(rx)) {
        if (elm.lastMatched) {
            nopol = elm.lastMatched;
        } else {
            nopol = "";
        }
    } else {
        elm.lastMatched = nopol;
    }


    $(elm).val(nopol.toUpperCase());
 }

function resetInput() {
    $('#inp_nama_ekspedisi').val('');
    $('#inp_alamat').val('');
    $('#inp_kota').val('');
    $('#inp_kota').attr('data-nama_ekspedisi', '');
    $('#inp_jumlah_kendaraan').val('');
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function cek_no_pol() {
    var tmp_no_pol;
    var count = 0;
    $.each($('#daftar-kendaraan tbody').find('tr'), function() {
        var data_ke = $(this).attr('data-ke');
        if (data_ke) {
            //console.log($(this).find('td input.inp_no_pol').val() +'=='+ tmp_no_pol)
            if ($(this).find('td input.inp_no_pol').val() == tmp_no_pol) {
                count = count + 1;
            }
            tmp_no_pol = $(this).find('td .inp_no_pol').val();
        }
    })

    return count;
}

function tambah_kendaraan(e) {
    /*
    if (form_mode == "tambah")
        $('#btnSimpan').addClass("disabled");

    if (form_mode == "ubah")
        $('#btnUbah').addClass("disabled");
    */
        
    var data_ke = $(e).parents('tr').prev().attr('data-ke');
    var no_pol = $('tr[data-ke="' + data_ke + '"] .inp_no_pol').val();
    var tipe_kendaraan = $('tr[data-ke="' + data_ke + '"] .inp_tipe_kendaraan').val();
    var kuantitas_maksimal = $('tr[data-ke="' + data_ke + '"] .inp_kuantitas_maksimal').val();
    var berat_maksimal = $('tr[data-ke="' + data_ke + '"] .inp_berat_maksimal').val();

    var lkf = $('tr[data-ke="' + data_ke + '"] .inp_kode_farm').length;
    var dkf = 0;
    $('tr[data-ke="' + data_ke + '"] .inp_kode_farm').each(function(i,v){
        dkf += ($.trim($(v).val()).length > 0) ? 1 : 0;
    });
    var kode_farm = (dkf == lkf) ? true : false;
    console.log(kode_farm);

    var lmr = $('tr[data-ke="' + data_ke + '"] .inp_max_rit').length;
    var dmr = 0;
    $('tr[data-ke="' + data_ke + '"] .inp_max_rit').each(function(i,v){
        dmr += ($.trim($(v).val()).length > 0) ? 1 : 0; 
    });
    var max_rit = (dmr == lmr) ? true : false;
    console.log(max_rit);
	$('tr[data-ke="' + data_ke + '"] .inp_min_rit').each(function(i,v){
        dmr += ($.trim($(v).val()).length > 0) ? 1 : 0; 
    });
    var min_rit = (dmr == lmr) ? true : false;
    //console.log(no_pol +'&&'+ no_pol +'&&'+ kuantitas_maksimal +'&&'+ berat_maksimal)
    if (cek_no_pol() == 0) {
        if (no_pol && tipe_kendaraan && kuantitas_maksimal && berat_maksimal && kode_farm && max_rit) {
            $(e).parents('tr').prev().clone().appendTo("table#daftar-kendaraan tbody");
            $(e).parents('tr').remove();
            var tambah_html = '<tr><td><div onclick="tambah_kendaraan(this)"><span class="glyphicon glyphicon-plus">Tambah</span></div></td></tr>';
            $("table#daftar-kendaraan tbody").append(tambah_html);
            $("table#daftar-kendaraan tbody").find('tr:last-child').prev().attr('data-ke', parseInt(data_ke) + 1);

            $("table#daftar-kendaraan tbody").find('tr:last-child').prev().attr('data-ke', parseInt(data_ke) + 1);

            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .deleted').attr('data-no-pol', '');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_no_pol').removeAttr('disabled');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_no_pol').val('');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_tipe_kendaraan').prop('selectedIndex',0);
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_tipe_kendaraan').removeAttr('disabled');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_kuantitas_maksimal').val('');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_kuantitas_maksimal').removeAttr('disabled');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_berat_maksimal').val('');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_berat_maksimal').removeAttr('disabled');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_max_rit').val('');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_max_rit').removeAttr('disabled');
			$('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_min_rit').val('');
            $('tr[data-ke="' + (parseInt(data_ke) + 1) + '"] .inp_min_rit').removeAttr('disabled');

            $('tr[data-ke="' + data_ke + '"] .inp_no_pol').attr('disabled', true);
            /*$('tr[data-ke="' + data_ke + '"] .inp_tipe_kendaraan').attr('disabled', true);
            $('tr[data-ke="' + data_ke + '"] .inp_kuantitas_maksimal').attr('disabled', true);
            $('tr[data-ke="' + data_ke + '"] .inp_berat_maksimal').attr('disabled', true);*/

        } else {
            toastr.error('Silahkan lengkapi data kendaran sebelumnya.', 'Peringatan');
        }
    } else {
        toastr.error('Nopol sudah ada.', 'Peringatan');
    }
}




function hapus_kendaraan(e) {
    var jml = $("table#daftar-kendaraan tbody tr").length;
    if (jml > 2) {
        $(e).parents('tr').remove();
        var data_ke = $("table#daftar-kendaraan tbody").find('tr:last-child').prev().attr('data-ke');
        var no_pol = $(e).attr('data-no-pol');

        var tmp_ubah = 0;
        $.each($('table#daftar-kendaraan tbody').find('tr'), function() {
            var data_no_pol = $(this).find('td .deleted').attr('data-no-pol');
            if (data_no_pol) {
                tmp_ubah = tmp_ubah + 1;
            }
        });

        if (tmp_ubah == 0) {
            $('tr[data-ke="' + data_ke + '"] .inp_no_pol').removeAttr('disabled');
        }
        $('tr[data-ke="' + data_ke + '"] .inp_tipe_kendaraan').removeAttr('disabled');
        $('tr[data-ke="' + data_ke + '"] .inp_kuantitas_maksimal').removeAttr('disabled');
        $('tr[data-ke="' + data_ke + '"] .inp_berat_maksimal').removeAttr('disabled');
    } else {
        notificationBox('Tidak bisa hapus data kendaraan terakhir');
    }
}

function checkInput() {

    kode_ekspedisi = $('#inp_kode_ekspedisi').val();
    nama_ekspedisi = $('#inp_nama_ekspedisi').val();
    alamat = $('#inp_alamat').val();
    kota = $('#inp_kota').val();

    var hasil = 0;
    $.each($('table#daftar-kendaraan tbody').find('tr'),function(){
        var data_ke = $(this).attr('data-ke');
        if(data_ke){
            var no_pol = $(this).find('.inp_no_pol').val();
            var tipe_kendaraan = $(this).find('.inp_tipe_kendaraan').val();
            var kuantitas_maksimal = $(this).find('.inp_kuantitas_maksimal').val();
            var berat_maksimal = $(this).find('.inp_berat_maksimal').val();
            if(no_pol && tipe_kendaraan && kuantitas_maksimal && berat_maksimal){
                hasil = hasil;
            }
            else{
                hasil = hasil + 1;
            }
        }
    })
    /*
    if (kode_ekspedisi != "" && nama_ekspedisi != "" && alamat != "" && kota != "" && hasil==0 ){ //&& no_pol != "" && tipe_kendaraan != "" && kuantitas_maksimal != "" && berat_maksimal != "") {
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