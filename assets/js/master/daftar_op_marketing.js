var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_op_marketing = "";

var json_month = $.datepicker.regional['id'].monthNamesShort; //["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

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

	grup = $('#q_grup').val();
	farm = $('#q_namafarm').val();
	tahun = $('#q_tahun').val();
	tanggal_kirim = $('#q_tanggal_kirim').val();
	no_op_awal = $('#q_no_op_awal').val();
	no_op_akhir = $('#q_no_op_akhir').val();
	no_op_pakai = $('#q_no_op_pakai').val();

	$.ajax({
		type : 'POST',
		dataType : 'json',
		url : "master/daftar_op_marketing/get_pagination/",
		data : {
			grup : grup,
			farm : farm,
			tahun : tahun,
			tanggal_kirim : tanggal_kirim,
			no_op_awal : no_op_awal,
			no_op_akhir : no_op_akhir,
			no_op_pakai : no_op_pakai,
			page_number : page_number,
			search : search
		}
	}).done(function(data) {
		$("tbody", "#master-daftar-op-marketing").html("");

		window.mydata = data;

		if (!empty(mydata.length)) {
			if (mydata.length > 0) {
				total_page = mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;

				//$('#q_grup').html("");
				//$('#q_tahun').html("");
				//$('#q_grup').append(new Option("SEMUA", ""));
				//$('#q_tahun').append(new Option("SEMUA", ""));
				//var x = [];
				//var y = [];
				$.each(record_par_page, function(key, data) {
					//if ($.inArray(data.GRUP_FARM, x) < 0) {
					//	$('#q_grup').append(new Option(data.GRUP_FARM_LABEL, data.GRUP_FARM));
					//}
					//if ($.inArray(data.TAHUN, y) < 0) {
					//	$('#q_tahun').append(new Option(data.TAHUN, data.TAHUN));
					//}
					//x.push(data.GRUP_FARM);
					//y.push(data.TAHUN);
					//append_grup += "<option value='" + data.GRUP_FARM + "'>" + data.GRUP_FARM_LABEL + "</option>";
					//append_tahun += "<option value='" + data.TAHUN + "'>" + data.TAHUN + "</option>";

					$("tbody", "#master-daftar-op-marketing").append('<tr data-tanggal-kirim="' + data.TGL_KIRIM + '"><td data-kode-grup="'+data.GRUP_FARM+'">' + data.GRUP_FARM_LABEL + '</td><td data-kode-farm="'+data.KODE_FARM+'">' + data.NAMA_FARM + '</td><td>' + data.TAHUN + '</td><td>' + convert_month(data.TGL_KIRIM_TEXT) + '</td><td>' + data.NO_OP_AWAL + '</td><td>' + data.NO_OP_AKHIR + '</td><td>' + data.NO_OP_PAKAI + '</td></tr>');

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


$("#inp_tanggal_kirim").datepicker({
	dateFormat : 'dd M yy',
});
$("#q_tanggal_kirim").datepicker({
	dateFormat : 'dd M yy',
});
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

$('#q_tanggal_kirim').change(function() {
	goSearch();
});

$('#q_grup').change(function() {
	goSearch();
});

$('#q_namafarm').change(function() {
	goSearch();
});

$('#q_tahun').change(function() {
	goSearch();
});

$('.field_input').keyup(function() {
	checkInput();
});

$('#master-daftar-op-marketing').on('click', 'tr', function() {
	selected_op_marketing = $(this).find('td:nth-child(1)').text();
});

$('#master-daftar-op-marketing > tbody').on('dblclick', 'tr', function() {
	selected_op_marketing = $(this).attr('data-tanggal-kirim');
	kode_farm = $(this).find('td').eq(1).data('kode-farm');
	form_mode = "ubah";
	$.ajax({
		type : 'POST',
		dataType : 'json',
		url : "master/daftar_op_marketing/get_op_marketing/",
		data : {
			tanggal_kirim : selected_op_marketing,
			kode_farm : kode_farm,
		}
	}).done(function(data) {
		$('#inp_grup').val(data.GRUP_FARM);
		$('#inp_nama_farm').val(data.KODE_FARM);
		$('#inp_tahun').val(data.TAHUN);
		$('#inp_tanggal_kirim').val(convert_month(data.TGL_KIRIM_TEXT));
		$('#inp_no_op_awal').val(data.NO_OP_AWAL);
		$('#inp_no_op_akhir').val(data.NO_OP_AKHIR);
		$('#inp_no_op_pakai').val(data.NO_OP_PAKAI);

		$('#inp_grup').attr("disabled", true);
		//$('#inp_nama_farm').attr("disabled", true);
		$('#inp_tahun').attr("readonly", true);
		$('#inp_tanggal_kirim').attr("readonly", true);

		$('#btnSimpan').hide();
		$('#btnUbah').show();
		//$('#btnUbah').removeClass('disabled');
		$('#myModalLabel').text("Master - Daftar OP Ubah");
		$('#modal_op_marketing').modal("show");
		$('#inp_tanggal_kirim').attr("disabled", true);
		$('#inp_no_op_pakai').attr("disabled", false);
	}).fail(function(reason) {
		console.info(reason);
	}).then(function(data) {
	});
});

$("#btnTambah").click(function() {
	resetInput();
	form_mode = "tambah";

	$('#inp_grup').removeAttr("disabled");
	$('#inp_tahun').attr("readonly", false);

	$('#inp_tanggal_kirim').attr("readonly", true);
	$('#inp_tanggal_kirim').removeAttr("disabled");
	$('#btnSimpan').show();
	$('#btnUbah').hide();

	$('#myModalLabel').text("Master - Daftar OP Baru");
	//$('#inp_nama_farm').attr("disabled", true);
	$('#inp_no_op_pakai').attr("disabled", true);
	
	$('#modal_op_marketing').modal("show");
});

$("#btnBatal").click(function() {
	$('#modal_op_marketing').modal("hide");
	resetInput();
});

$("#btnSimpan").click(function() {
	grup = $('#inp_grup').val();
	tahun = $('#inp_tahun').val();
	farm = $('#inp_nama_farm').val();
	tanggal_kirim = $('#inp_tanggal_kirim').val();
	no_op_awal = $('#inp_no_op_awal').val();
	no_op_akhir = $('#inp_no_op_akhir').val();
	no_op_pakai = $('#inp_no_op_pakai').val();

	if (grup && tahun && tanggal_kirim && no_op_awal && no_op_akhir && no_op_pakai) {

		kontrol_simpan(function(r){
			if(r==1){
				var data = "Apakah Anda yakin akan Menyimpan data Daftar OP?";
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
									url : "master/daftar_op_marketing/add_op_marketing/",
									data : {
										grup : grup,
										farm : farm,
										tahun : tahun,
										tanggal_kirim : tanggal_kirim,
										no_op_awal : no_op_awal,
										no_op_akhir : no_op_akhir,
										no_op_pakai : no_op_pakai
									}
								}).done(function(data) {
									if (data.result == "success") {
										notificationBox("Penyimpanan Daftar OP baru berhasil dilakukan.");

										$('#modal_op_marketing').modal("hide");
										resetInput();

										getReport(page_number);
									}
									else {
										if (data.check == "failed")
											notificationBox("Daftar OP sudah ada.");
										else
											notificationBox("Penyimpanan Daftar OP baru gagal dilakukan");
									}
								}).fail(function(reason) {
									console.info(reason);
								}).then(function(data) {
								});
							}
						}
					}
				})

			}
			else{
				notificationBox("Salah satu No. OP antara "+no_op_awal+" sampai "+no_op_akhir+" dalam pemakaian.");
			}
		});
			
	} else {
		notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
	}
});

$("#btnUbah").click(function() {
	grup = $('#inp_grup').val();
	tahun = $('#inp_tahun').val();
	tanggal_kirim = $('#inp_tanggal_kirim').val();
	no_op_awal = $('#inp_no_op_awal').val();
	no_op_akhir = $('#inp_no_op_akhir').val();
	no_op_pakai = $('#inp_no_op_pakai').val();

		kontrol_simpan(function(r){
			if(r==1){
				var data = "Apakah anda yakin akan mengubah data daftar OP?";
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
									url : "master/daftar_op_marketing/update_op_marketing/",
									data : {
										grup : grup,
										tahun : tahun,
										tanggal_kirim : tanggal_kirim,
										no_op_awal : no_op_awal,
										no_op_akhir : no_op_akhir,
										no_op_pakai : no_op_pakai
									}
								}).done(function(data) {
									if (data.result == "success") {
										notificationBox("Perubahan data Daftar OP berhasil dilakukan.");
										$('#modal_op_marketing').modal("hide");
										resetInput();

										getReport(page_number);
									} 
									else {
										if (data.check == "failed")
											notificationBox("Daftar OP sudah ada.");
										else
											notificationBox("Perubahan data Daftar OP gagal dilakukan.");
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
			else{
				notificationBox("Salah satu No. OP antara "+no_op_awal+" sampai "+no_op_akhir+" dalam pemakaian.");
			}
		});
});

$('#inp_grup').change(function(){
	
	var grup = $(this).val();
	
	if(grup == "BRD"){
		$('#inp_nama_farm').attr("disabled", false);
	}else{
		//$('#inp_nama_farm').attr("disabled", true);
	}
	
	
	$.ajax({
		type : 'POST',
		dataType : 'json',
		url : "master/daftar_op_marketing/get_farm/",
		data : {
			grup : grup
		}
	}).done(function(data) {
		
		if(data.TotalRows > 0){
			var farms = new Array();
			for(var i=0;i<(data.Rows).length;i++){
				var obj = (data.Rows)[i];
				farms[i] = '<option value="' + obj.KODE_FARM + '">' + obj.NAMA_FARM + '</option>';
			}
			
			$('#inp_nama_farm').html(farms.join(''));
		}
		

	}).fail(function(reason) {
		console.info(reason);
	}).then(function(data) {
	});
});

/*
 * FUNCTION
 */

function resetInput() {
	$('#inp_grup :nth-child(1)').prop('selected', true);
	$('#inp_nama_farm :nth-child(1)').prop('selected', true);
	$('#inp_tanggal_kirim').val('');
	$('#inp_no_op_awal').val('');
	$('#inp_no_op_akhir').val('');
	$('#inp_no_op_pakai').val('');
}

function kontrol_op(e){
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

function set_op_pakai(e){
	var no_op_awal = $(e).val();
	$('#inp_no_op_pakai').val('');
	if(no_op_awal){
		$('#inp_no_op_pakai').val(no_op_awal);
	}
}

function kontrol_op_pakai(e){

	var tahun = $('#inp_tahun').val();
	var no_op_awal = $('#inp_no_op_awal').val();
	var no_op_akhir = $('#inp_no_op_akhir').val();
	var no_op_pakai = $('#inp_no_op_pakai').val();
	if(tahun && no_op_awal && no_op_akhir && no_op_pakai){
		if(parseInt(no_op_pakai) >= parseInt(no_op_awal) && parseInt(no_op_pakai) <= parseInt(no_op_akhir)){
		
			$.ajax({
				type : 'POST',
				dataType : 'json',
				url : "master/daftar_op_marketing/kontrol_op_pakai/",
				data : {
					tahun : tahun,
					no_op_awal : no_op_awal,
					no_op_akhir : no_op_akhir,
					no_op_pakai : no_op_pakai
				}
			}).done(function(data) {
				if(data == 0){
					$('#inp_no_op_pakai').val('');
					notificationBox("No. OP Pakai tidak valid.");
					
				}

			}).fail(function(reason) {
				console.info(reason);
			}).then(function(data) {
			});
		}
		else{
			$('#inp_no_op_pakai').val('');
			notificationBox("No. OP Pakai tidak valid.");
		}
	}
}

function kontrol_simpan(callback){

	var tahun = $('#inp_tahun').val();
	var no_op_awal = $('#inp_no_op_awal').val();
	var no_op_akhir = $('#inp_no_op_akhir').val();
	var no_op_pakai = $('#inp_no_op_pakai').val();
	if(tahun && no_op_awal && no_op_akhir && no_op_pakai){
		if(parseInt(no_op_pakai) >= parseInt(no_op_awal) && parseInt(no_op_pakai) <= parseInt(no_op_akhir)){
		
			$.ajax({
				type : 'POST',
				dataType : 'json',
				url : "master/daftar_op_marketing/kontrol_simpan/",
				data : {
					tahun : tahun,
					no_op_awal : no_op_awal,
					no_op_akhir : no_op_akhir,
					no_op_pakai : no_op_pakai
				}
			}).done(function(data) {
				callback(data);

			}).fail(function(reason) {
				console.info(reason);
			}).then(function(data) {
			});
		}
		else{
			callback(0);
		}
	}else {
		notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
	}
}

function kontrol_kirim(elm) {
    var tanggal_kirim = $(elm).val();
    if(tanggal_kirim){

        tanggal_kirim = tanggal_kirim.split(" ");
        var tanggal_kirim_baru = tanggal_kirim[2]+'-'+(json_month.indexOf(tanggal_kirim[1])+1)+'-'+tanggal_kirim[0];
        $.ajax({
            type : 'POST',
            url : "master/daftar_op_marketing/kontrol_kirim/",
            data : {
                tanggal_kirim : tanggal_kirim_baru
            },
            dataType : "json"
        }).done(function(data) {
            if(data.result == 0){
                notificationBox("Tanggal kirim harus lebih besar atau sama dengan tanggal hari ini.");
                $(elm).val('');
            }
        }).fail(function(reason) {
            console.info(reason);
        }).then(function(data) {
        });
    }
}

function kontrol_number(e){
	var id = $(e).attr("id");
	
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
	
	if(id == "inp_no_op_awal" && form_mode == "tambah"){
		$("#inp_no_op_pakai").val($("#inp_no_op_awal").val());
	}
}

function goSearch() {
	page_number = 0;
	search = true;
	getReport(page_number);
}

function checkInput() {

	tanggal_kirim = $('#inp_tanggal_kirim').val();
	no_op_awal = $('#inp_no_op_awal').val();
	no_op_akhir = $('#inp_no_op_akhir').val();
	no_op_pakai = $('#inp_no_op_pakai').val();
	/*
	if (tanggal_kirim != "" && no_op_awal != "" && no_op_akhir != "" && no_op_pakai != "") {
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

function notificationBox(message){
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