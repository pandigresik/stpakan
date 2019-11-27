var lhk_state = "";
var date_now = "";
var selected_farm = "";
var selected_kandang = "";
var selected_noreg = "";
var selected_kode_verifikasi = "";
var selected_tgl_doc_in = "";
var selected_tgl_kebutuhan = "";
var tgl_lhk_now = "";
var kandang_in_farm = new Array();
var sekat_selected_kandang = 0;
var jml_doc_in = 0;
var batas_atas_pakan_campur = 0;
var selected_std_bdy = 0;
var bb_std = 0;
var umur_lhk = 0;
var bb_rata_last = 0;
var ket_penimbangan = 0;

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

	$('#div_tgl_lhk').datetimepicker({
		pickTime: false,
		format : "DD MMM YYYY"
	});
	var todayDbase = new Date($('#inp_today').val()); // format yyyy-mm-dd
	//alert($('#inp_today').val());
	$('#div_tgl_lhk').data("DateTimePicker").setMaxDate(new Date(todayDbase.setDate(todayDbase.getDate())));
	//$('#div_tgl_lhk').data("DateTimePicker").disable();

});

function setInputKandang(kode_farm){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_kandang_farm/",
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
					selected_tgl_kebutuhan = current.tgl_kebutuhan_awal;
					tgl = current.tgl_doc_in;
					tgl_kebutuhan = current.tgl_kebutuhan_awal;

					//selected_kode_verifikasi = current.kode_verifikasi;
					sekat_selected_kandang = current.jml_sekat;
					selected_std_bdy = current.kode_std_budidaya;

					var docInDate = new Date(selected_tgl_doc_in);
					var kebutuhanDate = new Date(selected_tgl_kebutuhan);
					var LhkDate = new Date(todayDbase);
					$('#div_tgl_lhk').data("DateTimePicker").setMinDate(new Date(kebutuhanDate.setDate(kebutuhanDate.getDate())));
					//$('#div_tgl_lhk').data("DateTimePicker").setMinDate(new Date(docInDate.setDate(docInDate.getDate())));
					//$('#div_tgl_lhk').data("DateTimePicker").setDate(new Date(LhkDate.setDate(LhkDate.getDate())));

					tgl_temp = tgl.split("-");
					ddDocIn = parseInt(tgl_temp[2]);
					mmDocIn = parseInt(tgl_temp[1]);
					yyDocIn = parseInt(tgl_temp[0]);

					tgl_kebutuhan_temp = tgl_kebutuhan.split("-");
					ddKebutuhan = parseInt(tgl_kebutuhan_temp[2]);
					mmKebutuhan = parseInt(tgl_kebutuhan_temp[1]);
					yyKebutuhan = parseInt(tgl_kebutuhan_temp[0]);

					last = '';

					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_last_lhk/",
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
							var dt = new Date(last);
							dt.setDate(dt.getDate() + 1);

							// t_arr = last.split('-');
							// ddLhk = (parseInt(t_arr[2]) + 1);
							// mmLhk = parseInt(t_arr[1]);
							// yyLhk = parseInt(t_arr[0]);

							ddLhk = dt.getDate();
							mmLhk = (dt.getMonth() + 1);
							yyLhk = dt.getFullYear();
						}else{
							// ddLhk = ddDocIn;
							// mmLhk = mmDocIn;
							// yyLhk = yyDocIn;

							ddLhk = ddKebutuhan;
							mmLhk = mmKebutuhan;
							yyLhk = yyKebutuhan;
						}

						$('#inp_flock').val(current.flok_bdy);
						$('#inp_doc_in').val(ddDocIn +' '+months[mmDocIn-1]+' '+yyDocIn);

						var pad = '00';
						var todayDate = new Date(todayDbase_tahun+"-"+(pad + (todayDbase_bulan).toString()).slice(-pad.length)+"-"+(pad + (todayDbase_hari).toString()).slice(-pad.length));
						var nextDate = new Date(yyLhk+"-"+(pad + (mmLhk).toString()).slice(-pad.length)+"-"+(pad + (ddLhk).toString()).slice(-pad.length));

						var day = nextDate.getDate();
						var monthIndex = nextDate.getMonth();
						var year = nextDate.getFullYear();

						// $( "#inp_tgl_lhk" ).val(day +' '+months[monthIndex-1]+' '+year);
						$( "#inp_tgl_lhk" ).val(day +' '+months[monthIndex]+' '+year);

						tgl_lhk_now = year+"-"+(pad + (monthIndex+1).toString()).slice(-pad.length)+"-"+(pad + (day).toString()).slice(-pad.length);

						var todayDay = todayDate.getDate();
						var todayMonth = todayDate.getMonth();
						var todayYear = todayDate.getFullYear();
						var todayString = todayYear.toString()+(todayMonth+1).toString()+todayDay.toString();
						var lhkString = year.toString()+(monthIndex+1).toString()+day.toString();

						resetInputLhk();

						//var umur_M = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) / 7;
						// var umur_H = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) % 7;
						var umur_H = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk));

						/*cara lain hitung selisih hari*/
						var date1 = new Date(yyDocIn.toString()+"-"+(pad + (mmDocIn).toString()).slice(-pad.length)+"-"+(pad + ddDocIn.toString()).slice(-pad.length));
						var date2 = new Date(yyLhk.toString()+"-"+(pad + (mmLhk).toString()).slice(-pad.length)+"-"+(pad + ddLhk.toString()).slice(-pad.length));
						var one_day=1000*60*60*24;
						var ddiff = Math.ceil((date2.getTime()-date1.getTime())/(one_day));
						// console.log(ddiff);

						// umur_minggu = Math.abs(Math.floor(umur_M));
						//console.log("umur_H : " + Math.abs(parseInt(umur_H))); //sebelum dirubah menjadi tgl_kebutuhan adalah : parseInt(umur_H)+1)
						// $( "#inp_umur" ).val(Math.abs(parseInt(umur_H)));
						$( "#inp_umur" ).val(Math.abs(parseInt(ddiff)));


						// umur_lhk = Math.abs(parseInt(umur_H));
						umur_lhk = Math.abs(parseInt(ddiff));

						var pad = '00';
						var todayString2 = todayYear.toString()+(pad + (todayMonth+1).toString()).slice(-pad.length)+(pad + todayDay.toString()).slice(-pad.length);
						var lhkString2   = year.toString()+(pad + (monthIndex+1).toString()).slice(-pad.length)+(pad + day.toString()).slice(-pad.length);

						if(!empty(last) && empty(data.ack_kf)){
							bootbox.alert("Terdapat LHK yang belum di-Ack");
						}
						else{
							if(umur_lhk>=28){
								bootbox.dialog({
									message: "Apakah terdapat Realisasi Panen?",
									title: "Konfirmasi",
									buttons: {
										success: {
											label: "Ya",
											className: "btn-primary",
											callback: function() {
												$.ajax({
													type:'POST',
													dataType: 'json',
													url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/cek_panen_exist/",
													data: {
														no_reg : selected_noreg
													}
												})
												.done(function(data){
													if(data.result=="success"){
														initializeData(todayString2, lhkString2);

														lhk_state = "WRITE";
													}else{
														bootbox.alert("Mohon entry realisasi panen terlebih dahulu");
													}
												})
												.fail(function(reason){
													console.info(reason);
												})
												.then(function(data){

												});
											}
										},
										danger: {
											label: "Tidak",
											className: "btn-default",
											callback: function() {
												initializeData(todayString2, lhkString2);

												lhk_state = "WRITE";


											}
										}
									}
								});


							}else{
								initializeData(todayString2, lhkString2);

								lhk_state = "WRITE";
							}
						}
					})
					.fail(function(reason){
						console.info(reason);
					})
					.then(function(data){

					});
					// This means the exact match is found. Use toLowerCase() if you want case insensitive match.
				} else {
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
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_last_lhk/",
		data: {
			no_reg : selected_noreg
		}
	})
	.done(function(data){
		var pad = '00';

		if(!empty(data.tgl_transaksi)){
			// var tgl_last_lhk = (data.tgl_transaksi).split('-');
			// var ddLhk = (parseInt(tgl_last_lhk[2]) + 1);
			// var mmLhk = parseInt(tgl_last_lhk[1]);
			// var yyLhk = parseInt(tgl_last_lhk[0]);

			var dt = new Date(data.tgl_transaksi);
			dt.setDate(dt.getDate() + 1);

			ddLhk = dt.getDate();
			mmLhk = dt.getMonth() + 1;
			yyLhk = dt.getFullYear();

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
			// var d = new Date(tahun, bulan, hari);
			var d = new Date(tahun+"-"+bulan+"-"+hari);
			d.setDate(d.getDate());
			//alert("selected_date:"++"-"+selected_date);

			lhk_state = "READ";
			LoadDataLHK(d.getFullYear()+"-"+(pad + ((d.getMonth()+1)).toString()).slice(-pad.length)+"-"+(pad + ((d.getDate())).toString()).slice(-pad.length));
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

function initializeData(todaydate, lhkdate){
	var status;
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
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_data_last_lhk/",
		data: {
			tgl_lhk : tgl_lhk_fix,
			no_reg : selected_noreg,
			tgl_doc_in : tgl_doc_fix
		}
	})
	.done(function(data){

		var jumlah_panen = data.jumlah_panen;
		var obj_populasi = data.populasi;
		var obj_pakan = data.pakan;
		// var obj_batas_pakai_pakan = data.batas_pakai_pakan;
		var obj_populasi_awal = data.populasi_awal;
		bb_rata_last = (data.bb_rata_last * 1);
		console.log("bb_rata_last:"+bb_rata_last);

		// batas_atas_pakan_campur = obj_batas_pakai_pakan.C;
		// console.log("maks pakan:"+batas_atas_pakan_campur);
		var sekat = new Array();
		for(var i=0; i<sekat_selected_kandang; i++){
			sekat[i] = ''+
			'<tr>'+
			'	<td align="center"><input type="hidden" id="inp_sekat_no_'+i+'" name="sekat_no[]" value="'+(i+1)+'">Sekat '+(i+1)+'</td>'+
			'	<td><input type="text" id="inp_sekat_'+i+'" name="sekat_jml[]" style="width:100px" class="form-control input-sm field_input" onkeyup="cekNumerik(this)"></td>'+
			'	<td><input type="text" id="inp_bb_'+i+'" name="sekat_bb[]" style="width:100px" class="form-control input-sm field_input" onkeyup="cekNumerik(this)"></td>'+
			'	<td><input type="text" id="inp_ket_'+i+'" name="sekat_ket[]" style="width:100%" class="form-control input-sm field_input"></td>'+
			'</tr>';
		}

		$('#lhk_sekat > tbody').html(sekat.join(''));

		// var pa = obj_populasi.jml;
		var pa = empty(obj_populasi.jml) ? 0 : obj_populasi.jml;
		var jml_doc_in = obj_populasi.jml_doc_in;
		// var dh = parseFloat((pa - parseInt(jumlah_panen)) * 100 / jml_doc_in);
		console.log("pa:"+pa);
		var dh = parseFloat((pa - parseInt(jumlah_panen)) * 100 / obj_populasi_awal.jml_populasi_awal);
		console.log(jml_doc_in);

		$('#inp_populasiAwal').val(pa);
		$('#inp_populasiAkhir').val(parseInt(pa) - parseInt(jumlah_panen));
		$('#inp_doc_in_campur').val(jml_doc_in);

		$('#inp_panen').val(jumlah_panen);
		if(umur_lhk >= 8)
			$('#inp_dayahidup').val(Number(Math.round(dh * 100) / 100).toFixed(2));
		else
			$('#inp_dayahidup').val('-');

		$('#inp_dayahidup_temp').val(Number(Math.round(dh * 100) / 100).toFixed(2));

		var pakan = new Array(),
			i = 0;

		if(!empty(obj_pakan)){
			for(var j=0;j<obj_pakan.length;j++){
				var obj = obj_pakan[j];
				if(obj.jk == 'C'){
					var totKg = parseFloat(obj.berat_awal) - parseFloat(obj.brt_sak) + parseFloat(obj.berat_kirim);
					var tot = parseInt(obj.jml_awal) - obj.jml_retur + parseInt(obj.jml_kirim);
					pakan[i] = ''+
					'<tr>'+
					'<td class="vert-align">' + obj.jenis_kelamin + '</td>'+
					'	<td class="vert-align" >' +
							obj.nama_barang +
							'<input type="hidden" class="form-control input-sm" name="inp_c_nama_pakan[]" value="' + obj.nama_barang + '">' +
							'<input type="hidden" class="form-control input-sm" name="inp_c_pakan[]" value="' + obj.kode_barang + '">' +
							'<input type="hidden" class="form-control input-sm" name="inp_c_bentuk_pakan[]" value="' + obj.bentuk_barang + '">' +
					'	</td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAwalKg[]" value="' + obj.berat_awal + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAwalSak[]" value="' + obj.jml_awal + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokReturKg[]" value="' + obj.brt_sak + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokReturSak[]" value="' + obj.jml_retur + '" disabled></td>'+
					// '	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimKg[]" value="' + obj.berat_akhir + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimKg[]" value="' + obj.berat_kirim + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimSak[]"  value="' + obj.jml_kirim + '"disabled></td>'+
					// '	<td><input type="text" data-jeniskelamin="J" class="form-control input-sm inp-numeric" name="inp_c_terpakaiKg[]" value="0" onkeyup="return cekNumerikPakanKg(event, this)" title="Kg harus lebih besar dari jumlah Sak" disabled></td>'+
					'	<td><input type="text" data-jeniskelamin="J" class="form-control input-sm inp-numeric" name="inp_c_terpakaiKg[]" value="0" onchange="return cekNumerikPakanKg(event, this)" title="Kg harus lebih besar dari jumlah Sak" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_terpakaiSak[]" value="0" onkeyup="cekNumerikPakanSak(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirKg[]" value="' + Number(totKg).toFixed(3) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirSak[]" value="' + tot + '" disabled></td>'+
					// '	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirSak[]" value="' + obj.jml_akhir  + '" disabled></td>'+
					//'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirSak[]" value="' + tot + '" disabled></td>'+
					'</tr>';

					i++;
				}
			}
		}

		var pakan_all = pakan;

		$('#lhk_pakan > tbody').html(pakan_all.join(''));

		if(umur_lhk < 8){
			$('#inp_populasi_awal_stlh_umur_7').val(obj_populasi_awal.jml_populasi_awal);
			$('#inp_populasi_awal_stlh_umur_7_temp').val(obj_populasi_awal.jml_populasi_awal);
		}else{
			$('#inp_populasi_awal_stlh_umur_7').val(obj_populasi_awal.populasi_awal_7);
			$('#inp_populasi_awal_stlh_umur_7_temp').val(obj_populasi_awal.populasi_awal_7);
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
		defineEnableDisabledInput(todaydate, lhkdate);
		var umur_H = $( "#inp_umur" ).val();

		set_bb_std(Math.abs(parseInt(umur_H)), selected_noreg, selected_farm);
	});
};

function set_bb_std(umur, selected_noreg, kode_farm){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_bb_std/",
		data: {
			umur : umur,
			kode_std_budidaya : selected_std_bdy
		}
	})
	.done(function(data){
		bb_std = data.target_bb;
		// console.log("std_bb:"+bb_std);
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

	resetInputLhk();

	/*cara lain hitung selisih hari*/
	var pad = '00';
	var date1 = new Date(yyDocIn.toString()+"-"+(pad + (mmDocIn).toString()).slice(-pad.length)+"-"+(pad + ddDocIn.toString()).slice(-pad.length));
	var date2 = new Date(yyLhk.toString()+"-"+(pad + (mmLhk).toString()).slice(-pad.length)+"-"+(pad + ddLhk.toString()).slice(-pad.length));
	var one_day=1000*60*60*24;
	var ddiff = Math.ceil((date2.getTime()-date1.getTime())/(one_day));
	console.log(ddiff);

	// var umur_M = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) / 7;
	// var umur_H = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk)) % 7;
	var umur_H = daydiff(new Date(yyDocIn, mmDocIn, ddDocIn), new Date(yyLhk, mmLhk, ddLhk));


	// $( "#inp_umur" ).val(Math.abs(parseInt(umur_H))); //sebelum dirubah menjadi tgl_kebutuhan adalah : parseInt(umur_H)+1)
	$( "#inp_umur" ).val(Math.abs(parseInt(ddiff))); //sebelum dirubah menjadi tgl_kebutuhan adalah : parseInt(umur_H)+1)
	// umur_lhk = (Math.abs(parseInt(umur_H)));
	umur_lhk = (Math.abs(parseInt(ddiff)));
	console.log("umur:" + umur_lhk);

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

	initializeData(todayString, lhkString);
	// if(parseInt(todayString)<=parseInt(lhkString)){
		// disabledLhk();
	// }else{
		// enabledLhk();
	// }
}

function defineEnableDisabledInput(today, lhkdate){
	if(parseInt(today)<=parseInt(lhkdate)){
		disabledLhk();
	}else{
		enabledLhk();
	}
}

function LoadDataLHK(tgl_lhk){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/go_view_lhk/",
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
			var tgl_lhk = data.tgl_lhk;
			var umur = data.umur;
			var fcr = data.fcr;
			var ip = data.ip;
			var adg = data.adg;
			var bb_rata = rhk.C_BERAT_BADAN;
			var class_pakan = data.class_pakan;
			var jumlah_panen = rhk.jumlah_panen;

			//header
			$('#inp_umur').val(umur);
			if(bb_rata > 0 && umur >= 8){
				$('#inp_fcr').val(Number(Math.round(fcr * 1000) / 1000).toFixed(3));
				$('#inp_ip').val(Number(Math.round(ip * 1000) / 1000).toFixed(0));
				$('#inp_adg').val(adg);
				$('#inp_bb_rata').val(Number(Math.round(bb_rata * 1000) / 1000).toFixed(3));
			}else{
				$('#inp_fcr').val("-");
				$('#inp_ip').val("-");
				$('#inp_adg').val("-");
				$('#inp_bb_rata').val("-");
			}
			//rhk penimbangan
			var rhk_penimbangan_arr = new Array();
			for(var i=0;i<rhk_penimbangan.length;i++){
				var bb_jml = (rhk_penimbangan[i]["JUMLAH"] == 0) ? "" : rhk_penimbangan[i]["JUMLAH"];
				var bb_berat = (rhk_penimbangan[i]["BERAT"] == 0) ? "" : rhk_penimbangan[i]["BERAT"];
				var bb_ket = (bb_jml == 0 && bb_berat == 0) ? "" : rhk_penimbangan[i]["KETERANGAN"];

				rhk_penimbangan_arr[i] = ''+
					'<tr>'+
						'<td align="center"><input type="hidden" value="'+(i+1)+'" name="sekat_no[]" id="inp_sekat_no_'+i+'">Sekat '+(i+1)+'</td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_jml+'" style="width:100px" name="sekat_jml[]" id="inp_sekat_0" disabled="disabled"></td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_berat+'" style="width:100px" name="sekat_bb[]" id="inp_bb_0" disabled="disabled"></td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_ket+'" style="width:100%" name="sekat_ket[]" id="inp_ket_0" disabled="disabled"></td>'+
					'</tr>';
			}
			$('#lhk_sekat > tbody').html(rhk_penimbangan_arr.join(''));

			//rhk_populasi

			var populasiAwal = parseInt(rhk.C_MATI) + parseInt(rhk.C_AFKIR) + parseInt(rhk.C_KURANG_LAIN) + parseInt(rhk.C_JUMLAH)+ parseInt(rhk.jumlah_panen);

			$('#inp_populasiAwal').val(populasiAwal);
			$('#inp_tambahLain').val(rhk.C_TERIMA_LAIN);
			$('#inp_kurangMati').val(rhk.C_MATI);
			$('#inp_kurangAfkir').val(rhk.C_AFKIR);
			$('#inp_kurangLain').val(rhk.C_KURANG_LAIN);
			$('#inp_populasiAkhir').val(rhk.C_JUMLAH);
			$('#inp_populasi_awal_stlh_umur_7').val(rhk.C_AWAL);
			$('#inp_populasi_awal_stlh_umur_7_temp').val(rhk.C_AWAL);
			$('#inp_panen').val(jumlah_panen);

			if(umur >=8)
				$('#inp_dayahidup').val(Number(Math.round(rhk.C_DAYA_HIDUP * 100) / 100).toFixed(2));
			else
				$('#inp_dayahidup').val('-');

			//rhk_pakan
			var pakan_c = new Array(),
				i = 0;

			for(var j=0;j<rhk_pakan.length;j++){
				var obj = rhk_pakan[j];

				if(obj.JENIS_KELAMIN == 'C'){
					var totKg = parseFloat(obj.berat_awal) + parseFloat(obj.berat_kirim);
					var tot = parseInt(obj.jml_awal) + parseInt(obj.jml_kirim);

					var awalKg = parseFloat(obj.BRT_AKHIR) + parseFloat(obj.brt_sak) + parseFloat(obj.BRT_PAKAI) - parseFloat(obj.BRT_TERIMA);
					var awalStok = parseInt(obj.JML_AKHIR) + parseInt(obj.jml_retur) + parseInt(obj.JML_PAKAI) - parseInt(obj.JML_TERIMA);

					pakan_c[i] = ''+
					'<tr>'+
					'<td class="vert-align">' + 'CAMPUR'+ '</td>'+
					'	<td class="vert-align" >' + obj.NAMA_BARANG + '</td>'+
					// '	<td class="inp-numeric">' + obj.BRT_AWAL + '</td>'+
					'	<td class="inp-numeric">' + Number(Math.round(awalKg * 1000) / 1000).toFixed(3) + '</td>'+
					// '	<td class="inp-numeric">' + obj.JML_AWAL + '</td>'+
					'	<td class="inp-numeric">' + awalStok + '</td>'+

					'	<td class="inp-numeric">' + Number(Math.round(obj.brt_sak * 1000) / 1000).toFixed(3) + '</td>'+
					// '	<td class="inp-numeric">' + obj.JML_AWAL + '</td>'+
					'	<td class="inp-numeric">' + obj.jml_retur + '</td>'+

					'	<td class="inp-numeric">' + obj.BRT_TERIMA + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_TERIMA + '</td>'+
					'	<td class="inp-numeric" '+class_pakan[obj.JENIS_KELAMIN]+'>' + obj.BRT_PAKAI + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_PAKAI + '</td>'+
					'	<td class="inp-numeric">' + obj.BRT_AKHIR + '</td>'+
					'	<td class="inp-numeric">' + obj.JML_AKHIR + '</td>'+
					'</tr>';

					i++;
				}
			}

			var pakan_all = pakan_c;
			$('#lhk_pakan > tbody').html(pakan_all.join(''));

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
	});
}

function hapusRow(elm){
	var td = $(elm).parent().parent();
	td.remove();
}

function daydiff(first, second) {
    return Math.floor((second-first)/(1000*60*60*24));
}

Array.prototype.removeDuplicates = function (){
	var temp=new Array();
	this.sort();
	for(i=0;i<this.length;i++){
		if(this[i]==this[i+1]) {continue}
		temp[temp.length]=this[i];
	}
	return temp;
}

$('#btnTestLoad').click(function(){
	var win = window.open('riwayat_harian_kandang/test_load', '_blank');
	win.focus();
});

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

$('#btnSimpan').click(function(){

	//Lhk Pakan - Jantan
	var c_pakan = new Array(),
		c_totalPakanKg = 0,
		passed = true;

	$('input[name^="inp_c_pakan"]').each(function() {
		c_pakan.push($(this).val());
	});

	if(c_pakan.length == 0){

		toastr.warning("Laporan Harian Kandang - Pakan harus di isi",'Peringatan');

		return false;
	}

	$('input[name^="inp_c_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_totalPakanKg += parseFloat(jml);

		var tr = $(this).parent().parent();
		var sak = $(tr).find('td').eq(3).find('input').val();
		var n_sak = parseInt(sak);

		if(jml <= 0 && n_sak > 0){
			passed = false;
			//passed = true;
		}
	});

	/*Tambahan baru 15102016*/
	//Penimbangan Berat Badan
	var sekat_no = new Array(),
		sekat_jml = new Array(),
	    sekat_bb = new Array(),
		sekat_ket = new Array(),
		sekat_jml_tot = 0,
		sekat_bb_tot = 0;

	$('input[name^="sekat_no"]').each(function() {
		sekat_no.push($(this).val());
	});

	$('input[name^="sekat_jml"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		sekat_jml.push(jml);
		sekat_jml_tot += parseInt(jml);
	});

	$('input[name^="sekat_bb"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		sekat_bb.push(jml);
		sekat_bb_tot += parseFloat(jml);
	});

	$('input[name^="sekat_ket"]').each(function() {
		sekat_ket.push($(this).val());
	});

	if(umur_lhk == 1 || umur_lhk == 7 || umur_lhk == 14 || umur_lhk == 21 || umur_lhk == 28){
		console.log(sekat_jml_tot + "-" + sekat_bb_tot);
		if(sekat_jml_tot == 0 || sekat_bb_tot == 0){
			bootbox.alert("Jumlah dan berat penimbangan harus diisi!");

			return false;
		}
	}


//	if(!passed){
		//bootbox.alert("Jumlah Kg Pakan terpakai masih ( 0 )");
//	}else{
		//Sistem memeriksa entrian timbangan BB
		//step a
		if(sekat_no.length > 0 && sekat_bb_tot > 0 && sekat_jml_tot > 0){



			var bb_rata = parseFloat(sekat_bb_tot/sekat_jml_tot) / 1000;
			/*
			Jika BB rata-rata yang dientri (saat ini) < BB rata-rata penimbangan terakhir,
			sistem menampilkan confirmation message “BB rata-rata penimbangan saat ini lebih kecil dari penimbangan sebelumnya.
			Apakah BB rata-rata yang dientri sudah benar?”
			*/

			//step b
			console.log(parseFloat(bb_rata)+'-'+parseFloat(bb_rata_last));
			if(parseFloat(bb_rata) < parseFloat(bb_rata_last)){
				bootbox.dialog({
					message: "BB rata-rata penimbangan saat ini lebih kecil dari penimbangan sebelumnya. Apakah BB rata-rata yang dientri sudah benar?",
					title: "Konfirmasi",
					buttons: {
						success: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								//step c
								var v_awal = $('#inp_populasiAwal').val();
								var v_mati = $('#inp_kurangMati').val();
								var v_pop_prc = (parseFloat(v_mati/v_awal)*100);

								if(v_pop_prc > 0.07){
									bootbox.dialog({
										message: "Apakah Anda yakin mengentri jumlah mati sebesar n ekor?<br/>*) Kesalahan entri akan memberi dampak pada performance kandang.",
										title: "Konfirmasi",
										buttons: {
											success: {
												label: "Ya",
												className: "btn-primary",
												callback: function() {
													//show pop up pengisian keterangan
													$('#modal_pengisian_keterangan').modal({
														backdrop: 'static',
														keyboard: false
													}).show();
													$('#inp_pengisian_keterangan').focus();
												}
											},
											danger: {
												label: "Tidak",
												className: "btn-default",
												callback: function() {
													//step f

												}
											}
										}
									});
								}
								else{
									bootbox.dialog({
										message: "Apakah Anda yakin melakukan penyimpanan (..) ?",
										title: "Konfirmasi",
										buttons: {
											success: {
												label: "Ya",
												className: "btn-primary",
												callback: function() {
													simpan_transaksi_verifikasi(function(result){
														if(result.date_transaction){
															var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
															var box = bootbox.dialog({
																message : _message,
																closeButton: false,
																title : "Fingerprint",
																buttons : {
																	success : {
																		label : "Batal",
																		className : "btn-danger",
																		callback : function() {
																			timer = false;
																			tkode_pegawai = '';
																			tnama_pegawai = '';
																			return true;
																		}
																	}
																}
															});

															box.bind('shown.bs.modal', function() {
																timer = true;
																tkode_pegawai = '';
																tnama_pegawai = '';
																cek_verifikasi(result.date_transaction);
															});

															box.bind('hidden.bs.modal', function() {
																if(tkode_pegawai && tnama_pegawai){
																	simpanLhk('N', 0);
																}
																else{
																	toastr.warning('Verifikasi fingerprint tidak berhasil.','Warning');
																}
															});
														}
													});
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
							}
						},
						danger: {
							label: "Tidak",
							className: "btn-default",
							callback: function() {
								//step f

							}
						}
					}
				});
			}
			else{
				//step c
				var v_awal = $('#inp_populasiAwal').val();
				var v_mati = $('#inp_kurangMati').val();
				var v_pop_prc = (parseFloat(v_mati/v_awal)*100);

				if(v_pop_prc > 0.07){
					bootbox.dialog({
						message: "Apakah Anda yakin mengentri jumlah mati sebesar n ekor?<br/>*) Kesalahan entri akan memberi dampak pada performance kandang.",
						title: "Konfirmasi",
						buttons: {
							success: {
								label: "Ya",
								className: "btn-primary",
								callback: function() {
									//show pop up pengisian keterangan
									$('#modal_pengisian_keterangan').modal({
										backdrop: 'static',
										keyboard: false
									}).show();
									$('#inp_pengisian_keterangan').focus();
								}
							},
							danger: {
								label: "Tidak",
								className: "btn-default",
								callback: function() {
									//step f

								}
							}
						}
					});
				}
				else{
					bootbox.dialog({
					message: "Apakah Anda yakin melakukan penyimpanan?",
					title: "Konfirmasi",
					buttons: {
						success: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								simpan_transaksi_verifikasi(function(result){
									if(result.date_transaction){
										var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
										var box = bootbox.dialog({
											message : _message,
											closeButton: false,
											title : "Fingerprint",
											buttons : {
												success : {
													label : "Batal",
													className : "btn-danger",
													callback : function() {
														timer = false;
														tkode_pegawai = '';
														tnama_pegawai = '';
														return true;
													}
												}
											}
										});

										box.bind('shown.bs.modal', function() {
											timer = true;
											tkode_pegawai = '';
											tnama_pegawai = '';
											cek_verifikasi(result.date_transaction);
										});

										box.bind('hidden.bs.modal', function() {
											if(tkode_pegawai && tnama_pegawai){
												simpanLhk('N', 0);
											}
											else{
												toastr.warning('Verifikasi fingerprint tidak berhasil.','Warning');
											}
										});
									}
								});
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
			}
		}
		else{
			//step c
			var v_awal = $('#inp_populasiAwal').val();
			var v_mati = $('#inp_kurangMati').val();
			var v_pop_prc = (parseFloat(v_mati/v_awal)*100);
			console.log(v_pop_prc);
			if(v_pop_prc > 0.07){
				bootbox.dialog({
					message: "Apakah Anda yakin mengentri jumlah mati sebesar n ekor?<br/>*) Kesalahan entri akan memberi dampak pada performance kandang.",
					title: "Konfirmasi",
					buttons: {
						success: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								//show pop up pengisian keterangan
								$('#modal_pengisian_keterangan').modal({
									backdrop: 'static',
									keyboard: false
								}).show();
								$('#inp_pengisian_keterangan').focus();
							}
						},
						danger: {
							label: "Tidak",
							className: "btn-default",
							callback: function() {
								//step f

							}
						}
					}
				});
			}
			else{
				bootbox.dialog({
					message: "Apakah Anda yakin melakukan penyimpanan?",
					title: "Konfirmasi",
					buttons: {
						success: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								simpan_transaksi_verifikasi(function(result){
									if(result.date_transaction){
										var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
										var box = bootbox.dialog({
											message : _message,
											closeButton: false,
											title : "Fingerprint",
											buttons : {
												success : {
													label : "Batal",
													className : "btn-danger",
													callback : function() {
														timer = false;
														tkode_pegawai = '';
														tnama_pegawai = '';
														return true;
													}
												}
											}
										});

										box.bind('shown.bs.modal', function() {
											timer = true;
											tkode_pegawai = '';
											tnama_pegawai = '';
											cek_verifikasi(result.date_transaction);
										});

										box.bind('hidden.bs.modal', function() {
											if(tkode_pegawai && tnama_pegawai){
												simpanLhk('N', 0);
											}
											else{
												toastr.warning('Verifikasi fingerprint tidak berhasil.','Warning');
											}
										});
									}
								});
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
		}

		/*End of tambahan baru*/
	//}
});

$('#btnTutupSiklus').click(function(){
	var pakan_sisa_kg = new Array();
	var pakan_sisa_sak = new Array();

	$('input[name^="inp_c_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_kg.push(jml);
	});
	$('input[name^="inp_c_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_sak.push(jml);
	});

	//Lhk Pakan - Jantan
	var c_pakan = new Array(),
		c_totalPakanKg = 0,
		passed = true;

	$('input[name^="inp_c_pakan"]').each(function() {
		c_pakan.push($(this).val());
	});

	if(c_pakan.length == 0){
		toastr.warning("Laporan Harian Kandang - Pakan harus di isi",'Peringatan');

		return false;
	}

	$('input[name^="inp_c_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_totalPakanKg += parseFloat(jml);
	});

	//Penimbangan Berat Badan
	var sekat_no = new Array(),
		sekat_jml = new Array(),
	    sekat_bb = new Array(),
		sekat_ket = new Array(),
		sekat_jml_tot = 0,
		sekat_bb_tot = 0;

	$('input[name^="sekat_no"]').each(function() {
		sekat_no.push($(this).val());
	});

	$('input[name^="sekat_jml"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		sekat_jml.push(jml);
		sekat_jml_tot += parseInt(jml);
	});

	$('input[name^="sekat_bb"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		sekat_bb.push(jml);
		sekat_bb_tot += parseFloat(jml);
	});

	$('input[name^="sekat_ket"]').each(function() {
		sekat_ket.push($(this).val());
	});

	/*for(var i=0;i<sekat_no.length;i++){
		console.log(sekat_jml[i]+'-'+sekat_jml[i]);

		if(sekat_jml[i] == 0 || sekat_bb[i] <= 0){
			bootbox.alert("Jumlah dan Berat Penimbangan tidak boleh kosong/nol");
			return false;
		}
	}*/

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

					simpan_transaksi_verifikasi(function(result){
						if(result.date_transaction){
							var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
							var box = bootbox.dialog({
								message : _message,
								closeButton: false,
								title : "Fingerprint",
								buttons : {
									success : {
										label : "Batal",
										className : "btn-danger",
										callback : function() {
											timer = false;
											tkode_pegawai = '';
											tnama_pegawai = '';
											return true;
										}
									}
								}
							});

							box.bind('shown.bs.modal', function() {
								timer = true;
								tkode_pegawai = '';
								tnama_pegawai = '';
								cek_verifikasi(result.date_transaction);
							});

							box.bind('hidden.bs.modal', function() {
								if(tkode_pegawai && tnama_pegawai){
									simpanLhk('Y', totalStokAkhir);
									$('#btnSimpan').addClass("disabled");
								}
								else{
									toastr.warning('Verifikasi fingerprint tidak berhasil.','Warning');
								}
							});
						}
					});
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
							url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/buat_pengajuan_retur/",
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
							url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/buat_pengajuan_retur/",
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
	var populasi_awal_temp = $('#inp_populasi_awal_stlh_umur_7_temp').val();
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

	var jml_doc_in = $('#inp_doc_in_campur').val();
	var vc1 = $(tr).find('td').eq(0).find('input').val();
	var vc2 = $(tr).find('td').eq(1).find('input').val();
	var vc3 = $(tr).find('td').eq(2).find('input').val();
	var vc4 = $(tr).find('td').eq(3).find('input').val();
	var vc5 = $(tr).find('td').eq(5).find('input').val();
	var col6 = $(tr).find('td').eq(6).find('input');

	vc1 = (!empty(vc1)) ? parseInt(vc1) : 0;
	vc2 = (!empty(vc2)) ? parseInt(vc2) : 0;
	vc3 = (!empty(vc3)) ? parseInt(vc3) : 0;
	vc4 = (!empty(vc4)) ? parseInt(vc4) : 0;
	vc5 = (!empty(vc5)) ? parseInt(vc5) : 0;

	var jml = vc1+vc2-vc3-vc4-vc5;
	var populasi_awal = $('#inp_populasi_awal_stlh_umur_7').val();

	if(umur_lhk < 8){
		var pop_awal_lalu = $('#inp_populasi_awal_stlh_umur_7_temp').val();
		var pop_tambah_lain = $('#inp_tambahLain').val();
		var pop_afkir = $('#inp_kurangAfkir').val();
		var pop_panen = $('#inp_panen').val();

		var awal = (pop_awal_lalu != "") ? parseInt(pop_awal_lalu) : 0;
		var tambah_lain = (pop_tambah_lain != "") ? parseInt(pop_tambah_lain) : 0;
		var afkir = (pop_afkir != "") ? parseInt(pop_afkir) : 0;
		var panen = (pop_panen != "") ? parseInt(pop_panen) : 0;

		populasi_awal = awal + tambah_lain - afkir;
		$('#inp_populasi_awal_stlh_umur_7').val(populasi_awal);
	}
	console.log("populasi_awal:"+populasi_awal);

	var dh = parseFloat(parseInt(jml) * 100 / parseInt(populasi_awal));

	if(jml < 0){
		if(td_index < 2){
			jml = parseInt(jml) - parseInt(field.value);
		}else{
			jml = parseInt(jml) + parseInt(field.value);
		}

		$(field).val('0');
	}

	$(col6).val(jml);
	if(umur_lhk >= 8)
		$('#inp_dayahidup').val(Number(Math.round(dh * 100) / 100).toFixed(2));
	else
		$('#inp_dayahidup').val('-');

	$('#inp_dayahidup_temp').val(Number(Math.round(dh * 100) / 100).toFixed(2));
	console.log($('#inp_dayahidup_temp').val());
}

function cekNumerikPakanKg(evt, field){
	var jenis_kelamin = $(field).attr("data-jeniskelamin");

	// var charCode = (evt.which) ? evt.which : evt.keyCode;
	// console.log("keyCode:"+charCode);
	//console.log("charCOde:" + charCode);
	// if (
		// (charCode != 45 || $(field).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
		// (charCode != 46 || $(field).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
		// (((charCode < 48) && charCode > 57) || (charCode <= 96 && charCode >= 105)) &&
		// (charCode != 8 && charCode != 9 && charCode != 37 && charCode != 38 && charCode != 39 && charCode != 40)){
		// console.log("masuk");
		// $(field).val(($(field).val()).replace(/[^0-9-'.']/g,""));
		// return false;

	// }else{
		// console.log("g masuk");
	// }

	var regxp = /^\d*\.?\d*$/;
	var txt = $(field).val();

	if(regxp.test(txt)){
		var td = $(field).parent();
		var tr = $(td).parent();

		var stokAwal = $(tr).find('td').eq(2).find('input');
		var stokRetur = $(tr).find('td').eq(4).find('input');
		var stokKirim = $(tr).find('td').eq(6).find('input');
		var stokAkhir = $(tr).find('td').eq(10).find('input');
		var nilai = parseFloat($(stokAwal).val()) - parseFloat($(stokRetur).val()) + parseFloat($(stokKirim).val()) - parseFloat(field.value);

		if(nilai < 0){
			nilai = nilai + parseFloat(field.value);
			$(field).val("0");
		}

		$(stokAkhir).val(Number(Math.round(nilai * 1000) / 1000).toFixed(3));

		return true;
	}else{
		$(field).val("0");

		return false;
	}

	// if(!empty($(field).val())){
	// }else{
		// $(field).val("0");
	// }

	// var td = $(field).parent();
	// var tr = $(td).parent();

	// var stokAwal = $(tr).find('td').eq(2).find('input');
	// var stokRetur = $(tr).find('td').eq(4).find('input');
	// var stokKirim = $(tr).find('td').eq(6).find('input');
	// var stokAkhir = $(tr).find('td').eq(10).find('input');
	// var nilai = parseFloat($(stokAwal).val()) - parseFloat($(stokRetur).val()) + parseFloat($(stokKirim).val()) - parseFloat(field.value);

	// if(nilai < 0){
		// nilai = nilai + parseFloat(field.value);
		// $(field).val("0");
	// }

	// $(stokAkhir).val(Number(Math.round(nilai * 1000) / 1000).toFixed(3));

	// return true;
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

	var stokAwalKg = $(tr).find('td').eq(2).find('input');
	var stokAwal = $(tr).find('td').eq(3).find('input');
	var stokReturKg = $(tr).find('td').eq(4).find('input');
	var stokRetur = $(tr).find('td').eq(5).find('input');
	var stokKirimKg = $(tr).find('td').eq(6).find('input');
	var stokKirim = $(tr).find('td').eq(7).find('input');
	var stokTerpakaiKg = $(tr).find('td').eq(8).find('input');
	var stokAkhirKg = $(tr).find('td').eq(10).find('input');
	var stokAkhir = $(tr).find('td').eq(11).find('input');

	var nilai = parseInt($(stokAwal).val()) - parseInt($(stokRetur).val()) + parseInt($(stokKirim).val()) - parseInt(field.value);
	var nilaiKg = parseFloat($(stokAwalKg).val()) - parseFloat($(stokReturKg).val()) + parseFloat($(stokKirimKg).val()) - parseFloat($(stokTerpakaiKg).val());

	// if((parseInt(field.value) > 0 && parseFloat($(stokTerpakaiKg).val()) <= 0)){
		// nilai = nilai + parseInt(field.value);
		// $(field).val("0");

		// $(stokAkhir).val(nilai);

		// return false;
	// }

	if( nilai < 0){
		nilai = nilai + parseInt(field.value);
		nilaiKg = nilaiKg + parseFloat($(stokTerpakaiKg).val());
		$(field).val("0");
		$(stokTerpakaiKg).val("0");

		$(stokAkhir).val(nilai);
		$(stokAkhirKg).val(Number(Math.round(nilaiKg * 1000) / 1000).toFixed(3));

		return false
	}

	$(stokAkhir).val(nilai);

	var kode_barang = $(tr).find('td').eq(1).find('[name^=inp_c_pakan]').val();
	var stok = $(field).val();
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_berat_pakan/",
		data: {
			stok : stok,
			no_reg : selected_noreg,
			kode_barang : kode_barang
		}
	})
	.done(function(data){
		$(stokTerpakaiKg).val(Number(Math.round(data.berat * 1000) / 1000).toFixed(3));
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){

		nilaiKg = parseFloat($(stokAwalKg).val()) - parseFloat($(stokReturKg).val()) + parseFloat($(stokKirimKg).val()) - parseFloat($(stokTerpakaiKg).val());

		$(stokAkhirKg).val(Number(Math.round(nilaiKg * 1000) / 1000).toFixed(3));
	});

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

function retur_pakan(){
	var pakan_kode = new Array();
	var pakan_nama = new Array();
	var pakan_sisa_kg = new Array();
	var pakan_sisa_sak = new Array();
	var pakan_bentuk = new Array();

	$('input[name^="inp_c_pakan"]').each(function() {
		pakan_kode.push($(this).val());
	});
	$('input[name^="inp_c_nama_pakan"]').each(function() {
		pakan_nama.push($(this).val());
	});
	$('input[name^="inp_c_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_kg.push(jml);
	});
	$('input[name^="inp_c_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		pakan_sisa_sak.push(jml);
	});
	$('input[name^="inp_c_bentuk_pakan"]').each(function() {
		pakan_bentuk.push($(this).val());
	});

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

var timer = true;
var tkode_pegawai = '';
var tnama_pegawai = '';

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
	var bb_rata = 0;
	var populasi_awal = $('#inp_populasi_awal_stlh_umur_7').val();
	var tutupSiklus = isTutupSiklus;

	//Penimbangan Berat Badan
	var sekat_no = new Array(),
		sekat_jml = new Array(),
	    sekat_bb = new Array(),
		sekat_ket = new Array(),
		sekat_jml_tot = 0,
		sekat_bb_tot = 0;

	var valid_penimbangan = true;
	var valid_isi_penimbangan = true;

	$('input[name^="sekat_no"]').each(function() {
		sekat_no.push($(this).val());
	});

	$('input[name^="sekat_jml"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		sekat_jml.push(jml);
		sekat_jml_tot += parseInt(jml);
	});

	$('input[name^="sekat_bb"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		sekat_bb.push(jml);
		sekat_bb_tot += parseFloat(jml);
	});

	$('input[name^="sekat_ket"]').each(function() {
		sekat_ket.push($(this).val());
	});

	console.log("umur_lhk_simpan:" + umur_lhk);

	if(umur_lhk == 1 || umur_lhk == 7 || umur_lhk == 14 || umur_lhk == 21 || umur_lhk == 28){
		console.log(sekat_jml_tot + "-" + sekat_bb_tot);
		if(sekat_jml_tot == 0 || sekat_bb_tot == 0){
			bootbox.alert("Jumlah dan berat penimbangan harus diisi!");

			return false;
		}
	}

	for(var i=0;i<sekat_no.length;i++){
		/*if(sekat_jml[i] == 0 || sekat_bb[i] <= 0){
			bootbox.alert("Jumlah dan Berat Penimbangan tidak boleh kosong/nol");
			return false;
		}*/
		if(sekat_jml[i] > 0){
			if((parseInt(sekat_bb[i])/parseInt(sekat_jml[i])) != bb_std && (sekat_ket[i]).trim() == ""){
				$('#inp_ket_'+i).css("border-color","red");
				$('#inp_ket_'+i).focus();

				valid_penimbangan = false;
			}else{
				$('#inp_ket_'+i).css("border-color","#ccc");
			}
		}
	}

	if(!valid_penimbangan)
		return false;

	if(sekat_bb.length > 0 && sekat_jml_tot > 0){
		bb_rata = parseFloat(sekat_bb_tot/sekat_jml_tot) / 1000;
	}


	//Populasi CAMPUR
	var populasiAwal = $('#inp_populasiAwal').val();
	var tambahLain = $('#inp_tambahLain').val();
	var kurangMati = $('#inp_kurangMati').val();
	var kurangAfkir = $('#inp_kurangAfkir').val();
	var kurangLain = $('#inp_kurangLain').val();
	var populasiAkhir = $('#inp_populasiAkhir').val();
	var populasiDH = $('#inp_dayahidup_temp').val(); // sebelumnya $('#inp_dayahidup').val();
	var doc_in_campur = $('#doc_in_campur').val();
	var ket_kematian = $('#inp_pengisian_keterangan').val();


	//Lhk Pakan - Jantan
	var c_pakan = new Array(),
		c_stokAwalKg = new Array(),
		c_stokAwalSak = new Array(),
		c_kirimKg = new Array(),
		c_kirimSak = new Array(),
		c_terpakaiKg = new Array(),
		c_terpakaiSak = new Array(),
		c_stokAkhirKg = new Array(),
		c_stokAkhirSak = new Array();

	$('input[name^="inp_c_pakan"]').each(function() {
		c_pakan.push($(this).val());
	});
	$('input[name^="inp_c_stokAwalKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_stokAwalKg.push(jml);
	});
	$('input[name^="inp_c_stokAwalSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_stokAwalSak.push(jml);
	});
	$('input[name^="inp_c_kirimKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_kirimKg.push(jml);
	});
	$('input[name^="inp_c_kirimSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_kirimSak.push(jml);
	});
	$('input[name^="inp_c_terpakaiKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_terpakaiKg.push(jml);
	});
	$('input[name^="inp_c_terpakaiSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_terpakaiSak.push(jml);
	});

	for(var i=0;i<c_terpakaiSak;i++){
		if(c_terpakaiSak[i] > 0 && c_terpakaiKg[i] <=0){
			bootbox.alert("Kg Terpakai tidak boleh kosong/nol");
			return false;
		}
	}

	$('input[name^="inp_c_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_stokAkhirKg.push(jml);
	});
	$('input[name^="inp_c_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_stokAkhirSak.push(jml);
	});

	//Simpan lhk
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/simpan_lhk/",
		data: {
			kode_farm : farm,
			kode_kandang : kandang,
			noreg : noreg,
			tgl_lhk : tglLhk_fix,
			umur : umur,
			bb_rata : bb_rata,
			tutup_siklus : tutupSiklus,
			populasi_awal : populasi_awal,
			sekat_no : sekat_no,
			sekat_jml : sekat_jml,
			sekat_bb : sekat_bb,
			sekat_ket : sekat_ket,
			populasi_awal_campur : populasiAwal,
			tambah_campurLain : tambahLain,
			kurangMati : kurangMati,
			kurang_campurAfkir : kurangAfkir,
			kurang_campurLain : kurangLain,
			populasi_akhir_campur : populasiAkhir,
			populasi_dh_campur : populasiDH,
			c_pakan : c_pakan,
			c_stokAwalKg : c_stokAwalKg,
			c_stokAwalSak : c_stokAwalSak,
			c_kirimKg : c_kirimKg,
			c_kirimSak : c_kirimSak,
			c_terpakaiKg : c_terpakaiKg,
			c_terpakaiSak : c_terpakaiSak,
			c_stokAkhirKg : c_stokAkhirKg,
			c_stokAkhirSak : c_stokAkhirSak,
			doc_in_campur : doc_in_campur,
			ket_kematian : ket_kematian
		}
	})
	.done(function(data){
		if(data.msg == "success"){
			if(totalStokAkhir > 0){
				retur_pakan();
			}else{
				toastr.success("Penyimpanan LHK berhasil dilakukan",'Informasi');
				var ip = data.ip;
				var fcr = data.fcr;
				var adg = data.adg;

				if(bb_rata > 0 && umur_lhk >= 8){
					$("#inp_bb_rata").val(Number(Math.round(bb_rata * 1000) / 1000).toFixed(3));
					$("#inp_ip").val(Number(Math.round(ip * 1000) / 1000).toFixed(3));
					$("#inp_fcr").val(Number(Math.round(fcr * 1000) / 1000).toFixed(3));
					$("#inp_adg").val(adg);
				}else{
					$("#inp_bb_rata").val("-");
					$("#inp_ip").val("-");
					$("#inp_fcr").val("-");
					$("#inp_adg").val("-");
				}
				disabledLhk();
				ket_kematian = $('#inp_ket_kematian').val(ket_kematian);
				$('#modal_pengisian_keterangan').modal('hide');
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
	$( "#inp_bb_rata" ).val('');
	$( "#inp_ip" ).val('');
	$( "#inp_fcr" ).val('');
	$( "#inp_adg" ).val('');
	$( "#inp_ket_kematian" ).val('');

	//Reset LHK-Populasi
	$( "#inp_populasiAwal" ).val('0');
	$( "#inp_tambahLain" ).val('0');
	$( "#inp_kurangMati" ).val('0');
	$( "#inp_kurangAfkir" ).val('0');
	$( "#inp_kurangLain" ).val('0');
	$( "#inp_populasiAkhir" ).val('0');
	$( "#inp_dayahidup" ).val('0');

	//Reset LHK-Pakan
	$('#lhk_pakan > tbody').html('');
}

function resetInputLhk(){
	//Reset Header
	$( "#inp_umur" ).val('');
	$( "#inp_bb_rata" ).val('');
	$( "#inp_ip" ).val('');
	$( "#inp_fcr" ).val('');
	$( "#inp_adg" ).val('');

	//Reset LHK-Populasi
	$( "#inp_populasiAwal" ).val('0');
	$( "#inp_tambahLain" ).val('0');
	$( "#inp_kurangMati" ).val('0');
	$( "#inp_kurangAfkir" ).val('0');
	$( "#inp_kurangLain" ).val('0');
	$( "#inp_populasiAkhir" ).val('0');
	$( "#inp_dayahidup" ).val('0');

	//Reset LHK-Pakan
	$('#lhk_pakan > tbody').html('');
}

function disabledLhk(){
	$('#btnSimpan').attr("disabled", true);
	$('#btnPanen').attr("disabled", true);
	$('#btnTutupSiklus').attr("disabled", true);
	$('#btnSimpan').hide();
	$('#btnPanen').hide();
	$('#btnTutupSiklus').hide();
	$('#inp_bb_ja').attr("disabled", true);
	$('#inp_bb_be').attr("disabled", true);

	//Sekat
	$('input[name^="sekat_jml"]').each(function() {
		$(this).attr("disabled", true);
	});

	$('input[name^="sekat_bb"]').each(function() {
		$(this).attr("disabled", true);
	});

	$('input[name^="sekat_ket"]').each(function() {
		$(this).attr("disabled", true);
	});

	//Populasi CAMPUR
	$('#inp_tambahLain').attr("disabled", true);
	$('#inp_kurangMati').attr("disabled", true);
	$('#inp_kurangAfkir').attr("disabled", true);
	$('#inp_kurangLain').attr("disabled", true);
	$('#inp_ket_kematian').attr("disabled", true);

	//Pakan Kg dan Sak
	$('input[name^="inp_c_terpakaiKg"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_c_terpakaiSak"]').each(function() {
		$(this).attr("disabled", true);
	});

}

function enabledLhk(){
	$('#btnSimpan').attr("disabled", false);
	$('#btnPanen').attr("disabled", false);
	$('#btnTutupSiklus').attr("disabled", false);
	$('#btnSimpan').show();
	$('#btnPanen').show();
	$('#btnTutupSiklus').show();

	//Populasi CAMPUR
	$('#inp_populasiAwal').attr("disabled", true);
	$('#inp_tambahLain').attr("disabled", false);
	$('#inp_kurangMati').attr("disabled", false);
	$('#inp_kurangAfkir').attr("disabled", false);
	$('#inp_kurangLain').attr("disabled", false);
	$('#inp_ket_kematian').attr("disabled", true);

	//Pakan Kg dan Sak
	$('input[name^="inp_c_terpakaiKg"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_c_terpakaiSak"]').each(function() {
		$(this).attr("disabled", false);
	});
	$('input[name^="inp_c_terpakaiKg"]').each(function() {
		$(this).attr("disabled", true);
	});
	$('input[name^="inp_c_terpakaiSak"]').each(function() {
		$(this).attr("disabled", false);
	});

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

function simpan_transaksi_verifikasi(callback){
    $.ajax({
        type : "POST",
        url : "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
        dataType : 'json',
		data : {
			kode_flok : $('#main_content').find('input[name=flock]').val(),
			transaction : 'lhk'
		},
        success : function(data) {
            callback(data);
        }
    });
}

function cek_verifikasi(date_transaction){
    if (timer == true) {
        $.ajax({
            type : "POST",
            url : "pengambilan_barang/transaksi/cek_verifikasi",
            data : {
                date_transaction : date_transaction,
				kode_flok : $('#main_content').find('input[name=flock]').val(),
            },
            dataType : 'json',
            success : function(data) {
                if(data.verificator){
                    timer = false;
                    tkode_pegawai = data.kode_pegawai;
                    tnama_pegawai = data.nama_pegawai;
                    $('.bootbox').modal('hide');
                }
                else{
                    timer = true;
                    tkode_pegawai = '';
                    tnama_pegawai = '';
                    setTimeout("cek_verifikasi('"+date_transaction+"')", 1000);
                }
            }
        });
    }
}

function checkPengisianKeterangan(elm){
	var length = $(elm).val().length;

	if(length>=10)
		$('#btntombolLanjutSimpan').removeClass('disabled');
	else
		$('#btntombolLanjutSimpan').addClass('disabled');

}

$('#btntombolLanjutSimpan').click(function(){
	simpan_transaksi_verifikasi(function(result){
		if(result.date_transaction){
			var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
			var box = bootbox.dialog({
				message : _message,
				closeButton: false,
				title : "Fingerprint",
				buttons : {
					success : {
						label : "Batal",
						className : "btn-danger",
						callback : function() {
							timer = false;
							tkode_pegawai = '';
							tnama_pegawai = '';
							return true;
						}
					}
				}
			});

			box.bind('shown.bs.modal', function() {
				timer = true;
				tkode_pegawai = '';
				tnama_pegawai = '';
				cek_verifikasi(result.date_transaction);
			});

			box.bind('hidden.bs.modal', function() {
				if(tkode_pegawai && tnama_pegawai){
					simpanLhk('N', 0);
				}
				else{
					toastr.warning('Verifikasi fingerprint tidak berhasil.','Warning');
				}
			});
		}
	});
});
