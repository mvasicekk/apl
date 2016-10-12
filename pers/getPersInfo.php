<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$persinfo = NULL;
$suchen = strtolower(trim($o->osoba));
$jenma = $o->jenma;
$a = AplDB::getInstance();

$u = $_SESSION['user'];

$sql="select  DATE_FORMAT(dpers.eintritt,'%d.%m.%Y') as eintritt,dpers.persnr,`name`,vorname,regeloe,dpersstatus from dpers where (";
$sql.=" ((`PersNr` like'".$suchen."%') or (LOWER(`name`) like '%".$suchen."%')  or (LOWER(`Vorname`) like '%".$suchen."%'))";
if($jenma===TRUE){
    $sql.=" and (dpersstatus='MA')";
}
$sql.=" )";
$sql.=" order by persnr";
if(strlen($suchen)>1){
    $osoby = $a->getQueryRows($sql);
}


$returnArray = array(
	'u'=>$u,
	'osoby'=>$osoby,
	'suchen'=>$suchen,
	'sql'=>$sql,
	'jenma'=>$jenma,
    );
    
echo json_encode($returnArray);
