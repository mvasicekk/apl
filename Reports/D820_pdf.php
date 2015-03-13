<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

// pod pod
$doc_title = "D820";
$doc_subject = "D820 Report";
$doc_keywords = "D820";

// necham si vygenerovat XML
$parameters=$_GET;

$datumvon=make_DB_datum($_GET['datumvon']);
$datumbis=make_DB_datum($_GET['datumbis']);
$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];
$dnyvTydnu = array("Po","Ut","St","Ct","Pa","So","Ne");

$a = AplDB::getInstance();

// nechci zobrazit parametry
// vynuluju promennou $params
$params="";

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2
				

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
		return TRUE;
	}
	return FALSE;
}



$kundenNrArray = array();
$importeDatumArray = array();
$importeDatumArrayDB = $a->getImporteDatumKunde($kundevon,$kundebis,$datumvon,$datumbis);
if($importeDatumArrayDB!==NULL){
    foreach ($importeDatumArrayDB as $imRow){
	$importDatum = $imRow['import_datum'];
	$kunde = $imRow['kunde'];
	$kundenNrArray[$kunde]+=1;
	if(!is_array($importeDatumArray[$importDatum][$kunde])){
	    $importeDatumArray[$importDatum][$kunde] = array();
	}
	array_push($importeDatumArray[$importDatum][$kunde]
			,array(
			    'kunde'=>$imRow['kunde'],
			    'import'=>$imRow['import'],
			    'bestellnr'=>$imRow['bestellnr'],
			    'im_soll_datum'=>$imRow['im_soll_datum'],
			    'im_soll_time'=>$imRow['im_soll_time'],
			    'vzkdsoll_import'=>$a->getVzKdSollImport($imRow['import']),
			    'draggable'=>$draggable,
			)
		);
    }
}
	
//AplDB::varDump($importeDatumArray);

$exporteDatumArray = array();
$exporteDatumArrayDB = $a->getExporteDatumKunde($kundevon,$kundebis,$datumvon,$datumbis);
if($exporteDatumArrayDB!==NULL){
    foreach ($exporteDatumArrayDB as $exRow){
	$exportDatum = $exRow['export_datum'];
	$kunde = $exRow['kunde'];
	$kundenNrArray[$kunde]+=1;
	if(!is_array($exporteDatumArray[$exportDatum][$kunde])){
	    $exporteDatumArray[$exportDatum][$kunde] = array();
	}
	$vzkdRest = $a->getRestVzkdForEx($exRow['export']);
	array_push($exporteDatumArray[$exportDatum][$kunde]
			,array(
			    'kunde'=>$exRow['kunde'],
			    'vzkdrest'=>$vzkdRest,
			    'export'=>$exRow['export'],
			    'auslief'=>$exRow['ausliefer_datum'],
			    'fertig'=>$exRow['fertig'],
			    'draggable'=>$draggable,
			    'zielort'=>$exRow['zielort'],
			    'exporttime'=>$exRow['export_time']
			)
		);
    }
}

/**
 * 
 * @param type $datumArray
 * @param type $datum
 */
function getMaxImEx($datumImArray,$datumExArray,$kundenArray){
    $maximum = 1;
    foreach ($kundenArray as $kunde=>$value){
	$countIm = count($datumImArray[$kunde]);
	$countEx = count($datumExArray[$kunde]);
	$countSum = $countEx+$countIm;
	if($countSum>$maximum)
	    $maximum = $countSum;
    }
    return $maximum;
}

/**
 * 
 * @param TCPDF $pdf
 * @param type $datumWidth
 * @param type $headerHeight
 * @param type $kundeNrArray
 */
function pageHeader($pdf,$datumWidth,$headerHeight,$kundeNrArray){
    //#D2F85B
    $pdf->SetFillColor(210, 248, 91);
    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->Cell($datumWidth, $headerHeight, 'Datum', 'LRBT', 0, 'L', 1);
    $pocetZakazniku = count($kundeNrArray);
    $kundeWidth = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$datumWidth)/$pocetZakazniku;
    
    foreach ($kundeNrArray as $kunde=>$count){
	$pdf->Cell($kundeWidth, $headerHeight, $kunde, 'LRBT', 0, 'C', 1);
    }
    $pdf->Ln($headerHeight);
}

ksort($kundenNrArray);
//AplDB::varDump($kundenNrArray);
$pocetZakazniku = count($kundenNrArray);
	
//require_once('../tcpdf/config/lang/eng.php');
//require_once('../tcpdf/tcpdf.php');

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params = "Kunde $kundevon - $kundebis, Datum ".$_GET['datumvon']."-".$_GET['datumbis'];
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D820 Rundlauf - Kalendar", $params);
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
$pdf->SetLineWidth(0.1);
// prvni stranka
$pdf->AddPage();


$datumWidth = 25;
$pocetDnu = (strtotime($datumbis)-strtotime($datumvon))/(60*60*24);
$vonTime = strtotime($datumvon);
//AplDB::varDump($pocetDnu);
$headerHeight = 5;
$imExLineHeight=5;
$kundeWidth = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$datumWidth)/$pocetZakazniku;
//hlavicka
pageHeader($pdf, $datumWidth, $headerHeight, $kundenNrArray);
$den = 0;
$aktualDatumIndex = date('Y-m-d');
while ($den<=$pocetDnu){
    
    $datumindex = date('Y-m-d',$vonTime);
    $cisloDne = date('N',$vonTime);
    $aktDatum = date('d.m.',$vonTime)." ".$dnyvTydnu[$cisloDne-1];
    if($cisloDne>=6){
	$bSoNe = TRUE;
    }
    else{
	$bSoNe = FALSE;
    }
    if($datumindex==$aktualDatumIndex){
	$bAktual = TRUE;
    }
    else{
	$bAktual = FALSE;
    }
    $imMax = getMaxImEx($importeDatumArray[$datumindex],$exporteDatumArray[$datumindex],$kundenNrArray);
    //oblast pro im nebo ex bude 2 radkova
    $imMax = 2*$imMax;
    $height=$imMax*$imExLineHeight;
    if(test_pageoverflow_noheader($pdf, $height)){
	pageHeader($pdf, $datumWidth, $headerHeight, $kundenNrArray);
    }
    
    
    if($bAktual){
	$pdf->SetFillColor(255,255,230);
    }
    else{
	$pdf->SetFillColor(255,255,255);
	if($bSoNe){
	    $pdf->SetFillColor(230,230,230);
	}
    }
    
    $pdf->SetFont("FreeSans", "", 8);
    		$pdf->MultiCell(
			$datumWidth, 
			$height, 
			$aktDatum, 
			'LRBT', 
			'C', 
			1, 
			0,
			'',
			'',
			TRUE,
			0,
			FALSE,
			FALSE,
			$height,
			'M'
			);

//    $pdf->Cell($datumWidth, $height, $aktDatum.'-'.$imMax, 'LRBT', 0, 'L', 0);
    $xLeft = $pdf->GetX();
    $yTop = $pdf->GetY();
    $pdf->SetFont("FreeSans", "", 6.5);
    //vypis pro zakazniky
    foreach ($kundenNrArray as $kunde=>$value){
	
	$pdf->SetY($yTop);
	$vypsanoRadku = 0;
	//importy
	if(is_array($importeDatumArray[$datumindex][$kunde])){
	    foreach ($importeDatumArray[$datumindex][$kunde] as $i=>$imArray){
		$pdf->SetX($xLeft);
		$pdf->SetFillColor(255, 230, 230);
		$pdf->MultiCell(
			$kundeWidth, 
			2*$imExLineHeight, 
			"Im".$imArray['import']."/".$imArray['im_soll_time']."\n".$imArray['bestellnr'], 
			'LRBT', 
			'L', 
			1, 
			1,
			'',
			'',
			TRUE,
			0,
			FALSE,
			FALSE,
			2*$imExLineHeight,
			'M'
			);
		$vypsanoRadku+=2;
	    }
	}
	
	//exporty
	if(is_array($exporteDatumArray[$datumindex][$kunde])){
	    foreach ($exporteDatumArray[$datumindex][$kunde] as $i=>$exArray){
		$pdf->SetX($xLeft);
		$pdf->SetFillColor(230, 255, 230);
		$zielort = substr($exArray['zielort'], 0, 15);
		$pdf->MultiCell(
			$kundeWidth, 
			2*$imExLineHeight, 
			"Ex".$exArray['export']."/".$exArray['exporttime']."\n".$zielort, 
			'LRBT', 
			'L', 
			1, 
			1,
			'',
			'',
			TRUE,
			0,
			FALSE,
			FALSE,
			2*$imExLineHeight,
			'M'
			);
//		$pdf->Cell($kundeWidth, 2*$imExLineHeight, "Ex".$exArray['export']."/".$exArray['exporttime'], 'LRBT', 1, 'L', 1);
		$vypsanoRadku+=2;
	    }
	}

	//vypsat zbytek radku do $imMax
	for($i=0;$i<($imMax-$vypsanoRadku);$i++){
	    $pdf->SetX($xLeft);
	    if($bAktual){
		$pdf->SetFillColor(255,255,230);
	    }
	    else{
		$pdf->SetFillColor(255,255,255);
		if($bSoNe){
		    $pdf->SetFillColor(230,230,230);
		}
	    }
	    $t="";
	    if($i==0){
		$t="T";
	    }
	    $pdf->MultiCell(
			$kundeWidth, 
			$imExLineHeight, 
			"", 
			$t.'LR', 
			'L', 
			1, 
			1,
			'',
			'',
			TRUE,
			0,
			FALSE,
			FALSE,
			$imExLineHeight,
			'M'
	    );

//	    $pdf->Cell($kundeWidth, $imExLineHeight, "", $t."LR", 1, 'L', 1);
	}
	$xLeft+=$kundeWidth;
    }
//    $pdf->Ln($height);
    $pdf->SetLineWidth(0.6);
    $pdf->Line(PDF_MARGIN_LEFT, $pdf->GetY(),$pdf->getPageWidth()-PDF_MARGIN_RIGHT,$pdf->GetY());
    $pdf->SetLineWidth(0.1);
    $den++;
    $vonTime+=(60*60*24);
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
