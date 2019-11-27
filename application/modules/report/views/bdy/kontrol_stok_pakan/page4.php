
<?php 
$breadcumb_pp = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadiv Budidaya'));
?>
<div class="table-responsive page  screen_4">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="7">Permintaan Pakan<?php echo $breadcumb_pp ?></th>
                <th colspan="8">Laporan Harian Kandang</th>
                <th colspan="7">Permintaan Pakan Selanjutnya</th>
                <th colspan="4">Retur Sak Kosong</th>
            </tr>
            <tr>
                <th rowspan="2">No.PP</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Tgl Kirim</th>
                <th rowspan="2">Tgl Kebutuhan</th>
                <th rowspan="2">Umur</th>
                <th rowspan="2">Jenis Pakan</th>
                <th rowspan="2">Qty PP (Sak)</th>

                <th colspan="3">Stok Pakan (Sak)</th>
                <th rowspan="2">BB Rata(Kg)</th>
                <th colspan="3">Stok Ayam (Ekor)</th>
                <th rowspan="2">Keterangan</th>

                <th rowspan="2">No.PP</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Tgl Kirim</th>
                <th rowspan="2">Tgl Kebutuhan</th>
                <th rowspan="2">Rekomendasi PP (Sak)</th>
                <th rowspan="2">Qty PP (Sak)</th>
                <th rowspan="2">Tgl Rilis</th>

                
                <th rowspan="2">Qty(Sak)</th>
                <th rowspan="2">Belum Kembali</th>
                <th rowspan="2">Tgl Retur</th>
                <th rowspan="2">DiRetur Oleh</th>
            </tr>
            <tr>
                <th colspan="2">Pakai</th>
                <th>Sisa Kandang</th>
                <th>Mati</th>
                <th>Afkir</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            /** hitung kembali total rownya  */
            foreach($pps as $no_lpb => &$pp){
                $detail_pp = $pp['detail'];
                foreach($detail_pp as $_tgl => $pertgl){
                    $barangPPSekarang  = array_keys($pertgl['detail']);
                    $ppLhkTgl = isset($pp_lhk[$_tgl]) ? $pp_lhk[$_tgl] : array();
                    $barangPPLanjut = !empty($ppLhkTgl) ? array_keys($ppLhkTgl['barang']) : array();
                    $allBarangPP = array_unique(array_values(array_merge($barangPPSekarang,$barangPPLanjut)));
                    $selisih = count($allBarangPP) - count($barangPPSekarang);
                    if($selisih > 0){
                        $pp['total_row']++;
                    }
                }
            }

            foreach($pps as $no_lpb => $pp){
                $data_pp = $pp['data'];
                $detail_pp = $pp['detail'];
                $rowspanLpb = $pp['total_row'];

                $lpb_str = '<span data-no_pp="'.$no_lpb.'" class="link_span" onclick="KSP.showPPDetail(this)">'.$no_lpb.'</span>';
            //  $status_lpb_str = $data_pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Approve</span> &nbsp; <span  data-no_pp="'.$no_lpb.'" onclick="Approval.rejectKadiv(this)" class="btn btn-default">Reject</span></div>' : convertKode('status_approve',$data_pp['status_lpb']);
                $status_lpb_str = $data_pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-url="'.site_url('report/overview/detailPermintaanPakan').'" data data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Proses</span></div>' : convertKode('status_approve',$data_pp['status_lpb']);
                //$keterangan = buildHistoryPP($data_pp);								
                echo '<tr>';
                echo '<td rowspan="'.$rowspanLpb.'">'.$lpb_str.'</td>';
                echo '<td rowspan="'.$rowspanLpb.'">'.$status_lpb_str.'</td>';
                //echo '<td rowspan="'.$rowspanLpb.'">'.$keterangan.'</td>';
                echo '<td rowspan="'.$rowspanLpb.'">'.tglIndonesia($data_pp['tgl_kirim'],'-',' ').'</td>';    

                foreach($detail_pp as $_tgl => $pertgl){
                    $barangPPSekarang  = array_keys($pertgl['detail']);
                    $ppLhkTgl = isset($pp_lhk[$_tgl]) ? $pp_lhk[$_tgl] : array();
                    $barangPPLanjut = !empty($ppLhkTgl) ? array_keys($ppLhkTgl['barang']) : array();
                    $allBarangPP = array_unique(array_values(array_merge($barangPPSekarang,$barangPPLanjut)));
                    asort($allBarangPP);
                    $rowspanTgl = count($allBarangPP); 
                    echo '<td rowspan="'.$rowspanTgl.'">'.tglIndonesia($_tgl,'-',' ').'</td>';
                    echo '<td rowspan="'.$rowspanTgl.'">'.$pertgl['umur'].'</td>';
                    $rhkTgl = isset($rhk[$_tgl]) ? $rhk[$_tgl] : array();    
                    $returTgl = isset($retur[$_tgl]) ? $retur[$_tgl] : array();     
                    $stokHarianKandangTgl = isset($stok_kandang[$_tgl]) ? $stok_kandang[$_tgl] : array();
                    $data_panen_tgl = isset($data_panen[$_tgl]) ? $data_panen[$_tgl] : array();
                    
                    $indexPerTgl = 0;
                    
                    foreach($allBarangPP as $_kb){
                        $perBarang = isset($pertgl['detail'][$_kb]) ? $pertgl['detail'][$_kb] : array('nama_barang' => $ppLhkTgl['barang'][$_kb]['nama_barang'], 'total' => 0);    
                        $rowspanBarang =  1;
                        $rhkTglBarang = isset($rhkTgl[$_kb]) ? $rhkTgl[$_kb][0] : array('jml_pakai' => 0,'telat_entry' => 0,'telat_cetak' => 0,'jumlah' => 0,'mati' => 0, 'afkir' => 0, 'berat_badan' => 0);
                        $returTglBarang = isset($returTgl[$_kb]) ? $returTgl[$_kb][0] : array();
                        $stokHarianKandangTglBarang = isset($stokHarianKandangTgl[$_kb]) ? $stokHarianKandangTgl[$_kb] : 0;       
                        if($indexPerTgl){
                            echo '<tr>';
                        } 
                        echo '<td>'.$perBarang['nama_barang'].'</td>';
                        echo '<td>'.$perBarang['total'].'</td>';
                        
                        if(!empty($rhkTglBarang)){
                            if(!$indexPerTgl){
                                echo '<td rowspan="'.$rowspanTgl.'" onclick="Permintaan.show_lhk_bdy(this)" data-noreg="'.$noreg.'" data-tgl_transaksi="'.$_tgl.'"  class="link_span">i</td>';
                            }
                            echo '<td onclick="KSP.showDetailTimbangSilo(this)" data-noreg="'.$noreg.'" data-tgl_transaksi="'.$_tgl.'"  class="link_span">'.$rhkTglBarang['jml_pakai'].'</td>';
                            echo '<td>'.$stokHarianKandangTglBarang.'</td>';
                            if(!$indexPerTgl){
                                $class_entri = $rhkTglBarang['telat_entry'] ? 'abang' : '';
                                $class_cetak = $rhkTglBarang['telat_cetak'] ? 'abang' : '';
                                $keterangan_rhk = array();
                                if(isset($rhkTglBarang['user_cetak'])){
                                    $keterangan_rhk = array(                                
                                        '<div class="'.$class_cetak.'">'.$rhkTglBarang['user_cetak'].' - Dicetak,'.convertElemenTglWaktuIndonesia($rhkTglBarang['tgl_cetak']).'</div>',
                                        '<div class="'.$class_entri.'">'.$rhkTglBarang['user_buat'].' - Dientri,'.convertElemenTglWaktuIndonesia($rhkTglBarang['tgl_buat']).'</div>',
                                    );
                                    if(!empty($rhkTglBarang['user_ack'])){
                                        array_push($keterangan_rhk,'<div>'.$rhkTglBarang['user_ack'].' - Diketahui,'.convertElemenTglWaktuIndonesia($rhkTglBarang['tgl_ack']).'</div>');
                                    }
                                }
                                
                                echo '<td rowspan="'.$rowspanTgl.'">'.number_format($rhkTglBarang['berat_badan'],3,',','.').'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.$rhkTglBarang['mati'].'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.$rhkTglBarang['afkir'].'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.angkaRibuan($rhkTglBarang['jumlah']).'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.implode('',array_reverse($keterangan_rhk)).'</td>';
                                $noPPLanjut = $statusPPLanjut = $tglKebutuhanPPLanjutStr = $tglKirimPPLanjut = $tglRilisPPLanjut ='';
                                if(!empty($ppLhkTgl)){
                                    $noPPLanjut = $ppLhkTgl['no_lpb'];
                                    $statusPPLanjut = convertKode('status_approve',$ppLhkTgl['status']);
                                    $tglKebutuhanPPLanjut = array_unique($ppLhkTgl['tgl_kebutuhan']);
                                    $tglKirimPPLanjut = tglIndonesia($ppLhkTgl['tgl_kirim'],'-',' ');
                                    $tglRilisPPLanjut = tglIndonesia($ppLhkTgl['tgl_rilis'],'-',' ');
                                    if(count($tglKebutuhanPPLanjut) > 1){
                                        $tglKebutuhanPPLanjutStr = tglIndonesia(min($tglKebutuhanPPLanjut),'-',' ').' s/d '.tglIndonesia(max($tglKebutuhanPPLanjut),'-',' ');
                                    }else{
                                        $tglKebutuhanPPLanjutStr = tglIndonesia($tglKebutuhanPPLanjut[0],'-',' ');
                                    }
                                }

                                echo '<td rowspan="'.$rowspanTgl.'">'.$noPPLanjut.'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.$statusPPLanjut.'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.$tglKirimPPLanjut.'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.$tglKebutuhanPPLanjutStr.'</td>';
                            }
                            $jmlRekomendasiPPLanjut =  '';
                            $jmlPPLanjut = '';
                            if(isset($ppLhkTgl['barang'])){
                                $jmlRekomendasiPPLanjut =  0;
                                $jmlPPLanjut = 0;
                                if(isset($ppLhkTgl['barang'][$_kb])){
                                    $jmlRekomendasiPPLanjut =  $ppLhkTgl['barang'][$_kb]['jml_rekomendasi'];
                                    $jmlPPLanjut = $ppLhkTgl['barang'][$_kb]['jml_order'];
                                }
                            }
                               
                            echo '<td>'.$jmlRekomendasiPPLanjut.'</td>';
                            echo '<td>'.$jmlPPLanjut.'</td>';
                            if(!$indexPerTgl){
                                echo '<td rowspan="'.$rowspanTgl.'">'.$tglRilisPPLanjut.'</td>';
                            }
                        }else{
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                        }
                        if(!$indexPerTgl){
                            if(!empty($returTglBarang)){
                                echo '<td rowspan="'.$rowspanTgl.'">'.$returTglBarang['jml_sak'].'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">0</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.convertElemenTglWaktuIndonesia($returTglBarang['tgl_buat']).'</td>';
                                echo '<td rowspan="'.$rowspanTgl.'">'.$returTglBarang['user_buat'].'</td>';
                            }else{
                                echo '<td rowspan="'.$rowspanTgl.'"></td>';
                                echo '<td rowspan="'.$rowspanTgl.'"></td>';
                                echo '<td rowspan="'.$rowspanTgl.'"></td>';
                                echo '<td rowspan="'.$rowspanTgl.'"></td>';
                            }
                        }    
                        
                                               
                    echo '</tr>';    
                    $indexPerTgl++;
                    }    
                        
                }
                echo '</tr>';
            }
            ?>    
                
        </tbody>
    </table>
</div>