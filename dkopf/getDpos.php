<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$teil = $o->teil;


$a = AplDB::getInstance();
$sql = "select * from dpos where teil='$teil' order by `TaetNr-Aby`";
$dpos = $a->getQueryRows($sql);
			
$returnArray = array(
	'teil'=>$teil,
	'dpos'=>$dpos,
    );
    
echo json_encode($returnArray);
