<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$a = AplDB::getInstance();

$kdVon=$o->kdvon;
$kdBis=$o->kdbis;

$sql=" select";
$sql.="     dispostatnrvzkd.statnr,";
$sql.="     sum(dispostatnrvzkd.vzkd) as vzkd";
$sql.=" from dispostatnrvzkd";
$sql.=" where";
$sql.="     kunde between '$kdVon' and '$kdBis'";
$sql.="     and datum=DATE_FORMAT(NOW(),'%Y-%m-%d')";
$sql.=" group by";
$sql.="     statnr";
    
$rows = $a->getQueryRows($sql);

$returnArray = array(
        'statArray'=>$rows,
);

echo json_encode($returnArray);
