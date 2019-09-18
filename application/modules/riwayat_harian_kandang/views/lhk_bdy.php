<div class="panel panel-default">
  <div class="panel-heading">Laporan Harian Kandang</div>
  <div class="panel-body">
	<div class="col-md-12">
		<center><h1><?php echo $nama_farm;?></h1></center>
		<div class="row">
			<button type="button" name="tombolSimpan" id="btnSimpan" class="btn btn-primary" style="display:none;">Simpan</button>
			<!--<button type="button" name="tombolPanen" id="btnPanen" class="btn btn-primary" style="display:none;">Panen</button>-->
			<!--<button type="button" name="tombolTutupSiklus" id="btnTutupSiklus" class="btn btn-primary" style="display:none;">Tutup Siklus</button>-->
			<br/><br/>
		</div>
		<div class="row">
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_kandang">Kandang</label>
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_farm" value="<?php echo $kode_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="farm" id="inp_nama_farm" value="<?php echo $nama_farm;?>">
						<input type="hidden" class="form-control input-sm field_input" name="today" id="inp_today" value="<?php echo $today;?>">
						<input type="hidden" class="form-control input-sm field_input" name="doc_in_campur" id="inp_doc_in_campur" value="">
						<input type="text" class="form-control input-sm field_input" name="kandang" id="inp_kandang" style="text-align:left">
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_flock">Flock</label>
						<input type="text" class="form-control input-sm field_input" name="flock" id="inp_flock" disabled>
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_doc_in">Tanggal DOC-In</label>
						<input type="text" class="form-control input-sm field_input" id="inp_doc_in" disabled>
					</div>
				</form>
			</div>
			<div class="col-md-3">
				<form class="form-inline">
					<div class="form-group pull-right">
						<label for="inp_doc_in">Umur</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px" name="umur" id="inp_umur" disabled> hari
					</div>
				</form>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-5">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_flock" style="margin-left:55px">Tgl. LHK</label>
						<div class="form-group">
							<div class="input-group date" id="div_tgl_lhk">
								<input type="text" name="startDate" style="width:100px;" class="form-control disabled" id="inp_tgl_lhk" readonly />
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
						<!-- Diganti pilih tanggal-->
						<!--<input type="text" class="form-control input-sm field_input" style="width:100px" name="flock" id="inp_tgl_lhk" disabled>-->
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-3">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_umur" style="">IP</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px;" name="ip" id="inp_ip" disabled> &nbsp;&nbsp;&nbsp;
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-4">
				<center>
				<form class="form-inline">
					<div class="form-group">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label for="inp_umur">FCR</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px" name="fcr" id="inp_fcr" disabled>
					</div>
				</form>
				</center>
			</div>

		</div>
		<br/>
		<div class="row">
			<div class="col-md-4">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_populasi_awal_stlh_umur_7">Populasi awal setelah umur 7</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px;text-align:right" name="populasi_awal_stlh_umur_7" id="inp_populasi_awal_stlh_umur_7" disabled> ekor
						<input type="hidden" class="form-control input-sm field_input"  style="width:100px;text-align:right" name="populasi_awal_stlh_umur_7_temp" id="inp_populasi_awal_stlh_umur_7_temp">
					</div>
				</form>
				</center>
			</div>
			<div class="col-md-4">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_umur" style="margin-left:35px;">BB rata-rata</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px" name="kandang" id="inp_bb_rata" disabled> Kg
					</div>
				</form>
				</center>
			</div>

			<div class="col-md-4">
				<center>
				<form class="form-inline">
					<div class="form-group">
						<label for="inp_umur" style="margin-left:30px;">ADG</label>
						<input type="text" class="form-control input-sm field_input"  style="width:100px" name="adg" id="inp_adg" disabled>
					</div>
				</form>
				</center>
			</div>
		</div>

		<br/>

		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Penimbangan per Sekat</div>
				<div class="panel-body">
					<div class="col-md-6">
						<table id="lhk_sekat" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-2">Sekat</th>
									<th class="vert-align" style="width:50px">Jumlah</th>
									<th class="vert-align" style="width:50px">BB (g)</th>
									<th class="vert-align col-md-6">Keterangan</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Populasi</div>
				<div class="panel-body">
					<div class="col-md-10">
						<table id="lhk_populasi" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-1" rowspan="2">Populasi<br>Awal</th>
									<th class="vert-align col-md-1">Penambahan</th>
									<th class="vert-align col-md-1" colspan="4">Pengurangan</th>
									<!--<th class="vert-align col-md-1" rowspan="2">Panen</th>-->
									<th class="vert-align col-md-1" rowspan="2">Populasi<br/>Akhir</th>
									<th class="vert-align col-md-1" rowspan="2">DH (%)</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
								</tr>
								<tr>
									<th class="vert-align col-md-1" >Lain-lain</th>
									<th class="vert-align col-md-1" >Mati</th>
									<th class="vert-align col-md-1" >Afkir</th>
									<th class="vert-align col-md-1" >Lain-lain</th>
									<th class="vert-align col-md-2" >Panen</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAwal" value="0" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_tambahLain" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangMati" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangAfkir" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_kurangLain" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_panen" value="0" onkeyup="cekNumerikPopluasi(this)" disabled /></td>
									<td><input type="text" class="form-control input-sm inp-numeric" id="inp_populasiAkhir" value="0" disabled /></td>
									<td>
										<input type="text" class="form-control input-sm inp-numeric" id="inp_dayahidup" value="0" disabled />
										<input type="hidden" class="form-control input-sm inp-numeric" id="inp_dayahidup_temp" value="0"/>
									</td>
									<td>
										<textarea class="form-control" id="inp_ket_kematian" disabled></textarea>
									</td>
								</tr>

							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="panel panel-primary">
				<div class="panel-heading">Laporan Harian Kandang - Pakan</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="lhk_pakan" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align" rowspan="2">Jenis<br>Kelamin</th>
									<th class="vert-align" rowspan="2">Pakan</th>
									<th class="vert-align" colspan="2">Stok Awal</th>
									<th class="vert-align" colspan="2">Pakan Rusak</th>
									<th class="vert-align" colspan="2">Kirim</th>
									<th class="vert-align" colspan="2">Terpakai</th>
									<th class="vert-align" colspan="2">Stok AKhir</th>
								</tr>
								<tr>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
									<th width="90px" class="vert-align" >Kg</th>
									<th width="90px" class="vert-align" >Sak</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div class="col-md-4"></div>
				</div>
			</div>
		</div>
	</div>
  </div>
</div>

<div class="modal fade" id="modal_pengurangan_ayam" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:50%">
    <div class="modal-content">
		<div class="modal-body">
			<div class="panel panel-primary">
				<div class="panel-heading">Pengurangan Ayam</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="md_pengurangan_ayam" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-3" rowspan="2">Tujuan Kandang</th>
									<th class="vert-align col-md-2" colspan="2">Jumlah Pindah</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-1" rowspan="2">No. Berita Acara</th>
									<th class="vert-align col-md-1" rowspan="2"></th>
								</tr>
								<tr>
									<th class="vert-align col-md-1">Jantan</th>
									<th class="vert-align col-md-1">Betina</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-12 has-error" id="pindahKandangErrMsgKet" style="display:none;color:red;"></div>
			<div class="col-md-12 has-error" id="pindahKandangErrMsgKet" style="display:none;color:red;"></div>
		</div>

		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolBatal" id="btnBatalPengurangan" class="btn btn-default">Batal</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPengurangan" class="btn btn-primary">Simpan</button>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_penambahan_ayam_lain" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:70%">
    <div class="modal-content">
		<div class="modal-header">
			<center><h4 class="modal-title">Penambahan Lain-lain</h4></center>
		</div>
		<div class="modal-body">
			<div class="panel panel-primary">
				<div class="panel-heading">Penambahan Lain-lain</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="md_penambahan_ayam_lain" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-4" colspan="2">Jumlah Penambahan Lain-lain</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-5" colspan="2">Berita Acara</th>
								</tr>
								<tr>
									<th class="vert-align col-md-2">Jantan</th>
									<th class="vert-align col-md-2">Betina</th>
									<th class="vert-align col-md-2">No. Berita Acara</th>
									<th class="vert-align col-md-3">Lampiran Berita Acara</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="number" class="form-control input-sm" name="slider_tambah_lain_jantan[]" value="" onchange="checkBatasJantanLain(this)"></td>
									<td><input type="number" class="form-control input-sm" name="slider_tambah_lain_betina[]" value="" onchange="checkBatasBetinaLain(this)"></td>
									<td><input type="text" class="form-control" name="inp_tambah_jantan_lain_ket[]"></td>
									<td><input type="text" class="form-control" name="inp_tambah_jantan_lain_nomemo[]"></td>
									<td>
										<div class="input-group">
											<span class="input-group-btn">
												<span class="btn btn-primary btn-file">
													Browse <input type="file" name="uploadFileTambahLain[]">
												</span>
											</span>
											<input class="form-control" type="text" readonly="">
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-12 has-error" id="tambahLainErrMsgKet" style="display:none;color:red;"></div>
		</div>

		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolBatal" id="btnBatalPenambahanLain" class="btn btn-default">Batal</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPenambahanLain" class="btn btn-primary">Simpan</button>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_pengurangan_ayam_lain" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:70%">
    <div class="modal-content">
		<div class="modal-header">
			<center><h4 class="modal-title">Pengurangan Lain-lain</h4></center>
		</div>
		<div class="modal-body">
			<div class="panel panel-primary">
				<div class="panel-heading">Pengurangan Lain-lain</div>
				<div class="panel-body">
					<div class="col-md-12">
						<table id="md_pengurangan_ayam_lain" class="table table-bordered table-condensed">
							<thead>
								<tr>
									<th class="vert-align col-md-4" colspan="2">Jumlah Pengurangan Lain-lain</th>
									<th class="vert-align col-md-2" rowspan="2">Keterangan</th>
									<th class="vert-align col-md-5" colspan="2">Berita Acara</th>
								</tr>
								<tr>
									<th class="vert-align col-md-2">Jantan</th>
									<th class="vert-align col-md-2">Betina</th>
									<th class="vert-align col-md-2">No. Berita Acara</th>
									<th class="vert-align col-md-3">Lampiran Berita Acara</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><input type="number" class="form-control input-sm" name="slider_kurang_lain_jantan[]" value="" onchange="checkBatasJantanLain(this)"></td>
									<td><input type="number" class="form-control input-sm" name="slider_kurang_lain_betina[]" value="" onchange="checkBatasBetinaLain(this)"></td>
									<td><input type="text" class="form-control" name="inp_kurang_jantan_lain_ket[]"></td>
									<td><input type="text" class="form-control" name="inp_kurang_jantan_lain_nomemo[]"></td>
									<td>
										<div class="input-group">
											<span class="input-group-btn">
												<span class="btn btn-primary btn-file">
													Browse <input type="file" name="uploadFileKurangLain[]">
												</span>
											</span>
											<input class="form-control" type="text" readonly="">
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col-md-12 has-error" id="kurangLainErrMsgKet" style="display:none;color:red;"></div>
		</div>

		<div class="modal-footer" style="margin:0px;padding:3px;">
			<div class="pull-right">
				<button type="button" name="tombolBatal" id="btnBatalPenguranganLain" class="btn btn-default">Batal</button>
				<button type="button" name="tombolSimpan" id="btnSimpanPenguranganLain" class="btn btn-primary">Simpan</button>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal fade bs-example-modal-lg" id="modal_sisa" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-body">
			<form name="returForm" method="post" id="returForm" action="riwayat_harian_kandang/printRetur" target="_blank">

			<div class="row">
				<div class="col-md-12">
					<center><h1>Retur Pakan Ke Gudang</h1></center>
					<center><h3>Farm <span id="titlefarm"></span></h3></center>
				</div>
			</div>
			<br/>
			<div class="row">
				<input type="hidden" name="inp_print_kodefarm" id="inp_print_kodefarm">
				<input type="hidden" name="inp_print_farm" id="inp_print_farm">
			</div>
			<div class="row">
				<div class="col-md-2">Kandang</div>
				<div class="col-md-5">: <span id="print_nama_kandang"></span><input type="hidden" name="inp_print_kandang" id="inp_print_kandang"></div>
				<div class="col-md-3">Tanggal Tutup Siklus</div>
				<div class="col-md-2">: <span id="print_tgl_lhk"></span><input type="hidden" name="inp_print_tgl" id="inp_print_tgl"></div>
			</div>

			<br/><br/>

			<div class="row">
				<div class="col-md-12">
					<table id="tb_sisa" class="table table-condensed table-striped table-bordered">
						<thead>
							<tr>
								<th class="vert-align">Kode Pakan</th>
								<th class="vert-align">Nama Pakan</th>
								<th class="vert-align">Jumlah<br>(zak)</th>
								<th class="vert-align">Berat<br>(kg)</th>
								<th class="vert-align">Bentuk<br>Pakan</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="vert-align">11-1234-21</td>
								<td class="vert-align">P 3 COBB</td>
								<td class="vert-align">10</td>
								<td class="vert-align">501.23</td>
								<td class="vert-align">CRUMBLE</td>
							</tr>
							<tr>
								<td class="vert-align">11-1234-22</td>
								<td class="vert-align">PJB COBB</td>
								<td class="vert-align">2</td>
								<td class="vert-align">120.25</td>
								<td class="vert-align">CRUMBLE</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<br/>

			Tabel di atas adalah sisa pakan dari Kandang pada Akhir Siklus yang dikembalikan ke Gudang.
			<br/><br/><br/>

			<table class="table borderless">
				<tr>
					<td class="col-md-4 vert-align borderless">Kepala Unit/Farm</td>
					<td class="col-md-4 vert-align borderless">Admin Gudang</td>
					<td class="col-md-4 vert-align borderless">Pengawas Kandang</td>
				</tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr><td class="col-md-4 vert-align borderless"></td><td class="borderless col-md-4 vert-align"></td><td class="borderless col-md-4 vert-align"></td></tr>
				<tr>
					<td class="col-md-4 vert-align borderless">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless">(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
					<td class="col-md-4 vert-align borderless"><button name="tombolPrint" id="btnPrint" class="btn btn-primary">Simpan</button></td>
				</tr>
			</table>
			</form>
		</div>
    </div>
  </div>
</div>

<div class="modal bs-example-modal-lg" id="modal_uniformity" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Uniformity</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					<center><h2>Farm <?php echo $nama_farm;?></h2></center>
					<center><h4>Tabel Berat Badan</h4></center>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Jenis Kelamin</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_jk">
								</div>
							</form>
						</div>
						<div class="col-md-6">
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Umur</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_umur" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>hari</span>
								</div>
							</form>
						</div>
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_tberat" style="width:100px">Target Berat</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_tberat" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>gram</span>
								</div>
							</form>
						</div>
					</div>
					<br/>
					<div class="panel panel-primary">
						<div class="panel-heading">Detail Penimbangan</div>
						<div class="panel-body">
							<div class="col-md-12">
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_tsampling" style="width:120px">Total Sampling</label>
										<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_tsampling" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<button type="button" name="btnSimpanUniform" id="btnOKTimbang"  class="btn btn-primary">OK</button>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<table id="preview_detail_penimbangan" class="table table-bordered table-condensed">
									<thead>
										<tr>
											<th class="vert-align">Berat(g)</th>
											<th class="vert-align">Jumlah Ayam<br>(ekor)</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="col-md-6">
							<p>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Uniformity</label>
										<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_uniformity" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<span>%</span>
									</div>
								</form>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Status</label>
										<label for="inp_uni_uniformity" id="lbl_status_uniformity" style="width:120px"></label>
									</div>
								</form>
							</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal bs-example-modal-lg" id="modal_uniformity_betina" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">Uniformity</h4>
		</div>
		<div class="modal-body">
			<div class="panel panel-default">
				<div class="panel-body">
					<center><h2>Farm <?php echo $nama_farm;?></h2></center>
					<center><h4>Tabel Berat Badan</h4></center>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Jenis Kelamin</label>
									<input type="text" class="form-control field_input inp-numeric" disabled style="width:80px" id="inp_uni_jk_betina">
								</div>
							</form>
						</div>
						<div class="col-md-6">
						</div>
					</div>
					<br/>
					<div class="row">
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_umur" style="width:100px">Umur</label>
									<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_umur_betina" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>hari</span>
								</div>
							</form>
						</div>
						<div class="col-md-6">
							<form class="form-inline">
								<div class="form-group">
									<label for="inp_uni_tberat" style="width:100px">Target Berat</label>
									<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_tberat_betina" onkeyup="cekNumerik(this)">
								</div>
								<div class="form-group">
									<span>gram</span>
								</div>
							</form>
						</div>
					</div>
					<br/>
					<div class="panel panel-primary">
						<div class="panel-heading">Detail Penimbangan</div>
						<div class="panel-body">
							<div class="col-md-12">
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_tsampling" style="width:120px">Total Sampling</label>
										<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_tsampling_betina" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<button type="button" name="btnSimpanUniform" id="btnOKTimbang_betina" class="btn btn-primary">OK</button>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<table id="preview_detail_penimbangan_betina" class="table table-bordered table-condensed">
									<thead>
										<tr>
											<th class="vert-align">Berat(g)</th>
											<th class="vert-align">Jumlah Ayam<br>(ekor)</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>
							<div class="col-md-6">
							<p>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Uniformity</label>
										<input type="text" class="form-control field_input inp-numeric" style="width:80px" disabled id="inp_uni_uniformity_betina" onkeyup="cekNumerik(this)">
									</div>
									<div class="form-group">
										<span>%</span>
									</div>
								</form>
								<form class="form-inline">
									<div class="form-group">
										<label for="inp_uni_uniformity" style="width:120px;font-weight:normal">Status</label>
										<label for="inp_uni_uniformity" id="lbl_status_uniformity_betina" style="width:120px"></label>
									</div>
								</form>
							</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_pengisian_keterangan" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog"  style="width:30%;;">
    <div class="modal-content">
		<div class="modal-body" style="margin:0px;padding:0px;>
			<div class="panel panel-primary">
				<div class="panel-heading">Keterangan Populasi Mati</div>
				<div class="panel-body">
					<div class="col-md-12">
						<textarea class="form-control" style="width:100%" name="inp_pengisian_keterangan" onkeyup="checkPengisianKeterangan(this)" id="inp_pengisian_keterangan" maxLength="50"></textarea>
					</div>
					<div class="col-md-12 has-error" id="pengisian_keteranganErrMsg" style="display:none;color:red;"></div>
					<div class="col-md-12">
						<br/>
						<center>
							<button type="button" name="tombolLanjutSimpan" id="btntombolLanjutSimpan" class="btn btn-primary disabled">Simpan</button>
						</center>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer" style="margin:0px auto;padding:3px;">

		</div>
    </div>
  </div>
</div>

<style type="text/css">
	hr {
		-moz-border-bottom-colors: none;
		-moz-border-image: none;
		-moz-border-left-colors: none;
		-moz-border-right-colors: none;
		-moz-border-top-colors: none;
		border-color: #EEEEEE -moz-use-text-color #FFFFFF;
		border-style: solid none;
		border-width: 1px 0;
		margin: 18px 0;
	}

	.table thead>tr>th.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align{
		vertical-align: middle;
		text-align : center;
	}
	.table tbody>tr>td.vert-align-sm{
		vertical-align: middle;
		text-align : center;
		font-size:12px;
		padding:2px;
	}
	.table tbody>tr>td.right-align-sm{
		vertical-align: middle;
		text-align : right;
		font-size:12px;
		padding:2px;
	}
	.table tbody tr.highlight td {
		background-color: #CBE8F7;
	}

	.table tbody>tr.rasio {
		background-color: #CBE8F7;
	}

	.table tbody>tr>td.borderless{
		border: none;
	}

	.link:hover{
		cursor:pointer;
	}

	.col-centered {
		display:inline-block;
		float:none;
		/* reset the text-align */
		text-align:left;
		/* inline-block space fix */
		margin-right:-4px;
	}

	.inp-numeric{
		text-align:right;
	}

	    .btn-file {
        position: relative;
        overflow: hidden;
    }
    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
	#modal_sisa .modal-dialog .modal-content .modal-body  {max-height:100%;}

</style>
<script type="text/javascript">

</script>
<link type="text/css" href="assets/libs/bootstrap/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="assets/libs/bootstrap/js/moment.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/riwayat_harian_kandang/riwayat_harian_kandang_bdy.js"></script>
