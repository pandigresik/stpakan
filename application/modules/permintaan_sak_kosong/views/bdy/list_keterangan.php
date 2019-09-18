<option value="">Pilih</option>
<?php
  if(!empty($keterangan)){
    foreach($keterangan as $ls){
      echo '<option value="'.$ls->KODE_BUDGET.'">'.$ls->NAMA_BUDGET.'</option>';
    }
  }
?>
