<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S810";
$doc_subject = "S810 Report";
$doc_keywords = "S810";

// necham si vygenerovat XML

$parameters=$_GET;

$a = AplDB::getInstance();

$auftragsnr_von=$a->make_DB_datum($_GET['auftragsnr_von']);
$auftragsnr_bis=$a->make_DB_datum($_GET['auftragsnr_bis']);

$teil=  trim($_GET['teil']);


require_once('S810_xml.php');


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

//if
//
// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells = 
array(

"palimp" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stkimp" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

//"impkorr" 
//=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stkrueG" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"abgnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stkexp" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stkre" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"aussstk_all"
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"bemerkung" 
=> array ("popis"=>"","sirka"=>35,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"pal_exp" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
    
"auftragsnrex" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auslief" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"giesstag" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),


);

$cells_header = 
array(

"palimp" 
=> array ("popis"=>"PalIM","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"stkimp" 
=> array ("popis"=>"StkIM","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

//"impkorr" 
//=> array ("popis"=>"StkIMk","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"stkrueG" 
=> array ("popis"=>"StkRM","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"abgnr" 
=> array ("popis"=>"Gtat","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"stkexp" 
=> array ("popis"=>"StkEX","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"stkre" 
=> array ("popis"=>"StkRE","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"aussstk_all"
=> array ("popis"=>"Auss","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"bemerkung" 
=> array ("popis"=>"","sirka"=>35,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"pal_exp" 
=> array ("popis"=>"EXPal","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auftragsnrex" 
=> array ("popis"=>"EXNr","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),

"auslief" 
=> array ("popis"=>"EXDat","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>1),

"giesstag" 
=> array ("popis"=>"GT","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>1),

);




$sum_zapati_auftrag_array = array(	
								"stkimp"=>0,
								"impkorr"=>0,
								"stkrueG"=>0,
								"stkexp"=>0,
								"stkre"=>0,
								"aussstk_all"=>0
								);
global $sum_zapati_auftrag_array;

$sum_zapati_sestava_array = array(	
								"stkimp"=>0,
								"impkorr"=>0,
								"stkrueG"=>0,
								"stkexp"=>0,
								"stkre"=>0,
								"aussstk_all"=>0
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
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	//if($pdfobjekt->PageNo()==1)
	//{
//		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	//}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
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



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_auftrag($pdfobjekt,$vyskaradku,$rgb,$cells_header,$auftragsnr,$aufdat)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 7);

	$pdfobjekt->Cell(0,$vyskaradku,"IM: ".$auftragsnr." (".$aufdat.")",'1',1,'L',$fill);
	
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$cells_header,$teilnr,$gew,$brgew,$muster_platz,$muster_vom,$bemerk)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 8);

	$pdfobjekt->Cell(30,$vyskaradku,$teilnr,'1',0,'L',$fill);
	
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->Cell(50,$vyskaradku," Muster: ".$muster_platz."  Einlager. ".$muster_vom,'1',0,'L',$fill);
	
	
	$pdfobjekt->Cell(30,$vyskaradku,"  Gew: ".$gew."kg  BrGew. ".$brgew."kg",'1',0,'L',$fill);
	
	//$pdfobjekt->MyMultiCell(0,$vyskaradku,$bemerk,1,'L',$fill);
	$pdfobjekt->SetFont("FreeSans", "", 5);
	$pdfobjekt->Cell(0,$vyskaradku,$bemerk,'1',1,'L',$fill);
	
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_taetigkeit($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$tatnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(100,$vyskaradku,$popis." ".$tatnr,'B',0,'L',$fill);
	
	
	$obsah=$pole['stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	//auss_typ
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	//vzkd_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	//vzaby_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_paleta($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$pal)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(0,$vyskaradku,"",'B',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$teilnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(100,$vyskaradku,$popis." ".$teilnr,'B',0,'L',$fill);
	
	
	$obsah=$pole['stk'];
	$obsah="";//number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['aussstk_all'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	//auss_typ
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	//vzkd_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['sumvzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	//vzaby_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['sumvzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['sumverb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//zapati_auftrag($pdf,$auftrag_childs,5,array(235,235,235),$sum_zapati_auftrag_array);
function zapati_auftrag($pdfobjekt,$node,$vyskaradku,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->Cell(15,$vyskaradku,"IM: ".getValueForNode($node,'auftragsnr'),'B',0,'L',$fill);
	
	/*
	"stkimp"=>0,	15
	"impkorr"=>0,	15	
	"stkrueG"=>0,	15
	"stkex"=>0,		15	
	"stkre"=>0,		15
	"aussstk"=>0	0
	*/			
	
	$obsah=$pole['stkimp'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

//	$obsah=$pole['impkorr'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['stkrueG'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['stkexp'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10+15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['stkre'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['aussstk_all'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah="";//$pole['aussstk'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$node,$vyskaradku,$rgb,$pole)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->Cell(15,$vyskaradku,"Berichtsumme:",'B',0,'L',$fill);
	
	/*
	"stkimp"=>0,	15
	"impkorr"=>0,	15	
	"stkrueG"=>0,	15
	"stkex"=>0,		15	
	"stkre"=>0,		15
	"aussstk"=>0	0
	*/			
	
	$obsah=$pole['stkimp'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

//	$obsah=$pole['impkorr'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['stkrueG'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['stkexp'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10+15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['stkre'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['aussstk_all'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah="";//$pole['aussstk'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);

}



function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S810 Teil - Bearbeitungsstand", $params);
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
pageheader($pdf,$cells_header,5);

dbConnect();

// a ted pujdu po zakazkach
$auftraege=$domxml->getElementsByTagName("auftrag");
foreach($auftraege as $auftrag)
{
	$auftrag_childs=$auftrag->childNodes;
	$auftragsnr=$auftrag->getElementsByTagName("auftragsnr")->item(0)->nodeValue;
	$aufdat = $auftrag->getElementsByTagName("aufdat")->item(0)->nodeValue;
	test_pageoverflow($pdf,5,$cells_header);
	zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr,$aufdat);
	nuluj_sumy_pole($sum_zapati_auftrag_array);
	
	$paletten=$auftrag->getElementsByTagName("palette");
	
	foreach($paletten as $palette)
	{
		$palette_childs=$palette->childNodes;
		if(test_pageoverflow($pdf,5,$cells_header))
			zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr,$aufdat);
		
			telo($pdf,$cells,4,array(255,255,255),"",$palette_childs);
			
			// sumy pro auftrag
			foreach($sum_zapati_auftrag_array as $key=>$prvek)
			{
				$hodnota=getValueForNode($palette_childs,$key);
				$sum_zapati_auftrag_array[$key]+=$hodnota;
			}
	}
	
	test_pageoverflow($pdf,5,$cells_header);
	zapati_auftrag($pdf,$auftrag_childs,5,array(235,235,235),$sum_zapati_auftrag_array);

	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_auftrag_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
}

test_pageoverflow($pdf,5,$cells_header);
zapati_sestava($pdf,$auftrag_childs,5,array(255,255,255),$sum_zapati_sestava_array);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
