<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$keyword = $o->params->a;
$auftrags = array();

$a = AplDB::getInstance();
if(strlen($keyword)>2)
{
    $sql = "select auftragsnr,kunde,bestellnr,DATE_FORMAT(Aufdat,'%Y-%m-%d') as Aufdat,DATE_FORMAT(fertig,'%Y-%m-%d') as fertig,DATE_FORMAT(ausliefer_datum,'%Y-%m-%d') as ausliefer_datum from daufkopf where ((auftragsnr like '".$keyword."%')) order by kunde, auftragsnr limit 100";
    $auftrags = $a->getQueryRows($sql);
}
			
$returnArray = array(
	'auftrags'=>$auftrags,
	'auftrag'=>$auftrag,
    );
    
echo json_encode($returnArray);
