<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S132";
$doc_subject = "S132 Report";
$doc_keywords = "S132";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$reporttyp = $_GET['reporttyp'];
$oe = $_GET['oe'];
$tagvon = $_GET['tagvon'];
$tagbis = $_GET['tagbis'];

if(!$tagvon) $tagvon = 1;
if(!$tagbis) $tagbis = 31;
// vymenim hvezdicky za procenta
//$oe = trim(str_replace('*', '%', $oe));

$oe = trim($_GET['oe']);

// v oe muze byt vice polozek oddelenych mezerama
$oeArray = split(' ', $oe);
if($oeArray==FALSE)
    $oeArray=NULL;

$apl = AplDB::getInstance();
$dbDatumVon = sprintf("%04d-%02d-%02d",$jahr,$monat,1);
$dbDatumBis = sprintf("%04d-%02d-%02d",$jahr,$monat,  cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));
$sollTageMonat = $apl->getArbTageBetweenDatums($dbDatumVon, $dbDatumBis);

$headerParams = sprintf("Zeitraum: %02d.%02d.%04d - %02d.%02d.%04d, PersNr: %d - %d, OE: %s / %d Solltage Monat"
                        ,$tagvon,$monat,$jahr,$tagbis,$monat,$jahr,$persvon,$persbis,$oe,$sollTageMonat);

$password = $_GET['password'];
$user = $_SESSION['user'];

$fullAccess = testReportPassword("S132",$password,$user);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}

define('SOLL', 1);
define('IST',2);
define('SOLLIST',3);

if($reporttyp=='soll')
    $typ = SOLL;
else if($reporttyp=='ist')
    $typ = IST;
else
    $typ = SOLLIST;

require_once('S132_xml.php');


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



global $oeFarbenArray;

$sum_zapati_persnr_array;
global $sum_zapati_persnr_array;

$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;


/**
 *
 * @param int $jahr
 * @param int $monat
 * @param DOMDocument $xml
 */
function naplnPolePracDoby($jahr,$monat,$xml){
    $calendarNodes = $xml->getElementsByTagName('calendar');
    $calendar = $calendarNodes->item(0);
    $tage = $calendar->getElementsByTagName('tag');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    foreach ($tage as $tag){
        $tagChilds = $tag->childNodes;
        $datum = getValueForNode($tagChilds,'datum');
        $den = intval(substr($datum, 8));
        $pole[$den]['von_f_guss']=getValueForNode($tagChilds,'vonfguss');
        $pole[$den]['bis_f_guss']=getValueForNode($tagChilds,'bisfguss');
        $pole[$den]['von_s_guss']=getValueForNode($tagChilds,'vonsguss');
        $pole[$den]['bis_s_guss']=getValueForNode($tagChilds,'bissguss');
        $pole[$den]['von_f_ne']=getValueForNode($tagChilds,'vonfne');
        $pole[$den]['bis_f_ne']=getValueForNode($tagChilds,'bisfne');
        $pole[$den]['von_s_ne']=getValueForNode($tagChilds,'vonsne');
        $pole[$den]['bis_s_ne']=getValueForNode($tagChilds,'bissne');
    }
    return $pole;
}


function naplnPoleSvatku($jahr,$monat) {
    dbConnect();
    $datvon = $jahr."-".$monat."-01";
    // get number of days in month
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $jahr."-".$monat."-".$pocetDnuVMesici;

    $sql = "select calendar.datum from calendar where calendar.svatek<>0 and calendar.datum between '$datvon' and '$datbis'";
    $result = mysql_query($sql);
    $i=0;
    $pole = array();
    while($row = mysql_fetch_assoc($result)) {
        $pole[$i++] = trim(substr($row['datum'],8,2));
    }
    return $pole;
}

function hatTagArbZeit($tag,$schicht_abteilung){
    $vonIndex = 'von_'.$schicht_abteilung;
    $bisIndex = 'bis_'.$schicht_abteilung;
    if((strlen($tag[$vonIndex])==0) &&(strlen($tag[$bisIndex])==0))
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
function pageheader($pdfobjekt,$pole,$headervyskaradku,$jahr,$monat,$svatky,$pracDobaA) {
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    //ZMENA 2011-02-22
    $pocetDnuVMesici=31;

    $days = array("So","Mo","Di","Mi","Do","Fr","Sa");

    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->SetFillColor(255,255,200,1);
    $fill = 1;
//    $pdfobjekt->Cell(10,5,"OE",'1',0,'L',$fill);
    $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

    $markActual = false;
    $actualMonat = date('m');
    if($actualMonat==$monat) $markActual = true;


    $pocetDnuVMesiciAktual = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    // prac doba guss fruh
    $pdfobjekt->Cell(10+3,3,"GFxx",'LRT',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", 3.5);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {

        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        $hasArbeit = !hatTagArbZeit($pracDobaA[$den], "f_guss");
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$den]['von_f_guss'].'-'.$pracDobaA[$den]['bis_f_guss'],'LRT',0,'R',$fill);
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
        }
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell(0,3,"",'LRT',1,'R',$fill);

    // prac doba guss spat
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->Cell(10+3,3,"GSxx",'LRB',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", 3.5);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {

        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        $hasArbeit = !hatTagArbZeit($pracDobaA[$den], "s_guss");
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$den]['von_s_guss'].'-'.$pracDobaA[$den]['bis_s_guss'],'LRB',0,'R',$fill);
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
        }
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell(0,3,"",'LRB',1,'R',$fill);

    // prac doba ne fruh
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->Cell(10+3,3,"NFxx",'LRT',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", 3.5);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {

        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        $hasArbeit = !hatTagArbZeit($pracDobaA[$den], "f_ne");
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$den]['von_f_ne'].'-'.$pracDobaA[$den]['bis_f_ne'],'LRT',0,'R',$fill);
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
        }
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell(0,3,"",'LRT',1,'R',$fill);

    // prac doba guss spat
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->Cell(10+3,3,"NSxx",'LRB',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", 3.5);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {

        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        $hasArbeit = !hatTagArbZeit($pracDobaA[$den], "s_ne");
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$den]['von_s_ne'].'-'.$pracDobaA[$den]['bis_s_ne'],'LRB',0,'R',$fill);
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
        }
        if($workday==6 || $workday==0 || in_array($den, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell(0,3,"",'LRB',1,'R',$fill);


    // cisla dnu
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->Cell(10+3,5,"",'1',0,'L',$fill);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {


        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        if($workday==6 || $workday==0 || in_array($den, $svatky))
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        if($den>$pocetDnuVMesiciAktual)
            $pdfobjekt->Cell($sirkabunky,5,'','1',0,'R',$fill);
        else
            $pdfobjekt->Cell($sirkabunky,5,$den,'1',0,'R',$fill);
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
        }
        if($workday==6 || $workday==0 || in_array($den, $svatky))
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell(0,5,"Sum",'1',1,'R',$fill);

    // popisky dnu
    $pdfobjekt->Cell(10+3,5,"OE",'1',0,'L',$fill);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        if($workday==6 || $workday==0  || in_array($den, $svatky))
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,5,$days[$workday],'1',0,'R',$fill);
        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
        }
        if($workday==6 || $workday==0  || in_array($den, $svatky))
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell(0,5,"",'1',1,'R',$fill);


//    $pdfobjekt->Ln();
    $pdfobjekt->Cell(0,1,"",'0',1,'0',0);
    $pdfobjekt->SetFont("FreeSans", "", 8);
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

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $sumArray
 * @param <type> $typ
 * @param <type> $monat
 * @param <type> $jahr
 * @param <type> $svatky
 */
function zapati_oes($oefarbenArray,$pdfobjekt,$vyskaradku,$rgb,$sumArray,$typ,$monat, $jahr,$svatky,$tagvon,$tagbis){

        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $aktualniDen = date('d');
        $pocetDnuVMesiciAktual = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $pocetDnuVMesici = 31;

        // seradim podle og
        if($sumArray!==NULL && count($sumArray)>0) ksort($sumArray);

        if($sumArray!==NULL && count($sumArray)>0){
        foreach($sumArray as $og=>$oes) {
            // seradim podle oe
            ksort($oes);
            foreach ($oes as $kz=>$sArray) {
                test_pageoverflow($pdfobjekt, $vyskaradku,"",$jahr,$monat,$svatky,$pracDobaA);
                $pdfobjekt->SetFont("FreeSans", "B", 6);
                $oergbStr = $oefarbenArray[$kz];
                $oergb = split(",", $oergbStr);
                $pdfobjekt->SetFillColor($oergb[0],$oergb[1],$oergb[2],1);
                $pdfobjekt->Cell(10,$vyskaradku,$kz,'1',0,'L',$fill);
                $pdfobjekt->Cell(3,$vyskaradku,$typ,'1',0,'R',$fill);
                $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
                $sumaCelkem = 0;

                $markActual = false;
                $actualMonat = date('m');
                if($actualMonat==$monat) $markActual = true;

                for($i=1;$i<=$pocetDnuVMesici;$i++) {
                    if($i>=$tagvon && $i<=$tagbis) {
                        if($aktualniDen==$i && $markActual) {
                            $pdfobjekt->SetLineWidth(0.5);
                            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
                        }
                        $workday = date('w',mktime(1, 1, 1, $monat, $i, $jahr));
                        if($workday==6 || $workday==0 || in_array($i, $svatky))
                            $pdfobjekt->SetFillColor(245, 245, 255,1);
                        $stunden = $sArray[$i];
                        $sumaCelkem += $stunden;
                        $stunden = number_format($stunden, 1);
                        $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

                        if($aktualniDen==$i && $markActual) {
                            $pdfobjekt->SetLineWidth(0.2);
                            $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
                        }
                        if($workday==6 || $workday==0 || in_array($i, $svatky))
                            $pdfobjekt->SetFillColor($oergb[0],$oergb[1],$oergb[2],1);
//                     $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                    }
                    else {
                        $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
                    }
                }

                $sumaCelkem = number_format($sumaCelkem, 1);
                $pdfobjekt->Cell(0,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

            }
        }
        }
}
/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $sumArray
 * @param <type> $typ
 * @param <type> $monat
 * @param <type> $jahr
 */
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$sumArray,$typ, $monat, $jahr,$svatky,$tagvon,$tagbis){
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $aktualniDen = date('d');
        $pocetDnuVMesiciAktual = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $pocetDnuVMesici = 31;

        $pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->Cell(10,$vyskaradku,"Sum",'1',0,'L',$fill);
        $pdfobjekt->Cell(3,$vyskaradku,$typ,'1',0,'R',$fill);
//        $pdfobjekt->Cell(0,$vyskaradku,$jmeno,'1',1,'L',$fill);
        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
        $sumaCelkem = 0;

        $markActual = false;
        $actualMonat = date('m');
        if($actualMonat==$monat) $markActual = true;


        for($i=1;$i<=$pocetDnuVMesici;$i++){
            if($i>=$tagvon && $i<=$tagbis){
            if($aktualniDen==$i && $markActual){
                $pdfobjekt->SetLineWidth(0.5);
                $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
            }
            $workday = date('w',mktime(1, 1, 1, $monat, $i, $jahr));
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                 $pdfobjekt->SetFillColor(245, 245, 255,1);

            $stunden = $sumArray[$i];
            $sumaCelkem += $stunden;
            $stunden = number_format($stunden, 1);
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

            if($aktualniDen==$i && $markActual){
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                 $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }
            else{
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
            }
        }

        $sumaCelkem = number_format($sumaCelkem, 1);
        $pdfobjekt->Cell(0,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $persnr
 * @param <type> $typ
 * @param <type> $sumArray
 * @param <type> $monat
 * @param <type> $jahr
 */
function zapati_personA($pdfobjekt,$vyskaradku,$rgb,$persnr,$typ,$sumArray,$monat,$jahr,$svatky,$tagvon,$tagbis){

        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $aktualniDen = date('d');
        $pocetDnuVMesiciAktual = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $pocetDnuVMesici = 31;

        $pdfobjekt->SetFont("FreeSans", "IB", 7);
	$pdfobjekt->Cell(10,$vyskaradku,$persnr,'1',0,'L',$fill);
        $pdfobjekt->Cell(3,$vyskaradku,$typ,'1',0,'R',$fill);
        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
        $sumaCelkem = 0;

        $markActual = false;
        $actualMonat = date('m');
        if($actualMonat==$monat) $markActual = true;

        for($i=1;$i<=$pocetDnuVMesici;$i++){
            if($i>=$tagvon && $i<=$tagbis){
            if($aktualniDen==$i && $markActual){
                $pdfobjekt->SetLineWidth(0.5);
                $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
            }
            $workday = date('w',mktime(1, 1, 1, $monat, $i, $jahr));
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                 $pdfobjekt->SetFillColor(245, 245, 255,1);

            $stunden = $sumArray[$i];
            $sumaCelkem += $stunden;
            $stunden = number_format($stunden, 1);
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

            if($aktualniDen==$i && $markActual){
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                 $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }
            else{
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
            }
        }

        $sumaCelkem = number_format($sumaCelkem, 1);
        $pdfobjekt->Cell(0,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function getArbTageBetweenDatums($dbDatumVon,$dbDatumBis) {
    $sql = "select count(calendar.datum) as worktage from calendar where svatek=0 and datum>'$dbDatumVon' and datum<='$dbDatumBis' and cislodne<>6 and cislodne<>7";
    dbConnect();
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    return $row['worktage'];
}

function getRegelarbzeit($persnr) {
    $sql = "select dpers.regelarbzeit from dpers where persnr='$persnr'";
    dbConnect();
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);
    return $row['regelarbzeit'];
}

function getNotATageCountBetweenDatums($dbDatumVon,$dbDatumBis,$persnr) {
    $sql = "select dtattypen.tat,count(datum) as tage";
    $sql.=" from dzeit ";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.=" where datum between '$dbDatumVon' and '$dbDatumBis' and persnr='$persnr' and dtattypen.oestatus<>'a'";
    $sql.=" group by persnr,dtattypen.tat";
    dbConnect();
    $res = mysql_query($sql);
    $tageArray = array();
    while($row = mysql_fetch_assoc($res)){
        $tageArray[$row['tat']] = $row['tage'];
    }
    return $tageArray;
}


function getATageCountBetweenDatums($dbDatumVon,$dbDatumBis,$persnr) {
    $sql = "select persnr,dtattypen.oestatus,datum";
    $sql.=" from dzeit ";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.=" where datum between '$dbDatumVon' and '$dbDatumBis' and persnr='$persnr' and dtattypen.oestatus='a'";
    $sql.=" group by persnr,dtattypen.oestatus,dzeit.datum";
    dbConnect();
    $res = mysql_query($sql);
    return mysql_num_rows($res);
}


function getNoArbTageBetweenDatums($dbDatumVon,$dbDatumBis,$persnr) {
//    $sql = "select count(dzeit.datum) as noworktage from dzeit join dtattypen on dzeit.tat=dtattypen.tat where datum>'$dbDatumVon' and datum<='$dbDatumBis' and persnr='$persnr' and (dzeit.tat='d' or dzeit.tat='nv' or dtattypen.oestatus='n')";
// d nebudu odecitat od fondu prac hodin
    $sql = "select count(dzeit.datum) as noworktage from dzeit join dtattypen on dzeit.tat=dtattypen.tat where datum>'$dbDatumVon' and datum<='$dbDatumBis' and persnr='$persnr' and (dzeit.tat='nv' or dtattypen.oestatus='n')";
    dbConnect();
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);

    return $row['noworktage'];
}



function getNoArbTagePlanBetweenDatums($dbDatumVon,$dbDatumBis,$persnr) {
    $sql = "select count(dzeitsoll.datum) as noworktage from dzeitsoll where datum>'$dbDatumVon' and datum<='$dbDatumBis' and persnr='$persnr' and (oe='d' or oe='nv')";
    dbConnect();
    $res = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($res);
        return $row['noworktage'];
    }
    else
        return 0;
}

function getSollStundenLautCalendar($rok,$mesic,$stundenProTag) {
//1. get number of workday in month
    $jahr = $rok;
    $monat = $mesic;
    $datvon = $jahr."-".$monat."-01";
    // get number of days in month
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $jahr."-".$monat."-".$pocetDnuVMesici;

    $sql = "select count(datum) as workdays from calendar where calendar.cislodne<6 and calendar.svatek=0 and datum between '$datvon' and '$datbis'";
    dbConnect();
    $result = mysql_query($sql);
    $row = mysql_fetch_assoc($result);
    $workDays = $row['workdays'];
    $sollStunden = $workDays * $stundenProTag;
    return array("arbtage"=>$workDays,"sollstunden"=>$sollStunden);
}


function getUrlaubTageInMonatSoll($persnr,$monat,$jahr) {
    dbConnect();
    $datvon = $jahr."-".$monat."-01";
    // get number of days in month
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $jahr."-".$monat."-".$pocetDnuVMesici;

    $aktualDatum = date('Y-m-d');
    $aktualJahr = date('Y');
    $aktualTag = date('d');
    $aktualMonat = date('m');

    if($monat==$aktualMonat && $jahr==$aktualJahr){
        // zajime me aktualni mesic, tj. vratim naplnovane dny od aktualniho dne vcetne az do konce mesice tj. datbis
        $sql = "select count(dzeitsoll.datum) as urlaubtage from dzeitsoll where persnr='$persnr' and datum between '$aktualDatum' and '$datbis' and oe='d'";
    }
    else if(($jahr*12+$monat)<($aktualJahr*12+$aktualMonat)){
        // zajima me predchozi mesic vratim nulu, protoze uz ma mit vyplneno dzeit se skutecne vybranymi dny
        return 0;
    }
    else{
        // zajima me nasleduji mesic takze vratim vsechny naplanovane dovolene mezi datvon a datbis
        $sql = "select count(dzeitsoll.datum) as urlaubtage from dzeitsoll where persnr='$persnr' and datum between '$datvon' and '$datbis' and oe='d'";
    }

//    $sql = "select count(dzeitsoll.datum) as urlaubtage from dzeitsoll where persnr='$persnr' and datum between '$datvon' and '$datbis' and oe='d'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        $urlaubDays = $row['urlaubtage'];
    }
    else
        $urlaubDays = 0;

    return $urlaubDays;

}

function getUrlaubTageInMonatIst($persnr,$monat,$jahr) {
    dbConnect();
    $datvon = $jahr."-".$monat."-01";
    // get number of days in month
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $jahr."-".$monat."-".$pocetDnuVMesici;

    $aktualDatum = date('Y-m-d');
    $aktualJahr = date('Y');
    $aktualTag = date('d');
    $aktualMonat = date('m');
    if($monat==$aktualMonat && $jahr==$aktualJahr){
        // zajime me aktualni mesic, tj. vratim skutecne vybrane dny do aktualniho dne
        $sql = "select count(dzeit.datum) as urlaubtage from dzeit where persnr='$persnr' and datum>='$datvon' and datum<'$aktualDatum' and tat='d'";
    }
    else if(($jahr*12+$monat)<($aktualJahr*12+$aktualMonat)){
        // zajima me predchozi mesic muzi vzit od prvniho dne az do datbis ( tj. konec mesice )
        $sql = "select count(dzeit.datum) as urlaubtage from dzeit where persnr='$persnr' and datum between '$datvon' and '$datbis' and tat='d'";
    }
    else{
        // zajima me nasleduji mesic, v budoucnu si jeste nemohl skutecne nic vybrat, takze rovnou vratim 0
        // POZOR - kdyby nekdo napred zadal dovolenou do tabulky dzeit, tak stejne vratim nulu !!!!
        return 0;
    }
//    $sql = "select count(dzeit.datum) as urlaubtage from dzeit where persnr='$persnr' and datum between '$datvon' and '$datbis' and tat='d'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        $urlaubDays = $row['urlaubtage'];
    }
    else
        $urlaubDays = 0;

    return $urlaubDays;

}

/**
 * gives info about holiday for persnr
 * @param integer $persnr
 * @param string $bisDatum in form of YYYY-MM-DD
 *
 * @return array('rest'=>$rest,'anspruch'=>$anspruch,'alt'=>$alt,'gekrzt'=>$gekrzt,'genommen'=>$genommenBis)
 */
function getUrlaubBisDatum($persnr,$bisDatum) {
    dbConnect();
    $sql = "select durlaub1.jahranspruch,durlaub1.rest,durlaub1.gekrzt from durlaub1 where `PersNr`='$persnr'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
    // should be only 1 row
        $row = mysql_fetch_assoc($result);
        $anspruch = $row['jahranspruch'];
        $alt = $row['rest'];
        $gekrzt = $row['gekrzt'];
    }
    else {
        $anspruch = 0;
        $rest = 0;
        $alt = 0;
        $gekrzt = 0;
    }

    // holiday day from begin of years to $bisDatum
    $aplDB = AplDB::getInstance();
    // genommen skutecne vybrana dovolena
    $genommenBis = $aplDB->getUrlaubtageGenommenBis($persnr, $bisDatum);

    // genommen naplanovana od aktualniho datumu do $bisDatum
    $nowDBDate = date('Y-m-d');
    $genommenBisSoll = $aplDB->getUrlaubtageGenommenBisSoll($persnr, $nowDBDate,$bisDatum);

    $rest = $anspruch + $alt + $gekrzt - $genommenBis - $genommenBisSoll;

    return array('rest'=>$rest,'anspruch'=>$anspruch,'alt'=>$alt,'gekrzt'=>$gekrzt,'genommen'=>$genommenBis);
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $persnr
 * @param <type> $nameArray
 */
function zahlavi_personA($pdfobjekt,$vyskaradku,$rgb,$persnr,$nameArray,$persInfoArray,$monat,$jahr,$typ,$sumsollArray,$fullAccess){
    	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $pocetDnuVMesiciAktual = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $pocetDnuVMesici = 31;
        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

        $name = $nameArray['name'];
        $vorname = $nameArray['vorname'];
        
        $jmeno = $vorname." ".$name;
//        $monatJahr = $monat."/".$jahr;
        $monatJahr = sprintf("%02d/%04d",$monat,$jahr);
        $monat = $monat*1;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(10,$vyskaradku,$persnr,'1',0,'L',$fill);
        $pdfobjekt->Cell(3+4*$sirkabunky,$vyskaradku,$jmeno,'1',0,'L',$fill);

        if($typ==1 || $typ==3 || $typ==2){

            $EndemonatJahr = sprintf("%s%02d%02d",substr($jahr,2),$monat,$pocetDnuVMesiciAktual);

            // vormonat und Jahr
            if($monat==1){
                $vormonat = 12;
                $vorjahr = $jahr-1;
            }
            else{
                $vormonat = $monat-1;
                $vorjahr = $jahr;
            }
            $vormonatJahr = $vormonat."/".$vorjahr;
            $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);
//            $EndevormonatJahr = substr($vorjahr,2)."/".$vormonat."/".$pocetDnuVMesici;

            $EndevormonatJahr = sprintf("%s%02d%02d",substr($vorjahr,2),$vormonat,$pocetDnuVMesici);

            $sumaSoll = 0;

    //        $stdsoll_datum = $persInfoArray['stdsoll_datum'];
            $apl = AplDB::getInstance();

            $regelarbZeit = $apl->getRegelarbeitDatum($monat, $jahr, $persnr);
            if($regelarbZeit===NULL) $regelarbZeit = $persInfoArray['regelarbzeit'];

            // vypocet datumu pro pripad nastupu v prubehu mesice
            // nebudu brat cely mesic ale jen cast kdy uz nastoupil
            //$stdsoll_datum = $persInfoArray['arbtage'] * $persInfoArray['regelarbzeit'];
            $dbDatumVon = sprintf("%04d-%02d-%02d",substr($persInfoArray['eintritt'], 0, 4),substr($persInfoArray['eintritt'], 5, 2),substr($persInfoArray['eintritt'], 8, 2));
            $dbDatumBis = sprintf("%04d-%02d-%02d",$jahr,$monat,  cal_days_in_month(CAL_GREGORIAN, $monat, $jahr));
            //echo "eintritt:".$persInfoArray['eintritt']." dbDatumVon $dbDatumVon dbDatumBis $dbDatumBis";
            $sollTageMonat = $apl->getArbTageBetweenDatums($dbDatumVon, $dbDatumBis);

            // pokud nastoupi v prubehu mesice omezim pocatecni datum mesice na datum nastupu
            if($sollTageMonat<$persInfoArray['arbtage'])
                $stdsoll_datum = $sollTageMonat * $regelarbZeit;
            else
                $stdsoll_datum = $persInfoArray['arbtage'] * $regelarbZeit;

            //TODO do aktualniho datumu brat urlaubtage ist, od aktualniho datumu do datbis brat urlaubtagesoll

            $persInfoArray['rest'] = number_format($persInfoArray['rest'],1);

            $urlaubRestBisEndeMonat = $persInfoArray['rest'] - $persInfoArray['urlaubtageist']  - $persInfoArray['urlaubtagesoll'];

            $urlaubRestBisEndeMonat = number_format($urlaubRestBisEndeMonat,1);
            
            if(is_array($sumsollArray))
                foreach($sumsollArray as $stunden) $sumaSoll += $stunden;
            else
                $sumaSoll = 0;

            $prozentSoll = 0;
            if($stdsoll_datum!=0){
                $prozentSoll = $sumaSoll / $stdsoll_datum * 100;
                $prozentSoll = round($prozentSoll);
            }

            if($fullAccess) {
                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $pdfobjekt->Cell(3*$sirkabunky,$vyskaradku,substr($persInfoArray['komm_ort'],0,30),'RTB',0,'L',$fill);

                $pdfobjekt->SetFont("FreeSans", "B", 7);
                //$pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"Std/Tag: ".substr($persInfoArray['regelarbzeit'],0,30),'LTBR',0,'L',$fill);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"Std/Tag: ".substr($regelarbZeit,0,30),'LTBR',0,'L',$fill);
//                $pdfobjekt->SetFont("FreeSans", "B", 7);
//                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,substr($persInfoArray['regelarbzeit'],0,30),'RTB',0,'R',$fill);

                // eintrittsdatum
                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,"Eintr.:",'LTB',0,'L',$fill);
                $pdfobjekt->SetFont("FreeSans", "B", 6);
                $obsah = substr($persInfoArray['eintritt'],0,10);
                $obsah = substr($obsah, 2, 2).substr($obsah, 5, 2).substr($obsah, 8, 2);
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,"" .$obsah. "",'TBR',0,'R',$fill);

                //doba urcita
                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,"befr.:",'LTB',0,'L',$fill);
                $pdfobjekt->SetFont("FreeSans", "B", 6);
                $obsah = substr($persInfoArray['dobaurcita'],0,10);
                $obsah = substr($obsah, 2, 2).substr($obsah, 5, 2).substr($obsah, 8, 2);
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,"" .$obsah. "",'TBR',0,'R',$fill);

                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"Plan 090420:",'LTB',0,'L',$fill);
                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,substr($stdsoll_datum,0,30),'RTB',0,'R',$fill);

                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"Soll $monatJahr:",'LTB',0,'L',$fill);
                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,$sumaSoll,'RTB',0,'R',$fill);

//                $pdfobjekt->SetFont("FreeSans", "", 7);
//                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"Soll % Plan",'LTB',0,'L',$fill);
//                $pdfobjekt->SetFont("FreeSans", "B", 7);
//                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,$prozentSoll,'RTB',0,'R',$fill);

                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"d $EndevormonatJahr",'LTB',0,'L',$fill);
                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $obsah = number_format($persInfoArray['rest'],1,',',' ');
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,$obsah,'RTB',0,'R',$fill);

                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,"d $EndemonatJahr",'LTB',0,'L',$fill);
                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $obsah = number_format($urlaubRestBisEndeMonat,1,',',' ');
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,$obsah,'RTB',0,'R',$fill);

                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,'+-Std'.$EndevormonatJahr,'LTB',0,'L',$fill);

                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $obsah = number_format($persInfoArray['plusminusstundenvor'],1,',',' ');
                $pdfobjekt->Cell(1*$sirkabunky,$vyskaradku,$obsah,'RTB',0,'R',$fill);

                $pdfobjekt->SetFont("FreeSans", "", 7);
                $pdfobjekt->Cell(2*$sirkabunky,$vyskaradku,'+-Std'.$EndemonatJahr,'LTB',0,'L',$fill);

                $pdfobjekt->SetFont("FreeSans", "B", 7);
                $obsah = number_format($persInfoArray['plusminusstunden'],1,',',' ');
                $pdfobjekt->Cell(0,$vyskaradku,$obsah,'RTB',0,'R',$fill);

            }
        }

        $pdfobjekt->Cell(0,$vyskaradku,"",'1',1,'L',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
function oe_radekA($oefarbenArray,$pdfobjekt,$vyskaradku,$rgb,$datumy,$oekz,$og,$typ,$ityp,$monat,$jahr,$svatky,$tagvon,$tagbis){

        // otestovat jestli mam pro dane $oekz v danem rozmezi $tagvon a $tagbis neco
            if($ityp==3)
                $kresliRadek = true;
            else
                $kresliRadek = false;

                
            for($den=$tagvon;$den<=$tagbis;$den++) {
                if(is_array($datumy)) {
                    foreach($datumy as $dat=>$stunden) {
                        $tag = intval(substr($dat, 8));
                        if($tag==$den) {
                            $kresliRadek = true;
                            break;
                        }
                    }
                    if($kresliRadek) break;
                }
                else
                    break;
            }

        if(!$kresliRadek) return;

        $oeRGBArray = split(",",$oefarbenArray);

        $fill = 1;
        $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
        $pdfobjekt->SetFont("FreeSans", "B", 8);
//        $pdfobjekt->Cell(10,$vyskaradku,$oekz."(".$og.")",'1',0,'L',$fill);
        $pdfobjekt->Cell(10,$vyskaradku,$oekz,'1',0,'L',$fill);
        $pdfobjekt->Cell(3,$vyskaradku,$typ,'1',0,'L',$fill);
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

        $aktualniDen = date('d');
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        //ZMENA 2011-02-22
        $pocetDnuVMesici = 31;

        $markActual = false;
        $actualMonat = date('m');
        if($actualMonat==$monat) $markActual = true;

        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $sumaHodin = 0;

        for($den=1;$den<=$pocetDnuVMesici;$den++) {
            if(($den>=$tagvon) && ($den<=$tagbis)) {
                // oznaceni aktualniho dne
                if($aktualniDen==$den && $markActual) {
                    $pdfobjekt->SetLineWidth(0.5);
                    $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
                }

                // oznaceni so+ne a svatku
                $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
                if($workday==6 || $workday==0 || in_array($den, $svatky))
                    $pdfobjekt->SetFillColor(245, 245, 255,1);

                $kresliPrazdny = 1;
                if(is_array($datumy)) {
                    foreach($datumy as $dat=>$stunden) {
                        $tag = intval(substr($dat, 8));
                        if($tag==$den) {
                            if($stunden==0) {
                                $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
                                $stunden = number_format($stunden, 1);
                                $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);
                                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                            }
                            else {
                                $sumaHodin += $stunden;
                                $stunden = number_format($stunden, 1);
                                $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
                                $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);
                                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                            }
                            $kresliPrazdny=0;
                            break;
                        }
                    }
                }

                if($workday==6 || $workday==0 || in_array($den, $svatky))
                    $pdfobjekt->SetFillColor(245, 245, 255,1);

                if($kresliPrazdny) {
                    $pdfobjekt->Cell($sirkabunky,$vyskaradku,"",'1',0,'R',$fill);
                }

                if($aktualniDen==$den && $markActual) {
                    $pdfobjekt->SetLineWidth(0.2);
                    $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
                }

                if($workday==6 || $workday==0 || in_array($den, $svatky))
                    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }
            else{
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
            }
        }
        // suma hodin pro danou cinnost
        $sumaHodin = number_format($sumaHodin, 1);
        $pdfobjekt->Cell(0,$vyskaradku,$sumaHodin,'1',0,'R',$fill);
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
function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$jahr,$monat,$svatky,$pracDoba)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5,$jahr,$monat,$svatky,$pracDoba);
	}
}
				

/**
 *
 * @param <type> $nodesArray
 * @param <type> $persnr
 * @return array
 */
function getPersonalInfo($nodesArray,$persnr){
    $vystup = array(
                        "komm_ort"=>"",
                        "regelarbzeit"=>"",
                        "eintritt"=>"",
                        "stdsoll_datum"=>"",
                        "dobaurcita"=>"",
                        "MAStunden"=>1,
                    );

    // hledam persnr
    foreach($nodesArray as $node){
        $nodeChilds = $node->childNodes;
        $persnr1 = getValueForNode($nodeChilds,'persnr');
        if($persnr1==$persnr){
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

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

if($reporttyp=='sollist') $rt = 'Soll Ist';
else
    if($reporttyp=='ist') $rt = 'Ist';
    else
        $rt = 'Soll';

//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S132 Personal Plan / Anwesenheit"." ( ".$rt." )", $params);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S132 Personal Plan / Anwesenheit"." ( ".$rt." )", $headerParams);
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


$persInfoArray = $domxml->getElementsByTagName('persinfo');

// vytahnu si oefarben
$farben = $domxml->getElementsByTagName('farbe');
foreach($farben as $farbe){
    $farbeChilds = $farbe->childNodes;
    $key = getValueForNode($farbeChilds, 'oe');
    $value = getValueForNode($farbeChilds, 'rgb');
    $oeFarbenArray[$key] = $value;
}

$reportArray = array();

if($typ==1) $sollIstArray = array("soll");
else if($typ==2) $sollIstArray = array("ist");
else $sollIstArray = array("soll","ist");


foreach($sollIstArray as $sollIst){
    $planTree = $domxml->getElementsByTagName($sollIst)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen=$planTree->getElementsByTagName("pers");
    foreach($personen as $person){
        $personChilds = $person->childNodes;
        $persnr = getValueForNode($personChilds, "persnr");
        $name = getValueForNode($personChilds, "name");
        $vorname = getValueForNode($personChilds, "vorname");
        $reportNameArray[$persnr] = array('name'=>$name,'vorname'=>$vorname);

        $oes = $person->getElementsByTagName("oe");
        foreach($oes as $oe){
            $oeChilds = $oe->childNodes;
            $oekz = getValueForNode($oeChilds, "oekz");
            $og = getValueForNode($oeChilds, "og");
            $tage = $oe->getElementsByTagName("tag");
            foreach($tage as $tag){
                $tagChilds = $tag->childNodes;
                $datum = getValueForNode($tagChilds, "datum");
                $stunden = getValueForNode($tagChilds, "stunden");
                $reportArray[$persnr][$og][$oekz][$sollIst][$datum]=$stunden;
            }
            // setridit dosavadni pole podle oe
            ksort($reportArray[$persnr][$og]);
        }
        // setridit pole podle og
        ksort($reportArray[$persnr]);
        
    }
}

// seradit podle persnr
ksort($reportArray);


//echo "<pre>";
//print_r($reportArray);
//echo "</pre>";

// spocitam sumu pro jednotlive osoby
foreach ($sollIstArray as $sollist){
    $planTree = $domxml->getElementsByTagName($sollist)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen=$planTree->getElementsByTagName("pers");
    foreach($personen as $person){
         $persChilds = $person->childNodes;
         $persnr = getValueForNode($persChilds, "persnr");
         for($den=1;$den<=31;$den++){

                $datumy=$person->getElementsByTagName("tag");
                foreach($datumy as $datum){
                    $datumChilds = $datum->childNodes;
                    $dat = getValueForNode($datumChilds, "datum");
                    $tag = intval(substr($dat, 8));
                    $stunden = getValueForNode($datumChilds, "stunden");
                    if($tag==$den){
                        $sum_zapati_persnr_array[$sollist][$persnr][$den] += $stunden;
                        $sum_zapati_sestava_array[$sollist][$den] += $stunden;
                    }
                    else{
                        $sum_zapati_persnr_array[$sollist][$persnr][$den] += 0;
                        $sum_zapati_sestava_array[$sollist][$den] += 0;
                    }
                }
         }
    }
}

//print_r($sum_zapati_persnr_array);

// spocitam sumy pro oe
foreach($sollIstArray as $sollist){
    $planTree = $domxml->getElementsByTagName($sollist)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $oes=$planTree->getElementsByTagName("oe");
    foreach ($oes as $oe){
        $oeChilds = $oe->childNodes;
        $oekz = getValueForNode($oeChilds, 'oekz');
        $og = getValueForNode($oeChilds, 'og');
        for($den=1;$den<=31;$den++){
            $datumy=$oe->getElementsByTagName("tag");
            foreach($datumy as $datum){
                $datumChilds = $datum->childNodes;
                $dat = getValueForNode($datumChilds, "datum");
                $tag = intval(substr($dat, 8));
                $stunden = getValueForNode($datumChilds, "stunden");
                if($tag==$den){
                    $sum_oe_array[$sollist][$og][$oekz][$den] += $stunden;
                }
                else{
                    $sum_oe_array[$sollist][$og][$oekz][$den] += 0;
                }
            }
        }
    }
}

$svatkyArray = naplnPoleSvatku($jahr,$monat);
$werkTageLautCalendarArray = getSollStundenLautCalendar($jahr, $monat, 0);
$werkTageLautCalendar = $werkTageLautCalendarArray['arbtage'];
$pracDobaA = naplnPolePracDoby($jahr, $monat, $domxml);

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);

$apl = AplDB::getInstance();

foreach($reportArray as $persnr=>$person){

    $personalinfo = getPersonalInfo($persInfoArray,$persnr);
    $personalinfo['arbtage'] = $werkTageLautCalendar;

  // vormonat und Jahr
    if($monat==1){
        $vormonat = 12;
        $vorjahr = $jahr-1;
    }
    else{
        $vormonat = $monat-1;
        $vorjahr = $jahr;
    }
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $vormonat, $vorjahr);
    $vormonatdatbis = $vorjahr."-".$vormonat."-".$pocetDnuVMesici;
    $restArray = getUrlaubBisDatum($persnr, $vormonatdatbis);
    //print_r($restArray);
    $personalinfo['rest'] = $restArray['rest'];

    $personalinfo['urlaubtagesoll'] = getUrlaubTageInMonatSoll($persnr, $monat, $jahr);
    $personalinfo['urlaubtageist'] = getUrlaubTageInMonatIst($persnr, $monat, $jahr);

    // prescasy
    $stddiffA = $apl->getStdDiff($monat, $jahr, $persnr);
    if($stddiffA!=null){
        $personalinfo['stddif_stunden'] = $stddiffA['stunden'];
        $personalinfo['stddif_datum'] = $stddiffA['datum'];
    }
    else{
        $personalinfo['stddif_stunden'] = 0;
        $personalinfo['stddif_datum'] = '??????';
    }

    if(intval($personalinfo['MAStunden'])!=0){
        $personalinfo['plusminusstunden'] = $apl->getPlusMinusStunden($monat, $jahr, $persnr);
        $personalinfo['plusminusstundenvor'] = $apl->getPlusMinusStunden($vormonat, $vorjahr, $persnr);
    }
    else{
        $personalinfo['plusminusstunden'] = 0;
        $personalinfo['plusminusstundenvor'] = 0;
    }

    if($typ == 2) {
    // pocty jednotlivych typu dnu
        $pocdnu = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $dbDatumVon = sprintf("%d-%02d-%02d",$jahr,$monat,1);
        $dbDatumBis = sprintf("%d-%02d-%02d",$jahr,$monat,$pocdnu);
        $personalinfo['atagecount'] = getATageCountBetweenDatums($dbDatumVon, $dbDatumBis, $persnr);
        $personalinfo['noatagecount'] = getNotATageCountBetweenDatums($dbDatumVon, $dbDatumBis, $persnr);
    }
    // pomocna ruka k otevreni portu
//    print_r($personalinfo);

    $ogs = $person;
    // spocitam si pocet oe
    $pocetOEs = 0;
    foreach ($ogs as $og=>$oes){
        $pocetOEs += count($oes);
    }

    // pocetOE + zahlavi pro persnr + zapati pro persnr
    $nasobek = 1;
    if($typ == 3) $nasobek = 2;
    $vyskaSekce = ($nasobek * $pocetOEs + 1 +1) * 5 + 1 ;
    test_pageoverflow($pdf, $vyskaSekce, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);

    zahlavi_personA($pdf,5,array(240,240,240),$persnr,$reportNameArray[$persnr],$personalinfo,$monat,$jahr,$typ,$sum_zapati_persnr_array['soll'][$persnr],$fullAccess);

    foreach($ogs as $og=>$oes){
        foreach($oes as $oekz=>$oe){
            if($typ==1||$typ==3){
                $datumy_soll = $oe['soll'];
                test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
                oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(255,245,245),$datumy_soll,$oekz,$og,"s",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
            }

            if($typ==2||$typ==3){
                $datumy_ist = $oe['ist'];
                test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
                oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(245,255,245),$datumy_ist,$oekz,$og,"i",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
            }
        }
    }

    if($typ==1||$typ==3){
        test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
        zapati_personA($pdf,5,array(255,245,245),$persnr,"s",$sum_zapati_persnr_array['soll'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
    }
    if($typ==2||$typ==3){
        test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
        zapati_personA($pdf,5,array(245,255,245),$persnr,"i",$sum_zapati_persnr_array['ist'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
    }

    $pdf->Cell(0,1,"",'0',1,'L',0);
    
}

$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);


if($typ==1||$typ==3){
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$sum_oe_array['soll'],"s",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
}

if($typ==2||$typ==3){
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$sum_oe_array['ist'],"i",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
}

if($typ==1||$typ==3){
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_sestava($pdf,5,array(255,245,245),$sum_zapati_sestava_array['soll'],"s",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
}

if($typ==2||$typ==3){
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_sestava($pdf,5,array(245,255,245),$sum_zapati_sestava_array['ist'],"i",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>