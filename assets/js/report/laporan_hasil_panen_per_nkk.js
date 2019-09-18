var hpp_nkk = {
	tampilkan_data : function(){
		var farm 	= $('#select_farm').val();
		var periode	= $('#select_periode').val();
		var kandang	= $('#select_kandang').val();
		$.ajax({
			url : 'report/Laporan_hasil_panen_per_nkk/get_data_table',
			type : 'POST',
			data : {
				farm_post		: farm,
				periode_post	: periode,
				kandang_post	: kandang,
			},
			dataType : 'html',
			success : function(data){
				$('#data_table').html(data);
			}
		});
	},
	
	set_list_periode : function(){
		$('#select_periode').empty();
		$('#select_kandang').empty();
		$farm = $('#select_farm').val();
		$.ajax({
			url : 'report/Laporan_hasil_panen_per_nkk/get_list_periode',
			type : 'POST',
			data : {kodefarm : $farm},
			dataType : 'JSON',
			success : function(data){
				for(var i=0;i<data.length;i++){
					$('#select_periode').append($('<option>', {
                        value: data[i].PERIODE_SIKLUS,
                        text: data[i].PERIODE_SIKLUS
                    }));
				}
				if(data.length > 0){
					hpp_nkk.set_list_kandang();
				}
			}
		});
	},
	
	set_list_kandang : function(){
		$('#select_kandang').empty();
		$farm = $('#select_farm').val();
		$.ajax({
			url : 'report/Laporan_hasil_panen_per_nkk/get_kandang_farm',
			type : 'POST',
			data : {kodefarm : $farm},
			dataType : 'JSON',
			success : function(data){
				for(var i=0;i<data.length;i++){
					$('#select_kandang').append($('<option>', {
                        value: data[i].KODE_KANDANG,
                        text: data[i].NAMA_KANDANG
                    }));
				}
			}
		});
	},
	
};