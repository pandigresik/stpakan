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
    $("#tanggal_awal").datepicker({
        dateFormat : 'dd M yy',
        onClose : function(selectedDate) {
            $("#tanggal_akhir").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#tanggal_akhir").datepicker({
        dateFormat : 'dd M yy',
        onClose : function(selectedDate) {
            $("#tanggal_awal").datepicker("option", "maxDate", selectedDate);
        }
    });

}())

kontrol_checkbox();

tampilkan();

function kontrol_checkbox() {
    var _val = $('#belum_tindak_lanjut').val();
    $('#form_input').find('input, select').val('');

    var pengambilan = $('#no_mutasi').attr('data-pengambilan');
    var no_mutasi = $('#no_mutasi').attr('value');
    if(pengambilan == 1){
        $('#no_mutasi').attr('data-pengambilan','0');
        $('#belum_tindak_lanjut').val('1');
        $('#no_mutasi').val(no_mutasi);
        _val = $('#belum_tindak_lanjut').val();
    }
    
    
    if(_val==1){
        $('#belum_tindak_lanjut').val('0');
        $('#belum_tindak_lanjut').removeAttr('checked');
        $('#form_input').find('input, select, button').removeAttr('disabled');
    }
    else{
        $('#belum_tindak_lanjut').val('1');
        $('#belum_tindak_lanjut').attr('checked', 'true');
        $('#form_input').find('input, select, button').attr('disabled', 'true');
        $('#belum_tindak_lanjut').removeAttr('disabled');

        tampilkan();
    } 
    

    
}

function tampilkan(){
    var belum_tindak_lanjut = $('#belum_tindak_lanjut').val();
    var farm = $('#farm').val();
    var no_mutasi = $('#no_mutasi').val();
    var tanggal = $('#tanggal').val();
    var tanggal_awal = $('#tanggal_awal').val();
    tanggal_awal = (tanggal_awal) ? Config._tanggalDb(tanggal_awal,' ','-') : '' ;
    var tanggal_akhir = $('#tanggal_akhir').val();
    tanggal_akhir = (tanggal_akhir) ? Config._tanggalDb(tanggal_akhir,' ','-') : '' ;
    var kandang = $('#kandang').val();
    var img_src = base_url+"assets/images/ajax-loader.gif";
    $('#daftar_mutasi_pakan div.panel-body').html('');
    $('#detail_mutasi_pakan div.panel-body').html('');
    $('#detail_mutasi_pakan').addClass('hide');
    //if(belum_tindak_lanjut == 1 || no_mutasi && tanggal && tanggal_awal && tanggal_akhir && kandang){
        $('#daftar_mutasi_pakan').removeClass('hide');
        $.ajax({
            type : "POST",
            url : "mutasi_pakan/main/get_data_mutasi_pakan",
            data : {
                farm : farm,
                belum_tindak_lanjut : belum_tindak_lanjut,
                no_mutasi : no_mutasi,
                tanggal : tanggal,
                tanggal_awal : tanggal_awal,
                tanggal_akhir : tanggal_akhir,
                kandang : kandang 
            },
            dataType : 'html',
            beforeSend:function(){
                $('#daftar_mutasi_pakan div.panel-body').html('<div class="text-center"><img width="2%" height="2%" src="'+img_src+'"></div>');
            },
            success : function(data) {
                $('#daftar_mutasi_pakan div.panel-body').html(data);
                inisialisasi();

                var _max_height = 300;
                var _height = $('#daftar_mutasi_pakan div.panel-body').find('table#tabel_daftar_mutasi_pakan').height();

                if(_height > _max_height){
                    $('#daftar_mutasi_pakan div.panel-body').find('table#tabel_daftar_mutasi_pakan').scrollabletable({
                        'scroll_horizontal' : 0,
                        'max_height_scrollable' : _max_height,
                        //'max_width' : 1200,
                    });
                }
            }
        });
    //}
    //else{
    //    toastr.warning('Parameter input harus lengkap.', 'Informasi');
    //}
}

function detail_mutasi(elm){
    var no_mutasi = $(elm).parents('td.no_mutasi').text();
    var kode_farm = $(elm).parents('td.no_mutasi').attr('data-kode-farm');
    var level_user = $(elm).parents('td.no_mutasi').attr('data-level-user');
    var jenis_pakan = $(elm).parents('tr').find('td.jenis_pakan').attr('data-jenis-pakan');
    var img_src = base_url+"assets/images/ajax-loader.gif";
    $('#detail_mutasi_pakan div.panel-body').html('');
    $('#detail_mutasi_pakan').removeClass('hide');
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/main/get_detail_mutasi_pakan",
        data : {
            kode_farm : kode_farm,
            jenis_pakan : jenis_pakan,
            no_mutasi : no_mutasi
        },
        dataType : 'html',
        beforeSend:function(){
            $('#detail_mutasi_pakan div.panel-body').html('<div class="text-center"><img width="2%" height="2%" src="'+img_src+'"></div>');
        },
        success : function(data) {
            $('#detail_mutasi_pakan div.panel-body').html(data);
            var parents_class;
            var alasan;
            switch(level_user){
                case 'kd':
                    alasan = $(elm).parents('tr').find('td.tindak_lanjut_kepala_farm p.alasan').text();
                    parents_class = "tindak_lanjut_kepala_departemen";
                break;
                case 'kdv':
                    var _alasan = $(elm).parents('tr').find('td.tindak_lanjut_kepala_departemen p').html();
                    _alasan = _alasan.split('<span');
                    alasan = _alasan[0];
                    parents_class = "tindak_lanjut_kepala_divisi";
                break;
            }
            var waktu = $(elm).parents('tr').find('td.'+parents_class).attr('data-waktu-tindak-lanjut');
            $('#tabel_daftar_mutasi_pakan tr .'+parents_class+' form').addClass('hide');
            if(!waktu){
                $(elm).parents('tr').find('td.'+parents_class+' form').removeClass('hide');
                $(elm).parents('tr').find('td.'+parents_class+' textarea.keterangan').val(alasan).focus();
                $(elm).parents('tr').find('td.'+parents_class+' span.btn').attr('disabled', true);  

                
            }
            
        }
    });
}

function get_data_kandang(){
    var farm = $('#farm').val();
    $('#kandang').html('');
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/main/get_data_kandang",
        data : {
            farm : farm
        },
        dataType : 'json',
        success : function(data) {
            var _html = "<option value=''>Pilih</option>";
            $.each(data, function(k, v){
                _html += "<option value='"+v.kode_kandang+"'>"+v.nama_kandang+"</option>";
            });
            $('#kandang').html(_html);  
            
        }
    });
}

function inisialisasi(){
    switch(level_user){
        case 'kf':
            $('#btn_baru').removeClass('hide');
            //$('#tabel_daftar_mutasi_pakan tbody tr td.tindak_lanjut_kepala_farm').removeClass('hide');
            $.each($('#tabel_daftar_mutasi_pakan tbody tr'),function(){
                if(!$(this).find('td.tindak_lanjut_kepala_departemen').attr('data-waktu-tindak-lanjut')){
                    $(this).find('td.tindak_lanjut_kepala_departemen').html('');
                }
                if(!$(this).find('td.tindak_lanjut_kepala_divisi').attr('data-waktu-tindak-lanjut')){
                    $(this).find('td.tindak_lanjut_kepala_divisi').html('');
                }

            });

        break;
        case 'ag':
            $('#btn_baru').addClass('hide');
            //$('#tabel_daftar_mutasi_pakan tbody tr td.tindak_lanjut_kepala_farm').removeClass('hide');
            $.each($('#tabel_daftar_mutasi_pakan tbody tr'),function(){
                if(!$(this).find('td.tindak_lanjut_kepala_departemen').attr('data-waktu-tindak-lanjut')){
                    $(this).find('td.tindak_lanjut_kepala_departemen').html('');
                }
                if(!$(this).find('td.tindak_lanjut_kepala_divisi').attr('data-waktu-tindak-lanjut')){
                    $(this).find('td.tindak_lanjut_kepala_divisi').html('');
                }

            });

        break;
        case 'kd':
            $('#btn_baru').addClass('hide');
            //$('#tabel_daftar_mutasi_pakan tr .tindak_lanjut_kepala_farm').addClass('hide');
            $.each($('#tabel_daftar_mutasi_pakan tbody tr'),function(){
                if(!$(this).find('td.tindak_lanjut_kepala_divisi').attr('data-waktu-tindak-lanjut')){
                    $(this).find('td.tindak_lanjut_kepala_divisi').html('');
                }
            });
        break;
        case 'kdv':
            $('#btn_baru').addClass('hide');
            //$('#tabel_daftar_mutasi_pakan tr .tindak_lanjut_kepala_farm').addClass('hide');
        break;
    }
        
}

function keterangan_kontrol(elm){
    var keterangan = $(elm).val();
    var parents_class = $(elm).parents('td').attr('class');
    if(keterangan.length >= 20){
        $(elm).parents('td.'+parents_class).find('span.btn').removeAttr('disabled');
    }
    else{
        $(elm).parents('td.'+parents_class).find('span.btn').attr('disabled', true);
    }
}

function konfirmasi_tindak_lanjut(elm, aksi){
    var parents_class = $(elm).parents('td').attr('class');
    if(aksi == 1){
        var aksi_tindak_lanjut = 'approval';
        var hasil_tindak_lanjut = 'approve';
        var keputusan = "RV";
    }
    else if(aksi == 2){
        var aksi_tindak_lanjut = 'Reject untuk Review Ulang';
        var hasil_tindak_lanjut = 'reject (Review Ulang)';
        var keputusan = "V";
    }
    else if(aksi == 3){
        var aksi_tindak_lanjut = 'approval';
        var hasil_tindak_lanjut = 'approve';
        var keputusan = "A";
    }
    else if(aksi == 4){
        var aksi_tindak_lanjut = 'Reject untuk Review Ulang';
        var hasil_tindak_lanjut = 'reject (Review Ulang)';
        var keputusan = "RU";
    }
    else{
        var aksi_tindak_lanjut = 'reject';
        var hasil_tindak_lanjut = 'reject';
        var keputusan = "RJ";
    }
    var alasan = $(elm).parents('td.'+parents_class).find('textarea.keterangan').val();
    var no_mutasi = $(elm).parents('tr').find('td.no_mutasi').text();
    bootbox.dialog({
        message : "Apakah Anda yakin akan melakukan "+aksi_tindak_lanjut+"?",
        title : "",
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
                className : "btn-default",
                callback : function() {
                    tindak_lanjut(no_mutasi, keputusan, alasan, function(data){
                        if(data.keputusan == keputusan){
                            messageBox('', '', 'Mutasi Pakan dengan No : '+data.no_mutasi+' Berhasil Di-'+hasil_tindak_lanjut+'.');
                            tampilkan();
                        }
                        else{
                            messageBox(elm, '', 'Mutasi Pakan dengan No : '+data.no_mutasi+' Gagal Di-'+hasil_tindak_lanjut+'.');
                        }
                    });
                }
            }
        }
    });
}

function tindak_lanjut(no_mutasi, keputusan, alasan, callback){
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/main/tindak_lanjut",
        data : {
            no_mutasi : no_mutasi,
            keputusan : keputusan,
            alasan : alasan
        },
        dataType : 'json',
        success : function(data) {
            callback(data);            
        }
    });
}


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
        if(element){
            $(element).focus().select();
        }
    });
}

function kontrol_tanggal(){
    $('#tanggal_awal').val('');
    $('#tanggal_akhir').val('');
}

function revisi(elm){
    var no_mutasi = $(elm).parents('tr').find('td.no_mutasi').text();

    
    var no_reg = $(elm).parents('tr').find('td.kandang').attr('data-no-reg');
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/transaksi/revisi",
        data : {
            no_mutasi : no_mutasi,
            no_reg : no_reg
        },
        dataType : 'html',
        success : function(data) {
            $('#main_content').html(data);           
        }
    });

}

function ack(elm){
    var no_mutasi = $(elm).parents('tr').find('td.no_mutasi').text();
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/main/ack",
        data : {
            no_mutasi : no_mutasi
        },
        dataType : 'json',
        success : function(data) {
            if(data.keputusan == 'ACK'){
                $('#toast-container div.toast div.toast-message:contains("'+no_mutasi+'")').parents('div.toast').find('button').click();

                tampilkan();      
            }
            else{
                toastr.error('Ack gagal.', 'Informasi');
            }   
        }
    });

}