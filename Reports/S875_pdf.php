<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "S875";
$doc_subject = "S875 Report";
$doc_keywords = "S875";

// necham si vygenerovat XML

$parameters=$_GET;
$ausliefer_von=make_DB_datum($_GET['ausliefer_von']);
$ausliefer_bis=make_DB_datum($_GET['ausliefer_bis']);
$kunde=$_GET['kunde'];
$teil = trim($_GET['teil']);

$teil = strtr($teil, '*', '%');

require_once('S875_xml.php');

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
"dummy1" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"statnr"
=> array ("popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text"
=> array ("popis"=>"","sirka"=>50,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"betrag"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(
"dummy1" 
=> array ("popis"=>"RechNr","sirka"=>30,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"statnr"
=> array ("popis"=>"StatNR","sirka"=>20,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"text"
=> array ("popis"=>"StatNr - Beschreibung","sirka"=>50,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"betrag" 
=> array ("nf"=>array(2,',',' '),"popis"=>"Betrag","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>0)

);

$sum_zapati_auftrag_array = array(	
								"betrag"=>0,
								);

$sum_zapati_sestava_array = array(
								"betrag"=>0
								);

$tat_sum_array = array(
						'xxx'=>array(
                                                                                'tat'=>'',
										'text'=>'neco',
										'betrag'=>0
									)
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
function zahlavi_auftrag($pdfobjekt,$vyskaradku,$rgb,$auftragsnr,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(0,$vyskaradku,$auftragsnr,'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	/*
	foreach($cells as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$vyskaradku,$cell["popis"],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	*/
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_auftrag($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole)
{
        global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell($cells['auftragsnr']['sirka']+$cells['statnr']['sirka']+$cells['text']['sirka'],$vyskaradku,$popis,'B',0,'L',$fill);
	
	$obsah=$pole['betrag'];
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
	$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,3.5);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S875 StatNr je Rechnung", $params);
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


// a ted pujdu po zakazkach
$auftraege=$domxml->getElementsByTagName("auftraege");
foreach($auftraege as $auftrag)
{
	$auftragChilds = $auftrag->childNodes;
        $auftragsnr = getValueForNode($auftragChilds, 'auftragsnr');
	test_pageoverflow($pdf,5,$cells_header);
	zahlavi_auftrag($pdf,5,array(255,255,255),$auftragsnr,$cells_header);

	nuluj_sumy_pole($sum_zapati_auftrag_array);

	$tatnodes=$auftrag->getElementsByTagName("statnr_row");
	foreach($tatnodes as $tatnode)
	{
		$tat_childs=$tatnode->childNodes;
		test_pageoverflow($pdf,3.5,$cells_header);
		telo($pdf,$cells,3.5,array(255,255,255),"",$tat_childs);
                $tat = getValueForNode($tat_childs, 'statnr');

		// projedu pole a aktualizuju sumy pro zapati auftrag
		foreach($sum_zapati_auftrag_array as $key=>$prvek)
		{
			$hodnota=$tatnode->getElementsByTagName($key)->item(0)->nodeValue;
			$sum_zapati_auftrag_array[$key]+=$hodnota;
		}

		// udelam soucty podle cinnosti
		$sum_tat_array[$tat]['betrag']+=floatval(getValueForNode($tat_childs, 'betrag'));
		$sum_tat_array[$tat]['text']=getValueForNode($tat_childs, 'text');
		$sum_tat_array[$tat]['statnr']=$tat;
	}
//
	zapati_auftrag($pdf,$tatnode,5,"Summe Rechnung",array(235,235,235),$sum_zapati_auftrag_array);
	// projedu pole a aktualizuju sumy pro zapati sestava
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_auftrag_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
}

//echo "<pre>".var_dump($sum_tat_array)."</pre>";
// tabulka se souctama podle cinnosti
$pdf->AddPage();
pageheader($pdf,$cells_header,5);
// projdu jednotlive cinnosti v poli

ksort($sum_tat_array);

foreach($sum_tat_array as $key=>$sum_tat)
{
	$pdf->SetFont("FreeSans", "", 8);
	$pdf->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($cells as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah =
			number_format($sum_tat_array[$key][$nodename], $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=$sum_tat_array[$key][$nodename];
		}
		$pdf->Cell($cell["sirka"],5,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdf->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdf->SetFont("FreeSans", "", 8);

}

zapati_auftrag($pdf,$palettenode,5,"Gesammtsumme ",array(200,200,255),$sum_zapati_sestava_array);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
