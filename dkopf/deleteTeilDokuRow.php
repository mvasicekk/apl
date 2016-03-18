<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$p = $o->params->r;

$id = intval($p->id);
$a = AplDB::getInstance();

if($id>0){
    $sql = "delete from dteildokument where id='$id' limit 1";
    $a->query($sql);
}

$returnArray = array(
	'p'=>$p,
	'id'=>$id,
    );
    
echo json_encode($returnArray);
