<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$apl = AplDB::getInstance();
$datStat = $o->stator;
//$datStat = '253y';
$stator =  preg_replace("/[^0-9,.]/", "", $datStat);


$sql .=  "select drueckplus.et_invnummer ,drueck.datum,drueck.TaetNr as tat,dstator.bemerkung as bem,dstator.warning as warn,";
$sql .=  "       dstator.danger as dang, drueck.drueck_id, dstator_pal.typ, dstator.vyrazen_datum from drueckplus";
$sql .=  "       join drueck on drueck.drueck_id=drueckplus.drueck_id";
$sql .=  "       join dstator on dstator.stator = drueckplus.et_invnummer";
$sql .=  "       join dstator_pal on dstator_pal.id = dstator.paleta";
$sql .=  "       where drueck.Datum >= '2017-01-01' ";
$sql .=  "       and dstator.stator like '%$stator%' ";
$sql .=  "       order by drueck.datum desc, drueck.TaetNr desc";

$res = $apl->getQueryRows($sql);
//echo json_encode($res);
$sql1 = "select dreparaturpos.et_invnummer as drEtin,
         dreparaturkopf.id as drId, dreparaturkopf.bemerkung as drRepBemer ,dreparaturkopf.persnr_ma  as drPersMa,dreparatur_anlagen.anlage_bemerkung,
         dreparaturkopf.persnr_reparatur as drPersVon,  dreparaturkopf.invnummer,dreparaturkopf.datum as drDate
         from dreparaturpos
         join dreparaturkopf on dreparaturkopf.id = dreparaturpos.reparatur_id
         join dreparatur_geraete on  dreparatur_geraete.invnummer = dreparaturkopf.invnummer
         join dreparatur_anlagen on dreparatur_anlagen.anlage_id = dreparatur_geraete.anlage_id
         where dreparaturpos.et_invnummer
         like '%$stator%' and dreparaturkopf.datum >= '2017-01-01' order by dreparaturkopf.datum desc ";
$res1 = $apl->getQueryRows($sql1);
//echo json_encode($res1);

$pocet = count($res);
//echo $pocet;
if($res !== null && $stator !== null){
foreach ($res as $d) {
 // Do stuff with $d ...
 $drueckDatum[] = array('datum' => $d['datum'],'TaetNr' => $d['tat'],"bemer" => $d['bem'],"vyrazen" => $d['vyrazen_datum'], "warN"=> $d['warn'], "dang" => $d['dang'] );
  }
}
//echo json_encode($countOper);
if($res1 !==null && $stator !== null){
foreach ($res1 as $value) {
  // Do stuff with $value
  $a[] = array("id"=>$value['drId'] ,"repStamp" => $value['drDate'], "inv" => $value['invnummer'],"nazev" => $value['anlage_bemerkung'], 'persVon' => $value['drPersVon'], "rePbemer" => $value['drRepBemer']  );
  }
}
// drueckInfo datum, reparinfo drDate

$sql2 .=  "select drueckplus.et_invnummer ,drueck.datum,drueck.TaetNr as tat,drueck.PersNr as pers,dstator.bemerkung as bem,dstator.warning as warn,";
$sql2 .=  "       dstator.danger as dang, drueck.drueck_id, dstator_pal.typ, dstator.vyrazen_datum from drueckplus";
$sql2 .=  "       join drueck on drueck.drueck_id=drueckplus.drueck_id";
$sql2 .=  "       join dstator on dstator.stator = drueckplus.et_invnummer";
$sql2 .=  "       join dstator_pal on dstator_pal.id = dstator.paleta";
$sql2 .=  "       where drueck.TaetNr like '8720' ";
$sql2 .=  "       and dstator.stator like '%$stator%' ";
$sql2 .=  "       order by drueck.datum desc, drueck.TaetNr desc";
$res2 = $apl->getQueryRows($sql2);

if($res2 !==null && $stator !== null){
foreach ($res2 as $value2) {
  // Do stuff with $value
  $prevInfo[] = array('datum' => $value2['datum'],'TaetNr' => $value2['tat'], "pers"=> $value2['pers']);
  }
}

$retArray = array(
  "drueckInfo" => $drueckDatum,"reparaturInfo"=> $a, "stator" => $datStat, "prevInfo" => $prevInfo
);

echo json_encode($retArray);
?>
