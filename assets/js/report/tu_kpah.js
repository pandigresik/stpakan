var paginate = null;
var vdata_list_report = null;
var KSG = {
  start_up: function() {
    KSG.set_table_page();
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

      KSG.execute_tampilkan(data_param);
    }
    // else {
    //   alertDialog(error_text.join(', ') + ' <br><b>Tidak boleh kosong</b>');
    // }

  }, // end - tampilkan

  execute_tampilkan: function(data_params) {
    $.ajax({
      url: 'report/ksg/get_list_report_ksg/'+ data_params.farm+'/'+ data_params.status,
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
            KSG.set_pagination();
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

  goto_summary_lr_sopir: function() {
    var url = 'report/laba_rugi_sopir';
    if (!empty($('#StartDate_KPAH').val()) || !empty($('#EndDate_KPAH').val())) {
      var start = $('#StartDate_KPAH').val();
      var end = $('#EndDate_KPAH').val();
      url = 'report/laba_rugi_sopir/index/' + start + '/' + end;
    }
    goToURL(url);
  }, // end goto_summary_lr_sopir

  goto_summary_pembayaran_sopir: function() {
    var url = 'report/summary_pembayaran_sopir';
    if (!empty($('#StartDate_KPAH').val())) {
      var start = dateSQL($('#StartDate_KPAH').datepicker('getDate'));
      url = 'report/summary_pembayaran_sopir/index/' + start;
    }
    goToURL(url);
  }, // end goto_summary_lr_sopir

  submit: function(elm) {

    if ( $('[name=mark]:checked').length > 0 ) {
      // 01 - collect data
      var row_index = [];
      var row_datas = $.map($('[name=mark]:checked'), function(icheck) {
        var row = $(icheck).closest('tr');
        var rdata = {
          'tanggal': row.find('td.tanggal').attr('data-s'),
          'rit': row.find('td.rit').attr('data-s'),
          'nomor_do': row.find('td.nodo').attr('data-s'),
          'nopol': row.find('td.nopol').attr('data-s'),
          'sopir': row.find('td.sopir').attr('data-s'),
          'sopir_id': row.find('td.sopir').attr('data-id'),
          'selisih_kg': row.find('td.selisih_kg').attr('data-s'),
          'selisih_kg_persen': row.find('td.selisih_kg_persen').attr('data-s'),
          'selisih_ekor': row.find('td.selisih_ekor').attr('data-s'),
          'selisih_bb': row.find('td.selisih_bb').attr('data-s'),
          'bonus_selisih_ekor': row.find('td.bonus_selisih_ekor_susut_bb').attr('data-s'),
          'bonus_bb': row.find('td.bonus_bb').attr('data-s'),
          'bonus_jam': row.find('td.bonus_jam').attr('data-s'),
          'denda_selisih_ekor': row.find('td.denda_selisih_ekor').attr('data-s'),
          'denda_susut_berat': row.find('td.denda_susut_berat').attr('data-s'),
          'denda_afkir': row.find('td.denda_afkir').attr('data-s'),
          'denda_jam': row.find('td.denda_jam').attr('data-s'),
          'ongkos': row.find('td.harga_ongkos_angkut').attr('data-s'),
          'harga_pasar': row.find('td.harga_pasar').attr('data-s'),
          'premi_kec_ayam': row.find('td.premi_kec_ayam').attr('data-s'),
          'nett': row.find('td.jumlah_net').attr('data-s'),
        };
        row_index.push(row);
        return rdata;
      });

      var row_error = [];
      for (var i = 0; i < row_datas.length; i++) {
        var iErr = 0;
        $.map(row_datas[i], function(val) {
          if (empty(val) || val == '-') {
            iErr++;
          }
        });

        if (iErr > 0) {
          row_error.push(row_index[i]);
        }
      }

      if (row_error.length == 0) {

        bootbox.confirm('Data akan disubmit?', function(result) {
          if (result) {
            KSG.execute_submit(row_datas);
          }
        });

      } else {
        alertDialog('Ada data belum lengkap pada baris yg tercentang, mohon cek kembali.');
      }

    } else {
      alertDialog("Belum ada data yang tercentang");
    }

  }, // end - submit

  execute_submit: function(data_params) {
    console.log(data_params);
    $.ajax({
      url: 'report/KPAH/submit',
      data: {
        'params': data_params
      },
      type: 'POST',
      dataType: 'JSON',
      beforeSend: function() {
        showLoading()
      },
      success: function(data) {
        hideLoading();
        if (data.status == 1) {
          bootbox.alert(data.message, function() {
            KSG.tampilkan();
          });
        } else {
          alertDialog(data.message);
        }
      }
    });

  }, // end - execute_submit

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
          KSG.execute_ack(row_datas);
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
            KSG.tampilkan();
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
                  KSG.execute_approve(row_datas);
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
                      KSG.tampilkan();
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
              KSG.set_table_page();
          }
      });
  }, // end - set_pagination

  print : function() {
      var data_param = {
          //'start_date': dateSQL($('#StartDate_KPAH').datepicker('getDate')),
          //'end_date': dateSQL($('#EndDate_KPAH').datepicker('getDate')),
          'status': $('#search_status').val(),
      };

      var url = 'report/ksg/get_/' + data_param.status + '/' + data_param.start_date + '/' +data_param.end_date + "/print";
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

      if(ksg_length > 0){
          var promises = $(ksg_checked).map(function (i, v) {
              var tr = v.closest('tr');
              var budgetData = [];
              $(tr).find('input[type=text][id=harga_E]').each(function (a, b) {
                  //console.log($(b).data('kodesiklus'));
                  budgetData.push({
                      'kodeBudget' : $(b).data('id'),
                      'value': $(b).val(),
                  });
              });
              //console.log(budgetData);
              if(_nextStatus == 'RJ'){
                  _keterangan = ''
              }
              _data.push({
                  'kodeSiklus'      : $(tr).data('kodesiklus'),
                  'jml_pengajuan'   : $(tr).find('input[type=text][id=jml_pengajuan]').val(),
                  'harga_pengajuan' : $(tr).find('input[type=text][id=harga_pengajuan]').val(),
                  'harga_d'         : budgetData,
                  'keterangan'      : _keterangan,
              });
          });
          var _url = 'report/ksg/updateKsg';
          var _confirm = {
              'N'  : 'Apakah anda yakin untuk menyimpan dan rilis laporan stok glangsing akhir siklus ini ?',
              'R1' : 'Apakah anda yakin untuk me-review laporan stok glangsing akhir?',
              'R2' : 'Apakah anda yakin untuk me-review laporan stok glangsing akhir?',
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
                  'tgl_buat'    : $(tr).data('tglbuat'),
                  'keterangan'  : _keterangan,
                  'kode_farm'   : $('#search_farm').val()
              });
          });
          var _url = 'report/ksg/updatePpsk';
          var _confirm = {
            'A' : 'Apakah anda yakin meyetujui Permintaan Sak?',
            'R1' : 'Apakah anda yakin untuk me-review Permintaan Sak?',
            'R2' : 'Apakah anda yakin untuk me-review Permintaan Sak?',
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
                          message: "Mohon Mengisi Keterangan Reject <b>(min. 10 karakter)</b><textarea class='bootbox-input bootbox-input-textarea form-control' id='keterangan_reject'></textarea>",
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
                                              $('#main_content').load('report/ksg');
                                          });
                                      },'json');
                                  });
                              }
                          }
                      });
                  }
                  else{
                      promises.promise().done(function(results) {
                          //console.log("success");
                          //console.log(_data);
                          var url = _url;
                          $.post(url,{ data : _data, nextStatus : _nextStatus, keterangan_reject : _keterangan },function(result){
                              bootbox.alert(result.message,function(){
                                  $('#main_content').load('report/ksg');
                              });
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
        url: 'report/ksg/get_detail_ppsk/',
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
              $('#table-detail'+siklus).html('');
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

      KSG.check_button(elm);

  }
};

KSG.start_up();
var data_param = {
    //'start_date': dateSQL($('#StartDate_KPAH').datepicker('getDate')),
    //'end_date': dateSQL($('#EndDate_KPAH').datepicker('getDate')),
    'farm': $('#search_farm').val(),
    'status': $('#search_status').val(),
};
KSG.execute_tampilkan(data_param);
