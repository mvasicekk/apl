<?
session_start();
require("./libs/Smarty.class.php");
$smarty = new Smarty;
require_once './db.php';

$apl = AplDB::getInstance();
//fferfer
$prihlasen = 0;
// otestuju hodnoty POST promennych user a password
// pokud budou neco obsahovat, zkusim uzivatele prihlasit
if (isset($_POST['username']) && isset($_POST['password'])) {
    // podivam se do DB, zda mam odpovidajiciho uzivatele
    // pokud ano, tak ho prihlasim a nastavim SESSION promenne
    $puser = $_POST['username'];
    $ppassword = $_POST['password'];
    $ip = $_SERVER["REMOTE_ADDR"];

    $access = $apl->grantAccess($puser, $ppassword, $ip);

    if ($access['loginok'] == 1) {
	$_SESSION['user'] = $access['name'];
	$_SESSION['level'] = $access['level'];
	$smarty->assign("prihlasen", 1);
	$prihlasen = 1;
	$smarty->assign("user", $access['name']);
	$smarty->assign("level", $access['level']);
    } else {
	unset($_SESSION['user']);
	unset($_SESSION['level']);
	session_destroy();
	$smarty->assign("prihlasen", 0);
    }
    $apl->insertAccessLog($_POST['username'], $_POST[password], $prihlasen, $apl->get_pc_ip());
}

if (isset($_GET['akce']) && $_GET['akce'] == "logout") {
    unset($_SESSION['user']);
    unset($_SESSION['level']);
    unset($_POST['username']);
    unset($_POST['password']);
    session_destroy();
}

//	require_once './security.php';
// pokud mam nastavene session promennes uzivatelem , nastavim priznak prihlaseni
if (isset($_SESSION['user']) && isset($_SESSION['level'])) {
    $smarty->assign("user", $_SESSION['user']);
    $smarty->assign("level", $_SESSION['level']);
    $smarty->assign("prihlasen", 1);
}

//security
$elementsIdArray = $apl->getResourcesForFormId('start');
$display_sec = array();
$puser = $_SESSION['user'];
if ($elementsIdArray !== NULL) {
    foreach ($elementsIdArray as $elementId) {
	$show = 'inline-block';
	//vyjimka pro branydiv
	if($elementId=='branydiv'){
	    $show = 'block';
	}
	$display_sec[$elementId] = $apl->getDisplaySec('start', $elementId, $puser) ? $show : 'none';
    }
}
$smarty->assign("display_sec", $display_sec);


// spocitam hodnoty pro tabulku s aktualnima vykonama pro tento mesic
$zielPG1 = 62000;
$zielPG4 = 17000;
$zielSum = $zielPG1 + $zielPG4;

$leistungTableArray = $apl->getLeistungTable();

//AplDB::varDump($leistungTableArray['pole']);

foreach ($leistungTableArray['pole'] as $i=>$a){
    $leistungTableArray['pole'][$i]['ziel_pg1'] = $leistungTableArray['pole'][$i]['pg1']/$zielPG1 * 100;
    $leistungTableArray['pole'][$i]['ziel_pg4'] = $leistungTableArray['pole'][$i]['pg4']/$zielPG4 * 100;
    $leistungTableArray['pole'][$i]['ziel_sum'] = $leistungTableArray['pole'][$i]['celkem']/$zielSum * 100;
}

//AplDB::varDump($leistungTableArray['pole']);

$smarty->assign("datum", $leistungTableArray['datum']);
$smarty->assign("pole", $leistungTableArray['pole']);
$smarty->assign("sum_pg1", $leistungTableArray['sum_pg1']);
$smarty->assign("sum_pg3", $leistungTableArray['sum_pg3']);
$smarty->assign("sum_pg4", $leistungTableArray['sum_pg4']);
$smarty->assign("sum_pg9", $leistungTableArray['sum_pg9']);
$smarty->assign("sum_celkem", $leistungTableArray['sum_celkem']);


// zjistim seznam dnesnich importu
$dnesniDatumDB = date('Y-m-d');
$sql = "select daufkopf.kunde,daufkopf.auftragsnr,DATE_FORMAT(daufkopf.`Aufdat`,'%d.%m.%Y') as aufdat,DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(daufkopf.fertig,'%d.%m.%Y') as fertig from daufkopf where (daufkopf.`Aufdat`='$dnesniDatumDB') order by kunde,auftragsnr";
$res = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_array($res)) {
    $zakazkyIM[$row['auftragsnr']] = $row;
}
$smarty->assign("zakazkyIM", $zakazkyIM);

// zjistim seznam dnesnich exportu
$dnesniDatumDB = date('Y-m-d');
$sql = "select daufkopf.kunde,daufkopf.auftragsnr,DATE_FORMAT(daufkopf.`Aufdat`,'%d.%m.%Y') as aufdat,DATE_FORMAT(daufkopf.ausliefer_datum,'%d.%m.%Y') as ausliefer_datum,DATE_FORMAT(daufkopf.fertig,'%d.%m.%Y') as fertig from daufkopf where (daufkopf.ausliefer_datum='$dnesniDatumDB' or DATE_FORMAT(daufkopf.ex_datum_soll,'%Y-%m-%d')='$dnesniDatumDB' ) order by kunde,auftragsnr";
$res = mysql_query($sql) or die(mysql_error());
while ($row = mysql_fetch_array($res)) {
    $zakazkyEX[$row['auftragsnr']] = $row;
}
$smarty->assign("zakazkyEX", $zakazkyEX);

//seznam souboru pro tv
//Aby 18 Mitarbeiter -\05 Projekte - Projekty\Projekt TV Abydos\Aktual video\
$cesta = $apl->getGdatPath()."Aby 18 Mitarbeiter -/05 Projekte - Projekty/Projekt TV Abydos/Aktual video";
$files = $apl->getFilesForPath($cesta);
$smarty->assign("tvFiles", $files);

//zjistit seznam roli pro uzivatele
require_once './assignsecurity.php';

//unset($_POST);
$URI = $_SERVER['REQUEST_URI'];
//unset($_POST);
if($_POST){
    header("Location: $URI");
}

$smarty->display('indexBS.tpl');
//$smarty->display('index.tpl');
?>

