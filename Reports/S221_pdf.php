<?php

require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S221";
$doc_subject = "S221 Report";
$doc_keywords = "S221";

$apl = AplDB::getInstance();
// necham si vygenerovat XML

$parameters = $_GET;

$export = $_GET['export'];

$puser = $_SESSION['user'];
$vzkdZeigen = $apl->getDisplaySec('S221', 'vzkd', $puser);
$preisZeigen = $apl->getDisplaySec('S221', 'preis', $puser);

require_once('S221_xml.php');


// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters = $domxml->getElementsByTagName("parameters");


foreach ($parameters as $param) {
    $parametry = $param->childNodes;
    // v ramci parametru si prectu label a hodnotu
    foreach ($parametry as $parametr) {
	$parametr = $parametr->childNodes;
	foreach ($parametr as $par) {
	    if ($par->nodeName == "label")
		$label = $par->nodeValue;
	    if ($par->nodeName == "value")
		$value = $par->nodeValue;
	}
	$params .= $label . ": " . $value . "  ";
    }
}


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells = array(
	    "pal"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "teil"
	    => array("popis" => "", "sirka" => 20, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "auftragsnr"
	    => array("popis" => "", "sirka" => 15, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "tat"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "stk"
	    => array("popis" => "", "sirka" => 15, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss2"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss4"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss6"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzkd"
	    => array("nf" => array(0, ',', ' '), "show"=>$vzkdZeigen,"popis" => "", "sirka" => 15, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzaby"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 15, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "verb"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 15, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "preis"
	    => array("nf" => array(4, ',', ' '), "show"=>$preisZeigen,"popis" => "", "sirka" => 15, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "fac1"
	    => array("nf" => array(2, ',', ' '), "popis" => "", "sirka" => 0, "ram" => '0', "align" => "R", "radek" => 1, "fill" => 0)
);

$vzkdPopis = $vzkdZeigen?"\nvzkd":"\n";
$preisPopis = $preisZeigen?"\npreis":"\n";
$fac1Popis = $vzkdZeigen?"VzKd/\nVerb":"VzAby/\nVerb";

$cells_header = array(
	    "pal"
	    => array("popis" => "\nPal", "sirka" => 10, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
	    "teil"
	    => array("popis" => "\nTeil", "sirka" => 20, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
	    "auftragsnr"
	    => array("popis" => "\nImport", "sirka" => 15, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
	    "tat"
	    => array("popis" => "\nTat.", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "stk"
	    => array("popis" => "\nStk", "sirka" => 15, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss2"
	    => array("popis" => "\n(2)", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss4"
	    => array("nf" => array(0, ',', ' '), "popis" => "\n(4)", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss6"
	    => array("nf" => array(0, ',', ' '), "popis" => "\n(6)", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzkd"
	    => array("nf" => array(0, ',', ' '), "popis" => $vzkdPopis, "sirka" => 15, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzaby"
	    => array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => 15, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "verb"
	    => array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => 15, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "preis"
	    => array("nf" => array(4, ',', ' '), "popis" => $preisPopis, "sirka" => 15, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    "fac1"
	    => array("nf" => array(2, ',', ' '), "popis" => $fac1Popis, "sirka" => 0, "ram" => 'B', "align" => "R", "radek" => 1, "fill" => 0)
);


$sum_zapati_auftrag_array = array(
    "auss2" => 0,
    "auss4" => 0,
    "auss6" => 0,
    "vzkd" => 0,
    "vzaby" => 0,
    "verb" => 0,
);
global $sum_zapati_auftrag_array;

$sum_zapati_teil_array = array(
    "auss2" => 0,
    "auss4" => 0,
    "auss6" => 0,
    "vzkd" => 0,
    "vzaby" => 0,
    "verb" => 0,
);
global $sum_zapati_teil_array;

$sum_zapati_pal_array = array(
    "auss2" => 0,
    "auss4" => 0,
    "auss6" => 0,
    "vzkd" => 0,
    "vzaby" => 0,
    "verb" => 0,
);
global $sum_zapati_pal_array;

$sum_zapati_sestava_array = array(
    "auss2" => 0,
    "auss4" => 0,
    "auss6" => 0,
    "vzkd" => 0,
    "vzaby" => 0,
    "verb" => 0,
);
global $sum_zapati_sestava_array;

/////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce k vynulovani pole se sumama
// jako parametr predam asociativni pole
function nuluj_sumy_pole(&$pole) {
    foreach ($pole as $key => $prvek) {
	$pole[$key] = 0;
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt, $pole, $headervyskaradku) {
    $pdfobjekt->SetFont("FreeSans", "", 8);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    foreach ($pole as $cell) {
	$pdfobjekt->MyMultiCell($cell["sirka"], $headervyskaradku, $cell['popis'], $cell["ram"], $cell["align"], $cell['fill']);
    }
    $pdfobjekt->Ln();
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 8);
}

// funkce pro vykresleni tela
function telo($pdfobjekt, $pole, $zahlavivyskaradku, $rgb, $funkce, $nodelist) {
    $pdfobjekt->SetFont("FreeSans", "", 8);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    // pujdu polem pro zahlavi a budu prohledavat predany nodelist
    foreach ($pole as $nodename => $cell) {
	if (array_key_exists("nf", $cell)) {
	    $cellobsah = number_format(getValueForNode($nodelist, $nodename), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
	} else {
	    $cellobsah = getValueForNode($nodelist, $nodename);
	}
	if(array_key_exists("show", $cell)){
	    if($cell['show']===FALSE) $cellobsah = '';
	}
	$pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, $cellobsah, $cell["ram"], $cell["radek"], $cell["align"], $cell["fill"]);
    }
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 8);
}

// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
function getValueForNode($nodelist, $nodename) {
    $nodevalue = "";
    foreach ($nodelist as $node) {
	if ($node->nodeName == $nodename) {
	    $nodevalue = $node->nodeValue;
	    return $nodevalue;
	}
    }
    return $nodevalue;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_schicht($pdfobjekt, $vyskaradku, $rgb, $schicht, $schichtfuehrer, $cells) {
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->Cell(0, $vyskaradku, $schicht . " " . $schichtfuehrer, '0', 1, 'L', $fill);
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_auftrag($pdfobjekt, $vyskaradku, $rgb, $auftragsnr, $cells) {
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(20, $vyskaradku, $obsah, '0', 0, 'R', $fill);

    $pdfobjekt->Cell(0, $vyskaradku, "IM: " . $auftragsnr, '0', 1, 'L', $fill);
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_palette($pdfobjekt, $vyskaradku, $rgb, $palnr, $teilnr, $auftragsnr, $cells) {
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');10,20,15
    $pdfobjekt->Cell(10, $vyskaradku, $palnr, '0', 0, 'L', $fill);

    $pdfobjekt->Cell(20, $vyskaradku, $teilnr, '0', 0, 'L', $fill);

    $pdfobjekt->Cell(30, $vyskaradku, "IM: " . $auftragsnr, '0', 0, 'L', $fill);

    $pdfobjekt->Cell(0, $vyskaradku, "", '0', 1, 'L', $fill);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt, $vyskaradku, $rgb, $teilnr, $cells) {
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->Cell(0, $vyskaradku, $teilnr, 'T', 1, 'L', $fill);
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $fac1) {
    global $vzkdZeigen;
    global $preisZeigen;

    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    //$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

    $pdfobjekt->Cell(20 + 25 + 10 + 15, $vyskaradku, $popis, 'B', 0, 'L', $fill);

    $obsah = $pole['auss2'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss4'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss6'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['vzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if (!$vzkdZeigen)
	$obsah = '';
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['vzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['verb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $fac1;
    $obsah = number_format($obsah, 2, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    $pdfobjekt->Ln();
    $pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_pal($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $fac1) {
    global $vzkdZeigen;
    global $preisZeigen;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    //$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

    $pdfobjekt->Cell(10 + 20 + 15 + 10 + 15, $vyskaradku, $popis, 'B', 0, 'L', $fill);

    $obsah = $pole['auss2'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss4'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss6'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['vzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if (!$vzkdZeigen)
	$obsah = '';
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['vzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['verb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $fac1;
    $obsah = number_format($obsah, 2, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    //$pdfobjekt->Ln();
    //$pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_auftrag($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $fac1) {
    global $vzkdZeigen;
    global $preisZeigen;

    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    //$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

    $pdfobjekt->Cell(20 + 25 + 10 + 15, $vyskaradku, $popis, 'B', 0, 'L', $fill);

    $obsah = $pole['auss2'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss4'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss6'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['vzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if (!$vzkdZeigen)
	$obsah = '';
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['vzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['verb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(15, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $fac1;
    $obsah = number_format($obsah, 2, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    //$pdfobjekt->Ln();
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_drueck($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $minpreis, $betrag) {
    global $preisZeigen;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    if ($preisZeigen === TRUE) {
	// dummy
	$obsah = "";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(20 + 25 + 10 + 15, $vyskaradku, $popis, 'LTB', 0, 'L', $fill);

	$betrag = number_format($betrag, 2, ',', ' ');
	$pdfobjekt->Cell(25, $vyskaradku, $betrag . " EUR", 'RTB', 0, 'R', $fill);

	$obsah = $minpreis;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell(0, $vyskaradku, "EUR/min " . $obsah, '0', 1, 'R', $fill);
    }


    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_dauftr($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $betrag) {
    global $preisZeigen;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    if ($preisZeigen === TRUE) {
	// dummy
	$obsah = "";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(20 + 25 + 10 + 15, $vyskaradku, $popis, 'LTB', 0, 'L', $fill);

	$betrag = number_format($betrag, 2, ',', ' ');
	$pdfobjekt->Cell(25, $vyskaradku, $betrag . " EUR", 'RTB', 1, 'R', $fill);
    }

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

function test_pageoverflow($pdfobjekt, $vysradku, $cellhead) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	$pdfobjekt->AddPage();
	pageheader($pdfobjekt, $cellhead, 3.5);
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
    }
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', 'mm', 'A4', 1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S221 Leistung Auftrag Export", $params);
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
pageheader($pdf, $cells_header, 5);
$pdf->Ln();
$pdf->Ln();


// a ted pujdu po importech
$importe = $domxml->getElementsByTagName("import");
foreach ($importe as $import) {
    $auftragsnr = $import->getElementsByTagName("auftragsnr")->item(0)->nodeValue;

    //test_pageoverflow($pdf,5,$cells_header);
    //zahlavi_teil($pdf,5,array(255,255,200),$teilnr,$cells_header);
    nuluj_sumy_pole($sum_zapati_auftrag_array);

    $teile = $import->getElementsByTagName("teil");

    foreach ($teile as $teil) {
	//$import_childs=$importnode->childNodes;
	$teilnr = $teil->getElementsByTagName("teilnr")->item(0)->nodeValue;

	//test_pageoverflow($pdf,5,$cells_header);
	//zahlavi_auftrag($pdf,5,array(255,255,255),$auftragsnr,$cells_header);
	nuluj_sumy_pole($sum_zapati_teil_array);

	$paletten = $teil->getElementsByTagName("palette");

	foreach ($paletten as $palette) {
	    $palnr = $palette->getElementsByTagName("pal")->item(0)->nodeValue;

	    test_pageoverflow($pdf, 5, $cells_header);
	    zahlavi_palette($pdf, 5, array(255, 255, 255), $palnr, $teilnr, $auftragsnr, $cells_header);
	    nuluj_sumy_pole($sum_zapati_pal_array);

	    $taetigkeiten = $palette->getElementsByTagName("taetigkeit");

	    foreach ($taetigkeiten as $taetigkeit) {
		$taetigkeit_childs = $taetigkeit->childNodes;
		test_pageoverflow($pdf, 5, $cells_header);
		telo($pdf, $cells, 5, array(255, 255, 255), "", $taetigkeit_childs);
		// projedu pole a aktualizuju sumy pro zapati schicht
		foreach ($sum_zapati_pal_array as $key => $prvek) {
		    $hodnota = $taetigkeit->getElementsByTagName($key)->item(0)->nodeValue;
		    $sum_zapati_pal_array[$key]+=$hodnota;
		}
	    }

	    test_pageoverflow($pdf, 5, $cells_header);
	    if ($sum_zapati_pal_array['verb'] != 0) {
		if ($vzkdZeigen === TRUE) {
		    $fac1 = $sum_zapati_pal_array['vzkd'] / $sum_zapati_pal_array['verb'];
		} else {
		    $fac1 = $sum_zapati_pal_array['vzaby'] / $sum_zapati_pal_array['verb'];
		}
	    } else
		$fac1 = 0;

	    zapati_pal($pdf, $teilnode, 5, "Summe Pal", array(235, 235, 235), $sum_zapati_pal_array, $fac1);

	    // sumy pro zapati teil
	    foreach ($sum_zapati_teil_array as $key => $prvek) {
		$hodnota = $sum_zapati_pal_array[$key];
		$sum_zapati_teil_array[$key]+=$hodnota;
	    }
	}
	// zobrazim zapati teil
	test_pageoverflow($pdf, 5, $cells_header);
	if ($sum_zapati_teil_array['verb'] != 0) {
	    if ($vzkdZeigen === TRUE) {
		$fac1 = $sum_zapati_teil_array['vzkd'] / $sum_zapati_teil_array['verb'];
	    } else {
		$fac1 = $sum_zapati_teil_array['vzaby'] / $sum_zapati_teil_array['verb'];
	    }
	} else
	    $fac1 = 0;
	//zapati_pal($pdf,$teilnode,5,"Summe Teil",array(235,235,235),$sum_zapati_teil_array,$fac1);
	// sumy pro zapati import
	foreach ($sum_zapati_auftrag_array as $key => $prvek) {
	    $hodnota = $sum_zapati_teil_array[$key];
	    $sum_zapati_auftrag_array[$key]+=$hodnota;
	}
    }

    // zobrazim zapati import
    test_pageoverflow($pdf, 5, $cells_header);
    if ($sum_zapati_auftrag_array['verb'] != 0) {
	if ($vzkdZeigen === TRUE) {
	    $fac1 = $sum_zapati_auftrag_array['vzkd'] / $sum_zapati_auftrag_array['verb'];
	} else {
	    $fac1 = $sum_zapati_auftrag_array['vzaby'] / $sum_zapati_auftrag_array['verb'];
	}
    } else
	$fac1 = 0;
    //zapati_pal($pdf,$teilnode,5,"Summe IM",array(235,235,235),$sum_zapati_auftrag_array,$fac1);
    // sumy pro zapati sestavy
    foreach ($sum_zapati_sestava_array as $key => $prvek) {
	$hodnota = $sum_zapati_auftrag_array[$key];
	$sum_zapati_sestava_array[$key]+=$hodnota;
    }
}

if ($sum_zapati_sestava_array['verb'] != 0) {
    if ($vzkdZeigen === TRUE) {
	$fac1 = $sum_zapati_sestava_array['vzkd'] / $sum_zapati_sestava_array['verb'];
    } else {
	$fac1 = $sum_zapati_sestava_array['vzaby'] / $sum_zapati_sestava_array['verb'];
    }
} else
    $fac1 = 0;

test_pageoverflow($pdf, 15, $cells_header);
$pdf->Ln();
zapati_pal($pdf, $import, 5, "Summe Bericht", array(200, 200, 255), $sum_zapati_sestava_array, $fac1);
$pdf->Ln();



// cena podle druecku
$exports = $domxml->getElementsByTagName("exporte")->item(0);
$minpreis = $exports->getElementsByTagName("minpreis")->item(0)->nodeValue;

zapati_drueck($pdf, $teilnode, 5, "RE-SUM (VzKd)", array(255, 255, 255), $minpreis, $minpreis * $sum_zapati_sestava_array['vzkd']);

// cena podle auftragu
dbConnect();
$betrag = get_preis_laut_auftrag($export);
zapati_dauftr($pdf, $teilnode, 5, "RE-SUM (lt.Auftrag)", array(200, 255, 200), $betrag);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
