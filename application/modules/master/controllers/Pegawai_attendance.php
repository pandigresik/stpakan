<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 * */
class Pegawai_attendance extends MY_Controller
{
    private $grupFinger = array('KFM', 'KPPB', 'PPB', 'OK', 'AGF');
    private $_limit = 10;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_pengawas');
    }

    public function index()
    {
        $grups = $this->db->select('grup_pegawai,deskripsi')->where_in('grup_pegawai', $this->grupFinger)->get('m_grup_pegawai')->result_array();
        $data['grups'] = $grups;
        $this->load->view('master/attendance/pengawas_list', $data);
    }

    public function lists($offset = 0)
    {
        $pencarian = $this->input->post('filter');
        $where = array();
        foreach ($pencarian as $v) {
            if (!empty($v['value'])) {
                array_push($where, $v['name'].' like \'%'.$v['value'].'%\'');
            }
        }

        if (!empty($where)) {
            $whereStr = implode(' and ', $where);
            $data = $this->db->select('mp.*,ui.NAME as NAMAABSEN,pa.BADGE_NUMBER,mgp.DESKRIPSI')
                            ->limit($this->_limit, $offset)
                            ->join('attendance.dbo.USERINFO ui', 'ui.BADGENUMBER = pa.BADGE_NUMBER')
                            ->join('m_pegawai mp', 'mp.KODE_PEGAWAI = pa.KODE_PEGAWAI')
                            ->join('m_grup_pegawai mgp', 'mgp.GRUP_PEGAWAI = mp.GRUP_PEGAWAI')
                            ->where($whereStr)
                            ->get('PEGAWAI_ATTENDANCE pa')
                            ->result_array();

            $countData = $this->db->join('attendance.dbo.USERINFO ui', 'ui.BADGENUMBER = pa.BADGE_NUMBER')
                                ->join('m_pegawai mp', 'mp.KODE_PEGAWAI = pa.KODE_PEGAWAI')
                                ->where($whereStr)
                                ->get('PEGAWAI_ATTENDANCE pa')
                                ->num_rows();
        } else {
            $data = $this->db->select('mp.*,ui.NAME as NAMAABSEN,pa.BADGE_NUMBER,mgp.DESKRIPSI')
                            ->limit($this->_limit, $offset)
                            ->join('attendance.dbo.USERINFO ui', 'ui.BADGENUMBER = pa.BADGE_NUMBER')
                            ->join('m_pegawai mp', 'mp.KODE_PEGAWAI = pa.KODE_PEGAWAI')
                            ->join('m_grup_pegawai mgp', 'mgp.GRUP_PEGAWAI = mp.GRUP_PEGAWAI')
                            ->get('PEGAWAI_ATTENDANCE pa')
                            ->result_array();
            $countData = $this->db->join('attendance.dbo.USERINFO ui', 'ui.BADGENUMBER = pa.BADGE_NUMBER')
                                ->join('m_pegawai mp', 'mp.KODE_PEGAWAI = pa.KODE_PEGAWAI')
                                ->get('PEGAWAI_ATTENDANCE pa')
                                ->num_rows();
        }

        $this->result['status'] = 1;
        $this->result['content'] = array(
            'data' => $this->load->view('master/attendance/lists', array('data' => $data, 'awal' => $offset), true),
            'pagination' => $this->generate_paging($countData, $this->_limit),
        );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }

    private function generate_paging($jml, $limit)
    {
        $this->load->library('pagination');
        $this->load->config('pagination');
        $config = $this->config->item('pagination');
        $config['total_rows'] = $jml;
        $config['per_page'] = $limit;
        $config['uri_segment'] = 4;

        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    public function add()
    {
        $sqStpakan = $this->db->select('KODE_PEGAWAI')->get_compiled_select('PEGAWAI_ATTENDANCE');
        $sqAttendance = $this->db->select('BADGE_NUMBER')->get_compiled_select('PEGAWAI_ATTENDANCE');
        $data['stpakan'] = $this->db->select(array('KODE_PEGAWAI', 'NAMA_PEGAWAI', 'DESKRIPSI'))->join('m_grup_pegawai mgp', 'mgp.grup_pegawai = mp.grup_pegawai')->where('mp.STATUS_PEGAWAI', 'A')->order_by('NAMA_PEGAWAI')->where('mp.KODE_PEGAWAI not in ('.$sqStpakan.')')->where_in('mp.grup_pegawai', $this->grupFinger)->get('m_pegawai as mp')->result_array();
        $data['attendance'] = $this->db->select(array('BADGENUMBER as KODE_PEGAWAI', 'NAME as NAMA_PEGAWAI'))->order_by('NAME')->where('BADGENUMBER not in ('.$sqAttendance.')')->get('attendance.dbo.USERINFO')->result_array();
        $this->load->view('master/attendance/form', $data);
    }

    public function save()
    {
        $this->result['message'] = 'Data pegawai gagal dimapping';
        $data = $this->input->post('data');
        $this->db->insert('PEGAWAI_ATTENDANCE', $data);
        if ($this->db->affected_rows() > 0) {
            $this->result['message'] = 'Data pegawai berhasil dimapping';
            $this->result['status'] = 1;
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->result));
    }
}
