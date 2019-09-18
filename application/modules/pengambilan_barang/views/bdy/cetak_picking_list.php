<?php
    $tgl_kebutuhan = '';
    if(isset($items[0]['tgl_keb_awal'])){
        if($grup_farm == 'brd'){
            $tgl_kebutuhan = convert_month($items[0]['tgl_keb_awal'], 1) . ' s/d ' . convert_month($items[0]['tgl_keb_akhir'], 1);
        }
        else{
            $tgl_kebutuhan = convert_month($items[0]['tgl_keb_awal'], 1);
        }
    }
    
?>
<div class="row">
    <div class="col-md-12">
        <a
            href='pengambilan_barang/transaksi/cetak_daftar_pengambilan?no_order=<?php echo $no_order; ?>&pick=1'
            class='link btn btn-default' target="_blank">Print</a>
    </div>
    <div class="col-md-12">
        <div class="text-center header-content">
            <h2>Picking List</h2>
        </div>
        <div class="new-line">
            <div class="col-md-6 left-content">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">No. Pengambilan</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['no_order']) ? strtoupper($items[0]['no_order']) : ''; ?></label>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">Farm</label> <label
                            for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['farm']) ? strtoupper($items[0]['farm']) : ''; ?></label>
                    </div>
                </div>
            </div>
            <div class="col-md-6 right-content">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">Tanggal Pengiriman</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo isset($items[0]['tgl_kirim']) ? convert_month($items[0]['tgl_kirim'], 1) : ''; ?></label>

                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-4">Tanggal Kebutuhan</label>
                        <label for="inputEmail3" class="col-sm-1">:</label> <label
                            for="inputEmail3" class="col-sm-5"><?php echo $tgl_kebutuhan; ?></label>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div id="print-preview-table">
                <table class="table table-bordered table-content">
                    <thead>
                        <tr>
                            <th>Flock</th>
                            <th>Kode Kandang</th>
                            <?php if($grup_farm == 'brd') {?>
                            <th>Jenis Kelamin</th>
                            <?php } ?>
                            <th>ID Kavling</th>
                            <?php if($grup_farm == 'brd') {?>
                            <th>Kode Pakan</th>
                            <?php } ?>
                            <th>Nama Pakan</th>
                            <?php if($grup_farm == 'brd') {?>
                            <th>Bentuk</th>
                            <th>Stok Gudang</th>
                            <?php } ?>
                            <th>Jumlah (sak)</th>                                                  
                            <th>Paraf</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach ($items as $key => $value) { ?>                        
                            <?php if ($value['keterangan'] == 0) { ?>
                                <tr>                                
                                    <td><?php echo $value['flock']; ?></td>
                                    <td><?php echo $value['kode_kandang']; ?></td>
                                    <?php if($grup_farm == 'brd') {?>
                                    <td><?php echo $value['jenis_kelamin']; ?></td>
                                    <?php } ?>
                                    <td><?php echo $value['kode_pallet']; ?></td>
                                    <?php if($grup_farm == 'brd') {?>
                                    <td><?php echo $value['kode_barang']; ?></td>
                                    <?php } ?>
                                    <td><?php echo $value['nama_barang']; ?></td>
                                    <?php if($grup_farm == 'brd') {?>
                                    <td><?php echo $value['bentuk_pakan']; ?></td>
                                    <td><?php echo $value['jml_stok_gudang']; ?></td>
                                    <?php } ?>
                                    <td><?php echo $value['kebutuhan_pakan']; ?></td>                                   
                                    <td></td>
                                </tr>
                            <?php } ?>                            
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>