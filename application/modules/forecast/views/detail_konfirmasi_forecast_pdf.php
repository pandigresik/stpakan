<?php

ini_set('memory_limit', '2500000M');
set_time_limit(10000000000);

$image_file = base_url() . "assets/images/feedmill_logo.png";
echo '
	<table width="100%" style="font-family:Arial">
		<tr>
			<td style="width:8%"><img src="' . $image_file . '" alt="test alt attribute" border="0" /></td>
			<td style="width:92%">
				<span style="font-weight:bold;font-size:8px;">PT. WONOKOYO JAYA CORPORINDO</span><br>
				<span style="font-weight:bold;font-size:6px;">DIVISI FEEDMILL</span><br>
				<span style="font-weight:bold;font-size:6px;">UNIT GEMPOL</span>
			</td>
		</tr>
	</table>	
	<br><br>
	<table width="100%" style="font-family:Arial;font-size:6px;" id="header-barang">
		<tr>
			<td align="center" style="font-size:8px;"><b>FARM '.$nama_farm.'</b></td>
		</tr>
		<tr>
			<td align="center" style="font-size:6px;"><b>'.$no_permintaan.'</b></td>
		</tr>
		<tr>
			<td align="center" style="">&nbsp;</td>
		</tr>
	</table>'.
	$data_html;
?>

<style>
    table#header-barang td{
        height: 10px;
    }
    table.custom_table{
    	font-family: Arial;
    	font-size: 6px;
    	width: 100%;
        border: solid 2px black;
        border-collapse: collapse;
    }
    table.custom_table tr td{
        height: 10px;
        border: solid 2px black;
        border-collapse: collapse;
        vertical-align: middle;
        text-align: center;
    }
    table.custom_table tr th{
        height: 15px;
        border: solid 2px black;
        border-collapse: collapse;
        font-weight: bold;
        vertical-align: middle;
        text-align: center;
    }
    .number{
        text-align: right;
    }
</style>