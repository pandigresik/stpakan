<table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No. DO</th>
                        <th>No. OP</th>
                        <th>No. SJ</th>
                        <th>Ekspedisi</th>
                        <th>Target Tanggal Kirim</th>
                    </tr>
                </thead>
                <tbody>
        <?php foreach ($list as $key => $value) { ?>
                        <tr data-kode-flok="<?php echo $value['kode_flok']; ?>"
                            data-nama-ekspedisi="<?php echo $value['nama_ekspedisi']; ?>"
                        >
                            <td><?php echo $value['no_do']; ?></td>
                            <td><?php echo $value['no_op']; ?></td>
                            <td><?php echo (empty($value['no_sj'])) ? 'N/A' : $value['no_sj']; ?></td>
                            <td><?php echo $value['nama_ekspedisi']; ?></td>
                            <td><?php echo date('d M Y', strtotime($value['tanggal_kirim'])); ?></td>
                            
                        </tr>
        <?php } ?>
                </tbody>
</table>