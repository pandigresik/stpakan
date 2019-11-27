<?php
  $_readonly_over = 'readonly';
  $_readonly = '';
  $_disable = '';
  if(isset($readonly)){
    $_readonly = $readonly;
    if(!empty($readonly)){
      $_disable = 'disabled';
    }
  }
  if (isset($readonly_over)) {
    $_readonly_over = $readonly_over;
  }


?>
<div class="row col-md-12">
  <form class="form form-horizontal form_permintaan" onsubmit="return false">
      <div class="btn-group" style="margin-bottom:10px">
          <button class="btn btn-default" type="button" onclick="permintaanSak.refresh_page()">Kembali</button>
          <?php echo isset($tombol) ? $tombol : '' ?>
      </div>
    <div class="form-group">
      <label class="control-label col-md-2">No. Permintaan Sak</label>
      <div class="col-md-3">
        <input type="text" class="form-control" readonly  name="no_ppsk" value="<?php echo $no_ppsk ?>" />
        <input type="hidden" name="kodeSiklus" id="kodeSiklus" value="<?php echo $kode_siklus?>"/>
      </div>
      <label class="control-label col-md-3">Tanggal permintaan</label>
      <div class="control-label col-md-1" style="color:#00F">
        <input type="hidden" name="tglPermintaan" id="tglPermintaan" value="<?php echo $tgl_permintaan?>"/>
        <?php echo $tgl_permintaan_text?>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-md-2">Kebutuhan</label>
      <div class="col-md-3">
        <select class="form-control" name="keterangan" id="keterangan" <?php echo $_disable ?> onchange="loadTotalBudget(this)" >
          <option value="">Pilih</option>
          <?php
            if(!empty($keterangan)){
              foreach($keterangan as $ls){
                if($ls->KODE_BUDGET == $kode_budget){
                  $selected = "selected";
                }
                else{
                  $selected = "";
                }
                if(!in_array($ls->KODE_BUDGET,$sudah_minta)){
                  echo '<option '.$selected.' value="'.$ls->KODE_BUDGET.'">'.$ls->NAMA_BUDGET.'</option>';
                }
              }
            }
          ?>
        </select>
      </div>
      <label class="control-label col-md-3">Tanggal Kebutuhan</label>
      <div class="control-label col-md-1" style="color:#00F">
        <input type="hidden" name="tglKebutuhan" id="tglKebutuhan" value="<?php echo $tgl_kebutuhan?>"/>
        <input type="hidden" name="tglSekarang" id="tglSekarang" value="<?php echo $tgl_sekarang?>"/>
        <?php echo $tgl_kebutuhan_text?>
      </div>
    </div>
    <div class="form-group">
      <div class="col-md-6 col-md-offset-4">

        <span id="tooltip-reject"></span>
        <span class="tooltipster-span hide">
          <div class="panel panel-primary" style="margin-bottom: 0px">
            <div class="panel-heading">Konfirmasi Reject</div>
            <div class="panel-body">
              <div class="form-group">
                <div style="margin-bottom: 5px">
                  <span>Mohon mengisi keterangan reject <br>(Min. 10 karakter)</span>
                </div>
                <textarea class="form-control" onkeyup="lengthCek(this)" cols="50" id="keterangan_reject" name="keterangan_reject"></textarea>
              </div>
              <div class="form-group pull-right" style="margin-bottom:0px">
                <button class="btn btn-default" onclick="$('#tooltip-reject').tooltipster('hide');">batal</button>
                <button class="btn btn-primary btn_simpan_reject" disabled style="margin-left: 5px" onclick="permintaanSak.reject(this)">Simpan</button>
              </div>
            </div>
          </div>
        </span>
      </div>
    </div>

    <div class="row col-md-12" >
		<div id="div_list_permintaan">
            <div class="panel panel-primary">
            	<div class="panel-heading">Persediaan Sak </div>
            	<div class="panel-body">
            		<!--
            		<table class="table table-bordered custom_table list_permintaan">
            		-->
            		<table class="table table-bordered persediaan_sak_table">
            			<thead>
                            <tr>
        						<th rowspan="2">Stok tersedia</th>
        						<th rowspan="2" id="th_keterangan">
        							Sisa Budget
        						</th>
        						<th rowspan="2">Jumlah yang diminta</th>
        						<th colspan="2">Over Budget</th>

        						<th rowspan="2">Keterangan</th>

        					</tr>
        					<tr>
        						<th>Jumlah</th>
        						<th>Alasan</th>
        					</tr>
            			</thead>
            			<tbody>
            				<?php
                    //cetak_r($budget_sisa);
                            echo '<tr >
                                <td id="sak_tersimpan">'.$sak_tersimpan.'</td>
                                <td id="budget_sisa">'.(isset($budget_sisa) ? $budget_sisa : '-').'</td>
                                <td id="jml_diminta">'.(isset($jml_sak) ? $jml_sak : '-').'</td>
                                <td id="jml_over">'.(isset($jml_over) ? $jml_over : '-').'</td>
                                <td><textarea id="alasan" name="alasan" rows="4" disabled="disabled">'.(isset($alasan_over) ? $alasan_over : '').'</textarea></td>
                                <td>'.(isset($remarks) ? $remarks : '-').'</td>
                            </tr>';
                            ?>
            			</tbody>
            		</table>
            	</div>
            </div>

		</div>
	</div>
    <div class="row col-md-5" >
		<div id="div_list_permintaan">
            <div class="panel panel-primary">
            	<div class="panel-heading">Daftar Permintaan Sak </div>
            	<div class="panel-body">
            		<!--
            		<table class="table table-bordered custom_table list_permintaan">
            		-->
            		<table class="table table-bordered permintaan_sak_table">
            			<thead>
                            <tr>
                            	<th>Kandang</th>
                            	<th>Umur</th>
                            	<th>Jml Sak</th>
                              <?php
                              if(isset($status) && $status == 'A'){
                                echo '<th>Penerima</th>';
                              }
                              ?>
                            </tr>
            			</thead>
            			<tbody>
            				<?php
                    if(isset($listPermintaan)){
                      echo $listPermintaan;

                    }else{
                      if(!empty($daftarpermintaan)){
                        foreach($daftarpermintaan as $minta){
                          echo '<tr >

                          </tr>';
                        }
                      }
                      else{
                        echo '<tr><td colspan=11>Data tidak ditemukan</td></tr>';
                      }
                    }

                            ?>
            			</tbody>
            			<tfoot>
            			</tfoot>
            		</table>
            	</div>
            </div>

		</div>
	</div>
  </form>

</div>

<form id="loginForm" method="post" class="form-horizontal cekRfidForm" style="display: none;">
    <input type="hidden" name="pop_jml_diminta">
    <input type="hidden" name="pop_no_reg">
    <div class="form-group">
        <label class="col-xs-8">RFID kandang</label>
        <div class="col-xs-3">
            <input type="text" name="rfid_kandang" data-kode-kandang="" class="form-control rfid_kandang text-center"
               value="" onchange="permintaanSak.cekRfidKandang(this)">
        </div>
    </div>
    <div class="form-group berat hide">
        <label class="col-xs-8">Timbang berat sesuai jumlah yang diambil (Kg)</label>
        <div class="col-xs-3">
            <input type="text" placeholder=""
               name="berat_timbang" class="form-control berat-timbang text-center"
               value=""
               onchange="permintaanSak.get_berat_timbang(this)">
            <!--input type="text" placeholder=""
               name="berat_timbang" class="form-control berat-timbang text-center"
               value="" readonly
               onchange="permintaanSak.get_berat_timbang(this)"
               onpaste="permintaanSak.get_berat_timbang(this)" onkeyup="permintaanSak.replace_timbang(this)"
               onclick="permintaanSak.selected(this)"-->
        </div>
    </div>

    <!--div class="form-group">
        <div class="col-xs-3 col-xs-offset-8">
            <button type="submit" class="btn btn-default">Ya</button>
        </div>
    </div-->
</form>
