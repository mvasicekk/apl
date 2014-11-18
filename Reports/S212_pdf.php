<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S212";
$doc_subject = "S212 Report";
$doc_keywords = "S212";

// necham si vygenerovat XML

$parameters=$_GET;

$termin  = 'P'.trim($_GET['termin']);
$reporttyp  = $_GET['reporttyp'];
//print_r($reporttyp);
if(strstr($reporttyp, 'Expediteur'))
    $expediteur = TRUE;
else
    $expediteur = FALSE;

require_once('S212_xml.php');

//exit;
// zapis do XML souboru bude rychlejsi pri cacheovani do binaru.
// pouzit BIG-endian
// PHPExcel objekt + IReader pro Excel5 = verze pro Office 2000
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


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2




$cells = 
array(

"platte"
=> array ("bold"=>'',"popis"=>"","sirka"=>13,"ram"=>'LB',"align"=>"L","radek"=>0,"fill"=>0),

"teilnr"
=> array ("bold"=>'',"popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"pal"
=> array ("bold"=>'',"popis"=>"","sirka"=>12,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"behaelter_gew_bestellung"
=> array ("bold"=>'',"nf"=>array(2,',',' '),"popis"=>"","sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"stkimport"
=> array ("bold"=>'',"popis"=>"","sirka"=>13,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

//"stkexport"
//=> array ("bold"=>'',"popis"=>"","sirka"=>13,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"stk_laut_waage"
=> array ("bold"=>'',"nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"kg_stk_bestellung"
=> array ("bold"=>'',"nf"=>array(4,',',' '),"popis"=>"","sirka"=>13,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"abywaage_kg_stk10"
=> array ("bold"=>'',"nf"=>array(4,',',' '),"popis"=>"","sirka"=>13,"ram"=>'BTLR',"align"=>"R","radek"=>0,"fill"=>0),

"abywaage_behaelter_ist"
=> array ("bold"=>'',"nf"=>array(2,',',' '),"popis"=>"","sirka"=>13,"ram"=>'BTLR',"align"=>"R","radek"=>0,"fill"=>0),

"behaelter_netto_ist"
=> array ("bold"=>'',"nf"=>array(2,',',' '),"popis"=>"","sirka"=>13,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"soll_gew_brutto"
=> array ("bold"=>'',"nf"=>array(2,',',' '),"popis"=>"","sirka"=>13,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"abywaage_brutto"
=> array ("bold"=>'',"nf"=>array(2,',',' '),"popis"=>"","sirka"=>13,"ram"=>'BTLR',"align"=>"R","radek"=>0,"fill"=>0),

"behaeltertyp"
=> array ("bold"=>'',"popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"L","radek"=>1,"fill"=>0),
);

$cells_header = 
array(

"platte"
=> array ("bold"=>'',"popis"=>"\n\nPlatteNr.\n\n","sirka"=>$cells['platte']['sirka'],"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>1),

"teilnr"
=> array ("bold"=>'',"popis"=>"\n\nArt.\nCode\n","sirka"=>$cells['teilnr']['sirka'],"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>1),

"pal"
=> array ("bold"=>'',"popis"=>"\n\nImp.\nBeh.\n","sirka"=>$cells['pal']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"behaelter_gew_bestellung"
=> array ("bold"=>'',"popis"=>"Beh.\nGew\nKunde\nBestell.\nNetto","sirka"=>$cells['behaelter_gew_bestellung']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"stkimport"
=> array ("bold"=>'',"popis"=>"\n\nStk\nImport\n","sirka"=>$cells['stkimport']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

//"stkexport"
//=> array ("bold"=>'',"popis"=>"\n\nStk\nExport\n","sirka"=>$cells['stkexport']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"stk_laut_waage"
=> array ("bold"=>'',"popis"=>"\n\nStk\nlaut\nWaage","sirka"=>$cells['stk_laut_waage']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"kg_stk_bestellung"
=> array ("bold"=>'',"popis"=>"\n\nKg\nStk\nBestell.","sirka"=>$cells['kg_stk_bestellung']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"abywaage_kg_stk10"
=> array ("bold"=>'',"popis"=>"\nkg\nStk\n(10Stk)\nAby","sirka"=>$cells['abywaage_kg_stk10']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"abywaage_behaelter_ist"
=> array ("bold"=>'',"popis"=>"\n\nGew\nBeh IST\nAbydos","sirka"=>$cells['abywaage_behaelter_ist']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"behaelter_netto_ist"
=> array ("bold"=>'',"popis"=>"\nIst\nGew.\nkg\nNetto","sirka"=>$cells['behaelter_netto_ist']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"soll_gew_brutto"
=> array ("bold"=>'',"popis"=>"\n\nSollgew.\nkg\nBrutto","sirka"=>$cells['soll_gew_brutto']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"abywaage_brutto"
=> array ("bold"=>'',"popis"=>"\n\nAbydos\nWaage\n","sirka"=>$cells['abywaage_brutto']['sirka'],"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),

"behaeltertyp"
=> array ("bold"=>'',"popis"=>"\n\nBehaelter\n\n","sirka"=>$cells['behaeltertyp']['sirka'],"ram"=>'LBTR',"align"=>"L","radek"=>1,"fill"=>1),
);

$summeZapatiTeil = array(
    'behaelter_gew_bestellung'=>0,
    'stkimport'=>0,
    //'stkexport'=>0,
    'stk_laut_waage'=>0,
    'abywaage_behaelter_ist'=>0,
    'behaelter_netto_ist'=>0,
    //'soll_gew_brutto'=>0,
    'abywaage_brutto'=>0,
);

$summeZapatiTermin = array(
    'behaelter_gew_bestellung'=>0,
    'stkimport'=>0,
    //'stkexport'=>0,
    'stk_laut_waage'=>0,
    'abywaage_behaelter_ist'=>0,
    'behaelter_netto_ist'=>0,
    'soll_gew_brutto'=>0,
    'abywaage_brutto'=>0,
);

$behGutSumme = array();
$behAussSumme = array();

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
function pageheader($pdfobjekt, $pole, $headervyskaradku) {

    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
//    echo "<br>pageheader";
    foreach ($pole as $cellname => $cell) {
            $pdfobjekt->MyMultiCell($cell["sirka"], $headervyskaradku, $cell['popis'], $cell["ram"], $cell["align"], $cell['fill']);
    }
    $pdfobjekt->Ln();
    $pdfobjekt->Ln();
    $pdfobjekt->Ln();
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 6);
}

function formatCell($cellName,$rawValue){
    global $cells;
    $cell = $cells[$cellName];
    if(array_key_exists('nf', $cell)){
        $cellobsah = number_format(floatval($rawValue), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
    }
    else{
        $cellobsah = $rawValue;
    }
    return $cellobsah;
}

function radek_paleta($pdf,$vyskaradku,$nodes){
    global $cells;
    $pdf->SetFont("FreeSans", "", 7);
    $fill=0;

    $cellName = 'platte';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'teilnr';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'pal';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaelter_gew_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'stkimport';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

//    $cellName = 'stkexport';
//    $cell = $cells[$cellName];
//    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
//    if(floatval($formattedValue)==0) $formattedValue='';
//    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'stk_laut_waage';
    $cell = $cells[$cellName];
    //$value = floatval(getValueForNode($nodes, 'abywaage_kg_stk10'))!=0?floor(floatval(getValueForNode($nodes, 'behaelter_netto_ist'))/floatval(getValueForNode($nodes, 'abywaage_kg_stk10'))):0;
    $value = getValueForNode($nodes, $cellName);
    $stk_laut_waage = $value;
    //$formattedValue = formatCell($cellName, $value);
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'kg_stk_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_kg_stk10';
    $cell = $cells[$cellName];
    $abywaage_kg_stk10 = getValueForNode($nodes, $cellName);
    //echo "<br>".$abywaage_kg_stk10;
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    //echo " formattedValue = $formattedValue";
    if(floatval($abywaage_kg_stk10)==0) $formattedValue='';
    //echo " floatvalue=".floatval($abywaage_kg_stk10);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_behaelter_ist';
    $cell = $cells[$cellName];
    $abywaage_behaelter_ist = getValueForNode($nodes, $cellName);
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($abywaage_behaelter_ist)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaelter_netto_ist';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'soll_gew_brutto';
    $cell = $cells[$cellName];
    $value = $abywaage_behaelter_ist+$abywaage_kg_stk10*$stk_laut_waage;
    $formattedValue = formatCell($cellName, $value);
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_brutto';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaeltertyp';
    $cell = $cells[$cellName];
    if(strlen(getValueForNode($nodes, 'behaelter_beschreibung'))==0)
            $value='';
    else
            $value = getValueForNode($nodes, 'behaelter_aby_id').' - '.getValueForNode($nodes, 'behaelter_beschreibung');
    $formattedValue = formatCell($cellName, $value);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    //$pdf->Ln();
}

function radek_leer($pdf,$vyskaradku,$nodes){
    global $cells;
    $pdf->SetFont("FreeSans", "", 7);
    $fill=0;

    $cellName = 'platte';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'teilnr';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'pal';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaelter_gew_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'stkimport';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

//    $cellName = 'stkexport';
//    $cell = $cells[$cellName];
//    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
//    if(floatval($formattedValue)==0) $formattedValue='';
//    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'stk_laut_waage';
    $cell = $cells[$cellName];
    $value = floatval(getValueForNode($nodes, 'abywaage_kg_stk10'))!=0?floor(floatval(getValueForNode($nodes, 'behaelter_netto_ist'))/floatval(getValueForNode($nodes, 'abywaage_kg_stk10'))):0;
    $stk_laut_waage = $value;
    $formattedValue = '';//formatCell($cellName, $value);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'kg_stk_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_kg_stk10';
    $cell = $cells[$cellName];
    $abywaage_kg_stk10 = getValueForNode($nodes, $cellName);
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_behaelter_ist';
    $cell = $cells[$cellName];
    $abywaage_behaelter_ist = getValueForNode($nodes, $cellName);
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaelter_netto_ist';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'soll_gew_brutto';
    $cell = $cells[$cellName];
    $value = $abywaage_behaelter_ist+$abywaage_kg_stk10*$stk_laut_waage;
    $formattedValue = '';//formatCell($cellName, $value);
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_brutto';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaeltertyp';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    //$pdf->Ln();
}

function radek_auss($pdf,$vyskaradku,$nodes,$aussStkExp){
    global $cells;
    $pdf->SetFont("FreeSans", "", 7);
    $fill=0;

    $cellName = 'platte';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'teilnr';
    $cell = $cells[$cellName];
    $formattedValue = 'A'.getValueForNode($nodes, 'platte');
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'pal';
    $cell = $cells[$cellName];
    $formattedValue = '';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaelter_gew_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = '';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'stkimport';
    $cell = $cells[$cellName];
    $formattedValue = '';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

//    $cellName = 'stkexport';
//    $cell = $cells[$cellName];
//    $formattedValue = formatCell($cellName, $aussStkExp);
//    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'stk_laut_waage';
    $cell = $cells[$cellName];
    //$value = floatval(getValueForNode($nodes, 'auss_abywaage_kg_stk10'))!=0?floor(floatval(getValueForNode($nodes, 'auss_behaelter_netto_ist'))/floatval(getValueForNode($nodes, 'auss_abywaage_kg_stk10'))):0;
    $value = floatval(getValueForNode($nodes, 'auss_stk_laut_waage'));
    $stk_laut_waage = $value;
    $formattedValue = formatCell($cellName, $value);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'kg_stk_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_kg_stk10';
    $cell = $cells[$cellName];
    $abywaage_kg_stk10 = getValueForNode($nodes, 'auss_abywaage_kg_stk10');
    $formattedValue = formatCell($cellName, getValueForNode($nodes, 'auss_abywaage_kg_stk10'));
    if(floatval($abywaage_kg_stk10)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_behaelter_ist';
    $cell = $cells[$cellName];
    $abywaage_behaelter_ist = getValueForNode($nodes, 'auss_abywaage_behaelter_ist');
    $formattedValue = formatCell($cellName, getValueForNode($nodes, 'auss_abywaage_behaelter_ist'));
    if(floatval($abywaage_behaelter_ist)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaelter_netto_ist';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, 'auss_behaelter_netto_ist'));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'soll_gew_brutto';
    $cell = $cells[$cellName];
    $value = $abywaage_behaelter_ist+$abywaage_kg_stk10*$stk_laut_waage;
    $formattedValue = formatCell($cellName, $value);
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_brutto';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, getValueForNode($nodes, 'auss_abywaage_brutto'));
    if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    $cellName = 'behaeltertyp';
    $cell = $cells[$cellName];
    if (strlen(getValueForNode($nodes, 'auss_behaelter_beschreibung')) == 0)
        $value = '';
    else
        $value = getValueForNode($nodes, 'auss_behaelter_aby_id').' - '.getValueForNode($nodes, 'auss_behaelter_beschreibung');
    $formattedValue = formatCell($cellName, $value);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, $cell["ram"], $cell["radek"], $cell["align"], $fill);

    //$pdf->Ln();
}

function zapati_teil($pdf,$vyskaradku,$rgb,$summen){
    global $cells;
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;

    $formattedValue = 'Summe Teil';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cells['platte']["sirka"]
                +$cells['teilnr']["sirka"]
                +$cells['pal']["sirka"]
                , $vyskaradku, $formattedValue, 'LTB', 0, 'L', $fill);

    $cellName = 'behaelter_gew_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'stkimport';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

//    $cellName = 'stkexport';
//    $cell = $cells[$cellName];
//    $formattedValue = formatCell($cellName, $summen[$cellName]);
//    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'stk_laut_waage';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'kg_stk_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_kg_stk10';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_behaelter_ist';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);
//
    $cellName = 'behaelter_netto_ist';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);
//
    $cellName = 'soll_gew_brutto';
    $cell = $cells[$cellName];
    //$value = floatval(getValueForNode($nodes, 'abywaage_behaelter_ist'))+floatval(getValueForNode($nodes, 'abywaage_kg_stk10'))*floatval(getValueForNode($nodes, 'stkexport'));
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_brutto';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);
//
    $cellName = 'behaeltertyp';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TBR', $cell["radek"], $cell["align"], $fill);

//      $pdf->Ln();
}

function zapati_termin($pdf,$vyskaradku,$rgb,$summen){
    global $cells;
    $pdf->SetFont("FreeSans", "B", 6.5);
    $pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;

    $formattedValue = 'Summe Ladungsgew. soll';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cells['platte']["sirka"]
                +$cells['teilnr']["sirka"]
                +$cells['pal']["sirka"]
                , $vyskaradku, $formattedValue, 'LTB', 0, 'L', $fill);

    $cellName = 'behaelter_gew_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'stkimport';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

//    $cellName = 'stkexport';
//    $cell = $cells[$cellName];
//    $formattedValue = formatCell($cellName, $summen[$cellName]);
//    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'stk_laut_waage';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'kg_stk_bestellung';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_kg_stk10';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_behaelter_ist';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);
//
    $cellName = 'behaelter_netto_ist';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);
//
    $cellName = 'soll_gew_brutto';
    $cell = $cells[$cellName];
    //$value = floatval(getValueForNode($nodes, 'abywaage_behaelter_ist'))+floatval(getValueForNode($nodes, 'abywaage_kg_stk10'))*floatval(getValueForNode($nodes, 'stkexport'));
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);

    $cellName = 'abywaage_brutto';
    $cell = $cells[$cellName];
    $formattedValue = formatCell($cellName, $summen[$cellName]);
    //if(floatval($formattedValue)==0) $formattedValue='';
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TB', $cell["radek"], $cell["align"], $fill);
//
    $cellName = 'behaeltertyp';
    $cell = $cells[$cellName];
    $formattedValue = '';//formatCell($cellName, getValueForNode($nodes, $cellName));
    $pdf->Cell($cell["sirka"], $vyskaradku, $formattedValue, 'TBR', $cell["radek"], $cell["align"], $fill);

//      $pdf->Ln();
}

// funkce pro vykresleni tela
function telo($pdfobjekt, $pole, $zahlavivyskaradku, $rgb, $funkce, $nodelist) {
    global $cells;
    global $vzabyFields;
    global $ohneVzAby;

    $pdfobjekt->SetFont("FreeSans", "", 8);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill=0;
    // pujdu polem pro zahlavi a budu prohledavat predany nodelist
    foreach ($pole as $nodename => $cell) {
        $useNumberFormat = TRUE;


        if (array_key_exists("nf", $cell)) {
            $cellobsah =
                    number_format(getValueForNode($nodelist, $nodename), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
        } else {
            $cellobsah = getValueForNode($nodelist, $nodename);
        }

        if ($nodename == 'kzgut') {
            $kzgutObsah = getValueForNode($nodelist, $nodename);
            if ($kzgutObsah == 'G') {
                $pdfobjekt->SetFont("FreeSans", "B", 8);
                $pdfobjekt->SetFillColor(200, 255, 200, 1);
                $fill=1;
            } else {
                $pdfobjekt->SetFont("FreeSans", "", 8);
                $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
                $fill=0;
            }
            $cellobsah = '';
        }

        if($fill==0 && $nodename=='gewicht') $cellobsah='';
        if($nodename=='kzgut'){
            //kzgut nebudu kreslit
        }
        else
            $pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, $cellobsah, $cell["ram"], $cell["radek"], $cell["align"], $fill);
    }
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_import($pdfobjekt, $vyskaradku, $rgb, $nodes) {
    global $cells;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "B", 9);
    $import = getValueForNode($nodes, 'auftragsnr');
    //$pdfobjekt->Cell($cells['platte']['sirka']+$cells['teilnr']['sirka'], $vyskaradku, "IM: " . $import, '1', 1, 'L', $fill);
    $pdfobjekt->Cell(0, $vyskaradku, "IM: " . $import, '1', 1, 'L', $fill);
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$cells_header,$teilnr,$gew,$muster_platz,$teillang,$abnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(40,$vyskaradku," ".$teilnr." / ".$teillang,'1',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 6);
       	$gew=number_format($gew,2,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$gew." kg/Stk ",'1',0,'R',$fill);
	$pdfobjekt->Cell(50,$vyskaradku,"[".$abnr."]",'1',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku," Muster: ".$muster_platz,'1',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_taetigkeit($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$tatnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(95,$vyskaradku,$popis." ".$tatnr,'B',0,'L',$fill);
	
	
	$obsah=$pole['stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	//auss_typ
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	//vzkd_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	//vzaby_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function zapati_sestava($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1)
{
        global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell($cells['teil']['sirka'],$vyskaradku,$popis." IST: ".$terminnr,'B',0,'L',$fill);
        $pdfobjekt->Cell($cells['gewicht']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
	$pdfobjekt->Cell($cells['abgnr']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
        $pdfobjekt->Cell($cells['stk_drueck']['sirka'],$vyskaradku,"",'B',0,'L',$fill);


	$obsah=$pole['auss2'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['auss2']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss4'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['auss4']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss6'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['auss6']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";
	$pdfobjekt->Cell($cells['vzkd_geplant']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['vzkd']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);
	// druhy radek
	$pdfobjekt->Cell($cells['teil']['sirka'],$vyskaradku,$popis." SOLL: ".$terminnr,'B',0,'L',$fill);
	$obsah=$pole['gew_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['gewicht']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	$pdfobjekt->Cell($cells['abgnr']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
        $pdfobjekt->Cell($cells['stk_drueck']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
        $obsah='';
        $pdfobjekt->Cell($cells['auss2']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
        $pdfobjekt->Cell($cells['auss4']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
        $pdfobjekt->Cell($cells['auss6']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	$obsah=$pole['vzkd_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell($cells['vzkd_geplant']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	$obsah="";
	$pdfobjekt->Cell($cells['vzkd']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);


        //tretiradek
        $pdfobjekt->Cell($cells['teil']['sirka'],$vyskaradku,"VzKd(geplant-bearbeitet)",'B',0,'L',$fill);
        $pdfobjekt->Cell($cells['gewicht']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
	$pdfobjekt->Cell($cells['abgnr']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
        $pdfobjekt->Cell($cells['stk_drueck']['sirka'],$vyskaradku,"",'B',0,'L',$fill);
        $obsah='';
        $pdfobjekt->Cell($cells['auss2']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
        $pdfobjekt->Cell($cells['auss4']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
        $pdfobjekt->Cell($cells['auss6']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	$rozdil = round($pole['vzkd_geplant'])-round($pole['vzkd']);
	$obsah=number_format($rozdil,0,',',' ');
	$pdfobjekt->Cell($cells['vzkd_geplant']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
	$obsah="";
	$pdfobjekt->Cell($cells['vzkd']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);

	//$pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);

}



function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku,$geplannt,$exdatum);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S212 Export Tableau ( ".$termin." )", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-5, PDF_MARGIN_RIGHT-10);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER+5);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();
pageheader($pdf,$cells_header,4);
$pdf->Ln();
$pdf->Ln();

$importe = $domxml->getElementsByTagName("import");

foreach ($importe as $import) {
    $importChilds = $import->childNodes;
    test_pageoverflow($pdf, 5, $cells_header,4);
    zahlavi_import($pdf,5,array(200,255,200),$importChilds);
    $teile = $import->getElementsByTagName("teil");
    foreach ($teile as $teil) {
        nuluj_sumy_pole($summeZapatiTeil);
        $aussExp = 0;
        $teilChilds = $teil->childNodes;
        $paletten = $teil->getElementsByTagName("palette");
        $aussChilds = array();
        foreach($paletten as $palette){
            $paletteChilds = $palette->childNodes;
            //$behtypIndex = getValueForNode($paletteChilds, 'behaeltertyp');
            // jen exportni palety ?
            //if(strlen($behtypIndex)>1){
                test_pageoverflow($pdf, 6, $cells_header,4);
                radek_paleta($pdf,6,$paletteChilds);
            //}
            //$stk_laut_waage = floatval(getValueForNode($paletteChilds, 'abywaage_kg_stk10'))!=0?floor(floatval(getValueForNode($paletteChilds, 'behaelter_netto_ist'))/floatval(getValueForNode($paletteChilds, 'abywaage_kg_stk10'))):0;
            $stk_laut_waage = getValueForNode($paletteChilds, 'stk_laut_waage');
            foreach ($summeZapatiTeil as $klic=>$hodnota) {
                $value = getValueForNode($paletteChilds,$klic);
                $summeZapatiTeil[$klic] += $value;
            }
            //$summeZapatiTeil['stk_laut_waage'] += $stk_laut_waage;
//            $summeZapatiTeil['stkexport'] += intval(getValueForNode($paletteChilds, 'auss_stk_exp'));
            $summeZapatiTeil['soll_gew_brutto'] += $stk_laut_waage*floatval(getValueForNode($paletteChilds, 'abywaage_kg_stk10'))+floatval(getValueForNode($paletteChilds, 'abywaage_behaelter_ist'));
            $aussExp += intval(getValueForNode($paletteChilds, 'auss_stk_exp'));
            // test , jestli paleta neobsahuje informaci o zmetcich
            $aussBehaelter = intval(getValueForNode($paletteChilds, 'aussbehaelter'));
            if($aussBehaelter!=0){
                $aussChilds = $paletteChilds;
                $summeZapatiTeil['abywaage_behaelter_ist'] += floatval(getValueForNode($paletteChilds, 'auss_abywaage_behaelter_ist'));
                $summeZapatiTeil['behaelter_netto_ist'] += floatval(getValueForNode($paletteChilds, 'auss_behaelter_netto_ist'));
                $summeZapatiTeil['abywaage_brutto'] += floatval(getValueForNode($paletteChilds, 'auss_abywaage_brutto'));
                $behtypIndex = getValueForNode($paletteChilds, 'auss_behaeltertyp');
                if(strlen($behtypIndex)>1) $behAussSumme[$behtypIndex]+=1;
            }

            $behtypIndex = getValueForNode($paletteChilds, 'behaeltertyp');
            if(strlen($behtypIndex)>1) $behGutSumme[$behtypIndex]+=1;

        }
        if(count($aussChilds)>0){
            //$aussStkLautWage = floatval(getValueForNode($aussChilds, 'auss_abywaage_kg_stk10'))!=0?floor(floatval(getValueForNode($aussChilds, 'auss_behaelter_netto_ist'))/floatval(getValueForNode($aussChilds, 'auss_abywaage_kg_stk10'))):0;
            $aussStkLautWage = getValueForNode($aussChilds, 'auss_stk_laut_waage');
            //$aussSollGewBrutto = floatval(getValueForNode($aussChilds, 'auss_abywaage_behaelter_ist'))+floatval(getValueForNode($aussChilds, 'auss_abywaage_kg_stk10'))*$aussExp;
            $aussSollGewBrutto = floatval(getValueForNode($aussChilds, 'auss_abywaage_behaelter_ist'))+floatval(getValueForNode($aussChilds, 'auss_abywaage_kg_stk10'))*$aussStkLautWage;
            $summeZapatiTeil['stk_laut_waage'] += $aussStkLautWage;
            $summeZapatiTeil['soll_gew_brutto'] += $aussSollGewBrutto;
        }
        // radek s ausschussy zobrazit, jen pokud ma nejaky behaelter
        $auss_behaeltertyp = trim(getValueForNode($aussChilds, 'auss_behaeltertyp'));
//        if($auss_behaeltertyp!=''){
            test_pageoverflow($pdf, 6, $cells_header,4);
            radek_auss($pdf,6,$aussChilds,$aussExp);
//        }

        if($expediteur==TRUE) {
            // 5 prazdnych radku
            for($i=0;$i<5;$i++){
                test_pageoverflow($pdf, 6, $cells_header,4);
                radek_leer($pdf,6,$paletteChilds);
            }
        }
        test_pageoverflow($pdf, 5, $cells_header,4);
        zapati_teil($pdf,5,array(230,230,230),$summeZapatiTeil);

        foreach ($summeZapatiTermin as $key => $value) {
            $summeZapatiTermin[$key] += $summeZapatiTeil[$key];
        }


    }
}

$pdf->Ln();
zapati_termin($pdf,5,array(230,230,230),$summeZapatiTermin);

// vytahnu si seznam moznych typu behaeltru
$apl = AplDB::getInstance();
$behaelterTypen = $apl->getBehaelterTypen();
$behaelterArray = array();
// vytvorim si vhodne asociativni pole
foreach ($behaelterTypen as $poradi => $radek) {
    $behaelterArray[$radek['typ']] = $radek;
}

//print_r($behGutSumme);
//print_r($behAussSumme);
//print_r($behaelterArray);

//zjistim zda se mi vejde vypis na stejnou stranku, pocet radku se seznamem behaeltru + 1 radek pro hlavicku
$pocetRadekSBehaeltry = max(array(count($behAussSumme),count($behGutSumme)))+1;

test_pageoverflow($pdf, $pocetRadekSBehaeltry*5+1, $cells_header, 4);
$pdf->Ln();
$pdf->Cell(80, 5, "Behaelter mit gutem Guss:", 'LTBR', 1, 'L', 1);
foreach ($behGutSumme as $typ => $pocet) {
    $popis = $behaelterArray[$typ]['aby_id'].' - '.$behaelterArray[$typ]['beschreibung'];
    $pdf->Cell(70, 5, $popis, 'LTBR', 0, 'L', 0);
    $pdf->Cell(10, 5, $pocet, 'LTBR', 1, 'R', 0);
}

test_pageoverflow($pdf, $pocetRadekSBehaeltry*5+5, $cells_header, 4);
$pdf->Ln();
$pdf->Cell(80, 5, "Behaelter mit Ausschuss:", 'LTBR', 1, 'L', 1);
foreach ($behAussSumme as $typ => $pocet) {
    $popis = $behaelterArray[$typ]['aby_id'].' - '.$behaelterArray[$typ]['beschreibung'];
    $pdf->Cell(70, 5, $popis, 'LTBR', 0, 'L', 0);
    $pdf->Cell(10, 5, $pocet, 'LTBR', 1, 'R', 0);
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
