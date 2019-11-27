<?php
echo '
<div class="col-md-12">
<!--pre>
';
#print_r($konfirmasi_ppic);
echo '
</pre-->
<table class="table table-bordered konfirmasi_table">
    <thead>
        <tr>
            <th class="text-center col-md-2">No. Konfirmasi</th>
            <th colspan="2" class="text-center col-md-4">Farm</th>
            <th class="text-center col-md-2">Populasi</th>
            <th class="text-center col-md-2">User</th>
            <th class="text-center col-md-2">Tanggal</th>
        </tr>
    </thead>
    <tbody> ';
    $data_ke = 1;
    $data_ke_flok = 1;
    foreach($konfirmasi_ppic as $key0 => $value0){
        if($data_ke > 1){
            echo '<tr><td colspan="6"></td></tr>';
        }
        echo '<tr class="tr_header'.$data_ke.'">';
        //echo '<td class="td_no_konfirmasi">'.$value1['no_konfirmasi'].'</td>';
        echo '<td rowspan="2"></td>';
        #echo (!empty($value1['no_konfirmasi'])) ? '<td class="td_no_konfirmasi"><span ondblclick="detail_konfirmasi_forecast(this)">'.$value1['no_konfirmasi'].'</span></td>' : '<td class="td_no_konfirmasi text-center">-</td>';
        echo '<td colspan="2" data-kode-farm="'.$value0['kode_farm'].'" class="td_farm"><span class="glyphicon glyphicon-minus" onclick="hide_detail(this,'.$data_ke.')"></span> '.$value0['nama_farm'].'</td>';
        echo '<td class="text-right">'.$value0['populasi_campuran'].'</td>';
        echo '<td colspan="2" rowspan="2"></td>';
        #echo (!empty($value1['user'])) ? '<td class="td_user">'.$value1['user'].'</td>' : '<td class="td_user text-center"><span class="btn btn-default" onclick="ack('.$data_ke.')">Ack</span></td>';
        #echo (!empty($value1['tanggal'])) ? '<td class="td_tanggal text-center">'.tglIndonesia(date('Y-m-d',strtotime($value1['tanggal'])),'-',' ').' '.date('H:i',strtotime($value1['tanggal'])).'</td>' : '<td class="td_tanggal text-center">-</td>';
        echo '</tr>';
        echo '<tr class=" '.$data_ke.'">';
        $rowspan = $value0['rowspan_farm']+1;
        #echo '<td rowspan='.$rowspan.'></td>';
        #echo '<td></td>';
        echo '<th class="text-center">Kandang</td>';
        echo '<th class="text-center">Tanggal Chick-in</td>';
        echo '<th class="text-center">Campur</td>';
        #echo '<td colspan="2"></td>';
        echo '</tr>';
    foreach($value0['detail'] as $key1 => $value1){
        foreach($value1 as $key2 => $value2){
            $rowspan_flok = count($value2['detail']);
            foreach($value2['detail'] as $key3 => $value3){
                $hide = "";
                if(!empty($value2['user'])){
                    #$hide = "hide";
                }
                echo '<tr class="'.$hide.' '.$data_ke.'">';
                if($key3 == 0){
                    echo (!empty($value2['no_konfirmasi'])) ? '<td class="td_no_konfirmasi" rowspan="'.$rowspan_flok.'"><span data-flok="'.$value0['kode_farm'].$data_ke_flok.'" data-namafarm="'.$value0['nama_farm'].'" ondblclick="detail_konfirmasi_forecast_bdy(this)" style="color:#428bca;font-weight:bold;">'.$value2['no_konfirmasi'].'</span></td>' : '<td class="td_no_konfirmasi" rowspan="'.$rowspan_flok.'">-</td>';
                    #echo '<td class="td_no_konfirmasi" rowspan="'.$rowspan_flok.'"></td>';
                }
                echo '<td class="td_no_reg '.$data_ke_flok.'"  data-flok="'.$value0['kode_farm'].$data_ke_flok.'" data-nama-farm="'.$value0['nama_farm'].'" data-kode-farm="'.$value0['kode_farm'].'" data-no-reg="'.$value3['no_reg'].'"><span
                data-doc-in="'.$value3['tanggal_chickin'].'"
                data-maks-populasi="'.$value3['maks_populasi'].'"
                data-kode-strain="'.$value3['kode_strain'].'"
                data-populasi="'.$value3['jml_campuran'].'"
                data-tipe-kandang="'.$value3['tipe_kandang'].'">'.$value3['kode_kandang'].'</span></td>';
                echo '<td class="td_tgl_chickin">'.tglIndonesia(date('Y-m-d',strtotime($value3['tanggal_chickin'])),'-',' ').'</td>';
                echo '<td class="text-right td_populasi_campuran">'.$value3['jml_campuran'].'</td>';

                if($key3 == 0){
                    #echo '<td class="td_user" rowspan="'.$rowspan_flok.'"></td>';
                    #echo '<td class="td_tanggal" rowspan="'.$rowspan_flok.'"></td>';
                    if($ack){
                    echo (!empty($value2['user'])) ? '<td class="td_user" rowspan="'.$rowspan_flok.'">'.$value2['user'].'</td>' : '<td class="td_user" rowspan="'.$rowspan_flok.'"><span class="btn btn-default" onclick="ack(this,'.$data_ke.','.$data_ke_flok.')">Ack</span></td>';
                    echo (!empty($value2['tanggal'])) ? '<td class="td_tanggal" rowspan="'.$rowspan_flok.'">'.tglIndonesia(date('Y-m-d',strtotime($value2['tanggal'])),'-',' ').' '.date('H:i',strtotime($value2['tanggal'])).'</td>' : '<td class="td_tanggal" rowspan="'.$rowspan_flok.'">-</td>';
                    }
                    else{
                    echo (!empty($value2['user'])) ? '<td class="td_user" rowspan="'.$rowspan_flok.'">'.$value2['user'].'</td>' : '<td class="td_user" rowspan="'.$rowspan_flok.'">-</td>';
                    echo (!empty($value2['tanggal'])) ? '<td class="td_tanggal" rowspan="'.$rowspan_flok.'">'.tglIndonesia(date('Y-m-d',strtotime($value2['tanggal'])),'-',' ').' '.date('H:i',strtotime($value2['tanggal'])).'</td>' : '<td class="td_tanggal" rowspan="'.$rowspan_flok.'">-</td>';

                    }
                }
                echo '</tr>';
            }
            $data_ke_flok++;
        }
    }
        $data_ke++;
    }
echo '</tbody>
</table>
</div>
';
echo '<div id="data-notif" class="hide">';
    if(isset($notif)){
        echo json_encode($notif);
    }
echo '</div>';
