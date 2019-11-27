<?php 
$breadcumb_siklus = generateBreadcumb(array('Kabag.Admin Budidaya' ,'Kadept PI','Kadiv Budidaya'));
$breadcumb_budget = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadept/Wakadept Admin Budidaya'));					
$breadcumb_plotting = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadiv Budidaya'));					

?>
<div class="table-responsive">
	<table class="table table-bordered custom_table detail_perencanaan_awal">
		<thead>
			<tr>
				<th colspan=3>Aktivasi Siklus<?php echo $breadcumb_siklus ?></th>
				<th colspan=2>Aktivasi Budget<?php echo $breadcumb_budget ?></th>
				<th colspan=2>Plotting Pelaksana<?php echo $breadcumb_plotting ?></th>
				<th colspan=2>Tara Timbang / Siklus</th>
			</tr>
			<tr>
				<th>Siklus Sebelumnya</th>
				<th>Status</th>
				<th>Keterangan</th>
				
				<th>Status</th>
				<th>Keterangan</th>
				
				<th>Status</th>
				<th>Keterangan</th>
				
				<th>Pallet</th>
				<th>Hand Pallet</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if(!empty($aktivasi_siklus)){
				$status_str = array(
					'D' => 'Dibuat',
					'N' => 'Dirilis',
					'R' => 'Dikoreksi',
					'RV' => 'Dikoreksi',
					'RJ' => 'Ditolak',
					'A' => 'Disetujui'
				);
				$keterangan_budget = array();
				$keterangan_ploting = array();
				$status_budget = '';
				$status_ploting = '';
				$status_ploting_btn = '';
				$keterangan_aktivasi = array();
				$aktivasi_akhir = $aktivasi_siklus[0];
				$status_aktivasi = $aktivasi_akhir['last_state'] == 'P2' ? '<div class="btnApproval"><span class="btn btn-default" data-flok_bdy="'.$flok_bdy.'" data-kode_siklus="'.$kode_siklus.'" onclick="Approval.approveSiklus(this)">Approve</span> &nbsp; <span  data-flok_bdy="'.$flok_bdy.'" data-kode_siklus="'.$kode_siklus.'" onclick="Approval.rejectSiklus(this)" class="btn btn-default">Reject</span></div>' : $aktivasi_akhir['keterangan_last_state'];
				
				foreach($aktivasi_siklus as $as){
					array_push($keterangan_aktivasi,'<div>'.$as['NAMA_PEGAWAI'].' - '.$as['keterangan_state'].', '.convertElemenTglWaktuIndonesia($as['stamp']).'</div>');
				}
				if(!empty($logPengajuanBudget)){
					$tmp_status_budget = '';
					foreach($logPengajuanBudget as $as){
						array_push($keterangan_budget,'<div>'.$as['NAMA_PEGAWAI'].' - '.$status_str[$as['STATUS']].', '.convertElemenTglWaktuIndonesia($as['TGL_BUAT']).'</div>');
						if(empty($tmp_status_budget)) {
							$tmp_status_budget = $as['STATUS'];
						} 
					}
					$status_budget = convertKode('status_approve',$tmp_status_budget);
				}
				if(!empty($status_plotting_pelaksana)){
					
					$status_ploting = $status_plotting_pelaksana['STATUS'];
					$status_ploting_btn = $status_ploting == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-flok_bdy="'.$flok_bdy.'" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-url="'.site_url('report/overview/detailPlottingPelaksana').'" onclick="Approval.prosesPloting(this)">Proses</span></div>' : convertKode('status_approve',$status_ploting);
					
					if(!empty($status_plotting_pelaksana['USER_ACK'])){
						array_push($keterangan_ploting,'<div>'.$status_plotting_pelaksana['USER_ACK'].' - '.$status_str['A'].', '.convertElemenTglWaktuIndonesia($status_plotting_pelaksana['TGL_ACK']).'</div>');
					}
					
					if(!empty($status_plotting_pelaksana['USER_REVIEW'])){
						array_push($keterangan_ploting,'<div>'.$status_plotting_pelaksana['USER_REVIEW'].' - '.$status_str['RV'].', '.convertElemenTglWaktuIndonesia($status_plotting_pelaksana['TGL_REVIEW']).'</div>');
					}

					array_push($keterangan_ploting,'<div>'.$status_plotting_pelaksana['USER_BUAT'].' - '.$status_str['N'].', '.convertElemenTglWaktuIndonesia($status_plotting_pelaksana['TGL_BUAT']).'</div>');
					
				}
				$timbang_pallet = '';
				$timbang_hand_pallet = '';
				if(!empty($status_timbang)){
					if(isset($status_timbang['pallet'])){
						if($status_timbang['pallet']['jumlah'] > 0){
							$timbang_pallet = '<span class="link_span" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-jenis="pallet" onclick="KSP.detailTimbang(this)">DONE</span>';
						}
					}
					if(isset($status_timbang['hand_pallet'])){
						if($status_timbang['hand_pallet']['jumlah'] > 0){
							$timbang_hand_pallet = '<span class="link_span" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-jenis="hand_pallet" onclick="KSP.detailTimbang(this)">DONE</span>';
						}
					}
				}
				echo '<tr>';
				echo '<td class="'.($aktivasi_akhir['status_tutup_siklus_sebelumnya'] == 'A' ? 'abang' : '').'">'.$aktivasi_akhir['siklus_sebelumnya'].'</td>';
				echo '<td>'.$status_aktivasi.'</td>';
				echo '<td>'.implode('',$keterangan_aktivasi).'</td>';
				echo '<td>'.$status_budget.'</td>';
				echo '<td>'.implode('',$keterangan_budget).'</td>';
				echo '<td>'.$status_ploting_btn.'</td>';
				echo '<td>'.implode('',$keterangan_ploting).'</td>';
				echo '<td>'.$timbang_pallet.'</td>';
				echo '<td>'.$timbang_hand_pallet.'</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td colspan="3" style="vertical-align:top">'.$tbl_detail_rencana_pengiriman.'</td>';
				echo '<td colspan="2" style="vertical-align:top">'.$tbl_detail_pengajuan_budget.'</td>';
				echo '<td colspan="2" style="vertical-align:top">'.$tbl_detail_plotting_pelaksana.'</td>';
				echo '<td colspan="2"></td>';
				echo '</tr>';
			}
			?>
		</tbody>
	</table>	
</div>


