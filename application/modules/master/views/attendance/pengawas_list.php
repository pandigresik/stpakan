<div class="panel panel-default">
  <div class="panel-heading">Mapping Attendance</div>
  <div class="panel-body">
	<div class="row>">
		<button type="button" name="tombolTambah" id="btnTambah" class="btn btn-primary" onclick="Attendance.add(this)">Tambah Mapping</button>
		<br/><br/>
	</div>
	<table id="pegawai_attendance" class="table table-bordered table-striped">
	<thead>
		<tr>
        <th></th>
        <th><input type="text" class="form-control q_search" name="pa.KODE_PEGAWAI" placeholder="Kode Pegawai"></th>
        <th><input type="text" class="form-control q_search" name="mp.NAMA_PEGAWAI" placeholder="Nama Pegawai"></th>
				<th><input type="text" class="form-control q_search" name="pa.BADGE_NUMBER" placeholder="Badge Number"></th>
        <th><input type="text" class="form-control q_search" name="ui.NAME" placeholder="Nama Pegawai"></th>
        <th class="col-md-1">
					<select class="form-control q_search" name="mp.grup_pegawai">
						<option value="">Semua</option>				
						<?php 
                            if (!empty($grups)) {
                                foreach ($grups as $g) {
                                    echo '<option value="'.$g['grup_pegawai'].'">'.$g['deskripsi'].'</option>';
                                }
                            }
                        ?>
					</select>
				
			</th>
        </tr>
		<tr>
            <th>No</th>
            <th>Kode Pegawai</th>
            <th>Nama Pegawai</th>
						<th>Badge Number</th>
						<th>Nama Pegawai</th>
            <th>Grup Pegawai</th>
        </tr>
    </thead>
	<tbody>
	</tbody>
	</table>
	<div class="new-line clear-fix">
    <div class="pull-right pagination">
                    
    </div>
  </div>
</div>

<script type="text/javascript" src="assets/js/master/pegawai_attendance.js"></script>