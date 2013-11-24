<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S165";
$doc_subject = "S165 Report";
$doc_keywords = "S165";

// necham si vygenerovat XML

$parameters=$_GET;
$datum=make_DB_datum($_GET['datum']);


require_once('S165_xml.php');

//exit;
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

"dummy"
=> array ("popis"=>"","sirka"=>60,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"anw_von"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"anw_bis"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stunden"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"pause1"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"pause2"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"schicht"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"tat"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"essen"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(
"dummy" 
=> array ("popis"=>"PersNr,Name","sirka"=>60,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"anw_von"
=> array ("popis"=>"od","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"anw_bis"
=> array ("popis"=>"do","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"stunden"
=> array ("nf"=>array(1,',',' '),"popis"=>"hodiny","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"pause1"
=> array ("nf"=>array(1,',',' '),"popis"=>"pause1","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"pause2"
=> array ("nf"=>array(1,',',' '),"popis"=>"pause2","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"schicht"
=> array ("popis"=>"Sch","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"tat"
=> array ("popis"=>"tat","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"essen"
=> array ("popis"=>"essen","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),

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
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
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
	$pdfobjekt->SetFont("FreeSans", "", 8);
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
function zahlavi_person($pdfobjekt,$vyskaradku,$rgb,$childs,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $persnr = getValueForNode($childs, 'persnr');
        $name = getValueForNode($childs, 'name');
        $pdfobjekt->Cell(10,$vyskaradku,$persnr,'T',0,'R',$fill);
        $pdfobjekt->Cell(0,$vyskaradku,$name,'T',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
	}
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S165 Anwesenheit - Tag", $params);
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

$personen=$domxml->getElementsByTagName("person");
foreach($personen as $person)
{
        $personChilds = $person->childNodes;
        $anwesenheiten = $person->getElementsByTagName('anwesenheit');

        $vyskasekce = 5*1+3.5*count($anwesenheiten);
	test_pageoverflow($pdf,$vyskasekce,$cells_header);
	zahlavi_person($pdf,5,array(255,255,255),$personChilds,$cells_header);
        
        foreach($anwesenheiten as $anwesenheit){
            $anwesenheitChilds = $anwesenheit->childNodes;
//            test_pageoverflow($pdf,5,$cells_header);
            telo($pdf,$cells,3.5,array(255,255,255),"",$anwesenheitChilds);
        }
}

//	nuluj_sumy_pole($sum_zapati_schicht_array);
//
//	$persnodes=$schicht->getElementsByTagName("pers");
//	foreach($persnodes as $persnode)
//	{
//		$pers_childs=$persnode->childNodes;
//
//		test_pageoverflow($pdf,3.5,$cells_header);
//		telo($pdf,$cells,3.5,array(255,255,255),"",$pers_childs);
//
//		// projedu pole a aktualizuju sumy pro zapati schicht
//		foreach($sum_zapati_schicht_array as $key=>$prvek)
//		{
//			$hodnota=$persnode->getElementsByTagName($key)->item(0)->nodeValue;
//			$sum_zapati_schicht_array[$key]+=$hodnota;
//		}
//	}
//
//	if($sum_zapati_schicht_array['verb']!=0)
//		$fac1=$sum_zapati_schicht_array['vzaby']/$sum_zapati_schicht_array['verb']*100;
//	else
//		$fac1=0;
//
//	if($sum_zapati_schicht_array['anwesenheit']!=0)
//		$fac2=$sum_zapati_schicht_array['vzaby']/$sum_zapati_schicht_array['anwesenheit']*100;
//	else
//		$fac2=0;
//
//	if($sum_zapati_schicht_array['anwesenheit']!=0)
//		$fac3=$sum_zapati_schicht_array['verb']/$sum_zapati_schicht_array['anwesenheit']*100;
//	else
//		$fac3=0;
//
//	test_pageoverflow($pdf,5,$cells_header);
//	zapati_schicht($pdf,$persnode,5,"Summe Schicht",array(235,235,235),$sum_zapati_schicht_array,$fac1,$fac2,$fac3);
//	// projedu pole a aktualizuju sumy pro zapati sestava
//	foreach($sum_zapati_sestava_array as $key=>$prvek)
//	{
//		$hodnota=$sum_zapati_schicht_array[$key];
//		$sum_zapati_sestava_array[$key]+=$hodnota;
//	}
//
//
//}
//
//if($sum_zapati_sestava_array['verb']!=0)
//	$fac1=$sum_zapati_sestava_array['vzaby']/$sum_zapati_sestava_array['verb']*100;
//else
//	$fac1=0;
//
//if($sum_zapati_sestava_array['anwesenheit']!=0)
//	$fac2=$sum_zapati_sestava_array['vzaby']/$sum_zapati_sestava_array['anwesenheit']*100;
//else
//	$fac2=0;
//
//if($sum_zapati_sestava_array['anwesenheit']!=0)
//	$fac3=$sum_zapati_sestava_array['verb']/$sum_zapati_sestava_array['anwesenheit']*100;
//else
//	$fac3=0;
//
//test_pageoverflow($pdf,5,$cells_header);
//zapati_schicht($pdf,$persnode,5,"Gesammtsumme ",array(200,200,255),$sum_zapati_sestava_array,$fac1,$fac2,$fac3);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
