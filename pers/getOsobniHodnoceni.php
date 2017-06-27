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

$osobniHodnoceni = $a->getOsobniHodnoceniProPersNr($persnr,  date('Y-m-d',$start),date('Y-m-d',$end));
$koeficientArray = $a->getOsobniHodnoceniKoeficientProPersNr($persnr,  date('Y-m-d',$start),date('Y-m-d',$end));

if($osobniHodnoceni!==NULL){
    $osobniHodnoceniArray = array();
    $osobniHodnoceniArray['jahrmonatArray'] = $jahrMonatArray;
    $osobniHodnoceniArray['hodnoceni'] = $osobniHodnoceni;
}



$returnArray = array(
    'u' => $u,
    'von'=>$von,
    'bis'=>$bis,
    'osobniHodnoceniArray' => $osobniHodnoceniArray,
    'osobniHodnoceniKoeficientArray'=>$koeficientArray,
    'sql' => $sql,
);

echo json_encode($returnArray);
