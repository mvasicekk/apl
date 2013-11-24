<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S795";
$doc_subject = "S795 Report";
$doc_keywords = "S795";

// centralni databazovy objekt

$aplDB = AplDB::getInstance();

// necham si vygenerovat XML

$parameters=$_GET;
$kunde_von=$_GET['kunde_von'];
$kunde_bis=$_GET['kunde_bis'];
$auftr_von=make_DB_datum($aplDB->validateDatum($_GET['auftr_von']));
$rm_von=make_DB_datum($aplDB->validateDatum($_GET['rm_von']));
$rm_bis=make_DB_datum($aplDB->validateDatum($_GET['rm_bis']));
$zeitpunkt=make_DB_datum($aplDB->validateDatum($_GET['zeitpunkt']));

$parameters['auftr_von']=$aplDB->validateDatum($_GET['auftr_von']);
$parameters['rm_von']=$aplDB->validateDatum($_GET['rm_von']);
$parameters['rm_bis']=$aplDB->validateDatum($_GET['rm_bis']);
$parameters['zeitpunkt']=$aplDB->validateDatum($_GET['zeitpunkt']);

//echo "<br>auftr_von=$auftr_von";
//echo "<br>rm_von=$rm_von";
//echo "<br>rm_bis=$rm_bis";
//echo "<br>zeitpunkt=$zeitpunkt";
//
//exit;

require_once('S795_xml.php');



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
"ausliefer_datum"
=> array ("format"=>array(7,'B'),"popis"=>"","sirka"=>20,"ram"=>'L',"align"=>"R","radek"=>0,"fill"=>0),

"exportnr"
=> array ("format"=>array(7,'B'),"popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkdex"
=> array ("format"=>array(7,'B'),"nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"dummy1"
=> array ("format"=>array(7,'B'),"popis"=>"","sirka"=>20*4,"ram"=>'R',"align"=>"R","radek"=>0,"fill"=>0),

"dummy"
=> array ("format"=>array(7,'B'),"popis"=>"","sirka"=>0,"ram"=>'R',"align"=>"R","radek"=>1,"fill"=>0),

);

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
    ""=>0,
    "delta_vor"=>0,
    "delta_in"=>0,
    "delta_nach"=>0
);
global $sum_zapati_kunde_array;
//-------------------------------------------------

//-------------------------------------------------
$sum_zapati_sestava_array = array(
    ""=>0,
    "delta_vor"=>0,
    "delta_in"=>0,
    "delta_nach"=>0
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
    $pdfobjekt->Cell(0,$vyskaradku,"KundeNr: ".getValueForNode($childs,"kundenr"),'1',1,'L',$fill);
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


function zahlavi_import($pdfobjekt,$nodes,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 9);
	$fill=1;
    $pdfobjekt->Cell(
        20+25+25,
        $vyskaradku,
        "Import: ".getValueForNode($nodes,"importnr")." ( ".getValueForNode($nodes, "aufdat")." )",
        'LT',
        0,
        'L',
        $fill
    );

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        "",
        'T',
        0,
        'L',
        $fill
    );

    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        "VOR+IN",
        'T',
        0,
        'R',
        $fill
    );

    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        "VOR",
        'T',
        0,
        'R',
        $fill
    );

    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        "IN",
        'TR',
        0,
        'R',
        $fill
    );

    $pdfobjekt->Cell(
        0,
        $vyskaradku,
        "NACH",
        'LTR',
        1,
        'R',
        $fill
    );

// druhy radek

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->Cell(
        20+25+25,
        $vyskaradku,
        "",
        'L',
        0,
        'L',
        $fill
    );

    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        "VzKd (Drueck)",
        '0',
        0,
        'L',
        $fill
    );

    $obsah = $obsah=number_format(getValueForNode($nodes, "sumvzkd"),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        '0',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(getValueForNode($nodes, "sumvzkd_vor"),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        '0',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(getValueForNode($nodes, "sumvzkd_in"),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        '0',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(getValueForNode($nodes, "sumvzkd_nach"),0,',',' ');
    $pdfobjekt->Cell(
        0,
        $vyskaradku,
        $obsah,
        'LR',
        1,
        'R',
        $fill
    );

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $nodes
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $sumArray
 */
function zapati_kunde($pdfobjekt,$nodes,$vyskaradku,$rgb,$sumArray){

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 9);
	$fill=1;
    $pdfobjekt->Cell(
        20+25+25+20,
        $vyskaradku,
        "Summe Kunde: ".getValueForNode($nodes, "kundenr"),
        'LT',
        0,
        'L',
        $fill
    );

    $pdfobjekt->SetFont("FreeSans", "B", 7);

    $obsah = $obsah=number_format($sumArray['delta_vor']+$sumArray['delta_in'],0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'T',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format($sumArray['delta_vor'],0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'T',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format($sumArray['delta_in'],0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'TR',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format($sumArray['delta_nach'],0,',',' ');
    $pdfobjekt->Cell(
        0,
        $vyskaradku,
        $obsah,
        'LTR',
        1,
        'R',
        $fill
    );

}


/**
 *
 * @param <type> $pdfobjekt
 * @param <type> $nodes
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $sumArray
 */
function zapati_sestava($pdfobjekt,$nodes,$vyskaradku,$rgb,$sumArray){

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 9);
	$fill=1;
    $pdfobjekt->Cell(
        20+25+25+20,
        $vyskaradku,
        "Summe Bericht ",
        'LTB',
        0,
        'L',
        $fill
    );

    $pdfobjekt->SetFont("FreeSans", "B", 7);

    $obsah = $obsah=number_format($sumArray['delta_vor']+$sumArray['delta_in'],0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'TB',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format($sumArray['delta_vor'],0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'TB',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format($sumArray['delta_in'],0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'TRB',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format($sumArray['delta_nach'],0,',',' ');
    $pdfobjekt->Cell(
        0,
        $vyskaradku,
        $obsah,
        'LTRB',
        1,
        'R',
        $fill
    );

}

function zapati_import($pdfobjekt,$nodes,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFont("FreeSans", "B", 9);
	$fill=1;
    $pdfobjekt->Cell(
        20+25+25+20,
        $vyskaradku,
        "VzKd(Ex)",
        'LT',
        0,
        'L',
        $fill
    );

    $pdfobjekt->SetFont("FreeSans", "B", 7);

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "ex_vor_plus_in")),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'T',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "sumvzkdex_vor")),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'T',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "sumvzkdex_in")),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'TR',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "sumvzkdex_nach")),0,',',' ');
    $pdfobjekt->Cell(
        0,
        $vyskaradku,
        $obsah,
        'LTR',
        1,
        'R',
        $fill
    );

// druhy radek

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->Cell(
        20+25+25+20,
        $vyskaradku,
        "VzKd(DRUECK)-VzKd(Ex)",
        'LB',
        0,
        'L',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "delta_vor"))+floatval(getValueForNode($nodes, "delta_in")),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'B',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "delta_vor")),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'B',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "delta_in")),0,',',' ');
    $pdfobjekt->Cell(
        20,
        $vyskaradku,
        $obsah,
        'B',
        0,
        'R',
        $fill
    );

    $obsah = $obsah=number_format(floatval(getValueForNode($nodes, "delta_nach")),0,',',' ');
    $pdfobjekt->Cell(
        0,
        $vyskaradku,
        $obsah,
        'LRB',
        1,
        'R',
        $fill
    );

    $pdfobjekt->Ln();
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

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S795 Abgrenzungstabelle", $params);
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
//pageheader($pdf,$cells_header,3.5);
//$pdf->Ln();

// a ted pujdu po zakaznicich
$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChilds = $kunde->childNodes;
//    nuluj_sumy_pole($sum_zapati_kunde_array);
//    test_pageoverflow($pdf,5,$cells_header);
    zahlavi_kunde($pdf, $kundeChilds,5,array(245,255,245));

	$importe = $kunde->getElementsByTagName("import");
    nuluj_sumy_pole($sum_zapati_kunde_array);

	foreach($importe as $import)
	{
		$importChilds=$import->childNodes;
//        nuluj_sumy_pole($sum_zapati_mesic_array);
		// zahlavi pro mesic
//		test_pageoverflow($pdf,5,$cells_header);
        zahlavi_import($pdf,$importChilds,5,array(245,245,255));

        $exporte = $import->getElementsByTagName("export");
        foreach($exporte as $export){
             $exportChilds = $export->childNodes;
             telo($pdf,$cells,3.5,array(255,255,255),"",$exportChilds);
        }
        zapati_import($pdf,$importChilds,5,array(245,245,255));
		// aktualizuju sumy delta
        foreach($sum_zapati_kunde_array as $key=>$prvek)
		{
            $hodnota = getValueForNode($importChilds,$key);
			$sum_zapati_kunde_array[$key]+=$hodnota;
		}
    }

    zapati_kunde($pdf,$kundeChilds,5,array(245,255,245),$sum_zapati_kunde_array);
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
        $hodnota=$sum_zapati_kunde_array[$key];
        $sum_zapati_sestava_array[$key]+=$hodnota;
    }
}

zapati_sestava($pdf,$kundeChilds,5,array(245,245,245),$sum_zapati_sestava_array);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
