<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

$apl = AplDB::getInstance();


$id = $_POST['id'];
$im = intval(trim($_POST['im']));
$ex = intval(trim($_POST['ex']));

$ab_aby_soll_date = $_POST['ab_aby_soll_date'];
$ab_aby_soll_time = $_POST['ab_aby_soll_time'];
$ab_aby_soll_datetime = $apl->datetimeOrNull($ab_aby_soll_date,$ab_aby_soll_time);


$ab_aby_ist_date = $_POST['ab_aby_ist_date'];
$ab_aby_ist_time = $_POST['ab_aby_ist_time'];
$ab_aby_ist_datetime = $apl->datetimeOrNull($ab_aby_ist_date,$ab_aby_ist_time);

$proforma = trim($_POST['proforma']);
$spediteur_id = intval(trim($_POST['spediteur_id']));
$fahrername = trim($_POST['fahrername']);
$lkw_kz = trim($_POST['lkw_kz']);
$an_kunde_ort = trim($_POST['an_kunde_ort']);

$an_kunde_soll_date = $_POST['an_kunde_soll_date'];
$an_kunde_soll_time = $_POST['an_kunde_soll_time'];
$an_kunde_soll_datetime = $apl->datetimeOrNull($an_kunde_soll_date,$an_kunde_soll_time);

$an_kunde_ist_date = $_POST['an_kunde_ist_date'];
$an_kunde_ist_time = $_POST['an_kunde_ist_time'];
$an_kunde_ist_datetime = $apl->datetimeOrNull($an_kunde_ist_date,$an_kunde_ist_time);

$an_aby_soll_date = $_POST['an_aby_soll_date'];
$an_aby_soll_time = $_POST['an_aby_soll_time'];
$an_aby_soll_datetime = $apl->datetimeOrNull($an_aby_soll_date,$an_aby_soll_time);

$an_aby_ist_date = $_POST['an_aby_ist_date'];
$an_aby_ist_time = $_POST['an_aby_ist_time'];
$an_aby_ist_datetime = $apl->datetimeOrNull($an_aby_ist_date,$an_aby_ist_time);

$an_aby_nutzlast = floatval(trim($_POST['an_aby_nutzlast']));
$preis = floatval(trim($_POST['preis']));
$rabatt = floatval(trim($_POST['rabatt']));
$betrag = floatval(trim($_POST['betrag']));
$rechnung = intval(trim($_POST['rechnung']));
$bemerkung = trim($_POST['bemerkung']);

$bInsert = TRUE;
$ar=0;
// nejaka sanace
// pokud nevyplni im ani ex nema smysl ukladat
if(($im==0) && ($ex==0)) $bInsert=FALSE;

if($bInsert===TRUE)
$ar = $apl->insertDRundlauf(
	$im,
	$ex,
	$ab_aby_ist_datetime,
	$ab_aby_soll_datetime,
	$an_aby_ist_datetime,
	$an_aby_soll_datetime,
	$an_kunde_ist_datetime,
	$an_kunde_soll_datetime,
	$proforma,
	$spediteur_id,
	$fahrername,
	$lkw_kz,
	$an_kunde_ort,
	$an_aby_nutzlast,
	$preis,
	$rabatt,
	$betrag,
	$rechnung,
	$bemerkung
	);

$returnArray = array(
    'id' => $id,
    'im' => $im,
    'ex' => $ex,
    'ab_aby_soll_datetime' => $ab_aby_soll_datetime,
    'ab_aby_ist_datetime' => $ab_aby_ist_datetime,
    'an_kunde_soll_datetime' => $an_kunde_soll_datetime,
    'an_kunde_ist_datetime' => $an_kunde_ist_datetime,
    'an_aby_soll_datetime' => $an_aby_soll_datetime,
    'an_aby_ist_datetime' => $an_aby_ist_datetime,
    'proforma' => $proforma,
    'spediteur_id' => $spediteur_id,
    'fahrername' => $fahrername,
    'lkw_kz' => $lkw_kz,
    'an_kunde_ort' => $an_kunde_ort,
    'an_aby_nutzlast' => $an_aby_nutzlast,
    'preis' => $preis,
    'rabatt' => $rabatt,
    'betrag' => $betrag,
    'rechnung' => $rechnung,
    'bemerkung' => $bemerkung,
    'ar'=>$ar,
);

echo json_encode($returnArray);

?>
