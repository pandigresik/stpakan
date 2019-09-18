<?php 
$breadcumb_pp = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadiv Budidaya'));
?>
<div class="table-responsive page screen_2">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="8">Permintaan Pakan<?php echo $breadcumb_pp ?></th>
                <th colspan="4">Verifikasi DO Pakan</th>
                <th colspan="6">Penerimaan Pakan dari FM</th>
            </tr>
            <tr>
                <th>No.PP</th>
                <th>Status</th>
                <th>Tgl Kebutuhan</th>
                <th>Umur</th>
                <th>Forecast Tgl Kirim</th>
                <th>Tgl Kirim</th>
                <th>Jenis Pakan</th>
                <th>Qty PP (Sak)</th>


                <th>No. DO</th>
                <th>Tgl Verifikasi</th>
                <th>Nopol</th>
                <th>Diverifikasi Oleh</th>

                <th>Tgl Terima</th>
                <th>Kode Pallet</th>
                <th>Qty Terima (Sak)</th>
                <th>Kg Terima</th>
                <th>Stok Akhir (Sak)</th>
                <th>Penerima</th>
            </tr>
        </thead>
        <tbody>
            <?php

            foreach($pps as $pp){
                //$keterangan = buildHistoryPP($pp);	
                $no_lpb = $pp['no_lpb'];
                $dos = isset($do_pps[$no_lpb]) ? $do_pps[$no_lpb] : array();

                $rowspanLpb = !empty($dos) ? $dos['total_row_penerimaan'] : count($pp['detail_barang']);
                if($rowspanLpb < count($pp['detail_barang'])){
                    $rowspanLpb = count($pp['detail_barang']);
                }
                $lpb_str = '<span data-no_pp="'.$no_lpb.'" class="link_span" onclick="KSP.showPPDetail(this)">'.$no_lpb.'</span>';
                //$status_lpb_str = $pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Approve</span> &nbsp; <span  data-no_pp="'.$no_lpb.'" onclick="Approval.rejectKadiv(this)" class="btn btn-default">Reject</span></div>' : convertKode('status_approve',$pp['status_lpb']);
                $status_lpb_str = $pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-url="'.site_url('report/overview/detailPermintaanPakan').'" data data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Proses</span></div>' : convertKode('status_approve',$pp['status_lpb']);
                echo '<tr>';
                    echo '<td rowspan="'.$rowspanLpb.'">'.$lpb_str.'</td>';
                    echo '<td rowspan="'.$rowspanLpb.'">'.$status_lpb_str.'</td>';
                    //echo '<td rowspan="'.$rowspanLpb.'">'.$keterangan.'</td>';
                    echo '<td rowspan="'.$rowspanLpb.'"><div class="div_baris">'.implode('</div><div class="div_baris">',array_unique($perpp[$no_lpb]['tgl_kebutuhan'])).'</div></td>';
                    echo '<td rowspan="'.$rowspanLpb.'"><div class="div_baris">'.implode('</div><div class="div_baris">',array_unique($perpp[$no_lpb]['umur'])).'</div></td>';
                    echo '<td rowspan="'.$rowspanLpb.'"><div>'.implode('</div><div>',array_unique($perpp[$no_lpb]['tgl_kirim'])).'</div></td>';
                    echo '<td rowspan="'.$rowspanLpb.'">'.convertElemenTglWaktuIndonesia($pp['tgl_kirim']).'</td>';
                    $i = 0;
                    foreach($pp['detail_barang'] as $kb => $perbarang){
                        $rowspanKb = isset($dos[$kb]) ? $dos[$kb]['total_row'] : 1 ;

                        if($i){
                            echo '<tr>';
                        }
                        echo '<td rowspan="'.$rowspanKb.'">'.$perbarang['nama_barang'].'</td>';
                        echo '<td rowspan="'.$rowspanKb.'">'.$perbarang['total'].'</td>';
                            if(!empty($dos)){
                                $do_kbs = isset($dos[$kb]) ? $dos[$kb] : array();
                                if(!empty($do_kbs)){
                                    foreach($do_kbs['detail'] as $_no_do => $do){
                                        $verifikasiDO = $do['verifikasi'];
                                        $penerimaanDO = $do['penerimaan'];
                                        $status_verifikasi = substr($verifikasiDO['tgl_verifikasi'],0,10) != $pp['tgl_kirim'] ? 'abang' : '';
                                        $rowspanDO = !empty($penerimaanDO) ? count($penerimaanDO) : 1;
                                        echo '<td rowspan="'.$rowspanDO.'">'.$_no_do.'</td>';
                                        echo '<td class="'.$status_verifikasi.'" rowspan="'.$rowspanDO.'">'.convertElemenTglWaktuIndonesia($verifikasiDO['tgl_verifikasi']).'</td>';
                                        
                                        $nopol_do_str = '<span class="link_span" onclick="showImage(\''.$verifikasiDO['photo'].'\')">'.$verifikasiDO['nopol'].'</span>';
                                        echo '<td rowspan="'.$rowspanDO.'">'.$nopol_do_str.'</td>';
                                        echo '<td rowspan="'.$rowspanDO.'">'.$verifikasiDO['user_verifikasi'].'</td>';
                                        
                                        if(!empty($penerimaanDO)){
                                            //$indexPenerimaan = 0;
                                            $tglTerimaSebelumnya = '';
                                            foreach($penerimaanDO as $terima){
                                                $status_terima = substr($terima['tgl_terima'],0,10) != $pp['tgl_kirim'] ? 'abang' : '';
                                                $stokAkhir = '';
                                                if(isset($stok_gudang[$terima['tgl_terima']])){
                                                    $stokAkhir = isset($stok_gudang[$terima['tgl_terima']][$kb]) ? $stok_gudang[$terima['tgl_terima']][$kb] : '';
                                                }
                                                echo '<td class="'.$status_terima.'">'.convertElemenTglWaktuIndonesia($terima['tgl_terima']).'</td>';    
                                                echo '<td>'.$terima['kode_pallet'].'</td>';    
                                                echo '<td>'.$terima['jumlah'].'</td>';    
                                                echo '<td>'.$terima['berat'].'</td>';                         
                                                if($tglTerimaSebelumnya != $terima['tgl_terima']){
                                                    echo '<td>'.$stokAkhir.'</td>';                                                    
                                                }else{
                                                    echo '<td></td>';
                                                }
                                                
                                                echo '<td>'.$terima['user_buat'].'</td>';    
                                                echo '</tr>';
                                                $tglTerimaSebelumnya = $terima['tgl_terima'];
                                            }
                                        }else{
                                            echo '<td></td>';
                                            echo '<td></td>';
                                            echo '<td></td>';
                                            echo '<td></td>';
                                            echo '<td></td>';    
                                            echo '<td></td>';    
                                            echo '</tr>';    
                                        }
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
                                    echo '</tr>';    
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
                                echo '</tr>';
                            }
                        echo '</tr>';
                        $i++;
                    }
                echo '</tr>';
            }

            ?>
        </tbody>
    </table>
</div>