<?php
$hide_breadcomb = isset($breadcomb) ? $breadcomb : '';
?>

<link rel="stylesheet" type="text/css" href="assets/libs/select2/select2.css" >

<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<?php if (!empty($kandang_pending)) {
    $active = ''; ?>
		<li class="active">
			<a href="#AktivasiSiklus" role="tab" data-toggle="tab" id="for_AktivasiSiklus">Aktivasi Siklus Baru<span class='help'></span></a>
		</li>
		<?php
} else {
        $active = 'active';
    }
        ?>
		<?php if (isset($approval_aktivasi_siklus) && $approval_aktivasi_siklus) {
            echo '<li class="">
				<a href="#approvalSiklus" role="tab" data-toggle="tab" id="for_approvalAktivasi">Approval Aktivasi Siklus<span class="help"></span></a>
			</li>';
        } ?>
		<li class="<?php echo $active; ?>">
			<a href="#PerencanaanChickin" role="tab" data-toggle="tab" id="for_PerencanaanChickin">Resume Siklus<span class='help'></span></a>
		</li>
	</ul>
</div>

<div class="tab-content new-line">
	<?php if (!empty($kandang_pending)) {
            $fade = 'fade'; ?>
	<div id="AktivasiSiklus" class="tab-pane active" data-bisa_konfirmasi="<?php echo $bisa_konfirmasi; ?>">
		<ul class="breadcrumb <?php echo $hide_breadcomb; ?>">
			 <li><span class="link_span" onclick="AktivasiKandang.showKonfirmasi(this)">Konfirmasi Tanggal Doc In</span></li>
			 <li class="active"><span class="link_span" onclick="AktivasiKandang.showRencanaPengiriman(this)">Plotting Rencana Pengiriman Pakan</span></li>
		 </ul>
		<?php echo $kandang_pending; ?>
	</div>
	<?php
        } else {
            $fade = 'active';
        }
     ?>
	<div id="PerencanaanChickin" class="tab-pane <?php echo $fade; ?>">
		<?php echo $resume_siklus; ?>
	</div>
	<?php if (isset($approval_aktivasi_siklus) && $approval_aktivasi_siklus) {
         echo '<div id="approvalSiklus" class="tab-pane '.$fade.'">';
         echo $approval_siklus;
         echo '</div>';
     }
    ?>
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

<div id="context-menu-tanggal">
	<ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1">Ubah</a></li>
   	</ul>
</div>
<div id="context-menu-kandang">
	<ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1">Ubah</a></li>
   	</ul>
</div>
<div id="context-menu-gantiflock">
	<ul class="dropdown-menu" role="menu">
        <li><a tabindex="-1">Ubah</a></li>
   	</ul>
</div>

<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<!-- ppHandler digunakan untuk menghitung perkiraan tanggal kirim -->
<script type="text/javascript" src="assets/js/permintaan_pakan_v2/ppHandler.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecastHandler.js"></script>
<script type="text/javascript" src="assets/js/forecast/aktivasiKandang.js"></script>
<script type="text/javascript" src="assets/js/forecast/farm_bdy.js"></script>

<script type="text/javascript" src="assets/libs/select2/select2.js"></script>
