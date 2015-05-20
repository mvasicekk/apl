<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

// pod pod
$doc_title = "D710";
$doc_subject = "D710 Report";
$doc_keywords = "D710";

// necham si vygenerovat XML
$parameters=$_GET;

$datumvon=make_DB_datum($_GET['termin']);
$export=$_GET['export'];
$ex = $export;

require_once('D710_xml.php');
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");
//unset ($parameters);

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


// nechci zobrazit parametry
// vynuluju promennou $params
$params="";

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells = 
array(

"Teil" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"import" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text" 
=> array ("popis"=>"","sirka"=>33,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"expstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss2_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss4_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss6_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vypln" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_header = 
array(

"Teil" 
=> array ("popis"=>"Teil","sirka"=>25,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"Import" 
=> array ("popis"=>"Import","sirka"=>25,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"Leistung" 
=> array ("popis"=>"Leistung","sirka"=>33,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"gutstk" 
=> array ("popis"=>"gute Stk","sirka"=>30,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"auss2" 
=> array ("popis"=>"auss2","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"auss4" 
=> array ("popis"=>"auss4","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),
	
"auss6" 
=> array ("popis"=>"auss6","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),
	
"bestellt" 
=> array ("popis"=>"Bestellt","sirka"=>15,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"gew" 
=> array ("popis"=>"Gew","sirka"=>0,"ram"=>'TB',"align"=>"R","radek"=>1,"fill"=>1)

);


// sumy pro zapati
$sum_zapati_import_array = array(	
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"expstk"=>0,
								"gew_auss"=>0,
								);
global $sum_zapati_import_array;

$sum_zapati_sestava_array = array(	
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"geliefert"=>0,
								"gew_auss"=>0,
								);


global $sum_zapati_sestava_array;

$sum_zapati_teil_array = array(
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"geliefert"=>0,
								"gew_auss"=>0,
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
	$pdfobjekt->SetFont("FreeSans", "B", 8);
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
			number_format(floatval(getValueForNode($nodelist,$nodename)), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
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

function getStk($bewArray,$behnr,$zustand){
    if($bewArray===NULL) return 0;
    foreach($bewArray as $bew){
        if($bew['behaelternr']==$behnr && $bew['zustand_id']==$zustand) return $bew['stk'];
    }
    return 0;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaradku
 * @param <type> $rgb
 * @param <type> $childNodes
 */
function sestava_tabulka($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
        global $ex;
        $apl = AplDB::getInstance();
        

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil
        // NEW
        $pdfobjekt->SetX(120);
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->MultiCell(50,5,"Ausschusstyp\n(2) KdAusschuss vor Arbeitsgang\n(4) KdAusschuss nach Arbeitsgang\n(6) Ausschuss Abydos",0,2,'L',0);

	$x_typausschussu = $pdfobjekt->GetX();
	$y_typausschussu = $pdfobjekt->GetY();

        // tabulka s behaeltrama
        $spaltenArray = $apl->getBehaelterZustandD710Array();
//        echo "<pre>".var_dump($spaltenArray)."</pre>";
        $kunde = $apl->getKundeFromAuftransnr($ex);
//        echo "<pre>".$kunde."</pre>";
        $behaelterArray = $apl->getBehaelterKundeMitInventur($kunde);
//        echo "<pre>".var_dump($behaelterArray)."</pre>";
        $stkArray = $apl->getBehaelterBewegungenFuerImEx($ex, 1);
//        echo "<pre>".var_dump($stkArray)."</pre>";

        $pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Cell(140,$vyskaradku,"Behälter",'0',1,'L',$fill);
        // radek s nadpisem tabulky
        $pdfobjekt->Cell(25,$vyskaradku,"",'1',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        foreach($spaltenArray as $spalte){
            $pdfobjekt->Cell(20,$vyskaradku,$spalte['zustand_text'],'1',0,'C',$fill);
        }
        $pdfobjekt->Ln();
        //ted radky s behaeltrama
        foreach($behaelterArray as $behaelter){
            $pdfobjekt->SetFont("FreeSans", "B", 7);
            $pdfobjekt->Cell(25,$vyskaradku,$behaelter['name'],'1',0,'L',$fill);
            $pdfobjekt->SetFont("FreeSans", "", 9);
            foreach($spaltenArray as $spalte){
                $behnr = $behaelter['behaelternr'];
                $zustand = $spalte['zustand_id'];
                $stk = getStk($stkArray, $behnr, $zustand);
                $pdfobjekt->Cell(20,$vyskaradku,$stk,'1',0,'C',$fill);
            }
            $pdfobjekt->Ln();
        }
        //jeden prazdny radek
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell(25,$vyskaradku,'','1',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 9);
        foreach($spaltenArray as $spalte){
            $pdfobjekt->Cell(20,$vyskaradku,'','1',0,'C',$fill);
        }
        $pdfobjekt->Ln();

	$x_old = $pdfobjekt->GetX();
	$y_old = $pdfobjekt->GetY();

	$pdfobjekt->SetX($x_old);
	$pdfobjekt->SetY($y_old);


	$pdfobjekt->Ln();
	$pdfobjekt->Cell(60,5,"Mit freundlichen Grüssen",'0',1,'L',$fill);
	$pdfobjekt->Cell(60,5,getValueForNode($childNodes,"SachbearbeiterAby"),'0',1,'L',$fill);
	$pdfobjekt->Cell(60,5,"Tel.  :".getValueForNode($childNodes,"TelAby"),'0',1,'L',$fill);
	$pdfobjekt->Cell(60,5,"Fax.  :".getValueForNode($childNodes,"FaxAby"),'0',1,'L',$fill);
	$pdfobjekt->Cell(60,5,"Email.:".getValueForNode($childNodes,"EmailAby"),'0',1,'L',$fill);

//	$pdfobjekt->SetY($y_typausschussu+90);
//	$pdfobjekt->SetX($x_typausschussu+120);


        $pdfobjekt->SetY($pdfobjekt->GetY()-20);
        $pdfobjekt->SetX(120);
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->MultiCell(70,10,"Datum: ........................\nUnterschrift: ...............",0,2,'R',0);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sum_zapati_array)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil

	$pdfobjekt->Ln();
	$pdfobjekt->Cell(25+25+33,$vyskaradku,"Summe Lieferung",'LBT',0,'L',$fill);
	$obsah=number_format($sum_zapati_array['geliefert'],0,',',' ');
	$pdfobjekt->Cell(30,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss2_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss4_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss6_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BTR',1,'R',$fill);
	


	$pdfobjekt->Cell(25+25,$vyskaradku,"Gewicht(to) Netto",'LBT',0,'L',$fill);

	$obsah = (getValueForNode($childNodes,"gew_gut")+$sum_zapati_array['gew_auss'])/1000;
	$obsah=number_format($obsah,3,',',' ');
	$pdfobjekt->Cell(33,$vyskaradku,$obsah,'BT',0,'L',$fill);

	$obsah=number_format(getValueForNode($childNodes,"gew_gut")/1000,3,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=number_format($sum_zapati_array['gew_auss']/1000,3,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BTR',1,'R',$fill);
	


	$pdfobjekt->Cell(25+25+33+10+10,$vyskaradku,"Gewicht(to) Brutto",'LBTR',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);


}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_import($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sum_zapati_array)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil
	$pdfobjekt->Cell(25,$vyskaradku,'','0',0,'L',$fill);
        $pdfobjekt->Cell(25,$vyskaradku,"IM: ".getValueForNode($childNodes, 'im'),'BT',0,'L',$fill);
	$pdfobjekt->Cell(33,$vyskaradku,"geliefert",'BT',0,'L',$fill);
	$pdfobjekt->Cell(30,$vyskaradku,getValueForNode($childNodes,"geliefert"),'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss2_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss4_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss6_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$pdfobjekt->Cell(15,$vyskaradku,getValueForNode($childNodes,"angeliefert"),'BT',0,'R',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,""),'BT',1,'L',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes,$sum_zapati_array)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil
        $teilnr = getValueForNode($childNodes, 'teilnr');
	$pdfobjekt->Cell(25+25+33,$vyskaradku,"Summe ".$teilnr,'BT',0,'L',$fill);
//	$pdfobjekt->Cell(33,$vyskaradku,"geliefert",'BT',0,'L',$fill);
        $obsah=number_format($sum_zapati_array['geliefert'],0,',',' ');
	$pdfobjekt->Cell(30,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss2_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss4_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah=number_format($sum_zapati_array['auss6_stk_exp'],0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$pdfobjekt->Cell(15,$vyskaradku,'','BT',0,'R',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,'','BT',1,'L',$fill);

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_import($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	// dummy
        // fremdauftr je u kazde palety, tj. u kazdeho radku v dauftr
        
	$obsah="";
	// mongolab sharding partition over geological areas
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	//misto pro teil
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,""),'0',0,'L',$fill);
//	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,"im"),'0',0,'L',$fill);
        $pdfobjekt->Cell(25,$vyskaradku,'','0',0,'L',$fill);
	//regularnim vyrazem odstranim mezery &nbsp;
	$cellobsah = preg_replace("/&#?[a-z0-9]{2,8};/i","",getValueForNode($childNodes,"fremdauftr"));
	//odstranim html tagy
	$cellobsah = trim(html_entity_decode(strip_tags($cellobsah)));
	$pdfobjekt->Cell(50,$vyskaradku,"Best.Nr.:".$cellobsah,'0',0,'L',$fill);
	$cellobsah = preg_replace("/&#?[a-z0-9]{2,8};/i","",getValueForNode($childNodes,"fremdpos"));
	$cellobsah = trim(html_entity_decode(strip_tags($cellobsah)));
	
	$pdfobjekt->Cell(0,$vyskaradku,"Pos.:".$cellobsah,'0',1,'L',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(25,$vyskaradku,getValueForNode($childNodes,"teilnr"),'T',0,'L',$fill);
	$pdfobjekt->Cell(50,$vyskaradku,getValueForNode($childNodes,"teillang"),'T',0,'L',$fill);

	$obsah=number_format(getValueForNode($childNodes,"gew"),3,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah."kg/Stk",'T',1,'R',$fill);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_export($pdfobjekt,$vyskaradku,$rgb,$exportChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->SetFont("FreeSans", "B", 9);
//    $pdfobjekt->SetY(-20);
    
        $pdfobjekt->Cell(
        0,
        $vyskaradku,
        "Export: ".getValueForNode($exportChildNodes,"ex")." ( ".getValueForNode($exportChildNodes,"termin")." )",
        '0',1,'L',$fill);
    $pdfobjekt->Ln();
	// dummy
	$obsah="";
        // pod pod
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
	$pdfobjekt->Cell(10,$vyskaradku,getValueForNode($exportChildNodes,"Kunde"),'0',0,'L',$fill);
	$pdfobjekt->Cell(120,$vyskaradku,getValueForNode($exportChildNodes,"Name1"),'0',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,"An:",'0',1,'L',$fill);

	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	$pdfobjekt->Cell(120,$vyskaradku,getValueForNode($exportChildNodes,"Name2"),'B',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,"Fax:".getValueForNode($exportChildNodes,"Fax"),'B',1,'L',$fill);


	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function test_pageoverflow($pdfobjekt, $vysradku, $cellhead,$childnodes=NULL) {
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		zahlavi_export($pdfobjekt,5,array(255,255,255),$childnodes);
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
		return TRUE;
	}
	return FALSE;
}
/**
 *
 * @param type $teil
 * @return type 
 */

function getGutAussStkTeil($teil) {
    
    $importe = $teil->getElementsByTagName("import");
    foreach ($importe as $import) {
	list($gut,$auss) = getGutAussStkImport($import);
	$aussSum+=$auss;
	$sumGut+=$gut;
    }
    return array($sumGut,$aussSum);
}


/**
 *
 * @param type $import
 * @return type 
 */
function getGutAussStkImport($import) {
    $taetigkeiten = $import->getElementsByTagName("tat");
    foreach ($taetigkeiten as $tat) {
	$tatChildNodes = $tat->childNodes;
	$aussSum+=(intval(getValueForNode($tatChildNodes, 'auss2_stk_exp'))+intval(getValueForNode($tatChildNodes, 'auss4_stk_exp'))+intval(getValueForNode($tatChildNodes, 'auss6_stk_exp')));
	$sumGut+=(intval(getValueForNode($tatChildNodes, 'expstk')));
    }
    return array($sumGut,$aussSum);
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D710 Liefer- und Leistungsübersicht", $params);
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
//pageheader($pdf,$cells_header,5);
//$pdf->Ln();
//$pdf->Ln();


// zacinam po exportech

$exporte=$domxml->getElementsByTagName("export");
foreach($exporte as $export)
{
	$exnr=$export->getElementsByTagName("ex")->item(0)->nodeValue;
	$gew_gut=$export->getElementsByTagName("gew_gut")->item(0)->nodeValue;
	$positionen=$export->getElementsByTagName("positionen")->item(0)->nodeValue;
	$kunde=$export->getElementsByTagName("Kunde")->item(0)->nodeValue;
	$name1=$export->getElementsByTagName("Name1")->item(0)->nodeValue;

	$exportChildNodes = $export->childNodes;

	test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
	zahlavi_export($pdf,5,array(255,255,255),$exportChildNodes);
	pageheader($pdf,$cells_header,5);



	// ted jdu po dilech
	$teile=$export->getElementsByTagName("teil");
	foreach($teile as $teil)
	{
		$teilChildNodes = $teil->childNodes;
		list($summeGut,$summeAuss) = getGutAussStkTeil($teil);
		if(($summeAuss+$summeGut)==0) continue;

		test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
		zahlavi_teil($pdf,5,array(255,255,255),$teilChildNodes);
                nuluj_sumy_pole($sum_zapati_teil_array);

		// ted pujdu po importech
		$importe = $teil->getElementsByTagName("import");
		foreach($importe as $import)
		{
			$importChildNodes = $import->childNodes;
			list($summeGut,$summeAuss) = getGutAussStkImport($import);
			if(($summeAuss+$summeGut)==0) continue;

			test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
			zahlavi_import($pdf,5,array(255,255,255),$importChildNodes);
			nuluj_sumy_pole($sum_zapati_import_array);

			// ted jdu po cinnostech
			$taetigkeiten = $import->getElementsByTagName("tat");
			foreach($taetigkeiten as $tat)
			{
				$tatChildNodes = $tat->childNodes;
				test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
				//function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
				detaily($pdf,$cells,4,array(255,255,255),$tatChildNodes);
				foreach($sum_zapati_import_array as $key=>$prvek)
				{
					$hodnota = $tat->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_import_array[$key]+=$hodnota;
				}				
			}
			test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
			zapati_import($pdf,5,array(255,255,255),$importChildNodes,$sum_zapati_import_array);
			// spocitam sumy pro zapati sestavy
			$sum_zapati_sestava_array['auss2_stk_exp']+=$sum_zapati_import_array['auss2_stk_exp'];
			$sum_zapati_sestava_array['auss4_stk_exp']+=$sum_zapati_import_array['auss4_stk_exp'];
			$sum_zapati_sestava_array['auss6_stk_exp']+=$sum_zapati_import_array['auss6_stk_exp'];
			$sum_zapati_sestava_array['gew_auss']+=$sum_zapati_import_array['gew_auss'];
			$sum_zapati_sestava_array['geliefert']+=getValueForNode($importChildNodes,"geliefert");

                        // pro zapati teil
                        $sum_zapati_teil_array['auss2_stk_exp']+=$sum_zapati_import_array['auss2_stk_exp'];
			$sum_zapati_teil_array['auss4_stk_exp']+=$sum_zapati_import_array['auss4_stk_exp'];
			$sum_zapati_teil_array['auss6_stk_exp']+=$sum_zapati_import_array['auss6_stk_exp'];
			$sum_zapati_teil_array['gew_auss']+=$sum_zapati_import_array['gew_auss'];
			$sum_zapati_teil_array['geliefert']+=getValueForNode($importChildNodes,"geliefert");
		}
		test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
                zapati_teil($pdf, 5, array(255,255,240), $teilChildNodes, $sum_zapati_teil_array);
	}
}

test_pageoverflow($pdf,5,$cells_header,$exportChildNodes);
zapati_sestava($pdf,5,array(255,255,255),$exportChildNodes,$sum_zapati_sestava_array);
$pdf->Ln();
$pdf->Ln();
if(test_pageoverflow_noheader($pdf,120))
	zahlavi_export($pdf,5,array(255,255,255),$exportChildNodes);
sestava_tabulka($pdf,10,array(255,255,255),$exportChildNodes);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
