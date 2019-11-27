<?php 
$perOrder = array();
?>
<div class="panel panel-default">
    <div class="panel-heading">Detail Permintaan Barang per Kandang</div>
    <div class="panel-body">       
            <table class="table table-bordered fixed-header">
                <thead>
                    <tr>
                        <th>Flock</th>
                        <th>Kandang</th>                                                
                        <th>Tgl Kirim</th>
                        <th>Tgl Kebutuhan</th>
                        <th>Jumlah Permintaan</th>                                                
                        <th>No. Pengambilan</th>                        
                    </tr>
                </thead>
                <tbody>                        
                   <?php 
                    if (!empty($list)) {
                        /** grouping tlgkirim dan kode kandang */
                        $kandangKirim = array();
                        foreach ($list as $l) {
                            $no_order = $l['no_order'];
                            $flok = $l['flok_bdy'];
                            $tglKirim = $l['tgl_kirim'];
                            if (!isset($kandangKirim[$tglKirim])) {
                                $kandangKirim[$tglKirim] = array();
                            }

                            if (!isset($kandangKirim[$tglKirim][$flok])) {
                                $kandangKirim[$tglKirim][$flok] = array(
                                    'no_order' => $l['no_order'],
                                    'kode_kandang' => $l['kode_kandang'],
                                    'tgl_kebutuhan' => $l['tgl_kebutuhan'],
                                    'jml_permintaan' => 0,
                                    'generate' => $l['generate'],
                                );
                            }
                            $kandangKirim[$tglKirim][$flok]['jml_permintaan'] += $l['jml_permintaan'];

                            if (!empty($no_order)) {
                                if (!isset($perOrder[$no_order])) {
                                    $perOrder[$no_order] = $l;
                                } else {
                                    $perOrder[$no_order]['jml_permintaan'] += $l['jml_permintaan'];
                                    $perOrder[$no_order]['jml_dropping'] += $l['jml_dropping'];
                                }
                            }
                        }

                        foreach ($kandangKirim as $tglKirim => $arr) {
                            foreach ($arr as $fl => $l) {
                                $no_order = $l['no_order'];
                                $span_order = '-';
                                if (empty($no_order)) {
                                    if ($l['generate']) {
                                        $span_order = '<span style="color: #428bca;" data-kode-farm="'.$kode_farm.'" data-kode-flok="'.$fl.'"  data-tanggal-kebutuhan="'.$l['tgl_kebutuhan'].'" href="#" class="btn link" onclick="Pengambilan.generate(this)">Generate</span>';
                                    } else {
                                        $span_order = '-';
                                    }
                                } else {
                                    $span_order = $no_order;
                                }

                                echo '<tr>
                                    <td>'.$fl.'</td>
                                    <td>'.$l['kode_kandang'].'</td>
                                    <td><span class="link_span">'.convertElemenTglWaktuIndonesia($tglKirim).'</span></td>
                                    <td>'.convertElemenTglWaktuIndonesia($l['tgl_kebutuhan']).'</td>
                                    <td>'.$l['jml_permintaan'].'</td>
                                    <td>'.$span_order.'</td>
                                </tr>';
                            }
                        }
                    }
                   ?>
                </tbody>
            </table>                  
    </div>
</div>            
<div class="panel panel-default">
    <div class="panel-heading">Summary Permintaan Barang per Flok</div>
    <div class="panel-body">       
            <table id="summary_picking_list" class="table table-bordered fixed-header">
                <thead>
                    <tr>
                        <th>No. Pengambilan</th>                        
                        <th>Flock</th>                                                
                        <th>Tgl Kirim</th>
                        <th>Tgl Kebutuhan</th>
                        <th>Jumlah Permintaan</th>                                                
                        <th>Jumlah Dropping</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>                        
                   <?php 
                    if (!empty($perOrder)) {
                        foreach ($perOrder as $l) {
                            echo '<tr ondblclick="Pengambilan.get_data_detail_pengambilan(this)">
                                <td data-kode_farm="'.$kode_farm.'" data-no_order="'.$l['no_order'].'">'.$l['no_order'].'</td>
                                <td data-flok="'.$l['flok_bdy'].'" class="flok">'.$l['flok_bdy'].'</td>
                                <td><span class="link_span">'.convertElemenTglWaktuIndonesia($l['tgl_kirim']).'</span></td>                                                                
                                <td>'.convertElemenTglWaktuIndonesia($l['tgl_kebutuhan']).'</td>
                                <td class="jml_permintaan">'.$l['jml_permintaan'].'</td>
                                <td class="jml_dropping">'.$l['jml_dropping'].'</td>
                                <td><span class="link_span" onclick="Pengambilan.cetak_picking_list(this)">Cetak Picking List</span></td>
                            </tr>';
                        }
                    }
                   ?>
                </tbody>
            </table>  
         
        
    </div>
</div>            

      
