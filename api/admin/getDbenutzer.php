<?php
session_start();
require_once '../../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);
$a = AplDB::getInstance();

$benutzerArray = $a->getBenutzers();

//$roles = $a->getRolesArray();

$returnArray = $benutzerArray;


echo json_encode($returnArray);
