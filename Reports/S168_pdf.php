<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S168";
$doc_subject = "S168 Report";
$doc_keywords = "S168";

// necham si vygenerovat XML

$parameters=$_GET;
$datum=make_DB_datum($_GET['datum']);
//$schicht_von=$_GET['schicht_von'];
//$schicht_bis=$_GET['schicht_bis'];
$oe = trim(str_replace('*', '%', $oe));

require_once('S168_xml.php');


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
=> array ("popis"=>"","sirka"=>5,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"persnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"name" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"vorname" 
=> array ("popis"=>"","sirka"=>13,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),


"anwesenheit" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"fac1" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"fac2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verbgesamt"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'L',"align"=>"R","radek"=>0,"fill"=>0),

"anwesenheitgesamt"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>14,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"fac4"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_header = 
array(
"dummy1" 
=> array ("popis"=>"\nOE","sirka"=>5,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"persnr" 
=> array ("popis"=>"\nMitarbeiter","sirka"=>48,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"(1)\nVzKd","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"(2)\nVzAby","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"(3)\nVerb","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),


"anwesenheit" 
=> array ("nf"=>array(0,',',' '),"popis"=>"(4)\nAnwOE","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"fac1" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n(2)/(3)","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"fac2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n(2)/(4)","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"verbgesamt"
=> array ("nf"=>array(0,',',' '),"popis"=>"(5)\nVerbGes","sirka"=>14,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),

"anwesenheitgesamt"
=> array ("nf"=>array(0,',',' '),"popis"=>"(6)\nAnwGes","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"fac4"
=> array ("nf"=>array(0,',',' '),"popis"=>"\n(5)/(6)","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1)
);


$sum_zapati_schicht_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								"anwesenheit"=>0
								);
global $sum_zapati_schicht_array;

$sum_zapati_sestava_array = array(
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								"anwesenheit"=>0
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
function zahlavi_oe($pdfobjekt,$vyskaradku,$rgb,$oe,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(0,$vyskaradku,$oe,'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_oe($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1,$fac2,$fac3)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        // dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell($cells['dummy1']['sirka']+$cells['persnr']['sirka']+$cells['name']['sirka']+$cells['vorname']['sirka'],$vyskaradku,$popis,'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['vzkd']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['vzaby']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['verb']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['anwesenheit'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['anwesenheit']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	

	$obsah=$fac1;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['fac1']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac2;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['fac2']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac3;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,'','LB',1,'R',$fill);
	
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S168 Leistung->Anwesenheit", $params);
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
pageheader($pdf,$cells_header,3.5);
$pdf->Ln();
$pdf->Ln();


// a ted pujdu po po ogs
$ogs=$domxml->getElementsByTagName("ogs");
foreach($ogs as $og){
    $ogChilds = $og->childNodes;
    $oes=$og->getElementsByTagName("oes");
    foreach($oes as $oe)
    {
        $oeChilds = $oe->childNodes;
	$oekz=getValueForNode($oeChilds, 'oe');
	
	test_pageoverflow($pdf,5,$cells_header);
	zahlavi_oe($pdf,5,array(255,255,255),$oekz,$cells_header);
	
	nuluj_sumy_pole($sum_zapati_schicht_array);
	
	$persnodes=$oe->getElementsByTagName("pers");
	foreach($persnodes as $persnode)
	{
		$pers_childs=$persnode->childNodes;
		
		test_pageoverflow($pdf,3.5,$cells_header);
		telo($pdf,$cells,3.5,array(255,255,255),"",$pers_childs);
		
		// projedu pole a aktualizuju sumy pro zapati schicht
		foreach($sum_zapati_schicht_array as $key=>$prvek)
		{
			$hodnota=$persnode->getElementsByTagName($key)->item(0)->nodeValue;
			$sum_zapati_schicht_array[$key]+=$hodnota;
		}
	}

	if($sum_zapati_schicht_array['verb']!=0)
		$fac1=$sum_zapati_schicht_array['vzaby']/$sum_zapati_schicht_array['verb']*100;
	else
		$fac1=0;

	if($sum_zapati_schicht_array['anwesenheit']!=0)
		$fac2=$sum_zapati_schicht_array['vzaby']/$sum_zapati_schicht_array['anwesenheit']*100;
	else
		$fac2=0;
		
	if($sum_zapati_schicht_array['anwesenheit']!=0)
		$fac3=$sum_zapati_schicht_array['verb']/$sum_zapati_schicht_array['anwesenheit']*100;
	else
		$fac3=0;

	test_pageoverflow($pdf,5,$cells_header);
	zapati_oe($pdf,$persnode,5,"Summe ".$oekz,array(235,235,235),$sum_zapati_schicht_array,$fac1,$fac2,$fac3);
	// projedu pole a aktualizuju sumy pro zapati sestava
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_schicht_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
    }
}

if($sum_zapati_sestava_array['verb']!=0)
	$fac1=$sum_zapati_sestava_array['vzaby']/$sum_zapati_sestava_array['verb']*100;
else
	$fac1=0;

if($sum_zapati_sestava_array['anwesenheit']!=0)
	$fac2=$sum_zapati_sestava_array['vzaby']/$sum_zapati_sestava_array['anwesenheit']*100;
else
	$fac2=0;
	
if($sum_zapati_sestava_array['anwesenheit']!=0)
	$fac3=$sum_zapati_sestava_array['verb']/$sum_zapati_sestava_array['anwesenheit']*100;
else
	$fac3=0;

test_pageoverflow($pdf,5,$cells_header);
zapati_oe($pdf,$persnode,5,"Gesammtsumme ",array(200,200,255),$sum_zapati_sestava_array,$fac1,$fac2,$fac3);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
