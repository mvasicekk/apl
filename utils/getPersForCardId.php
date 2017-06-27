<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$a = AplDB::getInstance();

$cardId=$o->cardid;
$readerId=$o->readerid;


// ulozi do rfidreader
$sql = "insert into rfidreader (cardid,readerid) values('$cardId','$readerId')";
$insertId = $a->insert($sql);

//simulovat zapis ctecky otisku

$sql = "select * from dpers where cardid125='$cardId'";
$rows = $a->getQueryRows($sql);
if($rows!==NULL){
    $persinfo = $rows[0];
    $sqlinsert = "insert into edata_access_events (class,time,dt,type,address,badgenumber,persnr) values('access',UNIX_TIMESTAMP(),NOW(),'Access granted','$readerId','karta:$cardId','".$persinfo['PersNr']."')";
    $a->insert($sqlinsert);
}
else{
    $persinfo = NULL;
}

$returnArray = array(
        'persinfo'=>$persinfo,
	'insertid'=>$insertId
);

echo json_encode($returnArray);
