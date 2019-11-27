
        <?php
        if(!empty($list_order)){      
            $convert_status = array(
                'A' => 'approve',
                'D' => 'buat',
                'N' => 'rilis',
                'T' => 'reject',
                'R' => 'review'
            );      
            
        	foreach($list_order as $tgl_kirim => $perfarm){
                $keterangan = isset($riwayat[$tgl_kirim]) ? $riwayat[$tgl_kirim] : array();
                foreach($perfarm as $kf => $_baris){
                    $keterangan_tmp = array();
                    $keterangan_op = isset($keterangan[$kf]) ? $keterangan[$kf] : array();
                    if(!empty($keterangan_op)){
								foreach($keterangan_op as $k){
									array_push($keterangan_tmp,'['.$k['nama_pegawai'].'], Di'.$convert_status[$k['status']]. (!empty($k['keterangan']) ? '( <em>'.$k['keterangan'].' </em>)' : '') .' '.convertElemenTglWaktuIndonesia($k['tgl_buat']));
								}
							}
                    $index = 0;
                    $rowspan = count($_baris['detail']);
                    $keterangan = '';
                    $status_ploting = empty($keterangan_op) ? $_baris['detail'][0]['status_plotting'] : 1;
                    echo '<tr data-noreg="'.$_baris['detail'][0]['no_reg'].'" data-status_plotting="'.$status_ploting.'" data-tgl_kirim="'.$tgl_kirim.'" data-kode_farm="'.$kf.'" data ondblclick="Plotting.detail_pp(this)">';
                    echo '<td rowspan="'.$rowspan.'">'.tglIndonesia($tgl_kirim, '-', ' ').'</td>';                
                    echo '<td rowspan="'.$rowspan.'">'.$_baris['detail'][0]['farm'].'</td>';                                                            
                    $total = array_sum($_baris['opUnique']);
                    foreach($_baris['detail'] as $baris){
                        if($index){
                            $status_ploting = empty($keterangan_op) ? $baris['status_plotting'] : 1;
                            echo '<tr data-noreg="'.$baris['no_reg'].'" data-status_plotting="'.$status_ploting.'"  data-tgl_kirim="'.$tgl_kirim.'" data-kode_farm="'.$kf.'" ondblclick="Plotting.detail_pp(this)">';
                        }                                                            
                        echo '<td>'.$baris['no_op'].'</td>';
                        echo '<td>'.$baris['no_pp'].'</td>';
                        if(!$index){
                            echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($total).'</td>';
                        }                    
                        echo '<td>'.$baris['ekspedisi'].'</td>';
                        echo '<td><div>'.implode('</div><div>',explode(',',$baris['no_do'])).'</div></td>';        		
                        if(!$index){
                            echo '<td rowspan="'.$rowspan.'"><div>'.implode('</div><div>',$keterangan_tmp).'</div></td>';      
                        }
                        
                        if($index){
                            echo '</tr>';
                        }
                        $index++;
                    }
                    
                }
                echo '</tr>';                    		
                
            }
        }    
        ?>


