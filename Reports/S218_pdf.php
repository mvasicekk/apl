<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S218";
$doc_subject = "S218 Report";
$doc_keywords = "S218";

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

//$planyArray = $apl->getPlaene($kd_von,$kd_bis,$timeVon,$timeBis);

$statnrA = array(
    "S0011"=>array("width"=>0),
    "S0041"=>array("width"=>0),
    "S0051"=>array("width"=>0),
    "S0061"=>array("width"=>0),
    "S0081"=>array("width"=>0),
    "sum"=>array("width"=>0),
    );
$statnrColumns = array(
    "VzKdPlan"=>array("width"=>13,"planindex"=>"vzkdplan"),
    "Tag/Soll"=>array("width"=>13,"planindex"=>"solltag"),
    );

$summeDay = array();
$summeKunde = array();
$summeAll = array();

/**
 *
 * @param TCPDF $pdf
 * @param type $rgb
 * @param type $time
 * @param type $statnrArray
 * @param type $statnrColumns 
 */

function dayHeader($pdf, $rgb, $rowHeight, $time, $statnrArray, $statnrColumns) {
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdf->SetFont("FreeSans", "B", 6);
    $timeDay = date('d.m.Y', $time);

    $pdf->Cell(20, $rowHeight, $timeDay, '1', 0, 'L', $fill);

    // calculate statnrWidth
    foreach ($statnrColumns as $colname => $colInfo) {
	$statnrWidth += $colInfo['width'];
    }

    // print statnrheader
    foreach ($statnrArray as $statnr => $aI) {
	$pdf->Cell(
		$statnrWidth
		, $rowHeight, $statnr, '1', 0, 'C', $fill);
    }
    $pdf->Ln();

    //----------------------------------------------------------------------
    $pdf->Cell(20, $rowHeight, "Termin", '1', 0, 'L', $fill);
    foreach ($statnrArray as $statnr) {
	foreach ($statnrColumns as $colname => $colInfo) {
	    $pdf->Cell(
		    $colInfo['width']
		    , $rowHeight
		    , $colname, '1', 0, 'R', $fill);
	}
    }
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param type $rgb
 * @param type $rowHeight
 * @param type $plan
 * @param type $von
 * @param type $bis
 * @param type $time
 * @param type $statnrA
 * @param type $statnrColumns 
 */
function printPlanDetail($pdf, $rgb, $rowHeight, $kunde,$plan,$von, $bis,$time, $statnrA, $statnrColumns,$rmDateTime,$show=TRUE){
    global $apl;
    global $summeDay;
    global $summeKunde;
    global $summeAll;


    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdf->SetFont("FreeSans", "", 7);
    $timeIndex = date('Ymd',$time);
    if($show===TRUE) $pdf->Cell(20, $rowHeight, $plan['auftragsnr']." ( ".$plan['ex_datum_soll']." )", '1', 0, 'L', $fill);
    $pIA = $apl->getPlanInfoArray("P".$plan['auftragsnr'],$von,$bis,$time,$rmDateTime);
    foreach ($statnrA as $statnr=>$statInfo){
	foreach ($statnrColumns as $colname=>$colInfo){
	    $planIndex = $colInfo['planindex'];
	    $obsah = number_format($pIA[$statnr][$planIndex],0,',',' ');
	    //$obsah = $planIndex." ".$plan['auftragsnr'];
	    if($show===TRUE){
		$pdf->Cell(
		    $colInfo['width']
		    , $rowHeight
		    , $obsah, '1', 0, 'R', $fill);
	    }
	    $summeDay[$timeIndex][$statnr][$planIndex]+=intval($pIA[$statnr][$planIndex]);
	    $summeKunde[$kunde][$timeIndex][$statnr][$planIndex]+=intval($pIA[$statnr][$planIndex]);
	    $summeAll[$statnr][$planIndex]+=intval($pIA[$statnr][$planIndex]);
	}
    }
    if($show===TRUE) $pdf->Ln();
}


function printSummeKunde($pdf, $rgb, $rowHeight, $summe, $kunde,$time, $statnrA, $statnrColumns){
    global $apl;
    
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdf->SetFont("FreeSans", "", 7);
    $timeIndex = date('Ymd',$time);
    $timeDay = date('d.m.Y',$time);
    $pdf->Cell(20, $rowHeight, "Sum $kunde", '1', 0, 'L', $fill);
    foreach ($statnrA as $statnr=>$statInfo){
	foreach ($statnrColumns as $colname=>$colInfo){
	    $planIndex = $colInfo['planindex'];
	    $obsah = number_format($summe[$kunde][$timeIndex][$statnr][$planIndex],0,',',' ');
	    $pdf->Cell(
		    $colInfo['width']
		    , $rowHeight
		    , $obsah, '1', 0, 'R', $fill);
	}
    }
}


function printSummeDay($pdf, $rgb, $rowHeight, $summe,$time, $statnrA, $statnrColumns){
    global $apl;
    
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdf->SetFont("FreeSans", "", 7);
    $timeIndex = date('Ymd',$time);
    $timeDay = date('d.m.Y',$time);
    $pdf->Cell(20, $rowHeight, "Sum $timeDay", '1', 0, 'L', $fill);
    foreach ($statnrA as $statnr=>$statInfo){
	foreach ($statnrColumns as $colname=>$colInfo){
	    $planIndex = $colInfo['planindex'];
	    $obsah = number_format($summe[$timeIndex][$statnr][$planIndex],0,',',' ');
	    $pdf->Cell(
		    $colInfo['width']
		    , $rowHeight
		    , $obsah, '1', 0, 'R', $fill);
	}
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
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$aktDatum = date('Y-m-d H:i:s');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S218 Dispo - Plan   $aktDatum", $params);
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
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);
$pdf->AddPage();
if (($timeVon > 0) && ($timeBis >= $timeVon)) {
    $time = $timeVon;
    while ($time <= $timeBis) {
	$summeDay = array();
	$planyArray = $apl->getPlaene($kd_von,$kd_bis,$time,$time);
	$testvysradku = 5*2 + 5*count($planyArray) + 5*1;
	if(test_pageoverflow_nopage($pdf, $testvysradku)){
	    $pdf->AddPage();
	}
	dayHeader($pdf, array(255,255,240), 5, $time, $statnrA, $statnrColumns);
	// plan detail
	$kundeOld = 0;
	$planyIndex = 0;
	foreach ($planyArray as $plan){
	    $kunde = $a->getKundeFromAuftransnr($plan['auftragsnr']);
	    if(($kunde!=$kundeOld)&&($planyIndex>0)){
		printSummeKunde($pdf, array(240,240,255), 5, $summeKunde,$kundeOld, $time, $statnrA, $statnrColumns);
		$kundeOld=$kunde;
		$pdf->Ln();
	    }
	    printPlanDetail($pdf, array(255,255,255), 5, $kunde,$plan, $von, $bis, $time, $statnrA, $statnrColumns,$summeDay,$rmDateTime,FALSE);
	    $planyIndex++;
	    $kundeOld = $kunde;
	}
	//suma posledniho zakaznika
	printSummeKunde($pdf, array(240,240,255), 5, $summeKunde,$kunde, $time, $statnrA, $statnrColumns);
	$pdf->Ln();
	// summe Day
	printSummeDay($pdf, array(240,255,240), 5, $summeDay, $time, $statnrA, $statnrColumns);
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
