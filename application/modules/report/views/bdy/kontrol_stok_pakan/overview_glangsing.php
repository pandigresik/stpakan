<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <table class="table table-bordered custom_table">
                <thead>
                    <tr class="bg_biru">
                        <th colspan="13">Overview Glangsing Farm Per Siklus</th>
                    </tr>
                    <tr>
                        <th rowspan="2">Kategori</th>
                        <th rowspan="2">Budget Per Siklus (Sak)</th>
                        <th colspan="2">Total Permintaan</th>
                        <th rowspan="2">Total Sak yang Diambil</th>
                        <th rowspan="2">Total Sak Dikembalikan</th>
                        <th rowspan="2">Total Sak Terpakai</th>
                        <th rowspan="2">Total Pemusnahan</th>
                        <th rowspan="2">SO/DO (Sak)</th>
                        <th rowspan="2">Sisa Stok Tersedia (Sak)</th>
                        <th rowspan="2">Verifikasi Pemb. (Sak)</th>
                        <th rowspan="2">Surat Jalan (Sak)</th>
                        <th rowspan="2">Sisa Stok (Sak)</th>
                    </tr>
                    <tr>
                        <th>Realisasi</th>
                        <th>Over Budget</th>
                    </tr>
                </thead>    
                <tbody>
                <?php 
                    if(!empty($content)){
                        foreach($content as $nama_barang => $c){
                            $overbudget = 0;
                            if($c['ppsk']->minta > $c['total_budget']){
                                $overbudget = $c['ppsk']->minta - $c['total_budget'];
                            }
                            $stok_tersedia = $c['ppsk']->pakai - $c['so']['so'];
                            echo '<tr>';
                            echo '<td>'.$nama_barang.'</td>';
                            echo '<td>'.$c['total_budget'].'</td>';
                            echo '<td>'.(!empty($c['ppsk']->minta) ? $c['ppsk']->minta : 0).'</td>';
                            echo '<td>'.$overbudget.'</td>';
                            echo '<td>'.(!empty($c['ppsk']->minta) ? $c['ppsk']->minta : 0).'</td>';
                            echo '<td>'.(!empty($c['ppsk']->kembali) ? $c['ppsk']->kembali : 0).'</td>';
                            echo '<td>'.(!empty($c['ppsk']->pakai) ? $c['ppsk']->pakai : 0).'</td>';
                            echo '<td>'.$c['pemusnahan'].'</td>';
                            echo '<td>'.$c['so']['so'].'</td>';
                            echo '<td>'.$stok_tersedia.'</td>';
                            echo '<td>'.$c['so']['verifikasi_pembayaran'].'</td>';
                            echo '<td>'.$c['so']['sj'].'</td>';
                            echo '<td>'.($stok_tersedia - $c['so']['sj']).'</td>';
                            echo '</tr>';
                        }
                    }
                ?>
                </tbody>
            </table>
        </div>    
    </div>
</div>