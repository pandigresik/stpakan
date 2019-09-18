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

    $('input.berat-timbang').numeric({
        allowPlus : false, // Allow the + sign
        allowMinus : false, // Allow the - sign
        allowThouSep : false, // Allow the thousands separator, default is the
        // comma eg 12,000
        allowDecSep : true
    // Allow the decimal separator, default is the fullstop eg 3.141
    });

}())

get_data_pengambilan();

get_data_riwayat_pengambilan();

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

function show_detail_kandang(elm) {
    $(elm).attr('disabled', true);
    $(elm).parents('tr.tr-sub-detail').next().removeClass('hide');
    $(elm).parents('tr.tr-sub-detail').find('td.arrow span').css('transform','rotate(180deg)');
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
    $.each($(elm).next().find('table.tbl-detail-pakan tbody tr.tr-sub-detail'), function() {
        var berat = $(this).find('td.berat-timbang input.berat-timbang').val();
        if (!berat && i == 0) {
            //$(this).find('td input.berat-timbang').removeAttr('readonly');
            i++;
        }
    });

    //$(elm).next().find('table tbody tr td input.berat-timbang:not([readonly]):first').focus().select();
    $(elm).next().find(
        'table tbody tr td input.berat-timbang:first')
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

function selesai(elm) {
    var detail_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var detail_ke_sub_detail = $(elm).parents('tr.tr-sub-detail').attr('data-ke');
    var kode_flok = $('tr.tr-header[data-ke="'+detail_ke_detail+'"]').attr('data-kode-flok');
    var no_order = $('tr.tr-header[data-ke="'+detail_ke_detail+'"]').attr('data-no-order');
    var no_kavling = $('tr.tr-header[data-ke="'+detail_ke_detail+'"]').find('td.no-kavling').attr('data-no-kavling');
    var kode_barang = $('tr.tr-header[data-ke="'+detail_ke_detail+'"]').find('td.kode-barang').text();
    var diserahkan_oleh = $('tr.tr-header[data-ke="'+detail_ke_detail+'"]').find('td.diserahkan-oleh').attr('data-diserahkan-oleh');
    var no_pallet = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail+'"]').attr('data-no-pallet');
    var berat_pallet = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail+'"] td.berat-pallet').text();
    var berat_bersih = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail+'"] td.berat-bersih').text();
    var jumlah_aktual = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail+'"] td.jumlah-sak').attr('data-jumlah-aktual-sak');
    var status_jumlah_aktual = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail+'"] td.jumlah-sak').attr('data-status-jumlah-aktual-sak');
    var jumlah_konversi_timbang = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail+'"] td.jumlah-sak').text();
    var data= [];
    /*
    var data_header = {
        'data_detail' : {},
        'no_kavling' : no_kavling,
        'kode_barang' : kode_barang,
        'diserahkan_oleh' : diserahkan_oleh,
        'no_pallet' : no_pallet,
        'berat_pallet' : berat_pallet,
        'berat_bersih' : berat_bersih,
        'jumlah_aktual' : jumlah_aktual
    }
    var data_detail = [];
    */

    jumlah_konversi_timbang = (status_jumlah_aktual == 1) ? jumlah_konversi_timbang : 0 ;
    $.each($('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+detail_ke_sub_detail+'"] table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
        var checked = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
        if(checked){
            var jumlah_pallet = $(this).attr('data-jml-pallet');
            jumlah_pallet = parseInt(jumlah_pallet);
            var no_reg = $(this).find('td.nama-kandang').attr('data-no-reg');
            no_pallet = $(this).find('td.nama-kandang').attr('data-no-pallet');
            var jumlah_kebutuhan = $(this).find('td.jml-kebutuhan').text();
            var jumlah = $(this).find('td.jml-aktual').text();
            var berat = $(this).find('td.berat').text();
            var diterima_oleh = $(this).find('td.konfirmasi').attr('data-user-gudang');
            if(jumlah_pallet>1){
                var tmp_hutang_sak_kosong= $(this).find('td.hutang-sak-kosong').text();
                tmp_hutang_sak_kosong = parseInt(tmp_hutang_sak_kosong);
                var tmp_no_pallet= $(this).find('td.nama-kandang').attr('data-no-pallet');
                tmp_no_pallet = tmp_no_pallet.split(',');
                var tmp_jml_kebutuhan = $(this).find('td.jml-kebutuhan').attr('data-jml-kebutuhan');
                tmp_jml_kebutuhan = tmp_jml_kebutuhan.split(',');
                berat = parseFloat(berat);
                jumlah = parseInt(jumlah);
                var berat_per_sak = berat/jumlah;
                berat_per_sak = berat_per_sak.toFixed(3);

                for(var i=0;i<tmp_no_pallet.length;i++){
                    no_pallet = tmp_no_pallet[i];
                    jumlah_kebutuhan = tmp_jml_kebutuhan[i];
                    jumlah_kebutuhan = parseInt(jumlah_kebutuhan);

                    //if(tmp_hutang_sak_kosong==0){
                        jumlah = jumlah_kebutuhan;
                    //}
                    //else{
                    //    if(jumlah_kebutuhan<=tmp_hutang_sak_kosong){
                    //        jumlah = tmp_hutang_sak_kosong - jumlah_kebutuhan;
                    //        tmp_hutang_sak_kosong = tmp_hutang_sak_kosong - jumlah_kebutuhan;
                    //    }
                    //    else{
                    //        jumlah = jumlah_kebutuhan - tmp_hutang_sak_kosong;
                    //        tmp_hutang_sak_kosong = 0;
                    //    }

                    //}
                    jumlah = parseInt(jumlah);
                    berat = jumlah * berat_per_sak;

                    data.push({

                        'no_order' : no_order,
                        'id_kavling' : no_kavling,
                        'kode_barang' : kode_barang,
                        'diserahkan_oleh' : diserahkan_oleh,
                        'no_pallet' : no_pallet,
                        'berat_pallet' : berat_pallet,
                        'berat_bersih' : berat_bersih,
                        'berat_timbang' : (parseFloat(berat_bersih)+parseFloat(berat_pallet)),
                        'jumlah_aktual' : jumlah_aktual,
                        'jumlah_konversi_timbang' : jumlah_konversi_timbang,
                        'jenis_kelamin' : 'C',
                        'kode_flok' : kode_flok,

                        'no_reg' : no_reg,
                        'jumlah' : jumlah_kebutuhan,
                        'jumlah_aktual_zak' : jumlah,
                        'berat' : berat,
                        'user_gudang' : diterima_oleh
                    });
                }

            }
            else{
                data.push({

                    'no_order' : no_order,
                    'id_kavling' : no_kavling,
                    'kode_barang' : kode_barang,
                    'diserahkan_oleh' : diserahkan_oleh,
                    'no_pallet' : no_pallet,
                    'berat_pallet' : berat_pallet,
                    'berat_bersih' : berat_bersih,
                    'berat_timbang' : (parseFloat(berat_bersih)+parseFloat(berat_pallet)),
                    'jumlah_aktual' : jumlah_aktual,
                    'jumlah_konversi_timbang' : jumlah_konversi_timbang,
                    'jenis_kelamin' : 'C',
                    'kode_flok' : kode_flok,

                    'no_reg' : no_reg,
                    'jumlah' : jumlah_kebutuhan,
                    'jumlah_aktual_zak' : jumlah,
                    'berat' : berat,
                    'user_gudang' : diterima_oleh
                });
            }

        }
    });
    /*
    data_header['data_detail'] = data_detail;
    var data = data_header['data_detail'];
    */

    console.log(data)
    
    
    if(data.length > 0){
        simpan_data(data, function(result){
            if(result.result == 1){
                toastr.success('Simpan berhasil.','Informasi');
                get_data_detail_pengambilan(data, 1, detail_ke_detail);
            }
            else{
                toastr.error('Simpan gagal.','Informasi');

            }
        });
    }
    
    
}

function simpan_data(data, callback){
    $.ajax({
        type : "POST",
        url : "pengambilan_barang/transaksi/simpan_data",
        data : {
            data : data
        },
        dataType : 'json',
        success : function(data) {
            callback(data);
        }
    });
}

function cek_pallet(no_pallet, zak, callback){
    $.ajax({
        type : "POST",
        url : "pengambilan_barang/transaksi/cek_pallet",
        data : {
            no_pallet : no_pallet,
            zak : zak
        },
        dataType : 'json',
        success : function(data) {
            callback(data);
        }
    });
}

function kontrol_timbangan(elm) {
    $(elm).parents('tr.tr-sub-detail').next().addClass('hide');
    $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
        'disabled', true);
    $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
        'data-result-timbang', '');
    var no_pallet = $(elm).parents('tr.tr-sub-detail').attr('data-no-pallet');
    var berat = $(elm).val();
    var berat_bersih = '';
    var jumlah = '';
    var keterangan = '';
    if(berat){
        var berat_pallet = $(elm).parents('tr.tr-sub-detail').find('td.berat-pallet').text();
        berat_bersih = parseFloat(berat) - parseFloat(berat_pallet);
        if(berat_bersih < 0){
            toastr.warning('Berat timbang harus lebih besar dari berat pallet.','Informasi');
            $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text('');
            $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
            $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
            $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
        }
        else{
            berat_bersih = berat_bersih.toFixed(3);
            cek_konversi(berat_bersih, function(data) {
                var result = 0;
                //if(data && (parseFloat(berat_pallet)<parseFloat(berat))){
                if(data){
                    jumlah = data.JML_SAK;
                    if(data.result == 1){
                        keterangan = 'Selesai';
                        result = 1;
                    }
                }
                else{
                    jumlah = 0;
                }

                $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text(berat_bersih);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
    	        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
    	
                
                if(result == 0){
                    berat_diluar_toleransi(elm,jumlah);
                }
                else{

                    var jumlah_stok_kavling = $('tr.mark_row').attr('data-stok-kavling');
                    if(parseInt(jumlah) != parseInt(jumlah_stok_kavling)){
                        berat_diluar_toleransi(elm,jumlah);
                        /*
                        toastr.warning('Jumlah (Sak) tidak sama dengan sisa Aktual Kavling.');
                        $(elm).val('');
                        $(elm).focus().select();
                        $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text('');
                        $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text('');
                        $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak','');
                        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html('');
                        */
                    }
                    else{
                        show_detail_kandang(elm);

                    }
                }
            });
		}
    }
    else{
        $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text(berat_bersih);
        $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
        $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
    }
}

function kontrol_timbangan_old(elm) {
    $(elm).parents('tr.tr-sub-detail').next().addClass('hide');
    $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
        'disabled', true);
    $(elm).parents('tr.tr-sub-detail').find('button.btn-selesai').attr(
        'data-result-timbang', '');
    var no_pallet = $(elm).parents('tr.tr-sub-detail').attr('data-no-pallet');
    var berat = $(elm).val();
    var berat_bersih = '';
    var jumlah = '';
    var keterangan = '';
    if(berat){
        var berat_pallet = $(elm).parents('tr.tr-sub-detail').find('td.berat-pallet').text();
        berat_bersih = parseFloat(berat) - parseFloat(berat_pallet);
        if(berat_bersih < 0){
            toastr.warning('Berat timbang harus lebih besar dari berat pallet.','Informasi');
            $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text('');
            $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
            $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
            $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
        }
        else{
            berat_bersih = berat_bersih.toFixed(3);
            cek_konversi(berat_bersih, function(data) {
                var result = 0;
                //if(data && (parseFloat(berat_pallet)<parseFloat(berat))){
                if(data){
                    jumlah = data.JML_SAK;
                    if(data.result == 1){
                        keterangan = 'Selesai';
                        result = 1;
                    }
                }
                else{
                    jumlah = 0;
                }

                $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text(berat_bersih);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
    	        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
    	
                
                if(result == 0){
                    berat_diluar_toleransi(elm,jumlah);
                }
                else{
                    show_detail_kandang(elm);
                }
            });
		}
    }
    else{
        $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text(berat_bersih);
        $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
        $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
    }
}

function kontrol_sak_aktual() {
    var jumlah_aktual = $('#jumlah_aktual').val();
    var jumlah_stok_kavling = $('tr.mark_row').attr('data-stok-kavling');
    if(parseInt(jumlah_aktual) != parseInt(jumlah_stok_kavling)){
        toastr.warning('Jumlah Aktual tidak sama dengan sisa Aktual Kavling.');
        $('#jumlah_aktual').val('');
        $('#jumlah_aktual').focus().select();
    }
}

function berat_diluar_toleransi(elm,jumlah) {
    var konfirmasi = 0;
    var keterangan = '';
    var jumlah_aktual = '';
    var _message = '<div class="form-group form-horizontal new-line">';
                        _message += '<div class="form-group">';
                        _message += '<label class="col-sm-5 control-label">Konversi Timbangan (Sak)</label>';
                        _message += '<div class="col-sm-5">';
                        _message += '<label class="control-label">'
                        + jumlah + '</label>';
                        _message += '</div></div>';
                        _message += '<div class="form-group">';
                        _message += '<label class="col-sm-5 control-label">Jumlah Sak Aktual</label>';
                        _message += '<div class="col-sm-5">';
                        _message += '<input type="text" placeholder="Jumlah Sak Aktual" id="jumlah_aktual" class="form-control" onchange="kontrol_sak_aktual()">';
                        _message += '</div></div>';
                        // _message += '<div class="form-group"><div
                        // class="col-sm-12 text-center"><button class="btn
                        // btn-default">Simpan</button></div></div>';
                        _message += '</div>';
                        var box_status = 0;
                        var box = bootbox.dialog({
                            message : _message,
                            title : "Konfirmasi Sak",
                            buttons : {
                                success : {
                                    label : "Simpan",
                                    className : "btn-success",
                                    callback : function() {
                                        jumlah_aktual = $('#jumlah_aktual').val();
                                        if (!jumlah_aktual || jumlah_aktual <= 0) {
                                            $('#jumlah_aktual').focus().select();
                                            toastr.error('Jumlah Aktual Sak harus diisi.','Peringatan');
                                            return false;
                                        } else {
                                            konfirmasi = 1;
                                            return true;

                                        }
                                    }
                                }
                            }
                        });
                        box.bind('shown.bs.modal', function() {
                            $('#jumlah_aktual').focus().select();
                            $('#jumlah_aktual').numeric({
                                allowPlus : false, // Allow the + sign
                                allowMinus : false, // Allow the - sign
                                allowThouSep : false, // Allow the
                                allowDecSep : false
                            });
                        });
                        box.bind('hidden.bs.modal', function() {
                            if(konfirmasi == 1){
                                keterangan = '<p>Jumlah konversi timbangan = '+jumlah+' sak</p><p>Jumlah aktual = '+jumlah_aktual+' sak</p>';
                                $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
                                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah_aktual);
                                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-status-jumlah-aktual-sak','1');
                                show_detail_kandang(elm);
                            }
                        });
}

function checkbox_kandang(elm){
    var detail_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var detail_ke_kandang = $(elm).parents('tr.tr-detail-kandang').attr('data-ke');
    var detail_ke_sub_detail_pakan = $(elm).parents('tr.tr-sub-detail-pakan').attr('data-ke');
    var jumlah_sak = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail_pakan+'"] td.jumlah-sak').attr('data-jumlah-aktual-sak');
    var jumlah_sak = parseInt(jumlah_sak);
    var berat_bersih = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail[data-ke="'+detail_ke_sub_detail_pakan+'"] td.berat-bersih').text();
    var berat_bersih = parseFloat(berat_bersih);
    var berat_per_sak = berat_bersih / jumlah_sak;
    var curr_jumlah_kebutuhan = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+detail_ke_sub_detail_pakan+'"] tr.tr-detail-kandang[data-ke="'+detail_ke_kandang+'"] td.jml-kebutuhan').text();
    curr_jumlah_kebutuhan = parseInt(curr_jumlah_kebutuhan);
    var hutang_sak_kosong = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+detail_ke_sub_detail_pakan+'"] tr.tr-detail-kandang[data-ke="'+detail_ke_kandang+'"] td.hutang-sak-kosong').text();
    hutang_sak_kosong = parseInt(hutang_sak_kosong);

    var no_reg = $('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+detail_ke_sub_detail_pakan+'"] tr.tr-detail-kandang[data-ke="'+detail_ke_kandang+'"] td.nama-kandang').attr('data-no-reg');

    $('#p_kandang').val(no_reg);
    get_data_riwayat_pengambilan();

    //console.log(jumlah_sak)
    //console.log(jumlah_kebutuhan)
    var jumlah_aktual = '';
    var sisa = '';
    var berat = '';
    $.each($('tr.tr-detail[data-ke="'+detail_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+detail_ke_sub_detail_pakan+'"] table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
        
        var tmp_detail_ke_kandang = $(this).attr('data-ke');
        var checked = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');      
        if(checked && (tmp_detail_ke_kandang != detail_ke_kandang)){
            var jumlah_kebutuhan = $(this).find('td.jml-kebutuhan').text();
            jumlah_kebutuhan = parseInt(jumlah_kebutuhan);
            var tmp_jumlah_aktual = $(this).find('td.jml-aktual').text();
            var tmp_pengurang = tmp_jumlah_aktual; //(tmp_jumlah_aktual) ? parseInt(tmp_jumlah_aktual) : jumlah_kebutuhan;
            jumlah_sak = jumlah_sak - tmp_pengurang;
            //console.log(jumlah_sak)
            //console.log(curr_jumlah_kebutuhan)
            //console.log(tmp_pengurang)
        }
    });

    if(jumlah_sak > 0){
        jumlah_aktual = curr_jumlah_kebutuhan;
        jumlah_aktual = parseInt(jumlah_aktual) - hutang_sak_kosong;
        sisa = curr_jumlah_kebutuhan - (jumlah_aktual + hutang_sak_kosong);
        berat = jumlah_aktual * berat_per_sak;
        berat = berat.toFixed(3);

        fingerprint(elm, jumlah_sak);
    }
    /*
    if(jumlah_sak >= curr_jumlah_kebutuhan && jumlah_sak > 0){
        jumlah_aktual = curr_jumlah_kebutuhan;
        jumlah_aktual = parseInt(jumlah_aktual) - hutang_sak_kosong;
        sisa = curr_jumlah_kebutuhan - (jumlah_aktual + hutang_sak_kosong);
        berat = jumlah_aktual * berat_per_sak;
        berat = berat.toFixed(3);

        fingerprint(elm, jumlah_sak);
    }
    else if(jumlah_sak < curr_jumlah_kebutuhan && jumlah_sak > 0){
        jumlah_aktual = jumlah_sak;
        jumlah_aktual = parseInt(jumlah_aktual) - hutang_sak_kosong;
        sisa = curr_jumlah_kebutuhan - (jumlah_aktual + hutang_sak_kosong);
        berat = jumlah_aktual * berat_per_sak;
        berat = berat.toFixed(3);

        fingerprint(elm, jumlah_sak);
    }
    */
    else{
        $(elm).attr('checked', false);
        toastr.warning('Total jumlah aktual kandang melebihi jumlah aktual timbang.', 'Informasi');

    }

    $(elm).parents('tr.tr-detail-kandang').find('td.jml-aktual').text(jumlah_aktual);
    $(elm).parents('tr.tr-detail-kandang').find('td.sisa').text(sisa);
    $(elm).parents('tr.tr-detail-kandang').find('td.berat').text(berat);
    $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text('');
}

function not_actived(elm){
    elm.preventDefault();
}

function selesai_old(elm) {
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

var timer = true;
var tkode_pegawai = '';
var tnama_pegawai = '';
                

function fingerprint(elm) {
    if($(elm).is(':checked')){
        simpan_transaksi_verifikasi(function(result){
            if(result.date_transaction){
                var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
                var box = bootbox.dialog({
                    message : _message,
                    closeButton: false,
                    title : "Fingerprint",
                    buttons : {
                        success : {
                            label : "Batal",
                            className : "btn-danger",
                            callback : function() {
                                timer = false;
                                tkode_pegawai = '';
                                tnama_pegawai = '';
                                return true;
                            }
                        }
                    }
                });

                box.bind('shown.bs.modal', function() {
                    timer = true;
                    tkode_pegawai = '';
                    tnama_pegawai = '';
                    cek_verifikasi(result.date_transaction);
                });

                box.bind('hidden.bs.modal', function() {
                    if(tkode_pegawai && tnama_pegawai){
                        $(elm).attr('disabled', true);
                        $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text(tnama_pegawai);
                        $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').attr('data-user-gudang',tkode_pegawai);
                        var done = cek_selesai(elm);
                        if(done == 0){
                            $(elm).parents('tr.tr-sub-detail-pakan').prev().find('td.sisa-pallet button').removeAttr('disabled');
                        }
                        toastr.success('Verifikasi fingerprint berhasil.','Berhasil');
                    }
                    else{
                        $(elm).attr('checked',false);
                        $(elm).parents('tr.tr-detail-kandang').find('td.jml-aktual').text('');
                        $(elm).parents('tr.tr-detail-kandang').find('td.berat').text('');
                        $(elm).parents('tr.tr-detail-kandang').find('td.sisa').text('');
                        $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text('');
                        $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').attr('data-user-gudang','');
                    }
                });
            }
        });
    }
}

function simpan_transaksi_verifikasi(callback){
    $.ajax({
        type : "POST",
        url : "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
        data : {
             transaction : 'pengambilan_barang'
        },
        dataType : 'json',
        success : function(data) {
            callback(data);
        }
    });
}

function cek_verifikasi(date_transaction){
    if (timer == true) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cek_verifikasi",
            data : {
                date_transaction : date_transaction
            },
            dataType : 'json',
            success : function(data) {
                if(data.verificator){
                    timer = false;
                    tkode_pegawai = data.kode_pegawai;
                    tnama_pegawai = data.nama_pegawai;
                    $('.bootbox').modal('hide');
                }
                else{
                    timer = true;
                    tkode_pegawai = '';
                    tnama_pegawai = '';
                    setTimeout("cek_verifikasi('"+date_transaction+"')", 1000);
                }
            }
        });
    }
}

function fingerprint_backup(elm) {
    var konfirmasi = 0;
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
    var id_user_gudang = '';
    var user_gudang = '';
    var box = bootbox.dialog({
        message : _message,
        title : "Fingerprint",
        buttons : {
            success : {
                label : "Simpan",
                className : "btn-success",
                callback : function() {
                    id_user_gudang = $('#user_gudang').val();
                    user_gudang = $("#user_gudang option:selected").text();
                    if (!user_gudang) {
                        $('#user_gudang').focus().select();
                        messageBox('', '', 'Verifikasi Gagal.');
                        return false;
                    } else {
                        konfirmasi = 1;
                        return true;
                    }
                }
            }
        }
    });

    box.bind('hidden.bs.modal', function() {
        if (konfirmasi == 1) {
            $(elm).attr('disabled', true);
            $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text(user_gudang);
            $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').attr('data-user-gudang',id_user_gudang);
            var done = cek_selesai(elm);
            if(done == 0){
                $(elm).parents('tr.tr-sub-detail-pakan').prev().find('td.sisa-pallet button').removeAttr('disabled');
            }
        }
        else{
            $(elm).attr('checked',false);
            $(elm).parents('tr.tr-detail-kandang').find('td.jml-aktual').text('');
            $(elm).parents('tr.tr-detail-kandang').find('td.berat').text('');
            $(elm).parents('tr.tr-detail-kandang').find('td.sisa').text('');
            $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text('');
            $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').attr('data-user-gudang','');
        }
    })
}

function cek_selesai_old(elm){
    var jumlah_sak_timbang = $(elm).parents('tr.tr-sub-detail-pakan').prev().find('td.jumlah-sak').attr('data-jumlah-aktual-sak');
    jumlah_sak_timbang = parseInt(jumlah_sak_timbang);
    $.each($(elm).parents('tr.tr-sub-detail-pakan').find('table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
        var jumlah = $(this).find('td.jml-aktual').text();
        jumlah = parseInt(jumlah);
        var checked = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');      
        if(checked){
            jumlah_sak_timbang = jumlah_sak_timbang - jumlah;
        }
    });
    return jumlah_sak_timbang;
}

function cek_selesai(elm){
    var result = 1;
    var sum_jumlah_kebutuhan = 0;
    var sum_jumlah_aktual = 0;
    var sum_sisa = 0;
    var jumlah_sak_timbang = $(elm).parents('tr.tr-sub-detail-pakan').prev().find('td.jumlah-sak').attr('data-jumlah-aktual-sak');
    jumlah_sak_timbang = parseInt(jumlah_sak_timbang);
    $.each($(elm).parents('tr.tr-sub-detail-pakan').find('table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
        var jumlah_kebutuhan =$(this).find('td.jml-kebutuhan').text();
        jumlah_kebutuhan = parseInt(jumlah_kebutuhan);
        sum_jumlah_kebutuhan = sum_jumlah_kebutuhan+ jumlah_kebutuhan;
        
        var sisa =$(this).find('td.sisa').text();
        sisa = parseInt(sisa);
        sum_sisa = sum_sisa+ sisa;


        var jumlah = $(this).find('td.jml-aktual').text();
        jumlah = parseInt(jumlah);
        var checked = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');      
        if(checked){
            jumlah_sak_timbang = jumlah_sak_timbang - jumlah;
            sum_jumlah_aktual = sum_jumlah_aktual + jumlah;
        }
    });
    /*
    if(sum_jumlah_kebutuhan == sum_jumlah_aktual){
        result = 0;
    }
    */
    //console.log(jumlah_sak_timbang+' atau '+sum_sisa);

    if(jumlah_sak_timbang == 0 || sum_sisa == 0){
        result = 0;
    }

    return result;
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
    var tanggal_kebutuhan = $(e).attr("data-tanggal-kebutuhan");
    var no_penerimaan = $(e).attr("data-no-penerimaan");
    var no_referensi = $(e).attr("data-no-referensi");
    var kode_flok = $(e).attr("data-kode-flok");


    ajax_generate(e, tanggal_kebutuhan, no_penerimaan,no_referensi,kode_flok);

}

function ajax_generate(e, tanggal_kebutuhan,no_penerimaan,no_referensi,kode_flok) {
    $.ajax({
        type : "POST",
        url : "pengambilan_barang/main/simpan_generate_permintaan",
        data : {
            tanggal_kebutuhan : tanggal_kebutuhan,
            no_penerimaan : no_penerimaan,
            no_referensi : no_referensi,
            kode_flok : kode_flok
        },
        dataType : 'json',
        success : function(data) {
            if (data.result == 1) {
                toastr.success('Generate permintaan Berhasil.', 'Informasi');
                get_data_pengambilan();
            } else if (data.result == 2) {
                toastr.error('Generate permintaan harus urut.', 'Peringatan');
            } else if (data.result == 3) {
                toastr.error('Penerimaan untuk Tanggal Kebutuhan '+Config._tanggalLocal(tanggal_kebutuhan,'-',' ')+' belum lengkap.', 'Peringatan');
            } else if (data.result == 4) {
                toastr.error('Terdapat pengambilan dari Mutasi Pakan yang belum dilakukan. Untuk Tanggal Kebutuhan '+Config._tanggalLocal(tanggal_kebutuhan,'-',' ')+'.', 'Peringatan');
            } else if (data.result == 5) {
                toastr.error('LHK Tanggal Kebutuhan '+Config._tanggalLocal(data.tanggal_kirim,'-',' ')+' belum dientri.', 'Peringatan');
            } else if (data.result == 6) {
                toastr.error('Terdapat pengambilan dari Pakan Rusak yang belum dilakukan. Untuk Tanggal Kebutuhan '+Config._tanggalLocal(tanggal_kebutuhan,'-',' ')+'.', 'Peringatan');
            } else if (data.result == 7){
                toastr.error('Stok gudang tidak ada.','Peringatan');
            } else {
                toastr.error('Generate permintaan Gagal.', 'Peringatan');
            }
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
            ajax_detail_pengambilan(no_order, _tab_active, data_ke);
        }
    }
}

function ajax_detail_pengambilan(no_order, _tab_active, data_ke){
    $.ajax({
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
    $("#picking-list-table").html('');
    var tanggal_kirim_awal = $("#tanggal-kirim-awal").val();
    var tanggal_kirim_akhir = $("#tanggal-kirim-akhir").val();
    var checkbox_normal = $("#checkbox_normal").val();
    var checkbox_mutasi = $("#checkbox_mutasi").val();
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
                checkbox_mutasi : checkbox_mutasi,
                checkbox_retur : checkbox_retur,
                checkbox_belum_proses : checkbox_belum_proses
            },
            dataType : 'html',
            success : function(data) {
                $("#picking-list-table").html(data);
            }
        });
    //} else {
    //    toastr.error('Range tanggal kirim tidak valid.', 'Peringatan');
    //}
}

function get_data_riwayat_pengambilan() {
    $("#tabel-riwayat").html('');
    var no_reg = $("#p_kandang").val();

        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/get_data_riwayat_pengambilan",
            data : {
                no_reg : no_reg
            },
            dataType : 'html',
            success : function(data) {
                $("#tabel-riwayat").html(data);
                var tabel_riwayat = $('#tabel-riwayat>table.table');
                if(tabel_riwayat.length > 0) {
                    tabel_riwayat.scrollabletable({
                        'max_height_scrollable' : 300,
                        'scroll_horizontal' : 0,
                    });
                }
            }
        });
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

function view_data_referensi(elm){
    var kode = $(elm).attr('data-kode-no-referensi');
    var no_referensi = $(elm).text();
    switch(kode){
        case 'MT' :
            
        $.ajax({
            type : "POST",
            url : "mutasi_pakan/main/index",
            data : {
                no_mutasi : no_referensi
            },
            dataType : 'html',
            success : function(data) {
                $('#main_content').html(data);           
            }
        });
        break;
        case 'OUT' :
            ajax_detail_pengambilan(no_referensi, 1);
        break;
        case 'RP' :
            $.ajax({
                type : "POST",
                url : "pengembalian_pakan_rusak/pengembalian/index",
                dataType : 'html',
                success : function(data) {
                    $('#main_content').hide();
                    $('#main_content').html(data);           
                }
            }).done(function(){
                var tmp_no_retur = no_referensi;
                no_referensi = no_referensi.substr(3);
                $.ajax({
                    type : "POST",
                    url : "pengembalian_pakan_rusak/pengembalian/list_pengembalian",
                    data : {
                        no_retur : no_referensi
                    },
                    dataType : 'html',
                    success : function(data) {
                        $('#list_pengembalian').html(data);        
                        $.each($('table.list_pengembalian tbody tr'),function(){
                            var no_retur = $(this).find('td.no_retur').text();
                            if(no_retur == tmp_no_retur){
                                $(this).find('td.no_retur span.link_span').click();
                            }
                        });
                        $('#main_content').show();
                    }
                });

            });
        break;
    }
}
    
function get_berat_timbang(elm){   
    $(elm).removeAttr('readonly');
    //console.log('OK');
    setTimeout(function(){
        var berat = $(elm).val();
        $(elm).val(berat);
        $(elm).attr('readonly', true);
    }, 0);
}

function replace_timbang(elm){
    //console.log($(elm).val());
    $(elm).select().focus().val($(elm).val());
        
}
    
function selected(elm){
    $(elm).select().focus();
}