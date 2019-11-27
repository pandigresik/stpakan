
<small>
<style type="text/css">
div.scroll {
    width:1105px;
    height: 500px;
    overflow: scroll;
}
</style>
<div class="scroll">
<table class="table table-bordered custom_table" id="table-list-report-detail" style="width:1100px">
     <thead>
       <tr>
         <th class="farm page0" rowspan="3">Tanggal Rilis</th>
         <th class="siklus page0" rowspan="3">Stok Glangsing Tersedia</th>
         <th class="siklus page0" rowspan="3">Glangsing Kembali Ke Gudang</th>      
         <th class="pakan page0" colspan="6">Pemakaian</th>
         <th class="stok_akhir page0" rowspan="3">Stok Akhir</th>
      
       </tr>
       <tr>
           <th class="budget1 page0" colspan="2">Bangkai</th>
           <th class="budget2 page0" colspan="2">Sekam gumpal basah</th>
           <th class="budget3 page0" colspan="2">Pupuk (Kotoran yang dijual)</th>
       </tr>
       <tr>
           <th class="pemakaian1_1 page0">realisasi</th>
           <th class="pemakaian1_2 page0">over Budget</th>
           <th class="pemakaian2_1 page0">realisasi</th>
           <th class="pemakaian2_2 page0">over Budget</th>
           <th class="pemakaian3_1 page0">realisasi</th>
           <th class="pemakaian3_2 page0">over Budget</th>
           <!--th class="harga_eksternal_1_1 page0">Harga Jual (Rp)</th-->
       </tr>
       <?php
          $total_budget_GB = 0;
          $count_over_GB = 0;
          $total_budget_GS = 0;
          $count_over_GS = 0;
          $total_budget_GP = 0;
          $count_over_GP = 0;

          $budget_GB = 0;
          $budget_GS = 0;
          $budget_GP = 0;

          //cetak_r($data_list_date);
          foreach ($data_list_date as $k_ppsk => $v_ppsk){
              $total_budget_GB += $v_ppsk['pakaireal_GB'];
              $budget_GB = $v_ppsk['budget_GB'];
              if($v_ppsk['pakaiover_GB'] > 0){
                  $count_over_GB++;
              }
              $total_budget_GS += $v_ppsk['pakaireal_GS'];
              $budget_GS = $v_ppsk['budget_GS'];
              if($v_ppsk['pakaiover_GS'] > 0){
                  $count_over_GS++;
              }
              $total_budget_GP += $v_ppsk['pakaireal_GP'];
              $budget_GP = $v_ppsk['budget_GP'];
              if($v_ppsk['pakaiover_GP'] > 0){
                  $count_over_GP++;
              }
          }
       ?>

       <tr>
           <th colspan="3" align="center"><b>Sisa Budget</b></th>
           <th colspan="2" align="center"><?php echo ($total_budget_GB <= $budget_GB ? $budget_GB - $total_budget_GB : 0) ?></th>
           <th colspan="2" align="center"><?php echo ($total_budget_GS <= $budget_GS ? $budget_GS - $total_budget_GS : 0) ?></th>
           <th colspan="3" align="center"><?php echo ($total_budget_GP <= $budget_GP ? $budget_GP - $total_budget_GP : 0) ?></th>
       </tr>
       <tr>
           <th colspan="3" align="center"><b>Total Pakai</b></th>
           <th colspan="2" align="center"><?php echo ($total_budget_GB > $budget_GB ? '<span style="color:red">'.$total_budget_GB.'</span>' : $total_budget_GB)?></th>
           <th colspan="2" align="center"><?php echo ($total_budget_GS > $budget_GS ? '<span style="color:red">'.$total_budget_GS.'</span>' : $total_budget_GS)?></th>
           <th colspan="3" align="center"><?php echo ($total_budget_GP > $budget_GP ? '<span style="color:red">'.$total_budget_GP.'</span>' : $total_budget_GP)?></th>
       </tr>
     </thead>
     <tbody style="height:100px; overflow:scroll">

         <?php foreach ($data_list_date as $k_ppsk => $v_ppsk):?>
             <tr data-tglbuat = '<?php echo $v_ppsk['tgl_kebutuhan']?>'>
                 <td class="tglBuat page0 tanggal" style="color:#0000FF;"><u><?php echo tglIndonesia($v_ppsk['tgl_kebutuhan'])?></u></td>
                 <td class="stokGlangsing page0"><?php echo $v_ppsk['stokglangsing']?></td>
                 <td class="glangsingKembali page0"><?php echo $v_ppsk['glangsingkembali']?></td>
                 <td class="pakaireal_GB page0 unit"><?php echo ($v_ppsk['pakaiover_GB'] > 0 ? '<span style="color:red">'.$v_ppsk['pakaireal_GB'].'</span>' : $v_ppsk['pakaireal_GB']);?></td>
                 <td class="pakaiover_GB page0 unit"><?php echo $v_ppsk['pakaiover_GB']?></td>
                 <td class="pakaireal_GS page0 unit"><?php echo ($v_ppsk['pakaiover_GS'] > 0 ? '<span style="color:red">'.$v_ppsk['pakaireal_GS'].'</span>' : $v_ppsk['pakaireal_GS']);?></td>
                 <td class="pakaiover_GS page0 unit"><?php echo $v_ppsk['pakaiover_GS']?></td>
                 <td class="pakaireal_GP page0 unit"><?php echo ($v_ppsk['pakaiover_GP'] > 0 ? '<span style="color:red">'.$v_ppsk['pakaireal_GP'].'</span>' : $v_ppsk['pakaireal_GP']);?></td>
                 <td class="pakaiover_GP page0 unit"><?php echo $v_ppsk['pakaiover_GP']?></td>
                 
                 <td class="stokAkhir page0 unit"><?php echo $v_ppsk['stokakhir']?></td>
                 

             </tr>
             <tr>
               <td colspan="11">
                  <table class="table table-bordered custom_table" id="table-list-report-ppsk" style="width:950px;margin-left: 100px">
                    <thead>
                      <tr>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:100px">No. Permintaan</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:150px">Kategori</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:150px">Budget Tersedia</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:60px">Jml Sak Diminta</th>
                        <th class="farm" colspan="2" style="background:#C0C0C0">over Budget</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:100px">Terpakai</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:100px">Sisa Budget</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:100px">Aksi</th>
                        <th class="farm page0" rowspan="2" style="background:#C0C0C0;width:200px">Keterangan</th>
                      </tr>
                      <tr>
                        <th class="farm page0" style="background:#C0C0C0;width:60px">Jml Sak</th>
                        <th class="farm page0" style="background:#C0C0C0;width:150px">Alasan</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $no_ppsk = '';
                      $keterangan = '';
                      $status = '';
                      foreach ($data_list_ppsk as $key => $val){
                        if($v_ppsk['tgl_kebutuhan'] == $val['tgl_kebutuhan']){
                          if($no_ppsk != $val['no_ppsk']){
                            if($keterangan != ''){
                              ?>
                              <td class="alasan"><?php echo $val['alasan']?></td>
                              <td class="terpakai"><?php echo $v_ppsk['pakaireal_'.$kodeBudget] ?></td>
                              <td class="sisa_budget"><?php echo $v_ppsk['budgetTersedia_'.$kodeBudget] ?></td>
                              <td class="statusApproval ">
                                <?php
                                  if($level_user == 'KDV'){
                                    echo $button_ppsk[$level_user][$status];
                                    /*
                                    if($val['jml_over_budget'] > 0){
                                      echo $button_ppsk[$level_user][$status];
                                    }else{
                                      echo $button_ppsk['KF'][$status];
                                    }*/
                                  }else{
                                    echo $button_ppsk[$level_user][$status];                                    
                                  }
                                ?>
                              </td>
                              <td class="keterangan"><?php echo $keterangan?></td></tr>
                              <?php
                            }
                            $keterangan = '';
                            $no_ppsk = $val['no_ppsk'];
                            $kodeBudget = $val['kode_budget'];
                            ?>
                              <tr data-no_ppsk='<?php echo $val['no_ppsk']?>'>
                              <td class="no_ppsk"><?php echo $val['no_ppsk']?></td>
                              <td class="nama_budget"><?php echo $val['nama_budget']?></td>
                              <td class="budget_tersedia"><?php echo ($v_ppsk['budgetTersedia_'.$kodeBudget] + $v_ppsk['pakaireal_'.$kodeBudget]) ?></td>
                              <td class="jml_diminta"><?php echo $val['jml_diminta']?></td>
                              <td class="jml_over_budget"><?php echo $val['jml_over_budget']?></td>                             
                            <?php
                          }

                          $status = $val['status'];
                          $keterangan .= '<div style="text-align:left">['.$val['nama_pegawai'].'] - '.$val['status_text'].', '.convertElemenTglWaktuIndonesia($val['tgl_buat']).'</div>';
                          $keterangan .= ($val['keterangan'] != '') ? '<div style="text-align:left;color:#ff0000">('.$val['keterangan'].')</div>' : '';
                      ?>
                        
                      
                      <?php 
                          }
                        }
                        if($keterangan != ''){
                          ?>
                          <td class="alasan"><?php echo $val['alasan']?></td>
                          <td class="terpakai"><?php echo $v_ppsk['pakaireal_'.$kodeBudget] ?></td>
                          <td class="sisa_budget"><?php echo $v_ppsk['budgetTersedia_'.$kodeBudget] ?></td>
                          <td class="statusApproval ">
                            <?php
                              if($level_user == 'KDV'){
                                if($val['jml_over_budget'] > 0){
                                  echo $button_ppsk[$level_user][$status];
                                }else{
                                  echo $button_ppsk['KF'][$status];
                                }
                              }else{
                                echo $button_ppsk[$level_user][$status];                                    
                              }
                            ?>
                          </td>
                          <td class="keterangan"><?php echo $keterangan?></td></tr>
                          <?php
                        }
                       ?>
                    </tbody>
                  </table>
               </td>
             </tr>
         <?php endforeach; ?>
     </tbody>
</table>
</div>
</small>
