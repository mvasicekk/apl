<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

// oeArray
$sql = "";
$sql.=" select doe.oe,doe.beschreibung_cz from doe where stredisko_isp is not null order by doe.oe";
$oeArray = $a->getQueryRows($sql);
array_unshift($oeArray, array('oe'=>'*','beschreibung_cz'=>'vše'));
$oeSelected = '*';


$returnArray = array(
	'oeArray'=>$oeArray,
	'oeSelected'=>$oeSelected,
	'u'=>$u
    );
    
echo json_encode($returnArray);
