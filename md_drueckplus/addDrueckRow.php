<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$sys = intval($o->sys);
$et_invnummer = $o->et_invnummer;

if(($sys>0) && (strlen((trim($et_invnummer)))>0)){
    $a = AplDB::getInstance();
    $ident = $a->get_user_pc();
    $sqlInsert = "insert into drueckplus (drueck_id,et_invnummer,user) values('$sys','$et_invnummer','$ident')";
    $insertId = $a->insert($sqlInsert);
}


$returnArray = array(
	'insertId'=>$insertId,
	'ident'=>$ident,
    );
    
echo json_encode($returnArray);
