<?php
class M_Lsgas extends CI_Model{

	public function __construct(){
		parent::__construct();

	}

	public function stok_pakan($kode_farm,$tgl_transaksi,$tgl_akses){

		$sql = <<<SQL
		exec dbo.STOK_PAKAN '{$kode_farm}','{$tgl_transaksi}'
SQL;
		$stmt = $this->db->conn_id->prepare($sql);

		$stmt->execute();
		return $stmt->fetchAll(2);
	}

	public function list_farm($id = null,$grup_farm){
		$where = 'where mf.grup_farm = \''.$grup_farm.'\'';
		$where .= (!empty($id)) ? ' and mf.kode_farm = \''.$id.'\'' : '' ;
		$sql = <<<SQL
		select mf.kode_farm
			,mp.kode_siklus
			,mf.nama_farm
			,mp.kode_strain
			,mp.periode_siklus
		from m_farm mf
		inner join m_periode mp
			on mp.kode_farm = mf.kode_farm and mp.status_periode = 'A' and kode_siklus in (select distinct kode_siklus from kandang_siklus where status_siklus = 'O' )
		{$where}
		order by mp.KODE_SIKLUS
SQL;

		return $this->db->query($sql);
	}

	function doReadStokGlangsingData() {
		$number = 1;
		$kode_farm = isset($_POST['kode_farm']) ? strtoupper(trim($_POST['kode_farm'])) : '';
		$result['aaData'] = array();
		$level_user = $this->session->userdata('level_user');
		switch ($level_user) {
			case 'KF':
				$status = "'RJ','N','R','A1','A2'";
				break;
			case 'KD':
				$status = "'RJ','N','R','A1','A2'";
				break;
			case 'KDB':
				$status = "'RJ','R','A1','A2'";
				break;
			case 'KDV':
				$status = "'RJ','A1','A2'";
				break;
			case 'KA':
				$status = "'RJ','R','A1','A2'";
				break;
			default :
				echo 'Halaman ini bukan untuk anda ';
		}
		$sql = <<<QUERY
			EXEC STK_GLANGSING_AKHIR_SIKLUS '{$kode_farm}'
QUERY;
		$stmt = $this->db->conn_id->prepare($sql);
        $stmt->execute();
        $hasil = $stmt->fetchAll(PDO::FETCH_ASSOC);


		foreach ($hasil as $key=>$data) {
			$user_level = $this->session->userdata('level_user');
			switch ($user_level) {
				case 'KF':
					$tombol = array(
						'progress' 		  => '',
						'draft' 		  => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'N\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Rilis</button>&nbsp;',
						'rilis' 		  => '',
						'review_kadept'   => '',
						'reject' 		  => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'N\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Rilis</button>&nbsp;',
						'approve_admin'   => '',
						'approve_kadiv'   => '',
					);
				break;
				case 'KD':
					$tombol = array(
						'progress' => '',
						'draft' => '',
						'rilis' => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'R\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Approve</button>&nbsp;
									<button class="btn btn-danger" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Reject</button>
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
							                <button class="btn btn-default" onclick="$(\'#tooltip-reject\').tooltipster(\'hide\');">batal</button>
							                <button class="btn btn-primary btn_simpan_reject" disabled style="margin-left: 5px" onclick="lsgas.reject(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Simpan</button>
							              </div>
							            </div>
							          </div>
							        </span>',

						'review_kadept' => '',
						'reject' => '',
						'approve_admin' => '',
						'approve_kadiv' => '',
					);
				break;
				case 'KDB':
					$tombol = array(
						'progress' => '',
						'draft' => '',
						'rilis' => '',
						'review_kadept' => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'A1\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Approve</button>&nbsp;
										<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Reject</button>',
						'reject' => '',
						'approve_admin' => '',
						'approve_kadiv' => '',
					);
				break;
				case 'KA':
					$tombol = array(
						'progress' => '',
						'draft' => '',
						'rilis' => '',
						'review_kadept' => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'A1\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Approve</button>&nbsp;
										<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Reject</button>',
						'reject' => '',
						'approve_admin' => '',
						'approve_kadiv' => '',
					);
				break;
				case 'KDV':
					$tombol = array(
						'progress' => '',
						'draft' => '',
						'rilis' => '',
						'review_kadept' => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'A1\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Approve</button>&nbsp;
											<button class="btn btn-danger" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Reject</button>
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
													<button class="btn btn-default" onclick="$(\'#tooltip-reject\').tooltipster(\'hide\');">batal</button>
													<button class="btn btn-primary btn_simpan_reject" disabled style="margin-left: 5px" onclick="lsgas.reject(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Simpan</button>
												  </div>
												</div>
											  </div>
											</span>',
						'reject' => '',
						'approve_admin' => '',
						'approve_kadiv' => '<button class="btn btn-primary" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'A2\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Approve</button>&nbsp;
											<button class="btn btn-danger" onclick="lsgas.update(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Reject</button>
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
													<button class="btn btn-default" onclick="$(\'#tooltip-reject\').tooltipster(\'hide\');">batal</button>
													<button class="btn btn-primary btn_simpan_reject" disabled style="margin-left: 5px" onclick="lsgas.reject(this,\''.$kode_farm.'\','.$data['KODE_SIKLUS'].',\'RJ\',\''.$data['PERIODE_SIKLUS'].'\',\''.$data['HARGA'].'\')">Simpan</button>
												  </div>
												</div>
											  </div>
											</span>',
					);
				break;
			}


			//echo "update t_users set user_password = '".base64_encode($data->MEMBER_ID)."' where user_name = '".$data->MEMBER_ID."';";
			$jml_sak_pakai = $data['SAK_PAKAI_INTERN'] + $data['SAK_PAKAI_EKSTERN'];
			$array = array(
				$number++,
				$data['PERIODE_SIKLUS'],
				$data['KODE_SIKLUS'],
				$data['NO_URUT'],
				$data['JML_TERIMA_PAKAN'],
				$data['JML_PAKAI_PAKAN'],
				$data['SAK_AWAL'],
				$data['SAK_TERIMA'],
				$data['SAK_PAKAI_INTERN'],
				$data['SAK_PAKAI_EKSTERN'],
				$data['SAK_DIJUAL'],
				$data['SAK_SISA'],
				$data['STATUS_DESC'],
				$jml_sak_pakai,
				$tombol[$data['STATUS']],
				$data['HARGA'],

			);
			array_push($result['aaData'],$array);
		}

		return json_encode($result);

	}
	public function getUserLSGAS($kode_siklus,$no_urut,$status)
	{
		$this->db->where('kode_siklus',$kode_siklus);
		$this->db->where('no_urut',$no_urut);
		$this->db->where('status',$status);
		$this->db->join('m_pegawai','lsgas.USER_BUAT = m_pegawai.KODE_PEGAWAI');
		$sql = $this->db->get('LOG_STOK_GLANGSING_AKHIR_SIKLUS lsgas');
		if($sql->num_rows() > 0){
			$data = $sql->result_array();
			$result['KODE_PEGAWAI'] = $data[0]['KODE_PEGAWAI'];
			$result['NAMA_PEGAWAI'] = $data[0]['NAMA_PEGAWAI'];
		}else {
			$result['KODE_PEGAWAI'] = '';
			$result['NAMA_PEGAWAI'] = '';
		}
		return $result;
	}
}
