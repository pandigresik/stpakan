<?php 
$breadcumb_harga = generateBreadcumb(array('Kadept Logistik','Kadiv Logistik','Kadiv Budidaya'));					
?>
<div class="table-responsive page screen_2">
    <table class="table table-bordered custom_table">
        <thead>
            <tr>
                <th rowspan="2">Tanggal Transaksi</th>
                <th colspan="3">Pengajuan Harga Glangsing<?php echo $breadcumb_harga ?></th>
                <th colspan="7">Rencana Penjualan Glangsing</th>
                <th colspan="3">Verifikasi Pembayaran</th>
                <th colspan="3">Delivery Order Glangsing</th>
                <th colspan="2">Verifikasi DO Glangsing</th>
                <th colspan="3">Surat Jalan</th>
            </tr>
            <tr>
                <th>No.Pengajuan</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Tanggal<br />SO/DO</th>
                <th>No.SO/DO</th>
                <th>Kategori Glangsing</th>
                <th>Pelanggan</th>
                <th>Jumlah Sak</th>
                <th>Harga<br />Jual/Sak<br />(Rp)</th>
                <th>Total<br />Pembayaran<br />(Rp)</th>
                <th>Total<br />Transfer<br />(Rp)</th>
                <th>Bukti</th>
                <th>Keterangan</th>
                <th>No.Pol</th>
                <th>Sopir</th>
                <th>Tanggal Buat</th>
                <th>Tanggal Verifikasi<br />DO</th>
                <th>User<br />Verifikasi DO</th>
                <th>No.SJ</th>
                <th>Realisasi<br />Glangsing</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if(!empty($so)){
            $ph_lama = '';
            foreach($so as $s){
                $urlLampiran = $s['lampiran'];
                $lampiran_str = empty($urlLampiran) ? '' : '<span class="link_span" onclick="showImage(\''.$urlLampiran.'\')">'.$s['no_so'].'</span>';
                $keterangan_so = array();
                $no_so = $s['no_so'];
                if(isset($logs[$no_so])){
                    $status_so = array(
                        'A' => 'Diketahui',
                        'U' => 'Diverifikasi'
                    );
                    foreach($logs[$no_so] as $_ls){
                        if(in_array($_ls['status'],array('A','U'))){
                            array_push($keterangan_so,'<div>'.$_ls['nama_pegawai'].' - '.$status_so[$_ls['status']].','.convertElemenTglWaktuIndonesia($_ls['tgl_buat']).'</div>');
                        }
                        
                    }
                }
                $no_ph = '';
                $status_ph = '';
                $status_ph_str = '';
                $urut_ph = 0;
                $no_ph_str = '';
                $keterangan_ph = array();
                if(isset($ph[$s['tgl_so']])){
                    $no_ph = $ph[$s['tgl_so']]['no_pengajuan_harga'];
                    if($ph_lama != $no_ph){
                        $status_ph_str = array(
                            'N' => 'Dibuat',
                            'R' => 'Dikoreksi',
                            'R1' => 'Dikoreksi',
                            'RJ' => 'Ditolak',
                            'A' => 'Disetujui',
                            
                        );
                        
                        if(isset($log_ph[$no_ph])){
                            foreach($log_ph[$no_ph] as $_ls){
                                array_push($keterangan_ph,'<div>'.$_ls['nama_pegawai'].' - '.$status_ph_str[$_ls['status']].','.convertElemenTglWaktuIndonesia($_ls['tgl_buat']).'</div>');
                                if(empty($status_ph)){
                                    $status_ph = $_ls['status'];
                                    $urut_ph = $_ls['no_urut'];
                                }
                            }
                        }
                        
                        $no_ph_str = empty($no_ph) ? '' : '<span class="link_span" onclick="KSP.showNextTrHidden(this)">'.$no_ph.'</span>';
                        $status_ph_str = in_array($status_ph,array('R','R1')) ? '<div class="btnApproval"><span class="btn btn-default" data-no_urut="'.$urut_ph.'" data-no_ph="'.$no_ph.'" data-kode_farm="'.$kode_farm.'" data-url="'.site_url('report/overview/detailPengajuanHarga').'" onclick="Approval.approvePh(this)">Proses</span>' : convertKode('status_approve', $status_ph); 
                    }
                    $ph_lama = $no_ph;
                }
                

                echo '<tr>';
                echo '<td>'.tglIndonesia($s['tgl_so'],'-',' ').'</td>';
                echo '<td>'.$no_ph_str.'</td>';
                echo '<td data-no_ph="'.$no_ph.'">'.$status_ph_str.'</td>';
                echo '<td>'.implode('',$keterangan_ph).'</td>';
                echo '<td>'.tglIndonesia($s['tgl_so'],'-',' ').'</td>';
                echo '<td>'.$s['no_so'].'</td>';
                echo '<td>'.$s['nama_budget'].'</td>';
                echo '<td>'.$s['nama_pelanggan'].'</td>';
                echo '<td>'.angkaRibuan($s['jumlah']).'</td>';
                echo '<td>'.angkaRibuan($s['harga_jual']).'</td>';
                echo '<td>'.angkaRibuan($s['harga_total']).'</td>';
                echo '<td>'.angkaRibuan($s['nominal_bayar']).'</td>';
                echo '<td>'.$lampiran_str.'</td>';
                echo '<td>'.implode('',$keterangan_so).'</td>';
                echo '<td>'.$s['no_kendaraan'].'</td>';
                echo '<td>'.$s['nama_sopir'].'</td>';
                echo '<td>'.convertElemenTglWaktuIndonesia($s['tgl_buat']).'</td>';
                if(!empty($s['tgl_verifikasi'])){
                    echo '<td>'.convertElemenTglWaktuIndonesia($s['tgl_verifikasi']).'</td>';
                    echo '<td>'.$s['user_verifikasi'].'</td>';
                }else{
                    echo '<td></td>';
                    echo '<td></td>';
                }
                if(!empty($s['tgl_realisasi'])){
                    $keterangan_sj = array(
                        '<div>'.$s['user_verifikasi'].' - Diverifikasi, '.convertElemenTglWaktuIndonesia($s['tgl_verifikasi_security']).'</div>',
                        '<div>'.$s['user_realisasi'].' - Dicetak, '.convertElemenTglWaktuIndonesia($s['tgl_realisasi']).'</div>'
                    );
                    echo '<td class="link_span"><a target="_blank" href="'.site_url('sales_order/realisasi_penjualan/cetakSJ?no_sj='.$s['no_sj']).'">'.$s['no_sj'].'</a></td>';
                    echo '<td>'.angkaRibuan($s['jumlah']).'</td>';
                    echo '<td>'.implode('',$keterangan_sj).'</td>';
                }else{
                    echo '<td></td>';
                    echo '<td></td>';
                    echo '<td></td>';
                }
                
                echo '</tr>';
                    
                if(isset($phd[$no_ph])){
                    echo '<tr class="hide detail_hidden">';
                    echo '<td colspan="3"></td>';    
                    echo '<td colspan="6">';
                    echo '<table class="table table-bordered custom_table">
                            <thead>
                                <tr class="bg_biru">
                                    <td colspan="3"><label>Item Pengajuan Harga</label></td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Harga Jual / Sak (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>';
                            foreach($phd[$no_ph] as $_pd){
                                echo '<tr>
                                    <td>'.$_pd['nama_budget'].'</td>
                                    <td>'.angkaRibuan($_pd['harga_jual']).'</td>
                                </tr>';
                            }
                                
                        echo '        
                            </tbody>	
                        </table>';    
                
                    echo '</td>';
                    echo '</tr>';
                }
            }
        }    
        ?>
        </tbody>
    </table>
</div>    