<div class="panel panel-default">
  <div class="panel-heading">Simulasi Rencana Pengiriman</div>
  <div class="panel-body">
    <div class='col-md-2' name="divFarm">
          <?php echo $list_farm?>
    </div>
    <div class="row container">
      <div class="col-md-12">
        Siklus Pending Rencana DOC In Tahunan (RDIT)
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 well">
        <div class="css-treeview" id="div_forecast">

        </div>
        <div class="btn btn-default pull-right" id="btnSimulasiKirim">Tampilkan</div>
      </div>
    </div>
    <div class="row container" id="block_simulasi">
      <div class="row container" id="div_simulasi">

      </div>
      <div class="btn btn-default pull-right" id="resumeSimulasiKirim">Simulasi</div>
      <div class="row">
        <div class="col-md-12" id="tabelresumeSimulasi"></div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript" src="assets/js/forecast/config.js"></script>
<script type="text/javascript" src="assets/js/permintaan_pakan/ppHandler.js"></script>
<script type="text/javascript" src="assets/js/forecast/forecastHandler.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/dist/xlsx.full.min.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/Blob.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/FileSaver.js"></script>
<script type="text/javascript" src="assets/libs/js-xlsx/Export2Excel.js"></script>
<script type="text/javascript" src="assets/js/forecast/aktivasiKandang.js"></script>
<script type="text/javascript" src="assets/js/forecast/simulasi.js"></script>
