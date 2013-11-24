<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "D210";
$doc_subject = "D210 Report";
$doc_keywords = "D210";

// necham si vygenerovat XML

$parameters=$_GET;

$palvon=$_GET['palvon'];
$palbis=$_GET['palbis'];
$auftragsnr=$_GET['auftragsnr'];
$order = $_GET['order'];

require_once('D210_xml.php');


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
// zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);
function zobraz_paletu($pdfobjekt,$paletteChildNodes,$importChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;

	// hlavni tabulka ma 3 radky

	$x_pocatek=$pdfobjekt->GetX();
	$y_pocatek=$pdfobjekt->GetY();
	$y_offset = 10;
	$auftragsnr_vyska = 110;
	$paletaPoziceX = 150;
	$datumCas = date('d.m.Y H:i:s');
	$ident = get_user();
	
	$datumCas = $datumCas. " ( $ident )";
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(10,15+$y_offset,"Auftrag / zakazka");
	$pdfobjekt->Text(100,15+$y_offset,"Stck / kusu :");
	$pdfobjekt->SetFont("Arial", "B", 20);
	$pdfobjekt->Text(150,15+$y_offset,getValueForNode($paletteChildNodes,"stk"));
	
	$pdfobjekt->SetFont("Arial", "B", $auftragsnr_vyska);
	$pdfobjekt->Text(10,50+$y_offset,getValueForNode($paletteChildNodes,"auftragsnr")."/");
	
	$pdfobjekt->SetFont("Arial", "", 70);
	$pdfobjekt->Text($paletaPoziceX,50+$y_offset,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("Arial", "", 15);
	$pdfobjekt->Text($paletaPoziceX+5,28+$y_offset,"Palette / paleta");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	//$pdfobjekt->SetXY(10,10);
	$pdfobjekt->Text(10,70+$y_offset,"Teil / dil");
	$pdfobjekt->SetFont("Arial", "B", 90);
	$pdfobjekt->Text(10,100+$y_offset,getValueForNode($paletteChildNodes,"teil"));
	
	
	$pdfobjekt->SetFont("Arial", "", 10);
	$pdfobjekt->Text(10,130+$y_offset,"Abydos s.r.o."."   $datumCas");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(120,130+$y_offset,"Gew / vaha");
	$pdfobjekt->Text(160,130+$y_offset,getValueForNode($paletteChildNodes,"gew")." Kg");
	
	$pdfobjekt->StartTransform();
	//point reflection at the lower left point of rectangle
	$pdfobjekt->MirrorP(105,150);
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(10,15+$y_offset,"Auftrag / zakazka");
	$pdfobjekt->Text(100,15+$y_offset,"Stck / kusu :");
	$pdfobjekt->SetFont("Arial", "B", 20);
	$pdfobjekt->Text(150,15+$y_offset,getValueForNode($paletteChildNodes,"stk"));
	
	$pdfobjekt->SetFont("Arial", "B", $auftragsnr_vyska);
	$pdfobjekt->Text(10,50+$y_offset,getValueForNode($paletteChildNodes,"auftragsnr")."/");
	
	$pdfobjekt->SetFont("Arial", "", 70);
	$pdfobjekt->Text($paletaPoziceX,50+$y_offset,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("Arial", "", 15);
	$pdfobjekt->Text($paletaPoziceX+5,28+$y_offset,"Palette / paleta");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	//$pdfobjekt->SetXY(10,10);
	$pdfobjekt->Text(10,70+$y_offset,"Teil / dil");
	$pdfobjekt->SetFont("Arial", "B", 90);
	$pdfobjekt->Text(10,100+$y_offset,getValueForNode($paletteChildNodes,"teil"));
	
	$pdfobjekt->SetFont("Arial", "", 10);
	$pdfobjekt->Text(10,130+$y_offset,"Abydos s.r.o."."   $datumCas");
	
	$pdfobjekt->SetFont("Arial", "", 20);
	$pdfobjekt->Text(120,130+$y_offset,"Gew / vaha");
	$pdfobjekt->Text(160,130+$y_offset,getValueForNode($paletteChildNodes,"gew")." Kg");
	
	//	Stop Transformation
	$pdfobjekt->StopTransform();
	
	
	/*
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"kunde"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"name1"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"name2"));$pdfobjekt->Ln();
*/
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}




require('../fpdf/transform.php');

$pdf = new PDF_Transform('P','mm','A4');
//$pdf = new TCPDF('L','mm','A4',1);

//$pdf->AddPage();

// zacinam po importech

$importe=$domxml->getElementsByTagName("auftrag");
foreach($importe as $import)
{
	$importChildNodes = $import->childNodes;

	// ted jdu po paletach
	$paletten=$import->getElementsByTagName("palette");
	foreach($paletten as $palette)
	{
		$pdf->AddPage();
		$paletteChildNodes = $palette->childNodes;
		zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);
	}
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
