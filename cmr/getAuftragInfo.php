<?php
session_start();
require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

$auftragsnr = $_GET['auftragsnr'];

$auftragInfo = NULL;
$zielortInfo = NULL;
$zielortInfoStandard = NULL;

$auftragInfoA = $a->getAuftragInfoArray($auftragsnr);
if($auftragInfoA!==NULL){
    $auftragInfo = $auftragInfoA[0];
    $zoI = $a->getZielortInfoArray($auftragInfo['zielort_id']);
    if($zoI!==NULL){
	$zielortInfo = $zoI[0];
	if(intval($zielortInfo['standard'])!=10){
	    // export nema standardni zielort , zjistit info o standardnim zielortu
	    $zoIS = $a->getZielortStandardInfoArray($zielortInfo['kunde']);
	    if($zoIS!==NULL){
		$zielortInfoStandard = $zoIS[0];
	    }
	}
    }
}

$rundlaufInfo = NULL;
$rundlaufId = $a->getRundlaufIdForExport($auftragsnr);
if($rundlaufId!==NULL){
    $rI = $a->getRundlaufInfoArray($rundlaufId);
    if($rI!==NULL){
	$rundlaufInfo = $rI[0];
	$lieferantA = $a->getSpediteurArray($rundlaufInfo['dspediteur_id']);
	if($lieferantA!==NULL){
	    $rundlaufInfo['spediteurname'] = $lieferantA[0]['name'];
	}
    }
}

$pokynyProOdesilatele = "Der Fahrer wurde auf Ladungsicherung hingewiesen.\nTeile in rostfreiem Zustand ūbernommen";

$puser = $_SESSION['user'];
$userFullA = $a->getUserInfoArray($puser);
$userFull  = $userFullA['realname'];

$palArray = NULL;

$palArrayA = $a->getBehaelterInExport($auftragsnr);
if($palArrayA!==NULL){
    $palArray = $palArrayA;
}

$returnArray = array(
    'palArray'=>$palArray,
    'pokynyProOdesilatele'=>$pokynyProOdesilatele,
    'zielortInfoStandard'=>$zielortInfoStandard,
    "zielOrtInfo"=>$zielortInfo,
    "rundlaufInfo"=>$rundlaufInfo,
    "auftragInfo"=>$auftragInfo,
    "user"=>$puser,
    "userFull"=>$userFull
);

echo json_encode($returnArray);
