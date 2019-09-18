<div class="">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Tanggal Pemakaian</th>
					<th>Kategori</th>
					<th>Jumlah (Sak)</th>
					<th>Berita Acara</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if(!empty($berita_acara)){
						foreach($berita_acara as $ba){
							$jml_tampil = empty($ba->no_berita_acara) ? $ba->pakai : $ba->jml;
							$aksi =  empty($ba->no_berita_acara) ? '<span data-tglkebutuhan="'.$ba->tgl_kebutuhan.'" data-jml="'.$ba->pakai.'" data-ppsk="'.$ba->no_ppsk.'" onclick="pemusnahanBangkai.generateBA(this)" class="btn btn-default">Generate BA</span>' : '<span data-ba="'.$ba->no_berita_acara.'" onclick="pemusnahanBangkai.cetakBA(this)" class="btn btn-default"><i class="glyphicon glyphicon-paperclip"></i> '.$ba->no_berita_acara.'</span>';
							echo '<tr data-ppsk="'.$ba->no_ppsk.'" ondblclick="pemusnahanBangkai.detailKandang(this)">
								<td>'.convertElemenTglIndonesia($ba->tgl_kebutuhan).'</td>
								<td>Bangkai</td>
								<td class="text-right">'.angkaRibuan($jml_tampil).'</td>
								<td>'.$aksi.'</td>
							</tr>';
						}
					}
				?>
			</tbody>
			<tfoot>
			</tfoot>
		</table>
</div>
