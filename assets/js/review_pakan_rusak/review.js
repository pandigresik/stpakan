var ReviewPakanRusak = {
			add_datepicker : function(elm,options){
				elm.datepicker(options);
			},
			kontrol_chekbox : function(elm){
				if($(elm).is(':checked')){
					$(elm).val('1');
					$('div.tanggal_retur').addClass('grey_color');
					$('div.tanggal_retur input').attr('disabled', true);
					$('div.tanggal_retur input').val('');
					$('.btn_cari').attr('disabled', true);
					ReviewPakanRusak.header_pakan_rusak();
				}
				else{
					$(elm).val('0');
					$('div.tanggal_retur').removeClass('grey_color');
					$('div.tanggal_retur input').removeAttr('disabled');
					$('.btn_cari').removeAttr('disabled');
				}
			},
			header_pakan_rusak : function(){
				$('#header_pakan_rusak tbody').html('');
				$('#detail_pakan_rusak tbody').html('');
				$('.div_button span').hide();
				$('#span_no_retur').text('');
				var tindak_lanjut = $('#checkbox_tindak_lanjut').val();
				var startDate = $('#startDate').val();
				var endDate = $('#endDate').val();
				var no_retur = $('#f_no_retur').val();
				var kandang = $('#f_kandang').val();
				var not_valid = 0;
				if(tindak_lanjut == 0){
					if(!startDate || !endDate){
						not_valid++;
					}
					startDate = Config._tanggalDb(startDate,' ' ,'-' );
					endDate = Config._tanggalDb(endDate,' ' ,'-' );
				}
				if(not_valid > 0){
					toastr.error('Tanggal retur harus diisi.','Peringatan');
				}
				else{
			
					$.ajax({
						type : 'post',
						data : {
							tindak_lanjut : tindak_lanjut
							, startDate : startDate
							, endDate : endDate
							, no_retur : no_retur
							, kandang : kandang
						},
						url : 'review_pakan_rusak/review/header_pakan_rusak',
						dataType : 'html',
						async : false,
						success : function(data){
							$('#header_pakan_rusak tbody').html(data);
						},
					}).done(function(){ 
					});
				}
			},
			detail_pakan_rusak : function(elm){
				$('#span_no_retur').text('');
				$('#detail_pakan_rusak tbody').html('');
				$('#header_pakan_rusak tbody tr').removeClass('mark');
				$(elm).addClass('mark');
				$('.div_button span').hide();
				var no_reg = $(elm).attr('data-no-reg');
				var no_urut = $(elm).attr('data-no-urut');
				var wkt_review = $('tr.mark td.wkt_review').text();
				if(!wkt_review){
					$('.div_button span').show();
				}
				$.ajax({
					type : 'post',
					data : {no_reg : no_reg, no_urut : no_urut},
					url : 'review_pakan_rusak/review/detail_pakan_rusak',
					dataType : 'html',
					async : false,
					success : function(data){
						$('#span_no_retur').text('No. RP'+no_reg+'-'+no_urut);
						$('#detail_pakan_rusak tbody').html(data);
					},
				}).done(function(){ 
				});
			},
			
			simpan : function(transaksi){
				if(transaksi == 'A'){
					ReviewPakanRusak.review(transaksi);
				}
				else{
					ReviewPakanRusak.konfirmasi_reject(transaksi);
				}
				
			
			},
			
			review : function(transaksi,alasan){
				var no_reg = $('tr.mark').attr('data-no-reg');
				var no_urut = $('tr.mark').attr('data-no-urut');
				$.ajax({
					type : 'post',
					data : {no_reg : no_reg, no_urut:no_urut, transaksi:transaksi, alasan:alasan},
					url : 'review_pakan_rusak/review/simpan',
					dataType : 'json',
					async : false,
					success : function(data){
						if(data){
							if(data.result == 1){

								var tgl_review = Config._tanggalLocal(data.tgl_review,'-' ,' ' );
								$('tr.mark td.wkt_review').text(tgl_review+' '+data.wkt_review);
								$('.div_button span').hide();
								$('#span_no_retur').text('');

								var tindak_lanjut = $('#checkbox_tindak_lanjut').val();
								if(tindak_lanjut == 1){
									$('tr.mark').remove();
									$('#detail_pakan_rusak tbody').html('');
								}
								toastr.success('Simpan Review RP/'+no_reg+'-'+no_urut+' berhasil.','Peringatan');
							}
							else if(data.result == 5){
								toastr.error('Simpan Review RP/'+no_reg+'-'+no_urut+' gagal. LHK Tanggal Kebutuhan '+Config._tanggalLocal(data.tanggal_kirim,'-',' ')+' belum dientri.','Peringatan');
							}
							else if(data.result == 6){
								toastr.error('Simpan Review RP/'+no_reg+'-'+no_urut+' gagal. Stok gudang tidak ada.','Peringatan');
							}
							else{
								toastr.error('Simpan Review RP/'+no_reg+'-'+no_urut+' gagal.','Peringatan');
							}
						}
						else{
							toastr.error('Simpan Review RP/'+no_reg+'-'+no_urut+' gagal.','Peringatan');
						}
					},
				});
				
			
			},

			konfirmasi_reject : function(transaksi){

                        var _message = '<div class="form-group form-horizontal new-line">';
                        _message += '<div class="form-group">';
                        _message += '<label class="col-sm-3 control-label">Alasan Reject :</label>';
                        _message += '<div class="col-sm-5">';
                        _message += '<textarea type="text" rows="5" placeholder="Alasan Reject" id="alasan_reject" class="form-control">';
                        _message += '</textarea><span class="do-not-valid">*Wajib diisi</span>';
                        _message += '</div></div>';
                        // _message += '<div class="form-group"><div
                        // class="col-sm-12 text-center"><button class="btn
                        // btn-default">Simpan</button></div></div>';
                        _message += '</div>';
                        var box = bootbox
                        .dialog({
                            message : _message,
                            title : "Konfirmasi Reject",
                            buttons : {
					            danger : {
					                label : "Batal",
					                className : "btn-danger",
					                callback : function() {
					                    return true;
					                }
					            },
                                success : {
                                    label : "Simpan",
                                    className : "btn-success",
                                    callback : function() {
                                    	var alasan_reject = $('#alasan_reject').val();
                                    	if(alasan_reject){
											ReviewPakanRusak.review(transaksi, alasan_reject);
											return true;
                                    	}
                                    	else{

											toastr.error('Alasan Reject harus diisi.','Peringatan');
					                    	return false;
                                    	}
                                    }
                                }
                            }
                        });
			}
			
	};
$(function(){
	'use strict';

	ReviewPakanRusak.kontrol_chekbox($('#checkbox_tindak_lanjut'));

	ReviewPakanRusak.header_pakan_rusak();

	ReviewPakanRusak.add_datepicker($('input[name=startDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		}
	});
	ReviewPakanRusak.add_datepicker($('input[name=endDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		}
	});
				
}());