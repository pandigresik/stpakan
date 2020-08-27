<?php

class M_log_doc_in extends MY_Model
{
    protected $_table;
    protected $before_create = ['no_urut'];
    private $_user;

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'log_doc_in';
        $this->_user = $this->session->userdata('kode_user');
    }

    public function no_urut($row)
    {
        if (is_object($row)) {
            $no_reg = $row->NO_REG;
        } else {
            $no_reg = $row['NO_REG'];
        }
        // dapatkan no_urut berdasarkan no_reg
        $tmp = $this->order_by('no_urut', 'desc')->get_by(['no_reg' => $no_reg]);
        if (!empty($tmp)) {
            $no_urut = $tmp->NO_URUT;
        } else {
            $no_urut = 0;
        }
        ++$no_urut;
        if (is_object($row)) {
            $row->no_urut = $no_urut;
        } else {
            $row['no_urut'] = $no_urut;
        }

        return $row;
    }
}
