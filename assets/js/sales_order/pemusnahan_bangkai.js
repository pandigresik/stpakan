'use strict';

var pemusnahanBangkai = {
	loadAwal : 1,
	listBA : function(){
		var _url = 'sales_order/pemusnahan_bangkai/listBA';
		var _startDate = empty($('input[name=startDateBangkai]').val()) ? '' : Config._tanggalDb($('input[name=startDateBangkai]').val(),' ','-');
		var _endDate = empty($('input[name=endDateBangkai]').val()) ? '' : Config._tanggalDb($('input[name=endDateBangkai]').val(),' ','-');
		var _error = 0, _message = 'Mohon mengisi tombol awal dan tombol akhir untuk melakukan filtering';
		if(!this.loadAwal){
			if(empty(_startDate)){
				_error++;
			}
			if(empty(_endDate)){
				_error++;
			}
		}
		this.loadAwal  = 0;
		var _data = {
			startDate : _startDate,
			endDate : _endDate
		};
		if(!_error){
			$.get(_url,_data,function(html){
				$('#div_list_laporan').html(html);
			},'html').done(function(){
				$('#div_list_laporan table').fixedHeaderTable({ height: '400', width: '95%'});
			});
		}else{
			bootbox.alert(_message);
		}

	},
	checkInput: function(elm){
		var _min = 10;
		var _isi = $.trim($(elm).val()).length;
		var _confirm = $(elm).closest('.bootbox').find('.modal-footer').find('button[data-bb-handler=confirm]');
		if(_isi > _min){
			_confirm.removeClass('disabled');
		}else{
			_confirm.addClass('disabled');
		}
	},
	generateBA : function(elm){
		var _no_ppsk = $(elm).data('ppsk');
		var _jml = $(elm).data('jml');
		var _tglkebutuhan = $(elm).data('tglkebutuhan');
		var _tr = $(elm).closest('tr');
		var _keterangan;
		var bootbox_content = [
			'<div>Mohon mengentri keterangan pemusnahan (min. terdiri dari 10 karakter). ?</div>',
			'<textarea rows="5" cols="70" maxlength="255" name="alasan" onkeyup="pemusnahanBangkai.checkInput(this)">',
		];
		var _options = {
			title : 'Keterangan',
			message : bootbox_content.join(''),
			buttons : {
				'cancel': {
					label: 'Batal',
					className: 'btn-default'
				},
				'confirm': {
					label: 'Lanjutkan',
					className: 'btn-success disabled',
					callback : function(e){
						_keterangan = $(e.target).closest('.bootbox').find('textarea').val();
							bootbox.confirm({
								title: 'Konfirmasi Penyimpanan',
								message: '<span class="text-center">Apakah anda yakin melanjutkan proses generate berita acara ?</span>',
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
											type : 'post',
											dataType : 'json',
											data : {no_ppsk : _no_ppsk, jml : _jml, tgl_kebutuhan : _tglkebutuhan, keterangan : _keterangan },
											url : 'sales_order/pemusnahan_bangkai/simpan',
											beforeSend : function(){

											},
											success : function(data){
												if(data.status){
													bootbox.alert(data.message);
													$(elm).replaceWith('<span data-ba="'+data.content+'" onclick="pemusnahanBangkai.cetakBA(this)" class="btn btn-default"><i class="glyphicon glyphicon-paperclip"></i> '+data.content+'</span>');
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
				}
			},
		};
		bootbox.dialog(_options);
		/*
		bootbox.dialog(_options).bind('shown.bs.modal',function(){
			$(this).find('.bootbox-body').css({
				'min-height' : '110px'
			});
		});
		*/
	},
	cetakBA: function(elm){
		var _ba = $(elm).data('ba');
		var _url = 'sales_order/pemusnahan_bangkai/cetakBA';
		$.redirect(_url,{ba : _ba},'POST','_blank');

	},

	goto: function(elm){
		var _url = $(elm).data('url');
		$('#main_content').load(_url);
	},

	detailKandang: function(elm){
		var url 	 = 'sales_order/pemusnahan_bangkai/detailKandang';
		var _no_ppsk = $(elm).attr('data-ppsk');
		$.post(url,{ no_ppsk : _no_ppsk},function(html){
		  	$('#div_detail_kandang').html(html);
		},'html');
	},

};

$(function(){
	'use strict';
	$('input[name=startDateBangkai]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDateBangkai]').datepicker('option','minDate',date);
				pemusnahanBangkai.listBA();
			}
		},
		minDate : Config._tglServer,
		defaultDate: Config._tglServer
	});
	$('input[name=endDateBangkai]').datepicker({
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDateBangkai]').datepicker('option','maxDate',date);
				pemusnahanBangkai.listBA();
			}
		},
		maxDate : Config._tglServer,
		defaultDate: Config._tglServer
	});

	pemusnahanBangkai.listBA();
}());
