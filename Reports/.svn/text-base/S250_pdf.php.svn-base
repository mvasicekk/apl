<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S250";
$doc_subject = "S250 Report";
$doc_keywords = "S250";

// necham si vygenerovat XML

$parameters=$_GET;

$datum_von=make_DB_datum($_GET['datum_von']);
$datum_bis=make_DB_datum($_GET['datum_bis']);
$schicht_von=$_GET['schicht_von'];
$schicht_bis=$_GET['schicht_bis'];


require_once('S250_xml.php');


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

"schicht" 
=> array ("popis"=>"","sirka"=>90,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"auftragsnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(

"schicht" 
=> array ("popis"=>"Schicht","sirka"=>90,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"auftragsnr" 
=> array ("popis"=>"AuftragsNr","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"VzKd","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"VzAby","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Verb","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_schicht_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_schicht_array;

$sum_zapati_datum_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_datum_array;

$sum_zapati_sestava_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
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
function zapati_schicht($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$schichtnr,$schichtfuehrer)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(30,$vyskaradku,$popis,'B',0,'L',$fill);
	
	$pdfobjekt->Cell(60+20,$vyskaradku,$schichtnr." ".$schichtfuehrer,'B',0,'L',$fill);

	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_datum($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$datumnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(30,$vyskaradku,$popis,'B',0,'L',$fill);
	
	$pdfobjekt->Cell(60+20,$vyskaradku,$datumnr,'B',0,'L',$fill);

	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(30+60+20,$vyskaradku,$popis,'B',0,'L',$fill);
	
	//$pdfobjekt->Cell(60+20,$vyskaradku,$datumnr,'B',0,'L',$fill);

	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S250 Leistung Schicht - Auftrag", $params);
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


// a ted pujdu po datumech
$datumy=$domxml->getElementsByTagName("datumy");
foreach($datumy as $datum)
{
	$datumnr=$datum->getElementsByTagName("datum")->item(0)->nodeValue;
		
	//test_pageoverflow($pdf,5,$cells_header);
	//zahlavi_teil($pdf,5,array(255,255,200),$teilnr,$cells_header);
	nuluj_sumy_pole($sum_zapati_datum_array);
	
	$sichty=$datum->getElementsByTagName("schicht");
	
	foreach($sichty as $schicht)
	{
		$schichtnr=$schicht->getElementsByTagName("schichtnr")->item(0)->nodeValue;
		$schichtfuehrer=$schicht->getElementsByTagName("Schichtfuehrer")->item(0)->nodeValue;
				
		//test_pageoverflow($pdf,5,$cells_header);
		//zahlavi_auftrag($pdf,5,array(255,255,255),$auftragsnr,$cells_header);
		nuluj_sumy_pole($sum_zapati_schicht_array);
		
		$auftraege=$schicht->getElementsByTagName("auftrag");
		
		foreach($auftraege as $auftrag)
		{
			$auftrag_childs=$auftrag->childNodes;
			test_pageoverflow($pdf,5,$cells_header);
			telo($pdf,$cells,5,array(255,255,255),"",$auftrag_childs);
			// projedu pole a aktualizuju sumy pro zapati schicht
			foreach($sum_zapati_schicht_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_schicht_array[$key]+=$hodnota;
			}
		}

		test_pageoverflow($pdf,5,$cells_header);
		zapati_schicht($pdf,$teilnode,5,"Summe Schicht",array(235,235,235),$sum_zapati_schicht_array,$schichtnr,$schichtfuehrer);
			
		// sumy pro zapati datum
		foreach($sum_zapati_datum_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_schicht_array[$key];
			$sum_zapati_datum_array[$key]+=$hodnota;
		}
		
	}
	
		test_pageoverflow($pdf,5,$cells_header);
		zapati_datum($pdf,$teilnode,5,"Summe Datum",array(235,235,235),$sum_zapati_datum_array,$datumnr);
			
		// sumy pro zapati sestava
		foreach($sum_zapati_sestava_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_datum_array[$key];
			$sum_zapati_sestava_array[$key]+=$hodnota;
		}

}

test_pageoverflow($pdf,10,$cells_header);
$pdf->Ln();
zapati_sestava($pdf,$import,5,"Summe Bericht",array(200,200,255),$sum_zapati_sestava_array);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
