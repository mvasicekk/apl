<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S185";
$doc_subject = "S185 Report";
$doc_keywords = "S185";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

$tagvon = 1;
$tagbis = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
require_once('S185_xml.php');

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

//global $oeFarbenArray;

$sum_zapati_persnr_array;
global $sum_zapati_persnr_array;

$sum_zapati_sestava_array;
global $sum_zapati_sestava_array;

//$sumOEProPersnr = array();
//global $sumOEProPersnr;

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
    $pdfobjekt->Cell(10+10,5,"",'1',0,'L',$fill);
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

        if($typ=='A')
            $typAppendix = "Anw[Std]";
        else
            $typAppendix = "VzAby[Std]";
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

                if($typ=='A')
                    $stunden = $sArray[$i]['stunden'];
                else
                    $stunden = $sArray[$i]['vzaby'];

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
    if($typ=='A')
        $typAppendix = "Anw[Std]";
    else
        $typAppendix = "VzAby[Std]";
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

            if($typ=='A')
                $stunden = $sumArray[$i]['stunden'];
            else
                $stunden = $sumArray[$i]['vzaby'];

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
function zapati_sestavafaktory($pdfobjekt,$vyskaradku,$rgb,$sumArray,$typ, $monat, $jahr,$svatky,$tagvon,$tagbis) {
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $pdfobjekt->SetFont("FreeSans", "B", 6);
    $pdfobjekt->Cell(10+10,$vyskaradku,"VzAby/Anw",'1',0,'L',$fill);
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

    if($typ=='A')
        $typAppendix = "Anw[Std]";
    else
        $typAppendix = "VzAby[Std]";

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

            if($typ=='A')
                $stunden = $sumArray[$i]['stunden'];
            else
                $stunden = $sumArray[$i]['vzaby'];

            $sumaCelkem += $stunden;
            if($typ=='A')
                $stunden = number_format($stunden, 2,',',' ');
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
function zapati_personAfaktory($pdfobjekt,$vyskaradku,$rgb,$persnr,$typ,$sumArray,$monat,$jahr,$svatky,$tagvon,$tagbis) {

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;
    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    //	$pdfobjekt->Cell(10,$vyskaradku,$persnr,'1',0,'L',$fill);
    $pdfobjekt->Cell(10+10,$vyskaradku,"VzAby/Anw",'1',0,'L',$fill);

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

    $pdfobjekt->Cell(10,$vyskaradku,$sumaCelkem,'1',1,'R',$fill);

    $pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $persnr
 * @param <type> $nameArray
 */
function zahlavi_personA($pdfobjekt,$vyskaradku,$rgb,$persnr,$name,$monat,$jahr) {
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;

    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
    $sirkabunky = ($pdfobjekt->getPageWidth()-10-3-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

    $jmeno = $name;

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
function oe_radekA($pdfobjekt,$vyskaradku,$rgb,$datumy,$oekz,$typ,$ityp,$monat,$jahr,$svatky,$tagvon,$tagbis) {


    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->Cell(10,$vyskaradku,$oekz,'1',0,'L',$fill);

    if($typ=='dzeit')
        $typAppendix = "Anw[Std]";
    else if($typ=='essen')
        $typAppendix = "[Kc]";
    else if($typ=='trans')
        $typAppendix = "[Anz.] [Kc]";


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
                foreach($datumy as $dat=>$stundenA) {
                    $tag = intval(substr($dat, 8));
                    if($typ=='dzeit')
                        $stunden = floatval($stundenA['stunden']);
                    else if($typ=='essen')
                            $stunden = floatval($stundenA['essen_preis']);
                    else if($typ=='trans'){
                            $stunden = floatval($stundenA['trans_preis']);
                            $count = floatval($stundenA['trans_count']);
                    }
                    else
                        $stunden = 0;

                    if($tag==$den) {
                        if(($stunden==0)&&($typ!='trans')) {
                            $stunden = '';
                            $pdfobjekt->Cell($sirkabunky,$vyskaradku,$stunden,'1',0,'R',$fill);
                            $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                        }
                        else {
                            $sumaHodin += $stunden;
                            if($typ=='dzeit')
                                $stunden = number_format($stunden, 2,',',' ');
                            else if($typ=='essen')
                                    $stunden = number_format($stunden, 0,',',' ');
                            else if($typ=='trans')
                                    $stunden = number_format($stunden, 0,',',' ');


                            $pdfobjekt->SetFillColor(255,255,255,1);
                            if($typ=='trans'){
                                $pdfobjekt->SetFont("FreeSans", "", 5);
                                $pdfobjekt->Cell($sirkabunky/3,$vyskaradku,$count,'LBT',0,'R',$fill);
                                $pdfobjekt->SetFont("FreeSans", "", 7);
                                $pdfobjekt->Cell($sirkabunky/3*2,$vyskaradku,$stunden,'RBT',0,'R',$fill);
                            }
                            else
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
    if($typ=='dzeit')
        $sumaHodin = number_format($sumaHodin, 2,',',' ');
    else if($typ=='essen')
        $sumaHodin = number_format($sumaHodin, 0,',',' ');
    $pdfobjekt->SetFillColor(255,255,255,1);
    $pdfobjekt->SetFont("FreeSans", "B", 7);
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
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S185 Anwesenheit/Essen/Transport", $arbtageText.$params);

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
$sollIstArray = array("dzeit","essen","trans");



// dam dohromady stromy s dochazkou,jidlem,transportem

foreach($sollIstArray as $sollIst){
    $planTree = $domxml->getElementsByTagName($sollIst)->item(0);
    // vytvorim si pole vsech pritomnych lidi
    $personen=$planTree->getElementsByTagName("pers");
    foreach($personen as $person){
        $personChilds = $person->childNodes;
        $persnr = getValueForNode($personChilds, "persnr");
        $name = getValueForNode($personChilds, "name");
//        echo "<br>name:$name";
        if(strlen(trim($name))>0)
            $reportNameArray[$persnr] = $name;
        $oes = $person->getElementsByTagName("oe");
        foreach($oes as $oe){
            $oeChilds = $oe->childNodes;
            $oekz = getValueForNode($oeChilds, "oekz");
            $tage = $oe->getElementsByTagName("tag");
            foreach($tage as $tag){
                $tagChilds = $tag->childNodes;
                $datum = getValueForNode($tagChilds, "datum");
                $stunden = getValueForNode($tagChilds, "stunden");
                $essen_preis = getValueForNode($tagChilds, "essen_preis");
                $reportArray[$persnr][$sollIst][$oekz][$datum]['stunden']=$stunden;
                $reportArray[$persnr][$sollIst][$oekz][$datum]['essen_preis']=$essen_preis;
                $reportArray[$persnr][$sollIst][$oekz][$datum]['trans_preis']=getValueForNode($tagChilds, "trans_preis");
                $reportArray[$persnr][$sollIst][$oekz][$datum]['trans_count']=getValueForNode($tagChilds, "trans_count");
            }
        }
    }
    if(is_array($reportArray[$persnr][$sollIst])) ksort($reportArray[$persnr][$sollIst]);
}

ksort($reportArray);

//echo "<pre>";
//    print_r($reportArray);
//echo "</pre>";
//
//exit();
$svatkyArray = naplnPoleSvatku($jahr,$monat);

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);

foreach($reportArray as $persnr=>$person) {
    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
    zahlavi_personA($pdf,5,array(240,240,240),$persnr,$reportNameArray[$persnr],$monat,$jahr,$typ);
    foreach($sollIstArray as $sollIst){
        $oes = $reportArray[$persnr][$sollIst];
//        echo "<pre>";
//        print_r($oes);
//        echo "</pre>";

        if(is_array($oes)){
            foreach($oes as $oekz=>$oeArray) {
                $datumy_dzeit = $oeArray;
                test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
                oe_radekA($pdf,5,array(255,245,245),$datumy_dzeit,$oekz,$sollIst,$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
            }
        }
    }
}
//        }
//        $faktory = array();
//        $sumAnw=0;$sumVzaby=0;
//        if (!$nurAnw) {
////            echo "<pre>";
////                print_r($sum_zapati_sestava_array);
////            echo "</pre>";
//
//            foreach ($sum_zapati_sestava_array['leistung'] as $tag => $leistung) {
//                $vzaby = $sum_zapati_persnr_array['leistung'][$persnr][$tag]['vzaby'];
//                $anw = $sum_zapati_persnr_array['dzeit'][$persnr][$tag]['stunden'];
//                $sumVzaby+=$vzaby;
//                $sumAnw+=$anw;
//                $faktory[$tag] = $anw != 0 ? $vzaby / $anw : 0;
//            }
//            $faktory['sum'] = $sumAnw != 0 ? $sumVzaby / $sumAnw : 0;
//        }
//    }
//
//    if(!$nurAnw){
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_personA($pdf,5,array(245,255,245),$persnr,"L",$sum_zapati_persnr_array['leistung'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//    }
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_personA($pdf,5,array(255,245,245),$persnr,"A",$sum_zapati_persnr_array['dzeit'][$persnr],$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//    if(!$nurAnw){
//    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//    zapati_personAfaktory($pdf,5,array(255,255,255),$persnr,"A",$faktory,$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//    }
//
//    // radky s oestatus !='a'
//    foreach($ogs as $og=>$oes) {
//        $pocetOEs = count($oes);
//        foreach($oes as $oekz=>$oeArray) {
//            foreach($oeArray as $oestatus=>$oe) {
//                if($oestatus!='a'){
//                    $leistFactor = $sumOEProPersnr['dzeit'][$persnr][$oekz]['stunden']!=0?$sumOEProPersnr['leistung'][$persnr][$oekz]['vzaby']/$sumOEProPersnr['dzeit'][$persnr][$oekz]['stunden']:0;
////                    $datumy_leistung = $oe['leistung'];
////                    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
////                    oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(245,255,245),$datumy_leistung,$oekz,"L",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$leistFactor);
//                    $datumy_dzeit = $oe['dzeit'];
//                    test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//                    oe_radekA($oeFarbenArray[$oekz],$pdf,5,array(255,245,245),$datumy_dzeit,$oekz,"A",$typ,$monat,$jahr,$svatkyArray,$tagvon,$tagbis,$leistFactor);
//                }
//            }
//        }
//    }
//
//    $pdf->Cell(0,2,"",'0',1,'L',0);
//}
//
//// oe, ktera maji oestatus='a'
//$pdf->AddPage();
//pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);
//
//if (!$nurAnw) {
//    $ogs = $sum_oe_array['leistung'];
//    if(is_array($ogs)){
//    foreach ($ogs as $og => $oeArray) {
//        foreach ($oeArray as $oestatus => $summenarray) {
//            if ($oestatus == 'a') {
//                test_pageoverflow($pdf, 5, $cellhead, $jahr, $monat, $svatkyArray, $pracDobaA);
//                zapati_oes($oeFarbenArray, $pdf, 4, array(255, 245, 245), $summenarray, "L", $monat, $jahr, $svatkyArray, $tagvon, $tagbis);
//            }
//        }
//    }
//    }
//}
//
//$ogs = $sum_oe_array['dzeit'];
//if(is_array($ogs)){
//foreach($ogs as $og=>$oeArray) {
//    foreach($oeArray as $oestatus=>$summenarray){
//        if($oestatus=='a'){
//            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"A",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//        }
//    }
//}
//}
//
//$pdf->Ln();
//
//if(!$nurAnw){
//test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//zapati_sestava($pdf,5,array(245,255,245),$sum_zapati_sestava_array['leistung'],"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//}
//
//test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//zapati_sestava($pdf,5,array(255,245,245),$sum_zapati_sestava_array['dzeit'],"A",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//
//
//if(!$nurAnw){
//$faktorySestava = array();$sumVzaby=0;$sumAnw=0;
//if(is_array($sum_zapati_sestava_array['leistung'])){
//foreach($sum_zapati_sestava_array['leistung'] as $tag=>$leistung) {
//    $vzaby = $leistung['vzaby'];
//    $anw = $sum_zapati_sestava_array['dzeit'][$tag]['stunden'];
//    $sumVzaby+=$vzaby;
//    $sumAnw+=$anw;
//    $faktorySestava[$tag] = $anw!=0?$vzaby/$anw:0;
//}
//}
//$faktorySestava['sum'] = $sumAnw!=0?$sumVzaby/$sumAnw:0;
//
//test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//zapati_sestavafaktory($pdf,5,array(255,255,255),$faktorySestava,"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//}
//
//// oe, ktera maji oestatus!='a'
//$pdf->Ln();
////pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray,$pracDobaA);
//
//if(!$nurAnw){
//$ogs = $sum_oe_array['leistung'];
//if(is_array($ogs)){
//foreach($ogs as $og=>$oeArray) {
//    foreach($oeArray as $oestatus=>$summenarray){
//        if($oestatus!='a'){
//            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"L",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//        }
//    }
//}
//}
//}
//
//$ogs = $sum_oe_array['dzeit'];
//if(is_array($ogs)){
//foreach($ogs as $og=>$oeArray) {
//    foreach($oeArray as $oestatus=>$summenarray){
//        if($oestatus!='a'){
//            test_pageoverflow($pdf, 5, $cellhead,$jahr,$monat,$svatkyArray,$pracDobaA);
//            zapati_oes($oeFarbenArray,$pdf,4,array(255,245,245),$summenarray,"A",$monat,$jahr,$svatkyArray,$tagvon,$tagbis);
//        }
//    }
//}
//}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>