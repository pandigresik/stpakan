<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/gantti/gantti.php';

class Gantt extends Gantti
{
    function __construct($data = null, $params=array())
    {
        parent::__construct($data, $params);
    }
}

