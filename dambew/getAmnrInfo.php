<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$amnr = $o->amnr;
$amnrinfo = NULL;

$a = AplDB::getInstance();
			
$sql="select `eink-artikel`.`art-nr` as amnr,`eink-artikel`.`art-name1` as text from `eink-artikel` where AM_Ausgabe<>0 and `art-nr`='$amnr'";
$amnrinfo = $a->getQueryRows($sql);
if($amnrinfo!=NULL){
    $amnrinfo = $amnrinfo[0];
    // mozne sklady
    $sql = "select `eink-artikel_sklad`.sklad from `eink-artikel_sklad` where amnr='$amnr' order by sklad";
    $amnrSklady = $a->getQueryRows($sql);
}

$returnArray = array(
	'amnrSklady'=>$amnrSklady,
	'amnrinfo'=>$amnrinfo,
	'amnr'=>$amnr,
	'sql'=>$sql
    );
    
echo json_encode($returnArray);
