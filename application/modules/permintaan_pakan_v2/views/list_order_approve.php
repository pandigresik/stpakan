
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
                $index = 0;
                $keterangan = isset($riwayat[$tgl_kirim]) ? $riwayat[$tgl_kirim] : array();
                foreach($perfarm as $kf => $_baris){
                    $rowspan = $_baris['rowspan'];
                    $total = $_baris['total'];

                    $keterangan_tmp = array();
                    $keterangan_op = isset($keterangan[$kf]) ? $keterangan[$kf] : array();
                    if(!empty($keterangan_op)){
								foreach($keterangan_op as $k){
									array_push($keterangan_tmp,'['.$k['nama_pegawai'].'], Di'.$convert_status[$k['status']]. (!empty($k['keterangan']) ? '( <em>'.$k['keterangan'].' </em>)' : '') .' '.convertElemenTglWaktuIndonesia($k['tgl_buat']));
								}
							}
                    echo '<tr data-tgl_kirim="'.$tgl_kirim.'" data-kode_farm="'.$kf.'">';
                    echo '<td rowspan="'.$rowspan.'"><input type="checkbox" /></td>';                
                    echo '<td rowspan="'.$rowspan.'">'.tglIndonesia($tgl_kirim, '-', ' ').'</td>';                
                    echo '<td rowspan="'.$rowspan.'">'.$nama_farm[$kf].'</td>';                                                            
                    foreach($_baris['detail'] as $barisRit){    
                        $rowspanRit = count($barisRit);
                        $totalKirim = array_sum(array_column($barisRit,'jml_kirim'));
                        $indexRit = 0;
                        foreach($barisRit as $baris){                                                            
                            echo '<td>'.$baris['rit'].'</td>';
                            echo '<td>'.$baris['no_op'].'</td>';
                            echo '<td>'.$baris['no_pp'].'</td>';
                            if(!$index){
                                echo '<td rowspan="'.$rowspan.'">'.angkaRibuan($total).'</td>';
                            }
                            echo '<td>'.$baris['ekspedisi'].'</td>';
                            if(!$indexRit){
                                
                                echo '<td rowspan="'.$rowspanRit.'">'.angkaRibuan($totalKirim).'</td>';
                            }                    
                            
                            if(!$index){
                                echo '<td rowspan="'.$rowspan.'"><div>'.implode('</div><div>',$keterangan_tmp).'</div></td>';         
                            }
                            
                            
                            echo '</tr>';
                            $indexRit++;
                            $index++;
                        }
                    }    
                }
                echo '</tr>';                    		
                
            }
        }    
        ?>


