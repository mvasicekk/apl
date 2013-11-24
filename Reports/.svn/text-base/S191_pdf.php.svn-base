<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S191";
$doc_subject = "S191 Report";
$doc_keywords = "S191";

// necham si vygenerovat XML

$parameters=$_GET;
$persnr = trim($_GET['persnr']);


require_once('S191_xml.php');

// priplatek v procentech pro jednotlive dny
// bude se nacitat z db podle cisla persnr
//                       1.  2.  3. 4. 5. 6. 7. 8. 9. 10 11 12 13 14 15 16 17 18 19 20 21 22 23 24
$priplatekArray = array(100,100,100,50,50,50,50,50,50,50,30,30,30,30,30,30,30,20,20,20,20,20,20,20);

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
 * @param SimpleXMLElement $personen
 */
function personalInfoHeader($pdf,$personen){
        $pdf->SetFont("FreeSans", "B", 10);
        $pdf->SetFillColor(255,255,200,1);
        $pdf->Cell(15, 7, $personen->person->persnr, 'BTLR', 0, 'C', 1);
        $pdf->Cell(60, 7, $personen->person->name, 'BTLR', 0, 'C', 1);
        $pdf->Cell(60, 7, " Eintritt: ".substr($personen->person->eintritt,0,10), 'BTLR', 0, 'C', 1);
        $pdf->Ln();
}



function test_pageoverflow($pdf,$vyskaradku,$rgb,$datumArray,$priplatekArray)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdf->GetY()+$vyskaradku)>($pdf->getPageHeight()-$pdf->getBreakMargin()))
	{
		$pdf->AddPage();
                pageheaderMesice($pdf,$datumArray,$priplatekArray,$rgb,5);
//		pageheader($pdfobjekt,$cellhead,$vysradku,$datumvon);
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

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S191 MA Zuschläge Einarbeitung", $params);
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

$pdf->AddPage();
personalInfoHeader($pdf,$personen);

$ogArray = array();
// vytvorim si pole s datumama
$datumyArray = array();
$anwesenheitArray = array();
// omezim na maximalne 24 dnu
$maxTagen = 24;
$tagcounter = 0;
foreach($personen->person->tage->tag as $tag){
    array_push($datumyArray, $tag->datum);
    $anwesenheitArray[trim($tag->datum)] = $tag->stunden;
    foreach($tag->ogs->og as $og){
        $ogArray[trim($og->ognr)] += 1;
    }
    $tagcounter++;
    if($tagcounter>=$maxTagen) break;
}

$ogArray = array_keys($ogArray);
sort($ogArray);

// prochazim pole s og
foreach ($ogArray as $og){
    $cislodne = 0;
    foreach($datumyArray as $datum){
        $og = trim($og);
        $datum = trim($datum);
        
        $hodnoty[$og][$datum]['stunden'] = getMinuten($personen, $og, $datum, 'stunden');
        $hodnoty[$og][$datum]['qpraemie'] = getMinuten($personen, $og, $datum, 'qualpraemie');
        $hodnoty[$og][$datum]['erschwerniss'] = getMinuten($personen, $og, $datum, 'prasne');
        $hodnoty[$og][$datum]['lohnfaktor'] = getMinuten($personen, $og, $datum, 'lohnfaktor');
        $hodnoty[$og][$datum]['vzaby'] = getMinuten($personen, $og, $datum, 'vzaby');
        $hodnoty[$og][$datum]['7xxx5xxx'] = getMinuten($personen, $og, $datum, 'bezpriplatku');
        $hodnoty[$og][$datum]['vzabybezpriplatku'] = $hodnoty[$og][$datum]['vzaby'] - $hodnoty[$og][$datum]['7xxx5xxx'];
        $hodnoty[$og][$datum]['priplatek'] = $hodnoty[$og][$datum]['vzabybezpriplatku']*($priplatekArray[$cislodne]/100);

        if($hodnoty[$og][$datum]['vzaby']!=0)
            $koeficientQPraemie = $hodnoty[$og][$datum]['qpraemie'] / $hodnoty[$og][$datum]['vzaby'];
        else
            $koeficientQPraemie = 0;
        
        $hodnoty[$og][$datum]['qpraemiepriplatku'] = $hodnoty[$og][$datum]['priplatek'] * $koeficientQPraemie;

        
        $hodnoty[$og][$datum]['celkemvzaby'] = $hodnoty[$og][$datum]['priplatek']+$hodnoty[$og][$datum]['vzaby'];
        $hodnoty[$og][$datum]['celkempriplatekkc'] = $hodnoty[$og][$datum]['priplatek']*$hodnoty[$og][$datum]['lohnfaktor'];
        $hodnoty[$og][$datum]['celkemvzabybezpriplatkukc'] = $hodnoty[$og][$datum]['vzaby']*$hodnoty[$og][$datum]['lohnfaktor'];
        $hodnoty[$og][$datum]['celkemvzabykc'] = $hodnoty[$og][$datum]['celkemvzaby']*$hodnoty[$og][$datum]['lohnfaktor'];
        $hodnoty[$og][$datum]['qpraemiekc'] = $hodnoty[$og][$datum]['qpraemie'] + $hodnoty[$og][$datum]['qpraemiepriplatku'];

        $hodnoty[$og][$datum]['celkempriplatekkc'] = $hodnoty[$og][$datum]['priplatek']*$hodnoty[$og][$datum]['lohnfaktor'];
        $hodnoty[$og][$datum]['celkemvzabybezpriplatkukc'] = $hodnoty[$og][$datum]['vzaby']*$hodnoty[$og][$datum]['lohnfaktor'];

        if($hodnoty[$og][$datum]['stunden']!=0)
            $hodnoty[$og][$datum]['leistungsfaktor'] = $hodnoty[$og][$datum]['vzaby'] / ($hodnoty[$og][$datum]['stunden'] * 60);
        else
            $hodnoty[$og][$datum]['leistungsfaktor'] = 0;

        $cislodne++;
    }
}

pageheaderMesice($pdf,$datumyArray,$priplatekArray,array(255,255,0),5);

foreach ($ogArray as $og){

    test_pageoverflow($pdf,5+8*5,array(255,255,0),$datumyArray,$priplatekArray);

    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->Cell(0, 5, $og, '0', 1, 'L', 0);
    // anwesenheit
    echoOGZeile($pdf,5,'Anwesenheit [Std]', $hodnoty,$datumyArray, $og, 'stunden',1);

    // erschwerniss
    //echoOGZeile($pdf,5,'prasne [CZK]', $hodnoty,$datumyArray, $og, 'erschwerniss',0);

    // vzaby
    echoOGZeile($pdf,5,'VzAby [min]', $hodnoty,$datumyArray, $og, 'vzaby',0);

    // leistungsfaktor
    echoOGZeile($pdf,5,'Leistungsfaktor', $hodnoty,$datumyArray, $og, 'leistungsfaktor',2);

    // 7xxx5xxx
    echoOGZeile($pdf,5,'7xxx,5xxx [min]', $hodnoty,$datumyArray, $og, '7xxx5xxx',0);

    //vzabybezpriplatku
    echoOGZeile($pdf,5,'VzAby bez 7xxx,5xxx [min]', $hodnoty,$datumyArray, $og, 'vzabybezpriplatku',0);

    //priplatek
    echoOGZeile($pdf,5,'priplatek [min]', $hodnoty,$datumyArray, $og, 'priplatek',0);

    //vzaby celkem ( s priplatkem )
    echoOGZeile($pdf,5,'celkem VzAby [min]', $hodnoty,$datumyArray, $og, 'celkemvzaby',0);

    //qpraemie
//    echoOGZeile($pdf,5,'QPraemie [CZK]', $hodnoty,$datumyArray, $og, 'qpraemiekc',0);
}

$pdf->Ln();
// celkove soucty pro OG v korunach
foreach ($ogArray as $og){
    test_pageoverflow($pdf,5,array(255,255,0),$datumyArray,$priplatekArray);
    OGSummeCZK($pdf,5,$hodnoty,$datumyArray,$og);
}

// a uplne celkove soucty pres vsechny OG
foreach ($ogArray as $og){
    $og = trim($og);
    foreach ($datumyArray as $datum){
        $datum = trim($datum);
        $gesamtSumme[$datum]['stunden'] += $hodnoty[$og][$datum]['stunden'];
        $gesamtSumme[$datum]['vzaby'] += $hodnoty[$og][$datum]['vzaby'];
        $gesamtSumme[$datum]['priplatek'] += $hodnoty[$og][$datum]['priplatek'];
        $gesamtSumme[$datum]['celkemvzabykc'] += $hodnoty[$og][$datum]['celkemvzabykc'];
        $gesamtSumme[$datum]['erschwerniss'] += $hodnoty[$og][$datum]['erschwerniss'];
        $gesamtSumme[$datum]['qpraemiekc'] += $hodnoty[$og][$datum]['qpraemiekc'];
        $gesamtSumme[$datum]['celkempriplatekkc'] += $hodnoty[$og][$datum]['celkempriplatekkc'];
        $gesamtSumme[$datum]['celkemvzabybezpriplatkukc'] += $hodnoty[$og][$datum]['celkemvzabybezpriplatkukc'];
    }
}

// leistungsfaktor
foreach ($datumyArray as $datum){
    $datum = trim($datum);
    if($gesamtSumme[$datum]['stunden']!=0)
        $gesamtSumme[$datum]['leistungsfaktor'] = $gesamtSumme[$datum]['vzaby']/($gesamtSumme[$datum]['stunden']*60);
    else
        $gesamtSumme[$datum]['leistungsfaktor'] = 0;
    
    if($gesamtSumme[$datum]['leistungsfaktor']>=1.16)
        $gesamtSumme[$datum]['leistungspraemie'] = 150;
    else if($gesamtSumme[$datum]['leistungsfaktor']>=1)
            $gesamtSumme[$datum]['leistungspraemie'] = 100;
         else if($gesamtSumme[$datum]['leistungsfaktor']>=0.83)
                $gesamtSumme[$datum]['leistungspraemie'] = 50;
              else
                $gesamtSumme[$datum]['leistungspraemie'] = 0;
    $gesamtSumme[$datum]['gesamtsummekc'] = $gesamtSumme[$datum]['celkemvzabykc'] + $gesamtSumme[$datum]['erschwerniss']+$gesamtSumme[$datum]['qpraemiekc']+$gesamtSumme[$datum]['leistungspraemie'];
}

$pdf->Ln();
test_pageoverflow($pdf,12*5,array(255,255,0),$datumyArray,$priplatekArray);
echoGesamtSumme($pdf,5,'Summe AnwStunden [Std]', $datumyArray,$gesamtSumme, 'stunden',1);
//echoGesamtSumme($pdf,5,'Summe VzAby [min]', $datumyArray,$gesamtSumme, 'vzaby');
echoGesamtSumme($pdf,5,'Leistungsfaktor', $datumyArray,$gesamtSumme, 'leistungsfaktor',2);

//echoGesamtSumme($pdf,5,'Summe priplatek [min]', $datumyArray,$gesamtSumme, 'priplatek');
echoGesamtSumme($pdf,5,'Summe [CZK]', $datumyArray,$gesamtSumme, 'celkemvzabykc');
echoGesamtSumme($pdf,5,'Summe VzAby [CZK]', $datumyArray,$gesamtSumme, 'celkemvzabybezpriplatkukc');
echoGesamtSumme($pdf,5,'Summe priplatek [CZK]', $datumyArray,$gesamtSumme, 'celkempriplatekkc');
echoGesamtSumme($pdf,5,'Summe prasne [CZK]', $datumyArray,$gesamtSumme, 'erschwerniss');
echoGesamtSumme($pdf,5,'Summe QPraemie [CZK]', $datumyArray,$gesamtSumme, 'qpraemiekc');
echoGesamtSumme($pdf,5,'Leistungspraemie [CZK]', $datumyArray,$gesamtSumme, 'leistungspraemie');
$pdf->Ln();

//test_pageoverflow($pdf,5,array(255,255,0),$datumyArray,$priplatekArray);
echoGesamtSumme($pdf,5,'Summe gesamt [CZK]', $datumyArray,$gesamtSumme, 'gesamtsummekc');

$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
