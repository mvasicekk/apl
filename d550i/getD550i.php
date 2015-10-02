<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

//$terminMatch = trim($_GET['termin']);
$kundeVon = trim($_GET['kundevon']);
$kundeBis = trim($_GET['kundebis']);
$datumVon = trim($_GET['von']);
$datumBis = trim($_GET['bis']);
if($datumVon!=NULL && $datumBis!=NULL){
    $datumVon = date('Y-m-d',  $datumVon/1000);
    $datumBis = date('Y-m-d',  $datumBis/1000)." 23:59:59";
}


$returnArray = array(
    'von'=>$datumVon,
    'bis'=>$datumBis,
    'kundevon'=>$kundeVon,
    'kundebis'=>$kundeBis,
    "zeilen" => $zeilenArray,
    "teileKeysArray"=>$teileKeysArray,
);

echo json_encode($returnArray);
