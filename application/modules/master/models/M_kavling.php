<?php
class M_kavling extends CI_Model{
	private $dbSqlServer ;

	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}

	function get_kavling($start = null, $offset = null, $namafarm = null, $namagudang = null,
						$nokavling = null, $jmlpallet = null, $status = null){

		$filter_str = "";
		$filter_arr = array();

		$filter_bottom_str = "";
		$filter_bottom_arr = array();

		if(isset($namafarm))
			$filter_arr[] = "b.nama_farm like '%".$namafarm."%'";
		if(isset($namagudang))
			$filter_arr[] = "c.nama_gudang like'%".$namagudang."%'";
		if(isset($nokavling))
			$filter_arr[] = "a.no_kavling like '%".$nokavling."%'";
	/*	if(isset($maxberat))
			$filter_arr[] = "a.max_berat like '%".$maxberat."%'";
	*/
		if(isset($jmlpallet))
			$filter_arr[] = "a.jml_pallet like '%".$jmlpallet."%'";
		if(isset($status))
			$filter_arr[] = "a.status_kavling = '".$status."'";

		if(count($filter_arr) > 0){
			$filter_str .= " where ";
			$filter_str .= implode(" and ", $filter_arr);
		}

		if(isset($start) and isset($offset))
			$filter_bottom_arr[] = "row > {$start} and row <= {$offset}";

		if(count($filter_bottom_arr) > 0){
			$filter_bottom_str .= " where ";
			$filter_bottom_str .= implode(" and ", $filter_bottom_arr);
		}

		$kode_user = $this->session->userdata("kode_user");

		$sql = <<<QUERY
			select * from (
				select
				  ROW_NUMBER() OVER (ORDER BY b.nama_farm) as row,
				  a.kode_farm, b.nama_farm, c.kode_gudang, c.nama_gudang, a.no_kavling, a.layout_posisi, a.jml_pallet, a.status_kavling
				from m_kavling a
				inner join m_farm b on a.kode_farm = b.kode_farm
				inner join m_gudang c on a.kode_gudang = c.kode_gudang and c.kode_farm = b.kode_farm
				inner join pegawai_d d on d.kode_farm = b.kode_farm and d.kode_pegawai = '{$kode_user}'
				$filter_str
	) mainqry
	$filter_bottom_str
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_kavling_existing($kodefarm, $kodegudang){
		$sql = <<<QUERY
			select no_kavling from m_kavling where kode_farm = '{$kodefarm}' and kode_gudang = '{$kodegudang}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_farm(){
		$sql = <<<QUERY
			select a.kode_farm, a.nama_farm from m_farm a
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_farm_gudang($kodefarm){
		$sql = <<<QUERY
		select a.kode_gudang, a.nama_gudang + ' ' + b.nama_farm + ' - ' + a.kode_gudang [nama_gudang]
		from m_gudang a
		inner join m_farm b on b.kode_farm = a.kode_farm
		where b.kode_farm = '{$kodefarm}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_gudang_in_farm($kode_farm){
		$sql = <<<QUERY
			select a.kode_gudang, a.nama_gudang
			from m_gudang a
			where a.kode_farm = '{$kode_farm}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	function insert($data){
		$this->dbSqlServer->trans_begin();
		$n_success = 0;
        $p_success = 0;

		for($i=0;$i<count($data);$i++){
			$this->dbSqlServer->insert("m_kavling", $data[$i]);

			if(($this->dbSqlServer->affected_rows() > 0)){
				$n_success++;
			}
            $x = 0;
            for($j=1;$j<=$data[$i]['jml_pallet'];$j++){
                $no_kolom = str_pad($data[$i]['no_kolom'], 2, '0', STR_PAD_LEFT);
                $no_urut = str_pad($j, 2, '0', STR_PAD_LEFT);
                $no_kavling = $data[$i]['no_baris'].''.$data[$i]['no_posisi'].'-'.$no_kolom;
                $kode_pallet = $no_kavling.'-'.$no_urut;
                $data_pallet = $this->insert_m_pallet($data[$i]['kode_farm'], $data[$i]['kode_gudang'], $no_kavling, $kode_pallet);
                if(!empty($data_pallet['kode_pallet'])){
                    $x++;
                }
            }
            if($x == $data[$i]['jml_pallet']){
                $p_success++;
            }
		}

		if($n_success == count($data) && $p_success == count($data)){
			$this->dbSqlServer->trans_commit();
			return true;
		}else{
			$this->dbSqlServer->trans_rollback();
			return false;
		}
	}
	
	function insert_m_pallet($kode_farm, $kode_gudang, $no_kavling, $kode_pallet){            
        $sql = <<<QUERY
            insert into m_pallet (
                kode_farm
                , kode_gudang
                , no_kavling
                , kode_pallet
                , tgl_timbang
                , status_pallet
            )
            output inserted.kode_pallet
            values(
                '$kode_farm'
                , '$kode_gudang'
                , '$no_kavling'
                , '$kode_pallet'
                , getdate()
                , 'N'
            )
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    }

	function update($data, $kode_farm, $kode_gudang, $baris, $nomorposisi, $kolom1, $namaposisi){

		$this->dbSqlServer->where("kode_farm", $kode_farm);
		$this->dbSqlServer->where("kode_gudang", $kode_gudang);
		$this->dbSqlServer->where("no_baris", $baris);
		$this->dbSqlServer->where("no_posisi", $nomorposisi);
		$this->dbSqlServer->where("layout_posisi", $namaposisi);
		$this->dbSqlServer->where("no_kolom", $kolom1);

		$this->dbSqlServer->update("m_kavling", $data);

		return ($this->dbSqlServer->affected_rows() > 0) ? true : false;
	}

	function get_data_kavling($kode_farm, $kode_gudang, $no_kavling, $baris, $no_posisi, $no_kolom, $lay_posisi = null){

		$layout_posisi = isset($lay_posisi) ? " and layout_posisi = '{$lay_posisi}'" : "";

		$sql = <<<QUERY
			select kode_farm, kode_gudang, kode_verifikasi, layout_posisi, jml_pallet,
				   no_baris, no_kavling, no_kolom, no_posisi, status_kavling
			from m_kavling
			where kode_farm = '{$kode_farm}' and
				  kode_gudang = '{$kode_gudang}' and
				  no_kavling = '{$no_kavling}' and
				  no_baris = '{$baris}' and
				  no_posisi = '{$no_posisi}' and
				  no_kolom = '{$no_kolom}'
				  {$layout_posisi}

QUERY;

		log_message("error", $sql);
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	function getGudangInFarm($kode_farm){
		$sql = <<<QUERY
			select kode_farm, kode_gudang, nama_gudang, nama_gudang + ' - ' + kode_gudang as nama_gudang_long
			from m_gudang
			where kode_farm = '{$kode_farm}'
QUERY;

		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	/*layout kavling, duplikasi dari M_transaksi*/
	public function group_layout_kavling($kode_farm, $kode_gudang) {
        $alldata = $this->layout_kavling($kode_farm, $kode_gudang);

        $result = [];
        foreach ($alldata as $key => $item) {
            $result ['max_no_baris'] = $item ['MAX_NO_BARIS'];
            $result ['data_kavling'] [$item ['NAMA_GUDANG']] [$item ['NO_POSISI']] [$item ['LAYOUT_POSISI']] [$item ['NO_KOLOM']] [$item ['NO_BARIS']] [$item ['NO_KAVLING']] [] = $item;
            $result ['data_kolom'] [$item ['NAMA_GUDANG']] [$item ['NO_POSISI']] [$item ['LAYOUT_POSISI']] = array(
                'min_kolom' => $item['min_kolom'],
                'max_kolom' => $item['max_kolom']
               );
        }

        return $result;
    }

	public function layout_kavling($kode_farm, $kode_gudang) {
        $query = <<<QUERY
       		EXEC BROWSE_KAVLING '$kode_farm', '$kode_gudang'
QUERY;
        $stmt = $this->dbSqlServer->conn_id->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
