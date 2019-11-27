<div class="panel panel-default">
    <div class="panel-heading">Budget Pemakaian Glangsing</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-4">
					<div class="panel panel-default">
						<div class="panel-heading">Periode</div>
						<div class="panel-body">
							<table id="table_search" style="display:none">				                
				                    <tr>
				                        <th class=" ctext-center id_pallet" style="padding-left:0px; width:45%">
				                            <input type="text" class="text-center form-control q_search" name="q_nm_farm" id="q_nm_farm"
				                                                    placeholder="search" onkeyup="refresh_table()" value="<?=$nama_farm?>">
				                        </th>
				                        <th class="text-center tanggal_penimbangan" style="padding-left:5px; width:30%">
				                            <input type="text" class="text-center form-control q_search" name="q_siklus" id="q_siklus"
				                                                    placeholder="search" onkeyup="refresh_table();">
				                        </th>
				                        <th class="text-center tara" style="padding-left:5px; width:30%">
				                            <select class="form-control" onchange="refresh_table();" id="q_status" name="q_status">

				                            </select>
				                        </th>
				                    </tr>
				                </thead>
				            </table>
				            <br>
							<table class="table table-bordered table-striped" id="tb_status_periode">
								<thead>
									<tr>
										<th class="text-center table-header" style="width: 170px">Farm</th>
										<th class="text-center table-header" style="width: 115px">Siklus</th>
										<th class="text-center table-header">Status</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-lg-8 non-margin-left">
					<div class="panel panel-default ">
						<div class="panel-heading">Form Pemakaian Glangsing &nbsp;<span class="nama_farm"></span></div>
						<div class="panel-body" id="list_permintaan">
                     <?php echo $list_budget?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" type="text/css" href="assets/css/pengambilan_barang/pengambilan.css">
<script type="text/javascript" src="assets/js/jquery.alphanum.js"></script>
<script type="text/javascript" src="assets/libs/jquery/plugin/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/libs/jquery/plugin/datatables/fnReloadAjax.js"></script>
<script type="text/javascript" src="assets/js/jquery.redirect.js"></script>
<script type="text/javascript" src="assets/js/budget_pengembalian_glangsing/budget_pengembalian.js"></script>
<script>
	$(function(){
		if('<?=$jabatan?>' == 'KF'){
			$('#table_search').hide();
			$('#q_status').html('<option value="">Semua</option>');
		}
		else if('<?=$jabatan?>' == 'KD' || '<?=$jabatan?>' == 'KDV'|| '<?=$jabatan?>' == 'KDB'){
			if('<?=$jabatan?>' == 'KDV' || '<?=$jabatan?>' == 'KDB'){ 
				$('#q_status').html('<option value="">Semua</option>'
					+'<option value="R">Review</option>'
					+'<option value="RJ">Reject</option>'
					+'<option value="C">Closed</option>'
					+'<option value="A">Approved</option>'
				);
			}
			else if('<?=$jabatan?>' == 'KD'){
				$('#q_status').html('<option value="">Semua</option>'
					+'<option value="N">New (Rilis)</option>'
					+'<option value="R">Review</option>'
					+'<option value="RJ">Reject</option>'
					+'<option value="C">Closed</option>'
					+'<option value="A">Approved</option>'
				);
			}
			$('#table_search').show();
			//$('#q_nm_farm').val('');
		}
		
		if($("#tb_status_periode").length > 0){			

			refresh_table();

			$("#tb_status_periode").on('page.dt',function () {
				onresize(100);
			});
		}

		$('#tb_status_periode tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            $('#tb_status_periode tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
	});

</script>
<style>
	#td_nama_budget{
		width:500px;
	}
	#td_jumlah_glangsing{
		width:70px;
	}
	#td_total_internal{
		width:70px;
	}
	#td_total_eksternal{
		width:70px;
	}
	#internal_budget td{
		padding:3px;
	}
	#eksternal_budget td{
		padding:3px;
	}
	table.dataTable tbody tr.selected {
	    background-color: #b0bed9;
	}
</style>
