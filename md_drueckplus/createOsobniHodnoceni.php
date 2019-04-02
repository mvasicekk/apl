<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$jm = $o->jm;
$oe = $o->oe;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$datum = $jm.'-01';


// TODO
//pokud vyberu jine oe pro dany mesic, smazu nezamcene radky s faktory, ktere neodpovidaji vybranemu OE
//pokud bude v danem mesici vice OE, v zahlase v select boxu nejak zobrazim tuto informaci (napr. vybrane OE +2 )
	
	
	
$regelOES = $a->getRegelOE($persnr);
$oeInfo = $a->getOEInfoForOES($regelOES);
$persOE = $oeInfo['oe'];
$insertArray = array();
if ($oe !== $persOE) {
    $osobniFaktoryArray = $a->getHodnoceniOsobniFaktoryForOE($oe);
    //postupne projdu a pokud pro datum a faktor tento osobni faktor jesne neni v tabulce, tak ho pridam
    foreach ($osobniFaktoryArray as $of) {
	$id_faktor = $of['id_osobni_faktor'];
	$sql = " select hodnoceni_osobni.id from hodnoceni_osobni";
	$sql .= " where";
	$sql .= " persnr='$persnr'";
	$sql .= " and datum='$datum'";
	$sql .= " and id_faktor='$id_faktor'";
	$rs = $a->getQueryRows($sql);
	if($rs===NULL){
	    //nemam takovou kombinaci, vytvorim radek
	    $insert = "insert into hodnoceni_osobni (persnr,datum,id_faktor,hodnoceni,castka,oe,last_edit)";
	    $insert.=" values('$persnr','$datum','$id_faktor','0','0','$oe','apl_not_regelOE')";
	    array_push($insertArray, $insert);
	    $a->insert($insert);
	}
    }
    //a nakonec smazu faktory ktere neodpovidaji vybranemu oe a nejsou zamcene
    $a->query("delete from hodnoceni_osobni where persnr='$persnr' and datum='$datum' and oe<>'$oe' and locked=0");
}


$returnArray = array(
    'u' => $u,
    'jm'=>$jm,
    'oe'=>$oe,
    'datum'=>$datum,
    'persOE'=>$persOE,
    'osobniFaktoryArray'=>$osobniFaktoryArray,
    'regeloe'=>$regelOES,
    'oeInfo'=>$oeInfo,
    'sql' => $sql,
    'insertArray'=>$insertArray
);

echo json_encode($returnArray);
