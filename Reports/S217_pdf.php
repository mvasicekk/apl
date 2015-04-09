<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S217";
$doc_subject = "S217 Report";
$doc_keywords = "S217";

// necham si vygenerovat XML

$parameters=$_GET;

$a = AplDB::getInstance();
$apl = $a;

$kd_von = $_GET['kundevon'];
$kd_bis = $_GET['kundebis'];
$von = $a->make_DB_datum($_GET['von']);
$bis = $a->make_DB_datum($_GET['bis']);
$rm_bis = $_GET['rm_bis'];

    $rmZeit = $apl->validateZeit($rm_bis);
    if($rmZeit=="00:00"){
	$rmZeit = date("H:i");
    }
    $rm_bis = $rmZeit;

    $rmDateTime = $apl->make_DB_datetime($rmZeit, date('d.m.Y'));

$timeVon = strtotime($von);
$timeBis = strtotime($bis);

$statnrArray = array("S0011","S0041","S0051","S0061","S0081");

$rmZeit = $apl->validateZeit($rm_bis);
if($rmZeit=="00:00"){
    $rmZeit = date("H:i");
}

$dnyvTydnu = array("Po","Ut","St","Ct","Pa","So","Ne");
$rm_bis = $rmZeit;

$rmDateTime = $apl->make_DB_datetime($rmZeit, date('d.m.Y'));
$rmBisFormat = date('d.m.Y')." ".$rmZeit;
$planyArray = $apl->getKundenMitGeplantenMinuten($kd_von, $kd_bis);

$sumIstTagS610Array = array();
$sumZuBearbeitenArray = array();
$sumSollPlanTagArray = array();

if($planyArray!==NULL){
    if (($timeVon > 0) && ($timeBis >= $timeVon)) {
	$time = $timeVon;
	while ($time <= $timeBis) {
	    $timeID = date('Ymd',$time);
	    $istTagArray1 = $apl->getIstFertigKundeS610($kd_von, $kd_bis, date('Y-m-d',$time));
	    // pro jednotlive zakazniky
	    foreach ($planyArray as $plan){
		$kunde = $plan['kunde'];
		//ist S610
		if($istTagArray1!==NULL){
		    $istTagS610Array[$timeID][$kunde]=$istTagArray1[$kunde];
		}
		else{
		    $istTagS610Array[$timeID][$kunde] = array(
			"termin"=>$kunde."NOEX",
			"sum_vzkd_S0011"=>0,
			"sum_vzkd_S0041"=>0,
			"sum_vzkd_S0051"=>0,
			"sum_vzkd_S0061"=>0,
			"sum_vzkd_S0081"=>0,
			"sum_vzkd"=>0,
			);
		}
		
		//echo "<h4>$kunde ist S610</h4>";
		//AplDB::varDump($istTagS610Array[$timeID][$kunde]);
		
		//planvzkd
		$r = $apl->getPlanVzKdNoEx($kunde, $time);
		if($r!==NULL){
		    $vzkdPlanArray[$timeID][$kunde] = $r[0];
		}
		else {
		    $vzkdPlanArray[$timeID][$kunde] = array(
			"termin"=>$kunde."NOEX",
			"sum_vzkd_S0011"=>0,
			"sum_vzkd_S0041"=>0,
			"sum_vzkd_S0051"=>0,
			"sum_vzkd_S0061"=>0,
			"sum_vzkd_S0081"=>0,
			"sum_vzkd"=>0,
			);
		}
		$planT = $vzkdPlanArray[$timeID][$kunde]['termin'];
		//istfertig
		$r = $apl->getPlanIstFertigNoEx($kunde, $time,$rmDateTime);
		if($r!==NULL){
		    $istFertigArray[$timeID][$kunde] = $r[0];
		}
		else {
		    $istFertigArray[$timeID][$kunde] = array(
			"termin"=>$kunde."NOEX",
			"sum_vzkd_S0011"=>0,
			"sum_vzkd_S0041"=>0,
			"sum_vzkd_S0051"=>0,
			"sum_vzkd_S0061"=>0,
			"sum_vzkd_S0081"=>0,
			"sum_vzkd"=>0,
			);
		}
		//zubearbeiten
		foreach ($vzkdPlanArray[$timeID][$kunde] as $minIndex=>$val){
		    $zuBearbeitenArray[$timeID][$kunde][$minIndex] = intval($val)-intval($istFertigArray[$timeID][$kunde][$minIndex]);
		}
		//solltag
		$s1 = 0;
		$s2 = 0;
		foreach ($statnrArray as $statnr){
		    $minuten = $apl->getPlanSollTagMinuten($kunde."NOEX", $statnr, date('Y-m-d',$time));
		    $minuten = intval($minuten);
		    $sollPlanTagArray[$timeID][$kunde][$statnr] = $minuten;
		    $beforeMins = floatval($apl->getPlanSollTagMinuten($planT, $statnr, date('Y-m-d',$time), TRUE));
		    $zuBearbeitenArray[$timeID][$kunde]["sum_vzkd_".$statnr] -= $beforeMins;
		    //nascitat sumu
		    $s1+=$zuBearbeitenArray[$timeID][$kunde]["sum_vzkd_".$statnr];
		    $s2+=$sollPlanTagArray[$timeID][$kunde][$statnr];
		}
		$zuBearbeitenArray[$timeID][$kunde]["sum_vzkd"] = $s1;
		$sollPlanTagArray[$timeID][$kunde]["sum_vzkd"] = $s2;
	    }
	    
	    //soucty pro vsechny zakazniky pro dany datum
	    foreach ($planyArray as $plan){
		$kunde = $plan['kunde'];
		foreach ($sollPlanTagArray[$timeID][$kunde] as $k=>$value){
		    $sumSollPlanTagArray[$timeID][$k]+=$value;
		}
		foreach ($zuBearbeitenArray[$timeID][$kunde] as $k=>$value){
		    $sumZuBearbeitenArray[$timeID][$k]+=$value;
		}
		if(is_array($istTagS610Array[$timeID][$kunde])){
		    foreach ($istTagS610Array[$timeID][$kunde] as $k=>$value){
			$sumIstTagS610Array[$timeID][$k]+=$value;
		    }
		}
		
	    }
	    
//	    AplDB::varDump($sumSollPlanTagArray);
//	    AplDB::varDump($sumZuBearbeitenArray);
//	    AplDB::varDump($sumIstTagS610Array);
//	    
	    $time = strtotime("+1 day", $time);
	}
    }
}

$statnrA = array(
    "S0011"=>array("width"=>0),
    "S0041"=>array("width"=>0),
    "S0051"=>array("width"=>0),
    "S0061"=>array("width"=>0),
    "S0081"=>array("width"=>0),
    "sum"=>array("width"=>0),
    );

$statnrColumns = array(
    "Stat"=>array("width"=>13,"planindex"=>"vzkdplan"),
    "VzKdSoll"=>array("width"=>13,"planindex"=>"vzkdplan"),
    "VzkdRest"=>array("width"=>13,"planindex"=>"solltag"),
    "S610"=>array("width"=>13,"planindex"=>"solltag"),
    );


function pageHeader($pdf,$rowHeight,$kdArray){
    global $datumColumnWidth;
    global $planyArray;
    global $statnrColumns;
    
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->SetFillColor(255,255,200);
    $pdf->Cell($datumColumnWidth, $rowHeight, 'Dat/StatNr', 'B', 0, 'L', 1);
    
    // pro sumy za vybrane zakazniky
    $pdf->Cell($statnrColumns['VzkdRest']['width'], $rowHeight, 'VzKdRest', 'LRB', 0, 'R', 1);
    $pdf->Cell($statnrColumns['VzKdSoll']['width'], $rowHeight, 'VzKdSoll', 'LRB', 0, 'R', 1);
    $pdf->Cell($statnrColumns['S610']['width'], $rowHeight, 'VzKdIst', 'LRB', 0, 'R', 1);
	
    foreach ($kdArray as $kd){
	$pdf->Cell($statnrColumns['VzkdRest']['width'], $rowHeight, 'VzKdRest', 'LRB', 0, 'R', 1);
	$pdf->Cell($statnrColumns['VzKdSoll']['width'], $rowHeight, 'VzKdSoll', 'LRB', 0, 'R', 1);
	$pdf->Cell($statnrColumns['S610']['width'], $rowHeight, 'VzKdIst', 'LRB', 0, 'R', 1);
    }
    $pdf->Ln();
}

function test_pageoverflow_nopage($pdfobjekt,$testvysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		return TRUE;
	}
	else
		return FALSE;
}
	

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf_new//tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$aktDatum = date('Y-m-d H:i:s');
$params = "(Kunde $kd_von - $kd_bis, Datum ".$_GET['von']." - ".$_GET['bis'].", VzKdRest mit RM bis $rmBisFormat)";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S217 Dispo - Plan ", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));
$pdf->setLanguageArray($l); //set language items
//initialize document
//$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);
$pdf->AddPage();


$rowHeight = 3;
$datumColumnWidth = $statnrColumns['Stat']['width'];

pageHeader($pdf, 5, $planyArray);

if (($timeVon > 0) && ($timeBis >= $timeVon)) {
    $time = $timeVon;
    while ($time <= $timeBis) {
	//datum
	$timeID = date('Ymd',$time);
	
	$dayClass = date('D', $time);
	$todayTime = strtotime($apl->make_DB_datum(date('d.m.Y')));
	if ($time == $todayTime)
	    $bToday = TRUE;
	else
	    $bToday = FALSE;

	if($time<$todayTime){
	    $bMinulost = TRUE;
	}
	else{
	    $bMinulost = FALSE;
	}
	
	if(test_pageoverflow_nopage($pdf, $rowHeight*(3+count($statnrArray)))){
	    $pdf->AddPage();
	    pageHeader($pdf, 5, $planyArray);
	}
	//cislo zakaznika + exportinfo
	// sloupec s datumem
	if($bToday){
	    $pdf->SetFillColor(255,255,230);
	}
	else if($dayClass=='Sat'){
	    $pdf->SetFillColor(200,200,200);
	}
	else if($dayClass=="Sun"){
	    $pdf->SetFillColor(180,180,180);
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	}
//	$pdf->Cell($datumColumnWidth, 3*$rowHeight, date('d.m.',$time), 'LRBT', 0, 'C', 1);
	$pdf->Cell($datumColumnWidth, $rowHeight, date('d.m.',$time), 'LRT', 0, 'C', 1);
	

	// hlavicka pro sumy pro vybrane zakazniy
	$pdf->Cell($statnrColumns['VzKdSoll']['width']
			+$statnrColumns['VzkdRest']['width']
			+$statnrColumns['S610']['width']
			,$rowHeight, "", 'LRT', 0, 'C', 1);

	//hlavicka pro cislo zakaznika
	foreach ($planyArray as $plan){
	    $kunde = $plan['kunde'];
	    $pdf->Cell($statnrColumns['VzKdSoll']['width']
			+$statnrColumns['VzkdRest']['width']
			+$statnrColumns['S610']['width']
			,$rowHeight, "$kunde", 'LRT', 0, 'C', 1);
	}
	$pdf->Ln();
	
	//prostor pro datum
	
	$dayNumber = date('N',$time);
	$denNazev = $dnyvTydnu[$dayNumber-1];
	$pdf->Cell($datumColumnWidth, $rowHeight, "", 'LR', 0, 'C', 1);
	//suma pro vybrane zakazniky
	$pdf->Cell($statnrColumns['VzKdSoll']['width']
			+$statnrColumns['VzkdRest']['width']
			+$statnrColumns['S610']['width']
			,$rowHeight, "SUM $kd_von - $kd_bis", 'LR', 0, 'C', 1);

	foreach ($planyArray as $plan){
	    $kunde = $plan['kunde'];
	    $exporteInfo="";
	    $exporteArray = $apl->getExporteVzkdDatumKunde($kunde,$time);
	    if($exporteArray!==NULL){
		$exMin = 0;
		$exNrArray = array();
		foreach ($exporteArray as $ex){
		    array_push($exNrArray, substr($ex['auftragsnr'], 3));
		}
		$exCelkem = count($exNrArray);
		$append="";
		if($exCelkem>4){
		    $exNrArray = array_slice($exNrArray, 0, 4);
		    $append = "(+".($exCelkem-4).")";
		}
		$exporteInfo = "".  join(',', $exNrArray)."$append ";
//		$exporteInfo = "".  join(',', $exNrArray)." ";
	    }
	    
	    if(strlen($exporteInfo)>0){
		$pdf->SetFillColor(230,255,230);
	    }
	    else{
		if($bToday){
		    $pdf->SetFillColor(255,255,230);
		}   
		else if($dayClass=='Sat'){
		    $pdf->SetFillColor(200,200,200);
		}
		else if($dayClass=="Sun"){
		    $pdf->SetFillColor(180,180,180);
		}
		else{
		    $pdf->SetFillColor(255,255,255);
		}
	    }
	    $pdf->Cell($statnrColumns['VzKdSoll']['width']
			+$statnrColumns['VzkdRest']['width']
			+$statnrColumns['S610']['width']
			,$rowHeight, "$exporteInfo", 'LR', 0, 'L', 1);
	}
	$pdf->Ln();
	
	//stat + importinfo
	// prostor pro datum
	if($bToday){
	    $pdf->SetFillColor(255,255,230);
	}
	else if($dayClass=='Sat'){
	    $pdf->SetFillColor(200,200,200);
	}
	else if($dayClass=="Sun"){
	    $pdf->SetFillColor(180,180,180);
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	}

	$pdf->Cell($datumColumnWidth, $rowHeight, "$denNazev", 'LR', 0, 'C', 1);
	//suma pro vybrane zakazniky
	$pdf->Cell($statnrColumns['VzKdSoll']['width']
			+$statnrColumns['VzkdRest']['width']
			+$statnrColumns['S610']['width']
			,$rowHeight, "", 'LR', 0, 'L', 1);

	foreach ($planyArray as $plan){
	    $kunde = $plan['kunde'];
	    $importeArray = $apl->getImporteVzkdDatumKunde($kunde,$time);
	    $importeInfo="";
	    $importeMin = "";
	    if($importeArray!==NULL){
		$imMin = 0;
		$imNrArray = array();
		foreach ($importeArray as $im){
		    $imMin += intval($im['vzkd']);
		    array_push($imNrArray, substr($im['auftragsnr'], 3));
		}
		$imCelkem = count($imNrArray);
		$append="";
		if($imCelkem>3){
		    $imNrArray = array_slice($imNrArray, 0, 3);
		    $append = "(+".($imCelkem-3).")";
		}
		$imNrs = "(".  join(',', $imNrArray).") ";
		$imMins = number_format($imMin,0,',',' ');
		$importeInfo = "".  join(',', $imNrArray)."$append ";
		$importeMin = number_format($imMin,0,',',' ')."";
	    }

	    if(strlen($importeInfo)>0){
		$pdf->SetFillColor(255,230,230);
	    }
	    else{
		if($bToday){
		    $pdf->SetFillColor(255,255,230);
		}
		else if($dayClass=='Sat'){
		    $pdf->SetFillColor(200,200,200);
		}
		else if($dayClass=="Sun"){
		    $pdf->SetFillColor(180,180,180);
		}
		else{
		    $pdf->SetFillColor(255,255,255);
		}
	    }
	    $pdf->Cell($statnrColumns['VzKdSoll']['width']
			+$statnrColumns['VzkdRest']['width']+5
			,$rowHeight, "$importeInfo", 'L', 0, 'L', 1);
    	    $pdf->Cell($statnrColumns['S610']['width']-5
			,$rowHeight, "$importeMin", 'R', 0, 'R', 1);

	}
	$pdf->Ln();

	if($bToday){
	    $pdf->SetFillColor(255,255,230);
	}
	else if($dayClass=='Sat'){
	    $pdf->SetFillColor(200,200,200);
	}
	else if($dayClass=="Sun"){
	    $pdf->SetFillColor(180,180,180);
	}
	else{
	    $pdf->SetFillColor(255,255,255);
	}

	foreach ($statnrArray as $statnr){
	    $pdf->Cell($datumColumnWidth, $rowHeight, $statnr, '1', 0, 'L', 1);
	    //sumy pro vybrane zakazniky
	    $obsah = !$bMinulost?number_format($sumZuBearbeitenArray[$timeID]["sum_vzkd_".$statnr], 0,',',' '):"";
	    $pdf->Cell($statnrColumns['VzkdRest']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	    $obsah = number_format($sumSollPlanTagArray[$timeID][$statnr], 0,',',' ');
	    $pdf->Cell($statnrColumns['VzKdSoll']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	    $obsah = number_format($sumIstTagS610Array[$timeID]["sum_vzkd_".$statnr], 0,',',' ');
	    $pdf->Cell($statnrColumns['S610']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	    
	    
	    foreach ($planyArray as $plan){
		$kunde = $plan['kunde'];
		$obsah = !$bMinulost?number_format($zuBearbeitenArray[$timeID][$kunde]["sum_vzkd_".$statnr], 0,',',' '):"";
		$pdf->Cell($statnrColumns['VzkdRest']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
		$obsah = number_format($sollPlanTagArray[$timeID][$kunde][$statnr], 0,',',' ');
		$pdf->Cell($statnrColumns['VzKdSoll']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
		$obsah = number_format($istTagS610Array[$timeID][$kunde]["sum_vzkd_".$statnr], 0,',',' ');
		$pdf->Cell($statnrColumns['S610']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	    }
	    $pdf->Ln($rowHeight);
	}
	
	//radek se sumou
	$pdf->SetFillColor(255,255,230);
	$pdf->Cell($datumColumnWidth, $rowHeight, "Sum", 'LRBT', 0, 'L', 1);
	
	//sumy pro vybrane zakazniky
	$obsah = !$bMinulost?number_format($sumZuBearbeitenArray[$timeID]["sum_vzkd"], 0,',',' '):"";
	$pdf->Cell($statnrColumns['VzkdRest']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	$obsah = number_format($sumSollPlanTagArray[$timeID]["sum_vzkd"], 0,',',' ');
	$pdf->Cell($statnrColumns['VzKdSoll']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	$obsah = number_format($sumIstTagS610Array[$timeID]["sum_vzkd"], 0,',',' ');
	$pdf->Cell($statnrColumns['S610']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);

	foreach ($planyArray as $plan){
	    $kunde = $plan['kunde'];
	    $obsah = !$bMinulost?number_format($zuBearbeitenArray[$timeID][$kunde]['sum_vzkd'], 0,',',' '):"";
	    $pdf->Cell($statnrColumns['VzkdRest']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	    $obsah = number_format($sollPlanTagArray[$timeID][$kunde]['sum_vzkd'], 0,',',' ');
	    $pdf->Cell($statnrColumns['VzKdSoll']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	    $obsah = number_format($istTagS610Array[$timeID][$kunde]['sum_vzkd'], 0,',',' ');
	    $pdf->Cell($statnrColumns['S610']['width'], $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
        }
	$pdf->SetFillColor(255,255,255);
        $pdf->Ln();
	
	$pdf->Ln($rowHeight*0.5);
	// move time to the next day
	$time = strtotime("+1 day", $time);	
    }
}
//Close and output PDF document
$pdf->Output();
//============================================================+
// END OF FILE                                                 
//============================================================+
?>
