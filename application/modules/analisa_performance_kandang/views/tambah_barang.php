
<?php
$list_kode_barang = [];
$list_tipe_barang = [];
foreach ($tambah_barang as $key => $value) {
    if (!array_key_exists($value ['kode_barang'], $list_kode_barang)) {
        $list_kode_barang [$value ['kode_barang']] = $value ['kode_barang'];
        $list_tipe_barang [$value ['tipe_barang']] = $value ['tipe_barang'];
    }
}
?>
<table class="table table-bordered text-center" id="tbl-tambah-barang">
    <thead>
        <tr>
            <th class="text-center col-md-2"><select
                    class="form-control filter kode-barang" onchange="filter(this)"
                    name="kode-barang" placeholder="Kode Barang">

                    <option value="Semua" selected>Semua</option>
                    <?php foreach ($list_kode_barang as $key => $value) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                    <?php } ?>
                </select></th>
            <th class="text-center col-md-2"><input type="text"
                                                    onkeyup="filter(this)" class="form-control filter nama-barang"
                                                    name="nama-barang" placeholder="Nama Barang"></th>
            <th class="text-center col-md-2"><input type="text"
                                                    onkeyup="filter(this)" class="form-control filter grup" name="grup"
                                                    placeholder="Grup"></th>
            <!--th class="text-center form-group col-md-4"-->
            <th class="text-center form-group col-md-2">
                <!--div class="col-md-7"--> <select
                    class="form-control filter tipe-barang" onchange="filter(this)"
                    name="tipe-barang" placeholder="Tipe Barang">
                    <option value="Semua" selected>Semua</option>
                    <?php foreach ($list_tipe_barang as $key => $value) { ?>
                        <option value="<?php echo $key; ?>"><?php echo $key; ?></option>
                    <?php } ?>
                </select> <!--/div>
        <div>
                <a href="#" class="btn btn-default col-md-2" onclick="search()">Search</a>
        </div-->
            </th>
        </tr>
        <tr>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Nama Barang</th>
            <th class="text-center">Grup</th>
            <th class="text-center">Tipe Barang</th>
        </tr>
    </thead>
    <tbody>
        <?php $data_ke = 1; ?>
        <?php foreach ($tambah_barang as $key => $value) { ?>
            <tr data-ke="<?php echo $data_ke; ?>" ondblclick="selected(this)">
                <td class='fkode-barang'><?php echo $value['kode_barang']; ?></td>
                <td class='fnama-barang'><?php echo $value['nama_barang']; ?></td>
                <td class='fgrup'><?php echo $value['grup_barang']; ?></td>
                <td class='ftipe-barang'><?php echo $value['tipe_barang']; ?></td>
            </tr>
            <?php $data_ke++; ?>
        <?php } ?>
    </tbody>
</table>