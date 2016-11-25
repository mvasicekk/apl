<?php
session_start();
require '../db.php';

 echo "<head>";
echo "<meta charset='UTF-8'>";
echo "</head> ";

$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

$a = AplDB::getInstance();

$sql=  " select";
$sql.= "    dpers.PersNr as persnr,";
$sql.= "    dpers.`Name` as `name`,";
$sql.= "    dpers.Vorname as vorname,";
$sql.= "    dpers.regeloe,";
$sql.= "    dpers.eintritt";
$sql.= " from dpers";
$sql.= " where";
$sql.= "    dpers.dpersstatus='MA'";
$sql.= "    and dpers.PersNr between '$persvon' and '$persbis'";
$sql.= " order by";
$sql.= "    dpers.PersNr";

//echo $sql;

$persRows = $a->getQueryRows($sql);

/**
 * 
 * @param type $datumvon, datum od vcetne
 * @param type $plustage, pocet prac dnu, bez svatku, n, nu
 * @param AplDB $a Description
 */
function addNArbTage($a, $persnr, $datumvon, $datum3mesice, $plustage) {
    $bisDatum = NULL;
    // zjistit kolik je svatku, sobot, nedeli
    $sql = "";
    $sql.=" select";
    $sql.=" calendar.datum";
    $sql.=" from calendar";
    $sql.=" where";
    $sql.=" (calendar.cislodne=6 or calendar.cislodne=7 or calendar.svatek<>0)";
    $sql.=" and (calendar.datum between '$datumvon' and '$datum3mesice')";
    //echo "$sql<br>";
    $svatkyRows = $a->getQueryRows($sql);
    $svatkyDatums = array();
    if($svatkyRows!==NULL){
	foreach ($svatkyRows as $r){
	    array_push($svatkyDatums, $r['datum']);
	}
    }
    //AplDB::varDump($svatkyDatums);
    //zjistit n,nu,u
    $sql = "";
    $sql.= " select dzeit.Datum as datum";
    $sql.= " from dzeit";
    $sql.= " where";
    $sql.= " (dzeit.PersNr='$persnr')";
    $sql.= " and";
    $sql.= " (dzeit.tat='n' or dzeit.tat='nu' or dzeit.tat='u')";
    $sql.= " and";
    $sql.= " (dzeit.Datum between '$datumvon' and '$datum3mesice'";
    $nRows = $a->getQueryRows($sql);
    $nDatums = array();
    if($nRows!==NULL){
	foreach ($nRows as $r){
	    array_push($nDatums, $r['datum']);
	}
    }
    //AplDB::varDump($nDatums);
    
    // pojedu po dne a napocitam plustage
    $start = strtotime($datumvon);
    $end = strtotime($datum3mesice);
    $arbtage = 1;
    for ($t = $start; $t <= $end; $t+=24 * 60 * 60) {
	$testdatum = date('Y-m-d', $t)." 00:00:00";
	// pokud mam test datum v svatkyDatums nebo nRows preskakuju
	
	
	    if (array_search($testdatum, $svatkyDatums)!==FALSE) {
		//echo "test na svatky testdatum=$testdatum<br>";
		continue;
	    }

	    if (array_search($testdatum, $nDatums)!==FALSE) {
		//echo "test na nrows testdatum=$testdatum<br>";
		continue;
	    }

	
	//echo "arbtage: $arbtage, testdatum:$testdatum<br>";
	
	$arbtage++;
	
	if (($arbtage > $plustage)||($t >=  strtotime($datum3mesice))) {
	    //echo "arbtage>plustage";
	    $bisDatum = date('Y-m-d', $t);
	    //echo "bisDatum: $bisDatum,t: $t<br>";
	    break;
	}
    }
    return $bisDatum;
}

if($persRows!==NULL){
    //AplDB::varDump($persRows);
    foreach ($persRows as $r){
	$persnr = $r['persnr'];
	$datumvon = substr($r['eintritt'],0,10);
	$datum3mesice = date('Y-m-d', strtotime("+3 months", strtotime($datumvon)));
	echo "datumvon: $datumvon datum3mesice: $datum3mesice<br>";
	
	$a1von = $datumvon;
	$bis1datum = addNArbTage($a, $persnr, $datumvon, $datum3mesice,20);
	$arbTage = $a->getArbTageBetweenDatums($a1von, $bis1datum);
	$fondStd = 8*$arbTage;
	$arbStunden = $a->getArbStundenBetweenDatums($persnr, $a1von, $bis1datum);
	$anwPct = $fondStd!=0?$arbStunden/$fondStd*100:0;
	echo "a1: von: $a1von,bis:$bis1datum, arbTage = $arbTage,arbStunden = $arbStunden,anw% = $anwPct<br>";	
	
	$a2von = addNArbTage($a, $persnr, $bis1datum, $datum3mesice,2);
	$bis2datum = addNArbTage($a, $persnr, $a2von, $datum3mesice,20);
	echo "a2: von: $a2von,bis:$bis2datum<br>";
	
	$a3von = addNArbTage($a, $persnr, $bis2datum, $datum3mesice,2);
	$bis3datum = addNArbTage($a, $persnr, $a3von, $datum3mesice,20);
	echo "a3: von: $a3von,bis:$bis3datum<br>";
	
	
    }
}