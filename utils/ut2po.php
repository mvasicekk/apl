<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../db.php';
$a = AplDB::getInstance();
$apl = $a;

$fromTo = array(
    "2016-12-06"=>"2016-12-05",
    "2016-12-13"=>"2016-12-12",
    "2016-12-20"=>"2016-12-19",
);

foreach ($fromTo as $f=>$t){
    $sf = "select dzeitsoll.persnr,dzeitsoll.oe,dzeitsoll.stunden,dzeitsoll.stunden_vzkd from dzeitsoll where (datum='$f') and (dzeitsoll.oe<>'dp' or dzeitsoll.oe<>'d')";
    $sfRows = $a->getQueryRows($sf);
    foreach ($sfRows as $fr){
	$persnr = $fr['persnr'];
	$oe = $fr['oe'];
	$stunden = $fr['stunden'];
	$stunden_vzkd = $fr['stunden_vzkd'];
	echo "persnr:$persnr,oe:$oe,stunden:$stunden,stunden_vzkd:$stunden_vzkd<br>";
	$u = "update dzeitsoll set oe='$oe',stunden='$stunden',stunden_vzkd='$stunden_vzkd' where (persnr=$persnr) and (datum='$t') and (oe<>'dp' or oe<>'d') limit 1";
	$ar = $a->query($u);
	echo "$u ($ar)<br>";
    }
}
// vybrat hodnoty 