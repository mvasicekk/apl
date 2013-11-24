<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S311";
$doc_subject = "S311 Report";
$doc_keywords = "S311";

// necham si vygenerovat XML

$parameters=$_GET;

$export=$_GET['export'];
$teil=$_GET['teil'];
$tat=$_GET['tat'];
$persnr=$_GET['persnr'];


require_once('S311_xml.php');


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

/*
"teil" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),
*/
"tat" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"datum" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"pal" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"oe"
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"persnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"name" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"stk" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"aussstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_typ" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),


);

$cells_header = 
array(

/*
"teil" 
=> array ("popis"=>"\n","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
*/

"tat" 
=> array ("popis"=>"\nTaet.","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"datum" 
=> array ("popis"=>"\nDatum","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"pal" 
=> array ("popis"=>"\nPal","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"oe"
=> array ("popis"=>"\nOE","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"persnr" 
=> array ("popis"=>"\nPers","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"name" 
=> array ("popis"=>"\nName","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"stk" 
=> array ("popis"=>"\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"auss_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"auss_typ" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss\nTyp","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"VzKd\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nVzKd","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"VzAby\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nVzAby","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("popis"=>"\nVerb","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_taetigkeit_array = array(	
								"stk"=>0,
								"aussstk"=>0,
								"sumvzkd"=>0,
								"sumvzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_taetigkeit_array;

$sum_zapati_teil_array = array(	
								"sumvzkd"=>0,
								"sumvzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_teil_array;

$sum_zapati_import_array = array(	
								"sumvzkd"=>0,
								"sumvzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_import_array;


$sum_zapati_sestava_array = array(	
								"sumvzkd"=>0,
								"sumvzaby"=>0,
								"verb"=>0,
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
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	
	$teilnr = getValueForNode($childNodes,"teilnr");
	$gew = number_format(getValueForNode($childNodes,"gew"),3,',',' ');
	$brgew = number_format(getValueForNode($childNodes,"brgew"),3,',',' ');
	$musterplatz = getValueForNode($childNodes,"f_muster_platz");
	$mustervom = getValueForNode($childNodes,"f_muster_vom");
	
	
	$pdfobjekt->Cell(30,$vyskaradku,$teilnr,'LBT',0,'L',1);
	
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(40,$vyskaradku,"Muster: $musterplatz Einlager: $mustervom",'LBT',0,'L',1);
	$pdfobjekt->Cell(0,$vyskaradku,"Gew: $gew kg, BrGew: $brgew kg",'LBTR',1,'L',1);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_import($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	
	$teilnr = getValueForNode($childNodes,"auftragsnr");
	
	$pdfobjekt->Cell(0,$vyskaradku,"Auftrag:  ".$teilnr,'1',1,'L',1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_taetigkeit($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumy,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	
	$tatnr = getValueForNode($childNodes,"tat");
	
	$pdfobjekt->Cell(	$cells['tat']['sirka']+
						$cells['datum']['sirka']+
						$cells['pal']['sirka']+
						$cells['oe']['sirka']+
						$cells['persnr']['sirka']+
						$cells['name']['sirka'],
						$vyskaradku,"Summe Taetigkeit ".$tatnr,'B',0,'L',1);
						
	$hodnota=number_format($sumy['stk'],0,',',' ');
	$pdfobjekt->Cell(	$cells['stk']['sirka'],
						$vyskaradku,$hodnota,'B',0,'R',1);

	$hodnota=number_format($sumy['aussstk'],0,',',' ');
	$pdfobjekt->Cell(	$cells['aussstk']['sirka'],
						$vyskaradku,$hodnota,'B',0,'R',1);
						
	$hodnota=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(	$cells['auss_typ']['sirka']+
						$cells['vzkd']['sirka']+
						$cells['sumvzkd']['sirka'],
						$vyskaradku,$hodnota,'B',0,'R',1);
						
	$hodnota=number_format($sumy['sumvzaby'],0,',',' ');
	$pdfobjekt->Cell(	$cells['vzaby']['sirka']+
						$cells['sumvzaby']['sirka'],
						$vyskaradku,$hodnota,'B',0,'R',1);

	$hodnota=number_format($sumy['verb'],0,',',' ');
	$pdfobjekt->Cell(	0,
						$vyskaradku,$hodnota,'B',1,'R',1);
						
						
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_import($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumy,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	
	$auftragnr = getValueForNode($childNodes,"auftragsnr");
	
	$pdfobjekt->Cell(	
						$cells['tat']['sirka']+
						$cells['datum']['sirka']+
						$cells['pal']['sirka']+
						$cells['oe']['sirka']+
						$cells['persnr']['sirka']+
						$cells['stk']['sirka']+
						$cells['aussstk']['sirka']+
						$cells['auss_typ']['sirka']+
						$cells['vzkd']['sirka']+
						$cells['name']['sirka'],
						$vyskaradku,
						"Summe Auftrag ".$auftragnr,
						'B',0,'L',1
					);
						
						
	$hodnota=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(	
						$cells['sumvzkd']['sirka'],
						$vyskaradku,
						$hodnota,
						'B',0,'R',1
					);
						
	$hodnota=number_format($sumy['sumvzaby'],0,',',' ');
	$pdfobjekt->Cell(	
						$cells['vzaby']['sirka']+
						$cells['sumvzaby']['sirka'],
						$vyskaradku,
						$hodnota,
						'B',0,'R',1
					);

	$hodnota=number_format($sumy['verb'],0,',',' ');
	$pdfobjekt->Cell(	
						0,
						$vyskaradku,
						$hodnota,
						'B',1,'R',1
					);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumy,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	
	$teilnr = getValueForNode($childNodes,"teilnr");
	
	$pdfobjekt->Cell(	
						$cells['tat']['sirka']+
						$cells['datum']['sirka']+
						$cells['pal']['sirka']+
						$cells['oe']['sirka']+
						$cells['persnr']['sirka']+
						$cells['stk']['sirka']+
						$cells['aussstk']['sirka']+
						$cells['auss_typ']['sirka']+
						$cells['vzkd']['sirka']+
						$cells['name']['sirka'],
						$vyskaradku,
						"Summe Teil ".$teilnr,
						'B',0,'L',1
					);
						
						
	$hodnota=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(	
						$cells['sumvzkd']['sirka'],
						$vyskaradku,
						$hodnota,
						'B',0,'R',1
					);
						
	$hodnota=number_format($sumy['sumvzaby'],0,',',' ');
	$pdfobjekt->Cell(	
						$cells['vzaby']['sirka']+
						$cells['sumvzaby']['sirka'],
						$vyskaradku,
						$hodnota,
						'B',0,'R',1
					);

	$hodnota=number_format($sumy['verb'],0,',',' ');
	$pdfobjekt->Cell(	
						0,
						$vyskaradku,
						$hodnota,
						'B',1,'R',1
					);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumy,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	
	$teilnr = getValueForNode($childNodes,"teilnr");
	
	$pdfobjekt->Cell(	
						$cells['tat']['sirka']+
						$cells['datum']['sirka']+
						$cells['pal']['sirka']+
						$cells['oe']['sirka']+
						$cells['persnr']['sirka']+
						$cells['stk']['sirka']+
						$cells['aussstk']['sirka']+
						$cells['auss_typ']['sirka']+
						$cells['vzkd']['sirka']+
						$cells['name']['sirka'],
						$vyskaradku,
						"Summe Bericht ",
						'B',0,'L',1
					);
						
						
	$hodnota=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(	
						$cells['sumvzkd']['sirka'],
						$vyskaradku,
						$hodnota,
						'B',0,'R',1
					);
						
	$hodnota=number_format($sumy['sumvzaby'],0,',',' ');
	$pdfobjekt->Cell(	
						$cells['vzaby']['sirka']+
						$cells['sumvzaby']['sirka'],
						$vyskaradku,
						$hodnota,
						'B',0,'R',1
					);

	$hodnota=number_format($sumy['verb'],0,',',' ');
	$pdfobjekt->Cell(	
						0,
						$vyskaradku,
						$hodnota,
						'B',1,'R',1
					);
}

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		return 1;
	}
	else
		return 0;
}
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S311 Leistung Auftrag - Teil - Tat - Datum - MA", $params);
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


dbConnect();

// zacinam po dilech
$teile=$domxml->getElementsByTagName("dil");
foreach($teile as $teil)
{
	
	$teilChildNodes=$teil->childNodes;
	$pdf->AddPage();
	pageheader($pdf,$cells_header,4);
	zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
		
	$importy = $teil->getElementsByTagName("import");
	nuluj_sumy_pole($sum_zapati_teil_array);
	
	foreach($importy as $import)
	{
		$importChildNodes=$import->childNodes;

		if(test_pageoverflow_noheader($pdf,5))
		{
			pageheader($pdf,$cells_header,4);
			zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
		}
		
		zahlavi_import($pdf,5,array(255,255,100),$importChildNodes);
		
		$taetigkeiten = $import->getElementsByTagName("taetigkeit");
		
		nuluj_sumy_pole($sum_zapati_import_array);
		
		foreach($taetigkeiten as $taetigkeit)
		{
			$taetigkeitChildNodes = $taetigkeit->childNodes;
			
			$positionen = $taetigkeit->getElementsByTagName("position");
			
			nuluj_sumy_pole($sum_zapati_taetigkeit_array);
			
			foreach($positionen as $position)
			{
				$positionChildNodes = $position->childNodes;
				
				if(test_pageoverflow_noheader($pdf,4))
				{
					pageheader($pdf,$cells_header,4);
					zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
				}
				
				detaily($pdf,$cells,4,array(255,255,255),$positionChildNodes);
				// aktualizuju sumy pro taetigkeit
				foreach($sum_zapati_taetigkeit_array as $key=>$prvek)
				{
					$hodnota=getValueForNode($positionChildNodes,$key);
					$sum_zapati_taetigkeit_array[$key]+=$hodnota;
				}
				
			}

			if(test_pageoverflow_noheader($pdf,5))
			{
				pageheader($pdf,$cells_header,4);
				zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
			}
			
			zapati_taetigkeit($pdf,5,array(255,255,255),$taetigkeitChildNodes,$sum_zapati_taetigkeit_array,$cells);
			// aktualizuju sumy pro import
			foreach($sum_zapati_import_array as $key=>$prvek)
			{
				$hodnota=$sum_zapati_taetigkeit_array[$key];
				$sum_zapati_import_array[$key]+=$hodnota;
			}
			
		}
		
		if(test_pageoverflow_noheader($pdf,5))
		{
			pageheader($pdf,$cells_header,4);
			zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
		}
		
		zapati_import($pdf,5,array(200,255,200),$importChildNodes,$sum_zapati_import_array,$cells);
		foreach($sum_zapati_teil_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_import_array[$key];
			$sum_zapati_teil_array[$key]+=$hodnota;
		}
		
	}
	
	if(test_pageoverflow_noheader($pdf,5))
	{
		pageheader($pdf,$cells_header,4);
		zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
	}
	
	zapati_teil($pdf,5,array(220,220,220),$teilChildNodes,$sum_zapati_teil_array,$cells);
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_teil_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
}

if(test_pageoverflow_noheader($pdf,5))
{
	pageheader($pdf,$cells_header,4);
	zahlavi_teil($pdf,5,array(220,220,220),$teilChildNodes);
}

zapati_sestava($pdf,5,array(200,200,255),$importChildNodes,$sum_zapati_sestava_array,$cells);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
