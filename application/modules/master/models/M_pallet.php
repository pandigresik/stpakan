<?php

class M_pallet extends CI_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database("default", true);
    }

    function get_data_pallet($kode_farm, $id_pallet = null, $tanggal_penimbangan = null, $pallet_aktif = null, $pallet_tidak_aktif = null) {
        $filter_str = "";
        $filter_arr = array();

        $filter_bottom_str = "";
        $filter_bottom_arr = array();
        $filter_arr [] = "mp.KODE_FARM = '".$kode_farm."'";
        #$filter_arr [] = "mp.STATUS = 'N'";
        if (isset($id_pallet))
            $filter_arr [] = "mp.KODE_PALLET like '%" . $id_pallet . "%'";
        if (isset($tanggal_penimbangan))
            $filter_arr [] = "cast(mp.TGL_TIMBANG as date) = '".$tanggal_penimbangan."'";

    /*
        if ($pallet_aktif == 1 && $pallet_tidak_aktif == 0)
            $filter_arr [] = "mp.STATUS_PALLET = 'N'";
        else if ($pallet_aktif == 0 && $pallet_tidak_aktif == 1)
            $filter_arr [] = "mp.STATUS_PALLET = 'C'";
        else if ($pallet_aktif == 1 && $pallet_tidak_aktif == 1)
            $filter_arr [] = "(mp.STATUS_PALLET = 'N' or mp.STATUS_PALLET = 'C')";
        else
            $filter_arr [] = "";
 */
        $status_pallet = '';
        if($pallet_aktif){
          $status_pallet = ' where status_pallet = \'N\'';
        }
        if($pallet_tidak_aktif){
          $status_pallet = ' where status_pallet = \'C\'';
        }
        if($pallet_aktif && $pallet_tidak_aktif){
          $status_pallet = '';
        }
        if (count($filter_arr) > 0) {
            $filter_str .= " where ";
            $filter_str .= implode(" and ", $filter_arr);
        }

        if (isset($start) and isset($offset))
            $filter_bottom_arr [] = "row > {$start} and row <= {$offset}";

        if (count($filter_bottom_arr) > 0) {
            $filter_bottom_str .= " where ";
            $filter_bottom_str .= implode(" and ", $filter_bottom_arr);
        }

        $sql = <<<QUERY
			select * from (
                select
                --    ROW_NUMBER() OVER (ORDER BY mp.KODE_PALLET) AS ROW
                     mp.*
                    , case
                        when mp.STATUS_PALLET = 'N' then 'Aktif'
                        else 'Tidak Aktif'
                    end STATUS_LABEL
                    , cast(mp.brt_bersih as decimal(20,1)) BRT_BERSIH_NEW
                from M_PALLET mp
                join (
        					select max(tgl_timbang) tgl_timbang,kode_pallet,kode_farm from M_PALLET
        					group by kode_farm,kode_pallet
        				)ls on ls.KODE_PALLET = mp.kode_pallet
					          and ls.kode_farm = mp.kode_farm and ls.tgl_timbang = mp.tgl_timbang
				$filter_str
			) mainqry
        {$status_pallet}
            order by BRT_BERSIH
                ,STATUS_PALLET DESC
                , KODE_PALLET ASC
QUERY;
        #echo $sql;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpan_berat_pallet($kode_farm, $data){
        $result = 0;
        $kode_pallet = $data['id_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        $tgl_timbang = $data['tgl_timbang'];
        if(!empty($tgl_timbang)){
            $data_result = $this->check_pallet($kode_farm, $kode_pallet, $tgl_timbang);
            if(count($data_result)>0){
                $result = 2;
            }
            else{
                $update_result = $this->update_pallet($kode_farm, $data, $tgl_timbang);
                if($update_result['status_pallet'] == 'C'){
                    $insert_result = $this->insert_pallet($kode_farm, $data, $tgl_timbang);
                    if(!empty($insert_result['kode_pallet'])){
                        $result = 1;
                    }
                }
            }
        }
        else{
            $update_result = $this->update_pallet($kode_farm, $data);
            if(!empty($update_result['berat'])){
                $result = 1;
            }
        }
        return $result;
    }

    public function update_pallet($kode_farm, $data, $tanggal = NULL){
        $result = 0;
        $kode_pallet = $data['id_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        if(empty($tanggal)){
        $query = <<<QUERY
            update M_PALLET
            set BRT_BERSIH = $tara
                , keterangan = '$keterangan'
                , TGL_TIMBANG = getdate()
            output inserted.brt_bersih berat
                , inserted.keterangan
            where KODE_FARM = '$kode_farm'
            and KODE_PALLET = '$kode_pallet'
QUERY;
        }
        else{
        $query = <<<QUERY
            update M_PALLET
            set status_pallet = 'C'
            output inserted.status_pallet
            where KODE_FARM = '$kode_farm'
            and KODE_PALLET = '$kode_pallet'
            and cast(tgl_timbang as date) = '$tanggal'
QUERY;
        }
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_pallet($kode_farm, $data, $tanggal){
        $result = 0;
        $kode_pallet = $data['id_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        $query = <<<QUERY
            insert into M_PALLET (
                kode_farm
                , kode_gudang
                , no_kavling
                , kode_pallet
                , tgl_timbang
                , brt_bersih
                , keterangan
                , status_pallet
            )
            output inserted.kode_pallet
            select
                kode_farm
                , kode_gudang
                , no_kavling
                , kode_pallet
                , getdate()
                , $tara
                , '$keterangan'
                , 'N'
            from m_pallet
            where KODE_FARM = '$kode_farm'
            and KODE_PALLET = '$kode_pallet'
            and cast(tgl_timbang as date) = '$tanggal'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_data_berat_pallet($level_user, $kode_farm) {
        $level_user = strtolower($level_user);
        switch ($level_user) {
            case 'ag':
                $query = <<<QUERY

                    select
                        *
                    from M_PALLET
                    where KODE_FARM = '$kode_farm'
                    and berat is null
QUERY;
                break;
        }

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function data_berat_pallet($level_user, $kode_farm){
        $data_berat_pallet = $this->get_data_berat_pallet($level_user, $kode_farm);
        $result = array(
            'title' => 'Master Pallet',
            'message' => ''
        );

        if(count($data_berat_pallet)>0){
            $result['message'] = 'Siklus kandang telah dilakukan aktivasi. Harap melakukan penimbangan pallet.';
        }


        return $result;
    }

    public function check_pallet($kode_farm, $kode_pallet, $tgl_timbang) {
        $query = <<<QUERY

            select
                *
            from M_PALLET
            where KODE_FARM = '$kode_farm'
            and KODE_PALLET = '$kode_pallet'
            and cast(TGL_TIMBANG as date) = '$tgl_timbang'
            and cast(TGL_TIMBANG as date) = cast(getdate() as date)
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function history_pallet($kode_farm, $kode_pallet) {
        $query = <<<QUERY

            select
                mp.*
                , case
                    when mp.STATUS_PALLET = 'N' then 'Aktif'
                    else 'Tidak Aktif'
                end STATUS_LABEL
                , cast(mp.brt_bersih as decimal(20,1)) BRT_BERSIH
            from m_pallet mp
            where mp.KODE_FARM = '$kode_farm'
            and mp.KODE_PALLET = '$kode_pallet'
            order by mp.TGL_TIMBANG desc
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ubah_status_pallet($kode_farm, $kode_pallet, $status_pallet, $keterangan, $tanggal_penimbangan) {
        $query = <<<QUERY
            update m_pallet
            set status_pallet = '$status_pallet'
                , keterangan = '$keterangan'
            output inserted.status_pallet
            where kode_farm = '$kode_farm'
            and kode_pallet = '$kode_pallet'
            and cast(tgl_timbang as date) = '$tanggal_penimbangan'
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function check_stok($kode_farm,$idpallet){
      $sql = <<<SQL

        select top 1
            CASE
            WHEN (SELECT min(no_pallet) FROM MOVEMENT_D WHERE KETERANGAN1 = 'PUT' AND KETERANGAN2 IN (SELECT no_reg FROM KANDANG_SIKLUS WHERE status_siklus = 'O' AND KODE_FARM = '{$kode_farm}')) IS NULL THEN 0
            else JML_ON_HAND END  stok
            from MOVEMENT where KODE_PALLET = '{$idpallet}' and kode_farm ='{$kode_farm}'
        order by NO_PALLET desc
      -- select top 1 JML_ON_HAND stok from MOVEMENT where KODE_PALLET = '{$idpallet}' and kode_farm ='{$kode_farm}' order by NO_PALLET desc
SQL;
    return $this->db->query($sql);
    }
}
