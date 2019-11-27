var paginate = null;
var vdata_list_report = null;
var KontrolStokGlangsing = {
  start_up: function() {
    KontrolStokGlangsing.set_table_page();
  }, // end - start_up

  set_table_page: function() {
    TUPageTable.destroy();
    TUPageTable.setPages(['page1', 'page2']);
    TUPageTable.setHideButton(true);
    TUPageTable.start();
  }, // end - set_table_page

  set_mark: function(elm) {
    $all_mark = $('[name=mark]');
    $row_marking = $.map($all_mark, function(ipt) {
      if ($(ipt).is(':checked')) {
        return $(ipt);
      }
    });

    if ($all_mark.length == $row_marking.length) {
      $('#markAll').prop('checked', true);
    } else {
      $('#markAll').prop('checked', false);
    }

  }, // end - set_mark

  set_mark_all: function(elm) {
    if ($(elm).is(':checked')) {
      $('[name=mark]').prop('checked', true);
    } else {
      $('[name=mark]').prop('checked', false);
    }
  }, // end - set_mark_all

  tampilkan: function() {

    var error = 0;

    if (error == 0) {
      var data_param = {
          'farm': $('#search_farm').val(),
          'status': $('#search_status').val(),
      };

      KontrolStokGlangsing.execute_tampilkan(data_param);
    }
    // else {
    //   alertDialog(error_text.join(', ') + ' <br><b>Tidak boleh kosong</b>');
    // }

  }, // end - tampilkan

  execute_tampilkan: function(data_params) {
    $.ajax({
      url: 'report/kontrol_stok_glangsing/get_list_report/'+ data_params.farm+'/'+ data_params.status,
      // data: {
      //   'params': data_params
      // },
      dataType: 'JSON',
      type: 'GET',
      beforeSend: function() {
        //showLoading();
      },
      success: function(data) {
        if (data.status == 1) {
          //$('#header-list-report').html(data.content.header_list);
          var total_data = data.content.data_list.length;
          if ( total_data > 0 ) {
            console.log(data.content.data_list);
            vdata_list_report = data.content.data_list;
            KontrolStokGlangsing.set_pagination();
          } else {
            $('#table-list-report-kpah').html('');
          }
        } else {
          $('#div-list-report').html(data.message);
          $('#table-list-report-kpah').html('');
        }
      },
    }).done(function() {
      //hideLoading();
    });
  }, // end - execute_tampilkan

  
  ack: function(elm) {
    var row_datas = $.map($('[name=mark]:checked'), function(icheck) {
      var row = $(icheck).closest('tr');
      return {
        'id_kpah': row.find('td.tanggal').attr('data-id')
      };
    });

    if (row_datas.length > 0) {
      bootbox.confirm('Data akan di-ACK?', function(result) {
        if (result) {
          KontrolStokGlangsing.execute_ack(row_datas);
        }
      });
    } else {
      alertDialog("Belum ada data yang tercentang");
    }
  }, // end - execute_ack

  execute_ack: function(data_params) {
    $.ajax({
      url: 'report/KPAH/ack',
      data: {
        'params': data_params
      },
      type: 'POST',
      dataType: 'JSON',
      beforeSend: function() {
        showLoading();
      },
      success: function(data) {
        hideLoading();
        if (data.status == 1) {
          bootbox.alert(data.message, function() {
            KontrolStokGlangsing.tampilkan();
          });
        } else {
          alertDialog(data.message);
        }
      }
    });
  }, // end - execute_ack


  approve: function(elm) {
      var row_datas = $.map($('[name=mark]:checked'), function(icheck) {
          var row = $(icheck).closest('tr');
          return {
              'id_kpah': row.find('td.tanggal').attr('data-id')
          };
      });

      if (row_datas.length > 0) {
          bootbox.confirm('Data akan di-approve?', function(result) {
              if (result) {
                  KontrolStokGlangsing.execute_approve(row_datas);
              }
          });
      } else {
          alertDialog("Belum ada data yang tercentang");
      }
  }, // end - execute_ack

  execute_approve: function(data_params) {
      $.ajax({
          url: 'report/KPAH/approve',
          data: {
              'params': data_params
          },
          type: 'POST',
          dataType: 'JSON',
          beforeSend: function() {
              showLoading();
          },
          success: function(data) {
              hideLoading();
              if (data.status == 1) {
                  bootbox.alert(data.message, function() {
                      KontrolStokGlangsing.tampilkan();
                  });
              } else {
                  alertDialog(data.message);
              }
          }
      });
  }, // end - execute_approve

  set_pagination : function () {
      paginate = $('#pagination-demo');
      paginate.twbsPagination('destroy');
      paginate.twbsPagination({
          totalPages: vdata_list_report.length,
          visiblePages: 1,
          onPageClick: function(event, page) {
              $('#div-list-report').html( vdata_list_report[ page - 1 ] );
              KontrolStokGlangsing.set_table_page();
          }
      });
  }, // end - set_pagination

  print : function() {
      var data_param = {
          //'start_date': dateSQL($('#StartDate_KPAH').datepicker('getDate')),
          //'end_date': dateSQL($('#EndDate_KPAH').datepicker('getDate')),
          'status': $('#search_status').val(),
      };

      var url = 'report/kontrol_stok_glangsing/get_/' + data_param.status + '/' + data_param.start_date + '/' +data_param.end_date + "/print";
      goToURL(url);
  },

  check_button:function(elm) {
      var tbody = $('#main_tbody');
      var tr = elm.closest('tr');
      var ksg_length = $(tbody).find('.check_ksg:checked').length;
      var ppsk_length = $(tbody).find('.check_ppsk:checked').length;

      if(ksg_length == 0 && ppsk_length == 0){
          $('.check_ksg').each(function () {
              this.disabled = false;
          });
          $('.check_ppsk').each(function () {
              this.disabled = false;
          });
          $('#check_all_ppsk').removeAttr('disabled');
      }
      else if (ksg_length > 0) {
          $('.check_ppsk').each(function () {
              this.checked = false;
              this.disabled = true;
          });
          $('#check_all_ppsk').attr('disabled','disabled');
          $('#check_all_ppsk').removeAttr('checked');
      }
      else if(ppsk_length > 0){
          $('.check_ksg').each(function () {
              this.checked = false;
              this.disabled = true;
          });
          $('#check_all_ppsk').removeAttr('disabled');
      }

      if(ksg_length > 0 || ppsk_length > 0){
          $('.btn_simpan').each(function () {
              this.disabled = false;
          });
          $(tr).find('input[type=text]').removeClass('hide');
      }else{
          $('.btn_simpan').each(function () {
              this.disabled = true;
          });
          //$('#btn_cetak').attr('disabled', true);
          $(tr).find('input[type=text]').addClass('hide');
          $(tr).find('input').val('0');
      }
  },
  update:function(elm,_nextStatus) {
      var lookup    = $('.btn_simpan').closest('#main_content');
      var table     = $(lookup).find('#table-list-report-kpah');
      var checked   = $(table).find(':checkbox:checked');
      var ksg_checked   = $(table).find('.check_ksg:checked');
      var ppsk_checked   = $(table).find('.check_ppsk:checked');
      var ksg_length = $(table).find('.check_ksg:checked').length;
      var ppsk_length = $(table).find('.check_ppsk:checked').length;
      var _message  = [];
      var str = '';
      var _int = 0;
      var _data = [];
      var _keterangan = '';
      var _error = 0;
      if(ksg_length > 0){
          var promises = $(ksg_checked).map(function (i, v) {
              var tr = v.closest('tr');
              var budgetData = [];
            //   $(tr).find('input[type=text][id=harga_E]').each(function (a, b) {
            //       console.log('sdasdascjhhdas');
            //       //console.log($(b).data('kodesiklus'));
            //       if(parseInt($(b).val()) < 0){
            //           _error++;
            //           _message.push('Harga pemakaian eksternal harus lebih besar dari 0');
            //       }
            //       budgetData.push({
            //           'kodeBudget' : $(b).data('id'),
            //           'value': $(b).val(),
            //       });
            //   });
              if(_nextStatus == 'RJ'){
                  _keterangan = ''
              }
            //   if(parseInt($(b).val()) < 0){
            //       _error++;
            //       _message.push('Harga pemakaian eksternal harus lebih besar dari 0');
            //   }
              _data.push({
                  'kodeSiklus'      : $(tr).data('kodesiklus'),
                  //'jml_pengajuan'   : $(tr).find('input[type=text][id=jml_pengajuan]').val(),
                  //'harga_pengajuan' : $(tr).find('input[type=text][id=harga_pengajuan]').val(),
                  'harga_d'         : budgetData,
                  'keterangan'      : _keterangan,
              });
              //console.log(_data);
          });
          var _url = 'report/kontrol_stok_glangsing/updateKsg';
          var _confirm = {
              'N'  : 'Apakah anda yakin untuk menyimpan dan rilis laporan stok glangsing akhir siklus ini ?',
              'R1' : 'Apakah anda yakin untuk me-review laporan stok glangsing akhir?',
              'R2' : 'Apakah anda yakin meyetujui laporan stok glangsing akhir?',
              'A'  : 'Apakah anda yakin meyetujui laporan stok glangsing akhir?',
              'RJ' : 'Apakah anda yakin untuk menolak laporan stok glangsing akhir?',
          };

      }else if (ppsk_length > 0) {

          if(_nextStatus == 'RJ'){
              _keterangan = ''
          }
          var promises = $(ppsk_checked).map(function (i, v) {
              var tr = v.closest('tr');
              _data.push({
                  'no_ppsk'    : $(tr).data('no_ppsk'),
                  'keterangan'  : _keterangan,
                  'kode_farm'   : $('#search_farm').val()
              });
          });
          var _url = 'report/kontrol_stok_glangsing/updatePpsk';
          var _confirm = {
            'A' : 'Apakah anda yakin menyetujui Permintaan Sak?',
            'R2' : 'Apakah anda yakin menyetujui Permintaan Sak?',
            'R1' : 'Apakah anda yakin untuk me-review Permintaan Sak?',
            'RJ' : 'Apakah anda yakin untuk menolak Permintaan Sak?',
            'N' : 'Apakah anda yakin untuk menyimpan dan rilis Permintaan Sak?',
          };
      }


      var box = bootbox.confirm({
          message: _confirm[_nextStatus],
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
                  if(_nextStatus == 'RJ'){
                      var box2 = bootbox.confirm({
                          message: "Mohon Mengisi Keterangan Reject <b>(min. 10 karakter)</b><textarea onkeyup='KontrolStokGlangsing.check_keterangan(this)' class='bootbox-input bootbox-input-textarea form-control' id='keterangan_reject'></textarea>",
                          buttons: {
                              confirm: {
                                  label: 'Simpan',
                                  className: 'btn-primary btn-keterangan-reject disabled',
                              },
                              cancel: {
                                  label: 'Batal',
                                  className: 'btn-default'
                              }
                          },
                      //var box2 = bootbox.prompt({
                          //message:

                          callback: function (result) {
                              if(result){
                                  _keterangan = $('#keterangan_reject').val();
                                  promises.promise().done(function(results) {
                                      //console.log("success");
                                      //console.log(_data);
                                      var url = _url;
                                      $.post(url,{ data : _data, nextStatus : _nextStatus, keterangan_reject : _keterangan },function(result){
                                          bootbox.alert(result.message,function(){
                                              $('#main_content').load('report/kontrol_stok_glangsing');
                                          });
                                      },'json');
                                  });
                              }
                          }
                      });
                  }
                  else{
                      promises.promise().done(function(results) {
                          console.log(_nextStatus);
                          console.log(_data);
                          var url = _url;
                          $.post(url,{ data : _data, nextStatus : _nextStatus, keterangan_reject : _keterangan },function(result){
                              if(result.status == 1){
                                toastr.success(result.message,'Berhasil');
                              }else{
                                toastr.error(result.message,'Gagal');
                              }
                              //bootbox.alert(result.message,function(){
                                  $('#main_content').load('report/kontrol_stok_glangsing');
                              //});
                              
                          },'json');
                      });
                  }
              }
          }
      });
  },
  getDetail:function(elm){
    var siklus = $(elm).data('siklus');
    var saldoawal = $(elm).data('saldoawal');
    //alert(siklus);
    if($(elm).hasClass('glyphicon-plus')){
      $('#table-detail'+siklus).show();
      $.ajax({
        url: 'report/kontrol_stok_glangsing/get_detail_ppsk/',
        data: {
          'siklus': siklus
          , 'saldo' : saldoawal
        },
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function() {
          //showLoading();
        },
        success: function(data) {
          if (data.status == 1) {
            //$('#header-list-report').html(data.content.header_list);
            var total_data = data.content.data_list.length;
            if ( total_data > 0 ) {
              console.log(data.content.data_list);
              $('#table-detail'+siklus).html('<td colspan="26">'+data.content.data_list+'</td>');
            } else {
              $('#table-detail'+siklus).html('<td colspan="26">'+data.content.message+'</td>');
            }
          } else {
            $('#div-list-report').html(data.message);
            $('#table-detail'+siklus).html('');
          }
        },
      }).done(function() {
        //hideLoading();
      });
      $(elm).removeClass('glyphicon-plus');
      $(elm).addClass('glyphicon-minus');
    }else{
      $('#table-detail'+siklus).hide();
      $(elm).removeClass('glyphicon-minus');
      $(elm).addClass('glyphicon-plus');
    }


  },
  check_all_ppsk:function(elm) {

      var tbody = $(elm).closest('table');
      var check = $(tbody).find('.check_ppsk');

      $(check).each(function () {
          this.checked = elm.checked;
      });

      KontrolStokGlangsing.check_button(elm);

  },
  check_keterangan:function(elm) {
    //   alert('gasdgshdg');\
    if($(elm).val().length >= 10){
        $('.btn-keterangan-reject').removeClass('disabled');
    }else {
        $('.btn-keterangan-reject').addClass('disabled');
    }
  }
};

KontrolStokGlangsing.start_up();
var data_param = {   
    'farm': $('#search_farm').val(),
    'status': $('#search_status').val(),
};
KontrolStokGlangsing.execute_tampilkan(data_param);
