<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S820";
$doc_subject = "S820 Report";
$doc_keywords = "S820";

// necham si vygenerovat XML

$parameters=$_GET;

$datevon=make_DB_datum($_GET['datevon']);
$datebis=make_DB_datum($_GET['datebis']);
$kundevon=$_GET['kundevon'];
$kundebis=$_GET['kundebis'];
$password = $_GET['password'];
$reporttyp = $_GET['reporttyp'];


$user = $_SESSION['user'];

// nacteni pole s rolema
if(!testReportPassword("S820",$password,$user,1))
	echo "password ??, etwas stimmt nicht !";
else
{

$apl = AplDB::getInstance();

require_once('S820_xml.php');


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

$minWidth = 8.6;
    $cells = array(
		"teilnr"
		=> array("popis" => "", "sirka" => 12, "ram" => 'LTB', "align" => "L", "radek" => 0, "fill" => 0),
		"teilbez"
		=> array("popis" => "", "sirka" => 28, "ram" => 'TB', "align" => "L", "radek" => 0, "fill" => 0),
		"gew"
		=> array("nf" => array(1, ',', ' '), "popis" => "", "sirka" => 10, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"g_imp_stk"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 10, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"pt_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"pt_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"pt_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"st_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"st_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"st_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"st1_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"st1_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"st1_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"e_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"e_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"e_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"g_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"g_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"g_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"g1_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"g1_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"g1_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"sonst_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"sonst_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"sonst_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"kdmin_zu_verb"
		=> array("nf" => array(2, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TB', "align" => "R", "radek" => 0, "fill" => 0),
		"abymin_zu_verb"
		=> array("nf" => array(2, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"sum_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => $minWidth, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"waehr_pro_tonne"
		=> array("nf" => array(1, ',', ' '), "popis" => "", "sirka" => 8, "ram" => 'TBR', "align" => "R", "radek" => 0, "fill" => 0),
		"mustervom"
		=> array("popis" => "", "sirka" => 0, "ram" => '1', "align" => "L", "radek" => 1, "fill" => 0),
    );

    $cells_header = array(
		"teilnr"
		=> array("popis" => "\nteil", "sirka" => 12, "ram" => 'LB', "align" => "L", "radek" => 0, "fill" => 1),
		"teilbez"
		=> array("popis" => "\nteilbez", "sirka" => 28, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 1),
		"gew"
		=> array("nf" => array(1, ',', ' '), "popis" => "\ngew", "sirka" => 10, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"g_imp_stk"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nstk", "sirka" => 10, "ram" => 'LB', "align" => "R", "radek" => 0, "fill" => 1),
		"pt_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"pt_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"pt_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"st_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"st_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"st_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"st1_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"st1_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"st1_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"e_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"e_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"e_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"g_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"g_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"g_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"g1_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"g1_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"g1_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"sonst_kdmin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzkd", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"sonst_abymin"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nvzaby", "sirka" => $minWidth, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 1),
		"sonst_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"kdmin_zu_verb"
		=> array("nf" => array(2, ',', ' '), "popis" => "vzkd\nverb", "sirka" => $minWidth, "ram" => 'LB', "align" => "R", "radek" => 0, "fill" => 1),
		"abymin_zu_verb"
		=> array("nf" => array(2, ',', ' '), "popis" => "vzaby\nverb", "sirka" => $minWidth, "ram" => 'BR', "align" => "R", "radek" => 0, "fill" => 1),
		"sum_verb"
		=> array("nf" => array(0, ',', ' '), "popis" => "ges.\nverb", "sirka" => $minWidth, "ram" => 'LBR', "align" => "R", "radek" => 0, "fill" => 1),
		"waehr_pro_tonne"
		=> array("nf" => array(1, ',', ' '), "popis" => "EUR\ntonne", "sirka" => 8, "ram" => 'LBR', "align" => "R", "radek" => 0, "fill" => 1),
		"mustervom"
		=> array("popis" => "muster\nvom", "sirka" => 0, "ram" => '1', "align" => "L", "radek" => 1, "fill" => 1),
    );
    
    $sum_zapati_kunde_array = array(
	"pt_kdmin" => 0,
	"pt_abymin" => 0,
	"pt_verb" => 0,
	"st_kdmin" => 0,
	"st_abymin" => 0,
	"st_verb" => 0,
	"st1_kdmin" => 0,
	"st1_abymin" => 0,
	"st1_verb" => 0,
	"e_kdmin" => 0,
	"e_abymin" => 0,
	"e_verb" => 0,
	"g_kdmin" => 0,
	"g_abymin" => 0,
	"g_verb" => 0,
	"g1_kdmin" => 0,
	"g1_abymin" => 0,
	"g1_verb" => 0,
	"sonst_kdmin" => 0,
	"sonst_abymin" => 0,
	"sonst_verb" => 0,
	"sum_kdmin" => 0,
	"sum_abymin" => 0,
	"sum_verb" => 0,
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
    function pageheader($pdfobjekt, $pole, $headervyskaradku, $popisek) {
	global $minWidth;
	$pdfobjekt->SetFillColor(255, 255, 200, 1);
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->MyMultiCell(12 + 28 + 10, $headervyskaradku, "\n", 'LT', 'L', 1);
	$pdfobjekt->MyMultiCell(10 + $minWidth + $minWidth + $minWidth, $headervyskaradku, "Trennen+Putzen\nS0011", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell($minWidth + $minWidth + $minWidth, $headervyskaradku, "Strahlen\nS0041", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell($minWidth + $minWidth + $minWidth, $headervyskaradku, "Strahlen\nS0043", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell($minWidth + $minWidth + $minWidth, $headervyskaradku, "Kontrolle\nS0051", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell($minWidth + $minWidth + $minWidth, $headervyskaradku, "Farbe\nS0061", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell($minWidth + $minWidth + $minWidth, $headervyskaradku, "Farbe\nS0062", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell($minWidth + $minWidth + $minWidth, $headervyskaradku, "Sonstiges\nS00XX", 'LTR', 'C', 1);
	$pdfobjekt->MyMultiCell(0, $headervyskaradku, "\n", 'LTR', 'C', 1);
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();

	$pdfobjekt->SetFont("FreeSans", "", 6);

	foreach ($pole as $cell) {
	    $pdfobjekt->MyMultiCell($cell["sirka"], $headervyskaradku, $cell['popis'], $cell["ram"], $cell["align"], $cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 6);
    }

// funkce pro vykresleni tela
    function telo($pdfobjekt, $pole, $zahlavivyskaradku, $rgb, $funkce, $nodelist) {
	global $minWidth;
	global $apl;
	$pdfobjekt->SetFont("FreeSans", "", 5.6);
	$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach ($pole as $nodename => $cell) {
	    if (array_key_exists("nf", $cell)) {
		$cellobsah = number_format(getValueForNode($nodelist, $nodename), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
	    } else {
		$cellobsah = getValueForNode($nodelist, $nodename);
	    }

	    if ($nodename == "mustervom") {
		$teilnr = getValueForNode($nodelist, "teilnr");
		$musterRow = $apl->getTeilDokument($teilnr, AplDB::DOKUNR_MUSTER, TRUE);
		if ($musterRow === NULL) {
		    $cellobsah = "";
		} else {
		    $cellobsah = $musterRow['einlag_datum'];
		}
	    }

	    $pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, $cellobsah, $cell["ram"], $cell["radek"], $cell["align"], $cell["fill"]);
	}
	$pdfobjekt->SetFont("FreeSans", "", 7);
    }

// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
// zaokrouhlovani jde nahoru

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

/**
 * 
 * @param type $pdfobjekt
 * @param type $childnodes
 * @param type $vyskaradku
 */
function zahlavi_kunde($pdfobjekt, $childnodes, $vyskaradku) {
    global $minWidth;
	$fill = 0;
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$kundePopis = getValueForNode($childnodes, 'kundenr') . " " . getValueForNode($childnodes, 'name1');
	$pdfobjekt->Cell(0, $vyskaradku, $kundePopis, '0', 1, 'L', $fill);
    }

/**
 * 
 * @param type $pdfobjekt
 * @param type $vyskaradku
 * @param type $rgb
 * @param type $childnodes
 * @param type $sum_zapati_array
 * @param type $positionen
 */
function zapati_kunde($pdfobjekt, $vyskaradku, $rgb, $childnodes, $sum_zapati_array, $positionen) {
    global $minWidth;
	$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
	$fill = 1;
	$pdfobjekt->SetFont("FreeSans", "B", 5.6);
	// dummy
	$obsah = "";
	$kundePopis = getValueForNode($childnodes, 'kundenr');
	$pdfobjekt->Cell(12 + 28 + 10 + 10, $vyskaradku, "Summe Kunde: " . $kundePopis . "  ( Anz. Positionen: $positionen )", 'LTB', 0, 'L', $fill);

	$obsah = $sum_zapati_array['pt_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['pt_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['pt_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['st_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['st_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['st_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['st1_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['st1_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['st1_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['e_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['e_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['e_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['g_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['g_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['g_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['g1_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['g1_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['g1_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['sonst_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['sonst_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['sonst_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['sum_kdmin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	$obsah = $sum_zapati_array['sum_abymin'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = $sum_zapati_array['sum_verb'];
	$obsah = number_format($obsah, 0, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTBR', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell(0, $vyskaradku, $obsah, 'TBR', 1, 'R', $fill);
    }

/**
 * 
 * @param type $pdfobjekt
 * @param type $vyskaradku
 * @param type $rgb
 * @param type $childnodes
 * @param type $sum_zapati_array
 */
function zapati_kunde_faktory($pdfobjekt, $vyskaradku, $rgb, $childnodes, $sum_zapati_array) {
    global $minWidth;
	$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
	$fill = 1;
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	// dummy
	$obsah = "";
	$pdfobjekt->Cell(12 + 28 + 10 + 10, $vyskaradku, "Faktoren", 'LTB', 0, 'L', $fill);

	if ($sum_zapati_array['pt_verb'] != 0)
	    $obsah = $sum_zapati_array['pt_kdmin'] / $sum_zapati_array['pt_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);


	if ($sum_zapati_array['pt_verb'] != 0)
	    $obsah = $sum_zapati_array['pt_abymin'] / $sum_zapati_array['pt_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['st_verb'] != 0)
	    $obsah = $sum_zapati_array['st_kdmin'] / $sum_zapati_array['st_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['st_verb'] != 0)
	    $obsah = $sum_zapati_array['st_abymin'] / $sum_zapati_array['st_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['st1_verb'] != 0)
	    $obsah = $sum_zapati_array['st1_kdmin'] / $sum_zapati_array['st1_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['st1_verb'] != 0)
	    $obsah = $sum_zapati_array['st1_abymin'] / $sum_zapati_array['st1_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['e_verb'] != 0)
	    $obsah = $sum_zapati_array['e_kdmin'] / $sum_zapati_array['e_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['e_verb'] != 0)
	    $obsah = $sum_zapati_array['e_abymin'] / $sum_zapati_array['e_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['g_verb'] != 0)
	    $obsah = $sum_zapati_array['g_kdmin'] / $sum_zapati_array['g_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['g_verb'] != 0)
	    $obsah = $sum_zapati_array['g_abymin'] / $sum_zapati_array['g_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['g1_verb'] != 0)
	    $obsah = $sum_zapati_array['g1_kdmin'] / $sum_zapati_array['g1_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['g1_verb'] != 0)
	    $obsah = $sum_zapati_array['g1_abymin'] / $sum_zapati_array['g1_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['sonst_verb'] != 0)
	    $obsah = $sum_zapati_array['sonst_kdmin'] / $sum_zapati_array['sonst_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['sonst_verb'] != 0)
	    $obsah = $sum_zapati_array['sonst_abymin'] / $sum_zapati_array['sonst_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TB', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	if ($sum_zapati_array['sum_verb'] != 0)
	    $obsah = $sum_zapati_array['sum_kdmin'] / $sum_zapati_array['sum_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTB', 0, 'R', $fill);

	if ($sum_zapati_array['sum_verb'] != 0)
	    $obsah = $sum_zapati_array['sum_abymin'] / $sum_zapati_array['sum_verb'];
	else
	    $obsah = 0;
	$obsah = number_format($obsah, 2, ',', ' ');
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'TBR', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell($minWidth, $vyskaradku, $obsah, 'LTBR', 0, 'R', $fill);

	$obsah = "";
	$pdfobjekt->Cell(0, $vyskaradku, $obsah, 'TBR', 1, 'R', $fill);
    }

 /**
  * 
  * @param type $pdfobjekt
  * @param type $vysradku
  * @param type $cellhead
  * @param type $datumnr
  */   
 function test_pageoverflow($pdfobjekt, $vysradku, $cellhead, $datumnr) {
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	    $pdfobjekt->AddPage();
	    pageheader($pdfobjekt, $cellhead, $vysradku);
	}
    }

    
    /**
     * 
     * @param type $pdfobjekt
     * @param type $vysradku
     * @return int
     */
    function test_pageoverflow_noheader($pdfobjekt, $vysradku) {
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	    $pdfobjekt->AddPage();
	    return 1;
	} else
	    return 0;
    }

    require_once('../tcpdf/config/lang/eng.php');
    require_once('../tcpdf/tcpdf.php');

    $pdf = new TCPDF('L', 'mm', 'A4', 1);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(PDF_AUTHOR);
    $pdf->SetTitle($doc_title);
    $pdf->SetSubject($doc_subject);
    $pdf->SetKeywords($doc_keywords);

    if ($reporttyp == 'sort VerbZeit')
	$popis = "S820 Teilstat-Sort-Verb-Summe";
    else
	$popis = "S820 Teilstat-Sort-TeilNr";

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $popis, $params);
//set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT - 10, PDF_MARGIN_TOP - 10, PDF_MARGIN_RIGHT - 10);
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

// zacinam po zakaznicich
    $kunden = $domxml->getElementsByTagName("kunde");
    foreach ($kunden as $kunde) {

	$kundeChildNodes = $kunde->childNodes;
	$teile = $kunde->getElementsByTagName("teil");
	$pdf->AddPage();
	zahlavi_kunde($pdf, $kundeChildNodes, 5);
	pageheader($pdf, $cells_header, 2.5, "");

	nuluj_sumy_pole($sum_zapati_kunde_array);
	$positionen = 0;
	foreach ($teile as $teil) {
	    $teilChildNodes = $teil->childNodes;
	    if (test_pageoverflow_noheader($pdf, 10)) {
		zahlavi_kunde($pdf, $kundeChildNodes, 5);
		pageheader($pdf, $cells_header, 2.5, "");
	    }
	    telo($pdf, $cells, 5, array(255, 255, 255), "", $teilChildNodes);
	    $positionen++;
	    // projedu pole a aktualizuju sumy pro zapati kunde
	    foreach ($sum_zapati_kunde_array as $key => $prvek) {
		$hodnota = getValueForNode($teilChildNodes, $key);
		$sum_zapati_kunde_array[$key] += $hodnota;
	    }
	}

	if (test_pageoverflow_noheader($pdf, 20)) {
	    zahlavi_kunde($pdf, $kundeChildNodes, 5);
	    pageheader($pdf, $cells_header, 2.5, "");
	}

	zapati_kunde($pdf, 5, array(235, 235, 235), $kundeChildNodes, $sum_zapati_kunde_array, $positionen);
	zapati_kunde_faktory($pdf, 5, array(235, 235, 235), $kundeChildNodes, $sum_zapati_kunde_array);
    }

//Close and output PDF document
    $pdf->Output();
}
