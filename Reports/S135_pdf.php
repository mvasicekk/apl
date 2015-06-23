<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S135";
$doc_subject = "S135 Report";
$doc_keywords = "S135";

// necham si vygenerovat XML

$parameters = $_GET;
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$oe = $_GET['oe'];
$von = make_DB_datum(validateDatum($_GET['von']));
$bis = make_DB_datum(validateDatum($_GET['bis']));
$reporttyp = trim($_GET['reporttyp']);
// vymenim hvezdicky za procenta
//$oe = trim(str_replace('*', '%', $oe));

$apl = AplDB::getInstance();

if ($reporttyp == 'nichtanwesend') {
    $oeArray = $apl->getOESForOEStatus('a', FALSE);
}
else
    $oeArray = split(' ', $oe);

//print_r($oeArray);

if ($oeArray == FALSE)
    $oeArray = NULL;

define('SOLL', 1);
define('IST', 2);
define('SOLLIST', 3);

if ($reporttyp == 'soll')
    $typ = SOLL;
else if ($reporttyp == 'ist')
    $typ = IST;
else
    $typ = SOLLIST;

$typ = SOLL;

$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S135", $password, $user);

if (!$fullAccess) {
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S135_xml.php');


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
	if (strtolower($label) != "password")
	    $params .= $label . ": " . $value . "  ";
	//		$params .= $label.": ".$value."  ";
    }
}



global $oeFarbenArray;

$sum_zapati_persnr_array;
global $sum_zapati_persnr_array;

$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;

$sumOEDatum = array();

function hatTagArbZeit($tag, $schicht_abteilung) {
    $vonIndex = 'von_' . $schicht_abteilung;
    $bisIndex = 'bis_' . $schicht_abteilung;
    if ((strlen($tag[$vonIndex]) == 0) && (strlen($tag[$bisIndex]) == 0))
	return 0;
    else
	return 1;
}

//
/**
 * funkce pro vykresleni hlavicky na kazde strance
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdfobjekt, $pole, $headervyskaradku, $jahr, $monat, $svatky, $pracDobaA, $vonstamp, $bisstamp, $pocetDnuNaStranku) {

    $sirkaOE = 10;
    $aktualniDen = date('Y-m-d');
    $daysBetween = ($bisstamp - $vonstamp) / (24 * 60 * 60);

    $days = array("So", "Mo", "Di", "Mi", "Do", "Fr", "Sa");

    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    $fill = 1;

    $sirkabunky = ($pdfobjekt->getPageWidth() - $sirkaOE - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT) / ($pocetDnuNaStranku);

    $markActual = false;
    $actualMonat = date('m');
    if ($actualMonat == $monat)
	$markActual = true;

    $textSize = 4.6;

    // cisla dnu
    $pdfobjekt->SetFont("FreeSans", "", $textSize);
    $pdfobjekt->Cell($sirkaOE, 5, "", '1', 0, 'L', $fill);
    for ($den = $vonstamp; $den <= $bisstamp; $den+=(24 * 60 * 60)) {
	$testDatum = date('Y-m-d', $den);

	$workday = date('w', $den);

	$hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

	//if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(245, 245, 255, 1);
	$pisDatum = date('y-m-d', $den);
	$pdfobjekt->Cell($sirkabunky, 5, $pisDatum, '1', 0, 'R', $fill);

	//if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    }
//    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(0, 5, '', '1', 1, 'R', $fill);

    // popisky dnu
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell($sirkaOE, 5, "OE", '1', 0, 'L', $fill);
    for ($den = $vonstamp; $den <= $bisstamp; $den+=(24 * 60 * 60)) {
	$testDatum = date('Y-m-d', $den);

	$workday = date('w', $den);

	$hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

	//if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(245, 245, 255, 1);
	$pdfobjekt->Cell($sirkabunky, 5, $days[$workday], '1', 0, 'R', $fill);

	//if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    }
//    $pdfobjekt->Cell($sirkabunky,5,"",'1',1,'R',$fill);


    $pdfobjekt->Ln();
    $pdfobjekt->Cell(0, 1, "", '0', 1, '0', 0);
    $pdfobjekt->SetFont("FreeSans", "", 8);
}

/**
 * funkce ktera vrati hodnotu podle nodename
 * predam ji nodelist a jmeno node ktereho hodnotu hledam
 * @param <type> $nodelist
 * @param <type> $nodename
 * @return <type>
 */
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

function zahlavi_personA($pdfobjekt, $vyskaradku, $rgb, $persnr, $nameArray, $persInfoArray, $monat, $jahr, $fullAccess) {

    global $von,$bis;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    $pocetDnuVMesiciAktual = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $pocetDnuVMesici = 31;
    $sirkabunky = ($pdfobjekt->getPageWidth() - 10 - 3 - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT - 10) / $pocetDnuVMesici;

    $name = $nameArray['name'];
    $vorname = $nameArray['vorname'];

    $jmeno = $vorname . " " . $name;
//        $monatJahr = $monat."/".$jahr;
    $monatJahr = sprintf("%02d/%04d", $monat, $jahr);
    $monat = $monat * 1;

    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->Cell(10, $vyskaradku, $persnr, '1', 0, 'L', $fill);
    $pdfobjekt->Cell(3 + 4 * $sirkabunky, $vyskaradku, $jmeno, '1', 0, 'L', $fill);

    $EndemonatJahr = sprintf("%s%02d%02d", substr($jahr, 2), $monat, $pocetDnuVMesiciAktual);

    // vormonat und Jahr
    if ($monat == 1) {
	$vormonat = 12;
	$vorjahr = $jahr - 1;
    } else {
	$vormonat = $monat - 1;
	$vorjahr = $jahr;
    }
    $vormonatJahr = $vormonat . "/" . $vorjahr;
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);
    $EndevormonatJahr = sprintf("%s%02d%02d", substr($vorjahr, 2), $vormonat, $pocetDnuVMesici);

    $sumaSoll = 0;

    $apl = AplDB::getInstance();
    $sumaSoll = $apl->getSollStundenLautDzeitSoll($von, $bis, $persnr);
    $regelarbZeit = $apl->getRegelarbeitDatum($monat, $jahr, $persnr);
    if ($regelarbZeit === NULL)
	$regelarbZeit = $persInfoArray['regelarbzeit'];

    // vypocet datumu pro pripad nastupu v prubehu mesice
    // nebudu brat cely mesic ale jen cast kdy uz nastoupil
    //$stdsoll_datum = $persInfoArray['arbtage'] * $persInfoArray['regelarbzeit'];
    $dbDatumVon = sprintf("%04d-%02d-%02d", substr($persInfoArray['eintritt'], 0, 4), substr($persInfoArray['eintritt'], 5, 2), substr($persInfoArray['eintritt'], 8, 2));
    $dbDatumBis = sprintf("%04d-%02d-%02d", $jahr, $monat, cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));
    //echo "eintritt:".$persInfoArray['eintritt']." dbDatumVon $dbDatumVon dbDatumBis $dbDatumBis";
    $sollTageMonat = $apl->getArbTageBetweenDatums($dbDatumVon, $dbDatumBis);

    // pokud nastoupi v prubehu mesice omezim pocatecni datum mesice na datum nastupu
    if ($sollTageMonat < $persInfoArray['arbtage'])
	$stdsoll_datum = $sollTageMonat * $regelarbZeit;
    else
	$stdsoll_datum = $persInfoArray['arbtage'] * $regelarbZeit;

    //TODO do aktualniho datumu brat urlaubtage ist, od aktualniho datumu do datbis brat urlaubtagesoll

    $persInfoArray['rest'] = number_format($persInfoArray['rest'], 1);

    $urlaubRestBisEndeMonat = $persInfoArray['rest'] - $persInfoArray['urlaubtageist'] - $persInfoArray['urlaubtagesoll'];

    $urlaubRestBisEndeMonat = number_format($urlaubRestBisEndeMonat, 1);

    $prozentSoll = 0;
    if ($stdsoll_datum != 0) {
	$prozentSoll = $sumaSoll / $stdsoll_datum * 100;
	$prozentSoll = round($prozentSoll);
    }

    if ($fullAccess) {
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->Cell(3 * $sirkabunky, $vyskaradku, substr($persInfoArray['komm_ort'], 0, 30), 'RTB', 0, 'L', $fill);

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	//$pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"Std/Tag: ".substr($persInfoArray['regelarbzeit'],0,30),'LTBR',0,'L',$fill);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, "Std/Tag: " . substr($regelarbZeit, 0, 30), 'LTBR', 0, 'L', $fill);
//                $pdfobjekt->SetFont("FreeSans", "B", 7);
//                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,substr($persInfoArray['regelarbzeit'],0,30),'RTB',0,'R',$fill);
	// eintrittsdatum
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, "Eintr.:", 'LTB', 0, 'L', $fill);
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$obsah = substr($persInfoArray['eintritt'], 0, 10);
	$obsah = substr($obsah, 2, 2) . substr($obsah, 5, 2) . substr($obsah, 8, 2);
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, "" . $obsah . "", 'TBR', 0, 'R', $fill);

	//doba urcita
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, "befr.:", 'LTB', 0, 'L', $fill);
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$obsah = substr($persInfoArray['dobaurcita'], 0, 10);
	$obsah = substr($obsah, 2, 2) . substr($obsah, 5, 2) . substr($obsah, 8, 2);
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, "" . $obsah . "", 'TBR', 0, 'R', $fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, "Plan 090420:", 'LTB', 0, 'L', $fill);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, substr($stdsoll_datum, 0, 30), 'RTB', 0, 'R', $fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, "Soll $monatJahr:", 'LTB', 0, 'L', $fill);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, number_format($sumaSoll, 1, ',',' '), 'RTB', 0, 'R', $fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, "d $EndevormonatJahr", 'LTB', 0, 'L', $fill);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$obsah = number_format($persInfoArray['rest'], 1, ',', ' ');
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, $obsah, 'RTB', 0, 'R', $fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, "d $EndemonatJahr", 'LTB', 0, 'L', $fill);
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$obsah = number_format($urlaubRestBisEndeMonat, 1, ',', ' ');
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, $obsah, 'RTB', 0, 'R', $fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, '+-Std' . $EndevormonatJahr, 'LTB', 0, 'L', $fill);

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$obsah = number_format($persInfoArray['plusminusstundenvor'], 1, ',', ' ');
	$pdfobjekt->Cell(1 * $sirkabunky, $vyskaradku, $obsah, 'RTB', 0, 'R', $fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(2 * $sirkabunky, $vyskaradku, '+-Std' . $EndemonatJahr, 'LTB', 0, 'L', $fill);

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$obsah = number_format($persInfoArray['plusminusstunden'], 1, ',', ' ');
	$pdfobjekt->Cell(0, $vyskaradku, $obsah, 'RTB', 0, 'R', $fill);
    }

    $pdfobjekt->Cell(0, $vyskaradku, "", '1', 1, 'L', $fill);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

/**
 *
 * @param array $oefarbenArray  rgb array
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $datumy
 * @param <type> $oekz
 * @param <type> $typ
 * @param <type> $monat
 * @param <type> $jahr
 */
function oe_radekA($persnr, $persname, $pocetOES, $oefarbenArray, $pdfobjekt, $vyskaradku, $rgb, $datumy, $oekz, $typ, $ityp, $monat, $jahr, $svatky, $tagvon, $tagbis, $von, $bis, $pocetDnuNaStranku, $lastpage = TRUE, $kumulSumaHodin = 0) {

    global $sumOEDatum;
    
    // nekresli prazdny radek pro lidi bez naplanovane dovolene
    if ($pocetOES > 1 && $oekz == 'name')
	return;
    $sirkaOE = 10;
    $sirkaPersNr = 10;
    $sirkaOEKz = 5;

    if ($pocetOES == 1) {
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->Cell($sirkaPersNr, $vyskaradku, '', '1', 0, 'L', $fill);
//	$pdfobjekt->Cell($sirkaPersNr, $vyskaradku, $persnr . ' ' . substr($persname['name'], 0, 20), '1', 0, 'L', $fill);
    } else {
	$oeRGBArray = split(",", $oefarbenArray);

	$fill = 1;
	$pdfobjekt->SetFillColor($oeRGBArray[0], $oeRGBArray[1], $oeRGBArray[2], 1);
	$pdfobjekt->SetFont("FreeSans", "B", 6);
//	$pdfobjekt->Cell($sirkaPersNr - $sirkaOEKz, $vyskaradku, $persnr . ' ' . substr($persname['name'], 0, 20), '1', 0, 'L', $fill);
	$pdfobjekt->Cell($sirkaPersNr, $vyskaradku, ' '.$oekz, '1', 0, 'L', $fill);
    }
//        $pdfobjekt->Cell(10,$vyskaradku,$oefarbenArray,'1',0,'L',$fill);
    //$pdfobjekt->Cell(3,$vyskaradku,$typ,'1',0,'L',$fill);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);


    $daysBetween = ($bis - $von) / (24 * 60 * 60);
//        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
    $sirkabunky = ($pdfobjekt->getPageWidth() - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT - $sirkaPersNr) / ($pocetDnuNaStranku);
    //$sirkabunky = 10;
    $pdfobjekt->SetFont("FreeSans", "", 7);
    $sumaHodin = 0;

    for ($den = $von; $den <= $bis; $den+=(24 * 60 * 60)) {
	$testDatum = date('Y-m-d', $den);
	// oznaceni so+ne a svatku
	$workday = date('w', $den);
	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(245, 245, 255, 1);

	$kresliPrazdny = 1;
	if (is_array($datumy)) {
	    foreach ($datumy as $dat => $stunden) {
		$tag = intval(substr($dat, 8));
		if ($dat == $testDatum) {
		    $sumOEDatum[$oekz][$testDatum] += floatval($stunden);
		    
		    if ($stunden == 0) {
			$pdfobjekt->SetFillColor($oeRGBArray[0], $oeRGBArray[1], $oeRGBArray[2], 1);
			//                            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$oekz,'1',0,'R',$fill);
			$stunden = number_format($stunden, 1);
			$pdfobjekt->Cell($sirkabunky, $vyskaradku, $stunden, '1', 0, 'R', $fill);
			$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
		    } else {
			$sumaHodin += $stunden;
			$stunden = number_format($stunden, 1);
			$pdfobjekt->SetFillColor($oeRGBArray[0], $oeRGBArray[1], $oeRGBArray[2], 1);
			$pdfobjekt->Cell($sirkabunky, $vyskaradku, $stunden, '1', 0, 'R', $fill);
			$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
		    }
		    $kresliPrazdny = 0;
		    break;
		}
	    }
	}

	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(245, 245, 255, 1);

	if ($kresliPrazdny) {
	    //                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	    $pdfobjekt->Cell($sirkabunky, $vyskaradku, "", '1', 0, 'R', $fill);
	}

	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    }
    $pdfobjekt->Ln();
    return $sumaHodin;
}

function oe_radekOE($oefarbenArray, $pdfobjekt, $vyskaradku, $rgb, $datumy, $oekz, $typ, $ityp, $monat, $jahr, $svatky, $tagvon, $tagbis, $von, $bis, $pocetDnuNaStranku, $lastpage = TRUE, $kumulSumaHodin = 0) {

    $sirkaOE = 10;
    $sirkaOEKz = 5;

    $oeRGBArray = split(",", $oefarbenArray);
    $fill = 1;
    $pdfobjekt->SetFillColor($oeRGBArray[0], $oeRGBArray[1], $oeRGBArray[2], 1);
    $pdfobjekt->SetFont("FreeSans", "B", 6);
    $pdfobjekt->Cell($sirkaOE, $vyskaradku, ' '.$oekz, '1', 0, 'L', $fill);

    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
//
//
    $daysBetween = ($bis - $von) / (24 * 60 * 60);
////        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
    $sirkabunky = ($pdfobjekt->getPageWidth() - PDF_MARGIN_RIGHT - PDF_MARGIN_LEFT - $sirkaOE) / ($pocetDnuNaStranku);
//    //$sirkabunky = 10;
    $pdfobjekt->SetFont("FreeSans", "", 7);
//    $sumaHodin = 0;
//
    for ($den = $von; $den <= $bis; $den+=(24 * 60 * 60)) {
	$testDatum = date('Y-m-d', $den);
	// oznaceni so+ne a svatku
	$workday = date('w', $den);
	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(245, 245, 255, 1);

	$kresliPrazdny = 1;
	if (is_array($datumy)) {
	    foreach ($datumy as $dat => $stunden) {
		$tag = intval(substr($dat, 8));
		if ($dat == $testDatum) {
		    if ($stunden == 0) {
			$pdfobjekt->SetFillColor($oeRGBArray[0], $oeRGBArray[1], $oeRGBArray[2], 1);
			$stunden = number_format($stunden, 1);
			$pdfobjekt->Cell($sirkabunky, $vyskaradku, $stunden, '1', 0, 'R', $fill);
			$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
		    } else {
			$stunden = number_format($stunden, 1);
			$pdfobjekt->SetFillColor($oeRGBArray[0], $oeRGBArray[1], $oeRGBArray[2], 1);
			$pdfobjekt->Cell($sirkabunky, $vyskaradku, $stunden, '1', 0, 'R', $fill);
			$pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
		    }
		    $kresliPrazdny = 0;
		    break;
		}
	    }
	}

	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor(245, 245, 255, 1);

	if ($kresliPrazdny) {
	    //                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	    $pdfobjekt->Cell($sirkabunky, $vyskaradku, "", '1', 0, 'R', $fill);
	}

	if ($workday == 6 || $workday == 0 || in_array($testDatum, $svatky))
	    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    }
    $pdfobjekt->Ln();
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vysradku
 * @param <type> $cellhead
 * @param <type> $jahr
 * @param <type> $monat
 */
function test_pageoverflow($pdfobjekt, $vysradku, $cellhead, $jahr, $monat, $svatky, $pracDoba, $von, $bis, $pocetDnuNaStranku) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
	$pdfobjekt->AddPage();
	pageheader($pdfobjekt, $cellhead, 5, $jahr, $monat, $svatky, $pracDoba, $von, $bis, $pocetDnuNaStranku);
    }
}

/**
 *
 * @param <type> $nodesArray
 * @param <type> $persnr
 * @return array
 */
function getPersonalInfo($nodesArray, $persnr) {
    $vystup = array(
	"komm_ort" => "",
	"regelarbzeit" => "",
	"eintritt" => "",
	"stdsoll_datum" => "",
	"dobaurcita" => "",
	"MAStunden" => 1,
    );

    // hledam persnr
    foreach ($nodesArray as $node) {
	$nodeChilds = $node->childNodes;
	$persnr1 = getValueForNode($nodeChilds, 'persnr');
	if ($persnr1 == $persnr) {
	    $vystup['komm_ort'] = getValueForNode($nodeChilds, 'komm_ort');
	    $vystup['regelarbzeit'] = getValueForNode($nodeChilds, 'regelarbzeit');
	    $vystup['eintritt'] = getValueForNode($nodeChilds, 'eintritt');
	    $vystup['stdsoll_datum'] = getValueForNode($nodeChilds, 'stdsoll_datum');
	    $vystup['dobaurcita'] = getValueForNode($nodeChilds, 'dobaurcita');
	    $vystup['MAStunden'] = getValueForNode($nodeChilds, 'MAStunden');
	    return $vystup;
	}
    }
    return $vystup;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L', 'mm', 'A4', 1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S135 Personal Anwesenheitsplanung", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP - 10, PDF_MARGIN_RIGHT);
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


$persInfoArray = $domxml->getElementsByTagName('persinfo');

// vytahnu si oefarben
$farben = $domxml->getElementsByTagName('farbe');
foreach ($farben as $farbe) {
    $farbeChilds = $farbe->childNodes;
    $key = getValueForNode($farbeChilds, 'oe');
    $value = getValueForNode($farbeChilds, 'rgb');
    $oeFarbenArray[$key] = $value;
}

//print_r($farben);

$reportArray = array();
$ogArray = array();

if ($typ == 1)
    $sollIstArray = array("soll");
foreach ($sollIstArray as $sollIst) {
    $planTree = $domxml->getElementsByTagName($sollIst)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen = $planTree->getElementsByTagName("pers");
    foreach ($personen as $person) {
	$personChilds = $person->childNodes;
	$persnr = getValueForNode($personChilds, "persnr");
	$name = getValueForNode($personChilds, "name");
	$vorname = getValueForNode($personChilds, "vorname");
	$reportNameArray[$persnr] = array('name' => $name, 'vorname' => '');
	$og = getValueForNode($personChilds, "og");
	$persoe = getValueForNode($personChilds, "persoe");
	if (strlen($persoe) == 0)
	    $persoe = 'ZZZ';
	//tady si budu vytvaret seznam vsech objevivsich se og
	$ogArray[$og]++;
	$persoeArray[$persoe]++;
	$oes = $person->getElementsByTagName("oe");
	$reportArray[$og][$persoe][$persnr]['name'] = $name;
	foreach ($oes as $oe) {
	    $oeChilds = $oe->childNodes;
	    $oekz = getValueForNode($oeChilds, "oekz");
//            $og = getValueForNode($oeChilds, "og");
	    $tage = $oe->getElementsByTagName("tag");
	    foreach ($tage as $tag) {
		$tagChilds = $tag->childNodes;
		$datum = getValueForNode($tagChilds, "datum");
		$stunden = getValueForNode($tagChilds, "stunden");
		$reportArray[$og][$persoe][$persnr][$oekz][$sollIst][$datum] = $stunden;
	    }
	}
    }
}

ksort($ogArray);
ksort($persoeArray);

$datetimevon = strtotime($von);
$datetimebis = strtotime($bis);
$monat = date('m', $datetimevon);
$jahr = date('Y', $datetimevon);

$daysBetween = ($datetimebis - $datetimevon) / (24 * 60 * 60);

$svatkyArray = $apl->naplnPoleSvatku(date('Y-m-d', $datetimevon), date('Y-m-d', $datetimebis));

$werkTageLautCalendarArray = $apl->getSollStundenLautCalendar($von, $bis, 0);
$werkTageLautCalendar = $werkTageLautCalendarArray['arbtage'];

// podle von a bis zjistim, zda budu tisknout vice mesicu
$mesicVon = substr($von, 5, 2);
$denVon = substr($von, 8, 2);
$rokVon = substr($von, 0, 4);

$mesicBis = substr($bis, 5, 2);
$denBis = substr($bis, 8, 2);
$rokBis = substr($bis, 0, 4);

$vonTest = mktime(0, 0, 1, $mesicVon, $denVon, $rokVon);
$cisloMesiceVon = date('m', $vonTest);
$bisTest = mktime(0, 0, 1, $mesicBis, $denBis, $rokBis);
$cisloMesiceBis = date('m', $bisTest);

$pocetMesicuMezi = $cisloMesiceBis - $cisloMesiceVon;
if ($pocetMesicuMezi < 0)
    $pocetMesicuMezi+=12;
$pocetMesicuMezi++;
$pocetDnuMezi = ($bisTest - $vonTest) / (60 * 60 * 24);

$pocetDnuNaStranku = 31;
// na stranku chci maximalne 30 dnu
$pocetStranekNaSirku = $pocetDnuMezi / $pocetDnuNaStranku;


$apl = AplDB::getInstance();

foreach ($ogArray as $og => $ogCount) {
    $datetimevon = $vonTest;

    for ($stranka = 0; $stranka < $pocetStranekNaSirku; $stranka++) {

	$datetimebis = $datetimevon + ($pocetDnuNaStranku - 1) * 24 * 60 * 60;
	if ($datetimebis > $bisTest)
	    $datetimebis = $bisTest;

	$pdf->AddPage();
	pageheader($pdf, $cells_header, 5, $jahr, $monat, $svatkyArray, $pracDobaA, $datetimevon, $datetimebis, $pocetDnuNaStranku);
	$pdf->SetFont("FreeSans", "B", 8);
	$pdf->Cell(0, 5, $og, 'LRTB', 1, 'L', 0);
	foreach ($reportArray[$og] as $persoe => $persoeCount) {
	    test_pageoverflow($pdf, 6, $cellhead, $jahr, $monat, $svatkyArray, $pracDobaA, $datetimevon, $datetimebis, $pocetDnuNaStranku);
	    $pdf->SetFont("FreeSans", "B", 6);
	    $pdf->Ln(3);
	    $pdf->Cell(30, 3, $persoe, 'LRTB', 1, 'L', 0);
	    $pdf->SetFont("FreeSans", "", 8);
	    foreach ($reportArray[$og][$persoe] as $persnr => $person) {
		// do oes si ted dam pole vsech cinnost
		$oes = $person;
		$pocetOEs = count($oes);
		$sumaHodin = 0;
		// kontrola, zda ma cloveknejake naplanovane oe
		//if(count($oes)>0) {
		$personalinfo = getPersonalInfo($persInfoArray, $persnr);
		$personalinfo['arbtage'] = $werkTageLautCalendar;
		// vormonat und Jahr
		if ($monat == 1) {
		    $vormonat = 12;
		    $vorjahr = $jahr - 1;
		} else {
		    $vormonat = $monat - 1;
		    $vorjahr = $jahr;
		}
		$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);
		$vormonatdatbis = $vorjahr . "-" . $vormonat . "-" . $pocetDnuVMesici;
		$restArray = $apl->getUrlaubBisDatum($persnr, $vormonatdatbis);
		//print_r($restArray);
		$personalinfo['rest'] = $restArray['rest'];

		$personalinfo['urlaubtagesoll'] = $apl->getUrlaubTageInMonatSoll($persnr, $monat, $jahr,TRUE);
		$personalinfo['urlaubtageist'] = $apl->getUrlaubTageInMonatIst($persnr, $monat, $jahr);

//		AplDB::varDump($personalinfo);
		// prescasy
		$stddiffA = $apl->getStdDiff($monat, $jahr, $persnr);
		if ($stddiffA != null) {
		    $personalinfo['stddif_stunden'] = $stddiffA['stunden'];
		    $personalinfo['stddif_datum'] = $stddiffA['datum'];
		} else {
		    $personalinfo['stddif_stunden'] = 0;
		    $personalinfo['stddif_datum'] = '??????';
		}

		if (intval($personalinfo['MAStunden']) != 0) {
		    $personalinfo['plusminusstunden'] = $apl->getPlusMinusStunden($monat, $jahr, $persnr);
		    $personalinfo['plusminusstundenvor'] = $apl->getPlusMinusStunden($vormonat, $vorjahr, $persnr);
		} else {
		    $personalinfo['plusminusstunden'] = 0;
		    $personalinfo['plusminusstundenvor'] = 0;
		}

		// pocetOE + zahlavi pro persnr + zapati pro persnr
		$nasobek = 1;
		if ($typ == 3)
		    $nasobek = 2;
		$vyskaSekce = ($nasobek * $pocetOEs + 1 + 1) * 5;
		if (($stranka + 1) > $pocetStranekNaSirku)
		    $lastPage = TRUE;
		else
		    $lastPage = FALSE;
		// NK probehnu vsechna oe pro osobu
		//$sql = "select dpers";
		//zahlavi persnr
		test_pageoverflow($pdf, 5, $cellhead, $jahr, $monat, $svatkyArray, $pracDobaA, $datetimevon, $datetimebis, $pocetDnuNaStranku);
		zahlavi_personA($pdf, 5, array(240, 240, 240), $persnr, $reportNameArray[$persnr], $personalinfo, $monat, $jahr, TRUE);
//		    zahlavi_personA($pdf, 5, array(230,255,230), $persnr, $reportNameArray[$persnr],$personalinfo, 0,0,0,0, 0,$datetimevon,$datetimebis,$pocetDnuNaStranku);
		unset($sumaPersNrOE);
		foreach ($oes as $oekz => $oe) {
		    $datumy_soll = $oe['soll'];
		    test_pageoverflow($pdf, 5, $cellhead, $jahr, $monat, $svatkyArray, $pracDobaA, $datetimevon, $datetimebis, $pocetDnuNaStranku);
		    $tmp = oe_radekA($persnr, $reportNameArray[$persnr], $pocetOEs, $oeFarbenArray[$oekz], $pdf, 5, array(255, 245, 245), $datumy_soll, $oekz, "s", $typ, $monat, $jahr, $svatkyArray, $tagvon, $tagbis, $datetimevon, $datetimebis, $pocetDnuNaStranku, $lastPage, $sumaPersNrOE[$oekz]);
		    $sumaPersNrOE[$oekz]+=$tmp;
		}
	    }
	}
	$datetimevon += ($pocetDnuNaStranku) * 24 * 60 * 60;
    }
}

//vytahnu vsechny oe z pole a seradim podle abecedy
$oesVSume = array();
$oesVSume = array_keys($sumOEDatum);
sort($oesVSume);
//AplDB::varDump($oesVSume);
//zapati se sumama pro OE
$datetimevon = $vonTest;

for ($stranka = 0; $stranka < $pocetStranekNaSirku; $stranka++) {

    $datetimebis = $datetimevon + ($pocetDnuNaStranku - 1) * 24 * 60 * 60;
    if ($datetimebis > $bisTest)
	$datetimebis = $bisTest;

    $pdf->AddPage();
    pageheader($pdf, $cells_header, 5, $jahr, $monat, $svatkyArray, $pracDobaA, $datetimevon, $datetimebis, $pocetDnuNaStranku);

    
    foreach ($oesVSume as $oe) {
	$oeArray = $sumOEDatum[$oe];
	oe_radekOE($oeFarbenArray[$oe], $pdf, 5, array(255,245,245), $oeArray, $oe, 0, 0, $monat, $jahr, $svatkyArray, $tagvon, $tagbis, $datetimevon, $datetimebis, $pocetDnuNaStranku,$lastPage);
//	$pdf->Cell(10, 5, $oe, 'LRBT', 0, 'L', 0);
//	$pdf->Cell(0, 5, join(',', $oeArray), 'LRBT', 1, 'L', 0);
    }
    
    
    $datetimevon += ($pocetDnuNaStranku) * 24 * 60 * 60;
}

//AplDB::varDump($oeFarbenArray);
//AplDB::varDump($reportArray);
//AplDB::varDump($sumOEDatum);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>