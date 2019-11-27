'use strict';

var plottingKendaraan = {
	_kodeFarm : null,
	_sopirTags : new Array(),

	highlight : function(elm){
		jQuery.each($('tbody tr'), function(k, v) {
			var checked = $(this).find("input:checkbox").is(':checked');
			if (checked)
				$(this).css('background-color','#FFFAFA');
			else
				$(this).css('background-color','');
		});
	},
	
	addDtlSopir : function(elm){
		$(elm).parent().closest('td').find('.glyphicon-minus').removeClass('hide');
		$(elm).parent().closest('td').find('.glyphicon-plus').addClass('hide');
		var _row =  $(elm).parent().closest('td').parent('tr').clone().find("input").val("").end();
			_row.find("input").val("");
			_row.find('.glyphicon-minus').removeClass('hide');
			_row.find('.glyphicon-plus').removeClass('hide');
		$($(elm).parent().closest('td').parent('tr')).after(_row);
		$('input[name=telp_sopir]').numeric({
			allowPlus : false, // Allow the + sign
			allowMinus : false, // Allow the - sign
			allowThouSep : false, // Allow the thousands separator, default is the
			// comma eg 12,000
			allowDecSep : false
			// Allow the decimal separator, default is the fullstop eg 3.141
		});
		$('input[name=nama_sopir]').alpha();
		$('input[name=nomor_kendaraan]').alphanum({
			allowSpace: false, // Allow the space character
		});
		plottingKendaraan.setInputSopir();
	},
	
	removeDtlSopir : function(elm){
		var tr = $(elm).closest('tr');
		var so = tr.attr('data-nomor_so');
		var countSo = $(tr).closest('tbody').find('tr[data-nomor_so='+ so +']').length;
		if (countSo > 1) {
			if (!($(elm).prev().is(".hide")))
				$(elm).parent().closest('td').parent('tr').prev().find('td > div > span.glyphicon-plus').removeClass('hide');
			$(elm).parent().closest('td').parent('tr').remove();
		}
		if (countSo == 2) {
			$('tbody').find('tr[data-nomor_so='+ so +']').find('.glyphicon-minus').addClass('hide');
		}
	},
	
	upperCaseWord : function(elm){
		$(elm).val(function () {
			return this.value.toUpperCase();
		});
	},
	
	clickAll : function(elm){
		var checked = $(elm).is(':checked');
		jQuery.each($('tbody tr'), function(k, v) {
			$(this).find("input:checkbox").prop("checked", checked);
			if (checked)
				$(this).css('background-color','#FFFAFA');
			else
				$(this).css('background-color','');
		});
		plottingKendaraan.enableSimpan(elm);
	},
	
	enableSimpan : function(elm){
		if ($('tbody > tr > td > input:checkbox:checked').length) {
			$('.div_btn > button').prop("disabled", false);
		} else {
			$('.div_btn > button').prop("disabled", true);
		}
	},
	
	searchSO : function(elm){
		$.ajax({
			url : 'sales_order/plotting_kendaraan/searchSO',
			data : {
				nomor_so : $('input[name=nomorSO]').val(),
				status_so : $('input[name=statusSO]').is(":checked"),
			},
			dataType : 'html',
			type:'POST',
			beforeSend : function(){
				$('#divtbl_plotting_kendaraan').html('Loading ......');
			},
			success : function(data){
				$('#divtbl_plotting_kendaraan').html(data);
				plottingKendaraan.setInputSopir();
			}
		});
	},
	
	setInputSopir : function(){
		$.ajax({
			type:'POST',
			dataType: 'json',
			url : 'sales_order/plotting_kendaraan/getSopir'
		})
		.done(function(data){
		
			$('input[name=nama_sopir]').each(function() {
				var $input = $(this);
				$input.typeahead({source:data,
						autoSelect: true});
				
				$input.change(function() {
					var current = $input.typeahead("getActive");
					if (current) {
						// Some item from your model is active!
						if (current.name == $input.val()) {
							$(this).val(current.nama_sopir);
							var tr = $(this).closest('tr');
							$(tr).find('.telp_sopir>input').val(current.no_telp_sopir);
						}
					}
				});
			});
			
		});
		return true;
	},
	
	save : function(){
		var dtl_plotting_kendaraan = [];
		var item = {};
		$('tbody#main_tbody > tr > td > input:checkbox:checked').each(function() {
			item = {
				'nomor_so' : $(this).closest('tr').data('nomor_so'),
				'nomor_do' : $(this).closest('tr').data('nomor_do'),
				'kode_farm' : $(this).closest('tr').find('td.farm_asal').data('kode_farm'),
				'nama_farm' : $(this).closest('tr').find('td.farm_asal').text(),
				'kode_pelanggan' : $(this).closest('tr').find('td.nama_pelgn').data('kode_pelanggan'),
				'nama_pelanggan' : $(this).closest('tr').find('td.nama_pelgn').text(),
				'alamat_pelanggan' : $(this).closest('tr').find('td.almt_pelgn').text(),
				'kota_pelanggan' : $(this).closest('tr').find('td.almt_pelgn').data('kota_pelanggan'),
				'telp_pelanggan' : $(this).closest('tr').find('td.telp_pelgn').text(),
				'nomor_kendaraan' : $(this).closest('tr').find('td.nomor_kendaraan > input').val(),
				'nama_sopir' : $(this).closest('tr').find('td.nama_sopir > input').val(),
				'telp_sopir' : $(this).closest('tr').find('td.telp_sopir > input').val(),
			};
			dtl_plotting_kendaraan.push(item);
		});
		
		var data_params = {
			'dtl_plotting_kendaraan' 	: dtl_plotting_kendaraan,
		}
		
		plottingKendaraan.validationSave(data_params, function(valid) {
			if(valid == 1){
				plottingKendaraan.executeSave(data_params, function(result){
					var obj = result;
					if (obj.status == 1) {
						//window.location.href = "pembelian/permintaan_barang/detail/" + result.content;
						bootbox.dialog({
							title : "Penyimpanan Berhasil",
							message : "Data Plotting Kendaraan berhasil Disimpan",
							buttons: {
								confirm: {
									label: 'OK',
									callback : function() {
										// location.reload(true);
										plottingKendaraan.searchSO('');
									}
								},
							},
						}, function() {
							$('.bootbox').modal('hide');
						});
					} else {
						bootbox.dialog({
							message : "Simpan gagal."
						}, function() {
							$('.bootbox').modal('hide');
						});
					}
				});
			}else{
			}
		});
		
  	},
	validationSave : function(data_params, callback) {
		var result=1;
		var count=0;
		var table_html = '';
		table_html += '<div class="row"><div class="form-group col-sm-12"></div></div>';
		table_html += '<div>';
		table_html += '<table class="table table-bordered custom_table">';
		table_html += '<thead>';
		table_html += '<tr>';
		table_html += '<th class="col-sm-3">No. DO</th>';
		table_html += '<th class="col-sm-3">No. Kendaraan</th>';
		table_html += '<th class="col-sm-3">Sopir</th>';
		table_html += '<th class="col-sm-3">No. Telp</th>';
		table_html += '</tr>';
		table_html += '</thead>';
		table_html += '<tbody>';
		
		$.each(data_params.dtl_plotting_kendaraan, function(index, value) {
			table_html += '<tr>';
			table_html += '<td>';
			table_html += value.nomor_do;
			table_html += '</td>';
			table_html += '<td>';
			table_html += value.nomor_kendaraan;
			table_html += '</td>';
			table_html += '<td>';
			table_html += value.nama_sopir;
			table_html += '</td>';
			table_html += '<td>';
			table_html += value.telp_sopir;
			table_html += '</td>';
			table_html += '</tr>';
		});
		table_html += '</tbody>';
		table_html += '</table>';
		table_html += '</div>';
		
		$('tbody#main_tbody > tr > td > input[data-mandatory=1]').each(function() {
			$(this).parent().closest('td').removeClass("has-error");
		});
		
		$('tbody#main_tbody > tr > td > input:checkbox:checked').each(function() {
			$(this).closest('tr').find('td > input[data-mandatory=1]').each(function() {
				if (empty($.trim($(this).val()))) {
					$(this).parent().closest('td').addClass("has-error");
					result = 0;
					count +=1;
				}
			});
		});
		
		if (result==1) {
			var message = 'Apakah anda yakin menyimpan plotting DO dengan data kendaraan dibawah ini?';
			var _options = {
				title : 'Konfirmasi Plotting Kendaraan',
				className : 'titleCenter',
				message : message+table_html,
				buttons: {
					cancel: {
						label: 'Tidak',
						callback : function() {
						}
					},
					confirm: {
						label: 'Ya',
						callback : function() {
							box.bind('hidden.bs.modal', function() {
								callback(result);
							});
						}
					},
				},
			};
			var box = bootbox.dialog(_options);
		} else {
			bootbox.dialog({
				title : "Penyimpanan Gagal",
				message : "Mohon melengkapi data entri terlebih dahulu",
				buttons: {
					confirm: {
						label: 'OK',
						callback : function() {
						}
					},
				},
			}, function() {
				$('.bootbox').modal('hide');
			});
		}
	},
	executeSave : function(data_params, callback){
		var formData = new FormData();
        formData.append("data_params", JSON.stringify(data_params));
		$.ajax({
            url 	: 'sales_order/plotting_kendaraan/save',
            data 	: formData,
            type 	: 'post',
            dataType : 'json',
            async 	: false,
            cache 	: false,
            contentType : false,
            processData : false,
            beforeSend : function() {
                bootbox.dialog({
                    message : "Sedang proses simpan..."
                });
            },
            success : function(data) {
                $('.bootbox').modal('hide');
				callback(data);
            }
        });

	},
};

$(function(){
	plottingKendaraan.setInputSopir();
	$('input[name=telp_sopir]').numeric({
        allowPlus : false, // Allow the + sign
        allowMinus : false, // Allow the - sign
        allowThouSep : false, // Allow the thousands separator, default is the
        // comma eg 12,000
        allowDecSep : false
		// Allow the decimal separator, default is the fullstop eg 3.141
    });
	$('input[name=nama_sopir]').alpha();
	$('input[name=nomor_kendaraan]').alphanum({
		allowSpace: false, // Allow the space character
	});
})
