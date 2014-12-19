<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D230";
$doc_subject = "D230 Report";
$doc_keywords = "D230";

// necham si vygenerovat XML

$parameters=$_GET;

$palvon=$_GET['palvon'];
$palbis=$_GET['palbis'];
$auftragsnr=$_GET['auftragsnr'];

require_once('D230_xml.php');


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

"Teil" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"import" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text" 
=> array ("popis"=>"","sirka"=>33,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"expstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss2_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss4_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss6_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vypln" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_header = 
array(

"Teil" 
=> array ("popis"=>"Teil","sirka"=>25,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"Import" 
=> array ("popis"=>"Import","sirka"=>25,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"Leistung" 
=> array ("popis"=>"Leistung","sirka"=>33,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"gutstk" 
=> array ("popis"=>"gute Stk","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"auss2" 
=> array ("popis"=>"auss2","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"auss4" 
=> array ("popis"=>"auss4","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),
	
"auss6" 
=> array ("popis"=>"auss6","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),
	
"bestellt" 
=> array ("popis"=>"Bestellt","sirka"=>15,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"gew" 
=> array ("popis"=>"Gew","sirka"=>0,"ram"=>'TB',"align"=>"R","radek"=>1,"fill"=>1)

);


$sum_zapati_import_array = array(	
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"expstk"=>0,
								"gew_auss"=>0,
								);
global $sum_zapati_import_array;

$sum_zapati_sestava_array = array(	
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"geliefert"=>0,
								"gew_auss"=>0,
								);
global $sum_zapati_sestava_array;

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
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
// zobraz_cinnosti($pdf,$taetigkeiten);
//
function zobraz_cinnosti($pdfobjekt,$taetigkeiten,$teil,$yOffset=0)
{
    $a = AplDB::getInstance();
    $cinnostMaxLen = 38;
	// vytahnu si jeste vsechny zaskrtnute komentace pro dany dil, vrati mi asociativni pole
        $k2 = getTatAktivFromTeilDpos($teil,2,1);
        $k3 = getTatAktivFromTeilDpos($teil,3,1);
        $komentare = array();
        if(is_array($k2))
            $komentare = array_merge($komentare,$k2);
        if(is_array($k3))
            $komentare = array_merge($komentare,$k3);
	
		
	$x_pocatek=125;
	$y_pocatek=23+$yOffset;
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek);
	$pdfobjekt->Cell(10,3,"taetnr",'B',0,'L');
	$pdfobjekt->Cell(42,3,"Bezeichnung",'B',0,'L');
	$pdfobjekt->Cell(43,3,"oznaceni",'B',0,'L');
	$pdfobjekt->Cell(37,3,"AM / MM",'B',0,'L');
	$pdfobjekt->Cell(15,3,"ks/hod",'B',0,'R');
	$pdfobjekt->Cell(0,3,"min/stk",'B',1,'R');
	$pdfobjekt->SetX($x_pocatek);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	
	// vypisu komentare
	if(is_array($komentare))
	{
		foreach($komentare as $komentar)
		{
			$abgnr = intval($komentar["taetnr"]);
			$pdfobjekt->Cell(10,3,$komentar["taetnr"],0,0,'L');
			$pdfobjekt->Cell(42,3,AplDB::aplSubstr($komentar["tatbez_d"],$cinnostMaxLen),0,0,'L');
			$pdfobjekt->Cell(43,3,AplDB::aplSubstr($komentar["tatbez_t"],$cinnostMaxLen),0,0,'L');
			$mittel = $a->getAMMMList($teil, $abgnr);
//			$pdfobjekt->Cell(37,3,$komentar["mittel"],0,0,'L');
			$pdfobjekt->Cell(37,3,$mittel,0,0,'L');
			$obsah=number_format($komentar["ks_hod"],0,',',' ');
			$pdfobjekt->Cell(15,3,$obsah,0,0,'R');
			$obsah=number_format($komentar["vzaby"],2,',',' ');
			$pdfobjekt->Cell(0,3,$obsah,0,1,'R');
			$pdfobjekt->SetX($x_pocatek);
		}
	}
	
	foreach($taetigkeiten as $taetigkeit)
	{
		$taetigkeitChildNodes = $taetigkeit->childNodes;
		$abgnr = intval(getValueForNode($taetigkeitChildNodes,"taetnr"));
		$pdfobjekt->Cell(10,3,getValueForNode($taetigkeitChildNodes,"taetnr"),0,0,'L');
		$pdfobjekt->Cell(42,3,AplDB::aplSubstr(getValueForNode($taetigkeitChildNodes,"tatbez_d"),$cinnostMaxLen),0,0,'L');
		$pdfobjekt->Cell(43,3,AplDB::aplSubstr(getValueForNode($taetigkeitChildNodes,"tatbez_t"),$cinnostMaxLen),0,0,'L');
		$mittel = $a->getAMMMList($teil, $abgnr);
//		$pdfobjekt->Cell(37,3,getValueForNode($taetigkeitChildNodes,"mittel"),0,0,'L');
		$pdfobjekt->Cell(37,3,$mittel,0,0,'L');
		$obsah=number_format(getValueForNode($taetigkeitChildNodes,"ks_hod"),0,',',' ');
		$pdfobjekt->Cell(15,3,$obsah,0,0,'R');
		$obsah=number_format(getValueForNode($taetigkeitChildNodes,"vzaby"),2,',',' ');
		$pdfobjekt->Cell(0,3,$obsah,0,1,'R');
		$pdfobjekt->SetX($x_pocatek);
	}
}



/**
 *
 * @param TCPDF $pdf
 * @param type $xOffset
 * @param type $yOffset
 * @param type $height 
 */
function show_DokuLegend($pdf,$teil,$xOffset,$yOffset,$height){
    // prepare legend array
    $a = AplDB::getInstance();
    $pdf->SetFont("FreeSans", "", 6);
    
    // *************************************************************************
    // second param = TRUE, use druck_arbpapier flag in dokumenttyp ************
    $dokLegendArray = $a->getTeilDokuDistinctDokuArray($teil,TRUE);
    //$aussLegendArray = array(array("aussnr"=>"10"),array("aussnr"=>"20"));
    if($dokLegendArray!==NULL){
	$dokCount = count($dokLegendArray);
	$rowHeight = $height/10;
	$y=$yOffset;
	$pocet=0;
        $vpa79=0;
	foreach ($dokLegendArray as $dL){
            $dokunr = $dL['doku_nr'];
            $dokuTypArray = $a->getDokuTypArray($dokunr);
            $dokuTyp = $dokuTypArray[0]['doku_beschreibung'];
            if ($dokunr != 12)
            {
                if ($dokunr == 50)
                {
                    $pdf->SetXY($xOffset, $yOffset+$rowHeight*11-2);
                    $vpa79 = 1;
                }
                else
                {
                    $pdf->SetY($y);
                    $pdf->SetX($xOffset);
                }
                $pdf->Write($rowHeight, $dokunr."-".$dokuTyp." / ");
                $pdf->Write($rowHeight, $dL['freigabe_am']." / ");
                $pdf->Write($rowHeight, $dL['freigabe_vom']." / ");
                $pdf->Write($rowHeight, $dL['musterplatz']);
                $y+=$rowHeight;
                // pojistka na max. 5 dokumentu
                $pocet++;
                if($pocet>10) break;
            }
	}
        if ($vpa79 == 0)
        {
            $pdf->SetXY($xOffset, $yOffset+$rowHeight*11-2);
            $pdf->Write($rowHeight, "50 - ?");
        }
    }    
}
/**
 *
 * @param TCPDF $pdf
 * @param type $x_pocatek
 * @param type $yTemp
 * @param type $height 
 */
function show_AussLegend($pdf,$xOffset,$yOffset,$height){
    // prepare legend array
    $a = AplDB::getInstance();
    $pdf->SetFont("FreeSans", "", 6);
    $aussLegendArray = $a->getAussArtArray(TRUE);
    //$aussLegendArray = array(array("aussnr"=>"10"),array("aussnr"=>"20"));
    if($aussLegendArray!==NULL){
	$aussCount = count($aussLegendArray);
	$rowHeight = $height/$aussCount;
	$y=$yOffset;
	foreach ($aussLegendArray as $aL){
	    $pdf->SetX($xOffset);
	    $pdf->SetY($y);
	    $pdf->Write($rowHeight, $aL['aussnr']." - ");
	    $pdf->Write($rowHeight, $aL['aussbeschreibung']);
	    $y+=$rowHeight;
	}
    }
}
/**
 *
 * @param type $pdf
 * @param type $teil
 * @param type $offset 
 */
function zobraz_schwierigkeit($pdf,$teil,$yOffset=0){
        // vytahnu seznam reklamaci pro zadany dil
    $a = AplDB::getInstance();
    $sArray = $a->getTeilSchwierigkeiten($teil);
    if ($sArray === NULL)
	return 0;
    
    $wholeWidth = 150+7;
    $schWidth = $wholeWidth/3;
    $schLabel = $schWidth*(1/4);
    $schValue = $schWidth*(3/4);
    
    $x_pocatek = 125;
    $y_pocatek = 23+$yOffset;
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->SetXY($x_pocatek, $y_pocatek);
    $pdf->Cell($schLabel, 3, "S11:", 'LBT', 0, 'L');
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->Cell($schValue, 3, $sArray['S11'], 'RBT', 0, 'L');
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->Cell($schLabel, 3, "S51:", 'LBT', 0, 'L');
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->Cell($schValue, 3, $sArray['S51'], 'RBT', 0, 'L');
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->Cell($schLabel, 3, "SO:", 'LBT', 0, 'L');
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->Cell($schValue, 3, $sArray['SO'], 'RBT', 0, 'L');
    return 1;
}

/**
 *
 * @param TCPDF $pdfobjekt
 * @param type $teil 
 * @return pocet zobrazenych reklamaci
 */
function zobraz_reklamace($pdfobjekt, $teil) {

    // vytahnu seznam reklamaci pro zadany dil
    $a = AplDB::getInstance();
    $reklArray = $a->getLetzteReklamation($teil, 1000);
    if ($reklArray === NULL)
	return 0;
    $restAnzahl = 0;
    if(count($reklArray)>5) $restAnzahl = count ($reklArray)-5;
    
    $x_pocatek = 125;
    $y_pocatek = 23;
    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->SetXY($x_pocatek, $y_pocatek);
    $pdfobjekt->Cell(12, 3, "Rekl-Nr", 'B', 0, 'L');
    $pdfobjekt->Cell(15, 3, "Erhalten am", 'B', 0, 'L');
    $pdfobjekt->Cell(95, 3, "Abweichung", 'B', 0, 'L');
    $pdfobjekt->Cell(17, 3, "Giesstag", 'B', 0, 'L');
    $pdfobjekt->Cell(0, 3, "Bewert.", 'B', 1, 'R');
    $pdfobjekt->SetX($x_pocatek);

    $pdfobjekt->SetFont("FreeSans", "", 7);

    $citac=0;
    foreach ($reklArray as $rekl) {
	$pdfobjekt->Cell(12, 3, $rekl["rekl_nr"], 0, 0, 'L');
	$reklDatum = substr($rekl['rekl_datum'],6)."-".substr($rekl['rekl_datum'],3,2)."-".substr($rekl['rekl_datum'],0,2);
	$pdfobjekt->Cell(15, 3, $reklDatum, 0, 0, 'L');
	$abweichung = str_replace('\n', ' ', $rekl["beschr_abweichung"]);
	$pdfobjekt->Cell(95, 3, $abweichung, 0, 0, 'L');
	$pdfobjekt->Cell(17, 3, $rekl["giesstag"], 0, 0, 'L');
	$pdfobjekt->Cell(0, 3, $rekl["interne_bewertung"], 0, 1, 'R');
	$pdfobjekt->SetX($x_pocatek);
	$citac++;
	if($citac>4) break;
    }
    
    if($restAnzahl>0)
	$pdfobjekt->Cell(12+15+95+17+10, 3, "+ weitere $restAnzahl Reklamationen", 0, 0, 'L');
    // cerveny ramecek okolo
    $pdfobjekt->SetDrawColor(255,0,0);
    $pdfobjekt->SetLineWidth(0.5);
    $pdfobjekt->Rect($x_pocatek, $y_pocatek-0.5, 150+7, $pdfobjekt->GetY()-$y_pocatek+0.5, 'D');
    $pdfobjekt->SetDrawColor(0,0,0);
    $pdfobjekt->SetLineWidth(0.2);
    $rest = $restAnzahl>0?1:0;
    $celkPocet = count($reklArray);
    if($celkPocet>5) $celkPocet=5;
    return $celkPocet+$rest;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);
/**
 *
 * @param TCPDF $pdfobjekt
 * @param type $paletteChildNodes
 * @param type $importChildNodes 
 */
function zobraz_paletu($pdfobjekt,$paletteChildNodes,$importChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;
	$pdfobjekt->SetFont("FreeSans", "", 9);

	// hlavni tabulka ma 3 radky

	$x_pocatek=$pdfobjekt->GetX();
	$y_pocatek=$pdfobjekt->GetY();

	// pole pro zakaznika
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->Rect($x_pocatek,$y_pocatek-10,52,30);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10);
	$pdfobjekt->Write(5,"Kunde / zakaznik :");$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"kunde"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,substr(getValueForNode($importChildNodes,"name1"),0,25));$pdfobjekt->Ln();
	$y1 = $pdfobjekt->GetY();
	$pdfobjekt->Write(8,substr(getValueForNode($importChildNodes,"name2"),0,25));$pdfobjekt->Ln(1);
	$pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->Write(8,substr(getValueForNode($paletteChildNodes,"restmengen_verw"),0,25));$pdfobjekt->Ln();
	

	// pole pro auftrag
	$pdfobjekt->Rect($x_pocatek+52+3,$y_pocatek-10,52,30);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->SetXY($x_pocatek+52+3,$y_pocatek-10);
	$pdfobjekt->Write(5,"Auftrag / dodavka / pal:");$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetX($x_pocatek+52+5);
	$pdfobjekt->SetFont("FreeSans", "B", 20);
	$pdfobjekt->Write(25,getValueForNode($importChildNodes,"auftragsnr")."/");
	$x1 = $pdfobjekt->GetX();
	$pdfobjekt->SetFont("FreeSans", "B", 18);
	$pdfobjekt->Write(25,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetXY($x1,$y1);
	$pdfobjekt->Write(8,getValueForNode($paletteChildNodes,"fremdpos"));$pdfobjekt->Ln();

	// pole pro cislo dilu
	$pdfobjekt->Rect($x_pocatek,$y_pocatek-10+30+3,52,24);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10+30+3);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->Write(5,"");$pdfobjekt->Ln(3);
	$pdfobjekt->SetX($x_pocatek);
	$pdfobjekt->SetFont("FreeSans", "B", 25);
	$teil = getValueForNode($paletteChildNodes,"teil");
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"teil"));$pdfobjekt->Ln(10);
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$teilbez = getValueForNode($paletteChildNodes,"teilbez");
	$suffix = '';
	if(strlen($teilbez)>25) $suffix = '...';
	$teilbez = substr($teilbez, 0, 25).$suffix;
	$pdfobjekt->Write(10,$teilbez);$pdfobjekt->Ln(5);

	// pole pro pocet kusu
	$pdfobjekt->Rect($x_pocatek+52+3,$y_pocatek-10+30+3,52,24);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->SetXY($x_pocatek+52+3,$y_pocatek-10+30+3);
	$yTemp = $pdfobjekt->GetY();
	$pdfobjekt->Write(5,"Stück / pocet kusu:");$pdfobjekt->Ln(7);
	$pdfobjekt->SetX($x_pocatek+52+5+15);
	$pdfobjekt->SetFont("FreeSans", "B", 18);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"stk"));
	//brgew
	$pdfobjekt->SetX($x_pocatek+52+5+30);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"brgew")." kg");
	$pdfobjekt->Ln(10);
	
	$pdfobjekt->SetX($x_pocatek+52+5);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Write(5,"Druh litiny:".getValueForNode($paletteChildNodes,"artguseisen"));
	$pdfobjekt->SetX($x_pocatek+52+5+30);
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Write(5,getValueForNode($paletteChildNodes,"gew")." kg");$pdfobjekt->Ln(5);
	$pdfobjekt->SetFont("FreeSans", "", 10);

	// pole pro muster
	$a = AplDB::getInstance();
	$teilnr = getValueForNode($paletteChildNodes, 'teil');
//	$musterRow = $a->getTeilDokument($teilnr, 29, TRUE);
	$musterRow = $a->getTeilDokument($teilnr, AplDB::DOKUNR_MUSTER, TRUE);
	if($musterRow===NULL)
	    $musterText = "Muster: ????";
	else
	    $musterText = $musterRow['doku_nr']."/".$musterRow['doku_beschreibung']."/".$musterRow['einlag_datum']."/".$musterRow['musterplatz']."/".$musterRow['freigabe_am']."/".$musterRow['freigabe_vom'];

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Rect($x_pocatek+52+3+52+3,$y_pocatek-10,150+7,5);
	$pdfobjekt->SetXY($x_pocatek+52+3+52+3,$y_pocatek-10);
	$pdfobjekt->Write(5,$musterText);
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Write(5,"      Teil (original): ".getValueForNode($paletteChildNodes,"teillang"));
	
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10+30+3+24+3);	

	// tabulka pro zapis vykonu
	// hlavicka
	
	$pdfobjekt->MyMultiCell(25,8,"Datum",1,'L',0);
//	$pdfobjekt->MyMultiCell(15,4,"Schicht\nsmena",1,'L',0);
	$pdfobjekt->MyMultiCell(20,4,"PersNr\nosobni cislo",1,'L',0);
	$pdfobjekt->MyMultiCell(65,4,"AFO - Nr.\ncislo operace",1,'L',0);
	//$pdfobjekt->SetFont("FreeSans", "B", 15);
	$pdfobjekt->MyMultiCell(28,4,"1.Muster (PersNr)\n    1.vzor (o.č.)",1,'L',0);
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->MyMultiCell(40,4,"           Stück / kus\n    gut           A-Stk/Art",1,'L',0);
	$pdfobjekt->MyMultiCell(40,4,"Arbeitszeit\nvon/od         bis/do",1,'C',0);
	//$pdfobjekt->SetFont("FreeSans", "B", 15);
	$pdfobjekt->MyMultiCell(25,4,"Q (PersNr)\nQ (o.č.)",1,'L',0);
	$pdfobjekt->MultiCell(0,4,"SYS (PersNr)\nSYS (o.č.)",1,'L',0);
	// radky tabulky
	$table = array(
	    array(25,'1',9),
	    array(20,'1',9),
	    array(13,'B',9),
	    array(13,'B',9),
	    array(13,'B',9),
	    array(13,'B',9),
	    array(13,'B',9),
	    array(28,'1',9),
	    array(15,'1',9),
	    array(25,'1',9),
	    array(20,'1',9),
	    array(20,'1',9),
	    array(25,'1',9),
	    array(0,'1',9),
	);
	
	$yProCarky = 0;
	for ($i = 0; $i < 8; $i++) {
	    if($i==0) $yProCarky=$pdfobjekt->GetY();
	    foreach ($table as $poradi => $cell) {
		$pdfobjekt->MyMultiCell($cell[0],$cell[2],"",$cell[1],'C',0);
	    }
	    $pdfobjekt->Ln();
	}

	// oddelovaci carky pro operace / aussarten
	for($i=0;$i<8;$i++){
	    for($j=0;$j<4;$j++){
		$pdfobjekt->Line(
			$x_pocatek+25+20+$table[2][0]*($j+1), 
			$yProCarky+8+$i*9, 
			$x_pocatek+25+20+$table[2][0]*($j+1), 
			$yProCarky+9+$i*9
			);
	    }
	    // aussarten
	    $pdfobjekt->Line(
		    $x_pocatek
		    +$table[0][0]
		    +$table[1][0]
		    +$table[2][0]*5
		    +$table[7][0]
		    +$table[8][0]
		    +$table[9][0]/2, 
		    $yProCarky+7+$i*9, 
		    $x_pocatek
		    +$table[0][0]
		    +$table[1][0]
		    +$table[2][0]*5
		    +$table[7][0]
		    +$table[8][0]
		    +$table[9][0]/2, 
		    $yProCarky+9+$i*9);
	}

	// spodni ramecek s poznamkou
	$xHranice = $pdfobjekt->getPageWidth()-PDF_MARGIN_RIGHT;
	
	// aussLegend
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$yTemp = $pdfobjekt->GetY();
	$pdfobjekt->Rect($x_pocatek,$yTemp,45,15+21);
	show_AussLegend($pdfobjekt,$x_pocatek,$yTemp+1,15-2);
	//$pdfobjekt->SetY($yTemp);

	// dokumente
	$pdfobjekt->Rect($x_pocatek+45, $yTemp, 5*13+28+15+25,15+21 );
	show_DokuLegend($pdfobjekt,$teilnr,$x_pocatek+45+0,$yTemp+1,15+21-2);
	
	
	// vpa + verpackung
	$pdfobjekt->Rect($x_pocatek+45, $yTemp, $xHranice-($x_pocatek+45),15+21 );
        $verpackungen;
        foreach($paletteChildNodes as $paletteChildNode)
            if ($paletteChildNode->nodeName == "taetigkeiten")
                $verpackungen = $paletteChildNode->firstChild->lastChild->childNodes;
        
        $x = 193;
        $y = $yTemp + 1;
        foreach($verpackungen as $verpackung)
        {
            $verp_id = $verpackung->firstChild->nodeValue;
            $verp_name = $verpackung->lastChild->nodeValue;
            $verp_stk = $verpackung->childNodes->item(1)->nodeValue;
            
            $pdfobjekt->SetXY($x, $y);
            $pdfobjekt->Cell(45, 2.5, $verp_stk . "x  " . $verp_id . " - " . $verp_name, "", 0 , "L");
            //echo $verpackung->firstChild->nodeValue . "<br>";
            
            $y += 2.5;
        }
        
        $pdfobjekt->SetXY(150, $yTemp + 1);
        $verpackungsmenge = getValueForNode($paletteChildNodes, "verpackungmenge");
        $pdfobjekt->Cell(45, 2.5, "1x VPE = " . $verpackungsmenge . " stk/ks", "", 0 , "L");
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	}
}
				

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
	}
}

/**
 *
 * @param PDF_Transform $pdfobjekt
 * @param <type> $paletteChildNodes
 * @param <type> $importChildNodes
 */
function zobraz_paletu_back($pdfobjekt,$paletteChildNodes,$importChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;

	// hlavni tabulka ma 3 radky

	$x_pocatek=$pdfobjekt->GetX();
	$y_pocatek=$pdfobjekt->GetY();
	$y_offset = 10;
	$auftragsnr_vyska = 85;
	$paletaPoziceX = 150;
	$datumCas = date('d.m.Y H:i:s');
	$ident = get_user();

	$datumCas = $datumCas. " ( $ident )";

        $pdfobjekt->StartTransform();
        $pdfobjekt->TranslateX(PDF_MARGIN_LEFT);
        $pdfobjekt->TranslateY($pdfobjekt->getPageHeight()-PDF_MARGIN_BOTTOM-25);
        $pdfobjekt->Rotate(90);
        $pdfobjekt->ScaleX(90);
        
	$pdfobjekt->SetFont("FreeSans", "", 20);
	$pdfobjekt->Text(10,15+$y_offset,"Auftrag / zakazka");
	$pdfobjekt->Text(100,15+$y_offset,"Stck / kusu :");
	$pdfobjekt->SetFont("FreeSans", "B", 20);
	$pdfobjekt->Text(150,15+$y_offset,getValueForNode($paletteChildNodes,"stk"));

	$pdfobjekt->SetFont("FreeSans", "B", $auftragsnr_vyska);
	$pdfobjekt->Text(10,50+$y_offset,getValueForNode($paletteChildNodes,"auftragsnr")."/");

	$pdfobjekt->SetFont("FreeSans", "", 70);
	$pdfobjekt->Text($paletaPoziceX,50+$y_offset,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("FreeSans", "", 15);
	$pdfobjekt->Text($paletaPoziceX+5,28+$y_offset,"Palette / paleta");
	$pdfobjekt->SetFont("FreeSans", "B", 35);
	$pdfobjekt->SetX($paletaPoziceX+5);
	$pdfobjekt->SetY(28+$y_offset+25);
	$pdfobjekt->Cell(189, 20, getValueForNode($paletteChildNodes,"fremdpos"), "0", 0 , "R");
	
	
	$pdfobjekt->SetFont("FreeSans", "", 20);
	//$pdfobjekt->SetXY(10,10);
	$pdfobjekt->Text(10,70+$y_offset,"Teil / dil");
	$pdfobjekt->SetFont("FreeSans", "B", 90);
	$pdfobjekt->Text(10,100+$y_offset,getValueForNode($paletteChildNodes,"teil"));


	$pdfobjekt->SetFont("FreeSans", "", 10);
	//$datumCas = '';
	$pdfobjekt->Text(10,130+$y_offset,"Abydos s.r.o."."   $datumCas");

	$pdfobjekt->SetFont("FreeSans", "", 20);
	$pdfobjekt->Text(120,130+$y_offset,"Gew / vaha");
	$pdfobjekt->Text(160,130+$y_offset,getValueForNode($paletteChildNodes,"gew")." Kg");

	$pdfobjekt->StartTransform();
	//point reflection at the lower left point of rectangle
	$pdfobjekt->MirrorP(105,150);

	$pdfobjekt->SetFont("FreeSans", "", 20);
	$pdfobjekt->Text(10,15+$y_offset,"Auftrag / zakazka");
	$pdfobjekt->Text(100,15+$y_offset,"Stck / kusu :");
	$pdfobjekt->SetFont("FreeSans", "B", 20);
	$pdfobjekt->Text(150,15+$y_offset,getValueForNode($paletteChildNodes,"stk"));

	$pdfobjekt->SetFont("FreeSans", "B", $auftragsnr_vyska);
	$pdfobjekt->Text(10,50+$y_offset,getValueForNode($paletteChildNodes,"auftragsnr")."/");

	$pdfobjekt->SetFont("FreeSans", "", 70);
	$pdfobjekt->Text($paletaPoziceX,50+$y_offset,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("FreeSans", "", 15);
	$pdfobjekt->Text($paletaPoziceX+5,28+$y_offset,"Palette / paleta");
	$pdfobjekt->SetFont("FreeSans", "B", 35);
	$pdfobjekt->SetX($paletaPoziceX+5);
	$pdfobjekt->SetY(28+$y_offset+25);
	$pdfobjekt->Cell(189, 20, getValueForNode($paletteChildNodes,"fremdpos"), "0", 0 , "R");

	$pdfobjekt->SetFont("FreeSans", "", 20);
	//$pdfobjekt->SetXY(10,10);
	$pdfobjekt->Text(10,70+$y_offset,"Teil / dil");
	$pdfobjekt->SetFont("FreeSans", "B", 90);
	$pdfobjekt->Text(10,100+$y_offset,getValueForNode($paletteChildNodes,"teil"));

	$pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->Text(10,130+$y_offset,"Abydos s.r.o."."   $datumCas");

	$pdfobjekt->SetFont("FreeSans", "", 20);
	$pdfobjekt->Text(120,130+$y_offset,"Gew / vaha");
	$pdfobjekt->Text(160,130+$y_offset,getValueForNode($paletteChildNodes,"gew")." Kg");

	//	Stop Transformation
	$pdfobjekt->StopTransform();

        $pdfobjekt->StopTransform();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


//require_once('../tcpdf/config/lang/eng.php');
//require_once('../tcpdf/tcpdf.php');
require('../tcpdf/transform.php');


$pdf = new PDF_Transform('L','mm','A4',1);
//$pdf = new PDF_Transform('L','mm','A4');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D230 Arbeitspapier - PRACOVNI PRUVODNI KARTA", $params);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D230 Arbeitspapier - PRACOVNI PRUVODNI KARTA", "");
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM-15);
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


// zacinam po importech

$importe=$domxml->getElementsByTagName("auftrag");
foreach($importe as $import)
{
	$importChildNodes = $import->childNodes;

	// ted jdu po paletach
	$paletten=$import->getElementsByTagName("palette");
	foreach($paletten as $palette)
	{
                $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D230 Arbeitspapier - PRACOVNI PRUVODNI KARTA", $params);
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

		$pdf->AddPage();
		$paletteChildNodes = $palette->childNodes;
		zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);

		// ted jdu po cinnostech = tabulka s operacema
		$teil = getValueForNode($paletteChildNodes,"teil");
		$taetigkeiten = $palette->getElementsByTagName("taetigkeit");

		// letzte Reklamationen zeigen
		$reklRadku = zobraz_reklamace($pdf,$teil);
		
		// add schwierigkeitsinfo
		if($reklRadku>0) $reklRadku+=1;
		$schwRadku = zobraz_schwierigkeit($pdf,$teil,($reklRadku)*3);  
		
		// block cinnost should be moved to bottom $reklRadku + 1 free row + 1 heading row + 1 schwierigkeitsinfo
		if($reklRadku>0) $reklRadku+=1;
		zobraz_cinnosti($pdf,$taetigkeiten,$teil,($reklRadku+$schwRadku)*3);  
		
                $pdf->SetHeaderData('', 0, "", '');
                $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
                $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
                $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor
                $pdf->AddPage();
                zobraz_paletu_back($pdf,$paletteChildNodes,$importChildNodes);
	}
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
