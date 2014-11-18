<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S192";
$doc_subject = "S192 Report";
$doc_keywords = "S192";

// necham si vygenerovat XML

$parameters=$_GET;
$persnr = trim($_GET['persnr']);

if(strlen($persnr)==0 || $persnr=='*')
    $bNoPersnr=TRUE;
else
    $bNoPersnr=FALSE;

$apl = AplDB::getInstance();

if(strlen(trim($_GET['eintrittvom']))>0)
    $bEintrittVom = TRUE;
else
    $bEintrittVom = FALSE;

$eintrittVom = $apl->make_DB_datum($apl->validateDatum(trim($_GET['eintrittvom'])));

require_once('S192_xml.php');

//exit;
$apl = AplDB::getInstance();
//exit;
// priplatek v procentech pro jednotlive dny
// bude se nacitat z db podle cisla persnr
//                       1.  2.  3. 4. 5. 6. 7. 8. 9. 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24
$priplatekArray = array(
                        "S0011"=>array(100,100,50,50,50,50,50,50,30,30,30,30,30,30,20,20,20,20,20,20),
                        "S0041"=>array(50,50,30,30,20,20,20,20,20,20,0,0,0,0,0,0,0,0,0,0),
                        "S0051"=>array(100,100,50,50,50,50,50,50,30,30,30,30,30,30,20,20,20,20,20,20),
                        "S0061"=>array(50,50,30,30,20,20,20,20,20,20,0,0,0,0,0,0,0,0,0,0),
                        "X"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
);





$priplatekDpersDatumZuschlag = $apl->getDpersDatumZuschlagArray($persnr);

//echo "<pre>";
//var_dump($priplatekDpersDatumZuschlag);
//echo "</pre>";
// definice barev pro priplatky

$priplatekBarva = array(
  100=>array(255,255,230),
  50=>array(230,230,255),
  30=>array(230,255,230),
  20=>array(255,230,230),
);

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



/**
 *
 * @param TCPDF $pdf
 * @param SimpleXMLElement $person
 */
function personalInfoHeader($pdf,$person){
        $pdf->SetFont("FreeSans", "B", 10);
        $pdf->SetFillColor(255,255,200,1);
//        $pdf->Cell(15, 7, $personen->person->persnr, 'BTLR', 0, 'C', 1);
//        $pdf->Cell(60, 7, $personen->person->name, 'BTLR', 0, 'C', 1);
//        $pdf->Cell(60, 7, " Eintritt: ".substr($personen->person->eintritt,0,10), 'BTLR', 0, 'C', 1);

        $persnr = $pdf->persnr;
        $pdf->Cell(15, 7, $person->persnr, 'BTLR', 0, 'C', 1);
        $pdf->Cell(60, 7, $person->name, 'BTLR', 0, 'C', 1);
        $pdf->Cell(60, 7, " Eintritt: ".substr($person->eintritt,0,10), 'BTLR', 0, 'C', 1);
        $pdf->Ln();
}


function test_pageoverflow_nonewpage($pdf,$vyskaradku)
{
	if(($pdf->GetY()+$vyskaradku)>($pdf->getPageHeight()-$pdf->getBreakMargin()))
	{
            return TRUE;
	}
        return FALSE;
}

function test_pageoverflow($pdf,$vyskaradku,$rgb,$datumArray,$priplatekArray)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdf->GetY()+$vyskaradku)>($pdf->getPageHeight()-$pdf->getBreakMargin()))
	{
		$pdf->AddPage();
                pageheaderMesice($pdf,$datumArray,$priplatekArray,$rgb,5);
	}
}

/**
 *
 * @param SimpleXMLElement $pole
 * @param string $og
 * @param string $datum
 * @param string $field 
 */
function getMinuten($pole,$og,$datum,$field){
    //najedu si na zvolene datum
    foreach($pole->person->tage->tag as $tag){
        $date = trim($tag->datum);
//        echo "<br>date=$date ";
        if(!strcmp($date, $datum)){
            // nasel jsem datum
//            echo "$date==$datum, pokacuju na hledani og ";
            foreach($tag->ogs->og as $ogcko){
                $ognr = trim($ogcko->ognr);
//                echo "ognr=$ognr ";
                if(!strcmp($ogcko->ognr,$og)){
//                    echo "$ognr==$og , vracim hodnotu";
                    return floatval($ogcko->{$field});
                }
            }
        }
    }
    return 0;
}

/**
 *
 * @param TCPDF $pdf
 * @param <type> $vyskaradku
 * @param <type> $beschreibung
 * @param <type> $datumyArray
 * @param <type> $summenArray
 * @param <type> $field
 * @param <type> $runden
 */
function echoGesamtSumme($pdf,$vyskaradku,$beschreibung,$datumyArray,$summenArray,$field,$decimals=0) {
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekArray;
    global $priplatekBarva;
    
    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    
    $pdf->Cell($sirkaBeschriftung,$vyskaradku, $beschreibung,'LRBT',0, 'L',0);
    $cislodne = 0;
    $sum = 0;
    foreach ($datumyArray as $datum) {
        $datum = trim($datum);
//        if($runden>0)
//            $value = round($summenArray[$datum][$field]);
//        else
//            $value = number_format($summenArray[$datum][$field],1);
        $value = number_format($summenArray[$datum][$field],$decimals,',',' ');

        $sum += $summenArray[$datum][$field];
        $sumvzaby += $summenArray[$datum]['vzaby'];
        $sumstunden += $summenArray[$datum]['stunden'];

        $rgbPriplatek = $priplatekBarva[$priplatekArray[$cislodne]];
//        var_dump($rgbPriplatek);
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky, $vyskaradku, $value, 'LRBT', 0, 'R', 1);
        $cislodne++;
    }
    for($i=$cislodne;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }
//    for($i=0;$i<$pocetPriplatkovychDnu-$cislodne;$i++) $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    if($runden>0) $sum = round($sum);
    $sum = number_format($sum, $decimals,',',' ');
    // pro nektera pole nema suma pro radek smysl, proto je nezobrazim, napr. leistungsfaktor
    if(strcmp($field, 'leistungsfaktor'))
        $pdf->Cell(0, $vyskaradku, $sum, 'LRBT', 0, 'R', 0);
    
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param <type> $beschreibung
 * @param <type> $hodnoty
 * @param <type> $datumyArray
 * @param <type> $og
 * @param <type> $field
 * @param int $decimals pocet desetinnych mist po zaokrouhleni
 */
function echoOGZeile($pdf,$vyskaradku,$beschreibung,$hodnoty,$datumyArray,$og,$field,$decimals=0) {
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekBarva;
    global $priplatekArray;

    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $pdf->Cell($sirkaBeschriftung,$vyskaradku, $beschreibung,'LRBT',0, 'L',0);
    $cislodne = 0;
    $sum = 0;
    foreach($datumyArray as $datum) {
        $og = trim($og);
        $datum = trim($datum);
//        if($runden>0)
//            $value = round($hodnoty[$og][$datum][$field]);
//        else
            $value = number_format($hodnoty[$og][$datum][$field],$decimals,',',' ');

        $sum += $hodnoty[$og][$datum][$field];
        $sumvzaby += $hodnoty[$og][$datum]['vzaby'];
        $sumstunden += $hodnoty[$og][$datum]['stunden'];
//        var_dump($priplatekBarva);
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$cislodne]];
//        var_dump($rgbPriplatek);
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);

        $pdf->Cell($sirkaBunky, $vyskaradku, $value, 'LRBT', 0, 'R', 1);
        $cislodne++;
    }
    for($i=$cislodne;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }

//    for($i=0;$i<$pocetPriplatkovychDnu-$cislodne;$i++) $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
//    if($runden>0) $sum = round($sum);
    if(!strcmp($field, 'leistungsfaktor')){
        if($sumstunden!=0)
            $sum = $sumvzaby/($sumstunden*60);
        else
            $sum = 0;
    }
    $sum = number_format($sum, $decimals,',',' ');
    $pdf->Cell(0, $vyskaradku, $sum, 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param array $datumArray
 * @param array $priplatekArray
 * @param array $rgb
 * @param int $vyskaradku 
 */
function pageheaderMesice($pdf,$datumArray,$priplatekArray,$rgb,$vyskaradku) {
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekBarva;

    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "B", 6);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    // bunka s popiskem, zde prazdna
    $pdf->Cell($sirkaBeschriftung, $vyskaradku,'Datum','LRT',0,'L',0);

    // datumy, datum je ve tvaru YYYYMMDD, zobrazim ve tvaru DD.MM.
    $den=0;
    foreach($datumArray as $datum) {
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$den]];
        $obsah = substr($datum, 6,2).".".substr($datum, 4,2).".";
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,$obsah,'LRBT',0,'R',1);
        //$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        $den++;
    }
    //dorovnam na 24 dnu
    for($i=$den;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }
    $pdf->Ln();

    // cisla dnu + procentni sazba priplatku
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdf->Cell($sirkaBeschriftung, $vyskaradku,'priplatek [%]','LRB',0,'L',0);
    $den = 0;
    foreach($datumArray as $datum) {
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$den]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,$priplatekArray[$den]." %",'LRBT',0,'R',1);
        $den++;
    }
    //dorovnam na 24 dnu
    for($i=$den;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }


    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdf->Cell(0, $vyskaradku, 'Sum', 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

/**
 *
 * @param TCPDF $pdf
 * @param <type> $vyskaradku
 * @param <type> $hodnoty
 * @param <type> $datumyArray
 * @param <type> $og 
 */
function OGSummeCZK($pdf,$vyskaradku,$hodnoty,$datumyArray,$og){
    $sirkaBeschriftung = 40;
    $sirkaSumaRadku = 20;
    $pocetPriplatkovychDnu = 24;
    global $priplatekArray;
    global $priplatekBarva;
    $sirkaBunky = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$sirkaBeschriftung-$sirkaSumaRadku)/$pocetPriplatkovychDnu;
    $pdf->SetFont("FreeSans", "B", 7);
//    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $og = trim($og);
    $lohnfaktor = 0;
    // najdu si mezi vsemi hodnotami datumu lohnfaktor
    foreach($datumyArray as $datum){
        $datindex = trim($datum);
        if($lohnfaktor<$hodnoty[$og][$datindex]['lohnfaktor']) $lohnfaktor = $hodnoty[$og][$datindex]['lohnfaktor'];
    }
    $pdf->Cell($sirkaBeschriftung, $vyskaradku,$og." ( ".$lohnfaktor." ) CZK",'LRB',0,'L',0);

    //celkem vzaby kc
    $cislodne = 0;
    $sum = 0;
    foreach($datumyArray as $datum){
        $og = trim($og);
        $datum = trim($datum);
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$cislodne]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,round($hodnoty[$og][$datum]['celkemvzabykc']),'LRBT',0,'R',1);
        $sum += $hodnoty[$og][$datum]['celkemvzabykc'];
        $cislodne++;
    }
    for($i=$cislodne;$i<$pocetPriplatkovychDnu;$i++){
        $rgbPriplatek = $priplatekBarva[$priplatekArray[$i]];
        $pdf->SetFillColor($rgbPriplatek[0],$rgbPriplatek[1],$rgbPriplatek[2],1);
        $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    }
//    for($i=0;$i<$pocetPriplatkovychDnu-$cislodne;$i++) $pdf->Cell($sirkaBunky,$vyskaradku,'','LRBT',0,'R',1);
    $pdf->Cell(0, $vyskaradku, round($sum), 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

// tisk detailu na prazdnem rasku

function statNrRadek($pdf, $vyskaRadku, $decimals, $label, $index, $datumArray, $og, $statnr, $hodnoty) {
    global $poleWidth;
    global $anwesenheitWidth;
    global $datumFontSize;
    $fill = 0;
    $sumRow = 0;
    $obsah = $label;
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->Cell($anwesenheitWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'L', $fill);
    foreach ($datumArray as $datum) {
        $sumRow += floatval($hodnoty[$statnr][$index][$datum]);
        if (floatval($hodnoty[$statnr][$index][$datum]) != 0)
            $obsah = number_format($hodnoty[$statnr][$index][$datum], $decimals, ',', ' ');
        else
            $obsah = '';
        $pdf->Cell($poleWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'R', $fill);
    }
    for($i=0;$i<24-count($datumArray);$i++) $pdf->Cell($poleWidth, 5, '', 'LRBT', 0, 'R', 0);
    $obsah = number_format($sumRow, $decimals, ',', ' ');
    if($index=='priplatek_prozent') $obsah = '';
    $pdf->Cell(0, $vyskaRadku, $obsah, 'LRBT', 1, 'R', $fill);
}

function OGRadek($pdf, $vyskaRadku, $decimals, $label, $index, $datumArray, $og, $hodnoty) {
    global $poleWidth;
    global $anwesenheitWidth;
    $fill = 0;
    $sumRow = 0;

    $obsah = $label;
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->Cell($anwesenheitWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'L', $fill);
    foreach ($datumArray as $datum) {
        $sumRow += floatval($hodnoty[$og][$index][$datum]);
        if (floatval($hodnoty[$og][$index][$datum]) != 0)
            $obsah = number_format($hodnoty[$og][$index][$datum], $decimals, ',', ' ');
        else
            $obsah = '';
        $pdf->Cell($poleWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'R', $fill);
    }
    for($i=0;$i<24-count($datumArray);$i++) $pdf->Cell($poleWidth, 5, '', 'LRBT', 0, 'R', 0);
    $obsah = number_format($sumRow, $decimals, ',', ' ');
    $pdf->Cell(0, $vyskaRadku, $obsah, 'LRBT', 1, 'R', $fill);
}

function gesamtOGRadek($pdf, $vyskaRadku, $decimals, $label, $index, $datumArray, $hodnoty) {
    global $poleWidth;
    global $anwesenheitWidth;
    $fill = 0;
    $sumRow = 0;

    $obsah = $label;
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->Cell($anwesenheitWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'L', $fill);
    foreach ($datumArray as $datum) {
        $sumRow += floatval($hodnoty[$index][$datum]);
        if (floatval($hodnoty[$index][$datum]) != 0)
            $obsah = number_format($hodnoty[$index][$datum], $decimals, ',', ' ');
        else
            $obsah = '';
        $pdf->Cell($poleWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'R', $fill);
    }
    for($i=0;$i<24-count($datumArray);$i++) $pdf->Cell($poleWidth, 5, '', 'LRBT', 0, 'R', 0);
    $obsah = number_format($sumRow, $decimals, ',', ' ');
    if($index=='leist_faktor') $obsah = '';

    $pdf->Cell(0, $vyskaRadku, $obsah, 'LRBT', 1, 'R', $fill);
}

function gesamtRadek($pdf, $vyskaRadku, $decimals, $label, $datumArray, $hodnoty) {
    global $poleWidth;
    global $anwesenheitWidth;
    $fill = 0;
    $sumRow = 0;
    $pdf->SetFont("FreeSans", "B", 7);
    $obsah = $label;
    $pdf->Cell($anwesenheitWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'L', $fill);
    foreach ($datumArray as $datum) {
        $value = floatval($hodnoty['vzaby_celkem_kc'][$datum])+floatval($hodnoty['prasne'][$datum])+floatval($hodnoty['qualpremie_kc'][$datum]);//+floatval($hodnoty['leist_prem'][$datum]);
        $sumRow += $value;
        if ($value != 0)
            $obsah = number_format($value, $decimals, ',', ' ');
        else
            $obsah = '';
        $pdf->Cell($poleWidth, $vyskaRadku, $obsah, 'LRBT', 0, 'R', $fill);
    }
    for($i=0;$i<24-count($datumArray);$i++) $pdf->Cell($poleWidth, 5, '', 'LRBT', 0, 'R', 0);
    $obsah = number_format($sumRow, $decimals, ',', ' ');
    $pdf->Cell(0, $vyskaRadku, $obsah, 'LRBT', 1, 'R', $fill);
}

function datumyHeader($pdf, $vyskaradku, $datumyArray) {
    global $datumFontSize;
    global $poleWidth;
    global $anwesenheitWidth;
    $citacDnu = 0;
    $pocetDnu = 24;
    $pdf->SetFont("FreeSans", "", $datumFontSize);
    $pdf->Cell($anwesenheitWidth, 5, 'datum', '0', 0, 'L', 0);
    foreach ($datumyArray as $datum) {
        $obsah = substr($datum, 2, 2) . substr($datum, 5, 2) . substr($datum, 8, 2);
        $obsah = substr($datum, 8, 2).'.'.substr($datum, 5, 2).'.';
        $pdf->Cell($poleWidth, 5, $obsah, 'LRBT', 0, 'R', 0);
        $citacDnu++;
    }
    for($i=$citacDnu;$i<$pocetDnu;$i++) $pdf->Cell($poleWidth, 5, '', 'LRBT', 0, 'R', 0);
    $pdf->Ln();
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S192 MA Zuschläge Einarbeitung", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-5, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setHeaderMargin(10);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);


$personen = simplexml_import_dom($domxml);


foreach ($personen as $person) {
    $datumyArray = array();
    $anwesenheitArray = array();
    $statnrArray = array();
    $priplatekDayCounter = array();
    $hodnoty = array();

    if (trim($person->persnr) > 0) {
        $pdf->AddPage();
        personalInfoHeader($pdf, $person);

        $persnr = trim($person->persnr);
        //vytvorim si pole s datumama
        // omezim na maximalne 24 dnu
        $maxTagen = 24;
        $tagcounter = 0;
        foreach ($person->tage->tag as $tag) {
            array_push($datumyArray, trim($tag->datum));
            foreach ($tag->statnrs->statnr as $statnr) {
                $statnrArray[trim($statnr->statnrnr)]+=1;
            }
            $tagcounter++;
            if ($tagcounter >= $maxTagen)
                break;
        }

        $statnrArray = array_keys($statnrArray);
        sort($statnrArray);
//        echo "<pre>".print_r($statnrArray)."</pre>";

        $summenArray = array();
        $gesamtSummenArray = array();
        $gesamtSummenArrayMonat = array();

        // vynuluju citace dnu pro jednotlive statnr
        foreach ($statnrArray as $statnr => $hodnota) {
            $priplatekDayCounter[$hodnota] = 0;
        }


        $maxTagen = 24;
        $tagcounter = 0;
        foreach ($person->tage->tag as $tag) {
            $hodnoty['stunden'][trim($tag->datum)] = round(floatval(trim($tag->stunden)), 1);
            $hodnoty['prasne'][trim($tag->datum)] = round(floatval(trim($tag->prasne)));
            if ($tagcounter == 0) {
                $monatAnfang = substr(trim($tag->datum), 5, 2);
                $datumAnfang = trim($tag->datum);
            }
            foreach ($tag->statnrs->statnr as $statnr) {
                $vzaby_min = floatval(trim($statnr->vzaby_min));
                $vzaby_min_kc = floatval(trim($statnr->vzaby_kc));
                $qualpremie_kc = floatval(trim($statnr->qualpraemie_kc));
                $bezpriplatku_min = floatval(trim($statnr->bezpriplatku_min));
                $bezpriplatku_kc = floatval(trim($statnr->bezpriplatku_kc));
                $statNr = trim($statnr->statnrnr);
                $datum = trim($tag->datum);
                if ($priplatekDpersDatumZuschlag !== NULL) {
                    if (array_key_exists($statNr, $priplatekDpersDatumZuschlag)) {
                        if (array_key_exists($datum, $priplatekDpersDatumZuschlag[$statNr])) {
                            $priplatekProzent = floatval($priplatekDpersDatumZuschlag[$statNr][$datum]);
                        }
                        else
                            $priplatekProzent = $priplatekArray[trim($statnr->statnrnr)][$priplatekDayCounter[trim($statnr->statnrnr)]];
                    }
                    else
                        $priplatekProzent = $priplatekArray[trim($statnr->statnrnr)][$priplatekDayCounter[trim($statnr->statnrnr)]];
                }
                else
                    $priplatekProzent = $priplatekArray[trim($statnr->statnrnr)][$priplatekDayCounter[trim($statnr->statnrnr)]];

                $vzabyPriplatek = $vzaby_min - $bezpriplatku_min;
                $vzabyPriplatek_kc = $vzaby_min_kc - $bezpriplatku_kc;
                $priplatek = $vzabyPriplatek * $priplatekProzent / 100;
                $priplatek_kc = $vzabyPriplatek_kc * $priplatekProzent / 100;
                $vzabyCelkem = $vzaby_min + $priplatek;
                $vzabyCelkem_kc = $vzaby_min_kc + $priplatek_kc;
                $hodnoty[trim($statnr->statnrnr)]['vzaby_min'][trim($tag->datum)] = round($vzaby_min);
                $hodnoty[trim($statnr->statnrnr)]['bezpriplatku'][trim($tag->datum)] = round($bezpriplatku_min);
                $hodnoty[trim($statnr->statnrnr)]['vzabypriplatek'][trim($tag->datum)] = round($vzabyPriplatek);
                $hodnoty[trim($statnr->statnrnr)]['priplatek_prozent'][trim($tag->datum)] = $priplatekProzent;
                $hodnoty[trim($statnr->statnrnr)]['priplatek'][trim($tag->datum)] = round($priplatek);
                $hodnoty[trim($statnr->statnrnr)]['vzaby_celkem'][trim($tag->datum)] = round($vzabyCelkem);
                $summenArray['vzaby_celkem_kc'][trim($tag->datum)] += $vzabyCelkem_kc;
                $gesamtSummenArray['vzaby_min_kc'][trim($tag->datum)] += $vzaby_min_kc;
                $gesamtSummenArray['vzaby_min'][trim($tag->datum)] += $vzaby_min;
                //budu nascitavat jen pro stejny mesic v datumu
                if (substr(trim($tag->datum), 5, 2) == $monatAnfang)
                    $gesamtSummenArrayMonat['vzaby_min'] += $vzaby_min;
                $gesamtSummenArray['qualpremie_kc'][trim($tag->datum)] += $qualpremie_kc;
                $gesamtSummenArray['priplatek_kc'][trim($tag->datum)] += $priplatek_kc;
                $gesamtSummenArray['vzaby_celkem_kc'][trim($tag->datum)] += $vzabyCelkem_kc;
                $priplatekDayCounter[trim($statnr->statnrnr)]+=1;
            }
            $gesamtSummenArray['stunden'][trim($tag->datum)] += floatval(trim($tag->stunden));

            // anwesenheit nascitam jen pro jeden mesic
            if (substr(trim($tag->datum), 5, 2) == $monatAnfang) {
                $gesamtSummenArrayMonat['stunden'] += floatval(trim($tag->stunden));
                $datumEnde = trim($tag->datum);
            }
            $gesamtSummenArray['prasne'][trim($tag->datum)] += floatval(trim($tag->prasne));
            $tagcounter++;
            if ($tagcounter >= $maxTagen)
                break;
        }

        $anwesenheitWidth = 40;
        $poleWidth = 8.5;
        $datumFontSize = 6;


// datumy
        datumyHeader($pdf, 5, $datumyArray);

// kreslime tabulku s detailama
        $pdf->SetFont("FreeSans", "B", 8);
        foreach ($statnrArray as $statNr) {
            if (array_key_exists($statNr, $hodnoty)) {
                if (test_pageoverflow_nonewpage($pdf, 5 * 7)) {
                    $pdf->AddPage();
                    personalInfoHeader($pdf, $person);
                    datumyHeader($pdf, 5, $datumyArray);
                }

                $pdf->SetFont("FreeSans", "B", 8);
                $pdf->Cell(0, 5, $statNr, '0', 1, 'L', 0);

                //priplatek procent
                statNrRadek($pdf, 5, 0, 'pripl %', 'priplatek_prozent', $datumyArray, $og, $statNr, $hodnoty);
                //vzaby_min
                statNrRadek($pdf, 5, 0, 'vzaby [min]', 'vzaby_min', $datumyArray, $og, $statNr, $hodnoty);
                //priplatek
                statNrRadek($pdf, 5, 0, 'priplatek [min]', 'priplatek', $datumyArray, $og, $statNr, $hodnoty);
                //bez priplatku
                statNrRadek($pdf, 5, 0, '5xxx-7xxx [min]', 'bezpriplatku', $datumyArray, $og, $statNr, $hodnoty);
                //vzaby_celkem
                statNrRadek($pdf, 5, 0, 'celkem VzAby [min]', 'vzaby_celkem', $datumyArray, $og, $statNr, $hodnoty);
            }
        }


        // sumy
        $pdf->AddPage();
        personalInfoHeader($pdf,$person);
        datumyHeader($pdf, 5, $datumyArray);
//sumy pro gesamtOG
   $pdf->SetFont("FreeSans", "B", 8);
   $pdf->Cell(0, 5, 'Gesamtsummen', '0', 1, 'L', 0);
    foreach ($datumyArray as $datum){
        //zaroven tady muzu spocitat leistungpremie
        $anwMin = $gesamtSummenArray['stunden'][$datum]*60;
        $abyMin = $gesamtSummenArray['vzaby_min'][$datum];
        if($anwMin!=0){
            $gesamtSummenArray['leist_faktor'][$datum] = $abyMin / $anwMin;
            if($gesamtSummenArray['leist_faktor'][$datum]>=1.16)
                $gesamtSummenArray['leist_prem'][$datum] = 150;
            else if($gesamtSummenArray['leist_faktor'][$datum]>=1)
                $gesamtSummenArray['leist_prem'][$datum] = 100;
            else if($gesamtSummenArray['leist_faktor'][$datum]>=0.83)
                $gesamtSummenArray['leist_prem'][$datum] = 50;
            else
                $gesamtSummenArray['leist_prem'][$datum] = 0;
        }
        else{
            $gesamtSummenArray['leist_prem'][$datum] = 0;
            $gesamtSummenArray['leist_faktor'][$datum] = 0;
        }
    }
    gesamtOGRadek($pdf,5,0,'Summe VzAby [min]', 'vzaby_min', $datumyArray,$gesamtSummenArray);
    gesamtOGRadek($pdf,5,1,'Summe AnwStunden [Std]', 'stunden', $datumyArray,$gesamtSummenArray);
    gesamtOGRadek($pdf,5,2,'Leistungsfaktor', 'leist_faktor', $datumyArray,$gesamtSummenArray);

    gesamtOGRadek($pdf,5,0,'Summe VzAby [CZK]', 'vzaby_min_kc', $datumyArray,$gesamtSummenArray);
    gesamtOGRadek($pdf,5,0,'Summe priplatek [CZK]', 'priplatek_kc', $datumyArray,$gesamtSummenArray);
    gesamtOGRadek($pdf,5,0,'Summe [CZK]', 'vzaby_celkem_kc', $datumyArray,$gesamtSummenArray);
    gesamtOGRadek($pdf,5,0,'Summe prasne [CZK]', 'prasne', $datumyArray,$gesamtSummenArray);
    gesamtOGRadek($pdf,5,0,'Summe QPraemie [CZK]', 'qualpremie_kc', $datumyArray,$gesamtSummenArray);
//    gesamtOGRadek($pdf,5,0,'Leistungspraemie [CZK]', 'leist_prem', $datumyArray,$gesamtSummenArray);
    $pdf->Ln();
    gesamtRadek($pdf,5,0,'Summe gesamt [CZK]',$datumyArray,$gesamtSummenArray);


    $pdf->Ln();
    $arbTage = $apl->getATageProPersnrBetweenDatums($persnr, $datumAnfang, $datumEnde, 1);
//    $pdf->Cell(0, 5, 'anfang '.$datumAnfang.' ende '.$datumEnde.' arbtage '.$arbTage, '0', 1,'L',0);
    if(($gesamtSummenArrayMonat['stunden'])!=0)
        $leistFaktorMonat = $gesamtSummenArrayMonat['vzaby_min'] / ($gesamtSummenArrayMonat['stunden']*60);
    else
        $leistFaktorMonat = 0;
    $pdf->Cell(0, 5, 'in Monat: '.$monatAnfang.' - '.$arbTage.' Arbeitstage gearbeitet.', '0', 1,'L',0);
    $pdf->Cell(20, 5, 'VzAby [min]: ', 'LRBT', 0,'L',0);
    $pdf->Cell(30, 5, number_format($gesamtSummenArrayMonat['vzaby_min'],0,',',' '), 'LRBT', 1,'R',0);
    $pdf->Cell(20, 5, 'Stunden [Std]: ', 'LRBT', 0,'L',0);
    $pdf->Cell(30, 5, number_format($gesamtSummenArrayMonat['stunden'],1,',',' '), 'LRBT', 1,'R',0);
    $pdf->Cell(20, 5, 'LeistFaktor: ', 'LRBT', 0,'L',0);
    $pdf->Cell(30, 5, number_format($leistFaktorMonat,2,',',' '), 'LRBT', 1,'R',0);

    if($leistFaktorMonat>=1.16)
        $leistPrem = 150*$arbTage;
    else if($leistFaktorMonat>=1)
        $leistPrem = 100*$arbTage;
    else if($leistFaktorMonat>=0.83)
        $leistPrem = 50*$arbTage;
    else
        $leistPrem = 0;
    $pdf->Cell(20, 5, 'LeistPraemie: ', 'LRBT', 0,'L',0);
    $pdf->Cell(30, 5, number_format($leistPrem,0,',',' '), 'LRBT', 1,'R',0);

    
    }
}


$pdf->Output();
//============================================================+
// END OF FILE                                                 
//============================================================+

