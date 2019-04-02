<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 13.11.2017
 * Time: 16:25
 */

session_start();
require_once '../db.php';
require "../fns_dotazy.php";
dbConnect();
$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$id= $o->id;
$typ = $o->typ;
$ks = $o->ks;
$pozn = $o->pozn;


$sql = "select id from vydej where id = $id order by stamp desc limit 1";
$resul = $a->getQueryRows($sql);

foreach ($resul as $v3){
    $id = $v3['id'];
}
$ident = get_user_pc();
$user = substr($ident, strpos($ident, "/") + 1);


if($id !== null){
    $sql1 = "update `vydej` set  `poznamka` = '$pozn',`vracena` = '$typ',`ks_vydano` = '$ks'  where `vydej`.`id` = '$id' ";
    $b = $a->query($sql1);
}