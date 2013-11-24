<?php
/*
 * Created on 30.11.2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');
 
$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);
//pprepo   joijfdgfdg
//fj  osifjs

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "DXXX Warenbegleitschein", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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



// prvni stranka
$pdf->AddPage();
//pageheader($pdf,$cells_header,5);
		/**
	 	 * Print Barcode.
		 * @param int $x x position in user units
		 * @param int $y y position in user units
		 * @param int $w width in user units
		 * @param int $h height position in user units
		 * @param string $type type of barcode (I25, C128A, C128B, C128C, C39)
		 * @param string $style barcode style
		 * @param string $font font for text
		 * @param int $xres x resolution
		 * @param string $code code to print
		 */
		 
$mmNaZnak=9;
$prirustek=20;
$vyska=15;

// poznamka

$test="P102-6341";
$krok=0;
$pdf->SetFont("FreeSans", "", 9);
$pdf->MyMultiCell(20,4,"PART NO.\n(P)",'T','L',0);
$pdf->SetFont("FreeSans", "B", 28);
$pdf->Cell(0,10,substr($test,1),'T',1,'L',0);
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, $vyska, "C39", "", "", "", $test);
$pdf->SetY($pdf->GetY()+$vyska+3);

$test="Q1";
$pdf->SetFont("FreeSans", "", 9);
$pdf->MyMultiCell(20,4,"QUANTITY\n(Q)",'T','L',0);

$pdf->SetFont("FreeSans", "", 24);
$pdf->Cell(0,10,substr($test,1),'T',1,'L',0);
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, $vyska, "C39", "", "", "", $test);
$pdf->SetY($pdf->GetY()+$vyska+3);


$test="S16619101";
$pdf->SetFont("FreeSans", "", 9);
$pdf->MyMultiCell(20,4,"SERIAL\n(S)",'T','L',0);

$pdf->SetFont("FreeSans", "", 24);
$pdf->Cell(0,10,substr($test,1),'T',1,'L',0);
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, $vyska, "C39", "", "", "", $test);
$pdf->SetY($pdf->GetY()+$vyska+3);

$pdf->Output();
?>
