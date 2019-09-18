
<div class="panel panel-default">
	<div class="panel-heading">Penimbangan Pakan</div>
	<div class="panel-body">
		<div class="col-md-12">
			<button class="btn btn-default" type="button"
				onclick='visualisasi_kavling()'>Visualisasi Kavling</button>
            <button class="btn btn-default btn-resave hide" type="button"
                onclick='simpan()'>Simpan</button>
		</div>
		<div class="col-md-12 new-line">
			<!--pre><?php print_r($penimbangan_pakan); ?></pre-->
			<table class="table table-bordered" id="tbl-detail-penerimaan">
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
                <?php $header = 1; ?>
                <?php foreach ($penimbangan_pakan as $key => $value) { ?>
                    <tr class="tr-header"
						data-ke="<?php echo $header; ?>"
						data-sisa="1"
						onclick="view_detail_penimbangan_pakan(this)"
						ondblclick="detail_penimbangan_pakan(this)">
						<td class='kode-pakan'>
							<?php ?>
						    <a class="seru" title="" data-placement="top" data-toggle="tooltip" href="#" data-original-title="Terdapat selisih sejumlah x sak dari jumlah sak menurut SJ.">!</a>
                            <?php ?>
                            <span><?php echo $value['kode_pakan']; ?></span>
                        </td>
                        <td class='nama-pakan'><?php echo $value['nama_pakan']; ?></td>
						<td class='bentuk-pakan'><?php echo $value['bentuk_pakan']; ?></td>
						<td class='jumlah-sj'><?php echo ($value['jml_sj'] == 0) ? '-' : $value['jml_sj']; ?></td>
						<td class='jumlah-terima'></td>
						<td class='jumlah-rusak'></td>
						<td class='jumlah-kurang'></td>
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
	    detail_kandang = <?php echo $sub_detail_penimbangan_pakan; ?>;
	    berat_standart = <?php echo $berat_standart; ?>;
	    //console.log(detail_kandang);
	    
	}())
</script>