<!--div><?php print_r($detail_kandang) ?></div-->
<div class="panel panel-default">
	<div class="panel-heading">Penimbangan Pakan</div>
	<div class="panel-body">
		<div class="col-md-12">
			<button onclick="visualisasi_kavling()" type="button" class="btn btn-default">Visualisasi Kavling</button>
		</div>
		<div class="col-md-12 new-line">
			<table id="tbl-detail-penerimaan" class="table table-bordered">
				<thead>
					<tr>
						<th class="col-md-1">Kode Pakan</th>
						<th class="col-md-2">Nama Pakan</th>
						<th class="col-md-2">Bentuk Pakan</th>
						<th class="col-md-1">Jumlah SJ</th>
						<th class="col-md-1">Sak Terima</th>
						<th class="col-md-1">Sak Tolak</th>
						<th class="col-md-1">Sak Hilang</th>
					</tr>
				</thead>
				<tbody>
					<?php $data_ke = 1; ?>
					<?php foreach ($data_penimbangan as $key => $value) { ?>
					<?php $nama_pakan = $value['nama_pakan']; ?>
					<tr onclick="view_detail_penimbangan_pakan(this)" data-sisa="1" data-ke="<?php echo $data_ke; ?>" class="tr-header">
						<td class="kode-pakan">
							<!--a data-original-title="Terdapat selisih sejumlah 1 sak dari jumlah sak menurut SJ." href="#" data-toggle="tooltip" data-placement="top" title="" class="seru">!</a-->
                            <span><?php echo $key; ?></span>
                        </td>
                        <td class="nama-pakan"><?php echo $nama_pakan; ?></td>
						<td class="bentuk-pakan"><?php echo $value['bentuk_pakan']; ?></td>
						<td class="jumlah-sj"><?php echo $value['jml_sj']; ?></td>
						<td class="jumlah-terima"><?php echo $value['jml_terima']; ?></td>
						<td class="jumlah-rusak"><?php echo $value['jml_tolak']; ?></td>
						<td class="jumlah-kurang"><?php echo $value['jml_hilang']; ?></td>
					</tr>
					<tr class="tr-detail hide" data-ke="<?php echo $data_ke; ?>">
						<td colspan="7">
							<div class="div-detail-pakan">
								<table class="table table-bordered tbl-detail-pakan">
									<thead>
										<tr>
											<th class="arrow"></th>
											<th class="no-pallet">No</th>
											<th class="no-kavling">Kavling-Pallet</th>
											<th class="berat-pallet">Berat Pallet (Kg)</th>
											<th class="berat-timbang">Berat Timbang (kg)</th>
											<th class="berat-bersih">Berat Bersih (kg)</th>
											<th class="timbangan-sak">Timbangan (Sak)</th>
											<th class="selesai">Scan Barcode Pallet</th>
										</tr>
									</thead>
									<tbody>
										<?php $data_ke_pallet = 1; ?>
										<?php $baris = 1; ?>
										<?php foreach ($value['detail'] as $key1 => $value1) { ?>
										<tr class="tr-detail-pakan" data-ke="<?php echo $data_ke_pallet; ?>">
											<td class="arrow">
												<span style="transform: rotate(180deg);" class="glyphicon glyphicon glyphicon-play"></span>
											</td>
											<td class="no-pallet" data-no-pallet="<?php echo $value1['no_pallet']; ?>"><?php echo $baris; ?>.</td>
											<td class="no-kavling"><?php echo $value1['kode_pallet']; ?></td>
											<td class="berat-pallet"><?php echo $value1['berat_pallet']; ?></td>
											<td class="berat-timbang"><?php echo $value1['berat_timbang']; ?></td>
											<td class="berat-bersih"><?php echo $value1['berat_terima']; ?></td>
											<td class="timbangan-sak"><?php echo $value1['timbangan_sak']; ?></td>
											<td data-selesai="<?php echo $value1['selesai']; ?>" class="selesai"><input type="checkbox" checked disabled /></td>
										</tr>
										<tr class="tr-sub-detail-pakan hide" data-ke="<?php echo $data_ke_pallet; ?>">
											<td colspan="8">
												<center>
													<table class="table table-bordered tbl-detail-kandang">
														<thead>
															<tr>
																<th class="checkbox-kandang"></th>
																<th class="nama-kandang">Kandang</th>
																<th class="jml-kebutuhan">Jml Kebutuhan (Sak)</th>
																<th class="jml-aktual">Jml Aktual (Sak)</th>
																<th class="berat">Berat (Kg)</th>
																<th class="sisa">Sisa</th>
															</tr>
														</thead>
														<tbody>
															<?php $data_ke_kandang = 1; ?>
															<?php foreach ($value1['detail'] as $key2 => $value2) { ?>
															<tr class="tr-detail-kandang" data-ke="<?php echo $data_ke_kandang; ?>">
																<td class="checkbox-kandang">
																	<label><input type="checkbox" disabled="true" checked="true" class="checkbox-kandang" onclick="checkbox_kandang(this)"></label>
																</td>
																<td data-no-reg="BT1/2015-1/01" class="nama-kandang"><?php echo $value2['nama_kandang']; ?></td>
																<td class="jml-kebutuhan"><?php echo $detail_kandang[$key][$value2['nama_kandang']]['jml_kebutuhan']; ?></td>
																<td class="jml-aktual"><?php echo $value2['jml_aktual']; ?></td>
																<td class="berat"><?php echo $value2['berat_aktual']; ?></td>
																<td class="sisa"><?php echo $detail_kandang[$key][$value2['nama_kandang']]['jml_kebutuhan'] - $value2['jml_aktual']; ?></td>
																<?php
																	$detail_kandang[$key][$value2['nama_kandang']]['jml_kebutuhan'] = $detail_kandang[$key][$value2['nama_kandang']]['jml_kebutuhan'] - $value2['jml_aktual'];
																?>
															</tr>
															<?php $data_ke_kandang++; ?>

															<?php } ?>
														</tbody>
													</table>
												</center>
											</td>
										</tr>

										<?php $data_ke_pallet++; ?>
										<?php $baris++; ?>

										<?php } ?>
									</tbody>
								</table>
							</div>
						</td>
					</tr>

					<?php $data_ke++; ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
    var tmp_data_pakan_rusak_hilang = '<?php echo $data_pakan_rusak_hilang; ?>'; 

    function susun_data_pakan_rusak_hilang(){
    	if(tmp_data_pakan_rusak_hilang.length > 0){
    		var lampirkan_file = '';
	    	tmp_data_pakan_rusak_hilang = JSON.parse(tmp_data_pakan_rusak_hilang);
	    	//console.log(tmp_data_pakan_rusak_hilang);
    		$.each(tmp_data_pakan_rusak_hilang, function(key0, value0){
	    		var detail_pakan_rusak = [];
	    		var detail_pakan_hilang = {};
	    		var sisa = 0;
	    		$.each(value0, function(key1, value1){
	    			if(value1['keterangan_rusak']){
	    				lampirkan_file = value1['attachment_name'];
		    			detail_pakan_rusak.push({
		                    'berat' : value1['berat_rusak'],
		                    'keterangan' : value1['keterangan_rusak']
		                });
		    		}
	    			if(value1['keterangan_kurang']){
				        detail_pakan_hilang = {
				            'jumlah' : value1['jml_kurang'],
				            'keterangan' : value1['keterangan_kurang']
				        };
		    		}
	        	});	
	        	sisa = parseInt(value0[0]['jml_rusak']) + parseInt(value0[0]['jml_kurang']);
	        	set_pakan_rusak_hilang(key0, sisa);
				set_detail_pakan_rusak_hilang(key0,detail_pakan_rusak,detail_pakan_hilang, lampirkan_file);
	    		
    		});
    	}
    /*


                jumlah_keterangan++;
                detail_pakan_rusak.push({
                    'berat' : berat_rusak,
                    'keterangan' : keterangan
                });
        detail_pakan_hilang = {
            'jumlah' : jumlah_hilang,
            'keterangan' : keterangan_hilang
        };
        */

    }
</script>
