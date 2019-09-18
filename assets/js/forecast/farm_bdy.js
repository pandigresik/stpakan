(function() {
    'use strict';
    /* set tanggal server */
    var _tglServer = $('#tanggal_server').data('tanggal_server');
    Config._setTglServer(_tglServer);

    var lockEditDocIn = $('#lockEditDocIn').text();
    Forecast.setLockEditDocIn(lockEditDocIn.split(','));

    /*
	var canCreateForecast = $('#canCreateForecast').text();
	var lockEditPakan = $('#lockEditPakan').text();

	$('#canCreateForecast').remove();
	Forecast.canCreateForecast = canCreateForecast;
	Forecast.lockRubahPakan = lockEditPakan.split(',');
*/
    /* jadikan draggable untuk blok tanggal doc in */
    var _elmDrag = $('#div_forecast ul>li>a').closest('li');
    var _elmDrop = _elmDrag.closest('ul').closest('li');
    var _minTglDocInStandartBaru = $('#minTglDocInStandartBaru').text();
    /* perbaiki tampilan tree */
    var _text = '',
        _cb, _adaCekbox = [1, 'RJ'],
        tmpTooltip = {};
    AktivasiKandang.overrideNilaiConfig(function() {
        $('#div_forecast ul>li>a,#div_kandang_pending ul>li>a,#div_kandang_konfirmasi ul>li>a').each(function() {
            _text = $(this).text().split('#');

            if ($(this).closest('div').attr('id') == 'div_kandang_pending') {
                _cb = _text[2];
                //console.log(_cb);
                var _t_idfarm = Config.mappingHeader(_text[1].split('/'));
                if (in_array(_cb, _adaCekbox)) {
                    /* jika hari ini < dari tanggal docin - 6, disable checkbox */
                    var _t_noreg = AktivasiKandang.parseElmKandang($(this));
                    var _tgldocin = Config._convertTgl([_t_noreg.tahun, Config._indexBulan(_t_noreg.bulan), _t_noreg.tanggal].join('-'));
                    var _tglServerDate = new Date(Config._tglServer);
                    var _maxKonfirmasiDate = new Date(_tgldocin);
                    var _minKonfirmasiDate = new Date(_tgldocin);
                    _maxKonfirmasiDate.setDate(_maxKonfirmasiDate.getDate() - AktivasiKandang.maxKonfirmasi);
                    _minKonfirmasiDate.setDate(_minKonfirmasiDate.getDate() - AktivasiKandang.minKonfirmasi);
                    var _disable = '';
                    if (_tglServerDate < _minKonfirmasiDate) {
                        _disable = 'disabled';
                    }
                    if (_tglServerDate > _maxKonfirmasiDate) {
                        _disable = 'disabled';
                    }

                    $('<input type="checkbox" class="kandangKonfirmasi" ' + _disable + ' onclick="AktivasiKandang.akanKonfirmasi(this,\'#tabelAkanKonfirmasi\')" style="opacity:1" />').insertBefore($(this));
                    $(this).css({
                        'background': 'none'
                    });
                    if (_cb == 'RJ') {
                        AktivasiKandang.setKandangReject(_t_idfarm.kode_farm, _tgldocin, _text[3]);
                    }
                    /* untuk menampilkan tooltip pada kandang yang bersangkutan di tgl doc yang akan datang */
                    if (tmpTooltip[_t_idfarm.kode_farm] == undefined) {
                        tmpTooltip[_t_idfarm.kode_farm] = {};
                    }
                    if (tmpTooltip[_t_idfarm.kode_farm][_t_idfarm.kandang] == undefined) {
                        tmpTooltip[_t_idfarm.kode_farm][_t_idfarm.kandang] = Config._tanggalLocal(_tgldocin, '-', ' ');
                    }
                } else {

                    if (tmpTooltip[_t_idfarm.kode_farm] != undefined) {
                        if (tmpTooltip[_t_idfarm.kode_farm][_t_idfarm.kandang] != undefined) {
                            $(this).attr('title', 'Anda dapat memilih Kandang ' + _t_idfarm.kandang + ' di tanggal ' + tmpTooltip[_t_idfarm.kode_farm][_t_idfarm.kandang]).tooltip();
                        }
                    }
                }
            }

            $(this).text(_text[0]).css({ 'color': AktivasiKandang.statusWarna[_text[2]] });
            $('<span class="hide" data-value="detail_kandang">' + _text[1] + '</span><span class="_status_approval hide">' + _text[2] + '</span><span class="no_reg hide">' + _text[3] + '</span>').insertAfter($(this));

        });
    });


    /* tambahkan contextmenu untuk tahun dan bulan */
    $('#div_forecast ul>li>label,#div_kandang_pending ul>li>label,#div_kandang_konfirmasi ul>li>label').each(function() {
        if (Forecast.is_bulan($(this).text())) {
            $(this).addClass('bulan');
            var _pertanggal = $(this).siblings('ul');
            _pertanggal.find('li').each(function() {
                if ($(this).closest('div').attr('id') == 'div_forecast') {
                    Forecast.list_kebutuhan_pakan_pertanggal_bdy($(this).find(':checkbox:first'));
                }

                var _tot = 0;
                var _label = $(this).find('label');
                var _perkandang = $(this).find('ul>li>a');
                var _populasi = '';
                _perkandang.each(function() {
                    _populasi = parse_number(Forecast.getPopulasiKandang($(this).text()));
                    _tot += _populasi;
                });
                _label.text(_label.text() + ' ( ' + number_format(_tot, 0, ',', '.') + ' ekor)');
            });
        }
    });

    /* sebenarnya bukan context menu, hanya untuk melihat kebutuhan pakan */
    Forecast.add_contextmenu_kandang_bdy(_elmDrag);
    _elmDrag.click(function() {
        var _t_noreg = AktivasiKandang.parseElmKandang($(this));
        var _t_idfarm = Config.mappingHeader($(this).find('span[data-value=detail_kandang]').text().split('/'));
        var _tgldocin = Config._convertTgl([_t_noreg.tahun, Config._indexBulan(_t_noreg.bulan), _t_noreg.tanggal].join('-'));
        Forecast.detail_perkandang_bdy('#info_detail_kandang', _t_idfarm['kode_farm'], _tgldocin, _t_idfarm['kandang']);
    });
    _elmDrop.click(function() {
        var _t_noreg = AktivasiKandang.parseElmKandang($(this).find('li:first'));
        var _t_idfarm = Config.mappingHeader($(this).find('span[data-value=detail_kandang]:first').text().split('/'));
        var _tgldocin = Config._convertTgl([_t_noreg.tahun, Config._indexBulan(_t_noreg.bulan), _t_noreg.tanggal].join('-'));
        Forecast.detail_perkandang_bdy('#info_detail_kandang', _t_idfarm['kode_farm'], _tgldocin);
    });

    if (lockEditDocIn == 0) {
        _elmDrop.children('input').each(function() {
            Forecast.list_kebutuhan_pakan_pertanggal_bdy($(this));
        });
        var _tmpKandang = $('#div_kandang_pending ul>li>a').closest('li');
        var _tmpTanggal = _tmpKandang.closest('ul').closest('li');
        /* mengubah tanggal doc in oleh kadiv */
        Forecast.add_contextmenu_tanggal(_tmpTanggal.find('label:first'));

        _tmpKandang.click(function() {
            var _t_noreg = AktivasiKandang.parseElmKandang($(this));
            var _t_idfarm = Config.mappingHeader($(this).find('span[data-value=detail_kandang]').text().split('/'));
            var _tgldocin = Config._convertTgl([_t_noreg.tahun, Config._indexBulan(_t_noreg.bulan), _t_noreg.tanggal].join('-'));
            Forecast.detail_perkandang_bdy('#div_kandang_konfirmasi', _t_idfarm['kode_farm'], _tgldocin, _t_idfarm['kandang']);
        });
        _tmpTanggal.click(function() {
            var _t_noreg = AktivasiKandang.parseElmKandang($(this).find('li:first'));
            var _t_idfarm = Config.mappingHeader($(this).find('span[data-value=detail_kandang]:first').text().split('/'));
            var _tgldocin = Config._convertTgl([_t_noreg.tahun, Config._indexBulan(_t_noreg.bulan), _t_noreg.tanggal].join('-'));
            Forecast.detail_perkandang_bdy('#div_kandang_konfirmasi', _t_idfarm['kode_farm'], _tgldocin);
        });

    }

    Forecast.reset();
    /* untuk budidaya farm yang ditampilkan tidak memperhatikan status aktif atau tidak */
    Forecast.init(0);
    if (!empty(_minTglDocInStandartBaru)) {
        Forecast.periksaStandardBaru(_minTglDocInStandartBaru);
    }
    var _bisaKonfirmasi = $('#AktivasiSiklus').data('bisa_konfirmasi');
    AktivasiKandang.setBisaKonfirmasi(_bisaKonfirmasi);
    /* untuk li level 1 maka tambahkan class nama_farm */
    $('.css-treeview>ul>li').addClass('nama_farm');

    /** tambahkan context menu untuk ubah flok kandang, yang bisa ubah hanya kabagadmin */
    if (AktivasiKandang.getBisaKonfirmasi()) {
        $('#div_kandang_pending ul>li>a').each(function() {
            Forecast.add_contextmenu_gantiflock($(this));
        });
    }

}());