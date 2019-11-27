var search = false;
var page_number=0;
var total_page =null;

var form_mode = "";
var selected_kandang = "";

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
	
	kodepengawas = $('#q_kodepengawas').val();
	namapengawas = $('#q_namapengawas').val();
	jeniskelamin = $('#q_jeniskelamin').val();
	status = $('#q_status').val();
		
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/pengawas/get_pagination/",
		data: {
			kodepengawas : kodepengawas,
			namapengawas : namapengawas,
			jeniskelamin : jeniskelamin,
			status : status,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-pengawas").html("");
		
		window.mydata = data;
		
		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;
				
				$.each(record_par_page, function (key, data) {
					
					var v_jeniskelamin = (data.jenis_kelamin == "L") ? "Laki-laki" : "Perempuan";
					var v_status = (data.status_pegawai == "A") ? "Aktif" : "Tidak Aktif";
									
					$("tbody", "#master-pengawas").append('<tr><td>'+
					data.row+'</td><td>'+
					data.kode_pegawai+'</td><td>'+
					data.nama_pegawai+'</td><td>'+
					v_jeniskelamin+'</td><td>'+
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

$('#q_jeniskelamin').change(function(){
	goSearch();
});

$('#q_status').change(function(){
	goSearch();
});

$('.field_input').keyup(function(){
	checkInput();
});

$('#inp_username').keyup(function(){
	$('#inp_password').val(this.value);
});

$('#master-pengawas').on('click','tr',function() {
	selected_pengawas = $(this).find('td:nth-child(2)').text();
});

$('#master-pengawas > tbody').on('dblclick','tr',function() {	
	resetInput();
	selected_pengawas = $(this).find('td:nth-child(2)').text();
	form_mode = "ubah";
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/pengawas/get_pengawas/",
		data: {
			kodepengawas : selected_pengawas
		}
	})
	.done(function(data){
		$('#inp_kodepengawas').val(data.kode_pegawai);
		$('#inp_namapengawas').val(data.nama_pegawai);
		var _list_farm = data.list_farm;
		$('input[name ^= kode_farm]').prop('disabled',true);
		for(var i in _list_farm){
			$('input[name ^= kode_farm][value='+_list_farm[i]+']').prop('checked',true);
		}
		if(data.jenis_kelamin == "L")
			$("#inp_jeniskelaminlaki").prop("checked", true)
		else
			$("#inp_jeniskelaminperempuan").prop("checked", true)
		
		$('#inp_telp').val(data.no_telp);
		$('#inp_gruppegawai').val(data.grup_pegawai);
		$('#inp_username').val(data.username);
		$('#inp_password').val(data.password);
		
		if(data.status_pegawai == 'A')
			$("#inp_status").prop("checked", true);
		else
			$("#inp_status").prop("checked", false);
		
		$('#inp_kodepengawas').attr("disabled", true);
		$('#inp_username').attr("disabled", true);
		$('#inp_password').attr("disabled", false);
			
		$('#btnSimpan').hide();
		$('#btnUbah').show();
		$('#btnUbah').removeClass('disabled');
	
		$('#modal_pengawas').modal("show");
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
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/pengawas/get_next_kode_pengawas/"
	})
	.done(function(data){
		$('#inp_kodepengawas').val(data.kode);
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
	
	
	$('#inp_kodepengawas').attr("disabled", true);
	$('#inp_username').attr("disabled", false);
	$('#inp_password').attr("disabled", true);
	$("#inp_status").prop("checked", true);
	
	$('#btnSimpan').show();
	$('#btnUbah').hide();
	
	$('#modal_pengawas').modal("show");
});

$("#btnBatal").click(function(){
	$('#modal_pengawas').modal("hide");
	resetInput();
});

$("#btnSimpan").click(function(){
	kodepengawas = $('#inp_kodepengawas').val();
	namapengawas = $('#inp_namapengawas').val();
	jeniskelamin = $('input:radio[name=jeniskelamin]:checked').val();
	telp = $('#inp_telp').val();
	gruppegawai = $('#inp_gruppegawai').val();
	username = $('#inp_username').val();
	password = $('#inp_password').val();
	var _list_farm = [];
	var _farm_terpilih = $('input:checkbox[name ^=kode_farm]:checked');
	if(_farm_terpilih.length){
		_farm_terpilih.each(function(){
			_list_farm.push($(this).val());
		});
	}else{
		bootbox.alert("Minimal harus memilih minimal satu farm untuk pegawai tersebut");		
		return false;
	}
	/* pastikan sudah memilih minimal satu farm */
	if(empty(namapengawas) || empty(username) || empty(password)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		
		return false;
	}
	
	if($("#inp_status").is(':checked'))
		status = 'A';
	else
		status = 'N';
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/pengawas/check_username/",
		data: {
			username : username
		}
	})
	.done(function(data){
		if(data.result == '1'){
			bootbox.alert("Username " + username + " sudah terdaftar");
		}else{
			bootbox.dialog({
				message: "Apakah Anda yakin akan Menyimpan data Pegawai?",
				title: "",
				buttons: {
					main: {
						label: "Ya",
						className: "btn-primary",
						callback: function() {
							$.ajax({
								type:'POST',
								dataType: 'json',
								url : "master/pengawas/add_pengawas/",
								data: {
									kodepengawas : kodepengawas,
									namapengawas : namapengawas,
									jeniskelamin : jeniskelamin,
									telp : telp,
									gruppegawai : gruppegawai,
									username : username,
									password : password,
									status : status,
									list_farm : _list_farm
								}
							})
							.done(function(data){
								if(data.result == "success"){
									toastr.success("Penyimpanan Pegawai dengan username " + username + " berhasil dilakukan",'Informasi');
									
									$('#modal_pengawas').modal("hide");
									resetInput();
									
									getReport(page_number);
								}else{
									toastr.warning("Penyimpanan Pegawai dengan username " + username + " gagal dilakukan",'Peringatan');
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
	kodepengawas = $('#inp_kodepengawas').val();
	namapengawas = $('#inp_namapengawas').val();
	jeniskelamin = $('input:radio[name=jeniskelamin]:checked').val();
	telp = $('#inp_telp').val();
	gruppegawai = $('#inp_gruppegawai').val();
	username = $('#inp_username').val();
	password = $('#inp_password').val();
	var _list_farm = [];
	var _farm_terpilih = $('input:checkbox[name ^=kode_farm]:checked');
	if(_farm_terpilih.length){
		_farm_terpilih.each(function(){
			_list_farm.push($(this).val());
		});
	}else{
		bootbox.alert("Minimal harus memilih minimal satu farm untuk pegawai tersebut");		
		return false;
	}
	if(empty(namapengawas) || empty(username) || empty(password)){
		bootbox.alert("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		
		return false;
	}
	
	if($("#inp_status").is(':checked'))
		status = 'A';
	else
		status = 'N';
	
	bootbox.dialog({
		message: "Apakah Anda yakin akan Mengubah data Pegawai?",
		title: "",
		buttons: {
			main: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "master/pengawas/update_pengawas/",
						data: {
							kodepengawas : kodepengawas,
							namapengawas : namapengawas,
							jeniskelamin : jeniskelamin,
							telp : telp,
							gruppegawai : gruppegawai,
							username : username,
							password : password,
							status : status,
							list_farm : _list_farm
						}
					})
					.done(function(data){
						if(data.result == "success"){
							toastr.success("Perubahan data Pegawai dengan Username " + username + " berhasil dilakukan",'Informasi');
							
							$('#modal_pengawas').modal("hide");
							resetInput();
							
							getReport(page_number);
						}else{
							toastr.warning("Perubahan data Pegawai dengan Username " + username + " gagal dilakukan",'Peringatan');
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

$('#inp_namapengawas').keyup(function(){
	this.value = this.value.toUpperCase();
});

/*
* FUNCTION
*/

function resetInput(){
	$('#inp_kodepengawas').val('');
	$('#inp_namapengawas').val('');
	$("#inp_jeniskelaminlaki").prop("checked", true);
	$('#inp_telp').val('');
	$('#inp_gruppegawai :nth-child(1)').prop('selected', true);
	$('#inp_username').val('');
	$('#inp_password').val('');
	$('#inp_status').prop('checked', true);	
	$('input[name ^= kode_farm]').prop('checked', false);	
	$('input[name ^= kode_farm]').prop('disabled',false);
}

function goSearch(){
	page_number = 0;
	search = true;
	getReport(page_number);	
}

function cekNumerik(field){	
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	}
}

function checkInput(){
	
	// kodepengawas = $('#inp_kodepengawas').val();
	// namapengawas = $('#inp_namapengawas').val();
	// username = $('#inp_username').val();
	// password = $('#inp_password').val();
	
	// if(kodepengawas != "" && namapengawas != "" && username != "" && password != ""){
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