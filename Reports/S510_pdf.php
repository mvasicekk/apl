<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S510";
$doc_subject = "S510 Report";
$doc_keywords = "S510";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$apl = AplDB::getInstance();

$von = $apl->make_DB_datum($_GET['von']);
$bis = $apl->make_DB_datum($_GET['bis']);
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

require_once('S510_xml.php');

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
'startdummy'=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'invnummer'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'anlage_beschreibung'=> array ("popis"=>"","sirka"=>35,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'rep_kosten'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'et_kosten'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'zuschlag'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'gesamt'=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"BR","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
'startdummy'=> array ("popis"=>"\n","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'invnummer'=> array ("popis"=>"\ninvnummer","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'anlage_beschreibung'=> array ("popis"=>"\nTyp","sirka"=>35,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'rep_kosten'=> array ("popis"=>"Reparaturkosten\n[Kc]","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'et_kosten'=> array ("popis"=>"ET-Kosten\n[Kc]","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'zuschlag'=> array ("popis"=>"Zuschlag\n10%[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'gesamt'=> array ("popis"=>"Gesamt\n[Kc]","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'lf'=> array ("popis"=>"\n","sirka"=>0,"ram"=>'0',"align"=>"BR","radek"=>1,"fill"=>1),
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

$sum_zapati_persnr = array(
    'rep_kosten'=>0,
    'et_kosten'=>0,
    'zuschlag'=>0,
    'gesamt'=>0,
    );

$sum_zapati_sestava = array(
    'rep_kosten'=>0,
    'et_kosten'=>0,
    'zuschlag'=>0,
    'gesamt'=>0,
    );

// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
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


function zahlavi_person($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $fullName = getValueForNode($childs, 'name').' '.getValueForNode($childs, 'vorname');
        $persnr = getValueForNode($childs, 'persnr_ma');

	$pdfobjekt->Cell(0,$vyskaradku,$persnr.' '.$fullName,'T',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_person($pdfobjekt,$vyskaradku,$rgb,$sumArray,$persnr=0)
{
    global $cells;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        //popis sumy
        $pdfobjekt->Cell($cells['startdummy']['sirka']+$cells['invnummer']['sirka']+$cells['anlage_beschreibung']['sirka'],$vyskaradku,'Summe Person '.$persnr,'TB',0,'L',$fill);

        // rep_kosten
        $obsah=number_format($sumArray['rep_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // et_kosten
        $obsah=number_format($sumArray['et_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['et_kosten']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // zuschlag
        $obsah=number_format($sumArray['zuschlag'],0,',',' ');
	$pdfobjekt->Cell($cells['zuschlag']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // gesamt
        $obsah=number_format($sumArray['gesamt'],0,',',' ');
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'BT',0,'R',$fill);

        //novy radek
        $pdfobjekt->Cell(0,$vyskaradku,'','TB',1,'R',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$sumArray)
{
    global $cells;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        //popis sumy
        $pdfobjekt->Cell($cells['startdummy']['sirka']+$cells['invnummer']['sirka']+$cells['anlage_beschreibung']['sirka'],$vyskaradku,'Summe Gesamt','1',0,'L',$fill);

        // rep_kosten
        $obsah=number_format($sumArray['rep_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        // et_kosten
        $obsah=number_format($sumArray['et_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['et_kosten']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        // zuschlag
        $obsah=number_format($sumArray['zuschlag'],0,',',' ');
	$pdfobjekt->Cell($cells['zuschlag']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        // gesamt
        $obsah=number_format($sumArray['gesamt'],0,',',' ');
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        //novy radek
        $pdfobjekt->Cell(0,$vyskaradku,'','1',1,'R',$fill);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S510 Reparaturen nach PersNr", $params);
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
pageheader($pdf,$cells_header,5);


$personen=$domxml->getElementsByTagName("person");
foreach($personen as $person)
{
    nuluj_sumy_pole($sum_zapati_persnr);
    $personChilds = $person->childNodes;
    test_pageoverflow($pdf,5,$cells_header);
    zahlavi_person($pdf, 5, array(255,255,250), $personChilds);
    $maschinen = $person->getElementsByTagName("machine");
    foreach ($maschinen as $maschine){
        $maschineChilds = $maschine->childNodes;
        test_pageoverflow($pdf,5,$cells_header);
        telo($pdf,$cells,5,array(255,255,255),"",$maschineChilds);
        foreach ($sum_zapati_persnr as $key=>$value){
            $hodnota = getValueForNode($maschineChilds, $key);
            $sum_zapati_persnr[$key] += $hodnota;
        }
    }
    $sum_zapati_persnr['zuschlag'] = ($sum_zapati_persnr['rep_kosten']+$sum_zapati_persnr['et_kosten'])*0.1;
    $sum_zapati_persnr['gesamt'] = $sum_zapati_persnr['rep_kosten']+$sum_zapati_persnr['et_kosten']+$sum_zapati_persnr['zuschlag'];

    test_pageoverflow($pdf,5,$cells_header);
    zapati_person($pdf, 5, array(255,255,240), $sum_zapati_persnr, getValueForNode($personChilds, 'persnr_ma'));
    $pdf->Ln();
    foreach($sum_zapati_sestava as $key=>$value){
        $hodnota = $sum_zapati_persnr[$key];
        $sum_zapati_sestava[$key] += $hodnota;
    }
}
test_pageoverflow($pdf,5,$cells_header);
zapati_sestava($pdf, 5, array(240,255,240), $sum_zapati_sestava);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
