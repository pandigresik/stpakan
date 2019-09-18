<?php
/** digunakan untuk generate gambar berdasarkan data json */
class TemplateOMR
{

    private $map;
    private $imagepath;
    private $mappath;    
    private $result = [];
    private $imagick;
    private $draw;    

    /**
     * Construtor, ira receber tudo que for necessário e irá jogar o resultado em $result, use getResult().
     * Se o caminho do $debugimagepath não existir, ele irá tentar criar um diretório 0777, é melhor você se certificar que o caminho existe.
     *
     * @param string $mappath
     * @param string $imagepath
     * @param int $tolerance
     * @param bool $debugimagepath
     * @param bool $debugfilename
     *
     * @uses Imagick
     * @uses ImagickDraw
     *
     *
     * @throws InvalidToleranceException
     * @throws JsonDecodeException
     * @throws FileNotFoundException
     *
     */
    public function __construct(){

    }

    public function setup($imagepath,$imagefilename = "template.jpeg"){
        try{                        
            $this->createFolder($imagepath);
            $this->prepareDraw();                                                           
            $json = $this->getMap();    
            $imagick = new Imagick();
            $imagick->newImage($json['expectedwidth'],$json['expectedheight'],"rgb(255, 255, 255)");        
            $imagick->modulateImage(100, 0, 100);
            $imagick->posterizeimage(2, false);
            $imagick->thresholdImage(0.5);            
            $imagick->setImageCompression(imagick::COMPRESSION_JPEG);
            $imagick->setImageCompressionQuality(100);

            $draw = $this->getDraw();
            $this->generateImage();

            $imagick->drawImage($draw);            
            $imagick->writeImage ($imagepath.DIRECTORY_SEPARATOR.$imagefilename);            
            
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function compareMap($imagefilename){
        try{                                    
            $this->prepareDraw();                                                           
            $json = $this->getMap();    
            $imagick = new Imagick();
            $imagick->readImage($imagefilename);

            $draw = $this->getDraw();
            $this->generateImage();

            $imagick->drawImage($draw);            
        //  $imagick->writeImage ($imagepath.DIRECTORY_SEPARATOR.$imagefilename);            
            header("Content-Type: image/" . $imagick->getImageFormat());
            echo $imagick;
            
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public function setJsonMapPath($mappath){
        if(!@$file = file_get_contents($mappath)){
            throw new FileNotFoundException('Não foi possivel encontrar o arquivo de mapa');
        }
        if(!$json = json_decode($file,true)){
            throw new JsonDecodeException('Erro ao decodificar o arquivo json passado');
        }         
        $this->setMap($json);
    }

    public function setArrayMap($arr){        
        $this->setMap($arr);
    }

    /** generate image rectangle berdasarkan data json */
    private function generateImage(){
        $map = $this->getMap();                
        foreach ($map['groups'] as $groupvalue){
            if(!empty($groupvalue)){
                $this->groupAnalytics($groupvalue);
            }            
        }        
    }

    /**
     * @param $group
     * @return array
     * @throws InvalidTargetTypeException
     */
    private function groupAnalytics($group){                
        foreach ($group['grouptargets'] as $target){
            $x = $target['x'];
            $y = $target['y'];
            $width = $target['width'];
            $height = $target['height'];
            $text =  isset($target['text']) ? $target['text'] : NULL;
            if($target['type'] == 'rectangle'){
                $color = '#0000CC';
                $this->drawRectangle($x,$y,$width,$height,$color,$text);                              
            }else if($target['type'] == 'circle'){
                $color = '#0000CC';                                
                $radius = ($width / 2);
                $x = $x + $radius;
                $y = $y + $radius;
                $x2 = $x + $radius;
                $this->drawCircle($x,$y,$x2,$color,$text);                              
            }else{
                throw new InvalidTargetTypeException('Tipo não suportado: ',$target['type']);
            }
        }        
    }
    /**
     *
     */
    private function prepareDraw(){
        $draw = new ImagickDraw();

        $draw->setStrokeOpacity(1);
        $draw->setFillOpacity(0);
        $draw->setStrokeWidth(1);
        $this->setDraw($draw);
    }

    /**
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     * @param $color
     */
    private function drawRectangle($x, $y, $width, $height, $color = '#0000CC',$text = NULL){
        $this->draw->setStrokeColor($color);
        $this->draw->rectangle($x, $y, ($x+$width), ($y+$height));
        if(!is_null($text)){
            $fontSize = $width / 2;
            $tposx = $x + ($width / 4);
            $tposy = $y + ($height / 2);
        //    $this->draw->setStrokeColor('grey30');
            $this->draw->setFontSize($fontSize);
            $this->draw->annotation($tposx, $tposy,$text);
        }
    }

     /**
     * @param $x
     * @param $y
     * @param $radius     
     * @param $color
     */
    private function drawCircle($x, $y, $x2, $color = '#0000CC',$text = NULL){
        $this->draw->setStrokeColor($color);
        $this->draw->circle($x, $y, $x2, $y);
        if(!is_null($text)){
            $fontSize = ($x2 - $x);
            $tposx = $x - (($x2 - $x) / 2);
        //    $this->draw->setStrokeColor('grey30');
            $this->draw->setFontSize($fontSize);
            $this->draw->annotation($tposx, $y,$text);
        }
    }

    /**
     * @param $path
     * @return bool
     * @throws Exception
     */
    private function createFolder($path){

        $path = utf8_decode($path);

        if (!file_exists($path)) {
            if (mkdir($path, 0777, true) == false) {
                throw new Exception('Não foi possivel criar o diretório: ' . utf8_encode($path));
            }
        }
        if (!file_exists($path)) {
            throw new Exception('Verificação falou, não foi possivel criar o diretório: ' . utf8_encode($path));
        }
        return true;
    }

    /**
     * @return mixed
     */
    private function getImagick()
    {
        return $this->imagick;
    }

    /**
     * @param mixed $imagick
     */
    private function setImagick($imagick)
    {
        $this->imagick = $imagick;
    }


    /**
     * @return mixed
     */
    private function getMap()
    {
        return $this->map;
    }

    /**
     * @param mixed $map
     */
    private function setMap($map)
    {
        $this->map = $map;
    }

    /**
     * @return mixed
     */
    private function getImagepath()
    {
        return $this->imagepath;
    }

    /**
     * @param mixed $imagepath
     */
    private function setImagepath($imagepath)
    {
        $this->imagepath = $imagepath;
    }

    /**
     * @return mixed
     */
    private function getMappath()
    {
        return $this->mappath;
    }

    /**
     * @param mixed $mappath
     */
    private function setMappath($mappath)
    {
        $this->mappath = $mappath;
    }

    /**
     * @return mixed
     */
    private function getDraw()
    {
        return $this->draw;
    }

    /**
     * @param mixed $draw
     */
    private function setDraw($draw)
    {
        $this->draw = $draw;
    }

}
class FileNotFoundException extends Exception{}
class JsonDecodeException extends Exception{}
class InvalidToleranceException extends Exception{}
class InvalidTargetTypeException extends Exception{}
class UnexpectedResolutionException extends Exception{}