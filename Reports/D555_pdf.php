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


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

//$cells = 
//array(
//
//"imanr"
//=> array ("popis"=>"IMANr","sirka"=>30,"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>0),
//
//"emanr" 
//=> array ("popis"=>"EMANr","sirka"=>20,"ram"=>'RBT',"align"=>"L","radek"=>0,"fill"=>0),
//
//"imavon" 
//=> array ("filterF"=>"stripPHP","popis"=>"Von","sirka"=>7,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
//
//"stamp" 
//=> array ("popis"=>"Stamp","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
//    
//"auftragsnrarray" 
//=> array ("filterF"=>"maxCountMore3","popis"=>"Importe","sirka"=>35,"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>0),
//
//"palarray" 
//=> array ("filterF"=>"maxCountMore5","popis"=>"Paletten","sirka"=>40,"ram"=>'BT',"align"=>"L","radek"=>0,"fill"=>0),
//
//"imastk" 
//=> array ("filterF"=>"getIMAStk","popis"=>"Stk","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
//    
//"tatundzeitarray" 
//=> array ("filterF"=>"maxCountMore3","popis"=>"Tat und Zeit","sirka"=>40,"ram"=>'BTR',"align"=>"L","radek"=>0,"fill"=>0),
//    
//"bemerkung" 
//=> array ("popis"=>"Bemerkung","sirka"=>0,"ram"=>'1',"align"=>"L","radek"=>1,"fill"=>0),
//
//
//
//);

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
/////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce k vynulovani pole se sumama
// jako parametr predam asociativni pole
//function nuluj_sumy_pole(&$pole)
//{
//	foreach($pole as $key=>$prvek)
//	{
//		$pole[$key]=0;
//	}
//}
////////////////////////////////////////////////////////////////////////////////////////////////////

 

// funkce pro vykresleni hlavicky na kazde strance
//function pageheader($pdfobjekt,$pole,$headervyskaradku)
//{
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$pdfobjekt->SetFillColor(255,255,200,1);
//	$fill=1;
//	foreach($pole as $cell)
//	{
//		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],'1',$cell["align"],$fill);
//	}
//	$pdfobjekt->Ln();
//	//$pdfobjekt->Ln();
//	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
//	$pdfobjekt->SetFont("FreeSans", "", 6);
//}

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * @param TCPDF $p
 * @param type $childNodes
 * @param type $rgb 
 */
//function zahlavi_kunde($p,$rowHeight,$childNodes,$rgb)
//{
//	$p->SetFont("FreeSans", "", 10);
//	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
//	$fill = 1;
//	$teilnr = getValueForNode($childNodes, 'kundenr');
//	$p->Cell(0,$rowHeight,$teilnr,'1',1,'L',$fill);
//}

/**
 *
 * @param type $p
 * @param type $rowHeight
 * @param type $childNodes
 * @param type $rgb 
 */
//function zahlavi_teil($p,$rowHeight,$childNodes,$rgb)
//{
//	$p->SetFont("FreeSans", "B", 10);
//	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
//	$fill = 1;
//	$teilnr = getValueForNode($childNodes, 'teilnr');
//	$teilbez = getValueForNode($childNodes, 'teilbez');
//	$p->Cell(0,$rowHeight,$teilnr.' '.$teilbez,'1',1,'L',$fill);
//}

/**
 *
 * @param type $s 
 */
//function stripPHP($s){
//  return substr($s, strpos($s, '/')+1);
//}
//
//function maxCountMore5($s,$maxCount=5){
//    return maxCountMore($s,$maxCount);
//}
//function maxCountMore3($s,$maxCount=3){
//    return maxCountMore($s,$maxCount);
//}
/**
 *
 * @param type $s
 * @param type $maxCount 
 */
//function maxCountMore($s,$maxCount=3){
//  $returnValue = $s;
//  $arr = explode(';', $s);
//  if(is_array($arr)){
//      $arrayCount = count($arr);
//      if($arrayCount>$maxCount){
//	  $rest = $arrayCount-$maxCount;
//	  $returnValue = join(';',  array_slice($arr, 0, $maxCount))." (+$rest)";
//      }
//  }
//  return $returnValue;
//}

//function getIMAStk($imanr){
//    global $apl;
//    return $apl->getIMAStkForIMANr($imanr);
//}

////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
//function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
//{
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
//	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
//	foreach($pole as $nodename=>$cell)
//	{
//		if(array_key_exists("nf",$cell))
//		{
//			$cellobsah = 
//			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
//		}
//		else
//		{
//			$cellobsah=getValueForNode($nodelist,$nodename);
//		}
//
//		if(array_key_exists("filterF", $cell)){
//		    $fName = $cell['filterF'];
//		    $cellobsah = call_user_func($fName, $cellobsah);
//		    if($nodename=='imastk'){
//			$cellobsah = call_user_func($fName, getValueForNode($nodelist, 'imanr'));
//		    }
//		}
//		
//		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
//	}
//	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//}


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

/**
 *
 * @param type $pdfobjekt
 * @param type $vysradku
 * @param type $cellhead 
 */
//function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$testVyskaRadku)
//{
//	// pokud bych prelezl s nasledujicim vystupem vysku stranky
//	// tak vytvorim novou stranku i se zahlavim
//	if(($pdfobjekt->GetY()+$testVyskaRadku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
//	{
//		$pdfobjekt->AddPage();
//		pageheader($pdfobjekt,$cellhead,$vysradku);
////		$pdfobjekt->Ln();
////		$pdfobjekt->Ln();
//		return 1;
//	}
//	return 0;
//}
				
/**
 *
 * @param type $pdfobjekt
 * @param type $vysradku 
 */
//function test_pageoverflow_noheader($pdfobjekt,$vysradku)
//{
//	// pokud bych prelezl s nasledujicim vystupem vysku stranky
//	// tak vytvorim novou stranku i se zahlavim
//	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
//	{
//		$pdfobjekt->AddPage();
//	}
//}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

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
$pdf->AliasNbPages();
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

foreach($imas as $ima){
    $imaChilds = $ima->childNodes;
    $emanr = getValueForNode($imaChilds, 'emanr');
    $teilnr = getValueForNode($imaChilds, 'teil');
    $imaid = getValueForNode($imaChilds, 'id');
    $kundenr = $apl->getKundeFromTeil($teilnr);
    $kundeInfoArray = $apl->getKundeInfoArray($kundenr);
    $kundeName = $kundeInfoArray[0]['name1'];
    
    $kundeGdatPath = $apl->getKundeGdatPath($kundenr);
    $anlagenDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teilnr . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
    $anlagenDir.= "/".$imanr;

    $teileInfoArray = $apl->getTeilInfoArray($teilnr);
    $teiloriginal = $teileInfoArray['teillang'];
    $teilbezeichnung = $teileInfoArray['Teilbez'];
    $importe = explode(';', getValueForNode($imaChilds, 'ema_auftragsarray'));
    $emaTatArray = explode(';',  getValueForNode($imaChilds, 'ema_tatundzeitarray'));
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
    
    //zjisteni skutecnych palet = prunik palet zadanych v ema pozadavku a palet v zakazce
    // plus zjisteni poctu kusu
    $menge = 0;
    $palArraySkutecne = array();
    $emaPalArray = explode(';', getValueForNode($imaChilds, 'ema_palarray'));
    if(is_array($emaPalArray)){
	foreach ($emaPalArray as $emaPal){
	    // zkusim najit paletu v importech
	    foreach ($importePalArray as $impal){
		if($emaPal==$impal['pal']){
		    array_push($palArraySkutecne, array('im'=>$impal['im'],'pal'=>$impal['pal'],'stk'=>$impal['stk'],'pos'=>$impal['pos']));
		    $menge+=intval($impal['stk']);
		}
	    }
	}
    }
    
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(0, 10, $emanr, '0', 1, 'L', 0);
    $pdf->Cell(0, 10, $kundenr.' '.$kundeName, 'B', 1, 'L', 0);
    $pdf->SetFont("FreeSans", "B", 12);
    $pdf->Cell(40, 10, 'Teil: '.$teilnr, '0', 0, 'L', 0);
    $pdf->SetFont("FreeSans", "", 12);
    $pdf->Cell(0, 10, '( '.$teiloriginal.' )'.' '.$teilbezeichnung, '0', 1, 'L', 0);
    
    $minpreis = 0;
    //seznam importu (pozic)
    if(is_array($palArraySkutecne)){
	//hlavicka
	$pdf->SetFont("FreeSans", "B", 10);
	$pdf->SetFillColor(255,255,200);
	$pdf->Cell(30, 5, "Best.Nr", 'BT', 0, 'L', 1);
	$pdf->Cell(35, 5, "Import", 'BT', 0, 'L', 1);
	$pdf->Cell(40, 5, "vom", 'BT', 0, 'L', 1);
	$pdf->Cell(0, 5, "Pos.", 'BT', 1, 'L', 1);
	//pozice
	$pdf->SetFont("FreeSans", "", 10);
	$posOld='-*-*-';
	foreach ($palArraySkutecne as $pas){
	    if($posOld==$pas['im'].'-'.$pas['pos']) continue;
	    $imInfoArray = $apl->getAuftragInfoArray($pas['im']);
	    $bestellNr = $imInfoArray[0]['bestellnr'];
	    $aufdatArray = explode('.',$imInfoArray[0]['aufdat']);
	    $minpreis = floatval($imInfoArray[0]['minpreis']);
	    $aufdat = $aufdatArray[2].'-'.$aufdatArray[1].'-'.$aufdatArray[0];
	    $pdf->Cell(30, 5, $bestellNr, '0', 0, 'L', 0);
	    $pdf->Cell(35, 5, $pas['im'], '0', 0, 'L', 0);
	    $pdf->Cell(40, 5, $aufdat, '0', 0, 'L', 0);
	    $pdf->Cell(0, 5, $pas['pos'], '0', 1, 'L', 0);
	    $posOld=$pas['im'].'-'.$pas['pos'];
	}
	//podtrhnout
	$pdf->Cell(0, 2, "", 'T', 1, 'L', 0);
    }
    
    $pdf->Ln();
    //antrag auf Mehrleistung - popis
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(0, 5, "Antrag auf Mehrleistung:", '0', 1, 'L', 0);
    //pro jednotlive radky vykreslim bunky
    $emaAntragText = trim(getValueForNode($imaChilds, 'ema_antrag_text'));
    $emaAntragTextArray = preg_split('/\n|\r\n?/', $emaAntragText);
    if(is_array($emaAntragTextArray)){
	$pdf->SetFont("FreeSans", "", 10);
	foreach ($emaAntragTextArray as $text){
	    $pdf->Cell(0, 5, $text, '0', 1, 'L', 0);
	}
    }
	
    $pdf->Ln();
    
    //seznam operaci, pocet kusu, cena
    if(count($tatArray)>0){
	//hlavicka
	$pdf->SetFont("FreeSans", "B", 10);
	$pdf->SetFillColor(255,255,200);
	$pdf->Cell(55, 5, "Mehrarbeit", 'BT', 0, 'L', 1);
	$pdf->Cell(15, 5, "Menge", 'BT', 0, 'R', 1);
	$pdf->Cell(35, 5, "Vorgabezeit", 'BT', 0, 'R', 1);
	$pdf->Cell(35, 5, "Kosten(Stk)", 'BT', 0, 'R', 1);
	$pdf->Cell(40, 5, "Kosten(SUM)", 'BT', 0, 'R', 1);
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
	    $pdf->Cell(0, 5, "EUR", '0', 1, 'R', 0);
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
	$pdf->Cell(0, 5, "EUR", 'BT', 1, 'R', 1);
    }
    
    //tabulka erstellung/genehmigung
    $pdf->Ln();
    $pdf->SetFont("FreeSans", "B", 10);
    $pdf->Cell(90, 5, "Erstellung", '0', 0, 'L', 0);
    $pdf->Cell(0, 5, "Genehmigung", '0', 1, 'L', 0);
    $pdf->SetFont("FreeSans", "", 10);
    $pdf->Cell(15, 5, "am:", '0', 0, 'L', 0);
    $am = substr(getValueForNode($imaChilds, 'ema_antrag_am'),0,10);
    $pdf->Cell(90-15, 5, $am, '0', 0, 'L', 0);
    $pdf->Cell(15, 5, "am:", '0', 0, 'L', 0);
    $pdf->Cell(0, 5, "", '0', 1, 'L', 0);
    $pdf->Cell(15, 5, "vom:", '0', 0, 'L', 0);
    $vom = getValueForNode($imaChilds, 'ema_antrag_vom');
    $pdf->Cell(90-15, 5, $vom, '0', 0, 'L', 0);
    $pdf->Cell(15, 5, "vom:", '0', 0, 'L', 0);
    $pdf->Cell(0, 5, "", '0', 1, 'L', 0);

    
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
		    $pdf->Text($x, $y + $imgHeight + 3, $anlage);
		    unlink($anlagenDir . '/' . $filenameNew);
		    array_push($anlagenPathArray, $anlagePath);
		    $y+=$imgHeight + $imgMezera + 3;
		}
	    }
	}
    }
}

$stamp = date('YmdHis');
//Close and output PDF document
$filename = 'D555_Mehrarbeitsanmeldung_'.$stamp.'.pdf';
$pdf->Output($anlagenDir.'/'.$filename, 'F');
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
