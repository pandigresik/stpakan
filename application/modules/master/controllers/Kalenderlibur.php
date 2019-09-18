<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MY_Controller atau CI_Controller
 */

class Kalenderlibur extends MY_Controller {
    protected $_user;
    protected $_farm;
    protected $_level_user;

    function __construct() {
        parent::__construct();
        $this->load->model('m_kalenderlibur');
        $this->load->model('m_farm');

        $this->_user = $this->session->userdata('kode_user');
        $this->_level_user = $this->session->userdata ( 'level_user' );
        $this->_farm = ($this->_level_user == 'BPM') ? NULL : $this->session->userdata('kode_farm');
    }

    public function getallkalenderlibur()
    {
        $pdo = $this->m_kalenderlibur->connect()->return;

        $ym = explode("-", $this->input->post('ym'));

        $formattext = $ym[0] .'-'. ((strlen($ym[1]) < 2) ? '0' . $ym[1] : $ym[1]);

        $stmt = $pdo->prepare("select * from m_kalender mk where substring(convert(NVARCHAR(max), mk.TANGGAL), 1, 7) = ?");
        $stmt->execute(array(
            $formattext
        ));

        $fetchdata = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($fetchdata);
        exit;
    }

    public function check()
    {
        $tglexp = explode("-", $this->input->post('formattext'));

        $tgl = $tglexp[2] .'-'. $tglexp[1] .'-'. $tglexp[0];

        try {
            $pdo = $this->m_kalenderlibur->connect()->return;

            $stmt = $pdo->prepare("select * from m_kalender mk where mk.TANGGAL = ?");
            $stmt->execute(array(
                $tgl
            ));

            $fetchdata = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!empty($fetchdata)) {
                echo json_encode(['status' => 'satu', 'fetch' => $fetchdata]);
            } else {
                echo json_encode(['status' => 'dua', 'fetch' => null]);
            }            
            exit;
        } catch(\PDOException $e) {
            echo json_encode(['status' => 'dua', 'fetch' => null]);
            exit;
        }
    }

    public function index() {
        $kalenderlibur = $this->m_kalenderlibur->get_all();

        $yearDataArr = [];

        foreach ($kalenderlibur as $key => $value) {
            $yearDataArr[] = [
                'id' => ($key + 1),
                'name' => $value['KETERANGAN'],
                'type' => 'Hari libur',
                'color' => 'red',
                'startDate' => $value['TANGGAL'],
                'endDate' => $value['TANGGAL'],
            ];
        }

        $data['kalenderlibur'] = $this->convertYearData($yearDataArr);
        /*echo "<pre>";
        print_r($data);
        exit;*/

        $this->load->view("kalenderlibur/index", $data);
        // die('ok');
    }

    private function convertYearData(array $yearData)
    {
        if (empty($yearData)) {
            return 'null';
        }
        $data = '';
        foreach ($yearData as $event) {
            if (empty($data)) {
                $data =  "[{id:{$event['id']}, color: '{$event['color']}', name:'{$event['name']}', type:'{$event['type']}', startDate: new Date('{$event['startDate']}'), endDate: new Date('{$event['endDate']}')}";
            } else {
                $data .=  ", {id:{$event['id']}, color: '{$event['color']}', name:'{$event['name']}', type:'{$event['type']}', startDate: new Date('{$event['startDate']}'), endDate: new Date('{$event['endDate']}')}";
            }   
        }
        $data .= ']';
        return $data;
    }

    public function add()
    {
        $datapost = $this->input->post();
        $msg = $this->m_kalenderlibur->insertkalender($datapost);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($msg));
        
        
    }

    public function edit()
    {
        $datapost = $this->input->post();
        $msg = $this->m_kalenderlibur->updatekalender($datapost);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($msg));
    }

    public function delete()
    {
        $datapost = $this->input->post();
        $msg = $this->m_kalenderlibur->deletekalender($datapost);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($msg));
    }
}
