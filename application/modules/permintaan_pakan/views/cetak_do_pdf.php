<?php
$image_file = base_url()."assets/images/feedmill_logo.png";
echo '
	<table width="97%" style="font-family:Arial">
		<tr>
			<td style="width:8%"><img src="'.$image_file.'" alt="test alt attribute" border="0" /></td>
			<td style="width:92%">
				<span style="font-weight:bold;font-size:8px;">PT. WONOKOYO JAYA CORPORINDO</span><br/>
				<span style="font-weight:bold;font-size:6px;">DIVISI FEEDMILL</span><br/>
				<span style="font-weight:bold;font-size:6px;">UNIT GEMPOL</span>
			</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr>
			<td colspan="2" align="center" style="font-size:1.5em;">Delivery Order</td>
		</tr>
	</table>

	<table>
		<tbody>
			<tr><td style="width:15%">No. DO</td><td style="width:65%"> : '.  $data_do[0]['no_do'].'</td><td rowspan="2" style="height:50px">'.$barcode.'</td></tr>
			<tr><td>No. OP</td><td> : '.  $data_do[0]['no_op'].'</td></tr>
		</tbody>
	</table>
	
	<div style="border:1px solid black;position:auto">

		<table class="table1" width="100%">
			<tbody>
				<tr>
					<td style="width:25%">Ekspedisi</td>
					<td style="width:80%">: '. $data_do[0]['nama_ekspedisi'] . '</td>
				</tr>
				<tr>
					<td>Alamat</td>
					<td>: ' . $data_do[0]['alamat_ekspedisi'] . '</td>
				</tr>
				<tr>
					<td>Tanggal Pengiriman</td>
					<td>: ' . tglIndonesia($data_do[0]['tgl_kirim'],'-',' ') . '</td>
				</tr>
				<tr>
					<td>Nopol</td>
					<td>: </td>
				</tr>
				<tr>
					<td>Sopir</td>
					<td>: </td>
				</tr>
				<tr>
					<td>Farm Penerima</td>
					<td>: '. $data_do[0]['nama_farm'] . '</td>
				</tr>
				<tr>
					<td>Alamat</td>
					<td>: '. $data_do[0]['alamat_farm'] . '</td>
				</tr>
			</tbody>
		</table>

	</div>
	<div class="caption">Detail Pakan</div>
<div style="">
	<table class="detail_pakan" cellpadding="4">
		<thead>
			<tr>
				<th>&nbsp;<br />Nama Barang<br /></th>
				<th class="number">Kuantitas <br />(Sak)<br /></th>
				<th class="number">Berat <br />(Kg)<br /></th>
			</tr>
		</thead>
		<tbody>';
		 $tot_sak = $tot_berat = 0;
			foreach($data_do as $d){
				$tot_sak += $d['jml_kirim'];
				$tot_berat += $d['berat'];;
	echo	'<tr>'.
				'<td>'.$d['nama_barang'].'</td>'.
				'<td class="number">'.angkaRibuan($d['jml_kirim']).'</td>'.
				'<td class="number">'.angkaRibuan($d['berat']).'</td>'.
			'</tr>';
			}
	echo '</tbody>
				<tfoot>
					<tr>
						<td style="border:none" class="number">Total</td>
						<td style="border:none" class="number">'.angkaRibuan($tot_sak).'</td>
						<td style="border:none" class="number">'.angkaRibuan($tot_berat).'</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<div style="">
			<table class="table3">
				<tbody>
					<tr>
						<td>Divalidasi Oleh, <br /> Ekspedisi</td>
						<td>Diberikan Oleh, <br /> Sopir</td>
						<td>Diterima Oleh, <br /> Admin Plant</td>
					</tr>
					<tr><td colspan="3" style="height:60px"></td></tr>
					<tr>
						<td>( _______________ )</td>
						<td>( _______________ )</td>
						<td>( _______________ )</td>
					</tr>

				</tbody>
			</table>
		</div>
';
?>
<style>
table{
	font-size : .72em;
}
.caption{
	font-size : .8em;
}
.table1{
	width : 70%;
	margin : 6px;
}

.table3 td{
	text-align : center
}
table.detail_pakan{

}
table.detail_pakan td{
	border : .5px solid gray;
	border-collapse : collapse;
}
table.detail_pakan th{
	valign : middle;
	border : .5px solid gray;
	border-collapse : collapse;
	font-size : 1.4em;
}

.number{
	text-align : right
}

</style>
