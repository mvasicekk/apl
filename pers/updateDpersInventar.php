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

// sanace hodnot
if($field=='pozn'){
    $value = trim($value);
}

if($id>0){
    $sql = "update dpersinventar set `$field`='$value' where id='$id'";
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
