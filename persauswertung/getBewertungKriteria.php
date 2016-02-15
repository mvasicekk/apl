<?
session_start();
require_once '../db.php';

    $data = file_get_contents("php://input");
    
    $inputData = json_decode($data);
    
    $kunde = $inputData->kunde;
    $bereich = $inputData->bereich;

    $apl = AplDB::getInstance();
    
    if(strlen($kunde)>0 && strlen($bereich)>0){
	$sql = "select * from bewertung_kriteria where kunde='$kunde' and bereich='$bereich' order by bereich,interval_monate,grenze";
    }
    else{
	$sql = "select * from bewertung_kriteria where kunde='$kunde' order by bereich,interval_monate,grenze";
    }
    $bewertungKriteriaRows = $apl->getQueryRows($sql);
    
    $returnArray = array(
	'inputData'=>$inputData,
	'kunde'=>$kunde,
	'bewertungKriteriaRows'=>$bewertungKriteriaRows
    );
    
    echo json_encode($returnArray);
