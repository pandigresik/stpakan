'use strict';

var realisasiPenjualan = {
	_no_sj : null,
	rowOnClick : function(elm){		
		$(elm).siblings().removeClass('terpilih');
		$(elm).addClass('terpilih');
		var _no_sj = $(elm).data('no_sj');
		this.detailDO(_no_sj);
		this._no_sj = _no_sj;
		if(empty($(elm).find('td.no_sj').text())){
			$('.simpansj').prop('disabled',0);		
			$('.cetaksj').prop('disabled',1);		
		}else{
			$('.cetaksj').prop('disabled',0);	
			$('.simpansj').prop('disabled',1);			
		}
		
	},
	listDO : function(){
		var _url = 'sales_order/realisasi_penjualan/listDO';
		var _data = {
			startDate: Config._tanggalDb($('input[name=startDate]').val(),' ','-'),
			endDate: Config._tanggalDb($('input[name=endDate]').val(),' ','-'),
			status: $('select[name=status_do]').val()
		};
		$.get(_url,_data,function(html){
			$('#div_list_laporan').html(html);
			$('#div_detail_do').html('');
		},'html');
	},
	detailDO : function(_no_sj){
		var _url = 'sales_order/realisasi_penjualan/detailSJ';
		var _data = {
			no_sj : _no_sj
		};
		$.get(_url,_data,function(html){
			$('#div_detail_do').html(html);
		},'html');
	},
	simpan : function(){
		var no_sj = this._no_sj;		
		var _tr = $('#main_tbody').find('tr.terpilih');
		if(!empty(_tr.find('td.no_sj').text())){
			toastr.error('DO sudah pernah disimpan','Error');
			exit();
		}
		if(empty(no_sj)){
			toastr.error('Belum ada DO yang dipilih','Error');
		}else{			
			var _listBarang = [];
			var _totalBarang = 0;
			$('#div_detail_do').find('table>tbody>tr').each(function(){
				_listBarang.push($(this).find('td.nama_barang').text()+' sebesar <u><strong>'+$(this).find('td.jumlah').text()+'</strong></u> sak');
				_totalBarang += parse_number($(this).find('td.jumlah').text(),'.',',');
			})

			bootbox.confirm({
				title: 'Konfirmasi Pengiriman',
				message: '<span class="text-center">Apakah anda yakin melakukan penyimpanan pengiriman barang dengan realisasi dibawah ini ? <br />'+_listBarang.join(' , ')+'</span>',
				buttons: {
					'cancel': {
						label: 'Tidak',
						className: 'btn-default'
					},
					'confirm': {
						label: 'Ya',
						className: 'btn-danger'
					}
				},
				callback : function(result){
					if(result){
						$.ajax({
							type : 'post',
							dataType : 'json',
							data : {no_sj : no_sj },
							url : 'sales_order/realisasi_penjualan/simpan',
							beforeSend : function(){
								
							},
							success : function(data){
								if(data.status){																
									bootbox.alert(data.message);
									/* update nomer sj dan jumlahnya  */
									var _tr = $('#main_tbody').find('tr.terpilih');
									_tr.find('td.no_sj').text(no_sj);
									_tr.find('td.jml_sak').text(_totalBarang);
									$('.cetaksj').prop('disabled',0);	
									$('.simpansj').prop('disabled',1);	
								}
								else{
									toastr.error(data.message);									
								}
							},
						});
					}
				}
			});
		}
	},
	cetakSJ: function(){
		if(!empty(realisasiPenjualan._no_sj)){
			var _url = 'sales_order/realisasi_penjualan/cetakSJ';
			$.redirect(_url,{no_sj : realisasiPenjualan._no_sj},'POST','_blank');
		}
	},
	
	goto: function(elm){
		var _url = $(elm).data('url');
		$('#main_content').load(_url);
	}

};

$(function(){
	'use strict';
	realisasiPenjualan.listDO();
	$('input[name=startDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
				realisasiPenjualan.listDO();
			}
		},
		minDate : Config._tglServer,
		defaultDate: Config._tglServer
	});
	$('input[name=endDate]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
				realisasiPenjualan.listDO();
			}
		},
		maxDate : Config._tglServer,
		defaultDate: Config._tglServer
	});	
	$('select[name=status_do]').change(function(){
		realisasiPenjualan.listDO();
	});	
}());