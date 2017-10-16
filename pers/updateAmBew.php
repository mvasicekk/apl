<?
session_start();
require_once '../db.php';
require_once '../sqldb.php'; '';

$data = file_get_contents("php://input");
$o = json_decode($data);
$pa = $o->pa;
$field = $o->field;
$value = $o->value;
$id =$pa->id;
$sqlDB = sqldb::getInstance();
$a = AplDB::getInstance();

$ident = $a->get_user_pc();

if($id>0){
    $sql = "";
    if($field=='Bemerkung'){
	$sql = "update dambew set `$field`='$value',comp_user_accessuser='$ident' where id='$id' limit 1";
    }

    if($field=='Datum'){
	//$sql = "update dambew set `$field`='$value',comp_user_accessuser='$ident' where id='$id' limit 1";
	
    }
    
    if(strlen($sql)>0){
	$ar = $a->query($sql);
    }
}
$returnArray = array(
    'field'=>$field,
    'value'=>$value,
    'ar'=>$ar,
    'sql'=>$sql,
    'userRoles'=>$userRoles,
    'u'=>$u,
    'pa'=>$pa,
);

echo json_encode($returnArray);

