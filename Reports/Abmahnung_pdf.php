<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "Abmahnung";
$doc_subject = "Abmahnung";
$doc_keywords = "Abmahnung";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$data = file_get_contents("php://input");

$o = json_decode($data);
$texte = $o->texte;
$abmahnungInfo = $o->abmahnungInfo;
$persInfo = $o->persInfo;
$persDetailInfo = $o->persDetailInfo;

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items
$pdf->SetFont("FreeSans", "", 9);
$pdf->SetAutoPageBreak(TRUE,15);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);


// prvni stranka
$pdf->AddPage();
//Description:
//Return the number of cells or 1 for html mode.
//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell)
$fill = FALSE;
$sirkaFull = 135;
$h = 5;
$tSizeNormal = 11;
$tSizeHlavPapir = 6.5;

$ram = '0';
$okrajXHlavPapir = 170;


$pdf->Ln(22);
$pdf->SetFont("FreeSans", "", $tSizeHlavPapir);
$pdf->MultiCell($sirkaFull, $h, "Abydos s.r.o., CZ - 351 32 Hazlov 247, Česká republika", $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);


$pdf->SetFont("FreeSans", "", $tSizeNormal);

$pdf->Ln(9);
$pdf->MultiCell(35, $h, $texte->text10, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);

//Anschrift
// prijmeni ocistit od znacek
$mezera = strpos($persInfo->Name, ' ');
if($mezera!==FALSE){
    $prijmeni = substr($persInfo->Name, 0, $mezera);
}
else{
    $prijmeni = $persInfo->Name;
}


$anschrift = $persInfo->Vorname." ".$prijmeni."\n"
	.$persDetailInfo->strasse_op."\n"
	.$persDetailInfo->plz_op." ".$persDetailInfo->ort_op;
//$anschrift="asda";
$pdf->MultiCell($sirkaFull, $h, $anschrift, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

// misto datum
$pdf->Ln(20);
// dummy posunuti vpravo
$pdf->MultiCell(95, $h, "", $ram, 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
$pdf->MultiCell($sirkaFull-95, $h, $texte->text20, $ram, 'R', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);

// Výtka
$pdf->Ln(10);
$pdf->SetFont("FreeSans", "B", 12);
$pdf->MultiCell($sirkaFull, 8, "Výtka", $ram, 'C', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);

// osobni cislo
$pdf->SetFont("FreeSans", "", $tSizeNormal);
$pdf->MultiCell($sirkaFull, $h, "os. č. ".$abmahnungInfo->persnr, $ram, 'C', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);

//osloveni
$pdf->Ln($h);
$pdf->MultiCell($sirkaFull, $h, $texte->text30, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);

//text 40
$pdf->Ln($h);
$pdf->MultiCell($sirkaFull, $h, $texte->text40, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

//text 50
//$pdf->Ln($h+10);
$pdf->Ln($h);
$pdf->MultiCell($sirkaFull, $h, $texte->text50, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

//text 60
$pdf->Ln($h);
$pdf->MultiCell($sirkaFull, $h, $texte->text60, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

//jednatelka
$pdf->Ln(3*$h);
$jednatelka = "................................\n"
	."jednatelka";
$pdf->MultiCell(35, $h, $jednatelka, $ram, 'C', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->MultiCell(40, $h, "", $ram, 'C', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->MultiCell($sirkaFull-35-40, $h, $texte->text70, $ram, 'C', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

//prevzal a souhlasi
$pdf->Ln(5*$h);
$pdf->MultiCell(50, $h, "Převzal a s výtkou souhlasí", $ram, 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->MultiCell(45, $h, "................................\n podpis", $ram, 'C', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);


//dne
$pdf->Ln(3*$h);
$pdf->MultiCell($sirkaFull, $h, "Dne: ................................", $ram, 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);


// a ted imitace hlavickoveho papiru
// logo
//$pdf->Image($file, $x, $y, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $ismask, $imgmask, $border)
$pdf->Image("../images/logo1.jpg", $okrajXHlavPapir, 38,30);
$pdf->SetFont("FreeSans", "", $tSizeHlavPapir);
$pdf->MultiCell(35, 0, "Abydos s.r.o.\n\nCZ - 351 32 Hazlov 247\n\nČeská republika\n\n", 'B', 'L', $fill, 1, $okrajXHlavPapir, 90, TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->SetX($okrajXHlavPapir);
$pdf->MultiCell(35, 0, "\nTelefon:\n\n++420 354 595 337\n\nFax:\n\n++420 354 596 993\n\nemail:\n\ninfo@abydos.cz\n\n", 'B', 'L', $fill, 1, '','', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->SetX($okrajXHlavPapir);
$pdf->MultiCell(35, 0, "\nDIČ/Ust-ID-Nr.:\n\nCZ25206958\n\n", 'B', 'L', $fill, 1, '','', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->SetX($okrajXHlavPapir);
$pdf->MultiCell(35, 0, "\nBankovní spojení/\n\nBankverbindung\n\nCZ - Citibank a.s. Praha\n\nSm. číslo/BLZ: 2600\n\nČ.ú./Konto:\n\n2514640100 (CZK)\n\n2514640207 (EUR)\n\nD - Raiffeisenbank Riedenburg\n\nSm. číslo/BLZ: 72169831\n\nČ.ú./Konto: 62251 (EUR)", '0', 'L', $fill, 1, '','', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

$stamp = date('YmdHis');
//Close and output PDF document
$savePath = "/mnt/gdat/Dat/Aby 18 Mitarbeiter -/02 Arbeitsverhaltnis - Pr.smlouvy,dodatky,skonceni PP/08 Slozky_novych_MA/".$abmahnungInfo->persnr;
$filename = sprintf("vytka_%s_%s.pdf",$abmahnungInfo->persnr,$stamp);
//otestovat zda existuje slozka
if(!file_exists($savePath)){
    mkdir($savePath,TRUE);
}
			
$pdf->Output($savePath.'/'.$filename, 'F');
//$pdf->Output();

$hrefPath = str_replace("/Dat", "", $savePath);

echo json_encode(array(
	'filePath'=>$savePath."/".$filename,
	'filename'=>$filename,
	'pdfPath'=>  substr($hrefPath, 4)."/".$filename,
    ));