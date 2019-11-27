var selected_riwayat = '';
var selected_jk = '';
var selected_tk = '';
var selected_musim = '';
var selected_riwayat_date = '';
var mode_riwayat = '';

var html_grup_barang = '';
var html_grup_barang_update = '';

var range_umur_awal = 0;
var range_umur_akhir = 0;

var masa_pertumbuhan_arr = new Array();

var months = new Array(12);
months[0] = "Jan";
months[1] = "Feb";
months[2] = "Mar";
months[3] = "Apr";
months[4] = "May";
months[5] = "Jun";
months[6] = "Jul";
months[7] = "Aug";
months[8] = "Sep";
months[9] = "Oct";
months[10] = "Nov";
months[11] = "Dec";

var months_id = new Array(12);
months_id[0] = "Januari";
months_id[1] = "Februari";
months_id[2] = "Maret";
months_id[3] = "April";
months_id[4] = "Mei";
months_id[5] = "Juni";
months_id[6] = "Juli";
months_id[7] = "Agustus";
months_id[8] = "September";
months_id[9] = "Oktober";
months_id[10] = "Nopember";
months_id[11] = "Desember";

$(document).ready(function () {
	$('#section_riwayat').hide();
	$('#section_kebutuhan_pakan').hide();
	$('#kontrol_input_kebutuhan').hide();
	$('#section_detail_std_budidaya').hide();
	
	$('#btnSaveStd').hide();
});

$('#riwayat-standar-budidaya').on('click','tr',function() {
	if(empty(mode_riwayat)){
		selected_riwayat = $(this).find('td:nth-child(1)').text();
		selected_riwayat_date = $(this).find('td:nth-child(3)').text();
		
		$('#slc-riwayat').html(selected_riwayat);	
		$(this).addClass('highlight').siblings().removeClass('highlight');
				
		if(selected_riwayat != ""){
			$('#btnDetail').removeClass("disabled");
			$('#btnNew').removeClass("disabled");
		}
	}
});

$('.farm_bdy').change(function(){
	var n_checked = 0;
	$('.farm_bdy').each(function() {
		if($(this).is(':checked'))
			n_checked++;
	});
	
	if(n_checked){
		$('#btnSet').removeClass("disabled");
	}else{
		$('#btnSet').addClass("disabled");
	}
});

$('#btnSet').click(function(){
	var filter = false,
		strain = '',
		kode_farm = new Array();
	
	strain = $('#inp_strain').val();
	
	
	$('.farm_bdy').each(function() {
		if($(this).is(':checked')){
			kode_farm.push($(this).val());
		}
	});
	{}
	if(empty(strain))
		filter = false;
	else
		filter = true;
	
	if(filter == true){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/std_budidaya_bdy/get_last_std/",
			data: {
				strain : strain,
				kodefarm : kode_farm
			}
		})
		.done(function(data){
			$("tbody", "#riwayat-standar-budidaya").html("");
			
			window.mydata = data;
			if(!empty(mydata.length)){
				if(mydata.length > 0){
					var record = mydata[0].Rows;
					$.each(record, function (key, data) {
						$("tbody", "#riwayat-standar-budidaya").append(
						'<tr>'+
						'<td class="vert-align">'+data.kode_std_budidaya+'</td>'+
						'<td class="vert-align">'+data.nama_farm+'</td>'+
						'<td class="vert-align">'+data.tgl_efektif_formated+'</td>'+
						'<td class="vert-align">'+data.tgl_akhir_formated+'</td></tr>');
					});
					
					 $('#inp_strain').prop("disabled", true);
					 
					 $('#btnSet').addClass("disabled");
				}
			}
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
			$('#section_riwayat').show();
		});
	}else{
		bootbox.alert("Kolom tidak lengkap!");
	}
});

$('#btnDetail').click(function(){
	mode_riwayat = "detail";
	$('#btnNew').addClass("disabled");	
	$('#inp_tanggalefektif').attr("disabled", true);	
	$('#btnSetDetail').hide();	
	
	if(!empty(selected_riwayat)){
		$( "#inp_tanggalefektif" ).val(selected_riwayat_date);
		
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/std_budidaya_bdy/get_detail_std/",
			data: {
				kode_riwayat : selected_riwayat
			}
		})
		.done(function(data){
			window.mydata = data;
			if(!empty(mydata.length)){
				if(mydata.length > 0){
					var budgetperformance = mydata[0].Head;
					var record = mydata[0].Rows;
					var recordDetail = mydata[0].RowsDetail;
					
					var dh_prc = Number(Math.round(parseFloat(budgetperformance["TARGET_DH_PRC"]) * 100) / 100).toFixed(2);
					$('#inp_bp_daya_hidup').val((dh_prc).toString());
					$('#inp_bp_berat_hidup').val(parseFloat(budgetperformance["TARGET_BB_PRC"]).toString());
					$('#inp_bp_fcr').val(parseFloat(budgetperformance["TARGET_FCR_PRC"]).toString());
					$('#inp_bp_umur_panen').val(parseFloat(budgetperformance["TARGET_UMUR_PANEN"]).toString());
					$('#inp_bp_ip').val(parseFloat(budgetperformance["TARGET_IP"]).toString());
					$('#inp_bp_kum').val(parseFloat(budgetperformance["TARGET_KUM"]).toString());
					
					$('#inp_bp_daya_hidup').attr("disabled", true);
					$('#inp_bp_berat_hidup').attr("disabled", true);
					$('#inp_bp_fcr').attr("disabled", true);
					$('#inp_bp_umur_panen').attr("disabled", true);
					$('#inp_bp_ip').attr("disabled", true);
					$('#inp_bp_kum').attr("disabled", true);
					
					var temp = new Array();
					var index;
					
					index = 0;
					$.each(record, function (key, data) {
						temp[index] = 
						'<tr>'+
						'<td class="vert-align">'+data.umur_awal+'</td>'+
						'<td class="vert-align">'+data.umur_akhir+'</td>'+
						'<td class="vert-align">'+data.nama_barang+'</td></tr>';
						
						index++;
					});
					
					$("tbody", "#detail-standar-budidaya").html("");
					$("tbody", "#detail-standar-budidaya").append(temp.join(''));
					
					temp = new Array();
					index = 0;
					
					$.each(recordDetail, function (key, data) {
						nama_barang = (!(data.nama_barang)) ? "" : data.nama_barang;
						fcr = isNaN(parseFloat(data.FCR)) ? 0 : parseFloat(data.FCR);
						temp[index] = 
						'<tr>'+
						'<td class="vert-align-sm">'+data.STD_UMUR+'</td>'+
						'<td class="vert-align-sm col-dhkum">'+data.DH_KUM_PRC+'</td>'+
						'<td class="vert-align-sm col-dhhr">'+(parseFloat(data.DH_HR_PRC)).toString()+'</td>'+
						'<td class="vert-align-sm col-spkum">'+data.PKN_KUM_STD+'</td>'+
						'<td class="vert-align-sm col-sphr">'+data.PKN_HR_STD+'</td>'+
						'<td class="vert-align-sm col-bpkum" style="background-color:#FAE9CD">'+data.PKN_KUM+'</td>'+
						'<td class="vert-align-sm col-bphr" style="background-color:#FAE9CD">'+data.PKN_HR+'</td>'+
						'<td class="vert-align-sm col-bb">'+data.TARGET_BB+'</td>'+
						'<td class="vert-align-sm col-fcr">'+(fcr).toString()+'</td>'+
						'<td class="vert-align-sm">'+nama_barang+'</td>'+
						'</tr>';
						
						index++;
					});
					
					$("tbody", "#detail-mingguan-standar-budidaya").html("");
					$("tbody", "#detail-mingguan-standar-budidaya").append(temp.join(''));
					
					mergeRow(10);
				}
			}
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
			$('#section_kebutuhan_pakan').show();
			$('#section_detail_std_budidaya').show();
		});
	}
});

$('#btnNew').click(function(){
	mode_riwayat = "baru";
	
	$('#btnNew').addClass("disabled");
	$('#btnDetail').addClass("disabled");
	$('#btnSetDetail').show();	
	$('#btnPrint').hide();	
	
	if(!empty(selected_riwayat)){
		bootbox.dialog({
			message: "Apakah Anda akan membuat standar budidaya baru berdasarkan nomor "+selected_riwayat+"?",
			title: "",
			buttons: {
				main: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						var tgl_efektif = selected_riwayat_date.split(" "); 
						var index = months.indexOf(tgl_efektif[1]);
						tahun = parseInt(tgl_efektif[2]);
						bulan = parseInt(index);
						hari = parseInt(tgl_efektif[0]);
						
						$( "#inp_tanggalefektif" ).datepicker( { 
							dateFormat: 'dd MM yy',
							setDate: new Date(tahun, bulan, hari + 1),
							minDate: new Date(tahun, bulan, hari + 1) 
						});
						
						var nextDate = new Date(tahun, bulan, hari + 1);
						var day = nextDate.getDate();
						var monthIndex = nextDate.getMonth();
						var year = nextDate.getFullYear();
						$( "#inp_tanggalefektif" ).val(day +' '+months[monthIndex]+' '+year);
												
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "master/std_budidaya_bdy/get_detail_std/",
							data: {
								kode_riwayat : selected_riwayat
							}
						})
						.done(function(data){
							$("tbody", "#detail-standar-budidaya").html("");
							$("tbody", "#detail-mingguan-standar-budidaya").html("");
							
							window.mydata = data;
							if(!empty(mydata.length)){
								if(mydata.length > 0){
									var budgetperformance = mydata[0].Head;
									var record = mydata[0].Rows;
									var recordDetail = mydata[0].RowsDetail;
									var grupBarang = mydata[0].GrupBarang;
									
									$('#inp_bp_daya_hidup').val(parseFloat(budgetperformance["TARGET_DH_PRC"]).toString());
									$('#inp_bp_berat_hidup').val(parseFloat(budgetperformance["TARGET_BB_PRC"]).toString());
									$('#inp_bp_fcr').val(parseFloat(budgetperformance["TARGET_FCR_PRC"]).toString());
									$('#inp_bp_umur_panen').val(parseFloat(budgetperformance["TARGET_UMUR_PANEN"]).toString());
									$('#inp_bp_ip').val(parseFloat(budgetperformance["TARGET_IP"]).toString());
									$('#inp_bp_kum').val(parseFloat(budgetperformance["TARGET_KUM"]).toString());
									
									$('#inp_bp_daya_hidup').attr("disabled", false);
									$('#inp_bp_berat_hidup').attr("disabled", false);
									$('#inp_bp_fcr').attr("disabled", false);
									$('#inp_bp_umur_panen').attr("disabled", false);
									$('#inp_bp_ip').attr("disabled", true);
									$('#inp_bp_kum').attr("disabled", true);
									
									var i = 1;
									$.each(record, function (key, data) {
										var select = '<select class="form-control multicolumn" name="jenis_pakan[]" onchange="pilihProdukPakan(this)">';
										select += '<option value="">Pilihan : </option>' + 
												  '<option class="header">Kode Pakan +Nama Produk</option>';
										$.each(grupBarang, function (key, row) {
											selected = (row.grup_barang == data.bentuk_barang) ? 'selected' : '';
											select += '<option value="'+row.grup_barang+'"'+selected+'>'+row.kode_barang + '+' + row.nama_barang + '</option>';
										});
										select += '</select>';
										
										if(i == record.length){
											dis_umur = "";
										}else{
											dis_umur = "disabled";
										}
										
										$("tbody", "#detail-standar-budidaya").append(
										'<tr>'+
										'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_awal[]" value="'+data.umur_awal+'" disabled/></div><center></td>'+
										'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_akhir[]" value="'+data.umur_akhir+'" '+dis_umur+'/></div><center></td>'+
										'<td class="vert-align">'+select+'</td></tr>');
										
										i++;
									});
														
									$('select.multicolumn').combomulticolumn();
									
									$('#btnSetDetail').removeClass("disabled");
									$('#btnSetDetail').show();
									$('#kontrol_input_kebutuhan').show();
								}
							}
						})
						.fail(function(reason){
							console.info(reason);
						})
						.then(function(data){
							$('#section_kebutuhan_pakan').show();
						});
					}
				},
				cancel: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
						$('#btnNew').removeClass("disabled");
						$('#btnDetail').removeClass("disabled");
						$('#btnSetDetail').hide();	
						$('#btnPrint').show();
					}
				}
			}
		});
	}
	else{
		var kode_strain = $('#inp_strain').val();
		
		bootbox.dialog({
			message: "Apakah Anda akan membuat standar budidaya baru?",
			title: "",
			buttons: {
				main: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						var tgl_efektif_arr = new Array();
						var index_tgl = 0;
						var pad = '00';
						$('#riwayat-standar-budidaya > tbody  > tr').each(function() {
							tgl_awal = $(this).find('td:nth-child(2)').text();
							tgl = $(this).find('td:nth-child(3)').text();
							
							tgl = (tgl == '-') ? tgl_awal : tgl;
							
							if(!empty(tgl)){								
								tgl_temp = tgl.split(" "); 
								var index = months.indexOf(tgl_temp[1]);
								tahun = parseInt(tgl_temp[2]);
								bulan = parseInt(index);
								hari = parseInt(tgl_temp[0]);
								
								bulan_pad = (pad + bulan).slice(-pad.length);
								hari_pad = (pad + hari).slice(-pad.length);
								tgl_efektif_arr[index_tgl] = tahun+bulan_pad+hari_pad;
							}
						});
						
						if(tgl_efektif_arr.length > 0){
							tgl_efektif_arr.reverse(); 
							tgl_last = tgl_efektif_arr[0];
							
							
							
							tahun_last = parseInt(tgl_last.substr(0,4));
							bulan_last = parseInt(tgl_last.substr(4,2));
							hari_last = parseInt(tgl_last.substr(6,2));
							
							$( "#inp_tanggalefektif" ).datepicker( { 
								dateFormat: 'dd MM yy',
								setDate: new Date(tahun_last, bulan_last, hari_last + 1),
								minDate: new Date(tahun_last, bulan_last, hari_last + 1) 
							});
							
							var nextDate = new Date(tahun_last, bulan_last, hari_last + 1);
							var day = nextDate.getDate();
							var monthIndex = nextDate.getMonth();
							var year = nextDate.getFullYear();
							$( "#inp_tanggalefektif" ).val(day +' '+months_id[monthIndex]+' '+year);					
						}else{
							$( "#inp_tanggalefektif" ).datepicker( { 
								dateFormat: 'dd MM yy'
							});
						}
						
						$( "#kontrol_input_kebutuhan" ).show();
						
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "master/std_budidaya_bdy/get_data_kebutuhanpakan/",
							data: {
								kode_strain : kode_strain
							}
						})
						.done(function(data){							
							$("tbody", "#detail-standar-budidaya").html("");
							
							var select = '<select class="form-control multicolumn" name="jenis_pakan[]" onchange="pilihProdukPakan(this)">';
							select += '<option value="">Pilihan : </option>' + 
									  '<option class="header">Kode Pakan +Nama Produk</option>';

							$.each(data.grup_barang, function (key, row) {
								select += '<option value="'+row.grup_barang+'">'+row.kode_barang + '+' + row.nama_barang + '</option>';
							});
							select += '</select>';
							
							html_grup_barang = select;
							
							html = '<tr>'+
							'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_awal[]" value="1" disabled/></div><center></td>'+
							'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" onkeyup="cekNumerikUmur(this)" style="text-align:center" type="text" name="umur_akhir[]" value=""/></div><center></td>'+
							'<td class="vert-align">'+select+'</td></tr>';
							
							$(html).appendTo("#detail-standar-budidaya > tbody");		
							$('select.multicolumn').combomulticolumn();			
							
							var select_parent = $('select.multicolumn').parent();
							html_grup_barang = $(select_parent).html();
						})
						.fail(function(reason){
							console.info(reason);
						})
						.then(function(data){
							$('#section_kebutuhan_pakan').show();
						});
					}
				},
				cancel: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
						mode_riwayat = "";
				
						$('#btnNew').removeClass("disabled");
						$('#btnSetDetail').hide();	
						$('#btnPrint').show();
					}
				}
			}
		});
	}
});

$('#linkTambah').on('click', function() {
	var valid = true;
	var currVal = 0;
	var currIndex = 0;
	var umur_awal = new Array();
	var umur_awal = new Array();
	var jenis_pakan = new Array();
	
	range_umur_akhir = $('#inp_bp_umur_panen').val();
	
	$('input[name^="umur_awal"]').each(function() {
		umur_awal.push($(this).val());
	});
	
	$('select[name^="jenis_pakan"]').each(function() {
		jenis_pakan.push($(this).val());
	});
	
	$('input[name^="umur_akhir"]').each(function() {
		attr = $(this).attr('disabled');
		currVal = parseInt($(this).val());
		
		if(alert !="disabled"){
			if(empty($(this).val())){
				bootbox.alert("Umur akhir masih kosong");
				valid = false;
				
				return false;
			}else if(empty(jenis_pakan[currIndex])){
				bootbox.alert("Jenis pakan masih kosong");
				valid = false;
				
				return false;
			}
			else{
				if(currVal <= umur_awal[currIndex]){
					bootbox.alert("Umur akhir harus lebih besar dari umur awal");
					$(this).val('');
					
					valid = false;
					
					return false;
				}
				
				if(currVal > range_umur_akhir){
					bootbox.alert("Umur akhir maksimal adalah " + range_umur_akhir);
					$(this).val('');
					
					valid = false;
					
					return false;
				}
				
				if(currVal == range_umur_akhir){
					valid = false;
					$(this).prop("disabled", true);
					$('#btnSetDetail').removeClass("disabled");
					
					return false;
				}
			}
		}
		
		currIndex++;
	});
	
	currVal= (currVal+1);
	
	element = '<tr>'+
	'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_awal[]" value="'+currVal+'" disabled/></div><center></td>'+
	'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" onkeyup="cekNumerikUmur(this)" type="text" name="umur_akhir[]" value=""/></div><center></td>'+
	'<td class="vert-align">'+html_grup_barang+'</td></tr>';
	
	if(valid){	
		$('input[name^="umur_akhir"]').prop("disabled", true);
		$(element).appendTo("#detail-standar-budidaya > tbody");
	}
});

$('#btnSetDetail').click(function(){
	tgl_efektif = $( "#inp_tanggalefektif" ).val();
	
	var inp_bp_daya_hidup = $('#inp_bp_daya_hidup').val();
	var inp_bp_berat_hidup = $('#inp_bp_berat_hidup').val();
	var inp_bp_fcr = $('#inp_bp_fcr').val();
	var inp_bp_umur_panen = $('#inp_bp_umur_panen').val();
	var inp_bp_ip = $('#inp_bp_ip').val();
	var inp_bp_kum = $('#inp_bp_kum').val();
	
	range_umur_akhir = $('#inp_bp_umur_panen').val();
	
	if(empty(tgl_efektif)){
		bootbox.alert("Tanggal efektif belum ditentukan");
		return false;
	}
	
	var umur_akhir = new Array();
	var index = 0;
	$('input[name^="umur_akhir"]').each(function() {
		umur_akhir[index] = parseInt($(this).val())
		index++;
	});
	
	if(umur_akhir[umur_akhir.length - 1] > range_umur_akhir){
		bootbox.alert("Umur akhir maksimal adalah " + range_umur_akhir);
		return false;
	}
	
	if(!empty(selected_riwayat)){
		$('#inp_tanggalefektif').prop("disabled", true);

		mode_riwayat = "update";
		
		var umur_awal = new Array();
		var umur_akhir = new Array();
		var jenis_pakan = new Array();
		
		kode_strain = $('#inp_strain').val();
		
		$('input[name^="umur_awal"]').each(function() {
			umur_awal.push($(this).val());
		});
		
		$('input[name^="umur_akhir"]').each(function() {
			umur_akhir.push($(this).val());
		});
		
		$('select[name^="jenis_pakan"]').each(function() {
			jenis_pakan.push($(this).val());
		});
		
		for(var i=0;i<jenis_pakan.length;i++){
			if(empty(jenis_pakan[i])){
				toastr.warning("Produk pakan ada yang belum ditentukan",'Peringatan');
				
				return false;
			}
		}

		tgl_efektif = $( "#inp_tanggalefektif" ).val();
		var tgl_efektif_arr = tgl_efektif.split(" "); 
		var index = (months.indexOf(tgl_efektif_arr[1]) >= 0) ? months.indexOf(tgl_efektif_arr[1]) : months_id.indexOf(tgl_efektif_arr[1]);
		tahun = tgl_efektif_arr[2];
		bulan = (parseInt(index) + 1);
		hari = tgl_efektif_arr[0];
		
		tgl_efektif = tahun + "-" + bulan + "-" + hari;
		
		// $.ajax({
			// type:'POST',
			// dataType: 'json',
			// url : "master/std_budidaya_bdy/add_std_budidaya/",
			// data: {
				// kode_strain : kode_strain,
				// umur_awal : umur_awal,
				// umur_akhir : umur_akhir,
				// jenis_pakan : jenis_pakan,
				// kode_riwayat : selected_riwayat,
				// tgl_efektif : tgl_efektif,
				// bp_daya_hidup : inp_bp_daya_hidup,
				// bp_berat_hidup : inp_bp_berat_hidup,
				// bp_fcr : inp_bp_fcr,
				// bp_umur_panen : inp_bp_umur_panen,
				// bp_ip :inp_bp_ip,
				// bp_kum : inp_bp_kum
			// }
		// })
		// .done(function(data){
			// if(data.result == "success"){
				var kode_std_budidaya = selected_riwayat;
				// var kode_std_budidaya = data.kode_std_budidaya;
				
				$.ajax({
					type:'POST',
					dataType: 'json',
					url : "master/std_budidaya_bdy/get_detail_std/",
					data: {
						kode_riwayat : selected_riwayat
						// kode_riwayat : data.kode_std_budidaya
					}
				})
				.done(function(data){
					selected_riwayat = kode_std_budidaya;
					
					$('#btnSetDetail').addClass("disabled");
					$('[name="jenis_pakan[]"]').prop("disabled", true);
					$('[name="umur_akhir[]"]').prop("disabled", true);
					$("tbody", "#detail-mingguan-standar-budidaya").html("");
					
					window.mydata = data;
					if(!empty(mydata.length)){
						if(mydata.length > 0){
							var recordDetail = mydata[0].RowsDetail;
							
							var temp = new Array();
							var index = 0;
							
							$.each(recordDetail, function (key, data) {
								var bp_kum = data.PKN_KUM, 
								    bp_hr = data.PKN_HR;
									
								if((index+1) == inp_bp_umur_panen){
									bp_kum = inp_bp_kum * 1000;
									bp_hr = bp_kum - recordDetail[index-1].PKN_KUM;
								}
								
								nama_barang = (!(data.nama_barang)) ? "" : data.nama_barang;
								
								fcr = isNaN(parseFloat(data.FCR)) ? 0 : parseFloat(data.FCR);
								
								temp[index] = '<tr>'+
								'<td class="vert-align-sm">'+data.STD_UMUR+'<input type="hidden" name="col_umurminggu[]" value="'+data.STD_UMUR+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+index+'-1" id="cell_'+index+'-1" class="form-control input-sm inp-right no-border" size="3" name="col_dhkum[]" value="'+data.DH_KUM_PRC+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+index+'-2" id="cell_'+index+'-2" class="form-control input-sm inp-right no-border" size="3" name="col_dhhr[]" onkeyup="calculateDH_KUM(this)" value="'+(parseFloat(data.DH_HR_PRC)).toString()+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+index+'-3" id="cell_'+index+'-3" class="form-control input-sm inp-right no-border" size="3" name="col_spkum[]" onkeyup="calculateSP_HR(this)" value="'+data.PKN_KUM_STD+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+index+'-4" id="cell_'+index+'-4" class="form-control input-sm inp-right no-border" size="3" name="col_sphr[]" value="'+data.PKN_HR_STD+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+index+'-5" id="cell_'+index+'-5" class="form-control input-sm inp-right no-border" size="3" name="col_bpkum[]" value="'+bp_kum+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+index+'-6" id="cell_'+index+'-6" class="form-control input-sm inp-right no-border" size="3" name="col_bphr[]" value="'+bp_hr+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+index+'-7" id="cell_'+index+'-7" class="form-control input-sm inp-right no-border" size="3" name="col_bb[]" onkeyup="cekNumerik(this)" value="'+data.TARGET_BB+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+index+'-8" id="cell_'+index+'-8" class="form-control input-sm inp-right no-border" size="3" name="col_fcr[]" onkeyup="cekNumerik(this)" value="'+(fcr).toString()+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+nama_barang+'</td>'+
								'</tr>';
							
								index++;
							});
							
							for(var i = recordDetail.length;i<42;i++){
								fcr = isNaN(recordDetail[i].FCR) ? 0 : recordDetail[i].FCR;
								temp[i] = '<tr>'+
								'<td class="vert-align-sm">'+recordDetail[i].STD_UMUR+'<input type="hidden" name="col_umurminggu[]" value="'+recordDetail[i].STD_UMUR+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-1" id="cell_'+i+'-1" class="form-control input-sm inp-right no-border" size="3" name="col_dhkum[]" onkeyup="cekNumerik(this)" value="'+recordDetail[i].DH_KUM_PRC+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-2" id="cell_'+i+'-2" class="form-control input-sm inp-right no-border" size="3" name="col_dhhr[]" onkeyup="cekNumerik(this)" value="'+(parseFloat(recordDetail[i].DH_HR_PRC)).toString()+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-3" id="cell_'+i+'-1" class="form-control input-sm inp-right no-border" size="3" name="col_spkum[]" onkeyup="cekNumerik(this)" value="'+recordDetail[i].PKN_KUM_STD+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-4" id="cell_'+i+'-4" class="form-control input-sm inp-right no-border" size="3" name="col_sphr[]" onkeyup="cekNumerik(this)" value="'+recordDetail[i].PKN_HR_STD+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+i+'-5" id="cell_'+i+'-5" class="form-control input-sm inp-right no-border" size="3" name="col_bpkum[]" onkeyup="cekNumerik(this)" value="'+recordDetail[i].PKN_KUM+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+i+'-6" id="cell_'+i+'-6" class="form-control input-sm inp-right no-border" size="3" name="col_bphr[]" onkeyup="cekNumerik(this)" value="'+recordDetail[i].PKN_HR+'" disabled/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-7" id="cell_'+i+'-7" class="form-control input-sm inp-right no-border" size="3" name="col_bb[]" onkeyup="cekNumerik(this)" value="'+recordDetail[i].TARGET_BB+'"/>'+'</td>'+
								'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-8" id="cell_'+i+'-8" class="form-control input-sm inp-right no-border" size="3" name="col_fcr[]" onkeyup="cekNumerik(this)" value="'+fcr+'"/>'+'</td>'+
								'<td class="vert-align-sm"></td>'+
								'</tr>';
							}
							
							$("tbody", "#detail-mingguan-standar-budidaya").html("");
							$("tbody", "#detail-mingguan-standar-budidaya").append(temp.join(''));
							$("tbody", "#detail-mingguan-standar-budidaya").formNavigation();
							mergeRow(10);
						}
					}
				})
				.fail(function(reason){
					console.info(reason);
				})
				.then(function(data){
					$('#inp_bp_daya_hidup').attr("disabled", true);
					$('#inp_bp_berat_hidup').attr("disabled", true);
					$('#inp_bp_fcr').attr("disabled", true);
					$('#inp_bp_umur_panen').attr("disabled", true);
					
					$('#section_detail_std_budidaya').show();
					$('#btnSaveStd').show();
				});						
			// }
			// else{
				// toastr.warning("Proses simpan gagal!",'Informasi');
			// }
		// })
		// .fail(function(reason){
			// console.info(reason);
		// })
		// .then(function(data){
			// $('#btnSaveStd').show();
		// });
					
	}
	else{
		mode_riwayat = "baru";
		
		var kode_strain = "";
				
		var umur_awal = new Array();
		var umur_akhir = new Array();
		var jenis_pakan = new Array();
		var jenis_pakan_desk = new Array();
		
		kode_strain = $('#inp_strain').val();
		
		$('input[name^="umur_awal"]').each(function() {
			umur_awal.push($(this).val());
		});
		
		$('input[name^="umur_akhir"]').each(function() {
			umur_akhir.push($(this).val());
		});
		
		$('select[name^="jenis_pakan"]').each(function() {
			jenis_pakan.push($(this).val());
			
			var pakan_val = $(this).val();
			var pakan_txt = $(this).children(':selected').text();
			var pakan_val_arr = pakan_val.split('*');
			jenis_pakan_desk.push(pakan_val_arr[0] + '<br/>' + (pakan_txt.replace(pakan_val_arr[0], '')).trim());
		});
		
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/std_budidaya_bdy/get_masa_pertumbuhan/",
			data: {
			}
		})
		.done(function(data){
			var temp = new Array();
			
			$('#btnSetDetail').addClass("disabled");
			$('[name="jenis_pakan[]"]').prop("disabled", true);
			$('[name="umur_akhir[]"]').prop("disabled", true);
			
			for(var i=0;i<umur_awal.length;i++){
				for(var j=parseInt(umur_awal[i]);j<=parseInt(umur_akhir[i]);j++){
					var obj = data[j]; 
					
					temp[j] = '<tr>'+
					'<td class="vert-align-sm">'+j+'<input type="hidden" name="col_umurminggu[]" value="'+j+'"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-1" id="cell_'+j+'-1" class="form-control input-sm inp-right no-border" size="3" name="col_dhkum[]" disabled/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-2" id="cell_'+j+'-2" class="form-control input-sm inp-right no-border" size="3" name="col_dhhr[]" onkeyup="calculateDH_KUM(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-3" id="cell_'+j+'-3" class="form-control input-sm inp-right no-border" size="3" name="col_spkum[]" onkeyup="calculateSP_HR(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-4" id="cell_'+j+'-4" class="form-control input-sm inp-right no-border" size="3" name="col_sphr[]" disabled/>'+'</td>'+
					'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+j+'-5" id="cell_'+j+'-5" class="form-control input-sm inp-right no-border" size="3" name="col_bpkum[]" disabled/>'+'</td>'+
					'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+j+'-6" id="cell_'+j+'-6" class="form-control input-sm inp-right no-border" size="3" name="col_bphr[]" disabled/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-7" id="cell_'+j+'-7" class="form-control input-sm inp-right no-border" size="3" name="col_bb[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-8" id="cell_'+j+'-8" class="form-control input-sm inp-right no-border" size="3" name="col_fcr[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+jenis_pakan_desk[i]+'</td>'+
					'</tr>';
				}
			}
			
			var n_detail = temp.length;
			for(var i = n_detail;i<=42;i++){
				temp[i] = '<tr>'+
				'<td class="vert-align-sm">'+i+'<input type="hidden" name="col_umurminggu[]" value="'+i+'"/>'+'</td>'+
				'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-1" id="cell_'+i+'-1" class="form-control input-sm inp-right no-border" size="3" name="col_dhkum[]" value="" disabled/>'+'</td>'+
				'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-2" id="cell_'+i+'-2" class="form-control input-sm inp-right no-border" size="3" name="col_dhhr[]" onkeyup="calculateDH_KUM(this)" value=""/>'+'</td>'+
				'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-3" id="cell_'+i+'-3" class="form-control input-sm inp-right no-border" size="3" name="col_spkum[]" onkeyup="calculateSP_HR(this)" value=""/>'+'</td>'+
				'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-4" id="cell_'+i+'-4" class="form-control input-sm inp-right no-border" size="3" name="col_sphr[]" value="" disabled/>'+'</td>'+
				'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+i+'-5" id="cell_'+i+'-5" class="form-control input-sm inp-right no-border" size="3" name="col_bpkum[]" value="" disabled/>'+'</td>'+
				'<td class="vert-align-sm" style="background-color:#FAE9CD">'+'<input type="text" data-index="'+i+'-6" id="cell_'+i+'-6" class="form-control input-sm inp-right no-border" size="3" name="col_bphr[]" value="" disabled/>'+'</td>'+
				'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-7" id="cell_'+i+'-7" class="form-control input-sm inp-right no-border" size="3" name="col_bb[]" onkeyup="cekNumerik(this)" value=""/>'+'</td>'+
				'<td class="vert-align-sm">'+'<input type="text" data-index="'+i+'-8" id="cell_'+i+'-8" class="form-control input-sm inp-right no-border" size="3" name="col_fcr[]" onkeyup="cekNumerik(this)" value="'+''+'"/>'+'</td>'+
				'<td class="vert-align-sm"></td>'+
				'</tr>';
			}
			
			$("tbody", "#detail-mingguan-standar-budidaya").html("");
			$("tbody", "#detail-mingguan-standar-budidaya").append(temp.join(''));
			$("tbody", "#detail-mingguan-standar-budidaya").formNavigation();
			// isiDataDummy();
			mergeRow(10);
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
			$('#inp_bp_daya_hidup').attr("disabled", true);
			$('#inp_bp_berat_hidup').attr("disabled", true);
			$('#inp_bp_fcr').attr("disabled", true);
			$('#inp_bp_umur_panen').attr("disabled", true);
					
			$('#section_detail_std_budidaya').show();
			$('#btnSaveStd').show();
		});
	}
});

//--------------------BATAS MANAGED FOR BUDIDAYA---------------------------------------------------------

function print_std(elm){	
	var tgl_efektif = selected_riwayat_date.split(" "); 
	var index = months.indexOf(tgl_efektif[1]);
	tahun = parseInt(tgl_efektif[2]);
	bulan = parseInt(index);
	hari = parseInt(tgl_efektif[0]);
	
	OpenInNewTab("master/std_budidaya_bdy/cetak_std?riwayat_date="+tgl_efektif+"&kode_riwayat="+selected_riwayat);
}

function OpenInNewTab(url) {
  var win = window.open(url, '_blank');
  win.focus();
}

$('#linkHapus').on('click', function(){
	var index = $("#detail-standar-budidaya > tbody > tr").length;
	
	if(index == 1){
		$('input[name^="umur_akhir"]').prop("disabled", false);
		$('input[name^="umur_akhir"]').val('');
		$('select[name^="jenis_pakan"]').val('');
	}else{
		$('#detail-standar-budidaya tbody tr').eq(index-1).remove();
	}
	
	$('#btnSetDetail').addClass("disabled");
});

function pilihProdukPakan(elm){
	if(empty(selected_riwayat)){
		var max_umur = $("#inp_bp_umur_panen").val();
		var tr = $(elm).parent().parent();
		var umur_akhir = $(tr).find('td').eq(1).find('input');
		var min_umur = $(tr).find('td').eq(0).find('input').val();
			
		if(!empty($(elm).val()) && !empty($(umur_akhir).val()) && parseInt($(umur_akhir).val()) <= parseInt(max_umur) && parseInt($(umur_akhir).val()) > parseInt(min_umur)){
			$('#btnSetDetail').removeClass("disabled");
		}else{
			$('#btnSetDetail').addClass("disabled");
		}
	}
}

function cekBpNumerik(field){
	var re = /^[0-9-'.']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.']/g,"");
	}

	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");	
	
	calculateIP();
	calculateKUM()
}

function cekBpDecimal(field){
	var re = /^[0-9-'.']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.']/g,"");
	} 
	
	if(!empty($(field).val())){
		if((field.value).charAt(0) == '0' && (field.value).charAt(1) != '.')
			$(field).val(parseFloat(field.value) * 1);
		else
			$(field).val(field.value);
	}else{
		$(field).val("0");
	}
	
	calculateIP();
	calculateKUM()
}

function calculateIP(){
	var dh = ($('#inp_bp_daya_hidup').val() != '') ? $('#inp_bp_daya_hidup').val() : 0;
	var bb = ($('#inp_bp_berat_hidup').val() != '') ? $('#inp_bp_berat_hidup').val() : 0;
	var fcr = ($('#inp_bp_fcr').val() != '') ? $('#inp_bp_fcr').val() : 0;
	var umur = ($('#inp_bp_umur_panen').val() != '') ? $('#inp_bp_umur_panen').val() : 0;
	
	var result = 0;
	if(fcr > 0 && umur > 0)
		result = ((parseFloat(dh)/100) * parseFloat(bb) * 10000) / (parseFloat(fcr) * parseInt(umur));
	
	$('#inp_bp_ip').val(Number(Math.round(result * 1000) / 1000).toFixed(0));
}

function calculateKUM(){
	var dh = ($('#inp_bp_daya_hidup').val() != '') ? $('#inp_bp_daya_hidup').val() : 0;
	var bb = ($('#inp_bp_berat_hidup').val() != '') ? $('#inp_bp_berat_hidup').val() : 0;
	var fcr = ($('#inp_bp_fcr').val() != '') ? $('#inp_bp_fcr').val() : 0;
	
	var result = 0;
	result = parseFloat(dh/100) * parseFloat(bb) * parseFloat(fcr);
	
	$('#inp_bp_kum').val(Number(Math.round(result * 1000) / 1000).toFixed(3));
}

function calculateDH_KUM(field){
	var re = /^[0-9'.']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9'.']/g,"");
	} 
	
	if(!empty($(field).val())){
		var temp = ($(field).val()).split('.');
		if(temp.length > 2){
			$(field).val(temp[0]+'.'+temp[1]);
		}else{
			if((field.value).charAt(0) == '0' && (field.value).charAt(1) != '.')
				$(field).val(parseFloat(field.value) * 1);
			else
				$(field).val(field.value);
		}
	}else{
		$(field).val("0");
	}
	
	var curr_index = (($(field).attr("data-index")).split('-'))[0];
	var arr_kum = new Array();
	var arr_hr = new Array();
	
	$('input[name^="col_dhhr"]').each(function() {
		arr_hr.push($(this).val());
	});
	
	$('input[name^="col_dhkum"]').each(function() {
		arr_kum.push($(this).val());
	});
	
	for(var i=0;i<arr_kum.length;i++){
		var kum = 0;
		if(i == 0){
			kum = 100 - parseFloat(arr_hr[i]);
		}else{
			kum = parseFloat(arr_kum[i-1]) - parseFloat(arr_hr[i]);
		}
		
		arr_kum[i] = Number(Math.round(kum * 100) / 100).toFixed(2);
		if(!isNaN(Number(Math.round(kum * 100) / 100).toFixed(2)))
			$('#cell_'+(i+1)+'-1').val(Number(Math.round(kum * 100) / 100).toFixed(2));	
	}
}

function calculateSP_HR(field){
	var re = /^[0-9]*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9]/g,"");
	} 
	
	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");
	
	var curr_index = (($(field).attr("data-index")).split('-'))[0];
	var umur_panen = $('#inp_bp_umur_panen').val();
	var kum = $('#inp_bp_kum').val();
	var arr_kum = new Array();
	
	$('#cell_'+umur_panen+'-5').val((kum*1000));
	
	$('input[name^="col_spkum"]').each(function(){
		arr_kum.push($(this).val());
	});
	
	for(var i=0;i<arr_kum.length;i++){
		var hr = 0;
		
		if(i == 0){
			hr = arr_kum[i] - 0;
		}else{
			hr = arr_kum[i] - arr_kum[i-1];
		}
		
		$('#cell_'+(i+1)+'-4').val(hr);
		
		if(((i+1))<umur_panen){
			$('#cell_'+(i+1)+'-5').val(arr_kum[i]);
			$('#cell_'+(i+1)+'-6').val(hr);
		}else if(((i+1)) == umur_panen){
			$('#cell_'+(i+1)+'-5').val((kum*1000));
			$('#cell_'+(i+1)+'-6').val((kum*1000) - arr_kum[i-1]);
		}else{
			$('#cell_'+(i+1)+'-5').val(0);
			$('#cell_'+(i+1)+'-6').val(0);
		}
	}
}

function cekNumerik(field){	
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	} 
	
	
}

function cekNumerikUmur(elm){
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(elm.value)) {
		elm.value = elm.value.replace(/[^0-9-'.'-',']/g,"");
	} 
	
	if(empty(selected_riwayat)){
		var max_umur = $("#inp_bp_umur_panen").val();
		var tr = $(elm).parent().parent().parent().parent();
		var min_umur = $(tr).find('td').eq(0).find('input').val();
		var pakan = $(tr).find('td').eq(2).find('select');
			
		if(!empty($(elm).val()) && !empty($(pakan).val()) && parseInt($(elm).val()) <= parseInt(max_umur) && parseInt($(elm).val()) > parseInt(min_umur)){
			$('#btnSetDetail').removeClass("disabled");
		}else{
			$('#btnSetDetail').addClass("disabled");
		}
	}
}

function mergeRow(jenisPakanCol){
	$('#detail-mingguan-standar-budidaya').each(function () {

		var JP_Previous_TD = null;
		var j = 1;
		$("tbody",this).find('tr').each(function () {
			var JP_Current_td = $(this).find('td:nth-child(' + jenisPakanCol + ')');
			
			if (JP_Previous_TD == null) {
				JP_Previous_TD = JP_Current_td;
				j = 1;
			} 
			else if (JP_Current_td.text() != '' && JP_Current_td.text() == JP_Previous_TD.text()) {				
				JP_Current_td.remove();
				JP_Previous_TD.attr('rowspan', j + 1);
				j = j + 1;
			} 
			else {				
				JP_Previous_TD = JP_Current_td;
				j = 1;
			}
		});
	});
}

function simpanStandarBaru(){
	var valid = true;
	
	var budget_daya_hidup = $('#inp_bp_daya_hidup').val();
	var budget_berat_hidup = $('#inp_bp_berat_hidup').val();
	var budget_fcr = $('#inp_bp_fcr').val();
	var budget_umur_panen = $('#inp_bp_umur_panen').val();
	var budget_ip = $('#inp_bp_ip').val();
	var budget_kum = $('#inp_bp_kum').val();
	
	var umur_arr = new Array();
	var dh_kum = new Array();
	var dh_hr = new Array();
	var sp_kum = new Array();
	var sp_hr = new Array();
	var bp_kum = new Array();
	var bp_hr = new Array();
	var bb = new Array();
	var fcr = new Array();
	
	if(valid){
		var i=0;
		
		$('input[name^="col_umurminggu"]').each(function() {
			umur_arr.push($(this).val());
			i++;
		});
		
		i=0;
		$('input[name^="col_dhkum"]').each(function() {
			var col_kum = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			dh_kum.push(col_kum);
			i++;
		});
		
		i=0;
		$('input[name^="col_dhhr"]').each(function() {
			var col_hr = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			dh_hr.push(col_hr);
			i++;
		});
		
		i=0;
		$('input[name^="col_spkum"]').each(function() {
			var col_kum = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			sp_kum.push(col_kum);
			i++;
		});
		
		i=0;
		$('input[name^="col_sphr"]').each(function() {
			var col_hr = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			sp_hr.push(col_hr);
			i++;
		});
		
		i=0;
		$('input[name^="col_bpkum"]').each(function() {
			var col_kum = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			bp_kum.push(col_kum);
			i++;
		});
		
		i=0;
		$('input[name^="col_bphr"]').each(function() {
			var col_hr = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			bp_hr.push(col_hr);
			i++;
		});
		
		i=0;
		$('input[name^="col_bb"]').each(function() {
			var col = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			bb.push(col);
			i++;
		});
				
		i=0;
		$('input[name^="col_fcr"]').each(function() {
			var col = (!empty($(this).val()) && $(this).val() != "-") ? $(this).val() : 0;
			
			fcr.push(col);
			i++;
		});
		
		if(cekDayaHidup(dh_kum) && cekStandarPakan(sp_kum, sp_hr) && cekBudgetPakan(bp_kum, bp_hr) && cekBBFCR(bb, fcr))
			valid = true;
		else{
			valid = false;
		}
		
		if(valid){
			if(mode_riwayat == "baru"){
				//baru
				var kode_strain = "";
				var kode_farm = new Array();
				
				var umur_awal = new Array();
				var umur_akhir = new Array();
				var jenis_pakan = new Array();
				
				kode_strain = $('#inp_strain').val();
				
				var tgl_efektif = $( "#inp_tanggalefektif" ).val();
				var tgl_efektif_arr = tgl_efektif.split(" "); 
				var index = (months.indexOf(tgl_efektif_arr[1]) >= 0) ? months.indexOf(tgl_efektif_arr[1]) : months_id.indexOf(tgl_efektif_arr[1]);
				
				tahun = tgl_efektif_arr[2];
				bulan = (parseInt(index) + 1);
				hari = tgl_efektif_arr[0];
				
				tgl_efektif = tahun + "-" + bulan + "-" + hari;
				
				$('.farm_bdy').each(function() {
					if($(this).is(':checked'))
						kode_farm.push($(this).val());
				});
				
				$('input[name^="umur_awal"]').each(function() {
					umur_awal.push($(this).val());
				});
				
				$('input[name^="umur_akhir"]').each(function() {	
					umur_akhir.push($(this).val());
				});
				
				$('select[name^="jenis_pakan"]').each(function() {
					jenis_pakan.push($(this).val());
				});
				
				bootbox.dialog({
					message: "Apakah anda yakin akan melanjutkan proses simpan?",
					title: "",
					buttons: {
						main: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								$.ajax({
									type:'POST',
									dataType: 'json',
									url : "master/std_budidaya_bdy/simpan_std_budidaya/",
									data: {
										kode_riwayat : selected_riwayat,
										kode_strain : kode_strain,
										kode_farm : kode_farm,
										budget_daya_hidup 	: budget_daya_hidup,
										budget_berat_hidup 	: budget_berat_hidup,
										budget_fcr 			: budget_fcr,
										budget_umur_panen 	: budget_umur_panen,
										budget_ip 			: budget_ip,
										budget_kum 			: budget_kum,
										umur_awal 		: umur_awal,
										umur_akhir 		: umur_akhir,
										jenis_pakan 	: jenis_pakan,
										tgl_efektif 	: tgl_efektif,
										col_umur 		: umur_arr,
										dh_kum 			: dh_kum,
										dh_hr 			: dh_hr,
										sp_kum 			: sp_kum,
										sp_hr 			: sp_hr,
										bp_kum 			: bp_kum,
										bp_hr 			: bp_hr,
										bb 				: bb,
										fcr 			: fcr
									}
								})
								.done(function(data){
									if(data.result == "success"){	
										$('#btnSaveStd').addClass("disabled");
										
										toastr.success("Proses Simpan Berhasil",'Informasi');
									}else
										toastr.warning("Proses Simpan Gagal",'Informasi');
								})
								.fail(function(reason){
									console.info(reason);
								})
								.then(function(data){
								});
							}
						},
						cancel: {
							label: "Tidak",
							className: "btn-default",
							callback: function() {
							}
						}
					}
				});
			}else{
				
				var kode_strain = $('#inp_strain').val();
				var kode_farm = new Array();
				$('.farm_bdy').each(function() {
					if($(this).is(':checked'))
						kode_farm.push($(this).val());
				});
				
				var tgl_efektif = $( "#inp_tanggalefektif" ).val();
				var tgl_efektif_arr = tgl_efektif.split(" "); 
				var index = (months.indexOf(tgl_efektif_arr[1]) >= 0) ? months.indexOf(tgl_efektif_arr[1]) : months_id.indexOf(tgl_efektif_arr[1]);
				
				tahun = tgl_efektif_arr[2];
				bulan = (parseInt(index) + 1);
				hari = tgl_efektif_arr[0];
				
				tgl_efektif = tahun + "-" + bulan + "-" + hari;
				
				var umur_awal = new Array();
				var umur_akhir = new Array();
				var jenis_pakan = new Array();
				
				$('input[name^="umur_awal"]').each(function() {
					umur_awal.push($(this).val());
				});
				
				$('input[name^="umur_akhir"]').each(function() {	
					umur_akhir.push($(this).val());
				});
				
				$('select[name^="jenis_pakan"]').each(function() {
					jenis_pakan.push($(this).val());
				});
				
				//update
				bootbox.dialog({
					message: "Apakah Anda Yakin Akan Menyimpan data standar budidaya ini?",
					title: "",
					buttons: {
						main: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								$.ajax({
									type:'POST',
									dataType: 'json',
									url : "master/std_budidaya_bdy/simpan_std_budidaya/",
									data: {
										kode_riwayat : selected_riwayat,
										kode_strain : kode_strain,
										kode_farm : kode_farm,
										budget_daya_hidup 	: budget_daya_hidup,
										budget_berat_hidup 	: budget_berat_hidup,
										budget_fcr 			: budget_fcr,
										budget_umur_panen 	: budget_umur_panen,
										budget_ip 			: budget_ip,
										budget_kum 			: budget_kum,
										umur_awal 		: umur_awal,
										umur_akhir 		: umur_akhir,
										jenis_pakan 	: jenis_pakan,
										tgl_efektif 	: tgl_efektif,
										col_umur 		: umur_arr,
										dh_kum 			: dh_kum,
										dh_hr 			: dh_hr,
										sp_kum 			: sp_kum,
										sp_hr 			: sp_hr,
										bp_kum 			: bp_kum,
										bp_hr 			: bp_hr,
										bb 				: bb,
										fcr 			: fcr
									}
								})
								.done(function(data){
									if(data.result == "success"){
										$('#btnSaveStd').addClass("disabled");
										
										toastr.success("Proses Simpan " + data.kode_std_budidaya + " Berhasil",'Informasi');
									}else
										toastr.warning("Proses Simpan Gagal",'Informasi');
								})
								.fail(function(reason){
									console.info(reason);
								})
								.then(function(data){
								});
							}
						},
						cancel: {
							label: "Tidak",
							className: "btn-default",
							callback: function() {
								
							}
						}
					}
				});
			}
		}else{
			return false;
		}
	}
}

function cekDayaHidup(dayahidup_arr){
	// var dayahidup_arr = new Array();
	
	// $('input[name^="col_dhkum"]').each(function() {
		// dayahidup_arr.push($(this).val());
	// });
	
	for(var i=0;i<dayahidup_arr.length;i++){
		var current_val = dayahidup_arr[i];
		
		var elm = $('input[name^="col_dhkum"]').eq(i);
		
		if(parseFloat(current_val) == ""){
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

function cekStandarPakan(col_spkum, col_sphr){
	// var col_spkum = new Array();
	// var col_sphr = new Array();
	
	// $('input[name^="col_spkum"]').each(function() {
		// col_spkum.push($(this).val());
	// });
	
	// $('input[name^="col_sphr"]').each(function() {
		// col_sphr.push($(this).val());
	// });
	
	for(var i=0;i<col_spkum.length;i++){
		var current_val = col_spkum[i];
				
		var elm = $('input[name^="col_spkum"]').eq(i);
				
		if(parseInt(current_val) <= 0){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	for(var i=0;i<col_sphr.length;i++){
		var current_val = col_sphr[i];
				
		var elm = $('input[name^="col_sphr"]').eq(i);
				
		if(parseInt(current_val) <= 0){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

function cekBudgetPakan(col_bpkum, col_bphr){
	// var col_bpkum = new Array();
	// var col_bphr = new Array();
	
	// $('input[name^="col_bpkum"]').each(function() {
		// col_bpkum.push($(this).val());
	// });
	
	// $('input[name^="col_bphr"]').each(function() {
		// col_bphr.push($(this).val());
	// });
	
	var batas_umur = $('#inp_bp_umur_panen').val();
	
	for(var i=0;i<batas_umur;i++){
		var current_val = col_bpkum[i];
				
		var elm = $('input[name^="col_bpkum"]').eq(i);
				
		if(parseInt(current_val) <= 0){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	for(var i=0;i<batas_umur;i++){
		var current_val = col_bphr[i];
				
		var elm = $('input[name^="col_bphr"]').eq(i);
				
		if(parseInt(current_val) <= 0){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

function cekBBFCR(col_bb, col_fcr){
	// var col_bb = new Array();
	// var col_fcr = new Array();
	
	// $('input[name^="col_bb"]').each(function() {
		// col_bb.push($(this).val());
	// });
	
	// $('input[name^="col_fcr"]').each(function() {
		// col_fcr.push($(this).val());
	// });
	
	for(var i=0;i<col_bb.length;i++){
		var current_val = col_bb[i];
				
		var elm = $('input[name^="col_bb"]').eq(i);
				
		if(parseInt(current_val) <= 0){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	for(var i=0;i<col_fcr.length;i++){
		var current_val = col_fcr[i];
				
		var elm = $('input[name^="col_fcr"]').eq(i);
				
		if(parseFloat(current_val) <= 0){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

(function ($) {
	$.fn.formNavigation = function () {
		$(this).each(function () {
			$(this).find('input').on('keyup', function(e) {
				switch (e.which) {
					case 39:
						var index = $(this).attr('data-index');
						var cell = index.split('-');
						var x_curr = cell[1],
						    y_curr = cell[0];
						
						var x_next = (parseInt(x_curr)+1),
							y_next = y_curr;
						var cell_next = 'cell_'+y_next+'-'+x_next;
						
						$('#'+cell_next).focus();
						
						break;
					case 37:
						var index = $(this).attr('data-index');
						var cell = index.split('-');
						var x_curr = cell[1],
						    y_curr = cell[0];
						
						var x_prev = (parseInt(x_curr)-1),
							y_prev = y_curr;
						var cell_prev = 'cell_'+y_prev+'-'+x_prev;
						
						$('#'+cell_prev).focus();
						
						break;
					case 40:
						var index = $(this).attr('data-index');
						var cell = index.split('-');
						var x_curr = cell[1],
						    y_curr = cell[0];
						
						var x_next = x_curr,
							y_next = (parseInt(y_curr)+1);
						var cell_next = 'cell_'+y_next+'-'+x_next;
						
						$('#'+cell_next).focus();
						
						break;
					case 38:
						var index = $(this).attr('data-index');
						var cell = index.split('-');
						var x_curr = cell[1],
						    y_curr = cell[0];
						
						var x_prev = x_curr,
							y_prev = (parseInt(y_curr)-1);
						var cell_prev = 'cell_'+y_prev+'-'+x_prev;
						
						$('#'+cell_prev).focus();
						
						break;
				}
			});
		});
	};
})(jQuery);