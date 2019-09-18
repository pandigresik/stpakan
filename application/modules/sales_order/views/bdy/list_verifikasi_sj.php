<form id="verifikasi_sj_form"  data-parsley-validate class="form-horizontal form-label-left col-md-10" onsubmit="return StandarProduksi.saveData(this)">
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_no_sj">
            Nomor Surat Jalan
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <input readonly type="text" value="<?php echo $data_sj['no_sj']?>" id="fm_no_sj" name="no_sj" class="form-control col-md-7 col-xs-12">
        </div>
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_nama_pelanggan">
            Pelanggan
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <input readonly type="text" value="<?php echo $data_sj['nama_pelanggan']?>" id="fm_nama_pelanggan" name="nama_pelanggan" class="form-control col-md-7 col-xs-12">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_tgl_realisasi">
            Tanggal Kirim
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <input readonly type="text" value="<?php echo tglIndonesia($data_sj['tgl_realisasi'])?>" id="fm_tgl_realisasi" name="tgl_realisasi" class="form-control col-md-7 col-xs-12">
        </div>
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_alamat_pelanggan">
            Alamat Pelanggan
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <textarea readonly type="text" id="fm_alamat_pelanggan" name="alamat_pelanggan" class="form-control col-md-7 col-xs-12"><?php echo $data_sj['alamat_pelanggan']?></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-6 col-sm-6 col-xs-6"></label>

        <label class="control-label col-md-3 col-sm-3 col-xs-3">
            Kota / Provinsi
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3" style="width: 12.5%;">
            <input readonly type="text" value="<?php echo $data_sj['kota_pelanggan']?>" id="fm_kota_pelanggan" name="kota_pelanggan" class="form-control col-md-1 col-xs-1">
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3" style="width: 12.5%;">
            <input readonly type="text" value="<?php echo $data_sj['provinsi_pelanggan']?>" id="fm_provinsi_pelanggan" name="provinsi_pelanggan" class="form-control col-md-1 col-xs-1">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-6 col-sm-6 col-xs-6">

        </label>
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_nama_sopir">
            Sopir
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <input readonly type="text" value="<?php echo $data_sj['nama_sopir']?>" id="fm_nama_sopir" name="nama_sopir" class="form-control col-md-7 col-xs-12">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-6 col-sm-6 col-xs-6">

        </label>
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_no_kendaraan">
            No. Pol Kendaraan
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <input readonly type="text" value="<?php echo $data_sj['no_kendaraan']?>" id="fm_no_kendaraan" name="no_kendaraan" class="form-control col-md-7 col-xs-12">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-6 col-sm-6 col-xs-6">

        </label>
        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="fm_no_telp_sopir">
            No. HP Sopir
        </label>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <input readonly type="text" value="<?php echo $data_sj['no_telp_sopir']?>" id="fm_no_telp_sopir" name="no_telp_sopir" class="form-control col-md-7 col-xs-12">
        </div>
    </div>

</form>

<div class="col-md-12">
	<div class="panel panel-primary">
		<div class="panel-heading">Detail Pengiriman</div>
		<div class="panel-body">
            <div id="detail_grading">
                <table id="datatable-fixed-header" class="table table-striped table-bordered detail_grading">
                    <thead>
                        <tr>
                            <th class="text-center" style="vertical-align:middle">Banyaknya</th>
                            <th class="text-center" style="vertical-align:middle">Nama Barang</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data_detail_sj)):?>
                            <?php foreach ($data_detail_sj as $key => $value):?>
                                <tr>
                                    <td class="text-center"><?php echo $value->jumlah?></td>
                                    <td class="text-center"><?php echo $value->kode_barang?></td>
                                </tr>
                            <?php endforeach;?>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>


<style>
    td span {
        line-height: 0px;
    }
</style>
