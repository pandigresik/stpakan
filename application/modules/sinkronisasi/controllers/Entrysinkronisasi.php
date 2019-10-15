<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
    /*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Entrysinkronisasi extends MX_Controller
{
    protected $result;
    protected $_user;
    protected $idFarm;
    protected $serverDirektur;
    protected $serverUtama;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sinkronisasi/m_sinkronisasi', 'sinc');
        $this->load->model('sinkronisasi/m_detail_sinkronisasi', 'dsinc');
        $this->load->module('sinkronisasi/sinkronisasi', 'sinkronisasi');
        $this->load->config('stpakan');
        $this->serverDirektur = $this->config->item('serverDirektur');
        $this->serverUtama = $this->config->item('serverUtama');
        $this->idFarm = $this->config->item('idFarm');
    }

    public function approve_rilis_rencanadocin($params, $output)
    {
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            /* status approval rencana DOC In tahunan */
            $status = $params['POST']['status'];
            $tahun = $params['POST']['tahun'];
            switch ($status) {
                case 'A':
                /* jika A maka isi data kandang_siklus, log_kandang_siklus_bdy, m_periode
                    :key2 = tahun, :key3 = kode_farm
                */
                $sqlDetail = <<<SQL
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'm_periode' as tabel
						,'{"kode_siklus" : "'+cast(mp.kode_siklus as varchar(10))+'"}' as kunci
						, 1 status_identity
					from m_periode mp
					join m_farm mf
						on mp.kode_farm = mf.kode_farm and mf.GRUP_FARM = 'BDY'
					where left(mp.periode_siklus,4) = :key2 and mp.kode_farm = :key3
				  union all
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'kandang_siklus' as tabel
						,'{"no_reg" : "'+ks.no_reg+'"}' as kunci
						, 0 status_identity
					from kandang_siklus ks
					join m_periode mp
						on mp.kode_farm = ks.kode_farm and mp.kode_siklus = ks.kode_siklus
					where year(ks.tgl_doc_in) = :key2 and ks.kode_std_budidaya is not null and ks.flok_bdy is not null and ks.kode_farm = :key3
					union all
					select distinct :key1 as sinkronisasi
						, 'I' as aksi
						, 'log_kandang_siklus_bdy' as tabel
						,'{"kode_farm" : "'+lks.kode_farm+'" ,"kode_siklus" : "'+cast(lks.kode_siklus as varchar(10))+'","kode_kandang" : "'+kode_kandang+'","no_urut" : "'+cast(lks.no_urut as varchar(5))+'"}' as kunci
						, 0 status_identity
					from log_kandang_siklus_bdy lks
					join m_periode mp
						on mp.kode_siklus = lks.kode_siklus and left(mp.PERIODE_SIKLUS,4) = :key2 and lks.kode_farm = mp.kode_farm
					where lks.kode_farm = :key3
SQL;
                /* cari semua kodefarm yang diupload pada RDIT tahun tersebut */
                $listFarm = $this->db->distinct()
                                        ->select('kode_farm')
                                        ->where('kode_std_budidaya is not null')
                                        ->where('flok_bdy is not null')
                                        ->where('year(tgl_doc_in) = \''.$tahun.'\'')
                                        ->get('kandang_siklus')
                                        ->result_array();
                /* yang melakukan adalah kabag_admin, posisinya di grha jadi server asal di set serverUtama */
                $datatransaksi = array(
                                'transaksi' => 'approve_rilis_rencanadocin',
                                'asal' => $this->idFarm,
                                'tujuan' => null,
                                'aksi' => 'PUSH',
                    );
                $this->db->trans_begin();
                foreach ($listFarm as $lf) {
                    $dataKey = array(':key2' => $tahun, ':key3' => $lf['kode_farm']);
                    $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail, $lf['kode_farm']);
                }
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    log_message('error', 'isi tabel sinkronisasi aksi approve_rilis_rencanadocin gagal pada '.date('Y-m-d H:i:s'));
                } else {
                    $this->db->trans_commit();
                }

                    break;
                default:
            }
        }
    }

    /* update tabel kandang_siklus dan insert ke log_doc_in */
    public function update_tgl_docin($params, $output)
    {
        $kodeFarm = $params['POST']['kodeFarm'];
        $tglDocIn = $params['POST']['tglDocIn']; /* tglDocIn sekarang */
        $tglDocInAsal = $params['POST']['tglDocInAsal'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            $sqlDetail = <<<SQL
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'kandang_siklus' as tabel
					,'{"no_reg" : "'+no_reg+'"}' as kunci
					, 0 status_identity
				from kandang_siklus
				where tgl_doc_in = :key2 and kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'log_doc_in' as tabel
					,'{"no_reg" : "'+ld.no_reg+'", "no_urut" : "'+cast(ld.no_urut as varchar(5))+'"}' as kunci
					, 0 status_identity
				from log_doc_in ld
				join kandang_siklus ks
					on ld.no_reg = ks.no_reg and ks.tgl_doc_in = :key2 and ks.kode_farm = :key3
				where ld.backup_tgl_doc_in = :key4
SQL;
        }
        /* yang melakukan adalah kabag_admin, posisinya di grha jadi server asal di set serverUtama */
        $datatransaksi = array(
                        'transaksi' => 'update_tgl_docin',
                        'asal' => $this->idFarm,
                        'tujuan' => $kodeFarm,
                        'aksi' => 'PUSH',
            );
        $dataKey = array(':key2' => $tglDocIn, ':key3' => $kodeFarm, ':key4' => $tglDocInAsal);
        $this->db->trans_begin();
        $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            log_message('error', 'isi tabel sinkronisasi aksi update_tgl_docin gagal pada '.date('Y-m-d H:i:s'));
        } else {
            $this->db->trans_commit();
        }
    }

    /* update standart budidaya pada tabel kandang_siklus */
    public function update_std_farm($params, $output)
    {
        $kodeFarm = $params['POST']['kodefarm'];
        $tglDocIn = $params['POST']['std']; /* standart budidaya */

        /* output dalam format json */
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            $sqlDetail = <<<SQL
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'kandang_siklus' as tabel
					,'{"no_reg" : "'+no_reg+'"}' as kunci
					, 0 status_identity
				from kandang_siklus
				where kode_farm = :key2 and tgl_doc_in > getdate() + 6
SQL;
        }
        /* yang melakukan adalah kabag_admin, posisinya di grha jadi server asal di set serverUtama */
        $datatransaksi = array(
                        'transaksi' => 'update_std_farm',
                        'asal' => $this->idFarm,
                        'tujuan' => $kodeFarm,
                        'aksi' => 'PUSH',
            );
        $dataKey = array(':key2' => $kodeFarm);
        $this->db->trans_begin();
        $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            log_message('error', 'isi tabel sinkronisasi aksi update_std_farm gagal pada '.date('Y-m-d H:i:s'));
        } else {
            $this->db->trans_commit();
        }
    }

    /* insert tabel cycle_state_transition dan forecast, update m_periode dan kandang_siklus */
    public function approveRejectKonfirmasiDOCIn($params, $output)
    {
        $kodeFarm = $params['POST']['kode_farm'];
        $tgldocin = $params['POST']['tgl_docin'];
        $aksi = $params['POST']['aksi'];
        $user_level = $this->session->userdata('level_user');

        /* output dalam format json */
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            if ($aksi == 'approve') {
                if ($user_level == 'KDV') {
                    $kode_siklus = $this->db->select('no_reg,kode_siklus,flok_bdy')->where(array('kode_farm' => $kodeFarm, 'tgl_doc_in' => $tgldocin))->get('kandang_siklus')->row_array();
                    //$noreg_konfirmasi = $this->db->select('noreg')->where(array('cycle'=>$kode_siklus['kode_siklus'],'flock'=>$kode_siklus['flok_bdy']))->get_compiled_select('cycle_state_transition');
                    $sqlDetail = <<<SQL
						select :key1 as sinkronisasi
								, 'I' as aksi
								, 'cycle_state_transition' as tabel
								,'{"noreg" : "'+noreg+'","flock" : "'+cast(flock as varchar(10))+'","cycle" : "'+cast(cycle as varchar(10))+'","stamp" : "'+left(cast(stamp as date),10)+' '+left(cast(stamp as time),12)+'"}' as kunci
								, 0 status_identity
							from cycle_state_transition
							where cycle = :key2 and flock = :key3
							union all
							select :key1 as sinkronisasi
									, 'U' as aksi
									, 'm_periode' as tabel
									,'{"kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
									, 1 status_identity
							from m_periode
							where kode_siklus = :key2
							union all
							select :key1 as sinkronisasi
									, 'U' as aksi
									, 'kandang_siklus' as tabel
									,'{"no_reg" : "'+no_reg+'"}' as kunci
									, 0 status_identity
							from kandang_siklus
							where no_reg in (select distinct no_reg from cycle_state_transition where cycle = :key2 and flock = :key3 and state = 'RL')
							union all
							select :key1 as sinkronisasi
									, 'I' as aksi
									, 'forecast' as tabel
									,'{"id" : "'+cast(id as varchar(max))+'"}' as kunci
									, 0 status_identity
							from forecast
							where kode_siklus = :key2 and kode_flok_bdy = :key3
SQL;
                    /* yang melakukan adalah kabag_admin, posisinya di grha jadi server asal di set serverUtama */
                    $datatransaksi = array(
                                        'transaksi' => 'approveRejectKonfirmasiDOCIn',
                                        'asal' => $this->idFarm,
                                        'tujuan' => $kodeFarm,
                                        'aksi' => 'PUSH',
                        );
                    $dataKey = array(':key2' => $kode_siklus['kode_siklus'], ':key3' => $kode_siklus['flok_bdy']);
                    $this->db->trans_begin();
                    $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
                    if ($this->db->trans_status() === false) {
                        $this->db->trans_rollback();
                        log_message('error', 'isi tabel sinkronisasi aksi approveRejectKonfirmasiDOCIn gagal pada '.date('Y-m-d H:i:s'));
                    } else {
                        $this->db->trans_commit();
                    }
                }
            }
        }
    }

    /* insert ke tabel do, do_d,do_e, op_vehicle */
    public function simpan_do($params, $output)
    {
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        $no_op = $output_arr['content']['no_op'];
        $kodeFarm = $output_arr['content']['kode_farm'];

        if (empty($no_op)) {
            return;
        }

        $str_op = implode("','", $no_op);
        if ($output_arr['status']) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'do' as tabel
					,'{"no_do" : "'+no_do+'"}' as kunci
					, 0 status_identity
				from do
				where no_op in ('{$str_op}')
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'do_d' as tabel
						,'{"no_do" : "'+no_do+'", "kode_barang" : "'+kode_barang+'"}' as kunci
						, 0 status_identity
					from do_d
					where no_op in ('{$str_op}')
				union all
				select distinct :key1 as sinkronisasi
							, 'I' as aksi
							, 'do_e' as tabel
							,'{"no_do" : "'+de.no_do+'", "kode_barang" : "'+de.kode_barang+'","no_reg" : "'+no_reg+'"}' as kunci
							, 0 status_identity
					from do_e de
					join do_d dd on dd.no_do = de.no_do and dd.no_op in ('{$str_op}')
				union all
				select distinct :key1 as sinkronisasi
							, 'I' as aksi
							, 'op_vehicle' as tabel
							,'{"no_op" : "'+no_op+'","no_urut" : "'+cast(no_urut as varchar(2))+'" , "kode_barang" : "'+kode_barang+'"}' as kunci
							, 0 status_identity
					from op_vehicle
					where no_op in ('{$str_op}')
SQL;
        }

        $datatransaksi = array(
                        'transaksi' => 'simpan_do',
                        'asal' => $this->idFarm,
                        'tujuan' => $kodeFarm,
                        'aksi' => 'PUSH',
            );

        $dataKey = array();
        $this->db->trans_begin();
        $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            log_message('error', 'isi tabel sinkronisasi aksi simpan_do gagal pada '.date('Y-m-d H:i:s'));
        } else {
            $this->db->trans_commit();
        }
    }

    public function approvereject_do($params, $output)
    {
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        $list_op_farm = $output_arr['content'];
        if (!empty($list_op_farm)) {
            $this->db->trans_begin();
            $no_op = array();
            foreach ($list_op_farm as $kf => $op) {
                foreach ($op as $_op) {
                    array_push($no_op, $_op['no_op']);
                }

                $str_op = implode("','", $no_op);
                $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'do' as tabel
					,'{"no_do" : "'+no_do+'"}' as kunci
					, 0 status_identity
				from do
				where no_op in ('{$str_op}')
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'do_d' as tabel
						,'{"no_do" : "'+no_do+'", "kode_barang" : "'+kode_barang+'"}' as kunci
						, 0 status_identity
					from do_d
					where no_op in ('{$str_op}')
				union all
				select distinct :key1 as sinkronisasi
							, 'I' as aksi
							, 'do_e' as tabel
							,'{"no_do" : "'+de.no_do+'", "kode_barang" : "'+de.kode_barang+'","no_reg" : "'+no_reg+'"}' as kunci
							, 0 status_identity
					from do_e de
					join do_d dd on dd.no_do = de.no_do and dd.no_op in ('{$str_op}')
				union all
				select distinct :key1 as sinkronisasi
							, 'I' as aksi
							, 'op_vehicle' as tabel
							,'{"no_op" : "'+no_op+'","no_urut" : "'+cast(no_urut as varchar(2))+'" , "kode_barang" : "'+kode_barang+'"}' as kunci
							, 0 status_identity
					from op_vehicle
					where no_op in ('{$str_op}')
SQL;
                $datatransaksi = array(
                    'transaksi' => 'approve_do',
                    'asal' => $this->idFarm,
                    'tujuan' => $kf,
                    'aksi' => 'PUSH',
                );

                $dataKey = array();
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi approve_do gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* insert atau update ke lpb,lpb_d,lpb_e dan review */
    public function simpan_pp($params, $output)
    {
        $statusLpb = $params['POST']['statusLpb'];
        $statusLpbLama = $params['POST']['_sl'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        $no_pp = $output_arr['content'];
        $ref_id = isset($output_arr['ref_id']) ? $output_arr['ref_id'] : '';
        $sqlDetail = array();
        if ($output_arr['status']) {
            if (!empty($ref_id)) {
                $sqlUpdate = <<<SQL
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'lpb' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}' as kunci
						, 0 status_identity
				from lpb
				where no_lpb = '{$ref_id}'
SQL;
            } else {
                /** hapus lalu insert lagi */
                $sqlUpdate = <<<SQL
				select :key1 as sinkronisasi
						, 'D' as aksi
						, 'review_lpb_budidaya' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}'  as kunci
						, 0 status_identity
				from review_lpb_budidaya
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'D' as aksi
						, 'lpb_e' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}'  as kunci
						, 0 status_identity
				from lpb_e
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'D' as aksi
						, 'lpb_d' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}'  as kunci
						, 0 status_identity
				from lpb_d
				where no_lpb = :key2
				union all 
				select :key1 as sinkronisasi
						, 'D' as aksi
						, 'lpb' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}' as kunci
						, 0 status_identity
				from lpb
				where no_lpb = :key2								
SQL;
            }

            $sqlInsert = <<<SQL
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'lpb' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}' as kunci
						, 0 status_identity
				from lpb
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'lpb_d' as tabel
						,'{"no_lpb" : "'+no_lpb+'","tgl_keb_awal":"'+cast(tgl_keb_awal as varchar(20))+'"}' as kunci
						, 0 status_identity
				from lpb_d
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'lpb_e' as tabel
						,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'"}' as kunci
						, 0 status_identity
				from lpb_e
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'review_lpb_budidaya' as tabel
						,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'"}'  as kunci
						, 0 status_identity
				from review_lpb_budidaya
				where no_lpb = :key2
SQL;

            if ($statusLpb == 'RV') {
                array_push($sqlDetail, $sqlUpdate);
            }
            array_push($sqlDetail, $sqlInsert);

            if (!empty($sqlDetail)) {
                /* yang melakukan adalah kabag_admin, posisinya di grha jadi server asal di set serverUtama */
                $kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb' => $no_pp))->get('lpb')->row_array();
                $datatransaksi = array(
                            'transaksi' => 'simpan_pp',
                            'asal' => $this->idFarm,
                            'tujuan' => $statusLpb == 'N' ? $this->serverUtama : $kodeFarm['kode_farm'],
                            'aksi' => 'PUSH',
                );
                $dataKey = array(':key2' => $no_pp);
                $this->db->trans_begin();
                $this->sinkronisasi->insert($datatransaksi, $dataKey, implode(' union all ', $sqlDetail));
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    log_message('error', 'isi tabel sinkronisasi aksi simpan_pp gagal pada '.date('Y-m-d H:i:s'));
                } else {
                    $this->db->trans_commit();
                }
            }
        }
    }

    /* insert atau update ke lpb,lpb_d,lpb_e dan review */
    public function simpan_pp_v2($params, $output)
    {
        $statusLpb = $params['POST']['statusLpb'];
        if($statusLpb == 'D') return;
        $statusLpbLama = $params['POST']['_sl'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        $no_pp = $output_arr['content'];
        $ref_id = isset($output_arr['ref_id']) ? $output_arr['ref_id'] : '';
        $sqlDetail = array();
        if ($output_arr['status']) {
            if (!empty($ref_id)) {
                $sqlUpdate = <<<SQL
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'lpb' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}' as kunci
					, 0 status_identity
			from lpb
			where no_lpb = '{$ref_id}'
SQL;
            } else {
                /** hapus lalu insert lagi */
                $sqlUpdate = <<<SQL
			select :key1 as sinkronisasi
					, 'D' as aksi
					, 'review_lpb_budidaya' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}'  as kunci
					, 0 status_identity
			from review_lpb_budidaya
			where no_lpb = :key2
			union all
			select :key1 as sinkronisasi
					, 'D' as aksi
					, 'lpb_e' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}'  as kunci
					, 0 status_identity
			from lpb_e
			where no_lpb = :key2
			union all
			select :key1 as sinkronisasi
					, 'D' as aksi
					, 'lpb_d' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}'  as kunci
					, 0 status_identity
			from lpb_d
			where no_lpb = :key2
			union all 
			select :key1 as sinkronisasi
					, 'D' as aksi
					, 'lpb' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}' as kunci
					, 0 status_identity
			from lpb
			where no_lpb = :key2								
SQL;
            }

            $sqlInsert = <<<SQL
			select :key1 as sinkronisasi
					, 'I' as aksi
					, 'lpb' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}' as kunci
					, 0 status_identity
			from lpb
			where no_lpb = :key2
			union all
			select :key1 as sinkronisasi
					, 'I' as aksi
					, 'lpb_d' as tabel
					,'{"no_lpb" : "'+no_lpb+'","tgl_keb_awal":"'+cast(tgl_keb_awal as varchar(20))+'"}' as kunci
					, 0 status_identity
			from lpb_d
			where no_lpb = :key2
			union all
			select :key1 as sinkronisasi
					, 'I' as aksi
					, 'lpb_e' as tabel
					,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'"}' as kunci
					, 0 status_identity
			from lpb_e
			where no_lpb = :key2
			union all
			select :key1 as sinkronisasi
					, 'I' as aksi
					, 'review_lpb_budidaya' as tabel
					,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'"}'  as kunci
					, 0 status_identity
			from review_lpb_budidaya
			where no_lpb = :key2
SQL;

            if ($statusLpb == 'RV') {
                array_push($sqlDetail, $sqlUpdate);
            }
            array_push($sqlDetail, $sqlInsert);

            if (!empty($sqlDetail)) {
                /* yang melakukan adalah kabag_admin, posisinya di grha jadi server asal di set serverUtama */
                $kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb' => $no_pp))->get('lpb')->row_array();
                $datatransaksi = array(
                            'transaksi' => 'simpan_pp_v2',
                            'asal' => $this->idFarm,
                            'tujuan' => $statusLpb == 'N' ? $this->serverUtama : $kodeFarm['kode_farm'],
                            'aksi' => 'PUSH',
                );
                $dataKey = array(':key2' => $no_pp);
                $this->db->trans_begin();
                $this->sinkronisasi->insert($datatransaksi, $dataKey, implode(' union all ', $sqlDetail));
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    log_message('error', 'isi tabel sinkronisasi aksi simpan_pp_v2 gagal pada '.date('Y-m-d H:i:s'));
                } else {
                    $this->db->trans_commit();
                }
            }
        }
    }

    /* insert atau update ke lpb,lpb_d,lpb_e dan review */
    public function reset_review_pp($params, $output)
    {
        $ref_id = $params['POST']['ref_id'];
        //  $no_pp = $params['POST']['no_pp'];
        $review = $params['POST']['review'];
        $reject = $params['POST']['reject'];
        //	$reviewInsert = !empty($review['insert']) ? $review['insert'] : null;
        //	$statusLpb = $params['POST']['statusLpb'];

        /* output dalam format json */
        $output_arr = json_decode($output, true);
        $no_pp = $output_arr['content']; /* no_pp yang baru */
        if ($output_arr['status']) {
            $sqlVoid = '';
            /* update statusnya menjadi V, artinya void gak dipakai, jika statusnya bukan reject */
            if (!$reject) {
                $sqlVoid = <<<sql
			 union all
			 select :key1 as sinkronisasi
					 , 'U' as aksi
					 , 'lpb' as tabel
					 ,'{"no_lpb" : "'+no_lpb+'"}' as kunci
					 , 0 status_identity
			 from lpb
			 where no_lpb = '{$ref_id}'
sql;
            }
            $sqlDetail = <<<SQL
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'lpb' as tabel
				,'{"no_lpb" : "'+no_lpb+'"}' as kunci
				, 0 status_identity
		from lpb
		where no_lpb = :key2
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'lpb_d' as tabel
				,'{"no_lpb" : "'+no_lpb+'","tgl_keb_awal":"'+cast(tgl_keb_awal as varchar(20))+'"}' as kunci
				, 0 status_identity
		from lpb_d
		where no_lpb = :key2
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'lpb_e' as tabel
				,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'"}' as kunci
				, 0 status_identity
		from lpb_e
		where no_lpb = :key2
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'review_lpb_budidaya' as tabel
				,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'"}' as kunci
				, 0 status_identity
		from review_lpb_budidaya
		where no_lpb = :key2
		{$sqlVoid}
SQL;

            $kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb' => $no_pp))->get('lpb')->row_array();
            $datatransaksi = array(
                        'transaksi' => 'reset_review_pp',
                        'asal' => $this->idFarm,
                        'tujuan' => $kodeFarm['kode_farm'],
                        'aksi' => 'PUSH',
            );
            $dataKey = array(':key2' => $no_pp);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi reset_review_pp gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function approve_pp_budidaya($params, $output)
    {
        $list_pp = $params['POST']['no_pp']; /** berbentuk array */
        /* output dalam format json */
        $output_arr = json_decode($output, true);

        if ($output_arr['status']) {
            $this->db->trans_begin();
            foreach ($list_pp as $no_pp) {
                $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'lpb' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}' as kunci
						, 0 status_identity
				from lpb
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'op' as tabel
						,'{"no_lpb" : "'+no_lpb+'","no_op" : "'+no_op+'"}' as kunci
						, 0 status_identity
				from op
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'op_d' as tabel
						,'{"kode_barang" : "'+od.kode_barang+'","no_op" : "'+od.no_op+'"}' as kunci
						, 0 status_identity
				from op
				join op_d od on op.no_op = od.no_op
				where op.no_lpb = :key2
SQL;

                $kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb' => $no_pp))->get('lpb')->row_array();
                $datatransaksi = array(
                                'transaksi' => 'approve_pp_budidaya',
                                'asal' => $this->idFarm,
                                'tujuan' => $kodeFarm['kode_farm'],
                                'aksi' => 'PUSH',
                    );
                $dataKey = array(':key2' => $no_pp);
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi approve_pp_budidaya gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function approve_pp_budidaya_v2($params, $output)
    {
        $list_pp = $params['POST']['no_pp']; /** berbentuk array */
        /* output dalam format json */
        $output_arr = json_decode($output, true);

        if ($output_arr['status']) {
            $this->db->trans_begin();
            foreach ($list_pp as $no_pp) {
                $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'lpb' as tabel
						,'{"no_lpb" : "'+no_lpb+'"}' as kunci
						, 0 status_identity
				from lpb
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'op' as tabel
						,'{"no_lpb" : "'+no_lpb+'","no_op" : "'+no_op+'"}' as kunci
						, 0 status_identity
				from op
				where no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'op_d' as tabel
						,'{"kode_barang" : "'+od.kode_barang+'","no_op" : "'+od.no_op+'"}' as kunci
						, 0 status_identity
				from op
				join op_d od on op.no_op = od.no_op
				where op.no_lpb = :key2
				union all
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'review_lpb_budidaya' as tabel
						,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'"}' as kunci
						, 0 status_identity
				from review_lpb_budidaya
				where no_lpb = :key2
SQL;

                $kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb' => $no_pp))->get('lpb')->row_array();
                $datatransaksi = array(
                                'transaksi' => 'approve_pp_budidaya',
                                'asal' => $this->idFarm,
                                'tujuan' => $kodeFarm['kode_farm'],
                                'aksi' => 'PUSH',
                    );
                $dataKey = array(':key2' => $no_pp);
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi approve_pp_budidaya_v2 gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* update status lpb dan review_lpb_budidaya */
    public function reject_pp_kadiv($params, $output)
    {
        $list_pp = $params['POST']['no_pp']; /** berbentuk array */
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            $this->db->trans_begin();
            foreach ($list_pp as $no_pp) {
                $sqlDetail = <<<SQL
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'lpb' as tabel
					,'{"no_lpb" : "'+no_lpb+'"}' as kunci
					, 0 status_identity
			from lpb
			where no_lpb = :key2
			union all
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'review_lpb_budidaya' as tabel
					,'{"no_lpb" : "'+no_lpb+'","no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'"}' as kunci
					, 0 status_identity
			from review_lpb_budidaya
			where no_lpb = :key2
SQL;
                $kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb' => $no_pp))->get('lpb')->row_array();
                $datatransaksi = array(
                            'transaksi' => 'reject_pp_kadiv',
                            'asal' => $this->idFarm,
                            'tujuan' => $kodeFarm['kode_farm'],
                            'aksi' => 'PUSH',
                );
                $dataKey = array(':key2' => $no_pp);
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi reject_pp_kadiv gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengembalian_sak_simpan($params, $output)
    {
        //	$no_pp = $params['POST']['no_pp'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        $rsk = $output_arr['content']['rsk'];
        $kode_farm = $output_arr['content']['kode_farm'];
        $kode_siklus = $output_arr['content']['kode_siklus'];
        $kode_barang = $output_arr['content']['kode_barang'];
        if ($output_arr['status']) {
            $sqlDetail = <<<SQL
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_sak_kosong' as tabel
				,'{"id" : "'+cast(id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from retur_sak_kosong
		where id = :key2
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_sak_kosong_item_pakan' as tabel
				,'{"id" : "'+cast(rskip.id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from retur_sak_kosong_item_pakan rskip
		join retur_sak_kosong rsk on rsk.id = rskip.retur_sak_kosong
		where rskip.retur_sak_kosong = :key2
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_sak_kosong_item_timbang_pakan' as tabel
				,'{"retur_sak_kosong_item_pakan" : "'+cast(rsktip.retur_sak_kosong_item_pakan as varchar(max))+'","no_urut" : "'+cast(rsktip.no_urut as varchar(5))+'"}' as kunci
				, 0 status_identity
		from retur_sak_kosong_item_timbang_pakan rsktip
		join retur_sak_kosong_item_pakan rskip on rsktip.retur_sak_kosong_item_pakan = rskip.id and rskip.retur_sak_kosong = :key2
		join retur_sak_kosong rsk on rsk.id = rskip.retur_sak_kosong
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'review_hutang_retur_sak' as tabel
				,'{"id" : "'+cast(rhrs.id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from review_hutang_retur_sak rhrs
		where rhrs.retur_sak_kosong = :key2
		union all
		select :key1 as sinkronisasi
				, 'U' as aksi
				, 'glangsing_movement' as tabel
				,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
				, 0 status_identity
		from glangsing_movement gm
		where gm.kode_farm = :key3 and gm.kode_siklus = :key4 and gm.kode_barang = :key5 
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'glangsing_movement_d' as tabel
				,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","keterangan1" : "'+keterangan1+'","keterangan2" : "'+keterangan2+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'","no_referensi" : "'+cast(no_referensi as varchar(max))+'"}' as kunci
				, 0 status_identity
		from glangsing_movement_d gmd
		where gmd.kode_farm = :key3 and gmd.kode_siklus = :key4 and gmd.kode_barang = :key5 and no_referensi = :key2
SQL;
            //	$kodeFarm = $this->db->select('kode_farm')->where(array('no_lpb'=>$no_pp))->get('lpb')->row_array();
            $datatransaksi = array(
                            'transaksi' => 'pengembalian_sak_simpan',
                            'asal' => $this->idFarm,
                            'tujuan' => $this->serverUtama,
                            'aksi' => 'PUSH',
                );
            $dataKey = array(':key2' => $rsk, ':key3' => $kode_farm, ':key4' => $kode_siklus, ':key5' => $kode_barang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi pengembalian_sak_simpan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function approve_retur_sak($params, $output)
    {
        $rsk = $params['POST']['id_retur'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        //	$rsk = $output_arr['content']['rsk'];
        if ($output_arr['status']) {
            $sqlDetail = <<<SQL
		select :key1 as sinkronisasi
				, 'U' as aksi
				, 'review_hutang_retur_sak' as tabel
				,'{"id" : "'+cast(id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from review_hutang_retur_sak
		where retur_sak_kosong = :key2
SQL;
            $kodeFarm = $this->db->select('kode_farm')
                        ->join('kandang_siklus ks', 'ks.no_reg = rhrs.no_reg')
                        ->where(array('rhrs.retur_sak_kosong' => $rsk))
                        ->get('review_hutang_retur_sak rhrs')->row_array();
            $datatransaksi = array(
                            'transaksi' => 'approve_retur_sak',
                            'asal' => $this->idFarm,
                            'tujuan' => $kodeFarm['kode_farm'],
                            'aksi' => 'PUSH',
                );
            $dataKey = array(':key2' => $rsk);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi approve_retur_sak gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function review_retur_sak($params, $output)
    {
        $data = $params['POST']['data'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        //	$rsk = $output_arr['content']['rsk'];
        if ($output_arr['status']) {
            $rskip = array();
            $rskip_awal = '';
            foreach ($data as $d) {
                array_push($rskip, $d['retur_sak_kosong_item_pakan']);
                if (empty($rskip_awal)) {
                    $rskip_awal = $d['retur_sak_kosong_item_pakan'];
                }
            }
            $rskip_str = '(\''.implode('\',\'', $rskip).'\')';
            $sqlDetail = <<<SQL
		select :key1 as sinkronisasi
				, 'U' as aksi
				, 'retur_sak_kosong_item_pakan' as tabel
				,'{"id" : "'+cast(id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from retur_sak_kosong_item_pakan
		where id  in {$rskip_str}
SQL;
            $kodeFarm = $this->db->select('kode_farm')
                        ->join('retur_sak_kosong rsk', 'rsk.id = rskip.retur_sak_kosong')
                        ->join('kandang_siklus ks', 'ks.no_reg = rsk.no_reg')
                        ->where(array('rskip.id' => $rskip_awal))
                        ->get('retur_sak_kosong_item_pakan rskip')->row_array();
            $datatransaksi = array(
                            'transaksi' => 'approve_retur_sak',
                            'asal' => $this->idFarm,
                            'tujuan' => $kodeFarm['kode_farm'],
                            'aksi' => 'PUSH',
                );
            $dataKey = array();
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi approve_retur_sak gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengembalian_pakan_rusak_simpan($params, $output)
    {
        $data = $params['POST']['data'];
        /* output dalam format json */
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            $no_retur = explode('-', $output_arr['content']['no_retur']);
            $noref = 'RP/'.$output_arr['content']['no_retur'];
            $no_reg = $no_retur[0];
            $no_urut = $no_retur[1];
            $sqlDetail = <<<SQL
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_pakan_rusak' as tabel
				,'{"id" : "'+cast(id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from retur_pakan_rusak
		where no_reg = :key2 and no_urut = :key3
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_pakan_rusak_item' as tabel
				,'{"id" : "'+cast(rpri.id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from retur_pakan_rusak_item rpri
		join retur_pakan_rusak rpr on rpr.id = rpri.retur_pakan_rusak and rpr.no_reg = :key2 and rpr.no_urut = :key3
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_pakan_rusak_item_timbang' as tabel
			  ,'{"retur_pakan_rusak_item" : "'+cast(rprit.retur_pakan_rusak_item as varchar(max))+'","no_urut":"'+cast(rprit.no_urut as varchar(10))+'"}' as kunci
				, 0 status_identity
		from retur_pakan_rusak_item_timbang rprit
		join retur_pakan_rusak_item rpri on rpri.id = rprit.retur_pakan_rusak_item
		join retur_pakan_rusak rpr on rpr.id = rpri.retur_pakan_rusak and rpr.no_reg = :key2 and rpr.no_urut = :key3
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'review_penggantian_pakan_rusak' as tabel
				,'{"id" : "'+cast(rppr.id as varchar(max))+'"}' as kunci
				, 0 status_identity
		from review_penggantian_pakan_rusak rppr
		join retur_pakan_rusak rpr on rpr.id = rppr.retur_pakan_rusak and rpr.no_reg = :key2 and rpr.no_urut = :key3
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'kandang_movement_d' as tabel
				,'{"no_reg" : "'+no_reg+'","kode_barang" : "'+kode_barang+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","jenis_kelamin":"'+jenis_kelamin+'","keterangan1":"'+keterangan1+'","keterangan2":"'+keterangan2+'"}' as kunci
				, 0 status_identity
		from kandang_movement_d
		where keterangan1 = 'RETUR PAKAN RUSAK' and keterangan2 = :key4
		union all
		select top 1 :key1 as sinkronisasi
				, 'U' as aksi
				, 'kandang_movement' as tabel
				,'{"no_reg" : "'+kd.no_reg+'","kode_barang" : "'+kd.kode_barang+'","jenis_kelamin":"'+kd.jenis_kelamin+'"}' as kunci
				, 0 status_identity
		from kandang_movement kd
		join kandang_movement_d kmd
			on kd.no_reg = kmd.no_reg and kd.kode_barang = kmd.kode_barang and kd.jenis_kelamin = kmd.jenis_kelamin
		 		and kmd.keterangan1 = 'RETUR PAKAN RUSAK' and kmd.keterangan2 = :key4
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'movement' as tabel
				,'{"kode_farm" : "'+m.kode_farm+'","no_kavling":"'+m.no_kavling+'","no_pallet":"'+m.no_pallet+'","kode_barang" : "'+m.kode_barang+'","jenis_kelamin":"'+m.jenis_kelamin+'"}' as kunci
				, 0 status_identity
		from movement m
		join movement_d md
			on m.kode_farm = md.kode_farm and m.kode_barang = md.kode_barang and m.jenis_kelamin = md.jenis_kelamin
			and m.no_pallet = md.no_pallet and m.no_kavling = md.no_kavling and md.no_referensi = :key4
		where m.no_kavling = 'DMG'
		union all
		select :key1 as sinkronisasi
				, 'I' as aksi
				, 'movement_d' as tabel
				,'{"kode_farm" : "'+md.kode_farm+'","no_kavling":"'+md.no_kavling+'","no_pallet":"'+md.no_pallet+'","kode_barang" : "'+md.kode_barang+'","jenis_kelamin":"'+md.jenis_kelamin+'","keterangan2":"'+md.keterangan2+'","no_referensi":"'+md.no_referensi+'"}' as kunci
				, 0 status_identity
		from movement m
		join movement_d md
			on m.kode_farm = md.kode_farm and m.kode_barang = md.kode_barang and m.jenis_kelamin = md.jenis_kelamin
			and m.no_pallet = md.no_pallet and m.no_kavling = md.no_kavling and md.no_referensi = :key4
		where m.no_kavling = 'DMG'
SQL;

            $datatransaksi = array(
                            'transaksi' => 'pengembalian_pakan_rusak_simpan',
                            'asal' => $this->idFarm,
                            'tujuan' => $this->serverUtama,
                            'aksi' => 'PUSH',
                );
            $dataKey = array(':key2' => $no_reg, ':key3' => $no_urut, ':key4' => $noref);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi pengembalian_pakan_rusak_simpan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_penerimaan_pakan($params, $output)
    {
        $output_arr = json_decode($output, true);
        $no_penerimaan = $output_arr['no_penerimaan'];
        //print_r($params['POST']);
        //print_r($output_arr);
        if ($output_arr['result'] == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'penerimaan' as tabel
					,'{"kode_farm" : "'+p.kode_farm+'", "no_penerimaan" : "'+p.no_penerimaan+'"}' as kunci
					, 0 status_identity
				from penerimaan p
				where p.no_penerimaan = :key2 and p.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'penerimaan_d' as tabel
					,'{"kode_farm" : "'+pd.kode_farm+'", "no_penerimaan" : "'+pd.no_penerimaan+'", "kode_barang" : "'+pd.kode_barang+'"}' as kunci
					, 0 status_identity
				from penerimaan_d pd
				where pd.no_penerimaan = :key2 and pd.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'penerimaan_e' as tabel
					,'{"kode_farm" : "'+pe.kode_farm+'", "no_penerimaan" : "'+pe.no_penerimaan+'", "kode_barang" : "'+pe.kode_barang+'", "no_pallet" : "'+pe.no_pallet+'"}' as kunci
					, 0 status_identity
				from penerimaan_e pe
				where pe.no_penerimaan = :key2 and pe.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'movement' as tabel
					,'{"kode_farm" : "'+m.kode_farm+'", "no_kavling" : "'+m.no_kavling+'", "no_pallet" : "'+m.no_pallet+'", "kode_barang" : "'+m.kode_barang+'", "jenis_kelamin" : "'+m.jenis_kelamin+'", "keterangan1" : "'+m.keterangan1+'"}' as kunci
					, 0 status_identity
				from movement m
				join MOVEMENT_D md
					on m.KODE_FARM = md.KODE_FARM
					and m.NO_KAVLING = md.NO_KAVLING
					and m.NO_PALLET = md.NO_PALLET
					and m.KODE_BARANG = md.KODE_BARANG
					and m.JENIS_KELAMIN = md.JENIS_KELAMIN
				where md.no_referensi = :key2 and md.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'movement_d' as tabel
					,'{"kode_farm" : "'+md.kode_farm+'", "no_kavling" : "'+md.no_kavling+'", "no_pallet" : "'+md.no_pallet+'", "kode_barang" : "'+md.kode_barang+'", "jenis_kelamin" : "'+md.jenis_kelamin+'", "no_referensi" : "'+md.no_referensi+'", "keterangan2" : "'+md.keterangan2+'"}' as kunci
					, 0 status_identity
				from MOVEMENT_D md
				where md.no_referensi = :key2 and md.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'berita_acara' as tabel
					,'{"kode_farm" : "'+ba.kode_farm+'", "no_ba" : "'+ba.no_ba+'", "no_penerimaan" : "'+ba.no_penerimaan+'"}' as kunci
					, 0 status_identity
				from BERITA_ACARA ba
				where ba.no_penerimaan = :key2 and ba.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'berita_acara_d' as tabel
					,'{"kode_farm" : "'+bad.kode_farm+'", "no_ba" : "'+bad.no_ba+'", "kode_barang" : "'+bad.kode_barang+'"}' as kunci
					, 0 status_identity
				from BERITA_ACARA ba
				join BERITA_ACARA_D bad
					on ba.KODE_FARM = bad.KODE_FARM
					and ba.NO_BA = bad.NO_BA
				where ba.no_penerimaan = :key2 and ba.kode_farm = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'penerimaan_pakan',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $no_penerimaan, ':key3' => $this->idFarm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi penerimaan_pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_generate_pengambilan($params, $output)
    {
        $output_arr = json_decode($output, true);
        $no_order = $output_arr['no_pengambilan'];
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'order_kandang' as tabel
					,'{"kode_farm" : "'+ok.kode_farm+'", "no_order" : "'+ok.no_order+'"}' as kunci
					, 0 status_identity
				from order_kandang ok
				where ok.no_order = :key2 and ok.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'order_kandang_d' as tabel
					,'{"kode_farm" : "'+okd.kode_farm+'", "no_order" : "'+okd.no_order+'", "no_reg" : "'+okd.no_reg+'"}' as kunci
					, 0 status_identity
				from order_kandang_d okd
				where okd.no_order = :key2 and okd.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'order_kandang_e' as tabel
					,'{"no_order" : "'+oke.no_order+'", "no_reg" : "'+oke.no_reg+'", "kode_barang" : "'+oke.kode_barang+'", "tgl_kebutuhan" : "'+(left(cast(oke.tgl_kebutuhan as date),10))+'", "jenis_kelamin" : "'+oke.jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from order_kandang ok
				join order_kandang_d okd
					on ok.kode_farm = okd.kode_farm
					and ok.no_order = okd.no_order
				join order_kandang_e oke
					on oke.no_order = okd.no_order
					and oke.no_reg = okd.no_reg
				where ok.no_order = :key2 and ok.kode_farm = :key3
				union all
				select distinct :key1 as sinkronisasi
					, 'U' as aksi
					, 'movement' as tabel
					,'{"kode_farm" : "'+m.kode_farm+'", "no_kavling" : "'+m.no_kavling+'", "no_pallet" : "'+m.no_pallet+'", "kode_barang" : "'+m.kode_barang+'", "jenis_kelamin" : "'+m.jenis_kelamin+'", "keterangan1" : "'+m.keterangan1+'"}' as kunci
					, 0 status_identity
				from MOVEMENT_D md
				join MOVEMENT m
					on m.KODE_FARM = md.KODE_FARM
					and m.NO_KAVLING = md.NO_KAVLING
					and m.NO_PALLET = md.NO_PALLET
					and m.KODE_BARANG = md.KODE_BARANG
					and m.JENIS_KELAMIN = md.JENIS_KELAMIN
				where md.no_referensi = :key2 and md.kode_farm = :key3 and md.keterangan1 = 'PICK'
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'movement_d' as tabel
					,'{"kode_farm" : "'+md.kode_farm+'", "no_kavling" : "'+md.no_kavling+'", "no_pallet" : "'+md.no_pallet+'", "kode_barang" : "'+md.kode_barang+'", "jenis_kelamin" : "'+md.jenis_kelamin+'", "no_referensi" : "'+md.no_referensi+'", "keterangan2" : "'+md.keterangan2+'"}' as kunci
					, 0 status_identity
				from MOVEMENT_D md
				where md.no_referensi = :key2 and md.kode_farm = :key3 and md.keterangan1 = 'PICK'
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'log_hutang_sak_kosong' as tabel
					,'{"no_order" : "'+lh.no_order+'", "kode_farm" : "'+lh.kode_farm+'"}' as kunci
					, 0 status_identity
				from log_hutang_sak_kosong lh
				where lh.no_order = :key2 and lh.kode_farm = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'generate_pengambilan_barang',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $no_order, ':key3' => $this->idFarm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi generate_pengambilan_barang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_pengambilan_barang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);
        $data = $params['POST']['data'];
        $sqlDetail = array();

        if ($output_arr['result'] == 1) {
            foreach ($data as $key => $value) {
                $no_order = $value['no_order'];
                $no_kavling = $value['id_kavling'];
                $no_pallet = $value['no_pallet'];
                $kode_barang = $value['kode_barang'];
                $jenis_kelamin = $value['jenis_kelamin'];
                $no_reg = $value['no_reg'];
                $kode_flok = $value['kode_flok'];
                $sqlDetail[] = <<<SQL
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'order_kandang' as tabel
						,'{"kode_farm" : "'+ok.kode_farm+'", "no_order" : "'+ok.no_order+'"}' as kunci
						, 0 status_identity
					from order_kandang ok
					where ok.kode_farm = :key2
					and ok.no_order = '$no_order'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'order_kandang_d' as tabel
						,'{"kode_farm" : "'+okd.kode_farm+'", "no_reg" : "'+okd.no_reg+'", "no_order" : "'+okd.no_order+'"}' as kunci
						, 0 status_identity
					from order_kandang_d okd
					where okd.kode_farm = :key2
					and okd.no_order = '$no_order'
					and okd.no_reg = '$no_reg'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'order_kandang_e' as tabel
						,'{"kode_barang" : "'+oke.kode_barang+'","tgl_kebutuhan":"'+cast(oke.tgl_kebutuhan as varchar(20))+'","no_reg" : "'+oke.no_reg+'", "no_order" : "'+oke.no_order+'"}' as kunci
						, 0 status_identity
					from order_kandang_e oke
					where oke.no_order = '$no_order'
					and oke.no_reg = '$no_reg'
					and oke.kode_barang = '$kode_barang'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'pick_d' as tabel
						,'{"kode_farm" : "'+pd.kode_farm+'", "no_kavling" : "'+pd.no_kavling+'", "no_pallet" : "'+pd.no_pallet+'", "kode_barang" : "'+pd.kode_barang+'", "no_reg" : "'+pd.no_reg+'", "no_order" : "'+pd.no_order+'"}' as kunci
						, 0 status_identity
					from pick_d pd
					where pd.kode_farm = :key2
					and pd.no_kavling = '$no_kavling'
					and pd.no_pallet = '$no_pallet'
					and pd.kode_barang = '$kode_barang'
					and pd.no_order = '$no_order'
					and pd.no_reg = '$no_reg'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'movement' as tabel
						,'{"kode_farm" : "'+m.kode_farm+'", "no_kavling" : "'+m.no_kavling+'", "no_pallet" : "'+m.no_pallet+'", "kode_barang" : "'+m.kode_barang+'", "jenis_kelamin" : "'+m.jenis_kelamin+'", "keterangan1" : "'+m.keterangan1+'","kode_pallet" : "'+m.KODE_PALLET+'"}' as kunci
						, 0 status_identity
					from MOVEMENT m
					where m.kode_farm = :key2
					and m.no_kavling = '$no_kavling'
					and m.no_pallet = '$no_pallet'
					and m.kode_barang = '$kode_barang'
					and m.jenis_kelamin = '$jenis_kelamin'
					and m.keterangan1 = '$kode_flok'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'movement_d' as tabel
						,'{"kode_farm" : "'+md.kode_farm+'", "no_kavling" : "'+md.no_kavling+'", "no_pallet" : "'+md.no_pallet+'", "kode_barang" : "'+md.kode_barang+'", "jenis_kelamin" : "'+md.jenis_kelamin+'", "no_referensi" : "'+md.no_referensi+'", "keterangan2" : "'+md.keterangan2+'","kode_pallet" : "'+md.KODE_PALLET+'"}' as kunci
						, 0 status_identity
					from MOVEMENT_D md
					where md.no_referensi = '$no_order'
					and md.kode_farm = :key2
					and md.no_kavling = '$no_kavling'
					and md.no_pallet = '$no_pallet'
					and md.kode_barang = '$kode_barang'
					and md.jenis_kelamin = '$jenis_kelamin'
					and md.keterangan2 = '$no_reg'
					union all
					select distinct :key1 as sinkronisasi
						, 'U' as aksi
						, 'penerimaan_kandang' as tabel
						,'{"no_reg" : "'+pk.no_reg+'", "no_penerimaan_kandang" : "'+pk.no_penerimaan_kandang+'", "no_order" : "'+pk.no_order+'"}' as kunci
						, 0 status_identity
					from penerimaan_kandang pk
					where pk.no_order = '$no_order'
					and pk.no_reg = '$no_reg'
					and pk.tgl_terima = cast(getdate() as date)
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'penerimaan_kandang_d' as tabel
						,'{"no_reg" : "'+pk.no_reg+'", "no_penerimaan_kandang" : "'+pk.no_penerimaan_kandang+'","kode_barang" :"'+pkd.kode_barang+'"}' as kunci
						, 0 status_identity
					from penerimaan_kandang_d pkd
					join penerimaan_kandang pk
						on pk.no_penerimaan_kandang = pkd.no_penerimaan_kandang
						and pk.no_reg = pkd.no_reg
					where pk.no_order = '$no_order'
					and pk.no_reg = '$no_reg'
					and pkd.kode_barang = '$kode_barang'
					and pkd.jenis_kelamin = '$jenis_kelamin'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'kandang_movement' as tabel
						,'{"no_reg" : "'+km.no_reg+'", "kode_barang" : "'+km.kode_barang+'", "jenis_kelamin" : "'+km.jenis_kelamin+'"}' as kunci
						, 0 status_identity
					from kandang_movement km
					where km.no_reg = '$no_reg'
					and km.kode_barang = '$kode_barang'
					and km.jenis_kelamin = '$jenis_kelamin'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'kandang_movement_d' as tabel
						,'{"no_reg" : "'+kmd.no_reg+'", "kode_barang" : "'+kmd.kode_barang+'", "tgl_transaksi" : "'+left(cast(kmd.tgl_transaksi as date),10)+'", "jenis_kelamin" : "'+kmd.jenis_kelamin+'", "keterangan1" : "'+kmd.keterangan1+'", "keterangan2" : "'+kmd.keterangan2+'"}' as kunci
						, 0 status_identity
					from kandang_movement_d kmd
					where kmd.no_reg = '$no_reg'
					and kmd.kode_barang = '$kode_barang'
					and kmd.jenis_kelamin = '$jenis_kelamin'
					and kmd.tgl_transaksi = cast(getdate() as date)
					and kmd.keterangan1 = 'PENERIMAAN KANDANG'

SQL;
            }

            //echo $sqlDetail;

            $datatransaksi = array(
                'transaksi' => 'konfirmasi_pengambilan_barang',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );
            $sqlDetail = implode($sqlDetail, ' union all ');
            $dataKey = array(':key2' => $this->idFarm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi konfirmasi_pengambilan_barang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_review_pakan_rusak($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $no_reg = $params['POST']['no_reg'];
        $no_urut = $params['POST']['no_urut'];

        if ($output_arr['result'] == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'review_penggantian_pakan_rusak' as tabel
					,'{"id" : "'+cast(rppr.id as varchar(max))+'"}' as kunci
					, 0 status_identity
				from review_penggantian_pakan_rusak rppr
				join retur_pakan_rusak rpr
					on rpr.id = rppr.retur_pakan_rusak
					and rpr.no_reg = rppr.no_reg
				where rpr.no_reg = :key2 and rpr.no_urut = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'review_penggantian_pakan_rusak',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $no_reg, ':key3' => $no_urut);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi review_penggantian_pakan_rusak gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_mutasi($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $no_mutasi = $output_arr['no_mutasi'];

        if ($output_arr['result'] == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'mutasi_pakan' as tabel
					,'{"no_mutasi" : "'+mp.no_mutasi+'"}' as kunci
					, 0 status_identity
				from mutasi_pakan mp
				where mp.no_mutasi = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'mutasi_pakan_d' as tabel
					,'{"no_mutasi" : "'+mpd.no_mutasi+'", "no_reg_tujuan" : "'+mpd.no_reg_tujuan+'"}' as kunci
					, 0 status_identity
				from mutasi_pakan_d mpd
				where mpd.no_mutasi = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'review_mutasi_pakan' as tabel
					,'{"id" : "'+cast(rmp.id as varchar(max))+'"}' as kunci
					, 0 status_identity
				from review_mutasi_pakan rmp
				where rmp.no_mutasi = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'mutasi_pakan',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $no_mutasi);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi mutasi_pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function review_mutasi_pakan($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $no_mutasi = $params['POST']['no_mutasi'];
        $keputusan = $params['POST']['keputusan'];
        $alasan = $params['POST']['alasan'];
        $user = $this->session->userdata('kode_user');

        if (!empty($output_arr['keputusan'])) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'review_mutasi_pakan' as tabel
					,'{"id" : "'+cast(rmp.id as varchar(max))+'"}' as kunci
					, 0 status_identity
				from review_mutasi_pakan rmp
				where rmp.no_mutasi = :key2
				and rmp.keputusan = :key3
				and rmp.alasan = :key4

SQL;

            $datatransaksi = array(
                    'transaksi' => 'review_mutasi_pakan',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $no_mutasi, ':key3' => $keputusan, ':key4' => $alasan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi review_mutasi_pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_periode_siklus($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $kode_farm = $params['POST']['kodefarm'];
        $kode_strain = $params['POST']['kodestrain'];
        $periode_siklus = $params['POST']['periodesiklus'];

        if ($output_arr['result'] == 'success') {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_periode' as tabel
					,'{"kode_siklus" : "'+cast(mp.kode_siklus as varchar(max))+'"}' as kunci
					, 1 status_identity
				from m_periode mp
				where mp.kode_farm = :key2
				and mp.periode_siklus = :key3
				and mp.kode_strain = :key4

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_periode_siklus',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_farm, ':key3' => $periode_siklus, ':key4' => $kode_strain);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_periode_siklus gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_periode_siklus($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $kode_farm = $params['POST']['kodefarm'];
        $kode_strain = $params['POST']['kodestrain'];
        $periode_siklus = $params['POST']['periodesiklus'];

        if ($output_arr['result'] == 'success') {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_periode' as tabel
					,'{"kode_siklus" : "'+cast(mp.kode_siklus as varchar(max))+'"}' as kunci
					, 1 status_identity
				from m_periode mp
				where mp.kode_farm = :key2
				and mp.periode_siklus = :key3
				and mp.kode_strain = :key4

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_periode_siklus',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_farm, ':key3' => $periode_siklus, ':key4' => $kode_strain);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_periode_siklus gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_ekspedisi($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']['data']);
        //print_r($output_arr);

        if ($output_arr['success'] == 1) {
            $kode_ekspedisi = $output_arr['kode_ekspedisi'];

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_ekspedisi' as tabel
					,'{"kode_ekspedisi" : "'+me.kode_ekspedisi+'"}' as kunci
					, 0 status_identity
				from m_ekspedisi me
				where me.kode_ekspedisi = :key2
			/*	union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_ekpedisi_vehicle' as tabel
					,'{"kode_ekspedisi" : "'+mev.kode_ekspedisi+'", "no_kendaraan" : "'+mev.no_kendaraan+'"}' as kunci
					, 0 status_identity
				from m_ekpedisi_vehicle mev
				where mev.kode_ekspedisi = :key2
			*/				

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_ekspedisi',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_ekspedisi);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_ekspedisi gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_ekspedisi($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']['data']);
        //print_r($output_arr);

        if ($output_arr['success'] == 1) {
            $kode_ekspedisi = $output_arr['kode_ekspedisi'];

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_ekspedisi' as tabel
					,'{"kode_ekspedisi" : "'+me.kode_ekspedisi+'"}' as kunci
					, 0 status_identity
				from m_ekspedisi me
				where me.kode_ekspedisi = :key2
			/*	union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_ekpedisi_vehicle' as tabel
					,'{"kode_ekspedisi" : "'+mev.kode_ekspedisi+'", "no_kendaraan" : "'+mev.no_kendaraan+'"}' as kunci
					, 0 status_identity
				from m_ekpedisi_vehicle mev
				where mev.kode_ekspedisi = :key2
			*/
SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_ekspedisi',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_ekspedisi);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_ekspedisi gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_uom($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $uom = $params['POST']['satuan'];

        if ($output_arr['result'] == 'success') {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_uom' as tabel
					,'{"uom" : "'+mu.uom+'"}' as kunci
					, 0 status_identity
				from m_uom mu
				where mu.uom = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_uom',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $uom);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_uom gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_uom($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $uom = $params['POST']['satuan'];

        if ($output_arr['result'] == 'success') {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_uom' as tabel
					,'{"uom" : "'+mu.uom+'"}' as kunci
					, 0 status_identity
				from m_uom mu
				where mu.uom = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_uom',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $uom);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_uom gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_op_marketing($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $tanggal_kirim = date('Y-m-d', strtotime(convert_month($params['POST']['tanggal_kirim'], 2)));

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_op' as tabel
					,'{"tgl_kirim" : "'+left(mo.tgl_kirim,10)+'"}' as kunci
					, 0 status_identity
				from m_op mo
				where mo.tgl_kirim = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_op',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $tanggal_kirim);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_op gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_op_marketing($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $tanggal_kirim = date('Y-m-d', strtotime(convert_month($params['POST']['tanggal_kirim'], 2)));

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_op' as tabel
					,'{"tgl_kirim" : "'+left(mo.tgl_kirim,10)+'"}' as kunci
					, 0 status_identity
				from m_op mo
				where mo.tgl_kirim = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_op',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $tanggal_kirim);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_op gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_harga_barang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $tanggal_berlaku = date('Y-m-d', strtotime(convert_month($params['POST']['tanggal_berlaku'], 2)));
            $pelanggan = $params['POST']['pelanggan'];
            $kode_barang = $params['POST']['kode_barang'];
            $satuan = $params['POST']['satuan'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'harga_barang' as tabel
					,'{"kode_pelanggan" : "'+hb.kode_pelanggan+'", "kode_barang" : "'+hb.kode_barang+'", "uom" : "'+hb.uom+'", "tgl_berlaku" : "'+left(hb.tgl_berlaku,10)+'"}' as kunci
					, 0 status_identity
				from harga_barang hb
				where hb.kode_pelanggan = :key2
				and hb.kode_barang = :key3
				and hb.uom = :key4
				and hb.tgl_berlaku = :key5

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_harga_barang',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $pelanggan, ':key3' => $kode_barang, ':key4' => $satuan, ':key5' => $tanggal_berlaku);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_harga_barang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_harga_barang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $tanggal_berlaku = date('Y-m-d', strtotime(convert_month($params['POST']['tanggal_berlaku'], 2)));
            $pelanggan = $params['POST']['pelanggan'];
            $kode_barang = $params['POST']['kode_barang'];
            $satuan = $params['POST']['satuan'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'harga_barang' as tabel
					,'{"kode_pelanggan" : "'+hb.kode_pelanggan+'", "kode_barang" : "'+hb.kode_barang+'", "uom" : "'+hb.uom+'", "tgl_berlaku" : "'+left(hb.tgl_berlaku,10)+'"}' as kunci
					, 0 status_identity
				from harga_barang hb
				where hb.kode_pelanggan = :key2
				and hb.kode_barang = :key3
				and hb.uom = :key4
				and hb.tgl_berlaku = :key5

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_harga_barang',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $pelanggan, ':key3' => $kode_barang, ':key4' => $satuan, ':key5' => $tanggal_berlaku);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_harga_barang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_barang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodebarang = $params['POST']['kodebarang'];
            $jenisgrupbarang = $params['POST']['jenisgrupbarang'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_barang' as tabel
					,'{"kode_barang" : "'+mb.kode_barang+'"}' as kunci
					, 0 status_identity
				from m_barang mb
				where mb.kode_barang = :key2
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_grup_barang' as tabel
					,'{"grup_barang" : "'+cast(mgb.grup_barang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_grup_barang mgb
				where mgb.grup_barang = :key3

SQL;
            //echo $sqlDetail;
            $datatransaksi = array(
                    'transaksi' => 'master_barang',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodebarang, ':key3' => $jenisgrupbarang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_barang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_barang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodebarang = $params['POST']['kodebarang'];
            $jenisgrupbarang = $params['POST']['jenisgrupbarang'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_barang' as tabel
					,'{"kode_barang" : "'+mb.kode_barang+'"}' as kunci
					, 0 status_identity
				from m_barang mb
				where mb.kode_barang = :key2
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_grup_barang' as tabel
					,'{"grup_barang" : "'+cast(mgb.grup_barang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_grup_barang mgb
				where mgb.grup_barang = :key3

SQL;

            //echo $sqlDetail;
            $datatransaksi = array(
                    'transaksi' => 'master_barang',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodebarang, ':key3' => $jenisgrupbarang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_barang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_farm($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodefarm = $params['POST']['kodefarm'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_farm' as tabel
					,'{"kode_farm" : "'+mf.kode_farm+'"}' as kunci
					, 0 status_identity
				from m_farm mf
				where mf.kode_farm = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_farm',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodefarm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_farm gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_farm($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodefarm = $params['POST']['kodefarm'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_farm' as tabel
					,'{"kode_farm" : "'+mf.kode_farm+'"}' as kunci
					, 0 status_identity
				from m_farm mf
				where mf.kode_farm = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_farm',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodefarm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_farm gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_gudang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodefarm = $params['POST']['kodefarm'];
            $kodegudang = $params['POST']['kodegudang'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_gudang' as tabel
					,'{"kode_farm" : "'+mg.kode_farm+'", "kode_gudang" : "'+mg.kode_gudang+'"}' as kunci
					, 0 status_identity
				from m_gudang mg
				where mg.kode_farm = :key2
				and mg.kode_gudang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_gudang',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodefarm, ':key3' => $kodegudang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_gudang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_gudang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodefarm = $params['POST']['kodefarm'];
            $kodegudang = $params['POST']['kodegudang'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_gudang' as tabel
					,'{"kode_farm" : "'+mg.kode_farm+'", "kode_gudang" : "'+mg.kode_gudang+'"}' as kunci
					, 0 status_identity
				from m_gudang mg
				where mg.kode_farm = :key2
				and mg.kode_gudang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_gudang',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodefarm, ':key3' => $kodegudang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_gudang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_kandang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodefarm = $params['POST']['kodefarm'];
            $kodekandang = $params['POST']['kodekandang'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_kandang' as tabel
					,'{"kode_farm" : "'+mk.kode_farm+'", "kode_kandang" : "'+mk.kode_kandang+'"}' as kunci
					, 0 status_identity
				from m_kandang mk
				where mk.kode_farm = :key2
				and mk.kode_kandang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_kandang',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodefarm, ':key3' => $kodekandang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_kandang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_kandang($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodefarm = $params['POST']['kodefarm'];
            $kodekandang = $params['POST']['kodekandang'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_kandang' as tabel
					,'{"kode_farm" : "'+mk.kode_farm+'", "kode_kandang" : "'+mk.kode_kandang+'"}' as kunci
					, 0 status_identity
				from m_kandang mk
				where mk.kode_farm = :key2
				and mk.kode_kandang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_kandang',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodefarm, ':key3' => $kodekandang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_kandang gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_kavling($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kode_farm = $params['POST']['kode_farm'];
            $kode_gudang = $params['POST']['kode_gudang'];
            $kolom1 = $params['POST']['kolom1'];
            $kolom2 = $params['POST']['kolom2'];
            $step = $params['POST']['step'];
            $baris = $params['POST']['baris'];
            $nomorposisi = $params['POST']['nomorposisi'];
            $sqlDetail = '';

            for ($i = $kolom1; $i <= $kolom2; $i += $step) {
                if ($i > $kolom1) {
                    $sqlDetail .= ' union all ';
                }

                $no_kavling = $baris.$nomorposisi.'-'.str_pad($i, 2, '0', STR_PAD_LEFT);

                $sqlDetail .= <<<SQL
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'm_kavling' as tabel
						,'{"kode_farm" : "'+mk.kode_farm+'", "kode_gudang" : "'+mk.kode_gudang+'", "no_kavling" : "'+mk.no_kavling+'"}' as kunci
						, 0 status_identity
					from m_kavling mk
					where mk.kode_farm = :key2
					and mk.kode_gudang = :key3
					and mk.no_kavling = '$no_kavling'
					union
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'm_pallet' as tabel
						,'{"kode_farm" : "'+mp.kode_farm+'", "kode_gudang" : "'+mp.kode_gudang+'", "no_kavling" : "'+mp.no_kavling+'","kode_pallet" : "'+mp.kode_pallet+'"}' as kunci
						, 0 status_identity
					from m_pallet mp
					where mp.kode_farm = :key2
					and mp.kode_gudang = :key3
					and mp.no_kavling = '$no_kavling'
SQL;
            }
            //echo $sqlDetail;
            $datatransaksi = array(
                    'transaksi' => 'master_kavling',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_farm, ':key3' => $kode_gudang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_kavling gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_kavling($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kode_farm = $params['POST']['kode_farm'];
            $kode_gudang = $params['POST']['kode_gudang'];
            $kolom1 = $params['POST']['kolom1'];
            $no_baris = $params['POST']['baris'];
            $no_posisi = $params['POST']['nomorposisi'];
            $layout_posisi = $params['POST']['namaposisi'];

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_kavling' as tabel
					,'{"kode_farm" : "'+mk.kode_farm+'", "kode_gudang" : "'+mk.kode_gudang+'", "no_kavling" : "'+mk.no_kavling+'"}' as kunci
					, 0 status_identity
				from m_kavling mk
				where mk.kode_farm = :key2
				and mk.kode_gudang = :key3
				and mk.no_baris = '$no_baris'
				and mk.no_posisi = '$no_posisi'
				and mk.layout_posisi = '$layout_posisi'
				and mk.no_kolom = '$kolom1'

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_kavling',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_farm, ':key3' => $kode_gudang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_kavling gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_pelanggan($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodepelanggan = $params['POST']['kodepelanggan'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_pelanggan' as tabel
					,'{"kode_pelanggan" : "'+mp.kode_pelanggan+'"}' as kunci
					, 0 status_identity
				from m_pelanggan mp
				where mp.kode_pelanggan = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_pelanggan',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodepelanggan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_pelanggan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_pelanggan($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodepelanggan = $params['POST']['kodepelanggan'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_pelanggan' as tabel
					,'{"kode_pelanggan" : "'+mp.kode_pelanggan+'"}' as kunci
					, 0 status_identity
				from m_pelanggan mp
				where mp.kode_pelanggan = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_pelanggan',
                    'asal' => $this->idFarm,
                    'tujuan' => '*', //$this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kodepelanggan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_pelanggan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function add_pengawas($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodepengawas = $params['POST']['kodepengawas'];
            $list_farm = $params['POST']['list_farm'];
            $this->db->trans_begin();
            foreach ($list_farm as $lf) {
                $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_pegawai' as tabel
					,'{"kode_pegawai" : "'+mp.kode_pegawai+'"}' as kunci
					, 0 status_identity
				from m_pegawai mp
				where mp.kode_pegawai = :key2
				union all 
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'pegawai_d' as tabel
					,'{"kode_pegawai" : "'+mp.kode_pegawai+'", "kode_farm" : "'+mp.kode_farm+'"}' as kunci
					, 0 status_identity
				from pegawai_d mp
				where mp.kode_pegawai = :key2 and mp.kode_farm = '{$lf}'
SQL;

                $datatransaksi = array(
                    'transaksi' => 'master_pegawai',
                    'asal' => $this->serverUtama,
                    'tujuan' => $lf,
                    'aksi' => 'PUSH',
                );

                $dataKey = array(':key2' => $kodepengawas);
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_pegawai gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function update_pengawas($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        if ($output_arr['result'] == 'success') {
            $kodepengawas = $params['POST']['kodepengawas'];
            $list_farm = $params['POST']['list_farm'];
            $this->db->trans_begin();
            foreach ($list_farm as $lf) {
                $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_pegawai' as tabel
					,'{"kode_pegawai" : "'+mp.kode_pegawai+'"}' as kunci
					, 0 status_identity
				from m_pegawai mp
				where mp.kode_pegawai = :key2

SQL;

                $datatransaksi = array(
                    'transaksi' => 'update_pegawai',
                    'asal' => $this->serverUtama,
                    'tujuan' => $lf,
                    'aksi' => 'PUSH',
                );

                $dataKey = array(':key2' => $kodepengawas);
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_pegawai gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_std_budidaya($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);
        if ($output_arr['result'] == 'success') {
            $kode_std_budidaya = $output_arr['kode_std_budidaya'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_std_budidaya' as tabel
					,'{"kode_std_budidaya" : "'+msd.kode_std_budidaya+'"}' as kunci
					, 0 status_identity
				from m_std_budidaya msd
				where msd.kode_std_budidaya = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_std_budidaya_d' as tabel
					,'{"kode_std_budidaya" : "'+msdd.kode_std_budidaya+'", "std_umur" : "'+cast(msdd.std_umur as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_std_budidaya_d msdd
				where msdd.kode_std_budidaya = :key2

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_standart_budidaya',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_std_budidaya);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_standart_budidaya gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_lhk_bdy($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            $noreg = $output_arr['no_reg'];
            $tgl_lhk = $output_arr['tgl_lhk'];
            //	$tutup_siklus = $params['POST']['tutup_siklus']; /* harus Y */
            $tutup_siklus = '';
            $sqlTutupSiklus = '';
            if ($tutup_siklus == 'Y') {
                $sqlTutupSiklus = <<<SQL
			union all
			select :key1 as sinkronisasi
				, 'U' as aksi
				, 'kandang_siklus' as tabel
				,'{"no_reg" : "'+no_reg+'"}' as kunci
				, 0 status_identity
			from kandang_siklus
			where no_reg = :key2
SQL;
            }

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'rhk' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'"}' as kunci
					, 0 status_identity
				from rhk
				where no_reg = :key2 and tgl_transaksi = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'rhk_pakan' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","kode_barang":"'+kode_barang+'","jenis_kelamin":"'+jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from rhk_pakan
				where no_reg = :key2 and tgl_transaksi = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'rhk_penimbangan_bb' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","sekat":"'+cast(sekat as varchar(10))+'","jenis_kelamin":"'+jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from rhk_penimbangan_bb
				where no_reg = :key2 and tgl_transaksi = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'kandang_movement' as tabel
					,'{"no_reg" : "'+km.no_reg+'","kode_barang":"'+km.kode_barang+'","jenis_kelamin":"'+km.jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from kandang_movement km
				join kandang_movement_d kmd on kmd.no_reg = km.no_reg and kmd.kode_barang = km.kode_barang
					and kmd.jenis_kelamin = km.jenis_kelamin and kmd.tgl_transaksi = :key3 and kmd.no_reg = :key2 and kmd.keterangan1 = 'LHK'
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'kandang_movement_d' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","kode_barang":"'+kode_barang+'","jenis_kelamin":"'+jenis_kelamin+'","keterangan1" : "LHK"}' as kunci
					, 0 status_identity
				from kandang_movement_d
				where no_reg = :key2 and tgl_transaksi = :key3 and keterangan1 = 'LHK'
				union all 
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'rhk_rekomendasi_pakan' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'","kode_barang":"'+kode_barang+'"}' as kunci
					, 0 status_identity
				from rhk_rekomendasi_pakan
				where no_reg = :key2 and tgl_transaksi = :key3
SQL;

            $datatransaksi = array(
                    'transaksi' => 'simpan_lhk_bdy',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $noreg, ':key3' => $tgl_lhk);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi simpan_lhk_bdy gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function lhk_buat_pengajuan_retur($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['result'] == 'success') {
            $noreg = $params['POST']['noreg'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'retur_kandang' as tabel
					,'{"no_reg" : "'+no_reg+'","no_retur":"'+no_retur+'"}' as kunci
					, 0 status_identity
				from retur_kandang
				where no_reg = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'retur_kandang_d' as tabel
					,'{"no_reg" : "'+no_reg+'","no_retur":"'+no_retur+'","kode_barang":"'+kode_barang+'"}' as kunci
					, 0 status_identity
				from retur_kandang_d
				where no_reg = :key2
				
SQL;

            $datatransaksi = array(
                    'transaksi' => 'lhk_buat_pengajuan_retur',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $noreg);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi lhk_buat_pengajuan_retur gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function lhk_simpan_ack($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['msg'] == 'success') {
            $noreg = $params['POST']['no_reg'];
            $tgl_transaksi = $params['POST']['tgl_transaksi'];

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'rhk' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'"}' as kunci
					, 0 status_identity
				from rhk
				where no_reg = :key2 and tgl_transaksi = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'rhk_pakan' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","kode_barang":"'+kode_barang+'","jenis_kelamin":"'+jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from rhk_pakan
				where no_reg = :key2 and tgl_transaksi = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'rhk_penimbangan_bb' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","sekat":"'+cast(sekat as varchar(10))+'","jenis_kelamin":"'+jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from rhk_penimbangan_bb
				where no_reg = :key2 and tgl_transaksi = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'kandang_movement' as tabel
					,'{"no_reg" : "'+km.no_reg+'","kode_barang":"'+km.kode_barang+'","jenis_kelamin":"'+km.jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from kandang_movement km
				join kandang_movement_d kmd on kmd.no_reg = km.no_reg and kmd.kode_barang = km.kode_barang
					and kmd.jenis_kelamin = km.jenis_kelamin and kmd.tgl_transaksi = :key3 and kmd.no_reg = :key2 and kmd.keterangan1 = 'LHK'
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'kandang_movement_d' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","kode_barang":"'+kode_barang+'","jenis_kelamin":"'+jenis_kelamin+'","keterangan1" : "LHK"}' as kunci
					, 0 status_identity
				from kandang_movement_d
				where no_reg = :key2 and tgl_transaksi = :key3 and keterangan1 = 'LHK'
SQL;

            $datatransaksi = array(
                    'transaksi' => 'lhk_simpan_ack',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $noreg, ':key3' => $tgl_transaksi);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi lhk_simpan_ack gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function lhk_simpan_kadep($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['msg'] == 'success') {
            $noreg = $params['POST']['no_reg'];
            $tgl_transaksi = $params['POST']['tgl_transaksi'];

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'rhk' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'"}' as kunci
					, 0 status_identity
				from rhk
				where no_reg = :key2 and tgl_transaksi = :key3
SQL;
            $kodeFarm = $this->db->select('kode_farm')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
            $datatransaksi = array(
                    'transaksi' => 'lhk_simpan_kadep',
                    'asal' => $this->idFarm,
                    'tujuan' => $kodeFarm['kode_farm'],
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $noreg, ':key3' => $tgl_transaksi);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi lhk_simpan_kadep gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function lhk_simpan_kadiv($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['msg'] == 'success') {
            $noreg = $params['POST']['no_reg'];
            $tgl_transaksi = $params['POST']['tgl_transaksi'];

            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'rhk' as tabel
					,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'"}' as kunci
					, 0 status_identity
				from rhk
				where no_reg = :key2 and tgl_transaksi = :key3
SQL;
            $kodeFarm = $this->db->select('kode_farm')->where(array('no_reg' => $noreg))->get('kandang_siklus')->row_array();
            $datatransaksi = array(
                    'transaksi' => 'lhk_simpan_kadep',
                    'asal' => $this->idFarm,
                    'tujuan' => $kodeFarm['kode_farm'],
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $noreg, ':key3' => $tgl_transaksi);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi lhk_simpan_kadep gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function realisasi_panen($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['result'] == 'success') {
            $no_surat_jalan = $params['POST']['no_sj'];
            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'realisasi_panen' as tabel
					,'{"no_reg" : "'+no_reg+'","no_surat_jalan":"'+no_surat_jalan+'"}' as kunci
					, 0 status_identity
				from realisasi_panen
				where no_surat_jalan = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'realisasi_panen_tara_keranjang' as tabel
					,'{"no_surat_jalan" : "'+no_surat_jalan+'","no_urut":"'+cast(no_urut as varchar(20))+'"}' as kunci
					, 0 status_identity
				from realisasi_panen_tara_keranjang
				where no_surat_jalan = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'realisasi_panen_detail' as tabel
					,'{"no_surat_jalan" : "'+no_surat_jalan+'","no_urut":"'+cast(no_urut as varchar(20))+'"}' as kunci
					, 0 status_identity
				from realisasi_panen_detail
				where no_surat_jalan = :key2
QUERY;

            $datatransaksi = array(
                'transaksi' => 'realisasi_panen',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_surat_jalan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi realisasi_panen gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_berat_hand_pallet($params, $output)
    {
        $output_arr = json_decode($output, true);
        $kode_hand_pallet = $params['POST']['params']['id_hand_pallet'];
        $tgl_timbang = $params['POST']['params']['tgl_timbang'];
        $kode_farm = $params['POST']['params']['kode_farm'];

        if ($output_arr == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_hand_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_hand_pallet" : "'+kode_hand_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_hand_pallet mhp
				where mhp.kode_farm = :key2
				and mhp.kode_hand_pallet = :key3
				and mhp.tgl_timbang = :key4
			union all 
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_hand_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_hand_pallet" : "'+kode_hand_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_hand_pallet mhp
				where mhp.kode_farm = :key2
				and mhp.kode_hand_pallet = :key3
				and mhp.tgl_timbang = cast(getdate() as date)

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_hand_pallet',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_farm, ':key3' => $kode_hand_pallet, ':key4' => $tgl_timbang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi m_hand_pallet gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function ubah_status_hand_pallet($params, $output)
    {
        $output_arr = json_decode($output, true);

        $kode_hand_pallet = $params['POST']['kode_hand_pallet'];
        $tanggal_penimbangan = $params['POST']['tanggal_penimbangan'];
        if (!empty($output_arr['status_pallet'])) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_hand_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_hand_pallet" : "'+kode_hand_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_hand_pallet mhp
				where mhp.kode_hand_pallet = :key2
				and mhp.tgl_timbang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'ubah_status_hand_pallet',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_hand_pallet, ':key3' => $tanggal_penimbangan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_periode_siklus gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function ubah_default_hand_pallet($params, $output)
    {
        $output_arr = json_decode($output, true);
        //print_r($params['POST']);
        //print_r($output_arr);

        $kode_hand_pallet = $params['POST']['kode_hand_pallet'];
        $tanggal_penimbangan = $params['POST']['tanggal_penimbangan'];

        if ($output_arr['_default'] != '') {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_hand_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_hand_pallet" : "'+kode_hand_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_hand_pallet mhp
				where mhp.kode_hand_pallet = :key2
				and mhp.tgl_timbang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_hand_pallet',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_hand_pallet, ':key3' => $tanggal_penimbangan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_periode_siklus gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function simpan_berat_pallet($params, $output)
    {
        $output_arr = json_decode($output, true);
        $kode_pallet = $params['POST']['params']['id_pallet'];
        $tgl_timbang = $params['POST']['params']['tgl_timbang'];
        $kode_farm = $this->idFarm;

        if ($output_arr == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_pallet" : "'+kode_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_pallet mhp
				where mhp.kode_farm = :key2
				and mhp.kode_pallet = :key3
				and mhp.tgl_timbang = :key4
			union all 
			select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_pallet" : "'+kode_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_pallet mhp
				where mhp.kode_farm = :key2
				and mhp.kode_pallet = :key3
				and mhp.tgl_timbang = cast(getdate() as date)

SQL;

            $datatransaksi = array(
                    'transaksi' => 'master_pallet',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_farm, ':key3' => $kode_pallet, ':key4' => $tgl_timbang);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi master_pallet gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function ubah_status_pallet($params, $output)
    {
        $output_arr = json_decode($output, true);

        $kode_pallet = $params['POST']['kode_pallet'];
        $tanggal_penimbangan = $params['POST']['tanggal_penimbangan'];
        if (!empty($output_arr['status_pallet'])) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'm_pallet' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_pallet" : "'+kode_pallet+'","tgl_timbang" : "'+cast(tgl_timbang as varchar(max))+'"}' as kunci
					, 0 status_identity
				from m_pallet mhp
				where mhp.kode_pallet = :key2
				and mhp.tgl_timbang = :key3

SQL;

            $datatransaksi = array(
                    'transaksi' => 'ubah_status_pallet',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_pallet, ':key3' => $tanggal_penimbangan);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi ubah_status_pallet gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function approvebapd($params, $output)
    {
        $output_arr = json_decode($output, true);
        $nextstatus = $params['POST']['nextstatus'];
        $noreg = $params['POST']['noreg'];
        if ($output_arr['status']) {
            if ($nextstatus == 'A') {
                $noreg_str = "('".implode("','", $noreg)."')";
                $sqlDetail = <<<SQL
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'bap_doc' as tabel
						,'{"no_reg" : "'+no_reg+'"}' as kunci
						, 0 status_identity
					from bap_doc
					where no_reg in {$noreg_str}
					union all
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'bap_doc_box' as tabel
						,'{"no_reg" : "'+no_reg+'","no_sj" :"'+no_sj+'","kode_box" : "'+kode_box+'"}' as kunci
						, 0 status_identity
					from bap_doc_box
					where no_reg in {$noreg_str}
					union all
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'log_bap_doc' as tabel
						,'{"no_reg" : "'+no_reg+'","no_urut" :"'+cast(no_urut as varchar(6))+'"}' as kunci
						, 0 status_identity
					from log_bap_doc
					where no_reg in {$noreg_str}

SQL;

                $datatransaksi = array(
                        'transaksi' => 'approvebapd',
                        'asal' => $this->idFarm,
                        'tujuan' => $this->serverUtama,
                        'aksi' => 'PUSH',
                    );

                $dataKey = array(':key2' => $noreg);
                $this->db->trans_begin();
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                    log_message('error', 'isi tabel sinkronisasi aksi approvebapd gagal pada '.date('Y-m-d H:i:s'));
                } else {
                    $this->db->trans_commit();
                }
            }
        }
    }

    /* simpan realisasi panen di farm */
    public function simpan_admin_farm($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['result'] == 'success') {
            $no_surat_jalan = $params['POST']['no_sj'];
            $no_reg = $params['POST']['no_reg'];
            $no_do = $params['POST']['no_do'];
            $sqlDetail = <<<QUERY

				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'realisasi_panen' as tabel
					,'{"no_reg" : "'+no_reg+'","no_surat_jalan":"'+no_surat_jalan+'","no_do" : "'+no_do+'"}' as kunci
					, 0 status_identity
				from realisasi_panen
				where no_surat_jalan = :key2 and no_reg = :key3 and no_do = :key4
QUERY;

            $datatransaksi = array(
                'transaksi' => 'realisasi_panen',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_surat_jalan, ':key3' => $no_reg, ':key4' => $no_do);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi realisasi_panen gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengajuan_glangsing_simpan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['result'] == 'success') {
            $no_ppsk = $output_arr['no_ppsk'];
            $kode_barang = $output_arr['kode_barang'];
            $kode_siklus = $output_arr['kode_siklus'];
            $kode_farm = $output_arr['kode_farm'];
            $ref_id = '';
            $sqlConditions = '';
            if (isset($output_arr['ref_id'])) {
                $ref_id = $output_arr['ref_id'];
                $sqlConditions = <<<SQL
					union all
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'log_ppsk_new' as tabel
						,'{"no_ppsk":"'+no_ppsk+'","no_urut":"'+cast(no_urut as varchar(5))+'"}' as kunci
						, 0 status_identity
					from log_ppsk_new
					where no_ppsk = :key3 and status = 'V'

SQL;
            }
            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'ppsk_new' as tabel
					,'{"no_ppsk":"'+no_ppsk+'"}' as kunci
					, 0 status_identity
				from ppsk_new
				where no_ppsk = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'ppsk_d' as tabel
					,'{"no_ppsk":"'+no_ppsk+'","no_reg" :"'+no_reg+'"}' as kunci
					, 0 status_identity
				from ppsk_d
				where no_ppsk = :key2
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'log_ppsk_new' as tabel
					,'{"no_ppsk":"'+no_ppsk+'"}' as kunci
					, 0 status_identity
				from log_ppsk_new
				where no_ppsk = :key2
				union all
				select :key1 as sinkronisasi
				, 'U' as aksi
				, 'glangsing_movement' as tabel
				,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
				, 0 status_identity
				from glangsing_movement gm
				where gm.kode_farm = :key6 and gm.kode_siklus = :key4 and gm.kode_barang = :key5
				union all
				select :key1 as sinkronisasi
				, 'U' as aksi
				, 'glangsing_movement' as tabel
				,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
				, 0 status_identity
				from glangsing_movement gm
				where gm.kode_farm = :key6 and gm.kode_siklus = :key4 and gm.kode_barang = 'GBP'
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'glangsing_movement_d' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","keterangan1" : "'+keterangan1+'","keterangan2" : "'+keterangan2+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'","no_referensi" : "'+cast(no_referensi as varchar(max))+'"}' as kunci
					, 0 status_identity
				from glangsing_movement_d
				where kode_farm = :key6 and kode_siklus = :key4 and no_referensi = :key2 and keterangan1 in ('IN','OUT')
				{$sqlConditions}
QUERY;

            $datatransaksi = array(
                'transaksi' => 'pengajuan_glangsing_simpan',
                //'asal' => $this->idFarm,
                //'tujuan' => $kodeFarm['kode_farm'],
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_ppsk, ':key3' => $ref_id, ':key4' => $kode_siklus, ':key5' => $kode_barang, ':key6' => $kode_farm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_glangsing_simpan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function kontrol_stok_glangsing_updateppsk($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $ppsk = $output_arr['ppsk'];
            $where = array();
            foreach ($ppsk as $key => $val) {
                $where[] = "(no_ppsk = '".$val['no_ppsk']."' and no_urut = '".$val['no_urut']."')";
            }
            $sqlConditions = join(' or ', $where);

            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'log_ppsk_new' as tabel
					,'{"no_ppsk":"'+no_ppsk+'","no_urut":"'+cast(no_urut as varchar(5))+'"}' as kunci
					, 0 status_identity
				from log_ppsk_new
				where {$sqlConditions}
QUERY;

            //cetak_r($sqlDetail);

            $datatransaksi = array(
                'transaksi' => 'kontrol_stok_glangsing_updateppsk',
                //'asal' => $this->idFarm,
                //'tujuan' => $kodeFarm['kode_farm'],
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array();
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'kontrol_stok_glangsing_updateppsk gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengajuan_glangsing_pengambilan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $no_ppsk = $output_arr['no_ppsk'];
            $no_reg = $output_arr['no_reg'];
            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'ppsk_d' as tabel
					,'{"no_ppsk":"'+no_ppsk+'","no_reg":"'+no_reg+'"}' as kunci
					, 0 status_identity
				from ppsk_d
				where no_ppsk = :key2 and no_reg = :key3				
QUERY;

            //cetak_r($sqlDetail);

            $datatransaksi = array(
                'transaksi' => 'pengajuan_glangsing_pengambilan',
                //'asal' => $this->idFarm,
                //'tujuan' => $kodeFarm['kode_farm'],
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_ppsk, ':key3' => $no_reg);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_glangsing_pengambilan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengajuan_glangsing_pengembalian_sak($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $no_ppsk = $output_arr['no_ppsk'];
            $no_reg = $output_arr['no_reg'];
            $movement_d = $output_arr['movement_d'];
            $sqlTambahan = '';
            $kode_barang = '';
            $kode_siklus = '';
            $kode_farm = '';
            if (!empty($movement_d)) {
                $kode_barang = $movement_d['kode_barang'];
                $kode_siklus = $movement_d['kode_siklus'];
                $kode_farm = $movement_d['kode_farm'];
                $sqlTambahan = <<<QUERY
					union all
					select :key1 as sinkronisasi
					, 'U' as aksi
					, 'glangsing_movement' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
					, 0 status_identity
					from glangsing_movement gm
					where gm.kode_farm = :key6 and gm.kode_siklus = :key4 and gm.kode_barang = :key5
					union all
					select :key1 as sinkronisasi
					, 'U' as aksi
					, 'glangsing_movement' as tabel
					,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
					, 0 status_identity
					from glangsing_movement gm
					where gm.kode_farm = :key6 and gm.kode_siklus = :key4 and gm.kode_barang = 'GBP'
					union all
					select :key1 as sinkronisasi
						, 'I' as aksi
						, 'glangsing_movement_d' as tabel
						,'{"kode_farm" : "'+kode_farm+'","kode_barang" : "'+kode_barang+'","keterangan1" : "'+keterangan1+'","keterangan2" : "'+keterangan2+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'","no_referensi" : "'+cast(no_referensi as varchar(max))+'"}' as kunci
						, 0 status_identity
					from glangsing_movement_d
					where keterangan2 = :key3 and kode_farm = :key6 and kode_siklus = :key4 and no_referensi = :key2 and keterangan1 in ('RETUR_IN','RETUR_OUT')
QUERY;
            }

            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'ppsk_d' as tabel
					,'{"no_ppsk":"'+no_ppsk+'","no_reg":"'+no_reg+'"}' as kunci
					, 0 status_identity
				from ppsk_d
				where no_ppsk = :key2 and no_reg = :key3	
				{$sqlTambahan}			
QUERY;

            //cetak_r($sqlDetail);

            $datatransaksi = array(
                'transaksi' => 'pengajuan_glangsing_pengembalian_sak',
                //'asal' => $this->idFarm,
                //'tujuan' => $kodeFarm['kode_farm'],
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_ppsk, ':key3' => $no_reg, ':key4' => $kode_siklus, ':key5' => $kode_barang, ':key6' => $kode_farm);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_glangsing_pengambilan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengajuan_glangsing_pengembalian_sak_ack($params, $output)
    {
        $output_arr = json_decode($output, true);
        //$this->result['no_ppsk'] = $where['no_ppsk'];
        //	$this->result['no_reg'] = $where['no_reg'];
        //	$this->result['status'] = 1;
        if ($output_arr['status'] == 1) {
            $sql = array();
            foreach ($output_arr['detail'] as $key => $val) {
                $no_ppsk = $val['no_ppsk'];
                $no_reg = $val['no_reg'];
                $sql[] = '
						select :key1 as sinkronisasi
							, \'U\' as aksi
							, \'ppsk_d\' as tabel
							,\'{"no_ppsk":"'.$no_ppsk.'","no_reg":"'.$no_reg.'"}\' as kunci
							, 0 status_identity
						';
            }
            $sqlDetail = join('union all', $sql);
            $sqlDetail2 = <<<SQL
							{$sqlDetail}
SQL;
            //cetak_r($sqlDetail);

            $datatransaksi = array(
                'transaksi' => 'pengajuan_glangsing_pengembalian_sak_ack',
                //'asal' => $this->idFarm,
                //'tujuan' => $kodeFarm['kode_farm'],
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array();
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail2);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_glangsing_pengembalian_sak_ack gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* sinkronisasi untuk realisasi penjualan glangsing */
    public function realisasi_penjualan_glangsing($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $no_sj = $params['POST']['no_sj'];
            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'U' as aksi
				, 'surat_jalan' as tabel
				,'{"no_sj":"'+no_sj+'"}' as kunci
				, 0 status_identity
			from surat_jalan
			where no_sj = :key2
			union all
			select :key1 as sinkronisasi
			, 'U' as aksi
			, 'surat_jalan_d' as tabel
			,'{"no_sj":"'+no_sj+'","kode_barang":"'+kode_barang+'"}' as kunci
			, 0 status_identity
			from surat_jalan_d
			where no_sj = :key2
			union all 
			select :key1 as sinkronisasi
			, 'U' as aksi
			, 'glangsing_movement' as tabel
			,'{"kode_farm":"'+gm.kode_farm+'","kode_barang":"'+gm.kode_barang+'","kode_siklus" : "'+cast(gm.kode_siklus as varchar(10))+'"}' as kunci
			, 0 status_identity
			FROM glangsing_movement gm 
			JOIN glangsing_movement_d gmd ON gmd.kode_farm = gm.kode_farm 
				AND gmd.kode_siklus = gm.kode_siklus AND gmd.kode_barang = gm.kode_barang AND  gmd.no_referensi = :key2		
			union all
			select :key1 as sinkronisasi
			, 'I' as aksi
			, 'glangsing_movement_d' as tabel
			,'{"no_referensi":"'+no_referensi+'","kode_barang":"'+kode_barang+'","kode_siklus" : "'+cast(kode_siklus as varchar(10))+'"}' as kunci
			, 0 status_identity
			from glangsing_movement_d
			where no_referensi = :key2			
			
QUERY;

            $datatransaksi = array(
                'transaksi' => 'realisasi_penjualan_glangsing',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_sj);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'realisasi_penjualan_glangsing gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* sinkronisasi untuk realisasi penjualan glangsing */
    public function plotting_kendaraan_so_do($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $no_sj = $output_arr['content']; /* ini isinya array list no_sj */
            /** grouping no_sj GD18-G00015 berdasarkan farm */
            $_groupFarm = array();
            foreach ($no_sj as $sj) {
                $kodeFarmSJ = substr($sj, 0, 2);
                if (!isset($_groupFarm[$kodeFarmSJ])) {
                    $_groupFarm[$kodeFarmSJ] = array();
                }
                array_push($_groupFarm[$kodeFarmSJ], $sj);
            }
            $this->db->trans_begin();
            foreach ($_groupFarm as $kodeFarmSJ => $arrSJ) {
                $list_sj = "('".implode("','", $arrSJ)."')";
                $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'sales_order' as tabel
					,'{"no_so":"'+no_so+'"}' as kunci
					, 0 status_identity
				from sales_order
				where no_so in (select distinct no_so from surat_jalan where no_sj in {$list_sj})
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'surat_jalan' as tabel
					,'{"no_sj":"'+no_sj+'"}' as kunci
					, 0 status_identity
				from surat_jalan
				where no_sj in {$list_sj}
				union all
				select :key1 as sinkronisasi
				, 'I' as aksi
				, 'surat_jalan_d' as tabel
				,'{"no_sj":"'+no_sj+'","kode_barang":"'+kode_barang+'"}' as kunci
				, 0 status_identity
				from surat_jalan_d
				where no_sj in {$list_sj}						
QUERY;

                $datatransaksi = array(
                    'transaksi' => 'plotting_kendaraan_so_do',
                    'asal' => $this->idFarm,
                    'tujuan' => $kodeFarmSJ,
                    'aksi' => 'PUSH',
                );

                $dataKey = array();
                $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'plotting_kendaraan_so_do gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* pemusnahan bangkai */
    public function pemusnahan_bangkai($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $no_ba = $output_arr['content'];
            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'I' as aksi
				, 'ba_pemusnahan' as tabel
				,'{"no_berita_acara":"'+no_berita_acara+'"}' as kunci
				, 0 status_identity
			from ba_pemusnahan
			where no_berita_acara = :key2			
QUERY;

            $datatransaksi = array(
                'transaksi' => 'pemusnahan_bangkai',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_ba);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pemusnahan_bangkai gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function pengajuan_retur_pakan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['result'] == 'success') {
            $no_retur = $output_arr['content'];
            $list_no_retur = "('".implode("','", $no_retur)."')";
            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'I' as aksi
				, 'retur_kandang' as tabel
				,'{"no_retur":"'+no_retur+'","no_reg":"'+no_reg+'"}' as kunci
				, 0 status_identity
			from retur_kandang
			where no_retur in {$list_no_retur}
			union all
			select :key1 as sinkronisasi
			, 'I' as aksi
			, 'retur_kandang_d' as tabel
			,'{"no_retur":"'+no_retur+'","no_reg":"'+no_reg+'","kode_barang":"'+kode_barang+'"}' as kunci
			, 0 status_identity
			from retur_kandang_d
			where no_retur in {$list_no_retur}						
QUERY;

            $datatransaksi = array(
                'transaksi' => 'pengajuan_retur_pakan',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array();
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_retur_pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function ubah_pengajuan_retur_pakan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['result'] == 'success') {
            $no_retur = $output_arr['content'];

            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'U' as aksi
				, 'retur_kandang' as tabel
				,'{"no_retur":"'+no_retur+'","no_reg":"'+no_reg+'"}' as kunci
				, 0 status_identity
			from retur_kandang
			where no_retur = :key2
			union all
			select :key1 as sinkronisasi
			, 'U' as aksi
			, 'retur_kandang_d' as tabel
			,'{"no_retur":"'+no_retur+'","no_reg":"'+no_reg+'","kode_barang":"'+kode_barang+'"}' as kunci
			, 0 status_identity
			from retur_kandang_d
			where no_retur = :key2						
QUERY;

            $datatransaksi = array(
                'transaksi' => 'ubah_pengajuan_retur_pakan',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_retur);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'ubah_pengajuan_retur_pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function reset_budget_otomatis($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $ppsk_noreg = $output_arr['content']['ppsk_d'];
            $ref_glangsing = $output_arr['content']['ref'];
            $listSql = array();
            $listbudget = $output_arr['content']['listbudget'];
            $kode_siklus = $output_arr['content']['kode_siklus'];
            if (!empty($ppsk_noreg)) {
                foreach ($ppsk_noreg as $ppskd) {
                    $sqlDetail = <<<QUERY
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'ppsk_d' as tabel
						,'{"no_ppsk":"'+no_ppsk+'","no_reg":"'+no_reg+'"}' as kunci
						, 0 status_identity
					from ppsk_d
					where no_ppsk = '{$ppskd['no_ppsk']}' and no_reg = '{$ppskd['no_reg']}'
								
QUERY;
                    array_push($listSql, $sqlDetail);
                }
            }
            if (!empty($listbudget)) {
                foreach ($listbudget as $tmp) {
                    $sqlDetail = <<<QUERY
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'glangsing_movement' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(10))+'","kode_barang":"'+kode_barang+'"}' as kunci
						, 0 status_identity
					from glangsing_movement
					where kode_siklus = '{$tmp['kode_siklus']}' and kode_barang = '{$tmp['kode_barang']}'
								
QUERY;
                    array_push($listSql, $sqlDetail);
                }
            }
            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'U' as aksi
				, 'glangsing_movement_d' as tabel
				,'{"kode_siklus":"'+cast(kode_siklus as varchar(10))+'","kode_barang":"'+kode_barang+'","no_referensi":"'+no_referensi+'"}' as kunci
				, 0 status_identity
			from glangsing_movement_d
			where no_referensi = ':key2' and kode_siklus = :key3
						
QUERY;
            if (!empty($listSql)) {
                $sqlDetail .= ' union all '.implode(' union all ', $listSql);
            }
            $datatransaksi = array(
                'transaksi' => 'reset_budget_otomatis',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $ref_glangsing, ':key3' => $kode_siklus);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'reset_budget_otomatis gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function terima_pakan_farm($params, $output)
    {
        $output_arr = json_decode($output, true);
        $content = $output_arr['content'];
        $kode_barang = $params['POST']['kode_barang'];
        $no_reg = $params['POST']['no_reg'];
        $no_order = $content['no_order'];
        $no_pallet = $content['no_pallet'];
        $no_penerimaan_kandang = $content['no_penerimaan_kandang'];
        if ($output_arr['status'] == 1) {
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'penerimaan' as tabel
					,'{"kode_farm" : "'+p.kode_farm+'", "no_penerimaan" : "'+p.no_penerimaan+'"}' as kunci
					, 0 status_identity
				from penerimaan p
				where p.no_penerimaan = :key4 and p.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'penerimaan_d' as tabel
					,'{"kode_farm" : "'+pd.kode_farm+'", "no_penerimaan" : "'+pd.no_penerimaan+'", "kode_barang" : "'+pd.kode_barang+'"}' as kunci
					, 0 status_identity
				from penerimaan_d pd
				where pd.no_penerimaan = :key4 and pd.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'penerimaan_e' as tabel
					,'{"kode_farm" : "'+pe.kode_farm+'", "no_penerimaan" : "'+pe.no_penerimaan+'", "kode_barang" : "'+pe.kode_barang+'", "no_pallet" : "'+pe.no_pallet+'"}' as kunci
					, 0 status_identity
				from penerimaan_e pe
				where pe.no_penerimaan = :key4 and pe.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'movement' as tabel
					,'{"kode_farm" : "'+m.kode_farm+'", "no_kavling" : "'+m.no_kavling+'", "no_pallet" : "'+m.no_pallet+'", "kode_barang" : "'+m.kode_barang+'", "jenis_kelamin" : "'+m.jenis_kelamin+'", "keterangan1" : "'+m.keterangan1+'"}' as kunci
					, 0 status_identity
				from movement m				
				where m.no_pallet = :key2 and m.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'movement_d' as tabel
					,'{"kode_farm" : "'+md.kode_farm+'", "no_kavling" : "'+md.no_kavling+'", "no_pallet" : "'+md.no_pallet+'", "kode_barang" : "'+md.kode_barang+'", "jenis_kelamin" : "'+md.jenis_kelamin+'", "no_referensi" : "'+md.no_referensi+'", "keterangan2" : "'+md.keterangan2+'"}' as kunci
					, 0 status_identity
				from MOVEMENT_D md
				where md.no_pallet = :key2 and md.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'order_kandang' as tabel
					,'{"kode_farm" : "'+ok.kode_farm+'", "no_order" : "'+ok.no_order+'"}' as kunci
					, 0 status_identity
				from order_kandang ok
				where ok.no_order = :key5 and ok.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'order_kandang_d' as tabel
					,'{"kode_farm" : "'+okd.kode_farm+'", "no_order" : "'+okd.no_order+'", "no_reg" : "'+okd.no_reg+'"}' as kunci
					, 0 status_identity
				from order_kandang_d okd
				where okd.no_order = :key5 and okd.kode_farm = :key3
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'order_kandang_e' as tabel
					,'{"no_order" : "'+oke.no_order+'", "no_reg" : "'+oke.no_reg+'", "kode_barang" : "'+oke.kode_barang+'", "tgl_kebutuhan" : "'+(left(cast(oke.tgl_kebutuhan as date),10))+'", "jenis_kelamin" : "'+oke.jenis_kelamin+'"}' as kunci
					, 0 status_identity
				from order_kandang ok
				join order_kandang_d okd
					on ok.kode_farm = okd.kode_farm
					and ok.no_order = okd.no_order
				join order_kandang_e oke
					on oke.no_order = okd.no_order
					and oke.no_reg = okd.no_reg
				where ok.no_order = :key5 and ok.kode_farm = :key3
				union all 
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'pick_d' as tabel
						,'{"kode_farm" : "'+pd.kode_farm+'", "no_kavling" : "'+pd.no_kavling+'", "no_pallet" : "'+pd.no_pallet+'", "kode_barang" : "'+pd.kode_barang+'", "no_reg" : "'+pd.no_reg+'", "no_order" : "'+pd.no_order+'"}' as kunci
						, 0 status_identity
					from pick_d pd
					where pd.kode_farm = :key3					
					and pd.no_pallet = '$no_pallet'
					and pd.kode_barang = '$kode_barang'
					and pd.no_order = '$no_order'
					and pd.no_reg = '$no_reg'					
					union all
					select distinct :key1 as sinkronisasi
						, 'U' as aksi
						, 'penerimaan_kandang' as tabel
						,'{"no_reg" : "'+pk.no_reg+'", "no_penerimaan_kandang" : "'+pk.no_penerimaan_kandang+'", "no_order" : "'+pk.no_order+'"}' as kunci
						, 0 status_identity
					from penerimaan_kandang pk
					where pk.no_order = '$no_order'
					and pk.no_reg = '$no_reg'
					and pk.no_penerimaan_kandang = '$no_penerimaan_kandang'					
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'penerimaan_kandang_d' as tabel
						,'{"no_reg" : "'+pk.no_reg+'", "no_penerimaan_kandang" : "'+pk.no_penerimaan_kandang+'","kode_barang" :"'+pkd.kode_barang+'"}' as kunci
						, 0 status_identity
					from penerimaan_kandang_d pkd
					join penerimaan_kandang pk
						on pk.no_penerimaan_kandang = pkd.no_penerimaan_kandang
						and pk.no_reg = pkd.no_reg
					where pk.no_order = '$no_order'
					and pk.no_reg = '$no_reg'
					and pkd.kode_barang = '$kode_barang'
					and pk.no_penerimaan_kandang = '$no_penerimaan_kandang'
					and pkd.jenis_kelamin = 'C'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'kandang_movement' as tabel
						,'{"no_reg" : "'+km.no_reg+'", "kode_barang" : "'+km.kode_barang+'", "jenis_kelamin" : "'+km.jenis_kelamin+'"}' as kunci
						, 0 status_identity
					from kandang_movement km
					where km.no_reg = '$no_reg'
					and km.kode_barang = '$kode_barang'
					and km.jenis_kelamin = 'C'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'kandang_movement_d' as tabel
						,'{"no_reg" : "'+kmd.no_reg+'", "kode_barang" : "'+kmd.kode_barang+'", "tgl_transaksi" : "'+left(cast(kmd.tgl_transaksi as date),10)+'", "jenis_kelamin" : "'+kmd.jenis_kelamin+'", "keterangan1" : "'+kmd.keterangan1+'", "keterangan2" : "'+kmd.keterangan2+'"}' as kunci
						, 0 status_identity
					from kandang_movement_d kmd
					where kmd.no_reg = '$no_reg'
					and kmd.kode_barang = '$kode_barang'
					and kmd.jenis_kelamin = 'C'
					and kmd.tgl_transaksi = cast(getdate() as date)
					and kmd.keterangan1 = 'PENERIMAAN KANDANG'
				
SQL;

            $datatransaksi = array(
                    'transaksi' => 'terima_pakan_farm',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $content['no_pallet'], ':key3' => $this->idFarm, ':key4' => $content['no_penerimaan'], ':key5' => $content['no_order']);
            //	$this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            /*
            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                log_message('error','isi tabel sinkronisasi aksi terima_pakan_farm gagal pada '.date('Y-m-d H:i:s'));
            }
            else{
                $this->db->trans_commit();
            }*/
        }
    }

    /* pengajuan_harga */
    public function simpan_pengajuan_harga($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $no_pengajuan_harga = $output_arr['content']['no_pengajuan_harga'];
            $update_ref = $output_arr['content']['update_ref'];
            $kode_farm = $output_arr['content']['kode_farm'];
            $update_ref_sql = '';
            if (!empty($update_ref)) {
                $update_ref_sql = <<<SQL
				union all 
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'pengajuan_harga' as tabel
					,'{"no_pengajuan_harga":"'+no_pengajuan_harga+'"}' as kunci
					, 0 status_identity
				from pengajuan_harga
				where no_pengajuan_harga = :key3			
SQL;
            }
            $sqlDetail = <<<SQL
			select :key1 as sinkronisasi
					, 'I' as aksi
					, 'pengajuan_harga' as tabel
					,'{"no_pengajuan_harga":"'+no_pengajuan_harga+'"}' as kunci
					, 0 status_identity
				from pengajuan_harga
				where no_pengajuan_harga = :key2			
			union all 
			select :key1 as sinkronisasi
				, 'I' as aksi
				, 'pengajuan_harga_d' as tabel
				,'{"no_pengajuan_harga":"'+no_pengajuan_harga+'","kode_barang" :"'+kode_barang+'"}' as kunci
				, 0 status_identity
			from pengajuan_harga_d
			where no_pengajuan_harga = :key2	
			union all 
			select :key1 as sinkronisasi
				, 'I' as aksi
				, 'log_pengajuan_harga' as tabel
				,'{"no_pengajuan_harga":"'+no_pengajuan_harga+'","no_urut":"'+cast(no_urut as varchar(5))+'"}' as kunci
				, 0 status_identity
			from log_pengajuan_harga
			where no_pengajuan_harga = :key2	
			{$update_ref_sql}
SQL;

            $datatransaksi = array(
                'transaksi' => 'pengajuan_harga_simpan',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_pengajuan_harga, ':key3' => $update_ref);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_harga_simpan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function approve_reject_pengajuan_harga($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $listPengajuanHarga = $output_arr['content'];
            $this->db->trans_begin();
            if (!empty($listPengajuanHarga)) {
                foreach ($listPengajuanHarga as $kf => $phs) {
                    if (!empty($phs)) {
                        $sqlArr = array();
                        foreach ($phs as $ph) {
                            $sqlDetail = <<<SQL
							select :key1 as sinkronisasi
								, 'I' as aksi
								, 'log_pengajuan_harga' as tabel
								,'{"no_pengajuan_harga":"'+no_pengajuan_harga+'","no_urut":"'+cast(no_urut as varchar(5))+'"}' as kunci
								, 0 status_identity
							from log_pengajuan_harga
							where no_pengajuan_harga = '{$ph['no_pengajuan_harga']}' and no_urut = '{$ph['no_urut']}'	 
SQL;
                        }
                        array_push($sqlArr, $sqlDetail);
                        $datatransaksi = array(
                        'transaksi' => 'approve_reject_pengajuan_harga',
                        'asal' => $this->idFarm,
                        'tujuan' => $kf,
                        'aksi' => 'PUSH',
                    );
                        $dataKey = array();
                        $this->sinkronisasi->insert($datatransaksi, $dataKey, implode(' union all ', $sqlArr));
                    }
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'approve_reject_pengajuan_harga gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* import panen do */
    public function import_panen_do($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $listDO = $output_arr['content'];
            $this->db->trans_begin();
            if (!empty($listDO)) {
                foreach ($listDO as $kf => $dos) {
                    if (!empty($dos)) {
                        $daftarDO = implode("','", $dos);
                        $sqlDetail = <<<SQL
			select :key1 as sinkronisasi
					, 'I' as aksi
					, 'realisasi_panen_do' as tabel
					,'{"no_do":"'+no_do+'"}' as kunci
					, 0 status_identity
				from realisasi_panen_do
				where no_do in ('{$daftarDO}')
SQL;
                        $datatransaksi = array(
                    'transaksi' => 'import_realisasi_panen_do',
                    'asal' => $this->idFarm,
                    'tujuan' => $kf,
                    'aksi' => 'PUSH',
                );
                        $dataKey = array();
                        $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
                    }
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'pengajuan_harga_simpan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* update general config */
    public function general_config_simpan($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['sinkron'] == 1) {
            $dataConfig = $output_arr['content'];
            $kodeFarm = $dataConfig['kode_farm'];
            $kodeConfig = $dataConfig['kode_config'];
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'sys_config_general' as tabel
						,'{"kode_farm":"'+kode_farm+'","kode_config":"'+kode_config+'"}' as kunci
						, 0 status_identity
					from sys_config_general
					where kode_farm = :key2 and kode_config = :key3
SQL;
            $datatransaksi = array(
                'transaksi' => 'general_config_simpan',
                'asal' => $this->idFarm,
                'tujuan' => $kodeFarm,
                'aksi' => 'PUSH',
            );
            $this->db->trans_begin();
            $dataKey = array(':key2' => $kodeFarm, ':key3' => $kodeConfig);
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'general_config_simpan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /** batal SO */
    public function batalSO($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $no_so = $params['POST']['no_so'];
            $kodeFarm = substr($no_so, 0, 2);
            $sqlDetail = <<<SQL
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'sales_order' as tabel
						,'{"kode_farm":"'+kode_farm+'","no_so":"'+no_so+'"}' as kunci
						, 0 status_identity
					from sales_order
					where kode_farm = :key2 and no_so = :key3
SQL;
            $datatransaksi = array(
                'transaksi' => 'batal so',
                'asal' => $this->idFarm,
                'tujuan' => $kodeFarm,
                'aksi' => 'PUSH',
            );
            $this->db->trans_begin();
            $dataKey = array(':key2' => $kodeFarm, ':key3' => $no_so);
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'batal so gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /** simpanBudgetGlangsing */
    public function simpanBudgetGlangsing($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $content = $output_arr['content'];
            $kodeFarm = $content['kode_farm'];
            $kodeSiklus = $content['kode_siklus'];
            $noUrut = $content['no_urut'];
            $noUrutApprove = $content['no_urut_approve'];
            $action = $content['action'];
            $sqlTemp = array();
            if ($action == 'D') {
                return;
            }
            if (isset($content['budget_glangsing'])) {
                $aksi = $content['budget_glangsing'];
                $sqlTemp[] = <<<SQL
				select :key1 as sinkronisasi
						, '{$aksi}' as aksi
						, 'budget_glangsing' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'"}' as kunci
						, 0 status_identity
					from budget_glangsing
					where kode_siklus = :key2 
SQL;
            }
            if (isset($content['budget_glangsing_d'])) {
                $aksi = $content['budget_glangsing_d'];
                $sqlTemp[] = <<<SQL
				select :key1 as sinkronisasi
						, 'U' as aksi
						, 'budget_glangsing_d' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","kode_budget":"'+kode_budget+'","no_urut":"'+cast(no_urut as varchar(3))+'"}' as kunci
						, 0 status_identity
					from budget_glangsing_d
					where kode_siklus = :key2
SQL;
            }
            if (isset($content['log_budget_glangsing'])) {
                $aksi = $content['log_budget_glangsing'];
                $sqlTemp[] = <<<SQL
				select :key1 as sinkronisasi
						, '{$aksi}' as aksi
						, 'log_budget_glangsing' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","status":"'+status+'","no_urut":"'+cast(no_urut as varchar(3))+'","no_urut_approve":"'+cast(no_urut_approve as varchar(3))+'"}' as kunci
						, 0 status_identity
					from log_budget_glangsing
					where kode_siklus = :key2 and no_urut = '{$noUrut}' and no_urut_approve = '{$noUrutApprove}' and status = '{$action}'
SQL;
            }
            if (isset($content['glangsing_movement'])) {
                $aksi = $content['glangsing_movement'];
                $siklusLalu = $content['siklus_lalu'];
                $sqlTemp[] = <<<SQL
				select :key1 as sinkronisasi
						, '{$aksi}' as aksi
						, 'glangsing_movement' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","kode_barang":"'+kode_barang+'"}' as kunci
						, 0 status_identity
					from glangsing_movement
					where kode_siklus = :key2 
					union all 
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'glangsing_movement' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","kode_barang":"'+kode_barang+'"}' as kunci
						, 0 status_identity
					from glangsing_movement
					where kode_siklus = '{$siklusLalu}' and kode_barang = 'GBP'
SQL;
            }

            if (isset($content['next_siklus'])) {
                $next_siklus = $content['next_siklus'];
                $sqlTemp[] = <<<SQL
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'budget_glangsing' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'"}' as kunci
						, 0 status_identity
					from budget_glangsing
					where kode_siklus = '{$next_siklus}'
				union all	
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'budget_glangsing_d' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","kode_budget":"'+kode_budget+'","no_urut":"'+cast(no_urut as varchar(3))+'"}' as kunci
						, 0 status_identity
					from budget_glangsing_d
				where kode_siklus = '{$next_siklus}' and no_urut = '1'
				union all 
				select :key1 as sinkronisasi
						, 'I' as aksi
						, 'log_budget_glangsing' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","status":"'+status+'","no_urut":"'+cast(no_urut as varchar(3))+'","no_urut_approve":"'+cast(no_urut_approve as varchar(3))+'"}' as kunci
						, 0 status_identity
					from log_budget_glangsing
				where kode_siklus = '{$next_siklus}' and no_urut = '1' and no_urut_approve = '1' and status = 'A'
				union all
				select :key1 as sinkronisasi
						, '{$aksi}' as aksi
						, 'glangsing_movement' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","kode_barang":"'+kode_barang+'"}' as kunci
						, 0 status_identity
					from glangsing_movement
					where kode_siklus = '{$next_siklus}'
					union all 
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'glangsing_movement' as tabel
						,'{"kode_siklus":"'+cast(kode_siklus as varchar(5))+'","kode_barang":"'+kode_barang+'"}' as kunci
						, 0 status_identity
					from glangsing_movement
					where kode_siklus = '{$kodeSiklus}' and kode_barang = 'GBP'

SQL;
            }

            $sqlDetail = implode(' union all ', $sqlTemp);
            $datatransaksi = array(
                'transaksi' => 'simpan budget glangsing',
                'asal' => $this->idFarm,
                'tujuan' => $this->idFarm == $kodeFarm ? 'FM' : $kodeFarm,
                'aksi' => 'PUSH',
            );
            $this->db->trans_begin();
            $dataKey = array(':key2' => $kodeSiklus);
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'simpan budget glangsing gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* plotting_pelaksana */
    public function plotting_pelaksana($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $kode_siklus = $output_arr['content'];
            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'M_PLOTING_PELAKSANA' as tabel
					,'{"no_reg":"'+no_reg+'","operator":"'+operator+'"}' as kunci
					, 0 status_identity
				from M_PLOTING_PELAKSANA
				where kode_siklus = :key2			
QUERY;

            $datatransaksi = array(
                    'transaksi' => 'plotting_pelaksana',
                    'asal' => $this->idFarm,
                    'tujuan' => $this->serverUtama,
                    'aksi' => 'PUSH',
                );

            $dataKey = array(':key2' => $kode_siklus);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'plotting_pelaksana gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function lhk_cetak($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status']) {
            $noreg = $params['POST']['no_reg'];
            $tgl_lhk_cetak = $params['POST']['tgllhk'];
            $tgl_lhk_kemarin = $params['POST']['tgllhk_sebelum'];

            $sqlDetail = <<<SQL
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'rhk' as tabel
						,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'"}' as kunci
						, 0 status_identity
					from rhk
					where no_reg = :key2 and tgl_transaksi = :key3
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'rhk_pakan' as tabel
						,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","kode_barang":"'+kode_barang+'","jenis_kelamin":"'+jenis_kelamin+'"}' as kunci
						, 0 status_identity
					from rhk_pakan
					where no_reg = :key2 and tgl_transaksi = :key3
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'rhk_penimbangan_bb' as tabel
						,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","sekat":"'+cast(sekat as varchar(10))+'","jenis_kelamin":"'+jenis_kelamin+'"}' as kunci
						, 0 status_identity
					from rhk_penimbangan_bb
					where no_reg = :key2 and tgl_transaksi = :key3
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'kandang_movement' as tabel
						,'{"no_reg" : "'+km.no_reg+'","kode_barang":"'+km.kode_barang+'","jenis_kelamin":"'+km.jenis_kelamin+'"}' as kunci
						, 0 status_identity
					from kandang_movement km
					join kandang_movement_d kmd on kmd.no_reg = km.no_reg and kmd.kode_barang = km.kode_barang
						and kmd.jenis_kelamin = km.jenis_kelamin and kmd.tgl_transaksi = :key3 and kmd.no_reg = :key2 and kmd.keterangan1 = 'LHK'
					union all
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'kandang_movement_d' as tabel
						,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'","kode_barang":"'+kode_barang+'","jenis_kelamin":"'+jenis_kelamin+'","keterangan1" : "LHK"}' as kunci
						, 0 status_identity
					from kandang_movement_d
					where no_reg = :key2 and tgl_transaksi = :key3 and keterangan1 = 'LHK'
					union all 
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'rhk_rekomendasi_pakan' as tabel
						,'{"no_reg" : "'+no_reg+'","tgl_kebutuhan":"'+cast(tgl_kebutuhan as varchar(20))+'","kode_barang":"'+kode_barang+'"}' as kunci
						, 0 status_identity
					from rhk_rekomendasi_pakan
					where no_reg = :key2 and tgl_transaksi = :key3
					union all 
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'rhk_cetak' as tabel
						,'{"no_reg" : "'+no_reg+'","tgl_transaksi":"'+cast(tgl_transaksi as varchar(20))+'"}' as kunci
						, 0 status_identity
					from rhk_cetak
					where no_reg = :key2 and tgl_transaksi = :key4
SQL;

            $datatransaksi = array(
                        'transaksi' => 'rhk_cetak',
                        'asal' => $this->idFarm,
                        'tujuan' => $this->serverUtama,
                        'aksi' => 'PUSH',
                    );

            $dataKey = array(':key2' => $noreg, ':key3' => $tgl_lhk_kemarin, ':key4' => $tgl_lhk_cetak);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi cetak rhk gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    /* plotting_pelaksana ack */
    public function plotting_pelaksana_ack($params, $output)
    {
        $output_arr = json_decode($output, true);

        if ($output_arr['status'] == 1) {
            $kode_siklus = $output_arr['kode_siklus'];
            $kodeFarm = $output_arr['kode_farm'];
            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'U' as aksi
				, 'M_PLOTING_PELAKSANA' as tabel
				,'{"no_reg":"'+no_reg+'","operator":"'+operator+'"}' as kunci
				, 0 status_identity
			from M_PLOTING_PELAKSANA
			where kode_siklus = :key2			
QUERY;

            $datatransaksi = array(
                'transaksi' => 'plotting_pelaksana_ack',
                'asal' => $this->idFarm,
                'tujuan' => $kodeFarm,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $kode_siklus);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'plotting_pelaksana_ack gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function tambah_hari_libur($params, $output)
    {
        $output_arr = json_decode($output, true);
        $sqlDetail = array();
        foreach ($output_arr as $v) {
            if ($v['status']) {
                $tgl = $v['tgl'];
                $sqlDetail[] = <<<SQL
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_kalender' as tabel
					,'{"tanggal" : "'+cast(tanggal as varchar(30))+'"}' as kunci
					, 0 status_identity
				from m_kalender
				where tanggal = '{$tgl}'
SQL;
            }
        }

        $datatransaksi = array(
                            'transaksi' => 'tambah_hari_libur',
                            'asal' => $this->idFarm,
                            'tujuan' => '*',
                            'aksi' => 'PUSH',
                );
        $dataKey = array();
        if (!empty($sqlDetail)) {
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, implode(' union all ', $sqlDetail));
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi tambah_hari_libur gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function edit_hari_libur($params, $output)
    {
        $output_arr = json_decode($output, true);
        $sqlDetail = array();
        foreach ($output_arr as $v) {
            if ($v['status']) {
                $tgl = $v['tgl'];
                $sqlDetail[] = <<<SQL
				select :key1 as sinkronisasi
					, 'D' as aksi
					, 'm_kalender' as tabel
					,'{"tanggal" : "'+cast(tanggal as varchar(30))+'"}' as kunci
					, 0 status_identity
				from m_kalender
				where tanggal = '{$tgl}'
				union all
				select :key1 as sinkronisasi
					, 'I' as aksi
					, 'm_kalender' as tabel
					,'{"tanggal" : "'+cast(tanggal as varchar(30))+'"}' as kunci
					, 0 status_identity
				from m_kalender
				where tanggal = '{$tgl}'
SQL;
            }
        }

        $datatransaksi = array(
                            'transaksi' => 'edit_hari_libur',
                            'asal' => $this->idFarm,
                            'tujuan' => '*',
                            'aksi' => 'PUSH',
                );
        $dataKey = array();
        if (!empty($sqlDetail)) {
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, implode(' union all ', $sqlDetail));
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi edit_hari_libur gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function hapus_hari_libur($params, $output)
    {
        $output_arr = json_decode($output, true);
        $sqlDetail = array();
        foreach ($output_arr as $v) {
            if ($v['status']) {
                $tgl = $v['tgl'];
                $sqlDetail[] = <<<SQL
				select :key1 as sinkronisasi
					, 'D' as aksi
					, 'm_kalender' as tabel
					,'{"tanggal" : "{$tgl}"}' as kunci
					, 0 status_identity
SQL;
            }
        }

        $datatransaksi = array(
                            'transaksi' => 'hapus_hari_libur',
                            'asal' => $this->idFarm,
                            'tujuan' => '*',
                            'aksi' => 'PUSH',
                );
        $dataKey = array();
        if (!empty($sqlDetail)) {
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, implode(' union all ', $sqlDetail));
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi hapus_hari_libur gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function proses_persetujuan_retur_pakan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['result'] == 'success') {
            $no_retur = $output_arr['content'];

            $sqlDetail = <<<QUERY
			select :key1 as sinkronisasi
				, 'U' as aksi
				, 'retur_kandang' as tabel
				,'{"no_retur":"'+no_retur+'","no_reg":"'+no_reg+'"}' as kunci
				, 0 status_identity
			from retur_kandang
			where no_retur = :key2
			union all
			select :key1 as sinkronisasi
			, 'U' as aksi
			, 'retur_kandang_d' as tabel
			,'{"no_retur":"'+no_retur+'","no_reg":"'+no_reg+'","kode_barang":"'+kode_barang+'"}' as kunci
			, 0 status_identity
			from retur_kandang_d
			where no_retur = :key2						
QUERY;

            $datatransaksi = array(
                'transaksi' => 'proses_persetujuan_retur_pakan',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => $no_retur);
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'proses_persetujuan_retur_pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function verifikasi_panen_keluar($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $detail_do = $output_arr['detail'];
            $str_do = implode("','", $detail_do);

            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'realisasi_panen' as tabel
					,'{"no_reg" : "'+no_reg+'","no_surat_jalan":"'+no_surat_jalan+'"}' as kunci
					, 0 status_identity
				from realisasi_panen
				where no_do in ('{$str_do}')
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'Verifikasi_DO_Panen' as tabel
					,'{"no_sj" : "'+no_sj+'","kode_farm":"'+kode_farm+'"}' as kunci
					, 0 status_identity
				from Verifikasi_DO_Panen
				where no_do in ('{$str_do}')
				
QUERY;

            $datatransaksi = array(
                'transaksi' => 'verifikasi_panen_keluar',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => '');
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);
        }
    }

    public function importbapdbox($params, $output)
    {
        $output_arr = json_decode($output, true);
        $siklus = $params['POST']['siklus'];
        $farm = $params['POST']['farm'];
        if ($output_arr['status']) {
            // if ($nextstatus == 'A') {
            $sqlDetail = <<<SQL
					select :key1 as sinkronisasi
						, 'U' as aksi
						, 'bap_doc_box' as tabel
						,'{"no_reg" : "'+no_reg+'","no_sj" :"'+no_sj+'","kode_box" : "'+kode_box+'"}' as kunci
						, 0 status_identity
					from bap_doc_box
					where no_reg like '{$farm}/{$siklus}/%'
SQL;

            $datatransaksi = array(
                        'transaksi' => 'importbapdbox',
                        'asal' => $this->idFarm,
                        'tujuan' => $this->serverUtama,
                        'aksi' => 'PUSH',
                    );

            $dataKey = array();
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi import bap_doc_box gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function timbang_doc_simpan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $noreg = $output_arr['detail'];
            $str_noreg = implode("','", $noreg);

            $sqlDetail = <<<QUERY
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'timbang_doc' as tabel
					,'{"no_reg" : "'+no_reg+'"}' as kunci
					, 0 status_identity
				from timbang_doc
				where no_reg in ('{$str_noreg}')
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'timbang_doc_detail' as tabel
					,'{"no_reg" : "'+no_reg+'","no_urut":"'+cast(no_urut  as varchar(5))+'"}' as kunci
					, 0 status_identity
				from timbang_doc_detail
				where no_reg in ('{$str_noreg}')
				
QUERY;

            $datatransaksi = array(
                'transaksi' => 'timbang_doc',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );

            $dataKey = array(':key2' => '');
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi timbang_doc_box gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }

    public function timbang_pakan_simpan($params, $output)
    {
        $output_arr = json_decode($output, true);
        if ($output_arr['status'] == 1) {
            $details = $output_arr['detail'];
            $detailSilo = $output_arr['detailSilo'];
            $sqlDetailNoreg = [];
            foreach($details as $noreg => $noOrders){
                $strNoOrder = implode("','",$noOrders);
                $sqlDetailNoreg[] = <<<QUERY
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'timbang_pakan' as tabel
					,'{"no_reg" : "'+no_reg+'", "no_order" : "'+no_order+'"}' as kunci
					, 0 status_identity
				from timbang_pakan
				where no_reg = '{$noreg}' and no_order in ('{$strNoOrder}')
				union all
				select :key1 as sinkronisasi
					, 'U' as aksi
					, 'timbang_pakan_detail' as tabel
					,'{"no_reg" : "'+no_reg+'", "no_order" : "'+no_order+'","no_urut":"'+cast(no_urut  as varchar(5))+'"}' as kunci
					, 0 status_identity
				from timbang_pakan_detail
				where no_reg = '{$noreg}' and no_order in ('{$strNoOrder}')
                
QUERY;
            }
            if(!empty($detailSilo)){
                foreach($detailSilo as $ds){
                    $noreg = $ds['no_reg'];
                    $nourut = $ds['no_urut'];
                    $sqlDetailNoreg[] = <<<QUERY
                    select :key1 as sinkronisasi
                        , 'U' as aksi
                        , 'timbang_pakan_silo_detail' as tabel
                        ,'{"no_reg" : "'+no_reg+'","no_urut":"'+cast(no_urut  as varchar(5))+'"}' as kunci
                        , 0 status_identity
                    from timbang_pakan_silo_detail
                    where no_reg = '{$noreg}' and no_urut = '{$nourut}'
QUERY;
                }
            }
            

            $sqlDetail = implode(' union all ',$sqlDetailNoreg);

            $datatransaksi = array(
                'transaksi' => 'timbang_pakan',
                'asal' => $this->idFarm,
                'tujuan' => $this->serverUtama,
                'aksi' => 'PUSH',
            );
            $dataKey = array(':key2' => '');
            $this->db->trans_begin();
            $this->sinkronisasi->insert($datatransaksi, $dataKey, $sqlDetail);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                log_message('error', 'isi tabel sinkronisasi aksi timbang pakan gagal pada '.date('Y-m-d H:i:s'));
            } else {
                $this->db->trans_commit();
            }
        }
    }
}
