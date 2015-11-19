<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;

$increment = 10;
$ex = $o->params->ex;
$teil = $o->params->t;
$palCount = intval($o->params->palCount);
$verpMenge = intval($o->params->verpMenge);
$verpRest = intval($o->params->verpRest);

$lastPal = $a->getLastPalBehExport($o->params->ex);
if($lastPal==0){
    // stejna logika jako u importu, ale odvozuju od exportu
    $lastExNr = substr($ex, -1);
    $lastPal = intval($lastExNr."000");
}

$startPal = $lastPal + $increment;


if(strlen($ex)>0 && $palCount>0 && $verpMenge>0){
    if($verpRest>0){
	$fullPalCount = $palCount - 1;
    }
    else{
	$fullPalCount = $palCount;
    }
    $counter = 0;
    for($pal = $startPal;$counter<$fullPalCount;$counter++){
	$sql = "insert into dbehexport (export,teil,ex_pal,ex_stk_gut) values('$ex','$teil','$pal','$verpMenge')";
	$a->query($sql);
	$pal+=$increment;
    }
    if($verpRest>0){
	$sql = "insert into dbehexport (export,teil,ex_pal,ex_stk_gut) values('$ex','$teil','$pal','$verpRest')";
	$a->query($sql);
    }
}
    
$sqlBehArray = "select * from dbehexport where dbehexport.teil='$teil' and dbehexport.export='$ex' order by gedruckt_am,teil,ex_pal";
//$sqlBehArray = "select * from dbehexport where dbehexport.teil='$teil' and dbehexport.export='$ex' order by teil,ex_pal";
$behArray = $a->getQueryRows($sqlBehArray);

//zjistim kolik mam pozic bez datumu, abych mohl povolit/zakazat tlacitko pro tisk - tisknu jen pozice bez datumu
$nochNichtGedruckt = 0;
if($behArray!==NULL){
    foreach ($behArray as $beh){
	$gedrucktAm = trim($beh['gedruckt_am']);
	if(strlen($gedrucktAm)==0){
	    $nochNichtGedruckt++;
	}
    }
}

$returnArray = array(
	'params'=>$o->params,
	'lastPal'=>$lastPal,
	'startPal'=>$startPal,
	'behArray'=>$behArray,
	'nochNichtGedruckt'=>$nochNichtGedruckt,
    );
    
echo json_encode($returnArray);
