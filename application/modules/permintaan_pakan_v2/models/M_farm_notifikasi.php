<?php
class M_farm_notifikasi extends MY_Model
{
    protected $_table;
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'farm_notifikasi';
    }
}

