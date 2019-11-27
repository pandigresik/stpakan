<?php
/* */
	$t = array(

	);
 ?>
<div class="panel panel-primary">
	<div class="panel-heading">Penerimaan DOC In</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-11">
				<table class="table table-bordered  custom_table" data-table="bapdocbox">
					<thead>
						<tr>
							<th>No. SJ</th>
							<th>Tanggal Penerimaan</th>
							<th>Jumlah Box</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="sj"><input class="form-control" type="text" name="suratjalan" onchange="BAPD.ceksuratjalan(this)" /></td>
							<td class="tgl_terima">
								<div class="input-group date">
									<input type="text" class="form-control" name="tgl_terima" readonly="">
									<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
								</div>
							</td>
							<td><span class="link_span col-md-6 text-center jmlbox" onclick="BAPD.show_suratjalan(this)" data-listsj='<?php echo htmlentities(json_encode($t),ENT_QUOTES, 'UTF-8') ?>'>Kode Box</span></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-1"><span class="btn btn-default" onclick="BAPD.show_performancedocin(this)">Lanjut</span></div>
		</div>
	</div>
</div>
