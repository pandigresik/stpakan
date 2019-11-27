<small>
<table class="table table-bordered custom_table" id="table-list-report-kpah">
     <thead>
       <tr>
         <th class="farm page0" rowspan="3">farm</th>
         <th class="siklus page0" rowspan="3">Siklus</th>
         <th class="pakan page0" colspan="3">Pakan</th>
         <th class="glangsing batas_kanan page0" colspan="4">Glangsing</th>         
         <th class="jenis_barang page0" rowspan="3">Jenis Barang</th>
         <th class="budget page1" rowspan="3" >Budget</th>
         <th class="pemakaian page1" colspan="6">Pengajuan Permintaan Glangsing</th>
         <th class="budget page2" rowspan="3">Total Pakai</th>
         <th class="pemakaian_total_pemusnahan page2" rowspan="3">Total Pemusnahan</th>
         <th class="stk_glangsing_tersedia page2" rowspan="3">Stok tersedia</th>
         <th class="jual_glangsing page2" rowspan="3">Total Penjualan Glangsing</th>
         <th class="stok_akhir page2" rowspan="3">Stok Akhir</th>
         <th class="aksi page2" rowspan="3">Aksi</th>
         <th class="keterangan page2" rowspan="3">Keterangan</th>
       </tr>
       <tr>
           <th class="pakan_terima page0" rowspan="2">Terima</th>
           <th class="pakan_pakai page0" rowspan="2">Pakai</th>
           <th class="pakan_sisa page0" rowspan="2">Sisa</th>
           <th class="glangsing_saldo_awal page0" rowspan="2">Saldo Awal</th>
           <th class="glangsing_kembali page0" rowspan="2">Kembali ke<br>gudang</th>
           <th class="glangsing_total page0" rowspan="2">Total Stok</th>
           <th class="glangsing_belum_kembali batas_kanan page0" rowspan="2">Belum kembali<br>ke gudang</th>                      

           <th class="budget page1" colspan="2">Permintaan</th>
           <th class="budget page1" rowspan="2">Pengambilan</th>
           <th class="budget page1" rowspan="2">Kembali</th>
           <th class="budget page1" rowspan="2">Total Pakai</th>                      
       </tr>
       <tr>
           <th class="pemakaian_internal_1_1 page1">Realisasi</th>
           <th class="pemakaian_internal_1_2 page1">Over Budget</th>
       </tr>
     </thead>
     <tbody id="main_tbody">
         <?php           
         $str = '';         
         foreach ($data_list_ksg as $kode_siklus => $barang){
            $count = 0;
            $rowspan = count($barang);
            if(!empty($barang)){
              foreach($barang as $val){                
                if(!$count){
                  $str .= '<tr data-kodesiklus = "'.$kode_siklus.'">';
                  $str .= '<td class="farm page0" style="width:125px;" rowspan="'.$rowspan.'">
                  <i class="glyphicon glyphicon-plus" data-saldoawal="'.$val['glangsingSaldoAwal'].'" 
                  data-siklus="'.$val['kode_siklus'].'" onclick="KontrolStokGlangsing.getDetail(this);"> </i> 
                          '.$val['nama_farm'].'</td>';
                  $str .= '<td class="siklus page0" rowspan="'.$rowspan.'">'.$val['periode'].'<sup style="color:red" rowspan="'.$rowspan.'">'.$val['jmlOutstanding'].'</sup></td>';
                  $str .= '<td class="pakan_terima page0" rowspan="'.$rowspan.'">'.$val['pakanTerima'].'</td>';
                  $str .= '<td class="pakan_pakai page0" rowspan="'.$rowspan.'">'.$val['pakanPakai'].'</td>';
                  $str .= '<td class="pakan_sisa page0" rowspan="'.$rowspan.'">'.$val['pakanSisa'].'</td>';
                  $str .= '<td class="glangsing_saldo_awal page0" rowspan="'.$rowspan.'">'.$val['glangsingSaldoAwal'].'</td>';
                  $str .= '<td class="glangsing_kembali page0" rowspan="'.$rowspan.'">'.$val['glangsingKembali'].'</td>';
                  $str .= '<td class="glangsing_total page0" rowspan="'.$rowspan.'">'.$val['glangsingTotalStok'].'</td>';
                  $str .= '<td class="glangsing_belum_kembali batas_kanan page0" rowspan="'.$rowspan.'">'.$val['glangsingBelumKembali'].'</td>';                                                  
                }
                if($count){
                  $str .= '<tr data-kodesiklus = "'.$kode_siklus.'">';
                }
                $str .= '<td class="jenis_barang page0">'.$val['nama_barang'].'</td>';
                $str .= '<td class="budget page1">'.$val['jml_budget'].'</td>';
                $str .= '<td class="jml_diminta page1">'.$val['jml_diminta'].'</td>';
                $str .= '<td class="jml_over_budget page1">'.$val['jml_over_budget'].'</td>';
                $str .= '<td class="jml_terima page1">'.$val['jml_terima'].'</td>';
                $str .= '<td class="jml_kembali page1">'.$val['jml_kembali'].'</td>';            
                $str .= '<td class="jml_dipakai page1">'.$val['jml_dipakai'].'</td>';
                $str .= '<td class="jml_dipakai page2">'.$val['jml_dipakai'].'</td>';                
                $str .= '<td class="pemakaian_total_pemusnahan page2">'.$val['totalMusnah'].'</td>';
                $str .= '<td class="stk_glangsing_tersedia page2" >'.$val['glangsingTersedia'].'</td>';
                $str .= '<td class="jual_glangsing page2">'.$val['jualGlangsing'].'</td>';
                $str .= '<td class="stok_akhir page2">'.$val['glangsingStokAkhir'].'</td>';
                $str .= '<td class="aksi page2">'.$button_ksg[$level_user][$val['status2']].'</td>';
                $str .= '<td class="keterangan page2">'.$val['keterangan'].'</td>';                
                $str .= '</tr>';
                $count++;
                if($rowspan == $count){
                  $str .= '<tr id="table-detail'.$kode_siklus.'"></tr>';
                }
                
              }
            }
         }

          echo $str;
                                           
          ?>
     </tbody>
</table>
</small>
