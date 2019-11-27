( function() {'use strict';

    $('div').on('click', 'a.btn', function(e) {
        // //console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })
    $("#tanggal-kirim").datepicker({
        dateFormat : 'dd MM yy',
    });

    // $("#tanggal-kebutuhan-awal").datepicker({
    // dateFormat : 'dd MM yy',
    // });

    $("#tanggal-kebutuhan-akhir").datepicker({
        dateFormat : 'dd MM yy',
        // minDate: -5,
        // maxDate: 5,
    });

    $(".berat.old").keydown(function(event) {
        return false;
    });

}())

function filter(e) {
    $.each($('#tbl-tambah-barang tbody').find('tr'), function() {
        $(this).show();
    })

    $.each($('#tbl-tambah-barang thead').find('.filter'), function() {
        var value = $(this).val();
        if (value) {
            var name = $(this).attr("name");
            console.log(name)
            if (name != 'kode-barang' && name != 'tipe-barang') {
                $('#tbl-tambah-barang tbody tr:visible .f' + name + ':not(:contains("' + value.toUpperCase() + '"))').parent().hide();
            } else {
                if (value != 'Semua') {
                    $('#tbl-tambah-barang tbody tr:visible .f' + name + ':not(:contains("' + value + '"))').parent().hide();
                }
            }
        }
    })
}

function get_data_detail_order(e) {
    var no_order = $(e).parents('tr').find('td:first').attr('data-no-order');
    var kode_farm = $(e).parents('tr').find('td:first').attr('data-kode-farm');
    var status = $(e).parents('tr').find('td:first').attr('data-status');
    var tanggal_kirim = $(e).text();
    var tanggal_kebutuhan_awal = $(e).parents('tr').find('td:first').attr('data-tgl-kebutuhan-awal');
    var tanggal_kebutuhan_akhir = $(e).parents('tr').find('td:first').attr('data-tgl-kebutuhan-akhir');
    if (kode_farm && tanggal_kebutuhan_awal && tanggal_kebutuhan_akhir) {
        $.ajax({
            type : "POST",
            url : "generate_permintaan/main/baru",
            data : {
                kode_farm : kode_farm,
                no_order : no_order,
                status : status,
                tanggal_kirim : tanggal_kirim,
                tanggal_kebutuhan_awal : tanggal_kebutuhan_awal,
                tanggal_kebutuhan_akhir : tanggal_kebutuhan_akhir
            },
            success : function(data) {
                $("#main_content").html(data);
                generate();
            }
        });
    }
}

function selected(e) {
    $('#tbl-tambah-barang tbody').find('tr').removeClass('selected');
    $(e).addClass('selected');
    $('.modal').modal('hide');
    var kode_barang = $('#tbl-tambah-barang tbody tr.selected .fkode-barang').text();
    var data_terakhir;
    var tambah = 0;
    $.each($('#tbl-daftar-barang tbody').find('tr.tmp_header_barang'), function() {
        data_terakhir = $(this).attr('data-ke');
        var tmp_kode_barang = $(this).attr('data-kode-barang');
        if (tmp_kode_barang == kode_barang) {
            tambah = tambah + 1;
        }
    })
    if (tambah == 0) {
        // console.log(data_terakhir)
        var kode_farm = $("#kode-farm").val();
        var tanggal_kirim = $("#tanggal-kirim").val();
        var tanggal_kebutuhan_awal = $("#tanggal-kebutuhan-awal").val();
        var tanggal_kebutuhan_akhir = $("#tanggal-kebutuhan-akhir").val();

        var tgl_kirim = new Date(format_datepicker(tanggal_kirim));
        var tgl_keb_awal = new Date(format_datepicker(tanggal_kebutuhan_awal));
        var tgl_keb_akhir = new Date(format_datepicker(tanggal_kebutuhan_akhir));

        var time_tgl_keb_akhir = tgl_keb_akhir.getTime() - tgl_kirim.getTime();
        var diff_tgl_keb_akhir = Math.ceil(time_tgl_keb_akhir / (1000 * 3600 * 24));

        var time_tgl_keb_awal = tgl_keb_akhir.getTime() - tgl_keb_awal.getTime();
        var diff_tgl_keb_awal = Math.ceil(time_tgl_keb_awal / (1000 * 3600 * 24));

        $.ajax({
            type : "POST",
            url : "generate_permintaan/main/tambah_daftar_barang",
            data : {
                kode_barang : kode_barang,
                data_terakhir : data_terakhir,
                kode_farm : kode_farm,
                tanggal_kebutuhan_awal : tanggal_kebutuhan_awal,
                tanggal_kebutuhan_akhir : tanggal_kebutuhan_akhir
            },
            success : function(data) {
                $("table#tbl-daftar-barang tbody.main-tbody").append(data);
            }
        });
    } else {
        toastr.error('Kode barang sudah ada dalam daftar.', 'Peringatan');
    }
}

function kontrol_jumlah_pp(e) {
    var tmp_jml_kebutuhan = $(e).parents('tr.data-tgl-kebutuhan').attr('data-jml-performance');
    var tmp_jml_pp = $(e).parents('tr.data-tgl-kebutuhan').attr('data-jml-pp');
    var jml_pp = $(e).val();
    if (parseInt(jml_pp) > parseInt(tmp_jml_kebutuhan)) {
        $(e).val(tmp_jml_pp);
        toastr.error('Jumlah PP harus kurang dari sama dengan jumlah kebutuhan.', 'Peringatan');
    }
}

function show_detail(e, header) {
    var data_ke = $(e).attr('data-ke');
    var kelas = (header == 1) ? 'header_barang' : 'header_kandang';
    // //console.log('.'+kelas+'[data-ke="'+data_ke+'"]');
    if ($('.' + kelas + '[data-ke="' + data_ke + '"]').hasClass('hide')) {
        $('.' + kelas + '[data-ke="' + data_ke + '"]').fadeIn('slow');
        $('.' + kelas + '[data-ke="' + data_ke + '"]').removeClass('hide');
        $(e).find('span').removeClass('glyphicon-chevron-right');
        $(e).find('span').addClass('glyphicon-chevron-down');
    } else {
        $('.' + kelas + '[data-ke="' + data_ke + '"]').fadeOut('slow');
        $('.' + kelas + '[data-ke="' + data_ke + '"]').addClass('hide');
        $(e).find('span').removeClass('glyphicon-chevron-down');
        $(e).find('span').addClass('glyphicon-chevron-right');
    }
}

function tambah_barang() {
    $.ajax({
        type : "POST",
        url : "generate_permintaan/main/tambah_barang",
        data : {
        },
        success : function(data) {
            var box = bootbox.dialog({
                title : "Master - Barang",
                className : "very-large",
                message : data,
            });
        }
    });
}

function simpan_baru(kode_farm, tanggal_kirim, tanggal_kebutuhan_awal, tanggal_kebutuhan_akhir) {
    var _message = '<div class="form-horizontal">';
    _message += '<label class="text-center">Apakah anda yakin akan melanjutkan proses Generate Permintaan ini ?</label>';
    _message += '</div>';
    var box1 = bootbox.dialog({
        message : _message,
        // title: "Konfirmasi",
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
                    var _params = [];
                    var _ok = [];
                    _ok.push({
                        'kode_farm' : kode_farm,
                        'tanggal_kirim' : tanggal_kirim,
                        'tanggal_kebutuhan_awal' : tanggal_kebutuhan_awal,
                        'tanggal_kebutuhan_akhir' : tanggal_kebutuhan_akhir,
                    });
                    _params.push({
                        'data_order_kandang' : _ok,
                    })
                    cek_order_kandang(_params, function(result) {
                        if (result.ada == 0) {
                            var _params1 = [];
                            // var _ok = [];
                            var _okd = [];
                            var _oke = [];
                            /*
                             * _ok.push({ 'kode_farm' : $('#kode-farm').val(),
                             * 'tanggal_kirim' : $('#tanggal-kirim').val(),
                             * 'tanggal_kebutuhan_awal' :
                             * $('#tanggal-kebutuhan-awal').val(),
                             * 'tanggal_kebutuhan_akhir' :
                             * $('#tanggal-kebutuhan-akhir').val(), });
                             */
                            var tmp_no_reg = [];
                            $.each($('#tbl-daftar-barang').find('tr.header_barang tr.data-kandang'), function() {
                                var no_reg = $(this).attr('data-no-reg');
                                var umur = $(this).attr('data-umur');
                                var tgl_lhk = $(this).attr('data-lhk');
                                if ($.inArray(no_reg, tmp_no_reg) < 0) {
                                    tmp_no_reg.push(no_reg);
                                    _okd.push({
                                        'no_reg' : no_reg,
                                        'umur' : umur,
                                        'tgl_lhk' : tgl_lhk,
                                    });
                                }

                            })
                            var tmp_kode_barang = [];
                            $.each($('#tbl-daftar-barang').find('tr.tmp_header_barang'), function() {
                                var kode_barang = $(this).attr('data-kode-barang');
                                if ($.inArray(kode_barang, tmp_kode_barang) < 0) {
                                    tmp_kode_barang.push(kode_barang);
                                    $.each($(this).next().find('tr.data-kandang'), function() {
                                        var no_reg = $(this).attr('data-no-reg');
                                        $.each($(this).next().find('table.tbl-daftar-tgl-kebutuhan-jantan tr.data-tgl-kebutuhan'), function() {
                                            var tgl_kebutuhan = $(this).attr('data-tgl-kebutuhan');
                                            var jml_performance = $(this).attr('data-jml-performance');
                                            var jml_pp = $(this).find('input.jumlah_pp').val();
                                            _oke.push({
                                                'kode_barang' : kode_barang,
                                                'no_reg' : no_reg,
                                                'jenis_kelamin' : 'J',
                                                'tgl_kebutuhan' : tgl_kebutuhan,
                                                'jml_performance' : jml_performance,
                                                'jml_pp' : jml_pp,
                                            });
                                        })
                                        $.each($(this).next().find('table.tbl-daftar-tgl-kebutuhan-betina tr.data-tgl-kebutuhan'), function() {
                                            var tgl_kebutuhan = $(this).attr('data-tgl-kebutuhan');
                                            var jml_performance = $(this).attr('data-jml-performance');
                                            var jml_pp = $(this).find('input.jumlah_pp').val();
                                            _oke.push({
                                                'kode_barang' : kode_barang,
                                                'no_reg' : no_reg,
                                                'jenis_kelamin' : 'B',
                                                'tgl_kebutuhan' : tgl_kebutuhan,
                                                'jml_performance' : jml_performance,
                                                'jml_pp' : jml_pp,
                                            });
                                        })
                                    })
                                }

                            })
                            _params1.push({
                                'data_order_kandang' : _ok,
                                'data_order_kandang_d' : _okd,
                                'data_order_kandang_e' : _oke,
                            });
                            simpan_generate_permintaan(_params1, function(result) {
                                if (result.success == 1) {
                                    /*
                                     * $('#btn-release').removeAttr('disabled');
                                     * $('#no-order').val(result.no_order);
                                     * $('#status').val('D'); generate();
                                     */
                                    release(kode_farm, result.no_order);
                                    toastr.success('Generate permintaan berhasil.', 'Berhasil');
                                } else {
                                    toastr.error('Generate permintaan gagal.', 'Peringatan');
                                }
                            });
                        } else {
                            toastr.error('Generate permintaan sudah pernah dilakukan.', 'Peringatan');
                            /*
							 var data_ke;
							 var blank = 0;
							 var _message = '<div class="form-horizontal">';
							 _message += '<label class="text-center">Data LHK dan Pakan sudah pernah disimpan, Apakah anda akan meng-update data LHK dan Pakan tersebut ?</label>';
							 _message += '</div>';
							 var box2 = bootbox.dialog({
							 message : _message,
							 // title: "Konfirmasi",
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
							 var _params2 = [];
							 var _oke = [];
							 var tmp_kode_barang = [];
							 $.each($('#tbl-daftar-barang').find('tr.tmp_header_barang'), function() {
							 var kode_barang = $(this).attr('data-kode-barang');
							 if ($.inArray(kode_barang, tmp_kode_barang) < 0) {
							 tmp_kode_barang.push(kode_barang);
							 $.each($(this).next().find('tr.data-kandang'), function() {
							 var no_reg = $(this).attr('data-no-reg');
							 data_ke = $(this).attr('data-ke');
							 var no_order = result.no_order;
							 $.each($(this).next().find('table.tbl-daftar-tgl-kebutuhan-jantan tr.data-tgl-kebutuhan'), function() {
							 var tgl_kebutuhan = $(this).attr('data-tgl-kebutuhan');
							 var jml_performance = $(this).attr('data-jml-performance');
							 var tmp_jml_pp = $(this).attr('data-jml-pp');
							 var jml_pp = $(this).find('input.jumlah_pp').val();
							 if (parseInt(tmp_jml_pp) != parseInt(jml_pp)) {
							 var keterangan = $(this).parents('tr.header_kandang').prev().find('td input.keterangan').val();
							 // //console.log('j'+keterangan)
							 blank = (keterangan) ? blank : blank + 1;
							 _oke.push({
							 'kode_farm' : $('#kode-farm').val(),
							 'no_order' : no_order,
							 'kode_barang' : kode_barang,
							 'no_reg' : no_reg,
							 'jenis_kelamin' : 'J',
							 'tgl_kebutuhan' : tgl_kebutuhan,
							 'jml_performance' : jml_performance,
							 'jml_pp' : jml_pp,
							 'keterangan' : keterangan,
							 });
							 }
							 })
							 $.each($(this).next().find('table.tbl-daftar-tgl-kebutuhan-betina tr.data-tgl-kebutuhan'), function() {
							 var tgl_kebutuhan = $(this).attr('data-tgl-kebutuhan');
							 var jml_performance = $(this).attr('data-jml-performance');
							 var tmp_jml_pp = $(this).attr('data-jml-pp');
							 var jml_pp = $(this).find('input.jumlah_pp').val();
							 if (parseInt(tmp_jml_pp) != parseInt(jml_pp)) {
							 var keterangan = $(this).parents('tr.header_kandang').prev().find('td input.keterangan').val();
							 // //console.log('b'+keterangan)
							 blank = (keterangan) ? blank : blank + 1;
							 _oke.push({
							 'kode_farm' : $('#kode-farm').val(),
							 'no_order' : no_order,
							 'kode_barang' : kode_barang,
							 'no_reg' : no_reg,
							 'jenis_kelamin' : 'B',
							 'tgl_kebutuhan' : tgl_kebutuhan,
							 'jml_performance' : jml_performance,
							 'jml_pp' : jml_pp,
							 'keterangan' : keterangan,
							 });
							 }
							 })
							 })
							 }

							 })
							 // //console.log(blank)
							 if (blank == 0) {
							 _params2.push({
							 'data_order_kandang_e' : _oke,
							 });
							 update_generate_permintaan(_params2, function(result) {
							 if (result == 1) {
							 $('#btn-release').removeAttr('disabled');
							 generate();
							 toastr.success('Update data LHK dan Pakan berhasil.', 'Berhasil');
							 } else if (result == 2) {
							 toastr.warning('Tidak ada data LHK dan Pakan yang diubah.', 'Peringatan');
							 } else {
							 toastr.error('Update data LHK dan Pakan gagal.', 'Peringatan');
							 }
							 })
							 } else {
							 toastr.error('Keterangan harus diisi.', 'Peringatan');
							 }
							 return true;
							 }
							 },
							 },
							 });*/
                        }
                    });
                }
            }
        }
    })
}

function simpan_generate_permintaan(data, callback) {
    if (data[0]['data_order_kandang'].length >= 1 && data[0]['data_order_kandang_d'].length >= 1 && data[0]['data_order_kandang_e'].length >= 1) {
        $.ajax({
            type : "POST",
            url : "generate_permintaan/main/simpan_generate_permintaan",
            data : {
                data : data
            },
            dataType : 'json',
            success : function(data) {
                callback(data);
            }
        });
    } else {
        toastr.error('Simpan data LHK dan Pakan gagal.', 'Peringatan');
    }
}

function update_generate_permintaan(data, callback) {
    // //console.log(data)
    if (data[0]['data_order_kandang_e'].length >= 1) {
        $.ajax({
            type : "POST",
            url : "generate_permintaan/main/update_generate_permintaan",
            data : {
                data : data
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

function release(kode_farm, no_order) {

    var _params = [];
    var data = [];
    $.each($('#tbl-daftar-barang').find('tr.header_barang tr.data-kandang'), function() {
        var no_reg = $(this).attr('data-no-reg');
        var sum_jumlah_pp = $(this).attr('data-sum-jumlah-pp');
        var sum_j_jumlah_pp = 0;
        var sum_b_jumlah_pp = 0;
        var kode_barang = $(this).parents('tr.header_barang').prev().attr('data-kode-barang');

        $.each($(this).next().find('table.tbl-daftar-tgl-kebutuhan-jantan tr.data-tgl-kebutuhan'), function() {
            var jml_pp = $(this).find('input.jumlah_pp').val();
            sum_j_jumlah_pp = sum_j_jumlah_pp + parseInt(jml_pp);
        })
        $.each($(this).next().find('table.tbl-daftar-tgl-kebutuhan-betina tr.data-tgl-kebutuhan'), function() {
            var jml_pp = $(this).find('input.jumlah_pp').val();
            sum_b_jumlah_pp = sum_b_jumlah_pp + parseInt(jml_pp);
        })
        if (sum_j_jumlah_pp > 0) {
            data.push({
                'kode_farm' : kode_farm,
                'kode_barang' : kode_barang,
                'no_order' : no_order,
                'no_reg' : no_reg,
                'sum_jumlah_pp' : sum_j_jumlah_pp,
                'jenis_kelamin' : 'J',
            });
        }
        if (sum_b_jumlah_pp > 0) {
            data.push({
                'kode_farm' : kode_farm,
                'kode_barang' : kode_barang,
                'no_order' : no_order,
                'no_reg' : no_reg,
                'sum_jumlah_pp' : sum_b_jumlah_pp,
                'jenis_kelamin' : 'B',
            });
        }
    })
    _params.push({
        data

    });

    // console.log(_params);
    if (_params.length >= 1) {
        // //console.log(_params);

        $.ajax({
            type : "POST",
            url : "generate_permintaan/main/release_generate_permintaan",
            data : {
                data : _params
            },
            dataType : 'json',
            success : function(data) {
                if (data == 1) {
                    /*
                     * $('#status').val('N'); $('#btn-baru').attr('disabled',
                     * true); $('#btn-release').attr('disabled', true);
                     * toastr.success('Release data LHK dan Pakan berhasil.',
                     * 'Berhasil');
                     */
                } else if (data == 2) {
                    toastr.error('Tidak ada kavling yang tersedia.', 'Peringatan');
                } else {
                    toastr.error('Release data LHK dan Pakan gagal.', 'Peringatan');
                }
            }
        });

    }
}

function format_datepicker(date) {
    var split = date.split(" ");
    return split[2] + '/' + $.datepicker.regional['id'].monthNamesShort.indexOf(split[1]) + '/' + split[0];
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

    if (kode_farm && tanggal_kebutuhan_awal && tanggal_kebutuhan_akhir && diff_tgl_keb_akhir <= 5 && diff_tgl_keb_awal >= 0) {

        ajax_generate(e, kode_farm, tanggal_kirim, tanggal_kebutuhan_awal, tanggal_kebutuhan_akhir);

    } else {
        toastr.error('Range tanggal tidak valid.', 'Peringatan');
    }

}

function clear_date() {
    $("#contain-daftar-barang").html('');
    $("#btn-tambah-barang").addClass('hide');
}

function ajax_generate(e, kode_farm, tanggal_kirim, tanggal_kebutuhan_awal, tanggal_kebutuhan_akhir) {
    $.ajax({
        type : "POST",
        url : "generate_permintaan/main/simpan_generate_permintaan",
        data : {
            kode_farm : kode_farm,
            tanggal_kirim : tanggal_kirim,
            tanggal_kebutuhan_awal : tanggal_kebutuhan_awal,
            tanggal_kebutuhan_akhir : tanggal_kebutuhan_akhir
        },
        dataType : 'json',
        success : function(data) {
            if (data.result == 1) {
                $(e).parents('th').prev().text(data.jml_pp);
                toastr.success('Generate permintaan berhasil', 'Berhasil');
            } else if (data.result == 2) {
                toastr.error('Tidak ada kavling yang tersedia.', 'Peringatan');
            } else if (data.result == 3) {
                toastr.error('Tanggal kebutuhan ' + tanggal_kebutuhan_awal + ' s/d ' + tanggal_kebutuhan_akhir + ' sudah dilakukan generate permintaan.', 'Peringatan');
            } else {
                toastr.error('Generate permintaan Gagal', 'Peringatan');
            }

            //$("#contain-daftar-barang").html(data);
            //simpan_baru(kode_farm, tanggal_kirim, tanggal_kebutuhan_awal, tanggal_kebutuhan_akhir);
            /*
             * $("#btn-tambah-barang").removeClass('hide'); var status =
             * $('#status').val(); if (status == 'D' || !status) {
             * $('#btn-baru').removeAttr('disabled'); }
             */
        }
    });
}

function detail_kandang(e) {
    var last_child = $(e).parents("tr").is(':last-child');
    var data_ke_next = $(e).parents("tr").next().attr('data-ke');
    // //console.log(data_ke_next);
    if (last_child || data_ke_next) {
        var data_ke = $(e).parents("tr").attr('data-ke');
        var append_text = "<tr>";
        append_text += "<td></td>";
        append_text += "<td colspan='5'>";

        append_text += "<table class='table table-bordered detail-kandang-table'>";
        append_text += "<thead>";
        append_text += "<tr>";
        append_text += "<th></th>";
        append_text += "<th>Kandang</th>";
        append_text += "<th>Populasi</th>";
        append_text += "<th>Tgl LHK</th>";
        append_text += "<th>Umur (minggu)</th>";
        append_text += "<th>Jumlah Kebutuhan (zak)</th>";
        append_text += "<th>Jumlah Stok (zak)</th>";
        append_text += "<th>Jumlah P (zak)</th>";
        append_text += "<th>Keterangan</th>";
        append_text += "</tr>";
        append_text += "</thead>";
        append_text += "<tbody>";
        append_text += "<tr>";
        append_text += "<td data-ke='1'><a onclick='detail_kebutuhan(this)'>Detail</a></td>";
        append_text += "<td>Kandang 01</td>";
        append_text += "<td>3856</td>";
        append_text += "<td>04 April 2015</td>";
        append_text += "<td>3+6</td>";
        append_text += "<td>8</td>";
        append_text += "<td>2</td>";
        append_text += "<td>6</td>";
        append_text += '<td><input type="text" class="form-control" name="keterangan" placeholder="Keterangan"></td>';
        append_text += "</tr>";
        append_text += "</tbody>";
        append_text += "</table>";

        append_text += "</td>";
        append_text += "</tr>";
        $(e).parents("tr").after(append_text);
    } else {
        $(e).parents("tr").next().remove();
    }
}

function detail_kebutuhan(e) {
    var last_child = $(e).parents("tr").is(':last-child');
    var data_ke_next = $(e).parents("tr").next().attr('data-ke');
    // console.log(data_ke_next);
    if (last_child || data_ke_next) {
        var data_ke = $(e).parents("tr").attr('data-ke');
        // console.log(data_ke);
        var append_text = "<tr>";
        append_text += "<td></td>";
        append_text += "<td colspan='8'>";

        append_text += "<div class='col-sm-7'>";
        append_text += "<table class='table table-bordered detail-kebutuhan-table'>";
        append_text += "<thead>";
        append_text += "<tr>";
        append_text += "<th>Tanggal</th>";
        append_text += "<th>Jumlah Keb. (zak)</th>";
        append_text += "<th>Jumlah PP (zak)</th>";
        append_text += "</tr>";
        append_text += "</thead>";
        append_text += "<tbody>";
        append_text += "<tr>";
        append_text += "<td>06 April 2015</td>";
        append_text += "<td>5</td>";
        append_text += '<td><input type="text" class="form-control" name="jumlah_pp" placeholder="Jumlah PP (zak)"></td>';
        append_text += "</tr>";
        append_text += "</tbody>";
        append_text += "</table>";
        append_text += "</div>";

        append_text += "</td>";
        append_text += "</tr>";
        $(e).parents("tr").after(append_text);
    } else {
        $(e).parents("tr").next().remove();
    }
}

function get_data_detail_pengambilan(e, _tab_active) {
    // var tanggal_kirim = $("#tanggal-kirim").val();
    var no_order = $(e).parents('tr').find('td:first').attr('data-no-order');
    var tanggal_kirim = $(e).parents('tr').find('td:first').text();
    if (!tanggal_kirim || !no_order) {
        tanggal_kirim = e[0]['tanggal_kirim'];
        no_order = e[0]['no_order'];
    }
    if (tanggal_kirim) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/view",
            data : {
                no_order : no_order,
                tanggal_kirim : tanggal_kirim,
                tab_active : _tab_active
            },
            success : function(data) {
                $("#main_content").html(data);
            }
        });
    }
}

function cetak_picking_list(e) {
    // var tanggal_kirim = $("#tanggal-kirim").val();
    var no_order = $(e).parents('tr').find('td:first').attr('data-no-order');
    var tanggal_kirim = $(e).parents('tr').find('td:first').text();
    if (tanggal_kirim) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cetak_picking_list",
            data : {
                no_order : no_order,
                tanggal_kirim : tanggal_kirim
            },
            success : function(data) {
                // $("#main_content").html(data);
                var _message = data;
                var box = bootbox.dialog({
                    message : _message,
                    title : "Print Preview",
                    buttons : {
                        danger : {
                            label : "Keluar",
                            className : "btn-danger",
                            callback : function() {
                                return true;
                            }
                        },
                        success : {
                            label : "Print",
                            className : "btn-success",
                            callback : function() {
                                $('.container').addClass('hidden-print');
                                $('.modal-body').addClass('visible-print-block');
                                window.print();
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

function get_data_pengambilan() {
    var tanggal_kirim = $("#tanggal-kirim").val();
    if (tanggal_kirim) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/main/get_data_pengambilan",
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
                        append_text += "<td data-no-order='" + value.no_order + "'><a href='#pengambilan_barang/transaksi' class='link' onclick='get_data_detail_pengambilan(this,1)'>" + convert_month(value.tgl_kirim) + "</a></td>";
                        append_text += "<td>" + convert_month(value.tgl_keb_awal) + " s/d " + convert_month(value.tgl_keb_akhir) + "</td>";
                        append_text += "<td>" + value.jumlah_kebutuhan + "</td>";
                        append_text += "<td>" + value.jumlah_belum_proses + "</td>";
                        append_text += "<td><a href='#pengambilan_barang/transaksi' class='link' onclick='cetak_picking_list(this)'>Cetak Picking List</a></td>";
                        append_text += "</tr>";
                    });
                    $("#picking-list-table table tbody").append(append_text);
                }
            }
        });
    }
}

function kontrol_option(e) {
    var data_ke = $(e).parents("tr").attr("data-ke");
    var disabled = $('tr[data-ke="' + data_ke + '"] .berat').attr('disabled');
    var berat = parseFloat($('tr[data-ke="' + data_ke + '"] .berat').val());
    // if(!berat || berat==0 || berat=='NaN'){
    if ( typeof disabled == 'undefined') {
        $("#btn-konfirmasi").attr('disabled', true);
        var checked = 0;
        $.each($('#tbl-tambah-barang table tbody').find('tr'), function() {
            var tmp_data_ke = $(this).attr("data-ke");
            // var tmp_berat = parseFloat($('tr[data-ke="'+tmp_data_ke+'"]
            // .berat').val());
            var tmp_disabled = $('tr[data-ke="' + tmp_data_ke + '"] .berat').attr('disabled');
            // //console.log(tmp_data_ke+" dan "+tmp_berat)
            // if(!tmp_berat || tmp_berat==0 || tmp_berat=='NaN'){
            if ( typeof tmp_disabled == 'undefined') {
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
    if ($('tr[data-ke="' + data_ke + '"] .radio').is(":checked") && berat && berat > 0) {
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
            url : "pengambilan_barang/transaksi/update_berat_pick_d",
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

function cek_order_kandang(data, callback) {
    if (data.length == 1) {
        $.ajax({
            type : "POST",
            url : "generate_permintaan/main/cek_order_kandang",
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

function cek_kode_verifikasi_kandang(data) {
    var data;
    if (data.length == 1) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cek_kode_verifikasi_kandang",
            data : {
                data : data
            },
            dataType : 'json',
            async : false,
            success : function(_data) {
                data = _data;
            }
        });
    }
    return data;
}

function konfirmasi() {
    var data_ke;
    var _params = [];
    $.each($('#tbl-tambah-barang tbody').find('input[type="radio"]'), function() {
        if ($(this).is(":checked")) {
            data_ke = $(this).parents("tr").attr("data-ke");
            _params.push({
                'tanggal_kirim' : $('tr[data-ke="' + data_ke + '"]').attr("data-tanggal-kirim"),
                'no_reg' : $('tr[data-ke="' + data_ke + '"]').attr("data-no-reg"),
                'no_order' : $('tr[data-ke="' + data_ke + '"]').attr("data-no-order"),
                'kode_farm' : $('tr[data-ke="' + data_ke + '"]').attr("data-kode-farm"),
                'kode_kandang' : $('tr[data-ke="' + data_ke + '"] .kode-kandang').text(),
                'kode_barang' : $('tr[data-ke="' + data_ke + '"] .kode-barang').text(),
                'id_kavling' : $('tr[data-ke="' + data_ke + '"] .id-kavling').text(),
                'berat' : $('tr[data-ke="' + data_ke + '"] .berat').val(),
                'kode_verifikasi' : ''
            });
        }
    })
    var toleransi = 50;
    var zak = Math.round(parseFloat($('tr[data-ke="' + data_ke + '"] .berat').val()) / toleransi);
    var jumlah = parseInt($('tr[data-ke="' + data_ke + '"] .jumlah').text());
    // //console.log(zak +" = "+ jumlah);
    if (zak == jumlah) {
        var _message = '<div class="form-group form-horizontal new-line">';
        _message += '<label class="col-sm-3 control-label" for="inputEmail3">RFID Card</label>';
        _message += '<div class="col-sm-8">';
        _message += '<input type="text" placeholder="RFID Card" id="rfid_card" class="form-control" autofocus>';
        _message += '</div>';
        _message += '</div>';
        var box = bootbox.dialog({
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
                        var kode_verifikasi = $("#rfid_card").val();
                        if (kode_verifikasi) {
                            _params[0]['kode_verifikasi'] = kode_verifikasi;
                            var cek = cek_kode_verifikasi_kandang(_params);
                            $.when(cek).done(function(result) {
                                if (result == 1) {
                                    simpan_konfirmasi(_params, function(result) {
                                        if (result == 1) {
                                            $("#btn-konfirmasi").attr('disabled', true);
                                            $('tr[data-ke="' + data_ke + '"] .radio').remove();
                                            $('tr[data-ke="' + data_ke + '"] .berat').attr('disabled', true);
                                            $('tr[data-ke="' + data_ke + '"] .berat').removeClass('berat');
                                            $('tr[data-ke="' + data_ke + '"] .berat').removeAttr('data-ke');
                                            $('tr[data-ke="' + data_ke + '"] .keterangan').text('Confirmed');
                                            $('#btn-tutup').removeAttr('disabled');
                                            $('#btn-tutup').attr('data-no-penerimaan', $('tr.mark_row').attr('data-no-penerimaan'));
                                            get_data_detail_pengambilan(_params, 1);
                                            toastr.success("Konfirmasi berhasil.", "Berhasil");
                                            box.modal('hide');
                                        } else {
                                            toastr.error("Konfirmasi gagal.", "Peringatan");
                                        }
                                    })
                                } else {
                                    toastr.error("Verifikasi RFID gagal.", "Peringatan");
                                }
                            })
                            return false;
                        } else {
                            toastr.error("RFID Card harus diisi.", "Peringatan");
                            return false;
                        }
                    }
                }
            }
        });

        box.bind('shown.bs.modal', function() {
            box.find("input#rfid_card").focus();
        });

        box.bind('hidden.bs.modal', function() {
            $('tr[data-ke="' + data_ke + '"] .berat').select();
        });
    } else {
        toastr.error("Konversi berat ke zak tidak sesuai.", "Peringatan");
        $('tr[data-ke="' + data_ke + '"] .berat').select();
    }
}