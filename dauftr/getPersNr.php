<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$id = $_POST['id'];
$persnr = intval(trim($_POST['persnr']));


$rmArray = $a->getPersonalArray($persnr);

$retArray = array(
    'id'=>$id,
    'persnr'=>$persnr,
    'rows'=>$rmArray
);


echo json_encode($retArray);