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
	<script type="text/javascript" src="assets/libs/toastr/js/toastr.js"></script>
	<script type="text/javascript" src="assets/libs/jquery/jquery.price_format.min.js"></script>
	
	<script type="text/javascript" src="assets/js/common.js"></script>
	<script type="text/javascript" src="assets/js/forecast/config.js"></script>
	<script type="text/javascript" src="assets/js/permintaan_pakan/ppHandler.js"></script>
	<script type="text/javascript" src="assets/js/permintaan_pakan/permintaan_pakan.js"></script>
</head>
<body>
   <div class="container col-md-12">
		<div id="transaksi" class="main_content">
			  <?php echo $content ?>
		</div>
		<div class="hide" id="tanggal_server" data-tanggal_server="<?php echo $tanggal_server ?>"><?php echo tglIndonesia($tanggal_server,'-',' ')?></div>
	</div>
</body>

<link rel="stylesheet" media="all" type="text/css" href="assets/libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/jquery-ui/css/jquery-ui.min.css">
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/toastr/css/toastr.css">

<link rel="stylesheet" type="text/css" href="assets/css/home.css" >



<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/libs/bootbox/js/bootbox.js"></script>


<script type="text/javascript" src="assets/js/ajaxSetup.js"></script>
<script type="text/javascript" src="assets/js/commonHandler.js"></script>
<script type="text/javascript" src="assets/js/index.js"></script>

</html>
