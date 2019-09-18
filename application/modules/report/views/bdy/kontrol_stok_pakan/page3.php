<?php 
$breadcumb_pp = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadiv Budidaya'));
?>
<div class="table-responsive page  screen_3">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="5">Permintaan Pakan<?php echo $breadcumb_pp ?></th>
<<<<<<< HEAD
                <th colspan="12">Dropping Pakan ke Kandang</th>
=======
                <th colspan="10">Dropping Pakan ke Kandang</th>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
            </tr>
            <tr>
                <th>No.PP</th>
                <th>Status</th>
                <th>Tgl Kirim</th>
                <th>Tgl Kebutuhan</th>
                <th>Umur</th>
                <th>Jenis Pakan</th>
                <th>Qty PP (Sak)</th>

                <th>Req. Kandang (Sak)</th>
                <th>Qty. Retur (Sak)</th>
                <th>Tgl Dropping</th>
<<<<<<< HEAD
            <!--    <th>Penerima</th> -->
                <th>No. Pallet</th>
                <th>Qty (Sak)</th>
                <th>Kg Dropping</th>
                <th>Tgl Terima Kandang</th>
                <th>User Verifikasi (Kandang)</th>
                <th>Kg Dropping (Kandang)</th>
=======
                <th>Penerima</th>
                <th>No. Pallet</th>
                <th>Qty (Sak)</th>
                <th>Kg Dropping</th>
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
                <th>Stok Akhir (Sak)</th>
                
            </tr>
        </thead>
        <tbody>
            <?php
            
            foreach($pps as $no_lpb => $pp){
                $indexRowspanLpb = 0;
                $data_pp = $pp['data'];
                $detail_pp = $pp['detail'];
                $rowspanLpb = $pp['total_row'];
                
                $totalDroping = 0;
                $totalDropingTglKb = array();
                $totalDropingTgl = array();
                foreach($detail_pp as $_tgl => $pertgl){
                    $totalDropingTgl[$_tgl] = 0;
                    $totalDropingTglKb[$_tgl] = array();
                    $droppingTgl = isset($dropping[$_tgl]) ? $dropping[$_tgl] : array();
                    if(!empty($droppingTgl)){
                        foreach($droppingTgl as $_kb => $perbarang){
                            $totalDroping += count($perbarang);
                            $totalDropingTgl[$_tgl] += count($perbarang);
                            $totalDropingTglKb[$_tgl][$_kb] = count($perbarang);   
                        }
                    }else{
                        $totalDroping += 1;
                    }
                }
                //$keterangan = buildHistoryPP($data_pp);								
                if($totalDroping > $rowspanLpb){
                    $rowspanLpb = $totalDroping;
                }
                $lpb_str = '<span data-no_pp="'.$no_lpb.'" class="link_span" onclick="KSP.showPPDetail(this)">'.$no_lpb.'</span>';
                //$status_lpb_str = $data_pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Approve</span> &nbsp; <span  data-no_pp="'.$no_lpb.'" onclick="Approval.rejectKadiv(this)" class="btn btn-default">Reject</span></div>' : convertKode('status_approve',$data_pp['status_lpb']);
                $status_lpb_str = $data_pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-url="'.site_url('report/overview/detailPermintaanPakan').'" data data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Proses</span></div>' : convertKode('status_approve',$data_pp['status_lpb']);
                echo '<tr>';
                echo '<td rowspan="'.$rowspanLpb.'">'.$lpb_str.'</td>';
                echo '<td rowspan="'.$rowspanLpb.'">'.$status_lpb_str.'</td>';
                //echo '<td rowspan="'.$rowspanLpb.'">'.$keterangan.'</td>';
                echo '<td rowspan="'.$rowspanLpb.'">'.convertElemenTglWaktuIndonesia($data_pp['tgl_kirim']).'</td>';
                
                foreach($detail_pp as $_tgl => $pertgl){
                    $rowspanTgl = count($pertgl['detail']); 
                    $droppingTgl = isset($dropping[$_tgl]) ? $dropping[$_tgl] : array();
                    if(isset($totalDropingTgl[$_tgl])){
                        if($totalDropingTgl[$_tgl] > $rowspanTgl){
                            $rowspanTgl = $totalDropingTgl[$_tgl];
                        }
                        /** jika total pertgl < total dropping pakan maka tambahkan satu baris */
                        if(count($pertgl['detail']) < count($droppingTgl)){
                            foreach($droppingTgl as $_kb => $_tmp){
                                if(!isset($pertgl['detail'][$_kb])){
                                    $pertgl['detail'][$_kb] = array(
                                        'nama_barang' => $_tmp[0]['nama_barang'],
                                        'total' => 0
                                    );
                                }
                            }
                        }
                    }
                    echo '<td rowspan="'.$rowspanTgl.'">'.tglIndonesia($_tgl,'-',' ').'</td>';
                    echo '<td rowspan="'.$rowspanTgl.'">'.$pertgl['umur'].'</td>';

                    foreach($pertgl['detail'] as $_kb => $perBarang){
                        $droppingTglBarang = isset($dropping[$_tgl][$_kb]) ? $dropping[$_tgl][$_kb] : array();
                        $rowspanBarang = isset($totalDropingTglKb[$_tgl][$_kb]) ? $totalDropingTglKb[$_tgl][$_kb] : 1;
                        $jmlPermintaan = isset($droppingTglBarang[0]) ? $droppingTglBarang[0]['jml_permintaan'] : 0;

                        $rhk_tgl_cetak = '';
                        $rhk_user_cetak = '';
                        $rhk_tgl_entri = '';
                        $rhk_pakai = '';

                        $rhkTglKb = array();
                        if(isset($rhk[$_tgl])){
                            if(isset($rhk[$_tgl][$_kb])){
                                $rhkTglKb = $rhk[$_tgl][$_kb][0];
                                $rhk_tgl_cetak = $rhkTglKb['tgl_cetak'];
                                $rhk_user_cetak = $rhkTglKb['user_cetak'];
                                $rhk_tgl_entri = $rhkTglKb['tgl_buat'];
                                $rhk_pakai = $rhkTglKb['jml_pakai'];
                            }
                        }

                        $returTglKb = array();
                        $jml_retur_sak = 0;
                        if(isset($retur_sak[$_tgl])){
                            if(isset($retur_sak[$_tgl][$_kb])){
                                $returTglKb = $retur_sak[$_tgl][$_kb][0];                                
                                $jml_retur_sak = $returTglKb['jml_sak'];
                            }
                        }

                        echo '<td rowspan="'.$rowspanBarang.'">'.$perBarang['nama_barang'].'</td>';
                        echo '<td rowspan="'.$rowspanBarang.'">'.$perBarang['total'].'</td>';
                        echo '<td rowspan="'.$rowspanBarang.'">'.$jmlPermintaan.'</td>';
                        echo '<td rowspan="'.$rowspanBarang.'">'.$jml_retur_sak.'</td>';
                        if(!empty($droppingTglBarang)){
                            $_index = 0;
                            foreach($droppingTglBarang as $_dpk){
                                $status_dropping = $_dpk['telat_dropping'] ? 'abang' : '';
                                $stokAkhir = '';
                                if(isset($stok_gudang[$_dpk['picked_date']])){
                                    $stokAkhir = isset($stok_gudang[$_dpk['picked_date']][$_kb]) ? $stok_gudang[$_dpk['picked_date']][$_kb] : '';
                                }
                                echo '<td class="'.$status_dropping.'">'.convertElemenTglWaktuIndonesia($_dpk['picked_date']).'</td>';
<<<<<<< HEAD
                            //    echo '<td>'.$pengawas.'</td>';
                                echo '<td>'.$_dpk['kode_pallet'].'</td>';
                                echo '<td>'.$_dpk['jml_pick'].'</td>';
                                echo '<td>'.formatAngka($_dpk['berat_pick'],3).'</td>';
                                echo '<td>'.convertElemenTglWaktuIndonesia($_dpk['timbang_kandang']).'</td>';
                                echo '<td>'.(empty($_dpk['user_verifikasi_kandang']) ? '' : $pengawas ).'</td>';
                                echo '<td>'.formatAngka($_dpk['berat_kandang'],3).'</td>';
                                echo '<td>'.$stokAkhir.'</td>';
                                echo '</tr>';
                                $_index++;
                            }
=======
                                echo '<td>'.$pengawas.'</td>';
                                echo '<td>'.$_dpk['kode_pallet'].'</td>';
                                echo '<td>'.$_dpk['jml_pick'].'</td>';
                                echo '<td>'.formatAngka($_dpk['berat_pick'],3).'</td>';
                                echo '<td>'.$stokAkhir.'</td>';
                                echo '</tr>';
                                $_index++;

                            }
                            
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
                        }else{
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
                            echo '<td></td>';
<<<<<<< HEAD
                            echo '<td></td>';
                            echo '<td></td>';
=======
>>>>>>> 53ac33e8886f01e73c357c79450caa9cbb1d4526
                            
                            echo '</tr>';
                        }

                        $indexRowspanLpb++;
                    }
                }
                echo '</tr>';
            }
            ?>    
                
        </tbody>
    </table>
</div>