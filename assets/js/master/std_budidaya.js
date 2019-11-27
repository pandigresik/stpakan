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
		selected_musim = ($(this).find('td:nth-child(2)').text() == "In Season") ? "I" : "O";
		selected_riwayat_date = $(this).find('td:nth-child(3)').text();
		
		$('#slc-riwayat').html(selected_riwayat);	
		$(this).addClass('highlight').siblings().removeClass('highlight');
				
		if(selected_riwayat != ""){
			$('#btnDetail').removeClass("disabled");
			$('#btnNew').removeClass("disabled");
		}
	}
});

$('#btnSet').click(function(){
	var filter = false;
		strain = '', 
		jenis_kelamin = '', 
		tipe_kandang = '', 
		in_season = '', 
		out_season = '';
	
	strain = $('#inp_strain').val();
	jenis_kelamin = $('input:radio[name=jeniskelamin]:checked').val();
	tipe_kandang = $('input:radio[name=tipekandang]:checked').val();
	
	selected_jk = jenis_kelamin;
	
	if($("#inp_musim_in").is(':checked'))
		in_season = 'I';
	
	if($("#inp_musim_out").is(':checked'))
		out_season = 'O';
		
	if(empty(strain) || empty(jenis_kelamin) || empty(tipe_kandang) || (empty(in_season) && empty(out_season)))
		filter = false;
	else
		filter = true;
	
	if(filter == true){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/std_budidaya/get_last_std/",
			data: {
				strain : strain,
				jenis_kelamin : jenis_kelamin,
				tipe_kandang : tipe_kandang,
				m_in : in_season,
				m_out : out_season
			}
		})
		.done(function(data){
			$("tbody", "#riwayat-standar-budidaya").html("");
			
			window.mydata = data;
			if(!empty(mydata.length)){
				if(mydata.length > 0){
					var record = mydata[0].Rows;
					$.each(record, function (key, data) {
						
						v_musim = (data.musim == "I") ? "In Season" : "Out Season";
						
						$("tbody", "#riwayat-standar-budidaya").append(
						'<tr>'+
						'<td class="vert-align">'+data.kode_std_breeding+'</td>'+
						'<td class="vert-align">'+v_musim+'</td>'+
						'<td class="vert-align">'+data.tgl_efektif_max_formated+'</td>'+
						'<td class="vert-align"> - </td></tr>');
					});
					
					 $('#inp_strain').prop("disabled", true);
					 $('#inp_jeniskelaminjantan').prop("disabled", true);
					 $('#inp_jeniskelaminbetina').prop("disabled", true);
					 $('#inp_tipekandang_open').prop("disabled", true);
					 $('#inp_tipekandang_close').prop("disabled", true);
					 $('#inp_musim_in').prop("disabled", true);
					 $('#inp_musim_out').prop("disabled", true);
					 
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

function print_std(elm){	
	var tgl_efektif = selected_riwayat_date.split(" "); 
	var index = months.indexOf(tgl_efektif[1]);
	tahun = parseInt(tgl_efektif[2]);
	bulan = parseInt(index);
	hari = parseInt(tgl_efektif[0]);
	
	OpenInNewTab("master/std_budidaya/cetak_std?jenis_kelamin="+selected_jk+"&riwayat_date="+tahun+"&kode_riwayat="+selected_riwayat+"&musim="+selected_musim);
}

function OpenInNewTab(url) {
  var win = window.open(url, '_blank');
  win.focus();
}

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
						$( "#inp_tanggalefektif" ).val(day +' '+months_id[monthIndex]+' '+year);
						
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "master/std_budidaya/get_detail_std/",
							data: {
								kode_riwayat : selected_riwayat,
								musim : selected_musim
							}
						})
						.done(function(data){
							$("tbody", "#detail-standar-budidaya").html("");
							$("tbody", "#detail-mingguan-standar-budidaya").html("");
							
							window.mydata = data;
							if(!empty(mydata.length)){
								if(mydata.length > 0){
									var record = mydata[0].Rows;
									var recordDetail = mydata[0].RowsDetail;
									var grupBarang = mydata[0].GrupBarang;
									
									$.each(record, function (key, data) {
										range_umur_awal = empty(range_umur_awal) ? data.umur_awal : range_umur_awal;
										
										selected_jk = data.jenis_kelamin;
										selected_tk = data.tipe_kandang;						
										selected_musim = data.musim;						
										
										var select = '<select class="form-control multicolumn" name="jenis_pakan[]" onchange="pilihProdukPakan(this)">';
										select += '<option value="">Pilihan : </option>' + 
												  '<option class="header">Kode Pakan +Nama Produk</option>';
										$.each(grupBarang, function (key, row) {
											selected = (row.grup_barang == data.grup_barang) ? 'selected' : '';
											select += '<option value="'+row.grup_barang+'"'+selected+'>'+row.kode_barang + '+' + row.nama_barang + '</option>';
										});
										select += '</select>';
										
										
										$("tbody", "#detail-standar-budidaya").append(
										'<tr>'+
										'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_awal[]" value="'+data.umur_awal+'" disabled/></div><center></td>'+
										'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_akhir[]" value="'+data.umur_akhir+'" disabled/></div><center></td>'+
										'<td class="vert-align">'+select+'</td></tr>');
										
										range_umur_akhir = data.umur_akhir;
									});
														
									$('select.multicolumn').combomulticolumn();
									
									$('#btnSetDetail').removeClass("disabled");
									$('#btnSetDetail').show();
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
					}
				}
			}
		});
	}
	else{
		var strain = $('#inp_strain').val();
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/std_budidaya/get_masa_pertumbuhan/",
			data: {
				kode_strain : strain
			}
		})
		.done(function(data){
			if(data.length <= 0){
				bootbox.alert("Tidak ada masa pertumbuhan untuk strain " + strain.toUpperCase());
			}else{
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
									tgl = $(this).find('td:nth-child(3)').text();
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
								kode_strain = $('#inp_strain').val();
								
								$.ajax({
									type:'POST',
									dataType: 'json',
									url : "master/std_budidaya/get_data_kebutuhanpakan/",
									data: {
										kode_strain : kode_strain
									}
								})
								.done(function(data){
									range_umur_awal = data.umur_awal;
									range_umur_akhir = data.umur_akhir;
									
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
									'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" type="text" name="umur_awal[]" value="0" disabled/></div><center></td>'+
									'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" onkeyup="cekNumerik(this)" style="text-align:center" type="text" name="umur_akhir[]" value=""/></div><center></td>'+
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
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
		});
	}
});

$('#btnDetail').click(function(){
	mode_riwayat = "detail";
	$('#btnNew').addClass("disabled");	
	$('#inp_tanggalefektif').addClass("disabled");	
	$('#btnSetDetail').hide();	
	
	if(!empty(selected_riwayat)){
		var jenis_kelamin = $('input:radio[name=jeniskelamin]:checked').val();
		$( "#inp_tanggalefektif" ).val(selected_riwayat_date);
		
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/std_budidaya/get_detail_std/",
			data: {
				kode_riwayat : selected_riwayat,
				musim : selected_musim
			}
		})
		.done(function(data){
			window.mydata = data;
			if(!empty(mydata.length)){
				if(mydata.length > 0){
					var record = mydata[0].Rows;
					var recordDetail = mydata[0].RowsDetail;
					
					var temp = new Array();
					var index;
					
					index = 0;
					$.each(record, function (key, data) {
						temp[index] = 
						'<tr>'+
						'<td class="vert-align">'+data.umur_awal+'</td>'+
						'<td class="vert-align">'+data.umur_akhir+'</td>'+
						'<td class="vert-align">'+data.deskripsi_full+'</td></tr>';
						
						index++;
					});
					
					$("tbody", "#detail-standar-budidaya").html("");
					$("tbody", "#detail-standar-budidaya").append(temp.join(''));
					
					//var untuk hitung standar-target
					masa_pertumbuhan_arr = new Array();
					var col_pakantarget_arr = new Array();
					var index_masa_pertumbuhan = 0;
					var currt_masa_pertumbuhan = "";
					//--------------end----------------------
					
					temp = new Array();
					index = 0;
					
					$.each(recordDetail, function (key, data) {
						if(currt_masa_pertumbuhan==""){
							masa_pertumbuhan_arr[index_masa_pertumbuhan] = new Array();
							masa_pertumbuhan_arr[index_masa_pertumbuhan][0] = data.deskripsi_masa_pertumbuhan;
							masa_pertumbuhan_arr[index_masa_pertumbuhan][1] = data.std_umur;
							
							currt_masa_pertumbuhan = data.deskripsi_masa_pertumbuhan;
						}else{
							if(currt_masa_pertumbuhan != data.deskripsi_masa_pertumbuhan){
								masa_pertumbuhan_arr[index_masa_pertumbuhan][2] = data.std_umur - 1;						
							
								currt_masa_pertumbuhan = data.deskripsi_masa_pertumbuhan;
								index_masa_pertumbuhan++;
								
								masa_pertumbuhan_arr[index_masa_pertumbuhan] = new Array();
								masa_pertumbuhan_arr[index_masa_pertumbuhan][0] = data.deskripsi_masa_pertumbuhan;
								masa_pertumbuhan_arr[index_masa_pertumbuhan][1] = data.std_umur;
								
								
							}else{								
								if(index == ((recordDetail.length)-1))
									masa_pertumbuhan_arr[index_masa_pertumbuhan][2] = data.std_umur;
							}
						}
						
						pengurangan  = (empty(data.pengurangan) || data.pengurangan == 0) ? '-' : data.pengurangan;
						mati_prc  = (empty(data.mati_prc) || data.mati_prc == 0) ? '-' : data.mati_prc;
						afkir_prc  = (empty(data.afkir_prc) || data.afkir_prc == 0) ? '-' : data.afkir_prc;
						seleksi_prc  = (empty(data.seleksi_prc) || data.seleksi_prc == 0) ? '-' : data.seleksi_prc;
						
						temp[index] = 
						'<tr>'+
						'<td class="vert-align-sm">'+data.deskripsi_masa_pertumbuhan+'</td>'+
						'<td class="vert-align-sm">'+data.std_umur+'</td>'+
						'<td class="right-align-sm">'+ pengurangan +'</td>'+
						'<td class="vert-align-sm col-mati">'+mati_prc+'</td>'+
						'<td class="vert-align-sm col-afkir">'+afkir_prc+'</td>'+
						'<td class="vert-align-sm col-seleksi">'+seleksi_prc+'</td>'+
						'<td class="vert-align-sm col-dayahidup">'+data.dh_prc+'</td>'+
						'<td class="vert-align-sm col-targetpakan">'+data.target_pkn+'</td>'+
						'<td class="vert-align-sm col-energi">'+data.energi+'</td>'+
						'<td class="vert-align-sm col-totalenergi">'+data.total_energi+'</td>'+
						'<td class="vert-align-sm col-protein">'+data.protein+'</td>'+
						'<td class="vert-align-sm col-totalprotein">'+data.total_protein+'</td>'+
						'<td class="vert-align-sm col-targetbb">'+data.target_bb+'</td>'+
						'<td class="vert-align-sm col-bb">'+data.bb_prc+'</td>'+
						'<td class="vert-align-sm">'+data.deskripsi_full+'</td>'+
						'<td class="vert-align-sm">'+data.keterangan+'</td>'+
						'</tr>';
						
						if((data.deskripsi_masa_pertumbuhan).toLowerCase() != "layer"){
							col_pakantarget_arr.push(!empty(data.target_pkn) ?  (isNaN(parseFloat(data.target_pkn)) ? 0 : data.target_pkn ) : 0);
						}
						
						
						
						index++;
					});
					
					$("tbody", "#detail-mingguan-standar-budidaya").html("");
					$("tbody", "#detail-mingguan-standar-budidaya").append(temp.join(''));
					// if(jenis_kelamin == 'B'){
						// $('#detail-mingguan-standar-budidaya td:nth-child(3),#detail-mingguan-standar-budidaya th:nth-child(3)').hide();
					// }
					mergeRow(1,15);
										
					var summary = new Array();
					var summary_avg = new Array();
					for(var i=0; i<masa_pertumbuhan_arr.length; i++){
						
						var summary_sub = 0;
						var min = parseInt(masa_pertumbuhan_arr[i][1]);
						var max = parseInt(masa_pertumbuhan_arr[i][2]);
						
						for(var j=min; j<=max; j++){
							summary_sub += parseFloat(col_pakantarget_arr[j]);
						}
						
						summary[i] = (parseFloat(summary_sub) * 7 / 1000).toFixed(2);
					}
					
					var summary_grand = 0;
					var summary_grand_avg = 0;
					for(var i=0;i<summary.length-1;i++){
						summary_grand += parseFloat(summary[i]);
					}
					
					summary_grand = summary_grand.toFixed(2);
					
					for(var i=0;i<summary.length-1;i++){
						console.log(i+'.'+summary[i]);
						summary_avg[i] = (summary[i] / summary_grand * 100).toFixed(2);
					}
					
					for(var i=0;i<summary_avg.length-1;i++){
						summary_grand_avg += parseFloat(summary_avg[i]);
					}
					
					console.log(summary_grand);
					
					temp = new Array();
					for(var i=0;i<masa_pertumbuhan_arr.length;i++){
						if((masa_pertumbuhan_arr[i][0]).toLowerCase() != "layer"){
							temp[i] = 
							'<div class="form-group">'+
							'	<label class="col-md-3 control-label">' + masa_pertumbuhan_arr[i][0] + '</label>'+
							'	<div class="col-md-2 input-group-sm">'+
							'		<input type="text" class="form-control input-sm-5 field_input inp-right" disabled name="target_berat[]" value="'+summary[i]+'">'+
							'	</div>'+
							'	<label class="col-md-2 control-label">Kg/Ekr</label>'+
							'	<div class="col-md-2 input-group-sm">'+
							'		<input type="text" class="form-control input-sm-2 field_input inp-right" disabled name="target_percent[]" value="'+summary_avg[i]+'">'+
							'	</div>'+
							'	<label class="control-label">%</label>'+
							'</div>';
						}
					}
					
					temp[masa_pertumbuhan_arr.length] = 
					'<div class="form-group">'+
					'	<label class="col-md-3 control-label">TOTAL</label>'+
					'	<div class="col-md-2 input-group-sm">'+
					'		<input type="text" class="form-control input-sm-5 field_input inp-right" disabled name="total_target_berat" value="'+summary_grand+'">'+
					'	</div>'+
					'	<label class="col-md-2 control-label">Kg/Ekr</label>'+
					'	<div class="col-md-2 input-group-sm">'+
					'		<input type="text" class="form-control input-sm-2 field_input inp-right" disabled name="total_target_percent" value="'+summary_grand_avg+'">'+
					'	</div>'+
					'	<label class="control-label">%</label>'+
					'</div>';
					
					$('#standar-target').append('<br/>'+temp.join(''));
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

$('#btnSetDetail').click(function(){
	tgl_efektif = $( "#inp_tanggalefektif" ).val();
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
	
	if(umur_akhir[umur_akhir.length - 1] != range_umur_akhir){
		bootbox.alert("Umur akhir tidak sesuai dengan masa pertumbuhan");
		return false;
	}
	
	
	
	if(!empty(selected_riwayat)){
		// bootbox.dialog({
			// message: "Apakah Anda akan membuat standar budidaya baru berdasarkan nomor "+selected_riwayat+"?",
			// title: "",
			// buttons: {
				// main: {
					// label: "Ya",
					// className: "btn-primary",
					// callback: function() {
						$('#inp_tanggalefektif').prop("disabled", true);
				
						mode_riwayat = "update";
						
						var umur_awal = new Array();
						var umur_akhir = new Array();
						var jenis_pakan = new Array();
						
						kode_strain = $('#inp_strain').val();
						jenis_kelamin = "";
						tipe_kandang = "";
						musim_in = "";
						musim_out = "";
						
						jenis_kelamin = $('input:radio[name=jeniskelamin]:checked').val();
						tipe_kandang = $('input:radio[name=tipekandang]:checked').val();
					
						if($("#inp_musim_in").is(':checked'))
							musim_in = 'I';
						if($("#inp_musim_out").is(':checked'))
							musim_out = 'O';

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
						
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "master/std_budidaya/add_std_budidaya/",
							data: {
								kode_strain : kode_strain,
								jenis_kelamin : selected_jk,
								tipe_kandang : selected_tk,
								musim : selected_musim,
								umur_awal : umur_awal,
								umur_akhir : umur_akhir,
								jenis_pakan : jenis_pakan,
								kode_riwayat : selected_riwayat,
								tgl_efektif : tgl_efektif
							}
						})
						.done(function(data){
							if(data.result == "success"){
								var kode_std_breeding = data.kode_std_breeding;
								//toastr.success("Penyimpanan data Standar Budidaya dengan kode " + data.kode_std_breeding + " berhasil dilakukan",'Informasi');
								
								$.ajax({
									type:'POST',
									dataType: 'json',
									url : "master/std_budidaya/get_detail_std/",
									data: {
										kode_riwayat : data.kode_std_breeding,
										musim : selected_musim
									}
								})
								.done(function(data){
									selected_riwayat = kode_std_breeding;
									
									$('#btnSetDetail').addClass("disabled");
									$('[name="jenis_pakan[]"]').prop("disabled", true);
									$("tbody", "#detail-mingguan-standar-budidaya").html("");
									
									window.mydata = data;
									if(!empty(mydata.length)){
										if(mydata.length > 0){
											var recordDetail = mydata[0].RowsDetail;
											
											//var untuk hitung standar-target
											masa_pertumbuhan_arr = new Array();
											var col_pakantarget_arr = new Array();
											var index_masa_pertumbuhan = 0;
											var currt_masa_pertumbuhan = "";
											
											var temp = new Array();
											var index = 0;
											
											$.each(recordDetail, function (key, data) {
												if(currt_masa_pertumbuhan==""){
													masa_pertumbuhan_arr[index_masa_pertumbuhan] = new Array();
													masa_pertumbuhan_arr[index_masa_pertumbuhan][0] = data.deskripsi_masa_pertumbuhan;
													masa_pertumbuhan_arr[index_masa_pertumbuhan][1] = data.std_umur;
													
													currt_masa_pertumbuhan = data.deskripsi_masa_pertumbuhan;
												}else{
													if(currt_masa_pertumbuhan != data.deskripsi_masa_pertumbuhan){
														masa_pertumbuhan_arr[index_masa_pertumbuhan][2] = data.std_umur - 1;						
													
														currt_masa_pertumbuhan = data.deskripsi_masa_pertumbuhan;
														index_masa_pertumbuhan++;
														
														masa_pertumbuhan_arr[index_masa_pertumbuhan] = new Array();
														masa_pertumbuhan_arr[index_masa_pertumbuhan][0] = data.deskripsi_masa_pertumbuhan;
														masa_pertumbuhan_arr[index_masa_pertumbuhan][1] = data.std_umur;
														
														
													}else{								
														if(index == ((recordDetail.length)-1))
															masa_pertumbuhan_arr[index_masa_pertumbuhan][2] = data.std_umur;
													}
												}
												
												pengurangan  = (empty(data.pengurangan) || data.pengurangan == 0) ? '-' : data.pengurangan;
												mati  = (empty(data.mati_prc) || data.mati_prc == 0) ? '-' : data.mati_prc;
												afkir  = (empty(data.afkir_prc) || data.afkir_prc == 0) ? '-' : data.afkir_prc;
												seleksi  = (empty(data.seleksi_prc) || data.seleksi_prc == 0) ? '-' : data.seleksi_prc;
												
												temp[index] = '<tr>'+
												'<td class="vert-align-sm">'+data.deskripsi_masa_pertumbuhan+'</td>'+
												'<td class="vert-align-sm">'+data.std_umur+'<input type="hidden" name="col_umurminggu[]" value="'+data.std_umur+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_pengurangan[]" onkeyup="cekNumerik(this)" value="'+pengurangan+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_mati[]" onkeyup="cekNumerik(this)" value="'+mati+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_afkir[]" onkeyup="cekNumerik(this)" value="'+afkir+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_seleksi[]" onkeyup="cekNumerik(this)" value="'+seleksi+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_dayahidup[]" onkeyup="cekNumerik(this)" value="'+data.dh_prc+'" title="Harus lebih kecil dari nilai sebelumnya"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_pakantarget[]" onkeyup="cekNumerik(this)" value="'+data.target_pkn+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_pakanenergi[]" onkeyup="cekNumerik(this)" value="'+data.energi+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_pakancumenergi[]" onkeyup="cekNumerik(this)" value="'+data.total_energi+'" title="Harus lebih besar dari nilai sebelumnya"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_pakanprotein[]" onkeyup="cekNumerik(this)" value="'+data.protein+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_pakancumprotein[]" onkeyup="cekNumerik(this)" value="'+data.total_protein+'" title="Harus lebih besar dari nilai sebelumnya"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_bbtarget[]" onkeyup="cekNumerik(this)" value="'+data.target_bb+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+'<input type="text" class="form-control input-sm inp-right no-border" size="3" name="col_bbtotal[]" onkeyup="cekNumerik(this)" value="'+data.bb_prc+'"/>'+'</td>'+
												'<td class="vert-align-sm">'+data.deskripsi_full+'</td>'+
												'<td class="vert-align-sm">'+data.keterangan+'</td>'+
												'</tr>';
											
												if((data.deskripsi_masa_pertumbuhan).toLowerCase() != "layer"){
													col_pakantarget_arr.push(!empty(data.target_pkn) ?  (isNaN(parseFloat(data.target_pkn)) ? 0 : data.target_pkn ) : 0);
												}
											
												index++;
											});
											
											$("tbody", "#detail-mingguan-standar-budidaya").html("");
											$("tbody", "#detail-mingguan-standar-budidaya").append(temp.join(''));
											$("tbody", "#detail-mingguan-standar-budidaya").formNavigation();
											mergeRow(1,15);
											
											var summary = new Array();
											var summary_avg = new Array();
											for(var i=0; i<masa_pertumbuhan_arr.length-1; i++){
												
												var summary_sub = 0;
												var min = parseInt(masa_pertumbuhan_arr[i][1]);
												var max = parseInt(masa_pertumbuhan_arr[i][2]);
												
												for(var j=min; j<=max; j++){
													summary_sub += parseFloat(col_pakantarget_arr[j]);
												}
												
												summary[i] = (parseFloat(summary_sub) * 7 / 1000).toFixed(2);
											}
											
											console.log("jumlah summary awal : " + summary);
											
											var summary_grand = 0;
											var summary_grand_avg = 0;
											for(var i=0;i<summary.length;i++){
												summary_grand += parseFloat(summary[i]);
											}
											
											summary_grand = summary_grand.toFixed(2);
											
											for(var i=0;i<summary.length;i++){
												console.log(i+'. summary: '+summary[i]);
												summary_avg[i] = (summary[i] / summary_grand * 100).toFixed(2);
											}
											
											for(var i=0;i<summary_avg.length-1;i++){
												summary_grand_avg += parseFloat(summary_avg[i]);
											}
											
											console.log(summary_grand);
											
											temp = new Array();
											for(var i=0;i<masa_pertumbuhan_arr.length;i++){
												console.log(masa_pertumbuhan_arr[i]);
												if((masa_pertumbuhan_arr[i][0]).toLowerCase() != "layer"){
													temp[i] = 
													'<div class="form-group">'+
													'	<label class="col-md-3 control-label">' + masa_pertumbuhan_arr[i][0] + '</label>'+
													'	<div class="col-md-2 input-group-sm">'+
													'		<input type="text" class="form-control input-sm-5 field_input inp-right" disabled name="target_berat[]" value="'+summary[i]+'">'+
													'	</div>'+
													'	<label class="col-md-2 control-label">Kg/Ekr</label>'+
													'	<div class="col-md-2 input-group-sm">'+
													'		<input type="text" class="form-control input-sm-2 field_input inp-right" disabled name="target_percent[]" value="'+summary_avg[i]+'">'+
													'	</div>'+
													'	<label class="control-label">%</label>'+
													'</div>';
												}
											}
											
											temp[masa_pertumbuhan_arr.length] = 
											'<div class="form-group">'+
											'	<label class="col-md-3 control-label">TOTAL</label>'+
											'	<div class="col-md-2 input-group-sm">'+
											'		<input type="text" class="form-control input-sm-5 field_input inp-right" disabled name="total_target_berat" value="'+summary_grand+'">'+
											'	</div>'+
											'	<label class="col-md-2 control-label">Kg/Ekr</label>'+
											'	<div class="col-md-2 input-group-sm">'+
											'		<input type="text" class="form-control input-sm-2 field_input inp-right" disabled name="total_target_percent" value="'+summary_grand_avg+'">'+
											'	</div>'+
											'	<label class="control-label">%</label>'+
											'</div>';
											
											$('#standar-target').append('<br/>'+temp.join(''));
										}
									}
								})
								.fail(function(reason){
									console.info(reason);
								})
								.then(function(data){
									$('#section_detail_std_budidaya').show();
									$('#btnSaveStd').show();
									
									//calculateStandarTarget();
								});						
							}
							else{
								toastr.warning("Proses simpan gagal!",'Informasi');
							}
						})
						.fail(function(reason){
							console.info(reason);
						})
						.then(function(data){
							$('#btnSaveStd').show();
						});
					// }
				// },
				// cancel: {
					// label: "Tidak",
					// className: "btn-default",
					// callback: function() {
					// }
				// }
			// }
		// });
	}
	else{
		mode_riwayat = "baru";
		
		var kode_strain = "";
		var jenis_kelamin = "";
		var tipe_kandang = "";
		var musim_in = "";
		var musim_out = "";
		
		var umur_awal = new Array();
		var umur_akhir = new Array();
		var jenis_pakan = new Array();
		var jenis_pakan_desk = new Array();
		
		kode_strain = $('#inp_strain').val();
		jenis_kelamin = $('input:radio[name=jeniskelamin]:checked').val();
		tipe_kandang = $('input:radio[name=tipekandang]:checked').val();
	
		if($("#inp_musim_in").is(':checked'))
			musim_in = 'I';
		if($("#inp_musim_out").is(':checked'))
			musim_out = 'O';

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
			url : "master/std_budidaya/get_masa_pertumbuhan/",
			data: {
				kode_strain : kode_strain
			}
		})
		.done(function(data){
			var temp = new Array();
			var mp_arr = new Array();
			
			var tempDes = null;
			var mp_temp = new Array();
			var mp_temp_index = 0;
			for(var i=0;i<data.length;i++){
				if(i==0){
					mp_temp[i] = data[i].deskripsi;
					
					masa_pertumbuhan_arr[mp_temp_index] = new Array();
					masa_pertumbuhan_arr[mp_temp_index][0] = data[i].deskripsi;
					masa_pertumbuhan_arr[mp_temp_index][1] = data[i].umur_awal;
					masa_pertumbuhan_arr[mp_temp_index][2] = data[i].umur_akhir;
					
					mp_temp_index++;
				}else{
					if(mp_temp.indexOf(data[i].deskripsi) < 0){
						mp_temp[i] = data[i].deskripsi;
					
						masa_pertumbuhan_arr[mp_temp_index] = new Array();
						masa_pertumbuhan_arr[mp_temp_index][0] = data[i].deskripsi;
						masa_pertumbuhan_arr[mp_temp_index][1] = data[i].umur_awal;
						masa_pertumbuhan_arr[mp_temp_index][2] = data[i].umur_akhir;
						
						mp_temp_index++;
					}
				}
				
				if(tempDes==null || data[i].deskripsi != tempDes){
					tempDes = data[i].deskripsi;
					mp_arr.push(tempDes);
				}
			}
			
			for(var i=0;i<umur_awal.length;i++){
				for(var j=parseInt(umur_awal[i]);j<=parseInt(umur_akhir[i]);j++){
					var obj = data[j]; 
					
					temp[j] = '<tr>'+
					'<td class="vert-align-sm">'+obj.deskripsi+'</td>'+
					'<td class="vert-align-sm">'+j+'<input type="hidden" name="col_umurminggu[]" value="'+j+'"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-1" id="cell_'+j+'-1" class="form-control input-sm inp-right no-border" size="3" name="col_pengurangan[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-2" id="cell_'+j+'-2" class="form-control input-sm inp-right no-border" size="3" name="col_mati[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-3" id="cell_'+j+'-3" class="form-control input-sm inp-right no-border" size="3" name="col_afkir[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-4" id="cell_'+j+'-4" class="form-control input-sm inp-right no-border" size="3" name="col_seleksi[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-5" id="cell_'+j+'-5" class="form-control input-sm inp-right no-border" size="3" name="col_dayahidup[]" onkeyup="cekNumerik(this)" title="Harus lebih kecil dari nilai sebelumnya"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-6" id="cell_'+j+'-6" class="form-control input-sm inp-right no-border" size="3" name="col_pakantarget[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-7" id="cell_'+j+'-7" class="form-control input-sm inp-right no-border" size="3" name="col_pakanenergi[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-8" id="cell_'+j+'-8" class="form-control input-sm inp-right no-border" size="3" name="col_pakancumenergi[]" onkeyup="cekNumerik(this)" title="Harus lebih besar dari nilai sebelumnya"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-9" id="cell_'+j+'-9" class="form-control input-sm inp-right no-border" size="3" name="col_pakanprotein[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-10" id="cell_'+j+'-10" class="form-control input-sm inp-right no-border" size="3" name="col_pakancumprotein[]" onkeyup="cekNumerik(this)" title="Harus lebih besar dari nilai sebelumnya"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-11" id="cell_'+j+'-11" class="form-control input-sm inp-right no-border" size="3" name="col_bbtarget[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-12" id="cell_'+j+'-12" class="form-control input-sm inp-right no-border" size="3" name="col_bbtotal[]" onkeyup="cekNumerik(this)"/>'+'</td>'+
					'<td class="vert-align-sm">'+jenis_pakan_desk[i]+'</td>'+
					'<td class="vert-align-sm">'+'<input type="text" data-index="'+j+'-13" id="cell_'+j+'-13" class="form-control input-sm no-border" size="30" name="col_keterangan[]" />'+'</td>'+
					'</tr>';
				}
			}
			
			$("tbody", "#detail-mingguan-standar-budidaya").html("");
			$("tbody", "#detail-mingguan-standar-budidaya").append(temp.join(''));
			$("tbody", "#detail-mingguan-standar-budidaya").formNavigation();
			// isiDataDummy();
			mergeRow(1,15);
			
			temp = new Array();
			for(var i=0;i<mp_arr.length;i++){
				if(mp_arr[i] != "Layer"){
					temp[i] = 
					'<div class="form-group">'+
					'	<label class="col-md-3 control-label">' + mp_arr[i] + '</label>'+
					'	<div class="col-md-2 input-group-sm">'+
					'		<input type="text" class="form-control input-sm-5 field_input inp-right" name="target_berat[]">'+
					'	</div>'+
					'	<label class="col-md-2 control-label">Kg/Ekr</label>'+
					'	<div class="col-md-2 input-group-sm">'+
					'		<input type="text" class="form-control input-sm-2 field_input inp-right" name="target_percent[]">'+
					'	</div>'+
					'	<label class="control-label">%</label>'+
					'</div>';
				}
			}
			
			temp[mp_arr.length] = 
			'<div class="form-group">'+
			'	<label class="col-md-3 control-label">TOTAL</label>'+
			'	<div class="col-md-2 input-group-sm">'+
			'		<input type="text" class="form-control input-sm-5 field_input inp-right" name="total_target_berat">'+
			'	</div>'+
			'	<label class="col-md-2 control-label">Kg/Ekr</label>'+
			'	<div class="col-md-2 input-group-sm">'+
			'		<input type="text" class="form-control input-sm-2 field_input inp-right" name="total_target_percent">'+
			'	</div>'+
			'	<label class="control-label">%</label>'+
			'</div>';
			
			$('#standar-target').append(temp.join(''));
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
			$('#section_detail_std_budidaya').show();
			$('#btnSaveStd').show();
		});
	}
});

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
		var tr = $(elm).parent().parent();
		var umur_akhir = $(tr).find('td').eq(1).find('input');
			
		if(!empty($(elm).val()) && !empty($(umur_akhir).val()) && $(umur_akhir).val() == range_umur_akhir){
			$('#btnSetDetail').removeClass("disabled");
		}else{
			$('#btnSetDetail').addClass("disabled");
		}
	}
}

$('#linkTambah').on('click', function() {
	var valid = true;
	var currVal = 0;
	var currIndex = 0;
	var umur_awal = new Array();
	var umur_awal = new Array();
	var jenis_pakan = new Array();
	
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
	'<td class="vert-align"><center><div class="col-lg-4 col-lg-offset-4 col-xs-4"><input class="form-control" style="text-align:center" onkeyup="cekNumerik(this)" type="text" name="umur_akhir[]" value=""/></div><center></td>'+
	'<td class="vert-align">'+html_grup_barang+'</td></tr>';
	
	if(valid){	
		$('input[name^="umur_akhir"]').prop("disabled", true);
		$(element).appendTo("#detail-standar-budidaya > tbody");
	}
});

function cekNumerik(field){	
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	} 
	
	calculateStandarTarget();
}

function mergeRow(masaPertumbuhanCol, jenisPakanCol){
	$('#detail-mingguan-standar-budidaya').each(function () {

		var MP_Previous_TD = null;
		var JP_Previous_TD = null;
		var i = 1;
		var j = 1;
		$("tbody",this).find('tr').each(function () {
			var MP_Current_td = $(this).find('td:nth-child(' + masaPertumbuhanCol + ')');
			var JP_Current_td = $(this).find('td:nth-child(' + jenisPakanCol + ')');
			 
			if (MP_Previous_TD == null) {
				MP_Previous_TD = MP_Current_td;
				i = 1;
			} 
			else if (MP_Current_td.text() == MP_Previous_TD.text()) {
				MP_Current_td.remove();
				MP_Previous_TD.attr('rowspan', i + 1);
				i = i + 1;
			} 
			else {
				MP_Previous_TD = MP_Current_td;
				i = 1;
			}
		
			
			if (JP_Previous_TD == null) {
				JP_Previous_TD = JP_Current_td;
				j = 1;
			} 
			else if (JP_Current_td.text() == JP_Previous_TD.text()) {				
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
	
	var umur_arr = new Array();
	var pengurangan_arr = new Array();
	var mati_arr = new Array();
	var afkir_arr = new Array();
	var seleksi_arr = new Array();
	var dayahidup_arr = new Array();
	var pakantarget_arr = new Array();
	var pakanenergi_arr = new Array();
	var pakancumenergi_arr = new Array();
	var pakanprotein_arr = new Array();
	var pakancumprotein_arr = new Array();
	var bbtarget_arr = new Array();
	var bbtotal_arr = new Array();
	var keterangan_arr = new Array();
	
	valid = cekDayaHidup();
	valid = (valid) ? cekCumEnergi() : valid;
	valid = (valid) ? cekCumProtein() : valid;
	
	if(valid){
		var i=0;
		
		$('input[name^="col_umurminggu"]').each(function() {
			umur_arr.push($(this).val());
			i++;
		});
		
		i=0;
		$('input[name^="col_pengurangan"]').each(function() {
			var col_val = (!empty($(this).val()) && $(this).val() != "-") ? parseFloat($(this).val()) : 0;
			
			pengurangan_arr.push(col_val);
			i++;
		});
				
		i=0;
		$('input[name^="col_mati"]').each(function() {
			var col_val = (!empty($(this).val()) && $(this).val() != "-") ? parseFloat($(this).val()) : 0;
			
			mati_arr.push(col_val);
			i++;
		});
		
		i=0;
		$('input[name^="col_afkir"]').each(function() {
			var col_val = (!empty($(this).val()) && $(this).val() != "-") ? parseFloat($(this).val()) : 0;
			
			afkir_arr.push(col_val);
			i++;
		});
		
		i=0;
		$('input[name^="col_seleksi"]').each(function() {
			var col_val = (!empty($(this).val()) && $(this).val() != "-") ? parseFloat($(this).val()) : 0;
			
			seleksi_arr.push(col_val);
			i++;
		});
		
		i=0;
		$('input[name^="col_dayahidup"]').each(function() {
			if(!empty($(this).val())){
				dayahidup_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_dayahidup"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_pakantarget"]').each(function() {
			if(!empty($(this).val())){
				pakantarget_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_pakantarget"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_pakanenergi"]').each(function() {
			if(!empty($(this).val())){
				pakanenergi_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_pakanenergi"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_pakancumenergi"]').each(function() {
			if(!empty($(this).val())){
				pakancumenergi_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_pakancumenergi"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_pakanprotein"]').each(function() {
			if(!empty($(this).val())){
				pakanprotein_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_pakanprotein"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_pakancumprotein"]').each(function() {
			if(!empty($(this).val())){
				pakancumprotein_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_pakancumprotein"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_bbtarget"]').each(function() {
			if(!empty($(this).val())){
				bbtarget_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_bbtarget"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		i=0;
		$('input[name^="col_bbtotal"]').each(function() {
			if(!empty($(this).val())){
				bbtotal_arr.push(parseFloat($(this).val()));
			}else{
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				
				var elm = $('input[name^="col_bbtotal"]').eq(i);
				elm.focus();
				
				valid = false;
				
				return false;
			}
			
			i++;
		});
		
		$('input[name^="col_keterangan"]').each(function() {
			keterangan_arr.push($(this).val());
		});
	
		if(valid){
			if(mode_riwayat == "baru"){
				//baru
				var kode_strain = "";
				var jenis_kelamin = "";
				var tipe_kandang = "";
				var musim_in = "";
				var musim_out = "";
				
				var umur_awal = new Array();
				var umur_akhir = new Array();
				var jenis_pakan = new Array();
				
				kode_strain = $('#inp_strain').val();
				jenis_kelamin = $('input:radio[name=jeniskelamin]:checked').val();
				tipe_kandang = $('input:radio[name=tipekandang]:checked').val();
				
				var tgl_efektif = $( "#inp_tanggalefektif" ).val();
				var tgl_efektif_arr = tgl_efektif.split(" "); 
				var index = (months.indexOf(tgl_efektif_arr[1]) >= 0) ? months.indexOf(tgl_efektif_arr[1]) : months_id.indexOf(tgl_efektif_arr[1]);
				
				tahun = tgl_efektif_arr[2];
				bulan = (parseInt(index) + 1);
				hari = tgl_efektif_arr[0];
				
				tgl_efektif = tahun + "-" + bulan + "-" + hari;
				
				if($("#inp_musim_in").is(':checked'))
					in_season = 'I';
				
				if($("#inp_musim_out").is(':checked'))
					out_season = 'O';
				
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
									url : "master/std_budidaya/add_std_budidaya_total/",
									data: {
										col_umur : umur_arr,
										col_pengurangan : pengurangan_arr,
										col_mati : mati_arr,
										col_afkir : afkir_arr,
										col_seleksi : seleksi_arr,
										col_dayahidup : dayahidup_arr,
										col_pakantarget : pakantarget_arr,
										col_pakanenergi : pakanenergi_arr,
										col_pakancumenergi : pakancumenergi_arr,
										col_pakanprotein : pakanprotein_arr,
										col_pakancumprotein : pakancumprotein_arr,
										col_bbtarget : bbtarget_arr,
										col_bbtotal : bbtotal_arr,
										col_keterangan : keterangan_arr,
										kode_strain : kode_strain,
										jenis_kelamin : jenis_kelamin,
										tipe_kandang : tipe_kandang,
										in_season : in_season,
										out_season : out_season,
										umur_awal : umur_awal,
										umur_akhir : umur_akhir,
										jenis_pakan : jenis_pakan,
										tgl_efektif : tgl_efektif
									}
								})
								.done(function(data){
									if(data.result == "success"){	
										$('#btnSaveStd').addClass("disabled");
										calculateStandarTarget();
										toastr.success("Proses Simpan " + selected_riwayat + " Berhasil",'Informasi');
									}else
										toastr.warning("Proses Simpan " + selected_riwayat + " Gagal",'Informasi');
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
									url : "master/std_budidaya/update_std_budidaya/",
									data: {
										kode_riwayat : selected_riwayat,
										col_umur : umur_arr,
										col_pengurangan : pengurangan_arr,
										col_mati : mati_arr,
										col_afkir : afkir_arr,
										col_seleksi : seleksi_arr,
										col_dayahidup : dayahidup_arr,
										col_pakantarget : pakantarget_arr,
										col_pakanenergi : pakanenergi_arr,
										col_pakancumenergi : pakancumenergi_arr,
										col_pakanprotein : pakanprotein_arr,
										col_pakancumprotein : pakancumprotein_arr,
										col_bbtarget : bbtarget_arr,
										col_bbtotal : bbtotal_arr,
										col_keterangan : keterangan_arr
									}
								})
								.done(function(data){
									if(data.result == "success"){
										$('#btnSaveStd').addClass("disabled");
										calculateStandarTarget();
										toastr.success("Proses Simpan " + selected_riwayat + " Berhasil",'Informasi');
									}else
										toastr.warning("Proses Simpan " + selected_riwayat + " Gagal",'Informasi');
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
	
	kode_strain = $('#inp_strain').val();
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/std_budidaya/get_masa_pertumbuhan/",
		data: {
			kode_strain : kode_strain
		}
	})
	.done(function(data){
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
		var prev_masapertumbuhan = null;
		var target_kg = new Array();
		
		var tempJumlah = 0;
		for(var i=0;i<data.length;i++){
			var curr_masapertumbuhan = data[i].deskripsi;
			
			if(prev_masapertumbuhan == null || prev_masapertumbuhan == curr_masapertumbuhan){
				prev_masapertumbuhan = curr_masapertumbuhan;
				tempJumlah += pakantarget_arr[i];
			}else{
				avg = tempJumlah*7/1000;
				index = (target_kg.length > 0) ? (target_kg.length + 1) : 0;
				target_kg[index] = avg;
				console.log(avg);
				tempJumlah = 0;
			}
		}
	});
}

function calculateStandarTarget(){
	console.log("itung");
	/*var dummy = [21,44,54,58,61,62,63,65,67,70,74,77,82,85,89,93,97,101,105,108,115,120,125,129,133,136,136,137,137,138,138,139,139,140,140,141,141,142,142,143,143,144,144,145,145,146,146,147,147,148,148,149,149,150,150,151,151,152,152,153,153,154,154,155,155,156,156,157,157];
	
	for(var i=0; i<dummy.length; i++){
		elm = $('input[name^="col_pakantarget"]').eq(i);
		elm.val(dummy[i]);
	}*/
		
	var col_pakantarget_arr = new Array();
	
	$('input[name^="col_pakantarget"]').each(function() {
		col_pakantarget_arr.push(!empty($(this).val()) ?  (isNaN(parseFloat($(this).val())) ? 0 : $(this).val() ) : 0);
	});
		
	var summary = new Array();
	var summary_avg = new Array();
	for(var i=0; i<masa_pertumbuhan_arr.length-1; i++){
		var summary_sub = 0;
		var min = parseInt(masa_pertumbuhan_arr[i][1]);
		var max = parseInt(masa_pertumbuhan_arr[i][2]);
		
		for(var j=min; j<=max; j++){
			summary_sub += parseFloat(col_pakantarget_arr[j]);
		}
		
		summary[i] = (parseFloat(summary_sub) * 7 / 1000).toFixed(2);
	}
	
	console.log("jumlah summary akhir : " + summary);
	
	var summary_grand = 0;
	var summary_grand_avg = 0;
	for(var i=0;i<summary.length;i++){
		summary_grand += parseFloat(summary[i]);
	}
	
	summary_grand = summary_grand.toFixed(2);
	
	for(var i=0;i<summary.length;i++){
		summary_avg[i] = (summary[i] / summary_grand * 100).toFixed(2);
	}
	
	for(var i=0;i<summary_avg.length;i++){
		summary_grand_avg += parseFloat(summary_avg[i]);
	}
	
	console.log("masa pertumbuhan:"+masa_pertumbuhan_arr.length);
	
	temp = new Array();
	// for(var i=0; i<masa_pertumbuhan_arr.length-1; i++){
		// elm = $('input[name^="target_berat"]').eq(i);
		// elm2 = $('input[name^="target_percent"]').eq(i);
		// elm.val(summary[i]);
		// elm2.val(isNaN(summary_avg[i]) ? 0 : summary_avg[i]);
	// }
	
	// $('input[name^="total_target_berat"]').val(summary_grand);
	// $('input[name^="total_target_percent"]').val(isNaN(summary_grand_avg) ? 0 : summary_grand_avg);
}

function cekDayaHidup(){
	var dayahidup_arr = new Array();
	
	$('input[name^="col_dayahidup"]').each(function() {
		dayahidup_arr.push($(this).val());
	});
	
	var previous_val = null;
	for(var i=0;i<dayahidup_arr.length;i++){
		var current_val = dayahidup_arr[i];
		
		var elm = $('input[name^="col_dayahidup"]').eq(i);
		
		if(previous_val == null){
			previous_val = dayahidup_arr[i];
		
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}else if(parseFloat(current_val) >= parseFloat(previous_val)){
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{
			previous_val = current_val;
			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

function cekCumEnergi(){
	var cumenergi_arr = new Array();
	
	$('input[name^="col_pakancumenergi"]').each(function() {
		cumenergi_arr.push($(this).val());
	});
	
	var previous_val = null;
	for(var i=0;i<cumenergi_arr.length;i++){
		var current_val = cumenergi_arr[i];
				
		var elm = $('input[name^="col_pakancumenergi"]').eq(i);
				
		if(previous_val == null){
			previous_val = cumenergi_arr[i];
			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}else if(parseFloat(current_val) <= parseFloat(previous_val)){			
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{
			previous_val = current_val;
			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

function cekCumProtein(){
	var cumprotein_arr = new Array();
	
	$('input[name^="col_pakancumprotein"]').each(function() {
		cumprotein_arr.push($(this).val());
	});
	
	var previous_val = null;
	for(var i=0;i<cumprotein_arr.length;i++){
		var current_val = cumprotein_arr[i];
				
		var elm = $('input[name^="col_pakancumprotein"]').eq(i);
				
		if(previous_val == null){
			previous_val = cumprotein_arr[i];
		}else if(parseFloat(current_val) <= parseFloat(previous_val)){
			elm.css({'border':'1px solid #D9411E'});
			elm.focus();
			elm.tooltip('enable');
			elm.tooltip('show');
			
			return false;
		}else{
			previous_val = current_val;
			
			elm.css({'border':'none'});
			elm.tooltip('hide');
			elm.tooltip('disable');
		}
	}
	
	return true;
}

function ambilData(){
	var col_mati = new Array();
	var col_afkir = new Array();
	var col_seleksi = new Array();
	var col_dayahidup = new Array();
	var col_pakan_target = new Array();
	var col_pakan_energi = new Array();
	var col_pakan_cumene = new Array();
	var col_pakan_protein = new Array();
	var col_pakan_cumpro = new Array();
	var col_bb_target = new Array();
	var col_bb_weight = new Array();
	
	$('.col-mati').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_mati.push($(obj).text());
	});
	
	$('.col-afkir').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_afkir.push($(obj).text());
	});
	
	$('.col-seleksi').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_seleksi.push($(obj).text());
	});
	
	$('.col-dayahidup').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_dayahidup.push($(obj).text());
	});
	
	$('.col-targetpakan').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_pakan_target.push($(obj).text());
	});
	
	$('.col-energi').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_pakan_energi.push($(obj).text());
	});
	
	$('.col-totalenergi').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_pakan_cumene.push($(obj).text());
	});
	
	$('.col-protein').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_pakan_protein.push($(obj).text());
	});
	
	$('.col-totalprotein').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_pakan_cumpro.push($(obj).text());
	});
	
	$('.col-targetbb').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_bb_target.push($(obj).text());
	});
	
	$('.col-bb').each(function(i, obj) {
		var txt = $(obj).text();
		var val = (txt != '-') ? txt : '-';
		col_bb_weight.push($(obj).text());
	});
	
	console.log("var col_mati = " + JSON.stringify(col_mati));
	console.log("var col_afkir = " + JSON.stringify(col_afkir));
	console.log("var col_seleksi = " + JSON.stringify(col_seleksi));
	console.log("var col_dayahidup = " + JSON.stringify(col_dayahidup));
	console.log("var col_pakan_target = " + JSON.stringify(col_pakan_target));
	console.log("var col_pakan_energi = " + JSON.stringify(col_pakan_energi));
	console.log("var col_pakan_cumene = " + JSON.stringify(col_pakan_cumene));
	console.log("var col_pakan_protein = " + JSON.stringify(col_pakan_protein));
	console.log("var col_pakan_cumpro = " + JSON.stringify(col_pakan_cumpro));
	console.log("var col_bb_target = " + JSON.stringify(col_bb_target));
	console.log("var col_bb_weight = " + JSON.stringify(col_bb_weight));
	
}
function isiDataDummy(){	
	
	var col_mati = ["0.50","0.20","0.20","0.17","0.20","0.20","0.17","0.20","0.20","0.17","0.20","0.20","0.17","0.20","0.20","0.17","0.20","0.20","0.17","0.20","0.20","0.17","0.20","0.20","0.20","0.40","0.40","0.30","0.30","0.30","0.30","0.30","0.20","0.20","0.20","0.20","0.10","0.20","0.20","0.20","0.20","0.20","0.20","0.20","0.20","0.20","0.20","0.20","0.10","0.20","0.20","0.20","0.20","0.20","0.20","0.20","0.10","0.20","0.20","0.20","0.20","0.20","0.10","0.10","0.10","0.10","0.10","0.10","0.10"];
	var col_afkir = ["-","0.50","-","0.50","-","-","0.50","-","-","0.50","-","-","0.50","-","-","-","-","0.50","-","-","-","0.50","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"];
	var col_seleksi = ["-","-","-","-","-","-","-","-","1.50","-","-","-","-","-","0.78","-","0.50","-","-","-","-","0.27","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-","-"];
	var col_dayahidup = ["99.50","98.80","98.60","97.93","97.73","97.53","96.86","96.66","94.96","94.29","94.09","93.89","93.22","93.02","92.04","91.87","91.17","90.47","90.30","90.10","89.90","88.96","88.76","88.56","88.36","87.96","87.56","87.26","86.96","86.66","86.36","86.06","85.86","85.66","85.46","85.26","85.16","84.96","84.76","84.56","84.36","84.16","83.96","83.76","83.56","83.36","83.16","82.96","82.86","82.66","82.46","82.26","82.06","81.86","81.66","81.46","81.36","81.16","80.96","80.76","80.56","80.36","80.26","80.16","80.06","79.96","79.86","79.76","79.66"];
	var col_pakan_target = ["21.00","44.00","54.00","58.00","61.00","62.00","63.00","65.00","67.00","70.00","74.00","77.00","82.00","85.00","89.00","93.00","97.00","101.00","105.00","108.00","115.00","120.00","125.00","129.00","133.00","136.00","136.00","137.00","137.00","138.00","138.00","139.00","139.00","140.00","140.00","141.00","141.00","142.00","142.00","143.00","143.00","144.00","144.00","145.00","145.00","146.00","146.00","147.00","147.00","148.00","148.00","149.00","149.00","150.00","150.00","151.00","151.00","152.00","152.00","153.00","153.00","154.00","154.00","155.00","155.00","156.00","156.00","157.00","157.00"];
	var col_pakan_energi = ["59.00","123.00","151.00","162.00","171.00","174.00","176.00","182.00","188.00","196.00","207.00","216.00","230.00","238.00","249.00","260.00","272.00","283.00","294.00","302.00","329.00","343.00","358.00","369.00","352.00","360.00","360.00","363.00","363.00","366.00","366.00","368.00","368.00","371.00","371.00","374.00","374.00","376.00","376.00","379.00","379.00","382.00","382.00","384.00","384.00","387.00","387.00","390.00","390.00","392.00","392.00","395.00","395.00","398.00","398.00","400.00","400.00","403.00","403.00","405.00","405.00","408.00","408.00","411.00","411.00","413.00","413.00","416.00","416.00"];
	var col_pakan_cumene = ["412.00","1274.00","2332.00","3469.00","4665.00","5880.00","7115.00","8389.00","9702.00","11074.00","12524.00","14034.00","15641.00","17307.00","19051.00","20874.00","22775.00","24755.00","26813.00","28930.00","31232.00","33634.00","36137.00","38719.00","41183.00","43703.00","46223.00","48764.00","51305.00","53867.00","56429.00","59005.00","61581.00","64178.00","66775.00","69393.00","72011.00","74643.00","77275.00","79928.00","82581.00","85255.00","87929.00","90617.00","93305.00","96014.00","98723.00","101453.00","104183.00","106927.00","109671.00","112436.00","115201.00","117987.00","120773.00","123573.00","126373.00","129194.00","132015.00","134850.00","137685.00","140541.00","143397.00","146274.00","149151.00","152042.00","154933.00","157845.00","160757.00"];
	var col_pakan_protein = ["3.80","7.90","9.70","10.40","11.00","11.20","9.80","10.10","10.40","10.90","11.50","11.90","12.70","13.20","13.80","14.40","15.00","15.70","16.30","16.70","18.40","19.20","20.00","20.60","18.00","18.40","18.40","18.50","18.50","18.60","18.60","18.80","18.80","18.90","18.90","19.00","19.00","19.20","19.20","19.30","19.30","19.40","19.40","19.60","19.60","19.70","19.70","19.80","19.80","20.00","20.00","20.10","20.10","20.30","20.30","20.40","20.40","20.50","20.50","20.70","20.70","20.80","20.80","20.90","20.90","21.10","21.10","21.20","21.20"];
	var col_pakan_cumpro = ["26.50","81.90","149.90","223.00","299.90","378.00","446.40","516.90","589.60","665.50","745.80","829.40","918.30","1010.60","1107.10","1208.00","1313.30","1422.90","1536.80","1654.00","1782.80","1917.20","2057.20","2201.60","2327.30","2455.80","2584.40","2713.80","2843.30","2973.70","3104.10","3235.50","3366.80","3499.10","3631.40","3764.70","3897.90","4032.10","4166.30","4301.40","4436.60","4572.60","4708.70","4845.80","4962.80","5120.70","5258.70","5397.60","5536.50","5676.40","5816.30","5957.10","6097.90","6239.60","6381.40","6524.10","6666.80","6810.40","6954.00","7098.60","7243.20","7388.70","7534.30","7680.80","7827.20","7974.60","8122.10","8270.40","8418.80"];
	var col_bb_target = ["40.000","150.000","340.000","540.000","700.000","870.000","1020.000","1170.000","1270.000","1400.000","1520.000","1640.000","1780.000","1900.000","2010.000","2150.000","2290.000","2440.000","2570.000","2760.000","2980.000","3250.000","3360.000","3470.000","3590.000","3700.000","3790.000","3880.000","3960.000","4030.000","4090.000","4140.000","4180.000","4210.000","4240.000","4260.000","4290.000","4310.000","4340.000","4360.000","4390.000","4410.000","4440.000","4460.000","4490.000","4510.000","4540.000","4560.000","4590.000","4610.000","4640.000","4660.000","4690.000","4710.000","4740.000","4760.000","4790.000","4810.000","4840.000","4860.000","4890.000","4910.000","4940.000","4960.000","4990.000","5010.000","5040.000","5060.000","5090.000"];
	var col_bb_weight = ["0.00","275.00","126.70","58.80","29.60","24.30","17.20","14.70","8.50","10.20","8.60","7.90","8.50","6.70","5.80","7.00","6.50","6.60","5.30","7.40","7.20","9.80","3.40","3.30","3.50","3.10","2.40","2.40","2.10","1.80","1.50","1.20","1.00","0.70","0.70","0.50","0.70","0.50","0.70","0.50","0.70","0.50","0.70","0.50","0.70","0.40","0.70","0.40","0.70","0.40","0.70","0.40","0.60","0.40","0.60","0.40","0.60","0.40","0.60","0.40","0.60","0.40","0.60","0.40","0.60","0.40","0.60","0.40","0.60"];
	
	for(var i=0;i<=68;i++){
		var elm = null;
		elm = $('input[name^="col_mati"]').eq(i);
		elm.val(col_mati[i]);
		
		elm = $('input[name^="col_afkir"]').eq(i);
		elm.val(col_afkir[i]);
		
		elm = $('input[name^="col_seleksi"]').eq(i);
		elm.val(col_seleksi[i]);
		
		elm = $('input[name^="col_dayahidup"]').eq(i);
		elm.val(col_dayahidup[i]);
		
		elm = $('input[name^="col_pakantarget"]').eq(i);
		elm.val(col_pakan_target[i]);
		
		elm = $('input[name^="col_pakanenergi"]').eq(i);
		elm.val(col_pakan_energi[i]);
		
		elm = $('input[name^="col_pakancumenergi"]').eq(i);
		elm.val(col_pakan_cumene[i]);
		
		elm = $('input[name^="col_pakanprotein"]').eq(i);
		elm.val(col_pakan_protein[i]);
		
		elm = $('input[name^="col_pakancumprotein"]').eq(i);
		elm.val(col_pakan_cumpro[i]);
		
		elm = $('input[name^="col_bbtarget"]').eq(i);
		elm.val(col_bb_target[i]);
		
		elm = $('input[name^="col_bbtotal"]').eq(i);
		elm.val(col_bb_weight[i]);
	}
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