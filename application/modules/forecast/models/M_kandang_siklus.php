<?php
class M_kandang_siklus extends MY_Model{
	protected $_table;
	private $_user;
	protected $primary_key;
	public function __construct(){
		parent::__construct();
		$this->_table = 'kandang_siklus';
		$this->primary_key = 'no_reg';
		$this->_user = $this->session->userdata('kode_user');
	}
	public function get_rdit_by_state($param){
		$this->db->where($param);
		$this->db->select(array('kode_farm','count(*) as total'));
		$this->db->group_by('KODE_FARM');
		return $this->db->get($this->_table);
	}
	public function simpan($dataKandang,$dataFarm,$docIn,$rilis = false){

		foreach($dataKandang as $row){
			$tmp = array();
			$tmp['kode_kandang'] = $row['kandang'];
			$tmp['kode_siklus'] = $dataFarm['kodeSiklus'];
			$tmp['no_reg'] =  $row['no_reg'];// $dataFarm['kodeFarm'].'-'.$dataFarm['periodeSiklus'].'/'.$row['kandang'];
			$tmp['kode_farm'] = $dataFarm['kodeFarm'];
			$tmp['kode_pegawai'] = $this->_user;
			$tmp['user_buat'] = $this->_user;
			$tmp['jml_betina'] = $row['betina'];
			$tmp['jml_jantan'] = $row['jantan'];
			$tmp['tipe_lantai'] = $row['lantai'];
			$tmp['tipe_kandang'] = $row['tipe'];
			$tmp['kode_std_breeding_j'] = $row['kode_std_breeding_j'];
			$tmp['kode_std_breeding_b'] = $row['kode_std_breeding_b'];
			$tmp['tgl_doc_in'] = $docIn;

			$tmp['status_siklus'] = 'O';
			if($rilis){
				$tmp['user_rilis'] = $this->_user;
				$tmp['tgl_rilis'] = date('Y-m-d H:i:s');
			}
			$this->db->insert($this->_table,$tmp);
		}
	}

	public function update_simpan($dataKandang = array(),$docIn){
		foreach($dataKandang as $row){
			$data = array(
					'jml_betina' => $row['betina'],
					'jml_jantan' => $row['jantan'],
					'tgl_doc_in' => $docIn,
					'kode_std_breeding_j' => $row['kode_std_breeding_j'],
					'kode_std_breeding_b' => $row['kode_std_breeding_b']
			);
			$no_reg = $row['no_reg'];
			$this->db->where('no_reg',$no_reg);
			$this->db->update($this->_table,$data);
		}
	}
	

	public function rilis($dataKandang = array(),$docIn){

		foreach($dataKandang as $row){
			$data = array(
					'tgl_rilis' => date('Y-m-d H:i:s'),
					'user_rilis'=> $this->_user,
					'jml_betina' => $row['betina'],
					'jml_jantan' => $row['jantan'],
					'tgl_doc_in' => $docIn,
					'kode_std_breeding_j' => $row['kode_std_breeding_j'],
					'kode_std_breeding_b' => $row['kode_std_breeding_b']
			);
			$no_reg = $row['no_reg'];
			$this->db->where('no_reg',$no_reg);
			$this->db->update($this->_table,$data);
		}
	}

	public function approve($dataKandang = array(),$docIn){

		foreach($dataKandang as $row){
			$data = array(
				'tgl_approve1' => date('Y-m-d H:i:s'),
				'user_approve1'=> $this->_user,
				'jml_betina' => $row['betina'],
				'jml_jantan' => $row['jantan'],
				'tgl_doc_in' => $docIn,
				'kode_std_breeding_j' => $row['kode_std_breeding_j'],
				'kode_std_breeding_b' => $row['kode_std_breeding_b']
			);
			$no_reg = $row['no_reg'];
			$this->db->where('no_reg',$no_reg);
			$this->db->update($this->_table,$data);
		}
	}

	public function approve_presdir($no_reg){

			$data = array(
					'tgl_approve2' => date('Y-m-d H:i:s'),
					'user_approve2'=> $this->_user,
			);

			$this->db->where('no_reg',$no_reg);
			$this->db->update($this->_table,$data);
	}

	public function reject_presdir($no_reg){
		$data = array(
				'tgl_approve1' => NULL,
				'user_approve1'=> NULL,

		);

		$this->db->where('no_reg',$no_reg);
		$this->db->update($this->_table,$data);
	}

	public function update_flok($noreg,$kodeflok){
		$data = array(
			'kode_flok' => $kodeflok
		);
		$this->db->where('no_reg',$noreg);
		$this->db->update($this->_table,$data);
	}

	public function list_farm_approval2($farm,$filter){

		$where = '';
		if(!empty($filter)){
			/* jika yang dicentang 3 maka gak usah ditambahkan apa2 */
			if(count($filter) < 3){
				if(isset($filter['filter_disetujui'])){
					$where = 'and ks.user_approve2 is not null';
					if(isset($filter['filter_approval'])){
						$where = 'or ks.user_approve2 is not null';
					}
					/* yang dicentang ditolak dan disetujui */
					if(isset($filter['filter_ditolak'])){
					//	$where = 'and ks.user_approve2 is not null and lks.jmlRilis > 1';
					}

				}
				else if(isset($filter['filter_ditolak'])){
					if(!isset($filter['filter_approval'])){
						$where = 'and lks.jmlRilis > 1';
					}
				}
			}
		}

		$sql = <<<SQL
		select ks.NO_REG no_reg
			,ks.kode_kandang
			,ks.JML_JANTAN jantan
			,ks.JML_BETINA betina
			,ks.TGL_DOC_IN tgl_doc_in
			,mf.NAMA_FARM nama_farm
			,mp.KODE_STRAIN strain
			,ks.USER_APPROVE2 approve
			,lks.jmlRilis jmlRilis
		from KANDANG_SIKLUS ks
		inner join M_PERIODE mp
		on mp.KODE_SIKLUS = ks.KODE_SIKLUS
		inner join M_FARM mf
		on mf.KODE_FARM = ks.KODE_FARM
		inner join (
			select no_reg,count(*) jmlRilis from LOG_KANDANG_SIKLUS lks
			where STATUS_APPROVE = 'A'
			group by no_reg
		)lks
		on lks.NO_REG = ks.NO_REG
		where ks.STATUS_SIKLUS = 'O'
		and ks.user_approve1 is not null
		and ks.TGL_DOC_IN > CURRENT_TIMESTAMP
		{$where}

SQL;

		return $this->db->query($sql);
	}

	public function get_first_docin($params = array(),$kodeflok = NULL){
		$this->db->where($params);
		$this->db->where('tgl_approve1 is not null');
		$this->db->select_min('tgl_doc_in');
		if(!empty($kodeflok)){
			$this->db->join('m_kandang mk','mk.kode_kandang = kandang_siklus.kode_kandang and mk.kode_farm = kandang_siklus.kode_farm and mk.no_flok='.$kodeflok);
		}
		return $this->db->get($this->_table);
	}

	public function get_first_docin_perflok($params = array()){
		$this->db->where($params);
		$this->db->where(array('status_siklus' => 'O'));
		$this->db->select('flok_bdy');
		$this->db->select_min('tgl_doc_in');
		$this->db->group_by('kode_siklus,flok_bdy');
		return $this->db->get($this->_table);
	}

	public function daftar_farm_kandang($kode_farm = NULL){
		$where = '';
		if(!empty($kode_farm)){
			$where = ' and ks.kode_farm = \''.$kode_farm.'\'';
		}
		$sql = <<<SQL
		select count(kode_kandang) jml_kandang
				,mp.KODE_STRAIN strain
				, ks.KODE_FARM kode_farm
				, ks.kode_siklus
				, mf.NAMA_FARM nama_farm
		from kandang_siklus ks
		inner join m_periode mp
			on mp.KODE_SIKLUS = ks.KODE_SIKLUS and ks.STATUS_SIKLUS = 'O'
		inner join m_farm mf
			on mf.kode_farm = ks.kode_farm
		where ks.tgl_approve1 is not null
		{$where}
		group by ks.KODE_FARM
			,mp.KODE_STRAIN
			, mf.NAMA_FARM
			, ks.kode_siklus
SQL;

		return $this->db->query($sql);
	}

	public function farm_budidaya(){
		return $this->db
			->select('kode_farm')
			->from('m_farm')
			->where('grup_farm','bdy')
			->get_compiled_select();
	}

	public function siklus_farm($kodeFarm, $status_siklus = 'O'){
		$sql = <<<SQL

			select distinct mp.kode_siklus, mp.periode_siklus
			from kandang_siklus ks
			join M_PERIODE mp on ks.KODE_SIKLUS = mp.KODE_SIKLUS
			where ks.STATUS_SIKLUS = '{$status_siklus}' and ks.KODE_FARM = '{$kodeFarm}'

SQL;
		return $this->db->query($sql);
	}
}
