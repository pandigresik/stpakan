<?php
class M_kalenderlibur extends MY_Model{
	protected $_table = 'm_kalender';
	public function __construct(){
		parent::__construct();
		$this->dbSqlServer = $this->load->database("default", true);
	}


	function get_all(){
		$sql = <<<QUERY
			select * from m_kalender
QUERY;
			
		$stmt = $this->dbSqlServer->conn_id->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function connect()
    {
        try {
            $dbc = new \PDO($this->dbSqlServer->dsn, $this->dbSqlServer->username, $this->dbSqlServer->password);

            $dbc->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return (Object)array(
                'return' => $dbc,
                'message' => 'connected.'
            );
        } catch (\PDOException $e) {
            return (Object)array(
                'return' => false,
                'message' => $e->getMessage()
            );
        }
    }

	public function insertkalender($data) {
        $keterangan = $data ['keterangan'];
        $daterange = $data ['daterange'];

        $fg = [];

        foreach ($daterange as $key => $value) {
        	try {
	        	$pdo = $this->connect()->return;

	        	$stmt = $pdo->prepare("insert into M_KALENDER(TANGGAL, KETERANGAN)values(?, ?)");
		        $stmt->execute(array(
		            $value,
		            $keterangan
		        ));

		        array_push($fg, ['status' => true, 'tgl' => $value, 'message' => '']);
        	} catch(\PDOException $e) {
        		array_push($fg, ['status' => false, 'tgl' => $value, 'message' => $e->getMessage()]);
        	}
        }

        return $fg;
    }

    public function updatekalender($data) {
        $keterangan = $data ['keterangan'];
        $daterange = $data ['daterange'];

        $fg = [];

        foreach ($daterange as $key => $value) {
        	try {
	        	$pdo = $this->connect()->return;

	        	$stmt = $pdo->prepare("delete mk from M_KALENDER mk where mk.tanggal = ?");
		        $stmt->execute(array(
		            $value
		        ));

	        	$stmt = $pdo->prepare("insert into M_KALENDER(TANGGAL, KETERANGAN)values(?, ?)");
		        $stmt->execute(array(
		            $value,
		            $keterangan
		        ));

		        array_push($fg, ['status' => true, 'tgl' => $value, 'message' => '']);
        	} catch(\PDOException $e) {
        		array_push($fg, ['status' => false, 'tgl' => $value, 'message' => $e->getMessage()]);
        	}
        }

        return $fg;
    }

    public function deletekalender($data) {
        $daterange = $data ['daterange'];

        $fg = [];

        foreach ($daterange as $key => $value) {
        	try {
	        	$pdo = $this->connect()->return;

	        	$stmt = $pdo->prepare("delete mk from M_KALENDER mk where mk.tanggal = ?");
		        $stmt->execute(array(
		            $value
		        ));

		        array_push($fg, ['status' => true, 'tgl' => $value, 'message' => 'Berhasil menghapus ' . $value]);
        	} catch(\PDOException $e) {
        		array_push($fg, ['status' => false, 'tgl' => $value, 'message' => $e->getMessage()]);
        	}
        }

        return $fg;
    }
}
