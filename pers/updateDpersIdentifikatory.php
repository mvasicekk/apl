<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$pi = $o->pi;
$field = $o->field;


$a = AplDB::getInstance();
$u = $_SESSION['user'];

$id = intval($pi->id);
$value = $pi->{$field};
$valid = FALSE;
$valueFromParam = $value;
// sanace hodnot

if($field=='vraceno'){
    if($vracenoTime=strtotime($value)){
	$value = date('Y-m-d',$vracenoTime);
    }
    else{
	$value = NULL;
    }
    $valid = TRUE;
}

if($field=='poznamka'){
    $value = trim($value);
    $valid = TRUE;
}

if($id>0 && $valid===TRUE){
    if($value===NULL){
	$sql = "update dpersident set `$field`=null where id='$id'";
    }
    else{
	$sql = "update dpersident set `$field`='$value' where id='$id'";
    }
    
    $ar = $a->query($sql);
}

$returnArray = array(
    'ar'=>$ar,
    'valueFromParam'=>$valueFromParam,
    'value'=>$value,
    'field'=>$field,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
