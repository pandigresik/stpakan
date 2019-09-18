<?php 
echo '<div id="data-notif" class="hide">';
	if(isset($notif)){
		echo json_encode($notif);
	}
echo '</div>';
?>	



<div class="row">
	<?php
		foreach ($farm as $key => $val) {
			if(isset($dashboard[$val['kode_farm']])){
				echo '<div class="x_panel_header col-md-11 col-sm-4 col-xs-12 overflow_hidden"><b>'.$val['nama_farm'].'</b></div>';
				echo '<div class="x_content_header col-md-11 col-sm-4 col-xs-12 overflow_hidden">';
				foreach ($dashboard[$val['kode_farm']] as $key => $val) {
	?>

	<div class="shortcut x_panel col-md-5 col-sm-4 col-xs-12 overflow_hidden" onclick="Home.openLinkShortcut('<?php echo $val['link'];?>');">
		<div class="x_panel_text col-md-11 col-sm-4 col-xs-12 overflow_hidden">
			<?php echo $key;?>
		</div>
		<div class="x_panel_number">
			<?php echo $val['jumlah'];?>
		</div>
	</div>

	<?php
					
				}
				echo '</div>';
			}
		}
		if(isset($dashboard['ALL'])){
			echo '<div class="x_panel_header col-md-11 col-sm-4 col-xs-12 overflow_hidden"><b>Semua</b></div>';
			echo '<div class="x_content_header col-md-11 col-sm-4 col-xs-12 overflow_hidden">';
			foreach ($dashboard['ALL'] as $key => $val) {
				?>
				<div class="shortcut x_panel col-md-5 col-sm-4 col-xs-12 overflow_hidden" onclick="Home.openLinkShortcut('<?php echo $val['link'];?>');">
					<div class="x_panel_text col-md-11 col-sm-4 col-xs-12 overflow_hidden">
						<?php echo $key;?>
					</div>
					<div class="x_panel_number">
						<?php echo $val['jumlah'];?>
					</div>
				</div>
				<?php
			}
			echo '</div>';
		}
	
	?>
</div>


<script type="text/javascript" src="assets/js/home/kadept.js"></script>
<style type="text/css">
.row{
	padding: 0px 20px 20px 20px;
}

</style>

