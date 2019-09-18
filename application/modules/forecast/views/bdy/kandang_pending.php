<?php
$id_title = ($ganti_info) ? 'Detail Kandang' : 'Konfirmasi Tanggal DOC-In';
//$id_info = ($ganti_info) ? 'info_detail_kandang' : 'div_kandang_konfirmasi';
//$id_kandang = ($ganti_info) ? 'div_forecast' : 'div_kandang_pending';
 ?>

<div class="row">
		<div class="col-md-4 block_konfirmasi konfirmasi">
			<div class="panel panel-default">
				<div class="panel-heading">Siklus Pending Rencana DOC In Tahunan (RDIT)</div>
				<div class="panel-body">
					<div class="css-treeview" id="div_kandang_pending">
						<?php echo $tree ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-8 block_rencana_pengiriman">
			<?php if($bisa_konfirmasi){ ?>
			<div class="panel panel-default konfirmasi">
				<div class="panel-heading">Daftar Kandang yang Dipilih</div>
				<div class="panel-body">
				<div class="table-responsive">
					<table id="tabelAkanKonfirmasi" class="table table-striped custom_table">
						<thead>
							<tr>
								<th>Farm</th>
								<th>Kandang</th>
								<th>DOC In <br /> (berdasarkan RDIT)</th>
								<th>Revisi Rencana DOC In <br />oleh Kadiv</th>
							</tr>
						</thead>
						<tbody class="text-center"></tbody>
					</table>
				</div>	
				</div>
			</div>

			<div class="row  konfirmasi">
				<div class="col-md-3">Total Populasi : <span id="totalPopulasiKonfirmasi"></span> ekor</div>
				<div class="col-md-3 col-md-offset-6">
					<span class="btn btn-default pull-right" onclick="AktivasiKandang.konfirmasi(this)">Konfirmasi</span>
				</div>
			</div>
			<?php } ?>
			<div class="panel panel-default konfirmasi rencana-pengiriman">
				<div class="panel-heading"><?php echo $id_title ?></div>
				<div class="panel-body">
						<div class="css-treeview" id="div_kandang_konfirmasi" data-minimum_konfirmasi="<?php echo $minimum_approve ?>">
							<?php echo $kandang_konfirmasi ?>
						</div>
				</div>
			</div>
		</div>
		<div class="col-md-8 rencana-pengiriman" style="display:none">
			<div class="panel panel-default">
				<div class="panel-heading">Rencana Pengiriman Pakan </div>
				<div class="panel-body" id="divTabelRencanaKirim">

				</div>
			</div>
		</div>
</div>
<div class="row">
	<div class="panel panel-default">
		<div class="panel-body">
			<ul>
				<li>Hitam : Kandang aktif</li>
				<li>Orange : Approval Kadep</li>
				<li>Biru : Sudah konfirmasi dan tahap pengajuan</li>
				<li>Merah : Reject</li>
			</ul>
	</div>
	</div>
</div>
<div class="hide" id="lockEditDocIn"><?php echo $lockEditDocIn ?></div>
