<?php
require '../db.php';
$apl = AplDB::getInstance();

$code = $_GET['p'];
$u = $_GET['u'];
$tag = $_GET['tag'];
$dir = $_GET['dir'];
$stk = intval($_GET['stk']);
$skrinka = intval($_GET['skrinka']);

//$post = $_POST;

if(strlen($code)>0){
    
    $sql = "insert into barcode_scanner (ean,tag,user,direction,stk,skrinka) values('$code','$tag','$u','$dir','$stk','$skrinka')";
    $insertId = $apl->insert($sql);
    echo "kod:$code,$stk ks,skrinka:$skrinka";
    if($insertId>0){
	echo "\nvlozeno do DB ($insertId)";
    }
}
else{
    //print_r($post);
    echo "nic ? :-(\n";
}