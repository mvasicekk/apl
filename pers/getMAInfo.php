<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$persinfo = NULL;
$a = AplDB::getInstance();

$u = $_SESSION['user'];

$sql="select * from dpers where ";
$sql.=" ((`PersNr`='$persnr'))";
$ma = $a->getQueryRows($sql);


$returnArray = array(
	'u'=>$u,
	'ma'=>$ma,
	'sql'=>$sql,
    );
    
echo json_encode($returnArray);
