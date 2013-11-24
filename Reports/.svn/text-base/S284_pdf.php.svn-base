<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S284";
$doc_subject = "S284 Report";
$doc_keywords = "S284";

// necham si vygenerovat XML

$parameters=$_GET;

$datum_von=make_DB_datum($_GET['datum_von']);
$datum_bis=make_DB_datum($_GET['datum_bis']);
$schicht_von=$_GET['schicht_von'];
$schicht_bis=$_GET['schicht_bis'];
$pers_von=$_GET['pers_von'];
$pers_bis=$_GET['pers_bis'];
$kunde=$_GET['kunde'];
$password = $_GET['password'];

$user = $_SESSION['user'];

if(!testReportPassword("S284",$password,$user))
	echo "password ($password,$user)";
else
{


/*
if($password!="")
	echo "password";
else
{
*/

require_once('S284_xml.php');


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

"teilnr" 
=> array ("popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"teilbez" 
=> array ("popis"=>"","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"tatnr" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"Name" 
=> array ("popis"=>"","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"sumstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumverb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)
);

$cells_header = 
array(

"teilnr" 
=> array ("popis"=>"Teil","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"teilbez" 
=> array ("popis"=>"Teilbez","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"tatnr" 
=> array ("popis"=>"TaetNr","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"Name" 
=> array ("popis"=>"Name","sirka"=>50,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"sumstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumstk","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"sumvzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumvzaby","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"sumverb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumverb","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1)
);


$cells_header_pers = 
array(

"persnr" 
=> array ("popis"=>"PersNr","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"name" 
=> array ("popis"=>"Name","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"vorname" 
=> array ("popis"=>"Vorname","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"sumvzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumvzaby","sirka"=>40,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"sumverb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumverb","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1)
);

$cells_pers = 
array(

"persnr" 
=> array ("popis"=>"PersNr","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"name" 
=> array ("popis"=>"Name","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"vorname" 
=> array ("popis"=>"Vorname","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"sumvzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumvzaby","sirka"=>40,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"sumverb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"sumverb","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0)
);

$sum_zapati_teil_array = array(	
								"sumstk"=>0,
								"sumvzaby"=>0,
								"sumverb"=>0,
								);
global $sum_zapati_teil_array;

$sum_zapati_tab1_array = array(	
								"sumstk"=>0,
								"sumvzaby"=>0,
								"sumverb"=>0,
								);
global $sum_zapati_tab1_array;

$sum_zapati_tab2_array = array(	
								"sumvzaby"=>0,
								"sumverb"=>0,
								);
global $sum_zapati_tab1_array;

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
function pageheader($pdfobjekt,$pole,$headervyskaradku,$popisek)
{
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(0,$headervyskaradku,$popisek,'0',1,'L',0);
	
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	//$pdfobjekt->Ln();
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$childnodes,$sum_zapati_array)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(20,$vyskaradku,"Summe Teil",'LTB',0,'L',$fill);
	$pdfobjekt->Cell(105,$vyskaradku,getValueForNode($childnodes,"teilnr"),'TB',0,'L',$fill);
	
	
	$obsah="";//0;//$sum_zapati_array['sumstk'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$sum_zapati_array['sumvzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$sum_zapati_array['sumverb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_tab1($pdfobjekt,$vyskaradku,$rgb,$childnodes,$sum_zapati_array)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(125,$vyskaradku,"Ges. Summe",'LTB',0,'L',$fill);
		
	
	$obsah="";//$sum_zapati_array['sumstk'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$sum_zapati_array['sumvzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$sum_zapati_array['sumverb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_tab2($pdfobjekt,$vyskaradku,$rgb,$childnodes,$sum_zapati_array)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(100,$vyskaradku,"Ges. Summe",'LTB',0,'L',$fill);
		
	
	$obsah=$sum_zapati_array['sumvzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(40,$vyskaradku,$obsah,'1',0,'R',$fill);

	$obsah=$sum_zapati_array['sumverb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',1,'R',$fill);

}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$datumnr)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
//		zahlavi_datum($pdfobjekt,5,array(255,255,200),$datumnr);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S284 Abrechnung Werkvertraege", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 7));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);



// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,5,"Leistung nach Teil - Taetigkeit:");
//$pdf->Ln();
//$pdf->Ln();


// zacinam po dilech
$dily=$domxml->getElementsByTagName("teil");
foreach($dily as $dil)
{
	
	$teilChildNodes = $dil->childNodes;
	
	$taetigkeiten = $dil->getElementsByTagName("taetigkeit");

	nuluj_sumy_pole($sum_zapati_teil_array);
		
	foreach($taetigkeiten as $taetigkeit)
	{
		$taetigkeitChildNodes = $taetigkeit->childNodes;
		if(test_pageoverflow_noheader($pdf,5))
				pageheader($pdf,$cells_header,5,"Leistung nach Teil - Taetigkeit:");
		telo($pdf,$cells,5,array(255,255,255),"",$taetigkeitChildNodes);
		
		// projedu pole a aktualizuju sumy pro zapati teil
		foreach($sum_zapati_teil_array as $key=>$prvek)
		{
			$hodnota=getValueForNode($taetigkeitChildNodes,$key);
			$sum_zapati_teil_array[$key]+=$hodnota;
		}
	} 
	
	//zapati pro dil
	if(test_pageoverflow_noheader($pdf,5))
		pageheader($pdf,$cells_header,5,"Leistung nach Teil - Taetigkeit:");
	zapati_teil($pdf,5,array(235,235,235),$teilChildNodes,$sum_zapati_teil_array);
	
	// sumy pro prvni tabulku
	foreach($sum_zapati_tab1_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_teil_array[$key];
		$sum_zapati_tab1_array[$key]+=$hodnota;
	}
	
}

if(test_pageoverflow_noheader($pdf,5))
		pageheader($pdf,$cells_header,5,"Leistung nach Teil - Taetigkeit:");
zapati_tab1($pdf,5,array(200,200,255),$teilChildNodes,$sum_zapati_tab1_array);


// a ted druha tabulka s prehledem podle lidi
$parameters=$_GET;

$datum_von=make_DB_datum($_GET['datum_von']);
$datum_bis=make_DB_datum($_GET['datum_bis']);
//$schicht_von=$_GET['schicht_von'];
//$schicht_bis=$_GET['schicht_bis'];
$pers_von=$_GET['pers_von'];
$pers_bis=$_GET['pers_bis'];
//$kunde=$_GET['kunde'];
$password = $_GET['password'];

require_once('S284_2_xml.php');

$pdf->AddPage();
pageheader($pdf,$cells_header_pers,5,"Leistung nach Personalnummer:");

// zacinam po persnr
$lidi=$domxml->getElementsByTagName("pers");
foreach($lidi as $clovek)
{
	
	$clovekChildNodes = $clovek->childNodes;
	
	if(test_pageoverflow_noheader($pdf,5))
			pageheader($pdf,$cells_header_pers,5,"Leistung nach Personalnummer:");
	telo($pdf,$cells_pers,5,array(255,255,255),"",$clovekChildNodes);
		
	// projedu pole a aktualizuju sumy pro zapati teil
	foreach($sum_zapati_tab2_array as $key=>$prvek)
	{
		$hodnota=getValueForNode($clovekChildNodes,$key);
		$sum_zapati_tab2_array[$key]+=$hodnota;
	}
} 

if(test_pageoverflow_noheader($pdf,5))
		pageheader($pdf,$cells_header_pers,5,"Leistung nach Personalnummer:");
zapati_tab2($pdf,5,array(200,200,255),$clovekChildNodes,$sum_zapati_tab2_array);


//Close and output PDF document
$pdf->Output();
}
//============================================================+
// END OF FILE                                                 
//============================================================+

?>
