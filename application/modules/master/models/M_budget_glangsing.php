<?php

class M_budget_glangsing extends CI_Model {

    private $dbSqlServer;

    public function __construct() {
        parent::__construct();
        $this->dbSqlServer = $this->load->database("default", true);
    }

    function get_data_budget($kode_budget, $nama_budget = null, $kategori = null, $status = null) {
		$sql = <<<QUERY
			select *,
            case when KATEGORI_BUDGET = 'I' then 'Internal' else 'Eksternal' end KATEGORI_BUDGET,
            case when STATUS = 'A' then 'Aktif'else 'Tidak Aktif' end STATUS
            from M_BUDGET_PEMAKAIAN_GLANGSING 
            where KODE_BUDGET like '%$kode_budget%'
            and NAMA_BUDGET like '%$nama_budget%'
            and KATEGORI_BUDGET like '%$kategori%'
            and STATUS like '%$status%'
            order by case when KATEGORI_BUDGET = 'I' then 1 else 2 end,KODE_BUDGET ASC
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
            order by mp.TGL_TIMBANG
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
      select top 1 JML_ON_HAND stok from MOVEMENT where KODE_PALLET = '{$idpallet}' and kode_farm ='{$kode_farm}' order by NO_PALLET desc
SQL;
    return $this->db->query($sql);
    }
	
	function docek_budget() {
		$id_budget = ($this->input->post("id_budget")) ? $this->input->post("id_budget") : null;
		
		$query = <<<QUERY
            SELECT *
			FROM M_BUDGET_PEMAKAIAN_GLANGSING
			where KODE_BUDGET = '$id_budget'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        
		$hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (count($hasil) == 0) {
			$result['passed'] = true;		
		}
		else{
			$result['passed'] = false;
		}
		return json_encode($result);	
	}	
	function doload_budget($id_budget = null) {
		$query = <<<QUERY
            SELECT *
			FROM M_BUDGET_PEMAKAIAN_GLANGSING
			where KODE_BUDGET = '$id_budget'
QUERY;
        #echo $query;
        $stmt = $this->db->conn_id->prepare($query);
        $stmt->execute();
        
		$hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($hasil as $key => $value) {
			$hasil = array(
				'id_budget'		=> $value['KODE_BUDGET'],
				'nama_budget' 	=> $value['NAMA_BUDGET'],
				'kategori_budget' => $value['KATEGORI_BUDGET'],
				'status' 		=> $value['STATUS'],
			);
		}
		$result['data'] = $hasil;
		return json_encode($hasil);	
	}
	function get_today(){
 		$sql = <<<QUERY
 		select getdate() as [today]
QUERY;
 		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
         $stmt->execute();
         return $stmt->fetch(PDO::FETCH_ASSOC);
 	}
    function getListFarm(){
        $sql = <<<QUERY
 		select * from m_farm
QUERY;
 		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	function dosave_budget() {
		$id_budget 	     = ($this->input->post("id_budget")) ? $this->input->post("id_budget") : null;
        $nama_budget     = ($this->input->post("nama_budget")) ? $this->input->post("nama_budget") : null;
        $kategori_budget = ($this->input->post("kategori_budget")) ? $this->input->post("kategori_budget") : null;
        $status 	= ($this->input->post("status")) ? $this->input->post("status") : null;
        $action 	= ($this->input->post("action")) ? $this->input->post("action") : null;
        $tgl_buat   = $this->get_today();
        $list_farm  = $this->getListFarm();
		$error 		= false;
		
        $this->dbSqlServer->trans_begin();
		if($action == 'add'){
            $data_budget_glangsing = array();
            $data_budget_glangsing['kode_budget'] = $id_budget;
            $data_budget_glangsing['nama_budget'] = $nama_budget;
            $data_budget_glangsing['kategori_budget'] = $kategori_budget;
            $data_budget_glangsing['status'] = 'A';
            $data_budget_glangsing['tgl_buat'] = $tgl_buat['today'];
            $this->dbSqlServer->insert("M_BUDGET_PEMAKAIAN_GLANGSING", $data_budget_glangsing);

            if($this->dbSqlServer->affected_rows() > 0){
				foreach($list_farm as $key => $data){
					$sinkronisasi = array();
					$sinkronisasi['transaksi'] = "simpan_master_budget_glangsing";
					$sinkronisasi['asal'] = "FM";
					$sinkronisasi['tujuan'] = $data['KODE_FARM'];
					$sinkronisasi['aksi'] = "PUSH";
					$sinkronisasi['tgl_buat'] = $tgl_buat['today'];
	   
					$this->dbSqlServer->insert("sinkronisasi", $sinkronisasi);
					
					if($this->dbSqlServer->affected_rows() > 0){
						$id = $this->dbSqlServer->insert_id();

						$detail_sinkronisasi = array();
						$detail_sinkronisasi["sinkronisasi"] = $id;
						$detail_sinkronisasi["aksi"] = "I";
						$detail_sinkronisasi["tabel"] = "M_BUDGET_PEMAKAIAN_GLANGSING";
						$detail_sinkronisasi["kunci"] = '{"KODE_BUDGET":"'.$id_budget.'"}';
						$detail_sinkronisasi["status_identity"] = 0;
						$this->dbSqlServer->insert("detail_sinkronisasi", $detail_sinkronisasi);

						if($this->dbSqlServer->affected_rows() <= 0){
						 // $this->dbSqlServer->trans_rollback();
						 // return false;
						 $error = true;
						}
					}else{
						// $this->dbSqlServer->trans_rollback();
						// return false;
						$error = true;
					}
				}
			}
            
            /* $query = <<<QUERY
				insert into M_BUDGET_PEMAKAIAN_GLANGSING (
					kode_budget
					, nama_budget
					, kategori_budget
					, status
					, tgl_buat
				)
				values(
					'$id_budget',
					'$nama_budget',
					'$kategori_budget',
					'A',
					getdate()
				);
QUERY;
			#echo $query;
			$stmt = $this->db->conn_id->prepare($query);
			$hasil = $stmt->execute(); */
		}
		else{
			$data_budget_glangsing = array(
				'nama_budget' 	  => $nama_budget,
				'kategori_budget' => $kategori_budget,
				'status' 		  => $status,
				'tgl_ubah' 		  => $tgl_buat['today'],
			);
			$this->dbSqlServer->where('kode_budget',$id_budget);
			$this->dbSqlServer->update("M_BUDGET_PEMAKAIAN_GLANGSING", $data_budget_glangsing);
			
			if($this->dbSqlServer->affected_rows() > 0){
				foreach($list_farm as $key => $data){
					$sinkronisasi = array();
					$sinkronisasi['transaksi'] = "ubah_master_budget_glangsing";
					$sinkronisasi['asal'] = "FM";
					$sinkronisasi['tujuan'] = $data['KODE_FARM'];
					$sinkronisasi['aksi'] = "PUSH";
					$sinkronisasi['tgl_buat'] = $tgl_buat['today'];
	   
					$this->dbSqlServer->insert("sinkronisasi", $sinkronisasi);
					
					if($this->dbSqlServer->affected_rows() > 0){
						$id = $this->dbSqlServer->insert_id();

						$detail_sinkronisasi = array();
						$detail_sinkronisasi["sinkronisasi"] = $id;
						$detail_sinkronisasi["aksi"] = "U";
						$detail_sinkronisasi["tabel"] = "M_BUDGET_PEMAKAIAN_GLANGSING";
						$detail_sinkronisasi["kunci"] = '{"KODE_BUDGET":"'.$id_budget.'"}';
						$detail_sinkronisasi["status_identity"] = 0;
						$this->dbSqlServer->insert("detail_sinkronisasi", $detail_sinkronisasi);

						if($this->dbSqlServer->affected_rows() <= 0){
						 // $this->dbSqlServer->trans_rollback();
						 // return false;
						 $error = true;
						}
					}else{
						// $this->dbSqlServer->trans_rollback();
						// return false;
						$error = true;
					}
				}
			}
			/* $query = <<<QUERY
				update M_BUDGET_PEMAKAIAN_GLANGSING
				set kode_budget = '$id_budget',
				nama_budget = '$nama_budget',
				kategori_budget = '$kategori_budget',
				status = '$status',
				tgl_ubah = getdate()
				where KODE_BUDGET = '$id_budget'
QUERY;
			
			#echo $query;
			$stmt = $this->db->conn_id->prepare($query);
			$hasil = $stmt->execute(); */
		}

		if(!$error){
			$this->dbSqlServer->trans_commit();
			$result['success'] = true;
		}
		else{
			$this->dbSqlServer->trans_rollback();
			$result['success'] = false;
		}
		return json_encode($result);
	}
}
