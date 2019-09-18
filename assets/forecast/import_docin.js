var Import = {
	dataDOCIn : {},
	kandangSiklus : {},
	awalDOCIn : null,
	thnDOCIn : null,
	umurPanen : {min : 20, max : 42},
	periodeSiklus : {},
	setKandangSiklus : function(ks){
		this.kandangSiklus = ks;
	},
	getKandangSiklus : function(){
		return this.kandangSiklus;
	},
	setThnDOCIn : function(thn){
		this.thnDocIn = thn;
	},
	getAwalDOCIn : function(){
		return this.getValidDate(this.awalDOCIn);
	},
	setAwalDOCIn : function(awalDocin){
		this.awalDOCIn = awalDocin;
	},
	getThnDataDocIn : function(){
		return this.thnDocIn;
	},
	getDataDocIn : function(){
		return this.dataDOCIn;
	},
	setDataDocIn : function(obj){
		this.dataDOCIn = obj;
	},
	setPeriodeSiklus : function(periodeSiklus){
		this.periodeSiklus = periodeSiklus;
	},
	getPeriodeSiklus : function(){
		return this.periodeSiklus;
	},
	showDiv : function(target){
		var t = $(target);
		if(t.is(':hidden')){
			t.show();
		}
		else{
			t.hide();
		}
	},
	bacaXLS : function(t,idTable){
		$(idTable).find('tbody').html('');
		var files = $(t).get(0).files;
		 var result = [];
		 var i,f;
		 for (i = 0, f = files[i]; i != files.length; ++i) {
		 var reader = new FileReader();
		 var name = f.name;
		 reader.onload = function(e) {
		 var data = e.target.result;
		 var _error = 0;
		 var workbook = XLSX.read(data, {type: 'binary'});
		 var _y = Import.to_json(workbook);
		 var _k = Import.periksaNamaKolom(_y);
		 _error += _k.err;
		 if(_k.err){
			 toastr.warning(_k.msg.join('</br>'),'Peringatan');
		 }
		 if(!_error){
			 var _p = Import.periksaDOCin(_y);
			 _error += _p.err;
			 if(_error){
				 for(var _n in _p.msg){
					toastr.error(_p.msg[_n]);
				 }
			 }
		 }

		 	if(!_error){
		 		var _isiTabel = Import.buatIsiTabel(Import.getDataDocIn());
		 		$(idTable).find('tbody').html(_isiTabel);
			 }

		 };

	    reader.readAsBinaryString(f);
	  }
	},
	/* pastikan memiliki tanggal doc in yang tahunnya sama
	 * sekalian kumpulkan data mengenai periode siklus dll
	 * */
	periksaDOCin : function(obj){
		var _error = 0;
		var _message = [], _periodeSiklus = {}, _tmpSiklus,_tmpFarm,_tmpStrain, _farmKandangSiklus = [],_tmpfks;
		var _thnDocIn,_tmpTahun,_baris,_dataDOCin = {}, _bv,_up,_awalDocin = null;
		for(var _sn in obj){
			var _x = obj[_sn];
			for(var _d in _x){
				_baris = parseInt(_d) + 2;
				_bv = this.periksaIsiBaris(_x[_d]);
				if(_bv.err){
					_error += _bv.err;
					_message.push('Data pada baris '+ _baris +' '+_bv.msg.join('<br />'));
				//	console.log('Data pada baris '+ _baris +' '+_bv.msg.join('<br />'));
				}
				_up = this.periksaUmurPanen(_x[_d]);
				_tmpfks = _x[_d]['Farm']+'_'+_x[_d]['Kandang']+'_'+_x[_d]['Siklus'];
				if(in_array(_tmpfks,_farmKandangSiklus)){
					_error++;
					_message.push('Data pada baris '+ _baris +' sudah ada pada baris sebelumnya, kombinasi farm, kandang dan siklus tidak boleh kembar');
				}
				else{
					_farmKandangSiklus.push(_tmpfks);
				}
				if(_up.err){
					_error++;
					_message.push('Data pada baris '+ _baris +' '+_up.msg);
				}
				_tmpTahun = this.getTahunDocIn(_x[_d]['Tanggal_Docin']);
				/* cari awal docin */
				if(empty(_awalDocin)){
					_awalDocin = _x[_d]['Tanggal_Docin'];
				}
				else{
					_awalDocin = _x[_d]['Tanggal_Docin'] < _awalDocin ? _x[_d]['Tanggal_Docin'] : _awalDocin;
				}

				if(_thnDocIn === undefined){
					_thnDocIn = _tmpTahun;
				}
				if(_tmpTahun != _thnDocIn){
					_error++;
					_message.push('Tahun pada baris '+ _baris +' tidak sama dengan tahun sebelumnya');
				}
				_dataDOCin[_d] = _x[_d];

				/* kumpulkan data untuk periode siklus */
				_tmpSiklus = _thnDocIn+'-'+_x[_d]['Siklus'];
				_tmpFarm = _x[_d]['Farm'];
				_tmpStrain = _x[_d]['Strain'];
				if(_periodeSiklus[_tmpFarm] == undefined){
					_periodeSiklus[_tmpFarm] = {};
				}
				if(_periodeSiklus[_tmpFarm][_tmpSiklus] == undefined){
					_periodeSiklus[_tmpFarm][_tmpSiklus] = {strain : _tmpStrain, siklus : _x[_d]['Siklus'], 'kandang' : []};
				}
				/* pastikan strain dalam 1 siklus per farm harus sama */
				if(_periodeSiklus[_tmpFarm][_tmpSiklus]['strain'] != _x[_d]['Strain']){
					_error++;
					var _tmp_pesan = [];
					for(var _tp in _periodeSiklus[_tmpFarm][_tmpSiklus]['kandang']){
						_tmp_pesan.push('kandang '+_periodeSiklus[_tmpFarm][_tmpSiklus]['kandang'][_tp]['Kandang']);
					}
					_message.push('Kode strain pada baris '+ _baris +' tidak sama dengan '+_tmp_pesan.join(' , '));
				}

				_periodeSiklus[_tmpFarm][_tmpSiklus]['kandang'].push(_x[_d]);
			}
		}
		/* pastikan tahun yang diupload adalah >= tahun saat ini */
		var t = new Date();
		var thnSekarang = t.getFullYear();
		if(_thnDocIn < thnSekarang){
			_error++;
			_message.push('Tahun DOC In lebih kecil dari tahun sekarang ('+thnSekarang+')');
		}

		if(!_error){
			this.setPeriodeSiklus(_periodeSiklus);
			this.setDataDocIn(_dataDOCin);
			this.setThnDOCIn(_thnDocIn);
			this.setAwalDOCIn(_awalDocin);
		}

		return {err : _error , msg : _message};
	},
	validNamaKolom : ['Farm','Kandang','Tanggal_Docin','Siklus','Populasi','Tanggal_Panen','Strain'],
	validFormatKolom : {
		'Farm' : /^\w+$/,'Kandang' : /^\d{2}$/,'Tanggal_Docin' : 'validDate','Siklus' : /^[1-7]{1}$/,'Populasi': /^\d+$/,'Tanggal_Panen' : 'validDate','Strain' : /^\w+$/
	},
	periksaNamaKolom : function (dataJson){
		var _result = {err : 1, msg:[]};
		var _i = 0;
		var _tmp,_error = 0;
		for(var _sn in dataJson){
			var _x = dataJson[_sn];
			for(var i in _x){
				_tmp = _x[i];
				if(_i > 0){
					_result.err = _error ;
					return _result;
				}
				for(var z in _tmp){
					if(!in_array(z,this.validNamaKolom)){
						_error++;
						_result.msg.push('Nama kolom <strong>'+z+'</strong> tidak sesuai template');
					}
				}

				_i++;
			}
		}

	},
	periksaIsiBaris : function(baris){
		var _error = 0;
		var _msg = [];
		var _jmlElm = 0;
		for(var i in baris){
			_jmlElm++;
			var t = this.validFormatKolom[i];
			if(t == 'validDate'){
				if(!this.isTanggalValid(baris[i])){
					_error++;
					_msg.push('Kolom '+i+' tidak valid');
				}
			}
			else{
				if(!t.test(baris[i])){
					_error++;
					_msg.push('Kolom '+i+' tidak valid');
				}
			}
		}
		if(_jmlElm < this.validNamaKolom.length){
			_error++;
			_msg.push('ada kolom yang kosong');
		}
		return {err : _error , msg : _msg};

	},
	periksaUmurPanen : function(baris){
		var _error = 0;
		var _msg,  _umurPanen;
		_umurPanen = selisihHari(new Date(this.getValidDate(baris['Tanggal_Docin'],'/','-')), new Date(this.getValidDate(baris['Tanggal_Panen'],'/','-')));

		if(_umurPanen > this.umurPanen.max){
			_error++;
			_msg = ' umur panen lebih besar dari '+ this.umurPanen.max;
		}
		if(_umurPanen < this.umurPanen.min){
			_error++;
			_msg = ' umur panen lebih kecil dari '+ this.umurPanen.min;
		}
		return {err : _error , msg : _msg};

	},
	buatIsiTabel : function(obj){
		var _tr = [];
		for(var _i in obj){
			var _td = [];
			var _tmp_tr = obj[_i];
			for(var _a in _tmp_tr){
				_td.push(_tmp_tr[_a]);
			}
			_tr.push('<tr><td>'+_td.join('</td><td>')+'</td></tr>');
		}
		return _tr.join('');
	},

	getTahunDocIn : function(str){
		if(!empty(str)){
			var _y = str.split('/');
			return _y[2];
		}
		else{
			return null;
		}
	},
	isTanggalValid : function(str){
		var polaTanggal = /\d{2}\/\d{2}\/\d{4}/;
		var _error = 0;
		if(!polaTanggal.test(str)){
			_error++;
		}
		if(!_error){
			var t = this.getValidDate(str,'/','-');

			if(!empty(t)){
				if(!this.isValidDate(t)){
					_error++ ;
				}

			}
		}
		return !_error;
	},
	/* format yang diberikan adalah format indonesia DD/MM/YYY*/
	getValidDate : function(str,separatorAsal,separatorTujuan){
		if(!empty(str)){
			var _y = str.split('/');
			return _y.reverse().join('-');
		}
		else{
			return null;
		}
	},
	isValidDate : function(str){
		if(!empty(str)){
			var t = new Date(str);
			return t == 'Invalid Date' ? 0 : 1;
		}
		else{
			return 0;
		}
	},
	to_json : function(workbook) {
	    var result = {};
	    workbook.SheetNames.forEach(function(sheetName) {
	        var roa = XLSX.utils.sheet_to_json(workbook.Sheets[sheetName]);
	        if(roa.length > 0){
	            result[sheetName] = roa;
	        }
	    });
	    return result;
	},
	/* simpan rencana doc in */
	simpanDocIn : function(){
		var _error = 0;
		if(empty(Import.getPeriodeSiklus())){
			_error++;
			toastr.error('File excel belum diimport');
		}
		var _awal_docin = new Date(this.getAwalDOCIn());
		var _error = 0;
		var _max_simpan = 10;
		var _tgl_server = new Date($('#tanggal_server').data('tanggal_server'));
		var _jarak_hari = selisihHari(_tgl_server,_awal_docin);

		if(_jarak_hari < _max_simpan){
			_error++;
			toastr.error('Maximal kabag admin melakukan simpan adalah '+_max_simpan+' hari dari awal docin');
		}

		if(!_error){
			$.ajax({
				url : 'forecast/forecast/simpan_rencana_docin',
				data : {periodeSiklus : Import.getPeriodeSiklus(), thnSiklus : Import.getThnDataDocIn()},
				type : 'post',
				dataType : 'json',
				success : function(data){
					if(data.status){
						toastr.success(data.message);
						/* periksa apakah datanya sudah ada ditabel atau belum*/
						var _ada = $('#tabelSiklusTahunan tbody').find('td.tahun:contains('+data.content.tahun+')').length;
						/* tambahkan siklus tahunan ke dalam tabel */
						if(!_ada){
							$('<tr><td class="tahun">'+data.content.tahun+'</td><td class="status">'+data.content.status+'</td></tr>').appendTo($('#tabelSiklusTahunan tbody'));
						}

					}
					else{
						for(var i in data.message){
							toastr.error(data.message[i]);
						}
					}

				},
			});
		}

	},
	rilis_approve : function(event,elm){
		 var _tr = $('#tabelSiklusTahunan tbody>tr.terpilih');
		 var _status = $.trim(_tr.find('td.status').text());
		 var _tahun = $.trim(_tr.find('td.tahun').text());
		 var _error = 0;
		 var _awal_docin = new Date($.trim(_tr.data('awal_docin')));
		 var _error = 0;
		 var _max_approve = {'N' : 10,'RV' : 10, 'A' : 7};
		 var _tgl_server = new Date($('#tanggal_server').data('tanggal_server'));
		 var _jarak_hari = selisihHari(_tgl_server,_awal_docin);

		 var statusApprove = $(elm).data('status');
		 if(!_tr.length){
			 _error++;
			 toastr.error('Belum ada tahun siklus yang dipilih');
		 }
		 if(!_error){
			 switch(statusApprove){
			 case 'N':
				 if(_status != 'DRAFT'){
					 _error++;
					 toastr.error('Hanya yang berstatus draft saja yang bisa dirilis');
				 }
				 else if(_jarak_hari < _max_approve[statusApprove]){
					 _error++;
					 toastr.error('Maximal kabag admin melakukan rilis adalah '+_max_approve[statusApprove]+' hari dari awal docin');
				 }
				 break;
			 case 'RV':
				 if(_status != 'PENGAJUAN'){
					 _error++;
					 toastr.error('Hanya yang berstatus pengajuan saja yang bisa direview');
				 }
				 else if(_jarak_hari < _max_approve[statusApprove]){
					 _error++;
					 toastr.error('Maximal kadept melakukan approval adalah '+_max_approve[statusApprove]+' hari dari awal docin');
				 }
				 break;
			 case 'A':
				 if(_status != 'REVIEW'){
					 _error++;
					 toastr.error('Hanya yang berstatus review saja yang bisa diapprove');
				 }
				 else if(_jarak_hari < _max_approve[statusApprove]){
					 _error++;
					 toastr.error('Maximal kadiv melakukan approval adalah '+_max_approve[statusApprove]+' hari dari awal docin');
				 }
				 break;
			}
		 }

		 if(!_error){
			 bootbox.confirm({
				    title: 'Konfirmasi',
				    message: 'Apakah anda yakin merilis Perencanaan DOC In tahun '+_tahun,
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
								url : 'forecast/forecast/approve_rilis_rencanadocin',
								data : {status : statusApprove, tahun : _tahun},
								type : 'post',
								dataType : 'json',
								success : function(data){
									if(data.status){
										toastr.success(data.message);
										/* periksa apakah datanya sudah ada ditabel atau belum*/
										var _statusTd = $('#tabelSiklusTahunan tbody').find('td.tahun:contains('+_tahun+')');
										/* update status tahunan di dalam tabel */
										_statusTd.next().text(data.content);
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
		 event.preventDefault();
	},
	reject : function(event,elm){
		 var _tr = $('#tabelSiklusTahunan tbody>tr.terpilih');
		 var _status = $.trim(_tr.find('td.status').text());
		 var _tahun = $.trim(_tr.find('td.tahun').text());
		 var _error = 0;

		 var statusApprove = $(elm).data('status');
		 if(!_tr.length){
			 _error++;
			 toastr.error('Belum ada tahun siklus yang dipilih');
		 }
		 switch(statusApprove){
		 	case 'RV':
		 		if(_status != 'PENGAJUAN'){
		 			_error++;
		 			toastr.error('Hanya yang berstatus pengajuan saja yang bisa direject oleh kadept');
		 		}
		 		break;
		 	case 'A':
		 		if(_status != 'REVIEW'){
		 			_error++;
		 			toastr.error('Hanya yang berstatus review saja yang bisa direject oleh kadiv');
		 		}
		 		break;
		 }
		 if(!_error){
			 var _m = '<div>\
			 					<h5>Keterangan Reject Perencanaan DOC In</h5>\
			 					<fieldset>\
									<textarea name="keterangan_reject" class="col-md-12"></textarea>\
								<fieldset>\
							</div>';
		var _options = {
				title : '<div class="text-center"><div>Perencanaan DOC In </div><div style="font-size:80%">Farm Budidaya -'+_tahun+'</div></div>',
				message : _m,
				className : '',
				buttons : {
					'ok':{
						label: 'Simpan',
						className: 'btn-default',
						callback : function(e){
								var tmb = e.target;
								var _minimum_char = 10;
								var _bb = $(tmb).closest('.modal-content');

								var _ket = $.trim(_bb.find('textarea[name=keterangan_reject]').val());
								if(_ket.length < _minimum_char){
									_error++;
									toastr.error('Keterangan harus diisi, minimal '+_minimum_char+' huruf');
									return false;
								}
								if(!_error){
									$.ajax({
									url : 'forecast/forecast/reject_rencanadocin',
									data : {status : statusApprove, tahun : _tahun, keterangan : _ket},
									type : 'post',
									dataType : 'json',
									success : function(data){
										if(data.status){
											toastr.success(data.message);
											/* periksa apakah datanya sudah ada ditabel atau belum*/
											var _statusTd = $('#tabelSiklusTahunan tbody').find('td.tahun:contains('+_tahun+')');
											/* update status tahunan di dalam tabel */
											_statusTd.next().text(data.content);
										}
										else{
											toastr.error(data.message);
										}

									},
								});
							}
						},
					}
				},

		};
		bootbox.dialog(_options);
	}
	event.preventDefault();
	},
	preview : function(target){
		 var _tr = $('#tabelSiklusTahunan tbody>tr.terpilih');
		 var _status = $.trim(_tr.find('td.status').text());
		 var _tahun = $.trim(_tr.find('td.tahun').text());
		 var _error = 0;

		 if(!_tr.length){
			 _error++;
			 toastr.error('Belum ada tahun siklus yang dipilih');
		 }
		 if(!_error){
			/* dapatkan semua farm pada tahun tersebut */
			$.ajax({
				url : 'forecast/forecast/list_farm_preview',
				data : {tahun : _tahun},
				type : 'post',
				dataType : 'json',
				success : function(data){
					if(data.status){
						/* update status tahunan di dalam tabel */
						var _t = $(target);
						var _sel = _t.find('select[name=list_farm]');
						var _tmp = [];
						_sel.children(':gt(0)').remove();
						for(var i in data.content){
							var opt = data.content[i];
							_tmp.push('<option data-tahun="'+_tahun+'" value="'+opt.kode_farm+'">'+opt.nama_farm+'</option>');
						}
						$(_tmp.join('')).appendTo(_sel);

						if(_t.is(':hidden')){
							_t.show();
						}
					}
					else{
						toastr.error(data.message);
					}
				},
			});

		 }

	},
	tampilkanDocIn : function(){
		var ini = $('#preview_div').find('select[name=list_farm]');
		var _tahun = ini.find('option:selected').data('tahun');
		var _farm = ini.val();
		var _error = 0;
		if(empty(_farm)){
			_error++;
			toastr.error('Pilih farm terlebih dahulu');
		}
		if(!_error){
			$.ajax({
				url : 'forecast/forecast/detail_docin_bdy',
				data : {tahun : _tahun, farm : _farm},
				type : 'post',
				dataType : 'json',
				success : function(data){
					if(data.status){
						var _isiTabel = Import.buatIsiTabel(data.content.tabel);
						$('#preview_tabel_docin').find('tbody').html(_isiTabel);
						var _header = data.content.header;
						$('#preview_div').find('input[name=dayahidup]').val(number_format(_header['TARGET_DH_PRC'] * 100,2,','));
						$('#preview_div').find('input[name=beratbadan]').val(number_format(_header['TARGET_BB_PRC'],2,','));
						$('#preview_div').find('input[name=fcr]').val(number_format(_header['TARGET_FCR_PRC'],3,','));
						$('#preview_div').find('input[name=ip]').val(number_format(_header['TARGET_IP'],0,','));
						$('#preview_div').find('input[name=kum]').val(number_format(_header['TARGET_KUM'],0,',','.'));
						$('#preview_div').find('input[name=umurpanen]').val(_header['TARGET_UMUR_PANEN']);
						/* ubah format populasi menjadi format angka ribuan*/
						var _t ;
						$('#preview_tabel_docin').find('tbody').find('tr').each(function(){
								_t = $(this).find('td:eq(4)');
								_t.text(number_format(_t.text(),0,',','.')).addClass('number');
						});
					}
					else{
						toastr.error(data.message);
					}

				},
			});

		}
	},
	cetakPerencanaanDocIn : function(elm){
		/* periksa apakah data yang akan digenerate sudah ada atau belum */
		if($('#preview_tabel_docin tbody tr').length){
			var data = [], fontSize = 10, height = 0, doc;
			doc = new jsPDF('p', 'pt', 'a4', true);
			doc.setFont("times", "normal");
			doc.setFontSize(fontSize);
		//	doc.text(20, 20, "hi table");
			doc.margins = 1;
			data = [];
			var columns = [], _clm, _clm_id = [];
			var rows = [], _tmp_r = {};
			var _y = 30;
			var _baris = 12;
			var _rows_header = [];
			var _f = $(elm).closest('.form-horizontal');
	//		_rows_header.push([: ,' FCR : '+_f.find('input[name=fcr]').val(),' IP : '+_f.find('input[name=ip]').val()]);
	//		_rows_header.push(['Daya Hidup : ' +_f.find('input[name=dayahidup]').val(),' FCR : '+_f.find('input[name=fcr]').val(),' IP : '+_f.find('input[name=ip]').val()]);
	//		_rows_header.push(['Daya Hidup : ' +_f.find('input[name=dayahidup]').val(),' FCR : '+_f.find('input[name=fcr]').val(),' IP : '+_f.find('input[name=ip]').val()]);
			/* buat informasi header */
			var _f_terpilih = _f.find('select[name=list_farm]').find('option:selected');
			doc.myText('PERENCANAAN DOC IN FARM '+_f_terpilih.text(),{align: "center"},20,_y);
			_y += _baris;

			doc.myText('TAHUN '+_f_terpilih.data('tahun'),{align: "center"},20,_y);

			_y += _baris + 10;
			doc.text(75,_y,'Daya Hidup');
			doc.text(130,_y,' : '+_f.find('input[name=dayahidup]').val());
			doc.text(210,_y,'FCR');
			doc.text(270,_y,' : '+_f.find('input[name=fcr]').val());
			doc.text(350,_y,'IP');
			doc.text(430,_y,' : '+_f.find('input[name=ip]').val());

			_y += _baris;
			doc.text(75,_y,'Berat Badan');
			doc.text(130,_y,' : '+_f.find('input[name=beratbadan]').val());
			doc.text(210,_y,'Umur Panen');
			doc.text(270,_y,' : '+_f.find('input[name=umurpanen]').val());
			doc.text(350,_y,'Kum');
			doc.text(430,_y,' : '+_f.find('input[name=kum]').val());
			/*
			doc.autoTable(['','',''], _rows_header,{
				 theme: 'plain',
				 tableWidth : 700

			 });
	*/
			$('#preview_tabel_docin thead tr th').each(function(){
				_clm = {title : $(this).text(), dataKey : $(this).data('id')};
				_clm_id.push($(this).data('id'));
				columns.push(_clm);
			});
			$('#preview_tabel_docin tbody tr').each(function(){
				_tmp_r = {};
				$(this).find('td').each(function(i){
						_tmp_r[_clm_id[i]] = $(this).text();
				});
				rows.push(_tmp_r);
			});

			_y += _baris;
			doc.autoTable(columns, rows,{
				 theme: 'grid',
				 startY : _y ,
				 /*
				 headerStyles: {
					 textColor: [0,0,0],
					 fillColor: [255,255,255],
					 lineColor: [0,0,0],
					 lineWidth : 1

			 },*/
			 /*
			 columnStyles: {
						 tgl_docin: {
								 fontStyle: 'bold',
								 halign : 'center'

						 }
				 },
				 */
			});

		//	doc.text('text', 40, doc.autoTableEndPosY() + 30);
			doc.output('dataurlnewwindow');
		}
		else{
			toastr.error('Data pada tabel masih kosong');
		}

	}

};
(function(){
	'use strict';
	$('#preview_div,#import_div').hide();
	 $(document).on('change', '.btn-file :file', function(e) {
	        var input = $(this),
	            numFiles = input.get(0).files ? input.get(0).files.length : 1,
	            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	            $('#docinfile').val(label);

	  });
	 $('#tabelSiklusTahunan').on('click','tr',function(){
		 $('#tabelSiklusTahunan tr').not($(this)).removeClass('terpilih');
		 $(this).addClass('terpilih');
	 })
	 $('#preview_div select[name=list_farm]').change(function(){
		  $(this).closest('form').find('input').val('');
		 	$('#preview_tabel_docin tbody').empty();
	 });
}());
