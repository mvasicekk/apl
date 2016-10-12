<?php
require_once '../security.php';

$doc_title = "S617";
$doc_subject = "S617 Report";
$doc_keywords = "S617";

// necham si vygenerovat XML

$parameters=$_GET;
$datumvon=$_GET['datevon'];
$datumbis=$_GET['datebis'];

// casti datumu povolim oddelovat znaky : ,.- a mezera
	$vymenit=array(",",".","-"," ");
	if(strlen($datumvon)>=5)
	{
		// sjednotim si oddelovaci znak
		$novy_datum=str_replace($vymenit,"/",$datumvon);
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
	
// pridat naformatovani datumu ajaxem

	$datumvon=$rok."-".$mesic."-".$den;

// casti datumu povolim oddelovat znaky : ,.- a mezera
	$vymenit=array(",",".","-"," ");
	if(strlen($datumbis)>=5)
	{
		// sjednotim si oddelovaci znak
		$novy_datum=str_replace($vymenit,"/",$datumbis);
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
	
	$datumbis=$rok."-".$mesic."-".$den;
	
require_once('S617_xml.php');
//exit;
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
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells = array(
	    "terminF"
	    => array("popis" => "AuftragsNr", "sirka" => 30, "ram" => 1, "align" => "L", "radek" => 0, "fill" => 0),
	    "T_S0011"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0011", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0041"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0041", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0043"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0043", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0051"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0051", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0061"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0061", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0062"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0062", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0081"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0081", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_S0091"
	    => array("nf" => array(0, ',', ' '), "popis" => "S0091", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_X"
	    => array("nf" => array(0, ',', ' '), "popis" => "X", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "T_M"
	    => array("nf" => array(0, ',', ' '), "popis" => "M", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 0),
	    "celkem"
	    => array("nf" => array(0, ',', ' '), "popis" => "celkem", "sirka" => 0, "ram" => 1, "align" => "R", "radek" => 1, "fill" => 0)
);

$cells_header = array(
	    "terminF"
	    => array("popis" => "Plan Ex", "sirka" => 30, "ram" => 1, "align" => "L", "radek" => 0, "fill" => 1),
	    "T_S0011"
	    => array("popis" => "S0011", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0041"
	    => array("popis" => "S0041", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0043"
	    => array("popis" => "S0043", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0051"
	    => array("popis" => "S0051", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0061"
	    => array("popis" => "S0061", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0062"
	    => array("popis" => "S0062", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0081"
	    => array("popis" => "S0081", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_S0091"
	    => array("popis" => "S0091", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_X"
	    => array("popis" => "X", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "T_M"
	    => array("popis" => "M", "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
	    "celkem"
	    => array("popis" => "Summe", "sirka" => 0, "ram" => 1, "align" => "R", "radek" => 1, "fill" => 1)
);

$zapati_kunde = array("T_S0011"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0041"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0043"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0051"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0061"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0062"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0081"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0091"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_X"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_M"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "celkem"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 0, "ram" => 1, "align" => "R", "radek" => 1, "fill" => 1)
);

$zapati_pg = array("T_S0011"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0041"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0043"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0051"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0061"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0062"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0081"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0091"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_X"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_M"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "celkem"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 0, "ram" => 1, "align" => "R", "radek" => 1, "fill" => 1)
);

$zapati_report = array("T_S0011"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0041"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0043"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0051"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0061"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0062"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0081"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_S0091"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_X"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "T_M"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 20, "ram" => 1, "align" => "R", "radek" => 0, "fill" => 1),
    "celkem"
    => array("nf" => array(0, ',', ' '), "summe" => 0, "sirka" => 0, "ram" => 1, "align" => "R", "radek" => 1, "fill" => 1)
);

// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}

// funkce pro vykresleni zapati
function zapati($pdfobjekt,$pole,$zapativyskaradku,$rgb,$funkce)
{
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	foreach($pole as $cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format($cell[$funkce], $cell["nf"][0],
										$cell["nf"][1],
										$cell["nf"][2]);
		}
		else
		{
			$cellobsah=$cell[$funkce];
		}

		$pdfobjekt->Cell($cell["sirka"],$zapativyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,8);
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






$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S617", $params);
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
$produktgruppen=$domxml->getElementsByTagName("produktgruppe");
$vyska_radku=5;

foreach($produktgruppen as $produktgruppe)
{
	$kunden=$produktgruppe->getElementsByTagName("kunde");
	foreach($kunden as $kunde)
	{
		$auftraege = $kunde->getElementsByTagName("term");
		foreach($auftraege as $auftrag)
		{
			$polozky = $auftrag->childNodes;
			foreach($polozky as $child)
			{
				test_pageoverflow($pdf,$vyska_radku,$cells_header);
				// nakreslim bunku podle node v XML
				// pokud najdu $child->nodeName mezi klici v cells, tak nakreslim bunku
				if(array_key_exists($child->nodeName,$cells))
				{
					// pokud najdu klic nf, zformatuju obsah bunky jako cislo, podle zadanych parametru
					if(array_key_exists("nf",$cells[$child->nodeName]))
					{
						$cellobsah = 
						number_format($child->nodeValue,$cells[$child->nodeName]["nf"][0],
														$cells[$child->nodeName]["nf"][1],
														$cells[$child->nodeName]["nf"][2]);
					}
					else
					{
						$cellobsah=$child->nodeValue;
					}
					$pdf->Cell($cells[$child->nodeName]["sirka"],$vyska_radku,$cellobsah,$cells[$child->nodeName]["ram"],$cells[$child->nodeName]["radek"],$cells[$child->nodeName]["align"],$cells[$child->nodeName]["fill"]);
				}
				// soucty pro zapati kunden
				if(array_key_exists($child->nodeName,$zapati_kunde))
				{
					$zapati_kunde[$child->nodeName]["sum"]+=$child->nodeValue;
				}
			}
		}
		// zapati kunde
		test_pageoverflow($pdf,$vyska_radku,$cells_header);
		$pdf->Cell(30,$vyska_radku,"kunde:  ".$kunde->firstChild->nodeValue."   ".$soucet_str,1,0,'L',0);
		zapati($pdf,$zapati_kunde,5,array(220,220,255),"sum");
		// soucty pro pg a vynulovani sum u zapati kunde
		foreach($zapati_kunde as $klic=>$s)
		{
			if(array_key_exists($klic,$zapati_pg))
			{
				$zapati_pg[$klic]["sum"]+=$zapati_kunde[$klic]["sum"];
			}
			// mam pripocteno, muzu vynulovat
			$zapati_kunde[$klic]["sum"]=0;
		}
	}
	//zapati produktgruppe
	test_pageoverflow($pdf,$vyska_radku,$cells_header);
	$pdf->Cell(30,$vyska_radku,"PG:  ".$produktgruppe->firstChild->nodeValue."   ".$soucet_str,1,0,'L',0);
	zapati($pdf,$zapati_pg,5,array(220,255,220),"sum");
	// soucty pro sestavu a vynulovani sum u zapati pg
	foreach($zapati_pg as $klic=>$s)
	{
		if(array_key_exists($klic,$zapati_report))
		{
			$zapati_report[$klic]["sum"]+=$zapati_pg[$klic]["sum"];
		}
		// mam pripocteno, muzu vynulovat
		$zapati_pg[$klic]["sum"]=0;
	}
	// odstrankovat za sekci ?
	//$pdf->AddPage();
	//pageheader($pdf,$cells_header,8);
}
	test_pageoverflow($pdf,$vyska_radku,$cells_header);			
	//zapati report
	$pdf->Cell(30,$vyska_radku,"Gesamt:  ",1,0,'L',0);
	zapati($pdf,$zapati_report,5,array(255,220,220),"sum");

	// vynulovani sum pro sestavvu, abych mohl udelat celkovy souhrn
	foreach($zapati_report as $klic=>$s)
	{
		$zapati_report[$klic]["sum"]=0;
	}
	
// na konec sestavy jeste jednou shrnuti bez auftragu
$pdf->AddPage();
pageheader($pdf,$cells_header,8);
$produktgruppen=$domxml->getElementsByTagName("produktgruppe");
$vyska_radku=5;

foreach($produktgruppen as $produktgruppe)
{
	$kunden=$produktgruppe->getElementsByTagName("kunde");
	foreach($kunden as $kunde)
	{
		$auftraege = $kunde->getElementsByTagName("term");
		foreach($auftraege as $auftrag)
		{
			$polozky = $auftrag->childNodes;
			foreach($polozky as $child)
			{
				test_pageoverflow($pdf,$vyska_radku,$cells_header);
				// nakreslim bunku podle node v XML
				// pokud najdu $child->nodeName mezi klici v cells, tak nakreslim bunku
				if(array_key_exists($child->nodeName,$cells))
				{
					// pokud najdu klic nf, zformatuju obsah bunky jako cislo, podle zadanych parametru
					if(array_key_exists("nf",$cells[$child->nodeName]))
					{
						$cellobsah = 
						number_format($child->nodeValue,$cells[$child->nodeName]["nf"][0],
														$cells[$child->nodeName]["nf"][1],
														$cells[$child->nodeName]["nf"][2]);
					}
					else
					{
						$cellobsah=$child->nodeValue;
					}
					//$pdf->Cell($cells[$child->nodeName]["sirka"],$vyska_radku,$cellobsah,$cells[$child->nodeName]["ram"],$cells[$child->nodeName]["radek"],$cells[$child->nodeName]["align"],$cells[$child->nodeName]["fill"]);
				}
				// soucty pro zapati kunden
				if(array_key_exists($child->nodeName,$zapati_kunde))
				{
					$zapati_kunde[$child->nodeName]["sum"]+=$child->nodeValue;
				}
			}
		}
		// zapati kunde
		test_pageoverflow($pdf,$vyska_radku,$cells_header);
		$pdf->Cell(30,$vyska_radku,"kunde:  ".$kunde->firstChild->nodeValue."   ".$soucet_str,1,0,'L',0);
		zapati($pdf,$zapati_kunde,5,array(220,220,255),"sum");
		// soucty pro pg a vynulovani sum u zapati kunde
		foreach($zapati_kunde as $klic=>$s)
		{
			if(array_key_exists($klic,$zapati_pg))
			{
				$zapati_pg[$klic]["sum"]+=$zapati_kunde[$klic]["sum"];
			}
			// mam pripocteno, muzu vynulovat
			$zapati_kunde[$klic]["sum"]=0;
		}
	}
	//zapati produktgruppe
	test_pageoverflow($pdf,$vyska_radku,$cells_header);
	$pdf->Cell(30,$vyska_radku,"PG:  ".$produktgruppe->firstChild->nodeValue."   ".$soucet_str,1,0,'L',0);
	zapati($pdf,$zapati_pg,5,array(220,255,220),"sum");
	// soucty pro sestavu a vynulovani sum u zapati pg
	foreach($zapati_pg as $klic=>$s)
	{
		if(array_key_exists($klic,$zapati_report))
		{
			$zapati_report[$klic]["sum"]+=$zapati_pg[$klic]["sum"];
		}
		// mam pripocteno, muzu vynulovat
		$zapati_pg[$klic]["sum"]=0;
	}
	// odstrankovat za sekci ?
	//$pdf->AddPage();
	//pageheader($pdf,$cells_header,8);
}
	test_pageoverflow($pdf,$vyska_radku,$cells_header);			
	//zapati report
	$pdf->Cell(30,$vyska_radku,"Gesamt:  ",1,0,'L',0);
	zapati($pdf,$zapati_report,5,array(255,220,220),"sum");

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
