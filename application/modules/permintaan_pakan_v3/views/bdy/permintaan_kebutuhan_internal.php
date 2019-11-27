<?php if(!$lock_pp) { ?>
<div class="row">
	<div class="col-md-12">
		<div class="btn btn-default" onclick="Permintaan.showModalTambahPakan(this,0)">Tambah Pakan</div>
		<div class="btn btn-default" onclick="Permintaan.hapusTambahPakan(this)" id="btnHapusPakan" disabled>Batal Tambah</div>
	</div>											
</div>
<?php } ?>										
<div class="row  new-line">
	<div class="col-md-12">
		<div class="table-responsive">
		<table class="table table-bordered">
			<thead class="header_center">
				<tr>
					<th rowspan="2">Jenis Pakan</th>
					<th rowspan="2">Populasi</th>
					<th rowspan="2">Tanggal LHK</th>
					<th rowspan="2">Umur Kandang <br /> (Hari)</th>
					<th rowspan="2">Tanggal Kebutuhan</th>
					<th rowspan="2">Rekomendasi kebutuhan <br /> (Sak)</th>
					<th rowspan="2">Stok Pakan Kandang (Sak)</th>
					<th rowspan="2">Pakan dari Farm Lain <br /> (Sak)</th>
					<th rowspan="2">Rekomendasi PP <br /> (sak)</th>
					<th colspan="2">Pengajuan Kepala Farm</th>
					<th colspan="2">Pengajuan Kepala Departemen</th>					
				</tr>
				<tr>
				<!--	<th>Gudang</th>
					<th>Kandang</th> -->
					<th>Sak</th>
					<th>Alasan</th>
					<th>Sak</th>
					<th>Alasan</th>
				</tr>
			</thead>
			<tbody class="text-center">
				<?php 
					echo $kebutuhan_pakan;
				?>	
			</tbody>
		</table>
		</div>
	</div>
</div>	