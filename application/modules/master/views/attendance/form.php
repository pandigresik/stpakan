<div class="row">
    <div class="col-md-6">
        <form class="form-horizontal"  id="form_stpakan">
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Pilih Pegawai</label>
                <div class="col-md-8">
                    <select onchange="Attendance.updateInfoPegawai(this)" class="form-control">
                        <option value="">Pilih Pegawai</option>
                        <?php 
                            if (!empty($stpakan)) {
                                foreach ($stpakan as $st) {
                                    echo '<option data-detail=\''.json_encode($st).'\' value="'.$st['KODE_PEGAWAI'].'">'.$st['NAMA_PEGAWAI'].'</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Kode Pegawai</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="KODE_PEGAWAI"  placeholder="Kode Pegawai" required readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Nama Pegawai</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="NAMA_PEGAWAI" placeholder="Nama Pegawai" required readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Grup Pegawai</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="DESKRIPSI" placeholder="Grup Pegawai" required readonly>
                </div>
            </div>
            
            
        </form>
    </div>
    <div class="col-md-6">
        <form class="form-horizontal" id="form_attendance">
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Pilih Pegawai</label>
                <div class="col-md-8">
                    <select class="form-control" onchange="Attendance.updateInfoAttendance(this)">
                        <option value="">Pilih Pegawai</option>
                        <?php 
                            if (!empty($attendance)) {
                                foreach ($attendance as $st) {
                                    echo '<option data-nama="'.strtoupper(trim($st['NAMA_PEGAWAI'])).'" data-detail=\''.json_encode($st).'\' value="'.$st['KODE_PEGAWAI'].'">'.$st['NAMA_PEGAWAI'].'</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Badge Number</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="KODE_PEGAWAI"  placeholder="Badge Number" required readonly>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-md-4 control-label">Nama Pegawai</label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="NAMA_PEGAWAI" placeholder="Nama Pegawai" required readonly>
                </div>
            </div>
        </form>
    </div>
</div>