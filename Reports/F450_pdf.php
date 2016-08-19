<?php
require_once '../security.php';
//require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "F450";
$doc_subject = "F450";
$doc_keywords = "F450";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$data = file_get_contents("php://input");

$o = json_decode($data);

$teile = $o->teile;
$ab = $o->ab;

$bLeer = $importAktual==NULL?TRUE:FALSE;

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "F450 - Skladová karta", '');
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

$pdf->setPrintHeader(TRUE);
$pdf->setPrintFooter(TRUE);
//$pdf->setCellPaddings($left, $top, $right, $bottom)
$pdf->setCellPaddings(0, 1, 3, 1);

//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);


// prvni stranka


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


foreach ($teile as $t){
    $pdf->AddPage();
    $pdf->SetFont("FreeSans", "B", 20);
    //$pdf->Cell(40, 15, $t->amnr, 'LRTB',0,'C', 0,'R');
    
      $pdf->MultiCell(
            60,
            15,
            $t->amnr,
            $border = '0',
            $align = 'R',
            $fill = false,
            $ln = 0,
            $x = 135,
            $y = '',
            $reseth = true,
            $stretch = 0,
            $ishtml = false,
            $autopadding = true,
            $maxh = 15,
            $valign = 'M',
            $fitcell = false
        );
    
    
    

    
    
       $pdf->Ln();
    $pdf->SetFont("FreeSans", "", 9);
    $pdf->Cell(0, 5, $t->text.' '.'('.$t->text1.')', '','L', 1);

$pdf->Ln();
$stamp = date('YmdHis');
//$timestamp = mktime( 0,0,0);
//            $rok = date("Y",$timestamp);
//            $mesic = date("m",$timestamp);
//            $den = date("d",$timestamp);
//            
$time = date('d.m.Y');
$pdf->Cell(45, 5, "Poč. zůstatek ".'('.$time.'): ', '0',0,'L', 0);
$pdf->SetFont("FreeSans", "B", 10);
$pdf->Cell(0, 5, $t->ab, '0', 0, 'L', 0);
//$pdf->Cell(0, 5, $ab, '1','L', 0);
$pdf->Ln();
$pdf->SetFillColor(193, 209, 213);
$pdf->SetFont("FreeSans", "B", 10);
$pdf->Cell(60, 9, "příjem", 'LRTB','','C',1, 0);
$pdf->Cell(60, 9, "výdej", 'LRTB','','C',1, 0);
$pdf->Cell(60, 9, "inventura", 'LRTB',1,'C',1, 0);
$pdf->SetFont("FreeSans", "", 9);

for($i = 0;$i < 3;$i++){
    $pdf->Cell(19, 11, "datum", 'LRTB','','C', 1);
    $pdf->Cell(19, 11, "počet", 'LRTB','','C', 1);
    $pdf->Cell(22, 11, "podpis", 'LRTB','','C', 1);
}
$pdf->Ln();
for($a = 0;$a < 20;$a++){
for($i = 0;$i < 3;$i++){
    $pdf->Cell(19, 9, "", 'LRTB','','C', 0);
    $pdf->Cell(19, 9, "", 'LRTB','','C', 0);
    $pdf->Cell(22, 9, "", 'LRTB','','C', 0);
}
$pdf->Ln();
}
$pdf->Cell(22, 11, "Pozn.:", '','','L', 0);

}
//Close and output PDF document

$savePath =$apl->getGdatPath()."Aby 99 Nezarazene/eforms/$doc_title";
$filename = sprintf("F450_%s.pdf",$stamp);
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
	'teile'=>$teile,
	'bLeer'=>$bLeer,
    ));