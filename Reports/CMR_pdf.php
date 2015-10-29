<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "CMR";
$doc_subject = "CMR";
$doc_keywords = "CMR";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$data = file_get_contents("php://input");

$o = json_decode($data);

$auftragInfo = $o->auftragInfo;
$rundlaufInfo = $o->rundlaufInfo;
$zielOrtInfo = $o->zielOrtInfo;
$zielOrtInfoStandard = $o->zielOrtInfoStandard;
$pokynyProOdesilatele = $o->pokynyProOdesilatele;
$username = $o->username;
$usernameFull = $o->usernameFull;
$palArray = $o->palArray;

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Dxxx - Frachtbrief - Speditionauftrag", $auftragInfo->auftragsnr);
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

//1
$fill = TRUE;
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(230, 230, 230);
$pdf->MultiCell(90, 3, "Odesílatel (jméno, adresa, země) / Absender (Name, Adresse, Land)", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "EX", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "B", 8);
$pdf->MultiCell(90, 27, "Abydos s.r.o.\nHazlov 247\nCZ 351 32 Hazlov", 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->SetFont("FreeSans", "B", 11);
$pdf->MultiCell(0, 27, $auftragInfo->auftragsnr, 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->Ln(27);

//2
$fill = TRUE;
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(230, 230, 230);
$pdf->MultiCell(90, 3, "Příjemce (jméno, adresa, země) / Empfānger (Name, Adresse, Land)", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Dopravce (jméno, adresa, země) / Frachtfūhrer (Name, Anschrift, Land)", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 8);
$obsah = $zielOrtInfo->firma;
if(intval($zielOrtInfo->standard)!=10){
    $obsah.= " in Name ".$zielOrtInfoStandard->firma;
}
$obsah.= "\n".$zielOrtInfo->strasse;
$obsah.= "\n".$zielOrtInfo->stat." - ".$zielOrtInfo->plz." ".$zielOrtInfo->ort;

$pdf->MultiCell(90, 27, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->SetFont("FreeSans", "", 8);
$obsah = $rundlaufInfo->spediteurname;
$pdf->MultiCell(0, 27, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->Ln(27);

//3
$fill = TRUE;
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(230, 230, 230);
$pdf->MultiCell(20, 2*3, "Počet\nAnzahl", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(55, 2*3, "Druh obalu\nArt der Verpackung", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(70, 2*3, "Označení zboží\nBezeichnung des Gutes", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 2*3, "Hr.hmotnost v kg\nBruttogewicht kg", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(2*3);

$pdf->SetFont("FreeSans", "", 8);
$pocetBeh = 0;
foreach ($palArray as $pal){
    if(strlen(trim($pal->behname))>0){
	$obsah = $pal->sum_stk;
	$pdf->MultiCell(20, 7, $obsah, 'LRBT', 'R', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
	
	$obsah = $pal->behname;
	$pdf->MultiCell(55, 7, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
	
	$obsah = $pal->zustand_text;
	$pdf->MultiCell(70, 7, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
	
	$obsah = '';
	$pdf->MultiCell(0, 7, $obsah, 'LRBT', 'R', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
	
	$pdf->Ln(7);
	
	$pocetBeh++;
    }
}

$zbyvaRadku = 12-$pocetBeh;
$obsah = '';
$pdf->MultiCell(0, 7*$zbyvaRadku, $obsah, 'LRT', 'R', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->Ln(7*$zbyvaRadku);

$obsah = '';
$pdf->MultiCell(20+55+70, 7, $obsah, 'LB', 'R', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$obsah = number_format($rundlaufInfo->bruttogewicht,0,',',' ');
$pdf->MultiCell(0, 7, $obsah, 'LRBT', 'R', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->Ln(7);

//4
$fill = TRUE;
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(230, 230, 230);
$pdf->MultiCell(90, 3, "Pokyny odesílatele / Anweisungen des Absenders", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Místo a datum vystavení / Ort und Datum der Ausfertigung", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 8);
$obsah = $pokynyProOdesilatele;
$pdf->MultiCell(90, 30, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->SetFont("FreeSans", "", 8);
$obsah = "Hazlov, ".date('d.m.Y');
$pdf->MultiCell(45, 30, $obsah, 'LBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'B', TRUE);
$obsah = $usernameFull;
$pdf->MultiCell(45, 30, $obsah, 'RBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'B', TRUE);
$pdf->Ln(30);

//5
$fill = TRUE;
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(230, 230, 230);
$pdf->MultiCell(90, 3, "SPZ / Amtl. Kennzeichnung", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Datum, podpis a razítko dopravce / Datum, Unterschrift und Stempeldes Frachtfūhrers", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 8);
$obsah = "Vozidlo - tahač / Kfz";
$obsah.= "\nPřívěs - návěs / Anhānger";
$obsah.= "\nJméno řidiče / Name des Fahrers";
$pdf->MultiCell(45, 30, $obsah, 'LBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$obsah = $rundlaufInfo->lkw_kz;
$obsah.= "\n".$rundlaufInfo->naves_kz;
$obsah.= "\n".$rundlaufInfo->fahrername;
$pdf->MultiCell(45, 30, $obsah, 'RBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->SetFont("FreeSans", "", 8);
$obsah = '';
$pdf->MultiCell(0, 30, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'B', TRUE);
$pdf->Ln(30);

//6
$fill = TRUE;
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(230, 230, 230);
$pdf->MultiCell(90, 3, "Poznámka / Bemerkung", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Datum, podpis a razítko příjemce / Datum, Unterschrift und Stempel des Empfāngers", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 8);
$obsah = $rundlaufInfo->bemerkung;
$pdf->MultiCell(90, 30, $obsah, 'LBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$obsah = '';
$pdf->MultiCell(0, 30, $obsah, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'B', TRUE);
$pdf->Ln(30);


$stamp = date('YmdHis');
//Close and output PDF document
$kunde = $auftragInfo->kunde;
$savePath =$apl->getGdatPath()."".$apl->getKundeGdatPath($kunde)."/CMR/";
$filename = sprintf("CMR_%s_%s.pdf",$auftragInfo->auftragsnr,$stamp);
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
	'auftragInfo'=>$auftragInfo,
	'rundlaufInfo'=>$rundlaufInfo,
	'zielOrtInfo'=>$zielOrtInfo,
	'zielOrtInfoStandard'=>$zielOrtInfoStandard,
	'pokynyProOdesilatele'=>$pokynyProOdesilatele,
	'username'=>$username,
	'usernameFull'=>$usernameFull,
	'palArray'=>$palArray,
    ));