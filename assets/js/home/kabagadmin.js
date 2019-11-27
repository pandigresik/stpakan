var DocIn = {
	_toastrObj : [],
	hideToastr : function(index){
		$toast = DocIn._toastrObj[index];
		toastr.clear($toast, { force: true });

	},
	lihatDataStandart : function(elm){
		var std = $(elm).data('std');
		toastr.info(std);
	},
	stdBaru : null,
	getStdBaru : function(std){

	},
	showStandartBaru : function(elm,indexToastr){
		var ini = $(elm);
		var farm = ini.data('farm');
		var kode_farm = ini.data('kode_farm');
		var std = ini.data('std');
		var tgl_efektif = ini.data('tgl_efektif');
		var populasi = ini.data('populasi');
		var bootbox_content ={
				input_str : [
					            '<form class="form-horizontal block_lokal">',
					            '<div class="form-group">',
					     			'<label class="col-md-4 control-label" for="farm">Farm</label> ',
					     			'<div class="col-md-4">',
						     			'<div class="input-group">' ,
						     			'<label name="farm" class="form-control">'+farm+'</label>',
						     			'</div>',
					     			'</div>',
					     		'</div>',

				     			'<div class="form-group">',
					     			'<label class="col-md-4 control-label" for="kapasitas">Jumlah Populasi</label> ',
					     			'<div class="col-md-4">',
						     			'<div class="input-group">' ,
						     			'<input name="kapasitas" type="text" class="form-control input-md numeric" value="'+populasi+'" readonly>',
						     			'<span class="input-group-addon">ekor</span>',
						     			'</div>',
					     			'</div>',
				     			'</div>',

				     			'<div class="form-group">',
					     			'<label class="col-md-4 control-label" for="std">Performance</label> ',
					     			'<div class="col-md-4">',
						     			'<input name="jantan" type="text" class="form-control input-md numeric" value="'+std+'" readonly>',
						     			'</div>',
					     			'</div>',
				     			'</div>',

					],
				content : function(){
					var _obj = $('<div/>').html(this.input_str.join(''));
					$.ajax({
						url : 'forecast/forecast/detail_std',
						data : {std : std},
						dataType : 'html',
						type : 'post',
						async : false,
						success : function(data){
							_obj.append(data);
						},
					});
					return _obj;
				}
			};
			var _options = {
				title : 'Perubahan Perencanaan DOC In',
				message : bootbox_content.content(),
				buttons : {
					set : {
						label : 'Set',
						className : '',
						callback : function(e){
							DocIn.confirmStandartBaru(kode_farm,tgl_efektif,std,indexToastr);

						}
					}
				},
			};

			bootbox.dialog(_options);
	},
	confirmStandartBaru : function(kodefarm,tgl_efektif,std,indexToastr){

		var msg = 'Tanggal DOC In setelah tanggal '+Config._tanggalLocal(tgl_efektif,'-',' ')+ ' akan mengikuti performance terbaru';
			msg += '\nApakah anda yakin melakukan penyimpanan ?';
		bootbox.confirm({
		    title: 'Konfirmasi',
		    message: msg,
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
		    			url : 'forecast/forecast/update_std_farm',
		    			data : {kodefarm : kodefarm, tgl_efktif : tgl_efektif, std : std},
		    			type : 'post',
		    			dataType : 'json',
		    			success : function(data){
		    				if(data.status){
		    					toastr.success(data.message);
		    					DocIn.hideToastr(indexToastr);
		    				}
		    				else{
		    					toastr.error(data.message);
		    				}
		    			},

		    		});
		    	}
		    }
		});

	},
};
$(window).load(function(){
	'use strict';

	/* cek apakah ada notif atau tidak */
	var _notif_json = $('#data-notif').text();
	var _notif;

	var _title = 'Informasi';
	if(_notif_json.length){
		var _indexToastr = 0;
		DocIn._toastrObj = [];
		_notif = $.parseJSON(_notif_json);

		toastr.options = {
				  "closeButton": true,
				  "timeOut": "0",
				  "extendedTimeOut": "0",
				  "tapToDismiss" : false
				};
		var _msg,_title;
		/*
		for(var i in _notif){
			_msg.push('Terdapat standart budidaya baru dengan effective date '+_notif[i].tgl_efektif);
			_msg.push('\n Apakah anda akan melakukan perubahan rencana DOC In ?');
			_msg.push('<div class="row"><button onclick="DocIn.hideToastr('+_indexToastr+')" class="btn clear col-md-4 btn-default">Tidak</button>&nbsp;<button class="col-md-4 col-md-offset-2 btn btn-default" data-tgl_efektif="'+_notif[i].tgl_efektif+'" data-farm="'+_notif[i].nama_farm+'" data-std="'+_notif[i].std_baru+'" data-kode_farm="'+_notif[i].kode_farm+'" data-populasi="'+_notif[i].jml_populasi+'" onclick="DocIn.showStandartBaru(this,'+_indexToastr+')">Ya</button></div>');
			_msg.push('<div class="row new-line"><div onclick="DocIn.lihatDataStandart(this)" data-std="'+_notif[i].std_baru+'" class="col-md-10 btn btn-default">Lihat Standart Budidaya</div></div>');
			DocIn._toastrObj[_indexToastr] = toastr.warning(_msg.join(''),_title+' Farm '+_notif[i].nama_farm);
			_indexToastr++;
		}
		*/
	//	toastr.clear();
		for(var i in _notif){
			_title = _notif[i]['title'];
			//console.log(_title);
			var _content = _notif[i]['message'];
			for(var y in _content){
				//console.log(_content[y]);
				_msg = _content[y];
				DocIn._toastrObj[_indexToastr] = toastr.warning(_msg,_title);
				_indexToastr++;
			}
		}
		toastr.options = {
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				};
	}
});
