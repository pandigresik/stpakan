var KPPG = {
			listFarm : null,
					
			detail_kandang : function(kode_kandang,kode_farm){
				var _flock = $('select[name=tgldocin]');
				_flock.find('option:not(:first)').remove();
				$.ajax({
					type : 'post',
					data : {kode_kandang : kode_kandang, kode_farm : kode_farm},
					url : 'report/report/detail_kandang',
					dataType : 'json',
					async : false,
					success : function(data){
						if(data.status){
							var c = data.content;
							// tampilkan flock yang bisa dipilih
							var _opt = [], _c;
							for(var i in c){
								_c = c[i];
								_opt.push('<option data-flokbdy="'+_c.flok_bdy+'" data-jmlpopulasi="'+_c.jml_populasi+'" data-docin="'+_c.tgl_doc_in+'" value="'+_c.no_reg+'">'+Config._tanggalLocal(_c.tgl_doc_in,'-',' ')+'&nbsp;&nbsp;(&nbsp;'+_c.periode_siklus+' )</option>');
							}

							_flock.append(_opt.join(''));
							_flock.find('option:eq(1)').prop('selected',1);
							Rhk.showDetailKandang(_flock);
						}
						else{
							toastr.error('Data tidak ditemukan');
						}

					},
				});


			},
			showDetailKandang : function(elm){
				var ini = $(elm);
				var _error = 0;
				if(empty(ini.val())){
					_error++;
					toastr.warning('Pilih salah satu tanggal doc in');
				}
				if(!_error){
					var _terpilih = ini.find('option:selected');
					$('label[name=flock]').text(_terpilih.data('flokbdy'));
					$('label[name=populasi]').text(number_format(_terpilih.data('jmlpopulasi'),0,',','.'));
				}
			},

			showListSiklusFarm : function(elm){
				var _error = 0;
				var tanggal = {}, where;				
				var _farm = $(elm).val();
				var _namafarm = $(elm).find('option:selected').text();
				if(empty(_farm)){
					_error++;
					$('#divListSiklus .panel-body').html('');
				}
				if(!_error){					
					/* tampilkan daftar siklus untuk farm tersebut */
					$.ajax({
						url : 'report/kontrol_pemakaian_penjualan_glangsing/listSiklus/'+_farm,
						type : 'get',
						data : {},
						dataType : 'json',
						async : false,
						success : function(data){
							if(data.status){
								var _dataFarm = {}, _tmp;								
								var _content = [];
								var _content_str = '';
								var _nextSiklus = '';
								if(!empty(data.content)){
									for(var i in data.content){
										var _dataFarm = data.content[i];										
										_content.push('<div onclick="KPPG.listBudgetGlangsing(this)"  class="pointer alert alert-info" data-farm="'+_farm+'" data-nextsiklus="'+_nextSiklus+'" data-kodesiklus="'+_dataFarm['kode_siklus']+'">');
										_content.push('Farm '+_namafarm+' periode siklus '+_dataFarm['periode_siklus']+' ('+_dataFarm['strain']+','+_dataFarm['jml_kandang']+' Kandang)');
										_content.push('</div>');
										_nextSiklus = _dataFarm['kode_siklus'];
										}
										_content_str = _content.join('');
									}									
								}
								else{
									_content_str = 'Data tidak ditemukan';
								}
								$('#divListSiklus .panel-body').html(_content_str);
							}						
					});
				}
			},

			listBudgetGlangsing : function(elm){			
				var kodeSiklus = $(elm).data('kodesiklus');
				var nextSiklus = $(elm).data('nextsiklus');
				var farm = $(elm).data('farm');
				var url = 'report/kontrol_pemakaian_penjualan_glangsing/listBudgetGlangsing/';
				var _action = 'KPPG.showDetailGlangsing(this)';
								
				/* cek apakah detailnya sudah tampil atau belum */
				var _detailElm = $(elm).next('div.detailGlangsing');
				if(!_detailElm.length){
					/* load dari server */
					$.ajax({
						type : 'post',
						dataType : 'json',		
						data : {kodeSiklus : kodeSiklus, nextSiklus : nextSiklus, kodeFarm : farm},	
						beforeSend : function(){
							$('<div>Mohon tunggu ....</div>').insertAfter($(elm));
						},			
						url : url,
						success : function(data){
							var _content = ['<div class="detailGlangsing" style="margin-top:-15px">'];
							var _content_str = '';
							if(data.status){
								var _dataBudget = {}, _tmp;																
								if(!empty(data.content)){
									for(var i in data.content){
										_dataBudget = data.content[i];										
										_content.push('<div style="padding:5px;margin:5px;background-color:#AB9087" onclick="KPPG.listDetailPemakaianGlangsing(this)"  class="pointer alert-info" data-kodebarang="'+_dataBudget['kode_barang']+'" data-kodesiklus="'+kodeSiklus+'">');										
										_content.push('Glangsing '+i);										
										_content.push('</div>');
										_content.push('<div>'+_dataBudget+'</div>');
										}																				
									}else{
										_content.push('<div style="padding:5px;margin:5px;background-color:#AB9087" class="pointer alert-info">');										
										_content.push('Data tidak ditemukan');
										_content.push('</div>');
									}									
								}
								
								console.log(_content);
							_content.push('</div>');
							_content_str = _content.join('');	
							$(elm).next().replaceWith(_content_str);							
							//$(_content_str).insertAfter($(elm));
						},
					}).done(function(){
						//$(elm).next('div.detailGlangsing');
					});
				}
				else{
					if(_detailElm.is(':visible')){
						_detailElm.hide();
					}
					else{
						_detailElm.show();
					}
				}
			},

			showDetailRhk : function(elm,tipe){
				var noreg = $(elm).data('noreg');
				var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'),' ','-');
				var _detailElm = $(elm).next('div.detailrhk');
				if(!_detailElm.length){
					$.ajax({
						url : 'report/report/detail_rhk_bdy',
						type : 'post',
						data : {noreg : noreg, tgl_docin : tgl_docin, tipe : tipe},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
						},
						success : function(data){
							$(elm).next('div.detailrhk').html(data);
						}
					}).done(function(){
						$(elm).next('div.detailrhk').find('a[data-toogle=tooltip]').tooltip();
						$(elm).next('div.detailrhk').find('table').scrollabletable({
							 'max_width' : $(elm).next('div.detailrhk').width(),
						});
					});
				}else{
					if(_detailElm.is(':visible')){
						_detailElm.hide();
					}
					else{
						_detailElm.show();
					}
				}
			},

			showDetailLsam : function(elm,tipe){
				var periode = $(elm).data('periode');
				var farm = $(elm).data('farm');
				var noreg = $(elm).data('noreg');
				var flock = $(elm).data('flock');
				var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'),' ','-');
				var _detailElm = $(elm).next('div.detailrhk');
				if(!_detailElm.length){
					$.ajax({
						url : 'report/report/detail_rhk_bdy',
						type : 'post',
						data : {noreg : noreg, tgl_docin : tgl_docin, tipe : tipe, flock : flock, farm : farm, periode : periode},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
						},
						success : function(data){
							$(elm).next('div.detailrhk').html(data);
						}
					}).done(function(){
						$(elm).next('div.detailrhk').find('a[data-toogle=tooltip]').tooltip();
						$(elm).next('div.detailrhk').find('table').scrollabletable({
							 'max_width' : $(elm).next('div.detailrhk').width(),
						});
					});
				}else{
					if(_detailElm.is(':visible')){
						_detailElm.hide();
					}
					else{
						_detailElm.show();
					}
				}
			},
			showDetailLspm : function(elm,tipe){
				var noreg = $(elm).data('noreg');
				var range_periode = $('select[name=range_periode]').val();
				var tgl_docin = Config._tanggalDb($(elm).data('tglchickin'),' ','-');
				var _detailElm = $(elm).next('div.detailrhk');
				if(!_detailElm.length){
					$.ajax({
						url : 'report/report/detaillspm',
						type : 'post',
						data : {noreg : noreg, tgl_docin : tgl_docin, tipe : tipe, range_periode : range_periode},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('<div class="detailrhk">Silakan tunggu ......</div>').insertAfter($(elm));
						},
						success : function(data){
							$(elm).next('div.detailrhk').html(data);
						}
					}).done(function(){
					//	$(elm).next('div.detailrhk').find('a[data-toogle=tooltip]').tooltip();
						$(elm).next('div.detailrhk').find('table').scrollabletable({
							 'max_width' : $(elm).next('div.detailrhk').width(),
						});
					});
				}else{
					if(_detailElm.is(':visible')){
						_detailElm.hide();
					}
					else{
						_detailElm.show();
					}
				}

			},

		showInformasi : function(elm,tipe){
			var lf = this.getListFarm();
			var _option = [];
			var tglserver = $('#tanggal_server').data('tanggal_server').split('-');
			var listTahun = [];
			var tahunSekarang = tglserver[0];
			var tahunLalu = parseInt(tahunSekarang) - 1;
			while(tahunSekarang >= tahunLalu){
				listTahun.push('<option value="'+tahunSekarang+'">'+tahunSekarang+'</option>');
				tahunSekarang--;
			}
			var _jmlFarm = 0;
			for(var i in lf){
				_option.push('<option value="'+lf[i]['kode_farm']+'">'+lf[i]['nama_farm']+'</option>');
				_jmlFarm++;
			}
			if(_jmlFarm > 1){
				_option.unshift('<option value="ALL">SEMUA</option>');
			}
			var input_str =[
							'<div class="row">',
								'<form class="form form-inline">',
									'<div class="form-group col-md-3">',
										'<label class="control-label" for="farm">Farm</label> ',
										'<select class="form-control" name="farm">'+_option.join('')+'</select>',
									'</div>',
									'<div class="form-group col-md-6">',
										'<label class="control-label" for="farm">Tahun</label> ',
										'<select class="form-control" name="tahun">'+listTahun.join('')+'</select>',
										'&nbsp;<span class="btn btn-primary" onclick="Rhk.detailInformasi(this,\''+tipe+'\')">Cari</span>',
									'</div>',
								'</form>',
								'</div>',
								'<br />',
								'<div class="div_detailInformasi"></div>'

					];
			var _options = {
				title : 'Informasi',
				message : input_str.join(''),
				className : 'largeWidth',
				buttons : {
					set : {
						label : 'Tutup',
						className : '',
						callback : function(e){

						}
					}
				},
			};

			bootbox.dialog(_options);
		},

		detailInformasi : function(elm,tipe){
			var _form = $(elm).closest('form');
			var _farm = _form.find('select[name=farm]').val();
			var _tahun = _form.find('select[name=tahun]').val();
			var _bd = _form.closest('.bootbox-body');
			$.get('report/report/detailInformasi',{farm : _farm, tahun : _tahun, tipe : tipe},function(data){
				_bd.find('.div_detailInformasi').html(data).find('table').scrollabletable();
			});
		},
		list_cari : function(elm){
				var _form = $(elm).closest('form');
				var _tgldocin = $('select[name=tgldocin]').val();
				var _error = 0;

				if(empty(_tgldocin)){
					_error++;
					toastr.error('Harus memilih tanggal doc in terlebih dahulu');
				}

				if(!_error){
					var noreg = $('select[name=tgldocin]').val();
					var _sel = $('select[name=tgldocin] option:selected');
					var populasi = _sel.data('jmlpopulasi');
					var tgl_docin = _sel.data('docin');

					$.ajax({
						url : 'report/report/detail_rhk_bdy',
						type : 'post',
						data : {noreg : noreg, populasi : populasi, tgl_docin : tgl_docin},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('#detail_rhk').html(' Silakan tunggu ....');
						},
						success : function(data){
							$('#detail_rhk').html(data);
						}
					}).done(function(){
						$('#detail_rhk').find('a[data-toogle=tooltip]').tooltip();
						$('#detail_rhk table').scrollabletable({
							 'max_width' : $('#detail_rhk').outerWidth(),
						})
						.siblings('div[id$=left_section_virtual]').find('table>tbody>tr.rekap').find('td.tanggal').each(function(){
							var _umur = parseInt($(this).next().text()) / 7;
							$('<span class="bookmark_span">Minggu '+(parseInt(_umur))+'</span>').css({
								'position' : 'absolute',
								'left' : '2px',
								'top' : $(this).position().top + $(this).height()  + 'px',
								'padding-left' : '3px',
								'padding-top': '2px',
								'height': $(this).height() / 2 + 'px',
								'background-color': 'orange',
								'display': 'cell-table',
								'font-size': '80%',
								'font-weight' : 'bolder'
							}).appendTo('#detail_rhk');
						//	$(this).find('td:first').addClass('kuning');
					});
					$('div[id$=table_wrapper_kj]').scroll(function(){
							var _sc = $(this).scrollTop();
							$('span.bookmark_span').each(function(i){
								$(this).css({
									'top' : $('div[id$=left_section_virtual] table>tbody>tr.rekap>td.tanggal').eq(i).position().top + $(this).height()  + 'px',
								});
					//			if($(this).position().top > )
							})
					});

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
			exportExcel : function(elm){
					var idtabel = $(elm).data('idtabel');
					var _f = $(elm).closest('form');
					var _kandang = _f.find('input[name=kandang]').val();
					var _d = _f.find('select[name=tgldocin]>option:selected');
					var _sheet = 'Doc In '+_d.data('docin')+' flok '+_d.data('flokbdy');
					if($('#'+idtabel).length){
						export_table(idtabel,null,'RHK '+_kandang,_sheet);
					}
			},
			exportSpreadsheet : function(zipname){
				var _maindiv = $('#div_rhklsamfarm');
				var _id = [], _rand, _name = [], _i = 0;
				var _zip = JSZip(), _farm,_noreg,_xls,_filename,_prevDiv, _result;
				_maindiv.find('.detailrhk').each(function(){
					_prevDiv = $(this).prev();
					_noreg = _prevDiv.data('noreg');
					_farm = _noreg.split('/')[0];
					_filename = _prevDiv.text()+'.xls';
					$(this).find('div[id$=table_wrapper_kj]>table').each(function(){
							if(empty($(this).attr('id'))){
								_rand = 'tbjs_'+Math.floor(Math.random() * 100);
								$(this).attr('id',_rand);
							}
							else{
								_rand = $(this).attr('id');
							}
							_xls = $(this).excelexportjs({
								containerid: _rand
								, datatype: 'table'
								, returnUri: true
								, worksheetName: _noreg
							});
							_zip.folder(_farm).file(_filename,_xls,{base64 : true});

					});
				});
				_result = _zip.generate({type:"blob"});
				saveAs(_result,zipname);
			},


/* laporan versi trs */
			showRhkTrs : function(elm,tipe){
				/* tampilkan semua rhk farm */
				var lf = this.getListFarm();
				var _form = $(elm).closest('form');
				var _status = _form.find('select[name=status_siklus]').val() || 'O';
				var _farm = _form.find('input[name=farm]').val() || '';
				var _sfarm, _namafarm = {};
				var _siklus = _form.find('input[name=siklus]').val() || '';

				$.when(lf).then(function(){
					var _f = [];
					for(var i in lf){
						_f.push(lf[i]['kode_farm']);
						_namafarm[lf[i]['kode_farm']] = lf[i]['nama_farm'];
					}
					_sfarm = empty(_farm) ? _f : [_farm];
					$.ajax({
						url : 'report/report/rhk_farm_trs',
						type : 'post',
						data : {farm : _sfarm, status_siklus : _status, siklus : _siklus, namafarm : _namafarm, tipe : tipe},
						dataType : 'html',
						async : false,
						beforeSend : function(){
							$('#div_detail_rhk').html(' Silakan tunggu ....');
						},
						success : function(data){
							$('#div_detail_rhk').html(data);
						}
					}).done(function(){
							/* hidden screen_3 & Screen_4 */
							$('.screen_3,.screen_4').hide();
					});
				})
			},
			next : function(elm){
				var _c = $(elm).data('current');
				var _min = $(elm).data('min');
				var _max = $(elm).data('max');
				var _next = parseInt(_c) + 1;
				if(_c < _max){
						$('.slider-table').data('current',_next);
						$('.screen_'+_c).hide();
						$('.screen_'+_next).show();
				}else{
					toastr.warning('Tombol next disable, silakan pilih tombol prev');
				}
			},

			prev : function(elm){
				var _c = $(elm).data('current');
				var _min = $(elm).data('min');
				var _max = $(elm).data('max');
				var _prev = parseInt(_c) - 1;
				if(_c > _min){
						$('.slider-table').data('current',_prev);
						$('.screen_'+_c).hide();
						$('.screen_'+_prev).show();
				}else{
					toastr.warning('Tombol prev disable, silakan pilih tombol next');
				}
			},

			exportSpreadsheetTrs : function(filename){
				var _maindiv = $('#div_rhklsamfarm');
				var _id = [], _rand, _name = [], _i = 0;
				var _zip = JSZip(), _farm,_noreg,_xls,_filename,_prevDiv, _result;
				var zipname = filename+'.zip';
				_maindiv.find('table').each(function(){
					_filename = filename+'.xls';
					if(empty($(this).attr('id'))){
						_rand = 'tbjs_'+Math.floor(Math.random() * 100);
						$(this).attr('id',_rand);
					}
					else{
						_rand = $(this).attr('id');
					}
					_xls = $(this).excelexportjs({
						containerid: _rand
						, datatype: 'table'
						, returnUri: true
						, worksheetName: filename
					});
					_zip.folder(_farm).file(_filename,_xls,{base64 : true});
				});
				_result = _zip.generate({type:"blob"});
				saveAs(_result,zipname);
			},


	showLspmTrs : function(elm,tipe){
		/* tampilkan semua rhk farm */
		var lf = this.getListFarm();
		var _form = $(elm).closest('form');
		var _status = _form.find('select[name=status_siklus]').val() || 'O';
		var _farm = _form.find('input[name=farm]').val() || '';
		var range_periode = $('select[name=range_periode]').val();
		var _sfarm, _namafarm = {};
		var _siklus = _form.find('input[name=siklus]').val() || '';

		$.when(lf).then(function(){
			var _f = [];
			for(var i in lf){
				_f.push(lf[i]['kode_farm']);
				_namafarm[lf[i]['kode_farm']] = lf[i]['nama_farm'];
			}
			_sfarm = empty(_farm) ? _f : [_farm];
			$.ajax({
				url : 'report/report/detailspm_trs',
				type : 'post',
				data : {farm : _sfarm, status_siklus : _status, siklus : _siklus, namafarm : _namafarm, tipe : tipe, range_periode : range_periode},
				dataType : 'html',
				async : false,
				beforeSend : function(){
				$('#detail_lspm').html(' Silakan tunggu ....');
				},
				success : function(data){
				$('#detail_lspm').html(data);
				}
			}).done(function(){
			/* hidden screen_3 */
			//$('.screen_3').hide();
			});
		})
	},

	next : function(elm){
		var _current = $(elm).data('current');
		var _min = $(elm).data('min');
		var _max = $(elm).data('max');
		var _next = _current + 1;
		if(_next <= _max){
			var _div = $(elm).closest('.table_paging');
			_div.find('table.page_'+_current).hide();
			_div.find('table.page_'+_next).show();
			$(elm).data('current',_next);
			var _prevBtn = $(elm).siblings();
			_prevBtn.data('current',_next);			
			if(_next == _max){
			//	$(elm).hide();
			}
		}
		if(_next > _min){			
			if(_prevBtn.is(':hidden')){				
			//	_prevBtn.show();
			}
		}				
		
	},

	prev : function(elm){
		var _current = $(elm).data('current');
		var _min = $(elm).data('min');
		var _max = $(elm).data('max');
		var _prev = _current - 1;
		if(_prev >= _min){
			var _div = $(elm).closest('.table_paging');
			_div.find('table.page_'+_current).hide();
			_div.find('table.page_'+_prev).show();
			$(elm).data('current',_prev);	
			var _nextBtn = $(elm).siblings();
			_nextBtn.data('current',_prev);	
			if(_prev == _min){
			//	$(elm).hide();
			}								
		}
		if(_prev < _max){			
			if(_nextBtn.is(':hidden')){
			//	_nextBtn.show();
			}
		}	
	},

	showDetailPengembalian : function(elm){
		var _ppsk = $(elm).data('ppsk');
		var url = 'report/kontrol_pemakaian_penjualan_glangsing/detailPpsk?ppsk='+_ppsk;
		var w = screen.width-300, h = 500;
		var left = (screen.width/2)-(w/2);
		var top = (screen.height/2)-(h/2);
		window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top="+top+", left="+left+", width="+w+", height="+h);
	}	
	};
