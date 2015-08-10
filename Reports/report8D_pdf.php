<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "8D";
$doc_subject = "8D Report";
$doc_keywords = "8D";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$data = file_get_contents("php://input");

$o = json_decode($data);
$rekl = $o->rekl;

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "8D-Report ($rekl->rekl_nr)", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items
$pdf->SetFont("FreeSans", "", 8);
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

//1
$fill = TRUE;
//FFE699
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(255, 230, 100);
$pdf->MultiCell(45, 3, "Teilenummer / Part Number", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(57, 3, "Reklamationsnummer / Complaint no.", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Reklamationsdatum / Complaint opening date", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 7);
$pdf->MultiCell(45, 7, "$rekl->teil", 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->MultiCell(57, 7, "$rekl->kd_rekl_nr", 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->MultiCell(0, 7, substr($rekl->rekl_datum,0,10), 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 7, 'M', TRUE);
$pdf->Ln(7);

//2
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(45, 3, "Teilebezeichnung / Part name", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(57, 3, "Zeichnungsnummer / Drawing no.", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Zeichnungsstand / Drawing revision", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 7);

$pdf->MultiCell(45, 5, $rekl->teilbezeichnung, 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 5, 'M', TRUE);
$pdf->MultiCell(57, 5, "$rekl->zeichnungsnummer", 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 5, 'M', TRUE);
$pdf->MultiCell(0, 5, $rekl->zeichnungsstand, 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 5, 'M', TRUE);
$pdf->Ln(5);

//3
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(80, 3, "Liefermenge / Quantity delivered", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "Beanstandete Menge / Quantity claimed", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 7);
$pdf->MultiCell(80, 5, number_format($rekl->stk_expediert, 0, ',',' '), 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 5, 'M', TRUE);
$pdf->MultiCell(0, 5, number_format($rekl->stk_reklammiert, 0, ',',' '), 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 5, 'M', TRUE);
$pdf->Ln(5);

//4
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(80, 3, "1 Team", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "2 Problembeschreibung / Problem description", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 7);
$fillTab = "                                            ";
$teamArray = array(
    "Name / Abt. / Dept",
);

if(trim($rekl->team_bespr_name1)!="" && $rekl->team_bespr_leiter1==0){
    array_push($teamArray, $rekl->team_bespr_name1." / ".$rekl->team_bespr_abteilung1);
}
if($rekl->team_bespr_leiter1==1){
    $leiterName = $rekl->team_bespr_name1;
    $leiterAbt = $rekl->team_bespr_abteilung1;
}
if(trim($rekl->team_bespr_name2)!="" && $rekl->team_bespr_leiter2==0){
    array_push($teamArray, $rekl->team_bespr_name2." / ".$rekl->team_bespr_abteilung2);
}
if($rekl->team_bespr_leiter2==1){
    $leiterName = $rekl->team_bespr_name2;
    $leiterAbt = $rekl->team_bespr_abteilung2;
}
if(trim($rekl->team_bespr_name3)!="" && $rekl->team_bespr_leiter3==0){
    array_push($teamArray, $rekl->team_bespr_name3." / ".$rekl->team_bespr_abteilung3);
}
if($rekl->team_bespr_leiter3==1){
    $leiterName = $rekl->team_bespr_name3;
    $leiterAbt = $rekl->team_bespr_abteilung3;
}
if(trim($rekl->team_bespr_name4)!="" && $rekl->team_bespr_leiter4==0){
    array_push($teamArray, $rekl->team_bespr_name4." / ".$rekl->team_bespr_abteilung4);
}
if($rekl->team_bespr_leiter4==1){
    $leiterName = $rekl->team_bespr_name4;
    $leiterAbt = $rekl->team_bespr_abteilung4;
}
if(trim($rekl->team_bespr_name5)!="" && $rekl->team_bespr_leiter5==0){
    array_push($teamArray, $rekl->team_bespr_name5." / ".$rekl->team_bespr_abteilung5);
}
if($rekl->team_bespr_leiter5==1){
    $leiterName = $rekl->team_bespr_name5;
    $leiterAbt = $rekl->team_bespr_abteilung5;
}

array_push($teamArray, "Teamleiter / Champion");
if(trim($leiterName)){
    array_push($teamArray, $leiterName." / ".$leiterAbt);
}

$teamString = join("\n", $teamArray);

$pdf->MultiCell(80, 24, $teamString, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 24, 'T', TRUE);
$pdf->MultiCell(0, 24, $rekl->beschr_abweichung, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 24, 'M', TRUE);
$pdf->Ln(24);

//5
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(135, 6, "3 Sofortmassmahme(n) / Corrective action(s)", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->MultiCell(0, 6, "Einfūhrungsdatum\nImplementation date", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->Ln(6);
$pdf->SetFont("FreeSans", "", 7);
$y = $pdf->GetY();
$x = $pdf->GetX();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_3a, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_3a_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_3b, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_3b_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_3c, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_3c_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
// a cely to oramovat
$vyskaSekce = 30;
$pdf->Rect($x, $y, $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT, $vyskaSekce);
$pdf->Rect($x+135, $y, $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-135, $vyskaSekce);
$pdf->SetX($x);
$pdf->SetY($y+$vyskaSekce);

//6
$pdf->SetFont("FreeSans", "B", 6);
$erst = $rekl->report8D_4_erstmalig == 0?"   ":"X";
$wieder = $rekl->report8D_4_wiederholfehler == 0?"   ":"X";
$pdf->MultiCell(0, 3, "4 Fehlerursache(n) / Root cause(s)                              [$erst] Fehler tritt erstmalig auf / First occurence defect    [$wieder] Wiederholfehler / Repetitive defect", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 7);
$pdf->MultiCell(0, 25, $rekl->beschr_ursache, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 25, 'M', TRUE);
$pdf->Ln(25);

//7 
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(113, 6, "5 Geplante Abstellmassnahme(n) / Chosen corrective action(s)", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->MultiCell(0, 6, "5a Wirksamkeitsprūfung mit methode\nVerification check by method of", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->Ln(6);
$pdf->SetFont("FreeSans", "", 7);
$pdf->MultiCell(113, 21, $rekl->report8D_5, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 21, 'M', TRUE);
$pdf->MultiCell(0, 21, $rekl->report8D_5a, 'LRBT', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 21, 'M', TRUE);
$pdf->Ln(21);

//8
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(135, 6, "6 Eingefūhrte Abstellmassmahme(n) / Implemented corrective action(s)", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->MultiCell(0, 6, "Einfūhrungsdatum\nImplementation date", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->Ln(6);
$pdf->SetFont("FreeSans", "", 7);
$y = $pdf->GetY();
$x = $pdf->GetX();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_6a, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_6a_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_6b, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_6b_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_6c, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_6c_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
// a cely to oramovat
$pdf->Rect($x, $y, $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT, $vyskaSekce);
$pdf->Rect($x+135, $y, $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-135, $vyskaSekce);
$pdf->SetX($x);
$pdf->SetY($y+$vyskaSekce);

//9
$pdf->SetFont("FreeSans", "B", 6);
$pdf->MultiCell(135, 6, "7 Massnahme(n) gegen Wiederholfehler / Action(s) to prevent recurrence\nFūr jede Massnahme ist ein Nachweis beizulegen / For each action below a documented evidence must be attached", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->MultiCell(0, 6, "Einfūhrungsdatum\nImplementation date", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 6, 'B', TRUE);
$pdf->Ln(6);
$pdf->SetFont("FreeSans", "", 7);
$y = $pdf->GetY();
$x = $pdf->GetX();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_7a, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_7a_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_7b, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_7b_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
$nc = $pdf->MultiCell(135, 0, $rekl->report8D_7c, '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
$pdf->MultiCell(0, 0, AplDB::toDBDate($rekl->report8D_7c_einsatzdatum1), '0', 'L', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 0, 'B', TRUE);
for($i=0;$i<$nc;$i++) $pdf->Ln();
// a cely to oramovat
$pdf->Rect($x, $y, $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT, $vyskaSekce);
$pdf->Rect($x+135, $y, $pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-135, $vyskaSekce);
$pdf->SetX($x);
$pdf->SetY($y+$vyskaSekce);

//10
$pdf->SetFont("FreeSans", "B", 6);

$pdf->MultiCell(60, 3, "Datum / erstellt von / Unterschrift", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(60, 3, "Datum / geprūft von / Unterschrift", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->MultiCell(0, 3, "8 Teamerfolg / Congratulations", 'LRBT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 3, 'B', TRUE);
$pdf->Ln(3);
$pdf->SetFont("FreeSans", "", 7);
$dat = date('Y-m-d');
$erstellt = $_SESSION['user'];$realnameA = $apl->getUserInfoArray($erstellt);
$realname = $realnameA['realname'];
$pdf->MultiCell(60, 20, "$dat\n$realname\n\n\n\n\n...................................................................", 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 20, 'B', TRUE);
$pdf->MultiCell(60, 20, "", 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 20, 'B', TRUE);
$pdf->MultiCell(0, 20, "\n\n\n\n\n...................................................................\nUnterschrift / Signature Teamleiter / Champion", 'LRBT', 'C', FALSE, 0, '', '', TRUE, 0, FALSE, TRUE, 20, 'B', TRUE);
$pdf->Ln(25);


//11
$pdf->SetFont("FreeSans", "B", 6);
$pdf->SetFillColor(255, 230, 180);
$pdf->MultiCell(60, 20, "Entscheid / Decision", 'LBT', 'C', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 20, 'M', TRUE);
$pdf->MultiCell(60, 20, "8D-Report akzeptiert / accepted\n\n[   ] Ja / Yes\n[   ] Nein / No :\n\nUpdate erforderlich bis / required until", 'BT', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 20, 'T', TRUE);
$pdf->MultiCell(0, 20, "Abschluss / Closure\n\n\n\n...............................................................................\nDatum/Date      Name / Unterschrift / Signature", 'BTR', 'L', $fill, 0, '', '', TRUE, 0, FALSE, TRUE, 20, 'B', TRUE);

$stamp = date('YmdHis');
//Close and output PDF document
$savePath = $rekl->savePath;
$reklnr = strtr($rekl->rekl_nr, '.', '-');
$filename = sprintf("8D-Report_%s_%s.pdf",$reklnr,$stamp);
//otestovat zda existuje slozka
if(!file_exists($savePath)){
    mkdir($savePath,TRUE);
}
			
$pdf->Output($savePath.'/'.$filename, 'F');

echo json_encode(array(
	'rekl'=>$rekl,
    ));