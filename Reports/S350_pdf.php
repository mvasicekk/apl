<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S350";
$doc_subject = "S350 Report";
$doc_keywords = "S350";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$behnrvon = $_GET['behnrvon'];
$behnrbis = $_GET['behnrbis'];

$apl = AplDB::getInstance();

$invdatum = $apl->validateDatum($_GET['invdatum']);
$invdatumDB = $apl->make_DB_datum($invdatum);

$zustandArray = $apl->getBehaelterStandArray(0,FALSE);
$lagerPlatzArray = $apl->getBehaelterLagerplatzArray(0);


require_once('S350_xml.php');

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
'kundenr2'=> array ("popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'kdname'=> array ("popis"=>"","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'teilnr'=> array ("popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'teilbez'=> array ("popis"=>"","sirka"=>45,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'schwgrad_num'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'anfang'=> array ("popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"C","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
'kundenr2'=> array ("popis"=>"KundeNr","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'kdname'=> array ("popis"=>"Kunde","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'teilnr'=> array ("popis"=>"TeilNr","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'teilbez'=> array ("popis"=>"Teilbezeichnung","sirka"=>45,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'schwgrad_num'=> array ("popis"=>"Schw.Grad","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'anfang'=> array ("popis"=>"Anfaeng. geeignet","sirka"=>0,"ram"=>'1',"align"=>"C","radek"=>1,"fill"=>1),
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

function getStkForZustandPlatz($kunde,$zustand_id,$platz_id){
    $stk = 0;
    $zustande = $kunde->getElementsByTagName('zustand');
    foreach($zustande as $zustand){
        $zustandChilds = $zustand->childNodes;
        $platze = $zustand->getElementsByTagName('platz');
        foreach($platze as $platz){
            $platzChilds = $platz->childNodes;
            $zustandId = getValueForNode($zustandChilds, 'zustand_id');
            $platzId = getValueForNode($platzChilds, 'platz_id');
            if(($zustandId==$zustand_id) && ($platzId==$platz_id)){
                $stk = intval(getValueForNode($platzChilds, 'stk'));
            }
        }
    }
    return $stk;
}

function zahlavi_behaelter($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah = getValueForNode($childs, 'behaelternr');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'L',$fill);

        $obsah = getValueForNode($childs, 'art-name1');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_stranka($pdfobjekt,$vyskaradku,$rgb,$lagerplatzArray){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 5.5);
	// dummy
//	$obsah = '';
//	$pdfobjekt->Cell(20,$vyskaradku,'','0',0,'L',0);
	// dummy
	$obsah = 'Zustand';
	$pdfobjekt->Cell(20+20,$vyskaradku,$obsah.'.','1',0,'L',$fill);
        foreach($lagerplatzArray as $lp){
            $platz_id = $lp['platz_id'];
            $obsah = $platz_id;
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'1',0,'R',$fill);
        }

        //suma pro zustand
//        $obsah = number_format($summeZustand,0,',',' ');
        $pdfobjekt->Cell(20,$vyskaradku,'Abydos inv.','1',0,'R',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,'KD-Konto inv.','1',0,'R',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,'Bew. Plus','1',0,'R',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,'Bew. Minus','1',0,'R',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,'KD-Konto aktuell','1',0,'R',$fill);

        //odradkovani
        $pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        return $summeZustand;

}

function zustand_sestava($pdfobjekt,$vyskaradku,$rgb,$kunde,$lagerplatzArray,$zustand,$gesamtSummen)
{
        if($zustand['zustand_id']>999) return;
        $summeZustand = 0;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 6);

        // nejdriv si zjistim sumu a pokud nebude nulova nakreslim radek
        $zustand_id = $zustand['zustand_id'];
        $testSuma = 0;
        foreach($lagerplatzArray as $lp){
            $platz_id = $lp['platz_id'];
            $obsah = $gesamtSummen[$zustand_id][$platz_id];
            $testSuma += $obsah;
        }

        if(intval($testSuma)==0) return;

	// dummy
//	$obsah = '';
//	$pdfobjekt->Cell(20,$vyskaradku,'','0',0,'L',$fill);
	// dummy
	$obsah = $zustand['zustand_id'];
        $zustand_id = $zustand['zustand_id'];
	$pdfobjekt->Cell(20+20,$vyskaradku,$obsah.'.'.$zustand['zustand_text'],'1',0,'L',$fill);
        foreach($lagerplatzArray as $lp){
            $platz_id = $lp['platz_id'];
            $obsah = $gesamtSummen[$zustand_id][$platz_id];
            $summeZustand += $obsah;
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'1',0,'R',$fill);
        }

        //suma pro zustand
        $obsah = number_format($summeZustand,0,',',' ');
        $pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',1,'R',$fill);

        //odradkovani
        //$pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        return $summeZustand;
}

function zustand_radek($pdfobjekt,$vyskaradku,$rgb,$kunde,$lagerplatzArray,$zustand)
{
        if($zustand['zustand_id']>999) return;
        $summeZustand = 0;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "", 6);

        // nejdriv si zjistim sumu a pokud nebude nulova nakreslim radek
        $zustand_id = $zustand['zustand_id'];
        $testSuma = 0;
        foreach($lagerplatzArray as $lp){
            $platz_id = $lp['platz_id'];
            $obsah = getStkForZustandPlatz($kunde, $zustand_id, $platz_id);
            $testSuma += $obsah;
        }

        if(intval($testSuma)==0) return;
	// dummy
//	$obsah = '';
//	$pdfobjekt->Cell(20,$vyskaradku,'','0',0,'L',$fill);
	// dummy
	$obsah = $zustand['zustand_id'];
        $zustand_id = $zustand['zustand_id'];
	$pdfobjekt->Cell(20+20,$vyskaradku,$obsah.'.','1',0,'L',$fill);
        foreach($lagerplatzArray as $lp){
            $platz_id = $lp['platz_id'];
            $obsah = getStkForZustandPlatz($kunde, $zustand_id, $platz_id);
            $summeZustand += $obsah;
            $pdfobjekt->Cell(10,$vyskaradku,$obsah,'1',0,'R',$fill);
        }

        //suma pro zustand
        $obsah = number_format($summeZustand,0,',',' ');
        $pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',1,'R',$fill);
        
        //odradkovani
        //$pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        return $summeZustand;
}

//zapati_gesamtSummen($pdf, 5, array(255,255,240),$behaelters,$lagerPlatzArray,$summeGesamt);
function zapati_gesamtSummen($pdfobjekt,$vyskaradku,$rgb,$behaelters,$lagerPlatzArray,$summeGesamt,$bewegungSummen)
{
        $childs = $kunde->childNodes;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah = 'Summe';
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'LTB',0,'L',$fill);

        $obsah = '';
	$pdfobjekt->Cell(20+count($lagerPlatzArray)*10,$vyskaradku,$obsah,'RTB',0,'L',$fill);

        $obsah = number_format($summeGesamt,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        foreach($behaelters as $behaelter){
            $kunden = $behaelter->getElementsByTagName('kunde');
            foreach($kunden as $kunde){
                $gesamtSummenKDKonto += getStkForZustandPlatz($kunde, 9999, 'KDKONTO');
            }
        }

        $obsah = number_format($gesamtSummenKDKonto,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $obsah = number_format($bewegungSummen['plus'],0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $obsah = number_format($bewegungSummen['minus'],0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $obsah = number_format($gesamtSummenKDKonto+$bewegungSummen['plus']-$bewegungSummen['minus'],0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $pdfobjekt->Ln();

        $pdfobjekt->SetFont("FreeSans", "B", 10);
        $pdfobjekt->Cell(20,$vyskaradku,'Delta Aby','LTB',0,'L',$fill);
        $obsah = '';
	$pdfobjekt->Cell(20+count($lagerPlatzArray)*10,$vyskaradku,$obsah,'RTB',0,'L',$fill);

        $obsah = number_format($summeGesamt-$gesamtSummenKDKonto,0,',',' ');
	$pdfobjekt->Cell(40,$vyskaradku,$obsah,'1',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}
function zapati_kunde($pdfobjekt,$vyskaradku,$rgb,$kunde,$lagerPlatzArray,$summeKunde,$invdatumDB,$behaelternr)
{
        global $bewegungSummen;
        $childs = $kunde->childNodes;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
        $kundenr = getValueForNode($childs, 'kundenr');
	$obsah = $kundenr;
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'L',$fill);

        $obsah = getValueForNode($childs, 'Name1');
	$pdfobjekt->Cell(20+count($lagerPlatzArray)*10,$vyskaradku,$obsah,'1',0,'L',$fill);

        $obsah = number_format($summeKunde,0);
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $kdKontoStand = getStkForZustandPlatz($kunde, 9999, 'KDKONTO');
        $obsah = number_format($kdKontoStand,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $a = AplDB::getInstance();
        $bewegungPlus = $a->getBehaelterBewegungungPlus($behaelternr,$kundenr,$invdatumDB);
        $bewegungMinus = $a->getBehaelterBewegungungMinus($behaelternr,$kundenr,$invdatumDB);

        $bewegungSummen['plus'] += $bewegungPlus;
        $bewegungSummen['minus'] += $bewegungMinus;
        
        $obsah = number_format($bewegungPlus,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $obsah = number_format($bewegungMinus,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $kdKontoAktuell = $kdKontoStand + $bewegungPlus - $bewegungMinus;
        $obsah = number_format($kdKontoAktuell,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_kunde($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "", 8);
	// dummy
	$obsah = getValueForNode($childs, 'kundenr');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'L',$fill);

        $obsah = getValueForNode($childs, 'Name1');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_gesamtSummen($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);

        $obsah = ' Gesamtsummen';
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S350 Verpackungsinventur", $params);
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

$bewegungSummen = array('plus'=>0,'minus'=>0);

// prvni stranka
$pdf->AddPage();
//pageheader($pdf,$cells_header,5);
$gesamtSummen = array();
    foreach ($zustandArray as $zustand){
        foreach($lagerPlatzArray as $lp){
            $gesamtSummen[$zustand['zustand_id']][$lp['platz_id']] = 0;
        }
    }

$behaelters = $domxml->getElementsByTagName("behaelter");
foreach($behaelters as $behaelter){

    $behaelterChilds = $behaelter->childNodes;
    zahlavi_behaelter($pdf, 4, array(255,255,255),$behaelterChilds);
    $behaelternr = getValueForNode($behaelterChilds, 'behaelternr');
    zahlavi_stranka($pdf, 4, array(255,255,200), $lagerPlatzArray);
    $kunden = $behaelter->getElementsByTagName('kunde');
    foreach($kunden as $kunde){
        $summeKunde = 0;
        $kundeChilds = $kunde->childNodes;
//        zahlavi_kunde($pdf, 5, array(255,255,255),$kundeChilds);
        foreach ($zustandArray as $zustand){
            $summe = zustand_radek($pdf, 3, array(255,255,255),$kunde,$lagerPlatzArray,$zustand);
            $summeKunde += $summe;
            $zustand_id = $zustand['zustand_id'];
            foreach($lagerPlatzArray as $lp){
                $platz_id = $lp['platz_id'];
                $gesamtSummen[$zustand_id][$platz_id] += getStkForZustandPlatz($kunde, $zustand_id, $platz_id);
            }
        }
        zapati_kunde($pdf, 4, array(255,255,240),$kunde,$lagerPlatzArray,$summeKunde,$invdatumDB,$behaelternr);
	$pdf->Ln(2);
    }
}
//pd
$pdf->Ln();
zahlavi_gesamtSummen($pdf, 5, array(240,255,240), NULL);
// a nakonec soucty celkem
foreach ($zustandArray as $zustand){
    $summe = zustand_sestava($pdf, 3, array(255,255,255),$kunde,$lagerPlatzArray,$zustand,$gesamtSummen);
    $summeGesamt += $summe;
}
zapati_gesamtSummen($pdf, 4, array(240,255,240),$behaelters,$lagerPlatzArray,$summeGesamt,$bewegungSummen);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
