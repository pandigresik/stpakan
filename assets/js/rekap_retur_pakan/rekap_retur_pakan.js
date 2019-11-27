var selected_noretur, 
    selected_noreg;

var can_propose = ["P"];
var can_approve = ["KF"];
var can_accept = ["AG"];
	
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
months_id[0] = "Jan";
months_id[1] = "Feb";
months_id[2] = "Mar";
months_id[3] = "Apr";
months_id[4] = "Mei";
months_id[5] = "Jun";
months_id[6] = "Jul";
months_id[7] = "Ags";
months_id[8] = "Sep";
months_id[9] = "Okt";
months_id[10] = "Nop";
months_id[11] = "Des";

$(document).ready(function () {
	$( "#inp_tglawal" ).datepicker( { 
		dateFormat: 'dd M yy'
	});
	$( "#inp_tglakhir" ).datepicker( { 
		dateFormat: 'dd M yy'
	});
});

$('#tb_rekap > tbody').on('dblclick','tr',function() {	
	var nama_user = $('#nama_user').val();
	var nama_farm = $('#inp_nama_farm').val();
	var level_user = $('#level_user').val();
	
	var no_retur = $(this).find('td:nth-child(1)').text();
	var no_reg = $(this).find('td:nth-child(2)').text();
	var nama_kandang = $(this).find('td:nth-child(3)').text();
	var tgl_tutup_siklus = $(this).find('td:nth-child(5)').text();
	var tgl_approve = $(this).find('td:nth-child(10)').text();
	
	selected_noretur = no_retur;
	selected_noreg = no_reg;
	
	var jml = $(this).find('td:nth-child(8)').text();
	var berat = $(this).find('td:nth-child(9)').text();
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "rekap_retur_pakan/get_retur_pakan/",
		data: {
			no_retur : no_retur,
			no_reg : no_reg
		}
	})
	.done(function(data){
		var html = new Array();
		var tgl_retur = "";
		var tgl_approve = "";
		var tgl_terima = "";
		
		var nama_retur = "";
		var nama_approve = "";
		var nama_terima = "";
		
		
		for(var i=0;i<data.length;i++){
			tgl_retur = (!empty(data[i]["tgl_retur"])) ? data[i]["tgl_retur"] : "";
			tgl_approve = (!empty(data[i]["user_approve"])) ? data[i]["tgl_approve"] : "";
			tgl_terima = (!empty(data[i]["user_terima"])) ? data[i]["tgl_terima"] : "";	

			nama_retur = (!empty(data[i]["tgl_retur"])) ? data[i]["user_buat"] : "";
			nama_approve = data[i]["user_approve"];
			nama_terima = data[i]["user_terima"];
			
			html[i] = '<tr>'+
			'<td>'+data[i]["KODE_BARANG"]+'</td>'+
			'<td>'+data[i]["NAMA_BARANG"]+'</td>'+
			'<td align="right">'+data[i]["JML"]+'</td>'+
			'<td align="right">'+data[i]["BRT"]+'</td>'+
			'<td>'+data[i]["BENTUK_BARANG"]+'</td>'+
			'</tr>';
		}
		
		if(can_propose.indexOf(level_user) >= 0){ //pengajuan retur : Pengawas Kandang
			if(empty(tgl_retur)){//belum melakukan pengajuan retur
				$('#pengajuan_print_nama_kandang').html(nama_kandang);
				$('#pengajuan_print_tgl_lhk').html(tgl_tutup_siklus);
				$('#pengajuan_titlefarm').html(nama_farm);
				
				$('#pengajuan_inp_print_no_retur').val(no_retur);
				$('#pengajuan_inp_print_no_reg').val(no_reg);
				
				$('#pengajuan_tb_sisa > tbody').html(html.join(''));
				$('#pengajuan_modal_sisa').modal("show");
			}
			else{
				if(!empty(tgl_approve)){
					$('#btnPrint').removeClass('disabled');
					$('#id_user_approve').html('<center>(' + nama_approve + ')</center>');
					$('#print_sj_retur').text(selected_noretur);
					$('#print_barcode_sj').barcode(selected_noretur, "code39", {showHRI:false,barWidth:2});
				}else{
					$('#id_user_approve').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				}
				
				if(!empty(tgl_terima)){
					$('#id_user_terima').html('<center>(' + nama_terima + ')</center>');
				}else{
					$('#id_user_terima').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				}
				
				$('#print_nama_kandang').html(nama_kandang);
				$('#print_tgl_lhk').html(tgl_tutup_siklus);
				$('#titlefarm').html(nama_farm);
				$('#label_sj').hide();
				
				$('#inp_print_tgl_lhk').val(tgl_tutup_siklus);
				$('#inp_print_nama_farm').val(nama_farm);
				$('#inp_print_nama_kandang').val(nama_kandang);
				$('#inp_print_no_retur').val(no_retur);
				$('#inp_print_no_reg').val(no_reg);
				$('#inp_print_nama_retur').val(nama_retur);
				$('#inp_print_nama_approve').val(nama_approve);
				$('#inp_print_nama_terima').val(nama_terima);
				$('#id_user_buat').html('<center>(' + nama_retur + ')</center>');
				
				$('#tb_sisa > tbody').html(html.join(''));
				$('#modal_sisa').modal("show");
			}
		}else if(can_approve.indexOf(level_user) >= 0){//approve retur : Kepala Farm€
			if(empty(tgl_retur)){//belum melakukan pengajuan retur
				$('#pengajuan_print_nama_kandang').html(nama_kandang);
				$('#pengajuan_print_tgl_lhk').html(tgl_tutup_siklus);
				$('#pengajuan_titlefarm').html(nama_farm);
				
				$('#pengajuan_inp_print_no_retur').val(no_retur);
				$('#pengajuan_inp_print_no_reg').val(no_reg);
				
				$('#pengajuan_tb_sisa > tbody').html(html.join(''));
				$('#id_user_buat2').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				$('#pengajuan_modal_sisa').modal("show");
			}
			else{
				if(!empty(tgl_approve)){
					$('#btnPrint').removeClass('disabled');
					$('#id_user_approve').html('<center>(' + nama_approve + ')</center>');
				}else{
					$('#id_user_approve').html('<div id="app_point"><button name="tombolPrint" onclick="approve(\'SETUJU\')" class="btn btn-primary">Approve</button></div>');
				}
				
				if(!empty(tgl_terima)){
					$('#id_user_terima').html('<center>(' + nama_terima + ')</center>');
				}else{
					$('#id_user_terima').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				}
				
				$('#print_nama_kandang').html(nama_kandang);
				$('#print_tgl_lhk').html(tgl_tutup_siklus);
				$('#titlefarm').html(nama_farm);
				$('#print_sj_retur').html(no_retur);
				$('#print_sj_retur').text(selected_noretur);
				$('#print_barcode_sj').barcode(selected_noretur, "code39", {showHRI:false,barWidth:1});
				
				$('#inp_print_tgl_lhk').val(tgl_tutup_siklus);
				$('#inp_print_nama_farm').val(nama_farm);
				$('#inp_print_nama_kandang').val(nama_kandang);
				$('#inp_print_no_retur').val(no_retur);
				$('#inp_print_no_reg').val(no_reg);
				$('#inp_print_nama_retur').val(nama_retur);
				$('#inp_print_nama_approve').val(nama_approve);
				$('#inp_print_nama_terima').val(nama_terima);
				$('#id_user_buat').html('<center>(' + nama_retur + ')</center>');
				
				$('#tb_sisa > tbody').html(html.join(''));
				$('#modal_sisa').modal("show");
			}
		}else if(can_accept.indexOf(level_user) >= 0){//terima retur : Admin Gudang
			if(empty(tgl_retur)){//belum melakukan pengajuan retur
				$('#pengajuan_print_nama_kandang').html(nama_kandang);
				$('#pengajuan_print_tgl_lhk').html(tgl_tutup_siklus);
				$('#pengajuan_titlefarm').html(nama_farm);
				
				$('#pengajuan_inp_print_no_retur').val(no_retur);
				$('#pengajuan_inp_print_no_reg').val(no_reg);
				
				$('#pengajuan_tb_sisa > tbody').html(html.join(''));
				$('#id_user_buat2').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				$('#pengajuan_modal_sisa').modal("show");
			}else{				
				if(!empty(tgl_approve)){
					$('#btnPrint').removeClass('disabled');
					$('#id_user_approve').html('<center>(' + nama_approve + ')</center>');
				}else{
					$('#id_user_approve').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
					$('#id_user_terima').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				}
				
				if(!empty(tgl_approve) && !empty(tgl_terima)){
					$('#id_user_terima').html('<center>(' + nama_terima + ')</center>');
				}else if(!empty(tgl_approve)){
					$('#id_user_terima').html('<div id="app_point"><button name="tombolPrint" onclick="approve(\'TERIMA\')" class="btn btn-primary">Approve</button></div>');
				}else{
					$('#id_user_terima').html("(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)");
				}
				
				$('#print_nama_kandang').html(nama_kandang);
				$('#print_tgl_lhk').html(tgl_tutup_siklus);
				$('#titlefarm').html(nama_farm);
				$('#print_sj_retur').html(no_retur);
				$('#print_sj_retur').text(selected_noretur);
				$('#print_barcode_sj').barcode(selected_noretur, "code39", {showHRI:false,barWidth:1});
				
				$('#inp_print_tgl_lhk').val(tgl_tutup_siklus);
				$('#inp_print_nama_farm').val(nama_farm);
				$('#inp_print_nama_kandang').val(nama_kandang);
				$('#inp_print_no_retur').val(no_retur);
				$('#inp_print_no_reg').val(no_reg);
				$('#inp_print_nama_retur').val(nama_retur);
				$('#inp_print_nama_approve').val(nama_approve);
				$('#inp_print_nama_terima').val(nama_terima);
				$('#id_user_buat').html('<center>(' + nama_retur + ')</center>');
				
				$('#tb_sisa > tbody').html(html.join(''));
				$('#modal_sisa').modal("show");
			}
		}else{}
		
		if(!empty(tgl_retur) && !empty(tgl_approve)){
			$("#btnPrint").removeClass("disabled");	
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
	
});

$('#btnTampilkan').click(function(){
	refreshData();
});

$('#pengajuan_btnPrint').click(function(e){
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
							url : "rekap_retur_pakan/proses_pengajuan/",
							data: {
								no_retur : selected_noretur,
								no_reg : selected_noreg
							}
						})
						.done(function(data){
							if(data.result == "success"){
								toastr.success("Pengajuan Retur Pakan berhasil dilakukan",'Informasi');
								
								$('#pengajuan_modal_sisa').modal("hide");
								refreshData();
							}else{
								toastr.warning("Pengajuan Retur Pakan gagal dilakukan",'Informasi');
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
					}
				}
			}
		}
	);
});

function approve(status){
	bootbox.dialog({
			message: "Apakah Anda yakin melakukan persetujuan retur pakan ke gudang?",
			title: "Konfirmasi",
			buttons: {
				success: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						$('#print_sj_retur').text(selected_noretur);
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "rekap_retur_pakan/proses_persetujuan/",
							data: {
								no_retur : selected_noretur,
								no_reg : selected_noreg
							}
						})
						.done(function(data){
							if(data.result == "success"){
								var level_user = $('#level_user').val();
								if(can_approve.indexOf(level_user) >= 0){
									$('#id_user_approve').html('<center>(' + data.nama_pegawai + ')</center>');
									$('#inp_print_nama_approve').val(data.nama_pegawai);
								}else if(can_accept.indexOf(level_user) >= 0){
									$('#id_user_terima').html('<center>(' + data.nama_pegawai + ')</center>');
									$('#inp_print_nama_terima').val(data.nama_pegawai);
								}else{
									
								}
								
								$('#btnPrint').removeClass("disabled");
								refreshData();
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
					}
				}
			}
		}
	);
}

function refreshData(){
	var kode_farm = $('#inp_farm').val();
	var tgl_awal = $('#inp_tglawal').val();
	var tgl_akhir = $('#inp_tglakhir').val();
	
	if(!empty(tgl_awal)){
		var tgl_awal_arr = tgl_awal.split(" "); 
		
		var index = (months.indexOf(tgl_awal_arr[1]) >= 0) ? months.indexOf(tgl_awal_arr[1]) : months_id.indexOf(tgl_awal_arr[1]);
		tahun_awal = tgl_awal_arr[2];
		bulan_awal = (parseInt(index) + 1);
		hari_awal = tgl_awal_arr[0];
		
		tgl_awal = tahun_awal + "-" + bulan_awal + "-" + hari_awal;
	}
	
	if(!empty(tgl_akhir)){
		var tgl_akhir_arr = tgl_akhir.split(" "); 
		var index = (months.indexOf(tgl_akhir_arr[1]) >= 0) ? months.indexOf(tgl_akhir_arr[1]) : months_id.indexOf(tgl_akhir_arr[1]);
		tahun_akhir = tgl_akhir_arr[2];
		bulan_akhir = (parseInt(index) + 1);
		hari_akhir = tgl_akhir_arr[0];
		
		tgl_akhir = tahun_akhir + "-" + bulan_akhir + "-" + hari_akhir;
	}
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "rekap_retur_pakan/get_retur_pakan_list/",
		data: {
			kode_farm : kode_farm,
			tgl_awal : tgl_awal,
			tgl_akhir : tgl_akhir
		}
	})
	.done(function(data){
		var html = new Array();
		for(var i=0;i<data.length;i++){
			var tgl_retur = (!empty(data[i]["tgl_retur"])) ? data[i]["tgl_retur"] : "";
			var tgl_approve = (!empty(data[i]["nama_approve"])) ? data[i]["tgl_approve"] : "";
			var tgl_terima = (!empty(data[i]["nama_terima"])) ? data[i]["tgl_terima"] : "";
			var no_retur = (!empty(data[i]["tgl_retur"])) ? data[i]["no_retur"] : "";
			
			var jml_retur = (!empty(tgl_retur) && !empty(data[i]["jml_retur"])) ? data[i]["jml_retur"] : "";
			var brt_retur = (!empty(tgl_retur) && !empty(data[i]["brt_retur"])) ? data[i]["brt_retur"] : "";
			
			var jml_putaway = (!empty(tgl_terima) && !empty(data[i]["jml_retur"])) ? data[i]["jml_retur"] : "";
			var brt_putaway = (!empty(tgl_terima) && !empty(data[i]["brt_putaway"])) ? data[i]["brt_putaway"] : "";
			
			html[i] = '<tr>'+
				'<td class="hidden">'+data[i]["no_retur"]+'</td>'+
				'<td class="hidden">'+data[i]["no_reg"]+'</td>'+
				'<td class="hidden">'+data[i]["kode_kandang"]+'</td>'+
				'<td class="link">'+data[i]["nama_kandang"]+'</td>'+
				'<td class="link">'+data[i]["tgl_tutupsiklus"]+'</td>'+
				'<td class="link">'+data[i]["nama_barang"]+'</td>'+
				'<td class="link">'+no_retur+'</td>'+
				'<td class="link">'+tgl_retur+'</td>'+
				'<td class="link" align="right">'+jml_retur+'</td>'+
				'<td class="link" align="right">'+brt_retur+'</td>'+
				'<td class="link">'+tgl_approve+'</td>'+
				'<td class="link">'+tgl_terima+'</td>'+
				'<td class="link" align="right">'+jml_putaway+'</td>'+
				'<td class="link" align="right">'+brt_putaway+'</td>'+
				'</tr>';
		}
		
		$('#tb_rekap > tbody').html(html.join(''));
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}