<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);


$a = AplDB::getInstance();

$aussStk = intval($o->aussStk);
$teil = trim($o->teil);

$teilInfo = $a->getTeilInfoArray($teil);
if($teilInfo!==NULL){
    $kosten_stk_auss = floatval($teilInfo['kosten_stk_auss']);
    if($kosten_stk_auss>0){
	//beru podle kosten_stk_auss
	$preisVorschlag = $aussStk * $kosten_stk_auss;
	$vom = 'kosten_stk_auss';
    }
    else{
	//beru podle werkstoffe
	$werkstoffId = intval($teilInfo['Wst']);
	$gew = floatval($teilInfo['Gew']);
	$kunde = $teilInfo['Kunde'];
	$werkstoffPreis = $a->getWerkstoffPreis($kunde,$werkstoffId);
	$preisVorschlag = $aussStk * $gew * $kosten_stk_auss;
	$vom = 'werkstoffpreis';
    }
}

$user = $_SESSION['user'];
$userpc = $a->get_user_pc();


$returnArray = array(
    'aussStk'=>$aussStk,
    'teil'=>$teil,
    'vom'=>$vom,
    'preisVorschlag'=>$preisVorschlag,
);

echo json_encode($returnArray);
