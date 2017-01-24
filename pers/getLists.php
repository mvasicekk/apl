<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

//dpersstatuses
$dpersstatuses = array();
$sql="select dpersstatus.status from dpersstatus order by status";
$rows = $a->getQueryRows($sql);
foreach ($rows as $r){
    array_push($dpersstatuses, $r['status']);
}
// oeArray
$sql = "";
$sql.=" select doe.oe,doe.beschreibung_cz from doe where stredisko_isp is not null order by doe.oe";
$oeArray = $a->getQueryRows($sql);
array_unshift($oeArray, array('oe'=>'*','beschreibung_cz'=>'vše'));
$oeSelected = '*';

//inventar
$sql = "";
$sql.=" select ";
$sql.="     inventartyp.typ inventartyp,";
$sql.="     inventartyp.popis as inventartyp_popis,";
$sql.="     inventar.*,";
$sql.="     mistnosti.mistnost,";
$sql.="     mistnosti.popis";
$sql.=" from";
$sql.="     inventar";
$sql.=" left join inventartyp on inventartyp.id=inventar.typinventare_id";
$sql.=" left join mistnosti on mistnosti.id=inventar.mistnost_id";
$sql.=" where";
$sql.="     inventar.popis like '%a%'";
$sql.=" order by";
$sql.="     inventartyp.typ,";
$sql.="     inventar.cislo";

$inventarArray = $a->getQueryRows($sql);

//fahigkeiten
$sql = " select dfaehigkeittyp.*";
$sql.= " from dfaehigkeittyp";
$sql.= " order by stat_nr";
$fahtypenArray = $a->getQueryRows($sql);
if($fahtypenArray!==NULL){
    $fahtypidSelected = $fahtypenArray[0]['id'];
}

$sql=" select dfaehigkeiten.*";
$sql.=" from dfaehigkeiten";
$sql.=" order by faeh_abkrz";
$fahigkeitenArray = $a->getQueryRows($sql);


$returnArray = array(
    'dpersstatuses'=>$dpersstatuses,
    'fahigkeitenArray'=>$fahigkeitenArray,
    'fahtypenArray' => $fahtypenArray,
    'fahtypidSelected' => $fahtypidSelected,
    'inventarArray' => $inventarArray,
    'oeArray' => $oeArray,
    'oeSelected' => $oeSelected,
    'u' => $u
);

echo json_encode($returnArray);
