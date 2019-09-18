<?php if(!$rekap){ ?>
<div class="row col-md-12">
    <div class="col-md-1">
        <span class="btn btn-default" onclick="Plotting.simpan_do(this)">Simpan</span>
    </div>
    <div class="col-md-2 pull-right">
        Sisa PP : <span id="sisaPlottingPP"></span> Sak
    </div>
</div>
<?php } ?>

<div class="section" id="div_detail_pp">
    <div class="container col-md-12">       
        <div class="content new-line">
        <?php 
        //    foreach($detail_pp as $tgl => $pp){
                //$jml_pakan = count($pp['nama_pakan']);
                $jml_ekspedisi = count($list_ekspedisi);
                echo '<table class="table table-bordered custom_table">';
                    echo '<thead>';
                        echo '<tr>';
                            echo '<th rowspan=2>No. OP</th><th rowspan=2>Jenis Pakan</th><th rowspan=2>Jumlah PP</th><th>EKSPEDISI</th>';                            
                            foreach($list_ekspedisi as $kode_ekspedisi){
                                $jml_rit = ceil($total_pakan / intval($kode_ekspedisi['min']));
                                echo '<th colspan="'.$jml_rit.'">'.$kode_ekspedisi['nama'].'</th>';
                            }
                        echo '</tr>';
                        echo '<tr>';
                        $index_loop = 0;
                        $rit_loop = 1;
                        foreach($list_ekspedisi as $kode_ekspedisi){
                            $jml_rit = ceil($total_pakan / intval($kode_ekspedisi['min']));
                            if(!$index_loop){
                                echo '<th>Rit @'.$kode_ekspedisi['min'].'</th>';
                                $index_loop++;
                            }
                            
                            while($rit_loop <= $jml_rit){
                                echo '<th data-minritase="'.$kode_ekspedisi['min'].'" data-maxritase="'.$kode_ekspedisi['max'].'" data-kode_ekspedisi="'.$kode_ekspedisi['kode'].'" data-rit="'.$rit_loop.'">'.$rit_loop.'</th>';
                                $rit_loop++;
                            }
                        }
                        echo '</tr>';                        
                    echo '</thead>';    
                    echo '<tbody>';                                        
                    $tmp_total_pakan = $total_pakan;    
                    $rit_ke = 1;
                    $sisa_ritase = 0;
                    $op_ekspedisi = array();
//                    if(!$sudahPlot){
                        foreach($list_op as $op => $perop){
                            $index = 0;
                            $rowspan = count($perop);
                            echo '<tr>';
                            $div_do = array();
                            if(isset($do_perop[$op])){
                                $div_do = $do_perop[$op];
                            }
                                                    
                            echo '<td rowspan="'.$rowspan.'">'.$op.'<br />'.implode(' ',array_unique($div_do)).'</td>';
                            $_indexPakan = 1;
                            $sudahPlot = 0;
                            $kendaraan_ekspedisi_op = isset($kendaraan_ekspedisi[$op]) ? $kendaraan_ekspedisi[$op] : array();
                            if(!empty($kendaraan_ekspedisi_op)){
                                $sudahPlot = 1;
                            }
                            foreach($perop as $_op){
                                if($index){
                                    echo '<tr>';
                                }
                                echo '<td class="nama_pakan" data-kode_pakan="'.$_op['kode_pakan'].'">'.$_op['nama_pakan'].'</td>';
                                echo '<td class="jml_pp">'.$_op['jumlah'].'</td>';                                                
                                echo '<td></td>';        
                                $_rit = 1;
                                $_rit_awal = 1;
                                foreach($list_ekspedisi as $kode_ekspedisi){
                                    /** ambil dari jumlah min kapasitas ekspedisi */
                                    $maxMuat = $kode_ekspedisi['min'];
                                    $jml_rit = ceil($total_pakan / $maxMuat);                          
                                    $_muat =  $_op['jumlah']; 
                                    $kendaraan_kode_ekspedisi_op = isset($kendaraan_ekspedisi_op[$kode_ekspedisi['kode']]) ? $kendaraan_ekspedisi_op[$kode_ekspedisi['kode']] : array();
                                    while($_rit <= $jml_rit){
                                        $status_do = '';
                                        $kendaraan_kode_ekspedisi_op_pakan = isset($kendaraan_kode_ekspedisi_op[$_op['kode_pakan']]) ? $kendaraan_kode_ekspedisi_op[$_op['kode_pakan']] : array();
                                        if($sisa_ritase <= 0){
                                            $sisa_ritase = $maxMuat;
                                        }
                                        if(isset($kendaraan_kode_ekspedisi_op_pakan[$_rit])){         
                                            $_tmpmuat = $kendaraan_kode_ekspedisi_op_pakan[$_rit]['jml_kirim'];
                                            $status_do = $kendaraan_kode_ekspedisi_op_pakan[$_rit]['status_do'];
                                        }else{
                                            if($_muat < $sisa_ritase){
                                                $_tmpmuat = $_muat;
                                            }else{
                                                $_tmpmuat =  $sisa_ritase;
                                            }
                                        }
                                        
                                        if($rit_ke == $_rit or (isset($kendaraan_kode_ekspedisi_op_pakan[$_rit])) ){         
                                            $_muat -= $_tmpmuat;                                
                                            $boxDo = '';
                                            $panah = '';
                                            if(in_array($status_do,array('','T'))){
                                                if(!empty($_tmpmuat)){
                                                    $boxDo = '<span class="box-kendaraan readonly" data-no_urut=""><input type="text" data-status_do="'.$status_do.'" value="'.$_tmpmuat.'" readonly></span>';		
                                                    $panah = '<i class="glyphicon glyphicon-resize-horizontal" onclick="Plotting.popup_pindah_ritase(this)"></i>';
                                                }
                                            }else{
                                                $boxDo = '<span class="box-kendaraan readonly" data-no_urut=""><input type="text" value="'.$_tmpmuat.'" disabled></span>';		
                                            }
                                            
                                            echo '<td data-kodefarm="'.$kode_farm.'" data-nomerop="'.$op.'" data-awalrit="'.$_rit_awal.'" data-maxritase="'.$maxMuat.'" data-maxrit="'.$jml_rit.'" data-rit="'.$_rit.'" style="text-align:left" data-kode_ekspedisi="'.$kode_ekspedisi['kode'].'">'.$boxDo. ' &nbsp; ' .$panah.'</td>';
                                            $sisa_ritase -=  $_tmpmuat;
                                        }else{
                                            echo '<td data-kodefarm="'.$kode_farm.'" data-nomerop="'.$op.'" data-awalrit="'.$_rit_awal.'" data-maxritase="'.$maxMuat.'" data-maxrit="'.$jml_rit.'" data-rit="'.$_rit.'" style="text-align:left" data-kode_ekspedisi="'.$kode_ekspedisi['kode'].'"></td>';
                                        }
    
                                        if($sisa_ritase <= 0){
                                            $rit_ke++;
                                        }
                                        $_rit++;
                                    }
                                    
                                }
                                if($index){
                                    echo '</tr>';
                                }
                                $index++;
                                $_indexPakan++;
                            }
                            echo '</tr>';
                        }

                    
                    echo '</tbody>';
                echo '</table>';
        //    } 
            ?>
            
        </div>
    </div>
</div>  