<?php

class M_rhk extends MY_Model
{
    protected $_table = 'rhk';

    public function __construct()
    {
        parent::__construct();
    }

    public function validasiScanEntriLHK($kode_farm, $barcode)
    {
        $sql = <<<SQL
			SELECT TOP 1 lhk.NO_REG, lhk.NO_REG_CETAK, siklus.KODE_KANDANG, siklus.FLOK_BDY, siklus.TGL_DOC_IN, siklus.TGL_DOC_IN as formatted_tgl_doc_in, lhk.tgl_lhk, lhk.tgl_lhk as formatted_tgl_lhk, DATEDIFF(DAY, siklus.TGL_DOC_IN, isnull(lhk.tgl_lhk,dateadd(day, 1, siklus.TGL_DOC_IN))) AS umur
			FROM KANDANG_SIKLUS siklus
			LEFT OUTER JOIN (
				SELECT RHK.NO_REG, RHK_CETAK.NO_REG AS NO_REG_CETAK, RHK_CETAK.TGL_TRANSAKSI AS tgl_lhk
				FROM RHK_CETAK
				LEFT OUTER JOIN RHK ON RHK.NO_REG=RHK_CETAK.NO_REG AND RHK.TGL_TRANSAKSI=RHK_CETAK.TGL_TRANSAKSI
				WHERE RHK_CETAK.BARCODE = '{$barcode}'
			) lhk ON lhk.NO_REG_CETAK = siklus.NO_REG
			WHERE siklus.KODE_FARM='{$kode_farm}' AND siklus.STATUS_SIKLUS = 'O'
			ORDER BY lhk.tgl_lhk DESC
SQL;

        return $this->db->query($sql)->result_array();
    }

    public function getJumlahSekatPenimbangan($kode_farm, $kandang)
    {
        $sql = <<<SQL
			SELECT * 
			FROM M_KANDANG
			WHERE KODE_FARM='{$kode_farm}' AND KODE_KANDANG='{$kandang}'
SQL;

        return $this->db->query($sql)->result_array();
    }

    public function maksPPKandangTglKebutuhan($no_reg, $kode_farm, $tgl_kebutuhan)
    {
        $result = array();
        $pakanTerima = $this->totalPakanSudahTerima($no_reg, $kode_farm, $tgl_kebutuhan);
        $sudahOrder = arr2DToarrKey($this->totalSudahOrder($no_reg, $kode_farm, $tgl_kebutuhan), 'kode_barang');
        if (!empty($pakanTerima)) {
            foreach ($pakanTerima as $pt) {
                $kode_barang = $pt['kode_barang'];
                $jmlOrder = isset($sudahOrder[$kode_barang]) ? $sudahOrder[$kode_barang]['sudah_order'] : 0;
                $result[$kode_barang] = $pt['sudah_terima'] - $jmlOrder;
            }
        }

        return $result;
    }

    private function totalPakanSudahTerima($no_reg, $Kode_farm, $tgl_kebutuhan)
    {
        $sql = <<<SQL
		SELECT sum(telah_terima.jml_putaway) sudah_terima,kode_barang FROM 
		(
			SELECT md.no_pallet,md.JML_PUTAWAY,p.KETERANGAN1 no_do,md.kode_barang FROM MOVEMENT_D md 
			JOIN PENERIMAAN_E pe ON pe.NO_PALLET = md.NO_PALLET AND pe.KODE_FARM = md.KODE_FARM
			JOIN PENERIMAAN p ON p.KODE_FARM = pe.KODE_FARM AND p.NO_PENERIMAAN = pe.NO_PENERIMAAN AND pe.NO_PENERIMAAN = md.no_referensi
			WHERE md.KETERANGAN2 = '{$no_reg}' AND md.keterangan1 = 'PUT'
		)telah_terima
		JOIN (
			SELECT DISTINCT dd.NO_DO FROM do_d dd
			JOIN op ON dd.NO_OP = OP.NO_OP AND dd.KODE_FARM = OP.KODE_FARM
			JOIN lpb_e le ON le.NO_LPB = op.NO_LPB AND le.NO_REG like '{$no_reg}' AND le.TGL_KEBUTUHAN <= '{$tgl_kebutuhan}'
		)do_kebutuhan ON do_kebutuhan.no_do = telah_terima.no_do
		GROUP BY telah_terima.kode_barang			
SQL;

        return $this->db->query($sql)->result_array();
    }

    private function totalSudahOrder($no_reg, $kode_farm, $tgl_kebutuhan)
    {
        $sql = <<<SQL
		SELECT sum(oke.jml_order) sudah_order,oke.kode_barang from ORDER_KANDANG_E oke
		JOIN ORDER_KANDANG ok ON ok.NO_ORDER = oke.NO_ORDER AND ok.NO_REFERENSI IS NULL AND ok.KODE_FARM = '{$kode_farm}'
		WHERE no_reg = '{$no_reg}' AND oke.TGL_KEBUTUHAN <= '{$tgl_kebutuhan}'
		GROUP BY oke.KODE_BARANG
SQL;

        return $this->db->query($sql)->result_array();
    }

    public function stokPakanTglTransaksi($noreg, $tgl_lhk, $kode_barang)
    {
        $stok = 0;
        $sqlStokAkhir = $this->db->select('jml_stok')->where(array('no_reg' => $noreg, 'kode_barang' => $kode_barang))->get('kandang_movement')->row_array();
        if (!empty($sqlStokAkhir)) {
            $stok = $sqlStokAkhir['jml_stok'];
            $sql = <<<SQL
			SELECT coalesce(sum(kmd.jml_order),0) terima
			FROM KANDANG_MOVEMENT_D kmd 
			JOIN penerimaan_kandang pk ON pk.NO_REG = kmd.NO_REG AND pk.NO_PENERIMAAN_KANDANG = kmd.KETERANGAN2 
			JOIN order_kandang_e oke ON oke.NO_ORDER = pk.NO_ORDER AND oke.NO_REG = pk.NO_REG AND oke.KODE_BARANG = kmd.KODE_BARANG AND oke.tgl_kebutuhan > '{$tgl_lhk}'
			WHERE kmd.NO_REG = '{$noreg}'
SQL;
            $cutOff = $this->db->query($sql)->row_array();
            if (!empty($cutOff)) {
                $stok -= $cutOff['terima'];
            }
        }

        return $stok > 0 ? $stok : 0;
    }
}
