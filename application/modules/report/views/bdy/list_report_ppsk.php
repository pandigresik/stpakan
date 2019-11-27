
<small>
<table class="table table-bordered custom_table" id="table-list-report-detail" style="width:1100px">
     <thead>
       <tr>
         <th class="farm page0" rowspan="4">Tanggal Rilis</th>
         <th class="siklus page0" rowspan="4">Stok Glangsing Tersedia</th>
         <th class="siklus page0" rowspan="4">Glangsing Kembali Ke Gudang</th>
         <!-- <th class="siklus page0" rowspan="4">Glangsing Kembali<br>ke gudang</th> -->
         <th class="pakan page0" colspan="7">Pemakaian</th>
         <th class="stok_akhir page0" rowspan="4">Stok Akhir</th>
         <th class="aksi page0" rowspan="4">
             Aksi<br>
             <input type="checkbox" id="check_all_ppsk" onclick="KSG.check_all_ppsk(this)">
         </th>
         <th class="keterangan page0" rowspan="4">Keterangan</th>
       </tr>
       <tr>
           <th class="pakan_terima page0" colspan="4">Internal</th>
           <th class="pakan_pakai page0" colspan="3">Eksternal</th>
       </tr>
       <tr>
           <th class="budget_internal_1 page0" colspan="2">Bangkai</th>
           <th class="budget_internal_2 page0" colspan="2">Sekam gumpal basah</th>
           <th class="budget_eksternal_1 page0" colspan="3">Pupuk (Kotoran yang dijual)</th>
       </tr>
       <tr>
           <th class="pemakaian_internal_1_1 page0">Realisasi</th>
           <th class="pemakaian_internal_1_2 page0">Over Budget</th>
           <th class="pemakaian_internal_2_1 page0">Realisasi</th>
           <th class="pemakaian_internal_2_2 page0">Over Budget</th>
           <th class="pemakaian_eksternal_1_1 page0">Realisasi</th>
           <th class="pemakaian_eksternal_1_2 page0">Over Budget</th>
           <th class="harga_eksternal_1_1 page0">Harga Jual (Rp)</th>
       </tr>
       <?php
          $total_budget_I_GB = 0;
          $count_over_I_GB = 0;
          $total_budget_I_GS = 0;
          $count_over_I_GS = 0;
          $total_budget_E_GP = 0;
          $count_over_E_GP = 0;

		  $budget_I_GB = 0;
		  $budget_I_GS = 0;
		  $budget_E_GP = 0;
          foreach ($data_list_ppsk as $k_ppsk => $v_ppsk){
              $total_budget_I_GB += $v_ppsk['pakaiReal_I_GB'];
			  $budget_I_GB = $v_ppsk['budget_I_GB'];
              if($v_ppsk['pakaiOver_I_GB'] > 0){
                  $count_over_I_GB++;
              }
              $total_budget_I_GS += $v_ppsk['pakaiReal_I_GS'];
			  $budget_I_GS = $v_ppsk['budget_I_GS'];
              if($v_ppsk['pakaiOver_I_GS'] > 0){
                  $count_over_I_GS++;
              }
              $total_budget_E_GP += $v_ppsk['pakaiReal_E_GP'];
			  $budget_E_GP = $v_ppsk['budget_E_GP'];
              if($v_ppsk['pakaiOver_E_GP'] > 0){
                  $count_over_E_GP++;
              }
          }
       ?>

       <tr>
           <th colspan="3" align="center"><b>Sisa Budget</b></th>
           <th colspan="2" align="center"><?php echo ($total_budget_I_GB <= $budget_I_GB ? $budget_I_GB - $total_budget_I_GB : 0) ?></th>
           <th colspan="2" align="center"><?php echo ($total_budget_I_GS <= $budget_I_GS ? $budget_I_GS - $total_budget_I_GS : 0) ?></th>
           <th colspan="3" align="center"><?php echo ($total_budget_E_GP <= $budget_E_GP ? $budget_E_GP - $total_budget_E_GP : 0) ?></th>
           <th colspan="3" align="center"></th>
       </tr>
       <tr>
           <th colspan="3" align="center"><b>Total Pakai</b></th>
           <th colspan="2" align="center"><?php echo ($total_budget_I_GB > $budget_I_GB ? '<span style="color:red">'.$total_budget_I_GB.'</span>' : $total_budget_I_GB)?></th>
           <th colspan="2" align="center"><?php echo ($total_budget_I_GS > $budget_I_GS ? '<span style="color:red">'.$total_budget_I_GS.'</span>' : $total_budget_I_GS)?></th>
           <th colspan="3" align="center"><?php echo ($total_budget_E_GP > $budget_E_GP ? '<span style="color:red">'.$total_budget_E_GP.'</span>' : $total_budget_E_GP)?></th>
           <th colspan="3" align="center"></th>
       </tr>
     </thead>
     <tbody style="height:100px; overflow:scroll">

         <?php foreach ($data_list_ppsk as $k_ppsk => $v_ppsk):?>
             <tr data-tglbuat = '<?php echo $v_ppsk['tglBuat']?>'>
                 <td class="tglBuat page0 tanggal"><?php echo tglIndonesia($v_ppsk['tglBuat'])?></td>
                 <td class="stokGlangsing page0"><?php echo $v_ppsk['stokGlangsing']?></td>
                 <td class="glangsingKembali page0"><?php echo $v_ppsk['glangsingKembali']?></td>
                 <td class="pakaiReal_I_GB page0 unit"><?php echo ($v_ppsk['pakaiOver_I_GB'] > 0 ? '<span style="color:red">'.$v_ppsk['pakaiReal_I_GB'].'</span>' : $v_ppsk['pakaiReal_I_GB']);?></td>
                 <td class="pakaiOver_I_GB page0 unit"><?php echo $v_ppsk['pakaiOver_I_GB']?></td>
                 <td class="pakaiReal_I_GS page0 unit"><?php echo ($v_ppsk['pakaiOver_I_GS'] > 0 ? '<span style="color:red">'.$v_ppsk['pakaiReal_I_GS'].'</span>' : $v_ppsk['pakaiReal_I_GS']);?></td>
                 <td class="pakaiOver_I_GS page0 unit"><?php echo $v_ppsk['pakaiOver_I_GS']?></td>
                 <td class="pakaiReal_E_GP page0 unit"><?php echo ($v_ppsk['pakaiOver_E_GP'] > 0 ? '<span style="color:red">'.$v_ppsk['pakaiReal_E_GP'].'</span>' : $v_ppsk['pakaiReal_E_GP']);?></td>
                 <td class="pakaiOver_E_GP page0 unit"><?php echo $v_ppsk['pakaiOver_E_GP']?></td>
                 <td class="harga_E_GP page0 unit"><?php echo $v_ppsk['harga_E_GP']?></td>
                 <td class="stokAkhir page0 unit"><?php echo $v_ppsk['stokAkhir']?></td>
                 <td class="statusApproval page0">
                     <?php
                         echo $button_ppsk[$level_user][$v_ppsk['status']];
                     ?>
                 </td>
                 <td class="keterangan page0" style="width:400px;text-align:left;"><?php echo $v_ppsk['keterangan']?></td>

             </tr>
         <?php endforeach; ?>
     </tbody>
</table>
</small>
