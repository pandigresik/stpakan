<?php
class M_do extends CI_Model{
	protected $_table;
	public function __construct(){
		parent::__construct();
		$this->_table = 'do';
	}
	public function get($param = array()){
		if(!empty($param)){
			$this->db->where($param);
		}
		return $this->db->get($this->_table);
	}
	
	
	public function simpan($no_op,$kode_farm,$tgl_kirim,$user,$tgl_sekarang){		
		$tahunKirim = substr($tgl_kirim,0,4);
		$prefix = 'DO/'.$kode_farm.'/'.$tahunKirim;
		/*
		*/	
		$sql = <<<SQL
		insert into do (no_do,no_op,tgl_kirim,no_urut,kode_farm,tgl_do,status_do,tgl_buat,tgl_ubah,user_buat,user_ubah) 
				output inserted.no_do,inserted.no_urut
		values ((select '{$prefix}'+'-'+replace(str(coalesce(max(convert(int,substring(no_do,12,20))),0) + 1,4),' ',0) from do
			where kode_farm = :kode_farm1 and  year(tgl_kirim) = {$tahunKirim} and NO_DO like 'DO%'),:no_op,:tgl_kirim
				,(select coalesce(max(no_urut),0) + 1 from do where no_op = :no_op1),:kode_farm
				,:tgl_do,'N',:tgl_buat,:tgl_ubah,:user_buat,:user_ubah)		
SQL;
		//echo '<pre>'.$sql;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam ( ':no_op', $no_op );
		$stmt->bindParam ( ':tgl_kirim', $tgl_kirim );
		$stmt->bindParam ( ':no_op1', $no_op );
		$stmt->bindParam ( ':kode_farm', $kode_farm );
		$stmt->bindParam ( ':kode_farm1', $kode_farm );
		$stmt->bindParam ( ':tgl_do', $tgl_sekarang );
		$stmt->bindParam ( ':tgl_buat', $tgl_sekarang );
		$stmt->bindParam ( ':tgl_ubah', $tgl_sekarang );
		$stmt->bindParam ( ':user_buat', $user );
		$stmt->bindParam ( ':user_ubah', $user );
		
		$stmt->execute();
		
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}
	public function simpan_pertglkirim($no_op,$kode_farm,$no_urut,$tgl_kirim,$user,$tgl_sekarang,$status_do = 'N'){		
		$tahunKirim = substr($tgl_kirim,0,4);
		$prefix = 'DO/'.$kode_farm.'/'.$tahunKirim;
		/*
		*/	
		$sql = <<<SQL
		insert into do (no_do,no_op,tgl_kirim,no_urut,kode_farm,tgl_do,status_do,tgl_buat,tgl_ubah,user_buat,user_ubah) 
				output inserted.no_do
		values ((select '{$prefix}'+'-'+replace(str(coalesce(max(convert(int,substring(no_do,12,20))),0) + 1,4),' ',0) from do
			where kode_farm = :kode_farm1 and  year(tgl_kirim) = {$tahunKirim} and NO_DO like 'DO%'),:no_op,:tgl_kirim
				,:no_urut,:kode_farm
				,:tgl_do,'{$status_do}',:tgl_buat,:tgl_ubah,:user_buat,:user_ubah)		
SQL;
		//echo '<pre>'.$sql;
		$stmt = $this->db->conn_id->prepare($sql);
		$stmt->bindParam ( ':no_op', $no_op );
		$stmt->bindParam ( ':tgl_kirim', $tgl_kirim );		
		$stmt->bindParam ( ':no_urut', $no_urut );
		$stmt->bindParam ( ':kode_farm', $kode_farm );		
		$stmt->bindParam ( ':kode_farm1', $kode_farm );		
		$stmt->bindParam ( ':tgl_do', $tgl_sekarang );
		$stmt->bindParam ( ':tgl_buat', $tgl_sekarang );
		$stmt->bindParam ( ':tgl_ubah', $tgl_sekarang );
		$stmt->bindParam ( ':user_buat', $user );
		$stmt->bindParam ( ':user_ubah', $user );
		
		
		$stmt->execute();
		
		return $stmt->fetch( PDO::FETCH_ASSOC );
	}
	public function insert($param = array()){
		$this->db->insert($this->_table,$param);
	}
	
	public function update($where = array(),$data = array()){
		$this->db->where($where);
		$this->db->update($this->_table,$data);
	}

	public function riwayat($no_do = array()){
		if(!is_array($no_do)){
			$no_do = array($no_do);
		}
		$no_do_str = implode("','",$no_do);
		$sql = <<<SQL
		select lpd.status
			,lpd.keterangan
			,stuff(convert(varchar(19), lpd.tgl_buat, 126),11,1,' ') tgl_buat	
			,mp.nama_pegawai
			,do.kode_farm
			,do.tgl_kirim	
		from log_ploting_do lpd
		join do on do.no_do = lpd.no_do
		join m_pegawai mp on mp.kode_pegawai = lpd.user_buat
		where lpd.no_do in ('{$no_do_str}')
		group by lpd.status
			,lpd.tgl_buat
			,lpd.user_buat
			,lpd.keterangan
			,mp.nama_pegawai
			,do.kode_farm
			,do.tgl_kirim	
			,lpd.no_urut
		order by lpd.no_urut desc
		
SQL;
		
		return $this->db->query($sql);
	}
}