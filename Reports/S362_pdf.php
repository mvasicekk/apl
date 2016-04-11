<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "S362";
$doc_subject = "S362";
$doc_keywords = "S362";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$reklnr = $_GET['reklnr'];

// muzu mit vic reklamaci se stejnym cislem
$reklInfo = $apl->getReklInfoFromReklNr($reklnr);

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";

$teil = $reklInfo[0]['teil'];
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S362 - Reklamationsbericht ( Teil: $teil )", $reklnr);
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
$abmahnungHeaderHeight = 5;
$abmahnungRowHeight = 5;

$schulungHeaderHeight = 5;
$schulungRowHeight = 10;

$problemBeschreibungMinHeight = 1;
$problemBeschreibungMaxHeight = 15;

$persTabsXOffset = 160;

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

if($reklInfo!==NULL){
    foreach ($reklInfo as $rekl){
	//AplDB::varDump($rekl);
	$abmahnungen = $apl->getAbmahnungenForReklamation($rekl['id']);
	$schulungen = $apl->getSchulungenForReklamation($rekl['id']);
	$teilInfo = $apl->getTeilInfoArray($rekl['teil']);
//	AplDB::varDump($abmahnungen);
	$pdf->AddPage();
	$yTop = $pdf->getY();
	
	//grundinfo
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Grundinfo", '0', 1, 'L', $fill);
	//reklnr
	$pdf->Cell(20, $schulungHeaderHeight, "Rekl.Nr.:", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 0;
	$obsah = $rekl['rekl_nr'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//erhalten am
	$pdf->Cell(20, $schulungHeaderHeight, "Erhalten am: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 0;
	$obsah = date('d.m.Y',strtotime($rekl['rekl_datum']));
	$pdf->SetFont("FreeSans", "", 7);
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//bearbeiter_kunde
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Bearbeiter Kd: ", '0', 0, 'L', 0);
	$fill = 0;
	$obsah = $rekl['bearbeiter_kunde'];
	$pdf->SetFont("FreeSans", "", 7);
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 1, 'L', 0);
	
	//kd reklnr
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20, $schulungHeaderHeight, "Kd Rekl.Nr.:", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$fill = 0;
	$obsah = $rekl['kd_rekl_nr'];
	$pdf->SetFont("FreeSans", "", 7);
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//erledigt am
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20, $schulungHeaderHeight, "Erledigt am:", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$fill = 0;
	if(strtotime($rekl['rekl_erledigt_am'])){
	    $obsah = date('d.m.Y',  strtotime($rekl['rekl_erledigt_am']));
	}
	else{
	    $obsah = "";
	}
	$pdf->SetFont("FreeSans", "", 7);
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	// per Email, .....
	if($rekl['mt_email']!="0"){
	    $obsah = "per Email";
	}
	if($rekl['mt_telefon']!="0"){
	    $obsah = "\nper Telefon";
	}
	if($rekl['mt_mund']!="0"){
	    $obsah = "\nMūndlich";
	}
	
	$pdf->MultiCell(55, $schulungHeaderHeight, $obsah, '0', 'L', 0, 1, '', '', TRUE, 0, FALSE, TRUE, 0, 'T', TRUE);
	
	//kdkd reklnr
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20, $schulungHeaderHeight, "KdKd Rekl.Nr.:", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$fill = 0;
	$obsah = $rekl['kd_kd_rekl_nr'];
	$pdf->SetFont("FreeSans", "", 7);
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 1, 'L', 0);
	
	$pdf->Ln(5);
	
	//Teilinfo - Identifikation
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Teilinfo - Identifikation", '0', 1, 'L', $fill);
	
	//teil
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Teil: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "B", 7);
	$obsah = $rekl['teil'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//liefermenge
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Liefermenge: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = $rekl['stk_expediert'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//auftrag
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Auftrag: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = $rekl['export'];
	$max = 20;
	if(strlen($obsah)>$max){
	    $obsah = substr($obsah, 0, $max)."...";
	}

	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 1, 'L', 0);
	
	//teil gewicht
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Gewicht : ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = number_format($teilInfo['Gew'],3,',',' ')." kg";
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//reklamiert
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Reklamiert: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = $rekl['stk_reklammiert'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//behaelter
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Behālter: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = $rekl['export_beh'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 1, 'L', 0);
	
	//charge / GT
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Charge / GT: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$max = 17;
	$obsah = $rekl['giesstag'];
	if(strlen($obsah)>$max){
	    $obsah = substr($obsah, 0, $max)."...";
	}
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//stempel
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Stempel: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = $rekl['stempel'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 0, 'L', 0);
	
	//praegestempel
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(20, $schulungHeaderHeight, "Prāgestempel: ", '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = $rekl['pragestempel'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, '0', 1, 'L', 0);
	
	$pdf->Ln(5);
	

	//kosten
	$pdf->SetFillColor(255, 255, 230);
	//header
	$tbWidth = 18;
	$fill = 0;
	
	$gew = $apl->getTeilGewicht($rekl['teil']);
	$kurs = $apl->getKurs($rekl['rekl_datum'], 'EUR', 'CZK');

	//header
	$pdf->SetFont("FreeSans", "B", 8);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Interne Bewertung:    ".$rekl['interne_bewertung'], '0', 0, 'L', 0);
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell($tbWidth, $schulungHeaderHeight, "Stk", 'LRBT', 0, 'R', 1);
	$pdf->Cell($tbWidth, $schulungHeaderHeight, "Gewicht", 'LRBT', 0, 'R', 1);
	$pdf->Cell($tbWidth, $schulungHeaderHeight, "Kosten[CZK]", 'LRBT', 1, 'R', 1);

	//zapamatovat x a y pro interne bewertung
	$ibX = $pdf->GetX();
	$ibY = $pdf->GetY();

	//ausschuss
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Ausschuss", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['anerkannt_stk_ausschuss'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['anerkannt_stk_ausschuss']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$obsah = number_format($rekl['anerkannt_ausschuss_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);
	
	//nacharbeit
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Nacharbeit", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['anerkannt_stk_nacharbeit'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['anerkannt_stk_nacharbeit']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$obsah = number_format($rekl['anerkannt_nacharbeit_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);

	//Dif / falsch deklariert
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Dif / falsch deklariert", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['dif_falsch_deklariert_stk'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['dif_falsch_deklariert_stk']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$obsah = number_format($rekl['dif_falsch_deklariert_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);
	
	// Verpackung
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Verpackung", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['verpackung_stk'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['verpackung_stk']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$obsah = number_format($rekl['verpackung_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);

	//  Kreislauf
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Kreislauf", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['kreislauf_stk'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['kreislauf_stk']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$obsah = number_format($rekl['kreislauf_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);

	//  Unklar
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Unklar", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['unklar_stk'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['unklar_stk']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$pdf->SetFillColor(240, 240, 240);
	$obsah = '';//number_format($rekl['kreislauf_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', 1);
	$pdf->SetFillColor(255, 255, 230);
	
	//  nicht anerkannt
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "nicht anerkannt", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$obsah = number_format($rekl['anerkannt_stk_nein'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//gewicht
	$obsah = number_format($rekl['anerkannt_stk_nein']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	//kosten czk
	$pdf->SetFillColor(240, 240, 240);
	$obsah = '';//number_format($rekl['kreislauf_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', 1);
	$pdf->SetFillColor(255, 255, 230);

	//  Pauschale
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->Cell(2*$tbWidth, $schulungHeaderHeight, "Pauschale", 'LRBT', 0, 'L', 1);
	$pdf->SetFont("FreeSans", "", 7);
	//stk
	$pdf->SetFillColor(240, 240, 240);
	$obsah = '';//number_format($rekl['anerkannt_stk_nein'],0,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', 1);
	//gewicht
	$obsah = '';//number_format($rekl['anerkannt_stk_nein']*$gew,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', 1);
	$pdf->SetFillColor(255, 255, 230);
	//kosten czk
	$obsah = number_format($rekl['pauschale_preis_eur']*$kurs,2,',',' ');
	$pdf->Cell($tbWidth, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);

	//interne bewertung
	
	$pdf->SetY($ibY-$schulungHeaderHeight);
	$pdf->SetX(PDF_MARGIN_LEFT+5*$tbWidth+5);
//	
//	$pdf->SetFont("FreeSans", "B", 7);
//	$pdf->Cell($persTabsXOffset-20-(5*$tbWidth-5+PDF_MARGIN_LEFT)+5, $schulungHeaderHeight, "Interne Bewertung", 'LRT', 1, 'C', 1);
//	$pdf->SetX(PDF_MARGIN_LEFT+5*$tbWidth+5);
//	$pdf->SetFont("FreeSans", "B", 15);
//	$pdf->Cell($persTabsXOffset-20-(5*$tbWidth-5+PDF_MARGIN_LEFT)+5, 3*$schulungHeaderHeight, $rekl['interne_bewertung'], 'LRB', 1, 'C', 0);
//	
//	
//	
//	$pdf->Ln(5);
	
	//anlagen
	$anlagen = array();
	$anlagenTypen = $apl->getAnlagenTypen();
	$obsah = "";
	$path=$apl->getGdatPath()."".$apl->getKundeGdatPath($rekl['kunde'])."/200 Teile/".$rekl['teil']."/".AplDB::$DIRS_FOR_TEIL_FINAL['100']."/".$rekl['rekl_nr'];
	
	$files = $apl->getFilesForPath($path);
	$pocetPriloh = count($files);
	
	$pocet = 0;
	foreach ($anlagenTypen as $anlage){
	    //zjistit pocet priloh daneho typu
	    if($files!==NULL){
		foreach ($files as $file){
		//$obsah.=$anlage['beschreibung']." ".strtoupper($anlage['muster'])." ".strtoupper($file['filename']);
		if(strstr(strtoupper($file['filename']), strtoupper($anlage['muster']))!==FALSE){
		    $anlagen[$anlage['beschreibung']]++;
		    $pocet++;
		    //$obsah.=$anlage['beschreibung']." ($pocet) ".$file['filename'];
		}
	    }
	    }
	}
	$sonstPocet = $pocetPriloh - $pocet;
	foreach($anlagen as $beschr=>$poc){
		$obsah.=$beschr." ($poc x)\n";
        }
	if($sonstPocet>0){
	    $obsah.="Sonst ($sonstPocet x)\n";
	}
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$pdf->SetX(PDF_MARGIN_LEFT+5*$tbWidth+5);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20-(5*$tbWidth-5+PDF_MARGIN_LEFT)+5, $schulungHeaderHeight, "Anlagen", '0', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	//$obsah = "";
	$pdf->SetX(PDF_MARGIN_LEFT+5*$tbWidth+5);
	$pdf->MultiCell(
		$persTabsXOffset-20-(5*$tbWidth-5+PDF_MARGIN_LEFT)+5, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'0', 
		'L', 
		$fill, 
		1,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		40,	    // max height, 0 disabled
		'T',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	$pdf->Ln(5);
	
	//problembeschreibung
	
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Problembeschreibung", '0', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['beschr_abweichung']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'0', 
		'L', 
		$fill, 
		1,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		15,	    // max height, 0 disabled
		'T',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	//fehlerursachen
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Fehlerursachen", '0', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['beschr_ursache']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'0', 
		'L', 
		$fill, 
		1,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		15,	    // max height, 0 disabled
		'T',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	//art der beseitigung
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Art der Beseitigung ", '0', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['beschr_beseitigung']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'0', 
		'L', 
		$fill, 
		1,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		15,	    // max height, 0 disabled
		'T',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	//bemerkung
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Bemerkung", '0', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['bemerkung']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'0', 
		'L', 
		$fill, 
		1,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		15,	    // max height, 0 disabled
		'T',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	
	//Mitarbeiterschulung
	$fill = 1;
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$pocetSchulungen = count($schulungen);
	
	$pdf->SetY($yTop);
	$pdf->SetX($persTabsXOffset);
	
	$pdf->Cell(25+35+25+35, $schulungHeaderHeight, "Mitarbeiterschulung: ($pocetSchulungen)", '0', 1, 'L', $fill);
	$pdf->Ln();
	if($schulungen!=NULL){
	    $pdf->SetFont("FreeSans", "B", 7);
	    $pdf->SetX($persTabsXOffset);
	    $pdf->Cell(25, $schulungHeaderHeight, 'PersNr Name', 'LRBT', 0, 'L', $fill);
	    $pdf->Cell(35, $schulungHeaderHeight, 'Unterschrift', 'LRBT', 0, 'L', $fill);
	    $pdf->Cell(25, $schulungHeaderHeight, 'PersNr Name', 'LRBT', 0, 'L', $fill);
	    $pdf->Cell(35, $schulungHeaderHeight, 'Unterschrift', 'LRBT', 1, 'L', $fill);
	    $counter = 0;
	    foreach ($schulungen as $schulung){
		$fill = 0;
		$pdf->SetFont("FreeSans", "", 8);
		$nameA = $apl->getNameVorname($schulung['persnr']);
		$name = $nameA['name'].' '.$nameA['vorname'];
		$obsah = $schulung['persnr']."\n".$name;
		$persInfoA = $apl->getPersInfoArray($schulung['persnr']);
		
		if($persInfoA!==NULL){
		    $persInfo = $persInfoA[0];
		    $hatAustritt = strlen(trim($persInfo['austritt']))>0?TRUE:FALSE;
		}
		
		if($counter%2==0){
		    $pdf->SetX($persTabsXOffset);
		}
		$verursacher = intval($schulung['rekl_verursacher'])!=0?TRUE:FALSE;
		if($hatAustritt){
		    $pdf->SetFillColor(255, 230, 230);    
		}
		else{
		    $pdf->SetFillColor(255, 255, 255);    
		}
		$pdf->MultiCell(25, $schulungRowHeight, $obsah, 'LRBT', 'L', 1, 0, '', '', TRUE, 0, FALSE, TRUE, $schulungRowHeight, 'M', TRUE);
		$obsah = '';
		if($verursacher){
		    //oznacit verursacher
		    $pdf->Image('./mracoun.png', $pdf->GetX()-($schulungRowHeight/2.5)-1,$pdf->GetY()+1,0,$schulungRowHeight/2.5);
		    //$obsah = 'X';
		}
		//$obsah = $schulung['rekl_verursacher'];
		$pdf->Cell(35, $schulungRowHeight, $obsah, 'LRBT', 0, 'L', 0);
		$counter++;
		if($counter%2==0){
		    $pdf->Ln($schulungRowHeight);
		}
		if($counter>25){
		    //zobrazit max. 10radku
		    break;
		}
	    }
	}
	
	
	// podpisy, erstellt am, zu laetzt geaendert, abgeschlossen am
	$pdf->SetFont("FreeSans", "B", 8);
	$pdf->SetY($pdf->getPageHeight()-PDF_MARGIN_BOTTOM);
	$pdf->SetX($persTabsXOffset);
	$obsah = "erstellt: ";
	$userInfo = $apl->getUserInfoArray($rekl['erstellt']);
	$obsah.= $rekl['erstellt'];//$userInfo['realname'];
	$obsah.="\nam: ".date('d.m.Y',  strtotime($rekl['rekl_datum']));
	$pdf->MultiCell(40, 5, $obsah, '0', 'L', 0, 0, '', '', TRUE, 0, FALSE, TRUE, 10, 'T', TRUE);
	$obsah = "zu lātzt geāndert: ";
	$userInfo = $apl->getUserInfoArray($rekl['letzt_geandert']);
	$obsah.= $rekl['letzt_geandert'];//$userInfo['realname'];
	$obsah.="\nam: ".date('d.m.Y',  strtotime($rekl['stamp']));
	$pdf->MultiCell(40, 5, $obsah, '0', 'L', 0, 0, '', '', TRUE, 0, FALSE, TRUE, 10, 'T', TRUE);
	$obsah = "abgeschlossen: ";
	$userInfo = $apl->getUserInfoArray($rekl['abgeschlossen']);
	$obsah.= $rekl['abgeschlossen'];//$userInfo['realname'];
	if(strtotime($rekl['rekl_erledigt_am'])){
	    $obsah.="\nam: ".date('d.m.Y',  strtotime($rekl['rekl_erledigt_am']));
	}
	else{
	    $obsah.="\nam: ";
	}
	
	$pdf->MultiCell(40, 5, $obsah, '0', 'L', 0, 0, '', '', TRUE, 0, FALSE, TRUE, 10, 'T', TRUE);
    }
}

$pdf->Output();


