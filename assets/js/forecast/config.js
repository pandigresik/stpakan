
'use strict';
var Config = {
		/* configurasi global */
		/* configurasi untuk drag dan drop pada fitur forecast */
		_lockDrag : ['Acc1','Acc2','Draft','Baru'],
		_indexHeader : ['kode_farm','kandang','lantai','tipe','kapasitas','jantan','betina'],
		_regexTahun : /\d{4}/,
//		_regexBulan : /\D{3}/i,
		_regexBulan : /^[a-z]{3}$/i,
		_canDragTutupSiklus : ['KF'],
		_bentuk_pakan : {T : 'Tepung', C : 'Crumble', P : 'Pellet'},
		_tipe_kandang : {O : 'Open', C : 'Closed'},
		_jenis_kelamin : {'j' : 'jantan', 'b' : 'betina'},
		_bisaDisimpan : ['*'],
		_bisaDirilis : ['*','Draft'],
		_lockRubahPakan : ['Acc1','Acc2','Baru'],
		_bisaDiapprove1 : ['Baru'],
		_bisaDiapprove2 : ['Acc1'],
		_currentUser : null,
		_currentFarm : null,
		_setCurrentUser : function(user){
			this._currentUser = user;
		},
		_tglServer : null,
		_setTglServer : function(tgl){
			this._tglServer = new Date(tgl);
		},
		_regional : {
				monthNames: ['Januari','Februari','Maret','April','Mei','Juni',
				'Juli','Agustus','September','Oktober','November','Desember'],
				monthNamesShort: ['Jan','Feb','Mar','Apr','Mei','Jun',
				'Jul','Ags','Sep','Okt','Nov','Des'],
				dayNames: ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
				dayNamesShort: ['Min','Sen','Sel','Rab','kam','Jum','Sab'],
				dayNamesMin: ['Mg','Sn','Sl','Rb','Km','jm','Sb'],

		},
		_getMusim : function(tglDocIn,separator){
			/* Sep sd Feb => O, Maret sd Agst => I */
			var insesion = [3,4,5,6,7,8];
			if(separator == undefined){
				separator = '-';
			}
			var _t = tglDocIn.split(separator);
			_t[1] = +_t[1];
			if(in_array(_t[1],insesion)){
				return 'I';
			}
			else return 'O';
		},
		_setCurrentFarm : function(idFarm){
			this._currentFarm = idFarm;
		},
		/* dt adalah object date */
		_getDateStr : function(dt,separator){
			if (separator == undefined){
				separator = '-';
			}
			var _arr = [dt.getFullYear(),dt.getMonth() + 1,dt.getDate()];
			return _arr.join(separator);
		},
		/* convert 2015-5-1 menjadi 2015-05-01 supaya valid tanggalnya di javascript */
		_convertTgl : function(tgl){
			var _t = tgl.split('-');
			var _new = [];
			for(var x in _t){
				if(_t[x].length < 2){
					_new.push('0'+_t[x]);
				}
				else{
					_new.push(_t[x]);
				}
			}
			return _new.join('-');
		},
		/* convert dari Mei menjadi 05 */
		_indexBulan : function(bulan){
			var _reg =  $.datepicker.regional['id'] || this._regional;
			var _bulan =_reg.monthNamesShort;
			return _bulan.indexOf(bulan) + 1;
		},
		_namaBulan : function(indexBulan){
			var _reg =  $.datepicker.regional['id'] || this._regional;
			return _reg.monthNamesShort[indexBulan - 1];
		},
		/* tgldb = 2015-05-26 dirubah jadi 26-Mei-2015 */
		_tanggalLocal : function(tgldb,separator_asal,separator_tujuan){
			if (separator_asal == undefined){
				separator_asal = '-';
			}
			if (separator_tujuan == undefined){
				separator_tujuan = '-';
			}
			var _t = tgldb.split(separator_asal);
			if(_t[2].length  < 2 ) {
				_t[2] = '0'+_t[2];
			}
			var _new = [_t[2],this._namaBulan(_t[1]),_t[0]];
			return _new.join(separator_tujuan);
		},
		/* 26-Mei-2015 dirubah menjadi tgldb 2015-05-26 */
		_tanggalDb : function(tgllocal,separator_asal,separator_tujuan){
			if (separator_asal == undefined){
				separator_asal = '-';
			}
			if (separator_tujuan == undefined){
				separator_tujuan = '-';
			}
			var _t = tgllocal.split(separator_asal);
			var _indexBulan = this._indexBulan(_t[1]);
			if(_indexBulan < 10){
				_indexBulan = '0'+_indexBulan.toString();
			}

			var _new = [_t[2],_indexBulan,_t[0]];
			return _new.join(separator_tujuan);
		},
		_getCurrentUser : function(){
			return this._currentUser;
		},
		/* mapping data kandang dari array menjadi json */
		mappingHeader : function(arr){
			var _tmp = {};
			for(var x in arr){
				_tmp[this._indexHeader[x]] = arr[x];
			}
			return _tmp;
		},
		/* hariH adalah dalam bentuk string '2015-06-05'
		 * hariLibur dalam bentuk array ==> ['2015-05-23','2015-06-25']
		 */
		cari_hari_kerja_terdekat : function(hariH,hariLibur){
			/* hariLibur dalam bentuk array */

			var _y;
			var _h = this._convertTgl(hariH);
			while(this.is_hari_libur(_h,hariLibur)){
				_y = new Date(_h);
				_y.setDate(_y.getDate() - 1);
				_h = this._convertTgl(this._getDateStr(_y,'-'));
			}
			return _h;
		},
		is_hari_libur : function(hariH,hariLibur){
			/* jika hari minggu atau ada dalam hari libur return true*/
			var _t = this._convertTgl(hariH);
			var _h = new Date(_t);
			return (_h.getDay() == 0) || in_array(_t,hariLibur);
		},
		is_hari_aktif : function(hariH,hariAktif){
			/* jika hari minggu atau ada dalam hari libur return true*/
			var _t = this._convertTgl(hariH);
			var _h = new Date(_t);
			return in_array(_t,hariAktif);
		},
		/* d1 dan d2 adalah object date*/
		get_selisih : function(d1,d2){
			var _t1 = d1.getTime();
			var _t2 = d2.getTime();
			return parseInt((_t2-_t1)/(24*3600*1000));
		} ,
		ceil2 : function(num){
			return number_format(num,2,',','.');
		},
	};
