( function() {
    'use strict';

    $('div').on('click', 'a.btn', function(e) {
        // console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })

    $("#tanggal-kirim-awal").datepicker({
        dateFormat : 'dd M yy',
    });
    $("#tanggal-kirim-akhir").datepicker({
        dateFormat : 'dd M yy',
    });

    $(".berat.old").keydown(function(event) {
        return false;
    });

}())

function get_data_detail_penerimaan(e, _tab_active) {
    // var tanggal_kirim = $("#tanggal-kirim").val();
    var tanggal_kirim = $(e).parents('tr').find('td:first').text();
    if (tanggal_kirim) {
        $.ajax({
            type : "POST",
            url : "penerimaan_kandang/transaksi/view",
            data : {
                tanggal_kirim : tanggal_kirim,
                tab_active : _tab_active
            },
            success : function(data) {
                $("#main_content").html(data);
            }
        });
    }
}

function get_data_pengambilan() {
    var tanggal_kirim_awal = $("#tanggal-kirim-awal").val();
    var tanggal_kirim_akhir = $("#tanggal-kirim-akhir").val();
    if (tanggal_kirim_awal && tanggal_kirim_akhir){ //&& (tanggal_kirim_awal <= tanggal_kirim_akhir)) {
        $.ajax({
            type : "POST",
            url : "penerimaan_kandang/main/get_data_pengambilan",
            data : {
                tanggal_kirim_awal : tanggal_kirim_awal,
                tanggal_kirim_akhir : tanggal_kirim_akhir
            },
            dataType : 'json',
            success : function(data) {
                if (data == 2) {
                    toastr.error('Range tanggal kirim tidak valid.', 'Peringatan');
                }
                else if (data){
                    $("#picking-list-table table tbody").html('');
                    var append_text = "";
                    $.each(data, function(key, value) {
                        if (value.status_order != 'D') {
                            append_text += "<tr>";
                            append_text += "<td data-no-order='" + value.no_order + "' data-kode-farm='" + value.kode_farm + "'><span href='#penerimaan_kandang/transaksi' style='color:#428bca;' class='btn link' onclick='get_data_detail_pengambilan(this,1)'>" + convert_month(value.tgl_kirim) + "</span></td>";
                            append_text += "<td>" + convert_month(value.tgl_keb_awal) + " s/d " + convert_month(value.tgl_keb_akhir) + "</td>";
                            append_text += "<td>" + value.jumlah_kebutuhan + "</td>";
                            append_text += "<td>" + value.jumlah_belum_proses + "</td>";
                            append_text += "</tr>";
                        }
                    });
                    $("#picking-list-table table tbody").append(append_text);
                }
            }
        });
    } else {
        toastr.error('Range tanggal kirim tidak valid.', 'Peringatan');
    }
}

function simpan_konfirmasi(data, callback) {
    if (data.length >= 1) {
        $.ajax({
            type : "POST",
            url : "penerimaan_kandang/transaksi/simpan_konfirmasi",
            data : {
                data : data
            },
            dataType : 'json',
            success : function(data) {
                callback(data);
            }
        });
    }
}

function get_data_detail_pengambilan(e, _tab_active) {
    var no_order = $(e).parents('tr').find('td:first').attr('data-no-order');
    var kode_farm = $(e).parents('tr').find('td:first').attr('data-kode-farm');
    if (!no_order || !kode_farm) {
        kode_farm = e[0]['kode_farm'];
        no_order = e[0]['no_order'];
    }
    // console.log(kode_farm +' && '+ no_order);
    if (kode_farm && no_order) {
        $.ajax({
            type : "POST",
            url : "penerimaan_kandang/transaksi/view",
            data : {
                no_order : no_order,
                tab_active : _tab_active,
                kode_farm : kode_farm
            },
            success : function(data) {
                $("#main_content").html(data);
            }
        });
    }
}

function get_data_penerimaan() {
    var tanggal_kirim = $("#tanggal-kirim").val();
    if (tanggal_kirim) {
        $.ajax({
            type : "POST",
            url : "penerimaan_kandang/main/get_data_penerimaan",
            data : {
                tanggal_kirim : tanggal_kirim
            },
            dataType : 'json',
            success : function(data) {
                if (data) {
                    $("#picking-list-table table tbody").html('');
                    var append_text = "";
                    $.each(data, function(key, value) {
                        append_text += "<tr>";
                        append_text += "<td><a href='#penerimaan_kandang/transaksi' class='link' onclick='get_data_detail_penerimaan(this,1)'>" + value.tgl_kirim + "</a></td>";
                        append_text += "<td>" + value.tgl_kebutuhan + "</td>";
                        append_text += "<td>" + value.jumlah_kebutuhan + "</td>";
                        append_text += "<td>" + value.jumlah_belum_proses + "</td>";
                        append_text += "<!--td><a href='#penerimaan_kandang/transaksi' class='link' onclick='get_data_detail_penerimaan(this,2)'>" + value.cetak + "</a></td-->";
                        append_text += "</tr>";
                    })
                    $("#picking-list-table table tbody").append(append_text);
                }
            }
        });
    }
}

function filter(elm) {

    $(elm).val($(elm).val().toUpperCase());

    $.each($('#transaction-table tbody').find('tr'), function() {
        $(this).show();
    })

    $.each($('#transaction-table thead').find('.filter'), function() {
        var value = $(this).val();
        if (value) {
            var name = $(this).attr("name");
            if (name != 'remark' && name != 'user_gudang') {
                $('#transaction-table tbody tr:visible .f' + name + ':not(:contains("' + value.toUpperCase() + '"))').parent().hide();
            } else {
                if (value != 'Semua') {
                    $('#transaction-table tbody tr:visible .f' + name + ':not(:contains("' + value + '"))').parent().hide();
                }
            }
        }
    })
}

function kontrol_option(e) {

    /* CHECKBOX
	 var checked = 0;
	 var data_ke = $(e).parents("tr").attr("data-ke");
	 $.each($('#transaction-table tbody').find('input[type="radio"]'), function() {
	 if ($(this).is(":checked")) {
	 checked++;
	 }
	 })
	 if (checked > 0) {
	 $("#btn-konfirmasi").attr("disabled", false);
	 } else {
	 $("#btn-konfirmasi").attr("disabled", true);
	 }
	 if ($(e).is(":checked")) {
	 $('tr[data-ke="' + data_ke + '"] .jumlah').select();
	 } else {
	 $('tr[data-ke="' + data_ke + '"] .jumlah').val('0');
	 }

	 */

    /* RADIO */
    $("#btn-konfirmasi").attr('disabled', false);
    var data_ke = $(e).parents("tr").attr("data-ke");
    //var disabled = $('tr[data-ke="' + data_ke + '"] .jumlah').attr('disabled');
    var disabled = $('tr[data-ke="' + data_ke + '"] select[name="user_gudang"]').attr('disabled');
    var berat = parseInt($('tr[data-ke="' + data_ke + '"] .jumlah').val());
    // if(!berat || berat==0 || berat=='NaN'){
    if ( typeof disabled == 'undefined') {
        $("#btn-konfirmasi").attr('disabled', true);
        var checked = 0;
        $.each($('#transaction-table table tbody').find('tr'), function() {
            var tmp_data_ke = $(this).attr("data-ke");
            // var tmp_berat = parseFloat($('tr[data-ke="'+tmp_data_ke+'"]
            // .berat').val());
            //var tmp_disabled = $('tr[data-ke="' + tmp_data_ke + '"] .jumlah').attr('disabled');
            var tmp_disabled = $('tr[data-ke="' + tmp_data_ke + '"] select[name="user_gudang"]').attr('disabled');
            // console.log(tmp_data_ke+" dan "+tmp_berat)
            // if(!tmp_berat || tmp_berat==0 || tmp_berat=='NaN'){
            if ( typeof tmp_disabled == 'undefined') {
                //$('tr[data-ke="' + tmp_data_ke + '"] .jumlah').val("0");
                $('tr[data-ke="' + tmp_data_ke + '"] select[name="user_gudang"]').prop('selectedIndex',0);
            }
        })
        //$('tr[data-ke="' + data_ke + '"] .jumlah').focus().select();
        $('tr[data-ke="' + data_ke + '"] select[name="user_gudang"]').focus().select();
    }

}

function kontrol_berat(e) {
    var data_ke = $(e).parents("tr").attr("data-ke");
    var berat = parseInt($(e).val());
    if ($('tr[data-ke="' + data_ke + '"] .radio').is(":checked") && berat && berat > 0) {
        $("#btn-konfirmasi").attr('disabled', false);
        $("#btn-konfirmasi").focus();
    } else {
        toastr.error("Konfirmasi gagal.", "Peringatan");
        $(e).val("0");
        $(e).focus();
    }
}

function kontrol_user_gudang(e) {
    var data_ke = $(e).parents("tr").attr("data-ke");
    var user_gudang = $(e).val();
    //console.log(user_gudang)
    if ($('tr[data-ke="' + data_ke + '"] .radio').is(":checked")) {
        //if(user_gudang){
            $("#btn-konfirmasi").attr('disabled', false);
            $("#btn-konfirmasi").focus();
        //}
        //else{
        //    toastr.error("Kolom Diserahkan Oleh harus diisi.", "Peringatan");
        //    $(e).focus();
        //}
    } else {
        toastr.warning("Silahkan pilih baris yang dikonfirmasi.", "Peringatan");
        $(e).prop('selectedIndex',0);
    }
}

function kontrol_zak(zak) {
    var result = 0;
    var data = [];
    /*
    var tmp_data_ke;
    $.each($('#transaction-table tbody').find('input[type="radio"]'), function() {
        if ($(this).is(":checked")) {
            var data_ke = $(this).parents("tr").attr("data-ke");
            var jumlah = $('tr[data-ke="' + data_ke + '"] .jumlah').val();
            var tmp_jumlah = $('tr[data-ke="' + data_ke + '"] .jumlah').attr('data-value');
            if (parseFloat(jumlah) == parseFloat(tmp_jumlah)) {
                result = result;
            } else {
                tmp_data_ke = data_ke;
                result = result + 1;
            }
        }
    })*/
    data.push({
        'tmp_data_ke' : '',//tmp_data_ke,
        'result' : result
    });
    return data[0];
}

function cek_verifikasi_rfid_card(data) {
    var tmp_data;
    if (data.length == 1) {
        $.ajax({
            type : "POST",
            url : "penerimaan_kandang/transaksi/cek_verifikasi_rfid_card",
            data : {
                data : data
            },
            dataType : 'json',
            async : false,
            success : function(_data) {
                tmp_data = _data;
            }
        });
    }
    return tmp_data;
}

function konfirmasi() {
    var unfill = 0;
    var tmp_data_ke;
    $.each($('#transaction-table tbody').find('input[type="radio"]'), function() {
        if ($(this).is(":checked")) {
            var data_ke = $(this).parents("tr").attr("data-ke");
            //var jumlah = parseFloat($('tr[data-ke="' + data_ke + '"] .jumlah').val());
            // console.log(jumlah);
            //if (!jumlah || jumlah == 'NaN' || jumlah <= 0) {
            //    unfill++;
            //}
            var user_gudang = $('tr[data-ke="' + data_ke + '"] select[name="user_gudang"]').val();
            if(!user_gudang){
                unfill++;
            }
        }
    })
    if (unfill == 0) {
        //var _message = '<div class="form-group form-horizontal new-line">';
        //_message += '<label class="control-label" for="inputEmail3">Apakah yakin anda ingin konfirmasi?</label>';
        //_message += '</div>';
        _message = '<div class="form-group form-horizontal new-line">';
        _message += '<label class="col-sm-3 control-label" for="inputEmail3">RFID Card</label>';
        _message += '<div class="col-sm-8">';
        _message += '<input type="password" placeholder="RFID Card" id="rfid_card" class="form-control" autofocus>';
        _message += '</div>';
        _message += '</div>';
        var box1 = bootbox.dialog({
            message : _message,
            title : "Konfirmasi",
            buttons : {
                danger : {
                    label : "Batal",
                    className : "btn-danger",
                    callback : function() {
                    }
                },
                success : {
                    label : "OK",
                    className : "btn-success",
                    callback : function() {

                        var data = [];
                        $.each($('#transaction-table tbody').find('input[type="radio"]'), function() {
                            if ($(this).is(":checked")) {
                                var data_ke = $(this).parents("tr").attr("data-ke");
                                data.push({
                                    'kode_farm' : $('tr[data-ke="' + data_ke + '"]').attr('data-kode-farm'),
                                    'no_order' : $('tr[data-ke="' + data_ke + '"]').attr('data-no-order'),
                                    'no_reg' : $('tr[data-ke="' + data_ke + '"]').attr('data-no-reg'),
                                    'jenis_kelamin' : $('tr[data-ke="' + data_ke + '"]').attr('data-jenis-kelamin'),
                                    'no_penerimaan_kandang' : $('tr[data-ke="' + data_ke + '"]').attr('data-no-penerimaan-kandang'),
                                    'kode_barang' : $('tr[data-ke="' + data_ke + '"] .fkode_barang').text(),
                                    'kode_kandang' : $('tr[data-ke="' + data_ke + '"] .fkode_kandang').text(),
                                    'user_gudang' : $('tr[data-ke="' + data_ke + '"] select[name="user_gudang"]').val(),
                                });
                            }
                        })
                        var rfid_card = $("#rfid_card").val();
                        if (rfid_card) {
                            data[0]['rfid_card'] = rfid_card;
                            var cek = cek_verifikasi_rfid_card(data);
                            $.when(cek).done(function(result) {
                                if (result == 1) {
                                    var sesuai = kontrol_zak();
                                    tmp_data_ke = sesuai.tmp_data_ke;
                                    if (sesuai.result == 0) {
                                        simpan_konfirmasi(data, function(result) {
                                            if (result == 1) {
                                                get_data_detail_pengambilan(data, 1);
                                                $.each($('#transaction-table tbody').find('input[type="radio"]'), function() {
                                                    if ($(this).is(":checked")) {
                                                        var data_ke = $(this).parents("tr").attr("data-ke");
                                                        $(this).remove();
                                                        //var jumlah = $('tr[data-ke="' + data_ke + '"] .jumlah').val();
                                                        //$('tr[data-ke="' + data_ke + '"] .jumlah').parent().html(jumlah);
                                                        //$('tr[data-ke="' + data_ke + '"] .fremark').html('Received');
                                                        var user_gudang = $('tr[data-ke="'+data_ke+'"] select[name="user_gudang"] option:selected').text();
                                                        $('tr[data-ke="' + data_ke + '"] .fuser_gudang').text(user_gudang);
                                                    }
                                                })
                                                //toastr.success('Konfirmasi berhasil.', 'Berhasil');

                                                messageBox('Verifikasi Sukses',1);
                                            } else {
                                                //toastr.error('Konfirmasi gagal.', 'Peringatan');
                                                messageBox('Verifikasi Gagal',0);
                                            }
                                            box1.modal('hide');
                                        });
                                    } else {

                                        box1.modal('hide');
                                        var box2 = bootbox.dialog({
                                            message : "Jumlah zak tidak sesuai.",
                                            title : "Error Message",
                                            buttons : {
                                                danger : {
                                                    label : "OK",
                                                    className : "btn-danger",
                                                    callback : function() {
                                                        return true;
                                                    }
                                                },
                                            }
                                        });
                                        box2.bind('hidden.bs.modal', function() {
                                            $('#transaction-table tbody tr[data-ke="' + tmp_data_ke + '"] .jumlah').select();
                                        });
                                    }
                                } else {
                                    //toastr.error("Verifikasi RFID card gagal.", "Peringatan");

                                    messageBox('Kode Verifikasi Salah',0);

                                }
                            })
                            return false;
                        } else {
                            toastr.error("RFID card harus diisi.", "Peringatan");
                            return false;
                        }
                    }
                }
            }
        });
        box1.bind('hidden.bs.modal', function() {
            $('#transaction-table tbody tr[data-ke="' + tmp_data_ke + '"] .jumlah').select();
        });

        box1.bind('shown.bs.modal', function() {
            box1.find("input#rfid_card").focus();
        });
    } else {
        //toastr.error("Jumlah zak harus diisi.", "Peringatan");
        toastr.error("Kolom Diserahkan Oleh harus diisi.", "Peringatan");
    }
}

function messageBox(message,result){
    var title = ['Error','Success','Warning'];
    bootbox.dialog({
        message : message,
        title : title[result],
        buttons : {
            success : {
                label : "OK",
                className : "btn-success",
                callback : function() {
                    return true;
                }
            }
        }
    });
}