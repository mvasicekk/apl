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
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S362 - Reklamationsbericht x Māngelrūge", $reklnr);
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
//	AplDB::varDump($abmahnungen);
	$pdf->AddPage();
	$yTop = $pdf->getY();
	
	//grundinfo
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20+25, $schulungHeaderHeight, "Grundingo", 'LRBT', 1, 'L', $fill);
	//reklnr
	$pdf->Cell(20, $schulungHeaderHeight, "Rekl.Nr.:", 'LRBT', 0, 'L', $fill);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 0;
	$obsah = $rekl['rekl_nr'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'L', $fill);
	
	//kd reklnr
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20, $schulungHeaderHeight, "Kd Rekl.Nr.:", 'LRBT', 0, 'L', $fill);
	$pdf->SetFont("FreeSans", "", 7);
	$fill = 0;
	$obsah = $rekl['kd_rekl_nr'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'L', $fill);
	
	//kdkd reklnr
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20, $schulungHeaderHeight, "KdKd Rekl.Nr.:", 'LRBT', 0, 'L', $fill);
	$pdf->SetFont("FreeSans", "", 7);
	$fill = 0;
	$obsah = $rekl['kd_kd_rekl_nr'];
	$pdf->Cell(25, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'L', $fill);
	
	$pdf->Ln(5);
	
	//Teilinfo - Identifikation
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell(20+25+50, $schulungHeaderHeight, "Teilinfo - Identifikation", 'LRBT', 1, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "Export / Beh", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(25, $schulungHeaderHeight, "Interne Bewertung", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(50, $schulungHeaderHeight, "Datum", 'LRBT', 1, 'L', $fill);
	
	$pdf->SetFont("FreeSans", "", 7);
	$fill = 0;
	
	$obsah = $rekl['export']." / ".$rekl['export_beh'];
	$pdf->MultiCell(
		20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
		'L', 
		$fill, 
		0,				//Indicates where the current position should go after the call
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
	
	$obsah = $rekl['interne_bewertung'];
	$pdf->SetFont("FreeSans", "B", 10);
	$pdf->MultiCell(
		25, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
		'C', 
		$fill, 
		0,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		15,	    // max height, 0 disabled
		'M',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	
	$obsah = "Erhalten am: ".date('d.m.Y',strtotime($rekl['rekl_datum']))."\n\nErledigt am:  ".date('d.m.Y',strtotime($rekl['rekl_erledigt_am']));
	$pdf->SetFont("FreeSans", "", 8);
	$pdf->MultiCell(
		50, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
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
		'M',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	
	$pdf->Ln(5);
	//kosten
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	//header
	$pdf->Cell(20, $schulungHeaderHeight, "", 'LRBT', 0, 'L', 0);
	$pdf->Cell(20, $schulungHeaderHeight, "Ausschuss", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "Nacharbeit", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "Nicht Anerkannt", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "Unklar", 'LRBT', 1, 'L', $fill);
	$fill = 0;
	//stk
	$pdf->Cell(20, $schulungHeaderHeight, "Stūck", 'LRBT', 0, 'L', 1);
	$obsah = number_format($rekl['anerkannt_stk_ausschuss'],0,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	$obsah = number_format($rekl['anerkannt_stk_nacharbeit'],0,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	$obsah = number_format($rekl['anerkannt_stk_nein'],0,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	$obsah = number_format($rekl['unklar_stk'],0,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);

	
	//Gewicht 
	$pdf->Cell(20, $schulungHeaderHeight, "Gewicht", 'LRBT', 0, 'L', 1);
	$gew = $apl->getTeilGewicht($rekl['teil']);
	$obsah = number_format($rekl['anerkannt_stk_ausschuss']*$gew,1,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	$obsah = number_format($rekl['anerkannt_stk_nacharbeit']*$gew,1,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	$obsah = number_format($rekl['anerkannt_stk_nein']*$gew,1,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 0, 'R', $fill);
	$obsah = number_format($rekl['unklar_stk']*$gew,1,',',' ');
	$pdf->Cell(20, $schulungHeaderHeight, $obsah, 'LRBT', 1, 'R', $fill);
	
	//CZK
	$pdf->Cell(20, $schulungHeaderHeight, "CZK", 'LRBT', 0, 'L', 1);
	$pdf->Cell(20, $schulungHeaderHeight, "", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "", 'LRBT', 0, 'L', $fill);
	$pdf->Cell(20, $schulungHeaderHeight, "", 'LRBT', 1, 'L', $fill);
	
	$pdf->Ln(5);
	
	//anlagen
	$anlagen = array();
	$anlagenTypen = $apl->getAnlagenTypen();
	$obsah = "";
	$path=$apl->getGdatPath()."".$apl->getKundeGdatPath($rekl['kunde'])."/200 Teile/".$rekl['teil']."/".AplDB::$DIRS_FOR_TEIL_FINAL['100']."/".$rekl['rekl_nr'];
	
	$files = $apl->getFilesForPath($path);
	
	foreach ($anlagenTypen as $anlage){
	    //zjistit pocet priloh daneho typu
	    $pocet = 0;
	    foreach ($files as $file){
		//$obsah.=$anlage['beschreibung']." ".strtoupper($anlage['muster'])." ".strtoupper($file['filename']);
		if(strstr(strtoupper($file['filename']), strtoupper($anlage['muster']))!==FALSE){
		    $anlagen[$anlage['beschreibung']]++;
		    //$obsah.=$anlage['beschreibung']." ($pocet) ".$file['filename'];
		}
	    }
	}
	foreach($anlagen as $beschr=>$poc){
		$obsah.=$beschr." ($poc) ";
        }
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Anlagen", 'LRBT', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	//$obsah = "";
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
		'L', 
		$fill, 
		1,				//Indicates where the current position should go after the call
		'', 
		'', 
		TRUE,	    //reset last cell height
		0,	    // font stretch mode
		FALSE,	    //is html
		TRUE,	    // uses internal padding
		10,	    // max height, 0 disabled
		'T',	    // valign
		TRUE	    // fit to cell reduces font size
	);
	$pdf->Ln(5);
	
	//problembeschreibung
	
	$pdf->SetFillColor(255, 255, 230);
	$pdf->SetFont("FreeSans", "B", 7);
	$fill = 1;
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Problembeschreibung", 'LRBT', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['beschr_abweichung']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
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
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Fehlerursachen", 'LRBT', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['beschr_ursache']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
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
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Art der Beseitigung ", 'LRBT', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['beschr_beseitigung']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
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
	$pdf->Cell($persTabsXOffset-20, $schulungHeaderHeight, "Bemerkung", 'LRBT', 1, 'L', $fill);
	$fill = 0;
	$pdf->SetFont("FreeSans", "", 7);
	$obsah = trim($rekl['bemerkung']);
	$pdf->MultiCell(
		$persTabsXOffset-20, 
		$problemBeschreibungMinHeight,	// cell minimum height, extends if needed
		$obsah , 
		'LRBT', 
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
	
	$pdf->Cell(25+35+25+35, $schulungHeaderHeight, "Mitarbeiterschulung: ($pocetSchulungen)", 'LRBT', 1, 'L', $fill);
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
		    $pdf->Image('./patmat.jpg', $pdf->GetX(),$pdf->GetY(),0,$schulungRowHeight);
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
	
    }
}


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


	//Abmahnung Vorschlaege
//	$fill = 1;
//	$pdf->SetFillColor(255, 255, 230);
//	$pdf->SetFont("FreeSans", "B", 7);
//	$pocetAbmahnungen = count($abmahnungen);
//	$pdf->SetX($persTabsXOffset);
//	$pdf->Cell(0, $abmahnungHeaderHeight, "Abmahnung Vorschlaege: ($pocetAbmahnungen)", 'LRBT', 1, 'L', $fill);
//	if($abmahnungen!=NULL){
//	    $pdf->SetFont("FreeSans", "B", 7);
//	    $pdf->SetX($persTabsXOffset);
//	    $pdf->Cell(23, $abmahnungRowHeight, 'Datum', 'LRBT', 0, 'L', $fill);
//	    $pdf->Cell(10, $abmahnungRowHeight, 'PersNr', 'LBT', 0, 'R', $fill);
//	    $pdf->Cell(30, $abmahnungRowHeight, 'Name', 'RBT', 0, 'L', $fill);
//	    $pdf->Cell(23, $abmahnungRowHeight, 'Betrag', 'LRBT', 0, 'R', $fill);
//	    $pdf->Cell(11, $abmahnungRowHeight, 'Von', 'LRBT', 0, 'L', $fill);
//	    $pdf->Cell(0, $abmahnungRowHeight, 'Bemerkung', 'LRBT', 1, 'L', $fill);
//	    $counter = 0;
//	    foreach ($abmahnungen as $abmahnung){
//		$pdf->SetX($persTabsXOffset);
//		$fill = 0;
//		$pdf->SetFont("FreeSans", "", 7);
//		$pdf->Cell(23, $abmahnungRowHeight, date('d.m.Y',strtotime($abmahnung['datum'])), 'LRBT', 0, 'L', $fill);
//		$nameA = $apl->getNameVorname($abmahnung['persnr']);
//		$name = $nameA['name'].' '.$nameA['vorname'];
//		$pdf->Cell(10, $abmahnungRowHeight, $abmahnung['persnr'], 'LBT', 0, 'R', $fill);
//		$pdf->Cell(30, $abmahnungRowHeight, $name, 'RBT', 0, 'L', $fill);
//		$pdf->Cell(23, $abmahnungRowHeight, $abmahnung['vorschlag_betrag'], 'LRBT', 0, 'R', $fill);
//		$pdf->Cell(11, $abmahnungRowHeight, $abmahnung['vorschlag_von'], 'LRBT', 0, 'L', $fill);
//		$pdf->Cell(0, $abmahnungRowHeight, $abmahnung['vorschlag_bemerkung'], 'LRBT', 1, 'L', $fill);
//		$counter++;
//		if($counter>9){
//		    //zobrazit max. 10radku
//		    break;
//		}
//	    }
//	}

$pdf->Output();


