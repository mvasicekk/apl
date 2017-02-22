<?
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$persnr = $o->persnr;

$a = AplDB::getInstance();


$sql.=" select id,persnr,oe,user,bewertung,poznamka,DATE_FORMAT(gilt_ab,'%d.%m.%Y') as gilt_ab,DATE_FORMAT(stamp,'%d.%m.%Y') as stamp from dpersoekvalifikace";
$sql.=" where";
$sql.="     persnr='$persnr'";
$sql.=" order by oe";
	

$persKvalifikaceArray = $a->getQueryRows($sql);


$returnArray = array(
    'persnr'=>$persnr,
    'persKvalifikaceArray' => $persKvalifikaceArray,
    'sql' => $sql
);

echo json_encode($returnArray);

