<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$a = AplDB::getInstance();

$kdVon=$o->kdvon;
$kdBis=$o->kdbis;

$sql.=" select";
$sql.="     `dtaetkz-abg`.Stat_Nr as statnr,";
$sql.="     round(sum(if(auss_typ=4,drueck.`VZ-SOLL`*(drueck.`Stück`+drueck.`Auss-Stück`),drueck.`VZ-SOLL`*(drueck.`Stück`)))) as vzkd";
$sql.=" from";
$sql.="     drueck";
$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
$sql.=" join dksd on dksd.Kunde=daufkopf.kunde";
$sql.=" join `dtaetkz-abg` on `dtaetkz-abg`.`abg-nr`=drueck.TaetNr";
$sql.=" where";
$sql.="     dksd.Kunden_Stat_Nr=1";
$sql.="     and";
$sql.="     drueck.Datum=DATE_FORMAT(NOW(),'%Y-%m-%d')";
$sql.=" group by";
$sql.="     `dtaetkz-abg`.Stat_Nr";
	    
    
$rows = $a->getQueryRows($sql);

$returnArray = array(
        'vzkdArray'=>$rows,
);

echo json_encode($returnArray);
