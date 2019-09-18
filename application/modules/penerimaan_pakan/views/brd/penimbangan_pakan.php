
<div class="panel panel-default">
	<div class="panel-heading">Penimbangan Pakan</div>
	<div class="panel-body">
		<div class="col-md-12">
			<button class="btn btn-default" type="button"
				onclick='visualisasi_kavling()'>Visualisasi Kavling</button>
		</div>
		<div class="col-md-12 new-line">
			<table class="table table-bordered" id="tbl-detail-penerimaan">
				<thead>
					<tr>
						<th class="col-md-1">Kode Pakan</th>
						<th class="col-md-2">Nama Pakan</th>
						<th class="col-md-2">Bentuk</th>
						<th class="col-md-1">Jumlah SJ (sak)</th>
						<th class="col-md-1">Sak Terima</th>
						<th class="col-md-1">Sak Tolak</th>
						<th class="col-md-1">Sak Hilang</th>
					</tr>
				</thead>
				<tbody>
					<!--pre><?php //print_r($penimbangan_pakan); ?></pre-->
                <?php $header = 1; ?>
                <?php foreach ($penimbangan_pakan as $key => $value) { ?>
                    <tr class="tr-header"
						data-ke="<?php echo $header; ?>"
						ondblclick="detail_penimbangan_pakan(this)">
						<td class='kode-pakan'><?php if($value['jml_sj'] != ($value['jml_terima']+$value['jml_tolak']+$value['jml_hilang'])){ ?>
						    <a class="seru" title="" data-placement="top" data-toggle="tooltip" href="#" data-original-title="Terdapat selisih sejumlah <?php echo abs(($value['jml_sj']) - ($value['jml_terima']+$value['jml_tolak']+$value['jml_hilang']));?> sak dari jumlah sak menurut SJ">!</a>
                            <?php } ?><span><?php echo $value['kode_pakan']; ?></span></td>
                        <td class='nama-pakan'><?php echo $value['nama_pakan']; ?></td>
						<td><?php echo $value['bentuk_pakan']; ?></td>
						<td class='jumlah-sj'><?php echo ($value['jml_sj'] == 0) ? '-' : $value['jml_sj']; ?></td>
						<td class='jumlah-terima'><?php echo ($value['jml_terima'] == 0) ? '-' : $value['jml_terima']; ?></td>
						<td class='jumlah-rusak'><?php echo ($value['jml_tolak'] == 0) ? '-' : $value['jml_tolak']; ?></td>
						<td class='jumlah-kurang'><?php echo ($value['jml_hilang'] == 0) ? '-' : $value['jml_hilang']; ?></td>
					</tr>
					<tr class="hide tr-detail" data-ke="<?php echo $header; ?>">
						<td colspan="7">
							<table class="table table-bordered tbl-sub-detail-penerimaan">
								<thead>
									<tr>
										<th class="col-md-2">Kandang</th>
										<th class="col-md-1">Jenis Kelamin</th>
										<th class="col-md-1">Jumlah Seharusnya (sak)</th>
										<th class="col-md-1">Timbangan (kg)</th>
										<th class="col-md-1">Timbangan (sak)</th>
										<th class="col-md-3">Keterangan</th>
										<th class="col-md-1">Kavling</th>
										<th class="col-md-1">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
                                    <?php $detail = 1; ?>
                                    <?php foreach ($value['detail'] as $key => $value) { ?>
                                    <tr class="tr-sub-detail"
										data-ke="<?php echo $detail; ?>">
										<td class="kandang"
											data-no-reg="<?php echo $value['no_reg']; ?>"
											data-jenis-kelamin="<?php echo $value['jenis_kelamin']; ?>"><?php echo $value['nama_kandang']; ?></td>
										<td class="jenis-kelamin"><?php echo ($value['jenis_kelamin'] == 'J') ? 'JANTAN' : 'BETINA'; ?></td>
										<td class="jml-seharusnya"><?php echo $value['jml_seharusnya']; ?></td>
										<td class="timbangan-kg"><input type="text"
											onkeyup="number_only(this)"
											onchange="kontrol_timbangan(this)"
											placeholder="Timbangan (kg)" name="timbangan-kg"
											class="form-control timbangan_kg"
											value="<?php echo ($value['timbangan_kg'] == 0 && $value['selesai'] == 0) ? '' : $value['timbangan_kg']; ?>"
											<?php echo ($value['timbangan_kg'] == 0 && $value['selesai'] == 0) ? '' : 'readonly'; ?>>
										</td>
										<td class="timbangan-sak"><?php echo ($value['timbangan_sak'] == 0 && $value['selesai'] == 0) ? '-' : $value['timbangan_sak']; ?></td>
										<td class="keterangan">-</td>
										<td class="kavling"><?php echo $value['kavling']; ?></td>
										<td class="selesai"
											data-selesai="<?php echo $value['selesai']; ?>"><?php echo $value['selesai'] == 0 ? '<button class="btn btn-default" type="button" onclick="selesai(this)" ondblclick="not_actived(this)" disabled>Selesai</button>' : 'Selesai'; ?></td>
									</tr>
                                    <?php $detail++; ?>
                                    <?php } ?>
                                </tbody>
							</table>
						</td>
					</tr>
                    <?php $header++; ?>
                <?php } ?>
            </tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	( function() {
	    $('a.seru').tooltip();

	}())
</script>