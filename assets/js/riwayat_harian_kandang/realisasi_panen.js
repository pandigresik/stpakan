var selected_kandang = "";
var selected_noreg = "";
var selected_tgl_doc_in = "";
var selected_row_input = null;

var selected_real_jml = "";
var selected_real_brt = "";

var temp_available_do = new Array();
var mode = "input";

var kandang_in_farm = new Array();

var arr_tara_berat = new Array();
var arr_tara_box = new Array();

var arr_ayam_jumlah = new Array();
var arr_ayam_tonase = new Array();

var n_row_per_kolom = 15;

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

var selected_umur_panen = "";
var selected_do = "";
var selected_kode_pelanggan = "";
var selected_nama_pelanggan = "";
var selected_jumlah = "";
var selected_berat = "";

$(document).ready(function() {
    selected_farm = $('#inp_farm').val();
    setInputKandang(selected_farm);

    $('#panen_final > tbody').hide();
    reset_input();
});

/*Kontrol Grid*/
$(".tgl_panen").on("dp.change", function(e) {
    var dateDocIn = $("#inp_doc_in").val();
    var datePanen = $(this).children('input').val();

    var date1 = new Date(dateDocIn);
    var date2 = new Date(datePanen);
    var one_day = 1000 * 60 * 60 * 24;
    var ddiff = Math.ceil((date2.getTime() - date1.getTime()) / (one_day));

    var td_umur = $(this).parent().parent().find('td').eq(1);

    selected_umur_panen = ddiff;
    $(td_umur).html(ddiff);
});

function hitung_umur_panen(dateDocIn, datePanen) {
    var date1 = new Date(dateDocIn);
    var date2 = new Date(datePanen);
    var one_day = 1000 * 60 * 60 * 24;
    var ddiff = Math.ceil((date2.getTime() - date1.getTime()) / (one_day));

    return ddiff;
}

function select_tgl_panen(elm) {
    var dateDocIn = $("#inp_doc_in").val();
    var datePanen = $(elm).children('input').val();

    var ddiff = hitung_umur_panen(dateDocIn, datePanen);
    var td_umur = $(elm).parent().parent().find('td').eq(1);

    selected_umur_panen = ddiff;
    $(td_umur).html(ddiff);
}

function refresh_available_do(no_reg = null, tgl_panen = null, sj_panen = null) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/get_available_do/",
            data: {
                no_reg: no_reg
            }
        })
        .done(function(data) {
            var html_option = new Array();
            var attr = "";

            if (!empty(tgl_panen)) {
                attr = ' data-tgl_panen="' + tgl_panen + '" data-sj="' + sj_panen + '"';
            }
            html_option.push('<option ' + attr + ' value=""></option>');
            var _berat_max;
            $.each(data.do, function(key, value) {
                temp_available_do.push(value);
                _berat_max = value.BERAT;
                if (data.do_nyeser[value.NO_DO] !== undefined) {
                    _berat_max = data.do_nyeser[value.NO_DO];
                }
                var html = '<option data-no_sj="' + value.NO_SJ + '" data-kode_plg="' + value.KODE_PELANGGAN + '" data-nama_pelanggan="' + value.NAMA_PELANGGAN + '" data-max_berat_rit="' + value.MAX_RIT + '" data-berat_max="' + _berat_max + '" data-berat="' + value.BERAT + '" data-jumlah="' + value.JUMLAH + '" data-tgl_panen="' + value.TGL_PANEN + '" value="' + value.NO_DO + '">' + value.NO_DO + '</option>';
                html_option.push(html);
            });

            $("#inp_no_do").html(html_option.join(''));
            $(".inp_no_do").html(html_option.join(''));
        })
        .fail(function(reason) {
            //console.info(reason);
        })
        .then(function(data) {});
}

function refresh_tgl_entry() {
    var pad = '00';
    var todayDbase = $('#inp_today').val();
    var todayDbase_arr = todayDbase.split('-');
    var todayDbase_hari = parseInt(todayDbase_arr[2]);
    var todayDbase_bulan = parseInt(todayDbase_arr[1]);
    var todayDbase_tahun = parseInt(todayDbase_arr[0]);
    var todayDate = new Date(todayDbase_tahun + "-" + (pad + (todayDbase_bulan).toString()).slice(-pad.length) + "-" + (pad + (todayDbase_hari).toString()).slice(-pad.length));
    var day = todayDate.getDate();
    var monthIndex = todayDate.getMonth();
    var year = todayDate.getFullYear();
    $("#inp_tgl_buat").val(day + ' ' + months[monthIndex] + ' ' + year);
    $("#span_tgl_buat").html(day + ' ' + months[monthIndex] + ' ' + year);
}

function pilih_do(elm) {
    var tr = $(elm).parent().parent();
    var td_tgl_panen = $(tr).find('td').eq(0);
    var td_umur_panen = $(tr).find('td').eq(1);
    var td_do_berat = $(tr).find('td').eq(3);
    var td_do_jumlah = $(tr).find('td').eq(4);
    var td_pelanggan = $(tr).find('td').eq(5);
    var no_sj = $(tr).find('td').eq(6);

    selected_do = $(elm).find('option:selected').val();
    selected_kode_pelanggan = "";
    selected_nama_pelanggan = "";
    selected_jumlah = "";
    selected_berat = "";
    selected_tgl_panen = "";

    if ($(elm).find('option:selected').val() == "") {
        $(td_do_berat).find('span').html('');
        $(td_do_berat).find('input').val('');
        $(td_do_jumlah).find('span').html('');
        $(td_do_jumlah).find('input').val('');
        $(td_pelanggan).find('span.pelanggan').html('');
        $(td_pelanggan).find('button').addClass('hide');
        $(td_pelanggan).find('input').val('');

        if ($(elm).hasClass('inp_no_do')) {

            selected_tgl_panen = $(elm).find('option:selected').data('tgl_panen');

            var ddiff = hitung_umur_panen($("#inp_doc_in").val(), selected_tgl_panen);

            var temp_tgl_arr = selected_tgl_panen.split('-');
            var dd = parseInt(temp_tgl_arr[2]);
            var mm = months_short[parseInt(temp_tgl_arr[1]) - 1];
            var yy = parseInt(temp_tgl_arr[0]);
            tgl_panen = dd + ' ' + mm + ' ' + yy;

            $(td_tgl_panen).find('span').html(tgl_panen);
            $(td_tgl_panen).find('input').val(tgl_panen);

            $(td_umur_panen).find('span').html(ddiff);
            $(td_umur_panen).find('input').val(ddiff);
        } else {
            $('.tgl_panen').data("DateTimePicker").setDate(new Date());
            $('.tgl_panen').data("DateTimePicker").enable();
        }
    } else {
        selected_kode_pelanggan = $(elm).find('option:selected').data('kode_plg');
        selected_nama_pelanggan = $(elm).find('option:selected').data('nama_pelanggan');
        selected_jumlah = $(elm).find('option:selected').data('jumlah');
        selected_berat = $(elm).find('option:selected').data('berat');
        selected_tgl_panen = $(elm).find('option:selected').data('tgl_panen');

        $(td_do_berat).find('span').html(selected_berat);
        $(td_do_berat).find('input').val(selected_berat);
        $(td_do_jumlah).find('span').html(selected_jumlah);
        $(td_do_jumlah).find('input').val(selected_jumlah);
        $(td_pelanggan).find('span.pelanggan').html(selected_nama_pelanggan + "&nbsp;&nbsp;&nbsp;");
        $(td_pelanggan).find('input').val(selected_nama_pelanggan);
        $(td_pelanggan).find('button').removeClass('hide');
        $(no_sj).find('span').html($(elm).find('option:selected').data('no_sj'));
        tr.find('input[name ^=no_sj]').val($(elm).find('option:selected').data('no_sj'));

        if ($(elm).hasClass('inp_no_do')) {

            var ddiff = hitung_umur_panen($("#inp_doc_in").val(), selected_tgl_panen);
            var dt = new Date(selected_tgl_panen);

            var temp_tgl_arr = selected_tgl_panen.split('-');
            var dd = parseInt(temp_tgl_arr[2]);
            var mm = months_short[parseInt(temp_tgl_arr[1]) - 1];
            var yy = parseInt(temp_tgl_arr[0]);
            tgl_panen = dd + ' ' + mm + ' ' + yy;

            $(td_tgl_panen).find('span').html(tgl_panen);
            $(td_tgl_panen).find('input').val(tgl_panen);
            // console.log($(td_tgl_panen).find('span'));

            $(td_umur_panen).find('span').html(ddiff);
            $(td_umur_panen).find('input').val(ddiff);
        } else {
            var temp_tgl_arr = selected_tgl_panen.split('-');
            var dd = parseInt(temp_tgl_arr[2]);
            var mm = months_short[parseInt(temp_tgl_arr[1]) - 1];
            var yy = parseInt(temp_tgl_arr[0]);
            tgl_panen = dd + ' ' + mm + ' ' + yy;

            $(td_tgl_panen).find('span').html(tgl_panen);

            $('.tgl_panen').data("DateTimePicker").setDate(new Date(selected_tgl_panen));
            $('.tgl_panen').data("DateTimePicker").disable();
        }
    }
};


function setInputKandang(kode_farm) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/get_kandang_farm/",
            data: {
                kode_farm: kode_farm
            }
        })
        .done(function(data) {
            for (var i = 0; i < data.length; i++) {
                var obj = data[i];

                if (obj.id != selected_kandang) {
                    var valueToPush = new Array();
                    valueToPush[0] = data[i].id;
                    valueToPush[1] = data[i].name;
                    valueToPush[2] = data[i].no_reg;
                    kandang_in_farm.push(valueToPush);
                }
            }

            var $input = $('#inp_kandang');
            $input.typeahead({
                source: data,
                autoSelect: true
            });
            $input.change(function() {
                var current = $input.typeahead("getActive");
                if (current) {

                    // Some item from your model is active!
                    if (current.name == $input.val()) {
                        selected_kandang = current.id;
                        selected_noreg = current.no_reg;
                        selected_tgl_doc_in = current.tgl_doc_in;

                        tgl_temp = selected_tgl_doc_in.split("-");
                        ddDocIn = parseInt(tgl_temp[2]);
                        mmDocIn = parseInt(tgl_temp[1]);
                        yyDocIn = parseInt(tgl_temp[0]);

                        $('#inp_flock').val(current.flok_bdy);
                        $('#inp_doc_in').val(ddDocIn + ' ' + months[mmDocIn - 1] + ' ' + yyDocIn);

                        initializeData();

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
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

function initializeData(is_view = true) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/get_realisasi_panen/",
            data: {
                no_reg: selected_noreg
            }
        })
        .done(function(data) {
            var panen = data.panen;

            var tr_html = '' +
                '<tr id="row_input">' +
                '<td class="vert-align" style="widtd:200px">' +
                '	<div class="input-group date tgl_panen hide" onchange="select_tgl_panen(this)">' +
                '		<input type="text" name="tgl_panen" id="inp_tgl_panen" style="width:120px;" class="form-control disabled" readonly />' +
                '		<span class="input-group-addon">' +
                '			<span class="glyphicon glyphicon-calendar"></span>' +
                '		</span>' +
                '	</div>' +
                '	<span></span>' +
                '</td>' +
                '<td class="vert-align" style="widtd:200px"></td>' +
                '<td class="vert-align" style="widtd:200px">' +
                '	<select name="no_do" id="inp_no_do" class="form-control" style="width:150px;" onchange="pilih_do(this)">' +
                '		<option value=""></option>' +
                '	</select>' +
                '</td>' +
                '<td class="vert-align" style="widtd:200px">' +
                '	<span></span>' +
                '	<input type="hidden" name="tonase_do[]" class="tonase_do"/>' +
                '</td>' +
                '<td class="vert-align" style="widtd:200px">' +
                '	<span></span>' +
                '	<input type="hidden" name="berat_do[]" class="berat_do"/>' +
                '</td>' +
                '<td class="vert-align" style="widtd:200px">' +
                '	<span class="pelanggan"></span>' +
                '	<input type="hidden" name="pelanggan[]" class="pelanggan"/>' +
                '</td>' +
                '<td class="vert-align" style="widtd:200px"><input type="text" name="no_sj[]" id="inp_no_sj_01" style="width:100px;" class="form-control no_sj hide" onchange="cek_sj(this)"/><span></span></td>' +
                '<td class="vert-align" style="widtd:200px"><input type="text" name="tonase_realisasi[]" id="inp_tonase_realisasi" style="width:100px;" class="form-control tonase_realisasi" onkeyup="cekBeratRataTonase(this)"/></td>' +
                '<td class="vert-align" style="widtd:200px"><input type="text" name="jumlah_realisasi[]" id="inp_jumlah_realisasi" style="width:100px;" class="form-control jumlah_realisasi" onkeyup="cekBeratRataJumlah(this)"/></td>' +
                '<td class="vert-align" style="widtd:200px"></td>' +
                '<td class="vert-align" style="widtd:300px">' +
                '	<div class="input-group date tgl_datang">' +
                '		<input type="text" name="tgl_datang" id="inp_tgl_datang" style="width:200px;" class="form-control disabled" readonly />' +
                '		<span class="input-group-addon">' +
                '			<span class="glyphicon glyphicon-calendar"></span>' +
                '		</span>' +
                '	</div>' +
                '</td>' +
                '<td class="vert-align" style="widtd:300px">' +
                '	<div class="input-group date tgl_mulai">' +
                '		<input type="text" name="tgl_mulai" id="inp_tgl_mulai" style="width:200px;" class="form-control disabled" readonly />' +
                '		<span class="input-group-addon">' +
                '			<span class="glyphicon glyphicon-calendar"></span>' +
                '		</span>' +
                '	</div>' +
                '</td>' +
                '<td class="vert-align" style="widtd:300px">' +
                '	<div class="input-group date tgl_selesai">' +
                '		<input type="text" name="tgl_selesai" id="inp_tgl_selesai" style="width:200px;" class="form-control disabled" readonly />' +
                '		<span class="input-group-addon">' +
                '			<span class="glyphicon glyphicon-calendar"></span>' +
                '		</span>' +
                '	</div>' +
                '</td>' +
                '<td class="vert-align" style="widtd:200px"><span id="span_tgl_buat"></span><input type="hidden" id="inp_tgl_buat" value=""/></td>' +
                '</tr>';

            var level_user = $('#inp_level_user').val();
            if (level_user == "AGF") {
                $('#panen_final > tbody').html(tr_html);

                $('.tgl_panen').datetimepicker({
                    pickTime: false,
                    format: "DD MMM YYYY"
                });

                $('.tgl_datang').datetimepicker({
                    format: "DD MMM YYYY HH:mm"
                });

                $('.tgl_mulai').datetimepicker({
                    format: "DD MMM YYYY HH:mm"
                });

                $('.tgl_selesai').datetimepicker({
                    format: "DD MMM YYYY HH:mm"
                });

                $('.tgl_panen').data("DateTimePicker").setMinDate(new Date(selected_tgl_doc_in));
                $('.tgl_panen').data("DateTimePicker").setDate(new Date());
            } else {
                var tr_html = '' +
                    '<tr id="row_input">' +
                    '<td class="vert-align" style="widtd:200px">&nbsp;</td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '<td class="vert-align" style="widtd:300px"></td>' +
                    '<td class="vert-align" style="widtd:300px"></td>' +
                    '<td class="vert-align" style="widtd:300px"></td>' +
                    '<td class="vert-align" style="widtd:200px"></td>' +
                    '</tr>';
                $('#panen_final > tbody').html(tr_html);
            }
            $('#inp_no_sj').attr('disabled', true);

            refresh_available_do(selected_noreg);
            refresh_tgl_entry();

            if (panen.length) {

                var html_arr = new Array();
                var j = 0;
                var index_do_empty = 0;
                var tgl_panen_do = "";
                var sj_panen_do = "";
                var noreg_panen_do = "";
                for (var i = 0; i < panen.length; i++) {
                    var obj = panen[i];

                    var flag_not_finished = "";
                    var v_do = empty(obj.no_do) ? "" : obj.no_do;
                    var v_tgl_panen = empty(obj.tgl_panen_format) ? "" : obj.tgl_panen_format;
                    var v_umur_panen = empty(obj.umur_panen) ? "" : obj.umur_panen;
                    var v_no_do = empty(obj.no_do) ? "" : obj.no_do;
                    var v_ton_do = empty(obj.berat_do) ? "" : obj.berat_do;
                    var v_jml_do = empty(obj.jumlah_do) ? "" : obj.jumlah_do;
                    var v_plg_do = empty(obj.nama_pelanggan) ? "" : obj.nama_pelanggan;
                    var v_brt_akhir = empty(obj.berat_akhir) ? "" : obj.berat_akhir;
                    var v_jml_akhir = empty(obj.jumlah_akhir) ? "" : obj.jumlah_akhir;
                    var v_brt_aktual = empty(obj.berat_akhir) ? "" : obj.berat_aktual;
                    var v_jml_aktual = empty(obj.jumlah_akhir) ? "" : obj.jumlah_aktual;
                    var v_brt_timbang = empty(obj.berat_timbang) ? "" : obj.berat_timbang;
                    var v_jml_timbang = empty(obj.jumlah_timbang) ? "" : obj.jumlah_timbang;

                    var tooltip_berat = '';
                    var tooltip_jumlah = '';

                    if (v_brt_timbang > 0 && v_jml_timbang > 0 && ((v_brt_timbang != v_brt_aktual) || (v_jml_timbang != v_jml_aktual))) {
                        tooltip_berat = ' title="' + v_brt_timbang + '" ';
                        tooltip_jumlah = ' title="' + v_jml_timbang + '" ';
                    }

                    tgl_panen_do = obj.tgl_panen;
                    sj_panen_do = obj.no_surat_jalan;
                    noreg_panen_do = obj.no_reg;

                    if (level_user != "AGF" && empty(v_no_do)) {
                        v_tgl_panen = '<span>' + obj.tgl_panen_format + '</span>' +
                            '<input type="hidden" name="tgl_panen[]" class="tgl_panen" value="' + obj.tgl_panen_format + '"/>';

                        v_umur_panen = '<span>' + obj.umur_panen + '</span>' +
                            '<input type="hidden" name="umur_panen[]" class="umur_panen" value="' + obj.umur_panen + '"/>';

                        var disabled = (index_do_empty > 0) ? "disabled" : "";
                        disabled = (is_view) ? disabled : "disabled";

                        v_no_do = '	<select name="no_do[]" ' + disabled + ' class="form-control inp_no_do" style="width:150px;" onchange="pilih_do(this)">' +
                            '		<option value=""></option>' +
                            '	</select>';

                        v_ton_do = '<span></span>' +
                            '<input type="hidden" name="tonase_do[]" class="tonase_do"/>';


                        v_jml_do = '<span></span>' +
                            '<input type="hidden" name="berat_do[]" class="berat_do"/>';

                        v_plg_do = '<span class="pelanggan"></span>' +
                            '<button type="button" class="btn btn-primary btn-xs hide" onclick="update_do(this)">' +
                            '	<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                            '</button>'
                        '<input type="hidden" name="pelanggan[]" class="pelanggan"/>';

                        index_do_empty++;
                    }

                    if (level_user != "AGF" && !empty(obj.no_do)) {
                        if (empty(obj.berat_akhir))
                            flag_not_finished = "highlight-red";
                    }

                    var html = '' +
                        '<tr class="' + flag_not_finished + '">' +
                        '	<td class="vert-align" style="widtd:200px" data-berat_akhir="' + v_brt_akhir + '" data-no_do="' + v_do + '">' + v_tgl_panen + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + v_umur_panen + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + v_no_do + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + v_ton_do + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + v_jml_do + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + v_plg_do + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + obj.no_surat_jalan + '</td>' +
                        '	<td class="vert-align" style="widtd:200px" ' + tooltip_berat + '>' + obj.berat_aktual + '</td>' +
                        '	<td class="vert-align" style="widtd:200px" ' + tooltip_jumlah + '>' + obj.jumlah_aktual + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + obj.berat_badan_rata2 + '</td>' +
                        '	<td class="vert-align" style="widtd:300px">' + obj.tgl_datang_format + '</td>' +
                        '	<td class="vert-align" style="widtd:300px">' + obj.tgl_mulai_format + '</td>' +
                        '	<td class="vert-align" style="widtd:300px">' + obj.tgl_selesai_format + '</td>' +
                        '	<td class="vert-align" style="widtd:200px">' + obj.tgl_buat_format + '</td>' +
                        '</tr>';

                    html_arr[j] = html;
                    j++;

                    $(html).insertBefore('#row_input');
                }
                refresh_available_do(selected_noreg, tgl_panen_do, sj_panen_do);

            }

            $('#panen_final > tbody').show();
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

/*Timbang Keranjang*/

$('.berat_tara').dblclick(function(e) {
    e.stopPropagation(); //<-------stop the bubbling of the event here
    showInputTaraKeranjang(this);
});

$('.box_tara').dblclick(function(e) {
    e.stopPropagation(); //<-------stop the bubbling of the event here
    showInputTaraKeranjang(this);
});

function showInputTaraKeranjang(elm) {
    var tr = $(elm).parent();
    var td_berat = $(tr).find('td').eq(1);
    var td_box = $(tr).find('td').eq(2);

    var is_inp_hide = $(td_berat).find('input').hasClass('hide');
    if (is_inp_hide) {
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

function simpanTaraKeranjang(elm) {
    var tr = $(elm).parent().parent().parent();

    var lbl_tara_berat = $(tr).find('td').eq(1).children('span');
    var inp_tara_berat = $(tr).find('td').eq(1).find('input');

    var lbl_tara_box = $(tr).find('td').eq(2).children('span');
    var inp_tara_box = $(tr).find('td').eq(2).find('input');

    var tara_berat = $(inp_tara_berat).val();
    var tara_box = $(inp_tara_box).val();

    if (tara_berat == '')
        tara_berat = 0;

    if (tara_box == '')
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
    if (tara_berat > 0 && tara_box > 0 && lbl_nomor == index) {
        arr_tara_berat.push(tara_berat);
        arr_tara_box.push(tara_box);

        $(inp_tara_berat).addClass('hide');
        $(inp_tara_box).addClass('hide');

        $(lbl_tara_berat).removeClass('hide');
        $(lbl_tara_box).removeClass('hide');

        $(elm).parent().addClass('hide');

        hitung_tara_keranjang();

        var html = '' +
            '<tr>' +
            '	<td class="vert-lign" align="center">' + (index + 1) + '</td>' +
            '	<td class="vert-align berat_tara" ondblclick="showInputTaraKeranjang(this)">' +
            '		<span class="berat_tara_lbl hide"></span>' +
            '		<input type="text" name="berat_tara_keranjang[]" style="text-align:center;" class="form-control input-sm" value="" onkeyup="cekDecimal(this)"/>' +
            '	</td>' +
            '	<td class="vert-align box_tara" ondblclick="showInputTaraKeranjang(this)">' +
            '		<span class="box_tara_lbl hide"></span>' +
            '		<input type="text" name="box_tara_keranjang[]" style="text-align:center" class="form-control input-sm" value="" onkeyup="cekNumerik(this)"/>' +
            '	</td>' +
            '	<td class="vert-align">' +
            '	 <div class="control">' +
            '		<button type="button" class="btn btn-primary btn-xs" onclick="simpanTaraKeranjang(this)">' +
            '			<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
            '		</button>' +
            '		<button type="button" class="btn btn-danger btn-xs" onclick="batalTaraKeranjang(this)">' +
            '			<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
            '		</button>' +
            '	 </div>' +
            '	</td>' +
            '</tr>';

        $(html).appendTo("#daftar_tara_keranjang > tbody");
    } else {
        $(lbl_tara_berat).addClass('hide');
        $(lbl_tara_box).addClass('hide');
        hitung_tara_keranjang();
    }
}

function batalTaraKeranjang(elm) {
    var tr = $(elm).parent().parent().parent();

    var lbl_tara_berat = $(tr).find('td').eq(1).children('span');
    var inp_tara_berat = $(tr).find('td').eq(1).find('input');

    var lbl_tara_box = $(tr).find('td').eq(2).children('span');
    var inp_tara_box = $(tr).find('td').eq(2).find('input');

    var tara_berat = $(inp_tara_berat).val();
    var tara_box = $(inp_tara_box).val();

    if (tara_berat == '')
        tara_berat = 0;

    if (tara_box == '')
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

    if (index > 1 || lbl_nomor < index) {
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

        if (lbl_urut == (index - 1)) {
            $(inp_tara_berat).removeClass('hide');
            $(inp_tara_box).removeClass('hide');

            $(lbl_tara_berat).addClass('hide');
            $(lbl_tara_box).addClass('hide');

            $(div_kontrol).removeClass('hide');
        }
    });

    hitung_tara_keranjang();
}

function hitung_tara_keranjang() {
    var val_berat = 0;
    var val_box = 0;

    arr_tara_berat = new Array();
    arr_tara_box = new Array();

    $('input[name^="berat_tara_keranjang"]').each(function() {
        var value = ($(this).val() == '') ? 0 : $(this).val();
        val_berat += parseFloat(value);

        arr_tara_berat.push(value);
    });

    $('input[name^="box_tara_keranjang"]').each(function() {
        var value = ($(this).val() == '') ? 0 : $(this).val();
        val_box += parseInt(value);

        arr_tara_box.push(value);
    });

    $('#total_tara_berat').html(Number(Math.round(val_berat * 1000) / 1000).toFixed(2));
    $('#total_tara_box').html(val_box);

    hitung_tara_end();
    hitung_ayam_end();
    hitung_bruto_end();
    hitung_netto_end();
}

/*Timbang Ayam*/
$('.jumlah_ayam').dblclick(function(e) {
    e.stopPropagation(); //<-------stop the bubbling of the event here

    var span = $(this).find("span").attr("data-kolom");
    console.log(span);
});

$('.tonase_ayam').dblclick(function(e) {
    e.stopPropagation(); //<-------stop the bubbling of the event here

    var span = $(this).find("span").attr("data-kolom");
    console.log(span);
});

function showInputTimbangAyam(elm) {
    var tr = $(elm).parent();
    var td_jumlah = $(tr).find('td').eq(1);
    var td_berat = $(tr).find('td').eq(2);

    var is_inp_hide = $(td_jumlah).find('input').hasClass('hide');
    if (is_inp_hide) {
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

function simpanJumlahAyam(elm, kolom) {
    var tr = $(elm).parent().parent().parent();

    var status = $(tr).find('td').eq(0).attr('data-status');
    var urutan = $(tr).find('td').eq(0).html();

    var lbl_ayam_jumlah = $(tr).find('td').eq(1).children('span');
    var inp_ayam_jumlah = $(tr).find('td').eq(1).find('input');

    var lbl_ayam_tonase = $(tr).find('td').eq(2).children('span');
    var inp_ayam_tonase = $(tr).find('td').eq(2).find('input');

    var ayam_jumlah = $(inp_ayam_jumlah).val();
    var ayam_tonase = $(inp_ayam_tonase).val();

    if (ayam_jumlah == '')
        ayam_jumlah = 0;

    if (ayam_tonase == '')
        ayam_tonase = 0;

    if (ayam_jumlah > 0 && ayam_tonase > 0) {
        console.log(status);
        if (status == "draft") {
            arr_ayam_jumlah.push(ayam_jumlah);
            arr_ayam_tonase.push(ayam_tonase);
        } else {
            arr_ayam_jumlah[(urutan - 1)] = ayam_jumlah;
            arr_ayam_tonase[(urutan - 1)] = ayam_tonase;
        }

        refreshDaftarTimbang(arr_ayam_jumlah, arr_ayam_tonase);
    } else {
        $(lbl_ayam_jumlah).addClass('hide');
        $(lbl_ayam_tonase).addClass('hide');
        hitung_timbangan_ayam(kolom);
    }
}

function refreshDaftarTimbang(arr_ayam_jumlah, arr_ayam_tonase) {
    var temp_kolom = 1;
    var html_table = new Array();
    var html_open = '<div class="col-md-3">' +
        '	<table id="daftar_timbang_ayam' + temp_kolom + '" class="table table-bordered table-condensed table-striped">' +
        '		<thead>' +
        '			<tr>' +
        '				<th class="vert-align" style="width:50px;">No</th>' +
        '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
        '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
        '				<th class="vert-align" style="width:300px"></th>' +
        '			</tr>' +
        '		</thead>' +
        '		<tbody>';

    var html_content = "";

    if (arr_ayam_jumlah.length > 0) {
        for (var i = 0; i < arr_ayam_jumlah.length; i++) {
            var temp_ayam_jumlah = arr_ayam_jumlah[i];
            var temp_ayam_tonase = arr_ayam_tonase[i];

            html_content += '<tr>' +
                '	<td class="vert-align" data-status="fix">' + (i + 1) + '</td>' +
                '	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">' +
                '		<span data-kolom="' + temp_kolom + '" class="jumlah_ayam_lbl">' + temp_ayam_jumlah + '</span>' +
                '		<input type="text" name="jumlah_ayam' + temp_kolom + '[]" style="text-align:center;" class="form-control input-sm hide" value="' + temp_ayam_jumlah + '"/>' +
                '	</td>' +
                '	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">' +
                '		<span data-kolom="' + temp_kolom + '" class="tonase_ayam_lbl">' + temp_ayam_tonase + '</span>' +
                '		<input type="text" name="tonase_ayam' + temp_kolom + '[]" style="text-align:center" class="form-control input-sm hide" value="' + temp_ayam_tonase + '"/>' +
                '	</td>' +
                '	<td class="vert-align">' +
                '		<div class="control hide">' +
                '			<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, ' + temp_kolom + ')">' +
                '				<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                '			</button>' +
                '			<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, ' + temp_kolom + ')">' +
                '				<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                '			</button>' +
                '		</div>' +
                '	</td>' +
                '</tr>';


            if (((i + 1) < (n_row_per_kolom * 4)) && ((i + 1) % n_row_per_kolom) == 0) {
                html_content += '</tbody>' +
                    '		<thead>' +
                    '			<tr>' +
                    '				<th class="vert-align" style="width:50px">Total</th>' +
                    '				<th class="vert-align" style="width:200px" id="total_jumlah_ayam' + temp_kolom + '"></th>' +
                    '				<th class="vert-align" style="width:200px" id="total_tonase_ayam' + temp_kolom + '"></th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '			</tr>' +
                    '		</thead>' +
                    '	</table>' +
                    '</div>';

                temp_kolom++;

                html_content += '<div class="col-md-3">' +
                    '	<table id="daftar_timbang_ayam' + temp_kolom + '" class="table table-bordered table-condensed table-striped">' +
                    '		<thead>' +
                    '			<tr>' +
                    '				<th class="vert-align" style="width:50px;">No</th>' +
                    '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                    '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                    '				<th class="vert-align" style="width:300px"></th>' +
                    '			</tr>' +
                    '		</thead>' +
                    '		<tbody>';
            }

            if ((i == arr_ayam_jumlah.length - 1) && (i + 1) < (n_row_per_kolom * 4)) {
                html_content += '<tr>' +
                    '	<td class="vert-align" data-status="draft">' + (i + 2) + '</td>' +
                    '	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">' +
                    '		<span data-kolom="' + temp_kolom + '" class="jumlah_ayam_lbl hide"></span>' +
                    '		<input type="text" name="jumlah_ayam' + temp_kolom + '[]" style="text-align:center;" class="form-control input-sm" value="" onkeyup="cekNumerik(this)"/>' +
                    '	</td>' +
                    '	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">' +
                    '		<span data-kolom="' + temp_kolom + '" class="tonase_ayam_lbl hide">' + temp_ayam_tonase + '</span>' +
                    '		<input type="text" name="tonase_ayam' + temp_kolom + '[]" style="text-align:center" class="form-control input-sm" value="" onkeyup="cekDecimal(this)"/>' +
                    '	</td>' +
                    '	<td class="vert-align">' +
                    '		<div class="control">' +
                    '			<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, ' + temp_kolom + ')">' +
                    '				<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                    '			</button>' +
                    '			<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, ' + temp_kolom + ')">' +
                    '				<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                    '			</button>' +
                    '		</div>' +
                    '	</td>' +
                    '</tr>';
            }
        }
    } else {
        html_content += '<tr>' +
            '	<td class="vert-align" data-status="fix">1</td>' +
            '	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">' +
            '		<span data-kolom="' + temp_kolom + '" class="jumlah_ayam_lbl hide">0</span>' +
            '		<input type="text" name="jumlah_ayam' + temp_kolom + '[]" style="text-align:center;" class="form-control input-sm" value="0" onkeyup="cekNumerik(this)"/>' +
            '	</td>' +
            '	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">' +
            '		<span data-kolom="' + temp_kolom + '" class="tonase_ayam_lbl hide">0</span>' +
            '		<input type="text" name="tonase_ayam' + temp_kolom + '[]" style="text-align:center" class="form-control input-sm" value="0" onkeyup="cekDecimal(this)"/>' +
            '	</td>' +
            '	<td class="vert-align">' +
            '		<div class="control">' +
            '			<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, ' + temp_kolom + ')">' +
            '				<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
            '			</button>' +
            '			<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, ' + temp_kolom + ')">' +
            '				<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
            '			</button>' +
            '		</div>' +
            '	</td>' +
            '</tr>';
    }

    var html_close = '</tbody>' +
        '		<thead>' +
        '			<tr>' +
        '				<th class="vert-align" style="width:50px">Total</th>' +
        '				<th class="vert-align" style="width:200px" id="total_jumlah_ayam' + temp_kolom + '"></th>' +
        '				<th class="vert-align" style="width:200px" id="total_tonase_ayam' + temp_kolom + '"></th>' +
        '				<th class="vert-align" style="width:200px"></th>' +
        '			</tr>' +
        '		</thead>' +
        '	</table>' +
        '</div>';

    html_table.push(html_open);
    html_table.push(html_content);
    html_table.push(html_close);

    $('#generate_this').html(html_table.join(''));

    while (temp_kolom > 0) {
        hitung_timbangan_ayam(temp_kolom);
        temp_kolom--;
    }
}

function batalJumlahAyam(elm, kolom) {
    var tr = $(elm).parent().parent().parent();

    var status = $(tr).find('td').eq(0).attr('data-status');
    var urutan = $(tr).find('td').eq(0).html();

    var lbl_ayam_jumlah = $(tr).find('td').eq(1).children('span');
    var inp_ayam_jumlah = $(tr).find('td').eq(1).find('input');

    var lbl_ayam_tonase = $(tr).find('td').eq(2).children('span');
    var inp_ayam_tonase = $(tr).find('td').eq(2).find('input');

    var ayam_jumlah = $(inp_ayam_jumlah).val();
    var ayam_tonase = $(inp_ayam_tonase).val();

    if (ayam_jumlah == '')
        ayam_jumlah = 0;

    if (ayam_tonase == '')
        ayam_tonase = 0;

    if (urutan > 1) {
        var minus = 1;

        if (ayam_jumlah == 0)
            minus++;
        arr_ayam_jumlah.splice((urutan - minus), 1);
        arr_ayam_tonase.splice((urutan - minus), 1);
    }

    refreshDaftarTimbang(arr_ayam_jumlah, arr_ayam_tonase);
}

function hitung_timbangan_ayam(kolom) {
    var val_jumlah = 0;
    var val_berat = 0;
    $('input[name^="jumlah_ayam' + kolom + '"]').each(function() {
        var value = ($(this).val() == '') ? 0 : $(this).val();
        val_jumlah += parseInt(value);
    });

    $('input[name^="tonase_ayam' + kolom + '"]').each(function() {
        var value = ($(this).val() == '') ? 0 : $(this).val();
        val_berat += parseFloat(value);
    });

    $('#total_jumlah_ayam' + kolom).html(val_jumlah);
    $('#total_tonase_ayam' + kolom).html(Number(Math.round(val_berat * 1000) / 1000).toFixed(2));

    hitung_tara_end();
    hitung_ayam_end();
    hitung_bruto_end();
    hitung_netto_end();
}

function hitung_tara_end() {
    var total_berat_box = $('#total_tara_berat').html();
    var total_jml_box = $('#total_tara_box').html();
    var rata_tara = 0;
    var tara = 0;

    if (parseInt(total_jml_box) > 0) {
        rata_tara = parseFloat(parseFloat(total_berat_box) / parseInt(total_jml_box));
    }

    tara = rata_tara * 2 * arr_ayam_jumlah.length;

    $('#inp_tot_tarra').val(Number(Math.ceil(tara * 100) / 100).toFixed(2));
}

function hitung_ayam_end() {
    var total = 0;
    for (var i = 0; i < arr_ayam_jumlah.length; i++) {
        total += parseInt(arr_ayam_jumlah[i]);
    }

    $('#inp_tot_ayam').val(total);
}

function hitung_bruto_end() {
    var total = 0;
    for (var i = 0; i < arr_ayam_tonase.length; i++) {
        total += parseFloat(arr_ayam_tonase[i]);
    }

    $('#inp_tot_bruto').val(Number(Math.round(total * 100) / 100).toFixed(2));
}

function hitung_netto_end() {
    var bruto = $('#inp_tot_bruto').val();
    var tara = $('#inp_tot_tarra').val();
    var netto = parseFloat(bruto) - parseFloat(tara);

    $('#inp_tot_netto').val(Number(Math.round(netto * 100) / 100).toFixed(2));
}

function cekNumerik(field) {
    var tr = $(field).parent().parent();
    var tonase = $(tr).find('td').eq(7).find('input').val();
    var jumlah = $(tr).find('td').eq(8).find('input').val();

    var re = /^[0-9-'.']*$/;
    if (!re.test(field.value)) {
        field.value = field.value.replace(/[^0-9-'.']/g, "");
    }

    if (!empty($(field).val()))
        $(field).val(parseInt(field.value) * 1);
    else
        $(field).val("0");
}

function cekDecimal(field) {
    var tr = $(field).parent().parent();
    var tonase = $(tr).find('td').eq(7).find('input').val();
    var jumlah = $(tr).find('td').eq(8).find('input').val();

    var re = /^[0-9-'.']*$/;
    if (!re.test(field.value)) {
        field.value = field.value.replace(/[^0-9-'.']/g, "");
    }

    if (!empty($(field).val())) {
        if ((field.value).charAt(0) == '0' && (field.value).charAt(1) != '.')
            $(field).val(parseFloat(field.value) * 1);
        else
            $(field).val(field.value);
    } else {
        $(field).val("0");
    }
}

function cekBeratRataTonase(field) {
    var tr = $(field).parent().parent();
    var tonase = $(tr).find('td').eq(7).find('input').val();
    var jumlah = $(tr).find('td').eq(8).find('input').val();

    var re = /^[0-9-'.']*$/;
    if (!re.test(field.value)) {
        field.value = field.value.replace(/[^0-9-'.']/g, "");
    }

    if (!empty($(field).val())) {
        if ((field.value).charAt(0) == '0' && (field.value).charAt(1) != '.')
            $(field).val(parseFloat(field.value) * 1);
        else
            $(field).val(field.value);
    } else {
        $(field).val("0");
    }

    hitungBeratRata(field, tonase, jumlah);
}

function cekBeratRataJumlah(field) {
    var tr = $(field).parent().parent();
    var tonase = $(tr).find('td').eq(7).find('input').val();
    var jumlah = $(tr).find('td').eq(8).find('input').val();

    var re = /^[0-9-'.']*$/;
    if (!re.test(field.value)) {
        field.value = field.value.replace(/[^0-9-'.']/g, "");
    }

    if (!empty($(field).val()))
        $(field).val(parseInt(field.value) * 1);
    else
        $(field).val("0");

    hitungBeratRata(field, tonase, jumlah);
}

function hitungBeratRata(field, tonase, jumlah) {
    var tr = $(field).parent().parent();
    var td_rata = $(tr).find('td').eq(9);

    if (tonase > 0 && jumlah > 0) {
        var berat = parseFloat(tonase / jumlah);
        $(td_rata).html(Number(Math.round(berat * 1000) / 1000).toFixed(3));
    } else {
        $(td_rata).html(Number(Math.round(0 * 1000) / 1000).toFixed(3));
    }
}

function cek_sj(field) {
    var sj = $(field).val();
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/cek_sj/",
            data: {
                no_sj: sj
            }
        })
        .done(function(data) {
            if (data.result == "success") {

            } else {
                toastr.warning("No. SJ '" + sj + "' telah terdaftar.", 'Warning');
                $(field).val('');
                $(field).focus();
            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

/*-------------Simpan-------------*/
$('#btnSimpan').click(function() {
    var passed = true;

    var no_sj = $('#inp_no_sj').val();
    var no_do = empty(selected_do) ? $('#inp_no_do').val() : selected_do;

    var no_reg = selected_noreg;
    var tgl_panen = $('#inp_tgl_panen').val();
    var umur_panen = selected_umur_panen;
    var berat_tara = selected_berat;
    var berat_aktual = $('#inp_tot_netto').val();
    var jumlah_aktual = $('#inp_tot_ayam').val();
    var bb_rata2 = parseFloat(parseFloat(berat_aktual) / parseInt(jumlah_aktual));
    var tgl_datang = $('#inp_tgl_datang').val();
    var tgl_mulai = $('#inp_tgl_mulai').val();
    var tgl_selesai = $('#inp_tgl_selesai').val();
    var tgl_buat = $('#inp_tgl_buat').val();

    var no_sj_01 = $('#inp_no_sj_01').val();
    var tonase_realisasi = $('#inp_tonase_realisasi').val();
    var jumlah_realisasi = $('#inp_jumlah_realisasi').val();
    var bb_rata_rata = parseFloat(parseFloat(tonase_realisasi) / parseInt(jumlah_realisasi));
    var level_user = $('#inp_level_user').val();

    if (passed && tgl_panen == '') {
        toastr.error("Mohon melakukan pengisian Tgl. Panen.");
        passed = false;
    }

    if (passed && no_sj_01 == '') {
        toastr.error("Mohon melakukan pengisian No. Surat Jalan.");
        passed = false;
    }

    if (passed && tonase_realisasi == '') {
        toastr.error("Mohon melakukan pengisian Tonase Realisasi.");
        passed = false;
    }

    if (passed && jumlah_realisasi == '') {
        toastr.error("Mohon melakukan pengisian Jumlah Realisasi.");
        passed = false;
    }

    if (passed && tgl_datang == '') {
        toastr.error("Mohon melakukan pengisian Tgl. Datang.");
        passed = false;
    }

    if (passed && tgl_mulai == '') {
        toastr.error("Mohon melakukan pengisian Tgl. Mulai.");
        passed = false;
    }

    if (passed && tgl_selesai == '') {
        toastr.error("Mohon melakukan pengisian Tgl. Selesai.");
        passed = false;
    }

    if (passed) {
        //Admin Farm Input
        if (level_user == "AGF") {
            var _resumeEntry = ['<table class="table table-bordered text-center custom_table">'];
            _resumeEntry.push('<thead>');
            _resumeEntry.push('<tr><th>No. SJ</th><th>Tanggal Panen</th><th>Umur Panen</th><th>Jumlah Realisasi <br >(Ekor)</th><th>Tonase Realisasi <br >(Kg)</th><th>BB Rata-Rata <br >(Kg)</th></tr>');
            _resumeEntry.push('</thead>');
            _resumeEntry.push('<tbody>');
            _resumeEntry.push('<tr><td>' + no_sj_01 + '</td><td>' + tgl_panen + '</td><td>' + umur_panen + '</td><td>' + number_format(jumlah_realisasi, 0, ',', '.') + '</td><td>' + number_format(tonase_realisasi, 0, ',', '.') + '</td><td>' + number_format(bb_rata_rata, 3, ',', '.') + '</td></tr>');
            _resumeEntry.push('</tbody>');
            _resumeEntry.push('</table>');
            if (no_do == '') {
                bootbox.dialog({
                    message: "Penyimpanan realisasi panen tanpa No DO. Apakah Anda yakin untuk melanjutkan proses penyimpanan?",
                    title: "Konfirmasi",
                    buttons: {
                        success: {
                            label: "Ya",
                            className: "btn-primary",
                            callback: function() {
                                bootbox.dialog({
                                    message: "Apakah Anda yakin untuk melanjutkan proses penyimpanan surat jalan berikut ?" + _resumeEntry.join('') + " Realisasi panen yang telah disimpan tidak dapat diubah.",
                                    title: "Konfirmasi",
                                    buttons: {
                                        success: {
                                            label: "Ya",
                                            className: "btn-primary",
                                            callback: function() {
                                                /*Simpan Admin Farm*/
                                                simpan_farm(no_reg, no_sj_01, no_do, tgl_panen, umur_panen,
                                                    tonase_realisasi, jumlah_realisasi, tgl_datang, tgl_mulai, tgl_selesai, tgl_buat);
                                            }
                                        },
                                        danger: {
                                            label: "Tidak",
                                            className: "btn-default",
                                            callback: function() {

                                            }
                                        }
                                    }
                                });
                            }
                        },
                        danger: {
                            label: "Tidak",
                            className: "btn-default",
                            callback: function() {

                            }
                        }
                    }
                });
            } else {
                var tr = $('#inp_tgl_panen').parent().parent().parent();
                var td_tonase_pakai = $('#inp_no_do').find('option:selected').data('berat_max'); //$(tr).find('td').eq(3).find('span').html();
                var td_max_rit = $('#inp_no_do').find('option:selected').data('max_berat_rit');
                var td_jumlah_pakai = $(tr).find('td').eq(4).find('span').html();

                if (parseFloat(tonase_realisasi) > parseFloat(td_tonase_pakai)) {
                    toastr.warning("Tonase realisasi lebih besar dari tonase DO ( " + td_tonase_pakai + " )");
                }

                if (parseFloat(tonase_realisasi) > parseFloat(td_max_rit)) {
                    toastr.error("Tonase realisasi tidak boleh lebih besar dari maximum rit ( " + td_max_rit + " )");
                } else {
                    bootbox.dialog({
                        message: "Apakah Anda yakin untuk melanjutkan proses penyimpanan surat jalan berikut ?" + _resumeEntry.join('') + " Realisasi panen yang telah disimpan tidak dapat diubah.",
                        title: "Konfirmasi",
                        buttons: {
                            success: {
                                label: "Ya",
                                className: "btn-primary",
                                callback: function() {
                                    /*Simpan Admin Farm*/
                                    simpan_farm(no_reg, no_sj_01, no_do, tgl_panen, umur_panen,
                                        tonase_realisasi, jumlah_realisasi, tgl_datang, tgl_mulai, tgl_selesai, tgl_buat);
                                }
                            },
                            danger: {
                                label: "Tidak",
                                className: "btn-default",
                                callback: function() {

                                }
                            }
                        }
                    });
                }
            }

            passed = false;
        } else {
            //Admin Budidaya
            if (passed && arr_tara_berat.length < 1) {
                toastr.warning("Hasil penimbangan tara harus diisi", 'Warning');
                passed = false;
            }

            if (passed && arr_ayam_tonase.length < 1) {
                toastr.warning("Hasil penimbangan ayam harus diisi", 'Warning');
                passed = false;
            }

            if (passed && (parseInt($('#total_tara_box').html()) < 25 || empty($('#total_tara_box').html()))) {
                toastr.warning("Penimbangan tara minimal sebanyak 25 keranjang", 'Warning');
                passed = false;
            }

            if (passed) {
                var tot_timbang_ayam = $('#inp_tot_ayam').val();
                var tot_timbang_netto = $('#inp_tot_netto').val();

                $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "riwayat_harian_kandang/realisasi_panen/compare_data/",
                        data: {
                            no_reg: no_reg,
                            no_sj: no_sj,
                            no_do: no_do,
                            tot_timbang_ayam: tot_timbang_ayam,
                            tot_timbang_netto: tot_timbang_netto
                        }
                    })
                    .done(function(data) {
                        if (data.result == "ok") {
                            bootbox.dialog({
                                message: "Apakah Anda yakin melakukan penyimpanan?",
                                title: "Konfirmasi",
                                buttons: {
                                    success: {
                                        label: "Ya",
                                        className: "btn-primary",
                                        callback: function() {
                                            berat_tara = $('#inp_tot_tarra').val();

                                            $.ajax({
                                                    type: 'POST',
                                                    dataType: 'json',
                                                    url: "riwayat_harian_kandang/realisasi_panen/simpan_detil_penimbangan/",
                                                    data: {
                                                        no_reg: no_reg,
                                                        no_sj: no_sj,
                                                        no_do: no_do,
                                                        berat_tara: berat_tara,
                                                        arr_tara_berat: arr_tara_berat,
                                                        arr_tara_box: arr_tara_box,
                                                        arr_ayam_jumlah: arr_ayam_jumlah,
                                                        arr_ayam_tonase: arr_ayam_tonase,
                                                        tot_timbang_ayam: tot_timbang_ayam,
                                                        tot_timbang_netto: tot_timbang_netto,
                                                        jumlah_akhir: data.akt_timbang_ayam,
                                                        berat_akhir: data.akt_timbang_netto,
                                                        level_user: level_user
                                                    }
                                                })
                                                .done(function(data) {
                                                    if (data.result == "success") {
                                                        toastr.success("Penyimpanan Realisasi Panen berhasil dilakukan", 'Informasi');
                                                    } else {
                                                        toastr.warning("Penyimpanan Realisasi Panen gagal dilakukan", 'Warning');
                                                    }

                                                    $('#detil_realisasi_panen').hide();
                                                    initializeData();
                                                })
                                                .fail(function(reason) {
                                                    console.info(reason);
                                                })
                                                .then(function(data) {});

                                        }
                                    },
                                    danger: {
                                        label: "Tidak",
                                        className: "btn-default",
                                        callback: function() {

                                        }
                                    }
                                }
                            });
                        } else {
                            var akt_timbang_ayam = data.akt_timbang_ayam;
                            var akt_timbang_netto = data.akt_timbang_netto;
                            berat_tara = $('#inp_tot_tarra').val();

                            var html = '' +
                                '<tr>' +
                                '<td class="vert-align">' + tot_timbang_netto + '</td>' +
                                '<td class="vert-align">' + akt_timbang_netto + '</td>' +
                                '<td class="vert-align">' + tot_timbang_ayam + '</td>' +
                                '<td class="vert-align">' + akt_timbang_ayam + '</td>' +
                                '</tr>';

                            console.log("selected_do>>" + selected_do);
                            console.log("#inp_no_do>>" + $('#inp_no_do').val());
                            console.log("no_do>>" + no_do);

                            $('#btnBatalPenyimpanan').attr("data-jawab", 'Farm');
                            $('#btnBatalPenyimpanan').attr("data-no_reg", no_reg);
                            $('#btnBatalPenyimpanan').attr("data-no_sj", no_sj);
                            $('#btnBatalPenyimpanan').attr("data-no_do", no_do);
                            $('#btnBatalPenyimpanan').attr("data-berat_tara", berat_tara);
                            $('#btnBatalPenyimpanan').attr("data-berat_akhir", data.akt_timbang_netto);
                            $('#btnBatalPenyimpanan').attr("data-jumlah_akhir", data.akt_timbang_ayam);
                            $('#btnBatalPenyimpanan').attr("data-berat_timbang", tot_timbang_netto);
                            $('#btnBatalPenyimpanan').attr("data-jumlah_timbang", tot_timbang_ayam);

                            $('#btnSimpanPenyimpanan').attr("data-jawab", 'Bdy');
                            $('#btnSimpanPenyimpanan').attr("data-no_reg", no_reg);
                            $('#btnSimpanPenyimpanan').attr("data-no_sj", no_sj);
                            $('#btnSimpanPenyimpanan').attr("data-no_do", no_do);
                            $('#btnSimpanPenyimpanan').attr("data-berat_tara", berat_tara);
                            $('#btnSimpanPenyimpanan').attr("data-berat_akhir", tot_timbang_netto);
                            $('#btnSimpanPenyimpanan').attr("data-jumlah_akhir", tot_timbang_ayam);

                            $('#tb_perbandingan > tbody').html(html);
                            $('#modal_notif_penimbangan').modal('show');
                        }
                    })
                    .fail(function(reason) {
                        console.info(reason);
                    })
                    .then(function(data) {});
            }
        }
    }
});

function update_do(elm) {
    var tr = $(elm).parent().parent();
    var td_umur_panen = $(tr).find('td').eq(1).find('span');
    var td_tgl_panen = $(tr).find('td').eq(2);
    var td_sj = $(tr).find('td').eq(6);

    var v_do = $(td_tgl_panen).find('option:selected').val();
    var v_sj = $(td_sj).html();
    var v_tgl_panen = $(td_tgl_panen).find('option:selected').data('tgl_panen');
    var v_umur_panen = $(td_umur_panen).html();
    var v_noreg = selected_noreg

    bootbox.dialog({
        message: "Apakah Anda yakin untuk melanjutkan proses penyimpanan? Realisasi panen yang telah disimpan tidak dapat diubah.",
        title: "Konfirmasi",
        buttons: {
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {
                    simpan_do_susulan(v_noreg, v_tgl_panen, v_umur_panen, v_do, v_sj);
                }
            },
            danger: {
                label: "Tidak",
                className: "btn-default",
                callback: function() {

                }
            }
        }
    });
}

function simpan_farm(no_reg, no_sj, no_do, tgl_panen, umur_panen, berat_aktual, jumlah_aktual, tgl_datang, tgl_mulai, tgl_selesai, tgl_buat) {

    var pad = '00';
    var temp_tgl_arr = tgl_panen.split(' ');
    var dd = parseInt(temp_tgl_arr[0]);
    var mm = (months.indexOf(temp_tgl_arr[1]) >= 0) ? (months.indexOf(temp_tgl_arr[1]) + 1) : (months_id.indexOf(temp_tgl_arr[1]) + 1);
    var yy = parseInt(temp_tgl_arr[2]);
    tgl_panen = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length);

    temp_tgl_arr = tgl_datang.split(' ');
    dd = parseInt(temp_tgl_arr[0]);
    mm = (months.indexOf(temp_tgl_arr[1]) >= 0) ? (months.indexOf(temp_tgl_arr[1]) + 1) : (months_id.indexOf(temp_tgl_arr[1]) + 1);
    yy = parseInt(temp_tgl_arr[2]);
    tgl_datang = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length) + ' ' + temp_tgl_arr[3];

    temp_tgl_arr = tgl_mulai.split(' ');
    dd = parseInt(temp_tgl_arr[0]);
    mm = (months.indexOf(temp_tgl_arr[1]) >= 0) ? (months.indexOf(temp_tgl_arr[1]) + 1) : (months_id.indexOf(temp_tgl_arr[1]) + 1);
    yy = parseInt(temp_tgl_arr[2]);
    tgl_mulai = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length) + ' ' + temp_tgl_arr[3];

    temp_tgl_arr = tgl_selesai.split(' ');
    dd = parseInt(temp_tgl_arr[0]);
    mm = (months.indexOf(temp_tgl_arr[1]) >= 0) ? (months.indexOf(temp_tgl_arr[1]) + 1) : (months_id.indexOf(temp_tgl_arr[1]) + 1);
    yy = parseInt(temp_tgl_arr[2]);
    tgl_selesai = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length) + ' ' + temp_tgl_arr[3];

    temp_tgl_arr = tgl_buat.split(' ');
    dd = parseInt(temp_tgl_arr[0]);
    mm = (months.indexOf(temp_tgl_arr[1]) >= 0) ? (months.indexOf(temp_tgl_arr[1]) + 1) : (months_id.indexOf(temp_tgl_arr[1]) + 1);
    yy = parseInt(temp_tgl_arr[2]);
    tgl_buat = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length);


    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/simpan_admin_farm/",
            data: {
                no_reg: no_reg,
                no_sj: no_sj,
                no_do: no_do,
                tgl_panen: tgl_panen,
                umur_panen: umur_panen,
                berat_aktual: berat_aktual,
                jumlah_aktual: jumlah_aktual,
                tgl_datang: tgl_datang,
                tgl_mulai: tgl_mulai,
                tgl_selesai: tgl_selesai,
                tgl_buat: tgl_buat
            }
        })
        .done(function(data) {
            if (data.result == "success") {
                toastr.success("Realisasi Panen dengan No. SJ : " + no_sj + " berhasil disimpan", 'Informasi');

                initializeData();
            } else {
                toastr.warning("Realisasi Panen dengan No. SJ : " + no_sj + " gagal disimpan", 'Warning');
            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

function simpan_do_susulan(no_reg, tgl_panen, umur_panen, no_do, no_sj) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/simpan_do_susulan/",
            data: {
                no_reg: no_reg,
                no_sj: no_sj,
                no_do: no_do,
                tgl_panen: tgl_panen,
                umur_panen: umur_panen
            }
        })
        .done(function(data) {
            if (data.result == "success") {
                toastr.success("Realisasi Panen dengan No. SJ : " + no_sj + " berhasil disimpan", 'Informasi');

                initializeData();
            } else {
                toastr.warning("Realisasi Panen dengan No. SJ : " + no_sj + " gagal disimpan", 'Warning');
            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

function simpan_detil(elm) {

    var tipe = $(elm).data('jawab');
    var no_reg = $(elm).data('no_reg');
    var no_sj = $('#inp_no_sj').val();
    var no_do = $('#inp_no_do_timbang').val();
    var berat_tara = $(elm).data('berat_tara');
    var berat_akhir = $(elm).data('berat_akhir');
    var jumlah_akhir = $(elm).data('jumlah_akhir');
    var berat_timbang = $(elm).data('berat_timbang');
    var jumlah_timbang = $(elm).data('jumlah_timbang');
    /*
    	console.log("no_reg : "+no_reg);
    	console.log("no_sj : "+no_sj);
    	console.log("no_do : "+no_do);
    	console.log("berat_tara : "+berat_tara);
    	console.log("arr_tara_berat : "+arr_tara_berat);
    	console.log("arr_tara_box : "+arr_tara_box);
    	console.log("arr_ayam_jumlah : "+arr_ayam_jumlah);
    	console.log("arr_ayam_tonase : "+arr_ayam_tonase);
    	console.log("jumlah_akhir : "+jumlah_akhir);
    	console.log("berat_akhir : "+berat_akhir);
    	console.log("berat_timbang : "+berat_timbang);
    	console.log("jumlah_timbang : "+jumlah_timbang);
    */
    // return false;

    if (tipe == "Bdy") {
        berat_timbang = berat_akhir;
        jumlah_timbang = jumlah_akhir;
    }

    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "riwayat_harian_kandang/realisasi_panen/simpan_detil_penimbangan/",
            data: {
                no_reg: no_reg,
                no_sj: no_sj,
                no_do: no_do,
                berat_tara: berat_tara,
                arr_tara_berat: arr_tara_berat,
                arr_tara_box: arr_tara_box,
                arr_ayam_jumlah: arr_ayam_jumlah,
                arr_ayam_tonase: arr_ayam_tonase,
                jumlah_akhir: jumlah_akhir,
                berat_akhir: berat_akhir,
                berat_timbang: berat_timbang,
                jumlah_timbang: jumlah_timbang
            }
        })
        .done(function(data) {
            if (data.result == "success") {
                toastr.success("Penyimpanan Realisasi Panen berhasil dilakukan", 'Informasi');

                arr_tara_berat = new Array();
                arr_tara_box = new Array();

                arr_ayam_jumlah = new Array();
                arr_ayam_tonase = new Array();

                $('#detil_realisasi_panen').hide();
                $('#modal_notif_penimbangan').modal('hide');
                initializeData();
            } else {
                toastr.warning("Penyimpanan Realisasi Panen gagal dilakukan", 'Warning');
            }


        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

function batal_simpan_detail(elm) {
    $('#modal_notif_penimbangan').modal('hide');
}

function reset_input() {
    $('#inp_no_sj').val('');
    $('#inp_no_sj').attr('disabled', true);

    $('#detil_realisasi_panen').hide();
}

$('#panen_final').on('dblclick', 'tr', function(e) {
    e.preventDefault();
});

var DELAY = 700,
    clicks = 0,
    timer = null;

$('#panen_final').on('click', 'tr', function() {
    var no_do = $(this).find('td').eq(0).attr('data-no_do');
    var berat_akhir = $(this).find('td').eq(0).attr('data-berat_akhir');
    var no_sj_elm = $(this).find('td').eq(6);
    var no_sj = $(this).find('td').eq(6).html();

    selected_do = no_do;
    selected_real_jml = $(this).find('td').eq(7).html();
    selected_real_brt = $(this).find('td').eq(8).html();

    $('#detil_realisasi_panen').show();
    $('#simpan_action').hide();

    $('#inp_no_sj').val(no_sj);
    $('#inp_no_sj').attr('disabled', true);

    $('#inp_no_do_timbang').val(no_do);

    clicks++;

    if ($(no_sj_elm).find('input'))
        $('#simpan_action').show();

    if (clicks === 1) {
        timer = setTimeout(function() {

            if (!empty(no_do)) {
                $(this).addClass('highlight').siblings().removeClass('highlight');
                $('#generate_this').html('');

                $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "riwayat_harian_kandang/realisasi_panen/get_detil_panen/",
                        data: {
                            no_reg: selected_noreg,
                            no_do: no_do,
                            no_sj: no_sj
                        }
                    })
                    .done(function(data) {
                        var detil_tara = data.tara;
                        var detil_ayam = data.ayam;

                        if (detil_tara.length > 0) {
                            var html_arr = new Array();
                            var jml_berat = 0;
                            var jml_box = 0;
                            for (var i = 0; i < detil_tara.length; i++) {
                                var obj = detil_tara[i];
                                html_arr.push('' +
                                    '<tr>' +
                                    '	<td class="vert-align">' + (i + 1) + '</td>' +
                                    '	<td class="vert-align berat_tara">' + Number(Math.ceil(obj.BERAT_TARA * 100) / 100).toFixed(2) + '</td>' +
                                    '	<td class="vert-align box_tara">' + obj.JUMLAH + '</td>' +
                                    '	<td class="vert-align"></td>' +
                                    '</tr>' +
                                    '');

                                jml_berat += parseFloat(obj.BERAT_TARA);
                                jml_box += parseInt(obj.JUMLAH);
                            }

                            $('#daftar_tara_keranjang > tbody').html(html_arr.join(''));
                            $('#total_tara_berat').html(Number(Math.ceil(jml_berat * 100) / 100).toFixed(2));
                            $('#total_tara_box').html(jml_box);
                        } else {
                            var html_arr = new Array();
                            html_arr.push('' +
                                '<tr>' +
                                '	<td class="vert-align">&nbsp;</td>' +
                                '	<td class="vert-align berat_tara"></td>' +
                                '	<td class="vert-align box_tara"></td>' +
                                '	<td class="vert-align"></td>' +
                                '</tr>' +
                                '');

                            $('#daftar_tara_keranjang > tbody').html(html_arr.join(''));
                        }

                        if (detil_ayam.length > 0) {
                            var temp_kolom = 1;
                            var html_table = new Array();
                            var html_open = '<div class="col-md-3">' +
                                '	<table id="daftar_timbang_ayam' + temp_kolom + '" class="table table-bordered table-condensed table-striped">' +
                                '		<thead>' +
                                '			<tr>' +
                                '				<th class="vert-align" style="width:50px;">No</th>' +
                                '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                                '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                                '				<th class="vert-align" style="width:300px"></th>' +
                                '			</tr>' +
                                '		</thead>' +
                                '		<tbody>';

                            var html_content = "";
                            for (var i = 0; i < detil_ayam.length; i++) {
                                var obj = detil_ayam[i];
                                var temp_ayam_jumlah = obj.BERAT_BRUTO;
                                var temp_ayam_tonase = obj.JUMLAH;

                                html_content += '<tr>' +
                                    '	<td class="vert-align" data-status="fix">' + (i + 1) + '</td>' +
                                    '	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">' +
                                    '		<span data-kolom="' + temp_kolom + '" class="jumlah_ayam_lbl">' + temp_ayam_jumlah + '</span>' +
                                    '		<input type="text" name="jumlah_ayam' + temp_kolom + '[]" style="text-align:center;" class="form-control input-sm hide" value="' + temp_ayam_jumlah + '"/>' +
                                    '	</td>' +
                                    '	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">' +
                                    '		<span data-kolom="' + temp_kolom + '" class="tonase_ayam_lbl">' + temp_ayam_tonase + '</span>' +
                                    '		<input type="text" name="tonase_ayam' + temp_kolom + '[]" style="text-align:center" class="form-control input-sm hide" value="' + temp_ayam_tonase + '"/>' +
                                    '	</td>' +
                                    '	<td class="vert-align"></td>' +
                                    '</tr>';

                                if (((i + 1) < (n_row_per_kolom * 4)) && ((i + 1) % n_row_per_kolom) == 0) {
                                    html_content += '</tbody>' +
                                        '		<thead>' +
                                        '			<tr>' +
                                        '				<th class="vert-align" style="width:50px">Total</th>' +
                                        '				<th class="vert-align" style="width:200px" id="total_jumlah_ayam' + temp_kolom + '"></th>' +
                                        '				<th class="vert-align" style="width:200px" id="total_tonase_ayam' + temp_kolom + '"></th>' +
                                        '				<th class="vert-align" style="width:200px"></th>' +
                                        '			</tr>' +
                                        '		</thead>' +
                                        '	</table>' +
                                        '</div>';

                                    temp_kolom++;

                                    html_content += '<div class="col-md-3">' +
                                        '	<table id="daftar_timbang_ayam' + temp_kolom + '" class="table table-bordered table-condensed table-striped">' +
                                        '		<thead>' +
                                        '			<tr>' +
                                        '				<th class="vert-align" style="width:50px;">No</th>' +
                                        '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                                        '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                                        '				<th class="vert-align" style="width:300px"></th>' +
                                        '			</tr>' +
                                        '		</thead>' +
                                        '		<tbody>';
                                }
                            }

                            var html_close = '</tbody>' +
                                '		<thead>' +
                                '			<tr>' +
                                '				<th class="vert-align" style="width:50px">Total</th>' +
                                '				<th class="vert-align" style="width:200px" id="total_jumlah_ayam' + temp_kolom + '"></th>' +
                                '				<th class="vert-align" style="width:200px" id="total_tonase_ayam' + temp_kolom + '"></th>' +
                                '				<th class="vert-align" style="width:200px"></th>' +
                                '			</tr>' +
                                '		</thead>' +
                                '	</table>' +
                                '</div>';

                            html_table.push(html_open);
                            html_table.push(html_content);
                            html_table.push(html_close);

                            $('#generate_this').html(html_table.join(''));

                            while (temp_kolom > 0) {
                                var val_jumlah = 0;
                                var val_berat = 0;
                                $('input[name^="jumlah_ayam' + temp_kolom + '"]').each(function() {
                                    var value = ($(this).val() == '') ? 0 : $(this).val();
                                    val_jumlah += parseInt(value);
                                });

                                $('input[name^="tonase_ayam' + temp_kolom + '"]').each(function() {
                                    var value = ($(this).val() == '') ? 0 : $(this).val();
                                    val_berat += parseFloat(value);
                                });

                                $('#total_jumlah_ayam' + temp_kolom).html(val_jumlah);
                                $('#total_tonase_ayam' + temp_kolom).html(Number(Math.round(val_berat * 1000) / 1000).toFixed(2));


                                temp_kolom--;
                            }

                            /*Hitung Tara End*/
                            var total_berat_box = $('#total_tara_berat').html();
                            var total_jml_box = $('#total_tara_box').html();
                            var rata_tara = 0;
                            var tara = 0;

                            if (parseInt(total_jml_box) > 0) {
                                rata_tara = parseFloat(parseFloat(total_berat_box) / parseInt(total_jml_box));
                            }

                            tara = rata_tara * 2 * detil_ayam.length;
                            $('#inp_tot_tarra').val(Number(Math.ceil(tara * 100) / 100).toFixed(2));

                            /*Hitung Ayam End*/
                            var total = 0;
                            for (var i = 0; i < detil_ayam.length; i++) {
                                total += parseInt((detil_ayam[i]).JUMLAH);
                            }

                            $('#inp_tot_ayam').val(total);

                            /*Hitung Bruto End*/
                            total = 0;
                            for (var i = 0; i < detil_ayam.length; i++) {
                                total += parseFloat((detil_ayam[i]).BERAT_BRUTO);
                            }

                            $('#inp_tot_bruto').val(Number(Math.round(total * 100) / 100).toFixed(2));

                            /*Hitung Netto End*/
                            var bruto = $('#inp_tot_bruto').val();
                            var tara = $('#inp_tot_tarra').val();
                            var netto = parseFloat(bruto) - parseFloat(tara);

                            $('#inp_tot_netto').val(Number(Math.round(netto * 100) / 100).toFixed(2));
                        } else {
                            var html_table = '' +
                                '<div class="col-md-3">' +
                                '	<table class="table table-bordered table-condensed table-striped">' +
                                '		<thead>' +
                                '			<tr>' +
                                '				<th class="vert-align" style="width:50px;">No</th>' +
                                '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                                '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                                '				<th class="vert-align" style="width:300px"></th>' +
                                '			</tr>' +
                                '		</thead>' +
                                // '		<tbody>';
                                '			<tr>' +
                                '				<th>&nbsp;</th>' +
                                '				<th></th>' +
                                '				<th></th>' +
                                '				<th></th>' +
                                '			</tr>' +
                                // '		</tbody>'+
                                '		<thead>' +
                                '			<tr>' +
                                '				<th class="vert-align" style="width:50px">Total</th>' +
                                '				<th class="vert-align" style="width:200px"></th>' +
                                '				<th class="vert-align" style="width:200px"></th>' +
                                '				<th class="vert-align" style="width:200px"></th>' +
                                '			</tr>' +
                                '		</thead>' +
                                '	</table>' +
                                '</div>';


                            $('#generate_this').html(html_table);
                            $('#inp_tot_tarra').val('');
                            $('#inp_tot_ayam').val('');
                            $('#inp_tot_bruto').val('');
                            $('#inp_tot_netto').val('');
                        }
                    })
                    .fail(function(reason) {
                        //console.info(reason);
                    })
                    .then(function(data) {});
            } else {
                $(this).addClass('highlight').siblings().removeClass('highlight');
                var html_arr = new Array();
                html_arr.push('' +
                    '<tr>' +
                    '	<td class="vert-align">&nbsp;</td>' +
                    '	<td class="vert-align berat_tara"></td>' +
                    '	<td class="vert-align box_tara"></td>' +
                    '	<td class="vert-align"></td>' +
                    '</tr>' +
                    '');

                $('#daftar_tara_keranjang > tbody').html(html_arr.join(''));

                var html_table = '' +
                    '<div class="col-md-3">' +
                    '	<table class="table table-bordered table-condensed table-striped">' +
                    '		<thead>' +
                    '			<tr>' +
                    '				<th class="vert-align" style="width:50px;">No</th>' +
                    '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                    '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                    '				<th class="vert-align" style="width:300px"></th>' +
                    '			</tr>' +
                    '		</thead>' +
                    // '		<tbody>';
                    '			<tr>' +
                    '				<th>&nbsp;</th>' +
                    '				<th></th>' +
                    '				<th></th>' +
                    '				<th></th>' +
                    '			</tr>' +
                    // '		</tbody>'+
                    '		<thead>' +
                    '			<tr>' +
                    '				<th class="vert-align" style="width:50px">Total</th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '			</tr>' +
                    '		</thead>' +
                    '	</table>' +
                    '</div>';


                $('#generate_this').html(html_table);
                $('#inp_tot_tarra').val('');
                $('#inp_tot_ayam').val('');
                $('#inp_tot_bruto').val('');
                $('#inp_tot_netto').val('');
            }

            clicks = 0; //after action performed, reset counter

        }, DELAY);
    } else {

        clearTimeout(timer); //prevent single-click action

        if (empty(berat_akhir)) {
            if (!empty(no_do)) {
                initializeData(false);

                $('#simpan_action').show();

                var html = '' +
                    '<tr>' +
                    '	<td class="vert-align">1</td>' +
                    '	<td class="vert-align berat_tara">' +
                    '		<span class="berat_tara_lbl hide"></span>' +
                    '		<input type="text" name="berat_tara_keranjang[]" style="text-align:center;" class="form-control input-sm" value="" onkeyup="cekDecimal(this)"/>' +
                    '	</td>' +
                    '	<td class="vert-align box_tara">' +
                    '		<span class="box_tara_lbl hide"></span>' +
                    '		<input type="text" name="box_tara_keranjang[]" style="text-align:center" class="form-control input-sm" value="" onkeyup="cekNumerik(this)"/>' +
                    '	</td>' +
                    '	<td class="vert-align">' +
                    '	 <div class="control">' +
                    '		<button type="button" class="btn btn-primary btn-xs" onclick="simpanTaraKeranjang(this)">' +
                    '			<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                    '		</button>' +
                    '		<button type="button" class="btn btn-danger btn-xs" onclick="batalTaraKeranjang(this)">' +
                    '			<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                    '		</button>' +
                    '	 </div>' +
                    '	</td>' +
                    '</tr>';


                $('#daftar_tara_keranjang > tbody').html(html);
                $('#total_tara_berat').html('');
                $('#total_tara_box').html('');

                html = '' +
                    '<div class="col-md-3">' +
                    '<table id="daftar_timbang_ayam1" class="table table-bordered table-condensed table-striped">' +
                    '	<thead>' +
                    '		<tr>' +
                    '			<th class="vert-align" style="width:50px;">No</th>' +
                    '			<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                    '			<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                    '			<th class="vert-align" style="width:300px"></th>' +
                    '		</tr>' +
                    '	</thead>' +
                    '	<tbody>' +
                    '		<tr>' +
                    '			<td class="vert-align" data-status="draft">1</td>' +
                    '			<td class="vert-align jumlah_ayam">' +
                    '				<span data-kolom="1" class="jumlah_ayam_lbl hide"></span>' +
                    '				<input type="text" name="jumlah_ayam1[]" style="text-align:center;" class="form-control input-sm" value="" onkeyup="cekNumerik(this)"/>' +
                    '			</td>' +
                    '			<td class="vert-align tonase_ayam">' +
                    '				<span data-kolom="1" class="tonase_ayam_lbl hide"></span>' +
                    '				<input type="text" name="tonase_ayam1[]" style="text-align:center" class="form-control input-sm" value="" onkeyup="cekDecimal(this)"/>' +
                    '			</td>' +
                    '			<td class="vert-align' +
                    '				<div class="control">' +
                    '					<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, 1)">' +
                    '						<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                    '					</button>' +
                    '					<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, 1)">' +
                    '						<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                    '					</button>' +
                    '				</div>' +
                    '			</td>' +
                    '		</tr>';
                '	</tbody>' +
                '	<thead>' +
                '		<tr>' +
                '			<th class="vert-align" style="width:50px">Total</th>' +
                '			<th class="vert-align" style="width:200px" id="total_jumlah_ayam1"></th>' +
                '			<th class="vert-align" style="width:200px" id="total_tonase_ayam1"></th>' +
                '			<th class="vert-align" style="width:200px"></th>' +
                '		</tr>' +
                '	</thead>' +
                '</table>';
                '</div>';

                $('#generate_this').html(html);
                $('#inp_tot_tarra').val('0');
                $('#inp_tot_ayam').val('0');
                $('#inp_tot_bruto').val('0');
                $('#inp_tot_netto').val('0');
            } else {
                var html_arr = new Array();
                html_arr.push('' +
                    '<tr>' +
                    '	<td class="vert-align">&nbsp;</td>' +
                    '	<td class="vert-align berat_tara"></td>' +
                    '	<td class="vert-align box_tara"></td>' +
                    '	<td class="vert-align"></td>' +
                    '</tr>' +
                    '');

                $('#daftar_tara_keranjang > tbody').html(html_arr.join(''));

                var html_table = '' +
                    '<div class="col-md-3">' +
                    '	<table class="table table-bordered table-condensed table-striped">' +
                    '		<thead>' +
                    '			<tr>' +
                    '				<th class="vert-align" style="width:50px;">No</th>' +
                    '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                    '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                    '				<th class="vert-align" style="width:300px"></th>' +
                    '			</tr>' +
                    '		</thead>' +
                    // '		<tbody>';
                    '			<tr>' +
                    '				<th>&nbsp;</th>' +
                    '				<th></th>' +
                    '				<th></th>' +
                    '				<th></th>' +
                    '			</tr>' +
                    // '		</tbody>'+
                    '		<thead>' +
                    '			<tr>' +
                    '				<th class="vert-align" style="width:50px">Total</th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '				<th class="vert-align" style="width:200px"></th>' +
                    '			</tr>' +
                    '		</thead>' +
                    '	</table>' +
                    '</div>';


                $('#generate_this').html(html_table);
                $('#inp_tot_tarra').val('');
                $('#inp_tot_ayam').val('');
                $('#inp_tot_bruto').val('');
                $('#inp_tot_netto').val('');
            }
        }
        clicks = 0; //after action performed, reset counter
    }
});

$('#panen_final_old').on('click', 'tr', function() {
    var no_do = $(this).find('td').eq(0).attr('data-no_do');
    var no_sj = $(this).find('td').eq(6).html();

    if (!empty(no_do)) {
        mode = "view";
        $(this).addClass('highlight').siblings().removeClass('highlight');
        $('#inp_no_sj').val(no_sj);
        $('#inp_no_sj').attr('disabled', true);
        $('#generate_this').html('');

        $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "riwayat_harian_kandang/realisasi_panen/get_detil_panen/",
                data: {
                    no_reg: selected_noreg,
                    no_do: no_do,
                    no_sj: no_sj
                }
            })
            .done(function(data) {
                var detil_tara = data.tara;
                var detil_ayam = data.ayam;

                if (detil_tara.length > 0) {
                    var html_arr = new Array();
                    var jml_berat = 0;
                    var jml_box = 0;
                    for (var i = 0; i < detil_tara.length; i++) {
                        var obj = detil_tara[i];
                        html_arr.push('' +
                            '<tr>' +
                            '	<td class="vert-align">' + (i + 1) + '</td>' +
                            '	<td class="vert-align berat_tara">' + Number(Math.ceil(obj.BERAT_TARA * 100) / 100).toFixed(2) + '</td>' +
                            '	<td class="vert-align box_tara">' + obj.JUMLAH + '</td>' +
                            '	<td class="vert-align"></td>' +
                            '</tr>' +
                            '');

                        jml_berat += parseFloat(obj.BERAT_TARA);
                        jml_box += parseInt(obj.JUMLAH);
                    }

                    $('#daftar_tara_keranjang > tbody').html(html_arr.join(''));
                    $('#total_tara_berat').html(Number(Math.ceil(jml_berat * 100) / 100).toFixed(2));
                    $('#total_tara_box').html(jml_box);
                }

                if (detil_ayam.length > 0) {
                    var temp_kolom = 1;
                    var html_table = new Array();
                    var html_open = '<div class="col-md-3">' +
                        '	<table id="daftar_timbang_ayam' + temp_kolom + '" class="table table-bordered table-condensed table-striped">' +
                        '		<thead>' +
                        '			<tr>' +
                        '				<th class="vert-align" style="width:50px;">No</th>' +
                        '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                        '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                        '				<th class="vert-align" style="width:300px"></th>' +
                        '			</tr>' +
                        '		</thead>' +
                        '		<tbody>';

                    var html_content = "";
                    for (var i = 0; i < detil_ayam.length; i++) {
                        var obj = detil_ayam[i];
                        var temp_ayam_jumlah = obj.BERAT_BRUTO;
                        var temp_ayam_tonase = obj.JUMLAH;

                        html_content += '<tr>' +
                            '	<td class="vert-align" data-status="fix">' + (i + 1) + '</td>' +
                            '	<td class="vert-align jumlah_ayam" ondblclick="showInputTimbangAyam(this)">' +
                            '		<span data-kolom="' + temp_kolom + '" class="jumlah_ayam_lbl">' + temp_ayam_jumlah + '</span>' +
                            '		<input type="text" name="jumlah_ayam' + temp_kolom + '[]" style="text-align:center;" class="form-control input-sm hide" value="' + temp_ayam_jumlah + '"/>' +
                            '	</td>' +
                            '	<td class="vert-align tonase_ayam" ondblclick="showInputTimbangAyam(this)">' +
                            '		<span data-kolom="' + temp_kolom + '" class="tonase_ayam_lbl">' + temp_ayam_tonase + '</span>' +
                            '		<input type="text" name="tonase_ayam' + temp_kolom + '[]" style="text-align:center" class="form-control input-sm hide" value="' + temp_ayam_tonase + '"/>' +
                            '	</td>' +
                            '	<td class="vert-align"></td>' +
                            '</tr>';

                        if (((i + 1) < (n_row_per_kolom * 4)) && ((i + 1) % n_row_per_kolom) == 0) {
                            html_content += '</tbody>' +
                                '		<thead>' +
                                '			<tr>' +
                                '				<th class="vert-align" style="width:50px">Total</th>' +
                                '				<th class="vert-align" style="width:200px" id="total_jumlah_ayam' + temp_kolom + '"></th>' +
                                '				<th class="vert-align" style="width:200px" id="total_tonase_ayam' + temp_kolom + '"></th>' +
                                '				<th class="vert-align" style="width:200px"></th>' +
                                '			</tr>' +
                                '		</thead>' +
                                '	</table>' +
                                '</div>';

                            temp_kolom++;

                            html_content += '<div class="col-md-3">' +
                                '	<table id="daftar_timbang_ayam' + temp_kolom + '" class="table table-bordered table-condensed table-striped">' +
                                '		<thead>' +
                                '			<tr>' +
                                '				<th class="vert-align" style="width:50px;">No</th>' +
                                '				<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                                '				<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                                '				<th class="vert-align" style="width:300px"></th>' +
                                '			</tr>' +
                                '		</thead>' +
                                '		<tbody>';
                        }
                    }

                    var html_close = '</tbody>' +
                        '		<thead>' +
                        '			<tr>' +
                        '				<th class="vert-align" style="width:50px">Total</th>' +
                        '				<th class="vert-align" style="width:200px" id="total_jumlah_ayam' + temp_kolom + '"></th>' +
                        '				<th class="vert-align" style="width:200px" id="total_tonase_ayam' + temp_kolom + '"></th>' +
                        '				<th class="vert-align" style="width:200px"></th>' +
                        '			</tr>' +
                        '		</thead>' +
                        '	</table>' +
                        '</div>';

                    html_table.push(html_open);
                    html_table.push(html_content);
                    html_table.push(html_close);

                    $('#generate_this').html(html_table.join(''));

                    while (temp_kolom > 0) {
                        var val_jumlah = 0;
                        var val_berat = 0;
                        $('input[name^="jumlah_ayam' + temp_kolom + '"]').each(function() {
                            var value = ($(this).val() == '') ? 0 : $(this).val();
                            val_jumlah += parseInt(value);
                        });

                        $('input[name^="tonase_ayam' + temp_kolom + '"]').each(function() {
                            var value = ($(this).val() == '') ? 0 : $(this).val();
                            val_berat += parseFloat(value);
                        });

                        $('#total_jumlah_ayam' + temp_kolom).html(val_jumlah);
                        $('#total_tonase_ayam' + temp_kolom).html(Number(Math.round(val_berat * 1000) / 1000).toFixed(2));


                        temp_kolom--;
                    }

                    /*Hitung Tara End*/
                    var total_berat_box = $('#total_tara_berat').html();
                    var total_jml_box = $('#total_tara_box').html();
                    var rata_tara = 0;
                    var tara = 0;

                    if (parseInt(total_jml_box) > 0) {
                        rata_tara = parseFloat(parseFloat(total_berat_box) / parseInt(total_jml_box));
                    }

                    tara = rata_tara * 2 * detil_ayam.length;
                    $('#inp_tot_tarra').val(Number(Math.ceil(tara * 100) / 100).toFixed(2));

                    /*Hitung Ayam End*/
                    var total = 0;
                    for (var i = 0; i < detil_ayam.length; i++) {
                        total += parseInt((detil_ayam[i]).JUMLAH);
                    }

                    $('#inp_tot_ayam').val(total);

                    /*Hitung Bruto End*/
                    total = 0;
                    for (var i = 0; i < detil_ayam.length; i++) {
                        total += parseFloat((detil_ayam[i]).BERAT_BRUTO);
                    }

                    $('#inp_tot_bruto').val(Number(Math.round(total * 100) / 100).toFixed(2));

                    /*Hitung Netto End*/
                    var bruto = $('#inp_tot_bruto').val();
                    var tara = $('#inp_tot_tarra').val();
                    var netto = parseFloat(bruto) - parseFloat(tara);

                    $('#inp_tot_netto').val(Number(Math.round(netto * 100) / 100).toFixed(2));
                }
            })
            .fail(function(reason) {
                console.info(reason);
            })
            .then(function(data) {});

    } else {
        if (mode == "view") {
            mode = "input";
            $('#inp_no_sj').val('');
            $('#inp_no_sj').attr('disabled', false);

            var html = '' +
                '<tr>' +
                '	<td class="vert-align">1</td>' +
                '	<td class="vert-align berat_tara">' +
                '		<span class="berat_tara_lbl hide"></span>' +
                '		<input type="text" name="berat_tara_keranjang[]" style="text-align:center;" class="form-control input-sm" value=""/>' +
                '	</td>' +
                '	<td class="vert-align box_tara">' +
                '		<span class="box_tara_lbl hide"></span>' +
                '		<input type="text" name="box_tara_keranjang[]" style="text-align:center" class="form-control input-sm" value=""/>' +
                '	</td>' +
                '	<td class="vert-align">' +
                '	 <div class="control">' +
                '		<button type="button" class="btn btn-primary btn-xs" onclick="simpanTaraKeranjang(this)">' +
                '			<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                '		</button>' +
                '		<button type="button" class="btn btn-danger btn-xs" onclick="batalTaraKeranjang(this)">' +
                '			<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                '		</button>' +
                '	 </div>' +
                '	</td>' +
                '</tr>';


            $('#daftar_tara_keranjang > tbody').html(html);
            $('#total_tara_berat').html('');
            $('#total_tara_box').html('');

            html = '' +
                '<div class="col-md-3">' +
                '<table id="daftar_timbang_ayam1" class="table table-bordered table-condensed table-striped">' +
                '	<thead>' +
                '		<tr>' +
                '			<th class="vert-align" style="width:50px;">No</th>' +
                '			<th class="vert-align" style="width:200px;">Jumlah<br/>Ekor</th>' +
                '			<th class="vert-align" style="width:200px;">Tonase<br>(kg)</th>' +
                '			<th class="vert-align" style="width:300px"></th>' +
                '		</tr>' +
                '	</thead>' +
                '	<tbody>' +
                '		<tr>' +
                '			<td class="vert-align" data-status="draft">1</td>' +
                '			<td class="vert-align jumlah_ayam">' +
                '				<span data-kolom="1" class="jumlah_ayam_lbl hide"></span>' +
                '				<input type="text" name="jumlah_ayam1[]" style="text-align:center;" class="form-control input-sm" value=""/>' +
                '			</td>' +
                '			<td class="vert-align tonase_ayam">' +
                '				<span data-kolom="1" class="tonase_ayam_lbl hide"></span>' +
                '				<input type="text" name="tonase_ayam1[]" style="text-align:center" class="form-control input-sm" value=""/>' +
                '			</td>' +
                '			<td class="vert-align' +
                '				<div class="control">' +
                '					<button type="button" class="btn btn-primary btn-xs" onclick="simpanJumlahAyam(this, 1)">' +
                '						<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                '					</button>' +
                '					<button type="button" class="btn btn-danger btn-xs" onclick="batalJumlahAyam(this, 1)">' +
                '						<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                '					</button>' +
                '				</div>' +
                '			</td>' +
                '		</tr>';
            '	</tbody>' +
            '	<thead>' +
            '		<tr>' +
            '			<th class="vert-align" style="width:50px">Total</th>' +
            '			<th class="vert-align" style="width:200px" id="total_jumlah_ayam1"></th>' +
            '			<th class="vert-align" style="width:200px" id="total_tonase_ayam1"></th>' +
            '			<th class="vert-align" style="width:200px"></th>' +
            '		</tr>' +
            '	</thead>' +
            '</table>';
            '</div>';

            $('#generate_this').html(html);
            $('#inp_tot_tarra').val('0');
            $('#inp_tot_ayam').val('0');
            $('#inp_tot_bruto').val('0');
            $('#inp_tot_netto').val('0');
        }
    }
});