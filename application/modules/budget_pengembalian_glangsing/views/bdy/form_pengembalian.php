<button onclick="save_budget('D')" class="btn btn-default" id="save_budget" style="display:none">Simpan</button>
<button onclick="save_budget('N')" class="btn btn-default" id="release_budget" style="display:none">Rilis</button>
<button onclick="save_budget('C')" class="btn btn-default" id="close_budget" style="display:none">Tutup Budget</button>
<button onclick="save_budget('R')" class="btn btn-default" id="review_budget" style="display:none">Approve</button>
<button onclick="save_budget('A')" class="btn btn-default" id="approve_budget" style="display:none">Approve</button>
<button onclick="save_budget('RJ')" class="btn btn-default" id="reject_budget" style="display:none">Reject</button>
<button onclick="print_budget()" class="btn btn-default" id="print_budget" style="display:none">Cetak laporan Glangsing</button>

<div class="text-danger" id="pesan_keterlambatan"></div>
<form method="post" id="fm" name="fm">
	<?php
	$count = 0;
	$val = 0;
	$count2 = 0;
	$val2 = 0;
	if(isset($internal_data)){?>
		<table id="internal_budget">
			<thead>
				<th></th>
				<th>Internal:</th>
				<th></th>
				<th></th>
			</thead>
			<tbody>
				<?php
					//cetak_r($internal_data);
					foreach ($internal_data as $key => $value) {?>
					<tr>
						<td><?php echo $key+1?></td>
						<td id='td_nama_budget'><?php echo $value->nama_budget?></td>
						<td id='td_jumlah_glangsing'>
							<input class='form-control' readonly id='tf_internal<?php echo $key?>' name='tf_budget_val[]' onkeyup='hitung_budget(this)' data-value="<?php echo $value->value?>" value=<?php echo $value->value?> type='text'>
						 	<input id='tf_budget_internal<?php echo $key?>' name='tf_budget_name[]' value='<?php echo $value->kode_budget?>' type='hidden'>
						</td>
						<td>lembar</td>
					</tr>
				<?php
					$count++;
					$val += $value->value;
				}?>
				 <tr style='border-top:3px solid'>
		 			 <td></td>
		 			 <td id='td_nama_budget'><strong>Total Pemakaian (Internal)</strong></td>
		 			 <td id='td_total_internal'><input readonly class='form-control' id='total_internal' name='total_internal' value='<?php echo $val?>' type='text'></td>
		 			 <td>lembar</td>
				 </tr>
			</tbody>
		</table>
		<br><br>
	<?php  }?>
	<?php if(isset($eksternal_data)){?>
		<table id="eksternal_budget">
			<thead>
				<th></th>
				<th>Eksternal:</th>
				<th></th>
				<th></th>
			</thead>
			<tbody>
				<?php

					foreach ($eksternal_data as $key => $value) {?>
					<tr>
						<td><?php echo $key+1?></td>
						<td id='td_nama_budget'><?php echo $value->nama_budget?></td>
						<td id='td_jumlah_glangsing'>
							<input class='form-control' readonly id='tf_eksternal<?php echo $key?>' name='tf_budget_val[]' onkeyup='hitung_budget(this)' data-value="<?php echo $value->value?>" value=<?php echo $value->value?> type='text'>
						 	<input id='tf_budget_eksternal<?php echo $key?>' name='tf_budget_name[]' value='<?php echo $value->kode_budget?>' type='hidden'>
						</td>
						<td>lembar</td>
					</tr>
				<?php
				$count2++;
				$val2 += $value->value;
				}?>
				<tr style='border-top:3px solid'>
					<td></td>
					<td id='td_nama_budget'><strong>Total Pemakaian (Eksternal)</strong></td>
					<td id='td_total_eksternal'><input readonly class='form-control' id='total_eksternal' name='total_eksternal' value='<?php echo $val2?>' type='text'></td>
					<td>lembar</td>
				</tr>
			</tbody>
		</table>
	<?php  }?>
	<input type="hidden" id="keterangan" name="keterangan">
	<input type="hidden" id="tgl_buat" name="tgl_buat">
	<input type="hidden" id="action" name="action">
	<input type="hidden" id="kd_siklus" name="kd_siklus">
	<input type="hidden" id="t_internal" name="t_internal" value="<?php echo $count?>">
	<input type="hidden" id="t_eksternal" name="t_eksternal" value="<?php echo $count2?>">
	<input type="hidden" id="kode_farm" name="kode_farm">
	<input type="hidden" id="reason" name="reason">
	<input type="hidden" id="periode" name="periode">
	<input type="hidden" id="count_updated" name="count_updated" value="0"/>
</form>
