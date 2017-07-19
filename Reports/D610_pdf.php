<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D610";
$doc_subject = "D610 Report";
$doc_keywords = "D610";

$apl = AplDB::getInstance();
// necham si vygenerovat XML

$parameters=$_GET;

$t = $_GET['termin'];
$termin = "P".$t;

require_once('D610_xml.php');

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

"teilnr" 
=> array ("popis"=>"Teil","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"teilbezeichnung"
=> array ("popis"=>"Bezeichnung","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"vpe" 
=> array ("nf"=>array(0,',',' '),"popis"=>"VPE","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gew" 
=> array ("nf"=>array(2,',',' '),"popis"=>"kg/Stk","sirka"=>23,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Stk","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_gew" 
=> array ("nf"=>array(2,',',' '),"popis"=>"Gew [to]","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0),
);

$sum_zapati_sestava_array = array(	
								"sum_gew"=>0,
								);

$teloRow = 0;
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


function zapatiSestava($pdf,$rowHeight,$rgb,$sumArray){
    global $cells;
    $pdf->SetFont("FreeSans", "B", 9);
    $pdf->SetFillColor(255, 255, 200, 1);
    $pdf->Cell(
	    $cells['teilnr']['sirka']
	    +$cells['teilbezeichnung']['sirka']
	    +$cells['vpe']['sirka']
	    +$cells['gew']['sirka']
	    +$cells['stk']['sirka']
	    ,$rowHeight
	    ,"Summe Gewicht [to] Netto"
	    ,'BT'
	    ,0
	    ,'L'
	    ,1
	    );
    
    $pdf->Cell(
	    0
	    ,$rowHeight
	    ,  number_format($sumArray['sum_gew'], 2, ',',' ')
	    ,'BT'
	    ,1
	    ,'R'
	    ,1
	    );
}
// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt, $pole, $headervyskaradku, $geplannt, $exdatum,$zielort="") {
    $pdfobjekt->SetFont("FreeSans", "B", 9);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    foreach ($pole as $key=>$cell) {
            $obsah = $cell['popis'];
	    $pdfobjekt->MyMultiCell($cell["sirka"], $headervyskaradku, $obsah, '1', $cell["align"], 1);
    }
    $pdfobjekt->Ln();
}

// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
    global $teloRow;
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,255);
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
		if($teloRow%2==0){
		    $pdfobjekt->SetFillColor(230,255,230);
		}
		else{
		    $pdfobjekt->SetFillColor(255,255,255);
		}
		
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],1);
	}
	$teloRow++;
//	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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

function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku,$geplannt,$exdatum);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D610 - Lieferavis", '');
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 11));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);
$pdf->AddPage();
dbConnect();

$terminInfoA = $apl->getAuftragInfoArray($t);
if($terminInfoA!==NULL){
    $tI = $terminInfoA[0];
    $uhr = $tI['exsolluhr1'];
    $datum = $tI['exsolldat1'];
    $kd = $apl->getKundeFromAuftransnr($t);
    $zielOrt = $apl->getZielortName($tI['zielort_id']);
    
    $cellobsah = "Plan: ".$t." ( ".$datum.' '.$uhr.' ) '.$zielOrt;
    $pdf->SetFont("FreeSans", "B", 11);
    $pdf->Cell(0,5,$cellobsah,'0',0,'L',0);
    $pdf->Ln(20);
}

// a ted pujdu po terminech
$teile=$domxml->getElementsByTagName("teil");

pageheader($pdf, $cells, 5, $termin, $exdatum, $zielort);
foreach ($teile as $teil) {
    
    $teilChilds = $teil->childNodes;
//    AplDB::varDump($teilChilds);
    test_pageoverflow($pdf, 5, $cells, 5);
    telo($pdf, $cells, 5, array(255, 255, 255), "", $teilChilds);
    foreach ($sum_zapati_sestava_array as $f=>$a){
	$sum_zapati_sestava_array[$f]+=getValueForNode($teilChilds, $f);
    }
}

zapatiSestava($pdf, 7, array(255,255,200), $sum_zapati_sestava_array);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
