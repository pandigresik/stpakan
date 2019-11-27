var selected_kandang = "";
var kandang_in_farm = new Array();

var arr_tara_berat 	= new Array();
var arr_tara_box 	= new Array();

var arr_ayam_jumlah = new Array();
var arr_ayam_tonase = new Array();

var n_row_per_kolom = 3;

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
months_id[0] = "Januari";
months_id[1] = "Februari";
months_id[2] = "Maret";
months_id[3] = "April";
months_id[4] = "Mei";
months_id[5] = "Juni";
months_id[6] = "Juli";
months_id[7] = "Agustus";
months_id[8] = "September";
months_id[9] = "Oktober";
months_id[10] = "Nopember";
months_id[11] = "Desember";

var months_short = new Array(12);
months_short[0] = "Jan";
months_short[1] = "Feb";
months_short[2] = "Mar";
months_short[3] = "Apr";
months_short[4] = "May";
months_short[5] = "Jun";
months_short[6] = "Jul";
months_short[7] = "Aug";
months_short[8] = "Sep";
months_short[9] = "Oct";
months_short[10] = "Nov";
months_short[11] = "Dec";

var months_id_short = new Array(12);
months_id_short[0] = "Jan";
months_id_short[1] = "Feb";
months_id_short[2] = "Mar";
months_id_short[3] = "Apr";
months_id_short[4] = "Mei";
months_id_short[5] = "Jun";
months_id_short[6] = "Jul";
months_id_short[7] = "Agt";
months_id_short[8] = "Sep";
months_id_short[9] = "Okt";
months_id_short[10] = "Nop";
months_id_short[11] = "Des";

$(document).ready(function () {
	selected_farm = $('#inp_farm').val();
	setInputKandang(selected_farm);
	
	$('.tgl_panen').datetimepicker({
		pickTime: false,
		format : "DD MMM YYYY"
	});
	
	$('.tgl_datang').datetimepicker({
		format : "DD MMM YYYY hh:mm A"
	});
	
	$('.tgl_mulai').datetimepicker({
		format : "DD MMM YYYY hh:mm A"
	});
	
	$('.tgl_selesai').datetimepicker({
		format : "DD MMM YYYY hh:mm A"
	});
});

/*Timbang Keranjang*/

$('.berat_tara').dblclick(function(e){
	e.stopPropagation();      //<-------stop the bubbling of the event here
	showInputTaraKeranjang(this);
});

$('.box_tara').dblclick(function(e){
	e.stopPropagation();      //<-------stop the bubbling of the event here
	showInputTaraKeranjang(this);
});

function setInputKandang(kode_farm){
	$.ajax({
		type:'POST',
		dataType: 'json',
		url : "riwayat_harian_kandang/riwayat_harian_kandang_bdy/get_kandang_farm/",
		data: {
			kode_farm:kode_farm
		}
	})
	.done(function(data){
		for(var i=0;i<data.length;i++){
			var obj = data[i];

			if(obj.id != selected_kandang){
				var valueToPush = new Array();
				valueToPush[0] = data[i].id;
				valueToPush[1] = data[i].name;
				valueToPush[2] = data[i].no_reg;
				kandang_in_farm.push(valueToPush);
			}
		}

		var $input = $('#inp_kandang');
		$input.typeahead({source:data,
					autoSelect: true});
		$input.change(function() {
			var current = $input.typeahead("getActive");
			if (current) {

				// Some item from your model is active!
				if (current.name == $input.val()) {
					var todayDbase = $('#inp_today').val();

					// This means the exact match is found. Use toLowerCase() if you want case insensitive match.
				} else {
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
}

function showInputTaraKeranjang(elm){
	var tr = $(elm).parent();
	var td_berat = $(tr).find('td').eq(1);
	var td_box = $(tr).find('td').eq(2);
	
	var is_inp_hide = $(td_berat).find('input').hasClass('hide');
	if(is_inp_hide){
		$(elm).parent().find('td').eq(3).find('div').removeClass('hide'); //menampilkan tombol kontrol
		
		//berat_tara
		$(td_berat).children('input').removeClass('hide'); //menampilkan input field
		$(td_berat).children('span').addClass('hide'); //hide value span
		
		//box_tara
		$(td_box).children('input').removeClass('hide'); //menampilkan input field
		$(td_box).children('span').addClass('hide'); //hide value 
		
		$(elm).children('input').focus(); //fokus ke input
	}
}

function simpanTaraKeranjang(elm){
	var tr = $(elm).parent().parent().parent();
	
	var lbl_tara_berat = $(tr).find('td').eq(1).children('span');
	var inp_tara_berat = $(tr).find('td').eq(1).find('input');
	
	var lbl_tara_box = $(tr).find('td').eq(2).children('span');
	var inp_tara_box = $(tr).find('td').eq(2).find('input');
	
	var tara_berat = $(inp_tara_berat).val();
	var tara_box = $(inp_tara_box).val();
	
	if(tara_berat == '')
		tara_berat = 0;
	
	if(tara_box == '')
		tara_box = 0;
	
	$(lbl_tara_berat).html(tara_berat);
	$(lbl_tara_box).html(tara_box);
	
	var index = 0;
	$('input[name^="berat_tara_keranjang"]').each(function() {
		index++;		
		//urutkan kembali nomor daftar tara keranjang
		$(this).parent().parent().find('td').eq(0).html(index);
	});
	
	var lbl_nomor = $(tr).find('td').eq(0).html();
	if(tara_berat > 0 && tara_box > 0 && lbl_nomor == index ){
		$(inp_tara_berat).addClass('hide');
		$(inp_tara_box).addClass('hide');
		
		$(lbl_tara_berat).removeClass('hide');
		$(lbl_tara_box).removeClass('hide');
		
		$(elm).parent().addClass('hide');
		
		hitung_tara_keranjang();
		
		var html = ''+
		'<tr>'+
		'	<td class="vert-lign" align="center">'+(index+1)+'</td>'+
		'	<td class="vert-align berat_tara" ondblclick="showInputTaraKeranjang(this)">'+
		'		<span class="berat_tara_lbl hide"></span>'+
		'		<input type="text" name="berat_tara_keranjang[]" style="text-align:center;" class="form-control input-sm" value=""/>'+
		'	</td>'+
		'	<td class="vert-align box_tara" ondblclick="showInputTaraKeranjang(this)">'+
		'		<span class="box_tara_lbl hide"></span>'+
		'		<input type="text" name="box_tara_keranjang[]" style="text-align:center" class="form-control input-sm" value=""/>'+
		'	</td>'+
		'	<td class="vert-align">'+
		'	 <div class="control">'+
		'		<button type="button" class="btn btn-primary btn-xs" onclick="simpanTaraKeranjang(this)">'+
		'			<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>'+
		'		</button>'+
		'		<button type="button" class="btn btn-danger btn-xs" onclick="batalTaraKeranjang(this)">'+
		'			<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>'+
		'		</button>'+
		'	 </div>'+
		'	</td>'+
		'</tr>';
		
		$(html).appendTo("#daftar_tara_keranjang > tbody");
	}else{
		$(lbl_tara_berat).addClass('hide');
		$(lbl_tara_box).addClass('hide');
		hitung_tara_keranjang();
	}
}

function batalTaraKeranjang(elm){
	var tr = $(elm).parent().parent().parent();
	
	var lbl_tara_berat = $(tr).find('td').eq(1).children('span');
	var inp_tara_berat = $(tr).find('td').eq(1).find('input');
	
	var lbl_tara_box = $(tr).find('td').eq(2).children('span');
	var inp_tara_box = $(tr).find('td').eq(2).find('input');
	
	var tara_berat = $(inp_tara_berat).val();
	var tara_box = $(inp_tara_box).val();
	
	if(tara_berat == '')
		tara_berat = 0;
	
	if(tara_box == '')
		tara_box = 0;
		
	var index = 0;
	$('input[name^="berat_tara_keranjang"]').each(function() {
		index++;		
		//urutkan kembali nomor daftar tara keranjang
		$(this).parent().parent().find('td').eq(0).html(index);
	});
	
	var lbl_nomor = $(tr).find('td').eq(0).html();
	
	$(lbl_tara_berat).val('0');
	$(lbl_tara_box).val('0');
	
	$(inp_tara_berat).val('0');
	$(inp_tara_box).val('0');
		
	if(index > 1 || lbl_nomor < index ){
		$(tr).remove();
	}
	
	$('input[name^="berat_tara_keranjang"]').each(function() {
		var temp_tr = $(this).parent().parent();
				
		var lbl_urut = $(temp_tr).find('td').eq(0).html();
		var lbl_tara_berat = $(temp_tr).find('td').eq(1).children('span');
		var inp_tara_berat = $(temp_tr).find('td').eq(1).find('input');
		
		var lbl_tara_box = $(temp_tr).find('td').eq(2).children('span');
		var inp_tara_box = $(temp_tr).find('td').eq(2).find('input');
		
		var div_kontrol = $(temp_tr).find('td').eq(3).children('div');
		
		if(lbl_urut == (index-1)){
			$(inp_tara_berat).removeClass('hide');
			$(inp_tara_box).removeClass('hide');
			
			$(lbl_tara_berat).addClass('hide');
			$(lbl_tara_box).addClass('hide');
			
			$(div_kontrol).removeClass('hide');
		}
	});
	
	hitung_tara_keranjang();
}

function hitung_tara_keranjang(){
	var val_berat = 0;
	var val_box = 0;
	$('input[name^="berat_tara_keranjang"]').each(function() {
		var value = ($(this).val() == '') ? 0 : $(this).val();
		val_berat += parseFloat(value);
	});
	
	$('input[name^="box_tara_keranjang"]').each(function() {
		var value = ($(this).val() == '') ? 0 : $(this).val();
		val_box += parseInt(value);
	});
	
	$('#total_tara_berat').html(Number(Math.round(val_berat * 1000) / 1000).toFixed(2));
	$('#total_tara_box').html(val_box);
}

/*Timbang Ayam*/
$('.jumlah_ayam').dblclick(function(e){
	e.stopPropagation();      //<-------stop the bubbling of the event here
	
	var span = $(this).find("span").attr("data-kolom");
	console.log(span);
});

$('.tonase_ayam').dblclick(function(e){
	e.stopPropagation();      //<-------stop the bubbling of the event here
	
	var span = $(this).find("span").attr("data-kolom");
	console.log(span);
});

function showInputTimbangAyam(elm){
	var tr = $(elm).parent();
	var td_jumlah = $(tr).find('td').eq(1);
	var td_berat = $(tr).find('td').eq(2);
	
	var is_inp_hide = $(td_jumlah).find('input').hasClass('hide');
	if(is_inp_hide){
		$(elm).parent().find('td').eq(3).find('div').removeClass('hide'); //menampilkan tombol kontrol
		
		//jumlah_ayam
		$(td_jumlah).children('input').removeClass('hide'); //menampilkan input field
		$(td_jumlah).children('span').addClass('hide'); //hide value span
		
		//berat_ayam
		$(td_berat).children('input').removeClass('hide'); //menampilkan input field
		$(td_berat).children('span').addClass('hide'); //hide value 
		
		$(elm).children('input').focus(); //fokus ke input
	}
}

function simpanJumlahAyam(elm, kolom){
	var tr = $(elm).parent().parent().parent();
	
	var lbl_ayam_jumlah = $(tr).find('td').eq(1).children('span');
	var inp_ayam_jumlah = $(tr).find('td').eq(1).find('input');
	
	var lbl_ayam_tonase = $(tr).find('td').eq(2).children('span');
	var inp_ayam_tonase = $(tr).find('td').eq(2).find('input');
	
	var ayam_jumlah = $(inp_ayam_jumlah).val();
	var ayam_tonase = $(inp_ayam_tonase).val();
	
	if(ayam_jumlah == '')
		ayam_jumlah = 0;
	
	if(ayam_tonase == '')
		ayam_tonase = 0;
	
	
	var index = 0;
	$('input[name^="jumlah_ayam'+kolom+'"]').each(function() {
		index++;		
		//urutkan kembali nomor daftar tara keranjang
		$(this).parent().parent().find('td').eq(0).html(index);
	});
	
	var lbl_nomor = $(tr).find('td').eq(0).html();
	if(ayam_jumlah > 0 && ayam_tonase > 0 && lbl_nomor == index ){
		//simpan sementara nilai pada Array
		arr_ayam_jumlah.push(ayam_jumlah);
		arr_ayam_tonase.push(ayam_tonase);
		
		refreshDaftarTimbang(arr_ayam_jumlah, arr_ayam_tonase);
		
		//end of Cara Baru
	}else{
		$(lbl_ayam_jumlah).addClass('hide');
		$(lbl_ayam_tonase).addClass('hide');
		hitung_timbangan_ayam(kolom);
	}
}

function refreshDaftarTimbang(arr_ayam_jumlah, arr_ayam_tonase){
	var temp_kolom = 1;
	var html_table = new Array();
	var html_open = '<div class="col-md-3">'+
					'	<table id="daftar_timbang_ayam'+temp_kolom+'" class="table table-bordered table-condensed table-striped">'+
					'		<thead>'+
					'			<tr>'+
					'				<th class="vert-align" style="width:50px;">No</th>'+
					'				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>'+
					'				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>'+
					'				<th class="vert-align" style="width:300px"></th>'+
					'			</tr>'+
					'		</thead>'+
					'		<tbody>';
	
	var html_content = "";
	for(var i=0; i<arr_ayam_jumlah.length; i++){
		var temp_ayam_jumlah = arr_ayam_jumlah[i];
		var temp_ayam_tonase = arr_ayam_tonase[i];
		
		html_content +=	'<tr>'+
				'	<td class="vert-align">'+(i+1)+'</td>'+
				'	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">'+
				'		<span data-kolom="'+temp_kolom+'" class="jumlah_ayam_lbl">'+temp_ayam_jumlah+'</span>'+
				'		<input type="text" name="jumlah_ayam'+temp_kolom+'[]" style="text-align:center;" class="form-control input-sm hide" value="'+temp_ayam_jumlah+'"/>'+
				'	</td>'+
				'	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">'+
				'		<span data-kolom="'+temp_kolom+'" class="tonase_ayam_lbl">'+temp_ayam_tonase+'</span>'+
				'		<input type="text" name="tonase_ayam'+temp_kolom+'[]" style="text-align:center" class="form-control input-sm hide" value="'+temp_ayam_tonase+'"/>'+
				'	</td>'+
				'	<td class="vert-align">'+
				'		<div class="control hide">'+
				'			<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, '+temp_kolom+')">'+
				'				<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>'+
				'			</button>'+
				'			<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, '+temp_kolom+')">'+
				'				<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>'+
				'			</button>'+
				'		</div>'+
				'	</td>'+
				'</tr>';
		
		
		if(((i+1) < (n_row_per_kolom * 4)) && ((i+1) % n_row_per_kolom) == 0){
			html_content +=	'</tbody>'+
					'		<thead>'+
					'			<tr>'+
					'				<th class="vert-align" style="width:50px">Total</th>'+
					'				<th class="vert-align" style="width:200px" id="total_jumlah_ayam'+temp_kolom+'"></th>'+
					'				<th class="vert-align" style="width:200px" id="total_tonase_ayam'+temp_kolom+'"></th>'+
					'				<th class="vert-align" style="width:200px"></th>'+
					'			</tr>'+
					'		</thead>'+
					'	</table>'+
					'</div>';
					
			temp_kolom ++; 
		
			html_content += 	'<div class="col-md-3">'+
					'	<table id="daftar_timbang_ayam'+temp_kolom+'" class="table table-bordered table-condensed table-striped">'+
					'		<thead>'+
					'			<tr>'+
					'				<th class="vert-align" style="width:50px;">No</th>'+
					'				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>'+
					'				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>'+
					'				<th class="vert-align" style="width:300px"></th>'+
					'			</tr>'+
					'		</thead>'+
					'		<tbody>';
		}
		
		if((i == arr_ayam_jumlah.length -1) && (i+1) < (n_row_per_kolom * 4)){
			html_content += '<tr>'+
					'	<td class="vert-align">'+(i+2)+'</td>'+
					'	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">'+
					'		<span data-kolom="'+temp_kolom+'" class="jumlah_ayam_lbl hide"></span>'+
					'		<input type="text" name="jumlah_ayam'+temp_kolom+'[]" style="text-align:center;" class="form-control input-sm" value=""/>'+
					'	</td>'+
					'	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">'+
					'		<span data-kolom="'+temp_kolom+'" class="tonase_ayam_lbl hide">'+temp_ayam_tonase+'</span>'+
					'		<input type="text" name="tonase_ayam'+temp_kolom+'[]" style="text-align:center" class="form-control input-sm" value=""/>'+
					'	</td>'+
					'	<td class="vert-align">'+
					'		<div class="control">'+
					'			<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, '+temp_kolom+')">'+
					'				<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>'+
					'			</button>'+
					'			<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, '+temp_kolom+')">'+
					'				<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>'+
					'			</button>'+
					'		</div>'+
					'	</td>'+
					'</tr>';
		}
	}
		
	var html_close ='</tbody>'+
					'		<thead>'+
					'			<tr>'+
					'				<th class="vert-align" style="width:50px">Total</th>'+
					'				<th class="vert-align" style="width:200px" id="total_jumlah_ayam'+temp_kolom+'"></th>'+
					'				<th class="vert-align" style="width:200px" id="total_tonase_ayam'+temp_kolom+'"></th>'+
					'				<th class="vert-align" style="width:200px"></th>'+
					'			</tr>'+
					'		</thead>'+
					'	</table>'+
					'</div>';
	
	html_table.push(html_open);
	html_table.push(html_content);
	html_table.push(html_close);
	
	$('#generate_this').html(html_table.join(''));
	
	while(temp_kolom > 0){
		hitung_timbangan_ayam(temp_kolom);
		temp_kolom--;
	}
}

function batalJumlahAyam(elm, kolom){
	var tr = $(elm).parent().parent().parent();
	
	var lbl_ayam_jumlah = $(tr).find('td').eq(1).children('span');
	var inp_ayam_jumlah = $(tr).find('td').eq(1).find('input');
	
	var lbl_ayam_tonase = $(tr).find('td').eq(2).children('span');
	var inp_ayam_tonase = $(tr).find('td').eq(2).find('input');
	
	var ayam_jumlah = $(inp_ayam_jumlah).val();
	var ayam_tonase = $(inp_ayam_tonase).val();
	
	if(ayam_jumlah == '')
		ayam_jumlah = 0;
	
	if(ayam_tonase == '')
		ayam_tonase = 0;
	
	//simpan sementara nilai pada Array
	arr_ayam_jumlah.pop(ayam_jumlah);
	arr_ayam_tonase.pop(ayam_tonase);
		
	var index = 0;
	$('input[name^="jumlah_ayam'+kolom+'"]').each(function() {
		index++;		
		//urutkan kembali nomor daftar tara keranjang
		$(this).parent().parent().find('td').eq(0).html(index);
	});
	
	var lbl_nomor = $(tr).find('td').eq(0).html();
	
	$(lbl_ayam_jumlah).val('0');
	$(lbl_ayam_tonase).val('0');
	
	$(inp_ayam_jumlah).val('0');
	$(inp_ayam_tonase).val('0');
	
	if(index > 1 || lbl_nomor < index ){
		$(tr).remove();
	}
	
	$('input[name^="jumlah_ayam'+kolom+'"]').each(function() {
		var temp_tr = $(this).parent().parent();
				
		var lbl_urut = $(temp_tr).find('td').eq(0).html();
		var lbl_ayam_jumlah = $(temp_tr).find('td').eq(1).children('span');
		var inp_ayam_jumlah = $(temp_tr).find('td').eq(1).find('input');
		
		var lbl_ayam_tonase = $(temp_tr).find('td').eq(2).children('span');
		var inp_ayam_tonase = $(temp_tr).find('td').eq(2).find('input');
		
		var div_kontrol = $(temp_tr).find('td').eq(3).children('div');
		
		if(lbl_urut == (index-1)){
			$(inp_ayam_jumlah).removeClass('hide');
			$(inp_ayam_tonase).removeClass('hide');
			
			$(lbl_ayam_jumlah).addClass('hide');
			$(lbl_ayam_tonase).addClass('hide');
			
			$(div_kontrol).removeClass('hide');
		}
	});
	
	hitung_timbangan_ayam(kolom);
}

function hitung_timbangan_ayam(kolom){
	var val_jumlah = 0;
	var val_berat = 0;
	$('input[name^="jumlah_ayam'+kolom+'"]').each(function() {
		var value = ($(this).val() == '') ? 0 : $(this).val();
		val_jumlah += parseFloat(value);
	});
	
	$('input[name^="tonase_ayam'+kolom+'"]').each(function() {
		var value = ($(this).val() == '') ? 0 : $(this).val();
		val_berat += parseInt(value);
	});
	
	$('#total_jumlah_ayam'+kolom).html(val_jumlah);
	$('#total_tonase_ayam'+kolom).html(Number(Math.round(val_berat * 1000) / 1000).toFixed(2));
}