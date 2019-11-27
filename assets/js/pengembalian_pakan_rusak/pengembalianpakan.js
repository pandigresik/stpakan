var Pengembalianpakan = {
	 		timer : true,
	 	  date_transaction : null,
	    tnama_pegawai : null,
			tkode_pegawai : null,
			user_gudang : [],
			add_datepicker : function(elm,options){
				elm.datepicker(options);
			},
			get_user_gudang : function(){
				if(empty(Pengembalianpakan.user_gudang)){
					$.ajax({
						type : 'post',
						url : 'pengembalian_pakan_rusak/pengembalian/get_user_pengawas',
						success : function(data){
							for(var i in data){
								Pengembalianpakan.user_gudang[i] = {value : data[i]['kode_pegawai'], label : data[i]['nama_pegawai'] + ' - ' + data[i]['kode_pegawai'], nama : data[i]['nama_pegawai']};
							}
						},
						dataType : 'json'
					});

				}

				return Pengembalianpakan.user_gudang;

			},
			transaksi : function(elm,target){
				var tgl_server = $('#tanggal_server').data('tanggal_server');
				var no_pengembalian = $(elm).data('no_pengembalian') || null;
				var nama_kandang = $(elm).data('nama_kandang') || null;
				var status = $(elm).data('status') || null;

				$.ajax({
					type : 'post',
					data : {no_pengembalian : no_pengembalian, status : status, tgl_server : tgl_server, nama_kandang : nama_kandang},
					url : 'pengembalian_pakan_rusak/pengembalian/transaksi',
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

			detail_transaksi : function(no_reg){
				$.ajax({
					type : 'post',
					data : {no_reg : no_reg},
					url : 'pengembalian_pakan_rusak/pengembalian/detail_transaksi',
					dataType : 'html',
					async : false,
					success : function(data){
						$('#tabel_pengembalian_pakan_rusak').html(data);
						/* reset nilai dari no_retur dan tgl retur */
						$('input#no_retur_pakan').val('');
						$('input#tanggal_waktu_retur').val('');
					},
				});


			},
			show_detail_timbang : function(elm){
				if(!empty($(elm).val())){
					var _tr = $(elm).closest('tr');
					var _terpilih = $(elm).find('option:selected');
					var _stok = _terpilih.data('stok');
					var _retur = _terpilih.data('retur');

					_tr.find('td.jml_stok').text(_stok);
					_tr.find('td.jml_retur').text(_retur);
					var d_timbang = _tr.next('tr.detail_timbang');
					if(d_timbang.is(':hidden')){
						d_timbang.show();
						d_timbang.find('input.number:not(".numeric")')
						.addClass('numeric');

					}
					/* tambahkan kode_barang dan jenis kelamin pada tr*/
					var _kodepj = _terpilih.data('kode_barang');
					var _jk = _terpilih.data('jenis_kelamin');
					d_timbang.find('table>tbody>tr:first').attr({
						'data-kode_barang': _kodepj,
						'data-jenis_kelamin' : _jk
					});
					/* tambahkan class untuk tr header untuk memudahkan membaca jml stok dan jml retur */
					_tr.attr('data-kode_barang',_kodepj).attr('data-jenis_kelamin',_jk);
				}
			},
			/* penimbangan pasti dilakukan per satu sak */
			timbang_lagi : function(elm){
				var _tr = $(elm).closest('tr');
				var _table = _tr.closest('table');
				var _tr_detail = _table.closest('tr');
				var _tr_header = _tr_detail.prev();
				/* pastikan sudah diisi semua */
				var _error = 0;
				var _field = [];
				_tr.find('input.required').each(function(){
					if($(this).hasClass('numeric')){
						if($(this).val() <= 0){
							_error++;
							_field.push($(this).data('field'));
						}
					}
					else{
						if(empty($(this).val())){
							_error++;
							_field.push($(this).data('field'));
						}
					}

				});
				var _jml_stok = _tr_header.find('td.jml_stok');
				if(parseInt(_jml_stok.text()) < 1){
					_error++;
					toastr.error('Jumlah stok sudah 0 tidak bisa retur kembali');
				}

				/* jika jumlah baris masih 1 maka insert juga nama pakan dan nama pakan yang masih bisa dipilih masih ada */
				var _jml_tr = _table.find('tbody>tr');
				if(_jml_tr.length == 1 && !_error ){

					var _tbody = _tr_header.closest('tbody');
					/* disabled nama pakan header */
					var _kb = _tr_header.find('td>select[name=kode_barang]');
					var _kode_pakan_terpilih = _kb.find('option:selected').val();

					var _tot_nama_pakan = _tr_header.find('td>select[name=kode_barang]>option[value!=""]:visible').length;
					if(_tot_nama_pakan > 1){
						var _clone_tr_header = _tr_header.clone();
						var _clone_tr_detail = _tr_detail.clone();
						_clone_tr_detail.find('input').val('');
						 _clone_tr_detail.find('input.number').val(0);
						_clone_tr_detail.hide();
						_tbody.append(_clone_tr_header);
						_tbody.append(_clone_tr_detail);
						/* hidden nama pakan yang telah dipilih */
						 _clone_tr_header.find('td>select option[value="'+_kode_pakan_terpilih+'"]').hide();
						/* remove data kode_barang dan jenis kelamain */
						 _clone_tr_header.attr('data-kode_barang',null).attr('data-jenis_kelamin',null);
					}

					_kb.prop('disabled',1);

				}

				if(!_error){
					var _new_tr = _tr.clone();
					_new_tr.find('input.numeric').val(0);
					_new_tr.find('input[name=keterangan]').val('');
					_new_tr.appendTo(_table);
					_new_tr.find('input.numeric');
					$(elm).remove();
					_tr.find('input').attr('readonly',true);
					_tr.addClass('siap_simpan');
					/* update jumlah yang sudah diretur */
					var _jml_retur = _tr_header.find('td.jml_retur');
					_jml_retur.text(parseInt(_jml_retur.text()) + 1);

					/* update jumlah stok akhir */
					_jml_stok.text(parseInt(_jml_stok.text()) - 1);
				}
				for(var i in _field){
					toastr.error(_field[i]+' tidak boleh kosong');
				}
			},
			/* simpan pengembalian sak */
			akan_simpan : function(elm){
				/* pastikan lampiran sudah diisi */
				var lampiran = $('#lampirkan-foto').val();
				var message = [];
				var _error = 0;
				var _siap_simpan = $('tr.siap_simpan');
				if(!_siap_simpan.length){
					_error++;
					message.push('Tidak ada yang akan disimpan, belum ada penimbangan sak');
				}
				if(empty(lampiran)){
					_error++;
					message.push('Lampiran foto harus diisi');
				}

				/* cek apakah sudah ada yang bisa disimpan */
				if(!_error){
					bootbox.confirm({
					    title: 'Konfirmasi Pengembalian pakan',
					    message: 'Apakah yakin akan melakukan proses simpan ?',
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
									Pengembalianpakan.simpan_transaksi_verifikasi(function(result){
										Pengembalianpakan.timer = true;
										Pengembalianpakan.tkode_pegawai = null;
										Pengembalianpakan.tnama_pegawai = null;
										Pengembalianpakan.date_transaction = result.date_transaction;
									});
					    		Pengembalianpakan.simpan(elm);
					    		}
					    	}
					    });

				}
				if(!empty(message)){
					for(var i in message){
						toastr.error(message[i]);
					}
				}
			},
			simpan : function(elm){
				var _siap_simpan = $('tr.siap_simpan');
				var _detail = {}, _tmp = {};
				var _no_reg = $('#tabel_detail_pengembalian_pakan_rusak').data('no_reg');
				var _brt_sak, _ket,_kode_barang, _jenis_kelamin;
				_siap_simpan.each(function(){
			//	_brt_sak = parse_number($(this).find('input[name=brt_sak]').val(),'.',',');
			  _brt_sak = $(this).find('input[name=brt_sak]').val();
				_ket = $(this).find('input[name=keterangan]').val();
				_kode_barang = $(this).data('kode_barang');
				_jenis_kelamin = $(this).data('jenis_kelamin');

						if(_detail[_kode_barang] === undefined){
							_detail[_kode_barang] = {};
						}
						if(_detail[_kode_barang][_jenis_kelamin] === undefined){
							_detail[_kode_barang][_jenis_kelamin] = [];
						}
					//	_tmp = {jml_k : _jml_kembali,brt_k : _brt_kembali,kb : _kode_barang,jk : _jenis_kelamin};
						_tmp = {brt : _brt_sak,ket : _ket};
						_detail[_kode_barang][_jenis_kelamin].push(_tmp);
					});

					/* cari jml_kirim dan jml_pakai serta jml_aktual pengiriman ketika dilakukan pengembalian
					 * */
					var _header_pj = {};
					var _tr_header_pj;
					var _jml_retur, _jml_sudah_diganti, _jml_stok;
					for(var kb in _detail){
						if(_header_pj[kb] === undefined){
							_header_pj[kb] = {};
						}
						for(var jk in _detail[kb]){
							if(_header_pj[kb][jk] === undefined){
								_header_pj[kb][jk] = {};
							}
						/*
							_jml_kembali = 0;
							for(var x in _detail[kb][jk]){
								_jml_kembali += parseInt(_detail[kb][jk][x].jml_k);
							}
						*/
							_tr_header_pj = $('tr.tr_header[data-kode_barang="'+kb+'"][data-jenis_kelamin="'+jk+'"]');
							_jml_retur = _tr_header_pj.find('td.jml_retur').text();
						//	_jml_sudah_diganti = _tr_header_pj.find('td.jml_sudah_diganti').text();
							_jml_stok = _tr_header_pj.find('td.jml_stok').text();
				//			_hutang = parseInt(_jml_pakai) -( parseInt(_jml_aktual) + _jml_kembali );
							_header_pj[kb][jk] = {retur : _jml_retur,  stok : _jml_stok};
						}
					}

					bootbox.confirm({
					    title: 'Finger print',
							message : '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>',
					    buttons: {
					        'cancel': {
					            label: 'Tidak',
					            className: 'btn-default'
					        },
					        'confirm': {
					            label: 'Ya',
					            className: 'btn-danger hide'
					        }
					    },
					    callback : function(result){
					    	if(result){
					    		var user_verifikasi = Pengembalianpakan.tkode_pegawai;
					    		var nama_verifikasi = Pengembalianpakan.tnama_pegawai;
					    		/* pastikan usernya memilih nama verifikasi dengan benar */

					    		var lampiran = $('#lampirkan-foto').val();
					    		var data = {data : _detail,noreg : _no_reg, headerpj : _header_pj};
					    		var attachment = $('#file-upload').get(0).files[0];
								var formData = new FormData();
				                formData.append('attachment', attachment);
				                formData.append('attachment_name', lampiran);
				                formData.append('data', JSON.stringify(data));
				                formData.append('user_verifikasi',user_verifikasi);
						    	$.ajax({
									url :'pengembalian_pakan_rusak/pengembalian/simpan',
									type : 'post',
									data : formData,
									success : function(data){
										if(data.status){
											/* hapus yang sudah disimpan */
											_siap_simpan.siblings(':not(.siap_simpan)').remove();
											_siap_simpan.find('input').prop('disabled',1);
											_siap_simpan.removeClass('siap_simpan');
											$('input#no_retur_pakan').val('RP/'+data.content.no_retur);
											$('input#tanggal_waktu_retur').val(data.content.tgl_buat);
											$('#pengawas-kandang').text(nama_verifikasi);
											toastr.success('Data sudah disimpan');
										}
									},
									dataType : 'json',
									async : false,
									contentType : false,
				                    processData : false,
								});
					    		}
						    	else{
						    		toastr.error('Pengawas kandang harus diisi');
										Pengembalianpakan.timer = false;
						    	//	return false;
						    	}
					    	}
					    }).bind('shown.bs.modal',function(){
					    	var _modal = $(this);
								Pengembalianpakan.cek_verifikasi(Pengembalianpakan.date_transaction);
					    }).bind('hide.bs.modal',function(){
								 Pengembalianpakan.timer = false;
							});

			},

			list_cari : function(elm){
				var _form = $(elm).closest('form');
				var _tgl = _form.find('input[name$=Date]');

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

				if(!_jmltgl){
					_error++;
					toastr.error('Minimal satu tanggal harus diisi');
				}
				if(!_error){
					$.ajax({
						url : 'pengembalian_pakan_rusak/pengembalian/list_pengembalian',
						type : 'post',
						data : {tanggal : _tanggal},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('#list_pengembalian').html(' Silakan tunggu ....');
						},
						success : function(data){
							$('#list_pengembalian').html(data);
						}
					});
				}

			},
			filter_content : function(elm){
				var _table = $(elm).closest('table');
				var _tbody = _table.find('tbody');
				var _content = $(elm).val();
				var _target = $(elm).attr('name');

				_tbody.find('td.'+_target+':contains('+_content.toUpperCase()+')').parent().show();
				_tbody.find('td.'+_target+':not(:contains('+_content.toUpperCase()+'))').parent().hide();
			},


			simpan_transaksi_verifikasi : function(callback){
			    $.ajax({
			        type : "POST",
							data : {"transaction" : "pengembalian_pakan_rusak"},
			        url : "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
			        dataType : 'json',
			        success : function(data) {
			            callback(data);
			        }
			    });
			},

			cek_verifikasi : function(){
			    if (Pengembalianpakan.timer) {
			        $.ajax({
			            type : "POST",
			            url : "pengambilan_barang/transaksi/cek_verifikasi",
			            data : {
			                date_transaction : Pengembalianpakan.date_transaction
			            },
			            dataType : 'json',
			            success : function(data) {
			                if(data.verificator){
			                    Pengembalianpakan.timer = false;
			                    Pengembalianpakan.tkode_pegawai = data.verificator;
													Pengembalianpakan.tnama_pegawai = data.nama_pegawai;
			                    $('.bootbox.modal .modal-footer').find('.btn.hide').click();
			                }
			                else{
												//	Pengembalianpakan.timer = false;
													Pengembalianpakan.tkode_pegawai = null;
													Pengembalianpakan.tnama_pegawai = null;
			                    setTimeout(Pengembalianpakan.cek_verifikasi(), 2000);
			                }
			            }
			        });
			    }
			},
			get_berat_timbang : function(elm){
			    $(elm).removeAttr('readonly');
			    //console.log('OK');
			    setTimeout(function(){
			        var berat = $(elm).val();
			        $(elm).val(berat);
			        $(elm).attr('readonly', true);
			    }, 0);
			}


	};
$(function(){
	'use strict';
	Pengembalianpakan.add_datepicker($('input[name=startDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
		}
	});
	Pengembalianpakan.add_datepicker($('input[name=endDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		}
	});

	$(document).on('keydown','input[name=kandang]',function(){
		$(this).autocomplete({
		      minLength: 2,
		      source: function( request, response ) {
		          $.ajax({
		        	type : 'post',
		            url: "pengembalian_sak/pengembalian/list_kandang",
		            dataType: "json",
		            data: {
		              nama_kandang : request.term
		            },
		            success: function( data ) {
		              response( data );
		            }
		          });
		        },
		      focus : function( event, ui ) {

		        return false;
		      },
		      select: function( event, ui ) {
		    	  $(this).val(ui.item.nama_kandang);
		    	  Pengembalianpakan.detail_transaksi(ui.item.no_reg);
		        return false;
		      }
		    })
		    .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		      return $( "<li  style='font-size:70%'>" )
		        .append( "<span>" + item.nama_kandang + "</span>&nbsp;&nbsp;<span>" + item.no_reg + "</span>" )
		        .appendTo( ul );
		    };
	});

	  $(document).on('change', '.btn-file :file', function() {
	        var input = $(this),
	            numFiles = input.get(0).files ? input.get(0).files.length : 1,
	            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	            /* dapatkan extensionnya */
	            var ext_file = label.split('.');
	            var allow_file = ['doc','docx','DOC','DOCX'];
	            if(in_array(ext_file[ext_file.length - 1],allow_file)){
	            	$('#lampirkan-foto').val(label);
	            }
	            else{
	            	toastr.error('File yang diijinkan adalah '+ allow_file.join(' , '));
	            	return false;
	            }
	    });

}());
