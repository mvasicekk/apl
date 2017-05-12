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



$jmArray = array_keys($jahrMonatArray);

$returnArray = array(
	'jmArray'=>$jmArray,
	'von'=>$von,
	'bis'=>$bis,
	'firemniFaktory'=>$firemniFaktory,
	'u'=>$u
    );
    
echo json_encode($returnArray);
