<div>
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li class="active">
			<a href="#daftarPengembalianpakan" role="tab" data-toggle="tab" id="for_daftarPermintaan">Daftar Pengembalian Pakan Rusak<span class='help'></span></a>
		</li>

		<li>
			<a href="#transaksi" role="tab" data-toggle="tab" id="for_transaksi">Transaksi<span class='help'></span></a>
		</li>

	</ul>
</div>

<div class="tab-content new-line">
	<div id="daftarPengembalianpakan" class="tab-pane fade active in">
		<form>
		<div class="col-md-12"><div class="form-inline new-line">



           <?php echo $buat_baru; ?>
        </div><div class="form-inline new-line">
            <label for="tanggal-kirim">Tanggal</label>
            <div class="form-group">
                <div class="input-group div-date">
                    <input type="text" class="form-control" id="startDate" name="startDate" readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
            <label for="tanggal-kirim">&nbsp;s/d&nbsp;</label>
            <div class="form-group">
                <div class="input-group div-date">
                    <input type="text" class="form-control" id="endDate" name="endDate" readonly>
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </div>
                </div>
            </div>
        </div>


		<div class="form-inline new-line">
           	<span class="btn btn-default" onclick="Pengembalianpakan.list_cari(this)" style="margin-left: 4.5%;">Cari</span>
						        </div>
		</form></div>


				<div class="col-md-12 new-line" id="list_pengembalian">
					<?php

					?>
				</div>
	</div>
	<div id="transaksi" class="tab-pane fade">

	</div>
	<div id="laporan" class="tab-pane fade">

	</div>
</div>
 <!--
<link rel="stylesheet" type="text/css" href="assets/css/pengembalian_pakan_rusak/pengembalian.css" >
  -->
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/pengembalian_pakan_rusak/pengembalianpakan.js"></script>
