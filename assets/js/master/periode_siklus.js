var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_periode_siklus = "";

function kontrol_periode(e) {
	var pola = "^";
	pola += "[0-9]*";
	pola += "$";
	rx = new RegExp(pola);

	if (!e.value.match(rx)) {
		if (e.lastMatched) {
			e.value = e.lastMatched;
		} else {
			e.value = "";
		}
	} else {
		e.lastMatched = e.value;
	}
}

function check_periode_siklus(callback) {
	$.ajax({
		type : 'POST',
		dataType : 'json',
		url : "master/periode_siklus/check_periode_siklus/",
		data : {
			periodesiklus : periodesiklus,
			kodefarm : kodefarm
		}
	}).done(function(data) {
		callback(data);
	}).fail(function(reason) {
		console.info(reason);
	}).then(function(data) {
	});
}

function getReport(page_number) {
	if (page_number == 0) {
		$("#previous").prop('disabled', true);
	} else {
		$("#previous").prop('disabled', false);
	}

	if (page_number == (total_page - 1)) {
		$("#next").prop('disabled', true);
	} else {
		$("#next").prop('disabled', false);
	}

	$("#page_number").text(page_number + 1);

	periodesiklus = $('#q_periodesiklus').val();
	namafarm = $('#q_namafarm').val();
	namastrain = $('#q_namastrain').val();
	status = $('#q_status').val();

	$.ajax({
		type : 'POST',
		dataType : 'json',
		url : "master/periode_siklus/get_pagination/",
		data : {
			periodesiklus : periodesiklus,
			namafarm : namafarm,
			namastrain : namastrain,
			status : status,
			page_number : page_number,
			search : search
		}
	}).done(function(data) {
		$("tbody", "#master-periode-siklus").html("");

		window.mydata = data;

		if (!empty(mydata.length)) {
			if (mydata.length > 0) {
				total_page = mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;

				$.each(record_par_page, function(key, data) {

					$("tbody", "#master-periode-siklus").append('<tr data-id="' + data.kode_siklus + '"><td>' + data.periode_siklus + '</td><td>' + data.nama_farm + '</td><td>' + data.nama_strain + '</td><td>' + data.status_periode_siklus + '</td></tr>');

				});
			}
			if (total_page == 1 || total_page == 0) {
				$("#next").prop('disabled', true);
			}
		} else {
			$("#page_number").text('0');
			$("#total_page").text('0');
			$("#next").prop('disabled', true);
		}

	}).fail(function(reason) {
		console.info(reason);
	}).then(function(data) {
	});
}


$(document).ready(function() {
	getReport(page_number);

	$("#next").on("click", function() {
		page_number = (page_number + 1);
		getReport(page_number);
	});

	$("#previous").on("click", function() {
		page_number = (page_number - 1);
		getReport(page_number);
	});
});

$('.q_search').keyup(function() {
	this.value = this.value.toUpperCase();
	goSearch();
});

$('#q_status').change(function() {
	goSearch();
});

$('.field_input').keyup(function() {
	checkInput();
});

$('#master-periode-siklus').on('click', 'tr', function() {
	selected_periode_siklus = $(this).find('td:nth-child(1)').text();
});

$('#master-periode-siklus > tbody').on('dblclick', 'tr', function() {
	selected_periode_siklus = $(this).attr('data-id');
	form_mode = "ubah";
	$.ajax({
		type : 'POST',
		dataType : 'json',
		url : "master/periode_siklus/get_periode_siklus/",
		data : {
			kodeperiodesiklus : selected_periode_siklus,
		}
	}).done(function(data) {
		var periodesiklus = data.periode_siklus;
		periodesiklus = periodesiklus.split('-');
		$('#kode_siklus').val(data.kode_siklus);
		$('#inp_periode_siklus').val(periodesiklus[0]);
		$('#inp_periode_siklus2').val(data.periode_siklus);
		$('#inp_periode_siklus3').val(data.periode_siklus);
		$('#inp_periode_siklus').addClass('hide');
		$('#inp_periode_siklus3').removeClass('hide');
		$('#inp_nama_farm').val(data.kode_farm);
		$('#inp_nama_strain').val(data.kode_strain);
		$('#inp_nama_farm2').val(data.nama_farm+' - '+data.kode_farm);
		$('#inp_nama_strain2').val(data.nama_strain+' - '+data.kode_strain);
		$('#inp_msg').html(data.msg);
		$('#inp_nama_farm').addClass('hide');
		$('#inp_nama_farm2').removeClass('hide');
		$('#inp_nama_strain').addClass('hide');
		$('#inp_nama_strain2').removeClass('hide');

		$('#inp_periode_siklus').attr("disabled", true);
		$('#inp_nama_farm').attr("disabled", true);
		$('#inp_nama_strain').attr("disabled", true);
		if (data.status_siklus == 'C' || data.status_siklus == 'P') {
			$('#inp_status_priode').attr("disabled", true);
		}else {
			$('#inp_status_priode').attr("disabled", false);
		}
		if (data.status_periode_siklus == 'A')
			$("#inp_status_priode").prop("checked", true);
		else
			$("#inp_status_priode").prop("checked", false);

		$('#btnSimpan').hide();
		$('#btnUbah').show();
		//$('#btnUbah').removeClass('disabled');

		$('#modal_periode_siklus').modal("show");
	}).fail(function(reason) {
		console.info(reason);
	}).then(function(data) {
	});
});

$("#btnTambah").click(function() {
	resetInput();
	form_mode = "tambah";

	$('#inp_periode_siklus3').addClass('hide');
	$('#inp_periode_siklus').removeClass('hide');
	$('#inp_periode_siklus').removeAttr("disabled");
	$('#inp_nama_farm').removeClass('hide');
	$('#inp_nama_farm2').addClass('hide');
	$('#inp_nama_strain').removeClass('hide');
	$('#inp_nama_strain2').addClass('hide');
	$('#inp_nama_farm').removeAttr("disabled");
	$('#inp_nama_strain').removeAttr("disabled");
	$('#btnSimpan').show();
	$('#btnUbah').hide();

	$('#modal_periode_siklus').modal("show");
});

$("#btnBatal").click(function() {
	$('#modal_periode_siklus').modal("hide");
	resetInput();
});

$("#btnSimpan").click(function() {
	periodesiklus = $('#inp_periode_siklus').val();
	kodefarm = $('#inp_nama_farm').val();
	kodestrain = $('#inp_nama_strain').val();

	if ($("#inp_status_priode").is(':checked'))
		status = 'A';
	else
		status = 'N';

	if (periodesiklus && kodefarm && kodestrain) {
		check_periode_siklus(function(d){
			console.log(d);
			console.log(d.periode_siklus);
			var data = (d.result == 1) ? "Periode Siklus untuk tahun "+periodesiklus+", sudah ada yaitu "+d.periode_siklus+", apakah anda membuat periode siklus "+d.new_periode_siklus+"?" : "Apakah Anda yakin akan Menyimpan data Periode Siklus ini?";
			var new_periode_siklus = (d.result == 1) ? d.new_periode_siklus : d.periode_siklus ;
			var box = bootbox.dialog({
				message : data,
				buttons : {
					danger : {
						label : "Tidak",
						className : "btn-default",
						callback : function() {
							return true;
						}
					},
					success : {
						label : "Ya",
						className : "btn-primary",
						callback : function() {
							$.ajax({
								type : 'POST',
								dataType : 'json',
								url : "master/periode_siklus/add_periode_siklus/",
								data : {
									periodesiklus : new_periode_siklus,
									kodefarm : kodefarm,
									kodestrain : kodestrain,
									status : status
								}
							}).done(function(data) {
								if (data.result == "success") {
									notificationBox("Penyimpanan Periode Siklus dengan Periode Siklus "+new_periode_siklus+" berhasil dilakukan.");
									$('#modal_periode_siklus').modal("hide");
									resetInput();

									getReport(page_number);
								} else {
									//if (data.check == "failed")
									//	notificationBox("Periode Siklus " + periodesiklus + " untuk Kode Farm " + kodefarm + " dengan Strain " + kodestrain + " sudah terdaftar.");
									//else
										notificationBox("Penyimpanan Periode Siklus dengan Periode Siklus "+new_periode_siklus+" gagal dilakukan");
								}
							}).fail(function(reason) {
								console.info(reason);
							}).then(function(data) {
							});
						}
					}
				}
			});
		});
	} else {
		notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
	}
});

$("#btnUbah").click(function() {
	periodesiklus = $('#inp_periode_siklus2').val();
	kodefarm   = $('#inp_nama_farm').val();
	kodestrain = $('#inp_nama_strain').val();
	nama_farm  = $('#inp_nama_farm2').val();
	periode    = $('#inp_periode_siklus3').val();

	var kode_siklus = $('#kode_siklus').val();

	if ($("#inp_status_priode").is(':checked')){
		status = 'A';
	}else{
		status = 'N';
	}
	$.post("master/periode_siklus/cek_aktivasi/",{kode_farm : kodefarm,nama_farm : nama_farm,periode : periode},function(result){
		if (result.success == false) {
			notificationBox(result.msg);
			return false;
		}else {
			var data = "Apakah Anda yakin akan Mengubah data Periode Siklus ini?";
			var box = bootbox.dialog({
				message : data,
				buttons : {
					danger : {
						label : "Tidak",
						className : "btn-default",
						callback : function() {
							return true;
						}
					},
					success : {
						label : "Ya",
						className : "btn-primary",
						callback : function() {
							$.ajax({
								type : 'POST',
								dataType : 'json',
								url : "master/periode_siklus/update_periode_siklus/",
								data : {
									periodesiklus : periodesiklus,
									kodefarm : kodefarm,
									kodestrain : kodestrain,
									status : status,
									kodesiklus : kode_siklus
								}
							}).done(function(data) {
								if (data.result == "success") {
									notificationBox(data.message);

									$('#modal_periode_siklus').modal("hide");
									resetInput();

									getReport(page_number);
								} else {
									//if (data.check == "failed")
									//	notificationBox("Periode Siklus " + periodesiklus + " untuk Kode farm " + kodefarm + " dengan Strain " + kodestrain + " sudah terdaftar.");
									//else
									notificationBox("Penyimpanan perubahan data Periode Siklus dengan kode " + periodesiklus + " gagal dilakukan <br />"+ data.message);
								}
							}).fail(function(reason) {
								console.info(reason);
							}).then(function(data) {
							});
						}
					}
				}
			});
		}
	},'json');
});

/*
 * FUNCTION
 */

function resetInput() {
	$('#inp_periode_siklus :nth-child(1)').prop('selected', true);
	$('#inp_nama_farm :nth-child(1)').prop('selected', true);
	$('#inp_nama_strain :nth-child(1)').prop('selected', true);
	$('#inp_status_priode').prop('checked', true);
}

function goSearch() {
	page_number = 0;
	search = true;
	getReport(page_number);
}

function checkInput() {

	periodesiklus = $('#inp_periode_siklus').val();
	kodefarm = $('#inp_nama_farm').val();
	kodestrain = $('#inp_nama_strain').val();
	/*
	 if (periodesiklus != "" && kodefarm != "" && kodestrain != "") {
	 if (form_mode == "tambah")
	 $('#btnSimpan').removeClass("disabled");

	 if (form_mode == "ubah")
	 $('#btnUbah').removeClass("disabled");
	 } else {
	 if (form_mode == "tambah")
	 $('#btnSimpan').addClass("disabled");

	 if (form_mode == "ubah")
	 $('#btnUbah').addClass("disabled");
	 }*/

}

function notificationBox(message) {
	bootbox.dialog({
		message : message,
		buttons : {
			success : {
				label : "OK",
				className : "btn-primary",
				callback : function() {
					return true;
				}
			}
		}
	});
}
