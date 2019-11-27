var KonfigurasiPakan = {
		updateStatus : function(){
			var _kb = [];  
			$('#KonfigurasiPakanbarang').find('input:checked').each(function(){
				_kb.push($(this).data('kode_barang'));
			});
			if(!empty(_kb)){
				bootbox.confirm({
				    title: 'Konfirmasi Perubahan',
				    message: '"Apakah Anda yakin akan Menyimpan data Konfigurasi Pakan Ternak ini?',
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
								data : {kodeBarang : _kb},
								url : 'master/barang/updateStatusBarang',
								dataType : 'json',
								success : function(data){
									if(data.status){
										/* hapus yang tercentang */
										$('#KonfigurasiPakanbarang').find('input:checked').closest('tr').remove();
										toastr.success('Penyimpanan Konfigurasi Pakan Ternak berhasil dilakukan');
									}
									else{
										toastr.error('Penyimpanan Konfigurasi Pakan Ternak gagal dilakukan');
									}
								},
								
							});
				    	}
				    },
				});
				
			}
			else{
				toastr.warning('Belum ada pakan yang dipilih');
			}
		},
		listBarang : function(paramCari){
			$.ajax({
				type : 'post',
				data : {paramCari : paramCari},
				url : 'master/barang/listKonfigurasiBarang',
				dataType : 'html',
				success : function(data){
					$('div.panel-body').html(data);
				},
			}).done(function(){
				$('div.panel-body table').scrollabletable({'scroll_horizontal' : 0,'padding_right' : 15,'max_height_scrollable': 400});
			});
		},
		cariBarang : function(elm){
			var _tr = $(elm).closest('tr');
			var paramCari = {};
			_tr.find('select.search,input.search').each(function(){
				if(!empty($(this).val())){
					paramCari[$(this).attr('name')] = $(this).val();
				}
				
			});
			this.listBarang(paramCari);
		}
};

$(function(){
	'use strict';
	KonfigurasiPakan.listBarang();
});
