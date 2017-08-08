<?php
session_start();
require_once '../../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$a = AplDB::getInstance();

$roles = $a->getRolesArray();

$returnArray = array(
        'role'=>$roles,
	'u'=> $_SESSION['user']
);

echo json_encode($returnArray);
