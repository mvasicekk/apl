<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$apl = AplDB::getInstance();
$typ = $o->typ;
//$typ = 3;
$vyrazene = $o->vyrazen;
$datod = strtr($o->odd, '/', '-');
$newDod = date("Y-m-d", strtotime($datod));
if($newDod == '1970-01-01'){
    $newDod = null;
}
$datdo = $o->ddo;
$newDdo = date("Y-m-d", strtotime($datdo));
if($newDdo == '1970-01-01'){
    $newDdo = null;
}

$sql .=  "select drueckplus.id,dstator.vyrazen_datum, drueckplus.drueck_id,drueckplus.et_invnummer,drueck.datum,drueck.PersNr,drueck.TaetNr,";
$sql .=  "      drueck.drueck_id,dstator.warning, dstator_pal.typ, dstator_pal.id from drueckplus";
$sql .=  "      join drueck on drueck.drueck_id=drueckplus.drueck_id";
$sql .=  "      join dstator on dstator.stator = drueckplus.et_invnummer";
$sql .=  "      join dstator_pal on dstator_pal.id = dstator.paleta";
if($newDod == null && $newDdo == null )
$sql .=  "      where drueck.datum >= '2017-01-01 00:00:00'";
if($newDod !== null && $newDdo == null)
$sql .=  "      where drueck.datum >= '$newDod 00:00:00'";
if($newDod == null && $newDdo !== null)
$sql .=  "      where drueck.datum between '2017-01-01 00:00:00' and '$newDdo 00:00:00'";
if($newDod !== null && $newDdo !== null)
$sql .=  "      where drueck.datum between '$newDod 00:00:00' and '$newDdo 00:00:00'";
if($vyrazene !== true)
$sql .=  "      and dstator.vyrazen_datum = ' 0000-00-00'";

$sql .=  "      and dstator_pal.id = '$typ' and drueckplus.et_invnummer like '___y'";
$sql .=  "      order by drueck.datum desc, drueck.TaetNr desc";
$res = $apl->getQueryRows($sql);


$retArray = array(
  "res" => $res,
    "od" => $newDod,
      "do" => $newDdo,
      "sql" => $sql
);

echo json_encode($retArray);

?>
