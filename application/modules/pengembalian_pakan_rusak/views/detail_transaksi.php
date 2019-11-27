<table data-no_reg=<?php echo $no_reg ?> id="tabel_detail_pengembalian_pakan_rusak" class="table table-bordered">
	<thead>
		<tr>
			<th>Nama Pakan</th>
			<th>Jumlah Retur</th>
			<th>Jumlah Stok Akhir</th>
		</tr>
	</thead>
	<tbody>

		<?php
		if(!empty($list_pakan)){
		/* buat dropdown list pakan */
		$o = array();
		foreach($list_pakan as $pakan){
			$kode_barang = $pakan['kode_barang'];
			$bentuk_barang = $pakan['bentuk_barang'];
			$nama_barang = $pakan['nama_barang'];
			$jk = $pakan['jenis_kelamin'];
			$retur_stok = isset($retur_pakan[$kode_barang][$jk]) ? $retur_pakan[$kode_barang][$jk] : 0;
			$stok = $pakan['jml_stok'];
			$x = '<option data-retur="'.$retur_stok.'" data-stok="'.$stok.'" data-kode_barang="'.$kode_barang.'" data-jenis_kelamin="'.$jk.'" value="'.$kode_barang.'/'.$jk.'">'.$nama_barang. '-' .convertKode('bentuk_barang',$bentuk_barang).' - ('.$jk.')</option>';
			array_push($o,$x);
		}
			$s = '<select name="kode_barang" onchange="Pengembalianpakan.show_detail_timbang(this)"><option value="">Pilih Nama Pakan</option>'.implode(' ', $o).'</select>';

			echo '<tr class="tr_header">
					<td>'.$s.'</td>
					<td class="jml_retur">0</td>
					<td class="jml_stok"></td>
			</tr>
			<tr class="detail_timbang" style="display:none">';

			echo '<td colspan="8">
					<table class="table pull-right" style="max-width:50%">
						<thead>
							<tr>
								<th>Timbangan (Kg) </th>
								<th>Keterangan </th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									<input type="text" class="required number" data-field="Berat Timbangan" value=0 onclick="this.select()" name="brt_sak" readonly onpaste="Pengembalianpakan.get_berat_timbang(this)" />
								</td>
								<td>
									<input type="text" class="required" data-field="Keterangan" name="keterangan" />
								</td>
								<td>
									<span class="btn btn-default" onclick="Pengembalianpakan.timbang_lagi(this)">Selesai</span>
								</td>
							</tr>
						</tbody>
					</table>
				</td>';

			echo '</tr>';
		}
		else{
			echo '<tr>
					<td colspan="4">Data tidak ditemukan</td>
				</tr>';
		}
		 ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan = "8">
				<div class="col-md-12">
					<div class="col-md-12 ">
						<div class="form-group form-horizontal">
					        <div class="form-inline new-line">
					            <label class="col-md-2" for="tanggal-kirim">Lampirkan Foto</label>
					            <div class="form-group">
					                <div class="input-group">
					                    <input type="text" class="form-control" id="lampirkan-foto" name="lampirkan-foto" value="<?php echo empty($value['attachment_name']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : $value['attachment_name']; ?>" readonly>
										 <span class="btn btn-default btn-file input-group-addon">
					                     	<b>...</b> <input type="file" id="file-upload" <?php echo empty($value['attachment_name']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'disabled'; ?>>
					                     </span>
					                </div>
					            </div>
					            <div class="col-md-offset-2">
					            <span class="help-block abang">* wajib diisi</span>
					            </div>
					        </div>
			        		<div id='format-file'></div>
			        	</div>
					</div>
				</div>
		    </td>
		</tr>
	</tfoot>
</table>
