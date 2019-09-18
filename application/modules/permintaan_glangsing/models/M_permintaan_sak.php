<?php
class M_permintaan_sak extends CI_Model{
  private $dbSqlServer;
  private $akses_reject;
  private $switch_user;

  public function __construct(){
    parent::__construct();
    $this->dbSqlServer = $this->load->database('default', TRUE);

    $this->akses_reject = array(
      'KFM' => array('WKDP','KDP','WKDV','KDV','WKBA'),
      'WKDP'=> array('WKDP','KDP','WKDV','KDV','WKBA'),
      'KDP' => array('WKDP','KDP','WKDV','KDV','WKBA'),
      'WKDV'=> array('WKDV','KDV','WKBA'),
      'WKBA'=> array('WKDV','KDV','WKBA')
    );

    $this->switch_user = array(
      'KDV' => 'KDV',
      'KDV' => 'WKDV', /* memiliki hak akses yang sama dengan kadiv*/
      'DP'  => 'KDP',
      'KD'  => 'KDP',
      //'KD'  => 'WKDP',
      'KF'  => 'KFM',
      'P'   => 'PPB',
      'P'   => 'KPPB',
      'AG'  => 'AGF',
      'KA'  => 'KBA',
      'KA'  => 'ABP',
      'KDB' => 'WKBA'
    );

    $this->_user_level = $this->session->userdata('level_user');

  //	$this->_table = 'lpb';
  }
  /* cari sak yang bisa diminta pada siklus yang aktif saja */
  public function sakTersedia($kodeFarm, $kodeBarang = 'GBP'){
    $where = '';
    if($kodeBarang != ''){
      $where = " and a.kode_barang = '".$kodeBarang."'";
    }
    $sql = <<<SQL
          select a.kode_farm, a.kode_siklus, a.kode_barang, a.jml_akhir sak_tersedia
          from glangsing_movement_d a
          join (
          select kode_farm, kode_barang, max(tgl_buat) tgl_buat from glangsing_movement_d
          group by kode_farm, kode_barang
          ) b on a.kode_farm = b.kode_farm and a.kode_barang = b.kode_barang and a.tgl_buat = b.tgl_buat
          where a.kode_farm = '{$kodeFarm}' {$where}
SQL;
    return $this->db->query($sql);
  }
  /*public function sakTersedia($kodefarm){
    $sql = <<<SQL
    select sakKembali.total_sak - coalesce(sakDiminta.total_sak,0) sak_tersedia
          ,sakKembali.prefix_ppsk
    from(
      select sum(rskitp.JML_SAK) total_sak,
      ('PPSK/'+mp.kode_farm+'/'+mp.periode_siklus+'/') as prefix_ppsk
          from RETUR_SAK_KOSONG rsk
          inner join RETUR_SAK_KOSONG_ITEM_PAKAN rskip
            on rsk.id = rskip.RETUR_SAK_KOSONG
          inner join RETUR_SAK_KOSONG_ITEM_TIMBANG_PAKAN rskitp
            on rskitp.RETUR_SAK_KOSONG_ITEM_PAKAN = rskip.ID
          inner join KANDANG_SIKLUS ks
            on ks.NO_REG = rsk.NO_REG and ks.kode_farm = 'BW'
                      inner join M_PERIODE mp
                              on mp.kode_siklus = ks.kode_siklus and mp.kode_farm = ks.kode_farm and mp.status_periode = 'A'
          group by mp.periode_siklus,mp.kode_farm
    )sakKembali
    left join (
      select left(no_ppsk,15) prefix_ppsk,sum(jml_diminta) total_sak
      from ppsk_new ph
      where no_ppsk not in (
        select no_ppsk
        from log_ppsk_new
        where status = 'V'
        group by no_ppsk
        )
      group by left(no_ppsk,15)
    )sakDiminta on sakDiminta.prefix_ppsk = sakKembali.prefix_ppsk
SQL;
    return $this->db->query($sql);
  }*/



  public function listPermintaan($filter,$periode_siklus,$status,$order_table){
    $kodefarm = $filter['kode_farm'];
    unset($filter['kode_farm']);
    $tgl_kebutuhan = array();
    if(isset($filter['tgl_kebutuhan'])){
      $tgl_kebutuhan = $filter['tgl_kebutuhan'];
    };
    unset($filter['tgl_kebutuhan']);
    $whereTgl = '';
    if(!empty($tgl_kebutuhan)){
      if(count($tgl_kebutuhan) == 2){
        $whereTgl = ' ph.tgl_kebutuhan between \''.$tgl_kebutuhan['tgl_awal'].'\' and \''.$tgl_kebutuhan['tgl_akhir'].'\'';
      }else{
        if(isset($tgl_kebutuhan['tgl_awal'])){
          $whereTgl = ' ph.tgl_kebutuhan >= \''.$tgl_kebutuhan['tgl_awal'].'\'';
        }else{
          $whereTgl = ' ph.tgl_kebutuhan <= \''.$tgl_kebutuhan['tgl_akhir'].'\'';
        }
      }
    }
    $wherePPsk = array();
    if(!empty($filter)){
      $_i = 0;
      foreach($filter as $k => $v){
        if($k == 'ph.no_ppsk'){
          array_push($wherePPsk,$k .' like \'%'.$v.'%\'');
        }else{
          array_push($wherePPsk,$k .' = \''.$v.'\'');
        }
        
        $_i++;
      }
    }
    if(!empty($whereTgl)){
      array_push($wherePPsk,$whereTgl);
    }

    $cariPPsk = implode(' and ',$wherePPsk);
    if(!empty($cariPPsk)){
      $cariPPsk = ' where '.$cariPPsk;
    }
    $level_user = $this->session->userdata('level_user');
    $sql = '';
    if($level_user == 'AG'){
        $sql = <<<SQL
            select mpe.kode_farm, ph.no_ppsk, ph.tgl_kebutuhan, ph.kode_budget, mb.nama_budget, ph.jml_diminta, ph.jml_over_budget, isnull(ph.keterangan,'') alasan
                , mp.nama_pegawai, lg.status, isnull(lg.keterangan, '') keterangan, lg.tgl_buat -- *
                , ph.kode_siklus, ph.tgl_permintaan
                , case when lg.status = 'N' then 'Dibuat'
                  when lg.status = 'R' then 'Dikoreksi'
                  when lg.status = 'RJ' then 'Ditolak'
                  when lg.status = 'A' and mp.grup_pegawai in('KBA','WKBA') then 'Disetujui'
                  when lg.status = 'A' and mp.grup_pegawai = 'KDV' then 'Disetujui'
                  when lg.status = 'V' then 'Revisi'
                  else 'Simpan Draf' end status_detail
              from ppsk_new ph
              join (
            select l1.*
            from log_ppsk_new l1
            join (
              select no_ppsk, max(no_urut) no_urut
              from log_ppsk_new
              group by no_ppsk
            ) l2 on l1.no_ppsk = l2.no_ppsk and l1.no_urut = l2.no_urut
            where l1.status = 'A' 
          ) lmax on ph.no_ppsk = lmax.no_ppsk
              join log_ppsk_new lg on ph.no_ppsk = lg.no_ppsk
              join M_BUDGET_PEMAKAIAN_GLANGSING mb on ph.kode_budget = mb.KODE_BUDGET
              join m_pegawai mp on lg.user_buat = mp.kode_pegawai
              join m_periode mpe on ph.kode_siklus = mpe.kode_siklus and mpe.kode_farm = '{$kodefarm}' and mpe.periode_siklus = '{$periode_siklus}'
              {$cariPPsk}
              order by ph.no_ppsk desc, lg.no_urut

SQL;
    }else{  
    $sql = <<<SQL
            select mpe.kode_farm, ph.no_ppsk, ph.tgl_kebutuhan, ph.kode_budget, mb.nama_budget, ph.jml_diminta, ph.jml_over_budget, isnull(ph.keterangan,'') alasan
                , mp.nama_pegawai, lg.status, isnull(lg.keterangan, '') keterangan, lg.tgl_buat -- *
                , ph.kode_siklus, ph.tgl_permintaan
                , case when lg.status = 'N' then 'Dibuat'
                  when lg.status = 'R' then 'Dikoreksi'
                  when lg.status = 'RJ' then 'Ditolak'
                  when lg.status = 'A' and mp.grup_pegawai in('KBA','WKBA') then 'Disetujui'
                  when lg.status = 'A' and mp.grup_pegawai = 'KDV' then 'Disetujui'
                  when lg.status = 'V' then 'Revisi'
                  else 'Simpan Draf' end status_detail
              from ppsk_new ph
              join log_ppsk_new lg on ph.no_ppsk = lg.no_ppsk
              join M_BUDGET_PEMAKAIAN_GLANGSING mb on ph.kode_budget = mb.KODE_BUDGET
              join m_pegawai mp on lg.user_buat = mp.kode_pegawai
              join m_periode mpe on ph.kode_siklus = mpe.kode_siklus and mpe.kode_farm = '{$kodefarm}' and mpe.periode_siklus = '{$periode_siklus}'
              {$cariPPsk}
              order by ph.no_ppsk desc, lg.no_urut 

SQL;
    }
    return $this->db->query($sql);
  }

  public function listHistori($kodefarm = '',$periode_siklus = '',$no_ppsk = ''){
    $sql = <<<SQL
    SELECT top 100
--lp.*,
'['+mp.NAMA_PEGAWAI+'] - '+
CASE
        WHEN lp.STATUS = 'D' THEN 'Draft, '
        WHEN lp.STATUS = 'N' THEN 'New (Rilis), '
        WHEN lp.STATUS = 'R' THEN 'Review, '
        WHEN lp.STATUS = 'RJ' THEN 'Rejected (<u style="color:blue">'+lp.KETERANGAN+'</u>), '
        WHEN lp.STATUS = 'A' THEN 'Approved'
        WHEN lp.STATUS = 'V' THEN 'Void, '
        END KETERANGAN,
        lp.TGL_BUAT
FROM log_ppsk_new lp
LEFT JOIN PPSK pp ON pp.NO_PPSK = lp.NO_PPSK
LEFT JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lp.USER_BUAT

WHERE lp.NO_PPSK LIKE '%$no_ppsk%'
ORDER BY lp.TGL_BUAT DESC
SQL;
    return $this->db->query($sql);
  }

  public function listBudgetGlangsing($kodefarm){
    $sql = <<<SQL
    select * from M_BUDGET_PEMAKAIAN_GLANGSING where status = 'A'
SQL;
    return $this->db->query($sql);
  }

  public function getBudgetTotal($kode_farm = '',$kategori_budget = '',$kode_budget = ''){
    $str   = '';
    if ($kode_budget != '') {
       $str = "And bgd.KODE_BUDGET = '$kode_budget'";
    }
    $sql = <<<QUERY
        SELECT sum(BGD.JML_ORDER) FROM BUDGET_GLANGSING_D BGD
        INNER JOIN M_PERIODE MP ON MP.KODE_SIKLUS = BGD.KODE_SIKLUS
        INNER JOIN BUDGET_GLANGSING BG ON BG.KODE_SIKLUS = BGD.KODE_SIKLUS
        INNER JOIN M_BUDGET_PEMAKAIAN_GLANGSING MBPG ON MBPG.KODE_BUDGET = BGD.KODE_BUDGET
        WHERE NO_URUT = (
            SELECT MAX(NO_URUT) FROM BUDGET_GLANGSING_D WHERE KODE_SIKLUS = MP.KODE_SIKLUS
        )
        and MP.KODE_FARM = '{$kode_farm}' AND MP.KODE_SIKLUS = (
            select top 1 ks.kode_siklus from kandang_siklus ks where ks.status_siklus = 'O' and mp.kode_farm = ks.kode_farm
        ) AND BG.STATUS = 'A'
        --group by BGD.KODE_BUDGET
        --AND BGD.KODE_BUDGET = '{$kategori_budget}'
       $str
QUERY;

    $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(0)[0];
  }

  public function getBudgetTerpakai($kodefarm = '',$kategori_budget = '',$kode_budget = ''){
    $sql = <<<QUERY
        select max(ph.jml_diminta) jml_diminta
        from ppsk_new ph
        join log_ppsk_new lg on ph.no_ppsk = lg.no_ppsk
        join (
          select no_ppsk, max(no_urut) no_urut
          from log_ppsk_new
          group by no_ppsk
          ) mx on lg.no_ppsk = mx.no_ppsk and lg.no_urut = mx.no_urut
        where lg.status <> 'RJ'
          and ph.kode_budget = '{$kode_budget}'
          and ph.kode_siklus in(
            select top 1 ks.kode_siklus
            from kandang_siklus ks
            where ks.status_siklus = 'O' and ks.kode_farm = '{$kodefarm}')
QUERY;
//print_r($sql);
    $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(0)[0];
  }

  public function getDaftarPermintaan($no_ppsk = '', $kodeBudget = '', $tglKebutuhan = null)
  {
      $sql = <<<QUERY
        select ks.no_reg, ks.FLOK_BDY, ks.KODE_SIKLUS, ks.KODE_FARM, mk.KODE_KANDANG, mk.NAMA_KANDANG, pd.no_ppsk, pd.no_ppsk, ISNULL(pd.jml_diminta, 0) jml_diminta
          , isnull(mp.NAMA_PEGAWAI, '') user_penerima, pd.tgl_terima, DATEDIFF(day,ks.TGL_DOC_IN,'{$tglKebutuhan}') umur
        from kandang_siklus ks
        join M_KANDANG mk on ks.KODE_FARM = mk.KODE_FARM and ks.KODE_KANDANG = mk.KODE_KANDANG
        join ppsk_d pd on ks.NO_REG = pd.no_reg
        join ppsk_new ph on pd.no_ppsk = ph.no_ppsk
        left join m_pegawai mp on pd.user_penerima = mp.KODE_PEGAWAI
        where pd.no_ppsk like '{$no_ppsk}'
QUERY;
//cetak_r($sql);
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
  }

  public function getDaftarPermintaanNew($kodeFarm = '', $tglKebutuhan = null)
  {

        $sql = <<<QUERY
          select ks.no_reg, mk.NAMA_KANDANG, 0 jml_diminta, ks.tgl_doc_in, DATEDIFF(day,ks.TGL_DOC_IN,'{$tglKebutuhan}') umur
          from KANDANG_SIKLUS ks
          join M_KANDANG mk on ks.KODE_FARM = mk.KODE_FARM and ks.KODE_KANDANG = mk.KODE_KANDANG
          where ks.STATUS_SIKLUS = 'O' and ks.KODE_FARM = '{$kodeFarm}' and TGL_DOC_IN <= '{$tglKebutuhan}'
QUERY;


        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
  }

  public function getSakTerpakai($kode_farm = '',$periode_siklus = ''){
    $sql = <<<QUERY
        SELECT SUM(JML_SAK+JML_OVER) FROM PPSK WHERE NO_PPSK LIKE '%$kode_farm/$periode_siklus%'
QUERY;

    $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(0)[0];
  }
  function doCek_status_reject_home($user_level,$kode_farm) {
    $result['message'] = '';
    $str = '';
    switch ($this->switch_user[$user_level]){
      case 'KFM':
         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
         case
            when pp.STATUS = 'RJ' then 'Reject'
            when pp.STATUS = 'N' then 'Rilis'
            when pp.STATUS = 'R' then 'Review'
            when pp.STATUS = 'A' then 'Approve'
         end STATUS_PPSK
         FROM PPSK pp
         LEFT JOIN log_ppsk_new lpp ON lpp.NO_PPSK = pp.NO_PPSK
         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
         WHERE pp.STATUS = 'RJ'
         ORDER BY lpp.NO_URUT DESC";
      break;
      case 'KDP':
         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
         case
            when pp.STATUS = 'RJ' then 'Reject'
            when pp.STATUS = 'N' then 'Rilis'
            when pp.STATUS = 'R' then 'Review'
            when pp.STATUS = 'A' then 'Approve'
         end STATUS_PPSK
         FROM PPSK pp
         LEFT JOIN log_ppsk_new lpp ON lpp.NO_PPSK = pp.NO_PPSK
         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
         WHERE (pp.STATUS = 'RJ' AND mp.GRUP_PEGAWAI in ('WKDV','KDV'))
         OR (pp.STATUS = 'N' AND mp.GRUP_PEGAWAI in ('KFM'))
         ORDER BY lpp.NO_URUT DESC";
      break;
      case 'WKDP':
         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
         case
            when pp.STATUS = 'RJ' then 'Reject'
            when pp.STATUS = 'N' then 'Rilis'
            when pp.STATUS = 'R' then 'Review'
            when pp.STATUS = 'A' then 'Approve'
         end STATUS_PPSK
         FROM PPSK pp
         LEFT JOIN log_ppsk_new lpp ON lpp.NO_PPSK = pp.NO_PPSK
         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
         WHERE (pp.STATUS = 'RJ' AND mp.GRUP_PEGAWAI in ('WKDV','KDV'))
         OR (pp.STATUS = 'N' AND mp.GRUP_PEGAWAI in ('KFM'))
         ORDER BY lpp.NO_URUT DESC";
      break;
//      case 'WKDV':
//         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
//         case
//            when pp.STATUS = 'RJ' then 'Reject'
//            when pp.STATUS = 'N' then 'Rilis'
//            when pp.STATUS = 'R' then 'Review'
//            when pp.STATUS = 'A' then 'Approve'
//         end STATUS_PPSK
//         FROM PPSK pp
//         LEFT JOIN log_ppsk_new lpp ON lpp.NO_PPSK = pp.NO_PPSK
//         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
//         WHERE (pp.STATUS = 'RJ' AND mp.GRUP_PEGAWAI in ('KDV'))
//         OR (pp.STATUS = 'R' AND mp.GRUP_PEGAWAI in ('KDP','WKDP'))
//         ORDER BY lpp.NO_URUT DESC";
//      break;

      case 'WKDV':
         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
         case
            when pp.STATUS = 'RJ' then 'Reject'
            when pp.STATUS = 'N' then 'Rilis'
            when pp.STATUS = 'R' then 'Review'
            when pp.STATUS = 'A' then 'Approve'
         end STATUS_PPSK
         FROM PPSK pp
         LEFT JOIN log_ppsk_new lpp ON lpp.NO_PPSK = pp.NO_PPSK
         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
         WHERE pp.STATUS = 'RJ' AND mp.GRUP_PEGAWAI in ('KDV')
         ORDER BY lpp.NO_URUT DESC";
      break;
      case 'WKBA':
         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
         case
            when pp.STATUS = 'RJ' then 'Reject'
            when pp.STATUS = 'N' then 'Rilis'
            when pp.STATUS = 'R' then 'Review'
            when pp.STATUS = 'A' then 'Approve'
         end STATUS_PPSK
         FROM PPSK pp
         LEFT JOIN log_ppsk_new lpp ON lpp.NO_PPSK = pp.NO_PPSK
         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
         WHERE (pp.STATUS = 'RJ' AND mp.GRUP_PEGAWAI in ('KDV'))
         OR (pp.STATUS = 'R' AND mp.GRUP_PEGAWAI in ('KDP','WKDP'))
         ORDER BY lpp.NO_URUT DESC";
      break;

    }


     $sql = <<<QUERY
      $str
QUERY;

     $stmt = $this->dbSqlServer->conn_id->prepare($sql);
     $stmt->execute();
     $hasil = $stmt->fetch(0);
     if($hasil != false){
       $result['message'] = "Pengajuan permintaan sak kosong, <br>
       <b>No. Permintaan : ".$hasil['NO_PPSK']."</b> di-".$hasil['STATUS_PPSK']." <br>
       <b>Oleh: </b>".$hasil['NAMA_PEGAWAI']."<br>";
       if($hasil['STATUS'] == 'RJ'){
           $result['message'] .= "<b>Keterangan: </b>".$hasil['KETERANGAN']."";
       }
     }



    return json_encode($result);
  }
  public function getRfidKandang($rfid){
      $sql = <<<QUERY
        select * from m_kandang where kode_verifikasi = '$rfid'
QUERY;
//cetak_r($sql);
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
  }
  public function no_ppsk($no_ppsk){
     /* dapatkan no_urut berdasarkan no_reg */
        $tmp = $this->db->order_by('no_ppsk','desc')->where('no_ppsk like substring(\''.$no_ppsk.'\', 1, 14)+\'%\'')->get('ppsk_new');
        //cetak_r($tmp);
        $tmp = $tmp->row(0);
        //print_r($tmp);

        if(count($tmp) > 0){
           $no_urut_ppsk = (int)substr($tmp->no_ppsk,-3);
        }
        else{
           $no_urut_ppsk = 0;
        }
        $no_urut_ppsk++;
        $no_ppsk = substr($no_ppsk, 0, 15);
        //cetak_r($no_ppsk);

        $no_urut_ppsk = str_pad($no_urut_ppsk,3,'0',STR_PAD_LEFT);

        return $no_ppsk.$no_urut_ppsk;
  }
  public function no_urut_log_ppsk($no_ppsk){
     /* dapatkan no_urut berdasarkan no_reg */
        $tmp = $this->db->order_by('no_urut','desc')->where(array('no_ppsk'=>$no_ppsk))->get('log_ppsk_new');
        $tmp = $tmp->row(0);

        if(count($tmp) > 0){
           $no_urut = $tmp->no_urut;
        }
        else{
           $no_urut = 0;
        }
        $no_urut++;
        return $no_urut;
  }
  public function getListFarm($kode_pegawai = ''){
     $this->db->join('M_FARM mf','mf.KODE_FARM = pd.KODE_FARM','left');
     $this->db->where('pd.KODE_PEGAWAI',$kode_pegawai);
     return $this->db->get('PEGAWAI_D pd')->result();
  }
  public function getKodeFarm($no_ppsk = ''){
     $result = $this->db->query("
         SELECT SUBSTRING(NO_PPSK,
          CHARINDEX('/', NO_PPSK)+1,
          Charindex('/', Substring(NO_PPSK, Charindex('/', NO_PPSK)+1, LEN(NO_PPSK)))-1
        ) NO_PPSK
         FROM PPSK WHERE NO_PPSK = '$no_ppsk'
     ");
     $result = $result->row(0);

     return $result->NO_PPSK;
  }
  public function getKategori(){
      $this->db->where('STATUS','A');
      $this->db->order_by('KATEGORI_BUDGET','desc');
      $sql = $this->db->get('M_BUDGET_PEMAKAIAN_GLANGSING');
      return $sql->result();
  }
}
