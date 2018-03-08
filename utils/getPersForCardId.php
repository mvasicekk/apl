<?php
session_start();
require_once '../db.php';

//$data = file_get_contents("php://input");

//$o = json_decode($data);
$a = AplDB::getInstance();

//$cardId=$o->cardid;
$cardId=$_GET['cardid'];
//$readerId=$o->readerid;
$readerId=$_GET['readerid'];;


// ulozi do rfidreader
$sql = "insert into rfidreader (cardid,readerid) values('$cardId','$readerId')";
$insertId = $a->insert($sql);

//zjistim informace o ctecce
$sql = "select * from cardreader where readerid='$readerId'";
$rs = $a->getQueryRows($sql);
if($rs===NULL){
    $readerInfo = $rs;
}
else{
    $readerInfo = $rs[0];
}
//simulovat zapis ctecky otisku

$sql = "select dpers.*,persnr_tag.pozn as perstag_pozn from dpers ";
$sql.=" join persnr_tag on persnr_tag.persnr=dpers.persnr";
$sql.=" where persnr_tag.tag='$cardId'";
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
	'cardid'=>$cardId,
	'readerid'=>$readerId,
	'readerinfo'=>$readerInfo,
	//'sql'=>$sql,
        'persinfo'=>$persinfo,
	'insertid'=>$insertId
);

echo json_encode($returnArray);
