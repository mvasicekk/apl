<?php

session_start();
require_once '../security.php';
require_once '../db.php';

$doc_title = "D155";
$doc_subject = "D155 Report";
$doc_keywords = "D155";

$parameters = $_GET;

$a = AplDB::getInstance();

$user = $_SESSION['user'];
$password = $_GET['password'];
$jahr = $_GET['jahr'];
$monat = $_GET['monat'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$oe1 = trim($_GET['oe']);
$oe1 = strtr($oe1, "*", "%");

if(strlen($oe1)==0 || $oe1=="%"){
    $oe1 = NULL;
}

$fullAccess = $a->testReportPassword("D155", $password, $user, 0);

if ((!$fullAccess)) {
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}

$von = sprintf("%02d.%02d.%04d", 1, $monat, $jahr);
$days = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = sprintf("%02d.%02d.%04d", $days, $monat, $jahr);

//echo "<br>von = $von";
//echo "<br>bis = $bis";

$start = strtotime($a->make_DB_datum($von));
$end = strtotime($a->make_DB_datum($bis));

/**
 * najde v poli zaznam s osobnim faktorem = $id
 * @param type $id id osobniho faktoru
 * @param type $arr pole hodnoceni pro persnr a datumy
 */
function searchOFR($id, $arr1) {
    foreach ($arr1 as $key => $arr) {
	if ($key !== "osobniFaktory") {
	    foreach ($arr as $jm => $hodnoceniRow) {
		$hodnoceni_osobni = $hodnoceniRow['hodnoceni_osobni'];
		if ($hodnoceni_osobni['id_faktor'] == $id) {
		    //nalezeno
		    $hodnoceni_osobni['hodnoceni_firma'] = $hodnoceniRow['hodnoceni_firma'];
		    return $hodnoceni_osobni;
		}
	    }
	}
    }
    // sem se dostanu, kdy nenajdu
    return NULL;
}

//AplDB::varDump($osobniHodnoceni);
//AplDB::varDump($koeficientArray);

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P', 'mm', 'A4', 1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

//$pdf->SetHeaderData("", 0, "D729", "");
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D155", $params);
$pdf->setRechnungFoot(FALSE);
// vypnout standardni zahlavi
$pdf->setPrintHeader(FALSE);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT + 5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER+3);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER + 6);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor
$pdf->SetProtection(array('extract'), $pdfpass, '', 1);

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items
//initialize document
//$pdf->AliasNbPages();

$pdf->SetFont("FreeSans", "", 8);

//vybrat osobni cisla, ktera mji v danem mesici osobni hodnoceni
$persnrArray = $a->getPersNrArrayHodnoceniMonatJahr($persvon, $persbis, $jahr, $monat,$oe1);
if ($persnrArray !== NULL) {
    foreach ($persnrArray as $pr) {
	$persnr = $pr['persnr'];
	// prvni stranka
	
	$persinfo = $a->getPersInfoArray($persnr);
	$name = $persinfo[0]['Name'];
	$vorname = $persinfo[0]['Vorname'];
	$regeloe = $persinfo[0]['regeloe'];
	$oeInfo = $a->getOEInfoForOES($regeloe);
	if($oeInfo!==NULL){
	    $oe = $oeInfo['oe'];
	    $popis = $oeInfo['beschreibung_cz'];
	    $dleOEText = "$oe - $popis";
	}
	else{
	    $dleOEText = "RegelOE $regeloe";
	}
	
	
	$osobniHodnoceni = $a->getOsobniHodnoceniProPersNr($persnr, date('Y-m-d', $start), date('Y-m-d', $end));
	$koeficientArray = $a->getOsobniHodnoceniKoeficientProPersNr($persnr, date('Y-m-d', $start), date('Y-m-d', $end));

	if ($koeficientArray != NULL) {
	    foreach ($koeficientArray as $jm => $k) {
		$koeficientPritomnosti = floatval($k);
	    }
	} else {
	    $koeficientPritomnosti = 0;
	}
	
	$pdf->SetLineWidth(0.1);
	if ($osobniHodnoceni !== NULL) {
	    
	    $pdf->AddPage();
	    $pdf->SetLineWidth(0.01);
	    
	    $pdf->Image("./images/abydos_logo1.png", 151, 10,43);
	    $pdf->SetFont("FreeSans", "U", 7);
	    //$pdf->Text($x, $y, $txt, $fstroke, $fclip, $ffill, $border, $ln, $align, $fill)
	    $pdf->Text(PDF_MARGIN_LEFT+5, 20, "Abydos s.r.o., CZ - 351 32 Hazlov 247, Česká Republika",FALSE,FALSE,TRUE,'0',0,'R',0);
	    
	    $pdf->SetFont("FreeSans", "B", 12);
	    $pdf->Text(PDF_MARGIN_LEFT+5, 45-8, "$persnr - $vorname $name");
	    $pdf->SetFont("FreeSans", "B", 8);
	    $pdf->Text(PDF_MARGIN_LEFT+5, 45+9-8, "Osobní hodnocení dle OE");
	    $pdf->SetFont("FreeSans", "", 8);
	    $pdf->Text(PDF_MARGIN_LEFT+5, 45+9+5-8, "$dleOEText");
	    
	    $pdf->SetY(115);
	    
	    //zapamatovat horni roh x,y
	    $xTop = $pdf->GetX();
	    $yTop = $pdf->GetY();
	    
	    $pdf->SetFont("FreeSans", "B", 9);
	    $pdf->Cell(70+8+10, 7, "$persnr - $vorname $name", 'TL', 0, 'L', 0);
	    	    
	    $pdf->SetFont("FreeSans", "", 9.5);
	    $pdf->Cell(63, 7, "Osobní měsíční hodnocení za měsíc", 'T', 0, 'R', 0);
	    $pdf->SetFont("FreeSans", "B", 12);
	    $mj = sprintf("%02d / %04d",$monat,$jahr);
	    $pdf->Cell(0, 7, "$mj ", 'TR', 0, 'R', 0);
	    $pdf->Ln();
	    
	    $pdf->SetFont("FreeSans", "", 7);
	    $pdf->Cell(70+8+10, 5, "$dleOEText", 'L', 0, 'L', 0);
	    $pdf->SetFont("FreeSans", "U", 7);
	    $pdf->Cell(0, 5, "Abydos s.r.o.", 'R', 0, 'R', 0);
	    
	    $pdf->Ln();
	    $yTableHeader = $pdf->GetY();
	    
	    $osobniFaktoryArray = $osobniHodnoceni['osobniFaktory'];
	    $hodnotyOsobnichFaktoruArray = $osobniHodnoceni;
	    $pdf->SetFont("FreeSans", "B", 8);
	    $pdf->Cell(70, 6, "", 'TL', 0, 'L', 0);
	    $pdf->Cell(8, 6, "", 'T', 0, 'R', 0);
	    //dummy
	    $pdf->Cell(10, 6, "", 'T', 0, 'L', 0);
	    $pdf->SetFillColor(255, 255, 230);
	    //cil body + castka
	    $pdf->Cell(11 + 18, 6, "Cíl", 'T', 0, 'C', 1);
	    //dummy
	    $pdf->Cell(10, 6, "", 'T', 0, 'L', 0);

	    //firma + os body
	    $pdf->Cell(12 + 12, 6, "Body", 'T', 0, 'C', 0);
	    //pers castka
	    $pdf->Cell(0, 6, "", 'TR', 0, 'R', 0);
	    $pdf->Ln();


	    $pdf->Cell(70, 6, "osobní faktor", 'LBT', 0, 'L', 0);
	    $pdf->Cell(8, 6, "váha", 'BT', 0, 'R', 0);
	    //dummy
	    $pdf->Cell(10, 6, "", '0', 0, 'L', 0);
	    $pdf->SetFillColor(255, 255, 230);
	    //cil body
	    $pdf->Cell(11, 6, "Body", 'LBT', 0, 'C', 1);
	    //cil castka
	    $pdf->Cell(18, 6, "Kč", 'RBT', 0, 'R', 1);

	    //dummy
	    $pdf->Cell(10, 6, "", '0', 0, 'L', 0);

	    //firma body
	    $pdf->Cell(12, 6, "firma", 'LBT', 0, 'C', 0);
	    //pers body
	    $pdf->Cell(12, 6, "os.", 'LBT', 0, 'C', 0);
	    //pers castka
	    $pdf->Cell(0, 6, "Kč", 'BTR', 0, 'R', 0);

	    $pdf->Ln();
	    $sumCilCastka = 0;
	    $sumPersCastka = 0;

	    foreach ($osobniFaktoryArray as $osFaktor) {
		$id_osobni_faktor = $osFaktor['id_faktor'];
		//echo "<br>popis=".$osFaktor['popis'];
		$vaha = floatval($osFaktor['vaha']);
		//echo "<br>vaha=".$vaha;
		$firemni_faktor_id = $osFaktor['id_firma_faktor'];
		//echo "<br>firemni_faktor_id=".$firemni_faktor_id;
		$firFaktor = $a->getHodnoceniFiremniFaktor($firemni_faktor_id);
		if ($firFaktor !== NULL) {
		    //je to firemni faktor
		    $firemni_cil_body = $firFaktor['cil_hodnoceni'];
		} else {
		    //je to osobni faktor, nastavim napevno 8
		    $firemni_cil_body = 8;
		}
		$cil_castka = AplDB::hodnoceni2Penize($vaha, $firemni_cil_body);
		//echo "<br>firemni cil body (castka)=".$firemni_cil_body."(".$cil_castka.")";
		$hodnotaOsobnihoFaktoruRow = searchOFR($id_osobni_faktor, $hodnotyOsobnichFaktoruArray);
		if ($hodnotaOsobnihoFaktoruRow !== NULL) {
		    $firma_body = $hodnotaOsobnihoFaktoruRow['hodnoceni_firma'];
		    $pers_body = $hodnotaOsobnihoFaktoruRow['hodnoceni'];
		    $pers_castka = $hodnotaOsobnihoFaktoruRow['castka'];
		    //echo "<br>pers body (castka)=".$pers_body."(".$pers_castka."), firma_body=$firma_body";
		    $pdf->SetFont("FreeSans", "", 8);
		    $pdf->Cell(70, 6, $osFaktor['popis'], 'LBT', 0, 'L', 0);
		    $pdf->Cell(8, 6, $vaha, 'BT', 0, 'R', 0);
		    //dummy
		    $pdf->Cell(10, 6, "", '0', 0, 'L', 0);
		    $pdf->SetFillColor(255, 255, 230);
		    //cil body
		    $pdf->Cell(11, 6, $firemni_cil_body, 'LBT', 0, 'C', 1);
		    //cil castka
		    $pdf->Cell(18, 6, number_format($cil_castka, 0, ',', ' '), 'RBT', 0, 'R', 1);

		    //dummy
		    $pdf->Cell(10, 6, "", '0', 0, 'L', 0);

		    $pdf->SetFont("FreeSans", "", 9);
		    //firma body
		    $pdf->Cell(12, 6, $firma_body, 'LBT', 0, 'C', 0);
		    //pers body
		    $pdf->Cell(12, 6, $pers_body, 'LBT', 0, 'C', 0);
		    //pers castka
		    $pdf->Cell(0, 6, number_format($pers_castka, 0, ',', ' '), 'BTR', 0, 'R', 0);

		    $pdf->Ln();
		    $sumPersCastka += $pers_castka;
		    $sumCilCastka += $cil_castka;
		}
		//echo "<hr>";
	    }
	    $yTableDetailEnd = $pdf->GetY();
	    //sumy
	    $pdf->SetFont("FreeSans", "B", 9);
	    $pdf->Cell(70 + 8 + 10, 6, "Celkem (normMonat)", 'LT', 0, 'L', 0);
	    $pdf->SetFillColor(255, 255, 230);
	    //cil body
	    $pdf->Cell(11, 6, "", 'LT', 0, 'C', 1);
	    //cil castka
	    $pdf->Cell(18, 6, number_format($sumCilCastka, 0, ',', ' '), 'RT', 0, 'R', 1);
	    //dummy
	    $pdf->Cell(10, 6, "", '0', 0, 'L', 0);
	    //firma body
	    $pdf->Cell(12, 6, "", 'LT', 0, 'C', 0);
	    //pers body
	    $pdf->Cell(12, 6, "", 'LT', 0, 'C', 0);
	    //pers castka
	    $pdf->Cell(0, 6, number_format($sumPersCastka, 0, ',', ' '), 'TR', 0, 'R', 0);
	    $pdf->Ln();

	    //sumy koeficient pritomnosti
	    $pdf->SetFont("FreeSans", "", 7);
	    $pdf->Cell(70 + 8 + 10 + 11 + 18 + 10 + 12 + 12, 6, "Koeficient přítomnosti", 'L', 0, 'L', 0);
	    $pdf->Cell(0, 6, number_format($koeficientPritomnosti, 2, ',', ' '), 'R', 0, 'R', 0);
	    $pdf->Ln();

	    //mzdova polozka
	    $pdf->SetFont("FreeSans", "B", 10);
	    $pdf->Cell(70 + 8 + 10 + 11 + 18 + 10 + 12 + 12, 10, "Mzdová položka - osobní hodnocení Kč:", 'LTB', 0, 'L', 0);
	    $pdf->Cell(0, 10, number_format($koeficientPritomnosti * $sumPersCastka, 0, ',', ' '), 'TBR', 0, 'R', 0);
	    $pdf->Ln();
	    $xBottom = $pdf->GetX();
	    $yBottom = $pdf->GetY();
	    $pdf->SetLineWidth(0.5);
	    $pdf->Rect($xTop, $yTop, $pdf->getPageWidth()-PDF_MARGIN_LEFT-5-PDF_MARGIN_RIGHT, $yBottom-$yTop, 'D');
	    $pdf->Rect($xTop, $yTop, $pdf->getPageWidth()-PDF_MARGIN_LEFT-5-PDF_MARGIN_RIGHT, $yTableHeader-$yTop, 'D');
	    $pdf->Rect($xTop, $yTableDetailEnd, $pdf->getPageWidth()-PDF_MARGIN_LEFT-5-PDF_MARGIN_RIGHT, 12, 'D');
	}
    }
}


$pdf->Output();
?>
