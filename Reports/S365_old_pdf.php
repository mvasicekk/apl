<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S365";
$doc_subject = "S365 Report";
$doc_keywords = "S365";

// necham si vygenerovat XML
$parameters=$_GET;

$von=make_DB_datum($_GET['von']);
$bis=make_DB_datum($_GET['bis']);
$kdvon = $_GET['kdvon'];
$kdbis = $_GET['kdbis'];
$laut = $_GET['laut'];
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
		return TRUE;
	}
	return FALSE;
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

$kundeVon = $kdvon;
$kundeBis = $kdbis;
$datVon = $von;
$datBis = $bis;
$kundenNrArray = array();

//********************************************************************************
//vypocet ppm
$sql.=" select";
$sql.="     dreklamation.kunde,";
$sql.="     YEAR(dreklamation.rekl_datum) as jahr,";
$sql.="     MONTH(dreklamation.rekl_datum) as monat,";
$sql.="     WEEKOFYEAR(dreklamation.rekl_datum) as kw,";
$sql.="     sum(dreklamation.anerkannt_stk_ausschuss+dreklamation.anerkannt_stk_nacharbeit) as stk_all,";
$sql.="     sum(if(dreklamation.ppm<>0,dreklamation.anerkannt_stk_ausschuss+dreklamation.anerkannt_stk_nacharbeit,0)) as stk_ppm";
$sql.=" from dreklamation";
$sql.=" where";
$sql.="     dreklamation.rekl_nr like 'E%'";
$sql.="     and dreklamation.kunde between '$kundeVon' and '$kundeBis'";
$sql.="     and dreklamation.rekl_datum between '$datVon' and '$datBis'";
$sql.=" group by";
$sql.="     dreklamation.kunde,";
$sql.="     YEAR(dreklamation.rekl_datum),";
$sql.="     MONTH(dreklamation.rekl_datum),";
$sql.="     WEEKOFYEAR(dreklamation.rekl_datum)";

$ppmRows = $a->getQueryRows($sql);

$ppmArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$ppmArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_all'] = $ppm['stk_all'];
	$ppmArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ppm'] = $ppm['stk_ppm'];
	$kundenNrArray[$ppm['kunde']]+=1;
    }
}

$sql =" select";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.Aufdat) as jahr,";
$sql.="     MONTH(daufkopf.Aufdat) as monat,";
$sql.="     WEEKOFYEAR(daufkopf.Aufdat) as kw,";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stück`,0)) as stk_import";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.auftragsnr";
$sql.=" join dkopf on dkopf.Teil=dauftr.teil";
$sql.=" where";
$sql.="     daufkopf.Aufdat between '$datVon' and '$datBis'";
$sql.="     and dkopf.dummy_flag=0";
$sql.="     and dkopf.Gew<>0";
$sql.="     and dkopf.Teilbez not like '%reisla%'";
$sql.="     and daufkopf.kunde between '$kundeVon' and '$kundeBis'";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.Aufdat),";
$sql.="     MONTH(daufkopf.Aufdat),";
$sql.="     WEEKOFYEAR(daufkopf.Aufdat)";

$ppmRows = $a->getQueryRows($sql);

$importArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$importArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_import'] = $ppm['stk_import'];
	$kundenNrArray[$ppm['kunde']]+=1;
    }
}

$sql =" select";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.ausliefer_datum) as jahr,";
$sql.="     MONTH(daufkopf.ausliefer_datum) as monat,";
$sql.="     WEEKOFYEAR(daufkopf.ausliefer_datum) as kw,";
$sql.="     sum(if(dauftr.KzGut='G',dauftr.`stk-exp`,0)) as stk_ex_gut,";
$sql.="     sum(auss2_stk_exp) as stk_ex_auss2,";
$sql.="     sum(auss4_stk_exp) as stk_ex_auss4,";
$sql.="     sum(auss6_stk_exp) as stk_ex_auss6";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
$sql.=" join dkopf on dkopf.Teil=dauftr.teil";
$sql.=" where";
$sql.="     daufkopf.ausliefer_datum between '$datVon' and '$datBis'";
$sql.="     and dkopf.dummy_flag=0";
$sql.="     and dkopf.Gew<>0";
$sql.="     and dkopf.Teilbez not like '%reisla%'";
$sql.="     and daufkopf.kunde between '$kundeVon' and '$kundeBis'";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     YEAR(daufkopf.ausliefer_datum),";
$sql.="     MONTH(daufkopf.ausliefer_datum),";
$sql.="     WEEKOFYEAR(daufkopf.ausliefer_datum)";

$ppmRows = $a->getQueryRows($sql);

$exportArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_gut'] = $ppm['stk_ex_gut'];
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_auss2'] = $ppm['stk_ex_auss2'];
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_auss4'] = $ppm['stk_ex_auss4'];
	$exportArray[$ppm['kunde']]['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['stk_ex_auss6'] = $ppm['stk_ex_auss6'];
	$kundenNrArray[$ppm['kunde']]+=1;
    }
}

$sql =" select";
$sql.="     YEAR(calendar.datum) as jahr,";
$sql.="     MONTH(calendar.datum) as monat,";
$sql.="     WEEKOFYEAR(calendar.datum) as kw";
$sql.=" from calendar";
$sql.=" where";
$sql.="     calendar.datum between '$datVon' and '$datBis'";
$sql.=" group by";
$sql.="     YEAR(calendar.datum),";
$sql.="     MONTH(calendar.datum),";
$sql.="     WEEKOFYEAR(calendar.datum)";
$sql.=" HAVING kw<53";
$sql.=" ORDER BY";
$sql.=" jahr,monat,kw";



$ppmRows = $a->getQueryRows($sql);

$calArray = array();
$jmArray = array();

if($ppmRows!==NULL){
    foreach ($ppmRows as $ppm){
	$calArray['jahre'][$ppm['jahr']]['monate'][$ppm['monat']]['kw'][$ppm['kw']]['letzt_datum_kw'] = $ppm['letzt_datum_kw'];
	$jmArray[$ppm['jahr']."-".$ppm['monat']]++;
    }
}

//AplDB::varDump($ppmArray);
//AplDB::varDump($importArray);
//AplDB::varDump($exportArray);
//AplDB::varDump($calArray);

//vytvorim si pole vsech zakazniku, ktere najdu

//********************************************************************************
ksort($kundenNrArray);
//AplDB::varDump($kundenNrArray);
$pocetZakazniku = count($kundenNrArray);

//zakaznik ma vzdy 3 sloupce, 
//Stk[Im nebo Ex] - odpovida stk_import[pro Import] z importArray nebo stk_ex_gut+stk_ex_auss2+stk_ex_auss4+stk_ex_auss6[pro Export] z exportArray
//Stk reklamiert - odpovida stk_ppm ppmArray
//PPM - stk_ppm s ppmArray



//vytvorim si vysledne pole, ktere budu zobrazovat
$anzeigeArray = array();
$monatAnzeigeArray = array();
$berichtAnzeigeArray = array();
$monatGraphArray = array();
$kdMonatAnzeigeArray = array();

foreach ($calArray['jahre'] as $jahr=>$m){
    foreach ($m['monate'] as $monat=>$k){
	foreach ($k['kw'] as $kw=>$cal){
//	    echo "$jahr - $monat - $kw<br>";
	    //projedu seznam zakazniku
	    foreach ($kundenNrArray as $kd=>$v){
		$stk = $laut=='Import'?$importArray[$kd]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_import']:$exportArray[$kd]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_gut']+$exportArray[$kd]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_auss2']+$exportArray[$kd]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_auss4']+$exportArray[$kd]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ex_auss6'];
		$stk_rekl = $ppmArray[$kd]['jahre'][$jahr]['monate'][$monat]['kw'][$kw]['stk_ppm'];
		$ppm = $stk != 0 ? 1e6 / $stk * $stk_rekl : 0;
		$anzeigeArray[$jahr][$monat][$kw][$kd]['stk'] = $stk;
		$anzeigeArray[$jahr][$monat][$kw][$kd]['stk_rekl'] = $stk_rekl;
		$anzeigeArray[$jahr][$monat][$kw][$kd]['ppm'] = $ppm;
		
		$monatAnzeigeArray[$jahr][$monat][$kd]['stk'] += $stk;
		$monatAnzeigeArray[$jahr][$monat][$kd]['stk_rekl'] += $stk_rekl;
		$monatAnzeigeArray[$jahr][$monat][$kd]['ppm'] = $monatAnzeigeArray[$jahr][$monat][$kd]['stk'] != 0 ? 1e6 / $monatAnzeigeArray[$jahr][$monat][$kd]['stk'] * $monatAnzeigeArray[$jahr][$monat][$kd]['stk_rekl'] : 0;
		
		$kdMonatAnzeigeArray[$kd][$jahr."-".$monat]['ppm'] = $monatAnzeigeArray[$jahr][$monat][$kd]['stk'] != 0 ? 1e6 / $monatAnzeigeArray[$jahr][$monat][$kd]['stk'] * $monatAnzeigeArray[$jahr][$monat][$kd]['stk_rekl'] : 0;
		
		$monatGraphArray[$kd]['stk'][$monat] += $stk;
		$monatGraphArray[$kd]['stk_rekl'][$monat] += $stk_rekl;
		$monatGraphArray[$kd]['ppm'][$monat] = $monatAnzeigeArray[$jahr][$monat][$kd]['ppm'];
		
		$berichtAnzeigeArray[$kd]['stk'] += $stk;
		$berichtAnzeigeArray[$kd]['stk_rekl'] += $stk_rekl;
		$berichtAnzeigeArray[$kd]['ppm'] = $berichtAnzeigeArray[$kd]['stk'] != 0 ? 1e6 / $berichtAnzeigeArray[$kd]['stk'] * $berichtAnzeigeArray[$kd]['stk_rekl'] : 0;
	    }
	}
    }
}

function pageReportHeader($pdf,$s,$kwWidth,$stkWidth,$rowHeight,$kundenNrArray,$kw="KW") {
    $fontSize = $s;
    $a = AplDB::getInstance();
    $pdf->SetFillColor(255, 255, 230);
    $pdf->SetFont("FreeSans", "B", $s);
    $pdf->Cell($kwWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    foreach ($kundenNrArray as $kd => $v) {
	$pdf->Cell($stkWidth, $rowHeight, '', 'LBT', 0, 'R', 1);
	$pdf->Cell($stkWidth, $rowHeight, $kd, 'BT', 0, 'C', 1);
	$pdf->Cell($stkWidth, $rowHeight, '', 'RBT', 0, 'R', 1);
    }
    $pdf->Ln();
    
    //2.radek
    $pdf->Cell($kwWidth, $rowHeight, '', 'LR', 0, 'R', 1);
    foreach ($kundenNrArray as $kd => $v) {
	$maxPPMInfo = $a->getBewertungKriteriumInfo($kd, 'ppm_max', date('y-m'));
	if($maxPPMInfo!==NULL){
	    $maxPPM = $maxPPMInfo[0]['grenze']."/".$maxPPMInfo[0]['interval_monate']." Mnt";
	}
	else{
	    $maxPPM = '?';
	}
	
	$pdf->Cell(1*$stkWidth, $rowHeight, 'Max PPM :', 'LBT', 0, 'L', 1);
	$pdf->Cell(2*$stkWidth, $rowHeight, $maxPPM, 'BRT', 0, 'R', 1);
//	$pdf->Cell($stkWidth, $rowHeight, '', 'RBT', 0, 'R', 1);
    }
    $pdf->Ln();
    //3.radek
    $pdf->Cell($kwWidth, $rowHeight, $kw, 'LRB', 0, 'R', 1);
    foreach ($kundenNrArray as $kd => $v) {
	$pdf->Cell($stkWidth, $rowHeight, 'Stk', 'LRBT', 0, 'R', 1);
	$pdf->Cell($stkWidth, $rowHeight, 'Stk rekl', 'LRBT', 0, 'R', 1);
	$pdf->Cell($stkWidth, $rowHeight, 'ppm', 'LRBT', 0, 'R', 1);
    }
}

//AplDB::varDump($anzeigeArray);

//require_once('../tcpdf/config/lang/eng.php');
//require_once('../tcpdf/tcpdf.php');

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params = "Kunde $kdvon - $kdbis, Datum ".$_GET['von']."-".$_GET['bis'].", lt. ".$laut;
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S365 PPM"." lt. ".$laut, $params);
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

$kwWidth = 10;
$stkWidthMax = 20;
$stkWidthBerechnet = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$kwWidth)/($pocetZakazniku*3);
$stkWidth = $stkWidthBerechnet>$stkWidthMax?$stkWidthMax:$stkWidthBerechnet;
$rowHeight = 4;
$pdf->SetFillColor(255, 255, 230);
//nastavit sirku fontu tak aby se vesel nejdelsi text do bunky
for($s=12;$s>1;$s-=0.5){
    $strWidth = $pdf->GetStringWidth("XXXXXXXX","FreeSans","B",$s);
    if($strWidth<=$stkWidth)	break;
}


$fontSize = $s;

pageReportHeader($pdf, $s, $kwWidth, $stkWidth, $rowHeight, $kundenNrArray);

//$pdf->SetFont("FreeSans", "B", $s);
//$pdf->Cell($kwWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
//foreach ($kundenNrArray as $kd=>$v){
//    $pdf->Cell($stkWidth, $rowHeight, '', 'LBT', 0, 'R', 1);
//    $pdf->Cell($stkWidth, $rowHeight, $kd, 'BT', 0, 'C', 1);
//    $pdf->Cell($stkWidth, $rowHeight, '', 'RBT', 0, 'R', 1);
//}
//$pdf->Ln();
//$pdf->Cell($kwWidth, $rowHeight, 'KW', 'LRB', 0, 'R', 1);
//foreach ($kundenNrArray as $kd=>$v){
//    $pdf->Cell($stkWidth, $rowHeight, 'Stk', 'LRBT', 0, 'R', 1);
//    $pdf->Cell($stkWidth, $rowHeight, 'Stk rekl', 'LRBT', 0, 'R', 1);
//    $pdf->Cell($stkWidth, $rowHeight, 'ppm', 'LRBT', 0, 'R', 1);
//}
$pdf->Ln();

$pdf->SetFont("FreeSans", "", $s);

foreach ($anzeigeArray as $jahr=>$m){
    foreach ($m as $monat=>$k){
	foreach ($k as $kw=>$cal){
	    $pdf->Cell($kwWidth, $rowHeight, $kw, 'LRBT', 0, 'R', 0);
	    //projedu seznam zakazniku
	    foreach ($kundenNrArray as $kd=>$v){
		$obsah = number_format($anzeigeArray[$jahr][$monat][$kw][$kd]['stk'],0,',',' ');
		$pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
		$obsah = number_format($anzeigeArray[$jahr][$monat][$kw][$kd]['stk_rekl'],0,',',' ');
		$pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
		$obsah = number_format($anzeigeArray[$jahr][$monat][$kw][$kd]['ppm'],0,',',' ');
		$pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
	    }
	    $pdf->Ln();
	}
    }
}

//sumy pro bericht
$pdf->SetFont("FreeSans", "B", $fontSize);
$pdf->Cell($kwWidth, $rowHeight, 'Sum', 'LRTB', 0, 'R', 1);
foreach ($kundenNrArray as $kd => $v) {
    $obsah = number_format($berichtAnzeigeArray[$kd]['stk'], 0, ',', ' ');
    $pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $obsah = number_format($berichtAnzeigeArray[$kd]['stk_rekl'], 0, ',', ' ');
    $pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $obsah = number_format($berichtAnzeigeArray[$kd]['ppm'], 0, ',', ' ');
    $pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
}

$pdf->AddPage();
//sumy pro mesice
$pdf->SetFillColor(210, 248, 91);
$pdf->SetFont("FreeSans", "B", 10);
$pdf->Cell(0, 6, 'Monat - Summen', '', 1, 'L', 1);

pageReportHeader($pdf, $fontSize, $kwWidth, $stkWidth, $rowHeight, $kundenNrArray,"Mnt");
/*
$pdf->SetFillColor(255, 255, 230);
$pdf->SetFont("FreeSans", "B", $fontSize);
$pdf->Cell($kwWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
foreach ($kundenNrArray as $kd=>$v){
    $pdf->Cell($stkWidth, $rowHeight, '', 'LBT', 0, 'R', 1);
    $pdf->Cell($stkWidth, $rowHeight, $kd, 'BT', 0, 'C', 1);
    $pdf->Cell($stkWidth, $rowHeight, '', 'RBT', 0, 'R', 1);
}
$pdf->Ln();
$pdf->Cell($kwWidth, $rowHeight, 'Mnt', 'LRB', 0, 'R', 1);
foreach ($kundenNrArray as $kd=>$v){
    $pdf->Cell($stkWidth, $rowHeight, 'Stk', 'LRBT', 0, 'R', 1);
    $pdf->Cell($stkWidth, $rowHeight, 'Stk rekl', 'LRBT', 0, 'R', 1);
    $pdf->Cell($stkWidth, $rowHeight, 'ppm', 'LRBT', 0, 'R', 1);
}
 */
 
$pdf->Ln();
$pdf->SetFont("FreeSans", "", $fontSize);
foreach ($monatAnzeigeArray as $jahr=>$m){
    foreach ($m as $monat=>$k){
	    $pdf->Cell($kwWidth, $rowHeight, $monat, 'LRBT', 0, 'R', 0);
	    //projedu seznam zakazniku
	    foreach ($kundenNrArray as $kd=>$v){
		$obsah = number_format($monatAnzeigeArray[$jahr][$monat][$kd]['stk'],0,',',' ');
		$pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
		$obsah = number_format($monatAnzeigeArray[$jahr][$monat][$kd]['stk_rekl'],0,',',' ');
		$pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
		$obsah = number_format($monatAnzeigeArray[$jahr][$monat][$kd]['ppm'],0,',',' ');
		$pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
	    }
	    $pdf->Ln();
	}
}
//sumy pro bericht
$pdf->SetFont("FreeSans", "B", $fontSize);
$pdf->Cell($kwWidth, $rowHeight, 'Sum', 'LRTB', 0, 'R', 1);
foreach ($kundenNrArray as $kd => $v) {
    $obsah = number_format($berichtAnzeigeArray[$kd]['stk'], 0, ',', ' ');
    $pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $obsah = number_format($berichtAnzeigeArray[$kd]['stk_rekl'], 0, ',', ' ');
    $pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
    $obsah = number_format($berichtAnzeigeArray[$kd]['ppm'], 0, ',', ' ');
    $pdf->Cell($stkWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 1);
}


// mesice ve sloupcich

$pdf->AddPage();

//AplDB::varDump($kdMonatAnzeigeArray);
//AplDB::varDump($jmArray);
$pocetMesicu = count($jmArray);
$kundeWidth = 20;
$ppmWidthMax = 20;
$ppmWidthBerechnet = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$kundeWidth)/($pocetMesicu+1); // +1 pro prumer na zakaznika
$ppmWidth = $ppmWidthBerechnet>$ppmWidthMax?$ppmWidthMax:$ppmWidthBerechnet;
$rowHeight = 4;
//hlavicka
$pdf->SetFont("FreeSans", "B", $fontSize);
$pdf->Cell($kundeWidth, $rowHeight, 'Kunde', 'LRTB', 0, 'R', 1);
foreach ($jmArray as $jm=>$v){
    $obsah = $jm;
    $pdf->Cell($ppmWidth, $rowHeight, $obsah, 'LRBT', 0, 'C', 1);
}
$obsah = 'AVG';
$pdf->Cell($ppmWidth, $rowHeight, $obsah, 'LRBT', 0, 'C', 1);
$pdf->Ln();

$pdf->SetFont("FreeSans", "", $fontSize);
// bunky s daty
foreach ($kdMonatAnzeigeArray as $kd=>$monatArray){
    $pdf->Cell($kundeWidth, $rowHeight, $kd, 'LRTB', 0, 'R', 0);
    foreach ($jmArray as $jm=>$v){
	$obsah = number_format($kdMonatAnzeigeArray[$kd][$jm]["ppm"],0,',',' ');
	$pdf->Cell($ppmWidth, $rowHeight, $obsah, 'LRBT', 0, 'R', 0);
    }
    $pdf->Ln();
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
