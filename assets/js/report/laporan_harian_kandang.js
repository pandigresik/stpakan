var lhk = {
	select_farm : '',
	select_periode : '',
	select_kandang : '',
	
	set_list_periode : function(){
		$('#select_periode').empty();
		$('#select_kandang').empty();
		$farm = $('#select_farm').val();
		$.ajax({
			url : 'report/laporan_harian_kandang/get_list_periode',
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
					lhk.set_list_kandang();
				}
			}
		});
	},
	
	set_list_kandang : function(){
		$('#select_kandang').empty();
		$farm = $('#select_farm').val();
		$.ajax({
			url : 'report/laporan_harian_kandang/get_kandang_farm',
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
	
	tampilkan_data : function(){
		select_farm 	= $('#select_farm').val();
		select_periode	= $('#select_periode').val();
		select_kandang	= $('#select_kandang').val();
		$.ajax({
			url : 'report/laporan_harian_kandang/get_data_table',
			type : 'POST',
			data : {
				farm_post		: select_farm,
				periode_post	: select_periode,
				kandang_post	: select_kandang,
			},
			dataType : 'html',
			success : function(data){
				$('#laporan_layout').html(data);
			}
		});
	},
	
};