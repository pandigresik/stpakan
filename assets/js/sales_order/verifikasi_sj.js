'use strict';

var timer = true;
var tkode_pegawai = '';
var tnama_pegawai = '';
var VerifikasiSJ = {
    refresh_page:function() {
		var html = 'Lakukan scan barcode Nomor Surat Jalan';
		var warning = '<span class="text-danger hide">Nomor Surat Jalan tidak dikenali</span>';
		html = html + '<input id="nomor_sj" type="text" class="form-control" />'+warning;
		bootbox.dialog({
			title       : 'Scan Barcode',
			className   : 'titleCenter',
			message     : html,
            closeButton : false,
			buttons: {
				confirm: {
					label: 'OK',
					callback : function() {
						// location.reload(true);
						$.ajax({
							url : 'sales_order/verifikasi_sj/check_nomor_sj',
							data : {
							   //no_ppsk : _no_ppsk, kodefarm : _kodefarm
							   no_sj : $('#nomor_sj').val(),
							},
							dataType : 'html',
							type:'POST',
							success : function(data){
								var dataObj= JSON.parse(data);
								$('span.text-danger').addClass('hide');
								if (dataObj.error=='0') {
									$('.bootbox').modal('hide');
                                    $.ajax({
										url : 'sales_order/verifikasi_sj/load_page',
										data : {
										   //no_ppsk : _no_ppsk, kodefarm : _kodefarm
										   data_sj : dataObj.content,
                                           no_sj : $('#nomor_sj').val(),
										},
										dataType : 'html',
										type:'POST',
										beforeSend : function(){
											$('#div_list_detail').html('Loading ......');
										},
										success : function(data){
											var hasil = $('#div_list_detail').html(data);
											$('.btn').removeAttr('disabled');
										}
									});
								}
                                else if (dataObj.error=='1') {
									$('span.text-danger').text(dataObj.error_msg);
									$('span.text-danger').removeClass('hide');
								}
							}
						});
						return false;
					}
				},
			},
		}, function() {
		}).bind('shown.bs.modal',function(){
			$('#bon_pengambilan_telur').focus();
		});
  	},
    verifikasi:function(elm) {
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
                    VerifikasiSJ.simpan_transaksi_verifikasi(function(result){
                        if(result.date_transaction){
                            var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
                            var box = bootbox.dialog({
                                message : _message,
                                closeButton: false,
                                title : "Fingerprint",
                                buttons : {
                                    success : {
                                        label : "Batal",
                                        className : "btn-danger",
                                        callback : function() {
                                            timer = false;
                                            tkode_pegawai = '';
                                            tnama_pegawai = '';
                                            return true;
                                        }
                                    }
                                }
                            });

                            box.bind('shown.bs.modal', function() {
                                timer = true;
                                tkode_pegawai = '';
                                tnama_pegawai = '';
                                VerifikasiSJ.cek_verifikasi(result.date_transaction);
                            });

                            box.bind('hidden.bs.modal', function() {
                                if(tkode_pegawai && tnama_pegawai){
                                    $(elm).attr('disabled', true);
                                    $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text(tnama_pegawai);
                                    $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').attr('data-user-gudang',tkode_pegawai);
                                    var done = VerifikasiSJ.cek_selesai(elm);

                                }
                                else{
                                    $(elm).attr('checked',false);
                                    $(elm).parents('tr.tr-detail-kandang').find('td.jml-aktual').text('');
                                    $(elm).parents('tr.tr-detail-kandang').find('td.berat').text('');
                                    $(elm).parents('tr.tr-detail-kandang').find('td.sisa').text('');
                                    $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text('');
                                    $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').attr('data-user-gudang','');
                                }
                            });
                        }
                    });
                }
            }
        });
    },
    simpan_transaksi_verifikasi : function(callback){
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
            data : {
                 transaction : 'verifikasi_sj'
            },
            dataType : 'json',
            success : function(data) {
                callback(data);
            }
        });
    },
    cek_verifikasi : function(date_transaction){
        if (timer == true) {
            $.ajax({
                type : "POST",
                url : "pengambilan_barang/transaksi/cek_verifikasi",
                data : {
                    date_transaction : date_transaction
                },
                dataType : 'json',
                success : function(data) {
                    if(data.verificator){
                        timer = false;
                        tkode_pegawai = data.kode_pegawai;
                        tnama_pegawai = data.nama_pegawai;
                        $('.bootbox').modal('hide');
                    }
                    else{
                        timer = true;
                        tkode_pegawai = '';
                        tnama_pegawai = '';
                        setTimeout("VerifikasiSJ.cek_verifikasi('"+date_transaction+"')", 1000);
                    }
                }
            });
        }
    },
    cek_selesai:function(elm) {
        $.ajax({
            type : "POST",
            url : "sales_order/verifikasi_sj/simpan_verifikasi",
            data : {
                 no_sj : $('input[name=no_sj]').val()
            },
            dataType : 'json',
            success : function(data) {
                if (data.success == 1) {
                    toastr.success('Verifikasi fingerprint berhasil.','Berhasil');
                }else {
                    toastr.error('Verifikasi fingerprint gagal.','Gagal');
                }
                $('#main_content').load('sales_order/verifikasi_sj');
            }
        });
    }
};

$(function() {
	VerifikasiSJ.refresh_page();
});
