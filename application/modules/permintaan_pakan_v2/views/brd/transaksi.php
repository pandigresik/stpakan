		<div class="row">
			<?php if(!empty($tombol_ubah_tanggal)){ ?>
			<div class="col-md-1" id="div_tombol_simpan">
				<?php echo $div_tombol_simpan ?>
			</div>
			<div class="col-md-1" id="div_ubah_tanggal">
				<?php echo $tombol_ubah_tanggal ?>
			</div>
		<?php }
			else{ ?>
			<div class="col-md-2" id="div_tombol_simpan">
				<?php echo $div_tombol_simpan ?>
			</div>
		<?php	}
		?>	
			
		</div>
		<div class="row col-md-12">
			<div class="form-inline new-line">
				<label for="no_pp">No. PP</label>
				<div class="form-group">
					<div class="input-group">
						<input type="text" readonly name="no_pp" value="<?php echo isset($pp['NO_LPB']) ? $pp['NO_LPB'] : '' ?>" class="form-control">
					</div>
				</div>
				<label for="tgl_permintaan">Tanggal Permintaan</label>
				<div class="form-group">
					<div class="input-group">
						<input type="date" readonly="" name="tgl_permintaan" class="form-control"  value="<?php echo isset($pp['TGL_BUAT']) ? tglIndonesia($pp['TGL_BUAT'],'-',' ') : '' ?>">
						<div class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="form-inline new-line">
				<label for="no_pp">No. Ref. PP</label>
				<div class="form-group">
					<div class="input-group">
						<span class="link_span" data-status="V" data-no_pp="<?php echo isset($pp['REF_ID']) ? $pp['REF_ID'] : '' ?>" id="ref_id" onclick="Permintaan.detail_pp_popup(this)"><?php echo isset($pp['REF_ID']) ? $pp['REF_ID'] : '' ?></span> 
					</div>
				</div>
			</div>
		</div>
		
		<div class="col-md-10 new-line" id="tabel_pp">
			<table class="table table-bordered  new-line" >
				<thead>
					<tr>
						<th>Tanggal Kirim</th>
						<th>Tanggal Kebutuhan</th>
						<th>Umur Pakan</th>
						<th>Kuantitas PP <br /> (sak)</th>
						<th>Keterangan</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php 
					if(!empty($data_pp)){
						foreach($data_pp as $baris){
							
				?>			
				<tr data-tgl_kirim="<?php echo tglIndonesia($baris['TGL_KIRIM'],'-',' ')?>">
						<td>
							<div class="col-md-12">
								<div class="input-group">
									<input type="text" readonly="" name="tgl_kirim" class="form-control" value="<?php echo tglIndonesia($baris['TGL_KIRIM'],'-',' ')?>">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>	
						</td>
						<td>
							<div class="row">
								<div class="col-sm-5">
						            <div class="form-group">
						                <div class="input-group date">
						                    <input type="text" readonly="" name="tgl_keb_awal" class="form-control" value="<?php echo tglIndonesia($baris['TGL_KEB_AWAL'],'-',' ')?>">
						                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
						            </div>
						        </div>
						    	<div class="col-sm-1 vcenter">s.d.</div>    
						        <div class="col-sm-5">
						            <div class="form-group">
						                <div class="input-group date">
						                    <input type="text" readonly="" name="tgl_keb_akhir" class="form-control"  value="<?php echo tglIndonesia($baris['TGL_KEB_AKHIR'],'-',' ')?>">
						                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						                    </span>	
						                </div>
						            </div>
						        </div>
							</div>
						</td>
						<td class="umur_pakan number"><?php echo dateDifference($baris['TGL_KIRIM'],$baris['TGL_KEB_AKHIR']) ?></td>
						<td class="kuantitas_pp number"><?php echo isset($baris['TOTAL_PP']) ? $baris['TOTAL_PP'] : '-' ?></td>
						<td class="keterangan_pp"><textarea <?php echo $status_keterangan ?> name="keterangan"><?php echo isset($baris['KETERANGAN']) ? $baris['KETERANGAN'] : '-' ?></textarea></td>
						<td><span class="btn btn-default pilih_btn exist" data-grup_farm="<?php echo $grup_farm ?>" data-hitung_ulang="<?php echo $bisaHitungUlang ?>" data-no_pp="<?php echo isset($pp['NO_LPB']) ? $pp['NO_LPB'] : '' ?>" onclick="Permintaan.list_kebutuhan_pakan(this)">Pilih</span></td>
					</tr>
				<?php 	}
					}
					else{
				?>	
					<tr>
						<td>
							<div class="col-md-12">
								<div class="input-group">
									<input type="text" readonly="" name="tgl_kirim" class="form-control">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-calendar"></span>
									</div>
								</div>
							</div>	
						</td>
						<td>
							<div class="row">
								<div class="col-sm-5">
						            <div class="form-group">
						                <div class="input-group date">
						                    <input type="text" readonly="" name="tgl_keb_awal" class="form-control">
						                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						                    </span>
						                </div>
						            </div>
						        </div>
						    	<div class="col-sm-1 vcenter">s.d.</div>    
						        <div class="col-sm-6">
						            <div class="form-group">
						                <div class="input-group date">
						                    <input type="text" readonly="" name="tgl_keb_akhir" class="form-control">
						                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
						                    </span>	
						                </div>
						            </div>
						        </div>
							</div>
						</td>
						<td class="umur_pakan">-</td>
						<td class="kuantitas_pp">-</td>
						<td class="keterangan_pp"><textarea name="keterangan">-</textarea></td>
						<td><span class="btn btn-default pilih_btn new" data-grup_farm="<?php echo $grup_farm ?>" data-hitung_ulang="1" data-no_pp="" onclick="Permintaan.list_kebutuhan_pakan(this)">Pilih</span></td>
					</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					
					<tr class="<?php echo $tambah_pengiriman ?>">
						<td colspan="4">
							<?php if(!$pp_awal){?>
							<span class="btn btn-default" id="link_tambah_pengiriman">Tambah Pengiriman</span>&nbsp;
							<span class="btn btn-default" id="link_hapus_pengiriman">Hapus Pengiriman</span>
							<?php }?>
						</td>
					</tr>
					
				</tfoot>
			</table>
		</div>
		
		<div class="row">
			<div class="panel panel-default"  id="div_sisa_konsumsi_pakan">
				<div class="panel-heading">Sisa Konsumsi Pakan <span class="infoKandang"></span></div>
				<div class="panel-body">
						<div class="col-md-12" id="sisa_konsumsi_pakan"></div>
				</div>	
			</div>
		</div>
		
		<div class="row">
			<div class="panel panel-default">
				<div class="panel-heading">Permintaan Kebutuhan Pakan Internal</div>
				<div class="panel-body">
						<div class="col-md-12" id="kebutuhan_pakan_internal"></div>
				</div>	
			</div>
		</div>
<link rel="stylesheet" type="text/css" href="assets/css/permintaan_pakan/transaksi.css?u" >
<link rel="stylesheet" type="text/css" href="assets/css/home/kertaskerja.css" >
					
<script type="text/javascript" src="assets/js/permintaan_pakan_v2/transaksi.js"></script>
			