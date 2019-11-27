<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email extends MY_Controller{
	public function __construct(){
		parent::__construct();
		$this->load->config('email');
		$config = $this->config->item('email_from');
		$config['_bit_depths'] = array('7bit', '8bit','base64');
		$config['_encoding'] = 'base64';
		$this->load->library('email',$config);
		
	//	$this->load->library('email');
		$this->load->helper('stpakan');
		$this->email->initialize($config);
	}

	public function send_email($subject,$alias_from,$from,$to,$message){
		$this->email->set_newline("\r\n");

		$this->email->from($from, $alias_from);

		$this->email->to(implode(',',$to));
		$this->email->subject($subject);
		$this->email->message($message);
		return $this->email->send();
	}

	public function email_op($nomerOP,$return = false){
		/* ambil data header OP */
		$this->load->model('permintaan_pakan/m_op','op');
		$header_op = $this->op->header_op($nomerOP)->row_array();
		$tgl_kirim = explode(',',$header_op['tgl_kirim']);
		$y = array();
		foreach($tgl_kirim as $x){
			array_push($y,convertElemenTglIndonesia(trim($x)));
		}
		$y = implode(' , ',$y);
		$subject = <<<sbj
		"[{$nomerOP}] Permintaan pengiriman pakan untuk tanggal {$y} dengan tujuan pengiriman {$header_op['nama_farm']}"
sbj;

		$from = "breeding@wonokoyo.co.id";
		$alias_from = "Team Support";
		$to = $this->config->item('email_pp_final');
		$message = <<<body
<div><h3>Tim Logistik,</h3></div>
<div>
Terkait dengan adanya order pembelian dengan nomor <strong><em>{$nomerOP}</em></strong>, mohon agar dipersiapkan ekspedisi<br />
untuk pengambilan pakan di feedmill pada tanggal  <strong><em>{$y}</em></strong> untuk dikirim ke Breeding<br />
Farm <strong><em>{$header_op['nama_farm']}</em></strong>.<br />
</div>
<br />
<div>
Terima kasih,<br />
Aplikasi ST Pakan<br />
</div>
<br />
<div>
Perhatian:<br />
Email ini dikirim otomatis oleh sistem WJC-ST Pakan, mohon tidak dibalas.<br />
</div>
body;

		if($this->send_email($subject, $alias_from, $from, $to, $message)){
			$result =  1;
		}
		else $result = 0;
		if($return){
			return $result;
		}
		else{
			$data['status'] = $result;
			$data['message'] = 'Email berhasil dikirim';
			if(!$result){
				$data['message'] = $this->email->print_debugger();
			}

			echo json_encode($data);
		}
}
public function email_op_budidaya($nomerPP,$return = false){
	/* ambil data header OP */
	$this->load->model('permintaan_pakan/m_op','op');
	$header_op = $this->op->header_op_budidaya($nomerPP)->result_array();
	$result = 0;
	foreach($header_op as $ho){
		$tgl_kirim = explode(',',$ho['tgl_kirim']);
		$y = array();
		foreach($tgl_kirim as $x){
			array_push($y,convertElemenTglIndonesia(trim($x)));
		}
		$y = implode(' , ',$y);
		$nomerOP = $ho['no_op'];
		$subject = <<<sbj
			"[{$nomerOP}] Permintaan pengiriman pakan untuk tanggal {$y} dengan tujuan pengiriman {$ho['nama_farm']}"
sbj;

		$from = "budidaya@wonokoyo.co.id";
		$alias_from = "Team Support";
		$to = $this->config->item('email_op');
		$message = <<<body
<div><h3>Tim Logistik,</h3></div>
<div>
Terkait dengan adanya order pembelian dengan nomor <strong><em>{$nomerOP}</em></strong>, mohon agar dipersiapkan ekspedisi<br />
untuk pengambilan pakan di feedmill pada tanggal  <strong><em>{$y}</em></strong> untuk dikirim ke Budidaya<br />
Farm <strong><em>{$ho['nama_farm']}</em></strong>.<br />
</div>
<br />
<div>
Terima kasih,<br />
Aplikasi ST Pakan<br />
</div>
<br />
<div>
Perhatian:<br />
Email ini dikirim otomatis oleh sistem WJC-ST Pakan, mohon tidak dibalas.<br />
</div>
body;

		if($this->send_email($subject, $alias_from, $from, $to, $message)){
			$result++;
		}
	}

		if($return){
			return $result == count($header_op) ? 1 : 0;
		}
		else{
			$data['status'] = $result == count($header_op) ? 1 : 0;
			$data['message'] = 'Email berhasil dikirim';
			if(!$result){
				$data['message'] = $this->email->print_debugger();
			}

			echo json_encode($data);
		}
	}

	public function send_notification($route,$params,$output){
		/* output dalam format json */
		$output_arr = json_decode($output,true);
		if($output_arr['status']){
			switch($route){
				/* kirim ke ppic ketika aktivasi kandang oleh kadiv */
				case 'forecast/forecast/approveRejectKonfirmasiDOCIn':
					$aksi = $params['POST']['aksi'];
					$kodefarm = $params['POST']['kode_farm'];
					$tgldocin = $params['POST']['tgl_docin'];
					$user_level = $this->session->userdata('level_user');
					if($aksi == 'approve'){
						if($user_level == 'KDV'){
							$farm = $this->db->select('nama_farm')->where(array('kode_farm'=>$kodefarm))->get('m_farm')->row_array();
							$periode_siklus= $this->db->select('mp.periode_siklus')->join('kandang_siklus ks','ks.kode_siklus = mp.kode_siklus and ks.kode_farm = \''.$kodefarm.'\' and ks.tgl_doc_in = \''.$tgldocin.'\'')->get('m_periode mp')->row_array();
							$subject = <<<sbj
								[{$farm['nama_farm']} {$periode_siklus['periode_siklus']}] Pengajuan Forecast untuk Farm {$farm['nama_farm']}
sbj;
							$from = "budidaya@wonokoyo.co.id";
							$alias_from = "Team Support";
							$to = $this->config->item('email_aktivasi_siklus');
							$data = array('nama_farm' => $farm['nama_farm']);
							$message = $this->load->view('client/email/aktivasi_kandang',$data,true);
							if(!$this->send_email($subject, $alias_from, $from, $to, $message)){
								log_message('error','Notifikasi aktivasi kandang gagal');
							}
						}
					}
					break;
			/* kirim ke kadept ketika pp perlu tindak lanjut kadept */
			case 'permintaan_pakan/permintaan_pakan/simpan_pp':
			$statusLpb = $params['POST']['statusLpb'];
			if($statusLpb == 'N'){
				$nomerPP = $output_arr['content'];
				$header = $this->db->select('mf.nama_farm,l.tgl_kirim')->where(array('l.no_lpb'=>$nomerPP))->join('m_farm mf','mf.kode_farm = l.kode_farm')->get('lpb_d l')->row_array();
				$result = 0;

					$tgl_kirim = explode(',',$header['tgl_kirim']);
					$y = array();
					foreach($tgl_kirim as $x){
						array_push($y,convertElemenTglIndonesia(trim($x)));
					}
					$tanggal_kirim = implode(' , ',$y);
					$subject = <<<sbj
						"[{$nomerPP}] Permintaan pengiriman pakan untuk tanggal {$tanggal_kirim} dengan tujuan pengiriman {$header['nama_farm']}"
sbj;
					$from = "budidaya@wonokoyo.co.id";
					$alias_from = "Team Support";
					$to = $this->config->item('email_pp_rilis');
					$data = array('nama_farm' => $farm['nama_farm'],'nomerPP' => $nomerPP, 'tanggal_kirim' => $tanggal_kirim);
					$message = $this->load->view('client/email/pp_rilis',$data,true);
					if(!$this->send_email($subject, $alias_from, $from, $to, $message)){
						log_message('error','Notifikasi tindak lanjut nomer pp '.$nomerPP.' kadept gagal');
					}

			}
				break;
			/* kirim ke ppic ketika pp approve oleh kadiv */
			case 'permintaan_pakan/permintaan_pakan/approve_pp_budidaya':
				$nomerPP = $params['POST']['no_pp'];
				$header_op = $this->op->header_op_budidaya($nomerPP)->result_array();
				$result = 0;
				foreach($header_op as $ho){
					$tgl_kirim = explode(',',$ho['tgl_kirim']);
					$nomerOP = $ho['no_op'];
					$y = array();
					foreach($tgl_kirim as $x){
						array_push($y,convertElemenTglIndonesia(trim($x)));
					}
					$tanggal_kirim = implode(' , ',$y);
				$subject = <<<sbj
					[{$nomerOP}] Permintaan pengiriman pakan untuk tanggal {$tanggal_kirim} dengan tujuan pengiriman {$ho['nama_farm']}
sbj;
				$from = "budidaya@wonokoyo.co.id";
				$alias_from = "Team Support";
				$to = $this->config->item('email_pp_final');
				$data = array('nama_farm' => $farm['nama_farm'],'nomerOP' => $nomerOP, 'tanggal_kirim' => $tanggal_kirim);
				$message = $this->load->view('client/email/pp_final',$data,true);
				if(!$this->send_email($subject, $alias_from, $from, $to, $message)){
					log_message('error','Notifikasi pp final ppic nomer pp '.$nomerOP.' gagal');
				}
			}
			break;
			}
		}
	}

	public function tes_email(){
		//echo Modules::run('client/email/email_op','00003/15');
		// cetak_r($this->do_pdf('GD18-G0063'));
		$subject = <<<sbj
			percobaan email

sbj;
		$from = "budidaya@wonokoyo.co.id";
		$alias_from = "Team Support";
		$to = array('afandi@wonokoyo.co.id','ahmad.afandi85@gmail.com');
		$message = '<div>Nyoba cah link </div>';
		print_r($this->send_email($subject,$alias_from,$from,$to,$message));
		//
		// $to = array('muslam@wonokoyo.co.id');
	//	 $message = '<div>Nyoba cah link </div>';
		// 
		// $this->email->attachBase64($this->do_pdf('GD18-G0063'),'attachment','report.pdf','application/pdf');
	//	 $this->send_email($subject,$alias_from,$from,$to,$message);
	}

}
