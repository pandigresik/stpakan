$(window).load(function(){
	'use strict';
	$('span.gantt-block').dblclick(function(){
		var _class = $(this).attr('class').split(' ');
		var _tglkirim, _tmp;
		for(var i in _class){
			if(_class[i].substr(0,6) == 'kirim_'){
				_tmp = _class[i].split('_');
				_tglkirim = Config._tanggalLocal(_tmp[1], '-', ' ');
			}
		}
		$.ajax({
			type : 'post',
			data : { },
			url : 'permintaan_pakan/permintaan_pakan/main',
			success : function(data){
				$('#main_content').html(data);
			},
			async : false,
		}).done(function(){
			setTimeout(function(){
				var _td = $('table tbody tr:contains(\''+_tglkirim+'\')').find('td:eq(4):contains(\''+_tglkirim+'\')');
				_td.closest('tr').find('td:first span.link_span').click();
			},1000);


		});
	});
	if($('#main_content div.analisa_pp figure.gantt').length){
		/* jadikan bisa discroll */
		$('#main_content div.analisa_pp figure.gantt').wrap('<div id="wrap_analisa_pp"></div>').css({
			'max-width' : $('#main_content .panel-body').innerWidth(),
			'overflow'  : 'auto',
			'max-height' : .6 * $(window).height(),
		//	'position' : 'relative',
		});

		var _pos = $('#main_content div.analisa_pp figure.gantt').offset();
		var _h = $('#main_content div.analisa_pp figure.gantt header').clone();
		_h.appendTo('#wrap_analisa_pp');
		_h.wrap('<div id="header_analisa"><figure class="gantt gantt_t"></figure></div>');
		$('#header_analisa figure.gantt_t').css({
			'position' : 'absolute',
			'left' : _pos.left,
			'top' : _pos.top,
			'max-width':$('#wrap_analisa_pp').innerWidth() - 15,
			'overflow-y' : 'hidden',
			'z-index':100
		});
		$('#wrap_analisa_pp figure').scroll(function(){
		/*      var maxScrollLeft = document.getElementById('header_analisa') != undefined ? document.getElementById('header_analisa').scrollWidth - $('#header_analisa').width() : 10;

		      if($(this).scrollLeft() > maxScrollLeft){
		        $(this).scrollLeft(maxScrollLeft);
		      }
		      $('#header_analisa').scrollLeft($(this).scrollLeft());
		  */
			$('#header_analisa figure.gantt_t').scrollLeft($(this).scrollLeft());

		    });
		$('#wrap_analisa_pp>figure').scrollLeft(
			$('li.akhir_permintaan').position().left
		);

	}

	/* cek apakah ada notif atau tidak */
	var _notif_json = $('#data-notif').text();
	var _notif;
	var _msg = '';
//	var _title = {'belum_ada_rp' : 'Pakan Belum Memiliki Rencana Produksi', 'belum_memenuhi_pp' : 'Jumlah Permintaan Belum Terpenuhi'};
	if(_notif_json.length){
		_notif = $.parseJSON(_notif_json);
		toastr.options = {
				  "closeButton": true,
				  "timeOut": "0",
				  "extendedTimeOut": "0",
				  "tapToDismiss" : false
				};

		for(var i in _notif){
			var _title = _notif[i]['title'];
			//console.log(_title);
			var _content = _notif[i]['message'];
			for(var y in _content){
				//console.log(_content[y]);
				_msg = _content[y];
				toastr.warning(_msg,_title);
			}
		}
		/*
		for(var i in _notif){

			var _content = _notif[i];
			for(var y in _content){
				_msg = 'Permintaan No. '+_content[y]['no_pp']+' dengan jenis pakan '+_content[y]['nama_barang']+' nama farm '+_content[y]['nama_farm'];
				toastr.warning(_msg,_title[i]);
			}
		}
		*/
		//toastr.clear();
		toastr.options = {
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				};
	}
});
