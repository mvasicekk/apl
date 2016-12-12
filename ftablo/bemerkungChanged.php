<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$t = trim($o->t);
$rid = intval($o->rid);
$poznamka = trim($o->value);

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$sql = "select bemerkungen.id from bemerkungen where (`table`='$t' and rowid='$rid')";
$rs = $a->getQueryRows($sql);

if($rs!==NULL){
    // update
    $sql = "update bemerkungen set bemerkung='$poznamka' where (`table`='$t' and rowid='$rid')";
    $ar = $a->query($sql);
}
else{
    //insert
    $sql = "insert into bemerkungen (`table`,rowid,bemerkung,`user`) values('$t','$rid','$poznamka','$u')";
    $iid = $a->insert($sql);
}

$returnArray = array(
    'iid'=>$iid,
    't'=>$t,
    'rid'=>$rid,
    'poznamka'=>$poznamka,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
