<?php defined('BASEPATH') OR exit('No direct script access allowed');

class KenaliPlatNomer extends REST_Controller
{

    public function check_post()
	{		
		$output = array('status' => 1,'content' => '','message' => '');   
		$folderPy = APPPATH.'third_party/ALPR/';
		$folder = 'images';
		$folder_upload = $folderPy.$folder;
		$path_image = $folder.'/plat_nomer.jpg';
		$path_image_upload = $folder_upload.'/plat_nomer.jpg';
		$image = $this->post('image');      
		$plat_nomer = 'not defined';					
		//log_message('error',$image);
		if(file_put_contents($path_image_upload,base64_decode($image))){
			chdir($folderPy);
			$cli = 'python PlateNumber.py -i '.$path_image;	
			
			$plat_nomer = shell_exec($cli);
		
            $output['content'] = trim(preg_replace('/\s+/', '', $plat_nomer));
            $output['status'] = 1;
		}else{
			$output['message'] =  'proses upload gagal';  
		}
		
        $this->response($output, 200);
    }
	
	public function simpanPlate_post()
	{		
		$output = array('status' => 1,'content' => '','message' => '');   
		$folderPy = APPPATH.'third_party/ALPR/';
		$folder = 'images';
		$folder_upload = $folderPy.$folder;
		$nosj = $this->post('nosj');
		//$path_image = $folder.'/plat_nomer.jpg';
		//$path_image_upload = $folder_upload.'/plat_nomer.jpg';
		$path_image = $folder.'/SJDOC_'.$nosj.'.jpg';
		$path_image_upload = $folder_upload.'/SJDOC_'.$nosj.'.jpg';
		$image = $this->post('image');      
		$plat_nomer = 'not defined';					
		//log_message('error',$image);
		if(file_put_contents($path_image_upload,base64_decode($image))){
			chdir($folderPy);
			$cli = 'python PlateNumber.py -i '.$path_image;				
			$plat_nomer = shell_exec($cli);
		
            $output['content'] = trim(preg_replace('/\s+/', '', $plat_nomer));
            $output['status'] = 1;
		}else{
			$output['message'] =  'proses upload gagal';  
		}
		
        $this->response($output, 200);
    }
}