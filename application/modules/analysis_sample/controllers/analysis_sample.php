<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analysis_sample extends MY_Controller {

    
    public $user;
    public function __construct(){
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(array('m_analysis_sample'));
        $this->load->helper(array('common'));
        $this->user = $this->session->userdata('username');
    }
    
    public function index(){
       // $nav = Modules::run('user/home/nav');
        
        $data['nav'] = $this->getNav();
        
        $data['data_sampel'] = $this->m_analysis_sample->get_data_sampel(1,1,0);
        $data['data_bahan_baku'] = $this->m_analysis_sample->get_bahan_baku();


        $data['as_print'] = Modules::run('user/user/hasPermission','analysis_sample/analysis_sample/print_sample') ? '' : 'hide';
        $data['as_entry'] = Modules::run('user/user/hasPermission','analysis_sample/analysis_sample/entry') ? '' : 'hide';
        $data['as_review'] = Modules::run('user/user/hasPermission','analysis_sample/analysis_sample/review') ? '' : 'hide';

        $this->smartyii->view('analysis_sample',$data);
    }

    public function filter(){
        $checkbox1 = $this->input->post('option1');
        $checkbox2 = $this->input->post('option2');
        $checkbox3 = $this->input->post('option3');
        $data_sampel = $this->m_analysis_sample->get_data_sampel($checkbox1,$checkbox2,$checkbox3);
        echo json_encode($data_sampel);
    }
    
    public function entry(){
    //    $nav = Modules::run('user/home/nav');
        
        $data['nav'] = $this->getNav();
        $data['status_sp_entry'] = $this->input->post('status_sp_entry');
        $data['status_nonsp_entry'] = $this->input->post('status_nonsp_entry');
        $sampel = $this->input->post('sample_entry');
        $data['no_sampel'] = $sampel;
        $analisa = $this->m_analysis_sample->get_analisa();
        $composit = $this->input->post('composit_entry');
        $data['composit'] = $composit;
        $data['detail_sample'] = $this->m_analysis_sample->get_detail_sampel($sampel,$composit);
        foreach($data['detail_sample'] as $key=>$value){
            $data['detail_sample'][$key]['list_keterangan'] = $analisa[$value['id_jenis']][$value['jenis']][$value['id_parameter']][$value['parameter']];
        }
        $this->smartyii->view('entry_analysis_sample',$data);
    }
    
    public function detail(){
    //    $nav = Modules::run('user/home/nav');
        
        $data['nav'] = $this->getNav();
        $data['status_sp'] = $this->input->post('status_sp');
        $data['status_nonsp'] = $this->input->post('status_nonsp');
        $data['lengkap'] = $this->input->post('lengkap');
        $sampel = $this->input->post('sample_detail');
        $data['no_sampel'] = $sampel;
        $composit = $this->input->post('composit_detail');
        $data['composit'] = $composit;
        $data['detail_sample'] = $this->m_analysis_sample->get_detail_sampel($sampel,$composit);
        $this->smartyii->view('detail_analysis_sample',$data);
    }
    
    public function review(){
    //    $nav = Modules::run('user/home/nav');
        
        $data['nav'] = $this->getNav();
        //$listRmCode = $this->config->item('rm_code_oracle');
        $sampel = $this->input->post('sample_review');
        $data['id_vdni'] = $this->input->post('vdni_review');
        $data['no_sampel'] = $sampel;
        $composit = $this->input->post('composit_review');
        $data['composit'] = $composit;
        $data['detail_sample'] = $this->m_analysis_sample->get_detail_sampel($sampel,$composit);
        //$keyRmCode = array_search($data['detail_sample'][0]['origin'],$listRmCode);
        //$data['rm_oracle'] = $this->m_analysis_sample->get_data_rm($keyRmCode);
        $this->smartyii->view('review_analysis_sample',$data);
    }
    
    public function get_detail_komposit(){
        $sampel = $this->input->post('sample');
        $detail_sample = $this->m_analysis_sample->get_detail_sampel_komposit($sampel);
        echo json_encode($detail_sample);
    }

    public function save(){
        $result['message'] = 0;
        $sample     = $this->input->post('no_sampel');
        $id_rasi    = $this->input->post('id');
        $id_sar     = $this->input->post('id_sar');
        $hasil      = $this->input->post('hasil');
        $keterangan = $this->input->post('keterangan');
        $important  = $this->input->post('important');
        for($i=0;$i<count($id_rasi);$i++){
            #echo $sample.' , '.$id_rasi[$i].' , '.$hasil[$i].' , '.$keterangan[$i].'<br>';
            if(($hasil[$i] == '') && empty($keterangan[$i])){
                $sample_analysis_result[] = array();
            }
            else{
                if(empty($id_sar[$i])){
                    $sample_analysis_result[] = $this->m_analysis_sample->insert_sample_analysis_result($sample,$id_rasi[$i],$hasil[$i],$keterangan[$i]);
                }
                else{    
                    $sample_analysis_result[] = $this->m_analysis_sample->edit_sample_analysis_result($id_sar[$i],$hasil[$i],$keterangan[$i]);
                
                }
            }
        }
        if(count($sample_analysis_result)>=1){
            $result['message'] = 1;
            $result['list_id_sar']  = $sample_analysis_result;
            $entry_count = $this->m_analysis_sample->check_entry_count($sample,$important);
            if($entry_count['result'] == 1){
                $this->m_analysis_sample->update_last_entry_sample($sample,$important) ;
            }
        }
        echo json_encode($result);
    }
    
    public function re_approve_sp(){
        $actor = $this->user;
        $result['message'] = 0;
        $sample     = $this->input->post('no_sampel'); 
        $composit   = $this->input->post('composit');   
        
        $vendor_delivery_note_item = $this->input->post('inputVdni');
        $vehicle_segment  = $this->input->post('inputVehicleSegment');

        $active_memo = $this->m_analysis_sample->get_active_memo($sample); 
        if($active_memo['special'] != 1 || $active_memo['special'] != '1'){
            $result['message'] = 2;
        }
        else{
            if($composit != 1){
                $update_ras_id_sample = $this->m_analysis_sample->update_sample($active_memo['ras_id'],$sample,'rm_acceptance_standard'); 
                if(empty($update_ras_id_sample['rm_acceptance_standard'])){
                    $result['message'] = 3;
                }
                else{
                    $system_recommendation = $this->m_analysis_sample->get_system_recommendation($sample);
                    $count = 0;
                    foreach($system_recommendation as $key=>$value){
                        #echo '('.$value['hasil'].'&'.$value['keterangan'].')';
                        if($value['important']){
                            ($value['hasil']==0 || $value['keterangan']==0) ? $count = $count+1 : $count = $count;
                        }
                        #echo '('.$count.')';
                    }
                    ($count==0) ? $this->m_analysis_sample->update_acceptance_verdict($sample,'ACCEPT') : $this->m_analysis_sample->update_acceptance_verdict($sample,'REJECT');
                    
                    $acceptance_verdict = ($count==0) ? $this->m_analysis_sample->update_acceptance_verdict_final($sample,'ACCEPT') : $this->m_analysis_sample->update_acceptance_verdict_final($sample,'REJECT');
                    
                    $cek_ip = $this->m_analysis_sample->ceck_item_placement($vendor_delivery_note_item,$vehicle_segment);
                    if(count($cek_ip)>=1){
                        $this->m_analysis_sample->update_item_placement($vendor_delivery_note_item,$vehicle_segment,$acceptance_verdict['verdict']);
                    }
                    //KURANG UPDATE T_SAMPEL1_HDR
                }
            }
            else{
                $result['message'] = 1;
            }

        }
        echo json_encode($result);
    }

    public function approve_sp(){
        $actor = $this->user;
        $result['message'] = 0;
        $sample     = $this->input->post('no_sampel'); 
        $composit   = $this->input->post('composit');   
        
        $vendor_delivery_note_item = $this->input->post('inputVdni');
        $tipe = $this->input->post('inputTipe');
        $vehicle_segment  = $this->input->post('inputVehicleSegment'); 

        //$klasifikasi_rm  = $this->input->post('inputKlasisfikasiBahanBaku');
        
        $vendor = $this->input->post('inputVendor');

        //if($composit != 1){
        if(($tipe != 'RIB')&&($tipe != 'OPI')){
            $system_recommendation = $this->m_analysis_sample->get_system_recommendation($sample);
            $count = 0;
            foreach($system_recommendation as $key=>$value){
                #echo '('.$value['hasil'].'&'.$value['keterangan'].')';
                if($value['important']){
                    ($value['hasil']==0 || $value['keterangan']==0) ? $count = $count+1 : $count = $count;
                }
                #echo '('.$count.')';
            }
            $acceptance_verdict = ($count==0) ? $this->m_analysis_sample->insert_acceptance_verdict($sample,'ACCEPT') : $this->m_analysis_sample->insert_acceptance_verdict($sample,'REJECT');
            //(count($acceptance_verdict)>=1) ? $result['message'] = 1 : $result['message'] = 2;
        }
        else{
            $acceptance_verdict = $this->m_analysis_sample->insert_acceptance_verdict($sample,'ACCEPT');
        }

        $result['approve'] = $acceptance_verdict;
        $sp_review = array();
        
        $r = '';
        
        if(isset($acceptance_verdict['system_recommendation'])){
            $actor_id  = $this->m_analysis_sample->insert_actor($actor,'posted');
            $sp_review = $this->m_analysis_sample->update_sample($actor_id['id'],$sample,'sp_review');
            $this->m_analysis_sample->update_acceptance_verdict_final($sample,$acceptance_verdict['system_recommendation']);
            
            //if($composit != 1){
            if(($tipe != 'RIB')&&($tipe != 'OPI')){
                #if($vendor == 12 || $vendor == '12'){
                #}
                #else{
                    $check_acceptance_verdict = $this->m_analysis_sample->check_acceptance_verdict($sample);
                    $tmp_total = 0;
                    foreach($check_acceptance_verdict as $key => $value){
                        ($value['result']==0) ? $tmp_total = $tmp_total + 1 : $tmp_total = $tmp_total ;
                    }
                    
                    if($vendor == 12 || $vendor == '12'){
                        $tmp_total = 0;
                    }
                    
                    if($tmp_total==0){
                        $id_vendor_delivery_note = '';
                        foreach($check_acceptance_verdict as $key => $value){
                            
                            if($vendor != 12 || $vendor != '12'){
                                #$value['av_verdict'] = 'ACCEPT';
                                $this->m_analysis_sample->update_item_placement($vendor_delivery_note_item,$value['vehicle_segment'],$value['av_verdict']);
                            }
                            
                            $data_bhnbaku = $this->m_analysis_sample->get_data_to_bhnbaku($value['no_sampel']);
                            //echo print_r($data_bhnbaku);
                            $this->m_analysis_sample->insert_NO_SAMPEL1(convertNomerSample($value['no_sampel']),$value['no_sampel'],$data_bhnbaku['no_plat'],$data_bhnbaku['tiket_masuk'],$data_bhnbaku['user_lab']);
                            $r .= $this->m_analysis_sample->check_data('NO_SAMPEL1','NO_SAMPEL1STR',$value['no_sampel']);
                            $this->m_analysis_sample->insert_T_SAMPEL1_HDR($value['no_sampel'],$data_bhnbaku['tiket_masuk'],$data_bhnbaku['bahan_baku'],
                                $data_bhnbaku['ambil_sampel'],$data_bhnbaku['nik'],$data_bhnbaku['jumlah_sampel'],$data_bhnbaku['keputusan_akhir'],$data_bhnbaku['keterangan_qc'],
                                $data_bhnbaku['approve'],$data_bhnbaku['user_lab'],$data_bhnbaku['waktu_lab'],$data_bhnbaku['user_lab'],$data_bhnbaku['keputusan_akhir'],$data_bhnbaku['pecah']);
                            $r .= $this->m_analysis_sample->check_data('T_SAMPEL1_HDR','NO_SAMPEL1',$value['no_sampel']);
                            $this->m_analysis_sample->update_T_KENDARAANMASUK($data_bhnbaku['no_sampel'],$data_bhnbaku['tiket_masuk']);
                            $r .= $this->m_analysis_sample->check_data('T_KENDARAANMASUK','NO_SAMPEL1',$value['no_sampel']);
                            $id_vendor_delivery_note = $data_bhnbaku['tiket_masuk'];
                        }

                        $this->m_analysis_sample->update_verdict_stamp($id_vendor_delivery_note);
                        /*
                        if($klasifikasi_rm != 0 || $klasifikasi_rm != '0'){
                            $this->m_analysis_sample->update_rm_T_SAMPEL2_HDR($klasifikasi_rm,$id_vendor_delivery_note);
                        }
                        */
                    }
                #}
            }
        }

        if(isset($sp_review['sp_review'])){
            $result['message'] = 1;
        }

        $result['insert_oracle'] = $r;

        echo json_encode($result);
    }
    
    public function approve_nonsp(){
        $actor = $this->user;
        $result['message'] = 0;
        $sample     = $this->input->post('no_sampel');   
        $composit   = $this->input->post('composit');   
        
        $actor_id  = $this->m_analysis_sample->insert_actor($actor,'posted');
        $nonsp_review = $this->m_analysis_sample->update_sample($actor_id['id'],$sample,'nonsp_review');
            
        (!empty($nonsp_review['nonsp_review'])) ? $result['message'] = 1 : '' ;

        echo json_encode($result);
    }

    public function print_sample_old(){
        $result['message'] = 1;
        /*
        $no_sampel = $this->input->post('no_sampel'); 
        $sampling_start = $this->m_analysis_sample->check_sample_start($no_sampel);
        if(!empty($sampling_start['sampling_start'])){
            $sample = $this->m_analysis_sample->update_sample_printed($no_sampel);
            (!empty($sample['id'])) ? $result['message'] = 1 : '';
        }
        else{
            $result['message'] = 2;
        }*/
        echo json_encode($result);
    }

    public function printed_sample_children(){
        $sample = $this->input->post('sample');
        $result = $this->m_analysis_sample->printed_sample_children($sample);
        echo json_encode(array('result'=>count($result)));
    }

    public function print_sample(){
        $sample = $this->input->post('sample');
        $bahanbaku = $this->input->post('rm');
        $composit = $this->input->post('composit');
		$nopanggil = $this->input->post('panggil');
		$nomerop = $this->input->post('nomerop');

		if(!preg_match("/(RIB)|(OPIIMP).*/", $nomerop)){
			$nomerop = ' ';
		}
		 
        $new_sample = explode('#',$sample);
        $this->m_analysis_sample->update_sample_printed($new_sample[1]);

        $sample_stamp = $this->m_analysis_sample->sample_stamp($new_sample[1]);
        $waktu = $sample_stamp['stamp'];
        $waktu = date('d M Y H:i',strtotime($waktu));

        $this->load->library('Pdf');
        $pdf = new Pdf('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setFontSubsetting(false);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(0, 2, 0, 0);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetAutoPageBreak(TRUE, 1);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        if($composit == 0){
            $resolution= array(70,43);
            $pdf->AddPage('L', $resolution);
        }
        else if($composit == 2){
            $pdf->SetMargins(0, 0.5, 0, 0);
            $resolution= array(70,43);
            $pdf->AddPage('L', $resolution);
        }
        else{
            $list_sample = $this->m_analysis_sample->get_sample_composit($new_sample[1]);
            $nomerop = $list_sample[0]['nomerop']; 
            $tambah = 41+(count($list_sample) * 5);
            $resolution= array(70,$tambah);
            ($tambah<=70) ? $pdf->AddPage('L', $resolution) : $pdf->AddPage('P', $resolution);
        }

        // define barcode style
        $style = array(
            'position' => 'C',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 10,
            'stretchtext' => 4
        );
        $styleLine = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
        if($composit == 0){
            $height = 20;
            $marginLeft = 10;
            $marginTop = 12;
            $pdf->Line(0, .5, 80, .5, $styleLine);
            $pdf->SetFontSize(10);
            $pdf->Write(4,'SAMPEL 1, '.$bahanbaku.'   '.$nomerop, '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Write(4,'Tanggal,Jam Pengambilan Sampel : '.$waktu, '', 0, 'C', true, 0, false, false, 0);
            $pdf->write1DBarcode($sample, 'C128B', $marginLeft,$marginTop,'' ,$height, 1,$style,'N');
            $pdf->SetFontSize(16);
            $pdf->Write(6,'No. Panggil : '.$nopanggil, '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            (strlen($nopanggil) <=3 ) ? $pdf->Rect(8,32, 55, 9) : $pdf->Rect(2,32, 66, 9);
        }
        else if($composit == 2){
            $height = 20;
            $marginLeft = 10;
            $marginTop = 12;
            $pdf->Line(0, .5, 80, .5, $styleLine);
            $pdf->SetFontSize(10);
            $pdf->Write(4,'SAMPEL 1 , '.$bahanbaku.' * '.$nomerop, '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->Write(4,'Tanggal,Jam Pengambilan Sampel : '.$waktu, '', 0, 'C', true, 0, false, false, 0);
            $pdf->write1DBarcode($sample, 'C128B', $marginLeft,$marginTop,'' ,$height, 1,$style,'N');
            $pdf->SetFontSize(16);
            $pdf->Write(6,'No. Panggil : '.$nopanggil, '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetFillColor(255, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            (strlen($nopanggil) <=3 ) ? $pdf->Rect(8,32, 55, 9) : $pdf->Rect(2,32, 66, 9);
        }
        else{
            $height = 20;
            $marginLeft = 10;
            $marginTop = 10;
            $pdf->Line(0, .5, 80, .5, $styleLine);
            $pdf->SetFontSize(10); 
            $pdf->Write(4,'SAMPEL 1, '.$bahanbaku.'   '.$nomerop, '', 0, 'C', true, 0, false, false, 0);
            $pdf->SetFontSize(7);
            $pdf->write1DBarcode($sample, 'C128B', $marginLeft,$marginTop,'' ,$height, 1,$style,'N');
            $pdf->Write(1,'', '', 0, 'C', true, 0, false, false, 0);
            $styleLine = array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0));
            $pdf->Line(0, 30, 80, 30, $styleLine);
            $pdf->SetFontSize(10);
            $pdf->Write(4,'Komposisi : ', '', 0, 'L', true, 0, false, false, 0);
            foreach($list_sample as $key => $value){
                $pdf->Write(3,'S1#'.$value['id'], '', 0, 'C', true, 0, false, false, 0);
            }

        }
        $pdf->Output($sample.'.pdf', 'I');
        
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */