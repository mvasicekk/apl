<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$sql = "select * from werkstoffe order by beschreibung";
$werkstoffe = $a->getQueryRows($sql);
			
$returnArray = array(
	'werkstoffe'=>$werkstoffe,
    );
    
echo json_encode($returnArray);
