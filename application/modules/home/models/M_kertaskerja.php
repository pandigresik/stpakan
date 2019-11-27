<?php
class M_kertaskerja extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	
	}
	
	public function timeLinePP($noreg,$kebutuhan_awal,$kebutuhan_akhir){
		$sql = <<<SQL
		select l.NO_LPB
			,kirim.no_reg
			,count(do.NO_DO) - coalesce(kirim.do_terkirim,0) rit_sisa
			,kirim.sj_terakhir
			,case 
				when kirim.total_terima_pakan = 0 or kirim.total_terima_pakan is null then 'BELUM TERIMA'	
				when kirim.total_terima_pakan < ll.total_order then 'TERIMA SEBAGIAN'
				else 'TERIMA LENGKAP'
			 end status_terima
		--	,op.no_op
		from lpb l
		inner join op
			on op.NO_LPB = l.no_lpb
		inner join do
			on do.NO_OP = op.NO_OP
		inner join(
			select l.no_lpb
				,le.NO_REG
				,sum(le.jml_order) total_order
			from lpb l
			inner join lpb_e le
				on l.NO_LPB = le.NO_LPB and le.no_reg = '{$noreg}' and le.tgl_kebutuhan between {}
			where l.STATUS_LPB = 'A'
			group by l.NO_LPB
					,le.NO_REG		
		)ll
			on ll.NO_LPB = l.NO_LPB
		left join (
			select p.no_op
				,max(p.TGL_SURAT_JALAN) sj_terakhir
				,max(p.TGL_TERIMA) akhir_terima
				,coalesce(sum(pe.JUMLAH),0) total_terima_pakan
				,count(distinct p.keterangan1) do_terkirim
				,m.KETERANGAN1 no_reg
			from penerimaan p 
			inner join penerimaan_e pe
				on p.NO_PENERIMAAN = pe.NO_PENERIMAAN
			inner join movement m
				on m.NO_PALLET = pe.NO_PALLET and m.PUT_DATE is not null and m.STATUS_STOK = 'NM' and m.keterangan1 = '{$noreg}'
			group by p.NO_op
				,m.KETERANGAN1
		)kirim
			on kirim.no_op = op.no_op  and ll.NO_REG = kirim.no_reg
		group by l.NO_LPB
			,kirim.do_terkirim
			,kirim.sj_terakhir
			,kirim.total_terima_pakan
			,kirim.no_reg
			,ll.total_order
			,op.no_op
		
						
SQL;
		return $this->db->query($sql);
	}

	public function list_flock_all($where = array()){
		$this->load->model('forecast/m_kandang_siklus','ks');
		/* cari maximal tgl rhk tiap kandang */
		$this->db->select('max(rhk.tgl_transaksi) tgl_transaksi, ks.no_reg')
			->join('kandang_siklus ks','ks.no_reg = rhk.no_reg')
		;
		$this->db->from('rhk')->group_by('ks.no_reg');
		if(!empty($where)){
			$this->db->where($where);
		}
		$subquery = $this->db->get_compiled_select();

		$this->db->select('ks.kode_farm+\'/\'+mp.periode_siklus no_reg, ks.flok_bdy, ks.kode_farm,ks.kode_siklus,
							ks.kode_std_breeding_j,ks.kode_std_breeding_b,ks.jml_jantan,
							ks.jml_betina,min(ks.tgl_doc_in) tgl_doc_in,ks.tipe_kandang,mp.kode_strain,
							mf.nama_farm,max(rhk.tgl_transaksi) rhk_terakhir,
							ks.status_siklus,mp.periode_siklus ')
				->from('kandang_siklus ks')
				->join('m_periode mp','ks.kode_siklus = mp.kode_siklus')
				->join('m_farm mf','mf.kode_farm = ks.kode_farm')
				->join('m_kandang mk','mk.kode_kandang = ks.kode_kandang and mk.kode_farm = mf.kode_farm')
				->join('('.$subquery.') as rhk','rhk.no_reg = ks.no_reg','left')
				->group_by('ks.flok_bdy, ks.kode_farm,ks.kode_siklus,
							ks.kode_std_breeding_j,ks.kode_std_breeding_b,ks.jml_jantan,
							ks.jml_betina,ks.tipe_kandang,mp.kode_strain,
							mf.nama_farm, ks.status_siklus,mp.periode_siklus')
				;
		if(!empty($where)){
			$this->db->where($where);
		}
		
		return $this->db->get();

	}
	
	
}