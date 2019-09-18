var search = false;
var page_number=0;
var total_page =null;
var flag_jenisbarang = "pakan";
var flag_tipepakan = "eksternal";

var form_mode = "";
var selected_barang = "";

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
	
	jenisbarang = $('#q_jenisbarang').val();
	tipebarang = $('#q_tipebarang').val();
	kodebarang = $('#q_kodebarang').val();
	namabarang = $('#q_namabarang').val();
	bentukbarang = $('#q_bentukbarang').val();
	satuan = $('#q_satuan').val();
	status = $('#q_status').val();
		
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/barang/get_pagination/",
		data: {
			jenisbarang : jenisbarang,
			tipebarang : tipebarang,
			kodebarang : kodebarang,
			namabarang : namabarang,
			bentukbarang : bentukbarang,
			satuan : satuan,
			status : status,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-barang").html("");
		
		window.mydata = data;
		
		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;
				
				$.each(record_par_page, function (key, data) {
					var v_jenisbarang = "";
					var v_tipebarang = "";
					var v_bentukbarang = "";
									
					switch(data.jenis_barang){
						case "PA" :
							v_jenisbarang = "Pakan Ayam";
							break;
						case "PI" :
							v_jenisbarang = "Pakan Ikan";
							break;
						case "O" :
							v_jenisbarang = "Obat/Vitamin";
							break;
						case "V" :
							v_jenisbarang = "Vaksin";
							break;
						case "L" :
							v_jenisbarang = "Lain-lain";
							break;
					}
					
					v_tipebarang = (data.tipe_barang == "I") ? "Internal" : "Eksternal";
					
					switch(data.bentuk_barang){
						case "C" :
							v_bentukbarang = "Crumble";
							break;
						case "T" :
							v_bentukbarang = "Tepung";
							break;
						case "P" :
							v_bentukbarang = "Padat";
							break;
						case "A" :
							v_bentukbarang = "Cair";
							break;
					}
					
					var v_status = (data.status_barang == "A") ? "Aktif" : "Tidak Aktif";
									
					$("tbody", "#master-barang").append('<tr><td>'+
					data.row+'</td><td>'+
					v_jenisbarang+'</td><td>'+
					v_tipebarang+'</td><td>'+
					data.kode_barang+'</td><td>'+
					data.nama_barang+'</td><td>'+
					data.bentuk_barang_konversi+'</td><td>'+
					data.uom+'</td><td>'+
					v_status+'</td></tr>');
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
	$('#q_status').val('A');
	
	getReport(page_number);
	
	$("#next").on("click", function(){
		page_number = (page_number+1);
		getReport(page_number);
	});
	
	$("#previous").on("click", function(){
		page_number = (page_number-1);
		getReport(page_number);
	});
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/barang/get_grupbarang/",
		data: {
		}
	})
	.done(function(data){
		var $input = $('#inp_jenisgrupbarang');
		$input.typeahead({source:data, 
					autoSelect: true}); 
		$input.change(function() {
			var current = $input.typeahead("getActive");
			if (current) {
				// Some item from your model is active!
				if (current.name == $input.val()) {
					$('#inp_jenisgrupbarang_val').val(current.id);
					// This means the exact match is found. Use toLowerCase() if you want case insensitive match.
				} else {
					$('#inp_jenisgrupbarang_val').val('');
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
});

$('.q_search').keyup(function(){
	this.value = this.value.toUpperCase();
	goSearch();
});

$('#q_jenisbarang').change(function(){
	goSearch();
});

$('#q_tipebarang').change(function(){
	goSearch();
});

$('#q_bentukbarang').change(function(){
	goSearch();
});

$('#q_satuan').change(function(){
	goSearch();
});

$('#q_status').change(function(){
	goSearch();
});

$('.field_input').keyup(function(){
	checkInput();
});

$('#inp_jenisbarang').change(function(){
	jenisbarang = $('#inp_jenisbarang').val();
	
	if(jenisbarang == "PA" || jenisbarang == "PI"){
		flag_jenisbarang = "pakan";
		
		$('#inp_tipepakan_eksternal').prop("disabled", true);
		$('#inp_tipepakan_internal').prop("disabled", true);
		$('#inp_jeniskelaminternak_betina').prop("disabled", false);
		$('#inp_jeniskelaminternak_jantan').prop("disabled", false);
		$('#inp_usiawalternak').prop("disabled", false);
		$('#inp_usiakhirternak').prop("disabled", false);
		
		$('#inp_jeniskelaminternak_betina').prop('checked', true);
		$('#inp_jeniskelaminternak_jantan').prop('checked', true);
		$('#inp_usiawalternak').val('');
		$('#inp_usiakhirternak').val('');
	}
	else{
		flag_jenisbarang = "non-pakan";
		
		$('#inp_tipepakan_eksternal').prop("disabled", true);
		$('#inp_tipepakan_internal').prop("disabled", true);
		$('#inp_jeniskelaminternak_betina').prop("disabled", true);
		$('#inp_jeniskelaminternak_jantan').prop("disabled", true);
		$('#inp_usiawalternak').prop("disabled", true);
		$('#inp_usiakhirternak').prop("disabled", true);
		
		$('#inp_jeniskelaminternak_betina').prop('checked', false);
		$('#inp_jeniskelaminternak_jantan').prop('checked', false);
		$('#inp_usiawalternak').val('');
		$('#inp_usiakhirternak').val('');
	}
		
	checkInput();
});

$('#inp_jeniskelaminternak_jantan').change(function() {
    checkInput();
});

$('#inp_jeniskelaminternak_betina').change(function() {
	checkInput();
});

$('#master-barang').on('click','tr',function() {
	selected_barang = $(this).find('td:nth-child(4)').text();
});

$('#master-barang > tbody').on('dblclick','tr',function() {	
	selected_barang = $(this).find('td:nth-child(4)').text();
	form_mode = "ubah";
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/barang/get_barang/",
		data: {
			kodebarang : selected_barang
		}
	})
	.done(function(data){
		$('#inp_jenisbarang').val(data.jenis_barang);
		if(data.tipe_barang == "I")
			$("#inp_tipepakan_internal").prop("checked", true);
		else
			$("#inp_tipepakan_eksternal").prop("checked", true);
		
		$('#inp_kodebarang').val(data.kode_barang);
		$('#inp_jenisgrupbarang').val(data.nama_grup_barang);
		$('#inp_jenisgrupbarang_val').val(data.grup_barang);
		$('#inp_namabarang').val(data.nama_barang);
		$('#inp_namaaliasbarang').val(data.alias);
		$('#inp_bentukbarang').val(data.bentuk_barang);
		$('#inp_satuanbarang').val(data.uom);
		if(!empty(data.pakan_betina))
			$('#inp_jeniskelaminternak_betina').prop('checked', true);
		if(!empty(data.pakan_jantan))
			$('#inp_jeniskelaminternak_jantan').prop('checked', true);
		
		$('#inp_usiawalternak').val(data.usia_awal_ternak);
		$('#inp_usiakhirternak').val(data.usia_akhir_ternak);
		if(data.status_barang == 'A')
			$("#inp_status").prop("checked", true);
		else
			$("#inp_status").prop("checked", false);
		
		
		$('#inp_jenisbarang').prop("disabled", true);
		$("#inp_tipepakan_internal").prop("disabled", true);
		$("#inp_tipepakan_eksternal").prop("disabled", true);
		$('#inp_kodebarang').prop("disabled", true);
		$('#inp_namabarang').prop("disabled", true);
		$('#inp_namaaliasbarang').prop("disabled", true);
		$('#inp_jenisgrupbarang').prop("disabled", true);
		$('#inp_jenisgrupbarang_val').prop("disabled", true);
		$('#inp_bentukbarang').prop("disabled", true);
		$('#inp_satuanbarang').prop("disabled", true);
		$('#inp_jeniskelaminternak_betina').prop("disabled", true);
		$('#inp_jeniskelaminternak_jantan').prop("disabled", true);
		$('#inp_usiawalternak').prop("disabled", true);
		$('#inp_usiakhirternak').prop("disabled", true);
		$("#inp_status").prop("disabled", true);
		
		if(data.tipe_barang == "I"){
			$("#inp_status").prop("disabled", false);
		}
		else{
			$('#inp_jenisbarang').prop("disabled", true);
			if(data.jenis_barang != "PA" || data.jenis_barang != "PI"){
				$("#inp_tipepakan_internal").prop("disabled", true);
				$("#inp_tipepakan_eksternal").prop("disabled", true);
			}
			$('#inp_kodebarang').prop("disabled", true);
		
			$('#inp_namabarang').prop("disabled", false);
			$('#inp_namaaliasbarang').prop("disabled", false);
			$('#inp_jenisgrupbarang').prop("disabled", false);
			$('#inp_jenisgrupbarang_val').prop("disabled", false);
			$('#inp_bentukbarang').prop("disabled", false);
			$('#inp_satuanbarang').prop("disabled", false);
			
			if(data.jenis_barang == "PA" || data.jenis_barang == "PI"){
				$('#inp_jeniskelaminternak_betina').prop("disabled", false);
				$('#inp_jeniskelaminternak_jantan').prop("disabled", false);
				$('#inp_usiawalternak').val(data.usia_awal_ternak);
				$('#inp_usiakhirternak').val(data.usia_akhir_ternak);
				$('#inp_usiawalternak').prop("disabled", false);
				$('#inp_usiakhirternak').prop("disabled", false);
			}
			else{
				$('#inp_jeniskelaminternak_betina').prop('checked', false);
				$('#inp_jeniskelaminternak_jantan').prop('checked', false);
				$('#inp_usiawalternak').val('');
				$('#inp_usiakhirternak').val('');
			}
			$("#inp_status").prop("disabled", false);
		}
		
		$('#btnSimpan').hide();
		$('#btnUbah').show();
		$('#btnUbah').removeClass('disabled');
	
		$('#modal_barang').modal("show");
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
	
	$('#btnSimpan').show();
	$('#btnUbah').hide();
	
	$('#inp_tipepakan_eksternal').prop("disabled", true);
	$('#inp_tipepakan_internal').prop("disabled", true);
		
	$('#modal_barang').modal("show");
});

$("#btnBatal").click(function(){
	$('#modal_barang').modal("hide");
	resetInput();
});

$("#btnSimpan").click(function(){
	jenisbarang = $('#inp_jenisbarang').val();
	tipepakan = $('input:radio[name=tipepakan]:checked').val();
	kodebarang = $('#inp_kodebarang').val();
	jenisgrupbarang = $('#inp_jenisgrupbarang').val();
	jenisgrupbarangval = $('#inp_jenisgrupbarang_val').val();
	namabarang = $('#inp_namabarang').val();
	namaaliasbarang = $('#inp_namaaliasbarang').val();
	bentukbarang = $('#inp_bentukbarang').val();
	satuanbarang = $('#inp_satuanbarang').val();
	jeniskelaminternakbetina = "";
	jeniskelaminternakjantan = "";
	
	if($("#inp_jeniskelaminternak_betina").is(':checked'))
		jeniskelaminternakbetina = 'B';
	if($("#inp_jeniskelaminternak_jantan").is(':checked'))
		jeniskelaminternakjantan = 'J';
	
	usiaawal = $('#inp_usiawalternak').val();
	usiaakhir = $('#inp_usiakhirternak').val();
	status = "";
	
	if($("#inp_status").is(':checked'))
		status = 'A';
	else
		status = 'N';
	
	if(flag_jenisbarang == "pakan"){
		if(empty(kodebarang) || empty(jenisgrupbarang) || empty(namabarang)  || empty(namaaliasbarang) || empty(bentukbarang) || empty(satuanbarang) || (empty(jeniskelaminternakbetina) && empty(jeniskelaminternakjantan)) || empty(usiaawal) || empty(usiaakhir)){
			bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
			lanjut = false;
			
			return false;
		}
		
		if(parseInt(usiaawal) > parseInt(usiaakhir)){
			bootbox.alert(" Inputan usia awal ternak tidak boleh lebih besar usia akhir ternak.");
			lanjut = false;
			
			return false;
		}
	}
	else{
		if(empty(kodebarang) || empty(jenisgrupbarang) || empty(namabarang) || empty(namaaliasbarang) || empty(bentukbarang) || empty(satuanbarang)){
			bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
			lanjut = false;
			
			return false;
		}
	}
	
	// if(empty(namabarang)){
		// bootbox.alert("Nama barang harus diisi");
		// lanjut = false;
		// return false;
	// }
	
	// if(empty(namaaliasbarang)){
		// bootbox.alert("Nama alias barang harus diisi");
		// lanjut = false;
		// return false;
	// }
		
	// if((jenisbarang == "PA" || jenisbarang == "PI") && empty(jeniskelaminternakbetina) && empty(jeniskelaminternakjantan)){
		// bootbox.alert("Jenis kelamin harus ditentukan");
		// lanjut = false;
		// return false;
	// }
	
	// if((jenisbarang == "PA" || jenisbarang == "PI") && (empty(usiaawal) || empty(usiaakhir))){
		// bootbox.alert("Usia Ternak harus ditentukan");
		// lanjut = false;
		// return false;
	// }
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/barang/cek_kodebarang/",
		data: {
			kodebarang : kodebarang
		}
	})
	.done(function(data){
		if(data.jumlah > 0){
			bootbox.alert("Kode Barang " + kodebarang + " sudah terdaftar");
		}else{
			if(tipepakan == 'E' && jenisgrupbarangval == ''){
				bootbox.dialog({
					message: "Apakah Anda yakin akan Menyimpan data Barang ini?",
					title: "",
					buttons: {
						main: {
							label: "Ya",
							className: "btn-primary",
							callback: function() {
								bootbox.dialog({
									message: "Jenis/Group tidak terdaftar. Anda yakin melanjutkan?",
									title: "",
									buttons: {
										main: {
											label: "Ya",
											className: "btn-primary",
											callback: function() {
												$.ajax({
													type:'POST',
													dataType: 'json',
													url : "master/barang/add_master_grupbarang/",
													data: {
														deskripsi : jenisgrupbarang
													}
												})
												.done(function(data){
													jenisgrupbarangval = data.id;		
													$.ajax({
														type:'POST',
														dataType: 'json',
														url : "master/barang/add_barang/",
														data: {
															jenisbarang : jenisbarang,
															tipepakan : tipepakan,
															kodebarang : kodebarang,
															jenisgrupbarang : jenisgrupbarangval,
															namabarang : namabarang,
															namaaliasbarang : namaaliasbarang,
															bentukbarang : bentukbarang,
															satuanbarang : satuanbarang,
															jeniskelaminternakbetina : jeniskelaminternakbetina,
															jeniskelaminternakjantan : jeniskelaminternakjantan,
															usiaawal : usiaawal,
															usiaakhir : usiaakhir,
															status : status,
															flag_tipepakan : flag_jenisbarang
														}
													})
													.done(function(data){
														if(data.result == "success"){
															toastr.success("Penyimpanan data Barang dengan kode " + kodebarang + " berhasil dilakukan",'Informasi');
															
															$('#modal_barang').modal("hide");
															resetInput();
															
															getReport(page_number);
														}else{
															toastr.warning(data.msg,'Peringatan');
														}
													})
													.fail(function(reason){
														console.info(reason);
													})
													.then(function(data){
													});
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
			else if((tipepakan == 'E' && !empty(jenisgrupbarangval)) || (tipepakan == 'I' && !empty(jenisgrupbarangval))){
				bootbox.dialog({
						message: "Apakah Anda yakin akan Menyimpan data Barang ini?",
						title: "",
						buttons: {
							main: {
								label: "Ya",
								className: "btn-primary",
								callback: function() {
									$.ajax({
										type:'POST',
										dataType: 'json',
										url : "master/barang/add_barang/",
										data: {
											jenisbarang : jenisbarang,
											tipepakan : tipepakan,
											kodebarang : kodebarang,
											jenisgrupbarang : jenisgrupbarangval,
											namabarang : namabarang,
											namaaliasbarang : namaaliasbarang,
											bentukbarang : bentukbarang,
											satuanbarang : satuanbarang,
											jeniskelaminternakbetina : jeniskelaminternakbetina,
											jeniskelaminternakjantan : jeniskelaminternakjantan,
											usiaawal : usiaawal,
											usiaakhir : usiaakhir,
											status : status,
											flag_tipepakan : flag_jenisbarang
										}
									})
									.done(function(data){
										if(data.result == "success"){
											toastr.success("Penyimpanan data Barang dengan kode " + kodebarang + " berhasil dilakukan",'Informasi');
											
											$('#modal_barang').modal("hide");
											resetInput();
											
											getReport(page_number);
										}else{
											toastr.warning(data.msg,'Peringatan');
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
			else if(tipepakan == 'I' && jenisgrupbarangval == ''){
				bootbox.alert("Jenis/Group tidak terdaftar");
				return false;
			}else{
				
			}
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
});

$("#btnUbah").click(function(){
	jenisbarang = $('#inp_jenisbarang').val();
	tipepakan = $('input:radio[name=tipepakan]:checked').val();
	kodebarang = $('#inp_kodebarang').val();
	jenisgrupbarang = $('#inp_jenisgrupbarang').val();
	jenisgrupbarangval = $('#inp_jenisgrupbarang_val').val();
	namabarang = $('#inp_namabarang').val();
	namaaliasbarang = $('#inp_namaaliasbarang').val();
	bentukbarang = $('#inp_bentukbarang').val();
	satuanbarang = $('#inp_satuanbarang').val();
	jeniskelaminternakbetina = "";
	jeniskelaminternakjantan = "";
	
	if($("#inp_jeniskelaminternak_betina").is(':checked'))
		jeniskelaminternakbetina = 'B';
	if($("#inp_jeniskelaminternak_jantan").is(':checked'))
		jeniskelaminternakjantan = 'J';
	
	usiaawal = $('#inp_usiawalternak').val();
	usiaakhir = $('#inp_usiakhirternak').val();
	status = "";
	
	if($("#inp_status").is(':checked'))
		status = 'A';
	else
		status = 'N';
	
	if(tipepakan == "E"){	
		if(flag_jenisbarang == "pakan"){
			if(empty(kodebarang) || empty(jenisgrupbarang) || empty(namabarang)  || empty(namaaliasbarang) || empty(bentukbarang) || empty(satuanbarang) || (empty(jeniskelaminternakbetina) && empty(jeniskelaminternakjantan)) || empty(usiaawal) || empty(usiaakhir)){
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				lanjut = false;
				
				return false;
			}
			
			if(parseInt(usiaawal) > parseInt(usiaakhir)){
				bootbox.alert(" Inputan usia awal ternak tidak boleh lebih besar usia akhir ternak.");
				lanjut = false;
				
				return false;
			}
		}
		else{
			if(empty(kodebarang) || empty(jenisgrupbarang) || empty(namabarang) || empty(namaaliasbarang) || empty(bentukbarang) || empty(satuanbarang)){
				bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
				lanjut = false;
				
				return false;
			}
		}
	}
	
	// if(empty(namabarang)){
		// bootbox.alert("Nama barang harus diisi");
		// lanjut = false;
		// return false;
	// }
	
	// if(empty(namaaliasbarang)){
		// bootbox.alert("Nama alias barang harus diisi");
		// lanjut = false;
		// return false;
	// }
		
	// if((jenisbarang == "PA" || jenisbarang == "PI") && empty(jeniskelaminternakbetina) && empty(jeniskelaminternakjantan)){
		// bootbox.alert("Jenis kelamin harus ditentukan");
		// lanjut = false;
		// return false;
	// }
	
	// if((jenisbarang == "PA" || jenisbarang == "PI") && (empty(usiaawal) || empty(usiaakhir))){
		// bootbox.alert("Usia Ternak harus ditentukan");
		// lanjut = false;
		// return false;
	// }
	
	if(tipepakan == 'E' && jenisgrupbarangval == ''){
		bootbox.dialog({
			message: "Apakah Anda yakin akan Mengubah data Barang ini?",
			title: "",
			buttons: {
				main: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						bootbox.dialog({
							message: "Jenis/Group tidak terdaftar. Anda yakin melanjutkan?",
							title: "",
							buttons: {
								main: {
									label: "Ya",
									className: "btn-primary",
									callback: function() {
										$.ajax({
											type:'POST',
											dataType: 'json',
											url : "master/barang/add_master_grupbarang/",
											data: {
												deskripsi : jenisgrupbarang
											}
										})
										.done(function(data){
											jenisgrupbarangval = data.id;		
											$.ajax({
												type:'POST',
												dataType: 'json',
												url : "master/barang/update_barang/",
												data: {
													jenisbarang : jenisbarang,
													tipepakan : tipepakan,
													kodebarang : kodebarang,
													jenisgrupbarang : jenisgrupbarangval,
													namabarang : namabarang,
													namaaliasbarang : namaaliasbarang,
													bentukbarang : bentukbarang,
													satuanbarang : satuanbarang,
													jeniskelaminternakbetina : jeniskelaminternakbetina,
													jeniskelaminternakjantan : jeniskelaminternakjantan,
													usiaawal : usiaawal,
													usiaakhir : usiaakhir,
													status : status,
													flag_tipepakan : flag_jenisbarang
												}
											})
											.done(function(data){
												if(data.result == "success"){
													toastr.success("Perubahan data Barang dengan kode " + kodebarang + " berhasil dilakukan",'Informasi');
													
													$('#modal_barang').modal("hide");
													resetInput();
													
													getReport(page_number);
												}else{
													toastr.warning("Perubahan data Barang dengan kode " + kodebarang + " gagal dilakukan",'Peringatan');
												}
											})
											.fail(function(reason){
												console.info(reason);
											})
											.then(function(data){
											});
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
				},
				cancel: {
					label: "Tidak",
					className: "btn-default",
					callback: function() {
					}
				}
			}
		});
	}else if((tipepakan == 'E' && !empty(jenisgrupbarangval)) || (tipepakan == 'I' && !empty(jenisgrupbarangval))){
		bootbox.dialog({
			message: "Apakah Anda yakin akan Mengubah data Barang ini?",
			title: "",
			buttons: {
				main: {
					label: "Ya",
					className: "btn-primary",
					callback: function() {
						$.ajax({
							type:'POST',
							dataType: 'json',
							url : "master/barang/update_barang/",
							data: {
								jenisbarang : jenisbarang,
								tipepakan : tipepakan,
								kodebarang : kodebarang,
								jenisgrupbarang : jenisgrupbarangval,
								namabarang : namabarang,
								namaaliasbarang : namaaliasbarang,
								bentukbarang : bentukbarang,
								satuanbarang : satuanbarang,
								jeniskelaminternakbetina : jeniskelaminternakbetina,
								jeniskelaminternakjantan : jeniskelaminternakjantan,
								usiaawal : usiaawal,
								usiaakhir : usiaakhir,
								status : status,
								flag_tipepakan : flag_jenisbarang
							}
						})
						.done(function(data){
							if(data.result == "success"){
								toastr.success("Perubahan data Barang dengan kode " + kodebarang + " berhasil dilakukan",'Informasi');
								
								$('#modal_barang').modal("hide");
								resetInput();
								
								getReport(page_number);
							}else{
								toastr.warning("Perubahan data Barang dengan kode " + kodebarang + " gagal dilakukan",'Peringatan');
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
	}else if(tipepakan == 'I' && jenisgrupbarangval == ''){
		bootbox.alert("Jenis/Group tidak terdaftar");
		return false;
	}
});

/*
* FUNCTION
*/

function resetInput(){
	$('#inp_jenisbarang :nth-child(1)').prop('selected', true);
	
	$('#inp_jenisgrupbarang').prop("disabled", false);
	$('#inp_namabarang').prop("disabled", false);
	$('#inp_namaaliasbarang').prop("disabled", false);
	$('#inp_bentukbarang').prop("disabled", false);
	$('#inp_satuanbarang').prop("disabled", false);
		
	$('#inp_jenisbarang').prop("disabled", false);
	$('#inp_tipepakan_eksternal').prop("disabled", false);
	$('#inp_tipepakan_internal').prop("disabled", false);
	$('#inp_kodebarang').prop("disabled", false);
	
	$('#inp_jeniskelaminternak_betina').prop("disabled", false);
	$('#inp_jeniskelaminternak_jantan').prop("disabled", false);
	$('#inp_usiawalternak').prop("disabled", false);
	$('#inp_usiakhirternak').prop("disabled", false);
		
	$("#inp_tipepakan_eksternal").prop("checked", true);
	$('#inp_kodebarang').val('');
	$('#inp_jenisgrupbarang').val('');
	$('#inp_jenisgrupbarang_val').val('');
	$('#inp_namabarang').val('');
	$('#inp_namaaliasbarang').val('');
	$('#inp_bentukbarang :nth-child(1)').prop('selected', true);
	$('#inp_satuanbarang :nth-child(1)').prop('selected', true);
	$('#inp_jeniskelaminternak_betina').prop('checked', true);
	$('#inp_jeniskelaminternak_jantan').prop('checked', true);
	$('#inp_usiawalternak').val('');
	$('#inp_usiakhirternak').val('');
	$('#inp_status').prop('checked', true);	
	
	//$('#btnSimpan').addClass("disabled");
}

function goSearch(){
	search = true;
	page_number = 0;
	getReport(page_number);	
}

function checkInput(){
	// jenisbarang = $('#inp_jenisbarang').val();
	// tipepakan = $('input:radio[name=tipepakan]:checked').val();
	// kodebarang = $('#inp_kodebarang').val();
	// jenisgrupbarang = $('#inp_jenisgrupbarang').val();
	// jenisgrupbarangval = $('#inp_jenisgrupbarang_val').val();
	// namabarang = $('#inp_namabarang').val();
	// namaaliasbarang = $('#inp_namaaliasbarang').val();
	// bentukbarang = $('#inp_bentukbarang').val();
	// satuanbarang = $('#inp_satuanbarang').val();
	// jeniskelaminternakbetina = "";
	// jeniskelaminternakjantan = "";
	
	// if($("#inp_jeniskelaminternak_betina").is(':checked'))
		// jeniskelaminternakbetina = 'B';
	// if($("#inp_jeniskelaminternak_jantan").is(':checked'))
		// jeniskelaminternakjantan = 'J';
	
	// usiaawal = $('#inp_usiawalternak').val();
	// usiaakhir = $('#inp_usiakhirternak').val();
	// status = "";
	
	// if($("#inp_status").is(':checked'))
		// status = 'A';
	// else
		// status = 'N';
	
	// if(flag_jenisbarang == "pakan"){
		// if(!empty(kodebarang) && !empty(jenisgrupbarang) && !empty(namabarang) && !empty(namaaliasbarang) && !empty(bentukbarang) && !empty(satuanbarang) && (!empty(jeniskelaminternakbetina) || !empty(jeniskelaminternakjantan)) && !empty(usiaawal) && !empty(usiaakhir)){
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
	// }
	// else{
		// if(!empty(kodebarang) && !empty(jenisgrupbarang) && !empty(namabarang) && !empty(namaaliasbarang) && !empty(bentukbarang) && !empty(satuanbarang)){
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
	// }
}

function cekNumerik(field){	
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	}
}