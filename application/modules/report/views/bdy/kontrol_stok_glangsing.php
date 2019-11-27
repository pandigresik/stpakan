<h3 class="text-center"> Kontrol Stok Glangsing </h3>
<div class="col-sm-12">

	<div class="panel panel-default">
		<div class="panel-body">
      <div class="row">
  			<div class="col-sm-12">
  				<form class="form-horizontal">
  					<div class="col-sm-10 no-padding">
						<div class="col-sm-4">
							<?php
							//cetak_r($list_farm);
							echo $button_simpan[$level_user];?>
							<!--button type="button" id="btn_cetak" class="btn btn-default <?php if($level_user_db != 'KFM') echo 'hidden'?>" disabled> <i class="glyphicon glyphicon-print"></i> Cetak </button-->
					  	</div>
		              <div class="col-sm-2">
		                <select class="form-control" id="search_status" data-required="1" placeholder="Status" onchange="KontrolStokGlangsing.tampilkan()">
		                  <option value="">-- All Status --</option>
		                  <?php foreach ($status as $k_status => $v_status): ?>
		                    <option value="<?php echo $k_status ?>"><?php echo $v_status ?></option>
		                  <?php endforeach; ?>
		                </select>
		              </div>
					  <?php if(count($list_farm) > 1):?>
						  <div class="col-sm-2">
			                <select class="form-control" id="search_farm" data-required="1" placeholder="Status" onchange="KontrolStokGlangsing.tampilkan()">
			                  <?php foreach ($list_farm as $key => $farm_data): ?>
			                    <option <?php echo ($kode_farm == $farm_data->KODE_FARM)? 'selected="selected"' : '' ?> value="<?php echo $farm_data->KODE_FARM ?>"><?php echo $farm_data->NAMA_FARM ?></option>
			                  <?php endforeach; ?>
			                </select>
			              </div>
				  	  <?php else:?>
						  <input type="hidden" id="search_farm" value="<?php echo $list_farm[0]->KODE_FARM ?>">
				  	  <?php endif;?>									  			  
  					</div>
  				</form>
  			</div>
      </div>
		</div>
	</div>
</div>

<div class="col-sm-12" id="header-list-report">
	<div class="col-sm-12">
     <div class="row">
       <a class="tu-float-btn tu-float-btn-right tu-table-next" >
         <i class="glyphicon glyphicon-circle-arrow-right my-float"></i>
       </a>

       <a class="tu-float-btn tu-float-btn-left tu-table-prev" >
         <i class="glyphicon glyphicon-circle-arrow-left my-float"></i>
       </a>
     </div>
  </div>

    <div class="row col-sm-12">
      <caption>
        <div>          
          <div class="row pull-right hide">
            <ul id="pagination-demo" class="pagination-sm"></ul>
          </div>
        </div>
      </caption>
    </div>

</div>
<div class="col-sm-12" id="div-list-report">

</div>
<link rel="stylesheet" type="text/css" href="assets/libs/jquery/tupage-table/jquery.tupage.table.css" >
<link rel="stylesheet" type="text/css" href="assets/libs/bootstrap-3.3.5/css/awesome-bootstrap-checkbox.css" >
<link rel="stylesheet" type="text/css" href="assets/css/report/tu_ksg.css" >
<script type="text/javascript"src="assets/libs/moments/moment.js",></script>
<script type="text/javascript"src="assets/js/common.js"></script>
<script type="text/javascript" src="assets/libs/jquery/tupage-table/jquery.tupage.table.js?v=<?php echo time()?>"></script>
<script type="text/javascript" src="assets/libs/pagination/jquery.twbsPagination.min.js"></script>
<script type="text/javascript" src="assets/js/report/kontrol_stok_glangsing.js"></script>
