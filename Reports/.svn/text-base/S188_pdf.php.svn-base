<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

// report tisk prehledu dopravy pro jednotlice zamesynance

$doc_title = "S188";
$doc_subject = "S188 Report";
$doc_keywords = "S188";

// necham si vygenerovat XML

$parameters=$_GET;
$monat = $_GET['monat'];
$jahr = $_GET['jahr'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];


$von = $jahr."-".$monat."-01";
$pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);
$bis = $jahr."-".$monat."-".$pocetDnuVMesici;

define(PERSNRWIDTH,9);
define(NAMEWIDTH, 25);

require_once('S188_xml.php');

//exit;

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

$sum_zapati_sestava_array = array();

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
    $sirkabunky = ($pdfobjekt->getPageWidth()-NAMEWIDTH-PERSNRWIDTH-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-5)/$pocetDnuVMesici;

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


/**
 *
 * @global <type> $sum_zapati_sestava_array
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $person
 * @param <type> $monat
 * @param <type> $jahr
 * @param <type> $svatky
 */
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

        $sirkabunky = ($pdfobjekt->getPageWidth()-NAMEWIDTH-PERSNRWIDTH-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-5)/$pocetDnuVMesici;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $suma = 0;
        $pocet = 0;

        $tagvon = 1;$tagbis=$pocetDnuVMesici;

        $pdfobjekt->SetFont("FreeSans", "", 6);
        for($den=1;$den<=$pocetDnuVMesici;$den++) {
            if(($den>=$tagvon) && ($den<=$tagbis)) {
                // oznaceni aktualniho dne
                if($aktualniDen==$den && $markActual) {
                    $pdfobjekt->SetLineWidth(0.5);
                    $prevcolor = $pdfobjekt->SetDrawColor(255,0,0);
                }
                //echo "den=$den ";
                // oznaceni so+ne a svatku
                $workday = date('w',mktime(1, 1, 1, $monat, $den, $jahr));
                if($workday==6 || $workday==0 || in_array($den, $svatky))
                    $pdfobjekt->SetFillColor(245, 245, 255,1);

                $kresliPrazdny = 1;
                $datumy = $person->getElementsByTagName('tag');
                    foreach($datumy as $datum) {
                        $datumChilds = $datum->childNodes;
                        $dat = getValueForNode($datumChilds, 'datum');
                        //echo "datum=$dat ";
                        $tag = intval(substr($dat, 8));
                        $autos = $datum->getElementsByTagName('kfz');
                        $autoRadek = 1;
                        foreach($autos as $auto){
                            //echo "autoRadek=$autoRadek<br>";
                            $autoChilds = $auto->childNodes;
                            $marke = getValueForNode($autoChilds, 'marke');
                            $preis = intval(getValueForNode($autoChilds, 'preis'));
                            $sitzen = intval(getValueForNode($autoChilds, 'sitzen'));
                            $rz = getValueForNode($autoChilds, 'rz');
                            $sum_zapati_sestava_array[$marke]['sitzen'] = $sitzen;
                            $sum_zapati_sestava_array[$marke]['rz'] = $rz;
                            if($tag==$den) {
                                    $suma += $preis;
                                    $sum_zapati_sestava_array[$marke]['count'][$den] += 1;
                                    $sum_zapati_sestava_array[$marke]['preis'][$den] += $preis;
                                    
                                    //pro celkovou sumu pouziju ZZZ
                                    if($sitzen!=0) $sum_zapati_sestava_array['ZZZ']['countsitzen'][$den] += 1;
                                    $sum_zapati_sestava_array['ZZZ']['count'][$den] += 1;
                                    if($preis!=0)
                                        $sum_zapati_sestava_array['ZZZ']['countmitpreis'][$den] += 1;
                                    $sum_zapati_sestava_array['ZZZ']['preis'][$den] += $preis;
                                    if($autoRadek==1){
                                        //horniradek
                                        $pdfobjekt->SetFillColor(255,200,200,1);
                                        $pdfobjekt->SetFont("FreeSans", "", 4);
                                        $pdfobjekt->Cell($sirkabunky/2,$vyskaradku/2,$marke,'1',0,'L',$fill);
                                        $pdfobjekt->SetFont("FreeSans", "", 6);
                                        $pdfobjekt->Cell($sirkabunky/2,$vyskaradku/2,$preis,'1',0,'R',$fill);
                                    }
                                    else{
                                        //spodniradek
                                        $pdfobjekt->SetFillColor(200,255,200,1);
                                        $x=$pdfobjekt->GetX();$y=$pdfobjekt->GetY();
                                        $pdfobjekt->SetXY($x-$sirkabunky, $y+$vyskaradku/2);
                                        $pdfobjekt->SetFont("FreeSans", "", 4);
                                        $pdfobjekt->Cell($sirkabunky/2,$vyskaradku/2,$marke,'1',0,'L',$fill);
                                        $pdfobjekt->SetFont("FreeSans", "", 6);
                                        $pdfobjekt->Cell($sirkabunky/2,$vyskaradku/2,$preis,'1',0,'R',$fill);
                                        $pdfobjekt->SetXY($x, $y);
                                    }
                                    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
                                    $pocet++;
                                    $kresliPrazdny=0;
                                    //break;
                                    $autoRadek++;
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
        $suma = number_format($suma, 0,',',' ');
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $pdfobjekt->Cell(6,$vyskaradku,$pocet,'1',0,'R',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(0,$vyskaradku,$suma,'1',0,'R',$fill);
        $pdfobjekt->Ln();
}


function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$pole,$monat,$jahr,$svatky,$auto,$text,$sitzen=0,$rz=NULL,$dbDatumVon=NULL,$dbDatumBis=NULL){

        $fill = 1;

        $apl = AplDB::getInstance();

        $pdfobjekt->SetFont("FreeSans", "B", 6.5);

        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

        //$pdfobjekt->Ln();
        if($sitzen!=0)
            $sitzenText = " ( ".$sitzen." )";
        else
            $sitzenText = '';

        //v druhem radku u prehledu aut zobrazim misto zkratky registracni znacku
        if(($auto!='Sum') && ($text=='Preis')){
            $pdfobjekt->SetFont("FreeSans", "", 5.5);
            $pdfobjekt->Cell(PERSNRWIDTH,$vyskaradku,$rz,'1',0,'L',$fill);
            $pdfobjekt->SetFont("FreeSans", "B", 6.5);
        }
        else{
            $pdfobjekt->Cell(PERSNRWIDTH,$vyskaradku,$auto,'1',0,'L',$fill);
        }
        $pdfobjekt->Cell(NAMEWIDTH,$vyskaradku,$text.$sitzenText,'1',0,'L',$fill);

        $aktualniDen = date('d');
        $pocetDnuVMesici = cal_days_in_month(CAL_GREGORIAN, $monat, $jahr);

        $markActual = false;
        $actualMonat = date('m');
        if($actualMonat==$monat) $markActual = true;

        $sirkabunky = ($pdfobjekt->getPageWidth()-NAMEWIDTH-PERSNRWIDTH-PDF_MARGIN_RIGHT-PDF_MARGIN_LEFT-5)/$pocetDnuVMesici;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $suma = 0;
        $tagvon = 1;$tagbis=$pocetDnuVMesici;
        for($d=1;$d<=$tagbis;$d++) $datumy[$d-1]=$d;

//        if($text=='Soll MA'){
//            var_dump($pole);
//        }

        for($den=1;$den<=$pocetDnuVMesici;$den++) {
//            echo "<br>$datumDB";
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
                                    if($text=='Auslastung [%]'){
                                    $datumDB = sprintf("%04d-%02d-%02d",$jahr,$monat,$den);
                                    $sollAnzahl = $apl->getSollMAFahrtDatum($datumDB);
                                    if($sollAnzahl!=0)
                                        $anteil = round($essen/$sollAnzahl*100);
                                    else
                                        $anteil = 0;

                                    $essen=$anteil;
                                }
                                else if($text=='Anzahl MA soll'){
                                    $datumDB = sprintf("%04d-%02d-%02d",$jahr,$monat,$den);
                                    $sollAnzahl = $apl->getSollMAFahrtDatum($datumDB);
                                    $essen = $sollAnzahl;
                                }
                                $suma += $essen;
                                if($essen==0) $essen = '';
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
        if($text=='Anzahl MA'){
//            echo "<br> von = $dbDatumVon,bis = $dbDatumBis,$auto";
            $sollArray = $apl->getSollMAFahrtenBetweenDatums($dbDatumVon, $dbDatumBis, $auto);
            if($sollArray!==NULL){
                $sollMA = $sollArray[0]['ma_soll']!=0?$suma / $sollArray[0]['ma_soll']:0;
                if($sollMA!=0)
                    $ausLastungObsah = number_format($sollMA*100, 0,',',' ');
                else
                    $ausLastungObsah = '';
            }
            else{
                $ausLastungObsah = '';
            }

            $pdfobjekt->SetFont("FreeSans", "", 5.5);
            $pdfobjekt->Cell(6,$vyskaradku,$ausLastungObsah,'1',0,'R',$fill);
            $suma = number_format($suma, 0,',',' ');
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(0,$vyskaradku,$suma,'1',0,'R',$fill);
            $pdfobjekt->Ln();
        }
        else{
            // suma hodin pro danou cinnost
            $suma = number_format($suma, 0,',',' ');
            if($text=='Auslastung [%]') $suma='';
            $pdfobjekt->Cell(0,$vyskaradku,$suma,'1',0,'R',$fill);
            $pdfobjekt->Ln();
        }
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S188 Transportuebersicht", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT-5);
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


//test_pageoverflow($pdf,15,'',$jahr,$monat,$svatkyArray);
$pdf->Ln();
if(count($sum_zapati_sestava_array)>0){
$autos = array_keys($sum_zapati_sestava_array);

//echo "<pre>";
//var_dump($sum_zapati_sestava_array);
//echo "</pre>";
 // ypetne roylisit sumu podle klicu.

sort($autos);
foreach($autos as $auto){
    if($auto!='ZZZ'){
        zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array[$auto]['count'], $monat, $jahr,$svatkyArray,$auto,"Anzahl MA",$sum_zapati_sestava_array[$auto]['sitzen'],$sum_zapati_sestava_array[$auto]['rz'],$von,$bis);
        zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array[$auto]['preis'], $monat, $jahr,$svatkyArray,$auto,"Preis",0,$sum_zapati_sestava_array[$auto]['rz'],$von,$bis);
    }
}

$soucetSedadel = 0;
foreach($autos as $auto){
    if($auto!='ZZZ'){
        $soucetSedadel += $sum_zapati_sestava_array[$auto]['sitzen'];
    }
}

//echo "soucet sedadel = $soucetSedadel";

$pdf->Ln();
//zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['countsitzen'], $monat, $jahr,$svatkyArray,"Sum","Auslastung [%]");
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['count'], $monat, $jahr,$svatkyArray,"Sum","Auslastung [%]");
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['countsitzen'], $monat, $jahr,$svatkyArray,"Sum","Anzahl MA soll",$soucetSedadel);
//zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['countsitzen'], $monat, $jahr,$svatkyArray,"Sum","Anzahl MA ber.");
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['count'], $monat, $jahr,$svatkyArray,"Sum","Anzahl  MA");
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['countmitpreis'], $monat, $jahr,$svatkyArray,"Sum","Anzahl MA bez.");
zapati_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array['ZZZ']['preis'], $monat, $jahr,$svatkyArray,"Sum","Preis");
}
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
