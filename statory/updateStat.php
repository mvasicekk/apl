<?php
session_start();
require_once '../security.php';
include "../fns_dotazy.php";
require_once '../db.php';
dbConnect();

mysql_query('set names utf8');
$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();


$bemerkung = $o->bb;
$stator = $o->stat;
$warning = $o->warning;
$danger = $o->danger;
$vyradit = $o->vyradit;
$ident = get_user_pc();
if($vyradit ==null){
  $newDate = "0000-00-00";
}else{
  $newDate = date("Y-m-d", strtotime($vyradit));
}

if($warning == true){
  $war = 1;
}else{
  $war = 0;
}
if($danger == true){
  $dang = 1;
}else{
  $dang = 0;
}
// ,vyrazen_datum= '$newDate'
$sql =" update dstator set bemerkung = '$bemerkung', warning = '$war',danger= '$dang' ,vyrazen_datum= '$newDate', user = '$ident' where stator = '$stator'";
$a->query($sql);
$retArray = array(
  "a" => $newDate
);

echo json_encode($retArray);
 ?>
