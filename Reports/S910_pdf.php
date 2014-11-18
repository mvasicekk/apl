<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S910";
$doc_subject = "S910 Report";
$doc_keywords = "S910";

// necham si vygenerovat XML

$parameters=$_GET;

$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];

require_once('S910_xml.php');


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

"kundenr"
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"name1"
=> array ("popis"=>"","sirka"=>50,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"name2"
=> array ("popis"=>"","sirka"=>50,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"plz"
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"ort"
=> array ("popis"=>"","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"strasse"
=> array ("popis"=>"","sirka"=>40,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"tel"
=> array ("popis"=>"","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"fax"
=> array ("popis"=>"","sirka"=>0,"ram"=>'B',"align"=>"L","radek"=>1,"fill"=>0)

);


$cells_header =
array(

"kundenr"
=> array ("popis"=>"Nr.","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"name1"
=> array ("popis"=>"Name1","sirka"=>50,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"name2"
=> array ("popis"=>"Name2","sirka"=>50,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>1),

"plz"
=> array ("popis"=>"Plz","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"ort"
=> array ("popis"=>"Ort","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"strasse"
=> array ("popis"=>"Strasse","sirka"=>40,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>1),

"tel"
=> array ("popis"=>"Tel","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"fax"
=> array ("popis"=>"Fax","sirka"=>0,"ram"=>'B',"align"=>"L","radek"=>1,"fill"=>1)

);

// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
//        echo "nodename:$nodename,cell:$cell<br>";
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
	$pdfobjekt->SetFont("FreeSans", "", 7);
}

// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
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
		pageheader($pdfobjekt,$cellhead,$vysradku);
	}
}
				

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S910 Abydos - Kunden", $params);
//$pdf->SetHeaderData("", 0, "D763 Rechnung", $params);
//$pdf->SetHeaderData("", 0, "", "");
//$pdf->setRechnungFoot(true);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT+5, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER+3);
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
    $kundeChildNodes = $kunde->childNodes;
    test_pageoverflow($pdf, 5, $cells_header);
    detaily($pdf,$cells,5,array(255,255,255),$kundeChildNodes);
//    print_r($kundeChildNodes);
}



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
