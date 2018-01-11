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
//oeIdentifikatory
$sql="";
$sql.=" select distinct ident.oe";
$sql.=" from ident";
$sql.=" order by";
$sql.=" oe";
$oeIdentArray = $a->getQueryRows($sql);
$oeIdentSelected = $oeIdentArray[0]['oe'];

//identKundeArray
$kundeIdentArray = $a->getKundeIdentArrayForOE($oeIdentSelected);
$kundeIdentSelected = $kundeIdentArray[0]['kunde'];

//identifikatorArray
$identifikatorArray = $a->getIdentifikatorArrayForOEKunde($oeIdentSelected,$kundeIdentSelected);
$identifikatorSelected = $identifikatorArray[0]['iident'];
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

//status_fur_aby
$sql = "select * from dtextbuch where kategorie='status_fur_aby' order by text_kurz";
$status_fur_aby = $a->getQueryRows($sql);

//staats_gruppe
$sql = "select * from dtextbuch where kategorie='staats_gruppe' order by text_kurz";
$staats_gruppe = $a->getQueryRows($sql);

//info_vom
$sql = "select * from dtextbuch where kategorie='info_vom_n' order by text_kurz";
$info_vom = $a->getQueryRows($sql);

//fahigkeiten
$sql = " select ";
$sql.= "     dfaehigkeittyp.beschreibung as typ_beschreibung,";
$sql.= "     dfaehigkeiten.faeh_abkrz,";
$sql.= "     dfaehigkeiten.beschreibung as faeh_beschreibung,";
$sql.= "     dfaehigkeiten.id as faeh_id,";
$sql.= "     dfaehigkeiten.bew";
$sql.= " from dfaehigkeittyp";
$sql.= " join dfaehigkeiten on dfaehigkeiten.faehigkeit_typ=dfaehigkeittyp.id";
$sql.= " where bew=1";
$sql.= " order by";
$sql.= "     dfaehigkeittyp.stat_nr,";
$sql.= "     dfaehigkeiten.faeh_abkrz";

$bewFahigkeiten = $a->getQueryRows($sql);

//staaten
$sql = "select * from dstaaten where anzeigen=1 order by staat_abkrz";
$staaten = $a->getQueryRows($sql);

//oeschicht
$oesArray = $a->getOESForOEStatus('a');
//autoleistungAbgnrArray
$sql = "select `abg-nr` as abgnr,`Name` as abgnrname from `dtaetkz-abg` where `abg-nr` between 7000 and 7999 order by `abg-nr`";
$autoleistungAbgnrArray = $a->getQueryRows($sql);

//anwgruppen
$sql = "select anwgruppe,bezeichnung from anwesenheitgruppen order by anwgruppe";
$anwgruppenArray = $a->getQueryRows($sql);

//lohnabrechtyp
$sql = "select lohntyp,beschr_kurz from lohnabrechtyp order by lohntyp";
$lohnabrechtypArray = $a->getQueryRows($sql);

// vystup ----------------------------------------------------------------------

$returnArray = array(
    'staats_gruppen'=>$staats_gruppe,
    'staaten'=>$staaten,
    'bewFahigkeiten'=>$bewFahigkeiten,
    'infoVomArray'=>$info_vom,
    'status_fur_aby'=>$status_fur_aby,
    'dpersstatuses'=>$dpersstatuses,
    'fahigkeitenArray'=>$fahigkeitenArray,
    'fahtypenArray' => $fahtypenArray,
    'fahtypidSelected' => $fahtypidSelected,
    'inventarArray' => $inventarArray,
    'oeArray' => $oeArray,
    'oesArray' => $oesArray,
    'oeSelected' => $oeSelected,
    'oeIdentArray'=>$oeIdentArray,
    'oeIdentSelected'=>$oeIdentSelected,
    'kundeIdentArray'=>$kundeIdentArray,
    'kundeIdentSelected'=>$kundeIdentSelected,
    'identifikatorArray'=>$identifikatorArray,
    'identifikatorSelected'=>$identifikatorSelected,
    'autoleistungAbgnrArray'=>$autoleistungAbgnrArray,
    'anwgruppenArray'=>$anwgruppenArray,
    'lohnabrechtypArray'=>$lohnabrechtypArray,
    'u' => $u
);

echo json_encode($returnArray);
