<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$id = $_POST['id'];
$import = intval(trim($_POST['import']));
$impal = intval(trim($_POST['pal1']));


$rmArray = $a->getRMArray($import,$impal);

$retArray = array(
    'id'=>$id,
    'import'=>$import,
    'impal'=>$impal,
    'rows'=>$rmArray
);


echo json_encode($retArray);