<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';
    

$doc_title = "S166";
$doc_subject = "S166 Report";
$doc_keywords = "S166";

// necham si vygenerovat XML

$parameters=$_GET;
$von=make_DB_datum($_GET['von']);
$bis=make_DB_datum($_GET['bis']);
$persvon = intval($_GET['persvon']);
$persbis = intval($_GET['persbis']);

require_once('S166_xml.php');

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

"persnr"
=> array ("popis"=>"PersNr","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"persname"
=> array ("popis"=>"PersName","sirka"=>50,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"dt"
=> array ("popis"=>"DateTime","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"karta"
=> array ("popis"=>"Card","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stunden"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

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
function zahlavi_event($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
    
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $dt = getValueForNode($childs, 'dt');
        $badgenumber = getValueForNode($childs, 'badgenumber');
        
        $pdfobjekt->Cell($cells['persnr']['sirka'],$vyskaradku,'','1',0,'R',$fill);
        $pdfobjekt->Cell($cells['dt']['sirka'],$vyskaradku,$dt,'1',0,'L',$fill);
        $pdfobjekt->Cell($cells['karta']['sirka'],$vyskaradku,$badgenumber,'1',0,'R',$fill);
        $pdfobjekt->Cell(0,$vyskaradku,$datum,'1',1,'L',$fill);
        
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_tag($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $datum = getValueForNode($childs, 'datum');
        $pdfobjekt->Cell(0,$vyskaradku,$datum,'1',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_sestava($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $a = AplDB::getInstance();
        $lastEdataArray = $a->getLastParsedEdataFile();
        $lastEdataName = '';
        $lastEdataSize = '';
        $lastEdataStamp = '';
        
        if($lastEdataArray!==NULL){
            $lastEdataName = $lastEdataArray['filename'];
            $lastEdataSize = $lastEdataArray['size'];
            $lastEdataStamp = $lastEdataArray['stamp'];
        }
        
        $pdfobjekt->SetFont("FreeSans", "I", 7);
        
        $obsah = '';
        if(strlen($lastEdataName)>0){
            $obsah = sprintf("data z %s ( %s [%d] )",$lastEdataStamp,$lastEdataName,$lastEdataSize);
        }
        
        $pdfobjekt->Cell(0,$vyskaradku,$obsah,'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_person($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $persnr = getValueForNode($childs, 'persnr');
        $persname = getValueForNode($childs, 'persname');
        
        if($persnr==0){
            $persnr='';
            $persname='Neznama karta';
        }
        
        $pdfobjekt->Cell($cells['persnr']['sirka'],$vyskaradku,$persnr,'1',0,'R',$fill);
        $pdfobjekt->Cell($cells['persname']['sirka'],$vyskaradku,$persname,'BT',0,'L',$fill);
        $pdfobjekt->Cell(0,$vyskaradku,'','BTR',1,'R',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S166 Anwesenheit-Tag-EData", $params);
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
//pageheader($pdf,$cells_header,5);

$tage=$domxml->getElementsByTagName("tag");
foreach($tage as $tag)
{
        $tagChilds = $tag->childNodes;
        zahlavi_tag($pdf, 5, array(240,255,240), $tagChilds);
        $personen = $tag->getElementsByTagName("person");
        foreach($personen as $person){
            $personChilds = $person->childNodes;
            zahlavi_person($pdf, 5, array(240,240,255), $personChilds);
            $events = $person->getElementsByTagName("event");
            foreach($events as $event){
                $eventChilds = $event->childNodes;
                zahlavi_event($pdf, 5, array(255,255,255), $eventChilds);
            }
        }
}
$pdf->Ln();
zahlavi_sestava($pdf, 5, array(255,255,255), NULL);
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
