( function() {
    'use strict';

    $('div').on('click', 'a.btn', function(e) {
        // console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })
}())

function simpan() {
    //var no_ba = $('#no-berita-acara').val();
    var no_sj = $('#no-sj').val();
    var tipe_ba = $('#tipe-berita-acara').val();
    var keterangan1 = $('#keterangan').val();
    console.log(no_ba + '&&' + no_sj + '&&' + tipe_ba + '&&' + keterangan1)
    if (/*no_ba &&*/ no_sj && tipe_ba && keterangan1) {
        $.ajax({
            type : "POST",
            url : "berita_acara/main/simpan",
            data : {
                no_sj : no_sj,
                //no_ba : no_ba,
                tipe_ba : tipe_ba,
                keterangan1 : keterangan1
            },
            success : function(data) {
                (data == 1) ? toastr.success('Simpan berita acara berhasil.', 'Berhasil') : toastr.error('Simpan berita acara gagal.', 'Peringatan');
            }
        });
    } else {
        toastr.error('Semua data harus diisi.', 'Peringatan');
    }
}

function get_data(e) {
    var no_sj = $(e).val();
    if (no_sj) {
        $.ajax({
            type : "POST",
            url : "berita_acara/main/get_data",
            data : {
                no_sj : no_sj
            },
            dataType : 'json',
            success : function(data) {
                if (data.length > 0) {
                    $(".daftar-barang-table table tbody").html('');
                    var append_text = "";
                    $.each(data, function(key, value) {
                        $('#ptanggal-berita-acara').text(convert_month(value.tgl_buat));
                        $('#no-berita-acara').val(value.no_ba);
                        $('#pno-berita-acara').text(value.no_ba);
                        $('#pno-sj').text(value.no_sj);
                        $('#no-penerimaan').val(value.no_penerimaan);
                        $('#pno-penerimaan').text(value.no_penerimaan);
                        $('#tipe-berita-acara').val(value.tipe_ba);
                        $('#no-op').val(value.no_op);
                        $('#pno-op').text(value.no_op);
                        $('#kode-farm').val(value.kode_farm);
                        $('#pkode-farm').text(value.kode_farm);
                        $('#nama-farm').val(value.nama_farm);
                        $('#pnama-farm').text(value.nama_farm);
                        $('#nama-sopir').val(value.nama_sopir);
                        $('#pnama-sopir').text(value.nama_sopir);
                        $('#no-kendaraan').val(value.no_kendaraan_terima);
                        $('#pno-kendaraan').text(value.no_kendaraan_terima);
                        $('#no-spm').val(value.no_spm);
                        $('#pno-spm').text(value.no_spm);
                        $('#keterangan').val(value.keterangan);
                        $('#pketerangan').text(value.keterangan);

                        append_text += "<tr>";
                        append_text += "<td>" + value.kode_barang + "</td>";
                        append_text += "<td>" + value.nama_barang + "</td>";
                        append_text += "<td>" + value.bentuk_barang + "</td>";
                        append_text += "<td>" + value.jml_rusak + "</td>";
                        append_text += "</tr>";
                    })
                    $(".daftar-barang-table table tbody").append(append_text);
                }
            }
        });
    }
}

function print_preview() {
    var no_sj = $('#no-sj').val();
    if (no_sj) {
        $.ajax({
            type : "POST",
            url : "berita_acara/main/print_preview",
            data : {
                no_sj : no_sj
            },
            success : function(data) {
                $('#print-preview').html(data);
            }
        });
    }
}