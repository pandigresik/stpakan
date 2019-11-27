(function() {
    'use strict';
    $('div').on('click', 'a.btn', function(e) {
        // console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    });
    $('ul.pagination').on('click', 'a', function(e) {
        // console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    });
    

    $('#kuantitas_pemberian_pakan').numeric({
        allowPlus : false,
        allowMinus : false,
        allowThouSep : false,
        allowDecSep : false
    });

    $('#kandang_asal').change(function(){
        cek_lhk();
    });

    //$('select#jenis_pakan option').addClass('hide');

}())

function batal() {
    reset();
    $('#div_baru').find('input:not([readonly]), select:not([readonly])').val('');
    
    $('.test-tooltip').tooltipster('hide');
}

function tampilkan(){
    reset();
    var no_mutasi = $('#no_mutasi').text();
    //var tanggal_pemberian = $('#tanggal_pemberian').val();
    var tanggal_pemberian = $('#tanggal_pemberian').attr('data-tanggal-pemberian');
    var nama_farm = $('#nama_farm').val();
    var jenis_pakan = $('#jenis_pakan').val();
    var kuantitas_pemberian_pakan = $('#kuantitas_pemberian_pakan').val();
    var tanggal_kebutuhan = $('#tanggal_kebutuhan').val();
    var kandang_asal = $('#kandang_asal').val();
    var alasan = $('#alasan').val();
    var img_src = base_url+"assets/images/ajax-loader.gif";
    $('#daftar_kandang div.panel-body').html('');
    if(tanggal_pemberian && nama_farm && jenis_pakan && kuantitas_pemberian_pakan && tanggal_kebutuhan && kandang_asal && alasan){
        $('#daftar_kandang').removeClass('hide');
        $.ajax({
            type : "POST",
            url : "mutasi_pakan/transaksi/get_daftar_kandang",
            data : {
                no_mutasi : no_mutasi,
                tanggal_pemberian : tanggal_pemberian,
                nama_farm : nama_farm,
                jenis_pakan : jenis_pakan,
                kuantitas_pemberian_pakan : kuantitas_pemberian_pakan,
                tanggal_kebutuhan : tanggal_kebutuhan,
                kandang_asal : kandang_asal
            },
            dataType : 'html',
            beforeSend:function(){
                $('#daftar_kandang div.panel-body').html('<div class="text-center"><img width="2%" height="2%" src="'+img_src+'"></div>');
            },
            success : function(data) {
                $('#daftar_kandang div.panel-body').html(data);

                $("input.aksi").spinner({
                    min : 1
                });

                $('a.ui-spinner-up, a.ui-spinner-down').click(function(){
                    kontrol_aksi(this, 1);
                });

                $('input.aksi').numeric({
                    allowPlus : false,
                    allowMinus : false,
                    allowThouSep : false,
                    allowDecSep : false
                });

                $('#tabel_daftar_kandang').scrollabletable({
                    'max_height_scrollable' : 300,
                    'scroll_horizontal' : 0,
                });

                var sisa = get_sisa_kuantitas_pemberian_pakan('');
                //console.log(sisa);
                if(sisa<0){

                    $('#tabel_daftar_kandang tbody td.aksi span.ui-spinner').css('border-color','red');
                    sisa = $('#kuantitas_pemberian_pakan').val()
                }
                else{
                    $('#tabel_daftar_kandang tbody td.aksi span.ui-spinner').css('border-color','#ddd');

                }
                $('input#aksi').val(sisa);
                if(sisa != 0){
                    $('#btn_simpan').attr('disabled', true);
                }
            }
        });

    }
    else{
        toastr.warning('Parameter input harus lengkap.', 'Informasi');
    }
}

function checkbox_kandang(elm){
    $(elm).parents('td.aksi').find('input.aksi').val('');
    var sisa = get_sisa_kuantitas_pemberian_pakan(elm);
    if(sisa<=0){
        $(elm).val('0');
        $(elm).attr('checked',false);
        toastr.warning('Total kuantitas aksi melebihi kuantitas pemberian pakan.','Informasi');
    }
    else{
        $('#tabel_daftar_kandang tbody td.aksi span.ui-spinner').css('border-color','#ddd');

        //var kuantitas_pemberian_pakan = $('#kuantitas_pemberian_pakan').val();
        //kuantitas_pemberian_pakan = parseInt(kuantitas_pemberian_pakan);
        var _val = $(elm).val();
        var stok_gudang = $(elm).parents('tr').find('td.stok_gudang').text();
        stok_gudang = parseInt(stok_gudang);
        var aksi = (stok_gudang<=sisa && stok_gudang>0) ? stok_gudang : sisa;
        if(_val==1){
            $(elm).val('0');
            $(elm).parents('td.aksi').find('input.aksi').addClass('hide');
        }
        else{
            $(elm).val('1');
            $(elm).parents('td.aksi').find('input.aksi').val(aksi);
            $(elm).parents('td.aksi').find('input.aksi').removeClass('hide');
            $(elm).parents('td.aksi').find('input.aksi').select().focus();
        }


    }
    var no_mutasi = $('#no_mutasi').text();
    var _sisa = get_sisa_kuantitas_pemberian_pakan(elm) ;
    if(no_mutasi && _sisa < 0){
        _sisa = $('#kuantitas_pemberian_pakan').val();
    }
    $('input#aksi').val(_sisa);
}

function get_sisa_kuantitas_pemberian_pakan(elm){

    var kuantitas_pemberian_pakan = $('#kuantitas_pemberian_pakan').val();
    kuantitas_pemberian_pakan = parseInt(kuantitas_pemberian_pakan);
    $.each($('#tabel_daftar_kandang tbody tr'),function(){
        if($(this).find('td.aksi input.checkbox').is(':checked')){
            var aksi = $(this).find('td.aksi input.aksi').val();
            aksi = (aksi) ? parseInt(aksi) : 0;
            kuantitas_pemberian_pakan = kuantitas_pemberian_pakan-aksi;

        }
    });

    (kuantitas_pemberian_pakan==0) ? $('#btn_simpan').removeAttr('disabled') : $('#btn_simpan').attr('disabled', true);

    return kuantitas_pemberian_pakan;
}

function kontrol_aksi(elm, spinner){
    var aksi = $(elm).parents('span.ui-spinner').find('input.aksi').attr('aria-valuenow');
    aksi = (spinner) ? parseInt(aksi) - 1 : parseInt(aksi);
    var sisa = get_sisa_kuantitas_pemberian_pakan(elm);
    $(elm).parents('span.ui-spinner').find('input.aksi').select().focus();
    if(sisa<0){
        $(elm).parents('span.ui-spinner').find('input.aksi').val(aksi);
        $(elm).parents('span.ui-spinner').find('input.aksi').attr('aria-valuenow', aksi);
        toastr.warning('Total kuantitas aksi melebihi kuantitas pemberian pakan.','Informasi');
    }
    else{
        $('#tabel_daftar_kandang tbody td.aksi span.ui-spinner').css('border-color','#ddd');
    }
    var no_mutasi = $('#no_mutasi').text();
    var _sisa = get_sisa_kuantitas_pemberian_pakan(elm) ;
    if(no_mutasi && _sisa < 0){
        _sisa = $('#kuantitas_pemberian_pakan').val();
    }
    $('input#aksi').val(_sisa);
}

function kontrol_kuantitas_pemberian_pakan(){
    $('.test-tooltip').tooltipster({
        animation: 'fade',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'custom',
        hideOnClick: true,
        position: 'right'
    });
    $('.test-tooltip').tooltipster('hide');
    var kuantitas_pemberian_pakan = $('#kuantitas_pemberian_pakan').val();
    if(kuantitas_pemberian_pakan.length > 0){
        get_konsumsi_per_ekor(function(data){
            //if(data.konsumsi_per_ekor < data.standar_konsumsi_budidaya){
            if(parseFloat(data.konsumsi_per_ekor) < parseFloat(data.standar_konsumsi_budidaya)){
                $('.test-tooltip').tooltipster('show');
            } 
        });
        
    }
    reset();
}

function get_konsumsi_per_ekor(callback) {

    //var tanggal_pemberian = $('#tanggal_pemberian').val();
    var tanggal_pemberian = $('#tanggal_pemberian').attr('data-tanggal-pemberian');
    var nama_farm = $('#nama_farm').val();
    var jenis_pakan = $('#jenis_pakan').val();
    var kuantitas_pemberian_pakan = $('#kuantitas_pemberian_pakan').val();
    //var tanggal_kebutuhan = $('#tanggal_kebutuhan').val();
    var tanggal_kebutuhan = $('#tanggal_kebutuhan').attr('data-tanggal-kebutuhan');
    var kandang_asal = $('#kandang_asal').val();
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/transaksi/get_konsumsi_per_ekor",
        data : {
            tanggal_pemberian : tanggal_pemberian,
            nama_farm : nama_farm,
            jenis_pakan : jenis_pakan,
            kuantitas_pemberian_pakan : kuantitas_pemberian_pakan,
            tanggal_kebutuhan : tanggal_kebutuhan,
            kandang_asal : kandang_asal 
        },
        dataType : 'json',
        success : function(data) {
            callback(data);
        }
    });
}

function reset(){
    $('#daftar_kandang').addClass('hide');
    $('#daftar_kandang div.panel-body').html('');
    $('#btn_simpan').attr('disabled',true);
}

function simpan(){
    var _message = '<div class="form-group form-horizontal new-line">';
    _message += '<label>Apakah Anda akan Melanjutkan Proses Simpan?</label>';
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

                    var aksi = $('#btn_simpan').attr('data-aksi');
                    var tanggal_pemberian = $('#tanggal_pemberian').attr('data-tanggal-pemberian');
                    var nama_farm = $('#nama_farm').val();
                    var jenis_pakan = $('#jenis_pakan').val();
                    var kuantitas_pemberian_pakan = $('#kuantitas_pemberian_pakan').val();
                    var tanggal_kebutuhan = $('#tanggal_kebutuhan').attr('data-tanggal-kebutuhan');
                    var kandang_asal = $('#kandang_asal').val();
                    var no_reg_asal = $('#kandang_asal option:selected').attr('data-no-reg');
                    var no_mutasi = $('#no_mutasi').text();
                    var alasan = $('#alasan').val();
                    var data_detail = [];
                    $.each($('#tabel_daftar_kandang tbody tr'),function(){
                        var checked = $(this).find('td.aksi input.checkbox').is(':checked');
                        if(checked){
                            var no_reg_tujuan = $(this).find('td.kandang').attr('data-no-reg');
                            var umur = $(this).find('td.umur').text();
                            var dh = $(this).find('td.dh').text();
                            var fcr = $(this).find('td.fcr').text();
                            var ip = $(this).find('td.ip').text();
                            var stok_gudang = $(this).find('td.stok_gudang').text();
                            var berat_stok_gudang = $(this).find('td.stok_gudang').attr('data-berat-stok-gudang');
                            var stok_kandang = $(this).find('td.stok_kandang').text();
                            var berat_stok_kandang = $(this).find('td.stok_kandang').attr('data-berat-stok-kandang');
                            var jml_terima = $(this).find('td.aksi input.aksi').val();
                            data_detail.push({
                                no_reg_tujuan : no_reg_tujuan,
                                umur : umur,
                                dh : dh,
                                fcr : fcr,
                                ip : ip,
                                stok_gudang : stok_gudang,
                                stok_kandang : stok_kandang,
                                berat_stok_gudang : berat_stok_gudang,
                                berat_stok_kandang : berat_stok_kandang,
                                jml_terima : jml_terima
                            })
                        }
                    });
                    var data = {
                        aksi : aksi,
                        no_mutasi : no_mutasi,
                        tanggal_pemberian : tanggal_pemberian,
                        nama_farm : nama_farm,
                        jenis_pakan : jenis_pakan,
                        kuantitas_pemberian_pakan : kuantitas_pemberian_pakan,
                        tanggal_kebutuhan : tanggal_kebutuhan,
                        kandang_asal : kandang_asal,
                        no_reg_asal : no_reg_asal,
                        alasan : alasan,
                        data_detail : data_detail
                    }

                    //console.log(data);

                    simpan_mutasi(data, function(result){
                        if(result.result == 1){
                            if(no_mutasi){
                                $('#toast-container div.toast div.toast-message:contains("'+no_mutasi+'")').parents('div.toast').find('button').click();
                            }
                            messageBox('', '', 'Mutasi pakan antar kandang berhasil disimpan dengan no mutasi : '+result.no_mutasi);
                            batal();
                        }
                        else{
                            messageBox('#btn_simpan', '', 'Mutasi pakan antar kandang gagal disimpan.');
                        }
                    });
                    
                    return true;
                }
            }
        }
    });

}

function simpan_mutasi(data, callback){
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/transaksi/simpan_mutasi",
        data : {
            data : data
        },
        dataType : 'json',
        success : function(msg) {
            callback(msg);
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

function cek_lhk() {
    var tanggal_pemberian = $('#tanggal_pemberian').attr('data-tanggal-pemberian');
    var no_reg_asal = $('#kandang_asal option:selected').attr('data-no-reg');
    var kandang = $('#kandang_asal option:selected').text();
    $.ajax({
        type : "POST",
        url : "mutasi_pakan/transaksi/cek_lhk",
        data : {
            tgl_transaksi : tanggal_pemberian,
            no_reg_asal : no_reg_asal
        },
        dataType : 'json',
        success : function(data) {
            //console.log(data);
            if(!data['berat_badan']){
                $('#btn_tampilkan').attr('disabled',true);
                $('select#jenis_pakan').val('');
                toastr.warning('LHK '+kandang+' tanggal '+Config._tanggalLocal(data['tgl_transaksi'],'-',' ')+' belum dientri.', 'Informasi');
            }
            else{
                if(parseFloat(data['berat_badan']) <= 0){
                    $('#btn_tampilkan').attr('disabled',true);
                    $('select#jenis_pakan').val('');
                    messageBox('', '', 'Proses mutasi pakan tidak dapat dilanjutkan. Mohon entri penimbangan berat badan ada LHK terlebih dahulu.');
            
                }
                else{
                 data_jenis_pakan();

                }
            }
            
        }
    });
}

function data_jenis_pakan() {
    var no_reg_asal = $('#kandang_asal option:selected').attr('data-no-reg');
    if(no_reg_asal){
        $.ajax({
            type : "POST",
            url : "mutasi_pakan/transaksi/data_jenis_pakan",
            data : {
                no_reg_asal : no_reg_asal
            },
            dataType : 'json',
            success : function(data) {
                var html = "<option value=''>Pilih</option>";
                $.each(data, function(key, value){
                    html += "<option value='"+value.kode_barang+"'>"+value.nama_barang+"</option>";
                });
                $('select#jenis_pakan').html(html);
                $('select#jenis_pakan').val('');
            }
        });
    }
    else{
        var html = "<option value=''>Pilih</option>";
        $('select#jenis_pakan').html(html);
        $('select#jenis_pakan').val('');
    }
}