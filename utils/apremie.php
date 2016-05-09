<?php

require_once '../db.php';

// vytahnu paramety z _GET ( z getparameters.php )
$parameters = $_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$stammOE = strtoupper(strtr(trim($_GET['stammoe']), '*', '%'));
$persVon = $persvon;
$persBis = $persbis;

$a = AplDB::getInstance();

$persPremieArray = $a->getPersnrApremieArray($monat, $jahr, $persvon, $persbis, $stammOE);
echo json_encode($persPremieArray);