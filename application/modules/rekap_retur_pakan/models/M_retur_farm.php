<?php
class M_retur_farm extends MY_Model{	
	protected $_table;
	private $_kodeRetur = 'RL';
	public $primary_key = 'NO_RETUR';
	public function __construct(){
		parent::__construct();
		$this->_table = 'RETUR_FARM';
	}

	public function getNomerRetur($kodeFarm,$periodeSiklus){
		$panjangUrut = 2;
		$sql = <<<SQL
		SELECT '{$this->_kodeRetur}/{$kodeFarm}/{$periodeSiklus}/'+ replace(str(coalesce(max(right(NO_RETUR,{$panjangUrut})),0)+1,{$panjangUrut}),' ',0) as nomer FROM RETUR_FARM WHERE NO_RETUR LIKE '{$this->_kodeRetur}/{$kodeFarm}/{$periodeSiklus}/%' AND farm_asal = '{$kodeFarm}'
SQL;
		return $this->db->query($sql)->row();
	}

	public function getSisaPakan($kodeFarm){		
		/** cari minimum no_pallet  */
		$sqlPallet = <<<SQL
		select min(no_pallet) no_pallet from MOVEMENT_D md
		join KANDANG_SIKLUS ks
			on ks.NO_REG = md.keterangan2 and ks.KODE_FARM = md.KODE_FARM AND ks.KODE_SIKLUS = 73 -- and ks.status_siklus = 'O'
		where md.keterangan1 = 'PUT' and md.KODE_FARM = '{$kodeFarm}'
SQL;
		$noPalletMinimal = 'SYS0000';
		$pallet = $this->db->query($sqlPallet)->row();
		if(!empty($pallet)){
			$noPalletMinimal = $pallet->no_pallet;
		}
		$sql = <<<SQL
		
		SELECT sum(jml_on_hand) jumlah,sum(berat_putaway) berat,movement_d.kode_barang kode_pakan,m_barang.nama_barang nama_pakan, m_barang.bentuk_barang bentuk
		FROM movement_d 
		join m_barang on m_barang.kode_barang = movement_d.kode_barang
		WHERE KETERANGAN2 IN ( SELECT kode_gudang FROM m_gudang WHERE KODE_FARM = '{$kodeFarm}') 
		AND KODE_FARM = '{$kodeFarm}'
		AND no_pallet >= '{$noPalletMinimal}'
		-- AND NO_PALLET NOT IN (SELECT NO_PALLET FROM MOVEMENT_D WHERE kode_farm = '{$kodeFarm}' and no_pallet >= '{$noPalletMinimal}' and NO_REFERENSI LIKE 'RL%')
		AND NO_REFERENSI LIKE 'RTN%'	
		GROUP BY movement_d.kode_barang,m_barang.nama_barang, m_barang.bentuk_barang
SQL;
		return $this->db->query($sql)->result_array();
	}

	public function pengurangPakan($kodeFarm,$periodeSiklus){
		$prefix = $this->_kodeRetur.'/'.$kodeFarm.'/'.$periodeSiklus;
		$sql = <<<SQL
		SELECT rfd.kode_pakan,sum(rfd.jumlah) jumlah 
		FROM RETUR_FARM rf 
		JOIN RETUR_FARM_D rfd ON rfd.NO_RETUR = rf.NO_RETUR 
		WHERE rf.STATUS not in ('V','RJ1','RJ2') 
		-- and rf.NO_RETUR not in (
		--	SELECT no_referensi FROM MOVEMENT_D WHERE NO_REFERENSI LIKE '{$prefix}%' AND KODE_FARM = '{$kodeFarm}'
		-- )
		AND rf.NO_RETUR LIKE '{$prefix}%'
		GROUP BY rfd.kode_pakan
SQL;
		return $this->db->query($sql)->result_array();
	}


	public function listReturTimbang($parameter,$tglKirim){
		$belumTindakLanjut = $parameter['belumTindakLanjut'];
		$kodeFarm = $parameter['rf.farm_asal'];
		$whereParam = array();
		unset($parameter['belumTindakLanjut']);
		foreach($parameter as $k => $v){
			array_push($whereParam,$k .' = \''.$v.'\'');
		}
		if(!$belumTindakLanjut){
			if(!empty($tglKirim['startDate'])){
				if(!empty($tglKirim['endDate'])){
					array_push($whereParam,'rf.tgl_kirim between \''.$tglKirim['startDate'].'\' and \''.$tglKirim['endDate'].'\'');
				}else{
					array_push($whereParam,'rf.tgl_kirim >=\''.$tglKirim['startDate'].'\'');				
				}
			}else{
				if(!empty($tglKirim['endDate'])){
					array_push($whereParam,'rf.tgl_kirim <=\''.$tglKirim['endDate'].'\'');								
				}
			}
		}else{
			array_push($whereParam,'(md.no_referensi is null or md.jml_on_pick > 0)');								
		}
		
		$whereStr = implode(' and ',$whereParam);
		$sql = <<<SQL
		SELECT md.NO_REFERENSI no_pengiriman,rf.NO_RETUR no_retur,rf.FARM_TUJUAN farm_tujuan,rf.TGL_KIRIM tgl_kirim,sum(case when md.JML_ON_PICK > 0 then md.JML_ON_PICK else md.JML_PICK end ) jml_kebutuhan
		FROM RETUR_FARM rf
		LEFT JOIN movement_d md ON md.NO_REFERENSI = rf.NO_RETUR AND md.KODE_FARM = '{$kodeFarm}'
		WHERE rf.STATUS = 'A' and {$whereStr}
		GROUP BY md.NO_REFERENSI,rf.NO_RETUR,rf.FARM_TUJUAN,rf.TGL_KIRIM
SQL;
		
		return $this->db->query($sql)->result_array();
	}
	/** cari pallet yang masih memiliki stok untuk dipindahkan ke farm lain */
	public function generate($kodeFarm,$noRetur){
		$sql = <<<SQL
		exec dbo.GENERATE_TIMBANG_RETUR_FARM '{$kodeFarm}','{$noRetur}'
SQL;
		return $this->db->query($sql)->row();	
	}

	public function detailTimbang($noRetur,$kodeFarm){
		$sql = <<<SQL
		select md.*
				,mb.NAMA_BARANG 
				,(select top 1 mhp.BRT_BERSIH from M_HAND_PALLET mhp where mhp._DEFAULT = 1 and mhp.KODE_FARM = '$kodeFarm' and mhp.STATUS_PALLET = 'N' order by tgl_timbang desc) BERAT_HAND_PALLET
				,m.JML_ON_HAND JML_AKTUAL
		from movement_d md
		join movement m on md.no_pallet = m.no_pallet and md.kode_farm = m.kode_farm
		join m_barang mb on mb.kode_barang = md.kode_barang
		where no_referensi = '{$noRetur}'
SQL;
		return $this->db->query($sql)->result_array();
	}
	
}
