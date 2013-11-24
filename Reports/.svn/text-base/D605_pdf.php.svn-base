<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "D605";
$doc_subject = "D605 Report";
$doc_keywords = "D605";

// necham si vygenerovat XML

$parameters=$_GET;

$auftragsnr=$_GET['auftragsnr'];

require_once('D605_xml.php');


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

"export_lief" 
=> array ("popis"=>"","sirka"=>11,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"import_pal" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"export_pal" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"export_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"import_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_St" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_G" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_E" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss4" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss6" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"GDiff" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>9,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"imp_gew" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0011T" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0011P" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0041" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0051" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0061" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_header = 
array(

"export_lief" 
=> array ("popis"=>"EX\n\n","sirka"=>11,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"Teil\n\n","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"import_pal" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Palette\nIMP EXP Stk\nexp","sirka"=>30,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"import_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nIM","sirka"=>7,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nTr","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nPU","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_St" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nSt","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_G" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nF","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sum_stk_E" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\nE","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n(2)","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss4" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n(4)","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"auss6" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n(6)","sirka"=>6,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"GDiff" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nG-\nIM","sirka"=>9,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"imp_gew" 
=> array ("nf"=>array(2,',',' '),"popis"=>"Gew\n(to)\nIMP","sirka"=>8,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0011T" 
=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011T" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0011P" 
=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0011P" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0041" 
=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0041" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0051" 
=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0051" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"cnt_S0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\n\n","sirka"=>3,"ram"=>'LTB',"align"=>"R","radek"=>0,"fill"=>0),

"S0061" 
=> array ("nf"=>array(2,',',' '),"popis"=>"\nvzkd\nmin","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"sumS0061" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nvzkd\nGes","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"\nGesamt\nvzkd","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)

);



$sum_zapati_import_array = array(
                    "import_stk"=>0,
					"imp_gew"=>0,
					"sumS0011T"=>0,
					"sumS0011P"=>0,
					"sumS0041"=>0,
					"sumS0051"=>0,
					"sumS0061"=>0,
					"sumvzkd"=>0
				);
global $sum_zapati_import_array;


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
/*
"export_lief" 
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
	$fill=1;
	// 1.radek
	$pdfobjekt->Cell($pole['export_lief']['sirka'],$headervyskaradku,"Lief-",'LRT',0,'L',1);
	$pdfobjekt->Cell($pole['teilnr']['sirka'],$headervyskaradku,"Teil",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['import_pal']['sirka']+$pole['export_pal']['sirka']+$pole['export_stk']['sirka'],$headervyskaradku,"Palette",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['import_stk']['sirka']+$pole['sum_stk_T']['sirka']+$pole['sum_stk_P']['sirka']+$pole['sum_stk_St']['sirka']+$pole['sum_stk_G']['sirka']+$pole['sum_stk_E']['sirka'],$headervyskaradku,"Stueckzahl",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['auss2']['sirka']+$pole['auss4']['sirka']+$pole['auss6']['sirka'],$headervyskaradku,"Ausschuss",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$headervyskaradku,"G-",'LRT',0,'R',$fill);
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$headervyskaradku,"Gew",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['cnt_S0011T']['sirka']+$pole['S0011T']['sirka']+$pole['sumS0011T']['sirka'],$headervyskaradku,"S0011 (T)",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['cnt_S0011P']['sirka']+$pole['S0011P']['sirka']+$pole['sumS0011P']['sirka'],$headervyskaradku,"S0011 (P)",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['cnt_S0041']['sirka']+$pole['S0041']['sirka']+$pole['sumS0041']['sirka'],$headervyskaradku,"S0041 (St)",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['cnt_S0051']['sirka']+$pole['S0051']['sirka']+$pole['sumS0051']['sirka'],$headervyskaradku,"S0051 (E)",'LRT',0,'C',$fill);
	$pdfobjekt->Cell($pole['cnt_S0061']['sirka']+$pole['S0061']['sirka']+$pole['sumS0061']['sirka'],$headervyskaradku,"S0061 (F)",'LRT',0,'C',$fill);
	$pdfobjekt->Cell(0,$headervyskaradku,"",'LRT',1,'C',$fill);

	// 2.radek
	$pdfobjekt->Cell($pole['export_lief']['sirka'],$headervyskaradku,"EXP",'LR',0,'L',$fill);
	$pdfobjekt->Cell($pole['teilnr']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"IMP",'LR',0,'C',$fill);
	$pdfobjekt->Cell($pole['export_pal']['sirka'],$headervyskaradku,"EXP",'LR',0,'C',$fill);
	$pdfobjekt->Cell($pole['export_stk']['sirka'],$headervyskaradku,"Stk.",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['import_stk']['sirka']+$pole['sum_stk_T']['sirka']+$pole['sum_stk_P']['sirka']+$pole['sum_stk_St']['sirka']+$pole['sum_stk_G']['sirka']+$pole['sum_stk_E']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['auss2']['sirka']+$pole['auss4']['sirka']+$pole['auss6']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$headervyskaradku,"IMP",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$headervyskaradku,"(to)",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0011T']['sirka']+$pole['S0011T']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0011P']['sirka']+$pole['S0011P']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0041']['sirka']+$pole['S0041']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0051']['sirka']+$pole['S0051']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0061']['sirka']+$pole['S0061']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$headervyskaradku,"VzKd",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(0,$headervyskaradku,"Gesamt",'LR',1,'R',$fill);
	
	// 3.radek
	$pdfobjekt->Cell($pole['export_lief']['sirka'],$headervyskaradku,"",'LR',0,'L',$fill);
	
	$pdfobjekt->Cell($pole['teilnr']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['import_pal']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	$pdfobjekt->Cell($pole['export_pal']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	$pdfobjekt->Cell($pole['export_stk']['sirka'],$headervyskaradku,"Exp",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['import_stk']['sirka'],$headervyskaradku,"IM",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_T']['sirka'],$headervyskaradku,"Tr",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_P']['sirka'],$headervyskaradku,"PU",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_St']['sirka'],$headervyskaradku,"St",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_G']['sirka'],$headervyskaradku,"F",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sum_stk_E']['sirka'],$headervyskaradku,"E",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['auss2']['sirka'],$headervyskaradku,"(2)",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['auss4']['sirka'],$headervyskaradku,"(4)",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['auss6']['sirka'],$headervyskaradku,"(6)",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['GDiff']['sirka'],$headervyskaradku,"",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['imp_gew']['sirka'],$headervyskaradku,"IMP",'LR',0,'C',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0011T']['sirka']+$pole['S0011T']['sirka'],$headervyskaradku,"min",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0011T']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0011P']['sirka']+$pole['S0011P']['sirka'],$headervyskaradku,"min",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0011P']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0041']['sirka']+$pole['S0041']['sirka'],$headervyskaradku,"min",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0041']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0051']['sirka']+$pole['S0051']['sirka'],$headervyskaradku,"min",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0051']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell($pole['cnt_S0061']['sirka']+$pole['S0061']['sirka'],$headervyskaradku,"min",'LR',0,'R',$fill);
	$pdfobjekt->Cell($pole['sumS0061']['sirka'],$headervyskaradku,"Ges",'LR',0,'R',$fill);
	
	$pdfobjekt->Cell(0,$headervyskaradku,"VzKd",'LR',1,'R',$fill);


	$pdfobjekt->SetFont("FreeSans", "", 6);
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
function zahlavi_import($pdfobjekt,$childNodes)
{

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->Cell(0,6,"Imp. Datum: ".getValueForNode($childNodes,"aufdat"),'0',1,'R',0);
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//zapati_import($pdf,$sum_zapati_teil_array);
function zapati_import($pdfobjekt,$sum_array)
{

	$pdfobjekt->SetFont("FreeSans", "B", 7);

	$pdfobjekt->Cell(11+20+10+10,6,"Summe Vzmin",'B',0,'L',0);

    $obsah="";//number_format($sum_array['import_stk'],0,',',' ');
	$pdfobjekt->Cell(7,6,$obsah,'B',0,'R',0);

    //dummy vypln
        $pdfobjekt->Cell(125-11-20-10-10-7,6,"",'B',0,'R',0);

	$obsah=number_format($sum_array['imp_gew'],2,',',' ');
	$pdfobjekt->Cell(8,6,$obsah,'B',0,'R',0);

	$obsah=number_format($sum_array['sumS0011T'],0,',',' ');
	$pdfobjekt->Cell(20,6,$obsah,'B',0,'R',0);
	$obsah=number_format($sum_array['sumS0011P'],0,',',' ');
	$pdfobjekt->Cell(20,6,$obsah,'B',0,'R',0);
	$obsah=number_format($sum_array['sumS0041'],0,',',' ');
	$pdfobjekt->Cell(20,6,$obsah,'B',0,'R',0);
	$obsah=number_format($sum_array['sumS0051'],0,',',' ');
	$pdfobjekt->Cell(20,6,$obsah,'B',0,'R',0);
	$obsah=number_format($sum_array['sumS0061'],0,',',' ');
	$pdfobjekt->Cell(20,6,$obsah,'B',0,'R',0);
	$obsah=number_format($sum_array['sumvzkd'],0,',',' ');
	$pdfobjekt->Cell(0,6,$obsah,'B',1,'R',0);

    
    $pdfobjekt->Cell(11+20+10+10+10,6,"Summe Stk",'B',0,'L',0);
    $pdfobjekt->SetFont("FreeSans", "B", 6);
    $obsah=number_format($sum_array['import_stk'],0,',',' ');
	$pdfobjekt->Cell(7,6,$obsah,'B',0,'R',0);
    $pdfobjekt->Cell(0,6,"",'B',1,'R',0);

	$pdfobjekt->Ln();
	$pdfobjekt->Ln();

	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Cell(50,6,"Erstellt: ",'0',0,'R',0);
	$pdfobjekt->Cell(50,6,"              ",'B',1,'L',0);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Cell(50,6,"",'0',0,'R',0);
	$pdfobjekt->Cell(50,6,"Datum/Unterschrift",'0',0,'C',0);


}


////////////////////////////////////////////////////////////////////////////////////////////////////
//zahlavi_teil($pdf,$dilChildNodes);
//
function zahlavi_teil($pdfobjekt,$childNodes)
{

	$pdfobjekt->SetFont("FreeSans", "", 10);

	$pdfobjekt->SetFillColor(255,255,200,1);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"TeilNr:",'TL',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"teilnr"),'TR',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"Gewicht (kg):",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(25,6,number_format(getValueForNode($childNodes,"Gew"),3,',',' '),'0',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"letztes Auftrag:",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->Cell(0,6,getValueForNode($childNodes,"auftragsnr")."  am ( ".getValueForNode($childNodes,"aufdat")." )",'0',1,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Original:",'L',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"teillang"),'R',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(25,6,"BrGewicht (kg):",'0',0,'L',1);
	$pdfobjekt->SetFont("FreeSans", "B", 13);
	$pdfobjekt->Cell(25,6,number_format(getValueForNode($childNodes,"BrGew"),3,',',' '),'0',1,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Bezeichnung:",'LB',0,'L',1);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"Teilbez"),'RB',1,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Musterplatz:",'0',0,'L',1);

	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"musterplatz"),'0',1,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->Cell(20,6,"Einlag.Datum:",'0',0,'L',1);

	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Cell(30,6,getValueForNode($childNodes,"mustervom"),'0',1,'R',0);

	$pdfobjekt->Ln();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_pozice($pdfobjekt,$cells)
{
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	foreach($cells as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],5,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->Ln();

}

////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$summe_array)
{
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$pdfobjekt->Cell(10+5+10+10+45+45,6,"Summen",'T',0,'L',0);

	$obsah=number_format($summe_array['vzkd'],4,',',' ');
	$pdfobjekt->Cell(15,6,$obsah,'T',0,'R',0);
	$obsah=number_format($summe_array['vzaby'],4,',',' ');
	$pdfobjekt->Cell(15,6,$obsah,'T',1,'R',0);

	$pdfobjekt->Cell(10+5+10+10+45+45,6,"Faktor",'T',0,'L',0);

	$obsah=number_format($summe_array['vzkd']/$summe_array['vzaby'],4,',',' ');
	$pdfobjekt->Cell(30,6,$obsah,'T',0,'R',0);

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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D605 Auftragsuebersicht (IM)", $params);
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
// zacinam po importech

$importy=$domxml->getElementsByTagName("import");
foreach($importy as $import)
{
	$importChildNodes = $import->childNodes;
	zahlavi_import($pdf,$importChildNodes);
	
	pageheader($pdf,$cells,3);
	// ted jdu po dilech
	$teile=$import->getElementsByTagName("teil");
	foreach($teile as $teil)
	{
		$teilChildNodes = $teil->childNodes;
		// a konecne po paletach
		$palety = $teil->getElementsByTagName("pal");
		foreach($palety as $paleta)
		{
			$paletaChildNodes = $paleta->childNodes;
			test_pageoverflow($pdf,3,$cells);
			detaily($pdf,$cells,5,array(255,255,255),$paletaChildNodes);
			// nascitam casy
			foreach($sum_zapati_import_array as $key=>$prvek)
			{
				$hodnota = getValueForNode($paletaChildNodes,$key);
				$sum_zapati_import_array[$key]+=$hodnota;
			}
		}
	}
	test_pageoverflow($pdf,3,$cells);
	zapati_import($pdf,$sum_zapati_import_array);
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
