<?php

/**
 * A wrapper to work with Seven Segment OCR for PHP.
 *
 * SSOCR - https://www.unix-ag.uni-kl.de/~auerswal/ssocr/
 */
class SSOCR
{
    /**
     * Path to the image to be recognized.
     *
     * @var string
     */
    protected $image;

    /**
     * Path to ssocr executable.
     * Default value assumes it is present in the $PATH.
     *
     * @var string
     */
    protected $ssocr = 'ssocr';

    /**
     * Use iterative thresholding method.
     *
     * @var bool
     */
    protected $iterThreshold;

    /**
     * Threshold value (in percentage).
     *
     * @var int
     */
    protected $threshold;

    /**
     * crop value X Y W H.
     *
     * @var string
     */
    protected $crop;

    /**
     * Number digits value.
     *
     * @var int
     */
    protected $numberDigits;

    /**
     * Path to the output image of the result of image processing.
     *
     * @var string
     */
    protected $outputImage;

    /**
     * If the image to be converted to monochrome using thresholding.
     *
     * @var bool
     */
    protected $makeMono;

    /**
     * List of ssocr other options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Class constructor.
     *
     * @param string $image
     *
     * @return SSOCR
     */
    public function __construct()
    {        
        return $this;
    }

    public function setImage($image){
        $this->image = $image;
    }
    /**
     * Executes tesseract command and returns the generated output.
     *
     * @return string
     */
    public function run($debug = false)
    {
        if ($debug) {
            return $this->buildCommand();
        }

        return trim(`{$this->buildCommand()}`);
    }

    /**
     * Sets a custom location for the ssocr executable.
     *
     * @param string $ssocr
     *
     * @return this
     */
    public function executable($ssocr)
    {
        $this->ssocr = $sssocr;

        return $this;
    }

    /**
     * Use iterative thresholding method.
     *
     * @return this
     */
    public function iterativeThreshold()
    {
        $this->iterThreshold = true;

        return $this;
    }

    /**
     * use THRESH (in percent) to distinguish black from white.
     *
     * @param int $threshold Threshold in percentage
     *
     * @return this
     */
    public function threshold($threshold)
    {
        $this->threshold = $threshold;

        return $this;
    }

    /**
     * Use only the subpicture with upper left corner (X,Y), width W and height H.
     *
     * @param int $x Upper left corner X
     * @param int $y Upper left corner Y
     * @param int $w Width
     * @param int $h Height
     *
     * @return this
     */
    public function crop($x, $y, $w, $h)
    {
        $this->crop = $x.' '.$y.' '.$w.' '.$h;

        return $this;
    }

    /**
     * Specifies the number of digits shown in the image.
     * If NUMBER is -1, the number of digits is detected automatically.
     *
     * @param int $numberDigits Number of digits
     *
     * @return this
     */
    public function numberDigits($numberDigits)
    {
        $this->numberDigits = $numberDigits;

        return $this;
    }

    /**
     * Process the given commands only, no segmentation or character recognition.
     * Should be used together with --output-image=FILE.
     *
     * @param string $outputImage Output image filename path
     *
     * @return this
     */
    public function processOnly($outputImage)
    {
        $this->outputImage = $outputImage;

        return $this;
    }

    /**
     * Convert the image to monochrome using thresholding.
     * The threshold can be specified with option --threshold and
     * is adjusted to the used luminance interval of the image unless option --absolute-threshold is specified.
     *
     * @return this
     */
    public function makeMono()
    {
        $this->makeMono = true;

        return $this;
    }

    /**
     * Sets a sssocr other options value.
     *
     * @param string $key
     * @param string $value
     *
     * @return this
     */
    public function option($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * Builds the ssocr command with all its options.
     *
     * @return string
     */
    protected function buildCommand()
    {
        return $this->ssocr.$this->buildCropParam()
            .$this->buildThresholdParam()
            .$this->buildIterativeThresholdParam()
            .$this->buildNumberDigitsParam()
            .$this->buildProcessOnlyParam()
            .$this->buildMakeMonoParam()
            .$this->buildOptionsParam()
            .' '.escapeshellarg($this->image);
    }

    /**
     * If threshold is defined, return the correspondent command line
     * argument to the ssocr command.
     *
     * @return string
     */
    protected function buildThresholdParam()
    {
        return is_null($this->threshold) ? '' : ' --threshold '.$this->threshold;
    }

    /**
     * If iterative threshold is defined, return the correspondent command line
     * argument to the ssocr command.
     *
     * @return string
     */
    protected function buildIterativeThresholdParam()
    {
        return is_null($this->iterThreshold) ? '' : ' --iter-threshold';
    }

    /**
     * If crop is defined, return the correspondent command line
     * argument to the ssocr command.
     *
     * @return string
     */
    protected function buildCropParam()
    {
        return is_null($this->crop) ? '' : ' crop '.$this->crop;
    }

    /**
     * If number digits is defined, return the correspondent command line
     * argument to the ssocr command.
     *
     * @return string
     */
    protected function buildNumberDigitsParam()
    {
        return is_null($this->numberDigits) ? '' : ' --number-digits '.$this->numberDigits;
    }

    /**
     * If output image is defined, return the correspondent command line
     * argument to the ssocr command.
     *
     * @return string
     */
    protected function buildProcessOnlyParam()
    {
        return is_null($this->outputImage) ? '' : ' --process-only --output-image '.escapeshellarg($this->outputImage);
    }

    /**
     * If make mono is defined, return the correspondent command line
     * argument to the ssocr command.
     *
     * @return string
     */
    protected function buildMakeMonoParam()
    {
        return is_null($this->makeMono) ? '' : ' make_mono';
    }

    /**
     * Return ssocr command line arguments for every custom options.
     *
     * @return string
     */
    private function buildOptionsParam()
    {
        $buildParam = function ($option, $value) {
            return ' '.$option.' '.$value;
        };

        return implode('', array_map(
            $buildParam,
            array_keys($this->options),
            array_values($this->options)
        ));
    }
}
