<div id="list_farm" class="header text-center"><h3><?php echo $list_farm ?></h3></div>
<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#PerencanaanChickin" role="tab" data-toggle="tab" id="for_PerencanaanChickin">Perencanaan DOC-In <span class='help'></span></a>
		</li>
		<?php if($flock){ ?>
		<li>
			<a href="#Flock" role="tab" data-toggle="tab" id="for_Flock">Flock<span class='help'></span></a>
		</li>
		<?php } ?>
	</ul>
</div>

<div class="tab-content new-line">
	<div id="PerencanaanChickin" class="tab-pane fade active in">
	</div>	
	<?php if($flock){ ?>
	<div id="Flock" class="tab-pane fade">
		<?php echo $view_flock ?>
	</div>
	<?php } ?>
</div>
<div id="context-menu-tahun">
	<ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1">Tambah bulan</a></li>
   	</ul>
</div>

<div id="context-menu-bulan">
	<ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1">Tambah tanggal</a></li>
   	</ul>
</div>

<div id="context-menu-kandang">
	<ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1">Ubah</a></li>
   	</ul>
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecastHandler.js?"></script> 
<script type="text/javascript" src="assets/js/forecast/forecast.js"></script>
  