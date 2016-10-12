<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S240";
$doc_subject = "S240 Report";
$doc_keywords = "S240";

// necham si vygenerovat XML

$parameters=$_GET;

$datum_von=make_DB_datum($_GET['datum_von']);
$datum_bis=make_DB_datum($_GET['datum_bis']);

require_once('S240_xml.php');


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

$cells_kd = 
array(

"auftragsnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'LT',"align"=>"L","radek"=>0,"fill"=>0),

"kd_S0011" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_S0043" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"kd_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_S0062" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

    "kd_S0081" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_S0091" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_X" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_M" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_celkem1" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_celkem2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"kd_summe_lieferung" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"delta_kd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_aby = 
array(

"auftragsnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'L',"align"=>"L","radek"=>0,"fill"=>0),

"aby_S0011" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_S0043" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

    "aby_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_S0062" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

    "aby_S0081" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_S0091" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_X" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_M" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_celkem1" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_celkem2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"aby_summe_lieferung" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"delta_aby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_verb = 
array(

"auftragsnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'LB',"align"=>"L","radek"=>0,"fill"=>0),

"verb_S0011" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_S0043" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

    "verb_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_S0062" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

    "verb_S0081" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_S0091" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_X" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_M" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_celkem1" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_celkem2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"verb_summe_lieferung" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"delta_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_header = 
array(

"auftragsnr" 
=> array ("popis"=>"\nAuftragsnr","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"kd_S0011" 
=> array ("popis"=>"S0011\nTren,Putz","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_S0041" 
=> array ("popis"=>"S0041\nStrahlen","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_S0043" 
=> array ("popis"=>"S0043\nStrahlen","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

    "kd_S0051" 
=> array ("popis"=>"S0051\nEndkontrolle","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_S0061" 
=> array ("popis"=>"S0061\nFarbe","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_S0062" 
=> array ("popis"=>"S0062\nFarbe","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_S0081" 
=> array ("popis"=>"S0081\nSonstige","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_S0091" 
=> array ("popis"=>"S0091\nRE o. VzKd","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_X" 
=> array ("popis"=>"X\nIntern","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_M" 
=> array ("popis"=>"M\n9X Mehrarb","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_celkem1" 
=> array ("popis"=>"\nres1","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_celkem2" 
=> array ("popis"=>"\nres2","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"kd_summe_lieferung" 
=> array ("popis"=>"Summe\nLieferung","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"delta_kd" 
=> array ("popis"=>"nicht in DStat\nenth. VzKd","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1)

);




$sum_zapati_kunde_kd_array = array(	
								"kd_S0011"=>0,
								"kd_S0041"=>0,
    "kd_S0043"=>0,
								"kd_S0051"=>0,
								"kd_S0061"=>0,
    "kd_S0062"=>0,
								"kd_S0081"=>0,
								"kd_S0091"=>0,
								"kd_X"=>0,
								"kd_M"=>0,
								"kd_celkem1"=>0,
								"kd_celkem2"=>0,
								"kd_summe_lieferung"=>0,
								"delta_kd"=>0
								);
global $sum_zapati_kunde_kd_array;

$sum_zapati_kunde_aby_array = array(	
								"aby_S0011"=>0,
								"aby_S0041"=>0,
    "aby_S0043"=>0,
								"aby_S0051"=>0,
								"aby_S0061"=>0,
    "aby_S0062"=>0,
								"aby_S0081"=>0,
								"aby_S0091"=>0,
								"aby_X"=>0,
								"aby_M"=>0,
								"aby_celkem1"=>0,
								"aby_celkem2"=>0,
								"aby_summe_lieferung"=>0,
								"delta_aby"=>0
								);
global $sum_zapati_kunde_aby_array;

$sum_zapati_kunde_verb_array = array(	
								"verb_S0011"=>0,
								"verb_S0041"=>0,
    "verb_S0043"=>0,
								"verb_S0051"=>0,
								"verb_S0061"=>0,
    "verb_S0062"=>0,
								"verb_S0081"=>0,
								"verb_S0091"=>0,
								"verb_X"=>0,
								"verb_M"=>0,
								"verb_celkem1"=>0,
								"verb_celkem2"=>0,
								"verb_summe_lieferung"=>0,
								"delta_verb"=>0
								);
global $sum_zapati_kunde_verb_array;

$sum_zapati_pg_kd_array = array(	
								"kd_S0011"=>0,
								"kd_S0041"=>0,
    "kd_S0043"=>0,
								"kd_S0051"=>0,
								"kd_S0061"=>0,
    "kd_S0062"=>0,
								"kd_S0081"=>0,
								"kd_S0091"=>0,
								"kd_X"=>0,
								"kd_M"=>0,
								"kd_celkem1"=>0,
								"kd_celkem2"=>0,
								"kd_summe_lieferung"=>0,
								"delta_kd"=>0
								);
global $sum_zapati_pg_kd_array;

$sum_zapati_pg_aby_array = array(	
								"aby_S0011"=>0,
								"aby_S0041"=>0,
    "aby_S0043"=>0,
								"aby_S0051"=>0,
								"aby_S0061"=>0,
    "aby_S0062"=>0,
								"aby_S0081"=>0,
								"aby_S0091"=>0,
								"aby_X"=>0,
								"aby_M"=>0,
								"aby_celkem1"=>0,
								"aby_celkem2"=>0,
								"aby_summe_lieferung"=>0,
								"delta_aby"=>0
								);
global $sum_zapati_pg_aby_array;

$sum_zapati_pg_verb_array = array(	
								"verb_S0011"=>0,
								"verb_S0041"=>0,
    "verb_S0043"=>0,
								"verb_S0051"=>0,
								"verb_S0061"=>0,
    "verb_S0062"=>0,
								"verb_S0081"=>0,
								"verb_S0091"=>0,
								"verb_X"=>0,
								"verb_M"=>0,
								"verb_celkem1"=>0,
								"verb_celkem2"=>0,
								"verb_summe_lieferung"=>0,
								"delta_verb"=>0
								);
global $sum_zapati_pg_verb_array;

$sum_zapati_sestava_kd_array = array(	
								"kd_S0011"=>0,
								"kd_S0041"=>0,
    "kd_S0043"=>0,
								"kd_S0051"=>0,
								"kd_S0061"=>0,
    "kd_S0062"=>0,
								"kd_S0081"=>0,
								"kd_S0091"=>0,
								"kd_X"=>0,
								"kd_M"=>0,
								"kd_celkem1"=>0,
								"kd_celkem2"=>0,
								"kd_summe_lieferung"=>0,
								"delta_kd"=>0
								);
global $sum_zapati_sestava_kd_array;

$sum_zapati_sestava_aby_array = array(	
								"aby_S0011"=>0,
								"aby_S0041"=>0,
    "aby_S0043"=>0,
								"aby_S0051"=>0,
								"aby_S0061"=>0,
    "aby_S0062"=>0,
								"aby_S0081"=>0,
								"aby_S0091"=>0,
								"aby_X"=>0,
								"aby_M"=>0,
								"aby_celkem1"=>0,
								"aby_celkem2"=>0,
								"aby_summe_lieferung"=>0,
								"delta_aby"=>0
								);
global $sum_zapati_sestava_aby_array;

$sum_zapati_sestava_verb_array = array(	
								"verb_S0011"=>0,
								"verb_S0041"=>0,
    "verb_S0043"=>0,
								"verb_S0051"=>0,
								"verb_S0061"=>0,
    "verb_S0062"=>0,
								"verb_S0081"=>0,
								"verb_S0091"=>0,
								"verb_X"=>0,
								"verb_M"=>0,
								"verb_celkem1"=>0,
								"verb_celkem2"=>0,
								"verb_summe_lieferung"=>0,
								"delta_verb"=>0
								);
global $sum_zapati_sestava_verb_array;


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
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	//if($pdfobjekt->PageNo()==1)
	//{
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	//}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
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


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_kunde($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$kundenr,$typmin)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	//$typmin="kd";
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	
	$pdfobjekt->Cell(20,$vyskaradku,$popis." ".$kundenr,'1',0,'L',$fill);
	

	$pdfobjekt->SetFont("FreeSans", "", 9);
	$obsah=$pole[$typmin."_S0011"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_S0041"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);
	
	$obsah=$pole[$typmin."_S0043"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);
	
	$obsah=$pole[$typmin."_S0051"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_S0061"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_S0062"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_S0081"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_S0091"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_X"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_M"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_celkem1"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_celkem2"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole[$typmin."_summe_lieferung"];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(17,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$pole["delta_".$typmin];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	//$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S240 VzKd pro Lieferung und Taetigkeitsgruppe", $params);
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

dbConnect();

// a ted pujdu po produkt grupach
$grupy=$domxml->getElementsByTagName("produkt_gruppe");
foreach($grupy as $grupa)
{
	$pgnr=$grupa->getElementsByTagName("pg")->item(0)->nodeValue;
		
	nuluj_sumy_pole($sum_zapati_pg_kd_array);
	nuluj_sumy_pole($sum_zapati_pg_aby_array);
	nuluj_sumy_pole($sum_zapati_pg_verb_array);
		
	//test_pageoverflow($pdf,5,$cells_header,5);
	//zahlavi_termin($pdf,5,array(255,255,100),$cells_header,$terminnr,$ex_datum);
	//nuluj_sumy_pole($sum_zapati_termin_array);
	
	$kunden=$grupa->getElementsByTagName("kunde");
	
	foreach($kunden as $kunde)
	{
		$kundenr=$kunde->getElementsByTagName("kundenr")->item(0)->nodeValue;
				
		//test_pageoverflow($pdf,5,$cells_header,5);
		//zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr);
		//zahlavi_teil($pdf,5,array(235,235,235),$cells_header,$teilnr,$gew,$brgew,$f_muster_platz,$f_muster_vom,get_teil_bemerk($teilnr,85));
		
		nuluj_sumy_pole($sum_zapati_kunde_kd_array);
		nuluj_sumy_pole($sum_zapati_kunde_aby_array);
		nuluj_sumy_pole($sum_zapati_kunde_verb_array);
		
		$auftraege=$kunde->getElementsByTagName("auftrag");
		
		foreach($auftraege as $auftrag)
		{
		
			$auftragsnr=$auftrag->getElementsByTagName("auftragsnr")->item(0)->nodeValue;
			
			//test_pageoverflow($pdf,10,$cells_header,5);
			//zahlavi_teil($pdf,5,array(255,255,255),$cells_header,$teilnr,$gew,$muster_platz,$teillang,$abnr);
			//nuluj_sumy_pole($sum_zapati_teil_array);
			
			$vzkd_minuten=$auftrag->getElementsByTagName("vzkd_min")->item(0);
			$vzkd_min_childs=$vzkd_minuten->childNodes;
			test_pageoverflow($pdf,25,$cells_header,5);
			telo($pdf,$cells_kd,5,array(255,255,255),"",$vzkd_min_childs);
			
			// projedu pole a aktualizuju sumy pro zapati kunde_kd
			foreach($sum_zapati_kunde_kd_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_kunde_kd_array[$key]+=$hodnota;
			}

			
			$vzaby_minuten=$auftrag->getElementsByTagName("vzaby_min")->item(0);
			$vzaby_min_childs=$vzaby_minuten->childNodes;
			//test_pageoverflow($pdf,4,$cells_header,5);
			telo($pdf,$cells_aby,4,array(255,255,255),"",$vzaby_min_childs);
			
			// projedu pole a aktualizuju sumy pro zapati kunde_aby
			foreach($sum_zapati_kunde_aby_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_kunde_aby_array[$key]+=$hodnota;
			}

			$verb_minuten=$auftrag->getElementsByTagName("verb_min")->item(0);
			$verb_min_childs=$verb_minuten->childNodes;
			//test_pageoverflow($pdf,4,$cells_header,5);
			telo($pdf,$cells_verb,4,array(255,255,255),"",$verb_min_childs);
			
			// projedu pole a aktualizuju sumy pro zapati kunde_verb
			foreach($sum_zapati_kunde_verb_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_kunde_verb_array[$key]+=$hodnota;
			}

		}
		
		test_pageoverflow($pdf,15,$cells_header,5);
		zapati_kunde($pdf,$auftrag,5,"SumKdmin",array(255,255,100),$sum_zapati_kunde_kd_array,$kundenr,"kd");
		
		// sumy pro zapati pg
		foreach($sum_zapati_pg_kd_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_kunde_kd_array[$key];
			$sum_zapati_pg_kd_array[$key]+=$hodnota;
		}

		
		//test_pageoverflow($pdf,5,$cells_header,5);
		zapati_kunde($pdf,$auftrag,5,"SumAbymin",array(255,255,100),$sum_zapati_kunde_aby_array,$kundenr,"aby");
		
		// sumy pro zapati pg
		foreach($sum_zapati_pg_aby_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_kunde_aby_array[$key];
			$sum_zapati_pg_aby_array[$key]+=$hodnota;
		}

		
		//test_pageoverflow($pdf,5,$cells_header,5);
		zapati_kunde($pdf,$auftrag,5,"SumVerb",array(255,255,100),$sum_zapati_kunde_verb_array,$kundenr,"verb");
		
		// sumy pro zapati pg
		foreach($sum_zapati_pg_verb_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_kunde_verb_array[$key];
			$sum_zapati_pg_verb_array[$key]+=$hodnota;
		}

		
	}
	
	test_pageoverflow($pdf,15,$cells_header,5);
	zapati_kunde($pdf,$auftrag,5,"VzKd PG",array(230,230,255),$sum_zapati_pg_kd_array,$pgnr,"kd");
	zapati_kunde($pdf,$auftrag,5,"VzAby PG",array(230,230,255),$sum_zapati_pg_aby_array,$pgnr,"aby");
	zapati_kunde($pdf,$auftrag,5,"Verb PG",array(230,230,255),$sum_zapati_pg_verb_array,$pgnr,"verb");
	// sumy pro zapati pg
	foreach($sum_zapati_sestava_kd_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_pg_kd_array[$key];
		$sum_zapati_sestava_kd_array[$key]+=$hodnota;
	}
	// sumy pro zapati pg
	foreach($sum_zapati_sestava_aby_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_pg_aby_array[$key];
		$sum_zapati_sestava_aby_array[$key]+=$hodnota;
	}
	// sumy pro zapati pg
	foreach($sum_zapati_sestava_verb_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_pg_verb_array[$key];
		$sum_zapati_sestava_verb_array[$key]+=$hodnota;
	}
		
}

test_pageoverflow($pdf,20,$cells_header,5);
$pdf->Ln();
zapati_kunde($pdf,$auftrag,5,"VzKd Ges",array(200,200,255),$sum_zapati_sestava_kd_array,"","kd");
zapati_kunde($pdf,$auftrag,5,"VzAby Ges",array(200,200,255),$sum_zapati_sestava_aby_array,"","aby");
zapati_kunde($pdf,$auftrag,5,"Verb Ges",array(200,200,255),$sum_zapati_sestava_verb_array,"","verb");


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
