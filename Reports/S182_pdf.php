<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S182";
$doc_subject = "S182 Report";
$doc_keywords = "S182";

// necham si vygenerovat XML

$parameters=$_GET;
$von = make_DB_datum($_GET['von']);
$bis = make_DB_datum($_GET['bis']);
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];


require_once('S182_xml.php');

$cells =
array(
'dummy1'=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'persnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'vollname'=> array ("popis"=>"","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'datum'=> array ("popis"=>"","sirka"=>25,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'typ'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'unfalltyp'=> array ("popis"=>"","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"B","radek"=>1,"fill"=>0),
    );


$cells_header =
array(
'dummy1'=> array ("popis"=>"\n","sirka"=>$cells['dummy1']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'persnr'=> array ("popis"=>"\nPersnr","sirka"=>$cells['persnr']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'vollname'=> array ("popis"=>"\nName","sirka"=>$cells['vollname']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'datum'=> array ("popis"=>"\nDatum","sirka"=>$cells['datum']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'typ'=> array ("popis"=>"\nUnftyp","sirka"=>$cells['typ']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'unfalltyp'=> array ("popis"=>"\nUnfall","sirka"=>$cells['unfalltyp']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'lf'=> array ("popis"=>"\n","sirka"=>$cells['lf']['sirka'],"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>1),
);

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param) {
    $parametry=$param->childNodes;
    // v ramci parametru si prectu label a hodnotu
    foreach($parametry as $parametr) {
        $parametr=$parametr->childNodes;
        foreach($parametr as $par) {
            if($par->nodeName=="label")
                $label=$par->nodeValue;
            if($par->nodeName=="value")
                $value=$par->nodeValue;
        }
        if(strtolower($label)!="password")
            $params .= $label.": ".$value."  ";
    //		$params .= $label.": ".$value."  ";
    }
}

$sum_zapati_sestava_array;
$sum_bericht_array;

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
/**
 * funkce pro vykresleni hlavicky na kazde strance
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdf,$pole,$headervyskaradku) {
    
    $fill = 1;
    $pdf->SetFillColor(255,255,230);
    $pdf->SetFont("FreeSans", "B", 6.5);

    $pdf->Cell($pole['dummy1']['sirka'],$headervyskaradku,'Jahr - Monat','B',0,'L',$fill);
    $pdf->Cell($pole['persnr']['sirka'],$headervyskaradku,'PersNr','B',0,'R',$fill);
    $pdf->Cell($pole['vollname']['sirka'],$headervyskaradku,'Name','B',0,'L',$fill);
    $pdf->Cell($pole['datum']['sirka'],$headervyskaradku,'Datum','B',0,'L',$fill);
    $pdf->Cell($pole['typ']['sirka'],$headervyskaradku,'Unftyp','B',0,'R',$fill);
    $pdf->Cell($pole['unfalltyp']['sirka'],$headervyskaradku,'Unfall','B',0,'L',$fill);
    
    $pdf->Cell(0,$headervyskaradku,'','B',1,'L',$fill);
}


/**
 * funkce ktera vrati hodnotu podle nodename
 * predam ji nodelist a jmeno node ktereho hodnotu hledam
 * @param <type> $nodelist
 * @param <type> $nodename
 * @return <type>
 */
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



function person_radek($pdfobjekt,$vyskaradku,$rgb,$person,$monat,$jahr,$svatky){
}

function zahlavi_monat($pdf,$vyskaRadku,$rgb,$childs){

    $fill = 1;
    $pdf->SetFont("FreeSans", "B", 6.5);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $text = getValueForNode($childs, 'jahrnr').' - '.getValueForNode($childs, 'monatnr');
    $pdf->Cell(0,$vyskaRadku,$text,'1',0,'L',$fill);

    //dalsi radek
    $pdf->Ln();
}

function zapati_sestava($pdf,$vyskaRadku,$rgb,$pole,$unfallTypArray){

    global $cells;
    $pole1 = $pole;
    $pole = $cells;
    
    $fill = 1;
    $pdf->SetFont("FreeSans", "B", 6.5);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $pdf->Ln();
    $pdf->Cell($pole['dummy1']['sirka']+$pole['persnr']['sirka']+$pole['vollname']['sirka']+$pole['datum']['sirka'],$headervyskaradku,'','0',0,'L',0);
    $pdf->Cell($pole['typ']['sirka']+$pole['unfalltyp']['sirka']+20,$vyskaRadku,'Berichtsumme - Anzahl Unfaelle nach Typen','1',1,'L',$fill);
    // zahlavi tabulky
    $pdf->Cell($pole['dummy1']['sirka']+$pole['persnr']['sirka']+$pole['vollname']['sirka']+$pole['datum']['sirka'],$headervyskaradku,'','0',0,'L',0);
    $pdf->Cell($pole['typ']['sirka'],$vyskaRadku,'Unftyp','1',0,'R',$fill);
    $pdf->Cell($pole['unfalltyp']['sirka'],$vyskaRadku,'Unfall','1',0,'L',$fill);
    $pdf->Cell(20,$vyskaRadku,'Anzahl','1',0,'R',$fill);
    $pdf->Ln();

    $fill = 0;
    foreach ($unfallTypArray as $typ=>$text){
        $pdf->Cell($pole['dummy1']['sirka']+$pole['persnr']['sirka']+$pole['vollname']['sirka']+$pole['datum']['sirka'],$headervyskaradku,'','0',0,'L',0);
        $pdf->Cell($pole['typ']['sirka'],$vyskaRadku,$typ,'1',0,'R',$fill);
        $pdf->Cell($pole['unfalltyp']['sirka'],$vyskaRadku,$text,'1',0,'L',$fill);
        $pdf->Cell(20,$vyskaRadku,$pole1[$typ],'1',0,'R',$fill);
        $pdf->Ln();
    }
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vysradku
 * @param <type> $cellhead
 * @param <type> $jahr
 * @param <type> $monat
 */
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

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S182 Unfalluebersicht", $params);
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
pageheader($pdf, $cells, 5);
$jahren = $domxml->getElementsByTagName('jahr');
foreach($jahren as $jahr){
    $jahrChilds  = $jahr->childNodes;
    $monate = $jahr->getElementsByTagName('monat');
    foreach($monate as $monat){
        $monatChilds = $monat->childNodes;
        test_pageoverflow($pdf, 5, $cells);
        zahlavi_monat($pdf,5,array(255,255,230),$monatChilds);
        $unfalle = $monat->getElementsByTagName('unfall');
        foreach($unfalle as $unfall){
            $unfallChilds = $unfall->childNodes;
            test_pageoverflow($pdf, 5, $cells);
            telo($pdf, $cells, 5, array(255,255,255), '', $unfallChilds);
            $unfallId = getValueForNode($unfallChilds, 'typ');
            $sum_zapati_sestava_array[getValueForNode($jahrChilds, 'jahrnr')][getValueForNode($monatChilds, 'monatnr')][$unfallId] += 1;
            $sum_bericht_array[$unfallId] += 1;
            $unfallTypArray[$unfallId] = getValueForNode($unfallChilds, 'unfalltyp');
        }
    }
}

//var_dump($sum_zapati_sestava_array);
ksort($unfallTypArray);
//test_pageoverflow($pdf, 5, $cells);
zapati_sestava($pdf, 5, array(255,255,230), $sum_bericht_array, $unfallTypArray);
//var_dump($sum_bericht_array);
//var_dump($unfallTypArray);

$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>