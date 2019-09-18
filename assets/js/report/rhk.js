var Rhk = {
			add_datepicker : function(elm,options){
				elm.datepicker(options);
			},
			
			detail_kandang : function(kode_kandang,kode_farm){
				var _flock = $('select[name=flock]');
				_flock.find('option:not(:first)').remove();
				$('input[name$=Date]').datepicker('disable');
				$.ajax({
					type : 'post',
					data : {kode_kandang : kode_kandang, kode_farm : kode_farm},
					url : 'report/report/detail_kandang',
					dataType : 'json',
					async : false,
					success : function(data){
						if(data.status){
							var c = data.content;
					/*		$('input[name=flock]').val(c.nama_flok);
							$('input[name=kandang]').data('noreg',no_reg);
							$('input[name=j_jantan]').val(number_format(c.jml_jantan,0,',','.'));
							$('input[name=j_betina]').val(number_format(c.jml_betina,0,',','.'));
							$('input[name=tgldocin]').val(c.tgl_doc_in);
							$('input[name=j_jantan]').data('jumlah',c.jml_jantan);
							$('input[name=j_betina]').data('jumlah',c.jml_betina);
							// update datepicker 
							c.tgl_doc_in = '2015-08-01';
							$('input[name$=Date]').datepicker('option','minDate',new Date(c.tgl_doc_in));
							$('input[name$=Date]').datepicker('enable');
					*/		
							// tampilkan flock yang bisa dipilih
							var _opt = [], _c;
							for(var i in c){
								_c = c[i];
								_opt.push('<option data-jmljantan="'+_c.jml_jantan+'" data-jmlbetina="'+_c.jml_betina+'" data-docin="'+_c.tgl_doc_in+'" data-rhkterakhir="'+_c.rhk_terakhir+'" value="'+_c.no_reg+'">'+_c.nama_flok+'&nbsp;&nbsp;&nbsp;'+_c.tgl_tetas+'</option>');
							}
							
							_flock.append(_opt.join(''));
							_flock.find('option:last').prop('selected',1);
							Rhk.setDatepicker(_flock);
						}
						else{
							toastr.error('Data tidak ditemukan flock belum diset');
						}
						
					},
				});
				
			
			},
			setDatepicker : function(elm){
				var _f = $(elm);
				if(!empty(_f.val())){
					var _opt = _f.find('option:selected');
					if(!empty(_opt.data('rhkterakhir'))){
						$('input[name$=Date]').datepicker('option','minDate',new Date(_opt.data('docin')));
						$('input[name$=Date]').datepicker('option','maxDate',new Date(_opt.data('rhkterakhir')));
						$('input[name$=Date]').datepicker('enable');
						$('input[name=kandang]').data('noreg',_f.val());
						$('input[name=tgldocin]').val(_opt.data('docin'));
						$('input[name=j_jantan]').data('jumlah',_opt.data('jmljantan'));
						$('input[name=j_betina]').data('jumlah',_opt.data('jmlbetina'));
						$('input[name=j_jantan]').val(number_format(_opt.data('jmljantan'),0,',','.'));
						$('input[name=j_betina]').val(number_format(_opt.data('jmlbetina'),0,',','.'));
					}
					else{
						toastr.error('Belum ada rhk yang diinput');
					}
				}
				
			},
			list_cari : function(elm){
				var _form = $(elm).closest('form');
				var _tgl = _form.find('input[name$=Date]');
				var _tanggal = {};
				var _flock = $('select[name=flock]').val();
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
				
				if(empty(_flock)){
					_error++;
					toastr.error('Harus memilih flock terlebih dahulu');
				}
				
				if(!_jmltgl){
					_error++;
					toastr.error('Minimal satu tanggal harus diisi');
				}
				if(!_error){
					var tgl_docin = $('input[name=tgldocin]').val();
					var noreg = $('input[name=kandang]').data('noreg');
					var jml_jantan = $('input[name=j_jantan]').data('jumlah');
					var jml_betina = $('input[name=j_betina]').data('jumlah');
					$.ajax({
						url : 'report/report/detail_rhk',
						type : 'post',
						data : {tanggal : _tanggal, tgl_docin : tgl_docin, noreg : noreg, jml_jantan : jml_jantan, jml_betina : jml_betina},
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
			}
			
			
	};
$(function(){
	'use strict';
	var tgl_server = $('#tanggal_server').data('tanggal_server');
	Rhk.add_datepicker($('input[name=startDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=endDate]').datepicker('option','minDate',date);
			}
			
		},
		disabled : true,
		maxDate : new Date(tgl_server)
	});
	Rhk.add_datepicker($('input[name=endDate]'),{
		dateFormat : 'dd M yy',
		onSelect: function(date,lastDate) {
			if(lastDate.lastVal != date){
				$('input[name=startDate]').datepicker('option','maxDate',date);
			}
		},
		disabled : true,
		maxDate : new Date(tgl_server)
	});
	
	$(document).on('keydown','input[name=kandang]',function(){
		$(this).autocomplete({
		      minLength: 2,
		      source: function( request, response ) {
		          $.ajax({
		        	type : 'post',  
		            url: "report/list_kandang",
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
		    	  Rhk.detail_kandang(ui.item.kode_kandang,ui.item.kode_farm);
		        return false;
		      }
		    })
		    .data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		      return $( "<li  style='font-size:70%'>" )
		        .append( "<span>" + item.nama_kandang + "</span>&nbsp;&nbsp;<span>" + item.kode_farm + "</span>" )
		        .appendTo( ul );
		    };
	});
				
}());