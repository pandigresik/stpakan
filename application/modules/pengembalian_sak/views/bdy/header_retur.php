<table class="table table-bordered custom_table">
	<thead>
		<tr>
			<th>
				<div class="right-inner-addon ">
                   	<i class="glyphicon glyphicon-search"></i>
                   	<input type="search" onchange="Approval.filter_content(this)" placeholder="Search" name="no_pengembalian" class="form-control ">
                </div>
            </th>
            <th>
				<div class="right-inner-addon ">
                   	<i class="glyphicon glyphicon-search"></i>
                   	<input type="search" onchange="Approval.filter_content(this)" placeholder="Search" name="no_pengembalian" class="form-control ">
                </div>
            </th>
            <th colspan="7">LHK</th>
		</tr>
		<tr>
			<th>No. Retur</th>
			<th>Kandang</th>
			<th>Tanggal/Jam Retur</th>
			<th>Tanggal LHK</th>
			<th>Kirim Pakan (Sak)</th>
			<th>Target Retur (Sak)</th>
			<th>Jumlah Retur (Sak)</th>
			<th>Hutang Retur Sak Kosong</th>
			<th>Pengiriman Pakan</th>
			<th>Tanggal/Jam Review Kadept</th>
			<th>Tanggal/Jam Review Kadiv</th>
			<th>Sisa Hutang</th>
		</tr>
	</thead>
	<tbody>
 
		<?php 
		
			$pengiriman_str = array('A' => 'NORMAL', 'R' => 'KURANGI');
			$aktif_btn = array();
			if(!empty($list_retur)){
				foreach($list_retur as $retur){
					$class_keputusan = $retur['keputusan'] == 'R' ? 'abang' : '';
					$pengiriman = '';
					$noreg = $retur['no_reg'];
					if(!empty($retur['keputusan'])){
						$pengiriman = $pengiriman_str[$retur['keputusan']]; 		
					}
					if($retur['aktif']){
						$aktif = 1;
						if($sisa_hutang[$noreg] == 0){
							$aktif = 0;
						}
					}
					else{
						$aktif = 0;						
					}
		
					echo '<tr onclick="Approval.showdetailretur(this)" data-aktif="'.$aktif.'" data-id_retur="'.$retur['id'].'" data-no_retur="'.$retur['no_retur'].'" data-keputusan="'.$retur['keputusan'].'" data-reviewkadept="'.$retur['tgl_review_kadept'].'">
						<td>RS/'.$retur['no_retur'].'</td>
						<td>'.$retur['nama_kandang'].'</td>
						<td>'.convertElemenTglWaktuIndonesia($retur['tgl_buat']).'</td>
						<td>'.convertElemenTglWaktuIndonesia($retur['tgl_rhk']).'</td>
						<td class="number">'.$retur['jml_kirim'].'</td>
						<td class="number">'.$retur['jml_pakai'].'</td>						
						<td class="number">'.$retur['jml_retur'].'</td>
						<td class="number">'.$retur['hutang'].'</td>
                   		<td class="pengiriman">'.$pengiriman.'</td>
						<td class="waktureview">'.convertElemenTglWaktuIndonesia($retur['tgl_review_kadept']).'</td>	
						<td class="waktu '.$class_keputusan.'">'.convertElemenTglWaktuIndonesia($retur['waktu']).'</td>
						<td class="bg_orange">'.$sisa_hutang[$noreg].'</td>
					</tr>';
				}
			}
		?>
	</tbody>
</table>