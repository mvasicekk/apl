<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$p = $o->params->r;

$dpos_id = intval($p->dpos_id);
$a = AplDB::getInstance();

if($dpos_id>0){
    $sql = "delete from dpos where dpos_id='$dpos_id' limit 1";
    $a->query($sql);
}

$returnArray = array(
	'p'=>$p,
	'dpos_id'=>$dpos_id,
    );
    
echo json_encode($returnArray);
