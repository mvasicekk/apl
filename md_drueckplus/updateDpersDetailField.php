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



//if($field=='geboren'){
//    $value1 = $value!==NULL?date('Y-m-d',strtotime($value)):NULL;
//    if(strtotime($value)!==FALSE || $value==NULL){
//	$valid = TRUE;
//    }
//}
//
//if($field=='Name'||$field=='Vorname'){
//    $value1 = strlen(trim($value))>0?trim($value):NULL;
//    if($value1!==NULL){
//	$valid = TRUE;
//    }
//}

if($field=='kom7'){
    $value1 = strlen(trim($value))>0?trim($value):NULL;
    $valid = TRUE;
}

if($field=='schuhegroesse'){
    $value1 = trim($value);
    $valid = TRUE;
}

if($field=='regeltrans'){
    $value1 = intval(trim($value));
    $valid = TRUE;
}

//if($field=='bewertung1'||$field=='bewertung2'||$field=='bewertung3'){
//    $value1 = intval($value);
//    if($value1>0){
//	$valid = TRUE;
//    }
//}
//
//if($field=='oe_voraussichtlich'){
//    $value1 = strlen(trim($value))>0?trim($value):NULL;
//    $valid = TRUE;
//}
//
//if($field=='bemerkung_sonst'||$field=='bemerkung_faehigkeiten'){
//    $value1 = strlen(trim($value))>0?trim($value):NULL;
//    $valid = TRUE;
//}
//
//if($field=='status_fur_aby_id'){
//    $value1 = intval($value);
//    if($value1>0){
//	$valid = TRUE;
//    }
//}


if(($valid===TRUE) && ($persnr>0)){
    $ar = $a->updateDpersDetailField($persnr,$field,$value1);
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
