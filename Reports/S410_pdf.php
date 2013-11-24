<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S410";
$doc_subject = "S410 Report";
$doc_keywords = "S410";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];

require_once('S410_xml.php');

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
		 if(strtolower($label)!="password")
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
'kundenr2'=> array ("popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'kdname'=> array ("popis"=>"","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'teilnr'=> array ("popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'teilbez'=> array ("popis"=>"","sirka"=>45,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'schwgrad_num'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'anfang'=> array ("popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"C","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
'kundenr2'=> array ("popis"=>"KundeNr","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'kdname'=> array ("popis"=>"Kunde","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'teilnr'=> array ("popis"=>"TeilNr","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'teilbez'=> array ("popis"=>"Teilbezeichnung","sirka"=>45,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'schwgrad_num'=> array ("popis"=>"Schw.Grad","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'anfang'=> array ("popis"=>"Anfaeng. geeignet","sirka"=>0,"ram"=>'1',"align"=>"C","radek"=>1,"fill"=>1),
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
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
//	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(floatval(getValueForNode($nodelist,$nodename)), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}
		
		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
	}
}
				
function zapati_kunde($pdfobjekt,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	// dummy
	$obsah="";
	$pdfobjekt->Cell(0,$vyskaradku,"",'0',0,'L',$fill);
        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S410 Teil - Schwierigkeitsgrad", $params);
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
pageheader($pdf,$cells_header,5);

$kunden = $domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde){
    $kundeChilds = $kunde->childNodes;
    $teile=$kunde->getElementsByTagName("teil");
    foreach($teile as $teil)
    {
        $teilChilds = $teil->childNodes;
        test_pageoverflow($pdf,5,$cells_header);
        telo($pdf,$cells,5,array(255,255,255),"",$teilChilds);
    }

//    pageheader($pdf,$cells_header,5);
    test_pageoverflow($pdf,5,$cells_header);
    zapati_kunde($pdf, 5, array(255,255,255));
}




//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
