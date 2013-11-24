<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S186";
$doc_subject = "S186 Report";
$doc_keywords = "S186";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

define(PERSNRWIDTH,9);
define(NAMEWIDTH, 30);

require_once('S186_xml.php');



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

$sum_zapati_sestava_array;

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


/**
 * funkce pro vykresleni hlavicky na kazde strance
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdfobjekt,$pole,$headervyskaradku,$jahr,$monat,$svatky) {

    $aktualniDen = date('d');
    $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

    $days = array("So","Mo","Di","Mi","Do","Fr","Sa");

    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->SetFillColor(255,255,200,1);
    $fill = 1;
//    $pdfobjekt->Cell(10,5,"OE",'1',0,'L',$fill);
    $sirkabunky = ($pdfobjekt->getPageWidth()-NAMEWIDTH-PERSNRWIDTH-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;

    $markActual = false;
    $actualMonat = date('m');
    if($actualMonat==$monat) $markActual = true;

    // cisla dnu
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->Cell(NAMEWIDTH+PERSNRWIDTH,5,"",'1',0,'L',$fill);
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
    $pdfobjekt->Cell(NAMEWIDTH+PERSNRWIDTH,5,"Persnr / Datum",'1',0,'L',$fill);
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



function person_radek($pdfobjekt,$vyskaradku,$rgb,$person,$monat,$jahr,$svatky){

        $fill = 1;
        $childs = $person->childNodes;
        global $sum_zapati_sestava_array;

        $pdfobjekt->SetFont("FreeSans", "", 7);
        $persnr = getValueForNode($childs, 'persnr');
        $name = getValueForNode($childs, 'name');

        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

        $pdfobjekt->Cell(PERSNRWIDTH,$vyskaradku,$persnr,'1',0,'R',$fill);
        $pdfobjekt->Cell(NAMEWIDTH,$vyskaradku,$name,'1',0,'L',$fill);

        $aktualniDen = date('d');
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

        $markActual = false;
        $actualMonat = date('m');
        if($actualMonat==$monat) $markActual = true;

        $sirkabunky = ($pdfobjekt->getPageWidth()-NAMEWIDTH-PERSNRWIDTH-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $suma = 0;
        $pocet = 0;

        $tagvon = 1;$tagbis=$pocetDnuVMesici;

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
                $datumy = $person->getElementsByTagName('tag');
                    foreach($datumy as $datum) {
                        $datumChilds = $datum->childNodes;
                        $dat = getValueForNode($datumChilds, 'datum');
                        $essen = intval(getValueForNode($datumChilds, 'essen'));
                        $tag = intval(substr($dat, 8));
                        if($tag==$den) {
                            if($essen==0) {
                                $pdfobjekt->Cell($sirkabunky,$vyskaradku,'0','1',0,'R',$fill);
                                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                            }
                            else {
                                $suma += $essen;
                                $pdfobjekt->SetFillColor(255,200,200,1);
                                $pdfobjekt->Cell($sirkabunky,$vyskaradku,$essen,'1',0,'R',$fill);
                                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                                $sum_zapati_sestava_array['sum'][$den] += $essen;
                                $sum_zapati_sestava_array['count'][$den] += 1;
                                $pocet++;
                            }
                            $kresliPrazdny=0;
                            break;
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
        $suma = number_format($suma, 0);
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $pdfobjekt->Cell(4,$vyskaradku,$pocet,'1',0,'R',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(0,$vyskaradku,$suma,'1',0,'R',$fill);
        $pdfobjekt->Ln();
}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$pole,$monat,$jahr,$svatky,$text){

        $fill = 1;

        $pdfobjekt->SetFont("FreeSans", "B", 6.5);

        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

        //$pdfobjekt->Ln();
        $pdfobjekt->Cell(PERSNRWIDTH+NAMEWIDTH,$vyskaradku,$text,'1',0,'L',$fill);

        $aktualniDen = date('d');
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

        $markActual = false;
        $actualMonat = date('m');
        if($actualMonat==$monat) $markActual = true;

        $sirkabunky = ($pdfobjekt->getPageWidth()-NAMEWIDTH-PERSNRWIDTH-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-10)/$pocetDnuVMesici;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $suma = 0;
        $tagvon = 1;$tagbis=$pocetDnuVMesici;
        for($d=1;$d<=$tagbis;$d++) $datumy[$d-1]=$d;

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

                    foreach($datumy as $datum) {
                        $essen = $pole[$datum];
                        $tag = $datum;
                        if($tag==$den) {
                            if($essen==0) {
                                $pdfobjekt->Cell($sirkabunky,$vyskaradku,'0','1',0,'R',$fill);
                                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                            }
                            else {
                                $suma += $essen;
                                //$pdfobjekt->SetFillColor(255,200,200,1);
                                $pdfobjekt->Cell($sirkabunky,$vyskaradku,$essen,'1',0,'R',$fill);
                                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                            }
                            $kresliPrazdny=0;
                            break;
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
        $suma = number_format($suma, 0,',',' ');
        $pdfobjekt->Cell(0,$vyskaradku,$suma,'1',0,'R',$fill);
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
function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$jahr,$monat,$svatky)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,'',5,$jahr,$monat,$svatky);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S186 Essenuebersicht", $params);
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

$svatkyArray = naplnPoleSvatku($jahr,$monat);

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,5,$jahr,$monat,$svatkyArray);
$personen = $domxml->getElementsByTagName('person');
foreach($personen as $person){
    test_pageoverflow($pdf,5,'',$jahr,$monat,$svatkyArray);
    person_radek($pdf, 5, array(255,255,255), $person, $monat, $jahr,$svatkyArray);
}


test_pageoverflow($pdf,15,'',$jahr,$monat,$svatkyArray);
$pdf->Ln();
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['count'], $monat, $jahr,$svatkyArray,"Anzahl");
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['sum'], $monat, $jahr,$svatkyArray,"Betrag");

$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>