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

// sanace hodnot
if($field=='pozn'){
    $value = trim($value);
    $valid = TRUE;
}

if($field=='vydej_datum'||$field=='vraceno_datum'){
    $field1 = $field.'1';
    $value = $value = $pi->{$field1};
    $t = strtotime($value);
    if($t){
	$value = date('Y-m-d',$t);
    }
    else{
	$value = NULL;
    }
    $valid = TRUE;
}

if($id>0 && $valid===TRUE){
    if($value===NULL){
	$sql = "update dpersinventar set `$field`=NULL where id='$id'";
    }
    else{
	$sql = "update dpersinventar set `$field`='$value' where id='$id'";
    }
    
    $ar = $a->query($sql);
}
$returnArray = array(
    'ar'=>$ar,
    'value'=>$value,
    'pi'=>$pi,
    'field'=>$field,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
