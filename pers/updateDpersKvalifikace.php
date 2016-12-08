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
if($field=='bewertung'){
    $value = intval(trim($value));
    if($value>=6 && $value<=9){
	$valid = TRUE;
    }
}

if($field=='poznamka'){
    $value = trim($value);
    $valid = TRUE;
}

if($id>0 && $valid===TRUE){
    if($value===NULL){
	$sql = "update dpersinventar set `$field`=NULL where id='$id'";
    }
    else{
	$sql = "update dpersoekvalifikace set `$field`='$value' where id='$id'";
    }
    
    $ar = $a->query($sql);
}
$returnArray = array(
    'ar'=>$ar,
    'value'=>$value,
    'field'=>$field,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
