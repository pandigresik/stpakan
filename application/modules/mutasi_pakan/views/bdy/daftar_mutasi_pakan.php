
<table class="table table-bordered" id="tabel_daftar_mutasi_pakan">
    <thead>
        <tr>
            <th class="no_mutasi" rowspan="2">No. Mutasi</th>
            <th class="tanggal_pemberian" rowspan="2">Tanggal Pemberian</th>
            <th class="tanggal_kebutuhan" rowspan="2">Tanggal Kebutuhan</th>
            <th class="jenis_pakan" rowspan="2">Jenis<br>Pakan</th>
            <th class="jumlah_mutasi" rowspan="2">Kuantitas Mutasi<br>(sak)</th>
            <th colspan="2">Kandang Asal</th>
            <th class="status_permintaan_mutasi" rowspan="2">Status Permintaan Mutasi</th>
            <th colspan="3">Tindak Lanjut</th>
        </tr>
        <tr>
            <th class="kandang">Kandang</th>
            <th class="umur">Umur (hari)</th>
            <th class="tindak_lanjut_kepala_farm <?php echo isset($tindak_lanjut_kd) ? $tindak_lanjut_kd : ''; ?>">Kepala Farm</th>
            <th class="tindak_lanjut_kepala_departemen">Kepala Departemen</th>
            <th class="tindak_lanjut_kepala_divisi">Kepala Divisi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($data_mutasi_pakan) > 0) { ?>
            <?php foreach ($data_mutasi_pakan as $key => $value) { ?>
                <tr>
                    <td class="no_mutasi" data-level-user="<?php echo $level_user; ?>" data-kode-farm="<?php echo $value['kode_farm']; ?>"><span onclick="detail_mutasi(this)"><?php echo $value['no_mutasi']; ?></span></td>
                    <td class="tanggal_pemberian"><?php echo date('d M Y', strtotime($value['tanggal_pemberian'])); ?></td>
                    <td class="tanggal_kebutuhan"><?php echo date('d M Y', strtotime($value['tanggal_kebutuhan'])); ?></td>
                    <td class="jenis_pakan" data-jenis-pakan="<?php echo $value['id_jenis_pakan']; ?>"><?php echo $value['jenis_pakan']; ?></td>
                    <td class="jumlah_mutasi"><?php echo $value['jumlah_mutasi']; ?></td>
                    <td class="kandang" data-no-reg="<?php echo $value['no_reg_asal']; ?>"><?php echo $value['kandang']; ?></td>
                    <td class="umur"><?php echo $value['umur']; ?></td>
                    <td class="status_permintaan_mutasi"><?php echo $value['status_permintaan_mutasi']; ?></td>
                    <td class="tindak_lanjut_kepala_farm">
                        <?php echo empty($value['alasan_tindak_lanjut_kepala_farm']) ? '' : '<p class="alasan">'.$value['alasan_tindak_lanjut_kepala_farm'].'</p>'; ?>
                        <?php if ($value['tindak_lanjut_kepala_farm'] == '0') { ?>
                            
                        <?php } else  if ($value['tindak_lanjut_kepala_farm'] == '1') { ?>
                            <?php echo empty($value['alasan_tindak_lanjut_kepala_farm']) ? '' : '<br>'; ?>
                            <span onclick="revisi(this)" class="btn btn-default btn_revisi">Revisi</span>
                        <?php } else  if ($value['tindak_lanjut_kepala_farm'] == '2') { ?>
                            <?php echo empty($value['alasan_tindak_lanjut_kepala_farm']) ? '' : '<br>'; ?>
                            <span onclick="ack(this)" class="btn btn-default btn_ack">Ack</span>
                        <?php } else { ?>
                            <?php echo empty($value['alasan_tindak_lanjut_kepala_farm']) ? '' : '<br>'; ?>
                            <?php echo $value['tindak_lanjut_kepala_farm']; ?>
                        <?php } ?>
                    </td>
                    <td class="tindak_lanjut_kepala_departemen" data-waktu-tindak-lanjut="<?php echo $value['waktu_tindak_lanjut_kepala_departemen']; ?>">
                        <?php $waktu_tindak_lanjut_kepala_departemen = empty($value['waktu_tindak_lanjut_kepala_departemen']) ? '' : date('d M Y',strtotime($value['waktu_tindak_lanjut_kepala_departemen'])); ?>
                        <?php if(!empty($waktu_tindak_lanjut_kepala_departemen)) { ?>
                        <p><?php echo $value['tindak_lanjut_kepala_departemen']; ?> <span class="waktu">- <?php echo $value['status_permintaan_mutasi'].' '.$waktu_tindak_lanjut_kepala_departemen; ?></span></p>
                        <?php } ?>
                        <form class="form-inline hide">
                            <div class="form-group">
                                <textarea class="form-control keterangan" rows="2" cols="10" name="keterangan" onkeyup="keterangan_kontrol(this)"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="text-left">
                                    <span class="btn btn-default btn_approve" onclick="konfirmasi_tindak_lanjut(this,1)" disabled>Approve</span>
                                </div>
                                <div class="text-left btn-group"> 
                                    <span aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" class="btn_reject btn btn-default dropdown-toggle" disabled>Reject >> 
                                    </span> 
                                    <ul class="dropdown-menu"> 
                                        <li><a onclick="konfirmasi_tindak_lanjut(this,2)">Review Ulang</a></li> 
                                        <li><a onclick="konfirmasi_tindak_lanjut(this,0)">Reject</a></li> 
                                    </ul> 
                                </div>
                            </div>
                        </form>
                    </td>
                    <td class="tindak_lanjut_kepala_divisi" data-waktu-tindak-lanjut="<?php echo $value['waktu_tindak_lanjut_kepala_divisi']; ?>">
                        <?php $waktu_tindak_lanjut_kepala_divisi = empty($value['waktu_tindak_lanjut_kepala_divisi']) ? '' : date('d M Y',strtotime($value['waktu_tindak_lanjut_kepala_divisi'])); ?>
                        <?php if(!empty($waktu_tindak_lanjut_kepala_divisi)) { ?>
                        <p><?php echo $value['tindak_lanjut_kepala_divisi']; ?> <span class="waktu">- <?php echo $value['status_permintaan_mutasi'].' '.$waktu_tindak_lanjut_kepala_divisi; ?></span></p>
                        <?php } ?>
                        <form class="form-inline hide">
                            <div class="form-group">
                                <textarea class="form-control keterangan" rows="2" cols="10" name="keterangan" onkeyup="keterangan_kontrol(this)"></textarea>
                            </div>
                            <div class="form-group">
                                <div class="text-left">
                                    <span class="btn btn-default btn_approve" onclick="konfirmasi_tindak_lanjut(this,3)" disabled>Approve</span>
                                </div>
                                <div class="text-left btn-group"> 
                                    <span aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" class="btn_reject btn btn-default dropdown-toggle" disabled>Reject >> 
                                    </span> 
                                    <ul class="dropdown-menu"> 
                                        <li><a onclick="konfirmasi_tindak_lanjut(this,4)">Review Ulang</a></li> 
                                        <li><a onclick="konfirmasi_tindak_lanjut(this,0)">Reject</a></li> 
                                    </ul> 
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="11">Tidak ada data.</td>
            </tr>
        <?php } ?>
    </tbody>
</table>