var mKandang = {
	filterInputan : function(elm){
		var ini = $(elm);
		var farmArr = ['BRD','BDY'];
		var kodeFarm = ini.val();
		if(empty(kodeFarm)){
			bootbox.alert("Pilih farm terlebih dahulu");
		}
		else{
			var opsi = ini.find('option:selected');
			var grupFarm = opsi.data('grupfarm');
			var jmlFlok = opsi.data('jmlflok');
			var tmpFarm;
			for(var i in farmArr){
				tmpFarm = farmArr[i];
				if(tmpFarm == grupFarm){
					$('div[data-grupfarm = '+tmpFarm+']').show();
				}
				else{
					$('div[data-grupfarm = '+tmpFarm+']').hide();
				}
			}
			$('#inp_noflok').empty();
			if(!empty(jmlFlok)){
				var i = 1;
				var elmOpsi = [];
				while(i <= jmlFlok){
					elmOpsi.push('<option>'+i+'</option>');
					i++;
				}
				$('#inp_noflok').append(elmOpsi.join(''));

			}

		}
	}
};

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

	namafarm = $('#q_namafarm').val();
	namakandang = $('#q_namakandang').val();
	kapasitaskandangjantan = $('#q_kapasitaskandangjantan').val();
	kapasitaskandangbetina = $('#q_kapasitaskandangbetina').val();
	kapasitaskandang = $('#q_kapasitaskandang').val();
	tipekandang = $('#q_tipekandang').val();
	tipelantai = $('#q_tipelantai').val();
	status = $('#q_status').val();

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kandang/get_pagination/",
		data: {
			namafarm : namafarm,
			namakandang : namakandang,
			kapasitaskandangjantan : kapasitaskandangjantan,
			kapasitaskandangbetina : kapasitaskandangbetina,
			kapasitaskandang : kapasitaskandang,
			tipekandang : tipekandang,
			tipelantai : tipelantai,
			status : status,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#master-kandang").html("");

		window.mydata = data;

		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;

				$.each(record_par_page, function (key, data) {

					var v_tipekandang = (data.tipe_kandang == "O") ? "Open" : "Close";
					var v_tipelantai = (data.tipe_lantai == "S") ? "Slate" : "Litter";
					var v_status = (data.status_kandang == "A") ? "Aktif"  : "Tidak Aktif";

					var jml_jantan = empty(data.jml_jantan) ? 0 : data.jml_jantan;
					var jml_betina = empty(data.jml_betina) ? 0 : data.jml_betina;
					var max_populasi = empty(data.max_populasi) ? 0 : data.max_populasi;
					$("tbody", "#master-kandang").append('<tr><td>'+
					data.kode_farm + '-' + data.kode_kandang+'</td><td>'+
					data.row+'</td><td>'+
					data.nama_farm+'</td><td>'+
					data.nama_kandang+'</td><td>'+
					jml_jantan+'</td><td>'+
					jml_betina+'</td><td>'+
					max_populasi+'</td><td>'+
					v_tipekandang+'</td><td>'+
					v_tipelantai+'</td><td>'+
					v_status+'</td></tr>');

				});

				if(total_page == 1)
					$("#next").prop('disabled', true);

				$('td:nth-child(1),th:nth-child(1)').hide();
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
	mKandang.filterInputan($('#inp_namafarm'));
	$('.numeric').priceFormat({
		prefix: '',
		centsLimit : 0,
	    thousandsSeparator: '.'
	});
	$('.digit').priceFormat({
		prefix: '',
		centsLimit : 0,
	    thousandsSeparator: ''
	});
});

$('.q_search').keyup(function(){
	this.value = this.value.toUpperCase();
	goSearch();
});

$('#q_tipekandang').change(function(){
	goSearch();
});

$('#q_tipelantai').change(function(){
	goSearch();
});

$('#q_status').change(function(){
	goSearch();
});

$('.field_input').keyup(function(){
	checkInput();
});

$('#master-kandang').on('click','tr',function() {
	selected_kandang = $(this).find('td:nth-child(1)').text();
});

$('#master-kandang > tbody').on('dblclick','tr',function() {
	selected_kandang = $(this).find('td:nth-child(1)').text();
	form_mode = "ubah";

	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kandang/get_kandang/",
		data: {
			kodekandang : selected_kandang
		}
	})
	.done(function(data){
		$('#inp_namafarm').val(data.kode_farm);
		$('#inp_kodekandang').val(data.kode_kandang);
		$('#inp_namakandang').val(data.nama_kandang);
		$('#inp_digitcek').val(data.kode_verifikasi);
		$('#inp_kapasitaskandangjantan').val(number_format(data.jml_jantan,0,',','.'));
		$('#inp_kapasitaskandangbetina').val(number_format(data.jml_betina,0,',','.'));
		$('#inp_luaskandangbetina').val(number_format(data.luas_kandang_betina,0,',','.'));
		$('#inp_luaskandangjantan').val(number_format(data.luas_kandang_jantan,0,',','.'));
		$("#inp_tipekandang").val(data.tipe_kandang);
		$("#inp_tipelantai").val(data.tipe_lantai);

		$('#inp_kapasitaskandang').val(number_format(data.max_populasi,0,',','.'));
		$('#inp_luaskandang').val(number_format(data.luas_kandang,0,',','.'));
		$('#inp_jmlsekat').val(data.jml_sekat);


		$('#inp_namafarm').attr("disabled", true);
		$('#inp_kodekandang').attr("disabled", true);

		if(data.status_kandang == 'A')
			$("#inp_statuskandang").prop("checked", true);
		else
			$("#inp_statuskandang").prop("checked", false);

		$('#btnSimpan').hide();
		$('#btnUbah').show();
		$('#btnUbah').removeClass('disabled');

		$('#modal_kandang').modal("show").bind('shown.bs.modal',function(){
			mKandang.filterInputan($(this).find('#inp_namafarm'));
			$('#inp_noflok').val(data.no_flok);
		});
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
	var rand = Math.floor(Math.random() * 6666633333) + 1000000000  ;

	$('#inp_namafarm').attr("disabled", false);
	$('#inp_kodekandang').attr("disabled", false);
	$('#btnSimpan').show();
	$('#btnUbah').hide();
	$('#inp_digitcek').val(rand);

	$('#modal_kandang').modal("show");
});

$("#btnBatal").click(function(){
	$('#modal_kandang').modal("hide");
	resetInput();
});

$("#btnSimpan").click(function(){
	var _form = $(this).closest('div.modal');
	kodefarm = $('#inp_namafarm').val();
	kodekandang = $('#inp_kodekandang').val();
	namakandang = $('#inp_namakandang').val();
	digitcek = $('#inp_digitcek').val();
	jmljantan = parse_number($('#inp_kapasitaskandangjantan').val());
	jmlbetina = parse_number($('#inp_kapasitaskandangbetina').val());
	luaskandangbetina = parse_number($('#inp_luaskandangbetina').val());
	luaskandangjantan = parse_number($('#inp_luaskandangjantan').val());
	tipekandang = $("#inp_tipekandang").val();
	tipelantai = $("#inp_tipelantai").val();
	var kapasitaskandang = parse_number($('#inp_kapasitaskandang').val());
	var luaskandang = parse_number($('#inp_luaskandang').val());
	var jmlsekat = parse_number($('#inp_jmlsekat').val());
	var noflok = $('#inp_noflok').val();


	var grupFarm = $('#inp_namafarm').find('option:selected').data('grupfarm');
	var adaKosong = 0;
	_form.find('input.required:visible').each(function(){
		if($.trim($(this).val()).length == 0 || $(this).val() <= 0){
			adaKosong++;
		}
	});

	/*
	if(empty(kodekandang) || empty(namakandang) || empty(jmljantan) || empty(jmlbetina)){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		return false;
	}
	*/
	if(adaKosong){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		return false;
	}
/*
	if(grupFarm == 'BDY'){
		if(jmlsekat <= 0){
			bootbox.alert("Jumlah sekat harus lebih besar dari 0");
			return false;
		}
	}
*/
	if($("#inp_statuskandang").is(':checked'))
		status = 'A';
	else
		status = 'N';

	var dataKirim = {};
	dataKirim['BRD'] = {
			kodefarm : kodefarm,
			kodekandang : kodekandang,
			namakandang : namakandang,
			digitcek : digitcek,
			jmljantan : jmljantan,
			jmlbetina : jmlbetina,
			luaskandangbetina : luaskandangbetina,
			luaskandangjantan : luaskandangjantan,
			tipekandang : tipekandang,
			tipelantai : tipelantai,
			status : status
	};
	dataKirim['BDY'] = {
			kodefarm : kodefarm,
			kodekandang : kodekandang,
			namakandang : namakandang,
			digitcek : digitcek,
			kapasitaskandang : kapasitaskandang,
			luaskandang : luaskandang,
			jmlsekat : jmlsekat,
			noflok : noflok,
			tipekandang : tipekandang,
			tipelantai : tipelantai,
			status : status
	};
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "master/kandang/cek_kodekandang/",
		data: {
			kodefarm : kodefarm,
			kodekandang : kodekandang
		}
	})
	.done(function(data){
		if(data.jumlah > 0){
			bootbox.alert("Kode Kandang " + kodekandang + " sudah terdaftar");
		}else{
			bootbox.dialog({
				message: "Apakah Anda yakin akan Menyimpan data Kandang?",
				title: "",
				buttons: {
					main: {
						label: "Ya",
						className: "btn-primary",
						callback: function() {
							$.ajax({
								type:'POST',
								dataType: 'json',
								url : "master/kandang/add_kandang/",
								data: dataKirim[grupFarm]
							})
							.done(function(data){
								if(data.result == "success"){
									toastr.success("Penyimpanan Kandang dengan kode " + kodekandang + " berhasil dilakukan",'Informasi');

									$('#modal_kandang').modal("hide");
									resetInput();

									getReport(page_number);
								}else{
									if(data.err == "kodecek")
										toastr.warning("Kode Kandang sudah digunakan kandang lain",'Peringatan');
									else if(data.err == "digitcek")
										toastr.warning("Digit Cek sudah digunakan kandang lain",'Peringatan');
									else
										toastr.warning("Penyimpanan Kandang dengan kode " + kodekandang + " gagal dilakukan",'Peringatan');
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
	var _form = $(this).closest('div.modal');
	kodefarm = $('#inp_namafarm').val();
	kodekandang = $('#inp_kodekandang').val();
	namakandang = $('#inp_namakandang').val();
	digitcek = $('#inp_digitcek').val();
	jmljantan = $('#inp_kapasitaskandangjantan').val();
	jmlbetina = $('#inp_kapasitaskandangbetina').val();
	luaskandangbetina = $('#inp_luaskandangbetina').val();
	luaskandangjantan = $('#inp_luaskandangjantan').val();
	tipekandang = $("#inp_tipekandang").val();
	tipelantai = $("#inp_tipelantai").val();

	var kapasitaskandang = parse_number($('#inp_kapasitaskandang').val());
	var luaskandang = parse_number($('#inp_luaskandang').val());
	var jmlsekat = parse_number($('#inp_jmlsekat').val());
	var noflok = $('#inp_noflok').val();
	/*
	if(empty(kodekandang) || empty(namakandang) || empty(jmljantan) || empty(jmlbetina)){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");

		return false;
	}
	*/
	var grupFarm = $('#inp_namafarm').find('option:selected').data('grupfarm');
	var adaKosong = 0;
	_form.find('input.required:visible').each(function(){
		if($.trim($(this).val()).length == 0 || $(this).val() <= 0){
			adaKosong++;
		}
	});

	/*
	if(empty(kodekandang) || empty(namakandang) || empty(jmljantan) || empty(jmlbetina)){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		return false;
	}
	*/
	if(adaKosong){
		bootbox.alert("Parameter data yang anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
		return false;
	}

	if($("#inp_statuskandang").is(':checked'))
		status = "A";
	else
		status = "N";
	var dataKirim = {};
	dataKirim['BRD'] = {
			kodefarm : kodefarm,
			kodekandang : kodekandang,
			namakandang : namakandang,
			digitcek : digitcek,
			jmljantan : jmljantan,
			jmlbetina : jmlbetina,
			luaskandangbetina : luaskandangbetina,
			luaskandangjantan : luaskandangjantan,
			tipekandang : tipekandang,
			tipelantai : tipelantai,
			status : status
	};
	dataKirim['BDY'] = {
			kodefarm : kodefarm,
			kodekandang : kodekandang,
			namakandang : namakandang,
			digitcek : digitcek,
			kapasitaskandang : kapasitaskandang,
			luaskandang : luaskandang,
			jmlsekat : jmlsekat,
			noflok : noflok,
			tipekandang : tipekandang,
			tipelantai : tipelantai,
			status : status
	};
	bootbox.dialog({
		message: "Apakah Anda yakin akan Mengubah data Kandang ini?",
		title: "",
		buttons: {
			main: {
				label: "Ya",
				className: "btn-primary",
				callback: function() {
					$.ajax({
						type:'POST',
						dataType: 'json',
						url : "master/kandang/update_kandang/",
						data: dataKirim[grupFarm]
					})
					.done(function(data){
						if(data.result == "success"){
							toastr.success("Perubahan data Kandang dengan kode " + kodekandang + " berhasil dilakukan",'Informasi');

							$('#modal_kandang').modal("hide");
							resetInput();

							getReport(page_number);
						}else{
							if(data.err == "kodecek")
								toastr.warning("Kode Kandang sudah digunakan kandang lain",'Peringatan');
							else if(data.err == "digitcek")
								toastr.warning("Digit Cek sudah digunakan kandang lain",'Peringatan');
							else
								toastr.warning("Perubahan data Kandang dengan kode " + kodekandang + " gagal dilakukan",'Peringatan');
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

$('#inp_kodekandang').keyup(function(){
	this.value = this.value.toUpperCase();
});

$('#inp_namakandang').keyup(function(){
	this.value = this.value.toUpperCase();
});

/*
* FUNCTION
*/

function resetInput(){
	$('#inp_namafarm :nth-child(1)').prop('selected', true);
	$('#inp_kodekandang').val('');
	$('#inp_namakandang').val('');
	$('#inp_digitcek').val('');
	$('#inp_kapasitaskandangbetina').val('');
	$('#inp_kapasitaskandangjantan').val('');
	$('#inp_luaskandangbetina').val('');
	$('#inp_luaskandangjantan').val('');
	$("#inp_tipekandang").val("O");
	$("#inp_tipelantai").val("L");
	$('#inp_statuskandang').prop('checked', true);
}

function goSearch(){
	page_number = 0;
	search = true;
	getReport(page_number);
}

function checkInput(){

	// kodekandang = $('#inp_kodekandang').val();
	// namakandang = $('#inp_namakandang').val();
	// kapasitaskandang = $('#inp_kapasitaskandang').val();

	// if(kodekandang != "" && namakandang != "" && kapasitaskandang != ""){
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
