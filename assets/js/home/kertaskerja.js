'use strict';
var KertasKerja = {
	listChart : {},
	defaultKonversi : {'skp' : 'sak', 'kons' : 'sak', 'b_skp' : 'sak', 'j_skp' : 'sak', 'b_kons' : 'sak', 'j_kons' : 'sak','c_skp' : 'sak', 'c_kons' : 'sak'},
	sliderOption : {
		 type: "double",
	        values: [

	        ],
	        from : 1,
	        to : 5,

	},
	getSatuanKonversi : function(){
		if(localStorage.getItem('konversiSatuanStpakan') === null){
			localStorage.setItem('konversiSatuanStpakan',JSON.stringify(this.defaultKonversi));
		}
		return localStorage.getItem('konversiSatuanStpakan');
	},
	setSatuanKonversi : function(id,value){
		var _data = JSON.parse(this.getSatuanKonversi());
		_data[id] = value;
		localStorage.setItem('konversiSatuanStpakan',JSON.stringify(_data));
	},
	showKandang : function(elm){
		var kodefarm = $(elm).data('kodefarm');
		var awalDocin = $(elm).data('awaldocin');
		var akhirDocin = $(elm).data('akhirdocin');
		var kodeSiklus = $(elm).data('kodesiklus');
		var where = { where : 'tgl_doc_in between \''+awalDocin+'\' and \''+akhirDocin+'\' and ks.kode_farm=\''+kodefarm+'\' and ks.kode_siklus=\''+kodeSiklus+'\''};

		/* cek apakah detailnya sudah tampil atau belum */
		var _detailElm = $(elm).next('div.detailkandang');
		if(!_detailElm.length){
			/* load dari server */
			$.ajax({
				type : 'post',
				dataType : 'html',
				data : where,
				url : 'home/kertas_kerja/list_kandang_all',
				success : function(data){
					$(data).insertAfter($(elm));
				},
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
	showKertasKerja : function(elm,grafik){
		var noreg = $(elm).data('noreg');
		var par = $(elm).closest('div.section.detailkandang').prev();

		var listGrafik = par.data('listgrafik').split(',');
		var _tglChickIn = new Date(Config._tanggalDb($(elm).data('tglchickin'),' ','-'));

		var _rhkTerakhir = !empty($(elm).data('rhk_terakhir')) ? $(elm).data('rhk_terakhir') : $('#tanggal_server').data('tanggal_server');
		var _statusKandang = new Date($(elm).data('statussiklus')); /* menandakan apakah kandang masih open (O) atau sudah close (C)  */
		var _tglKebAkhir = new Date(_rhkTerakhir);
		if(_statusKandang == 'C'){
			var _tglKebAwal = new Date(_rhkTerakhir);
		}
		else{
			var _tglKebAwal = new Date($('#tanggal_server').data('tanggal_server'));
			var _tglKebAkhir = new Date($('#tanggal_server').data('tanggal_server'));
		}

		var _umurSaatIni = Config.get_selisih(_tglChickIn,_tglKebAwal);
		if(_umurSaatIni < -14){
			_tglKebAwal.setDate(_tglKebAwal.getDate() - 14);
			_tglKebAkhir.setDate(_tglKebAkhir.getDate() + 14 );
		}
		else{
			var _sisaHari = _umurSaatIni % 7;
			_tglKebAwal.setDate(_tglKebAwal.getDate() - (14 + _sisaHari));
			_tglKebAkhir.setDate(_tglKebAkhir.getDate() +  _sisaHari);
		}

		/* cek apakah detailnya sudah tampil atau belum */
		var _detailElm = $(elm).next('div.kertaskerja');
		var _info_load_bawah = $('<div class="badge btn load_info_bawah" onclick="KertasKerja.loadDataSelanjutnya(this)">Lihat data selanjutnya</div>');
		var _info_load_atas = $('<div class="badge  btn load_info_atas" onclick="KertasKerja.loadDataSebelumnya(this)">Lihat data sebelumnya</div>');
		var _tampilkan_tabular = 1;
		if(grafik && !in_array('tabular',listGrafik)){
			_tampilkan_tabular = 0;
		}
		if(!_detailElm.length){
			$('.kertaskerja').hide();
			/* load dari server */
			if(_tampilkan_tabular){
				$.ajax({
					beforeSend : function(){
						$('<div class="section kertaskerja"><span class="info">Sedang loading...</span></div>').insertAfter($(elm));
					},
					type : 'post',
					dataType : 'json',
					async : false,
					data : {noreg : noreg,kebutuhanawal : Config._getDateStr(_tglKebAwal),kebutuhanakhir : Config._getDateStr(_tglKebAkhir), konversi : JSON.parse(KertasKerja.getSatuanKonversi())},
					url : 'home/kertas_kerja/list_kertas_kerja',
					success : function(data){
						var _data = KertasKerja.groupingRowspan(data);
						if(!empty(data.kebutuhan_awal)){
							$(elm).data('kebutuhanawal',data.kebutuhan_awal);
						}
						if(!empty(data.kebutuhan_akhir)){
							$(elm).data('kebutuhanakhir',data.kebutuhan_akhir);
						}

						$(_data).appendTo($(elm).next('div.kertaskerja'));
						$(elm).next('div.kertaskerja').find('span.info').remove();
					},
				}).done(function(){
					/**/
					$(elm).next('div.kertaskerja').find('table:first')
					.scrollabletable2({
						// 'padding_right' : 18,
					      'max_height_scrollable' : 500,
					      'max_width' : $(elm).next('div.section.kertaskerja').innerWidth(),
					      'scroll_horizontal' : 1,
					     // 'tambahan_top_left' : 3
					})

					.parent().scroll(function(e){

						var _posisi = $(this).scrollTop();
						var _id = $(this).attr('id');
						var _max_scroll = document.getElementById(_id).scrollHeight - $(this).height();
						var _min_scroll = 0;
						var _elm;
						if( _min_scroll == _posisi){
							_elm = $(this).siblings('.load_info_atas').not('div.div_grafik');;
							if((_elm).is(':hidden')){
								_elm.fadeIn();
							};
						}
						else if(_max_scroll == _posisi){
							_elm = $(this).siblings('.load_info_bawah').not('div.div_grafik');
							if((_elm).is(':hidden')){
								_elm.fadeIn();
							};
						}
						else{
							$(this).siblings(':visible').not('div.div_grafik').fadeOut();
						}
					});
					$(elm).next('div.kertaskerja').append(_info_load_atas).append(_info_load_bawah);
					_info_load_atas.css({
						position : 'absolute',
						top : $(elm).next('div.kertaskerja').find('table.kertas_kerja:first thead').height(),
						right : '1%',
						display : 'none',
					});
					_info_load_bawah.css({
						position : 'relative',
						bottom : '9%',
						left :'89%',
						display : 'none',
					});

					/* semua yang memiliki data-no_pp dan nilainya tidak kosong, maka jika diklik akan menunjukkan cell lain yang berkaitan	*/
					$(elm).next('div.kertaskerja').find('table.kertas_kerja:first tbody td[data-no_pp*="/"]').each(function(){
						$(this).bind('mouseenter mouseleave',function(){
							KertasKerja.showKaitan($(this),$(this).data('no_pp'));
						});
					});

					/* set scrollTop untuk document */
			//		var _scroll_body = $(document).height();
					var _posisi_kertas_kerja = $(elm).next('div.section.kertaskerja').position().top;
					$(document).scrollTop(_posisi_kertas_kerja - 100);
				});
			}
			/* tampilkan grafik */
			if(!empty(listGrafik)){
				var jenisGrafik = '';
				var idElm;
			//	var umurAwal = parseInt(Config.get_selisih(_tglChickIn,_tglKebAwal)/7);
				var umurAkhir = parseInt(Config.get_selisih(_tglChickIn,_tglKebAkhir)/7);
				var param = {
						umur_awal : 0 ,
						umur_akhir : umurAkhir,
						rhk_terakhir : _rhkTerakhir,
						doc_in : Config._tanggalDb($(elm).data('tglchickin'),' ','-'),
						keb_awal : Config._convertTgl(Config._getDateStr(_tglChickIn)),
						keb_akhir : Config._convertTgl(Config._getDateStr(_tglKebAkhir)),
						noreg : noreg,
						standard_betina : $(elm).data('kodestd_b'),
						standard_jantan : $(elm).data('kodestd_j')
				};

				var div_grafik = [];
				var link_grafik = [];
				var tmp_grafik = [];
				for(var i in listGrafik){
					jenisGrafik = listGrafik[i];
					if((!empty(jenisGrafik)) && (jenisGrafik != 'tabular')){
						idElm = jenisGrafik+'_'+noreg.replace(/\//g,'');
					//	param['grafik'] = jenisGrafik;
						div_grafik.push(this.createDivChart(idElm));
						tmp_grafik.push({"grafik": jenisGrafik, "elm" : idElm, "param" : param});
					//	link_grafik.push("<span class='btn btn-default' onclick='KertasKerja.showChart(event,\""+jenisGrafik+"\",\""+idElm+"\","+JSON.stringify(param)+")'>"+jenisGrafik+"</span>");
					}
				}

				if(!_tampilkan_tabular){
					$('<div class="section kertaskerja"><div class="div_grafik">'+div_grafik.join(' ')+'</div></div>').insertAfter($(elm));
				}
				else{
					$('<div class="div_grafik">'+div_grafik.join(' ')+'</div>').appendTo($(elm).next('div.kertaskerja'));
				}

				for(var i in tmp_grafik){
					KertasKerja.showChart(tmp_grafik[i]['grafik'],tmp_grafik[i]['elm'],tmp_grafik[i]['param']);
				}


			//	$(elm).append('<span class="pull-right">'+link_grafik.join(' ')+'</span>');

			}
		}
		else{
			if(_detailElm.is(':visible')){
				_detailElm.hide();

			}
			else{
				_detailElm.show();
				$('.kertaskerja').not(_detailElm).hide();

			}
		}
	},

	showKertasKerjaBdy : function(elm){
		var noreg = $(elm).data('noreg');
		var _tglChickIn = Config._tanggalDb($(elm).data('tglchickin'),' ','-');

		var _detailElm = $(elm).next('div.kertaskerja');

		if(!_detailElm.length){
			$('.kertaskerja').hide();
			$.ajax({
				beforeSend : function(){
					$('<div class="section kertaskerja"><span class="info">Sedang loading...</span></div>').insertAfter($(elm));
				},
				type : 'post',
				dataType : 'json',
				async : false,
				data : {noreg : noreg, docin : _tglChickIn, konversi : JSON.parse(KertasKerja.getSatuanKonversi())},
				url : 'home/kertas_kerja/list_kertas_kerja_bdy',
				success : function(data){
						$(data.content).appendTo($(elm).next('div.kertaskerja'));
						$(elm).next('div.kertaskerja').find('span.info').remove();
				},
			}).done(function(){
				/**/
				$(elm).next('div.kertaskerja').find('table:first')
				.scrollabletable2({
					// 'padding_right' : 18,
				      'max_height_scrollable' : 500,
				      'max_width' : $(elm).next('div.section.kertaskerja').innerWidth(),
				      'scroll_horizontal' : 1,
				     // 'tambahan_top_left' : 3
				});
			});
		}
		else{
			if(_detailElm.is(':visible')){
				_detailElm.hide();

			}
			else{
				_detailElm.show();
				$('.kertaskerja').not(_detailElm).hide();

			}
		}

	},
	showKaitan : function(elm,no_pp){
		$(elm).closest('tbody').find('td[data-no_pp*="/"][data-no_pp!="'+no_pp+'"]').toggleClass('remeng');
	},
	loadDataSelanjutnya : function(elm){
		/* elm kandang */
		var _elmKandang = $(elm).closest('.section.kertaskerja').prev();
		var _noreg = _elmKandang.data('noreg');
		var _saatIni = new Date($('#tanggal_server').data('tanggal_server'));
		var _kebutuhanAkhir = _elmKandang.data('kebutuhanakhir');
		/* tgl kebutuhan awal selanjutnya adalah kebutuhan akhir + 1 */
		var _tglKebAwal = new Date(Config._convertTgl(_kebutuhanAkhir, ' ', '-'));
		_tglKebAwal.setDate(_tglKebAwal.getDate() + 1);
		var _tglKebAkhir = new Date(Config._convertTgl(_kebutuhanAkhir, ' ', '-'));
		/* tgl kebutuhan akhir selanjutnya adalah kebutuhan akhir + 14 */
		_tglKebAkhir.setDate(_tglKebAkhir.getDate() + 14);
		_saatIni.setDate(_saatIni.getDate() + 7);

		if(_tglKebAkhir <= _saatIni){
			$.ajax({
				beforeSend : function(){
					$(elm).fadeOut();
				},
				type : 'post',
				dataType : 'json',
				data : {noreg : _noreg, kebutuhanawal : Config._getDateStr(_tglKebAwal),kebutuhanakhir : Config._getDateStr(_tglKebAkhir), konversi : JSON.parse(KertasKerja.getSatuanKonversi())},
				url : 'home/kertas_kerja/list_kertas_kerja_sebagian',
				success : function(data){
					/* tambahkan ke tbody pada baris terakhir */
					var _wrap = $(elm).siblings('[id$="table_wrapper_kj"]');
					var _anyar = KertasKerja.groupingRowspan(data);
					var _tr;
					var _left_tbody = _wrap.find('[id$="left_section_virtual"] table.kertas_kerja tbody');
					$.each(_anyar,function(){
						_tr = $('<tr></tr>');
						$(this).children('td').each(function(){
							if($(this).hasClass('ftl')){
								_tr.append($(this).clone());
							}
						});
						_left_tbody.append(_tr);
					});
					_wrap.find('table.kertas_kerja:first tbody').append(_anyar);
					_anyar.find('td[data-no_pp*="/"]').each(function(){
						$(this).bind('mouseenter mouseleave',function(){
							KertasKerja.showKaitan($(this),$(this).data('no_pp'));
						});
					});

					KertasKerja.setUlangPosisiHeader(_wrap);
				},
			}).done(function(){
				/* update data kebutuhan akhir pada elemen kandang */
				_elmKandang.data('kebutuhanakhir',Config._getDateStr(_tglKebAkhir),'-',' ');
			});
		}
		else{
			toastr.warning('Tanggal kebutuhan akhir sudah paling terbaru');
		}
	},
	loadDataSebelumnya : function(elm){
		/* elm kandang */
		var _elmKandang = $(elm).closest('.section.kertaskerja').prev();
		var _noreg = _elmKandang.data('noreg');
		var _kebutuhanAwal = _elmKandang.data('kebutuhanawal');

		/* tgl kebutuhan awal selanjutnya adalah kebutuhan akhir - 15 */
		var _tglKebAwal = new Date(Config._convertTgl(_kebutuhanAwal, ' ', '-'));
		_tglKebAwal.setDate(_tglKebAwal.getDate() - 14);
		var _tglKebAkhir = new Date(Config._convertTgl(_kebutuhanAwal, ' ', '-'));
		/* tgl kebutuhan akhir selanjutnya adalah kebutuhan akhir - 1 */
		_tglKebAkhir.setDate(_tglKebAkhir.getDate() - 1);

		$.ajax({
			beforeSend : function(){
				$(elm).fadeOut();
			},
			type : 'post',
			dataType : 'json',
			data : {noreg : _noreg, kebutuhanawal : Config._getDateStr(_tglKebAwal),kebutuhanakhir : Config._getDateStr(_tglKebAkhir)},
			url : 'home/kertas_kerja/list_kertas_kerja_sebagian',
			success : function(data){
				/* tambahkan ke tbody pada baris pertama */
				var _wrap = $(elm).siblings('[id$="table_wrapper_kj"]');
				var _anyar = KertasKerja.groupingRowspan(data);
				var _tr;
				var _left_tbody = _wrap.find('[id$="left_section_virtual"] table.kertas_kerja tbody tr:first');
				$.each(_anyar,function(){
					_tr = $('<tr></tr>');
					$(this).children('td').each(function(){
						if($(this).hasClass('ftl')){
							_tr.append($(this).clone());
						}
					});
					_tr.insertBefore(_left_tbody);
				});
				_anyar.insertBefore(_wrap.find('table.kertas_kerja:first tbody tr:first'));
				_anyar.find('td[data-no_pp*="/"]').each(function(){
					$(this).bind('mouseenter mouseleave',function(){
						KertasKerja.showKaitan($(this),$(this).data('no_pp'));
					});
				});

				KertasKerja.setUlangPosisiHeader(_wrap);
			},
		}).done(function(){
			/* update data kebutuhan akhir pada elemen kandang */
			_elmKandang.data('kebutuhanawal',Config._getDateStr(_tglKebAwal),'-',' ');
		});
	},
	setUlangPosisiHeader : function(elm){
		var _wrap = elm;
		var _thead_table = _wrap.find('table.kertas_kerja:first thead tr');
		var _tr;
		_wrap.find('[id$="div_header_virtual_kj"] table.kertas_kerja thead tr').each(function(i){
			_tr = _thead_table.eq(i).find('th');
			$(this).find('th').each(function(i){
				$(this).find('div:first').css({
					'width' : _tr.eq(i).width(),
				});
			});
		});
	},
	showLHK : function(elm){
		var tgl_lhk = $(elm).data('tgl_lhk');
		var no_reg = $(elm).data('no_reg');
		var doc_in = $(elm).data('doc_in');
		var url = 'home/home/view_lhk?no_reg='+no_reg+'&tgl_lhk='+tgl_lhk+'&doc_in='+doc_in;
		var w = screen.width * .9, h = 500;
		var left = (screen.width/2)-(w/2);
		var top = (screen.height/2)-(h/2);
		window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top="+top+", left="+left+", width="+w+", height="+h);
	},
	showLHKBdy : function(elm){
		var tgl_lhk = $(elm).data('tgl_lhk');
		var no_reg = $(elm).data('no_reg');
		var doc_in = $(elm).data('doc_in');
		var url = 'home/home/view_lhk_bdy?no_reg='+no_reg+'&tgl_lhk='+tgl_lhk+'&doc_in='+doc_in;
		var w = screen.width * .9, h = 500;
		var left = (screen.width/2)-(w/2);
		var top = (screen.height/2)-(h/2);
		window.open(url, "_blank", "toolbar=no, scrollbars=yes, resizable=yes, top="+top+", left="+left+", width="+w+", height="+h);
	},
	groupingRowspan : function(data){
		var _data = $(data.content);
		if(data.level == 'PD'){

			var _grouping_pp = {};
			var _grouping_col = ['app','edo','rk','sj','sdo','ttk','spn'];
			var _pp,_pekan;
			_data.find('td[data-col=app]').each(function(){
				_pp = $(this).data('no_pp');
				_pekan = $(this).data('pekan');
				if(!empty(_pp)){
					if(_grouping_pp[_pp] === undefined){
						_grouping_pp[_pp] = {};

					}
					if(_grouping_pp[_pp][_pekan] === undefined){
						_grouping_pp[_pp][_pekan] = {'jml' : 1};
					}

					_grouping_pp[_pp][_pekan]['jml']++;

				}
			});
			var _tmp_td, _tr_tmp;
			for(var i in _grouping_pp){
				var _no_pp = i;
				var _perpekan = _grouping_pp[_no_pp];

				for(var p in _perpekan){

					_tmp_td = _data.find('td[data-no_pp="'+_no_pp+'"][data-pekan="'+p+'"]:first');
					_tr_tmp = _tmp_td.closest('tr');
					/*
					for(var _c in _grouping_col){
						_tr_data[i][p][_grouping_col[_c]] = _tr_tmp.find('td[data-col="'+_grouping_col[_c]+'"]').html();
					}
					*/
				}
			}

			/* saatnya digrouping */
			var _rw = 1;
			for(var i in _grouping_pp){
				var _no_pp = i;
				var _perpekan = _grouping_pp[_no_pp];

				for(var p in _perpekan){

					_tmp_td = _data.find('td[data-no_pp="'+_no_pp+'"][data-pekan="'+p+'"]:first');
					_tr_tmp = _tmp_td.closest('tr');

					for(var _c in _grouping_col){
						_rw = _data.find('td[data-no_pp="'+_no_pp+'"][data-pekan="'+p+'"][data-col="'+_grouping_col[_c]+'"]').length * 2;
						_tr_tmp.find('td[data-col="'+_grouping_col[_c]+'"]').attr('rowspan',_rw);
						_data.find('td[data-no_pp="'+_no_pp+'"][data-pekan="'+p+'"][data-col="'+_grouping_col[_c]+'"]:not(:first)').remove();
					}
				}
			}

			//$(elm).html(_data);
		}
		/* tambahkan tooltip*/
		_data.find('span[data-toogle=tooltip]').tooltip();
		return _data;
	},
	riwayatPP : function(elm){
		var _tr = $(elm).closest('tr');
		var _jk = $(elm).data('jk');
		var _no_pp = $(elm).data('no_pp');

		var _no_reg = _tr.closest('div.kertaskerja').prev().data('noreg');
		$.ajax({
			type : 'post',
			data : {no_pp : _no_pp, no_reg : _no_reg, jk : _jk},
			async : false,
			dataType : 'html',
			url : 'home/kertas_kerja/riwayat_pp',
			success : function(data){
				var _options = {
						title : '<div class="text-center"> Riwayat Pengajuan PP</div>',
						message : data,
						className : 'largeWidth',
					/*	buttons : {
							Ok : {
								label : 'Tutup',
								className : '',
								callback : function(e){

								}
							}
						},
					*/
					};
				bootbox.dialog(_options);
			}
		});
	},
	showHideColumn : function(elm,target){
	//	.lhk.waktu
		var _div = $(elm).closest('div.kertaskerja.section');
		if($(elm).find('i').hasClass('glyphicon-plus-sign')){
			_div.find(target).show();
			$(elm).find('i').addClass('glyphicon-minus-sign').removeClass('glyphicon-plus-sign');
		/*	var _colspan = _div.find('tr.rekap td:first').attr('colspan');
			_div.find('tr.rekap td.ftl').attr('colspan',parseInt(_colspan) + 1);
		*/
		}
		else{
			_div.find(target).hide();
			$(elm).find('i').removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
		/*	var _colspan = _div.find('tr.rekap td:first').attr('colspan');
			_div.find('tr.rekap td.ftl').attr('colspan',parseInt(_colspan) - 1);
		*/
		}

	},
	konversiSatuan : function(elm,target){
		var _pengali = parseInt($(elm).find('option:selected').data('pengali'));
		var _nilaiTmp;
		var _id = $(elm).attr('name');
		var _nilai = $(elm).val();
		this.setSatuanKonversi(_id,_nilai);
		$(target).each(function(){
			_nilaiTmp = number_format($(this).data('asli') * _pengali,3,',','.');
			$(this).text(_nilaiTmp);
		});
	},
	showListFarm : function(elm,grafik){
		var _error = 0;
		var $form = $(elm).closest('div.form');
		var list_grafik = [];
		var tanggal = {};
		/* reset nilai dari listChart */
		KertasKerja.listChart = {};
		/* tanggal periode chick in harus diisi */
		$form.find('input[name$=Date]').each(function(i,v){
			tanggal[v.name] = v.value;
			if(empty(v.value)){
				_error++;
				toastr.warning('Periode Chick-in harus diisi');
			}
		});

		/* jika grafik bernilai 1 maka yang login adalah presdir */
		if(grafik){
			/* salah satu cekbox harus dipilih */
			var _filter = $('input.input-filter:checked');
			if(!_filter.length){
				_error++;
				toastr.warning('Minimal salah satu pilihan output harus dipilih');
			}
			else{
				_filter.each(function(i,elm){
				/*	var t = /^grafik/gi;
					if(t.test(elm.name)){
						list_grafik.push(elm.value);
					}
				*/
					list_grafik.push(elm.value);
				});
			}
		}

		if(!_error){
			var awalDocin = Config._tanggalDb(tanggal['startDate'],' ','-');
			var akhirDocin = Config._tanggalDb(tanggal['endDate'],' ','-');
			var where = { where : 'tgl_doc_in between \''+awalDocin+'\' and \''+akhirDocin+'\''};
			/* jika grafik bernilai 0 maka yang login adalah kepala farm */
			if(!grafik){
				var farm_user = $('#popup_gantipassword').data('farm');
				where['where'] += ' and ks.kode_farm=\''+farm_user+'\'';
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
								_content.push('<div onclick="KertasKerja.showKandang(this)" data-kodefarm="'+_dataFarm[_x]['farm']+'" data-awaldocin="'+awalDocin+'"  data-akhirdocin="'+akhirDocin+'" class="pointer alert alert-info" data-kodesiklus="'+_x+'" data-listgrafik="'+list_grafik.join(',')+'">');
								_content.push('Farm '+_dataFarm[_x]['nama_farm']+' periode siklus '+_dataFarm[_x]['periode_siklus']+' ('+_dataFarm[_x]['strain']+','+_dataFarm[_x]['jml_kandang']+' Kandang)');
								_content.push('</div>');
							}
							_content_str = _content.join('');
						}
						else{
							_content_str = 'Data tidak ditemukan';
						}
						$('div.section .panel-body').html(_content_str);
					}
				}
			});
		}
	},
	createDivChart : function(elm){
		return "<div id='"+elm+"' style='display:none;border:1px solid gray;margin:10px;padding-bottom:50px' class='row new-line'><div class='col-md-6'><div id='"+elm+"_j'></div></div><div class='col-md-6'><div id='"+elm+"_b'></div></div></div>";
	},
	showChart : function(jenisChart,elm,param){
		var _target = $('div[id ^='+elm+']');

	//	$('div[id^=grafik]').not(_target).hide();
		if(!_target.hasClass('c3')){
			this.createChart(jenisChart,elm,param);
			if(_target.is(':hidden')){
				_target.show();
			}
		}
		else{
			if(_target.is(':hidden')){
				_target.show();
			}
			else{
				_target.hide();
			}
		}

	//	e.stopPropagation();
	},

	createChart : function(jenisChart,elm,param){
		param['grafik'] = jenisChart;
		/* ambil data dari database */
		$.ajax({
			url : 'home/kertas_kerja/grafik',
			type : 'post',
			beforeSend : function(){
				//$('#'+elm).find('span.info').html('Sedang loading data ........');
			},
			data : param,
			cache : true,
			dataType : 'json',
			success : function(data){
				$('#'+elm).find('span.info').html('');
			},
		}).done(function(data){
			var _data = [], _axis, _std, _label, _ylabel, _tmp_data;
			if(data.status){
				var _jk = {b : 'Betina', j : 'Jantan'};
				for(var i in data.data){
					_data = [];
					for(var y in data.data[i]){
						_tmp_data = data.data[i][y];
						var _new_tmp = [];
						for(var v in _tmp_data){
							if(v == 0){
								_new_tmp.push(new Date(_tmp_data[v]));
							}
							else _new_tmp.push(parseFloat(_tmp_data[v]));
						}
						_data.push(_new_tmp);
					}
					_label = data.label;
					_ylabel = data.legend_y;
			/* generate grafik */

			KertasKerja.listChart[elm+'_'+i] = new Dygraph(
			    document.getElementById(elm+'_'+i),
			    _data,
			    	{labels: _label,
			    	 xlabel:'Umur Tanggal',
			    	 ylabel : _ylabel,
			    	 strokeWidth: 1.5,
			    	 title: data.title+' '+_jk[i],
				//     rollPeriod: 7,
				//     showRoller: true,
				     legend: 'always',
				  	 axes : {
				  		 x : {
				  			valueFormatter: function(ms) {
				  				var doc_in = new Date(param['doc_in']);
				  				var _tgl = new Date(ms);
				  				var _umurSaatIni = Config.get_selisih(doc_in,_tgl);
				  				var _hari = _umurSaatIni % 7 ;
				  				var _minggu = parseInt(_umurSaatIni / 7) ;
				                  return Config._tanggalLocal(Config._getDateStr(_tgl),'-',' ');
				                },

				  			axisLabelFormatter : function(ms){
				  				var doc_in = new Date(param['doc_in']);
				  				var _tgl = new Date(ms);
				  				var _umurSaatIni = Config.get_selisih(doc_in,_tgl);
				  				var _hari = _umurSaatIni % 7 ;
				  				var _minggu = parseInt(_umurSaatIni / 7) ;
				                return _minggu +'M '+_hari+' H <br />'+Config._tanggalLocal(Config._getDateStr(_tgl),'-',' ');

				  			},
				  			 axisLabelWidth: 150
				  		 }
				  	 }
				    }
				);

			/* tambahkan title tiap grafik */

		//	 $('<div class="text-center" style="margin-top:30px">'+data.title+' '+_jk[i]+'</div>').insertBefore($('#'+elm+'_'+i).children().eq(0));
				}

			}
			else{
				var _jk = {b : 'Betina', j : 'Jantan'};
				for(var i in _jk){
					if(KertasKerja.listChart[elm+'_'+i] !== undefined){
						KertasKerja.listChart[elm+'_'+i].hide();
					}
				}
				toastr.warning('Data tidak ditemukan');
				$('#'+elm).addClass('c3').html('Data tidak ditemukan');
			}

		});
	}
};

$(function(){
	if($("input[name=startDate]").length){
		$("input[name=startDate]").datepicker({
		    //  defaultDate: "+1w",
			  maxDate : 'today',
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		        $( "input[name=endDate]" ).datepicker( "option", "minDate", selectedDate );
		      }
		   });
	}
	if($("input[name=endDate]").length){
		 $("input[name=endDate]").datepicker({
		    //  defaultDate: "+1w",
			  maxDate : 'today',
		      dateFormat : 'dd M yy',
		      onClose: function( selectedDate ) {
		        $( "input[name=startDate]" ).datepicker( "option", "maxDate", selectedDate );
		    }
		  });
	}
});
