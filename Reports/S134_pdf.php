<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S134";
$doc_subject = "S134 Report";
$doc_keywords = "S134";

// necham si vygenerovat XML

$parameters=$_GET;
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
$oe = $_GET['oe'];
$von = make_DB_datum(validateDatum($_GET['von']));
$bis = make_DB_datum(validateDatum($_GET['bis']));

// vymenim hvezdicky za procenta
$oe = trim(str_replace('*', '%', $oe));



define('SOLL', 1);
define('IST',2);
define('SOLLIST',3);

if($reporttyp=='soll')
    $typ = SOLL;
else if($reporttyp=='ist')
    $typ = IST;
else
    $typ = SOLLIST;

$typ = SOLL;

$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S134",$password,$user);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S134_xml.php');


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

//echo "von=$von,bis=$bis";
//exit;


global $oeFarbenArray;

$sum_zapati_persnr_array;
global $sum_zapati_persnr_array;

$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;



/**
 *
 * @param DOMDocument $xml
 */
function naplnPolePracDoby($xml){
    $calendarNodes = $xml->getElementsByTagName('calendar');
    $calendar = $calendarNodes->item(0);
    $tage = $calendar->getElementsByTagName('tag');
    foreach ($tage as $tag){
        $tagChilds = $tag->childNodes;
        $datum = getValueForNode($tagChilds,'datum');
        $pole[$datum]['von_f_guss']=getValueForNode($tagChilds,'vonfguss');
        $pole[$datum]['bis_f_guss']=getValueForNode($tagChilds,'bisfguss');
        $pole[$datum]['von_s_guss']=getValueForNode($tagChilds,'vonsguss');
        $pole[$datum]['bis_s_guss']=getValueForNode($tagChilds,'bissguss');
        $pole[$datum]['von_f_ne']=getValueForNode($tagChilds,'vonfne');
        $pole[$datum]['bis_f_ne']=getValueForNode($tagChilds,'bisfne');
        $pole[$datum]['von_s_ne']=getValueForNode($tagChilds,'vonsne');
        $pole[$datum]['bis_s_ne']=getValueForNode($tagChilds,'bissne');
    }
    return $pole;
}


function naplnPoleSvatku($von,$bis) {
    dbConnect();
    $datvon = $von;//$jahr."-".$monat."-01";
    // get number of days in month
//    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $bis;//$jahr."-".$monat."-".$pocetDnuVMesici;

    $sql = "select calendar.datum from calendar where calendar.svatek<>0 and calendar.datum between '$datvon' and '$datbis'";
    $result = mysql_query($sql);
    $i=0;
    $pole = array();
    while($row = mysql_fetch_assoc($result)) {
        $pole[$i] = trim(substr($row['datum'],0,10));
//        echo $pole[$i];
        $i++;
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
function pageheader($pdfobjekt,$pole,$headervyskaradku,$jahr,$monat,$svatky,$pracDobaA,$vonstamp,$bisstamp) {
    
    $aktualniDen = date('Y-m-d');
    $daysBetween = ($bisstamp-$vonstamp)/(24*60*60);

//    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $days = array("So","Mo","Di","Mi","Do","Fr","Sa");

    $pdfobjekt->SetFillColor(255,255,200,1);
    $fill = 1;
//    $pdfobjekt->Cell(10,5,"OE",'1',0,'L',$fill);
//    $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
    
    //$sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/($daysBetween+1);
    $sirkabunky = 10;

    $markActual = false;
    $actualMonat = date('m');
    if($actualMonat==$monat) $markActual = true;

    $textSize = 4.6;
    // prac doba guss fruh
    $suffix = "f_guss";
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(10+3,3,"GFxx",'LRT',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", $textSize);
    for($den=$vonstamp;$den<=$bisstamp;$den+=(24*60*60)) {
        $testDatum = date('Y-m-d',$den);

        $workday = date('w',$den);

        $hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$testDatum]['von_'.$suffix].'-'.$pracDobaA[$testDatum]['bis_'.$suffix],'LRT',0,'R',$fill);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell($sirkabunky,3,"",'LRT',1,'R',$fill);

    // prac doba guss spat
    $suffix = "s_guss";
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(10+3,3,"GSxx",'LRB',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", $textSize);
    for($den=$vonstamp;$den<=$bisstamp;$den+=(24*60*60)) {
        $testDatum = date('Y-m-d',$den);

        $workday = date('w',$den);

        $hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$testDatum]['von_'.$suffix].'-'.$pracDobaA[$testDatum]['bis_'.$suffix],'LRT',0,'R',$fill);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell($sirkabunky,3,"",'LRB',1,'R',$fill);

    // prac doba ne fruh
    $suffix = "f_ne";
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(10+3,3,"NFxx",'LRT',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", $textSize);
    for($den=$vonstamp;$den<=$bisstamp;$den+=(24*60*60)) {
        $testDatum = date('Y-m-d',$den);

        $workday = date('w',$den);

        $hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$testDatum]['von_'.$suffix].'-'.$pracDobaA[$testDatum]['bis_'.$suffix],'LRT',0,'R',$fill);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell($sirkabunky,3,"",'LRT',1,'R',$fill);

    // prac doba guss spat
    $suffix = "s_ne";
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(10+3,3,"NSxx",'LRB',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "", $textSize);
    for($den=$vonstamp;$den<=$bisstamp;$den+=(24*60*60)) {
        $testDatum = date('Y-m-d',$den);

        $workday = date('w',$den);

        $hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,3,$pracDobaA[$testDatum]['von_'.$suffix].'-'.$pracDobaA[$testDatum]['bis_'.$suffix],'LRT',0,'R',$fill);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell($sirkabunky,3,"",'LRB',1,'R',$fill);


    // cisla dnu
    $pdfobjekt->SetFont("FreeSans", "", $textSize);
    $pdfobjekt->Cell(10+3,5,"",'1',0,'L',$fill);
    for($den=$vonstamp;$den<=$bisstamp;$den+=(24*60*60)) {
        $testDatum = date('Y-m-d',$den);

        $workday = date('w',$den);

        $hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,5,$testDatum,'1',0,'R',$fill);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell($sirkabunky,5,"Sum",'1',1,'R',$fill);

    // popisky dnu
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell(10+3,5,"OE",'1',0,'L',$fill);
    for($den=$vonstamp;$den<=$bisstamp;$den+=(24*60*60)) {
        $testDatum = date('Y-m-d',$den);

        $workday = date('w',$den);

        $hasArbeit = !hatTagArbZeit($pracDobaA[$testDatum], $suffix);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(245, 245, 255,1);
        $pdfobjekt->Cell($sirkabunky,5,$days[$workday],'1',0,'R',$fill);

        if($workday==6 || $workday==0 || in_array($testDatum, $svatky) || $hasArbeit)
            $pdfobjekt->SetFillColor(255, 255, 200,1);
    }
    $pdfobjekt->Cell($sirkabunky,5,"",'1',1,'R',$fill);


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
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

        ksort($sumArray);
        
        foreach ($sumArray as $kz=>$sArray){

            test_pageoverflow($pdfobjekt, $vyskaradku,"",$jahr,$monat,$svatky,$pracDobaA);

            $pdfobjekt->SetFont("FreeSans", "B", 6);
            $oergbStr = $oefarbenArray[$kz];
            $oergb = split(",", $oergbStr);
            $pdfobjekt->SetFillColor($oergb[0],$oergb[1],$oergb[2],1);
                $pdfobjekt->Cell(10,$vyskaradku,$kz,'1',0,'L',$fill);
//            $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
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

                $stunden = $sArray[$i];
                $sumaCelkem += $stunden;
                $stunden = number_format($stunden, 1);
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

                if($aktualniDen==$i && $markActual){
                    $pdfobjekt->SetLineWidth(0.2);
                    $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
                }
                if($workday==6 || $workday==0 || in_array($i, $svatky))
                    $pdfobjekt->SetFillColor($oergb[0],$oergb[1],$oergb[2],1);
//                     $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                }
                else{
                    $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
                }
            }

            $sumaCelkem = number_format($sumaCelkem, 1);
            $pdfobjekt->Cell(0,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

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
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

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
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

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

/**
 *
 * @param <type> $monat
 * @param <type> $jahr
 * @param <type> $persnr
 * @return array array("datum"=>$row['datum'],"stunden"=>$row['stunden']);
 */
function getStdDiff($monat,$jahr,$persnr) {
    if($monat==1) {
        $vormonat = 12;
        $vorjahr = $jahr - 1;
    }
    else {
        $vormonat = $monat - 1;
        $vorjahr = $jahr;
    }

    $sql = "select DATE_FORMAT(datum,'%y%m%d') as datum,stunden from dstddif where persnr='$persnr' and MONTH(datum)<='$vormonat' and YEAR(datum)<='$vorjahr' order by datum desc";
    dbConnect();
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        return array("datum"=>$row['datum'],"stunden"=>$row['stunden']);
    }
    else
        return null;
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

/**
 *
 * @param <type> $datvon
 * @param <type> $datbis
 * @param <type> $persnr
 * @return <type> 
 */
function getLastDZeitDatum($datvon,$datbis,$persnr){
    $sql = "select persnr,max(datum) as letzte_datum from dzeit";
    $sql.= " where dzeit.persnr='$persnr' and datum>'$datvon' and datum<='$datbis' group by persnr";
    dbConnect();
    $res = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($res);
        return substr($row['letzte_datum'], 0,10);
    }
    else {
        return null;
    }
}


/**
 *
 * @param <type> $dbDatumVon
 * @param <type> $dbDatumBis
 * @param <type> $persnr
 * @param <type> $oestatus
 * @return <type> 
 */
function getIstAnwesenheitStundenBetweenDatumsForOEStatus($datvon, $datbis, $persnr,$oestatus){
    $sql = "select persnr,sum(stunden) as sumstunden from dzeit";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.= " where dzeit.persnr='$persnr' and datum>'$datvon' and datum<='$datbis' and dtattypen.oestatus='n' group by persnr";
//    echo $sql;
    dbConnect();
    $res = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($res);
        return $row['sumstunden'];
    }
    else {
        return 0;
    }
}

/**
 * ohne nw stunden
 * @param <type> $datvon
 * @param <type> $datbis
 * @param <type> $persnr
 * @return <type>
 */
function getIstAnwesenheitStundenBetweenDatums($datvon,$datbis,$persnr) {
    $sql = "select persnr,sum(stunden) as sumstunden from dzeit";
    $sql.=" join dtattypen on dzeit.tat=dtattypen.tat";
    $sql.= " where dzeit.persnr='$persnr' and datum>'$datvon' and datum<='$datbis' and dtattypen.oestatus='a' group by persnr";
    dbConnect();
    $res = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($res);
        return $row['sumstunden'];
    }
    else {
        return 0;
    }
}

/**
 * suma hodin pro oe tat
 * bez ohledu na oestatus
 * 
 * crati sumu planovanych hodin pro zadane oe v rozmezi $dbDatumVon a $dbDatumBis
 * @param string $dbDatumVon ve formatu YYYY-MM-DD
 * @param string $dbDatumBis ve formatu YYYY-MM-DD
 * @param int $persnr
 * @param string $oe
 * @return double
 */
function getPlanOEStundenBetweenDatums($dbDatumVon,$dbDatumBis,$persnr,$oe) {
    $sql = "select sum(dzeitsoll.stunden) as sumstunden from dzeitsoll where datum>'$dbDatumVon' and datum<='$dbDatumBis' and persnr='$persnr' and oe='$oe'";
    dbConnect();
    $res = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($res);
        $v = $row['sumstunden'];
        if($v==null)
            return 0;
        else
            return $v;
    }
    else
        return 0;
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


/**
 * vypocita prescasove hodiny, jako vychozi bere hodnotu z tabulky stddiff
 * @param <type> $monat
 * @param <type> $jahr
 * @param <type> $persnr
 * @return <type>
 */
function getPlusMinusStunden($monat, $jahr, $persnr) {
    $returnArray = array();
    $plusMinusStunden = 0;
    $stddifA = getStdDiff($monat, $jahr, $persnr);
    if($stddifA==null)
        return null;
    else {
        $stddifStunden = $stddifA['stunden'];
        $dbDatumVon = '20'.substr($stddifA['datum'], 0, 2).'-'.substr($stddifA['datum'], 2, 2).'-'.substr($stddifA['datum'], 4, 2);


        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $dbDatumBis = sprintf("%s-%02d-%02d",$jahr,$monat,$pocetDnuVMesici);

        $regelStunden = getRegelarbzeit($persnr);
        $lastDzeitDatum = getLastDZeitDatum($dbDatumVon,$dbDatumBis,$persnr);

        // zjistim pocet prac dnu od 1 do lastDzeitDatum
        $arbTage = getArbTageBetweenDatums($dbDatumVon, $lastDzeitDatum);
//        $plusMinusStunden = $arbTage;
        $nStunden = getIstAnwesenheitStundenBetweenDatumsForOEStatus($dbDatumVon, $lastDzeitDatum, $persnr,'n');
        $plusMinusStunden = $nStunden;

        // kolik hodin mam do lastDzeitDatum odpracovat
        $sollStundenLastDzeitDatum = $arbTage * $regelStunden - $nStunden;
        $plusMinusStunden = $sollStundenLastDzeitDatum;

        // pocet odpracovanych hodin mezi datumama a posledni datum prace
        $istStundenA = getIstAnwesenheitStundenBetweenDatums($dbDatumVon, $lastDzeitDatum, $persnr);
        $plusMinusStunden = $istStundenA;

        $prescasyVMesici = $istStundenA - $sollStundenLastDzeitDatum;

        $nwStunden = getPlanOEStundenBetweenDatums($lastDzeitDatum,$dbDatumBis,$persnr,"nw");

        $prescasyVMesici = $prescasyVMesici - $nwStunden;
        $plusMinusStunden = $prescasyVMesici;

        $prescasyCelkem = $prescasyVMesici + $stddifStunden;
        $plusMinusStunden = $prescasyCelkem;

        // plusminusstunden prubezne prepisuju po poslednim prepisu v nem mam spravnou hodnotu
        // zapisu to tam
        return $plusMinusStunden;
    }
}

function getSollStundenLautCalendar($von,$bis,$stundenProTag) {
//1. get number of workday in month
    $jahr = $rok;
    $monat = $mesic;
    $datvon = $von;//$jahr."-".$monat."-01";
    // get number of days in month
    //$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $datbis = $bis;//$jahr."-".$monat."-".$pocetDnuVMesici;

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

    $sql = "select count(dzeitsoll.datum) as urlaubtage from dzeitsoll where persnr='$persnr' and datum between '$datvon' and '$datbis' and oe='d'";
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
    // 1. January
    $datvon = substr($bisDatum, 0, 4)."-"."01-01";
    $sql = "select count(datum) as hd from dzeit where dzeit.`Datum` between '$datvon' and '$bisDatum' and persnr='$persnr' and dzeit.tat='d'";
    $result = mysql_query($sql);
    if(mysql_affected_rows()>0) {
        $row = mysql_fetch_assoc($result);
        $genommenBis = $row['hd'];
    }
    else
        $genommenBis = 0;

    $rest = $anspruch + $alt - $gekrzt - $genommenBis;

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
function zahlavi_personA($pdfobjekt,$vyskaradku,$rgb,$persnr,$nameArray,$persInfoArray,$monat,$jahr,$typ,$sumsollArray,$fullAccess,$von,$bis){
    	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

//        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
        $daysBetween = ($bis-$von)/(24*60*60);
//        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

//        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/($daysBetween+1);
        $sirkabunky = 10;


        $name = $nameArray['name'];
        $vorname = $nameArray['vorname'];
        
        $jmeno = $vorname." ".$name;
//        $monatJahr = $monat."/".$jahr;
//        $monatJahr = sprintf("%02d/%04d",$monat,$jahr);
//        $monat = $monat*1;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(10,$vyskaradku,$persnr,'1',0,'L',$fill);
        $pdfobjekt->Cell(3+($daysBetween+2)*$sirkabunky,$vyskaradku,$jmeno,'LBTR',1,'L',$fill);


//        $pdfobjekt->Cell($sirkabunky,$vyskaradku,"",'RBT',1,'L',$fill);
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
function oe_radekA($oefarbenArray,$pdfobjekt,$vyskaradku,$rgb,$datumy,$oekz,$typ,$ityp,$monat,$jahr,$svatky,$tagvon,$tagbis,$von,$bis){

        $oeRGBArray = split(",",$oefarbenArray);

        $fill = 1;
        $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->Cell(10,$vyskaradku,$oekz,'1',0,'L',$fill);
//        $pdfobjekt->Cell(10,$vyskaradku,$oefarbenArray,'1',0,'L',$fill);
        $pdfobjekt->Cell(3,$vyskaradku,$typ,'1',0,'L',$fill);
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);


        $daysBetween = ($bis-$von)/(24*60*60);
//        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
//        $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/($daysBetween+1);
        $sirkabunky = 10;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $sumaHodin = 0;

        for($den=$von;$den<=$bis;$den+=(24*60*60)) {
                $testDatum = date('Y-m-d',$den);
                // oznaceni so+ne a svatku
                $workday = date('w',$den);
                if($workday==6 || $workday==0 || in_array($testDatum, $svatky))
                    $pdfobjekt->SetFillColor(245, 245, 255,1);

                $kresliPrazdny = 1;
                if(is_array($datumy)) {
                    foreach($datumy as $dat=>$stunden) {
                        $tag = intval(substr($dat, 8));
                        if($dat==$testDatum) {
                            if($stunden==0) {
                                $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
                                //                            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$oekz,'1',0,'R',$fill);
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

                if($workday==6 || $workday==0 || in_array($testDatum, $svatky))
                    $pdfobjekt->SetFillColor(245, 245, 255,1);

                if($kresliPrazdny) {
                //                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                    $pdfobjekt->Cell($sirkabunky,$vyskaradku,"",'1',0,'R',$fill);
                }

                if($workday==6 || $workday==0 || in_array($testDatum, $svatky))
                    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }
        // suma hodin pro danou cinnost
        $sumaHodin = number_format($sumaHodin, 1);
        $pdfobjekt->Cell($sirkabunky,$vyskaradku,$sumaHodin,'1',0,'R',$fill);
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
function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$jahr,$monat,$svatky,$pracDoba,$von,$bis)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5,$jahr,$monat,$svatky,$pracDoba,$von,$bis);
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
                        "stdsoll_datum"=>"",
                        "dobaurcita"=>""
                    );

    // hledam persnr
    foreach($nodesArray as $node){
        $nodeChilds = $node->childNodes;
        $persnr1 = getValueForNode($nodeChilds,'persnr');
        if($persnr1==$persnr){
            $vystup['komm_ort'] = getValueForNode($nodeChilds, 'komm_ort');
            $vystup['regelarbzeit'] = getValueForNode($nodeChilds, 'regelarbzeit');
            $vystup['stdsoll_datum'] = getValueForNode($nodeChilds, 'stdsoll_datum');
            $vystup['dobaurcita'] = getValueForNode($nodeChilds, 'dobaurcita');
            return $vystup;
        }
    }
    return $vystup;
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S134 Personal Plan / Anwesenheit"." ( ".$reporttyp." )", $params);
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

//print_r($farben);

$reportArray = array();

if($typ==1) $sollIstArray = array("soll");
else if($typ==2) $sollIstArray = array("ist");
else $sollIstArray = array("soll","ist");

//print_r($sollIstArray);

//$sollIstArray  = array("soll","ist");
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
            $tage = $oe->getElementsByTagName("tag");
            foreach($tage as $tag){
                $tagChilds = $tag->childNodes;
                $datum = getValueForNode($tagChilds, "datum");
                $stunden = getValueForNode($tagChilds, "stunden");
                $reportArray[$persnr][$oekz][$sollIst][$datum]=$stunden;
            }
        }
    }
}

ksort($reportArray);
//print_r($reportArray);

//exit;
$datetimevon = strtotime($von);
$datetimebis = strtotime($bis);

$daysBetween = ($datetimebis-$datetimevon)/(24*60*60);

//echo "datetimevon = $datetimevon, datetimebis = $datetimebis, daysBetween = $daysBetween";
//
//for($time = $datetimevon;$time<=$datetimebis;$time+=(24*60*60)){
//    $datum = date('Y-m-d', $time);
//    echo "datum = $datum<br>";
//}
//exit;

// spocitam sumu pro jednotlive osoby
foreach ($sollIstArray as $sollist){
    $planTree = $domxml->getElementsByTagName($sollist)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen=$planTree->getElementsByTagName("pers");
    foreach($personen as $person){
         $persChilds = $person->childNodes;
         $persnr = getValueForNode($persChilds, "persnr");
         for($den=$datetimevon;$den<=$datetimebis;$den+=(24*60*60)){
                $testDatum = date('Y-m-d',$den);
                $datumy=$person->getElementsByTagName("tag");
                foreach($datumy as $datum){
                    $datumChilds = $datum->childNodes;
                    $dat = getValueForNode($datumChilds, "datum");
//                    $tag = intval(substr($dat, 8));
                    $stunden = getValueForNode($datumChilds, "stunden");
                    if($testDatum==$dat){
                        $sum_zapati_persnr_array[$sollist][$persnr][$testDatum] += $stunden;
                        $sum_zapati_sestava_array[$sollist][$testDatum] += $stunden;
                    }
                    else{
                        $sum_zapati_persnr_array[$sollist][$persnr][$testDatum] += 0;
                        $sum_zapati_sestava_array[$sollist][$testDatum] += 0;
                    }
                }
         }
    }
}

//print_r($sum_zapati_persnr_array);
//exit;

// spocitam sumy pro oe
foreach($sollIstArray as $sollist){
    $planTree = $domxml->getElementsByTagName($sollist)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $oes=$planTree->getElementsByTagName("oe");
    foreach ($oes as $oe){
        $oeChilds = $oe->childNodes;
        $oekz = getValueForNode($oeChilds, 'oekz');
        for($den=$datetimevon;$den<=$datetimebis;$den+=(24*60*60)){
            $testDatum = date('Y-m-d',$den);
            $datumy=$oe->getElementsByTagName("tag");
            foreach($datumy as $datum){
                $datumChilds = $datum->childNodes;
                $dat = getValueForNode($datumChilds, "datum");
//                $tag = intval(substr($dat, 8));
                $stunden = getValueForNode($datumChilds, "stunden");
                if($testDatum==$dat){
                    $sum_oe_array[$sollist][$oekz][$testDatum] += $stunden;
                }
                else{
                    $sum_oe_array[$sollist][$oekz][$testDatum] += 0;
                }
            }
        }
    }
}


//print_r($sum_oe_array);
//exit;


$svatkyArray = naplnPoleSvatku($von,$bis);
//print_r($svatkyArray);
//exit;

$werkTageLautCalendarArray = getSollStundenLautCalendar($von,$bis, 0);
$werkTageLautCalendar = $werkTageLautCalendarArray['arbtage'];

$pracDobaA = naplnPolePracDoby($domxml);
//print_r($pracDobaA);
//exit;

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA,$datetimevon,$datetimebis);


////print_r($reportArray);
foreach($reportArray as $persnr=>$person){

    $personalinfo = getPersonalInfo($persInfoArray,$persnr);
    $personalinfo['arbtage'] = $werkTageLautCalendar;

    // do oes si ted dam pole vsech cinnost
    $oes = $person;
    $pocetOEs = count($oes);

//    echo "oes:";print_r($oes);
    // pocetOE + zahlavi pro persnr + zapati pro persnr
    $nasobek = 1;
    if($typ == 3) $nasobek = 2;
    $vyskaSekce = ($nasobek * $pocetOEs + 1 +1) * 5;
    test_pageoverflow($pdf, $vyskaSekce, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA,$datetimevon,$datetimebis);

    zahlavi_personA($pdf,5,array(240,240,240),$persnr,$reportNameArray[$persnr],$personalinfo,$monat,$jahr,$typ,$sum_zapati_persnr_array['soll'][$persnr],$fullAccess,$datetimevon,$datetimebis);
//
    foreach($oes as $oekz=>$oe){

//    echo "oe:";print_r($oe);
        if($typ==1||$typ==3){
            $datumy_soll = $oe['soll'];
//            echo "datumy_soll";print_r($datumy_soll);
            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA,$datetimevon,$datetimebis);
            oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(255,245,245),$datumy_soll,$oekz,"s",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$datetimevon,$datetimebis);
        }

        if($typ==2||$typ==3){
            $datumy_ist = $oe['ist'];
//            echo "datumy_ist";print_r($datumy_ist);
            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
            oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(245,255,245),$datumy_ist,$oekz,"i",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
        }
    }
//
//    if($typ==1||$typ==3){
//        test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//        zapati_personA($pdf,5,array(255,245,245),$persnr,"s",$sum_zapati_persnr_array['soll'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//    }
//    if($typ==2||$typ==3){
//        test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//        zapati_personA($pdf,5,array(245,255,245),$persnr,"i",$sum_zapati_persnr_array['ist'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//    }

    $pdf->Cell(0,1,"",'0',1,'L',0);

}
//
//$pdf->AddPage();
//pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);
//
//
//if($typ==1||$typ==3){
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$sum_oe_array['soll'],"s",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//}
//
//if($typ==2||$typ==3){
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$sum_oe_array['ist'],"i",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//}
//
//if($typ==1||$typ==3){
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_sestava($pdf,5,array(255,245,245),$sum_zapati_sestava_array['soll'],"s",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//}
//
//if($typ==2||$typ==3){
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_sestava($pdf,5,array(245,255,245),$sum_zapati_sestava_array['ist'],"i",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>