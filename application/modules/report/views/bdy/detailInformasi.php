<table class="table table-bordered custom_table" >
	<thead>
			<tr>
				<th>Siklus</th>
				<th>Flock</th>
				<th>Farm</th>
        <th>Kandang</th>
				<th class="tanggal">Tgl DOC In</th>
        <th>Populasi</th>
				<th class="tanggal">Tgl Panen</th>
        <th>Jumlah Panen</th>

			</tr>
    </thead>
    <tbody class="text-center">
    <?php
      $cur_flok = '';
      $cur_siklus = '';

      foreach($kandang as $k){
        $flok = $k['flok_bdy'];
        $siklus = $k['periode_siklus'];
        $flok_str = '';
        $siklus_str = '';
        if($cur_flok != $flok){
          $flok_str = $flok;
          $cur_flok = $flok;
        }
        if($cur_siklus != $siklus){
          $siklus_str = $siklus;
          $cur_siklus = $siklus;
        }
        echo '<tr>';
        echo '<td><a href="#report/report/'.$tipe.'?farm='.$pilih_farm.'&status_siklus=C&siklus='.$siklus_str.'" target="_blank">'.$siklus_str.'</a></td>';
				echo '<td>'.$flok_str.'</td>';
				echo '<td>'.$k['nama_farm'].'</td>';
        echo '<td>'.$k['kode_kandang'].'</td>';
        echo '<td class="tanggal">'.tglIndonesia($k['tgl_doc_in'],'-',' ').'</td>';
        echo '<td class="number">'.formatAngka($k['jml_populasi'],0).'</td>';
        echo '<td class="tanggal">'.tglIndonesia($k['tgl_panen'],'-',' ').'</td>';
        echo '<td class="tanggal">'.formatAngka($k['jml_panen'],0).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
  </table>
