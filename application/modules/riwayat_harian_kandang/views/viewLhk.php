<div class="row">
    <div class="row col-md-12">
        <div class="col-md-2">
            <div class="col-md-6">Kandang</div>
            <div class="col-md-6">: <?php echo $kandang['kode_kandang'] ?></div>
        </div>
        <div class="col-md-4">
            <div class="col-md-6">Flock</div>
            <div class="col-md-6">: <?php echo $kandang['flok_bdy'] ?></div>
        </div>
        <div class="col-md-4">
            <div class="col-md-5">Tanggal DOC-In</div>
            <div class="col-md-7">: <?php echo tglIndonesia($kandang['tgl_doc_in'],'-',' ') ?></div>
        </div>
        <div class="col-md-2">
            <div class="col-md-4">Umur</div>
            <div class="col-md-8">: <?php echo $kandang['umur'] ?> Hari</div>
        </div>
    </div>    
    <div class="row col-md-12">
        <div class="col-md-4 col-md-offset-2">
            <div class="col-md-6">Tanggal LHK</div>
            <div class="col-md-6">: <?php echo tglIndonesia($tgl_transaksi,'-',' ') ?></div>
        </div>
        <div class="col-md-4">
            <div class="col-md-5">Pengawas</div>
            <div class="col-md-7">: <?php echo (isset($pegawai[$rhk['USER_BUAT']]) ? $pegawai[$rhk['USER_BUAT']]['nama_pegawai'] : '') ?></div>
        </div>
    </div>    
    <br />
    <div class="col-md-12">
        <table class="table table-bordered custom_table">
            <thead>
                <tr>
                    <th colspan="4">Penimbangan Per Sekat</th>
                    <th colspan="3">Populasi</th>
                    <th colspan="3">Pakan</th>
                </tr>
                <tr>
                    <th>Sekat</th>
                    <th>Jumlah</th>
                    <th>BB (gr)</th>
                    <th>Keterangan</th>
                    <th>Mati</th>
                    <th>Afkir</th>
                    <th>Jenis Kelamin</th>
                    <th>Nama Pakan</th>
                    <th>Terpakai</th>
                    <th>Permintaan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                
                    foreach($rhk_penimbangan as $k => $v){
                        echo '<tr>
                                <td> Sekat '.$v['SEKAT'].'</td>
                                <td>'.angkaRibuan($v['JUMLAH']).'</td>
                                <td>'.angkaRibuan($v['BERAT']).'</td>
                                <td>'.$v['KETERANGAN'].'</td>';
                        if(!$k){
                            echo '<td>'.angkaRibuan($rhk['C_MATI']).'</td>';
                            echo '<td>'.angkaRibuan($rhk['C_AFKIR']).'</td>';
                            echo '<td>Campur</td>';
                        }else{
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                        }
                        
                        if(isset($rhk_pakan[$k])){
                            echo '<td>'.$rhk_pakan[$k]['nama_barang'].'</td>';
                            echo '<td>'.$rhk_pakan[$k]['jml_pakai'].'</td>';
                            echo '<td>'.$rhk_pakan[$k]['jml_permintaan'].'</td>';
                        }else{
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                        }
                        
                        echo '</tr>';
                    }
                
                ?>
                
            </tbody>
        </table>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <div>Dientri pada <?php echo  convertElemenTglWaktuIndonesia($rhk['TGL_BUAT']) ?></div>            
            <div>Diverifikasi <?php echo (isset($pegawai[$rhk['USER_ACK1']]) ? $pegawai[$rhk['USER_ACK1']]['nama_pegawai'] : '') ?> pada  <?php echo  convertElemenTglWaktuIndonesia($rhk['ACK1']) ?></div>            
        </div>                    
    </div>
</div>