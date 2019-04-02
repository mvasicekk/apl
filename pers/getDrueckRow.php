<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$sys = intval($o->sys);
$rows = NULL;

if($sys>0){
    $a = AplDB::getInstance();
    $sql = "select persnr from drueck where drueck_id='$sys'";
    $rows = $a->getQueryRows($sql);
    $ident = $a->get_user_pc();
}


$returnArray = array(
	'rows'=>$rows,
	'sys'=>$sys,
	'ident'=>$ident,
    );
    
echo json_encode($returnArray);
