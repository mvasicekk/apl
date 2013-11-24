<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "D370";
$doc_subject = "D370 Report";
$doc_keywords = "D370";

// necham si vygenerovat XML

$parameters=$_GET;

$auftragsnr=$_GET['auftragsnr'];


require_once('D370_xml.php');


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
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"palnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gew" 
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_4" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_6" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"sonst" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"summe_lieferung" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gut_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"import_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"gew_auss_teil_pal" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(

"teilnr" 
=> array ("popis"=>"Teil","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"palnr" 
=> array ("nf"=>array(0,',',' '),"popis"=>"pal","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"gew" 
=> array ("nf"=>array(0,',',' '),"popis"=>"gew","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"auss_2" 
=> array ("nf"=>array(0,',',' '),"popis"=>"2","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),


"auss_4" 
=> array ("nf"=>array(0,',',' '),"popis"=>"4","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"auss_6" 
=> array ("nf"=>array(0,',',' '),"popis"=>"6","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"sonst" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sons","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"summe_lieferung" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"gut_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"gutStk","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"import_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"IMStk","sirka"=>20,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"gew_auss_teil" 
=> array ("nf"=>array(0,',',' '),"popis"=>"Auss kg","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>0),

);




$sum_zapati_auftrag_array = array(	
								"auss_2"=>0,
								"auss_4"=>0,
								"auss_6"=>0,
								"sonst"=>0,
								"summe_lieferung"=>0,
								"gut_stk"=>0,
								"import_stk"=>0,
								"gew_auss_teil_pal"=>0,
								);
global $sum_zapati_auftrag_array;


$sum_zapati_teil_array = array(	
								"auss_2"=>0,
								"auss_4"=>0,
								"auss_6"=>0,
								"sonst"=>0,
								"gut_stk"=>0,
								"import_stk"=>0,
								"summe_lieferung"=>0,
								"gew_auss_teil_pal"=>0,
								);
global $sum_zapati_teil_array;


$sum_zapati_sestava_array = array(	
								"auss_2"=>0,
								"auss_4"=>0,
								"auss_6"=>0,
								"sonst"=>0,
								"gut_stk"=>0,
								"import_stk"=>0,
								"summe_lieferung"=>0,
								"gew_auss_teil_pal"=>0,
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
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	//if($pdfobjekt->PageNo()==1)
	//{
		//$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	//}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	//$pdfobjekt->SetFont("FreeSans", "", 6);
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
function zapati_auftrag($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	//$typmin="kd";
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 9);
	
	$pdfobjekt->Cell(50,$vyskaradku,$popis,'TB',0,'L',$fill);
	

	$pdfobjekt->SetFont("FreeSans", "", 9);
	
	$obsah=$pole['auss_2'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['auss_4'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['auss_6'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);


	$obsah=$pole['sonst'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['summe_lieferung'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['gut_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['import_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['gew_auss_teil_pal'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'TB',1,'R',$fill);

	//$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$teilnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	//$typmin="kd";
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 9);
	
	$pdfobjekt->Cell(50,$vyskaradku,$popis." (".$teilnr.")",'TB',0,'L',$fill);
	

	$pdfobjekt->SetFont("FreeSans", "", 9);
	
	$obsah=$pole['auss_2'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['auss_4'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['auss_6'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);


	$obsah=$pole['sonst'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['summe_lieferung'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['gut_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['import_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'TB',0,'R',$fill);

	$obsah=$pole['gew_auss_teil_pal'];;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'TB',1,'R',$fill);

	//$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zapati_sestava($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$gew_auss,$gew_import)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	//$typmin="kd";
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->SetFont("FreeSans", "", 9);
	
	$pdfobjekt->Ln();
	
	$cislo=number_format($gew_import-$gew_auss,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"Gewicht [kg]    gute : ".$cislo,'LRT',1,'R',$fill);
	
	$cislo=number_format($gew_import,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"IMP : ".$cislo,'LRB',1,'R',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D370 Ausschuss Auftrag Teil mit Palette - nach Ausschusstypen", $params);
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

dbConnect();

// a ted pujdu po produkt zakazkach
$auftraege=$domxml->getElementsByTagName("auftrag");
foreach($auftraege as $auftrag)
{
	$auftragsnr=$auftrag->getElementsByTagName("auftragsnr")->item(0)->nodeValue;
	$import_gew=$auftrag->getElementsByTagName("import_gew")->item(0)->nodeValue;
		
	//nuluj_sumy_pole($sum_zapati_pg_kd_array);
	//nuluj_sumy_pole($sum_zapati_pg_aby_array);
	//nuluj_sumy_pole($sum_zapati_pg_verb_array);
		
	//test_pageoverflow($pdf,5,$cells_header,5);
	//zahlavi_termin($pdf,5,array(255,255,100),$cells_header,$terminnr,$ex_datum);
	nuluj_sumy_pole($sum_zapati_auftrag_array);
	
	$teile=$auftrag->getElementsByTagName("teil");
	
	foreach($teile as $teil)
	{
		$teilnr=$teil->getElementsByTagName("teilnr")->item(0)->nodeValue;
		$teil_childs=$teil->childNodes;
		nuluj_sumy_pole($sum_zapati_teil_array);
		
		$paletten=$teil->getElementsByTagName("pal");
		
		foreach($paletten as $palette)
		{
			$palette_childs=$palette->childNodes;
			test_pageoverflow($pdf,5,$cells_header,5);
			telo($pdf,$cells,5,array(255,255,255),"",$palette_childs);
		
			// projedu pole a aktualizuju sumy pro zapati auftrag
			foreach($sum_zapati_teil_array as $key=>$prvek)
			{
				$hodnota=$palette->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_teil_array[$key]+=$hodnota;
			}
		}
		
		test_pageoverflow($pdf,5,$cells_header,5);
		zapati_teil($pdf,$auftrag,5,"Summe Teil",array(255,255,100),$sum_zapati_teil_array,$teilnr);
		
		// projedu pole a aktualizuju sumy pro zapati auftrag
		foreach($sum_zapati_auftrag_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_teil_array[$key];
			$sum_zapati_auftrag_array[$key]+=$hodnota;
		}
	}
	
	test_pageoverflow($pdf,15,$cells_header,5);
	zapati_auftrag($pdf,$auftrag,5,"Summe Auftrag",array(255,255,100),$sum_zapati_auftrag_array);

}

zapati_sestava($pdf,$node,5,"",array(255,255,100),$sum_zapati_auftrag_array,$sum_zapati_auftrag_array['gew_auss_teil_pal'],$import_gew);
/*
	
		//test_pageoverflow($pdf,5,$cells_header,5);
		//zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr);
		//zahlavi_teil($pdf,5,array(235,235,235),$cells_header,$teilnr,$gew,$brgew,$f_muster_platz,$f_muster_vom,get_teil_bemerk($teilnr,85));
		
		//nuluj_sumy_pole($sum_zapati_kunde_kd_array);
		//nuluj_sumy_pole($sum_zapati_kunde_aby_array);
		//nuluj_sumy_pole($sum_zapati_kunde_verb_array);
		
		$auftraege=$kunde->getElementsByTagName("auftrag");
		
		foreach($auftraege as $auftrag)
		{
		
			$auftragsnr=$auftrag->getElementsByTagName("auftragsnr")->item(0)->nodeValue;
			
			//test_pageoverflow($pdf,10,$cells_header,5);
			//zahlavi_teil($pdf,5,array(255,255,255),$cells_header,$teilnr,$gew,$muster_platz,$teillang,$abnr);
			//nuluj_sumy_pole($sum_zapati_teil_array);
			
			$vzkd_minuten=$auftrag->getElementsByTagName("vzkd_min")->item(0);
			$vzkd_min_childs=$vzkd_minuten->childNodes;
			test_pageoverflow($pdf,25,$cells_header,5);
			telo($pdf,$cells_kd,5,array(255,255,255),"",$vzkd_min_childs);
			
			// projedu pole a aktualizuju sumy pro zapati kunde_kd
			foreach($sum_zapati_kunde_kd_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_kunde_kd_array[$key]+=$hodnota;
			}

			
			$vzaby_minuten=$auftrag->getElementsByTagName("vzaby_min")->item(0);
			$vzaby_min_childs=$vzaby_minuten->childNodes;
			//test_pageoverflow($pdf,4,$cells_header,5);
			telo($pdf,$cells_aby,4,array(255,255,255),"",$vzaby_min_childs);
			
			// projedu pole a aktualizuju sumy pro zapati kunde_aby
			foreach($sum_zapati_kunde_aby_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_kunde_aby_array[$key]+=$hodnota;
			}

			$verb_minuten=$auftrag->getElementsByTagName("verb_min")->item(0);
			$verb_min_childs=$verb_minuten->childNodes;
			//test_pageoverflow($pdf,4,$cells_header,5);
			telo($pdf,$cells_verb,4,array(255,255,255),"",$verb_min_childs);
			
			// projedu pole a aktualizuju sumy pro zapati kunde_verb
			foreach($sum_zapati_kunde_verb_array as $key=>$prvek)
			{
				$hodnota=$auftrag->getElementsByTagName($key)->item(0)->nodeValue;
				$sum_zapati_kunde_verb_array[$key]+=$hodnota;
			}

		}
		
		test_pageoverflow($pdf,15,$cells_header,5);
		zapati_kunde($pdf,$auftrag,5,"SumKdmin",array(255,255,100),$sum_zapati_kunde_kd_array,$kundenr,"kd");
		
		// sumy pro zapati pg
		foreach($sum_zapati_pg_kd_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_kunde_kd_array[$key];
			$sum_zapati_pg_kd_array[$key]+=$hodnota;
		}

		
		//test_pageoverflow($pdf,5,$cells_header,5);
		zapati_kunde($pdf,$auftrag,5,"SumAbymin",array(255,255,100),$sum_zapati_kunde_aby_array,$kundenr,"aby");
		
		// sumy pro zapati pg
		foreach($sum_zapati_pg_aby_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_kunde_aby_array[$key];
			$sum_zapati_pg_aby_array[$key]+=$hodnota;
		}

		
		//test_pageoverflow($pdf,5,$cells_header,5);
		zapati_kunde($pdf,$auftrag,5,"SumVerb",array(255,255,100),$sum_zapati_kunde_verb_array,$kundenr,"verb");
		
		// sumy pro zapati pg
		foreach($sum_zapati_pg_verb_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_kunde_verb_array[$key];
			$sum_zapati_pg_verb_array[$key]+=$hodnota;
		}

		
	}
	
	test_pageoverflow($pdf,15,$cells_header,5);
	zapati_kunde($pdf,$auftrag,5,"VzKd PG",array(230,230,255),$sum_zapati_pg_kd_array,$pgnr,"kd");
	zapati_kunde($pdf,$auftrag,5,"VzAby PG",array(230,230,255),$sum_zapati_pg_aby_array,$pgnr,"aby");
	zapati_kunde($pdf,$auftrag,5,"Verb PG",array(230,230,255),$sum_zapati_pg_verb_array,$pgnr,"verb");
	// sumy pro zapati pg
	foreach($sum_zapati_sestava_kd_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_pg_kd_array[$key];
		$sum_zapati_sestava_kd_array[$key]+=$hodnota;
	}
	// sumy pro zapati pg
	foreach($sum_zapati_sestava_aby_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_pg_aby_array[$key];
		$sum_zapati_sestava_aby_array[$key]+=$hodnota;
	}
	// sumy pro zapati pg
	foreach($sum_zapati_sestava_verb_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_pg_verb_array[$key];
		$sum_zapati_sestava_verb_array[$key]+=$hodnota;
	}
		
}

test_pageoverflow($pdf,20,$cells_header,5);
$pdf->Ln();
zapati_kunde($pdf,$auftrag,5,"VzKd Ges",array(200,200,255),$sum_zapati_sestava_kd_array,"","kd");
zapati_kunde($pdf,$auftrag,5,"VzAby Ges",array(200,200,255),$sum_zapati_sestava_aby_array,"","aby");
zapati_kunde($pdf,$auftrag,5,"Verb Ges",array(200,200,255),$sum_zapati_sestava_verb_array,"","verb");
*/

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
