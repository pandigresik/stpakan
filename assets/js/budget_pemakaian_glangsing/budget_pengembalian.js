function refresh_table() {
   var table = $('#tb_status_periode').dataTable({
      lengthChange:false,
      info:false,
      paging:false,
      searching:false,
      bDestroy: true,
      //processing: true,
      serverSide: true,
      ajax:{
         url:"budget_pengembalian_glangsing/main/read_periode",
         type:"POST",
         data:{
            //coba:"oke"
            nama_farm : $('#q_nm_farm').val(),
            siklus : $('#q_siklus').val(),
            status : $('#q_status').val(),
         }
      },
      fnCreatedRow: function(nRow, nData, iDataIndex){
         $(nRow).click(function() {
            alert('tes');
            $.ajax({
               url: 'budget_pengembalian_glangsing/main/cek_status_siklus',
               dataType: 'json',
               data:{
                  status_siklus:nData[7],
                  kode_siklus:nData[1],
               },
               type:'POST',
               success:function(response){
                  if(response.field == 'write'){
                     input_attr = false;
                  }else{
                     input_attr = true;
                  }

                  if(response.message == true){
                     notificationBox("Budget pemakaian glangsing untuk siklus "+response.periode_siklus+" harus dilakukan penutupan terlebih dahulu.");
                  }
                  if(nData[7] == null){
                     $('#fm')[0].reset();
                  }


                  $("#eksternal_budget").find('input').attr('readonly',input_attr);
                  $("#internal_budget").find('input').attr('readonly',input_attr);
                  $("#td_total_internal").find('input').attr('readonly',true);
                  $("#td_total_eksternal").find('input').attr('readonly',true);

                  $("#kd_siklus").val(nData[1]);
                  $("#tgl_buat").val(nData[8]);
                  $("#kode_farm").val(nData[2]);

                  $('#save_budget').css('display',response.simpan);
                  $('#release_budget').css('display',response.rilis);
                  $('#close_budget').css('display',response.tutup_bugdet);
                  $('#review_budget').css('display',response.review);
                  $('#approve_budget').css('display',response.approve);
                  $('#reject_budget').css('display',response.reject);
                  //alert($('#save_budget')+'.'+response.simpan+'()');

                  $.ajax({
                     url: 'budget_pengembalian_glangsing/main/get_budget_data',
                     dataType: 'json',
                     data:{
                        kode_siklus : nData[1],
                        nama_farm	: $('#q_nm_farm').val(),
                        status : nData[7]
                     },
                     type:'POST',
                     success:function(response){
                        // var internal = response.internal;
                        // var eksternal = response.eksternal;
                        // var total_internal = 0;
                        // var total_eksternal = 0
                        //
                        // if(Object.keys(internal).length > 0){
                        //    $.each(internal, function(index, val){
                        //       $('#'+val.id_field+'').val(val.value);
                        //       total_internal += parseInt(val.value);
                        //    });
                        //    $('#total_internal').val(total_internal);
                        // }
                        //
                        // if(Object.keys(eksternal).length > 0){
                        //    $.each(eksternal, function(index, val){
                        //       $('#'+val.id_field+'').val(val.value);
                        //       total_eksternal += parseInt(val.value);
                        //    });
                        //    $('#total_eksternal').val(total_eksternal);
                        // }

                     }
                  });
               }
            });
         })
      },
      aoColumns: [
         { "sClass": "text-center",
           "mData":[9]
         },{ "sClass": "text-center",
           "mData":[3]
         },
         { "sClass": "text-center",
           "mData":[6]
           /*"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
              $(nTd).click(function() {
               alert('asdasdasdasd');
              })
            }  */
         },
      ]

   });
}
function save_budget(action){
   $("#action").val(action);
   switch(action){
      case 'D':
         var data = "Apakah Anda yakin akan Menyimpan data Budget Pemakaian Glangsing ini?";
         break;
      case 'N':
         var data = "Apakah Anda yakin akan Menyimpan data Budget Pemakaian Glangsing ini?";
         break;
      case 'C':
         var data = "Apakah Anda yakin akan Menyimpan data Budget Pemakaian Glangsing ini?";
         break;
      case 'R':
         var data = "Apakah anda yakin melakukan Approval untuk data Budget Pemakaian Glangsing ini?";
         break;
      case 'A':
         var data = "Apakah anda yakin melakukan Approval untuk data Budget Pemakaian Glangsing ini?";
         break;
      case 'RJ':
         var data = "Apakah anda yakin melakukan Reject untuk data Budget Pemakaian Glangsing ini?";
         break;

   }
   var box = bootbox.confirm({
      message : data,
      buttons : {
         confirm : {
            label : "Ya",
            className : "btn-primary",
         },
         cancel : {
            label : "Tidak",
            className : "btn-default",
         },
      },
      callback: function (result) {
           if(result == true){
            if(action == 'RJ'){
                var box2 = bootbox.prompt({
                    title: "Keterangan",
                    inputType: 'textarea',
                     buttons: {
                       confirm: {
                           label: 'Simpan',
                           className: 'btn-primary btn-keterangan-reject',
                       },
                       cancel: {
                           label: 'Batal',
                           className: 'btn-default'
                       }
                   },
                    callback: function (result) {
                     if(result){
                        $('#keterangan').val(result);
                           $("#fm").submit();
                     }
                    }
                });

               box2.bind('shown.bs.modal', function() {
                  $('.btn-keterangan-reject').prop('disabled',true);
                  $('.bootbox-form textarea').on('keyup',function() {
                     if($(this).val().length >= 10){
                        $('.btn-keterangan-reject').prop('disabled',false);
                     }
                     else{
                        $('.btn-keterangan-reject').prop('disabled',true);
                     }
                  });
               });
            }
            else{
               $("#fm").submit();
            }
           }
       }
   });

}

$("#fm").submit(function(){
   $.ajax({
      url: 'budget_pengembalian_glangsing/main/save_budget',
      dataType: 'json',
      type:'POST',
      data:$("#fm").serialize(),
      success:function(response){
         if (response.success == true) {
            $('#fm')[0].reset();
            $('#save_budget').css('display','none');
            $('#release_budget').css('display','none');
            $('#close_budget').css('display','none');
            $('#review_budget').css('display','none');
            $('#approve_budget').css('display','none');
            $('#reject_budget').css('display','none');

            $("#eksternal_budget").find('input').attr('readonly',true);
            $("#internal_budget").find('input').attr('readonly',true);
            $("#td_total_internal").find('input').attr('readonly',true);
            $("#td_total_eksternal").find('input').attr('readonly',true);

            notificationBox(response.message);
            refresh_table();
         }else{
            notificationBox(response.message);
         }
      }
   });
   return false;
});


function hitung_internal(data){
   var t_internal = $('#t_internal').val();
   var total = 0;

   if(parseInt(t_internal) > 0){
      for(a = 0;a < t_internal;a++){
         total += parseInt($('#tf_internal'+a).val());
      }
   }
   $('#total_internal').val(total);
}
function notificationBox(message){
   bootbox.dialog({
      message : message,
      buttons : {
         success : {
            label : "OK",
            className : "btn-primary",
            callback : function() {
               return true;
            }
         }
      }
   });
}
function hitung_eksternal(data){
   var t_eksternal = $('#t_eksternal').val();
   var total = 0;

   if(parseInt(t_eksternal) > 0){
      for(a = 0;a < t_eksternal;a++){
         total += parseInt($('#tf_eksternal'+a).val());
      }
   }

   $('#total_eksternal').val(total);
}
