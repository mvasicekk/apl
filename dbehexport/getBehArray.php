<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;

$ex = $o->params->ex;
$teil = $o->params->t;

$sqlBehArray = "select * from dbehexport where dbehexport.teil='$teil' and dbehexport.export='$ex' order by gedruckt_am,teil,ex_pal";
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

$sqlBehArray = "select * from dbehexport where dbehexport.teil='$teil' and dbehexport.export='$ex' order by teil,ex_pal";
$behArray = $a->getQueryRows($sqlBehArray);

$returnArray = array(
	'params'=>$o->params,
	'behArray'=>$behArray,
	'nochNichtGedruckt'=>$nochNichtGedruckt,
    );
    
echo json_encode($returnArray);
