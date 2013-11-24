<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S184";
$doc_subject = "S184 Report";
$doc_keywords = "S184";

// necham si vygenerovat XML

$parameters=$_GET;
$von = make_DB_datum($_GET['von']);
$bis = make_DB_datum($_GET['bis']);
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];


require_once('S184_xml.php');

//exit;

$cells =
        array(
            'persnr' => array("popis" => "", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
            'name' => array("popis" => "", "sirka" => 40, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'datum' => array("popis" => "", "sirka" => 20, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'grund' => array("substring"=>array(0,15),"popis" => "", "sirka" => 25, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'bemerk' => array("popis" => "", "sirka" => 50, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'betr' => array("nf"=>array(0,',',' '),"popis" => "", "sirka" => 15, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
            'betrdat' => array("popis" => "", "sirka" => 20, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
//	    'vorschlag' => array("popis" => "", "sirka" => 5, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
	    'vorschlag_von' => array("popis" => "", "sirka" => 20, "ram" => 'LB', "align" => "L", "radek" => 0, "fill" => 0),
	    'vorschlag_betrag' => array("popis" => "", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'rekl_nr' => array("popis" => "", "sirka" => 20, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
	    'vorschlag_bemerkung' => array("popis" => "", "sirka" => 0, "ram" => 'B', "align" => "L", "radek" => 1, "fill" => 0),
);


$cells_header =
        array(
            'persnr' => array("popis" => "Persnr", "sirka" => $cells['persnr']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
            'name' => array("popis" => "Name", "sirka" => $cells['name']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
            'datum' => array("popis" => "Datum", "sirka" => $cells['datum']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
            'grund' => array("popis" => "Grund", "sirka" => $cells['grund']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
            'bemerk' => array("popis" => "Bemerkung", "sirka" => $cells['bemerk']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
            'betr' => array("popis" => "Betrag", "sirka" => $cells['betr']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
            'betrdat' => array("popis" => "AbmahnDat", "sirka" => $cells['betrdat']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
//	    'vorschlag' => array("popis" => "Vor", "sirka" => $cells['vorschlag']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
	    'vorschlag_von' => array("popis" => "Vors. von", "sirka" => $cells['vorschlag_von']['sirka'], "ram" => 'LB', "align" => "L", "radek" => 0, "fill" => 1),
	    'vorschlag_betrag' => array("popis" => "Vors. Betrag", "sirka" => $cells['vorschlag_betrag']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    'rekl_nr' => array("popis" => "ReklNr", "sirka" => $cells['rekl_nr']['sirka'], "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
	    'vorschlag_bemerkung' => array("popis" => "Vors. Bemerkung", "sirka" => $cells['vorschlag_bemerkung']['sirka'], "ram" => 'B', "align" => "L", "radek" => 1, "fill" => 1),
);

$sumZapatiBericht = array(
    'betr'=>0,
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


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist,$vorschlag=0)
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

                // zkraceni na pozadovany pocet znaku
                if(array_key_exists("substring",$cell))
			$cellobsah = substr($cellobsah, $cell['substring'][0], $cell['substring'][1]);

		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
		if($vorschlag!=0){
		    $pdfobjekt->SetFillColor(235,235,235,1);
		    $pdfobjekt->SetFont("FreeSans", "I", 8);
		    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],1);
		}
		else
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

    global $cells_header;
    $fill = 1;
    $pdf->SetFillColor(255,255,230);
    $pdf->SetFont("FreeSans", "B", 6.5);

    foreach ($cells_header as $key=>$valArray){
        $pdf->Cell($pole[$key]['sirka'],$headervyskaradku,$cells_header[$key]['popis'],$cells_header[$key]['ram'],$cells_header[$key]['radek'],$cells_header[$key]['align'],$cells_header[$key]['fill']);
    }
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



function zapati_sestava($pdf,$vyskaRadku,$rgb,$pole){

    global $cells;
    $fill = 1;
    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $pdf->Cell($cells['persnr']['sirka']+$cells['name']['sirka']+$cells['datum']['sirka']+$cells['grund']['sirka']+$cells['bemerk']['sirka'],$vyskaRadku,'Gesamtsumme Betrag','0',0,'L',$fill);
    $obsah = number_format($pole['betr'],0,',',' ');
    $pdf->Cell($cells['betr']['sirka'],$vyskaRadku,$obsah,'0',0,'R',$fill);
    $pdf->Cell(0,$vyskaRadku,'','0',1,'R',$fill);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S184 Abmahnung / Vorschlaege", $params);
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
$rows = $domxml->getElementsByTagName('person');
foreach($rows as $row){
    $rowChilds = $row->childNodes;
    test_pageoverflow($pdf, 5, $cells_header);
    $vorschlag = getValueForNode($rowChilds,'vorschlag');
    telo($pdf, $cells, 5, array(255,255,255), '', $rowChilds,$vorschlag);
    foreach ($sumZapatiBericht as $key=>$value){
        $sumZapatiBericht[$key] += floatval(getValueForNode($rowChilds, $key));
    }
}
test_pageoverflow($pdf, 5, $cells_header);

zapati_sestava($pdf, 5, array(240,255,240), $sumZapatiBericht);
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>