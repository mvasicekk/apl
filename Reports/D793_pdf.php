<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D793";
$doc_subject = "D793 Report";
$doc_keywords = "D793";

// necham si vygenerovat XML

$parameters=$_GET;

$a = AplDB::getInstance();

$vonF = $_GET['von'];
$bisF = $_GET['bis'];

$von=$a->make_DB_datum($_GET['von']);
$bis=$a->make_DB_datum($_GET['bis']);
$kunde=$_GET['kunde'];
$typ=$_GET['typ'];
$komplexKz=$_GET['komplex'];

if($komplexKz=='*') $komplexKz='';

$bSortKomplex = $typ=="sort Komplex"?TRUE:FALSE;

$rechnung = $_GET['rechnung'];

require_once('D793_xml.php');

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

"teilnr"
=> array ("popis"=>"Teil","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"teillang"
=> array ("popis"=>"Teil-Original","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"teilbez"
=> array ("popis"=>"Bezeichnung","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text1"
=> array ("popis"=>"","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"auss"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preis"
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"betrag"
=> array ("nf"=>array(2,',',' '),"popis"=>"Betrag","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"betragneu"
=> array ("nf"=>array(2,',',' '),"popis"=>"BetragNeu","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preisstk"
=> array ("nf"=>array(2,',',' '),"popis"=>"Preis[Stk]","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preisstkneu"
=> array ("nf"=>array(2,',',' '),"popis"=>"PreisNeu[Stk]","sirka"=>25,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

//"waehrung"
//=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_header =
array(

"teil"
=> array ("popis"=>"Teil","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),
"teillang"
=> array ("popis"=>"Teil-Original","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"bezeichnung"
=> array ("popis"=>"Bezeichnung","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"dienstleistung"
=> array ("popis"=>"Art der Dienstleistung","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"auss"
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gutstk"
=> array ("popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"preis"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"betrag"
=> array ("popis"=>"Betrag","sirka"=>45,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gutbetrag"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"aussbetrag"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gesbetrag"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"dummy"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_import_array = array();
$sum_zapati_teil_array = array();
$sum_zapati_sestava_array = array();
$sum_zapati_tat_sestava = array();
$sum_zapati_regel_array = array();
$tatigkeitenText = array();
$tatigkeitenAbgnr = array();
$tatkz = array();
$tatkzAbgnrArray = array();
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
function pageheader($pdfobjekt,$cells,$childnodes)
{

    $pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);

	$pdfobjekt->SetY(15);
 	$pdfobjekt->Cell(0,5,"Seite ".$pdfobjekt->PageNo()." von {nb}",'0',1,'R',0);

	$pdfobjekt->SetY(35);
	// schovam si pozice xy
	$xOld=$pdfobjekt->GetX();
	$yOld=$pdfobjekt->GetY();

    $vomZeile = getValueForNode($childnodes,"vomname");
    $vomZeile.= "   ".getValueForNode($childnodes,"vomstrasse");
    $vomZeile.= "   ".getValueForNode($childnodes,"vomland");
    $vomZeile.= " - ".getValueForNode($childnodes,"vomplz");
    $vomZeile.= " ".getValueForNode($childnodes,"vomort");

    $pdfobjekt->SetFont("FreeSans", "U", 7);
	$pdfobjekt->Cell(60,3,"$vomZeile",'0',1,'L',0);
    $pdfobjekt->Ln();

	// adresa zakaznika
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"anname1"),'0',1,'L',0);
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"anname2"),'0',1,'L',0);
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"anstrasse"),'0',1,'L',0);
	//$pdfobjekt->Ln();
	$pdfobjekt->Cell(100,5,getValueForNode($childnodes,"anland")."-".getValueForNode($childnodes,"anplz")." ".getValueForNode($childnodes,"anort"),'0',1,'L',0);

	//datum faktury a lieferdatum
	$pdfobjekt->SetLeftMargin(100);

	//$pdfobjekt->SetX($xOld+100);
	$pdfobjekt->SetY($yOld);
	$pdfobjekt->Cell(0,5,"Rechnungsdatum:  ".getValueForNode($childnodes,"rechdatum"),'0',1,'R',0);
	//$pdfobjekt->SetX($xOld+100);
	$pdfobjekt->Cell(0,5,"Lieferdatum :          ".getValueForNode($childnodes,"lieferdatum"),'0',1,'R',0);
	$pdfobjekt->Ln();



	$pdfobjekt->SetLeftMargin(PDF_MARGIN_LEFT+5);
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
    $pdfobjekt->Ln();
    $pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetLineWidth(0.5);
	$pdfobjekt->Cell(50,7,"Rechnung",'TB',0,'L',0);
	$pdfobjekt->Cell(0,7,"Nr.:  ".getValueForNode($childnodes,"auftragsnr"),'TB',1,'R',0);

	// toto zobrazim jen na prvni strance
	if($pdfobjekt->PageNo()==1)
	{
		$pdfobjekt->SetFont("FreeSans", "B", 8);
        $rechtext = getValueForNode($childnodes,"rechtext");
        $lsnr = getValueForNode($childnodes,"origauftrag");
        $renr = getValueForNode($childnodes,"auftragsnr");
        $bestellnr = trim(getValueForNode($childnodes,"bestellnr"));
        
        if(strlen($bestellnr)>0)
            $berechnenText = "Gemäß Ihrer Bestellung Nr. $bestellnr berechnen wir Ihnen : ";
        else
            $berechnenText = "Wir berechnen Ihnen ";

        $textPredZavorkama="";
        $textMeziZavorkama="";
        $textZaZavorkama="";

        // zkusim najit v rechtextu neco v hranate zavorce
        $pozicePromenne = strpos($rechtext,"[");
        $poziceDruheZavorky = false;

        if($pozicePromenne){
            // vytahnu si obsah hranate zavorky
            $poziceDruheZavorky = strpos($rechtext,"]");
            if($poziceDruheZavorky){
                $textMeziZavorkama = substr($rechtext, $pozicePromenne+1, $poziceDruheZavorky-$pozicePromenne-1);
            }
        }

        if($pozicePromenne)
            $textPredZavorkama = substr($rechtext,0,$pozicePromenne);
        else
            $textPredZavorkama = $rechtext;

        if($poziceDruheZavorky)
            $textZaZavorkama = substr($rechtext,$poziceDruheZavorky+1);

        if($textMeziZavorkama=="ls-nr") $textMeziZavorkama = $lsnr;
        if($textMeziZavorkama=="re-nr") $textMeziZavorkama = $renr;

        $berechnenText .= $textPredZavorkama." ".$textMeziZavorkama." ".$textZaZavorkama;
		$pdfobjekt->Cell(0,7,$berechnenText,'0',1,'L',0);
	}
	else
	{
		$pdfobjekt->SetFont("FreeSans", "B", 8);
		$pdfobjekt->Cell(0,3,"",'0',1,'L',0);
	}

	$pdfobjekt->SetLineWidth(0.2);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$cell['fill']);
	}
}

// funkce pro vykresleni hlavicky na kazde strance
function pageheader_simple($pdfobjekt,$cells,$childnodes)
{
global $cells;    

	$pdfobjekt->SetFillColor(255,255,200,1);
	$pdfobjekt->SetLineWidth(0.2);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
        $fill = 1;
//	foreach($cells as $cell)
//	{
//		$pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$cell['fill']);
//	}
        
        $cell = $cells['teilnr'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);
        
        $cell = $cells['teillang'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);

        $cell = $cells['teilbez'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);

        $cell = $cells['text1'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);

        $cell = $cells['betrag'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);

        $cell = $cells['betragneu'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);

        $cell = $cells['preisstk'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);

        $cell = $cells['preisstkneu'];
        $pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$fill);
        
        $pdfobjekt->Ln();

}

// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
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
        if(($nodename=="auss")&&($cellobsah==0))
            $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,"",$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
        else
            $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

		//$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
function zapati_fremdpos($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(0,$vyskaradku,"",'T',1,'L',1);
}


function zapati_rechnung($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumarray)
{

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(20+45+45+10+12,$vyskaradku,"",'0',0,'R',0);
    $mwst = getValueForNode($childNodes,"mwst");
    $wahr = getValueForNode($childNodes,"wahr");
    if($mwst>0)
        $pdfobjekt->Cell(20,$vyskaradku,"Netto:",'T',0,'L',1);
    else
        $pdfobjekt->Cell(20,$vyskaradku,"Summe:",'B',0,'L',1);


	$obsah=number_format($sumarray['betrag'],2,',',' ');
        $obsahG=number_format($sumarray['gutbetrag'],2,',',' ');
        $obsahA=number_format($sumarray['aussbetrag'],2,',',' ');
        $obsahS=number_format($sumarray['gesbetrag'],2,',',' ');

    if($mwst>0){
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'T',0,'R',1);
        $pdfobjekt->Cell(20,$vyskaradku,$obsahG,'T',0,'R',1);
        $pdfobjekt->Cell(20,$vyskaradku,$obsahA,'T',0,'R',1);
        $pdfobjekt->Cell(20,$vyskaradku,$obsahS,'T',0,'R',1);
    }
    else{
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',1);
        $pdfobjekt->Cell(20,$vyskaradku,$obsahG,'B',0,'R',1);
        $pdfobjekt->Cell(20,$vyskaradku,$obsahA,'B',0,'R',1);
        $pdfobjekt->Cell(20,$vyskaradku,$obsahS,'B',0,'R',1);
    }

    if($mwst>0)
        $pdfobjekt->Cell(0,$vyskaradku,$wahr,'T',1,'R',1);
    else
        $pdfobjekt->Cell(0,$vyskaradku,$wahr,'B',1,'R',1);

    if($mwst>0){
        $pdfobjekt->Cell(20+45+45+10+12,$vyskaradku,"",'0',0,'R',1);
        $mwstValue = $mwst/100 * $sumarray['betrag'];
        $mwstValue = round($mwstValue,2);
        $pdfobjekt->Cell(20,$vyskaradku,"+ MWSt ".$mwst." %",'B',0,'L',1);
        $obsah=number_format($mwstValue,2,',',' ');
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',1);
        $pdfobjekt->Cell(0,$vyskaradku,$wahr,'B',1,'R',1);

        $pdfobjekt->Cell(20+45+45+10+12,$vyskaradku,"",'0',0,'R',1);
        $brutto = $sumarray['betrag']+$mwstValue;
        $brutto = round($brutto,2);
        $pdfobjekt->Cell(20,$vyskaradku,"Brutto",'B',0,'L',1);
        $obsah=number_format($brutto,2,',',' ');
        $pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',1);
        $pdfobjekt->Cell(0,$vyskaradku,$wahr,'B',1,'R',1);

    }


}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//function zapati_rechnung($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumarray)
//{
//
//	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$pdfobjekt->Cell(20+45+45+12+12,$vyskaradku,"",'0',0,'R',1);
//	$pdfobjekt->Cell(20,$vyskaradku,"Summe:",'TB',0,'R',1);
//	$obsah=number_format($sumarray['betrag'],2,',',' ');
//	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'TB',0,'R',1);
//	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"waehrung"),'TB',1,'R',1);
//}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$childNodes,$pole,$fracht,$sumTatArray,$tatigkeiten)
{
    $sirka1 = 29;
    global $cells;
    global $tatigkeitenAbgnr;
    global $tatigkeitenText;
    global $tatkzAbgnrArray;
    global $sum_zapati_regel_array;

    $sumRegelArray = array();
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
        $reg = $pole['betrag']['REGEL'];
        $regoF = $reg-$fracht;

        $ma = $pole['betrag']['MA'];

        $podil = 0;
        if($reg!=0)
            $podil = $ma/$reg*100;

	$obsahPodil = number_format($podil,2,',',' ');

        $podiloF = 0;
        if($regoF!=0)
            $podiloF = $ma/$regoF*100;

	$obsahPodiloF = number_format($podiloF,2,',',' ');

        $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']+
                            $cells['teilbez']['sirka']
//                            +$cells['text1']['sirka']+
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
                            ,$vyskaradku,"Berichtsumme mit Fracht",'1',0,'L',$fill);

        //REGEL
        $obsah = number_format($reg,2,',',' ');
        $pdfobjekt->Cell(
//                            $cells['teilnr']['sirka']+
//                            $cells['teilbez']['sirka']
                            $sirka1
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
                            ,$vyskaradku,"REGEL mit Fracht:",'LBT',0,'L',$fill);
        $pdfobjekt->Cell(
//                            $cells['teilnr']['sirka']+
//                            $cells['teilbez']['sirka']
                            $cells['text1']['sirka']-$sirka1
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
                            ,$vyskaradku,"$obsah",'RBT',0,'R',$fill);

        //MA
        $obsah = "";//number_format($ma,2,',',' ');
        $pdfobjekt->Ln();

        // ohne Fracht
                $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,"Berichtsumme ohne Fracht",'1',0,'L',$fill);

        //REGEL
        $obsah = number_format($regoF,2,',',' ');
        $pdfobjekt->Cell(
                            $sirka1
                            ,$vyskaradku,"REGEL ohne Fracht:",'TBL',0,'L',$fill);
        $pdfobjekt->Cell(
                            $cells['text1']['sirka']-$sirka1
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        //MA
        $obsah = number_format($ma,2,',',' ');
        $pdfobjekt->Cell(
                            $sirka1
                            ,$vyskaradku,"MA:",'TBL',0,'L',$fill);

        $pdfobjekt->Cell(
                            $cells['auss']['sirka']+
                            $cells['stk']['sirka']+
                            $cells['preis']['sirka']-$sirka1
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        $pdfobjekt->Cell($cells['text1']['sirka']*3/4,$vyskaradku, "MA in % der Regel",'LTB',0,'L',$fill);
        $pdfobjekt->Cell($cells['text1']['sirka']/4,$vyskaradku, $obsahPodiloF,'TBR',0,'R',$fill);

        $pdfobjekt->Ln();
        $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,"Berichtsumme REGEL o.Fracht+MA",'1',0,'L',$fill);

        $obsah = number_format($regoF+$ma,2,',',' ');
        $pdfobjekt->Cell(
                            $sirka1
                            ,$vyskaradku,"",'TBL',0,'L',$fill);
        $pdfobjekt->Cell(
                            $cells['text1']['sirka']-$sirka1
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);
        
        
        
        $pdfobjekt->Ln();
        $pdfobjekt->Cell(0,$vyskaradku, 'Summe Bericht NEU','T',1,'L',0);
        //-----------------------------------------------------------------------------------------
        // NEU
        $reg = $pole['betragneu']['REGEL'];
        $regoF = $reg-$fracht;

        $ma = $pole['betragneu']['MA'];

        $podil = 0;
        if($reg!=0)
            $podil = $ma/$reg*100;

	$obsahPodil = number_format($podil,2,',',' ');

        $podiloF = 0;
        if($regoF!=0)
            $podiloF = $ma/$regoF*100;

	$obsahPodiloF = number_format($podiloF,2,',',' ');

        $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']+
                            $cells['teilbez']['sirka']
//                            +$cells['text1']['sirka']+
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
                            ,$vyskaradku,"Berichtsumme mit Fracht",'1',0,'L',$fill);

        //REGEL
        $obsah = number_format($reg,2,',',' ');
        $pdfobjekt->Cell(
//                            $cells['teilnr']['sirka']+
//                            $cells['teilbez']['sirka']
                            $sirka1
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
                            ,$vyskaradku,"REGEL mit Fracht:",'LBT',0,'L',$fill);
        $pdfobjekt->Cell(
//                            $cells['teilnr']['sirka']+
//                            $cells['teilbez']['sirka']
                            $cells['text1']['sirka']-$sirka1
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
                            ,$vyskaradku,"$obsah",'RBT',0,'R',$fill);

        //MA
        $obsah = "";//number_format($ma,2,',',' ');
        $pdfobjekt->Ln();

        // ohne Fracht
                $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,"Berichtsumme ohne Fracht",'1',0,'L',$fill);

        //REGEL
        $obsah = number_format($regoF,2,',',' ');
        $pdfobjekt->Cell(
                            $sirka1
                            ,$vyskaradku,"REGEL ohne Fracht:",'TBL',0,'L',$fill);
        $pdfobjekt->Cell(
                            $cells['text1']['sirka']-$sirka1
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        //MA
        $obsah = number_format($ma,2,',',' ');
        $pdfobjekt->Cell(
                            $sirka1
                            ,$vyskaradku,"MA:",'TBL',0,'L',$fill);

        $pdfobjekt->Cell(
                            $cells['auss']['sirka']+
                            $cells['stk']['sirka']+
                            $cells['preis']['sirka']-$sirka1
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        $pdfobjekt->Cell($cells['text1']['sirka']*3/4,$vyskaradku, "MA in % der Regel",'LTB',0,'L',$fill);
        $pdfobjekt->Cell($cells['text1']['sirka']/4,$vyskaradku, $obsahPodiloF,'TBR',0,'R',$fill);
        
        $pdfobjekt->Ln();
        $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,"BerichtsummeNEU REGEL o.Fracht+MA",'1',0,'L',$fill);

        $obsah = number_format($regoF+$ma,2,',',' ');
        $pdfobjekt->Cell(
                            $sirka1
                            ,$vyskaradku,"",'TBL',0,'L',$fill);
        $pdfobjekt->Cell(
                            $cells['text1']['sirka']-$sirka1
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

//-------------------------------------------------------------------------------------------------------
        $pdfobjekt->AddPage();

        $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,"Taetigkeit",'1',0,'L',$fill);
        $regelKz='R';
        $obsah = "Betrag $regelKz [EUR]";
        $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        
        $regelKz='R+';
        $obsah = "Betrag $regelKz [EUR]";
        $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);

        
        $regelKz='R+R+';
        $obsah = "$regelKz in %";
        $pdfobjekt->Cell(
                            15
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        //------------------------------------------------------------------
        
        $regelKz='MA';
        $obsah = "Betrag $regelKz [EUR]";
        $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        $obsah = "$regelKz in %";
        $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        //------------------------------------------------------------------
        $regelKz='F';
        $obsah = "Betrag $regelKz [EUR]";
        $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        $obsah = "$regelKz in %";
        $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        //------------------------------------------------------------------
        $regelKz='So';
        $obsah = "Betrag $regelKz [EUR]";
        $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        $obsah = "$regelKz in %";
        $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
        //------------------------------------------------------------------
        $pdfobjekt->Cell(
                            0
                            ,$vyskaradku,"TaetNr",'1',1,'R',$fill);
        $totalSum = 0;
        //setridit $tatigkeiten podle abgnr
        asort($tatigkeitenAbgnr);

        $tatkzArray = array();

        // zjistim si napred sumy
        $sumRegelArray1 = array();
        foreach ($tatigkeitenAbgnr as $key=>$abgnr){
            $regelKzArray = array("R","R+","MA","F","So");
            foreach($regelKzArray as $regelKz){
                $sumRegelArray1[$regelKz]+=$sum_zapati_regel_array[$key][$regelKz];
            }
        }
        
        $sum100Prozent = $sumRegelArray1['R']+$sumRegelArray1['R+'];
        
        foreach ($tatigkeitenAbgnr as $key=>$abgnr){
            $beschreibung = $key.' - '.$tatigkeitenText[$key];
            $value = $sumTatArray[$key];
//            echo "$key";
//            var_dump($tatkzAbgnrArray[$key]);
            $abgnrArray = $tatkzAbgnrArray[$key];
            ksort($abgnrArray);
//            var_dump($abgnrArray);
            $abgnrText="";
            foreach($abgnrArray as $a=>$hodnota){
                $abgnrText.="$a, ";
            }
            $abgnrText = substr($abgnrText, 0, strlen($abgnrText)-2);
//            echo "$abgnrText<br>";
            
            $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,$beschreibung,'1',0,'L',0);
        
//            $betrag = number_format($value,2,',',' ');
//            $pdfobjekt->Cell(
//                            25
//                            ,$vyskaradku,$betrag,'1',0,'R',0);
            $sumRRplus = 0;
            // R
            $regelKz = 'R';
            $sumRegelArray[$regelKz]+=$sum_zapati_regel_array[$key][$regelKz];
            $sumRRplus+=$sum_zapati_regel_array[$key][$regelKz];
            if($sum_zapati_regel_array[$key][$regelKz]!=0)
                $obsah = number_format($sum_zapati_regel_array[$key][$regelKz],2,',',' ');
            else
                $obsah = '';
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);

            // R+
            $regelKz = 'R+';
            $sumRegelArray[$regelKz]+=$sum_zapati_regel_array[$key][$regelKz];
            $sumRRplus+=$sum_zapati_regel_array[$key][$regelKz];
            if($sum_zapati_regel_array[$key][$regelKz]!=0)
                $obsah = number_format($sum_zapati_regel_array[$key][$regelKz],2,',',' ');
            else
                $obsah = '';
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            
            $obsah=0;
            if($sum100Prozent!=0)
                $obsah = round($sumRRplus/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            
            $pdfobjekt->Cell(
                            15
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            //
            // MA
            $regelKz = 'MA';
            $sumRegelArray[$regelKz]+=$sum_zapati_regel_array[$key][$regelKz];
            if($sum_zapati_regel_array[$key][$regelKz]!=0)
                $obsah = number_format($sum_zapati_regel_array[$key][$regelKz],2,',',' ');
            else
                $obsah = '';
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sum_zapati_regel_array[$key][$regelKz]/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            // F
            $regelKz = 'F';
            $sumRegelArray[$regelKz]+=$sum_zapati_regel_array[$key][$regelKz];
            if($sum_zapati_regel_array[$key][$regelKz]!=0)
                $obsah = number_format($sum_zapati_regel_array[$key][$regelKz],2,',',' ');
            else
                $obsah = '';
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sum_zapati_regel_array[$key][$regelKz]/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            // So
            $regelKz = 'So';
            $sumRegelArray[$regelKz]+=$sum_zapati_regel_array[$key][$regelKz];
            if($sum_zapati_regel_array[$key][$regelKz]!=0)
                $obsah = number_format($sum_zapati_regel_array[$key][$regelKz],2,',',' ');
            else
                $obsah = '';
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sum_zapati_regel_array[$key][$regelKz]/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------

            $pdfobjekt->Cell(
                            0
                            ,$vyskaradku,$abgnrText,'1',0,'R',0);
            
            
            $totalSum += $value;
            $pdfobjekt->Ln();
        }
            $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teilbez']['sirka']
                            ,$vyskaradku,"Summe Taetigkeiten",'1',0,'L',0);

            $sumRRplus = 0;
            $regelKz='R';
            $obsah = number_format($sumRegelArray[$regelKz],2,',',' ');
            $sumRRplus+=$sumRegelArray[$regelKz];
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $regelKz='R+';
            $obsah = number_format($sumRegelArray[$regelKz],2,',',' ');
            $sumRRplus+=$sumRegelArray[$regelKz];
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sumRRplus/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            15
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            $regelKz='MA';
            $obsah = number_format($sumRegelArray[$regelKz],2,',',' ');
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sumRegelArray[$regelKz]/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            $regelKz='F';
            $obsah = number_format($sumRegelArray[$regelKz],2,',',' ');
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sumRegelArray[$regelKz]/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            $regelKz='So';
            $obsah = number_format($sumRegelArray[$regelKz],2,',',' ');
            $pdfobjekt->Cell(
                            25
                            ,$vyskaradku,$obsah,'1',0,'R',0);

            $betrag = number_format($totalSum,2,',',' ');
            $obsah=0;
            if($sum100Prozent!=0)
            $obsah = round($sumRegelArray[$regelKz]/$sum100Prozent*100,2);
            $obsah = number_format($obsah,2,',',' ');
            $pdfobjekt->Cell(
                            13
                            ,$vyskaradku,$obsah,'1',0,'R',$fill);
            //------------------------------------------------------------------
            $pdfobjekt->Cell(
                            0
                            ,$vyskaradku,$betrag,'1',1,'R',0);


        $pdfobjekt->Ln();

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_fremdposition($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(20,$vyskaradku,"Best.Nr.:",'0',0,'L',1);
	$fremdauftr = getValueForNode($childNodes,"fremdauftrnr");
	$pdfobjekt->Cell(45,$vyskaradku,$fremdauftr,'0',0,'L',1);
	$fremdpos = getValueForNode($childNodes,"fremdposnr");
	$pdfobjekt->Cell(45,$vyskaradku,"Pos.:".$fremdpos,'0',1,'L',1);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	$pdfobjekt->Cell($cells['teilnr']['sirka'],$vyskaradku,getValueForNode($childNodes,"teilnr"),'BTL',0,'L',$fill);
        $pdfobjekt->Cell($cells['teillang']['sirka'],$vyskaradku,'['.getValueForNode($childNodes,"teillang").']'.' ('.getValueForNode($childNodes,"komplex").')','BT',0,'L',$fill);
        $pdfobjekt->Cell($cells['teilbez']['sirka'],$vyskaradku,getValueForNode($childNodes,"teilbez"),'BTR',0,'L',$fill);
        $pdfobjekt->Cell($cells['text1']['sirka'],$vyskaradku, number_format(floatval(getValueForNode($childNodes,"gew")),2,',',' ').' [kg/Stk]','BTR',0,'L',$fill);

        $pdfobjekt->Ln();
}

function zahlavi_import($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
        $pdfobjekt->Cell($cells['teilnr']['sirka'],$vyskaradku,$obsah,'0',0,'L',$fill);
	$pdfobjekt->Cell($cells['teilbez']['sirka'],$vyskaradku,getValueForNode($childNodes,"import"),'BTL',0,'L',$fill);
        $pdfobjekt->Cell(0,$vyskaradku,"",'BTR',0,'L',$fill);
        $pdfobjekt->Ln();
}

function zapati_import($pdfobjekt,$vyskaradku,$rgb,$childNodes,$pole)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
        $reg = $pole['betrag']['REGEL'];
        $ma = $pole['betrag']['MA'];
        
        $podil = 0;
        if($reg!=0)
            $podil = $ma/$reg*100;

	$obsahPodil = number_format($podil,2,',',' ');
        $pdfobjekt->Cell($cells['teilnr']['sirka'],$vyskaradku,"",'T',0,'L',$fill);
	$pdfobjekt->Cell(
                            $cells['teilbez']['sirka']
                            +$cells['text1']['sirka']+
                            $cells['auss']['sirka']+
                            $cells['stk']['sirka']+
                            $cells['preis']['sirka']
                            ,$vyskaradku,"Auftrag %MA der Regeltat",'T',0,'L',$fill);


//        number_format(getValueForNode($childNodes,"betrag"), 4,',',' ');
        $obsah = "";
        $pdfobjekt->Cell($cells['text1']['sirka'],$vyskaradku,$obsahPodil,'0',0,'R',$fill);
        $pdfobjekt->Ln();
}

function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes,$pole)
{
    $sirka1 = 30/2;
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
        $reg = $pole['betrag']['REGEL'];
        $ma = $pole['betrag']['MA'];

        $regNeu = $pole['betragneu']['REGEL'];
        $maNeu = $pole['betragneu']['MA'];

        $podil = 0;
        if($reg!=0)
            $podil = $ma/$reg*100;

        $podilNeu = 0;
        if($regNeu!=0)
            $podilNeu = $maNeu/$regNeu*100;

	$obsahPodil = number_format($podil,2,',',' ');
        $pdfobjekt->Cell(
                            $cells['teilnr']['sirka']+
                            $cells['teillang']['sirka']

                            ,$vyskaradku,"Teil",'1',0,'L',$fill);

        //REGEL
        $obsah = number_format($reg,2,',',' ');
        $pdfobjekt->Cell(
                            20
                            ,$vyskaradku,"REGEL:",'TBL',0,'L',$fill);
        $pdfobjekt->Cell(
                            15
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        //MA
        $obsah = number_format($ma,2,',',' ');
        $pdfobjekt->Cell(
                            20
                            ,$vyskaradku,"MA:",'TBL',0,'L',$fill);

        $pdfobjekt->Cell(
                        15
                        ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        $pdfobjekt->Cell(40*3/4,$vyskaradku, "MA in % der Regel",'LTB',0,'L',$fill);
        $pdfobjekt->Cell(40/4,$vyskaradku, $obsahPodil,'TBR',0,'R',$fill);
        
        
        //REGELNEU
        $obsah = number_format($regNeu,2,',',' ');
        $pdfobjekt->Cell(
                            20
                            ,$vyskaradku,"REGELNEU:",'TBL',0,'L',$fill);
        $pdfobjekt->Cell(
                            15
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);
        //MANEU
        $obsah = number_format($maNeu,2,',',' ');
        $pdfobjekt->Cell(
                            20
                            ,$vyskaradku,"MANEU:",'TBL',0,'L',$fill);

        $pdfobjekt->Cell(
                            15
                            ,$vyskaradku,"$obsah",'TBR',0,'R',$fill);

        $obsahPodil = number_format($podilNeu,2,',',' ');
        $pdfobjekt->Cell(40*3/4,$vyskaradku, "MA in % der Regel",'LTB',0,'L',$fill);
        $pdfobjekt->Cell(40/4,$vyskaradku, $obsahPodil,'TBR',0,'R',$fill);


        
        $pdfobjekt->Ln();
}

function telo_tat($pdfobjekt,$vyskaradku,$rgb,$childNodes,$ram)
{
    global $cells;
    global $sum_zapati_tat_sestava;
    global $tatigkeiten;
    global $tatigkeitenAbgnr;
    global $tatigkeitenText;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
        $pdfobjekt->Cell($cells['teilnr']['sirka'],$vyskaradku,$obsah,'0',0,'L',$fill);
        $pdfobjekt->Cell($cells['teillang']['sirka'],$vyskaradku,$obsah,'0',0,'L',$fill);
        // cislo operace, pocet
        $obsah = getValueForNode($childNodes,"maxabgnr");
        $pdfobjekt->Cell($cells['teilbez']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);
//        $obsah = "(".getValueForNode($childNodes,"countabgnr").")";
//        $pdfobjekt->Cell(10,$vyskaradku,$obsah,'0',0,'R',$fill);

        $tatkz = getValueForNode($childNodes, 'tatkz');
        $abgnr = getValueForNode($childNodes, 'maxabgnr');

        $obsah = "";


        if($ram===TRUE)
            $ramec = 'T';
        else {
            $ramec = '0';
        }
        
	$pdfobjekt->Cell($cells['text1']['sirka'],$vyskaradku,getValueForNode($childNodes,"text1"),$ramec,0,'L',$fill);
//        $pdfobjekt->Cell(
//                            $cells['auss']['sirka']+
//                            $cells['stk']['sirka']+
//                            $cells['preis']['sirka']
//                            ,$vyskaradku,$obsah,'0',0,'L',$fill);

        $sum_zapati_tat_sestava[$tatkz] += floatval(getValueForNode($childNodes,"betrag"));
        $tatigkeiten[$tatkz]['text']=getValueForNode($childNodes, 'text1');
        $tatigkeiten[$tatkz]['abgnr']=$abgnr;
        $tatigkeitenText[$tatkz] = getValueForNode($childNodes, 'text1');
        $tatigkeitenAbgnr[$tatkz] = $abgnr;

        $obsah = number_format(getValueForNode($childNodes,"betrag"), 4,',',' ');
        $pdfobjekt->Cell($cells['betrag']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        $obsah = number_format(getValueForNode($childNodes,"betragNeu"), 4,',',' ');
        $pdfobjekt->Cell($cells['betragneu']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        $obsah = number_format(getValueForNode($childNodes,"preis"), 4,',',' ');
        $pdfobjekt->Cell($cells['preisstk']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        $obsah = number_format(getValueForNode($childNodes,"preisNeu"), 4,',',' ');
        $pdfobjekt->Cell($cells['preisstkneu']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        
        $pdfobjekt->Ln();
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
		return 1;
	}
	else
		return 0;
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData("", 0, "D793 Umsatzstatistik $vonF - $bisF", $params);
//$pdf->setRechnungFoot(true);
$pdf->setRechnungFoot(false);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT+5, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER+3);
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
pageheader_simple($pdf,$cells_header,NULL);

// zacinam po rechnungach

$z1 = "Abydos s.r.o.                  Hazlov 247                 Tel:+420 354 595 337               DIC / Ust/Id/Nr:CZ25206958";
$z2 = "                                       35132 Hazlov             Fax:+420 354 596 993                                         ";
$z3 = "";

$pdf->setRechnungZeilen($z1, $z2, $z3);

$a = AplDB::getInstance();

$teile = $domxml->getElementsByTagName("teil");
foreach($teile as $teil)
{
    unset($sum_zapati_teil_array);
    $teilChilds = $teil->childNodes;
    if(test_pageoverflow_noheader($pdf, 5)) pageheader_simple($pdf,$cells_header,NULL);
    zahlavi_teil($pdf, 5, array(255,255,240), $teilChilds);
    $importe = $teil->getElementsByTagName("im");
    foreach($importe as $import){
        $imChilds = $import->childNodes;
        if(test_pageoverflow_noheader($pdf, 5)) pageheader_simple($pdf,$cells_header,NULL);
        zahlavi_import($pdf, 5, array(255,255,255), $imChilds);
        $tatkaiten = $import->getElementsByTagName("tat");
        $rma_old = 'REGEL';
        unset($sum_zapati_import_array);
        foreach($tatkaiten as $tat){
            $tatChilds = $tat->childNodes;
            $kusy = $tat->getElementsByTagName("kus");
            foreach($kusy as $kus){
                $kusChilds = $kus->childNodes;
                $rma = getValueForNode($kusChilds, 're_ma');
                $kz = getValueForNode($kusChilds, 'tatkz');
                $maxabgnr = getValueForNode($kusChilds, 'maxabgnr');
                $regelKz = $a->getRegelKZForAbgNr($maxabgnr);
                $betrag = floatval(getValueForNode($kusChilds, 'betrag'));
                $betragNeu = floatval(getValueForNode($kusChilds, 'betragNeu'));
                if($kz=='F') $sumFracht += $betrag;
                $sum_zapati_import_array['betrag'][$rma] += $betrag;
                $sum_zapati_import_array['betragneu'][$rma] += $betragNeu;
                $sum_zapati_regel_array[$kz][$regelKz] += $betrag;
                if($rma!=$rma_old){
                    $ram = TRUE;
                    $rma_old = $rma;
                }
                else
                    $ram = FALSE;
                if(test_pageoverflow_noheader($pdf, 3)) pageheader_simple($pdf,$cells_header,NULL);
                telo_tat($pdf, 3, array(255,255,255), $kusChilds,$ram);
                $tatkzAbgnrArray[$kz][$maxabgnr]++;
            }
        }
        $sum_zapati_teil_array['betrag']['REGEL'] += $sum_zapati_import_array['betrag']['REGEL'];
        $sum_zapati_teil_array['betrag']['MA'] += $sum_zapati_import_array['betrag']['MA'];
        $sum_zapati_teil_array['betragneu']['REGEL'] += $sum_zapati_import_array['betragneu']['REGEL'];
        $sum_zapati_teil_array['betragneu']['MA'] += $sum_zapati_import_array['betragneu']['MA'];

// IMPORT je cislo FAKTURY - jsem dylyna
//
//        zapati_import($pdf, 5, array(240,250,240), $imChilds, $sum_zapati_import_array);
    }
    if(test_pageoverflow_noheader($pdf, 5)) pageheader_simple($pdf,$cells_header,NULL);
    zapati_teil($pdf, 5, array(240,250,240), $imChilds, $sum_zapati_teil_array);

    $sum_zapati_sestava_array['betrag']['REGEL'] += $sum_zapati_teil_array['betrag']['REGEL'];
    $sum_zapati_sestava_array['betrag']['MA'] += $sum_zapati_teil_array['betrag']['MA'];

    $sum_zapati_sestava_array['betragneu']['REGEL'] += $sum_zapati_teil_array['betragneu']['REGEL'];
    $sum_zapati_sestava_array['betragneu']['MA'] += $sum_zapati_teil_array['betragneu']['MA'];

}

    $pdf->Ln();
    if(test_pageoverflow_noheader($pdf, 2*5)) pageheader_simple($pdf,$cells_header,NULL);
    zapati_sestava($pdf, 5, array(240,240,255), $imChilds, $sum_zapati_sestava_array,$sumFracht,$sum_zapati_tat_sestava,$tatigkeiten);

//Close and output PDF document

//echo "<pre>";
//var_dump($sum_zapati_regel_array);
//echo "</pre>";


//var_dump($sum_zapati_tat_sestava);
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
