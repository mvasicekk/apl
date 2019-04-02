<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$id = intval($o->id);


if(($id>0)){
    $a = AplDB::getInstance();
    $ident = $a->get_user_pc();
    $sqlDel = "delete from drueckplus where id='$id'";
    $a->query($sqlDel);
}


$returnArray = array(
	'ident'=>$ident,
    );
    
echo json_encode($returnArray);
