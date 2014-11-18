<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "S357";
$doc_subject = "S357 Report";
$doc_keywords = "S357";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$behnrvon = $_GET['behnrvon'];
$behnrbis = $_GET['behnrbis'];
$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];
$zeitpunkt = $_GET['zeitpunkt'];

$apl = AplDB::getInstance();

$zeitpunktDB = $apl->make_DB_datum($zeitpunkt);

require_once('S357_xml.php');

$sumZapatiBehNr = array(
    'inventur'=>0,
    'plus'=>0,
    'minus'=>0,
    'stand'=>0,
);

$sumBehNr = array();
$sumBehNrZustand = array();
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
		 if(strtolower($label)!="password")
                    $params .= $label.": ".$value."  ";
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
'id'=> array ("popis"=>"","sirka"=>35,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'invdatum'=> array ("popis"=>"","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'invstk'=> array ("popis"=>"","sirka"=>35,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'plus'=> array ("popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'minus'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'bestand'=> array ("popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'user'=> array ("popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
'behaelternr'=> array ("popis"=>"BehaelterNr","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'datum'=> array ("popis"=>"Datum","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'import'=> array ("popis"=>"Import","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'export'=> array ("popis"=>"Export","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'stk'=> array ("popis"=>"Stk","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'zustand_id'=> array ("popis"=>"Zustand","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'user'=> array ("popis"=>"Benutzer","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1),
);

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
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
//	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 8);
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
		
		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
	}
}


function zapati_behnr($pdfobjekt,$vyskaradku,$rgb,$pole){
global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 9);


        $obsah = 'Summe:';
        $pdfobjekt->Cell($cells['id']['sirka'], $vyskaradku, $obsah, 'LBT', 0, 'L', 1);

        $obsah = '';
        $pdfobjekt->Cell($cells['invdatum']['sirka'], $vyskaradku, $obsah, 'RBT', 0, 'L', 1);


        $obsah = number_format($pole['inventur'],0,',',' ');
        $pdfobjekt->Cell($cells['invstk']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);


        $obsah = number_format($pole['plus'],0,',',' ');
        $pdfobjekt->Cell($cells['plus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

        $obsah = number_format($pole['minus'],0,',',' ');
        $pdfobjekt->Cell($cells['minus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

        $obsah = number_format($pole['stand'],0,',',' ');
        $pdfobjekt->Cell($cells['bestand']['sirka'], $vyskaradku, $obsah, '1', 1, 'R', 1);
}

function zustand_radek($pdfobjekt, $vyskaradku, $rgb, $zustandRow, $inventurDatum, $inventurStk, $behnr) {
    global $cells;
    global $sumBehNrZustand;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "", 8);

    $zustandId = $zustandRow['zustand_id'];
    $inhaltId = $zustandRow['inhalt_id'];
    $plus = $zustandRow['bewplus'];
    $minus = $zustandRow['bewminus'];

    $sumBehNrZustand[$behnr][$zustandId][$inhaltId]['stand'] += $inventurStk + ($plus - $minus);
    $sumBehNrZustand[$behnr][$zustandId][$inhaltId]['inventur'] += $inventurStk;
    $sumBehNrZustand[$behnr][$zustandId][$inhaltId]['plus'] += $plus;
    $sumBehNrZustand[$behnr][$zustandId][$inhaltId]['minus'] += $minus;

    $obsah = $zustandId . ' / ' . $inhaltId;
    $pdfobjekt->Cell($cells['id']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);

    $obsah = $inventurDatum;
    $pdfobjekt->Cell($cells['invdatum']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);


    $obsah = $inventurStk;
    $pdfobjekt->Cell($cells['invstk']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);


    $obsah = $plus;
    $pdfobjekt->Cell($cells['plus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

    $obsah = $minus;
    $pdfobjekt->Cell($cells['minus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $obsah = number_format($inventurStk + $plus - $minus, 0, ',', ' ');
    $pdfobjekt->Cell($cells['bestand']['sirka'], $vyskaradku, $obsah, '1', 1, 'R', 1);
}

function page_header($pdfobjekt,$vyskaradku,$rgb,$sestava=0){

    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "", 8);

        if($sestava!=0){
            $obsah = "BehaelterNr";
            $pdfobjekt->Cell($cells['id']['sirka']+$cells['invdatum']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);

        }
        else{
            $obsah = "BehZustand / BehInhalt";
            $pdfobjekt->Cell($cells['id']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);

            $obsah = "Inventurdatum";
            $pdfobjekt->Cell($cells['invdatum']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);
        }

        $obsah = "KDKONTO-Inventur Stk";
        $pdfobjekt->Cell($cells['invstk']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);


        $obsah = "Bew Plus";
        $pdfobjekt->Cell($cells['plus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

        $obsah = "Bew Minus";
        $pdfobjekt->Cell($cells['minus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $obsah = "KDKONTO Aktuell";
        $pdfobjekt->Cell($cells['bestand']['sirka'], $vyskaradku, $obsah, '1', 1, 'R', 1);

}

function zahlavi_behnr($pdfobjekt,$vyskaradku,$rgb,$behnr,$invdatum){
    $a = AplDB::getInstance();
    $beharray = $a->getBehaelterArray($behnr);
    $name = $beharray[0]['name1'];
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $obsah = $behnr.' - '.$name;
        $pdfobjekt->Cell(0, $vyskaradku, $obsah, '1', 1, 'L', 1);
}
/**
 *
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $childs
 */
function zahlavi_kunde($pdfobjekt,$vyskaradku,$rgb,$childs){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $kundenr = getValueForNode($childs, 'kundenr');
        $name = getValueForNode($childs, 'name1');
        $obsah = $kundenr.' '.$name;
        $pdfobjekt->Cell(0, $vyskaradku, $obsah, '1', 1, 'L', 1);
}

function zapati_sestava_behnrTable($pdfobjekt, $vyskaradku, $rgb, $sumBehNrArray, $sumBehNrZustandArray) {
    global $cells;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $a = AplDB::getInstance();

    $sumZustandId = array();
    $pdfobjekt->SetFont("FreeSans", "B", 10);
//        $pdfobjekt->Ln();

    foreach ($sumBehNrZustandArray as $behnr => $bstand) {

        $beharray = $a->getBehaelterArray($behnr);
        $name = $beharray[0]['name1'];
        $obsah = $behnr . ' - ' . $name;
        $pdfobjekt->Cell(0, $vyskaradku, $obsah, '1', 1, 'L', 1);

        foreach ($bstand as $zustand_id => $inhalt_array) {
            nuluj_sumy_pole($sumZustandId);
            foreach ($inhalt_array as $inhalt_id => $stand) {
                $pdfobjekt->SetFont("FreeSans", "", 10);
                $obsah = $zustand_id . ' / ' . $inhalt_id;
                $pdfobjekt->Cell($cells['id']['sirka'] + $cells['invdatum']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);

                $obsah = number_format($stand['inventur'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['invstk']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

                $obsah = number_format($stand['plus'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['plus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

                $obsah = number_format($stand['minus'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['minus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);
                $pdfobjekt->SetFont("FreeSans", "B", 10);
                $obsah = number_format($stand['stand'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['bestand']['sirka'], $vyskaradku, $obsah, '1', 1, 'R', 1);
                $sumZustandId['inventur'] += $stand['inventur'];
                $sumZustandId['plus'] += $stand['plus'];
                $sumZustandId['minus'] += $stand['minus'];
                $sumZustandId['stand'] += $stand['stand'];
            }
            //suma pro zustand_id
                $obsah = "Summe $zustand_id";
                $pdfobjekt->Cell($cells['id']['sirka'] + $cells['invdatum']['sirka'], $vyskaradku, $obsah, '1', 0, 'L', 1);

                $obsah = number_format($sumZustandId['inventur'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['invstk']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

                $obsah = number_format($sumZustandId['plus'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['plus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);

                $obsah = number_format($sumZustandId['minus'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['minus']['sirka'], $vyskaradku, $obsah, '1', 0, 'R', 1);
                $pdfobjekt->SetFont("FreeSans", "B", 10);
                $obsah = number_format($sumZustandId['stand'], 0, ',', ' ');
                $pdfobjekt->Cell($cells['bestand']['sirka'], $vyskaradku, $obsah, '1', 1, 'R', 1);
        }
    }
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S357 Behaelter Kdkonto Stand", $params);
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




//pageheader($pdf,$cells_header,5);

$kunden = $domxml->getElementsByTagName("kunde");

foreach ($kunden as $kunde) {
    $kundeChilds = $kunde->childNodes;
    // zjistim datum posledni inventury pro daneho zakaznika a behaelter
    $kundenr = intval(getValueForNode($kundeChilds, 'kundenr'));
    $behaelherInventurArray = $apl->getLastKDKontoInvDatumDBArray($kundenr, $zeitpunktDB);
    if ($behaelherInventurArray !== NULL) {
        $pdf->AddPage();
        page_header($pdf, 5, array(255, 255, 240));
        zahlavi_kunde($pdf, 5, array(240, 255, 240), $kundeChilds);

        foreach ($behaelherInventurArray as $behnr => $lastInvDatum) {

            zahlavi_behnr($pdf, 5, array(255, 255, 255), $behnr, $lastInvDatum);
            nuluj_sumy_pole($sumZapatiBehNr);
            $behaelterZustandBewegungenArray = $apl->getBehBewArrayFuerKundeDatumBereich($behnr, $kundenr, $lastInvDatum, $zeitpunktDB);

            if ($behaelterZustandBewegungenArray !== NULL) {

                foreach ($behaelterZustandBewegungenArray as $behZustand) {
                    $inventurRow = $apl->getBehInventurRow($behnr, $kundenr, $behZustand['zustand_id'], $behZustand['inhalt_id'], $lastInvDatum, 'KDKONTO');
                    $inventurDatum = '';
                    $inventurStk = 0;
                    if ($inventurRow !== NULL) {
                        $inventurDatum = $inventurRow['datum'];
                        $inventurStk = $inventurRow['stk'];
                    }
                    zustand_radek($pdf, 5, array(255, 255, 255), $behZustand, $inventurDatum, $inventurStk,$behnr);
                    $sumZapatiBehNr['inventur'] += $inventurStk;
                    $sumZapatiBehNr['plus'] += $behZustand['bewplus'];
                    $sumZapatiBehNr['minus'] += $behZustand['bewminus'];
                    $sumZapatiBehNr['stand'] += ( $inventurStk + $behZustand['bewplus'] - $behZustand['bewminus']);
                }

                // a jeste variantu mam inventuru pro urcitou kombinaci zustand_id a inhalt_id, pro kterou nemam zadny pohyb
                $behaelterZustandInventurArray = $apl->getBehInventurArrayFuerKundeDatum($behnr, $kundenr, $lastInvDatum, 'KDKONTO');
                if ($behaelterZustandInventurArray !== NULL) {
                    foreach ($behaelterZustandInventurArray as $behaelterZustandInventur) {
                        $zustand_id = $behaelterZustandInventur['zustand_id'];
                        $inhalt_id = $behaelterZustandInventur['inhalt_id'];
                        //zkontroluju zda tato kombinace existuje v behaelterZustandBewegungenArray
                        $found = FALSE;
                        foreach ($behaelterZustandBewegungenArray as $behZustand) {
                            if (($behZustand['zustand_id'] == $zustand_id) && ($behZustand['inhalt_id'] == $inhalt_id))
                                $found = TRUE;
                            if ($found == TRUE)
                                break;
                        }
                        if ($found == FALSE) {
                            // tuto kombinaci jsem nanasel, tak ji zobrazim
                            $inventurStk = $behaelterZustandInventur['suminvstk'];
                            $inventurDatum = $lastInvDatum;
                            $behZustand['bewplus'] = 0;
                            $behZustand['bewminus'] = 0;
                            $behZustand['zustand_id'] = $zustand_id;
                            $behZustand['inhalt_id'] = $inhalt_id;
                            zustand_radek($pdf, 5, array(255, 255, 255), $behZustand, $inventurDatum, $inventurStk,$behnr);
                            $sumZapatiBehNr['inventur'] += $inventurStk;
                            $sumZapatiBehNr['plus'] += $behZustand['bewplus'];
                            $sumZapatiBehNr['minus'] += $behZustand['bewminus'];
                            $sumZapatiBehNr['stand'] += ( $inventurStk + $behZustand['bewplus'] - $behZustand['bewminus']);
                        }
                    }
                }
            } else {
                // pro dany behnr mam inventuru, ale nemam zadne pohyby
                $behaelterZustandInventurArray = $apl->getBehInventurArrayFuerKundeDatum($behnr, $kundenr, $lastInvDatum, 'KDKONTO');
                if ($behaelterZustandInventurArray !== NULL) {

                    foreach ($behaelterZustandInventurArray as $behZustand) {
                        $inventurDatum = $lastInvDatum;
                        $inventurStk = $behZustand['suminvstk'];
                        $behZustand['bewplus'] = 0;
                        $behZustand['bewminus'] = 0;
                        zustand_radek($pdf, 5, array(255, 255, 255), $behZustand, $inventurDatum, $inventurStk,$behnr);
                        $sumZapatiBehNr['inventur'] += $inventurStk;
                        $sumZapatiBehNr['plus'] += $behZustand['bewplus'];
                        $sumZapatiBehNr['minus'] += $behZustand['bewminus'];
                        $sumZapatiBehNr['stand'] += ( $inventurStk + $behZustand['bewplus'] - $behZustand['bewminus']);
                    }
                }
            }
                    $sumBehNr[$behnr]['stand'] += $sumZapatiBehNr['stand'];
                    $sumBehNr[$behnr]['inventur'] += $sumZapatiBehNr['inventur'];
                    $sumBehNr[$behnr]['plus'] += $sumZapatiBehNr['plus'];
                    $sumBehNr[$behnr]['minus'] += $sumZapatiBehNr['minus'];

            zapati_behnr($pdf, 5, array(255, 255, 240), $sumZapatiBehNr);
        }
    }
}

$pdf->AddPage();
page_header($pdf, 5, array(255,255,240),1);
zapati_sestava_behnrTable($pdf, 5, array(255,255,255), $sumBehNr,$sumBehNrZustand);

//echo "<pre>";
//var_dump($sumBehNrZustand);
//echo "</pre>";
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
