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
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", $params);
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



// prvni stranka
$pdf->AddPage();

//MultiCell  This method allows printing text with line breaks. They can be automatic (as soon as the text reaches the right border of the cell) or explicit (via the \n character). As many cells as necessary are output, one below the other. Text can be aligned, centered or justified. The cell block can be framed and the background painted. 
//Parameters:
//$w
//(float) Width of cells. If 0, they extend up to the right margin of the page.
//$h
//(float) Cell minimum height. The cell extends automatically if needed.
//$txt
//(string) String to print
//$border
//(mixed) Indicates if borders must be drawn around the cell. The value can be a number:
//0: no border (default)
//1: frame
//or a string containing some or all of the following characters (in any order):
//L: left
//T: top
//R: right
//B: bottom
//or an array of line styles for each border group - for example: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)))
//$align
//(string) Allows to center or align the text. Possible values are:
//L or empty string: left align
//C: center
//R: right align
//J: justification (default value when $ishtml=false)
//$fill
//(boolean) Indicates if the cell background must be painted (true) or transparent (false).
//$ln
//(int) Indicates where the current position should go after the call. Possible values are:
//0: to the right
//1: to the beginning of the next line [DEFAULT]
//2: below
//$x
//(float) x position in user units
//$y
//(float) y position in user units
//$reseth
//(boolean) if true reset the last cell height (default true).
//$stretch
//(int) font stretch mode:
//0 = disabled
//1 = horizontal scaling only if text is larger than cell width
//2 = forced horizontal scaling to fit cell width
//3 = character spacing only if text is larger than cell width
//4 = forced character spacing to fit cell width
//General font stretching and scaling values will be preserved when possible.
//$ishtml
//(boolean) INTERNAL USE ONLY -- set to true if $txt is HTML content (default = false). Never set this parameter to true, use instead writeHTMLCell() or writeHTML() methods.
//$autopadding
//(boolean) if true, uses internal padding and automatically adjust it to account for line width.
//$maxh
//(float) maximum height. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature. This feature works only when $ishtml=false.
//$valign
//(string) Vertical alignment of text (requires $maxh = $h > 0). Possible values are:
//T: TOP
//M: middle
//B: bottom
//. This feature works only when $ishtml=false and the cell must fit in a single page.
//$fitcell
//(boolean) if true attempt to fit all the text within the cell by reducing the font size (do not work in HTML mode). $maxh must be greater than 0 and wqual to $h.
//Returns:
//Type:
//int
//Description:
//Return the number of cells or 1 for html mode.
//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell)
$fill = FALSE;
$sirkaFull = 135;
$h = 5;
$tSizeNormal = 10;
$ram = '0';
$pdf->SetFont("FreeSans", "", $tSizeNormal);

$pdf->Ln(60);
$pdf->MultiCell(35, $h, $texte->text10, $ram, 'L', $fill, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);

//Anschrift
$anschrift = $persInfo->Vorname." ".$persInfo->Name."\n"
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
$pdf->MultiCell(45, $h, "Převzal a s výtkou souhlasí", $ram, 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);
$pdf->MultiCell(45, $h, "................................\n podpis", $ram, 'C', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);


//dne
$pdf->Ln(5*$h);
$pdf->MultiCell($sirkaFull, $h, "Dne: ................................", $ram, 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', FALSE);

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