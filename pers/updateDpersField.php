<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$makeedit = $o->makeedit;
$value = $o->value;
$timezoneOffsetMinutes = $o->timezoneOffset;
$field = $o->field;
$valid = FALSE;
$persnr = intval($o->persnr);

$a = AplDB::getInstance();
$u = $_SESSION['user'];

date_default_timezone_set('Europe/Prague');

if($field=='bewerbe_datum'){
    $value1 = date('Y-m-d',strtotime($value));
    if(strtotime($value)!==FALSE){
	$valid = TRUE;
    }
}

if($field=='adaptace_bis'){
    $value1 = $value!==NULL?date('Y-m-d',strtotime($value)):NULL;
    if(strtotime($value)!==FALSE || $value==NULL){
	$valid = TRUE;
    }
}

if($field=='geboren'){
    $value1 = $value!==NULL?date('Y-m-d',strtotime($value)-$timezoneOffsetMinutes*60):NULL;
    if(strtotime($value)!==FALSE || $value==NULL){
	$valid = TRUE;
    }
}

if($field=='Name'||$field=='Vorname'){
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    if($value1!==NULL){
	$valid = TRUE;
    }
}

if($field=='regeloe'||$field=='alteroe'||$field=='oe3'){
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    if($value1!==NULL){
	$valid = TRUE;
    }
}

if($field=='email'){
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    $valid = TRUE;
}

if($field=='auto_leistung_abgnr'){
    $value1 = $value->abgnr;
    $valid = TRUE;
}

if($field=='anwgruppe'){
    $value1 = $value->anwgruppe;
    $valid = TRUE;
}

if($field=='lohnabrechtyp'){
    $value1 = $value->lohntyp;
    $valid = TRUE;
}

if($field=='dpersstatus'){
    $value1 = trim($value);
    $valid = TRUE;
}

if($field=='lohnfaktor'){
    $value1 = floatval(strtr(trim($value), ',', '.'));
    if($value1>=0 && $value<1000)
    $valid = TRUE;
}

if($field=='regelarbzeit'){
    $value1 = floatval(strtr(trim($value), ',', '.'));
    if($value1>=0 && $value<1000)
    $valid = TRUE;
}

if($field=='anwvzkd_faktor'){
    $value1 = floatval(strtr(trim($value), ',', '.'));
    if($value1>=0 && $value<1000)
    $valid = TRUE;
}

if($field=='specnr'){
    $value1 = intval(trim($value));
    if($value1>=0 && $value<999999)
    $valid = TRUE;
}

if($field=='leistfaktor'){
    $value1 = floatval(strtr(trim($value), ',', '.'));
    if($value1>=0 && $value<1000)
    $valid = TRUE;
}

if(
	$field=='premie_za_vykon'
	||$field=='qpremie_akkord'
	||$field=='premie_za_3_mesice'
	||$field=='a_praemie'
	||$field=='bewertung'
	||$field=='qpremie_zeit'
	||$field=='MAStunden'
	||$field=='a_praemie_st'
	||$field=='auto_leistung'
	||$field=='kor'
	||$field=='einarb_zuschlag')
    {
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    $valid = TRUE;
}


if(($valid===TRUE) && ($persnr>0) && ($makeedit===TRUE)){
    $ar = $a->updateDpersField($persnr,$field,$value1,$u);
}

$returnArray = array(
    'ar'=>$ar,
    'value'=>$value,
    'valueDB'=>$value1,
    'field'=>$field,
    'u' => $u,
    'sql' => $sql,
    'persnr'=>$persnr,
    'default_timezone'=>date_default_timezone_get(),
);

// HID Card Info

echo json_encode($returnArray);
