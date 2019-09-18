<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<div>
		<h3 align="center"><u>LAPORAN STOK GLANGSING AKHIR SIKLUS</u></h3>
	</div>
	<table id="desc1">
		<tr>
			<td width="60">Unit/Farm</td>
			<td width="10">:</td>
			<td><?php echo $nama_farm?></td>
		</tr>
		<tr>
			<td>Siklus</td>
			<td>:</td>
			<td><?php echo $bulan?></td>
		</tr>
		<tr>
			<td>Tahun</td>
			<td>:</td>
			<td><?php echo $tahun?></td>
		</tr>
	</table>
	<br><br>
	<table id="desc2" width="700">
		<tr>
			<td width="50"></td>
			<td width="120" style="margin-left: 150px">Total Pakan Terima</td>
			<td width="10">:</td>
			<td style="border: 1px solid #000; text-align: right;"><?php echo $total_pakan_terima?></td>
		</tr>
		<tr>
			<td></td>
			<td>Total Pakan Pakai</td>
			<td>:</td>
			<td style="border: 1px solid #000; text-align: right;"><?php echo $total_pakan_pakai?></td>
		</tr>
		<tr>
			<td></td>
			<td>Sisa Pakan</td>
			<td>:</td>
			<td style="border: 1px solid #000; text-align: right;"><?php echo $sisa_pakan?></td>
		</tr>
	</table>
	<br><br>
	<table>
		<tr>
			<td align="left">
				<table id="desc3" width="500" border="1">
					<tr>
						<td width="300" height="30" valign="center" align="center" colspan="2" style="margin-left: 150px; line-height: 30px">KETERANGAN</td>
						<td width="50" align="right">Jumlah (Lembar)</td>
						<td width="50" align="right">Budget (Lembar)</td>
					</tr>
					<tr>
						<td width="70" align="center">I</td>
						<td width="230">Stok Glangsing bekas di farm siklus lalu (Sisa Siklus <?php echo $siklus_lalu?>)(Saldo Awal)</td>
						<td align="right"><?php echo $stok_lalu?></td>
						<td align="right"></td>
					</tr>
					<tr>
						<td align="center">II</td>
						<td>Pemasukan siklus ini (a)</td>
						<td align="right"><?php echo $pemasukan_siklus_ini?></td>
						<td align="right"></td>
					</tr>
					<tr>
						<td align="center">III</td>
						<td>Total Stok glangsing saat ini</td>
						<td align="right"><?php echo $glangsing_saat_ini?></td>
						<td align="right"></td>
					</tr>
					<tr>
						<td align="center">IV</td>
						<td>Dipakai Siklus <?php echo $periode_siklus?> (Pemakaian Intern)*</td>
						<td align="right"><?php echo $pemakaian_internal?></td>
						<td align="right"><?php echo $sisa_budget_internal?></td>
					</tr>
					<tr>
						<td align="center">V</td>
						<td>Dipakai Siklus <?php echo $periode_siklus?> (Pemakaian Ekstern)*</td>
						<td align="right"><?php echo $pemakaian_eksternal?></td>
						<td align="right"><?php echo $sisa_budget_eksternal?></td>
					</tr>
					<tr>
						<td align="center">VI</td>
						<td>Dijual</td>
						<td align="right"><?php echo $dijual?></td>
						<td align="right"></td>
					</tr>
					<tr>
						<td align="center">VII</td>
						<td>Sisa Siklus <?php echo $periode_siklus?></td>
						<td align="right"><?php echo $sisa?></td>
						<td align="right"></td>
					</tr>
				</table>
				<br><br><br><br><br><br><br><br>

				<table width="400" border="1">
					<tr>
						<td>Dibuat Oleh</td>
						<td>Disetujui Oleh</td>
						<td>Diperiksa Oleh</td>
						<td>Mengetahui</td>
					</tr>
					<tr>
						<td height="50">
							
						</td>
						<td>
							
						</td>
						<td>
							
						</td>
						<td>
							
						</td>
					</tr>
				</table>

			</td>
			<td width="180"></td>
			<td>
				<table border="0" cellpadding="2">
					<tr>
						<td colspan="4" align="left"><b>Pemakaian Glangsing (Intern) siklus <?php echo $periode_siklus?></b></td>
					</tr>
					<?php
						$number = 1;
						$total_pakai_internal = 0;
						foreach ($budget_internal as $data) {
							echo '<tr>
								<td width="15">'.$number.'</td>
								<td width="200">'.$data->nama.'</td>
								<td width="50" align="right" style="border-bottom: 1px solid #000">'.$data->jml_dipakai.'</td>
								<td>lembar</td>
							</tr>';
							$total_pakai_internal += $data->jml_dipakai;
							$number++;
						}
					?>
					<tr>
						<td style="border-top: 2px solid #000; border-bottom: double #000;" colspan="2"><b>Total Pemakaian (Intern)*</b></td>
						<td style="border-top: 2px solid #000; border-bottom: double #000;" align="right"><b><?php echo $total_pakai_internal ?></b></td>
						<td style="border-top: 2px solid #000; border-bottom: double #000;"><b>lembar</b></td>
					</tr>

					<tr>
						<td height="20"></td>
						<td></td>
						<td></td>
					</tr>

					<tr>
						<td colspan="4" align="left"><b>Pemakaian Glangsing (Ekstern) siklus <?php echo $periode_siklus?></b></td>
					</tr>
					<?php
						$number = 1;
						$total_pakai_eksternal = 0;
						foreach ($budget_eksternal as $data) {
							echo '<tr>
								<td width="15">'.$number.'</td>
								<td width="200">'.$data->nama.'</td>
								<td width="50" align="right" style="border-bottom: 1px solid #000">'.$data->jml_dipakai.'</td>
								<td>lembar</td>
							</tr>';
							$total_pakai_eksternal += $data->jml_dipakai;
							$number++;
						}
					?>
					<tr>
						<td style="border-top: 2px solid #000; border-bottom: double #000;" colspan="2"><b>Total Pemakaian (Ekstern)**</b></td>
						<td style="border-top: 2px solid #000; border-bottom: double #000;" align="right"><b><?php echo $total_pakai_eksternal ?></b></td>
						<td style="border-top: 2px solid #000; border-bottom: double #000;"><b>lembar</b></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
<style type="text/css">
	table{
		font-size : .9em;
		border-collapse: collapse;
	}

</style>
