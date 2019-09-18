<?php 
$image_file = base_url()."assets/images/feedmill_logo.png";
echo '
	<table width="97%" style="font-family:Arial">
		<tr>
			<td style="width:8%"><img src="'.$image_file.'" alt="test alt attribute" border="0" /></td>
			<td style="width:62%" colspan="2">
				<span style="font-weight:bold;font-size:8px;">PT. WONOKOYO JAYA CORPORINDO</span><br/>
				<span style="font-weight:bold;font-size:6px;">Kantor Pusat : Jl. Taman Bungkul No. 1 - 7 Surabaya</span><br/>
				<span style="font-weight:bold;font-size:6px;">Pabrik : Desa Winong Kec. Gempol, Kab. Pasuruan</span>
			</td>
			<td rowspan="3" style="text-align:left">
				<span>DIJUAL KEPADA :</span><br />
				<span>NIP     :</span><br />
				<span>NAMA    : '.$data_do[0]['nama_farm'].'</span><br />
				<span>OP  '.date('Y').'    : </span><br />
				<span>OP : No : '.$data_do[0]['no_op_logistik'].'</span>			
			</td>
		</tr>
		<tr>
			<td colspan="3" align="right" style="text-decoration:underline"><h2>ORDER PENJUALAN PAKAN</h2></td>
		</tr>
		<tr>
			<td colspan="3">Tanggal : '.tglIndonesia(date('Y-m-d'),'-',' ').'</td>
		</tr>			
	</table>	
												
	
<div style="">	
																			
	<table class="detail_pakan">
		<thead>
			<tr>
				<th style="width:30px">&nbsp;<br />NO.<br /></th>
				<th style="width:160px">&nbsp;<br />JENIS BARANG<br /></th>
				<th>&nbsp;<br />UNIT<br /></th>
				<th>HARGA SATUAN<br />(Rp)<br /></th>
				<th>&nbsp;<br />KETERANGAN<br /></th>
			</tr>
		</thead>
		<tbody>';
		 	$no = 1;
			foreach($data_do as $d){
	echo	'<tr>'.
				'<td style="width:30px">'.$no++.'</td>'.
				'<td style="width:160px">'.$d['nama_barang'].' - '.$d['kode_barang'].'</td>'.
				'<td class="number">'.angkaRibuan($d['jml_kirim']).'</td>'.
				'<td class="number">'.angkaRibuan($d['harga_satuan']).'</td>'.
				'<td>'.$d['no_op_logistik'].'</td>'.
			'</tr>';	
			}
	echo '</tbody>
			</table>
		<span style="text-decoration:underline;font-size:80%">CATATAN : </span><br />
				
		<span style="font-size:70%">1. Order Penjualan berlaku mulai tanggal '.$data_do[0]['tgl_op'].' s/d tanggal '.$data_do[0]['tgl_kadaluarsa_op'].'</span><br />
		<span style="font-size:70%">2. </span>____________________________________________<br />
		<span style="font-size:70%">3. </span>____________________________________________<br />
					
		</div>
								
		<div style="">						
			<table class="table3">
				<tbody>
					<tr>
						<td>Pembeli</td>
						<td style="width:195px"></td>
						<td>PT. Wonokoyo Jaya Corp.</td>
					</tr>
					<tr><td colspan="3" style="height:45px"></td></tr>
					<tr>
						<td>( _______________ )</td>
						<td></td>
						<td>( _______________ )</td>
					</tr>
					<tr>
						<td>Nama & Cap perusahaan</td>
						<td></td>
						<td>Pejabat .....................</td>
					</tr>
					<tr>
						<td>Asli - Bag. SJ (FM/Farm)</td>
						<td>Copy 1 - Pembeli</td>
						<td>Copy 2 - Arsip Kantor Perwakilan</td>
					</tr>
				</tbody>
			</table>
		</div>		
		<span style="font-size:70%">	Order Penjualan ini dilayani dengan SJ. no. ______________________ tanggal ________________</span>					
';
?>
<style>
table{
	font-size : .72em;
}
.table1{
	width : 70%;
	margin : 6px;
}

.table3 td{
	text-align : center
}
table.detail_pakan{
	width : 100%
}
table.detail_pakan td{
	border : .5px solid gray;
	border-collapse : collapse;
}
table.detail_pakan th{
	text-align : center;
	valign : middle;
	border : .5px solid gray;
	border-collapse : collapse;
	font-size : 1.4em;
}

.number{
	text-align : right
}

</style>