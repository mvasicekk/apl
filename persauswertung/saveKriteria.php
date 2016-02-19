<?
session_start();
require_once '../db.php';

    $data = file_get_contents("php://input");
    
    $inputData = json_decode($data);

    $value = $inputData->v;
    $id = $inputData->kriteria->id;
    $field = $inputData->kriterium;

    if($field=='betrag'||$field=='grenze'){
	$value = floatval(strtr($value, ',', '.'));
    }
    
    
    $apl = AplDB::getInstance();
    
    $ar = $apl->updateBewertungKriteriaField($id,$field,$value);
    
    $returnArray = array(
	'inputData'=>$inputData,
	'ar'=>$ar,
	'value'=>$value,
	'id'=>$id,
	'field'=>$field
    );
    
    echo json_encode($returnArray);
