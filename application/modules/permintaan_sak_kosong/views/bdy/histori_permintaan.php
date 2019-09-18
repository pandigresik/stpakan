<div class="row col-md-6 panel_histori" style="display: <?php echo $log_ppsk_prop?>">
  <div class="panel panel-primary">
	<div class="panel-heading">Histori Permintaan Sak </div>
	<div class="panel-body" style="height:300px; overflow:auto;">
		<!--
		<table class="table table-bordered custom_table list_permintaan">
		-->
		<table id="tb_histori">
			<tbody>
				<?php
				if(!empty($histori_permintaan)){
					foreach($histori_permintaan as $minta){
						echo '<tr>
							<td>'.$minta['KETERANGAN'].' '.convertElemenTglWaktuIndonesia($minta['TGL_BUAT']).'</td>
						</tr>';
					}
				}
				else{
					echo '<tr><td>Data tidak ditemukan</td></tr>';
				}
				 ?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
	</div>
</div>


</div>
<style type="text/css">
	#tb_histori{
		height:200px;
		overflow: scroll;
	}
</style>