<div class="panel panel-default">
	<div class="panel-heading">Penimbangan Pakan</div>
	<div class="panel-body">
		<div class="col-md-12 text-center">
			<h2><?php echo $nama_pakan; ?></h2>
			<h2 id="kode-pakan-rusak" class="hide"><?php echo $kode_pakan; ?></h2>
		</div>
    <div class="div-panel-pakan-rusak col-md-12">
    </div>
		<div class="col-md-12 new-line">
			<div class="col-md-7">
				<div class="panel panel-default">
					<div class="panel-heading">Pakan Rusak</div>
					<div class="panel-body">
						<table class="tabel_input_rusak table table-bordered"
							data-sisa="<?php echo $sisa; ?>">
							<thead>
								<tr>
									<th class="">No.</th>
									<th class="col-md-3">Berat (Kg)</th>
									<th class="col-md-8">Keterangan</th>
									<th class=""></th>
								</tr>
							</thead>
							<tbody>
                <?php $data_ke = 1; ?>
                <!--pre><?php #print_r($pakan_rusak_hilang); ?></pre-->
                <?php foreach ($pakan_rusak_hilang as $key => $value) { ?>
								<tr class="row-timbang" data-ke="<?php echo $data_ke; ?>">
									<td><?php echo $data_ke; ?>.</td>
									<td><input type="text"
										value="<?php echo empty($value['berat_putaway']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : $value['berat_putaway']; ?>"
										placeholder="Berat" name="berat-rusak"
										onkeyup="number_only(this)"
										onchange="kontrol_berat_rusak(this)"
										class="form-control berat-rusak"
										<?php echo empty($value['berat_putaway']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'readonly'; ?>></td>
									<td><input type="text" placeholder="Keterangan"
										name="keterangan-rusak"
										value="<?php echo empty($value['keterangan_rusak']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : $value['keterangan_rusak']; ?>"
										class="form-control keterangan-rusak"
										<?php echo empty($value['keterangan_rusak']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'readonly'; ?>></td>
									<td>
										<div onclick="tambah_timbang_rusak(this)" class="<?php echo empty($value['berat_putaway']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'hide'; ?>">

											<span class="glyphicon glyphicon-plus"></span>
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
			<div class="col-md-5">
				<div class="panel panel-default">
					<div class="panel-heading">Pakan Hilang</div>
					<div class="panel-body panel-pakan-hilang">
						<div class="form-horizontal">
							<div class="form-group">
								<label for="label" class="col-md-5 control-label text-left">Jumlah
									(sak)</label>

								<div class="col-md-6">
									<input type="text" class="form-control validasi jumlah-sak"
										name="jumlah-sak" onchange="" placeholder="Jumlah (sak)"
										onkeyup="number_only(this)" value="<?php echo (!empty($pakan_rusak_hilang[0]['jml_kurang']) && !empty($pakan_rusak_hilang[0]['keterangan_kurang']) && !empty($pakan_rusak_hilang[0]['no_ba'])) ? $pakan_rusak_hilang[0]['jml_kurang'] : ''; ?>" 
                    <?php echo (!empty($pakan_rusak_hilang[0]['jml_kurang']) && !empty($pakan_rusak_hilang[0]['keterangan_kurang']) && !empty($pakan_rusak_hilang[0]['no_ba'])) ? 'readonly' : ''; ?>>
								</div>

							</div>
							<div class="form-group">
								<label for="label" class="col-md-5 control-label text-left">Keterangan</label>

								<div class="col-md-6">
									<input type="text" class="form-control validasi keterangan"
										name="keterangan" onchange="" placeholder="Keterangan"
										onkeyup="" value="<?php echo (!empty($pakan_rusak_hilang[0]['jml_kurang']) && !empty($pakan_rusak_hilang[0]['keterangan_kurang']) && !empty($pakan_rusak_hilang[0]['no_ba'])) ? $pakan_rusak_hilang[0]['keterangan_kurang'] : ''; ?>" 
                    <?php echo (!empty($pakan_rusak_hilang[0]['jml_kurang']) && !empty($pakan_rusak_hilang[0]['keterangan_kurang']) && !empty($pakan_rusak_hilang[0]['no_ba'])) ? 'readonly' : ''; ?>>
								</div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>


	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading">&nbsp;</div>
	<div class="panel-body">

    <div class="col-md-12 div-lampirkan-foto text-left">
    </div>
		<div class="col-md-12">
			<div class="col-md-12 ">
				<div class="form-group form-horizontal">

          <div class="form-inline new-line">
            <label for="tanggal-kirim">Lampirkan Foto</label>
            <div class="form-group" style="padding-left: 2%">
                <div class="input-group">
                    <input type="text" class="form-control" id="lampirkan-foto" name="lampirkan-foto" 
                    value="<?php echo empty($value['attachment_name']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : $value['attachment_name']; ?>" readonly>
                    
                <span class="btn btn-default btn-file input-group-addon">
                                <b>...</b> <input type="file" id="file-upload" <?php echo empty($value['attachment_name']) && empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'disabled'; ?>>
                            </span></div>
            </div>
        </div>
        <div id='format-file'>
        </div>
        </div>
			</div>
		</div>
		<div class="col-md-12 text-center">
			<button onclick="simpan_pakan_rusak_hilang(this)" type="button" data-ke="<?php echo $tmp_data_ke; ?>"
				class="btn btn-default" <?php echo empty($pakan_rusak_hilang[0]['no_ba']) ? '' : 'disabled'; ?>>Simpan</button>
		</div>
	</div>
</div>