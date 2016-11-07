<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$von = $o->von;
$bis = $o->bis;

$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];


$jahrMonatArray = array();
$start = strtotime($von);
$end = strtotime($bis);
$step = 24 * 60 * 60;
for ($t = $start; $t <= $end; $t+=$step) {
    $jm = date('Y-m', $t);
    $jahrMonatArray[$jm] += 1;
}

$jmArray = array_keys($jahrMonatArray);

$returnArray = array(
	'jmArray'=>$jmArray,
	'von'=>$von,
	'bis'=>$bis,
	'u'=>$u
    );
    
echo json_encode($returnArray);
