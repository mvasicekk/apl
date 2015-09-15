<?
require_once '../db.php';
$data = file_get_contents("php://input");

$o = json_decode($data);
$firstPal = $o->firstPal;
$attrs = $o->attrs;
$stkincA = split(':', $o->attrs->stkinc);



$palStk = intval($stkincA[0]);
$increment = intval($stkincA[1]);
$auftragsnr = $stkincA[2];

$a = AplDB::getInstance();
$ar = 0;
$palCanExist = FALSE;
$errorMessage = "";
$palArray = array();
$palArrayCreate = array();
$palIntersect = array();

for($i=0;$i<$palStk;$i++){
    array_push($palArrayCreate, $firstPal+$i*$increment);
}

$sql = "select dauftr.`pos-pal-nr` as pal from dauftr where auftragsnr='$auftragsnr' and kzgut='G' order by `pos-pal-nr`";
$palArray1 = $a->getQueryRows($sql);
if($palArray1!==NULL){
    foreach ($palArray1 as $r){
	array_push($palArray, intval($r['pal']));
    }
    $palIntersect = array_intersect($palArray, $palArrayCreate);
    $palCanExist = count($palIntersect)>0;
    if($palCanExist){
	$errorMessage = "Pal existieren : ".join(',', $palIntersect);
    }
}


$returnArray = array(
	'palCanExist'=>$palCanExist,
	'errorMessage'=>$errorMessage,
	'firstPal'=>$firstPal,
	'attrs'=>$attrs,
	'palStk'=>$palStk,
	'increment'=>$increment,
	'auftragsnr'=>$auftragsnr,
	'palArrayAuftrag'=>$palArray,
	'palArrayCreate'=>$palArrayCreate,
	'palArrayIntersect'=>$palIntersect,
    );
    
    echo json_encode($returnArray);
