<!DOCTYPE html>
<html>
<head>
	<title>ST Pakan</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=10.0">
	<base href="<?php echo $base_url?>" />
	<script type="text/javascript" src="assets/libs/jquery/jquery-2.0.0.min.js"></script>
	<script type="text/javascript" src="assets/js/jquery.scrollabletable.js"></script>
	<script type="text/javascript" src="assets/libs/toastr/js/toastr.min.js"></script>
	<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/libs/jquery-ui/js/jquery.ui.datepicker-id.js"></script>
	<script type="text/javascript" src="assets/js/common.js"></script>
</head>
<body>
   <div class="container col-md-12 ">
		<nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <span class="btn navbar-brand" href="#" onclick="window.location.reload()"><?php echo $project_name ?></span>
          </div>
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <?php //echo $menu ?>

            </ul>
            <ul class="nav navbar-nav navbar-right">
               <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $nama_user ?><span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li><a id="popup_gantipassword" data-farm=<?php echo $user['farm'] ?> data-level="<?php echo $user['level'] ?>" data-nama_user="<?php echo $nama_user ?>" href="user/user/changePassword">Ganti Password</a></li>
                  <li><a href="user/user/logout">Logout</a></li>
                </ul>
              </li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
		<div id="main_content" class="main_content">
			  <?php echo $content ?>
		</div>
		<div class="hide" id="tanggal_server" data-tanggal_server="<?php echo $tanggal_server ?>"><?php echo tglIndonesia($tanggal_server,'-',' ')?></div>
	</div>
</body>

<link rel="stylesheet" media="all" type="text/css" href="assets/libs/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/jquery-ui/css/jquery-ui.min.css">
<link rel="stylesheet" media="screen" type="text/css" href="assets/libs/toastr/css/toastr.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/treeview.css" >
<link rel="stylesheet" type="text/css" href="assets/css/home.css" >

<script type="text/javascript" src="assets/libs/jquery/jquery.price_format.min.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap-contextmenu.js"></script>
<script type="text/javascript" src="assets/libs/bootstrap/js/bootstrap3-typeahead.min.js"></script>
<script type="text/javascript" src="assets/libs/bootbox/js/bootbox.js"></script>
<script type="text/javascript" src="assets/js/jquery.alphanum.js"></script>
<!-- untuk generate pdf dari browser -->
<script type="text/javascript" src="assets/libs/jquery/jspdf.min.js"></script>
<script type="text/javascript" src="assets/libs/jquery/jspdf.plugin.autotable.js"></script>

<script type="text/javascript" src="assets/js/ajaxSetup.js"></script>
<script type="text/javascript" src="assets/js/commonHandler.js"></script>
<script type="text/javascript" src="assets/js/index.js"></script>

</html>
