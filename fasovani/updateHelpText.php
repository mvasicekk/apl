<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$id = $o->id;
$text = $o->helptext;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$sql = "update resources set help_text='$text' where id='$id'";
$ar = $a->query($sql);
$returnArray = array(
    'ar'=>$ar,
    'id'=>$id,
    'text'=>$text,
    'sql'=>$sql,
    'u'=>$u
);

echo json_encode($returnArray);