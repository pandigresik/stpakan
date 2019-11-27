
<div class="row">
	<div id="div_permintaan">

	</div>
</div>
<div class="row col-md-12">

	<div class="panel panel-primary">
		<div class="panel-heading">Daftar Permintaan Sak Over Budget</div>
		<div class="panel-body" style="overflow-x: scroll;">
			<!--
			<table class="table table-bordered custom_table list_permintaan">
			-->
			<table class="table table-bordered custom_table" width="3000">
			<thead>
				<tr>
						<th rowspan="2">No. Permintaan Sak</th>
						<th rowspan="2" id="th_keterangan">
							Keterangan
						</th>
						<th rowspan="2">Jumlah Sak</th>
						<th colspan="2">Over Budget</th>

						<th rowspan="2">Penerima Sak</th>
						<th rowspan="2">Status</th>
						<th rowspan="2">Tgl Rilis</th>
						<th colspan="3">Tindak Lanjut</th>
						<th rowspan="2">Keterangan</th>

					</tr>
					<tr>
						<th>Jumlah</th>
						<th>Alasan</th>
						<th>Kadept Pemeliharaan Internal</th>
						<th>Kadept Admin Budidaya</th>
						<th width="170">Kadiv Budidaya</th>

					</tr>
				<!-- <tr>
					<th>No. Permintaan Sak</th>
					<th id="th_keterangan">
						Keterangan
						<span class="caret btn-column-filter" style="cursor:pointer;"></span>
					</th>
					<th>Jumlah Sak</th>
					<th>Penerima Sak</th>
					<th>Status</th>
					<th>Tgl Rilis</th>
					<th>Tgl Ack</th>
					<th>Tgl Approve</th>
				</tr> -->
			</thead>
			<tbody>
				<?php
				if(!empty($listoverbudget)){
					$no = 0;
					foreach($listoverbudget as $minta){
						$str = ($minta['GRUP_PEGAWAI'] == 'KDV') ? convertElemenTglWaktuIndonesia($minta['TGL_APPROVE_KADIV']) : '';
						if ($str == '') {
							$str = ($minta['GRUP_PEGAWAI'] != 'KFM' && $minta['STATUS'] == 'A') ? '<div><button class="btn_approve btn btn-primary" style="display:none" onclick="permintaanSak.update(this,\'AA\')">Approve</button>
					&nbsp;<button style="display:none" class="btn_reject btn btn-danger" data-no_ppsk="'.$minta['NO_PPSK'].'" onclick="showPopUp(this)">Reject</button><span class="bt_reject" id="btn_reject_'.$no.'"  ></span></div>' : "";
						}
						echo '<tr >
							<td><span class="link_span" data-kode_budget="'.$minta['KODE_BUDGET'].'" data-status="'.$minta['STATUS'].'" data-no_ppsk="'.$minta['NO_PPSK'].'" onclick="permintaanSak.showButton(this)">'.$minta['NO_PPSK'].'</span></td>
							<td class="Keterangan">'.$minta['KETERANGAN'].'</td>
							<td class="number">'.$minta['JML_SAK'].'</td>
							<td>'.$minta['JML_OVER'].'</td>
							<td>'.$minta['ALASAN_OVER'].'</td>
							<td>'.$minta['NAMA_PEGAWAI'].'</td>
							<td id="stt_ppsk">'.$minta['STATUS_DESC'].'</td>
							<td>'.convertElemenTglWaktuIndonesia($minta['TGL_RILIS']).'</td>
							<td>'.convertElemenTglWaktuIndonesia($minta['TGL_ACK']).'</td>
							<td>'.(convertElemenTglWaktuIndonesia($minta['TGL_APPROVE'])).'</td>
							<td>'.$str.'</td>
							<td>'.((($minta['STATUS'] == 'A' && $minta['JML_APPROVE'] > 1 && $minta['JML_OVER'] == 0) || $minta['STATUS'] == 'V') ? $minta['STATUS_MESSAGE'] : "").'</td>
						</tr>';
						$no++;
					}
				}
				else{
					echo '<tr><td colspan=12>Data tidak ditemukan</td></tr>';
				}
				 ?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
		</div>
	</div>
</div>
<span class="tooltipster-span hide">
  	<div class="panel panel-primary" style="margin-bottom: 0px">
    	<div class="panel-heading">Konfirmasi Reject</div>
    	<div class="panel-body">
      		<div class="form-group">
        		<div style="margin-bottom: 5px">
          			<span>Mohon mengisi keterangan reject <br>(Min. 10 karakter)</span>
        		</div>
        		<textarea class="form-control" onkeyup="lengthCek(this)" cols="50" id="keterangan_reject" name="keterangan_reject"></textarea>
      		</div>
      		<div class="form-group pull-right" style="margin-bottom:0px">
        		<button class="btn btn-default" onclick="$('.btn_reject').tooltipster('hide');">batal</button>
        		<button class="btn btn-primary btn_simpan_reject" disabled style="margin-left: 5px" onclick="permintaanSak.update(this,'RJA')">Simpan</button>
      		</div>
		</div>
  	</div>
</span>
<script type="text/javascript" src="assets/libs/jquery/tooltipster/jquery.tooltipster.min.js"></script>
<script type="text/javascript">
	var T_NO_PPSK = "";
	function showPopUp(elm) {
		T_NO_PPSK = $(elm).attr('data-no_ppsk');
		var _panel = $(elm).closest('div.panel-body');
		var _keterangan_reject = _panel.find('textarea').val();
		var box = bootbox.confirm({
		  message: 'Apakah anda yakin untuk menolak No. Permintaan Sak '+T_NO_PPSK+ ' ?',
		  buttons: {
			 'cancel': {
				  label: 'Tidak',
				  className: 'btn-default'
			 },
			 'confirm': {
				  label: 'Ya',
				  className: 'btn-primary'
			 }
		  },
		  callback: function(result) {
			 if(result){
				 box.bind('hidden.bs.modal', function() {


					  var id = $(elm).siblings('span').attr('id');
					  $('#'+id).tooltipster({
						  animation : 'fade',
						  delay : 200,
						  theme : 'tooltipster-light',
						  touchDevices : false,
						  trigger : 'click',
						  contentAsHTML : true,
						  interactive : true,
						  position : 'bottom-left',
						  content: $('.tooltipster-span').html()
						});

					   $('#'+id).tooltipster('show');

 				});
			 }
		  }
		});
	}


</script>
<style type="text/css"></style>
<link rel="stylesheet" type="text/css" href="assets/css/permintaan_sak_kosong/permintaan.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/tooltipster.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-light.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-noir.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-punk.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tooltipster/themes/tooltipster-shadow.css" >
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/permintaan_sak_kosong/permintaanSak.js"></script>
