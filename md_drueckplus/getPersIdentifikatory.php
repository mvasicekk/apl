<?
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$persnr = $o->persnr;

$a = AplDB::getInstance();


$sql.=" select ";
$sql.="     id,";
$sql.="     persnr,";
$sql.="     oe,";
$sql.="     kunde,";
$sql.="     identifikator,";
$sql.="     IF(vydano<>'0000-00-00',DATE_FORMAT(vydano,'%d.%m.%Y'),null) as vydano,";
//$sql.="     DATE_FORMAT(vraceno,'%d.%m.%Y') as vraceno,";
$sql.="     vraceno,";
$sql.="     poznamka";
$sql.=" from dpersident";
$sql.=" where";
$sql.="     persnr='$persnr'";
$sql.=" order by";
$sql.="     vydano desc,";
$sql.="     oe,";
$sql.="     kunde,";
$sql.="     identifikator";
	

$persIdentifikatoryArray = $a->getQueryRows($sql);


$returnArray = array(
    'persnr'=>$persnr,
    'persIdentifikatoryArray' => $persIdentifikatoryArray,
    'sql' => $sql
);

echo json_encode($returnArray);

