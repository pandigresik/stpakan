<script type="text/javascript">

	function tandai_sampel(elm){
		$.each($(elm).parent().find('tr'), function(key, value) {
			$(this).removeClass('dipilih');
		});
		$(elm).addClass('dipilih');
		var id_vdni = $(elm).attr('data-vdni');
		var printed = $('.dipilih .col-cetak-label').attr('data');
		var sampel = $('.dipilih .col-sampel label').text();
		var nopanggil = $(elm).attr('data-nopanggil');
		var nomerop = $(elm).attr('data-nomerop');
		var sp = $('.dipilih .col-sp').attr('data');
		var sp_label = $('.dipilih .col-sp label').html();
		var lengkap = $('.dipilih .col-lengkap label').html();
		var non_sp = $('.dipilih .col-non-sp').attr('data');
		var nonsp_label = $('.dipilih .col-non-sp label').html();
		var bahan_baku = $('.dipilih .col-bahan-baku label').html();
		var waktu = $('.dipilih .col-cetak-label').attr('stamp');
		$('#id_sampel').html(sampel);

		$('#sample_cetak').val(sampel);
		$('#nopanggil').val(nopanggil);
		$('#nomerop').val(nomerop);
		$('#rm_cetak').val(bahan_baku);
		$('#waktu_cetak').val(waktu);
		$('#sample_cetak2').val(sampel);
		$('#nopanggil2').val(nopanggil);
		$('#nomerop2').val(nomerop);
		$('#rm_cetak2').val(bahan_baku);
		$('#waktu_cetak2').val(waktu);
		$('#vdni_review').val(id_vdni);

		$('#sample_entry').val(sampel);
		$('#sample_review').val(sampel);
		$('#sample_detail').val(sampel);
		$('#sample_detail_composit').val(sampel);
		$('#status_sp').val(sp_label);
		$('#status_nonsp').val(nonsp_label);
		$('#status_sp_entry').val(sp_label);
		$('#status_nonsp_entry').val(nonsp_label);
		$('#composit_entry').val(0);
		$('#composit_review').val(0);
		$('#composit_detail').val(0);
		(lengkap=='YA') ? lengkap = 'LENGKAP' : lengkap = 'BELUM LENGKAP';
		$('#lengkap').val(lengkap);
			
		if($(elm).hasClass('not_composed')){
			$('#composit_cetak').val(2);
			$('#composit_cetak2').val(2);
			
			//$('#btn_cetak').removeClass('disabled');
			//$('#btn_entry').addClass('disabled');
			//$('#btn_review').addClass('disabled');
			//$('#btn_detail').removeClass('disabled');
			//$('#btn_detail_composit').addClass('disabled');
			
			$('#btn_cetak').attr('disabled',false);
			$('#btn_entry').attr('disabled',true);
			$('#btn_review').attr('disabled',true);
			$('#btn_detail').attr('disabled',false);
			$('#btn_detail_composit').attr('disabled',true);
			(printed==1) ? $('#btn_cetak').attr('data-toggle','modal') : $('#btn_cetak').attr('data-toggle','');
			(printed==1) ? $('#btn_cetak').attr('type','button') : $('#btn_cetak').attr('type','submit');
		}
		else{
			if($(elm).hasClass('composed')){
				$('#composit_cetak').val(1);
				$('#composit_cetak2').val(1);
				//$('#btn_detail_composit').removeClass('disabled');
				$('#btn_detail_composit').attr('disabled',false);
				$('#composit_review').val(1);
				$('#composit_entry').val(1);
				$('#composit_detail').val(1);
			}
			else{
				$('#composit_cetak').val(0);
				$('#composit_cetak2').val(0);
				//$('#btn_detail_composit').addClass('disabled');
				$('#btn_detail_composit').attr('disabled',true);
			}

			(printed==1) ? $('#btn_cetak').attr('data-toggle','modal') : $('#btn_cetak').attr('data-toggle','');
			//(printed==1) ? $('#btn_detail').removeClass('disabled') : $('#btn_detail').addClass('disabled');
			(printed==1) ? $('#btn_detail').attr('disabled',false) : $('#btn_detail').attr('disabled',true);
				
			if(printed==1){
				//(sp==0 || non_sp==0) ? $('#btn_entry').removeClass('disabled') : $('#btn_entry').addClass('disabled');
				(sp==0 || non_sp==0) ? $('#btn_entry').attr('disabled',false) : $('#btn_entry').attr('disabled',true);
				$('#btn_cetak').attr('type','button');
			}
			else{
				//$('#btn_entry').addClass('disabled');
				//$('#btn_review').addClass('disabled');
				$('#btn_entry').attr('disabled',true);
				$('#btn_review').attr('disabled',true);

				$('#btn_cetak').attr('type','submit');
					
					
			}

			if(sp==2 && non_sp==2){
				//$('#btn_entry').addClass('disabled');
				//$('#btn_review').addClass('disabled');
				$('#btn_entry').attr('disabled',true);
				$('#btn_review').attr('disabled',true);
			}

			if(sp==1 || non_sp==1){
				//$('#btn_review').removeClass('disabled');
				//$('#btn_entry').removeClass('disabled');
				$('#btn_review').attr('disabled',false);
				$('#btn_entry').attr('disabled',false);
			}
			else{
				//$('#btn_review').addClass('disabled');
				$('#btn_review').attr('disabled',true);
			}
			
			//$('#btn_entry').removeClass('disabled');

		}
	}

	function kontrol_save(elm){
		var id_table = $(elm).parents('table').attr('id');
		var tipe = id_table.split('-');
		var count = 0;
		$.each($('#'+id_table+' tbody').find('tr'), function() {
			var kls = $(this).attr('class');
			//($('.'+kls+' .col-hasil input').val() && parseFloat($('.'+kls+' .col-hasil input').val()) > 0) ? count = count+1 : count = count;
			($('#'+id_table+' .'+kls+' .col-hasil input').val()) ? count = count+1 : count = count;
			($('#'+id_table+' .'+kls+' .col-keterangan select').val()) ? count = count+1 : count = count;
		});
		//(count >=1 ) ? $('#save-'+tipe[1]).removeClass('disabled') : $('#save-'+tipe[1]).addClass('disabled') ;
		(count >=1 ) ? $('#save-'+tipe[1]).attr('disabled',false) : $('#save-'+tipe[1]).attr('disabled',true) ;
	}

	function filter(elm){
		/*
		var kls = $(elm).parent().attr('class');
		var teks = $(elm).val();
		if(teks){
			$('#table-sample tbody .'+kls+':contains("'+teks+'")').parent().show();
			$('#table-sample tbody .'+kls+':not(:contains("'+teks+'"))').parent().hide();
		}
		else{
			$('#table-sample tbody .'+kls).parent().show();
		}
		*/

		$.each($('#table-sample tbody').find('tr'), function() {
			$(this).show();
		})

		var sample = $('#inputSample').val();
		var bahan_baku = $('#inputBahanBaku').val();
		if(sample){
			console.log(sample)
			$('#table-sample tbody tr:visible .col-sampel:not(:contains("'+sample+'"))').parent().hide();
		}
		if(bahan_baku){
			console.log(bahan_baku)
			$('#table-sample tbody tr:visible .col-bahan-baku:not(:contains("'+bahan_baku+'"))').parent().hide();
			
		}
	}

	function filter_checkbox(elm){
		$('#inputSample').val('');
		$('#inputBahanBaku').prop('selectedIndex',0);
		var val = $(elm).val();
        var isChecked = elm.checked;
        isChecked = (isChecked)? $(elm).val('1') : $(elm).val('0');
        generate_table_sample();
	}


	function re_approve_sp(){
		var _url = 'analysis_sample/re_approve_sp';
		var formData = new FormData();
		var sample_prefix = $('#inputNoSampel').val().split('#');
		var sample = sample_prefix[1];
		var vdni = $('#inputVdni').val();
		var segment = $('#inputVehicleSegment').val();
		var composit = $('#composit').val();

		formData.append('no_sampel', sample);
		formData.append('inputVdni', vdni);
		formData.append('inputVehicleSegment', segment);
		formData.append('composit', composit);
		
		$.ajax({
			url 	: _url,
			type 	: 'POST',
			data 	: formData,
			async 	: false,
			cache 	: false,
			contentType : false,
			processData : false,
			dataType    : "json",
			success: function (data) {
				
				if(data.message == 1){
					toastr.success('re-Approval hasil analisis berhasil','Berhasil');
					//$('#re-approve-sp').addClass('disabled');
					$('#re-approve-sp').attr('disabled',true);
				}
				else if(data.message == 2){
					toastr.error('Tidak ada memo insidentil yang aktif','Gagal');
				}
				else{
					toastr.error('Approve Gagal','Gagal');
				}
			}
		});
		
	}

	function generate_table_sample(){
		var lengkap = $('#checkbox_belum_lengkap').val();
		var cek_ot	= $('#checkbox_belum_checkout').val();
		var pending	= $('#checkbox_pending').val();
		$.ajax({
			url 	: 'analysis_sample/filter',
			type 	: 'POST',
			data 	: {
						option1 : lengkap,
						option2 : cek_ot,
						option3 : pending
						 },
			dataType    : "json",
			success: function (data) {
				$('#table-sample tbody').empty();
				if(data.length>0){
					var i = 1;
					var tmp_sample = '';
					$.each(data, function(key, value) {
						if(value.no_sampel != tmp_sample){
							var textAppend = '';
							var kelas;
							if(value.to_be_composed == 1 && !value.item_placement){
								kelas = kelas = 'composed';
							} 
							else if (value.to_be_composed == '0' && value.item_placement != ''){
							}
							else{
								kelas = 'not_composed';
							}
							var nopanggil = value.no_panggil != null ? value.no_panggil : ''; 
							textAppend += '<tr ondblclick="detail_hasil(this)" onclick="tandai_sampel(this)" class="'+i+' '+kelas+'" data-nopanggil="'+nopanggil+'" data-vdni="'+value.id_vdni+'" data-nomerop="'+value.nomerop+'">';
							var bintang = (value.printed == 0) ? '<span class="glyphicon glyphicon-star" style="color:blue;"></span>' : '';
							textAppend += '<td class="col-sampel"><label class="vertical-align">'+value.no_sampel+' '+bintang+'</label></td>';
							textAppend += '<td class="col-bahan-baku"><label class="vertical-align">'+value.item_label+'</label></td>';
							textAppend += '<td data="'+value.printed+'" class="col-cetak-label" stamp="'+value.stamp+'"><label class="vertical-align">'+value.printed_label+'</label></td>';
							if(value.to_be_composed == 1){
								textAppend += '<td data="'+value.sp+'" class="col-sp"><label class="vertical-align">'+value.sp_label+'</label></td>';
								textAppend += '<td data="'+value.non_sp+'" class="col-non-sp"><label class="vertical-align">'+value.non_sp_label+'</label></td>';
								textAppend += '<td class="col-lengkap"><label class="vertical-align">'+value.lengkap+'</label></td>';
							}
							else{

								textAppend += '<td data="'+value.sp_nol+'" class="col-sp"><label class="vertical-align">'+value.sp_label_nol+'</label></td>';
								textAppend += '<td data="'+value.non_sp_nol+'" class="col-non-sp"><label class="vertical-align">'+value.non_sp_label_nol+'</label></td>';
								textAppend += '<td class="col-lengkap"><label class="vertical-align">'+value.lengkap_nol+'</label></td>';
							}

							textAppend += '</tr>';

							$('#table-sample tbody').append(textAppend);

							tmp_sample = value.no_sampel;
							i++;
						}
					})
				}
			}
		});
	}

	function detail_hasil(elm) {
		tandai_sampel(elm);
		$('#form_detail').submit();
	}

	function cetak_label() {
		var sampel = $('.dipilih .col-sampel label').html();
		var waktu = $('.dipilih .col-cetak-label').attr('stamp');
		var bahan_baku = $('.dipilih .col-bahan-baku label').html();
		if(sampel){
			var sample_prefix = sampel.split('#');
			var smpl = sample_prefix[1];
			var toggle = $('#btn_cetak').attr('data-toggle');
			/*
			if($('#btn_cetak').attr('type')=='submit'){
				$('.dipilih .col-cetak-label').attr('data',1);
				$('.dipilih .col-cetak-label label').html('SUDAH');
				$('.dipilih .col-sampel').find('span').remove();
			}
			if(toggle){
				$('.hidden-print').show();
			}
			else{
				printed(sampel,smpl,bahan_baku,waktu);
			}*/
		}
		else{
			toastr.warning('Sampel Belum Dipilih','Peringatan');
		}
	}

	function printed(sampel,smpl,bahan_baku,waktu){
		if(waktu){
			$.ajax({
				url 	: 'analysis_sample/print_sample',
				type 	: 'POST',
				data 	: {
							sample : smpl, rm : bahan_baku, datetime : waktu
							 },
				dataType    : "json",
				success: function (data) {

					$('.dipilih .col-cetak-label').attr('data',1);
					$('.dipilih .col-cetak-label label').html('SUDAH');

					/*
					if(data.message == 1){
		        		$("#label-bahan-baku-sampel").html('Sample 1, '+bahan_baku);
		        		$("#label-waktu-sampel").html(waktu);
		        		$("#label-barcode-sampel").html("").barcode(sampel,'code128');
		       			window.print();
						toastr.success('Cetak Label Sampel '+sampel,'Berhasil');
						$('.dipilih .col-cetak-label').attr('data',1);
						$('.dipilih .col-cetak-label label').html('SUDAH');
						//$("#barcode_sampel").html("").show();
						$('.modal_question').modal('hide');
					}
					else if(data.message == 2){
						toastr.warning('Sampel '+sampel+' Belum Dimulai','Peringatan');
					}
					else{

					}*/
				}
			});
		}
		else{
			toastr.warning('Sampel '+sampel+' Belum Dimulai','Peringatan');
		}

	}

	var resultSubmit;
	function check_printed_composit(){
		if($('.dipilih').hasClass('composed')){
			check_printed_composit_callback()
		}
		return resultSubmit;
  	}

  	function returnSubmitForm(){
  		resultSubmit = true;
  	}
  	
  	function check_printed_composit_callback(){
  		resultSubmit = false;
		var sample = $('#sample_cetak').val().split('#');
	  	$.ajax({ 
			type	: "POST", 
			url 	: 'analysis_sample/printed_sample_children', 
			data 	: {
						sample : sample[1]
					  },
			dataType: "json", 
			async 	: false,
			success	: function(data){
				if(data.result == 0){
					$('.dipilih .col-cetak-label').attr('data',1);
					$('.dipilih .col-cetak-label label').html('SUDAH');
					$('.dipilih .col-sampel').find('span').remove();
					returnSubmitForm();
				}
				else{
					toastr.warning('Ada sampel komposit yang belum dicetak','Peringatan');
				}
			}
		})
		return resultSubmit;
  	}

	function detail_komposit(){
		var sample = $('#sample_detail_composit').val().split('#');
		$.ajax({ 
			type	: "POST", 
			url 	: 'analysis_sample/get_detail_komposit', 
			data 	: {
						sample : sample[1]
					  },
			dataType: "json", 
			success	: function(data){
				$('#list-sample').empty();
				if(data.length >= 1){
					var textAppend  = '';
						textAppend += '<div>';
						textAppend += '<div class="panel panel-default">';
						textAppend += '<div class="panel-body composite-child-panel">';
						textAppend += '<div id="tree"">';
						textAppend += '<ul id="treeData" style="display: none;">';
						textAppend += '<li id="id4" class="folder expanded">'+sample[0]+'#'+sample[1];
						textAppend += '<ul>';
					var i = 1;
					$.each(data,function(key,value){
						var bintang = (value.sampling_start) ? "" : " <star class='glyphicon glyphicon-star'></star>" ;
						textAppend += '<li id="id4.'+i+'">S1#'+value.id+''+bintang;
						i++;
					})
						textAppend += '</ul>';
						textAppend += '</ul>';
						textAppend += '</div>';
						textAppend += "<div class='bintang'><star class='glyphicon glyphicon-star'></star> Belum diambil sampel / belum dicetak.";
						textAppend += '</div>';
						textAppend += '</div>';
						textAppend += '</div>';
						textAppend += '</div>';

						$('#list-sample').append(textAppend);
						$("#tree").fancytree();
				}
				else{
					$('#list-sample').html('Tidak Ada Sampel Komposit');
				}

			}
		})

  	}

	function print_again(){
		var bahan_baku = $('.dipilih .col-bahan-baku label').html();
		var waktu = $('.dipilih .col-cetak-label').attr('stamp');
		var sampel = $('.dipilih .col-sampel label').html();
		var sample_prefix = sampel.split('#');
		var smpl = sample_prefix[1];
		printed(sampel,smpl,bahan_baku,waktu);
	}

	function decimalOnly(obj) {
		var pola = "^[0-9]*[.]*[0-9]*$";
		rx = new RegExp(pola);
		 
		if (!obj.value.match(rx)){
			if (obj.lastMatched){
				obj.value = obj.lastMatched;
		    }
		    else{
		    	obj.value = "";
		    }
		}
		else{
			obj.lastMatched = obj.value;
		}

		kontrol_save(obj);
	}

	/* inisialisasi element audio */
	var suara = document.createElement('audio');
	var source= document.createElement('source');
	if (suara.canPlayType('audio/mpeg;')) {
	    source.type= 'audio/mpeg';
	    source.src= 'assets/sound/emergency007.mp3';
	} else {
	    source.type= 'audio/ogg';
	    source.src= 'assets/sound/emergency007.ogg';
	}
	suara.appendChild(source);
	suara.addEventListener('ended', function() {
	    this.currentTime = 0;
	    this.play();
	}, false);
		
	/* untuk menangani web socket */
	var socket;
	
	function initWebSocket() {
	/* ip dan port harus disesuaikan dengan ip port websocket di server */
		var ip_websocket = "192.168.111.17";
		var port_websocket = "62121"; 
		var host = "ws://"+ip_websocket+":"+port_websocket+"/"; // SET THIS TO YOUR SERVER
		try {
			socket = new FancyWebSocket(host);
		//	socket = new WebSocket(host);
			
			socket.bind('mulaiProbe',function(data){
				bootbox.alert('Proses pengambilan sampel nomor '+data.noSampel+' sudah dimulai');
				suara.play();
			});
		}
		catch(ex){ 
			console.log(ex); 
		}
	}
	

	function quitWebSocket(){
		if (socket != null) {
			console.log("Goodbye!");
			socket.close();
			socket=null;
		}
	}
	
	function reconnect() {
		quitWebSocket();
		initWebSocket();
	}
	
	function matikanSuara(){
		if(!suara.paused){
			suara.pause();
		}
	}
	
	function sudahPenuh(){
		var data = new Object;
		data.message = 'Sudah penuh ....';
		socket.send('sampelPenuh',data);
	}

	var globalKelas;

	function search_class(kls,keyCode,type){
		if($('#list-'+type+' tbody .'+kls+' .col-hasil input').attr('type') == 'hidden'){
			(keyCode == 40) ? kls = kls + 1 : kls = kls - 1;
			search_class(kls, keyCode, type);
		}
		else{
			globalKelas = kls;
		}
	}

	// ================================================================== //


	$(document).ready(function() {

		$( '#list-sp .col-hasil input' ).on( "keydown", function( event ) {
			var kelas = parseInt($(this).parents('tr').attr('class'));
			var type = 'sp';
			switch(event.which){
				case 37:
				    // Key left.
			    break;
			  	case 38:
				    // Key up.
				    kelas = kelas - 1;
				    search_class(kelas,event.which,type);
				    $('#list-sp tbody .'+globalKelas+' .col-hasil input').select();
			    break;
			  	case 39:
				    // Key right.
			    break;
			  	case 40:
				    // Key down.
				    kelas = kelas + 1;
				    search_class(kelas,event.which,type);
				    $('#list-sp tbody .'+globalKelas+' .col-hasil input').select();
			    break;
			}
		})

		$( '#list-nonsp .col-hasil input' ).on( "keydown", function( event ) {
			var kelas = parseInt($(this).parents('tr').attr('class'));
			var type = 'nonsp';
			switch(event.which){
				case 37:
				    // Key left.
			    break;
			  	case 38:
				    // Key up.
				    kelas = kelas - 1;
				    search_class(kelas,event.which,type);
				    $('#list-nonsp tbody .'+globalKelas+' .col-hasil input').select();
			    break;
			  	case 39:
				    // Key right.
			    break;
			  	case 40:
				    // Key down.
				    kelas = kelas + 1;
				    search_class(kelas,event.which,type);
				    $('#list-nonsp tbody .'+globalKelas+' .col-hasil input').select();
			    break;
			}
		})
		
		$('.glyphicon-remove').on('click', function() {
			window.location.href="{base_url()}analysis_sample"
		});

		$('#btn-again').on('click', function() {
			$('.modal_question').modal('hide');
		});

		$("#form-sp-entry").submit( function(e) {
			e.preventDefault();
			var _url = $('#form-sp-entry').attr("action");
			var formData = new FormData();
			var sample_prefix = $('#inputNoSampel').val().split('#');
			var sample = sample_prefix[1];
			$.each($('#form-sp-entry').find('input, select'), function() {
				formData.append($(this).attr("name"), $(this).val());
			});
			formData.append('no_sampel', sample);
			var important = $('#important_sp').val();
			formData.append('important', important);
			$.ajax({
				url 	: _url,
				type 	: 'POST',
				data 	: formData,
				async 	: false,
				cache 	: false,
				contentType : false,
				processData : false,
				dataType    : "json",
				success: function (data) {
					
					if(data.message == 1){
						var list_id_sar = data.list_id_sar;
						var i = 0;
						$.each($('#list-sp tbody').find('tr'), function(key, value) {
							var kls = $(this).attr('class');
							$('#list-sp .'+kls+' .col-number input.id_sar').val(list_id_sar[i]['id']);
							i++;
						});
						toastr.success('Penyimpanan hasil analisis berhasil','Berhasil');
					}
					else{
						toastr.error('Entry Analysis Sample Gagal','Gagal');
					}
				}
			});
		});

		$("#form-nonsp-entry").submit( function(e) {
			e.preventDefault();
			var _url = $('#form-nonsp-entry').attr("action");
			var formData = new FormData();
			var sample_prefix = $('#inputNoSampel').val().split('#');
			var sample = sample_prefix[1];
			$.each($('#form-nonsp-entry').find('input, select'), function() {
				formData.append($(this).attr("name"), $(this).val());
			});
			formData.append('no_sampel', sample);
			var important = $('#important_nonsp').val();
			formData.append('important', important);
			$.ajax({
				url 	: _url,
				type 	: 'POST',
				data 	: formData,
				async 	: false,
				cache 	: false,
				contentType : false,
				processData : false,
				dataType    : "json",
				success: function (data) {
					
					if(data.message == 1){
						var list_id_sar = data.list_id_sar;
						var i = 0;
						$.each($('#list-nonsp tbody').find('tr'), function(key, value) {
							var kls = $(this).attr('class');
							$('#list-nonsp .'+kls+' .col-number input.id_sar').val(list_id_sar[i]['id']);
							i++;
						});
						toastr.success('Penyimpanan hasil analisis berhasil','Berhasil');
					}
					else{
						toastr.error('Entry Analysis Sample Gagal','Gagal');
					}
				}
			});
		});

		$("#form-sp-review").submit( function(e) {
			e.preventDefault();
			var _url = $('#form-sp-review').attr("action");
			var formData = new FormData();
			var sample_prefix = $('#inputNoSampel').val().split('#');
			var sample = sample_prefix[1];
			var vdni = $('#inputVdni').val();
			var segment = $('#inputVehicleSegment').val();
			var composit = $('#composit').val();
			//var klasifikasi_bb = $('#inputKlasisfikasiBahanBaku').val();
			var vendor = $('#inputVendor').val();
			var tipe = $('#inputTipe').val();
			/*
			$.each($('#form-sp').find('input, select'), function() {
				formData.append($(this).attr("name"), $(this).val());
			});*/
			formData.append('no_sampel', sample);
			formData.append('inputVdni', vdni);
			formData.append('inputVehicleSegment', segment);
			formData.append('composit', composit);
			//formData.append('inputKlasisfikasiBahanBaku', klasifikasi_bb);
			formData.append('inputVendor', vendor);
			formData.append('inputTipe', tipe);
			//if(klasifikasi_bb){
				$.ajax({
					url 	: _url,
					type 	: 'POST',
					data 	: formData,
					async 	: false,
					cache 	: false,
					contentType : false,
					processData : false,
					dataType    : "json",
					success: function (data) {
						
						if(data.message == 1){
							toastr.success('Approval hasil analisis berhasil','Berhasil');
							if(data.insert_oracle){
								toastr.warning('Note : Penyimpanan oracle gagal pada tabel : '+data.insert_oracle,'Peringatan');
							}
							//$('#approve-sp').addClass('disabled');
							$('#approve-sp').attr('disabled',true);
						}
						else{
							toastr.error('Approve Gagal','Gagal');
						}
					}
				});
			//}
			//else{
			//	toastr.warning('Klasifikasi Bahan Baku Masih Kosong','Peringatan');
			//}
		});

		$("#form-nonsp-review").submit( function(e) {
			e.preventDefault();
			var _url = $('#form-nonsp-review').attr("action");
			var formData = new FormData();
			var sample_prefix = $('#inputNoSampel').val().split('#');
			var sample = sample_prefix[1];
			formData.append('no_sampel', sample);
			$.ajax({
				url 	: _url,
				type 	: 'POST',
				data 	: formData,
				async 	: false,
				cache 	: false,
				contentType : false,
				processData : false,
				dataType    : "json",
				success: function (data) {
					if(data.message == 1){
						toastr.success('Approval hasil analisis berhasil','Berhasil');
						//$('#approve-nonsp').addClass('disabled');
						$('#approve-nonsp').attr('disabled',true);
					}
					else{
						toastr.error('Approve Gagal','Gagal');
					}
				}
			});
		});
		
	//	initWebSocket();
	}); 
</script>