
                    <?php $data_ke = 1; ?>
        <?php foreach ($list as $key => $value) { ?>
                        <?php 
                            $detail_barang = '';
                            $barang = [];
                            $no_ba = '';
                            $ba = [];
                            foreach ($value['detail_barang'] as $key1 => $value1) {
                                if(!in_array($value1['kode_barang'], $barang)){
                                    $detail_barang .= ($key1==0) ? $value1['kode_barang'].';'.$value1['jml_sj'] : '?'.$value1['kode_barang'].';'.$value1['jml_sj'];
                                }
                                $barang[] = $value1['kode_barang'];
                                if(!in_array($value1['no_berita_acara'], $ba)){
                                    $no_ba .= ($key1==0) ? $value1['no_berita_acara'] : ', '.$value1['no_berita_acara'];
                                }
                                $ba[] = $value1['no_berita_acara'];
                            } 
                        ?>
                        <tr ondblclick="baru(this,1)"
                            data-ke="<?php echo $data_ke; ?>"
                            data-no-do="<?php echo $value['no_do']; ?>"
                            data-no-kendaraan-kirim="<?php echo $value['no_kendaraan_kirim']; ?>"
                            data-no-spm="<?php echo $value['no_spm']; ?>"
                            data-detail-barang="<?php echo $detail_barang; ?>">
                            <td class='fno_op'><?php echo $value['no_op']; ?></td>
                            <?php if(!empty($no_ba) && $no_ba != '-'){?>
                            <td class='fno_berita_acara' data-no-ba="<?php echo $no_ba; ?>" onclick="print_view_berita_acara(this)"><b style="color:blue;"><?php echo $no_ba; ?></b></td>
                            <?php }else{?>
                            <td class='fno_berita_acara' data-no-ba="<?php echo $no_ba; ?>"><?php echo $no_ba; ?></td>
                            <?php }?>
                            <td class='fno_penerimaan'><?php echo $value['no_penerimaan']; ?></td>
                            <td class='fno_sj'><?php echo $value['no_sj']; ?></td>
                            <td class='fekspedisi'><?php echo $value['ekspedisi']; ?></td>
                            <td class='ftanggal_kirim'><?php echo convert_month($value['tanggal_kirim'], 1); ?></td>
                            
                        </tr>
                    <?php $data_ke++; ?>
        <?php } ?>