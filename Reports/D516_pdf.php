<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

//test svn
$doc_title = "D515";
$doc_subject = "D515 Report";
$doc_keywords = "D515";

// necham si vygenerovat XML

$parameters=$_GET;

$teil = $parameters['teil'];
$kunde = $parameters['kunde'];
$dokunr = $parameters['dokunr'];

require_once('D516_xml.php');



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
//    global $ersteller;
    
	$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $sirkaCelkem = 85;
        $vyskaCelkem = 45;

	$x_pocatek=$pdf->GetX();
	$y_pocatek=$pdf->GetY();

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
	
	$pdf->SetLineWidth(0.2);
	//kunde
        // ramecek
        $pdf->SetLineWidth(0.2);
        $sirkaKunde = 30;$vyskaKunde = 13;
	$xKunde = $x_pocatek;$yKunde = $y_pocatek;
        $pdf->Rect($x_pocatek,$y_pocatek,$sirkaKunde, $vyskaKunde);
        // popisek
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
	$pdf->SetXY($xKunde, $yKunde);
        $pdf->Cell($sirkaKunde, 5, 'Zakaznik/Kunde', '0', 1, 'L', 0);
        // obsah
	$pdf->SetXY($xKunde, $yKunde+5);
        $obsah = getValueForNode($childs, 'kunde');
        $pdf->SetFont('FreeSans', 'B',12);
        $pdf->Cell($sirkaKunde, 8, $obsah, '0', 1, 'L', 0);
	
	//deska
        //ramecek desku
        $pdf->SetLineWidth(0.2);
        $sirkaDeska = $sirkaCelkem-$sirkaKunde;$vyskaDeska = $vyskaKunde;
        $xDeska = $x_pocatek+$sirkaKunde;$yDeska = $y_pocatek;
        $pdf->Rect($xDeska,$yDeska,$sirkaDeska, $vyskaDeska);
        $pdf->SetXY($xDeska, $yDeska);
        // popisek
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaDeska, 5, 'Deska/Platte:', '0', 1, 'L', 0);
        // obsah
        $obsah = getValueForNode($childs, 'teillang');
        $pdf->SetFont('FreeSans', 'B',12);
        $pdf->SetX($xDeska);
        $pdf->Cell($sirkaDeska, 8, $obsah, '0', 1, 'L', 0);
	
	
	//dil
        //ramecek pro teil
        $pdf->SetLineWidth(0.2);
        $sirkaTeil = 60;$vyskaTeil = 16;
        $xTeil = $x_pocatek;$yTeil = $y_pocatek+$vyskaDeska;
        $pdf->Rect($xTeil,$yTeil,$sirkaTeil, $vyskaTeil);
        // popisek
        $pdf->SetXY($xTeil, $yTeil);
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaTeil, 5, 'Dil/Teil:', '0', 0, 'L', 0);
	//obsah
	$yTeil+= 5;
	$pdf->SetFont('FreeSans', 'B',18);
        $pdf->SetY($yTeil);
	$pdf->SetX($xTeil);
	$obsah = getValueForNode($childs, 'teilnr');
	$pdf->Cell($sirkaTeil, 7, $obsah, '0', 1, 'L', 0);
	$yTeil+=6;
	$pdf->SetY($yTeil);
	$pdf->SetX($xTeil);
	$obsah = getValueForNode($childs, 'teilbez');
	$pdf->SetFont('FreeSans', '',10);
	$pdf->Cell($sirkaTeil, 5, $obsah, '0', 1, 'L', 0);

	//hmotnost
        //ramecek
        $pdf->SetLineWidth(0.2);
        $sirkaGew = $sirkaCelkem-$sirkaTeil;$vyskaGew = $vyskaTeil;
        $xGew = $x_pocatek+$sirkaTeil;$yGew = $y_pocatek+$vyskaDeska;
        $pdf->Rect($xGew,$yGew,$sirkaGew, $vyskaGew);
        // popisek
        $pdf->SetXY($xGew, $yGew);
        $pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaGew, 5, 'Hmotnost/Gewicht:', '0', 0, 'L', 0);
	//obsah
	$yGew+= 5;
	$pdf->SetFont('FreeSans', 'B',10);
	$pdf->SetY($yGew);
	$pdf->SetX($xGew);
	$obsah = number_format(getValueForNode($childs, 'gew'),3,',',' ')." kg";
	$pdf->Cell($sirkaGew, 7, $obsah, '0', 1, 'L', 0);

	$a = AplDB::getInstance();
	$teil = getValueForNode($childs, 'teilnr');
	global $dokunr;
	
	$dokumentInfo = $a->getTeilDokument($teil, $dokunr, TRUE);
	if($dokumentInfo===NULL){
	    $dNR = $dokunr;
	    $dBeschr = "???";
	    $einlagDatum = "????-??-??";
	    $freigabeDatum = "????-??-??";
	    $freigabeVon = "???";
	    $platz = "???";
	}
	else{
    	    $dNR = $dokunr;
	    $dBeschr = $dokumentInfo['doku_beschreibung'];
	    $einlagDatum = substr($dokumentInfo['einlag_datum'],6,4).'-'.substr($dokumentInfo['einlag_datum'],3,2).'-'.substr($dokumentInfo['einlag_datum'],0,2);
	    $freigabeDatum = substr($dokumentInfo['freigabe_am'],6,4).'-'.substr($dokumentInfo['freigabe_am'],3,2).'-'.substr($dokumentInfo['freigabe_am'],0,2);
	    $freigabeVon = $dokumentInfo['freigabe_vom'];
	    $platz = $dokumentInfo['musterplatz'];
	}
	
	//dokunr
	//ramecek
        $pdf->SetLineWidth(0.2);
        $sirkaDokunr = 15;$vyskaDokunr = $vyskaCelkem-$vyskaDeska-$vyskaTeil;
        $xDokunr = $x_pocatek;$yDokunr = $y_pocatek+$vyskaDeska+$vyskaTeil;
        $pdf->Rect($xDokunr,$yDokunr,$sirkaDokunr, $vyskaDokunr);
        // popisek
        $pdf->SetY($yDokunr);
	$pdf->SetX($xDokunr);
	$pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaTeil, 5, 'Teiledoku:', '0', 0, 'L', 0);
	//obsah
	$yDokunr+= 5;
	$pdf->SetFont('FreeSans', 'B',10);
	$pdf->SetY($yDokunr);
        $pdf->SetX($xDokunr);
	$obsah = $dNR;
	$pdf->Cell($sirkaDokunr, 7, $obsah, '0', 1, 'C', 0);
	$yDokunr+=6;
	$pdf->SetY($yDokunr);
	$pdf->SetX($xDokunr);
	$obsah = $dBeschr;
	$pdf->SetFont('FreeSans', '',5);
	$pdf->Cell($sirkaDokunr, 5, $obsah, '0', 1, 'C', 0);

	//einlagerdatum
	//ramecek
        $pdf->SetLineWidth(0.2);
        $sirkaED = 18;$vyskaED = $vyskaCelkem-$vyskaDeska-$vyskaTeil;
        $xED = $x_pocatek+$sirkaDokunr;$yED = $y_pocatek+$vyskaDeska+$vyskaTeil;
        $pdf->Rect($xED,$yED,$sirkaED, $vyskaED);
        // popisek
        $pdf->SetY($yED);
	$pdf->SetX($xED);
	$pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaED, 5, 'Einlag.Datum:', '0', 0, 'L', 0);
	//obsah
	$yED+= 5;
	$pdf->SetFont('FreeSans', 'B',9);
	$pdf->SetY($yED);
        $pdf->SetX($xED);
	$obsah = $einlagDatum;
	$pdf->Cell($sirkaED, 7, $obsah, '0', 1, 'C', 0);

	//freigabe
	//ramecek
        $pdf->SetLineWidth(0.2);
        $sirkaFA = 27;$vyskaFA = $vyskaCelkem-$vyskaDeska-$vyskaTeil;
        $xFA = $x_pocatek+$sirkaDokunr+$sirkaED;$yFA = $y_pocatek+$vyskaDeska+$vyskaTeil;
        $pdf->Rect($xFA,$yFA,$sirkaFA, $vyskaFA);
        // popisek
        $pdf->SetY($yFA);
	$pdf->SetX($xFA);
	$pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaFA, 5, 'Freigabe am/von:', '0', 0, 'L', 0);
	//obsah
	$yFA+= 5;
	$pdf->SetFont('FreeSans', 'B',9);
	$pdf->SetY($yFA);
        $pdf->SetX($xFA);
	$obsah = $freigabeDatum;
	$pdf->Cell($sirkaFA, 7, $obsah, '0', 1, 'C', 0);
	$yFA+=6;
	$pdf->SetY($yFA);
	$pdf->SetX($xFA);
	$obsah = $freigabeVon;
	$pdf->SetFont('FreeSans', '',6);
	$pdf->Cell($sirkaFA, 5, $obsah, '0', 1, 'C', 0);

	//musterplatz
	//ramecek
        $pdf->SetLineWidth(0.2);
        $sirkaMP = $sirkaCelkem-$sirkaDokunr-$sirkaED-$sirkaFA;$vyskaMP = $vyskaCelkem-$vyskaDeska-$vyskaTeil;
        $xMP = $x_pocatek+$sirkaDokunr+$sirkaED+$sirkaFA;$yMP = $y_pocatek+$vyskaDeska+$vyskaTeil;
        $pdf->Rect($xMP,$yMP,$sirkaMP, $vyskaMP);
        // popisek
        $pdf->SetY($yMP);
	$pdf->SetX($xMP);
	$pdf->SetFont('FreeSans', 'B',$popiskyFontSize);
        $pdf->Cell($sirkaMP, 5, 'Musterplatz/Datei:', '0', 0, 'L', 0);
	//obsah
	$yMP+= 5;
	$pdf->SetFont('FreeSans', 'B',12);
	$pdf->SetY($yMP);
        $pdf->SetX($xMP);
	$obsah = $platz;
	$pdf->Cell($sirkaMP, 7, $obsah, '0', 1, 'C', 0);

//	
//	$pdf->SetFont('FreeSans', 'B',8);
//	$pdf->SetXY($x_pocatek,$y_pocatek+$vyskaCelkem);
//	$datumcas = date('d.m.Y H:i:s')." ( ".$_SESSION['user']." )";
//	$pdf->Cell($sirkaCelkem, 5, $datumcas, '0', 0, 'L', 0);
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D516 Musterkennzeichnung", '');
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
//    echo "radek=$radek, sloupec=$sloupec, stitek=$stitek<br>";
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

//$teile=$domxml->getElementsByTagName("teil");
//
//foreach ($teile as $teil) {
//    $teilChilds = $teil->childNodes;
//    stitek($pdf, 5, array(255,255,255), $teilChilds);
//}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
