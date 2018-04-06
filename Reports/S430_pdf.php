<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S430";
$doc_subject = "S430 Report";
$doc_keywords = "S430";

// necham si vygenerovat XML

// u checkboxu, pokud neni zaskrtnutej tak se vubec neprenasi

$parameters=$_GET;

$kunde1=$_GET['kunde'];
$heslo=$_GET['password'];
$teil=$_GET['teil'];
$user = $_SESSION['user'];
$typ = $_GET['typ'];
$abgnr = $_GET['abgnr'];
$preise = $_GET['preise'];
$bMitMuster = $_GET['mitmuster']=='checked'?TRUE:FALSE;

if($_GET['jb']=='ja'){
    $jb=TRUE;
}
else{
    $jb=FALSE;
}

if($_GET['alt']=='ja'){
    $alt=TRUE;
}
else{
    $alt=FALSE;
}


if($typ=='STANDARD') $zp = FALSE;
else $zp = TRUE;

$teil = strtr($teil, '*', '%');

$teil1 = $teil;

$fullAccess = testReportPassword("S430",$heslo,$user,1);


if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}

if ($typ == 'PREIS/STK') {
    $nurKopfZielpreis = TRUE;
} else {
    $nurKopfZielpreis = FALSE;
}

require_once('S430_xml.php');
//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");


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
        if (strtolower($label) != "password"){
            $params .= $label . ": " . $value . ";  ";
        }
    }
}

$paramsArray = split(";", $params);
//var_dump($paramsArray);

$params = join("; ", array_slice($paramsArray,0,7));
//echo "bMitMuster:$bMitMuster";
//echo "<br>params:$params";
$a = AplDB::getInstance();
$kia = $a->getKundeInfoArray($kunde1);
$wahr = $kia[0]['waehrkz'];

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2


$cells =
	array(
	    "abgnr"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 20, "ram" => '1', "align" => "R", "radek" => 0, "fill" => 0),
	    "abgnr_name"
	    => array("substring" => array(0, 30), "popis" => "", "sirka" => 40, "ram" => '1', "align" => "L", "radek" => 0, "fill" => 0),
	    "preis"
	    => array("nf" => array(4, ',', ' '), "popis" => "", "sirka" => 25, "ram" => '1', "align" => "R", "radek" => 0, "fill" => 0),
	    "vzkd"
	    => array("nf" => array(4, ',', ' '), "popis" => "", "sirka" => 25, "ram" => '1', "align" => "R", "radek" => 0, "fill" => 0),
	    "bed_lfd_j_preis"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 25, "ram" => '1', "align" => "R", "radek" => 0, "fill" => 0),
	    "bed_lfd_plus_1_preis"
	    => array("nf" => array(0, ',', ' '), "popis" => "", "sirka" => 25, "ram" => '1', "align" => "R", "radek" => 0, "fill" => 0),
	    "ln"
	    => array("popis" => "", "sirka" => 0, "ram" => '0', "align" => "R", "radek" => 1, "fill" => 0)
);



$sum_zapati_teil = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_lfd_1_vzkd'=>0,
    'bed_lfd_j_vzkd'=>0,
    'bed_lfd_plus_1_vzkd',
    'bed_lfd_1_preis'=>0,
    'bed_lfd_j_preis'=>0,
    'bed_lfd_plus_1_preis'=>0,
    'tonnen_lfd_j'=>0,
    'tonnen_lfd_plus_1'=>0,
);

$sum_zapati_sestava = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_lfd_1_vzkd'=>0,
    'bed_lfd_j_vzkd'=>0,
    'bed_lfd_plus_1_vzkd'=>0,
    'zielpreis_lfd_1'=>0,
    'zielpreis_lfd_j'=>0,
    'zielpreis_lfd_plus_1'=>0,
    'kosten_auss_stk'=>0,
    'tonnen_lfd_1'=>0,
    'tonnen_lfd_j'=>0,
    'tonnen_lfd_plus_1'=>0,
    'bed_lfd_1_preis'=>0,
    'bed_lfd_j_preis'=>0,
    'bed_lfd_plus_1_preis'=>0,
);

$sum_zapati_sestava_abgnr = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_lfd_1_vzkd'=>0,
    'bed_lfd_j_vzkd'=>0,
    'bed_lfd_plus_1_vzkd'=>0,
    'zielpreis_lfd_1'=>0,
    'zielpreis_lfd_j'=>0,
    'zielpreis_lfd_plus_1'=>0,
    'tonnen_lfd_1'=>0,
    'tonnen_lfd_j'=>0,
    'tonnen_lfd_plus_1'=>0,
    'bed_lfd_1_preis'=>0,
    'bed_lfd_j_preis'=>0,
    'bed_lfd_plus_1_preis'=>0,
);

$abgnrArray = array();
$abgnrTextArray = array();

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
function pageheader($pdfobjekt, $headervyskaradku) {
    global $cells;
    global $jb,$wahr;
    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    $fill = 1;
    $pdfobjekt->Cell($cells['abgnr']['sirka'], $headervyskaradku, "", '0', 0, 'R', 0);
    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $headervyskaradku, "", '0', 0, 'L', 0);
    $pdfobjekt->Cell($cells['preis']['sirka'], $headervyskaradku, "Preis [$wahr]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Cell($cells['vzkd']['sirka'], $headervyskaradku, "VzKd [min]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->SetFont("FreeSans", "B", 5.5);
    if($jb===TRUE){
	$pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $headervyskaradku, "JB2013 [$wahr]", 'LRBT', 0, 'R', 1);
	$pdfobjekt->Cell($cells['bed_lfd_plus_1_preis']['sirka'], $headervyskaradku, "JB2014 [$wahr]", 'LRBT', 0, 'R', 1);
    }
    else{
	$pdfobjekt->Cell(
		$cells['bed_lfd_j_preis']['sirka']
		+$cells['bed_lfd_plus_1_preis']['sirka']
		, $headervyskaradku, "", '0', 0, 'R', 0);
    }
    $pdfobjekt->Ln();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
function zahlavi_teil($pdfobjekt,$vyskaRadku,$childNodes)
{
        global $cells;
	global $sum_zapati_teil;
	global $bMitMuster;
	
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=1;
        $pdfobjekt->Cell($cells['abgnr']['sirka'],$vyskaRadku,getValueForNode($childNodes,"teilnr"),'LBT',0,'L',$fill);
        $pdfobjekt->Cell(
                $cells['abgnr_name']['sirka']+$cells['preis']['sirka'],
                $vyskaRadku,getValueForNode($childNodes,"teilbez"),'BT',0,'L',$fill);

	$a = AplDB::getInstance();
	$teilnr = getValueForNode($childNodes, 'teilnr');
	$musterRow = $a->getTeilDokument($teilnr, 0,FALSE);
	if($musterRow===NULL)
	    $musterText = "Muster: ????";
	else
	    $musterText = $musterRow['doku_nr']."/".$musterRow['doku_beschreibung']."/".$musterRow['einlag_datum']."/".$musterRow['musterplatz']."/".$musterRow['freigabe_am']."/".$musterRow['freigabe_vom'];

	if(!$bMitMuster){
	    $musterText="";
	}
        //musterplatz
        $pdfobjekt->Cell(
                $cells['vzkd']['sirka']+90,
                $vyskaRadku,$musterText,'LTB',0,'L',$fill);

	$brGew = getValueForNode($childNodes, 'brgew');
	$nettoGew = getValueForNode($childNodes, 'gew');
	$jb_stk_j = getValueForNode($childNodes, 'jb_lfd_j');
	$jb_stk_plus_1 = getValueForNode($childNodes, 'jb_lfd_plus_1');
	$sum_zapati_teil['tonnen_lfd_j'] = $jb_stk_j * $nettoGew;
	$sum_zapati_teil['tonnen_lfd_plus_1'] = $jb_stk_plus_1 * $nettoGew / 1000;
	
	
//	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$obsah = "Kg Brutto / Netto";
	$pdfobjekt->Cell(25, $vyskaRadku, $obsah, 'LBT', 0, 'L', $fill);
	$obsah = number_format($brGew, 2, ',', ' ') . " / " . number_format($nettoGew, 2, ',', ' ');
	$pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'BTR', 0, 'R', $fill);
	
        // fremdauftr_dkopf
        $pdfobjekt->Cell(
                0,
                $vyskaRadku,
                '['.getValueForNode($childNodes,"fremdauftr_dkopf").']'
                ,'BTR',1,'R',$fill);

}


////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * @global array $cells
 * @global <type> $sum_zapati_sestava
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 * @param <type> $sumArray
 * @param <type> $teilchilds
 * @param <type> $kundechilds
 */
function zapati_teil($pdfobjekt, $vyskaRadku, $rgb, $sumArray, $teilchilds, $kundechilds) {

    global $cells;
    global $sum_zapati_sestava;
    global $zp;
    global $nurKopfZielpreis;
    global $jb,$wahr;

    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    if (!$nurKopfZielpreis) {
	//apl
	$pdfobjekt->SetFillColor(255, 255, 240, 1);
	$teillang = getValueForNode($teilchilds, 'teillang');
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, $teillang, '1', 0, 'L', 0);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Summe [APL]: ", 'LBT', 0, 'L', $fill);
	$preis = $sumArray['preis'];
	$obsah = number_format($preis, 4, ',', ' ');
	$pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	$vzkd = $sumArray['vzkd'];
	$obsah = number_format($vzkd, 4, ',', ' ');
	$pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	if ($jb === TRUE) {
	    $b_lfd_j_preis = $sumArray['bed_lfd_j_preis'];
	    $obsah = number_format($b_lfd_j_preis, 0, ',', ' ');
	    $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	    $b_lfd_plus_1_preis = $sumArray['bed_lfd_plus_1_preis'];
	    $obsah = number_format($b_lfd_plus_1_preis, 0, ',', ' ');
	    $pdfobjekt->Cell($cells['bed_lfd_plus_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
	}

	$pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);
    }

    if ($zp == TRUE) {
	//zielpreis
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(240, 255, 240, 1);
	$pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
	$pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Preis: ", 'LBT', 0, 'L', $fill);

	$ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut'));
	$obsah = number_format($ziel_preis, 4, ',', ' ');
	$pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	$ziel_vzkd = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) / floatval(getValueForNode($kundechilds, 'preismin'));
	$obsah = number_format($ziel_vzkd, 4, ',', ' ');
	$pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	$b_lfd_j_ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) * intval(getValueForNode($teilchilds, 'jb_lfd_j'));
	$sum_zapati_sestava['zielpreis_lfd_j'] += $b_lfd_j_ziel_preis;
	$obsah = number_format($b_lfd_j_ziel_preis, 0, ',', ' ');
	$pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);


	$b_lfd_plus_1_ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) * intval(getValueForNode($teilchilds, 'jb_lfd_plus_1'));
	$sum_zapati_sestava['zielpreis_lfd_plus_1'] += $b_lfd_plus_1_ziel_preis;
	$obsah = number_format($b_lfd_plus_1_ziel_preis, 0, ',', ' ');
	$pdfobjekt->Cell($cells['bed_lfd_plus_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);


	// kosten_stk_auss
	$obsah = "Kosten Auss-Stk [$wahr]";
	$pdfobjekt->Cell(47, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);
	$obsah = number_format(getValueForNode($teilchilds, 'kosten_stk_auss'), 2, ',', ' ');
	$pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 1, 'R', $fill);

	$kosten_stk_auss = floatval(getValueForNode($teilchilds, 'kosten_stk_auss')) * intval(getValueForNode($teilchilds, 'jb_lfd_j'));
	$sum_zapati_sestava['kosten_stk_auss'] += $kosten_stk_auss;
	// novy radek
//        $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);

	if (!$nurKopfZielpreis) {
	    //deltas
	    $pdfobjekt->SetFont("FreeSans", "B", 8);
	    $pdfobjekt->SetFillColor(255, 255, 255, 1);
	    $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
	    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Diff ( Ziel / APL ): ", 'LBT', 0, 'L', $fill);

	    $delta_preis = $ziel_preis - $preis;
	    if (round($delta_preis) < 0)
		$pdfobjekt->SetTextColor(255, 0, 0);
	    else
		$pdfobjekt->SetTextColor(0, 0, 0);

	    $obsah = number_format($delta_preis, 4, ',', ' ');
	    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	    $delta_vzkd = $ziel_vzkd - $vzkd;
	    if (round($delta_vzkd) < 0)
		$pdfobjekt->SetTextColor(255, 0, 0);
	    else
		$pdfobjekt->SetTextColor(0, 0, 0);

	    $obsah = number_format($delta_vzkd, 4, ',', ' ');
	    $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	    $b_lfd_j_delta_preis = $b_lfd_j_ziel_preis - $b_lfd_j_preis;
	    if (round($b_lfd_j_delta_preis) < 0)
		$pdfobjekt->SetTextColor(255, 0, 0);
	    else
		$pdfobjekt->SetTextColor(0, 0, 0);
	    $obsah = number_format($b_lfd_j_delta_preis, 0, ',', ' ');
	    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);


	    $b_lfd_plus_1_delta_preis = $b_lfd_plus_1_ziel_preis - $b_lfd_plus_1_preis;

	    if (round($b_lfd_plus_1_delta_preis) < 0)
		$pdfobjekt->SetTextColor(255, 0, 0);
	    else
		$pdfobjekt->SetTextColor(0, 0, 0);

	    $obsah = number_format($b_lfd_plus_1_delta_preis, 0, ',', ' ');
	    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

	    $pdfobjekt->SetTextColor(0, 0, 0);
	    // kosten_stk_auss zu zielpreis
	    $obsah = "KostenAussStk Factor";
	    $pdfobjekt->Cell(47, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);
	    $cislo = $ziel_preis != 0 ? floatval(getValueForNode($teilchilds, 'kosten_stk_auss')) / $ziel_preis : 0;
	    $obsah = number_format($cislo, 2, ',', ' ');
	    $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 1, 'R', $fill);


	    $pdfobjekt->Cell(0, $vyskaRadku, "", '', 1, 'R', 0);
	}
    }
    else {
	// v pripade, ze nezobrazuju Zielpreis a diff, pokusu kusy s bedarfem doleva
    }

    $pdfobjekt->Ln(2);
}


function zapati_sestava($pdfobjekt, $vyskaRadku, $rgb, $sumArray, $teilchilds, $kundechilds, $abgnrArray, $abgnrTextArray, $sumAbgnrArray) {

    global $cells;
    global $zp;
    global $nurKopfZielpreis;
    global $kunde1;
    global $teil1;

    $a = AplDB::getInstance();
    $abgnrKorrArray = $a->getAbgnrArrayForKundeAbgnr($kunde1, 95,$teil1);
    if ($abgnrKorrArray != NULL)
        $abgnrKorrArray1 = $abgnrKorrArray[0];

    $pdfobjekt->SetFont("FreeSans", "", 8);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

//    var_dump($abgnrKorrArray);
    foreach ($abgnrArray as $abgnr => $pocet) {
        $fill = 0;
        if($abgnr==95)
            $pdfobjekt->SetFont("FreeSans", "IB", 8);
        else
            $pdfobjekt->SetFont("FreeSans", "", 8);
        $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, $abgnr, '1', 0, 'R', 0);
        $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, $abgnrTextArray[$abgnr], 'LBT', 0, 'L', $fill);
        $obsah = "";
        $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);
        $obsah = "";
        $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

        $b_lfd_j_preis = $sumAbgnrArray[$abgnr]['bed_lfd_j_preis'];
        $obsah = number_format($b_lfd_j_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
        
	$b_lfd_plus_1_preis = $sumAbgnrArray[$abgnr]['bed_lfd_plus_1_preis'];
        $obsah = number_format($b_lfd_plus_1_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_lfd_plus_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
        $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);
    }

    $fill = 1;
    if (!$nurKopfZielpreis) {
        //apl
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->SetFillColor(255, 255, 240, 1);
//        $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
        $pdfobjekt->Cell($cells['abgnr']['sirka'] + $cells['abgnr_name']['sirka'], $vyskaRadku, "Summe Gesamt [APL]: ", 'LBT', 0, 'L', $fill);
        $preis = $sumArray['preis'];
        $obsah = "";
        $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

        $vzkd = $sumArray['vzkd'];
        $obsah = "";
        $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

        $b_lfd_j_preis = $sumArray['bed_lfd_j_preis'];
        $obsah = number_format($b_lfd_j_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $b_lfd_plus_1_preis = $sumArray['bed_lfd_plus_1_preis'];
        $obsah = number_format($b_lfd_plus_1_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_lfd_plus_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);
    }

    if ($zp === TRUE) {
        //zielpreis
        $pdfobjekt->Ln();
        $pdfobjekt->SetFillColor(240, 255, 240, 1);
//        $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
        $pdfobjekt->Cell($cells['abgnr']['sirka'] + $cells['abgnr_name']['sirka'], $vyskaRadku, "Preis Gesamt: ", 'LBT', 0, 'L', $fill);

        $ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut'));
        $obsah = "";
        $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

        $ziel_vzkd = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) / floatval(getValueForNode($kundechilds, 'preismin'));
        $obsah = "";
        $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

        $b_lfd_j_ziel_preis = $sumArray['zielpreis_lfd_j'];
        $obsah = number_format($b_lfd_j_ziel_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $b_lfd_plus_1_ziel_preis = $sumArray['zielpreis_lfd_plus_1'];
        $obsah = number_format($b_lfd_plus_1_ziel_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_lfd_plus_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);

        // pri omezeni na hlavicku a zapati s zielpreis
        if (!$nurKopfZielpreis) {
            //deltas
            $pdfobjekt->Ln();
            $pdfobjekt->SetFillColor(255, 255, 255, 1);


            $pdfobjekt->SetFont("FreeSans", "B", 8);
            $pdfobjekt->Cell($cells['abgnr']['sirka'] + $cells['abgnr_name']['sirka'], $vyskaRadku, "Diff Gesamt( Preis / APL ): ", 'LBT', 0, 'L', $fill);

            $delta_preis = $ziel_preis - $preis;
            $obsah = "";
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

            $delta_vzkd = $ziel_vzkd - $vzkd;
            $obsah = "";
            $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);
            $b_lfd_j_ziel_preis = $sumArray['zielpreis_lfd_j'];
            $b_lfd_plus_1_ziel_preis = $sumArray['zielpreis_lfd_plus_1'];
            $b_lfd_j_delta_preis = $b_lfd_j_ziel_preis - $b_lfd_j_preis;
            $obsah = number_format($b_lfd_j_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $b_lfd_plus_1_delta_preis = $b_lfd_plus_1_ziel_preis - $b_lfd_plus_1_preis;
            $obsah = number_format($b_lfd_plus_1_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $tonnen_lfd_j = $sumArray['tonnen_lfd_j'];
            $tonnen_lfd_plus_1 = $sumArray['tonnen_lfd_plus_1'];

            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $obsah = "Jahresbedarf to 2013 / 2014";
            $pdfobjekt->Cell(40, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);

            $obsah = number_format($tonnen_lfd_j, 0, ',', ' ') . " / " . number_format($tonnen_lfd_plus_1, 0, ',', ' ');

            $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
            // kosten_stk_auss zu zielpreis
            $obsah = "KostenAussStk Factor";
            $pdfobjekt->Cell(30, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);
            $cislo = $b_lfd_j_ziel_preis!=0?$sumArray['kosten_stk_auss']/$b_lfd_j_ziel_preis:0;
            $obsah = number_format($cislo, 2, ',', ' ');
            $pdfobjekt->Cell(0, $vyskaRadku, $obsah, 'LBTR', 1, 'R', $fill);

//            $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);
        }
    }
    else{
        //zielpreis
        // pri omezeni na hlavicku a zapati s zielpreis
        if (!$nurKopfZielpreis) {
            //deltas
            $pdfobjekt->Ln();
            $pdfobjekt->SetFillColor(255, 255, 255, 1);

            $pdfobjekt->SetFont("FreeSans", "B", 8);
//            $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
            $pdfobjekt->Cell($cells['abgnr']['sirka'] + $cells['abgnr_name']['sirka'], $vyskaRadku, "Jahresbedarf to gesamt", 'LBT', 0, 'L', $fill);

            $delta_preis = $ziel_preis - $preis;
            $obsah = "";
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

            $delta_vzkd = $ziel_vzkd - $vzkd;
            $obsah = "";
            $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);
            $b_lfd_j_ziel_preis = $sumArray['zielpreis_lfd_j'];
            $b_lfd_plus_1_ziel_preis = $sumArray['zielpreis_lfd_plus_1'];
            $b_lfd_j_delta_preis = $b_lfd_j_ziel_preis - $b_lfd_j_preis;
            
            $tonnen_lfd_j = $sumArray['tonnen_lfd_j'];
            $tonnen_lfd_plus_1 = $sumArray['tonnen_lfd_plus_1'];
            
            $obsah = number_format($b_lfd_j_delta_preis, 0, ',', ' ');
            $obsah = number_format($tonnen_lfd_j, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $b_lfd_plus_1_delta_preis = $b_lfd_plus_1_ziel_preis - $b_lfd_plus_1_preis;
            $obsah = number_format($b_lfd_plus_1_delta_preis, 0, ',', ' ');
            $obsah = number_format($tonnen_lfd_plus_1, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $tonnen_lfd_j = $sumArray['tonnen_lfd_j'];
            $tonnen_lfd_plus_1 = $sumArray['tonnen_lfd_plus_1'];

            $obsah='';
            $pdfobjekt->Cell(0, $vyskaRadku, $obsah, '0', 1, 'R', 0);

//            $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);
        }
        
    }
    $pdfobjekt->Ln();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist,$abgnr,$vzkd)
{
	global $jb;
	$pdfobjekt->SetFont("FreeSans", "", 7);
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

                if(array_key_exists("substring",$cell))
		{
                        $append='';
                        if(strlen($cellobsah)>$cell['substring'][1]) $append='...';
			$cellobsah = substr($cellobsah,$cell['substring'][0],$cell['substring'][1]).$append;
		}

                if(($abgnr==95)){
                    $pdfobjekt->SetFont("FreeSans", "I", 7);
                    if($vzkd<0)
                        $pdfobjekt->SetTextColor(255,0,0);
                    else
                        $pdfobjekt->SetTextColor(0,0,0);
                    }
                else
                    $pdfobjekt->SetFont("FreeSans", "", 7);

	    // pokud nechci jahresbedaf vynecham jeste vybrane sloupce
	    if(($jb===FALSE)&&(($nodename=="bed_lfd_j_preis")||($nodename=="bed_lfd_plus_1_preis"))){
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,"",'0',$cell["radek"],$cell["align"],$cell["fill"]);
	    }
	    else{
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	    }
//	    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        $pdfobjekt->SetTextColor(0,0,0);
	$pdfobjekt->SetFont("FreeSans", "", 7);
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
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
	}
}
				

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
                pageheader($pdfobjekt, 5);
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
	}
}

/**
 *
 * @param TCPDF $pdf
 * @param type $left
 * @param type $top
 * @param type $vyskaradku 
 */
function printBedarfTable($pdf,$left,$top,$vyskaradku,$nodes,$sumArray){
    $cols = array(
	array('width'=>20),
	array('width'=>20),
	array('width'=>20),
	array('width'=>20),
    );
    
    // whole width
    foreach ($cols as $value) {
	$tableWidth += $value['width'];
    }
    //header
    $pdf->SetY($top);
    $pdf->SetX(-($tableWidth+PDF_MARGIN_RIGHT));
    
    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->Cell($cols[0]['width'], $vyskaradku, "", 'LRBT', 0, 'L', 0);
    $pdf->Cell($cols[1]['width'], $vyskaradku, "Stk", 'LRBT', 0, 'R', 0);
    $pdf->Cell($cols[2]['width'], $vyskaradku, "to", 'LRBT', 0, 'R', 0);
    $pdf->Cell($cols[3]['width'], $vyskaradku, "$wahr", 'LRBT', 1, 'R', 0);
    
//    		    'jb_lfd_1',
//		    'jb_lfd_plus_1',
//		    'jb_lfd_j',
//		    'stk_g_ist_2012',
//		    'stk_g_ist_2013',
//		    'stk_g_ist_2014',

    $jb_lfd_1 = getValueForNode($nodes, 'jb_lfd_1');
    $jb_lfd_j = getValueForNode($nodes, 'jb_lfd_j');
    $jb_lfd_plus_1 = getValueForNode($nodes, 'jb_lfd_plus_1');
    
    $stk_g_ist_2012 = getValueForNode($nodes, 'stk_g_ist_2012');
    $stk_g_ist_2013 = getValueForNode($nodes, 'stk_g_ist_2013');
    
    $gew = getValueForNode($nodes, 'gew');
    
//    $stk_g_ist_2014 = getValueForNode($nodes, 'stk_g_ist_2014');
    
    
    
    $rows = array(
	array("JB - 2012",$jb_lfd_1,0,0),
	array("Ist - 2012",$stk_g_ist_2012,0,0),
	array("JB - 2013",$jb_lfd_j,0,0),
	array("Ist - 2013",$stk_g_ist_2013,0,0),
	array("JB - 2014",$jb_lfd_plus_1,0,0),
    );
    
    foreach ($rows as $key => $value) {
	$rows[$key][2] = number_format($rows[$key][1] * $gew/1000,0,',',' ');
	$rows[$key][3] = number_format($rows[$key][1] * $sumArray['preis'],0,',',' ');
    }
    
    $pdf->SetFont("FreeSans", "", 8);
    foreach ($rows as $fields) {
	$pdf->SetX(-($tableWidth+PDF_MARGIN_RIGHT));
	foreach ($fields as $index=>$value) {
	    $align = 'R';
	    if($index==0) $align='L';
	    $pdf->Cell($cols[$index]['width'], $vyskaradku, $value, 'LRBT', 0, $align, 0);
	}
	$pdf->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S430 - Kunde - Preise und Vorgabezeiten", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-8, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 12));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();
pageheader($pdf, 5);
$zbyva = $pdf->getPageHeight();

// zacinam po dilech
$kunden = $domxml->getElementsByTagName("kunde");
foreach ($kunden as $kunde) {
    $kundeChilds = $kunde->childNodes;
    
    $teile = $kunde->getElementsByTagName("teil");
    foreach ($teile as $teil) {
        nuluj_sumy_pole($sum_zapati_teil);
        $teilChildNodes = $teil->childNodes;
        $taetigkeiten = $teil->getElementsByTagName("tat");
        $tatCount = 0;
        foreach ($taetigkeiten as $tat) $tatCount++;
	$maxRadku = max(array($tatCount+5,10));
	if($zbyva<$maxRadku*5){
	    $pdf->AddPage();
	    pageheader($pdf, 5);
	}
	zahlavi_teil($pdf, 5, $teilChildNodes);
	$top = $pdf->GetY();
	
	$pocetTat = 0;
	$neededRows = 5;
        foreach ($taetigkeiten as $tat) {
            $tatChildNodes = $tat->childNodes;
            $abgnr = getValueForNode($tatChildNodes, 'abgnr');
            $vzkd = getValueForNode($tatChildNodes, 'vzkd');
            if(!$nurKopfZielpreis) detaily($pdf, $cells, 5, array(255, 255, 255), $tatChildNodes,$abgnr,$vzkd);
            $abgnrArray[$abgnr]++;
            $abgnrTextArray[$abgnr] = getValueForNode($tatChildNodes, 'abgnr_name');;
            $sum_zapati_sestava_abgnr[$abgnr]['bed_lfd_1_preis'] += floatval(getValueForNode($tatChildNodes, 'bed_lfd_1_preis'));
            $sum_zapati_sestava_abgnr[$abgnr]['bed_lfd_j_preis'] += floatval(getValueForNode($tatChildNodes, 'bed_lfd_j_preis'));
	    $sum_zapati_sestava_abgnr[$abgnr]['bed_lfd_plus_1_preis'] += floatval(getValueForNode($tatChildNodes, 'bed_lfd_plus_1_preis'));
	    $pocetTat++;
            foreach ($sum_zapati_teil as $key => $value) {
                $hodnota = getValueForNode($tatChildNodes, $key);
                $sum_zapati_teil[$key]+=$hodnota;
            }
        }
	if ($jb === TRUE) {
	    // make enough room for bedarf table
	    for ($i = 0; $i < ($neededRows - $pocetTat); $i++) {
		$pdf->Ln(5);
	    }
	}

	zapati_teil($pdf, 5, array(255, 255, 240), $sum_zapati_teil, $teilChildNodes, $kundeChilds);
	$y=$pdf->GetY();
	//spocitat, klik zbyva mista na strance vertikalne
	$vyskaStranky = $pdf->getPageHeight()-$pdf->getBreakMargin();
	$zbyva = $vyskaStranky-$y;
	$left = 150;//$pdf->GetX()+$cells['abgnr']['sirka']+$cells['abgnr_name']['sirka']+$cells['preis']['sirka']+$cells['vzkd']['sirka']+$cells['bed_lfd_j_preis']['sirka']+$cells['bed_lfd_plus_1_preis']['sirka'];
//	$top = $pdf->GetY();
	if($jb===TRUE) printBedarfTable($pdf, $left, $top, 5, $teilChildNodes,$sum_zapati_teil);
	// abych neovlivnil predchoti tok dokumentu vratim hodnotu y po vykresleni zapati dilu
	$pdf->SetY($y);
        foreach($sum_zapati_sestava as $key=>$value){
            $hodnota = $sum_zapati_teil[$key];
            $sum_zapati_sestava[$key] += $hodnota;
        }
    }
}

ksort($abgnrArray);
$pdf->AddPage();
pageheader($pdf, 5);
zapati_sestava($pdf, 5, array(240, 240, 255), $sum_zapati_sestava, $teilChildNodes, $kundeChilds,$abgnrArray,$abgnrTextArray,$sum_zapati_sestava_abgnr);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>