<div class="panel panel-primary">
	<div class="panel-heading">Daftar Permintaan Sak </div>
	<div class="panel-body">
		<!--
		<table class="table table-bordered custom_table list_permintaan">
		-->
		<table class="table table-bordered custom_table">
			<thead>
				<tr>
					<th rowspan="2">No. Permintaan Sak</th>
					<th rowspan="2">Tgl Kebutuhan</th>
					<th rowspan="2">Kebutuhan</th>
					<th rowspan="2">Jml Sak Diminta</th>
					<th colspan="2">Over Budget</th>
					<th rowspan="2">Keterangan</th>
				</tr>
				<tr>
					<th>Jml Sak</th>
					<th>Alasan</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!empty($list_permintaan)){
					$str = '';
					$no_ppsk = '';
					$status = '';
					//cetak_r($list_permintaan);
					foreach($list_permintaan as $minta){
						if ($no_ppsk != $minta['no_ppsk']){
							if($no_ppsk != ''){
								$str = str_replace('~~status~~',$status, $str);
								$str . '</td></tr>';
								$status = '';
							}
							$str .= '<tr >
								<td><span class="link_span" data-kode_budget="'.$minta['kode_budget'].'" data-kode_siklus="'.$minta['kode_siklus'].'" data-status="~~status~~" data-no_ppsk="'.$minta['no_ppsk'].'" data-kode_farm="'.$minta['kode_farm'].'" onclick="permintaanSak.editView(this)">'.$minta['no_ppsk'].'</span></td>
								<td class="tgl_kebutuhan">'.convertElemenTglIndonesia($minta['tgl_kebutuhan']).'</td>
								<td class="kebutuhan">'.$minta['nama_budget'].'</td>
								<td>'.$minta['jml_diminta'].'</td>
								<td>'.$minta['jml_over_budget'].'</td>
								<td>'.$minta['alasan'].'</td>
								<td class="keterangan">';
							$no_ppsk = $minta['no_ppsk'];
							$str .= '<div style="text-align:left;">['.$minta['nama_pegawai'].'] - '.$minta['status_detail'].', '.convertElemenTglWaktuIndonesia($minta['tgl_buat'], true).'</div>';
							$str .= ($minta['keterangan'] != '') ? '<div style="text-align:left;color:#ff0000">('.$minta['keterangan'].')</div>' : '';
							$status = $minta['status'];
						}
						else{
							$str .= '<div style="text-align:left;">['.$minta['nama_pegawai'].'] - '.$minta['status_detail'].', '.convertElemenTglWaktuIndonesia($minta['tgl_buat'], true).'</div>';
							$str .= ($minta['keterangan'] != '') ? '<div style="text-align:left;color:#ff0000">('.$minta['keterangan'].')</div>' : '';
							$status = $minta['status'];
						}
						/*echo '<tr >
							<td><span class="link_span" data-no_ppsk="'.$minta['no_ppsk'].'" data-kode_farm="'.$minta['kode_farm'].'" onclick="permintaanSak.editView(this)">'.$minta['no_ppsk'].'</span></td>
							<td class="tgl_kebutuhan">'.$minta['tgl_kebutuhan'].'</td>
							<td class="kebutuhan">'.$minta['nama_budget'].'</td>
							<td>'.$minta['jml_diminta'].'</td>
							<td>'.$minta['jml_over_budget'].'</td>
							<td>'.$minta['alasan'].'</td>
							<td>'.$minta['status_detail'].'</td>
						</tr>';*/
					}
					$str = str_replace('~~status~~',$status, $str);
					$str . '</td></tr>';
					echo $str;
				}
				else{
					echo '<tr><td colspan="7">Data tidak ditemukan</td></tr>';
				}
				 ?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>
