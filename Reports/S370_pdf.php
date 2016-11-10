<?php

require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S370";
$doc_subject = "S370 Report";
$doc_keywords = "S370";

// necham si vygenerovat XML
$parameters = $_GET;

// vytahnu paramety z _GET ( z getparameters.php )

$von = make_DB_datum($_GET['von']);
$bis = make_DB_datum($_GET['bis']);
$kdvon = $_GET['kdvon'];
$kdbis = $_GET['kdbis'];
$mitdetail = $_GET['mitdetail'] == 'a' ? TRUE : FALSE;

$dnyvTydnu = array("Po", "Ut", "St", "Ct", "Pa", "So", "Ne");

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

function test_pageoverflow($pdfobjekt, $vysradku, $cellhead) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	pageHeader($pdfobjekt, $cellhead, $vysradku);
	foreach ($detailArray as $jmk => $kundeRow) {
	    $pdf->SetFillColor(192, 192, 192);
	    $pdf->Cell(4 * $stkWidth, $rowHeight, $jmk, 'LRBT', 0, 'L', 1);

	    $pdf->Ln();
	    foreach ($kundeRow as $kd => $detailRow) {
		$pdf->SetFillColor(255, 255, 230);
		$pdf->Cell(4 * $stkWidth, $rowHeight, $kd, 'LRBT', 0, 'L', 1);
		$pdf->Ln();
	    }
	    $pdf->Ln();
	}
	//$pdfobjekt->Ln();
	//$pdfobjekt->Ln();
    }
}

/**
 *
 * @param TCPDF $pdf
 * @param type $datumWidth
 * @param type $headerHeight
 * @param type $kundeNrArray
 */
function pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis) {
    $pdf->SetFillColor(255, 255, 230);
    $pdf->SetFont("FreeSans", "B", $s);
    $pdf->Cell($mntWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    $pdf->Cell($bewWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    $pdf->Cell(2 * $stkWidth, $rowHeight, "Sum $kdvon-$kdbis", 'LRT', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
	$pdf->Cell(2 * $stkWidth, $rowHeight, "$kd", 'LRT', 0, 'C', 1);
    }
    $pdf->Ln();

    $pdf->Cell($mntWidth, $rowHeight, 'Mnt.', 'LRB', 0, 'R', 1);
    $pdf->Cell($bewWidth, $rowHeight, 'Bew.', 'LRB', 0, 'R', 1);
    $pdf->Cell($stkWidth, $rowHeight, "E", 'LRTB', 0, 'C', 1);
    $pdf->Cell($stkWidth, $rowHeight, "I", 'LRTB', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
	$pdf->Cell($stkWidth, $rowHeight, "E", 'LRTB', 0, 'C', 1);
	$pdf->Cell($stkWidth, $rowHeight, "I", 'LRTB', 0, 'C', 1);
    }
    $pdf->Ln();
}

$kundeVon = $kdvon;
$kundeBis = $kdbis;
$datVon = $von;
$datBis = $bis;
$kundenNrArray = array();
$jahrMonatArray = array();
$jahrMonatKwArray = array();
$bewertungArray = array();
$summeKunden = array();
$monatSummen = array();
$monatSummenKunden = array();
$gesamtSummen = array();
$gesamtSummenKunden = array();


// hodnoty pro "citatele" po tydnech -------------------------------------------

$sql.=" select";
$sql.="     daufkopf.kunde as kunde,";
$sql.="     drueck.Datum as datum,";
$sql.="     sum(if(auss_typ=6,`Auss-Stück`,0)) as sum_auss6_stk,";
$sql.="     sum(if(auss_typ=6,`Auss-Stück`*dkopf.Gew,0)) as sum_auss6_kg";
$sql.=" from drueck";
$sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
$sql.=" join dkopf on dkopf.Teil=drueck.Teil";
$sql.=" where";
$sql.="     (datum between '$von' and '$bis')";
$sql.="     and (daufkopf.kunde between '$kdvon' and '$kdbis')";
$sql.="     and (dkopf.dummy_flag=0)";
$sql.="     and (dkopf.Gew<>0)";
$sql.="     and (dkopf.Teilbez not like '%reisla%')";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     drueck.datum";

$aussArray = $a->getQueryRows($sql);


//AplDB::varDump($aussArray);
if ($aussArray !== NULL) {
    foreach ($aussArray as $auss) {
	$jahrMonat = date('Y-m', strtotime($auss['datum']));
	$kw = date('W', strtotime($auss['datum']));
	$kunde = $auss['kunde'];
	$a6Array["$jahrMonat" . "-" . $kw][$kunde]["a6_rm"]["stk"] += intval($auss['sum_auss6_stk']);
	$a6Array["$jahrMonat" . "-" . $kw][$kunde]["a6_rm"]["kg"] += floatval($auss['sum_auss6_kg']);

	$a6ArrayMonatSummen[$jahrMonat][$kunde]["a6_rm"]["stk"] += intval($auss['sum_auss6_stk']);
	$a6ArrayMonatSummen[$jahrMonat][$kunde]["a6_rm"]["kg"] += floatval($auss['sum_auss6_kg']);

	$kundenNrArray[$kunde] += 1;
	$jahrMonatArray[$jahrMonat] += 1;
	$jahrMonatKwArray["$jahrMonat" . "-" . $kw] += 1;
    }
}

//AplDB::varDump($a6ArrayMonatSummen);
//echo "<hr>";
// hodnoty pro "jmenovatele" po tydnech ----------------------------------------
$sql = "";
$sql.=" select";
$sql.="     daufkopf.kunde as kunde,";
$sql.="     daufkopf.ausliefer_datum as datum,";
$sql.="     sum(if(dauftr.KzGut='G',`stk-exp`,0)) as stk_exp_gut,";
$sql.="     sum(dauftr.auss2_stk_exp+dauftr.auss4_stk_exp+dauftr.auss6_stk_exp) as stk_exp_auss,";
$sql.="     sum(if(dauftr.KzGut='G',`stk-exp`*dkopf.Gew,0)) as kg_exp_gut,";
$sql.="     sum((dauftr.auss2_stk_exp+dauftr.auss4_stk_exp+dauftr.auss6_stk_exp)*dkopf.Gew) as kg_exp_auss";
$sql.=" from dauftr";
$sql.=" join daufkopf on daufkopf.auftragsnr=dauftr.`auftragsnr-exp`";
$sql.=" join dkopf on dkopf.Teil=dauftr.Teil";
$sql.=" where";
$sql.="     (ausliefer_datum between '$von' and '$bis')";
$sql.="     and (daufkopf.kunde between '$kdvon' and '$kdbis')";
$sql.="     and (dkopf.dummy_flag=0)";
$sql.="     and (dkopf.Gew<>0)";
$sql.="     and (dkopf.Teilbez not like '%reisla%')";
$sql.=" group by";
$sql.="     daufkopf.kunde,";
$sql.="     daufkopf.ausliefer_datum";


$exp1Array = $a->getQueryRows($sql);

//AplDB::varDump($exp1Array);
if ($exp1Array !== NULL) {
    foreach ($exp1Array as $exp) {
	$jahrMonat = date('Y-m', strtotime($exp['datum']));
	$kw = date('W', strtotime($exp['datum']));
	$kunde = $exp['kunde'];

	$expArray["$jahrMonat" . "-" . $kw][$kunde]["exp_gut"]["stk"] += intval($exp['stk_exp_gut']);
	$expArray["$jahrMonat" . "-" . $kw][$kunde]["exp_gut"]["kg"] += floatval($exp['kg_exp_gut']);
	$expArray["$jahrMonat" . "-" . $kw][$kunde]["exp_auss"]["stk"] += intval($exp['stk_exp_auss']);
	$expArray["$jahrMonat" . "-" . $kw][$kunde]["exp_auss"]["kg"] += floatval($exp['kg_exp_auss']);

	$expArrayMonatSummen[$jahrMonat][$kunde]["exp_gut"]["stk"] += intval($exp['stk_exp_gut']);
	$expArrayMonatSummen[$jahrMonat][$kunde]["exp_gut"]["kg"] += floatval($exp['kg_exp_gut']);
	$expArrayMonatSummen[$jahrMonat][$kunde]["exp_auss"]["stk"] += intval($exp['stk_exp_auss']);
	$expArrayMonatSummen[$jahrMonat][$kunde]["exp_auss"]["kg"] += floatval($exp['kg_exp_auss']);

	$kundenNrArray[$kunde] += 1;
	$jahrMonatArray[$jahrMonat] += 1;
	$jahrMonatKwArray["$jahrMonat" . "-" . $kw] += 1;
    }
}

ksort($kundenNrArray);
ksort($jahrMonatArray, SORT_STRING);
ksort($jahrMonatKwArray, SORT_STRING);

// ext Stk pro vyhodnoceni po mesicich -----------------------------------------
$sql = "";
$sql.=" select ";
$sql.="     dreklamation.rekl_datum as datum,";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.anerkannt_stk_ausschuss";
$sql.=" from dreklamation";
$sql.=" where";
$sql.="     (dreklamation.rekl_datum between '$von' and '$bis')";
$sql.="     and (rekl_nr like 'E%')"; // jen externi reklamace
$sql.="     and (kunde between '$kdvon' and '$kdbis')";

$extStk1Array = $a->getQueryRows($sql);

//AplDB::varDump($extStk1Array);
if ($extStk1Array !== NULL) {
    foreach ($extStk1Array as $es) {
	$jahrMonat = date('Y-m', strtotime($es['datum']));
	$kunde = $es['kunde'];
	$extStkArray[$jahrMonat][$kunde]["extStk"]["stk"] += intval($es['anerkannt_stk_ausschuss']);
    }
}


// v pripade, ze chci detail
//
if ($mitdetail === TRUE) {
    //echo "<hr>mit Detail<hr>";
    $sql = "";
    $sql.=" select";
    $sql.=" YEAR(drueck.Datum) as jahr,";
    $sql.=" MONTH(drueck.Datum) as monat,";
    $sql.=" WEEK(drueck.Datum) as kw,";
    $sql.=" daufkopf.kunde as kunde,";
    $sql.=" drueck.Teil as teil,";
    $sql.=" dkopf.gew as gew,";
    $sql.=" drueck.PersNr as persnr,";
    $sql.=" dpers.regeloe as regeloe,";
    $sql.=" drueck.oe as rmoe,";
    $sql.=" CONCAT(dpers.name,' ',dpers.vorname) as persname,";
    $sql.=" drueck.taetnr as abgnr,";
    $sql.=" sum(if(auss_typ=6,`Auss-Stück`,0)) as sum_auss6";
    $sql.=" from drueck";
    $sql.=" join daufkopf on daufkopf.auftragsnr=drueck.AuftragsNr";
    $sql.=" join dkopf on dkopf.Teil=drueck.Teil";
    $sql.=" join dpers on dpers.persnr=drueck.persnr";
    $sql.=" where";
    $sql.=" datum between '$von' and '$bis'";
    $sql.=" and daufkopf.kunde between '$kdvon' and '$kdbis'";
    $sql.=" group by";
    $sql.=" YEAR(drueck.Datum),";
    $sql.=" MONTH(drueck.Datum),";
    $sql.=" WEEK(drueck.Datum),";
    $sql.=" daufkopf.kunde,";
    $sql.=" drueck.Teil,";
    $sql.=" drueck.PersNr";
    $sql.=" ,drueck.taetnr";
    $sql.=" having sum_auss6<>0";

    //echo "$sql<br>";
    $detail1Array = $a->getQueryRows($sql);

    //AplDB::varDump($reklArray);
    if ($detail1Array !== NULL) {
	foreach ($detail1Array as $d) {
	    $jmk = sprintf("%04d-%02d-%02d", $d['jahr'], $d['monat'], $d['kw']);
	    $kunde = $d['kunde'];
	    if (!is_array($detailArray[$jmk][$kunde])) {
		$detailArray[$jmk][$kunde] = array();
	    }
	    array_push($detailArray[$jmk][$kunde], $d);
	}
    }
    //AplDB::varDump($detailArray);
    //echo "<hr>mit Detail<hr>";
}

//AplDB::varDump($extStkArray);
//AplDB::varDump($expArray);
//AplDB::varDump($jahrMonatKwArray);
//AplDB::varDump($kundenNrArray);

function pageHeaderMain($pdf, $rowHeight, $kundenNrArray, $stkWidth, $pocetZakazniku) {
    global $a;
    $pdf->SetFillColor(255, 255, 230);
    $pdf->Cell(10, $rowHeight, '', 'LRT', 0, 'R', 1);

//zakaznici
// *************************************************************************************************** \\
    foreach ($kundenNrArray as $kd => $v1) {
	$pdf->Cell($stkWidth, $rowHeight, $kd, 'LRTB', 0, 'C', 1);
	//AplDB::varDump($kd);
    }
    $pdf->Cell(10, $rowHeight, '', 'LRT', 0, 'C', 1);
    $pdf->Ln();
// *************************************************************************************************** \\
    $pdf->Cell(10, $rowHeight, '', 'LR', 0, 'R', 1);


// Max BA-Anteil Kd:
// *************************************************************************************************** \\

    $pdf->SetFont("FreeSans", "", 5.5);
    foreach ($kundenNrArray as $kd => $v2) {

	$maxBAInfo = $a->getBewertungKriteriumInfo($kd, 'ba_anteil_max', date('y-m'));
	if ($maxBAInfo !== NULL) {
	    $maxBA = $maxBAInfo[0]['grenze'] . "/" . $maxBAInfo[0]['interval_monate'];
	} else {
	    $maxBA = '?';
	}
	$ba = $a->getKundeBAAnteilStk($kd);
	$pdf->Cell($stkWidth, $rowHeight, 'Max BA-Anteil Kd: ' . $maxBA, 'LRTB', 0, 'L', 1);
    }
    $pdf->Cell(10, $rowHeight, '', 'LR', 0, 'C', 1);
    $pdf->Ln();

// Max BA-Anteil Aby : 0,25
// *************************************************************************************************** \\

    $pdf->Cell(10, $rowHeight, 'KW', 'LR', 0, 'C', 1);
    $pdf->Cell($stkWidth * $pocetZakazniku, $rowHeight, 'Max BA-Anteil Aby : 0,25', 'LTB', 0, 'C', 1);
    $pdf->Cell(10, $rowHeight, 'Ø', 'LR', 0, 'C', 1);
    $pdf->Ln();

//IST / STK & IST / KG
// *************************************************************************************************** \\
    $pdf->Cell(10, $rowHeight, '', 'LRB', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
	$ba = $a->getKundeBAAnteilStk($kd);
	$pdf->Cell($stkWidth, $rowHeight, 'INT / ' . $ba, 'LRBT', 0, 'C', 1);
    }
    $pdf->Cell(10, $rowHeight, '', 'LRB', 0, 'C', 1);
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

$params = "Kunde $kdvon - $kdbis, Datum " . $_GET['von'] . "-" . $_GET['bis'];
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S370 - 50 Ausschuss", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP - 10, PDF_MARGIN_RIGHT);
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

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
$pocetZakazniku = count($kundenNrArray);

//AplDB::varDump($pocetZakazniku);
//***************************************************************************************************************************\\
$stkWidth = 26;
$rowHeight = 4;
//***************************************************************************************************************************\\
$pdf->AddPage();
pageHeaderMain($pdf, $rowHeight, $kundenNrArray, $stkWidth, $pocetZakazniku);

/*
  $pdf->SetFillColor(255,255,230);
  $pdf->Cell(10,$rowHeight,'','LRT',0,'R',1);

  //zakaznici
  // *************************************************************************************************** \\
  foreach($kundenNrArray as $kd => $v1){
  $pdf->Cell($stkWidth,$rowHeight,$kd,'LRTB',0,'C',1);
  //AplDB::varDump($kd);
  }
  $pdf->Cell(10,$rowHeight,'','LRT',0,'C',1);
  $pdf->Ln();
  // *************************************************************************************************** \\
  $pdf->Cell(10,$rowHeight,'','LR',0,'R',1);


  // Max BA-Anteil Kd:
  // *************************************************************************************************** \\

  $pdf->SetFont("FreeSans", "", 5.5);
  foreach ($kundenNrArray as $kd=>$v2){

  $maxBAInfo = $a->getBewertungKriteriumInfo($kd, 'ba_anteil_max', date('y-m'));
  if($maxBAInfo!==NULL){
  $maxBA = $maxBAInfo[0]['grenze']."/".$maxBAInfo[0]['interval_monate'];
  }
  else{
  $maxBA = '?';
  }
  $ba = $a->getKundeBAAnteilStk($kd);
  $pdf->Cell($stkWidth,$rowHeight,'Max BA-Anteil Kd: '.$maxBA,'LRTB',0,'L',1);
  }
  $pdf->Cell(10,$rowHeight,'','LR',0,'C',1);
  $pdf->Ln();

  // Max BA-Anteil Aby : 0,25
  // *************************************************************************************************** \\

  $pdf->Cell(10,$rowHeight,'KW','LR',0,'C',1);
  $pdf->Cell($stkWidth*$pocetZakazniku,$rowHeight,'Max BA-Anteil Aby : 0,25','LTB',0,'C',1);
  $pdf->Cell(10,$rowHeight,'Ø','LR',0,'C',1);
  $pdf->Ln();

  //IST / STK & IST / KG
  // *************************************************************************************************** \\
  $pdf->Cell(10,$rowHeight,'','LRB',0,'C',1);

  foreach ($kundenNrArray as $kd=>$v){
  $ba = $a->getKundeBAAnteilStk($kd);
  $pdf->Cell($stkWidth,$rowHeight,'Ist / '.$ba,'LRBT',0,'C',1);
  }
  $pdf->Cell(10,$rowHeight,'','LRB',0,'C',1);
 */
$pdf->Ln();

// Tabulka
// *************************************************************************************************** \\
foreach ($jahrMonatKwArray as $jmk => $v) {
    if (test_pageoverflow_noheader($pdf, $rowHeight)) {
	pageHeaderMain($pdf, $rowHeight, $kundenNrArray, $stkWidth, $pocetZakazniku);
	$pdf->Ln();
    }
    $pdf->Cell(10, $rowHeight, substr($jmk, 8), 'LRTB', 0, 'C', 0);
    $podilPctSum = 0;
    foreach ($kundenNrArray as $kd => $v1) {
	$ba = $a->getKundeBAAnteilStk($kd); //ba urcuje zda se podil pocita z kg nebo z kusu
	//$ba='kg';

	$citatel = floatval($a6Array[$jmk][$kd]['a6_rm'][$ba]);
	$jmenovatel = floatval($expArray[$jmk][$kd]['exp_gut'][$ba] + $expArray[$jmk][$kd]['exp_auss'][$ba]);
	$podilPct = $jmenovatel != 0 ? $citatel / $jmenovatel * 100 : 0;
	//AplDB::varDump($citatel);
	$podilPctSum += $podilPct;
	$pod = number_format($podilPct, 2, ',', ' ');
	$pdf->Cell($stkWidth, $rowHeight, $pod, 'LRBT', 0, 'C', 0);
    }

    $avgKw = count($kundenNrArray) != 0 ? $podilPctSum / count($kundenNrArray) : 0;
    $avg = number_format($avgKw, 2, ',', ' ');
    $pdf->Cell(10, $rowHeight, $avg, 'LRBT', 0, 'C', 0);
    $pdf->Ln();
}
// AVG celkově
// *************************************************************************************************** \\

$pdf->Cell(10, $rowHeight, 'Ø', 'LRBT', 0, 'C', 1);

foreach ($kundenNrArray as $kd => $v1) {
    $podilPctSum = 0;
    foreach ($jahrMonatKwArray as $jmk => $v) {
	$ba = $a->getKundeBAAnteilStk($kd); //ba urcuje zda se podil pocita z kg nebo z kusu

	$citatel = floatval($a6Array[$jmk][$kd]['a6_rm'][$ba]);
	$jmenovatel = floatval($expArray[$jmk][$kd]['exp_gut'][$ba] + $expArray[$jmk][$kd]['exp_auss'][$ba]);
	$podilPct = $jmenovatel != 0 ? $citatel / $jmenovatel * 100 : 0;
	$podilPctSum += $podilPct;
	$pod = number_format($podilPctSum, 2, ',', ' ');

	//AplDB::varDump($pod);
    }

    $vypocet = count($pod) != 0 ? $podilPctSum / count($jahrMonatKwArray) : 0;
    //AplDB::varDump($vypocet);

    $avg = number_format($vypocet, 2, ',', ' ');
    //AplDB::varDump($avg);

    $pdf->Cell($stkWidth, $rowHeight, $avg, 'LRBT', 0, 'C', 1);
    $podilPctSum = 0;
}
$pdf->Cell(10, $rowHeight, '', 'LRTB', 0, 'C', 1);

$pdf->Ln();

$pdf->Cell($stkWidth, $rowHeight, 'Hodnocení:', '', 0, 'L', 0);
$pdf->Ln();

$krRows = $a->getBewertungKriteriumInfo(100, "q_S370_ausschuss", substr($jmk, 2));

foreach ($krRows as $kr) {
    $znaminko = $kr['bis_von'] == 'bis' ? '<=' : '>';
    $pdf->Cell($stkWidth, $rowHeight, $znaminko . $kr['grenze'] . '%', 'LRTB', 0, 'L', 0);
    $pdf->Cell($stkWidth / 4, $rowHeight, $kr['bewertung'], 'LRTB', 0, 'R', 0);

    $pdf->Ln();
}
$pdf->Cell($stkWidth, $rowHeight, '>' . '0,2 %', 'LRTB', 0, 'L', $fill);
$pdf->Cell($stkWidth / 4, $rowHeight, '6', 'LRTB', 0, 'R', $fill);


// *************************************************************************************************** \\
$pdf->AddPage();
// *************************************************************************************************** \\
// Po mesicich
// *************************************************************************************************** \\
$pdf->Cell(10, $rowHeight, '', 'LRT', 0, 'C', 1);
foreach ($kundenNrArray as $kd => $v1) {
    $pdf->Cell($stkWidth, $rowHeight, $kd, 'LRTB', 0, 'C', 1);
}
$pdf->Cell($stkWidth / 2, $rowHeight, '', 'LT', 0, 'C', 1);
$pdf->Cell($stkWidth / 2, $rowHeight, '', 'TR', 0, 'C', 1);
$pdf->Ln();

//IST / STK & IST / KG
// *************************************************************************************************** \\
$pdf->Cell(10, $rowHeight, '', 'LR', 0, 'C', 1);
foreach ($kundenNrArray as $kd => $v2) {

    $maxBAInfo = $a->getBewertungKriteriumInfo($kd, 'ba_anteil_max', date('y-m'));
    if ($maxBAInfo !== NULL) {
	$maxBA = $maxBAInfo[0]['grenze'] . "/" . $maxBAInfo[0]['interval_monate'];
    } else {
	$maxBA = '?';
    }
    $ba = $a->getKundeBAAnteilStk($kd);
    $pdf->Cell($stkWidth, $rowHeight, 'Max BA-Anteil Kd: ' . $maxBA, 'LRTB', 0, 'L', 1);
}
$pdf->Cell($stkWidth / 2, $rowHeight, '', 'L', 0, 'C', 1);
$pdf->Cell($stkWidth / 2, $rowHeight, '', 'R', 0, 'C', 1);
$pdf->Ln();

$pdf->Cell(10, $rowHeight, '', 'LR', 0, 'C', 1);
$pdf->Cell($stkWidth * $pocetZakazniku, $rowHeight, 'Max BA-Anteil Aby : 0,25', 'LTB', 0, 'C', 1);

$pdf->Cell($stkWidth / 2, $rowHeight, '', 'L', 0, 'C', 1);
$pdf->Cell($stkWidth / 2, $rowHeight, '', 'R', 0, 'C', 1);
$pdf->Ln();
$pdf->Cell(10, $rowHeight, 'MONAT', 'LR', 0, 'C', 1);
foreach ($kundenNrArray as $kd => $v) {
    $ba = $a->getKundeBAAnteilStk($kd); //ba urcuje zda se podil pocita z kg nebo z kusu

    $pdf->Cell($stkWidth / 2, $rowHeight, 'INT / ' . $ba, 'LRBT', 0, 'C', 1);
    $pdf->Cell($stkWidth / 2, $rowHeight, "Ext.Stk", 'LRBT', 0, 'C', 1);
}
$pdf->Cell($stkWidth / 2, $rowHeight, 'Ø', 'LB', 0, 'C', 1);
$pdf->Cell($stkWidth / 2, $rowHeight, "Ext.Stk", 'RB', 0, 'C', 1);
$pdf->Ln();

// ********************************************************************************************//

foreach ($jahrMonatArray as $jm => $v) {
    $pdf->Cell(10, $rowHeight, $jm, 'LRTB', 0, 'C', 0);

    $podilPctSum = 0;
    foreach ($kundenNrArray as $kd => $v) {
	//ist
	$ba = $a->getKundeBAAnteilStk($kd); //ba urcuje zda se podil pocita z kg nebo z kusu

	$citatel = floatval($a6ArrayMonatSummen[$jm][$kd]['a6_rm'][$ba]);
	$jmenovatel = floatval($expArrayMonatSummen[$jm][$kd]['exp_gut'][$ba] + $expArrayMonatSummen[$jm][$kd]['exp_auss'][$ba]);
	$podilPct = $jmenovatel != 0 ? $citatel / $jmenovatel * 100 : 0;
	$podilPctSum += $podilPct;
	$po = number_format($podilPct, 2, ',', ' ');
	$pdf->Cell($stkWidth / 2, $rowHeight, $po, 'LRBT', 0, 'C', 0);
	//extern

	$citatel_stk = floatval($extStkArray[$jm][$kd]["extStk"]["stk"]);
	$jmenovatel_stk = floatval($expArrayMonatSummen[$jm][$kd]['exp_gut']['stk'] + $expArrayMonatSummen[$jm][$kd]['exp_auss']['stk']);
	$podilStkPct = $jmenovatel_stk != 0 ? $citatel_stk / $jmenovatel_stk * 100 : 0;
	$podilStkPctSum += $podilStkPct;
	$pod = number_format($podilStkPct, 2, ',', ' ');
	$pdf->Cell($stkWidth / 2, $rowHeight, $pod, 'LRBT', 0, 'C', 0);
    }

    $avgMnt = count($kundenNrArray) != 0 ? $podilPctSum / count($kundenNrArray) : 0;
    $av = number_format($avgMnt, 2, ',', ' ');
    $pdf->Cell($stkWidth / 2, $rowHeight, $av, 'LRBT', 0, 'C', 0);
    $avgMnt = count($kundenNrArray) != 0 ? $podilStkPctSum / count($kundenNrArray) : 0;
    $avg = number_format($avgMnt, 2, ',', ' ');
    $pdf->Cell($stkWidth / 2, $rowHeight, $avg, 'LRBT', 0, 'C', 0);
    $pdf->Ln();
}


//$pdf->Cell(10,$rowHeight,'AVG','LRBT',0,'C',1);
$pdf->Cell(10, $rowHeight, 'Ø', 'LRBT', 0, 'C', 1);

foreach ($kundenNrArray as $kd => $v1) {
    $podilPctSum = 0;
    $podilStkPctSum = 0;
    foreach ($jahrMonatArray as $jm => $v) {
	$ba = $a->getKundeBAAnteilStk($kd); //ba urcuje zda se podil pocita z kg nebo z kusu

	$citatel = floatval($a6ArrayMonatSummen[$jm][$kd]['a6_rm'][$ba]);
	$jmenovatel = floatval($expArrayMonatSummen[$jm][$kd]['exp_gut'][$ba] + $expArrayMonatSummen[$jm][$kd]['exp_auss'][$ba]);
	$podilPct = $jmenovatel != 0 ? $citatel / $jmenovatel * 100 : 0;
	$podilPctSum += $podilPct;
	$po = number_format($podilPct, 2, ',', ' ');
	//extern
	$citatel_stk = floatval($extStkArray[$jm][$kd]["extStk"]["stk"]);
	$jmenovatel_stk = floatval($expArrayMonatSummen[$jm][$kd]['exp_gut']['stk'] + $expArrayMonatSummen[$jm][$kd]['exp_auss']['stk']);
	$podilStkPct = $jmenovatel_stk != 0 ? $citatel_stk / $jmenovatel_stk * 100 : 0;
	$podilStkPctSum += $podilStkPct;
	$podS = number_format($podilStkPctSum, 2, ',', ' ');
	//AplDB::varDump($pod);
    }

    $vypocet = count($po) != 0 ? $podilPctSum / count($jahrMonatArray) : 0;
    //AplDB::varDump($vypocet);
    $vypocet2 = count($podS) != 0 ? $podilStkPctSum / count($jahrMonatArray) : 0;
    $avg = number_format($vypocet, 2, ',', ' ');
    $avg2 = number_format($vypocet2, 2, ',', ' ');
    //AplDB::varDump($avg);

    $pdf->Cell($stkWidth / 2, $rowHeight, $avg, 'LRBT', 0, 'C', 1);
    $pdf->Cell($stkWidth / 2, $rowHeight, $avg2, 'LRBT', 0, 'C', 1);
    $podilPctSum = 0;
    $podilStkPctSum = 0;
}

$pdf->Cell($stkWidth / 2, $rowHeight, '', 'LRBT', 0, 'C', 1);
$pdf->Cell($stkWidth / 2, $rowHeight, '', 'LRBT', 0, 'C', 1);


// ********************************************************************************************//



$pdf->Ln();
$pdf->Cell($stkWidth, $rowHeight, 'Hodnoceni:', '', 0, 'L', 0);
$pdf->Ln();

foreach ($krRows as $kr) {
    $znaminko = $kr['bis_von'] == 'bis' ? '<=' : '>';
    $pdf->Cell($stkWidth, $rowHeight, $znaminko . $kr['grenze'] . '%', 'LRTB', 0, 'L', 0);
    $pdf->Cell($stkWidth / 4, $rowHeight, $kr['bewertung'], 'LRTB', 0, 'R', 0);

    $pdf->Ln();
}
$pdf->Cell($stkWidth, $rowHeight, '>' . '0,2 %', 'LRTB', 0, 'L', $fill);
$pdf->Cell($stkWidth / 4, $rowHeight, '6', 'LRTB', 0, 'R', $fill);

// *************************************************************************************************** \\
$xLeft = $pdf->GetX();
$yTop = $pdf->GetY();



$pdf->SetY($yTop);

function pageheade($pdf, $pole, $headervyskaradku) {
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->SetFillColor(255, 255, 200, 1);

    $pdf->Cell($pole, $headervyskaradku);
}

function test_pageoverflow_nohead($pdfobjekt, $vysradku) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	//$pdfobjekt->AddPage();
	return TRUE;
    }
    return FALSE;
}

// *************************************************************************************************** \\
if ($mitdetail === TRUE) {
    $sumyKd = array();

    $wTeil = $stkWidth / 2+10;
    $wPers = $stkWidth / 2;
    $wName = 1.5 * $stkWidth;
    $wAussStk = $stkWidth / 2;
    $wGew = $stkWidth / 2;
    $wGewGes = $stkWidth / 1.5;
    $wAbgnr = $stkWidth / 2;
    $wRegOE = $stkWidth / 2;
    $wRmOE = $stkWidth / 2;
    $wFull = $wTeil + $wPers + $wName + $wAussStk + $wGew + $wGewGes + $wAbgnr + $wRegOE + $wRmOE;

    $pdf->AddPage("P");
    $pdf->SetFillColor(160, 160, 160);

    $pdf->Cell($wTeil, $rowHeight, "Teil", 'LRBT', 0, 'C', 1);
    $pdf->Cell($wPers, $rowHeight, "Pers", 'LRBT', 0, 'C', 1);
    $pdf->Cell($wName, $rowHeight, "Name", 'LRBT', 0, 'C', 1);
    $pdf->Cell($wAussStk, $rowHeight, "Auss6Stk", 'LRBT', 0, 'C', 1);
    $pdf->Cell($wGew, $rowHeight, "Gew/Stk[kg]", 'LRBT', 0, 'R', 1);
    $pdf->Cell($wGewGes, $rowHeight, "GewGes/Stk[kg]", 'LRBT', 0, 'R', 1);
    $pdf->Cell($wRegOE, $rowHeight, "RegelOE", 'LRBT', 0, 'L', 1);
    $pdf->Cell($wRmOE, $rowHeight, "RueckOE", 'LRBT', 0, 'L', 1);
    $pdf->Cell($wAbgnr, $rowHeight, "abgnr", 'LRBT', 0, 'R', 1);
    $pdf->Ln();
    foreach ($detailArray as $jmk => $kundeRow) {

	if (test_pageoverflow_nohead($pdf, $rowHeight)) {
	    $pdf->AddPage("P");
	    $pdf->SetFillColor(160, 160, 160);
	    $pdf->Cell($wTeil, $rowHeight, "Teil", 'LRBT', 0, 'C', 1);
	    $pdf->Cell($wPers, $rowHeight, "Pers", 'LRBT', 0, 'C', 1);
	    $pdf->Cell($wName, $rowHeight, "Name", 'LRBT', 0, 'C', 1);
	    $pdf->Cell($wAussStk, $rowHeight, "Auss6Stk", 'LRBT', 0, 'C', 1);
	    $pdf->Cell($wGew, $rowHeight, "Gew/Stk[kg]", 'LRBT', 0, 'R', 1);
	    $pdf->Cell($wGewGes, $rowHeight, "GewGes/Stk[kg]", 'LRBT', 0, 'R', 1);
	    $pdf->Cell($wRegOE, $rowHeight, "RegelOE", 'LRBT', 0, 'L', 1);
	    $pdf->Cell($wRmOE, $rowHeight, "RueckOE", 'LRBT', 0, 'L', 1);
	    $pdf->Cell($wAbgnr, $rowHeight, "abgnr", 'LRBT', 0, 'R', 1);
	    $pdf->Ln();
	}
	$pdf->SetFillColor(192, 192, 192);
	$jm = substr($jmk, 0, 7);
	$kw = substr($jmk, 8);
	$pdf->Cell($wFull, $rowHeight, "Jahr-Monat : $jm, KW : $kw", 'LRBT', 0, 'L', 1);

	$pdf->Ln();
	foreach ($kundeRow as $kd => $detailRow) {
	    $sumStk = 0;
	    $sumGew = 0;

	    if (test_pageoverflow_nohead($pdf, $rowHeight)) {
		$pdf->AddPage("P");
		$pdf->SetFillColor(160, 160, 160);
		$pdf->Cell($wTeil, $rowHeight, "Teil", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wPers, $rowHeight, "Pers", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wName, $rowHeight, "Name", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wAussStk, $rowHeight, "Auss6Stk", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wGew, $rowHeight, "Gew/Stk[kg]", 'LRBT', 0, 'R', 1);
		$pdf->Cell($wGewGes, $rowHeight, "GewGes/Stk[kg]", 'LRBT', 0, 'R', 1);
		$pdf->Cell($wRegOE, $rowHeight, "RegelOE", 'LRBT', 0, 'L', 1);
		$pdf->Cell($wRmOE, $rowHeight, "RueckOE", 'LRBT', 0, 'L', 1);
		$pdf->Cell($wAbgnr, $rowHeight, "abgnr", 'LRBT', 0, 'R', 1);
		$pdf->Ln();
	    }
	    $pdf->SetFillColor(255, 255, 230);
	    $pdf->Cell($wFull, $rowHeight, $kd, 'LRBT', 0, 'L', 1);
	    $pdf->Ln();
	    foreach ($detailRow as $r) {
		if (test_pageoverflow_nohead($pdf, $rowHeight)) {
		    $pdf->SetFillColor(160, 160, 160);
		    $pdf->Cell($wTeil, $rowHeight, "Teil", 'LRBT', 0, 'C', 1);
		    $pdf->Cell($wPers, $rowHeight, "Pers", 'LRBT', 0, 'C', 1);
		    $pdf->Cell($wName, $rowHeight, "Name", 'LRBT', 0, 'C', 1);
		    $pdf->Cell($wAussStk, $rowHeight, "Auss6Stk", 'LRBT', 0, 'C', 1);
		    $pdf->Cell($wGew, $rowHeight, "Gew/Stk[kg]", 'LRBT', 0, 'R', 1);
		    $pdf->Cell($wGewGes, $rowHeight, "GewGes/Stk[kg]", 'LRBT', 0, 'R', 1);
		    $pdf->Cell($wRegOE, $rowHeight, "RegelOE", 'LRBT', 0, 'L', 1);
		    $pdf->Cell($wRmOE, $rowHeight, "RueckOE", 'LRBT', 0, 'L', 1);
		    $pdf->Cell($wAbgnr, $rowHeight, "abgnr", 'LRBT', 0, 'R', 1);
		    $pdf->Ln();

		    $pdf->SetFillColor(192, 192, 192);
		    $jm = substr($jmk, 0, 7);
		    $kw = substr($jmk, 8);
		    $pdf->Cell($wFull, $rowHeight, "Jahr-Monat : $jm, KW : $kw", 'LRBT', 0, 'L', 1);
		    //$pdf->Cell($wFull,$rowHeight,$jmk,'LRBT',0,'L',1);

		    $pdf->Ln();
		    $pdf->SetFillColor(255, 255, 230);
		    $pdf->Cell($wFull, $rowHeight, $kd, 'LRBT', 0, 'L', 1);
		    $pdf->Ln();
		}
		$pdf->Cell($wTeil, $rowHeight, $r['teil'], 'LRBT', 0, 'L', 0);
		$pdf->Cell($wPers, $rowHeight, $r['persnr'], 'LRBT', 0, 'R', 0);
		$pdf->Cell($wName, $rowHeight, $r['persname'], 'LRBT', 0, 'L', 0);
		$pdf->Cell($wAussStk, $rowHeight, $r['sum_auss6'], 'LRBT', 0, 'R', 0);
		$pdf->Cell($wGew, $rowHeight, number_format($r['gew'], 2, ',', ' '), 'LRBT', 0, 'R', 0);
		$pdf->Cell($wGewGes, $rowHeight, number_format($r['gew'] * $r['sum_auss6'], 2, ',', ' '), 'LRBT', 0, 'R', 0);
		$pdf->Cell($wRegOE, $rowHeight, $r['regeloe'], 'LRBT', 0, 'L', 0);
		$pdf->Cell($wRmOE, $rowHeight, $r['rmoe'], 'LRBT', 0, 'L', 0);
		$pdf->Cell($wAbgnr, $rowHeight, $r['abgnr'], 'LRBT', 0, 'R', 0);
		$pdf->Ln();
		$sumStk += intval($r['sum_auss6']);
		$sumGew += ($r['gew'] * $r['sum_auss6']);
		$height = $pdf->getPageHeight();
	    }

	    //zapati Kunde
	    if (test_pageoverflow_nohead($pdf, $rowHeight)) {
		$pdf->AddPage("P");
		$pdf->SetFillColor(160, 160, 160);
		$pdf->Cell($wTeil, $rowHeight, "Teil", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wPers, $rowHeight, "Pers", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wName, $rowHeight, "Name", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wAussStk, $rowHeight, "Auss6Stk", 'LRBT', 0, 'C', 1);
		$pdf->Cell($wGew, $rowHeight, "Gew/Stk[kg]", 'LRBT', 0, 'R', 1);
		$pdf->Cell($wGewGes, $rowHeight, "GewGes/Stk[kg]", 'LRBT', 0, 'R', 1);
		$pdf->Cell($wRegOE, $rowHeight, "RegelOE", 'LRBT', 0, 'L', 1);
		$pdf->Cell($wRmOE, $rowHeight, "RueckOE", 'LRBT', 0, 'L', 1);
		$pdf->Cell($wAbgnr, $rowHeight, "abgnr", 'LRBT', 0, 'R', 1);
		$pdf->Ln();
	    }
	    $pdf->SetFont("FreeSans", "B", 6);
	    $pdf->SetFillColor(255, 255, 200);
	    $pdf->Cell($wTeil + $wPers + $wName, $rowHeight, "Sum $kd", 'LRBT', 0, 'L', 1);
	    $pdf->Cell($wAussStk, $rowHeight, $sumStk, 'LRBT', 0, 'R', 1);
	    $pdf->Cell($wGew, $rowHeight, '', 'LRBT', 0, 'R', 1);
	    $pdf->Cell($wGewGes, $rowHeight, number_format($sumGew, 2, ',', ' '), 'LRBT', 0, 'R', 1);
	    $pdf->Cell($wRegOE+$wRmOE+$wAbgnr, $rowHeight,'', 'LRBT', 0, 'L', 1);
	    $pdf->Ln();
	    $pdf->SetFont("FreeSans", "", 7);
	}
    }
}


//Close and output PDF document
// *************************************************************************************************** \\
// *************************************************************************************************** \\
$pdf->Ln();
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+