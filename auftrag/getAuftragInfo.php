<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

$auftragsnr = $_GET['auftragsnr'];

$auftragInfo = NULL;

$auftragInfoA = $a->getAuftragInfoArray($auftragsnr);
if($auftragInfoA!==NULL){
    $auftragInfo = $auftragInfoA[0];
}

// dauftrpositionen

$dauftrPos = $a->getDauftrRowsForImport($auftragsnr);
if($dauftrPos!==NULL){
    $oldpal = $dauftrPos[0]['imp_pal'];
    foreach($dauftrPos as $p=>$row){
	//zjistim zda ma exportni cislo u pozice fakturu
	$ex = $row['ex'];
	$hatRechnung = 0;
	if(strlen(trim($ex))>0){
	    $exInfoArray = $a->getAuftragInfoArray($auftragsnr);
	    $hatRechnung = $exInfoArray[0]['hatrechnung'];
	}
	$dauftrPos[$p]['hatrechnung']=$hatRechnung;
	$dauftrPos[$p]['edit']=0;
	if($row['imp_pal']!=$oldpal){
	    $dauftrPos[$p]['newpal']=1;
	    $oldpal = $row['imp_pal'];
	}
	else{
	    $dauftrPos[$p]['newpal']=0;
	}
    }
}

$returnArray = array(
    "auftragInfo"=>$auftragInfo,
    "dauftrPos"=>$dauftrPos
);

echo json_encode($returnArray);
