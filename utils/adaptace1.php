<?php

session_start();
require '../db.php';
//
// echo "<head>";
//echo "<meta charset='UTF-8'>";
//echo "</head> ";

$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$jahr = $_GET['jahr'];
$monat = $_GET['monat'];

$a = AplDB::getInstance();

$sql = " select";
$sql.= "    dpers.PersNr as persnr,";
$sql.= "    dpers.`Name` as `name`,";
$sql.= "    dpers.Vorname as vorname,";
$sql.= "    dpers.regeloe,";
$sql.= "    dpers.eintritt";
$sql.= " from dpers";
$sql.= " where";
$sql.= "    dpers.dpersstatus='MA'";
//$sql.= "    and dpers.PersNr between '$persvon' and '$persbis'";
$sql.= "    and dpers.PersNr between '$persvon' and '$persbis'";
$sql.= " order by";
$sql.= "    dpers.PersNr";

//echo $sql;

$persRows = $a->getQueryRows($sql);

/**
 * 
 * @param AplDB $a
 * @param int $persnr
 * @param type $von
 * @param type $bis
 */
function leistung123($a, $persnr, $von, $bis) {

    $retArray = array(
	'l12' => array(
	    'von' => NULL,
	    'bis' => NULL,
	    'vzaby' => 0,
	    'leistungsGradR' => 0,
	    'leistungsGradGanzMonatR' => 0,
	    'monatNormMinuten' => 0,
	    'ganzMonatNormMinuten' => 0
	),
	'l22' => array(
	    'von' => NULL,
	    'bis' => NULL,
	    'vzaby' => 0,
	    'leistungsGradR' => 0,
	    'leistungsGradGanzMonatR' => 0,
	    'monatNormMinuten' => 0,
	    'ganzMonatNormMinuten' => 0
	),
	'lsum' => array(
	    'von' => NULL,
	    'bis' => NULL,
	    'vzaby' => 0,
	    'leistungsGradR' => 0,
	    'leistungsGradGanzMonatR' => 0,
	    'monatNormMinuten' => 0,
	    'ganzMonatNormMinuten' => 0
	),
    );

    $retArray['l12']['von'] = $von;
    $jahr = date('Y', strtotime($von));
    $monat = date('m', strtotime($von));
    $mDays = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $retArray['l12']['bis'] = $jahr . '-' . $monat . '-' . $mDays;

    $jahr = date('Y', strtotime($bis));
    $monat = date('m', strtotime($bis));
    $retArray['l22']['von'] = $jahr . '-' . $monat . '-01';
    $retArray['l22']['bis'] = $bis;

    $retArray['lsum']['von'] = $von;
    $retArray['lsum']['bis'] = $bis;



    foreach ($retArray as $ind => $val) {
	$von = $val['von'];
	$bis = $val['bis'];
	$arbTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
	$vonPers = $von;
	$arbTagePersMonat = $a->getArbTageBetweenDatums($vonPers, $bis);
	$dTage = $a->getTatTageBetweenDatums('d', $vonPers, $bis, $persnr);
	$nwTage = $a->getTatTageBetweenDatums('nw', $vonPers, $bis, $persnr);
	$monatNormMinuten = ($arbTagePersMonat - $dTage - $nwTage) * 8 * 60;
	$ganzMonatNormMinuten = $arbTageProMonat * 8 * 60;
	$leistungArray = $a->getPersLeistungArray($persnr, $von, $bis);
	$persInfoA = $a->getPersInfoArray($persnr);
	$leistFaktor = $persInfoA[0]['leistfaktor'];

	if ($leistungArray !== NULL) {
	    $vzaby = $leistungArray['vzaby'];
	    $vzaby_akkord = $leistungArray['vzaby_akkord'];
	    $vzaby_zeit = ($vzaby - $vzaby_akkord);
	} else {
	    $vzaby = 0;
	    $vzaby_akkord = 0;
	    $vzaby_zeit = 0;
	}

	$anwTageArbeitsTage = $a->getATageProPersnrBetweenDatums($persnr, $vonPers, $bis, 1);

	$leistungsGradGanzMonatR = $ganzMonatNormMinuten != 0 ? (($vzaby_akkord + $vzaby_zeit) / $ganzMonatNormMinuten) : 0;
	$leistungsGradR = $monatNormMinuten != 0 ? (($vzaby_akkord + $vzaby_zeit) / $monatNormMinuten) : 0;

	$retArray[$ind]['vzaby'] = $vzaby;
	$retArray[$ind]['leistungsGradR'] = $leistungsGradR;
	$retArray[$ind]['leistungsGradGanzMonatR'] = $leistungsGradGanzMonatR;
	$retArray[$ind]['monatNormMinuten'] = $monatNormMinuten;
	$retArray[$ind]['ganzMonatNormMinuten'] = $ganzMonatNormMinuten;
    }


    return $retArray;
}

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
    if ($svatkyRows !== NULL) {
	foreach ($svatkyRows as $r) {
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
    if ($nRows !== NULL) {
	foreach ($nRows as $r) {
	    array_push($nDatums, $r['datum']);
	}
    }
    //AplDB::varDump($nDatums);
    // pojedu po dne a napocitam plustage
    $start = strtotime($datumvon);
    $end = strtotime($datum3mesice);
    $arbtage = 1;
    for ($t = $start; $t <= $end; $t+=24 * 60 * 60) {
	$testdatum = date('Y-m-d', $t) . " 00:00:00";
	// pokud mam test datum v svatkyDatums nebo nRows preskakuju


	if (array_search($testdatum, $svatkyDatums) !== FALSE) {
	    //echo "test na svatky testdatum=$testdatum<br>";
	    continue;
	}

	if (array_search($testdatum, $nDatums) !== FALSE) {
	    //echo "test na nrows testdatum=$testdatum<br>";
	    continue;
	}


	//echo "arbtage: $arbtage, testdatum:$testdatum<br>";

	$arbtage++;

	if (($arbtage > $plustage) || ($t >= strtotime($datum3mesice))) {
	    //echo "arbtage>plustage";
	    $bisDatum = date('Y-m-d', $t);
	    //echo "bisDatum: $bisDatum,t: $t<br>";
	    break;
	}
    }
    return $bisDatum;
}

$afterMonat = 30;
echo "<table border='1'>";
//hlavicka pro mesic
$den = 1;
$denDo = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
//$denDo += $afterMonat;
$timeStart = strtotime($jahr . '-' . $monat . '-01');
$timeEnd = strtotime($jahr . '-' . $monat . '-' . $denDo)+$afterMonat*24*60*60;
echo "<tr>";
echo "<th>";
echo "persnr,von,3 mesice";
echo "</th>";
for ($timeN = $timeStart; $timeN <= $timeEnd; $timeN += 24 * 60 * 60) {
    echo "<th>";
    echo date('d', $timeN);
    echo "</th>";
}
echo "</tr>";
if ($persRows !== NULL) {
    //AplDB::varDump($persRows);
    foreach ($persRows as $r) {
	$persnr = $r['persnr'];
	$datumvon = substr($r['eintritt'], 0, 10);
	$name = $r['vorname'] . ' ' . $r['name'];
	$regeloe = $r['regeloe'];
	$datum3mesice = date('Y-m-d', strtotime("+3 months", strtotime($datumvon)));
	$a1von = $datumvon;
	$bis1datum = addNArbTage($a, $persnr, $datumvon, $datum3mesice, 20);
	$bis1Time = strtotime($bis1datum);
	$a2von = addNArbTage($a, $persnr, $bis1datum, $datum3mesice, 2);
	$bis2datum = addNArbTage($a, $persnr, $a2von, $datum3mesice, 20);
	$bis2Time = strtotime($bis2datum);
	$a3von = addNArbTage($a, $persnr, $bis2datum, $datum3mesice, 2);
	$bis3datum = addNArbTage($a, $persnr, $a3von, $datum3mesice, 20);
	$bis3Time = strtotime($bis3datum);

	$bIsInAdaptation = strtotime($jahr . '-' . $monat . '-01') > $bis3Time ? FALSE : TRUE;
	if ($bIsInAdaptation) {
	    echo "<tr>";
	    echo "<td>";
	    echo "$persnr ($datumvon) ,$datum3mesice";
	    echo "</td>";
	    //zjistit jestli je v adaptaci a kde
	    //projit dny vybraneho mesice
	    $den = 1;
	    $denDo = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
	    //$denDo += $afterMonat;
	    $timeStart = strtotime($jahr . '-' . $monat . '-01');
	    $timeEnd = strtotime($jahr . '-' . $monat . '-' . $denDo)+$afterMonat*24*60*60;
	    for ($timeN = $timeStart; $timeN <= $timeEnd; $timeN += 24 * 60 * 60) {
		$timeNow = $timeN;
		$adaptEndTime = strtotime($bis3datum);
		$adaptation = 0;
		$bIsInAdaptation = $timeNow > $adaptEndTime ? FALSE : TRUE;
		if ($bIsInAdaptation) {
		    if ($timeNow < $bis1Time) {
			$adaptation = 1;
		    } elseif ($timeNow < $bis2Time) {
			$adaptation = 2;
		    } else {
			$adaptation = 3;
		    }
		}
		echo "<td>";
		echo $adaptation;
		echo "</td>";
	    }
	    echo "</tr>";
	}
    }
}
echo "</table>";
