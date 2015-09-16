<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$index2dbField = array(
    'bemerkung'=>'bemerkung',
    'fremdpos'=>'fremdpos',
    'giesstag'=>'giesstag',
    'fremdauftr'=>'fremdauftr',
    'im_stk'=>'Stück',
    'plan'=>'termin'
);

$index = $_POST['index'];
$id = $_POST['id'];
$val = $_POST['value'];

$field = $index2dbField[$index];
$r=0;
if(strlen($field)>0){
    $r = $a->updateDauftrFieldAllePositionenProPalFromId($field, $val, $id);
    // pokud upravuju kusy a skutecne doslo k update kusu, musim upravit importni lager
    if(($index=='im_stk')&&($r>0)){
	$a->updateDlagerImportStkForDauftrId($id, $val);
    }
}
    

$ar = array(
    'index'=>$index,
    'field'=>$field,
    'id'=>$id,
    'value'=>$val,
    'sql'=>$sql,
    'r'=>$r,
);

echo json_encode($ar);