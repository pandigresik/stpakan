<?php
 if (!defined('BASEPATH')) {
     exit('No direct script access allowed');
 }

$config['berat_standart'] = 50;

/* configurasi untuk sinkronisasi stpakan*/
$config['serverUtama'] = 'FM';
$config['serverDirektur'] = 'KRM';
$config['idFarm'] = 'CJ'; /* id farm ini tergantung difarm mana komputer tersebut berada */
$config['filetimbang'] = 'file_upload';
/* setting general untuk timbangan, fingerprint dll suatu saat bisa ambil dari database */
$config['lockTimbangan'] = 1; /* jika 1, berarti user tidak bisa entry, jika 0 user bisa entry */
$config['lockFinger'] = 1; /* jika 1, berarti user harus melakukan finger, jika 0 gak usah finger */
$config['lockEntryRHK'] = 1; /* jika 1, berarti user harus melakukan finger, jika 0 gak usah finger */

$config['smsDO'] = array(
    /*'GD' => array('085791726948'),*/
    'GD' => array('081939344473'),
   /* 'CJ' => array('087855977704'),*/
    'BW' => array('085733659400'),
    'JD' => array('085733659400'),
);
$config['namaFarm'] = array(
    'GD' => 'Gondang',
    'CJ' => 'Cobanjoyo',
    'BW' => 'Benerwojo',
    'JD' => 'Jatiduwur',
    'WK' => 'Wangkal',
);

$config['kodeFarm'] = array(
    'GONDANG' => 'GD',
    'COBANJOYO' => 'CJ',
    'BENERWOJO' => 'BW',
    'JATIDUWUR' => 'JD',
    'WANGKAL' => 'WK',
);

$config['versiBaru'] = array(
    'GD' => 1,
    'CJ' => 1,
    'WK' => 1,
    'BW' => 0,
    'JD' => 0,
 );

 $config['versi3'] = array(
    'GD' => 1,
    'CJ' => 0,
    'WK' => 1,
    'BW' => 0,
    'JD' => 0,
 );
