<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "D550";
$doc_subject = "D550 Report";
$doc_keywords = "D550";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$parameters=$_GET;

$kdvon=$_GET['kundevon'];
$kdbis=$_GET['kundebis'];
$teil=  strtr($_GET['teil'], '*', '%');
$von = $apl->make_DB_datum($_GET['von']);
$bis = $apl->make_DB_datum($_GET['bis']);
$maxbild = intval($_GET['maxbild']);

require_once('D550_xml.php');

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

$cells = 
array(

"imanr"
=> array ("popis"=>"IMANr","sirka"=>30,"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>0),

"emanr" 
=> array ("popis"=>"EMANr","sirka"=>20,"ram"=>'RBT',"align"=>"L","radek"=>0,"fill"=>0),

"imavon" 
=> array ("filterF"=>"stripPHP","popis"=>"Von","sirka"=>7,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"stamp" 
=> array ("popis"=>"Stamp","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"auftragsnrarray" 
=> array ("filterF"=>"maxCountMore3","popis"=>"Importe","sirka"=>35,"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>0),

"palarray" 
=> array ("filterF"=>"maxCountMore5","popis"=>"Paletten","sirka"=>40,"ram"=>'BT',"align"=>"L","radek"=>0,"fill"=>0),

"imastk" 
=> array ("filterF"=>"getIMAStk","popis"=>"Stk","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"tatundzeitarray" 
=> array ("filterF"=>"maxCountMore3","popis"=>"Tat und Zeit","sirka"=>40,"ram"=>'BTR',"align"=>"L","radek"=>0,"fill"=>0),
    
"bemerkung" 
=> array ("popis"=>"Bemerkung","sirka"=>0,"ram"=>'1',"align"=>"L","radek"=>1,"fill"=>0),



);

/////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce k vynulovani pole se sumama
// jako parametr predam asociativni pole
function nuluj_sumy_pole(&$pole)
{
	foreach($pole as $key=>$prvek)
	{
		$pole[$key]=0;
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////

 

// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=1;
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],'1',$cell["align"],$fill);
	}
	$pdfobjekt->Ln();
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * @param TCPDF $p
 * @param type $childNodes
 * @param type $rgb 
 */
function zahlavi_kunde($p,$rowHeight,$childNodes,$rgb)
{
	$p->SetFont("FreeSans", "", 10);
	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill = 1;
	$teilnr = getValueForNode($childNodes, 'kundenr');
	$p->Cell(0,$rowHeight,$teilnr,'1',1,'L',$fill);
}

/**
 *
 * @param type $p
 * @param type $rowHeight
 * @param type $childNodes
 * @param type $rgb 
 */
function zahlavi_teil($p,$rowHeight,$childNodes,$rgb)
{
	$p->SetFont("FreeSans", "B", 10);
	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill = 1;
	$teilnr = getValueForNode($childNodes, 'teilnr');
	$teilbez = getValueForNode($childNodes, 'teilbez');
	$p->Cell(0,$rowHeight,$teilnr.' '.$teilbez,'1',1,'L',$fill);
}

/**
 *
 * @param type $s 
 */
function stripPHP($s){
  return substr($s, strpos($s, '/')+1);
}

function maxCountMore5($s,$maxCount=5){
    return maxCountMore($s,$maxCount);
}
function maxCountMore3($s,$maxCount=3){
    return maxCountMore($s,$maxCount);
}
/**
 *
 * @param type $s
 * @param type $maxCount 
 */
function maxCountMore($s,$maxCount=3){
  $returnValue = $s;
  $arr = explode(';', $s);
  if(is_array($arr)){
      $arrayCount = count($arr);
      if($arrayCount>$maxCount){
	  $rest = $arrayCount-$maxCount;
	  $returnValue = join(';',  array_slice($arr, 0, $maxCount))." (+$rest)";
      }
  }
  return $returnValue;
}

function getIMAStk($imanr){
    global $apl;
    return $apl->getIMAStkForIMANr($imanr);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}

		if(array_key_exists("filterF", $cell)){
		    $fName = $cell['filterF'];
		    $cellobsah = call_user_func($fName, $cellobsah);
		    if($nodename=='imastk'){
			$cellobsah = call_user_func($fName, getValueForNode($nodelist, 'imanr'));
		    }
		}
		
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 7);
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

/**
 *
 * @param type $pdfobjekt
 * @param type $vysradku
 * @param type $cellhead 
 */
function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$testVyskaRadku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testVyskaRadku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
		return 1;
	}
	return 0;
}
				
/**
 *
 * @param type $pdfobjekt
 * @param type $vysradku 
 */
function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
	}
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D550 IMA Liste", $params);
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
pageheader($pdf, $cells, 5);
// zacinam po dilech


$kunden=$domxml->getElementsByTagName("kunde");
$gdatPath = "/mnt/gdat/Dat/";
$att='mehr';
$att2FolderArray = AplDB::$ATT2FOLDERARRAY;
$extensions = 'JPG|jpg';
$filter = "/.*.($extensions)$/";

foreach($kunden as $kunde){
	$kundeChilds = $kunde->childNodes;
	$kundenr = getValueForNode($kundeChilds, 'kundenr');
	$kundeGdatPath = $apl->getKundeGdatPath($kundenr);
	test_pageoverflow($pdf,5,$cells,5);
	zahlavi_kunde($pdf,5,$kundeChilds,array(230,250,230));
	$teile = $kunde->getElementsByTagName("teil");
	foreach($teile as $teil){
	    $teilChilds = $teil->childNodes;
	    $teilnr = getValueForNode($teilChilds,'teilnr');
	    if(test_pageoverflow($pdf,5,$cells,5)>0){
		zahlavi_kunde($pdf,5,$kundeChilds,array(230,250,230));
	    }
	    zahlavi_teil($pdf,5,$teilChilds,array(230,230,250));
	    $imas = $teil->getElementsByTagName("ima");
	    foreach($imas as $ima){
		$imaChilds = $ima->childNodes;
		$imanr = getValueForNode($imaChilds,'imanr');
		if(test_pageoverflow($pdf,5,$cells,5)>0){
		    zahlavi_kunde($pdf,5,$kundeChilds,array(230,250,230));
		    zahlavi_teil($pdf,5,$teilChilds,array(230,230,250));
		}
		detaily($pdf, $cells, 5, array(255,255,255), $imaChilds);
		//get files
		$imgDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teilnr . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
		$imgDir.= "/".$imanr;
		$docsArray = $apl->getFilesForPath($imgDir,$filter);
		$docsCount = $docsArray===NULL?0:count($docsArray);
		$showCount = $docsCount>$maxbild?$maxbild:$docsCount;
		//$showCount = $docsCount;
		$showImages = $showCount>0?1:0;
		$imgMaxColumns = 5;
		$imgWidth = 54.55;
		$mezera = 1;
		$imgHeight = $imgWidth*(3/4);
		if(test_pageoverflow($pdf,5,$cells,$showImages*($imgHeight+$mezera))>0){
		    zahlavi_kunde($pdf,5,$kundeChilds,array(230,250,230));
		    zahlavi_teil($pdf,5,$teilChilds,array(230,230,250));
		}
		$x=$pdf->GetX();
		$y=$pdf->GetY();
		$imgColumn = 0;
		$imgRow = 0;
		for($i=0;$i<$showCount;$i++){
		    $fo = $docsArray[$i]['filename'];
		    $filenameNew = substr($fo, 0,  strrpos($fo, '.')).'_tmp'.substr($fo, strrpos($fo, '.'));
		    $draw = new ImagickDraw();
		    /* Black text */
		    $draw->setFillColor('red');
		    /* Font properties */
		    //$draw->setFont('Arial');
		    $draw->setFontSize( 30 );
		    /* Create text */
		    $img = new Imagick($imgDir.'/'.$docsArray[$i]['filename']);
		    $img->thumbnailimage(640, 640, TRUE);
		    $img->annotateImage($draw, 5, 30, 0, $teilnr.' '.$docsArray[$i]['filename']);
		    $img->writeimage($imgDir.'/'.$filenameNew);
		    $pdf->Image($imgDir.'/'.$filenameNew, $x, $y, $imgWidth,$imgHeight);
		    unlink($imgDir.'/'.$filenameNew);
		    $x+=($imgWidth+$mezera);
		    $imgColumn++;
		    if(($imgColumn==$imgMaxColumns)){
			$imgColumn=0;
			//zvysim o jednicku, pokud jeste budou nasledovat nejake
			if(($i+1)<$showCount) $imgRow++;
			//test jestli se nepresunout na dalsi stranku
			if(($y+2*$imgHeight)>($pdf->getPageHeight()-$pdf->getBreakMargin())){
			    $pdf->AddPage();
			    pageheader($pdf, $cells, 5);
			    //zahlavi_kunde($pdf,5,$kundeChilds,array(230,250,230));
			    //zahlavi_teil($pdf,5,$teilChilds,array(230,230,250));
			    $x=$pdf->GetX();
			    $y=$pdf->GetY();
			    $imgRow=0;
			}
			else{
			    $x=(PDF_MARGIN_LEFT-5);
			    $y+=($imgHeight+$mezera);
			}
		    }
		}
		if($showCount>0) $pdf->Ln(($imgRow+1)*$imgHeight+$mezera);
		
	    }
	    $pdf->Ln(2);
	}
	$pdf->Ln();
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
