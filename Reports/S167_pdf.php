<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S167";
$doc_subject = "S167 Report";
$doc_keywords = "S167";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];


//$fullAccess = testReportPassword("S169",$password,$user,0);
//
//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}

$apl = AplDB::getInstance();

$eintrittab = $apl->validateDatum(trim($_GET['eintrittab']));
if($eintrittab!=NULL)
    $eintrittab = $apl->make_DB_datum($eintrittab);

$eintrittbis = $apl->validateDatum(trim($_GET['eintrittbis']));
if($eintrittbis!=NULL)
    $eintrittbis = $apl->make_DB_datum($eintrittbis);

$bewerbdatvon = $apl->validateDatum(trim($_GET['bewerbdatvon']));
if($bewerbdatvon!=NULL)
    $bewerbdatvon = $apl->make_DB_datum($bewerbdatvon);

$bewerbdatbis = $apl->validateDatum(trim($_GET['bewerbdatbis']));
if($bewerbdatbis!=NULL)
    $bewerbdatbis = $apl->make_DB_datum($bewerbdatbis);

$geeignet = $_GET['geeignet'];
$vorausoe = $_GET['vorausoe'];

require_once('S167_xml.php');

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
    'eintritt_datum'=> array ("popis"=>"","sirka"=>13,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),
    'eintritt_datum_aktual'=> array ("popis"=>"","sirka"=>13,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
    'austritt'=> array ("popis"=>"","sirka"=>13,"ram"=>'LB',"align"=>"L","radek"=>0,"fill"=>0),
    'eingang_untersuchung'=> array ("popis"=>"","sirka"=>8,"ram"=>'LB',"align"=>"L","radek"=>0,"fill"=>0),
    'oe_voraussichtlich'=> array ("popis"=>"","sirka"=>9,"ram"=>'LRB',"align"=>"L","radek"=>0,"fill"=>0),
    'regeloe'=> array ("popis"=>"","sirka"=>9,"ram"=>'LRB',"align"=>"L","radek"=>0,"fill"=>0),
    'dpersstatus'=> array ("popis"=>"","sirka"=>9,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),
    'persnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
    'vollname'=> array ("popis"=>"","sirka"=>33,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
    'geboren'=> array ("popis"=>"","sirka"=>14,"ram"=>'LBR',"align"=>"L","radek"=>0,"fill"=>0),
    'handy'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
    'aufenthalt'=> array ("popis"=>"","sirka"=>40,"ram"=>'LB',"align"=>"L","radek"=>0,"fill"=>0),
    'bewerbe_datum'=> array ("popis"=>"","sirka"=>14,"ram"=>'LBR',"align"=>"L","radek"=>0,"fill"=>0),
    'bewertung'=> array ("popis"=>"","sirka"=>9,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),
    'transport'=> array ("popis"=>"","sirka"=>10,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),
    'geeignet_text_kurz'=> array ("popis"=>"","sirka"=>10,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),
    'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
    'eintritt_datum'=> array ("popis"=>"ET-P\n","sirka"=>$cells['eintritt_datum']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'eintritt_datum_aktual'=> array ("popis"=>"ET-I\n","sirka"=>$cells['eintritt_datum']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'austritt'=> array ("popis"=>"AT-I\n","sirka"=>$cells['austritt']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'eingang_untersuchung'=> array ("popis"=>"Eing.\nunters.","sirka"=>$cells['eingang_untersuchung']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'oe_voraussichtlich'=> array ("popis"=>"OE-\nP","sirka"=>$cells['oe_voraussichtlich']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'regeloe'=> array ("popis"=>"OE-\nI","sirka"=>$cells['oe_voraussichtlich']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'dpersstatus'=> array ("popis"=>"\nstatus","sirka"=>$cells['dpersstatus']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'persnr'=> array ("popis"=>"\nPersnr","sirka"=>$cells['persnr']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
    'vollname'=> array ("popis"=>"\nName","sirka"=>$cells['vollname']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'geboren'=> array ("popis"=>"Geb.\ndat.","sirka"=>$cells['geboren']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'handy'=> array ("popis"=>"\nHandy","sirka"=>$cells['handy']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'aufenthalt'=> array ("popis"=>"\naktuelle Adresse","sirka"=>$cells['aufenthalt']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'bewerbe_datum'=> array ("popis"=>"Bew.\ndat","sirka"=>$cells['bewerbe_datum']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'bewertung'=> array ("popis"=>"Bewert.\n1/2/3","sirka"=>$cells['bewertung']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'transport'=> array ("popis"=>"\nTransp.","sirka"=>$cells['transport']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'geeignet_text_kurz'=> array ("popis"=>"\ngeeign.","sirka"=>$cells['transport']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
    'lf'=> array ("popis"=>"\n","sirka"=>$cells['lf']['sirka'],"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>1),
);

$anzahlBewerber = 0;

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
function zahlavi_schicht($pdfobjekt,$vyskaradku,$rgb,$schicht,$schichtfuehrer,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(0,$vyskaradku,$schicht." ".$schichtfuehrer,'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$vyskaradku,$popis,$rgb,$anzahlBewerber)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(0,$vyskaradku,$popis.' '.$anzahlBewerber,'LRBT',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


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

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S167 Bewerber ( sort. voraus. Eintritt, voraus. OE, PersNr )", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-3, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER+7);
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
    $personChilds = $person->childNodes;
    test_pageoverflow($pdf,5,$cells_header);
    telo($pdf,$cells,5,array(255,255,255),"",$personChilds);
    $anzahlBewerber++;
}

zapati_sestava($pdf, 5, "Anzahl Bewerber:", array(230,255,230), $anzahlBewerber);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
