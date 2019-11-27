var generateBudget = 0;
var generateBudgetConfirm = 0;

function refresh_table(kode_siklus) {
    var table = $('#tb_status_periode').dataTable({
        lengthChange: false,
        info: false,
        paging: false,
        searching: false,
        bDestroy: true,
        //processing: true,
        serverSide: true,
        ajax: {
            url: "budget_pengembalian_glangsing/main/read_periode",
            type: "POST",
            data: {
                //coba:"oke"
                nama_farm: $('#q_nm_farm').val(),
                siklus: $('#q_siklus').val(),
                status: $('#q_status').val(),
            }
        },
        initComplete: function() {
            if (kode_siklus != undefined) {
                $('#tb_status_periode tr[data-kode_siklus=' + kode_siklus + ']').click();
            }
        },
        fnCreatedRow: function(nRow, nData, iDataIndex) {
            $(nRow).attr('data-kode_siklus', nData[1]);
            $(nRow).click(function() {
                $.ajax({
                    url: 'budget_pengembalian_glangsing/main/cek_status_siklus',
                    dataType: 'json',
                    data: {
                        status_siklus: nData[7],
                        kode_siklus: nData[1],
                    },
                    type: 'POST',
                    success: function(response) {
                        if (response.field == 'write') {
                            input_attr = false;
                        } else {
                            input_attr = true;
                        }

                        if (response.message == true) {
                            notificationBox("Budget pemakaian glangsing untuk siklus " + response.periode_siklus + " harus dilakukan penutupan terlebih dahulu.");
                        }
                        if (!empty(response.messageTimeline)) {
                            notificationBox(response.messageTimeline);
                        }

                        if (nData[7] == null) {
                            $('#fm')[0].reset();
                        }



                        $.ajax({
                            url: 'budget_pengembalian_glangsing/main/get_budget_data',
                            dataType: 'html',
                            data: {
                                kode_siklus: nData[1],
                                nama_farm: $('#q_nm_farm').val(),
                                status_siklus: nData[7]
                            },
                            type: 'POST',
                            beforeSend: function() {
                                $('#list_permintaan').html('Loading ......');
                            },
                            success: function(data) {
                                var hasil = $('#list_permintaan').html(data);
                                if (hasil) {
                                    $("#eksternal_budget").find('input').attr('readonly', input_attr);
                                    $("#internal_budget").find('input').attr('readonly', input_attr);
                                    $("#td_total_internal").find('input').attr('readonly', true);
                                    $("#td_total_eksternal").find('input').attr('readonly', true);

                                    $("#kd_siklus").val(nData[1]);
                                    $("#tgl_buat").val(nData[8]);
                                    $("#kode_farm").val(nData[2]);
                                    $("#periode").val(nData[3]);

                                    $('#save_budget').css('display', response.simpan);
                                    $('#release_budget').css('display', response.rilis);
                                    $('#close_budget').css('display', response.tutup_bugdet);
                                    $('#review_budget').css('display', response.review);
                                    $('#approve_budget').css('display', response.approve);
                                    $('#reject_budget').css('display', response.reject);
                                    $('#print_budget').css('display', response.print);
                                    $('#pesan_keterlambatan').html('');
                                    if (!empty(response.pesan_keterlambatan)) {
                                        $('#pesan_keterlambatan').html(response.pesan_keterlambatan);
                                    }
                                    //alert($('#save_budget')+'.'+response.simpan+'()');
                                }
                            }

                            // }
                        });
                    }
                });
            })
        },
        aoColumns: [{
                "sClass": "text-center",
                "mData": [9]
            }, {
                "sClass": "text-center",
                "mData": [3]
            },
            {
                "sClass": "text-center",
                "mData": [6]
                    /*"fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                       $(nTd).click(function() {
                        alert('asdasdasdasd');
                       })
                     }  */
            },
        ]

    });
}

function save_budget(action) {
    generateBudgetConfirm = 0;
    generateBudget = 0;
    $("#action").val(action);
    var _cekKontrol = cekKontrolTutupBudget(action);

    switch (action) {
        case 'D':
            var data = "Apakah Anda yakin akan Menyimpan data Budget Pemakaian Glangsing ini?";
            break;
        case 'N':
            var data = "Apakah Anda yakin akan Menyimpan data Budget Pemakaian Glangsing ini?";
            break;
        case 'C':
            var data = "Siklus aktif tidak ditemukan. <br >Apakah Anda yakin akan melanjutkan tutup budget glangsing pada siklus <strong>" + $('#periode').val() + "</strong> ?";
            data += " <br ><br >Jika <strong>Ya</strong>, maka user perlu pengajuan budget  untuk siklus selanjutnya."
            break;
        case 'R':
            var data = "Apakah anda yakin melakukan Approval untuk data Budget Pemakaian Glangsing ini?";
            break;
        case 'A':
            var data = "Apakah anda yakin melakukan Approval untuk data Budget Pemakaian Glangsing ini?";
            break;
        case 'RJ':
            var data = "Apakah anda yakin melakukan Reject untuk data Budget Pemakaian Glangsing ini?";
            break;
    }
    $.when(_cekKontrol).done(function() {
        if (!_cekKontrol.status) {
            return;
        } else {
            switch (action) {
                case 'C':
                    if (!empty(_cekKontrol.konfirmasi)) {
                        data = _cekKontrol.konfirmasi;
                        generateBudgetConfirm = 1;
                    }
                    break;
            }
            if (action == 'C') {
                /** tampilkan konfirmasi lagi */
                bootbox.confirm({
                    message: "Apakah Anda yakin untuk penutupan Budget Pemakaian Glangsing siklus " + $('#periode').val() + "?",
                    buttons: {
                        confirm: {
                            label: "Ya",
                            className: "btn-primary",
                        },
                        cancel: {
                            label: "Tidak",
                            className: "btn-default",
                        },
                    },
                    callback: function(result) {
                        if (result) {
                            lanjutSimpan(action, data);
                        }
                    }
                });
            } else {
                lanjutSimpan(action, data);
            }

        }
    })
}

function lanjutSimpan(action, data) {
    var box = bootbox.confirm({
        message: data,
        buttons: {
            confirm: {
                label: "Ya",
                className: "btn-primary",
            },
            cancel: {
                label: "Tidak",
                className: "btn-default",
            },
        },
        callback: function(result) {
            if (result == true) {
                if (action == 'RJ') {
                    var box2 = bootbox.prompt({
                        title: "Keterangan",
                        inputType: 'textarea',
                        buttons: {
                            confirm: {
                                label: 'Simpan',
                                className: 'btn-primary btn-keterangan-reject',
                            },
                            cancel: {
                                label: 'Batal',
                                className: 'btn-default'
                            }
                        },
                        callback: function(result) {
                            if (result) {
                                $('#keterangan').val(result);
                                //$("#fm").submit();
                                submit_form();
                            }
                        }
                    });

                    box2.bind('shown.bs.modal', function() {
                        $('.btn-keterangan-reject').prop('disabled', true);
                        $('.bootbox-form textarea').on('keyup', function() {
                            if ($(this).val().length >= 10) {
                                $('.btn-keterangan-reject').prop('disabled', false);
                            } else {
                                $('.btn-keterangan-reject').prop('disabled', true);
                            }
                        });
                    });
                } else {
                    if (action == 'C') {
                        if (generateBudgetConfirm) {
                            generateBudget = 1;
                        }
                    }
                    submit_form();
                }
            } else {
                if (action == 'C') {
                    if (generateBudgetConfirm) {
                        submit_form();
                    }
                }
            }
        }
    });
}

function cekKontrolTutupBudget(action) {
    var _result = { 'status': 0 };
    if (action != 'C') {
        _result['status'] = 1;
        return _result;
    }

    $.ajax({
        url: 'budget_pengembalian_glangsing/main/cekKontrolTutupBudget',
        dataType: 'json',
        type: 'POST',
        async: false,
        data: {
            kode_farm: $('#kode_farm').val(),
            periode: $('#periode').val(),
        },
        success: function(response) {
            if (response.status) {
                _result.status = 1;
                _result.konfirmasi = response.konfirmasi;
                return _result;
            } else {
                notificationBox(response.message);
            }
        }
    });
    return _result;
}

function submit_form() {
    $.ajax({
        url: 'budget_pengembalian_glangsing/main/save_budget',
        dataType: 'json',
        type: 'POST',
        data: $("#fm").serialize() + '&generatebudget=' + generateBudget,
        success: function(response) {
            if (response.success == true) {
                $('#fm')[0].reset();
                $('#save_budget').css('display', 'none');
                $('#release_budget').css('display', 'none');
                $('#close_budget').css('display', 'none');
                $('#review_budget').css('display', 'none');
                $('#approve_budget').css('display', 'none');
                $('#reject_budget').css('display', 'none');

                $("#eksternal_budget").find('input').attr('readonly', true);
                $("#internal_budget").find('input').attr('readonly', true);
                $("#td_total_internal").find('input').attr('readonly', true);
                $("#td_total_eksternal").find('input').attr('readonly', true);

                notificationBox(response.message);
                var _triggerClick;
                if (response.trigger != undefined) {
                    _triggerClick = response.kode_siklus;
                }
                refresh_table(_triggerClick);
            } else {
                notificationBox(response.message);
            }
        }
    });
    return false;
}
$("#fm").submit(function() {

});


function hitung_internal(elm) {

    if (elm.value == $(elm).data('value') && parseInt($('#count_updated').val()) != 0) {
        $('#count_updated').val(parseInt($('#count_updated').val()) - 1);
    } else {
        $('#count_updated').val(parseInt($('#count_updated').val()) + 1);
    }

    var t_internal = $('#t_internal').val();
    var total = 0;


    if (parseInt(t_internal) > 0) {
        for (a = 0; a < t_internal; a++) {
            total += parseInt($('#tf_internal' + a).val());
        }
    }
    $('#total_internal').val(total);

}

function hitung_eksternal(elm) {
    var t_eksternal = $('#t_eksternal').val();
    var total = 0;

    if (parseInt(t_eksternal) > 0) {
        for (a = 0; a < t_eksternal; a++) {
            total += parseInt($('#tf_eksternal' + a).val());
        }
    }
    $('#total_eksternal').val(total);
}

function hitung_budget(elm) {
    var t_internal = $('#t_internal').val();
    var t_eksternal = $('#t_eksternal').val();
    var total_internal = 0;
    var total_eksternal = 0;
    var count = 0;

    if (parseInt(t_internal) > 0) {
        for (a = 0; a < t_internal; a++) {
            if (parseInt($('#tf_internal' + a).val()) != parseInt($('#tf_internal' + a).data('value'))) {
                count++;
            }
            total_internal += parseInt($('#tf_internal' + a).val());
        }
    }

    if (parseInt(t_eksternal) > 0) {
        for (a = 0; a < t_eksternal; a++) {
            if (parseInt($('#tf_eksternal' + a).val()) != parseInt($('#tf_eksternal' + a).data('value'))) {
                count++;
            }
            total_eksternal += parseInt($('#tf_eksternal' + a).val());
        }
    }

    $('#count_updated').val(count);
    $('#total_internal').val(total_internal);
    $('#total_eksternal').val(total_eksternal);
}

function notificationBox(message) {
    bootbox.dialog({
        message: message,
        buttons: {
            success: {
                label: "OK",
                className: "btn-primary",
                callback: function() {
                    return true;
                }
            }
        }
    });
}

function print_budget() {
    var _kode_siklus = $('#kd_siklus').val();
    var _kode_farm = $('#kode_farm').val();
    $.redirect('budget_pengembalian_glangsing/main/cetakHistori', { kode_farm: _kode_farm, kode_siklus: _kode_siklus }, 'get', '_blank');
}