var permintaanSak = {
    submitPenjualan : function(elm,_nextStatus){
      var _error = 0;
      var _message = [];
      var _form = $(elm).closest('form');
      var _budgetsisa = parseInt(_form.find('input[name=budget_sisa]').unmask());
      var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
      var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
      var _kategori = $.trim(_form.find('radio[name=kategori]').val());
      var _NoDo = $.trim(_form.find('input[name=no_do]').val());
      var _userpenerima = _form.find('input[name=user_penerima]').val();
      var _descketerangan = _form.find('select[name=keterangan]').find('option:selected').text();
      /*if(_sakdiminta > _budgetsisa){
        _error++;
        _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sisa budget sak');
      }*/
      if(_sakdiminta < 1 || isNaN(_sakdiminta)){
        _error++;
        _message.push('Jumlah yang diminta harus lebih besar dari 0');
      }
      if(empty(_userpenerima)){
        _error++;
        _message.push('Penerima harus diisi');
      }
      if(!_error){
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
              var url = 'permintaan_sak_kosong/permintaan/simpan';
              var _prefix_ppsk = _form.find('input[name=sak_tersimpan]').data('prefix_ppsk');
              var _data = {/*'kategori':_kategori, */'jml_sak' : _sakdiminta, 'no_do' : _NoDo, 'user_peminta' : _userpenerima, 'no_ppsk': _prefix_ppsk, 'status':_nextStatus};
              $.post(url,{ data : _data,nextStatus:_nextStatus },function(data){
                bootbox.alert(data.message,function(){
                  $('#main_content').load('permintaan_sak_kosong/permintaan');
                });
              },'json');
            }
          }
        });
      }else{
        bootbox.alert(_message.join('<br />'));
      }
      return false;
    },

    submit : function(elm,_nextStatus){
     var _error = 0;
      var _message = [];
      var _form = $(elm).closest('form');
      var _budgetsisa = parseInt(_form.find('input[name=budget_sisa]').unmask());
      var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
      var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
      var _jmlOver = parseInt(_form.find('input[name=jml_over]').unmask());
      var _alasanOver = $.trim(_form.find('textarea[name=alasan_over]').val());
      var _kategori = $.trim(_form.find('radio[name=kategori]').val());
      var _keterangan = $.trim(_form.find('select[name=keterangan]').val());
      var _userpeminta = _form.find('select[name=user_peminta]').val();
      var _descketerangan = _form.find('select[name=keterangan]').find('option:selected').text();
      _jmlOver = isNaN(_jmlOver)?'0':_jmlOver;
      _sakdiminta = isNaN(_sakdiminta)?'0':_sakdiminta;
      var _confirm = {
        'D' : 'Apakah anda yakin menyimpan transaksi ini ?',
        'N' : 'Apakah anda yakin untuk merilis transaksi ini ?',
      };
      /*if(_sakdiminta > _budgetsisa){
        _error++;
        _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sisa budget sak');
      }*/
      if(_sakdiminta < 1 && _jmlOver <= 0){
        _error++;
        _message.push('Jumlah yang diminta harus lebih besar dari 0');
      }
      if(_keterangan == ''){
        _error++;
        _message.push('Keterangan harus dipilih');
      }
      if(empty(_userpeminta)){
        _error++;
        _message.push('Penerima harus dipilih');
      }
      if(empty(_alasanOver) && _jmlOver > 0){
        _error++;
        _message.push('alasan over budget tidak boleh kosong');
      }
      if(!_error){
        bootbox.confirm({
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
              var url = 'permintaan_sak_kosong/permintaan/simpan';
              var _prefix_ppsk = _form.find('input[name=sak_tersimpan]').data('prefix_ppsk');
              var _data = {/*'kategori':_kategori, */'jml_sak' : _sakdiminta, 'kode_budget' : _keterangan, 'user_peminta' : _userpeminta, 'no_ppsk': _prefix_ppsk, 'status':_nextStatus,'jml_over':_jmlOver,'no_do' : ''};
              $.post(url,{ data : _data,nextStatus:_nextStatus,alasan_over:_alasanOver },function(data){
                bootbox.alert(data.message,function(){
                  $('#main_content').load('permintaan_sak_kosong/permintaan');
                });
              },'json');
            }
          }
        });
      }else{
        bootbox.alert(_message.join('<br />'));
      }
      return false;
    },

    update : function(elm,_nextStatus){
      if(_nextStatus == 'RJA'){
        var _error = 0;
        var _panel = $(elm).closest('div.panel-body');
        var _keterangan_reject = _panel.find('textarea').val();
        var _kodefarm = $('select[name=list_farm]').find('option:selected').val();

         str = _keterangan_reject;
         if(_keterangan_reject.length < 10){
            _error++;
            _message.push('Keterangan reject tidak boleh kurang dari 10 karakter');
         }
         if(empty(_alasanOver) && _jmlOver > 0){
            _error++;
            _message.push('alasan over budget tidak boleh kosong');
         }

         if(!_error){
           $.ajax({
            url : 'permintaan_sak_kosong/permintaan/getFormPPSKData',
            type:'POST',
            data : {
              no_ppsk : T_NO_PPSK
            },
            dataType : 'JSON',
            success : function(result){
              if(result){
                 var url = 'permintaan_sak_kosong/permintaan/update';
                 var _data = {'jml_sak' : result[0].JML_SAK, /*'keterangan' : _keterangan,*/'kode_budget' : result[0].KODE_BUDGET, 'user_peminta' : result[0].USER_PEMINTA, 'no_ppsk': result[0].NO_PPSK,'jml_over':result[0].JML_OVER};
                 $.post(url,{ data : _data, nextStatus : 'RJ', keterangan_reject:_keterangan_reject, alasan_over:result[0].KETERANGAN, kodefarm:_kodefarm},function(data){
                   bootbox.alert(data.message,function(){
                     $('#main_content').load('permintaan_sak_kosong/permintaan/approvalpsk');
                   });
                 },'json');
              }
            }
           });
         }
        return false;
      }
      else if(_nextStatus == 'AA'){
         var _tr = elm.closest('tr');
         var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
         var box = bootbox.confirm({
           message: 'Apakah anda yakin untuk mengkonfirmasi No. Permintaan Sak '+$(_tr).find('.link_span').data('no_ppsk')+ ' ?',
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
                $.ajax({
                url : 'permintaan_sak_kosong/permintaan/getFormPPSKData',
                type:'POST',
                data : {
                   no_ppsk : $(_tr).find('.link_span').data('no_ppsk'),
                },
                dataType : 'JSON',
                success : function(result){
                   if(result){
                     var url = 'permintaan_sak_kosong/permintaan/update';
                     var _data = {'jml_sak' : result[0].JML_SAK, /*'keterangan' : _keterangan,*/'kode_budget' : result[0].KODE_BUDGET, 'user_peminta' : result[0].USER_PEMINTA, 'no_ppsk': result[0].NO_PPSK,'jml_over':result[0].JML_OVER};
                     $.post(url,{ data : _data, nextStatus : 'A', kodefarm : _kodefarm},function(data){
                      bootbox.alert(data.message,function(){
                         $('#main_content').load('permintaan_sak_kosong/permintaan/approvalpsk');
                      });
                     },'json');
                   }
                }
               });
             }
           }
        });
        return false;
      }
      else{
        var _error = 0;
        var _message = [];
        var _form = $('.form_permintaan');
        var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
        var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
        var _keterangan = $.trim(_form.find('select[name=keterangan]').val());
        var _userpeminta = _form.find('select[name=user_peminta]').val();
        var _no_ppsk = _form.find('input[name=no_ppsk]').val();
        var _jmlOver = parseInt(_form.find('input[name=jml_over]').unmask());
        var _alasanOver = $.trim(_form.find('textarea[name=alasan_over]').val());
        var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
        _jmlOver = isNaN(_jmlOver)?'0':_jmlOver;
        _sakdiminta = isNaN(_sakdiminta)?'0':_sakdiminta;
        var str = '';
        var _confirm = {
          'A' : 'Apakah anda yakin untuk mengkonfirmasi No. Permintaan Sak '+_no_ppsk+ ' ?',
          'R' : 'Apakah anda yakin untuk mengkonfirmasi No. Permintaan Sak '+_no_ppsk+ ' ?',
          'RJ' : 'Apakah anda yakin untuk menolak No. Permintaan Sak '+_no_ppsk+ ' ?',
          'D' : 'Apakah anda yakin untuk mengubah data dengan No. Permintaan Sak '+_no_ppsk+ ' ?',
          'N' : 'Apakah anda yakin untuk merilis transaksi ini ?',
          'V' : 'Apakah anda yakin untuk menghapus No. Permintaan Sak '+_no_ppsk+ ' ?',
        };
        /*if(_sakdiminta > _saktersimpan){
          _error++;
          _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sak tersimpan');
        }*/
        if(_sakdiminta < 1 && _jmlOver <= 0){
          _error++;
          _message.push('Jumlah yang diminta harus lebih besar dari 0');
        }
        if(_keterangan == ''){
          _error++;
          _message.push('Keterangan harus dipilih');
        }
        if(empty(_userpeminta)){
          _error++;
          _message.push('Penerima harus dipilih');
        }
        if(!_error){
          var reject = 0;
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
                    reject++;
                 }
                 else{
                     var url = 'permintaan_sak_kosong/permintaan/update';
                     var _data = {'jml_sak' : _sakdiminta, /*'keterangan' : _keterangan,*/'kode_budget' : _keterangan, 'user_peminta' : _userpeminta, 'no_ppsk': _no_ppsk,'jml_over':_jmlOver};
                     $.post(url,{ data : _data, nextStatus : _nextStatus, keterangan_reject:str,alasan_over:_alasanOver, kodefarm : _kodefarm },function(data){
                       bootbox.alert(data.message,function(){
                        //  $('#main_content').load('permintaan_sak_kosong/permintaan');
                        permintaanSak.loadListPermintaan(this);
                        $('#div_permintaan').html('');
                        $('#histori').html('');
                       });
                     },'json');
                 }
              }
            }
          });
          box.bind('hidden.bs.modal', function() {
            if(reject>0){
               $('#tooltip-reject').tooltipster({
                  animation : 'fade',
                  delay : 200,
                  theme : 'tooltipster-light',
                  touchDevices : false,
                  contentAsHTML : true,
                  interactive : true,
                  position : 'bottom-left',
                  content: $('.btn-danger').siblings('.tooltipster-span').html()
                });
                $('#tooltip-reject').tooltipster('show');
            }
          });
        }else{
          bootbox.alert(_message.join('<br />'));
        }
        return false;
      }
    },
    reject : function(elm) {
      var _error = 0;
      var _message = [];
      var _form = $('.form_permintaan');
      var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
      var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
      var _keterangan = $.trim(_form.find('select[name=keterangan]').val());
      var _userpeminta = _form.find('select[name=user_peminta]').val();
      var _no_ppsk    = _form.find('input[name=no_ppsk]').val();
      var _panel      = $(elm).closest('div.panel-body');
      var _keterangan_reject = _panel.find('textarea').val();//$('#keterangan_reject').val();
      var _jmlOver    = parseInt(_form.find('input[name=jml_over]').unmask());
      var _alasanOver = $.trim(_form.find('textarea[name=alasan_over]').val());
      var _kodefarm   = $('select[name=list_farm]').find('option:selected').val();

      str = _keterangan_reject;
      if(_keterangan_reject.length < 10){
        _error++;
       _message.push('Keterangan reject tidak boleh kurang dari 10 karakter');
      }
      if(empty(_alasanOver) && _jmlOver > 0){
        _error++;
        _message.push('alasan over budget tidak boleh kosong');
      }
      if(!_error){
         $('#tooltip-reject').tooltipster('hide');
         var url = 'permintaan_sak_kosong/permintaan/update';
         var _data = {'jml_sak' : _sakdiminta, /*'keterangan' : _keterangan,*/'kode_budget' : _keterangan, 'user_peminta' : _userpeminta, 'no_ppsk': _no_ppsk,'jml_over':_jmlOver};
         $.post(url,{ data : _data, nextStatus : 'RJ', keterangan_reject:str,alasan_over:_alasanOver, kodefarm : _kodefarm },function(data){
           bootbox.alert(data.message,function(){
             $('#main_content').load('permintaan_sak_kosong/permintaan');
           });
         },'json');
      }

    },
    updatePenjualan : function(elm,_nextStatus){
      var _error = 0;
      var _message = [];
      var _form = $(elm).closest('form');
      var _budgetsisa = parseInt(_form.find('input[name=budget_sisa]').unmask());
      var _saktersimpan = parseInt(_form.find('input[name=sak_tersimpan]').unmask());
      var _sakdiminta = parseInt(_form.find('input[name=jml_sak]').unmask());
      var _kategori = $.trim(_form.find('radio[name=kategori]').val());
      var _NoDo = $.trim(_form.find('input[name=no_do]').val());
      var _userpenerima = _form.find('input[name=user_penerima]').val();
      var _descketerangan = _form.find('select[name=keterangan]').find('option:selected').text();
      var _no_ppsk = _form.find('input[name=no_ppsk]').val();
      var _kodefarm = $('select[name=list_farm]').find('option:selected').val();
      var _confirm = {
        'A' : 'Apakah anda yakin menyimpan transaksi ini ?',
      };
      if(_sakdiminta < 1){
        _error++;
        _message.push('Jumlah yang diminta harus lebih besar dari 0');
      }
      if(empty(_userpenerima)){
        _error++;
        _message.push('Penerima harus diisi');
      }
      if(!_error){
        bootbox.confirm({
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
              var url = 'permintaan_sak_kosong/permintaan/update';
              var _prefix_ppsk = _form.find('input[name=sak_tersimpan]').data('prefix_ppsk');
              var _data = {/*'kategori':_kategori, */'jml_sak' : _sakdiminta, 'no_do' : _NoDo, 'user_peminta' : _userpenerima, 'no_ppsk': _no_ppsk, 'status':_nextStatus};
              $.post(url,{ data : _data, nextStatus : _nextStatus, keterangan_reject:'',type : 'penjualan',kodefarm : _kodefarm},function(data){
                bootbox.alert(data.message,function(){
                  $('#main_content').load('permintaan_sak_kosong/permintaan');
                });
              },'json');
            }
          }
        });
      }else{
        bootbox.alert(_message.join('<br />'));
      }
      return false;
    },

    editView : function(elm){
      $('.panel_histori').css('display','none');
      var _no_ppsk = $(elm).data('no_ppsk');
      var _status =  $(elm).data('status');
      var _kode_budget =  $(elm).data('kode_budget');
      var _kodefarm = $('select[name=list_farm]').find('option:selected').val();

      if(_no_ppsk.substring(0,4) == 'PPSK'){
        var _url = 'permintaan_sak_kosong/permintaan/viewForm';
      }
      else{
        var _url = 'permintaan_sak_kosong/permintaan/viewFormPenjualan';
      }

      $.ajax({
        url : _url,
        data : {no_ppsk : _no_ppsk, status : _status, kode_budget : _kode_budget, kodefarm : _kodefarm},
        dataType : 'html',
        beforeSend : function(){
          $('#div_permintaan').html('Loading ......');
        },
        success : function(data){
          var hasil = $('#div_permintaan').html(data);
          $('#div_list_farm').css('display','none');
         //  if(hasil){
         //    $('#btn_reject').tooltipster({
         //      animation : 'fade',
         //      delay : 200,
         //      theme : 'tooltipster-light',
         //      touchDevices : false,
         //      trigger : 'click',
         //      contentAsHTML : true,
         //      interactive : true,
         //      position : 'bottom-left',
         //      content: $('.btn-danger').siblings('.tooltipster-span').html()
         //    });
         //  }

        }
      });

      $.ajax({
        url : 'permintaan_sak_kosong/permintaan/loadViewHistory',
        data : {
           no_ppsk : _no_ppsk, kodefarm : _kodefarm
        },
        dataType : 'html',
        type:'POST',
        beforeSend : function(){
          $('#histori').html('Loading ......');
        },
        success : function(data){
          var hasil = $('#histori').html(data);
          if(hasil){
             $('.panel_histori').css('display','inline');
          }
        }
      });
    },
    baru : function(elm){
      var _url = 'permintaan_sak_kosong/permintaan/newForm';
      $.ajax({
        url : _url,
        data : {},
        dataType : 'html',
        beforeSend : function(){
          $('#div_permintaan').html('Loading ......');
        },
        success : function(data){
          $('#div_permintaan').html(data);
        }
      });
    },
   showButton:function(elm) {
      var _tr = elm.closest('tr');
      $(_tr).find('.btn_approve').css('display','inline');
      $(_tr).find('.btn_reject').css('display','inline');
   },
   loadListPermintaan:function(elm) {
      var _url = 'permintaan_sak_kosong/permintaan/loadListPermintaan';
      $.ajax({
        url : _url,
        data : {
           kode_farm : $('select[name=list_farm]').find('option:selected').val(),
        },
        dataType : 'html',
        method:'POST',
        beforeSend : function(){
          $('#div_list_permintaan').html('Loading ......');
        },
        success : function(data){
          $('#div_list_permintaan').css('display','inline');
          $('#div_list_permintaan').html(data);
        }
      });
   }
};
$(function(){
  $('.number').priceFormat({
    prefix: '',
    centsSeparator: ',',
    centsLimit : 0,
    clearOnEmpty : false,
    thousandsSeparator: '.'
  });
  /*$('#btn_reject').tooltipster({
    animation : 'fade',
    delay : 200,
    theme : 'tooltipster-light',
    touchDevices : false,
    trigger : 'click',
    contentAsHTML : true,
    interactive : true,
    position : 'bottom-left',
    content: $('.btn-danger').siblings('.tooltipster-span').html()
  });
  $('.custom_table tbody tr').each(function() {
    alert($(this).find('#stt_ppsk:contains(Draft)'));
    $(this).find('#stt_ppsk:contains(Draft)').hide();
  })*/
});
function load_keterangan(kategori){
  $.ajax({
    url : 'permintaan_sak_kosong/Permintaan/getJumlahSak',
    data : {
      prefix_ppsk : $('#prefix_ppsk').val(),
      kategori    : kategori.value,
      kode_budget : '',
      kodefarm    : $('select[name=list_farm]').find('option:selected').val()
    },
    type:'POST',
    dataType : 'json',
    success : function(data){
      //$('#keterangan').html(data);
      $('#budget_sisa').val(data.budgetsisa);
      $('#budget_sisa_t').val(data.budgetsisa);
      $('#budget_total').val(data.budgettotal);
      $('#jml_sak').val('0');
    }
  });

  $.ajax({
    url : 'permintaan_sak_kosong/Permintaan/listbudgetglangsing2',
    data : {
      kategori:kategori.value
    },
    type:'POST',
    dataType : 'html',
    success : function(data){
      $('#keterangan').html(data);
    }
  });
}
function hitungSisaBudget(str){
  var total_sak = $('#sak_tersimpan_t').val();
  var total_budget = $('#budget_total').val();
  var budget_sisa = $('#budget_sisa_t').val();

  $('#sak_tersimpan').val(total_sak-str.value);
  $('#budget_sisa').val(budget_sisa-str.value);

  if (budget_sisa-str.value < 0) {
    $('#jml_over').prop('readonly',false);
    $('#alasan_over').prop('readonly',false);
    $('#jml_over').focus();

    $('#jml_sak').val(budget_sisa);
    $('#sak_tersimpan').val(total_sak-budget_sisa);
    $('#budget_sisa').val(0);

    $('#over_warning').css('display','inline');
  }
  else{
    $('#jml_over').prop('readonly',true);
    $('#alasan_over').prop('readonly',true);
    $('#jml_over').val('');
    $('#alasan_over').val('');
    $('#over_warning').css('display','none');
  }
}

function loadTotalBudget(keterangan){
  $.ajax({
    url : 'permintaan_sak_kosong/Permintaan/getJumlahSak',
    data : {
      prefix_ppsk : $('#prefix_ppsk').val(),
      kategori    : $("input[name='kategori']:checked").val(),
      kode_budget : keterangan.value,
      kodefarm    : $('select[name=list_farm]').find('option:selected').val(),
    },
    type:'POST',
    dataType : 'json',
    success : function(data){
      //$('#keterangan').html(data);
      $('#budget_sisa').val(data.budgetsisa - $('#jml_sak').val());
      $('#budget_sisa_t').val(data.budgetsisa);
      $('#budget_total').val(data.budgettotal);
    }
  });
}
function lengthCek(str) {
  if(str.value.length < 10){
    $('.btn_simpan_reject').prop('disabled',true);
  }
  else{
    $('.btn_simpan_reject').prop('disabled',false);
  }
}
