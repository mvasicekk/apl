<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 14.11.2017
 * Time: 7:12
 */
session_start();
require_once '../db.php';
require "../fns_dotazy.php";
dbConnect();
$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();
$date = date('Y-m-d', strtotime($o->dat));
$sklad = $o->sklad;
if($sklad == '1'){
    $sk = '6';
}else if($sklad == '2'){
    $sk = '11';
}
$sqlS = "Select cislo, popis from sez_skl_isp where cislo like '$sk' limit 1 ";
$sks = $a->getQueryRows($sqlS);
foreach ($sks as $skl){
    $cskladu = $skl['popis'];
}
mysql_query('set names utf8');
$sql = "select id,inv,pocet_ks,popis from vydej where stamp like '%$date%' and Typ like '20' and reklamace like '0' and vracena like '0' order by stamp desc ";
$result = $a->getQueryRows($sql);
$ident = get_user_pc();
$user = substr($ident, strpos($ident, "/") + 1);
//echo json_encode($result);
// poslat email

// debug info

$recipient = "mva@abydos.cz";
$recipient .= ",ko@abydos.cz";
$recipient .= ",ino@abydos.cz";

$subject = "Fasovaní od $user ($cskladu)";
$message = "<h3><b>Požadavek</b> na fasování.</h3>";
foreach ($result as $r ){
    $message .= $r['inv']." " .$r['popis']." " . $r['pocet_ks']. " ks" . "<br>";
}
$headers = "From: <fasovani@abydos.cz>\n";
$headers = "Content-Type: text/html; charset=UTF-8\n";
@mail($recipient,$subject,$message,$headers);


foreach ($result as $c ){
    $id = $c['id'];
    $sql1 = "update `vydej` set  `reklamace` = '1'  where `vydej`.`id` = '$id' ";
    $b = $a->query($sql1);
}
