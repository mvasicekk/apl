<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S420";
$doc_subject = "S420 Report";
$doc_keywords = "S420";

// necham si vygenerovat XML

$parameters=$_GET;

$import=$_GET['import'];

require_once('S420_xml.php');

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

//"export"
//=> array ("popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"teilnr" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),

"pal"
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

//"stk_exp"
//=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>12,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gew_brutto"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>18,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gew_netto"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>18,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"ln"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);


$sum_zapati_import_array = array(
    "gew_brutto" => 0,
    "gew_netto" => 0,
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
//"export"
//"teilnr"
//"pal"
//"stk_exp"
//"stk"
//"gew_brutto"
//"gew_netto"

    global $cells;
    $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	$fill=1;

//        $pdfobjekt->Cell($cells['export']['sirka'],$headervyskaradku,"Export",'LRBT',0,'L',1);
        $pdfobjekt->Cell($cells['teilnr']['sirka'],$headervyskaradku,"Teil",'LRBT',0,'L',1);
        $pdfobjekt->Cell($cells['pal']['sirka'],$headervyskaradku,"IMP-Pal",'LRBT',0,'R',1);
//        $pdfobjekt->Cell($cells['stk_exp']['sirka'],$headervyskaradku,"Stk-EX",'LRBT',0,'R',1);
        $pdfobjekt->Cell($cells['stk']['sirka'],$headervyskaradku,"Stk-IM",'LRBT',0,'R',1);
        $pdfobjekt->Cell($cells['gew_brutto']['sirka'],$headervyskaradku,"Gew-Brutto",'LRBT',0,'R',1);
        $pdfobjekt->Cell($cells['gew_netto']['sirka'],$headervyskaradku,"Gew-Netto",'LRBT',0,'R',1);
        $pdfobjekt->Ln();
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
function zapati_import($pdfobjekt,$sumArray)
{

    global $cells;
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(
                $cells['teilnr']['sirka']+
                $cells['pal']['sirka']+
                
                $cells['stk']['sirka'],
                5,"Summe: ",'LRBT',0,'L',0);

        $obsah = number_format($sumArray['gew_brutto'],1,',',' ');
        $pdfobjekt->Cell($cells['gew_brutto']['sirka'],
                5,$obsah,'LRBT',0,'R',0);

        $obsah = number_format($sumArray['gew_netto'],1,',',' ');
        $pdfobjekt->Cell($cells['gew_netto']['sirka'],
                5,$obsah,'LRBT',0,'R',0);

        $pdfobjekt->Ln();

        }


////////////////////////////////////////////////////////////////////////////////////////////////////
// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 9);
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

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S420 - Import - Gewicht", $params);
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
	
	pageheader($pdf,$cells,5);
	// ted jdu po dilech
	$teile=$import->getElementsByTagName("teil");
	foreach($teile as $teil)
	{
		$teilChildNodes = $teil->childNodes;
		// a konecne po paletach
		$palety = $teil->getElementsByTagName("palette");
		foreach($palety as $paleta)
		{
			$paletaChildNodes = $paleta->childNodes;
			test_pageoverflow($pdf,5,$cells);
			detaily($pdf,$cells,5,array(255,255,255),$paletaChildNodes);
			// nascitam casy
			foreach($sum_zapati_import_array as $key=>$prvek)
			{
				$hodnota = getValueForNode($paletaChildNodes,$key);
				$sum_zapati_import_array[$key]+=$hodnota;
			}
		}
	}
//        var_dump($sum_zapati_import_array);
	test_pageoverflow($pdf,5,$cells);
	zapati_import($pdf,$sum_zapati_import_array);
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
