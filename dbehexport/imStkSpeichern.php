<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$auftragsnr = $o->params->r->auftragsnr;

$a->ImStkSpeichern($auftragsnr);


$returnArray = array(
	'auftragsnr'=>$auftragsnr,
    );
    
echo json_encode($returnArray);
