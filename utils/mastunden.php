<?php
session_start();
require '../db.php';

 echo "<head>";
echo "<meta charset='UTF-8'>";
echo "</head> ";
	 
$apl = AplDB::getInstance();


$persnrArray = $apl->getPersnrFromEintritt('1990-01-01', TRUE);
//echo "persnr,eintritt,name,plusminusStunden2011,arbstunden2012,plusminusStunden2012,arbstunden2013,plusminusStunden2013,ma2013,arbstunden2014,plusminusStunden2014,kor,maStd,regelStunden<br>";
echo "persnr;eintritt;name;arbstunden2014;plusminusStunden2014;endOfVorYearStd;arbstunden2015;plusminusStunden2015;kor;maStd;regelStunden;startStd;maStundenDatumAb<br>";
foreach ($persnrArray as $persnr) {
    $eintritt = substr($apl->getEintrittsDatumDB($persnr), 0, 10);
    $nameArray = $apl->getNameVorname($persnr);
    if ($nameArray !== NULL)
	$name = $nameArray['name'] . ' ' . $nameArray['vorname'];
    else
	$name = '';
    
    $pi = $apl->getPersInfoArray($persnr);
    $kor=$pi[0]['kor'];
    $maStd = $pi[0]['MAStunden'];
    
    //zjistit jaky ma regelzeit
    $regelStunden = $apl->getRegelarbeitDatum(12, 2015, $persnr);
    // v pripade, ze name zaznam v tabulce dstddif tak pouziju hodnotu z dpers
    if ($regelStunden === NULL)
	$regelStunden = $apl->getRegelarbzeit($persnr);

    $regelStunden = floatval($regelStunden);
    
    // budu brat jen ma s nastupem pred 2013-12-31
    $t = strtotime($eintritt);
    $t2013 = strtotime("2013-12-31");
//    if (($t < $t2013)&&($kor!=1)&&($maStd==1)) {
    
    if (($kor!=1)&&($maStd==1)) {
//	$plusminusStunden2011 = number_format($apl->getPlusMinusStunden(12, 2011, $persnr), 1, '.', '');
//	$arbstunden2012 = number_format($apl->getArbStundenBetweenDatums($persnr, '2012-01-01', '2012-12-31'), 1, '.', '');
//	$plusminusStunden2012 = number_format($apl->getPlusMinusStunden(12, 2012, $persnr), 1, '.', '');
//	$arbstunden2013 = number_format($apl->getArbStundenBetweenDatums($persnr, '2013-01-01', '2013-12-31'), 1, '.', '');
//	$plusminusStunden2013 = number_format($apl->getPlusMinusStunden(12, 2013, $persnr), 1, '.', '');
	$arbstunden2014 = number_format($apl->getArbStundenBetweenDatums($persnr, '2014-01-01', '2014-12-31'), 1, '.', '');
	$plusminusStunden2014 = number_format($apl->getPlusMinusStunden(12, 2014, $persnr), 1, '.', '');
	$endOfVorYearStd = $apl->getMAStundenDatum(date('Y-m-d',mktime(1, 1, 1, 12, 31, 2014)),$persnr);
	
	$stddiff = $apl->getStdDiff(12, 2015, $persnr);
	    if($stddiff===NULL) {
		$startStd = 0;
	    }
	    else{
		$startStd = floatval ($stddiff['stunden']);
		$maStundenDatumAb = $stddiff['datumDB'];
	    }
	    
	$arbstunden2015 = number_format($apl->getArbStundenBetweenDatums($persnr, '2015-01-01', '2015-12-31'), 1, '.', '');
	$plusminusStunden2015 = number_format($apl->getPlusMinusStunden(12, 2015, $persnr), 1, '.', '');

	//$ma2013 = number_format($plusminusStunden2013-$plusminusStunden2012, 1, '.', '');
	//echo "$persnr,$eintritt,$name,$plusminusStunden2011,$arbstunden2012,$plusminusStunden2012,$arbstunden2013,$plusminusStunden2013,$ma2013,$arbstunden2014,$plusminusStunden2014,$kor,$maStd,$regelStunden<br>";
	
	echo "$persnr;$eintritt;$name;$arbstunden2014;$plusminusStunden2014;$endOfVorYearStd;$arbstunden2015;$plusminusStunden2015;$kor;$maStd;$regelStunden;$startStd;$maStundenDatumAb<br>";
//	$ma2013 = 0;
//	$sql = "insert into dstddif (persnr,datum,stunden,regelstunden_weiter)";
//	$sql.=" values($persnr,'2013-12-31',$ma2013,$regelStunden);";
//	echo "$sql<br>";
    }
}
