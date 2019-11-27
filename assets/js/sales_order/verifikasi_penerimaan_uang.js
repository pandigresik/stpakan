'use strict';

var VerifikasiPU = {
    checkField:function(elm) {
        var _tr = $(elm).closest('tr');
        var nominal = $(_tr).find('input[name=nominal_bayar]').val();
        var file = $(_tr).find('#file-upload').attr('data-base64');

        console.log(file);
        if(nominal != '' && file != ''){
            _tr.find('.verifikasi').attr('disabled',false);
        }else {
            _tr.find('.verifikasi').attr('disabled',true);
        }

    },
    selectFile:function(elm) {
        var _closest = $(elm).closest('.attachment');
        var _tr = $(elm).closest('tr');
        var file = $(elm).get(0).files[0];

        $(elm).attr('data-base64', '');

        _closest.find('i').removeClass('hide');
        _closest.find('i').attr('title',$(elm).val());



        file.convertToBase64(function (base64) {
            $(elm).attr('data-base64', base64);
            VerifikasiPU.checkField(elm);
         // if (tipe == 1) {
         //  if (base64) {
         //   _data.base64 = base64;
         //   Supplier.showPreviewFileDDS(file, _data);
         //  }
         // }
        });
    },
    previewFile: function (elm) {
        var div = $(elm).closest('div.attachment');
        var base64 = div.find('#file-upload').attr('data-base64');
        // console.log(base64);
        // var src = iframe.attr('src');
        window.open(base64, '_blank', 'location=yes,status=yes');
    },
    check_button:function(elm) {
        var tbody = $('#headerTable');
        var tr = elm.closest('tr');
        var hrg_length = $(tbody).find('.verifikasi:checked').length;

        console.log(hrg_length);
        $('.btn').removeAttr('disabled');

        if(hrg_length > 0){
            $('.btn').each(function () {
                this.disabled = false;
            });
        }else{
            $('.btn').each(function () {
                this.disabled = true;
            });
        }
    },

    verifikasi:function(elm) {
        var _checked  = $('#headerTable').find('.verifikasi:checked');
        var success = 0;
        bootbox.confirm({
            message: 'Apakah anda yakin menyimpan transaksi ini ?',
            buttons: {
                'cancel': {
                    label: 'Tidak',
                    className: 'btn-default'
                },
                'confirm': {
                    label: 'Ya',
                    className: 'btn-primary'
                }
            },
            callback: function(result) {
                if(result){
                    $(_checked).map(function (i, v) {
                        var data = {};
                        var obj = [];
                        var formData = new FormData();
                        var tr = v.closest('tr');
                        var attachment = ($(v).data('status') == 'U') ? $(tr).find('#file-upload').get(0).files[0] : null;
                        formData.append("fileToUpload[]", attachment);
                        obj.push({
                            'kode_pembayaran': $(tr).data('so'),
                            'no_so'          : $(tr).data('so'),
                            'nominal_harga'  : $(tr).data('harga'),
                            'nominal_bayar'  : $(tr).find('input[name=nominal_bayar]').val(),
                            'status_order'   : $(v).data('status'),
                        });
                        formData.append("data", JSON.stringify(obj));
                        $.ajax({
                            type : "POST",
                            url : "sales_order/verifikasi_penerimaan_uang/verifikasiSO",
                            data : formData,
                            cache   : false,
                            contentType : false,
                            processData : false,
                            dataType : 'json',
                            async : false,
                            success : function(_data) {
                                if(_data.status != 1){
                                    // verifikasi_do();
                                    bootbox.alert('Proses simpan gagal',function(){
                                        VerifikasiPU.refreshPage();
                                    });
                                    return false;
                                }else {
                                    success++;
                                }
                            }
                        });
                        console.log(tr);
                    });
                    if(success > 0){
                        bootbox.alert('Proses simpan berhasil',function(){
                            VerifikasiPU.refreshPage();
                        });
                    }
                }
            }
        });

        // return false;
        // var promises = $(_checked).map(function (i, v) {
        //     var tr = v.closest('tr');
        //     var attachment = ($(v).data('status') == 'U') ? $(tr).find('#file-upload').get(0).files[0] : null;
        //     // var attachment_name = $(tr).find('#file-upload').val();
        //     formData.append("fileToUpload[]", attachment);
        //     obj.push({
        //         'kode_pembayaran': $(tr).data('so'),
        //         'no_so'          : $(tr).data('so'),
        //         'nominal_harga'  : $(tr).data('harga'),
        //         'nominal_bayar'  : $(tr).find('input[name=nominal_bayar]').val(),
        //         'status_order'   : $(v).data('status'),
        //     });
        //     // console.log(tr);
        // });
        // promises.promise().done(function(results) {
        //     formData.append("data", JSON.stringify(obj));
        //     bootbox.confirm({
        //         message: 'Apakah anda yakin menyimpan transaksi ini ?',
        //         buttons: {
        //             'cancel': {
        //                 label: 'Tidak',
        //                 className: 'btn-default'
        //             },
        //             'confirm': {
        //                 label: 'Ya',
        //                 className: 'btn-primary'
        //             }
        //         },
        //         callback: function(result) {
        //             if(result){
        //                 $.ajax({
        //                     type : "POST",
        //                     url : "sales_order/verifikasi_penerimaan_uang/verifikasiSO",
        //                     data : formData,
        //                     cache   : false,
        //                     contentType : false,
        //                     processData : false,
        //                     dataType : 'json',
        //                     async : false,
        //                     success : function(_data) {
        //                         if(_data.status == 1){
        //                             // verifikasi_do();
        //                             bootbox.alert('Proses simpan berhasil',function(){
        //                                 VerifikasiPU.refreshPage();
        //                             });
        //                         }
        //                         else{
        //                             bootbox.alert('Proses simpan gagal',function(){
        //                                 VerifikasiPU.refreshPage();
        //                             });
        //                         }
        //                     }
        //                 });
        //             }
        //         }
        //     });
        // });
        // var promises = $(_checked).map(function (i, v) {
        //     var tr = v.closest('tr');
        //     // var budgetData = [];
        //     // if(_nextStatus == 'RJ'){
        //     //     _keterangan = ''
        //     // }
        //     // _data.push({
        //     //     'kodeSiklus'      : $(tr).data('kodesiklus'),
        //     //     'harga_d'         : budgetData,
        //     //     'keterangan'      : _keterangan,
        //     // });
        //     var attachment = $(tr).find('#file-upload').get(0).files[0];
        //     var attachment_name = $(tr).find('#file-upload').val();
        //     var formData = new FormData();
        //     formData.append("fileToUpload[]", attachment);
        //     // console.log(attachment);
        //     // console.log(attachment_name);
        // });
        //
        //
        // promises.promise().done(function(results) {
        //     // formData.append("attachment_name", attachment_name);
        //     // formData.append("data", JSON.stringify(data));
        //     console.log(attachment);
        //     // $.ajax({
        //     //     type : "POST",
        //     //     url : "sales_order/verifikasi_penerimaan_uang/simpanVerifikasi",
        //     //     data : formData,
        //     //     cache   : false,
        //     //     contentType : false,
        //     //     processData : false,
        //     //     dataType : 'json',
        //     //     async : false,
        //     //     success : function(_data) {
        //     //         if(_data.result == 1){
        //     //             // verifikasi_do();
        //     //             messageBox('','Proses simpan berhasil');
        //     //         }
        //     //         else{
        //     //             messageBox('','Proses simpan gagal');
        //     //         }
        //     //     }
        //     // });
        // });




        // $.each($('#headerTable tbody').find('tr.row-timbang'),function(){
        //     var berat_rusak = parseFloat($(this).find('input.berat-rusak').val());
        //     if(berat_rusak){
        //         var keterangan_rusak = $(this).find('input.keterangan-rusak').val();
        //         var jumlah_rusak = 1;
        //         obj.push({
        //             'no_penerimaan' : no_penerimaan,
        //             'no_sj' : no_sj,
        //             'kode_barang' : kode_barang_rusak,
        //             'jumlah' : jumlah_rusak,
        //             'berat' : berat_rusak,
        //             'keterangan' : keterangan_rusak
        //         });
        //         jumlah++;
        //         berat = parseFloat(berat) + parseFloat(berat_rusak);
        //     }
        // });
        // var attachment = $('#file-upload').get(0).files[0];
        // var attachment_name = lampirkan_file;
        //
        //
        // data['data_rusak'] = data_rusak;
        // data['data_kurang'] = data_kurang;
        //
        // var formData = new FormData();
        // formData.append("attachment", attachment);
        // formData.append("attachment_name", attachment_name);
        // formData.append("data", JSON.stringify(data));
        //
        //
        // $('tr.tr-header[data-ke="'+data_ke+'"] td.kode-pakan a.seru').remove();
        //

    },
    refreshPage:function() {
        $('#main_content').load('sales_order/verifikasi_penerimaan_uang');
    },
    checkedList:function(elm) {
        // console.log($(elm).is(':checked'));
        // console.log($('input[name=startDate]'));
        if($(elm).is(':checked') == true){
            $('input[name=startDate]').attr('disabled',true);
            $('input[name=endDate]').attr('disabled',true);
            $('input[name=startDate]').val('');
            $('input[name=endDate]').val('');
            VerifikasiPU.loadData();
        }else {
            $('input[name=startDate]').attr('disabled',false);
            $('input[name=endDate]').attr('disabled',false);
        }
    },
    loadData:function(elm) {
        var startDate = $('input[name=startDate]').val();
        var endDate   = $('input[name=endDate]').val();
        $.ajax({
            url : 'sales_order/verifikasi_penerimaan_uang/loadPage',
            data : {
               //no_ppsk : _no_ppsk, kodefarm : _kodefarm
               startDate : (startDate != '') ? Config._tanggalDb(startDate,' ','-') : '',
               endDate   : (endDate != '') ? Config._tanggalDb(endDate,' ','-') : '',
               isAjax   : true
            },
            dataType : 'html',
            type:'POST',
            beforeSend : function(){
                $('#div_list_sales_order').html('Loading ......');
            },
            success : function(data){
                var hasil = $('#div_list_sales_order').html(data);
                // $('.btn').removeAttr('disabled');
            }
        });
    }
};

(function(){
	'use strict';
	$("input[name=startDate]").datepicker({
			    //  defaultDate: "+1w",
	      dateFormat : 'dd M yy',
	      onClose: function( selectedDate ) {
	        $( "input[name=endDate]" ).datepicker( "option", "minDate", selectedDate );
	      }
	});
	$("input[name=endDate]").datepicker({
	//  defaultDate: "+1w",
	      dateFormat : 'dd M yy',
	      onClose: function( selectedDate ) {
	        $( "input[name=startDate]" ).datepicker( "option", "maxDate", selectedDate );
	    }
	});

}())
