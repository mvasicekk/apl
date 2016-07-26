<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$start = 2014;$pocet=30;$konec=$start+$pocet;
for($i=$start;$i<=$konec;$i++){
    $cislotydne = date('W',mktime(0, 1, 1, 12, 28, $i));
    $v = sprintf("rok: %04d , pocet tydnu: %02d",$i,$cislotydne);
    echo $v."<br>";
}

// podle ISO8601 je posledni tyden roku tyden, ve kterem se nachazi den 28.12.