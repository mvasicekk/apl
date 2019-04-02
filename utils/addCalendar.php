<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require '../db.php';

$apl = AplDB::getInstance();

// vytvoreni kalendare a vlozeni do DB
//
$startDatum = '2018-12-03';

$stampStart = strtotime($startDatum);
//$stampStart = mktime(2,2,2, 1, 1, 2017);
$stampAktual = $stampStart;

for($den=0;$den<=700;$den++){
    $dateAktual = date('Y-m-d', $stampAktual);
    $cisloDne = date('N', $stampAktual);
    echo "<br>$dateAktual, cislo dne : $cisloDne";
    //$stampAktual += 60*60*24;
    $stampAktual = strtotime("+1 days",$stampAktual);
    $sql = "insert into calendar (datum,cislodne) values('$dateAktual',$cisloDne)";
    $apl->query($sql);
}
