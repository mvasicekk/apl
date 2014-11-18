<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "T100";
$doc_subject = "T100 Report";
$doc_keywords = "T100";

// necham si vygenerovat XML

$parameters=$_GET;

$a = AplDB::getInstance();
//reportname;benutzer;von;bis

$reportname = strtr(trim($_GET['reportname']),'*','%');
$benutzer = strtr(trim($_GET['benutzer']),'*','%');
$von = $a->make_DB_datum($_GET['von']);
$bis = $a->make_DB_datum($_GET['bis']);
$details = $_GET['details'];

if($details=="a")
    $bDetails = TRUE;

require_once('T100_xml.php');

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

"reportnr"
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"stamp"
=> array ("popis"=>"","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"reporturl"
=> array ("substr"=>array(0,170),"popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"L","radek"=>1,"fill"=>0),
);

$sum_report = array();
$sum_user = array();

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
		
	if(array_key_exists("substr", $cell)){
	    $suffix = '';
	    if(strlen($cellobsah)>$cell['substr'][1]) $suffix='...';
	    $cellobsah = substr($cellobsah, $cell['substr'][0], $cell['substr'][1]).$suffix;
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

function zahlavi_report($p,$rowHeight,$childNodes,$rgb)
{
	$p->SetFont("FreeSans", "", 10);
	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill = 1;
	$reportnr = getValueForNode($childNodes, 'reportnr');
	$p->Cell(0,$rowHeight,$reportnr,'1',1,'L',$fill);
}

function zapati_report($p,$rowHeight,$childNodes,$sum,$rgb)
{
	$p->SetFont("FreeSans", "", 10);
	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill = 1;
	$reportnr = getValueForNode($childNodes, 'reportnr');
	$p->Cell(50,$rowHeight,$reportnr,'1',0,'L',$fill);
	$p->Cell(0,$rowHeight,  number_format($sum, 0, ',', ' ').'x','1',1,'R',$fill);
}

function zahlavi_user($p,$rowHeight,$childNodes,$rgb)
{
	$p->SetFont("FreeSans", "", 10);
	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill = 1;
	$obsah = getValueForNode($childNodes, 'u');
	$p->Cell(0,$rowHeight,$obsah,'1',1,'L',$fill);
}

function zapati_user($p,$rowHeight,$childNodes,$sum,$rgb)
{
	$p->SetFont("FreeSans", "", 10);
	$p->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill = 1;
	$reportnr = getValueForNode($childNodes, 'u');
	$p->Cell(50,$rowHeight,$reportnr,'1',0,'L',$fill);
	$p->Cell(0,$rowHeight,  number_format($sum, 0, ',', ' ').'x','1',1,'R',$fill);
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "T100 Reportusage", $params);
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
//pageheader($pdf,$cells_header,5);


$reports = $domxml->getElementsByTagName("report");

foreach($reports as $report){
    $reportChilds = $report->childNodes;
    $reportnr = getValueForNode($reportChilds, 'reportnr');
    zahlavi_report($pdf, 5, $reportChilds, array(230,255,230));
    $users = $report->getElementsByTagName("user");
    foreach($users as $user){
	$userChilds = $user->childNodes;
	$userid = getValueForNode($userChilds, 'u');
//	zahlavi_user($pdf, 5, $userChilds, array(230,230,255));
	$logs = $user->getElementsByTagName("log");
	foreach($logs as $log){
	    $logChilds = $log->childNodes;
	    if($bDetails) 
		detaily($pdf,$cells,5,array(255,255,255),$logChilds);
	    $sum_report[$reportnr]+=1;
	    $sum_user[$reportnr][$userid]+=1;
	}
	zapati_user($pdf, 5, $userChilds, $sum_user[$reportnr][$userid],array(230,230,255));
    }
    zapati_report($pdf, 5, $reportChilds, $sum_report[$reportnr],array(230,255,230));
    $pdf->Ln();
}

//echo "<pre>";
//var_dump($sum_report);
//echo "</pre>";
//
//echo "<pre>";
//var_dump($sum_user);
//echo "</pre>";

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
