<?php if (! defined ( 'BASEPATH' ))	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Pengembalian_budget_otomatis extends MY_Controller {
    protected $result = array('status' =>0, 'content' => '');
    function __construct(){
        parent::__construct();
    }

    public function resetBudget(){
        $this->load->model('pengembalian_sak/m_glangsing_movement','gm');
        $this->load->model('pengembalian_sak/m_glangsing_movement_d','gmd');
        $this->_user = $this->session->userdata ( 'kode_user' );        
        $kodefarm = $this->session->userdata('kode_farm');
        $tglServer = Modules::run('home/home/getDateServer');
        $tgl_buat = $tglServer->tglserver;
        $periode_aktif = $this->db->select('kode_siklus')->where(array('kode_farm' => $kodefarm))->order_by('kode_siklus','desc')->get('glangsing_movement')->row_array();
        $kodeSiklus = $periode_aktif['kode_siklus'];
        if(empty($kodeSiklus)){
            return;
        }
        $listBudget = array();
        $whereGlangsingMovement = array('kode_barang' => 'GBP','kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus);		
        $stokAwalGlangsing = $this->gm->get_by($whereGlangsingMovement);
        // echo $this->db->last_query();    
        $ref = time();    
        $glangsingMovementGBP = array(
			'kode_farm' => $kodefarm,
			'kode_siklus' => $kodeSiklus,
			'kode_barang' => 'GBP',
			'no_referensi' => $ref,
			'jml_awal' => $stokAwalGlangsing->jml_stok,
			'jml_order' => 0,
			'jml_akhir' => 0,
			'tgl_transaksi' => $tgl_buat,
			'keterangan1' => 'BUDGET_IN',
			'keterangan2' => '',
			'user_buat' => $this->_user,
		);

		
        $sql = <<<SQL
        SELECT sum(pd.jml_diminta) jml_diminta, pn.kode_budget, pn.no_ppsk 
        from ppsk_new pn
        JOIN ppsk_d pd ON pn.no_ppsk = pd.no_ppsk AND pd.tgl_terima IS NULL AND pd.jml_diminta > 0 AND pd.status_retur IS null
        where CAST(pn.tgl_kebutuhan AS date) < CAST(GETDATE() AS date) AND pn.kode_siklus = {$kodeSiklus}
        GROUP BY pn.kode_budget, pn.no_ppsk
        UNION ALL         
        select sum(pd.jml_diminta) jml_diminta, pn.kode_budget, pn.no_ppsk 
		from ppsk_new pn
		JOIN ppsk_d pd ON pn.no_ppsk = pd.no_ppsk AND pd.tgl_terima IS NULL AND pd.jml_diminta > 0 AND pd.status_retur IS null
		JOIN (
			SELECT no_ppsk,max(no_urut) no_urut FROM log_ppsk_new  WHERE CAST(tgl_buat AS DATE) = CAST(getdate() AS DATE) GROUP BY no_ppsk
		)lg ON lg.no_ppsk = pn.no_ppsk 
		JOIN log_ppsk_new lpn ON lpn.no_ppsk = pn.no_ppsk AND lpn.no_urut = lg.no_urut AND lpn.status = 'RJ'  
		where cast(pn.tgl_permintaan as date) = cast(getdate() as date) AND pn.kode_siklus = {$kodeSiklus}
		GROUP BY pn.kode_budget, pn.no_ppsk
SQL;
        $tidakDiambil = $this->db->query($sql)->result_array();
        $no_ppsk = array();
        $jmlBudget = array();
        $totalSak = 0;
        if(!empty($tidakDiambil)){
            $perBudget = simpleGrouping($tidakDiambil,'kode_budget');
            
            foreach($perBudget as $kb => $budget){
                foreach($budget as $val){
                    array_push($no_ppsk,$val['no_ppsk']);
                    if(!isset($jmlBudget[$kb])){
                        $jmlBudget[$kb] = array('jml' => 0);
                    }
                    $jmlBudget[$kb]['jml'] += $val['jml_diminta'];
                    $totalSak += $val['jml_diminta'];
                }                
            }
        
            $this->db->trans_start();
            
            $glangsingMovementGBP['jml_order'] = $totalSak;
            $glangsingMovementGBP['jml_akhir'] = $stokAwalGlangsing->jml_stok + $totalSak;
                            
            if(!empty($jmlBudget)){
                $this->gmd->insert($glangsingMovementGBP);                
                $this->gm->update_by($whereGlangsingMovement,array('jml_stok' => $stokAwalGlangsing->jml_stok + $totalSak));
                array_push($listBudget,array('kode_barang' => 'GBP','kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus));
                foreach($jmlBudget as $kb => $val){
                    $whereGlangsingBudgetMovement = array('kode_barang' => $kb,'kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus);
                    $stokAwalGlangsingBudget = $this->gm->get_by($whereGlangsingBudgetMovement);                
                    $glangsingMovementBudget = array(
                        'kode_farm' => $kodefarm,
                        'kode_siklus' => $kodeSiklus,
                        'kode_barang' => $kb,
                        'no_referensi' => $ref,
                        'jml_awal' => $stokAwalGlangsingBudget->jml_stok,
                        'jml_order' => -1 * $val['jml'],
                        'jml_akhir' => $stokAwalGlangsingBudget->jml_stok - $val['jml'],
                        'tgl_transaksi' => $tgl_buat,
                        'keterangan1' => 'BUDGET_OUT',
                        'keterangan2' => '',
                        'user_buat' => $this->_user,
                    );
                    $this->gmd->insert($glangsingMovementBudget);
                    $this->gm->update_by($whereGlangsingBudgetMovement,array('jml_stok' => $stokAwalGlangsingBudget->jml_stok - $val['jml']));
                    array_push($listBudget,array('kode_barang' => $kb,'kode_farm' => $kodefarm,'kode_siklus' => $kodeSiklus));
                }
                /* update status returnya */
                $textNoppsk = implode("','",$no_ppsk);
                $sqlListPpskNoreg = <<<SQL
                select no_ppsk, no_reg from ppsk_d where tgl_terima is null and no_ppsk in ('{$textNoppsk}') 
SQL;
                $listPpskNoreg = $this->db->query($sqlListPpskNoreg)->result_array();
                $listPpskNoregArr = array();
                if(!empty($listPpskNoreg)){
                    foreach($listPpskNoreg as $pn){
                        array_push($listPpskNoregArr,array('no_ppsk' => $pn['no_ppsk'], 'no_reg' => $pn['no_reg']));
                    }
                }
                $sqlUpdateRetur = <<<SQL
                update ppsk_d set status_retur = 1, tgl_retur = getdate() where tgl_terima is null and no_ppsk in ('{$textNoppsk}') 
SQL;
                $this->db->query($sqlUpdateRetur);
            }  
            $this->db->trans_complete();
            if($this->db->trans_status() === FALSE){
                $this->result['content'] = 'Gagal menyimpan..';
            }{
                $this->result['status'] = 1;
                $this->result['content'] = array( 'ppsk' => $no_ppsk, 'ref' => $ref, 'ppsk_d' => $listPpskNoregArr, 'listbudget' => $listBudget, 'kode_siklus' => $kodeSiklus);
            }
    }    
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));              
    }
}