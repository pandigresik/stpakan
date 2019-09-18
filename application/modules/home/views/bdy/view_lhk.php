<!DOCTYPE html>
<html>
<head>
	<title>ST Pakan</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=10.0">
	<base href="<?php echo $base_url?>" />
	<script type="text/javascript" src="assets/libs/jquery/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery.ui.datepicker-id.js"></script>
	<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap3-typeahead.min.js"></script>
	<script type="text/javascript" src="assets/js/forecast/config.js"></script>
	<script type="text/javascript" src="assets/js/common.js"></script>
</head>
<body>
   <div class="container col-md-12">
		<div id="transaksi" class="main_content">
			  <?php echo $content ?>
		</div>

	</div>
</body>

<link rel="stylesheet" media="all" type="text/css" href="assets/libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/jquery-ui/css/jquery-ui.min.css">
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/toastr/css/toastr.css">

<link rel="stylesheet" type="text/css" href="assets/css/home.css" >
<script type="text/javascript">
	$(function(){
		var tgl_lhk = '<?php echo $tgl_lhk ?>';
		selected_noreg = '<?php echo $noreg ?>';
		selected_tgl_doc_in = '<?php echo $tgl_docin ?>';
		LoadDataLHK(tgl_lhk);
		$('#inp_tgl_lhk').val(Config._tanggalLocal(tgl_lhk,'-',' '));
		$('#inp_doc_in').val(Config._tanggalLocal(selected_tgl_doc_in,'-',' '));
		$('#inp_kandang').val('<?php echo $nama_kandang ?>');
		$('#inp_flock').val('<?php echo $flock ?>');
	})

</script>
</html>
