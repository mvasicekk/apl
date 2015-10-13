<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once '../db.php';

$a = AplDB::getInstance();

$oes4abgnr = $a->getOEForAbgnr(95);

echo "oes4abgnr<br>";
AplDB::varDump($oes4abgnr);

$oes4abgnrA1 = split(";", $oes4abgnr);
$oes4abgnrA = array();
foreach ($oes4abgnrA1 as $oe1) {
    array_push($oes4abgnrA, trim($oe1));
}

echo "oes4abgnrA<br>";
AplDB::varDump($oes4abgnrA);

$import = 195000452;
$pg = $a->getPGFromAuftragsnr($import);

echo "pg<br>";
AplDB::varDump($pg);

$oes4PG = $a->getOESForPG($pg);

echo "oes4PG<br>";
AplDB::varDump($oes4PG);

$oes4frSp = $a->getOEForFrSp('N');



$intersectOE = array_intersect($oes4abgnrA, $oes4PG, $oes4frSp);
if (is_array($intersectOE)) {
    if (count($intersectOE) > 0) {
	foreach ($intersectOE as $k => $val) {
	    $oe = $val;
	}
    }
}

//posledni zachrana
if(trim($oe)==""){
    if(count($oes4abgnrA)>0){
	$oe = $oes4abgnrA[0];
    }
}

if(trim($oe)=="") $oe="?";

echo "oe = $oe";