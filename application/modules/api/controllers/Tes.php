<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Changes:
 * 1. This project contains .htaccess file for windows machine.
 *    Please update as per your requirements.
 *    Samples (Win/Linux): http://stackoverflow.com/questions/28525870/removing-index-php-from-url-in-codeigniter-on-mandriva
 *
 * 2. Change 'encryption_key' in application\config\config.php
 *    Link for encryption_key: http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/
 * 
 * 3. Change 'jwt_key' in application\config\jwt.php
 *
 */

class Tes extends MX_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->helper(array('authorization','jwt'));
        $this->load->model('user/m_user','m_user');
    }
    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: GET
     */    

    public function token()
    {   
       // $username = $this->get('username');
       // $password = $this->get('password');
        $tokenData = array();
        $output = array('status' => 0, 'content' => '', 'message' => '');    
        $user = $this->db->query('EXEC dbo.LOGIN_CHECK "danielr","danielr"')->row();
        print_r($user);
        if(!empty($user)){
            $tokenData['kode_user'] = $user['KODE_PEGAWAI'];        
            $tokenData['level_user'] = $user['GRUP_PEGAWAI'];
            $tokenData['kode_farm'] = $user['KODE_FARM'];           
            $output['content'] = AUTHORIZATION::generateToken($tokenData); 
            $output['status'] = 1;
        }else{
            $output['message'] = 'Username atau password salah';
        }                
     //   $this->response($output, 200);
    }

    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     */
    public function token_post()
    {
        $headers = $this->input->request_headers();

        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $this->response($decodedToken, 200);
                return;
            }
        }

        $this->response('Unauthorised', 403);
    }

    /** test generate template json */
    public function generateTemplate(){
        $this->load->library('TemplateOMR');
    //    $mappath = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'LHK'.DIRECTORY_SEPARATOR.'template.json';
        $imagepath = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'LHK';
        $imagefilename = 'tes.jpeg';
        $template = new TemplateOMR();
        $template->setArrayMap($this->generateMap());
        //$template->setup($mappath, $imagepath,$imagefilename);
        $template->setup($imagepath,$imagefilename);
    }

    public function compareMap(){
        $imagepath = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'LHK'.DIRECTORY_SEPARATOR.'exam.jpg';
        
        $image = new Imagick();
        $image->readImage($imagepath);
        $template = new TemplateOMR();
        $template->setArrayMap($this->generateMap());
        $template->compareMap($imagepath);
      
    }

    public function generateMap(){
        /** ukuran A4 adalah 595 x 841 */
        $lebar = 595;
        $tinggi = 841;
        $template = array('expectedwidth' => '595','expectedheight' => '841','groups' => array());
        $marginX = 5;        
        $tmp = array(                        
            'groups' => array(
                    array(
                        'groupname' => 'mati_satuan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 158,
                            'awalPosisiY' => 50,
                            'marginX' => $marginX,
                            'tipe' => 'rectangle'                            
                        )
                    ),
                    array(
                        'groupname' => 'mati_puluhan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 158,
                            'awalPosisiY' => 70,
                            'marginX' => $marginX,
                            'tipe' => 'rectangle'
                        )
                    ),
                    array(
                        'groupname' => 'mati_ratusan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 158,
                            'awalPosisiY' => 90,
                            'marginX' => $marginX,
                            'tipe' => 'rectangle'                   
                        )
                    ),
                    array(
                        'groupname' => 'afkir_satuan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 48,
                            'awalPosisiY' => 150,
                            'marginX' => $marginX,
                            'tipe' => 'circle'
                        )
                    ),
                    array(
                        'groupname' => 'afkir_puluhan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 48,
                            'awalPosisiY' => 170,
                            'marginX' => $marginX,
                            'tipe' => 'rectangle'
                        )
                    ),
                    array(
                        'groupname' => 'afkir_ratusan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 48,
                            'awalPosisiY' => 190,
                            'marginX' => $marginX,
                            'tipe' => 'circle'
                        )
                    ), 
                    array(
                        'groupname' => 'afkir_ratusan',
                        'grouptargets' => array(
                            'jml' => 10,
                            'width' => 15,
                            'height' => 15,
                            'awalPosisiX' => 258,
                            'awalPosisiY' => 190,
                            'marginX' => $marginX,
                            'tipe' => 'circle'
                        )
                    ), 
                )
            );
        foreach($tmp['groups'] as $group){
            $template['groups'][] = $this->generateGroupName($group);
        }
    //    echo '<pre>';
     //   print_r($template);die();
        return $template;
    }

    private function generateGroupName($arr){
        $result = array();
        $result['groupname'] = $arr['groupname'];
        $result['grouptargets'] = array();
        if(!empty($arr['grouptargets'])){
            $jmlElemen = $arr['grouptargets']['jml'];
            $width = $arr['grouptargets']['width'];
            $height = $arr['grouptargets']['height'];
            $awalPosisiX = $arr['grouptargets']['awalPosisiX'];
            $awalPosisiY = $arr['grouptargets']['awalPosisiY'];
            $marginX = $arr['grouptargets']['marginX'];            
            $tipe = $arr['grouptargets']['tipe'];            
            $index = 0;
            while($index < $jmlElemen){                
                $tmp = array(
                    'x' => $awalPosisiX,
                    'y' => $awalPosisiY,
                    'width' => $width,
                    'height' => $height,
                    'id' => $index,
                    'text' => $index,
                    'type' => $tipe
                );
                array_push($result['grouptargets'],$tmp);
                $awalPosisiX += $width + $marginX;                
                $index++;                
            }
        }
        return $result;
    }
    /** file dari hasil scan lalu diconvert online ke jpg */
    public function extractImage(){
        $folder_image = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'rhk'.DIRECTORY_SEPARATOR;
        $image_path = $folder_image.'lhk_A4.jpg';
        $image_width = 88;
        $image_height = 36;
        $new_line = 41;
        $height_header = 70;
        $y_awal = 233;
        $barcode = array(            
            'barcode' => array('x_awal' => 955, 'y_awal' => 85, 'width' => 170),                        
        );
        $sekat = array(            
            'jumlah' => array('x_awal' => 485, 'y_awal' => $y_awal, 'width' => $image_width),            
            'berat' => array('x_awal' => 675, 'y_awal' => $y_awal, 'width' => $image_width * 2)
        );
        $y_awal_populasi = $y_awal + ($new_line * 4) + $height_header;
        $populasi = array(            
            'mati' => array('x_awal' => 348, 'y_awal' => $y_awal_populasi, 'width' => $image_width),            
            'afkir' => array('x_awal' => 900, 'y_awal' => $y_awal_populasi, 'width' => $image_width)
        );
        $y_awal_pakan = $y_awal_populasi + $new_line + $height_header;
        $pakan = array(            
            'pbr1' => array('x_awal' => 990, 'y_awal' => $y_awal_pakan, 'width' => $image_width),            
            'pbr2' => array('x_awal' => 990, 'y_awal' => $y_awal_pakan + $new_line, 'width' => $image_width)
        );
        $y_awal_rekomendasi = $y_awal_pakan + ($new_line * count($pakan)) + $height_header;
        $rekomendasi = array(            
            'rbr1' => array('x_awal' => 1040, 'y_awal' => $y_awal_rekomendasi, 'width' => $image_width),            
            'rbr2' => array('x_awal' => 1075, 'y_awal' => $y_awal_rekomendasi + $new_line, 'width' => $image_width)
        );
        $this->load->library('image_lib');       
        $config_crop['maintain_ratio'] = FALSE;
        $config_crop['source_image'] = $image_path;        
        $config_crop['height'] = $image_height;        
        //for resising we want 70% image quality
        $config_crop['quality'] = '90%';

        foreach($barcode as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();                                        
        }

        foreach($sekat as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            for($i = 0 ; $i < 4 ; $i++){                
                $tmp_image = $k.'_'.$i.'.jpg';
                $config_crop['width'] = $width;
                $config_crop['x_axis'] = $x_axis;
                $config_crop['y_axis'] = $y_axis;
                $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
                $this->image_lib->initialize($config_crop);        
                if ( ! $this->image_lib->crop())
                {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();            
                $y_axis += $new_line;
            }
        }
        foreach($populasi as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();                                        
        }
        foreach($pakan as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();                                        
        }
        foreach($rekomendasi as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();
        }                        
    }
    /** file dari convert online */
    public function extractImage2(){
        $folder_image = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'rhk'.DIRECTORY_SEPARATOR;
        $image_path = $folder_image.'Scan_Pic0075.jpg';
        $image_width = 90;
        $image_height = 52;
        $new_line = 62;
        $height_header = 79;
        #$y_awal = 278;
        $y_awal = 274;
        $barcode = array(            
            'barcode' => array('x_awal' => 810, 'y_awal' => 85, 'width' => 170),                        
        );
        $sekat = array(            
            'jumlah' => array('x_awal' => 653, 'y_awal' => $y_awal, 'width' => $image_width),            
            'berat' => array('x_awal' => 950, 'y_awal' => $y_awal, 'width' => $image_width * 2)
        );
        $y_awal_populasi = $y_awal + ($new_line * 4) + $height_header;
        $populasi = array(            
            'mati' => array('x_awal' => 480, 'y_awal' => $y_awal_populasi, 'width' => $image_width),            
            'afkir' => array('x_awal' => 1170, 'y_awal' => $y_awal_populasi, 'width' => $image_width)
        );
        $y_awal_pakan = $y_awal_populasi + $new_line + $height_header;
        $pakan = array(            
            'pbr1' => array('x_awal' => 1275, 'y_awal' => $y_awal_pakan, 'width' => $image_width),            
            'pbr2' => array('x_awal' => 1275, 'y_awal' => $y_awal_pakan + $new_line, 'width' => $image_width)
        );
        $y_awal_rekomendasi = $y_awal_pakan + ($new_line * count($pakan)) + $height_header;
        $rekomendasi = array(            
            'rbr1' => array('x_awal' => 1340, 'y_awal' => $y_awal_rekomendasi, 'width' => $image_width),            
            'rbr2' => array('x_awal' => 1340, 'y_awal' => $y_awal_rekomendasi + $new_line, 'width' => $image_width)
        );
        $this->load->library('image_lib');       
        $config_crop['maintain_ratio'] = FALSE;
        $config_crop['source_image'] = $image_path;        
        $config_crop['height'] = $image_height;        
        //for resising we want 70% image quality
        $config_crop['quality'] = '90%';

        foreach($barcode as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();                                        
        }

        foreach($sekat as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            for($i = 0 ; $i < 4 ; $i++){                
                $tmp_image = $k.'_'.$i.'.jpg';
                $config_crop['width'] = $width;
                $config_crop['x_axis'] = $x_axis;
                $config_crop['y_axis'] = $y_axis;
                $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
                $this->image_lib->initialize($config_crop);        
                if ( ! $this->image_lib->crop())
                {
                    echo $this->image_lib->display_errors();
                }
                $this->image_lib->clear();            
                $y_axis += $new_line;
            }
        }
        foreach($populasi as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();                                        
        }
        foreach($pakan as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();                                        
        }
        foreach($rekomendasi as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }
            $this->image_lib->clear();
        }                        
    }
    public function extractImage3(){
        $pakanPakai = array('1126-10-11');
        $rekomendasiPakan = array('1126-10-11');
        $folder_image = FCPATH.DIRECTORY_SEPARATOR.'file_upload'.DIRECTORY_SEPARATOR.'rhk'.DIRECTORY_SEPARATOR;
        $image_path = $folder_image.'Scan_Pic0075.jpg';
        $image_width = 90;
        $image_height = 52;
        $new_line = 62;
        $height_header = 79;
        #$y_awal = 278;
        $y_awal = 272;
        $barcode = array(            
            'barcode' => array('x_awal' => 810, 'y_awal' => 85, 'width' => 170),                        
        );
        $sekat = array(            
            'jumlah' => array('x_awal' => 653, 'y_awal' => $y_awal, 'width' => $image_width),            
            'berat' => array('x_awal' => 950, 'y_awal' => $y_awal, 'width' => $image_width * 2)
        );
        $y_awal_populasi = $y_awal + ($new_line * 4) + $height_header;
        $populasi = array(            
            'mati' => array('x_awal' => 480, 'y_awal' => $y_awal_populasi, 'width' => $image_width),            
            'afkir' => array('x_awal' => 1170, 'y_awal' => $y_awal_populasi, 'width' => $image_width)
        );
		$y_awal_pakan = $y_awal_populasi + $new_line + $height_header;
		$pakan = array();
		if(!empty($pakanPakai)){
			$tmp_awal_pakan = $y_awal_pakan;
			foreach($pakanPakai as $_kb){
				$pakan[$_kb] = array('x_awal' => 1275, 'y_awal' => $tmp_awal_pakan, 'width' => $image_width);
				$tmp_awal_pakan += $new_line;
			}
		}
		$rekomendasi = array();
        if(!empty($rekomendasiPakan)){
			$tmp_awal_rekomendasi = $y_awal_pakan + ($new_line * count($pakan)) + $height_header;
			foreach($rekomendasiPakan as $_kb){
				$rekomendasi[$_kb] = array('x_awal' => 1340, 'y_awal' => $tmp_awal_rekomendasi, 'width' => $image_width);
				$tmp_awal_rekomendasi += $new_line;
			}
		}
        
        $this->load->library('image_lib');       
        $config_crop['maintain_ratio'] = FALSE;
        $config_crop['source_image'] = $image_path;        
        $config_crop['height'] = $image_height;        
        //for resising we want 70% image quality
        $config_crop['quality'] = '90%';
		$list_image['sekat'] = array();
        foreach($sekat as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            for($i = 0 ; $i < 4 ; $i++){                
                $tmp_image = $k.'_'.$i.'.jpg';
                $config_crop['width'] = $width;
                $config_crop['x_axis'] = $x_axis;
                $config_crop['y_axis'] = $y_axis;
                $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
                $this->image_lib->initialize($config_crop);        
                if ( ! $this->image_lib->crop())
                {
                    echo $this->image_lib->display_errors();
                }else{
					$list_image['sekat'][$k.'_'.$i] = $config_crop['new_image'];
				}	
                $this->image_lib->clear();            
                $y_axis += $new_line;
            }
		}
		$list_image['populasi'] = array();
        foreach($populasi as $k => $v){
            $x_axis = $v['x_awal'];
            $y_axis = $v['y_awal'];
            $width = $v['width'];            
            $i = 0;                
            $tmp_image = $k.'_'.$i.'.jpg';
            $config_crop['width'] = $width;
            $config_crop['x_axis'] = $x_axis;
            $config_crop['y_axis'] = $y_axis;
            $config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
            $this->image_lib->initialize($config_crop);        
            if ( ! $this->image_lib->crop())
            {
                echo $this->image_lib->display_errors();
            }else{
				$list_image['populasi'][$k.'_'.$i] = $config_crop['new_image'];
			}	
            $this->image_lib->clear();                                        
		}
		if(!empty($pakan)){
			$list_image['pakai'] = array();
			foreach($pakan as $k => $v){
				$x_axis = $v['x_awal'];
				$y_axis = $v['y_awal'];
				$width = $v['width'];            
				$i = 0;                
				$tmp_image = $k.'_'.$i.'.jpg';
				$config_crop['width'] = $width;
				$config_crop['x_axis'] = $x_axis;
				$config_crop['y_axis'] = $y_axis;
				$config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
				$this->image_lib->initialize($config_crop);        
				if ( ! $this->image_lib->crop())
				{
					echo $this->image_lib->display_errors();
				}else{
					$list_image['pakai'][$k.'_'.$i] = $config_crop['new_image'];
				}	
				$this->image_lib->clear();                                        
			}
		}	
		if(!empty($rekomendasi)){
			$list_image['rekom'] = array();
			foreach($rekomendasi as $k => $v){
				$x_axis = $v['x_awal'];
				$y_axis = $v['y_awal'];
				$width = $v['width'];            
				$i = 0;                
				$tmp_image = $k.'_'.$i.'.jpg';
				$config_crop['width'] = $width;
				$config_crop['x_axis'] = $x_axis;
				$config_crop['y_axis'] = $y_axis;
				$config_crop['new_image'] = $folder_image.'scanning'.DIRECTORY_SEPARATOR.$tmp_image;
				$this->image_lib->initialize($config_crop);        
				if ( ! $this->image_lib->crop())
				{
					echo $this->image_lib->display_errors();
				}else{
					$list_image['rekom'][$k.'_'.$i] = $config_crop['new_image'];
				}	
				$this->image_lib->clear();
			}   
		}
    }
    public function bacaImage(){
        $this->load->library('SSOCR');
        $folder_image = 'file_upload/rhk/scanning/';
        $image = $folder_image.'004200_mono.png';     
    //    echo $image;  
        $ssocr = new SSOCR();
        $ssocr->setImage($image);
        echo $ssocr->run();
        
    }

    public function detikSetelah(){
        $tgl = Modules::run('home/home/getDateServer');
        echo $tgl->saatini;
        echo '<br >';
        $tglserver = detikSetelah($tgl->saatini,2);
        echo $tglserver;
    }
}