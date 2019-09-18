(function(){
	'use strict';

	/* jika pilihan farm bukan kosong maka load farm yang dipilih */
	$('#filter_farm').click(function(){
		var level_user = $('#popup_gantipassword').data('level');
		var farm_user = $('#popup_gantipassword').data('farm');
		var kode_farm = level_user == 'KF' ? farm_user : 'all';
		Forecast.modal_filter_farm('checkedAll',kode_farm,function(_form){
			var _filter = {};
			$('div.date input.kebutuhan').each(function(){
				_filter[$(this).attr('name')] = Config._tanggalDb($(this).val(),' ', '-');
			});
			/* dapatkan semua farm berdasarkan parameter yang diberikan */
			var _farm_terpilih = [];
			_form.find(':checked').each(function(){
				_farm_terpilih.push($(this).val());
			});
			if(!empty(_farm_terpilih)){
				$.ajax({
					type : 'post',
					url : 'forecast/forecast/kebutuhan_pakan_ppic',
					data : {tanggal : _filter, farm  : _farm_terpilih},
					success : function(data){
						$('#div_kebutuhan_pakan_ppic').html(data);
					},
				}).done(function(){
					var _max_width = $('#div_kebutuhan_pakan_ppic').closest('div.panel-body').innerWidth();
					$('#div_kebutuhan_pakan_ppic').find('table:first').scrollabletable({
						max_width : (_max_width * .95)
					});

						var _wrap = $('#div_kebutuhan_pakan_ppic');
						var _thead_table = _wrap.find('table:first thead');
						_wrap.find('[id$="div_header_virtual_kj"] table thead').width(_thead_table.width());


				});
			}
			else{
				toastr.warning('Tidak ada farm yang dipilih');
			}
		});
	});

	$('#checkbox_belum_konfirmasi,#checkbox_sudah_konfirmasi').click(function(){
		var _jmlchecked = $(':checked').length;

		if($(this).is(':checked')){
			$('div.tanggal-chick-in input').attr('disabled', true);
			$(this).val("0");
		}
		else{
			if(!_jmlchecked){
				$('div.tanggal-chick-in input').removeAttr('disabled');
			}
			$(this).val("1");
		}
	});

	$('#filter_konfirmasi_ppic').click(function(){
		Forecast.modal_filter_farm('checkedAll','all',function(_form){
			var _filter = {};
			$('div.date input.chickin').each(function(){
				_filter[$(this).attr('name')] = Config._tanggalDb($(this).val(),' ', '-');
			});
			/* dapatkan semua farm berdasarkan parameter yang diberikan */
			var _farm_terpilih = [];
			_form.find(':checked').each(function(){
				_farm_terpilih.push($(this).val());
			});

			var _konfirmasi = $('#checkbox_belum_konfirmasi').val();
			var _sudah_konfirmasi = $('#checkbox_sudah_konfirmasi').val();
			var _grup_farm = $('#myTab li.active a').attr('data-grup-farm');
			if(!empty(_farm_terpilih)){
				$.ajax({
					type : 'post',
					url : 'forecast/forecast/data_konfirmasi_ppic',
					data : {tanggal : _filter, farm  : _farm_terpilih,sudah_konfirmasi : _sudah_konfirmasi, konfirmasi : _konfirmasi, grup_farm : _grup_farm},
					success : function(data){
						$('#div_konfirmasi_ppic').html(data);
						$('table.konfirmasi_table thead th').css('vertical-align','middle');
						$('table.konfirmasi_table tbody td').css('vertical-align','middle');
					},
				}).done(function(){

				});
			}
			else{
				toastr.warning('Silahkan pilih nama farm yang ditampilkan');
			}
		});
	});
	if($("input[name=startDate]").length){
		$("input[name=startDate]").datepicker({
		    //  defaultDate: "+1w",
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		        $( "input[name=endDate]" ).datepicker( "option", "minDate", selectedDate );
		      }
		   }).val(Config._tanggalLocal(Config._getDateStr(new Date()),'-',' '));
	}
	if($("input[name=endDate]").length){
		 $("input[name=endDate]").datepicker({
		    //  defaultDate: "+1w",
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		        $( "input[name=startDate]" ).datepicker( "option", "maxDate", selectedDate );
		    }
		  }).val(Config._tanggalLocal(Config._getDateStr(new Date()),'-',' '));
	}
	if($("input[name=startDateChickIn]:visible").length){
	 $("input[name=startDateChickIn]").datepicker({
		    //  defaultDate: "+1w",
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		        $( "input[name=endDateChickIn]" ).datepicker( "option", "minDate", selectedDate );
		      }
		   }).val(Config._tanggalLocal(Config._getDateStr(new Date()),'-',' '));
	}
	if($("input[name=endDateChickIn]:visible").length){
		 $("input[name=endDateChickIn]").datepicker({
		    //  defaultDate: "+1w",
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		        $( "input[name=startDateChickIn]" ).datepicker( "option", "maxDate", selectedDate );
		    }
		  }).val(Config._tanggalLocal(Config._getDateStr(new Date()),'-',' '));
	}

	/* cek apakah ada notif atau tidak */
	var _notif_json = $('#data-notif').text();
	var _notif;
	var _msg,_title;
//	var _title = {'belum_ada_rp' : 'Pakan Belum Memiliki Rencana Produksi', 'belum_memenuhi_pp' : 'Jumlah Permintaan Belum Terpenuhi'};
	if(_notif_json.length){
		_notif = $.parseJSON(_notif_json);
		toastr.options = {
				  "timeOut": "0",
				  "extendedTimeOut": "0",
				  "closeButton" : true,
				  "tapToDismiss" : false
				};
	for(var i in _notif){
		if(_notif[i]){
			_title = _notif[i]['title'];
			var _content = _notif[i]['message'];
			for(var y in _content){
					_msg = _content[y];
					toastr.warning(_msg,_title);
				}
			}
		}
		toastr.options = {
					"timeOut": "5000",
					"extendedTimeOut": "1000",
				};
	}
}());

clear_content();

function tutup_modal(){
	$('.bootbox').modal('hide');
}
function hide_detail(elm, data_ke){
	if($('.'+data_ke).hasClass('hide')){
		$('.'+data_ke).fadeIn('slow');
		$('.'+data_ke).removeClass('hide');
		$(elm).removeClass('glyphicon-plus').addClass('glyphicon-minus');
	}
	else{
		$('.'+data_ke).fadeOut('slow');
		$('.'+data_ke).addClass('hide');
		$(elm).removeClass('glyphicon-minus').addClass('glyphicon-plus');
	}
}
function ack(elm, data_ke, data_ke_flok){
	var _grup_farm = $('#myTab li.active a').attr('data-grup-farm');
	var _no_reg = [];
	if(_grup_farm == 'bdy'){
		$.each($('.konfirmasi_table tbody tr').find('td.'+data_ke_flok),function(){
			var no_reg = $(this).attr('data-no-reg');
			if(no_reg){
				_no_reg.push(no_reg);
			}
		});

	}
	else{
		$.each($('.konfirmasi_table tbody').find('tr.'+data_ke),function(){
			var no_reg = $(this).find('td.td_no_reg').attr('data-no-reg');
			if(no_reg){
				_no_reg.push(no_reg);
			}
		});

	}


	if(_no_reg.length > 0){
		$.ajax({
			type : 'post',
			url : 'forecast/forecast/ack_forecast',
			data : {no_reg : _no_reg},
			dataType : 'json',
			success : function(data){
				if(data.result == 1){
					toastr.success('Berhasil melakukan Konfirmasi');

					if(_grup_farm == 'bdy'){
						$(elm).parents('tr').find('td.td_no_konfirmasi').html('<span ondblclick="detail_konfirmasi_forecast(this)">'+data.no_konfirmasi+'</span>');

						$(elm).parents('tr').find('td.td_tanggal').text(data.tanggal);
						$(elm).parents('tr').find('td.td_user').text(data.nama_pegawai);
						var _ada_centang = $('#checkbox_belum_konfirmasi').length;
						if($('#checkbox_belum_konfirmasi').is(':checked') || !_ada_centang){

							$.each($('.konfirmasi_table tbody tr').find('td.'+data_ke_flok),function(){
								if($(this).length > 0){
									$(this).parent().addClass('hide');
								}
							});
							var cek = 0;
							$.each($('.konfirmasi_table tbody tr.'+data_ke).find('td.td_no_reg'),function(){
								//console.log($(this).length)
								if($(this).length > 0){
									//console.log($(this).parent());
									if(!$(this).parent().hasClass('hide')){
										cek++;
									}
								}
							});
							//console.log(cek)
							if(cek==0){
								$('tr.tr_header'+data_ke).addClass('hide');
								$('tr.tr_header'+(parseInt(data_ke)+1)).prev().addClass('hide');
							}
						}
					}
					else{
						$('tr.tr_header'+data_ke+' td.td_no_konfirmasi').html('<span ondblclick="detail_konfirmasi_forecast(this)">'+data.no_konfirmasi+'</span>');
						$('tr.tr_header'+data_ke+' td.td_user').text(data.nama_pegawai);
						$('tr.tr_header'+data_ke+' td.td_tanggal').text(data.tanggal);
						var _ada_centang = $('#checkbox_belum_konfirmasi').length;
						if($('#checkbox_belum_konfirmasi').is(':checked') || !_ada_centang){
							$('tr.tr_header'+data_ke).addClass('hide');
							$('tr.'+data_ke).addClass('hide');
							$('tr.tr_header'+(parseInt(data_ke)+1)).prev().addClass('hide');
						}
					}
				}
				else{
					toastr.error('Gagal melakukan Konfirmasi');
				}
			},
		});
	}

}
function ack_old(data_ke){
	var _no_reg = [];
	$.each($('.konfirmasi_table tbody').find('tr.'+data_ke),function(){
		var no_reg = $(this).find('td.td_no_reg').attr('data-no-reg');
		if(no_reg){
			_no_reg.push(no_reg);
		}
	});
	if(_no_reg.length > 0){
		$.ajax({
			type : 'post',
			url : 'forecast/forecast/ack_forecast',
			data : {no_reg : _no_reg},
			dataType : 'json',
			success : function(data){
				if(data.result == 1){
					toastr.success('Berhasil melakukan Konfirmasi');
					$('tr.tr_header'+data_ke+' td.td_no_konfirmasi').html('<span ondblclick="detail_konfirmasi_forecast(this)">'+data.no_konfirmasi+'</span>');
					$('tr.tr_header'+data_ke+' td.td_user').text(data.nama_pegawai);
					$('tr.tr_header'+data_ke+' td.td_tanggal').text(data.tanggal);
					var _ada_centang = $('#checkbox_belum_konfirmasi').length;
					if($('#checkbox_belum_konfirmasi').is(':checked') || !_ada_centang){
						$('tr.tr_header'+data_ke).addClass('hide');
						$('tr.'+data_ke).addClass('hide');
						$('tr.tr_header'+(parseInt(data_ke)+1)).prev().addClass('hide');
					}
				}
				else{
					toastr.error('Gagal melakukan Konfirmasi');
				}
			},
		});
	}
}
function detail_konfirmasi_forecast(elm){
	var _filter = {};
	$('div.date input.chickin').each(function(){
		_filter[$(this).attr('name')] = Config._tanggalDb($(this).val(),' ', '-');
	});
	var _farm_terpilih = [];
	_farm_terpilih.push($(elm).parents('td.td_no_konfirmasi').next().attr('data-kode-farm'));
	$.ajax({
		type : 'post',
		url : 'forecast/forecast/kebutuhan_pakan_ppic',
		data : {tanggal : _filter, farm  : _farm_terpilih},
		success : function(data){
			var no_permintaan = $(elm).text();
			var nama_farm = $(elm).parents('td.td_no_konfirmasi').next().text();
			var _message = '<div class="row">';
				_message += '<div class="col-md-4">';
				//_message += '<a target="_blank" class="link btn btn-default" href="forecast/forecast/cetak_konfirmasi_forecast?tanggal='+_filter+'&farm='+_farm_terpilih+'">Print</a>';

				_message += '<form method="post" action="forecast/forecast/cetak_konfirmasi_forecast" target="_blank">';
				//_message += '<a target="_blank" class="link btn btn-default" href="forecast/forecast/cetak_breakdown_pakan?strain='+strain+'&tipe_kandang='+tipe_kandang+'&tgl_doc_in='+tglDocIn+'">Print</a>';
				_message += '<input type="hidden" value="'+nama_farm+'" name="nama_farm">';
				_message += '<input type="hidden" value="'+no_permintaan+'" name="no_permintaan">';
				_message += '<textarea name="data_html" class="hide">'+data+'</textarea>';
				_message += '<input type="submit" value="Print" class="btn btn-default">';
				_message += '</form>';
				_message += '</div>';
				_message += '<div class="col-md-4 text-center"><span style="font-weight:bold;font-size:25px">FARM '+nama_farm+'</span><br><span style="font-weight:bold;font-size:18px">'+no_permintaan+'</span>';
				_message += '</div>';
				_message += '<div class="col-md-4 text-right">';
				_message += '<span class="link btn btn-default" onclick="tutup_modal()">Tutup</span>';
				_message += '</div>';
				_message += '</div>';
				_message += '<div id="modal_kebutuhan_pakan_ppic" class="new-line">'+data+'</div>';
			var _options = {
					title : 'Detail Konfirmasi Forecast',
					message : _message,
					className : 'very-large',
					buttons : {
						tambahTanggal : {
							label : 'Keluar',
							className : 'hide',
							callback : function(){
							}
						}
					},
				};

				bootbox.dialog(_options).bind('shown.bs.modal',function(){
					$('#modal_kebutuhan_pakan_ppic div.container').removeClass('col-md-12');

					var _max_width = $('#modal_kebutuhan_pakan_ppic').closest('div.bootbox-body').innerWidth();

					$('#modal_kebutuhan_pakan_ppic').find('table:first').scrollabletable({
						max_width : (_max_width * .95)
					});

					var _wrap = $('#modal_kebutuhan_pakan_ppic');
					var _thead_table = _wrap.find('table:first thead');
					_wrap.find('[id$="div_header_virtual_kj"] table thead').width(_thead_table.width());
				});
		},
	}).done(function(){

	});
}
function perencanaan_doc_in(elm){
	var strain = $(elm).attr('data-kode-strain');
	var tipe_kandang = $(elm).attr('data-tipe-kandang');
	var tglDocIn = $(elm).attr('data-doc-in');
	var populasiBetina = $(elm).find('td.td_populasi_betina').text();
	var populasiJantan = $(elm).find('td.td_populasi_jantan').text();
	$.ajax({
		type : 'post',
		url : 'forecast/forecast/standart_budidaya/',
		data : {strain : strain, tipe_kandang : tipe_kandang, tglDocIn : tglDocIn},
		dataType : 'json',
		success :function(data){
			var kelas = $(elm).attr('class');
			var nama_farm = $('tr.tr_header'+kelas).find('td.td_farm').text();
			var nama_kandang = $(elm).find('td.td_no_reg').text();
			var _thead = '<thead><tr><th>Minggu+Hari</th><th>Tanggal</th><th>Jenis Kelamin</th><th>Kode Pakan</th><th>Nama Pakan</th><th>Bentuk</th><th>Keb. Pakan <br /> /Ekor (gr)<th>Kuantitas <br /> (Sak)</th></th></tr></thead>';
			var _tbody = "<tbody>";
			var _nextDate = new Date(tglDocIn);
			var standart_perumur = Forecast.get_standart_budidaya(strain,tipe_kandang,tglDocIn);
			var standart_pakan_jantan = standart_perumur['j'];
			var _tmp_populasi_jantan = populasiJantan;
			var standart_pakan_betina = standart_perumur['b'];
			var _tmp_populasi_betina = populasiBetina;
			var _tbody_content = "";
			$.each(data,function(key0, value0){
				$.each(value0,function(key1, value1){
					for(var i=1;i<=7;i++){
						var _tglTampil = Config._tanggalLocal(Config._getDateStr(_nextDate),'-',' ');
						_tmp_populasi_jantan = Forecast.get_populasi_deplesi(standart_pakan_jantan[data['j'][key1].umur],_tmp_populasi_jantan);
						_tmp_populasi_betina = Forecast.get_populasi_deplesi(standart_pakan_betina[data['b'][key1].umur],_tmp_populasi_betina);
						//console.log(_tmp_populasi_betina);
						var _kebutuhan_pakan_jantan = Forecast.rumus_perhitungan_harian(standart_pakan_jantan[data['j'][key1].umur],_tmp_populasi_jantan,'sak');
						var _kebutuhan_pakan_betina = Forecast.rumus_perhitungan_harian(standart_pakan_betina[data['b'][key1].umur],_tmp_populasi_betina,'sak');
						_tbody_content += '<tr>';
						_tbody_content += '<td class="text-center">'+data['j'][key1].umur+'+'+i+'</td>';
						_tbody_content += '<td class="text-center">'+_tglTampil+'</td>';
						_tbody_content += '<td class="text-center">'+Config._jenis_kelamin[data['j'][key1].jenis_kelamin]+'<br>'+Config._jenis_kelamin[data['b'][key1].jenis_kelamin]+'</td>';
						_tbody_content += '<td class="text-center">'+data['j'][key1].kode_barang+'<br>'+data['j'][key1].kode_barang+'</td>';
						_tbody_content += '<td class="text-center">'+data['j'][key1].nama_barang+'<br>'+data['j'][key1].nama_barang+'</td>';
						_tbody_content += '<td class="text-center">'+Config._bentuk_pakan[data['j'][key1].bentuk]+'<br>'+Config._bentuk_pakan[data['j'][key1].bentuk]+'</td>';
						_tbody_content += '<td class="text-center">'+data['j'][key1].target_pakan+'<br>'+data['j'][key1].target_pakan+'</td>';
						_tbody_content += '<td class="text-right">'+Forecast.ceil2(_kebutuhan_pakan_jantan,5)+'<br>'+Forecast.ceil2(_kebutuhan_pakan_betina,5)+'</td>';
						_tbody_content += '</tr>';
						_nextDate.setDate(_nextDate.getDate() + 1);
					}
				});
			});
			_tbody += _tbody_content+"</tbody>";

			var table = '<div id="breakdown_pakan_contain" class=""><div><table class="table table-bordered breakdown_pakan">'+_thead+''+_tbody+'</table></div></div>';

			var _message = '<div class="row">';
				_message += '<div class="col-md-4">';
				_message += '<form method="post" action="forecast/forecast/cetak_breakdown_pakan" target="_blank">';
				//_message += '<a target="_blank" class="link btn btn-default" href="forecast/forecast/cetak_breakdown_pakan?strain='+strain+'&tipe_kandang='+tipe_kandang+'&tgl_doc_in='+tglDocIn+'">Print</a>';
				_message += '<input type="hidden" value="'+strain+'" name="strain">';
				_message += '<input type="hidden" value="'+tipe_kandang+'" name="tipe_kandang">';
				_message += '<input type="hidden" value="'+tglDocIn+'" name="tglDocIn">';
				_message += '<input type="hidden" value="'+nama_farm+'" name="nama_farm">';
				_message += '<input type="hidden" value="'+nama_kandang+'" name="nama_kandang">';
				_message += '<input type="hidden" value="'+Config._tipe_kandang[tipe_kandang]+'" name="tipe_kandang">';
				_message += '<input type="hidden" value="'+Forecast.ceil2(parseFloat(populasiJantan)+parseFloat(populasiBetina),0)+'" name="kapasitas">';
				_message += '<input type="hidden" value="'+Forecast.ceil2(populasiJantan,0)+'" name="jantan">';
				_message += '<input type="hidden" value="'+Forecast.ceil2(populasiBetina,0)+'" name="betina">';
				_message += '<textarea name="data_html" class="hide">'+_tbody_content+'</textarea>';
				_message += '<input type="submit" value="Print" class="btn btn-default">';
				_message += '</form>';
				_message += '</div>';
				_message += '<div class="col-md-4 text-center"><span style="font-weight:bold;font-size:25px">FARM '+nama_farm+'</span><br><span style="font-weight:bold;font-size:18px">'+nama_kandang+'</span>';
				_message += '</div>';
				_message += '<div class="col-md-4 text-right">';
				_message += '<span class="link btn btn-default" onclick="tutup_modal()">Tutup</span>';
				_message += '</div>';
				_message += '</div>';
				_message += '<div class="row"><div class="col-md-4 text-center" style="padding-top: 1%;">Tipe Kandang : '+Config._tipe_kandang[tipe_kandang]+'</div><div class="col-md-4 text-center" style="padding-top: 1%;">Kapasitas : '+Forecast.ceil2(parseFloat(populasiJantan)+parseFloat(populasiBetina),0)+' ekor</div><div class="col-md-4 text-center">Jantan : '+Forecast.ceil2(populasiJantan,0)+' ekor<br>Betina : '+Forecast.ceil2(populasiBetina,0)+' ekor</div></div>';
				_message += '<div id="modal_perencaan_docin" class="new-line">'+table+'</div>';
			var _options = {
					title : 'Breakdown Kebutuhan Pakan',
					message : _message,
					className : 'very-large',
					buttons : {
						tambahTanggal : {
							label : 'Keluar',
							className : 'hide',
							callback : function(){
							}
						}
					},
				};

				bootbox.dialog(_options).bind('shown.bs.modal',function(){
					$(this).find('table').scrollabletable({
						'scroll_horizontal' : 0,
					});
					$('table.breakdown_pakan thead th').css('vertical-align','middle');
					$('table.breakdown_pakan tbody td').css('vertical-align','middle');
				});
		},
	});
}

function detail_konfirmasi_forecast_bdy(elm){
	var _elm = $(elm);
	var _ack = _elm.text();
	var _flok = _elm.data('flok');
	var _tb = _elm.closest('tbody');
	var _tr = _elm.closest('tr');
	var _jml_kandang = 0, _populasi = 0, _docin;
	_tb.find('td[data-flok='+_flok+']').each(function(){
		_jml_kandang++;
		_populasi += parseInt($(this).find('span').data('populasi'));
		_docin = $(this).find('span').data('doc-in');
	});
	var nama_farm = _elm.data('namafarm');
	$.ajax({
		type : 'post',
		url : 'forecast/forecast/kebutuhan_pakan_ppic_bdy',
		data : {ack : _ack, nama_farm : nama_farm, docin : _docin, populasi : number_format(_populasi,0,',','.')+' ('+_jml_kandang+' Kandang)'},
		success : function(data){
			var _form = [
			'<div class="col-md-offset-3 col-md-7">',
				'<form class="form-horizontal">',
				'<div class="form-group">',
					'<label class="col-md-4 control-label" for="farm">Farm</label> ',
					'<div class="col-md-4">',
						'<div class="input-group">' ,
						'<label name="farm" class="control-label">'+nama_farm+'</label>',
						'</div>',
					'</div>',
				'</div>',
				'<div class="form-group">',
					'<label class="col-md-4 control-label" for="tglDocIn">DOC In</label> ',
					'<div class="col-md-4">',
						'<label name="tglDocIn" class="control-label">'+Config._tanggalLocal(_docin,'-',' ')+'</label>',
					'</div>',
				'</div>',
				'<div class="form-group">',
					'<label class="col-md-4 control-label" for="populasi">Populasi</label> ',
					'<div class="col-md-4">',
						'<label name="populasi" class="control-label">'+number_format(_populasi,0,',','.')+' ('+_jml_kandang+' Kandang)</label>',
					'</div>',
				'</div>',
				'</form>',
			'</div>'
			];
			var _message = '<div class="row">';
				_message += _form.join('');
				_message += '<div class="col-md-2 text-right">';
				_message += '<span class="btn btn-default" href="#" onclick="export_table(\'tabelrencanakirim\', null,\'Rencana Kirim\',\'Rencana Kirim\');">Export to Excel</span>';
				_message += '</div>';
				_message += '</div>';
				_message += '<div id="modal_kebutuhan_pakan_ppic" class="new-line">'+data+'</div>';
			var _options = {
					title : 'Breakdown Kebutuhan Pakan',
					message : _message,
					className : 'very-large',
					buttons : {
						tambahTanggal : {
							label : 'Keluar',
							className : 'hide',
							callback : function(){
							}
						}
					},
				};

				bootbox.dialog(_options).bind('shown.bs.modal',function(){
					$('#modal_kebutuhan_pakan_ppic div.container').removeClass('col-md-12');
					var _tglkirim = {}, _tglkirimpp = {}, _tmpkirim, _tmpkirimpp,_pakan = [];

					$('#modal_kebutuhan_pakan_ppic').find('table>thead>tr>th.namapakan').each(function(){
						_pakan.push($(this).data('kodepakan'));
					});
					$('#modal_kebutuhan_pakan_ppic').find('table>tbody>tr').each(function(){
						_tmpkirim = $(this).find('td.tgl_kirim').data('tglkirim');
						_tmpkirimpp = $(this).find('td.tgl_kirimpp').data('tglkirimpp');
						if(_tglkirim[_tmpkirim] == undefined){
							_tglkirim[_tmpkirim] = _tmpkirim;
						}
						if(_tglkirimpp[_tmpkirimpp] == undefined){
							_tglkirimpp[_tmpkirimpp] = _tmpkirimpp;
						}
					});
					for(var i in _tglkirim){
						var _y = $('#modal_kebutuhan_pakan_ppic').find('table>tbody>tr>td.tgl_kirim[data-tglkirim='+i+']');
						var _l = _y.length;
						if(_l > 1){
							_y.first().attr('rowspan',_l);
							_y.not(':first').remove();
						}
					}

					for(var i in _tglkirimpp){
						var _y = $('#modal_kebutuhan_pakan_ppic').find('table>tbody>tr>td.tgl_kirimpp[data-tglkirimpp='+i+']');
						var _l = _y.length;
						if(_l > 1){
							_y.first().attr('rowspan',_l);
							_y.not(':first').remove();
							for(var j in _pakan){
								var _z = $('#modal_kebutuhan_pakan_ppic').find('table>tbody>tr>td.'+_pakan[j]+'[data-tglkirimpp='+i+']');
								_z.first().attr('rowspan',_l);
								_z.not(':first').remove();
							}
						}

					}
/*
					var _max_width = $('#modal_kebutuhan_pakan_ppic').closest('div.bootbox-body').innerWidth();
					$('#modal_kebutuhan_pakan_ppic').find('table:first').scrollabletable({
						max_width : (_max_width * .95)
					});

					var _wrap = $('#modal_kebutuhan_pakan_ppic');
					var _thead_table = _wrap.find('table:first thead');
					_wrap.find('[id$="div_header_virtual_kj"] table thead').width(_thead_table.width());
*/
				});
		},
	}).done(function(){

	});
}

function perencanaan_doc_in_bdy(elm){
	var strain = $(elm).attr('data-kode-strain');
	var tipe_kandang = $(elm).attr('data-tipe-kandang');
	var tglDocIn = $(elm).attr('data-doc-in');
	var populasi = [$(elm).closest('tr').find('td.td_populasi_campuran').text()];
//	var populasiJantan = $(elm).closest('tr').find('td.td_populasi_campuran').text();
//	var populasiBetina = $(elm).closest('tr').find('td.td_populasi_campuran').text();
	var kodeFarm = $(elm).closest('td').data('kode-farm');
	var nama_farm = $(elm).closest('td').data('nama-farm');
	var nama_kandang = $(elm).text();
	var _rencanaKirim = Forecast.getRencanaKirimBdy(kodeFarm,tglDocIn);
	var tt = Forecast.generateRencanaKirim(_rencanaKirim,kodeFarm,tglDocIn,populasi);

	var _tbody = '';
	var _tbody_content = tt.data;

	_tbody +='<tr>'+ _tbody_content.join('</tr><tr>')+"</tr></tbody>";
	var _thead = '<thead><tr><th data-id="tglkirim">Tanggal Kirim</th><th data-id="tglkebutuhan">Tanggal Kebutuhan</th><th data-id="kodepakan">Kode Pakan</th><th data-id="namapakan">Nama Pakan</th><th data-id="bentuk">Bentuk</th><th data-id="kuantitas">Kuantitas <br /> (Sak)</th></tr></thead>';
	var table = '<div id="breakdown_pakan_contain" class=""><div><table class="table table-bordered breakdown_pakan">'+_thead+''+_tbody+'</table></div></div>';

			var _message = '<div class="row">';
				_message += '<div class="col-md-4">';
				_message += '<form method="post" action="forecast/forecast/cetak_breakdown_pakan" target="_blank">';
				//_message += '<a target="_blank" class="link btn btn-default" href="forecast/forecast/cetak_breakdown_pakan?strain='+strain+'&tipe_kandang='+tipe_kandang+'&tgl_doc_in='+tglDocIn+'">Print</a>';
				_message += '<input type="hidden" value="'+strain+'" name="strain">';
				_message += '<input type="hidden" value="'+tipe_kandang+'" name="tipe_kandang">';
				_message += '<input type="hidden" value="'+tglDocIn+'" name="tglDocIn">';
				_message += '<input type="hidden" value="'+nama_farm+'" name="nama_farm">';
				_message += '<input type="hidden" value="'+nama_kandang+'" name="nama_kandang">';
				_message += '<input type="hidden" value="'+Config._tipe_kandang[tipe_kandang]+'" name="tipe_kandang">';
				_message += '<input type="hidden" value="'+Forecast.ceil2(populasi[0],0)+'" name="kapasitas">';
		//		_message += '<input type="hidden" value="'+Forecast.ceil2(populasiJantan,0)+'" name="jantan">';
		//		_message += '<input type="hidden" value="'+Forecast.ceil2(populasiBetina,0)+'" name="betina">';
		//		_message += '<textarea name="data_html" class="hide">'+_tbody_content+'</textarea>';
				_message += '<input type="submit" value="Print" class="btn btn-default">';
				_message += '</form>';
				_message += '</div>';
				_message += '<div class="col-md-4 text-center"><span style="font-weight:bold;font-size:25px">FARM '+nama_farm+'</span><br><span style="font-weight:bold;font-size:18px">'+nama_kandang+'</span>';
				_message += '</div>';
				_message += '<div class="col-md-4 text-right">';
				_message += '<span class="link btn btn-default" onclick="tutup_modal()">Tutup</span>';
				_message += '</div>';
				_message += '</div>';
	//			_message += '<div class="row"><div class="col-md-4 text-center" style="padding-top: 1%;">Tipe Kandang : '+Config._tipe_kandang[tipe_kandang]+'</div><div class="col-md-4 text-center" style="padding-top: 1%;">Kapasitas : '+Forecast.ceil2(parseFloat(populasiJantan)+parseFloat(populasiBetina),0)+' ekor</div><div class="col-md-4 text-center">Jantan : '+Forecast.ceil2(populasiJantan,0)+' ekor<br>Betina : '+Forecast.ceil2(populasiBetina,0)+' ekor</div></div>';
				_message += '<div class="row"><div class="col-md-4 text-center" style="padding-top: 1%;">Tipe Kandang : '+Config._tipe_kandang[tipe_kandang]+'</div><div class="col-md-4 text-center" style="padding-top: 1%;">Kapasitas : '+Forecast.ceil2(populasi[0],0)+' ekor</div></div>';
				_message += '<div id="modal_perencaan_docin" class="new-line">'+table+'</div>';
			var _options = {
					title : 'Breakdown Kebutuhan Pakan',
					message : _message,
					className : 'very-large',
					buttons : {
						tambahTanggal : {
							label : 'Keluar',
							className : 'hide',
							callback : function(){
							}
						}
					},
				};

				bootbox.dialog(_options).bind('shown.bs.modal',function(){
					$(this).find('table').scrollabletable({
						'scroll_horizontal' : 0,
					});
					$('table.breakdown_pakan thead th').css('vertical-align','middle');
					$('table.breakdown_pakan tbody td').css('vertical-align','middle');
				});


}


function clear_content(elm){
	$('#div_konfirmasi_ppic').html('');
	var hide = $('#myTab li.estimasi').hasClass('hide');
	if(hide){
		var farm = Forecast.get_list_farm('all');
		var _farm_terpilih = [];
			$.each(farm,function(key, value){
				_farm_terpilih.push(value.kode_farm);
			});
		var _grup_farm = $(elm).attr('data-grup-farm') || $('#myTab li.konfirmasi.active>a').data('grup-farm');
		var _konfirmasi = 0;
		var _filter = {};
			$('div.date input.chickin').each(function(){
				_filter[$(this).attr('name')] = Config._tanggalDb($(this).val(),' ', '-');
			});
		$.ajax({
			type : 'post',
			url : 'forecast/forecast/data_konfirmasi_ppic',
			data : {tanggal : _filter, farm  : _farm_terpilih, konfirmasi : _konfirmasi, grup_farm : _grup_farm},
			success : function(data){
				$('#div_konfirmasi_ppic').html(data);
				$('table.konfirmasi_table thead th').css('vertical-align','middle');
				$('table.konfirmasi_table tbody td').css('vertical-align','middle');
			},
		});
	}
}
