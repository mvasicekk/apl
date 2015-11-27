<?php
require '../db.php';
$apl = AplDB::getInstance();

$persVon = 1;
$persBis = 99999;
$datVon = '2015-01-01';
$datBis = '2015-12-31';

$sql.=" select";
$sql.="     drueck.PersNr as persnr,";
$sql.="     drueck.Teil,";
$sql.="     drueck.insert_stamp,";
$sql.="     drueck.`Stück` as stk,";
$sql.="     drueck.Datum as datum,";
$sql.="     dkopf.Gew as teil_gew,";
$sql.="     count(TaetNr) as tat_count,";
$sql.="     sum(`Auss-Stück`) as stk_auss_sum";
$sql.=" from";
$sql.="     drueck";
$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
$sql.=" where";
$sql.="     PersNr between '$persVon' and '$persBis'";
$sql.="     and Datum between '$datVon' and '$datBis'";
$sql.="     and (DATE_FORMAT(`verb-von`,'%H:%i:%s')!='00:00:00')";
$sql.=" group by";
$sql.="     PersNr,";
$sql.="     drueck.Teil,";
$sql.="     drueck.insert_stamp,";
$sql.="     drueck.`Stück`";
    

$persRows = $apl->getQueryRows($sql);

foreach ($persRows as $pr){
    $persnr = $pr['persnr'];
    $datum = $pr['datum'];
    $month = date('m',  strtotime($datum));
    $stkGut = intval($pr['stk']);
    $stkAuss = intval($pr['stk_auss_sum']);
    $gew = floatval($pr['teil_gew']);
    
    $zeilen[$persnr][$month]['stk_gut']+=$stkGut;
    $zeilen[$persnr][$month]['stk_auss']+=$stkAuss;
    $zeilen[$persnr][$month]['sum_gew']+=($stkGut+$stkAuss)*$gew;
}

//AplDB::varDump($zeilen);


echo "<table border='1'>";
foreach ($zeilen as $persnr=>$monthArray){
    echo "<tr>";
    echo "<td>PersNr</td>";
    foreach ($monthArray as $month=>$paramArray){
	echo "<td>$month</td>";
    }
    echo "</tr>";
    
    echo "<tr>";
    echo "<td>$persnr</td>";
    foreach ($monthArray as $month=>$paramArray){
	echo "<td>";
	echo "StkGut:".$paramArray['stk_gut']."<br>";
	echo "StkAuss:".$paramArray['stk_auss']."<br>";
	echo "GewGesamt:".$paramArray['sum_gew']."<br>";
	echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";