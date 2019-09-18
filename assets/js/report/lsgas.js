var lsgas = {
    refresh_table : function(elm){
        $('#print_frame').attr('src','');
        var table = $('#tb_lsgas').dataTable({
            lengthChange:false,
            info:false,
            paging:false,
            searching:false,
            bDestroy: true,
            //processing: true,
            serverSide: true,
            ajax:{
                url:"report/lsgas/getStokGlangsingData",
                type:"POST",
                data:{
                    //coba:"oke"
                    kode_farm : $('select[name=list_farm]').find('option:selected').val(),
                    siklus : $('#q_siklus').val(),
                    status : $('#q_status').val(),
                }
            },
            fnCreatedRow: function(nRow, nData, iDataIndex){
                $(nRow).dblclick(function() {
                    var kode_farm = $('select[name=list_farm]').find('option:selected').val();
                    //window.open('report/lsgas/cetakHistori?kode_siklus='+nData[2]+'&siklus_periode='+nData[1]+'&no_urut='+nData[3]+'');
                    $('#print_frame').attr('src','report/lsgas/cetakHistori?kode_farm='+kode_farm+'&kode_siklus='+nData[2]+'&siklus_periode='+nData[1]+'&no_urut='+nData[3]+'');
                })
            },
            aoColumns: [
                { "sClass": "text-center",
                  "mData":[1]
                },{ "sClass": "text-center",
                  "mData":[6]
                },{ "sClass": "text-center",
                  "mData":[7]
                },{ "sClass": "text-center",
                  "mData":[13]
                },{ "sClass": "text-center",
                  "mData":[10]
                },{ "sClass": "text-center",
                  "mData":[11]
                },
                { "sClass": "text-center",
                  "mData":[12]
                  /*"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                      $(nTd).click(function() {
                        alert('asdasdasdasd');
                      })
                    }  */
                },
                { "sClass": "text-center",
                  "mData":[14],
                  /*"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                      $(nTd).click(function() {
                        alert('asdasdasdasd');
                      })
                    }  */
                },
            ]

        });
    },

    reject : function(elm,kode_farm,kode_siklus,_nextStatus,periode_siklus) {
      var _error = 0;
      var _message = [];
      var _panel      = $(elm).closest('div.panel-body');
      var _keterangan_reject = _panel.find('textarea').val();//$('#keterangan_reject').val();

      str = _keterangan_reject;
      if(_keterangan_reject.length < 10){
        _error++;
        _message.push('Keterangan reject tidak boleh kurang dari 10 karakter');
      }
      if(!_error){
          var url = 'report/lsgas/updateLsgas';
          var harga_dijual = $('#harga_dijual').val();
          $.post(url,{  kode_farm : kode_farm, kode_siklus : kode_siklus, nextStatus : _nextStatus, periode_siklus : periode_siklus, harga_dijual :harga_dijual, keterangan_reject : _keterangan_reject},function(data){
              $('#tooltip-reject').tooltipster('hide');
              bootbox.alert(data.message,function(){
              //$('#main_content').load('permintaan_sak_kosong/permintaan');
              $('#print_frame').attr('src','report/lsgas/cetakHistori?kode_farm='+kode_farm+'&kode_siklus='+data.kode_siklus+'&siklus_periode='+data.periode_siklus+'&no_urut='+data.no_urut+'');
              lsgas.refresh_table();
            });
          },'json');
      }

    },
    update : function(elm,kode_farm,kode_siklus,_nextStatus,periode_siklus){

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
        var detail_tabel = ['<div class="row" style="margin-top:35px;">',
            '<table class="table table-bordered table-striped dataTable" id="tblPopUp">',
                '<thead><tr>',
                    '<th>Keterangan</th>',
                    '<th>Harga Per Sak</th>',
                '</tr></thead>',
                '<tbody></tbody>',
            '</table>',
            '</div>'];
        var _confirm = {
            'A1' : 'Apakah anda yakin untuk mengkonfirmasi periode siklus '+periode_siklus+ ' ?<br/>'+detail_tabel.join(''),
            'A2' : 'Apakah anda yakin untuk mengkonfirmasi periode siklus '+periode_siklus+ ' ?<br/>'+detail_tabel.join(''),
            'R' : 'Apakah anda yakin untuk mengkonfirmasi periode siklus '+periode_siklus+ ' ?<br/>'+detail_tabel.join(''),
          'RJ' : 'Apakah anda yakin untuk menolak periode siklus '+periode_siklus+ ' ?<br/>'+detail_tabel.join(''),
          'N' : 'Apakah anda yakin untuk merilis transaksi ini ?',
        };
        /*if(_sakdiminta > _saktersimpan){
          _error++;
          _message.push('Jumlah yang diminta harus lebih kecil atau sama dengan jumlah sak tersimpan');
        }*/
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
                  if(_nextStatus == 'N'){
                      var input_str =[
                                    '<div class="row">',
                                        '<table class="table table-bordered table-striped dataTable">',
                                            '<tr>',
                                                '<th>Keterangan</th>',
                                                '<th>Harga Per Sak</th>',
                                            '</tr>',
                                            '<tr>',
                                                '<td width=60%>Glangsing Untuk Pupuk</td>',
                                                '<td align="center">Rp.<input class="form-control number" id="harga_dijual" placeholder="" type="text" value="0" style="width:100px;display:inline">.00</td>',
                                            '</tr>',
                                        '</table>',
                                        '</div>',
                                        '<br />',
                                        '<div class="div_detailInformasi"></div>'

                            ];
                      var _options = {
                         title : 'Penawaran Harga Jual',
                         message : input_str.join(''),
                         className : 'medium',
                         buttons : {
                             unset : {
                                 label : 'Batal',
                                 className : '',
                                 callback : function(e){

                                 }
                             },
                             set : {
                                 label : 'Rilis',
                                 className : '',
                                 callback : function(e){
                                      if(_nextStatus == 'RJ'){
                                         reject++;
                                      }
                                      else{
                                          var url = 'report/lsgas/updateLsgas';
                                          var harga_dijual = $('#harga_dijual').val();
                                          $.post(url,{  kode_farm : kode_farm, kode_siklus : kode_siklus, nextStatus : _nextStatus, periode_siklus : periode_siklus, harga_dijual :harga_dijual},function(data){
                                            bootbox.alert(data.message,function(){
                                              //$('#main_content').load('permintaan_sak_kosong/permintaan');
                                              $('#print_frame').attr('src','report/lsgas/cetakHistori?kode_farm='+kode_farm+'&kode_siklus='+data.kode_siklus+'&siklus_periode='+data.periode_siklus+'&no_urut='+data.no_urut+'');
                                              lsgas.refresh_table();
                                            });
                                          },'json');
                                      }
                                 }
                             }
                         },
                     };
                     bootbox.dialog(_options);
                  }else{
                      if(_nextStatus == 'RJ'){
                         reject++;
                      }
                      else{
                          var url = 'report/lsgas/updateLsgas';
                          var harga_dijual = $('#harga_dijual').val();
                          $.post(url,{  kode_farm : kode_farm, kode_siklus : kode_siklus, nextStatus : _nextStatus, periode_siklus : periode_siklus, harga_dijual :harga_dijual},function(data){
                            bootbox.alert(data.message,function(){
                              //$('#main_content').load('permintaan_sak_kosong/permintaan');
                              lsgas.refresh_table();
                              $('#print_frame').attr('src','report/lsgas/cetakHistori?kode_farm='+kode_farm+'&kode_siklus='+data.kode_siklus+'&siklus_periode='+data.periode_siklus+'&no_urut='+data.no_urut+'');
                            });
                          },'json');
                      }
                  }


              }
            }
          });
          if(_nextStatus != 'N'){
              var url = 'report/lsgas/getHargaJualGlangsing';
              var harga_dijual = $('#harga_dijual').val();
              $.post(url,{  kode_siklus : kode_siklus},function(data){
                  console.log(data);
                  var strHtml = '';
                  	$.each(data, function(key, value) {
                        var nama_budget = value.NAMA_BUDGET;
                        strHtml += '<tr><td width=60%>Glangsing untuk '+nama_budget.toLowerCase()+'</td>'+
                                    '<td align="center">Rp. '+ parseInt(value.HARGA) +'.00</td></tr>'
                    });
                  $('#tblPopUp tbody').html(strHtml);
                //bootbox.alert(data.message,function(){
                  //$('#main_content').load('permintaan_sak_kosong/permintaan');
                  //$('#print_frame').attr('src','report/lsgas/cetakHistori?kode_farm='+kode_farm+'&kode_siklus='+data.kode_siklus+'&siklus_periode='+data.periode_siklus+'&no_urut='+data.no_urut+'');
                //});
              },'json');
          }
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

};


$(function(){
    if($("#tb_lsgas").length > 0){
        /*$("#tb_lsgas").dataTable({"lengthChange": false, "info": false, "paging": false});
        */

        lsgas.refresh_table();

        $("#tb_lsgas").on('page.dt',function () {
            onresize(100);
        });
    }

    $('#tb_lsgas tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            $('#tb_lsgas tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });
});
function lengthCek(str) {
  if(str.value.length < 10){
    $('.btn_simpan_reject').prop('disabled',true);
  }
  else{
    $('.btn_simpan_reject').prop('disabled',false);
  }
}
