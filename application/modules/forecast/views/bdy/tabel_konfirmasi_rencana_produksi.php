<table class='table table-bordered custom_table konfirmasi_tabel_bdy'>
	<thead>
		<tr>
			<th rowspan="2">Tanggal Kirim</th>
			<th rowspan="2">Pakan</th>
			<th class="total_keb" colspan="2">Total <br /> Kebutuhan  <br />Farm (Sak)</th>
			<th rowspan="2"><div style="width:150px">Estimasi Tanggal Produksi</div></th>
			<th colspan="3">Rencana Produksi</th>
			<th colspan="3">Pakan Lolos QC</th>
		</tr>
		<tr>
			<th>Forecast</th>
			<th>PP</th>
			<th>Kode RP</th>
			<th>Pakan Belum Dialokasikan (sak)</th>
			<th>Kebutuhan Farm (sak)</th>

			<th>Pakan Belum Dialokasikan (sak)</th>
			<th>Kebutuhan Farm (sak)</th>
			<th>Total Kebutuhan Farm (sak)</th>
		</tr>
	</thead>
	<tbody>
		<?php

		$plot_pakan = array();
		$plot_pakan_lolos = array();

		if(!empty($kp)){
			foreach($kp as $tk => $tk_arr){
				$bisaInput = 0;
				$paramKirimDate = new \DateTime($tk);
				$maxEntryDate = new \DateTime($tk);
				$sekarangDate = new \DateTime($hari_ini);
				/* parameter maximal input, 3 hari kerja */
				$hari_kerja = 3;
				while($hari_kerja > 0){
					$paramKirimDate->sub(new \DateInterval('P1D'));
					$workday = new \DateTime(hari_kerja_terdekat($paramKirimDate->format('Y-m-d'),$hari_libur));
					if($workday->format('Y-m-d') == $paramKirimDate->format('Y-m-d')){
							$hari_kerja--;
					}
				}



				$maxEntryDate->sub(new \DateInterval('P4D'));
		//		if($sekarangDate >= $minEntryDate && $sekarangDate <= $maxEntryDate){
				if($sekarangDate <= $maxEntryDate){
					$bisaInput = 1;
				}

				echo '<tr data-tgl_kirim="'.$tk.'">';
				echo '<td class="tgl_kirim" data-tgl_kirim="'.$tk.'" data-rowspan="'.$rowspan[$tk]['rowspan'].'" rowspan="'.$rowspan[$tk]['rowspan'].'">'.tglIndonesia($tk,'-',' ').'</td>';
				$i = 0;
				foreach($tk_arr as $p => $p_arr){

					if($i > 0){
						echo '<tr data-tgl_kirim="'.$tk.'">';
					}
					$fixpp = $workday < $sekarangDate ? 1 : 0;
					$maxInput = $workday < $sekarangDate ? $p_arr['header']['pp'] : $p_arr['header']['jml'];
					$tooltip = '<span class="tooltip_bdy"><table class="table table-bordered table-stripped konfirmasi_tabel_bdy"><thead><tr><th rowspan="2">Farm</th><th colspan="2">Jumlah Kebutuhan Sak</th></tr><tr><th>Forecast</th><th>PP</th></tr></thead><tbody>'.implode('',$p_arr['header']['tooltip']).'</tbody></table></span>';
					$class_pp = $fixpp ? ($p_arr['header']['jml'] != $p_arr['header']['pp'] ? 'abang' : 'biru') : '';
					$nama_pakan = $p_arr['header']['nama_pakan'];
					echo '<td data-kode_barang="'.$p.'" data-tgl_kirim="'.$tk.'" data-rowspan="'.$rowspan[$tk][$p]['rowspan'].'" rowspan="'.$rowspan[$tk][$p]['rowspan'].'" class="has-tooltip_bdy nama_pakan">'.$nama_pakan.$tooltip.'</td>';
					echo '<td data-kode_barang="'.$p.'" data-tgl_kirim="'.$tk.'" class="number total_keb" data-rowspan="'.$rowspan[$tk][$p]['rowspan'].'" rowspan="'.$rowspan[$tk][$p]['rowspan'].'">'.(!empty($p_arr['header']['jml']) ? angkaRibuan($p_arr['header']['jml']) : '-').'</td>';
					echo '<td data-kode_barang="'.$p.'" data-tgl_kirim="'.$tk.'" class="number total_pp '.$class_pp.'" data-rowspan="'.$rowspan[$tk][$p]['rowspan'].'" rowspan="'.$rowspan[$tk][$p]['rowspan'].'">'.(empty($p_arr['header']['pp']) && $fixpp ? '-' : angkaRibuan($p_arr['header']['pp'])).'</td>';
					if(!empty($p_arr['detail'])){
						$_z = 0;
						$_tambah_baris = '';
						$arr_terakhir = end($p_arr['detail']);
						$_class_tambah_baris = null;
						$_total_keb_farm = 0;
						$inputFilterArr = array();
						$adaRp = 0;
						foreach($p_arr['detail'] as $d){
						//	$class_revisi_rp = empty($d['alokasi_pakan_untuk_farm']) && !empty($d['alokasi_pakan_lolos_untuk_farm']) ? 'bg_orange' : '';
						$class_revisi_rp = '';
							if($_z > 0){
								echo '<tr class="'.$class_revisi_rp.'" data-tgl_kirim="'.$tk.'">';
							}
							$krp = $d['kode_rencana_produksi'];
							if(!isset($plot_pakan[$krp])){
								$plot_pakan[$krp] = array();
							}
							if(!isset($plot_pakan[$krp][$p])){
								$plot_pakan[$krp][$p] = 0;
								if(isset($plot_sebelumnya[$krp])){
									if(isset($plot_sebelumnya[$krp][$p])){
										$plot_pakan[$krp][$p] = $plot_sebelumnya[$krp][$p]['alokasi_pakan'];
									}
								}
							}

							if(!isset($plot_pakan_lolos[$krp])){
								$plot_pakan_lolos[$krp] = array();
							}
							if(!isset($plot_pakan_lolos[$krp][$p])){
								$plot_pakan_lolos[$krp][$p] = 0;
								if(isset($plot_sebelumnya[$krp])){
									if(isset($plot_sebelumnya[$krp][$p])){
										$plot_pakan_lolos[$krp][$p] = $plot_sebelumnya[$krp][$p]['alokasi_pakan_lolos'];
									}
								}
							}
							if(!empty($d['kode_rencana_produksi'])){
									$adaRp++;
							}

							$tgl_produksi = !empty($d['tanggal_produksi_estimasi']) ? $d['tanggal_produksi_estimasi'] : $d['tanggal_produksi'];
							$dari_tambah_rp = empty($d['tanggal_produksi_estimasi']) ? 'bg_orange' : '';
							$kode_rp = empty($d['kode_rencana_produksi']) ? (isset($list_rp[$p][$tgl_produksi]) && !empty($list_rp[$p][$tgl_produksi])  ? dropdownRencanaProduksi($list_rp[$p][$tgl_produksi],'kode_rencana_produksi','Konfirmasi_rp.pilih_rencana_produksi(this)'): '-'): '<span class="link_span" onclick="Konfirmasi_rp.detail_rencana_produksi(this)">'.$d['kode_rencana_produksi'].'</span>';
							$keb_farm = empty($d['alokasi_pakan_untuk_farm']) ? (isset($list_rp[$p][$tgl_produksi]) && is_null($d['alokasi_pakan_lolos_untuk_farm']) ? '<input type="text" data-nilai_lama="0" name="alokasi_pakan_untuk_farm" class="numeric" onchange="Konfirmasi_rp.periksa_alokasi_farm(this)"/>' : '-'): angkaRibuan($d['alokasi_pakan_untuk_farm']);

							$total_produksi = empty($d['total_produksi']) ? '-': angkaRibuan($d['total_produksi'] - $plot_pakan[$krp][$p]);
							$hasil_produksi = empty($d['total_pakan_lolos']) ? (is_null($d['total_pakan_lolos']) ? '-' : $d['total_pakan_lolos'] - $plot_pakan_lolos[$krp][$p] ) : angkaRibuan($d['total_pakan_lolos'] - $plot_pakan_lolos[$krp][$p]);
							$keb_lolos_pakan = is_null($d['alokasi_pakan_lolos_untuk_farm']) ? (!empty($d['total_pakan_lolos']) ? '<input type="text" name="alokasi_pakan_lolos_untuk_farm" class="numeric" onchange="Konfirmasi_rp.periksa_alokasi_lolos_farm(this)"/>' : '-'): ((!empty($p_arr['header']['pp']) && $fixpp && ($p_arr['header']['pp'] > $maxInput)) ? '<input type="text" name="alokasi_pakan_lolos_untuk_farm" class="numeric" onchange="Konfirmasi_rp.periksa_alokasi_lolos_farm(this)" data-nilaidb="'.$d['alokasi_pakan_lolos_untuk_farm'].'" value="'.angkaRibuan($d['alokasi_pakan_lolos_untuk_farm']).'"/>':angkaRibuan($d['alokasi_pakan_lolos_untuk_farm']));
							if($fixpp){
								if($total_alokasi[$tk][$p]['jml'] > $maxInput){
									$keb_lolos_pakan = is_null($d['alokasi_pakan_lolos_untuk_farm']) ? '-' : '<input type="text" name="alokasi_pakan_lolos_untuk_farm" class="numeric input_edit" onchange="Konfirmasi_rp.periksa_alokasi_lolos_farm(this)" data-nilaidb="'.$d['alokasi_pakan_lolos_untuk_farm'].'" value="'.angkaRibuan($d['alokasi_pakan_lolos_untuk_farm']).'"/>';
								}
							/*else {
									$keb_lolos_pakan = is_null($d['alokasi_pakan_lolos_untuk_farm']) ? '-' : angkaRibuan($d['alokasi_pakan_lolos_untuk_farm']);
								}
							*/
							}
					/*		if(!empty($p_arr['header']['pp']) && ($p_arr['header']['pp'] < $p_arr['header']['jml'])){
								$keb_lolos_pakan =
							}
							*/
							$total_lolos_pakan = !empty($d['total_pakan_lolos']) ? $d['total_pakan_lolos'] : 0 ;
							$_total_keb_farm += $d['alokasi_pakan_untuk_farm'];

							$plot_pakan[$krp][$p] += $d['alokasi_pakan_untuk_farm'];
							$plot_pakan_lolos[$krp][$p] += $d['alokasi_pakan_lolos_untuk_farm'];

							$inputFilter = '';
							if(empty($d['kode_rencana_produksi'])){
								$inputFilter = 'input_rencana_produksi';
							}
							else if(empty($d['alokasi_pakan_lolos_untuk_farm'])){
								$inputFilter = 'input_kelolosan_pakan';
							}
							array_push($inputFilterArr,$inputFilter);
							/* tambahkan tombol untuk menambah baris */
							if(empty($_class_tambah_baris) && $kode_rp != '-'){
									$_class_tambah_baris = 'disabled';
							}
							if($arr_terakhir == $d){
								$_tambah_baris = '&nbsp;<i onclick="Konfirmasi_rp.addEstimasiRencanaProduksi(this)" class="glyphicon glyphicon-plus-sign pull-right '.$_class_tambah_baris.'"></i>';
							}

							echo '<td data-tgl_kirim="'.$tk.'" data-inputFilter="'.$inputFilter.'" data-fixpp="'.$fixpp.'"  data-nama_pakan ="'.$nama_pakan.'" data-kode_barang="'.$p.'" data-max-input="'.$maxInput.'" data-rencana_kirim="'.$d['rencana_kirim'].'" data-id_hasil_produksi="'.$d['id_hasil_produksi'].'" class="tgl_produksi data_pakan '.$dari_tambah_rp.'">'.tglIndonesia($tgl_produksi,'-',' ').$_tambah_baris.'</td>
								<td class="kode_rencana_produksi">'.$kode_rp.'</td>
								<td class="number total_produksi">'.$total_produksi.'</td>
								<td class="number alokasi_farm">'.$keb_farm.'</td>
								<td class="number hasil_produksi" data-total_lolos_pakan="'.$total_lolos_pakan.'">'.$hasil_produksi.'</td>
								<td class="number alokasi_lolos_farm">'.$keb_lolos_pakan.'</td>';
							if($_z == 0){
								$str_total_alokasi = $total_alokasi[$tk][$p]['revisi'] > 1 ? '<span onclick="Konfirmasi_rp.riwayatAlokasiPakan(this,\''.$tk.'\',\''.$p.'\')" class="link_span">'.angkaRibuan($total_alokasi[$tk][$p]['jml']).'</span>' : angkaRibuan($total_alokasi[$tk][$p]['jml']);
						//		$str_total_alokasi = $total_alokasi[$tk][$p] > $maxInput ? '<span onclick="Konfirmasi_rp.riwayatAlokasiPakan(this,\''.$tk.'\',\''.$p.'\')" class="link_span">'.$total_alokasi[$tk][$p].'</span>' : $total_alokasi[$tk][$p];
								echo '<td class="number lolos_pakan" data-kode_barang="'.$p.'" data-tgl_kirim="'.$tk.'" data-rowspan="'.($rowspan[$tk][$p]['rowspan'] - 1).'" rowspan="'.($rowspan[$tk][$p]['rowspan'] - 1).'">'.$str_total_alokasi.'</td>';
							}
							if($_z > 0){
								echo '</tr>';
							}
							$_z++;
						}
						/* tambahkan satu baris untuk input revisi rp */
						$kekurangan = $maxInput - $total_alokasi[$tk][$p]['jml'];
						$info_pemenuhan = $kekurangan > 0 ? '<span class="'.$class_pp.'">Jumlah Pakan Lolos QC masih kurang '.angkaRibuan($kekurangan).' sak.</span>' : '';
						$revisi_rp = 'disabled';
						if($total_alokasi[$tk][$p]['jml'] < $maxInput ){
							if($adaRp){
									$revisi_rp = '';
							}
						}
						echo '</tr>';
						echo '<tr data-max-input="'.$maxInput.'" data-tgl_kirim="'.$tk.'" data-kode_barang="'.$p.'">';
						echo '<td data-inputFilter="'.implode(' ',$inputFilterArr).'" colspan="2"><span class="col-md-12 btn btn-default '.$revisi_rp.'" onclick="Konfirmasi_rp.inputRevisi(this)">Tambah RP</span></td>
								<td style="text-align:center" colspan="5">'.$info_pemenuhan.'</td>
								';
						echo '</tr>';
					}
					else{
						if($bisaInput){
							$inputElm = '<input class="col-md-10" type="text" name="tglproduksi" readonly/>&nbsp;<i onclick="Konfirmasi_rp.removeTgl(this)" class="glyphicon glyphicon-remove-circle pull-right"></i>';
							$tombol_tambah_RP = '';
							echo '<td data-inputFilter="input_tanggal_produksi" class="estimasi_tglproduksi data_pakan" data-tgl_kirim="'.$tk.'" data-nama_pakan ="'.$nama_pakan.'"  data-kode_barang="'.$p.'">'.$inputElm.'</td>
									<td class="kode_rencana_produksi"></td>
									<td></td>
									<td class="rencana_kebutuhan_farm"></td>
									<td class="kelolosan_kebutuhan_farm"></td>
									<td></td>
									<td class="lolos_pakan"></td>';
						}
						else{
							$inputElm = 'Tidak ada Estimasi Tanggal Produksi yang dapat dipilih, gunakan Tambah RP.';
							$kekurangan = $maxInput - (isset($total_alokasi[$tk][$p]['jml']) ? $total_alokasi[$tk][$p]['jml'] : 0);
							$info_pemenuhan = $kekurangan > 0 ? '<span class="'.$class_pp.'">Jumlah Pakan Lolos QC masih kurang '.angkaRibuan($kekurangan).' sak.</span>' : '';
							$tombol_tambah_RP = '<tr data-max-input="'.$maxInput.'" data-tgl_kirim="'.$tk.'" data-kode_barang="'.$p.'">
											<td data-inputFilter="" colspan="2"><span class="col-md-12 btn btn-default" onclick="Konfirmasi_rp.inputRevisi(this)">Tambah RP</span></td>
											<td style="text-align:center" colspan="5">'.$info_pemenuhan.'</td>
											</tr>';
							echo '<td colspan="6" data-inputFilter="" class="estimasi_tglproduksi data_pakan text-center"  data-nama_pakan ="'.$nama_pakan.'" data-tgl_kirim="'.$tk.'" data-kode_barang="'.$p.'">'.$inputElm.'</td>
									<td class="lolos_pakan"></td>';
						}
					echo $tombol_tambah_RP;
					}
						if($i > 0){
						echo '</tr>';

					}
					$i++;
				}
				echo '</tr>';
			}
		}
		?>
	</tbody>
</table>
