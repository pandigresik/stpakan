<div class="row">
	<div class="col-md-2">
		<div class="panel panel-default">
			<div class="panel-heading">Daftar Farm</div>
			<div class="panel-body" data-status="<?php echo $status ?>" data-startdate="<?php echo $tanggal['startDate'] ?>" data-enddate="<?php echo $tanggal['endDate'] ?>">
				<div class="search">
					<div class="right-inner-addon ">
                    	<i class="glyphicon glyphicon-search"></i>
                    	<input type="search" onchange="Approval.filter_content_farm(this)" placeholder="Search" name="no_pengembalian" class="form-control ">
                	</div>
                	<div class="new-line">
                	<?php 
                	if(!empty($list_farm)){
					echo '<ul class="list-group">';
                		foreach($list_farm as $kf => $f){
							$_j_retur = isset($jml_retur[$kf]) ? $jml_retur[$kf] : 0 ; 
							$_j_retur_str = !empty($_j_retur) ? ' ( '.$_j_retur.' ) ' : '';
                			echo '<li class="menu_list_retur list-group-item" data-jml_retur="'.$_j_retur.'" data-kode_strain="'.$f['kode_strain'].'" data-kode_siklus="'.$f['kode_siklus'].'" data-kode_farm="'.$kf.'" onclick="Approval.showlistretur(this)"><span>'.strtoupper($f['nama_farm']).'</span> '.$_j_retur_str.' </li>';
                		}
                	echo '</ul>';
                	}
                	
                	?>
                	</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="col-md-10 non-margin-left">
		<div class="panel panel-default ">
			<div class="panel-heading">FARM &nbsp;<span class="nama_farm"></span></div>
			<div class="panel-body" id="header_retur">
			</div>
		</div>
		<div class="panel panel-default non-margin-top ">
			<div class="panel-heading">Detail Retur Sak Kosong No  <span class="no_retur"></span></div>
			<div class="panel-body" id="detail_retur">
			</div>
		</div>
	</div>
</div>