<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf_barcodes_2d.php';

class PdfBarcode2D extends TCPDF2DBarcode
{
    function __construct($code = NULL, $type = NULL)
    {
        parent::__construct($code,$type);
    }
}

