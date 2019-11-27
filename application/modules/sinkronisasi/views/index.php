<div class="col-md-4 col-md-offset-4 text-center"></div>
<div>
	<div class="new-line">
	<div class="col-md-4">		
		<div class="btn btn-primary" onclick="Sinc.sinkron(this)">Sinkronkan Sekarang</div>
		&nbsp;
		<div class="btn btn-danger" onclick="Sinc.sinkron2(this)">Sinkronkan (Cadangan)</div>
	</div>	
	<div class="col-md-8">
		<label class="col-md-3">Jumlah data yang ditampilkan </label>
		<div class="col-md-2">
			<select name="limit_data" onchange="Sinc.refresh()" class="form-control">		
				<?php 
					if(!empty($list_option)){
						foreach($list_option as $lo){
							$selected = $lo == $limit ? 'selected' : '';
							echo '<option '.$selected.'>'.$lo.'</option>';
						}
					}
				?>								
			</select>
		</div>
		<div class="col-md-5 pull-right div_cari">
			<div class="col-md-10">
				<input type="text" class="form-control" value="<?php echo $search ?>" name="input_cari" placeholder="kunci pencarian"/>
			</div>
			<div class="col-md-2"><span class="btn btn-default" onclick="Sinc.search(this)">Cari</span></div>
		</div>
		
	</div>
	<div id="info_sinkron" class="new-line container col-md-12"></div>
	</div>
	<div class="row col-md-12 new-line">
		<div class="sinkronisasi">
			<?php
			if(!empty($data)){
				echo '<table class="table table-bordered">';
				echo '<thead>
					<tr>
						<td>Id</td>
						<td>Id Ref</td>
						<td>Transaksi</td>
						<td>Asal</td>
						<td>Tujuan</td>
						<td>Aksi</td>
						<td>Tanggal Sinkronisasi</td>
						<td>Tanggal Buat</td>
					</tr>
				</thead>';
				echo '<tbody>';
				foreach($data as $tr){
					echo '<tr>';
					foreach($tr as $kc => $td){
						if($kc == 'transaksi'){
							echo '<td class="'.$kc.'"><span class="link_span" onclick="Sinc.detailSinkron(this)">'.$td.'</span></td>';
						}else{
							echo '<td class="'.$kc.'">'.$td.'</td>';
						}
					}
					echo '</tr>';
				}
				echo '</tbody>';
				echo '</table>';
			}
			else{
				echo '<div>Tidak ada data dari client yang perlu dikirim</div>';
			}

			?>
		</div>
	</div>
</div>

 <script type="text/javascript" src="assets/js/sinkronisasi/proses.js"></script>
