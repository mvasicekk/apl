<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';
//test svn
$doc_title = "T011";
$doc_subject = "T011 Report";
$doc_keywords = "T011";

// necham si vygenerovat XML
$a = AplDB::getInstance();

$parameters=$_GET;

$teil = $parameters['teil'];
$kunde = $parameters['kunde'];
$ersteller = $parameters['ersteller'];
$datumvon = $a->make_DB_datum($parameters['datumvon']);
$datumbis = $a->make_DB_datum($parameters['datumbis']);

require_once('T011_xml.php');


//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
//$parameters=$domxml->getElementsByTagName("parameters");
//
//
//foreach ($parameters as $param)
//{
//	$parametry=$param->childNodes;
//	// v ramci parametru si prectu label a hodnotu
//	foreach($parametry as $parametr)
//	{
//		$parametr=$parametr->childNodes;
//		foreach($parametr as $par)
//		{
//			if($par->nodeName=="label")
//				$label=$par->nodeValue;
//			if($par->nodeName=="value")
//				$value=$par->nodeValue;
//		}
//		$params .= $label.": ".$value."  ";
//	}
//}


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

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

/**
 *
 * @param TCPDF $pdf
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $childs
 * @param <type> $datvon
 * @param <type> $tagen
 */
function stitek($pdf,$vyskaradku,$rgb,$childs,$radek,$sloupec)
{
    global $ersteller;
    
	$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $sirkaCelkem = 85;
        $vyskaCelkem = 45;
	$zapatiStitek=5;
	$horizMezeraMeziStitky = 5;
	
        $x_pocatek=PDF_MARGIN_LEFT;
	$y_pocatek=PDF_MARGIN_TOP-10;

	$x_pocatek+=$sloupec*($sirkaCelkem+$horizMezeraMeziStitky);
	$y_pocatek+=$radek*($vyskaCelkem+$zapatiStitek);
	
	$xa = $x_pocatek;
	$ya = $y_pocatek;
	
        $popiskyFontSize = 7;
        // celkovy ramecek
        $pdf->SetLineWidth(0.5);
        $pdf->Rect($x_pocatek,$y_pocatek,$sirkaCelkem, $vyskaCelkem);
        // ramecek pro Deska/Plate
        $pdf->SetLineWidth(0.2);
        $sirkaDeska = 55;$vyskaDeska = 13;
        $pdf->Rect($x_pocatek,$y_pocatek,$sirkaDeska, $vyskaDeska);
        // popisek
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);

	$pdf->SetXY($x_pocatek, $y_pocatek);
	$pdf->Cell($sirkaDeska, 5, 'Deska/Plate:                   T011 Lagerzettel', '0', 1, 'L', 0);
//	$pdf->Cell($sirkaDeska, 5, 'Deska/Plate: T011 Lagerzettel', '0', 1, 'L', 0);
        // obsah
        $obsah = getValueForNode($childs, 'teillang');
        $pdf->SetFont('FreeSans', 'B',18);
	$pdf->SetXY($x_pocatek, $pdf->GetY());
        $pdf->Cell($sirkaDeska, 8, $obsah, '0', 1, 'L', 0);
//
        //ramecek pro zakaznik/Kunde
        $pdf->SetLineWidth(0.2);
        $sirkaKunde = $sirkaCelkem-$sirkaDeska;$vyskaKunde = $vyskaDeska;
        $xKunde = $x_pocatek+$sirkaDeska;$yKunde = $y_pocatek;
        $pdf->Rect($xKunde,$yKunde,$sirkaKunde, $vyskaKunde);
        $pdf->SetXY($xKunde, $yKunde);
        // popisek
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaKunde, 5, 'Zakaznik/Kunde:', '0', 1, 'L', 0);
        // obsah
        $obsah = getValueForNode($childs, 'kunde');
        $pdf->SetFont('FreeSans', 'B',12);
        $pdf->SetX($xKunde);
        $pdf->Cell($sirkaKunde, 8, $obsah, '0', 1, 'C', 0);
        //ramecek pro teil
        $pdf->SetLineWidth(0.2);
        $sirkaTeil = $sirkaCelkem;$vyskaTeil = 16;
        $xTeil = $x_pocatek;$yTeil = $y_pocatek+$vyskaDeska;
        $pdf->Rect($xTeil,$yTeil,$sirkaTeil, $vyskaTeil);
        // popisek
        $pdf->SetXY($xTeil, $yTeil);
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaTeil/2, 5, 'Dil/Teil:', '0', 0, 'L', 0);
        $pdf->Cell(12, 5, 'Freigabe:', '0', 0, 'L', 0);
        // obsah freigabe
        $obsah = getValueForNode($childs, 'freigabe1');
        $pdf->SetFont('FreeSans', '',$popiskyFontSize);
//        $pdf->SetX($xTeil);
        $pdf->Cell(13, 3, $obsah, '0', 0, 'L', 0);
        $obsah = 'am: '.getValueForNode($childs, 'freigabe1_vom');
        $pdf->Cell(12, 3, $obsah, '0', 1, 'L', 0);
        $pdf->SetX($xTeil+$sirkaTeil/2+12);
        $obsah = getValueForNode($childs, 'freigabe2');
        $pdf->SetFont('FreeSans', '',$popiskyFontSize);
        $pdf->Cell(13, 3, $obsah, '0', 0, 'L', 0);
        $obsah = 'am: '.getValueForNode($childs, 'freigabe2_vom');
        $pdf->Cell(12, 3, $obsah, '0', 1, 'L', 0);
        $pdf->Rect($xTeil+$sirkaTeil/2, $yTeil, $sirkaTeil/2, 6);
//        //eingelagert am
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->SetX($xTeil+$sirkaTeil/2);
        $pdf->Cell(12+13, 5, 'Einlag.Datum:', 'LB', 0, 'L', 0);
        $obsah = getValueForNode($childs, 'muster_vom');
        $pdf->SetFont('FreeSans', '',$popiskyFontSize);
        $pdf->Cell(17, 5, $obsah, 'B', 1, 'R', 0);
//	// ersteller
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->SetX($xTeil+$sirkaTeil/2);
        $pdf->Cell(12+13, 5, 'Ersteller:', 'LB', 0, 'L', 0);
        $obsah = $ersteller;
        $pdf->SetFont('FreeSans', '',$popiskyFontSize);
        $pdf->Cell(17, 5, $obsah, 'B', 0, 'R', 0);
//        
//        // obsah 
        $obsah = getValueForNode($childs, 'teilnr');
        $pdf->SetFont('FreeSans', 'B',16);

	$pdf->SetY($yTeil+5);
	$pdf->SetX($xTeil);
        $pdf->Cell($sirkaTeil, 5, $obsah, '0', 1, 'L', 0);
//        // obsah popis dilu
        $obsah = getValueForNode($childs, 'teilbez');
        $pdf->SetFont('FreeSans', '',11);
        $pdf->SetX($xTeil);
        $pdf->Cell($sirkaTeil, 5, $obsah, '0', 1, 'L', 0);
//
//        //ramecek pro gewicht
        $pdf->SetLineWidth(0.2);
        $sirkaGew = 25;$vyskaGew = $vyskaCelkem-$vyskaDeska-$vyskaTeil;
        $xGew = $x_pocatek;$yGew = $y_pocatek+$vyskaDeska+$vyskaTeil;
        $pdf->Rect($xGew,$yGew,$sirkaGew, $vyskaGew);
//        // popisek
        $pdf->SetXY($xGew, $yGew);
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaGew, 5, 'Hmotnost/Gewicht:', '0', 1, 'L', 0);
//        // obsah hmotnost
        $obsah = number_format(getValueForNode($childs, 'gew'),3,',',' ')." kg";
        $pdf->SetFont('FreeSans', '',10);
        $pdf->SetX($xGew);
        $pdf->Cell($sirkaGew, 6, $obsah, '0', 1, 'C', 0);
//
//        //ramecek pro regal
        $pdf->SetLineWidth(0.2);
        $sirkaRegal = $sirkaCelkem-$sirkaGew;$vyskaRegal = $vyskaCelkem-$vyskaDeska-$vyskaTeil;
        $xRegal = $x_pocatek+$sirkaGew;$yRegal = $y_pocatek+$vyskaDeska+$vyskaTeil;
        $pdf->Rect($xRegal,$yRegal,$sirkaRegal, $vyskaRegal);
//        // popisek
        $pdf->SetXY($xRegal, $yRegal);
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaRegal, 5, 'Regal:', '0', 1, 'L', 0);
//        // obsah hmotnost
//        // provedu odvedu
        $obsah = getValueForNode($childs, 'platz');
        $pdf->SetFont('FreeSans', 'B',16);
        $pdf->SetX($xRegal);
        $pdf->Cell($sirkaRegal, 6, $obsah, '0', 1, 'C', 0);
//	
	$pdf->SetFont('FreeSans', 'B',8);
	$pdf->SetXY($x_pocatek,$y_pocatek+$vyskaCelkem);
	$datumcas = date('d.m.Y H:i:s')." ( ".$_SESSION['user']." )";
	$pdf->Cell($sirkaCelkem, 5, $datumcas, '0', 0, 'L', 0);
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "T011 Lagerzettel", '');
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



// prvni stranka
$pdf->AddPage();

$teile=$domxml->getElementsByTagName("teil");

$pocetStitkuNaStranku = 10;
$stitek = 1;
$radek=0;
$sloupec=0;
foreach ($teile as $teil) {
    $teilChilds = $teil->childNodes;
    stitek($pdf, 5, array(255,255,255), $teilChilds,$radek,$sloupec);
    
    $stitek++;
    $sloupec++;
    if($sloupec>1){
	$sloupec=0;
	$radek++;
    }
    if($stitek>$pocetStitkuNaStranku){
	$stitek=1;
	$radek=0;$sloupec=0;
	$pdf->AddPage();
    }
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
