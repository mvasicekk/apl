<?
require_once '../db.php';
$data = file_get_contents("php://input");
$o = json_decode($data);


$abmahnungId = $o->abmahnungid;
$a = AplDB::getInstance();

$abmahnungInfo = NULL;

$abmahnungInfoA = $a->getAbmahnungInfo($abmahnungId);

if($abmahnungInfoA!==NULL){
    $abmahnungInfo = $abmahnungInfoA[0];
    $textArray = $a->getAbmahnungTexte($a->getAbmahnungGrundIdFromText($abmahnungInfo['grund']));
    $persInfoA = $a->getPersInfoArray($abmahnungInfo['persnr']);
    if($persInfoA!==NULL){
	$persInfo = $persInfoA[0];
    }
    $persDetailInfoA = $a->getPersDetailInfoArray($abmahnungInfo['persnr']);
    if($persDetailInfoA!==NULL){
	$persDetailInfo = $persDetailInfoA[0];
    }
    if(intval($abmahnungInfo['dreklamation_id'])>0){
	$reklInfoA = $a->getReklamationenArray(intval($abmahnungInfo['dreklamation_id']));
	if($reklInfoA!==NULL){
	    $reklInfo=$reklInfoA[0];
	}
    }
    
    //upravim nektere texty, nahradim je hodnotama z reklamace / vytky
    // hodnoty z reklamace
    foreach ($reklInfo as $promenna=>$hodnota){
	// co hledam
	$hledam = "{\$".$promenna."}";
	$textArray['text40'] = str_replace($hledam, $hodnota, $textArray['text40']);
	$textArray['text50'] = str_replace($hledam, $hodnota, $textArray['text50']);
    }
    // hodnoty z vytky
    foreach ($abmahnungInfo as $promenna=>$hodnota){
	// co hledam
	$hledam = "{\$".$promenna."}";
	$textArray['text40'] = str_replace($hledam, $hodnota, $textArray['text40']);
	$textArray['text50'] = str_replace($hledam, $hodnota, $textArray['text50']);
    }
}

$returnArray = array(
	'abmahnungId'=>$abmahnungId,
	'abmahnungInfo'=>$abmahnungInfo,
	'textArray'=>$textArray,
	'persInfo'=>$persInfo,
	'persDetailInfo'=>$persDetailInfo,
	'aktualDatum'=>date('d.m.Y'),
	'reklInfo'=>$reklInfo,
    );
    
echo json_encode($returnArray);
