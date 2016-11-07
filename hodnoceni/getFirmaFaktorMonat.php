<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$von = $o->von;
$bis = $o->bis;

$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];


// rok-mesic -------------------------------------------------------------------
$jahrMonatArray = array();
$start = strtotime($von);
$end = strtotime($bis);
$step = 24 * 60 * 60;
for ($t = $start; $t <= $end; $t+=$step) {
    $jm = date('Y-m', $t);
    $jahrMonatArray[$jm] += 1;
}
$jmArray = array_keys($jahrMonatArray);

//firemni faktory --------------------------------------------------------------
$sql = "select * from hodnoceni_firemni_faktory order by `sort`";
$firemniFaktory = $a->getQueryRows($sql);

foreach ($firemniFaktory as $ff){
    $ffId = $ff['id'];
    foreach ($jmArray as $jm){
	$datum = $jm."-01";
	$sql = "select * from hodnoceni_firemni where id_faktor='$ffId' and datum='$datum'";
	$hr = $a->getQueryRows($sql);
	if($hr!==NULL){
	    $firmaFaktorMonat[$ffId][$jm]['hodnoceni'] = intval($hr[0]['hodnoceni']);
	    $firmaFaktorMonat[$ffId][$jm]['id'] = intval($hr[0]['id']);
	}
	else{
	    // musim v db vytvorit
	    $ins = "insert into hodnoceni_firemni (id_faktor,datum,hodnoceni) values('$ffId','$datum',0)";
	    $a->insert($ins);
	    $sql = "select * from hodnoceni_firemni where id_faktor='$ffId' and datum='$datum'";
	    $hr = $a->getQueryRows($sql);
	    $firmaFaktorMonat[$ffId][$jm]['hodnoceni'] = intval($hr[0]['hodnoceni']);
	    $firmaFaktorMonat[$ffId][$jm]['id'] = intval($hr[0]['id']);
	}
    }
}

$returnArray = array(
	'firmaFaktorMonat'=>$firmaFaktorMonat,
	'jmArray'=>$jmArray,
	'von'=>$von,
	'bis'=>$bis,
	'u'=>$u
    );
    
echo json_encode($returnArray);
