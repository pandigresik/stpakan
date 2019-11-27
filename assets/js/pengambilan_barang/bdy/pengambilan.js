var Pengambilan = {
    timer: true,
    tkode_pegawai: '',
    tnama_pegawai: '',
    _m_hand_pallet: {},
    _checkboxElm: null,
    get_m_hand_pallet: function() {
        if (empty(this._m_hand_pallet)) {
            var url = 'master/hand_pallet/hand_pallet_aktif';
            $.ajax({
                url: url,
                async: false,
                success: function(data) {},
                dataType: 'json'
            }).done(function(data) {
                Pengambilan._m_hand_pallet = data;
            });
        }
        return this._m_hand_pallet;
    },
    show_detail_kandang: function(elm) {
        // $(elm).attr('disabled', true);
        $(elm).parents('tr.tr-sub-detail').next().removeClass('hide');
        $(elm).replaceWith('<input type="checkbox" checked disabled />');
        // $(elm).parents('tr.tr-sub-detail').find('td.arrow span').css('transform','rotate(180deg)');
    },
    show_detail: function(elm) {
        var data_ke = $(elm).attr('data-ke');
        $('#transaction-table table tbody tr.tr-header').removeClass('mark_row');
        $('#transaction-table table tbody tr.tr-detail').fadeOut('slow').addClass(
            'hide');
        $(elm).addClass('mark_row');
        if ($(elm).next().hasClass('hide')) {
            $(elm).next().fadeIn('slow').removeClass('hide');
        } else {
            $(elm).next().fadeOut('slow').addClass('hide');
        }

        var i = 0;
        $.each($(elm).next().find('table.tbl-detail-pakan tbody tr.tr-sub-detail'), function() {
            var berat = $(this).find('td.berat-timbang input.berat-timbang').val();
            if (!berat && i == 0) {
                i++;
            }
        });
    },

    cek_konversi: function(berat, callback) {
        if (!empty(berat)) {
            berat = parseFloat(berat);
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cek_konversi",
                data: {
                    berat: berat
                },
                dataType: 'json',
                success: function(data) {
                    callback(data);
                }
            });
        } else {
            callback(2);
        }
    },

    selesai: function(elm) {
        var detail_ke_detail = $(elm).closest('tr.tr-detail').attr('data-ke');
        var detail_ke_sub_detail = $(elm).closest('tr.tr-sub-detail-pakan').prev('tr.tr-sub-detail').attr('data-ke');

        var kode_flok = $('tr.tr-header[data-ke="' + detail_ke_detail + '"]').attr('data-kode-flok');
        var no_order = $('tr.tr-header[data-ke="' + detail_ke_detail + '"]').attr('data-no-order');
        var no_kavling = $('tr.tr-header[data-ke="' + detail_ke_detail + '"]').find('td.no-kavling').attr('data-no-kavling');
        var kode_barang = $('tr.tr-header[data-ke="' + detail_ke_detail + '"]').find('td.kode-barang').text();
        var diserahkan_oleh = $('tr.tr-header[data-ke="' + detail_ke_detail + '"]').find('td.diserahkan-oleh').attr('data-diserahkan-oleh');
        var no_pallet = $('tr.tr-detail[data-ke="' + detail_ke_detail + '"] tr.tr-sub-detail[data-ke="' + detail_ke_sub_detail + '"]').attr('data-no-pallet');
        var berat_pallet = $('tr.tr-detail[data-ke="' + detail_ke_detail + '"] tr.tr-sub-detail[data-ke="' + detail_ke_sub_detail + '"] td.berat-pallet').text();
        var berat_bersih = $('tr.tr-detail[data-ke="' + detail_ke_detail + '"] tr.tr-sub-detail[data-ke="' + detail_ke_sub_detail + '"] td.berat-bersih').text();
        var jumlah_aktual = $('tr.tr-detail[data-ke="' + detail_ke_detail + '"] tr.tr-sub-detail[data-ke="' + detail_ke_sub_detail + '"] td.jumlah-sak').attr('data-jumlah-aktual-sak');
        var status_jumlah_aktual = $('tr.tr-detail[data-ke="' + detail_ke_detail + '"] tr.tr-sub-detail[data-ke="' + detail_ke_sub_detail + '"] td.jumlah-sak').attr('data-status-jumlah-aktual-sak');
        var jumlah_konversi_timbang = $('tr.tr-detail[data-ke="' + detail_ke_detail + '"] tr.tr-sub-detail[data-ke="' + detail_ke_sub_detail + '"] td.jumlah-sak').text();
        var _tbody_kandang = $(elm).closest('tbody');
        var data = [];

        jumlah_konversi_timbang = (status_jumlah_aktual == 1) ? jumlah_konversi_timbang : 0;
        _tbody_kandang.find('tr').each(function() {
            var jumlah_pallet = $(this).attr('data-jml-pallet');
            jumlah_pallet = parseInt(jumlah_pallet);
            var no_reg = $(this).find('td.nama-kandang').attr('data-no-reg');
            no_pallet = $(this).find('td.nama-kandang').attr('data-no-pallet');
            var jumlah_kebutuhan = $(this).find('td.jml-kebutuhan').text();
            var jumlah = $(this).find('td.jml-aktual').text();
            var berat = $(this).find('td.berat').text();
            var diterima_oleh = $(this).find('td.konfirmasi').attr('data-user-gudang');

            if (jumlah_pallet > 1) {

                var tmp_no_pallet = $(this).find('td.nama-kandang').attr('data-no-pallet');
                tmp_no_pallet = tmp_no_pallet.split(',');
                var tmp_jml_kebutuhan = $(this).find('td.jml-kebutuhan').attr('data-jml-kebutuhan');
                tmp_jml_kebutuhan = tmp_jml_kebutuhan.split(',');
                berat = parseFloat(berat);
                jumlah = parseInt(jumlah);
                var berat_per_sak = berat / jumlah;
                berat_per_sak = berat_per_sak.toFixed(3);

                for (var i = 0; i < tmp_no_pallet.length; i++) {
                    no_pallet = tmp_no_pallet[i];
                    jumlah_kebutuhan = tmp_jml_kebutuhan[i];
                    jumlah_kebutuhan = parseInt(jumlah_kebutuhan);

                    jumlah = jumlah_kebutuhan;

                    jumlah = parseInt(jumlah);
                    berat = jumlah * berat_per_sak;

                    data.push({
                        'no_order': no_order,
                        'id_kavling': no_kavling,
                        'kode_barang': kode_barang,
                        'diserahkan_oleh': diserahkan_oleh,
                        'no_pallet': no_pallet,
                        'berat_pallet': berat_pallet,
                        'berat_bersih': berat_bersih,
                        'berat_timbang': (parseFloat(berat_bersih) + parseFloat(berat_pallet)),
                        'jumlah_aktual': jumlah_aktual,
                        'jumlah_konversi_timbang': jumlah_konversi_timbang,
                        'jenis_kelamin': 'C',
                        'kode_flok': kode_flok,
                        'no_reg': no_reg,
                        'jumlah': jumlah_kebutuhan,
                        'jumlah_aktual_zak': jumlah,
                        'berat': berat,
                        'user_gudang': diterima_oleh
                    });
                }

            } else {
                data.push({
                    'no_order': no_order,
                    'id_kavling': no_kavling,
                    'kode_barang': kode_barang,
                    'diserahkan_oleh': diserahkan_oleh,
                    'no_pallet': no_pallet,
                    'berat_pallet': berat_pallet,
                    'berat_bersih': berat_bersih,
                    'berat_timbang': (parseFloat(berat_bersih) + parseFloat(berat_pallet)),
                    'jumlah_aktual': jumlah_aktual,
                    'jumlah_konversi_timbang': jumlah_konversi_timbang,
                    'jenis_kelamin': 'C',
                    'kode_flok': kode_flok,

                    'no_reg': no_reg,
                    'jumlah': jumlah_kebutuhan,
                    'jumlah_aktual_zak': jumlah,
                    'berat': berat,
                    'user_gudang': diterima_oleh
                });
            }
        });

        if (data.length > 0) {
            this.simpan_data(data, function(result) {
                if (result.result == 1) {
                    toastr.success('Simpan berhasil.', 'Informasi');
                    Pengambilan.updateTotalDropping(result.totalPengambilan);
                } else {
                    toastr.error('Simpan gagal.', 'Informasi');

                }
            });
        }
    },
    updateTotalDropping: function(totalPengambilan) {
        var _totalAwal = parseInt($('#summaryTable tbody>tr>td.total_dropping').text()) || 0;
        var _nilaiBaru = _totalAwal + parseInt(totalPengambilan);
        $('#summaryTable tbody>tr>td.total_dropping').text(_nilaiBaru);
    },
    simpan_data: function(data, callback) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/simpan_data",
            data: {
                data: data
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    cek_pallet: function(no_pallet, zak, callback) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/cek_pallet",
            data: {
                no_pallet: no_pallet,
                zak: zak
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    kontrol_timbangan: function(elm) {
        $(elm).parents('tr.tr-sub-detail').next().addClass('hide');
        var no_pallet = $(elm).parents('tr.tr-sub-detail').attr('data-no-pallet');
        var berat = $(elm).val();
        var berat_bersih = '';
        var jumlah = '';
        var keterangan = '';
        if (berat) {
            var berat_pallet = $(elm).parents('tr.tr-sub-detail').find('td.berat-pallet').text();
            berat_bersih = parseFloat(berat) - parseFloat(berat_pallet);
            if (berat_bersih < 0) {
                toastr.warning('Berat timbang harus lebih besar dari berat pallet.', 'Informasi');
                $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text('');
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
                $(elm).parents('tr.tr-sub-detail').find('td.scan_pallet input').prop('readonly', 1);
            } else {
                berat_bersih = berat_bersih.toFixed(3);
                var berat_rata2 = $(elm).parents('tr.tr-sub-detail').find('td.berat-pallet').data('berat_rata2');
                var sak_aktual = $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').data('jumlah-aktual-sak');
                var konversi_sak = Math.round(berat_bersih / berat_rata2);
                if (konversi_sak != sak_aktual) {
                    bootbox.alert('Jumlah hasil konversi ( ' + konversi_sak + ' )  tidak sama dengan sisa Aktual Kavling.');
                    return;
                }
                $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text(berat_bersih);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak span').removeClass('hide');
                $(elm).parents('tr.tr-sub-detail').find('td.scan_pallet input').prop('readonly', 0);

            }
        } else {
            $(elm).parents('tr.tr-sub-detail').find('td.berat-bersih').text(berat_bersih);
            //$(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').text(jumlah);
            //$(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak',jumlah);
            $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
        }
    },

    kontrol_sak_aktual: function() {
        var jumlah_aktual = $('#jumlah_aktual').val();
        var jumlah_stok_kavling = $('tr.mark_row').attr('data-stok-kavling');
        if (parseInt(jumlah_aktual) != parseInt(jumlah_stok_kavling)) {
            toastr.warning('Jumlah Aktual tidak sama dengan sisa Aktual Kavling.');
            $('#jumlah_aktual').val('');
            $('#jumlah_aktual').focus().select();
        }
    },

    berat_diluar_toleransi: function(elm, jumlah) {
        var konfirmasi = 0;
        var keterangan = '';
        var jumlah_aktual = '';
        var _message = '<div class="form-group form-horizontal new-line">';
        _message += '<div class="form-group">';
        _message += '<label class="col-sm-5 control-label">Konversi Timbangan (Sak)</label>';
        _message += '<div class="col-sm-5">';
        _message += '<label class="control-label">' +
            jumlah + '</label>';
        _message += '</div></div>';
        _message += '<div class="form-group">';
        _message += '<label class="col-sm-5 control-label">Jumlah Sak Aktual</label>';
        _message += '<div class="col-sm-5">';
        _message += '<input type="text" placeholder="Jumlah Sak Aktual" id="jumlah_aktual" class="form-control" onchange="kontrol_sak_aktual()">';
        _message += '</div></div>';
        _message += '</div>';
        var box_status = 0;
        var box = bootbox.dialog({
            message: _message,
            title: "Konfirmasi Sak",
            buttons: {
                success: {
                    label: "Simpan",
                    className: "btn-success",
                    callback: function() {
                        jumlah_aktual = $('#jumlah_aktual').val();
                        if (!jumlah_aktual || jumlah_aktual <= 0) {
                            $('#jumlah_aktual').focus().select();
                            toastr.error('Jumlah Aktual Sak harus diisi.', 'Peringatan');
                            return false;
                        } else {
                            konfirmasi = 1;
                            return true;

                        }
                    }
                }
            }
        });
        box.bind('shown.bs.modal', function() {
            $('#jumlah_aktual').focus().select();
            $('#jumlah_aktual').numeric({
                allowPlus: false, // Allow the + sign
                allowMinus: false, // Allow the - sign
                allowThouSep: false, // Allow the
                allowDecSep: false
            });
        });
        box.bind('hidden.bs.modal', function() {
            if (konfirmasi == 1) {
                keterangan = '<p>Jumlah konversi timbangan = ' + jumlah + ' sak</p><p>Jumlah aktual = ' + jumlah_aktual + ' sak</p>';
                $(elm).parents('tr.tr-sub-detail').find('td.keterangan').html(keterangan);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak', jumlah_aktual);
                $(elm).parents('tr.tr-sub-detail').find('td.jumlah-sak').attr('data-status-jumlah-aktual-sak', '1');
                Pengambilan.show_detail_kandang(elm);
            }
        });
    },

    checkbox_kandang: function(elm) {
        var jumlah_aktual = '';
        var sisa = '';
        var berat = '';
        var _checked;
        var _tr = $(elm).closest('tr');
        var _tbody = _tr.closest('tbody');
        var jumlah_sak = _tbody.closest('tr').prev('.tr-sub-detail').find('td.jumlah-sak').attr('data-jumlah-aktual-sak');
        var berat_bersih = _tbody.closest('tr').prev('.tr-sub-detail').find('td.berat-bersih').text();
        var berat_per_sak = (berat_bersih / jumlah_sak).toFixed(3);
        _checked = $(elm).is(':checked') ? 1 : 0;
        if (_checked) {
            _tbody.find('tr').each(function() {
                _checked = $(this).find('td.checkbox-kandang input.checkbox-kandang').is(':checked');
                if (_checked) {
                    var jumlah_kebutuhan = $(this).find('td.jml-kebutuhan').text();
                    jumlah_kebutuhan = parseInt(jumlah_kebutuhan);
                    var tmp_jumlah_aktual = $(this).find('td.jml-aktual').text();
                    var tmp_pengurang = tmp_jumlah_aktual; //(tmp_jumlah_aktual) ? parseInt(tmp_jumlah_aktual) : jumlah_kebutuhan;
                    jumlah_sak = jumlah_sak - tmp_pengurang;
                }
            })

            if (jumlah_sak > 0) {
                jumlah_aktual = parseInt(_tr.find('td.jml-kebutuhan').text());
                sisa = 0;
                berat = jumlah_aktual * berat_per_sak;
                berat = berat.toFixed(3);

                this.fingerprint(elm);
            } else {
                $(elm).prop('checked', 0);
                toastr.warning('Total jumlah aktual kandang melebihi jumlah aktual timbang.', 'Informasi');

            }

            $(elm).parents('tr.tr-detail-kandang').find('td.jml-aktual').text(jumlah_aktual);
            $(elm).parents('tr.tr-detail-kandang').find('td.sisa').text(sisa);
            $(elm).parents('tr.tr-detail-kandang').find('td.berat').text(berat);
            $(elm).parents('tr.tr-detail-kandang').find('td.konfirmasi').text('');
        }

    },

    not_actived: function(elm) {
        elm.preventDefault();
    },

    konfirmasi_dialog: function(data_result, callback) {
        var konfirmasi = 0;
        var _message = '<div class="form-group form-horizontal new-line">';
        if (data_result == 0) {
            _message += '<label>Jumlah Timbangan (Sak) diluar Batas Toleransi. Apakah akan Melanjutkan Proses Simpan ?</label>';
        } else {
            _message += '<label>Jumlah Timbangan (Sak) kurang dari rencana Kirim. Apakah akan Melanjutkan Proses Simpan ?</label>';
        }
        _message += '</div>';
        var box = bootbox.dialog({
            message: _message,
            title: "",
            buttons: {
                danger: {
                    label: "Tidak",
                    className: "btn-danger",
                    callback: function() {
                        return true;
                    }
                },
                success: {
                    label: "Ya",
                    className: "btn-success",
                    callback: function() {
                        konfirmasi = 1;
                        return true;
                    }
                }
            }
        });

        box.bind('hidden.bs.modal', function() {
            callback(konfirmasi);
        })
    },


    fingerprint: function(elm) {
        if ($(elm).is(':checked')) {
            this._checkboxElm = elm;
            var _noreg = $(elm).closest('tr').find('td.nama-kandang').data('no-reg');
            this.simpan_transaksi_verifikasi(function(result) {
                bootbox.hideAll();
                if (result.date_transaction) {
                    var _message = '<div><p data-kode-pegawai=""></p><p>Sedang menunggu verifikasi fingerprint...</p></div>';
                    var box = bootbox.dialog({
                        message: _message,
                        closeButton: false,
                        title: "Fingerprint",
                        buttons: {
                            success: {
                                label: "Batal",
                                className: "btn-danger",
                                callback: function() {
                                    Pengambilan.timer = false;
                                    Pengambilan.tkode_pegawai = '';
                                    Pengambilan.tnama_pegawai = '';
                                    $(elm).prop('checked', 0);
                                    return true;
                                }
                            }
                        }
                    });

                    box.bind('shown.bs.modal', function() {
                        Pengambilan.timer = true;
                        Pengambilan.tkode_pegawai = '';
                        Pengambilan.tnama_pegawai = '';
                        Pengambilan.cek_verifikasi(result.date_transaction, _noreg);
                    });
                }
            });
        }
    },

    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
            data: {
                transaction: 'pengambilan_barang',
                kode_flok: $('#transaction-table').find('tbody tr').attr('data-kode-flok')
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
    },

    cek_verifikasi: function(date_transaction, _noreg) {
        if (this.timer == true) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cek_verifikasi",
                data: {
                    date_transaction: date_transaction,
                    noreg: _noreg,
                    level: 'PENGAWAS'
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        Pengambilan.timer = false;
                        if (data.match) {
                            Pengambilan.tkode_pegawai = data.kode_pegawai;
                            Pengambilan.tnama_pegawai = data.nama_pegawai;
                            $(Pengambilan._checkboxElm).closest('tr').find('td.konfirmasi').attr('data-user-gudang', Pengambilan.tkode_pegawai);
                            $(Pengambilan._checkboxElm).closest('tr').find('td.konfirmasi').text(Pengambilan.tnama_pegawai);
                            $(Pengambilan._checkboxElm).prop('disabled', 1);
                            var done = Pengambilan.cek_selesai(Pengambilan._checkboxElm);
                            $('.bootbox').modal('hide');
                            toastr.success('Verifikasi fingerprint berhasil.', 'Berhasil');
                            if (done) {
                                Pengambilan.selesai(Pengambilan._checkboxElm);
                            }
                            Pengambilan._checkboxElm = null;
                        } else {
                            Pengambilan.fingerprint(Pengambilan._checkboxElm);
                        }
                    } else {
                        Pengambilan.timer = true;
                        Pengambilan.tkode_pegawai = '';
                        Pengambilan.tnama_pegawai = '';
                        setTimeout("Pengambilan.cek_verifikasi('" + date_transaction + "','" + _noreg + "')", 1000);
                    }
                }
            });
        }
    },

    cek_selesai: function(elm) {
        var _jmlKandang = $(elm).closest('tbody').find(':checkbox').length;
        var _jmlKandangTerpilih = $(elm).closest('tbody').find(':checked').length;
        return _jmlKandangTerpilih == _jmlKandang ? 1 : 0;
    },

    simpan_konfirmasi_dialog: function(elm, data_ke, _params) {
        this.simpan_konfirmasi(_params, function(result) {
            if (result.result == 1) {
                Pengambilan.get_data_detail_pengambilan(_params, 1, data_ke);
                toastr.success("Konfirmasi berhasil.", "Berhasil");
            } else {
                toastr.error("Konfirmasi gagal.", "Peringatan");
            }
        });
    },

    print: function() {
        toastr.warning('Masih Proses...', 'Peringatan');
    },

    format_datepicker: function(date) {
        var split = date.split(" ");
        return split[2] + '/' +
            $.datepicker.regional['id'].monthNamesShort.indexOf(split[1]) +
            '/' + split[0];
    },
    cek_hutang_sak: function(kodeflok) {
        var _pesan = '<div class="text-center">Dropping pakan tidak dapat dilakukan. <br />Harap melakukan pengembalian seluruh sak kosong.</div>';
        var _result = { status: 0, message: _pesan };
        $.ajax({
            url: 'pengambilan_barang/main/check_sisa_hutang_sak',
            data: { flok: kodeflok },
            success: function(data) {
                if (data.status) {
                    _result['status'] = 1;
                }
            },
            dataType: 'json',
            async: false,
        });
        return _result;
    },
    get_data_detail_pengambilan: function(elm, data_ke) {
        /** jika punya hutang sak gak bisa lanjut */
        var no_order = $(elm).find('td:first').data('no_order');
        var kode_farm = $(elm).find('td:first').data('kode_farm');
        var kode_flok = $(elm).find('td.flok').text();
        var tgl_kirim = $(elm).find('td:eq(2)').text();
        var tgl_kebutuhan = $(elm).find('td:eq(3)').text();
        var jml_dropping = $(elm).find('td.jml_dropping').text();
        var jml_permintaan = $(elm).find('td.jml_permintaan').text();
        var summaryData = {
            no_order: no_order,
            kode_flok: kode_flok,
            tgl_kirim: tgl_kirim,
            tgl_kebutuhan: tgl_kebutuhan,
            jml_dropping: jml_dropping,
            jml_permintaan: jml_permintaan

        };

        if (jml_dropping < jml_permintaan) {
            var r = Pengambilan.cek_hutang_sak(kode_flok);
        } else {
            var r = { status: 1 };
        }
        //r = { status: 1 };
        $.when(r).done(function() {
            if (r.status) {
                Pengambilan.ajax_detail_pengambilan(no_order, data_ke, summaryData);
            } else {
                bootbox.alert(r.message);
            }
        })

    },

    ajax_detail_pengambilan: function(no_order, data_ke, summaryData) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/view",
            data: {
                no_order: no_order,
                summary: summaryData
            },
            success: function(data) {
                $("#main_content").html(data).promise().done(function() {
                    $('input.scan_pallet').focus();
                });
                if (data_ke) {
                    $('tr.tr-header[data-ke="' + data_ke + '"]').dblclick();
                }
            }
        });
    },

    cetak_picking_list: function(e) {
        var no_order = $(e).parents('tr').find('td:first').data('no_order');
        var kode_farm = $(e).parents('tr').find('td:first').data('kode_farm');
        if (kode_farm && no_order) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cetak_picking_list",
                data: {
                    no_order: no_order,
                    kode_farm: kode_farm
                },
                success: function(data) {
                    var _message = data;
                    var box = bootbox.dialog({
                        message: _message,
                        title: "Pengambilan Barang",

                        buttons: {
                            danger: {
                                label: "Keluar",
                                className: "btn-danger",
                                callback: function() {
                                    return true;
                                }
                            }
                        },
                        className: "very-large"
                    });
                }
            });
        }
    },

    cetak_picking_list_pdf: function(no_order) {
        if (no_order) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cetak_picking_list_pdf",
                data: {
                    no_order: no_order
                },
                success: function(data) {
                    window.open(data, '_blank');
                }
            });
        }
    },

    kontrol_chekbox: function(elm) {
        $(elm).is(':checked') ? $(elm).val('1') : $(elm).val('0');

        this.get_data_pengambilan();
    },

    get_data_pengambilan: function() {
        $("#picking-list-table").html('');
        var tanggal_kirim_awal = Config._convertTgl(Config._getDateStr($("#tanggal-kirim-awal").datepicker('getDate')));
        var tanggal_kirim_akhir = Config._convertTgl(Config._getDateStr($("#tanggal-kirim-akhir").datepicker('getDate')));
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/main/get_data_pengambilan",
            data: {
                tanggal_kirim_awal: tanggal_kirim_awal,
                tanggal_kirim_akhir: tanggal_kirim_akhir,

            },
            dataType: 'html',
            success: function(data) {
                $("#picking-list-table").html(data);

            }
        });
    },

    get_data_riwayat_pengambilan: function() {
        $("#tabel-riwayat").html('');
        var no_reg = $("#p_kandang").val();

        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/get_data_riwayat_pengambilan",
            data: {
                no_reg: no_reg
            },
            dataType: 'html',
            success: function(data) {
                $("#tabel-riwayat").html(data);
                var tabel_riwayat = $('#tabel-riwayat>table.table');
                if (tabel_riwayat.length > 0) {
                    tabel_riwayat.scrollabletable({
                        'max_height_scrollable': 300,
                        'scroll_horizontal': 0,
                    });
                }
            }
        });
    },

    kontrol_option: function(e) {
        var data_ke = $(e).parents("tr").attr("data-ke");
        var disabled = $('tr[data-ke="' + data_ke + '"] .berat').attr('disabled');
        var berat = parseFloat($('tr[data-ke="' + data_ke + '"] .berat').val());

        if (typeof disabled == 'undefined') {
            $("#btn-konfirmasi").attr('disabled', true);
            var checked = 0;
            $.each($('#transaction-table table tbody').find('tr'), function() {
                var tmp_data_ke = $(this).attr("data-ke");

                var tmp_disabled = $('tr[data-ke="' + tmp_data_ke + '"] .berat').attr('disabled');

                if (typeof tmp_disabled == 'undefined') {
                    $('tr[data-ke="' + tmp_data_ke + '"] .berat').val("0");
                }
            })
            $('tr[data-ke="' + data_ke + '"] .berat').focus().select();
        }

    },

    kontrol_berat: function(e) {
        var data_ke = $(e).parents("tr").attr("data-ke");
        var berat = parseFloat($(e).val());
        if ($('tr[data-ke="' + data_ke + '"] .radio').is(":checked") && berat &&
            berat > 0) {
            $("#btn-konfirmasi").attr('disabled', false);
            $("#btn-konfirmasi").focus();
        } else {
            toastr.error("Konfirmasi gagal.", "Peringatan");
            $(e).val("0");
            $(e).focus();
        }
    },

    simpan_konfirmasi: function(data, callback) {
        if (data.length == 1) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/simpan_konfirmasi",
                data: {
                    data: data
                },
                dataType: 'json',
                success: function(data) {
                    callback(data);
                }
            });
        }
    },

    get_berat_timbang: function(elm) {
        $(elm).removeAttr('readonly');
        setTimeout(function() {
            var berat = $(elm).val();
            $(elm).val(berat);
            $(elm).attr('readonly', true);
        }, 0);
    },

    replace_timbang: function(elm) {
        $(elm).select().focus().val($(elm).val());

    },

    selected: function(elm) {
        $(elm).select().focus();
    },

    ganti_hand_pallet: function(elm) {
        var status = $(elm).data('status');
        $('span.total_pallet.edit_hand_pallet').removeClass('edit_hand_pallet');
        $(elm).prev().addClass('edit_hand_pallet');
        /* jika sudah nimbang gak boleh ganti kavling */
        var _td_nimbang = $(elm).closest('tr').find('td.berat-timbang').find('input').val();
        if (status == 1 && empty(_td_nimbang)) {
            var url = 'master/hand_pallet/hand_pallet_aktif';
            var data = this.get_m_hand_pallet();
            var _list_hand_pallet = [];
            for (var i in data) {
                _list_hand_pallet.push(
                    '<tr onclick="Pengambilan.set_ganti_hand_pallet(this)">' +
                    '<td class="ghp_kode_hand_pallet">' + data[i]['KODE_HAND_PALLET'] + '</td>' +
                    '<td class="ghp_berat">' + data[i]['BRT_BERSIH'] + '</td>' +
                    '</tr>'
                );
            }
            var _html = [
                '<div class="col-md-12 new-line">',
                '<table class="table table-bordered" id="tbl-ganti-hand-pallet">',
                '<thead>',
                '<tr>',
                '<th class="col-md-2">Hand Pallet</th>',
                '<th class="col-md-2">Berat Timbang (Kg)</th>',
                '</tr>',
                '</thead>',
                '<tbody>',
                _list_hand_pallet.join(''),
                '</tbody>',
                '</table>',
                '</div>'
            ];
            messageBox('Pilih Hand Pallet', _html.join(' '));
        }
    },

    set_ganti_hand_pallet: function(elm) {
        var _tr = $(elm);
        var _berat = _tr.find('td.ghp_berat').text();
        var _elm = $('.edit_hand_pallet:first');
        var _saat_ini = parseFloat(_elm.text());
        var _def_hand_pallet = _elm.closest('td').data('berat_hand_pallet');
        var _def_pallet = _elm.closest('td').data('berat_pallet_murni');
        _elm.text(parseFloat(_def_pallet) + parseFloat(_berat));
        $('.bootbox button.btn-success').click();
    },

    /* pastikan semua kandang sudah input lhk */
    cek_input_lhk: function(flok) {
        var kodeflok = (flok === undefined) ? '' : flok;
        var _result = {};
        $.ajax({
            url: 'permintaan_pakan/permintaan_pakan/cek_input_lhk/' + kodeflok,
            type: 'post',
            dataType: 'json',
            async: false,
            cache: false,
            success: function(data) {
                if (data.status) {
                    if (!empty(data.content['belumInputRhk'])) {
                        _result['status'] = 0;
                        _result['message'] = data.content['belumInputRhk'].join(' <br >') + '<br >  LHK belum dientri';
                    } else if (!empty(data.content['belumAckRhk'])) {
                        _result['status'] = 0;
                        _result['message'] = data.content['belumAckRhk'].join('<br >') + '<br > Mohon lakukan ack LHK terlebih dahulu';
                    } else {
                        /* lhk sudah diinput semua dan sudah ack */
                        _result['status'] = 1;
                    }
                    /* cek pengembalian sak */
                    if (_result['status']) {
                        var _pesan = 'Dropping pakan tidak dapat dilakukan. <br />Harap melakukan pengembalian seluruh sak kosong.';
                        $.ajax({
                            url: 'pengambilan_barang/main/check_sisa_hutang_sak',
                            data: { flok: kodeflok },
                            success: function(data) {
                                if (!data.status) {
                                    _result['status'] = 0;
                                    _result['message'] = _pesan;
                                }
                            },
                            dataType: 'json',
                            async: false,
                        });
                    }
                }
            },
        });
        return _result;
    },
    /** generate hanya dilakukan sebelum adanya lhk */
    generate: function(e) {
        var tanggal_kebutuhan = $(e).attr("data-tanggal-kebutuhan");
        var kode_flok = $(e).attr("data-kode-flok");
        /** generate hanya dilakukan sebelum adanya lhk */
        //var r = Pengambilan.cek_input_lhk(kode_flok);  // sudah gak diperlukan lagi     
        var r = { status: 1 };
        $.when(r).done(function() {
            if (r.status) {
                var proses = $(e).data('proses') == undefined ? 0 : $(e).data('proses');
                if (!proses) {
                    Pengambilan.ajax_generate(e, tanggal_kebutuhan, kode_flok);
                } else {
                    toastr.info('Masih menunggu proses di server', 'Info');
                }
            } else {
                toastr.error(r.message);
            }
        });

    },

    ajax_generate: function(e, tanggal_kebutuhan, kode_flok) {
        var opts = { lines: 11, length: 17, width: 19, radius: 39, scale: 0.75, corners: 1, color: '#000', opacity: 0.5, rotate: 0, direction: 1, speed: 1, trail: 60, fps: 20, zIndex: 2e9, className: 'spinner', top: '50%', left: '49%', shadow: false, hwaccel: false, position: 'absolute' }
        var target = document.getElementById('div_loading');
        var spinner = new Spinner(opts);
        $.ajax({
            type: "POST",
            beforeSend: function() {
                $(e).data('proses', 1);
                $(e).attr('disabled', 'disabled');
                spinner.spin(target);
            },
            url: "pengambilan_barang/main/simpan_generate_permintaan",
            data: {
                tanggal_kebutuhan: tanggal_kebutuhan,
                kode_flok: kode_flok
            },
            dataType: 'json',
            success: function(data) {
                $(e).data('proses', 0);
                $(e).removeAttr('disabled');
                spinner.stop(target);
                if (data.result == 1) {
                    toastr.success('Generate permintaan Berhasil.', 'Informasi');
                    Pengambilan.get_data_pengambilan();
                } else if (data.result == 2) {
                    toastr.error('Generate permintaan harus urut.', 'Peringatan');
                } else if (data.result == 3) {
                    toastr.error('Penerimaan untuk Tanggal Kebutuhan ' + Config._tanggalLocal(tanggal_kebutuhan, '-', ' ') + ' belum lengkap.', 'Peringatan');
                } else if (data.result == 4) {
                    toastr.error('Terdapat pengambilan dari Mutasi Pakan yang belum dilakukan. Untuk Tanggal Kebutuhan ' + Config._tanggalLocal(tanggal_kebutuhan, '-', ' ') + '.', 'Peringatan');
                } else if (data.result == 5) {
                    toastr.error('LHK Tanggal Kebutuhan ' + Config._tanggalLocal(data.tanggal_kirim, '-', ' ') + ' belum dientri.', 'Peringatan');
                } else if (data.result == 6) {
                    toastr.error('Terdapat pengambilan dari Pakan Rusak yang belum dilakukan. Untuk Tanggal Kebutuhan ' + Config._tanggalLocal(tanggal_kebutuhan, '-', ' ') + '.', 'Peringatan');
                } else if (data.result == 7) {
                    toastr.error('Stok gudang tidak ada.', 'Peringatan');
                } else if (data.result == 8) {
                    var _no_pengambilan = $(e).closest('tr').find('td.no_pengambilan').text();
                    toastr.error('Proses generate pengambilan <strong>' + _no_pengambilan + '</strong> sudah dilakukan.', 'Peringatan');
                } else {
                    toastr.error('Generate permintaan Gagal.', 'Peringatan');
                }
            }
        });
    },
    check_pallet: function(elm) {
        var _pallet = $(elm).closest('tr').data('id_pallet');
        var _scan = $.trim($(elm).val());
        if (empty($(elm).closest('tr').find('td.berat-bersih').text())) {
            bootbox.alert('Belum dilakukan penimbangan');
            return;
        }
        if (_pallet == _scan) {
            this.show_detail_kandang(elm);
        } else {
            bootbox.alert('Barcode pallet tidak sesuai', function() {
                $(elm).val('');
                setTimeout(function() {
                    $(elm).focus();
                }, 1000);

            });

        }
    },
    pilih_nomer_order: function(elm) {
        var _rfID = $(elm).val();
        if (!empty(_rfID)) {
            $.ajax({
                type: "POST",
                url: "api/general/kandang",
                data: {
                    rfid: _rfID
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        var _flok = data.content.flok_bdy;
                        var _flok_td = $('#summary_picking_list tbody').find('tr>td.flok[data-flok=' + _flok + ']');
                        if (_flok_td.length) {
                            _flok_td.eq(0).closest('tr').trigger('dblclick');
                        } else {
                            toastr.warning('Pengambilan untuk flok ' + _flok + ' tidak ditemukan');
                        }
                    } else {
                        toastr.warning('Data rfid tidak ditemukan', 'Gagal');
                    }
                    $(elm).val('');
                    $(elm).focus();
                }
            });
        }
    },
    pilih_kavling: function(elm) {
        var _kode_pallet = $(elm).val();
        if (!empty(_kode_pallet)) {
            var _palletElm = $('#transaction-table>table>tbody>tr[data-id_kavling="' + _kode_pallet + '"]');
            if (_palletElm.length) {
                _palletElm.trigger('dblclick');
            } else {
                toastr.warning('Pallet ' + _kode_pallet + ' tidak ditemukan');
            }
            $(elm).val('');
        }

    }
};
(function() {
    'use strict';
    $('div').on('click', 'a.btn', function(e) {
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })
    $('ul.pagination').on('click', 'a', function(e) {
        Home.load_main_content(e, e.target, e.target.href, '#main_content');
    })
    $("#tanggal-kirim-awal").datepicker({
        dateFormat: 'dd M yy',
        maxDate: new Date(),
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {
                $('#tanggal-kirim-akhir').datepicker('option', 'minDate', date);
            }
        }
    });
    $("#tanggal-kirim-akhir").datepicker({
        dateFormat: 'dd M yy',
        maxDate: new Date(),
        onSelect: function(date, lastDate) {
            if (lastDate.lastVal != date) {
                $('#tanggal-kirim-awal').datepicker('option', 'maxDate', date);
            }
        }
    });

    $(".berat.old").keydown(function(event) {
        return false;
    });
    $('input.scan_rfid').focus();
    $('input.berat-timbang').numeric({
        allowPlus: false, // Allow the + sign
        allowMinus: false, // Allow the - sign
        allowThouSep: false, // Allow the thousands separator, default is the
        // comma eg 12,000
        allowDecSep: true
            // Allow the decimal separator, default is the fullstop eg 3.141
    });
    Pengambilan.get_data_pengambilan();
    //Pengambilan.get_data_riwayat_pengambilan();
}())