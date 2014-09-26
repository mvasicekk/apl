<?
session_start();
require("../libs/Smarty.class.php");
require_once '../db.php';
$smarty = new Smarty;

// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
if (isset($_SESSION['user']) && isset($_SESSION['level'])) {
    $smarty->assign("user", $_SESSION['user']);
    $smarty->assign("level", $_SESSION['level']);
    $smarty->assign("prihlasen", 1);
} else {
    header("Location: ../index.php");
}
// kontrola registrace podle tabulky s opravnenima
//nacteni roli podle prihlaseni
require_once '../assignsecurity.php';

// vytvorit rozsah od prvniho do posledniho minuleho mesice

$akt_den = date("d");
$akt_mesic = date("m");
$akt_rok = date("Y");

// posledni datum minuleho mesice ziskam jako den 0 aktualniho mesice

$lastday = mktime(0, 0, 0, $akt_mesic, 0, $akt_rok);
$firstday = mktime(0, 0, 0, date('m', $lastday), 1, $akt_rok);

$min_mesic_od = date('d.m.Y', $firstday);
$min_mesic_do = date('d.m.Y', $lastday);

$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $akt_mesic, $akt_rok);
$tagbis = $pocetDnuVMesici;

$prvniDenAktualnihoRoku = date('d.m.Y', mktime(1, 1, 1, 1, 1, $akt_rok));
$prvniDenAktualnihoRokuDB = date('Y-m-d', mktime(1, 1, 1, 1, 1, $akt_rok));
$prvniDenAktualnihoMesice = date('d.m.Y', mktime(1, 1, 1, $akt_mesic, 1, $akt_rok));
$dnes = date('d.m.Y');

$predchozi_den = date('d.m.Y', mktime(0, 0, 0, $akt_mesic, $akt_den - 1, $akt_rok));

// seznam vsech statistickych cinnosti
$sql = "select dstat.`Stat_Nr` from dstat where Stat_Nr like 'S%' order by `Stat_Nr`";
$result = mysql_query($sql);
$StatSeznam = array();
array_push($StatSeznam, '*');
while ($row = mysql_fetch_assoc($result)) {
    array_push($StatSeznam, $row['Stat_Nr']);
}


$apl = AplDB::getInstance();

// seznam vsech skoleni 
$schulungenA = array();
array_push($schulungenA, '*');
$sA = $apl->getSchulungenArray();
if ($sA !== NULL) {
    foreach ($sA as $s)
	array_push($schulungenA, $s['beschreibung']);
}
$smarty->assign("schulungen", join(',', $schulungenA));



// seznam vsech typu kvalifikaci
$qtypenA = array();
array_push($qtypenA, '*');
$qTA = $apl->getQualifikationsTypenArray();
if ($qTA !== NULL) {
    foreach ($qTA as $typ)
	array_push($qtypenA, $typ['typ']);
}
$smarty->assign("qtypen", join(',', $qtypenA));

$qtypenA = array();
array_push($qtypenA, '*');
$qTA = $apl->getQualifikationsTypenArrayS171();
if ($qTA !== NULL) {
    foreach ($qTA as $typ)
	array_push($qtypenA, $typ['typ']);
}
$smarty->assign("qtypenS171", join(',', $qtypenA));


// seznam vsech OES
$oes = array();
array_push($oes, '*');
$oesA = $apl->getOEInfoArray();
if ($oesA !== NULL) {
    foreach ($oesA as $oe)
	array_push($oes, $oe['tat']);
}
$smarty->assign("oes", join(',', $oes));

// seznam vsech OG
//$ogs = array();
//array_push($ogs,'*');
//$ogsA = $apl->getOEInfoArray();
//if($oesA!==NULL){
//    foreach ($oesA as $oe) array_push ($oes, $oe['tat']);
//}
//$smarty->assign("oes",join(',',$oes));
// geeignet
$geignet = array();
array_push($geignet, '*');
$geignetA = $apl->getGeeignetArray();
if ($geignetA !== NULL) {
    foreach ($geignetA as $g)
	array_push($geignet, $g['text_kurz']);
}
$smarty->assign("geeignet", join(',', $geignet));


// nowpondeli, nowsobota pro D105
// zjistim aktualni cislo dne 0 nedele, 6 - sobota
$weekday = date('w');
if ($weekday == 0)
    $weekday = 7;
$pocetDnuDoPondeli = $weekday - 1;
$pocetDnuDoSobory = 6 - $weekday;
$pondelistamp = time() - $pocetDnuDoPondeli * 24 * 60 * 60;
$sobotastamp = time() + $pocetDnuDoSobory * 24 * 60 * 60;
$nowpondeli = date('d.m.Y', $pondelistamp);
$nowsobota = date('d.m.Y', $sobotastamp);
$predTricetiDny = time() - 30 * 24 * 60 * 60;

$smarty->assign('nowpondeli', $nowpondeli);
$smarty->assign('nowsobota', $nowsobota);

$statpolozky = join(',', $StatSeznam);

$smarty->assign("statpolozky", $statpolozky);
$smarty->assign("min_mesic_od", $min_mesic_od);
$smarty->assign("min_mesic_do", $min_mesic_do);

$smarty->assign("predchozi_den", $predchozi_den);
$smarty->assign("predtricetidny", date('d.m.Y', $predTricetiDny));
$smarty->assign("now", date("d.m.Y"));
$smarty->assign("nowtime", date("Y-m-d H:i:s"));
$smarty->assign("nowZeit", date("H:i"));
$smarty->assign("dnes", $dnes);
$smarty->assign("prvnidenroku", $prvniDenAktualnihoRoku);
$smarty->assign("prvnidenrokuDB", $prvniDenAktualnihoRokuDB);
$smarty->assign("prvnidenmesice", $prvniDenAktualnihoMesice);
$smarty->assign("aktualniMesic", $akt_mesic);
$smarty->assign("aktualniRok", $akt_rok);
$smarty->assign('tagvon', 1);
$smarty->assign('tagbis', $tagbis);
$smarty->assign('user', $_SESSION['user']);

// security

//$elementsIdArray = array(
//    "D550_sec",
//);

$elementsIdArray = $apl->getResourcesForFormId('berichte');

$puser = $_SESSION['user'];
foreach ($elementsIdArray as $elementId) {
    $display_sec[$elementId] = $apl->getDisplaySec('berichte', $elementId, $puser) ? 'inline-block' : 'none';
}

$smarty->assign("display_sec", $display_sec);
//$smarty->display('reports.tpl');
?>
