<?
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$rundlaufInfo = $o->params->rundlaufInfo;
$field = $o->params->field;
$value = $rundlaufInfo->{$field};

$a = AplDB::getInstance();
$ar = 0;

$ar = $a->updateRundlaufField($field, $value, $rundlaufInfo->id);

$returnArray = array(
	'ar'=>$ar,
	'rundlaufInfo'=>$rundlaufInfo,
	'field'=>$field,
	'value'=>$value
    );
    
    echo json_encode($returnArray);
