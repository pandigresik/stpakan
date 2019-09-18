var pengembalianSakAck = {
    _varSakBatasAtas : 0.134,
    _varSakBatasBawah : 0.114,
    _varBrtBawah : 0,
    _varBrtAtas : 0,
    _varNoReg : '',
    _varNoPPSK : '',
    _varJmlDiminta : 0,
    _varBrtTimbang : 0.00,
    _timer : true,
    _tkode_pegawai : '',
    _tnama_pegawai : '',
    _date_transaction : null,
    _trSelected : null,
    _resultData : null,

    loadPage : function(){
      var _kandang = $('select[name=kandang]').find('option:selected').val();
      var _status = $('input[name=filterStatus]:checked').map(function(){
                      return this.value;
                    }).get().join(",");

      $.ajax({
          type : "POST",
          url : "permintaan_glangsing/pengembalian_sak/getListPengembalianAck",
          data : {
              kode_kandang : _kandang
              , status : _status
          },
          dataType : 'json',
          success : function(data) {
              if(data.status == '1'){
                  $('.custom_table tbody').html(data.data);
                  //toastr.success(data.message,'Berhasil');
              }
              else{
                  toastr.warning(data.message,'Peringatan');
              }
          }
      });
      console.log(_status);
    },

    check_button:function(elm) {
        var checkLength = $('[name=ack]:checked').length;

        //console.log(checkLength > 0);
        if(checkLength > 0){
          $('button').removeAttr('disabled');
        }else{
          //$('button').removeAttr('disabled');
          $('button').attr('disabled','disabled');
        }
    },

    simpan : function(elm){
        var _check = $('[name=ack]:checked');
        var _data = [];
        var promises = $(_check).map(function (i, v) {
          
          _data.push({
              'no_ppsk' : $(v).data('no_ppsk'),
              'no_reg'  : $(v).data('no_reg'),
          });
        });
        var box = bootbox.confirm({
          message: 'Apakah anda yakin untuk melanjutkan proses ACK?',
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
                  promises.promise().done(function(results) {
                      console.log(_data);
                      var url = 'permintaan_glangsing/pengembalian_sak/simpanAck';
                      $.post(url,{ data : _data},function(result){
                          if(result.status == 1){
                            toastr.success(result.message,'Berhasil');
                          }else{
                            toastr.error(result.message,'Gagal');
                          }
                          //bootbox.alert(result.message,function(){
                          pengembalianSakAck.loadPage();
                          //});
                          
                      },'json');
                  });
              }
          }
      });
    }
};

pengembalianSakAck.loadPage();