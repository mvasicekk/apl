<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S520";
$doc_subject = "S520 Report";
$doc_keywords = "S520";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$apl = AplDB::getInstance();

$von = $apl->make_DB_datum($_GET['von']);
$bis = $apl->make_DB_datum($_GET['bis']);
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

$invnrvon = $_GET['invnrvon'];
$invnrbis = $_GET['invnrbis'];

$et = $_GET['et'];
$bET = TRUE;
if($et=="*" || strlen($et)==0)
    $bET=FALSE;
$et = strtr($et, '*', '%');


$reportTyp = $_GET['reporttyp'];

$password = $_GET['password'];

$go = floatval(strtr($_GET['go'], ',','.'));
$gu = floatval(strtr($_GET['gu'], ',','.'));
$premieProzent = floatval(strtr($_GET['p'], ',','.'));

// podej na ty
// p jako boolean
// 
//echo $reportTyp;

if($reportTyp=="nach PersNr")
    $reportTypPersNr = TRUE;
else
    $reportTypPersNr = FALSE;

if($reportTyp=="PersNr Praemien"||$reportTyp=="SumPersExcell"){
    $bPraemien = TRUE;
    $reportTypPersNr = TRUE;
}
else{
    $bPraemien = FALSE;
}

$mitvzkd = $_GET['mitvzkd']=='a'?TRUE:FALSE;;


$fullAccess = testReportPassword("S520",$password,$user,0);

if((!$fullAccess) && ($bPraemien))
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}
    

require_once('S520_xml.php');

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
'startdummy'=> array ("popis"=>"","sirka"=>0.1,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'invnummer'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'anlage_beschreibung'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'persnr_reparatur'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'datum'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'rep_kosten'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'artnr'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'artname'=> array ("popis"=>"","sirka"=>50,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'anzahl'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'preis'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'et_alt'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"C","radek"=>0,"fill"=>0),
'gespreis'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'zuschlag'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'gesamt'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'faktor1'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"BR","radek"=>1,"fill"=>0),
);

$cells_header = 
array(
'startdummy'=> array ("popis"=>"\n","sirka"=>0.1,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'invnummer'=> array ("popis"=>"\ninvnummer","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'anlage_beschreibung'=> array ("popis"=>"\nTyp","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'persnr_reparatur'=> array ("popis"=>"repariert\nvon","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'datum'=> array ("popis"=>"\nDatum","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'rep_kosten'=> array ("popis"=>"Reparatur-\nkosten [Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'artnr'=> array ("popis"=>"\nArtNR","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'artname'=> array ("popis"=>"\nArtName","sirka"=>50,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'anzahl'=> array ("popis"=>"\nStk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'preis'=> array ("popis"=>"Preis\n[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'et_alt'=> array ("popis"=>"gebraucht\n40%","sirka"=>15,"ram"=>'0',"align"=>"C","radek"=>0,"fill"=>1),
'gespreis'=> array ("popis"=>"Ers.Teile\nPreis[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'zuschlag'=> array ("popis"=>"Zuschlag\n10%[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'gesamt'=> array ("popis"=>"Gesamt\n[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'faktor1'=> array ("popis"=>"CZK/\nVzKd(S0011)","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'lf'=> array ("popis"=>"\n","sirka"=>0,"ram"=>'0',"align"=>"BR","radek"=>1,"fill"=>1),
);

$cells_invnr = 
array(
'startdummy'=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'invnummer'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'anlage_beschreibung'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'persnr_ma'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'persnr_reparatur'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'datum'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'rep_kosten'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'artnr'=> array ("popis"=>"","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'artname'=> array ("popis"=>"","sirka"=>50,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
'anzahl'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'preis'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'et_alt'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"C","radek"=>0,"fill"=>0),
'gespreis'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
'zuschlag'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'gesamt'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'faktor1'=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),
'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"BR","radek"=>1,"fill"=>0),
);



$cells_header_invnr = 
array(
//'startdummy'=> array ("popis"=>"\n","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'invnummer'=> array ("popis"=>"\ninvnummer","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'anlage_beschreibung'=> array ("popis"=>"\nTyp","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'persnr_ma'=> array ("popis"=>"gegeben\nvon","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'persnr_reparatur'=> array ("popis"=>"repariert\nvon","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'datum'=> array ("popis"=>"\nDatum","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'rep_kosten'=> array ("popis"=>"Reparatur-\nkosten [Kc]","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'artnr'=> array ("popis"=>"\nArtNR","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'artname'=> array ("popis"=>"\nArtName","sirka"=>50,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
'anzahl'=> array ("popis"=>"\nStk","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'preis'=> array ("popis"=>"Preis\n[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'et_alt'=> array ("popis"=>"gebraucht\n40%","sirka"=>15,"ram"=>'0',"align"=>"C","radek"=>0,"fill"=>1),
'gespreis'=> array ("popis"=>"Ers.Teile\nPreis[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'zuschlag'=> array ("popis"=>"Zuschlag\n10%[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
'gesamt'=> array ("popis"=>"Gesamt\n[Kc]","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
//'faktor1'=> array ("popis"=>"CZK/\nVzKd(S0011)","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
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

$sum_zapati_invnr = array(
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

$sumPremie = 0;

$sum_zapati_repariertVom = array();

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

function zahlavi_maschine($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
    global $sum_zapati_persnr;
    global $sum_zapati_repariertVom;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $pdfobjekt->SetFont("FreeSans", "", 8);


        //dummy
        $pdfobjekt->Cell($cells['startdummy']['sirka'],$vyskaradku,'','T',0,'L',$fill);
        //invnummer
        $pdfobjekt->Cell($cells['invnummer']['sirka'],$vyskaradku,  getValueForNode($childs, 'invnummer'),'T',0,'R',$fill);

        //typ
        $pdfobjekt->Cell($cells['anlage_beschreibung']['sirka'],$vyskaradku,  getValueForNode($childs, 'anlage_beschreibung'),'T',0,'L',$fill);

        //persnr_reparatur
        $pdfobjekt->Cell($cells['persnr_reparatur']['sirka'],$vyskaradku,  getValueForNode($childs, 'persnr_reparatur'),'T',0,'R',$fill);
        
        //datum
        $pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,  getValueForNode($childs, 'datum'),'T',0,'L',$fill);

        //rep_kosten
        $sum_zapati_persnr['rep_kosten'] += intval(getValueForNode($childs, 'rep_kosten'));
        
        $obsah = number_format(getValueForNode($childs, 'rep_kosten'),0,',',' ');
        $pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah  ,'T',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,'','T',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);

        // nascitat minuty oprav
        $persnrRep = getValueForNode($childs, 'persnr_reparatur');
        $sum_zapati_repariertVom[$persnrRep]['repzeit'] += intval(getValueForNode($childs, 'repzeit'));
        $sum_zapati_repariertVom[$persnrRep]['pocet']++;
}

function zahlavi_maschine_invnr($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells_invnr;
    global $sum_zapati_invnr;
    global $sum_zapati_repariertVom;

        $cells = $cells_invnr;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $pdfobjekt->SetFont("FreeSans", "", 8);


        //startdummy
//        $invnr = '';
//        $pdfobjekt->Cell($cells['startdummy']['sirka'],$vyskaradku,$invnr  ,'0',0,'R',$fill);

        //invnummer
        $invnr = getValueForNode($childs, 'invnummer');
        $pdfobjekt->Cell($cells['invnummer']['sirka'],$vyskaradku,$invnr  ,'0',0,'R',$fill);

        //typ
        $pdfobjekt->Cell($cells['anlage_beschreibung']['sirka'],$vyskaradku,  getValueForNode($childs, 'anlage_beschreibung'),'0',0,'L',$fill);

        //persnr_ma
        $pdfobjekt->Cell($cells['persnr_ma']['sirka'],$vyskaradku,  getValueForNode($childs, 'persnr_ma'),'0',0,'R',$fill);

        //persnr_reparatur
        $pdfobjekt->Cell($cells['persnr_reparatur']['sirka'],$vyskaradku,  getValueForNode($childs, 'persnr_reparatur'),'0',0,'R',$fill);
        
        //datum
        $pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,  getValueForNode($childs, 'datum'),'0',0,'L',$fill);

        //rep_kosten
        $repkosten = intval(getValueForNode($childs, 'rep_kosten'));
        $sum_zapati_invnr['rep_kosten'] += $repkosten;
//        echo "<br>invnr=$invnr,repkosten=$repkosten,sumrepkosten=".$sum_zapati_invnr['rep_kosten'];
        
        
        $obsah = number_format(getValueForNode($childs, 'rep_kosten'),0,',',' ');
        $pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah  ,'0',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);

        // nascitat minuty oprav
        $persnrRep = getValueForNode($childs, 'persnr_reparatur');
        $sum_zapati_repariertVom[$persnrRep]['repzeit'] += intval(getValueForNode($childs, 'repzeit'));
        $sum_zapati_repariertVom[$persnrRep]['pocet']++;
}


function zahlavi_et($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
    global $cells_invnr;
    global $sum_zapati_persnr;
    global $sum_zapati_invnr;
    global $reportTypPersNr;

    
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $pdfobjekt->SetFont("FreeSans", "", 8);


        //dummy
        if($reportTypPersNr===TRUE){
        $pdfobjekt->Cell($cells['startdummy']['sirka']
                        +$cells['invnummer']['sirka']
                        +$cells['anlage_beschreibung']['sirka']
                        +$cells['persnr_reparatur']['sirka']
                        +$cells['datum']['sirka']
                        +$cells['rep_kosten']['sirka'],$vyskaradku,'','0',0,'L',$fill);
        }
        else{
        $pdfobjekt->Cell(
//                        $cells['startdummy']['sirka']
                        $cells_invnr['invnummer']['sirka']
                        +$cells_invnr['anlage_beschreibung']['sirka']
                        +$cells_invnr['persnr_ma']['sirka']
                        +$cells_invnr['persnr_reparatur']['sirka']
                        +$cells_invnr['datum']['sirka']
                        +$cells_invnr['rep_kosten']['sirka'],$vyskaradku,'','0',0,'L',$fill);
            
        }

        //artnr
        $pdfobjekt->Cell($cells['artnr']['sirka'],$vyskaradku,  getValueForNode($childs, 'artnr'),'0',0,'L',$fill);

        //artname
        $pdfobjekt->Cell($cells['artname']['sirka'],$vyskaradku,  getValueForNode($childs, 'artname'),'0',0,'L',$fill);

        //anzahl
        $obsah = number_format(getValueForNode($childs, 'anzahl'),0,',',' ');
        $pdfobjekt->Cell($cells['anzahl']['sirka'],$vyskaradku,  $obsah,'0',0,'R',$fill);

        //preis
        $alt = intval(getValueForNode($childs, 'et_alt'));
        $multi = $alt>0?0.4:1;
        $obsah = number_format(floatval(getValueForNode($childs, 'preis'))*$multi,0,',',' ');
        $pdfobjekt->Cell($cells['preis']['sirka'],$vyskaradku,  $obsah,'0',0,'R',$fill);

        //et_alt
        $obsah = $alt>0?'*':'';
        $pdfobjekt->Cell($cells['et_alt']['sirka'],$vyskaradku,$obsah  ,'0',0,'C',$fill);


        //preisgesamt
        $obsah = number_format(floatval(getValueForNode($childs, 'preis'))*$multi*getValueForNode($childs, 'anzahl'),0,',',' ');
        $sum_zapati_persnr['et_kosten'] += floatval(getValueForNode($childs, 'preis'))*$multi*getValueForNode($childs, 'anzahl');
        $sum_zapati_invnr['et_kosten'] += floatval(getValueForNode($childs, 'preis'))*$multi*getValueForNode($childs, 'anzahl');
        $pdfobjekt->Cell($cells['gespreis']['sirka'],$vyskaradku,  $obsah,'0',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childs, 'bemerkung'),'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_person($pdfobjekt,$vyskaradku,$rgb,$sumArray,$childs,$persnr=0)
{
    global $mitvzkd;
    global $cells;
    global $von,$bis,$gu,$go,$premieProzent,$sumPremie,$bPraemien;

    $a = AplDB::getInstance();

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        //popis sumy
        $pdfobjekt->Cell($cells['startdummy']['sirka']+$cells['invnummer']['sirka']+$cells['anlage_beschreibung']['sirka']+$cells['persnr_reparatur']['sirka']+$cells['datum']['sirka'],$vyskaradku,'Summe Person '.$persnr,'TB',0,'L',$fill);

        // rep_kosten
        $obsah=number_format($sumArray['rep_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        //vypln
        $pdfobjekt->Cell($cells['artnr']['sirka']+$cells['artname']['sirka']+$cells['anzahl']['sirka']+$cells['preis']['sirka']+$cells['et_alt']['sirka'],$vyskaradku,'','TB',0,'L',$fill);
        // et_kosten
        $obsah=number_format($sumArray['et_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['gespreis']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // zuschlag
        $obsah=number_format($sumArray['zuschlag'],0,',',' ');
	$pdfobjekt->Cell($cells['zuschlag']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // gesamt
        $obsah=number_format($sumArray['gesamt'],0,',',' ');
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // faktor1
        $obsah='';
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);


        //novy radek
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'R',$fill);

	$vzkdGesamt = 0;
        // S0011 radek s vzkd a koeficientem
        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";
        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0011 )",'B',0,'L',$fill);

        // vzkd
	if($bPraemien)
	    $vzkd = floatval(getValueForNode($childs, 'vzkd_11'));
	else
	    $vzkd = $a->getVzKdProStatNrDatumPersnr('S0011',$von,$bis,$persnr);
	$vzkdGesamt+=$vzkd;
	
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);

	//-----------------------------------------------------------------------------------------
        // S0051 radek s vzkd a koeficientem
        //popis sumy
		if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0051 )",'B',0,'L',$fill);

        // vzkd
	if($bPraemien)
	    $vzkd = floatval(getValueForNode($childs, 'vzkd_51'));
	else
	    $vzkd = $a->getVzKdProStatNrDatumPersnr('S0051',$von,$bis,$persnr);
	
	$vzkdGesamt+=$vzkd;
	
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);

	//-----------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------
        // gesamt radek s vzkd a koeficientem
        //popis sumy
	global $von,$bis;
	
	if($mitvzkd) 
	    $o = "Summe VzAby ( $von - $bis )";
	else
	    $o = "";

	
	$pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                ,
                $vyskaradku,
                "$o",'B',0,'L',$fill);

	$vzAby = $a->getVzAbyProPersNrDatum($von, $bis, $persnr);
	$obsah = number_format($vzAby, 0, ',', ' ');
	if(!$mitvzkd) $obsah="";
        $pdfobjekt->Cell(
                $cells['rep_kosten']['sirka']
                ,
                $vyskaradku,
                $obsah,'B',0,'R',$fill);

	if ($bPraemien) {
	$pdfobjekt->Cell(
		$cells['artnr']['sirka']
		, $vyskaradku, 'Premie10%', 'B', 0, 'L', $fill);
	} else {
	$pdfobjekt->Cell(
		$cells['artnr']['sirka']
		, $vyskaradku, '', 'B', 0, 'L', $fill);
	}

    if($vzkdGesamt!=0)
            $faktor1 = $sumArray['gesamt']/$vzkdGesamt;
        else
            $faktor1 = 0;
	
	$premie=0;
	if($faktor1<=$gu){
	    $premie = round($vzkdGesamt*$premieProzent/100);
	}
	if($faktor1>=$go){
	    $premie = -round($vzkdGesamt*$premieProzent/100);
	}
	    
	$sumPremie+=$premie;
	$r=40;
	$obsah = number_format($premie, 0, ',', ' ');
	if(!$bPraemien) $obsah = "";
        $pdfobjekt->Cell(
                $cells['artname']['sirka']-$r
                ,
                $vyskaradku,
                $obsah,'B',0,'R',$fill);

	$pdfobjekt->Cell(
		$r
                ,
                $vyskaradku,
                '','B',0,'L',$fill);

	        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell(
		+$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0011+S0051 )",'B',0,'L',$fill);

        // vzkd
//        $vzkd = $a->getVzKdProStatNrDatumPersnr('S0051',$von,$bis,$persnr);
//	$vzkdGesamt+=$vzkd;
	
        $obsah=number_format($vzkdGesamt,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkdGesamt!=0)
            $faktor1 = $sumArray['gesamt']/$vzkdGesamt;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

	//-----------------------------------------------------------------------------------------

	//
        //novy radek
        $pdfobjekt->Cell(0,$vyskaradku,'','B',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_invnr($pdfobjekt,$vyskaradku,$rgb,$sumArray)
{
    global $cells_invnr;
    global $von,$bis;

    $cells = $cells_invnr;
    $a = AplDB::getInstance();

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        //popis sumy
        $pdfobjekt->Cell(
                $cells['invnummer']['sirka']
//                +$cells['startdummy']['sirka']+
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_ma']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka'],
                $vyskaradku,'Summe Invnr ','TB',0,'L',$fill);

        // rep_kosten
        $obsah=number_format($sumArray['rep_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        //vypln
        $pdfobjekt->Cell(
                $cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka'],
                $vyskaradku,'','TB',0,'L',$fill);
        // et_kosten
        $obsah=number_format($sumArray['et_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['gespreis']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // zuschlag
        $obsah=number_format($sumArray['zuschlag'],0,',',' ');
	$pdfobjekt->Cell($cells['zuschlag']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // gesamt
        $obsah=number_format($sumArray['gesamt'],0,',',' ');
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // faktor1
        $obsah='';
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'TB',1,'R',$fill);


        //novy radek
        //$pdfobjekt->Cell(0,$vyskaradku,'x','1',1,'R',0);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function zapati_sestava_repariertVom($pdfobjekt,$vyskaradku,$rgb,$sumArray)
{
    global $cells;
    global $von,$bis;
    $sumRow = array();

    $a = AplDB::getInstance();

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        //repariertvom PersNr
        $pdfobjekt->Cell(25,$vyskaradku,'repariert PersNr','B',0,'R',$fill);

        //repariertvom Name

	$pdfobjekt->Cell(50,$vyskaradku,'Name','B',0,'L',$fill);

        //repariertvom repzeit
        $pdfobjekt->Cell(30,$vyskaradku,'Reparaturzeit ges.[min]','B',0,'R',$fill);

        //repariertvom pocet
	$pdfobjekt->Cell(30,$vyskaradku,'Reparaturenanzahl','B',0,'R',$fill);

        //repariertvom doba 1 opravy
	$pdfobjekt->Cell(30,$vyskaradku,'Reparaturzeit [min]','B',0,'R',$fill);

	//novy radek
        $pdfobjekt->Cell(0,$vyskaradku,'','B',1,'R',$fill);

        foreach ($sumArray as $persnr => $values) {
	    $fill = 0;

	    //repariertvom PersNr
	    $pdfobjekt->Cell(25, $vyskaradku, $persnr, 'B', 0, 'R', $fill);

	    //repariertvom Name
	    $nameA = $a->getNameVorname($persnr);
	    $name = $nameA['name'] . ' ' . $nameA['vorname'];
	    $pdfobjekt->Cell(50, $vyskaradku, $name, 'B', 0, 'L', $fill);

	    //repariertvom repzeit
	    $pdfobjekt->Cell(30, $vyskaradku, $values['repzeit'], 'B', 0, 'R', $fill);
	    $sumRow['repzeit'] += intval($values['repzeit']);

	    //repariertvom pocet
	    $pdfobjekt->Cell(30, $vyskaradku, $values['pocet'], 'B', 0, 'R', $fill);
	    $sumRow['pocet'] += intval($values['pocet']);

	    //repariertvom doba 1 opravy
	    $repZeit = $values['pocet'] != 0 ? $values['repzeit'] / $values['pocet'] : 0;
	    $repZeit = number_format($repZeit, 1, ',', ' ');
	    $pdfobjekt->Cell(30, $vyskaradku, $repZeit, 'B', 0, 'R', $fill);

	    //novy radek
	    $pdfobjekt->Cell(0, $vyskaradku, '', 'B', 1, 'R', $fill);
	}
	$fill = 1;
	//zadek se sumou a prumerem
	$pdfobjekt->Cell(75, $vyskaradku, 'SUM', 'B', 0, 'L', $fill);
	//repariertvom repzeit
	$pdfobjekt->Cell(30, $vyskaradku, $sumRow['repzeit'], 'B', 0, 'R', $fill);
	//repariertvom pocet
	$pdfobjekt->Cell(30, $vyskaradku, $sumRow['pocet'], 'B', 0, 'R', $fill);
	//repariertvom doba 1 opravy
	$repZeit = $sumRow['pocet'] != 0 ? $sumRow['repzeit'] / $sumRow['pocet'] : 0;
	$repZeit = number_format($repZeit, 1, ',', ' ');
	$pdfobjekt->Cell(30, $vyskaradku, $repZeit, 'B', 0, 'R', $fill);
	//novy radek
	$pdfobjekt->Cell(0, $vyskaradku, '', 'B', 1, 'R', $fill);

    $pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$sumArray)
{
    global $mitvzkd;
    global $cells;
    global $cells_invnr;
    global $reportTypPersNr;
    global $von,$bis,$persvon,$persbis,$sumPremie,$bPraemien;

    $a = AplDB::getInstance();

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        //popis sumy
        if($reportTypPersNr===TRUE){
            $pdfobjekt->Cell($cells['startdummy']['sirka']+$cells['invnummer']['sirka']+$cells['anlage_beschreibung']['sirka']+$cells['persnr_reparatur']['sirka']+$cells['datum']['sirka'],$vyskaradku,'Summe Gesamt','TB',0,'L',$fill);
        }
        else{
            $pdfobjekt->Cell(
//                    $cells['startdummy']['sirka']
                    +$cells_invnr['invnummer']['sirka']
                    +$cells_invnr['anlage_beschreibung']['sirka']
                    +$cells_invnr['persnr_reparatur']['sirka']
                    +$cells_invnr['persnr_reparatur']['sirka']
                    +$cells_invnr['datum']['sirka'],
                    $vyskaradku,'Summe Gesamt','TB',0,'L',$fill);            
        }

        // rep_kosten
        $obsah=number_format($sumArray['rep_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['rep_kosten']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        //vypln
        $pdfobjekt->Cell($cells['artnr']['sirka']+$cells['artname']['sirka']+$cells['anzahl']['sirka']+$cells['preis']['sirka']+$cells['et_alt']['sirka'],$vyskaradku,'','TB',0,'L',$fill);

        // et_kosten
        $obsah=number_format($sumArray['et_kosten'],0,',',' ');
	$pdfobjekt->Cell($cells['gespreis']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // zuschlag
        $obsah=number_format($sumArray['zuschlag'],0,',',' ');
	$pdfobjekt->Cell($cells['zuschlag']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        // gesamt
        $obsah=number_format($sumArray['gesamt'],0,',',' ');
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        if($reportTypPersNr===TRUE){
        // faktor1
        $obsah='';
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'TB',0,'R',$fill);

        //novy radek
        $pdfobjekt->Cell(0,$vyskaradku,'','TB',1,'R',$fill);

	$vzkdGes = 0;
	//S0011
        // radek s vzkd a koeficientem
        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0011 )",'B',0,'L',$fill);

        // vzkd
        $vzkd = $a->getVzKdProStatNrDatumPersnr('S0011',$von,$bis,NULL,$persvon,$persbis);
	$vzkdGes+=$vzkd;
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);

	//S0051
        // radek s vzkd a koeficientem
        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0051 )",'B',0,'L',$fill);

        // vzkd
        $vzkd = $a->getVzKdProStatNrDatumPersnr('S0051',$von,$bis,NULL,$persvon,$persbis);
	$vzkdGes+=$vzkd;
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);


	//gesamt
        // radek s vzkd a koeficientem
        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=Gesamt )",'B',0,'L',$fill);

        // vzkd
        $vzkd = $vzkdGes;
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);

	//suma premii
        //popis sumy
	if($bPraemien){
        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                ,
                $vyskaradku,
                'Summe','B',0,'L',$fill);
	

        $pdfobjekt->Cell(
		+$cells['artnr']['sirka']
                ,
                $vyskaradku,
                $obsah,'B',0,'L',$fill);
	$r=40;
	
        $pdfobjekt->Cell(
                +$cells['artname']['sirka']-$r
                ,
                $vyskaradku,
                $sumPremie,'B',0,'R',$fill);
        $pdfobjekt->Cell(
                0
                ,
                $vyskaradku,
                '','B',0,'R',$fill);
	    
	}

	$pdfobjekt->Ln();

	$fill=0;
        //S0041
        // radek s vzkd a koeficientem
        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0041 )",'T',0,'L',$fill);

        // vzkd
        $vzkd = $a->getVzKdProStatNrDatumPersnr('S0041',$von,$bis,NULL,$persvon,$persbis);
	$vzkdGes+=$vzkd;
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'T',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'T',1,'R',$fill);

	//S0061
        // radek s vzkd a koeficientem
        //popis sumy
	if($mitvzkd) 
	    $o = "Summe VzKd";
	else
	    $o = "Faktor";

        $pdfobjekt->Cell($cells['startdummy']['sirka']
                +$cells['invnummer']['sirka']
                +$cells['anlage_beschreibung']['sirka']
                +$cells['persnr_reparatur']['sirka']
                +$cells['datum']['sirka']
                +$cells['rep_kosten']['sirka']
                +$cells['artnr']['sirka']
                +$cells['artname']['sirka']
                +$cells['anzahl']['sirka']
                +$cells['preis']['sirka']
                +$cells['et_alt']['sirka']
                +$cells['gespreis']['sirka']
                +$cells['zuschlag']['sirka']
                ,
                $vyskaradku,
                "$o ( StatNr=S0061 )",'B',0,'L',$fill);

        // vzkd
        $vzkd = $a->getVzKdProStatNrDatumPersnr('S0061',$von,$bis,NULL,$persvon,$persbis);
	$vzkdGes+=$vzkd;
        $obsah=number_format($vzkd,0,',',' ');
	if(!$mitvzkd) $obsah="";
	$pdfobjekt->Cell($cells['gesamt']['sirka'],$vyskaradku,$obsah,'B',0,'R',$fill);

        // faktor1

        if($vzkd!=0)
            $faktor1 = $sumArray['gesamt']/$vzkd;
        else
            $faktor1 = 0;

        $obsah=number_format($faktor1,2,',',' ');
	$pdfobjekt->Cell($cells['faktor1']['sirka'],$vyskaradku,$obsah,'B',1,'R',$fill);
	
        }
        else{
            $pdfobjekt->Cell(0,$vyskaradku,'','B',0,'R',$fill);
            $pdfobjekt->Ln();
        }
        
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
				
function test_pageoverflow_noheader($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
                return TRUE;
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
	}
        else
                return FALSE;
}

if($reportTyp=="SumPersExcell"){
    date_default_timezone_set('Europe/Prague');
    /** PHPExcel */
    require_once '../Classes/PHPExcel.php';
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    $user = get_user_pc();
    // Set properties
    $objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy($user)
							 ->setTitle("E520")
							 ->setSubject("E520")
							 ->setDescription("E520")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");
    
    $cells_header = array(
	"persnr"=>array(
	    "popis"=>"persnr    ",
	    ),
	"name"=>array(
	    "popis"=>"Name                               ",
	    ),
	"rep_kosten"=>array(
	    "popis"=>"Reparaturkosten - Arbeit",
	    ),
	"et_kosten"=>array(
	    "popis"=>"Ers.Teile",
	    ),
	"zuschlag"=>array(
	    "popis"=>"Repkosten Zuschlag 10%",
	    ),
	"gesamt"=>array(
	    "popis"=>"Repkosten - Gesamt",
	    ),
	"vzkd_11"=>array(
	    "popis"=>"Vzkd S0011",
	    ),
	"vzkd_51"=>array(
	    "popis"=>"Vzkd S0051",
	    ),
	"vzaby"=>array(
	    "popis"=>"VzAby $von - $bis",
	    ),
	
    );
    
    $sloupec=0;
    $radek=1;
    

    foreach($cells_header as $ch){
	$popis = $ch['popis'];
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $popis);
	$sloupec++;
    }
    
    $sloupecAktual = 'A';
    foreach ($cells_header as $ch) {
	$objPHPExcel->getActiveSheet()->getColumnDimension($sloupecAktual)->setWidth(strlen($ch['popis'])+2);
	$sloupecAktual = chr(ord($sloupecAktual)+1);
    }

    $radek++;


    
    $personen = $domxml->getElementsByTagName("person");
    foreach ($personen as $person) {
	$sloupec=0;
        nuluj_sumy_pole($sum_zapati_persnr);
        $personChilds = $person->childNodes;
	$persnr = getValueForNode($personChilds, 'persnr_ma');
        $maschinen = $person->getElementsByTagName("machine");
        foreach ($maschinen as $maschine) {
            $maschineChilds = $maschine->childNodes;
            $positionen = $maschine->getElementsByTagName("et");
	    $sum_zapati_persnr['rep_kosten'] += intval(getValueForNode($maschineChilds, 'rep_kosten'));
            foreach ($positionen as $et) {
                $etChilds = $et->childNodes;
		$alt = intval(getValueForNode($etChilds, 'et_alt'));
		$multi = $alt>0?0.4:1;
		$sum_zapati_persnr['et_kosten'] += floatval(getValueForNode($etChilds, 'preis'))*$multi*getValueForNode($etChilds, 'anzahl');
            }
        }
        $sum_zapati_persnr['zuschlag'] = ($sum_zapati_persnr['rep_kosten'] + $sum_zapati_persnr['et_kosten']) * 0.1;
        $sum_zapati_persnr['gesamt'] = $sum_zapati_persnr['rep_kosten'] + $sum_zapati_persnr['et_kosten'] + $sum_zapati_persnr['zuschlag'];
	
	
	$popis = getValueForNode($personChilds, 'persnr_ma');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$popis = getValueForNode($personChilds, 'name').' '.getValueForNode($personChilds, 'vorname');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$popis = $sum_zapati_persnr['rep_kosten'];
	$popis = number_format($popis, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$popis = $sum_zapati_persnr['et_kosten'];
	$popis = number_format($popis, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$popis = $sum_zapati_persnr['zuschlag'];
	$popis = number_format($popis, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$popis = $sum_zapati_persnr['gesamt'];
	$popis = number_format($popis, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);

	$popis = getValueForNode($personChilds, 'vzkd_11');
	$popis = number_format($popis, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$popis = getValueForNode($personChilds, 'vzkd_51');
	$popis = number_format($popis, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);

	$vzAby = $apl->getVzAbyProPersNrDatum($von, $bis, $persnr);
	$popis = number_format($vzAby, 0, ',', '');
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec++, $radek, $popis);
	
	$radek++;
//        zapati_person($pdf, 5, array(255, 255, 240), $sum_zapati_persnr, $personChilds,getValueForNode($personChilds, 'persnr_ma'));
    }

    $objPHPExcel->getActiveSheet()->setTitle('E520');
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet	
    $objPHPExcel->setActiveSheetIndex(0);
    // Redirect output to a client’s web browser (Excel5)
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="E520.xls"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;

}
else{
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

if($reportTypPersNr===TRUE)
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S520 Reparaturen nach PersNr - Detail", $params);
else
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S520 Reparaturen nach Invnummer - Detail", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10+5, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER+5);
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
if ($reportTypPersNr === TRUE) {
    $pdf->AddPage();
    pageheader($pdf, $cells_header, 5);
    $personen = $domxml->getElementsByTagName("person");
    foreach ($personen as $person) {
        nuluj_sumy_pole($sum_zapati_persnr);
        $personChilds = $person->childNodes;
        test_pageoverflow($pdf, 5, $cells_header);
        zahlavi_person($pdf, 5, array(255, 255, 250), $personChilds);
        $maschinen = $person->getElementsByTagName("machine");
        foreach ($maschinen as $maschine) {
            $maschineChilds = $maschine->childNodes;
            test_pageoverflow($pdf, 5, $cells_header);
            zahlavi_maschine($pdf, 5, array(255, 255, 255), $maschineChilds);
            $positionen = $maschine->getElementsByTagName("et");
            foreach ($positionen as $et) {
                $etChilds = $et->childNodes;
                test_pageoverflow($pdf, 5, $cells_header);
                zahlavi_et($pdf, 5, array(255, 255, 255), $etChilds);
            }
            // test na vyssi radek nez ve skutecnosti  potrebuju

            test_pageoverflow($pdf, 2, $cells_header);
            $pdf->Cell($cells['startdummy']['sirka'], 1, '', '0', 0, 'L', 0);
            $pdf->Cell(0, 1, '', '0', 1, 'L', 0);
        }
        test_pageoverflow($pdf, 4*5, $cells_header);
        $sum_zapati_persnr['zuschlag'] = ($sum_zapati_persnr['rep_kosten'] + $sum_zapati_persnr['et_kosten']) * 0.1;
        $sum_zapati_persnr['gesamt'] = $sum_zapati_persnr['rep_kosten'] + $sum_zapati_persnr['et_kosten'] + $sum_zapati_persnr['zuschlag'];

        zapati_person($pdf, 5, array(255, 255, 240), $sum_zapati_persnr, $personChilds,getValueForNode($personChilds, 'persnr_ma'));
        $pdf->Ln();
        foreach ($sum_zapati_sestava as $key => $value) {
            $hodnota = $sum_zapati_persnr[$key];
            $sum_zapati_sestava[$key] += $hodnota;
        }
    }
    test_pageoverflow($pdf, 5, $cells_header);
    zapati_sestava($pdf, 5, array(240, 255, 240), $sum_zapati_sestava);
} else {
    $pdf->AddPage();
    pageheader($pdf, $cells_header_invnr, 5);
        $maschinen = $domxml->getElementsByTagName("machine");
        $invnummerOld='';$citac=0;
        foreach ($maschinen as $maschine) {
            $maschineChilds = $maschine->childNodes;
            $invnr = getValueForNode($maschineChilds,'invnummer');
            if($invnr!=$invnummerOld){
                $invnummerOld=$invnr;
                if($citac>0){
                    //doslo ke zmene inventarniho cisla, zobrazim zapati pro invnr
                    test_pageoverflow($pdf, 5, $cells_header_invnr);
                    zapati_invnr($pdf,5,array(240,240,240),$sum_zapati_invnr);
                    foreach ($sum_zapati_sestava as $key => $value) {
                        $hodnota = $sum_zapati_invnr[$key];
                        $sum_zapati_sestava[$key] += $hodnota;
                    }
                    nuluj_sumy_pole($sum_zapati_invnr);
                }
            }
            test_pageoverflow($pdf, 5, $cells_header_invnr);
            zahlavi_maschine_invnr($pdf, 5, array(255, 255, 255), $maschineChilds);
            $positionen = $maschine->getElementsByTagName("et");
            foreach ($positionen as $et) {
                $etChilds = $et->childNodes;
                test_pageoverflow($pdf, 5, $cells_header_invnr);
                zahlavi_et($pdf, 5, array(255, 255, 255), $etChilds);
            }
            $citac++;
            $pdf->Cell(0, 1, '', 'B', 1, 'L', 0);
            
            $sum_zapati_invnr['zuschlag'] = ($sum_zapati_invnr['rep_kosten'] + $sum_zapati_invnr['et_kosten']) * 0.1;
            $sum_zapati_invnr['gesamt'] = $sum_zapati_invnr['rep_kosten'] + $sum_zapati_invnr['et_kosten'] + $sum_zapati_invnr['zuschlag'];
        }
        test_pageoverflow($pdf, 5, $cells_header_invnr);
        zapati_invnr($pdf,5,array(240,240,240),$sum_zapati_invnr);
        foreach ($sum_zapati_sestava as $key => $value) {
            $hodnota = $sum_zapati_invnr[$key];
            $sum_zapati_sestava[$key] += $hodnota;
        }

//
//    }
    test_pageoverflow($pdf, 5, $cells_header);
    zapati_sestava($pdf, 5, array(240, 255, 240), $sum_zapati_sestava);
}
$pdf->Ln();

$sirkaZapatiPersNrVom = (count($sum_zapati_repariertVom)+1)*5;
test_pageoverflow($pdf,$sirkaZapatiPersNrVom,$cells_header);
//sort($sum_zapati_repariertVom);
zapati_sestava_repariertVom($pdf, 5, array(240,240,255), $sum_zapati_repariertVom);

//echo "<pre>";
//var_dump($sum_zapati_repariertVom);
//echo "</pre>";

//Close and output PDF document
$pdf->Output();
    
}
