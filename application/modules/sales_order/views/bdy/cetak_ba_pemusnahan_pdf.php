<table cellpadding="3px">
	<tr>
		<td>
			<table>
				<tr>
					<td width="100%"  style="text-decoration:underline;font-weight:bold" align="center">BERITA ACARA PEMUSNAHAN GLANGSING BANGKAI</td>
				</tr>
				<tr>
					<td width="100%"  align="center">No. <?php echo $berita_acara->no_berita_acara ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<br />
	<tr>
		<td>
			Yang bertanda tangan dibawah ini, kami selaku Admin Gudang Farm <b><?php echo $farm->nama_farm ?></b>, telah melakukan pemusnahan glangsing untuk bangkai pada tanggal <?php echo convertElemenTglIndonesia($berita_acara->tgl_buat) ?>.
			<br /> Dengan rincian sebagai berikut :
		</td>
	</tr>
	<tr>
		<td>
			<table class="garis" width="100%"  align="center" style="border:1px solid black;" cellpadding="8px">
				<thead>
					<tr>
						<th width="10%" style="background-color:gray;border-right:1px solid black">No</th>
						<th width="15%" style="background-color:gray;border-right:1px solid black">Jenis Barang</th>
						<th width="20%" style="background-color:gray;border-right:1px solid black">Asal Kandang</th>
						<th width="20%" style="background-color:gray;border-right:1px solid black">Jumlah Sak</th>
						<th width="35%" style="background-color:gray;border-right:1px solid black">Keterangan</th>
					</tr>
				</thead>
				<tbody>
				<?php

					// if(!empty($berita_acara)){
					//
					// 		echo '<tr>
					// 			<td width="10%" style="border:1px solid black">1</td>
					// 			<td width="30%" style="border:1px solid black">Glangsing Bangkai</td>
					// 			<td width="20%" style="border:1px solid black">'.angkaRibuan($berita_acara->jml).'</td>
					// 			<td width="40%" style="border:1px solid black">'.$berita_acara->keterangan.'</td>
					// 		</tr>';
					// }
					if(!empty($berita_acara_d)){
						$no = 1;
						foreach ($berita_acara_d as $key => $value) {
							if ($no == 1) {
								echo '<tr>
									<td width="10%" style="border:1px solid black">'.$no.'</td>
									<td width="15%" style="border:1px solid black" rowspan="'.count($berita_acara_d).'">Glangsing Bangkai</td>
									<td width="20%" style="border:1px solid black">Kandang '.$value->KODE_KANDANG.'</td>
									<td width="20%" style="border:1px solid black">'.$value->jml_akhir.'</td>
									<td width="35%" style="border:1px solid black" rowspan="'.count($berita_acara_d).'">'.$berita_acara->keterangan.'</td>
								</tr>';
							}else {
								echo '<tr>
									<td width="10%" style="border:1px solid black">'.$no.'</td>
									<td width="20%" style="border:1px solid black">Kandang '.$value->KODE_KANDANG.'</td>
									<td width="20%" style="border:1px solid black">'.$value->jml_akhir.'</td>
								</tr>';
							}

							$no++;
						}
					}

				?>
				</tbody>
			</table>

		</td>
	</tr>
	<br />
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align="center" width="33%"></td>
					<td align="center" width="33%"></td>
					<td align="center" width="33%"><?php echo $farm->nama_farm ?>, <?php echo convertElemenTglIndonesia($berita_acara->tgl_buat) ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align="center" width="33%">Saksi,</td>
					<td align="center" width="33%"></td>
					<td align="center" width="33%">Dibuat Oleh,</td>
				</tr>
				<br /><br />
				<br /><br />
				<tr>
					<td align="center" width="33%">(...............................)</td>
					<td align="center" width="33%"></td>
					<td align="center" width="33%">(...............................)</td>
				</tr>

			</table>
		</td>
	</tr>
	<br />
	<br />
	<tr>
		<td>
			<table width="100%">
				<tr>
					<td align="center" width="33%">Mengetahui,</td>
					<td align="center" width="33%"></td>
					<td align="center" width="33%">Menyetujui,</td>
				</tr>
				<br /><br />
				<br /><br />
				<tr>
					<td align="center" width="33%">(...............................)</td>
					<td align="center" width="33%"></td>
					<td align="center" width="33%">(...............................)</td>
				</tr>

			</table>
		</td>
	</tr>
</table>
<style>
	table.garis{

	}
	table.garis th,table.garis td{
		border: 1px solid black;
		border-collapse: collapse;
	}

</style>
