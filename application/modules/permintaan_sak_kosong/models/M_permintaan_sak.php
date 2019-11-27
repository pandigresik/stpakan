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
  public function sakTersedia($kodefarm){
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
            on ks.NO_REG = rsk.NO_REG and ks.kode_farm = '{$kodefarm}'
                      inner join M_PERIODE mp
                              on mp.kode_siklus = ks.kode_siklus and mp.kode_farm = ks.kode_farm and mp.status_periode = 'A'
          group by mp.periode_siklus,mp.kode_farm
    )sakKembali
    left join (
      select left(no_ppsk,15) prefix_ppsk,sum(jml_sak) total_sak from PPSK where status != 'V'
      group by left(no_ppsk,15)
    )sakDiminta on sakDiminta.prefix_ppsk = sakKembali.prefix_ppsk
SQL;
    return $this->db->query($sql);
  }

  public function listPermintaan($kodefarm,$periode_siklus,$status,$order_table){
/*    ,(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D') TGL_BUAT    -- untuk SQLSERVER
    ,(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'A') TGL_APPROVE -- untuk SQLSERVER
    ,(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'R') TGL_ACK -- untuk SQLSERVER
    ,(select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N') TGL_RILIS -- untuk SQLSERVER
  --    ,(select tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D' limit 1) TGL_BUAT -- untuk Mysql
  --    ,(select tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'A' limit 1) TGL_APPROVE -- untuk Mysql*/
//    $sql = <<<SQL
//       select pp.* ,
//       COALESCE((select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D' ORDER BY NO_URUT DESC), (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC)) TGL_BUAT ,
//       (select top 1 tgl_buat from LOG_PPSK
//         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'WKDV'
//         where no_ppsk = pp.no_ppsk and status in('A','RJ') ORDER BY NO_URUT DESC
//       ) TGL_APPROVE,
//       (select top 1 tgl_buat from LOG_PPSK
//         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
//         where no_ppsk = pp.no_ppsk and status in('A','RJ') ORDER BY NO_URUT DESC
//       ) TGL_APPROVE_KADIV,
//       (select top 1 tgl_buat from LOG_PPSK
//         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDP'
//         where no_ppsk = pp.no_ppsk and status in('R','RJ') ORDER BY NO_URUT DESC) TGL_ACK ,
//       (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC) TGL_RILIS ,
//       mp.NAMA_PEGAWAI ,
//       case
//         when pp.STATUS = 'D' then 'Draft'
//         when pp.STATUS = 'N' then 'New (Rilis)'
//         when pp.STATUS = 'R' then 'Review'
//         when pp.STATUS = 'RJ' then 'Rejected'
//         when pp.STATUS = 'A' then 'Approved'
//         when pp.STATUS = 'V' then 'Void'
//       end STATUS_DESC ,
//       case
//         when pp.STATUS = 'D' then {$order_table['D']}
//         when pp.STATUS = 'N' then {$order_table['N']}
//         when pp.STATUS = 'R' then {$order_table['R']}
//         when pp.STATUS = 'RJ' then {$order_table['RJ']}
//         when pp.STATUS = 'A' then {$order_table['A']}
//         when pp.STATUS = 'V' then {$order_table['V']}
//       end urutan ,COALESCE(mbpg.NAMA_BUDGET,'Penjualan Sak '+'(DO: '+pp.NO_DO+')') KETERANGAN ,
//       COALESCE(mp.NAMA_PEGAWAI,UPPER(pp.USER_PEMINTA)) NAMA_PEGAWAI,
//       (select top 1 keterangan from LOG_PPSK where no_ppsk = pp.no_ppsk and status != 'RJ' ORDER BY NO_URUT DESC) ALASAN_OVER
//     from PPSK pp
//     left join M_PEGAWAI mp on mp.KODE_PEGAWAI = pp.USER_PEMINTA
//     left join M_BUDGET_PEMAKAIAN_GLANGSING mbpg on mbpg.kode_budget = pp.kode_budget
//     where pp.no_ppsk like '%{$kodefarm}/{$periode_siklus}/%'
//     and pp.status in($status) order by urutan asc,no_ppsk desc
// SQL;
   $str = "'".implode("','" , $this->akses_reject[$this->switch_user[$this->_user_level]])."'";
   $sql = <<<SQL
         --IF OBJECT_ID ('tempdb..#tmp_logppsk') IS NOT NULL
       --DROP TABLE #tmp_logppsk

       --SELECT NO_PPSK,max(tgl_buat) max_tgl_buat INTO #tmp_logppsk FROM LOG_PPSK GROUP BY NO_PPSK
       select mp.GRUP_PEGAWAI,pp.* ,
       COALESCE(
          (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D' ORDER BY NO_URUT DESC),
       (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC)
       ) TGL_BUAT ,
       (
          select top 1 tgl_buat from LOG_PPSK
         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI in ('WKDV','WKBA')
         where no_ppsk = pp.no_ppsk and status in('A','RJ') ORDER BY NO_URUT DESC
       ) TGL_APPROVE,
       (
          select top 1 tgl_buat from LOG_PPSK
         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
         where no_ppsk = pp.no_ppsk and status in('A','RJ') ORDER BY NO_URUT DESC
       ) TGL_APPROVE_KADIV,
       (
          select top 1 tgl_buat from LOG_PPSK
         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDP'
         where no_ppsk = pp.no_ppsk and status in('R','RJ') ORDER BY NO_URUT DESC
       ) TGL_ACK ,
       (
          select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC
       ) TGL_RILIS ,
       case
         when pp.STATUS = 'D' then 'Draft'
         when pp.STATUS = 'N' then 'New (Rilis)'
         when pp.STATUS = 'R' then 'Review'
         when pp.STATUS = 'RJ' then 'Rejected'
         when pp.STATUS = 'A' then 'Approved'
         when pp.STATUS = 'V' then 'Void'
       end STATUS_DESC ,
       case
       when pp.STATUS = 'D' then {$order_table['D']}
       when pp.STATUS = 'N' then {$order_table['N']}
       when pp.STATUS = 'R' then {$order_table['R']}
       when pp.STATUS = 'RJ' then {$order_table['RJ']}
       when pp.STATUS = 'A' then {$order_table['A']}
       when pp.STATUS = 'V' then {$order_table['V']}
       end urutan ,
       COALESCE(mbpg.NAMA_BUDGET,'Penjualan Sak '+'(DO: '+pp.NO_DO+')') KETERANGAN ,
       COALESCE(mp2.NAMA_PEGAWAI,UPPER(pp.USER_PEMINTA)) NAMA_PEGAWAI,
       (
          select top 1 keterangan from LOG_PPSK where no_ppsk = pp.no_ppsk and status in ('D','N') ORDER BY NO_URUT DESC
       ) ALASAN_OVER,
       '$kodefarm' KODE_FARM
       from PPSK pp

       left join M_BUDGET_PEMAKAIAN_GLANGSING mbpg on mbpg.kode_budget = pp.kode_budget
       JOIN log_ppsk lpp on lpp.NO_PPSK = pp.NO_PPSK
       JOIN (
         SELECT NO_PPSK,max(NO_URUT) NO_URUT FROM LOG_PPSK GROUP BY NO_PPSK
         ) tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.NO_URUT = lpp.NO_URUT
         INNER JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
        LEFT JOIN M_PEGAWAI mp2 ON mp2.KODE_PEGAWAI = pp.USER_PEMINTA
       where pp.no_ppsk like '%{$kodefarm}/{$periode_siklus}/%'
       and pp.status in($status)

       union all


       SELECT mp.GRUP_PEGAWAI,pp.*,
       COALESCE(
          (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'D' ORDER BY NO_URUT DESC),
       (select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC)
       ) TGL_BUAT ,
       (
          select top 1 tgl_buat from LOG_PPSK
         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI in ('WKDV','WKBA')
         where no_ppsk = pp.no_ppsk and status in('A','RJ') ORDER BY NO_URUT DESC
       ) TGL_APPROVE,
       (
          select top 1 tgl_buat from LOG_PPSK
         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDV'
         where no_ppsk = pp.no_ppsk and status in('A','RJ') ORDER BY NO_URUT DESC
       ) TGL_APPROVE_KADIV,
       (
          select top 1 tgl_buat from LOG_PPSK
         JOIN M_PEGAWAI ON M_PEGAWAI.KODE_PEGAWAI = LOG_PPSK.USER_BUAT AND M_PEGAWAI.GRUP_PEGAWAI = 'KDP'
         where no_ppsk = pp.no_ppsk and status in('R','RJ') ORDER BY NO_URUT DESC
       ) TGL_ACK ,
       (
          select top 1 tgl_buat from LOG_PPSK where no_ppsk = pp.no_ppsk and status = 'N' ORDER BY NO_URUT DESC
       ) TGL_RILIS ,
       case
         when pp.STATUS = 'D' then 'Draft'
         when pp.STATUS = 'N' then 'New (Rilis)'
         when pp.STATUS = 'R' then 'Review'
         when pp.STATUS = 'RJ' then 'Rejected'
         when pp.STATUS = 'A' then 'Approved'
         when pp.STATUS = 'V' then 'Void'
       end STATUS_DESC ,
       case
         when pp.STATUS = 'D' then {$order_table['D']}
         when pp.STATUS = 'N' then {$order_table['N']}
         when pp.STATUS = 'R' then {$order_table['R']}
         when pp.STATUS = 'RJ' then {$order_table['RJ']}
         when pp.STATUS = 'A' then {$order_table['A']}
         when pp.STATUS = 'V' then {$order_table['V']}
       end urutan ,
       COALESCE(mbpg.NAMA_BUDGET,'Penjualan Sak '+'(DO: '+pp.NO_DO+')') KETERANGAN ,
       COALESCE(mp2.NAMA_PEGAWAI,UPPER(pp.USER_PEMINTA)) NAMA_PEGAWAI,
       (
          select top 1 keterangan from LOG_PPSK where no_ppsk = pp.no_ppsk and status in ('D','N') ORDER BY NO_URUT DESC
       ) ALASAN_OVER,
       '$kodefarm' KODE_FARM
       FROM PPSK pp
       JOIN log_ppsk lpp on lpp.NO_PPSK = pp.NO_PPSK
       --JOIN #tmp_logppsk tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.max_tgl_buat = lpp.TGL_BUAT
       JOIN (
         SELECT NO_PPSK,max(NO_URUT) NO_URUT FROM LOG_PPSK GROUP BY NO_PPSK
         ) tlp ON tlp.NO_PPSK = lpp.NO_PPSK AND tlp.NO_URUT = lpp.NO_URUT
       INNER JOIN M_PEGAWAI mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
       LEFT JOIN M_PEGAWAI mp2 ON mp2.KODE_PEGAWAI = pp.USER_PEMINTA
       LEFT JOIN M_BUDGET_PEMAKAIAN_GLANGSING mbpg ON mbpg.kode_budget = pp.kode_budget
       WHERE mp.GRUP_PEGAWAI IN($str)
       AND pp.STATUS = 'RJ' AND pp.no_ppsk like '%{$kodefarm}/{$periode_siklus}/%'
       order by urutan asc,no_ppsk desc
SQL;
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
FROM LOG_PPSK lp
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
       and MP.KODE_FARM = '$kode_farm' AND MP.KODE_SIKLUS = (
         select top 1 ks.kode_siklus from kandang_siklus ks where ks.status_siklus = 'O' and mp.kode_farm = ks.kode_farm
       ) AND BG.STATUS = 'A'
       AND MBPG.KATEGORI_BUDGET = '$kategori_budget'
       $str
QUERY;

    $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(0)[0];
  }

  public function getBudgetTerpakai($prefix_ppsk = '',$kategori_budget = '',$kode_budget = ''){
    $sql = <<<QUERY
        select sum(ppsk.JML_SAK) from ppsk
        join M_BUDGET_PEMAKAIAN_GLANGSING mbpg on mbpg.KODE_BUDGET = ppsk.KODE_BUDGET and mbpg.KATEGORI_BUDGET like '%$kategori_budget%'
        where ppsk.NO_PPSK LIKE '$prefix_ppsk%' and ppsk.kode_budget like '%$kode_budget%'
        and ppsk.STATUS != 'V'
QUERY;

    $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(0)[0];
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
         LEFT JOIN log_ppsk lpp ON lpp.NO_PPSK = pp.NO_PPSK
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
         LEFT JOIN log_ppsk lpp ON lpp.NO_PPSK = pp.NO_PPSK
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
         LEFT JOIN log_ppsk lpp ON lpp.NO_PPSK = pp.NO_PPSK
         LEFT JOIN m_pegawai mp ON mp.KODE_PEGAWAI = lpp.USER_BUAT
         WHERE (pp.STATUS = 'RJ' AND mp.GRUP_PEGAWAI in ('WKDV','KDV'))
         OR (pp.STATUS = 'N' AND mp.GRUP_PEGAWAI in ('KFM'))
         ORDER BY lpp.NO_URUT DESC";
      break;
      case 'WKDV':
         $str = "SELECT TOP 1 pp.NO_PPSK,mp.NAMA_PEGAWAI,lpp.KETERANGAN,pp.STATUS,
         case
            when pp.STATUS = 'RJ' then 'Reject'
            when pp.STATUS = 'N' then 'Rilis'
            when pp.STATUS = 'R' then 'Review'
            when pp.STATUS = 'A' then 'Approve'
         end STATUS_PPSK
         FROM PPSK pp
         LEFT JOIN log_ppsk lpp ON lpp.NO_PPSK = pp.NO_PPSK
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
         LEFT JOIN log_ppsk lpp ON lpp.NO_PPSK = pp.NO_PPSK
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
  public function no_ppsk($no_ppsk){
     /* dapatkan no_urut berdasarkan no_reg */
        $tmp = $this->db->order_by('no_ppsk','desc')->get('ppsk where no_ppsk like \''.$no_ppsk.'%\'');
        $tmp = $tmp->row(0);

        if(count($tmp) > 0){
           $no_urut_ppsk = (int)substr($tmp->NO_PPSK,-3);
        }
        else{
           $no_urut_ppsk = 0;
        }
        $no_urut_ppsk++;
        $no_urut_ppsk = str_pad($no_urut_ppsk,3,'0',STR_PAD_LEFT);

        return $no_ppsk.$no_urut_ppsk;
  }
  public function no_urut_log_ppsk($no_ppsk){
     /* dapatkan no_urut berdasarkan no_reg */
        $tmp = $this->db->order_by('no_urut','desc')->where(array('no_ppsk'=>$no_ppsk))->get('log_ppsk');
        $tmp = $tmp->row(0);

        if(count($tmp) > 0){
           $no_urut = $tmp->NO_URUT;
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
}
