<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S816";
$doc_subject = "S816 Report";
$doc_keywords = "S816";

// necham si vygenerovat XML

$parameters=$_GET;
$auftragsnr_von=$_GET['auftragsnr_von'];
$auftragsnr_bis=$_GET['auftragsnr_bis'];
$teil=strtr($_GET['teil'],'*','%');


require_once('S817_xml.php');

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

$cells = 
array(
"dummy1" 
=> array ("popis"=>"","sirka"=>45,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"stkauftrag" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gutstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"pal" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"Gew" 
=> array ("nf"=>array(3,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vahacelkem" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vahacelkembr" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"Termin" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)
);

$cells_header = array(

"dummy1" 
=> array ("popis"=>"","sirka"=>45,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"Teil","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"stkauftrag" 
=> array ("popis"=>"StkAuftrag","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"gutstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"GTat","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"auss" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"pal" 
=> array ("nf"=>array(0,',',' '),"popis"=>"pal","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"Gew" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Gew","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vahacelkem" 
=> array ("nf"=>array(0,',',' '),"popis"=>"GesGewN","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vahacelkembr" 
=> array ("nf"=>array(0,',',' '),"popis"=>"GesGewBr","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"Termin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"geplannt","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>0)
);

$sum_zapati_teil_array = array(	
								"stkauftrag"=>0,
								"gutstk"=>0,
								"auss"=>0,
								"vahacelkem"=>0,
                                                                "vahacelkembr"=>0
								);
global $sum_zapati_teil_array;

$sum_zapati_auftrag_array = array(
									"stkauftrag"=>0,
									"gutstk"=>0,
									"auss"=>0,
									"vahacelkem"=>0,
                                                                        "vahacelkembr"=>0
								);
								
global $sum_zapati_auftrag_array;


$sum_zapati_sestava_array = array(
								"stkauftrag"=>0,
								"gutstk"=>0,
								"auss"=>0,
								"vahacelkem"=>0,
                                                                "vahacelkembr"=>0
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

function zahlavi_ex($pdfobjekt,$node,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(20,$vyskaradku,"EX ".$node->getElementsByTagName("ex")->item(0)->nodeValue,'LBT',0,'L',$fill);

	$obsah=$node->getElementsByTagName("sumpreis_leistung_EUR")->item(0)->nodeValue;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(50,$vyskaradku,"Re-Preis (Leistung) ".$obsah,'BT',0,'L',$fill);

	
	$obsah=$node->getElementsByTagName("sumpreis_sonst_EUR")->item(0)->nodeValue;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(50,$vyskaradku,"Re-Preis (Sonst.) ".$obsah,'BT',0,'L',$fill);
	
	$obsah=$node->getElementsByTagName("preismin")->item(0)->nodeValue;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"preismin ".$obsah,'BTR',1,'L',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_auftrag($pdfobjekt,$vyskaradku,$rgb,$auftragsnr,$aufdat,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Cell(0,$vyskaradku,"Auftragsnr: ".$auftragsnr." ( ".$aufdat." )",'LBTR',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$vyskaradku,$cell["popis"],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_ex($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(15,$vyskaradku,$popis,'T',0,'L',$fill);
	
	$obsah=$pole['stkauftrag'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);

	$obsah=$pole['gutstk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);
	
	$obsah=$pole['auss'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'L',$fill);

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);
	
	$obsah=$pole['vahacelkem'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'T',1,'L',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_auftrag($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(60,$vyskaradku,$popis,'T',0,'L',$fill);
	
	$obsah=$pole['stkauftrag'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);

	$obsah=$pole['gutstk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);
	
	$obsah=$pole['auss'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'L',$fill);

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);
	
	$obsah=$pole['vahacelkem'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);

	$obsah=$pole['vahacelkembr'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',$fill);
        
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'T',1,'L',$fill);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S817 T_ohne Leistung", $params);
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
//pageheader($pdf,$cells_header,3.5);
//$pdf->Ln();
//$pdf->Ln();


// a ted pujdu po zakazkach
$auftraege=$domxml->getElementsByTagName("auftraege");
foreach($auftraege as $auftrag)
{
	$auftragsnr=$auftrag->getElementsByTagName("AuftragsNr")->item(0)->nodeValue;
	$aufdat=$auftrag->getElementsByTagName("Aufdat")->item(0)->nodeValue;
	zahlavi_auftrag($pdf,5,array(255,255,200),$auftragsnr,$aufdat,$cells_header);
	
	nuluj_sumy_pole($sum_zapati_auftrag_array);
	
	$teilnodes=$auftrag->getElementsByTagName("teil");
	foreach($teilnodes as $teilnode)
	{
		nuluj_sumy_pole($sum_zapati_teil_array);
		$palettenodes=$teilnode->getElementsByTagName("palette");
		foreach($palettenodes as $palettenode)
		{
			$palette_childs=$palettenode->childNodes;
			telo($pdf,$cells,3.5,array(255,255,255),"",$palette_childs);
			// spocitam sumy vybranych sloupcu
			foreach($sum_zapati_teil_array as $key=>$prvek)
			{
				$hodnota=$palettenode->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_teil_array[$key]+=$hodnota;
			}
		}
		
		zapati_ex($pdf,$palettenode,5,"SumTeil",array(255,255,230),$sum_zapati_teil_array);
		
		// projedu pole a aktualizuju sumy pro zapati auftrag
		foreach($sum_zapati_auftrag_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_teil_array[$key];
			$sum_zapati_auftrag_array[$key]+=$hodnota;
		}
	}
	
	zapati_auftrag($pdf,$palettenode,5,"Summe Auftrag",array(255,255,200),$sum_zapati_auftrag_array);
	// projedu pole a aktualizuju sumy pro zapati sestava
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_auftrag_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
}

zapati_auftrag($pdf,$palettenode,5,"Gesammtsumme ",array(200,200,255),$sum_zapati_sestava_array);



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
