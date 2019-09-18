<!--pre><?php //print_r($list); ?></pre-->
<?php if(isset($list['header_barang'])){ ?>
<input type="text" class="form-control hide confirmed" id="confirmed"
readonly=true name="confirmed" value="<?php echo $list['konfirmasi']; ?>">

<div class="panel panel-default">
	<div class="panel-heading new-line">Detail Barang Rusak</div>
	<div class="panel-body">
		<div id="detail-penerimaan-barang-rusak-table" class="new-line">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="col-md-2">Kode Barang</th>
						<th class="col-md-2">Nama Barang</th>
						<th class="col-md-2">Bentuk</th>
						<th class="col-md-2">Jumlah SJ (Zak)</th>
						<th class="col-md-2">Jumlah Aktual</th>
					</tr>
				</thead>
				<tbody>
                    <?php $nomor = 1; ?>
                    <?php foreach ($list['header_barang'] as $key => $value) { ?>
					<?php if($value['jml_rusak'] >= 0){ ?>
                    <tr class="header-barang-rusak" onclick="show_input_rusak(this)" data-ke="<?php echo $nomor; ?>">
						<td class='h-kode-barang'><?php echo $value['kode_barang']; ?></td>
						<td><?php echo $value['nama_barang']; ?></td>
						<td><?php echo $value['bentuk_barang']; ?></td>
						<td class='h-jml-sj'><?php echo $value['jml_sj']; ?></td>
						<td class='h-jml-sisa'><?php echo $value['jml_rusak']; ?></td>
					</tr>
                    <tr class="hide tmp-header-barang-rusak">
                        <td></td>
                        <td colspan='4'>
                            <table class="tabel_input_rusak table table-bordered" data-ke="<?php echo $nomor; ?>" style="width: 80%">
                                <thead>
                                    <tr>
                                        <th class="">No.</th>
                                        <th class="col-md-3">Berat (Kg)</th>
                                        <th class="col-md-8">Keterangan</th>
                                        <th class=""></th>
                                    </tr>
                                </thead>
                                <tbody>
                                	<?php if(!empty($value['detail_barang'][0]['jml_putaway'])){ ?>
				                    <?php $no = 1; ?>
				                    <?php foreach ($value['detail_barang'] as $k => $v) { ?>
	                                    <tr data-ke="<?php echo $no; ?>" class="row-timbang">
	                                        <td><?php echo $no; ?>.</td>
	                                        <td><input type="text"
	                                            class="form-control berat-rusak" onchange="kontrol_berat_rusak(this)" onkeyup="number_only(this)" id="berat-rusak"
	                                            readonly=true name="berat-rusak" placeholder="Berat" value="<?php echo $v['berat_putaway']; ?>"></td>
	                                        <td><input type="text" class="form-control keterangan-rusak"
	                                                    readonly=true id="keterangan-rusak" name="keterangan-rusak"
	                                                    placeholder="Keterangan" value="<?php echo $v['keterangan_rusak']; ?>">

	                                        </td>
	                                        <td>
	                                                <!--div onclick="tambah_timbang_rusak(this)">

	                                                <?php //if(count($value['detail_barang']) == $no){?>
	                                                    <span class="glyphicon glyphicon-plus"></span>
	                                                <?php //} else { ?>
	                                                    <span class="glyphicon glyphicon-minus"></span>
	                                                <?php //} ?>
	                                                </div-->
	                                        </td>
	                                    </tr>
                                	<?php $no++; ?>
                                	<?php } ?>
                                	<?php } else{ ?>
	                                    <tr data-ke="1" class="row-timbang">
	                                        <td>1.</td>
	                                        <td><input type="text"
	                                            class="form-control berat-rusak" onchange="kontrol_berat_rusak(this)" onkeyup="number_only(this)" id="berat-rusak"
	                                            name="berat-rusak" placeholder="Berat" value=""></td>
	                                        <td><input type="text" class="form-control keterangan-rusak"
	                                                    id="keterangan-rusak" name="keterangan-rusak"
	                                                    placeholder="Keterangan" value="">

	                                        </td>
	                                        <td>
	                                                <div onclick="tambah_timbang_rusak(this)">
	                                                    <span class="glyphicon glyphicon-plus"></span>
	                                                </div>
	                                        </td>
	                                    </tr>
                                	<?php } ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php $nomor++; ?>
                    <?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<div class="panel-heading new-line">Detail Barang Kurang</div>
	<div class="panel-body">
		<div id="detail-penerimaan-barang-kurang-table" class="new-line">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="col-md-2">Kode Barang</th>
						<th class="col-md-2">Nama Barang</th>
						<th class="col-sm-1">Bentuk</th>
						<th class="col-sm-1">Jumlah SJ (Zak)</th>
						<th class="col-sm-1">Jumlah Aktual</th>
						<th class="col-md-4">Keterangan</th>
					</tr>
				</thead>
				<tbody>
                    <?php $nomor = 1; ?>
					<?php foreach ($list['header_barang'] as $key => $value) { ?>
					<?php if($value['jml_kurang'] >= 0){ ?>
					<tr class="header-barang-kurang" data-ke="<?php echo $nomor; ?>">
						<td class='h-kode-barang'><?php echo $value['kode_barang']; ?></td>
						<td><?php echo $value['nama_barang']; ?></td>
						<td><?php echo $value['bentuk_barang']; ?></td>
						<td class='h-jml-sj'><?php echo $value['jml_sj']; ?></td>
						<td class='h-jml-sisa'><?php echo $value['jml_kurang']; ?></td>
						<td><input type="text" class="form-control keterangan-kurang"
							id="keterangan-kurang" name="keterangan-kurang"
							placeholder="Keterangan" value="<?php echo (empty($value['keterangan_kurang'])) ? '' : $value['keterangan_kurang']; ?>" <?php echo (empty($value['keterangan_kurang'])) ? '' : 'readonly'; ?>></td>
					</tr>
					<?php } ?>
                    <?php $nomor++; ?>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php } 
else{
?>
<div class="">
	Tidak ada barang rusak/kurang, semua barang normal.
</div>
<?php } ?>
