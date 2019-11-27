<!-- from pakan rusak dan hilang -->
<br>
<div class="panel panel-primary">
	<div class="panel-heading">Pakan Rusak/Hilang</div>	
	<div class="panel-body">
			<div class='row'>
				<div class='col-md-12'>
					<center><h3 id="nama_pakan">BR 2 Super</h3></center>
				</div>
			</div>
			<br>
			<div class='row'>
				<div class='col-md-offset-1 col-md-10'>
					<i style="color:red;">*Mohon dilengkapi</i>
				</div>
			</div>
			<div class='row'>
				<div class='col-md-offset-1 col-md-5'>
					<fieldset style="border:none;border:1px solid #AFAFAF;padding:10px;">
						<legend style="border:none;width:auto;">Pakan Rusak</legend>
						<table class='table table-bordered custom_table'>
							<thead>
							<tr>
								<th>No.</th>
								<th>Tonase Pakan<br>Rusak</th>
								<th colspan='2'>Keterangan</th>
							</tr>
							</thead>
							<tbody>
							<tr style='cursor:default;'>
								<td>1</td>
								<td><input type='text' class='form-control' onKeyup='number_only(this)'></td>
								<td><input type='text' class='form-control'></td>
								<td>+<br>-</td>
							</tr>
							</tbody>
						</table>
						<br><br>						
					</fieldset>
				</div>
				<div class='col-md-offset-1 col-md-4'>
					<fieldset style="border:none;border:1px solid #AFAFAF;padding:10px;">
						<legend style="border:none;width:auto;">Pakan Hilang</legend>
						<br>
						<div class='row'>
							<label class='col-md-6'>Jumlah (sak)</label>
							<div class='col-md-6'>
								<input type='text' class='form-control' onKeyup='number_only(this)'> 
							</div>
						</div>
						<br>
						<div class='row'>
							<label class='col-md-6'>Keterangan</label>
							<div class='col-md-6'>
								<input type='text' class='form-control'> 
							</div>
						</div>
						<br><br>
					</fieldset>
				</div>
			</div>
			<br>
			<div class='row'>
					<label class='col-md-offset-1 col-md-2'>Lampirkan foto</label>
					<div class='col-md-2'>
						<div class="input-group">
                    		<input type="text" readonly id="lampirkan-file" class="form-control" value="<?php echo isset($retur) ? $retur['LAMPIRAN'] : '' ?>" >                    
                			<span class="btn btn-default btn-file input-group-addon">
                               	<b>...</b> <input type="file" name="lampiran" id="lampiran">
                           	</span>
						</div>
					</div>
			</div>
			<br>
			<div class='row'>
				<center>
					<br><button type='button' class='btn btn-primary'>Simpan</button><br><br>
				</center>
			</div>
			<br>
	</div>	
</div>
<br><br>