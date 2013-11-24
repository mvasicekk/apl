<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S790";
$doc_subject = "S790 Report";
$doc_keywords = "S790";

// necham si vygenerovat XML

$parameters=$_GET;
$kunde_von=$_GET['kunde_von'];
$kunde_bis=$_GET['kunde_bis'];
$ausliefer_von=make_DB_datum($_GET['ausliefer_von']);
$ausliefer_bis=make_DB_datum($_GET['ausliefer_bis']);


require_once('S790_xml.php');


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
// format pole s parametrama velikost fontu,'B' = bold

$cells = 
array(
"auftragsnr"
=> array ("format"=>array(7,'B'),"popis"=>"","sirka"=>10,"ram"=>'BL',"align"=>"R","radek"=>0,"fill"=>0),

"wert"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"waehr"
=> array ("popis"=>"","sirka"=>7,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd_dauftr"
=> array ("format"=>array(7,'B'),"nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd"
=> array ("format"=>array(7,'B'),"nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd1999" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby1999" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby3999" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"dummy3" 
=> array ("popis"=>"","sirka"=>35,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"ton"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),

"eur_pro_tonne" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"fac1" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),

"fac2" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"aufdat"
=> array ("popis"=>"","sirka"=>15,"ram"=>'LBR',"align"=>"L","radek"=>0,"fill"=>0),

"fertig" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"ausliefer" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"L","radek"=>1,"fill"=>0));

global $cells;

$kunde_header = 
array(
"kunde" 
=> array ("popis"=>"kunde","sirka"=>0,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1)
);



$cells_header = 
array(
"auftragsnr" 
=> array ("popis"=>"\nImport","sirka"=>10,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),

"wert" 
=> array ("popis"=>"\nWert","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),

"waehr"
=> array ("popis"=>"\nWähr","sirka"=>7,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"kdminrechn" 
=> array ("popis"=>"kdmin.\nAuftrag","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"kdmin" 
=> array ("popis"=>"RM\nkdmin","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"abymin" 
=> array ("popis"=>"RM\nabymin","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"verb" 
=> array ("popis"=>"RM\nverb","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"vzkd1999" 
=> array ("popis"=>"vzkd\n9X or >1999","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"vzaby19999" 
=> array ("popis"=>"vzaby\n9X or >1999","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"vzaby3999" 
=> array ("popis"=>"vzaby\n >3999","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"dummy1" 
=> array ("popis"=>"\n","sirka"=>35,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"extonnen" 
=> array ("popis"=>"Auftr.\nTonnen","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"eur_pro_tonne" 
=> array ("popis"=>"EUR\nTonn","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"kdminabymin" 
=> array ("popis"=>"kdmin\nabymin","sirka"=>10,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"kdminverb" 
=> array ("popis"=>"kdmin\nverb","sirka"=>10,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"aufdat" 
=> array ("popis"=>"\nAufdat","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"rech" 
=> array ("popis"=>"\nRech","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"auslief" 
=> array ("popis"=>"\nAuslief","sirka"=>0,"ram"=>1,"align"=>"L","radek"=>1,"fill"=>1)
);

//-------------------------------------------------
$sum_zapati_kunde_array = array(
    "vzkd_dauftr"=>0,
    "vzkd"=>0,
    "vzaby"=>0,
    "verb"=>0,
    "vzkd1999"=>0,
    "vzaby1999"=>0,
    "vzaby3999"=>0,
    "ton"=>0,
    "numauftr"=>0,
);
global $sum_zapati_kunde_array;
//-------------------------------------------------

//-------------------------------------------------
$sum_zapati_mesic_array = array(
    "vzkd_dauftr"=>0,
    "vzkd"=>0,
    "vzaby"=>0,
    "verb"=>0,
    "vzkd1999"=>0,
    "vzaby1999"=>0,
    "vzaby3999"=>0,
    "ton"=>0,
    "numauftr"=>0,
);
global $sum_zapati_mesic_array;
//-------------------------------------------------

//-------------------------------------------------
$sum_zapati_sestava_array = array(
    "vzkd_dauftr"=>0,
    "vzkd"=>0,
    "vzaby"=>0,
    "verb"=>0,
    "vzkd1999"=>0,
    "vzaby1999"=>0,
    "vzaby3999"=>0,
    "ton"=>0,
    "numauftr"=>0,
);
global $sum_zapati_sestava_array;
//-------------------------------------------------

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
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
    $pdfobjekt->Ln();
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

        if(array_key_exists("format", $cell)){
            // pokud mam zadan pro policko format, tak ho nastavim
            $pdfobjekt->SetFont("FreeSans", $cell['format'][1], $cell['format'][0]);
        }
        else{
            // nejaky default
            $pdfobjekt->SetFont("FreeSans", "", 8);
        }
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

function zahlavi_kunde($pdfobjekt,$childs,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
    $pdfobjekt->Cell(0,$vyskaradku,"KundeNr: ".getValueForNode($childs,"kunde"),'1',1,'L',$fill);
//
//	$obsah=$node->getElementsByTagName("sumpreis_leistung_EUR")->item(0)->nodeValue;
//	$obsah=number_format($obsah,2,',',' ');
//	$pdfobjekt->Cell(50,$vyskaradku,"Re-Preis (Leistung) ".$obsah,'BT',0,'L',$fill);
//
//
//	$obsah=$node->getElementsByTagName("sumpreis_sonst_EUR")->item(0)->nodeValue;
//	$obsah=number_format($obsah,2,',',' ');
//	$pdfobjekt->Cell(50,$vyskaradku,"Re-Preis (Sonst.) ".$obsah,'BT',0,'L',$fill);
//
//	$obsah=$node->getElementsByTagName("preismin")->item(0)->nodeValue;
//	$obsah=number_format($obsah,3,',',' ');
//	$pdfobjekt->Cell(0,$vyskaradku,"preismin ".$obsah,'BTR',1,'L',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

/**
 * zapati pro zakaznika
 * @param <type> $pdfobjekt
 * @param <type> $childs
 * @param <type> $vyskaradku
 * @param <type> $rgb 
 * @param array $sumArray
 */
function zapati_kunde($pdfobjekt,$childs,$vyskaradku,$rgb,$sumArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
    $pdfobjekt->Cell(10+15+7,$vyskaradku,"Summe KundeNr: ".getValueForNode($childs,"kunde"),'LTB',0,'L',$fill);

    $obsah=number_format($sumArray['vzkd_dauftr'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzkd'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['verb'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzkd1999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby1999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby3999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(35,$vyskaradku,"",'TBR',0,'R',$fill);

    //ton
    $obsah=number_format($sumArray['ton'],1,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(15,$vyskaradku,"",'TBR',0,'R',$fill);

    //fac1
    if($sumArray['vzaby']!=0)
        $fac = $sumArray['vzkd'] / $sumArray['vzaby'];
    else
        $fac = 0;

    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //fac2
    if($sumArray['verb']!=0)
        $fac = $sumArray['vzkd'] / $sumArray['verb'];
    else
        $fac = 0;

    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(0,$vyskaradku," ".$sumArray['numauftr']." Aufträge",'TBR',1,'L',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_mesic($pdfobjekt,$popis,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 9);
	$fill=1;
	$pdfobjekt->Cell(0,$vyskaradku,"Auslieferung Monat: ".$popis,'LBTR',1,'L',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

/**
 * zapati sestavy
 * @param <type> $pdfobjekt
 * @param <type> $childs
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $sumArray 
 */
function zapati_sestava($pdfobjekt,$childs,$vyskaradku,$rgb,$sumArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 7);
	$fill=1;
    $pdfobjekt->Cell(10+15+7,$vyskaradku,"Summe Bericht: ",'LTB',0,'L',$fill);

    $obsah=number_format($sumArray['vzkd_dauftr'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzkd'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['verb'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzkd1999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby1999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby3999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(35,$vyskaradku,"",'TBR',0,'R',$fill);

    //ton
    $obsah=number_format($sumArray['ton'],1,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(15,$vyskaradku,"",'TBR',0,'R',$fill);

    //fac1
    if($sumArray['vzaby']!=0)
        $fac = $sumArray['vzkd'] / $sumArray['vzaby'];
    else
        $fac = 0;

    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //fac2
    if($sumArray['verb']!=0)
        $fac = $sumArray['vzkd'] / $sumArray['verb'];
    else
        $fac = 0;

    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(0,$vyskaradku," ".$sumArray['numauftr']." Aufträge",'TBR',1,'L',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

/**
 * zapati pro mesic
 * @param <type> $pdfobjekt
 * @param <type> $childs
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $sumArray
 */
function zapati_mesic($pdfobjekt,$childs,$vyskaradku,$rgb,$sumArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 7);
	$fill=1;
    $pdfobjekt->Cell(10+15+7,$vyskaradku,"Sum:".getValueForNode($childs,"mesic"),'LTB',0,'L',$fill);


    $obsah=number_format($sumArray['vzkd_dauftr'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzkd'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['verb'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzkd1999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby1999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    $obsah=number_format($sumArray['vzaby3999'],0,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(35,$vyskaradku,"",'TBR',0,'R',$fill);

    //ton
    $obsah=number_format($sumArray['ton'],1,',',' ');
    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(15,$vyskaradku,"",'TBR',0,'R',$fill);

    //fac1
    if($sumArray['vzaby']!=0)
        $fac = $sumArray['vzkd'] / $sumArray['vzaby'];
    else
        $fac = 0;

    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //fac2
    if($sumArray['verb']!=0)
        $fac = $sumArray['vzkd'] / $sumArray['verb'];
    else
        $fac = 0;

    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(10,$vyskaradku,$obsah,'TBR',0,'R',$fill);

    //dummy
    $pdfobjekt->Cell(0,$vyskaradku," ".$sumArray['numauftr']." Aufträge",'TBR',1,'L',$fill);



//    $obsah=number_format($sumArray['vzkd_dauftr'],0,',',' ');
//    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',1,'R',$fill);

//    $obsah=number_format($sumArray['vzkd_dauftr'],0,',',' ');
//    $pdfobjekt->Cell(15,$vyskaradku,$obsah,'TBR',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_ex($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1,$fac2,$ept)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	
	$pdfobjekt->Cell(15,$vyskaradku,$popis,'LBTR',0,'L',$fill);
	
	$obsah=$pole['sumpreis_sonst_EUR']+$pole['sumpreis_leistung_EUR'];
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['sumpreismin_leistung'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['kdmin'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BTR',0,'R',$fill);

	$obsah=$pole['abymin'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);
	
	$obsah=$pole['vzkd1999'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['vzaby1999'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);
	
	$obsah=$pole['vzaby3999'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BTR',0,'R',$fill);
	
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(35,$vyskaradku,$obsah,'BT',0,'R',$fill);
	
	$obsah=$pole['aufgew'];
	$obsah=number_format($obsah,1,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'LBT',0,'R',$fill);

	$obsah=$ept;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BTR',0,'R',$fill);

	$obsah=$fac1;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$fac2;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BTR',0,'R',$fill);

	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(12,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(12,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BTR',1,'R',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,3.5);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S790 Auftragsuebersicht nach Import", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
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

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,3.5);
$pdf->Ln();

// a ted pujdu po zakaznicich
$kunden=$domxml->getElementsByTagName("kunden");
foreach($kunden as $kunde)
{
    $kundeChilds = $kunde->childNodes;
    nuluj_sumy_pole($sum_zapati_kunde_array);
    test_pageoverflow($pdf,5,$cells_header);
    zahlavi_kunde($pdf, $kundeChilds,5,array(200,255,200));

	$mesice = $kunde->getElementsByTagName("mesic");
	foreach($mesice as $mesic)
	{
		$mesicChilds=$mesic->childNodes;
        nuluj_sumy_pole($sum_zapati_mesic_array);
		// zahlavi pro mesic
		test_pageoverflow($pdf,5,$cells_header);
        zahlavi_mesic($pdf,getValueForNode($mesicChilds,"mesicpopis"),5,array(230,230,255));
        $importy = $mesic->getElementsByTagName("import");
        foreach($importy as $import)
		{
            $importChilds = $import->childNodes;
            test_pageoverflow($pdf,3.5,$cells_header);
			telo($pdf,$cells,3.5,array(255,255,255),"",$importChilds);

			foreach($sum_zapati_mesic_array as $key=>$prvek)
			{
                $hodnota = getValueForNode($importChilds,$key);
				$sum_zapati_mesic_array[$key]+=$hodnota;
			}
            $sum_zapati_mesic_array['numauftr']++;
        }
        //projedu pole a aktualizuju sumy pro zapati kunde
		foreach($sum_zapati_kunde_array as $key=>$prvek)
		{
            $hodnota=$sum_zapati_mesic_array[$key];
            $sum_zapati_kunde_array[$key]+=$hodnota;
        }
        //zapati mesic
        test_pageoverflow($pdf,5,$cells_header);
        zapati_mesic($pdf, $mesicChilds,5,array(230,230,255),$sum_zapati_mesic_array);
    }

    // zapati kunde
    test_pageoverflow($pdf,5,$cells_header);
    zapati_kunde($pdf, $kundeChilds,5,array(200,255,200),$sum_zapati_kunde_array);
    //odstrankuju
    $pdf->AddPage();
    pageheader($pdf,$cells_header,3.5);
    $pdf->Ln();
    //projedu pole a aktualizuju sumy pro zapati sestavy
    foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
           $hodnota=$sum_zapati_kunde_array[$key];
           $sum_zapati_sestava_array[$key]+=$hodnota;
    }

}

test_pageoverflow($pdf,5,$cells_header);
zapati_sestava($pdf, $kundeChilds,5,array(240,240,240),$sum_zapati_sestava_array);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
