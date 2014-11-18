<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S173";
$doc_subject = "S173 Report";
$doc_keywords = "S173";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];
$password = $_GET['password'];

//$fullAccess = testReportPassword("S102",$password,$user,0);

//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}

$apl = AplDB::getInstance();

$schulung = $_GET['schulung'];
$schulungvon = $apl->make_DB_datum($_GET['schulungvon']);
$schulungbis = $apl->make_DB_datum($_GET['schulungbis']);

$persvon = intval($_GET['persvon']);
$persbis = intval($_GET['persbis']);

if($schulung=='*') $schulung='';

require_once('S173_xml.php');

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
'persnr'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'name'=> array ("popis"=>"","sirka"=>50,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'letzte_datum'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'gilt_bis'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'zbyva'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
'persnr'=> array ("popis"=>"\nPersnr","sirka"=>$cells['persnr']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'name'=> array ("popis"=>"\nName","sirka"=>$cells['name']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'letzte_datum'=> array ("popis"=>"letztes\nDatum","sirka"=>$cells['letzte_datum']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'gilt_bis'=> array ("popis"=>"naechste\nam","sirka"=>$cells['gilt_bis']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'zbyva'=> array ("popis"=>"Tagen\nbis naechste","sirka"=>$cells['zbyva']['sirka'],"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),
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
	$pdfobjekt->SetFillColor(255,255,255,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
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
function zahlavi_schulung($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $obsah = getValueForNode($childs, 'beschreibung');
        $pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'LRT',1,'C',$fill);

        $obsah = 'Lektor :  '.getValueForNode($childs, 'lektor').' ';
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(0,$vyskaradku/2,$obsah,'LRB',1,'C',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_schicht($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1,$fac2,$fac3)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(70,$vyskaradku,$popis,'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['anwesenheit'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$fac1;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac2;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac3;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
	
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,3.5);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S173 Schulungen - Datum - Persnr", $params);
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

//pageheader($pdf,$cells_header,5);

$schulungen = $domxml->getElementsByTagName("schulung");
foreach ($schulungen as $schulung) {
    $schulungChildnodes = $schulung->childNodes;
    $pdf->AddPage();
    zahlavi_schulung($pdf, 10, array(255,255,240), $schulungChildnodes);
    pageheader($pdf,$cells_header,5);

    $personen = $schulung->getElementsByTagName("person");
    foreach ($personen as $person) {
        $personChilds = $person->childNodes;
        test_pageoverflow($pdf, 5, $cells_header);
        telo($pdf, $cells, 5, array(255, 255, 255), "", $personChilds);
    }

}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
