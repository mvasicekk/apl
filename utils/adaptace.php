<?php
session_start();
require '../db.php';
//
// echo "<head>";
//echo "<meta charset='UTF-8'>";
//echo "</head> ";

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
//$sql.= "    dpers.dpersstatus='MA'";
//$sql.= "    and dpers.PersNr between '$persvon' and '$persbis'";
$sql.= "    dpers.PersNr between '$persvon' and '$persbis'";
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
	'l12'=>array(
	    'von'=>NULL,
	    'bis'=>NULL,
	    'vzaby'=>0,
	    'leistungsGradR'=>0,
	    'leistungsGradGanzMonatR'=>0,
	    'monatNormMinuten'=>0,
	    'ganzMonatNormMinuten'=>0
	),
	'l22'=>array(
	    'von'=>NULL,
	    'bis'=>NULL,
	    'vzaby'=>0,
	    'leistungsGradR'=>0,
	    'leistungsGradGanzMonatR'=>0,
	    'monatNormMinuten'=>0,
	    'ganzMonatNormMinuten'=>0
	),
	'lsum'=>array(
	    'von'=>NULL,
	    'bis'=>NULL,
	    'vzaby'=>0,
	    'leistungsGradR'=>0,
	    'leistungsGradGanzMonatR'=>0,
	    'monatNormMinuten'=>0,
	    'ganzMonatNormMinuten'=>0
	),
    );
    
    $retArray['l12']['von'] = $von;
    $jahr = date('Y',  strtotime($von));
    $monat = date('m',  strtotime($von));
    $mDays = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $retArray['l12']['bis'] = $jahr.'-'.$monat.'-'.$mDays;
    
    $jahr = date('Y',  strtotime($bis));
    $monat = date('m',  strtotime($bis));
    $retArray['l22']['von'] = $jahr.'-'.$monat.'-01';
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
	$retArray[$ind]['leistungsGradR']=$leistungsGradR;
	$retArray[$ind]['leistungsGradGanzMonatR']=$leistungsGradGanzMonatR;
	$retArray[$ind]['monatNormMinuten']=$monatNormMinuten;
	$retArray[$ind]['ganzMonatNormMinuten']=$ganzMonatNormMinuten;
	
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

date_default_timezone_set('Europe/Prague');

/** PHPExcel */
require_once '../Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

//$user = get_user_pc();
$user = "arnost";
// Set properties
$objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy($user)
							 ->setTitle("adaptace")
							 ->setSubject("adaptace")
							 ->setDescription("adaptace")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");


$radek = 1;
$sloupec = 0;

$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "persnr");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "name");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "regeloe");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "eintritt");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "probezeit3mon");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "adapt");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "od");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "do");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "anw");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "l12");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "l22");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "lsum");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "od");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "do");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "anw");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "l12");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "l22");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "lsum");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "od");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "do");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "anw");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "l12");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "l22");
$sloupec++;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, "lsum");
$sloupec++;

$radek++;
$sloupec = 0;



if($persRows!==NULL){
    //AplDB::varDump($persRows);
    foreach ($persRows as $r){
	$persnr = $r['persnr'];
	$datumvon = substr($r['eintritt'],0,10);
	$name = $r['vorname'].' '.$r['name'];
	$regeloe = $r['regeloe'];
	$datum3mesice = date('Y-m-d', strtotime("+3 months", strtotime($datumvon)));
	$a1von = $datumvon;
	$bis1datum = addNArbTage($a, $persnr, $datumvon, $datum3mesice,20);
	$bis1Time = strtotime($bis1datum);
	$a2von = addNArbTage($a, $persnr, $bis1datum, $datum3mesice,2);
	$bis2datum = addNArbTage($a, $persnr, $a2von, $datum3mesice,20);
	$bis2Time = strtotime($bis2datum);
	$a3von = addNArbTage($a, $persnr, $bis2datum, $datum3mesice,2);
	$bis3datum = addNArbTage($a, $persnr, $a3von, $datum3mesice,20);
	$bis3Time = strtotime($bis3datum);
	
	//zjistit jestli je v adaptaci a kde
	$timeNow = time();
	$adaptEndTime = strtotime($bis3datum);
	
	$adaptation = 0;
	$bIsInAdaptation = $timeNow>$adaptEndTime?FALSE:TRUE;
	if($bIsInAdaptation){
	    if($timeNow<$bis1Time){
		$adaptation = 1;
	    }
	    elseif ($timeNow<$bis2Time) {
		$adaptation = 2;
	    }
	    else{
		$adaptation = 3;
	    }
	}
	
	//echo "$persnr;$name;$regeloe;$datumvon;$datum3mesice;";
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $persnr);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $name);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $regeloe);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $datumvon);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $datum3mesice);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $adaptation);
	$sloupec++;
	
	// adaptace 1
	
	$arbTage = $a->getArbTageBetweenDatums($a1von, $bis1datum);
	$fondStd = 8 * $arbTage;
	$arbStunden = $a->getArbStundenBetweenDatums($persnr, $a1von, $bis1datum);
	$anwPct = $fondStd != 0 ? $arbStunden / $fondStd * 100 : 0;
    	$leistung123 = leistung123($a, $persnr, $a1von, $bis1datum);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $a1von);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $bis1datum);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $anwPct);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['l12']['leistungsGradR']);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['l22']['leistungsGradR']);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['lsum']['leistungsGradR']);
	$sloupec++;

	// adaptace 2
	
	$arbTage = $a->getArbTageBetweenDatums($a2von, $bis2datum);
	$fondStd = 8 * $arbTage;
	$arbStunden = $a->getArbStundenBetweenDatums($persnr, $a2von, $bis2datum);
	$anwPct = $fondStd != 0 ? $arbStunden / $fondStd * 100 : 0;
    	$leistung123 = leistung123($a, $persnr, $a2von, $bis2datum);

	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $a2von);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $bis2datum);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $anwPct);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['l12']['leistungsGradR']);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['l22']['leistungsGradR']);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['lsum']['leistungsGradR']);
	$sloupec++;

	// adaptace 3
	
	$arbTage = $a->getArbTageBetweenDatums($a3von, $bis3datum);
	$fondStd = 8 * $arbTage;
	$arbStunden = $a->getArbStundenBetweenDatums($persnr, $a3von, $bis3datum);
	$anwPct = $fondStd != 0 ? $arbStunden / $fondStd * 100 : 0;
    	$leistung123 = leistung123($a, $persnr, $a3von, $bis3datum);
	
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $a3von);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $bis3datum);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $anwPct);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['l12']['leistungsGradR']);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['l22']['leistungsGradR']);
	$sloupec++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $leistung123['lsum']['leistungsGradR']);
	$sloupec++;

	//echo "<br>";
	$radek++;
	$sloupec=0;
    }
    
}

$objPHPExcel->getActiveSheet()->setTitle('adaptace');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="adaptace.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;