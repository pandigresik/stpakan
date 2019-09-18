var BAPD = {
	listFarm : '',
    list_bapd : function(periode, kodefarm){
		var _data = {};
		var _error = 0;
		_data['periode'] = periode;
		_data['kodefarm'] = kodefarm;
		/* lakukan pencarian */
		if(!_error){
			var _url = 'report/laporan_bapd/list_bapd';
			$.get(_url,_data,function(data){
				$('#list_bapdocin').html(data);
			},'html');
		}
    },
	
	getListFarm : function(){
		var tmp;
		if(empty(BAPD.listFarm)){
			$.ajax({
				type : 'get',
				url : 'report/report/userFarm',
				data : {},
				dataType : 'json',
					async:false,
					cache : true,
				}).done(function(data){
					if(data.status){
						BAPD.listFarm = data.content;
						tmp = BAPD.listFarm;
					}
				});
			}else{
				tmp = BAPD.listFarm;
		}
		return tmp;
	},
	
	export_pdf : function(elm){
		var tipe_info 		= $('#select_tipe_informasi').val();
		var select_farm		= $('#select_farm').val();
		var periode 		= $(elm).data('periode');
		var kodefarm		= $(elm).data('kodefarm');
		var _data			= {};
		if(periode != '' && kodefarm != ''){
			_data['periode']		= periode;
			_data['kodefarm']		= kodefarm;
		}
		if(select_farm != ''){
			_data['kodefarm']		= select_farm;
		}
		switch(tipe_info){
			case 'bap_doc':
				var _url = 'report/Laporan_bapd/bapd_pdf';
				$.redirect(_url, _data, 'GET','_blank');
			break;
			case 'kodebox':
				var _url = 'report/Laporan_bapd/kodebox_pdf';
				$.redirect(_url, _data, 'GET','_blank');
			break;
		}
	},
	
	detailInformasi : function(elm,tipe){
			var _form = $(elm).closest('form');
			var _farm = _form.find('select[name=farm]').val();
			var _tahun = _form.find('select[name=tahun]').val();
			var _bd = _form.closest('.bootbox-body');
			$.get('report/Laporan_bapd/detailInformasi',{farm : _farm, tahun : _tahun, tipe : tipe},function(data){
				_bd.find('.div_detailInformasi').html(data).find('table').scrollabletable();
			});
	},
	
	show_bap_doc : function(elm, tipe){
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
									'&nbsp;<span class="btn btn-primary" onclick="BAPD.detailInformasi(this,\''+tipe+'\')">Cari</span>',
								'</div>',
							'</form>',
							'</div>',
							'<br />',
							'<div class="div_detailInformasi"></div>'
					];
		var _options = {
			title : 'Informasi Farm',
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
	}
};

(function(){
	var default_kodefarm = $('#select_farm').find('option').first().val();
	BAPD.list_bapd('', default_kodefarm);
}());

function get_bapd(elm){
	var kodefarm = $(elm).val();
	BAPD.list_bapd('', kodefarm);
}