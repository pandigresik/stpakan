( function() {
    'use strict';

    $('div').on('click', 'a.btn', function(e) {
        // //console.log(e.target);
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })

     $("#tanggal-kirim-awal").datepicker({
            //  defaultDate: "+1w",
              dateFormat : 'dd M yy',
              onClose: function( selectedDate ) {
                $( "#tanggal-kirim-akhir" ).datepicker( "option", "minDate", selectedDate );
              }
           }).val(Config._tanggalLocal(Config._getDateStr(new Date()),'-',' '));
         $("#tanggal-kirim-akhir").datepicker({
            //  defaultDate: "+1w",
              dateFormat : 'dd M yy',
              onClose: function( selectedDate ) {
                $( "#tanggal-kirim-awal" ).datepicker( "option", "maxDate", selectedDate );
            }
          }).val(Config._tanggalLocal(Config._getDateStr(new Date()),'-',' '));

         $('input.filter[name="tanggal_kirim"').datepicker({
              dateFormat : 'dd M yy',
          })


    $("#next").on("click", function() {
        page_number = (page_number + 1);
        getReport(page_number);
    });

    $("#previous").on("click", function() {
        page_number = (page_number - 1);
        getReport(page_number);
    });

    $('input.filter').keyup(function() {
        this.value = this.value.toUpperCase();
        goSearch();
    });

    $('input.filter').change(function() {
        goSearch();
    });

    $(document).on('change', '.btn-file :file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            $('#lampirkan-foto').val(label);
    });

    $('a.seru').tooltip();

    $('#nopol-terima').alphanum({allowSpace:false});
    $('#sopir').alpha({allow : "'"});

}())

var search = false;
var page_number = 0;
var total_page = null;
var detail_kandang;
var berat_standart;
var array_format = ['doc','docx'];
var no_kavling_pakan_rusak = 'DMG';

//getReport(page_number);

$('#nomor-do').focus();

check_dblclick();

remove_local_storage();


function check_dblclick(){
    var nomor_do = $('#nomor-do').val();
    if(nomor_do){
        input_do();
        //$('#nopol-terima').select().focus();
    }
}

function isValidNopol(nopol){
    var polaNopol = /[A-Z]+[0-9]+[A-Z]+/;
    return polaNopol.test(nopol);
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function kontrol_chekbox(elm){
    $(elm).is(':checked') ? $(elm).val('1') : $(elm).val('0');
}

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

    var do_belum_diterima = $('#do_belum_diterima').val();
    var tanggal_kirim_awal = $('#tanggal-kirim-awal').val();
    tanggal_kirim_awal = Config._tanggalDb(tanggal_kirim_awal,' ','-');
    var tanggal_kirim_akhir = $('#tanggal-kirim-akhir').val();
    tanggal_kirim_akhir = Config._tanggalDb(tanggal_kirim_akhir,' ','-');
    var no_op = $('input.filter[name="no_op"]').val();
    var no_do = $('input.filter[name="no_do"]').val();
    var no_sj = $('input.filter[name="no_sj"]').val();
    var nama_ekspedisi = $('input.filter[name="nama_ekspedisi"]').val();
    var tanggal_kirim = $('input.filter[name="tanggal_kirim"]').val();
    if(tanggal_kirim){
        tanggal_kirim = Config._tanggalDb(tanggal_kirim,' ','-');
    }

    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "penerimaan_pakan/main/get_pagination/",
        data : {
            do_belum_diterima : do_belum_diterima,
            tanggal_kirim_awal : tanggal_kirim_awal,
            tanggal_kirim_akhir : tanggal_kirim_akhir,
            no_op : no_op,
            no_do : no_do,
            no_sj : no_sj,
            nama_ekspedisi : nama_ekspedisi,
            tanggal_kirim : tanggal_kirim,
            page_number : page_number,
            search : search
        }
    }).done(function(data) {
        $("#daftar-do-table table tbody").html("");

        window.mydata = data;

        if (!empty(mydata.length)) {
            if (mydata.length > 0) {
                total_page = mydata[0].TotalRows;
                $("#total_page").text(total_page);
                var record_par_page = mydata[0].Rows;

                $.each(record_par_page, function(key, data) {
                    var pink = (data.pink == 1) ? "pink" : "";
                    var td_ba = (data.no_ba) ? "<td data-no-ba='"+data.no_ba+"' onclick='print_view_berita_acara(this)'><b style='color:blue;'>"+data.no_ba+"</b></td>" : "<td data-no-ba='"+data.no_ba+"'>"+data.no_ba+"</td>";
                    var _tgl_kirim = (data.tanggal_kirim) ? Config._tanggalLocal(Config._getDateStr(new Date(data.tanggal_kirim)),'-',' ') : '' ;
                    var _tgl_terima = (data.tanggal_terima) ? Config._tanggalLocal(Config._getDateStr(new Date(data.tanggal_terima)),'-',' ') : '';
                    $("#daftar-do-table table tbody").append('<tr ondblclick="baru(this,1)" data-no-do="'+data.no_do+'" class="'+pink+'"><td>' + data.no_op + '</td><td>' + data.no_do + '</td><td>' + data.no_sj + '</td><td>' + data.nama_ekspedisi + '</td><td>' + _tgl_kirim + '</td><td>' + _tgl_terima + '</td><td>' + data.jam_terima + '</td><td>' + data.penerima + '</td>' + td_ba + '</tr>');

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

function print_view_berita_acara(elm){
    var no_berita_acara = $(elm).attr('data-no-ba');
    if(no_berita_acara && no_berita_acara != '-'){
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/view_berita_acara",
            data : {
                no_berita_acara : no_berita_acara
            },
            dataType : 'html',
            success : function(data) {
                bootbox.dialog({
                    message : data,
                    title : "Berita Acara - Print Preview",
                    className : "very-large",
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
        });
    }
}

function baru(elm, dblclick) {

    var no_do = (dblclick == 1) ? $(elm).attr('data-no-do') : '' ;

    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/index",
        data : {
            nomor_do : no_do
        },
        dataType : 'html',
        success : function(data) {
            $('#main_content').html(data);
        }
    });
}

function reset_detail_do(){
    $('#nopol-terima').next().remove();
    $('#nomor-do').next().remove();
    $('label[for="label"]').addClass('grey-label');
    $('label[for="input"]').addClass('grey-label');
    $('label[for="input"]').text('');
    $('#nopol-terima').val('');
    $('#sopir').val('');
    $('#nopol-terima').attr('readonly',true);
    $('#sopir').attr('readonly',true);
    $('#div-tanggal-terima').addClass('hide');
    $('#btn-verifikasi').show();
    $('#btn-verifikasi').attr('disabled',true);
    $('#penimbangan-pakan').html('');
    $('#pakan-rusak-hilang').html('');

}


function verifikasi_do(callback) {
    var nomor_do = $('#nomor-do').val();
    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/verifikasi_do",
        data : {
            nomor_do : nomor_do
        },
        dataType : 'json',
        success : function(data) {
            callback(data);
        }
    })

}

function data_sj() {
    var daftar_do_dan_sj;
    if(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan)){
        daftar_do_dan_sj = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
    }
    var _do = [];
    var _spm = [];
    var _sj = [];
    if(daftar_do_dan_sj){
        $.each(daftar_do_dan_sj, function(key, value){
            _do.push(key);
            _spm.push(value['no_spm']);
            _sj.push(value['no_sj']);
        });
    }
    return {
        'do_params' : _do,
        'spm_params' : _spm,
        'sj_params' : _sj
    };

}

function daftar_do_dan_sj() {
    /*
    var _params = data_sj();
    if(_params.length > 0){
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/daftar_do_dan_sj",
            data : {
                nomor_do : _params
            },
            dataType : 'html',
            success : function(data) {
                $('#panel-daftar-do-sj').removeClass('hide');
                $('#table-daftar-do-sj').html(data);
            }
        })

    }
    */

    var daftar_do_dan_sj = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
    var _html = '<table class="table table-bordered">';
        _html += '<thead>';
        _html += '<tr>';
        _html += '<th>No. DO</th>';
        _html += '<th>No. OP</th>';
        _html += '<th>No. SJ</th>';
        _html += '<th>Ekspedisi</th>';
        _html += '<th>Target Tanggal Kirim</th>';
        _html += '</tr>';
        _html += '</thead>';
        _html += '<tbody>';
        var no_penerimaan = $('#nomor-do').attr('data-no-penerimaan');
        var i = 0;
        $.each(daftar_do_dan_sj, function(key, value){
            _html += '<tr data-kode-flok="'+value.kode_flok+'" data-nama-ekspedisi="'+value.nama_ekspedisi+'">';
            _html += '<td>'+value.no_do+'</td>';
            _html += '<td>'+value.no_op+'</td>';
            var sj = value.no_sj;
            var _no_sj;
            if(no_penerimaan){
                var array_no_sj = [];
                array_no_sj = sj.split(',');
                _no_sj = (!array_no_sj[i]) ? 'N/A' : array_no_sj[i] ;
            }
            else{
                _no_sj = (!sj) ? 'N/A' : sj ;
            }
            _html += '<td>'+_no_sj+'</td>';
            _html += '<td>'+value.nama_ekspedisi+'</td>';
            _html += '<td>'+Config._tanggalLocal(Config._getDateStr(new Date(value.tanggal_kirim)),'-',' ')+'</td>';

            _html += '</tr>';
            i++;
        });
        _html += '</tbody>';
        _html += '</table>';

    $('#panel-daftar-do-sj').removeClass('hide');
    $('#table-daftar-do-sj').html(_html);


}

function validasi_do(data){
    var not_valid_flok = 0;
    var not_valid_nopol_kirim = 0;
    var not_valid_nama_ekspedisi = 0;
    var not_valid_nomor_do = 0;
    var _params = data_sj();
    if(_params['do_params'].length > 0){
        var nomor_do = $('#nomor-do').val();
        var daftar_do_dan_sj = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
        $.each(daftar_do_dan_sj, function(key, value){
            if(value.no_do != nomor_do){
                not_valid_nomor_do++;
            }
            /*
            if(key != nomor_do){
                if(data.kode_flok != value.kode_flok){
                    not_valid_flok++;
                }
                if(value.nopol_kirim && data.nopol_kirim != value.nopol_kirim){
                    not_valid_nopol_kirim++;
                }
                if(data.nama_ekspedisi != value.nama_ekspedisi){
                    not_valid_nama_ekspedisi++;
                }
                    //console.log(data.nama_ekspedisi)
                    //console.log(value.nama_ekspedisi)
                    //console.log(not_valid_nama_ekspedisi)
            }
            */

        });
    }

    var result = {
        'kode_flok' : not_valid_flok,
        'nopol_kirim' : not_valid_nopol_kirim,
        'nama_ekspedisi' : not_valid_nama_ekspedisi,
        'nomor_do' : not_valid_nomor_do

    };

    return result;
}

function validasi_do_old(data){
    var not_valid_flok = 0;
    var not_valid_nopol_kirim = 0;
    var _params = data_sj();
    if(_params['do_params'].length > 0){
        var nomor_do = $('#nomor-do').val();
        var daftar_do_dan_sj = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
        $.each(daftar_do_dan_sj, function(key, value){
            if(key != nomor_do && value.nopol_kirim){
                if(data.kode_flok != value.kode_flok){
                    not_valid_flok++;
                }
                if(data.nopol_kirim != value.nopol_kirim){
                    not_valid_nopol_kirim++;
                }
            }

        });
    }

    var result = {
        'kode_flok' : not_valid_flok,
        'nopol_kirim' : not_valid_nopol_kirim

    };

    return result;
}

function set_all_view(nomor_do){

    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/get_all_data",
        data : {
            nomor_do : nomor_do
        },
        dataType : 'json',
        success : function(data) {
            var sopir;
            var nopol_terima;
            for(var i=0;i<(data.length);i++){
                sopir = data[i]['sopir'];
                nopol_terima = data[i]['nopol_terima'];
                set_daftar_do_dan_sj(data[i], data[i]['no_do']);
                $('#nomor-do').attr('data-no-penerimaan', data[i]['no_penerimaan']);
            }
            daftar_do_dan_sj();
            lanjut();
            $('#nopol-terima').val(nopol_terima);
            $('#sopir').val(sopir);
            verifikasi();
        }
    });

}

function input_do() {
    reset_detail_do();
    verifikasi_do(function(data) {

        var nomor_do = $('#nomor-do').val();
        if(data == 0){
            $('#nomor-do').parent().append('<span class="do-not-valid">*No. DO tidak valid</span>');
        }
        else{
            if(data.no_penerimaan){
            	global_no_penerimaan = 1;
            }
            //console.log(data.no_penerimaan);
            if(data.list_do){
                set_all_view(data.list_do);
            }
            else{
                var validasi = validasi_do(data);
                if(validasi.kode_flok > 0){
                    $('#nomor-do').parent().append('<span class="do-not-valid">*No. DO beda kode flok</span>');
                }
                else if (validasi.nopol_kirim > 0){
                    $('#nomor-do').parent().append('<span class="do-not-valid">*No. DO beda nopol kirim</span>');
                }
                else if (validasi.nama_ekspedisi > 0){
                    $('#nomor-do').parent().append('<span class="do-not-valid">*No. DO beda nama ekspedisi</span>');
                }
                else if (validasi.nomor_do > 0){
                    $('#nomor-do').parent().append('<span class="do-not-valid">*Penerimaan pakan dilakukan per Surat Jalan (DO)</span>');
                }
                else{
                    if(data.validasi_tanggal_kirim == 0){
                        messageBox('Peringatan','Penerimaan terlambat, melebihi target tanggal kirim ('+Config._tanggalLocal(Config._getDateStr(new Date(data.tanggal_kirim)),'-',' ')+') harap melakukan koordinasi lebih lanjut.');
                    }
                    set_daftar_do_dan_sj(data, nomor_do);
                    $('#btn-lanjut').removeClass('hide');
                    $('#btn-reset').removeClass('hide');
                    daftar_do_dan_sj();

                    if(localStorage.getItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan)){
                        lanjut();
                    }
                }

            }
        }
    });

}

function lanjut() {
    var _params = data_sj();
    if(_params['do_params'].length > 0){

        reset_detail_do();

        var _nopol_kirim = 'N/A';
        var _tanggal_terima = 'N/A';
        var _kandang = 'N/A';

        $('#tanggal-terima').text(_tanggal_terima);

        var daftar_do_dan_sj = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
        $.each(daftar_do_dan_sj, function(key, value){

            $('#nomor-do').val('');
            $('#nomor-do').attr('readonly', true);
            $('#btn-lanjut').attr('disabled', true);

            if(value.nopol_kirim){
                 _nopol_kirim = value.nopol_kirim;
            }
            if(value.nama_kandang){
                 _kandang = value.nama_kandang;
            }
            if(value.tanggal_terima){
                 _tanggal_terima = value.tanggal_terima;
                $('#tanggal-terima').text(Config._tanggalLocal(Config._getDateStr(new Date(_tanggal_terima)),'-',' '));
                $('#tanggal-terima').attr('data-tanggal-terima', _tanggal_terima);
            }

            $('#nopol-kirim').text(_nopol_kirim);

            $('#kandang').text(_kandang);

            $('div.detail-do label').removeClass('grey-label');

            $('#div-tanggal-terima').removeClass('hide');

            $('#panel-penerimaan-pakan').removeClass('hide');

        });


        if(localStorage.getItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan)){
            $('#btn-verifikasi').attr('disabled', true);
            var nopol_sopir = JSON.parse(localStorage.getItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan));
            $('#nopol-terima').val(nopol_sopir.nopol_terima);
            $('#sopir').val(nopol_sopir.sopir);
            verifikasi();
        }
        else{

            $('#nopol-terima').removeAttr('readonly');
            $('#sopir').removeAttr('readonly');
            $('#nopol-terima').focus().select();

        }

    }
}

function set_keterangan(){
    $.each($('#tbl-detail-penerimaan').find('input.timbangan_kg'),function(){
        var timbangan_kg = $(this).val();
        var readonly = $(this).attr('readonly');
        if(readonly && timbangan_kg){
            var jml_seharusnya = parseInt($(this).parents('tr.tr-sub-detail').find('td.jml-seharusnya').text());
            //console.log(jml_seharusnya);
            var timbangan_sak = parseInt($(this).parents('tr.tr-sub-detail').find('td.timbangan-sak').text());
            //console.log(timbangan_sak);
            //if(jml_seharusnya > timbangan_sak){
            //    $(this).parents('tr.tr-sub-detail').find('td.keterangan').text('Kurang '+(jml_seharusnya - timbangan_sak)+' sak lagi');
            //}
            //else if(jml_seharusnya < timbangan_sak){
            //    $(this).parents('tr.tr-sub-detail').find('td.keterangan').text('Cek Timbangan diluar batas toleransi');
            //}
            //else{
                $(this).parents('tr.tr-sub-detail').find('td.keterangan').text('Selesai');
            //}
        }
    });
}

function validasi_verifikasi(){
    $('#btn-verifikasi').attr('disabled',true);
    $('#nopol-terima').next().remove();
    var nopol_terima = $('#nopol-terima').val();
    var sopir = $('#sopir').val();
    if(nopol_terima.trim() && sopir.trim()){
        if(!isValidNopol(nopol_terima)){
            $('#nopol-terima').parent().append('<span class="do-not-valid">*Nopol Terima tidak valid</span>');
        }
        else{
            $('#btn-verifikasi').removeAttr('disabled');
            $('#btn-verifikasi').focus().select();

        }
    }
}



function visualisasi_kavling() {
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/layout_kavling",
            data : {
            },
            success : function(data) {
                var box = bootbox.dialog({
                    title : "Visualisasi Kavling",
                    className : "very-large",
                    message : data,
                    buttons : {
                        danger : {
                            label : "Keluar",
                            className : "btn-danger",
                            callback : function() {
                                return true;
                            }
                        },
                    }
                });

                box.bind('shown.bs.modal', function() {
                    $('.detail_selected').tooltip();

                    $('.tooltipster').tooltipster({
                        animation: 'fade',
                        delay: 200,
                        theme: 'tooltipster-default',
                        touchDevices: false,
                        trigger: 'hover',
                        contentAsHTML : true
                        //content: $(this).text() //$(this).parent().find('div.detail-barang').html()
                    });
                });

                $('.bootbox-body').find('table.tbl-layout-kavling').css('border', 'none');
                $('.bootbox-body').find('table.tbl-layout-kavling thead tr th.no-border').css('border', 'none');
                $('.bootbox-body').find('table.tbl-layout-kavling th').css('border-color', 'black');
                $('.bootbox-body').find('table.tbl-layout-kavling td').css('border-color', 'black');
            }
        });
}

function selected() {
}

function detail_selected(e) {
	/*
    //var data = $(e).next().attr('data-detail-barang');
    var data = $(e).next().html();
    var box = bootbox.dialog({
        title : "Detail Barang",
        message : data,
        buttons : {
            danger : {
                label : "Keluar",
                className : "btn-danger",
                callback : function() {
                    return true;
                }
            },
        }
    });
                $('.bootbox-body').find('table.detail-per-kavling th').css('border', '1px solid black');
                $('.bootbox-body').find('table.detail-per-kavling td').css('border', '1px solid black');
	*/
}

function tutup_otomatis() {
    var nomor_do = $('#nomor-do').val();
    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/tutup_otomatis",
        data : {
            nomor_do : nomor_do
        },
        success : function(data) {
            if(data==1){
                //toastr.success('Tutup surat jalan otomatis berhasil.', 'Berhasil');
                console.log('Berhasil tutup otomatis SJ');
            }
        }
    });
}

function generate_pakan_rusak_hilang(kode_pakan, nama_pakan, data_ke){
    $('#pakan-rusak-hilang').html('');
    var data_pakan_rusak_hilang;
    if(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan)){
        data_pakan_rusak_hilang = JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan));
    }
    if(data_pakan_rusak_hilang && data_pakan_rusak_hilang[kode_pakan]){
        var _data = data_pakan_rusak_hilang[kode_pakan];
        //console.log(_data);
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/pakan_rusak_hilang",
            data : {
                nama_pakan : nama_pakan,
                sisa : _data['jumlah'],
                kode_pakan : kode_pakan,
                data_ke : data_ke,
                pakan_rusak_hilang : _data
            },
            dataType : 'html',
            success : function(result) {
                $('#pakan-rusak-hilang').html(result);
                                $('input.berat-rusak').numeric({
                                    allowPlus           : false, // Allow the + sign
                                    allowMinus          : false,  // Allow the - sign
                                    allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                    allowDecSep         : true  // Allow the decimal separator, default is the fullstop eg 3.141
                                });
                                $('input.jumlah-sak').numeric({
                                    allowPlus           : false, // Allow the + sign
                                    allowMinus          : false,  // Allow the - sign
                                    allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                    allowDecSep         : false  // Allow the decimal separator, default is the fullstop eg 3.141
                                });



                kontrol_lampirkan_file();
            }
        });

    }
}

function view_detail_penimbangan_pakan(elm){
    var data_ke = $(elm).attr('data-ke');
    var kode_pakan = $(elm).find('td.kode-pakan span').text();
    var nama_pakan = $(elm).find('td.nama-pakan').text();
    var no_penerimaan = $('#nomor-do').attr('data-no-penerimaan');
    if($('tr.tr-detail[data-ke="'+data_ke+'"]').hasClass('hide')){
        $('tr.tr-detail[data-ke="'+data_ke+'"]').removeClass('hide');
    }
    else{
        $('tr.tr-detail[data-ke="'+data_ke+'"]').addClass('hide');
    }
    if(no_penerimaan){
        generate_pakan_rusak_hilang(kode_pakan, nama_pakan, data_ke);
    }
}

function timbangan_hanya_satu(data_ke){
    $.each($('#tbl-detail-penerimaan tbody tr.tr-header'), function(){
        var tmp_data_ke = $(this).attr('data-ke');
        var tmp_selesai = $('tr.tr-detail[data-ke="'+tmp_data_ke+'"] table.tbl-detail-pakan tbody tr.tr-detail-pakan:last td.selesai').attr('data-selesai');
        if(tmp_selesai && tmp_selesai == 0 && tmp_data_ke != data_ke){
            $('tr.tr-detail[data-ke="'+tmp_data_ke+'"]').remove();
        }
    });
}

function cek_simpan(){
    var not_yet = 0;
    $.each($('table#tbl-detail-penerimaan tbody tr.tr-header'), function (){
        var tmp_jml_sak = $(this).find('td.jumlah-sj').text();
        tmp_jml_sak = parseInt(tmp_jml_sak);
        var tmp_jml_terima = $(this).find('td.jumlah-terima').text();
        tmp_jml_terima = parseInt(tmp_jml_terima);
        var tmp_jml_tolak = $(this).find('td.jumlah-rusak').text();
        tmp_jml_tolak = parseInt(tmp_jml_tolak);
        var tmp_jml_hilang = $(this).find('td.jumlah-kurang').text();
        tmp_jml_hilang = parseInt(tmp_jml_hilang);
        //console.log(tmp_jml_sak+'='+tmp_jml_terima+'+'+tmp_jml_tolak+'+'+tmp_jml_hilang)
        if(tmp_jml_sak != (tmp_jml_terima+tmp_jml_tolak+tmp_jml_hilang)){
            //console.log(tmp_jml_sak+'='+tmp_jml_terima+'+'+tmp_jml_tolak+'+'+tmp_jml_hilang)
            not_yet++;
        }
    });
    //console.log(not_yet)
    return not_yet;
}

function detail_penimbangan_pakan(elm){
    var data_ke = $(elm).attr('data-ke');
    var kode_pakan = $(elm).find('td.kode-pakan span').text();
    var nama_pakan = $(elm).find('td.nama-pakan').text();
    var jml_sj = $(elm).find('td.jumlah-sj').text();
    var penimbangan_pakan;
    var data_pakan_rusak_hilang;
    var no_urut = 1;
    var no_pallet;
    var no_kavling=[];
    var list_pakan=[];
    var data_pakan;

    timbangan_hanya_satu(data_ke);

    $('tr.tr-detail[data-ke="'+data_ke+'"]').remove();
    if(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan)){
        data_pakan_rusak_hilang = JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan));
    }
    if(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan)){
        penimbangan_pakan = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan));
    }
    if(penimbangan_pakan){
        var tmp_no_pallet = '';
        $.each(penimbangan_pakan, function(_key0, _value0){
            $.each(_value0['detail'], function(_key1, _value1){
                if(tmp_no_pallet < _key1){
                    no_pallet = _key1;
                }
                tmp_no_pallet = _key1;
                no_kavling.push(_value1['no_kavling']);
                list_pakan.push(_key0);
            });
        });
    }
    if(penimbangan_pakan && penimbangan_pakan[kode_pakan]){
        var _html='';
        _html += '<tr data-ke="'+data_ke+'" class="tr-detail">';
        _html += '<td colspan="7">';
        _html += '<div class="div-detail-pakan">';
        _html += '<table class="table table-bordered tbl-detail-pakan">';
        _html += '<thead>';
        _html += '<tr>';
        _html += '<th class="arrow"></th>';
        _html += '<th class="no-pallet">No</th>';
        _html += '<th class="no-kavling">Kavling-Pallet</th>';
        _html += '<th class="berat-pallet">Berat Pallet (Kg)</th>';
        _html += '<th class="berat-timbang">Berat Timbang (kg)</th>';
        _html += '<th class="berat-bersih">Berat Bersih (kg)</th>';
        _html += '<th class="timbangan-sak">Timbangan (Sak)</th>';
        _html += '<th class="selesai">Keterangan</th>';
        _html += '</tr>';
        _html += '</thead>';
        _html += '<tbody>';
        var baris = 1;
        $.each(penimbangan_pakan[kode_pakan]['detail'], function(key, value){
            _html += '<tr data-ke="'+no_urut+'" class="tr-detail-pakan">';
            _html += '<td class="arrow">';
            _html += '<span class="glyphicon glyphicon glyphicon-play" style="transform: rotate(180deg);"></span>';
            _html += '</td>';
            _html += '<td class="no-pallet" data-no-pallet="'+value.no_pallet+'">'+baris+'.</td>';
            _html += '<td class="no-kavling">'+value.no_kavling+'</td>';
            _html += '<td class="berat-pallet">'+value.berat_pallet+'</td>';
            //_html += '<input type="text" onchange="get_detail_kandang(this)" class="form-control berat-pallet text-center" name="berat-pallet" placeholder="">';
            //_html += '</td>';
            _html += '<td class="berat-timbang">'+value.berat_timbang+'</td>';
            //_html += '<input type="text" onchange="get_detail_kandang(this)" class="form-control berat-timbang text-center" name="berat-timbang" placeholder="">';
            //_html += '</td>';
            _html += '<td class="berat-bersih">'+value.berat_bersih+'</td>';
            _html += '<td class="timbangan-sak">'+value.timbangan_sak+'</td>';
            //_html += '<td class="selesai" data-selesai="1">Selesai</td>';
            _html += '<td class="selesai" data-selesai="1"><button class="btn btn-default" type="button" onclick="delete_data(this)" ondblclick="not_actived(this)">Reset</button></td>';
            //_html += '<button ondblclick="not_actived(this)" onclick="konfirmasi_selesai(this)" type="button" class="btn btn-default">Selesai</button>';
            //_html += '</td>';
            _html += '</tr>';
            _html += '<tr data-ke="'+no_urut+'" class="tr-sub-detail-pakan hide"><td colspan="8">';
            _html += '<center>';
            _html += '<table class="table table-bordered tbl-detail-kandang">';
            _html += '<thead>';
            _html += '<tr>';
            _html += '<th class="checkbox-kandang"></th>';
            _html += '<th class="nama-kandang">Kandang</th>';
            _html += '<th class="jml-kebutuhan">Jml Kebutuhan (Sak)</th>';
            _html += '<th class="jml-aktual">Jml Aktual (Sak)</th>';
            _html += '<th class="berat">Berat (Kg)</th>';
            _html += '<th class="sisa">Sisa</th>';
            _html += '</tr>';
            _html += '</thead>';
            _html += '<tbody>';
            var no_urut_detail = 1;
            $.each(value['detail'],function(k, v){
                _html += '<tr data-ke="'+no_urut_detail+'" class="tr-detail-kandang">';
                _html += '<td class="checkbox-kandang">';
                _html += '<label><input type="checkbox" onclick="checkbox_kandang(this)" class="checkbox-kandang" checked="true" disabled="true"></label>';
                _html += '</td>';
                _html += '<td class="nama-kandang" data-no-reg="'+v.no_reg+'">'+v.nama_kandang+'</td>';
                _html += '<td class="jml-kebutuhan">'+v.jml_kebutuhan+'</td>';
                _html += '<td class="jml-aktual">'+v.jml_aktual+'</td>';
                //_html += '<input type="text" onchange="kontrol_jml_aktual(this)" class="form-control jml-aktual text-center"></td>';
                _html += '<td class="berat">'+v.berat+'</td>';
                _html += '<td class="sisa">'+v.sisa+'</td>';
                _html += '</tr>';
                no_urut_detail++;
            })
            _html += '</tbody>';
            _html += '</table>';
            _html += '</center>';
            _html += '</td>';
            _html += '</tr>';
            no_urut++;
            baris++;
        });
        _html += '</tbody>';
        _html += '</table>';
        _html += '</div>';
        _html += '</td>';
        _html += '</tr>';

        $(elm).after(_html);
    }

    data_pakan = kalkulasi_pakan(kode_pakan);
    $(elm).find('td.jumlah-terima').text(data_pakan.sum_terima);
    $(elm).find('td.jumlah-rusak').text(data_pakan.sum_rusak);
    $(elm).find('td.jumlah-kurang').text(data_pakan.sum_hilang);
    var n_sak = Math.abs(parseInt(jml_sj) - (parseInt(data_pakan.sum_terima)+parseInt(data_pakan.sum_rusak)+parseInt(data_pakan.sum_hilang)));
    if(n_sak > 0){
        $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').attr('data-original-title',"Terdapat selisih sejumlah "+n_sak+" sak dari jumlah sak menurut SJ.");
        $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').show();
    }
    else{
        $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').hide();
    }

    var jumlah_sj = $('tr.tr-header[data-ke="'+data_ke+'"] td.jumlah-sj').text();
    if(!jumlah_sj){
        jumlah_sj = 0;
    }
    var jumlah_terima = $('tr.tr-header[data-ke="'+data_ke+'"] td.jumlah-terima').text();
    if(!jumlah_terima){
        jumlah_terima = 0;
    }
    var tmp_sisa = parseInt(jumlah_sj) - parseInt(jumlah_terima);

    if(tmp_sisa > 0){

        if(data_pakan_rusak_hilang && data_pakan_rusak_hilang[kode_pakan]){
        }
        else{
            var data_ke_params = (no_urut == 1) ? data_ke : no_urut;
            var len = $('tr.tr-detail[data-ke="'+data_ke+'"]').length;
            var kode_flok = $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok');
            $.ajax({
                type : "POST",
                url : "penerimaan_pakan/transaksi/detail_penimbangan_pakan",
                data : {
                    data_ke : data_ke_params,
                    no_pallet : no_pallet,
                    no_kavling : no_kavling,
                    kode_pakan : kode_pakan,
                    list_kode_pakan : list_pakan,
                    kode_flok : kode_flok,
                    len : len,
                    baris : baris
                },
                dataType : "html",
                success : function(data) {
                    (len == 0) ? $(elm).after(data) : $('tr.tr-detail[data-ke="'+data_ke+'"] table.tbl-detail-pakan>tbody').append(data);
                    $('tr.tr-detail[data-ke="'+data_ke+'"] tr.tr-detail-pakan:last input.berat-pallet').focus();

                    $('input.berat-pallet').numeric({
                                    allowPlus           : false, // Allow the + sign
                                    allowMinus          : false,  // Allow the - sign
                                    allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                    allowDecSep         : true  // Allow the decimal separator, default is the fullstop eg 3.141
                                });
                                $('input.berat-timbang').numeric({
                                    allowPlus           : false, // Allow the + sign
                                    allowMinus          : false,  // Allow the - sign
                                    allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                    allowDecSep         : true  // Allow the decimal separator, default is the fullstop eg 3.141
                                });
                }
            });
        }
    }
    else{
        data_pakan = kalkulasi_pakan(kode_pakan);
        $(elm).find('td.jumlah-terima').text(data_pakan.sum_terima);
        $(elm).find('td.jumlah-rusak').text(data_pakan.sum_rusak);
        $(elm).find('td.jumlah-kurang').text(data_pakan.sum_hilang);
        var n_sak = Math.abs(parseInt(jml_sj) - (parseInt(data_pakan.sum_terima)+parseInt(data_pakan.sum_rusak)+parseInt(data_pakan.sum_hilang)));
        if(n_sak > 0){
            $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').attr('data-original-title',"Terdapat selisih sejumlah "+n_sak+" sak dari jumlah sak menurut SJ.");
            $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').show();
        }
        else{
            $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').hide();
        }
    }

    generate_pakan_rusak_hilang(kode_pakan, nama_pakan, data_ke);


}

function get_total_timbangan(data_ke_detail, data_ke_detail_pakan){
    var total_timbangan = 0;
    $.each($('tr.tr-detail[data-ke="'+data_ke_detail+'"]').find('tr.tr-detail-pakan'), function(key, value){
        var timbangan = $(this).find('td.timbangan-sak').text();
        var data_ke = $(this).attr('data-ke');
        if(data_ke != data_ke_detail_pakan){

            timbangan = parseInt(timbangan);
            total_timbangan = total_timbangan + timbangan;
        }
    });
    return total_timbangan;
}

function get_detail_kandang(elm){
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.selesai button').attr('disabled', true);
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').remove();

    var berat_pallet = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] input.berat-pallet').val();
    if(berat_pallet){

        if($(elm).hasClass('berat-pallet')){
            $(elm).removeAttr('readonly');
            $(elm).attr('disabled', true);
            $(elm).closest('tr.tr-detail-pakan').find('td.berat-timbang input.berat-timbang').focus().select();
        }

        var berat_timbang = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] input.berat-timbang').val();
        if(berat_pallet && berat_timbang){
            if(parseFloat(berat_timbang)>parseFloat(berat_pallet)){
                var berat_bersih = parseFloat(berat_timbang) - parseFloat(berat_pallet);
                var jml_sak = '';
                var jml_sj = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.jumlah-sj').text();
                jml_sj = parseFloat(jml_sj);
                cek_konversi(berat_bersih,function(data){
                    if(data){
                        jml_sak = data.JML_SAK;
                        var total_timbangan = get_total_timbangan(data_ke_detail, data_ke_detail_pakan);
                        total_timbangan = total_timbangan + parseInt(data.JML_SAK);
                        if(total_timbangan>jml_sj){
                            berat_bersih = '';
                            jml_sak = '';
                            toastr.warning('Konversi Timbangan (Sak) melebihi Jumlah SJ.', 'Informasi');
                        }
                        else{
                            berat_bersih = berat_bersih.toFixed(3);
                            if($('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').length == 0){

                                var no_kavling = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.no-kavling').text();
                                cek_maks_pallet(no_kavling, function(data_pallet){
                                    var sak_tersimpan = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.no-kavling').attr('data-sak-tersimpan');
                                    sak_tersimpan = parseInt(sak_tersimpan);
                                    jml_sak = parseInt(jml_sak);
                                    maks_pallet = parseInt(data_pallet['maks']);
                                    //console.log(jml_sak+' '+maks_pallet+' '+sak_tersimpan);
                                    if((jml_sak+sak_tersimpan)>maks_pallet){
                                        berat_bersih = '';
                                        jml_sak = '';
                                        toastr.warning('Konversi Timbangan (Sak) melebihi kuantitas maksimal pallet.', 'Informasi');
                                        


                                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text(berat_bersih);
                                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text(jml_sak);


                                        $.each($('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
                                            $(this).find('td.checkbox-kandang input.checkbox-kandang').click();
                                        });
                                    }
                                    else{
                                        var kode_pakan = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.kode-pakan span').text();
                                        var _html = generate_tabel_detail_kandang(kode_pakan, data_ke_detail_pakan);
                                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').after(_html);

                                        $('input.jml-aktual').numeric({
                                            allowPlus           : false, // Allow the + sign
                                            allowMinus          : false,  // Allow the - sign
                                            allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                            allowDecSep         : false  // Allow the decimal separator, default is the fullstop eg 3.141
                                        });

                                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.arrow span').css('transform','rotate(180deg)');


                                        /* Sementara */

                                        if($(elm).hasClass('berat-timbang')){
                                            $(elm).removeAttr('readonly');
                                            $(elm).attr('disabled', true);
                                            var konfirmasi = 0;
                                            var html = '';
                                            html += '<div class="form-horizontal">';
                                            html += '<div class="form-group">';
                                            html += '<label class="col-md-4 control-label text-right" for="">Jumlah Sak Aktual</label>';
                                            html += '<div class="col-md-5">';
                                            html += '<input type="text" placeholder="Jumlah Sak Aktual" name="jumlah_sak_aktual" id="jumlah_sak_aktual" class="form-control">';
                                            html += '</div>';
                                            html += '</div>';
                                            html += '</div>';
                                            var box = bootbox.dialog({
                                                message : html,
                                                title : "Konfirmasi",
                                                buttons : {
                                                    success : {
                                                        label : "Ya",
                                                        className : "btn-success",
                                                        callback : function() {

                                                            jml_sak = $('#jumlah_sak_aktual').val();
                                                            if(jml_sak){
                                                                konfirmasi = 1;
                                                                return true;

                                                            }
                                                            else{
                                                                toastr.warning('Jumlah Sak Aktual harus diisi.', 'Informasi');
                                                                return false;
                                                            }
                                                        }
                                                    },
                                                    danger : {
                                                        label : "Tidak",
                                                        className : "btn-danger",
                                                        callback : function() {
                                                            return true;
                                                        }
                                                    }
                                                }
                                            });


                                            box.bind('shown.bs.modal', function() {
                                                $('#jumlah_sak_aktual').val('').focus().select();

                                                $('#jumlah_sak_aktual').numeric({
                                                    allowPlus           : false, // Allow the + sign
                                                    allowMinus          : false,  // Allow the - sign
                                                    allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                                    allowDecSep         : false  // Allow the decimal separator, default is the fullstop eg 3.141
                                                });
                                            });

                                            box.bind('hidden.bs.modal', function() {
                                                if(konfirmasi == 1){
                                                    $(elm).closest('tr.tr-detail-pakan').find('td.timbangan-sak').text(jml_sak);
                                                    


                                                    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text(berat_bersih);
                                                    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text(jml_sak);


                                                    $.each($('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
                                                        $(this).find('td.checkbox-kandang input.checkbox-kandang').click();
                                                    });
                                                }
                                                else{
                                                    $(elm).closest('td.berat-timbang').find('input.berat-timbang').removeAttr('disabled');
                                                    $(elm).closest('td.berat-timbang').find('input.berat-timbang').attr('readonly', true);
                                                    $(elm).closest('td.berat-timbang').find('input.berat-timbang').select().focus();
                                                }
                                            });
                                        }

                                    }
                                });


                            }
                        }
                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text(berat_bersih);
                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text(jml_sak);


                    }
                    else{
                        berat_bersih = '';
                        toastr.warning('Cek Timbangan diluar batas toleransi.', 'Informasi');
                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text(berat_bersih);
                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text(jml_sak);

                    }

                    //$.each($('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] table.tbl-detail-kandang tbody tr.tr-detail-kandang'), function(){
                    //    $(this).find('td.checkbox-kandang input.checkbox-kandang').click();
                    //});
                });
            }
            else{
                $(elm).val('');
                toastr.warning('Berat timbang harus lebih besar dari berat pallet.', 'Informasi');
                $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-timbang input').text('');
                $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text('');
                $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text('');
                $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] input.berat-pallet').focus().select();

            }
        }
        else{
            $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-timbang input').text('');
            $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text('');
            $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text('');

        }
    }
    else{
        $(elm).val('');
        toastr.warning('Berat pallet masih kosong.', 'Informasi');
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-timbang input').text('');
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text('');
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text('');
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] input.berat-pallet').focus().select();

    }



}

function edit_berat_timbang(elm){
    var berat_timbang = $(elm).closest('tr.tr-detail-pakan').find('td.berat-timbang input.berat-timbang').val();
    if(!berat_timbang){
        $(elm).closest('td.berat-pallet').find('input.berat-pallet').removeAttr('disabled');
        $(elm).closest('td.berat-pallet').find('input.berat-pallet').attr('readonly', true);
        $(elm).closest('td.berat-pallet').find('input.berat-pallet').select().focus();
    }
}

function get_detail_kandang_old(elm){
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.selesai button').attr('disabled', true);
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').remove();

    var berat_pallet = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] input.berat-pallet').val();
    var berat_timbang = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] input.berat-timbang').val();
    if(berat_pallet && berat_timbang){
        var berat_bersih = parseFloat(berat_timbang) - parseFloat(berat_pallet);
        var jml_sak = '';
        var jml_sj = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.jumlah-sj').text();
        jml_sj = parseFloat(jml_sj);
        cek_konversi(berat_bersih,function(data){
            if(data){
                jml_sak = data.JML_SAK;
                var total_timbangan = get_total_timbangan(data_ke_detail, data_ke_detail_pakan);
                total_timbangan = total_timbangan + parseInt(data.JML_SAK);
                if(total_timbangan>jml_sj){
                    berat_bersih = '';
                    jml_sak = '';
                    toastr.warning('Konversi Timbangan (Sak) melebihi Jumlah SJ.', 'Informasi');
                }
                else{
                    berat_bersih = berat_bersih.toFixed(3);
                    if($('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').length == 0){

                        var kode_pakan = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.kode-pakan span').text();
                        var _html = generate_tabel_detail_kandang(kode_pakan, data_ke_detail_pakan);
                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').after(_html);

                        $('input.jml-aktual').numeric({
                            allowPlus           : false, // Allow the + sign
                            allowMinus          : false,  // Allow the - sign
                            allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                            allowDecSep         : false  // Allow the decimal separator, default is the fullstop eg 3.141
                        });

                        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.arrow span').css('transform','rotate(180deg)');


                    }
                }

            }
            else{
                berat_bersih = '';
                toastr.warning('Cek Timbangan diluar batas toleransi.', 'Informasi');
            }
            $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text(berat_bersih);
            $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text(jml_sak);

        });
    }
    else{
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-timbang input').text('');
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text('');
        $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text('');

    }
}

function checkbox_kandang(elm){
    var tmp_jml_aktual = '';
    var berat = '';
    var sisa = '';
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var data_ke_detail_pakan = $(elm).parents('tr.tr-sub-detail-pakan').attr('data-ke');
    var data_ke_detail_kandang = $(elm).parents('tr.tr-detail-kandang').attr('data-ke');
    if($(elm).is(':checked')){
        var timbangan = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text();
        var berat_bersih = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text();
        var jml_kebutuhan = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"] td.jml-kebutuhan').text();
        var berat_per_sak = parseFloat(berat_bersih) / parseInt(timbangan);
        timbangan = parseInt(timbangan);
        jml_kebutuhan = parseInt(jml_kebutuhan);
        $.each($(elm).parents('table.tbl-detail-kandang').find('tbody tr.tr-detail-kandang'),function(){
            var ischeckbox = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
            var tmp_data_ke = $(this).attr('data-ke');
            if(ischeckbox && (tmp_data_ke != data_ke_detail_kandang)){
                var jml_aktual = $(this).find('td.jml-aktual input.jml-aktual').val();
                timbangan = timbangan - parseInt(jml_aktual);
            }
        });
        if(timbangan>0){
            if(timbangan<=jml_kebutuhan){
                tmp_jml_aktual = timbangan;
            }
            else{
                tmp_jml_aktual = jml_kebutuhan;
            }
            berat = tmp_jml_aktual * berat_per_sak; //berat_standart;
            berat = berat.toFixed(3);
            sisa = jml_kebutuhan - tmp_jml_aktual;
        }
        else{
            toastr.warning('Jml Aktual (Sak) melebihi Timbangan (Sak).', 'Informasi');
            $(elm).attr('checked',false);
        }
    }
    $(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.jml-aktual input.jml-aktual').val(tmp_jml_aktual);
    $(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.berat').text(berat);
    $(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.sisa').text(sisa);

    validasi_selesai(elm);

}

function validasi_selesai(elm){
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var data_ke_detail_pakan = $(elm).parents('tr.tr-sub-detail-pakan').attr('data-ke');
    var sum_jml_aktual=0;
    var timbangan = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text();
    timbangan = parseInt(timbangan);
    $.each($(elm).parents('table.tbl-detail-kandang').find('tbody tr.tr-detail-kandang'),function(){
        var ischeckbox = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
        if(ischeckbox){
            var tmp_jml_aktual = $(this).find('td.jml-aktual input.jml-aktual').val();
            sum_jml_aktual = sum_jml_aktual + parseInt(tmp_jml_aktual);
        }
    });
    if(timbangan == sum_jml_aktual){
       $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.selesai button').removeAttr('disabled');
    }
    else{
       $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.selesai button').attr('disabled', true);
    }
}

function kalkulasi_sisa(elm){
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var jml_sj = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.jumlah-sj').text();
    var jml_terima = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.jumlah-terima').text();
    var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
    //var sum_sisa = 0;
    var sum_sisa = parseInt(jml_sj)-parseInt(jml_terima);
    $.each($('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] table.tbl-detail-kandang').find('tbody tr.tr-detail-kandang'),function(){
        var ischeckbox = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
        if(ischeckbox){
            //var tmp_sisa = $(this).find('td.sisa').text();
            //sum_sisa = sum_sisa + parseInt(tmp_sisa);
            var tmp_jml_aktual = $(this).find('td.jml-aktual input').val();
            //console.log(tmp_jml_aktual)
            sum_sisa = sum_sisa - parseInt(tmp_jml_aktual);
        }
    });
    return sum_sisa;

}

function kalkulasi_pakan(kode_pakan){
    var penimbangan_pakan;
    var data_pakan_rusak_hilang;
    var sum_terima = 0;
    var sum_rusak = 0;
    var sum_hilang = 0;
    var data = {};
    if(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan)){
        data_pakan_rusak_hilang = JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan));
    }
    if(data_pakan_rusak_hilang && data_pakan_rusak_hilang[kode_pakan]){
        sum_rusak = (data_pakan_rusak_hilang[kode_pakan]['detail_rusak']['jumlah']) ? data_pakan_rusak_hilang[kode_pakan]['detail_rusak']['jumlah'] : 0;
        sum_hilang = (data_pakan_rusak_hilang[kode_pakan]['detail_hilang']['jumlah']) ? data_pakan_rusak_hilang[kode_pakan]['detail_hilang']['jumlah'] : 0;
    }
    if(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan)){
        penimbangan_pakan = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan));
    }
    if(penimbangan_pakan && penimbangan_pakan[kode_pakan]){
        sum_sj = penimbangan_pakan[kode_pakan]['jml_sj'];
        $.each(penimbangan_pakan[kode_pakan]['detail'], function(key1, value1){
            sum_terima = sum_terima + parseInt(value1['timbangan_sak']);
        });
    }
    data = {
        'sum_terima' : sum_terima,
        'sum_rusak' : sum_rusak,
        'sum_hilang' : sum_hilang

    }
    return data;

}

function kontrol_jml_aktual(elm){
    var berat = '';
    var sisa = '';
    var data_ke_detail_pakan = $(elm).parents('tr.tr-sub-detail-pakan').attr('data-ke');
    var data_ke_detail_kandang = $(elm).parents('tr.tr-detail-kandang').attr('data-ke');
    var jml_aktual = $(elm).val();
    if(jml_aktual){
        var sum_jml_aktual=0;
        $.each($(elm).parents('table.tbl-detail-kandang').find('tbody tr.tr-detail-kandang'),function(){
            var ischeckbox = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
            var tmp_data_ke = $(this).attr('data-ke');
            if(ischeckbox && (tmp_data_ke != data_ke_detail_kandang)){
                var tmp_jml_aktual = $(this).find('td.jml-aktual input.jml-aktual').val();
                sum_jml_aktual = sum_jml_aktual + parseInt(tmp_jml_aktual);
            }
        });

        if($(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.checkbox-kandang input.checkbox-kandang').is(':checked')){

            jml_aktual = parseInt(jml_aktual);
            var timbangan = $('tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.timbangan-sak').text();
            var berat_bersih = $('tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-bersih').text();
            timbangan = parseInt(timbangan);
            var berat_per_sak = parseFloat(berat_bersih) / timbangan;
            sum_jml_aktual = sum_jml_aktual+jml_aktual;
            if(timbangan<sum_jml_aktual){
                toastr.warning('Jml Aktual (Sak) melebihi Timbangan (Sak).', 'Informasi');
                $(elm).val('').focus().select();
            }
            else{
                var jml_kebutuhan = $(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.jml-kebutuhan').text();
                jml_kebutuhan = parseInt(jml_kebutuhan);
                if(jml_aktual>jml_kebutuhan){
                    toastr.warning('Jml Aktual (Sak) melebihi Jml Kebutuhan (Sak).', 'Informasi');
                    $(elm).val('').focus().select();

                }
                else{
                    berat = jml_aktual * berat_per_sak; //berat_standart;
                    berat = berat.toFixed(3);
                    sisa = jml_kebutuhan - jml_aktual;
                }
            }
        }
        else{
            toastr.warning('Kandang belum dipilih.', 'Informasi');
            $(elm).val('').focus().select();
        }
    }
    $(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.berat').text(berat);
    $(elm).parents('tr.tr-detail-kandang[data-ke="'+data_ke_detail_kandang+'"]').find('td.sisa').text(sisa);

    validasi_selesai(elm);
}

function generate_detail_kandang(kode_pakan){
    var penimbangan_pakan;
    if(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan)){
        var data = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan));
        if(data && data[kode_pakan]){
            penimbangan_pakan = data[kode_pakan]['detail'];
        }
    }
    var _data = [];
    if(penimbangan_pakan){
        $.each(detail_kandang[kode_pakan], function(key, value){
            var jml_aktual = 0;
            $.each(penimbangan_pakan,function(_key0, _value0){
                $.each(_value0['detail'],function(_key1, _value1){
                    if(value['nama_kandang'] == _value1['nama_kandang']){
                        jml_aktual = jml_aktual + parseInt(_value1['jml_aktual']);
                    }
                });
            });
            var sisa = parseInt(value['jml_kebutuhan']) - jml_aktual;
            if(sisa>0){
                _data.push({
                    'jml_kebutuhan' : sisa,
                    'no_reg' : value['no_reg'],
                    'nama_kandang' : value['nama_kandang'],
                })
            }
        });

    }
    else{
        _data = detail_kandang[kode_pakan];
    }

    return _data;
}

function generate_tabel_detail_kandang(kode_pakan, data_ke){
    var data_detail_kandang = generate_detail_kandang(kode_pakan);
    var _html = '<tr class="tr-sub-detail-pakan hide" data-ke="'+data_ke+'">';
        _html += '<td colspan="8"><center>';
        _html += '<table class="table table-bordered tbl-detail-kandang">';
        _html += '<thead>';
        _html += '<tr>';
        _html += '<th class="checkbox-kandang"></th>';
        _html += '<th class="nama-kandang">Kandang</th>';
        _html += '<th class="jml-kebutuhan">Jml Kebutuhan (Sak)</th>';
        _html += '<th class="jml-aktual">Jml Aktual (Sak)</th>';
        _html += '<th class="berat">Berat (Kg)</th>';
        _html += '<th class="sisa">Sisa</th>';
        _html += '</tr>';
        _html += '</thead>';
        _html += '<tbody>';
        data_ke = 1;
        $.each(data_detail_kandang, function(key, value){
            _html += '<tr class="tr-detail-kandang" data-ke="'+data_ke+'">';
            _html += '<td class="checkbox-kandang"><label><input type="checkbox" class="checkbox-kandang" onclick="checkbox_kandang(this)"></label></td>';
            _html += '<td class="nama-kandang" data-no-reg="'+value.no_reg+'">'+value.nama_kandang+'</td>';
            _html += '<td class="jml-kebutuhan">'+value.jml_kebutuhan+'</td>';
            _html += '<td class="jml-aktual">';
            _html += '<input type="text" class="form-control jml-aktual text-center" onchange="kontrol_jml_aktual(this)">';
            _html += '</td>';
            _html += '<td class="berat"></td>';
            _html += '<td class="sisa"></td>';
            _html += '</tr>';
            data_ke++;
        })
        _html += '</tbody>';
        _html += '</table>';
        _html += '</center></td>';
        _html += '</tr>';

    return _html;
}

function selesai(elm){
    var data_ke_tr_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var kode_pakan = $('tr.tr-header[data-ke="'+data_ke_tr_detail+'"] td.kode-pakan span').text();


    var data = {};
    var nama_pakan = $('tr.tr-header[data-ke="'+data_ke_tr_detail+'"] td.nama-pakan').text();
    var bentuk_pakan = $('tr.tr-header[data-ke="'+data_ke_tr_detail+'"] td.bentuk-pakan').text();
    var jml_sj = $('tr.tr-header[data-ke="'+data_ke_tr_detail+'"] td.jumlah-sj').text();
    data[kode_pakan] = {
        'kode_pakan' : kode_pakan,
        'nama_pakan' : nama_pakan,
        'bentuk_pakan' : bentuk_pakan,
        'jml_sj' : jml_sj,
        'detail' : {},
    };


    var data_ke_tr_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
    var no_pallet = $('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] td.no-pallet').attr('data-no-pallet');
    var no_kavling = $('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] td.no-kavling').text();
    var berat_pallet = $('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] td.berat-pallet input').val();
    var berat_timbang = $('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] td.berat-timbang input').val();
    var berat_bersih = $('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] td.berat-bersih').text();
    var timbangan_sak = $('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] td.timbangan-sak').text();
    data[kode_pakan]['detail'][no_pallet] = {
        'no_pallet' : no_pallet,
        'no_kavling' : no_kavling,
        'berat_pallet' : berat_pallet,
        'berat_timbang' : berat_timbang,
        'berat_bersih' : berat_bersih,
        'timbangan_sak' : timbangan_sak,
        'detail' : {},
    };
    var data_kandang = [];
    $.each($('tr.tr-detail[data-ke="'+data_ke_tr_detail+'"] tr.tr-sub-detail-pakan[data-ke="'+data_ke_tr_detail_pakan+'"] table.tbl-detail-kandang tbody').find('tr.tr-detail-kandang'),function(){
        var ischeckbox = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
        if(ischeckbox){
            var nama_kandang = $(this).find('td.nama-kandang').text();
            var no_reg = $(this).find('td.nama-kandang').attr('data-no-reg');
            var jml_kebutuhan = $(this).find('td.jml-kebutuhan').text();
            var jml_aktual = $(this).find('td.jml-aktual input').val();
            var berat = $(this).find('td.berat').text();
            var sisa = $(this).find('td.sisa').text();
            data_kandang.push({
                'no_reg' : no_reg,
                'nama_kandang' : nama_kandang,
                'jml_kebutuhan' : jml_kebutuhan,
                'jml_aktual' : jml_aktual,
                'berat' : berat,
                'sisa' : sisa,
            });
        }
    });
    data[kode_pakan]['detail'][no_pallet]['detail'] = data_kandang;

    set_penimbangan_pakan(data, kode_pakan, no_pallet);

    var _elm = 'tr.tr-header[data-ke="'+data_ke_tr_detail+'"]';
    detail_penimbangan_pakan(_elm);
}

function konfirmasi_selesai(elm){
    var sisa = kalkulasi_sisa(elm);
    if(sisa>0){

        var msg = '<p>Sisa sak yang belum ditimbang adalah '+sisa+' sak. Apakah anda ingin melanjutkan proses penimbangan pada pallet selanjutnya?';
            msg += '<br><br>Jika anda pilih "Tidak" maka '+sisa+' sak akan dianggap bermasalah (Hilang/Rusak).</p>';
        var box = bootbox.dialog({
            message : msg,
            buttons : {
                danger : {
                    label : "Tidak",
                    className : "btn-danger",
                    callback : function() {

                        var data_ke_tr_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
                        var kode_pakan = $('tr.tr-header[data-ke="'+data_ke_tr_detail+'"] td.kode-pakan span').text();
                        var nama_pakan = $('tr.tr-header[data-ke="'+data_ke_tr_detail+'"] td.nama-pakan').text();
                        set_pakan_rusak_hilang(kode_pakan, sisa);
                        generate_pakan_rusak_hilang(kode_pakan, nama_pakan, data_ke_tr_detail);
                        return true;
                    }
                },
                success : {
                    label : "Ya",
                    className : "btn-success",
                    callback : function() {
                        return true;
                    }
                }
            }
        });

        box.bind('hidden.bs.modal', function() {
            selesai(elm);
        });
    }
    else{
        selesai(elm);
        simpan();
    }
}

function detail_penimbangan_pakan_old(elm,_data_ke){
    var sub_data_ke;
    var data_ke;
    $('tr.tr-header').removeClass('mark_row');
    if(elm){
        data_ke = $(elm).attr('data-ke');
        $(elm).addClass('mark_row');
    }
    else{
        data_ke = _data_ke;
       $('tr.tr-header[data-ke="'+_data_ke+'"]').addClass('mark_row');
    }
    var mark = 0;
    $.each($('tr.tr-detail[data-ke="'+data_ke+'"] table tbody').find('tr'),function(){
        if($(this).find('td.selesai').attr('data-selesai') == 0){
            $(this).find('td.selesai').addClass('hide');
            $(this).find('td.timbangan-kg input').attr('readonly',true);
            mark++;
            if(mark == 1){
                $(this).find('td.selesai').removeClass('hide');
                $(this).find('td.timbangan-kg input').removeAttr('readonly');
                sub_data_ke = $(this).attr('data-ke');
                //console.log(sub_data_ke);
            }
        }
    });

    if($('tr.tr-detail[data-ke="'+data_ke+'"]').hasClass('hide')){
        $('tr.tr-detail[data-ke="'+data_ke+'"]').removeClass('hide');
        //$('tr.tr-detail[data-ke="'+data_ke+'"]').find('tr.tr-sub-detail[data-ke="'+sub_data_ke+'"] td.timbangan-kg input.timbangan_kg').val('0');
        $('tr.tr-detail[data-ke="'+data_ke+'"]').find('tr.tr-sub-detail[data-ke="'+sub_data_ke+'"] td.timbangan-kg input.timbangan_kg').focus().select();
    }
    else{
        $('tr.tr-detail[data-ke="'+data_ke+'"]').addClass('hide');
    }

    $('#pakan-rusak-hilang').html('');
    //if(mark == 0){
        pakan_rusak_hilang(data_ke);
    //}
}

function pakan_rusak_hilang_old(data_ke){

    var mark = 0;
    $.each($('tr.tr-detail[data-ke="'+data_ke+'"] table tbody').find('tr'),function(){
        if($(this).find('td.selesai').attr('data-selesai') == 0){
            mark++;
        }
    });

    if(mark == 0){
        var jumlah_sj = $('tr.tr-header[data-ke="'+data_ke+'"] td.jumlah-sj').text();
        jumlah_sj = parseInt(jumlah_sj);
        var jumlah_terima = $('tr.tr-header[data-ke="'+data_ke+'"] td.jumlah-terima').text();
        jumlah_terima = parseInt(jumlah_terima);
        var jumlah_rusak = $('tr.tr-header[data-ke="'+data_ke+'"] td.jumlah-rusak').text();
        jumlah_rusak = parseInt(jumlah_rusak);
        var jumlah_kurang = $('tr.tr-header[data-ke="'+data_ke+'"] td.jumlah-kurang').text();
        jumlah_kurang = parseInt(jumlah_kurang);

        if(jumlah_sj > jumlah_terima){
            var sisa = jumlah_sj - jumlah_terima;
            var nama_pakan = $('tr.tr-header[data-ke="'+data_ke+'"] td.nama-pakan').text();
            var kode_pakan = $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan span').text();
            var nomor_do = $('#nomor-do').val();

            $.ajax({
                type : "POST",
                url : "penerimaan_pakan/transaksi/pakan_rusak_hilang",
                data : {
                    nomor_do : nomor_do,
                    nama_pakan : nama_pakan,
                    sisa : sisa,
                    kode_pakan : kode_pakan,
                    data_ke : data_ke
                },
                dataType : 'html',
                success : function(data) {
                    $('#pakan-rusak-hilang').html(data);
                }
            });
        }
    }

}

function kontrol_timbangan(elm){
    var berat = $(elm).val();
    var jumlah = $(elm).parents('tr.tr-sub-detail').find('td.jml-seharusnya').text();
    jumlah = parseInt(jumlah);
    cek_konversi(berat,function(data){
        $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text('0');
        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('-');
        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').attr('disabled',true);
        if(data){
            if(data.result == 0){
                    $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text(data.JML_SAK);

                if(jumlah < parseInt(data.JML_SAK)){
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Hasil konversi melebihi jumlah seharusnya (sak)');

                    $(elm).select();
                    $(elm).css('border-color','red');
                    $(elm).css('border-width','2px');
                    $(elm).parent().next().css('color','red');
                    $(elm).parent().next().css('font-weight','bold');

                }
                else{
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Cek Timbangan diluar batas toleransi');
                        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').removeAttr('disabled');

                        $(elm).css('border-color','#ccc');
                        $(elm).css('border-width','1px');
                        $(elm).parent().next().css('color','#000');
                        $(elm).parent().next().css('font-weight','normal');
                        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').focus().select();
                    /*
                    $(elm).select();
                        $(elm).css('border-color','red');
                        $(elm).css('border-width','2px');
                        $(elm).parent().next().css('color','red');
                        $(elm).parent().next().css('font-weight','bold');
                        */
                }
            }
            else if(data == 2){
                $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text('-');
                $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Timbangan (kg) masih kosong');
                $(elm).select();
                    $(elm).css('border-color','red');
                    $(elm).css('border-width','2px');
                    $(elm).parent().next().css('color','red');
                    $(elm).parent().next().css('font-weight','bold');
            }
            else{
                $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text(data.JML_SAK);
                $(elm).parents('tr.tr-sub-detail').find('td.selesai button').select();
                //console.log(jumlah)
                //console.log(parseInt(data.JML_SAK))
                if(jumlah > parseInt(data.JML_SAK)){
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Kurang '+(jumlah - parseInt(data.JML_SAK))+' sak lagi');
                    $(elm).parents('tr.tr-sub-detail').find('td.selesai button').removeAttr('disabled');

                    $(elm).css('border-color','#ccc');
                    $(elm).css('border-width','1px');
                    $(elm).parent().next().css('color','#000');
                    $(elm).parent().next().css('font-weight','normal');
                        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').focus().select();
                    /*
                    $(elm).css('border-color','#ccc');
                    $(elm).css('border-width','1px');
                    $(elm).parent().next().css('color','#000');
                    $(elm).parent().next().css('font-weight','normal');
                    */
                }
                else if(jumlah < parseInt(data.JML_SAK)){
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Hasil konversi melebihi jumlah seharusnya (sak)');

                    $(elm).select();
                    $(elm).css('border-color','red');
                    $(elm).css('border-width','2px');
                    $(elm).parent().next().css('color','red');
                    $(elm).parent().next().css('font-weight','bold');

                }
                else{
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Selesai');
                    $(elm).parents('tr.tr-sub-detail').find('td.selesai button').removeAttr('disabled');
                    $(elm).css('border-color','#ccc');
                    $(elm).css('border-width','1px');
                    $(elm).parent().next().css('color','#000');
                    $(elm).parent().next().css('font-weight','normal');
                        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').focus().select();
                }
            }
        }
        else{
            $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Cek Timbangan diluar batas toleransi');
                    $(elm).parents('tr.tr-sub-detail').find('td.selesai button').removeAttr('disabled');

                    $(elm).css('border-color','#ccc');
                    $(elm).css('border-width','1px');
                    $(elm).parent().next().css('color','#000');
                    $(elm).parent().next().css('font-weight','normal');
                        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').focus().select();
            /*
                    $(elm).css('border-color','red');
                    $(elm).css('border-width','2px');
                    $(elm).parent().next().css('color','red');
                    $(elm).parent().next().css('font-weight','bold');
                    */
        }
    });
}

function not_actived(elm){
    return false;
}

function selesai_old(elm){
    $(elm).attr('disabled', true);
    var data_ke = $(elm).parents('tr.tr-detail').attr('data-ke');
    var sub_data_ke = $(elm).parents('tr.tr-sub-detail').attr('data-ke');
    var _params = [];
    var all_kode_barang = '';
    var all_jumlah = '';
    $.each($('#tbl-detail-penerimaan tbody').find('tr.tr-header'), function(key, value) {
        if (key == 0) {
            all_kode_barang += "" + $(this).find('td.kode-pakan span').text() + "";
            all_jumlah += $(this).find('td.jumlah-sj').text();
        } else {
            all_kode_barang += "," + $(this).find('td.kode-pakan span').text() + "";
            all_jumlah += "," + $(this).find('td.jumlah-sj').text();
        }
    })
    _params.push({
        'no_sj' : $('#nomor-sj').text(),
        'tanggal_sj' : $('#tanggal-sj').text(),
        'kuantitas_kg' : $('#kuantitas-kg').text(),
        'kuantitas_zak' : $('#kuantitas-zak').text(),
        'tanggal_verifikasi_do' : $('#tanggal-verifikasi-do').text(),
        'no_op' : $('#nomor-op').text(),
        'no_do' : $('#nomor-do').val(),
        'nama_ekspedisi' : $('#nama-ekspedisi').text(),
        'no_kendaraan_kirim' : $('#nopol-kirim').text(),
        'no_kendaraan_terima' : $('#nopol-terima').val(),
        'nama_sopir' : $('#sopir').val(),
        'kode_barang' : $(elm).parents('tr.tr-detail').prev().find('td.kode-pakan span').text(),
        'berat_aktual' : $(elm).parents('tr.tr-sub-detail').find('td.timbangan-kg input').val(),
        'jumlah_aktual' : $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text(),
        'all_kode_barang' : all_kode_barang,
        'all_jumlah' : all_jumlah,
        'no_reg' : $(elm).parents('tr.tr-sub-detail').find('td.kandang').attr('data-no-reg'),
        'jenis_kelamin' : $(elm).parents('tr.tr-sub-detail').find('td.kandang').attr('data-jenis-kelamin'),
    });
    //console.log(_params);
    konfirmasi_selesai(_params, function(_result) {
        if (_result.result == 1) {
            $('#nomor-penerimaan').text(_result.no_penerimaan);
            //console.log(_result.no_penerimaan);
            verifikasi(data_ke,sub_data_ke);
            tutup_otomatis();
        }
    });

}

function konfirmasi_selesai_old(data, callback) {
    if (data.length > 0) {
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/konfirmasi_selesai",
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

function get_data_surat_jalan(no_op, callback) {
    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/get_data_surat_jalan",
        data : {
            no_op : no_op
        },
        dataType : 'json',
        success : function(data) {
            callback(data);
        }
    });
}

function cek_konversi(berat,callback){
    if (!empty(berat)) {

        berat = parseFloat(berat);
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/cek_konversi",
            data : {
                berat : berat
            },
            dataType : 'json',
            success : function(data) {
                callback(data);
            }
        });
    }
    else{
        callback(2);
    }
}

function cek_maks_pallet(no_kavling,callback){
    if (!empty(no_kavling)) {
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/cek_maks_pallet",
            data : {
                no_kavling : no_kavling
            },
            dataType : 'json',
            success : function(data) {
                callback(data);
            }
        });
    }
}

function tambah_timbang_rusak(elm){
    var kode_pakan = $('#kode-pakan-rusak').text();
    var sisa = $('table.tabel_input_rusak').attr('data-sisa');
    //var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
    //jumlah_hilang = parseInt(jumlah_hilang) - 1;

    var nomor = 0;
    $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
        var berat_rusak = parseFloat($(this).find('input.berat-rusak').val());
        if(!berat_rusak){
            nomor++;
        }
    });

    //if((nomor)<=jumlah_hilang){
        //if (jumlah_hilang > 0){
            var data_ke_timbang = parseInt($(elm).parents('tr.row-timbang').attr('data-ke'));
            $(elm).parents('tr.row-timbang').clone().appendTo('table.tabel_input_rusak tbody');
            $(elm).parents('tr.row-timbang').next().attr('data-ke',(data_ke_timbang+1));
            $(elm).parents('tr.row-timbang').next().find('input.berat-rusak').attr('readonly',false);
            $(elm).parents('tr.row-timbang').next().find('input.keterangan-rusak').attr('readonly',false);
            $(elm).parents('tr.row-timbang').next().find('input.berat-rusak').val('');
            $(elm).parents('tr.row-timbang').next().find('input.keterangan-rusak').val('');
            $(elm).parents('tr.row-timbang').next().find('input.berat-rusak').select();

            nomor = 1;
            $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
                $(this).find('td:first-child').text(nomor+'.');
                nomor++;
            });
            //$(elm).find('span').removeClass('glyphicon-plus');
            //$(elm).find('span').addClass('glyphicon-minus');
            //$(elm).attr('onclick','hapus_timbang_rusak(this)');
            $(elm).addClass('hide');


                                $('input.berat-rusak').numeric({
                                    allowPlus           : false, // Allow the + sign
                                    allowMinus          : false,  // Allow the - sign
                                    allowThouSep        : false,  // Allow the thousands separator, default is the comma eg 12,000
                                    allowDecSep         : true  // Allow the decimal separator, default is the fullstop eg 3.141
                                });
       // }
    //}
    //else{
    //    toastr.error('Jumlah maksimal barang (rusak+hilang) '+kode_pakan+' adalah '+(sisa) +' sak.','Peringatan');
    //}

    kontrol_lampirkan_file();
}

function hapus_timbang_rusak(elm){
    var row_count = $('table.tabel_input_rusak tbody tr').length;
    if(row_count>1){
        var berat_rusak = $(elm).parents('tr.row-timbang').find('input.berat-rusak').val();
        $(elm).parents('tr.row-timbang').remove();
        var nomor = 1;
        $.each($('table.tabel_input_rusak tbody').find('tr'),function(){
            $(this).find('td:first-child').text(nomor+'.');
            nomor++;
        });
        if(berat_rusak && parseFloat(berat_rusak)>0){

            //var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
            //jumlah_hilang = parseInt(jumlah_hilang) - 1;

            //$('.panel-pakan-hilang input.jumlah-sak').val(jumlah_hilang);
        }
        $('table.tabel_input_rusak tbody tr:last td').find('div.div-plus').removeClass('hide');
        $('table.tabel_input_rusak tbody tr:last td').find('div.div-plus').removeClass('hide');
    }

    kontrol_lampirkan_file();
}

function kontrol_berat_rusak(elm){
    var berat_rusak = parseFloat($(elm).val());
    if(berat_rusak && berat_rusak > 0){
        $(elm).attr('readonly',true);

        //var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
        //jumlah_hilang = parseInt(jumlah_hilang) - 1;

        //$('.panel-pakan-hilang input.jumlah-sak').val(jumlah_hilang);
    }

    kontrol_lampirkan_file();
}

function kontrol_keterangan_rusak(elm){
    var berat_rusak = $(elm).parents('tr.row-timbang').find('td input.berat-rusak').val();
    if(berat_rusak){
        var keterangan_rusak = $(elm).val();
        keterangan_rusak = keterangan_rusak.replace(' ','');
        if(keterangan_rusak){
            $(elm).attr('readonly',true);

            //var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
            //jumlah_hilang = parseInt(jumlah_hilang) - 1;

            //$('.panel-pakan-hilang input.jumlah-sak').val(jumlah_hilang);
        }

    }
    else{
        $(elm).val('');
        toastr.warning('Berat harus diisi.', 'Informasi');
    }


    kontrol_lampirkan_file();
}

function kontrol_lampirkan_file(){
    var count = 0;
    $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
            var berat_rusak = parseFloat($(this).find('input.berat-rusak').val());
            var keterangan = $(this).find('input.keterangan-rusak').val();
      if(berat_rusak && keterangan){
        count++;
      }
    })

    var lampirkan_file = $('#lampirkan-foto').val();

    if(count == 0){
        //$('#panel-lampirkan-foto').addClass('hide');
        $('#file-upload').attr('disabled', true);
    }
    else{
        //$('#panel-lampirkan-foto').removeClass('hide');
        if(lampirkan_file){
            $('#file-upload').attr('disabled', true);

        } else{
        $('#file-upload').removeAttr('disabled');

        }
    }
}

function simpan_pakan_rusak_hilang(elm){

    $(elm).attr('disabled', true);
    var validasi = 0;

    var data_ke = $(elm).attr('data-ke');
    var kode_pakan = $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan span').text();
    $('.div-panel-pakan-rusak').html('');
    $('.div-lampirkan-foto').html('');

    var jumlah_sisa = $('table.tabel_input_rusak').attr('data-sisa');
    jumlah_sisa = parseInt(jumlah_sisa);

    var jumlah_rusak = 0;
    var jumlah_keterangan = 0;

    var detail_pakan_rusak = [];

    $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
        var berat_rusak = parseFloat($(this).find('input.berat-rusak').val());
        var keterangan = $(this).find('input.keterangan-rusak').val();
        keterangan = keterangan.replace(' ','');
        if(berat_rusak){
            jumlah_rusak++;
            if(keterangan){
                jumlah_keterangan++;
                detail_pakan_rusak.push({
                    'berat' : berat_rusak,
                    'keterangan' : keterangan
                });
            }
        }
    });
    jumlah_rusak = parseInt(jumlah_rusak);

    var detail_pakan_hilang = {};
    var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
    var keterangan_hilang = $('.panel-pakan-hilang input.keterangan').val();
    keterangan_hilang = keterangan_hilang.replace(' ','');
    if(!jumlah_hilang){
        jumlah_hilang = 0;
    }
    else{
        detail_pakan_hilang = {
            'jumlah' : jumlah_hilang,
            'keterangan' : keterangan_hilang
        };
    }
    jumlah_hilang = parseInt(jumlah_hilang);

    var lampirkan_file = $('#lampirkan-foto').val();
    var attachment = '';

    if((jumlah_rusak != jumlah_keterangan) || ((keterangan_hilang && jumlah_hilang==0) || (!keterangan_hilang && jumlah_hilang>0))){
        validasi++;
        $('.div-panel-pakan-rusak').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
        $(elm).removeAttr('disabled');
    }

    if(jumlah_rusak > 0 && !lampirkan_file){
        validasi++;
        $('.div-lampirkan-foto').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
        $(elm).removeAttr('disabled');
    }

    if(jumlah_sisa < (jumlah_rusak+jumlah_hilang)){
        validasi++;
        $(elm).removeAttr('disabled');
    }

    if(lampirkan_file){
        var split_lampirkan_file = lampirkan_file.split('.');
        var format = split_lampirkan_file[split_lampirkan_file.length - 1];
        format = format.toLowerCase();
        if ($.inArray(format, array_format) < 0) {
            validasi++;
            $('.div-lampirkan-foto').html('<span class="do-not-valid">*Format file harus .doc atau .docx</span>');
            $(elm).removeAttr('disabled');
        }
        else{
            attachment = $('#file-upload').get(0).files[0];
            //console.log(data_file);
        }

    }

    var n_sak = Math.abs(jumlah_sisa - (jumlah_rusak+jumlah_hilang));
    $('tr.tr-header').removeAttr('tabindex');
    if(n_sak == 0){
        $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').hide();
    }
    else{
        validasi++;
        $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').attr('data-original-title',"Terdapat selisih sejumlah "+n_sak+" sak dari jumlah sak menurut SJ.");

        $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').show();
        $('tr.tr-header[data-ke="'+data_ke+'"]').attr('tabindex', 0);
        $('tr.tr-header[data-ke="'+data_ke+'"]').focus();
    }

    //console.log(validasi);

    if(validasi == 0 && n_sak ==0){
        set_detail_pakan_rusak_hilang(kode_pakan,detail_pakan_rusak,detail_pakan_hilang, lampirkan_file);
        if(jumlah_rusak > 0){
            simpan_attachment(kode_pakan,attachment);

        }
        simpan();
    }

}

function simpan_attachment(kode_pakan, attachment){
    var _params = data_sj();
    var formData = new FormData();
        formData.append("attachment", attachment);
        formData.append("kode_pakan", kode_pakan);
        formData.append("nomor_do", _params['do_params']);
    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/simpan_attachment",
        data : formData,
        cache   : false,
        contentType : false,
        processData : false,
        dataType : 'json',
        async : false,
        success : function(_data) {
            //console.log(_data);
        }

    });
}

function simpan_pakan_rusak_hilang_old(elm){
    $(elm).attr('disabled', true);
    var validasi = 0;

    var data_ke = $(elm).attr('data-ke');

    $('.div-panel-pakan-rusak').html('');
    $('.div-lampirkan-foto').html('');
    var jumlah_sisa = $('table.tabel_input_rusak').attr('data-sisa');
    jumlah_sisa = parseInt(jumlah_sisa);

    var jumlah_rusak = 0;
    var jumlah_keterangan = 0;
    $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
        var berat_rusak = parseFloat($(this).find('input.berat-rusak').val());
        var keterangan = $(this).find('input.keterangan-rusak').val();
        if(berat_rusak){
            jumlah_rusak++;
            if(!keterangan){
                jumlah_keterangan++;
            }
        }
    });
    jumlah_rusak = parseInt(jumlah_rusak);

    var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
    var keterangan_hilang = $('.panel-pakan-hilang input.keterangan').val();
    if(!jumlah_hilang){
        jumlah_hilang = 0;
    }
    jumlah_hilang = parseInt(jumlah_hilang);

    var lampirkan_file = $('#lampirkan-foto').val();

    if((jumlah_sisa > (jumlah_rusak+jumlah_hilang)) || jumlah_keterangan){
        validasi++;
        $('.div-panel-pakan-rusak').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
        $(elm).removeAttr('disabled');
    }
    else{

        if(jumlah_hilang > 0){
            if(!keterangan_hilang){
                validasi++;
                $('.div-panel-pakan-rusak').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
                $(elm).removeAttr('disabled');
            }
        }

    }

    if(jumlah_rusak > 0 && empty(lampirkan_file)){
        validasi++;
        $('.div-lampirkan-foto').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
        $(elm).removeAttr('disabled');
    }

    if(jumlah_sisa < (jumlah_rusak+jumlah_hilang)){
        validasi++;
        $(elm).removeAttr('disabled');
        //$('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan').prepend('<span class="do-not-valid seru" style="padding-right : 10%;">!</span>');
    }



    var n_sak = Math.abs(jumlah_sisa - (jumlah_rusak+jumlah_hilang));
    $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').attr('data-original-title',"Terdapat selisih sejumlah "+n_sak+" sak dari jumlah sak menurut SJ");


    if(validasi == 0){

        var no_penerimaan = $('#nomor-penerimaan').text();
        var no_sj = $('#nomor-sj').text();
        var kode_barang_rusak = $('#kode-pakan-rusak').text();
        var kode_barang_kurang = $('#kode-pakan-rusak').text();
        var berat = 0;
        var jumlah = 0;


    var data = {};
    var data_rusak = [];
    var data_kurang = [];
        var obj = [];

    $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
        var berat_rusak = parseFloat($(this).find('input.berat-rusak').val());
        if(berat_rusak){
        var keterangan_rusak = $(this).find('input.keterangan-rusak').val();
            var jumlah_rusak = 1;
                obj.push({
                    'no_penerimaan' : no_penerimaan,
                    'no_sj' : no_sj,
                    'kode_barang' : kode_barang_rusak,
                    'jumlah' : jumlah_rusak,
                    'berat' : berat_rusak,
                    'keterangan' : keterangan_rusak
                });

                jumlah++;

                berat = parseFloat(berat) + parseFloat(berat_rusak);
        }

    });
            var attachment = $('#file-upload').get(0).files[0];
            var attachment_name = lampirkan_file;
            if(berat>0){
            data_rusak.push({
                'no_penerimaan' : no_penerimaan,
                'no_sj' : no_sj,
                'kode_barang' : kode_barang_rusak,
                'jumlah' : jumlah,
                'berat' : berat,
                'keterangan' : '',
                'tipe_ba' : 'R',
                'detail_rusak' : obj
            });
        }


        if(jumlah_hilang > 0 ){
                data_kurang.push({
                    'no_penerimaan' : no_penerimaan,
                    'no_sj' : no_sj,
                    'kode_barang' : kode_barang_kurang,
                    'jumlah' : jumlah_hilang,
                    'berat' : 0,
                    'keterangan' : keterangan_hilang,
                    'tipe_ba' : 'K'
                });
            }

        data.data_rusak = data_rusak;
        data.data_kurang = data_kurang;

                    var formData = new FormData();
                    formData.append("attachment", attachment);
                    formData.append("attachment_name", attachment_name);
                    formData.append("data", JSON.stringify(data));

    $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').remove();
    $('#format-file').html('');

                    $.ajax({
                        type : "POST",
                        url : "penerimaan_pakan/transaksi/simpan_konfirmasi_rk",
                        data : formData,
                        cache   : false,
                        contentType : false,
                        processData : false,
                        dataType : 'json',
                        async : false,
                        success : function(_data) {
                            if(_data.result == 1 && _data.format_not_valid == 0){
                                verifikasi(data_ke, 1);
                                tutup_otomatis();
                                messageBox('','Proses simpan berhasil');


                            }
                            else if(_data.result == 1 && _data.format_not_valid >= 1){
                                $('#format-file').html('<span class="do-not-valid">*Format file harus .doc atau .docx</span>');
                            }
                            else{
                                messageBox('','Proses simpan gagal');
                            }
                        }
                    });
    }

}

function verifikasi(){
    $('#penimbangan-pakan').html('');
    var nomor_do = $('#nomor-do').val();
    var no_penerimaan = $('#nomor-do').attr('data-no-penerimaan');
    $('#nopol-terima').attr('readonly', true);
    $('#sopir').attr('readonly', true);
    $('#btn-verifikasi').attr('disabled', true);
    set_nopol_sopir();
    var _params = data_sj();
    if(_params['do_params'].length > 0){
        $.ajax({
            type : "POST",
            url : "penerimaan_pakan/transaksi/penimbangan_pakan",
            data : {
                nomor_do : _params['do_params'],
                no_penerimaan : no_penerimaan
            },
            dataType : 'html',
            success : function(data) {
                $('#penimbangan-pakan').html(data);
                if(!no_penerimaan){
                    $.each($('#tbl-detail-penerimaan tbody tr.tr-header'),function(){
                        var tmp_data_ke = $(this).attr('data-ke');
                        var kode_pakan = $(this).find('td.kode-pakan span').text();
                        var jml_sj = $(this).find('td.jumlah-sj').text();
                        var data_pakan = kalkulasi_pakan(kode_pakan);
                        $(this).find('td.jumlah-terima').text(data_pakan.sum_terima);
                        $(this).find('td.jumlah-rusak').text(data_pakan.sum_rusak);
                        $(this).find('td.jumlah-kurang').text(data_pakan.sum_hilang);
                        var n_sak = Math.abs(parseInt(jml_sj) - (parseInt(data_pakan.sum_terima)+parseInt(data_pakan.sum_rusak)+parseInt(data_pakan.sum_hilang)));
                        if(n_sak > 0){
                            $('tr.tr-header[data-ke="'+tmp_data_ke+'"] td.kode-pakan a.seru').attr('data-original-title',"Terdapat selisih sejumlah "+n_sak+" sak dari jumlah sak menurut SJ.");

                            $('tr.tr-header[data-ke="'+tmp_data_ke+'"] td.kode-pakan a.seru').show();
                        }
                        else{
                            $('tr.tr-header[data-ke="'+tmp_data_ke+'"] td.kode-pakan a.seru').hide();
                        }
                    });
                }
                else{
                    susun_data_pakan_rusak_hilang();
                }
                /*
                pakan_rusak_hilang(data_ke);
                set_keterangan();
                $('#nomor-do').focus();
                if(data_ke){
                    //$('#tbl-detail-penerimaan').find('tr.tr-detail[data-ke="'+data_ke+'"]').removeClass('hide');

                    detail_penimbangan_pakan(null,data_ke);
                    //if(sub_data_ke){
                        //$('#tbl-detail-penerimaan').find('tr.tr-detail[data-ke="'+data_ke+'"] table.tbl-sub-detail-penerimaan tbody tr.tr-sub-detail[data-ke="'+sub_data_ke+'"] td.timbangan-kg input.timbangan_kg').focus();
                    //}
                }
                //tutup_otomatis();
                */
            }
        });
    }
}


function messageBox(title,message){
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

    box.bind('shown.bs.modal', function() {
        $('div.bootbox button.btn-success').focus().select();
    });
}

function set_daftar_do_dan_sj(data, no_do){
    var alldata = {};
    localStorage.removeItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan);
    if(!localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan)){
        alldata[no_do] = data;
        localStorage.setItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
    else{
        var _data = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
        alldata = _data;
        if(!_data[no_do]){
            alldata[no_do] = data;
        }

        localStorage.setItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
    //console.log(JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan)));
}

function set_nopol_sopir(){

    var nopol_terima = $('#nopol-terima').val();
    var sopir = $('#sopir').val();
    var nopol_kirim = $('#nopol-kirim').text();
    var tanggal_terima = $('#tanggal-terima').attr('data-tanggal-terima');
    var alldata = {
        'nopol_terima' : nopol_terima,
        'sopir' : sopir,
        'nopol_kirim' : nopol_kirim,
        'tanggal_terima' : tanggal_terima
    };
    if(!localStorage.getItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan)){
        localStorage.setItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
}

function set_detail_pakan_rusak_hilang(kode_pakan,detail_pakan_rusak,detail_pakan_hilang, lampirkan_file){
    var alldata = {};
    var data = JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan));
    alldata = data;
    if(data && data[kode_pakan]){
        alldata[kode_pakan]['detail_rusak']['data'] = detail_pakan_rusak;
        alldata[kode_pakan]['detail_hilang']['data'] = detail_pakan_hilang;
        alldata[kode_pakan]['detail_rusak']['jumlah'] = detail_pakan_rusak.length;
        alldata[kode_pakan]['detail_rusak']['nama_file'] = lampirkan_file;
        alldata[kode_pakan]['detail_hilang']['jumlah'] = detail_pakan_hilang['jumlah'];
    }
    localStorage.setItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    //console.log(JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan)));
}

function set_pakan_rusak_hilang(kode_pakan, jumlah){
    var alldata = {};
    var data = {
        'jumlah' : jumlah,
        'detail_rusak' : {
            'jumlah' : '',
            'data' : {},
            'nama_file' : ''
        },
        'detail_hilang' : {
            'jumlah' : '',
            'data' : {}
        }
    }
    if(!localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan)){
        alldata[kode_pakan] = data;
        localStorage.setItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
    else{
        var _data = JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan));
        alldata = _data;
        if(!_data[kode_pakan]){
            alldata[kode_pakan] = data;
        }
        else{
            alldata[kode_pakan] = data;
        }

        localStorage.setItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
}

function set_penimbangan_pakan(data, kode_pakan, no_pallet){
    //console.log(data);
    var alldata = {};
    if(!localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan)){
        alldata = data;
        localStorage.setItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
    else{
        var _data = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan));
        alldata = _data;
        if(!_data[kode_pakan]){
            alldata[kode_pakan] = data[kode_pakan];
        }
        else{
            alldata[kode_pakan]['detail'][no_pallet] = data[kode_pakan]['detail'][no_pallet];
        }

        localStorage.setItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan,JSON.stringify(alldata));
    }
    //console.log(JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan)));
}

function set_ganti_kavling(elm, data_ke_detail, data_ke_detail_pakan){
    var data_no_kavling = $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.no-kavling').attr('data-no-kavling');
    var no_kavling = (!elm) ? data_no_kavling : $(elm).find('td.gk_no_kavling').text();
    var berat = (!elm) ? '' : $(elm).find('td.gk_berat').text();
    var sak = (!elm) ? '0' : $(elm).find('td.gk_sak').text();
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.no-kavling span').text(no_kavling);
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.no-kavling').attr('data-sak-tersimpan', sak);
    $('tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"] td.berat-pallet input').val(berat);
    $('.bootbox button.btn-success').click();
    get_detail_kandang('input.berat-pallet');
}

function ganti_kavling(elm){
    var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var kode_flok = $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok');
    var kode_barang = $('tr.tr-header[data-ke="'+data_ke_detail+'"] td.kode-pakan span').text();
    var data_kavling = get_data_kavling(kode_barang);
    $.ajax({
        type : 'POST',
        dataType : 'html',
        url : "penerimaan_pakan/transaksi/ganti_kavling",
        data : {
            kode_flok : kode_flok,
            kode_barang : kode_barang,
            data_kavling : data_kavling,
            data_ke_detail_pakan : data_ke_detail_pakan,
            data_ke_detail : data_ke_detail
        }
    }).done(function(data) {
        messageBox('Pilih Kavling', data);
    });
}

function get_data_kavling(kode_barang){

    var array_kavling = {};
    var penimbangan_pakan;
    if(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan)){
        penimbangan_pakan = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan));
    }
    if(penimbangan_pakan && penimbangan_pakan[kode_barang]){
        var berat = 0;
        var sak = 0;
        var tmp_no_pallet = '';
        $.each(penimbangan_pakan, function(key, value){
            if(kode_barang == key){
                $.each(value['detail'], function(key1, value1){
                    var no_pallet = value1['no_pallet'];
                    if(tmp_no_pallet < no_pallet){
                        var no_kavling = value1['no_kavling'];
                        var berat_pallet = value1['berat_pallet'];
                        var berat_timbang = value1['berat_bersih'];
                        var sak = value1['timbangan_sak'];
                        berat = parseFloat(berat_pallet)+parseFloat(berat_timbang);
                        if(array_kavling[no_kavling]){
                            array_kavling[no_kavling]['berat'] = berat;
                            array_kavling[no_kavling]['timbangan_sak'] = parseInt(array_kavling[no_kavling]['timbangan_sak']) + parseInt(sak);
                        }
                        else{
                            array_kavling[no_kavling] = {
                                'no_kavling' : no_kavling,
                                'berat' : berat,
                                'timbangan_sak' : sak
                            }
                        }
                    }
                    tmp_no_pallet = no_pallet;
                });
            }
        });
    }
    //console.log(array_kavling);

    return array_kavling;
}

function build_save_params(){
    var _data_daftar_do_dan_sj = JSON.parse(localStorage.getItem('daftar_do_dan_sj_'+kode_farm+'_'+global_no_penerimaan));
    var _data_nopol_sopir = JSON.parse(localStorage.getItem('nopol_sopir_'+kode_farm+'_'+global_no_penerimaan));
    var _data_penimbangan_pakan = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_'+global_no_penerimaan));
    var _data_pakan_rusak_hilang = JSON.parse(localStorage.getItem('pakan_rusak_hilang_'+kode_farm+'_'+global_no_penerimaan));
    var _data_sj = data_sj();
    var _penerimaan = {
        'sopir' : _data_nopol_sopir['sopir'],
        'nama_ekspedisi' : $('#table-daftar-do-sj table tbody tr:first').attr('data-nama-ekspedisi'),
        'no_kendaraan_kirim' : _data_nopol_sopir['nopol_kirim'],
        'no_kendaraan_terima' : _data_nopol_sopir['nopol_terima'],
        'no_spm' : _data_sj['spm_params'],
        'tanggal_terima' : _data_nopol_sopir['tanggal_terima'],
        'no_do' : _data_sj['do_params'],
        'no_sj' : _data_sj['sj_params']
    }

    var _penerimaan_d = [];
    var _penerimaan_e = [];
    var _movement = [];
    var _movement_d = {};
    var _berita_acara = {
        'no_sj' :  _data_sj['sj_params'],
        'no_do' :  _data_sj['do_params']
    };
    var _berita_acara_d = [];
    $.each(_data_penimbangan_pakan, function(key0, value0){
        var sak_terima = 0;
        var berat_terima = 0;
        var berat_rusak = 0;
        var berat_bersih = 0;
        var berat_pallet = 0;
        var sak_rusak = (_data_pakan_rusak_hilang && _data_pakan_rusak_hilang[value0['kode_pakan']] && _data_pakan_rusak_hilang[value0['kode_pakan']]['detail_rusak']['jumlah']) ? parseInt(_data_pakan_rusak_hilang[value0['kode_pakan']]['detail_rusak']['jumlah']) : 0;
        var sak_hilang = (_data_pakan_rusak_hilang && _data_pakan_rusak_hilang[value0['kode_pakan']] && _data_pakan_rusak_hilang[value0['kode_pakan']]['detail_hilang']['jumlah']) ? parseInt(_data_pakan_rusak_hilang[value0['kode_pakan']]['detail_hilang']['jumlah']) : 0;
        $.each(value0['detail'], function(key1, value1){
            sak_terima = sak_terima + parseInt(value1['timbangan_sak']);
            berat_bersih = parseFloat(value1['berat_bersih']);
            berat_terima = berat_terima+berat_bersih;
            berat_pallet = parseFloat(value1['berat_pallet']);
            _penerimaan_e.push({
                'no_pallet' : value1['no_pallet'],
                'kode_barang' : value0['kode_pakan'],
                'jumlah' : parseInt(value1['timbangan_sak']),
                'berat' : berat_bersih,
                'status_stok' : 'NM',
                'no_kavling' : value1['no_kavling'],
                'kode_flok' : $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok'),
                'berat_pallet' : berat_pallet,
                'no_reg' : '',
                'keterangan_rusak' : ''
            });
            /*
            _movement.push({
                'no_kavling' : value1['no_kavling'],
                'no_pallet' : value1['no_pallet'],
                'kode_barang' : value0['kode_pakan'],
                'jml_on_putaway' : parseInt(value1['timbangan_sak']),
                'berat_on_putaway' : berat_bersih,
                'kode_flok' : $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok'),
                'berat_pallet' : berat_pallet,
                'status_stok' : 'NM'
            });
            */
            _movement_d[value1['no_pallet']]  = [];
            $.each(value1['detail'], function(key2, value2){
                var obj = {
                    'no_kavling' : value1['no_kavling'],
                    'no_pallet' : value1['no_pallet'],
                    'kode_barang' : value0['kode_pakan'],
                    'jumlah' : parseInt(value2['jml_aktual']),
                    'berat' : parseFloat(value2['berat']),
                    'no_reg' : value2['no_reg'],
                    'status_stok' : 'NM',
                    'keterangan_rusak' : ''

                }
                _movement_d[value1['no_pallet']].push(obj);
            });
        });
        var keterangan_kurang = (sak_hilang > 0) ? _data_pakan_rusak_hilang[value0['kode_pakan']]['detail_hilang']['data']['keterangan'] : '';
        var nama_file = (sak_rusak > 0) ? _data_pakan_rusak_hilang[value0['kode_pakan']]['detail_rusak']['nama_file'] : '';
        _penerimaan_d.push({
            'kode_barang' : value0['kode_pakan'],
            'jml_sj' : value0['jml_sj'],
            'jml_terima' : sak_terima,
            'berat_terima' : berat_terima,
            'jml_rusak' : sak_rusak,
            'jml_kurang' : sak_hilang,
            'keterangan_kurang' : keterangan_kurang
        });
        if(sak_rusak > 0 || sak_hilang > 0){
            _berita_acara_d.push({
                'kode_barang' : value0['kode_pakan'],
                'jml_rusak' : sak_rusak,
                'jml_kurang' : sak_hilang,
                'keterangan_kurang' : keterangan_kurang,
                'nama_file' : nama_file
            });
        }
        var berat_rusak = 0;
        if(sak_rusak > 0){
            _movement_d[no_kavling_pakan_rusak]  = [];
            $.each(_data_pakan_rusak_hilang, function(key3, value3){
                $.each(value3['detail_rusak']['data'], function(key4, value4){
                    berat_rusak = berat_rusak + parseFloat(value4['berat']);
                    /*
                    _movement.push({
                        'no_kavling' : no_kavling_pakan_rusak,
                        'no_pallet' : '',
                        'kode_barang' : key3,
                        'jml_on_putaway' : 1,
                        'berat_on_putaway' : parseFloat(value4['berat']),
                        'status_stok' : 'DM'
                    });
                    */
                    var obj = {
                        'no_kavling' : no_kavling_pakan_rusak,
                        'no_pallet' : 'DMG',
                        'kode_barang' : key3,
                        'jumlah' : 1,
                        'berat' : parseFloat(value4['berat']),
                        'status_stok' : 'DM',
                        'keterangan_rusak' : value4['keterangan'],
                        'no_reg' : ''
                    };
                    _movement_d[no_kavling_pakan_rusak].push(obj);
                });
            });
        }
        _penerimaan_d[(_penerimaan_d.length - 1)]['berat_rusak'] = berat_rusak;
    });

    if(_movement_d && _movement_d[no_kavling_pakan_rusak]){

                $.each(_movement_d[no_kavling_pakan_rusak], function(key5, value5){
                    _penerimaan_e.push({
                        'no_pallet' : '',
                        'kode_barang' : value5['kode_barang'],
                        'jumlah' : 1,
                        'berat' : parseFloat(value5['berat']),
                        'status_stok' : 'DM',
                        'no_kavling' : no_kavling_pakan_rusak,
                        'kode_flok' : $('div#table-daftar-do-sj table tbody tr:first').attr('data-kode-flok'),
                        'berat_pallet' : 0,
                        'keterangan_rusak' : value5['keterangan_rusak'],
                        'no_reg' : ''
                    });
                });
    }


    var data = {
        'penerimaan' : _penerimaan,
        'penerimaan_d' : _penerimaan_d,
        'penerimaan_e' : _penerimaan_e,
        'movement' : _movement,
        'movement_d' : _movement_d,
        'berita_acara' : _berita_acara,
        'berita_acara_d' : _berita_acara_d
    };


    //console.log(data);

    return data;
}

function simpan(){
    $.each($('#tbl-detail-penerimaan tbody tr.tr-header'),function(){
        var tmp_data_ke = $(this).attr('data-ke');
        var kode_pakan = $(this).find('td.kode-pakan span').text();
        var jml_sj = $(this).find('td.jumlah-sj').text();
        var data_pakan = kalkulasi_pakan(kode_pakan);
        $(this).find('td.jumlah-terima').text(data_pakan.sum_terima);
        $(this).find('td.jumlah-rusak').text(data_pakan.sum_rusak);
        $(this).find('td.jumlah-kurang').text(data_pakan.sum_hilang);
    });
    var status_simpan = cek_simpan();
    var data = build_save_params();
    if(status_simpan == 0){
        $.ajax({
            type : 'POST',
            dataType : 'json',
            url : "penerimaan_pakan/transaksi/simpan_penerimaan",
            data : {
                data : data
            }
        }).done(function(data) {
            if(data.result == 1){
                localStorage.removeItem('daftar_do_dan_sj_'+kode_farm+'_0');
                localStorage.removeItem('nopol_sopir_'+kode_farm+'_0');
                localStorage.removeItem('penimbangan_pakan_'+kode_farm+'_0');
                localStorage.removeItem('pakan_rusak_hilang_'+kode_farm+'_0');
                localStorage.removeItem('data_kavling_'+kode_farm+'_0');
                messageBox('','Proses simpan berhasil');

                set_all_view(data.no_do);
            }
            else{
                //$('#btn-simpan').removeAttr('disabled');
                messageBox('','Proses simpan gagal');
            }
        });
    }
    else{
        messageBox('','Proses simpan berhasil');
    }
}

function reset(){

                localStorage.removeItem('daftar_do_dan_sj_'+kode_farm+'_0');
                localStorage.removeItem('nopol_sopir_'+kode_farm+'_0');
                localStorage.removeItem('penimbangan_pakan_'+kode_farm+'_0');
                localStorage.removeItem('pakan_rusak_hilang_'+kode_farm+'_0');
                localStorage.removeItem('data_kavling_'+kode_farm+'_0');

                baru('.btn-baru',0);
}


function delete_data(elm){
    var data_ke_detail = $(elm).parents('tr.tr-detail').attr('data-ke');
    var data_ke_detail_pakan = $(elm).parents('tr.tr-detail-pakan').attr('data-ke');
    var data_penimbangan = JSON.parse(localStorage.getItem('penimbangan_pakan_'+kode_farm+'_0'));

    var kode_pakan = $('#tbl-detail-penerimaan tbody tr.tr-header[data-ke="'+data_ke_detail+'"]').find('td.kode-pakan span').text(); //'1126-10-11';
    var no_pallet = $('#tbl-detail-penerimaan tbody tr.tr-detail[data-ke="'+data_ke_detail+'"] tr.tr-detail-pakan[data-ke="'+data_ke_detail_pakan+'"]').find('td.no-pallet').attr('data-no-pallet'); //'SYS00000007';

    delete data_penimbangan[kode_pakan]['detail'][no_pallet];
    localStorage.removeItem('penimbangan_pakan_'+kode_farm+'_0');

    localStorage.setItem('penimbangan_pakan_'+kode_farm+'_0',JSON.stringify(data_penimbangan));

    var _elm = 'tr.tr-header[data-ke="'+data_ke_detail+'"]';
    detail_penimbangan_pakan(_elm);

    var reset_all = 0;
    $.each(data_penimbangan, function(key, value){
        $.each(value['detail'], function(k, v){
            if(k){
                reset_all++;
            }
        });
    });

    if(reset_all == 0){
        reset();
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
