<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$id = $_POST['id'];
$import = intval(trim($_POST['import']));
$impal = intval(trim($_POST['pal1']));

$isEx = FALSE;
$rmArray = $a->getRMArray($import,$impal);
// $rmArray upravim, najdu pozice s vice stejnymi abgnr
// pokud najdu radek s aussstk==0 a aussart==0, prictu dobre kusy k nasledujicimu radku 
// a puvodni radek odstranim
$rmArrayNew = array();
$abgnrOld = -1;
$gutAlt = 0;
if ($rmArray !== NULL) {
    foreach ($rmArray as $rm) {
	$abgnr = $rm['abgnr'];
	if ($abgnr == $abgnrOld) {
	    //predchozi operace je stejna jako aktualni, přičtu $gutAlt
	    $rm['gutstk']+=$gutAlt;
	    $gutAlt = 0;
	    $abgnrOld = -1;
	}
	$abgnrOld = $abgnr;
	if(($rm['aussstk']==0) && ($rm['aussart']==0)){
	    $gutAlt = $rm['gutstk'];
	}
	else{
	    $gutAlt = 0;
	}
	array_push($rmArrayNew, $rm);
    }
}

$rmArrayNew1 = array();
$abgnrOld = -1;
for($i=count($rmArrayNew)-1;$i>=0;$i--){
    $abgnr = $rmArrayNew[$i]['abgnr'];
    if($abgnr!=$abgnrOld){
	array_push($rmArrayNew1, $rmArrayNew[$i]);
	$abgnrOld=$abgnr;
    }
    else{
	$aussStk = $rmArrayNew[$i]['aussstk'];
	$aussArt = $rmArrayNew[$i]['aussart'];
	if($aussStk!=0 || $aussArt!=0){
	    array_push($rmArrayNew1, $rmArrayNew[$i]);
	}
    }
}

$rmArrayNew1 = array_reverse($rmArrayNew1);

$dauftrArray = $a->getDauftrRowsForImportPal($import, $impal);
$isEx = $a->istExportiert($import, $impal);

$retArray = array(
    'id'=>$id,
    'import'=>$import,
    'impal'=>$impal,
    'rows'=>$rmArrayNew1,
    'dauftrRows'=>$dauftrArray,
    'isEx'=>$isEx
);


echo json_encode($retArray);