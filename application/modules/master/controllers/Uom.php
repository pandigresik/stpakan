<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Uom extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_uom');
    }

    public function index() {
        $data['list_konversi'] = $this->m_uom->list_konversi();
        $this->load->view("uom/uom_list",$data);
    }

    function get_pagination() {
        $offset = 8;
        $page_number = ($this->input->post('page_number')) ? $this->input->post('page_number') : 0;
        $is_search = ($this->input->post('search')) ? $this->input->post('search') : false;

        $satuan = (($this->input->post("satuan")) and $is_search == true) ? $this->input->post("satuan") : null;
        $deskripsi = (($this->input->post("deskripsi")) and $is_search == true) ? $this->input->post("deskripsi") : null;
        $satuan_dasar = (($this->input->post("satuan_dasar")) and $is_search == true) ? $this->input->post("satuan_dasar") : null;
        $konversi = (($this->input->post("konversi")) and ($this->input->post("konversi")) != "" and $is_search == true) ? $this->input->post("konversi") : null;

        $data_satuan_all = $this->m_uom->get_uom(NULL, NULL, $satuan, $deskripsi, $satuan_dasar, $konversi);

        $data_satuan = $this->m_uom->get_uom(($page_number * $offset), ($page_number + 1) * $offset, $satuan, $deskripsi, $satuan_dasar, $konversi);

        $total = count($data_satuan_all);
        $pages = ceil($total / $offset);

        if (count($data_satuan) > 0) {
            $data = array(
                'TotalRows' => $pages,
                'Rows' => $data_satuan
            );

            $this->output->set_content_type('application/json');
            echo json_encode(array(
                $data
            ));
        } else {
            echo json_encode(array());
        }

        exit();
    }

    function get_uom() {
        $satuan = ($this->input->post("satuan")) ? $this->input->post("satuan") : null;

        $satuan = $this->m_uom->get_uom_by_id($satuan);

        echo json_encode($satuan);
    }

    function get_master_uom() {
        $data ['uom'] = $this->m_uom->get_all_uom();

        $this->load->view("uom/master_uom", $data);
    }

    function add_uom() {
        $satuan = ($this->input->post("satuan")) ? $this->input->post("satuan") : null;
        $deskripsi = ($this->input->post("deskripsi")) ? $this->input->post("deskripsi") : null;
        $satuan_dasar = ($this->input->post("satuan_dasar")) ? $this->input->post("satuan_dasar") : null;
        $konversi = ($this->input->post("konversi")) ? $this->input->post("konversi") : null;

        $return = array();

        $return ["form_mode"] = "tambah";
        $check = $this->m_uom->check_uom($satuan);
        if ($check ['n_result'] > 0) {
            $return ["result"] = "failed";
            $return ["check"] = "failed";
        } else {
            $data = array(
                "UOM" => $satuan,
                "DESKRIPSI" => $deskripsi,
                "BASE_UOM" => $satuan_dasar,
                "KONVERSI" => $konversi
            );
            //print_r($data);
            $result = $this->m_uom->insert($data);
            if ($result) {
                $return ["result"] = "success";
            } else {
                $return ["result"] = "failed";
                $return ["check"] = "success";
            }
        }
        #echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
    }

    function update_uom() {
        $satuan = ($this->input->post("satuan")) ? $this->input->post("satuan") : null;
        $deskripsi = ($this->input->post("deskripsi")) ? $this->input->post("deskripsi") : null;
        $satuan_dasar = ($this->input->post("satuan_dasar")) ? $this->input->post("satuan_dasar") : null;
        $konversi = ($this->input->post("konversi")) ? $this->input->post("konversi") : null;

        $return = array();

        $return ["form_mode"] = "ubah";
        $data = array(
            "DESKRIPSI" => $deskripsi,
            "BASE_UOM" => $satuan_dasar,
            "KONVERSI" => $konversi
        );

        $result = $this->m_uom->update($data, $satuan);
        if ($result) {
            $return ["result"] = "success";
        } else {
            $return ["result"] = "failed";
        }
        #echo json_encode($return);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($return));
    }

}
