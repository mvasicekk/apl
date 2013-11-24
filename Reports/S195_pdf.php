<?php
session_start();

$doc_title = "S195";
$doc_subject = "S195 Report";
$doc_keywords = "S195";

$datumvon=$_GET['datumvon'];
$datumbis=$_GET['datumbis'];

$datum=$datumvon;
	// casti datumu povolim oddelovat znaky : ,.- a mezera
	$vymenit=array(",",".","-"," ");
	if(strlen($datum)>=5)
	{
		// sjednotim si oddelovaci znak
		$novy_datum=str_replace($vymenit,"/",$datum);
		// rozkouskuju na jednotlivy casti
		$dily=explode("/",$novy_datum);
		// trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
		if(($dily[1]<13)&&($dily[1]>0)&&($dily[0]>0)&&($dily[0]<32))
		{
			$timestamp=mktime(0,0,0,$dily[1],$dily[0],$dily[2]);
			$rok=date("Y",$timestamp);
			$mesic=date("m",$timestamp);
			$den=date("d",$timestamp);
			// provedena jen mala kontrola datumu
			//echo "$den.$mesic.$rok";
		}
	}
	$datum=$rok."-".$mesic."-".$den;

$datumvon=$datum;

$datum=$datumbis;
	// casti datumu povolim oddelovat znaky : ,.- a mezera
	$vymenit=array(",",".","-"," ");
	if(strlen($datum)>=5)
	{
		// sjednotim si oddelovaci znak
		$novy_datum=str_replace($vymenit,"/",$datum);
		// rozkouskuju na jednotlivy casti
		$dily=explode("/",$novy_datum);
		// trochu otestuju jednotlivy dily,jestli tam neni uplnej nesmysl
		if(($dily[1]<13)&&($dily[1]>0)&&($dily[0]>0)&&($dily[0]<32))
		{
			$timestamp=mktime(0,0,0,$dily[1],$dily[0],$dily[2]);
			$rok=date("Y",$timestamp);
			$mesic=date("m",$timestamp);
			$den=date("d",$timestamp);
			// provedena jen mala kontrola datumu
			//echo "$den.$mesic.$rok";
		}
	}
	$datum=$rok."-".$mesic."-".$den;

$datumbis=$datum;
$teile=$_GET['teile'];

	$parameters=$_GET;	
	require_once('S195_xml.php');

// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");
//$paramnodes=$parameters->childNodes;

foreach ($parameters as $param)
{
	$parametry=$param->childNodes;
	//print_r($parametry);
	// v ramci parametru si prectu label a hodnotu
	//$params.=$parametry->nodeName.": ".$parametry->nodeValue."   ";
	foreach($parametry as $parametr)
	{
		//echo $parametr->nodeName;
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

$cells = 
array(
"PersNr" 
=> array ("popis"=>"PersNr","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>0),
"Name" 
=> array ("popis"=>"Name","sirka"=>60,"ram"=>"BTR","align"=>"L","radek"=>0,"fill"=>0),
"Teil" 
=> array ("popis"=>"Teil","sirka"=>60,"ram"=>"BTR","align"=>"L","radek"=>0,"fill"=>0),
"stk_plus_auss" 
=> array ("popis"=>"Stk","sirka"=>0,"ram"=>'RTB',"align"=>"R","radek"=>1,"fill"=>0)
);

$cells_header = 
array(
"PersNr" 
=> array ("popis"=>"PersNr","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"Name" 
=> array ("popis"=>"Name","sirka"=>60,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"Teil" 
=> array ("popis"=>"Teil","sirka"=>60,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"stk_plus_auss" 
=> array ("popis"=>"Stk","sirka"=>0,"ram"=>1,"align"=>"R","radek"=>1,"fill"=>1)
);

// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}
					
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S195 obrousene kusy", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);


// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,8);
$rows=$domxml->getElementsByTagName("row");

$vyska_radku=5;
// projedu jednotlive radky
foreach ($rows as $node)
{
	//echo "nodeName=".$node->nodeName;
	//echo " nodeType=".$node->nodeType;
	//echo " parentNode->nodeName=".$node->parentNode->nodeName."<br>";
	$childnodes = $node->childNodes;
	
	foreach ($childnodes as $child)
	{
		
		// pokud bych prelezl s nasledujicim vystupem vysku stranky
		// tak vytvorim novou stranku i se zahlavim
		if(($pdf->GetY()+$vyska_radku)>($pdf->getPageHeight()-$pdf->getBreakMargin()))
		{
			$pdf->AddPage();
			pageheader($pdf,$cells_header,8);
		}
		// nakreslim bunku podle node v XML
		// pokud najdu $child->nodeName mezi klici v cells, tak nakreslim bunku
		if(array_key_exists($child->nodeName,$cells))
			$pdf->Cell($cells[$child->nodeName]["sirka"],$vyska_radku,$child->nodeValue,$cells[$child->nodeName]["ram"],$cells[$child->nodeName]["radek"],$cells[$child->nodeName]["align"],$cells[$child->nodeName]["fill"]);
	}
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
