(function() {
    'use strict';
    $('div').on('click', 'a.btn', function(e) {
        // console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })
    $('ul.pagination').on('click', 'a', function(e) {
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

    $('input.timbangan_kg').numeric({
        allowPlus : false, // Allow the + sign
        allowMinus : false, // Allow the - sign
        allowThouSep : false, // Allow the thousands separator, default is the
        // comma eg 12,000
        allowDecSep : true
    // Allow the decimal separator, default is the fullstop eg 3.141
    });

}())

function messageBox(element, title, message) {
    var box = bootbox.dialog({
        message : message,
        title : title,
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

    box.bind('hidden.bs.modal', function() {
        $(element).focus().select();
    });
}

function show_detail(elm) {
    var data_ke = $(elm).attr('data-ke');
    $('#transaction-table table tbody tr.tr-header').removeClass('mark_row');
    $('#transaction-table table tbody tr.tr-detail').fadeOut('slow').addClass(
        'hide');
    $(elm).addClass('mark_row');
    if ($(elm).next().hasClass('hide')) {
        $(elm).next().fadeIn('slow').removeClass('hide');
    } else {
        $(elm).next().fadeOut('slow').addClass('hide');
    }

    var i = 0;
    $.each($(elm).next().find('table tbody tr'), function() {
        var berat = $(this).find('td input.timbangan_kg').val();
        if (!berat && i == 0) {
            $(this).find('td input.timbangan_kg').removeAttr('readonly');
            i++;
        }
    });

    $(elm).next().find(
        'table tbody tr td input.timbangan_kg:not([readonly]):first')
    .focus().select();

}

function cek_konversi(berat, callback) {
    if (!empty(berat)) {

        berat = parseFloat(berat);
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cek_konversi",
            data : {
                berat : berat
            },
            dataType : 'json',
            success : function(data) {
                callback(data);
            }
        });
    } else {
        callback(2);
    }
}

function kontrol_timbangan(elm) {
    $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
        'disabled', true);
    var berat = $(elm).val();
    var jumlah = $(elm).parents('tr.tr-sub-detail').find('td.rencana_kirim')
    .text();
    jumlah = parseInt(jumlah);
    $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
        'data-result-timbang', '');
    cek_konversi(berat, function(data) {
        var jml_sak = (data) ? parseInt(data.JML_SAK) : 0;
        $(elm).parents('tr.tr-sub-detail').find('td.timbangan_zak').text(
            jml_sak);
        /*
        if (jml_sak <= 0) {
            messageBox(elm, '',
                'Jumlah Timbangan (Sak) harus Lebih Besar dari 0.');
        } else if (jumlah < jml_sak) {
            messageBox(elm, '',
                'Jumlah Timbangan (Sak) Melebihi Rencana Kirim.');
        */
        if (jumlah < jml_sak) {
            messageBox(elm, '',
                'Jumlah Timbangan (Sak) Melebihi Rencana Kirim.');
        } else {
            $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai')
            .removeAttr('disabled');
            $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai')
            .focus().select();

        }
        $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
            'data-result-timbang', data.result);
    });
}

function not_actived(elm){
    elm.preventDefault();
}

function selesai(elm) {
	$(elm).attr('disabled', true);
    var data_ke = $(elm).parents('tr.tr-detail').attr('data-ke');
    var data_result = $(elm).attr('data-result-timbang');
    var _params = [];
    /*
	 * var user_gudang = $('#transaction-table table tbody
	 * tr.tr-header[data-ke="'+data_ke+'"] td select.user_gudang').val();
	 * if(!user_gudang){ toastr.error("Penerima harus diisi.", "Peringatan"); }
	 * else{
	 */
    var rencana_kirim = $(elm).parents('tr.tr-sub-detail').find(
        'td.rencana_kirim').text();
    rencana_kirim = parseInt(rencana_kirim);
    var timbangan_zak = $(elm).parents('tr.tr-sub-detail').find(
        'td.timbangan_zak').text();
    timbangan_zak = parseInt(timbangan_zak);
    // console.log(rencana_kirim)
    // console.log(timbangan_zak)

    _params
    .push({
        'no_reg' : $(elm).parents('tr.tr-sub-detail').attr(
            'data-no-reg'),
        'no_order' : $(elm).parents('tr.tr-sub-detail').attr(
            "data-no-order"),
        'no_pallet' : $(elm).parents('tr.tr-sub-detail').attr(
            "data-no-pallet"),
        'kode_kandang' : $(elm).parents('tr.tr-sub-detail').attr(
            "data-kode-kandang"),
        'jenis_kelamin' : $(elm).parents('tr.tr-sub-detail').attr(
            "data-jenis-kelamin"),
        'kode_barang' : $(elm).parents('tr.tr-sub-detail').attr(
            "data-kode-barang"),
        'id_kavling' : $(elm).parents('tr.tr-sub-detail').attr(
            "data-id-kavling"),
        // 'user_gudang' : user_gudang,
        'jumlah' : timbangan_zak,
        'jumlah_konversi_timbang' : timbangan_zak,
        'berat' : $(elm).parents('tr.tr-sub-detail').find(
            'td input.timbangan_kg').val()
    });

    //if (rencana_kirim > timbangan_zak) {
        if (data_result == 1) {
            if (rencana_kirim > timbangan_zak) {
                konfirmasi_dialog(data_result, function(lanjut) {

                    if (lanjut == 1) {

                        fingerprint(elm, data_ke, _params);
                    } else {
                        $(elm).focus().select();
                    }
                });
            }
            else{
                fingerprint(elm, data_ke, _params);
            }
        } else {
            konfirmasi_dialog(
                data_result,
                function(konfirmasi) {

                    if (konfirmasi == 1) {
                        var _message = '<div class="form-group form-horizontal new-line">';
                        _message += '<div class="form-group">';
                        _message += '<label class="col-sm-5 control-label">Konversi Timbangan (Sak)</label>';
                        _message += '<div class="col-sm-5">';
                        _message += '<label class="control-label">'
                        + timbangan_zak + '</label>';
                        _message += '</div></div>';
                        _message += '<div class="form-group">';
                        _message += '<label class="col-sm-5 control-label">Jumlah Sak Aktual</label>';
                        _message += '<div class="col-sm-5">';
                        _message += '<input type="text" placeholder="Jumlah Sak Aktual" id="jumlah_sak_aktual" class="form-control">';
                        _message += '</div></div>';
                        // _message += '<div class="form-group"><div
                        // class="col-sm-12 text-center"><button class="btn
                        // btn-default">Simpan</button></div></div>';
                        _message += '</div>';
                        var box_status = 0;
                        var box = bootbox
                        .dialog({
                            message : _message,
                            title : "Konfirmasi Sak",
                            buttons : {
                                success : {
                                    label : "Simpan",
                                    className : "btn-success",
                                    callback : function() {
                                        var jumlah_aktual_zak = $(
                                            '#jumlah_sak_aktual')
                                        .val();
                                        if (!jumlah_aktual_zak
                                            || jumlah_aktual_zak <= 0) {
                                            $('#jumlah_sak_aktual')
                                            .focus()
                                            .select();
                                            toastr
                                            .error(
                                                'Jumlah Aktual Sak harus diisi.',
                                                'Peringatan');
                                            return false;
                                        } else {
                                            _params[0]['jumlah_aktual_zak'] = jumlah_aktual_zak;

                                            // console.log(_params);
                                            box_status = 1;
                                            return true;

                                        }
                                    }
                                }
                            }
                        });

                        box.bind('shown.bs.modal', function() {
                            $('#jumlah_sak_aktual').numeric({
                                allowPlus : false, // Allow the + sign
                                allowMinus : false, // Allow the - sign
                                allowThouSep : false, // Allow the
                                // thousands
                                // separator,
                                // default is the
                                // comma eg 12,000
                                allowDecSep : false
                            // Allow the decimal separator, default is the
                            // fullstop eg 3.141
                            });
                            $('#jumlah_sak_aktual').focus().select();
                        });

                        box.bind('hidden.bs.modal', function() {
                            if (box_status == 1) {
                                data_result = 1;
                                konfirmasi_dialog(data_result, function(
                                    lanjut) {

                                    if (lanjut == 1) {

                                        fingerprint(elm, data_ke, _params);
                                    } else {
                                        $(elm).focus().select();
                                    }
                                });
                            } else {
                                $(elm).focus().select();
                            }

                        });
                    } else {
                        $(elm).focus().select();
                    }
                });
        }
    /*} else {

        // console.log(_params);
        fingerprint(elm, data_ke, _params);
    }*/
// }
}

function konfirmasi_dialog(data_result, callback) {
    var konfirmasi = 0;
    var _message = '<div class="form-group form-horizontal new-line">';
    if (data_result == 0) {
        _message += '<label>Jumlah Timbangan (Sak) diluar Batas Toleransi. Apakah akan Melanjutkan Proses Simpan ?</label>';
    } else {
        _message += '<label>Jumlah Timbangan (Sak) kurang dari rencana Kirim. Apakah akan Melanjutkan Proses Simpan ?</label>';
    }
    _message += '</div>';
    var box = bootbox.dialog({
        message : _message,
        title : "",
        buttons : {
            danger : {
                label : "Tidak",
                className : "btn-danger",
                callback : function() {
                    return true;
                }
            },
            success : {
                label : "Ya",
                className : "btn-success",
                callback : function() {
                    konfirmasi = 1;
                    return true;
                }
            }
        }
    });

    box.bind('hidden.bs.modal', function() {
        callback(konfirmasi);
    })
}

function fingerprint(elm, data_ke, _params) {
    var _message = '<div class="form-group form-horizontal new-line">';
    _message += '<div class="form-group">';
    _message += '<div class="col-sm-12">';
    _message += '<select class="form-control" id="user_gudang" placeholder="User Gudang" name="user_gudang">';
    _message += '<option value=""></option>';
    $.each(daftar_user_gudang, function(key, value) {
        _message += '<option value="' + value.kode_pegawai + '">'
        + value.nama_pegawai + '</option>';
    });
    _message += '</select></div>';
    _message += '</div>';
    var user_gudang;
    var box = bootbox.dialog({
        message : _message,
        title : "Fingerprint",
        buttons : {
            success : {
                label : "Simpan",
                className : "btn-success",
                callback : function() {
                    user_gudang = $('#user_gudang').val();
                    if (!user_gudang) {
                        $('#user_gudang').focus().select();
                        messageBox('', '', 'Verifikasi Gagal.');
                        return false;
                    } else {
                        _params[0]['user_gudang'] = user_gudang;

                        // console.log(_params);

                        return true;
                    }
                }
            }
        }
    });

    box.bind('hidden.bs.modal', function() {
        if (user_gudang) {
            simpan_konfirmasi_dialog(elm, data_ke, _params);

        }
    })
}

function simpan_konfirmasi_dialog(elm, data_ke, _params) {
    // console.log(_params);

    simpan_konfirmasi(_params, function(result) {
        if (result.result == 1) {
            // console.log(result);
            get_data_detail_pengambilan(_params, 1, data_ke);
            // $('#transaction-table table tbody
            // tr.tr-detail[data-ke="'+data_ke+'"] td
            // input.timbangan_kg').attr('readonly',true);
            // var html_penerima =
            // "<p>"+result.data.user_gudang+"</p><p>"+convert_month(result.data.tgl_buat)+"
            // "+result.data.wkt_buat+"</p>";
            // $('#transaction-table table tbody
            // tr.tr-detail[data-ke="'+data_ke+'"] td
            // button.btn-selesai').parent().html(html_penerima);
            /*
			 * var count = 0; $.each($('#transaction-table table
			 * tbody').find('tr.tr-detail[data-ke="'+data_ke+'"]'),function(){
			 * var timbangan_kg = $(this).find('td input.timbangan_kg').val();
			 * if(timbangan_kg){ count++; } }); var data_count =
			 * $(elm).parents('tr.tr-sub-detail').attr('data-count');
			 * if(data_count == count){ //console.log(user_gudang);
			 * //console.log(tgl_buat); }
			 */
            toastr.success("Konfirmasi berhasil.", "Berhasil");
        } else {
            toastr.error("Konfirmasi gagal.", "Peringatan");
        }
    });

}

function print() {
    toastr.warning('Masih Proses...', 'Peringatan');
}

function format_datepicker(date) {
    var split = date.split(" ");
    return split[2] + '/'
    + $.datepicker.regional['id'].monthNamesShort.indexOf(split[1])
    + '/' + split[0];
}

function generate(e) {
    var kode_farm = $(e).attr("data-kode-farm");
    var tanggal_kirim = $(e).attr("data-tanggal-kirim");
    var tanggal_kebutuhan_awal = $(e).attr("data-tanggal-kebutuhan-awal");
    var tanggal_kebutuhan_akhir = $(e).attr("data-tanggal-kebutuhan-akhir");

    var tgl_kirim = new Date(format_datepicker(tanggal_kirim));
    var tgl_keb_awal = new Date(format_datepicker(tanggal_kebutuhan_awal));
    var tgl_keb_akhir = new Date(format_datepicker(tanggal_kebutuhan_akhir));

    var time_tgl_keb_akhir = tgl_keb_akhir.getTime() - tgl_kirim.getTime();
    var diff_tgl_keb_akhir = Math.ceil(time_tgl_keb_akhir / (1000 * 3600 * 24));

    var time_tgl_keb_awal = tgl_keb_akhir.getTime() - tgl_keb_awal.getTime();
    var diff_tgl_keb_awal = Math.ceil(time_tgl_keb_awal / (1000 * 3600 * 24));

    // console.log(diff_tgl_keb_akhir+'dan'+diff_tgl_keb_awal)

    // if (kode_farm && tanggal_kebutuhan_awal && tanggal_kebutuhan_akhir &&
    // diff_tgl_keb_akhir <= 5 && diff_tgl_keb_awal >= 0) {

    ajax_generate(e, kode_farm, tanggal_kirim, tanggal_kebutuhan_awal,
        tanggal_kebutuhan_akhir);

// } else {
// toastr.error('Range tanggal tidak valid.', 'Peringatan');
// }

}

function ajax_generate(e, kode_farm, tanggal_kirim, tanggal_kebutuhan_awal,
    tanggal_kebutuhan_akhir) {
    $
    .ajax({
        type : "POST",
        url : "pengambilan_barang/main/simpan_generate_permintaan",
        data : {
            kode_farm : kode_farm,
            tanggal_kirim : tanggal_kirim,
            tanggal_kebutuhan_awal : tanggal_kebutuhan_awal,
            tanggal_kebutuhan_akhir : tanggal_kebutuhan_akhir
        },
        dataType : 'json',
        success : function(data) {
            if (data.result == 1) {
                $(e).parents('tr').find('td.first').attr(
                    'data-generate', '0');
                $(e).parents('tr').find('td.first').attr(
                    'data-no-order', data.no_order);
                $(e).parents('tr').find('td.no_pengambilan').text(
                    data.no_order);
                $(e).parents('tr').find('td.jml_belum_proses').text(
                    data.jumlah_belum_proses);
                $(e).parents('tr').find('td.jml_kebutuhan').text(
                    data.jml_pp);
                // var html = "<a
                // href='pengambilan_barang/transaksi/cetak_picking_list_pdf/"+data.no_order+"'
                // class='link' target='_blank'>Cetak Picking List</a>";
                // var html = "<a
                // href='pengambilan_barang/transaksi/cetak_daftar_pengambilan?no_order="+data.no_order+"&pick=1'
                // class='link' target='_blank'>Cetak Picking List</a>";
                var html = "<span style='color:#428bca;' href='#pengambilan_barang/transaksi' class='btn link' onclick='cetak_picking_list(this)'>Cetak Picking List</span>";
                $(e).parents('td').html(html);
                toastr.success('Generate permintaan berhasil',
                    'Berhasil');
            } else if (data.result == 2) {
                toastr.error('Tidak ada kavling yang tersedia.',
                    'Peringatan');
            } else if (data.result == 3) {
                toastr.error('Tanggal kebutuhan '
                    + tanggal_kebutuhan_awal + ' s/d '
                    + tanggal_kebutuhan_akhir
                    + ' sudah dilakukan generate permintaan.',
                    'Peringatan');
            } else {
                toastr.error('Generate permintaan Gagal', 'Peringatan');
            }

        // $("#contain-daftar-barang").html(data);
        // simpan_baru(kode_farm, tanggal_kirim,
        // tanggal_kebutuhan_awal, tanggal_kebutuhan_akhir);
        /*
					 * $("#btn-tambah-barang").removeClass('hide'); var status =
					 * $('#status').val(); if (status == 'D' || !status) {
					 * $('#btn-baru').removeAttr('disabled'); }
					 */
        }
    });
}

function get_data_detail_pengambilan(e, _tab_active, data_ke) {
    var no_order = $(e).find('td.first').attr('data-no-order');
    var kode_farm = $(e).find('td.first').attr('data-kode-farm');
    var _generate = $(e).find('td.first').attr('data-generate');
    if (_generate == 1) {
        toastr.error('Belum dilakukan generate permintaan.', 'Peringatan');
    } else {
        if ((!no_order || !kode_farm) && (typeof e[0] != 'undefined')) {
            kode_farm = e[0]['kode_farm'];
            no_order = e[0]['no_order'];
        }
        if (no_order) {
            $
            .ajax({
                type : "POST",
                url : "pengambilan_barang/transaksi/view",
                data : {
                    no_order : no_order,
                    tab_active : _tab_active
                },
                success : function(data) {
                    $("#main_content").html(data);
                    if (data_ke) {
                        // $('tr.tr-header[data-ke="'+data_ke+'"]').addClass('mark_row');
                        // $('tr.tr-detail[data-ke="'+data_ke+'"]').removeClass('hide');
                        $('tr.tr-header[data-ke="' + data_ke + '"]')
                        .dblclick();
                    }
                }
            });
        }
    }
}

function cetak_picking_list(e) {
    // var tanggal_kirim = $("#tanggal-kirim").val();
    var no_order = $(e).parents('tr').find('td.first').attr('data-no-order');
    var kode_farm = $(e).parents('tr').find('td.first').attr('data-kode-farm');
    if (kode_farm && no_order) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cetak_picking_list",
            data : {
                no_order : no_order,
                kode_farm : kode_farm
            },
            success : function(data) {
                // $("#main_content").html(data);
                var _message = data;
                var box = bootbox.dialog({
                    message : _message,
                    title : "Pengambilan Barang",

                    buttons : {
                        danger : {
                            label : "Keluar",
                            className : "btn-danger",
                            callback : function() {
                                return true;
                            }
                        }
                    },
                    className : "very-large"
                });
            }
        });
    }
}

function cetak_picking_list_pdf(no_order) {
    if (no_order) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cetak_picking_list_pdf",
            data : {
                no_order : no_order
            },
            success : function(data) {
                window.open(data, '_blank');
            }
        });
    }
}

function kontrol_chekbox(elm){
    $(elm).is(':checked') ? $(elm).val('1') : $(elm).val('0');

    get_data_pengambilan();
}

function get_data_pengambilan() {
    var tanggal_kirim_awal = $("#tanggal-kirim-awal").val();
    var tanggal_kirim_akhir = $("#tanggal-kirim-akhir").val();
    var checkbox_normal = $("#checkbox_normal").val();
    var checkbox_retur = $("#checkbox_retur").val();
    var checkbox_belum_proses = $("#checkbox_belum_proses").val();
    //if (tanggal_kirim_awal && tanggal_kirim_akhir) { // } &&
        // (tanggal_kirim_awal
        // <=
        // tanggal_kirim_akhir))
        // {
        $
        .ajax({
            type : "POST",
            url : "pengambilan_barang/main/get_data_pengambilan",
            data : {
                tanggal_kirim_awal : tanggal_kirim_awal,
                tanggal_kirim_akhir : tanggal_kirim_akhir,
                checkbox_normal : checkbox_normal,
                checkbox_retur : checkbox_retur,
                checkbox_belum_proses : checkbox_belum_proses
            },
            dataType : 'json',
            success : function(data) {
                if (data == 2) {
                    toastr.error('Range tanggal kirim tidak valid.',
                        'Peringatan');
                } else if (data) {
                    $("#picking-list-table table tbody").html('');
                    var append_text = "";
                    $
                    .each(
                        data,
                        function(key, value) {
                            // if (value.status_order !=
                            // 'D') {
                            append_text += "<tr class='tr_order' ondblclick='get_data_detail_pengambilan(this,1)'>";
                            var no_pengambilan = (value.no_order) ? value.no_order
                            : '-';
                            append_text += "<td class='no_pengambilan'>"
                            + no_pengambilan
                            + "</td>";
                            append_text += "<td class='first' data-no-order='"
                            + value.no_order
                            + "' data-kode-farm='"
                            + value.kode_farm
                            + "' data-generate='"
                            + value.generate + "'>";
                            var tgl_kirim = (value.tgl_kirim) ? convert_month(value.tgl_kirim) : '-';
                            append_text += ""
                            + tgl_kirim
                            + "</td>";
                            var tgl_keb = (value.tgl_keb_awal) ? convert_month(value.tgl_keb_awal)+ " s/d "+ convert_month(value.tgl_keb_akhir) : '-';
                            append_text += "<td>"
                            + tgl_keb
                            + "</td>";
                            append_text += "<td class='jml_kebutuhan'>"
                            + value.jumlah_kebutuhan
                            + "</td>";
                            append_text += "<td class='jml_belum_proses'>"
                            + value.jumlah_belum_proses
                            + "</td>";
                            var no_referensi = (value.no_referensi) ? value.no_referensi : '-';
                            append_text += "<td>"
                            + no_referensi + "</td>";
                            if (value.generate == 1) {
                                append_text += "<td><span style='color:#428bca;' data-kode-farm='"
                                + value.kode_farm
                                + "'";
                                append_text += "data-tanggal-kirim='"
                                + convert_month(
                                    value.tgl_kirim,
                                    1) + "'";
                                append_text += "data-tanggal-kebutuhan-awal='"
                                + convert_month(
                                    value.tgl_keb_awal,
                                    1) + "'";
                                append_text += "data-tanggal-kebutuhan-akhir='"
                                + convert_month(
                                    value.tgl_keb_akhir,
                                    1) + "'";
                                append_text += "href='#' class='btn link' onclick='generate(this)'>Generate</span></td>";
                            } else {
                                // append_text += "<td><a
                                // href='pengambilan_barang/transaksi/cetak_picking_list_pdf/"+value.no_order+"'
                                // class='link'
                                // target='_blank'>Cetak
                                // Picking List</a></td>";
                                // append_text += "<td><a
                                // href='pengambilan_barang/transaksi/cetak_daftar_pengambilan?no_order="+value.no_order+"&pick=1'
                                // class='link'
                                // target='_blank'>Cetak
                                // Picking List</a></td>";

                                append_text += "<td><span style='color:#428bca;' href='#pengambilan_barang/transaksi' class='btn link' onclick='cetak_picking_list(this)'>Cetak Picking List</span></td>";
                            }
                            append_text += "</tr>";
                        // }
                        });
                    $("#picking-list-table table tbody").append(
                        append_text);
                }
            }
        });
    //} else {
    //    toastr.error('Range tanggal kirim tidak valid.', 'Peringatan');
    //}
}

function kontrol_option(e) {
    var data_ke = $(e).parents("tr").attr("data-ke");
    var disabled = $('tr[data-ke="' + data_ke + '"] .berat').attr('disabled');
    var berat = parseFloat($('tr[data-ke="' + data_ke + '"] .berat').val());
    // if(!berat || berat==0 || berat=='NaN'){
    if (typeof disabled == 'undefined') {
        $("#btn-konfirmasi").attr('disabled', true);
        var checked = 0;
        $.each($('#transaction-table table tbody').find('tr'), function() {
            var tmp_data_ke = $(this).attr("data-ke");
            // var tmp_berat = parseFloat($('tr[data-ke="'+tmp_data_ke+'"]
            // .berat').val());
            var tmp_disabled = $('tr[data-ke="' + tmp_data_ke + '"] .berat')
            .attr('disabled');
            // console.log(tmp_data_ke+" dan "+tmp_berat)
            // if(!tmp_berat || tmp_berat==0 || tmp_berat=='NaN'){
            if (typeof tmp_disabled == 'undefined') {
                $('tr[data-ke="' + tmp_data_ke + '"] .berat').val("0");
            }
        })
        $('tr[data-ke="' + data_ke + '"] .berat').focus().select();
    }
/*
	 * $("#btn-konfirmasi").attr('disabled', true); var data_ke =
	 * $(e).parents("tr").attr("data-ke"); var checked = 0; var next = 1; var
	 * tmp_berat = $('tr[data-ke="'+next+'"] .berat').val();
	 * $(".berat").val("0"); $('tr[data-ke="'+next+'"] .berat').val(tmp_berat);
	 * while(next < data_ke){ if(!$('.radio[data-ke="'+next+'"]').is(':checked') &&
	 * $('.radio[data-ke="'+next+'"]').attr("data-ke")){ checked++ } next++; }
	 * if(checked > 0){ $(e).attr("checked",false); toastr.error('Harus
	 * urut.','Peringatan'); } else{ $('tr[data-ke="'+data_ke+'"] .berat')
	 * .focus() .select(); }
	 */
}

function kontrol_berat(e) {
    var data_ke = $(e).parents("tr").attr("data-ke");
    var berat = parseFloat($(e).val());
    if ($('tr[data-ke="' + data_ke + '"] .radio').is(":checked") && berat
        && berat > 0) {
        $("#btn-konfirmasi").attr('disabled', false);
        $("#btn-konfirmasi").focus();
    } else {
        toastr.error("Konfirmasi gagal.", "Peringatan");
        $(e).val("0");
        $(e).focus();
    }
}

function simpan_konfirmasi(data, callback) {
    if (data.length == 1) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/simpan_konfirmasi",
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

function cek_kode_verifikasi_kavling(data) {
    var tmp_data;
    if (data.length == 1) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cek_kode_verifikasi_kavling",
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
    var data_ke;
    var _params = [];
    $.each($('#transaction-table tbody').find('input[type="radio"]'),
        function() {
            if ($(this).is(":checked")) {
                data_ke = $(this).parents("tr").attr("data-ke");
                _params.push({
                    'tanggal_kirim' : $('tr[data-ke="' + data_ke + '"]')
                    .attr("data-tanggal-kirim"),
                    'no_reg' : $('tr[data-ke="' + data_ke + '"]').attr(
                        "data-no-reg"),
                    'no_order' : $('tr[data-ke="' + data_ke + '"]').attr(
                        "data-no-order"),
                    'kode_farm' : $('tr[data-ke="' + data_ke + '"]').attr(
                        "data-kode-farm"),
                    'no_pallet' : $('tr[data-ke="' + data_ke + '"]').attr(
                        "data-no-pallet"),
                    'kode_kandang' : $(
                        'tr[data-ke="' + data_ke + '"] .kode-kandang')
                    .text(),
                    'jenis_kelamin' : $('tr[data-ke="' + data_ke + '"]')
                    .attr("data-jenis-kelamin"),
                    'no_kavling' : $(
                        'tr[data-ke="' + data_ke + '"] .id-kavling')
                    .text(),
                    'kode_barang' : $(
                        'tr[data-ke="' + data_ke + '"] .kode-barang')
                    .text(),
                    'id_kavling' : $(
                        'tr[data-ke="' + data_ke + '"] .id-kavling')
                    .text(),
                    'jumlah' : $('tr[data-ke="' + data_ke + '"] .jumlah')
                    .text(),
                    'berat' : $('tr[data-ke="' + data_ke + '"] .berat')
                    .val(),
                    'kode_verifikasi' : ''
                });
            }
        })
    var toleransi = 50;
    var zak = Math.round(parseFloat($('tr[data-ke="' + data_ke + '"] .berat')
        .val())
    / toleransi);
    var jumlah = parseInt($('tr[data-ke="' + data_ke + '"] .jumlah').text());
    var _berat = parseFloat($('tr[data-ke="' + data_ke + '"] .berat').val());
    var _min = parseFloat($('tr[data-ke="' + data_ke + '"] .berat').attr(
        'data-min'));
    var _max = parseFloat($('tr[data-ke="' + data_ke + '"] .berat').attr(
        'data-max'));
    // console.log(zak +" = "+ jumlah);
    // if (zak == jumlah) {
    if (_berat >= _min && _berat <= _max) {
        var _message = '<div class="form-group form-horizontal new-line">';
        _message += '<label class="col-sm-3 control-label" for="inputEmail3">Kode Verifikasi</label>';
        _message += '<div class="col-sm-8">';
        _message += '<input type="password" placeholder="Kode Verifikasi" id="kode_verifikasi" class="form-control" autofocus>';
        _message += '</div>';
        _message += '</div>';
        var box = bootbox
        .dialog({
            message : _message,
            title : "Konfirmasi",
            buttons : {
                danger : {
                    label : "Batal",
                    className : "btn-danger",
                    callback : function() {
                        return true;
                    }
                },
                success : {
                    label : "OK",
                    className : "btn-success",
                    callback : function() {
                        var kode_verifikasi = $("#kode_verifikasi")
                        .val();
                        if (kode_verifikasi) {
                            _params[0]['kode_verifikasi'] = kode_verifikasi;
                            var cek = cek_kode_verifikasi_kavling(_params);
                            $
                            .when(cek)
                            .done(
                                function(result) {
                                    if (result == 1) {
                                        simpan_konfirmasi(
                                            _params,
                                            function(
                                                result) {
                                                if (result == 1) {
                                                    $(
                                                        "#btn-konfirmasi")
                                                    .attr(
                                                        'disabled',
                                                        true);
                                                    $(
                                                        'tr[data-ke="'
                                                        + data_ke
                                                        + '"] .radio')
                                                    .remove();
                                                    $(
                                                        'tr[data-ke="'
                                                        + data_ke
                                                        + '"] .berat')
                                                    .attr(
                                                        'disabled',
                                                        true);
                                                    $(
                                                        'tr[data-ke="'
                                                        + data_ke
                                                        + '"] .berat')
                                                    .removeClass(
                                                        'berat');
                                                    $(
                                                        'tr[data-ke="'
                                                        + data_ke
                                                        + '"] .berat')
                                                    .removeAttr(
                                                        'data-ke');
                                                    $(
                                                        'tr[data-ke="'
                                                        + data_ke
                                                        + '"] .keterangan')
                                                    .text(
                                                        'Picked');
                                                    get_data_detail_pengambilan(
                                                        _params,
                                                        1);
                                                    toastr
                                                    .success(
                                                        "Konfirmasi berhasil.",
                                                        "Berhasil");
                                                    box
                                                    .modal('hide');
                                                } else {
                                                    toastr
                                                    .error(
                                                        "Konfirmasi gagal.",
                                                        "Peringatan");
                                                }
                                            })
                                    } else {
                                        toastr
                                        .error(
                                            "Verifikasi kode gagal.",
                                            "Peringatan");
                                    }
                                })
                            return false;
                        } else {
                            toastr.error(
                                "Kode verifikasi harus diisi.",
                                "Peringatan");
                            return false;
                        }
                    }
                }
            }
        });

        box.bind('shown.bs.modal', function() {
            box.find("input#kode_verifikasi").focus();
        });

        box.bind('hidden.bs.modal', function() {
            $('tr[data-ke="' + data_ke + '"] .berat').select();
        });
    } else {
        // toastr.error("Konversi berat ke zak tidak sesuai.", "Peringatan");

        bootbox.dialog({
            message : 'Qty tidak sesuai.',
            title : "Error",
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
        $('tr[data-ke="' + data_ke + '"] .berat').select();
    }
}
