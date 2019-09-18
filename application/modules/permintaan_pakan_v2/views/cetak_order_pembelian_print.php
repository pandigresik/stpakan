<html>
	<head>
	</head>
	<body>
		<div id="penerima"><?php echo  $data_do[0]['penerima'] ?><br />
		<?php echo $data_do[0]['alamat_penerima'] ?></div>	
		<div id="tanggal"><?php echo tglIndonesia(date('Y-m-d'),'-',' ')?></div>
		<div id="pengirim">
			WONOKOYO JAYA CORP, PT<br>
			PT. WJC (FEED) JL. TAMAN <br>
			SURABAYA
		</div>
		<div id="no_op_Logistik"><?php echo $data_do[0]['no_op_logistik']?></div>
		<div id="content">
			<?php 
				$no = 1; 
				$total_jumlah = 0;
				foreach($data_do as $d){
				echo '<div class="baris">
					<div class="nomer">'.$no++.'</div>
					<div class="jenis_barang">'.$d['nama_barang'].'</div>
					<div class="jumlah">'.$d['jml_kirim'].'</div>
					<div class="keterangan">SAK @50.00 KG</div>
					<div class="harga">'.$d['harga_satuan'].'</div>
					<div class="jumlah">'.$d['harga_total'].'</div>
				</div> 
				';
				$total_jumlah += $d['harga_total'];
			}?>
			
		</div>
		<div id="no_pp"><?php echo $data_do[0]['no_pp'] ?></div>
		<div id="total_jumlah"><?php echo $total_jumlah ?></div>
		<div id="total_semua"><?php echo $total_jumlah ?></div>
	</body>
</html>

<style>
@page {
    /* dimensions for the whole page */
    size: A5;
    
    margin: 0;
}
 
html {
    /* off-white, so body edge is visible in browser */
    background: #eee;
}
 
body {
    /* A5 dimensions */
    width: 210mm;
    height: 148.5mm;
    margin: 5;
    font-size : .9em;
}
#tanggal{
	position : absolute;
	left : 650px;
	top : 33px;
}
#pengirim{
	position : absolute;
	left : 440px;
	top : 58px;
}
#no_op_Logistik{
	position : absolute;
	left :100px;
	top : 115px;
}
#content{
	position : absolute;
	left : 23px;
	top : 200px;
}
div.baris{
	clear : both
}
div.baris > div{
	float : left;
	margin : 1px 1px 2px 3px;
}
div.nomer{
	width : 20px
}
div.jenis_barang{
	width : 220px;
	padding-left: 5px;
}
div.keterangan{
	width : 150px;
	padding-left : 10px;
}
div.jumlah{
	width : 110px;
	text-align : right
}

div.harga{
	width : 110px;
	text-align : right
}
#no_pp{
	position : absolute;
	left : 95px;
	top : 373px;
}
#total_jumlah{
	position : absolute;
	left : 750px;
	top : 373px;
	text-align : right
}
#total_semua{
	position : absolute;
	left : 750px;
	top : 443px;
	text-align : right
}
</style>