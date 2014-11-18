<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S216";
$doc_subject = "S216 Report";
$doc_keywords = "S216";

// necham si vygenerovat XML

$parameters=$_GET;

$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];

require_once('S216_xml.php');

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


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$fillSoll = array(255,255,180);
$fillIst = array(180,255,180);
$fillRest = array(255,180,180);
$fillTag = array(180,180,255);
$fillRw = array(255,255,180);
    
$cells = 
array(

"kunde" 
=> array ("popis"=>"Kunde","sirka"=>13,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd_soll"
=> array ("popis"=>"VzkdSoll","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd_soll_pct"
=> array ("popis"=>"Vzkd","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"vzkd_bearbeitet"
=> array ("popis"=>"VzkdIst","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"vzkd_bearbeitet_pct"
=> array ("popis"=>"Vzkd","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"vzkd_rest"
=> array ("popis"=>"VzkdRest","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"vzkd_rest_pct"
=> array ("popis"=>"Vzkd","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd_soll_tag"
=> array ("popis"=>"VzkdSoll","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"reichweite"
=> array ("popis"=>"Reichweite","sirka"=>17,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gew_abgnr"
=> array ("nf"=>array(0,',',' '),"popis"=>"Gew","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gew_ges"
=> array ("nf"=>array(0,',',' '),"popis"=>"Gew","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"lf" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$sum_zapati_statnr_array = array(	
								"vzkd_gesamt"=>0,
								"vzkd_bearbeitet"=>0,
								"gew_gesamt"=>0,
								"gew_gesamtabgnr"=>0,
								);

$sum_zapati_statnr_sestava_array = array(	
								"vzkd_gesamt"=>0,
								"vzkd_bearbeitet"=>0,
								"gew_gesamt"=>0,
								"gew_gesamtabgnr"=>0,
								);

$sum_zapati_kunde_array = array(	
								"vzkd_gesamt"=>0,
								"vzkd_bearbeitet"=>0,
								"gew_gesamt"=>0,
								"gew_gesamtabgnr"=>0,
								"vzkdplan"=>0,
								"vzkd_rest"=>0,
								);

$sum_zapati_kunde_sestava_array = array(	
								"vzkd_gesamt"=>0,
								"vzkd_bearbeitet"=>0,
								"gew_gesamt"=>0,
								"gew_gesamtabgnr"=>0,
								"vzkdplan"=>0,
								"vzkd_rest"=>0,
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
/**
 *
 * @global array $cells
 * @param type $pdfobjekt
 * @param type $vyskaradku
 * @param type $rgb
 * @param type $childs
 * @param type $sestava kdyz bude TRUE pouziju jako zahlavi pro sumy cele sestavy
 */
function zahlavi_kunde($pdfobjekt, $vyskaradku, $rgb, $childs,$sestava=FALSE) {
    global $cells;
    
    global $fillSoll;
    global $fillIst;
    global $fillRest;
    global $fillTag;
    global $fillRw;
    
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "B", 8);

    $kundenr = getValueForNode($childs, 'kd');
    if($sestava===TRUE){
	$r1p = "Gesamt";
	$r2p = "Summe";
    }
    else{
	$r1p = "Kunde";
	$r2p = $kundenr;
    }

    //prvniradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$r1p, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    $cell='vzkd_rest';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $cell='vzkd_soll_tag';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$cells[$cell]['popis'], $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    //druhyradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$r2p, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[min]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[%]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[min]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[%]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    $cell='vzkd_rest';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[min]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[%]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $cell='vzkd_soll_tag';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[min]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[Tag]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[kg]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'[kg]', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_statnr($pdfobjekt, $vyskaradku, $rgb, $childs) {
    global $cells;
    
    global $fillSoll;
    global $fillIst;
    global $fillRest;
    global $fillTag;
    global $fillRw;
    
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "B", 8);

    $statnr = getValueForNode($childs, 'statnr');

    //prvniradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$statnr, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    $cell='vzkd_rest';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $cell='vzkd_soll_tag';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_kunde($pdfobjekt, $vyskaradku, $rgb, $childs,$pole,$sumvzkdgesamt,$sestava=FALSE) {
    global $cells;
    
    global $fillSoll;
    global $fillIst;
    global $fillRest;
    global $fillTag;
    global $fillRw;
    
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "B", 8);

    if($sestava===TRUE)
	$r1p = "GesamtS";
    else
	$r1p = "SumKD";
    
//    $kunde = getValueForNode($childs, 'kd');
    $vzkd_gesamt = $pole['vzkd_gesamt'];
    $vzkd_gesamt_pct = $sumvzkdgesamt!=0?$vzkd_gesamt / $sumvzkdgesamt *100:0;
    $vzkd_bearbeitet = $pole['vzkd_bearbeitet'];
    $vzkd_bearbeitet_pct = $vzkd_gesamt!=0?$vzkd_bearbeitet / $vzkd_gesamt *100:0;
//    $vzkd_rest = $vzkd_gesamt-$vzkd_bearbeitet;
    $vzkd_rest = $pole['vzkd_rest'];
    $vzkd_rest_pct = $vzkd_gesamt!=0?$vzkd_rest / $vzkd_gesamt *100:0;
    $gew_gesamt = $pole['gew_gesamt'];
    $vzkdplan = $pole['vzkdplan'];
    $reichweite = $vzkdplan!=0?$vzkd_rest / $vzkdplan:0;
    //prvniradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$r1p, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $obsah = number_format($vzkd_gesamt,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $obsah = number_format($vzkd_gesamt_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $obsah = number_format($vzkd_bearbeitet,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $obsah = number_format($vzkd_bearbeitet_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    $cell='vzkd_rest';
    $obsah = number_format($vzkd_rest,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $obsah = number_format($vzkd_rest_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $cell='vzkd_soll_tag';
    $obsah = number_format($vzkdplan,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $obsah = number_format($reichweite,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $obsah = number_format($gew_gesamt,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_statnr($pdfobjekt, $vyskaradku, $rgb, $childs,$pole,$sumvzkdgesamt=0,$sestava=FALSE,$statnr="") {
    global $cells;
    
    global $fillSoll;
    global $fillIst;
    global $fillRest;
    global $fillTag;
    global $fillRw;
    
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "B", 8);

    if($sestava===TRUE)
	$statnr = $statnr;
    else
	$statnr = getValueForNode($childs, 'statnr');
    
    
    $vzkd_gesamt = $pole['vzkd_gesamt'];
    $vzkd_gesamt_pct = $sumvzkdgesamt!=0?$vzkd_gesamt / $sumvzkdgesamt *100:0;
    $vzkd_bearbeitet = $pole['vzkd_bearbeitet'];
    $vzkd_bearbeitet_pct = $vzkd_gesamt!=0?$vzkd_bearbeitet / $vzkd_gesamt *100:0;
//    $vzkd_rest = $vzkd_gesamt-$vzkd_bearbeitet;
    $vzkd_rest = $pole['vzkd_rest'];
    $vzkd_rest_pct = $vzkd_gesamt!=0?$vzkd_rest / $vzkd_gesamt *100:0;
    if($sestava===FALSE)
	$vzkdplan = getValueForNode($childs, 'vzkdplan');
    else
	$vzkdplan = $pole['vzkdplan'];
    
    $reichweite = $vzkdplan!=0?$vzkd_rest / $vzkdplan:0;
    
    //prvniradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$statnr, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $obsah = number_format($vzkd_gesamt,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $obsah = number_format($vzkd_gesamt_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $obsah = number_format($vzkd_bearbeitet,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $obsah = number_format($vzkd_bearbeitet_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    $cell='vzkd_rest';
    $obsah = number_format($vzkd_rest,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $obsah = number_format($vzkd_rest_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $cell='vzkd_soll_tag';
    $obsah = intval($vzkdplan);
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $obsah = number_format($reichweite,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * @global array $cells
 * @global array $fillSoll
 * @global array $fillIst
 * @global array $fillRest
 * @global array $fillTag
 * @global array $fillRw
 * @param TCPDF $pdfobjekt
 * @param type $vyskaradku
 * @param type $rgb
 * @param type $childs
 * @param type $sumvzkd_gesamt 
 */
function radek_tatnr($pdfobjekt, $vyskaradku, $rgb, $childs,$sumvzkd_gesamt) {
    global $cells;
    
    global $fillSoll;
    global $fillIst;
    global $fillRest;
    global $fillTag;
    global $fillRw;
    
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "", 8);

    $tatnr = getValueForNode($childs, 'abgnr');
    $vzkd_gesamt = floatval(getValueForNode($childs, 'vzkd_gesamt'));
    $vzkd_gesamt_pct = $sumvzkd_gesamt!=0?$vzkd_gesamt / $sumvzkd_gesamt *100:0;
    $vzkd_bearbeitet = floatval(getValueForNode($childs, 'vzkd_bearbeitet'));
    $vzkd_bearbeitet_pct = $vzkd_gesamt!=0?$vzkd_bearbeitet / $vzkd_gesamt *100:0;
    $vzkd_rest = $vzkd_gesamt-$vzkd_bearbeitet;
    
    if($vzkd_rest<0){
	$bCervenaNula = TRUE;
	$vzkd_rest=0;
    }	
    else{
	$bCervenaNula = FALSE;
    }
    
    $vzkd_rest_pct = $vzkd_gesamt!=0?$vzkd_rest / $vzkd_gesamt *100:0;
    //prvniradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$tatnr, $cells[$cell]['ram'], 0, 'R', $fill);
//    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $obsah = number_format($vzkd_gesamt,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $obsah = number_format($vzkd_gesamt_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $obsah = number_format($vzkd_bearbeitet,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $obsah = number_format($vzkd_bearbeitet_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    if($bCervenaNula)
	$pdfobjekt->SetTextColor(255,0,0);
    $cell='vzkd_rest';
    $obsah = number_format($vzkd_rest,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $obsah = number_format($vzkd_rest_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $pdfobjekt->SetTextColor(0,0,0);
    $cell='vzkd_soll_tag';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', 'LR', 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', 'LR', 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $obsah = number_format(floatval(getValueForNode($childs, 'gew_gesamtabgnr')),0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}


function radek_tatnr_sestava($pdfobjekt, $vyskaradku, $rgb, $pole,$abgnr,$sumvzkdgesamt) {
    global $cells;
    
    global $fillSoll;
    global $fillIst;
    global $fillRest;
    global $fillTag;
    global $fillRw;
    
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;
    $pdfobjekt->SetFont("FreeSans", "", 8);

    $tatnr = $abgnr;
    $vzkd_gesamt = $pole['vzkd_gesamt'];
    $vzkd_gesamt_pct = $sumvzkdgesamt!=0?$vzkd_gesamt / $sumvzkdgesamt *100:0;
    $vzkd_bearbeitet = $pole['vzkd_bearbeitet'];
    $gew_gesamtabgnr = $pole['gew_gesamtabgnr'];
    $vzkd_bearbeitet_pct = $vzkd_gesamt!=0?$vzkd_bearbeitet / $vzkd_gesamt *100:0;
    $vzkd_rest = $vzkd_gesamt-$vzkd_bearbeitet;
    
    if($vzkd_rest<0){
	$bCervenaNula = TRUE;
	$vzkd_rest=0;
    }	
    else{
	$bCervenaNula = FALSE;
    }
    
    $vzkd_rest_pct = $vzkd_gesamt!=0?$vzkd_rest / $vzkd_gesamt *100:0;
    //prvniradek
    $cell='kunde';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$tatnr, $cells[$cell]['ram'], 0, 'R', $fill);
//    $pdfobjekt->SetFillColor($fillSoll[0], $fillSoll[1], $fillSoll[2], 1);
    $cell='vzkd_soll';
    $obsah = number_format($vzkd_gesamt,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_soll_pct';
    $obsah = number_format($vzkd_gesamt_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillIst[0], $fillIst[1], $fillIst[2], 1);
    $cell='vzkd_bearbeitet';
    $obsah = number_format($vzkd_bearbeitet,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_bearbeitet_pct';
    $obsah = number_format($vzkd_bearbeitet_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillRest[0], $fillRest[1], $fillRest[2], 1);
    if($bCervenaNula)
	$pdfobjekt->SetTextColor(255,0,0);

    $cell='vzkd_rest';
    $obsah = number_format($vzkd_rest,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='vzkd_rest_pct';
    $obsah = number_format($vzkd_rest_pct,1,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillTag[0], $fillTag[1], $fillTag[2], 1);
    $pdfobjekt->SetTextColor(0,0,0);
    $cell='vzkd_soll_tag';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', 'LR', 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($fillRw[0], $fillRw[1], $fillRw[2], 1);
    $cell='reichweite';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', 'LR', 0, $cells[$cell]['align'], $fill);
//    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $cell='gew_abgnr';
    $obsah = number_format($gew_gesamtabgnr,0,',',' ');
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,$obsah, $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
//    $cell='gew_ges';
//    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 0, $cells[$cell]['align'], $fill);
    $cell='lf';
    $pdfobjekt->Cell($cells[$cell]['sirka'], $vyskaradku,'', $cells[$cell]['ram'], 1, $cells[$cell]['align'], 0);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}
				
function test_pageoverflow_nopage($pdfobjekt,$testvysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		return TRUE;
	}
	else
		return FALSE;
}
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$aktDatum = date('Y-m-d H:i:s');
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S216 VzKd Stand - Dispo   $aktDatum", $params);
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
// a ted pujdu po zakaznicich

$kunden=$domxml->getElementsByTagName("kunde");
// na zacatku zjistim celkovou sumu pro vzkd_gesamt pro jednotlive zakazniky
foreach ($kunden as $kunde) {
    $stats = $kunde->getElementsByTagName("stat");
    $kundeChilds = $kunde->childNodes;
    $kundenr = getValueForNode($kundeChilds, 'kd');
    foreach ($stats as $stat) {
	$tats = $stat->getElementsByTagName("tat");
	foreach ($tats as $tat) {
	    $tatChilds = $tat->childNodes;
	    $summeVzKdGesamt[$kundenr]+=floatval(getValueForNode($tatChilds, 'vzkd_gesamt'));
	}
    }
}

//echo "<pre>";
//var_dump($summeVzKdGesamt);
//echo "</pre>";

foreach ($kunden as $kunde) {
    $pdf->AddPage();
    nuluj_sumy_pole($sum_zapati_kunde_array);
    $kundeChilds = $kunde->childNodes;
    $kundenr = getValueForNode($kundeChilds, 'kd');
    zahlavi_kunde($pdf, 5, array(230, 255, 230), $kundeChilds);
    $stats = $kunde->getElementsByTagName("stat");
    foreach ($stats as $stat) {
	$statChilds = $stat->childNodes;
	$statnr = getValueForNode($statChilds, 'statnr');
	$tats = $stat->getElementsByTagName("tat");
	nuluj_sumy_pole($sum_zapati_statnr_array);
	foreach ($tats as $tat) {
	    $tatChilds = $tat->childNodes;
	    $abgnr = getValueForNode($tatChilds, 'abgnr');
	    radek_tatnr($pdf, 5, array(255, 255, 255), $tatChilds, $summeVzKdGesamt[$kundenr]);
	    foreach ($sum_zapati_statnr_array as $key => $prvek) {
		$hodnota = $tat->getElementsByTagName($key)->item(0)->nodeValue;
		$sum_zapati_statnr_array[$key]+=$hodnota;
		$sum_zapati_sestava_array[$statnr][$abgnr][$key]+=$hodnota;
	    }
	    $rest = floatval(getValueForNode($tatChilds,"vzkd_gesamt"))-floatval(getValueForNode($tatChilds,"vzkd_bearbeitet"));
	    if($rest<0) $rest=0;
	    $sum_zapati_statnr_array["vzkd_rest"]+=$rest;
	    $sum_zapati_sestava_array[$statnr][$abgnr]["vzkd_rest"]+=$rest;
	}
	zapati_statnr($pdf, 5, array(255, 255, 255), $statChilds, $sum_zapati_statnr_array, $summeVzKdGesamt[$kundenr]);
	foreach ($sum_zapati_kunde_array as $key => $prvek) {
	    $hodnota = $sum_zapati_statnr_array[$key];
	    $sum_zapati_kunde_array[$key]+=$hodnota;
	}
	$sum_zapati_kunde_array["vzkdplan"]+=floatval(getValueForNode($statChilds, "vzkdplan"));
	$vzkdplanArray[$statnr]+=floatval(getValueForNode($statChilds, "vzkdplan"));
    }
    zapati_kunde($pdf, 5, array(255, 255, 255), $kundeChilds, $sum_zapati_kunde_array, $summeVzKdGesamt[$kundenr]);
}

//echo "<pre>";
//var_dump($sum_zapati_sestava_array);
//echo "</pre>";

// zapati sestavy
$pdf->AddPage();
//spocitam celkovou sumu pro vsechny kd
foreach($summeVzKdGesamt as $hodnota){
    $sumVzkdGesamtSestava+=$hodnota;
}

zahlavi_kunde($pdf, 5, array(230, 255, 230), $kundeChilds,TRUE);
$statnrKeys = array_keys($sum_zapati_sestava_array);
sort($statnrKeys);
foreach ($statnrKeys as $statnr){
    $abgnrKeys = array_keys($sum_zapati_sestava_array[$statnr]);
    sort($abgnrKeys);
    nuluj_sumy_pole($sum_zapati_statnr_sestava_array);
    foreach ($abgnrKeys as $abgnr){
	if(test_pageoverflow_nopage($pdf,5))
	    zahlavi_kunde($pdf, 5, array(230, 255, 230), $kundeChilds,TRUE);
	radek_tatnr_sestava($pdf, 5, array(255,255,255), $sum_zapati_sestava_array[$statnr][$abgnr],$abgnr,$sumVzkdGesamtSestava);
	foreach ($sum_zapati_statnr_sestava_array as $key => $prvek) {
		$hodnota = $sum_zapati_sestava_array[$statnr][$abgnr][$key];
		if($key!="vzkd_rest")
		    $sum_zapati_statnr_sestava_array[$key]+=$hodnota;
	}
	$sum_zapati_statnr_sestava_array["vzkd_rest"]+=$sum_zapati_sestava_array[$statnr][$abgnr]["vzkd_rest"];
//	echo "$statnr:$abgnr<br>";
//	echo "<pre>";
//	var_dump($sum_zapati_statnr_sestava_array);
//	echo "</pre>";
    }
    $sum_zapati_statnr_sestava_array['vzkdplan']=$vzkdplanArray[$statnr];
    if(test_pageoverflow_nopage($pdf,5))
        zahlavi_kunde($pdf, 5, array(230, 255, 230), $kundeChilds,TRUE);
    zapati_statnr($pdf, 5, array(255, 255, 255), $statChilds,$sum_zapati_statnr_sestava_array,$sumVzkdGesamtSestava,TRUE,$statnr);
    foreach ($sum_zapati_kunde_sestava_array as $key => $prvek) {
	    $hodnota = $sum_zapati_statnr_sestava_array[$key];
	    $sum_zapati_kunde_sestava_array[$key]+=$hodnota;
    }
}
if(test_pageoverflow_nopage($pdf,5))
    zahlavi_kunde($pdf, 5, array(230, 255, 230), $kundeChilds,TRUE);
zapati_kunde($pdf, 5, array(255, 255, 255), $kundeChilds,$sum_zapati_kunde_sestava_array,$sumVzkdGesamtSestava,TRUE);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
