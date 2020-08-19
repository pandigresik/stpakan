<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Sinkronisasi extends MX_Controller {
	protected $result;
	protected $_user;

	public function __construct() {
		parent::__construct ();
		$this->load->model('sinkronisasi/m_sinkronisasi','sinc');
		$this->load->model('sinkronisasi/m_detail_sinkronisasi','dsinc');
		$this->load->config('stpakan');
	}

	public function index(){
//		$where = array('tgl_sinkron is null','id_ref is null');
		$limit = $this->input->get('limit');
		$search = $this->input->get('cari');
		$limit = empty($limit) ? 15 : $limit; 
		$where = array();
		if(!empty($search)){
			$where = 'id in (select distinct sinkronisasi from detail_sinkronisasi_e where kunci like \'%'.$search.'%\')';
		}
		$sinc = $this->sinc->order_by('id','desc')->limit($limit)->get_many_by($where);
		$data['data'] = $sinc;
		$data['limit'] = $limit;
		$data['list_option'] = array(15,50,100,150,250,500);
		$data['search'] = $search;
		$this->load->view('sinkronisasi/index',$data);
	}

	public function insert($datatransaksi,$dataDetail,$sqlDetail,$farmTujuan = NULL){
		//$sinkron = $this->config->item('sinkronisasi');
		$conf = $datatransaksi;
		if(!empty($farmTujuan)){
			$conf['tujuan'] = $farmTujuan;
		}
		$id = $this->sinc->insert($conf);
		/* key1 selalu berisi id dari sinkronisasi */
		$dataDetail[':key1'] = $id;
		#echo $sqlDetail;
		$this->insert_detail($dataDetail,$sqlDetail);
	}

	public function insert_detail($dataKey,$sql){
		$sql = $this->generateSqlDetail($dataKey,$sql);
		$sqlInsert = <<<SQL
		insert into detail_sinkronisasi
		{$sql}
SQL;
		//log_message('error',$sqlInsert);
		$this->db->query($sqlInsert);
	}

	/* dapatkan data yang akan diinsert berdasarkan nama_tabel dan keynya*/
	public function generateSqlDetail($dataKey,$sql){
		return $this->buildSql($sql,$dataKey);
	}

	private function buildSql($sql,$data_array = array()){
		return preg_replace_callback("/(:key\d+)/i", function($m) use($data_array){
  			return is_string($data_array[$m[1]]) ? '\''.$data_array[$m[1]].'\'' : $data_array[$m[1]];
		}, $sql);
	}

	public function pushData($id){
	/* $id adalah id terakhir yang telah disimpan di server utama */
		$result = array();
		#$id = 265;
		#$id2 = 265;
		$all_sinkron = $this->sinc->limit(1)->get_many_by(array('id > '.$id.' and id_ref is null and tgl_sinkron is null'));

		if(!empty($all_sinkron)){
			foreach($all_sinkron as $data_sinkron){
				#print_r($data_sinkron);
				$kirim = array('data_sinkron' => $data_sinkron, 'data_aplikasi' => array());
				$upload = $this->getDataUpload($data_sinkron->id);				

				$kirim['data_aplikasi'] = $upload ;
				$kirim['hash'] = hash('sha256',json_encode($upload));
				array_push($result,$kirim);
			}

			return $result;

		}

	}
	/* untuk mendapatkan id terakhir yang disimpan di db local
	 * dimana field asal tidak bernilai farm tersebut
	 * */
	public function lastIdSinkronLocal(){
		$result = 0;
		$asal_farm = $this->config->item('idFarm');
		$lastId = $this->sinc->order_by('id','desc')->limit(1)->get_by(array('asal != \''.$asal_farm.'\'','id_ref is not null'));
		if(!empty($lastId)){
			$result = $lastId->id_ref;
		}
		return $result;
	}

	public function updateInfo($waktuSinkron){
		foreach($waktuSinkron as $k => $w){
			$this->sinc->update($k,array('tgl_sinkron'=>$w));
		}
	}

	/* perbarui data di client sesuai dengan data yang diterima dari server utama */
	public function simpanData($data_all){

		$waktu_sinkron = array();
		$this->db->trans_start();
		if(isset($data_all['data_sinkron'])){
			$loop_data = array($data_all);
		}
		else{
			$loop_data = $data_all;
		}
		//log_message('error',json_encode($loop_data));
		foreach($loop_data as $data){
			$data_sinkron = $data['data_sinkron'];
			$data_aplikasi = $data['data_aplikasi'];

			foreach($data_aplikasi as $kl => $data_tabel){
				foreach($data_tabel as $kk => $d_tabel){
					if(!isset($d_tabel[0])){
						$d_tabel = array('0' => $d_tabel);
					}

					foreach($d_tabel as $tabel){

						$nama_tabel = $tabel['nama_tabel'];
						$aksi = $tabel['aksi'];
						$identity = $tabel['identity'];
		
						$data_aksi = isset($tabel['data']['datum']) ? $tabel['data']['datum'] : $tabel['data']['item'];
						$where = isset($tabel['_where']) && !empty($tabel['_where']) ? $tabel['_where'] : null;

						try{
							if(!empty($where)){
								if($aksi == 'update'){
									if($this->isMultiDimensional($data_aksi)){
										foreach($data_aksi as $update){
											$update = $this->removeKeyUpdate($update,array_keys($where));
											//$this->db->where($where)->$aksi($nama_tabel,$update);
											/** periksa dulu apakah ada datanya atau tidak, jika tidak ada lakukan insert */
											$checkData = $this->db->where($where)->get($nama_tabel)->row_array();
											if(!empty($checkData)){
												$rawSQL = $this->db->where($where)->set($update)->get_compiled_update($nama_tabel);
											}else{
												$rawSQL = $this->db->set($update)->get_compiled_insert($nama_tabel);
											}	

											if($identity){
												$sqlAll = <<<sql
												SET IDENTITY_INSERT {$nama_tabel} ON
												{$rawSQL}
												SET IDENTITY_INSERT {$nama_tabel} OFF
sql;
											}
											else{
												$sqlAll = $rawSQL;
											}
											/* ini untuk update */
											$this->db->query($sqlAll);
										}
									}else{
										//$this->db->where($where)->$aksi($nama_tabel,$data_aksi);
										$data_aksi = $this->removeKeyUpdate($data_aksi,array_keys($where));
										
										/** periksa dulu apakah ada datanya atau tidak, jika tidak ada lakukan insert */
										$checkData = $this->db->where($where)->get($nama_tabel)->row_array();
										if (!empty($checkData)) {
											$rawSQL = $this->db->where($where)->set($data_aksi)->get_compiled_update($nama_tabel);
										} else {
											$rawSQL = $this->db->set($data_aksi)->get_compiled_insert($nama_tabel);
										}
										
										if($identity){
											$sqlAll = <<<sql
											SET IDENTITY_INSERT {$nama_tabel} ON
											{$rawSQL}
											SET IDENTITY_INSERT {$nama_tabel} OFF
sql;
										}
										else{
											$sqlAll = $rawSQL;
										}
										/* ini untuk update */
										$this->db->query($sqlAll);
									}
								}else{
									/** ini untuk delete */
									$rawSQL = $this->db->where($where)->get_compiled_delete($nama_tabel);
									$this->db->query($rawSQL);
									//log_message('error','hapus id sinkron '.$data_sinkron['id'].' '.$rawSQL);
								}								
							}else{
								$rawSQL = $this->db->set($data_aksi)->get_compiled_insert($nama_tabel);
								//log_message('error','insert id sinkron '.$data_sinkron['id'].' '.$rawSQL);
								if($identity){
									$sqlAll = <<<sql
									SET IDENTITY_INSERT {$nama_tabel} ON
									{$rawSQL}
									SET IDENTITY_INSERT {$nama_tabel} OFF
sql;
								}
								else{
									$sqlAll = $rawSQL;
								}
								/* ini untuk insert */
								$this->db->query($sqlAll);
							}
						}catch(\Exception $e){
						//	throw new \Exception($e->getMessage());
							$message = array('status' => 0,'message' => $e->getMessage());
						}
					}
				}
			}
			$data_sinkron['aksi'] = 'PULL';
			$data_sinkron['id_ref'] = $data_sinkron['id'];
			$data_sinkron['tgl_sinkron'] = date('Y-m-d H:i:s');
			$waktu_sinkron[$data_sinkron['id']] = $data_sinkron['tgl_sinkron'];
			unset($data_sinkron['id']);
			try{
				$this->sinc->insert($data_sinkron);
			}
			catch(\Exception $e){
			//	throw new \Exception($e->getMessage());
				$message = array('status' => 0,'message' => $e->getMessage());
			}
		}
		$this->db->trans_complete();

		$this->result['content'] = json_encode($waktu_sinkron) ;
		$this->result['status'] = 1 ;
		$this->result['message'] = 'OK';
		return $this->result;
	}

	/* dapatkan semua data yang akan diupload ke server sinkron */
	private function getDataUpload($idSinkronisasi){

		$list_aksi = $this->dsinc->order_by('no_urut','desc')->get_many_by(array('sinkronisasi' => $idSinkronisasi));

		$tmp = array('data_tabel' => array());
		$data_tabel = array();
		$aksi_arr = array('I' => 'insert','U' => 'update', 'D' => 'delete');

		foreach($list_aksi as $aksi){
			$data_tabel['aksi'] = $aksi_arr[$aksi->aksi];
			$nama = $aksi->tabel;
			// jika aksi = update maka where harus diisi
			$where = json_decode($aksi->kunci,1);						
			$data_tabel['nama_tabel'] = $nama;
			$data_tabel['identity'] = $aksi->status_identity;
			if(in_array($data_tabel['aksi'],array('update','delete'))){
				$data_tabel['_where'] = $where;
			}else{
				$data_tabel['_where'] = null;
			}
			if(in_array($data_tabel['aksi'],array('delete'))){
				$content = array();
			}else{
				$content = $this->db->where($where)->get($nama)->result_array();
			}
					
			$data_tabel['data'] = $this->removeEmpty($content);		
			array_push($tmp['data_tabel'],$data_tabel);
		}
	return $tmp;
	}

	private function removeEmpty($arr){
		$tmp = array();
		if(!empty($arr)){
			foreach($arr as $r){
				array_push($tmp,array_filter($r,function($var){
						return !is_null($var);
					}
				));
			}
		}		
		return $tmp;
	}



	private function removeKeyUpdate($update,$keys){
		foreach($keys as $k){
				unset($update[strtoupper($k)]);
		}
		return $update;
	}

	private function isMultiDimensional($myarray){
		$result = 1;
		if (count($myarray) == count($myarray, COUNT_RECURSIVE)){
			$result = 0;
		}
		return $result;
	}

	public function sinkronUlang($id){
		$conf = $this->sinc->as_array()->get($id);
		unset($conf['id']);
		unset($conf['tgl_sinkron']);
		$conf['transaksi'] = $conf['transaksi'].'_'.$id;
		$idbaru = $this->sinc->insert($conf);
		$sql=<<<SQL
		insert into detail_sinkronisasi_e (sinkronisasi,aksi,tabel,kunci,status_identity)
		select {$idbaru} as sinkronisasi,'U' as aksi,tabel,kunci,status_identity from detail_sinkronisasi_e  where sinkronisasi = {$id} order by no_urut	
SQL;
		$s = $this->db->query($sql);
		if($s){
			echo 'buat sinkron ulang id '.$id.' berhasil';
		}else{
			echo 'buat sinkron ulang id '.$id.' gagal';
		}

	}

	public function detailSinkron(){
		$idSinkron = $this->input->get('id');
		$dataSinkron = $this->dsinc->as_array()->get_many_by(array('sinkronisasi' => $idSinkron));
		$this->load->view('sinkronisasi/detail_sinkron',array('data' => $dataSinkron));
	}
}
