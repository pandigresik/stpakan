<?php 
$breadcumb_glangsing = generateBreadcumb(array('Kepala Farm','Kadept PI','Kadept/Wakadept Admin Budidaya','Kadiv Budidaya <span class="abang">(* bila over budget)</span>'));					
?>
<div class="table-responsive page screen_1">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th colspan="7">Permintaan Glangsing Bekas Pakai<?php echo $breadcumb_glangsing ?></th>
                <th colspan="3">Pengambilan Glangsing</th>
                <th colspan="2">Pengembalian Glangsing</th>
                <th colspan="2">Pemusnahan Glangsing Bangkai</th>
            </tr>
            <tr>
                <th>Tanggal<br />Kebutuhan</th>
                <th>Umur</th>
                <th>No.PPSK</th>
                <th>Status</th>
                <th>Kategori</th>
                <th>QTY<br />(Sak)</th>
                <th>Keterangan</th>
                <th>QTY<br />(Sak)</th>
                <th>Tgl Ambil</th>
                <th>Diterima Oleh</th>
                <th>QTY<br />(Sak)</th>
                <th>Keterangan</th>
                <th>QTY<br />(Sak)</th>
                <th>Berita Acara Pemusnahan</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(!empty($ppsks)){
            $keterangan_status = array(
                'N' => 'Dientri',
                'R' => 'Dikoreksi',
                'A' => 'Disetujui',
                'A0' => 'Disetujui',
                'RJ' => 'Direject'
            );

            
            foreach($ppsks as $ppsk){
                $keterangan = array();
                $status_ppsk = '';
                $no_ppsk = $ppsk['no_ppsk'];
                if(isset($logs[$no_ppsk])){
                    foreach($logs[$no_ppsk] as $lp){
                        array_push($keterangan,'<div>'.$lp['nama_pegawai'].' - '.$keterangan_status[$lp['status']].', '.convertElemenTglWaktuIndonesia($lp['tgl_buat']).'</div>');
                        if(empty($status_ppsk)){
                            $status_ppsk = $lp['status'];
                        }
                    }
                }
                $status_ppsk_str = in_array($status_ppsk,array('A0')) ? '<div class="btnApproval"><span class="btn btn-default" data-kode_farm="'.$kode_farm.'" data-url="'.site_url('report/overview/detailPPSK').'" onclick="Approval.approvePPSK(this)">Proses</span>' : convertKode('status_approve', $status_ppsk); 
                echo '<tr>';
                echo '<td>'.tglIndonesia($ppsk['tgl_kebutuhan'],'-',' ').'</td>';
                echo '<td>'.$ppsk['umur'].'</td>';
                echo '<td><span class="link_span" onclick="KSP.showNextTrHidden(this)">'.$ppsk['no_ppsk'].'</span></td>';
                echo '<td>'.$status_ppsk_str.'</td>';
                echo '<td>'.$ppsk['nama_budget'].'</td>';
                echo '<td>'.$ppsk['jml_diminta'].'</td>';
                echo '<td>'.implode('',$keterangan).'</td>';
                if(!empty($ppsk['tgl_terima'])){
                    $keterangan_kembali = array();
                    if(!empty($ppsk['user_ack'])){
                        array_push($keterangan_kembali,'<div>'.$ppsk['user_ack'].' - Diketahui, '.convertElemenTglWaktuIndonesia($ppsk['tgl_ack']).'</div>');
                    }

                    if(!empty($ppsk['user_pengembali'])){
                        array_push($keterangan_kembali,'<div>'.$ppsk['user_pengembali'].' - Dikembalikan, '.convertElemenTglWaktuIndonesia($ppsk['tgl_kembali']).'</div>');
                    }
                    echo '<td>'.$ppsk['jml_diminta'].'</td>';
                    echo '<td>'.convertElemenTglWaktuIndonesia($ppsk['tgl_terima']).'</td>';
                    echo '<td>'.$ppsk['user_penerima'].'</td>';
                    echo '<td>'.$ppsk['jml_kembali'].'</td>';
                    echo '<td>'.implode('',$keterangan_kembali).'</td>';
                    if(!empty($ppsk['no_berita_acara'])){
                        echo '<td>'.($ppsk['jml_diminta'] - $ppsk['jml_kembali']).'</td>';
                        $ba_str = empty($ppsk['no_berita_acara']) ? '' : '<span class="link_span"><a target="_blank" href="sales_order/pemusnahan_bangkai/cetakBA?ba='.$ppsk['no_berita_acara'].'">'.$ppsk['no_berita_acara'].'</a></span>';
                        echo '<td>'.$ba_str.'</td>';
                    }else{
                        echo '<td></td>';
                        echo '<td></td>';    
                    }
                    
                }else{
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                }
                
                echo '</tr>';

                echo '<tr class="hide detail_hidden">';
                echo '<td colspan="2"></td>';    
                echo '<td colspan="5">';
                echo '<table class="table table-bordered custom_table">
                        <thead>
                            <tr class="bg_biru">
                                <td colspan="3"><label>Daftar Permintaan Glangsing</label></td>
                            </tr>
                            <tr>
                                <th rowspan="2">Jumlah Sak Diminta</th>
                                <th colspan="2">Over Budget</th>
                            </tr>
                            <tr>
                                <th>Jml Sak</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>'.$ppsk['jml_diminta'].'</td>
                                <td>'.$ppsk['jml_over_budget'].'</td>
                                <td>'.$ppsk['keterangan'].'</td>
                            </tr>
                        </tbody>	
                    </table>';    
            
                echo '</td>';
                echo '</tr>';
            }
        }
        ?>
        </tbody>
    </table>
</div>    