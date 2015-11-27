<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

//$terminMatch = trim($_GET['termin']);
$persVon = intval(trim($_GET['persvon']));
$persBis = intval(trim($_GET['persbis']));
$datumVon = trim($_GET['von']);
$datumBis = trim($_GET['bis']);


if ($datumVon != 0 && $datumBis != 0) {
    $datumVon = date('Y-m-d', $datumVon / 1000);
    $datumBis = date('Y-m-d', $datumBis / 1000);
}

$sql.=" select";
$sql.="     drueck.PersNr as persnr,";
$sql.="     drueck.Teil,";
$sql.="     drueck.insert_stamp,";
$sql.="     drueck.`Stück` as stk,";
$sql.="     drueck.Datum as datum,";
$sql.="     dkopf.Gew as teil_gew,";
$sql.="     count(TaetNr) as tat_count,";
$sql.="     sum(`Auss-Stück`) as stk_auss_sum";
$sql.=" from";
$sql.="     drueck";
$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
$sql.=" where";
$sql.="     PersNr between '$persVon' and '$persBis'";
$sql.="     and Datum between '$datumVon' and '$datumBis'";
$sql.="     and (DATE_FORMAT(`verb-von`,'%H:%i:%s')!='00:00:00')";
$sql.=" group by";
$sql.="     PersNr,";
$sql.="     drueck.Teil,";
$sql.="     drueck.insert_stamp,";
$sql.="     drueck.`Stück`";
    

$persRows = $a->getQueryRows($sql);
$monthsArray = array();

if($persRows!==NULL){
    foreach ($persRows as $pr){
    $persnr = $pr['persnr'];
    $datum = $pr['datum'];
    $month = date('m',  strtotime($datum));
    $yearMonth = date('y-m',  strtotime($datum));
    $monthsArray[$yearMonth]+=1;
    $stkGut = intval($pr['stk']);
    $stkAuss = intval($pr['stk_auss_sum']);
    $gew = floatval($pr['teil_gew']);
    
    $zeilen[$persnr][$yearMonth]['stk_gut']+=$stkGut;
    $zeilen[$persnr][$yearMonth]['stk_auss']+=$stkAuss;
    $zeilen[$persnr][$yearMonth]['sum_gew']+=($stkGut+$stkAuss)*$gew;
}

$monthsArray = array_keys($monthsArray);
sort($monthsArray);
}



$zeilenArray = array();

$returnArray = array(
    'monthsArray'=>$monthsArray,
    'zeilenraw' => $zeilen,
    'von' => $datumVon,
    'bis' => $datumBis,
    'persvon' => $persVon,
    'persbis' => $persBis,
    "zeilen" => $zeilenArray,
);

echo json_encode($returnArray);