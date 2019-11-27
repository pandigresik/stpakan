
<div class="panel panel-default">
	<div class="panel-heading">Pakan Rusak/Hilang</div>
	<div class="panel-body">
		<div class="div-detail-pakan-rusak-hilang">
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
                <!--pre><?php print_r($pakan_rusak_hilang); ?></pre-->
                <?php if(isset($pakan_rusak_hilang['detail_rusak']['data'])){ ?>
                	<?php foreach ($pakan_rusak_hilang['detail_rusak']['data'] as $key => $value) { ?>
								<tr class="row-timbang" data-ke="<?php echo $data_ke; ?>">
									<td><?php echo $data_ke; ?>.</td>
									<td><input type="text"
										value="<?php echo $value['berat']; ?>"
										placeholder="Berat" name="berat-rusak"
										onkeyup="number_only(this)"
										onchange="kontrol_berat_rusak(this)"
										class="form-control berat-rusak"
										<?php echo empty($value['berat']) ? '' : 'readonly'; ?>></td>
									<td><input type="text" placeholder="Keterangan"
										name="keterangan-rusak"
										value="<?php echo $value['keterangan']; ?>"
										class="form-control keterangan-rusak"
										<?php echo empty($value['keterangan']) ? '' : 'readonly'; ?>></td>
									<td>
										<div onclick="tambah_timbang_rusak(this)" class="div-plus hide">

											<span class="glyphicon glyphicon-plus"></span>
										</div>
										<div onclick="hapus_timbang_rusak(this)" class="div-minus hide">
											<span class="glyphicon glyphicon-minus"></span>
										</div>
									</td>
								</tr>
                	<?php $data_ke++; ?>
                	<?php } ?>
                <?php } else{ ?>
								<tr class="row-timbang" data-ke="<?php echo $data_ke; ?>">
									<td><?php echo $data_ke; ?>.</td>
									<td><input type="text"
										placeholder="Berat" name="berat-rusak"
										onkeyup="number_only(this)"
										onchange="kontrol_berat_rusak(this)"
										class="form-control berat-rusak"
										<?php echo isset($pakan_rusak_hilang['detail_rusak']['data']) || isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'readonly' : ''; ?>
										>
										</td>
									<td><input type="text" placeholder="Keterangan"
										name="keterangan-rusak"
										onchange="kontrol_keterangan_rusak(this)"
										class="form-control keterangan-rusak"
										<?php echo isset($pakan_rusak_hilang['detail_rusak']['data']) || isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'readonly' : ''; ?>
										>
									<td>
										<div onclick="tambah_timbang_rusak(this)" class="div-plus <?php echo isset($pakan_rusak_hilang['detail_rusak']['data']) || isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'hide' : ''; ?>">
											<span class="glyphicon glyphicon-plus"></span>
										</div>
										<div onclick="hapus_timbang_rusak(this)" class="div-minus <?php echo isset($pakan_rusak_hilang['detail_rusak']['data']) || isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'hide' : ''; ?>">
											<span class="glyphicon glyphicon-minus"></span>
										</div>
									</td>
								</tr>
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
										onkeyup="number_only(this)" value="<?php echo isset($pakan_rusak_hilang['detail_hilang']['data']) ? $pakan_rusak_hilang['detail_hilang']['data']['jumlah'] : ''; ?>" 
                    <?php echo isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'readonly' : ''; ?>>
								</div>

							</div>
							<div class="form-group">
								<label for="label" class="col-md-5 control-label text-left">Keterangan</label>

								<div class="col-md-6">
									<input type="text" class="form-control validasi keterangan"
										name="keterangan" onchange="" placeholder="Keterangan"
										onkeyup="" value="<?php echo isset($pakan_rusak_hilang['detail_hilang']['data']) ? $pakan_rusak_hilang['detail_hilang']['data']['keterangan'] : ''; ?>" 
                    <?php echo isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'readonly' : ''; ?>>
								</div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>


	</div>
	</div>
</div>
<div class="panel panel-default" id="panel-lampirkan-foto">
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
                    value="<?php echo isset($pakan_rusak_hilang['detail_rusak']['data']) ? $pakan_rusak_hilang['detail_rusak']['nama_file'] : '' ?>" readonly>
                    
                <span class="btn btn-default btn-file input-group-addon">
                                <b>...</b> <input type="file" id="file-upload">
                            </span></div>
            </div>
        </div>
        <div id='format-file'>
        </div>
        </div>
			</div>
		</div>
		<div class="col-md-12 text-center">
			<button onclick="simpan_pakan_rusak_hilang(this)" id="btn-simpan" ondblclick="not_actived(this)" type="button" data-ke="<?php echo $tmp_data_ke; ?>"
				<?php echo isset($pakan_rusak_hilang['detail_rusak']['data']) || isset($pakan_rusak_hilang['detail_hilang']['data']) ? 'disabled' : ''; ?>
				class="btn btn-default">Simpan</button>
		</div>
	</div>
</div>
</div>