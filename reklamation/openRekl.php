<?php
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");

$o = json_decode($data);

$rekl = $o->rekl;

$a = AplDB::getInstance();

$a->query("update dreklamation set rekl_erledigt_am=NULL where id='".$rekl->id."' limit 1");


$returnArray = array(
    'rekl'=>$rekl,
);

echo json_encode($returnArray);
