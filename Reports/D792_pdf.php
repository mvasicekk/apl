<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "D792";
$doc_subject = "D792 Report";
$doc_keywords = "D792";

// necham si vygenerovat XML

$parameters=$_GET;

$export=$_GET['auftragsnr'];

require_once('D792_xml.php');



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
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"teilbez"
=> array ("popis"=>"","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text1"
=> array ("popis"=>"","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"auss"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"preis"
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"betrag"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gutstk"
=> array ("popis"=>"GutBetrag","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gutbetrag"
=> array ("popis"=>"GutBetrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"aussbetrag"
=> array ("popis"=>"AussBetrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gesbetrag"
=> array ("popis"=>"GesBetrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

//"waehrung"
//=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_header =
array(

"teil"
=> array ("popis"=>"Teil","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"bezeichnung"
=> array ("popis"=>"Bezeichnung","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"dienstleistung"
=> array ("popis"=>"Art der Dienstleistung","sirka"=>45,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"auss"
=> array ("popis"=>"Ausschuss","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gutstk"
=> array ("popis"=>"GutStk","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"preis"
=> array ("popis"=>"Preis","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"betrag"
=> array ("popis"=>"Betrag","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gutbetrag"
=> array ("popis"=>"GutBetrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"aussbetrag"
=> array ("popis"=>"AussBetrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"mastk"
=> array ("popis"=>"MA Stk","sirka"=>12,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"mabetrag"
=> array ("popis"=>"MA Betrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"gesbetrag"
=> array ("popis"=>"GesBetrag","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"dummy"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),

);



$sum_zapati_rechnung_array = array(	
                                                                "preis"=>0,
								"betrag"=>0,
                                                                "gutbetrag"=>0,
                                                                "aussbetrag"=>0,
                                                                "mabetrag"=>0,
								);
global $sum_zapati_rechnung_array;

$sum_zapati_teil_array= array(
                                                                "preis"=>0,
								"betrag"=>0,
                                                                "gutbetrag"=>0,
                                                                "aussbetrag"=>0,
                                                                "mabetrag"=>0,
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
	$pdfobjekt->SetFillColor(255,255,200,1);
	$pdfobjekt->SetLineWidth(0.2);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell['sirka'],5,$cell['popis'],$cell['ram'],$cell['radek'],$cell['align'],$cell['fill']);
	}
}

// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist,$gutStk,$aussStk,$gutpreis,$ausspreis)
{
    global $sum_zapati_teil_array;
//preis
//betrag
//gutbetrag
//aussbetrag
//mabetrag

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

        $reihe = getValueForNode($nodelist, 'reihe');
        $isMA = ($reihe>=2000)&&($reihe!=2095)?TRUE:FALSE;

        //teilnr
        $nodename = 'teilnr';
        $obsah = getValueForNode($nodelist, $nodename);
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //teilbez
        $nodename = 'teilbez';
        $obsah = getValueForNode($nodelist, $nodename);
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //text1
        $nodename = 'text1';
        $obsah = getValueForNode($nodelist, $nodename);
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //auss
        $nodename = 'auss';
        $obsah = intval(getValueForNode($nodelist, $nodename));
        if($obsah==0) $obsah = "";
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //stk
        $nodename = 'stk';
        $obsah = "";
        if(!$isMA) $obsah = getValueForNode($nodelist, $nodename);
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //preis
        $nodename = 'preis';
        $obsah = "";
        if(!$isMA) {
            $obsah = number_format (getValueForNode($nodelist, $nodename),4,',',' ');
            $sum_zapati_teil_array['preis']+=floatval(getValueForNode($nodelist, $nodename));
        }
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //betrag
        $nodename = 'betrag';
        $obsah = "";
        if(!$isMA) {
            $obsah = number_format ((intval (getValueForNode($nodelist, 'auss1'))+intval (getValueForNode($nodelist, 'stk')))*floatval (getValueForNode($nodelist, 'preis')),2,',',' ');
            $sum_zapati_teil_array['betrag']+=(intval (getValueForNode($nodelist, 'auss1'))+intval (getValueForNode($nodelist, 'stk')))*floatval (getValueForNode($nodelist, 'preis'));
        }
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        // zobrazit jen polozky, ktre nejsou MA
        //gutbetrag
        $nodename = 'gutbetrag';
        $obsah = "";
        if(!$isMA) {
            $obsah = number_format ($gutStk*floatval (getValueForNode($nodelist, 'preis')),2,',',' ');
            $sum_zapati_teil_array['gutbetrag']+=$gutStk*floatval (getValueForNode($nodelist, 'preis'));
        }
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);


        //aussbetrag
        $nodename = 'aussbetrag';
        $obsah = "";
        $auss = intval(getValueForNode($nodelist, 'auss'));
        if(!$isMA){
            $obsah = number_format ($auss*$ausspreis,2,',',' ');
            $sum_zapati_teil_array['aussbetrag']+=$auss*$ausspreis;
        }
        if($auss==0) $obsah = '';
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //MA Stk
        $nodename = 'stk';
        $obsah = "";
        if($isMA) $obsah = getValueForNode($nodelist, $nodename);
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);

        //MA betrag
        $nodename = 'betrag';
        $obsah = "";
        if($isMA) {
            $obsah = number_format (intval (getValueForNode($nodelist, 'stk'))*floatval (getValueForNode($nodelist, 'preis')),2,',',' ');
            $sum_zapati_teil_array['mabetrag']+=intval (getValueForNode($nodelist, 'stk'))*floatval (getValueForNode($nodelist, 'preis'));
        }
        
        $cell = $pole[$nodename];
        $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);


        $pdfobjekt->Ln();
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

function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumArray,$gutStk,$aussStk)
{
    global $cells;
    global $sum_zapati_rechnung_array;

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);

        $pdfobjekt->Cell(
                $cells['teilnr']['sirka']
                +$cells['teilbez']['sirka']
                +$cells['text1']['sirka']
                +$cells['auss']['sirka']
                +$cells['stk']['sirka']
                ,$vyskaradku,"",'T',0,'L',1);

        // preis
        $obsah = number_format($sumArray['preis'], 4,',',' ');
        $pdfobjekt->Cell(
                $cells['preis']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['betrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['betrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['gutbetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['gutbetrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['aussbetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['aussbetrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = '';
        $pdfobjekt->Cell(
                $cells['stk']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['mabetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['betrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['gutbetrag']+$sumArray['aussbetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['gesbetrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);


        $pdfobjekt->Cell(0,$vyskaradku,"",'T',1,'L',1);
}

function zapati_rechnung($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sumArray)
{
global $cells;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);

        $pdfobjekt->Cell(
                $cells['teilnr']['sirka']
                +$cells['teilbez']['sirka']
                +$cells['text1']['sirka']
                +$cells['auss']['sirka']
                +$cells['stk']['sirka']
                +$cells['preis']['sirka']
                ,$vyskaradku,"Summe:",'T',0,'L',1);

        $obsah = number_format($sumArray['betrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['betrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['gutbetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['gutbetrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['aussbetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['aussbetrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = '';
        $pdfobjekt->Cell(
                $cells['stk']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['mabetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['betrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);

        $obsah = number_format($sumArray['gutbetrag']+$sumArray['aussbetrag'], 2,',',' ');
        $pdfobjekt->Cell(
                $cells['gesbetrag']['sirka']
                ,$vyskaradku,$obsah,'T',0,'R',1);


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

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{

    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Ln();
	$pdfobjekt->Cell(0,$vyskaradku,"Bitte überweisen Sie den Betrag bis ".getValueForNode($childNodes,"zahldatum")." auf das Konto",'0',1,'1',0);
	$pdfobjekt->Cell(0,$vyskaradku,"Nr. ".getValueForNode($childNodes,"kontotext")."",'0',1,'1',0);


    // verwendungszweck
    $zweck=getValueForNode($childNodes,"verwzweck");


    if(strlen($zweck)>0){

    $renr = getValueForNode($childNodes,"auftragsnr");
    $lsnr = getValueForNode($childNodes,"origauftrag");

    $zweckText = "Als Verwendungszweck geben Sie bitte an: ";

    $textPredZavorkama="";
    $textMeziZavorkama="";
    $textZaZavorkama="";

    // zkusim najit v rechtextu neco v hranate zavorce
    $pozicePromenne = strpos($zweck,"[");
    $poziceDruheZavorky = false;


    if($pozicePromenne){
        // vytahnu si obsah hranate zavorky
        $poziceDruheZavorky = strpos($zweck,"]");
        if($poziceDruheZavorky){
            $textMeziZavorkama = substr($zweck, $pozicePromenne+1, $poziceDruheZavorky-$pozicePromenne-1);
        }
    }

    if($pozicePromenne)
        $textPredZavorkama = substr($zweck,0,$pozicePromenne);
    else
        $textPredZavorkama = $zweck;

    if($poziceDruheZavorky)
        $textZaZavorkama = substr($zweck,$poziceDruheZavorky+1);

    if($textMeziZavorkama=="re-nr") $textMeziZavorkama = $renr;
    if($textMeziZavorkama=="ls-nr") $textMeziZavorkama = $lsnr;

    if(strlen($zweck)>0)
        $zweckText = $zweckText." ".$zweck." ".$renr;
    else
        $zweckText="";
//    $zweckText .= $textPredZavorkama." ".$textMeziZavorkama." ".$textZaZavorkama;
    $pdfobjekt->Cell(0,$vyskaradku,$zweckText,'0',1,'1',0);
    }

	$dic=getValueForNode($childNodes,"andic");
    $vom=getValueForNode($childNodes,"vom");

    $zeile1='Es handelt sich um eine Dienstleistung nach §9 Abs. 1 UStG (Gesetzessammlung 235 / 2004 in aktueller Fassung).';
    $zeile2='Die erbrachte Dienstleistung unterliegt gemäß Artikel 196 der EU Richtlinie dem Reverse-Charge Verfahren.';
    $zeile3='Die Steuerschuld geht auf den Leistungsempfänger über.';

    // pridani odstavce pro DPH
    if($vom==100)
	{
        $pdfobjekt->SetFont("FreeSans", "", 8);
		$pdfobjekt->Cell(0,$vyskaradku,$zeile1,'0',1,'1',0);
                $pdfobjekt->Cell(0,$vyskaradku,$zeile2,'0',1,'1',0);
                $pdfobjekt->Cell(0,$vyskaradku,$zeile3,'0',1,'1',0);
		$pdfobjekt->Cell(0,$vyskaradku,"Die USt.Id.Nr. des Rechnungsempfängers ist: ".$dic,'0',1,'1',0);
	}

    $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Ln();
	$pdfobjekt->Cell(0,$vyskaradku,"Mit freundlichen Grüssen",'0',1,'L',0);
    $vomname = getValueForNode($childNodes,"vomname");
	$pdfobjekt->Cell(0,$vyskaradku,$vomname,'0',1,'L',0);
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
function zahlavi_import($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,""),'0',0,'L',$fill);
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,"im"),'0',0,'L',$fill);
	$pdfobjekt->Cell(25,$vyskaradku,"Best.Nr.:".getValueForNode($childNodes,"fremdauftr"),'0',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,"Pos.:".getValueForNode($childNodes,"fremdpos"),'0',1,'L',$fill);


	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,"teilnr"),'0',0,'L',$fill);
	$pdfobjekt->Cell(50,$vyskaradku,getValueForNode($childNodes,"teillang"),'0',0,'L',$fill);

	$obsah=number_format(getValueForNode($childNodes,"gew"),3,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah."kg/Stk",'0',1,'R',$fill);

	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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

$pdf->SetHeaderData("", 0, "D792", "");
$pdf->setRechnungFoot(true);
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
//pageheader($pdf,$cells_header,5);

// zacinam po rechnungach

$z1 = "Abydos s.r.o.                  Hazlov 247                 Tel:+420 354 595 337               DIC / Ust/Id/Nr:CZ25206958";
$z2 = "                                       35132 Hazlov             Fax:+420 354 596 993                                         ";
$z3 = "";

$pdf->setRechnungZeilen($z1, $z2, $z3);



// zacinam po rechnungach

$rechnunge=$domxml->getElementsByTagName("rechnung");
foreach ($rechnunge as $rechnung) {
    $rechnungChildNodes = $rechnung->childNodes;

    //test_pageoverflow($pdf,5,$cells_header);
    //zahlavi_rechnung($pdf,5,array(255,255,255),$exportChildNodes);
    pageheader($pdf, $cells_header, $rechnungChildNodes);

    // ted jdu po fremdauftr
    $fremdauftraege = $rechnung->getElementsByTagName("fremdauftr");
    foreach ($fremdauftraege as $fremdauftrag) {
        $fremdauftragChildNodes = $fremdauftrag->childNodes;
        //test_pageoverflow($pdf,5,$cells_header);
        //zahlavi_fremdauftrag($pdf,5,array(255,255,255),$teilChildNodes);
        // ted pujdu po fremdpositionech
        $fremdpositionen = $fremdauftrag->getElementsByTagName("fremdpos");
        foreach ($fremdpositionen as $fremdposition) {
            $fremdpositionChildNodes = $fremdposition->childNodes;
            //test_pageoverflow($pdf,5,$cells_header);
            if (test_pageoverflow_noheader($pdf, 5))
                pageheader_simple($pdf, $cells_header, $rechnungChildNodes);
            zahlavi_fremdposition($pdf, 5, array(255, 255, 255), $fremdpositionChildNodes);
            //nuluj_sumy_pole($sum_zapati_import_array);
            // ted jdu po dilech
            $teile = $fremdposition->getElementsByTagName("teil");
            foreach ($teile as $teil) {
                $teilChildNodes = $teil->childNodes;
                $teilGutStk = 0;
                $teilAussStk = 0;
                $taetigkeiten = $teil->getElementsByTagName("taetigkeit");
                $gutPreis = floatval(getValueForNode($teilChildNodes, 'preis_stk_gut'));
                $aussPreis = floatval(getValueForNode($teilChildNodes, 'preis_stk_auss'));
                nuluj_sumy_pole($sum_zapati_teil_array);
                // pro zjisteni dobrych kusu jeste pred projitim vsech cinnosti
                foreach ($taetigkeiten as $taetigkeit) {
                    $kusy = $taetigkeit->getElementsByTagName("kus");
                    //zjistim pocet dobrych kusu projitim cyklu kusy
                    foreach ($kusy as $kus) {
                        $kusChildNodes = $kus->childNodes;
                        $teilGutStk += intval(getValueForNode($kusChildNodes, 'gut_stk'));
                        $teilAussStk+=intval(getValueForNode($kusChildNodes, 'auss'));
                    }
                }
                //------------------------------------------------------------------------------


                foreach ($taetigkeiten as $taetigkeit) {
                    $kusy = $taetigkeit->getElementsByTagName("kus");
                    //a jedu po kusech
                    foreach ($kusy as $kus) {
                        $kusChildNodes = $kus->childNodes;
                        if (test_pageoverflow_noheader($pdf, 5))
                            pageheader_simple($pdf, $cells_header, $rechnungChildNodes);

                        detaily($pdf, $cells, 5, array(255, 255, 255), $kusChildNodes,$teilGutStk,$teilAussStk,$gutPreis,$aussPreis);

                    }
                }
                // zapati teil
                if (test_pageoverflow_noheader($pdf, 5))
                pageheader_simple($pdf, $cells_header, $rechnungChildNodes);
                zapati_teil($pdf, 5, array(255, 255, 240), $teilChildNodes,$sum_zapati_teil_array,$teilGutStk,$teilAussStk);

                // aktualizuju sumy zapati sestavy
                foreach ($sum_zapati_rechnung_array as $key=>$value){
                    $sum_zapati_rechnung_array[$key]+=$sum_zapati_teil_array[$key];
                }
            }
            // zapati position
            if (test_pageoverflow_noheader($pdf, 1))
                pageheader_simple($pdf, $cells_header, $rechnungChildNodes);
            zapati_fremdpos($pdf, 1, array(255, 255, 255), $fremdpositionChildNodes);
        }
    }
    // zapati rechnung
    if (test_pageoverflow_noheader($pdf, 6))
        pageheader_simple($pdf, $cells_header, $rechnungChildNodes);
    zapati_rechnung($pdf, 6, array(255, 255, 255), $rechnungChildNodes, $sum_zapati_rechnung_array);
}

//zapati_sestava($pdf,6,array(255,255,255),$rechnungChildNodes);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
