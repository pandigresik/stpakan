<?php

defined('BASEPATH') or exit('No direct script access allowed');

class VerifikasiDOPanen extends REST_Controller
{
    private $decodedToken;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('authorization', 'jwt', 'stpakan'));
        $this->checkToken();
    }

    private function checkToken()
    {
        $headers = $this->input->request_headers();
        $result = false;
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $this->decodedToken = $decodedToken;
                $result = true;
            }
        }

        if (!$result) {
            $this->response('Unauthorized', 401);

            return;
        }
    }

    public function check_post()
    {
        $headers = $this->input->request_headers();
        $output = array('status' => 0, 'content' => '', 'message' => 'Data DO ditolak. Mohon melakukan scan ulang');
        $data = json_decode($this->post('data'), 1);
        $nomerDO = $data['no_do'];
        $do = $this->db->where(array('no_do' => $nomerDO))
                            ->get('realisasi_panen_do')->row_array();
        if (!empty($do)) {
            /* pastikan do ini belum ada di Verifikasi_DO_Panen */
            $sudahVerifikasi = $this->db->where(array('no_do' => $nomerDO))->get('Verifikasi_DO_Panen')->row_array();
            if (empty($sudahVerifikasi)) {
                $convertDO = array(
                    'no_sj' => $do['NO_SJ'],
                    'no_do' => $do['NO_DO'],
                    'kandang' => substr($do['NO_REG'], -2),
                    'tgl_panen' => tglIndonesia($do['TGL_PANEN'], '-', ' '),
                    'jumlah' => angkaRibuan($do['JUMLAH']),
                    'berat' => angkaRibuan($do['BERAT']),
                );
                $output['content'] = json_encode(array('do' => $convertDO));
                $output['status'] = 1;
            } else {
                $output['message'] = 'DO sudah pernah diverifikasi pada '.convertElemenTglWaktuIndonesia($sudahVerifikasi['TGL_VERIFIKASI']);
            }
        }

        $this->response($output, 200);
    }

    /** pastikan nopol masih ada dalam tabel realisasi_panen_do */
    /*check platnomor DO masuk*/
    public function checkNopol_post()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Nopol Ditolak Harap periksa nopol kembali');
        $data = json_decode($this->post('data'), 1);
        $nopol = $data['nopol'];
        $maxHari = 1;
        $diffDay = new DateInterval('P'.$maxHari.'D');
        //$trans			= 'verifikasi_do_panen_'.$data['verify_type'];
        $tgl_date = new Datetime();
        $tgl_date->sub($diffDay);
        $kode_farm = $this->decodedToken->kode_farm;
        //$subSql = 'SELECT no_do FROM realisasi_panen WHERE tgl_panen >= \''.$tgl_date->format('Y-m-d').'\' and no_reg like \'%'.$kode_farm.'%\'';
        $subSql = 'select TOP(1) RIT from REALISASI_PANEN_DO where NOPOL = \''.$nopol.'\' 
				and TGL_PANEN >= getDate() - 1 and TGL_PANEN <= getDate() + 1 
				order by TGL_PANEN,RIT ASC';
        $subTgl = 'select TOP(1) TGL_PANEN from REALISASI_PANEN_DO
				where NOPOL = \''.$nopol.'\' 
				and NO_DO NOT IN (select NO_DO from verifikasi_do_panen where verifikasi_do_panen.nopol =  REALISASI_PANEN_DO.NOPOL
					and verifikasi_do_panen.NO_DO = REALISASI_PANEN_DO.NO_DO)
				and TGL_PANEN >= getDate() - 1 and TGL_PANEN <= getDate() + 1  
				order by TGL_PANEN ASC';

        $cekMasuk = $this->db->where(array('nopol' => $nopol, 'kode_farm' => $kode_farm))
                        ->where('(TGL_VERIFIKASI IS NULL OR TGL_VERIFIKASI_SJ IS NULL)')
                        //->where('TGL_VERIFIKASI_SJ')
                        ->get('verifikasi_do_panen')->result();

        if (!empty($cekMasuk)) {
            $output['status'] = 0;
            $output['message'] = 'Kendaraan sudah di verifikasi masuk';
        } else {
            $cekData = $this->db->where(array('nopol' => $nopol, 'kode_farm' => $kode_farm))
                        ->where('tgl_panen = ('.$subTgl.')')
                        ->where('rit = ('.$subSql.')')
                        ->get('realisasi_panen_do')->result_array();

            if (!empty($cekData)) {
                $output['status'] = 1;
                $output['content'] = json_encode($cekData);
            }
        }
        /*}*/
        $this->response($output, 200);
    }

    /*end check platnomor DO masuk*/

    /*check platnomor DO keluar*/
    public function checkVerifikasiDO_post()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Nopol Ditolak Harap periksa nopol kembali');
        $data = json_decode($this->post('data'), 1);
        $nopol = $data['nopol'];
        $kode_farm = $this->decodedToken->kode_farm;
        $maxHari = 1;

        $cekMasuk = $this->db->where(array('NOPOL' => $nopol))
                        ->where('user_verifikasi IS NOT NULL')
                        ->where('tgl_verifikasi IS NOT NULL')
                        ->where('user_verifikasi_sj IS NULL')
                        ->where('tgl_verifikasi_sj IS NULL')
                        ->get('verifikasi_do_panen')->result_array();

        if (!empty($cekMasuk)) {
            $nodo = array();
            $num = 0;
            foreach ($cekMasuk as $dt) {
                $nodo[$num] = array('rp.no_do' => $dt['NO_DO']);
                ++$num;
            }
            if ($num == 1) {
                $cekSelesai = $this->db->where($nodo[0])->get('realisasi_panen as rp')->result_array();
            } else {
                $cekSelesai = $this->db->where($nodo[0])->or_where($nodo[1])->get('realisasi_panen as rp')->result_array();
            }

            if (!empty($cekSelesai) && count($cekSelesai) == $num) {
                if ($num == 1) {
                    $getDetail = $this->db->select('rpd.*, rp.JUMLAH_AKTUAL, rp.BERAT_AKTUAL, vdp.TGL_VERIFIKASI')
                            ->join('realisasi_panen as rp', 'rpd.no_do = rp.no_do', 'left')
                            ->join('verifikasi_do_panen as vdp', 'vdp.no_do = rpd.no_do', 'left')
                            ->where($nodo[0])
                            ->get('realisasi_panen_do as rpd')->result_array();
                } else {
                    $getDetail = $this->db->select('rpd.*, rp.JUMLAH_AKTUAL, rp.BERAT_AKTUAL, vdp.TGL_VERIFIKASI')
                            ->join('realisasi_panen as rp', 'rpd.no_do = rp.no_do', 'left')
                            ->join('verifikasi_do_panen as vdp', 'vdp.no_do = rpd.no_do', 'left')
                            ->where($nodo[0])
                            ->or_where($nodo[1])
                            ->get('realisasi_panen_do as rpd')->result_array();
                }
                if (!empty($getDetail)) {
                    $output = array('status' => 1, 'content' => json_encode($getDetail));
                }
            } else {
                $output = array('status' => 0, 'content' => '', 'message' => 'Kendaraan belum selesai panen');
            }
        } else {
            $output = array('status' => 0, 'content' => '', 'message' => 'Kendaraan belum di verifikasi masuk');
        }
        $this->response($output, 200);
    }

    /*end check platnomor DO keluar*/

    /*verifikasi DO masuk*/
    public function verify_post()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Data gagal disimpan');
        $data = json_decode($this->post('data'), 1);
        $sopir = $data['nama_sopir'];
        $nopol = $data['nopol'];
        $detail = json_decode($data['detail']);
        $kode_pegawai = $this->decodedToken->kode_user;
        $kode_farm = $this->decodedToken->kode_farm;
        $sekarang = (new \DateTime())->format('Y-m-d H:i:s');
        $image = $data['image'];
        //$no_do = $data['no_do'];
        $path_baru = 'file_upload/plat_nomer';
        if (!file_exists($path_baru)) {
            mkdir($path_baru);
        }

        $path_baru_photo = $path_baru.'/'.date('YmdHis').'.jpg';
        file_put_contents($path_baru_photo, base64_decode($image));
        $nopol = trim(preg_replace('/\s+/', '', $nopol));

        /*app finger & webservice*/
        if ($data['app'] == 'finger') {
            foreach ($detail as $do) {
                $vdp = array(
                        'kode_farm' => $kode_farm,
                        'nopol' => $nopol,
                        'no_do' => $do->no_do,
                        'no_sj' => $do->no_sj,
                        'nama_sopir' => $sopir,
                        'user_verifikasi' => $this->decodedToken->kode_user,
                        'tgl_verifikasi' => $sekarang,
                        'photo' => $path_baru_photo,
                    );
                $ins = $this->db->insert('verifikasi_do_panen', $vdp);
                /* insert juga ke realisasi_panen */
                $sqlRealisasiPanen = <<<SQL
                INSERT INTO REALISASI_PANEN (NO_SURAT_JALAN,NO_DO,NO_REG,TGL_PANEN,UMUR_PANEN,TGL_DATANG,USER_BUAT)
                SELECT rpd.no_sj,rpd.no_do,rpd.no_reg,CAST(rpd.tgl_panen AS DATE) TGL_PANEN
                    ,datediff(day,(SELECT ks.tgl_doc_in FROM KANDANG_SIKLUS ks WHERE ks.NO_REG = rpd.no_reg),CAST(rpd.tgl_panen AS date)) UMUR
                    ,GETDATE()
                    ,(SELECT top 1 pengawas FROM M_PLOTING_PELAKSANA WHERE NO_REG = rpd.no_reg) 
                FROM REALISASI_PANEN_DO rpd WHERE NO_DO = '{$do->no_do}'
SQL;

                $this->db->query($sqlRealisasiPanen);
                //log_message('error',$sqlRealisasiPanen);
                if ($ins) {
                    $output['status'] = 1;
                    $output['message'] = 'DO berhasil diverifikasi';
                }
            }
        }
        /*end app finger & webservice*/

        $this->response($output, 200);
    }

    /*end verifikasi DO masuk*/

    /*verifikasi DO Keluar*/
    public function verifikasiDOkeluar_post()
    {
        $output = array('status' => 0, 'content' => '', 'message' => 'Data gagal diverifikasi');
        $data = json_decode($this->post('data'), 1);
        $kode_pegawai = $this->decodedToken->kode_user;
        $kode_farm = $this->decodedToken->kode_farm;
        $app = $data['app'];
        $sekarang = (new \DateTime())->format('Y-m-d H:i:s');
        $detail = json_decode($data['detail']);
        $selesai = false;
        $do_arr = array();
        /*app finger*/
        if ($app == 'finger') {
            foreach ($detail as $do) {
                $param = array('NO_DO' => $do->no_do);
                array_push($do_arr,$do->no_do);
                $update = array(
                    'USER_VERIFIKASI_SJ' => $kode_pegawai,
                    'TGL_VERIFIKASI_SJ' => $sekarang,
                );
                $verify_keluar = $this->db->where($param)->update('verifikasi_do_panen', $update);

                if ($verify_keluar) {
                    $output['status'] = 1;
                    $output['content'] = $sekarang;
                    $output['detail'] = $do_arr;
                    $output['message'] = 'DO keluar berhasil diverifikasi';
                }
            }
        }
        /*end app finger*/
        $this->response($output, 200);
    }

    /*end verifikasi DO keluar*/

    /*check finger sopir - absent*/
    public function checkFingerSopir_post()
    {
        $output = array('status' => 0, 'content' => '', 'message' => '');
        $data = json_decode($this->post('data'), 1);
        $kode_sopir = $data['kode_sopir'];

        $sql = 'select TOP(1) attend.*, sopir.NAME as ID_SOPIR, sopir.NAME as NAMA_SOPIR 
								from [attendance].[dbo].[CHECKINOUT] as attend
								left join [attendance].[dbo].[USERINFO] sopir on attend.USERID = sopir.USERID
								where attend.CREATED_AT >= dateadd(second, -5, getDate())
								-- and attend.verifikasi IS NULL
								order by attend.CREATED_AT DESC';
        $getFinger = $this->db->query($sql)->result();
        $output['message'] = json_encode($getFinger);
        if (!empty($getFinger)) {
            if ($getFinger[0]->ID_SOPIR == $kode_sopir) {
                /*
                $sql = 'update [attendance].[dbo].[CHECKINOUT] set verifikasi = 1 where
                        USERID = \''.$getFinger[0]->USERID.'\' and CHECKTIME = \''.$getFinger[0]->CHECKTIME.'\'
                        and verifikasi IS NULL';
                $verified = $this->db->query($sql);
                if ($verified) {
                    $output['status'] = 1;
                }*/
                $output['status'] = 1;
            }
        }

        $this->response($output, 200);
    }

    /*end check finger sopir - absent*/
}
