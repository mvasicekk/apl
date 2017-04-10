<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$a = AplDB::getInstance();

$kdVon=$o->kdvon;
$kdBis=$o->kdbis;

// S0081 bude delat rozdil hl, ok vi, souhlasi
$sql=" select";
$sql.="     dispostatnrvzkd.statnr,";
$sql.="     sum(dispostatnrvzkd.vzkd) as vzkd";
$sql.=" from dispostatnrvzkd";
$sql.=" join dksd on dksd.Kunde=dispostatnrvzkd.kunde";
$sql.=" where";
$sql.="     dksd.Kunden_Stat_Nr=1";
$sql.="     and datum=DATE_FORMAT(NOW(),'%Y-%m-%d')";
$sql.="     and";
$sql.="     dispostatnrvzkd.statnr<>'S0081'";

$sql.=" group by";
$sql.="     statnr";
    
$rows = $a->getQueryRows($sql);

$returnArray = array(
        'statArray'=>$rows,
);

echo json_encode($returnArray);
