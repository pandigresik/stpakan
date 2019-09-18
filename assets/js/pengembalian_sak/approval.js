var Approval = {
			add_datepicker : function(elm,options){
				elm.datepicker(options);
			},
			transaksi : function(elm,target){
				var tgl_server = $('#tanggal_server').data('tanggal_server');
				var no_pengembalian = $(elm).data('no_pengembalian') || null;
				var status = $(elm).data('status') || null;
			
				$.ajax({
					type : 'post',
					data : {no_pengembalian : no_pengembalian, status : status, tgl_server : tgl_server},
					url : 'pengembalian_sak/pengembalian/transaksi',
					dataType : 'html',
					async : false,
					success : function(data){
						$('#transaksi').html(data);
					},
				}).done(function(){
					if(empty(no_pengembalian)){
						
						}   
					});
				
				$(target).click();
			},
			
			list_cari : function(elm){
				var _form = $(elm).closest('form');
				var _tgl = _form.find('input[name$=Date]');
				var _status = $('input:checkbox[name=filter_retur]').is(':checked') ? 1 : 0;
				var _tanggal = {};
				var _error = 0;
				_tanggal['operand'] = null;
				var _jmltgl = 0;
				if(_tgl.length){
					_tgl.each(function(){
						if(!empty($(this).val())){
							_tanggal[$(this).attr('name')] = Config._tanggalDb($(this).val(),' ' ,'-' );
							_jmltgl++;
						}
						
					});
					if(_jmltgl == 2){
						_tanggal['operand'] = 'between';
					}
					else {
						if(_tanggal['startDate'] != undefined){
							_tanggal['operand'] = '>=';
						}
						else if(_tanggal['endDate'] != undefined){
							_tanggal['operand'] = '<=';
						}
					}
					
				}
				
				if(!_jmltgl && !_status){
					_error++;
					toastr.error('Minimal satu tanggal harus diisi');
				}
				if(!_error){
					$.ajax({
						url : 'pengembalian_sak/approval/list_farm_retur',
						type : 'post',
						data : {tanggal : _tanggal, status : _status},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('#list_farm_retur').html(' Silakan tunggu ....');
						},
						success : function(data){
							$('#list_farm_retur').html(data);
						}
					});
				}
				
			},
			filter_content_farm : function(elm){
				var _panel_body = $(elm).closest('div.panel-body');
				var _list_group = _panel_body.find('ul.list-group');
				var _content = $(elm).val();
			//	var _target = $(elm).attr('name');
				
				_list_group.find('li.list-group-item:not(:contains('+_content.toUpperCase()+'))').hide();
				_list_group.find('li.list-group-item:contains('+_content.toUpperCase()+')').show();
				
			},
			
			filter_content : function(elm){
				var _table = $(elm).closest('table');
				var _tbody = _table.find('tbody');
				var _content = $(elm).val();
				var _target = $(elm).attr('name');
				
				_tbody.find('td.'+_target+':contains('+_content.toUpperCase()+')').parent().show();
				_tbody.find('td.'+_target+':not(:contains('+_content.toUpperCase()+'))').parent().hide();
			},
			showlistretur : function(elm){
				var _kode_strain = $(elm).data('kode_strain');
				var _kode_farm = $(elm).data('kode_farm');
				var _kode_siklus = $(elm).data('kode_siklus');
				var _nama_farm = $(elm).find('span').text().toUpperCase();
				var _jml_retur = $(elm).data('jml_retur');
				var _panel_body = $(elm).closest('div.panel-body');
				
				var _status = _panel_body.data('status');
				var _startdate = _panel_body.data('startdate');
				var _enddate = _panel_body.data('enddate');
				var _tanggal = {'startDate': _startdate,'endDate' : _enddate};
				if(!empty(_startdate) && !empty(_enddate)){
					_tanggal['operand'] = 'between';
				}
				else {
					if(!empty(_startdate)){
						_tanggal['operand'] = '>=';
					}
					else if(!empty(_enddate)){
						_tanggal['operand'] = '<=';
					}
				}
				
				if(_jml_retur > 0){
					var _h_retur = $('#header_retur');
					_h_retur.siblings('.panel-heading').find('span.nama_farm').text(_nama_farm+' ( '+_kode_strain+' )');
					$.ajax({
						data : {kode_farm : _kode_farm, kode_siklus : _kode_siklus, status : _status, tanggal : _tanggal},
						type : 'post',
						url : 'pengembalian_sak/approval/view_retur_sak',
						success : function(data){
							var _d_retur = $('#detail_retur');
							/* hapus data pada detail retur */
							_d_retur.html('');
							_h_retur.html(data);
						},
						dataType : 'html'
					});
				}
				else{
					toastr.error('Tidak ada nomer retur pada farm yang dipilih');
				}
			},	
			showdetailretur : function(elm){				
				var _d_retur = $('#detail_retur');
				var _no_retur = $(elm).data('no_retur');
				var _keputusan = $(elm).data('keputusan');
				var _aktif = $(elm).data('aktif');
				var _reviewkadept = $(elm).data('reviewkadept') || null;
				if(empty(_keputusan)){
					/* jika aktif = 0, maka ubah menjadi 1 keputusan */
					_keputusan = (_aktif) ? '' : '1'; 
				}
				$(elm).addClass('terpilih').siblings().removeClass('terpilih');
				_d_retur.siblings('.panel-heading').find('span.no_retur').text(_no_retur);
				$.ajax({
					data : {no_pengembalian : _no_retur, keputusan : _keputusan, reviewkadept : _reviewkadept },
					type : 'post',
					url : 'pengembalian_sak/approval/detail_retur_sak',
					success : function(data){
						_d_retur.html(data);
					},
					dataType : 'html'
				});
			},
			approveretursak : function(elm,keputusan){
				var header_retur = $('#header_retur');
				var tr_retur = header_retur.find('table>tbody>tr.terpilih');
				var id_retur = tr_retur.data('id_retur');
				$.ajax({
					data : {id_retur : id_retur, keputusan : keputusan},
					type : 'post',
					url : 'pengembalian_sak/approval/approve_retur_sak',
					success : function(data){
						if(data.status){
							var pengiriman_str = {'A' : 'NORMAL', 'R' : 'KURANGI'};
							tr_retur.find('td.waktu').text(data.content);
							if(keputusan == 'R'){
								tr_retur.find('td.waktu').addClass('abang');
							}
							tr_retur.find('td.pengiriman').text(pengiriman_str[keputusan]);
							/* hapus tombolnya */
							$(elm).parent().remove();
						}
					},
					dataType : 'json'
				});
			},
			cancelreview : function(elm){
				$('#detail_retur input[name=keterangan]').val('');
				
			},
			reviewretursak : function(elm){
				/* periksa apakah keterangan sudah diisi semua */
				var _error = 0;
				var _data = [];
				$('#detail_retur input[name=keterangan]').each(function(){
					if(empty($.trim($(this).val()))){
						_error++;
					}
					var _tr = $(this).closest('tr');
					_data.push({retur_sak_kosong_item_pakan : _tr.data('retur_sak_kosong_item_pakan'), keterangan : $(this).val()});
					if(!_error){
						$.ajax({
							data : {data : _data},
							type : 'post',
							url : 'pengembalian_sak/approval/review_retur_sak',
							success : function(data){
								if(data.status){
									var header_retur = $('#header_retur');
									var tr_retur = header_retur.find('table>tbody>tr.terpilih');
									tr_retur.find('td.waktureview').text(data.content);
									/* hapus tombolnya */
									$(elm).parent().remove();
								}
							},
							dataType : 'json'
						});
					}
					else{
						toastr.error('Keterangan harus diisi semua');
					}
					
				});
			}
	};
$(function(){
	'use strict';
	var tgl_server = $('#tanggal_server').data('tanggal_server');
	Config._setTglServer(tgl_server);
	Approval.add_datepicker($('input[name=startDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		},
		maxDate : Config._tglServer
	});
	Approval.add_datepicker($('input[name=endDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		},
		maxDate : Config._tglServer
	});
	
	$('input:checkbox[name=filter_retur]').click(function(){
		var _status = $(this).is(':checked');
		/* jika true maka disable datepicker, jik false enable */
		if(_status){
			$('input[name$=Date]').datepicker('option','disabled',1);
		}
		else{
			$('input[name$=Date]').datepicker('option','disabled',0);
		}
		
	});
	$('input[name$=Date]').datepicker('option','disabled',1);
	$('span#btn_cari').click();
}());