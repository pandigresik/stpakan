var pengembalianSak = {
    _varSakBatasAtas: 0.134,
    /*0.114,*/
    /*0.134*/
    _varSakBatasBawah: 0.114,
    /*0.134,*/
    /*0.114*/
    _varBrtBawah: 0,
    _varBrtAtas: 0,
    _varNoReg: '',
    _varNoPPSK: '',
    _varJmlDiminta: 0,
    _varBrtTimbang: 0.00,
    _timer: true,
    _tkode_pegawai: '',
    _tnama_pegawai: '',
    _date_transaction: null,
    _trSelected: null,
    _resultData: null,

    /* baru */
    _noReg: null,
    _noPPSK: null,
    _kodeFlok: null,
    _jmlDiminta: null,
    _jmlKonfirmasi: 0,
    _rataRataKembali: null,
    _konfirmasiError: null,
    _beratKembali: null,
    _jumlahKembali: null,
    _kategoriGlangsing: null,
    /* end baru */

    scanRFID: function(elm) {
        var _rfID = $(elm).val();

        pengembalianSak.setFormDisable();
        $('#nama_kandang').text('');
        $('#no_ppsk').empty();
        $('#tgl_kebutuhan').empty();

        $.ajax({
            type: "POST",
            url: "api/General/kandang",
            data: {
                rfid: _rfID
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    pengembalianSak.statusOK();
                    $('#nama_kandang').text(data.content.nama_kandang);
                    pengembalianSak.checkPermintaan(_rfID);
                } else {
                    pengembalianSak.statusNO();
                }
            }
        });
    },

    checkPermintaan: function(rfidVal) {
        var _rfID = rfidVal;
        var kategoriTemp = [];

        $.ajax({
            type: "POST",
            url: "permintaan_glangsing/pengembalian_sak/getRFID",
            data: {
                rfid: _rfID
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == 1) {
                    /* $('input[name=no_reg]').val(data.no_reg); */
                    /* $('input[name=kode_flok]').val(data.kode_flok); */
                    pengembalianSak._resultData = data.data;
                    pengembalianSak._noReg = data.no_reg;
                    pengembalianSak._kodeFlok = data.kode_flok;
                    if (data.data.length > 0) {
                        for (i = 0; i < data.data.length; i++) {
                            /*if(i==0){
							  $('#no_ppsk').append($('<option>',{
								  value: '',
								  text : 'Pilih'
							  }));
						  }  
						  $('#no_ppsk').append($('<option>',{
							  value: data.data[i].nama_budget,
							  text : data.data[i].nama_budget
						  }));*/
                            if (kategoriTemp.includes(data.data[i].nama_budget) == false) {
                                kategoriTemp[kategoriTemp.length] = data.data[i].nama_budget;
                                $('#no_ppsk').append($('<option>', {
                                    value: data.data[i].nama_budget,
                                    text: data.data[i].nama_budget
                                }));
                            }
                        }
                        pengembalianSak.setFormActive();
                        $('#no_ppsk').removeAttr("disabled");
                        pengembalianSak.setTanggalKebutuhan(kategoriTemp[0]);
                        kategoriTemp.reset;
                    } else {
                        toastr.warning(data.message, 'warning');
                    }
                } else {
                    toastr.warning(data.message, 'Gagal');
                }
            }
        });
    },

    setTanggalKebutuhan: function(elm) {
        var masterData = pengembalianSak._resultData;
        var setJml = false;
        $('#tgl_kebutuhan').removeAttr("disabled");
        $('#tgl_kebutuhan').empty();

        for (i = 0; i < masterData.length; i++) {
            if (masterData[i].nama_budget == elm) {
                $('#tgl_kebutuhan').append($('<option>', {
                    value: masterData[i].no_ppsk,
                    text: masterData[i].tgl_kebutuhan_text
                }));
                if (setJml == false) {
                    pengembalianSak._jmlDiminta = masterData[i].jml_diminta;
                    pengembalianSak._noPPSK = masterData[i].no_ppsk;
                    setJml = true;
                }
            }
        }
        $('#berat_glansing').val('');
        $('#jumlah_kembali').val('');
    },

    resetStatus: function() {
        $('#rfid-status').removeClass('glyphicon-ok-sign');
        $('#rfid-status').removeClass('glyphicon-remove-sign');
        $('#rfid-status-label').text('RFID Ditolak');
    },

    statusOK: function() {
        $('#rfid-status').css('color', 'green');
        $('#rfid-status').addClass('glyphicon-ok-sign');
        $('#rfid-status-label').text('RFID Diterima');
        $('#rfid-status-label').css('color', 'green');
    },

    statusNO: function() {
        $('#rfid-status').addClass('glyphicon-remove-sign');
        $('#rfid-status').css('color', 'red');
        $('#rfid-status-label').text('RFID Ditolak');
        $('#rfid-status-label').css('color', 'red');
    },

    setFormActive: function() {
        $('#no_ppsk').removeAttr("disabled");
        $('#berat_glansing').removeAttr("disabled");
        $('#tgl_kebutuhan').removeAttr("disabled");
    },

    setFormDisable: function() {
        $('#no_ppsk').attr('disabled', 'disabled');
        $('#berat_glansing').attr('disabled', 'disabled');
        $('#tgl_kebutuhan').attr('disabled', 'disabled');
        $('#rfid-status-label').text('');
    },

    selectKategoriBudget: function(elm) {
        var no_ppsk = $(elm).find('option:selected').val();
        var data = pengembalianSak._resultData;
        for (i = 0; i < data.length; i++) {
            if (data[i].no_ppsk == no_ppsk) {
                //$('#tgl_kebutuhan_text').text(data[i].tgl_kebutuhan_text);
                //$('input[name=tgl_kebutuhan]').val(data[i].tgl_kebutuhan);
                //$('input[name=jml_diminta]').val(data[i].jml_diminta);
                pengembalianSak._jmlDiminta = data[i].jml_diminta;
                pengembalianSak._noPPSK = data[i].no_ppsk;
            }
        }
        $('#berat_glansing').val('');
        $('#jumlah_kembali').val('');
    },

    confirmQtyCheck: function(elm) {
        var jmlMinta = pengembalianSak._jmlDiminta;
        var inputConfirm = $(elm).val();
        pengembalianSak._jmlKonfirmasi = inputConfirm;
        if (parseInt(inputConfirm) > parseInt(jmlMinta)) {
            $(elm).val(jmlMinta);
            pengembalianSak._jmlKonfirmasi = jmlMinta;
        }
    },

    get_berat_timbang: function(elm) {
        setTimeout(function() {
            var berat = $(elm).val();
            /*pengembalianSak._varBrtBawah = berat / pengembalianSak._varSakBatasBawah;
            pengembalianSak._varBrtAtas = berat / pengembalianSak._varSakBatasAtas;
            pengembalianSak._rataRataKembali = Math.ceil((pengembalianSak._varBrtBawah + pengembalianSak._varBrtAtas) / 2);*/
            pengembalianSak._beratKembali = berat;
            $(elm).val(berat);
            bootbox
                .dialog({
                    title: 'Pengembalian Glangsing',
                    //message: $('#formKonfirmasi'),
                    message: "<div class='form-group'>" +
                        "<div class='col-xs-8'>Masukkan jumlah pengembalian glangsing</div>" +
                        "<div class='col-xs-4'>" +
                        "<input type='text' id='qty_confirm' class='form-control' onBlur='pengembalianSak.confirmQtyCheck(this)'" +
                        " onkeyup='number_only(this)' value='" + pengembalianSak._jmlKonfirmasi + "'>" +
                        "</div>",
                    show: false,
                    /* We will show it manually later */
                    buttons: [{
                            label: "Batal",
                            className: "btn btn-default pull-left",
                            callback: function() {
                                // Do not close modal
                                $('input[name=jml_kembali]').val('');
                                $(elm).val('');
                                pengembalianSak._jmlKonfirmasi = 0;
                                //$(elm).focus();
                            }
                        },
                        {
                            label: "Konfirmasi",
                            className: "btn btn-primary pull-right",
                            callback: function() {
                                if (pengembalianSak.cekJumlah() == true) {
                                    // $('#btnsimpan').removeAttr('disabled');
                                    pengembalianSak._jmlKonfirmasi = 0;
                                    pengembalianSak.submit();
                                    //return true;
                                } else {
                                    $('.bootbox').modal('hide');
                                    pengembalianSak.getKonfirmasiSalah();
                                    return false;
                                }

                            }
                        }
                    ]
                })
                .on('shown.bs.modal', function() {
                    //$('#formKonfirmasi').show();                             /* Show the login form */
                    //.formValidation('resetForm', true); /* Reset form */
                })
                .on('hide.bs.modal', function(e) {
                    /**
                     * Bootbox will remove the modal (including the body which contains the login form)
                     * after hiding the modal
                     * Therefor, we need to backup the form
                     */
                    //$('#formKonfirmasi').hide().appendTo('body');
                    pengembalianSak._jmlKonfirmasi = 0;
                })
                .modal('show');
        }, 0);
    },

    replace_timbang: function(elm) {
        //console.log($(elm).val());
        $(elm).select().focus().val($(elm).val());
    },

    selected: function(elm) {
        $(elm).select().focus();
    },

    cekJumlah: function() {
        var jmlKonfirmasi = pengembalianSak._jmlKonfirmasi;
        var jmlDiminta = parseInt(pengembalianSak.jmlDiminta);
        var hasilHitung = parseFloat(pengembalianSak._beratKembali) / parseInt(jmlKonfirmasi);

        if (hasilHitung.toFixed(3) > pengembalianSak._varSakBatasAtas) {
            pengembalianSak._konfirmasiError = 'Pengembalian glangsing kurang dari rata-rata';
            return false;
        } else if (hasilHitung.toFixed(3) < pengembalianSak._varSakBatasBawah) {
            pengembalianSak._konfirmasiError = 'Pengembalian glangsing melebihi dari rata-rata';
            return false;
        } else {
            $('input[name=jml_kembali]').val(jmlKonfirmasi);
            return true;
        }
    },

    getKonfirmasiSalah: function() {
        bootbox.alert({
            title: "Pengembalian Glangsing",
            message: pengembalianSak._konfirmasiError,
            buttons: {
                ok: {
                    label: 'Kembali'
                }
            },
            callback: function(result) {
                pengembalianSak.get_berat_timbang($('#berat_glansing'));
                $('input[name=jmlKonfirmasi]').val('0');
                $('input[name=jmlKonfirmasi]').focus();
            }
        });
    },

    submit: function(elm) {
        var _error = 0;
        var _message = [];
        var _form = $(elm).closest('form');
        /*var _noPPSK = $('select[name=no_ppsk]').find('option:selected').val();*/
        /*var _noReg = $('input[name=no_reg]').val();*/
        var _noPPSKVal = pengembalianSak._noPPSK;
        var _noRegVal = pengembalianSak._noReg;
        var _jmlKembali = $('input[name=jml_kembali]').val();
        var _brtKembali = $('input[name=brt_kembali]').val();

        if (_noPPSKVal != '' && _noRegVal != '' && _jmlKembali != '') {
            var data = {
                no_ppsk: _noPPSKVal,
                no_reg: _noRegVal,
                jml_kembali: _jmlKembali,
                brt_kembali: _brtKembali
            };
            pengembalianSak.fingerprint(data);
        } else {
            bootbox.alert({
                title: "Pengembalian Glangsing",
                message: "Data tidak boleh kosong",
                buttons: {
                    ok: {
                        label: 'Kembali'
                    }
                },
                callback: function(result) {}
            });
        }

    },

    fingerprint: function(elm) {
        pengembalianSak.simpan_transaksi_verifikasi(function(result) {
            if (result.date_transaction) {
                var _message = "<center><img src='assets/images/finger.jpg' height='260px' style='filter:invert(100%);'></center>";
                var box = bootbox.dialog({
                    message: _message,
                    closeButton: true,
                    title: "Fingerprint untuk pengawas",
                    onEscape: function() {
                        pengembalianSak._timer = false;
                        pengembalianSak._tkode_pegawai = '';
                        pengembalianSak._tnama_pegawai = '';
                        return true;
                    }
                });

                box.bind('shown.bs.modal', function() {
                    pengembalianSak._timer = true;
                    pengembalianSak._tkode_pegawai = '';
                    pengembalianSak._tnama_pegawai = '';
                    pengembalianSak._date_transaction = result.date_transaction;
                    pengembalianSak.cek_verifikasi(result.date_transaction);
                });

                box.bind('hidden.bs.modal', function() {
                    if (pengembalianSak._tkode_pegawai && pengembalianSak._tnama_pegawai) {
                        var done = pengembalianSak.cek_selesai(elm);
                    }
                });
            }
        });
    },

    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
            data: {
                transaction: 'pengembalian_glangsing',
                kode_flok: pengembalianSak._kodeFlok //$('input[name=kode_flok]').val()
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    cek_verifikasi: function(date_transaction) {
        if (pengembalianSak._timer == true) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cek_verifikasi",
                data: {
                    date_transaction: date_transaction,
                    noreg: pengembalianSak._noReg,
                    level: 'PENGAWAS'
                },
                dataType: 'json',
                success: function(data) {
                    if (data.verificator) {
                        $('.bootbox').modal('hide');
                        if (data.match == 1) {
                            pengembalianSak._timer = false;
                            pengembalianSak._tkode_pegawai = data.kode_pegawai;
                            pengembalianSak._tnama_pegawai = data.nama_pegawai;

                        } else {
                            var _message =
                                "<center><h1 class='glyphicon glyphicon-remove-sign'style='color:red;font-size:20vw;'>" +
                                "</h1><div>Anda bukan pengawas untuk kandang ini</div></center>";
                            var box = bootbox.dialog({
                                message: _message,
                                closeButton: true,
                                title: "Fingerprint",
                                onEscape: function() {
                                    pengembalianSak._timer = false;
                                    pengembalianSak._tkode_pegawai = '';
                                    pengembalianSak._tnama_pegawai = '';
                                    return true;
                                },
                                buttons: {
                                    success: {
                                        label: "<i class='glyphicon glyphicon-refresh'></i> Refresh",
                                        className: "btn-primary",
                                        callback: function() {
                                            $('.bootbox').modal('hide');
                                            pengembalianSak.fingerprint();
                                            return true;
                                        }
                                    }
                                }
                            });
                        }
                    } else {
                        pengembalianSak._timer = true;
                        pengembalianSak._tkode_pegawai = '';
                        pengembalianSak._tnama_pegawai = '';
                        setTimeout("pengembalianSak.cek_verifikasi('" + date_transaction + "')", 1000);
                    }
                }
            });
        }
    },

    cek_selesai: function(elm) {
        $.ajax({
            type: "POST",
            url: "permintaan_glangsing/pengembalian_sak/konfirmasiPengembalian",
            data: {
                no_ppsk: elm.no_ppsk,
                no_reg: elm.no_reg,
                jml_kembali: elm.jml_kembali,
                brt_kembali: elm.brt_kembali,
                user_pengembali: pengembalianSak._tkode_pegawai,
                tgl_kembali: pengembalianSak._date_transaction
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == '1') {
                    toastr.success(data.message, 'Berhasil');
                    pengembalianSak.resetStatus();
                    pengembalianSak.setFormDisable();
                    pengembalianSak.refreshForm();
                } else {
                    toastr.error(data.message, 'Gagal');
                }
            }
        });

        return 1;
    },

    refreshForm: function() {
        var promises = $('input').map(function(i, v) {
            $(v).val('');
            //console.log($(v).attr('name'));
        });

        $('#no_ppsk').empty();
        $('#tgl_kebutuhan').empty();

        $('#nama_kandang').text('');
        $('#tgl_kebutuhan_text').text('');

        promises.promise().done(function(results) {
            //$('#btnsimpan').attr('disabled', 'disabled');
        });
    }

};