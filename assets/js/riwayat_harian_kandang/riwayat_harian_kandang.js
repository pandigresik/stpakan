var lhk_state = "";
var date_now = "";
var selected_farm = "";
var selected_kandang = "";
var selected_noreg = "";
var selected_kode_verifikasi = "";
var selected_tgl_doc_in = "";
var tgl_lhk_now = "";
var kandang_in_farm = new Array();
var pengurangan_ayam_arr = new Array();
var barang_obat = new Array();
var barang_vaksin = new Array();
var batas_atas_pakan_jantan = 0;
var batas_atas_pakan_betina = 0;

//Var untuk browse penambahan lain-lain ayam
var valid_penambahan_lain = true;

var tambah_jantan_lain_arr = new Array();
var tambah_betina_lain_arr = new Array();
var tambah_keterangan_lain_arr = new Array();
var tambah_noberitaacara_lain_arr = new Array();
var tambah_beritaacara_lain_arr = new Array();

//Var untuk browse pengurangan lain-lain ayam
var valid_pengurangan_lain = true;

var kurang_jantan_lain_arr = new Array();
var kurang_betina_lain_arr = new Array();
var kurang_keterangan_lain_arr = new Array();
var kurang_noberitaacara_lain_arr = new Array();
var kurang_beritaacara_lain_arr = new Array();

//Var untuk browse pengurangan ayam
var kandang_id_arr = new Array();
var jantan_arr = new Array();
var betina_arr = new Array();
var keterangan_arr = new Array();
var beritaacara_arr = new Array();

//Var untuk modal uniformity
var umur_minggu = 0;
var target_bb_j_minggu = 0;
var target_bb_b_minggu = 0;

var timbang_j_bb = 0;
var timbang_j_ba = 0;
var timbang_j_arr_bb = new Array();
var timbang_j_arr_jml = new Array();

var timbang_b_bb = 0;
var timbang_b_ba = 0;
var timbang_b_arr_bb = new Array();
var timbang_b_arr_jml = new Array();

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

var months_short = new Array(12);
months_short[0] = "Jan";
months_short[1] = "Feb";
months_short[2] = "Mar";
months_short[3] = "Apr";
months_short[4] = "May";
months_short[5] = "Jun";
months_short[6] = "Jul";
months_short[7] = "Aug";
months_short[8] = "Sep";
months_short[9] = "Oct";
months_short[10] = "Nov";
months_short[11] = "Dec";

var months_id_short = new Array(12);
months_id_short[0] = "Jan";
months_id_short[1] = "Feb";
months_id_short[2] = "Mar";
months_id_short[3] = "Apr";
months_id_short[4] = "Mei";
months_id_short[5] = "Jun";
months_id_short[6] = "Jul";
months_id_short[7] = "Agt";
months_id_short[8] = "Sep";
months_id_short[9] = "Okt";
months_id_short[10] = "Nop";
months_id_short[11] = "Des";

$("#btnSisa").click(function(){
	retur_pakan('Y');
});

$(document).ready(function () {
	selected_farm = $('#inp_farm').val();
	setInputKandang(selected_farm);
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/get_obat_vaksin/",
		data: {
		}
	})
	.done(function(data){
		date_now = data.date_now;
		
		var data_obat = data.obat;
		var data_vaksin = data.vaksin;
		
		var select_obat = '<select class="form-control input-sm"  name="inp_obat_kodebarang[]" onchange="setNamaBarang(this)">';
		select_obat += '<option value=""></option>';
		for(var i=0;i<data_obat.length;i++){
			var obj = data_obat[i];
			barang_obat.push(new Array(obj.kode_barang, obj.nama_barang));
			
			select_obat += '<option value="' + obj.kode_barang + '">' + obj.nama_barang + '</option>';
		}
		select_obat += '</select>';
		
		var select_vaksin = '<select class="form-control input-sm"  name="inp_vaksin_kodebarang[]" onchange="setNamaBarang(this)">';
		select_vaksin += '<option value=""></option>';
		for(var i=0;i<data_vaksin.length;i++){
			var obj = data_vaksin[i];
			barang_vaksin.push(new Array(obj.kode_barang, obj.nama_barang));
			
			select_vaksin += '<option value="' + obj.kode_barang + '">' + obj.nama_barang + '</option>';
		}
		select_vaksin += '</select>';
				
		html_obat = ''+
		'<tr>'+
		'	<td>' + select_obat + '</td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_obat_namabarang[]" disabled></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_obat_keterangan[]"></td>'+
		'	<td class="vert-align col-md-1">'+
		'		<button type="button" data-toggle="tooltip" onclick="tambahObat(this)" title="Tambah" class="btn btn-sm btn-primary">'+
		'			<i class="glyphicon glyphicon-plus-sign"></i>'+
		'		</button>'+
		'	</td>'+
		'</tr>';
		
		html_vaksin = ''+
		'<tr>'+
		'	<td>' + select_vaksin + '</td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_namabarang[]" disabled></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_keterangan[]"></td>'+
		'	<td class="vert-align col-md-1">'+
		'		<button type="button" data-toggle="tooltip" onclick="tambahVaksin(this)" title="Tambah" class="btn btn-sm btn-primary">'+
		'			<i class="glyphicon glyphicon-plus-sign"></i>'+
		'		</button>'+
		'	</td>'+
		'</tr>';
		
		$('#lhk_obat > tbody').html(html_obat);
		$('#lhk_vaksin > tbody').html(html_vaksin);
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
	
	$('#div_tgl_lhk').datetimepicker({
		pickTime: false,
		format : "DD MMM YYYY"
	});
	var todayDbase = new Date($('#inp_today').val()); // format yyyy-mm-dd
	//alert($('#inp_today').val());
	$('#div_tgl_lhk').data("DateTimePicker").setMaxDate(new Date(todayDbase.setDate(todayDbase.getDate()-1)));
	//$('#div_tgl_lhk').data("DateTimePicker").disable();
	
});

$("#div_tgl_lhk").on("dp.change", function(e) {
	
	var pad = "00";
	
	var tgl_arr = $("#inp_tgl_lhk").val().split(" ");
	var index = (months_short.indexOf(tgl_arr[1]) >= 0) ? months_short.indexOf(tgl_arr[1]) : months_id_short.indexOf(tgl_arr[1]);
	tahun = parseInt(tgl_arr[2]);
	bulan = pad.substring(0, pad.length - ("" + (parseInt(index)+1)).length) + (parseInt(index)+1);
	hari = pad.substring(0, pad.length - ("" + parseInt(tgl_arr[0])).length) + parseInt(tgl_arr[0]);
	
	// var selected_date = tahun+"-"+bulan+"-"+hari;
	var selected_date = tahun+bulan+hari;
	
	var lhk_sekarang = "";
	var lhk_sekarang_fix = "";
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/get_last_lhk/",
		data: {
			no_reg : selected_noreg
		}
	})
	.done(function(data){		
		var pad = '00';
		
		if(!empty(data.tgl_transaksi)){
			var tgl_last_lhk = (data.tgl_transaksi).split('-');
			var ddLhk = (parseInt(tgl_last_lhk[2]) + 1);
			var mmLhk = parseInt(tgl_last_lhk[1]);
			var yyLhk = parseInt(tgl_last_lhk[0]);
			
			lhk_sekarang     = yyLhk+(pad + (mmLhk).toString()).slice(-pad.length)+(pad + (ddLhk).toString()).slice(-pad.length)
			lhk_sekarang_fix = yyLhk+"-"+(pad + (mmLhk).toString()).slice(-pad.length)+"-"+(pad + (ddLhk).toString()).slice(-pad.length)
		}else{
			lhk_sekarang = selected_tgl_doc_in;
		}
		
		if(selected_date == lhk_sekarang){
			//ISI LHK
			LoadDataLastLHK(selected_noreg, selected_tgl_doc_in, lhk_sekarang_fix);
			lhk_state = "WRITE";
		}else{
			//LIHAT LHK
			//alert(tahun+"-"+bulan+"-"+hari);
			var d = new Date(tahun, bulan, hari);
			d.setDate(d.getDate());
			//alert("selected_date:"++"-"+selected_date);
			
			lhk_state = "READ";
			LoadDataLHK(d.getFullYear()+"-"+(pad + ((d.getMonth())).toString()).slice(-pad.length)+"-"+(pad + ((d.getDate())).toString()).slice(-pad.length));
			console.log(selected_date+":"+lhk_sekarang+">lihat lhk");
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
});

$("#div_tgl_lhk").on("dp.show", function(e) {
	var todayDbase = new Date($('#inp_today').val()); // format yyyy-mm-dd	
	
	//if(e.date > new Date(todayDbase.setDate(todayDbase.getDate()-1))){
	//	console.log("tgl lhk loh:"+tgl_lhk_now);
	//	$('#div_tgl_lhk').data("DateTimePicker").setDate(new Date(tgl_lhk_now));
	//}
});

function initializeData(){
	var pad = '00';
	
	var tgl_lhk = $('#inp_tgl_lhk').val();
	var tgl_lhk_arr = tgl_lhk.split(' ');
	var ddLhk = parseInt(tgl_lhk_arr[0]);
	var mmLhk = ((months.indexOf(tgl_lhk_arr[1]) + 1) >= 0) ? (months.indexOf(tgl_lhk_arr[1]) + 1) : (months_id.indexOf(tgl_lhk_arr[1]) + 1);
	var yyLhk = parseInt(tgl_lhk_arr[2]);
	var tgl_lhk_fix = yyLhk + '-' + (pad + mmLhk).slice(-pad.length) + '-' + (pad + ddLhk).slice(-pad.length);
	
	var tgl_doc_in = $('#inp_doc_in').val();
	var tgl_doc_in_arr = tgl_doc_in.split(' ');
	var ddDoc = parseInt(tgl_doc_in_arr[0]);
	var mmDoc = ((months.indexOf(tgl_doc_in_arr[1]) + 1) >= 0) ? (months.indexOf(tgl_doc_in_arr[1]) + 1) : (months_id.indexOf(tgl_doc_in_arr[1]) + 1);
	var yyDoc = parseInt(tgl_doc_in_arr[2]);
	var tgl_doc_fix = yyDoc + '-' + (pad + mmDoc).slice(-pad.length) + '-' + (pad + ddDoc).slice(-pad.length);
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/get_data_last_lhk/",
		data: {
			tgl_lhk : tgl_lhk_fix,
			no_reg : selected_noreg,
			tgl_doc_in : tgl_doc_fix
		}
	})
	.done(function(data){
		
		var obj_populasi = data.populasi;
		var obj_pakan = data.pakan;
		var obj_batas_pakai_pakan = data.batas_pakai_pakan;
		
		batas_atas_pakan_jantan = obj_batas_pakai_pakan.J;
		batas_atas_pakan_betina = obj_batas_pakai_pakan.B;
		
		console.log("batas max pakai pakan J : " + batas_atas_pakan_jantan + '; B : ' + batas_atas_pakan_betina);
		
		var pa_b = obj_populasi.b_jml;
		var pa_j = obj_populasi.j_jml;
		var pi_b = obj_populasi.b_pindah;
		var pi_j = obj_populasi.j_pindah;
		
		var b_pindah_semu = obj_populasi.b_pindah_semu;
		var j_pindah_semu = obj_populasi.j_pindah_semu;
		
		var b_daya_hidup = obj_populasi.b_daya_hidup;
		var j_daya_hidup = obj_populasi.j_daya_hidup;
		var b_jml_pembagi = obj_populasi.b_jumlah_pembagi;
		var j_jml_pembagi = obj_populasi.j_jumlah_pembagi;
		
		var pakan_j = new Array(),
		    pakan_b = new Array(),
			i_j = 0,
			i_b = 0;
		
		if(!empty(obj_pakan)){		
			for(var i=0;i<obj_pakan.length;i++){
				var obj = obj_pakan[i];
				if(obj.jk == 'J'){
					var totKg = parseFloat(obj.berat_awal) + parseFloat(obj.berat_kirim);
					var tot = parseInt(obj.jml_awal) + parseInt(obj.jml_kirim);
					pakan_j[i_j] = ''+
					'<tr>'+
					'<td class="vert-align">' + obj.jenis_kelamin + '</td>'+
					'	<td class="vert-align" >' + 
							obj.nama_barang + 
							'<input type="hidden" class="form-control input-sm" name="inp_j_nama_pakan[]" value="' + obj.nama_barang + '">' + 
							'<input type="hidden" class="form-control input-sm" name="inp_j_pakan[]" value="' + obj.kode_barang + '">' + 
							'<input type="hidden" class="form-control input-sm" name="inp_j_bentuk_pakan[]" value="' + obj.bentuk_barang + '">' + 
					'	</td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_stokAwalKg[]" value="' + obj.berat_awal + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_stokAwalSak[]" value="' + obj.jml_awal + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_kirimKg[]" value="' + obj.berat_kirim + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_kirimSak[]"  value="' + obj.jml_kirim + '"disabled></td>'+
					'	<td><input type="text" data-jeniskelamin="J" class="form-control input-sm inp-numeric" name="inp_j_terpakaiKg[]" value="0" onkeyup="return cekNumerikPakanKg(event, this)" title="Kg harus lebih besar dari jumlah Sak"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_terpakaiSak[]" value="0" onkeyup="cekNumerikPakanSak(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_stokAkhirKg[]" value="' + Number(totKg).toFixed(3) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_j_stokAkhirSak[]" value="' + tot + '" disabled></td>'+
					'</tr>';
					
					i_j++;
				}else{
					var totKg = parseFloat(obj.berat_awal) + parseFloat(obj.berat_kirim);
					var tot = parseInt(obj.jml_awal) + parseInt(obj.jml_kirim);
					pakan_b[i_b] = ''+
					'<tr>'+
					'<td class="vert-align">' + obj.jenis_kelamin + '</td>'+
					'	<td class="vert-align" >' + 
							obj.nama_barang + 
							'<input type="hidden" class="form-control input-sm" name="inp_b_nama_pakan[]" value="' + obj.nama_barang + '">' + 
							'<input type="hidden" class="form-control input-sm" name="inp_b_pakan[]" value="' + obj.kode_barang + '">' + 
							'<input type="hidden" class="form-control input-sm" name="inp_b_bentuk_pakan[]" value="' + obj.bentuk_barang + '">' + 
					'	</td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_stokAwalKg[]" value="' + obj.berat_awal + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_stokAwalSak[]" value="' + obj.jml_awal + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_kirimKg[]" value="' + obj.berat_kirim + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_kirimSak[]"  value="' + obj.jml_kirim + '"disabled></td>'+
					'	<td><input type="text" data-jeniskelamin="B" class="form-control input-sm inp-numeric" name="inp_b_terpakaiKg[]" value="0" onkeyup="return cekNumerikPakanKg(event, this)" title="Kg harus lebih besar dari jumlah Sak"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_terpakaiSak[]" value="0" onkeyup="cekNumerikPakanSak(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_stokAkhirKg[]" value="' + Number(totKg).toFixed(3) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_b_stokAkhirSak[]"  value="' + tot + '" disabled></td>'+
					'</tr>';
					
					i_b++;
				}
			}
		}
		
		var pakan_all = pakan_j.concat(pakan_b);
		
		$('#lhk_pakan > tbody').html(pakan_all.join(''));
		
		$('#inp_populasiAwalJantan').val(pa_j);
		$('#inp_populasiAkhirJantan').val((parseInt(pa_j)+parseInt(pi_j)));
		$('#inp_populasiAwalBetina').val(pa_b);
		$('#inp_populasiAkhirBetina').val((parseInt(pa_b)+parseInt(pi_b)));
		$('#inp_tambahJantan').val(pi_j);
		$('#inp_tambahBetina').val(pi_b);
		
		$('#inp_j_pindah_semu').val(j_pindah_semu);
		$('#inp_j_daya_hidup').val(j_daya_hidup);
		$('#inp_j_jml_pembagi').val(j_jml_pembagi);
		$('#inp_b_pindah_semu').val(b_pindah_semu);
		$('#inp_b_daya_hidup').val(b_daya_hidup);
		$('#inp_b_jml_pembagi').val(b_jml_pembagi);
				
		count_rasio();
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
		$('#btnBrowseKurangBetina').removeClass('disabled');
		$('#btnBrowseKurangJantan').removeClass('disabled');
	});
};

function count_rasio(){
	var pawal_j = $('#inp_populasiAwalJantan').val();
	var pawal_b = $('#inp_populasiAwalBetina').val();
	
	var pakhir_j = $('#inp_populasiAkhirJantan').val();
	var pakhir_b = $('#inp_populasiAkhirBetina').val();
	
	var awal_low = (parseInt(pawal_j) > parseInt(pawal_b)) ? parseInt(pawal_b) : parseInt(pawal_j);
	var akhir_low = (parseInt(pakhir_j) > parseInt(pakhir_b)) ? parseInt(pakhir_b) : parseInt(pakhir_j);
	
	if(awal_low > 0 && akhir_low > 0){
		$('#inp_populasiAwalRasio').val((pawal_j/awal_low) + ':' + Number(Math.round(parseFloat(pawal_b/awal_low) * 1000) / 1000).toFixed(2));
		$('#inp_populasiAkhirRasio').val((pakhir_j/akhir_low) + ':' + Number(Math.round(parseFloat(pakhir_b/akhir_low) * 1000) / 1000).toFixed(2));
	}
}

function setInputKandang(kode_farm){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/get_kandang_farm/",
		data: {
			kode_farm:kode_farm
		}
	})
	.done(function(data){
		for(var i=0;i<data.length;i++){
			var obj = data[i];
			
			if(obj.id != selected_kandang){
				var valueToPush = new Array();
				valueToPush[0] = data[i].id;
				valueToPush[1] = data[i].name;
				valueToPush[2] = data[i].no_reg;
				kandang_in_farm.push(valueToPush);
			}
		}
	
		var $input = $('#inp_kandang');
		$input.typeahead({source:data, 
					autoSelect: true}); 
		$input.change(function() {
			var current = $input.typeahead("getActive");
			if (current) {
				
				// Some item from your model is active!
				if (current.name == $input.val()) {
					var todayDbase = $('#inp_today').val();
					var todayDbase_arr = todayDbase.split('-');
					var todayDbase_hari = todayDbase_arr[2];
					var todayDbase_bulan = todayDbase_arr[1];
					var todayDbase_tahun = todayDbase_arr[0];
					
					var ddLhk, mmLhk, yyLhk;
					
					selected_kandang = current.id;
					selected_noreg = current.no_reg;
					selected_tgl_doc_in = current.tgl_doc_in;
					tgl = current.tgl_doc_in; 
					selected_kode_verifikasi = current.kode_verifikasi;
										
					var docInDate = new Date(selected_tgl_doc_in);					
					var LhkDate = new Date(todayDbase);					
					$('#div_tgl_lhk').data("DateTimePicker").setMinDate(new Date(docInDate.setDate(docInDate.getDate())));
					//$('#div_tgl_lhk').data("DateTimePicker").setDate(new Date(LhkDate.setDate(LhkDate.getDate())));
					
					tgl_temp = tgl.split("-"); 
					ddDocIn = parseInt(tgl_temp[2]);
					mmDocIn = parseInt(tgl_temp[1]);
					yyDocIn = parseInt(tgl_temp[0]);
					
					last = '';
					
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "riwayat_harian_kandang/get_last_lhk/",
						data: {
							no_reg : selected_noreg
						}
					})
					.done(function(data){
						$('#btnSimpan').removeClass("disabled");
						if(!empty(data.tgl_transaksi)){
							last = data.tgl_transaksi;
						}
						
						if(!empty(last)){
							t_arr = last.split('-');
							ddLhk = (parseInt(t_arr[2]) + 1);
							mmLhk = parseInt(t_arr[1]);
							yyLhk = parseInt(t_arr[0]);
						}else{
							ddLhk = ddDocIn;
							mmLhk = mmDocIn;
							yyLhk = yyDocIn;
						}
						
						$('#inp_flock').val(current.nama_flok);
						$('#inp_doc_in_jantan').val(current.jml_jantan);
						$('#inp_doc_in_betina').val(current.jml_betina);
						$('#inp_doc_in').val(ddDocIn +' '+months[mmDocIn-1]+' '+yyDocIn);
						
						var pad = '00';						
						var todayDate = new Date(todayDbase_tahun+"-"+(pad + (todayDbase_bulan).toString()).slice(-pad.length)+"-"+(pad + (todayDbase_hari).toString()).slice(-pad.length));
						var nextDate = new Date(yyLhk+"-"+(pad + (mmLhk).toString()).slice(-pad.length)+"-"+(pad + (ddLhk).toString()).slice(-pad.length));
						
						var day = nextDate.getDate();
						var monthIndex = nextDate.getMonth();
						var year = nextDate.getFullYear();
						// $( "#inp_tgl_lhk" ).val(day +' '+months[monthIndex-1]+' '+year);
						$( "#inp_tgl_lhk" ).val(day +' '+months[monthIndex]+' '+year);
												
						
						tgl_lhk_now = year+"-"+(pad + (monthIndex).toString()).slice(-pad.length)+"-"+(pad + (day).toString()).slice(-pad.length);
						
						var todayDay = todayDate.getDate();
						var todayMonth = todayDate.getMonth();
						var todayYear = todayDate.getFullYear();
						var todayString = todayYear.toString()+todayMonth.toString()+todayDay.toString();
						var lhkString = year.toString()+(monthIndex).toString()+day.toString();
						
						var umur_M = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) / 7;
						var umur_H = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) % 7;
						
						resetInputLhk();
						initializeData();
						
						umur_minggu = Math.abs(Math.floor(umur_M));						
						$( "#inp_umur" ).val(Math.abs(Math.floor(umur_M)) + ' + ' + Math.abs(parseInt(umur_H)));
						
						console.log("Umur ayam adl " + Math.abs(Math.floor(umur_M)) + " minggu, maka:");
						if(Math.abs(Math.floor(umur_M)) == 10 || Math.abs(Math.floor(umur_M)) == 19 || Math.abs(Math.floor(umur_M)) == 42){
							console.log("-boleh input penambahan dan pengurangan lain-lain");
							$('#btnBrowseTambahJantanLain').removeClass('disabled');
							$('#btnBrowseTambahBetinaLain').removeClass('disabled');
														
							$('#btnBrowseKurangJantanLain').removeClass('disabled');
							$('#btnBrowseKurangBetinaLain').removeClass('disabled');
						}else{
							console.log("-tidak boleh input penambahan dan pengurangan lain-lain");
							// $('#btnBrowseTambahJantanLain').removeClass('disabled');
							// $('#btnBrowseKurangJantanLain').removeClass('disabled');
						}
						
						var pad = '00';
						var todayString2 = todayYear.toString()+(pad + todayMonth.toString()).slice(-pad.length)+(pad + todayDay.toString()).slice(-pad.length);
						var lhkString2   = year.toString()+(pad + (monthIndex).toString()).slice(-pad.length)+(pad + day.toString()).slice(-pad.length);
						
						console.log(todayString2 + '<' + lhkString2);
						if(parseInt(todayString2)<=parseInt(lhkString2)){
							console.log("disabled");
							disabledLhk();
						}else{
							console.log("enabled");
							enabledLhk();
						}
						
						lhk_state = "WRITE";
					})
					.fail(function(reason){
						console.info(reason);
					})
					.then(function(data){
						//Ambil target berat BB di std budidaya
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "riwayat_harian_kandang/get_target_bb/",
							data: {
								umur : umur_minggu,
								no_reg : selected_noreg
							}
						})
						.done(function(data){
							for(var i=0;i<data.length;i++){
								var obj = data[i];
								if(obj.JENIS_KELAMIN == 'B'){
									target_bb_b_minggu = obj.TARGET_BB;
								}else{
									target_bb_j_minggu = obj.TARGET_BB;
								}
							}
						})
						.fail(function(reason){
							console.info(reason);
						})
						.then(function(data){
							
						});
					});
					// This means the exact match is found. Use toLowerCase() if you want case insensitive match.
				} else {
					// selected_kandang = '';
					// This means it is only a partial match, you can either add a new item 
					// or take the active if you don't want new items
				}
			} else {
				// Nothing is active so it is a new value (or maybe empty value)
			}
		});
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

function LoadDataLastLHK(noreg, tgl_doc_in, tgl_lhk_sekarang){
	console.log(noreg+";"+tgl_doc_in+";"+tgl_lhk_sekarang);
	//Dipanggil jika tgl input sama dengan tanggal DOC IN atau Tanggal Next LHK
	var todayDbase = $('#inp_today').val();
	var todayDbase_arr = todayDbase.split('-');
	var todayDbase_hari = todayDbase_arr[2];
	var todayDbase_bulan = todayDbase_arr[1];
	var todayDbase_tahun = todayDbase_arr[0];
	
	var tgl_temp_doc_in = tgl_doc_in.split("-"); 
	var ddDocIn = parseInt(tgl_temp_doc_in[2]);
	var mmDocIn = parseInt(tgl_temp_doc_in[1]);
	var yyDocIn = parseInt(tgl_temp_doc_in[0]);
	
	var tgl_temp_lhk = tgl_lhk_sekarang.split("-"); 
	var ddLhk = parseInt(tgl_temp_lhk[2]);
	var mmLhk = parseInt(tgl_temp_lhk[1]);
	var yyLhk = parseInt(tgl_temp_lhk[0]);
	
	var umur_M = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) / 7;
	var umur_H = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) % 7;
	
	resetInputLhk();
	initializeData();
	
	$( "#inp_umur" ).val(Math.abs(Math.floor(umur_M)) + ' + ' + Math.abs(parseInt(umur_H)));
	console.log("Umur ayam adl " + Math.abs(Math.floor(umur_M)) + " minggu, maka:");
	if(Math.abs(Math.floor(umur_M)) == 10 || Math.abs(Math.floor(umur_M)) == 19 || Math.abs(Math.floor(umur_M)) == 42){
		console.log("-boleh input penambahan dan pengurangan lain-lain");
		$('#btnBrowseTambahJantanLain').removeClass('disabled');
		$('#btnBrowseTambahBetinaLain').removeClass('disabled');
									
		$('#btnBrowseKurangJantanLain').removeClass('disabled');
		$('#btnBrowseKurangBetinaLain').removeClass('disabled');
	}else{
		console.log("-tidak boleh input penambahan dan pengurangan lain-lain");
	}
	
	var pad = '00';
	var todayDate = new Date(todayDbase_tahun, todayDbase_bulan, todayDbase_hari);
	var lhkDate   = new Date(yyLhk, mmLhk, ddLhk);
	
	var todayDay = todayDate.getDate();
	var todayMonth = todayDate.getMonth();
	var todayYear = todayDate.getFullYear();

	var lhkDay = lhkDate.getDate();
	var lhkMonth = lhkDate.getMonth();
	var lhkYear = lhkDate.getFullYear();
	
	var todayString = todayYear.toString()+(pad + todayMonth.toString()).slice(-pad.length)+(pad + todayDay.toString()).slice(-pad.length);
	var lhkString   = lhkYear.toString()+(pad + (lhkMonth).toString()).slice(-pad.length)+(pad + lhkDay.toString()).slice(-pad.length);

	console.log(todayString + '<' + lhkString);
	if(parseInt(todayString)<=parseInt(lhkString)){
		disabledLhk();
	}else{
		enabledLhk();
	}
}

function LoadDataLHK(tgl_lhk){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/go_view_lhk/",
		data: {
			tgl_lhk : tgl_lhk,
			no_reg : selected_noreg,
			tgl_doc_in : selected_tgl_doc_in
		}
	})
	.done(function(data){
		if(data.rhk){
			var rhk = data.rhk;
			var rhk_penimbangan = data.rhk_penimbangan;
			var rhk_pakan = data.rhk_pakan;
			var rhk_produksi = data.rhk_produksi;
			var rhk_vaksin = data.rhk_vaksin;
			var rhk_pindah = data.rhk_pindah;
			var tgl_doc_in = data.tgl_doc_in;
			var tgl_lhk = data.tgl_lhk;
			var umur = data.umur;
			var class_pakan = data.class_pakan;
			
			//header
			$('#inp_umur').val(umur);
			$('#inp_bb_ja').val(rhk.J_BERAT_BADAN);
			$('#inp_bb_be').val(rhk.B_BERAT_BADAN);
			
			//rhk_populasi
			var populasiJantanAwal = parseInt(rhk.J_MATI) + parseInt(rhk.J_AFKIR) + parseInt(rhk.J_SEXSLIP) + parseInt(rhk.J_KANIBAL) + parseInt(rhk.J_CAMPUR) + parseInt(rhk.J_SELEKSI) + parseInt(rhk.J_LAIN2) + parseInt(rhk.J_JUMLAH);
			var populasiBetinaAwal = parseInt(rhk.B_MATI) + parseInt(rhk.B_AFKIR) + parseInt(rhk.B_SEXSLIP) + parseInt(rhk.B_KANIBAL) + parseInt(rhk.B_CAMPUR) + parseInt(rhk.B_SELEKSI) + parseInt(rhk.B_LAIN2) + parseInt(rhk.B_JUMLAH);
			
			$('#inp_populasiAwalJantan').val(populasiJantanAwal);				
			$('#inp_tambahJantan').val('0');				
			$('#inp_tambahJantanLain').val('0');				
			$('#inp_kurangJantanMati').val(rhk.J_MATI);				
			$('#inp_kurangJantanAfkir').val(rhk.J_AFKIR);				
			$('#inp_kurangJantan').val('0');				
			$('#inp_kurangJantanSexslip').val(rhk.J_SEXSLIP);				
			$('#inp_kurangJantanKanibal').val(rhk.J_KANIBAL);				
			$('#inp_kurangJantanCampur').val(rhk.J_CAMPUR);				
			$('#inp_kurangJantanSeleksi').val(rhk.J_SELEKSI);				
			$('#inp_kurangJantanLain').val(rhk.J_LAIN2);				
			$('#inp_populasiAkhirJantan').val(rhk.J_JUMLAH);

			$('#inp_populasiAwalBetina').val(populasiBetinaAwal);				
			$('#inp_tambahBetina').val('0');				
			$('#inp_tambahBetinaLain').val('0');				
			$('#inp_kurangBetinaMati').val(rhk.B_MATI);				
			$('#inp_kurangBetinaAfkir').val(rhk.B_AFKIR);				
			$('#inp_kurangBetina').val('0');				
			$('#inp_kurangBetinaSexslip').val(rhk.B_SEXSLIP);				
			$('#inp_kurangBetinaKanibal').val(rhk.B_KANIBAL);				
			$('#inp_kurangBetinaCampur').val(rhk.B_CAMPUR);				
			$('#inp_kurangBetinaSeleksi').val(rhk.B_SELEKSI);				
			$('#inp_kurangBetinaLain').val(rhk.B_LAIN2);				
			$('#inp_populasiAkhirBetina').val(rhk.B_JUMLAH);

			var pembagi_awal = parseInt(populasiJantanAwal) > parseInt(populasiBetinaAwal) ? parseInt(populasiBetinaAwal) : parseInt(populasiJantanAwal);
			var pembagi_akhr = parseInt(rhk.J_JUMLAH) > parseInt(rhk.B_JUMLAH) ? parseInt(rhk.B_JUMLAH) : parseInt(rhk.J_JUMLAH);
			
			$('#inp_populasiAwalRasio').val(parseFloat(populasiJantanAwal/pembagi_awal) + ":" + Number(Math.round(parseFloat(populasiBetinaAwal/pembagi_awal) * 1000) / 1000).toFixed(2));				
			$('#inp_populasiAkhirRasio').val(parseFloat(rhk.J_JUMLAH/pembagi_akhr) + ":" + Number(Math.round(parseFloat(rhk.B_JUMLAH/pembagi_akhr) * 1000) / 1000).toFixed(2));	
			
			//rhk_pakan
			var pakan_j = new Array(),
		    pakan_b = new Array(),
			i_j = 0,
			i_b = 0;
			
			for(var i=0;i<rhk_pakan.length;i++){
				var obj = rhk_pakan[i];
				if(obj.JENIS_KELAMIN == 'J'){
					var totKg = parseFloat(obj.berat_awal) + parseFloat(obj.berat_kirim);
					var tot = parseInt(obj.jml_awal) + parseInt(obj.jml_kirim);
					pakan_j[i_j] = ''+
					'<tr>'+
					'<td class="vert-align">' + 'JANTAN'+ '</td>'+
					'	<td class="vert-align" >' + obj.NAMA_BARANG + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_AWAL + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_AWAL + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_TERIMA + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_TERIMA + '</td>'+
					'	<td class="inp-numeric" '+class_pakan[obj.JENIS_KELAMIN]+'>' + obj.BRT_PAKAI + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_PAKAI + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_AKHIR + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_AKHIR + '</td>'+
					'</tr>';
					
					i_j++;
				}else{
					var totKg = parseFloat(obj.berat_awal) + parseFloat(obj.berat_kirim);
					var tot = parseInt(obj.jml_awal) + parseInt(obj.jml_kirim);
					pakan_b[i_b] = ''+
					'<tr>'+
					'	<td class="vert-align">' + 'BETINA' + '</td>'+
					'	<td class="vert-align" >' + obj.NAMA_BARANG + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_AWAL + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_AWAL + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_TERIMA + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_TERIMA + '</td>'+
					'	<td class="inp-numeric" '+class_pakan[obj.JENIS_KELAMIN]+'>' + obj.BRT_PAKAI + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_PAKAI + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_AKHIR + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_AKHIR + '</td>'+
					'</tr>';
					
					i_b++;
				}
			}
			
			var pakan_all = pakan_j.concat(pakan_b);
			$('#lhk_pakan > tbody').html(pakan_all.join(''));
			
			//rhk_obat
			var barang_obat = new Array(),
		    barang_vaksin = new Array(),
			i_o = 0,
			i_v = 0;
			
			for(var i=0;i<rhk_vaksin.length;i++){
				var obj = rhk_vaksin[i];
				if(obj.JENIS_BARANG == 'O'){
					barang_obat[i_o] = ''+
					'<tr>'+
					'	<td>' + obj.NAMA_BARANG + '</td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_obat_namabarang[]" disabled value="' + obj.KODE_BARANG + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaijantan[]" value="' + obj.J + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaibetina[]" value="' + obj.B + '"></td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_obat_keterangan[]" value=""></td>'+
					'	<td class="vert-align col-md-1"></td>'+
					'</tr>';
					
					i_o++;
				}
				else{
					barang_vaksin[i_v] = ''+
					'<tr>'+
					'	<td>' + obj.NAMA_BARANG + '</td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_obat_namabarang[]" disabled value="' + obj.KODE_BARANG + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaijantan[]" value="' + obj.J + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaibetina[]" value="' + obj.B + '"></td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_obat_keterangan[]" value=""></td>'+
					'	<td class="vert-align col-md-1"></td>'+
					'</tr>';
					
					i_v++;
				}
			}
			
			$('#lhk_obat > tbody').html(barang_obat.join(''));
			$('#lhk_vaksin > tbody').html(barang_vaksin.join(''));
			
			//rhk_produksi
			var produksi = new Array(),
			i_p = 0;
			
			for(var i=0;i<rhk_produksi.length;i++){
				var obj = rhk_produksi[i];
				
				produksi[i_p] = ''+
					'<tr>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_BAIK + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_BESAR + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_TIPIS + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_KECIL + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_KOTOR + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_ABNORMAL + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_IB + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_RETAK + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.PROD_HANCUR + '"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" value="' + obj.BERAT_TOTAL + '"></td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_prod_keterangan[]"></td>'+
					'	<td></td>'+
					'</tr>';
					
					i_p++;
			}
			
			$('#inp_berat_telur').val(																																																																																																																																																																																																																																																																																									);				
			$('#inp_cv_jantan').val(rhk.J_CV);				
			$('#inp_cv_betina').val(rhk.B_CV);
			$('#inp_uniformity_jantan').val(rhk.J_UNIFORMITY);
			$('#inp_uniformity_betina').val(rhk.B_UNIFORMITY);
			$('#inp_dayahidup_jantan').val(rhk.J_DAYA_HIDUP);
			$('#inp_dayahidup_betina').val(rhk.B_DAYA_HIDUP);
			
			var j_total = 0;
			var j_html = '';
			var j_total_sampling = 0;
			var j_target_berat = 0;
			
			var b_total = 0;
			var b_html = '';
			var b_total_sampling = 0;
			var b_target_berat = 0;
			for(var i=0;i<rhk_penimbangan.length;i++){
				var obj = rhk_penimbangan[i];
				if(obj.JENIS_KELAMIN == "J"){
					j_total_sampling += parseInt(obj.JUMLAH);
					j_target_berat = obj.TARGET_BB;
					
					var timbang_j_bb_temp = parseFloat(j_target_berat) - (0.1 * parseFloat(j_target_berat));
					var timbang_j_ba_temp = parseFloat(j_target_berat) + (0.1 * parseFloat(j_target_berat));
					
					var style = "";
			
					if(parseInt(obj.BERAT) >= timbang_j_bb_temp && parseInt(obj.BERAT) <= timbang_j_ba_temp){
						style = "style='color:#000;background-color:#FAFDFF'";
					}
					
					j_html += '<tr>';
					j_html += '<td align="center" '+style+'>'+obj.BERAT+'</td><td align="center" '+style+'>'+obj.JUMLAH+'</td>';
					j_html += '</tr>';
					
					j_total+=parseInt(obj.JUMLAH);
				}else{
					b_total_sampling += parseInt(obj.JUMLAH);
					b_target_berat = obj.TARGET_BB;
					
					var timbang_b_bb_temp = parseFloat(b_target_berat) - (0.1 * parseFloat(b_target_berat));
					var timbang_b_ba_temp = parseFloat(b_target_berat) + (0.1 * parseFloat(b_target_berat));
					
					var style = "";
					
					if(parseInt(obj.BERAT) >= timbang_b_bb_temp && parseInt(obj.BERAT) <= timbang_b_ba_temp){
						style = "style='color:#000;background-color:#FAFDFF'";
					}
					
					b_html += '<tr>';
					b_html += '<td align="center" '+style+'>'+obj.BERAT+'</td><td align="center" '+style+'>'+obj.JUMLAH+'</td>';
					b_html += '</tr>';
					
					b_total+=parseInt(obj.JUMLAH);
				}
			}
			
			var umur_arr = new Array();
			umur_arr = umur.split("+");
			umur = (parseInt(umur_arr[0]) * 7) + (parseInt(umur_arr[1]));
			
			
			$('#inp_uni_jk').val("Jantan");
			$('#inp_uni_umur').val(umur);
			$('#inp_uni_tberat').val(parseInt(j_target_berat));
			$('#inp_uni_tsampling').val(j_total);
			$('#inp_uni_uniformity').val(rhk.J_UNIFORMITY);
			if(parseFloat(rhk.J_UNIFORMITY) >= 85)
				$('#lbl_status_uniformity').html("NORMAL");
			else
				$('#lbl_status_uniformity').html("BURUK");
			$("#btnOKTimbang").hide();
			$('#preview_detail_penimbangan > tbody').html(j_html);
			
			$('#inp_uni_jk_betina').val("Betina");
			$('#inp_uni_umur_betina').val(umur);
			$('#inp_uni_tberat_betina').val(parseInt(b_target_berat));
			$('#inp_uni_tsampling_betina').val(b_total);
			$('#inp_uni_uniformity_betina').val(rhk.B_UNIFORMITY);
			if(parseFloat(rhk.B_UNIFORMITY) >= 85)
				$('#lbl_status_uniformity_betina').html("NORMAL");
			else
				$('#lbl_status_uniformity_betina').html("BURUK");
			$("#btnOKTimbang_betina").hide();
			$('#preview_detail_penimbangan_betina > tbody').html(b_html);
			
			disabledLhk();
		}else{
			//Tidak ada LHK pada tanggal ini
			resetInputLhk();
			disabledLhk();
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
		$('#btnBrowseKurangBetina').removeClass('disabled');
		$('#btnBrowseKurangJantan').removeClass('disabled');
	});
}

function tambahProduksi(elm){
	html_btn = ''+
		'<button type="button" data-toggle="tooltip" onclick="hapusRow(this)" title="Hapus Produksi" class="btn btn-sm btn-primary">'+
		'	<i class="glyphicon glyphicon-minus-sign"></i>'+
		'</button>';
	
	html = ''+
		'<tr>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_baik[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_besar[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_tipis[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_kecil[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_kotor[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_abnormal[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_ib[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_retak[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_hancur[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_jumlah[]" disabled></td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_prod_keterangan[]"></td>'+
		'	<td>'+
		'	<button type="button" data-toggle="tooltip" onclick="tambahProduksi(this)" title="Tambah" class="btn btn-sm btn-primary">'+
		'		<i class="glyphicon glyphicon-plus-sign"></i>'+
		'	</button>'+
		'	</td>'+
		'</tr>';
		
	var td = $(elm).parent();
	td.html(html_btn);
	
	$(html).appendTo("#lhk_produksi > tbody");
}

function tambahObat(elm){
	html_btn = ''+
		'<button type="button" data-toggle="tooltip" onclick="hapusRow(this)" title="Hapus" class="btn btn-sm btn-primary">'+
		'	<i class="glyphicon glyphicon-minus-sign"></i>'+
		'</button>';
	
	var select_obat = '<select class="form-control input-sm"  name="inp_obat_kodebarang[]" onchange="setNamaBarang(this)">';
	select_obat += '<option value=""></option>';
	for(var i=0;i<barang_obat.length;i++){
		var obj = barang_obat[i];		
		select_obat += '<option value="' + obj[0] + '">' + obj[1] + '</option>';
	}
	select_obat += '</select>';
	
	html = ''+
	'<tr>'+
	'	<td>' + select_obat + '</td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_obat_namabarang[]" disabled></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_obat_keterangan[]"></td>'+
	'	<td class="vert-align col-md-1">'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahObat(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'			<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
			
	var td = $(elm).parent();
	td.html(html_btn);
	
	$(html).appendTo("#lhk_obat > tbody");
}

function tambahVaksin(elm){
	html_btn = ''+
		'<button type="button" data-toggle="tooltip" onclick="hapusRow(this)" title="Hapus" class="btn btn-sm btn-primary">'+
		'	<i class="glyphicon glyphicon-minus-sign"></i>'+
		'</button>';
	
	var select_vaksin = '<select class="form-control input-sm"  name="inp_vaksin_kodebarang[]" onchange="setNamaBarang(this)">';
	select_vaksin += '<option value=""></option>';
	for(var i=0;i<barang_vaksin.length;i++){
		var obj = barang_vaksin[i];		
		select_vaksin += '<option value="' + obj[0] + '">' + obj[1] + '</option>';
	}
	select_vaksin += '</select>';
	
	html = ''+
	'<tr>'+
	'	<td>' + select_vaksin + '</td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_namabarang[]" disabled></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_keterangan[]"></td>'+
	'	<td class="vert-align col-md-1">'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahVaksin(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'			<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
			
	var td = $(elm).parent();
	td.html(html_btn);
	
	$(html).appendTo("#lhk_vaksin > tbody");
}

function hapusRow(elm){
	var td = $(elm).parent().parent();
	td.remove();
}

function daydiff(first, second) {
    return Math.floor((second-first)/(1000*60*60*24));
}

function cekTujuanPindahPakan(elm){
	// var tujuanPindahPakan = new Array();
	
	// var i = 0;
	// $('select[name^="inp_kandang_tujuan"]').each(function() {
		// if(!empty($(this).val())){
			// tujuanPindahPakan[i] = $(this).val();
			
			// i++;
		// }
	// });
	
	// var similar = 0;
	// for(var k=0;k<tujuanPindahPakan.length;k++){
		// if(tujuanPindahPakan[k] == $(elm).val()){
			// similar++;
		// }
	// }
	
	// if(similar > 1){
		// $(elm).val("");
	// }
}

function showModalPengurangan(param){
	
	if(kandang_id_arr.length < 1){
		var select = new Array();
		for(var i=0;i<kandang_in_farm.length;i++){
			var obj = kandang_in_farm[i];
			if(obj[0] != selected_kandang){
				select [i] = '<option value="' + obj[2] + '">' + obj[1] + '</option>'; 
			}
		}
		
		html =  '<tr>'+
			    '	<td>'+
				'		<select class="form-control input-sm" name="inp_kandang_tujuan[]" onfocus="startPilihKandangPindah(this)">'+
				'		</select>'+
				'	</td>'+
				'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_kurang_ayam_j[]" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)"></td>'+
				'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_kurang_ayam_b[]" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)"></td>'+
				'	<td><input type="text" class="form-control input-sm" name="inp_kurang_ayam_k[]"></td>'+
				'	<td><input type="text" class="form-control input-sm" name="inp_kurang_ayam_memo[]"></td>'+
				'	<td>'+
				'	<button type="button" data-toggle="tooltip" onclick="tambahPenguranganAyam(this)" title="Tambah" class="btn btn-sm btn-primary">'+
				'		<i class="glyphicon glyphicon-plus-sign"></i>'+
				'	</button>'+
				'	</td>'+
				'</tr>';
		
		$('#md_pengurangan_ayam > tbody').html(html);		
	}else{
		var html = new Array();
		for(var j=0;j<kandang_id_arr.length;j++){
			var select = new Array();
			var selected = '';
			
			var selected_option = new Array();
			var i = 0;
			$('select[name^="inp_kandang_tujuan"]').each(function() {
				if(!empty($(this).val())){
					selected_option[i] = $(this).val();
					i++;
				}
			});
			
			for(var i=0;i<kandang_in_farm.length;i++){
				var obj = kandang_in_farm[i];
				if(obj[0] != selected_kandang){
					if(obj[2] == kandang_id_arr[j]){
						selected = 'selected';
						select [i] = '<option value="' + obj[2] + '"' + selected + '>' + obj[1] + '</option>'; 	
					}else{
						if(jQuery.inArray(obj[2], selected_option)){
							selected = '';
							
							select [i] = '<option value="' + obj[2] + '"' + selected + '>' + obj[1] + '</option>'; 
						}
					}	
				}
			}
						
			html[j] =  '<tr>'+
					'	<td>'+
					'		<select class="form-control input-sm" name="inp_kandang_tujuan[]" onfocus="startPilihKandangPindah(this)">'+
					'		<option></option>'+select.join('')+
					'		</select>'+
					'	</td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_kurang_ayam_j[]" value="' + jantan_arr[j] + '" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_kurang_ayam_b[]" value="' + betina_arr[j] + '" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_kurang_ayam_k[]" value="' + keterangan_arr[j] + '"></td>'+
					'	<td><input type="text" class="form-control input-sm" name="inp_kurang_ayam_memo[]" value="' + beritaacara_arr[j] + '""></td>'+
					'	<td>'+
					'	<button type="button" data-toggle="tooltip" onclick="tambahPenguranganAyam(this)" title="Tambah" class="btn btn-sm btn-primary">'+
					'		<i class="glyphicon glyphicon-plus-sign"></i>'+
					'	</button>'+
					'	</td>'+
					'</tr>';
		}
		
		$('#md_pengurangan_ayam > tbody').html(html.join(''));
	}
	
	$('#modal_pengurangan_ayam').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#modal_pengurangan_ayam').modal("show");
}

$('#btnBatalPengurangan').click(function(){
	$('#modal_pengurangan_ayam').modal("hide");
});

$('#btnSimpanPengurangan').click(function(){
	var valid = true;
	var i = 0;
	var i_empty = null;
	var rMsg = new Array();
	
	kandang_id_arr = new Array();
	jantan_arr = new Array();
	betina_arr = new Array();
	keterangan_arr = new Array();
	beritaacara_arr = new Array();
	
	$('select[name^="inp_kandang_tujuan"]').each(function() {
		if(empty($(this).val()))
			i_empty = i;
		else
			kandang_id_arr.push($(this).val());
		
		i++;
	});
		
	i = 0;
	$('input[name^="inp_kurang_ayam_j"]').each(function() {
		var jml = !empty($(this).val()) ? ($(this).val()).trim() : '0';
		
		if(i != i_empty)
			jantan_arr.push(parseInt(jml));
		
		i++;
	});
	
	i = 0;
	$('input[name^="inp_kurang_ayam_b"]').each(function() {
		var jml = !empty($(this).val()) ? ($(this).val()).trim() : '0';
		
		if(i != i_empty)
			betina_arr.push(parseInt(jml));
		
		i++;
	});
	
	var jml_j = 0;
	for(var ij=0;ij<jantan_arr.length;ij++){
		if(!empty(jantan_arr[ij]))
			jml_j += parseInt(jantan_arr[ij]);
	}
	
	var jml_b = 0;
	for(var ib=0;ib<betina_arr.length;ib++){
		if(!empty(betina_arr[ib]))
			jml_b += parseInt(betina_arr[ib]);
	}
	
	//Notifikasi ini menggantikan notifikasi di bawah
	if(parseInt(jml_j) <= 0 && parseInt(jml_b) <= 0){
		rMsg[(rMsg.length) + 1] = "Jumlah pindah Jantan atau Betina harus lebih besar 0";
	}
	
	// if(parseInt(jml_j) <= 0){
		// rMsg[(rMsg.length) + 1] = "Jumlah pindah Jantan harus lebih besar 0";
	// }
	// if(parseInt(jml_b) <= 0){
		// rMsg[(rMsg.length) + 1] = "Jumlah pindah Betina harus lebih besar 0";
	// }
	
	i = 0;
	$('input[name^="inp_kurang_ayam_k"]').each(function() {
		if(i != i_empty){
			if(empty($(this).val())){
				rMsg[(rMsg.length) + 1] = "Kolom 'Keterangan' harus di isi";
			}else{
				keterangan_arr.push($(this).val());
			}
		}
		
		i++;
	});
	
	i = 0;
	$('input[name^="inp_kurang_ayam_memo"]').each(function() {
		if(i != i_empty){
			if(empty($(this).val())){
				rMsg[(rMsg.length) + 1] = "Kolom 'Berita Acara' harus di isi";
			}else{
				beritaacara_arr.push($(this).val());
			}
		}
		i++;
	});
	
	var rKuota = true;
	
	if((parseInt($('#inp_populasiAkhirJantan').val())-parseInt(jml_j)) < 0){
		rMsg[(rMsg.length) + 1] = "Jumlah pindah Jantan melebihi populasi awal";
		  
		rKuota = false;
	}
	
	if((parseInt($('#inp_populasiAkhirBetina').val())-parseInt(jml_b)) < 0){
		rMsg[(rMsg.length) + 1] = "Jumlah pindah Betina melebihi populasi awal";
		
		rKuota = false;
	}
	
	var newRMsg = cleanArray(rMsg);
	
	if(newRMsg.length > 0){
		$('#pindahKandangErrMsgKet').html(newRMsg.join('<br>'));
		$('#pindahKandangErrMsgKet').show();
		valid = false;
	}else{
		$('#pindahKandangErrMsgKet').html("");
		$('#pindahKandangErrMsgKet').hide();
	}

	
	if(!valid){
		return false;
	}
	
	$('#inp_kurangJantan').val(jml_j);
	$('#inp_kurangBetina').val(jml_b);
	
	
	//Populasi JANTAN
	var j1 = $('#inp_populasiAwalJantan').val();
	var j2 = $('#inp_tambahJantan').val();
	var j3 = $('#inp_tambahJantanLain').val();
	var j4 = $('#inp_kurangJantanMati').val();
	var j5 = $('#inp_kurangJantanAfkir').val();
	var j6 = $('#inp_kurangJantan').val();
	var j7 = $('#inp_kurangJantanSexslip').val();
	var j8 = $('#inp_kurangJantanKanibal').val();
	var j9 = $('#inp_kurangJantanCampur').val();
	var j10 = $('#inp_kurangJantanSeleksi').val();
	var j11 = $('#inp_kurangJantanLain').val();
	
	j1 = (!empty(j1)) ? parseInt(j1) : 0;
	j2 = (!empty(j2)) ? parseInt(j2) : 0;
	j3 = (!empty(j3)) ? parseInt(j3) : 0;
	j4 = (!empty(j4)) ? parseInt(j4) : 0;
	j5 = (!empty(j5)) ? parseInt(j5) : 0;
	j6 = (!empty(j6)) ? parseInt(j6) : 0;
	j7 = (!empty(j7)) ? parseInt(j7) : 0;
	j8 = (!empty(j8)) ? parseInt(j8) : 0;
	j9 = (!empty(j9)) ? parseInt(j9) : 0;
	j10 = (!empty(j10)) ? parseInt(j10) : 0;
	j11 = (!empty(j11)) ? parseInt(j11) : 0;
	
	var j_jml = j1+j2+j3-j4-j5-j6-j7-j8-j9-j10-j11;
	
	//Populasi BETINA
	var b1 = $('#inp_populasiAwalBetina').val();
	var b2 = $('#inp_tambahBetina').val();
	var b3 = $('#inp_tambahBetinaLain').val();
	var b4 = $('#inp_kurangBetinaMati').val();
	var b5 = $('#inp_kurangBetinaAfkir').val();
	var b6 = $('#inp_kurangBetina').val();
	var b7 = $('#inp_kurangBetinaSexslip').val();
	var b8 = $('#inp_kurangBetinaKanibal').val();
	var b9 = $('#inp_kurangBetinaCampur').val();
	var b10 = $('#inp_kurangBetinaSeleksi').val();
	var b11 = $('#inp_kurangBetinaLain').val();
	
	b1 = (!empty(b1)) ? parseInt(b1) : 0;
	b2 = (!empty(b2)) ? parseInt(b2) : 0;
	b3 = (!empty(b3)) ? parseInt(b3) : 0;
	b4 = (!empty(b4)) ? parseInt(b4) : 0;
	b5 = (!empty(b5)) ? parseInt(b5) : 0;
	b6 = (!empty(b6)) ? parseInt(b6) : 0;
	b7 = (!empty(b7)) ? parseInt(b7) : 0;
	b8 = (!empty(b8)) ? parseInt(b8) : 0;
	b9 = (!empty(b9)) ? parseInt(b9) : 0;
	b10 = (!empty(b10)) ? parseInt(b10) : 0;
	b11 = (!empty(b11)) ? parseInt(b11) : 0;
	
	var b_jml = b1+b2+b3-b4-b5-b6-b7-b8-b9-b10-b11;
	
	
	$('#inp_populasiAkhirJantan').val(j_jml);
	$('#inp_populasiAkhirBetina').val(b_jml);
	
	$('#modal_pengurangan_ayam').modal("hide");
	
	count_rasio();
});

function showModalPenambahanLain(param){
	if(tambah_jantan_lain_arr.length < 1){
		
	}else{
		
	}
	
	$('#modal_penambahan_ayam_lain').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#modal_penambahan_ayam_lain').modal("show");
}

function showModalPenguranganLain(param){
	if(kurang_jantan_lain_arr.length < 1){
		
	}else{
		
	}
	
	$('#modal_pengurangan_ayam_lain').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#modal_pengurangan_ayam_lain').modal("show");
}

function checkBatasJantanLain(elm){
	var min = 0,
	    max = 4;
	
	if(elm.value < min)
		elm.value = min;
	
	if(elm.value > max)
		elm.value = max;
};

function checkBatasBetinaLain(elm){
	var min = 0,
	    max = 6;
	
	if(elm.value < min)
		elm.value = min;
	
	if(elm.value > max)
		elm.value = max;
};

$(document).on('change', '.btn-file :file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
	input.trigger('fileselect', [numFiles, label, input]);
});

$('.btn-file :file').on('fileselect', function(event, numFiles, label, elm) {
	var elm_input = $(elm).parent().parent().parent().find('input')[1];
	$(elm_input).val(label);
});

$('#btnBatalPenambahanLain').click(function(){
	$('#modal_penambahan_ayam_lain').modal("hide");
	if(!valid_penambahan_lain){		
		$('input[name^="slider_tambah_lain_jantan"]').each(function() {
			$(this).val('');
		});
		
		$('input[name^="slider_tambah_lain_betina"]').each(function() {
			$(this).val('');
		});
		
		$('input[name^="inp_tambah_jantan_lain_ket"]').each(function() {		
			$(this).val('');
		});
		
		$('input[name^="inp_tambah_jantan_lain_nomemo"]').each(function() {		
			$(this).val('');
		});
		
		$('input[name^=uploadFileTambahLain]').each(function(){
			var elm_input = $(this).parent().parent().parent().find('input')[1];
			$(elm_input).val('');				
		});
		
		$('#tambahLainErrMsgKet').html("");
		$('#tambahLainErrMsgKet').hide();
	}
});

$('#btnBatalPenguranganLain').click(function(){
	$('#modal_pengurangan_ayam_lain').modal("hide");
	if(!valid_pengurangan_lain){
		
		$('input[name^="slider_kurang_lain_jantan"]').each(function() {
			$(this).val('');
		});
		
		$('input[name^="slider_kurang_lain_betina"]').each(function() {
			$(this).val('');
		});
		
		$('input[name^="inp_kurang_jantan_lain_ket"]').each(function() {		
			$(this).val('');
		});
		
		$('input[name^="inp_kurang_jantan_lain_nomemo"]').each(function() {		
			$(this).val('');
		});
		
		$('input[name^=uploadFileKurangLain]').each(function(){
			var elm_input = $(this).parent().parent().parent().find('input')[1];
			$(elm_input).val('');				
		});
		
		$('#kurangLainErrMsgKet').html("");
		$('#kurangLainErrMsgKet').hide();
	}
});

$('#btnSimpanPenambahanLain').click(function(){
	valid_penambahan_lain = true;
	var rMsg = new Array();
	var tambah_jantan_jml = 0;
	var tambah_betina_jml = 0;
	
	tambah_jantan_lain_arr = new Array();
	tambah_betina_lain_arr = new Array();
	tambah_keterangan_lain_arr = new Array();
	tambah_noberitaacara_lain_arr = new Array();
	tambah_beritaacara_lain_arr = new Array();
	
	$('input[name^="slider_tambah_lain_jantan"]').each(function() {
		var jml_j = !empty($(this).val()) ? ($(this).val()).trim() : '0';
		
		tambah_jantan_lain_arr.push(parseInt(jml_j));
	});
	
	$('input[name^="slider_tambah_lain_betina"]').each(function() {
		var jml_b = !empty($(this).val()) ? ($(this).val()).trim() : '0';
		
		tambah_betina_lain_arr.push(parseInt(jml_b));
	});
	
	$('input[name^="inp_tambah_jantan_lain_ket"]').each(function() {		
		tambah_keterangan_lain_arr.push($(this).val());
	});
	
	$('input[name^="inp_tambah_jantan_lain_nomemo"]').each(function() {		
		tambah_noberitaacara_lain_arr.push($(this).val());
	});
	
	$('input[name^=uploadFileTambahLain]').each(function(){
		var elm_input = $(this).parent().parent().parent().find('input')[1];
		if(!empty($(elm_input).val())){
			tambah_beritaacara_lain_arr.push($(elm_input).val());
			
			var f = $(this).get(0).files[0].name;
			var f_arr = f.toLowerCase().split('.');
			var f_type = f_arr[f_arr.length-1]; 
			
			if(f_type != "jpg" && f_type != "pdf" && f_type != "doc"){
				console.log("Not allowed filtype!");
			}
		}
			
	});
	
	for(var i=0;i<tambah_jantan_lain_arr.length;i++){
		var jml_j = parseInt(tambah_jantan_lain_arr[i]);
		var jml_b = parseInt(tambah_betina_lain_arr[i]);
		var no_berita = tambah_noberitaacara_lain_arr[i];
		var isi_berita = tambah_beritaacara_lain_arr[i];
		
		tambah_jantan_jml = tambah_jantan_jml + jml_j;
		tambah_betina_jml = tambah_betina_jml + jml_b;
		
		if((jml_j > 0 || jml_b > 0) && (empty(no_berita) || empty(isi_berita))){
			if(empty(no_berita))
				rMsg.push("Kolom 'No. Berita Acara' harus di isi");
			
			if(empty(isi_berita))
				rMsg.push("Kolom 'Lampiran Berita Acara' harus di isi");
			
			valid_penambahan_lain = false;
		}
	}
	
	if(rMsg.length > 0){
		$('#tambahLainErrMsgKet').html(rMsg.join('<br>'));
		$('#tambahLainErrMsgKet').show();
		valid = false;
	}else{
		$('#tambahLainErrMsgKet').html("");
		$('#tambahLainErrMsgKet').hide();
	}
	
	if(!valid_penambahan_lain){
		return false;
	}
	
	$('#inp_tambahJantanLain').val(tambah_jantan_jml);
	$('#inp_tambahBetinaLain').val(tambah_betina_jml);
	
	$('#modal_penambahan_ayam_lain').modal("hide");
});

$('#btnSimpanPenguranganLain').click(function(){
	valid_pengurangan_lain = true;
	var rMsg = new Array();
	var kurang_jantan_jml = 0;
	var kurang_betina_jml = 0;
	
	kurang_jantan_lain_arr = new Array();
	kurang_betina_lain_arr = new Array();
	kurang_keterangan_lain_arr = new Array();
	kurang_noberitaacara_lain_arr = new Array();
	kurang_beritaacara_lain_arr = new Array();
	
	$('input[name^="slider_kurang_lain_jantan"]').each(function() {
		var jml_j = !empty($(this).val()) ? ($(this).val()).trim() : '0';
		
		kurang_jantan_lain_arr.push(parseInt(jml_j));
	});
	
	$('input[name^="slider_kurang_lain_betina"]').each(function() {
		var jml_b = !empty($(this).val()) ? ($(this).val()).trim() : '0';
		
		kurang_betina_lain_arr.push(parseInt(jml_b));
	});
	
	$('input[name^="inp_kurang_jantan_lain_ket"]').each(function() {		
		kurang_keterangan_lain_arr.push($(this).val());
	});
	
	$('input[name^="inp_kurang_jantan_lain_nomemo"]').each(function() {		
		kurang_noberitaacara_lain_arr.push($(this).val());
	});
	
	$('input[name^=uploadFileKurangLain]').each(function(){
		var elm_input = $(this).parent().parent().parent().find('input')[1];
		if(!empty($(elm_input).val())){
			kurang_beritaacara_lain_arr.push($(elm_input).val());
			
			var f = $(this).get(0).files[0].name;
			var f_arr = f.toLowerCase().split('.');
			var f_type = f_arr[f_arr.length-1]; 
			
			if(f_type != "jpg" && f_type != "pdf" && f_type != "doc"){
				console.log("Not allowed filtype!");
			}
		}
			
	});
	
	for(var i=0;i<kurang_jantan_lain_arr.length;i++){
		var jml_j = parseInt(kurang_jantan_lain_arr[i]);
		var jml_b = parseInt(kurang_betina_lain_arr[i]);
		var no_berita = kurang_noberitaacara_lain_arr[i];
		var isi_berita = kurang_beritaacara_lain_arr[i];
		
		kurang_jantan_jml = kurang_jantan_jml + jml_j;
		kurang_betina_jml = kurang_betina_jml + jml_b;
		
		if((jml_j > 0 || jml_b > 0) && (empty(no_berita) || empty(isi_berita))){
			if(empty(no_berita))
				rMsg.push("Kolom 'No. Berita Acara' harus di isi");
			
			if(empty(isi_berita))
				rMsg.push("Kolom 'Lampiran Berita Acara' harus di isi");
			
			valid_pengurangan_lain = false;
		}
	}
	
	if(rMsg.length > 0){
		$('#kurangLainErrMsgKet').html(rMsg.join('<br>'));
		$('#kurangLainErrMsgKet').show();
		valid = false;
	}else{
		$('#kurangLainErrMsgKet').html("");
		$('#kurangLainErrMsgKet').hide();
	}
	
	if(!valid_pengurangan_lain){
		return false;
	}
	
	$('#inp_kurangJantanLain').val(kurang_jantan_jml);
	$('#inp_kurangBetinaLain').val(kurang_betina_jml);
	
	$('#modal_pengurangan_ayam_lain').modal("hide");
});


$('#btnBrowseUniformityJantan').click(function(){
	if(lhk_state == "WRITE"){
		timbang_j_arr_bb = new Array();
		var farm = $("#inp_nama_farm").val();
		var umur = $("#inp_umur").val();
		var umur_arr = new Array();
		umur_arr = umur.split("+");
		umur = (parseInt(umur_arr[0]) * 7) + (parseInt(umur_arr[1]));
		var j_kelamin = "Jantan";
		
		$('#inp_uni_jk').val(j_kelamin);
		$('#inp_uni_umur').val(umur);
		$('#inp_uni_tberat').val(parseInt(target_bb_j_minggu));
		
		var arr_bb = new Array();
		var batas_bawah_tampilan = parseFloat(target_bb_j_minggu) - (0.2 * parseFloat(target_bb_j_minggu));
		var batas_atas_tampilan = parseFloat(target_bb_j_minggu) + (0.2 * parseFloat(target_bb_j_minggu));
		
		timbang_j_bb = parseFloat(target_bb_j_minggu) - (0.1 * parseFloat(target_bb_j_minggu));
		timbang_j_ba = parseFloat(target_bb_j_minggu) + (0.1 * parseFloat(target_bb_j_minggu));
		
		var inc_bb_tamp = (parseFloat(target_bb_j_minggu)-parseFloat(batas_bawah_tampilan))/10;
		var inc_ba_tamp = (parseFloat(batas_atas_tampilan)-parseFloat(target_bb_j_minggu))/10;
		
		timbang_j_arr_bb.push(batas_bawah_tampilan);
		for(var i=(batas_bawah_tampilan+inc_bb_tamp);i<target_bb_j_minggu;i+=inc_bb_tamp){
			timbang_j_arr_bb.push(Math.ceil(i));
		}
		
		for(var i=(parseFloat(target_bb_j_minggu)+inc_ba_tamp);i<batas_atas_tampilan;i+=inc_ba_tamp){
			timbang_j_arr_bb.push(Math.ceil(i));
		}
		timbang_j_arr_bb.push(batas_atas_tampilan);
		
		timbang_j_arr_bb = timbang_j_arr_bb.removeDuplicates();
	
		var html = '';
		
		for(var i=0;i<timbang_j_arr_bb.length;i++){
			var style = "";
			
			if(parseInt(timbang_j_arr_bb[i]) >= timbang_j_bb && parseInt(timbang_j_arr_bb[i]) <= timbang_j_ba){
				style = "style='color:#000;background-color:#DCF0FA'";
			}
			
			var jml = (!timbang_j_arr_jml[i]) ? "0" : timbang_j_arr_jml[i];
			html += '<tr>';
			html += '<td align="center" '+style+'>'+timbang_j_arr_bb[i]+'</td><td align="center" '+style+'><input style="width:60px;text-align:center" type="text" onkeyup="cekNumerikUniform(this)"  class="form-control" name="j_timbang_arr[]" value="'+jml+'"></td>';
			html += '</tr>';
		}
		
		$('#inp_uni_tsampling').val(0);
		$("#btnOKTimbang").show();
		$('#preview_detail_penimbangan > tbody').html(html);
	}
	$('#modal_uniformity').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#modal_uniformity').modal('show');
});

$("#btnOKTimbang").click(function(){
	// var uniformity = $('#inp_uni_uniformity').val();
	// uniformity = (uniformity.trim() == "") ? 0 : uniformity;
	// $("#inp_uniformity_jantan").val(uniformity);
	
	// $('#modal_uniformity').modal('hide');
	timbang_j_arr_jml = new Array();
	var total_sampling = 0;
	
	$('input[name^="j_timbang_arr"]').each(function() {		
		var jml = (($(this).val()).trim() == '') ? 0 : $(this).val();
		timbang_j_arr_jml.push(jml);
	});
		
	var jml_sample_ayam = 0;
	
	for(var i=0;i<timbang_j_arr_jml.length;i++){
		if(timbang_j_arr_bb[i] >= timbang_j_bb && timbang_j_arr_bb[i] <= timbang_j_ba){
			jml_sample_ayam += parseInt(timbang_j_arr_jml[i]);
		}
		
		total_sampling += parseInt(timbang_j_arr_jml[i]);
	}
	
	if(total_sampling <= 0){
		bootbox.alert("Jumlah sampling ayam tidak boleh nol (0)");
		
		return false;
	}
	
	var status = jml_sample_ayam/total_sampling*100;
	$('#inp_uni_uniformity').val(status);
	if(status >= 85)
		$('#lbl_status_uniformity').html("NORMAL");
	else
		$('#lbl_status_uniformity').html("BURUK");
	
	bootbox.alert("Proses penimbangan sampling telah selesai");
});

$('#modal_uniformity').on('hidden.bs.modal', function () {
	var uniformity = $('#inp_uni_uniformity').val();
	uniformity = (uniformity.trim() == "") ? 0 : uniformity;
	$("#inp_uniformity_jantan").val(uniformity);
})


$('#btnBrowseUniformityBetina').click(function(){
	if(lhk_state == "WRITE"){	
		timbang_b_arr_bb = new Array();
		var farm = $("#inp_nama_farm").val();
		var umur = $("#inp_umur").val();
		var umur_arr = new Array();
		umur_arr = umur.split("+");
		umur = (parseInt(umur_arr[0]) * 7) + (parseInt(umur_arr[1]));
		var j_kelamin = "Betina";
		
		$('#inp_uni_jk_betina').val(j_kelamin);
		$('#inp_uni_umur_betina').val(umur);
		$('#inp_uni_tberat_betina').val(parseInt(target_bb_b_minggu));
		
		var arr_bb = new Array();
		var batas_bawah_tampilan = parseFloat(target_bb_b_minggu) - (0.2 * parseFloat(target_bb_b_minggu));
		var batas_atas_tampilan = parseFloat(target_bb_b_minggu) + (0.2 * parseFloat(target_bb_b_minggu));
		
		timbang_b_bb = parseFloat(target_bb_b_minggu) - (0.1 * parseFloat(target_bb_b_minggu));
		timbang_b_ba = parseFloat(target_bb_b_minggu) + (0.1 * parseFloat(target_bb_b_minggu));
		
		var inc_bb_tamp = (parseFloat(target_bb_b_minggu)-parseFloat(batas_bawah_tampilan))/10;
		var inc_ba_tamp = (parseFloat(batas_atas_tampilan)-parseFloat(target_bb_b_minggu))/10;
		
		timbang_b_arr_bb.push(batas_bawah_tampilan);
		for(var i=(batas_bawah_tampilan+inc_bb_tamp);i<target_bb_b_minggu;i+=inc_bb_tamp){
			timbang_b_arr_bb.push(Math.ceil(i));
		}
		
		for(var i=(parseFloat(target_bb_b_minggu)+inc_ba_tamp);i<batas_atas_tampilan;i+=inc_ba_tamp){
			timbang_b_arr_bb.push(Math.ceil(i));
		}
		timbang_b_arr_bb.push(batas_atas_tampilan);
		
		timbang_b_arr_bb = timbang_b_arr_bb.removeDuplicates();
		
		var html = '';
		
		for(var i=0;i<timbang_b_arr_bb.length;i++){
			var style = "";
			
			if(parseInt(timbang_b_arr_bb[i]) >= timbang_b_bb && parseInt(timbang_b_arr_bb[i]) <= timbang_b_ba){
				style = "style='color:#000;background-color:#DCF0FA'";
			}
			
			var jml = (!timbang_b_arr_jml[i]) ? "0" : timbang_b_arr_jml[i];
			html += '<tr>';
			html += '<td align="center" '+style+'>'+timbang_b_arr_bb[i]+'</td><td align="center" '+style+'><input style="width:60px;text-align:center" type="text" onkeyup="cekNumerikUniform(this)" class="form-control" name="j_timbang_arr_betina[]" value="'+jml+'"></td>';
			html += '</tr>';
		}
		
		$('#inp_uni_tsampling_betina').val(0);
		$("#btnOKTimbang_betina").show();
		$('#preview_detail_penimbangan_betina > tbody').html(html);
	}
	$('#modal_uniformity_betina').modal({
		backdrop: 'static',
		keyboard: false
	});
	$('#modal_uniformity_betina').modal('show');
});

$("#btnOKTimbang_betina").click(function(){
	// var uniformity = $('#inp_uni_uniformity_betina').val();
	// uniformity = (uniformity.trim() == "") ? 0 : uniformity;
	// $("#inp_uniformity_betina").val(uniformity);
	
	// $('#modal_uniformity_betina').modal('hide');
	timbang_b_arr_jml = new Array();
	var total_sampling = 0;
	
	$('input[name^="j_timbang_arr_betina"]').each(function() {
		var jml = (($(this).val()).trim() == '') ? 0 : $(this).val();
		timbang_b_arr_jml.push(jml);
	});
		
	var jml_sample_ayam = 0;
	
	for(var i=0;i<timbang_b_arr_jml.length;i++){
		if(timbang_b_arr_bb[i] >= timbang_b_bb && timbang_b_arr_bb[i] <= timbang_b_ba){
			jml_sample_ayam += parseInt(timbang_b_arr_jml[i]);
		}
		
		total_sampling += parseInt(timbang_b_arr_jml[i]);
	}
	
	if(total_sampling <= 0){
		bootbox.alert("Jumlah sampling ayam tidak boleh nol (0)");
		
		return false;
	}
	
	var status = jml_sample_ayam/total_sampling*100;
	$('#inp_uni_uniformity_betina').val(status);
	if(status >= 85)
		$('#lbl_status_uniformity_betina').html("NORMAL");
	else
		$('#lbl_status_uniformity_betina').html("BURUK");
	
	bootbox.alert("Proses penimbangan sampling telah selesai");
});

$('#modal_uniformity_betina').on('hidden.bs.modal', function () {
	var uniformity = $('#inp_uni_uniformity_betina').val();
	uniformity = (uniformity.trim() == "") ? 0 : uniformity;
	$("#inp_uniformity_betina").val(uniformity);
})

Array.prototype.removeDuplicates = function (){
	var temp=new Array();
	this.sort();
	for(i=0;i<this.length;i++){
		if(this[i]==this[i+1]) {continue}
		temp[temp.length]=this[i];
	}
	return temp;
} 

// $('#modal_uniformity').on('show.bs.modal', function () {
// $('.modal-content').css('height',$( window ).height()*0.9);
// });

$('#btnTestLoad').click(function(){
	var win = window.open('riwayat_harian_kandang/test_load', '_blank');
	win.focus();
});

function simpanPenambahanLain2(){
	if(valid_penambahan_lain){
		var formData = new FormData();
		
		var pad = '00';
		var noreg = selected_noreg;
		var tglLhk = $('#inp_tgl_lhk').val();
		var tglLhk_arr = tglLhk.split(' ');
		var ddLhk = parseInt(tglLhk_arr[0]);
		var mmLhk = (months.indexOf(tglLhk_arr[1]) >= 0) ? (months.indexOf(tglLhk_arr[1]) + 1) : (months_id.indexOf(tglLhk_arr[1])+1);
		var yyLhk = parseInt(tglLhk_arr[2]);
		var tglLhk_fix = yyLhk + '-' + (pad + mmLhk).slice(-pad.length) + '-' + (pad + ddLhk).slice(-pad.length);
		
		formData.append('no_reg', noreg);
		formData.append('tgl_transaksi', tglLhk_fix);
		
		$('input[name^="slider_tambah_lain_jantan"]').each(function() {
			var jml_j = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			formData.append('tambah_lain_jml_j[]', parseInt(jml_j));
		});
		
		$('input[name^="slider_tambah_lain_betina"]').each(function() {
			var jml_b = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			formData.append('tambah_lain_jml_b[]', parseInt(jml_b));
		});
		
		$('input[name^="inp_tambah_jantan_lain_ket"]').each(function() {		
			formData.append('tambah_lain_ket[]', $(this).val());
		});
		
		$('input[name^="inp_tambah_jantan_lain_nomemo"]').each(function() {		
			formData.append('tambah_lain_nomemo[]', $(this).val());
		});
		
		var count_attachment = 0;
		$('input[name^=uploadFileTambahLain]').each(function(){
			var elm_input = $(this).parent().parent().parent().find('input')[1];
			if(!empty($(elm_input).val())){
				var file = $(this).get(0).files[0];
				
				formData.append('uploadFileTambahLain[]', file, file.name);
				count_attachment++;
			}
				
		});
		
		if(count_attachment > 0){
			$.ajax({
				type:'POST',
				url : "riwayat_harian_kandang/tambahLain2",
				data: formData,
				async : false,
				cache : false,
				contentType : false,
				processData : false
			})
			.done(function(html){
				
			})
			.fail(function(reason){
				console.info(reason);
			})
			.then(function(data){
			});
			
		}
	}else{
		console.log("WARNING!!! Ada kesalahan di fitur penambahan lain-lain.");
	}
}

function simpanPenguranganLain2(){
	if(valid_pengurangan_lain){
		var formData = new FormData();
		
		var pad = '00';
		var noreg = selected_noreg;
		var tglLhk = $('#inp_tgl_lhk').val();
		var tglLhk_arr = tglLhk.split(' ');
		var ddLhk = parseInt(tglLhk_arr[0]);
		var mmLhk = (months.indexOf(tglLhk_arr[1]) >= 0) ? (months.indexOf(tglLhk_arr[1]) + 1) : (months_id.indexOf(tglLhk_arr[1])+1);
		var yyLhk = parseInt(tglLhk_arr[2]);
		var tglLhk_fix = yyLhk + '-' + (pad + mmLhk).slice(-pad.length) + '-' + (pad + ddLhk).slice(-pad.length);
		
		formData.append('no_reg', noreg);
		formData.append('tgl_transaksi', tglLhk_fix);
		
		$('input[name^="slider_kurang_lain_jantan"]').each(function() {
			var jml_j = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			formData.append('kurang_lain_jml_j[]', parseInt(jml_j));
		});
		
		$('input[name^="slider_kurang_lain_betina"]').each(function() {
			var jml_b = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			formData.append('kurang_lain_jml_b[]', parseInt(jml_b));
		});
		
		$('input[name^="inp_kurang_jantan_lain_ket"]').each(function() {		
			formData.append('kurang_lain_ket[]', $(this).val());
		});
		
		$('input[name^="inp_kurang_jantan_lain_nomemo"]').each(function() {		
			formData.append('kurang_lain_nomemo[]', $(this).val());
		});
		
		var count_attachment = 0;
		$('input[name^=uploadFileKurangLain]').each(function(){
			var elm_input = $(this).parent().parent().parent().find('input')[1];
			if(!empty($(elm_input).val())){
				var file = $(this).get(0).files[0];
				
				formData.append('uploadFileKurangLain[]', file, file.name);
				count_attachment++;
			}
				
		});
		
		if(count_attachment > 0){
			$.ajax({
				type:'POST',
				url : "riwayat_harian_kandang/kurangLain2",
				data: formData,
				async : false,
				cache : false,
				contentType : false,
				processData : false
			})
			.done(function(html){
				
			})
			.fail(function(reason){
				console.info(reason);
			})
			.then(function(data){
			});
			
		}
		
	}else{
		console.log("WARNING!!! Ada kesalahan di fitur pengurangan lain-lain.");
	}
}

$('#btnTest').click(function(){
	if(valid_penambahan_lain){
		var formData = new FormData();
		
		var pad = '00';
		var noreg = selected_noreg;
		var tglLhk = $('#inp_tgl_lhk').val();
		var tglLhk_arr = tglLhk.split(' ');
		var ddLhk = parseInt(tglLhk_arr[0]);
		var mmLhk = (months.indexOf(tglLhk_arr[1]) >= 0) ? (months.indexOf(tglLhk_arr[1]) + 1) : (months_id.indexOf(tglLhk_arr[1])+1);
		var yyLhk = parseInt(tglLhk_arr[2]);
		var tglLhk_fix = yyLhk + '-' + (pad + mmLhk).slice(-pad.length) + '-' + (pad + ddLhk).slice(-pad.length);
		
		formData.append('no_reg', noreg);
		formData.append('tgl_transaksi', tglLhk_fix);
		
		$('input[name^="slider_tambah_lain_jantan"]').each(function() {
			var jml_j = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			formData.append('tambah_lain_jml_j[]', parseInt(jml_j));
		});
		
		$('input[name^="slider_tambah_lain_betina"]').each(function() {
			var jml_b = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			formData.append('tambah_lain_jml_b[]', parseInt(jml_b));
		});
		
		$('input[name^="inp_tambah_jantan_lain_ket"]').each(function() {		
			formData.append('tambah_lain_ket[]', $(this).val());
		});
		
		$('input[name^="inp_tambah_jantan_lain_nomemo"]').each(function() {		
			formData.append('tambah_lain_nomemo[]', $(this).val());
		});
		
		$('input[name^=uploadFileTambahLain]').each(function(){
			var elm_input = $(this).parent().parent().parent().find('input')[1];
			if(!empty($(elm_input).val())){
				var file = $(this).get(0).files[0];
				
				formData.append('uploadFileTambahLain[]', file, file.name);
			}
				
		});
		
		$.ajax({
			type:'POST',
			url : "riwayat_harian_kandang/test_tambah",
			data: formData,
			async : false,
			cache : false,
			contentType : false,
			processData : false
		})
		.done(function(html){
			
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
		});
		
	}else{
		console.log("WARNING!!! Ada kesalahan di fitur penambahan lain-lain.");
	}
	
	// if(valid_pengurangan_lain){
		// var formData = new FormData();
		
		// $('input[name^="slider_kurang_lain_jantan"]').each(function() {
			// var jml_j = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			// formData.append('kurang_lain_jml_j[]', parseInt(jml_j));
		// });
		
		// $('input[name^="slider_kurang_lain_betina"]').each(function() {
			// var jml_b = !empty($(this).val()) ? ($(this).val()).trim() : '0';
			
			// formData.append('kurang_lain_jml_b[]', parseInt(jml_b));
		// });
		
		// $('input[name^="inp_kurang_jantan_lain_ket"]').each(function() {		
			// formData.append('kurang_lain_ket[]', $(this).val());
		// });
		
		// $('input[name^="inp_kurang_jantan_lain_nomemo"]').each(function() {		
			// formData.append('kurang_lain_nomemo[]', $(this).val());
		// });
		
		// $('input[name^=uploadFileKurangLain]').each(function(){
			// var elm_input = $(this).parent().parent().parent().find('input')[1];
			// if(!empty($(elm_input).val())){
				// var file = $(this).get(0).files[0];
				
				// formData.append('uploadFileKurangLain[]', file, file.name);
			// }
				
		// });
		
		// $.ajax({
			// type:'POST',
			// url : "riwayat_harian_kandang/test",
			// data: formData,
			// async : false,
			// cache : false,
			// contentType : false,
			// processData : false
		// })
		// .done(function(html){
			
		// })
		// .fail(function(reason){
			// console.info(reason);
		// })
		// .then(function(data){
		// });
		
	// }else{
		// console.log("WARNING!!! Ada kesalahan di fitur pengurangan lain-lain.");
	// }
});

function cleanArray(actual){
  var newArray = new Array();
  for(var i = 0; i<actual.length; i++){
      if (actual[i]){
        newArray.push(actual[i]);
    }
  }
  return newArray;
}

function tambahPenguranganAyam(elm){
	var optionSelected = $(elm).val();
	var select = new Array();
	
	for(var i=0;i<kandang_in_farm.length;i++){
		var obj = kandang_in_farm[i];
		if(obj[0] != selected_kandang){
			select [i] = '<option value="' + obj[2] + '">' + obj[1] + '</option>'; 
		}
	}
	
	html_btn = ''+
		'<button type="button" data-toggle="tooltip" onclick="hapusRow(this)" title="Hapus" class="btn btn-sm btn-primary">'+
		'	<i class="glyphicon glyphicon-minus-sign"></i>'+
		'</button>';
	html =  '<tr>'+
			'	<td>'+
			'		<select class="form-control input-sm" name="inp_kandang_tujuan[]" onfocus="startPilihKandangPindah(this)">'+
			'		</select>'+
			'	</td>'+
			'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_kurang_ayam_j[]" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)"></td>'+
			'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_kurang_ayam_b[]" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)"></td>'+
			'	<td><input type="text" class="form-control input-sm" name="inp_kurang_ayam_k[]"></td>'+
			'	<td><input type="text" class="form-control input-sm" name="inp_kurang_ayam_memo[]"></td>'+
			'	<td>'+
			'	<button type="button" data-toggle="tooltip" onclick="tambahPenguranganAyam(this)" title="Tambah" class="btn btn-sm btn-primary">'+
			'		<i class="glyphicon glyphicon-plus-sign"></i>'+
			'	</button>'+
			'	</td>'+
			'</tr>';
		
	var td = $(elm).parent();
	td.html(html_btn);
	
	$(html).appendTo("#md_pengurangan_ayam > tbody");
}

$('#btnSimpan').click(function(){
	//Lhk Pakan - Jantan
	var j_pakan = new Array(),
		b_pakan = new Array(),
		j_totalPakanKg = 0,
		b_totalPakanKg = 0,
		passed = true;
		
	$('input[name^="inp_j_pakan"]').each(function() {
		j_pakan.push($(this).val());
	});
	
	$('input[name^="inp_b_pakan"]').each(function() {
		b_pakan.push($(this).val());
	});
	
	if(b_pakan.length == 0 && j_pakan.length == 0){
		toastr.warning("Laporan Harian Kandang - Pakan harus di isi",'Peringatan');
		
		return false;
	}
	
	$('input[name^="inp_j_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_totalPakanKg += parseFloat(jml);
	});
	
	$('input[name^="inp_b_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_totalPakanKg += parseFloat(jml);
	});
	
	if(parseFloat(j_totalPakanKg) > batas_atas_pakan_jantan || parseFloat(b_totalPakanKg) > batas_atas_pakan_betina){
		bootbox.dialog({
			message: "Entrian data pemakaian pakan melebihi batas maksimum pakan sistem. Apakah Anda yakin untuk melanjutkan?",
			title: "Notifikasi",
			buttons: {
				success: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						simpanLhk('N', 0);
					}
				},
				danger: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
						
					}
				}
			}
		});
	}else{
		bootbox.dialog({
			message: "Apakah Anda yakin melakukan penyimpanan?",
			title: "Konfirmasi",
			buttons: {
				success: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						simpanLhk('N', 0);
					}
				},
				danger: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
					
					}
				}
			}
		});
	}
});

$('#btnTutupSiklus').click(function(){
	var pakan_sisa_kg = new Array();
	var pakan_sisa_sak = new Array();
	
	$('input[name^="inp_j_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_kg.push(jml);
	});
	$('input[name^="inp_j_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_sak.push(jml);
	});
		
	$('input[name^="inp_b_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_kg.push(jml);
	});
	$('input[name^="inp_b_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_sak.push(jml);
	});
	
	$('#btnSimpan').addClass("disabled");
		
	//Lhk Pakan - Jantan
	var j_pakan = new Array(),
		b_pakan = new Array(),
		j_totalPakanKg = 0,
		b_totalPakanKg = 0,
		passed = true;
		
	$('input[name^="inp_j_pakan"]').each(function() {
		j_pakan.push($(this).val());
	});
	
	$('input[name^="inp_b_pakan"]').each(function() {
		b_pakan.push($(this).val());
	});
	
	if(b_pakan.length == 0 && j_pakan.length == 0){
		toastr.warning("Laporan Harian Kandang - Pakan harus di isi",'Peringatan');
		
		return false;
	}
	
	$('input[name^="inp_j_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_totalPakanKg += parseFloat(jml);
	});
	
	$('input[name^="inp_b_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_totalPakanKg += parseFloat(jml);
	});
	
	if(parseFloat(j_totalPakanKg) > batas_atas_pakan_jantan || parseFloat(b_totalPakanKg) > batas_atas_pakan_betina){
		bootbox.dialog({
			message: "Entrian data pemakaian pakan melebihi batas maksimum pakan sistem. Apakah Anda yakin untuk melanjutkan?",
			title: "Notifikasi",
			buttons: {
				success: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						simpanLhk('N', 0);
					}
				},
				danger: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
						
					}
				}
			}
		});
	}else{
		bootbox.dialog({
			message: "Apakah Anda yakin melakukan TUTUP SIKLUS?",
			title: "Konfirmasi (Tutup Siklus)",
			buttons: {
				success: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						var totalStokAkhir = 0;
						for(var i=0;i<pakan_sisa_sak.length;i++){
							totalStokAkhir+=(parseFloat(pakan_sisa_sak[i])+parseFloat(pakan_sisa_kg[i]));
						}
						
						simpanLhk('Y', totalStokAkhir);
					}
				},
				danger: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
					
					}
				}
			}
		});
	}
});

$('#btnPrint').click(function(e){
	e.preventDefault();
	
	bootbox.dialog({
			message: "Apakah Anda yakin melakukan retur pakan ke gudang?",
			title: "Konfirmasi",
			buttons: {
				success: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "riwayat_harian_kandang/buat_pengajuan_retur/",
							data: {
								no_reg : selected_noreg,
								setuju : 'Y'
							}
						})
						.done(function(data){
							if(data.result == "success"){
								toastr.success("Penyimpanan LHK dan Pengajuan Retur Pakan berhasil dilakukan",'Informasi');
							}else{
								toastr.warning("Penyimpanan Data Retur Pakan gagal dilakukan",'Informasi');
							}
						})
						.fail(function(reason){
							console.info(reason);
						})
						.then(function(data){
						});
						
						$('#modal_sisa').modal("hide");
						resetLhk();
					}
				},
				danger: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "riwayat_harian_kandang/buat_pengajuan_retur/",
							data: {
								no_reg : selected_noreg,
								setuju : 'T'
							}
						})
						.done(function(data){
							if(data.result == "success"){
								toastr.success("Penyimpanan LHK berhasil dilakukan",'Informasi');
							}else{
								toastr.warning("Penyimpanan Data Retur Pakan gagal dilakukan",'Informasi');
							}
						})
						.fail(function(reason){
							console.info(reason);
						})
						.then(function(data){
						});
						
						$('#modal_sisa').modal("hide");
						resetLhk();
					}
				}
			}
		}
	);
});

function cekNumerik(field){
	var re = /^[0-9-'.']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.']/g,"");
	}

	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");	
}

function cekNumerikUniform(field){
	var re = /^[0-9-'.']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.']/g,"");
	}

	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");

	var name_elm = ($(field).attr("name")).replace("[]","");
	var tot_sampling = 0;
	$('input[name^="'+name_elm+'"]').each(function() {
		tot_sampling += parseInt($(this).val());
	});
	
	if(name_elm.indexOf("_betina") > -1){
		$("#inp_uni_tsampling_betina").val(tot_sampling);
		/*
		timbang_b_arr_jml = new Array();
		var total_sampling = 0;
		
		$('input[name^="j_timbang_arr_betina"]').each(function() {			
			var jml = (($(this).val()).trim() == '') ? 0 : $(this).val();
			timbang_b_arr_jml.push(jml);
		});
		
		var jml_sample_ayam = 0;
		
		for(var i=0;i<timbang_b_arr_jml.length;i++){
			if(timbang_b_arr_bb[i] >= timbang_b_bb && timbang_b_arr_bb[i] <= timbang_b_ba){
				jml_sample_ayam += parseInt(timbang_b_arr_jml[i]);
			}
			
			total_sampling += parseInt(timbang_b_arr_jml[i]);
		}
		
		var status = jml_sample_ayam/total_sampling*100;
		$('#inp_uni_uniformity_betina').val( Number(Math.round(parseFloat(status) * 1000) / 1000).toFixed(2));
		if(status >= 85)
			$('#lbl_status_uniformity_betina').html("NORMAL");
		else
			$('#lbl_status_uniformity_betina').html("BURUK");
		*/
	}
	else{
		$("#inp_uni_tsampling").val(tot_sampling);
		
		/*
		timbang_j_arr_jml = new Array();
		var total_sampling = 0;
		
		$('input[name^="j_timbang_arr"]').each(function() {
			
			var jml = (($(this).val()).trim() == '') ? 0 : $(this).val();
			timbang_j_arr_jml.push(jml);
		});
		
		var jml_sample_ayam = 0;
		
		for(var i=0;i<timbang_j_arr_jml.length;i++){
			if(timbang_j_arr_bb[i] >= timbang_j_bb && timbang_j_arr_bb[i] <= timbang_j_ba){
				jml_sample_ayam += parseInt(timbang_j_arr_jml[i]);
			}
			
			total_sampling += parseInt(timbang_j_arr_jml[i]);
		}
		
		var status = jml_sample_ayam/total_sampling*100;
		$('#inp_uni_uniformity').val(Number(Math.round(parseFloat(status) * 1000) / 1000).toFixed(2));
		
		if(status >= 85)
			$('#lbl_status_uniformity').html("NORMAL");
		else
			$('#lbl_status_uniformity').html("BURUK");
		*/
	}
}

function cekDecimal(field){
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
	
}

function cekNumerikNoMinusNoCommaNoDot(field){
	var re = /^[0-9]*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9]/g,"");
	} 
	
	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");
}

function cekNumerikPopluasi(field){	
	var re = /^[0-9]*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9]/g,"");
	}
	
	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");
	
	var td = $(field).parent();
	var td_index = $(field).parent().index();
	var tr = $(td).parent();
	
	var vc1 = $(tr).find('td').eq(1).find('input').val();
	var vc2 = $(tr).find('td').eq(2).find('input').val();
	var vc3 = $(tr).find('td').eq(3).find('input').val();
	var vc4 = $(tr).find('td').eq(4).find('input').val();
	var vc5 = $(tr).find('td').eq(5).find('input').val();
	var vc6 = $(tr).find('td').eq(6).find('input').val();
	var vc7 = $(tr).find('td').eq(7).find('input').val();
	var vc8 = $(tr).find('td').eq(8).find('input').val();
	var vc9 = $(tr).find('td').eq(9).find('input').val();
	var vc10 = $(tr).find('td').eq(10).find('input').val();
	var vc11 = $(tr).find('td').eq(11).find('input').val();
	var col12 = $(tr).find('td').eq(12).find('input');
	
	vc1 = (!empty(vc1)) ? parseInt(vc1) : 0;
	vc2 = (!empty(vc2)) ? parseInt(vc2) : 0;
	vc3 = (!empty(vc3)) ? parseInt(vc3) : 0;
	vc4 = (!empty(vc4)) ? parseInt(vc4) : 0;
	vc5 = (!empty(vc5)) ? parseInt(vc5) : 0;
	vc6 = (!empty(vc6)) ? parseInt(vc6) : 0;
	vc7 = (!empty(vc7)) ? parseInt(vc7) : 0;
	vc8 = (!empty(vc8)) ? parseInt(vc8) : 0;
	vc9 = (!empty(vc9)) ? parseInt(vc9) : 0;
	vc10 = (!empty(vc10)) ? parseInt(vc10) : 0;
	vc11 = (!empty(vc11)) ? parseInt(vc11) : 0;
	
	var jml = vc1+vc2+vc3-vc4-vc5-vc6-vc7-vc8-vc9-vc10-vc11;
	
	if(jml < 0){
		if(td_index < 4){
			jml = parseInt(jml) - parseInt(field.value); 
		}else{
			jml = parseInt(jml) + parseInt(field.value); 
		}
		
		$(field).val('0');
	}
	
	$(col12).val(jml);
	
	count_rasio();
}

function cekNumerikPakanKg(evt, field){
	var jenis_kelamin = $(field).attr("data-jeniskelamin");
	
	var charCode = (evt.which) ? evt.which : evt.keyCode;
	//console.log("charCOde:" + charCode);
	if (
		(charCode != 45 || $(field).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
		(charCode != 46 || $(field).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
		((charCode < 48) || charCode > 57) &&
		(charCode != 8 && charCode != 9 && charCode != 37 && charCode != 38 && charCode != 39 && charCode != 40)){
		
		$(field).val(($(field).val()).replace(/[^0-9-'.']/g,""));
		return false;
	
	}
		
	if(!empty($(field).val())){
	}else{
		$(field).val("0");	
	}
	
	var td = $(field).parent();
	var tr = $(td).parent();
	
	var stokAwal = $(tr).find('td').eq(2).find('input');
	var stokKirim = $(tr).find('td').eq(4).find('input');
	var stokAkhir = $(tr).find('td').eq(8).find('input');
	var nilai = parseFloat($(stokAwal).val()) + parseFloat($(stokKirim).val()) - parseFloat(field.value);
	
	if(nilai < 0){
		nilai = nilai + parseFloat(field.value);
		$(field).val("0");
	}
	
	$(stokAkhir).val(Number(Math.round(nilai * 1000) / 1000).toFixed(3));
	
	return true;
}

function cekNumerikPakanSak(field){
	var re = /^[0-9]*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9]/g,"");
	} 
	
	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");
	
	var td = $(field).parent();
	var tr = $(td).parent();
	
	var stokAwal = $(tr).find('td').eq(3).find('input');
	var stokKirim = $(tr).find('td').eq(5).find('input');
	var stokTerpakaiKg = $(tr).find('td').eq(6).find('input');
	var stokAkhir = $(tr).find('td').eq(9).find('input');
	var nilai = parseInt($(stokAwal).val()) + parseInt($(stokKirim).val()) - parseInt(field.value);
	
	if((parseInt(field.value) > 0 && parseFloat($(stokTerpakaiKg).val()) <= 0)){
		nilai = nilai + parseInt(field.value);
		$(field).val("0");
		
		$(stokAkhir).val(nilai);
		
		return false;
	}
	
	if( nilai < 0){
		nilai = nilai + parseInt(field.value);
		$(field).val("0");
		
		$(stokAkhir).val(nilai);
		
		return false
	}
	
	$(stokAkhir).val(nilai);
}

function cekNumerikProduksi(field){
	var re = /^[0-9]*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9]/g,"");
	} 
	
	if(!empty($(field).val()))
		$(field).val(parseInt(field.value) * 1);
	else
		$(field).val("0");
	
	var td = $(field).parent();
	var tr = $(td).parent();
	
	var vc0 = $(tr).find('td').eq(0).find('input').val();
	var vc1 = $(tr).find('td').eq(1).find('input').val();
	var vc2 = $(tr).find('td').eq(2).find('input').val();
	var vc3 = $(tr).find('td').eq(3).find('input').val();
	var vc4 = $(tr).find('td').eq(4).find('input').val();
	var vc5 = $(tr).find('td').eq(5).find('input').val();
	var vc6 = $(tr).find('td').eq(6).find('input').val();
	var vc7 = $(tr).find('td').eq(7).find('input').val();
	var vc8 = $(tr).find('td').eq(8).find('input').val();
	var col9 = $(tr).find('td').eq(9).find('input');
	
	vc0 = (!empty(vc0)) ? parseInt(vc0) : 0;
	vc1 = (!empty(vc1)) ? parseInt(vc1) : 0;
	vc2 = (!empty(vc2)) ? parseInt(vc2) : 0;
	vc3 = (!empty(vc3)) ? parseInt(vc3) : 0;
	vc4 = (!empty(vc4)) ? parseInt(vc4) : 0;
	vc5 = (!empty(vc5)) ? parseInt(vc5) : 0;
	vc6 = (!empty(vc6)) ? parseInt(vc6) : 0;
	vc7 = (!empty(vc7)) ? parseInt(vc7) : 0;
	vc8 = (!empty(vc8)) ? parseInt(vc8) : 0;
	
	var jml = vc0+vc1+vc2+vc3+vc4+vc5+vc6+vc7+vc8;
	$(col9).val(jml);
}

function setNamaBarang(elm){
	var kode = $(elm).val();
	var td = $(elm).parent();
	var tr = $(td).parent();
	var colKode = $(tr).find('td').eq(1);
	var input = $(colKode).find('input');
	
	$(input).val(kode);
}

function startPilihKandangPindah(elm){
	
	var selected_option = new Array();
	var select = new Array();
	var its_value = $(elm).val();
	var i = 0;
	
	$('select[name^="inp_kandang_tujuan"]').each(function() {
		if(!empty($(this).val())){
			selected_option[i] = $(this).val();
			i++;
		}
	});
	
	for(var j=0;j<kandang_in_farm.length;j++){
		var obj = kandang_in_farm[j];
		if(obj[0] != selected_kandang && jQuery.inArray(obj[2], selected_option)){
			select[j] = '<option value="' + obj[2] + '">' + obj[1] + '</option>'; 
		}
	}
		
	$(elm).html('<option value=""></option>'+select.join(''));
	if(!empty(its_value)){
		$(elm).val(its_value);
	}
}

function retur_pakan(){
	var pakan_kode = new Array();
	var pakan_nama = new Array();
	var pakan_sisa_kg = new Array();
	var pakan_sisa_sak = new Array();
	var pakan_bentuk = new Array();
	
	$('input[name^="inp_j_pakan"]').each(function() {
		pakan_kode.push($(this).val());
	});
	$('input[name^="inp_j_nama_pakan"]').each(function() {
		pakan_nama.push($(this).val());
	});
	$('input[name^="inp_j_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_kg.push(jml);
	});
	$('input[name^="inp_j_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_sak.push(jml);
	});
	$('input[name^="inp_j_bentuk_pakan"]').each(function() {
		pakan_bentuk.push($(this).val());
	});	
	
	$('input[name^="inp_b_pakan"]').each(function() {
		pakan_kode.push($(this).val());
	});	
	$('input[name^="inp_b_nama_pakan"]').each(function() {
		pakan_nama.push($(this).val());
	});
	$('input[name^="inp_b_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_kg.push(jml);
	});
	$('input[name^="inp_b_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_sak.push(jml);
	});
	$('input[name^="inp_b_bentuk_pakan"]').each(function() {
		pakan_bentuk.push($(this).val());
	});
	
	// pakan_kode.push('11-1234-21');
	// pakan_nama.push('P 3 COBB');
	// pakan_sisa_kg.push(10);
	// pakan_sisa_sak.push(20);
	// pakan_bentuk.push('CRUMBLE');
	
	// pakan_kode.push('11-1234-22');
	// pakan_nama.push('PJ COBB');
	// pakan_sisa_kg.push(10);
	// pakan_sisa_sak.push(20);
	// pakan_bentuk.push('CRUMBLE');
	
	// pakan_kode.push('11-1234-21');
	// pakan_nama.push('P 3 COBB');
	// pakan_sisa_kg.push(10);
	// pakan_sisa_sak.push(20);
	// pakan_bentuk.push('CRUMBLE');
	
	// pakan[0] = ["11-1234-21","P 3 COBB","10","501.23","CRUMBLE"];
	// pakan[1] = ["11-1234-22","PJB COBB","2","120.25","CRUMBLE"];
	
	var pakan = new Array();
	
	for(var i=0;i<pakan_kode.length;i++){
		if(pakan.length == 0){
			pakan.push( new Array(pakan_kode[i], pakan_nama[i], parseFloat(pakan_sisa_kg[i]), parseFloat(pakan_sisa_sak[i]), pakan_bentuk[i]));
		}else{
			var alreadyExist = false;
			for(var j=0;j<pakan.length;j++){
				if(pakan[j][0] == pakan_kode[i]){
					alreadyExist = true;
					
					pakan[j][2] += parseFloat(pakan_sisa_kg[j]);
					pakan[j][3] += parseFloat(pakan_sisa_sak[j]);
					
					break;
				}
			}
			
			if(!alreadyExist)
				pakan.push( new Array(pakan_kode[i], pakan_nama[i], parseFloat(pakan_sisa_kg[i]), parseFloat(pakan_sisa_sak[i]), pakan_bentuk[i]));
		}
	}
	
	var kodefarm = $('#inp_farm').val();
	var namafarm = $('#inp_nama_farm').val();
	var namakandang = $('#inp_kandang').val();
	var tglTutupSiklus = $('#inp_tgl_lhk').val();
	
	var html = new Array();
	for(var i=0; i<pakan.length; i++){
		if(parseFloat(pakan[i][2]) > parseFloat(0) || parseFloat(pakan[i][3]) > parseFloat(0)){
			var str = '<tr>'+
					  '		<td>'+pakan[i][0]+'<input type="hidden" name="inp_print_kodebarang[]" value="'+pakan[i][0]+'"/></td>'+
					  '		<td>'+pakan[i][1]+'<input type="hidden" name="inp_print_namabarang[]" value="'+pakan[i][1]+'"/></td>'+
					  '		<td>'+pakan[i][2]+'<input type="hidden" name="inp_print_jml[]" onkeyup="cekNumerikNoMinusNoCommaNoDot(this)" value="'+pakan[i][2]+'"/></td>'+
					  '		<td>'+pakan[i][3]+'<input type="hidden" name="inp_print_berat[]" onkeyup="cekNumerik(this)" value="'+pakan[i][3]+'"/></td>'+
					  '		<td>'+pakan[i][4]+'<input type="hidden" name="inp_print_bentuk[]" value="'+pakan[i][4]+'"/></td>'+
					  '</tr>';
			
			html.push(str);
		}
	}
	
	$('#print_nama_kandang').html(namakandang);
	$('#print_tgl_lhk').html(tglTutupSiklus);
	$('#titlefarm').html(namafarm);
	
	$('#inp_print_kodefarm').val(kodefarm);
	$('#inp_print_farm').val(namafarm);
	$('#inp_print_kandang').val(namakandang);
	$('#inp_print_tgl').val(tglTutupSiklus);
	
	$('#tb_sisa > tbody').html(html.join(''));
	
	$('#modal_sisa').modal({
		backdrop: 'static',
		keyboard: false
	});
	
	$('#modal_sisa').modal("show");
}

function simpanLhk(isTutupSiklus, totalStokAkhir){
	//Header LHK
	var pad = '00';
	var farm = selected_farm;
	var kandang = selected_kandang;
	var noreg = selected_noreg;
	var tglLhk = $('#inp_tgl_lhk').val();
	var tglLhk_arr = tglLhk.split(' ');
	var ddLhk = parseInt(tglLhk_arr[0]);
	var mmLhk = (months.indexOf(tglLhk_arr[1]) >= 0) ? (months.indexOf(tglLhk_arr[1]) + 1) : (months_id.indexOf(tglLhk_arr[1])+1);
	var yyLhk = parseInt(tglLhk_arr[2]);
	var tglLhk_fix = yyLhk + '-' + (pad + mmLhk).slice(-pad.length) + '-' + (pad + ddLhk).slice(-pad.length);
	var umur = ($('#inp_umur').val()).replace(' ','');
	var bb_jantan = (($('#inp_bb_ja').val()).trim()!="") ? parseFloat($('#inp_bb_ja').val()) : 0;
	var bb_betina = (($('#inp_bb_be').val()).trim()!="") ? parseFloat($('#inp_bb_be').val()) : 0;
	var tutupSiklus = isTutupSiklus;	
	
	//Populasi JANTAN
	var populasiAwalJantan = $('#inp_populasiAwalJantan').val();
	var tambahJantan = $('#inp_tambahJantan').val();
	var tambahJantanLain = $('#inp_tambahJantanLain').val();
	var kurangJantanMati = $('#inp_kurangJantanMati').val();
	var kurangJantanAfkir = $('#inp_kurangJantanAfkir').val();
	var kurangJantanPindah = $('#inp_kurangJantan').val();
	var kurangJantanSexslip = $('#inp_kurangJantanSexslip').val();
	var kurangJantanKanibal = $('#inp_kurangJantanKanibal').val();
	var kurangJantanCampur = $('#inp_kurangJantanCampur').val();
	var kurangJantanSeleksi = $('#inp_kurangJantanSeleksi').val();
	var kurangJantanLain = $('#inp_kurangJantanLain').val();
	var populasiAkhirJantan = $('#inp_populasiAkhirJantan').val();
	
	//Populasi BETINA
	var populasiAwalBetina = $('#inp_populasiAwalBetina').val();
	var tambahBetina = $('#inp_tambahBetina').val();
	var tambahBetinaLain = $('#inp_tambahBetinaLain').val();
	var kurangBetinaMati = $('#inp_kurangBetinaMati').val();
	var kurangBetinaAfkir = $('#inp_kurangBetinaAfkir').val();
	var kurangBetinaPindah = $('#inp_kurangBetina').val();
	var kurangBetinaSexslip = $('#inp_kurangBetinaSexslip').val();
	var kurangBetinaKanibal = $('#inp_kurangBetinaKanibal').val();
	var kurangBetinaCampur = $('#inp_kurangBetinaCampur').val();
	var kurangBetinaSeleksi = $('#inp_kurangBetinaSeleksi').val();
	var kurangBetinaLain = $('#inp_kurangBetinaLain').val();
	var populasiAkhirBetina = $('#inp_populasiAkhirBetina').val();
	
	//Lhk Pakan - Jantan
	var j_pakan = new Array(),
	    j_stokAwalKg = new Array(),
	    j_stokAwalSak = new Array(),
	    j_kirimKg = new Array(),
	    j_kirimSak = new Array(),
	    j_terpakaiKg = new Array(),
	    j_terpakaiSak = new Array(),
	    j_stokAkhirKg = new Array(),
	    j_stokAkhirSak = new Array();
	
	$('input[name^="inp_j_pakan"]').each(function() {
		j_pakan.push($(this).val());
	});
	$('input[name^="inp_j_stokAwalKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_stokAwalKg.push(jml);
	});
	$('input[name^="inp_j_stokAwalSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_stokAwalSak.push(jml);
	});
	$('input[name^="inp_j_kirimKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_kirimKg.push(jml);
	});
	$('input[name^="inp_j_kirimSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_kirimSak.push(jml);
	});
	$('input[name^="inp_j_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_terpakaiKg.push(jml);
	});
	$('input[name^="inp_j_terpakaiSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_terpakaiSak.push(jml);
	});
	
	for(var i=0;i<j_terpakaiSak;i++){
		if(j_terpakaiSak[i] > 0 && j_terpakaiKg[i] <=0){
			bootbox.alert("Kg Terpakai tidak boleh kosong/nol");
			return false;
		}
	}
	
	$('input[name^="inp_j_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_stokAkhirKg.push(jml);
	});
	$('input[name^="inp_j_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		j_stokAkhirSak.push(jml);
	});
	
	//Lhk Pakan - Betina
	var b_pakan = new Array(),
	    b_stokAwalKg = new Array(),
	    b_stokAwalSak = new Array(),
	    b_kirimKg = new Array(),
	    b_kirimSak = new Array(),
	    b_terpakaiKg = new Array(),
	    b_terpakaiSak = new Array(),
	    b_stokAkhirKg = new Array(),
	    b_stokAkhirSak = new Array();
	
	$('input[name^="inp_b_pakan"]').each(function() {
		b_pakan.push($(this).val());
	});
	$('input[name^="inp_b_stokAwalKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_stokAwalKg.push(jml);
	});
	$('input[name^="inp_b_stokAwalSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_stokAwalSak.push(jml);
	});
	$('input[name^="inp_b_kirimKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_kirimKg.push(jml);
	});
	$('input[name^="inp_b_kirimSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_kirimSak.push(jml);
	});
	$('input[name^="inp_b_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_terpakaiKg.push(jml);
	});
	$('input[name^="inp_b_terpakaiSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_terpakaiSak.push(jml);
	});
	
	for(var i=0;i<b_terpakaiSak;i++){
		if(b_terpakaiSak[i] > 0 && b_terpakaiKg[i] <=0){
			bootbox.alert("Kg Terpakai tidak boleh kosong/nol");
			return false;
		}
	}
	
	$('input[name^="inp_b_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_stokAkhirKg.push(jml);
	});
	$('input[name^="inp_b_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		b_stokAkhirSak.push(jml);
	});
	
	//Lhk Obat
	var obat_kodebarang = new Array();
	var obat_pakaijantan = new Array();
	var obat_pakaibetina = new Array();
	var obat_keterangan = new Array();
	
	$('select[name^="inp_obat_kodebarang"]').each(function() {
		obat_kodebarang.push($(this).val());
	});
	$('input[name^="inp_obat_pakaijantan"]').each(function() {
		obat_pakaijantan.push($(this).val());
	});
	$('input[name^="inp_obat_pakaibetina"]').each(function() {
		obat_pakaibetina.push($(this).val());
	});
	$('input[name^="inp_obat_keterangan"]').each(function() {
		obat_keterangan.push($(this).val());
	});
	
	//Lhk Vaksin
	var vaksin_kodebarang = new Array();
	var vaksin_pakaijantan = new Array();
	var vaksin_pakaibetina = new Array();
	var vaksin_keterangan = new Array();
	
	$('select[name^="inp_vaksin_kodebarang"]').each(function() {
		vaksin_kodebarang.push($(this).val());
	});
	$('input[name^="inp_vaksin_pakaijantan"]').each(function() {
		vaksin_pakaijantan.push($(this).val());
	});
	$('input[name^="inp_vaksin_pakaibetina"]').each(function() {
		vaksin_pakaibetina.push($(this).val());
	});
	$('input[name^="inp_vaksin_keterangan"]').each(function() {
		vaksin_keterangan.push($(this).val());
	});
	
	//Pengurangan Ayam
	//var jantan_arr = new Array();
	//var betina_arr = new Array();
	// for(var m=0;m<kandang_id_arr.length;m++){
		// console.log(kandang_id_arr[m]);
		// console.log(jantan_arr[m]);
		// console.log(betina_arr[m]);
	// }
	
	//Produksi
	var pro_baik = new Array();
	var pro_besar = new Array();
	var pro_tipis = new Array();
	var pro_kecil = new Array();
	var pro_kotor = new Array();
	var pro_abnormal = new Array();
	var pro_ib = new Array();
	var pro_retak = new Array();
	var pro_hancur = new Array();
	var pro_jumlah = new Array();
	var pro_keterangan = new Array();
	
	$('input[name^="inp_prod_baik"]').each(function() {
		pro_baik.push($(this).val());
	});
	$('input[name^="inp_prod_besar"]').each(function() {
		pro_besar.push($(this).val());
	});
	$('input[name^="inp_prod_tipis"]').each(function() {
		pro_tipis.push($(this).val());
	});
	$('input[name^="inp_prod_kecil"]').each(function() {
		pro_kecil.push($(this).val());
	});
	$('input[name^="inp_prod_kotor"]').each(function() {
		pro_kotor.push($(this).val());
	});
	$('input[name^="inp_prod_abnormal"]').each(function() {
		pro_abnormal.push($(this).val());
	});
	$('input[name^="inp_prod_ib"]').each(function() {
		pro_ib.push($(this).val());
	});
	$('input[name^="inp_prod_retak"]').each(function() {
		pro_retak.push($(this).val());
	});
	$('input[name^="inp_prod_hancur"]').each(function() {
		pro_hancur.push($(this).val());
	});
	$('input[name^="inp_prod_jumlah"]').each(function() {
		pro_jumlah.push($(this).val());
	});
	$('input[name^="inp_prod_keterangan"]').each(function() {
		pro_keterangan.push($(this).val());
	});
	
	var br_telur, 
		cv_jantan, 
		cv_betina,
		uniformity_jantan,
		uniformity_betina,
		doc_in_jantan,
		doc_in_betina;
	
	br_telur = $('#inp_berat_telur').val();
	cv_jantan = $('#inp_cv_jantan').val();
	cv_betina = $('#inp_cv_betina').val();
	uniformity_jantan = $('#inp_uniformity_jantan').val();
	uniformity_betina = $('#inp_uniformity_betina').val();
	doc_in_jantan = $('#inp_doc_in_jantan').val();
	doc_in_betina = $('#inp_doc_in_betina').val();
	
	j_pindah_semu = $('#inp_j_pindah_semu').val();
	j_daya_hidup = $('#inp_j_daya_hidup').val();
	j_jml_pembagi = $('#inp_j_jml_pembagi').val();
	
	b_pindah_semu = $('#inp_b_pindah_semu').val();
	b_daya_hidup = $('#inp_b_daya_hidup').val();
	b_jml_pembagi = $('#inp_b_jml_pembagi').val();
	
	//Uniformity -JANTAN
	uni_j_arr_bb = timbang_j_arr_bb;
	uni_j_arr_jml = timbang_j_arr_jml;
	
	//Uniformity -BETINA
	uni_b_arr_bb = timbang_b_arr_bb;
	uni_b_arr_jml = timbang_b_arr_jml;
	
	//Simpan lhk
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/simpan_lhk/",
		data: {
			kode_farm : farm,
			kode_kandang : kandang,
			noreg : noreg,
			tgl_lhk : tglLhk_fix,
			umur : umur,
			bb_jantan : bb_jantan,
			bb_betina : bb_betina,
			tutup_siklus : tutupSiklus,
			populasi_awal_jantan : populasiAwalJantan,
			tambah_jantan : tambahJantan,
			tambah_jantanLain : tambahJantanLain,
			kurang_jantanMati : kurangJantanMati,
			kurang_jantanAfkir : kurangJantanAfkir,
			kurang_jantanPindah : kurangJantanPindah,
			kurang_jantanSexslip : kurangJantanSexslip,
			kurang_jantanKanibal : kurangJantanKanibal,
			kurang_jantanCampur : kurangJantanCampur,
			kurang_jantanSeleksi : kurangJantanSeleksi,
			kurang_jantanLain : kurangJantanLain,
			populasi_akhir_jantan : populasiAkhirJantan,
			populasi_awal_betina : populasiAwalBetina,
			tambah_betina : tambahBetina,
			tambah_betinaLain : tambahBetinaLain,
			kurang_betinaMati : kurangBetinaMati,
			kurang_betinaAfkir : kurangBetinaAfkir,
			kurang_betinaPindah : kurangBetinaPindah,
			kurang_betinaSexslip : kurangBetinaSexslip,
			kurang_betinaKanibal : kurangBetinaKanibal,
			kurang_betinaCampur : kurangBetinaCampur,
			kurang_betinaSeleksi : kurangBetinaSeleksi,
			kurang_betinaLain : kurangBetinaLain,
			populasi_akhir_betina : populasiAkhirBetina,
			j_pakan : j_pakan,
			j_stokAwalKg : j_stokAwalKg,
			j_stokAwalSak : j_stokAwalSak,
			j_kirimKg : j_kirimKg,
			j_kirimSak : j_kirimSak,
			j_terpakaiKg : j_terpakaiKg,
			j_terpakaiSak : j_terpakaiSak,
			j_stokAkhirKg : j_stokAkhirKg,
			j_stokAkhirSak : j_stokAkhirSak,
			b_pakan : b_pakan,
			b_stokAwalKg : b_stokAwalKg,
			b_stokAwalSak : b_stokAwalSak,
			b_kirimKg : b_kirimKg,
			b_kirimSak : b_kirimSak,
			b_terpakaiKg : b_terpakaiKg,
			b_terpakaiSak : b_terpakaiSak,
			b_stokAkhirKg : b_stokAkhirKg,
			b_stokAkhirSak : b_stokAkhirSak,
			obat_kodebarang : obat_kodebarang,
			obat_pakaijantan : obat_pakaijantan,
			obat_pakaibetina : obat_pakaibetina,
			obat_keterangan : obat_keterangan,
			vaksin_kodebarang : vaksin_kodebarang,
			vaksin_pakaijantan : vaksin_pakaijantan,
			vaksin_pakaibetina : vaksin_pakaibetina,
			vaksin_keterangan : vaksin_keterangan,
			pindah_kandang : kandang_id_arr,			
			pindah_jantan : jantan_arr,			
			pindah_betina : betina_arr,			
			pindah_keterangan : keterangan_arr,			
			pindah_ba : beritaacara_arr,
			pro_baik : pro_baik,
			pro_besar : pro_besar,
			pro_tipis : pro_tipis,
			pro_kecil : pro_kecil,
			pro_kotor : pro_kotor,
			pro_abnormal : pro_abnormal,
			pro_ib : pro_ib,
			pro_retak : pro_retak,
			pro_hancur : pro_hancur,
			pro_jumlah : pro_jumlah,
			pro_keterangan : pro_keterangan,
			br_telur : br_telur,
			cv_jantan : cv_jantan,
			cv_betina : cv_betina,
			uniformity_jantan : uniformity_jantan,
			uniformity_betina : uniformity_betina,
			doc_in_jantan : doc_in_jantan,
			doc_in_betina : doc_in_betina,
			j_pindah_semu : j_pindah_semu,
			j_daya_hidup : j_daya_hidup,
			j_jml_pembagi : j_jml_pembagi,
			b_pindah_semu : b_pindah_semu,
			b_daya_hidup : b_daya_hidup,
			b_jml_pembagi : b_jml_pembagi,
			j_uni_bb : uni_j_arr_bb,
			j_uni_jml : uni_j_arr_jml,
			b_uni_bb : uni_b_arr_bb,
			b_uni_jml : uni_b_arr_jml
			
		}
	})
	.done(function(data){
		if(data.msg == "success"){
			if(totalStokAkhir > 0){
				retur_pakan();
			}else{
				toastr.success("Penyimpanan LHK berhasil dilakukan",'Informasi');
				
				var j_daya_hidup = data.j_daya_hidup;
				var b_daya_hidup = data.b_daya_hidup;
				
				$('#inp_dayahidup_jantan').val(Number(Math.round(j_daya_hidup * 1000) / 1000).toFixed(3));
				$('#inp_dayahidup_betina').val(Number(Math.round(b_daya_hidup * 1000) / 1000).toFixed(3));
				
				
				simpanPenambahanLain2();
				simpanPenguranganLain2();
				disabledLhk();				
			}
		}else{
			toastr.warning("Penyimpanan LHK gagal dilakukan",'Informasi');
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

function resetLhk(){
	//Reset Header
	$('#btnSimpan').removeClass("disabled");
	$('#inp_kandang').val('');
	$('#inp_flock').val('');
	$( "#inp_doc_in" ).val('');
	$( "#inp_tgl_lhk" ).val('');
	$( "#inp_umur" ).val('');
	$( "#inp_bb_ja" ).val('0');
	$( "#inp_bb_be" ).val('0');
	
	//Reset LHK-Populasi
	$( "#inp_populasiAwalJantan" ).val('0');
	$( "#inp_tambahJantan" ).val('0');
	$( "#inp_tambahJantanLain" ).val('0');
	$( "#inp_kurangJantanMati" ).val('0');
	$( "#inp_kurangJantanAfkir" ).val('0');
	$( "#inp_kurangJantan" ).val('0');
	$( "#inp_kurangJantanSexslip" ).val('0');
	$( "#inp_kurangJantanKanibal" ).val('0');
	$( "#inp_kurangJantanCampur" ).val('0');
	$( "#inp_kurangJantanSeleksi" ).val('0');
	$( "#inp_kurangJantanLain" ).val('0');
	$( "#inp_populasiAkhirJantan" ).val('0');
	
	$( "#inp_populasiAwalBetina" ).val('0');
	$( "#inp_tambahBetina" ).val('0');
	$( "#inp_tambahBetinaLain" ).val('0');
	$( "#inp_kurangBetinaMati" ).val('0');
	$( "#inp_kurangBetinaAfkir" ).val('0');
	$( "#inp_kurangBetina" ).val('0');
	$( "#inp_kurangBetinaSexslip" ).val('0');
	$( "#inp_kurangBetinaKanibal" ).val('0');
	$( "#inp_kurangBetinaCampur" ).val('0');
	$( "#inp_kurangBetinaSeleksi" ).val('0');
	$( "#inp_kurangBetinaLain" ).val('0');
	$( "#inp_populasiAkhirBetina" ).val('0');
	
	$( "#inp_populasiAwalRasio" ).val('0');
	$( "#inp_populasiAkhirRasio" ).val('0');
	
	//Reset LHK-Pakan
	$('#lhk_pakan > tbody').html('');
	//Reset LHK-Obat
	var select_obat = '<select class="form-control input-sm"  name="inp_obat_kodebarang[]" onchange="setNamaBarang(this)">';
	select_obat += '<option value=""></option>';
	for(var i=0;i<barang_obat.length;i++){
		var obj = barang_obat[i];		
		select_obat += '<option value="' + obj[0] + '">' + obj[1] + '</option>';
	}
	select_obat += '</select>';
	
	html = ''+
	'<tr>'+
	'	<td>' + select_obat + '</td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_obat_namabarang[]" disabled></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_obat_keterangan[]"></td>'+
	'	<td class="vert-align col-md-1">'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahObat(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'			<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
	
	$("#lhk_obat > tbody").html(html);
	
	//Reset LHK-Vaksin
	var select_vaksin = '<select class="form-control input-sm"  name="inp_vaksin_kodebarang[]" onchange="setNamaBarang(this)">';
	select_vaksin += '<option value=""></option>';
	for(var i=0;i<barang_vaksin.length;i++){
		var obj = barang_vaksin[i];		
		select_vaksin += '<option value="' + obj[0] + '">' + obj[1] + '</option>';
	}
	select_vaksin += '</select>';
	
	html = ''+
	'<tr>'+
	'	<td>' + select_vaksin + '</td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_namabarang[]" disabled></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_keterangan[]"></td>'+
	'	<td class="vert-align col-md-1">'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahVaksin(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'			<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
			
	$("#lhk_vaksin > tbody").html(html);
	
	//Reset LHK-Produksi
	html = ''+
		'<tr>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_baik[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_besar[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_tipis[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_kecil[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_kotor[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_abnormal[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_ib[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_retak[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_hancur[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_jumlah[]" disabled></td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_prod_keterangan[]"></td>'+
		'	<td>'+
		'	<button type="button" data-toggle="tooltip" onclick="tambahProduksi(this)" title="Tambah" class="btn btn-sm btn-primary">'+
		'		<i class="glyphicon glyphicon-plus-sign"></i>'+
		'	</button>'+
		'	</td>'+
		'</tr>';
		
	$("#lhk_produksi > tbody").html(html);
	
	//Reset 
	$( "#inp_berat_telur" ).val('0');
	$( "#inp_cv_jantan" ).val('0');
	$( "#inp_cv_betina" ).val('0');
	
}

function resetInputLhk(){
	//Reset Header
	$( "#inp_umur" ).val('');
	$( "#inp_bb_ja" ).val('0');
	$( "#inp_bb_be" ).val('0');
	//Reset batas atas niput pamakaian pakan
	var batas_atas_pakan_jantan = 0;
	var batas_atas_pakan_betina = 0;
	//Reset input uniformity dan daya hidup
	$('#inp_uniformity_jantan').attr("disabled", true);
	$('#inp_uniformity_betina').attr("disabled", true);
	$('#inp_dayahidup_jantan').val('0');
	$('#inp_dayahidup_betina').val('0');
	//Reset LHK-Populasi
	$( "#inp_populasiAwalJantan" ).val('0');
	$( "#inp_tambahJantan" ).val('0');
	$( "#inp_tambahJantanLain" ).val('0');
	$( "#inp_kurangJantanMati" ).val('0');
	$( "#inp_kurangJantanAfkir" ).val('0');
	$( "#inp_kurangJantan" ).val('0');
	$( "#inp_kurangJantanSexslip" ).val('0');
	$( "#inp_kurangJantanKanibal" ).val('0');
	$( "#inp_kurangJantanCampur" ).val('0');
	$( "#inp_kurangJantanSeleksi" ).val('0');
	$( "#inp_kurangJantanLain" ).val('0');
	$( "#inp_populasiAkhirJantan" ).val('0');
	
	$( "#inp_populasiAwalBetina" ).val('0');
	$( "#inp_tambahBetina" ).val('0');
	$( "#inp_tambahBetinaLain" ).val('0');
	$( "#inp_kurangBetinaMati" ).val('0');
	$( "#inp_kurangBetinaAfkir" ).val('0');
	$( "#inp_kurangBetina" ).val('0');
	$( "#inp_kurangBetinaSexslip" ).val('0');
	$( "#inp_kurangBetinaKanibal" ).val('0');
	$( "#inp_kurangBetinaCampur" ).val('0');
	$( "#inp_kurangBetinaSeleksi" ).val('0');
	$( "#inp_kurangBetinaLain" ).val('0');
	$( "#inp_populasiAkhirBetina" ).val('0');
	
	$( "#inp_populasiAwalRasio" ).val('0');
	$( "#inp_populasiAkhirRasio" ).val('0');
	
	//Reset Modal RHK Pindah Populasi
	kandang_id_arr = new Array();
	jantan_arr = new Array();
	betina_arr = new Array();
	keterangan_arr = new Array();
	beritaacara_arr = new Array();
	
	//Reset Modal RHK Tambah Lain-lain
	tambah_jantan_lain_arr = new Array();
	tambah_betina_lain_arr = new Array();
	tambah_keterangan_lain_arr = new Array();
	tambah_noberitaacara_lain_arr = new Array();
	tambah_beritaacara_lain_arr = new Array();

	$('input[name^="slider_tambah_lain_jantan"]').each(function() {
		$(this).val('');
	});
	
	$('input[name^="slider_tambah_lain_betina"]').each(function() {
		$(this).val('');
	});
	
	$('input[name^="inp_tambah_jantan_lain_ket"]').each(function() {		
		$(this).val('');
	});
	
	$('input[name^="inp_tambah_jantan_lain_nomemo"]').each(function() {		
		$(this).val('');
	});
	
	$('input[name^=uploadFileTambahLain]').each(function(){
		var elm_input = $(this).parent().parent().parent().find('input')[1];
		$(elm_input).val('');				
	});
	
	//Reset Modal RHK Kurang Lain-lain
	kurang_jantan_lain_arr = new Array();
	kurang_betina_lain_arr = new Array();
	kurang_keterangan_lain_arr = new Array();
	kurang_noberitaacara_lain_arr = new Array();
	kurang_beritaacara_lain_arr = new Array();
	
	$('input[name^="slider_kurang_lain_jantan"]').each(function() {
		$(this).val('');
	});
	
	$('input[name^="slider_kurang_lain_betina"]').each(function() {
		$(this).val('');
	});
	
	$('input[name^="inp_kurang_jantan_lain_ket"]').each(function() {		
		$(this).val('');
	});
	
	$('input[name^="inp_kurang_jantan_lain_nomemo"]').each(function() {		
		$(this).val('');
	});
	
	$('input[name^=uploadFileKurangLain]').each(function(){
		var elm_input = $(this).parent().parent().parent().find('input')[1];
		$(elm_input).val('');				
	});
	
	//Reset LHK-Pakan
	$('#lhk_pakan > tbody').html('');
	//Reset LHK-Obat
	var select_obat = '<select class="form-control input-sm"  name="inp_obat_kodebarang[]" onchange="setNamaBarang(this)">';
	select_obat += '<option value=""></option>';
	for(var i=0;i<barang_obat.length;i++){
		var obj = barang_obat[i];		
		select_obat += '<option value="' + obj[0] + '">' + obj[1] + '</option>';
	}
	select_obat += '</select>';
	
	html = ''+
	'<tr>'+
	'	<td>' + select_obat + '</td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_obat_namabarang[]" disabled></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_obat_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_obat_keterangan[]"></td>'+
	'	<td class="vert-align col-md-1">'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahObat(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'			<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
	
	$("#lhk_obat > tbody").html(html);
	
	//Reset LHK-Vaksin
	var select_vaksin = '<select class="form-control input-sm"  name="inp_vaksin_kodebarang[]" onchange="setNamaBarang(this)">';
	select_vaksin += '<option value=""></option>';
	for(var i=0;i<barang_vaksin.length;i++){
		var obj = barang_vaksin[i];		
		select_vaksin += '<option value="' + obj[0] + '">' + obj[1] + '</option>';
	}
	select_vaksin += '</select>';
	
	html = ''+
	'<tr>'+
	'	<td>' + select_vaksin + '</td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_namabarang[]" disabled></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaijantan[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_vaksin_pakaibetina[]" onkeyup="cekDecimal(this)"></td>'+
	'	<td><input type="text" class="form-control input-sm" name="inp_vaksin_keterangan[]"></td>'+
	'	<td class="vert-align col-md-1">'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahVaksin(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'			<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
			
	$("#lhk_vaksin > tbody").html(html);
	
	//Reset LHK-Produksi
	html = ''+
		'<tr>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_baik[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_besar[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_tipis[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_kecil[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_kotor[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_abnormal[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_ib[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_retak[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_hancur[]" onkeyup="cekNumerikProduksi(this)"></td>'+
		'	<td><input type="text" class="form-control input-sm inp-numeric" value="0" name="inp_prod_jumlah[]" disabled></td>'+
		'	<td><input type="text" class="form-control input-sm" name="inp_prod_keterangan[]"></td>'+
		'	<td>'+
		'	<button type="button" data-toggle="tooltip" onclick="tambahProduksi(this)" title="Tambah" class="btn btn-sm btn-primary">'+
		'		<i class="glyphicon glyphicon-plus-sign"></i>'+
		'	</button>'+
		'	</td>'+
		'</tr>';
		
	$("#lhk_produksi > tbody").html(html);
	
	//Reset 
	$( "#inp_berat_telur" ).val('0');
	$( "#inp_cv_jantan" ).val('0');
	$( "#inp_cv_betina" ).val('0');
	
}

function disabledLhk(){
	$('#btnSimpan').attr("disabled", true);
	$('#btnTutupSiklus').attr("disabled", true);
	$('#btnSimpan').hide();
	$('#btnTutupSiklus').hide();
	$('#inp_bb_ja').attr("disabled", true);
	$('#inp_bb_be').attr("disabled", true);
	
	//Populasi JANTAN
	$('#inp_tambahJantanLain').attr("disabled", true);
	$('#inp_kurangJantanMati').attr("disabled", true);
	$('#inp_kurangJantanAfkir').attr("disabled", true);
	$('#btnBrowseKurangJantan').attr("disabled", true);
	$('#inp_kurangJantanSexslip').attr("disabled", true);
	$('#inp_kurangJantanKanibal').attr("disabled", true);
	$('#inp_kurangJantanCampur').attr("disabled", true);
	$('#inp_kurangJantanSeleksi').attr("disabled", true);
	$('#inp_kurangJantanLain').attr("disabled", true);
	
	//Populasi BETINA
	$('#inp_tambahBetinaLain').attr("disabled", true);
	$('#inp_kurangBetinaMati').attr("disabled", true);
	$('#inp_kurangBetinaAfkir').attr("disabled", true);
	$('#btnBrowseKurangBetina').attr("disabled", true);
	$('#inp_kurangBetinaSexslip').attr("disabled", true);
	$('#inp_kurangBetinaKanibal').attr("disabled", true);
	$('#inp_kurangBetinaCampur').attr("disabled", true);
	$('#inp_kurangBetinaSeleksi').attr("disabled", true);
	$('#inp_kurangBetinaLain').attr("disabled", true);
	$('#inp_populasiAkhirBetina').attr("disabled", true);
	
	//Pakan Kg dan Sak
	$('input[name^="inp_j_terpakaiKg"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_j_terpakaiSak"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_b_terpakaiKg"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_b_terpakaiSak"]').each(function() {
		$(this).attr("disabled", true);
	});
	
	//Obat
	$('select[name^="inp_obat_kodebarang"]').each(function() {
		$(this).attr("disabled", true);
		
		var tr = $(this).parent().parent();
		var tdButton = $(tr).find('td').eq(5).find('button');
		$(tdButton).attr("disabled", true);
	});
	$('input[name^="inp_obat_pakaijantan"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_obat_pakaibetina"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_obat_keterangan"]').each(function() {
		$(this).attr("disabled", true);
	});
	
	//Vaksin
	$('select[name^="inp_vaksin_kodebarang"]').each(function() {
		$(this).attr("disabled", true);
		
		var tr = $(this).parent().parent();
		var tdButton = $(tr).find('td').eq(5).find('button');
		$(tdButton).attr("disabled", true);
	});
	$('input[name^="inp_vaksin_pakaijantan"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_vaksin_pakaibetina"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_vaksin_keterangan"]').each(function() {
		$(this).attr("disabled", true);
	});
	
	//Produksi
	$('input[name^="inp_prod_baik"]').each(function() {
		$(this).attr("disabled", true);
		
		var tr = $(this).parent().parent();
		var tdButton = $(tr).find('td').eq(11).find('button');
		$(tdButton).attr("disabled", true);
	});
	$('input[name^="inp_prod_besar"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_tipis"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_kecil"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_kotor"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_abnormal"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_ib"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_retak"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_hancur"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_prod_keterangan"]').each(function() {
		$(this).attr("disabled", true);
	});
	
	//Telur
	$('#inp_berat_telur').attr("disabled", true);
	$('#inp_cv_jantan').attr("disabled", true);
	$('#inp_cv_betina').attr("disabled", true);
	$('#inp_uniformity_jantan').attr("disabled", true);
	$('#inp_uniformity_betina').attr("disabled", true);
}

function enabledLhk(){
	$('#btnSimpan').attr("disabled", false);
	$('#btnTutupSiklus').attr("disabled", false);
	$('#btnSimpan').show();
	$('#btnTutupSiklus').show();
	$('#inp_bb_ja').attr("disabled", false);
	$('#inp_bb_be').attr("disabled", false);
	
	//Populasi JANTAN
	$('#inp_tambahJantanLain').attr("disabled", true);
	$('#inp_kurangJantanMati').attr("disabled", false);
	$('#inp_kurangJantanAfkir').attr("disabled", false);
	$('#btnBrowseKurangJantan').attr("disabled", false);
	$('#inp_kurangJantanSexslip').attr("disabled", false);
	$('#inp_kurangJantanKanibal').attr("disabled", false);
	$('#inp_kurangJantanCampur').attr("disabled", false);
	$('#inp_kurangJantanSeleksi').attr("disabled", false);
	$('#inp_kurangJantanLain').attr("disabled", true);
	
	//Populasi BETINA
	$('#inp_tambahBetinaLain').attr("disabled", true);
	$('#inp_kurangBetinaMati').attr("disabled", false);
	$('#inp_kurangBetinaAfkir').attr("disabled", false);
	$('#btnBrowseKurangBetina').attr("disabled", false);
	$('#inp_kurangBetinaSexslip').attr("disabled", false);
	$('#inp_kurangBetinaKanibal').attr("disabled", false);
	$('#inp_kurangBetinaCampur').attr("disabled", false);
	$('#inp_kurangBetinaSeleksi').attr("disabled", false);
	$('#inp_kurangBetinaLain').attr("disabled", true);
	
	//Pakan Kg dan Sak
	$('input[name^="inp_j_terpakaiKg"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_j_terpakaiSak"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_b_terpakaiKg"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_b_terpakaiSak"]').each(function() {
		$(this).attr("disabled", false);
	});
	
	//Obat
	$('select[name^="inp_obat_kodebarang"]').each(function() {
		$(this).attr("disabled", false);
		
		var tr = $(this).parent().parent();
		var tdButton = $(tr).find('td').eq(5).find('button');
		$(tdButton).attr("disabled", false);
	});
	$('input[name^="inp_obat_pakaijantan"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_obat_pakaibetina"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_obat_keterangan"]').each(function() {
		$(this).attr("disabled", false);
	});
	
	//Vaksin
	$('select[name^="inp_vaksin_kodebarang"]').each(function() {
		$(this).attr("disabled", false);
		
		var tr = $(this).parent().parent();
		var tdButton = $(tr).find('td').eq(5).find('button');
		$(tdButton).attr("disabled", false);
	});
	$('input[name^="inp_vaksin_pakaijantan"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_vaksin_pakaibetina"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_vaksin_keterangan"]').each(function() {
		$(this).attr("disabled", false);
	});
	
	//Produksi
	$('input[name^="inp_prod_baik"]').each(function() {
		$(this).attr("disabled", false);
		
		var tr = $(this).parent().parent();
		var tdButton = $(tr).find('td').eq(11).find('button');
		$(tdButton).attr("disabled", false);
	});
	$('input[name^="inp_prod_besar"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_tipis"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_kecil"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_kotor"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_abnormal"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_ib"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_retak"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_hancur"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_prod_keterangan"]').each(function() {
		$(this).attr("disabled", false);
	});
	
	//Telur
	$('#inp_berat_telur').attr("disabled", false);
	$('#inp_cv_jantan').attr("disabled", false);
	$('#inp_cv_betina').attr("disabled", false);
}

function box_rfid_unused(){
	var judulKonfirm = "Konfirmasi";
	var box = bootbox.dialog({
			title: judulKonfirm,
			message: 
				'<div class="row">  ' +
				'<div class="col-md-12"> ' +
				'<form class="form-horizontal"> ' +
				'<div class="form-group"> ' +
				'<label class="col-md-4 control-label" for="name">Scan RFID</label> ' +
				'<div class="col-md-4"> ' +
				'<input id="inp_rfid" name="rfid" type="text" class="form-control input-md" autofocus title="RFID harus diisi"> ' +
				'</div> ' +
				'</div> ' +
				'</form> </div>  </div>',
			buttons: {
				success: {
					label: "OK",
					className: "btn-primary",
					callback: function () {
						var rfid = $('#inp_rfid').val();
						if(!empty(rfid)){
							if(rfid == selected_kode_verifikasi){
								
							}else{
								toastr.warning("Kode tidak sesuai",'Peringatan');
								box.find("input").val('');
								box.find("input").focus();
								return false;
							}
						}else{
							toastr.warning("Kode RFID harus diisi",'Peringatan');
							return false;
						}
					}
				}
			}
		}
	);

	box.bind('shown.bs.modal', function(){
		box.find("input").focus();
	});
}