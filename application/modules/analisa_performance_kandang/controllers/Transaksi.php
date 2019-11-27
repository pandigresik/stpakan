<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	/*
 * controller user ini akan digunakan untuk autentikasi dan otorisasi
 * semua controller yang bebas diakses seharusnya mengextends MX_Controller atau CI_Controller
 */
class Transaksi extends MX_Controller {
	public function index() {
		$this->view ();
	}
	public function view($offset = 0) {
		// $data ['all_items'] = array (
		$tab_active = $this->input->post ( 'tab_active' );
		$data ['tab_active'] = (empty ( $tab_active )) ? 1 : $tab_active;
		$tanggal_kirim = $this->input->post ( 'tanggal_kirim' );
		$data ['items'] = array (
				'05 Apr 2015' => array (
						array (
								'kode_kandang' => 'K003',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '10',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '1' 
						),
						array (
								'kode_kandang' => 'K004',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '' 
						),
						array (
								'kode_kandang' => 'K005',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '' 
						),
						array (
								'kode_kandang' => 'K006',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '' 
						) 
				),
				'08 Apr 2015' => array (
						array (
								'kode_kandang' => 'K007',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '' 
						),
						array (
								'kode_kandang' => 'K008',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '' 
						),
						array (
								'kode_kandang' => 'K009',
								'kode_barang' => '1174-13-33',
								'nama_barang' => 'P 3 COBB',
								'jumlah' => '',
								'bentuk_pakan' => 'TEPUNG',
								'remark' => '' 
						) 
				) 
		);
		$data ['items_result'] = [ ];
		if (! empty ( $tanggal_kirim )) {
			foreach ( $data ['items'] as $key => $value ) {
				if (date ( 'd M Y', strtotime ( $key ) ) == date ( 'd M Y', strtotime ( $tanggal_kirim ) )) {
					$data ['items_result'] = $value;
				}
			}
		} else {
			foreach ( $data ['items'] as $key1 => $value2 ) {
				foreach ( $value2 as $key2 => $value2 ) {
					$data ['items_result'] [] = $value2;
				}
			}
		}
		
		$jml = count ( $data ['items_result'] );
		$config ['base_url'] = base_url () . '#penerimaan_kandang/transaksi/view';
		$config ['total_rows'] = $jml;
		$config ['per_page'] = 2;
		$config ['uri_segment'] = 2;
		$config ['full_tag_open'] = "<ul class='pagination'>";
		$config ['full_tag_close'] = "</ul>";
		$config ['num_tag_open'] = '<li>';
		$config ['num_tag_close'] = '</li>';
		$config ['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		$config ['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		$config ['next_tag_open'] = "<li>";
		$config ['next_tagl_close'] = "</li>";
		$config ['prev_tag_open'] = "<li>";
		$config ['prev_tagl_close'] = "</li>";
		$config ['first_tag_open'] = "<li>";
		$config ['first_tagl_close'] = "</li>";
		$config ['last_tag_open'] = "<li>";
		$config ['last_tagl_close'] = "</li>";
		/*
		 * $config ['full_tag_open'] = "<ul class='pagination pagination-sm' style='position:relative; top:-25px;'>";
		 * $config ['full_tag_close'] = "</ul>";
		 * $config ['num_tag_open'] = '<li>';
		 * $config ['num_tag_close'] = '</li>';
		 * $config ['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
		 * $config ['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
		 * $config ['next_tag_open'] = "<li>";
		 * $config ['next_tagl_close'] = "</li>";
		 * $config ['prev_tag_open'] = "<li>";
		 * $config ['prev_tagl_close'] = "</li>";
		 * $config ['first_tag_open'] = "<li>";
		 * $config ['first_tagl_close'] = "</li>";
		 * $config ['last_tag_open'] = "<li>";
		 * $config ['last_tagl_close'] = "</li>";
		 */
		$this->pagination->initialize ( $config );
		$data ['halaman'] = $this->pagination->create_links ();
		$data ['offset'] = $offset;
		$data ['items'] = [ ];
		foreach ( $data ['items_result'] as $key => $value ) {
			if ($key >= $offset and $key < $offset + $config ['per_page']) {
				$data ['items'] [] = $value;
			}
		}
		
		$this->load->view ( 'transaksi', $data );
	}
}
