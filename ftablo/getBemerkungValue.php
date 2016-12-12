<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$t = trim($o->t);
$rid = intval($o->rid);
$poznamka = "";

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$sql = "select bemerkungen.bemerkung from bemerkungen where (`table`='$t' and rowid='$rid')";
$rs = $a->getQueryRows($sql);

if($rs!==NULL){
    // update
    $poznamka = $rs[0]['bemerkung'];
}

$returnArray = array(
    'poznamka'=>$poznamka,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
