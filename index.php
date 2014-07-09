<?
session_start();
require("./libs/Smarty.class.php");
$smarty = new Smarty;
require_once './db.php';

$apl = AplDB::getInstance();

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
	$display_sec[$elementId] = $apl->getDisplaySec('start', $elementId, $puser) ? 'inline-block' : 'none';
    }
}
$smarty->assign("display_sec", $display_sec);


// spocitam hodnoty pro tabulku s aktualnima vykonama pro tento mesic

$leistungTableArray = $apl->getLeistungTable();
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

//zjistit seznam roli pro uzivatele
require_once './assignsecurity.php';

$smarty->display('index.tpl');
?>

