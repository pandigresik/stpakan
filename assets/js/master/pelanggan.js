var search = false;
var page_number=0;
var total_page =null;
var sr =0;
var sr_no =0;

var form_mode = "";
var selected_pelanggan = "";

function getReport(page_number){
	if(page_number==0){
		$("#previous").prop('disabled', true);}
	else{
		$("#previous").prop('disabled', false);}
		
	if(page_number==(total_page-1)){
		$("#next").prop('disabled', true);}
	else{
		$("#next").prop('disabled', false);}
	
	$("#page_number").text(page_number+1);
	
	kodepelanggan = $('#q_kodepelanggan').val();
	namapelanggan = $('#q_namapelanggan').val();
	alamat = $('#q_alamat').val();
	kota = $('#q_kota').val();
	
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/pelanggan/get_pagination/",
		data: {
			kodepelanggan : kodepelanggan,
			namapelanggan : namapelanggan,
			alamat : alamat,
			kota : kota,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-pelanggan").html("");
		
		window.mydata = data;
		
		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;
				
				$("tbody", "#master-pelanggan").html("");
				$.each(record_par_page, function (key, data) {
					$("tbody", "#master-pelanggan").append('<tr><td>'+data.row+'</td><td>'+data.kode_pelanggan+'</td><td>'+data.nama_pelanggan+'</td><td>'+data.alamat+'</td><td>'+data.kota+'</td></tr>');

				});
				
				if(total_page == 1)
					$("#next").prop('disabled', true);
			}
		}else{
			$("#page_number").text('0');
			$("#total_page").text('0');	
			$("#next").prop('disabled', true);
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

$(document).ready(function () {
	getReport(page_number);
	 
	$("#next").on("click", function(){
		page_number = (page_number+1);
		getReport(page_number);
	});
	
	$("#previous").on("click", function(){
		page_number = (page_number-1);
		getReport(page_number);
	});
});


$('.q_search').keyup(function(){
	this.value = this.value.toUpperCase();
	goSearch();
});

$('.field_input').keyup(function(){
	checkInput();
});

$('#master-pelanggan').on('click','tr',function() {
	selected_pelanggan = $(this).find('td:nth-child(2)').text();
});

$('#inp_kodepelanggan').keyup(function(){
	this.value = this.value.toUpperCase();
});

$('#inp_namapelanggan').keyup(function(){
	this.value = this.value.toUpperCase();
});

$('#master-pelanggan > tbody').on('dblclick','tr',function() {	
	selected_pelanggan = $(this).find('td:nth-child(2)').text();
	form_mode = "ubah";
	
	kodepelanggan = $(this).find('td:nth-child(2)').text();
	namapelanggan = $(this).find('td:nth-child(3)').text();
	alamat = $(this).find('td:nth-child(4)').text();
	kota = $(this).find('td:nth-child(5)').text();
		
	$('#inp_kodepelanggan').val(kodepelanggan);
	$('#inp_namapelanggan').val(namapelanggan);
	$('#inp_alamat').val(alamat);
	$('#inp_kota').val(kota);
	
	$('#btnSimpan').hide();
	$('#btnUbah').show();
	$('#btnUbah').removeClass('disabled');
	$('#inp_kodepelanggan').attr("disabled", true);
	
	$('#modal_pelanggan').modal("show");
	
});

$("#btnTambah").click(function(){
	resetInput();
	form_mode = "tambah";
	
	$('#inp_kodepelanggan').attr("disabled", false);
	$('#btnSimpan').show();
	$('#btnUbah').hide();
	
	$('#modal_pelanggan').modal("show");
});

$("#btnBatal").click(function(){
	$('#modal_pelanggan').modal("hide");
	resetInput();
});

$('#btnSimpan').click(function(){
	
	kodepelanggan = $('#inp_kodepelanggan').val();
	namapelanggan = $('#inp_namapelanggan').val();
	alamat = $('#inp_alamat').val();
	kota = $('#inp_kota').val();
	
	if(empty(kodepelanggan) || empty(namapelanggan) || empty(alamat) || empty(kota)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		
		return false;
	}
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/pelanggan/check_kodepelanggan/",
		data: {
			kodepelanggan : kodepelanggan
		}
	})
	.done(function(data){
		if(data.jumlah > 0){
			bootbox.alert("Kode Pelanggan " + kodepelanggan + " sudah terdaftar");
		}else{
			bootbox.dialog({
				message: "Apakah Anda yakin akan Menyimpan data Pelanggan?",
				title: "",
				buttons: {
					main: {
						label: "Ya",
						className: "btn-primary",
						callback: function() {
							$.ajax({
								type:'POST',
								dataType: 'json',
								url : "master/pelanggan/add_pelanggan/",
								data: {
									kodepelanggan : kodepelanggan,
									namapelanggan : namapelanggan,
									alamat : alamat,
									kota : kota
								}
							})
							.done(function(data){
								if(data.result == "success"){
									toastr.success("Penyimpanan Pelanggan dengan Kode Pelanggan " + kodepelanggan + " berhasil dilakukan.",'Informasi');
									
									$('#modal_pelanggan').modal("hide");
									resetInput();
									
									getReport(page_number);
								}else{
									toastr.warning("Penyimpanan Pelanggan dengan Kode Pelanggan " + kodepelanggan + " gagal dilakukan.",'Peringatan');
								}
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
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
});

$("#btnUbah").click(function(){
	kodepelanggan = $('#inp_kodepelanggan').val();
	namapelanggan = $('#inp_namapelanggan').val();
	alamat = $('#inp_alamat').val();
	kota = $('#inp_kota').val();
	
	if(empty(kodepelanggan) || empty(namapelanggan) || empty(alamat) || empty(kota)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		
		return false;
	}
	
	bootbox.dialog({
		message: "Apakah Anda yakin akan Mengubah data Pelanggan?",
		title: "",
		buttons: {
			main: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "master/pelanggan/update_pelanggan/",
						data: {
							kodepelanggan : kodepelanggan,
							namapelanggan : namapelanggan,
							alamat : alamat,
							kota : kota
						}
					})
					.done(function(data){
						if(data.result == "success"){
							toastr.success("Perubahan Pelanggan dengan Kode Pelanggan " + kodepelanggan + " berhasil diperbarui.",'Informasi');
							
							$('#modal_pelanggan').modal("hide");
							resetInput();
							
							getReport(page_number);
						}else{
							toastr.warning("Perubahan Pelanggan dengan Kode Pelanggan " + kodepelanggan + " gagal diperbarui.",'Peringatan');
						}
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
});


/*
* FUNCTION
*/

function resetInput(){
	$('#inp_kodepelanggan').val('');
	$('#inp_namapelanggan').val('');
	$('#inp_alamat').val('');
	$('#inp_kota').val('');
}

function goSearch(){
	search = true;
	page_number = 0;
	getReport(page_number);	
}

function checkInput(){
	
	// kodepelanggan = $('#inp_kodepelanggan').val();
	// namapelanggan = $('#inp_namapelanggan').val();
	// alamat = $('#inp_alamat').val();
	// kota = $('#inp_kota').val();
	
	// if(kodepelanggan != "" && namapelanggan != "" && alamat != "" && kota != ""){
		// if(form_mode == "tambah")
			// $('#btnSimpan').removeClass("disabled");
		
		// if(form_mode == "ubah")
			// $('#btnUbah').removeClass("disabled");
	// }
	// else{
		// if(form_mode == "tambah")
			// $('#btnSimpan').addClass("disabled");
		
		// if(form_mode == "ubah")
			// $('#btnUbah').addClass("disabled");
	// }
		
}