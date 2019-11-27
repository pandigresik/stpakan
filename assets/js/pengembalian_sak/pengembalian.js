var Pengembalian = {
    _begin_detail_pengembalian: 0,
    _flok_selected: 0,
    _timer_get_timbang: false,
    _total_jml_sak: 0,
    _total_berat_timbang: 0,
    _total_berat_timbang_kg: 0,
    _date_transaction: null,
    _verificator: null,
    _master_noreg: [],
    _tmp_noreg: [],
    _tmp_date_finger: [],
    _hasil_simpan: 1,
    _rfid_scanned: [],
    _num_cek: 0,
    _tmp_pengawas: [],
    _batas_atas: 0.134,
    _batas_bawah: 0.114,
    _bisa_entri: 0,
    /*finger*/
    date_finger: '',
    noreg_finger: '',
    /*end finger*/

    timer: true,
    verifiedFinger: false,
    _error_msg: '',
    /*add_datepicker : function(elm,options){
    	elm.datepicker(options);
    },*/
    /* periksa apakah berat yang diinput sudah sesuai dengan standart */
    berat_sesuai: function(jmlsak, beratsak) {
        var bts_atas = this._batas_atas;
        var bts_bawah = this._batas_bawah;
        var berat_atas = jmlsak * bts_atas;
        var berat_bawah = jmlsak * bts_bawah;
        if (parseFloat(beratsak) > berat_atas) {
            Pengembalian._error_msg = 'Berat timbang melebihi batas toleransi';
            return 0;
        } else if (parseFloat(beratsak) < berat_bawah) {
            Pengembalian._error_msg = 'Berat timbang kurang dari batas toleransi';
            return 0;
        } else {
            Pengembalian._error_msg = '';
            return 1;
        }
    },

    transaksi: function(elm, target) {
        var tgl_server = $('#tanggal_server').data('tanggal_server');
        var no_pengembalian = $(elm).data('no_pengembalian') || null;
        var status = $(elm).data('status') || null;

        $.ajax({
            type: 'post',
            data: { no_pengembalian: no_pengembalian, status: status, tgl_server: tgl_server },
            url: 'pengembalian_sak/pengembalian/transaksi',
            dataType: 'html',
            async: false,
            success: function(data) {
                $(target).html(data);
            },
        }).done(function() {
            if (empty(no_pengembalian)) {

            }
        });
    },

    detail_transaksi: function(no_reg) {
        var data_tipe = '';
        var rowid_val = 0;
        if (Pengembalian._begin_detail_pengembalian == 1) {
            data_tipe = 'append';
            $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody').find('tr').each(function() {
                rowid_val++;
            });
        }
        $.ajax({
            type: 'post',
            data: { no_reg: no_reg, tipe: data_tipe, rowid: rowid_val },
            url: 'pengembalian_sak/pengembalian/detail_transaksi',
            dataType: 'html',
            async: false,
            success: function(data) {
                if (Pengembalian._begin_detail_pengembalian == 0) {
                    $('#tabel_pengembalian_sak').html(data);
                    Pengembalian._begin_detail_pengembalian = 1;
                } else {
                    $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody').append(data);
                    var hitung = 0;
                    $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody').find('tr').each(function() { hitung++; });
                    $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody tr #berat_timbang_sak').attr('rowspan', hitung);
                }
            },
        }).done(function() {
            $('#tabel_pengembalian_sak').find('input:first').focus();
        });
    },

    show_detail_timbang: function(elm) {
        var d_timbang = $(elm).next('tr.detail_timbang');
        if (d_timbang.is(':hidden')) {
            d_timbang.show();
            d_timbang.find('input.number:not(".numeric")')
                .addClass('numeric').priceFormat({
                    prefix: '',
                    centsLimit: 0,
                    thousandsSeparator: '.'
                });
        } else {
            d_timbang.hide();
        }
    },
    /* cek dahulu apakah jml pengembalian < outstanding sak */
    timbang_lagi: function(elm) {
        var _tr = $(elm).closest('tr');
        var _table = _tr.closest('table');
        var _tr_detail = _table.closest('tr');
        var _tr_header = _tr_detail.prev();
        var _total_pakai = parseInt(_tr_header.data('jml_pakai'));
        var _sudahretur = parseInt(_tr_header.data('jml_retur'));
        /* pastikan sudah diisi semua */
        var _error = 0;
        var _field = {};
        _tr.find('input').each(function(i) {
            if ($(this).val() <= 0) {
                _error++;
                _field[i] = $(this).data('field');
            }
        });
        /* pastikan brt_penimbangan tidak melebihi batas konversi berat */
        var _jmlretursak = parse_number(_tr.find('input[name=jml_pengembalian]').val(), '.', ',');
        var _brtretursak = parse_number(_tr.find('input[name=brt_pengembalian]').val(), '.', ',');
        if (!Pengembalian.berat_sesuai(_jmlretursak, _brtretursak)) {
            _error++;
            toastr.error('Berat sak tidak sesuai');
        }

        if (!_error) {
            var _jml_pengembalian = 0;
            _table.find('input[name=jml_pengembalian]').each(function() {
                _jml_pengembalian += parseInt($(this).val());
            });
            var _total_retur = _jml_pengembalian + _sudahretur;
            if (_total_retur <= _total_pakai) {
                //				if(_jml_pengembalian != _outstanding){
                var _new_tr = _tr.clone();
                _new_tr.find('input').val(0);
                _new_tr.appendTo(_table);
                _new_tr.find('input.numeric').priceFormat({
                    prefix: '',
                    centsLimit: 0,
                    thousandsSeparator: '.'
                });
                //				}
                $(elm).remove();
                _tr.find('input').attr('readonly', true);
                _tr.addClass('siap_simpan');
                _tr_header.find('td.jml_aktual').text(_jml_pengembalian);
            } else {
                toastr.error('Total penimbangan melebihi jumlah sak yang telah dikirim ke kandang');
            }
        }
        for (var i in _field) {
            toastr.error(_field[i] + ' harus lebih besar dari 0');
        }
    },
    /* simpan pengembalian sak */
    simpan: function(elm) {
        /* cek apakah sudah ada yang bisa disimpan */
        var _ini = this;
        var _siap_simpan = $('#tabel_detail_pengembalian_sak tbody tr');
        var _pesan = [];
        var _error = 0;
        if (_siap_simpan.length) {
            var _detail = {},
                _total_sak_semua = 0,
                _tmp = {};
            var _no_reg = $('#tabel_detail_pengembalian_sak').data('no_reg');
            var _jml_kembali, _brt_kembali, _kode_barang, _jenis_kelamin, _target_kembali, _lepas_kontrol;
            var _total_kembali = {}; /* total pengembalian sak per kode barang */
            var _total_outstanding = {}; /* total outstanding sak per kode barang */
            _siap_simpan.each(function() {
                _jml_kembali = parse_number($(this).find('input[name=jml_pengembalian]').val(), '.', ',');
                _kode_barang = $(this).data('kode_barang');
                _jenis_kelamin = $(this).data('jenis_kelamin');
                if (_jml_kembali <= 0) {
                    return;
                }

                /** pastikan yang diinput = target pengembalian */
                _target_kembali = $(this).find('input[name=jml_pengembalian]').data('maxvalue');
                _lepas_kontrol = $(this).find('input[name=jml_pengembalian]').data('lepaskontrol');
                if (_jml_kembali != _target_kembali) {
                    if(!_lepas_kontrol){
                        _error++;
                        _pesan.push('Jumlah pengembalian sak belum sesuai dengan jumlah pemakaian');
                        return false;
                    }
                }
                if (_total_kembali[_kode_barang] === undefined) {
                    _total_kembali[_kode_barang] = 0;
                }
                if (_detail[_kode_barang] === undefined) {
                    _detail[_kode_barang] = {};
                }
                if (_detail[_kode_barang][_jenis_kelamin] === undefined) {
                    _detail[_kode_barang][_jenis_kelamin] = [];
                }
                _tmp = { jml_k: _jml_kembali, brt_k: _brt_kembali, kb: _kode_barang, jk: _jenis_kelamin };
                _detail[_kode_barang][_jenis_kelamin].push(_tmp);
                _total_kembali[_kode_barang] += _jml_kembali;

                _total_sak_semua += _jml_kembali;
            });

            /** cek apakah bisa entri timbangan atau tidak */
            if ((_total_sak_semua * this._batas_bawah) <= 1) {
                this._bisa_entri = 1;
            }


            if (!_error) {
                if (empty(_detail)) {
                    _pesan.push('Belum dilakukan proses penimbangan atau jumlah pengembalian <= 0');
                    _error++;
                }
            }

            if (!_error) {

                bootbox.confirm({
                    title: 'Konfirmasi Pengembalian',
                    message: 'Apakah yakin akan melakukan penyimpanan ?',
                    buttons: {
                        'cancel': {
                            label: 'Tidak',
                            className: 'btn-default'
                        },
                        'confirm': {
                            label: 'Ya',
                            className: 'btn-danger'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            var rowid = 0;
                            var jml_target = 0;
                            var input_jml = 0;
                            var _error_flag = 0;
                            $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody').find('tr .input_jml_kembali').each(function() {
                                rowid++;
                                jml_target += parseInt($('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody #tr' + rowid + ' .target_kembali').html());
                                input_jml += parseInt($('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody #tr' + rowid + ' .input_jml_kembali').val());
                            });

                            
                            if (jml_target != input_jml) {
                                _error_flag = 1;
                            }
                            
                            if(_lepas_kontrol){
                                _error_flag = 0;
                            }
                            
                            if (!_error_flag) {
                                Pengembalian._total_jml_sak = input_jml;
                                Pengembalian.tampil_dialog_timbang();
                            } else {
                                bootbox.confirm({
                                    title: 'Simpan Gagal!',
                                    message: '<br><br><center><b style="font-size:13pt;">Jumlah pengembalian sak belum<br>sesuai dengan jumlah pemakaian</b>',
                                    callback: function() {}
                                });
                            }
                        }
                    }
                });
            } else {
                if (!empty(_pesan)) {
                    toastr.error(_pesan.join(''), 'Simpan gagal');
                } else {
                    toastr.error('Belum ada penimbangan sak atau jumlah sak atau berat timbang harus lebih besar dari 0');
                }
            }

        } else {
            toastr.error('Tidak ada yang akan disimpan, belum ada penimbangan sak');
        }
    },
    tampil_dialog_timbang: function() {
        var _readonly_input = !this._bisa_entri ? 'onfocus="Home.getDataTimbang(this)" readonly' : '';
        bootbox.dialog({
            title: '',
            message: '<br><center><b style="font-size:14pt;">Silahkan melakukan<br>penimbangan sak kembali</b>' +
                '<br><br>' +
                '<input id="dlg_hasil_timbang" class="form-control" style="width:30%;display:inline;" onblur="Pengembalian.check_hasil_timbang(this)" type="text" ' + _readonly_input + '>' +
                '<b style="display:inline;margin-left:10px;">Kg</b></center>' +
                '<br>' +
                '<center><b  style="color:red;" id="dlg_timbang_alert"></b></center>',
            onEscape: function() {
                //Pengembalian._timer_get_timbang = false;
                return true;
            },
            callback: function() {}
        });

    },

    check_hasil_timbang: function(elm) {
        var berat = $(elm).val();
        if(empty(berat)){
            berat = 0;
        }
        //$('.modal-dialog #dlg_hasil_timbang').val(parseFloat(berat).toFixed(3));
        Pengembalian._total_berat_timbang_kg = berat;
        Pengembalian._total_berat_timbang = parseInt(berat * 1000);

        var check = Pengembalian.berat_sesuai(Pengembalian._total_jml_sak, Pengembalian._total_berat_timbang_kg);
        if (check == 1) {
            bootbox.hideAll();
            Pengembalian.finger_pengawas();
            $('.modal-dialog #dlg_timbang_alert').html('');
        } else {
            $('.modal-dialog #dlg_timbang_alert').html(Pengembalian._error_msg);
        }
    },


    finger_pengawas: function() {
        Pengembalian.simpan_transaksi_verifikasi(function(result) {
            if (result.date_transaction) {
                Pengembalian._tmp_date_finger.push(result.date_transaction);
            }
        });
    },

    simpan_transaksi_verifikasi: function(callback) {
        $.ajax({
            type: "POST",
            url: "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
            data: {
                transaction: 'pengembalian_sak'
            },
            dataType: 'json',
            success: function(data) {
                callback(data);
            }
        });
        Pengembalian.tampil_notif_finger();
    },
    tampil_notif_finger: function() {
        var data_noreg = Pengembalian._tmp_noreg[0];
        var arr_noreg = data_noreg.split('/');
        var no_kandang = arr_noreg[2];
        var _message = "<center><b id='finger_label' style='font-size:12.5pt;text-align:center;'>Silahkan Melakukan<br>Fingerprint Pengawas" +
            "<br>Kandang " + no_kandang + "</b></center>" +
            "<center><img src='assets/images/finger.jpg' height='260px' style='filter:invert(100%);'></center>" +
            "<center><b><p id='alert_finger' style='color:red;'></p></b></center>";
        var box = bootbox.dialog({
            message: _message,
            closeButton: true,
            title: "",
            onEscape: function() {
                Pengembalian.timer = false;
                Pengembalian._tmp_noreg = Pengembalian._master_noreg;
                Pengembalian._tmp_date_finger = [];
                Pengembalian.verifiedFinger = false;
                return true;
            }
        });
        box.bind('shown.bs.modal', function() {
            Pengembalian.timer = true;
            Pengembalian.date_finger = Pengembalian._tmp_date_finger[0];
            Pengembalian.noreg_finger = Pengembalian._tmp_noreg[0];
            Pengembalian.cek_verifikasi();
            //Pengembalian.cek_verifikasi(Pengembalian._tmp_date_finger[0], Pengembalian._tmp_noreg[0], 'PENGAWAS');
        });
    },
    cek_verifikasi: function() {
        var _ini = this;
        var cek_pos = Pengembalian._num_cek;
        var _noreg = Pengembalian.noreg_finger;
        var finger_pengawas = '';
        if (_ini.timer == true) {
            $.ajax({
                type: "POST",
                url: "pengambilan_barang/transaksi/cek_verifikasi",
                data: {
                    date_transaction: Pengembalian.date_finger,
                    noreg: _noreg,
                    level: 'PENGAWAS',
                },
                dataType: 'json',
                success: function(data) {
                    /*var indexVal = _ini._tmp_noreg.indexOf(_noreg);
                    _ini._tmp_noreg.splice(indexVal, 1);
                    _ini._tmp_date_finger.splice(indexVal, 1);*/

                    if (data.status == 1) {
                        var indexVal = _ini._tmp_noreg.indexOf(_noreg);
                        _ini._tmp_noreg.splice(indexVal, 1);
                        _ini._tmp_date_finger.splice(indexVal, 1);
                        if (data.match == 0) {
                            $.ajax({
                                type: "POST",
                                url: "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
                                data: {
                                    transaction: 'pengembalian_sak'
                                },
                                dataType: 'json',
                                success: function(data) {
                                    Pengembalian._tmp_noreg.push(_noreg);
                                    Pengembalian._tmp_date_finger.push(data.date_transaction);
                                }
                            });
                            $('#alert_finger').html('Fingerprint Tidak Sesuai');
                            _ini.verifiedFinger = false;
                            _ini.timer = true;
                        } else {
                            finger_pengawas = data.kode_pengawas;
                            /*var indexVal = _ini._tmp_noreg.indexOf(_noreg);
                            _ini._tmp_noreg.splice(indexVal, 1);
                            _ini._tmp_date_finger.splice(indexVal, 1);*/
                            $('#alert_finger').html('');
                            var check_pengawas = _ini._tmp_pengawas.indexOf(finger_pengawas);
                            if (check_pengawas > 0 || _ini._tmp_noreg.length == 0) {
                                _ini.timer = false;
                                _ini.verifiedFinger = true;
                                $('.bootbox').modal('hide');
                                _ini._tmp_pengawas = [];
                                Pengembalian.prepare_simpan();
                            } else {
                                var data_noreg = Pengembalian._tmp_noreg[0];
                                var arr_noreg = data_noreg.split('/');
                                var no_kandang = arr_noreg[2];
                                $('#finger_label').html('Silahkan Melakukan<br>Fingerprint Pengawas<br>Kandang ' + no_kandang);
                                _ini.time = true;
                                _ini.verifiedFinger = false;
                                $.ajax({
                                    type: "POST",
                                    url: "pengambilan_barang/transaksi/simpan_transaksi_verifikasi",
                                    data: {
                                        transaction: 'pengembalian_sak'
                                    },
                                    dataType: 'json',
                                    success: function(data) {
                                        Pengembalian._tmp_date_finger.push(data.date_transaction);
                                    }
                                });
                            }
                        }
                    } else {
                        _ini.timer = true;
                    }

                    if (_ini.timer == true) {
                        cek_pos = 0;
                        _ini.date_finger = _ini._tmp_date_finger[cek_pos];
                        _ini.noreg_finger = _ini._tmp_noreg[cek_pos];
                        setTimeout("Pengembalian.cek_verifikasi()", 1000);
                        //setTimeout("Pengembalian.cek_verifikasi('"+_ini._tmp_date_finger[cek_pos]+"','"+_ini._tmp_noreg[cek_pos]+"','PENGAWAS')", 1000);
                    }
                }
            });
        }
    },

    list_awal: function(elm) {
        $.ajax({
            url: 'pengembalian_sak/pengembalian/list_pengembalian',
            type: 'post',
            dataType: 'html',
            async: false,
            beforeSend: function() {
                $('#list_pengembalian').html(' Silakan tunggu ....');
            },
            success: function(data) {
                $('#list_pengembalian').html(data);
                if ($('#list_pengembalian').find('table tbody').height() >= 200) {
                    var y = $('#list_pengembalian').find('table thead');
                    y.width(y.width() - 16);
                }

            }
        });
    },

    filter_content: function(elm) {
        var _table = $(elm).closest('table');
        var _tbody = _table.find('tbody');
        var _content = $(elm).val();
        var _target = $(elm).data('target');

        _tbody.find('td.' + _target + ':contains(' + _content.toUpperCase() + ')').parent().show();
        _tbody.find('td.' + _target + ':not(:contains(' + _content.toUpperCase() + '))').parent().hide();

        if ($('#list_pengembalian').find('table tbody').height() >= 300) {
            var y = $('#list_pengembalian').find('table thead');
            y.width(y.width() - 16);
        }
    },
    checkMaxValue: function(elm) {
        var _max = $(elm).data('maxvalue');
        var _lhk = $(elm).data('maxpakai');
        var _val = $(elm).val();

        if (_val > _max) {
            $(elm).val(_max);
            toastr.warning('Jumlah yang diinput melebihi stok yang ada yaitu ' + _max + '.');
        }
        var _tr = $(elm).closest('tr');
        _tr.next().find('input:first').focus();
    },
    setFocus: function(elm) {
        var _val = $(elm).val();
        if (_val != '') {
            var _tr = $(elm).closest('tr');
            _tr.find('input[name=jml_pengembalian]').focus();
        }
    },
    scanRFID: function(elm) {
        var _rfID = $.trim($(elm).val());
        var _minimumRFID = 8;
        var insert_noreg = 0;
        $(elm).next('span.help-block').html('');
        $('#div_tombol_simpan .btn').addClass('disabled');
        $(elm).closest('div').removeClass('has-error');
        if (_rfID.length >= _minimumRFID) {
            $.ajax({
                type: "POST",
                url: "pengembalian_sak/pengembalian/getRFID",
                data: {
                    rfid: _rfID
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        //if(Pengembalian._tmp_pengawas.length > 0){
                        /*if(Pengembalian._tmp_noreg.length > 0){
                        	var check = -1;
                        	//check = Pengembalian._tmp_pengawas.indexOf(data.content['pengawas']);
                        	check = Pengembalian._tmp_noreg.indexOf(data.content['pengawas']);
                        	if(check < 0){
                        		//Pengembalian._tmp_pengawas.push(data.content['pengawas']);
                        		Pengembalian._tmp_noreg.push(data.content['no_reg']);
                        		Pengembalian._master_noreg.push(data.content['no_reg']);
                        	}
                        }else{
                        	//Pengembalian._tmp_pengawas.push(data.content['pengawas']);
                        	Pengembalian._tmp_noreg.push(data.content['no_reg']);
                        	Pengembalian._master_noreg.push(data.content['no_reg']);
                        }*/
                        if (Pengembalian._begin_detail_pengembalian == 0) {
                            Pengembalian._flok_selected = data.content['flok_bdy'];
                        }
                        if (Pengembalian._flok_selected == data.content['flok_bdy']) {
                            $(elm).data('kode_flok', data.content.flok_bdy);
                            $(elm).closest('div').addClass('has-success');
                            $('#div_tombol_simpan .btn').removeClass('disabled');
                            $(elm).next('span.help-block').html(data.content.nama_kandang);
                            if (Pengembalian._rfid_scanned.length == 0 || Pengembalian._rfid_scanned.indexOf(_rfID) < 0) {
                                Pengembalian.detail_transaksi(data.content.no_reg);
                                Pengembalian._rfid_scanned.push(_rfID);
                                if (Pengembalian._tmp_pengawas.length == 0) {
                                    insert_noreg = 1;
                                    Pengembalian._tmp_pengawas.push(data.content['pengawas']);
                                } else {
                                    var check_pengawas = Pengembalian._tmp_pengawas.indexOf(data.content['pengawas']);
                                    if (check_pengawas < 0) {
                                        insert_noreg = 1;
                                        Pengembalian._tmp_pengawas.push(data.content['pengawas']);
                                    } else {
                                        insert_noreg = 0;
                                    }
                                }
                            }
                            var detailCheck = 0;
                            $('#tabel_detail_pengembalian_sak tbody tr').each(function() {
                                detailCheck++;
                            });
                            if (detailCheck == 0) {
                                Pengembalian._begin_detail_pengembalian = 0;
                                Pengembalian._flok_selected = '';
                            } else {
                                if (insert_noreg == 1) {
                                    Pengembalian._tmp_noreg.push(data.content['no_reg']);
                                    Pengembalian._master_noreg.push(data.content['no_reg']);
                                    insert_noreg = 0;
                                }
                            }
                        } else {
                            $(elm).next('span.help-block').html('Kandang tidak memiliki flock yang sama');
                            $(elm).closest('div').addClass('has-error');
                        }
                    } else {
                        $(elm).next('span.help-block').html(data.message);
                        $(elm).closest('div').addClass('has-error');
                        //$('#tabel_pengembalian_sak').html('');
                    }
                }
            });
        } else {
            $(elm).next('span.help-block').html('Minimal RFID ' + _minimumRFID + ' karakter');
            $(elm).closest('div').addClass('has-error');
        }
    },
    prepare_simpan: function() {
        Pengembalian._tmp_date_finger = [];
        Pengembalian._tmp_noreg = [];
        var _ini = this;
        var _siap_simpan = $('#tabel_detail_pengembalian_sak tbody tr');
        var _siap_simpan_length = _siap_simpan.length;
        var _pesan = [];
        var _error = 0;
        var this_noreg = '';
        var rata_rata = parseInt(Pengembalian._total_berat_timbang) / parseInt(Pengembalian._total_jml_sak);
        var dataLen = 0;

        var arr_detail = [];
        var arr_header_pj = [];
        var master_noreg = [];

        if (_siap_simpan.length) {
            var _detail = {},
                _tmp = {};
            var _jml_kembali, _brt_kembali, _kode_barang, _jenis_kelamin, _target_kembali;
            var _total_kembali = {}; /* total pengembalian sak per kode barang */
            var _total_outstanding = {}; /* total outstanding sak per kode barang */

            var _header_pj = {};
            var _tr_header_pj;
            var _jml_pakai, _jml_kirim, _jml_retur, _hutang, _jml_kembali;

            _siap_simpan.each(function() {
                dataLen++;
                var select_data = $(this).find('input[name=jml_pengembalian]');
                if (this_noreg == "") {
                    this_noreg = select_data.data('noreg');
                    master_noreg.push(this_noreg);
                }

                if (this_noreg != select_data.data('noreg')) {
                    for (var kb in _detail) {
                        if (_header_pj[kb] === undefined) {
                            _header_pj[kb] = {};
                        }
                        for (var jk in _detail[kb]) {
                            if (_header_pj[kb][jk] === undefined) {
                                _header_pj[kb][jk] = {};
                            }
                            _jml_kembali = 0;
                            for (var x in _detail[kb][jk]) {
                                _jml_kembali += parseInt(_detail[kb][jk][x].jml_k);
                            }
                            _tr_header_pj = select_data.data('jenis_kelamin');
                            _jml_pakai = select_data.data('jml_pakai');
                            _jml_kirim = select_data.data('jml_kirim');
                            _jml_retur = select_data.data('jml_retur');
                            _keterangan = '';
                            _hutang = parseInt(_jml_pakai) - (parseInt(_jml_retur) + _jml_kembali);
                            _header_pj[kb][jk] = { pakai: _jml_pakai, hutang: _hutang, kirim: _jml_kirim, keterangan: _keterangan };
                        }
                    }

                    arr_detail.push(_detail);
                    arr_header_pj.push(_header_pj);
                    _detail = {};
                    _tmp = {};
                    _header_pj = {};
                    this_noreg = select_data.data('noreg');
                    master_noreg.push(this_noreg);
                }

                _jml_kembali = parse_number(select_data.val(), '.', ',');
                _brt_kembali = parseInt(rata_rata) * parseInt(_jml_kembali);
                _kode_barang = select_data.data('kode_barang');
                _jenis_kelamin = select_data.data('jenis_kelamin');

                if (_total_kembali[_kode_barang] === undefined) { _total_kembali[_kode_barang] = 0; }
                if (_detail[_kode_barang] === undefined) { _detail[_kode_barang] = {}; }
                if (_detail[_kode_barang][_jenis_kelamin] === undefined) { _detail[_kode_barang][_jenis_kelamin] = []; }

                _tmp = { jml_k: _jml_kembali, brt_k: _brt_kembali, kb: _kode_barang, jk: _jenis_kelamin };
                _detail[_kode_barang][_jenis_kelamin].push(_tmp);
                _total_kembali[_kode_barang] += _jml_kembali;

                if (_siap_simpan.length == dataLen) {
                    for (var kb in _detail) {
                        if (_header_pj[kb] === undefined) {
                            _header_pj[kb] = {};
                        }
                        for (var jk in _detail[kb]) {
                            if (_header_pj[kb][jk] === undefined) {
                                _header_pj[kb][jk] = {};
                            }
                            _jml_kembali = 0;
                            for (var x in _detail[kb][jk]) {
                                _jml_kembali += parseInt(_detail[kb][jk][x].jml_k);
                            }
                            _tr_header_pj = select_data.data('jenis_kelamin');
                            _jml_pakai = select_data.data('jml_pakai');
                            _jml_kirim = select_data.data('jml_kirim');
                            _jml_retur = select_data.data('jml_retur');
                            _keterangan = '';
                            _hutang = parseInt(_jml_pakai) - (parseInt(_jml_retur) + _jml_kembali);
                            _header_pj[kb][jk] = { pakai: _jml_pakai, hutang: _hutang, kirim: _jml_kirim, keterangan: _keterangan };
                        }
                    }

                    arr_detail.push(_detail);
                    arr_header_pj.push(_header_pj);
                    _detail = {};
                    _tmp = {};
                    _header_pj = {};
                }
            });

            var dataLen = arr_detail.length;
            for (var i = 0; i < dataLen; i++) {
                if (Pengembalian._hasil_simpan) {
                    Pengembalian.simpanData(arr_detail[i], master_noreg[i], arr_header_pj[i], dataLen, i);
                }
            }
        }
    },
    simpanData: function(_detail, _no_reg, _header_pj, dataLen, dataIndex) {
        $.ajax({
            url: 'pengembalian_sak/pengembalian/simpan',
            type: 'post',
            data: { data: _detail, noreg: _no_reg, headerpj: _header_pj },
            success: function(data) {
                if (data.status) {
                    Pengembalian._hasil_simpan = 1;
                    if (dataIndex == (dataLen - 1)) {
                        $('#input_kandang_pengembalian').val('');
                        $('#input_kandang_pengembalian').css('border-color', 'grey');
                        $('#input_kandang_pengembalian').attr('disabled', 'disabled');
                        $('#div_tombol_simpan .btn').addClass('disabled');
                        $('.help-block').html('');
                        var rowid = 0;
                        $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody').find('tr .jml_kembali').each(function() {
                            rowid++;
                            var setValue = $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody #tr' + rowid + ' .input_jml_kembali').val();
                            $('#tabel_pengembalian_sak #tabel_detail_pengembalian_sak tbody #tr' + rowid + ' .jml_kembali').html(setValue);
                        });
                        $('#berat_timbang_sak').html(parseFloat(Pengembalian._total_berat_timbang).toFixed(3));
                        Pengembalian._rfid_scanned = [];
                        Pengembalian._master_noreg = [];
                        Pengembalian._tmp_noreg = [];
                        Pengembalian._tmp_date_finger = [];
                    }
                } else {
                    Pengembalian._hasil_simpan = 0;
                    toastr.error('Pengembalian gagal disimpan');
                    _ini.verifiedFinger = false;
                    Pengembalian._tmp_noreg = Pengembalian._master_noreg;
                }
            },
            dataType: 'json',
            async: false,
        });
    },

};
$(function() {
    Pengembalian.list_awal();
    'use strict';
    var tgl_server = $('#tanggal_server').data('tanggal_server');
    Config._setTglServer(tgl_server);
    Pengembalian._bisa_entri = parseInt($('#status_lock_timbang').text());
}());