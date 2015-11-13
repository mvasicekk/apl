<?php

require_once '../db.php';

$inputData = $_GET;

// zjistim zda mam hodnotu value ulozenou v databazi artiklu

$a = AplDB::getInstance();

$auftragsnr = $_GET['aftragsnr'];

$auftragInfo = NULL;

$returnArray = array(
    "auftragInfo"=>$auftragInfo
);

echo json_encode($returnArray);
