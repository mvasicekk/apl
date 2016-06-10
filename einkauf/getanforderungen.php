<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$u = $_GET['user'];
$sl = $_GET['showalllist'];
$mitFertigenB = $_GET['mitfertigen']=='mit'?TRUE:FALSE;

$bAll = $sl=='block'?TRUE:FALSE;

$bAll = TRUE;

$ar = $a->getEinkaufAnforderungenArray($u,$bAll,$mitFertigenB);

$retArray = array(
    'ar'=>$ar,
    'u'=>$u,
    'bAll'=>$bAll
);
echo json_encode($ar);