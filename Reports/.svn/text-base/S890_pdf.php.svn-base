<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S890";
$doc_subject = "S890 Report";
$doc_keywords = "S890";

// necham si vygenerovat XML

$parameters=$_GET;
$aufdatvon=make_DB_datum($_GET['aufdatvon']);
$aufdatbis=make_DB_datum($_GET['aufdatbis']);
$kundevon=$_GET['kundevon'];
$kundebis=$_GET['kundebis'];


require_once('S890_xml.php');


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
"dummy"
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"auftragsnr"
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"aufdat"
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"stk"
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vaha"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)
);


$cells_header = 
array(

"dummy"
=> array ("popis"=>"","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"auftragsnr"
=> array ("popis"=>"IMPORT","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"aufdat"
=> array ("popis"=>"datum IM","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"stk"
=> array ("popis"=>"kusy","sirka"=>30,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vaha"
=> array ("nf"=>array(2,',',' '),"popis"=>"vaha [kg]","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1)
);


$sum_zapati_kunde_array = array(
								"stk"=>0,
                                "vaha"=>0
								);
global $sum_zapati_kunde_array;

$sum_zapati_sestava_array = array(
								"stk"=>0,
                                "vaha"=>0
								);
global $sum_zapati_sestava_array;

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
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_kunde($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
    $obsah = "Zakaznik : ".getValueForNode($childs,"kunde");
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	/*
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$vyskaradku,$cell["popis"],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	*/
}

/**
 * zobrazi zapati pro zakaznika
 * 
 * @param <type> $pdfobjekt
 * @param <type> $childs
 * @param <type> $popisek
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $summeArray
 */
function zapati_kunde($pdfobjekt,$childs,$vyskaradku,$popisek,$rgb,$summeArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
    $kndnr = getValueForNode($childs,"kunde");
    $obsah = "Suma zakaznik $kndnr: ";
    $pdfobjekt->Cell(30+30+30,$vyskaradku,$obsah,'B',0,'L',$fill);
    $obsah = number_format($summeArray['stk'],0,',',' ');
	$pdfobjekt->Cell(30,$vyskaradku,$obsah,'BT',0,'R',$fill);

    $obsah = number_format($summeArray['vaha'],2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BT',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	/*
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$vyskaradku,$cell["popis"],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	*/
}

function zapati_sestava($pdfobjekt,$childs,$vyskaradku,$popisek,$rgb,$summeArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
    $kndnr = getValueForNode($childs,"kunde");
    $obsah = "Suma celkem ";
    $pdfobjekt->Cell(30+30+30,$vyskaradku,$obsah,'B',0,'L',$fill);
    $obsah = number_format($summeArray['stk'],0,',',' ');
	$pdfobjekt->Cell(30,$vyskaradku,$obsah,'BT',0,'R',$fill);

    $obsah = number_format($summeArray['vaha'],2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BT',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	/*
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$vyskaradku,$cell["popis"],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	*/
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,3.5);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	}
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S890 přehled zakázek importovaných pro Intrastat", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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
//$pdf->Ln();
//$pdf->Ln();


// a ted pujdu po zakaznicich
$kunden=$domxml->getElementsByTagName("knd");
foreach($kunden as $kunde)
{
    $kndChilds = $kunde->childNodes;
	
	test_pageoverflow($pdf,5,$cells_header);
	zahlavi_kunde($pdf,5,array(200,255,200),$kndChilds);
	
	nuluj_sumy_pole($sum_zapati_kunde_array);
	
	$auftraege=$kunde->getElementsByTagName("auftrag");
	foreach($auftraege as $auftrag)
	{
        $auftragChilds = $auftrag->childNodes;
		test_pageoverflow($pdf,3.5,$cells_header);
		telo($pdf,$cells,3.5,array(255,255,255),"",$auftragChilds);
		
		// projedu pole a aktualizuju sumy pro zapati
		foreach($sum_zapati_kunde_array as $key=>$prvek)
		{
			$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
			$sum_zapati_kunde_array[$key]+=$hodnota;
		}
	}

    test_pageoverflow($pdf,5,$cells_header);
	zapati_kunde($pdf,$kndChilds,5,"",array(200,255,200),$sum_zapati_kunde_array);
	// projedu pole a aktualizuju sumy pro zapati sestava
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_kunde_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
}

zapati_sestava($pdf,$kndChilds,5,"",array(200,200,255),$sum_zapati_sestava_array);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
