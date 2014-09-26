<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$index2dbField = array(
    'status'=>'status',
    'lieferdatum'=>'lieferdatum',
    'erledigt'=>'erledigt',
);

$index = $_POST['index'];
$id = $_POST['id'];
$val = $_POST['value'];

$field = $index2dbField[$index];
$r=0;
$user = "not set";

if(strlen($field)>0){
    $user = $a->get_user_pc();
    $r = $a->updateEinkaufAufforderungFieldFromId($field, $val, $id,$user);
}
    

$ar = array(
    'index'=>$index,
    'field'=>$field,
    'id'=>$id,
    'value'=>$val,
    'sql'=>$sql,
    'r'=>$r,
    'user'=>$user,
);

echo json_encode($ar);