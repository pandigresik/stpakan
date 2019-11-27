var umur_lhk = 0;
var ack_ket_var = "";
var bb_rata_last
var left_panel_stat = "open";

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

$(document).ready(function() {
	var nama_farm = $('#q_farm_true').val();
	var level_user = $('#q_leveluser').val();

	if(level_user == "KDV" || level_user == "KD"){
		$('#lbl_nama_farm').show();
		$('#column-left').show();
		$('#btn').show();
		$('#lbl_pemantauan_lhk').html("PEMANTAUAN LAPORAN HARIAN KANDANG");
	}else{

		$('#content').removeClass("col-md-10");
		$('#content').addClass("col-md-12");


		$('#lbl_pemantauan_lhk').html("PEMANTAUAN LAPORAN HARIAN KANDANG" + "<br/>" + nama_farm.toUpperCase());
	}

	$('#q_lhk_tidak_sesuai_timeline').attr("checked", true);
	$('#q_belum_konfirmasi').attr("checked", true);
	// $('#q_lhk_pakan_berlebih').attr("checked", true);

	// $('#q_lhk_sesuai_timeline').attr("disabled", true);
	$('#q_lhk_sesuai_timeline').attr("checked", true);
	$("#q_lhk_belum_dientry").attr("disabled", true);
});

$('#div_q_start_tgl_lhk').datetimepicker({
	pickTime: false,
	format : "DD MMM YYYY"
});


$('#div_q_end_tgl_lhk').datetimepicker({
	pickTime: false,
	format : "DD MMM YYYY"
});

// $('#div_q_end_tgl_lhk').data("DateTimePicker").setMaxDate(new Date());

// $("#div_q_start_tgl_lhk").on("dp.change", function(e) {
	// $('#div_q_end_tgl_lhk').data("DateTimePicker").setDate(e.date);
	// $('#div_q_end_tgl_lhk').data("DateTimePicker").setMinDate(e.date);
// });

// $("#div_q_end_tgl_lhk").on("dp.change", function(e) {
	// $('#div_q_start_tgl_lhk').data("DateTimePicker").setMaxDate(e.date);
// });

$('.search').keyup(function(){
	var txt = $(this).val();
	refreshData();
});

$("#q_lhk_tidak_sesuai_timeline").change(function () {
	if($(this).is(':checked')){
		$('#q_belum_konfirmasi').attr("disabled", false);
		$('#q_sudah_konfirmasi').attr("disabled", false);
	}else{
		// if ($('#q_lhk_pakan_berlebih').is(":checked"))
		// {
			// $('#q_lhk_pakan_berlebih').attr("checked", false);
			$("#q_lhk_belum_dientry").attr("disabled", false);
			$('#q_lhk_sesuai_timeline').attr("disabled", false);
		// }else{
		// }


		$('#q_belum_konfirmasi').attr("checked", false);
		$('#q_sudah_konfirmasi').attr("checked", false);

		// $('#q_belum_konfirmasi').attr("disabled", true);
		// $('#q_sudah_konfirmasi').attr("disabled", true);
	}
});

$("#q_lhk_sesuai_timeline").change(function () {
    if($(this).is(':checked')){
		if ($('#q_lhk_tidak_sesuai_timeline').is(":checked"))
		{}else{
			$('#q_belum_konfirmasi').attr("checked", false);
			$('#q_sudah_konfirmasi').attr("checked", false);

			// $('#q_belum_konfirmasi').attr("disabled", true);
			// $('#q_sudah_konfirmasi').attr("disabled", true);
		}
	}else{
		if ($('#q_lhk_belum_dientry').is(":checked"))
		{}else{
			$('#q_belum_konfirmasi').attr("disabled", false);
			$('#q_sudah_konfirmasi').attr("disabled", false);
		}

	}
});

$("#q_lhk_belum_dientry").change(function () {
    if($(this).is(':checked')){
		if ($('#q_lhk_tidak_sesuai_timeline').is(":checked"))
		{}else{
			$('#q_belum_konfirmasi').attr("checked", false);
			$('#q_sudah_konfirmasi').attr("checked", false);

			$('#q_belum_konfirmasi').attr("disabled", true);
			$('#q_sudah_konfirmasi').attr("disabled", true);
		}
	}else{
		if ($('#q_lhk_sesuai_timeline').is(":checked"))
		{}else{
			$('#q_belum_konfirmasi').attr("disabled", false);
			$('#q_sudah_konfirmasi').attr("disabled", false);
		}
	}
});

// $("#q_lhk_pakan_berlebih").change(function () {
    // if($(this).is(':checked')){
		// $('#q_lhk_tidak_sesuai_timeline').attr("disabled", false);
		// $('#q_belum_konfirmasi').attr("disabled", false);
		// $('#q_sudah_konfirmasi').attr("disabled", false);

		// $('#q_lhk_sesuai_timeline').attr("checked", false);
		// $('#q_lhk_sesuai_timeline').attr("disabled", true);
		// $("#q_lhk_belum_dientry").attr("checked", false);
		// $("#q_lhk_belum_dientry").attr("disabled", true);

	// }else{
		// if ($('#q_lhk_sesuai_timeline').is(":checked"))
		// {}else{
			// $("#q_lhk_belum_dientry").attr("disabled", false);
			// $('#q_lhk_sesuai_timeline').attr("disabled", false);

			// $('#q_belum_konfirmasi').attr("disabled", false);
			// $('#q_sudah_konfirmasi').attr("disabled", false);
		// }
	// }
// });

$('#btnCari').click(function(){
	refreshData();
});

$('#btn').click(function(){
	$( "#column-left" ).animate({
		width: "toggle"
	}, {
		complete: function() {
			if(left_panel_stat == "open"){
				left_panel_stat = "close";
				$('#content').removeClass("col-md-10");
				$('#content').addClass("col-md-12");
			}else{
				left_panel_stat = "open";
			}
		}
	});

	if(left_panel_stat == "close"){
		$('#content').removeClass("col-md-12");
		$('#content').addClass("col-md-10");
	}
});

function change_farm(elm){
	var namafarm = $(elm).html();
	var kodefarm = $(elm).attr("data-farm");

	var namafarm_arr = namafarm.split("<span");

	$('#q_farm').val(kodefarm);
	$('#span_lbl_farm').html(namafarm_arr[0]);

	refreshData();
}

function acknowledge_kf(elm){
	var ket = $(elm).data('keterangan');
	var tgl_doc_in = $(elm).data('doc_in');

	var tr = $(elm).parent().parent().parent();

	var td_noreg = $(tr).find('td').eq(0);
	var td_tgl_transaksi = $(tr).find('td').eq(3);
	var td_desc = $(tr).find('td').eq(6);

	var no_reg = $(td_noreg).attr("data-noreg");
	var nama_kandang = $(td_noreg).attr("data-kandang");
	var tgl_transaksi = $(td_tgl_transaksi).attr("data-tgl_transaksi");
	var tgl_transaksi_lhk = $(td_tgl_transaksi).attr("data-tgl_transaksi_lhk");
	var tgl_entri = $(td_tgl_transaksi).attr("data-tgl_entri");

	var nama_farm = ($('#q_farm_true').val()).toUpperCase();


	var tgl_arr = tgl_transaksi.split(' ');
	var tgl_transaksi = tgl_arr[0];

	var x = tgl_doc_in.split('-');
	var doc_in = x[2]+' '+months[parseInt(x[1])-1]+' '+x[0];

	var y = tgl_transaksi.split('-');
	var lhk = y[2]+' '+months[parseInt(y[1])-1]+' '+y[0];

	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/get_data_lhk/",
		data: {
			tgl_lhk : tgl_transaksi,
			no_reg : no_reg,
			tgl_doc_in : tgl_doc_in
		}
	})
	.done(function(data){
		if(data.rhk){
			var rhk = data.rhk;
			var rhk_penimbangan = data.rhk_penimbangan;
			bb_rata_last = data.bb_rata_last;
			var rhk_pakan = data.rhk_pakan;
			var tgl_lhk = data.tgl_lhk;
			var umur = data.umur;
			var fcr = data.fcr;
			var ip = data.ip;
			var adg = data.adg;
			var bb_rata = rhk.C_BERAT_BADAN;
			var class_pakan = data.class_pakan;
			var jumlah_panen = rhk.jumlah_panen;
			var kode_farm = rhk.KODE_FARM;
			nama_farm = rhk.NAMA_FARM;
			var no_reg_lhk = rhk.NO_REG;
			var user_buat = rhk.USER_BUAT;
			var tgl_buat = rhk.TGL_BUAT;

			//header
			$('#inp_kandang').val(nama_kandang);
			$('#inp_flock').val(rhk.FLOK_BDY);
			$('#inp_doc_in').val(doc_in);
			$('#inp_umur').val(umur);
			umur_lhk = umur;
			$('#inp_tgl_lhk').val(lhk);
			$('#inp_dayahidup_temp').val(rhk.C_DAYA_HIDUP);
			$('#inp_last_bb_rata').val(bb_rata_last);
			$('#inp_status_lhk').val(ket);
			$('#inp_farm').val(kode_farm);
			$('#inp_nama_farm').val(nama_farm);
			$('#inp_noreg_lhk').val(no_reg_lhk);
			$('#inp_tgl_input').val(tgl_entri);
			$('#inp_tgl_trnsaksi').val(tgl_transaksi_lhk);
			$('#inp_tgl_transaksi').val(tgl_transaksi);
			$('#inp_user_buat').val(user_buat);
			$('#inp_tgl_buat').val(tgl_buat);
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
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_jml+'" style="width:100px" name="sekat_jml[]" ></td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_berat+'" style="width:100px" name="sekat_bb[]"></td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_ket+'" style="width:100%" name="sekat_ket[]" ></td>'+
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
			$('#inp_ket_kematian').val(rhk.KETERANGAN1);

			$('#inp_tambahLain').attr("disabled", false);
			$('#inp_kurangMati').attr("disabled", false);
			$('#inp_kurangAfkir').attr("disabled", false);
			$('#inp_kurangLain').attr("disabled", false);
			$('#inp_ket_kematian').attr("disabled", true);

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
					'	<td class="vert-align" >' +
						obj.NAMA_BARANG +
						'<input type="hidden" class="form-control input-sm" name="inp_c_nama_pakan[]" value="' + obj.NAMA_BARANG + '">' +
						'<input type="hidden" class="form-control input-sm" name="inp_c_pakan[]" value="' + obj.KODE_BARANG + '">' +
						'<input type="hidden" class="form-control input-sm" name="inp_c_bentuk_pakan[]" value="' + obj.BENTUK_BARANG + '">' +
					'</td>'+
					'	<td class="inp-numeric"><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAwalKg[]" value="' + (Number(Math.round(awalKg * 1000) / 1000).toFixed(3)) + '" disabled></td>'+
					'	<td class="inp-numeric"><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAwalSak[]" value="' + (awalStok) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokReturKg[]" value="' + (Number(Math.round(obj.brt_sak * 1000) / 1000).toFixed(3)) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokReturSak[]" value="' + obj.jml_retur + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimKg[]" value="' + obj.BRT_TERIMA + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimSak[]"  value="' + obj.JML_TERIMA + '"disabled></td>'+
					'	<td><input type="text" data-jeniskelamin="J" class="form-control input-sm inp-numeric" name="inp_c_terpakaiKg[]" value="'+obj.BRT_PAKAI+'" onchange="return cekNumerikPakanKg(event, this)" title="Kg harus lebih besar dari jumlah Sak" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_terpakaiSak[]" value="' + obj.JML_PAKAI + '" onkeyup="cekNumerikPakanSak(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirKg[]" value="' + obj.BRT_AKHIR + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirSak[]" value="' + obj.JML_AKHIR + '" disabled></td>'+
					'</tr>';

					i++;
				}
			}

			var pakan_all = pakan_c;
			$('#lhk_pakan > tbody').html(pakan_all.join(''));

			$('#btnLanjutRilis').show();
			$('input[name^="sekat_no"]').each(function() {
				$(this).attr("disabled", false);
			});

			$('input[name^="sekat_jml"]').each(function() {
				$(this).attr("disabled", false);
			});

			$('input[name^="sekat_bb"]').each(function() {
				$(this).attr("disabled", false);
			});

			$('input[name^="sekat_ket"]').each(function() {
				$(this).attr("disabled", false);
			});

			$('#inp_tambahLain').attr("disabled", false);
			$('#inp_kurangMati').attr("disabled", false);
			$('#inp_kurangAfkir').attr("disabled", false);
			$('#inp_kurangLain').attr("disabled", false);
			$('#inp_ket_kematian').attr("disabled", true);

			$('input[name^="inp_c_terpakaiSak"]').each(function() {
				$(this).attr("disabled", false);
			});

			$('#modal_lhk').modal('show');
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
};

function simpan_acknowledge_kf(elm){
	var status_lhk = $('#inp_status_lhk').val();
	var no_reg_lhk = $('#inp_noreg_lhk').val();
	var kode_farm = $('#inp_farm').val();
	var nama_farm = $('#inp_nama_farm').val();
	var nama_kandang = $('#inp_kandang').val();
	var tgl_entri = $('#inp_tgl_input').val();
	var tgl_transaksi_lhk = $('#inp_tgl_trnsaksi').val();
	var ket_kematian = $('#inp_ket_kematian').val();

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
		if(sekat_jml_tot == 0 || sekat_bb_tot == 0){
			bootbox.alert("Jumlah dan berat penimbangan harus diisi!");

			return false;
		}
	}

	//if(!passed){
//		bootbox.alert("Jumlah Kg Pakan terpakai masih 0");
//	}else{
		//Sistem memeriksa entrian timbangan BB
		//step a
		if(sekat_no.length > 0 && sekat_bb_tot > 0 && sekat_jml_tot > 0){

			var bb_rata = parseFloat(sekat_bb_tot/sekat_jml_tot) / 1000;
			console.log("bb_rata:"+bb_rata);
			/*
			Jika BB rata-rata yang dientri (saat ini) < BB rata-rata penimbangan terakhir,
			sistem menampilkan confirmation message “BB rata-rata penimbangan saat ini lebih kecil dari penimbangan sebelumnya.
			Apakah BB rata-rata yang dientri sudah benar?”
			*/

			//step b
			if(parseFloat(bb_rata) < parseFloat(bb_rata_last)){
				console.log("a");
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
													$('#inp_pengisian_keterangan').val(ket_kematian);
													if(ket_kematian.length>=10)
														$('#btntombolLanjutSimpan').removeClass('disabled');
													else
														$('#btntombolLanjutSimpan').addClass('disabled');
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
													return false;
												}
											}
										}
									});
								}
								else{
									bootbox.dialog({
										message: "LHK yang dientri sudah benar. Apakah Anda yakin melanjutkan proses penyimpanan LHK?",
										title: "Konfirmasi",
										buttons: {
											success: {
												label: "Ya",
												className: "btn-primary",
												callback: function() {
													var status_lhk = $('#inp_status_lhk').val();
													if(status_lhk=="SESUAI TIMELINE"){
														acknowledge_kf_final();
													}
													else{

														bootbox.prompt({
														title: ""+
														"<table width='100%'>"+
														"<tr><td colspan='4' align='center'><h2>Laporan Harian Kandang<br/>Farm "+nama_farm+"<br/></h2></td><tr>"+

														"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>No. Reg</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + no_reg_lhk + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_transaksi_lhk + "</td><tr>"+
														"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>Kandang</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + nama_kandang + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. Transaksi LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_entri + "</td><tr>"+
														"</table><br><span style='font-size:14px;font-weight:bold'>Keterangan Acknowlegement LHK</span>",
														inputType: 'textarea',
														value: "",
														callback: function(result) {
															if (result === null) {

															} else {
																if(result == null || result == ""){
																	bootbox.alert("Mohon mencantumkan keterangan.");
																	return false;
																}else{
																	ack_ket_var = result;
																	acknowledge_kf_final(ack_ket_var);
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
				console.log("b");
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
									$('#inp_pengisian_keterangan').val(ket_kematian);

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
						message: "LHK yang dientri sudah benar. Apakah Anda yakin melanjutkan proses penyimpanan LHK?",
						title: "Konfirmasi",
						buttons: {
							success: {
								label: "Ya",
								className: "btn-primary",
								callback: function() {
									var status_lhk = $('#inp_status_lhk').val();
									if(status_lhk=="SESUAI TIMELINE"){
										acknowledge_kf_final();
									}
									else{

										bootbox.prompt({
											title: ""+
											"<table width='100%'>"+
											"<tr><td colspan='4' align='center'><h2>Laporan Harian Kandang<br/>Farm "+nama_farm+"<br/></h2></td><tr>"+

											"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>No. Reg</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + no_reg_lhk + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_transaksi_lhk + "</td><tr>"+
											"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>Kandang</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + nama_kandang + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. Transaksi LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_entri + "</td><tr>"+
											"</table><br><span style='font-size:14px;font-weight:bold'>Keterangan Acknowlegement LHK</span>",
											inputType: 'textarea',
											value: "",
											callback: function(result) {
												if (result === null) {

												} else {
													if(result == null || result == ""){
														bootbox.alert("Mohon mencantumkan keterangan.");
														return false;
													}else{
														ack_ket_var = result;
														acknowledge_kf_final(ack_ket_var);
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

								}
							}
						}
					});
				}
			}
		}
		else{
			console.log("2");
			//step c
			var v_awal = $('#inp_populasiAwal').val();
			var v_mati = $('#inp_kurangMati').val();
			var v_pop_prc = (parseFloat(v_mati/v_awal)*100);

			if(v_pop_prc > 0.07){
				bootbox.dialog({
					message: "Apakah Anda yakin mengentri jumlah mati sebesar "+v_mati+" ekor?<br/>*) Kesalahan entri akan memberi dampak pada performance kandang.",
					title: "Konfirmasi",
					buttons: {
						success: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								//show pop up pengisian keterangan
								$('#inp_pengisian_keterangan').val(ket_kematian);
								if(ket_kematian.length>=10)
									$('#btntombolLanjutSimpan').removeClass('disabled');
								else
									$('#btntombolLanjutSimpan').addClass('disabled');

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
					message: "LHK yang dientri sudah benar. Apakah Anda yakin melanjutkan proses penyimpanan LHK?",
					title: "Konfirmasi",
					buttons: {
						success: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								var status_lhk = $('#inp_status_lhk').val();
								if(status_lhk=="SESUAI TIMELINE"){
									acknowledge_kf_final();
								}
								else{

									bootbox.prompt({
									title: ""+
									"<table width='100%'>"+
									"<tr><td colspan='4' align='center'><h2>Laporan Harian Kandang<br/>Farm "+nama_farm+"<br/></h2></td><tr>"+

									"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>No. Reg</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + no_reg_lhk + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_transaksi_lhk + "</td><tr>"+
									"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>Kandang</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + nama_kandang + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. Transaksi LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_entri + "</td><tr>"+
									"</table><br><span style='font-size:14px;font-weight:bold'>Keterangan Acknowlegement LHK</span>",
									inputType: 'textarea',
									value: "",
									callback: function(result) {
										if (result === null) {

										} else {
											if(result == null || result == ""){
												bootbox.alert("Mohon mencantumkan keterangan.");
												return false;
											}else{
												ack_ket_var = result;
												acknowledge_kf_final(ack_ket_var);
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

							}
						}
					}
				});
			}
		}

		/*End of tambahan baru*/
	//}

}

function acknowledge_kadep(elm){
	var tr = $(elm).parent().parent().parent();

	var td_noreg = $(tr).find('td').eq(0);
	var td_tgl_transaksi = $(tr).find('td').eq(3);

	var no_reg = $(td_noreg).attr("data-noreg");
	var tgl_transaksi = $(td_tgl_transaksi).attr("data-tgl_transaksi");

	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_kadep/",
		data: {
			no_reg : no_reg,
			tgl_transaksi : tgl_transaksi
		}
	})
	.done(function(data){
		if(data.msg == "success"){
			bootbox.alert("Acknowledge pada no.reg LHK " + no_reg + " telah berhasil dilakukan.");

			refreshData();
			refreshMenu();
		}else{
			console.log("failed to update rhk1");
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
};

function acknowledge_kadiv(elm){
	var tr = $(elm).parent().parent().parent();

	var td_noreg = $(tr).find('td').eq(0);
	var td_tgl_transaksi = $(tr).find('td').eq(3);

	var no_reg = $(td_noreg).attr("data-noreg");
	var tgl_transaksi = $(td_tgl_transaksi).attr("data-tgl_transaksi");

	//alert(no_reg + tgl_transaksi);


	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_kadiv/",
		data: {
			no_reg : no_reg,
			tgl_transaksi : tgl_transaksi
		}
	})
	.done(function(data){
		if(data.msg == "success"){
			bootbox.alert("Acknowledge pada no.reg LHK " + no_reg + " telah berhasil dilakukan.");

			refreshData();
			refreshMenu();
		}else{
			console.log("failed to update rhk1");
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
};

function refreshMenu(){
	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/get_menu_farm/",
		data: {
		}
	})
	.done(function(data){
		var items = data.items;
		var menus = new Array();

		i = 0;
		$.each(items, function(idx, obj) {
			var badge = (obj.jml > 0) ? " <span class='badge'>" + obj.jml + "</span>" : "";
			menus[i] = "<div data-farm='" + obj.kode_farm + "' class='menu_farm' onclick='change_farm(this)'>" + (obj.nama_farm).toUpperCase() + badge + "</div>";

			i++;
		});

		$('#daftar_farm').html(menus.join(''));
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

function refreshData(){
	var q_tidak_sesuai_timeline,
		q_sesuai_timeline,
		q_belum_dientry,
		q_belum_konfirmasi,
		q_sudah_konfirmasi,
		q_tgl_start,
		q_tgl_end,
		q_kandang,
		q_noreg,
		q_farm,
		q_pakan_berlebih;

	q_tidak_sesuai_timeline = ($('#q_lhk_tidak_sesuai_timeline').is(":checked")) ? 1 : 0;
	q_sesuai_timeline = ($('#q_lhk_sesuai_timeline').is(":checked")) ? 1 : 0;
	q_belum_dientry = ($('#q_lhk_belum_dientry').is(":checked")) ? 1 : 0;
	q_belum_konfirmasi = ($('#q_belum_konfirmasi').is(":checked")) ? 1 : 0;
	q_sudah_konfirmasi = ($('#q_sudah_konfirmasi').is(":checked")) ? 1 : 0;
	// q_pakan_berlebih = ($('#q_lhk_pakan_berlebih').is(":checked")) ? 1 : 0;

	var pad = "00";

	var tgl_awal = $("#q_start_tgl_lhk").val();
	var tgl_awal_arr = tgl_awal.split(" ");

	var index_0 = (months.indexOf(tgl_awal_arr[1]) >= 0) ? months.indexOf(tgl_awal_arr[1]) : months_id.indexOf(tgl_awal_arr[1]);
	tahun_0 = parseInt(tgl_awal_arr[2]);
	bulan_0 = pad.substring(0, pad.length - ("" + (parseInt(index_0) + 1)).length) + (parseInt(index_0) + 1);
	hari_0 = pad.substring(0, pad.length - ("" + (parseInt(tgl_awal_arr[0]))).length) + (parseInt(tgl_awal_arr[0]));

	var tgl_akhir = $("#q_end_tgl_lhk").val();
	var tgl_akhir_arr = tgl_akhir.split(" ");

	var index_1 = (months.indexOf(tgl_akhir_arr[1]) >= 0) ? months.indexOf(tgl_akhir_arr[1]) : months_id.indexOf(tgl_akhir_arr[1]);
	tahun_1 = parseInt(tgl_akhir_arr[2]);
	bulan_1 = pad.substring(0, pad.length - ("" + (parseInt(index_1) + 1)).length) + (parseInt(index_1) + 1);
	hari_1 = pad.substring(0, pad.length - ("" + (parseInt(tgl_akhir_arr[0]))).length) + (parseInt(tgl_akhir_arr[0]));

	q_tgl_start = (tgl_awal!="") ? tahun_0 + '-' + bulan_0 + '-' + hari_0 : "";
	q_tgl_end = (tgl_akhir!="") ? tahun_1 + '-' + bulan_1 + '-' + hari_1 : "";
	q_kandang = $('#q_kandang').val();
	q_noreg = $('#q_noreg').val();
	q_farm = $('#q_farm').val();

	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/get_data/",
		data: {
			q_tidak_sesuai_timeline :q_tidak_sesuai_timeline,
			q_sesuai_timeline : q_sesuai_timeline,
			q_belum_dientry : q_belum_dientry,
			q_belum_konfirmasi : q_belum_konfirmasi,
			q_sudah_konfirmasi : q_sudah_konfirmasi,
			q_tgl_start : q_tgl_start,
			q_tgl_end : q_tgl_end,
			q_kandang : q_kandang,
			q_noreg : q_noreg,
			q_farm : q_farm,
			q_pakan_berlebih : q_pakan_berlebih
		}
	})
	.done(function(data){
		if(data.msg == "failed"){

		}
		else{
			var items = data.items;

			var rows = new Array();
			var i = 0;
			$.each(items, function(idx, obj) {

				var tgl_lhk = (obj.colDate == null) ? '-' : obj.colDate;
				var tgl_entri = (obj.tgl_buat == null) ? '-' : obj.tgl_buat;

				var ack_kf = "-";
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE")
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE" || obj.tgl_transaksi2 != "")
				//console.log(obj.stTemp + "-" + obj.tgl_transaksi2);
				var statusLhk = "-";
				var statusLhkArr = new Array();
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))

				if(obj.stTemp == "TIDAK SESUAI TIMELINE")
				{
					if(obj.stTemp == "TIDAK SESUAI TIMELINE")
						statusLhkArr.push("Tidak Sesuai Timeline");

					// if((obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))
						// statusLhkArr.push("Konsumsi Pakan Berlebih");

					if(tgl_entri != "" && obj.ack_kf == null){
						if(data.level_user == "KF"){
							ack_kf = '<input type="button" data-tgl_entri="'+obj.tgl_buat+'" data-tgl_transaksi_lhk="'+tgl_lhk+'" data-tgl_transaksi="' + obj.tgl_transaksi + '" data-doc_in="'+obj.tgl_doc_in+'" data-keterangan="'+obj.stTemp+'" class="form-control btn-primary" style="width:100px" value="Ack" onclick="acknowledge_kf(this)"/>';
						}else{
							ack_kf = '-';
						}
					}else if(tgl_entri != "" && obj.ack_kf != null){
						var ack_temp = obj.ack_kf;
						var ack_temp_arr0 = ack_temp.split(' ');

						var ack_temp_arr1 = ack_temp_arr0[0].split("-");
						var tahun = ack_temp_arr1[0];
						var bulan = months[(parseInt(ack_temp_arr1[1]) - 1)];
						var hari = parseInt(ack_temp_arr1[2]);

						ack_kf = hari + '-' + bulan + '-' + tahun + ' ' + ack_temp_arr0[1];// + ' ' + ack_temp_arr0[2];
					}else{
						ack_kf = '-';
					}

					if(statusLhkArr.length > 0)
						statusLhk = statusLhkArr.join("<br/>");
				}else{

					if(obj.stTemp == "SESUAI TIMELINE"){
						statusLhkArr.push("-");//statusLhkArr.push("Sesuai Timeline");


						if(tgl_entri != "" && obj.ack_kf == null){
							if(data.level_user == "KF"){
								ack_kf = '<input type="button" data-tgl_entri="'+obj.tgl_buat+'" data-tgl_transaksi_lhk="'+tgl_lhk+'" data-tgl_transaksi="' + obj.tgl_transaksi + '" data-doc_in="'+obj.tgl_doc_in+'" data-keterangan="'+obj.stTemp+'" class="form-control btn-primary" style="width:100px" value="Ack" onclick="acknowledge_kf(this)"/>';
							}

						}else if(tgl_entri != "" && obj.ack_kf != null){
							var ack_temp = obj.ack_kf;
							var ack_temp_arr0 = ack_temp.split(' ');

							var ack_temp_arr1 = ack_temp_arr0[0].split("-");
							var tahun = ack_temp_arr1[0];
							var bulan = months[(parseInt(ack_temp_arr1[1]) - 1)];
							var hari = parseInt(ack_temp_arr1[2]);

							ack_kf = hari + '-' + bulan + '-' + tahun + ' ' + ack_temp_arr0[1];// + ' ' + ack_temp_arr0[2];
						}else{
							ack_kf = '-';
						}

						if(statusLhkArr.length > 0)
							statusLhk = statusLhkArr.join("<br/>");
					}
				}

				var ack_1 = "";
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))
				if(obj.stTemp == "TIDAK SESUAI TIMELINE")
				{
					if(tgl_entri != ""  && obj.ack_kf != null && obj.ack1 == null){
						if(data.level_user == "KD"){
							ack_1 = '<input type="button" class="form-control btn-primary" style="width:100px" value="Ack" onclick="acknowledge_kadep(this)"/>';
						}else{
							ack_1 = '-';
						}
					}else if(tgl_entri != "" && obj.ack1 != null){
						var ack_temp = obj.ack1;
						var ack_temp_arr0 = ack_temp.split(' ');

						var ack_temp_arr1 = ack_temp_arr0[0].split("-");
						var tahun = ack_temp_arr1[0];
						var bulan = months[(parseInt(ack_temp_arr1[1]) - 1)];
						var hari = parseInt(ack_temp_arr1[2]);

						ack_1 = hari + '-' + bulan + '-' + tahun + ' ' + ack_temp_arr0[1];// + ' ' + ack_temp_arr0[2];
					}else{
						ack_1 = '-';
					}
				}else{
					ack_1 = '-';
				}

				var ack_2 = "";
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))
				if(obj.stTemp == "TIDAK SESUAI TIMELINE")
				{
					if(tgl_entri != ""  && obj.ack_kf != null && obj.ack1 != null && obj.ack2 == null){
						if(data.level_user == "KDV"){
							ack_2 = '<input type="button" class="form-control btn-primary" style="width:100px" value="Ack" onclick="acknowledge_kadiv(this)"/>';
						}else{
							ack_2 = '-';
						}
					}else if(tgl_entri != "" && obj.ack2 != null){
						var ack_temp = obj.ack2;
						var ack_temp_arr0 = ack_temp.split(' ');

						var ack_temp_arr1 = ack_temp_arr0[0].split("-");
						var tahun = ack_temp_arr1[0];
						var bulan = months[(parseInt(ack_temp_arr1[1]) - 1)];
						var hari = parseInt(ack_temp_arr1[2]);

						ack_2 = hari + '-' + bulan + '-' + tahun + ' ' + ack_temp_arr0[1];// + ' ' + ack_temp_arr0[2];
					}else{
						ack_2 = '-';
					}
				}else{
					ack_2 = '-';
				}

				var ack_desc = (obj.ack_desc == null) ? '-' : obj.ack_desc;

				if(tgl_lhk != '-'){
					var tgl_lhk_arr = tgl_lhk.split("-");
					var tahun = tgl_lhk_arr[0];
					var bulan = months[(parseInt(tgl_lhk_arr[1]) - 1)];
					var hari = parseInt(tgl_lhk_arr[2]);

					tgl_lhk = hari + '-' + bulan + '-' + tahun;
				}

				if(tgl_entri != '-'){
					var tgl_entri_arr0 = tgl_entri.split(" ");
					var tgl_entri_arr1 = tgl_entri_arr0[0].split("-");
					var tahun = tgl_entri_arr1[0];
					var bulan = months[(parseInt(tgl_entri_arr1[1]) - 1)];
					var hari = parseInt(tgl_entri_arr1[2]);

					tgl_entri = hari + '-' + bulan + '-' + tahun + ' ' + tgl_entri_arr0[1];// + ' ' + tgl_entri_arr0[2];
				}

				var tgl_entri_arr = tgl_entri.split(' ');
				var tgl_entri_arr2 = tgl_entri.split(':');
				var tanggal_entri = tgl_entri_arr2[0]+':'+tgl_entri_arr2[1];


				var backColorRow = "";
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.stTemp == "TIDAK SESUAI TIMELINE" && (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))){
				if(obj.stTemp == "TIDAK SESUAI TIMELINE"){
					backColorRow = 'style="color:#FF0000;"';
				}

				// if(obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)){
					// backColorRow = 'style="color:#0C4EE8;"';
				// }

				if(obj.stTemp == "BELUM ENTRY - TELAT"){
					backColorRow = 'style="color:#E6A205"';
				}

				if(statusLhkArr.length == 2){
					backColorRow = 'style="color:#0C4EE8;"';
				}

				var link_noreg = "";
				// if((obj.stTemp == "SESUAI TIMELINE" || obj.stTemp == "TIDAK SESUAI TIMELINE")){
					console.log("data-tgl_doc_in_lhk="+obj.tgl_doc_in_lhk);
				if(obj.ack_kf != null){
					var t_transaksi = (obj.tgl_transaksi).split(' ');
					link_noreg = '<div style="text-decoration:underline;" data-kandang="'+obj.nama_kandang+'" data-tgl_doc_in_lhk="'+obj.tgl_doc_in_lhk+'" data-tgl_transaksi_lhk="'+obj.tgl_lhk+'" data-no_reg="'+obj.noReg+'" data-doc_in="'+obj.tgl_doc_in+'" data-tgl_transaksi="'+t_transaksi[0]+'" onclick="view_lhk(this)">'+obj.noReg+'</div>';
				}
				else{
					link_noreg = '<div>'+obj.noReg+'</div>';
				}

				rows[i] = "<tr>" +
						  "<td "+backColorRow+" data-kandang='"+obj.nama_kandang+"' data-noreg='" + obj.no_reg + "' class='vert-align'>" + obj.nama_kandang + "</td>" +
						  "<td "+backColorRow+" class='vert-align'>" + link_noreg + "</td>" +
						  "<td "+backColorRow+" class='vert-align'>" + tgl_lhk + "</td>" +
						  "<td "+backColorRow+" data-kandang='"+obj.nama_kandang+"' data-tgl_entri='"+tanggal_entri+"' data-tgl_transaksi_lhk='"+tgl_lhk+"' data-tgl_transaksi='" + obj.tgl_transaksi + "' class='vert-align'>" + tgl_entri + "</td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + statusLhk + "</center></td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + ack_kf + "</center></td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + ack_1 + "</center></td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + ack_2 + "</center></td>" +
						  "<td "+backColorRow+" class='vert-align'>" + ack_desc + "</td>" +
						  "</tr>";

				i++;
			});

			$("tbody","#tb_lhk").html(rows.join(''));
		}

	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

function view_lhk(elm){
	var no_reg = $(elm).data("no_reg");
	var tgl_transaksi = $(elm).data("tgl_transaksi");
	var tgl_doc_in_lhk = $(elm).data("tgl_doc_in_lhk");
	var tgl_doc_in = $(elm).data("doc_in");

	var nama_kandang = $(elm).data("kandang");
	var lhk = $(elm).data("tgl_transaksi_lhk");

	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/get_data_lhk/",
		data: {
			tgl_lhk : tgl_transaksi,
			no_reg : no_reg,
			tgl_doc_in : tgl_doc_in
		}
	})
	.done(function(data){
		if(data.rhk){
			var rhk = data.rhk;
			var rhk_penimbangan = data.rhk_penimbangan;
			bb_rata_last = data.bb_rata_last;
			var rhk_pakan = data.rhk_pakan;
			var tgl_lhk = data.tgl_lhk;
			var umur = data.umur;
			var fcr = data.fcr;
			var ip = data.ip;
			var adg = data.adg;
			var bb_rata = rhk.C_BERAT_BADAN;
			var class_pakan = data.class_pakan;
			var jumlah_panen = rhk.jumlah_panen;
			var kode_farm = rhk.KODE_FARM;
			nama_farm = rhk.NAMA_FARM;
			var no_reg_lhk = rhk.NO_REG;
			var ket = rhk.KETERANGAN1;

			//header

			$('#inp_kandang').val(nama_kandang);
			$('#inp_flock').val(rhk.FLOK_BDY);
			$('#inp_doc_in').val(tgl_doc_in_lhk);
			$('#inp_umur').val(umur);
			umur_lhk = umur;
			$('#inp_tgl_lhk').val(lhk);
			$('#inp_dayahidup_temp').val(rhk.C_DAYA_HIDUP);
			$('#inp_last_bb_rata').val(bb_rata_last);
			$('#inp_status_lhk').val(ket);
			$('#inp_farm').val(kode_farm);
			$('#inp_nama_farm').val(nama_farm);
			$('#inp_noreg_lhk').val(no_reg_lhk);
			$('#inp_tgl_input').val('');
			$('#inp_tgl_trnsaksi').val();
			$('#inp_tgl_transaksi').val(tgl_transaksi);
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
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_jml+'" style="width:100px" name="sekat_jml[]" ></td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_berat+'" style="width:100px" name="sekat_bb[]"></td>'+
						'<td><input type="text" class="form-control input-sm field_input" value="'+bb_ket+'" style="width:100%" name="sekat_ket[]" ></td>'+
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
			$('#inp_ket_kematian').val(rhk.KETERANGAN1);

			$('#inp_tambahLain').attr("disabled", true);
			$('#inp_kurangMati').attr("disabled", true);
			$('#inp_kurangAfkir').attr("disabled", true);
			$('#inp_kurangLain').attr("disabled", true);
			$('#inp_ket_kematian').attr("disabled", true);

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
					'	<td class="vert-align" >' +
						obj.NAMA_BARANG +
						'<input type="hidden" class="form-control input-sm" name="inp_c_nama_pakan[]" value="' + obj.NAMA_BARANG + '">' +
						'<input type="hidden" class="form-control input-sm" name="inp_c_pakan[]" value="' + obj.KODE_BARANG + '">' +
						'<input type="hidden" class="form-control input-sm" name="inp_c_bentuk_pakan[]" value="' + obj.BENTUK_BARANG + '">' +
					'</td>'+
					'	<td class="inp-numeric"><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAwalKg[]" value="' + (Number(Math.round(awalKg * 1000) / 1000).toFixed(3)) + '" disabled></td>'+
					'	<td class="inp-numeric"><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAwalSak[]" value="' + (awalStok) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokReturKg[]" value="' + (Number(Math.round(obj.brt_sak * 1000) / 1000).toFixed(3)) + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokReturSak[]" value="' + obj.jml_retur + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimKg[]" value="' + obj.BRT_TERIMA + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_kirimSak[]"  value="' + obj.JML_TERIMA + '"disabled></td>'+
					'	<td><input type="text" data-jeniskelamin="J" class="form-control input-sm inp-numeric" name="inp_c_terpakaiKg[]" value="'+obj.BRT_PAKAI+'" onchange="return cekNumerikPakanKg(event, this)" title="Kg harus lebih besar dari jumlah Sak" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_terpakaiSak[]" value="' + obj.JML_PAKAI + '" onkeyup="cekNumerikPakanSak(this)"></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirKg[]" value="' + obj.BRT_AKHIR + '" disabled></td>'+
					'	<td><input type="text" class="form-control input-sm inp-numeric" name="inp_c_stokAkhirSak[]" value="' + obj.JML_AKHIR + '" disabled></td>'+
					'</tr>';

					i++;
				}
			}

			var pakan_all = pakan_c;
			$('#lhk_pakan > tbody').html(pakan_all.join(''));

			$('#btnLanjutRilis').hide();
			$('input[name^="sekat_no"]').each(function() {
				$(this).attr("disabled", true);
			});

			$('input[name^="sekat_jml"]').each(function() {
				$(this).attr("disabled", true);
			});

			$('input[name^="sekat_bb"]').each(function() {
				$(this).attr("disabled", true);
			});

			$('input[name^="sekat_ket"]').each(function() {
				$(this).attr("disabled", true);
			});

			$('#inp_tambahLain').attr("disabled", true);
			$('#inp_kurangMati').attr("disabled", true);
			$('#inp_kurangAfkir').attr("disabled", true);
			$('#inp_kurangLain').attr("disabled", true);
			$('#inp_ket_kematian').attr("disabled", true);

			$('input[name^="inp_c_terpakaiSak"]').each(function() {
				$(this).attr("disabled", true);
			});

			$('#modal_lhk').modal('show');
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

$('#btntombolLanjutSimpan').click(function(){
	bootbox.dialog({
		message: "LHK yang dientri sudah benar. Apakah Anda yakin melanjutkan proses penyimpanan LHK?",
		title: "Konfirmasi",
		buttons: {
			success: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					var status_lhk = $('#inp_status_lhk').val();
					if(status_lhk=="SESUAI TIMELINE"){
						acknowledge_kf_final();
					}
					else{
						var status_lhk = $('#inp_status_lhk').val();
						var no_reg_lhk = $('#inp_noreg_lhk').val();
						var kode_farm = $('#inp_farm').val();
						var nama_farm = $('#inp_nama_farm').val();
						var nama_kandang = $('#inp_kandang').val();
						var tgl_entri = $('#inp_tgl_input').val();
						var tgl_transaksi_lhk = $('#inp_tgl_trnsaksi').val();
						var ket_kematian = $('#inp_ket_kematian').val();
						bootbox.prompt({
						title: ""+
						"<table width='100%'>"+
						"<tr><td colspan='4' align='center'><h2>Laporan Harian Kandang<br/>Farm "+nama_farm+"<br/></h2></td><tr>"+

						"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>No. Reg</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + no_reg_lhk + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_transaksi_lhk + "</td><tr>"+
						"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>Kandang</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + nama_kandang + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. Transaksi LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_entri + "</td><tr>"+
						"</table><br><span style='font-size:14px;font-weight:bold'>Keterangan Acknowlegement LHK</span>",
						inputType: 'textarea',
						value: "",
						callback: function(result) {
							if (result === null) {

							} else {
								if(result == null || result == ""){
									bootbox.alert("Mohon mencantumkan keterangan.");
									return false;
								}else{
									ack_ket_var = result;
									acknowledge_kf_final(ack_ket_var);
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

				}
			}
		}
	});
});

function acknowledge_kf_final(ack_desc = null){
	//Penimbangan Berat Badan
	var sekat_no = new Array(),
		sekat_jml = new Array(),
	    sekat_bb = new Array(),
		sekat_ket = new Array(),
		sekat_jml_tot = 0,
		sekat_bb_tot = 0,
		bb_rata = 0;


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

	if(sekat_bb.length > 0 && sekat_jml_tot > 0){
		bb_rata = parseFloat(sekat_bb_tot/sekat_jml_tot) / 1000;
	}

	//Header
	var noreg = $('#inp_noreg_lhk').val();
	var tgl_transaksi = $('#inp_tgl_transaksi').val();
	var populasi_awal = $('#inp_populasi_awal_stlh_umur_7').val();
	var tgl_entri = $('#inp_tgl_input').val();

	//Populasi CAMPUR
	var populasiAwal = $('#inp_populasiAwal').val();
	var tambahLain = $('#inp_tambahLain').val();
	var kurangMati = $('#inp_kurangMati').val();
	var kurangAfkir = $('#inp_kurangAfkir').val();
	var kurangLain = $('#inp_kurangLain').val();
	var populasiAkhir = $('#inp_populasiAkhir').val();
	var populasiDH = $('#inp_dayahidup_temp').val(); // sebelumnya $('#inp_dayahidup').val();
	var ket_kematian = $('#inp_pengisian_keterangan').val();
	if(ket_kematian=='')
		ket_kematian = $('#inp_ket_kematian').val();

	var user_buat = $('#inp_user_buat').val();
	var tgl_buat = $('#inp_tgl_buat').val();

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

	$('input[name^="inp_c_stokAkhirKg"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_stokAkhirKg.push(jml);
	});
	$('input[name^="inp_c_stokAkhirSak"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		c_stokAkhirSak.push(jml);
	});

	var ack_kf = ack_desc;

	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk_bdy/simpan_ack/",
		data: {
			no_reg : noreg,
			tgl_transaksi : tgl_transaksi,
			bb_rata : bb_rata,

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
			ket_kematian : ket_kematian,
			c_pakan : c_pakan,
			c_stokAwalKg : c_stokAwalKg,
			c_stokAwalSak : c_stokAwalSak,
			c_kirimKg : c_kirimKg,
			c_kirimSak : c_kirimSak,
			c_terpakaiKg : c_terpakaiKg,
			c_terpakaiSak : c_terpakaiSak,
			c_stokAkhirKg : c_stokAkhirKg,
			c_stokAkhirSak : c_stokAkhirSak,
			ack_desc : ack_kf,
			tgl_entri : tgl_entri,
			user_buat : user_buat,
			tgl_buat : tgl_buat
		}
	})
	.done(function(data){
		if(data.msg == "success"){
			bootbox.alert("Acknowledge pada no.reg LHK " + noreg + " telah berhasil dilakukan.");

			$('#modal_lhk').modal('hide');
			$('#modal_pengisian_keterangan').modal('hide');

			refreshData();
			refreshMenu();
		}else{
			console.log("failed to update rhk");
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

function checkPengisianKeterangan(elm){
	var length = $(elm).val().length;

	if(length>=10)
		$('#btntombolLanjutSimpan').removeClass('disabled');
	else
		$('#btntombolLanjutSimpan').addClass('disabled');

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
}

function cekNumerikPakanSak(field){
	var selected_noreg = $('#inp_noreg_lhk').val();
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
