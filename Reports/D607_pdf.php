<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D607";
$doc_subject = "D607 Report";
$doc_keywords = "D607";

// necham si vygenerovat XML


$parameters=$_GET;

$von1 = $_GET['von'];
$bis1 = $_GET['bis'];

$von="P".$_GET['von'];
$bis="P".$_GET['bis'];
$odstrankovatPoDilu = $_GET['teilpager'];
$reporttyp = $_GET['reporttyp'];
$teil = $_GET['teil'];

if(!strcmp($reporttyp, "Info an Kunden"))
    $bKunde = TRUE;
else
    $bKunde=FALSE;


require_once('D607_xml.php');


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

"import" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"abnummer" 
=> array ("popis"=>"","sirka"=>16,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"","sirka"=>18,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"import_pal" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"import_stk" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_T_k"
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_P_k"
=> array ("popis"=>"","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_St_k"
=> array ("popis"=>"","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_G_k"
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_E_k"
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss2" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss4" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss6" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_Gtat"
=> array ("popis"=>"","sirka"=>9,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"GDiff" 
=> array ("popis"=>"","sirka"=>9,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"imp_gew" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"bemerkung" 
=> array ("popis"=>"","sirka"=>45,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"cnt_S0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0011T" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0011P" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0041" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0051" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0061" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_header = 
array(

"export_lief" 
=> array ("popis"=>"IM\nAuftr\n","sirka"=>11,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"abnummer" 
=> array ("popis"=>"AB\nNummer\n","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"Teil\n\n","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"import_pal" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Palette\nIMP EXP Stk\nexp","sirka"=>30,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"import_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nIM","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_T_k"
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nTr","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_P_k"
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nPU","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_St_k"
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nSt","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_G_k"
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nF","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_E_k"
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nE","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n(2)","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss4" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n(4)","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss6" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n(6)","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"GDiff" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nG+A-\nIM","sirka"=>9,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"imp_gew" 
=> array ("nf"=>array(2,',',' '),"popis"=>"Gew\n(to)\nIMP","sirka"=>9,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0011T" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0011P" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0041" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0051" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

//"S0061" 
//=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nGesamt\nvzkd","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);



$sum_zapati_sestava_array = array(	
                                        "import_stk"=>0,
                                        "sum_stk_T_k"=>0,
                                        "sum_stk_P_k"=>0,
                                        "sum_stk_St_k"=>0,
                                        "sum_stk_G_k"=>0,
                                        "sum_stk_E_k"=>0,
                                        "auss2"=>0,
                                        "auss4"=>0,
                                        "auss6"=>0,
					"imp_gew"=>0,
					"sumS0011T"=>0,
					"sumS0011P"=>0,
					"sumS0041"=>0,
					"sumS0051"=>0,
					"sumS0061"=>0,
					"sumvzkd"=>0,
                                        "export_stk"=>0,
                                        "GDiff"=>0,
                                        "sum_stk_Gtat"=>0,
					"sum_Ggew"=>0,
					"a2gew"=>0,
					"a4gew"=>0,
					"a6gew"=>0,
				);
global $sum_zapati_sestava_array;

$sum_zapati_termin_array = array(	
                                        "import_stk"=>0,
                                        "sum_stk_T_k"=>0,
                                        "sum_stk_P_k"=>0,
                                        "sum_stk_St_k"=>0,
                                        "sum_stk_G_k"=>0,
                                        "sum_stk_E_k"=>0,
                                        "auss2"=>0,
                                        "auss4"=>0,
                                        "auss6"=>0,
					"imp_gew"=>0,
					"sumS0011T"=>0,
					"sumS0011P"=>0,
					"sumS0041"=>0,
					"sumS0051"=>0,
					"sumS0061"=>0,
					"sumvzkd"=>0,
                                        "export_stk"=>0,
                                        "GDiff"=>0,
                                        "sum_stk_Gtat"=>0,
					"sum_Ggew"=>0,
					"a2gew"=>0,
					"a4gew"=>0,
					"a6gew"=>0,
				);

$sum_zapati_teil_array = array(
                                        "import_stk"=>0,
                                        "sum_stk_T_k"=>0,
                                        "sum_stk_P_k"=>0,
                                        "sum_stk_St_k"=>0,
                                        "sum_stk_G_k"=>0,
                                        "sum_stk_E_k"=>0,
                                        "auss2"=>0,
                                        "auss4"=>0,
                                        "auss6"=>0,
					"imp_gew"=>0,
					"sumS0011T"=>0,
					"sumS0011P"=>0,
					"sumS0041"=>0,
					"sumS0051"=>0,
					"sumS0061"=>0,
					"sumvzkd"=>0,
                                        "export_stk"=>0,
                                        "GDiff"=>0,
                                        "sum_stk_Gtat"=>0,
					"sum_Ggew"=>0,
					"a2gew"=>0,
					"a4gew"=>0,
					"a6gew"=>0,
				);

global $sum_zapati_termin_array;

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
    global $bKunde;
    
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	
		$fill=1;
	// 1.radek
        if ($bKunde) {
	$pdfobjekt->Cell($pole['import']['sirka'], $headervyskaradku, "", 'LRT', 0, 'L', 1);
	$pdfobjekt->Cell($pole['abnummer']['sirka'], $headervyskaradku, "Verpackungs-", 'LRT', 0, 'L', 1);
    } else {
	$pdfobjekt->Cell($pole['import']['sirka'], $headervyskaradku, "IM-", 'LRT', 0, 'L', 1);
	$pdfobjekt->Cell($pole['abnummer']['sirka'], $headervyskaradku, "AB", 'LRT', 0, 'L', 1);
    }

	$pdfobjekt->Cell($pole['teilnr']['sirka'],$headervyskaradku,"Teil",'LRT',0,'C',$fill);
	
	if ($bKunde) {
	    $pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"",'LRT',0,'C',$fill);
	}
	else{
	    $pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"Palette",'LRT',0,'C',$fill);
	}
	
	$pdfobjekt->Cell($pole['import_stk']['sirka']+$pole['sum_stk_T_k']['sirka']+$pole['sum_stk_P_k']['sirka']+$pole['sum_stk_St_k']['sirka']+$pole['sum_stk_G_k']['sirka']+$pole['sum_stk_E_k']['sirka']+$pole['auss2']['sirka']+$pole['auss4']['sirka']+$pole['auss6']['sirka'],$headervyskaradku,"Stueckzahl",'LT',0,'C',$fill);
//	$pdfobjekt->Cell($pole['auss2']['sirka']+$pole['auss4']['sirka']+$pole['auss6']['sirka'],$headervyskaradku,"Ausschuss",'LRT',0,'C',$fill);
        $pdfobjekt->Cell($pole['sum_stk_Gtat']['sirka'],$headervyskaradku,"",'RT',0,'R',$fill);
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$headervyskaradku,"G+A-",'LRT',0,'R',$fill);
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$headervyskaradku,"to",'LRT',0,'C',$fill);
	if($bKunde)
	    $pdfobjekt->Cell(0,$headervyskaradku,"",'LRT',1,'C',$fill);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$headervyskaradku,"",'LRT',0,'C',$fill);
	
	if (!$bKunde) {
	$pdfobjekt->Cell(
		$pole['cnt_S0011T']['sirka'] + $pole['S0011T']['sirka'] + $pole['sumS0011T']['sirka']
		+ $pole['cnt_S0011P']['sirka'] + $pole['S0011P']['sirka'] + $pole['sumS0011P']['sirka']
		+ $pole['cnt_S0041']['sirka'] + $pole['S0041']['sirka'] + $pole['sumS0041']['sirka']
		+ $pole['cnt_S0051']['sirka'] + $pole['S0051']['sirka'] + $pole['sumS0051']['sirka']
		+ $pole['cnt_S0061']['sirka'] + $pole['S0061']['sirka'] + $pole['sumS0061']['sirka']
		, $headervyskaradku, "VzKd [min]", 'LRT', 0, 'C', $fill);
	$pdfobjekt->Cell(0, $headervyskaradku, "", 'LRT', 1, 'C', $fill);
	}

    // 2.radek
	if ($bKunde) {
	$pdfobjekt->Cell($pole['import']['sirka'],$headervyskaradku,"",'LR',0,'L',$fill);
	$pdfobjekt->Cell($pole['abnummer']['sirka'],$headervyskaradku,"einheit",'LR',0,'L',1);
	}
	else{
	$pdfobjekt->Cell($pole['import']['sirka'],$headervyskaradku,"Auftr",'LR',0,'L',$fill);
	$pdfobjekt->Cell($pole['abnummer']['sirka'],$headervyskaradku,"Nummer",'LR',0,'L',1);
	}
	
	$pdfobjekt->Cell($pole['teilnr']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	if ($bKunde) {
	    $pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	}
	else{
	    $pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"IMP",'LR',0,'C',$fill);
	}
	
	$pdfobjekt->Cell($pole['import_stk']['sirka']+$pole['sum_stk_T_k']['sirka']+$pole['sum_stk_P_k']['sirka']+$pole['sum_stk_St_k']['sirka']+$pole['sum_stk_G_k']['sirka']+$pole['sum_stk_E_k']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	$pdfobjekt->Cell($pole['auss2']['sirka']+$pole['auss4']['sirka']+$pole['auss6']['sirka'],$headervyskaradku,"Ausschuss",'LR',0,'C',$fill);
        $pdfobjekt->Cell($pole['sum_stk_Gtat']['sirka'],$headervyskaradku,"",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$headervyskaradku,"IM",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	if($bKunde)
	    $pdfobjekt->Cell(0,$headervyskaradku,"Bemerkung",'LR',1,'C',$fill);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$headervyskaradku,"Bemerkung",'LR',0,'C',$fill);
	
	if (!$bKunde) {
	$pdfobjekt->Cell(
		$pole['cnt_S0011T']['sirka']+$pole['S0011T']['sirka']
		+$pole['sumS0011T']['sirka']
		,$headervyskaradku,"S0011T",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(
		$pole['cnt_S0011P']['sirka']+$pole['S0011P']['sirka']
		+$pole['sumS0011P']['sirka']
		,$headervyskaradku,"S0011P",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(
		$pole['cnt_S0041']['sirka']+$pole['S0041']['sirka']
		+$pole['sumS0041']['sirka']
		,$headervyskaradku,"S0041",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(
		$pole['cnt_S0051']['sirka']+$pole['S0051']['sirka']
		+$pole['sumS0051']['sirka']
		,$headervyskaradku,"S0051",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(
		$pole['cnt_S0061']['sirka']+$pole['S0061']['sirka']
		+$pole['sumS0061']['sirka']
		,$headervyskaradku,"S0061",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(0,$headervyskaradku,"Gesamt",'LR',1,'R',$fill);
	    
	}
// TODO	
	// 3.radek
	$pdfobjekt->Cell($pole['import']['sirka'],$headervyskaradku,"",'LR',0,'L',$fill);
	$pdfobjekt->Cell($pole['abnummer']['sirka'],$headervyskaradku,"VPE",'LR',0,'R',1);
	$pdfobjekt->Cell($pole['teilnr']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['export_pal']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
//	$pdfobjekt->Cell($pole['export_stk']['sirka'],$headervyskaradku,"Exp",'LR',0,'C',$fill);
//
	$pdfobjekt->Cell($pole['import_stk']['sirka'],$headervyskaradku,"IM",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_T_k']['sirka'],$headervyskaradku,"Tr",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_P_k']['sirka'],$headervyskaradku,"Pu",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_St_k']['sirka'],$headervyskaradku,"St",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_G_k']['sirka'],$headervyskaradku,"F",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_E_k']['sirka'],$headervyskaradku,"E",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['auss2']['sirka'],$headervyskaradku,"(2)",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['auss4']['sirka'],$headervyskaradku,"(4)",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['auss6']['sirka'],$headervyskaradku,"(6)",'LR',0,'R',$fill);

        $pdfobjekt->Cell($pole['sum_stk_Gtat']['sirka'],$headervyskaradku,"G(ut)",'LR',0,'C',$fill);
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$headervyskaradku,"IM",'LR',0,'C',$fill);
	
	if($bKunde)
	    $pdfobjekt->Cell(0,$headervyskaradku,"",'LRB',1,'C',$fill);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$headervyskaradku,"",'LRB',0,'C',$fill);
	
	if (!$bKunde) {
	$pdfobjekt->Cell($pole['cnt_S0011T']['sirka'],$headervyskaradku,"n",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0011P']['sirka'],$headervyskaradku,"n",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0041']['sirka'],$headervyskaradku,"n",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0051']['sirka'],$headervyskaradku,"n",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0061']['sirka'],$headervyskaradku,"n",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(0,$headervyskaradku,"VzKd",'LR',1,'R',$fill);
	    
	}
	
	/*
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	*/
	$pdfobjekt->SetFont("FreeSans", "", 6);
}

/**
 * funkce pro vykresleni sekci - zahlavi, zapati podle seskupovaci grupy. 
 */

////////////////////////////////////////////////////////////////////////////////////////////////////
//
function zahlavi_termin($pdfobjekt,$childNodes,$pole,$vyskaradku)
{

	$pdfobjekt->SetFillColor(255,255,100,1);
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(		
		$pole['import']['sirka']+
		$pole['abnummer']['sirka']
		,$vyskaradku,"geplant mit: ",'LBT',0,'L',1);
	
	
	$pdfobjekt->SetFont("FreeSans", "B", 10);
        $termin = substr(getValueForNode($childNodes,"terminF"),1);
        $a = AplDB::getInstance();
        $bemerkungA = $a->getAuftragInfoArray($termin);
	$zielort = $a->getZielortAuftrag($termin);
	
        if($bemerkungA===NULL)
            $bemerkung='';
        else
            $bemerkung = $bemerkungA[0]['bemerkung'];

	$pdfobjekt->Cell(0,$vyskaradku,getValueForNode($childNodes,"terminF")." ( ".getValueForNode($childNodes,"geplanntdatum")." ) $zielort $bemerkung",'RBT',1,'L',1);
}

/*
"import" 
"teilnr" 
"import_pal" 
"export_pal" 
"export_stk" 
"import_stk" 
"sum_stk_T" 
"sum_stk_P" 
"sum_stk_St" 
"sum_stk_G" 
"sum_stk_E" 
"auss2" 
"auss4" 
"auss6" 
"GDiff" 
"imp_gew" 
"cnt_S0011T" 
"S0011T" 
"sumS0011T" 
"cnt_S0011P" 
"S0011P" 
"sumS0011P" 
"cnt_S0041" 
"S0041" 
"sumS0041" 
"cnt_S0051" 
"S0051" 
"sumS0051" 
"cnt_S0061" 
"S0061" 
"sumS0061" 
"sumvzkd" 
*/

function zapati_termin($pdfobjekt,$childNodes,$pole,$sumy,$vyskaradku)
{
    global $reporttyp;
    global $bKunde;
    
        $termin  = getValueForNode($childNodes, 'terminF');
	$pdfobjekt->SetFillColor(255,255,240,1);
	//	$pdfobjekt->Cell(
//						$pole['import']['sirka']+
//						$pole['abnummer']['sirka']+
//						$pole['teilnr']['sirka']+
//						$pole['import_pal']['sirka']+
////						$pole['export_pal']['sirka']+
//						$pole['sum_stk_Gtat']['sirka']+
//						$pole['import_stk']['sirka']+
//						$pole['sum_stk_T_k']['sirka']+
//						$pole['sum_stk_P_k']['sirka']+
//						$pole['sum_stk_St_k']['sirka']+
//						$pole['sum_stk_G_k']['sirka']+
//						$pole['sum_stk_E_k']['sirka']+
//						$pole['auss2']['sirka']+
//						$pole['auss4']['sirka']+
//						$pole['auss6']['sirka']+
//						$pole['GDiff']['sirka']
//						,$vyskaradku,
//						"Summe Termin ( $termin ) :",
//						'LRBT',
//						0,
//						'L'
//						,1
//						);
	
        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(
		$pole['import']['sirka']+
		$pole['abnummer']['sirka']+5
		,$vyskaradku,"Sum Tonen/Stk./VzKd",'LBTR',0,'L',1);
	
	$pdfobjekt->SetFont("FreeSans", "B", 10);
        $termin = substr(getValueForNode($childNodes,"terminF"),1);
        $a = AplDB::getInstance();
        $bemerkungA = $a->getAuftragInfoArray($termin);
        if($bemerkungA===NULL)
            $bemerkung='';
        else
            $bemerkung = $bemerkungA[0]['bemerkung'];

	$zielort = $a->getZielortAuftrag($termin);
	$pdfobjekt->Cell(
		$pole['teilnr']['sirka']+
		$pole['import_pal']['sirka']+
		$pole['import_stk']['sirka']+
		$pole['sum_stk_T_k']['sirka']+
		$pole['sum_stk_P_k']['sirka']+
		$pole['sum_stk_St_k']['sirka']+
		$pole['sum_stk_G_k']['sirka']+
		$pole['sum_stk_E_k']['sirka']
		-5
//		$pole['auss2']['sirka']+
//		$pole['auss4']['sirka']+
//		$pole['auss6']['sirka']+
//		$pole['GDiff']['sirka']
		,$vyskaradku,
		getValueForNode($childNodes,"terminF")." ( ".getValueForNode($childNodes,"geplanntdatum")." ) $zielort"
		,'RBT',0,'L',1);
	
	$pdfobjekt->SetFont("FreeSans", "B", 6.5);
	
	$obsah=number_format($sumy['a2gew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['auss2']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah=number_format($sumy['a4gew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['auss4']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah=number_format($sumy['a6gew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['auss6']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah=number_format($sumy['sum_Ggew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['sum_stk_Gtat']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah="";
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);
	
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	
	$obsah=number_format($sumy['imp_gew'],2,',',' ');
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);
	if($bKunde)
	    $pdfobjekt->Cell(0,$vyskaradku,'','LRBT',1,'R',1);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$vyskaradku,'','LRBT',0,'R',1);

	if(!$bKunde){
	$pdfobjekt->Cell(
						$pole['cnt_S0011T']['sirka']+
						$pole['S0011T']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011T'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0011P']['sirka']+
						$pole['S0011P']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011P'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);
	
	$pdfobjekt->Cell(
						$pole['cnt_S0041']['sirka']+
						$pole['S0041']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0041'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);
	
	$pdfobjekt->Cell(
						$pole['cnt_S0051']['sirka']+
						$pole['S0051']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0051'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);
	
	$pdfobjekt->Cell(
						$pole['cnt_S0061']['sirka']+
						$pole['S0061']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0061'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);
	
	$obsah=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BTR',1,'R',1);
	    
	}

//        if($reporttyp!='nur Summen') $pdfobjekt->Ln(2);
        $pdfobjekt->Ln(2);
}

function zapati_teil($pdfobjekt,$childNodes,$teilnr,$pole,$sumy,$vyskaradku,$teilChilds)
{

    global $reporttyp;
    global $bKunde;
    
	$pdfobjekt->SetFillColor(240,255,240,1);
	$pdfobjekt->SetFont("FreeSans", "B", 7);

        $verpackungMenge = getValueForNode($teilChilds, 'verpackungmenge');
	$restMenge = getValueForNode($teilChilds, 'restmengen_verw');
	$gew = number_format(floatval(getValueForNode($teilChilds, 'gew')),1,',',' ');
     	$pdfobjekt->Cell(
						$pole['import']['sirka']+5
						,$vyskaradku,
						"kg/Stk: ".$gew,
						'LBT',
						0,
						'L'
						,1
						);
         $pdfobjekt->Cell(
						$pole['abnummer']['sirka']-5
						,$vyskaradku,
						"$verpackungMenge",
						'BT',
						0,
						'R'
						,1
						);
     	$pdfobjekt->Cell(
						$pole['teilnr']['sirka']
						,$vyskaradku,
						"$teilnr",
						'LBT',
						0,
						'L'
						,1
						);

         $pdfobjekt->Cell(
						$pole['import_pal']['sirka']
//						$pole['export_pal']['sirka']
						,$vyskaradku,
						"$restMenge",
						'LBT',
						0,
						'R'
						,1
						);

        $obsah=number_format($sumy['import_stk'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['import_stk']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_T_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_T_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_P_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_P_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_St_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_St_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_G_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_G_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_E_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_E_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['auss2'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['auss2']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['auss4'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['auss4']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['auss6'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['auss6']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_Gtat'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_Gtat']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);

        $obsah=number_format($sumy['GDiff'],0,',',' ');
	if($bKunde) $obsah="";
	
        $pdfobjekt->Cell(
						$pole['GDiff']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);


	$obsah=number_format($sumy['imp_gew'],2,',',' ');
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);
	
	if($bKunde)
	    $pdfobjekt->Cell(0,$vyskaradku,'','LRBT',1,'R',1);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$vyskaradku,'','LRBT',0,'R',1);

	if(!$bKunde){
	$pdfobjekt->Cell(
						$pole['cnt_S0011T']['sirka']+
						$pole['S0011T']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011T'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0011P']['sirka']+
						$pole['S0011P']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011P'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0041']['sirka']+
						$pole['S0041']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0041'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0051']['sirka']+
						$pole['S0051']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0051'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0061']['sirka']+
						$pole['S0061']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0061'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$obsah=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BTR',1,'R',1);
	    
	}
        if(($reporttyp!='nur Summen') && (!$bKunde)) $pdfobjekt->Ln(2);
        //$pdfobjekt->Ln(2);
}

function zapati_sestava_teil($pdfobjekt,$childNodes,$pole,$sumy,$vyskaradku,$teil)
{

    global $bKunde;
	$pdfobjekt->SetFillColor(255,255,100,1);
        $pdfobjekt->SetFont("FreeSans", "B", 7);

     	$pdfobjekt->Cell(
						$pole['import']['sirka']+
						$pole['abnummer']['sirka']+
						$pole['teilnr']['sirka']+
						$pole['import_pal']['sirka']
//						$pole['export_pal']['sirka']
						,$vyskaradku,
						"Summe Gesamt ($teil)",
						'LRBT',
						0,
						'L'
						,1
						);

//        $obsah=number_format($sumy['export_stk'],0,',',' ');
//        $pdfobjekt->Cell(
//						$pole['export_stk']['sirka']
//						,$vyskaradku,
//						$obsah,
//						'LRBT',
//						0,
//						'R'
//						,1
//						);

        $obsah=number_format($sumy['import_stk'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['import_stk']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_T_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_T_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_P_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_P_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_St_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_St_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_G_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_G_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['sum_stk_E_k'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_E_k']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['auss2'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['auss2']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['auss4'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['auss4']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);
        $obsah=number_format($sumy['auss6'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['auss6']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);

        $obsah=number_format($sumy['sum_stk_Gtat'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['sum_stk_Gtat']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);

        $obsah=number_format($sumy['GDiff'],0,',',' ');
        $pdfobjekt->Cell(
						$pole['GDiff']['sirka']
						,$vyskaradku,
						$obsah,
						'LRBT',
						0,
						'R'
						,1
						);


	$obsah=number_format($sumy['imp_gew'],2,',',' ');
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	if($bKunde)
	    $pdfobjekt->Cell(0,$vyskaradku,'','LRBT',1,'R',1);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$vyskaradku,'','LRBT',0,'R',1);
	
	if(!$bKunde){
	$pdfobjekt->Cell(
						$pole['cnt_S0011T']['sirka']+
						$pole['S0011T']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011T'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0011P']['sirka']+
						$pole['S0011P']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011P'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0041']['sirka']+
						$pole['S0041']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0041'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0051']['sirka']+
						$pole['S0051']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0051'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0061']['sirka']+
						$pole['S0061']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0061'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$obsah=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BTR',1,'R',1);
	    
	}

}

function zapati_sestava($pdfobjekt,$childNodes,$pole,$sumy,$vyskaradku)
{

    global $bKunde;
	$pdfobjekt->SetFillColor(255,255,100,1);
        $pdfobjekt->SetFont("FreeSans", "B", 7);

	$pdfobjekt->Cell(
						$pole['import']['sirka']+
						$pole['abnummer']['sirka']+
						$pole['teilnr']['sirka']+
						$pole['import_pal']['sirka']+
//						$pole['sum_stk_Gtat']['sirka']+
//						$pole['export_stk']['sirka']+
						$pole['import_stk']['sirka']+
						$pole['sum_stk_T_k']['sirka']+
						$pole['sum_stk_P_k']['sirka']+
						$pole['sum_stk_St_k']['sirka']+
						$pole['sum_stk_G_k']['sirka']+
						$pole['sum_stk_E_k']['sirka']
//						$pole['auss2']['sirka']+
//						$pole['auss4']['sirka']+
//						$pole['auss6']['sirka']+
//						$pole['GDiff']['sirka']
						,$vyskaradku,
						"Summen gesamt:",
						'LRBT',
						0,
						'L'
						,1
						);

	$pdfobjekt->SetFont("FreeSans", "B", 6.5);
	
	$obsah=number_format($sumy['a2gew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['auss2']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah=number_format($sumy['a4gew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['auss4']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah=number_format($sumy['a6gew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['auss6']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah=number_format($sumy['sum_Ggew']/1000,1,',',' ');
	$pdfobjekt->Cell($pole['sum_stk_Gtat']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$obsah="";
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$obsah=number_format($sumy['imp_gew'],2,',',' ');
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$vyskaradku,$obsah,'LRBT',0,'R',1);
	if($bKunde)
	    $pdfobjekt->Cell(0,$vyskaradku,'','LRBT',1,'R',1);
	else
	    $pdfobjekt->Cell($pole['bemerkung']['sirka'],$vyskaradku,'','LRBT',0,'R',1);
	
	
	if(!$bKunde){
	    	$pdfobjekt->Cell(
						$pole['cnt_S0011T']['sirka']+
						$pole['S0011T']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011T'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0011P']['sirka']+
						$pole['S0011P']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0011P'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0041']['sirka']+
						$pole['S0041']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0041'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0051']['sirka']+
						$pole['S0051']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0051'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$pdfobjekt->Cell(
						$pole['cnt_S0061']['sirka']+
						$pole['S0061']['sirka']
						,$vyskaradku,
						"",
						'TB',
						0,
						'L'
						,1
						);

	$obsah=number_format($sumy['sumS0061'],0,',',' ');
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$vyskaradku,$obsah,'BT',0,'R',1);

	$obsah=number_format($sumy['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BTR',1,'R',1);
	}

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//zobraz_pozice($pdf,$tatChildNodes,$cells);
function zobraz_pozice($pdfobjekt,$childNodes,$cells)
{


	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($cells as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($childNodes,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($childNodes,$nodename);
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
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
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


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

if($bKunde)
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D607 Auftragsübersicht (P)", "EX von - bis : $von1 - $bis1, Teil: $teil, Reporttyp: $reporttyp");
else
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D607 Auftragsübersicht (P)", $params);    
	    
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
pageheader($pdf,$cells,3);
// zacinam po terminech

$teilParam = $teil;

$terminy=$domxml->getElementsByTagName("termin");
foreach($terminy as $termin)
{
	$importChildNodes = $termin->childNodes;
	zahlavi_termin($pdf,$importChildNodes,$cells,5);
	nuluj_sumy_pole($sum_zapati_termin_array);
	
	// ted jdu po dilech
	
	$teile=$termin->getElementsByTagName("teil");
	foreach($teile as $teil)
	{
                nuluj_sumy_pole($sum_zapati_teil_array);
		$teilChildNodes = $teil->childNodes;
		// a konecne po paletach
		$palety = $teil->getElementsByTagName("pal");
		foreach($palety as $paleta)
		{
			$paletaChildNodes = $paleta->childNodes;
                        if($reporttyp=='Detail mit Summen' || $reporttyp=='Detail'){
                            test_pageoverflow($pdf,3,$cells);
                            detaily($pdf,$cells,5,array(255,255,255),$paletaChildNodes);
                        }
			// nascitam casy
			foreach($sum_zapati_termin_array as $key=>$prvek)
			{
				$hodnota = getValueForNode($paletaChildNodes,$key);
				$sum_zapati_termin_array[$key]+=$hodnota;
			}

       			foreach($sum_zapati_teil_array as $key=>$prvek)
			{
				$hodnota = getValueForNode($paletaChildNodes,$key);
				$sum_zapati_teil_array[$key]+=$hodnota;
			}

		}

                $teilnr = getValueForNode($teilChildNodes, 'teilnr');
                if($reporttyp=='Detail mit Summen' || $reporttyp=='nur Summen'||$bKunde){
                    test_pageoverflow($pdf,5,$cells);
                    zapati_teil($pdf,$importChildNodes,$teilnr,$cells,$sum_zapati_teil_array,5,$teilChildNodes);
                }

		if($odstrankovatPoDilu){
			$pdf->AddPage();
			pageheader($pdf,$cells,3);
		}
	}
	//test_pageoverflow($pdf,3,$cells);
	zapati_termin($pdf,$importChildNodes,$cells,$sum_zapati_termin_array,5);
	// spocitam sumy pro sestavu
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota = $sum_zapati_termin_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}

        if($teilParam=='*'){
            // odstrankovat po terminu
            $pdf->AddPage();
            pageheader($pdf,$cells,3);
        }
        else{
            test_pageoverflow($pdf,5,$cells);
        }
	
}

if($teilParam!='*')
    zapati_sestava_teil($pdf,$importChildNodes,$cells,$sum_zapati_sestava_array,5,$teilParam);
else
    zapati_sestava($pdf,$importChildNodes,$cells,$sum_zapati_sestava_array,5);

//Close and output PDF document

$pdf->Cell(0, 5, 'Wenn geplant mit von <> geplant mit bis, dann werden auch Positionen ohne Plan (Termin) gezeigt.', '0', 0, 'L', 0);
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
