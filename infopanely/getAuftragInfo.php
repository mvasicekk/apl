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
