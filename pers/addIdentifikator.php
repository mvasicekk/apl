<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$oe = $o->oe;
$kunde = $o->kunde;
$identifikator = $o->identifikator;
$vydano = strtotime($o->vydano);
if($vydano!==FALSE){
    $vydano = date('Y-m-d',$vydano);
}
$poznamka = trim($o->poznamka);
$persnr = $o->persnr;

$a = AplDB::getInstance();
$u = $_SESSION['user'];

$k = $o->k;



if($k!==NULL){
    //smazani existujuciho
    $idDel = intval($k->id);
    $sql = "delete from dpersident where id='$idDel'";
    $a->query($sql);
    $delRows = 1;
}
else{
// vlozeni noveho 
    if(strlen(trim($kunde))==0){
	$sql = "insert into dpersident (persnr,oe,vydano,poznamka,identifikator) values('$persnr','$oe','$vydano','$poznamka','$identifikator')";
    }
    else{
	$sql = "insert into dpersident (persnr,oe,kunde,vydano,poznamka,identifikator) values('$persnr','$oe','$kunde','$vydano','$poznamka','$identifikator')";
    }
    
    //nejake testy
    if(($vydano!==FALSE)){
	$insertId = $a->insert($sql);
    }
}

$returnArray = array(
    'oe' => $oe,
    'kunde' => $kunde,
    'identifikator' => $identifikator,
    'vydano' => $vydano,
    'poznamka' => $poznamka,
    'persnr' => $persnr,
    'idDel' => $idDel,
    'delRows' => $delRows,
    'insertId' => $insertId,
    'id' => $id,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
