<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S123";
$doc_subject = "S123 Report";
$doc_keywords = "S123";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];
//$reporttyp = $_GET['reporttyp'];
//$oe = $_GET['oe'];
$tagvon = $_GET['tagvon'];
$tagbis = $_GET['tagbis'];

if(!$tagvon) $tagvon = 1;
if(!$tagbis) $tagbis = 31;
// vymenim hvezdicky za procenta
//$oe = trim(str_replace('*', '%', $oe));


//$password = $_GET['password'];
//$user = $_SESSION['user'];

//$fullAccess = testReportPassword("S122",$password,$user);

$user = $_SESSION['user'];
$password = $_GET['password'];

//$fullAccess = testReportPassword("S122",$password,$user,0);
//
//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}

$oe = $_GET['oe'];
$oe = str_replace('*', '%', $oe);
if($oe=='%') $oe='';

require_once('S123_xml.php');


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

$sumOEProPersnr = array();
global $sumOEProPersnr;

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
function pageheader($pdfobjekt,$pole,$headervyskaradku,$jahr,$monat,$svatky,$pracDobaA) {
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $days = array("So","Mo","Di","Mi","Do","Fr","Sa");

    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->SetFillColor(255,255,200,1);
    $fill = 1;
    //    $pdfobjekt->Cell(10,5,"OE",'1',0,'L',$fill);
    $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

    $markActual = false;
    $actualMonat = date('m');
    if($actualMonat==$monat) $markActual = true;



    // cisla dnu
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->Cell(10+10,5,"",'1',0,'L',$fill);
    for($den=1;$den<=$pocetDnuVMesici;$den++) {


        if($aktualniDen==$den && $markActual) {
            $pdfobjekt->SetLineWidth(0.5);
            $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
        }
        $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
        if($workday==6 || $workday==0 || in_array($den, $svatky))
            $pdfobjekt->SetFillColor(245, 245, 255,1);
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
    $pdfobjekt->Cell(10+10,5,"OE",'1',0,'L',$fill);
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
function zapati_oes($oefarbenArray,$pdfobjekt,$vyskaradku,$rgb,$sumArray,$typ,$monat, $jahr,$svatky,$tagvon,$tagbis) {

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    ksort($sumArray);

    //        echo "<pre>";
    //        print_r($sumArray);
    //        echo "</pre>";

    foreach ($sumArray as $kz=>$sArray) {

        test_pageoverflow($pdfobjekt, $vyskaradku,"",$jahr,$monat,$svatky,$pracDobaA);

        $pdfobjekt->SetFont("FreeSans", "B", 6);
        $oergbStr = $oefarbenArray[$kz];
        $oergb = split(",", $oergbStr);
        $pdfobjekt->SetFillColor($oergb[0],$oergb[1],$oergb[2],1);
        $pdfobjekt->Cell(10,$vyskaradku,$kz,'1',0,'L',$fill);
        //            $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    switch ($typ) {
        case 'A':
            $typAppendix = "vzkd_s[Std]";
            break;
        case 'L':
            $typAppendix = "vzkd[Std]";
            break;
        case 'W':
            $typAppendix = "Anw[Std]";
            break;
        case 'S':
            $typAppendix = "AnwSoll[Std]";
            break;
        default:
            $typAppendix = "???";
            break;
    }

        $pdfobjekt->SetFont("FreeSans", "", 5.5);
        $pdfobjekt->Cell(10,$vyskaradku,$typAppendix,'1',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "B", 6);
        //        $pdfobjekt->Cell(0,$vyskaradku,$jmeno,'1',1,'L',$fill);
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

                    switch ($typ) {
                    case 'A':
                        $stunden = $sArray[$i]['stunden'];
                        break;
                    case 'L':
                        $stunden = $sArray[$i]['vzkd'];
                        break;
                    case 'W':
                        $stunden = $sArray[$i]['stundenanwesenheit'];
                        break;
                    case 'S':
                        $stunden = $sArray[$i]['stundensoll'];
                        break;
                    default:
                        $stunden = 0;
                        break;
                }

//                if($typ=='A')
//                    $stunden = $sArray[$i]['stunden'];
//                else
//                    $stunden = $sArray[$i]['vzkd'];

                $sumaCelkem += $stunden;
                if($typ=='A')
                    $stunden = number_format($stunden, 2,',',' ');
                else
                    $stunden = number_format($stunden, 2,',',' ');

                //                $stunden = $sArray[$i];
                //                $sumaCelkem += $stunden;
                //                $stunden = number_format($stunden, 1);
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

        if($typ=='A')
            $sumaCelkem = number_format($sumaCelkem, 2,',',' ');
        else
            $sumaCelkem = number_format($sumaCelkem, 2,',',' ');
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
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$sumArray,$typ, $monat, $jahr,$svatky,$tagvon,$tagbis) {
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $pdfobjekt->SetFont("FreeSans", "B", 6);
    $pdfobjekt->Cell(10,$vyskaradku,"Sum",'1',0,'L',$fill);
    switch ($typ) {
        case 'A':
            $typAppendix = "vzkd_s[Std]";
            break;
        case 'L':
            $typAppendix = "vzkd[Std]";
            break;
        case 'W':
            $typAppendix = "Anw[Std]";
            break;
        case 'S':
            $typAppendix = "AnwSoll[Std]";
            break;
        default:
            $typAppendix = "???";
            break;
    }


    $pdfobjekt->SetFont("FreeSans", "", 5.5);
    $pdfobjekt->Cell(10,$vyskaradku,$typAppendix,'1',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "B", 6);
    //        $pdfobjekt->Cell(0,$vyskaradku,$jmeno,'1',1,'L',$fill);
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

                switch ($typ) {
                case 'A':
                    $stunden = $sumArray[$i]['stunden'];
                    break;
                case 'L':
                    $stunden = $sumArray[$i]['vzkd'];
                    break;
                case 'W':
                    $stunden = $sumArray[$i]['stundenanwesenheit'];
                    break;
                case 'S':
                    $stunden = $sumArray[$i]['stundensoll'];
                    break;
                default:
                    $typAppendix = "???";
                    break;
            }

//            if($typ=='A')
//                $stunden = $sumArray[$i]['stunden'];
//            else
//                $stunden = $sumArray[$i]['vzkd'];

            $sumaCelkem += $stunden;
            if($typ=='A')
                $stunden = number_format($stunden, 2,',',' ');
            else
                $stunden = number_format($stunden, 2,',',' ');

            //            $stunden = $sumArray[$i];
            //            $sumaCelkem += $stunden;
            //            $stunden = number_format($stunden, 1);
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

            if($aktualniDen==$i && $markActual) {
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        }
        else {
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
        }
    }

    if($typ=='A')
        $sumaCelkem = number_format($sumaCelkem, 2,',',' ');
    else
        $sumaCelkem = number_format($sumaCelkem, 2,',',' ');
    $pdfobjekt->Cell(0,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

    $pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
function zapati_sestavafaktory($pdfobjekt,$vyskaradku,$rgb,$sumArray,$typ, $monat, $jahr,$svatky,$tagvon,$tagbis,$zU) {
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $pdfobjekt->SetFont("FreeSans", "B", 6);
    $pdfobjekt->Cell(10+10,$vyskaradku,$zU,'1',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "B", 6);
    //        $pdfobjekt->Cell(0,$vyskaradku,$jmeno,'1',1,'L',$fill);
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

            $stunden = $sumArray[$i];
            $stunden = number_format($stunden, 2,',',' ');

            //            $stunden = $sumArray[$i];
            //            $sumaCelkem += $stunden;
            //            $stunden = number_format($stunden, 1);
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

            if($aktualniDen==$i && $markActual) {
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        }
        else {
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
        }
    }

    $sumaCelkem = number_format($sumArray['sum'], 2,',',' ');
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
function zapati_personA($pdfobjekt,$vyskaradku,$rgb,$persnr,$typ,$sumArray,$monat,$jahr,$svatky,$tagvon,$tagbis) {

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    //	$pdfobjekt->Cell(10,$vyskaradku,$persnr,'1',0,'L',$fill);
    $pdfobjekt->Cell(10,$vyskaradku,"Sum",'1',0,'L',$fill);

    switch ($typ) {
        case 'A':
            $typAppendix = "vzkd_s[Std]";
            break;
        case 'L':
            $typAppendix = "vzkd[Std]";
            break;
        case 'W':
            $typAppendix = "Anw[Std]";
            break;
        case 'S':
            $typAppendix = "AnwSoll[Std]";
            break;
        default:
            $typAppendix = "???";
            break;
    }

    $pdfobjekt->SetFont("FreeSans", "", 5.5);
    $pdfobjekt->Cell(10,$vyskaradku,$typAppendix,'1',0,'L',$fill);
    $pdfobjekt->SetFont("FreeSans", "B", 7);
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

                switch ($typ) {
                case 'A':
                    $stunden = $sumArray[$i]['stunden'];
                    break;
                case 'L':
                    $stunden = $sumArray[$i]['vzkd'];
                    break;
                case 'W':
                    $stunden = $sumArray[$i]['stundenanwesenheit'];
                    break;
                case 'S':
                    $stunden = $sumArray[$i]['stundensoll'];
                    break;
                default:
                    $stunden = 0;
                    break;
            }

//            if ($typ == 'A')
//                $stunden = $sumArray[$i]['stunden'];
//            else
//                $stunden = $sumArray[$i]['vzkd'];

            $sumaCelkem += $stunden;

            $bLeer = FALSE;
            if($stunden==0)
                $bLeer = TRUE;
            if($typ=='A')
                $stunden = number_format($stunden, 2,',',' ');
            else
                $stunden = number_format($stunden, 2,',',' ');

            if(!$bLeer)
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);
            else
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','1',0,'R',$fill);

            if($aktualniDen==$i && $markActual) {
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        }
        else {
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
        }
    }

    if($typ=='A')
        $sumaCelkem = number_format($sumaCelkem, 2,',',' ');
    else
        $sumaCelkem = number_format($sumaCelkem, 2,',',' ');

    $pdfobjekt->Cell(10,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

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
function zapati_personAfaktory($pdfobjekt,$vyskaradku,$rgb,$persnr,$typ,$sumArray,$monat,$jahr,$svatky,$tagvon,$tagbis,$zeileUeberschrift) {

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    //	$pdfobjekt->Cell(10,$vyskaradku,$persnr,'1',0,'L',$fill);
    $pdfobjekt->Cell(10+10,$vyskaradku,$zeileUeberschrift,'1',0,'L',$fill);

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

            $stunden = $sumArray[$i];

            if($stunden===FALSE)
                $stunden = '';
            else
                $stunden = number_format($stunden, 2,',',' ');
            
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);

            if($aktualniDen==$i && $markActual) {
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }
            if($workday==6 || $workday==0 || in_array($i, $svatky))
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        }
        else {
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
        }
    }

    if ($sumArray['sum'] === FALSE)
        $sumaCelkem = '';
    else
        $sumaCelkem = number_format($sumArray['sum'], 2, ',', ' ');
    $pdfobjekt->Cell(10, $vyskaradku, $sumaCelkem, '1', 1, 'R', $fill);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $persnr
 * @param <type> $nameArray
 */
function zahlavi_personA($pdfobjekt,$vyskaradku,$rgb,$persnr,$nameArray,$persInfoArray,$monat,$jahr,$typ,$sumsollArray,$fullAccess) {
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;

    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

    $name = $nameArray['name'];
    $vorname = $nameArray['vorname'];

    $jmeno = $vorname." ".$name;

    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->Cell(10,$vyskaradku,$persnr,'LBT',0,'L',$fill);
    $pdfobjekt->Cell(3+4*$sirkabunky,$vyskaradku,$jmeno,'LBT',0,'L',$fill);


    $pdfobjekt->Cell(0,$vyskaradku,"",'RBT',1,'L',$fill);

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
function oe_radekA($oefarbenArray,$pdfobjekt,$vyskaradku,$rgb,$datumy,$oekz,$typ,$ityp,$monat,$jahr,$svatky,$tagvon,$tagbis,$leistFaktor) {

    $oeRGBArray = split(",",$oefarbenArray);

    $fill = 1;
    $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->Cell(10,$vyskaradku,$oekz,'1',0,'L',$fill);

    switch ($typ) {
        case 'A':
            $typAppendix = "vzkd_s[Std]";
            break;
        case 'L':
            $typAppendix = "vzkd[Std]";
            break;
        case 'W':
            $typAppendix = "Anw[Std]";
            break;
        case 'S':
            $typAppendix = "AnwSoll[Std]";
            break;
        default:
            $typAppendix = "???";
            break;
    }

    $pdfobjekt->SetFont("FreeSans", "", 5.5);
    $pdfobjekt->Cell(10,$vyskaradku,$typAppendix,'1',0,'L',$fill);
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $markActual = false;
    $actualMonat = date('m');
    if($actualMonat==$monat) $markActual = true;

    $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
    $pdfobjekt->SetFont("FreeSans", "", 7);
    $sumaHodin = 0;

    for($den=1;$den<=$pocetDnuVMesici;$den++) {
    //        echo "<br>den = $den, tagvon = $tagvon, tagbis = $tagbis";
        if(($den>=$tagvon) && ($den<=$tagbis)) {
        //                echo "splnena podminka";
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
                foreach($datumy as $dat=>$stundenA) {
                    $tag = intval(substr($dat, 8));
                   switch ($typ) {
                        case 'A':
                            $stunden = $stundenA['stunden'];
                            break;
                        case 'L':
                            $stunden = $stundenA['vzkd'];
                            break;
                        case 'W':
                            $stunden = $stundenA['stundenanwesenheit'];
                            break;
                        case 'S':
                            $stunden = $stundenA['stundensoll'];
                            break;
                        default:
                            $stunden = 0;
                            break;
                    }

//                    if($typ=='A')
//                        $stunden = $stundenA['stunden'];
//                    else
//                        $stunden = $stundenA['vzkd'];

                    if($tag==$den) {
                        if($stunden==0) {
                            $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
                            //                            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$oekz,'1',0,'R',$fill);
                            if($typ=='A')
                                $stunden = number_format($stunden, 2,',',' ');
                            else
                                $stunden = number_format($stunden, 2,',',' ');
                            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);
                            $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                        }
                        else {
                            $sumaHodin += $stunden;
                            if($typ=='A')
                                $stunden = number_format($stunden, 2,',',' ');
                            else
                                $stunden = number_format($stunden, 2,',',' ');

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
            //                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                $pdfobjekt->Cell($sirkabunky,$vyskaradku,"",'1',0,'R',$fill);
            }

            if($aktualniDen==$den && $markActual) {
                $pdfobjekt->SetLineWidth(0.2);
                $pdfobjekt->SetDrawColor($prevcolor[0],$prevcolor[1],$prevcolor[2]);
            }

            if($workday==6 || $workday==0 || in_array($den, $svatky))
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        }
        else {
            $pdfobjekt->Cell($sirkabunky,$vyskaradku,'','B',0,'R',$fill);
        }
    }
    // suma hodin pro danou cinnost
    if($typ=='A')
        $sumaHodin = number_format($sumaHodin, 2,',',' ');
    else
        $sumaHodin = number_format($sumaHodin, 2,',',' ');
    $pdfobjekt->SetFillColor($oeRGBArray[0],$oeRGBArray[1],$oeRGBArray[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->Cell(10,$vyskaradku,$sumaHodin,'1',0,'R',$fill);

    $pdfobjekt->SetFont("FreeSans", "", 7);
    if($typ=='A')
        $obsah = number_format($leistFaktor, 2,',',' ');
    else
        $obsah = '';
    $pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',0,'R',$fill);
   
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

function testSummePersNrStunden($persnr,$sollist,$stunden){
    global $sum_zapati_persnr_array;
    $sum = 0;
    for($den=1;$den<=31;$den++){
        $sum += $sum_zapati_persnr_array[$sollist][$persnr][$den][$stunden];
    }
    return $sum;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);
// pujde nahoru
$von = $jahr."-".$monat."-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

$aplDB = AplDB::getInstance();
$arbTage = $aplDB->getArbTageBetweenDatums($von,$bis);
$arbStunden = 8*$arbTage;
$arbtageText = "Arbeitstage: $arbTage ($arbStunden Std.)  ";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S123 VzKd / VzKdSoll", $arbtageText.$params);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT-10);
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


// vytahnu si oefarben
$farben = $domxml->getElementsByTagName('farbe');
foreach($farben as $farbe){
    $farbeChilds = $farbe->childNodes;
    $key = getValueForNode($farbeChilds, 'oe');
    $value = getValueForNode($farbeChilds, 'rgb');
    $oeFarbenArray[$key] = $value;
}

$reportArray = array();
$sollIstArray = array("dzeit","leistung","dzeitanwesenheit");

// dam dohromady stromy s dochazkou a s vykonama

foreach($sollIstArray as $sollIst){
    $planTree = $domxml->getElementsByTagName($sollIst)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen=$planTree->getElementsByTagName("pers");
    foreach($personen as $person){
        $personChilds = $person->childNodes;
        $persnr = getValueForNode($personChilds, "PersNr");
        $name = getValueForNode($personChilds, "Name");
        $vorname = getValueForNode($personChilds, "Vorname");
        $reportNameArray[$persnr] = array('name'=>$name,'vorname'=>$vorname);

        $oes = $person->getElementsByTagName("oe");
        foreach($oes as $oe){
            $oeChilds = $oe->childNodes;
            $oekz = getValueForNode($oeChilds, "oekz");
            $og = getValueForNode($oeChilds, "og");
            $oestatus = getValueForNode($oeChilds, "oestatus");
            $tage = $oe->getElementsByTagName("tag");
//            $reportArray[$persnr][$oekz]['og']=$og;
            foreach($tage as $tag){
                $tagChilds = $tag->childNodes;
                $datum = getValueForNode($tagChilds, "datum");
                $stunden = getValueForNode($tagChilds, "sumstunden");
                $stundenSoll = getValueForNode($tagChilds, "sumstundensoll");
                $stundenAnwesenheit = getValueForNode($tagChilds, "sumstundenanwesenheit");
                $vzkd = getValueForNode($tagChilds, "sumvzkd");
                $verb = getValueForNode($tagChilds, "sumverb");
                $reportArray[$persnr][$og][$oekz][$oestatus][$sollIst][$datum]['stunden']=$stunden;
                $reportArray[$persnr][$og][$oekz][$oestatus][$sollIst][$datum]['stundensoll']=$stundenSoll;
                $reportArray[$persnr][$og][$oekz][$oestatus][$sollIst][$datum]['stundenanwesenheit']=$stundenAnwesenheit;
                $reportArray[$persnr][$og][$oekz][$oestatus][$sollIst][$datum]['vzkd']=$vzkd;
                $reportArray[$persnr][$og][$oekz][$oestatus][$sollIst][$datum]['verb']=$verb;
            }
            if(is_array($reportArray[$persnr][$og])) ksort($reportArray[$persnr][$og]);
        }
        if(is_array($reportArray[$persnr])) ksort($reportArray[$persnr]);
    }
}

//ksort($reportArray);
//echo "<pre>";
//    print_r($reportArray);
//echo "</pre>";
//
//exit();
//
//
// spocitam sumu pro jednotlive osoby
foreach ($sollIstArray as $sollist) {
    $planTree = $domxml->getElementsByTagName($sollist)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen = $planTree->getElementsByTagName("pers");
    foreach ($personen as $person) {
        $persChilds = $person->childNodes;
        $persnr = getValueForNode($persChilds, "PersNr");
        $oes = $person->getElementsByTagName('oe');
        foreach ($oes as $oe) {
            $oechilds = $oe->childNodes;
            $oekz = getValueForNode($oechilds, 'oekz');
            $og = getValueForNode($oechilds, 'og');
            $oestatus = getValueForNode($oechilds, 'oestatus');
            for ($den = 1; $den <= 31; $den++) {
                $datumy = $oe->getElementsByTagName("tag");
                foreach ($datumy as $datum) {
                    $datumChilds = $datum->childNodes;
                    $dat = getValueForNode($datumChilds, "datum");
                    $tag = intval(substr($dat, 8, 2));
                    $stunden = getValueForNode($datumChilds, "sumstunden");
                    $stundenSoll = getValueForNode($datumChilds, "sumstundensoll");
                    $stundenAnwesenheit = getValueForNode($datumChilds, "sumstundenanwesenheit");
                    $vzkd = getValueForNode($datumChilds, "sumvzkd");
                    $verb = getValueForNode($datumChilds, "sumverb");

                    if (($tag == $den) && ($oestatus == 'a')) {
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['stunden'] += $stunden;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['stundensoll'] += $stundenSoll;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['stundenanwesenheit'] += $stundenAnwesenheit;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['vzkd'] += $vzkd;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['verb'] += $verb;
                    } else {
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['stunden'] += 0;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['stundensoll'] += 0;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['stundenanwesenheit'] += 0;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['vzkd'] += 0;
                        $sum_zapati_persnr_array[$sollist][$persnr][$den]['verb'] += 0;
                    }
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['stunden'] += $stunden;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['stundensoll'] += $stundenSoll;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['stundenanwesenheit'] += $stundenAnwesenheit;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['vzkd'] += $vzkd;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['verb'] += $verb;
                }
            }
        }
    }
}

// spocitam sumy pro oe a sestavu
foreach ($sollIstArray as $sollist) {
    $planTree = $domxml->getElementsByTagName($sollist)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen = $planTree->getElementsByTagName("pers");
    foreach ($personen as $person) {
        $persChilds = $person->childNodes;
        $persnr = getValueForNode($persChilds, "PersNr");
//        echo "<br>persnr=$persnr,sollist=$sollist";
        // vzkd soll
        $sum1 = testSummePersNrStunden($persnr, 'dzeit', 'stunden');
//        echo "<br>testSummePersNrStunden($persnr, 'dzeit', 'stunden') ".$sum1;
        // anwesenheit soll
        $sum2 = testSummePersNrStunden($persnr, 'dzeit', 'stundensoll');
        // vzkd ist
        $sum3 = testSummePersNrStunden($persnr, 'leistung', 'vzkd');
        // anwesenheit ist
        $sum4 = testSummePersNrStunden($persnr, 'dzeitanwesenheit', 'stundenanwesenheit');

        if ($sum1 == 0 && $sum3 == 0)
        continue;

        $persChilds = $person->childNodes;
        $persnr = getValueForNode($persChilds, "PersNr");
        $oes = $person->getElementsByTagName('oe');
        foreach ($oes as $oe) {
            $oechilds = $oe->childNodes;
            $oekz = getValueForNode($oechilds, 'oekz');
            $og = getValueForNode($oechilds, 'og');
            $oestatus = getValueForNode($oechilds, 'oestatus');
            for ($den = 1; $den <= 31; $den++) {
                //$datumy=$person->getElementsByTagName("tag");
                $datumy = $oe->getElementsByTagName("tag");
                foreach ($datumy as $datum) {
                    $datumChilds = $datum->childNodes;
                    $dat = getValueForNode($datumChilds, "datum");
                    $tag = intval(substr($dat, 8, 2));
                    $stunden = getValueForNode($datumChilds, "sumstunden");
                    $stundenSoll = getValueForNode($datumChilds, "sumstundensoll");
                    $stundenAnwesenheit = getValueForNode($datumChilds, "sumstundenanwesenheit");
                    $vzkd = getValueForNode($datumChilds, "sumvzkd");
                    $verb = getValueForNode($datumChilds, "sumverb");

                    if (($tag == $den) && ($oestatus == 'a')) {
                        $sum_zapati_sestava_array[$sollist][$den]['stunden'] += $stunden;
                        $sum_zapati_sestava_array[$sollist][$den]['stundensoll'] += $stundenSoll;
                        $sum_zapati_sestava_array[$sollist][$den]['stundenanwesenheit'] += $stundenAnwesenheit;
                        $sum_zapati_sestava_array[$sollist][$den]['vzkd'] += $vzkd;
                        $sum_zapati_sestava_array[$sollist][$den]['verb'] += $verb;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['stunden'] += $stunden;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['stundensoll'] += $stundenSoll;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['stundenanwesenheit'] += $stundenAnwesenheit;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['vzkd'] += $vzkd;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['verb'] += $verb;
                    } else {
                        $sum_zapati_sestava_array[$sollist][$den]['stunden'] += 0;
                        $sum_zapati_sestava_array[$sollist][$den]['stundensoll'] += 0;
                        $sum_zapati_sestava_array[$sollist][$den]['stundenanwesenheit'] += 0;
                        $sum_zapati_sestava_array[$sollist][$den]['vazby'] += 0;
                        $sum_zapati_sestava_array[$sollist][$den]['verb'] += 0;

                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['stunden'] += 0;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['stundensoll'] += 0;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['stundenanwesenheit'] += 0;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['vzkd'] += 0;
                        $sum_oe_array[$sollist][$og][$oestatus][$oekz][$den]['verb'] += 0;
                    }
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['stunden'] += $stunden;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['stundensoll'] += $stundenSoll;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['stundenanwesenheit'] += $stundenAnwesenheit;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['vzkd'] += $vzkd;
                    $sumOEProPersnr[$sollist][$persnr][$oekz]['verb'] += $verb;
                }
            }
            if (is_array($sum_oe_array[$sollist][$og]['a']))
                ksort($sum_oe_array[$sollist][$og]['a']);
        }
        if (is_array($sum_oe_array[$sollist]))
            ksort($sum_oe_array[$sollist]);
    }
}

//echo "<pre>";
//print_r($sum_zapati_sestava_array);
//echo "</pre>";
//
//exit();


//echo "<pre>";
//print_r($sum_oe_array);
//echo "</pre>";
//exit();

$svatkyArray = naplnPoleSvatku($jahr,$monat);
$werkTageLautCalendar = $werkTageLautCalendarArray['arbtage'];

//echo "<pre>";
//print_r($svatkyArray);
//echo "</pre>";
//exit();

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);

foreach($reportArray as $persnr=>$person) {

    // vzkd soll
    $sum1 = testSummePersNrStunden($persnr, 'dzeit', 'stunden');
    // anwesenheit soll
    $sum2 = testSummePersNrStunden($persnr, 'dzeit', 'stundensoll');
    // vzkd ist
    $sum3 = testSummePersNrStunden($persnr, 'leistung', 'vzkd');
    // anwesenheit ist
    $sum4 = testSummePersNrStunden($persnr, 'dzeitanwesenheit', 'stundenanwesenheit');

    if($sum1==0 && $sum3==0)        continue;

// do oes si ted dam pole vsech cinnost
    $oes = $person;
    $ogs = $person;
    $pocetOEs = 0;
    foreach ($ogs as $og=>$oes) $pocetOEs += count($oes);
    $nasobek = 2;
    $vyskaSekce = ($nasobek * $pocetOEs + 1 + 4 + 3) * 5;

    test_pageoverflow($pdf, $vyskaSekce, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zahlavi_personA($pdf,5,array(240,240,240),$persnr,$reportNameArray[$persnr],$personalinfo,$monat,$jahr,$typ,$sum_zapati_persnr_array['dzeit'][$persnr],$fullAccess);

    // nejdriv radky s oestatus='a'
    foreach($ogs as $og=>$oes) {
        $pocetOEs = count($oes);
        foreach($oes as $oekz=>$oeArray) {
            foreach($oeArray as $oestatus=>$oe) {
                if($oestatus=='a'){
                    $leistFactor = $sumOEProPersnr['dzeit'][$persnr][$oekz]['stunden']!=0?$sumOEProPersnr['leistung'][$persnr][$oekz]['vzkd']/$sumOEProPersnr['dzeit'][$persnr][$oekz]['stunden']:0;
                    $datumy_leistung = $oe['leistung'];
                    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
                    oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(245,255,245),$datumy_leistung,$oekz,"L",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$leistFactor);
                    $datumy_dzeit = $oe['dzeit'];
                    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
                    oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(255,245,245),$datumy_dzeit,$oekz,"A",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$leistFactor);
//                    $datumy_anwesenheit = $oe['dzeitanwesenheit'];
//                    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//                    oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(255,245,245),$datumy_anwesenheit,$oekz,"W",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$leistFactor);
//                    $datumy_soll = $oe['dzeit'];
//                    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//                    oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(255,245,245),$datumy_soll,$oekz,"S",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$leistFactor);
                }
            }
        }
        $faktory = array();
        $sumAnw=0;$sumvzkd=0;$sumAnwesenheit=0;$sumSoll=0;
        foreach($sum_zapati_sestava_array['leistung'] as $tag=>$leistung) {
            $vzkd = $sum_zapati_persnr_array['leistung'][$persnr][$tag]['vzkd'];
            $anw = $sum_zapati_persnr_array['dzeit'][$persnr][$tag]['stunden'];
            $anwesenheit = $sum_zapati_persnr_array['dzeitanwesenheit'][$persnr][$tag]['stundenanwesenheit'];
            $soll = $sum_zapati_persnr_array['dzeit'][$persnr][$tag]['stundensoll'];
            $sumvzkd+=$vzkd;
            $sumAnw+=$anw;
            $sumAnwesenheit+=$anwesenheit;
            $sumSoll+=$soll;
            $faktory['vzkd_zu_vzkdsoll'][$tag] = $anw!=0?$vzkd/$anw:FALSE;
            $faktory['vzkd_zu_anwesenheit'][$tag] = $anwesenheit!=0?$vzkd/$anwesenheit:FALSE;
            $faktory['vzkd_zu_anwesenheitsoll'][$tag] = $soll!=0?$vzkd/$soll:FALSE;
            $faktory['vzkdsoll_zu_anwesenheitsoll'][$tag] = $soll!=0?$anw/$soll:FALSE;
        }

        $faktory['vzkd_zu_vzkdsoll']['sum'] = $sumAnw!=0?$sumvzkd/$sumAnw:FALSE;
        $faktory['vzkd_zu_anwesenheit']['sum'] = $sumAnwesenheit!=0?$sumvzkd/$sumAnwesenheit:FALSE;
        $faktory['vzkd_zu_anwesenheitsoll']['sum'] = $sumSoll!=0?$sumvzkd/$sumSoll:FALSE;
        $faktory['vzkdsoll_zu_anwesenheitsoll']['sum'] = $sumSoll!=0?$sumAnw/$sumSoll:FALSE;

    }

//echo "<pre>";
//print_r($faktory);
//echo "</pre>";

    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personA($pdf,5,array(245,255,245),$persnr,"L",$sum_zapati_persnr_array['leistung'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personA($pdf,5,array(255,245,245),$persnr,"A",$sum_zapati_persnr_array['dzeit'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personA($pdf,5,array(255,245,245),$persnr,"W",$sum_zapati_persnr_array['dzeitanwesenheit'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personA($pdf,5,array(255,245,245),$persnr,"S",$sum_zapati_persnr_array['dzeit'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personAfaktory($pdf,5,array(255,255,255),$persnr,"A",$faktory['vzkd_zu_vzkdsoll'],$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd/vzkd_s");
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personAfaktory($pdf,5,array(255,255,255),$persnr,"A",$faktory['vzkd_zu_anwesenheit'],$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd/Anw.");
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_personAfaktory($pdf,5,array(255,255,255),$persnr,"A",$faktory['vzkd_zu_anwesenheitsoll'],$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd/Anw.soll");
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zapati_personAfaktory($pdf,5,array(255,255,255),$persnr,"A",$faktory['vzkdsoll_zu_anwesenheitsoll'],$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd_s/Anw.soll");

    $pdf->Cell(0,2,"",'0',1,'L',0);
}

// oe, ktera maji oestatus='a'
$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);

$ogs = $sum_oe_array['leistung'];
foreach($ogs as $og=>$oeArray) {
    foreach($oeArray as $oestatus=>$summenarray){
        if($oestatus=='a'){
            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
        }
    }
}

$ogs = $sum_oe_array['dzeit'];
foreach($ogs as $og=>$oeArray) {
    foreach($oeArray as $oestatus=>$summenarray){
        if($oestatus=='a'){
            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"A",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
        }
    }
}

$ogs = $sum_oe_array['dzeitanwesenheit'];
foreach($ogs as $og=>$oeArray) {
    foreach($oeArray as $oestatus=>$summenarray){
        if($oestatus=='a'){
            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"W",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
        }
    }
}

$ogs = $sum_oe_array['dzeit'];
foreach($ogs as $og=>$oeArray) {
    foreach($oeArray as $oestatus=>$summenarray){
        if($oestatus=='a'){
            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"S",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
        }
    }
}

$pdf->Ln();

test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestava($pdf,5,array(245,255,245),$sum_zapati_sestava_array['leistung'],"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);

test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestava($pdf,5,array(255,245,245),$sum_zapati_sestava_array['dzeit'],"A",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);

test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestava($pdf,5,array(255,245,245),$sum_zapati_sestava_array['dzeitanwesenheit'],"W",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);

test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestava($pdf,5,array(255,245,245),$sum_zapati_sestava_array['dzeit'],"S",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);

$faktorySestava = array();$sumvzkd=0;$sumAnw=0;$sumAnwesenheit=0;$sumSoll=0;
foreach($sum_zapati_sestava_array['leistung'] as $tag=>$leistung) {
    $vzkd = $leistung['vzkd'];
    $anw = $sum_zapati_sestava_array['dzeit'][$tag]['stunden'];
    $anwesenheit = $sum_zapati_sestava_array['dzeitanwesenheit'][$tag]['stundenanwesenheit'];
    $soll = $sum_zapati_sestava_array['dzeit'][$tag]['stundensoll'];
    $sumvzkd+=$vzkd;
    $sumAnw+=$anw;
    $sumAnwesenheit+=$anwesenheit;
    $sumSoll+=$soll;
    $faktorySestava['vzkd_zu_vzkdsoll'][$tag] = $anw!=0?$vzkd/$anw:0;
    $faktorySestava['vzkd_zu_anwesenheit'][$tag] = $anwesenheit!=0?$vzkd/$anwesenheit:0;
    $faktorySestava['vzkd_zu_anwesenheitsoll'][$tag] = $soll!=0?$vzkd/$soll:0;
    $faktorySestava['vzkdsoll_zu_anwesenheitsoll'][$tag] = $soll!=0?$anw/$soll:0;
}
$faktorySestava['vzkd_zu_vzkdsoll']['sum'] = $sumAnw!=0?$sumvzkd/$sumAnw:0;
$faktorySestava['vzkd_zu_anwesenheit']['sum'] = $sumAnwesenheit!=0?$sumvzkd/$sumAnwesenheit:0;
$faktorySestava['vzkd_zu_anwesenheitsoll']['sum'] = $sumSoll!=0?$sumvzkd/$sumSoll:0;
$faktorySestava['vzkdsoll_zu_anwesenheitsoll']['sum'] = $sumSoll!=0?$sumAnw/$sumSoll:0;


test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestavafaktory($pdf,5,array(255,255,255),$faktorySestava['vzkd_zu_vzkdsoll'],"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd/vzkd_s");

test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestavafaktory($pdf,5,array(255,255,255),$faktorySestava['vzkd_zu_anwesenheit'],"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd/Anw.");

//test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//zapati_sestavafaktory($pdf,5,array(255,255,255),$faktorySestava['vzkd_zu_anwesenheitsoll'],"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd/Anw.Soll");

test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
zapati_sestavafaktory($pdf,5,array(255,255,255),$faktorySestava['vzkdsoll_zu_anwesenheitsoll'],"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis,"vzkd_s/Anw.Soll");

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>