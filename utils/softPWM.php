<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$outCount = 8;
$steps = 10;
$outIntensity = array(0,0,0,0,0,0,0,0);
$pattern = array(10,10,2,1);
$patternLength = count($pattern);

$mainCounter = 0;
for($l=0;$l<100;$l++){
    if($mainCounter<$patternLength){
	//nasouvam vzorek
	for($k=0;$k<=$mainCounter;$k++){
	    $outIntensity[$k]=$pattern[$mainCounter-$k];
	}
	$mainCounter++;
    }
    else{
	//rotuju pole vpravo o 1
	$lastVal = $outIntensity[$outCount-1];
	for($i=($outCount-1);$i>0;$i--){
	    $outIntensity[$i]=$outIntensity[$i-1];
	}
	$outIntensity[0]=$lastVal;
    }
    echo join(':', $outIntensity)."<br>";
}
//echo "outCount=$outCount,steps=$steps,outIntensity=".join(',',$outIntensity)."<hr>";
//
////misto tohoto cyklu bude nekonecny cyklus
//for ($a = 0; $a < 5; $a++) {
//    for ($step = 0; $step < $steps; $step++) {
//	$outByte = 0;
//	$outValue = 1 << ($outCount - 1);
//	$o = sprintf("outByte = %02X, outValue = %02X", $outByte, $outValue);
////	echo "step=$step,".$o,"<hr>";
//	for ($output = 0; $output < $outCount; $output++) {
//	    if ($outIntensity[$output] == 0 || $outIntensity[$output] <= $step) {
//		;
//	    } else {
//		$outByte|=$outValue;
//	    }
//	    $outValue>>=1;
//	    $o = sprintf("step=%d,output=%d,outIntensity=%d,outByte = %02X, outValue = %02X", $step,$output,$outIntensity[$output],$outByte, $outValue);
////	    echo $o."<hr>";
//	}
//	$o = sprintf("step=%02d,outByte=%02X", $step, $outByte);
//	echo $o . "<hr>";
//	//tady poslu byte do serioveho registru
//    }
//}
