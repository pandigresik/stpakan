var kandang_in_farm = new Array();
var sisa_pakan = new Array();
var sisa_pakan_temp = new Array();

var edit_state = "closed";

var alokasi_retur = new Array(),
    alokasi_tujuan = new Array(),
    alokasi_pakan = new Array(),
    alokasi_sak = new Array();

var selected_farm,
    selected_noretur,
    selected_kandang,
    selected_noreg;

var can_propose = ["P"];
var can_approve = ["KF"];
var can_accept = ["AG"];

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

$(document).ready(function() {
    $("#inp_tglawal").datepicker({
        dateFormat: 'dd M yy'
    });
    $("#inp_tglakhir").datepicker({
        dateFormat: 'dd M yy'
    });

    refreshDataRetur();
});
/*Baru.......................*/
$('#btnBaru').click(function() {
    $('#modal_buat_retur_pakan').modal('show');
    $('#inp_kandang').val('');
    $('#pengajuan_tb_sisa > tbody').html('');
    initializeKandang();

    $('.tgl_retur input').datetimepicker({
        pickTime: false,
        format: "DD MMM YYYY"
    });
    $('.tgl_retur input').data("DateTimePicker").disable();
    $('#alokasi_tb_sisa > tbody').html('');
});

function initializeKandang() {
    selected_farm = $('#inp_farm').val();

    alokasi_retur = new Array();
    alokasi_tujuan = new Array();
    alokasi_pakan = new Array();
    alokasi_sak = new Array();

    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "rekap_retur_pakan/retur_sisa_pakan/get_kandang_farm/",
            data: {
                kode_farm: selected_farm
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

                        $('#inp_flock').val('');
                        $('#inp_doc_in').val('');
                        $('#inp_umur').val('');
                        $('#inp_tgl_retur').val('');
                        $('.tgl_retur input').data("DateTimePicker").disable();

                        if (parseInt(current.umur) > 0) {
                            if (parseInt(current.jml_panen) > 0) {
                                $('#inp_flock').val(current.flok_bdy);
                                $('#inp_doc_in').val(current.tgl_doc_in);
                                $('#inp_umur').val(current.umur);

                                $('.tgl_retur input').data("DateTimePicker").setDate(new Date());
                                //$('.tgl_retur input').data("DateTimePicker").enable();

                                initializeSisaPakan(current.kode_farm, current.no_reg);
                            } else {
                                toastr.warning("Realisasi panen untuk kandang " + current.nama_kandang + " belum dientri.");
                            }
                        } else {
                            toastr.warning("LHK Kandang " + current.nama_kandang + " belum dientri.");
                        }


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

function initializeSisaPakan(kode_farm, no_reg) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "rekap_retur_pakan/retur_sisa_pakan/get_sisa_pakan/",
            data: {
                no_reg: no_reg
            }
        })
        .done(function(data) {
            var obj = data;

            if (!empty(obj)) {
                sisa_pakan = new Array();
                sisa_pakan_temp = new Array();
                var html = new Array();

                for (var i = 0; i < obj.length; i++) {
                    var kode_barang = obj[i].kode_barang;
                    var nama_barang = obj[i].nama_barang;
                    var jumlah_sak = obj[i].jml_akhir;
                    var berat_sak = obj[i].berat_akhir;
                    var bentuk_barang = obj[i].bentuk_barang;

                    var str = '<tr>' +
                        '<td class="vert-align">' + kode_barang + ' <input type="hidden" name="sisa_kode_pakan[]" value="' + kode_barang + '"></td>' +
                        '<td class="vert-align">' + nama_barang + '</td>' +
                        '<td class="vert-align">' + jumlah_sak + ' <input type="hidden" name="sisa_jml_pakan[]" value="' + jumlah_sak + '"></td>' +
                        '<td class="vert-align">' + berat_sak + ' <input type="hidden" name="sisa_brt_pakan[]" value="' + berat_sak + '"></td>' +
                        '<td class="vert-align">' + bentuk_barang + '</td>' +
                        '</tr>';

                    sisa_pakan.push(obj[i]);
                    var temp = new Array();
                    temp["kode_barang"] = obj[i].kode_barang;
                    temp["nama_barang"] = obj[i].nama_barang;
                    temp["jml_barang"] = obj[i].jml_akhir;

                    sisa_pakan_temp.push(temp);
                    html.push(str);
                }

                $('#pengajuan_tb_sisa > tbody').html(html.join(''));
                initializeAlokasiPakan(kode_farm, no_reg);
            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {

        });
}

function initializeAlokasiPakan(kode_farm, no_reg, state = null) {
    var html = new Array();
    var str = '';
    var selected = null;
    var selected_retur = "";
    var index_last = 0;

    if (alokasi_retur.length > 0) {
        for (var i = 0; i < alokasi_retur.length; i++) {
            if (i < (alokasi_retur.length)) {
                console.log("index:" + i);
                str = '' +
                    '<tr>' +
                    '	<td data-index="' + i + '" class="vert-align">' + (alokasi_retur[i]).toUpperCase() + '</td>' +
                    '	<td class="vert-align">' + alokasi_tujuan[i] + '</td>' +
                    '	<td class="vert-align">' + alokasi_pakan[i] + '</td>' +
                    '	<td class="vert-align">' + alokasi_sak[i] + '</td>' +
                    '	<td>' +
                    '		<div class="control">' +
                    '			<center>' +
                    '				<button type="button" class="btn btn-danger" onclick="batalReturPakan(this, 1)">' +
                    '					<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                    '				</button>' +
                    '			</center>' +
                    '		</div>' +
                    '	</td>' +
                    '</tr>' +
                    '';
            } else {
                if (!empty(state)) {
                    index_last = i;

                    selected = alokasi_tujuan[i];
                    selected_retur = (alokasi_retur[i]).toLowerCase();

                    str += '' +
                        '<tr>' +
                        '	<td>' +
                        '		<select class="form-control" name="alokasi_retur[]" id="inp_alokasi_retur" onchange="pilihAlokasiRetur(this)">' +
                        '			<option data-kode_farm="' + kode_farm + '" ' + (((alokasi_retur[i]).toLowerCase() == 'kandang') ? 'selected="selected"' : '') + ' value="kandang">Kandang</option>' +
                        '			<option data-kode_farm="' + kode_farm + '" ' + (((alokasi_retur[i]).toLowerCase() == 'gudang') ? 'selected="selected"' : '') + ' value="gudang">Gudang</option>' +
                        '		</select>' +
                        '	</td>' +
                        '	<td>' +
                        '		<select class="form-control" name="tujuan_retur[]">' +
                        '		</select>' +
                        '	</td>' +
                        '	<td>' +
                        '		<select class="form-control" name="barang_retur[]" onchange="pilihAlokasiPakan(this)">';

                    for (var j = 0; j < sisa_pakan_temp.length; j++) {
                        var obj = sisa_pakan_temp[j];
                        var selected = "";

                        if (alokasi_pakan[i] == obj["kode_barang"]) {
                            selected = 'selected="selected"';

                            sisa_pakan_temp[j]["jml_barang"] = (parseInt(sisa_pakan_temp[j]["jml_barang"]) + parseInt(alokasi_sak[i]));
                        }

                        str += '<option data-jml_sisa="' + obj["jml_barang"] + '" ' + selected + ' value="' + obj["kode_barang"] + '">' + obj["nama_barang"] + '</option>';

                    }

                    str += '' +
                        '		</select>' +
                        '	</td>' +
                        '	<td><input type="text" name="jumlah_retur[]" value="' + alokasi_sak[i] + '" class="form-control" onkeyup="cekJmlRetur(this)"></td>' +
                        '	<td>' +
                        '		<div class="control">' +
                        '			<center>' +
                        '				<button type="button" class="btn btn-primary" onclick="simpanReturPakan(this, 1)">' +
                        '					<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                        '				</button>' +
                        '			</center>' +
                        '		</div>' +
                        '	</td>' +
                        '</tr>' +
                        '';
                } else {
                    console.log("index:" + i);
                    str = '' +
                        '<tr>' +
                        '	<td data-index="' + i + '" class="vert-align">' + (alokasi_retur[i]).toUpperCase() + '</td>' +
                        '	<td class="vert-align">' + alokasi_tujuan[i] + '</td>' +
                        '	<td class="vert-align">' + alokasi_pakan[i] + '</td>' +
                        '	<td class="vert-align">' + alokasi_sak[i] + '</td>' +
                        '	<td>' +
                        '		<div class="control">' +
                        '			<center>' +
                        '				<button type="button" class="btn btn-danger" onclick="batalReturPakan(this, 1)">' +
                        '					<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                        '				</button>' +
                        '			</center>' +
                        '		</div>' +
                        '	</td>' +
                        '</tr>' +
                        '';
                }
            }

            html.push(str);
        }
    }

    var str = '';
    var ext = false;

    str += '' +
        '<tr>' +
        '	<td>' +
        '		<select class="form-control" name="alokasi_retur[]" id="inp_alokasi_retur" onchange="pilihAlokasiRetur(this)">' +
        '			<option data-kode_farm="' + kode_farm + '" value="kandang">Kandang</option>' +
        '			<option data-kode_farm="' + kode_farm + '" value="gudang">Gudang</option>' +
        '		</select>' +
        '	</td>' +
        '	<td>' +
        '		<select class="form-control" name="tujuan_retur[]">' +
        '		</select>' +
        '	</td>' +
        '	<td>' +
        '		<select class="form-control" name="barang_retur[]" onchange="pilihAlokasiPakan(this)">';

    var default_jml = "";
    for (var i = 0; i < sisa_pakan_temp.length; i++) {
        var obj = sisa_pakan_temp[i];

        if (obj["jml_barang"] > 0) {
            str += '<option data-jml_sisa="' + obj["jml_barang"] + '" value="' + obj["kode_barang"] + '">' + obj["nama_barang"] + '</option>';

            if (empty(default_jml))
                default_jml = obj["jml_barang"];

            ext = true;
        }
    }

    str += '' +
        '		</select>' +
        '	</td>' +
        '	<td><input type="text" name="jumlah_retur[]" value="' + default_jml + '" class="form-control" onkeyup="cekJmlRetur(this)"></td>' +
        '	<td>' +
        '		<div class="control">' +
        '			<center>' +
        '				<button type="button" class="btn btn-primary" onclick="simpanReturPakan(this, 1)">' +
        '					<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
        '				</button>' +
        '				<button type="button" class="btn btn-danger" onclick="resetReturPakanDeleteRow(this, 1)">' +
        '					<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
        '				</button>' +
        '			</center>' +
        '		</div>' +
        '	</td>' +
        '</tr>' +
        '';

    if (ext)
        html.push(str);


    $('#alokasi_tb_sisa > tbody').html(html.join(''));
    if (empty(state)) {
        initializeKandangTujuan($('select[name^=tujuan_retur]'), kode_farm, no_reg);
    } else {
        if (selected_retur == 'gudang')
            initializeGudangTujuan($('select[name^=tujuan_retur]'), kode_farm, no_reg, state, selected);
        else
            initializeKandangTujuan($('select[name^=tujuan_retur]'), kode_farm, no_reg, state, selected);

    }
}

function initializeKandangTujuan(elm, kode_farm, no_reg, state = null, selected_val = null) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "rekap_retur_pakan/retur_sisa_pakan/get_kandang_tujuan/",
            data: {
                kode_farm: kode_farm,
                no_reg: no_reg
            }
        })
        .done(function(data) {
            var obj = data;

            if (!empty(obj)) {
                var html = new Array();
                for (var i = 0; i < obj.length; i++) {
                    var kode_farm = obj[i].kode_farm;
                    var no_reg = obj[i].no_reg;
                    var nama_kandang = obj[i].nama_kandang;

                    var selected = '';
                    if (!empty(state) && no_reg == selected_val) {
                        selected = 'selected="selected"';
                    }

                    var str = '<option value="' + no_reg + '" ' + selected + '>' + nama_kandang + '</option>';

                    html.push(str);
                }

                $(elm).html(html.join(''));

                if (obj.length > 1) {
                    $(elm).attr("disabled", false);
                } else {
                    $(elm).attr("disabled", true);
                }
            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {

        });
}

function initializeGudangTujuan(elm, kode_farm, state = null, selected_val = null) {
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "rekap_retur_pakan/retur_sisa_pakan/get_gudang_tujuan/",
            data: {
                kode_farm: kode_farm
            }
        })
        .done(function(data) {
            var obj = data;

            if (!empty(obj)) {
                var html = new Array();
                for (var i = 0; i < obj.length; i++) {
                    var kode_gudang = obj[i].kode_gudang;
                    var nama_gudang = obj[i].nama_gudang;

                    var selected = '';
                    if (!empty(state) && kode_gudang == selected_val) {
                        selected = 'selected="selected"';
                    }

                    var str = '<option value="' + kode_gudang + '" ' + selected + '>' + nama_gudang + '</option>';

                    html.push(str);
                }

                $(elm).html(html.join(''));

                if (obj.length > 1) {
                    $(elm).attr("disabled", false);
                } else {
                    $(elm).attr("disabled", true);
                }
            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {

        });
}

function cekJmlRetur(elm) {
    var jml = 0;

    var tr = $(elm).parent().parent();
    var td_barang = $(tr).find('td').eq(2).find('select');
    var sisa_jml = $(td_barang).find('option:selected').data('jml_sisa');

    if (!empty($(elm).val())) {
        jml = parseInt($(elm).val());
    } else {

    }

    if (jml > 0) {
        if (jml > sisa_jml) {
            $(elm).val(sisa_jml);
            toastr.warning("Jumlah retur tidak terpenuhi. Jumlah retur lebih besar dari sisa stok kandang", "Warning");
        } else {
            $(elm).val(jml);
        }
    } else {
        // $(elm).val(jml*1);
    }

}

function cekJmlReturNow(elm) {
    var td = $(elm).parent();
    var tr = $(td).parent();
    var jml_akhir = parseFloat($(td).data("jml_akhir"));
    var jml_pakai = parseFloat($(td).data("jml_pakai"));
    var jml_retur = parseInt($(td).data("jml_retur"));
    var brt_retur = parseFloat($(td).data("brt_retur"));

    var jml_sisa = (parseInt(jml_akhir) - parseInt(jml_pakai)) + parseInt(jml_retur);

    if (parseInt($(elm).val()) > parseInt(jml_sisa)) {
        $(elm).val(0);
        toastr.warning("Jumlah retur tidak terpenuhi. Jumlah retur maksimal " + jml_sisa, "Warning");
    } else {
        var brt_rata = brt_retur / jml_retur;
        var brt = (parseInt($(elm).val()) > 0) ? parseInt($(elm).val()) * brt_rata : 0;
        $(td).attr("data-brt_retur_baru", brt);
        $(tr).find('td:nth-child(10)').find('span.berat').text(Number(brt).toFixed(3));
        $(elm).val((parseInt($(elm).val()) >= 0) ? parseInt($(elm).val()) : 0);
    }
}

function pilihAlokasiPakan(elm) {
    var tr = $(elm).parent().parent();
    var td_jumlah = $(tr).find('td').eq(3).find('input');

    var jml = $(elm).find('option:selected').data('jml_sisa');
    $(td_jumlah).val(jml);

}

function pilihAlokasiRetur(elm) {
    var tr = $(elm).parent().parent();
    var td_tujuan = $(tr).find('td').eq(1).find('select');

    var alokasi_farm = $(elm).find('option:selected').data('kode_farm');
    var alokasi = $(elm).find('option:selected').val();

    if (alokasi == "kandang") {
        initializeKandangTujuan(td_tujuan, alokasi_farm, selected_noreg);
    } else {
        initializeGudangTujuan(td_tujuan, alokasi_farm);
    }
}

function simpanReturPakan(elm, val) {
    var tr = $(elm).parent().parent().parent().parent();
    var alokasi = $(tr).find('td').eq(0).find('select').find('option:selected').val();
    var tujuan = $(tr).find('td').eq(1).find('select').find('option:selected').val();
    var pakan = $(tr).find('td').eq(2).find('select').find('option:selected').val();
    var jml = $(tr).find('td').eq(3).find('input').val();

    jml = parseInt(jml);

    if (jml > 0) {
        var exist = false;

        for (var i = 0; i < alokasi_pakan.length; i++) {
            if (alokasi_pakan[i] == pakan && alokasi_tujuan[i] == tujuan) {
                alokasi_sak[i] = parseInt(parseInt(alokasi_sak[i]) + parseInt(jml));

                exist = true;
            }
        }

        if (!exist) {
            alokasi_retur.push(alokasi);
            alokasi_tujuan.push(tujuan);
            alokasi_pakan.push(pakan);
            alokasi_sak.push(jml);
        }

        for (var i = 0; i < sisa_pakan_temp.length; i++) {
            var obj = sisa_pakan_temp[i];
            var sisa_jml = obj["jml_barang"];

            if (obj["kode_barang"] == pakan) {
                sisa_pakan_temp[i]["jml_barang"] = sisa_jml - jml;
            }
        }

        initializeAlokasiPakan(selected_farm, selected_noreg);
        $(tr).find('td').eq(3).removeClass("has-error");
    } else {
        $(tr).find('td').eq(3).addClass("has-error");
        $(tr).find('td').eq(3).find('input').focus();
        toastr.warning("Mohon lakukan pengisian untuk jumlah sak retur", "Warning");
    }
}

function batalReturPakanRow(elm, val) {
    refreshDataRetur();
    edit_state = "closed";
}

function simpanReturPakanRow(elm, val) {
    var tr = $(elm).closest('tr');
    var no_retur = $.trim($(tr).find('td.no_retur').text());
    var no_reg = $.trim($(tr).find('td.no_reg').text());
    // var tgl_retur = $(tr).find('td.tgl_retur').find('input').val();

    var kode_barang_arr = new Array();
    var jml_retur_arr = new Array();
    var brt_retur_arr = new Array();
    var kode_barang = $.trim(tr.find('td.kode_barang').data('kode_barang'));
    var keterangan1 = tr.find('td.tujuan_retur select').val();
    var jml_retur = tr.find('td.jml_retur').find('input').val();
    var brt_retur = tr.find('td.jml_retur').attr('data-brt_retur_baru');

    if (empty(brt_retur))
        brt_retur = tr.find('td.berat_retur').text();

    kode_barang_arr.push(kode_barang);
    jml_retur_arr.push(parseInt(jml_retur));
    brt_retur_arr.push(brt_retur);
    /*
        var pad = '00';
        var temp_tgl_retur = tgl_retur.split(' ');
        var dd = parseInt(temp_tgl_retur[0]);
        var mm = (months.indexOf(temp_tgl_retur[1]) >= 0) ? (months.indexOf(temp_tgl_retur[1]) + 1) : (months_id.indexOf(temp_tgl_retur[1]) + 1);
        var yy = parseInt(temp_tgl_retur[2]);
        tgl_retur = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length);
    */
    for (var i = 0; i < jml_retur_arr.length; i++) {
        if (jml_retur_arr[i] <= 0) {

            toastr.warning("Jumlah retur harus lebih besar dari 0", "Warning");
            return false;

        }
    }

    if (jml_retur_arr.length > 0) {
        bootbox.dialog({
            message: "Apakah Anda yakin untuk melanjutkan perubahan retur sisa pakan?",
            title: "Konfirmasi",
            buttons: {
                success: {
                    label: "Ya",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: "rekap_retur_pakan/retur_sisa_pakan/proses_pengajuan_retur_ubah/",
                                data: {
                                    no_reg: no_reg,
                                    no_retur: no_retur,
                                    // tgl_retur: tgl_retur,
                                    kode_barang: kode_barang_arr,
                                    jml_retur: jml_retur_arr,
                                    brt_retur: brt_retur_arr,
                                    keterangan1: keterangan1
                                }
                            })
                            .done(function(data) {
                                if (data.result == "success") {
                                    toastr.success("Perubahan retur sisa pakan berhasil disimpan", 'Informasi');

                                    refreshDataRetur();
                                    edit_state = "closed";
                                } else {
                                    toastr.error("Perubahan retur sisa pakan gagal disimpan", 'Warning');
                                }
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
                    callback: function() {}
                }
            }
        });
    } else {
        toastr.warning("Mohon lakukan pengisian untuk jumlah sak retur", "Warning");
    }
}

function batalReturPakan(elm, val) {
    var tr = $(elm).parent().parent().parent().parent();
    var index = $(tr).find('td').eq(0).data("index");
    var jml = $(tr).find('td').eq(3).html();
    var alokasi = $(tr).find('td').eq(0).html();
    var tujuan = $(tr).find('td').eq(1).html();


    for (var i = 0; i < sisa_pakan_temp.length; i++) {
        var obj = sisa_pakan_temp[i];
        var sisa_jml = obj["jml_barang"];

        if (obj["kode_barang"] == alokasi_pakan[index] && alokasi.toUpperCase() == (alokasi_retur[index]).toUpperCase() && tujuan == alokasi_tujuan[index]) {
            sisa_pakan_temp[i]["jml_barang"] = parseInt(parseInt(sisa_jml) + parseInt(jml));


        }
    }

    alokasi_retur.splice(index, 1);
    alokasi_tujuan.splice(index, 1);
    alokasi_pakan.splice(index, 1);
    alokasi_sak.splice(index, 1);

    initializeAlokasiPakan(selected_farm, selected_noreg, 'deleteRow');
}

function resetReturPakan(elm, val) {
    var tr = $(elm).parent().parent().parent().parent();

    $(tr).find('td').eq(0).find('select').find('option').eq(0).prop('selected', true);
    $(tr).find('td').eq(2).find('select').find('option').eq(0).prop('selected', true);
    $(tr).find('td').eq(3).find('input').val('');

    initializeKandangTujuan($('select[name^=tujuan_retur]'), selected_farm, selected_noreg);
}

function resetReturPakanDeleteRow(elm, val) {
    if (alokasi_retur.length > 0) {
        index = (alokasi_retur.length) - 1;

        for (var i = 0; i < sisa_pakan_temp.length; i++) {
            var obj = sisa_pakan_temp[i];
            var sisa_jml = obj["jml_barang"];

            if (obj["kode_barang"] == alokasi_pakan[index]) {
                sisa_pakan_temp[i]["jml_barang"] = parseInt(parseInt(sisa_jml) + parseInt(alokasi_sak[index]));
            }
        }

        alokasi_retur.splice(index, 1);
        alokasi_tujuan.splice(index, 1);
        alokasi_pakan.splice(index, 1);
        alokasi_sak.splice(index, 1);
    }

    initializeAlokasiPakan(selected_farm, selected_noreg, 'deleteRow');
}

function simpanReturPakanDB() {
    var tgl_input = $('#inp_tgl_retur').val();
    var passed = true;
    var tgl_buat = $('#inp_today').val();
    var sisa_kode_pakan = new Array();
    var sisa_jml_pakan = new Array();
    var sisa_brt_pakan = new Array();

    $('input[name^="sisa_kode_pakan"]').each(function() {
        sisa_kode_pakan.push($(this).val());
    });

    $('input[name^="sisa_jml_pakan"]').each(function() {
        sisa_jml_pakan.push($(this).val());
    });

    $('input[name^="sisa_brt_pakan"]').each(function() {
        sisa_brt_pakan.push($(this).val());
    });

    if (passed && empty(tgl_input)) {
        toastr.warning("Mohon lakukan pengisian untuk Tgl. Retur", "Warning");
        passed = false;
    }

    if (alokasi_retur.length > 0) {} else {
        toastr.warning("Mohon lakukan pengisian untuk Alokasi Retur Sisa Pakan", "Warning");
        passed = false;
    }

    var pad = '00';
    var temp_tgl_arr = tgl_input.split(' ');
    var dd = parseInt(temp_tgl_arr[0]);
    var mm = (months.indexOf(temp_tgl_arr[1]) >= 0) ? (months.indexOf(temp_tgl_arr[1]) + 1) : (months_id.indexOf(temp_tgl_arr[1]) + 1);
    var yy = parseInt(temp_tgl_arr[2]);
    tgl_input = yy + '-' + (pad + mm).slice(-pad.length) + '-' + (pad + dd).slice(-pad.length);

    if (passed) {
        bootbox.dialog({
            message: "Apakah Anda yakin untuk melanjutkan proses penyimpanan?",
            title: "Konfirmasi",
            buttons: {
                success: {
                    label: "Ya",
                    className: "btn-primary",
                    callback: function() {
                        $.ajax({
                                type: 'POST',
                                dataType: 'json',
                                url: "rekap_retur_pakan/retur_sisa_pakan/proses_pengajuan_retur/",
                                data: {
                                    tgl_buat: tgl_buat,
                                    kode_farm: selected_farm,
                                    no_reg: selected_noreg,
                                    tgl_retur: tgl_input,
                                    alokasi_retur: alokasi_retur,
                                    alokasi_tujuan: alokasi_tujuan,
                                    alokasi_pakan: alokasi_pakan,
                                    alokasi_sak: alokasi_sak,
                                    sisa_kode_pakan: sisa_kode_pakan,
                                    sisa_jml_pakan: sisa_jml_pakan,
                                    sisa_brt_pakan: sisa_brt_pakan
                                }
                            })
                            .done(function(data) {
                                if (data.result == "success") {
                                    alokasi_retur = new Array();
                                    alokasi_tujuan = new Array();
                                    alokasi_pakan = new Array();
                                    alokasi_sak = new Array();

                                    $('#inp_kandang').val('');
                                    $('#pengajuan_tb_sisa > tbody').html('');
                                    initializeAlokasiPakan(selected_farm, selected_noreg);

                                    $('#modal_buat_retur_pakan').modal('hide');
                                    toastr.success("Pengajuan retur sisa pakan berhasil disimpan", 'Informasi');
                                    refreshDataRetur();
                                } else {
                                    toastr.error("Pengajuan retur sisa pakan gagal disimpan", 'Warning');
                                }
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
                        /*Jika sistem me-reset inputan maka hilangkan remark pada code di bawah ini!*/
                        /*
                        alokasi_retur = new Array();
                        alokasi_tujuan = new Array();
                        alokasi_pakan = new Array();
                        alokasi_sak = new Array();
						
                        initializeAlokasiPakan(selected_farm, selected_noreg);
                        */
                    }
                }
            }
        });
    }
}

function refreshDataRetur() {
    var kode_farm = $('#inp_farm').val();
    var tgl_awal = $('#inp_tglawal').val();
    var tgl_akhir = $('#inp_tglakhir').val();

    if (!empty(tgl_awal)) {
        var tgl_awal_arr = tgl_awal.split(" ");

        var index = (months.indexOf(tgl_awal_arr[1]) >= 0) ? months.indexOf(tgl_awal_arr[1]) : months_id.indexOf(tgl_awal_arr[1]);
        tahun_awal = tgl_awal_arr[2];
        bulan_awal = (parseInt(index) + 1);
        hari_awal = tgl_awal_arr[0];

        tgl_awal = tahun_awal + "-" + bulan_awal + "-" + hari_awal;
    }

    if (!empty(tgl_akhir)) {
        var tgl_akhir_arr = tgl_akhir.split(" ");
        var index = (months.indexOf(tgl_akhir_arr[1]) >= 0) ? months.indexOf(tgl_akhir_arr[1]) : months_id.indexOf(tgl_akhir_arr[1]);
        tahun_akhir = tgl_akhir_arr[2];
        bulan_akhir = (parseInt(index) + 1);
        hari_akhir = tgl_akhir_arr[0];

        tgl_akhir = tahun_akhir + "-" + bulan_akhir + "-" + hari_akhir;
    }

    $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "rekap_retur_pakan/retur_sisa_pakan/get_table_retur_sisa_pakan_list/",
            data: {
                kode_farm: kode_farm,
                tgl_awal: tgl_awal,
                tgl_akhir: tgl_akhir
            }
        })
        .done(function(html) {
            $('#tb_rekap').replaceWith(html);
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(dataJson) {});
}

$('#btnTampilkan').click(function() {
    refreshDataRetur();
});

function approve_retur(no_retur, lvl_user, tipe_retur) {
    var msg = "";
    if (can_approve.indexOf(lvl_user) >= 0) {
        msg = "Apakah Anda yakin melanjutkan approval?";
    } else if (can_accept.indexOf(lvl_user) >= 0) {
        msg = "Apakah Anda yakin melanjutkan serah terima pakan?";
    }
    var _dataRetur = {};
    var _error = 0,
        _message = [];
    /** pastikan yang diinput maksimal retur <= sisa pakan kandang */
    if (can_approve.indexOf(lvl_user) >= 0) {
        _dataRetur['detail'] = {};
        $('#modal_sisa input[name=jml_retur]').each(function() {
            if (parseInt($(this).val()) > $(this).data('maxretur')) {
                _error++;
                _message.push('maksimum entry retur > sisa pakan di kandang');
            }
            _dataRetur['detail'][$(this).data('kodebarang')] = parseInt($(this).val());
        });

        _dataRetur['kandang_tujuan'] = $('#print_alokasi_retur>select[name=kandang_tujuan]').val();
    }

    if (_error) {
        toastr.warning(_message.join(''));
        return;
    }

    bootbox.dialog({
        message: (can_approve.indexOf(lvl_user) >= 0) ? "Apakah Anda yakin melanjutkan approval?" : "Apakah Anda yakin melanjutkan serah terima pakan?",
        title: "Konfirmasi",
        buttons: {
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {
                    $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "rekap_retur_pakan/retur_sisa_pakan/proses_persetujuan/",
                            data: {
                                tipe_retur: tipe_retur,
                                no_retur: no_retur,
                                lvl_user: lvl_user,
                                data_retur: _dataRetur
                            }
                        })
                        .done(function(data) {
                            if (data.result == "success") {
                                var level_user = $('#level_user').val();
                                if (can_approve.indexOf(level_user) >= 0) {
                                    $('#id_user_approve').html('<center>( ' + data.nama_pegawai + ' )</center>');
                                    $('#inp_print_nama_approve').val(data.nama_pegawai);

                                    toastr.success("Pertanggungjawaban Sisa Pakan di Kandang berhasil di-approve", 'Informasi');
                                } else if (can_accept.indexOf(level_user) >= 0) {
                                    $('#id_user_terima').html('<center>( ' + data.nama_pegawai + ' )</center>');
                                    $('#inp_print_nama_terima').val(data.nama_pegawai);

                                    toastr.success("Serah Terima Sisa Pakan di Kandang berhasil di-approve", 'Informasi');
                                } else {

                                }

                                $('#btnPrint').removeClass("disabled");
                                refreshDataRetur();
                                $('#modal_sisa').modal("hide");
                            } else {
                                var level_user = $('#level_user').val();
                                if (can_approve.indexOf(level_user) >= 0) {
                                    toastr.error("Pertanggungjawaban Sisa Pakan di Kandang gagal di-approve", 'Peringatan');
                                } else if (can_accept.indexOf(level_user) >= 0) {
                                    toastr.error("Serah Terima Sisa Pakan di Kandang gagal di-approve", 'Peringatan');
                                } else {

                                }
                            }
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
                callback: function() {}
            }
        }
    });
}

function reject_retur(no_retur, lvl_user, tipe_retur) {
    var _content = ['<div class="dialog_reject">',
        '<div class="col-md-12">Mohon mengisi alasan reject retur sisa pakan (min. 10 karakter dan max. 100 karakter)</div>',
        '<div class="col-md-12">',
        '<textarea name="keterangan_reject" class="col-md-10" maxLength=100 onblur="aktifkanBtn(this)"></textarea>',
        '</div>',
        '<div class="col-md-12 new-line">',
        '<div class="col-md-2">',
        '<div name="simpanRejectBtn" class="btn btn-default disabled" onclick="simpanRejectRetur(this)">Simpan</div>',
        '</div>',
        '<div class="col-md-2">',
        '<div class="btn btn-default" onclick="bootbox.hideAll()">Batal</div>',
        '</div>',
        '</div>',
        '</div>'
    ];
    var _options = {
        title: 'Konfirmasi',
        message: _content.join(''),
        //	className : 'largeWidth',
    };

    bootbox.dialog(_options);
}

function aktifkanBtn(elm) {
    var ini = $(elm);
    var _p = ini.closest('.dialog_reject');
    if ($.trim(ini.val()).length >= 10) {
        _p.find('div[name=simpanRejectBtn]').removeClass('disabled');
    } else {
        _p.find('div[name=simpanRejectBtn]').addClass('disabled');
    }
}

function simpanRejectRetur(elm) {
    var ini = $(elm);
    var _error = 0;
    var _p = ini.closest('.dialog_reject');
    var _ket = $.trim(_p.find('textarea[name=keterangan_reject]').val());
    if (_ket.length == 0) {
        _error++;
        toastr.error('keterangan harus diisi');
    }
    if (!_error) {

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: "rekap_retur_pakan/retur_sisa_pakan/reject_retur/",
            data: {
                no_retur: $('#print_no_retur').text(),
                keterangan2: _ket
            },
            success: function(data) {
                if (data.status) {
                    bootbox.hideAll();
                    $('.modal').modal('hide');
                    toastr.success(data.message);
                    refreshDataRetur();
                } else {
                    toastr.error(data.message);
                }
            },
        });

    }
}

function detailRetur(elm) {

    //if (edit_state == "closed") {
    //    edit_state = "open";
    var nama_user = $('#nama_user').val();
    var kode_farm = $('#inp_farm').val();
    var nama_farm = $('#inp_nama_farm').val();
    var level_user = $('#level_user').val();

    var no_retur = $.trim($(elm).find('td:nth-child(1)').text());
    var tipe_retur = $(elm).find('td:nth-child(1)').data('tipe_retur');
    var no_reg = $(elm).find('td:nth-child(2)').text();
    var nama_kandang = $(elm).find('td:nth-child(3)').text();
    var tgl_tutup_siklus = $(elm).find('td:nth-child(6)').data('tgl_retur');
    var tgl_approve = $(elm).find('td:nth-child(13)').text();

    selected_noretur = no_retur;
    selected_noreg = no_reg;

    var jml = $(elm).find('td:nth-child(10)').text();
    var berat = $(elm).find('td:nth-child(11)').text();

    var ini = $(elm);

    /*init form approval*/
    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "rekap_retur_pakan/retur_sisa_pakan/get_retur_sisa_pakan_list/",
            data: {
                kode_farm: kode_farm,
                no_retur: no_retur
            }
        })
        .done(function(dataJson) {
            var data = dataJson.data;
            var sisa_pakan_kandang = dataJson.sisa_pakan;
            var obj = data[0];
            var user_buat = obj.user_buat;
            var brt_retur = obj.brt_retur;
            var jml_retur = obj.jml_retur;
            var kode_barang = obj.kode_barang;
            var kode_farm = obj.kode_farm;
            var nama_farm = obj.nama_farm + ' ( ' + obj.strain + ' )';
            var kode_kandang = obj.kode_kandang;
            var nama_barang = obj.nama_barang;
            var bentuk_barang = obj.bentuk_barang;
            var nama_buat = obj.nama_buat;
            var nama_approve = obj.nama_approve;
            var nama_terima = obj.nama_terima;
            var nama_kandang = obj.nama_kandang;
            var no_reg = obj.no_reg;
            var no_retur = obj.no_retur;
            var tgl_retur_ori = obj.tgl_retur_ori;
            var tgl_retur = obj.tgl_retur;
            var tgl_approve = obj.tgl_approve;
            var tgl_terima = obj.tgl_terima;
            var tujuan_retur = obj.tujuan_retur;
            var tipe_retur = obj.tipe_retur;
            var bisa_edit = obj.can_edit == '1' ? true : false;
            var statusReject = empty(obj.keterangan2) ? 0 : 1;
            var _maxReturPakan = {};

            $('#divAlasanReject').html('');
            $('#statusReject').text('');
            if (statusReject) {
                $('#divAlasanReject').html('<div><strong>Retur Dibatalkan</strong></div><div><u>Alasan : </u> <i>' + obj.keterangan2 + '</i></div>');
                $('#statusReject').text('[REJECT]');
            }

            if (!empty(sisa_pakan_kandang)) {
                var _tmp_text_sisa_kandang = [];
                for (var i in sisa_pakan_kandang) {
                    _tmp_text_sisa_kandang.push(sisa_pakan_kandang[i]['nama_barang'] + ' = ' + sisa_pakan_kandang[i]['jml_stok'] + ' sak');
                    _maxReturPakan[sisa_pakan_kandang[i]['kode_barang']] = sisa_pakan_kandang[i]['jml_stok'];
                }

                $('#sisa_pakan_kandang').text(_tmp_text_sisa_kandang.join(', '));
            }

            $('#inp_print_tgl_retur').val(tgl_retur_ori);
            $('#inp_print_nama_farm').val(nama_farm);
            $('#inp_print_nama_kandang').val(nama_kandang);
            $('#inp_print_no_retur').val(no_retur);
            $('#inp_print_no_reg').val(no_reg);

            if (empty(nama_approve)) {
                $('#titleretur').html("Pertanggung Jawaban Sisa Pakan di Kandang");
            } else {
                $('#titleretur').html('');
            }

            if (bisa_edit) {
                var _no_reg_tujuan_org = obj.kode_tujuan_retur;
                var _kode_tujuan_retur = obj.list_kode_tujuan_retur;
                var _html_dropdown_kandang = ['<select name="kandang_tujuan">'];
                var _selected_option = '';
                var _label_tujuan;
                for (var i in _kode_tujuan_retur) {
                    _selected_option = _no_reg_tujuan_org == _kode_tujuan_retur[i] ? 'selected' : '';
                    _label_tujuan = _kode_tujuan_retur[i].length < 12 ? 'Gudang' : 'Kandang';
                    _html_dropdown_kandang.push('<option ' + _selected_option + ' value="' + _kode_tujuan_retur[i] + '">' + _label_tujuan + '  ' + _kode_tujuan_retur[i].substr(-2) + ' </option>');
                }
                _html_dropdown_kandang.push('</select>');
                $('#print_alokasi_retur').html(_html_dropdown_kandang.join(''));
            } else {
                $('#print_alokasi_retur').html(tujuan_retur);
            }

            $('#titlefarm').html(nama_farm);
            $('#print_kandang_asal').html(nama_kandang);
            $('#print_no_retur').html(no_retur);

            $('#print_tgl_retur').html(tgl_retur);

            if (!empty(nama_approve)) {
                $('#id_user_approve').html('<center>(' + nama_approve + ')</center>');
            } else {
                $('#id_user_approve').html('<center>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</center>');
            }

            if (!empty(nama_terima)) {
                $('#id_user_terima').html('<center>(' + nama_terima + ')</center>');
            } else {
                $('#id_user_terima').html('<center>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</center>');
            }

            $('#id_user_buat').html("( " + nama_buat + " )");

            if (can_propose.indexOf(level_user) >= 0) {
                if (empty(nama_approve)) {
                    var tgl_retur = tgl_tutup_siklus;

                    var tgl_retur_arr = tgl_retur.split(" ");
                    var index = (months.indexOf(tgl_retur_arr[1]) >= 0) ? months.indexOf(tgl_retur_arr[1]) : months_id.indexOf(tgl_retur_arr[1]);
                    tahun_akhir = tgl_retur_arr[2];
                    bulan_akhir = (parseInt(index) + 1);
                    hari_akhir = tgl_retur_arr[0];

                    tgl_retur = tahun_akhir + "-" + bulan_akhir + "-" + hari_akhir;

                    var htlm_datepicker = '' +
                        '<form class="form-inline">' +
                        '	<div class="form-group">' +
                        '		<div class="input-group date tgl_retur_row">' +
                        '			<input type="text" name="tgl_retur_row" id="inp_tgl_retur_row" style="width:120px;" class="form-control" readonly />' +
                        '			<span class="input-group-addon">' +
                        '				<span class="glyphicon glyphicon-calendar"></span>' +
                        '			</span>' +
                        '		</div>' +
                        '	</div>' +
                        '</form>';

                    var jml = $(elm).find('td.jml_retur').text();
                    var berat = $(elm).find('td.berat_retur').text();
                    var html_sak = '<input type="text" size=2 style="text-align:center" name="jumlah_retur_row[]" value="' + jml + '" class="form-control" onkeyup="cekJmlReturNow(this)">';

                    var html_centang = '<div class="row"><div class="col-md-4"><span class="berat">' + berat + '</span></div><div class="control col-md-4">' +
                        '			<button type="button" class="btn btn-primary" onclick="simpanReturPakanRow(this, 1)">' +
                        '				<span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span>' +
                        '			</button>' +
                        '			<button type="button" class="btn btn-danger" onclick="batalReturPakanRow(this, 1)">' +
                        '				<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                        '			</button>' +

                        '</div></div>';


                    //                $(elm).find('td.tgl_retur').html(htlm_datepicker);
                    $(elm).find('td.tujuan_retur').html(_html_dropdown_kandang.join(''));
                    $(elm).find('td.tujuan_retur').find('select').addClass('form-control');
                    $(elm).find('td.jml_retur').html(html_sak);

                    $(elm).find('td.berat_retur').html(html_centang);

                    $('.tgl_retur_row').datetimepicker({
                        pickTime: false,
                        format: "DD MMM YYYY"
                    });

                    var returDate = new Date(tgl_retur_ori);
                    //                    $(elm).find('.tgl_retur_row').data("DateTimePicker").setDate(returDate);

                } else {

                }
            } else if (can_approve.indexOf(level_user) >= 0) {
                var row_detil = new Array();
                for (var i = 0; i < data.length; i++) {
                    var obj = data[i];
                    var _kode_barang = obj.kode_barang;
                    var _nama_barang = obj.nama_barang;
                    var _jml_retur = obj.jml_retur;
                    var _brt_retur = obj.brt_retur;
                    var _bentuk_barang = obj.bentuk_barang;
                    var _readonly_input = '';
                    var _input_jml_retur = _jml_retur;
                    if (!empty(nama_approve)) {
                        _readonly_input = 'readonly';
                    } else {
                        _input_jml_retur = '<input onchange="update_berat_retur(this)" data-kodebarang="' + _kode_barang + '" data-maxretur="' + _maxReturPerbarang + '" data-brt_rata="' + (_brt_retur / _jml_retur) + '" name="jml_retur"' + _readonly_input + ' value="' + _jml_retur + '" />';
                    }
                    var _maxReturPerbarang = _maxReturPakan[_kode_barang] != undefined ? _maxReturPakan[_kode_barang] : 0;
                    var detil = '<tr>' +
                        '<td class="vert-align">' + _kode_barang + '</td>' +
                        '<td class="vert-align">' + _nama_barang + '</td>' +
                        '<td class="vert-align jml">' + _input_jml_retur + '</td>' +
                        '<td class="vert-align berat">' + number_format(_brt_retur, 1, ',', '.') + '</td>' +
                        '<td class="vert-align">' + _bentuk_barang + '</td>' +
                        '</tr>';

                    row_detil.push(detil);
                }


                $('#tb_sisa > tbody').html(row_detil.join(''));

                if (empty(nama_approve)) {
                    var _divApprove = [
                        '<button name="tombolPrint" onclick="approve_retur(\'' + no_retur + '\',\'' + level_user + '\',\'' + tipe_retur + '\')" class="btn btn-primary">Approve</button>',
                    ];
                    if (can_approve.indexOf(level_user) >= 0) {
                        _divApprove.push('<button name="tombolPrint" onclick="reject_retur(\'' + no_retur + '\',\'' + level_user + '\',\'' + tipe_retur + '\')" class="btn btn-danger">Reject</button>');
                    }
                    $('#id_user_approve').html('<div id="app_point">(&nbsp;&nbsp;' + _divApprove.join('&nbsp;&nbsp;') + '&nbsp;&nbsp;)</div>');
                }
                $('#modal_sisa').find('input[name=jml_retur]').numeric({});
                $('#modal_sisa').modal("show");
            } else if (can_accept.indexOf(level_user) >= 0 && tipe_retur == 'gudang' && !empty(nama_approve)) {
                var row_detil = new Array();
                for (var i = 0; i < data.length; i++) {
                    var obj = data[i];
                    var _kode_barang = obj.kode_barang;
                    var _nama_barang = obj.nama_barang;
                    var _jml_retur = obj.jml_retur;
                    var _brt_retur = obj.brt_retur;
                    var _bentuk_barang = obj.bentuk_barang;

                    var detil = '<tr>' +
                        '<td class="vert-align">' + _kode_barang + '</td>' +
                        '<td class="vert-align">' + _nama_barang + '</td>' +
                        '<td class="vert-align">' + _jml_retur + '</td>' +
                        '<td class="vert-align">' + _brt_retur + '</td>' +
                        '<td class="vert-align">' + _bentuk_barang + '</td>' +
                        '</tr>';

                    row_detil.push(detil);
                }


                $('#tb_sisa > tbody').html(row_detil.join(''));

                if (empty(nama_terima)) {
                    $('#id_user_terima').html('<div id="app_point">(&nbsp;&nbsp;&nbsp;<button name="tombolPrint" onclick="approve_retur(\'' + no_retur + '\',\'' + level_user + '\',\'' + tipe_retur + '\')" class="btn btn-primary">Approve</button>&nbsp;&nbsp;&nbsp;)</div>');
                }

                $('#modal_sisa').modal("show");
            } else {

            }
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(dataJson) {});
    /*end of init*/
    //}
};

/*end of Baru..........*/

$('#pengajuan_btnPrint').click(function(e) {
    e.preventDefault();

    bootbox.dialog({
        message: "Apakah Anda yakin melakukan retur pakan ke gudang?",
        title: "Konfirmasi",
        buttons: {
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {
                    $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "rekap_retur_pakan/proses_pengajuan/",
                            data: {
                                no_retur: selected_noretur,
                                no_reg: selected_noreg
                            }
                        })
                        .done(function(data) {
                            if (data.result == "success") {
                                toastr.success("Pengajuan Retur Pakan berhasil dilakukan", 'Informasi');

                                $('#pengajuan_modal_sisa').modal("hide");
                                refreshData();
                            } else {
                                toastr.warning("Pengajuan Retur Pakan gagal dilakukan", 'Informasi');
                            }
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
                callback: function() {}
            }
        }
    });
});

function approve(status) {
    bootbox.dialog({
        message: "Apakah Anda yakin melakukan persetujuan retur pakan ke gudang?",
        title: "Konfirmasi",
        buttons: {
            success: {
                label: "Ya",
                className: "btn-primary",
                callback: function() {
                    $('#print_sj_retur').text(selected_noretur);
                    $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "rekap_retur_pakan/proses_persetujuan/",
                            data: {
                                no_retur: selected_noretur,
                                no_reg: selected_noreg
                            }
                        })
                        .done(function(data) {
                            if (data.result == "success") {
                                var level_user = $('#level_user').val();
                                if (can_approve.indexOf(level_user) >= 0) {
                                    $('#id_user_approve').html('<center>(' + data.nama_pegawai + ')</center>');
                                    $('#inp_print_nama_approve').val(data.nama_pegawai);
                                } else if (can_accept.indexOf(level_user) >= 0) {
                                    $('#id_user_terima').html('<center>(' + data.nama_pegawai + ')</center>');
                                    $('#inp_print_nama_terima').val(data.nama_pegawai);
                                } else {

                                }

                                $('#btnPrint').removeClass("disabled");
                            }
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
                callback: function() {}
            }
        }
    });
}

function refreshData() {
    var kode_farm = $('#inp_farm').val();
    var tgl_awal = $('#inp_tglawal').val();
    var tgl_akhir = $('#inp_tglakhir').val();

    if (!empty(tgl_awal)) {
        var tgl_awal_arr = tgl_awal.split(" ");

        var index = (months.indexOf(tgl_awal_arr[1]) >= 0) ? months.indexOf(tgl_awal_arr[1]) : months_id.indexOf(tgl_awal_arr[1]);
        tahun_awal = tgl_awal_arr[2];
        bulan_awal = (parseInt(index) + 1);
        hari_awal = tgl_awal_arr[0];

        tgl_awal = tahun_awal + "-" + bulan_awal + "-" + hari_awal;
    }

    if (!empty(tgl_akhir)) {
        var tgl_akhir_arr = tgl_akhir.split(" ");
        var index = (months.indexOf(tgl_akhir_arr[1]) >= 0) ? months.indexOf(tgl_akhir_arr[1]) : months_id.indexOf(tgl_akhir_arr[1]);
        tahun_akhir = tgl_akhir_arr[2];
        bulan_akhir = (parseInt(index) + 1);
        hari_akhir = tgl_akhir_arr[0];

        tgl_akhir = tahun_akhir + "-" + bulan_akhir + "-" + hari_akhir;
    }

    $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "rekap_retur_pakan/get_retur_pakan_list/",
            data: {
                kode_farm: kode_farm,
                tgl_awal: tgl_awal,
                tgl_akhir: tgl_akhir
            }
        })
        .done(function(data) {
            var html = new Array();

            for (var i = 0; i < data.length; i++) {
                var tgl_retur = (!empty(data[i]["tgl_retur"])) ? data[i]["tgl_retur"] : "";
                var tgl_approve = (!empty(data[i]["nama_approve"])) ? data[i]["tgl_approve"] : "";
                var tgl_terima = (!empty(data[i]["nama_terima"])) ? data[i]["tgl_terima"] : "";
                var no_retur = (!empty(data[i]["tgl_retur"])) ? data[i]["no_retur"] : "";

                var jml_retur = (!empty(tgl_retur) && !empty(data[i]["jml_retur"])) ? data[i]["jml_retur"] : "";
                var brt_retur = (!empty(tgl_retur) && !empty(data[i]["brt_retur"])) ? data[i]["brt_retur"] : "";

                var jml_putaway = (!empty(tgl_terima) && !empty(data[i]["jml_retur"])) ? data[i]["jml_retur"] : "";
                var brt_putaway = (!empty(tgl_terima) && !empty(data[i]["brt_putaway"])) ? data[i]["brt_putaway"] : "";

                html[i] = '<tr>' +
                    '<td class="hidden">' + data[i]["no_retur"] + '</td>' +
                    '<td class="hidden">' + data[i]["no_reg"] + '</td>' +
                    '<td class="hidden">' + data[i]["kode_kandang"] + '</td>' +
                    '<td class="link">' + data[i]["nama_kandang"] + '</td>' +
                    '<td class="link">' + data[i]["tgl_tutupsiklus"] + '</td>' +
                    '<td class="link">' + data[i]["nama_barang"] + '</td>' +
                    '<td class="link">' + no_retur + '</td>' +
                    '<td class="link">' + tgl_retur + '</td>' +
                    '<td class="link" align="right">' + jml_retur + '</td>' +
                    '<td class="link" align="right">' + brt_retur + '</td>' +
                    '<td class="link">' + tgl_approve + '</td>' +
                    '<td class="link">' + tgl_terima + '</td>' +
                    '<td class="link" align="right">' + jml_putaway + '</td>' +
                    '<td class="link" align="right">' + brt_putaway + '</td>' +
                    '</tr>';
            }

            $('#tb_rekap > tbody').html(html.join(''));
        })
        .fail(function(reason) {
            console.info(reason);
        })
        .then(function(data) {});
}

function update_berat_retur(elm) {
    var _tr = $(elm).closest('tr');
    var _n = parseInt($(elm).val());
    var _max = $(elm).data('maxretur');
    var _rata = $(elm).data('brt_rata');
    if (_n > _max) {
        _n = _max;
        $(elm).val(_n);
    }
    _tr.find('td.berat').text(number_format((_n * _rata), 1, ',', '.'));
}