<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S847";
$doc_subject = "S847 Report";
$doc_keywords = "S847";

// necham si vygenerovat XML

// u checkboxu, pokud neni zaskrtnutej tak se vubec neprenasi

$parameters=$_GET;

$a = AplDB::getInstance();

$teil=$_GET['teil'];
$rmab = $a->make_DB_datum($_GET['rmab']);
$hitvon = $a->make_DB_datum($_GET['hitvon']);
$hitbis = $a->make_DB_datum($_GET['hitbis']);


$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S847",$password,$user,0);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


require_once('S847_xml.php');

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml_1->getElementsByTagName("parameters");


foreach ($parameters as $param) {
    $parametry = $param->childNodes;
    // v ramci parametru si prectu label a hodnotu
    foreach ($parametry as $parametr) {
        $parametr = $parametr->childNodes;
        foreach ($parametr as $par) {
            if ($par->nodeName == "label")
                $label = $par->nodeValue;
            if ($par->nodeName == "value")
                $value = $par->nodeValue;
        }
        if (strtolower($label) != "password"){
            $params .= $label . ": " . $value . ";  ";
        }
    }
}


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells_S310 = 
array(

"tat" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"datum" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"pal" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"oe"
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"persnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"name" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"stk" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_typ" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"d1" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header_S310 = 
array(

"tat" 
=> array ("popis"=>"\nTaet.","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"datum" 
=> array ("popis"=>"\nDatum","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"pal" 
=> array ("popis"=>"\nPal","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"oe"
=> array ("popis"=>"\nOE","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"persnr" 
=> array ("popis"=>"\nPers","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"name" 
=> array ("popis"=>"\nName","sirka"=>25,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"stk" 
=> array ("popis"=>"\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"auss_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"auss_typ" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss\nTyp","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"VzKd\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nVzKd","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby_stk" 
=> array ("nf"=>array(2,',',' '),"popis"=>"VzAby\nStk","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nVzAby","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("popis"=>"\nVerb","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"d1" 
=> array ("popis"=>"\n","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),

);


$sum_zapati_taetigkeit_array_S310 = array(	
								"stk"=>0,
								"auss_stk"=>0,
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);

$sum_zapati_teil_array_S310 = array(	
								"stk"=>0,
								"auss_stk"=>0,
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);

$sum_zapati_auftrag_array_S310 = array(	
								"stk"=>0,
								"auss_stk"=>0,
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);

$sum_zapati_sestava_array_S310 = array(	
								"stk"=>0,
								"auss_stk"=>0,
								"vzkd"=>0,
								"vzaby"=>0,
								"verb"=>0,
								);

//---------------------------------------------------------------------------------------------------
$s1_S850 = 13;
$cells_S820 = 
array(

"pt_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"pt_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"pt_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"pt_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"st_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"st_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"st_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"st_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"g_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"g_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"g_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"g_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sonst_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"sonst_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"sonst_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"sonst_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"kdmin_zu_verb" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>0),

"abymin_zu_verb" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sum_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>$s1_S850,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"waehr_pro_tonne" 
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>0,"ram"=>'TBR',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header_S820 = 
array(

"pt_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nstk","sirka"=>$s1_S850,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),

"pt_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"pt_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzaby","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"pt_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nverb","sirka"=>$s1_S850,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>1),

"st_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nstk","sirka"=>$s1_S850,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),

"st_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"st_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzaby","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"st_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nverb","sirka"=>$s1_S850,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>1),

"g_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nstk","sirka"=>$s1_S850,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),

"g_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"g_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzaby","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"g_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nverb","sirka"=>$s1_S850,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>1),

"sonst_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nstk","sirka"=>$s1_S850,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),

"sonst_kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"sonst_abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzaby","sirka"=>$s1_S850,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"sonst_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nverb","sirka"=>$s1_S850,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>1),

"kdmin_zu_verb" 
=> array ("nf"=>array(2,',',' '),"popis"=>"vzkd\nverb","sirka"=>$s1_S850,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),

"abymin_zu_verb" 
=> array ("nf"=>array(2,',',' '),"popis"=>"vzaby\nverb","sirka"=>$s1_S850,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>1),

"sum_verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"ges.\nverb","sirka"=>$s1_S850,"ram"=>'LBR',"align"=>"R","radek"=>0,"fill"=>1),

"waehr_pro_tonne" 
=> array ("nf"=>array(1,',',' '),"popis"=>"EUR\ntonne","sirka"=>0,"ram"=>'LBR',"align"=>"R","radek"=>0,"fill"=>1),

);


$cells_S430 = 
array(
"abgnr"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"abgnr_name"
=> array ("substring"=>array(0,30),"popis"=>"","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"preis"
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd"
=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gut_lfd_1_preis"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
//"bed_2011_vzkd"
//=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
"bed_lfd_j_preis"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
//"bed_2012_vzkd"
//=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"ln"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_dokument = array(
"doku_nr"
=> array ("nf"=>array(0,',',' '),"popis"=>"DokuNr","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"doku_beschreibung"
=> array ("substring"=>array(0,50),"popis"=>"DokuBeschreibung","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"einlager_datum"
=> array ("substring"=>array(0,10),"popis"=>"Einlag. Datum","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"musterplatz"
=> array ("substring"=>array(0,100),"popis"=>"Musterplatz / Datei","sirka"=>120,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"freigabe_am"
=> array ("substring"=>array(0,10),"popis"=>"Freigabe am","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"freigabe_vom"
=> array ("substring"=>array(0,50),"popis"=>"Freigabe vom","sirka"=>0,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    
"ln"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)    
);

$sum_zapati_teil = array(
    'preis'=>0,
    'vzkd'=>0,
    'bed_lfd_1_vzkd'=>0,
    'bed_lfd_j_vzkd'=>0,
    'bed_lfd_1_preis'=>0,
    'gut_lfd_1_preis'=>0,
    'bed_lfd_j_preis'=>0,
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

/**
 *
 * @param TCPDF $pdfobjekt
 * @param array $pole
 * @param int $headervyskaradku 
 */
// header only for dokumenty

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
// funkce pro vykresleni hlavicky na kazde strance
function pageheader_S310($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	//if($pdfobjekt->PageNo()==1)
	//{
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	//}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}
////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
function telo_S310($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
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
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 7);
}

function pageheader_S820($pdfobjekt,$pole,$headervyskaradku,$popisek)
{
	
	$pdfobjekt->SetFillColor(255,255,200,1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->MyMultiCell(
		$pole['pt_stk']['sirka']
		+$pole['pt_kdmin']['sirka']
		+$pole['pt_abymin']['sirka']
		+$pole['pt_verb']['sirka']
		,$headervyskaradku,"Trennen+Putzen\nS0011",'LTR','C',1);
	
	$pdfobjekt->MyMultiCell(
		$pole['st_stk']['sirka']
		+$pole['st_kdmin']['sirka']
		+$pole['st_abymin']['sirka']
		+$pole['st_verb']['sirka']
		,$headervyskaradku,"Strahlen\nS0041",'LTR','C',1);
	
	$pdfobjekt->MyMultiCell(
		$pole['g_stk']['sirka']
		+$pole['g_kdmin']['sirka']
		+$pole['g_abymin']['sirka']
		+$pole['g_verb']['sirka']
		,$headervyskaradku,"Farbe\nS0061",'LTR','C',1);
	
	$pdfobjekt->MyMultiCell(
		$pole['sonst_stk']['sirka']
		+$pole['sonst_kdmin']['sirka']
		+$pole['sonst_abymin']['sirka']
		+$pole['sonst_verb']['sirka']
		,$headervyskaradku,"Sonstiges\nS00XX",'LTR','C',1);

	$pdfobjekt->MyMultiCell(0,$headervyskaradku,"\n",'LTR','C',1);
	$pdfobjekt->Ln();$pdfobjekt->Ln();
	
	$pdfobjekt->SetFont("FreeSans", "", 8);
	
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 8);
}
 

// funkce pro vykresleni hlavicky na kazde strance
function pageheader_S430($pdfobjekt, $headervyskaradku) {

    global $cells_S430;
    $cells = $cells_S430;
    
    $pdfobjekt->SetFillColor(240, 240, 255, 1);
    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->Cell(0, $headervyskaradku, "Kalkulation (<S430)", '0', 1, 'L', 1);
    
    
    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    $fill = 1;
    $pdfobjekt->Cell($cells['abgnr']['sirka'], $headervyskaradku, "", '0', 0, 'R', 0);
    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $headervyskaradku, "", '0', 0, 'L', 0);
    $pdfobjekt->Cell($cells['preis']['sirka'], $headervyskaradku, "Preis [EUR]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Cell($cells['vzkd']['sirka'], $headervyskaradku, "VzKd [min]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->SetFont("FreeSans", "B", 5.5);
    $pdfobjekt->Cell($cells['gut_lfd_1_preis']['sirka'], $headervyskaradku, "Ist 2013 [EUR]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $headervyskaradku, "JB  2014 [EUR]", 'LRBT', 0, 'R', 1);
    $pdfobjekt->Ln();
}

/**
 *
 * @param TCPDF $pdfobjekt
 * @param type $vyskaRadku
 * @param type $childNodes 
 */
function header_tat_D510($pdfobjekt,$vyskaRadku,$childNodes){
        $pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=0;
	
	//prvni radek
	$pdfobjekt->Cell(10, 5, "", 'LRT', 0, 'R', $fill);
	$pdfobjekt->Cell(12, 5, "TatNr", 'LRT', 0, 'R', $fill);
	$pdfobjekt->Cell(10, 5, "taetkz", 'LRT', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, "", 'LRT', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, "Kz", 'LRT', 0, 'L', $fill);
	$pdfobjekt->Cell(60+60, 5, "Bezeichnung", 'LRT', 0, 'L', $fill);
	$pdfobjekt->Cell(15, 5, "vzkd", 'LRT', 0, 'R', $fill);
	$pdfobjekt->Cell(15, 5, "vzaby", 'LRT', 0, 'R', $fill);
	$pdfobjekt->Cell(20, 5, "Lager", 'LRT', 0, 'C', $fill);
	$pdfobjekt->Cell(0, 5, "", 'LRT', 0, 'L', $fill);
	$pdfobjekt->Ln();
	//druhy radek
	$pdfobjekt->Cell(10, 5, "dr", 'LRB', 0, 'R', $fill);
	$pdfobjekt->Cell(12, 5, "oper.", 'LRB', 0, 'R', $fill);
	$pdfobjekt->Cell(10, 5, "RE", 'LRB', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, "StatNr", 'LRB', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, "Gut", 'LRB', 0, 'L', $fill);
	$pdfobjekt->Cell(60+60, 5, "oznaceni", 'LRB', 0, 'L', $fill);
	$pdfobjekt->Cell(15, 5, "min/stk", 'LRB', 0, 'R', $fill);
	$pdfobjekt->Cell(15, 5, "min/stk", 'LRB', 0, 'R', $fill);
	$pdfobjekt->Cell(10, 5, "von", 'LRB', 0, 'C', $fill);
	$pdfobjekt->Cell(10, 5, "nach", 'LRB', 0, 'C', $fill);
	$pdfobjekt->Cell(0, 5, "Bedarf", 'LRB', 0, 'L', $fill);
	$pdfobjekt->Ln();
}
/**
 *
 * @param TCPDF $pdfobjekt
 * @param type $vyskaRadku
 * @param type $childNodes 
 */
function tat_D510($pdfobjekt,$vyskaRadku,$childNodes){
        $pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=0;
	$o = getValueForNode($childNodes, 'kzdruck')!=0?'x':'';
	$pdfobjekt->Cell(10, 5, $o, '1', 0, 'R', $fill);
	$pdfobjekt->Cell(12, 5, getValueForNode($childNodes, 'abgnr'), '1', 0, 'R', $fill);
	$pdfobjekt->Cell(10, 5, getValueForNode($childNodes, 'dtaetkz'), '1', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, getValueForNode($childNodes, 'statnr'), '1', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, getValueForNode($childNodes, 'kzgut'), '1', 0, 'L', $fill);
	$pdfobjekt->Cell(60, 5, getValueForNode($childNodes, 'tatbez_d'), '1', 0, 'L', $fill);
	$pdfobjekt->Cell(60, 5, getValueForNode($childNodes, 'tatbez_t'), '1', 0, 'L', $fill);
	$o = number_format(floatval(getValueForNode($childNodes, 'vzkd')), 4, ',', ' ');
	$pdfobjekt->Cell(15, 5, $o, '1', 0, 'R', $fill);
	$o = number_format(floatval(getValueForNode($childNodes, 'vzaby')), 4, ',', ' ');
	$pdfobjekt->Cell(15, 5, $o, '1', 0, 'R', $fill);
	$pdfobjekt->Cell(10, 5, getValueForNode($childNodes, 'lager_von'), '1', 0, 'L', $fill);
	$pdfobjekt->Cell(10, 5, getValueForNode($childNodes, 'lager_nach'), '1', 0, 'L', $fill);
	$pdfobjekt->Cell(0, 5, getValueForNode($childNodes, 'bedarf_typ'), '1', 0, 'L', $fill);
	
	$pdfobjekt->Ln();
}
/**
 *
 * @param TCPDF $pdfobjekt
 * @param int $vyskaRadku
 * @param $childNodes 
 */
function zahlavi_teil_D510($pdfobjekt,$vyskaRadku,$childNodes)
{
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=0;
	
	$x = $pdfobjekt->GetX();
	$y = $pdfobjekt->GetY();
	
	$p=$pdfobjekt;
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"TeilNr:",'LBT',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$p->Cell(30,$vyskaRadku,  getValueForNode($childNodes, 'teil'),'RBT',0,'L',$fill);
	
	$teil = getValueForNode($childNodes, 'teil');
	
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Original:",'LBT',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$p->Cell(50,$vyskaRadku,  getValueForNode($childNodes, 'teillang'),'RBT',0,'L',$fill);
	
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Bezeichnung:",'LBT',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$p->Cell(0,$vyskaRadku,  getValueForNode($childNodes, 'teilbez'),'RBT',0,'L',$fill);
	
	$p->Ln();
	
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Brutto-Gew [kg]:",'LBT',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = number_format(floatval(getValueForNode($childNodes, 'brgew')), 3, ',', ' ');
//	$p->Cell(30,$vyskaRadku,  $o,'RBT',0,'L',$fill);
//
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Netto-Gew [kg]:",'LBT',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = number_format(floatval(getValueForNode($childNodes, 'gew')), 3, ',', ' ');
//	$p->Cell(30,$vyskaRadku,  $o,'RBT',0,'L',$fill);
//
//	$p->Ln();
	
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Werkstoff:",'LBT',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'artguseisen');
	$p->Cell(30,$vyskaRadku,  $o,'RBT',0,'L',$fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Fremdauftr:",'LBT',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'fremdauftr_dkopf');
	$p->Cell(30,$vyskaRadku,  $o,'RBT',0,'L',$fill);
	
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Status:",'LBT',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'status');
	$p->Cell(30,$vyskaRadku,  $o,'RBT',0,'L',$fill);

	$p->Ln();
	
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Musterplatz:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = getValueForNode($childNodes, 'musterplatz');
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(40,$vyskaRadku,"Verpackungsmenge [Stk]:",'B',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'verpackungmenge');
	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
	
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Stk pro Gehaenge:",'B',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'stk_pro_gehaenge');
	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Restmengenverw.:",'B',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'restmengen_verw');
	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
	
	$p->Ln();
	
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Einlag.Datum:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = substr(getValueForNode($childNodes, 'mustervom'),0,10);
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
//
//	$p->Ln();
	$a = AplDB::getInstance();
	$letzteReklString = $a->getLetzteReklamationString($teil, 5);
	
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Reklamation:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = getValueForNode($childNodes, 'reklamation');
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(40,$vyskaRadku,"letzte Reklamation:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = getValueForNode($childNodes, 'letzte_reklamation');
	$o = $letzteReklString;
	$p->Cell(0,$vyskaRadku,  $o,'0',0,'L',$fill);
	$p->Ln();
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Freigabe1:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = getValueForNode($childNodes, 'mfreigabe1');
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Freigabe1 vom:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = substr(getValueForNode($childNodes, 'mfreigabe1vom'),0,10);
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Freigabe2:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = getValueForNode($childNodes, 'mfreigabe2');
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "", 7);
//	$p->Cell(30,$vyskaRadku,"Freigabe2 vom:",'B',0,'L',$fill);
//	$pdfobjekt->SetFont("FreeSans", "B", 8);
//	$o = substr(getValueForNode($childNodes, 'mfreigabe2vom'),0,10);
//	$p->Cell(30,$vyskaRadku,  $o,'B',0,'L',$fill);
//	$p->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Bemerkung:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'bemerk');
	$p->Cell(0,$vyskaRadku,  $o,'0',0,'L',$fill);
	$p->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Komplex:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'komplex');
	$p->Cell(0,$vyskaRadku,  $o,'0',0,'L',$fill);
	$p->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Preis-Gut:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = floatval(getValueForNode($childNodes, 'preis_stk_gut'));
	$o = number_format($o, 2, ',', ' ');
	$p->Cell(30,$vyskaRadku,  $o,'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Preis-Auss:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = floatval(getValueForNode($childNodes, 'preis_stk_auss'));
	$o = number_format($o, 2, ',', ' ');
	$p->Cell(30,$vyskaRadku,  $o,'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(30,$vyskaRadku,"Kosten-Auss:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = floatval(getValueForNode($childNodes, 'kosten_stk_auss'));
	$o = number_format($o, 2, ',', ' ');
	$p->Cell(30,$vyskaRadku,  $o,'0',0,'L',$fill);
	$p->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(40,$vyskaRadku,"Schwierigkeitsgrad_S0011:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'schwierigkeitsgrad_S11');
	$p->Cell(30,$vyskaRadku,  $o,'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(40,$vyskaRadku,"Schwierigkeitsgrad_S0051:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'schwierigkeitsgrad_S51');
	$p->Cell(30,$vyskaRadku,  $o,'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$p->Cell(40,$vyskaRadku,"Schwierigkeitsgrad_SO:",'0',0,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$o = getValueForNode($childNodes, 'schwierigkeitsgrad_SO');
	$p->Cell(30,$vyskaRadku,  $o,'0',0,'L',$fill);
	$p->Ln();
	$pdfobjekt->Rect($x, $y, $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT, $vyskaRadku*11);
}
////////////////////////////////////////////////////////////////////////////////////////////////////
//
function zahlavi_teil_S430($pdfobjekt,$vyskaRadku,$childNodes)
{
        global $cells_S430;
	$cells = $cells_S430;
	
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=1;
        $pdfobjekt->Cell($cells['abgnr']['sirka'],$vyskaRadku,getValueForNode($childNodes,"teilnr"),'LBT',0,'L',$fill);
        $pdfobjekt->Cell(
                $cells['abgnr_name']['sirka']+$cells['preis']['sirka'],
                $vyskaRadku,getValueForNode($childNodes,"teilbez"),'BT',0,'L',$fill);

	$a = AplDB::getInstance();
	$teilnr = getValueForNode($childNodes, 'teilnr');
	$musterRow = $a->getTeilDokument($teilnr, 0,FALSE);
	if($musterRow===NULL)
	    $musterText = "Muster: ????";
	else
	    $musterText = $musterRow['doku_nr']."/".$musterRow['doku_beschreibung']."/".$musterRow['einlag_datum']."/".$musterRow['musterplatz']."/".$musterRow['freigabe_am']."/".$musterRow['freigabe_vom'];

        //musterplatz
        $pdfobjekt->Cell(
                $cells['vzkd']['sirka']+130,
                $vyskaRadku,$musterText,'BLT',0,'L',$fill);

//        // freigabe1
//        $pdfobjekt->Cell(
//                130,
//                $vyskaRadku,
//                ' Freigabe1 am: '.getValueForNode($childNodes,"freigabe1vom").
//                ' von: '.getValueForNode($childNodes,"freigabe1")
//                .' Freigabe2 am: '.getValueForNode($childNodes,"freigabe2vom").
//                ' von: '.getValueForNode($childNodes,"freigabe2")
//                ,'BT',0,'L',$fill);

        // fremdauftr_dkopf
        $pdfobjekt->Cell(
                0,
                $vyskaRadku,
                '['.getValueForNode($childNodes,"fremdauftr_dkopf").']'
                ,'BTR',1,'L',$fill);

}


////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 *
 * @global array $cells
 * @global <type> $sum_zapati_sestava
 * @param TCPDF $pdfobjekt
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 * @param <type> $sumArray
 * @param <type> $teilchilds
 * @param <type> $kundechilds
 */
function zapati_teil_S430($pdfobjekt, $vyskaRadku, $rgb, $sumArray, $teilchilds, $kundechilds) {
//"abgnr"
//"abgnr_name"
//"preis"
//"vzkd"
//"ln"

    global $cells_S430;
    $cells = $cells_S430;

    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    //apl
    $pdfobjekt->SetFillColor(255, 255, 240, 1);
    $teillang = getValueForNode($teilchilds, 'teillang');
    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, $teillang, '1', 0, 'L', 0);
    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Summe [APL]: ", 'LBT', 0, 'L', $fill);
    $preis = $sumArray['preis'];
    $obsah = number_format($preis, 4, ',', ' ');
    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

    $vzkd = $sumArray['vzkd'];
    $obsah = number_format($vzkd, 4, ',', ' ');
    $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

    $b2011_preis = $sumArray['gut_lfd_1_preis'];
    $obsah = number_format($b2011_preis, 0, ',', ' ');
    $pdfobjekt->Cell($cells['gut_lfd_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

    $b2012_preis = $sumArray['bed_lfd_j_preis'];
    $obsah = number_format($b2012_preis, 0, ',', ' ');
    $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);

    $pdfobjekt->SetFont("FreeSans", "B", 7);
    $obsah = "Kg Brutto / Netto";
    $pdfobjekt->Cell(40, $vyskaRadku, $obsah, 'LBTR', 0, 'L', $fill);

    $obsah = number_format(getValueForNode($teilchilds, 'brgew'), 2, ',', ' ') . " / " . number_format(getValueForNode($teilchilds, 'gew'), 2, ',', ' ');

    $pdfobjekt->Cell(20, $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
    $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);

    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->SetFillColor(240, 255, 240, 1);
    $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Jahresbedarf Stk", 'LBT', 0, 'L', $fill);
    $obsah = '';
    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);
    $obsah = '';
    $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BTR', 0, 'R', $fill);
    $obsah = number_format(getValueForNode($teilchilds, 'gut_lfd_1'), 0, ',', ' ');
    $pdfobjekt->Cell($cells['gut_lfd_1_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
    $obsah = number_format(getValueForNode($teilchilds, 'jb_lfd_j'), 0, ',', ' ');
    $pdfobjekt->Cell($cells['bed_lfd_j_preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
    // novy radek
    $pdfobjekt->Cell(0, $vyskaRadku, "", '0', 1, 'R', 0);
    $pdfobjekt->SetFont("FreeSans", "B", 8);
    $pdfobjekt->SetFillColor(255, 255, 255, 1);
    $pdfobjekt->Cell($cells['abgnr']['sirka'], $vyskaRadku, "", '0', 0, 'L', 0);
    $pdfobjekt->Cell($cells['abgnr_name']['sirka'], $vyskaRadku, "Jahresbedarf to", 'LBT', 0, 'L', $fill);
    $obsah = '';
    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'BT', 0, 'R', $fill);
    $obsah = '';
    $pdfobjekt->Cell($cells['vzkd']['sirka'], $vyskaRadku, $obsah, 'BTR', 0, 'R', $fill);
    $tonnen2011 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'gut_lfd_1')) / 1000;
    $obsah = number_format($tonnen2011, 0, ',', ' ');
    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
    $tonnen2012 = floatval(getValueForNode($teilchilds, 'gew')) * intval(getValueForNode($teilchilds, 'jb_lfd_j')) / 1000;
    $obsah = number_format($tonnen2012, 0, ',', ' ');
    $pdfobjekt->Cell($cells['preis']['sirka'], $vyskaRadku, $obsah, 'LBTR', 0, 'R', $fill);
    $pdfobjekt->Ln();
}


////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
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

////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
function detaily_S430($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist,$abgnr,$vzkd)
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

                if(($abgnr==95)){
                    $pdfobjekt->SetFont("FreeSans", "I", 7);
                    if($vzkd<0)
                        $pdfobjekt->SetTextColor(255,0,0);
                    else
                        $pdfobjekt->SetTextColor(0,0,0);
                    }
                else
                    $pdfobjekt->SetFont("FreeSans", "", 7);

		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
        $pdfobjekt->SetTextColor(0,0,0);
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



function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
	}
}
				

function test_pageoverflow_nopage($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
	    return TRUE;
	}
	else
	    return FALSE;
}

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
                pageheader($pdfobjekt, 5);
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
	}
}

function telo_S820($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
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
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}

function test_pageoverflow_S310($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader_S310($pdfobjekt,$cellhead,$vysradku);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}

function zahlavi_auftrag_S310($pdfobjekt,$vyskaradku,$rgb,$cells_header,$auftragsnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);

	$pdfobjekt->Cell(0,$vyskaradku,"AuftragsNr: ".$auftragsnr,'1',1,'L',$fill);
	
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_auftrag_S310($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $auftragsnr="") {
    global $cells_S310;
    $c = $cells_S310;

    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";
    //$obsah=number_format($obsah,0,',',' ');
    //$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

    $pdfobjekt->Cell(
	    $c['tat']['sirka']
	    +$c['datum']['sirka']
	    +$c['pal']['sirka']
	    +$c['oe']['sirka']
	    +$c['persnr']['sirka']
	    +$c['name']['sirka']
	    , $vyskaradku, $popis . " " . $auftragsnr, 'B', 0, 'L', $fill);


    $obsah = $pole['stk'];
    $obsah = "";//number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['stk']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss_stk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['auss_stk']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //auss_typ
    $pdfobjekt->Cell(
	    $c['auss_typ']['sirka']
	    , $vyskaradku, "", 'B', 0, 'L', $fill);

    //vzkd_stk
    $pdfobjekt->Cell(
	    $c['vzkd_stk']['sirka']
	    , $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['vzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['vzkd']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //vzaby_stk
    $pdfobjekt->Cell(
	    $c['vzaby_stk']['sirka']
	    , $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['vzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['vzaby']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['verb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['verb']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = "";
    $pdfobjekt->Cell(
	    $c['d1']['sirka']
	    , $vyskaradku, $obsah, 'B', 1, 'R', $fill);

//    $pdfobjekt->Ln();

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

function zapati_taetigkeit_S310($pdfobjekt, $node, $vyskaradku, $popis, $rgb, $pole, $tatnr) {
//"tat" 
//"datum" 
//"pal" 
//"oe"
//"persnr" 
//"name" 
//"stk" 
//"auss_stk" 
//"auss_typ" 
//"vzkd_stk" 
//"vzkd" 
//"vzaby_stk" 
//"vzaby" 
//"verb" 

    global $cells_S310;
    $c = $cells_S310;
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    $fill = 1;

    // dummy
    $obsah = "";
    $pdfobjekt->Cell(
	    $c['tat']['sirka']
	    +$c['datum']['sirka']
	    +$c['pal']['sirka']
	    +$c['oe']['sirka']
	    +$c['persnr']['sirka']
	    +$c['name']['sirka']
	    ,$vyskaradku, $popis . " " . $tatnr, 'B', 0, 'L', $fill);


    $obsah = $pole['stk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['stk']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['auss_stk'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['auss_stk']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //auss_typ
    $pdfobjekt->Cell(
	    +$c['auss_typ']['sirka']
	    , $vyskaradku, "", 'B', 0, 'L', $fill);

    //vzkd_stk
    $pdfobjekt->Cell(
	    +$c['vzkd_stk']['sirka']
	    , $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['vzkd'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['vzkd']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    //vzaby_stk
    $pdfobjekt->Cell(
	    $c['vzaby_stk']['sirka']
	    , $vyskaradku, "", 'B', 0, 'L', $fill);

    $obsah = $pole['vzaby'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['vzaby']['sirka']
	    , $vyskaradku, $obsah, 'B', 0, 'R', $fill);

    $obsah = $pole['verb'];
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdfobjekt->Cell(
	    $c['verb']['sirka']
	    , $vyskaradku, $obsah, 'B', 1, 'R', $fill);

    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
}

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S847 - Teileinfo", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-8, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 12));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();
pageheader_S430($pdf, 5);
// zacinam po dilech
$kunden = $domxml_1->getElementsByTagName("kunde");
foreach ($kunden as $kunde) {
    $kundeChilds = $kunde->childNodes;
    $teile = $kunde->getElementsByTagName("teil");
    foreach ($teile as $teil) {
        nuluj_sumy_pole($sum_zapati_teil);
        $teilChildNodes = $teil->childNodes;
        $taetigkeiten = $teil->getElementsByTagName("tat");
        $tatCount = 0;
        foreach ($taetigkeiten as $tat) $tatCount++;
        test_pageoverflow_noheader($pdf, 5*($tatCount+1+3));
        
        zahlavi_teil_S430($pdf, 5, $teilChildNodes);
        foreach ($taetigkeiten as $tat) {
            $tatChildNodes = $tat->childNodes;
            $abgnr = getValueForNode($tatChildNodes, 'abgnr');
            $vzkd = getValueForNode($tatChildNodes, 'vzkd');
            detaily_S430($pdf, $cells_S430, 5, array(255, 255, 255), $tatChildNodes,$abgnr,$vzkd);
            foreach ($sum_zapati_teil as $key => $value) {
                $hodnota = getValueForNode($tatChildNodes, $key);
                $sum_zapati_teil[$key]+=$hodnota;
            }
        }
        zapati_teil_S430($pdf, 5, array(255, 255, 240), $sum_zapati_teil, $teilChildNodes, $kundeChilds);
    }
}

$pdf->Ln();
//D510 aplinfo

$pdf->SetFillColor(240, 240, 255, 1);
$pdf->SetFont("FreeSans", "B", 8);
$pdf->Cell(0, 5, "Stammdaten (<D510)", '0', 1, 'L', 1);

$teile = $domxml_2->getElementsByTagName("t");
foreach ($teile as $teil) {
    $teilChilds = $teil->childNodes;
    zahlavi_teil_D510($pdf, 5, $teilChilds);
    $tats = $teil->getElementsByTagName("tat");
    $pdf->SetFillColor(240, 240, 255, 1);
    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->Cell(0, 5, "Arbeitsplan (<D510)", '0', 1, 'L', 1);
    header_tat_D510($pdf,5,$tatChilds);
    foreach ($tats as $tat) {
	$tatChilds = $tat->childNodes;
	if(test_pageoverflow_nopage($pdf,5)) header_tat_D510($pdf,5,$tatChilds);
	tat_D510($pdf,5,$tatChilds);
    }
}

// dokumenty
$pdf->Ln();

$pdf->SetFillColor(240, 240, 255, 1);
$pdf->SetFont("FreeSans", "B", 8);
$pdf->Cell(0, 5, "Teiledokumentation", '0', 1, 'L', 1);
pageheader_dokumenty($pdf, $cells_dokument, 5);
$docs = $domxml_5->getElementsByTagName("dokument");
foreach ($docs as $doc){
    $docChilds = $doc->childNodes;
    detaily_dokumenty($pdf, $cells_dokument, 5, array(255,255,255), $docChilds);
}
//------------------------------------------------------------------------------

//S820
$pdf->AddPage();
$pdf->SetFillColor(240, 240, 255, 1);
$pdf->SetFont("FreeSans", "B", 8);
$pdf->Cell(0, 5, "Hitliste (<S820)", '0', 1, 'L', 1);

pageheader_S820($pdf,$cells_header_S820,2.5,"");
$teile = $domxml_3->getElementsByTagName("t");
foreach ($teile as $teil) {
    $teilChildNodes = $teil->childNodes;
    telo_S820($pdf, $cells_S820, 5, array(255, 255, 255), "", $teilChildNodes);
}

//S310
// prehled S310 vzdy zacne na nove strance
$pdf->AddPage();
$pdf->SetFillColor(240, 240, 255, 1);
$pdf->SetFont("FreeSans", "B", 8);
$pdf->Cell(0, 5, "Neueste Rūckmeldungen (<S310)", '0', 1, 'L', 1);

pageheader_S310($pdf,$cells_header_S310,5);
// a ted pujdu po zakazkach
$auftraege=$domxml_4->getElementsByTagName("auftrag");
foreach ($auftraege as $auftrag) {
    $auftragsnr = $auftrag->getElementsByTagName("auftragsnr")->item(0)->nodeValue;
    test_pageoverflow_S310($pdf, 5, $cells_header_S310);
    zahlavi_auftrag_S310($pdf, 5, array(200, 255, 200), $cells_header_S310, $auftragsnr);
    nuluj_sumy_pole($sum_zapati_auftrag_array_S310);
    $teile = $auftrag->getElementsByTagName("teil");
    foreach ($teile as $teil) {
	$taetigkeiten = $teil->getElementsByTagName("taetigkeit");
	nuluj_sumy_pole($sum_zapati_teil_array_S310);
	foreach ($taetigkeiten as $taetigkeit) {
	    nuluj_sumy_pole($sum_zapati_taetigkeit_array_S310);
	    $tatnr = $taetigkeit->getElementsByTagName("tatnr")->item(0)->nodeValue;
	    $positionen = $taetigkeit->getElementsByTagName("position");
	    foreach ($positionen as $position) {
		$position_childs = $position->childNodes;
		if (test_pageoverflow_S310($pdf, 4, $cells_header_S310))
		    zahlavi_auftrag_S310($pdf, 5, array(200, 255, 200), $cells_header_S310, $auftragsnr);
		telo_S310($pdf, $cells_S310, 4, array(255, 255, 255), "", $position_childs);
		foreach ($sum_zapati_taetigkeit_array_S310 as $key => $prvek) {
		    $hodnota = $position->getElementsByTagName($key)->item(0)->nodeValue;
		    $sum_zapati_taetigkeit_array_S310[$key]+=$hodnota;
		}
	    }
	    if (test_pageoverflow_S310($pdf, 5, $cells_header_S310))
		zahlavi_auftrag_S310($pdf, 5, array(255, 255, 200), $cells_header_S310, $auftragsnr);

	    zapati_taetigkeit_S310($pdf, $taetigkeit, 5, "Summe Taetigkeit", array(235, 235, 235), $sum_zapati_taetigkeit_array_S310, $tatnr);
	    foreach ($sum_zapati_teil_array_S310 as $key => $prvek) {
		$hodnota = $sum_zapati_taetigkeit_array_S310[$key];
		$sum_zapati_teil_array_S310[$key]+=$hodnota;
	    }
	}
	foreach ($sum_zapati_auftrag_array_S310 as $key => $prvek) {
	    $hodnota = $sum_zapati_teil_array_S310[$key];
	    $sum_zapati_auftrag_array_S310[$key]+=$hodnota;
	}
    }
    //
    foreach ($sum_zapati_sestava_array_S310 as $key => $prvek) {
	$hodnota = $sum_zapati_auftrag_array_S310[$key];
	$sum_zapati_sestava_array_S310[$key]+=$hodnota;
    }

    if (test_pageoverflow_S310($pdf, 5, $cells_header_S310))
	zahlavi_auftrag_S310($pdf, 5, array(255, 255, 200), $cells_header_S310, $auftragsnr);
    zapati_auftrag_S310($pdf, $pers, 5, "Summe Auftrag", array(200, 255, 200), $sum_zapati_auftrag_array_S310, $auftragsnr);
}
if (test_pageoverflow_S310($pdf, 5, $cells_header_S310))
    zahlavi_auftrag_S310($pdf, 5, array(255, 255, 200), $cells_header_S310, $auftragsnr);
zapati_auftrag_S310($pdf, $pers, 5, "Summe Teil", array(235, 235, 235), $sum_zapati_sestava_array_S310);


$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
