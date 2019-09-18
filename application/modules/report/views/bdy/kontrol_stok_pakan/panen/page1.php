<div class="table-responsive page  screen_1">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="3"></th>
                <th colspan="6">Laporan Harian Kandang</th>
                <th colspan="12"></th>
            </tr>
            <tr>
                <th rowspan="2">Tgl Kebutuhan</th>
                <th rowspan="2">Umur</th>
                <th rowspan="2">Jenis Pakan</th>
                <th colspan="3">Stok Pakan (Sak)</th>
                <th rowspan="2">BB Rata (Kg)</th>
                <th colspan="3">Stok Ayam (Ekor)</th>
                <th colspan="5">DO Panen</th>
                <th colspan="3">Realisasi Panen</th>
                <th colspan="2">Total Realisasi</th>
                <th rowspan="2">BB Rata (Kg)</th>
            </tr>
            <tr>
                <th colspan="2">Pakai</th>
                <th>Sisa Kandang</th>
                <th>Mati</th>
                <th>Afkir</th>
                <th>Stok Akhir</th>
                <th>No.DO</th>
                <th>No.SJ</th>
                <th>Pelanggan</th>
                <th>Tonase (Kg)</th>
                <th>Jumlah Ekor</th>
                <th>Tonase (Kg)</th>
                <th>Jumlah Ekor</th>
                <th>BB Rata-rata (Kg)</th>
                <th>Ekor</th>
                <th>Kg</th>
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
                        $mati = $perBarang[0]['mati'];
                        $afkir = $perBarang[0]['afkir'];
                        $jumlah = $perBarang[0]['jumlah'];
                        $berat_badan = $perBarang[0]['berat_badan'];
                        array_push($nama_barang,$perBarang[0]['nama_barang']);
                        array_push($pakai_barang,$perBarang[0]['jml_pakai']);
                        array_push($jml_akhir,$perBarang[0]['jml_akhir']);
                    }
                    echo '<tr>';
                    echo '<td rowspan="'.$rowspan.'">'.convertElemenTglWaktuIndonesia($tgl_rhk).'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.$umur.'</td>';
                    echo '<td rowspan="'.$rowspan.'"><div class="div_baris">'.implode('</div><div class="div_baris">',$nama_barang).'</div></td>';
                    echo '<td rowspan="'.$rowspan.'" onclick="Permintaan.show_lhk_bdy(this)" data-noreg="'.$noreg.'" data-tgl_transaksi="'.$_tgl.'"  class="link_span">i</td>';
                    echo '<td rowspan="'.$rowspan.'"><div class="div_baris">'.implode('</div><div class="div_baris">',$pakai_barang).'</div></td>';
                    echo '<td rowspan="'.$rowspan.'"><div class="div_baris">'.implode('</div><div class="div_baris">',$jml_akhir).'</div></td>';
                    echo '<td rowspan="'.$rowspan.'">'.formatAngka($berat_badan,3).'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($mati).'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($afkir).'</td>';
                    echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($jumlah).'</td>';

                    if(!empty($panenTgl)){
                        foreach($panenTgl as $_do){
                            if($indexPerTgl){
                                echo '<tr>';
                            }
                            echo '<td>'.$_do['no_do'].'</td>';
                            echo '<td>'.$_do['no_sj'].'</td>';
                            echo '<td>'.$_do['kode_pelanggan'].'</td>';
                            echo '<td>'.angkaRibuan($_do['berat']).'</td>';
                            echo '<td>'.angkaRibuan($_do['jumlah']).'</td>';
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