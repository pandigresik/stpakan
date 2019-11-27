var search = false;
var page_number=0;
var total_page =null;
var selected_kavling = "";
var selected_tr = null;
var selected_td = null;

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
	getReport(page_number);
	
	$("#next").on("click", function(){
		page_number = (page_number+1);
		getReport(page_number);
	});
	
	$("#previous").on("click", function(){
		page_number = (page_number-1);
		getReport(page_number);
	});
	
	$( "#inp_tanggal" ).datepicker( { 
			dateFormat: 'dd M yy'
	});
	
	$( "#inp_tgladjustment" ).datepicker( { 
		dateFormat: 'dd M yy',
		setDate: new Date()
	});
	
	var pad = '00';
	var dt = new Date();
	var yy = dt.getFullYear();
	var mm = months_id[dt.getMonth()];
	var dd = (pad + dt.getDay()).slice(-pad.length);
	
	$( "#inp_tgladjustment" ).val(dd + ' ' + mm + ' ' + yy);
});

function getReport(page_number){
	if(page_number==0){
		$("#previous").prop('disabled', true);}
	else{
		$("#previous").prop('disabled', false);}
		
	if(page_number==(total_page-1) || empty(total_page)){
		$("#next").prop('disabled', true);}
	else{
		$("#next").prop('disabled', false);}
	
	$("#page_number").text(page_number+1);
	
	var noadjustment = $('#q_jenisbarang').val();
	var tgl = $('#q_tanggal').val();
	var namafarm = $('#q_namafarm').val();
	var tipe = $('#q_tipe').val();
	var alasan = $('#q_alasan').val();
	var tanggal = "";
	
	if(!empty(tgl)){
		var pad = '00';
		var tgl_arr = tgl.split(" "); 
		var index = months_id.indexOf(tgl_arr[1]);
		var tahun = parseInt(tgl_arr[2]);
		var bulan = parseInt(index);
		var hari = parseInt(tgl_arr[0]);
		
		var bulan_pad = (pad + bulan).slice(-pad.length);
		var hari_pad = (pad + hari).slice(-pad.length);
		tanggal = tahun+'-'+bulan_pad+'-'+hari_pad;
	}
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "adjustment/get_pagination/",
		data: {
			noadjustment : noadjustment,
			tanggal : tanggal,
			namafarm : namafarm,
			tipe : tipe,
			alasan : alasan,
			page_number : page_number,
			search : search
		}
	})
	.done(function(data){
		$("tbody", "#tb_adjustment").html("");
		
		window.mydata = data;
		
		if(!empty(mydata.length)){
			if(mydata.length > 0){
				total_page= mydata[0].TotalRows;
				$("#total_page").text(total_page);
				var record_par_page = mydata[0].Rows;
				
				$.each(record_par_page, function (key, data) {
					var html = new Array();
					html.push(''+
						'<tr>' +
						'<td>' + data.row + '<td>' +
						'<td>' + data.no_adjustment + '<td>' +
						'<td>' + data.tgl_adjustment_formated+ '<td>' +
						'<td>' + data.nama_farm + '<td>' +
						'<td>' + data.tipe_adjustment_desc+ '<td>' +
						'<td>' + data.alasan_adjustment+ '<td>' +
						'</tr>'
					);
					
					$("tbody", "#tb_adjustment").html(html.join(''));
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

function tambahRow(elm){
	html_btn = ''+
		'<button type="button" data-toggle="tooltip" onclick="hapusRow(this)" title="Hapus" class="btn btn-sm btn-primary">'+
		'	<i class="glyphicon glyphicon-minus-sign"></i>'+
		'</button>';
		
	html = ''+
	'<tr>'+
	'	<td>'+
	'		<div class="input-group">'+
	'			<input type="text" class="form-control input-sm" name="kodeKavling[]" disabled>'+
	'			<span class="input-group-btn">'+
	'				<button class="btn btn-sm btn-default" onclick="showBrowseKavling(this)" type="button">...</button>'+
	'			</span>'+
	'		</div>'+
	'	</td>'+
	'	<td>'+
	'		<input type="hidden" name="kodekandang[]" class="form-control input-sm"/>'+
	'		<select class="form-control input-sm multicolumn" name="kodeBarang[]" onchange="getDetailBarang(this)"></select>'+
	'	</td>'+
	'	<td><input type="hidden" name="jk[]" class="form-control input-sm"/></td>'+
	'	<td></td>'+
	'	<td><input type="text" name="jml_awal[]" class="form-control input-sm" disabled/></td>'+
	'	<td>'+
	'		<input type="text" class="form-control input-sm" name="jmlAdjustment[]" onkeyup="cekNumerik(this)">'+
	'	</td>'+
	'	<td><input type="text" name="jml_akhir[]" class="form-control input-sm" disabled/></td>'+
	'	<td>'+
	'		<button type="button" data-toggle="tooltip" onclick="tambahRow(this)" title="Tambah" class="btn btn-sm btn-primary">'+
	'		<i class="glyphicon glyphicon-plus-sign"></i>'+
	'		</button>'+
	'	</td>'+
	'</tr>';
			
	var td = $(elm).parent();
	td.html(html_btn);
	
	$(html).appendTo("#tb_adjustment_input > tbody");
}

function hapusRow(elm){
	var td = $(elm).parent().parent();
	td.remove();
}

function showBrowseKavling(elm){
	var td = $(elm).parent().parent();
	var tr = $(td).parent().parent();
	selected_tr = tr; 
	selected_td = td; 
	
	kode_farm = 'SSG5';
	$.ajax({
		type : "POST",
		url : "penerimaan_pakan/transaksi/layout_kavling",
		data : {
			kode_farm : kode_farm
		},
		success : function(data) {
			var box = bootbox.dialog({
				title : "Layout Kavling",
				className : "very-large",
				message : data,
				buttons : {
					danger : {
						label : "Keluar",
						className : "btn-danger",
						callback : function() {
							return true;
						}
					},
				}
			});

			$('.bootbox-body').find('table:first-child').css('border','none');
			$('.bootbox-body').find('table:first-child thead tr th.no-border').css('border','none');
			$('.bootbox-body').find('table.tbl-layout-kavling th').css('border-color','black');
			$('.bootbox-body').find('table.tbl-layout-kavling td').css('border-color','black');
		}
	});
	
}

function getDetailBarang(elm){
	var td = $(elm).parent();
	var tr = $(td).parent();
	
	var kode_kavling = $(tr).find('td').eq(0).children().find('input').val();
	var kode_kandang = $(td).find('input').val();
	var kode_barang = $(elm).val();
	var tipe = $('#inp_tipe').val();
	
	var txt_arr = $(elm).find('option:selected').text().split(' ');
	var jenis_kelamin = txt_arr[(txt_arr.length)-1];
		
	if(!empty(kode_barang)){
		$.ajax({
			type : "POST",
			dataType: 'json',
			url : "adjustment/get_databarang",
			data : {
				kode_kavling : kode_kavling,
				kode_barang : kode_barang,
				no_reg : kode_kandang,
				tipe : tipe,
				jenis_kelamin : jenis_kelamin
			},
			success : function(data) {
				$(tr).find('td').eq(2).html(data.nama_barang + '<input type="hidden" name="jk[]" class="form-control input-sm" value="' + jenis_kelamin + '"/>');
				$(tr).find('td').eq(3).html(data.bentuk_barang);
				$(tr).find('td').eq(4).find('input').val(data.jumlah_awal);
				$(tr).find('td').eq(6).find('input').val(data.jumlah_awal);
			}
		});
	}
}

function selected(e) {
	var kode_kavling = $(e).attr('data-no-kavling');
	var nama_kandang = $(e).find('div.nama-kandang').text();
	var no_reg = $(e).attr('data-no-reg');
	
	var tr = $(e).parent().parent().parent();
	var kodebarang = $(selected_tr).find('td').eq(1).find('select.multicolumn');
	var kodekandang = $(selected_tr).find('td').eq(1).find('input');
	var namabarang = $(selected_tr).find('td').eq(2);
	var bentuk = $(selected_tr).find('td').eq(3);
	var jumlahawal = $(selected_tr).find('td').eq(4).find('input');
	var jumlahadj = $(selected_tr).find('td').eq(5).find('input');
	var jumlahakhir = $(selected_tr).find('td').eq(6).find('input');
	
	if(empty(nama_kandang))
		toastr.warning("Kavling masih kosong",'Peringatan');
	else{
		$(selected_td).find('input').val(kode_kavling);
		$('.bootbox').modal('hide');
	
		$.ajax({
			type : "POST",
			url : "adjustment/get_kavlingbarang",
			data : {
				kode_kavling : kode_kavling,
				no_reg : no_reg
			},
			success : function(data) {
				var items = $.parseJSON(data);
								
				var html = new Array();
				html.push('<option value="">Pilihan : </option>');
				html.push('<option class="header">Kode Barang +Nama Barang +Jenis Kelamin</option>');
				for(var i=0;i<items.length;i++){
					html.push('<option value="' + items[i]["kode_barang"] + '">' + items[i]["kode_barang"] + ' + ' + items[i]["nama_barang"] + ' + ' + items[i]["jenis_kelamin"] + '</option>');
				}
				
				$(kodebarang).html(html.join(''));
				$('select.multicolumn').combomulticolumn();
				$(kodekandang).val(no_reg);
				$(namabarang).html('<input type="hidden" name="jk[]" class="form-control input-sm"/>');
				$(bentuk).html('');
				$(jumlahawal).val('');
				$(jumlahadj).val('');
				$(jumlahakhir).val('');
			}
		});
	}
}

function detail_selected(){
	
}

function cekNumerik(field){
	var re = /^[0-9-'.'-',']*$/;
	if (!re.test(field.value)) {
		field.value = field.value.replace(/[^0-9-'.'-',']/g,"");
	} 
	
	var td = $(field).parent();
	var tr = $(td).parent();
	
	var jml_awal = parseInt($(tr).find('td').eq(4).find('input').val().trim());
	var jml_akhir = jml_awal + parseInt($(field).val());
	
	$(tr).find('td').eq(6).find('input').val(jml_akhir);
}

$('#btnTambah').click(function(){
	$('#modal_adjustment').modal('show');
});

$('#btnSimpan').click(function(){
	var kode_farm,
		no_adjustment,
		tgl_adjusment,
		tipe_adjustment,
		alasan_adjustment,
		keterangan1;
	
	var kode_kavling = new Array();
	var kode_kandang = new Array();
	var kode_barang = new Array();
	var jenis_kelamin = new Array();
	var jml_awal = new Array();
	var jml_adjustment = new Array();
	var jml_akhir = new Array();

	
	kode_farm = $('#inp_kodefarm').val();
	no_adjustment = $('#inp_noadjustment').val();
	
	var pad = '00';
	var tglAdj_arr = ($('#inp_tgladjustment').val()).split(' ');
	var ddAdj = parseInt(tglAdj_arr[0]);
	var mmAdj = (months.indexOf(tglAdj_arr[1]) >= 0) ? months.indexOf(tglAdj_arr[1]) : months_id.indexOf(tglAdj_arr[1]);
	var yyAdj = parseInt(tglAdj_arr[2]);
	
	tgl_adjusment = yyAdj + '-' + (pad + mmAdj).slice(-pad.length) + '-' + (pad + ddAdj).slice(-pad.length);
	tipe_adjustment = $('#inp_tipe').val();
	alasan_adjustment = $('textarea#inp_alasanadj').val();
	keterangan1 = $('#inp_referensi').val();
	
	$('input[name^="kodeKavling"]').each(function() {
		kode_kavling.push($(this).val());
	});
	$('select[name^="kodeBarang"]').each(function() {
		kode_barang.push($(this).val());
	});
	$('input[name^="kodekandang"]').each(function() {
		kode_kandang.push($(this).val());
	});
	$('input[name^="jk"]').each(function() {
		jenis_kelamin.push($(this).val());
	});
	$('input[name^="jml_awal"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		jml_awal.push(jml);
	});
	$('input[name^="jmlAdjustment"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		jml_adjustment.push(jml);
	});
	$('input[name^="jml_akhir"]').each(function() {
		var jml = (empty($(this).val())) ? 0 : $(this).val();
		jml_akhir.push(jml);
	});
	
	
	
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "adjustment/simpan/",
		data: {
			kode_farm : kode_farm,
			no_adjustment : no_adjustment,
			tgl_adjusment : tgl_adjusment,
			tipe_adjustment : tipe_adjustment,
			alasan_adjustment : alasan_adjustment,
			keterangan1 : keterangan1,
			kode_kavling : kode_kavling,
			kode_barang : kode_barang,
			kode_kandang : kode_kandang,
			jenis_kelamin : jenis_kelamin,
			jml_awal : jml_awal,
			jml_adjustment : jml_adjustment,
			jml_akhir : jml_akhir
		}
	})
	.done(function(data){
		
	})
	.fail(function(reason){
		console.info(reason);
	})
	.then(function(data){
	});
	
	
	
	
	
	
});