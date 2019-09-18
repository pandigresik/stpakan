( function() {
    'use strict';

    $('div').on('click', 'a.btn', function(e) {
        // console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })
}())

function simpan() {
    var no_ba = $('#no-berita-acara').val();
    var no_sj = $('#no-sj').val();
    var tipe_ba = $('#tipe-berita-acara').val();
    var keterangan1 = $('#keterangan').val();
    var no_penerimaan = $('#no-penerimaan').val();
    if(no_ba){
        toastr.error('Data sudah tersimpan.', 'Peringatan');
    }
    else{
        //console.log(no_sj + '&&' + tipe_ba + '&&' + keterangan1 + '&&' + no_penerimaan)
        if (/* no_ba && */no_sj && tipe_ba && keterangan1 && no_penerimaan) {
            $.ajax({
                type : "POST",
                url : "berita_acara/main/simpan",
                data : {
                    no_sj : no_sj,
                    // no_ba : no_ba,
                    tipe_ba : tipe_ba,
                    keterangan1 : keterangan1
                },
                dataType : 'json',
                success : function(data) {
                    if(data.result == 1){
                        $('#no-berita-acara').val(data.no_ba);
                        toastr.success('Simpan berita acara berhasil.', 'Berhasil');
                        get_data();
                    }
                    else{
                        toastr.error('Simpan berita acara gagal.', 'Peringatan');
                    }
                }
            });
        } else {
            toastr.error('Semua data harus diisi.', 'Peringatan');
        }
    }
}

function list_surat_jalan(e) {
    $.ajax({
        type : "POST",
        url : "berita_acara/main/list_surat_jalan",
        data : {
        },
        success : function(html) {
            bootbox.dialog({
                message : html,
                title : "Surat Jalan",
                className : "very-large",
                buttons : {
                    success : {
                        label : "OK",
                        className : "btn-success",
                        callback : function() {
                            return true;
                        }
                    },
                },
            });
        }
    });
}

function list_berita_acara(e) {
    $.ajax({
        type : "POST",
        url : "berita_acara/main/list_berita_acara",
        data : {
        },
        success : function(html) {
            bootbox.dialog({
                message : html,
                title : "Berita Acara",
                className : "very-large",
                buttons : {
                    success : {
                        label : "OK",
                        className : "btn-success",
                        callback : function() {
                            return true;
                        }
                    },
                },
            });
        }
    });
}

function pilih_surat_jalan(e){
    $('#no-sj').val($(e).find('td.no-sj').text());
    $(e).parents('div.modal').modal('hide');
    get_data();
}

function pilih_berita_acara(e){
    $('#no-sj').val($(e).find('td.no-sj').text());
    $('#tipe-berita-acara').val($(e).find('td.tipe-ba').attr('data-tipe-ba'));
    $('#no-berita-acara').val($(e).find('td.no-ba').text());
    $(e).parents('div.modal').modal('hide');
    get_data();
}

function get_data() {
    var no_sj = $('#no-sj').val();
    var tipe_ba = $('#tipe-berita-acara').val();
    $('#no-berita-acara').val('');
    $('.form-clear').val('');
    $('.form-clear').text('');
    $(".daftar-barang-table table tbody").html('');
    $('#btn-print').attr('href','berita_acara/main/cetak_daftar_penerimaan?no_sj=&tipe_ba=');
    var append_text = "";
    if (no_sj && tipe_ba) {
        $.ajax({
            type : "POST",
            url : "berita_acara/main/get_data",
            data : {
                no_sj : no_sj,
                tipe_ba : tipe_ba
            },
            dataType : 'json',
            success : function(data) {
                if (data.length > 0) {
                    $.each(data, function(key, value) {
                        if(value.tgl_buat){
                            $('#ptanggal-berita-acara').text(convert_month(value.tgl_buat));
                        }
                        $('#no-berita-acara').val(value.no_ba);
                        $('#pno-berita-acara').text(value.no_ba);
                        $('#pno-sj').text(value.no_sj);
                        $('#no-penerimaan').val(value.no_penerimaan);
                        $('#pno-penerimaan').text(value.no_penerimaan);
                        //$('#tipe-berita-acara').val(value.tipe_ba);
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

                        $('#btn-print').attr('href','berita_acara/main/cetak_daftar_penerimaan?no_sj='+value.no_sj+'&tipe_ba='+tipe_ba);

                        append_text += "<tr>";
                        append_text += "<td>" + value.kode_barang + "</td>";
                        append_text += "<td>" + value.nama_barang + "</td>";
                        append_text += "<td>" + value.bentuk_barang + "</td>";
                        if(tipe_ba == 'R'){
                            append_text += "<td>" + value.jml_rusak + "</td>";
                        }
                        else{
                            append_text += "<td>" + value.jml_kurang + "</td>";
                        }
                        append_text += "</tr>";
                    })
                    $(".daftar-barang-table table tbody").append(append_text);
                }
            }
        });
    }
}

function ubah_judul_tipe_ba(e){
    if($(e).val()){
        var tipe_ba = ($(e).val() == 'K') ? 'Kurang' : 'Rusak';
        $('.daftar-barang-table .tipe-ba').text(tipe_ba);
        get_data();
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