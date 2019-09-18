( function() {
    'use strict';

    $('div').on('click', 'a.btn', function(e) {        
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })

     $("#tanggal-kirim-awal").datepicker({
            //  defaultDate: "+1w",
              dateFormat : 'dd M yy',
              onClose: function( selectedDate ) {
                $( "#tanggal-kirim-akhir" ).datepicker( "option", "minDate", selectedDate );
              }
           });  
         $("#tanggal-kirim-akhir").datepicker({
            //  defaultDate: "+1w",
              dateFormat : 'dd M yy',
              onClose: function( selectedDate ) {
                $( "#tanggal-kirim-awal" ).datepicker( "option", "maxDate", selectedDate );
            }
          });

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

}())

var search = false;
var page_number = 0;
var total_page = null;

getReport(page_number);

function check_dblclick(){
    var nomor_do = $('#nomor-do').val();
    if(nomor_do){
        verifikasi_do();
    }
}


function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function kontrol_chekbox(elm){
    if($(elm).is(':checked')){
        $(elm).val('1');
        $('#tanggal-kirim-awal').val('');
        $('#tanggal-kirim-akhir').val('');
    }
    else{
        $(elm).val('0');
    }
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
    tanggal_kirim_awal = (tanggal_kirim_awal) ? Config._tanggalDb(tanggal_kirim_awal,' ','-') : '';
    var tanggal_kirim_akhir = $('#tanggal-kirim-akhir').val();
    tanggal_kirim_akhir = (tanggal_kirim_akhir) ? Config._tanggalDb(tanggal_kirim_akhir,' ','-') : '';
    var no_op = $('input.filter[name="no_op"]').val();
    var no_do = $('input.filter[name="no_do"]').val();
    var no_sj = $('input.filter[name="no_sj"]').val();
    var nama_ekspedisi = $('input.filter[name="nama_ekspedisi"]').val();
    var tanggal_kirim = $('input.filter[name="tanggal_kirim"]').val();
    if(tanggal_kirim){
        tanggal_kirim = Config._tanggalDb(tanggal_kirim,' ','-');
    }

    if(do_belum_diterima == 0 && (!tanggal_kirim_awal || !tanggal_kirim_akhir)){
        toastr.error('Parameter input harus lengkap.','Peringatan');
    }
    else{

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
                        var tmp_jam_terima = (data.jam_terima) ? data.jam_terima : '';
                        $("#daftar-do-table table tbody").append('<tr class="'+pink+'"><td>' + data.no_op + '</td><td>' + data.no_do + '</td><td>' + data.no_sj + '</td><td>' + data.nama_ekspedisi + '</td><td>' + _tgl_kirim + '</td><td>' + _tgl_terima + '</td><td>' + tmp_jam_terima.substring(0,5) + '</td><td>' + data.penerima + '</td>' + td_ba + '</tr>');

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

function verifikasi_do() {
    reset_detail_do();
    var nomor_do = $('#nomor-do').val();
    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/verifikasi_do",
        data : {
            nomor_do : nomor_do
        },
        dataType : 'json',
        success : function(data) {
            if(data == 0){
                $('#nomor-do').parent().append('<span class="do-not-valid">*No. DO tidak valid</span>');
            }
            else{
                $('div.detail-do label').removeClass('grey-label');
                if(data.nopol_terima){
                    $('#nopol-terima').val(data.nopol_terima);
                }
                else{
                    $('#nopol-terima').removeAttr('readonly');
                }
                if(data.sopir){
                    $('#sopir').val(data.sopir);
                }
                else{
                    $('#sopir').removeAttr('readonly');
                }

                $('#div-tanggal-terima').removeClass('hide');
                $('#nomor-sj').text(data.no_sj);
                $('#nomor-op').text(data.no_op);
                $('#target-tanggal-kirim').text(Config._tanggalLocal(Config._getDateStr(new Date(data.tanggal_kirim)),'-',' '));
                $('#tanggal-terima').text(Config._tanggalLocal(Config._getDateStr(new Date(data.tanggal_terima)),'-',' '));
                $('#nama-ekspedisi').text(data.nama_ekspedisi);
                $('#nopol-kirim').text(data.nopol_kirim);

                if(data.validasi_tanggal_kirim == 0){
                    messageBox('Peringatan','Penerimaan terlambat, melebihi target tanggal kirim ('+Config._tanggalLocal(Config._getDateStr(new Date(data.tanggal_kirim)),'-',' ')+') harap melakukan koordinasi lebih lanjut.');
                }

                if(data.no_penerimaan){
                    $('#nomor-penerimaan').text(data.no_penerimaan);
                    $('#btn-verifikasi').hide();
                    verifikasi();
                }
            }
        }
    });
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
    var nopol_terima = $('#nopol-terima').val();
    var sopir = $('#sopir').val();
    if(nopol_terima.trim() && sopir.trim()){
        $('#btn-verifikasi').removeAttr('disabled');
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
                });

                $('.bootbox-body').find('table:first-child').css('border', 'none');
                $('.bootbox-body').find('table:first-child thead tr th.no-border').css('border', 'none');
                $('.bootbox-body').find('table.tbl-layout-kavling th').css('border-color', 'black');
                $('.bootbox-body').find('table.tbl-layout-kavling td').css('border-color', 'black');
            }
        });
}

function selected() {
}

function detail_selected(e) {
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
            	toastr.success('Tutup surat jalan otomatis berhasil.', 'Berhasil');
            }
        }
    });
}

function detail_penimbangan_pakan(elm){
    var sub_data_ke;
    var data_ke = $(elm).attr('data-ke');
    $('tr.tr-header').removeClass('mark_row');
    $(elm).addClass('mark_row');
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
            }
        }
    });

    if($('tr.tr-detail[data-ke="'+data_ke+'"]').hasClass('hide')){
        $('tr.tr-detail[data-ke="'+data_ke+'"]').removeClass('hide');
        $('tr.tr-detail[data-ke="'+data_ke+'"]').find('tr.tr-sub-detail[data-ke="'+sub_data_ke+'"] td.timbangan-kg input.timbangan_kg').focus();
    }
    else{
        $('tr.tr-detail[data-ke="'+data_ke+'"]').addClass('hide');
    }
    
    $('#pakan-rusak-hilang').html('');
    //if(mark == 0){
    	pakan_rusak_hilang(data_ke);
    //}
}

function pakan_rusak_hilang(data_ke){

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
    berat = parseFloat(berat);
    var jumlah = $(elm).parents('tr.tr-sub-detail').find('td.jml-seharusnya').text();
    jumlah = parseInt(jumlah);
    cek_konversi(berat,function(data){
        $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text('-');
        $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('-');
        $(elm).parents('tr.tr-sub-detail').find('td.selesai button').attr('disabled',true);
        if(data){
            if(data.result == 0){
                $(elm).parents('tr.tr-sub-detail').find('td.timbangan-sak').text(data.JML_SAK);
                $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Cek Timbangan diluar batas toleransi');
                $(elm).select();
	                $(elm).css('border-color','red');
	                $(elm).css('border-width','2px');
	                $(elm).parent().next().css('color','red');
	                $(elm).parent().next().css('font-weight','bold');
            }
            else if(data == 2){
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
                if(jumlah > data.JML_SAK){
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Kurang '+(jumlah - parseInt(data.JML_SAK))+' sak lagi');
                    $(elm).parents('tr.tr-sub-detail').find('td.selesai button').removeAttr('disabled');
	                $(elm).css('border-color','#ccc');
	                $(elm).css('border-width','1px');
	                $(elm).parent().next().css('color','#000');
	                $(elm).parent().next().css('font-weight','normal');
                }
                else if(jumlah < data.JML_SAK){
                    $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Cek Timbangan diluar batas toleransi');
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
                }
            }
        }
        else{
            $(elm).parents('tr.tr-sub-detail').find('td.keterangan').text('Cek Timbangan diluar batas toleransi');
            $(elm).select();
	                $(elm).css('border-color','red');
	                $(elm).css('border-width','2px');
	                $(elm).parent().next().css('color','red');
	                $(elm).parent().next().css('font-weight','bold');
        }
    });
}

function selesai(elm){
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
        }
    });
    
}

function konfirmasi_selesai(data, callback) {
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

function cek_konversi(berat,callback){
    if (berat && berat > 0 && berat != '') {
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
            $(elm).parents('tr.row-timbang').next().find('input.berat-rusak').val('');
            $(elm).parents('tr.row-timbang').next().find('input.keterangan-rusak').val('');
            $(elm).parents('tr.row-timbang').next().find('input.berat-rusak').select();

            nomor = 1;
            $.each($('table.tabel_input_rusak tbody').find('tr.row-timbang'),function(){
                $(this).find('td:first-child').text(nomor+'.');
                nomor++;
            });
            $(elm).find('span').removeClass('glyphicon-plus');
            $(elm).find('span').addClass('glyphicon-minus');
            $(elm).attr('onclick','hapus_timbang_rusak(this)');
       // }
    //}
    //else{
    //    toastr.error('Jumlah maksimal barang (rusak+hilang) '+kode_pakan+' adalah '+(sisa) +' sak.','Peringatan');
    //}
}

function hapus_timbang_rusak(elm){
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
}

function kontrol_berat_rusak(elm){
    var berat_rusak = parseFloat($(elm).val());
    if(berat_rusak && berat_rusak > 0){
        $(elm).attr('readonly',true);

        //var jumlah_hilang = $('.panel-pakan-hilang input.jumlah-sak').val();
        //jumlah_hilang = parseInt(jumlah_hilang) - 1;

        //$('.panel-pakan-hilang input.jumlah-sak').val(jumlah_hilang);
    }
}

function simpan_pakan_rusak_hilang(elm){
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
    }
    else{

        if(jumlah_hilang > 0){
            if(!keterangan_hilang){
                validasi++;
                $('.div-panel-pakan-rusak').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
            }
        }

    }

    if(jumlah_rusak > 0 && empty(lampirkan_file)){
        validasi++;
        $('.div-lampirkan-foto').html('<span class="do-not-valid">*Mohon dilengkapi</span>');
    }

    if(jumlah_sisa < (jumlah_rusak+jumlah_hilang)){
        validasi++;
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

        data['data_rusak'] = data_rusak;
        data['data_kurang'] = data_kurang;

                    var formData = new FormData();
                    formData.append("attachment", attachment);
                    formData.append("attachment_name", attachment_name);
                    formData.append("data", JSON.stringify(data));

    $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').remove();
    
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
                            if(_data.result == 1){
                                verifikasi(data_ke, 1);
                                messageBox('','Proses simpan berhasil');
                                
            
                            }
                            else{
                                messageBox('','Proses simpan gagal');
                            }
                        }
                    });
    }

}

function verifikasi(data_ke, sub_data_ke){
    $('#penimbangan-pakan').html('');
    var nomor_do = $('#nomor-do').val();
    $('#nopol-terima').attr('readonly', true);
    $('#sopir').attr('readonly', true);
    $('#btn-verifikasi').hide();
    $.ajax({
        type : "POST",
        url : "penerimaan_pakan/transaksi/penimbangan_pakan",
        data : {
            nomor_do : nomor_do
        },
        dataType : 'html',
        success : function(data) {
            $('#penimbangan-pakan').html(data);
            pakan_rusak_hilang(data_ke);
            set_keterangan();
            $('#nomor-do').focus();
            if(data_ke){
                $('#tbl-detail-penerimaan').find('tr.tr-detail[data-ke="'+data_ke+'"]').removeClass('hide');
                //if(sub_data_ke){
                    $('#tbl-detail-penerimaan').find('tr.tr-detail[data-ke="'+data_ke+'"] table.tbl-sub-detail-penerimaan tbody tr.tr-sub-detail[data-ke="'+sub_data_ke+'"] td.timbangan-kg input.timbangan_kg').focus();
                //}
            }
            tutup_otomatis();
        }
    });
}


function messageBox(title,message){
    bootbox.dialog({
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
}