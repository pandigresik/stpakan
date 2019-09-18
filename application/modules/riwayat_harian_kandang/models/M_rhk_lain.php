<?php
class M_rhk_lain extends MY_Model{
	protected $_table = 'rhk_lain2';
	public function __construct(){
		parent::__construct();
	
	}
	public function simpan_attachment($noreg, $tgl, $format, $fileContent,$keterangan) {
        $attachment = $this->mssql_escape(file_get_contents($fileContent));
        $query = <<<QUERY
            INSERT INTO [dbo].[RHK_LAIN2]
                   ([NO_REG]
                   ,[TGL_TRANSAKSI]
				   ,[TIPE]
				   ,[ATTACHMENT_FORMAT]
                   ,[ATTACHMENT])            
            VALUES (
                    '{$noreg}',
                    '{$tgl}',
					'I',
					'{$format}',
                    $attachment,
                    {$keterangan}
            )
QUERY;
        
        $stmt = $this->db->conn_id->prepare($query);        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}
function mssql_escape($data) {
        if(is_numeric($data))
          return $data;
        $unpacked = unpack('H*hex', $data);
        return '0x' . $unpacked['hex'];
    }

}