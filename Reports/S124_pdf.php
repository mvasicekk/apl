<?php

require_once '../security.php';
//require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S124";
$doc_subject = "S124 Report";
$doc_keywords = "S124";

// necham si vygenerovat XML
$parameters = $_GET;

// vytahnu paramety z _GET ( z getparameters.php )

$jahr = $_GET['jahr'];
$monat=$_GET['monat'];

$von = $jahr.'-'.$monat.'-01';
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$tagbis = $pocetDnuVMesici;
$bis = $jahr.'-'.$monat.'-'.$tagbis;
$dnyvTydnu = array("Po", "Ut", "St", "Ct", "Pa", "So", "Ne");

$timeArray = array();
for($t = 1;$t<=$pocetDnuVMesici;$t++){
    array_push($timeArray, sprintf("%04d-%02d-%02d",$jahr,$monat,$t));
}

//AplDB::varDump($timeArray);

$a = AplDB::getInstance();

// nechci zobrazit parametry
// vynuluju promennou $params
$params = "";

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2


function test_pageoverflow_noheader($pdfobjekt, $vysradku) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	//$pdfobjekt->AddPage();
	return TRUE;
    }
    return FALSE;
}

//pageheader -------------------------------------------------------------------
function pageHeader($pdf, $rowHeight, $oeWidth, $rowLegendWidth, $tagWidth, $rowSumWidth) {
    global $timeArray;
    global $jahr;
    global $monat;
    $pdf->SetFillColor(255, 255, 230);
    $pdf->Cell($oeWidth, $rowHeight, "OE", 'LRTB', 0, 'C', 1);
    $pdf->Cell($rowLegendWidth, $rowHeight, "$monat/$jahr", 'LRBT', 0, 'L', 1);
    foreach ($timeArray as $t) {
	$pdf->SetFont("FreeSans", "", 5.5);
	$pdf->Cell($tagWidth, $rowHeight, substr($t, 8), 'LRBT', 0, 'R', 1);
    }
    $pdf->Cell($rowSumWidth, $rowHeight, "Sum", 'LRBT', 0, 'R', 1);
    $pdf->Ln();
    $pdf->SetFont("FreeSans", "", 8);
}

//------------------------------------------------------------------------------

//------------------------------------------------------------------------------

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('L', 'mm', 'A4', 1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params = "Jahr: $jahr, Monat: $monat";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S124 - VzKd Soll x VzKd Ist", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-10, PDF_MARGIN_TOP - 10, PDF_MARGIN_RIGHT-10);
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
$pdf->AddPage();

$summenArray = array();

//vzkdSoll
$sql = "";
$sql.=" select ";
$sql.="     doe.oe,";
$sql.="     doe.farbe_rgb,";
$sql.="     dzeitsoll.datum,";
$sql.="     sum(dzeitsoll.stunden_vzkd*60) as vzkd_soll";
$sql.=" from dzeitsoll";
$sql.=" join dtattypen on dtattypen.tat=dzeitsoll.oe";
$sql.=" join doe on doe.oe=dtattypen.oe";
$sql.=" where";
$sql.="     doe.oe<='G62'";
$sql.="     and ";
$sql.="     doe.oe like 'G%'";
$sql.="     and";
$sql.="     dzeitsoll.datum between '$von' and '$bis'";
$sql.=" group by";
$sql.="     doe.oe,";
$sql.="     dzeitsoll.datum";


$sollRows = $a->getQueryRows($sql);
//AplDB::varDump($sollRows);

$sollArray = array();
$oeArray = array();
foreach ($sollRows as $r){
    $oe = $r['oe'];
    $datum = date('Y-m-d',strtotime($r['datum']));
    $vzkdSoll = floatval($r['vzkd_soll']);
    $sollArray[$oe][$datum] = $vzkdSoll;
    $oeArray[$oe] = $r['farbe_rgb'];
    $summenArray["vzkd_soll"][$datum] += $vzkdSoll;
}

ksort($oeArray);

//AplDB::varDump($oeArray);
//AplDB::varDump($sollArray);

//vzkd ist
$sql = "";
$sql.=" select";
$sql.="     dtattypen.oe,";
$sql.="     drueck.oe as oes,";
$sql.="     drueck.Datum as datum,";
$sql.="     sum(if(drueck.auss_typ=4,(drueck.`Stück`+drueck.`Auss-Stück`)*drueck.`VZ-SOLL`,(drueck.`Stück`)*drueck.`VZ-SOLL`)) as vzkd_ist";
$sql.=" from drueck";
$sql.=" join dtattypen on dtattypen.tat=drueck.oe";
$sql.=" where";
$sql.="     drueck.Datum between '$von' and '$bis'";
$sql.="     and dtattypen.oe like 'G%'";
$sql.="     and dtattypen.oe <= 'G62'";
$sql.=" group by";
$sql.="     dtattypen.oe,";
$sql.="     drueck.oe,";
$sql.="     drueck.Datum";

$istRows = $a->getQueryRows($sql);
//AplDB::varDump($sollRows);

$istArray = array();

if ($istRows !== NULL) {
    foreach ($istRows as $r) {
	$oe = $r['oe'];
	$oes = $r['oes'];
	$datum = date('Y-m-d', strtotime($r['datum']));
	$vzkdIst = floatval($r['vzkd_ist']);
	$istArray[$oe][$oes][$datum] = $vzkdIst;
	$istArray[$oe]['sum'][$datum] += $vzkdIst;
	//oe schicht bez cisel
	$oesR = substr($oes, 0, 2);
	$summenArray[$oesR][$datum] += $vzkdIst;
	$summenArray["vzkd_ist"][$datum] += $vzkdIst;
    }
}
else{
    $summenArray["vzkd_ist"] = array();
    foreach ($oeArray as $oe=>$rgb){
	$istArray[$oe]['sum'] = array();
    }
}

foreach ($timeArray as $t){
	$diff = 0;
	$diffPct = 0;
	$soll = 0;
	$ist = 0;
	if(array_key_exists($t, $summenArray['vzkd_soll'])){
	    $soll = $summenArray['vzkd_soll'][$t];
	}
	if(array_key_exists($t, $summenArray['vzkd_ist'])){
	    $ist = $summenArray['vzkd_ist'][$t];
	}
	$diff = $ist-$soll;
	$diffPct = $soll!=0?$diff/$soll*100:0;
	$summenArray['diff'][$t] = $diff;
	$summenArray['diffPct'][$t] = $diffPct;
}

//AplDB::varDump($summenArray);

//AplDB::varDump($istArray);
$oeWidth = 10;
$rowHeight = 5;
$rowLegendWidth = 15;
$rowSumWidth = 10;

$tagWidth = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$oeWidth-$rowLegendWidth-$rowSumWidth+20)/count($timeArray);

pageHeader($pdf,$rowHeight,$oeWidth,$rowLegendWidth,$tagWidth,$rowSumWidth);

//celkove sumy -----------------------------------------------------------------
// vzkd soll
$pdf->setTextColor(0, 0, 0);
$pdf->SetFont("FreeSans", "", 8);
if (test_pageoverflow_noheader($pdf, $rowHeight)) {
    pageHeader($pdf, $rowHeight, $oeWidth, $rowLegendWidth, $tagWidth, $rowSumWidth);
}
$pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
$pdf->Cell($oeWidth, $rowHeight, "", 'LRT', 0, 'L', 0);
$pdf->Cell($rowLegendWidth, $rowHeight, "VzKd soll", 'LRBT', 0, 'L', 0);
$rowSum = 0;
foreach ($timeArray as $t) {
    $obsah = '';
    if (array_key_exists($t, $summenArray['vzkd_soll'])) {
	$rowSum += $summenArray['vzkd_soll'][$t];
	$obsah = number_format($summenArray['vzkd_soll'][$t], 0, ',', ' ');
    }
    $pdf->SetFont("FreeSans", "", 5.5);
    $pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
}
$obsah = number_format($rowSum, 0, ',', ' ');
//zapamatovat sumu
$sollSum = $rowSum;
$pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
$pdf->Ln();
// vzkd ist
$pdf->setTextColor(0, 0, 0);
$pdf->SetFont("FreeSans", "", 8);
if (test_pageoverflow_noheader($pdf, $rowHeight)) {
    pageHeader($pdf, $rowHeight, $oeWidth, $rowLegendWidth, $tagWidth, $rowSumWidth);
}
$pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
$pdf->Cell($oeWidth, $rowHeight, "", 'LR', 0, 'L', 0);
$pdf->Cell($rowLegendWidth, $rowHeight, "VzKd ist", 'LRBT', 0, 'L', 0);
$rowSum = 0;
foreach ($timeArray as $t) {
    $obsah = '';
    if (array_key_exists($t, $summenArray['vzkd_ist'])) {
	$rowSum += $summenArray['vzkd_ist'][$t];
	$obsah = number_format($summenArray['vzkd_ist'][$t], 0, ',', ' ');
    }
    $pdf->SetFont("FreeSans", "", 5.5);
    $pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
}
$obsah = number_format($rowSum, 0, ',', ' ');
$pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
$pdf->Ln();

// diff
$pdf->setTextColor(0, 0, 0);
$pdf->SetFont("FreeSans", "", 8);
if (test_pageoverflow_noheader($pdf, $rowHeight)) {
    pageHeader($pdf, $rowHeight, $oeWidth, $rowLegendWidth, $tagWidth, $rowSumWidth);
}
$pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
$pdf->Cell($oeWidth, $rowHeight, "", 'LR', 0, 'C', 0);
$pdf->Cell($rowLegendWidth, $rowHeight, "Diff", 'LRBT', 0, 'L', 0);
$rowSum = 0;
foreach ($timeArray as $t) {
    $soll = 0;
    $ist = 0;
    $obsah = '';
    if (array_key_exists($t, $summenArray['diff'])) {
	$diff = $summenArray['diff'][$t];
    }
    $rowSum += $diff;
    if ($diff < 0) {
	$pdf->setTextColor(255, 0, 0);
    } else {
	$pdf->setTextColor(0, 0, 0);
    }
    $obsah = number_format($diff, 0, ',', ' ');
    $pdf->SetFont("FreeSans", "", 5.5);
    $pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
    $pdf->setTextColor(0, 0, 0);
}
$obsah = number_format($rowSum, 0, ',', ' ');
if ($rowSum < 0) {
    $pdf->setTextColor(255, 0, 0);
} else {
    $pdf->setTextColor(0, 0, 0);
}
$diffSum = $rowSum;
$pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
$pdf->Ln();
// diffPct
$pdf->setTextColor(0, 0, 0);
$pdf->SetFont("FreeSans", "", 8);
if (test_pageoverflow_noheader($pdf, $rowHeight)) {
    pageHeader($pdf, $rowHeight, $oeWidth, $rowLegendWidth, $tagWidth, $rowSumWidth);
}
$pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
$pdf->Cell($oeWidth, $rowHeight, "Sum", 'LR', 0, 'C', 0);
$pdf->Cell($rowLegendWidth, $rowHeight, "Diff %", 'LRBT', 0, 'L', 0);
$rowSum = 0;
foreach ($timeArray as $t) {
    $soll = 0;
    $ist = 0;
    $obsah = '';
    if (array_key_exists($t, $summenArray['diffPct'])) {
	$diff = $summenArray['diffPct'][$t];
    }
    $rowSum += $diff;
    if ($diff < 0) {
	$pdf->setTextColor(255, 0, 0);
    } else {
	$pdf->setTextColor(0, 0, 0);
    }
    $obsah = number_format($diff, 1, ',', ' ');
    $pdf->SetFont("FreeSans", "", 5.5);
    $pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
    $pdf->setTextColor(0, 0, 0);
}
$rowSum = $sollSum!=0?$diffSum/$sollSum*100:0;
$obsah = number_format($rowSum, 1, ',', ' ');
if ($rowSum < 0) {
    $pdf->setTextColor(255, 0, 0);
} else {
    $pdf->setTextColor(0, 0, 0);
}
$pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
$pdf->Ln();

// podrobnosti s ist
foreach ($summenArray as $oes => $v) {
    if($oes=="vzkd_soll" || $oes=="vzkd_ist" || $oes=="diff" || $oes=="diffPct"){
	continue;
    }
    $pdf->setTextColor(0, 0, 0);
    $pdf->SetFont("FreeSans", "", 8);
    if (test_pageoverflow_noheader($pdf, $rowHeight)) {
	pageHeader($pdf, $rowHeight, $oeWidth, $rowLegendWidth, $tagWidth, $rowSumWidth);
    }
    $pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
    $pdf->Cell($oeWidth, $rowHeight, "", 'LR', 0, 'C', 0);
    $pdf->Cell($rowLegendWidth, $rowHeight, "$oes", 'LRBT', 0, 'L', 0);
    $rowSum = 0;
    foreach ($timeArray as $t) {
	$soll = 0;
	$ist = 0;
	$obsah = '';
	$diff = 0;
	if (array_key_exists($t, $summenArray[$oes])) {
	    $diff = $summenArray[$oes][$t];
	}
	$rowSum += $diff;
	
	$obsah = number_format($diff, 0, ',', ' ');
	$pdf->SetFont("FreeSans", "", 5.5);
	$pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
	$pdf->setTextColor(0, 0, 0);
    }
    $obsah = number_format($rowSum, 0, ',', ' ');
    $pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

//------------------------------------------------------------------------------
//jedu podle oe
foreach ($oeArray as $oe=>$rgbString){
    $rgbA = split(",", $rgbString);
    
//    $pdf->Cell(0, $rowHeight, $oe, 'LRBT', 0, 'L', 1);
//    $pdf->Ln();
    
    // vzkd soll
    $pdf->setTextColor(0, 0, 0);
    $pdf->SetFont("FreeSans", "", 8);
    
    
    $pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
    $pdf->Cell($oeWidth, $rowHeight, "", 'LRT', 0, 'L', 1);
    $pdf->Cell($rowLegendWidth, $rowHeight, "VzKd soll", 'LRBT', 0, 'L', 1);
    $rowSum = 0;
    foreach ($timeArray as $t){
	$obsah = '';
	if(array_key_exists($t, $sollArray[$oe])){
	    $rowSum += $sollArray[$oe][$t];
	    $obsah = number_format($sollArray[$oe][$t],0,',',' ');
	}
	$pdf->SetFont("FreeSans", "", 5.5);
	$pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    }
    $obsah = number_format($rowSum,0,',',' ');
    $pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $pdf->Ln();
    
    // vzkd ist
    $pdf->setTextColor(0, 0, 0);
    $pdf->SetFont("FreeSans", "", 8);
    if(test_pageoverflow_noheader($pdf, $rowHeight)){
	pageHeader($pdf,$rowHeight,$oeWidth,$rowLegendWidth,$tagWidth,$rowSumWidth);
    }
    $pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
    $pdf->Cell($oeWidth, $rowHeight, "", 'LR', 0, 'L', 1);
    $pdf->Cell($rowLegendWidth, $rowHeight, "VzKd ist", 'LRBT', 0, 'L', 1);
    $rowSum = 0;
    foreach ($timeArray as $t){
	$obsah = '';
	if(array_key_exists($t, $istArray[$oe]['sum'])){
	    $rowSum += $istArray[$oe]['sum'][$t];
	    $obsah = number_format($istArray[$oe]['sum'][$t],0,',',' ');
	}
	$pdf->SetFont("FreeSans", "", 5.5);
	$pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    }
    $obsah = number_format($rowSum,0,',',' ');
    $pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $pdf->Ln();
    
    // diff
    $pdf->setTextColor(0, 0, 0);
    $pdf->SetFont("FreeSans", "", 8);
    if(test_pageoverflow_noheader($pdf, $rowHeight)){
	pageHeader($pdf,$rowHeight,$oeWidth,$rowLegendWidth,$tagWidth,$rowSumWidth);
    }
    $pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
    $pdf->Cell($oeWidth, $rowHeight, "$oe", 'LR', 0, 'C', 1);
    $pdf->Cell($rowLegendWidth, $rowHeight, "Diff $oe", 'LRBT', 0, 'L', 1);
    $rowSum = 0;
    foreach ($timeArray as $t){
	$soll = 0;
	$ist = 0;
	$obsah = '';
	if(array_key_exists($t, $istArray[$oe]['sum'])){
	    $ist = $istArray[$oe]['sum'][$t];
	}
	if(array_key_exists($t, $sollArray[$oe])){
	    $soll = $sollArray[$oe][$t];
	}
	$diff = $ist-$soll;
	$rowSum += $diff;
	if($diff<0){
	    $pdf->setTextColor(255,0,0);
	}
	else{
	    $pdf->setTextColor(0,0,0);
	}
	$obsah = number_format($diff,0,',',' ');
	$pdf->SetFont("FreeSans", "", 5.5);
	$pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
	$pdf->setTextColor(0,0,0);
    }
    $obsah = number_format($rowSum,0,',',' ');
    if ($rowSum < 0) {
	$pdf->setTextColor(255, 0, 0);
    } else {
	$pdf->setTextColor(0, 0, 0);
    }
    $pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $pdf->Ln();
    
    //podrobnosti k vzkd ist
    foreach ($istArray[$oe] as $oes => $v) {
	if($oes=="sum"){
	    // to me nezajima
	    continue;
	}
	$pdf->SetFont("FreeSans", "", 8);
	if(test_pageoverflow_noheader($pdf, $rowHeight)){
	    pageHeader($pdf,$rowHeight,$oeWidth,$rowLegendWidth,$tagWidth,$rowSumWidth);
	}
	$pdf->setTextColor(0, 0, 0);
	$pdf->SetFillColor($rgbA[0], $rgbA[1], $rgbA[2]);
	$pdf->Cell($oeWidth, $rowHeight, "", 'LR', 0, 'L', 1);
	$pdf->Cell($rowLegendWidth, $rowHeight, "$oes", 'LRBT', 0, 'L', 0);
	$rowSum = 0;
	foreach ($timeArray as $t) {
	    $soll = 0;
	    $ist = 0;
	    $obsah = '';
	    if (array_key_exists($t, $istArray[$oe][$oes])) {
		$ist = $istArray[$oe][$oes][$t];
		$rowSum += $ist;
	    }
	    
	    $obsah = number_format($ist, 0, ',', ' ');
	    $pdf->SetFont("FreeSans", "", 5.5);
	    $pdf->Cell($tagWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
	}
	$obsah = number_format($rowSum,0,',',' ');
	$pdf->Cell($rowSumWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
	$pdf->Ln();
    }
    if(test_pageoverflow_noheader($pdf, 6*$rowHeight)){
	$pdf->AddPage();
	pageHeader($pdf,$rowHeight,$oeWidth,$rowLegendWidth,$tagWidth,$rowSumWidth);
    }
}
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+