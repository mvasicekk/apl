<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S194";
$doc_subject = "S194 Report";
$doc_keywords = "S194";

// necham si vygenerovat XML

$a = AplDB::getInstance();
$parameters=$_GET;

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S142",$password,$user,0);
if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}

$reporttyp = $_GET['reporttyp'];
$persvon = intval($_GET['persvon']);
$persbis = intval($_GET['persbis']);
$datumvon = $a->make_DB_datum($_GET['datumvon']);
$datumbis = $a->make_DB_datum($_GET['datumbis']);

require_once('S194_xml.php');

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
'persnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'name'=> array ("popis"=>"","sirka"=>65,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'abgnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'abgnr_name'=> array ("popis"=>"","sirka"=>65,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'verb_zeit'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
'risiko_zuschlag_id'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
'zuschlag_beschreibung'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>80,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
'stunden_zuschlag'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
'abgnrzuschlag'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>20,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>0),
    );

$cells_header =
array(
'persnr'=> array ("popis"=>"\nPersNr","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'name'=> array ("popis"=>"\nName","sirka"=>65,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'abgnr'=> array ("popis"=>"\n","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
//'abgnr_name'=> array ("popis"=>"","sirka"=>65,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'verb_zeit'=> array ("nf"=>array(2,',',' '),"popis"=>"\nVerbZeit","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'risiko_zuschlag_id'=> array ("nf"=>array(2,',',' '),"popis"=>"\n","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'zuschlag_beschreibung'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>80,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
'stunden_zuschlag'=> array ("nf"=>array(2,',',' '),"popis"=>"Std\nZuschlag","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'abgnrzuschlag'=> array ("nf"=>array(2,',',' '),"popis"=>"Risiko\nZuschlag","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'lf'=> array ("popis"=>"\n","sirka"=>0,"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>1),
    );


$sumPersNr = array('verb_zeit'=>0,'abgnrzuschlag'=>0);
$sumBericht = array('verb_zeit'=>0,'abgnrzuschlag'=>0);

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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_person($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 9);
        $obsah = getValueForNode($childs, 'persnr');
	$pdfobjekt->Cell($cells['persnr']['sirka'],$vyskaradku,$obsah,'T',0,'R',$fill);
        $obsah = getValueForNode($childs, 'name');
	$pdfobjekt->Cell($cells['persnr']['name'],$vyskaradku,$obsah,'T',0,'L',$fill);
        $obsah = '';
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'T',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_tat($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $verbZeit = intval(getValueForNode($childs, 'verb_zeit'));
        if($verbZeit>0){

        $obsah = getValueForNode($childs, 'abgnr');
	$pdfobjekt->Cell($cells['abgnr']['sirka']+$cells['persnr']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        $obsah = getValueForNode($childs, 'abgnr_name');
	$pdfobjekt->Cell($cells['abgnr_name']['sirka'],$vyskaradku,$obsah,'0',0,'L',$fill);

        $obsah = number_format($verbZeit,0,',',' ');
	$pdfobjekt->Cell($cells['verb_zeit']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);


        $obsah = '';
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'0',0,'R',$fill);

        $pdfobjekt->Ln();
        }
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_risiko($pdfobjekt,$vyskaradku,$rgb,$childs,$verbZeit=0,$reporttyp='Summe')
{
    global $cells;
    global $sumPersNr;

        $abgnrZuschlag = floatval(getValueForNode($childs, 'stunden_zuschlag'))*$verbZeit/60;
        $sumPersNr['abgnrzuschlag'] += $abgnrZuschlag;

        if($reporttyp=='Detail'){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "", 8);
        $obsah = '';
	$pdfobjekt->Cell($cells['abgnr']['sirka']+$cells['persnr']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        $obsah = getValueForNode($childs, 'risiko_zuschlag_id');
	$pdfobjekt->Cell($cells['risiko_zuschlag_id']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        $obsah = getValueForNode($childs, 'zuschlag_beschreibung');
	$pdfobjekt->Cell($cells['zuschlag_beschreibung']['sirka'],$vyskaradku,$obsah,'0',0,'L',$fill);

        $obsah = number_format(floatval(getValueForNode($childs, 'stunden_zuschlag')),2,',',' ');
	$pdfobjekt->Cell($cells['stunden_zuschlag']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);


        $abgnrZuschlag = floatval(getValueForNode($childs, 'stunden_zuschlag'))*$verbZeit/60;
        $obsah = number_format($abgnrZuschlag,2,',',' ');
	$pdfobjekt->Cell($cells['abgnrzuschlag']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);
        //$sumPersNr['abgnrzuschlag'] += $abgnrZuschlag;

        $obsah = '';
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'0',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        }

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_persnr($pdfobjekt,$vyskaradku,$rgb,$childs,$sumArray)
{
    global $cells;
    global $sumPersNr;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 9);
        $obsah = getValueForNode($childs, 'persnr');
	$pdfobjekt->Cell($cells['persnr']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);
        $obsah = getValueForNode($childs, 'name');
	$pdfobjekt->Cell($cells['name']['sirka'],$vyskaradku,$obsah,'B',0,'L',$fill);

        $sirka = $cells['verb_zeit']['sirka']+$cells['persnr']['sirka'];
        $obsah = number_format($sumArray['verb_zeit'],0,',',' ');
	$pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'B',0,'R',$fill);

        $sirka = $cells['stunden_zuschlag']['sirka']+$cells['abgnrzuschlag']['sirka']+$cells['abgnr']['sirka'];
        $obsah = number_format($sumArray['abgnrzuschlag'],2,',',' ');
	$pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'B',0,'R',$fill);


        $obsah = 0;
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_bericht($pdfobjekt,$vyskaradku,$rgb,$childs,$sumArray)
{
    global $cells;
    global $sumPersNr;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 9);
        $obsah = 'Summe Gesamt';
	$pdfobjekt->Cell($cells['persnr']['sirka']+$cells['name']['sirka'],$vyskaradku,$obsah,'B',0,'L',$fill);

        $sirka = $cells['verb_zeit']['sirka']+$cells['persnr']['sirka'];
        $obsah = number_format($sumArray['verb_zeit'],0,',',' ');
	$pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'B',0,'R',$fill);

        $sirka = $cells['stunden_zuschlag']['sirka']+$cells['abgnrzuschlag']['sirka']+$cells['abgnr']['sirka'];
        $obsah = number_format($sumArray['abgnrzuschlag'],2,',',' ');
	$pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'B',0,'R',$fill);


        $obsah = 0;
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S194 Risiko Zuschlaege", $params);
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


$personen = $domxml->getElementsByTagName("person");
foreach ($personen as $person) {
    nuluj_sumy_pole($sumPersNr);
    $personChilds = $person->childNodes;
    test_pageoverflow($pdf, 5, $cells_header);
    //zahlavi_person($pdf, 5, array(255,255,240), $personChilds);
    $taetigkeiten = $person->getElementsByTagName("taetigkeit");
    foreach ($taetigkeiten as $tat) {
        $tatChilds = $tat->childNodes;
        test_pageoverflow($pdf, 5, $cells_header);
        if($reporttyp=='Detail') zahlavi_tat($pdf, 5, array(255, 255, 255), $tatChilds);
        $risiken = $tat->getElementsByTagName("risiko");
        $verbZeit = intval(getValueForNode($tatChilds, 'verb_zeit'));
        if ($verbZeit > 0) {
            $sumPersNr['verb_zeit'] += $verbZeit;
            foreach ($risiken as $risiko) {
                $risikoChilds = $risiko->childNodes;
                test_pageoverflow($pdf, 5, $cells_header);
                zahlavi_risiko($pdf, 3.5, array(255, 255, 255), $risikoChilds, $verbZeit,$reporttyp);
            }
        }
    }
    test_pageoverflow($pdf, 5, $cells_header);
    if($reporttyp=='Detail'){
        zapati_persnr($pdf, 5, array(255, 255, 240), $personChilds, $sumPersNr);
        $sumBericht['verb_zeit']+=$sumPersNr['verb_zeit'];
    }
    else{
        if($sumPersNr['abgnrzuschlag']!=0){
            zapati_persnr($pdf, 5, array(255, 255, 255), $personChilds, $sumPersNr);
            $sumBericht['verb_zeit']+=$sumPersNr['verb_zeit'];
        }
    }
    $sumBericht['abgnrzuschlag']+=$sumPersNr['abgnrzuschlag'];
}

test_pageoverflow($pdf, 5, $cells_header);
zapati_bericht($pdf, 5, array(240,255,240), $personChilds, $sumBericht);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
