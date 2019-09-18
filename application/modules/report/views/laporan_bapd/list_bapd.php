<?php if(isset($list_bapd)){ ?> <!--begin isset $list_bapd-->

<style>
	.tbl_bapd{margin-top:-20px;}
	.tbl_bapd thead tr{background:#dfdfdf;}
	.bapd-header .ph-title{
		padding:8px 15px;
		margin:0;
	}
	.bapd-header .ph-title-bottom{
		border-top:1px solid #fff;
	}
</style>

			<?php foreach($list_bapd as $_kf => $val){ ?>
				<div class="page-header bapd-header bg-primary">
					<p class="ph-title">Farm <?=$nama_farm[$_kf]?></p>
					<p id="fsiklus" class="ph-title ph-title-bottom farm-siklus" data-kodefarm="<?=$_kf?>">
						Farm <?=$nama_farm[$_kf]?> 
						periode siklus <?=$periode_siklus[$_kf]?>
					</p>
				</div>
			<?php
						echo '<table class="table table-striped  custom_table tbl_bapd">';
						echo '<thead>';
							echo '<tr>';
								echo '<th rowspan="2">No. Reg</th>';
								echo '<th rowspan="2">Kandang</th>';
								echo '<th rowspan="2">Hatchery</th>';
								echo '<th rowspan="2">Tanggal<br>DOC In</th>';
								echo '<th rowspan="2">Box</th>';
								echo '<th rowspan="2">Ekor</th>';
								echo '<th rowspan="2">Afkir</th>';
								echo '<th rowspan="2">Stok Awal</th>';
								echo '<th rowspan="2">BB<br>rata-rata</th>';
								echo '<th rowspan="2">Uniformity</th>';
								echo '<th colspan="2">Tindak Lanjut</th>';
							echo '</tr>';
							echo '<tr>';
								echo '<th>Pengawas Kandang</th>';
								echo '<th>Kepala Farm</th>';
							echo '</tr>';
						echo '</thead>';
						echo '<tbody>';
						foreach($val as $baris){
							$jml_box 	= $box[$_kf][$baris['no_reg']];
							//if($jml_box != 'Nan'){
							$jml_ekor 	= $jml_box * 102;
							$jml_afkir	= $afkir[$_kf][$baris['no_reg']][0]->JML_AFKIR + $baris['jml_afkir'];
							$stok_awal 	= $jml_ekor - $jml_afkir;
							$tglreview 	= (!empty($baris['tindaklanjutpengawas'])) ? convertElemenTglWaktuIndonesia($baris['tindaklanjutpengawas']) :  ($baris['status'] == 'N' && $user_level == 'P' ? $tombol : '');
							$tglapprove = (!empty($baris['tindaklanjutkafarm'])) ? convertElemenTglWaktuIndonesia($baris['tindaklanjutkafarm']) : ($baris['status'] == 'RV' && $user_level == 'KF' ? $tombol : '');
							$tgldocin 	= convertElemenTglIndonesia($baris['tgl_doc_in']);
							echo '<tr>';
								echo '<td class="noreg">'.$baris['no_reg'].'</td>';
								echo '<td class="kandang">'.$baris['kode_kandang'].'</td>';
								echo '<td class="hatchery">'.$baris['nama_hatchery'].'</td>';
								echo '<td class="tanggal">'.$tgldocin.'</td>';
								echo '<td>'.$jml_box.'</td>';
								echo '<td>'.angkaRibuan($jml_ekor).'</td>';
								echo '<td>'.$jml_afkir.'</td>';
								echo '<td>'.angkaRibuan($stok_awal).'</td>';
								echo '<td>'.str_replace('.', ',', $baris['bb_rata2']).'</td>';
								echo '<td>'.str_replace('.', ',', $baris['uniformity']).'</td>';
								echo '<td class="tanggal">'.$tglreview.'</td>';
								echo '<td class="tanggal">'.$tglapprove.'</td>';
							echo '</tr>';
							echo '<tr class="detailbapdoc" style="display:none">';
								echo '<td></td>';
								echo '<td class="tddetail" colspan="5"></td>';
								echo '<td></td>';
							echo '<tr>';
							//}
						}
						echo '</tbody>';
						echo '</table>';
					}
				?>

<?php } ?> <!--end isset $list_bapd-->