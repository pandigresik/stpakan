<table data-no_reg=<?php echo $no_reg ?> id="tabel_detail_pengembalian_pakan_rusak" class="table table-bordered">
    <thead>
        <tr>
            <th class="text-center">Nama Pakan</th>
            <th class="text-center">Jumlah Retur</th>
            <th class="text-center">Jumlah Sudah Diganti</th>
            <th class="text-center">Jumlah Stok Akhir</th>
        </tr>
    </thead>
    <tbody>
    
        <?php 
        /* buat dropdown list pakan */ 
        $o = array();
        foreach($list_pakan as $key => $pakan){
            $kode_barang = $pakan['kode_barang'];
            $bentuk_barang = $pakan['bentuk_barang'];
            $nama_barang = $pakan['nama_barang'];
            $jk = $pakan['jenis_kelamin'];
            $stok = $pakan['jml_stok'];
            $x = '<option data-stok="'.$stok.'" value="'.$kode_barang.'/'.$jk.'">'.$nama_barang. '-' .convertKode('bentuk_barang',$bentuk_barang).' - ('.$jk.')</option>';
            array_push($o,$x);  
        }   
            $s = '<select name="kode_barang" onchange="Pengembalianpakan.show_detail_timbang(this)"><option value="">Pilih Nama Pakan</option>'.implode(' ', $o).'</select>';
            
            echo '<tr class="header_pakan" data-ke="1">
                    <td>'.$s.'</td>
                    <td class="jml_retur"></td>
                    <td class="jml_sudah_diganti"></td>
                    <td class="jml_stok"></td>
            </tr>
            <tr class="detail_timbang" style="display:none" data-ke="1">';
                        
            echo '<td colspan="8">
                    <table class="table pull-right" style="max-width:50%">
                        <thead>
                            <tr>
                                <th>Timbangan (Kg) </th>
                                <th>Keterangan </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="sub_detail_timbang">
                                <td>
                                    <input type="text" class="required number" data-field="Jumlah pengembalian" value=0 name="jml_pengembalian" />
                                </td>
                                <td>
                                    <input type="text" class="required" data-field="Keterangan" name="keterangan" />
                                </td>
                                <td>
                                    <span class="btn btn-default" onclick="Pengembalianpakan.timbang_lagi(this)">Selesai</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>    
                </td>';
            
            echo '</tr>';
        
         ?>
    </tbody>
</table>