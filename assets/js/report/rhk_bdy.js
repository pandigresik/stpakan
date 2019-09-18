var Rhk = {
			listFarm : null,
			add_datepicker : function(elm,options){
				elm.datepicker(options);
			},
			getListFarm : function(){
				var tmp;
				if(empty(Rhk.listFarm)){
					$.ajax({
						type : 'get',
						url : 'report/report/userFarm',
						data : {},
						dataType : 'json',
						async:false,
						cache : true,
					}).done(function(data){
						if(data.status){
							Rhk.listFarm = data.content;
							tmp = Rhk.listFarm;
						}
					});
				}
				else{
					tmp = Rhk.listFarm;
				}
				return tmp;
			},
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

			showListFarm : function(elm,tipe){
				var _error = 0;
				var _form = $(elm).closest('form');
				var tanggal = {}, where;
				var _status = _form.find('select[name=status_siklus]').val() || 'O';
				var _farm = _form.find('input[name=farm]').val() || '';
				var _siklus = _form.find('input[name=siklus]').val() || '';
				if(!_error){
					switch(_status){
						case 'C':
							/*
							var awalDocin = Config._tanggalDb(tanggal['startDate'],' ','-');
							var akhirDocin = Config._tanggalDb(tanggal['endDate'],' ','-');
								where = { where : 'tgl_doc_in between \''+awalDocin+'\' and \''+akhirDocin+'\' and ks.status_siklus = \'C\''};
							*/
							where = { where : 'ks.status_siklus = \'C\' and ks.kode_farm in (\''+_farm+'\') and ks.kode_siklus in (\''+_siklus+'\')'};
							break;
						case 'O':
							where = { where : 'ks.status_siklus = \'O\''};
							break;
					}

					/* tampilkan daftar farm */
					$.ajax({
						url : 'home/kertas_kerja/list_farm_all',
						type : 'post',
						data : where,
						dataType : 'json',
						async : false,
						success : function(data){
							if(data.status){
								var _dataFarm = {}, _tmp;
								for(var i in data.content){
									_tmp = data.content[i];
									if(_dataFarm[_tmp['kode_siklus']] == undefined){
										_dataFarm[_tmp['kode_siklus']] = {farm : _tmp['kode_farm'], periode_siklus : _tmp['periode_siklus'], strain : _tmp['kode_strain'], nama_farm : _tmp['nama_farm'], jml_kandang : 0};
									}
									_dataFarm[_tmp['kode_siklus']]['jml_kandang']++;
								}
								var _content = [];
								var _content_str = '';
								if(!empty(_dataFarm)){
									for(var _x in _dataFarm){
										_content.push('<div onclick="Rhk.showKandang(this,\''+tipe+'\')" data-kodefarm="'+_dataFarm[_x]['farm']+'" class="pointer alert alert-info" data-kodesiklus="'+_x+'">');
										_content.push('Farm '+_dataFarm[_x]['nama_farm']+' periode siklus '+_dataFarm[_x]['periode_siklus']+' ('+_dataFarm[_x]['strain']+','+_dataFarm[_x]['jml_kandang']+' Kandang)');
										_content.push('</div>');
									}
									_content_str = _content.join('');
								}
								else{
									_content_str = 'Data tidak ditemukan';
								}
								var _leveluser = $.trim($('#divleveluserinfo').text());
								if(_leveluser == 'KA'){
									$('div.section .panel-body').html(_content_str).children('div');	
								}else{
							//		$('div.section .panel-body').html(_content_str).children('div').click();
									$('div.section .panel-body').html(_content_str).children('div');
								}

							}
						}
					});
				}
			},

			showKandang : function(elm,tipe){
				var kodefarm = $(elm).data('kodefarm');
				var awalDocin = $(elm).data('awaldocin');
				var akhirDocin = $(elm).data('akhirdocin');
				var kodeSiklus = $(elm).data('kodesiklus');
				var url = 'home/kertas_kerja/list_kandang_all';
				var _action;
				switch(tipe){
					case 'rhk':
						_action = 'Rhk.showDetailRhk(this,\''+tipe+'\')';
						break;
					case 'lsam':
						_action = 'Rhk.showDetailRhk(this,\''+tipe+'\')';
						break;
					case 'lsam_flock':
						_action = 'Rhk.showDetailLsam(this,\''+tipe+'\')';
						url = 'home/kertas_kerja/list_flock_all';
						break;
					case 'lsam_farm':
						_action = 'Rhk.showDetailLsam(this,\''+tipe+'\')';
						url = 'home/kertas_kerja/list_farm';
						break;
					case 'lspm':
						_action = 'Rhk.showDetailLspm(this,\''+tipe+'\')';
						break;
				}
				var where = { where : ' ks.kode_farm=\''+kodefarm+'\' and ks.kode_siklus=\''+kodeSiklus+'\'', action : _action, tipe : tipe};

				/* cek apakah detailnya sudah tampil atau belum */
				var _detailElm = $(elm).next('div.detailkandang');
				if(!_detailElm.length){
					/* load dari server */
					$.ajax({
						type : 'post',
						dataType : 'html',
						data : where,
						url : url,
						success : function(data){
							$(data).insertAfter($(elm));
						},
					}).done(function(){
						//$(elm).next('div.detailkandang').find('.div_detailkandang').click();
                                                   $(elm).next('div.detailkandang').find('.div_detailkandang');

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

	showAttachmentRhk : function(elm){
		var noreg = $(elm).data('noreg');
		var tgltransaksi = $(elm).data('tgltransaksi');
		var url = 'report/report/showImage?noreg='+noreg+'&tgl='+tgltransaksi;
		var w = screen.width-300, h = 500;
		var left = (screen.width/2)-(w/2);
		var top = (screen.height/2)-(h/2);
		window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top="+top+", left="+left+", width="+w+", height="+h);
	}

};
