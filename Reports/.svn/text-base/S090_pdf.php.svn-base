<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S090";
$doc_subject = "S090 Report";
$doc_keywords = "S090";

// necham si vygenerovat XML

$parameters=$_GET;

$datumvon=make_DB_datum($_GET['datevon']);
$datumbis=make_DB_datum($_GET['datebis']);
$up=$_GET['up'];
$down=$_GET['down'];
$kcplus=$_GET['kcplus'];
$kcminus=$_GET['kcminus'];
$password = $_GET['password'];

require_once('S090_xml.php');


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

/*
$cells = 
array(

"Teil" 
=> array ("popis"=>"","sirka"=>17,"ram"=>'L',"align"=>"L","radek"=>0,"fill"=>0),

"pal" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"Teilbez" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"drueck_schicht" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"TaetNr" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_typ" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"ma" 
=> array ("popis"=>"","sirka"=>5,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"von" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"bis" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'R',"align"=>"R","radek"=>1,"fill"=>0),

);
 */
$cells_header = 
array(

"vypln" 
=> array ("popis"=>"Tag","sirka"=>27,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"1" 
=> array ("popis"=>"1","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"2" 
=> array ("popis"=>"2","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"3" 
=> array ("popis"=>"3","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"4" 
=> array ("popis"=>"4","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"5" 
=> array ("popis"=>"5","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"6" 
=> array ("popis"=>"6","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"7" 
=> array ("popis"=>"7","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"8" 
=> array ("popis"=>"8","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"9" 
=> array ("popis"=>"9","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"10" 
=> array ("popis"=>"10","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"11" 
=> array ("popis"=>"11","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"12" 
=> array ("popis"=>"12","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"13" 
=> array ("popis"=>"13","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"14" 
=> array ("popis"=>"14","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"15" 
=> array ("popis"=>"15","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"16" 
=> array ("popis"=>"16","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"17" 
=> array ("popis"=>"17","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"18" 
=> array ("popis"=>"18","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"19" 
=> array ("popis"=>"19","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"20" 
=> array ("popis"=>"20","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"21" 
=> array ("popis"=>"21","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"22" 
=> array ("popis"=>"22","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"23" 
=> array ("popis"=>"23","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"24" 
=> array ("popis"=>"24","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"25" 
=> array ("popis"=>"25","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"26" 
=> array ("popis"=>"26","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"27" 
=> array ("popis"=>"27","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"28" 
=> array ("popis"=>"28","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"29" 
=> array ("popis"=>"29","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"30" 
=> array ("popis"=>"30","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
"31" 
=> array ("popis"=>"31","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

);


$sum_zapati_teil_array = array(	
								"teil_celkem"=>0,
								);
global $sum_zapati_teil_array;


$sum_zapati_sestava_frezy_array = array(	
								"d1"=>0,
								"d2"=>0,
								"d3"=>0,
								"d4"=>0,
								"d5"=>0,
								"d6"=>0,
								"d7"=>0,
								"d8"=>0,
								"d9"=>0,
								"d10"=>0,
								"d11"=>0,
								"d12"=>0,
								"d13"=>0,
								"d14"=>0,
								"d15"=>0,
								"d16"=>0,
								"d17"=>0,
								"d18"=>0,
								"d19"=>0,
								"d20"=>0,
								"d21"=>0,
								"d22"=>0,
								"d23"=>0,
								"d24"=>0,
								"d25"=>0,
								"d26"=>0,
								"d27"=>0,
								"d28"=>0,
								"d29"=>0,
								"d30"=>0,
								"d31"=>0,
								"fraese_celkem"=>0
								);
global $sum_zapati_sestava_frezy_array;


$sum_zapati_sestava_dily_array = array(
						"d1_teil_stk"=>0,	
						"d2_teil_stk"=>0,	
						"d3_teil_stk"=>0,	
						"d4_teil_stk"=>0,	
						"d5_teil_stk"=>0,	
						"d6_teil_stk"=>0,	
						"d7_teil_stk"=>0,	
						"d8_teil_stk"=>0,	
						"d9_teil_stk"=>0,	
						"d10_teil_stk"=>0,	
						"d11_teil_stk"=>0,	
						"d12_teil_stk"=>0,	
						"d13_teil_stk"=>0,	
						"d14_teil_stk"=>0,	
						"d15_teil_stk"=>0,	
						"d16_teil_stk"=>0,	
						"d17_teil_stk"=>0,	
						"d18_teil_stk"=>0,	
						"d19_teil_stk"=>0,	
						"d20_teil_stk"=>0,	
						"d21_teil_stk"=>0,	
						"d22_teil_stk"=>0,	
						"d23_teil_stk"=>0,	
						"d24_teil_stk"=>0,	
						"d25_teil_stk"=>0,	
						"d26_teil_stk"=>0,	
						"d27_teil_stk"=>0,	
						"d28_teil_stk"=>0,	
						"d29_teil_stk"=>0,	
						"d30_teil_stk"=>0,	
						"d31_teil_stk"=>0,	
						"teil_celkem"=>0	
);
global $sum_zapati_sestava_dily_array;


$sum_zapati_sestava_vzkdwett_array = array(
						"d1_wettkampf_vzkd"=>0,
						"d2_wettkampf_vzkd"=>0,
						"d3_wettkampf_vzkd"=>0,
						"d4_wettkampf_vzkd"=>0,
						"d5_wettkampf_vzkd"=>0,
						"d6_wettkampf_vzkd"=>0,
						"d7_wettkampf_vzkd"=>0,
						"d8_wettkampf_vzkd"=>0,
						"d9_wettkampf_vzkd"=>0,
						"d10_wettkampf_vzkd"=>0,
						"d11_wettkampf_vzkd"=>0,
						"d12_wettkampf_vzkd"=>0,
						"d13_wettkampf_vzkd"=>0,
						"d14_wettkampf_vzkd"=>0,
						"d15_wettkampf_vzkd"=>0,
						"d16_wettkampf_vzkd"=>0,
						"d17_wettkampf_vzkd"=>0,
						"d18_wettkampf_vzkd"=>0,
						"d19_wettkampf_vzkd"=>0,
						"d20_wettkampf_vzkd"=>0,
						"d21_wettkampf_vzkd"=>0,
						"d22_wettkampf_vzkd"=>0,
						"d23_wettkampf_vzkd"=>0,
						"d24_wettkampf_vzkd"=>0,
						"d25_wettkampf_vzkd"=>0,
						"d26_wettkampf_vzkd"=>0,
						"d27_wettkampf_vzkd"=>0,
						"d28_wettkampf_vzkd"=>0,
						"d29_wettkampf_vzkd"=>0,
						"d30_wettkampf_vzkd"=>0,
						"d31_wettkampf_vzkd"=>0,
						"wettkampf_celkem"=>0
					);

global $sum_zapati_sestava_vzkdwett_array;

$sum_zapati_sestava_vzkdnowett_array = array(
						"d1_nowettkampf_vzkd"=>0,
						"d2_nowettkampf_vzkd"=>0,
						"d3_nowettkampf_vzkd"=>0,
						"d4_nowettkampf_vzkd"=>0,
						"d5_nowettkampf_vzkd"=>0,
						"d6_nowettkampf_vzkd"=>0,
						"d7_nowettkampf_vzkd"=>0,
						"d8_nowettkampf_vzkd"=>0,
						"d9_nowettkampf_vzkd"=>0,
						"d10_nowettkampf_vzkd"=>0,
						"d11_nowettkampf_vzkd"=>0,
						"d12_nowettkampf_vzkd"=>0,
						"d13_nowettkampf_vzkd"=>0,
						"d14_nowettkampf_vzkd"=>0,
						"d15_nowettkampf_vzkd"=>0,
						"d16_nowettkampf_vzkd"=>0,
						"d17_nowettkampf_vzkd"=>0,
						"d18_nowettkampf_vzkd"=>0,
						"d19_nowettkampf_vzkd"=>0,
						"d20_nowettkampf_vzkd"=>0,
						"d21_nowettkampf_vzkd"=>0,
						"d22_nowettkampf_vzkd"=>0,
						"d23_nowettkampf_vzkd"=>0,
						"d24_nowettkampf_vzkd"=>0,
						"d25_nowettkampf_vzkd"=>0,
						"d26_nowettkampf_vzkd"=>0,
						"d27_nowettkampf_vzkd"=>0,
						"d28_nowettkampf_vzkd"=>0,
						"d29_nowettkampf_vzkd"=>0,
						"d30_nowettkampf_vzkd"=>0,
						"d31_nowettkampf_vzkd"=>0,
						"nowettkampf_celkem"=>0
					);

global $sum_zapati_sestava_vzkdnowett_array;


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
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 6);
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
function zahlavi_person($pdfobjekt,$vyskaradku,$rgb,$personChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$persnr=getValueForNode($personChildNodes,"persnr");
	$pdfobjekt->Cell(20,$vyskaradku,$persnr,'LT',0,'L',$fill);
	$name=getValueForNode($personChildNodes,"Name");
	$vorname=getValueForNode($personChildNodes,"Vorname");
	$pdfobjekt->Cell(0,$vyskaradku,$name." ".$vorname,'TR',1,'L',$fill);

	// vypisu policka s poctem frez pro kazdy den d1 az d31, ulozeno v $personChildNodes
	// popis
	$fill=1;
	$pdfobjekt->SetFillColor(235,255,235,1);
	$pdfobjekt->Cell(27,$vyskaradku,"Fraese stk",'L',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	// vypsat policka pro kazdy den
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j;
		$hodnotaPolicka=getValueForNode($personChildNodes,$jmenoPolicka);
		$pdfobjekt->Cell(7,$vyskaradku,$hodnotaPolicka,'1',0,'R',$fill);
	}

	// celkova suma a na dalsi radek
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$suma = getValueForNode($personChildNodes,'fraese_celkem');
	$pdfobjekt->Cell(0,$vyskaradku,$suma,'1',1,'R',$fill);
	
	$pdfobjekt->SetFont("FreeSans", "", 8);
	// dummy
	$obsah="";
	$pdfobjekt->Cell(27,$vyskaradku,"Vzkd(P) Wettbewerb",'L',0,'L',0);
	
	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 6.5);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j."_wettkampf_vzkd";
		$hodnotaPolicka=getValueForNode($personChildNodes,$jmenoPolicka);
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}

	// celkova suma a na dalsi radek
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$suma = getValueForNode($personChildNodes,'wettkampf_celkem');
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	$pdfobjekt->Cell(27,$vyskaradku,"Vzkd(P) Gesamt",'L',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 6.5);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j."_nowettkampf_vzkd";
		$hodnotaPolicka=getValueForNode($personChildNodes,$jmenoPolicka);
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}

	// celkova suma a na dalsi radek
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$suma = getValueForNode($personChildNodes,'nowettkampf_celkem');
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);
	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$teilnr,$factor,$teilChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;
	$pdfobjekt->SetFont("FreeSans", "", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(15,$vyskaradku,$teilnr,'L',0,'L',$fill);
	$pdfobjekt->Cell(12,$vyskaradku,"stk*".$factor,'0',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_teil_stk az d31_teil_stk, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j."_teil_stk";
		$hodnotaPolicka=getValueForNode($teilChildNodes,$jmenoPolicka);
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}

	// celkova suma a na dalsi radek
	$suma = getValueForNode($teilChildNodes,'teil_celkem');
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);
	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_vzkd($pdfobjekt,$vyskaradku,$rgb,$teilChildNodes)
{
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_frezy($pdfobjekt,$vyskaradku,$rgb,$sumy)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"Sum Fraeser",'LT',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 6.5);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j;
		$hodnotaPolicka=$sumy[$jmenoPolicka];
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$suma = $sumy["fraese_celkem"];
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	
	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_dily($pdfobjekt,$vyskaradku,$rgb,$sumy)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"Sum ITG Stk",'LB',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 6.5);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j."_teil_stk";
		$hodnotaPolicka=$sumy[$jmenoPolicka];
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$suma = $sumy["teil_celkem"];
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	
	
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_vzkdwett($pdfobjekt,$vyskaradku,$rgb,$sumy)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"Sum Vzkd Wettkampf",'LB',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 6);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j."_wettkampf_vzkd";
		$hodnotaPolicka=$sumy[$jmenoPolicka];
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$suma = $sumy["wettkampf_celkem"];
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	
	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_vzkdnowett($pdfobjekt,$vyskaradku,$rgb,$sumy)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"Sum Vzkd Gesamt",'LB',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 5.5);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka="d".$j."_nowettkampf_vzkd";
		$hodnotaPolicka=$sumy[$jmenoPolicka];
		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$suma = $sumy["nowettkampf_celkem"];
	$obsah=number_format($suma,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_procentwett($pdfobjekt,$vyskaradku,$rgb,$sumwett,$sumgesamt)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"% im Wett.",'LB',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 5.5);
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka_gesamt="d".$j."_nowettkampf_vzkd";
		$jmenoPolicka_wett="d".$j."_wettkampf_vzkd";
		$hodnotaPolicka_gesamt=$sumgesamt[$jmenoPolicka_gesamt];
		$hodnotaPolicka_wett=$sumwett[$jmenoPolicka_wett];

		if($hodnotaPolicka_gesamt!=0)
			$hodnotaPolicka=$hodnotaPolicka_wett/$hodnotaPolicka_gesamt*100;
		else
			$hodnotaPolicka=0;

		$obsah=number_format($hodnotaPolicka,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$suma = $sumy["nowettkampf_celkem"];
	//$obsah=number_format($suma,0,',',' ');
	$obsah="";
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_frezy_wett($pdfobjekt,$vyskaradku,$rgb,$sumfrezy,$sumwett,$sumgesamt)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"Sum Fraeser im Wett.",'LB',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 5.5);
	$sumaFrezyWett=0;
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka_gesamt="d".$j."_nowettkampf_vzkd";
		$jmenoPolicka_frezy="d".$j;
		$jmenoPolicka_wett="d".$j."_wettkampf_vzkd";
		$hodnotaPolicka_gesamt=$sumgesamt[$jmenoPolicka_gesamt];
		$hodnotaPolicka_wett=$sumwett[$jmenoPolicka_wett];
		$hodnotaPolicka_frezy=$sumfrezy[$jmenoPolicka_frezy];

		if($hodnotaPolicka_gesamt!=0)
			$hodnotaPolicka=$hodnotaPolicka_wett/$hodnotaPolicka_gesamt;
		else
			$hodnotaPolicka=0;

		$procent_wett = $hodnotaPolicka;
		$frezy_wett = $hodnotaPolicka*$hodnotaPolicka_frezy;

		$obsah=number_format($frezy_wett,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
		$sumaFrezyWett+=$frezy_wett;
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$suma = $sumy["nowettkampf_celkem"];
	$obsah=number_format($sumaFrezyWett,0,',',' ');
	//$obsah="";
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava_sum_stk_pro_frezu($pdfobjekt,$vyskaradku,$rgb,$sumdily,$sumfrezy,$sumwett,$sumgesamt)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(27,$vyskaradku,"ITGStk pro Fraese",'LB',0,'L',$fill);

	// vypisu policka s poctem kusu dilu pro kazdy den d1_wettkampf_vzkd az d31_wettkampf_vzkd, ulozeno v $teilChildNodes
	// popis
	$fill=0;
	// vypsat policka pro kazdy den
	$pdfobjekt->SetFont("FreeSans", "", 5.5);
	$sumaFrezyWett=0;
	for($j=1;$j<32;$j++)
	{
		$jmenoPolicka_dily="d".$j."_teil_stk";
		$jmenoPolicka_gesamt="d".$j."_nowettkampf_vzkd";
		$jmenoPolicka_frezy="d".$j;
		$jmenoPolicka_wett="d".$j."_wettkampf_vzkd";
		$hodnotaPolicka_gesamt=$sumgesamt[$jmenoPolicka_gesamt];
		$hodnotaPolicka_wett=$sumwett[$jmenoPolicka_wett];
		$hodnotaPolicka_frezy=$sumfrezy[$jmenoPolicka_frezy];
		$hodnotaPolicka_dily=$sumdily[$jmenoPolicka_dily];

		if($hodnotaPolicka_gesamt!=0)
			$hodnotaPolicka=$hodnotaPolicka_wett/$hodnotaPolicka_gesamt;
		else
			$hodnotaPolicka=0;

		$procent_wett = $hodnotaPolicka;
		$frezy_wett = $hodnotaPolicka*$hodnotaPolicka_frezy;
		
		if($frezy_wett!=0)
			$stkProFrezu = $hodnotaPolicka_dily/$frezy_wett;
		else
			$stkProFrezu = 0;

		$obsah=number_format($stkProFrezu,0,',',' ');
		$pdfobjekt->Cell(7,$vyskaradku,$obsah,'1',0,'R',$fill);
		$sumaFrezyWett+=$frezy_wett;
	}
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$suma = $sumy["nowettkampf_celkem"];
	//$obsah=number_format($sumaFrezyWett,0,',',' ');
	$obsah="";
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$sumArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$pdfobjekt->Cell(27,$vyskaradku,"Summe Stk ITG",'L',0,'L',$fill);
	$obsah=$sumArray["teil_celkem"];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);
	

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_person($pdfobjekt,$vyskaradku,$rgb,$procentInWettkampf,$pocetFrezVSoutezi,$ITGProFraese,$premie)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah=$pocetFrezVSoutezi;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(80,$vyskaradku,"Fraeser fuer Wettbewerb: ".$obsah,'1',0,'L',$fill);

	$obsah=$ITGProFraese;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(70,$vyskaradku,"ITG Stk pro Fraeser: ".$obsah,'1',0,'R',$fill);

	$obsah=$premie;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(40,$vyskaradku,"Praemie: ".$obsah,'1',0,'R',$fill);

	$obsah=$procentInWettkampf*100;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"% VzKd inWettbewerb: ".$obsah,'1',1,'R',$fill);
	

	$pdfobjekt->Ln();
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$sumFraeseGesamt,$sumFraeseFuerWettBewerb,$sumITGStk,$sumVzkdWettbewerb,$sumVzKdGesamt,$sumPraemie)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$obsah=$sumFraeseFuerWettBewerb;
	$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(40,$vyskaradku,"Fraeser fuer Wettbewerb: ",'1',0,'L',$fill);
	//$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

	$obsah=$sumPraemie;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(40,$vyskaradku,"Praemie: ",'1',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);


	$pdfobjekt->Ln();
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	}
}

function test_pageoverflow_noheader($pdfobjekt,$vysradku,$cellhead)
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
				
				
function vypoctiPremie($itgNaFrezu,$pocetDilu,$pocetFrez,$spodniMez,$horniMez,$premiePlusKc,$premieMinusKc)
{
	$premie=0;
	if($itgNaFrezu>$horniMez)
	{
		$premie = (round($pocetDilu / $horniMez)-$pocetFrez)*$premiePlusKc;
	}
	if($itgNaFrezu<$spodniMez)
	{
		$premie = (round($pocetDilu / $spodniMez)-$pocetFrez)*$premieMinusKc;
	}

	if($itgNaFrezu==0)
	{
		$premie=0;
	}

	return $premie;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S090 Fraeserwettbewerb - Auswertung", $params);
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


// a ted pujdu po lidech
$sumFraeseFuerWettBewerb = 0;
$sumFraeseGesamt = 0;
$sumPraemie = 0;
$sumITGStk = 0;
$sumVzkdWettbewerb = 0;
$sumVzKdGesamt = 0;

$personen=$domxml->getElementsByTagName("personen");
foreach($personen as $person)
{
	$personChildNodes = $person->childNodes;
	
	if(test_pageoverflow_noheader($pdf,4*5,$cells_header))
		pageheader($pdf,$cells_header,5);
		
	zahlavi_person($pdf,5,array(255,255,200),$personChildNodes);

	// zkusim jestli mam nejake dily v soutezi
	nuluj_sumy_pole($sum_zapati_teil_array);
	$teile = $person->getElementsByTagName("teil");

	foreach($teile as $teil)
	{
		$teilChildNodes = $teil->childNodes;
		$teilnr = getValueForNode($teilChildNodes,"teilnr");
		$factor = getValueForNode($teilChildNodes,"fraese_wettkampf_factor");

		if(test_pageoverflow_noheader($pdf,5,$cells_header))
			pageheader($pdf,$cells_header,5);
		
		zahlavi_teil($pdf,5,array(255,255,200),$teilnr,$factor,$teilChildNodes);
		foreach($sum_zapati_teil_array as $key=>$prvek)
		{
			$hodnota=$teil->getElementsByTagName($key)->item(0)->nodeValue;
			$sum_zapati_teil_array[$key]+=$hodnota;
		}

	}

	if(test_pageoverflow_noheader($pdf,5,$cells_header))
		pageheader($pdf,$cells_header,5);

	zapati_teil($pdf,5,array(255,255,200),$sum_zapati_teil_array);

	// spocitat pomer casu v soutezi k celkovemu casu
	$vzkdWettbewerb = getValueForNode($personChildNodes,"wettkampf_celkem");
	$vzkdGesamt = getValueForNode($personChildNodes,"nowettkampf_celkem");
	if($vzkdGesamt!=0)
		$procentInWettkampf = $vzkdWettbewerb / $vzkdGesamt;
	else
		$procentInWettkampf = 0;
	
	$pocetFrezVSoutezi = round(getValueForNode($personChildNodes,"fraese_celkem")*$procentInWettkampf);
	if($pocetFrezVSoutezi!=0)
	{
		$ITGProFraese = round($sum_zapati_teil_array["teil_celkem"] / $pocetFrezVSoutezi);
	}
	else
	{
		$ITGProFraese = 0;
	}
	
	// zobrazim souhrnny radek pro cloveka
	if(test_pageoverflow_noheader($pdf,5,$cells_header))
		pageheader($pdf,$cells_header,5);

	$premie=vypoctiPremie($ITGProFraese,$sum_zapati_teil_array["teil_celkem"],$pocetFrezVSoutezi,$down,$up,$kcplus,$kcminus);
	zapati_person($pdf,5,array(255,255,200),$procentInWettkampf,$pocetFrezVSoutezi,$ITGProFraese,$premie);
	$sumFraeseGesamt+=getValueForNode($personChildNodes,"fraese_celkem");
	$sumFraeseFuerWettBewerb+=$pocetFrezVSoutezi;
	$sumITGStk+=$sum_zapati_teil_array["teil_celkem"];
	$sumVzkdWettbewerb+=$vzkdWettbewerb;
	$sumVzKdGesamt+=$vzkdGesamt;
	$sumPraemie+=$premie;
	
	// soucty pro frezy
	foreach($sum_zapati_sestava_frezy_array as $key=>$prvek)
	{
		$hodnota=$person->getElementsByTagName($key)->item(0)->nodeValue;
		$sum_zapati_sestava_frezy_array[$key]+=$hodnota;
	}

	// soucty pro vzkdwett
	foreach($sum_zapati_sestava_vzkdwett_array as $key=>$prvek)
	{
		$hodnota=$person->getElementsByTagName($key)->item(0)->nodeValue;
		$sum_zapati_sestava_vzkdwett_array[$key]+=$hodnota;
	}
	// soucty pro novzkdwett
	foreach($sum_zapati_sestava_vzkdnowett_array as $key=>$prvek)
	{
		$hodnota=$person->getElementsByTagName($key)->item(0)->nodeValue;
		$sum_zapati_sestava_vzkdnowett_array[$key]+=$hodnota;
	}
	// soucty pro dily
	// potvrzeni zapati pro soucty dilu
	
	foreach($sum_zapati_sestava_dily_array as $key=>$prvek)
	{
		$kusyArray = $person->getElementsByTagName($key);
		foreach($kusyArray as $element)
		{
			$hodnota=$element->nodeValue;
			$sum_zapati_sestava_dily_array[$key]+=$hodnota;
		}
	}

}

if(test_pageoverflow_noheader($pdf,8*5,$cells_header))
	pageheader($pdf,$cells_header,5);

zapati_sestava_sum_frezy($pdf,5,array(255,255,200),$sum_zapati_sestava_frezy_array);
zapati_sestava_sum_frezy_wett($pdf,5,array(255,255,200),$sum_zapati_sestava_frezy_array,$sum_zapati_sestava_vzkdwett_array,$sum_zapati_sestava_vzkdnowett_array);
zapati_sestava_sum_dily($pdf,5,array(255,255,200),$sum_zapati_sestava_dily_array);
zapati_sestava_sum_stk_pro_frezu($pdf,5,array(255,255,200),$sum_zapati_sestava_dily_array,$sum_zapati_sestava_frezy_array,$sum_zapati_sestava_vzkdwett_array,$sum_zapati_sestava_vzkdnowett_array);
zapati_sestava_sum_vzkdwett($pdf,5,array(255,255,200),$sum_zapati_sestava_vzkdwett_array);
zapati_sestava_sum_vzkdnowett($pdf,5,array(255,255,200),$sum_zapati_sestava_vzkdnowett_array);
zapati_sestava_sum_procentwett($pdf,5,array(255,255,200),$sum_zapati_sestava_vzkdwett_array,$sum_zapati_sestava_vzkdnowett_array);

zapati_sestava($pdf,5,array(255,255,200),$sumFraeseGesamt,$sumFraeseFuerWettBewerb,$sumITGStk,$sumVzkdWettbewerb,$sumVzKdGesamt,$sumPraemie);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
