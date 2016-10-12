<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$persnr = $o->persnr;
$persinfo = NULL;

$a = AplDB::getInstance();
$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

//oe
$sql="select dtattypen.tat,dtattypen.tatBezeichnung from dtattypen where oestatus='a' order by dtattypen.tat";
$oeArray = $a->getQueryRows($sql);

// sklady z premiera
$sql=" select SEZ_SKL.CISLO as cislo,SEZ_SKL.POPIS as popis,SEZ_SKL.POZNAMKA as poznamka from SEZ_SKL order by CISLO";
$res = $sqlDB->getResult($sql);
if($res!==NULL){
    $skladyArray = array();
    foreach ($res as $r){
	$value = trim($r['popis']);
	$txt = iconv('windows-1250', 'UTF-8', $value);
	$poznamka = iconv('windows-1250', 'UTF-8', trim($r['poznamka']));
	array_push($skladyArray, array('cislo'=>$r['cislo'],'popis'=> $txt,'poznamka'=> $poznamka ));
    }
}

// sklady pro prihlaseneho uzivatele
$sql = "select dbenutzer_sklady.sklad,dbenutzer_sklady.prio from dbenutzer_sklady where `name`='$u' order by sklad,prio";
$persSklady = $a->getQueryRows($sql);

$filteredSkladyArray = array();

//prefiltrovat sklady podle skladu povolenych pro prihlaseneho uzivatele
if ($persSklady !== NULL) {
    foreach ($persSklady as $ps) {
	foreach ($skladyArray as $sa) {
	    if ($ps['sklad'] == $sa['cislo']) {
		// nasel jsem, pridam do pole
		array_push($filteredSkladyArray, $sa);
		break;
	    }
	}
    }
}


$returnArray = array(
	'persSklady'=>$persSklady,
	'skladyArray'=>$filteredSkladyArray,
	'oeArray'=>$oeArray,
	'sql'=>$sql,
	'u'=>$u
    );
    
echo json_encode($returnArray);
