<?php
require_once '../security.php';
//require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "F355";
$doc_subject = "F355";
$doc_keywords = "F355";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$data = file_get_contents("php://input");

$o = json_decode($data);

$importAktual = $o->importAktual;
$teilAktual = $o->teilAktual;
$fehlerArray = $o->fehlerArray;
$sumaKs = intval($o->sumaKs);

$bLeer = $importAktual==NULL?TRUE:FALSE;

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "F355 - Māngelbericht", '');
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items
$pdf->SetFont("FreeSans", "", 9);
$pdf->SetAutoPageBreak(FALSE,15);

$pdf->setPrintHeader(TRUE);
$pdf->setPrintFooter(TRUE);
$pdf->setCellPaddings(3, 1, 3, 1);
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);


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

//hlavicka

//plattennr
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(30, 5, 'Platten-Nr:', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$obsah = $bLeer?'':$teilAktual->teillang;
$pdf->Cell(30, 5, $obsah, '0', FALSE, 'L', 0);

//teilnr
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(30, 5, 'TeilNr:', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$obsah = $bLeer?'':$teilAktual->Teil;
$pdf->Cell(20, 5, $obsah, '0', FALSE, 'L', 0);

//teilnr
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(20, 5, 'Artikelcode:', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$obsah = $bLeer?'':$teilAktual->artikelCode;
$pdf->Cell(0, 5, $obsah, '0', FALSE, 'L', 0);

$pdf->Ln();

//dummy
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(30, 5, '', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$pdf->Cell(30, 5, '', '0', FALSE, 'L', 0);

//teilbezeichnung
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(30, 5, 'Bezeichnung:', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$obsah = $bLeer?'':$teilAktual->Teilbez;
$pdf->Cell(0, 5, $obsah, '0', FALSE, 'L', 0);

$pdf->Ln();

//import
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(30, 5, 'Auftrag (IM):', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$obsah = $bLeer?'':$importAktual->auftragsnr;
$pdf->Cell(30, 5, $obsah, '0', FALSE, 'L', 0);

//fremdauftrag
$pdf->SetFont("FreeSans", "B", 9);
$pdf->Cell(30, 5, 'Fremd Auftrag:', '0', FALSE, 'L', 0);
$pdf->SetFont("FreeSans", "", 9);
$obsah = $bLeer?'':$importAktual->fremdauftr;
$pdf->Cell(0, 5, $obsah, '0', FALSE, 'L', 0);

$pdf->Ln();

//oddelovac
$pdf->SetFillColor(200,200,200);
$pdf->SetFont("FreeSans", "", 2);
$pdf->Cell(0, 1, '', 'B', TRUE, 'L', TRUE);
$pdf->Ln(5);

//tabulka

//zahlavi
$druhW = 45;
$popisW = 60;
$dummyW = 2;
$ksW = 23;
$ks_kemperW = 23;
$ks_nacharbeitW = 0;
$mezeraRows = 3;
$povolenyProstor = 175;

$pdf->SetFont("FreeSans", "B", 9);
$pdf->MultiCell($druhW, 2*5, "Aufgetretene Fehler\nDruh vady", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($popisW, 2*5, "Fehlerbeschreibung\nPopis chyby", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($dummyW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($ksW, 2*5, "Stūckzahl\nAbydos", 'B', 'R', FALSE, FALSE);
$pdf->MultiCell($dummyW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($ks_kemperW, 2*5, "Stūckzahl\nKemper", 'B', 'R', FALSE, FALSE);
$pdf->MultiCell($dummyW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($ks_nacharbeitW, 2*5, "Nacharbeit\nKemper", 'B', 'R', FALSE, FALSE);
$pdf->Ln();

//kousek posun dolu
$pdf->SetY($pdf->GetY()+$mezeraRows);
$rowHeight = 10;
$mezeraRows = $rowHeight/3;

//spocitat jestli se mi do stanoveneho prostoru vejdou vsechy radky
$pocetRadku = count($fehlerArray);
$potrebnyProstor = $pocetRadku * ($rowHeight+$mezeraRows);
if($potrebnyProstor>$povolenyProstor){
    //zmensim vysku radku tak , aby se veslo do povoleneho prostoru
    $rowGes = $povolenyProstor / $pocetRadku;
    $rowHeight = $rowGes / (1+1/3);
    $mezeraRows = $rowHeight/3;
}
//$pdf->MultiCell($ksW, 5, "potrebuji prostor : $potrebnyProstor", 'B', 'R', FALSE, FALSE);
//$pdf->Ln();
//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell)
$pdf->SetFont("FreeSans", "", 9);
foreach ($fehlerArray as $f){
    $obsah = $f->druh;
    $pdf->MultiCell($druhW, $rowHeight ,$obsah , '0', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $obsah = $bLeer?'':trim($f->popis);
    $pdf->MultiCell($popisW, $rowHeight,$obsah , '1', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $pdf->MultiCell($dummyW, $rowHeight, "\n", '0', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $obsah = $bLeer?'':trim($f->ks);
    $pdf->MultiCell($ksW, $rowHeight ,$obsah , '1', 'R', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $pdf->MultiCell($dummyW, $rowHeight, "\n", '0', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $obsah = $bLeer?'':trim($f->ks_kemper);
    $pdf->MultiCell($ks_kemperW, $rowHeight ,$obsah , '1', 'R', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $pdf->MultiCell($dummyW, $rowHeight, "\n", '0', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $obsah = $bLeer?'':trim($f->ks_nacharbeit);
    $pdf->MultiCell($ks_nacharbeitW, $rowHeight ,$obsah , '1', 'R', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,$rowHeight,'M',TRUE);
    $pdf->Ln();

    //kousek posun dolu
    $pdf->SetY($pdf->GetY()+$mezeraRows);
}

// poftrhnout .... daaaf fi fofolu ?
$pdf->SetFont("FreeSans", "", 1);
$pdf->Cell(0, 1, '', 'BT', TRUE, 'L', TRUE);

//suma
$pdf->SetFont("FreeSans", "B", 9);
$pdf->MultiCell($druhW, 2*5, "Summe\nsoučet", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($popisW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$pdf->MultiCell($dummyW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$obsah = $bLeer?'':number_format($sumaKs,0,',',' ');
$pdf->MultiCell($ksW, 2*5 , $obsah, '1', 'R', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*5,'M',TRUE);
$pdf->MultiCell($dummyW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$obsah = '';
$pdf->MultiCell($ks_kemperW, 2*5 , $obsah, '1', 'R', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*5,'M',TRUE);
$pdf->MultiCell($dummyW, 2*5, "\n", 'B', 'L', FALSE, FALSE);
$obsah = '';
$pdf->MultiCell($ks_nacharbeitW, 2*5 , $obsah, '1', 'R', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*5,'M',TRUE);
$pdf->Ln(15);
$pdf->SetFont("FreeSans", "I", 9);
$pdf->Cell(0, 5, "Bemerkung:");

// pole pro datum a podpis
$pdf->SetFont("FreeSans", "", 9);

$pdf->SetY($pdf->getPageHeight()- PDF_MARGIN_BOTTOM - 35);
$pdf->MultiCell(50, 2*10 , 'Datum:', 'B', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*10,'B',TRUE);
//dummy
$pdf->MultiCell(20, 2*10 , '', '0', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*10,'B',TRUE);
$pdf->MultiCell(0, 2*10 , 'Unterschrift / podpis:', 'B', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*10,'B',TRUE);
$pdf->Text(PDF_MARGIN_LEFT, $pdf->GetY()+2*10, "Abydos");
$pdf->Text(PDF_MARGIN_LEFT+50+20, $pdf->GetY(), "Abydos");

$pdf->SetY($pdf->getPageHeight()- PDF_MARGIN_BOTTOM - 15);
$pdf->MultiCell(50, 2*10 , 'Datum:', 'B', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*10,'B',TRUE);
//dummy
$pdf->MultiCell(20, 2*10 , '', '0', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*10,'B',TRUE);
$pdf->MultiCell(0, 2*10 , 'Unterschrift / podpis:', 'B', 'L', FALSE, FALSE,'','',TRUE,FALSE,FALSE,FALSE,2*10,'B',TRUE);
$pdf->Text(PDF_MARGIN_LEFT, $pdf->GetY()+2*10, "Kemper");
$pdf->Text(PDF_MARGIN_LEFT+50+20, $pdf->GetY(), "Kemper");

$pdf->Text(PDF_MARGIN_LEFT-3, $pdf->GetY()+12, "QMF G 19/5");
// pokud mam vyplneny i import, pridam dalsi stranku
if(!$bLeer){
    $pdf->setPrintHeader(FALSE);
    $pdf->setPrintFooter(FALSE);
//    //$pdf->SetHeaderData('', 0,"", '');
    $pdf->AddPage();
    
    $pdf->Image('./images/abydos_logo1.png', 20, 20, 45, 0);
    $pdf->SetFont("FreeSans", "B", 90);
    $pdf->Cell(0, 70, 'Ausschuss', '0',FALSE,'L');
    $pdf->Ln(50);
    $pdf->SetFont("FreeSans", "B", 45);
    $pdf->Cell(0, 50, 'Fremd Auftrag: '.$importAktual->fremdauftr, '0',FALSE,'L');
    $pdf->Ln(30);
    $pdf->SetFont("FreeSans", "B", 45);
    $obsah = $bLeer?'':$teilAktual->teillang;
    $pdf->Cell(0, 50, 'Platten-Nr: '.$obsah, '0',FALSE,'L');
    
    $pdf->StartTransform();
    $pdf->SetY($pdf->getPageHeight()/2);
    $pdf->Translate($pdf->getPageWidth()-25,$pdf->getPageHeight()/2-25);
    $pdf->Rotate(180);
    
    //$pdf->MirrorV();
    $pdf->Image('./images/abydos_logo1.png', 20, '', 45, 0);
    $pdf->SetFont("FreeSans", "B", 90);
    $pdf->Cell(0, 70, 'Ausschuss', '0',FALSE,'L');
    $pdf->Ln(50);
    $pdf->SetFont("FreeSans", "B", 45);
    $pdf->Cell(0, 50, 'Fremd Auftrag: '.$importAktual->fremdauftr, '0',FALSE,'L');
    $pdf->Ln(30);
    $pdf->SetFont("FreeSans", "B", 45);
    $obsah = $bLeer?'':$teilAktual->teillang;
    $pdf->Cell(0, 50, 'Platten-Nr: '.$obsah, '0',FALSE,'L');
    
    $pdf->StopTransform();
    
}
$stamp = date('YmdHis');
//Close and output PDF document

$savePath =$apl->getGdatPath()."Aby 99 Nezarazene/eforms/$doc_title";
$filename = sprintf("F355_%s.pdf",$stamp);
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
	'importAktual'=>$importAktual,
	'teilAktual'=>$teilAktual,
	'fehlerArray'=>$fehlerArray,
	'bLeer'=>$bLeer,
    ));