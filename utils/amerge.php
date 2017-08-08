<?php
require_once '../db.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$a1 = array(
    array('id_faktor'=>10,'persnr'=>104,'id_firma_faktor'=>10),
    array('id_faktor'=>20,'persnr'=>104,'id_firma_faktor'=>10),
    array('id_faktor'=>30,'persnr'=>104,'id_firma_faktor'=>10),
);

$a2 = array(
    array('id_faktor'=>40,'persnr'=>104,'id_firma_faktor'=>10),
    array('id_faktor'=>30,'persnr'=>104,'id_firma_faktor'=>10),
);



echo "dve pole<hr>";
AplDB::varDump($a1);
AplDB::varDump($a2);

echo "sloucena pole<hr>";
foreach ($a1 as $p){
    $a3[$p['id_faktor'].':'.$p['id_firma_faktor']] = $p;
}
foreach ($a2 as $p){
    $a3[$p['id_faktor'].':'.$p['id_firma_faktor']] = $p;
}
//$a3 = array_merge($a1,$a2);

AplDB::varDump($a3);
