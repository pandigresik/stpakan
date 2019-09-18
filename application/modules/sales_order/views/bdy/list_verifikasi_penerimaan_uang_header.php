<table class="table table-bordered" id="headerTable">
	<thead>
		<tr>
			<th>No. SO</th>
			<th>Tanggal SO</th>
			<th>Nama Pelanggan</th>
			<th>Term Pembayaran</th>
			<th>Total SO (Rp)</th>
			<th>Total Transfer</th>
			<th>Status</th>
			<th>Bukti</th>
			<th>Verifikasi</th>
		</tr>
	</thead>
	<tbody id="main_tbody">
		<?php		
		$str = '';
		$tmpKodeFarm = '';
		$count = 0;
		$arr = array();
		$arr_data = array(
			'N' => 'Dibuat',
			'N2' => '<b>[By System]</b> Dibatalkan',
			'U'  => 'verifikasi',
			'U2'  => '<b>[By System]</b> Dibatalkan',
			'A'  => 'verifikasi',
			'V' => 'Dibatalkan'
		);
		foreach ($list_so as $key => $val) {
			$status = '';

			$dt = array_keys(array_column($status_list, 'no_so'), $val['no_so']);
		
			foreach ($dt as $key_dt => $dt_data) {
				if($status_list[$dt_data]['status_order'] == 'N' && $status_list[$dt_data]['tgl'] != $status_list[$dt_data]['tgl_skrg']){
					$status_list[$dt_data]['status'] = 'N2';
					$status .= $arr_data[$status_list[$dt_data]['status']].' <b>'.convertElemenTglWaktuIndonesia($status_list[$dt_data]['tgl_buat']).'</b>'.'<br>';
					
				}
				elseif ($key_dt == 0 && $status_list[$dt_data]['status_order'] == 'U' && $status_list[$dt_data]['tgl'] != $status_list[$dt_data]['tgl_skrg']) {
					$status_list[$dt_data]['status'] = 'U2';
					$status .= $arr_data[$status_list[$dt_data]['status']].' <b>'.convertElemenTglWaktuIndonesia($status_list[$dt_data]['tgl_buat']).'</b>'.'<br>';
					
				}else {

					$status .= '<b>['.$status_list[$dt_data]['nama_pegawai'].']</b> '.$arr_data[$status_list[$dt_data]['status']].' <b>'.convertElemenTglWaktuIndonesia($status_list[$dt_data]['tgl_buat']).'</b>'.'<br>';
				}


			}

			$str .= '<tr data-kode_farm="'.$val['kode_farm'].'" data-harga="'.$val['harga_total'].'" data-so="'.$val['no_so'].'">';		
			$str .= '<td class="no_so" style="text-align:left">'.$val['no_so'].'</td>';
			$str .= '<td class="tgl_so">'.tglIndonesia($val['tgl_so']).'</td>';
			$str .= '<td class="nama_pelanggan">'.$val['NAMA_PELANGGAN'].'</td>';
			$str .= '<td class="term_pembayaran">'.$val['term_pembayaran'].'</td>';
			$str .= '<td class="harga_total">'.angkaRibuan($val['harga_total']).'</td>';

			$str .= '<td class="total_transfer">';
			if($val['tgl_so'] == $val['tgl_skrg'] && ($val['status_order'] == 'N')){
				$str .=	'<input type="text" name="nominal_bayar" class="form-control input-sm" data-tipe="integer" onkeyup="VerifikasiPU.checkField(this)">';
			}
			else if ($val['status_order'] == 'A' || ($val['tgl_so'] == $val['tgl_skrg'] && ($val['status_order'] == 'U'))){
				$str .= angkaRibuan($val['nominal_bayar']);
			}
			else {
				$str .= 0;
			}
			$str .= '</td>';
		
			$str .= '<td class="status">'.$status.'</td>';

			if ($val['lampiran'] == '') {
				if($val['tgl_so'] == $val['tgl_skrg']){
					$str .= '<td class="status">
						<div class="input-group attachment">
							<i class="glyphicon glyphicon-paperclip hide" onclick="VerifikasiPU.previewFile(this)"></i>
							<span class="btn btn-default btn-file input-group-addon">
								<b>...</b> <input type="file" id="file-upload" data-base64="" onchange="VerifikasiPU.selectFile(this)">
							</span>
						</div>
					</td>';
				}else {
					$str .= '<td class="status"></td>';
				}
			}else {
				$str .= '<td class="status"><a href="'.base_url().$val['lampiran'].'" target="_blank"><i class="glyphicon glyphicon-paperclip"></i> </a></td>';
			}
			
			$str .= '<td class="verifikasi">';
			if($val['status_order'] == 'N' && $val['tgl_so'] == $val['tgl_skrg']){
				$str .= '<input disabled data-status="U" type="checkbox" class="verifikasi" onclick="VerifikasiPU.check_button(this)">';
			}elseif ($val['status_order'] == 'U' && $val['tgl_so'] == $val['tgl_skrg'] && $level_user == 'KDKEU') {
				$str .= '<input data-status="A" type="checkbox" class="verifikasi" onclick="VerifikasiPU.check_button(this)">';
			}
			$str .= '</td>';
			$count++;
			$str .= '</tr>';
		}
	
		echo $str;
		?>
	</tbody>
	<tfoot>
	</tfoot>
</table>
