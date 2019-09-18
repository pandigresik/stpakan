var search = false;
var page_number=0;
var total_page =null;

var form_mode = "";
var selected_gudang = "";
var selected_farm = "";

function cekNumerik(field){
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	}
}

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

	namafarm = $('#q_namafarm').val();
	kodegudang = $('#q_kodegudang').val();
	namagudang = $('#q_namagudang').val();
	beratmaksimal = $('#q_maxberat').val();
	qtymaksimal = $('#q_maxkuantitas').val();

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/gudang/get_pagination/",
		data: {
			namafarm : namafarm,
			kodegudang : kodegudang,
			namagudang : namagudang,
			beratmaksimal : beratmaksimal,
			qtymaksimal : qtymaksimal,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-gudang").html("");

		window.mydata = data;

		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;

				$.each(record_par_page, function (key, data) {
					$("tbody", "#master-gudang").append('<tr><td>'+
					data.row+'</td><td>'+
					data.nama_farm+'</td><td>'+
					data.kode_gudang+'</td><td>'+
					data.kode_farm+'</td><td>'+
					data.nama_gudang+'</td><td>'+
					data.max_berat+'</td><td>'+
					data.max_kuantitas+'</td></tr>');

				});

				$('#master-gudang th:nth-child(4)').hide();
				$('#master-gudang td:nth-child(4)').hide();

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

$('#inp_kodegudang').keyup(function(){
	this.value = this.value.toUpperCase();
});

$('#inp_namagudang').keyup(function(){
	this.value = this.value.toUpperCase();
});

$('#master-gudang').on('click','tr',function() {
	selected_gudang = $(this).find('td:nth-child(3)').text();
});

$('#master-gudang > tbody').on('dblclick','tr',function() {
	selected_gudang = $(this).find('td:nth-child(3)').text();
	selected_farm = $(this).find('td:nth-child(4)').text();
	form_mode = "ubah";

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/gudang/get_gudang/",
		data: {
			kodefarm : selected_farm,
			kodegudang : selected_gudang
		}
	})
	.done(function(data){
		$('#inp_namafarm').val(data.kode_farm);
		$('#inp_kodegudang').val(data.kode_gudang);
		$('#inp_namagudang').val(data.nama_gudang);
		$('#inp_maxkuantitas').val(data.max_kuantitas);
		$('#inp_maxberat').val(data.max_berat);

		$('#inp_namafarm').attr("disabled", true);
		$('#inp_kodegudang').attr("disabled", true);


		$('#btnSimpan').hide();
		$('#btnUbah').show();
		$('#btnUbah').removeClass('disabled');

		$('#modal_gudang').modal("show");
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
});

$("#btnTambah").click(function(){
	resetInput();
	form_mode = "tambah";

	$('#inp_namafarm').attr("disabled", false);
	$('#inp_kodegudang').attr("disabled", false);
	$('#btnSimpan').show();
	$('#btnUbah').hide();

	$('#modal_gudang').modal("show");
});

$("#btnBatal").click(function(){
	$('#modal_gudang').modal("hide");
	resetInput();
});

$('#btnSimpan').click(function(){

	kodefarm = $('#inp_namafarm').val();
	kodegudang = $('#inp_kodegudang').val();
	namagudang = $('#inp_namagudang').val();
	beratmaksimal = $('#inp_maxberat').val();
	qtymaksimal = $('#inp_maxkuantitas').val();

	if(empty(kodefarm) || empty(kodegudang) || empty(namagudang) || empty(qtymaksimal) || empty(beratmaksimal)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");

		return false;
	}

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/gudang/cek_kodegudang/",
		data: {
			kodefarm : kodefarm,
			kodegudang : kodegudang
		}
	})
	.done(function(data){
		if(data.jumlah > 0){
			bootbox.alert("Kode Gudang " + kodegudang + " sudah terdaftar");
		}else{
			bootbox.dialog({
				message: "Apakah Anda yakin akan Menyimpan data Gudang ini?",
				title: "",
				buttons: {
					main: {
						label: "Ya",
						className: "btn-primary",
						callback: function() {
							$.ajax({
								type:'POST',
								dataType: 'json',
								url : "master/gudang/add_gudang/",
								data: {
									kodefarm : kodefarm,
									kodegudang : kodegudang,
									namagudang : namagudang,
									beratmaksimal : beratmaksimal,
									qtymaksimal : qtymaksimal
								}
							})
							.done(function(data){
								if(data.result == "success"){
									toastr.success("Penyimpanan Gudang dengan kode " + kodegudang + " berhasil dilakukan.",'Informasi');

									$('#modal_gudang').modal("hide");
									resetInput();

									getReport(page_number);
								}else{
									toastr.warning("Penyimpanan Gudang dengan kode " + kodegudang + " gagal dilakukan.",'Peringatan');
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
	kodefarm = $('#inp_namafarm').val();
	kodegudang = $('#inp_kodegudang').val();
	namagudang = $('#inp_namagudang').val();
	beratmaksimal = $('#inp_maxberat').val();
	qtymaksimal = $('#inp_maxkuantitas').val();

	if(empty(kodefarm) || empty(kodegudang) || empty(namagudang) || empty(qtymaksimal) || empty(beratmaksimal)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");

		return false;
	}

	bootbox.dialog({
		message: "Apakah Anda yakin akan Mengubah data Gudang ini?",
		title: "",
		buttons: {
			main: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "master/gudang/update_gudang/",
						data: {
							kodefarm : kodefarm,
							kodegudang : kodegudang,
							namagudang : namagudang,
							beratmaksimal : beratmaksimal,
							qtymaksimal : qtymaksimal
						}
					})
					.done(function(data){
						if(data.result == "success"){
							toastr.success("Perubahan data Gudang dengan kode " + kodegudang + " berhasil dilakukan.",'Informasi');

							$('#modal_gudang').modal("hide");
							resetInput();

							getReport(page_number);
						}else{
							toastr.warning("Perubahan data Gudang dengan kode " + kodegudang + " gagal dilakukan.",'Peringatan');
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
	$('#inp_namafarm :nth-child(1)').prop('selected', true);
	$('#inp_kodegudang').val('');
	$('#inp_namagudang').val('');
}

function goSearch(){
	page_number = 0;
	search = true;
	getReport(page_number);
}

function checkInput(){

	// kodefarm = $('#inp_namafarm').val();
	// kodegudang = $('#inp_kodegudang').val();
	// namagudang = $('#inp_namagudang').val();

	// if(kodefarm != "" && kodegudang != "" && namagudang != "" ){
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
