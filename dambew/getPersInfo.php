<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$persinfo = NULL;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$sql="select  dpers.persnr,`name`,vorname,regeloe from dpers where ((`PersNr`='".$persnr."') and ((austritt is null) or (DATEDIFF(NOW(),austritt)<60)))";
$persinfo = $a->getQueryRows($sql);
if($persinfo!=NULL){
    $persinfo = $persinfo[0];
}

$returnArray = array(
	'u'=>$u,
	'persinfo'=>$persinfo,
	
	'persnr'=>$persnr,
	'sql'=>$sql
    );
    
echo json_encode($returnArray);
