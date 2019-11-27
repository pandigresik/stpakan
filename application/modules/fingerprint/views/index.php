<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-1">
                Cari   
            </div>
        </div>
        <table class="table table-bordered custom_table">
            <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Kode Pegawai</th>
                    <th>Grup Pegawai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
                if(!empty($listPegawai)){
                    foreach($listPegawai as $l){
                        echo '<tr>
                                <td>'.$l['nama_pegawai'].'</td>
                                <td>'.$l['kode_pegawai'].'</td>
                                <td>'.$l['deskripsi'].'</td>
                                <td><span class="btn btn-default" data-kode_pegawai="'.$l['kode_pegawai'].'" onclick="Finger.verifikasi(this)">Finger</span></td>
                            </tr>';
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript" src="assets/js/fingerprint/finger.js"></script>	