<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S280";
$doc_subject = "S280 Report";
$doc_keywords = "S280";

// necham si vygenerovat XML

$parameters=$_GET;

$datum_von=make_DB_datum($_GET['datum_von']);
$datum_bis=make_DB_datum($_GET['datum_bis']);
//$schicht_von=$_GET['schicht_von'];
//$schicht_bis=$_GET['schicht_bis'];
$pers_von=$_GET['pers_von'];
$pers_bis=$_GET['pers_bis'];
$kunde=$_GET['kunde'];
$tatvon = $_GET['tatvon'];
$tatbis = $_GET['tatbis'];
$oe = trim($_GET['oe']);
$oeStamm = trim($_GET['ogStamm']);
$oe1Stamm = trim($_GET['oeStamm']);


$vzkdsicht = $_GET['vzkdsicht'];
$teil = trim($_GET['teil']);

// v oe muze byt vice polozek oddelenych mezerama
$oeArray = split(' ', $oe);
if($oeArray==FALSE)
    $oeArray=NULL;

$oeStammArray = split(' ', $oeStamm);
if($oeStammArray==FALSE)
    $oeStammArray=NULL;

$oe1StammArray = split(' ', $oe1Stamm);
if($oe1StammArray==FALSE)
    $oe1StammArray=NULL;

require_once('S280_xml.php');

$apl = AplDB::getInstance();

$puser = $_SESSION['user'];
if($apl->getDisplaySec('S280', 'vzkd', $puser)===FALSE)
	$vzkdsicht = 0;


$priplatekArray = array(
                        "S0011"=>array(100,100,50,50,50,50,50,50,30,30,30,30,30,30,20,20,20,20,20,20),
                        "S0041"=>array(50,50,30,30,20,20,20,20,20,20,0,0,0,0,0,0,0,0,0,0),
                        "S0051"=>array(100,100,50,50,50,50,50,50,30,30,30,30,30,30,20,20,20,20,20,20),
                        "S0061"=>array(50,50,30,30,20,20,20,20,20,20,0,0,0,0,0,0,0,0,0,0),
                        "X"=>array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),
);





//$priplatekDpersDatumZuschlag = $apl->getDpersDatumZuschlagArray($persnr);


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
=> array ("popis"=>"","sirka"=>17,"ram"=>'L',"align"=>"L","radek"=>0,"fill"=>0),

"pal" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"Teilbez" 
=> array ("substring"=>array(0,20),"popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"oe"
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"TaetNr" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_typ" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"ma" 
=> array ("popis"=>"","sirka"=>5,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"von" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"bis" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'R',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(

"Teil" 
=> array ("popis"=>"Teil","sirka"=>17,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"pal" 
=> array ("popis"=>"Pal","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"Teilbez" 
=> array ("popis"=>"Bezeichnung","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"oe"
=> array ("popis"=>"OE-IST","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"TaetNr" 
=> array ("popis"=>"TatNr","sirka"=>8,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"stk" 
=> array ("popis"=>"Stk","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auss_stk" 
=> array ("popis"=>"Auss","sirka"=>7,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auss_typ" 
=> array ("popis"=>"AussTyp","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd_stk" 
=> array ("popis"=>"vzkd/stk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby_stk" 
=> array ("popis"=>"vzaby/stk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"verb_stk" 
=> array ("popis"=>"verb/stk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd" 
=> array ("popis"=>"vzkd","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("popis"=>"vzaby","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("popis"=>"verb","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"ma" 
=> array ("popis"=>"auft","sirka"=>5,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"von" 
=> array ("popis"=>"von","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"bis" 
=> array ("popis"=>"bis","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_auftrag_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_auftrag_array;

$sum_zapati_pers_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_pers_array;

$sum_zapati_datum_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);
global $sum_zapati_datum_array;

$sum_zapati_sestava_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
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
        global $vzkdsicht;
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $klic=>$cell)
	{
                $obsah = $cell['popis'];
                if($vzkdsicht==0 &&($klic=='vzkd' || $klic=='vzkd_stk')) $obsah = '';
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$obsah,$cell["ram"],$cell["align"],$cell['fill']);
	}
//	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
        global $vzkdsicht;
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
                
                if(array_key_exists("substring",$cell))
		{
                        $append='';
                        if(strlen($cellobsah)>$cell['substring'][1]) $append='...';
			$cellobsah = substr($cellobsah,$cell['substring'][0],$cell['substring'][1]).$append;
		}
                
                if(($nodename=='vzkd' || $nodename=='vzkd_stk') && $vzkdsicht==0) $cellobsah='';
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
function zahlavi_pers($pdfobjekt,$vyskaradku,$rgb,$cells_header,$persnr,$name,$vorname,$regeloe,$von,$bis,$datum,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $eintritt_letzt = trim(getValueForNode($childs,'eintritt_letzt'));
        if(strlen($eintritt_letzt)>0) $eintritt_letzt = substr ($eintritt_letzt, 0, 10);

        $austritt_letzt = trim(getValueForNode($childs,'austritt_letzt'));
        if(strlen($austritt_letzt)>0) $austritt_letzt = substr ($austritt_letzt, 0, 10);

        $eintritt_count = getValueForNode($childs,'eintritt_count');
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(15,$vyskaradku,$persnr,'LT',0,'L',$fill);
	$pdfobjekt->Cell(30,$vyskaradku,$name." ".$vorname,'T',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(20,$vyskaradku,"( OE-ST:".$regeloe." )",'T',0,'L',$fill);
        $pdfobjekt->Cell(40,$vyskaradku,"L. Eintritt: ".$eintritt_letzt." (".$eintritt_count.".)",'LT',0,'L',$fill);
        $pdfobjekt->Cell(40,$vyskaradku,"L. Austritt: ".$austritt_letzt,'TR',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->Cell(0,$vyskaradku,  substr($datum, 0, 10),'TR',1,'R',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_auftrag($pdfobjekt,$vyskaradku,$rgb,$cells_header,$auftragsnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(0,$vyskaradku,"AuftragsNr: ".$auftragsnr,'LR',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

// pritiskne tabulku s dochazku pro dane persnr a datum
function persnr_anwesenheit($pdfobjekt,$vyskaradku,$anwesenheitArray,$vzaby,$rgb) {
//    "Teil"
//"pal"
//"Teilbez"
//"oe"
//"TaetNr"
//"stk"
//"auss_stk"
//"auss_typ"
//"vzkd_stk"
//"vzaby_stk"
//"verb_stk"
//"vzkd"
//"vzaby"
//"verb"
//"ma"
//"von"
//"bis"

    global $cells;

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $fill=1;

//    $obsah="";
//    $pdfobjekt->SetFont("FreeSans", "B", 8);
//    $pdfobjekt->Cell(
//            0,
//            $vyskaradku,
//            "Anwesenheit",
//            'LRT',
//            1,
//            'L',
//            $fill);

    $pdfobjekt->SetFont("FreeSans", "", 7);
    $i=0;
    $sumStunden=0;
    foreach ($anwesenheitArray as $anwesenheit) {
        $fill=0;
        $pdfobjekt->Cell(
                $cells['Teil']['sirka']+
                $cells['pal']['sirka']+
                $cells['Teilbez']['sirka']
                ,$vyskaradku,
                $i==0?"Anwesenheit":"",
                'L',
                0,
                'L',
                $fill);

        $pdfobjekt->Cell(
                $cells['oe']['sirka'],
                $vyskaradku,
                $anwesenheit['tat'],
                '',
                0,
                'R',
                $fill
        );

        $pdfobjekt->Cell(
                $cells["TaetNr"]['sirka']+
                $cells["stk"]['sirka']+
                $cells["auss_stk"]['sirka']+
                $cells["auss_typ"]['sirka']+
                $cells["vzkd_stk"]['sirka']+
                $cells["vzaby_stk"]['sirka']+
                $cells["verb_stk"]['sirka']+
                $cells["vzkd"]['sirka']+
                $cells["vzaby"]['sirka'],
                $vyskaradku,
                '',
                '',
                0,
                'L',
                $fill
        );

        $obsah = number_format($anwesenheit['stunden']*60,0,',',' ');
        $sumStunden += $anwesenheit['stunden']*60;
        $pdfobjekt->Cell(
                $cells['verb']['sirka'],
                $vyskaradku,
                $obsah,
                '',
                0,
                'R',
                $fill
        );

        $pdfobjekt->Cell(
                $cells["ma"]['sirka'],
                $vyskaradku,
                '',
                '',
                0,
                'L',
                $fill
        );

        $pdfobjekt->Cell(
                $cells['von']['sirka'],
                $vyskaradku,
                $anwesenheit['anw_von'],
                '',
                0,
                'R',
                $fill
        );

        $pdfobjekt->Cell(
                $cells['bis']['sirka'],
                $vyskaradku,
                $anwesenheit['anw_bis'],
                'R',
                1,
                'R',
                $fill
        );

        $i++;
    }

    $obsah=number_format($sumStunden,0,',',' ');
    $fill=1;
    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->Cell(
            $cells['Teil']['sirka']+
            $cells['pal']['sirka']+
            $cells['Teilbez']['sirka']+
            $cells["oe"]['sirka']+
            $cells["TaetNr"]['sirka']+
            $cells["stk"]['sirka']+
            $cells["auss_stk"]['sirka']+
            $cells["auss_typ"]['sirka']+
            $cells["vzkd_stk"]['sirka']+
            $cells["vzaby_stk"]['sirka']+
            $cells["verb_stk"]['sirka']+
            $cells["vzkd"]['sirka']+
            $cells["vzaby"]['sirka']
            ,
            $vyskaradku,
            "Anwesenheit Summe",
            'LB',
            0,
            'L',
            $fill);

    $pdfobjekt->Cell(
            $cells['verb']['sirka'],
            $vyskaradku,
            $obsah,
            'B',
            0,
            'R',
            $fill
    );

    $factor = $sumStunden!=0?$vzaby/$sumStunden:0;
    $obsah = number_format($factor,2,',',' ');
    $pdfobjekt->SetFont("FreeSans", "B", 6);
    $pdfobjekt->Cell(
            $cells["ma"]['sirka'],
            $vyskaradku,
            $obsah,
            'B',
            0,
            'R',
            $fill
    );

    $pdfobjekt->Cell(
            0,
            $vyskaradku,
            '',
            'RB',
            1,
            'R',
            $fill
    );


    $pdfobjekt->Ln();
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_pers($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $vzkdsicht;

	// dummy
	$obsah="";
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(125,$vyskaradku,$popis,'LB',0,'L',$fill);
	
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
        if($vzkdsicht==0) $obsah='';
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

        $pdfobjekt->SetFont("FreeSans", "B", 6);
      	$obsah=$pole['verb']!=0?$pole['vzaby']/$pole['verb']:0;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell($cells['ma']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	//dummy
	$pdfobjekt->Cell(0,$vyskaradku,"",'BR',1,'R',$fill);

//	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_pers_zuschlag($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $vzkdsicht;
        global $priplatekArray;

        //projdu pole vsech statnr a zjistim zda mam nejake nenulove priplatky, pokud ano, teprve potom zobrazim sekci s priplatkama
        $multiMax = 0;
        foreach ($pole as $statnr=>$info){
            if($info['vzaby']!=0){
                $multi = 0;
                if(array_key_exists($statnr, $priplatekArray)){
                    if($info['poradi']<=count($priplatekArray[$statnr])){
                        $multi = $priplatekArray[$statnr][$info['poradi']-1];
                    }
                }
                if($multi>$multiMax) $multiMax=$multi;
            }
        }

        if($multiMax==0) return;
	// dummy
	$obsah="";
	$pdfobjekt->SetFont("FreeSans", "B", 8);

        // radek s popisem
        $pdfobjekt->Cell(0,$vyskaradku,$popis,'LRT',1,'L',$fill);

        // projdu pole vsech statnr
        $sumaPriplatku = 0;$pocetPriplatku = 0;
        foreach ($pole as $statnr=>$info){
            if($info['vzaby']!=0){
                $multi = 0;
                if(array_key_exists($statnr, $priplatekArray)){
                    if($info['poradi']<=count($priplatekArray[$statnr])){
                        $multi = $priplatekArray[$statnr][$info['poradi']-1];
                    }
                }
                if($multi>0){
                $priplatekPopis = "$statnr (".$multi."%)";
                $pdfobjekt->Cell(125,$vyskaradku,$priplatekPopis,'L',0,'L',$fill);
                //vzkd
                $pdfobjekt->Cell(10,$vyskaradku,'','',0,'R',$fill);
                //vzaby
              	$obsah=$info['vzaby']*$multi/100;
                $sumaPriplatku += $obsah;
                $obsah=number_format($obsah,0,',',' ');
                $pdfobjekt->Cell(10,$vyskaradku,$obsah,'',0,'R',$fill);
                //dummy
                $pdfobjekt->Cell(0,$vyskaradku,"",'R',1,'R',$fill);
                $pocetPriplatku++;
                }
            }
        }

        if($pocetPriplatku>1){
                $pdfobjekt->Cell(125,$vyskaradku,'Summe Zuschlaege:','L',0,'L',$fill);
                //vzkd
                $pdfobjekt->Cell(10,$vyskaradku,'','',0,'R',$fill);
                $obsah=number_format($sumaPriplatku,0,',',' ');
                $pdfobjekt->Cell(10,$vyskaradku,$obsah,'',0,'R',$fill);
                //dummy
                $pdfobjekt->Cell(0,$vyskaradku,"",'R',1,'R',$fill);
        }

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_datum($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$datumnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $vzkdsicht;
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(125,$vyskaradku,$popis." ".substr($datumnr, 0, 10),'LTB',0,'L',$fill);
	
	
	$obsah=$pole['vzkd'];

	$obsah=number_format($obsah,0,',',' ');
        if($vzkdsicht==0) $obsah='';
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

        $pdfobjekt->SetFont("FreeSans", "B", 6);
      	$obsah=$pole['verb']!=0?$pole['vzaby']/$pole['verb']:0;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell($cells['ma']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

	//dummy
	$pdfobjekt->Cell(0,$vyskaradku,"",'TBR',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        global $cells;
        global $vzkdsicht;
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(125,$vyskaradku,$popis,'LTB',0,'L',$fill);
	
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
        if($vzkdsicht==0) $obsah='';
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

        $pdfobjekt->SetFont("FreeSans", "B", 6);
      	$obsah=$pole['verb']!=0?$pole['vzaby']/$pole['verb']:0;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell($cells['ma']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	//dummy
	$pdfobjekt->Cell(0,$vyskaradku,"",'TBR',1,'R',$fill);

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
		pageheader($pdfobjekt,$cellhead,5);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S280 Leistung MA - Auftrag - Teil (\"Dukla\")", $params);
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
pageheader($pdf,$cells_header,5);
//$pdf->Ln();
//$pdf->Ln();


// a ted pujdu po datumech
$datumy=$domxml->getElementsByTagName("datumy");
$apl = new AplDB();

foreach($datumy as $datum)
{
	$datumnr=$datum->getElementsByTagName("datum")->item(0)->nodeValue;
		
	//test_pageoverflow($pdf,5,$cells_header);
	//zahlavi_teil($pdf,5,array(255,255,200),$teilnr,$cells_header);
	nuluj_sumy_pole($sum_zapati_datum_array);
	
	$personal=$datum->getElementsByTagName("pers");
	
	foreach($personal as $pers)
	{
                $persChilds = $pers->childNodes;
		$persnr=$pers->getElementsByTagName("PersNr")->item(0)->nodeValue;
		$name=$pers->getElementsByTagName("Name")->item(0)->nodeValue;
		$vorname=$pers->getElementsByTagName("Vorname")->item(0)->nodeValue;
		$regelOE=$pers->getElementsByTagName("regeloe")->item(0)->nodeValue;
		$von=$pers->getElementsByTagName("anwvon")->item(0)->nodeValue;
		$bis=$pers->getElementsByTagName("anwbis")->item(0)->nodeValue;
                $einarbZuschlag = intval(getValueForNode($persChilds, 'einarb_zuschlag'));
                // zjistim pocet dnu mezi aktualnim datumem a datumem nastupu
                $eintrittDB = substr(getValueForNode($persChilds, 'eintritt_letzt'),0,10);
                $aktualDatumDB = substr($datumnr,0,10);
                $aktualDatumStamp = strtotime($aktualDatumDB);
                $eintrittStamp = strtotime($eintrittDB);
                if($aktualDatumStamp!=FALSE && $eintrittStamp!=FALSE){
                    $pocetDnuOdNastupu = round(($aktualDatumStamp-$eintrittStamp)/(60*60*24));
                }
                else
                    $pocetDnuOdNastupu = 9999;

                // v pripade ze je pocet dnu od nastupu < 60, zjistim si pole priplatku pro datum a cloveka
                if($pocetDnuOdNastupu<60)
                    $datumPriplatekStatNrArray = $apl->getZuschlagTageCount($persnr,$eintrittDB,$aktualDatumDB);
//                echo "<pre>";var_dump($datumPriplatekStatNrArray);echo "</pre>";

                //pro dany den projdu vsechny mozne priplatky a vyrobim si pole s priplatky
                $priplatekPopis = '';
                $snrArray = NULL;
                if($datumPriplatekStatNrArray!==NULL){
                        $snrArray = $datumPriplatekStatNrArray[$aktualDatumDB];
////                        echo "<pre>";var_dump($snrArray);echo "</pre>";
//                        foreach ($snrArray as $statnr=>$info){
//                            $priplatekPopis .= "$statnr (poradi:".$info['poradi'].",vzaby:".$info['vzaby'].")";
//                        }
                }
		test_pageoverflow($pdf,5,$cells_header);

		zahlavi_pers($pdf,5,array(235,235,235),$cells_header,$persnr,$name,$vorname,$regelOE,$von,$bis,$datumnr,$persChilds);
		nuluj_sumy_pole($sum_zapati_pers_array);
		
		$auftraege=$pers->getElementsByTagName("auftrag");
		
		foreach($auftraege as $auftrag)
		{
		
			$auftragsnr=$auftrag->getElementsByTagName("AuftragsNr")->item(0)->nodeValue;
			test_pageoverflow($pdf,5,$cells_header);
			zahlavi_auftrag($pdf,5,array(255,255,255),$cells_header,$auftragsnr);
			nuluj_sumy_pole($sum_zapati_auftrag_array);
			
			$positionen=$auftrag->getElementsByTagName("position");
			
			foreach($positionen as $position)
			{
				$position_childs=$position->childNodes;
				test_pageoverflow($pdf,5,$cells_header);
				telo($pdf,$cells,5,array(255,255,255),"",$position_childs);
				
				// projedu pole a aktualizuju sumy pro zapati pers
				foreach($sum_zapati_auftrag_array as $key=>$prvek)
				{
					$hodnota=$position->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_auftrag_array[$key]+=$hodnota;
				}
			}
			
			// sumy pro zapati pers
			foreach($sum_zapati_pers_array as $key=>$prvek)
			{
				$hodnota=$sum_zapati_auftrag_array[$key];
				$sum_zapati_pers_array[$key]+=$hodnota;
			}
		}
		
		test_pageoverflow($pdf,5,$cells_header);
		zapati_pers($pdf,$pers,5,"Summe PersNr ".$priplatekPopis,array(235,235,235),$sum_zapati_pers_array);
                if(is_array($snrArray)){
                    if(($pocetDnuOdNastupu<60)&&($einarbZuschlag!=0)){
                    test_pageoverflow($pdf,5,$cells_header);
                    zapati_pers_zuschlag($pdf,$pers,5,"Einarbeitungszuschlag [abymin]"." (".($pocetDnuOdNastupu+1).".)",array(240,255,240),$snrArray);
                    }
                }

                $anwArray = $apl->getAnwesenheitArrayForPersNrDatum($persnr, $datumnr);
                test_pageoverflow($pdf,(count($anwArray)+1)*5,$cells_header);
                persnr_anwesenheit($pdf,5,$anwArray,$sum_zapati_pers_array['vzaby'],array(235,235,235));

		foreach($sum_zapati_datum_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_pers_array[$key];
			$sum_zapati_datum_array[$key]+=$hodnota;
		}

	}

        if($pers_von!=$pers_bis){
            test_pageoverflow($pdf,5,$cells_header);
            zapati_datum($pdf,$pers,5,"Summe Datum",array(235,235,235),$sum_zapati_datum_array,$datumnr);
        }
		
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_datum_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
}

test_pageoverflow($pdf,5,$cells_header);
zapati_sestava($pdf,$import,5,"Summe Bericht",array(200,200,255),$sum_zapati_sestava_array);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
