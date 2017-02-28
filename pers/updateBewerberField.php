<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$value = $o->value;
$field = $o->field;
$valid = FALSE;
$persnr = intval($o->persnr);

$a = AplDB::getInstance();
$u = $_SESSION['user'];


if($field=='bewerbe_datum'){
    $value1 = date('Y-m-d',strtotime($value));
    if(strtotime($value)!==FALSE){
	$valid = TRUE;
    }
}

if($field=='eintritt_datum'){
    $value1 = $value!==NULL?date('Y-m-d',strtotime($value)):NULL;
    if(strtotime($value)!==FALSE || $value==NULL){
	$valid = TRUE;
    }
}

if($field=='bewertung1'||$field=='bewertung2'||$field=='bewertung3'){
    $value1 = intval($value);
    if($value1>0){
	$valid = TRUE;
    }
}

if($field=='oe_voraussichtlich'){
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    $valid = TRUE;
}

if($field=='bemerkung_sonst'||$field=='bemerkung_faehigkeiten'){
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    $valid = TRUE;
}

if($field=='status_fur_aby_id'){
    $value1 = intval($value);
    if($value1>0){
	$valid = TRUE;
    }
}

if($field=='staats_angehoerigkeit_id'){
    $value1 = intval($value);
    if($value1>0){
	$valid = TRUE;
    }
}

if($field=='staats_gruppe_id'){
    $value1 = intval($value);
    if($value1>0){
	$valid = TRUE;
    }
}


if($field=='arbamt_evidenz'){
    $value1 = intval($value);
    $valid = TRUE;
}

if($field=='exekution'){
    $value1 = intval($value);
    $valid = TRUE;
}

if($field=='vermindertfah'){
    $value1 = intval($value);
    $valid = TRUE;
}

if($field=='infoVomArray'){
    $value1 = count($value)==0?NULL:join(',', $value);
    $valid = TRUE;
}

if($field=='faehigkeitenArray'){
    $value1 = count($value)==0?NULL:join(',', $value);
    $valid = TRUE;
}

if(($valid===TRUE) && ($persnr>0)){
    $ar = $a->updateBewerberField($persnr,$field,$value1);
}

$returnArray = array(
    'ar'=>$ar,
    'value'=>$value,
    'valueDB'=>$value1,
    'field'=>$field,
    'u' => $u,
    'sql' => $sql,
    'persnr'=>$persnr
);

echo json_encode($returnArray);
