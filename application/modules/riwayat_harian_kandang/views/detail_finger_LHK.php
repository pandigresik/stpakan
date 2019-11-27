<?php if ($header_finger_LHK['umur_hari']>=1): ?>
	<div class="detail_finger_LHK" data-update_data="false" data-noreg="<?php echo $header_finger_LHK['NO_REG']; ?>">
		<div class="row" class="col-md-12">
			<div class="col-md-3">			
				<div class="form-group">
					<label for="inp_kandang">Kandang</label>
					<input type="text" class="form-control input-sm field_input" name="kandang" id="inp_kandang" value="<?php echo $header_finger_LHK['KODE_KANDANG']; ?>" disabled>
				</div>
			</div>
			<div class="col-md-3">			
				<div class="form-group">
					<label for="inp_flock">Flock</label>
					<input type="text" class="form-control input-sm field_input" name="flock" id="inp_flock" value="<?php echo $header_finger_LHK['FLOK_BDY']; ?>" disabled>
				</div>
			</div>
			<div class="col-md-3">			
				<div class="form-group">
					<label for="inp_doc_in">Tanggal DOC-In</label>
					<input type="text" class="form-control input-sm field_input" name="tglDOC-In" id="inp_doc_in" value="<?php echo tglIndonesia($header_finger_LHK['TGL_DOC_IN'],'-',' '); ?>" disabled>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="inp_umur">Umur</label>
					<input type="text" class="form-control input-sm field_input" name="umur" id="inp_umur" value="<?php echo $header_finger_LHK['umur_hari']; ?>" disabled>
				</div>
			</div>
		</div>
		<div class="row" class="col-md-12">
			<div class="col-md-6">			
				<div class="form-group">
					<label for="inp_kandang">Tanggal LHK</label>
					<input type="text" class="form-control input-sm field_input" name="tglLHK" id="inp_tgl_LHK" value="<?php echo tglIndonesia($header_finger_LHK['tgl_lhk'],'-',' '); ?>" disabled>
				</div>
			</div>
			<div class="col-md-6">			
				<div class="form-group">
					<label for="inp_flock">Pengawas</label>
					<input type="text" class="form-control input-sm field_input" name="pengawas" id="inp_pengawas" value="<?php echo $header_finger_LHK['NAMA_PEGAWAI']; ?>" disabled>
				</div>
			</div>
		</div>
		<table class="table table-condensed table-striped table-bordered custom_table">
			<thead>
				<tr>
					<th colspan="4">Penimbangan Per Sekat</th>
					<th colspan="2">Populasi</th>
					<th colspan="4">Pakan</th>
				</tr>
				<tr>
					<th>Sekat</th>
					<th>Jumlah</th>
					<th>BB(g)</th>
					<th>Keterangan</th>
					<th>Mati</th>
					<th>Afkir</th>
					<th>Jenis Kelamin</th>
					<th>Nama Pakan</th>
					<th>Terpakai</th>
					<th>Permintaan</th>
				</tr>
			</thead>
			<tbody>
				<?php for($i= 0 ; $i <= $max_rowspan[0]["max_rowspan"]-1 ; $i++ ): ?>
					<tr>
						<?php if (isset($detail_finger_LHK['penimbangan'][$i]['rowspan']) && !empty($detail_finger_LHK['penimbangan'][$i]['rowspan'])): ?>
							<td class="sekat penimbangan" data-row="<?php echo $detail_finger_LHK['penimbangan'][$i]['SEKAT']; ?>" rowspan="<?php echo $detail_finger_LHK['penimbangan'][$i]['rowspan']; ?>">
								<?php echo 'Sekat ' . $detail_finger_LHK['penimbangan'][$i]['SEKAT']; ?>
							</td>
							<td class="jumlah penimbangan" rowspan="<?php echo $detail_finger_LHK['penimbangan'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input inp-numeric" name="jumlah_penimbangan" id="inp_jml_penimbangan" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['penimbangan'][$i]['JUMLAH']; ?>" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['penimbangan'][$i]['JUMLAH']; ?>">
							</td>
							<td class="berat penimbangan" rowspan="<?php echo $detail_finger_LHK['penimbangan'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input inp-numeric" name="bb_penimbangan" id="inp_bb_penimbangan" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['penimbangan'][$i]['BERAT']; ?>" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['penimbangan'][$i]['BERAT']; ?>">
							</td>
							<td class="keterangan penimbangan" rowspan="<?php echo $detail_finger_LHK['penimbangan'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input" name="ket_penimbangan" id="inp_ket_penimbangan" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['penimbangan'][$i]['KETERANGAN']; ?>" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['penimbangan'][$i]['KETERANGAN']; ?>">
							</td>
						<?php endif; ?>
						<?php if (isset($detail_finger_LHK['populasi'][$i]['rowspan']) && !empty($detail_finger_LHK['populasi'][$i]['rowspan'])): ?>
							<td class="c_mati populasi" rowspan="<?php echo $detail_finger_LHK['populasi'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input inp-numeric" name="mati_populasi" id="inp_mati_populasi" max="<?php echo $detail_finger_LHK['populasi'][$i]['JUMLAH_AYAM']; ?>" onchange="CetakLHK.validatorMaxPengurang(this)" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['populasi'][$i]['C_MATI']; ?>" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['populasi'][$i]['C_MATI']; ?>">
							</td>
							<td class="c_afkir populasi" rowspan="<?php echo $detail_finger_LHK['populasi'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input inp-numeric" name="afkir_populasi" id="inp_afkir_populasi" max="<?php echo $detail_finger_LHK['populasi'][$i]['JUMLAH_AYAM']; ?>" onchange="CetakLHK.validatorMaxPengurang(this)" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['populasi'][$i]['C_AFKIR']; ?>" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['populasi'][$i]['C_AFKIR']; ?>">
							</td>
						<?php endif; ?>
						<?php if (isset($detail_finger_LHK['pakan'][$i]['rowspan']) && !empty($detail_finger_LHK['pakan'][$i]['rowspan'])): ?>
							<td class="jenis_kelamin pakan" data-row="<?php echo $detail_finger_LHK['pakan'][$i]['JENIS_KELAMIN']; ?>" rowspan="<?php echo $detail_finger_LHK['pakan'][$i]['rowspan']; ?>">
								<?php echo $detail_finger_LHK['pakan'][$i]['JENIS_KELAMIN']; ?>
							</td>
							<td class="kode_barang pakan" data-row="<?php echo $detail_finger_LHK['pakan'][$i]['KODE_BARANG']; ?>" rowspan="<?php echo $detail_finger_LHK['pakan'][$i]['rowspan']; ?>">
								<?php echo $detail_finger_LHK['pakan'][$i]['NAMA_BARANG']; ?>
							</td>
							<td class="jml_pakai pakan" rowspan="<?php echo $detail_finger_LHK['pakan'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input inp-numeric" name="jumlah_pakai_pakan" id="inp_jumlah_pakai_pakan" max="<?php echo $detail_finger_LHK['pakan'][$i]['JML_STOK_PAKAN_PAKAI']; ?>" onchange="CetakLHK.validatorMaxPakai(this)" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['pakan'][$i]['JML_PAKAI']; ?>" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['pakan'][$i]['JML_PAKAI']; ?>">
							</td>
							<td class="jml_permintaan pakan" rowspan="<?php echo $detail_finger_LHK['pakan'][$i]['rowspan']; ?>">
								<input type="text" class="form-control input-sm field_input inp-numeric" name="jumlah_permintaan_pakan" id="inp_jumlah_permintaan_pakan" min="0" max="<?php echo $detail_finger_LHK['pakan'][$i]['JML_MAKS_PP_ORDER']; ?>" data-status_ubah = "false" data-prior_value="<?php echo $detail_finger_LHK['pakan'][$i]['JML_PERMINTAAN']; ?>" onchange="CetakLHK.validatorMaxPP(this);" onblur="CetakLHK.check_changed_LHK(this);" value="<?php echo $detail_finger_LHK['pakan'][$i]['JML_PERMINTAAN']; ?>">
							</td>
						<?php endif; ?>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>
	</div>
	<div class="pull-right"><?php echo 'Di entri pada ' . tglIndonesia($detail_finger_LHK['populasi'][0]['TGL_BUAT'],'-',' ') . ' ' . date("H:i:s",strtotime($detail_finger_LHK['populasi'][0]['TGL_BUAT'])); ?></div>
<?php endif; ?>
<div>
	<p data-kode-pegawai=""></p>
	<p>Silakan scan fingerprint Operator untuk verifikasi</p>
</div>
<div class="fingerprint_verfication_message pull-right">
</div>