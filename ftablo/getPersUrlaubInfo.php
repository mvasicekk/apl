<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$urlaubInfo = $a->getUrlaubBisDatum($persnr, date('Y-m-d'));
$maStunden = $a->getPlusMinusStunden(date('m'), date('Y'), $persnr);

$urlaubInfo["maStunden"] = $maStunden;

$returnArray = array(
    'urlaubInfo'=>$urlaubInfo,
    'u' => $u,
    'sql' => $sql,
    'persnr'=>$persnr
);

echo json_encode($returnArray);
