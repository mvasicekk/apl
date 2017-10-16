<?
session_start();
require_once '../../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$text = $o->text;
$auftragsnr = $o->auftragsnr;

$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

if ($auftragsnr>0) {
    $ar = $a->updateDaufkopfField('rechnung_kopf_text', mysql_real_escape_string($text), $auftragsnr);
}

$returnArray = array(
    'u' => $u,
    'auftragsnr'=>$auftragsnr,
    'text'=>$text,
    'ar'=>$ar,
);

echo json_encode($returnArray);
