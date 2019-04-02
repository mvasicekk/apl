<?
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$u = $_SESSION['user'];
$panelid = $o->panelid;
$resourceid = $o->resourceid;
$pass = $o->pass;

$a = AplDB::getInstance();

$allow = $a->testReportPassword($resourceid, $pass, $u,1);

$returnArray = array(
	'u'=>$u,
	'panelid'=>$panelid,
	'resourceid'=>$resourceid,
	'allow'=>$allow
    );
    
echo json_encode($returnArray);
