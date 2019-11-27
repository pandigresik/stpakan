var ack_ket_var = "";
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
	
	if(level_user == "DB"){
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
	$('#q_lhk_pakan_berlebih').attr("checked", true);
	
	$('#q_lhk_sesuai_timeline').attr("disabled", true);
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
		if ($('#q_lhk_pakan_berlebih').is(":checked"))
		{
			$('#q_lhk_pakan_berlebih').attr("checked", false);
			$("#q_lhk_belum_dientry").attr("disabled", false);
			$('#q_lhk_sesuai_timeline').attr("disabled", false);
		}else{
		}
		
				
		$('#q_belum_konfirmasi').attr("checked", false);
		$('#q_sudah_konfirmasi').attr("checked", false);
		
		$('#q_belum_konfirmasi').attr("disabled", true);
		$('#q_sudah_konfirmasi').attr("disabled", true);
	}
});

$("#q_lhk_sesuai_timeline").change(function () {
    if($(this).is(':checked')){
		if ($('#q_lhk_tidak_sesuai_timeline').is(":checked"))
		{}else{
			$('#q_belum_konfirmasi').attr("checked", false);
			$('#q_sudah_konfirmasi').attr("checked", false);
			
			$('#q_belum_konfirmasi').attr("disabled", true);
			$('#q_sudah_konfirmasi').attr("disabled", true);
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

$("#q_lhk_pakan_berlebih").change(function () {
    if($(this).is(':checked')){
		$('#q_lhk_tidak_sesuai_timeline').attr("disabled", false);
		$('#q_belum_konfirmasi').attr("disabled", false);
		$('#q_sudah_konfirmasi').attr("disabled", false);
		
		$('#q_lhk_sesuai_timeline').attr("checked", false);
		$('#q_lhk_sesuai_timeline').attr("disabled", true);
		$("#q_lhk_belum_dientry").attr("checked", false);
		$("#q_lhk_belum_dientry").attr("disabled", true);
		
	}else{
		if ($('#q_lhk_sesuai_timeline').is(":checked"))
		{}else{
			$("#q_lhk_belum_dientry").attr("disabled", false);
			$('#q_lhk_sesuai_timeline').attr("disabled", false);
			
			$('#q_belum_konfirmasi').attr("disabled", false);
			$('#q_sudah_konfirmasi').attr("disabled", false);
		}
	}
});

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
	
	bootbox.prompt({
		title: ""+
		"<table width='100%'>"+
		"<tr><td colspan='4' align='center'><h2>Laporan Harian Kandang<br/>Farm "+nama_farm+"<br/></h2></td><tr>"+
		
		"<tr><td width='15%' style='font-size:14px;font-weight:bold' align='left'>No. Reg</td><td width='35%' style='font-size:14px;font-weight:normal' align='left'> : " + no_reg + "</td><td width='25%' style='font-size:14px;font-weight:bold' align='left'>Tgl. LHK</td><td width='25%' style='font-size:14px;font-weight:normal' align='left'> : " + tgl_transaksi_lhk + "</td><tr>"+
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
					$.ajax({
						type:'POST',
						dataType: "JSON",
						url : "riwayat_harian_kandang/pemantauan_lhk/simpan_ack/",
						data: {
							no_reg : no_reg,
							tgl_transaksi : tgl_transaksi,
							ack_desc : result
						}
					})
					.done(function(data){
						if(data.msg == "success"){
							bootbox.alert("Acknowledge pada no.reg LHK " + no_reg + " telah berhasil dilakukan.");
							
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
			}
		}
	});
};

function acknowledge_dir(elm){
	var tr = $(elm).parent().parent().parent();
	
	var td_noreg = $(tr).find('td').eq(0);
	var td_tgl_transaksi = $(tr).find('td').eq(3);
	
	var no_reg = $(td_noreg).attr("data-noreg");
	var tgl_transaksi = $(td_tgl_transaksi).attr("data-tgl_transaksi");
	
	//alert(no_reg + tgl_transaksi);
	
	
	$.ajax({
		type:'POST',
		dataType: "JSON",
		url : "riwayat_harian_kandang/pemantauan_lhk/simpan_ack/",
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
		url : "riwayat_harian_kandang/pemantauan_lhk/get_menu_farm/",
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
	q_pakan_berlebih = ($('#q_lhk_pakan_berlebih').is(":checked")) ? 1 : 0;
	
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
		url : "riwayat_harian_kandang/pemantauan_lhk/get_data/",
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
			
		}else{
			var items = data.items;
			
			var rows = new Array();
			var i = 0;
			$.each(items, function(idx, obj) {
				
				var tgl_lhk = (obj.colDate == null) ? '-' : obj.colDate;
				var tgl_entri = (obj.tgl_buat == null) ? '-' : obj.tgl_buat;
				
				var ack_kf = "";
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE")
				// if(obj.stTemp == "TIDAK SESUAI TIMELINE" || obj.tgl_transaksi2 != "")
				console.log(obj.stTemp + "-" + obj.tgl_transaksi2);
			var statusLhk = "-";
			var statusLhkArr = new Array();
				if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))
				{
					
					if(obj.stTemp == "TIDAK SESUAI TIMELINE")
						statusLhkArr.push("Tidak Sesuai Timeline");
					
					if((obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))
						statusLhkArr.push("Konsumsi Pakan Berlebih");
				
					if(tgl_entri != "" && obj.ack_kf == null){
						if(data.level_user == "KF"){
							ack_kf = '<input type="button" class="form-control btn-primary" style="width:100px" value="Ack" onclick="acknowledge_kf(this)"/>';
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
					ack_kf = '-';
				}
				
				var ack_dir = "";
				if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))
				{
					if(tgl_entri != ""  && obj.ack_kf != null && obj.ack_dir == null){
						if(data.level_user == "DB"){
							ack_dir = '<input type="button" class="form-control btn-primary" style="width:100px" value="Ack" onclick="acknowledge_dir(this)"/>';
						}else{
							ack_dir = '-';
						}
					}else if(tgl_entri != "" && obj.ack_dir != null){
						var ack_temp = obj.ack_dir;
						var ack_temp_arr0 = ack_temp.split(' ');
						
						var ack_temp_arr1 = ack_temp_arr0[0].split("-");
						var tahun = ack_temp_arr1[0];
						var bulan = months[(parseInt(ack_temp_arr1[1]) - 1)];
						var hari = parseInt(ack_temp_arr1[2]);
						
						ack_dir = hari + '-' + bulan + '-' + tahun + ' ' + ack_temp_arr0[1];// + ' ' + ack_temp_arr0[2];
					}else{
						ack_dir = '-';
					}					
				}else{
					ack_dir = '-';
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
				if(obj.stTemp == "TIDAK SESUAI TIMELINE" || (obj.stTemp == "TIDAK SESUAI TIMELINE" && (obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)))){
					backColorRow = 'style="color:#FF0000;"';
				}
				
				if(obj.tgl_transaksi2 != "" && obj.tgl_transaksi2 != "null" && !empty(obj.tgl_transaksi2)){
					backColorRow = 'style="color:#0C4EE8;"';
				}
				
				if(obj.stTemp == "BELUM ENTRY - TELAT"){
					backColorRow = 'style="color:#E6A205"';
				}
				
				if(statusLhkArr.length == 2){
					backColorRow = 'style="color:#0C4EE8;"';
				}
				
				rows[i] = "<tr>" +
						  "<td "+backColorRow+" data-kandang='"+obj.nama_kandang+"' data-noreg='" + obj.no_reg + "' class='vert-align'>" + obj.nama_kandang + "</td>" +
						  "<td "+backColorRow+" class='vert-align'>" + obj.noReg + "</td>" +
						  "<td "+backColorRow+" class='vert-align'>" + tgl_lhk + "</td>" +
						  "<td "+backColorRow+" data-tgl_entri='"+tanggal_entri+"' data-tgl_transaksi_lhk='"+tgl_lhk+"' data-tgl_transaksi='" + obj.tgl_transaksi + "'class='vert-align'>" + tgl_entri + "</td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + statusLhk + "</center></td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + ack_kf + "</center></td>" +
						  "<td "+backColorRow+" class='vert-align'><center>" + ack_dir + "</center></td>" +
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

