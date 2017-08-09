<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$apl = AplDB::getInstance();
$pal = $o->pal;
//$pal = 5;

$sql = "select stator,vyrazen_datum,bemerkung,warning,danger from dstator where paleta = '$pal'";
$res = $apl->getQueryRows($sql);
$retArray = array(
  "res" => $res
);

echo json_encode($retArray);

?>
