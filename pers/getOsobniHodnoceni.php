<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$von = $o->von;
$bis = $o->bis;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$jahrMonatArray = array();
$start = strtotime($von);
$end = strtotime($bis);
$step = 24 * 60 * 60;
for ($t = $start; $t <= $end; $t+=$step) {
    $jm = date('Y-m', $t);
    $jahrMonatArray[$jm] += 1;
}

$osobniHodnoceniArray = NULL;
$koeficientArray = NULL;

$regelOES = $a->getRegelOE($persnr);
$oeInfo = $a->getOEInfoForOES($regelOES);
$oe = $oeInfo['oe'];
$osobniFaktoryArray = $a->getHodnoceniOsobniFaktoryForOE($oe);
$hasOEHodnoceni = $osobniFaktoryArray!==NULL?TRUE:FALSE;

//$osobniHodnoceni = $a->getOsobniHodnoceniProPersNr($persnr,  date('Y-m-d',$start),date('Y-m-d',$end));
$osobniHodnoceniForm = $a->getOsobniHodnoceniProPersNrPersForm($persnr,  date('Y-m-d',$start),date('Y-m-d',$end));
$koeficientArray = $a->getOsobniHodnoceniKoeficientProPersNr($persnr,  date('Y-m-d',$start),date('Y-m-d',$end));

$oeSelectArray = NULL;

if($osobniHodnoceniForm!==NULL){
    $osobniHodnoceniArray = array();
    $osobniHodnoceniArray['jahrmonatArray'] = $jahrMonatArray;
    $osobniHodnoceniArray['hodnoceni'] = $osobniHodnoceniForm;
    $oeSelectArray = array();
    foreach ($osobniHodnoceniForm as $idFaktor=>$ohf){
	foreach ($ohf as $jm=>$r){
	    if($r['hodnoceni_osobni']['rowexists']==TRUE){
		$oeSelectArray[$jm][$r['hodnoceni_osobni']['oe']] +=1;
	    }
	}
    }
    //doplnim mesice ktere nemaji zadne OE
    foreach ($jahrMonatArray as $jm=>$p){
	if(array_key_exists($jm, $oeSelectArray)){
	    //prevedu klice na pole
	    $klice = array_keys($oeSelectArray[$jm]);
	    $oeSelectArray[$jm] = $klice;
	}
	else{
	    //pridam klic a hodnotu null
	    $oeSelectArray[$jm] = NULL;
	}
    }
}



$returnArray = array(
    'u' => $u,
    'von'=>$von,
    'bis'=>$bis,
    'osobniHodnoceni'=>$osobniHodnoceni,
    'osobniHodnoceniForm'=>$osobniHodnoceniForm,
    'osobniHodnoceniArray' => $osobniHodnoceniArray,
    'osobniHodnoceniKoeficientArray'=>$koeficientArray,
    'osobniFaktoryArray'=>$osobniFaktoryArray,
    'hasOEHodnoceni'=>$hasOEHodnoceni,
    'jahrmonatarray'=>$jahrMonatArray,
    'regeloe'=>$regelOES,
    'oeInfo'=>$oeInfo,
    'oe'=>$oe,
    'oeSelectArray'=>$oeSelectArray,
    'sql' => $sql,
);

echo json_encode($returnArray);
