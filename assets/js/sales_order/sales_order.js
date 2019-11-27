'use strict';

var salesOrder = {
	_kodeFarm : null,
	_kodeSiklus : null,
	_statusAddNewSO : false,
	_loop : 0,
	_barang : null,
	_harga : null,
	_no_so : null,
	_pelangganTags : new Array(),
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
	_selectPelanggan : {
        'kode_pelanggan'	: '',
        'nama_pelanggan'	: '',
        'alamat'			: '',
        'kota'				: '',
        'no_telp'			: '',
        'term_pembayaran'	: '',
    },	

	rowOnClick : function(elm){
		salesOrder._no_so = $(elm).data('no_so');
		$(elm).siblings().css("font-weight","normal");
		$(elm).css("font-weight","bold");		
		$('.btn').removeAttr('disabled');		
	},

	showDetail: function(elm){
		var _url = 'sales_order/sales_order/detail_so_view';
		$.get(_url,{no_so : $(elm).data('no_so')},function(html){
			$('#div_list_detail').html(html);
		},'html');
		
	},

	addNewSO : function(){
		var url = 'sales_order/sales_order/addNewROwSO';
		$.post(url,{ kode_farm : salesOrder._kodeFarm, kode_siklus : salesOrder._kodeSiklus },function(data){
			if(data.status == 1){
				$('#headerTable tbody>tr:last').remove();
				$('#headerTable tbody').append(data.content);
				salesOrder.setInputPelanggan();
				salesOrder._barang = data.barang;
				salesOrder._tempBarang = data.barang;
				salesOrder._harga = data.harga;
				$('#headerTable tbody input.telepon').priceFormat(salesOrder._formatNumber['telpon']);
				$('#headerTable tbody input[name=term_pembayaran]').priceFormat(salesOrder._formatNumber['integer']);
				$('#headerTable tbody input').not('input[name=nama_pelanggan]').change(function(){
					salesOrder.showDetailSO(this);
				});
				$('#btn1').hide();
				$('#btn2').show();
				var url = 'sales_order/sales_order/getListDetail/'+salesOrder._kodeFarm+'/'+salesOrder._kodeSiklus;
				$.post(url,{},function(data2){
					if(data2.status == 1){
						$('#div_list_detail').html(data2.content);
						$('#div_list_detail').addClass('hide');
					}else{
						toastr.error('Terjadi kesalahan.','Gagal');
					}
				},'json');
			}else{
				toastr.error(data.message,'Gagal');
			}
		},'json');		
		
	},
	showDetailSO :function(elm){
		var _tr = $(elm).closest('tr');				
		var _show = 1;
		_tr.find('input').each(function(){
			if(empty($.trim($(this).val()))){				
				_show = 0;
			}
		});
		
		if(_show){
			$('#div_list_detail').removeClass('hide');
		}
	},
	setInputPelanggan : function(){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : 'sales_order/sales_order/getPelanggan'
		})
		.done(function(data){
			for(var i=0;i<data.length;i++){
				var obj = data[i];
				//kode_pelanggan, nama_pelanggan, alamat, kota, no_telp, term_pembayaran
				if(obj.id != salesOrder._selectPelanggan.kode_pelanggan){
					var valueToPush = new Array();
					valueToPush[0] = data[i].id;
					valueToPush[1] = data[i].name;
					valueToPush[2] = data[i].alamat;
					valueToPush[3] = data[i].no_telp;
					salesOrder._pelangganTags.push(valueToPush);
				}
			}

			var $input = $('input[name=nama_pelanggan]');
			$input.typeahead({source:data,
						autoSelect: true});
			$input.change(function() {
				var current = $input.typeahead("getActive");
				if (current) {					
					// Some item from your model is active!
					if (current.name == $input.val()) {
						salesOrder._selectPelanggan.kode_pelanggan = current.id;
						salesOrder._selectPelanggan.nama_pelanggan = current.name;
						salesOrder._selectPelanggan.alamat = current.alamat;
						salesOrder._selectPelanggan.kota = current.kota;
						salesOrder._selectPelanggan.no_telp = current.no_telp;
						salesOrder._selectPelanggan.term_pembayaran = current.term_pembayaran;
						
			            var tr = $input.closest('tr');
			            $(tr).find('.alamat>input').val(salesOrder._selectPelanggan.alamat);
			            $(tr).find('.kota>input').val(salesOrder._selectPelanggan.kota);
			            $(tr).find('.no_telp>input').val(salesOrder._selectPelanggan.no_telp);
			            $(tr).find('.term_pembayaran>input').val(salesOrder._selectPelanggan.term_pembayaran);
						$(tr).find('.nama_farm input[name=kode_pelanggan]').val(current.id);			            
						
						$(tr).find('.term_pembayaran>input').trigger('change');						
					}
				}
			});
		});
		return true;
	},

	addProduct : function(kodeFarm){
		var url = 'sales_order/sales_order/addProduct';
		var _maxRow, _error = 0;
		/* jika detailTable tbody masih kosong, maka gak perlu cek _maxRow */
		_maxRow = $('#detailTable tbody tr:first select>option').length;
		if(_maxRow){
			var _jmlBaris = $('#detailTable tbody tr').length;
			if(_jmlBaris >= (_maxRow - 1)){
				_error++;
			}
		}
		if(!_error){
			$.post(url,{kode_farm : salesOrder._kodeFarm, kode_siklus : salesOrder._kodeSiklus},function(data){
				if(data.status == 1){
					
					$('#detailTable tbody').append(data.content);
					$('#detailTable tbody>tr:last').find('input[name=jumlah]').priceFormat(salesOrder._formatNumber['integer']);
				}else{
					toastr.error('Terjadi kesalahan.','Gagal');
				}
			},'json');
		}else{
			toastr.error('Sudah tidak bisa menambah jenis barang','Gagal');
		}
		
	},

	openLaporanStokGlangsingPage : function(){
		$('#main_content').load('sales_order/laporan_stok_glangsing/index/');
	},

	kembali: function(){
		$('#main_content').load('sales_order/sales_order/index/'+this._kodeFarm+'/'+this._kodeSiklus);
	},

	onchangeProduct : function(elm){
		var kodeBarang = $(elm).find('option:selected').val();
		var tr = $(elm).closest('tr');
		var row_id = $(tr).data('row_id');
		var tbody = $(elm).closest('tbody');
		var statusValid = true;

		$(tr).find('.satuan').text('');
		$(tr).find('.harga').text('');
		$(tr).find('.harga_total').text('');
		$(tr).find('input[name=jumlah]').val('0');
		jQuery.each($('#detailTable>tbody>tr'), function( k, v) {
			if($(v).data('row_id') != row_id){
				if($(v).find('select>option:selected').val() == kodeBarang){					
					$(elm).attr('style','border:1px solid red');
					$(tr).find('input[name=jumlah]').val('0');
					$(elm).val('');
					statusValid = false;
				}								
			}
		});

		if(statusValid){
			for(var i in salesOrder._barang) {				
				if(salesOrder._barang[i]['kode_barang'] == kodeBarang){					
					$(tr).find('input[name=jumlah]').val(salesOrder._barang[i]['jml_stok']).blur();					
				}
			}
			$(elm).removeAttr('style');
		}
		
		salesOrder.onchangeJumlah($(tr).find('input[name=jumlah]'));	
	},

	onchangeHarga : function(elm){		
		var tr = $(elm).closest('tr');
		var kodeBarang = $(tr).find('select>option:selected').val();
		var jumlah = parse_number($(tr).find('input[name=jumlah]').val(),'.',',');
		var hargaTotal = 0;

		for(var i=0; i< salesOrder._harga.length; i++) {
			if(salesOrder._harga[i]['kode_barang'] == kodeBarang){
				var harga = salesOrder._harga[i]['harga_jual'];
				var satuan = salesOrder._harga[i]['satuan'];
				$(tr).find('.satuan').text(satuan);
				$(tr).find('.harga').text(number_format(harga,0,',','.'));
				$(tr).find('.harga_total').text(number_format(jumlah * harga,0,',','.'));
			}
		}
		jQuery.each($('#detailTable>tbody>tr'), function( k, v) {
			if(!empty($(v).find('.harga_total').text())){
				hargaTotal += parse_number($(v).find('.harga_total').text(),'.',',');
			}			
		});
		
		$('.new').find('td.harga_total').text(number_format(hargaTotal,0,',','.'));
	},

	onchangeJumlah : function(elm){		
		var jumlah = parse_number($(elm).val(),'.',',');
		var tr = $(elm).closest('tr'); 
		var kodeBarang = $(tr).find('select>option:selected').val();
		var jumlahTotal = 0;

		for(var i in salesOrder._barang) {
			if(salesOrder._barang[i]['kode_barang'] == kodeBarang){
				if(jumlah > parse_number(salesOrder._barang[i]['jml_stok'],'.',',')){
					$(tr).find('input[name=jumlah]').val(number_format(salesOrder._barang[i]['jml_stok'],0,',','.'));
					toastr.warning('Jumlah Max yang dimiliki hanya : '+salesOrder._barang[i]['jml_stok']+' sak');
				}				
			}
		}
		jQuery.each($('#detailTable>tbody>tr'), function( k, v) {
			jumlahTotal += parse_number($(v).find('input[name=jumlah]').val(),'.',',');
		});
		$('.new').find('td.jumlah_total').text(number_format(jumlahTotal,0,',','.'));
		
		salesOrder.onchangeHarga(elm);
	},

	deleteRow : function(elm){
		var tr = $(elm).closest('tr');
		var _sibling = tr.siblings().eq(0).find('select');
		$(tr).remove();
		salesOrder.onchangeJumlah(_sibling);		 
	},

	clickSubmit : function(){
		var _error = 0, _message = [], _kode_barang, _jml_barang;
		var header = {
			kode_farm : salesOrder._kodeFarm
			, kode_siklus : salesOrder._kodeSiklus
			, nama_pelanggan : $('input[name=nama_pelanggan]').val()
			, alamat : $('input[name=alamat]').val()
			, no_telp : $('input[name=no_telp]').val()
			, term_pembayaran : $('input[name=term_pembayaran]').val()
			, jumlah_total : parse_number($('tr.new').find('.jumlah_total').text(),'.',',')
			, harga_total : parse_number($('tr.new').find('.harga_total').text(),'.',',')
		};
		var detail = [], _tmp = {};
		var loop = 1;
		/* pastikan no telpon harus diisi */
		if(empty(header['no_telp'])){
			_error++;
			_message.push('Nomer telepon harus diisi');
			$('input[name=no_telp]').focus();
		}
		if(empty(header['nama_pelanggan'])){
			_error++;
			_message.push('Nama pelanggan harus diisi');
			$('input[name=nama_pelanggan]').focus();
		}
		/* pastikan sudah isi detail barang yang dijual */
		jQuery.each($('#detailTable>tbody>tr'), function( k, v) {
			_kode_barang = $(v).find('select>option:selected').val();
			_jml_barang = parse_number($(v).find('input[name=jumlah]').val(),'.',',');
			if(empty(_kode_barang)){
				_error++;
				_message.push('Masih ada barang yang belum diisi');
			}
			if(_jml_barang <= 0){
				_error++;
				_message.push('Jumlah barang harus lebih besar dari 0');
			}
			_tmp = { 
				index : loop
				, kode_barang : _kode_barang
				, jumlah : _jml_barang
				, harga_jual : parse_number($(v).find('td.harga').text(),'.',',')
				, harga_total : parse_number($(v).find('td.harga_total').text(),'.',',')
			};
			detail.push(_tmp);
			loop++;
		});		
		if(empty(detail)){
			_error++;
			_message.push('Detail barang harus diisi');
		}
		if(!empty(_message)){
			toastr.error(_message.join(' \n'),'Error');
		}
		if(!_error){	
			bootbox.confirm({
				message: 'Apakah Anda yakin melakukan penyimpanan SO ?',
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
						$.ajax({
							type : "POST",
							url : "sales_order/sales_order/simpan",
							data : {
								header : header
								, detail : detail
							},
							dataType : 'json',
							success : function(data) {
								if(data.status == '1'){						
									bootbox.alert(data.message,function(){
										salesOrder.kembali();
									});																
								}else{
									toastr.error(data.message,'Gagal');
								}
							}
						});
					}					
				}
			});				
			
		}
		
	},
	cetakDO: function(){
		if(!empty(salesOrder._no_so)){
			var _url = 'sales_order/sales_order/cetakDO';
			$.redirect(_url,{no_so : salesOrder._no_so},'POST','_blank');
		}
	},
	cetakSO: function(){
		if(!empty(salesOrder._no_so)){
			var _url = 'sales_order/sales_order/cetakSO';
			$.redirect(_url,{no_so : salesOrder._no_so},'POST','_blank');
		}
	},
	cancelSO : function(){
		var _statusSO = $('#main_tbody').find('tr[data-no_so='+this._no_so+']').data('status_order');
		if(_statusSO == 'V'){
			bootbox.alert('Status SO sudah dibatalkan');
			return;
		}
		if(!empty(this._no_so)){
			bootbox.confirm({
				title : 'Konfirmasi Pembatalan SO',
				message: 'Apakah Anda akan membatalkan SO ?',
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
						salesOrder.gantiSO();
					}					
				}
			});					
		}
	},
	gantiSO : function(){
		bootbox.confirm({
			title : 'Konfirmasi Ganti SO',
			message: 'Apakah Anda akan membuat SO pengganti ?',
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
					salesOrder.batalSO(1)
				}else{
					salesOrder.batalSO(0);
				}					
			}
		});
	},
	batalSO : function(renew){
		$.ajax({
			type : "POST",
			url : "sales_order/sales_order/batalSO",
			data : {
				renew : renew
				,no_so : salesOrder._no_so
			},
			dataType : 'json',
			success : function(data) {
				if(data.status == '1'){						
					bootbox.alert(data.message,function(){
						salesOrder.kembali();
					});	
				}else{
					bootbox.alert(data.message);
				}
			}
		});
	}
};

$(function(){
	'use strict';
	salesOrder._kodeFarm = $('#kode_farm').val();
	salesOrder._kodeSiklus = $('#kode_siklus').val();
	$('#btn2').hide();
	$('input.number').priceFormat(salesOrder._formatNumber['telpon']);

}());