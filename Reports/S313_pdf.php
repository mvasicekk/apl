<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';


$doc_title = "S313";
$doc_subject = "S313 Report";
$doc_keywords = "S313";

$apl = AplDB::getInstance();
// necham si vygenerovat XML

$parameters = $_GET;

$auftragsnr_von = $_GET['auftragsnr_von'];
$auftragsnr_bis = $_GET['auftragsnr_bis'];
$teil = $_GET['teil'];
$palvon = $_GET['palvon'];
$palbis = $_GET['palbis'];
$reporttyp = $_GET['reporttyp'];

$bAlle = FALSE;
if($reporttyp=="alles")
    $bAlle = TRUE;
    

$pcip=get_pc_ip();

require_once('S313_xml.php');

$apl = AplDB::getInstance();

$puser = $_SESSION['user'];
$vzkdZeigen = $apl->getDisplaySec('S313', 'vzkd', $puser);

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

$cells =
	array(
	    "teil"
	    => array("popis" => "", "sirka" => 20, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "taetnr"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "datum"
	    => array("popis" => "", "sirka" => 15, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "pal"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
//"schicht" 
//=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

	    "oe"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "persnr"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "name"
	    => array("popis" => "", "sirka" => 25, "ram" => '0', "align" => "L", "radek" => 0, "fill" => 0),
	    "stk"
	    => array("popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "aussstk"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "auss_typ"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzkd"
	    => array("show"=>$vzkdZeigen,"nf" => array(2, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "sumvzkd"
	    => array("show"=>$vzkdZeigen,"nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzaby"
	    => array("nf" => array(2, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "sumvzaby"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => '0', "align" => "R", "radek" => 0, "fill" => 0),
	    "sumverb"
	    => array("popis" => "", "sirka" => 0, "ram" => '0', "align" => "R", "radek" => 1, "fill" => 0),
);

$cells_header =
	array(
	    "teil"
	    => array("popis" => "\n", "sirka" => 20, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
	    "tat"
	    => array("popis" => "\nTaet.", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "datum"
	    => array("popis" => "\nDatum", "sirka" => 15, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
	    "pal"
	    => array("popis" => "\nPal", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
//"schicht" 
//=> array ("popis"=>"\nSch.","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

	    "oe"
	    => array("popis" => "\nOE", "sirka" => 10, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
	    "persnr"
	    => array("popis" => "\nPers", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "name"
	    => array("popis" => "\nName", "sirka" => 25, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
	    "stk"
	    => array("popis" => "\nStk", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "auss_stk"
	    => array("nf" => array(0, ',', ' '), "popis" => "Auss\nStk", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "auss_typ"
	    => array("nf" => array(0, ',', ' '), "popis" => "Auss\nTyp", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "vzkd_stk"
	    => array("show"=>$vzkdZeigen,"nf" => array(2, ',', ' '), "popis" => "VzKd\nStk", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "vzkd"
	    => array("show"=>$vzkdZeigen,"nf" => array(0, ',', ' '), "popis" => "\nVzKd", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "vzaby_stk"
	    => array("nf" => array(2, ',', ' '), "popis" => "VzAby\nStk", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "vzaby"
	    => array("nf" => array(0, ',', ' '), "popis" => "\nVzAby", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
	    "verb"
	    => array("popis" => "\nVerb", "sirka" => 0, "ram" => 'B', "align" => "R", "radek" => 1, "fill" => 1),
);


$sum_zapati_taetigkeit_array = array(
    "stk" => 0,
    "aussstk" => 0,
    "sumvzkd" => 0,
    "sumvzaby" => 0,
    "sumverb" => 0,
);
global $sum_zapati_taetigkeit_array;

$sum_zapati_teil_array = array(
    "stk" => 0,
    "aussstk" => 0,
    "sumvzkd" => 0,
    "sumvzaby" => 0,
    "sumverb" => 0,
);
global $sum_zapati_teil_array;


$sum_zapati_paleta_array = array(
    "stk" => 0,
    "aussstk" => 0,
    "sumvzkd" => 0,
    "sumvzaby" => 0,
    "sumverb" => 0,
);
global $sum_zapati_paleta_array;


$sum_zapati_auftrag_array = array(
    "stk" => 0,
    "aussstk" => 0,
    "sumvzkd" => 0,
    "sumvzaby" => 0,
    "sumverb" => 0,
);
global $sum_zapati_auftrag_array;

$sum_zapati_sestava_array = array(
    "stk" => 0,
    "aussstk" => 0,
    "sumvzkd" => 0,
    "sumvzaby" => 0,
    "sumverb" => 0,
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
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    foreach ($pole as $cell) {
		    $obsah = $cell['popis'];
	    if(array_key_exists('show', $cell)){
		if($cell['show']===TRUE)
		    $obsah = $cell['popis'];
		else{
		    //musim zjistit kolik bylo v puvodnim obsahu znaku \n a tolik jich musim dat i do 
		    //noveho obsahu
		    $lines_arr = preg_split('/\n|\r/',$str);
		    $num_newlines = count($lines_arr);
		    $obsah = "";
		    for($i=0;$i<$num_newlines;$i++){
			$obsah.="\n";
		    }
		}
		    
	    }
	    $pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$obsah,$cell["ram"],$cell["align"],$cell['fill']);

    }
    //if($pdfobjekt->PageNo()==1)
    //{
    $pdfobjekt->Ln();
    $pdfobjekt->Ln();
    //}
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 6);
}

// funkce pro vykresleni tela
function telo($pdfobjekt, $pole, $zahlavivyskaradku, $rgb, $funkce, $nodelist) {
    $pdfobjekt->SetFont("FreeSans", "", 7);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    // pujdu polem pro zahlavi a budu prohledavat predany nodelist
    foreach ($pole as $nodename => $cell) {
	if (array_key_exists("nf", $cell)) {
	    $cellobsah =
		    number_format(getValueForNode($nodelist, $nodename), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
	} else {
	    $cellobsah = getValueForNode($nodelist, $nodename);
	}
	
	if(array_key_exists("show", $cell)){
	    if($cell['show']===FALSE) $cellobsah = '';
	}	

	$pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, $cellobsah, $cell["ram"], $cell["radek"], $cell["align"], $cell["fill"]);
    }
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 7);
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
function zahlavi_auftrag($pdfobjekt, $vyskaradku, $rgb, $cells_header, $auftragsnr) {
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "", 7);

    $pdfobjekt->Cell(0, $vyskaradku, "AuftragsNr: " . $auftragsnr, '1', 1, 'L', $fill);


    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt, $vyskaradku, $rgb, $cells_header, $teilnr, $gew, $brgew, $muster_platz, $muster_vom, $bemerk) {
    global $apl;
    
    	$musterRow = $apl->getTeilDokument($teilnr, AplDB::DOKUNR_MUSTER, TRUE);
	if($musterRow===NULL)
	    $musterText = "Muster: ????";
	else
	    $musterText = "Muster: ".$musterRow['musterplatz'].' Einlager.: '.$musterRow['einlag_datum'];

    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "", 8);

    $pdfobjekt->Cell(30, $vyskaradku, $teilnr, '1', 0, 'L', $fill);

    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(50, $vyskaradku, $musterText, '1', 0, 'L', $fill);


    $pdfobjekt->Cell(30, $vyskaradku, "  Gew: " . $gew . "kg  BrGew. " . $brgew . "kg", '1', 0, 'L', $fill);

    $pdfobjekt->SetFont("FreeSans", "", 5);
    $pdfobjekt->Cell(0, $vyskaradku, $bemerk, '1', 1, 'L', $fill);


    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_taetigkeit($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $tatnr) {
    global $vzkdZeigen;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";

    $pdfobjekt->Cell(100, $vyskaradku, $popis . " " . $tatnr, 'B', 0, 'L', $fill);


    $obsah = $pole['stk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss_stk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //auss_typ
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    //vzkd_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['vzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if(!$vzkdZeigen) $obsah='';
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //vzaby_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['vzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['verb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    $pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_paleta($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $pal) {
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";

    $pdfobjekt->Cell(0, $vyskaradku, "", 'B', 1, 'L', $fill);
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $teilnr) {
    global $vzkdZeigen;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";

    $pdfobjekt->Cell(100, $vyskaradku, $popis . " " . $teilnr, 'B', 0, 'L', $fill);


    $obsah = $pole['stk'];
    $obsah = ""; //number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['aussstk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //auss_typ
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    //vzkd_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['sumvzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if(!$vzkdZeigen) $obsah='';
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //vzaby_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['sumvzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['sumverb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    $pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_auftrag($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $auftragsnr) {
    global $vzkdZeigen;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";

    $pdfobjekt->Cell(100, $vyskaradku, $popis . " " . $auftragsnr, 'B', 0, 'L', $fill);


    $obsah = $pole['stk'];
    $obsah = ""; //number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['aussstk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //auss_typ
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    //vzkd_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['sumvzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if(!$vzkdZeigen) $obsah='';
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //vzaby_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['sumvzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['sumverb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    $pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole) {
    global $vzkdZeigen;

    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";

    $pdfobjekt->Cell(100, $vyskaradku, $popis, 'B', 0, 'L', $fill);


    $obsah = $pole['stk'];
    $obsah = ""; //number_format($obsah,0,',',' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['aussstk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //auss_typ
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    //vzkd_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['sumvzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    if(!$vzkdZeigen) $obsah='';
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //vzaby_stk
    $pdfobjekt->Cell(10, $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['sumvzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['sumverb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(0, $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    //$pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

function test_pageoverflow($pdfobjekt, $vysradku, $cellhead) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	$pdfobjekt->AddPage();
	pageheader($pdfobjekt, $cellhead, $vysradku);
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	return 1;
    }
    else
	return 0;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P', 'mm', 'A4', 1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S313 Leistung Auftrag - Teil - Pal", $params);
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

dbConnect();

// a ted pujdu po zakazkach
$auftraege = $domxml->getElementsByTagName("auftrag");
foreach ($auftraege as $auftrag) {
    $auftragsnr = $auftrag->getElementsByTagName("auftragsnr")->item(0)->nodeValue;

    test_pageoverflow($pdf, 5, $cells_header);
    zahlavi_auftrag($pdf, 5, array(255, 255, 200), $cells_header, $auftragsnr);
    nuluj_sumy_pole($sum_zapati_auftrag_array);

    $teile = $auftrag->getElementsByTagName("teil");

    foreach ($teile as $teil) {
	$teilnr = $teil->getElementsByTagName("teilnr")->item(0)->nodeValue;
	$gew = $teil->getElementsByTagName("gew")->item(0)->nodeValue;
	$brgew = $teil->getElementsByTagName("brgew")->item(0)->nodeValue;
	$f_muster_platz = $teil->getElementsByTagName("f_muster_platz")->item(0)->nodeValue;
	$f_muster_vom = $teil->getElementsByTagName("f_muster_vom")->item(0)->nodeValue;

	if (test_pageoverflow($pdf, 5, $cells_header))
	    zahlavi_auftrag($pdf, 5, array(255, 255, 200), $cells_header, $auftragsnr);
	zahlavi_teil($pdf, 5, array(235, 235, 235), $cells_header, $teilnr, $gew, $brgew, $f_muster_platz, $f_muster_vom, get_teil_bemerk($teilnr, 85));

	nuluj_sumy_pole($sum_zapati_teil_array);

	$paletten = $teil->getElementsByTagName('palette');
	// ted prochazim palety
	foreach ($paletten as $paleta) {
	    $taetigkeiten = $paleta->getElementsByTagName("taetigkeit");
	    nuluj_sumy_pole($sum_zapati_paleta_array);
	    //ted prochazim cinnosti
	    foreach ($taetigkeiten as $taetigkeit) {
		$positionen = $taetigkeit->getElementsByTagName("position");
		nuluj_sumy_pole($sum_zapati_taetigkeit_array);
		// a nakonec jdu po jednotlivych pozicich

		foreach ($positionen as $position) {
		    $position_childs = $position->childNodes;
		    if (test_pageoverflow($pdf, 4, $cells_header))
			zahlavi_auftrag($pdf, 5, array(255, 255, 200), $cells_header, $auftragsnr);

		    telo($pdf, $cells, 4, array(255, 255, 255), "", $position_childs);

		    // projedu pole a aktualizuju sumy pro zapati taetigkeit
		    foreach ($sum_zapati_taetigkeit_array as $key => $prvek) {
			$hodnota = $position->getElementsByTagName($key)->item(0)->nodeValue;
			$sum_zapati_taetigkeit_array[$key]+=$hodnota;
		    }
		}
		//zapati_taetigkeit($pdf,$taetigkeit,5,"Summe Taetigkeit",array(235,235,235),$sum_zapati_taetigkeit_array,$tatnr);
		//sumy pro zapati palety
		makeSumZapati($sum_zapati_paleta_array, $sum_zapati_taetigkeit_array);
	    }

	    //zapati pro paletu
	    zapati_paleta($pdf, $node, 1, "", array(235, 235, 235), $sum_zapati_paleta_array, 0);
	    // sumy pro zapati teil
	    makeSumZapati($sum_zapati_teil_array, $sum_zapati_paleta_array);
	}


	if (test_pageoverflow($pdf, 5, $cells_header))
	    zahlavi_auftrag($pdf, 5, array(255, 255, 200), $cells_header, $auftragsnr);

	zapati_teil($pdf, $pers, 5, "Summe Teil", array(235, 235, 235), $sum_zapati_teil_array, $teilnr);

	makeSumZapati($sum_zapati_auftrag_array, $sum_zapati_teil_array);
    }


    if (test_pageoverflow($pdf, 5, $cells_header))
	zahlavi_auftrag($pdf, 5, array(255, 255, 200), $cells_header, $auftragsnr);

    zapati_auftrag($pdf, $pers, 5, "Summe Auftrag", array(235, 235, 235), $sum_zapati_auftrag_array, $auftragsnr);

    makeSumZapati($sum_zapati_sestava_array, $sum_zapati_auftrag_array);

    // po auftragu odstrankuju
    $pdf->AddPage();
    pageheader($pdf, $cells_header, 5);
}


if (test_pageoverflow($pdf, 5, $cells_header))
    zahlavi_auftrag($pdf, 5, array(255, 255, 200), $cells_header, $auftragsnr);

zapati_sestava($pdf, $import, 5, "Summe Bericht", array(200, 200, 255), $sum_zapati_sestava_array);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>
