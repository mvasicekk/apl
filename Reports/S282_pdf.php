<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S282";
$doc_subject = "S282 Report";
$doc_keywords = "S282";

// necham si vygenerovat XML

$parameters=$_GET;

$datum_von=make_DB_datum($_GET['datum_von']);
$datum_bis=make_DB_datum($_GET['datum_bis']);
$schicht_von=$_GET['schicht_von'];
$schicht_bis=$_GET['schicht_bis'];
$pers_von=$_GET['pers_von'];
$pers_bis=$_GET['pers_bis'];
$kunde=$_GET['kunde'];
$password = $_GET['password'];
$user = $_SESSION['user'];

if(!testReportPassword("S284",$password,$user))
	echo "password ($password,$user)";
else
{
require_once('S282_xml.php');


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

"Teil" 
=> array ("popis"=>"","sirka"=>17,"ram"=>'L',"align"=>"L","radek"=>0,"fill"=>0),

"pal" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"Teilbez" 
=> array ("popis"=>"","sirka"=>40,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"drueck_schicht" 
=> array ("popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"TaetNr" 
=> array ("popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_typ" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"vzaby_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

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

$cells_header = 
array(

"Teil" 
=> array ("popis"=>"Teil","sirka"=>17,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"pal" 
=> array ("popis"=>"Pal","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"Teilbez" 
=> array ("popis"=>"Bezeichnung","sirka"=>40,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"drueck_schicht" 
=> array ("popis"=>"Sch.","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"TaetNr" 
=> array ("popis"=>"TatNr","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"stk" 
=> array ("popis"=>"Stk","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auss_stk" 
=> array ("popis"=>"Auss","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auss_typ" 
=> array ("popis"=>"AussTyp","sirka"=>10,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"vzaby_stk" 
=> array ("popis"=>"vzaby/stk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"verb_stk" 
=> array ("popis"=>"verb/stk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("popis"=>"vzaby","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("popis"=>"verb","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"ma" 
=> array ("popis"=>"auft","sirka"=>5,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"von" 
=> array ("popis"=>"von","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"bis" 
=> array ("popis"=>"bis","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_auftrag_array = array(	
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_auftrag_array;

$sum_zapati_pers_array = array(	
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_pers_array;

$sum_zapati_datum_array = array(	
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_datum_array;

$sum_zapati_sestava_array = array(	
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
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
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
function zahlavi_pers($pdfobjekt,$vyskaradku,$rgb,$cells_header,$persnr,$name,$vorname,$schicht,$datumnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(15,$vyskaradku,$persnr,'LT',0,'L',$fill);
	$pdfobjekt->Cell(30,$vyskaradku,$name." ".$vorname,'T',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,$schicht."   ( ".substr($datumnr,0,10)." )",'TR',1,'L',$fill);
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_auftrag($pdfobjekt,$vyskaradku,$rgb,$cells_header,$auftragsnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);

	$pdfobjekt->Cell(0,$vyskaradku,"AuftragsNr: ".$auftragsnr,'LR',1,'L',$fill);
	
	
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_datum($pdfobjekt,$vyskaradku,$rgb,$datumnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);

	$pdfobjekt->Cell(0,$vyskaradku,"Datum: ".substr($datumnr,0,10),'1',1,'L',$fill);
	
	
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_pers($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(125+8,$vyskaradku,$popis,'LB',0,'L',$fill);
	
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	//dummy
	$pdfobjekt->Cell(0,$vyskaradku,"",'BR',1,'R',$fill);

	$pdfobjekt->Ln();
	
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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

	$pdfobjekt->Cell(125+8,$vyskaradku,$popis." ".substr($datumnr,0,10),'LTB',0,'L',$fill);
	
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	//dummy
	$pdfobjekt->Cell(0,$vyskaradku,"",'TBR',1,'R',$fill);

	$pdfobjekt->Ln();
	
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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

	$pdfobjekt->Cell(125+8,$vyskaradku,$popis,'LTB',0,'L',$fill);
	
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	//dummy
	$pdfobjekt->Cell(0,$vyskaradku,"",'TBR',1,'R',$fill);

	$pdfobjekt->Ln();
	
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$datumnr)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
//		zahlavi_datum($pdfobjekt,5,array(255,255,200),$datumnr);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S282 Abrechnung Werkvertraege je MA", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 7));
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
		
	test_pageoverflow($pdf,5,$cells_header,$datumnr);
	nuluj_sumy_pole($sum_zapati_datum_array);
	
	$personal=$datum->getElementsByTagName("pers");
	
	foreach($personal as $pers)
	{
		$persnr=$pers->getElementsByTagName("PersNr")->item(0)->nodeValue;
		$name=$pers->getElementsByTagName("Name")->item(0)->nodeValue;
		$vorname=$pers->getElementsByTagName("Vorname")->item(0)->nodeValue;
		$schicht=$pers->getElementsByTagName("schicht")->item(0)->nodeValue;
				
		test_pageoverflow($pdf,5,$cells_header,$datumnr);
		zahlavi_pers($pdf,5,array(235,235,235),$cells_header,$persnr,$name,$vorname,$schicht,$datumnr);
		nuluj_sumy_pole($sum_zapati_pers_array);
		
		$auftraege=$pers->getElementsByTagName("auftrag");
		
		foreach($auftraege as $auftrag)
		{
		
			$auftragsnr=$auftrag->getElementsByTagName("AuftragsNr")->item(0)->nodeValue;
			test_pageoverflow($pdf,5,$cells_header,$datumnr);
			zahlavi_auftrag($pdf,5,array(255,255,255),$cells_header,$auftragsnr);
			nuluj_sumy_pole($sum_zapati_auftrag_array);
			
			$positionen=$auftrag->getElementsByTagName("position");
			
			foreach($positionen as $position)
			{
				$position_childs=$position->childNodes;
				test_pageoverflow($pdf,5,$cells_header,$datumnr);
				telo($pdf,$cells,5,array(255,255,255),"",$position_childs);
				
				// projedu pole a aktualizuju sumy pro zapati pers
				foreach($sum_zapati_auftrag_array as $key=>$prvek)
				{
					$hodnota=$position->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_auftrag_array[$key]+=$hodnota;
				}
			}
			
			// sumy pro zapati pers
			foreach($sum_zapati_pers_array as $key=>$prvek)
			{
				$hodnota=$sum_zapati_auftrag_array[$key];
				$sum_zapati_pers_array[$key]+=$hodnota;
			}
		}
		
		test_pageoverflow($pdf,5,$cells_header,$datumnr);
		zapati_pers($pdf,$pers,5,"Summe PersNr",array(235,235,235),$sum_zapati_pers_array);
		
		foreach($sum_zapati_datum_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_pers_array[$key];
			$sum_zapati_datum_array[$key]+=$hodnota;
		}

	}
	
	test_pageoverflow($pdf,5,$cells_header,$datumnr);
	zapati_datum($pdf,$pers,5,"Summe Datum",array(235,235,235),$sum_zapati_datum_array,$datumnr);
		
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_datum_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
}

test_pageoverflow($pdf,5,$cells_header,$datumnr);
zapati_sestava($pdf,$import,5,"Summe Bericht",array(200,200,255),$sum_zapati_sestava_array);


//Close and output PDF document
$pdf->Output();
}
//============================================================+
// END OF FILE                                                 
//============================================================+

?>
