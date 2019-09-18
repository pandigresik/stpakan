'use strict';
var pengajuanHarga = {

    _varNoPengajuanHarga : '',
    _hargaRegional : {},
    _varKodeFarm : '',
    _timer : true,
    _tkode_pegawai : '',
    _tnama_pegawai : '',
    _date_transaction : null,
    _trSelected : null,
    _hargaPengajuanLama : {},
    _formatNumber : {
        decimal : {
          prefix: '',
          centsSeparator: ',',
          centsLimit : 2,
          thousandsSeparator: '.'
        },
        angka : {
          prefix: '',
          centsSeparator: '',
          centsLimit : 0,
          clearOnEmpty : true,
          thousandsSeparator: ''
        },
        telpon : {
          prefix: '0',
          centsSeparator: '',
          centsLimit : 0,
          clearOnEmpty : false,
          thousandsSeparator: '',
          insertPlusSign : false,
          allowNegative : false,
        },
        integer : {
          prefix: '',
          centsSeparator: ',',
          centsLimit : 0,
          thousandsSeparator: '.'
        },
        tahun : {
          prefix: '',
          centsSeparator: ',',
          limit : 4,
          centsLimit : 0,
          clearOnEmpty : true,
          thousandsSeparator: ''
        },
      },
    kembali: function(){
        $('#main_content').load('sales_order/pengajuan_harga/');
    },  
    baru: function(elm){
        $(elm).hide();
        $(elm).next().removeClass('hide');
        $('tr.entry_pengajuan').removeClass('hide');
        var _dropdownFarm = $('tr.entry_pengajuan').find('select');
        if(_dropdownFarm.data('userlevel') == 'KF'){
            _dropdownFarm.find('option:last').prop('selected',1);
            _dropdownFarm.trigger('change');            
        }
    },

    buatRef: function(elm){
        var _statusPengajuan = $(elm).find('td.tgl_pengajuan').data('status_pengajuan');
        var _userLevel = $(elm).data('userlevel');
        var _kodeFarm = $(elm).find('td.tgl_pengajuan').data('kode_farm');
        var _namaFarm = $(elm).find('td.nama_farm').text();
        var _keterangan = $(elm).find('td.keterangan').text();
        if(_statusPengajuan == 'D' && _userLevel == 'KDLOG'){
            var _rilisBtn = $('div.div_btn').find('.btn-primary:first');
            _rilisBtn.hide();
            _rilisBtn.next().removeClass('hide');
            $('tr.entry_pengajuan').removeClass('hide');
            var _dropdownFarm = $('tr.entry_pengajuan').find('select');
            _dropdownFarm.find('option[value='+_kodeFarm+']').prop('selected',1);
            _dropdownFarm.trigger('change');
            _dropdownFarm.hide();
            $('<label>'+_namaFarm+'</label>').insertAfter(_dropdownFarm);  
            $('tr.entry_pengajuan').find('td.keterangan').text(_keterangan);
        }        
    },
    
    newForm:function(elm) {
        var _url = 'sales_order/pengajuan_harga/newForm';
        $.ajax({
          url : _url,
          data : {
             kode_farm : $('select[name=list_farm]').find('option:selected').val(),
          },
          dataType : 'html',
          method:'POST',
          beforeSend : function(){
            $('#div_content').html('Loading ......');
          },
          success : function(data){
            $('#div_content').css('display','inline');
            $('#div_content').html(data);
          }
        });
    },
    getHargaLama : function(no_pengajuan){
        var ini = this;
        if(this._hargaPengajuanLama[no_pengajuan] == undefined){
            var _url = 'sales_order/pengajuan_harga/getHargaPengajuan';
            $.ajax({
                type : 'get',
                url : _url,
                data : { pengajuan_harga : no_pengajuan},
                success : function(data){
                    if(data.status){                        
                        ini._hargaPengajuanLama[no_pengajuan] = data.content;                
                    }
                    
                },
                async : false,
                cache : true,
                dataType : 'json',
            });
        }
        return ini._hargaPengajuanLama[no_pengajuan];
    },
    submit : function(elm, status){
      var _data = [];
      var _url = '';
      var _message = '',_error = 0, _pesan = '';

      if(in_array(status,['N','D'])){
        _message = 'Apakah anda yakin akan melakukan pengajuan harga?'; 
        var _dropdown = $('tr.entry_pengajuan').find('select');
        var _kodeFarm = _dropdown.val();
        var _pengajuanLama = _dropdown.find('option:selected').data('pengajuanlama');
        var _idRef = _pengajuanLama.no_pengajuan_harga;
        var _setVoidLama = 0;
        var _tambahPesan = ['N','R1'];
        if(status == 'D'){
            _tambahPesan = ['D'];
        }        
        if(in_array(_pengajuanLama.status,_tambahPesan)){
            if(!empty(_pengajuanLama.status)){
                if(status == 'D'){
                    _message = 'Terdapat pengajuan harga yang belum ditindaklanjuti Departemen Logistik.<br /> Apakah anda yakin akan merilis pengajuan harga baru ?';
                }else{
                    _message = 'Terdapat pengajuan harga yang belum diapprove.<br /> Apakah anda yakin akan merilis pengajuan harga baru ?';
                    _setVoidLama = 1;
                }                
                _message += '<br > *) Jika ya, maka pengajuan harga sebelumnya akan dibatalkan prosesnya.';                 
            }                                
        }else{
            _setVoidLama = 1;
        }
        var _td, _tmp,_nilaiHarga, _tr, _hargaReg;
        _url = 'sales_order/pengajuan_harga/simpan';        
        $('input[name=harga],input[name=harga_reg]').each(function () {
            _td = $(this).closest('td');
            _tr = _td.closest('tr');
            _tmp = {
                'kode_farm'     : _kodeFarm,
                'kode_barang'   : $(this).data('kode_barang'),
                'jumlah'        : parse_number(_tr.find('td.estimasi_jumlah').text(),'.',','),
                'tgl_pengajuan' : $(this).data('tgl_pengajuan'),                
                'no_urut'       : $(this).closest('tr').data('no_urut'),                
                'status'        : status
            };
            _nilaiHarga = parse_number($(this).val(),'.',',');
            _tmp[$(this).attr('name')] = _nilaiHarga;
            if(_setVoidLama){
                if(empty(_idRef)){
                    _idRef = _dropdown.closest('tr').find('td.ref').text();
                }
                _tmp['id_ref'] = _idRef;
            }
            /* pastikan harga jual >= harga regional */
            if(status == 'N'){
                _hargaReg = parse_number(_tr.find('td.harga_reg').text(),'.',',');                               
                if(_nilaiHarga < _hargaReg){                    
                    _error++;
                    _pesan = 'Harga jual yang diajukan tidak dapat kurang dari harga jual regional';
                }
                _tmp['harga_reg'] = _hargaReg;
            }
            if(!_error){
                _data.push(_tmp);            
            }            
        });             
      }else{
        _url = 'sales_order/pengajuan_harga/approval';
        if(status == 'R1' || status == 'A'){
          _message = 'Apakah anda yakin melakukan persetujuan pengajuan harga?';
        }else{
          _message = 'Mohon Mengisi Keterangan Reject <b>(min. 10 karakter)</b>' +
                    '<textarea onkeyup="pengajuanHarga.check_keterangan(this)" '+
                    'class="bootbox-input bootbox-input-textarea form-control" id="keterangan_reject"></textarea>';
        }
        
        var tbody = $('#main_tbody');
        $(tbody).find('.check_hrg:checked').each(function (i, v) {
            _data.push({
                'no_pengajuan_harga' : $(v).closest('tr').data('no_pengajuan_harga')
                , 'no_urut' : $(v).closest('tr').data('no_urut')
                , 'kode_farm' : $(v).closest('tr').find('td.tgl_pengajuan').data('kode_farm')
            });
        });
      }

      if(_error){
        bootbox.alert(_pesan);
        return;
      } 
      
      if(empty(_data)){
        bootbox.alert('Tidak ada data yang akan disimpan ');
        return;
      }


      if(!in_array(status,['RJ','RJV'])){
        var box2 = bootbox.confirm({
            message: _message,
            buttons: {
                confirm: {
                    label: 'Ya',
                    className: 'btn-primary',
                },
                cancel: {
                    label: 'Tidak',
                    className: 'btn-default'
                }

            },        
            callback: function (result) {
                if(result){                                          
                        var url = _url;
                        $.post(url,{ data : _data, nextStatus : status },function(result){                            

                            if(result.status == 1){
                              toastr.success(result.message,'Berhasil');
                            }else{
                              toastr.error(result.message,'Gagal');
                            }
                            $('#main_content').load('sales_order/pengajuan_harga');
                        },'json');                  
                }
            }
        });
      }else{
        var box2 = bootbox.confirm({
            message: _message,
            buttons: {
                'cancel': {
                    label: 'Tidak',
                    className: 'btn-default hide'
                },
                'confirm': {
                    label: 'Ya',
                    className: 'btn btn-primary btn-keterangan-reject disabled'
                }
            },
            callback: function (result) {
               // console.log(result);
                var _keterangan = $('#keterangan_reject').val();
                if(result){
                //    promises.promise().done(function(results) {

                        $.post(_url,{ data : _data, nextStatus : status, keterangan_reject : _keterangan},function(result){                          

                          if(result.status == 1){
                            toastr.success(result.message,'Berhasil');
                          }else{
                            toastr.error(result.message,'Gagal');
                          }
                          $('#main_content').load('sales_order/pengajuan_harga');
                        },'json');
                //    });
                }
            }
        });
      }
    },

    check_button:function(elm) {
      var tbody = $('#main_tbody');
      var tr = elm.closest('tr');
      var hrg_length = $(tbody).find('.check_hrg:checked').length;      
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
  setEstimasiJml: function(elm){
    var _val = $(elm).val();
    var _td = $(elm).closest('td');
    var _tbody = _td.closest('tbody');
    var _inputTr = _tbody.find('tr.entry_pengajuan');
    var _tdJml, _kodeBarang, _jmlBarang = 0;
    var _userlevel = $(elm).data('userlevel');
    // pengajuanlama
    if(empty(_val)){
        if(_inputTr.length){
            _inputTr.each(function(){
                _jmlBarang = 0;
                _tdJml = $(this).find('td.estimasi_jumlah');                                   
                _tdJml.text(number_format(_jmlBarang,0,',','.'));             
                $(elm).closest('tr').find('td.ref').text('');
            });
        }
    }else{
        var _estimasi = $(elm).find('option:selected').data('estimasi');
        var _pengajuanlama = $(elm).find('option:selected').data('pengajuanlama');
        var _hargaLama = {}, _tr, _hargaRegional = {};        
        if(in_array(_pengajuanlama.status,['RJ','RJV'])){
            _hargaLama = this.getHargaLama(_pengajuanlama.no_pengajuan_harga);            
        }else{
            /** cari harga regionalnya dulu */
            if(_userlevel == 'KDLOG'){
                _hargaRegional = this.getHargaRegional(_val);  
                if(empty(_hargaRegional)){
                    bootbox.alert('Belum ada pengajuan harga regional dari farm');
                    $(elm).find('option:first').prop('selected',1);
                    $(elm).trigger('change');
                    return;
                }              
            }   
        }
        if(_inputTr.length){             
            _inputTr.each(function(){
                _jmlBarang = 0;
                _tdJml = $(this).find('td.estimasi_jumlah');
                _tr = $(this).closest('tr');
                _kodeBarang = _tdJml.data('kodebarang');
                if(_estimasi[_kodeBarang] != undefined){
                    _jmlBarang = _estimasi[_kodeBarang];
                }
                if(_hargaRegional[_kodeBarang] != undefined){
                    _tr.find('td.harga_reg').find('label').text(number_format(_hargaRegional[_kodeBarang]['harga_reg'],0,',','.'));
                    _td.prev().text(_hargaRegional[_kodeBarang]['no_pengajuan_harga']);
                }
                if(_hargaLama[_kodeBarang] != undefined){
                    _tr.find('input[name=harga]').val(number_format(_hargaLama[_kodeBarang]['harga_jual'],0,',','.'));
                    _tr.find('td.harga_reg').find('label').text(number_format(_hargaLama[_kodeBarang]['harga_reg'],0,',','.'));
                }else{
                    _tr.find('input[name=harga]').val(0);
                }   
                _tdJml.text(number_format(_jmlBarang,0,',','.'));             
            });
        }   
        if(_userlevel == 'KF'){
            var _namaFarm = $(elm).find('option:last').text();
            $(elm).hide();
            $('<label>'+_namaFarm+'</label>').insertAfter($(elm));  
        }  
         
    }
  },
  getHargaRegional: function(_kodeFarm){
    if(this._hargaRegional[_kodeFarm] == undefined){
        $.ajax({
            url : 'sales_order/pengajuan_harga/hargaRegional',
            type : 'get',
            data : {kode_farm : _kodeFarm},
            dataType : 'json',
            async : false,
            cache : false,
            success : function(data){
                if(data.status){
                    pengajuanHarga._hargaRegional[_kodeFarm] = data.content;
                }else{
                    pengajuanHarga._hargaRegional[_kodeFarm] = null;
                }                
            }
        });    
    }
    return this._hargaRegional[_kodeFarm];
  },  
  check_keterangan:function(elm) {    
    if($(elm).val().length >= 10){
        $('.btn-keterangan-reject').removeClass('disabled');
    }else {
        $('.btn-keterangan-reject').addClass('disabled');
    }
  }

};
$(function(){
    $('input[name=harga_new],input[name=harga],input[name=harga_reg]').change(function(){
        $('.simpanBtn').prop('disabled',0);
    });
    $('input.number').priceFormat(pengajuanHarga._formatNumber['integer']);    
    $('input[name=tanggal_awal]').datepicker({
     //   beforeShowDay: function(date){ var y = date.getDay(); return [y == 5, ''] },
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
                var _tglPengajuan = Config._tanggalDb(date,' ','-');                
                var _url = 'sales_order/pengajuan_harga/daftarPengajuan';
                $.get(_url,{tgl_pengajuan : _tglPengajuan},function(data){
                    $('#div_list_permintaan').html(data);
                },'html');                
			}
		},				
	});
})

