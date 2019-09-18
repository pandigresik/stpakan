<?php $metodeTimbangan = $lockTimbangan ? 'onfocus="Home.getDataTimbang(this)" readonly' : '' ?>
                            <div class="panel panel-default" id="riwayat-pengambilan-pakan">
                                <div class="panel-heading">  Pengambilan Pakan                                 
                                    <div class="pull-right">
                                        <label>Scan ID Pallet</label> <input type="text" class="scan_pallet" onchange="Pengambilan.pilih_kavling(this)" />
                                    </div>    
                                </div>
                                <div class="panel-body">
                                <div class="new-line">
                                    <table id="summaryTable" class="table table-bordered">
                                        <thead>
                                        <thead>
                                            <tr>
                                                <th>No. Pengambilan</th>                        
                                                <th>Flock</th>                                                
                                                <th>Tgl Kirim</th>
                                                <th>Tgl Kebutuhan</th>
                                                <th>Jumlah Permintaan</th>                                                
                                                <th>Jumlah Dropping</th>                                         
                                            </tr>                                        
                                        </thead>
                                        <tbody>
                                            <tr> 
                                                <td><?php echo $summary['no_order'] ?></td>
                                                <td><?php echo $summary['kode_flok'] ?></td>
                                                <td><?php echo $summary['tgl_kirim'] ?></td>
                                                <td><?php echo $summary['tgl_kebutuhan'] ?></td>
                                                <td><?php echo $summary['jml_permintaan'] ?></td>
                                                <td class="total_dropping"><?php echo $summary['jml_dropping'] ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="transaction-table" class="new-line">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="col-md-1">Kavling-Pallet</th>
                                                <th class="col-md-1">Kode Pakan</th>
                                                <th class="col-md-1">Nama Pakan</th>
                                                <th class="col-md-2">Diserahkan Oleh</th>
                                                <th class="col-md-3" colspan="5"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $number = 1; ?>
                                            <?php foreach ($items_result_transaksi as $key1 => $value1) { ?>
                                                <?php                                                
                                                $kode = explode('#', $key1);
                                                $kode_barang = $kode [0];
                                                $id_kavling = $kode [1];
                                                ?>
                                                <tr class='tr-header'
                                                    data-id_kavling="<?php echo $id_kavling ?>"
                                                    data-ke="<?php echo $number; ?>"
                                                    data-no-order="<?php echo $no_order; ?>"
                                                    data-kode-flok="<?php echo $value1['kode_flok']; ?>"
                                                    data-stok-kavling="<?php echo $value1['stok_kavling']; ?>"
                                                    ondblclick="Pengambilan.show_detail(this);">
                                                    <td class="no-kavling" data-no-kavling="<?php echo $value1['no_kavling']; ?>"><?php echo $id_kavling; ?></td>
                                                    <td class="kode-barang"><?php echo $kode_barang; ?></td>
                                                    <td class="nama-barang col-md-2"><?php echo $value1['nama_barang']; ?></td>
                                                    <td class="diserahkan-oleh" data-diserahkan-oleh="<?php echo empty($value1['id_diserahkan_oleh']) ? $kode_penerima : $value1['id_diserahkan_oleh']; ?>"><?php echo empty($value1['diserahkan_oleh']) ? $penerima : $value1['diserahkan_oleh']; ?></td>
                                                    <td colspan="5"></td>
                                                </tr>
                                                <tr class='tr-detail hide' data-ke="<?php echo $number; ?>">
                                                    <td colspan="9">
                                                        <div class="div-detail-pakan">
                                                        <table class="table table-bordered tbl-detail-pakan">
                                                            <thead>
                                                                <tr>                                                                    
                                                                    <th class="">Berat Pallet</th>
                                                                    <th class="">Berat Timbang (Kg)</th>
                                                                    <th class="">Berat Bersih (Kg)</th>
                                                                    <th class="">Jumlah (Sak)</th>                                                                   
                                                                    <th class="">Sisa Pallet</th>
                                                                    <th class="">Scan Barcode Pallet</th>
                                                </tr>
                                                </thead>
                                            <tbody>
                                                <?php $data_ke_pallet = 1; ?>
                                                <?php foreach ($value1['detail'] as $key2 => $value2) { ?>
                                                    <tr data-ke = "<?php echo $data_ke_pallet; ?>"
                                                        class="tr-sub-detail"
                                                        data-id_pallet="<?php echo $id_kavling ?>"
                                                        data-no-pallet="<?php echo $key2; ?>">   
                                                        <?php $berat_rata2 = array_sum($value2['berat_rata2']) / count($value2['berat_rata2']);  ?>                                                   
                                                        <td class="berat-pallet" data-berat_rata2="<?php echo $berat_rata2 ?>" data-berat_hand_pallet="<?php echo $value2['berat_hand_pallet'] ?>" data-berat_pallet_murni="<?php echo $value2['berat_pallet_murni']; ?>">
                                                          <span class="total_pallet"><?php echo  $value2['berat_pallet'] ; ?></span>
                                                          <?php if($jml_hand_pallet > 1 && !$value2['selesai']){ ?><span data-status="1" onclick="Pengambilan.ganti_hand_pallet(this)" class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span><?php } ?>
                                                        </td>
                                                        <td class="berat-timbang">
                                                            <input type="text" placeholder=""
                                                                   name="berat-timbang" class="form-control berat-timbang text-center"
                                                                   value="<?php echo ($value2['selesai'] == 1) ? $value2['berat_per_pallet'] : '' ; ?>"
                                                                   onblur="Pengambilan.kontrol_timbangan(this)" <?php echo ($value2['selesai'] == 1) ? "readonly" : ""; ?>
                                                                   <?php echo $metodeTimbangan ?> >

                                                        </td>
                                                        <td class="berat-bersih"><?php echo ($value2['selesai'] == 1) ? $value2['berat_per_pallet'] - $value2['berat_pallet'] : '' ; ?></td>
                                                        <td class="jumlah-sak" data-status-jumlah-aktual-sak="0" data-jumlah-aktual-sak="<?php echo ($value2['selesai'] == 1) ? $value2['jumlah_aktual'] : $value2['stok_kavling'] ; ?>">
                                                            <?php if($value2['selesai'] == 1){
                                                                echo $value2['jumlah_aktual'];
                                                            }else{
                                                                echo '<span class="hide">'.$value2['stok_kavling'].'</span>';
                                                            }

                                                            ?>
                                                        </td>                                                       
                                                        <td class="sisa-pallet">                                                          
                                                            <?php echo ($value2['selesai'] == 1) ? $value2['sisa'] : '' ; ?>                                                            
                                                        </td>
                                                        <td class="text-center scan_pallet">
                                                            <?php
                                                                $scan_barcode = '<input class="form-control" readonly="" onchange="Pengambilan.check_pallet(this)" type="text">';
                                                                if($value2['selesai'] == 1){
                                                                    $scan_barcode = '<input type="checkbox" checked disabled />';
                                                                }
                                                                echo $scan_barcode;

                                                            ?>
                                                        </td>

                                                    </tr>
                                                    <tr class="tr-sub-detail-pakan <?php echo ($value2['selesai'] == 1) ? '' : 'hide'; ?>" data-ke="<?php echo $data_ke_pallet; ?>">
                                                        <td colspan="7">
                                                        <center>
                                                            <table class="table table-bordered tbl-detail-kandang">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="checkbox-kandang"></th>
                                                                        <th class="nama-kandang">Kandang</th>
                                                                        <th class="jml-kebutuhan">Jml Kebutuhan (Sak)</th>                                                                        
                                                                        <th class="jml-aktual">Jml Aktual (Sak)</th>
                                                                        <th class="berat">Berat (Kg)</th>
                                                                        <th class="sisa">Sisa</th>
                                                                        <th class="konfirmasi">Konfirmasi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $data_ke_kandang = 1; ?>
                                                                    <?php foreach ($value2['detail'] as $key3 => $value3) { ?>
                                                                    <?php if(count($value3['detail']) == 1){ ?>
                                                                        <?php
                                                                            $value33 = $value3['detail'][0];
                                                                            $hide_kandang = '';
                                                                            if($value2['selesai'] == 1){
                                                                                if($value33['selesai'] == 0){
                                                                                    $hide_kandang = 'hide';
                                                                                }
                                                                            }
                                                                        ?>
                                                                        <?php ?>
                                                                        <?php if($value33['jml_kebutuhan'] > 0){ ?>
                                                                        <tr data-jml-pallet="<?php echo count($value3['detail']); ?>" class="tr-detail-kandang <?php echo $hide_kandang; ?>" data-ke="<?php echo $data_ke_kandang; ?>">
                                                                            <td class="checkbox-kandang">
                                                                                <label><input type="checkbox" class="checkbox-kandang" onclick="Pengambilan.checkbox_kandang(this)"
                                                                                    <?php echo ($value33['selesai'] == 1) ? 'checked disabled' : ''; ?>                                                                                    
                                                                                    ></label>
                                                                            </td>
                                                                            <td data-no-reg="<?php echo $value33['no_reg']; ?>" data-no-pallet="<?php echo $value33['no_pallet']; ?>" class="nama-kandang"><?php echo $value33['nama_kandang']; ?></td>                                                                           
                                                                            <td class="jml-kebutuhan"><?php echo $value33['jml_kebutuhan']; ?></td>
                                                                            <td class="jml-aktual"><?php echo ($value33['selesai'] == 1) ? $value33['jml_aktual_per_kandang'] : '' ; ?></td>
                                                                            <td class="berat"><?php echo ($value33['selesai'] == 1) ? $value33['berat_bersih_per_kandang'] : '' ; ?></td>
                                                                            <td class="sisa"><?php echo ($value33['selesai'] == 1) ? $value33['jml_kebutuhan']-$value33['jml_aktual_per_kandang'] : '' ; ?></td>
                                                                            <td class="konfirmasi" data-user-gudang="<?php echo ($value33['selesai'] == 1) ? $value33['id_diterima_oleh'] : '' ; ?>"><?php echo ($value33['selesai'] == 1) ? $value33['diterima_oleh'] : '' ; ?></td>
                                                                        </tr>
                                                                        <?php } ?>


                                                                    <?php } else{ ?>
                                                                        <?php
                                                                            $tmp_no_pallet = '';
                                                                            $tmp_jml_kebutuhan = '';
                                                                            $jml_kebutuhan = 0;
                                                                            $jml_aktual_per_kandang = 0;
                                                                            $berat_bersih_per_kandang = 0;
                                                                            foreach ($value3['detail'] as $key4 => $value4) {
                                                                                $tmp_no_pallet .= ($key4 > 0) ? ','.$value4['no_pallet'] : $value4['no_pallet'];
                                                                                $tmp_jml_kebutuhan .= ($key4 > 0) ? ','.$value4['jml_kebutuhan'] : $value4['jml_kebutuhan'];
                                                                                $jml_kebutuhan = $jml_kebutuhan+$value4['jml_kebutuhan'];
                                                                                $jml_aktual_per_kandang = $jml_aktual_per_kandang+$value4['jml_aktual_per_kandang'];
                                                                                $berat_bersih_per_kandang = $berat_bersih_per_kandang+$value4['berat_bersih_per_kandang'];
                                                                            }
                                                                        ?>
                                                                        <?php
                                                                            $value33 = $value4;
                                                                            $hide_kandang = '';
                                                                            if($value2['selesai'] == 1){
                                                                                if($value33['selesai'] == 0){
                                                                                    $hide_kandang = 'hide';
                                                                                }
                                                                            }
                                                                        ?>
                                                                        <?php ?>
                                                                        <?php if($jml_kebutuhan > 0){ ?>
                                                                        <tr data-jml-pallet="<?php echo count($value3['detail']); ?>" class="tr-detail-kandang <?php echo $hide_kandang; ?>" data-ke="<?php echo $data_ke_kandang; ?>">
                                                                            <td class="checkbox-kandang">
                                                                                <label><input type="checkbox" class="checkbox-kandang" onclick="Pengambilan.checkbox_kandang(this)"
                                                                                    <?php echo ($value33['selesai'] == 1) ? 'checked disabled' : ''; ?>                                                                             
                                                                                    ></label>
                                                                            </td>
                                                                            <td data-no-reg="<?php echo $value33['no_reg']; ?>" data-no-pallet="<?php echo $tmp_no_pallet; ?>" class="nama-kandang"><?php echo $value33['nama_kandang']; ?></td>                                
                                                                            <td class="jml-kebutuhan" data-jml-kebutuhan="<?php echo $tmp_jml_kebutuhan; ?>"><?php echo $jml_kebutuhan; ?></td>                                                                                                                                                        

                                                                            <td class="jml-aktual"><?php echo ($value33['selesai'] == 1) ? $jml_aktual_per_kandang : '' ; ?></td>
                                                                            <td class="berat"><?php echo ($value33['selesai'] == 1) ? number_format($berat_bersih_per_kandang,3) : '' ; ?></td>
                                                                            <td class="sisa"><?php echo ($value33['selesai'] == 1) ? $jml_kebutuhan-$jml_aktual_per_kandang : '' ; ?></td>
                                                                            <td class="konfirmasi" data-user-gudang="<?php echo ($value33['selesai'] == 1) ? $value33['id_diterima_oleh'] : '' ; ?>"><?php echo ($value33['selesai'] == 1) ? $value33['diterima_oleh'] : '' ; ?></td>
                                                                        </tr>
                                                                        <?php } ?>

                                                                    <?php } ?>
                                                                    <?php $data_ke_kandang++; ?>

                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </center>
                                                        </td>
                                                    </tr>
                                        <?php $data_ke_pallet++; ?>
                                        <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                        </td>
                                        </tr>
                                        <?php $number++; ?>
                                    <?php } ?>
                                    </tbody>
                                    </table>
                                </div>
                                              
    <link rel="stylesheet" type="text/css" href="assets/css/pengambilan_barang/pengambilan.css">
  
    
