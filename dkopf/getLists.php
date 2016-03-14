<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$sql = "select * from werkstoffe order by beschreibung";
$werkstoffe = $a->getQueryRows($sql);

$sql = "select dlager.Lager as lager from dlager order by dlager.Lager";
$lager = $a->getQueryRows($sql);

$returnArray = array(
	'werkstoffe'=>$werkstoffe,
	'lager'=>$lager,
    );
    
echo json_encode($returnArray);
