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


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2


$cells = 
array(
"abgnr"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"abgnr_name"
=> array ("substring"=>array(0,30),"popis"=>"","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"preis"
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd"
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"bed_2011_preis"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
//"bed_2011_vzkd"
//=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"bed_2012_preis"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
//"bed_2012_vzkd"
//=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"ln"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);



$sum_zapati_teil = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_2011_vzkd'=>0,
    'bed_2012_vzkd'=>0,
    'bed_2011_preis'=>0,
    'bed_2012_preis'=>0,
);

$sum_zapati_sestava = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_2011_vzkd'=>0,
    'bed_2012_vzkd'=>0,
    'zielpreis_2011'=>0,
    'zielpreis_2012'=>0,
    'kosten_auss_stk'=>0,
    'tonnen_2011'=>0,
    'tonnen_2012'=>0,
    'bed_2011_preis'=>0,
    'bed_2012_preis'=>0,
);

$sum_zapati_sestava_abgnr = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_2011_vzkd'=>0,
    'bed_2012_vzkd'=>0,
    'zielpreis_2011'=>0,
    'zielpreis_2012'=>0,
    'tonnen_2011'=>0,
    'tonnen_2012'=>0,
    'bed_2011_preis'=>0,
    'bed_2012_preis'=>0,
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
//"abgnr"
//"abgnr_name"
//"preis"
//"vzkd"
//"bed_2011_preis"
//"bed_2011_vzkd"
//"bed_2012_preis"
//"bed_2012_vzkd"

    global $cells;
    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    $fill = 1;
    $pdfobjekt->Cell($cells['abgnr']['sirka'], $headervyskaradku, "", '0', 0, 'R', 0);
    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $headervyskaradku, "", '0', 0, 'L', 0);
    $pdfobjekt->Cell($cells['preis']['sirka'], $headervyskaradku, "Preis [EUR]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Cell($cells['vzkd']['sirka'], $headervyskaradku, "VzKd [min]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->SetFont("FreeSans", "B", 5.5);
    $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $headervyskaradku, "Jahresbedarf2012 [EUR]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $headervyskaradku, "Jahresbedarf2013 [EUR]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Ln();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
function zahlavi_teil($pdfobjekt,$vyskaRadku,$childNodes)
{

//"abgnr"
//"abgnr_name"
//"preis"
//"vzkd"
//"bed_2011_preis"
//"bed_2012_preis"
//"ln"

        global $cells;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=1;
        $pdfobjekt->Cell($cells['abgnr']['sirka'],$vyskaRadku,getValueForNode($childNodes,"teilnr"),'LBT',0,'L',$fill);
        $pdfobjekt->Cell(
                $cells['abgnr_name']['sirka']+$cells['preis']['sirka'],
                $vyskaRadku,getValueForNode($childNodes,"teilbez"),'BT',0,'L',$fill);

        //musterplatz
        $pdfobjekt->Cell(
                $cells['vzkd']['sirka'],
                $vyskaRadku,'Musterplatz: '.getValueForNode($childNodes,"musterplatz"),'LT',0,'L',$fill);

        // freigabe1
        $pdfobjekt->Cell(
                130,
                $vyskaRadku,
                ' Freigabe1 am: '.getValueForNode($childNodes,"freigabe1vom").
                ' von: '.getValueForNode($childNodes,"freigabe1")
                .' Freigabe2 am: '.getValueForNode($childNodes,"freigabe2vom").
                ' von: '.getValueForNode($childNodes,"freigabe2")
                ,'BT',0,'L',$fill);

        // fremdauftr_dkopf
        $pdfobjekt->Cell(
                0,
                $vyskaRadku,
                '['.getValueForNode($childNodes,"fremdauftr_dkopf").']'
                ,'BTR',1,'L',$fill);

}


////////////////////////////////////////////////////////////////////////////////////////////////////
//zapati_import($pdf,$sum_zapati_teil_array);
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
//"abgnr"
//"abgnr_name"
//"preis"
//"vzkd"
//"ln"

    global $cells;
    global $sum_zapati_sestava;
    global $zp;
    global $nurKopfZielpreis;

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

        $b2011_preis = $sumArray['bed_2011_preis'];
        $obsah = number_format($b2011_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $b2012_preis = $sumArray['bed_2012_preis'];
        $obsah = number_format($b2012_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $obsah = "Kg Brutto / Netto";
        $pdfobjekt->Cell(40, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);

        $obsah = number_format(getValueForNode($teilchilds, 'brgew'), 2, ',', ' ') . " / " . number_format(getValueForNode($teilchilds, 'gew'), 2, ',', ' ');

        $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);


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

        $b2011_ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) * intval(getValueForNode($teilchilds, 'jb_lfd_1'));
        $sum_zapati_sestava['zielpreis_2011'] += $b2011_ziel_preis;
        $obsah = number_format($b2011_ziel_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);


        $b2012_ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) * intval(getValueForNode($teilchilds, 'jb_lfd_j'));
        $sum_zapati_sestava['zielpreis_2012'] += $b2012_ziel_preis;
        $obsah = number_format($b2012_ziel_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $obsah = "Jahresbedarf Stk 2012 / 2013";
        $pdfobjekt->Cell(40, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);

        $obsah = number_format(getValueForNode($teilchilds, 'jb_lfd_1'), 0, ',', ' ') . " / " . number_format(getValueForNode($teilchilds, 'jb_lfd_j'), 0, ',', ' ');

        $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        // kosten_stk_auss
        $obsah = "Kosten Auss-Stk [EUR]";
        $pdfobjekt->Cell(30, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);
        $obsah = number_format(getValueForNode($teilchilds, 'kosten_stk_auss'), 2, ',', ' ');
        $pdfobjekt->Cell(0, $vyskaRadku, $obsah, 'LBTR', 1, 'R', $fill);
        
        $kosten_stk_auss = floatval(getValueForNode($teilchilds, 'kosten_stk_auss')) * intval(getValueForNode($teilchilds, 'jahr_bedarf_stk_2012'));
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

            $b2011_delta_preis = $b2011_ziel_preis - $b2011_preis;
            if (round($b2011_delta_preis) < 0)
                $pdfobjekt->SetTextColor(255, 0, 0);
            else
                $pdfobjekt->SetTextColor(0, 0, 0);
            $obsah = number_format($b2011_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);


            $b2012_delta_preis = $b2012_ziel_preis - $b2012_preis;

            if (round($b2012_delta_preis) < 0)
                $pdfobjekt->SetTextColor(255, 0, 0);
            else
                $pdfobjekt->SetTextColor(0, 0, 0);

            $obsah = number_format($b2012_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $pdfobjekt->SetTextColor(0, 0, 0);

            $tonnen2011 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_1')) / 1000;
            $sum_zapati_sestava['tonnen_2011'] += $tonnen2011;
            $tonnen2012 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_j')) / 1000;
            $sum_zapati_sestava['tonnen_2012'] += $tonnen2012;

            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $obsah = "Jahresbedarf Ton 2012 / 2013";
            $pdfobjekt->Cell(40, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);

            $obsah = number_format($tonnen2011, 0, ',', ' ') . " / " . number_format($tonnen2012, 0, ',', ' ');

            $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            // kosten_stk_auss zu zielpreis
        $obsah = "KostenAussStk Factor";
        $pdfobjekt->Cell(30, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);
        $cislo = $ziel_preis!=0?floatval(getValueForNode($teilchilds, 'kosten_stk_auss'))/$ziel_preis:0;
        $obsah = number_format($cislo, 2, ',', ' ');
        $pdfobjekt->Cell(0, $vyskaRadku, $obsah, 'LBTR', 1, 'R', $fill);
	
            $pdfobjekt->Cell(
		    $cells['abgnr']['sirka']
		    +$cells['abgnr_name']['sirka']
		    , $vyskaRadku, "Bedarf [Stk] Jahr 2011/2012/2013/gut 2012", 'LRBT', 0, 'L', 0);
            
           $cislo = floatval(getValueForNode($teilchilds, 'jb_lfd_2'));
	   $obsah = number_format($cislo, 0,',', ' ');
	   $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

           $cislo = floatval(getValueForNode($teilchilds, 'jb_lfd_1'));
	   $obsah = number_format($cislo, 0,',', ' ');
	   $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
	    
           $cislo = floatval(getValueForNode($teilchilds, 'jb_lfd_j'));
	   $obsah = number_format($cislo, 0,',', ' ');
	   $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

           $cislo = floatval(getValueForNode($teilchilds, 'gut_lfd_1'));
	   $obsah = number_format($cislo, 0,',', ' ');
	   $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
	   
	   $pdfobjekt->Cell(0, $vyskaRadku, "", '', 1, 'R', 0);
        }
    }
    else{
        // v pripade, ze nezobrazuju Zielpreis a diff, pokusu kusy s bedarfem doleva
        //zielpreis
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->SetFillColor(240, 255, 240, 1);
        $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
        $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Jahresbedarf Stk", 'LBT', 0, 'L', $fill);

        $ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut'));
        $obsah = '';//number_format($ziel_preis, 4, ',', ' ');
        $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

        $ziel_vzkd = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) / floatval(getValueForNode($kundechilds, 'preismin'));
        $obsah = '';//number_format($ziel_vzkd, 4, ',', ' ');
        $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BTR', 0, 'R', $fill);

        
        $b2011_ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) * intval(getValueForNode($teilchilds, 'jb_lfd_1'));
        $sum_zapati_sestava['zielpreis_2011'] += $b2011_ziel_preis;
        $obsah = number_format($b2011_ziel_preis, 0, ',', ' ');
        $obsah = number_format(getValueForNode($teilchilds, 'jb_lfd_1'), 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $b2012_ziel_preis = floatval(getValueForNode($teilchilds, 'preis_stk_gut')) * intval(getValueForNode($teilchilds, 'jb_lfd_j'));
        $sum_zapati_sestava['zielpreis_2012'] += $b2012_ziel_preis;
        $obsah = number_format($b2012_ziel_preis, 0, ',', ' ');
        $obsah = number_format(getValueForNode($teilchilds, 'jb_lfd_j'), 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $obsah = '';//"Jahresbedarf Stk 2011 / 2012";
        $pdfobjekt->Cell(40, $vyskaRadku, $obsah, '0', 0, 'L', 0);

        $obsah = '';

        $pdfobjekt->Cell(20, $vyskaRadku, $obsah, '0', 0, 'R', 0);

        // kosten_stk_auss
        $obsah = '';//"Kosten Auss-Stk [EUR]";
        $pdfobjekt->Cell(30, $vyskaRadku, $obsah, '0', 0, 'L', 0);
        $obsah = '';//number_format(getValueForNode($teilchilds, 'kosten_stk_auss'), 2, ',', ' ');
        $pdfobjekt->Cell(0, $vyskaRadku, $obsah, '0', 1, 'R', 0);
        
        $kosten_stk_auss = floatval(getValueForNode($teilchilds, 'kosten_stk_auss')) * intval(getValueForNode($teilchilds, 'jahr_bedarf_stk_2012'));
        $sum_zapati_sestava['kosten_stk_auss'] += $kosten_stk_auss;
        // novy radek
//        $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);

        if (!$nurKopfZielpreis) {
            //deltas
            $pdfobjekt->SetFont("FreeSans", "B", 8);
            $pdfobjekt->SetFillColor(255, 255, 255, 1);
            $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
            $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Jahresbedarf to", 'LBT', 0, 'L', $fill);

            $delta_preis = $ziel_preis - $preis;
//            if (round($delta_preis) < 0)
//                $pdfobjekt->SetTextColor(255, 0, 0);
//            else
//                $pdfobjekt->SetTextColor(0, 0, 0);

            $obsah = '';//number_format($delta_preis, 4, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);

            $delta_vzkd = $ziel_vzkd - $vzkd;
//            if (round($delta_vzkd) < 0)
//                $pdfobjekt->SetTextColor(255, 0, 0);
//            else
//                $pdfobjekt->SetTextColor(0, 0, 0);

            $obsah = '';//number_format($delta_vzkd, 4, ',', ' ');
            $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BTR', 0, 'R', $fill);

            $b2011_delta_preis = $b2011_ziel_preis - $b2011_preis;
//            if (round($b2011_delta_preis) < 0)
//                $pdfobjekt->SetTextColor(255, 0, 0);
//            else
//                $pdfobjekt->SetTextColor(0, 0, 0);
            
            $tonnen2011 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_1')) / 1000;
            $obsah = number_format($tonnen2011, 0, ',', ' ');
//            $obsah = number_format($b2011_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

//    $b2011_delta_vzkd = $b2011_ziel_vzkd-$b2011_vzkd;
//    $obsah = number_format($b2011_delta_vzkd, 0, ',', ' ');
//    $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $b2012_delta_preis = $b2012_ziel_preis - $b2012_preis;

//            if (round($b2012_delta_preis) < 0)
//                $pdfobjekt->SetTextColor(255, 0, 0);
//            else
//                $pdfobjekt->SetTextColor(0, 0, 0);

            $obsah = number_format($b2012_delta_preis, 0, ',', ' ');
            $tonnen2012 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_j')) / 1000;
            $obsah = number_format($tonnen2012, 0, ',', ' ');

            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $pdfobjekt->SetTextColor(0, 0, 0);

            $tonnen2011 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_1')) / 1000;
            $sum_zapati_sestava['tonnen_2011'] += $tonnen2011;
            $tonnen2012 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_j')) / 1000;
            $sum_zapati_sestava['tonnen_2012'] += $tonnen2012;

            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $obsah = '';//"Jahresbedarf Ton 2011 / 2012";
            $pdfobjekt->Cell(40, $vyskaRadku, $obsah, '0', 0, 'L', 0);

            $obsah = '';//number_format($tonnen2011, 0, ',', ' ') . " / " . number_format($tonnen2012, 0, ',', ' ');

            $pdfobjekt->Cell(20, $vyskaRadku, $obsah, '0', 0, 'R', 0);

            // kosten_stk_auss zu zielpreis
        $obsah = '';//"KostenAussStk Factor";
        $pdfobjekt->Cell(30, $vyskaRadku, $obsah, '0', 0, 'L', 0);
        $cislo = $ziel_preis!=0?floatval(getValueForNode($teilchilds, 'kosten_stk_auss'))/$ziel_preis:0;
        $obsah = '';//number_format($cislo, 2, ',', ' ');
        $pdfobjekt->Cell(0, $vyskaRadku, $obsah, '0', 1, 'R', 0);

//            $pdfobjekt->Cell(0, $vyskaRadku, "", '', 1, 'R', 0);
        }
        
    }

    $pdfobjekt->Ln();
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

        $b2011_preis = $sumAbgnrArray[$abgnr]['bed_2011_preis'];
        $obsah = number_format($b2011_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
        $b2012_preis = $sumAbgnrArray[$abgnr]['bed_2012_preis'];
        $obsah = number_format($b2012_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
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

        $b2011_preis = $sumArray['bed_2011_preis'];
        $obsah = number_format($b2011_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $b2012_preis = $sumArray['bed_2012_preis'];
        $obsah = number_format($b2012_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

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

        $b2011_ziel_preis = $sumArray['zielpreis_2011'];
        $obsah = number_format($b2011_ziel_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2011_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

        $b2012_ziel_preis = $sumArray['zielpreis_2012'];
        $obsah = number_format($b2012_ziel_preis, 0, ',', ' ');
        $pdfobjekt->Cell($cells['bed_2012_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

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
            $b2011_ziel_preis = $sumArray['zielpreis_2011'];
            $b2012_ziel_preis = $sumArray['zielpreis_2012'];
            $b2011_delta_preis = $b2011_ziel_preis - $b2011_preis;
            $obsah = number_format($b2011_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $b2012_delta_preis = $b2012_ziel_preis - $b2012_preis;
            $obsah = number_format($b2012_delta_preis, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $tonnen2011 = $sumArray['tonnen_2011'];
            $tonnen2012 = $sumArray['tonnen_2012'];

            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $obsah = "Jahresbedarf to 2012 / 2013";
            $pdfobjekt->Cell(40, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);

            $obsah = number_format($tonnen2011, 0, ',', ' ') . " / " . number_format($tonnen2012, 0, ',', ' ');

            $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
            // kosten_stk_auss zu zielpreis
            $obsah = "KostenAussStk Factor";
            $pdfobjekt->Cell(30, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);
            $cislo = $b2012_ziel_preis!=0?$sumArray['kosten_stk_auss']/$b2012_ziel_preis:0;
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
            $b2011_ziel_preis = $sumArray['zielpreis_2011'];
            $b2012_ziel_preis = $sumArray['zielpreis_2012'];
            $b2011_delta_preis = $b2011_ziel_preis - $b2011_preis;
            
            $tonnen2011 = $sumArray['tonnen_2011'];
            $tonnen2012 = $sumArray['tonnen_2012'];
            
            $obsah = number_format($b2011_delta_preis, 0, ',', ' ');
            $obsah = number_format($tonnen2011, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $b2012_delta_preis = $b2012_ziel_preis - $b2012_preis;
            $obsah = number_format($b2012_delta_preis, 0, ',', ' ');
            $obsah = number_format($tonnen2012, 0, ',', ' ');
            $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

            $tonnen2011 = $sumArray['tonnen_2011'];
            $tonnen2012 = $sumArray['tonnen_2012'];

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

		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
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
        test_pageoverflow_noheader($pdf, 5*($tatCount+1+3));
        
        zahlavi_teil($pdf, 5, $teilChildNodes);
        foreach ($taetigkeiten as $tat) {
            $tatChildNodes = $tat->childNodes;
            $abgnr = getValueForNode($tatChildNodes, 'abgnr');
            $vzkd = getValueForNode($tatChildNodes, 'vzkd');
            if(!$nurKopfZielpreis) detaily($pdf, $cells, 5, array(255, 255, 255), $tatChildNodes,$abgnr,$vzkd);
            $abgnrArray[$abgnr]++;
            $abgnrTextArray[$abgnr] = getValueForNode($tatChildNodes, 'abgnr_name');;
            $sum_zapati_sestava_abgnr[$abgnr]['bed_2011_preis'] += floatval(getValueForNode($tatChildNodes, 'bed_2011_preis'));
            $sum_zapati_sestava_abgnr[$abgnr]['bed_2012_preis'] += floatval(getValueForNode($tatChildNodes, 'bed_2012_preis'));
            foreach ($sum_zapati_teil as $key => $value) {
                $hodnota = getValueForNode($tatChildNodes, $key);
                $sum_zapati_teil[$key]+=$hodnota;
            }
        }
        zapati_teil($pdf, 5, array(255, 255, 240), $sum_zapati_teil, $teilChildNodes, $kundeChilds);
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


//echo "<pre>";
//var_dump($abgnrArray);
//echo "</pre>";
//echo "<pre>";
//var_dump($sum_zapati_sestava_abgnr);
//echo "</pre>";
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
