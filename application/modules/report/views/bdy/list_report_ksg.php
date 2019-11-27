<small>
<table class="table table-bordered custom_table" id="table-list-report-kpah">
     <thead>
       <tr>
         <th class="farm page0" rowspan="4">farm</th>
         <th class="siklus page0" rowspan="4">Siklus</th>
         <th class="pakan page0" colspan="3">Pakan</th>
         <th class="glangsing batas_kanan page0" colspan="5">Glangsing</th>
         <th class="budget page1" colspan="3">Budget</th>
         <th class="pemakaian page1" colspan="7">Pemakaian</th>
         <th class="pemakaian page2" colspan="2">Pemakaian</th>
         <th class="stk_glangsing_tersedia page2" rowspan="4">Stok glangsing tersedia</th>
         <th class="approval_harga page2" colspan="2">Approval harga</th>
         <th class="stok_akhir page2" rowspan="4">Stok Akhir</th>
         <th class="aksi page2" rowspan="4">Aksi</th>
         <th class="keterangan page2" rowspan="4">Keterangan</th>
       </tr>
       <tr>
           <th class="pakan_terima page0" rowspan="3">Terima</th>
           <th class="pakan_pakai page0" rowspan="3">Pakai</th>
           <th class="pakan_sisa page0" rowspan="3">Sisa</th>
           <th class="glangsing_saldo_awal page0" rowspan="3">Saldo Awal</th>
           <th class="glangsing_sisa_penjualan page0" rowspan="3">Sisa Penjualan</th>
           <th class="glangsing_kembali page0" rowspan="3">Kembali ke gudang</th>
           <th class="glangsing_total page0" rowspan="3">Total Stok</th>
           <th class="glangsing_belum_kembali batas_kanan page0" rowspan="3">Belum kembali ke gudang</th>
           <th class="budget_internal page1" colspan="2">Internal</th>
           <th class="budget_eksternal page1" colspan="1">Eksternal</th>
           <th class="pemakaian_internal page1" colspan="4">Internal</th>
           <th class="pemakaian_eksternal page1" colspan="3">Eksternal</th>
           <th class="pemakaian_total_internal page2" rowspan="3">Total internal</th>
           <th class="pemakaian_total_eksternal page2" rowspan="3">Total eksternal</th>
           <th class="pengajuan_penjualan_glangsing page2" colspan="2">Pengajuan penjualan glangsing</th>
       </tr>
       <tr>
           <th class="budget_internal_1 page1" rowspan="2">Bangkai</th>
           <th class="budget_internal_2 page1" rowspan="2">Sekam gumpal basah</th>
           <th class="budget_eksternal_1 page1" rowspan="2">Pupuk (Kotoran yang dijual)</th>
           <th class="pemakaian_internal_1 page1" colspan="2">Bangkai</th>
           <th class="pemakaian_internal_2 page1" colspan="2">Sekam gumpal basah</th>
           <th class="pemakaian_eksternal_1 page1" colspan="3">Pupuk (Kotoran yang dijual)</th>
           <th class="pengajuan_jumlah page2" rowspan="2">Jumlah</th>
           <th class="pengajuan_harga page2" rowspan="2">Harga (Rp/Sak)</th>
       </tr>
       <tr>
           <th class="pemakaian_internal_1_1 page1">Realisasi</th>
           <th class="pemakaian_internal_1_2 page1">Over Budget</th>
           <th class="pemakaian_internal_2_1 page1">Realisasi</th>
           <th class="pemakaian_internal_2_2 page1">Over Budget</th>
           <th class="pemakaian_eksternal_1_1 page1">Realisasi</th>
           <th class="pemakaian_eksternal_1_2 page1">Over Budget</th>
           <!-- <th class="pemakaian_eksternal_1_3 page1">harga (Rp/Sak)</th> -->
       </tr>
     </thead>
     <tbody id="main_tbody">
         <?php foreach ($data_list_ksg as $k_ksg => $v_ksg):?>
             <tr data-kodesiklus = '<?php echo $v_ksg['KODE_SIKLUS']?>'>
                 <td class="farm page0" style="width:125px;"><i class="glyphicon glyphicon-plus" data-saldoawal="<?php echo $v_ksg['glangsingSaldoAwal']?>" data-siklus="<?php echo $v_ksg['KODE_SIKLUS']?>" onclick="KSG.getDetail(this);"> </i> <?php echo $v_ksg['nama_farm']?></td>
                 <td class="siklus page0"><?php echo $v_ksg['periode'].'<sup style="color:red">'.$v_ksg['jmlOutstanding'].'</sup>'?></td>
                 <td class="pakan_terima page0"><?php echo $v_ksg['pakanTerima']?></td>
                 <td class="pakan_pakai page0"><?php echo $v_ksg['pakanPakai']?></td>
                 <td class="pakan_sisa page0"><?php echo $v_ksg['pakanSisa']?></td>
                 <td class="glangsing_saldo_awal page0"><?php echo $v_ksg['glangsingSaldoAwal']?></td>
                 <td class="glangsing_sisa_penjualan page0"><?php echo $v_ksg['glangsingSisaJual']?></td>
                 <td class="glangsing_kembali page0"><?php echo $v_ksg['glangsingKembali']?></td>
                 <td class="glangsing_total page0"><?php echo $v_ksg['glangsingTotalStok']?></td>
                 <td class="glangsing_belum_kembali batas_kanan page0"><?php echo $v_ksg['glangsingBelumKembali']?></td>
                 <td class="budget_internal_1 page1"><?php echo $v_ksg['budget_I_GB']?></td>
                 <td class="budget_internal_2 page1"><?php echo $v_ksg['budget_I_GS']?></td>
                 <td class="budget_eksternal_1 page1"><?php echo $v_ksg['budget_E_GP']?></td>
                 <td class="pemakaian_internal_1_1 page1"><?php echo ($v_ksg['pakaiOver_I_GB'] > 0 ? '<span style="color:red">'.$v_ksg['pakaiReal_I_GB'].'</span>' : $v_ksg['pakaiReal_I_GB']);?></td>
                 <td class="pemakaian_internal_1_2 page1"><?php echo $v_ksg['pakaiOver_I_GB']?></td>
                 <td class="pemakaian_internal_2_1 page1"><?php echo ($v_ksg['pakaiOver_I_GS'] > 0 ? '<span style="color:red">'.$v_ksg['pakaiReal_I_GS'].'</span>' : $v_ksg['pakaiReal_I_GS']);?></td>
                 <td class="pemakaian_internal_2_2 page1"><?php echo $v_ksg['pakaiOver_I_GS']?></td>
                 <td class="pemakaian_eksternal_1_1 page1"><?php echo ($v_ksg['pakaiOver_E_GP'] > 0 ? '<span style="color:red">'.$v_ksg['pakaiReal_E_GP'].'</span>' : $v_ksg['pakaiReal_E_GP']);?></td>
                 <td class="pemakaian_eksternal_1_2 page1"><?php echo $v_ksg['pakaiOver_E_GP']?></td>
                 <!-- <td class="pemakaian_eksternal_1_3 page1"><?php if($v_ksg['STATUS2'] == 'D' || $v_ksg['STATUS2'] == 'RJ') echo "<input style='font-size:8pt;' id='harga_E' class='form-control hide' value = 0 data-id = 'GP' data-kodesiklus = ".$v_ksg['KODE_SIKLUS']." type='text' >"; else echo $v_ksg['pakaiHargaJual_E_GP']?></td> -->
                 <td class="pemakaian_total_internal page2"><?php echo $v_ksg['totalPakai_I']?></td>
                 <td class="pemakaian_total_eksternal page2"><?php echo $v_ksg['totalPakai_E']?></td>
                 <td class="stk_glangsing_tersedia page2"><?php echo $v_ksg['stokGlangsingTersedia']?></td>
                 <td class="pengajuan_jumlah page2"><?php if(($v_ksg['STATUS2'] == 'D' || $v_ksg['STATUS2'] == 'RJ') && $level_user == 'KF') echo "<input style='font-size:8pt;' data-pengajuan = ".$v_ksg['pengajuan_sak_dijual']." id='jml_pengajuan' class='form-control hide' value = 0 data-kodesiklus = ".$v_ksg['KODE_SIKLUS']." type='text' >"; else echo $v_ksg['pengajuan_sak_dijual']?></td>
                 <td class="pengajuan_harga page2"><?php if(($v_ksg['STATUS2'] == 'D' || $v_ksg['STATUS2'] == 'RJ') && $level_user == 'KF') echo "<input style='font-size:8pt;' id='harga_pengajuan' data-harga = ".$v_ksg['pengajuan_harga_sak']." class='form-control hide' value = 0 data-kodesiklus = ".$v_ksg['KODE_SIKLUS']." type='text' >"; else echo $v_ksg['pengajuan_harga_sak']?></td>
                 <td class="stok_akhir page2"><?php echo $v_ksg['StokAkhir']?></td>
                 <td class="aksi page2">
                    <?php
                        echo $button_ksg[$level_user][$v_ksg['STATUS2']];
                    ?>
                 </td>
                 <td class="keterangan page2">
                    <?php
                        echo $v_ksg['keterangan'];
                    ?>
                 </td>
             </tr>
             <tr id="table-detail<?php echo $v_ksg['KODE_SIKLUS']?>">
             </tr>
         <?php endforeach; ?>
     </tbody>
</table>
</small>
