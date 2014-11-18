<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S805";
$doc_subject = "S805 Report";
$doc_keywords = "S805";

// necham si vygenerovat XML

$parameters=$_GET;
$kundevon=$_GET['kundevon'];
$kundebis=$_GET['kundebis'];
$jahr = $_GET['jahr'];

require_once('S805_xml.php');

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

$sirka=18;
$cells = 
array(
"statnr" 
=> array ("popis"=>"StatNr / Monat","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
"m_01"
=> array ("nf"=>array(0,',',' '),"popis"=>"1.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_02"
=> array ("nf"=>array(0,',',' '),"popis"=>"2.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_03"
=> array ("nf"=>array(0,',',' '),"popis"=>"3.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_04"
=> array ("nf"=>array(0,',',' '),"popis"=>"4.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_05"
=> array ("nf"=>array(0,',',' '),"popis"=>"5.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_06"
=> array ("nf"=>array(0,',',' '),"popis"=>"6.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_07"
=> array ("nf"=>array(0,',',' '),"popis"=>"7.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_08"
=> array ("nf"=>array(0,',',' '),"popis"=>"8.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_09"
=> array ("nf"=>array(0,',',' '),"popis"=>"9.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_10"
=> array ("nf"=>array(0,',',' '),"popis"=>"10.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_11"
=> array ("nf"=>array(0,',',' '),"popis"=>"11.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"m_12"
=> array ("nf"=>array(0,',',' '),"popis"=>"12.","sirka"=>$sirka,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"betrag"
=> array ("nf"=>array(0,',',' '),"popis"=>"Betrag","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0),

);


$sumZapatiKunde = array();

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
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],0,$cell["align"],1);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist,$kunde)
{
    global $sumZapatiKunde;
    
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
	    $sumZapatiKunde[$kunde][$nodename]+=floatval(getValueForNode($nodelist,$nodename));
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
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

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
//		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S805 - Umsatz je Kunde + StatNr nach Monaten", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);



// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells,5);

$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
	$kundeChildNodes = $kunde->childNodes;
	$kundenr = getValueForNode($kundeChildNodes,'kundenr');
	zahlavi_kunde($pdf,array(230,255,230),$kundeChildNodes,5);
	$stats =$kunde->getElementsByTagName("statnr_row");
	foreach($stats as $stat){
	    $statChilds = $stat->childNodes;
	    test_pageoverflow($pdf,5,$cells);
	    telo($pdf, $cells, 5, array(255,255,255), '', $statChilds,$kundenr);
	}
	test_pageoverflow($pdf,15,$cells);
	zapati_kunde($pdf,array(230,255,230),$kundeChildNodes,5,$sumZapatiKunde);
}

//echo "<pre>";
//var_dump($sumZapatiKunde);
//echo "</pre>";

// a ted pujdu po zakazkach
////Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

/**
 *
 * @param TCPDF $pdf
 * @param type $childnodes
 * @param int $vyskaRadku 
 */
function zahlavi_kunde($pdf,$rgb,$childnodes,$vyskaRadku){
    
    global $cells;
    global $sirka;
    
    	$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdf->SetFont("FreeSans", "B", 8);
	
	//kundenr
	$kundenr = getValueForNode($childnodes, 'kundenr');
	$name = getValueForNode($childnodes, 'name1');
	$obsah = $kundenr.' - '.$name;
	$pdf->Cell(0, $vyskaRadku, $obsah,'1', 0, 'L', $fill);
	
	$pdf->Ln();
}

/**
 *
 * @global array $cells
 * @global int $sirka
 * @param TCPDF $pdf
 * @param type $rgb
 * @param type $childnodes
 * @param type $vyskaRadku 
 */
function zapati_kunde($pdf,$rgb,$childnodes,$vyskaRadku,$sumArray){
    
    global $cells;
    global $sirka;
    
    	$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdf->SetFont("FreeSans", "B", 8);
	
	//kundenr
	$kundenr = getValueForNode($childnodes, 'kundenr');
	$name = getValueForNode($childnodes, 'name1');
	$obsah = $kundenr.' - '.$name;
	$pdf->Cell($cells['statnr']['sirka'], $vyskaRadku, 'Summe ','1', 0, 'L', $fill);
	$sumBetrag = 0;
	for($i=1;$i<13;$i++){
	    $monatIndex = sprintf("m_%02d",$i);
	    $floatValue = floatval($sumArray[$kundenr][$monatIndex]);
	    $sumBetrag+=$floatValue;
	    $obsah = number_format($floatValue,0,',',' ');
	    $pdf->Cell($sirka, $vyskaRadku, $obsah,'1', 0, 'R', $fill);
	}
	$obsah = number_format($sumBetrag,0,',',' ');
	$pdf->Cell(0, $vyskaRadku, $obsah,'1', 0, 'R', $fill);
	$pdf->Ln();
	$pdf->Ln();
}
?>
