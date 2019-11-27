var search = false;
var page_number=0;
var total_page =null;

var form_mode = "";
var selected_farm = "";

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
	
	kodefarm = $('#q_kodefarm').val();
	namafarm = $('#q_namafarm').val();
	alamat = $('#q_alamat').val();
	kota = $('#q_kota').val();
	tipefarm = $('#q_tipefarm').val();
	grup = $('#q_grup').val();
	gruppelanggan = $('#q_gruppelanggan').val();
	
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/farm/get_pagination/",
		data: {
			kodefarm : kodefarm,
			namafarm : namafarm,
			alamat : alamat,
			kota : kota,
			tipefarm : tipefarm,
			grup : grup,
			gruppelanggan : gruppelanggan,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-farm").html("");
		
		window.mydata = data;
		
		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;
				
				$.each(record_par_page, function (key, data) {
					
					var tipe = (data.tipe_farm == "I") ? "Internal" : "Ekstrenal";
					var grup = (data.grup_farm == "BRD") ? "Breeding" : "Budidaya";
					
					
					$("tbody", "#master-farm").append('<tr data-jmlflok="'+data.jml_flok+'"><td>'+
					data.row+'</td><td>'+
					data.kode_farm+'</td><td>'+
					data.nama_farm+'</td><td>'+
					data.alamat+'</td><td>'+
					data.kota+'</td><td>'+
					tipe+'</td><td>'+
					grup+'</td><td>'+
					data.grup_pelanggan+'</td></tr>');

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
	
	$('#inp_jmlflok').priceFormat({
		prefix: '',
		centsLimit : 0,
	    thousandsSeparator: '.'
	});
	
});


$('.q_search').keyup(function(){
	this.value = this.value.toUpperCase();
	goSearch();
});

$('#q_tipefarm').change(function(){
	goSearch();
});

$('#q_grup').change(function(){
	goSearch();
});

$('.field_input').keyup(function(){
	checkInput();
});

$('#master-farm').on('click','tr',function() {
	selected_farm = $(this).find('td:nth-child(2)').text();
});

$('#master-farm > tbody').on('dblclick','tr',function() {	
	selected_farm = $(this).find('td:nth-child(2)').text();
	form_mode = "ubah";
	
	kodefarm = $(this).find('td:nth-child(2)').text();
	namafarm = $(this).find('td:nth-child(3)').text();
	alamat = $(this).find('td:nth-child(4)').text();
	kota = $(this).find('td:nth-child(5)').text();
	tipefarm = $(this).find('td:nth-child(6)').text();
	grupfarm = $(this).find('td:nth-child(7)').text();
	gruppelanggan = $(this).find('td:nth-child(8)').text();
	jmlFlok = empty($(this).data('jmlflok')) ? 0 : $(this).data('jmlflok') ;	
	$('#inp_kodefarm').val(kodefarm);
	$('#inp_namafarm').val(namafarm);
	$('#inp_alamat').val(alamat);
	$('#inp_kota').val(kota);
	$('#inp_jmlflok').val(jmlFlok);
	if(tipefarm == "Internal")
		$("#inp_tipefarminternal").prop("checked", true);
	else
		$("#inp_tipefarmeksternal").prop("checked", true);
	
	if(grupfarm == "Breeding")
		$("#inp_grupfarmbreeding").prop("checked", true);
	else
		$("#inp_grupfarmbudidaya").prop("checked", true);
	
	$("select option").filter(function() {
		return $(this).text() == gruppelanggan; 
	}).prop('selected', true);
	
	/* cek apakah jmlfok harus disable atau tidak */
	mFarm.enableJmlFlok();
	
	$('#btnSimpan').hide();
	$('#btnUbah').show();
	$('#btnUbah').removeClass('disabled');
	$('#inp_kodefarm').attr("disabled", true);
	
	$('#modal_farm').modal("show");
	
});

$("#btnTambah").click(function(){
	resetInput();
	form_mode = "tambah";
	
	$('#inp_kodefarm').attr("disabled", false);
	$('#btnSimpan').show();
	$('#btnUbah').hide();
	
	$('select.multicolumn').combomulticolumn();
	
	$('#modal_farm').modal("show");
	
});

$("#btnBatal").click(function(){
	$('#modal_farm').modal("hide");
	resetInput();
});

$('#btnSimpan').click(function(){
	
	kodefarm = $('#inp_kodefarm').val();
	namafarm = $('#inp_namafarm').val();
	alamat = $('#inp_alamat').val();
	kota = $('#inp_kota').val();
	tipefarm = $('input:radio[name=tipefarm]:checked').val();
	grupfarm = $('input:radio[name=grupfarm]:checked').val();
	gruppelanggan = $('#inp_gruppelanggan').val();
	var elmJmlFlok = $('#inp_jmlflok');
	jmlFlok = elmJmlFlok.val(); 
	
	if(empty(kodefarm) || empty(namafarm) || empty(alamat) || empty(kota)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		return false;
	}
	
	var dataKirim = {
			kodefarm : kodefarm,
			namafarm : namafarm,
			alamat : alamat,
			kota : kota,
			tipefarm : tipefarm,
			grupfarm : grupfarm,
			gruppelanggan : gruppelanggan
	};
	
	if(!elmJmlFlok.prop('disabled')){
		if(jmlFlok <= 0){
			bootbox.alert("Jumlah flok harus lebih besar dari 0");
			return false;
		}
		else{
			dataKirim['jmlFlok'] = jmlFlok; 
		}
	}
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/farm/cek_kodefarm/",
		data: {
			kode_farm : kodefarm
		}
	})
	.done(function(data){
		if(data.jumlah > 0){
			bootbox.alert("Kode Farm " + kodefarm + " sudah terdaftar");
		}else{
			bootbox.dialog({
				message: "Apakah Anda yakin akan Menyimpan data Farm?",
				title: "",
				buttons: {
					main: {
						label: "Ya",
						className: "btn-primary",
						callback: function() {
							$.ajax({
								type:'POST',
								dataType: 'json',
								url : "master/farm/add_farm/",
								data: dataKirim 
							})
							.done(function(data){
								if(data.result == "success"){
									toastr.success("Penyimpanan Farm dengan nama " + namafarm + " berhasil dilakukan.",'Informasi');
									
									$('#modal_farm').modal("hide");
									resetInput();
									
									getReport(page_number);
								}else{
									toastr.warning("Penyimpanan Farm dengan nama " + namafarm + " gagal dilakukan.",'Peringatan');
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
	kodefarm = $('#inp_kodefarm').val();
	namafarm = $('#inp_namafarm').val();
	alamat = $('#inp_alamat').val();
	kota = $('#inp_kota').val();
	tipefarm = $('input:radio[name=tipefarm]:checked').val();
	grupfarm = $('input:radio[name=grupfarm]:checked').val();
	gruppelanggan = $('#inp_gruppelanggan').val();
	var elmJmlFlok = $('#inp_jmlflok');
	jmlFlok = elmJmlFlok.val(); 
	if(empty(kodefarm) || empty(namafarm) || empty(alamat) || empty(kota)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		return false;
	}
	
	var dataKirim = {
			kodefarm : kodefarm,
			namafarm : namafarm,
			alamat : alamat,
			kota : kota,
			tipefarm : tipefarm,
			grupfarm : grupfarm,
			gruppelanggan : gruppelanggan
	};
	
	if(!elmJmlFlok.prop('disabled')){
		if(jmlFlok <= 0){
			bootbox.alert("Jumlah flok harus lebih besar dari 0");
			return false;
		}
		else{
			dataKirim['jmlFlok'] = jmlFlok; 
		}
	}
	
	bootbox.dialog({
		message: "Apakah Anda yakin akan Mengubah data Farm?",
		title: "",
		buttons: {
			main: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "master/farm/update_farm/",
						data: dataKirim
					})
					.done(function(data){
						if(data.result == "success"){
							toastr.success("Perubahan data Farm dengan nama " + namafarm + " berhasil dilakukan.",'Informasi');
							
							$('#modal_farm').modal("hide");
							resetInput();
							
							getReport(page_number);
						}else{
							toastr.warning("Perubahan data Farm dengan nama " + namafarm + " gagal dilakukan.",'Peringatan');
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
	$('#inp_kodefarm').val('');
	$('#inp_namafarm').val('');
	$('#inp_alamat').val('');
	$('#inp_kota').val('');
	$("#inp_tipefarminternal").prop("checked", true);
	$("#inp_grupfarmbreeding").prop("checked", true);
	$('#inp_jmlflok').prop('disabled',1);
}

function goSearch(){
	page_number = 0;
	search = true;
	getReport(page_number);	
}

function checkInput(){
	
	// kodefarm = $('#inp_kodefarm').val();
	// namafarm = $('#inp_namafarm').val();
	// alamat = $('#inp_alamat').val();
	// kota = $('#inp_kota').val();
	
	// if(kodefarm != "" && namafarm != "" && alamat != "" && kota != ""){
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

$('#inp_kota').change(function(){
	checkInput();
});

$('#inp_kodefarm').keyup(function(){
	this.value = this.value.toUpperCase();
});

$('#inp_namafarm').keyup(function(){
	this.value = this.value.toUpperCase();
});

var mFarm = {
	enableJmlFlok : function(){
		var tipefarm = $('input:radio[name=tipefarm]:checked').val();
		var grupfarm = $('input:radio[name=grupfarm]:checked').val();
		var jmlFlok = $('#inp_jmlflok');
		var statusJmlFlok = jmlFlok.prop('disabled');
		console.log(statusJmlFlok);
		if(tipefarm == 'I' && grupfarm == 'BDY'){
			if(statusJmlFlok){
				jmlFlok.prop('disabled',0);
			}
		}
		else{
			if(!statusJmlFlok){
				jmlFlok.prop('disabled',1);
			}
		}
	},
};