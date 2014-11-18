<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "D510";
$doc_subject = "D510 Report";
$doc_keywords = "D510";

// necham si vygenerovat XML

$parameters=$_GET;

$teil=$_GET['teil'];
$musterfoto=$_GET['musterfoto'];
$sl = intval($_GET['sloupcu']);

if($sl<1) $sl=1;
if($sl>10) $sl=10;

$bMusterfoto = FALSE;
if($musterfoto=="a")
    $bMusterfoto = TRUE;


require_once('D510_xml.php');


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

$cells_dokument = array(
"doku_nr"
=> array ("nf"=>array(0,',',' '),"popis"=>"DokuNr","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"doku_beschreibung"
=> array ("substring"=>array(0,50),"popis"=>"DokuBeschreibung","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"einlager_datum"
=> array ("substring"=>array(0,10),"popis"=>"Einlag. Datum","sirka"=>15,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"musterplatz"
=> array ("substring"=>array(0,100),"popis"=>"Musterplatz / Datei","sirka"=>60,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"freigabe_am"
=> array ("substring"=>array(0,10),"popis"=>"Freigabe am","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"freigabe_vom"
=> array ("substring"=>array(0,50),"popis"=>"Freigabe vom","sirka"=>0,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"ln"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)    
);

$cells = 
array(

"kzdruck"
=> array ("popis"=>"","sirka"=>5,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"tatnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"tatkz" 
=> array ("popis"=>"","sirka"=>5,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"Stat_Nr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"KzGut" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_d" 
=> array ("popis"=>"","sirka"=>35,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_t" 
=> array ("popis"=>"","sirka"=>35,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"mittel" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"lager_von" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"lager_nach" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"bedarf_typ" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_header = 
array(
"kzdruck"
=> array ("popis"=>"\ndr","sirka"=>5,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"tatnr" 
=> array ("popis"=>"TatNr\noper.","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"tatkz" 
=> array ("popis"=>"taetkz\nRE   Stat.","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"KzGut" 
=> array ("popis"=>"Kz\nGut","sirka"=>7,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_d" 
=> array ("popis"=>"Bezeichnung\noznaceni","sirka"=>70,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"mittel" 
=> array ("popis"=>"\nAM","sirka"=>25,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(4,',',' '),"popis"=>"VzKd\nmin/stk","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(4,',',' '),"popis"=>"VzAby\nmin/stk","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"lager_von" 
=> array ("popis"=>"Lager\nvon   nach","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"bedarf_typ" 
=> array ("popis"=>"Be-\ndarf","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>0)

);



$sum_zapati_teil_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
                                "sumvzkd_regel"=>0,
                                "sumvzaby_regel"=>0
								);
global $sum_zapati_teil_array;

// pole s podminkama, ktere urcuji co mam do dane sumy nascitavat
// "suma"=>array("pole s kriteriem",podminka)

$sumBedingungen = array (
                            "sumvzkd_regel"=>array("bedarf_typ","R"),
                            "sumvzaby_regel"=>array("bedarf_typ","R")
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

////////////////////////////////////////////////////////////////////////////////////////////////////
//zahlavi_teil($pdf,$dilChildNodes);
//
function zahlavi_teil($pdfobjekt,$childNodes)
{

	$pdfobjekt->SetFont("FreeSans", "", 10);

	$pdfobjekt->SetFillColor(255,255,200,1);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"TeilNr:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"teilnr"),'0',0,'L',0);

    // bruttogeweicht
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"Brutto-Gew (kg):",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Cell(15,6,number_format(getValueForNode($childNodes,"BrGew"),3,',',' '),'0',0,'R',0);


	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"letzter IM:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->Cell(0,6,getValueForNode($childNodes,"auftragsnr")." ( ".getValueForNode($childNodes,"aufdat")." )",'0',1,'L',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Original:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"teillang"),'0',0,'L',0);

        // zobrazeni hmostnosti podle jednotkoveho scenare
        
    // nettogewicht
    $pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"Netto-Gewicht (kg):",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Cell(15,6,number_format(getValueForNode($childNodes,"Gew"),3,',',' '),'0',1,'R',0);

        // Art Guseisen
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Werkstoff: ",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"age"),'0',1,'L',0);

    // teilbezeichnung
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Bezeichnung:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"Teilbez"),'0',1,'L',0);

        // fremdauftr_dkopf
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Fremdauftr:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"fremdauftr_dkopf"),'0',1,'L',0);

        // kunde
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Kunde:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(
                        0,
                        6,
                        getValueForNode($childNodes,"kunde")." - ".getValueForNode($childNodes,"name1")." ".getValueForNode($childNodes,"name2")
                        ,'0',1,'L',0);

    //status
    $filldata = 0;
    $status = getValueForNode($childNodes,"status");
    if(trim($status)=="ALT"){
        $filldata=1;
        $pdfobjekt->SetFillColor(255,200,200,1);
    }
    $pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Status:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(30,6,$status,'0',1,'R',$filldata);


//        $pdfobjekt->SetFillColor(255,255,200,1);
//	$pdfobjekt->SetFont("FreeSans", "", 8);
//	$pdfobjekt->Cell(20,6,"Musterplatz:",'0',0,'L',1);
//
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"musterplatz"),'0',0,'R',0);
	
	// verpackungmenge
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"Verpackungsmenge (Stk):",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(25,6,number_format(getValueForNode($childNodes,"verpackungmenge"),0,',',' '),'0',0,'R',0);

        // stk pro gehaenge	
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"Stk pro Gehänge:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(13,6,number_format(getValueForNode($childNodes,"stk_pro_gehaenge"),0,',',' '),'0',0,'R',0);

        // restmengen_verw
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"Restmengenverw.:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(13,6,getValueForNode($childNodes,"restmengen_verw"),'0',1,'L',0);

        
//	$pdfobjekt->SetFont("FreeSans", "", 8);
//	$pdfobjekt->Cell(20,6,"Einlag.Datum:",'0',0,'L',1);
//
//	$pdfobjekt->SetFont("FreeSans", "B", 10);
//	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"mustervom"),'0',1,'R',0);

	$pdfobjekt->Ln();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_pozice($pdfobjekt,$cells)
{
		$a = AplDB::getInstance();
		$puser = $_SESSION['user'];

    $pdfobjekt->SetFont("FreeSans", "B", 7);
	foreach($cells as $nodename=>$cell)
	{
		$cellobsah = $cell['popis'];
    		// security
		if($nodename=='vzkd'){
		    $elementId = 'vzkd';
		    $cellobsah=$a->getDisplaySec('d510',$elementId,$puser)?$cellobsah:"\n";
		}

		$pdfobjekt->MyMultiCell($cell["sirka"],5,$cellobsah,$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();

}

////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$summe_array)
{
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->Cell(5+7+5+10+10+35+35+25,6,"Summe alle Taetigkeiten",'T',0,'L',0);

	$obsah=number_format($summe_array['vzkd'],4,',',' ');
	// security
	$a = AplDB::getInstance();
	$puser = $_SESSION['user'];
	$elementId = 'vzkd';
	$obsah=$a->getDisplaySec('d510',$elementId,$puser)?$obsah:'';
	$pdfobjekt->Cell(15,6,$obsah,'T',0,'R',0);
	$obsah=number_format($summe_array['vzaby'],4,',',' ');
	$pdfobjekt->Cell(15,6,$obsah,'T',1,'R',0);

	$obsah = "VzKd/VzAby";
	$obsah=$a->getDisplaySec('d510',$elementId,$puser)?$obsah:'';
	$pdfobjekt->Cell(5+7+5+10+10+35+35+25,6,$obsah,'T',0,'L',0);

	if($summe_array['vzaby']==0)
		$cislo=0;
	else
		$cislo=$summe_array['vzkd']/$summe_array['vzaby'];
		
	
	$obsah=number_format($cislo,4,',',' ');
	$obsah=$a->getDisplaySec('d510',$elementId,$puser)?$obsah:'';
	$pdfobjekt->Cell(30,6,$obsah,'T',1,'R',0);

    // podminene sumy
    $pdfobjekt->Ln();
	$pdfobjekt->Cell(5+7+5+10+10+35+35+25,6,"Summe Regel-Taetigkeiten",'T',0,'L',0);

	$obsah=number_format($summe_array['sumvzkd_regel'],4,',',' ');
	$obsah=$a->getDisplaySec('d510',$elementId,$puser)?$obsah:'';
	$pdfobjekt->Cell(15,6,$obsah,'T',0,'R',0);
	$obsah=number_format($summe_array['sumvzaby_regel'],4,',',' ');
	$pdfobjekt->Cell(15,6,$obsah,'T',1,'R',0);

	$obsah = "VzKd/VzAby";
	$obsah=$a->getDisplaySec('d510',$elementId,$puser)?$obsah:'';
	
	$pdfobjekt->Cell(5+7+5+10+10+35+35+25,6,$obsah,'T',0,'L',0);

	if($summe_array['sumvzaby_regel']==0)
		$cislo=0;
	else
		$cislo=$summe_array['sumvzkd_regel']/$summe_array['sumvzaby_regel'];

	$obsah=number_format($cislo,4,',',' ');
	$obsah=$a->getDisplaySec('d510',$elementId,$puser)?$obsah:'';
	$pdfobjekt->Cell(30,6,$obsah,'T',0,'R',0);

}
////////////////////////////////////////////////////////////////////////////////////////////////////
//zobraz_pozice($pdf,$tatChildNodes,$cells);
function zobraz_pozice($pdfobjekt,$childNodes,$cells)
{
	
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($cells as $nodename=>$cell)
	{
                $pdfobjekt->SetFont("FreeSans", "", 7);
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($childNodes,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($childNodes,$nodename);
		}

               if($nodename=="kzdruck"){
                    $cellobsah=getValueForNode($childNodes,$nodename);
                    if($cellobsah!=0){
                            $pdfobjekt->SetFont("FreeSans", "B", 7);
                            $cellobsah="x";
                    }
                    else{
                        $cellobsah="";
                    }
                }

		// security
		$a = AplDB::getInstance();
		$puser = $_SESSION['user'];

		if($nodename=='vzkd'){
		    $elementId = 'vzkd';
		    $cellobsah=$a->getDisplaySec('d510',$elementId,$puser)?$cellobsah:'';
		}

                
		$pdfobjekt->Cell($cell["sirka"],6,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 7);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
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
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2])."$nodename";
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename)."$nodename";
		}

                if($nodename=="kzdruck"){
                    $cellobsah=getValueForNode($nodelist,$nodename);
                    if($cellobsah!=0)
                        $cellobsah="x";
                    else
                        $cellobsah="";
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
function zobraz_cinnosti($pdfobjekt,$taetigkeiten)
{
	$x_pocatek=130;
	$y_pocatek=25;
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek);
	$pdfobjekt->Cell(10,3,"taetnr",'B',0,'L');
	$pdfobjekt->Cell(50,3,"Bezeichnung",'B',0,'L');
	$pdfobjekt->Cell(50,3,"oznaceni",'B',0,'L');
	$pdfobjekt->Cell(15,3,"ks/hod",'B',0,'R');
	$pdfobjekt->Cell(15,3,"min/stk",'B',1,'R');
	$pdfobjekt->SetX($x_pocatek);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	foreach($taetigkeiten as $taetigkeit)
	{
		$taetigkeitChildNodes = $taetigkeit->childNodes;
		$pdfobjekt->Cell(10,3,getValueForNode($taetigkeitChildNodes,"taetnr"),0,0,'L');
		$pdfobjekt->Cell(50,3,getValueForNode($taetigkeitChildNodes,"tatbez_d"),0,0,'L');
		$pdfobjekt->Cell(50,3,getValueForNode($taetigkeitChildNodes,"tatbez_t"),0,0,'L');

		$obsah=number_format(getValueForNode($taetigkeitChildNodes,"ks_hod"),0,',',' ');
		$pdfobjekt->Cell(15,3,$obsah,0,0,'R');
		$obsah=number_format(getValueForNode($taetigkeitChildNodes,"vzaby"),2,',',' ');
		$pdfobjekt->Cell(15,3,$obsah,0,1,'R');
		$pdfobjekt->SetX($x_pocatek);
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);
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
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"name1"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"name2"));$pdfobjekt->Ln();

	// pole pro auftrag
	$pdfobjekt->Rect($x_pocatek+52+3,$y_pocatek-10,52,30);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->SetXY($x_pocatek+52+3,$y_pocatek-10);
	$pdfobjekt->Write(5,"Auftrag / dodavka / pal:");$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetX($x_pocatek+52+5);
	$pdfobjekt->SetFont("FreeSans", "B", 25);
	$pdfobjekt->Write(25,getValueForNode($importChildNodes,"auftragsnr")."/");
	$pdfobjekt->SetFont("FreeSans", "B", 18);
	$pdfobjekt->Write(25,getValueForNode($paletteChildNodes,"pal"));$pdfobjekt->Ln();

	// pole pro cislo dilu
	$pdfobjekt->Rect($x_pocatek,$y_pocatek-10+30+3,52,24);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10+30+3);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->Write(5,"Teil / cislo dilu:");$pdfobjekt->Ln();
	$pdfobjekt->SetX($x_pocatek);
	$pdfobjekt->SetFont("FreeSans", "B", 20);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"teil"));$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"teilbez"));$pdfobjekt->Ln();

	// pole pro pocet kusu
	$pdfobjekt->Rect($x_pocatek+52+3,$y_pocatek-10+30+3,52,24);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->SetXY($x_pocatek+52+3,$y_pocatek-10+30+3);
	$pdfobjekt->Write(5,"Stuck / pocet kusu:");$pdfobjekt->Ln();
	$pdfobjekt->SetX($x_pocatek+52+5+15);
	$pdfobjekt->SetFont("FreeSans", "B", 18);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"stk"));$pdfobjekt->Ln();
	$pdfobjekt->SetX($x_pocatek+52+5);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Write(5,"Druh litiny:".getValueForNode($paletteChildNodes,"artguseisen"));
	$pdfobjekt->SetX($x_pocatek+52+5+30);
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Write(5,getValueForNode($paletteChildNodes,"gew")." kg");

	// pole pro muster
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->Rect($x_pocatek+52+3+52+3,$y_pocatek-10,150,5);
	$pdfobjekt->SetXY($x_pocatek+52+3+52+3,$y_pocatek-10);
	$pdfobjekt->Write(5,"Muster: ".getValueForNode($paletteChildNodes,"musterplatz"));
	$pdfobjekt->Write(5,"      Eingelagert am: ".getValueForNode($paletteChildNodes,"mustervom"));
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Write(5,"      Teil (original): ".getValueForNode($paletteChildNodes,"teillang"));
	
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10+30+3+24+3);	

	// tabulka pro zapis vykonu
	// hlavicka
	$pdfobjekt->MyMultiCell(25,8,"Datum",1,'L',0);
	$pdfobjekt->MyMultiCell(15,4,"Schicht\nsmena",1,'L',0);
	$pdfobjekt->MyMultiCell(20,4,"PersNr\nosobni cislo",1,'L',0);
	$pdfobjekt->MyMultiCell(90,4,"AFO - Nr.\ncislo operace",1,'L',0);
	$pdfobjekt->MyMultiCell(30,4,"Stuck / kus\nvporadku zmetky",1,'L',0);
	$pdfobjekt->MyMultiCell(40,4,"Arbeitszeit\nvon/od    bis/do",1,'C',0);
	$pdfobjekt->SetFont("FreeSans", "B", 15);
	$pdfobjekt->MyMultiCell(20,8,"Q",1,'C',0);
	$pdfobjekt->MultiCell(20,8,"SYS",1,'C',0);
	// radky tabulky
	$sirky = array(25,15,20,10,10,10,10,10,10,10,10,10,15,15,20,20,20,20);
	for($i=0;$i<9;$i++)
	{
		foreach($sirky as $sirka)
		{
			$pdfobjekt->MyMultiCell($sirka,10,"",1,'C',0);
		}
		$pdfobjekt->Ln();
	}

	// spodni ramecek s poznamkou
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	// posunu se o mm dolu
	$pdfobjekt->SetY($pdfobjekt->GetY()+2);
	$pdfobjekt->MyMultiCell(40,4,"Druhy zmetku\nAusschussart\n",1,'C',0);
	$pdfobjekt->MyMultiCell(110,4,"10(2) od zak. pred obrousenim / Kd. vor Putzen\n20(4) od zak. po obrouseni / Kd. nach Putzen\n50(6) Aby / Aby",1,'L',0);
	$pdfobjekt->MyMultiCell(110,4,"Bemerkung\nPoznamka\n",1,'L',0);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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

function pageheader_dokumenty($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
	    $pdfobjekt->Cell($cell["sirka"], $headervyskaradku,$cell['popis'], $cell['ram'], 0, $cell["align"],1);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}

function detaily_dokumenty($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
{
    
	$pdfobjekt->SetFont("FreeSans", "", 7);
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

                if(array_key_exists("substring",$cell))
		{
                        $append='';
                        if(strlen($cellobsah)>$cell['substring'][1]) $append='...';
			$cellobsah = substr($cellobsah,$cell['substring'][0],$cell['substring'][1]).$append;
		}

		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        $pdfobjekt->SetTextColor(0,0,0);
	$pdfobjekt->SetFont("FreeSans", "", 7);
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D510 ARBEITSPLAN INFORMATION", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT-5);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 13));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();

// zacinam po dilech

$dily=$domxml->getElementsByTagName("teil");
foreach($dily as $dil)
{
	$dilChildNodes = $dil->childNodes;
	zahlavi_teil($pdf,$dilChildNodes);
	
	//dokumenty
	pageheader_dokumenty($pdf, $cells_dokument, 5);
	$docs = $domxml_5->getElementsByTagName("dokument");
	foreach ($docs as $doc){
	    $docChilds = $doc->childNodes;
	    detaily_dokumenty($pdf, $cells_dokument, 5, array(255,255,255), $docChilds);
	}
	$pdf->Ln();
//------------------------------------------------------------------------------

	// ted jdu po cinnostech
	$pozice=$dil->getElementsByTagName("tat");
	zahlavi_pozice($pdf,$cells_header);
	foreach($pozice as $tat)
	{
		$tatChildNodes = $tat->childNodes;
		zobraz_pozice($pdf,$tatChildNodes,$cells);
		// nascitam casy
		foreach($sum_zapati_teil_array as $key=>$prvek)
		{
			$hodnota = getValueForNode($tatChildNodes,$key);
			$sum_zapati_teil_array[$key]+=$hodnota;
		}

		$hodnotaVzkd = getValueForNode($tatChildNodes,"vzkd");
        $hodnotaVzaby = getValueForNode($tatChildNodes,"vzaby");
        $bedarf_typ = getValueForNode($tatChildNodes,"bedarf_typ");

        if($bedarf_typ=="R"){
            $sum_zapati_teil_array['sumvzkd_regel']+=$hodnotaVzkd;
            $sum_zapati_teil_array['sumvzaby_regel']+=$hodnotaVzaby;
        }
	}
	zapati_teil($pdf,$sum_zapati_teil_array);
}

//pro pozadavku na zobrazeni musterfoto
if ($bMusterfoto) {
    $apl = AplDB::getInstance();
    $pdf->AddPage();
    $pdf->SetFillColor(230, 230, 255);
    $pdf->Cell(0, 5, "Muster Fotos:", '1', 1, 'L', 1);
    $gdatPath = "/mnt/gdat/Dat/";
    $att='muster';
    $att2FolderArray = AplDB::$ATT2FOLDERARRAY;
    $extensions = 'JPG|jpg';
    $filter = "/.*.($extensions)$/";
    $teilnr = $teil;
    $kundeGdatPath = $apl->getKundeGdatPath($apl->getKundeFromTeil($teilnr));
    $anlagenDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teilnr . "/" . AplDB::$DIRS_FOR_TEIL_FINAL[$att2FolderArray[$att]];
    $pdf->Cell(0, 5, substr($anlagenDir,14), '1', 1, 'L', 1);
    $mezeraMeziObrazky = 5;
    $docsArray = $apl->getFilesForPath($anlagenDir,$filter);
    if($docsArray!==NULL){
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$column=0;
	$imgMaxHeight=0;
	$sloupcu = $sl;
	foreach ($docsArray as $doc){
	    $filePath = $anlagenDir.'/'.$doc['filename'];
	    $anlage = $doc['filename'];
//	    $pdf->Cell(0, 5, $filePath, '1', 1, 'L', 0);
	    if (file_exists($filePath)) {
		$filenameNew = substr($anlage, 0, strrpos($anlage, '.')) . '_tmp' . substr($anlage, strrpos($anlage, '.'));
		$img = new Imagick($filePath);
		$heightOriginal = $img->getimageheight();
		$widthOriginal = $img->getimagewidth();
		$ratio = $widthOriginal / $heightOriginal;
		$imgWidth = ($pdf->getPageWidth()-($mezeraMeziObrazky*($sloupcu-1))-PDF_MARGIN_LEFT-5)/$sloupcu;
//		$imgWidth = 50;
		$imgHeight = $imgWidth / $ratio;
		if($column<$sloupcu){
		    if($imgMaxHeight<$imgHeight)
			$imgMaxHeight = $imgHeight;
		}
//		$pdf->Cell(0, 5, "$imgWidth x $imgHeight", '1', 1, 'L', 0);
		$img->thumbnailimage(600, 600, TRUE);
		$img->writeimage($anlagenDir . '/' . $filenameNew);
		//nez vykreslim obrazek, otestuju, zda nepujdu pres konec stranky
		if (($y + $imgHeight ) > ($pdf->getPageHeight() - $pdf->getBreakMargin())) {
		    $pdf->AddPage();
		    $y = $pdf->GetY();
		    $x = $pdf->GetX();
		    $column = 0;
		}
		$pdf->Image($anlagenDir . '/' . $filenameNew, $x+($column*($imgWidth+$mezeraMeziObrazky)), $y+$mezeraMeziObrazky, $imgWidth, $imgHeight);
		$pdf->Text($x+($column*($imgWidth+$mezeraMeziObrazky)), $y+$mezeraMeziObrazky-1, $anlage);
		$column++;
		if($column>($sloupcu-1)){
		    $column=0;
		    // posunout y na dalsi radek tabulky
		    $y += ($imgMaxHeight+$mezeraMeziObrazky);
		    $imgMaxHeight=0;
		}
		unlink($anlagenDir . '/' . $filenameNew);
	    }
	}
    }
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
