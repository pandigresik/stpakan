<div class="table-responsive page  screen_2">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="16">Panen</th>
            </tr>
            <tr>
                <th rowspan="2">Tgl Kebutuhan</th>
                <th rowspan="2">Umur</th>
                <th colspan="3">DO Panen</th>
                <th colspan="3">Realisasi Panen</th>
                <th colspan="2">Total Realisasi</th>
                <th rowspan="2">BB Rata (Kg)</th>
                <th colspan="4">Waktu Kendaraan Panen</th>
            </tr>
            <tr>
                <th>No.DO</th>
                <th>No.SJ</th>
                <th>Pelanggan</th>
                <th>Tonase (Kg)</th>
                <th>Jumlah Ekor</th>
                <th>Tonase (Kg)</th>
                <th>Jumlah Ekor</th>
                <th>BB Rata-rata (Kg)</th>
                <th>Verifikasi Kendaraan Keluar RPA</th>
                <th>Berangkat dari RPA</th>
                <th>Datang di Farm</th>
            <!--    <th>[<span class="link_span"><i class="glyphicon glyphicon-minus" onclick="KSP.hideShowNextColumn(this)"></i></span>] Datang di Farm</th> -->
                <th class="hide_column">No.Pol</th>
                <th class="hide_column">Sopir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if(!empty($rhks)){
                foreach($rhks as $_tgl => $rhk){
                    $tgl_rhk = $_tgl;
                    $rowspan = 1;
                    $panenTgl = array();
                    if(isset($panen[$tgl_rhk])){
                        $rowspan = count($panen[$tgl_rhk]);
                        $panenTgl = $panen[$tgl_rhk];
                    }
                    $indexPerTgl = 0;
                    $umur = 0;
                    $mati = $afkir = $jumlah = $berat_badan = 0;
                    $nama_barang = array();
                    $pakai_barang = array();
                    $jml_akhir = array();
                    foreach($rhk as $perBarang){
                        $umur = $perBarang[0]['umur'];
                    }
                    echo '<tr>';
                    echo '<td rowspan="'.$rowspan.'">'.convertElemenTglWaktuIndonesia($tgl_rhk).'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.$umur.'</td>';

                    if(!empty($panenTgl)){
                        foreach($panenTgl as $_do){
                            if($indexPerTgl){
                                echo '<tr>';
                            }
                            echo '<td>'.$_do['no_do'].'</td>';
                            echo '<td>'.$_do['no_sj'].'</td>';
                            echo '<td>'.$_do['kode_pelanggan'].'</td>';
                            echo '<td>'.angkaRibuan($_do['r_berat']).'</td>';
                            echo '<td>'.angkaRibuan($_do['r_jumlah']).'</td>';
                            echo '<td>'.formatAngka($_do['r_berat_badan'],3).'</td>';
                            if(!$indexPerTgl){
                                $totalEkor = array_sum(array_column($panenTgl,'r_jumlah'));
                                $totalBerat = array_sum(array_column($panenTgl,'r_berat'));
                                echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($totalEkor).'</td>';
                                echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($totalBerat).'</td>';
                                echo '<td rowspan="'.$rowspan.'">'.formatAngka(($totalBerat / $totalEkor),3).'</td>';
                            }
                            echo '<td>'.convertElemenTglWaktuIndonesia($_do['verifikasi_berangkat_rpa']).'</td>';
                            echo '<td>'.convertElemenTglWaktuIndonesia($_do['berangkat_rpa']).'</td>';
                            echo '<td>'.convertElemenTglWaktuIndonesia($_do['tgl_datang']).'</td>';
                            $nopol_str = !empty($_do['nopol']) ? '<span class="link_span" onclick="showImage(\''.$_do['photo'].'\')" >'.$_do['nopol'].'</span>' : ''; 
                            echo '<td class="hide_column">'.$nopol_str.'</td>';
                            echo '<td class="hide_column">'.$_do['sopir'].'</td>';
                            $indexPerTgl++;
                        }
                    }else{
                        for($i = 0; $i <= 10 ; $i++){
                            echo '<td></td>';
                        }    
                    }
                    
                    echo '</tr>';
                }
            }
            
            ?>    
                
        </tbody>
    </table>
</div>