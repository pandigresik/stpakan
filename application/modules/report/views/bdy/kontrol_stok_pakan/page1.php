<?php 
$breadcumb_pp = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadiv Budidaya'));
$breadcumb_do = generateBreadcumb(array('Kabag. Admin Budidaya','Kadept PI','Kadiv Budidaya'));
?>
<div class="table-responsive page screen_1">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="9">Permintaan Pakan<?php echo $breadcumb_pp ?></th>
                <th colspan="6">Delivery Order<?php echo $breadcumb_do ?></th>
            </tr>
            <tr>
                <th>No.PP</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Tgl Kebutuhan</th>
                <th>Umur</th>
                <th>Forecast Tgl Kirim</th>
                <th>Tgl Kirim</th>
                <th>Jenis Pakan</th>
                <th>Qty PP (Sak)</th>

                <th>No.DO</th>
                <th>Ekspedisi</th>
                <th>Rit</th>
                <th>Qty DO (Sak)</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $status_do_str = array(
                'N' => 'Approve',
                'R' => 'Review',
                'D' => 'Rilis',
                'T' => 'Reject',
            );
            $status_log_do = array(
                'D' => 'Dirilis',
                'R' => 'Dikoreksi',
                'T' => 'Ditolak',
                'N' => 'Disetujui',
            );
            foreach($pps as $pp){
                $keterangan = buildHistoryPP($pp);								
                $no_lpb = $pp['no_lpb'];
                $dos = isset($do_pps[$no_lpb]) ? $do_pps[$no_lpb] : array();
                $rowspanLpb = !empty($dos) ? $dos['total_row'] : count($pp['detail_barang']);
                $umur = dateDifference($tgldocin,$pp['tgl_kebutuhan']);
                $lpb_str = '<span data-no_pp="'.$no_lpb.'" class="link_span" onclick="KSP.showPPDetail(this)">'.$no_lpb.'</span>';
                $status_lpb_str = $pp['status_lpb'] == 'RV' ? '<div class="btnApproval"><span class="btn btn-default" data-kode_farm="'.$kode_farm.'" data-kode_siklus="'.$kode_siklus.'" data-url="'.site_url('report/overview/detailPermintaanPakan').'" data data-no_pp="'.$no_lpb.'" onclick="Approval.approveKadiv(this)">Proses</span></div>' : convertKode('status_approve',$pp['status_lpb']);
                $indexLpb = 0;
                echo '<tr>';
                    echo '<td rowspan="'.$rowspanLpb.'">'.$lpb_str.'</td>';
                    echo '<td rowspan="'.$rowspanLpb.'">'.$status_lpb_str.'</td>';
                    echo '<td rowspan="'.$rowspanLpb.'">'.$keterangan.'</td>';
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
                            if(isset($dos[$kb]) && !empty($dos[$kb])){
                                $do_kbs = isset($dos[$kb]) ? $dos[$kb] : array();
                                if(!empty($do_kbs)){
                                    foreach($do_kbs['detail'] as $do){
                                        $status_do_btn = $do['status_do'] == 'R' ? '<div class="btnApproval"><span class="btn btn-default" data-kode_farm="'.$kode_farm.'" data-tgl_kirim="'.$pp['tgl_kirim'].'" data-kode_siklus="'.$kode_siklus.'" data-url="'.site_url('report/overview/detailDOPakan').'"  onclick="Approval.approveDO(this)">Proses</span></div>' : $status_do_str[$do['status_do']];
                                        //$selisihBuatDO = $do['selisih'];
                                        $timelineBuatDO = $umur >= 25 ? 0 : -1;
                                        echo '<td>'.$do['no_do'].'</td>';
                                        echo '<td>'.$do['nama_ekspedisi'].'</td>';
                                        echo '<td>'.$do['rit'].'</td>';
                                        echo '<td>'.$do['jml_muat'].'</td>';
                                        
                                        if(!$indexLpb){
                                            $keterangan_do = array();
                                            if(isset($log_do[$no_lpb])){
                                                foreach($log_do[$no_lpb] as $_ket_do){
                                                    $status_do = '';
                                                    if($_ket_do['tgl_buat'] >= $pp['tgl_kirim']){
                                                        $aktualSelisihBuatDO = dateDifference($_ket_do['tgl_buat'],$pp['tgl_kirim']);
                                                        $status_do = $aktualSelisihBuatDO > $timelineBuatDO ? 'abang' : '';
                                                    }
                                                    
                                                    array_push($keterangan_do,'<div class="'.$status_do.'">'.$_ket_do['nama_pegawai'].' - '.$status_log_do[$_ket_do['status']].','.convertElemenTglWaktuIndonesia($_ket_do['tgl_buat']).'</div>');
                                                }
                                            }
                                            echo '<td rowspan="'.$rowspanLpb.'">'.$status_do_btn.'</td>';
                                            echo '<td rowspan="'.$rowspanLpb.'">'.implode('',$keterangan_do).'</td>';
                                        }
                                        
                                    //    echo '<td class="'.$status_do.'">'.convertElemenTglWaktuIndonesia($do['tgl_buat']).'</td>';  
                                        echo '</tr>';
                                        $indexLpb++;
                                    }
                                }
                            }else{
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '<td></td>';
                                echo '</tr>';
                            }
                        $indexLpb++;
                        $i++;
                    }
                echo '</tr>';
            }

            ?>
        </tbody>
    </table>
</div>