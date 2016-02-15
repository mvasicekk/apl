<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S190";
$doc_subject = "S190 Report";
$doc_keywords = "S190";

// necham si vygenerovat XML

$parameters=$_GET;

if($_GET['datumvon']=='*')
    $datumvon = '1.1.2000';
else
    $datumvon = $_GET['datumvon'];

if($_GET['datumbis']=='*')
    $datumbis = '1.1.2015';
else
    $datumbis = $_GET['datumbis'];

$datumvon=make_DB_datum(validateDatum($datumvon));
$datumbis=make_DB_datum(validateDatum($datumbis));

$persnrvon=$_GET['persnrvon'];
$persnrbis=$_GET['persnrbis'];

$amnr=$_GET['amnr'];
$amnr = strtr($amnr, '*', '%');
if(strlen($amnr)==1 && $amnr=='%') $amnr = '';

$bemerkung=$_GET['bemerkung'];
$bemerkung = strtr($bemerkung, '*', '%');
if(strlen($bemerkung)==1 && $bemerkung=='%') $bemerkung = '';

$benutzer=$_GET['benutzer'];
$benutzer = strtr($benutzer, '*', '%');
if(strlen($benutzer)==1 && $benutzer=='%') $benutzer = '';

$oe = trim($_GET['oe']);
$oeArray = split(' ', $oe);
if($oeArray==FALSE)
    $oeArray=NULL;

$reporttyp = $_GET['reporttyp'];
//echo "$amnr";
require_once('S190_xml.php');
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

$cells = 
array(

"dummy1"
=> array ("popis"=>"","sirka"=>40,"ram"=>'L',"align"=>"L","radek"=>0,"fill"=>0),

"datum"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"oe"
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"ausgabestk"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"rueckgabestk"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"differenz"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preisdiff"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"bemerkung"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preisausgabe"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>0,"ram"=>'R',"align"=>"R","radek"=>1,"fill"=>0),
);

$cells_header = 
array(

"dummy1"
=> array ("popis"=>"","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"datum"
=> array ("popis"=>"\nDatum","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"oe"
=> array ("popis"=>"\nOE","sirka"=>8,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"ausgabestk"
=> array ("popis"=>"Aus\ngabe","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"rueckgabestk"
=> array ("popis"=>"Rueck\ngabe","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),


"differenz"
=> array ("popis"=>"\nDiff","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"preisdiff"
=> array ("popis"=>"Preis\naushgabe","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"bemerkung"
=> array ("popis"=>"\nBemerkung","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preisausgabe"
=> array ("popis"=>"Preis\naushgabe","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),
);

$sum_zapati_sestava_array = array(
								"ausgabestk"=>0,
								"rueckgabestk"=>0,
								"differenz"=>0,
                                                                "preisdiff"=>0,
								"preisausgabe"=>0
								);

$sum_zapati_amnr_array = array(
								"ausgabestk"=>0,
								"rueckgabestk"=>0,
								"differenz"=>0,
                                                                "preisdiff"=>0,
								"preisausgabe"=>0
								);

$sum_zapati_person_array = array(
								"ausgabestk"=>0,
								"rueckgabestk"=>0,
								"differenz"=>0,
                                                                "preisdiff"=>0,
								"preisausgabe"=>0
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
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	global $cells;
        global $reporttyp;
        $fill = 1;

        $zahlavivyskaradku = $headervyskaradku;

        $pdfobjekt->Cell($cells['dummy1']['sirka'],$zahlavivyskaradku,'','T',0,'L',$fill);
        if($reporttyp=='summe'){
            $pdfobjekt->Cell($cells['datum']['sirka'],$zahlavivyskaradku,'','T',0,'L',$fill);
            //$pdfobjekt->Cell($cells['oe']['sirka'],$zahlavivyskaradku,'','T',0,'L',$fill);
        }
        else{
            $pdfobjekt->Cell($cells['datum']['sirka'],$zahlavivyskaradku,'Datum','T',0,'L',$fill);
            $pdfobjekt->Cell($cells['oe']['sirka'],$zahlavivyskaradku,'OE','T',0,'L',$fill);
        }

        $pdfobjekt->Cell($cells['ausgabestk']['sirka'],$zahlavivyskaradku,'Aus-','T',0,'R',$fill);
        $pdfobjekt->Cell($cells['rueckgabestk']['sirka'],$zahlavivyskaradku,'Rueck-','T',0,'R',$fill);
        $pdfobjekt->Cell($cells['differenz']['sirka'],$zahlavivyskaradku,'Diff','T',0,'R',$fill);
        $pdfobjekt->Cell($cells['preisdiff']['sirka'],$zahlavivyskaradku,'PreisDiff','T',0,'R',$fill);
	if($reporttyp!='summe')
	    $pdfobjekt->Cell($cells['bemerkung']['sirka'],$zahlavivyskaradku,'Bemerkung','T',0,'R',$fill);

        $pdfobjekt->Cell($cells['preisausgabe']['sirka'],$zahlavivyskaradku,'Preis-','T',1,'R',$fill);

        $pdfobjekt->Cell($cells['dummy1']['sirka'],$zahlavivyskaradku,'','B',0,'L',$fill);
        $pdfobjekt->Cell($cells['datum']['sirka'],$zahlavivyskaradku,'','B',0,'L',$fill);
        if($reporttyp!='summe')
            $pdfobjekt->Cell($cells['oe']['sirka'],$zahlavivyskaradku,'','B',0,'L',$fill);
        $pdfobjekt->Cell($cells['ausgabestk']['sirka'],$zahlavivyskaradku,'gabe','B',0,'R',$fill);
        $pdfobjekt->Cell($cells['rueckgabestk']['sirka'],$zahlavivyskaradku,'gabe','B',0,'R',$fill);
        $pdfobjekt->Cell($cells['differenz']['sirka'],$zahlavivyskaradku,'','B',0,'R',$fill);
        $pdfobjekt->Cell($cells['preisdiff']['sirka'],$zahlavivyskaradku,'','B',0,'R',$fill);
	if($reporttyp!='summe')
	    $pdfobjekt->Cell($cells['bemerkung']['sirka'],$zahlavivyskaradku,'','B',0,'R',$fill);
        $pdfobjekt->Cell($cells['preisausgabe']['sirka'],$zahlavivyskaradku,'ausgabe','B',1,'R',$fill);

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
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
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
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;

        $pdfobjekt->SetFont("FreeSans", "B", 8);

        $obsah = getValueForNode($childs, 'persnr');
	$pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,$obsah,'LT',0,'L',$fill);

        $obsah = getValueForNode($childs, 'name');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'RT',1,'L',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_am($pdfobjekt,$vyskaradku,$rgb,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;

        $pdfobjekt->SetFont("FreeSans", "B", 8);

        $obsah = getValueForNode($childs, 'amnr');
	$pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,$obsah,'LT',0,'L',$fill);

        $obsah = getValueForNode($childs, 'artikelname');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'RT',1,'L',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_ogoe($pdfobjekt,$vyskaradku,$rgb,$og,$oe)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(0,$vyskaradku,$og.' -> '.$oe,'1',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_oe($pdfobjekt, $vyskaradku, $rgb, $sum,$oe,$text){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(80,$vyskaradku,$text.' '.$oe,'1',0,'L',$fill);

        $obsah = number_format($sum['ausgabestk'], 0);
        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $obsah = number_format($sum['rueckgabestk'], 0);
        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $obsah = number_format($sum['differenz'], 0);
        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

//        $obsah = number_format($sum['preisdiff'], 0);
//        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $obsah = number_format($sum['preisausgabe'], 2);
        $pdfobjekt->Cell(0,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);

}
function pageheaderOGOE($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	global $cells;
        global $reporttyp;
        $fill = 1;

        $zahlavivyskaradku = $headervyskaradku;

        $pdfobjekt->Cell(20,$zahlavivyskaradku,'OG->OE','T',0,'L',$fill);
        $pdfobjekt->Cell(60,$zahlavivyskaradku,'Artikel','T',0,'L',$fill);

        $pdfobjekt->Cell(20,$zahlavivyskaradku,'Aus-','T',0,'R',$fill);
        $pdfobjekt->Cell(20,$zahlavivyskaradku,'Rueck-','T',0,'R',$fill);
        $pdfobjekt->Cell(20,$zahlavivyskaradku,'Diff','T',0,'R',$fill);
//        $pdfobjekt->Cell(20,$zahlavivyskaradku,'PreisDiff','T',0,'R',$fill);
        $pdfobjekt->Cell(0,$zahlavivyskaradku,'Preis-','T',1,'R',$fill);

        $pdfobjekt->Cell(20,$zahlavivyskaradku,'','B',0,'L',$fill);
        $pdfobjekt->Cell(60,$zahlavivyskaradku,'','B',0,'L',$fill);

        $pdfobjekt->Cell(20,$zahlavivyskaradku,'gabe','B',0,'R',$fill);
        $pdfobjekt->Cell(20,$zahlavivyskaradku,'gabe','B',0,'R',$fill);
        $pdfobjekt->Cell(20,$zahlavivyskaradku,'','B',0,'R',$fill);
//        $pdfobjekt->Cell(20,$zahlavivyskaradku,'','B',0,'R',$fill);
        $pdfobjekt->Cell(0,$zahlavivyskaradku,'ausgabe','B',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}

function radek_am($pdfobjekt,$vyskaradku,$rgb,$amChilds,$ausStk,$rueckStk,$diff,$ausPreis)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;
        global $cells;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(20,$vyskaradku,'','1',0,'L',$fill);
        $pdfobjekt->Cell(20,  $vyskaradku,getValueForNode($amChilds, 'amnr'),'TB',0,'L',$fill);
        $pdfobjekt->Cell(40,  $vyskaradku,getValueForNode($amChilds, 'artikelname'),'TB',0,'L',$fill);
        $obsah = number_format($ausStk, 0);
        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $obsah = number_format($rueckStk, 0);
        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $obsah = number_format($diff, 0);
        $pdfobjekt->Cell(20,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $obsah = number_format($ausPreis, 2);
        $pdfobjekt->Cell(0,  $vyskaradku,$obsah,'LTB',0,'R',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_am($pdfobjekt,$vyskaradku,$rgb,$childs,$sumArray)
{

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $reporttyp;

        $pdfobjekt->SetFont("FreeSans", "B", 8);

        $obsah = getValueForNode($childs, 'amnr');
	$pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,$obsah,'LT',0,'L',$fill);

        $obsah = getValueForNode($childs, 'artikelname');
	$pdfobjekt->Cell($cells['dummy1']['sirka'],$vyskaradku,$obsah,'RT',0,'L',$fill);

//        ausgabestk"=>0,
//	rueckgabestk"=>0,
//	differenz"=>0,
//	preisausgabe"=>0

        if($reporttyp=='detail')
            $pdfobjekt->Cell($cells['oe']['sirka'],$vyskaradku,'','T',0,'L',$fill);

        $key = 'ausgabestk';
        $obsah = number_format($sumArray[$key],0,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RT',0,'R',$fill);
        $key = 'rueckgabestk';
        $obsah = number_format($sumArray[$key],0,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RT',0,'R',$fill);
        $key = 'differenz';
        $obsah = number_format($sumArray[$key],0,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RT',0,'R',$fill);
        $key = 'preisdiff';
        if($sumArray[$key]!=0)
            $obsah = number_format($sumArray[$key],2,',',' ');
        else
            $obsah = '';
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RT',0,'R',$fill);
        $key = 'preisausgabe';
        $obsah = number_format($sumArray[$key],2,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RT',1,'R',$fill);

        $pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_person($pdfobjekt,$vyskaradku,$rgb,$childs,$sumArray)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $reporttyp;

        $pdfobjekt->SetFont("FreeSans", "B", 8);

        $obsah = 'Summe';//getValueForNode($childs, 'persnr');
	$pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,$obsah,'LTB',0,'L',$fill);

        $obsah = '';//getValueForNode($childs, 'name');
	$pdfobjekt->Cell($cells['dummy1']['sirka'],$vyskaradku,$obsah,'RTB',0,'L',$fill);

//        ausgabestk"=>0,
//	rueckgabestk"=>0,
//	differenz"=>0,
//	preisausgabe"=>0

        if($reporttyp=='detail')
            $pdfobjekt->Cell($cells['oe']['sirka'],$vyskaradku,'','TB',0,'L',$fill);

        $key = 'ausgabestk';
        $obsah = '';//number_format($sumArray[$key],0,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RTB',0,'R',$fill);
        $key = 'rueckgabestk';
        $obsah = '';//number_format($sumArray[$key],0,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RTB',0,'R',$fill);
        $key = 'differenz';
        $obsah = '';//number_format($sumArray[$key],0,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RTB',0,'R',$fill);

        $key = 'preisdiff';
        $obsah = number_format($sumArray[$key],2,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RTB',0,'R',$fill);

        $key = 'preisausgabe';
        $obsah = number_format($sumArray[$key],2,',',' ');
	$pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,$obsah,'RTB',1,'R',$fill);

        $pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb, $pole){
    	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $reporttyp;

        $pdfobjekt->SetFont("FreeSans", "B", 8);

        $obsah = '';//getValueForNode($childs, 'persnr');
        if($reporttyp=='detail')
            $pdfobjekt->Cell($cells['dummy1']['sirka']+$cells['datum']['sirka']+$cells['oe']['sirka'],$vyskaradku,"Gesamtsumme",'TB',0,'L',$fill);
        else
            $pdfobjekt->Cell($cells['dummy1']['sirka']+$cells['datum']['sirka'],$vyskaradku,"Gesamtsumme",'TB',0,'L',$fill);

        $obsah = $pole['ausgabestk'];
        $pdfobjekt->Cell($cells['ausgabestk']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        $obsah = $pole['rueckgabestk'];
        $pdfobjekt->Cell($cells['rueckgabestk']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        $obsah = $pole['differenz'];
        $pdfobjekt->Cell($cells['differenz']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        $obsah = number_format($pole['preisdiff'],2,',',' ');
        $pdfobjekt->Cell($cells['preisdiff']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        $obsah = number_format($pole['preisausgabe'],2,',',' ');
        $pdfobjekt->Cell($cells['preisausgabe']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        $obsah = '';//getValueForNode($childs, 'name');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'TB',1,'L',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//function zapati_person($pdfobjekt,$vyskaradku,$rgb,$childs)
//{
//	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
//	$fill=1;
//        global $cells;
//
//        $pdfobjekt->SetFont("FreeSans", "B", 8);
//
//        $obsah = '';//getValueForNode($childs, 'persnr');
//	$pdfobjekt->Cell($cells['amnr']['sirka'],$vyskaradku,$obsah,'T',0,'L',$fill);
//
//        $obsah = '';//getValueForNode($childs, 'name');
//	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'T',1,'L',$fill);
//
//	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
//}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,4);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
	}
}
				
function test_pageoverflowOGOE($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheaderOGOE($pdfobjekt,$cellhead,4);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
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

if (strstr($reporttyp, "sort lt.og-oe") != FALSE)
        $reportBeschreibung = 'S190 Arbeitsmittelausgabe gruppiert lt. OG->OE->AmNr';
else
        $reportBeschreibung = "S190 Arbeitsmittelausgabe pro Persnr";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $reportBeschreibung, $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-8, PDF_MARGIN_RIGHT);
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

$sumOG = array();
$sumOE = array();
$sumGesamt = array();

if (strstr($reporttyp, "sort lt.og-oe") != FALSE) {
    $summenOG = array();
// zobrazeni en pro OG,OE
    $pdf->AddPage();
    pageheaderOGOE($pdf, $cells, 4);
    $ogs = $domxml->getElementsByTagName("og");
    foreach ($ogs as $og) {
        $ogChilds = $og->childNodes;
        $ognr = getValueForNode($ogChilds, 'ognr');
        $oes = $og->getElementsByTagName('oe');

        foreach ($oes as $oe) {
            $oeChilds = $oe->childNodes;
            $oenr = getValueForNode($oeChilds, 'oenr');
            //zahlavi OG/OE
            test_pageoverflowOGOE($pdf,5,$cells_header);
            zahlavi_ogoe($pdf, 5, array(230,255,230), $ognr, $oenr);
            $ams = $oe->getElementsByTagName('am');
            $sumOE = array();
            foreach($ams as $am){
                $amChilds = $am->childNodes;
                $amnr = getValueForNode($amChilds, 'amnr');
                $details = $am->getElementsByTagName('detail');
                $detaily = array();
                foreach($details as $detail) array_push ($detaily, $detail);
                $detail = $detaily[0];
                $detailChilds = $detail->childNodes;
                $ausStk = getValueForNode($detailChilds, 'ausgabestk');
                $rueckStk = getValueForNode($detailChilds, 'rueckgabestk');
                $differenz = getValueForNode($detailChilds, 'differenz');
                $preisausgabe = getValueForNode($detailChilds, 'preisausgabe');
                $sumOE[$oenr]['ausgabestk'] += $ausStk;$sumOE[$oenr]['rueckgabestk'] += $rueckStk;$sumOE[$oenr]['differenz'] += $differenz;$sumOE[$oenr]['preisausgabe'] += $preisausgabe;
                $sumOG[$ognr]['ausgabestk'] += $ausStk;$sumOG[$ognr]['rueckgabestk'] += $rueckStk;$sumOG[$ognr]['differenz'] += $differenz;$sumOG[$ognr]['preisausgabe'] += $preisausgabe;
                $sumGesamt['ausgabestk'] += $ausStk;$sumGesamt['rueckgabestk'] += $rueckStk;$sumGesamt['differenz'] += $differenz;$sumGesamt['preisausgabe'] += $preisausgabe;
                test_pageoverflowOGOE($pdf,5,$cells_header);
                radek_am($pdf, 5, array(255,255,255), $amChilds, $ausStk, $rueckStk, $differenz, $preisausgabe);
            }
            test_pageoverflowOGOE($pdf,2*5,$cells_header);
            zapati_oe($pdf, 5, array(230,255,230), $sumOE[$oenr],$oenr,"Summe");
            $pdf->Ln();
        }
        test_pageoverflowOGOE($pdf,2*5,$cells_header);
        zapati_oe($pdf, 5, array(255,255,230), $sumOG[$ognr],$ognr,"Summe");
        $pdf->Ln();
    }

    $pdf->AddPage();
    pageheaderOGOE($pdf, $cells, 4);
    $pdf->Ln();
    $pdf->Cell(0, 5, 'Summen nach OG Zusammenfassung:', '1', 1, 'L', 0);
    foreach ($sumOG as $og=>$sumArray){
        zapati_oe($pdf, 5, array(255,255,230), $sumArray,$og,"Summe");
    }

    
    zapati_oe($pdf, 5, array(255,255,255), $sumGesamt,'',"Summe Gesamt");
    $pdf->Output();
    exit;
}
else{
// prvni stranka
$pdf->AddPage();
pageheader($pdf, $cells_header, 4);
// 
$personen = $domxml->getElementsByTagName("person");
foreach($personen as $person)
{
        $personChilds = $person->childNodes;
	$amArray = $person->getElementsByTagName("am");
        
        // kolik budu potrebovat radku pro vystup jednoho cloveka
        $pocetRadku = $amArray->length + 1 + 1;
//        echo "persnr=".getValueForNode($personChilds, 'persnr').", pocetRadku=$pocetRadku<br>";
        test_pageoverflow($pdf,$pocetRadku*5,$cells_header);
        zahlavi_person($pdf, 4, array(240,240,240), $personChilds);

        nuluj_sumy_pole($sum_zapati_person_array);

	foreach($amArray as $am)
	{
                nuluj_sumy_pole($sum_zapati_amnr_array);
		$amChilds = $am->childNodes;

                if($reporttyp=='detail'){
                    test_pageoverflow($pdf,5,$cells_header);
                    zahlavi_am($pdf, 4, array(240,240,240), $amChilds);
                }

                $detailArray = $am->getElementsByTagName('detail');
                foreach($detailArray as $detail){
                    $detailChilds = $detail->childNodes;
                    if($reporttyp=='detail'){
                        test_pageoverflow($pdf,4,$cells_header);
                        telo($pdf,$cells,4,array(255,255,255),"",$detailChilds);
                    }

                    foreach($sum_zapati_amnr_array as $key=>$prvek)
                    {
			$hodnota=floatval(getValueForNode($detailChilds, $key));
			$sum_zapati_amnr_array[$key]+=$hodnota;
                    }

                }

                foreach($sum_zapati_sestava_array as $key=>$prvek)
                    {
			$hodnota=$sum_zapati_amnr_array[$key];
			$sum_zapati_sestava_array[$key]+=$hodnota;
                    }

                foreach($sum_zapati_person_array as $key=>$prvek)
                    {
			$hodnota=$sum_zapati_amnr_array[$key];
			$sum_zapati_person_array[$key]+=$hodnota;
                    }

//                test_pageoverflow($pdf,5,$cells_header);
                if($reporttyp=='detail')
                    zapati_am($pdf, 4, array(240,240,240), $amChilds,$sum_zapati_amnr_array);
                else
                    zapati_am($pdf, 4, array(255,255,255), $amChilds,$sum_zapati_amnr_array);

	}
        test_pageoverflow($pdf,4,$cells_header);
        zapati_person($pdf, 4, array(240,240,240), $personChilds,$sum_zapati_person_array);
        $pdf->Ln();
}

test_pageoverflow($pdf,4,$cells_header);
zapati_sestava($pdf, 4, array(255,255,255), $sum_zapati_sestava_array);
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
