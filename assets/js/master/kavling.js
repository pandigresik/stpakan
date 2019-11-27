var search = false;
var page_number=0;
var total_page =null;

var form_mode = "";
var selected_kavling = "";

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
	namagudang = $('#q_namagudang').val();
	nomorkavling = $('#q_nomorkavling').val();
/*	beratmak = $('#q_beratmak').val();*/
	jmlpallet = $('#q_jmlpallet').val();
	status = $('#q_status').val();

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kavling/get_pagination/",
		data: {
			namafarm : namafarm,
			namagudang : namagudang,
			nomorkavling : nomorkavling,
//			beratmak : beratmak,
			jmlpallet : jmlpallet,
			status : status,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-kavling").html("");

		window.mydata = data;

		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;
				var html = new Array();

				$.each(record_par_page, function (key, data) {
					var v_layoutposisi = "";

					switch(data.layout_posisi){
						case "L" :
							v_layoutposisi = "Kiri Lorong";
							break;
						case "R" :
							v_layoutposisi = "Kanan Lorong";
							break;
					}

					var v_status = "";
					if(data.status_kavling == "A")
						v_status = "Aktif";
					else if(data.status_kavling == "N")
						v_status = "Tidak Aktif";
					else if(data.status_kavling == "M")
						v_status = "Kunci Masuk";
					else
						v_status = "Kunci Keluar";


					html.push('<tr><td>'+
					data.row+'</td><td>'+
					data.nama_farm+'</td><td>'+
					data.nama_gudang+'</td><td>'+
					data.no_kavling+'</td><td>'+
					v_layoutposisi+'</td><td>'+
					data.jml_pallet+'</td><td>'+
					v_status+'</td><td>'+
					data.kode_gudang+'</td><td>'+
					data.kode_farm+'</td></tr>');
				});

				if(total_page == 1)
					$("#next").prop('disabled', true);

				$("tbody", "#master-kavling").html(html.join(''));
				$('#master-kavling td:nth-child(8),th:nth-child(8)').hide();
				$('#master-kavling td:nth-child(9),th:nth-child(9)').hide();
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
	setDataFarm();

	$("#next").on("click", function(){
		page_number = (page_number+1);
		getReport(page_number);
	});

	$("#previous").on("click", function(){
		page_number = (page_number-1);
		getReport(page_number);
	});
});

function setDataFarm(){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kavling/getDataFarm/",
		data: {
		}
	})
	.done(function(data){
		var option = new Array();

		for(var i=0;i<data.length;i++){
			option[i] = '<option value="' + data[i]["kode_farm"] + '">' + data[i]["nama_farm"] + ' - ' + data[i]["kode_farm"] + '</option>';
		}

		$('#selectFarm').html('<option value=""> - Pilih Farm - </option>' + option.join(""));
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
}

$('#selectFarm').change(function(){
	var kode_farm = $(this).val();
	if(!empty(kode_farm)){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/kavling/getDataFarmGudang/",
			data: {
				kode_farm : kode_farm
			}
		})
		.done(function(data){
			var option = new Array();

			for(var i=0;i<data.length;i++){
				option[i] = '<option value="' + data[i]["kode_gudang"] + '">' + data[i]["nama_gudang"] + '</option>';
			}

			$('#selectGudang').html('<option value=""> - Pilih Gudang - </option>' + option.join(""));
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
		});
	}
});

$('#selectGudang').change(function(){
	var kode_farm = $('#selectFarm').val();
	var kode_gudang = $('#selectGudang').val();
	if(!empty(kode_farm) && !empty(kode_gudang)){
		$.ajax({
			type : "POST",
			url : "master/kavling/setLayoutKavling",
			data : {
				kode_farm : kode_farm,
				kode_gudang : kode_gudang
			},
			success : function(data) {
				$('#layout').html(data);

				$('#layout').find('table:first-child').css('border','none');
				$('#layout').find('table:first-child thead tr th.no-border').css('border','none');
				$('#layout').find('table.tbl-layout-kavling th').css('border-color','black');
				$('#layout').find('table.tbl-layout-kavling td').css('border-color','black');
			}
		});
	}
});

$('.q_search').keyup(function(){
	this.value = this.value.toUpperCase();
	goSearch();
});

$('#q_status').change(function(){
	goSearch();
});

$('.field_input').keyup(function(){
	checkInput();
});

$('#master-kavling').on('click','tr',function() {

});

$('#master-kavling > tbody').on('dblclick','tr',function() {
	var no_kavling = $(this).find('td:nth-child(4)').text();
	var po_kavling = $(this).find('td:nth-child(5)').text();
	var kode_gudang = $(this).find('td:nth-child(8)').text();
	var kode_farm = $(this).find('td:nth-child(9)').text();

	var kav = no_kavling.split('-');
	var baris = kav[0].substring(0,1);
	var nomor_posisi = kav[0].substring(1,2);
	var nomor_kolom = parseInt(kav[1]);
	var layout_posisi = (po_kavling == "Kiri Lorong") ? 'L' : 'R';


	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kavling/getDataKavling/",
		data: {
			kode_farm : kode_farm,
			kode_gudang : kode_gudang,
			no_kavling : no_kavling,
			baris : baris,
			no_posisi : nomor_posisi,
			no_kolom : nomor_kolom,
			lay_posisi : layout_posisi
		}
	})
	.done(function(data){
		resetInput();

		$('select#inp_namafarm option[value='+data.kode_farm+']').prop('selected',1);

		if(!empty(data.kode_farm)){
			$.ajax({
				type:'POST',
				dataType: 'json',
				url : "master/kavling/getGudangInFarm/",
				data: {
					kode_farm : data.kode_farm
				}
			})
			.done(function(data_gudang){
				var html = new Array();
				for(var i=0; i<data_gudang.length; i++){
					html[i] = '<option value="' + data_gudang[i]["kode_gudang"] + '">' + data_gudang[i]["nama_gudang_long"] + '</option>';
				}

				$('#inp_namagudang').html(html.join(''));

				$('select#inp_namagudang option[value='+data.kode_gudang+']').prop('selected',1);
				$('select#inp_nomorbaris option[value='+data.no_baris+']').prop('selected',1);
				$('#inp_nomorposisi').val(data.no_posisi);
				$('#inp_nomorposisi').val(data.no_posisi);
				$('select#inp_namaposisi option[value='+data.layout_posisi+']').prop('selected',1);
				$('#inp_kolom1').val(data.no_kolom);
		//		$('#inp_beratmaksimal').val(data.max_berat);
				$('#inp_jmlpallet').val(data.jml_pallet);
				$('#inp_kodeverifikasi').val(data.kode_verifikasi);

				if(data.status_kavling == "A")
					$("#stAktif").prop("checked", true)
				else if(data.status_kavling == "N")
					$("#stTdkAktif").prop("checked", true)
				else if(data.status_kavling == "M")
					$("#stKunciMasuk").prop("checked", true)
				else
					$("#stKunciKeluar").prop("checked", true)

				$('#inp_namafarm').prop("disabled", true);
				$('#inp_namagudang').prop("disabled", true);
				$('#inp_nomorbaris').prop("disabled", true);
				$('#inp_nomorposisi').prop("disabled", true);
				$('#inp_namaposisi').prop("disabled", true);
				$('#inp_kolom1').prop("disabled", true);
				$('#inp_kolom2').hide();
				$('.hideable').hide();

				$('#btnSimpan').hide();
				$('#btnUbah').show();

				$('#modal_kavling').modal("show");

			})
			.fail(function(reason){
				console.info(reason);
			})
			.then(function(data_gudang){
			});
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});

	form_mode = "ubah";
});

$("#btnTambah").click(function(){
	resetInput();
	form_mode = "tambah";

	$('#btnSimpan').show();
	$('#btnUbah').hide();

	var arr = []
	while(arr.length < 4){
	  var randomnumber=Math.ceil(Math.random()*9)
	  var found=false;
	  for(var i=0;i<arr.length;i++){
		if(arr[i]==randomnumber){found=true;break}
	  }
	  if(!found)arr[arr.length]=randomnumber;
	}

	$("#inp_kodeverifikasi").val(arr.join(""));

	var kode_farm = $('#ses_kodefarm').val();
	$('select#inp_namafarm option[value='+kode_farm+']').prop('selected',1);
	$('#btnSimpan').addClass("disabled");
	$('#btnUbah').hide();

	$('#modal_kavling').modal("show");
});

$("#btnBatal").click(function(){
	$('#modal_kavling').modal("hide");
	resetInput();
});

$("#btnSimpan").click(function(){
	var kode_farm = $('#inp_namafarm').val();
	var kode_gudang = $('#inp_namagudang').val();

	if(empty(kode_gudang)){
		$('#inp_namagudang').parent().addClass("has-error");
		$('#inp_namagudang').focus();
		return false;
	}else{
		$('#inp_namagudang').parent().removeClass("has-error");
	}

	var baris = $('#inp_nomorbaris').val();
	var nomorposisi = $('#inp_nomorposisi').val();

	if(empty(nomorposisi)){
		$('#inp_nomorposisi').parent().addClass("has-error");
		$('#inp_nomorposisi').focus();
		return false;
	}else{
		$('#inp_nomorposisi').parent().removeClass("has-error");
	}

	var namaposisi = $('#inp_namaposisi').val();
	var kolom1 = $('#inp_kolom1').val();

	if(empty(kolom1)){
		$('#inp_kolom1').parent().addClass("has-error");
		$('#inp_kolom1').focus();
		return false;
	}else{
		$('#inp_kolom1').parent().removeClass("has-error");
	}

	var kolom2 = $('#inp_kolom2').val();

	if(empty(kolom2)){
		$('#inp_kolom2').parent().addClass("has-error");
		$('#inp_kolom2').focus();
		return false;
	}else{
		$('#inp_kolom2').parent().removeClass("has-error");
	}

	var step = $('#inp_step').val();

	if(empty(step)){
		$('#inp_step').parent().addClass("has-error");
		$('#inp_step').focus();
		return false;
	}else{
		$('#inp_step').parent().removeClass("has-error");
	}
/*
	var berat_maks = $('#inp_beratmaksimal').val();

	if(empty(berat_maks)){
		$('#inp_beratmaksimal').parent().addClass("has-error");
		$('#inp_beratmaksimal').focus();
		return false;
	}else{
		$('#inp_beratmaksimal').parent().removeClass("has-error");
	}
*/
	var jmlpallet = $('#inp_jmlpallet').val();

	if(empty(jmlpallet)){
		$('#inp_jmlpallet').parent().addClass("has-error");
		$('#inp_jmlpallet').focus();
		return false;
	}else{
		$('#inp_jmlpallet').parent().removeClass("has-error");
	}

	var kode_verifikasi = $('#inp_kodeverifikasi').val();

	if(empty(kode_verifikasi)){
		$('#inp_kodeverifikasi').parent().addClass("has-error");
		$('#inp_kodeverifikasi').focus();
		return false;
	}else{
		$('#inp_kodeverifikasi').parent().removeClass("has-error");
	}

	var status = $('input:radio[name=statuskavling]:checked').val();

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kavling/cekNomorKavling/",
		data: {
			kode_farm : kode_farm,
			kode_gudang : kode_gudang,
			baris : baris,
			nomorposisi : nomorposisi,
			kolom1 : kolom1,
			kolom2 : kolom2,
			step : step
		}
	})
	.done(function(data){
		if(!$.isEmptyObject(data)){
			var _dd = [];
			for(var _x in data){
				_dd.push(data[_x]);
			}
			bootbox.alert("Nomor Kavling " + _dd.join(", ") + " sudah terdaftar");
		}else{
			bootbox.dialog({
				message: "Apakah Anda yakin akan Menyimpan data Kavling ini?",
				title: "",
				buttons: {
					main: {
						label: "Ya",
						className: "btn-primary",
						callback: function() {
							$.ajax({
								type:'POST',
								dataType: 'json',
								url : "master/kavling/simpanKavling/",
								data: {
									kode_farm : kode_farm,
									kode_gudang : kode_gudang,
									baris : baris,
									nomorposisi : nomorposisi,
									namaposisi : namaposisi,
									kolom1 : kolom1,
									kolom2 : kolom2,
									step : step,
					//				berat_maks : berat_maks,
									jmlpallet : jmlpallet,
									kode_verifikasi : kode_verifikasi,
									status : status
								}
							})
							.done(function(data){
								if(data.result == "success"){
									toastr.success("Penyimpanan Kavling berhasil dilakukan.",'Informasi');

									$('#modal_kavling').modal("hide");
									resetInput();

									getReport(page_number);
								}else{
									toastr.warning("Penyimpanan data Kavling gagal dilakukan.",'Peringatan');
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
	var kode_farm = $('#inp_namafarm').val();
	var kode_gudang = $('#inp_namagudang').val();
	var baris = $('#inp_nomorbaris').val();
	var nomorposisi = $('#inp_nomorposisi').val();
	var namaposisi = $('#inp_namaposisi').val();
	var kolom1 = $('#inp_kolom1').val();
//	var berat_maks = $('#inp_beratmaksimal').val();
	var jmlpallet = $('#inp_jmlpallet').val();
	var kode_verifikasi = $('#inp_kodeverifikasi').val();
	var status = $('input:radio[name=statuskavling]:checked').val();

	bootbox.dialog({
		message: "Apakah Anda yakin akan Mengubah data Kavling ini?",
		title: "",
		buttons: {
			main: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "master/kavling/ubahKavling/",
						data: {
							kode_farm : kode_farm,
							kode_gudang : kode_gudang,
							baris : baris,
							nomorposisi : nomorposisi,
							namaposisi : namaposisi,
							kolom1 : kolom1,
			//				berat_maks : berat_maks,
							jmlpallet : jmlpallet,
							kode_verifikasi : kode_verifikasi,
							status : status
						}
					})
					.done(function(data){
						if(data.result == "success"){
							toastr.success("Perubahan Kavling berhasil dilakukan.",'Informasi');

							$('#modal_kavling').modal("hide");
							resetInput();

							getReport(page_number);
						}else{
							toastr.warning("Perubahan data Kavling gagal dilakukan.",'Peringatan');
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

$('#inp_namafarm').change(function(){
	var kode_farm = $('#inp_namafarm').val();

	if(!empty(kode_farm)){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : "master/kavling/getGudangInFarm/",
			data: {
				kode_farm : kode_farm
			}
		})
		.done(function(data){
			var html = new Array();
			for(var i=0; i<data.length; i++){
				html[i] = '<option value="' + data[i]["kode_gudang"] + '">' + data[i]["nama_gudang_long"] + '</option>';
			}

			$('#inp_namagudang').html(html.join(''));
		})
		.fail(function(reason){
			console.info(reason);
		})
		.then(function(data){
		});
	}
});

/*
* FUNCTION
*/

function selected(e) {
	var no_kavling = $(e).attr('data-no-kavling');
	var kode_farm = $('#selectFarm').val();
	var kode_gudang = $('#selectGudang').val();

	var kav = no_kavling.split('-');
	var baris = kav[0].substring(0,1);
	var nomor_posisi = kav[0].substring(1,2);
	var nomor_kolom = parseInt(kav[1]);


	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kavling/getDataKavling/",
		data: {
			kode_farm : kode_farm,
			kode_gudang : kode_gudang,
			no_kavling : no_kavling,
			baris : baris,
			no_posisi : nomor_posisi,
			no_kolom : nomor_kolom
		}
	})
	.done(function(data){
		resetInput();

		$('select#inp_namafarm option[value='+data.kode_farm+']').prop('selected',1);

		if(!empty(data.kode_farm)){
			$.ajax({
				type:'POST',
				dataType: 'json',
				url : "master/kavling/getGudangInFarm/",
				data: {
					kode_farm : data.kode_farm
				}
			})
			.done(function(data_gudang){
				var html = new Array();
				for(var i=0; i<data_gudang.length; i++){
					html[i] = '<option value="' + data_gudang[i]["kode_gudang"] + '">' + data_gudang[i]["nama_gudang_long"] + '</option>';
				}

				$('#inp_namagudang').html(html.join(''));

				$('select#inp_namagudang option[value='+data.kode_gudang+']').prop('selected',1);
				$('select#inp_nomorbaris option[value='+data.no_baris+']').prop('selected',1);
				$('#inp_nomorposisi').val(data.no_posisi);
				$('#inp_nomorposisi').val(data.no_posisi);
				$('select#inp_namaposisi option[value='+data.layout_posisi+']').prop('selected',1);
				$('#inp_kolom1').val(data.no_kolom);
	//			$('#inp_beratmaksimal').val(data.max_berat);
				$('#inp_jmlpallet').val(data.jml_pallet);
				$('#inp_kodeverifikasi').val(data.kode_verifikasi);

				if(data.status_kavling == "A")
					$("#stAktif").prop("checked", true)
				else if(data.status_kavling == "N")
					$("#stTdkAktif").prop("checked", true)
				else if(data.status_kavling == "M")
					$("#stKunciMasuk").prop("checked", true)
				else
					$("#stKunciKeluar").prop("checked", true)

				$('#inp_namafarm').prop("disabled", true);
				$('#inp_namagudang').prop("disabled", true);
				$('#inp_nomorbaris').prop("disabled", true);
				$('#inp_nomorposisi').prop("disabled", true);
				$('#inp_namaposisi').prop("disabled", true);
				$('#inp_kolom1').prop("disabled", true);
				$('#inp_kolom2').hide();
				$('.hideable').hide();

				$('#btnSimpan').hide();
				$('#btnUbah').show();

				$('#modal_kavling').modal("show");

			})
			.fail(function(reason){
				console.info(reason);
			})
			.then(function(data_gudang){
			});
		}
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});

	form_mode = "ubah";
}

function resetInput(){
	$('#inp_namafarm :nth-child(1)').prop('selected', true);
	$('#inp_namagudang :nth-child(1)').prop('selected', true);
	$('#inp_nomorposisi').val('');
	$('#inp_namaposisi :nth-child(1)').prop('selected', true);
	$('#inp_kolom1').val('');
	$('#inp_kolom2').val('');
	$('#inp_step').val('');
//	$('#inp_beratmaksimal').val('');
	$('#inp_jmlpallet').val('');
	$('#inp_kodeverifikasi').val('');
	$("#stAktif").prop("checked", true);

	$('#inp_nomorbaris').prop("disabled", false);
	$('#inp_nomorposisi').prop("disabled", false);
	$('#inp_namaposisi').prop("disabled", false);
	$('#inp_kolom1').prop("disabled", false);
	$('#inp_namafarm').prop("disabled", false);
	$('#inp_namagudang').prop("disabled", false);
	$('#inp_kolom2').show();
	$('.hideable').show();
}

function goSearch(){
	search = true;
	page_number = 0;
	getReport(page_number);
}

$('.field_input').keyup(function(){
	checkInput();
});

function checkInput(){
	var kode_farm = $('#inp_namafarm').val();
	var kode_gudang = $('#inp_namagudang').val();
	var baris = $('#inp_nomorbaris').val();
	var nomorposisi = $('#inp_nomorposisi').val();
	var namaposisi = $('#inp_namaposisi').val();
	var kolom1 = $('#inp_kolom1').val();
	var kolom2 = $('#inp_kolom2').val();
	var step = $('#inp_step').val();
//	var berat_maks = $('#inp_beratmaksimal').val();
	var jmlpallet = $('#inp_jmlpallet').val();
	var kode_verifikasi = $('#inp_kodeverifikasi').val();
	var status = $('input:radio[name=statuskavling]:checked').val();

	if(form_mode == "tambah"){
		if(empty(nomorposisi) || empty(kolom1) || empty(kolom2) || empty(step) ||  empty(jmlpallet) || empty(kode_verifikasi)){
			$('#btnSimpan').addClass("disabled");
		}else{
			$('#btnSimpan').removeClass("disabled");
		}
	}else{
		if(empty(nomorposisi) || empty(kolom1) || empty(jmlpallet) || empty(kode_verifikasi)){
			$('#btnUbah').addClass("disabled");
		}else{
			$('#btnUbah').removeClass("disabled");
		}
	}

}

function cekNumerik(field){
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	}
}

function generateKavling(){
	var baris = $('#inp_nomorbaris').val();
	var nomorposisi = $('#inp_nomorposisi').val();
	var namaposisi = $('#inp_namaposisi').val();
	var kolom1 = $('#inp_kolom1').val();
	var kolom2 = $('#inp_kolom2').val();
	var step = $('#inp_step').val();

	if(!empty(nomorposisi) && !empty(kolom1) && !empty(kolom2) && !empty(step)){
		var pad = "00";
		var block = new Array();
		for(var i=parseInt(kolom1);i<=parseInt(kolom2);i+=parseInt(step)){
			var div = "<div style='color:#A6A5A8;float:left;width:50px;padding:5px;margin:1px;border:1px solid #D1CFD4;'>" +
					   "<center>" + baris + nomorposisi + "-" + (pad + i).slice(-pad.length) + "</center>" +
					   "</div>";
			block.push(div);
			console.log(div);
		}

		$(".generate-kavling").html(block.join(''));
	}
}
