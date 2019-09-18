var search = false;
var page_number = 0;
var total_page = null;

var form_mode = "";
var selected_uom = "";

function pilih_uom(e) {
    var id_budget = $(e).find('td.id_budget').text();
    var tara = $(e).find('td.tara').text();
    $('#inp_siklus').attr('data-id_budget', id_budget);
    $('#inp_siklus').val(tara + ' - ' + id_budget);
    $('#modal_master_uom').modal("hide");
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

    id_budget = $('#q_id_budget').val();
    nama_budget = $('#q_nama_budget').val();
    kategori = $('#q_kategori').val();
    status = $('#q_status').val();

    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/budget_glangsing/get_pagination/",
        data : {
            id_budget : id_budget,
            nama_budget : nama_budget,
            kategori : kategori,
            status : status,
            page_number : page_number,
            search : search
        }
    }).done(function(data) {
        $("#master-budget").remove();
        var _tt = '<table id="master-budget" class="table table-bordered table-striped">'+
            '<thead>'+
                '<tr>'+
                    '<td class="text-center table-header">ID Budget</td>'+
                    '<td class="text-center table-header">Nama Budget</td>'+
                    '<td class="text-center table-header">Kategori</td>'+
                    '<td class="text-center table-header">Status</td>'+
                    //'<td class="text-center table-header"></td>'+
                '</tr>'+
            '</thead>'+
            '<tbody>'+
            '</tbody>'+
          '</table>';
          _tt = $(_tt);
          _tt.insertAfter('#search_table');
                $.each(data, function(key, data) {
                    var _html = '<tr ondblclick="edit_budget(\''+ data.KODE_BUDGET +'\')">';
                    _html += '<td class="id_budget" align="center">' + data.KODE_BUDGET + '</td>';
                    _html += '<td class="nama_budget" align="left">' + data.NAMA_BUDGET + '</td>';
                    _html += '<td class="kategori" align="center">' + data.KATEGORI_BUDGET + '</td>';
                    _html += '<td class="status" align="center">' + data.STATUS + '</td>';
                    //_html += '<td class="status" align="center"> <span style="width: 115px;" class="col-md-1 edit"><i class="btn-glyphicon glyphicon glyphicon-pencil" onclick="edit_budget(\''+ data.KODE_BUDGET +'\')"></i></span> </td>';
                    _html += '</tr>';
                    _tt.find('tbody').append(_html);
                });

                var tabel_riwayat = $('table#master-budget');
                if(tabel_riwayat.length > 0) {
                    tabel_riwayat.scrollabletable({
                        'max_height_scrollable' : 400
                    });
                }

                /*
                $("input.input_keterangan").spinner({
                    min : 1
                });

                */
                $('td.tara input').numeric({
                    allowPlus : false,
                    allowMinus : false,
                    allowThouSep : false,
                    allowDecSep : true
                });

                var tr = $('#master-budget tbody tr:first');
                tr.find('td.tara input.input_tara').focus().select();


    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
}

function master_uom() {
    $.ajax({
        type : 'POST',
        url : "master/budget_glangsing/get_master_uom/",
        data : {}
    }).done(function(data) {
        $('#modal_master_uom .modal-body').html(data);
        $('#modal_master_uom').modal('show');
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
}


$(document).ready(function() {
    goSearch();


    $('#q_tanggal_penimbangan').datepicker({
        dateFormat : 'dd M yy'
    });
});

$('input[type="checkbox"]').click(function() {
    goSearch();
});

$('.field_input').keyup(function() {
    checkInput();
});

$('#master-budget').on('click', 'tr', function() {
    selected_uom = $(this).find('td:nth-child(1)').text();
});

$('#master-budget > tbody').on('', 'tr', function() {
    selected_uom = $(this).attr('data-id');
    form_mode = "ubah";
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/budget_glangsing/get_uom/",
        data : {
            id_budget : selected_uom,
        }
    }).done(function(data) {
        $('#inp_id_budget').val(data.UOM);
        $('#inp_tara').val(data.tara);
        $('#inp_siklus').val(data.tara_BASE_UOM + ' - ' + data.UOM_BASE_UOM);
        $('#inp_siklus').attr('data-id_budget', data.UOM_BASE_UOM);
        $('#inp_konversi').val(data.KONVERSI);

        $('#inp_id_budget').attr("readonly", true);
        $('#inp_siklus').attr("readonly", true);

        $('#btnSimpan').hide();
        $('#btnUbah').show();
        //$('#btnUbah').removeClass('disabled');

        $('#modal_uom').modal("show");
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
        });
});

$("#btnTambah").click(function() {
    resetInput();
    form_mode = "tambah";

    $('#inp_id_budget').attr("readonly", false);
    $('#inp_siklus').attr("readonly", true);
    $('#btnSimpan').show();
    $('#btnUbah').hide();

    $('#modal_uom').modal("show");
});

$("#btnBatal").click(function() {
    $('#modal_uom').modal("hide");
    resetInput();
});

$("#btnKembali").click(function() {
    $('#modal_master_uom').modal("hide");
});

$("#btnSimpan").click(function() {
    var passed = true;
	id_budget = $('#inp_id_budget').val();
    nama_budget = $('#inp_nama_budget').val();
    kategori_budget = $('#inp_kategori').val();
    action = $('#prop').val();
	
	if($('#inp_status').is(':checked')){
        status = 'A';
    }
	else{
		status = 'N';
	}
    //alert(kategori_budget);
    konversi = $('#inp_konversi').val();
	
    if (id_budget && nama_budget) {
		if(action == 'add'){
			$.ajax({
				type : 'POST',
				dataType : 'json',
				url : "master/budget_glangsing/cek_budget/",
				data : {
					id_budget : id_budget,
				}
			}).done(function(data) {
				if (data.passed == false) {
					notificationBox("Kode budget glangsing <strong>" + id_budget + "</strong> sudah terdaftar. Mohon diperhatikan kembali.");
				}else{
					var data = "Apakah Anda yakin akan Menyimpan data Budget Glangsing ini?";
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
										url : "master/budget_glangsing/save_budget/",
										data : {
											id_budget : id_budget,
											nama_budget : nama_budget,
											kategori_budget : kategori_budget,
											action : action,
										}
									}).done(function(data) {
										//alert(data.success);
										if (data.success == true) {
											notificationBox("Penyimpanan data Budget Glangsing dengan kode " + id_budget + " berhasil dilakukan.");
											$('.form_budget').modal("hide");
											resetInput();

											goSearch();
										}else{
											notificationBox("Penyimpanan data Budget Glangsing dengan kode " + id_budget + " gagal dilakukan");
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
			}).fail(function(reason) {
				console.info(reason);
			}).then(function(data) {
			});
		}
		else{
			var data = "Apakah Anda yakin akan Menyimpan data Budget Glangsing ini?";
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
								url : "master/budget_glangsing/save_budget/",
								data : {
									id_budget : id_budget,
									nama_budget : nama_budget,
									kategori_budget : kategori_budget,
									status : status,
									action : action,
								}
							}).done(function(data) {
								//alert(data.success);
								if (data.success == true) {
									notificationBox("Penyimpanan data perubahan Budget Glangsing dengan kode " + id_budget + " berhasil dilakukan.");
									$('.form_budget').modal("hide");
									resetInput();

									goSearch();
								}else{
									notificationBox("Penyimpanan data perubahan Budget Glangsing dengan kode " + id_budget + " gagal dilakukan");
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
		
    } else {
        notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
    }
});

$("#btnUbah").click(function() {
    var passed = true;

	id_budget = $('#inp_id_budget').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').attr('data-id_budget');
    konversi = $('#inp_konversi').val();

	if(id_budget == siklus)
		passed = false;

	if(!passed){
		bootbox.alert("Kolom id_budget Dasar tidak boleh sama dengan kolom id_budget.");
		return false;
	}

    if (id_budget && tara && konversi) {

   var data = "Apakah Anda yakin akan Mengubah data id_budget ini?";
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
                                    url : "master/budget_glangsing/update_uom/",
                                    data : {
                                        id_budget : id_budget,
                                        tara : tara,
                                        siklus : siklus,
                                        konversi : konversi
                                    }
                                }).done(function(data) {
                                    if (data.result == "success") {
                                        notificationBox("Perubahan data id_budget " + id_budget + " berhasil dilakukan.");

                                        $('#modal_uom').modal("hide");
                                        resetInput();

                                        goSearch();
                                    } else {
                                        if (data.check == "failed")
                                                notificationBox("id_budget " + id_budget + " sudah terdaftar.");
                                        else
                                            notificationBox("Perubahan data id_budget " + id_budget + " gagal dilakukan");
                                    }
                                }).fail(function(reason) {
                                    console.info(reason);
                                }).then(function(data) {
                                });
                            }
                        }
                    }
                });
    } else {
        notificationBox("Parameter data yang Anda inputkan belum lengkap. Mohon lengkapi isian data terlebih dahulu.");
    }
});

/*
 * FUNCTION
 */

function resetInput() {
    $('#inp_id_budget').val('');
    $('#inp_tara').val('');
    $('#inp_siklus').val('');
    $('#inp_siklus').attr('data-id_budget', '');
    $('#inp_konversi').val('');
}

function goSearch() {
    page_number = 0;
    search = true;
    getReport(page_number);
}

function checkInput() {

    id_budget = $('#inp_id_budget').val();
    tara = $('#inp_tara').val();
    siklus = $('#inp_siklus').val();
    konversi = $('#inp_konversi').val();
/*
    if (id_budget != "" && tara != "" && konversi != "") {
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

function set_berat_budget(elm){
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var _aksi = tr.find('td.keterangan span.set').attr('data-aksi');
    var id_budget = tr.find('td.id_budget').text();
    var tara = tr.find('td.tara input.input_tara').val();
    var data_tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var keterangan = tr.find('td.keterangan input.input_keterangan').val();
    var tgl_timbang  = (data_tara) ? tr.find('td.tanggal').attr('data-tanggal-db') : '';
    var params = {
        'id_budget' : id_budget,
        'tara' : tara,
        'keterangan' : keterangan,
        'tgl_timbang' : tgl_timbang
    }
    if(tara && keterangan){
        if(_aksi == 'U'){
            dialog_set_berat_budget('U', params);
        }
        else{
            dialog_set_berat_budget('I', params);
        }
    }
    else{

        toastr.warning('Mohon melakukan pengisian secara lengkap.', 'Informasi');
        var i = 0;
        $.each(tr.find('input'), function(){
            var _value = $(this).val();
            //$(this).removeClass('red_border');
            if(!_value){
                //$(this).focus().select();.addClass('red_border');
                if(i==0){
                    $(this).focus().select();
                }

                i++;
            }
        });

    }
}

function dialog_set_berat_budget(aksi , params){
    var data = (aksi == 'U') ? "Apakah Anda yakin akan melanjutkan perubahan?" : "Apakah Anda yakin akan menyimpan penimbangan budget?";
    var msg_sukses = (aksi == 'U') ? "Data budget berhasil diubah." : "Penimbangan budget baru berhasil disimpan.";
    var msg_gagal = (aksi == 'U') ? "Data budget gagal diubah." : "Penimbangan budget baru gagal disimpan.";
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

                    simpan_berat_budget(params,function(result){

                        if(result == 1){
                            goSearch();
                            toastr.success(msg_sukses,'Informasi');

                            return true;
                        }
                        else if(result == 2){
                            toastr.warning('Tidak bisa ubah data budget pada hari H.','Informasi');

                            return false;
                        }
                        else{
                            toastr.error(msg_gagal,'Informasi');
                            return false;
                        }
                    });
                }
            }
        }
    });
}

function reset(elm){
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var id_budget = tr.find('td.id_budget').text();
    var tanggal = tr.find('td.tanggal').attr('data-tanggal');
    var tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var tara_span_last = tr.find('td.tara span.tooltips-base').html();
    var keterangan = tr.find('td.keterangan input.input_keterangan').attr('data-keterangan');
    //tara = tara.replace('.',',');
    //tara = tara.replace(',','.');
    tr.find('td.tanggal').html(tanggal);
    var _html_tara = '<span>'+tara+'</span>';
    _html_tara += '<span class="tooltips-base tooltips-default hide">'+tara_span_last+'</span>';

    tr.find('td.tara').html(_html_tara);
    var _html_keterangan = '<div class="form-inline"><span style="width:160px" class="col-md-1">'+keterangan+'</span>';
    _html_keterangan += '<span style="width:115px" class="col-md-1 reset">'+tr.find('td.keterangan span.reset').html()+'</span>';
    _html_keterangan += '<span style="width:115px" class="col-md-1 set">'+tr.find('td.keterangan span.set').html()+'</span>';
    _html_keterangan += '<span style="width:115px" class="col-md-1 edit">'+tr.find('td.keterangan span.edit').html()+'</span></div>';
    tr.find('td.keterangan').html(_html_keterangan);
    tr.find('td.keterangan span.reset, td.keterangan span.set').addClass('hide');
    tr.find('td.keterangan span.edit').removeClass('hide');

}

function edit_berat_budget(elm){
    var tr = $(elm).closest('tr');
    var _aksi_reset = tr.find('td.keterangan span.reset').hasClass('hide');
    var _aksi_set = tr.find('td.keterangan span.set').hasClass('hide');
    var id_budget = tr.find('td.id_budget').text();
    var tara = tr.find('td.tara span:first').text();
    var tara_span_last = tr.find('td.tara span.tooltips-base').html();
    var keterangan = tr.find('td.keterangan').text();
    /* periksa dulu apakah masih memiliki stok atau tidak */
    $.get('master/budget_glangsing/check_stok',{idbudget : id_budget},function(data){
      if(data.stok > 0){
        bootbox.alert('Terdapat stok pakan pada budget tersebut');
      }else{
        if(_aksi_reset && _aksi_set){
            //tara = tara.replace(/[.]/gi, '');
            //tara = tara.replace(',','.');
            tr.find('td.tanggal').text('');
            var _html_tara = '<input data-toggle="tooltip" data-placement="right" title="" onpaste="get_berat_timbang(this)" onkeyup="replace_timbang(this)" onclick="selected(this)" readonly onmouseover="view_tooltip(this)" data-tara="'+tara+'" class="text-center input_tara" name="input_tara" style="width:100px;">';
            _html_tara += '<span class="tooltips-base tooltips-default hide">'+tara_span_last+'</span>';
            tr.find('td.tara').html(_html_tara);
            tr.find('td.tara span.tooltips-content').text(tara);
            var _html_keterangan = '<div class="form-inline"><span style="width: 150px;" class="col-md-1"><input onkeyup="kontrol_keterangan(this);" data-keterangan="'+keterangan+'" class="text-center input_keterangan" name="input_keterangan" style="width:150px;"></span>';
            _html_keterangan += '<span style="width: 35px;" class="col-md-1 reset">'+tr.find('td.keterangan span.reset').html()+'</span>';
            _html_keterangan += '<span style="width: 35px;" class="col-md-1 set" data-aksi="U">'+tr.find('td.keterangan span.set').html()+'</span>';
            _html_keterangan += '<span style="width: 35px;" class="col-md-1 edit">'+tr.find('td.keterangan span.edit').html()+'</span></div>';
            tr.find('td.keterangan').html(_html_keterangan);
            tr.find('td.keterangan span.reset, td.keterangan span.set').removeClass('hide');
            tr.find('td.keterangan span.edit').addClass('hide');


            tr.find('td.tara input.input_tara').focus().select();
        }


          $('td.tara input').numeric({
              allowPlus : false,
              allowMinus : false,
              allowThouSep : false,
              allowDecSep : true
          });
      }
    },'json');
}

function simpan_berat_budget(params, callback){
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/budget_glangsing/simpan_berat_budget/",
        data : {
            params : params
        }
    }).done(function(result) {
        callback(result);
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
    });
}

function view_tooltip(elm){
    /*
    $('td.tara input').removeClass('input_tara');
    var tr = $(elm).closest('tr');
    tr.find('td.tara input').addClass('input_tara');
    var _aksi_reset = tr.find('td.keterangan span.reset').hasClass('hide');
    var id_budget = tr.find('td.id_budget').text();
    var siklus = tr.find('td.siklus').attr('data-kode-siklus');
    var tara = tr.find('td.tara input.input_tara').attr('data-tara');
    var new_tara = $(elm).val();
    tr.find('td.tara input.input_tara').attr('title', tara);
    var title = tr.find('td.tara input.input_tara').attr('title');
    var keterangan = tr.find('td.keterangan input.input_keterangan').val();
    var failed = 1;
    if(tara && new_tara){
        if(parseFloat(tara) == parseFloat(new_tara)){
            failed = 0;
        }
    }*/
        /*
        tr.find('td.tara input.input_tara').tooltipster({
            animation: 'fade',
            delay: 500,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'custom',
            hideOnClick: true,
            position: 'right'
        });
        */
        /*
    if(title && failed == 1){
        //tr.find('td.tara input.input_tara').tooltipster('show');
        tr.find('td.tara span.tooltips-base').removeClass('hide').fadeIn(3000);
    }
    else{
        //tr.find('td.tara input.input_tara').tooltipster('hide');
        tr.find('td.tara span.tooltips-base').addClass('hide').fadeOut(3000);
    }
    */

}

function kontrol_keterangan(elm){
    set_class_tara(elm);
    var tr = $(elm).closest('tr');
    var keterangan = $(elm).val();
        /*
        tr.find('td.tara input.input_tara').tooltipster({
            animation: 'fade',
            delay: 500,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'custom',
            hideOnClick: true,
            position: 'right'
        });
        */
    if(keterangan){
        //tr.find('td.tara input.input_tara').tooltipster('hide');
        tr.find('td.tara span.tooltips-base').addClass('hide').fadeOut(3000);
    }
}

function set_class_tara(elm){
    var tr = $(elm).closest('tr');
    if(!tr.find('td.tara input').hasClass('input_tara')){
        tr.find('td.tara input').addClass('input_tara');
    }
}

function kontrol_checkbox(elm){
    var _val = 0;
    if($(elm).is(':checked')){
        _val = 1;
    }
    $(elm).val(_val);
}

function add_budget(elm){
    $('#master-budget tbody tr').removeClass('double_click');
    $(elm).addClass('double_click');
    var kode_budget = $(elm).find('td.id_budget').text();
    /* periksa dulu apakah masih memiliki stok atau tidak */
    $.ajax({
		type : 'POST',
		dataType : 'html',
		url : "master/budget_glangsing/add_budget/",
		data : {
			kode_budget : kode_budget
		},
		async : false,
	}).done(function(data) {
		var box = bootbox.dialog({
			message : data,
			title : 'Tambah Budget Glangsing',
			className : "medium-large form_budget",
			idName:'form_budget'
		});
		$('#inp_id_budget').removeAttr('readonly');
		$('#grp-status').hide();
		$('#prop').val('add');
		$('#btnSimpan').text('Simpan');
	}).fail(function(reason) {
		console.info(reason);
	});
}
function edit_budget(kode_budget){
    $.ajax({
		type : 'POST',
		dataType : 'html',
		url : "master/budget_glangsing/edit_budget/",
		data : {
			kode_budget : kode_budget
		},
		async : false,
	}).done(function(data) {
		
		var box = bootbox.dialog({
			message : data,
			title : 'Edit Budget Glangsing',
			className : "medium-large form_budget"
		});
		
		$.ajax({
			type : 'POST',
			dataType : 'JSON',
			url : "master/budget_glangsing/load_budget/",
			data : {
				kode_budget : kode_budget
			},
			async : false,
		}).done(function(result) {
			$('#inp_id_budget').val(result.id_budget);
			$('#inp_kategori').val(result.kategori_budget);
			$('#inp_nama_budget').val(result.nama_budget);
			if(result.status == 'A'){
				$('#inp_status').attr('checked',true);
			}
			else{
				$('#inp_status').attr('checked',false);
			}
		}).fail(function(reason) {
			console.info(reason);
		});
		
		$('#inp_id_budget').attr('readonly', true);
		$('#grp-status').show();
		$('#prop').val('edit');
		$('#btnSimpan').text('Ubah');
	}).fail(function(reason) {
		console.info(reason);
	});
}

function dialog_add_budget(html){
    var data = html;
}
function dialog_edit_budget(html){
    var data = html;
}

function dialog_status_budget(elm){
    var kode_budget = $(elm).closest('tr').find('td.id_budget').text();
    var status_budget = $(elm).val();
    var tanggal_penimbangan = $(elm).closest('tr').find('td.tanggal').attr('data-tanggal');
    var label = (status_budget == 'N') ? "mengaktifkan" : "menonaktifkan";
    var data = "Apakah anda yakin akan "+label+" ID-Budget : "+kode_budget+" ?";
    var kembali_status = 0;
    var aktifkan = 0;
    var box = bootbox.dialog({
        message : data,
        buttons : {
            danger : {
                label : "Tidak",
                className : "btn-default",
                callback : function() {
                    kembali_status = 1;
                    return true;
                }
            },
            success : {
                label : "Ya",
                className : "btn-primary",
                callback : function() {
                    if(status_budget == 'N'){
                        aktifkan = 1;
                        $('div.bootbox').modal('hide');

                    }
                    else{
                        dialog_keterangan(kode_budget, status_budget, tanggal_penimbangan);
                    }
                    return true;
                }
            },
        },
    });

    box.bind('hidden.bs.modal', function() {
        if(kembali_status == 1){
            var status = $(elm).attr('data-status');
            $(elm).val(status);

        }
        if(aktifkan == 1){

            var tr = $('tr.double_click');
            var table = $('tr.double_click').closest('table');
            tr.find('td.keterangan span.edit i').click();
            tr.find('td.keterangan span.reset').addClass('hide');
            tr.find('td.keterangan span.set').attr('data-aksi' ,'I');
            table.find('tbody').prepend(tr);
            tr.find('td.tara input').focus().select();
        }
    });

}

function dialog_keterangan(kode_budget, status_budget, tanggal_penimbangan){
    var data = "<div class='col-md-12 form-group'>Keterangan perubahan status budget</div>";
        data += '<div class="col-md-12 form-group"><textarea onkeyup="maks_karakter(this)" class="form-control" id="keterangan" name="keterangan" type="text"></textarea></div>';
        data += '<div class="col-md-12 form-group text-center"><button id="btn-simpan" data-kode-budget="'+kode_budget+'" data-status="'+status_budget+'" data-tanggal="'+tanggal_penimbangan+'" onclick="ubah_status_budget(this)" class="btn btn-primary" type="button" disabled>Simpan</button></div>';
    var box = bootbox.dialog({
        message : data,
    });

    box.bind('shown.bs.modal', function() {
        box.find("textarea#keterangan").focus();
    });
}

function ubah_status_budget(elm){
    var kode_budget = $(elm).attr('data-kode-budget');
    var status_budget = $(elm).attr('data-status');
    var tanggal_penimbangan = $(elm).attr('data-tanggal');
    var keterangan = $('#keterangan').val();
    $.ajax({
        type : 'POST',
        dataType : 'json',
        url : "master/budget_glangsing/ubah_status_budget/",
        data : {
            kode_budget : kode_budget,
            status_budget : status_budget,
            keterangan : keterangan,
            tanggal_penimbangan : tanggal_penimbangan
        }
    }).done(function(data) {
        if(data.status_budget == status_budget){
            goSearch();
            toastr.success('Data budget telah dinonaktifkan.', 'Informasi');
            $('div.bootbox').modal('hide');
        }
        else{
            toastr.error('Data budget gagal dinonaktifkan.', 'Informasi');
        }
    }).fail(function(reason) {
        console.info(reason);
    }).then(function(data) {
    });
}

function maks_karakter(elm){
    var result = 0;
    var keterangan = $(elm).val();
    keterangan = keterangan.replace(/\s/gi, '');
    $('#btn-simpan').attr('disabled', true);
    if(keterangan.length >= 10){
        $('#btn-simpan').removeAttr('disabled');
    }
}

function set_tanggal(elm){


    $('#q_tanggal_penimbangan').datepicker({
        dateFormat : 'dd M yy'
    });
}

function get_berat_timbang(elm){
    $(elm).removeAttr('readonly');
    //console.log('OK');
    setTimeout(function(){
        var berat = $(elm).val();
        $(elm).val(berat);
        $(elm).attr('readonly', true);
    }, 0);
}

function replace_timbang(elm){
    //console.log($(elm).val());
    $(elm).select().focus().val($(elm).val());

}

function selected(elm){
    $(elm).select().focus();
}
