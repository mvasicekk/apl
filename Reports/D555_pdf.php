<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "D555";
$doc_subject = "D555 Report";
$doc_keywords = "D555";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$parameters=$_GET;
$imanr=$_GET['imanr'];

require_once('D555_xml.php');

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param)
{
	$parametry=$param->childNodes;
	// v ramci parametru si prectu label a hodnotu
	foreach($parametry as $parametr)
	{
		$parametr=$parametr->childNodes;
		foreach($parametr as $par)
		{
			if($par->nodeName=="label")
				$label=$par->nodeValue;
			if($par->nodeName=="value")
				$value=$par->nodeValue;
		}
		$params .= $label.": ".$value."  ";
	}
}



/**
 * 
 * @param type $to
 * @param type $subject
 * @param type $message
 * @param type $from
 * @param type $file
 * @return type
 */
function mail_attachment($to, $subject, $message, $from, $file) {
  // $file should include path and filename
  $filename = basename($file);
  $file_size = filesize($file);
  $content = chunk_split(base64_encode(file_get_contents($file))); 
  $uid = md5(uniqid(time()));
  $from = str_replace(array("\r", "\n"), '', $from); // to prevent email injection
  $header = "From: ".$from."\r\n"
      ."MIME-Version: 1.0\r\n"
      ."Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n"
      ."This is a multi-part message in MIME format.\r\n" 
      ."--".$uid."\r\n"
      ."Content-type:text/plain; charset=iso-8859-1\r\n"
      ."Content-Transfer-Encoding: 7bit\r\n\r\n"
      .$message."\r\n\r\n"
      ."--".$uid."\r\n"
      ."Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"
      ."Content-Transfer-Encoding: base64\r\n"
      ."Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n"
      .$content."\r\n\r\n"
      ."--".$uid."--"; 
  return mail($to, $subject, "", $header);
 }


// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
function getValueForNode($nodelist,$nodename)
{
	$nodevalue="";
	foreach($nodelist as $node)
	{
		if($node->nodeName==$nodename)
		{
			$nodevalue=$node->nodeValue;
			return $nodevalue;
		}
	}
	return $nodevalue;
}


//require_once('../tcpdf/config/lang/eng.php');
//require_once('../tcpdf/tcpdf.php');

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D555 Mehrarbeitsanmeldung", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-5, PDF_MARGIN_RIGHT-5);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 13));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
//$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();
//pageheader($pdf, $cells, 5);
// zacinam po dilech


$imas=$domxml->getElementsByTagName("ima");
$gdatPath = "/mnt/gdat/Dat/";
$att='mehr';
$att2FolderArray = AplDB::$ATT2FOLDERARRAY;
$extensions = 'JPG|jpg';
$filter = "/.*.($extensions)$/";
$kunde = 0;
foreach($imas as $ima){
    $imaChilds = $ima->childNodes;
    $emanr = getValueForNode($imaChilds, 'emanr');
    $teilnr = getValueForNode($imaChilds, 'teil');
    $imaid = getValueForNode($imaChilds, 'id');
    $kundenr = $apl->getKundeFromTeil($teilnr);
    $kunde = $kundenr;
    $kundeInfoArray = $apl->getKundeInfoArray($kundenr);
    $waehrkz = $kundeInfoArray[0]['waehrkz'];
    $kundeName = $kundeInfoArray[0]['name1'];
    
    $kundeGdatPath = $apl->getKundeGdatPath($kundenr);
    $anlagenDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teilnr . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
    $anlagenDir.= "/".$imanr;

    $teileInfoArray = $apl->getTeilInfoArray($teilnr);
    $teiloriginal = $teileInfoArray['teillang'];
    $teilbezeichnung = $teileInfoArray['Teilbez'];
    $importe = explode(';', getValueForNode($imaChilds, 'ema_auftragsarray'));
    $emaTatArray = explode(';',  getValueForNode($imaChilds, 'ema_tatundzeitarray'));
    $dauftrIdArray = explode(';',  getValueForNode($imaChilds, 'ema_dauftrid_array'));
    $tatArray = array();
    //tat:vzaby:vzkd
    if(is_array($emaTatArray)){
	foreach ($emaTatArray as $t){
	    list($tatnr,$vzaby,$vzkd) = explode(':', $t);
	    array_push($tatArray, array('tatnr'=>$tatnr,'vzkd'=>floatval($vzkd)));
	}
    }
    
    $importePalArray = array();
    if(is_array($importe)){
	foreach ($importe as $import){
	    $palArray = $apl->getPaletteMitAuftragTeil('', $import, $teilnr);
	    foreach ($palArray as $palRow){
		array_push($importePalArray, array('im'=>$import,'pal'=>$palRow['pal'],'stk'=>$palRow['stk'],'pos'=>$palRow['fremdpos']));
	    }
	}
    }
    
    
    //zjisteni skutecnych palet
    // plus zjisteni poctu kusu
    $menge = 0;
    $palArraySkutecne = array();
    $seskupenePole = array();
//    $emaPalArray = explode(';', getValueForNode($imaChilds, 'ema_palarray'));
    if(is_array($dauftrIdArray)){
	foreach ($dauftrIdArray as $i){
	    $dauftrRow = $apl->getDauftrRow($i);
	    if($dauftrRow!==NULL){
		array_push($palArraySkutecne, array('im'=>$dauftrRow['auftragsnr'],'pal'=>$dauftrRow['pal'],'stk'=>$dauftrRow['stk'],'pos'=>$dauftrRow['fremdpos'],'gt'=>$dauftrRow['giesstag']));
		$import = $dauftrRow['auftragsnr'];
		$pos = $dauftrRow['fremdpos'];
		$gt = $dauftrRow['giesstag'];
		$stk = intval($dauftrRow['stk']);
		$seskupenePole[$import][$pos][$gt]['stk']+=$stk;
		$imInfoArray = $apl->getAuftragInfoArray($import);
		$bestellNr = $imInfoArray[0]['bestellnr'];
		$aufdatArray = explode('.',$imInfoArray[0]['aufdat']);
		$minpreis = floatval($imInfoArray[0]['minpreis']);
		$aufdat = $aufdatArray[2].'-'.$aufdatArray[1].'-'.$aufdatArray[0];
		$seskupenePole[$import]['auftraginfo']['bestellnr'] = $bestellNr;
		$seskupenePole[$import]['auftraginfo']['aufdat'] = $aufdat;
		$menge+=intval($dauftrRow['stk']);
	    }
	}
    }

    
//    AplDB::varDump($palArraySkutecne);
    
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(0, 10, $emanr, '0', 1, 'L', 0);
    $pdf->Cell(0, 10, $kundenr.' '.$kundeName, 'B', 1, 'L', 0);
    $pdf->SetFont("FreeSans", "B", 12);
    $pdf->Cell(40, 10, 'Teil: '.$teilnr, '0', 0, 'L', 0);
    $pdf->SetFont("FreeSans", "", 12);
    $pdf->Cell(0, 10, '( '.$teiloriginal.' )'.' '.$teilbezeichnung, '0', 1, 'L', 0);
    
    //$minpreis = 0;
    //seznam importu (pozic)
    if(is_array($seskupenePole)){
	//hlavicka
	$pdf->SetFont("FreeSans", "B", 10);
	$pdf->SetFillColor(255,255,200);
	$pdf->Cell(30, 5, "Best.Nr", 'BT', 0, 'L', 1);
	$pdf->Cell(35, 5, "Import", 'BT', 0, 'L', 1);
	$pdf->Cell(30, 5, "vom", 'BT', 0, 'L', 1);
	$pdf->Cell(30, 5, "Pos.", 'BT', 0, 'L', 1);
	$pdf->Cell(30, 5, "GT", 'BT', 0, 'L', 1);
	$pdf->Cell(0, 5, "Menge (Stk)", 'BT', 1, 'R', 1);
	//pozice
	$pdf->SetFont("FreeSans", "", 10);
	foreach ($seskupenePole as $im=>$imInfo){
	    foreach ($imInfo as $pos=>$posInfo){
		if($pos=="auftraginfo"){
		    continue;
		}
		foreach ($posInfo as $gt=>$gtInfo){
		    $pdf->Cell(30, 5, $imInfo['auftraginfo']['bestellnr'], '0', 0, 'L', 0);
		    $pdf->Cell(35, 5, $im, '0', 0, 'L', 0);
		    $pdf->Cell(30, 5, $imInfo['auftraginfo']['aufdat'], '0', 0, 'L', 0);
		    $pdf->Cell(30, 5, $pos, '0', 0, 'L', 0);
		    $pdf->Cell(30, 5, $gt, '0', 0, 'L', 0);
		    $pdf->Cell(0, 5, $gtInfo['stk'], '0', 1, 'R', 0);
		}
	    }
	}
	//podtrhnout
	$pdf->Cell(0, 2, "", 'T', 1, 'L', 0);
    }
    
    
//    if(is_array($palArraySkutecne)){
//	//hlavicka
//	$pdf->SetFont("FreeSans", "B", 10);
//	$pdf->SetFillColor(255,255,200);
//	$pdf->Cell(30, 5, "Best.Nr", 'BT', 0, 'L', 1);
//	$pdf->Cell(35, 5, "Import", 'BT', 0, 'L', 1);
//	$pdf->Cell(40, 5, "vom", 'BT', 0, 'L', 1);
//	$pdf->Cell(0, 5, "Pos.", 'BT', 1, 'L', 1);
//	//pozice
//	$pdf->SetFont("FreeSans", "", 10);
//	$posOld='-*-*-';
//	foreach ($palArraySkutecne as $pas){
//	    if($posOld==$pas['im'].'-'.$pas['pos']) continue;
//	    $imInfoArray = $apl->getAuftragInfoArray($pas['im']);
//	    $bestellNr = $imInfoArray[0]['bestellnr'];
//	    $aufdatArray = explode('.',$imInfoArray[0]['aufdat']);
//	    $minpreis = floatval($imInfoArray[0]['minpreis']);
//	    $aufdat = $aufdatArray[2].'-'.$aufdatArray[1].'-'.$aufdatArray[0];
//	    $pdf->Cell(30, 5, $bestellNr, '0', 0, 'L', 0);
//	    $pdf->Cell(35, 5, $pas['im'], '0', 0, 'L', 0);
//	    $pdf->Cell(40, 5, $aufdat, '0', 0, 'L', 0);
//	    $pdf->Cell(0, 5, $pas['pos'], '0', 1, 'L', 0);
//	    $posOld=$pas['im'].'-'.$pas['pos'];
//	}
//	//podtrhnout
//	$pdf->Cell(0, 2, "", 'T', 1, 'L', 0);
//    }

    /*
float
$w
Width of cells. If 0, they extend up to the right margin of the page.
 
float
$h
Cell minimum height. The cell extends automatically if needed.
 
string
$txt
String to print
 
mixed
$border
Indicates if borders must be drawn around the cell. The value can be a number:
0: no border (default)
1: frame
or a string containing some or all of the following characters (in any order):
L: left
T: top
R: right
B: bottom
or an array of line styles for each border group - for example: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)))
 
string
$align
Allows to center or align the text. Possible values are:
L or empty string: left align
C: center
R: right align
J: justification (default value when $ishtml=false)
 
boolean
$fill
Indicates if the cell background must be painted (true) or transparent (false).
 
int
$ln
Indicates where the current position should go after the call. Possible values are:
0: to the right
1: to the beginning of the next line [DEFAULT]
2: below
 
float
$x
x position in user units
 
float
$y
y position in user units
 
boolean
$reseth
if true reset the last cell height (default true).
 
int
$stretch
font stretch mode:
0 = disabled
1 = horizontal scaling only if text is larger than cell width
2 = forced horizontal scaling to fit cell width
3 = character spacing only if text is larger than cell width
4 = forced character spacing to fit cell width
General font stretching and scaling values will be preserved when possible.
 
boolean
$ishtml
set to true if $txt is HTML content (default = false).
 
boolean
$autopadding
if true, uses internal padding and automatically adjust it to account for line width.
 
float
$maxh
maximum height. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature. This feature works only when $ishtml=false.
 
string
$valign
Vertical alignment of text (requires $maxh = $h > 0). Possible values are:
T: TOP
M: middle
B: bottom
. This feature works only when $ishtml=false.
 
boolean
$fitcell
if true attempt to fit all the text within the cell by reducing the font size.
Returns:
    
endif
     * 
     */
    //$pdf->Ln();
    //antrag auf Mehrleistung - popis
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(0, 5, "Antrag auf Mehrleistung:", '0', 1, 'L', 0);
    //pro jednotlive radky vykreslim bunky
    $emaAntragText = trim(getValueForNode($imaChilds, 'ema_antrag_text'));
    $pdf->SetFont("FreeSans", "", 10);
    $pdf->MultiCell(0, 5, $emaAntragText, '0', 'L', 0, 0, '', '', FALSE, 0, FALSE, FALSE, 0);
    
    /*
    $emaAntragTextArray = preg_split('/\n|\r\n?/', $emaAntragText);
    if(is_array($emaAntragTextArray)){
	$pdf->SetFont("FreeSans", "", 10);
	foreach ($emaAntragTextArray as $text){
	    $pdf->Cell(0, 5, $text, '0', 1, 'L', 0);
	}
    }
     * 
     */
	
    $pdf->Ln();
    
//    AplDB::varDump($tatArray);
//    AplDB::varDump($minpreis);
//    exit();
    //seznam operaci, pocet kusu, cena
    if(count($tatArray)>0){
	//hlavicka
	$pdf->SetFont("FreeSans", "B", 10);
	$pdf->SetFillColor(255,255,200);
	$pdf->Cell(55, 5, "Mehrarbeit", 'BT', 0, 'L', 1);
	$pdf->Cell(15, 5, "Menge (SUM)", 'BT', 0, 'R', 1);
	$pdf->Cell(35, 5, "Vorgabezeit", 'BT', 0, 'R', 1);
	$pdf->Cell(35, 5, "Kosten (Stk)", 'BT', 0, 'R', 1);
	$pdf->Cell(40, 5, "Kosten (SUM)", 'BT', 0, 'R', 1);
	$pdf->Cell(0, 5, "", 'BT', 1, 'L', 1);
	$pdf->SetFont("FreeSans", "", 10);
	$sumVzKd = 0;
	$sumKostenStk = 0;
	$sumKostenSum = 0;
	
	foreach ($tatArray as $t){
	    $tatRechnungBezeichnung = $apl->getTatRechnungBezeichnung($t['tatnr']);
//	    $tatnr = '('.$t['tatnr'].')';
	    $tatnr = '';
	    $pdf->Cell(55, 5, $tatRechnungBezeichnung.$tatnr, '0', 0, 'L', 0);
	    $obsah = number_format($menge, 0, ',', ' ');
	    $pdf->Cell(15, 5, $obsah, '0', 0, 'R', 0);
	    $vzkd = floatval($t['vzkd']);
	    $obsah = number_format($vzkd, 2, ',', ' ');
	    $pdf->Cell(35, 5, $obsah, '0', 0, 'R', 0);
	    $kostenStk = $minpreis*$vzkd;
	    $obsah = number_format($kostenStk, 4, ',', ' ');
	    $pdf->Cell(35, 5, $obsah, '0', 0, 'R', 0);
	    $kostenSum = $menge*$kostenStk;
	    $obsah = number_format($kostenSum, 2, ',', ' ');
	    $pdf->Cell(40, 5, $obsah, '0', 0, 'R', 0);
	    $pdf->Cell(0, 5, "$waehrkz", '0', 1, 'R', 0);
	    $sumVzKd+=$vzkd;
	    $sumKostenStk+=$kostenStk;
	    $sumKostenSum+=$kostenSum;
	}
	
	//sumy
	$pdf->SetFont("FreeSans", "B", 10);
	$pdf->SetFillColor(255,255,255);
	$pdf->Cell(55, 5, "Summe", 'BT', 0, 'L', 1);
	$pdf->Cell(15, 5, "", 'BT', 0, 'R', 1);
	$obsah = number_format($sumVzKd, 2, ',', ' ');
	$pdf->Cell(35, 5, $obsah, 'BT', 0, 'R', 1);
	$obsah = number_format($sumKostenStk, 4, ',', ' ');
	$pdf->Cell(35, 5, $obsah, 'BT', 0, 'R', 1);
	$obsah = number_format($sumKostenSum, 2, ',', ' ');
	$pdf->Cell(40, 5, $obsah, 'BT', 0, 'R', 1);
	$pdf->Cell(0, 5, "$waehrkz", 'BT', 1, 'R', 1);
    }
    
    //tabulka erstellung/genehmigung
    $pdf->Ln();
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(60, 5, "Erstellt", '0', 0, 'L', 0);
    $pdf->Cell(60, 5, "Sachlich geprūft", '0', 0, 'L', 0);
    $pdf->Cell(0, 5, "Genehmigt", '0', 1, 'L', 0);
    
    $pdf->SetFont("FreeSans", "", 10);
    $pdf->Cell(15, 5, "von:", '0', 0, 'L', 0);
    $vom = getValueForNode($imaChilds, 'ema_antrag_vom');
    $pdf->Cell(60-15, 5, $vom, '0', 0, 'L', 0);
    $pdf->Cell(60, 5, "von:", '0', 0, 'L', 0);
    $pdf->Cell(0, 5, "von:", '0', 1, 'L', 0);

    $pdf->Cell(15, 5, "am:", '0', 0, 'L', 0);
    $am = substr(getValueForNode($imaChilds, 'ema_antrag_am'),0,10);
    $pdf->Cell(60-15, 5, $am, '0', 0, 'L', 0);
    $pdf->Cell(60, 5, "am:", '0', 0, 'L', 0);
    $pdf->Cell(0, 5, "am:", '0', 1, 'L', 0);
    
    //anlagen
    $pdf->Ln();
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(0, 5, "Anlagen:", 'B', 1, 'L', 0);
    $pdf->Ln();

    //pripravim si pole s cestama k souborum=priloham
    $anlagenArray = explode(';', getValueForNode($imaChilds, 'ema_anlagen_array'));
    if(is_array($anlagenArray)){
	sort($anlagenArray);
    }
    $anlagenPathArray = array();
    $maxWidth = 180;
    $maxHeight = 120;
    $imgMezera = 5;
    
    if (is_array($anlagenArray)) {
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	foreach ($anlagenArray as $anlage) {
	    if (strlen($anlage) > 0) {
		$anlagePath = $anlagenDir . '/' . $anlage;
		//otestuju jestli je soubor dostupny
		if (file_exists($anlagePath)) {
		    $filenameNew = substr($anlage, 0, strrpos($anlage, '.')) . '_tmp' . substr($anlage, strrpos($anlage, '.'));
		    $img = new Imagick($anlagePath);
		    $heightOriginal = $img->getimageheight();
		    $widthOriginal = $img->getimagewidth();
		    $ratio = $widthOriginal / $heightOriginal;
		    //test, jestli je to obrazek na vysku nebo sirku
		    if ($ratio > 1) {
			// na sirku
			// sirku nastavim tak aby mi vyska neprelezla maxHeight a sirka nebyla vetsi nez maxWidth
			$imgWidth = $maxHeight*$ratio;
			$imgHeight = $maxHeight;
			if($imgWidth>$maxWidth){
			    $imgWidth = $maxWidth;
			    $imgHeight = $imgWidth / $ratio;
			}
		    } else {
			$imgHeight = $maxHeight;
			$imgWidth = $imgHeight * ($ratio);
		    }
		    $img->thumbnailimage(1600, 1600, TRUE);
		    $img->writeimage($anlagenDir . '/' . $filenameNew);
		    //nez vykreslim obrazek, otestuju, zda nepujdu pres konec stranky
		    if (($y + $imgHeight ) > ($pdf->getPageHeight() - $pdf->getBreakMargin())) {
			$pdf->AddPage();
			$y = $pdf->GetY();
		    }
		    $pdf->Image($anlagenDir . '/' . $filenameNew, $x, $y, $imgWidth, $imgHeight);
		    //$pdf->Text($x, $y + $imgHeight + 3, $anlage);
		    $pdf->Text($x, $y + $imgHeight, $anlage);
		    unlink($anlagenDir . '/' . $filenameNew);
		    array_push($anlagenPathArray, $anlagePath);
		    //$y+=$imgHeight + $imgMezera + 3;
		    $y+=$imgHeight + $imgMezera;
		}
	    }
	}
    }
}

// multicell
//$w
//(float) Width of cells. If 0, they extend up to the right margin of the page.
// 
//
//$h
//(float) Cell minimum height. The cell extends automatically if needed.
// 
//
//$txt
//(string) String to print
// 
//
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
// 
//
//$align
//(string) Allows to center or align the text. Possible values are:
//L or empty string: left align
//C: center
//R: right align
//J: justification (default value when $ishtml=false)
// 
//
//$fill
//(boolean) Indicates if the cell background must be painted (true) or transparent (false).
// 
//
//$ln
//(int) Indicates where the current position should go after the call. Possible values are:
//0: to the right
//1: to the beginning of the next line [DEFAULT]
//2: below
// 
//
//$x
//(float) x position in user units
// 
//
//$y
//(float) y position in user units
// 
//
//$reseth
//(boolean) if true reset the last cell height (default true).
// 
//
//$stretch
//(int) font stretch mode:
//0 = disabled
//1 = horizontal scaling only if text is larger than cell width
//2 = forced horizontal scaling to fit cell width
//3 = character spacing only if text is larger than cell width
//4 = forced character spacing to fit cell width
//General font stretching and scaling values will be preserved when possible.
// 
//
//$ishtml
//(boolean) INTERNAL USE ONLY -- set to true if $txt is HTML content (default = false). Never set this parameter to true, use instead writeHTMLCell() or writeHTML() methods.
// 
//
//$autopadding
//(boolean) if true, uses internal padding and automatically adjust it to account for line width.
// 
//
//$maxh
//(float) maximum height. It should be >= $h and less then remaining space to the bottom of the page, or 0 for disable this feature. This feature works only when $ishtml=false.
// 
//
//$valign
//(string) Vertical alignment of text (requires $maxh = $h > 0). Possible values are:
//T: TOP
//M: middle
//B: bottom
//. This feature works only when $ishtml=false and the cell must fit in a single page.
// 
//
//$fitcell
//(boolean) if true attempt to fit all the text within the cell by reducing the font size (do not work in HTML mode). $maxh must be greater than 0 and wqual to $h.
# 20170816 -> veta u kazdeho zakaznika

    $pdf->SetX(0);
    $pdf->SetAutoPageBreak(FALSE);
//$pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y, $reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell)
    $text = "Die Teile entsprechen nicht der PPA und/oder dem Putzmuster. Ein Mehraufwand ist erforderlich.\n";
    $text .= "Um die Liefertermine nicht zu gefährden bitten wir Sie innerhalb von 24 Stunden die Mehrarbeitsanmeldung zu\n";
    $text .= "überprüfen bzw. zu genehmigen. Sonst werden wir diese Mehrarbeitsanmeldung als genehmigt betrachten.";
    $radku = count(split("\n", $text));
    $pdf->SetY($pdf->getPageHeight() - 9 * $radku);
    $pdf->SetFont("FreeSans", "", 10);
    $pdf->MultiCell(0, 5, $text, '0', 'L', 0, 0, '', '', FALSE, 0, FALSE, FALSE, 0, 'B');


//$pdf->Output();
////AplDB::varDump($seskupenePole);
//exit();
    
    
$stamp = date('YmdHis');
//Close and output PDF document
$teilnrNew = strtr($teilnr, '/', '-');
$filename = sprintf("%03d_MA_%s_%s.pdf",$kundenr,$teilnrNew,$stamp);// 'D555_Mehrarbeitsanmeldung_'.$stamp.'.pdf';
//otestovat zda existuje slozka
if(!file_exists($anlagenDir)){
    mkdir($anlagenDir,TRUE);
}
			
$pdf->Output($anlagenDir.'/'.$filename, 'F');
//$pdf->Output();
//vzgenerovat novou tabulku se souborama k IMA
$imaInfoArray = $apl->getIMAInfoArray($imaid);
$emaAnlagenArray = array();
    
if ($imaInfoArray !== NULL) {
    $ir = $imaInfoArray[0];
    $emaAnlagenStr = $ir['ema_anlagen_array'];
    if(strlen($emaAnlagenStr)>0){
        $emaAnlagenArray = explode(';', $emaAnlagenStr);
    }
}

$formDiv='';
$ppaDir= $anlagenDir;
$extensions = 'JPG|jpg|pdf|txt';
$filter = "/.*.($extensions)$/";
$docsArray = $apl->getFilesForPath($ppaDir,$filter);
$formDiv.="<table id='dokutable_edit'>";
$formDiv.="<tr><td style='font-size:x-small;' colspan='5'>";
$formDiv.="<input type='hidden' id='rootPath' value='0' />";
$formDiv.="</td>";
$formDiv.="<td style='text-align:right;font-size:x-small;' >"." (".$extensions.")</td></tr>";
if ($docsArray !== NULL) {
    $formDiv.="<tr>";
    $formDiv.="<td class='filetableheader' style='' colspan='4'>Datei / soubor</td>";
    $formDiv.="<td class='filetableheader' style='width:160px;'>Datum</td>";
    $formDiv.="<td class='filetableheader' style='width:120px;text-align:right;'>Size</td>";
    $formDiv.="<td class='filetableheader' style='width:120px;text-align:center;'>als EMA Anlage</td>";
    $formDiv.="</tr>";
    $i = 0;
    foreach ($docsArray as $doc) {
	if($doc['filename']=='..') continue;
	$trclass = $i++ % 2 == 0 ? 'sudy' : 'lichy';
	if($doc['filename']==$filename)
	    $trclass = 'pridany';
	$typeclass = $doc['type'];
	$filetypeclass = $doc['ext'];
	$checkBoxId = 'anlage_'.$doc['filename'];
	if($typeclass=='file') $target="_blank";
	$formDiv.="<tr class='$trclass'>";
	$fN = $doc['filename'];
	if($filetypeclass=="JPG")
	    $text = $doc['filename'];//"<img src='".$doc['url']."' width='50'>".$doc['filename'];
	else
	    $text = $doc['filename'];
	$formDiv.="<td class='filetableitem' colspan='4'>";
	$formDiv.="<a title='$fN' target='$target' acturl='./getFilesTable.php' class='$typeclass $filetypeclass' href='" . $doc['url'] . "'>";
	$formDiv.= $text;
	$formDiv.= "</a></td>";
	$formDiv.="<td class='filetableitem' >" . date('Y-m-d H:i:s', $doc['mtime']) . "</td>";
	if($doc['type']=='file')
	    $formDiv.="<td class='filetableitem' style='text-align:right;'>" . number_format(floatval($doc['size']), 0, ',', ' ') . "</td>";
	if($doc['type']=='dir')
	    $formDiv.="<td class='filetableitem' style='text-align:right;'>" . "DIR" . "</td>";

	$checked = '';
	if(in_array($doc['filename'], $emaAnlagenArray)) $checked = "checked='checked'";
	if($filetypeclass=='JPG')
	    $formDiv.="<td class='filetableitem' style='text-align:center;'>" . "<input acturl='./updateEmaAnlage.php' id='$checkBoxId' type='checkbox' $checked>" . "</td>";
	else
	    $formDiv.="<td class='filetableitem' style='text-align:center;'></td>";
	$formDiv.="</tr>";
    }
}
$formDiv.="</table>";


//poslat email s prilohou
//1. komu poslat
//prihlasenemu uzivateli ( musi mit emailovou adresu ! )

$user = $apl->get_user_pc();
$username = substr($user, strrpos($user, '/')+1);
$userInfo = $apl->getUserInfoArray($username);
if($userInfo!==NULL){
    $to = $userInfo['email'];
    mail_attachment($to, "EMA Antrag $filename", "EMA Antrag generiert $filename", "apl@abydos.cz", $anlagenDir.'/'.$filename);
}


echo json_encode(array(
                            'filename'=>$filename,
			    'imanr'=>$imanr,
			    'filetable'=>$formDiv,
    ));
//============================================================+
// END OF FILE                                                 
//============================================================+

?>
