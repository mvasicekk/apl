<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 13.11.2017
 * Time: 13:47
 */

session_start();
require_once '../db.php';
require "../fns_dotazy.php";
dbConnect();
$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();
$inv = $o->co;
$z = $o->z;
$d = $o->d;
$ks = $o->pcs;
$pozn = $o->pozn;
$ident = get_user_pc();
$user = substr($ident, strpos($ident, "/") + 1);

$popis = substr($inv, strpos($inv, " ") + 1);
$int = $firstCharacter = substr($inv, 0, 6);
$datum = date('Y-m-d H:i:s', strtotime($o->datum));
if($inv !== null && $z!== null && $d!== null && $ks !== null){
    $sql = "insert into vydej (`id`,`inv`,`popis`,`pers_kdo`,`poznamka`,`stamp`,`Typ`,`pocet_ks`,`show_from`,`show_to`) VALUES 
('','$int','$popis','$user','$pozn','$datum','20','$ks','$z','$d')";
    $result = $a->query($sql);
    echo $datum;
}else{
    echo "ERROR";
}
