<?php

class M_hand_pallet extends MY_Model {

    private $dbSqlServer;
    protected $_table;
    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database("default", true);
        $this->_table = 'm_hand_pallet';
    }

    function get_data_hand_pallet($kode_farm, $id_hand_pallet = null, $tanggal_penimbangan = null, $hand_pallet_aktif = null, $hand_pallet_tidak_aktif = null) {
        $filter_str = "";
        $filter_arr = array();

        $filter_bottom_str = "";
        $filter_bottom_arr = array();
        $filter_arr [] = "mp.KODE_FARM = '".$kode_farm."'";
        #$filter_arr [] = "mp.STATUS = 'N'";
        if (isset($id_hand_pallet))
            $filter_arr [] = "mp.KODE_hand_pallet like '%" . $id_hand_pallet . "%'";
        if (isset($tanggal_penimbangan))
            $filter_arr [] = "cast(mp.TGL_TIMBANG as date) = '".$tanggal_penimbangan."'";

    /*
        if ($hand_pallet_aktif == 1 && $hand_pallet_tidak_aktif == 0)
            $filter_arr [] = "mp.STATUS_PALLET = 'N'";
        else if ($hand_pallet_aktif == 0 && $hand_pallet_tidak_aktif == 1)
            $filter_arr [] = "mp.STATUS_PALLET = 'C'";
        else if ($hand_pallet_aktif == 1 && $hand_pallet_tidak_aktif == 1)
            $filter_arr [] = "(mp.STATUS_PALLET = 'N' or mp.STATUS_PALLET = 'C')";
        else
            $filter_arr [] = "";
  */
        $status_pallet = '';
        if($hand_pallet_aktif){
          $status_pallet = ' where status_pallet = \'N\'';
        }
        if($hand_pallet_tidak_aktif){
          $status_pallet = ' where status_pallet = \'C\'';
        }
        if($hand_pallet_aktif && $hand_pallet_tidak_aktif){
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
                    ROW_NUMBER() OVER (ORDER BY mp.KODE_hand_pallet) AS ROW
                    , mp.*
                    , case
                        when mp.STATUS_PALLET = 'N' then 'Aktif'
                        else 'Tidak Aktif'
                    end STATUS_LABEL
                    , cast(mp.brt_bersih as decimal(20,1)) BRT_BERSIH_NEW
                from M_hand_pallet mp
                join (
                    select max(tgl_timbang) tgl_timbang,kode_hand_pallet,kode_farm from m_hand_pallet
                    group by kode_farm,kode_hand_pallet
                  )ls on ls.kode_hand_pallet = mp.kode_hand_pallet
                    and ls.kode_farm = mp.kode_farm and ls.tgl_timbang = mp.tgl_timbang
				$filter_str
			) mainqry
          {$status_pallet}
            order by STATUS_PALLET DESC
                , KODE_hand_pallet ASC
QUERY;
        #echo $sql;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simpan_berat_hand_pallet($kode_farm, $data){

        $this->dbSqlServer->conn_id->beginTransaction();
        $result = 0;
        $kode_hand_pallet = $data['id_hand_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        $tgl_timbang = $data['tgl_timbang'];
        #print_r($data);
        if(!empty($tgl_timbang)){
            $data_result = $this->check_hand_pallet($kode_farm, $kode_hand_pallet, $tgl_timbang);
            if(count($data_result) > 0){
                $result = 2;
            }
            else{
                #$data_hp = $this->check_hand_pallet($kode_farm, $kode_hand_pallet);
                #if(count($data_hp) > 0){
                    $update_result = $this->update_hand_pallet($kode_farm, $data, $tgl_timbang);
                    if($update_result['STATUS_PALLET'] == 'C'){
                        $insert_result = $this->insert_hand_pallet($kode_farm, $data, $tgl_timbang);
                        if(!empty($insert_result['kode_hand_pallet'])){
                            $result = 1;
                        }
                    }
                #}
                #else{
                #    $insert_result = $this->insert_hand_pallet_baru($kode_farm, $data, $tgl_timbang);
                #    if(!empty($insert_result['kode_hand_pallet'])){
                #        $result = 1;
                #    }
                #}
            }
        }
        else{
            $data_hp = $this->check_hand_pallet($kode_farm, $kode_hand_pallet);
            if(count($data_hp) > 0){
                $update_result = $this->update_hand_pallet($kode_farm, $data);
                if(!empty($update_result['berat'])){
                    $result = 1;
                }
            }
            else{
                $insert_result = $this->insert_hand_pallet_baru($kode_farm, $data);
                if(!empty($insert_result['kode_hand_pallet'])){
                    $result = 1;
                }
            }
        }
        if($result == 1){

            $this->dbSqlServer->conn_id->commit();
        }
        else{
            $this->dbSqlServer->conn_id->rollback();

        }
        return $result;
    }

    public function update_hand_pallet($kode_farm, $data, $tanggal = NULL){
        $result = 0;
        $kode_hand_pallet = $data['id_hand_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        if(empty($tanggal)){
        $query = <<<QUERY
            update M_hand_pallet
            set BRT_BERSIH = $tara
                , keterangan = '$keterangan'
                , TGL_TIMBANG = getdate()
            output inserted.brt_bersih berat
                , inserted.keterangan
            where KODE_FARM = '$kode_farm'
            and KODE_hand_pallet = '$kode_hand_pallet'
QUERY;
        }
        else{
        $query = <<<QUERY
            update M_hand_pallet
            set STATUS_PALLET = 'C'
            output inserted.STATUS_PALLET
            where KODE_FARM = '$kode_farm'
            and KODE_hand_pallet = '$kode_hand_pallet'
            and cast(tgl_timbang as date) = '$tanggal'
QUERY;
        }
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_hand_pallet($kode_farm, $data, $tanggal){
        $result = 0;
        $kode_hand_pallet = $data['id_hand_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        $default = ($data['_default'] == 'true') ? 1 : 0;
        if($default == 1){
            $this->ubah_all_status_hand_pallet($kode_farm);
        }
        $query = <<<QUERY
            insert into M_hand_pallet (
                kode_farm
                , kode_hand_pallet
                , tgl_timbang
                , brt_bersih
                , keterangan
                , STATUS_PALLET
                , _DEFAULT
            )
            output inserted.kode_hand_pallet
            select
                kode_farm
                , kode_hand_pallet
                , getdate()
                , $tara
                , '$keterangan'
                , 'N'
                , $default
            from m_hand_pallet
            where KODE_FARM = '$kode_farm'
            and KODE_hand_pallet = '$kode_hand_pallet'
            and cast(tgl_timbang as date) = '$tanggal'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_hand_pallet_baru($kode_farm, $data){
        $result = 0;
        $kode_hand_pallet = $data['id_hand_pallet'];
        $tara = $data['tara'];
        $keterangan = $data['keterangan'];
        $default = ($data['_default'] == 'true') ? 1 : 0;
        if($default == 1){
            $this->ubah_all_status_hand_pallet($kode_farm);
        }
        $query = <<<QUERY
            insert into M_hand_pallet (
                kode_farm
                , kode_hand_pallet
                , tgl_timbang
                , brt_bersih
                , keterangan
                , STATUS_PALLET
                , _DEFAULT
            )
            output inserted.kode_hand_pallet
            values(
                '$kode_farm'
                , '$kode_hand_pallet'
                , getdate()
                , $tara
                , '$keterangan'
                , 'N'
                , $default
            )
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_data_berat_hand_pallet($level_user, $kode_farm) {
        $level_user = strtolower($level_user);
        switch ($level_user) {
            case 'ag':
                $query = <<<QUERY

                    select
                        *
                    from M_hand_pallet
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

    public function data_berat_hand_pallet($level_user, $kode_farm){
        $data_berat_hand_pallet = $this->get_data_berat_hand_pallet($level_user, $kode_farm);
        $result = array(
            'title' => 'Master hand_pallet',
            'message' => ''
        );

        if(count($data_berat_hand_pallet)>0){
            $result['message'] = 'Siklus kandang telah dilakukan aktivasi. Harap melakukan penimbangan hand_pallet.';
        }


        return $result;
    }

    public function check_hand_pallet($kode_farm, $kode_hand_pallet, $tgl_timbang = NULL) {
        $filter = "";
        if(!empty($tgl_timbang)){
            $filter = "and cast(TGL_TIMBANG as date) = '$tgl_timbang' and cast(TGL_TIMBANG as date) = cast(getdate() as date)";
        }
        $query = <<<QUERY

            select
                *
            from M_hand_pallet
            where KODE_FARM = '$kode_farm'
            and KODE_hand_pallet = '$kode_hand_pallet'
            $filter

QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function history_hand_pallet($kode_farm, $kode_hand_pallet) {
        $query = <<<QUERY

            select
                mp.*
                , case
                    when mp.STATUS_PALLET = 'N' then 'Aktif'
                    else 'Tidak Aktif'
                end STATUS_LABEL
                , cast(mp.brt_bersih as decimal(20,1)) BRT_BERSIH
                , mp._DEFAULT
            from m_hand_pallet mp
            where mp.KODE_FARM = '$kode_farm'
            and mp.KODE_hand_pallet = '$kode_hand_pallet'
            order by mp.TGL_TIMBANG
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ubah_status_hand_pallet($kode_farm, $kode_hand_pallet, $status_pallet, $keterangan, $tanggal_penimbangan) {
        $query = <<<QUERY
            update m_hand_pallet
            set STATUS_PALLET = '$status_pallet'
                , keterangan = '$keterangan'
            output inserted.status_pallet
            where kode_farm = '$kode_farm'
            and kode_hand_pallet = '$kode_hand_pallet'
            and cast(tgl_timbang as date) = '$tanggal_penimbangan'
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function ubah_default_hand_pallet($kode_farm, $kode_hand_pallet, $status_pallet, $tanggal_penimbangan, $default) {
        $this->ubah_all_status_hand_pallet($kode_farm);
        $query = <<<QUERY
            update m_hand_pallet
            set _DEFAULT = $default
            output inserted._DEFAULT _default
            where kode_farm = '$kode_farm'
            and kode_hand_pallet = '$kode_hand_pallet'
            and cast(tgl_timbang as date) = '$tanggal_penimbangan'
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function ubah_all_status_hand_pallet($kode_farm) {
        $query = <<<QUERY
            update m_hand_pallet
            set _DEFAULT = 0
            output inserted._DEFAULT _default
            where kode_farm = '$kode_farm'
            --and (_default = 0 or _default is null)
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function generate_kode_hand_pallet($kode_farm) {
        $query = <<<QUERY
            select
                '$kode_farm/HP/'+replace((str(max(right(KODE_HAND_PALLET, 2))+1, 2)),' ','0') kode_hand_pallet
                , cast(GETDATE() as date) tanggal
            from M_HAND_PALLET
            where KODE_FARM = '$kode_farm'
QUERY;

        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if($data['kode_hand_pallet'] == $kode_farm.'/HP/' || empty($data['kode_hand_pallet'])){
            $data['kode_hand_pallet'] = $kode_farm.'/HP/01';
        }
        return $data;
    }
}
