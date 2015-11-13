<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$field = $o->params->field;
$value = $o->params->value;
$id = $o->params->id;

$ar = $a->updateBehExport($field,$value,$id);
$returnArray = array(
	'field'=>$field,
	'value'=>$value,
	'id'=>$id,
	'ar'=>$ar,
    );
    
echo json_encode($returnArray);
