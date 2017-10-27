<?php
require '../db.php';
$apl = AplDB::getInstance();

$code = $_GET['p'];
$u = $_GET['u'];
$tag = $_GET['tag'];
$dir = $_GET['dir'];

//$post = $_POST;

if(strlen($code)>0){
    
    $sql = "insert into barcode_scanner (ean,tag,user,direction) values('$code','$tag','$u','$dir')";
    $insertId = $apl->insert($sql);
    echo "kod $code nacten :-), vlozen do DB (id=$insertId) \n";
}
else{
    //print_r($post);
    echo "nic ? :-(\n";
}