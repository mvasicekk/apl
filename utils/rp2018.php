<?php

require_once '../db.php';

// vytahnu paramety z _GET ( z getparameters.php )
$parameters = $_GET;
$von = $_GET['von'];
$bis = $_GET['bis'];
$datumVon = $von;
$datumBis = $bis;
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$output = trim($_GET['output']);

$persVon = $persvon;
$persBis = $persbis;

$a = AplDB::getInstance();

$sql = "select dpers.PersNr as persnr from dpers";
$sql .= " where (PersNr between '$persVon' and '$persBis') and (dpersstatus='MA')";
$sql .= " and (kor=0)";
$sql .= " order by dpers.persnr";

$persnrArray = $a->getQueryRows($sql);

if ($persnrArray !== NULL) {
    foreach ($persnrArray as $p) {
	$persnr = $p['persnr'];

	//loajalita ----------------------------------------------------------------
	$persInfoA = $a->getPersInfoArray($persnr);
	$zeilen[$persnr]['name'] = $persInfoA[0]['Name'] . ' ' . $persInfoA[0]['Vorname'];


	$zeilen[$persnr]['apremie_flag'] = $persInfoA[0]['a_praemie'] != 0 ? $persInfoA[0]['a_praemie_st'] != 0 ? '!' : 'V' : '';
	if ($calculateIfFlagNotTrue === FALSE && $zeilen[$persnr]['apremie_flag'] == '') {
	    continue;
	}
	$regeloe = $persInfoA[0]['regeloe'];
	$zeilen[$persnr]['regeloe'] = $persInfoA[0]['regeloe'];

	$eintritt = $a->getEintrittsDatumDB($persnr);
	$zeilen[$persnr]['loajalita']['eintritt']['sum'] = date('y-m-d', strtotime($eintritt));
	$zeilen[$persnr]['loajalita']['austritt']['sum'] = strlen(trim($persInfoA[0]['austritt'])) == 0 ? '' : date('y-m-d', strtotime($persInfoA[0]['austritt']));
	$aTageFond = $a->getArbTageBetweenDatums($datumVon, $datumBis);
	$zeilen[$persnr]['loajalita']['von_bis_fond_days'] = $aTageFond;
	$zeilen[$persnr]['loajalita']['von_bis_fond_hours'] = $aTageFond * 8;


	// nacharbeit ---------------------------------------------------------------

	$sql = " select";
	$sql .= " drueck.PersNr as persnr";
	$sql .= " ,drueck.Datum as datum";
	//2016-07-08
	//$sql.=" ,sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,abs(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,abs(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx";
	$sql .= " ,sum(if(TaetNr>=6500 and TaetNr<=6599,if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-IST`,(drueck.`Stück`)*`VZ-IST`),0)) as vzaby_65xx";
	$sql .= " ,sum(if(auss_typ=4,(drueck.`Stück`+`Auss-Stück`)*`VZ-SOLL`,(drueck.`Stück`)*`VZ-SOLL`)) as vzkd";
	$sql .= " from";
	$sql .= " drueck";
	$sql .= " where";
	$sql .= " PersNr='$persnr'";
	$sql .= " and Datum between '$datumVon' and '$datumBis'";
	$sql .= " group by";
	$sql .= " PersNr,";
	$sql .= " drueck.Datum";

	$persRows = $a->getQueryRows($sql);
	if ($persRows !== NULL) {
	    foreach ($persRows as $pr) {
		$vzaby_65xx = abs(floatval($pr['vzaby_65xx']));
		$vzkd = floatval($pr['vzkd']);
		$zeilen[$persnr]['nacharbeit']['vzaby_65xx'] += $vzaby_65xx;
		$zeilen[$persnr]['nacharbeit']['vzkd'] += $vzkd;
	    }
	    $vzaby_65xx = floatval($zeilen[$persnr]['nacharbeit']['vzaby_65xx']);
	    $vzkd = floatval($zeilen[$persnr]['nacharbeit']['vzkd']);
	    if (($vzkd != 0)) {
		$zeilen[$persnr]['nacharbeit']['faktor'] = ($vzaby_65xx / $vzkd) * 100;
	    } else {
		$zeilen[$persnr]['nacharbeit']['faktor'] = '';
	    }
	}
	//--------------------------------------------------------------------------
	// Ausschuss ---------------------------------------------------------------
	$sql = " select";
	$sql .= "     drueck.PersNr as persnr,";
	$sql .= "     drueck.Teil,";
	$sql .= "     drueck.insert_stamp,";
	$sql .= "     drueck.`Stück` as stk,";
	$sql .= "     drueck.Datum as datum,";
	$sql .= "     dkopf.Gew as teil_gew,";
	$sql .= "     count(TaetNr) as tat_count,";
	$sql .= "     sum(`Auss-Stück`) as stk_auss_sum";
	$sql .= " from";
	$sql .= "     drueck";
	$sql .= " join dkopf on dkopf.Teil=drueck.Teil";
	$sql .= " where";
	$sql .= "     PersNr='$persnr'";
	$sql .= "     and Datum between '$datumVon' and '$datumBis'";
	$sql .= "     and (DATE_FORMAT(`verb-von`,'%H:%i:%s')!='00:00:00')";
	$sql .= " group by";
	$sql .= "     PersNr,";
	$sql .= "     drueck.Teil,";
	$sql .= "     drueck.insert_stamp,";
	$sql .= "     drueck.`Stück`";
	$persRows = $a->getQueryRows($sql);
	if ($persRows !== NULL) {
	    foreach ($persRows as $pr) {
		$stkGut = intval($pr['stk']);
		$stkAuss = intval($pr['stk_auss_sum']);
		$gew = floatval($pr['teil_gew']);
		$zeilen[$persnr]['A6']['sum_gew'] += ($stkGut + $stkAuss) * $gew;
	    }
	    $a6Gew = $a->getGewAussTypVonBisPersnr(6, $datumVon, $datumBis, $persnr);
	    $sumGew = floatval($zeilen[$persnr]['A6']['sum_gew']);
	    $zeilen[$persnr]['A6']['a6_gew'] = $a6Gew;
	    if (($sumGew != 0)) {
		$zeilen[$persnr]['A6']['a6_prozent'] = ($a6Gew / $sumGew) * 100;
	    } else {
		$zeilen[$persnr]['A6']['a6_prozent'] = '';
	    }
	}
	//--------------------------------------------------------------------------
	// reklamace ---------------------------------------------------------------
	$sql = " select";
	$sql .= " dpersschulung.persnr,";
	$sql .= " dreklamation.rekl_nr,";
	$sql .= "     dreklamation.rekl_datum,";
	$sql .= " dreklamation.interne_bewertung";
	$sql .= " from";
	$sql .= " dreklamation";
	$sql .= " join dpersschulung on dpersschulung.rekl_id=dreklamation.id";
	$sql .= " where";
	$sql .= " dreklamation.rekl_datum between '$datumVon' and '$datumBis'";
	$sql .= " and dpersschulung.persnr='$persnr'";
	$sql .= " and dpersschulung.rekl_verursacher<>0";
	$sql .= " group by";
	$sql .= " dpersschulung.persnr,";
	$sql .= " dreklamation.rekl_nr";

	$monthsArray = array();
	$persRows = $a->getQueryRows($sql);
	if ($persRows !== NULL) {
	    foreach ($persRows as $pr) {
		$ie = strtoupper(substr($pr['rekl_nr'], 0, 1));
		if ($ie == 'I') {
		    $zeilen[$persnr]['rekl']['sum_bewertung_I'] += $pr['interne_bewertung'];
		}
		if ($ie == 'E') {
		    $zeilen[$persnr]['rekl']['sum_bewertung_E'] += $pr['interne_bewertung'];
		}
	    }
	}

	//dochazka -----------------------------------------------------------------
	$sql = " select";
	$sql .= " dzeit.PersNr as persnr,";
	$sql .= " dzeit.tat,";
	$sql .= " dtattypen.oestatus,";
	$sql .= " dzeit.Datum as datum,";
	$sql .= " sum(if(dtattypen.oestatus='a',dzeit.stunden,0)) as sum_stundena";
	$sql .= " from";
	$sql .= " dzeit";
	$sql .= " join dtattypen on dtattypen.tat=dzeit.tat";
	$sql .= " where";
	$sql .= " dzeit.persnr='$persnr'";
	$sql .= " and dzeit.datum between '$datumVon' and '$datumBis'";
	$sql .= " group by";
	$sql .= " dzeit.persnr,";
	$sql .= " dzeit.tat,";
	$sql .= " dzeit.Datum";
	$persRows = $a->getQueryRows($sql);
	if ($persRows !== NULL) {
	    foreach ($persRows as $pr) {
		$zeilen[$persnr]['dzeit']['anwstd'] += $pr['sum_stundena'];
		if ($pr['tat'] == 'd' || $pr['tat'] == 'n' || $pr['tat'] == 'np' || $pr['tat'] == 'nu' || $pr['tat'] == 'nv' || $pr['tat'] == 'nw' || $pr['tat'] == 'p' || $pr['tat'] == 'u' || $pr['tat'] == 'z' || $pr['tat'] == '?') {
		    // nacitat jen ty, ktere me zajimaji
		    $zeilen[$persnr]['dzeit'][$pr['tat']] += 1;
		}
	    }
	    $arbTageProMonat = $a->getArbTageBetweenDatums($von, $bis);
	    $zeilen[$persnr]['dzeit']['astunden_fond'] = $arbTageProMonat * 8;
	    $zeilen[$persnr]['dzeit']['anw_prozent'] = $zeilen[$persnr]['dzeit']['astunden_fond'] != 0 ? $zeilen[$persnr]['dzeit']['anwstd'] / $zeilen[$persnr]['dzeit']['astunden_fond'] * 100 : 0;
	}
    }
}

$separator = ";";
$cells_header = array(
    "persnr",
    "name",
    "regeloe",
    "astunden_fond",
    "anwstd",
    "anw_prozent",
    "z_tage",
    "a6_gew",
    "sum_gew",
    "a6_prozent",
    "na_vzaby_65xx",
    "na_vzkd",
    "na_faktor",
    "rekl_bewertung_I",
    "rekl_bewertung_E"
    );
//echo "persnr;name;regeloe;astunden_fond;anwstd;anw_prozent;z_tage;a6_gew;sum_gew;a6_prozent;na_vzaby_65xx;na_vzkd;na_faktor;rekl_bewertung_I;rekl_bewertung_E<br>";
//foreach ($zeilen as $persnr=>$persZeile){
//    echo "$persnr"."$separator";
//    echo $persZeile['name']."$separator";
//    echo $persZeile['regeloe']."$separator";
//    echo $persZeile['dzeit']['astunden_fond']."$separator";
//    echo $persZeile['dzeit']['anwstd']."$separator";
//    echo $persZeile['dzeit']['anw_prozent']."$separator";
//    echo $persZeile['dzeit']['z']."$separator";
//    echo $persZeile['A6']['a6_gew']."$separator";
//    echo $persZeile['A6']['sum_gew']."$separator";
//    echo $persZeile['A6']['a6_prozent']."$separator";
//    echo $persZeile['nacharbeit']['vzaby_65xx']."$separator";
//    echo $persZeile['nacharbeit']['vzkd']."$separator";
//    echo $persZeile['nacharbeit']['faktor']."$separator";
//    echo $persZeile['rekl']['sum_bewertung_I']."$separator";
//    echo $persZeile['rekl']['sum_bewertung_E']."$separator";
//    echo "<br>";
//}

date_default_timezone_set('Europe/Prague');

if($output=='json'){
    echo json_encode($zeilen);
}
else{
    /** PHPExcel */
require_once '../Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();


// Set properties
$objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy("apl")
							 ->setTitle("RP2018")
							 ->setSubject("RP2018")
							 ->setDescription("RP2018")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

// popisky sloupcu
$radek = 1;
$sloupec = 0;

foreach($cells_header as $ch){
    $popis = $ch;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}
$radek++;
$sloupec = 0;

foreach ($zeilen as $persnr=>$persZeile){
    $sloupec = 0;
    $popis = "$persnr";
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;


    $popis = $persZeile['name'];
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = $persZeile['regeloe'];
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['dzeit']['astunden_fond']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['dzeit']['anwstd']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['dzeit']['anw_prozent']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['dzeit']['z']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['A6']['a6_gew']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['A6']['sum_gew']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['A6']['a6_prozent']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['nacharbeit']['vzaby_65xx']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['nacharbeit']['vzkd']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['nacharbeit']['faktor']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['rekl']['sum_bewertung_I']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
    
    $popis = floatval($persZeile['rekl']['sum_bewertung_E']);
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $radek++;
}
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('RP2018');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="RP2018.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
}


