<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S791";
$doc_subject = "S791 Report";
$doc_keywords = "S791";

// necham si vygenerovat XML

$parameters=$_GET;
$kunde_von=$_GET['kunde_von'];
$kunde_bis=$_GET['kunde_bis'];
$ausliefer_von=make_DB_datum($_GET['ausliefer_von']);
$ausliefer_bis=make_DB_datum($_GET['ausliefer_bis']);


require_once('S791_xml.php');


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
"im" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'BL',"align"=>"R","radek"=>0,"fill"=>0),
"dummy1" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
"dummy2" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"kdmin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
"abymin" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
"vzkd1999" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
"vzaby1999" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
"vzaby3999" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"dummy3" 
=> array ("popis"=>"","sirka"=>35,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"aufgew" 
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
"eur_pro_tonne" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
"fac1" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
"fac2" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"Aufdat" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'LB',"align"=>"L","radek"=>0,"fill"=>0),

"fertig" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"ausliefer_datum" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"L","radek"=>1,"fill"=>0));

$kunde_header = 
array(
"kunde" 
=> array ("popis"=>"kunde","sirka"=>0,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1)
);

$cells_header = 
array(
"auftragsnr" 
=> array ("popis"=>"\nAuftragsN","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"wert" 
=> array ("popis"=>"\nWert","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"kdminrechn" 
=> array ("popis"=>"kdmin.\nRechn","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"kdmin" 
=> array ("popis"=>"RM\nkdmin","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"abymin" 
=> array ("popis"=>"RM\nabymin","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"verb" 
=> array ("popis"=>"RM\nverb","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"vzkd1999" 
=> array ("popis"=>"vzkd\n9X or >1999","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"vzaby19999" 
=> array ("popis"=>"vzaby\n9X or >1999","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"vzaby3999" 
=> array ("popis"=>"vzaby\n >3999","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"dummy1" 
=> array ("popis"=>"\n","sirka"=>35,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"extonnen" 
=> array ("popis"=>"export.\nTonnen","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"eur_pro_tonne" 
=> array ("popis"=>"EUR\nTonn","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"kdminabymin" 
=> array ("popis"=>"kdmin\nabymin","sirka"=>10,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"kdminverb" 
=> array ("popis"=>"kdmin\nverb","sirka"=>10,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"aufdat" 
=> array ("popis"=>"\nAufdat","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"rech" 
=> array ("popis"=>"\nRech","sirka"=>15,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"auslief" 
=> array ("popis"=>"\nAuslief","sirka"=>0,"ram"=>1,"align"=>"L","radek"=>1,"fill"=>1)
);

$sum_zapati_ex_array = array("sumpreismin_leistung"=>0,"sumpreis_leistung_EUR"=>0,"sumpreis_sonst_EUR"=>0,"kdmin"=>0,"abymin"=>0,"verb"=>0,"vzkd1999"=>0,"vzaby1999"=>0,"vzaby3999"=>0,"aufgew"=>0);
global $sum_zapati_ex_array;

$sum_zapati_mesic_array = array("sumpreismin_leistung"=>0,"sumpreis_leistung_EUR"=>0,"sumpreis_sonst_EUR"=>0,"kdmin"=>0,"abymin"=>0,"verb"=>0,"vzkd1999"=>0,"vzaby1999"=>0,"vzaby3999"=>0,"aufgew"=>0);
global $sum_zapati_mesic_array;

$sum_zapati_kunde_array = array("sumpreismin_leistung"=>0,"sumpreis_leistung_EUR"=>0,"sumpreis_sonst_EUR"=>0,"kdmin"=>0,"abymin"=>0,"verb"=>0,"vzkd1999"=>0,"vzaby1999"=>0,"vzaby3999"=>0,"aufgew"=>0);
global $sum_zapati_kunde_array;

$sum_zapati_sestava_array = array("sumpreismin_leistung"=>0,"sumpreis_leistung_EUR"=>0,"sumpreis_sonst_EUR"=>0,"kdmin"=>0,"abymin"=>0,"verb"=>0,"vzkd1999"=>0,"vzaby1999"=>0,"vzaby3999"=>0,"aufgew"=>0);
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
			number_format(floatval(getValueForNode($nodelist,$nodename)), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
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

function zahlavi_ex($pdfobjekt,$node,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(20,$vyskaradku,"EX ".$node->getElementsByTagName("ex")->item(0)->nodeValue,'LBT',0,'L',$fill);

	$obsah=$node->getElementsByTagName("sumpreis_leistung_EUR")->item(0)->nodeValue;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(50,$vyskaradku,"Re-Preis (Leistung) ".$obsah,'BT',0,'L',$fill);

	
	$obsah=$node->getElementsByTagName("sumpreis_sonst_EUR")->item(0)->nodeValue;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(50,$vyskaradku,"Re-Preis (Sonst.) ".$obsah,'BT',0,'L',$fill);
	
	$obsah=$node->getElementsByTagName("preismin")->item(0)->nodeValue;
	$obsah=number_format($obsah,3,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"preismin ".$obsah,'BTR',1,'L',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function zahlavi_mesic($pdfobjekt,$node,$vyskaradku,$rgb)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(0,$vyskaradku,"Auslieferung Monat: ".$node->nodeValue,'LBTR',1,'L',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_ex($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1,$fac2,$ept)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	
	$pdfobjekt->Cell(15,$vyskaradku,$popis,'LBTR',0,'L',$fill);
	
	$obsah=$pole['sumpreis_sonst_EUR']+$pole['sumpreis_leistung_EUR'];
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['sumpreismin_leistung'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['kdmin'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BTR',0,'R',$fill);

	$obsah=$pole['abymin'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);
	
	$obsah=$pole['vzkd1999'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$pole['vzaby1999'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BT',0,'R',$fill);
	
	$obsah=$pole['vzaby3999'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BTR',0,'R',$fill);
	
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(35,$vyskaradku,$obsah,'BT',0,'R',$fill);
	
	$obsah=$pole['aufgew'];
	$obsah=number_format($obsah,1,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'LBT',0,'R',$fill);

	$obsah=$ept;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'BTR',0,'R',$fill);

	$obsah=$fac1;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BT',0,'R',$fill);

	$obsah=$fac2;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'BTR',0,'R',$fill);

	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(12,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(12,$vyskaradku,$obsah,'BT',0,'R',$fill);
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BTR',1,'R',$fill);


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

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S791 Auftragsuebersicht nach Export", $params);
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
pageheader($pdf,$cells_header,3.5);
$pdf->Ln();
$pdf->Ln();

// a ted pujdu po zakaznicich
$kunden=$domxml->getElementsByTagName("kunden");
foreach($kunden as $kunde)
{
	$kundenodes=$kunde->getElementsByTagName("kunde");
	foreach($kundenodes as $kundenode)
	{
		$pdf->Ln();
		
		nuluj_sumy_pole($sum_zapati_kunde_array);
		$mesicenodes=$kunde->getElementsByTagName("mesice");
		foreach($mesicenodes as $mesicnode)
		{
			$mesic=$mesicnode->getElementsByTagName("mesic")->item(0);
			// zahlavi pro mesic
			test_pageoverflow($pdf,5,$cells_header);
			zahlavi_mesic($pdf,$mesic,5,array(230,230,255));
		
			nuluj_sumy_pole($sum_zapati_mesic_array);
					
			$exportsnodes=$mesicnode->getElementsByTagName("exports");
			foreach($exportsnodes as $exportnode)
			{
				$exporty=$exportnode->getElementsByTagName("export");
				foreach($exporty as $export)
				{
					// zahlavi pro export
					test_pageoverflow($pdf,5,$cells_header);
					zahlavi_ex($pdf,$export,5,array(255,255,230));
					
					$preismin=$export->getElementsByTagName("preismin")->item(0)->nodeValue;
					
					$export_childs=$export->childNodes;

					nuluj_sumy_pole($sum_zapati_ex_array);
					$key="sumpreismin_leistung";
					$hodnota=$export->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_ex_array[$key]+=$hodnota;
					
					$key="sumpreis_leistung_EUR";
					$hodnota=$export->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_ex_array[$key]+=$hodnota;

					$key="sumpreis_sonst_EUR";
					$hodnota=$export->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_ex_array[$key]+=$hodnota;
					
					$importsnodes=$export->getElementsByTagName("imports");
					foreach($importsnodes as $importnode)
					{
						$importy=$importnode->getElementsByTagName("import");
						foreach($importy as $import)
						{
							$import_childs=$import->childNodes;
							
							// projedu pole a aktualizuju sumy pro zapati ex
							foreach($sum_zapati_ex_array as $key=>$prvek)
							{
								if(($key!="sumpreismin_leistung")&&($key!="sumpreis_leistung_EUR")&&($key!="sumpreis_sonst_EUR"))
								{
									$hodnota=$import->getElementsByTagName($key)->item(0)->nodeValue;
									$sum_zapati_ex_array[$key]+=$hodnota;
								}
							}

                            test_pageoverflow($pdf,3.5,$cells_header);
							telo($pdf,$cells,3.5,array(255,255,255),"",$import_childs);
							
						}
					}
					
					// zapati pro export
					test_pageoverflow($pdf,5,$cells_header);
					
					if($sum_zapati_ex_array['abymin']!=0)
						$fac1=$sum_zapati_ex_array['kdmin']/$sum_zapati_ex_array['abymin'];
					else
						$fac1=0;
						
					if($sum_zapati_ex_array['verb']!=0)
						$fac2=$sum_zapati_ex_array['kdmin']/$sum_zapati_ex_array['verb'];
					else
						$fac2=0;
					
					if($sum_zapati_ex_array['aufgew']!=0)
						$ept=$sum_zapati_ex_array['kdmin']*$preismin/$sum_zapati_ex_array['aufgew'];
					else
						$ept=0;

					zapati_ex($pdf,$export,5,"Sum EX",array(255,255,230),$sum_zapati_ex_array,$fac1,$fac2,$ept);
					
					// projedu pole a aktualizuju sumy pro zapati mesic
					foreach($sum_zapati_mesic_array as $key=>$prvek)
					{
						$hodnota=$sum_zapati_ex_array[$key];
						$sum_zapati_mesic_array[$key]+=$hodnota;
					}
					
					
				}
			}
			
			// zapati pro mesic
			test_pageoverflow($pdf,5,$cells_header);
			
			if($sum_zapati_mesic_array['abymin']!=0)
				$fac1m=$sum_zapati_mesic_array['kdmin']/$sum_zapati_mesic_array['abymin'];
			else
				$fac1m=0;
						
			if($sum_zapati_mesic_array['verb']!=0)
				$fac2m=$sum_zapati_mesic_array['kdmin']/$sum_zapati_mesic_array['verb'];
			else
				$fac2m=0;

			if($sum_zapati_mesic_array['aufgew']!=0)
				$ept=$sum_zapati_mesic_array['kdmin']*$preismin/$sum_zapati_mesic_array['aufgew'];
			else
				$ept=0;

			//zapati_mesic($pdf,$mesic,5,array(200,200,255));
			zapati_ex($pdf,$export,5,"Sum Monat",array(230,230,255),$sum_zapati_mesic_array,$fac1m,$fac2m,$ept);

			// projedu pole a aktualizuju sumy pro zapati kunde
			foreach($sum_zapati_kunde_array as $key=>$prvek)
			{
				$hodnota=$sum_zapati_mesic_array[$key];
				$sum_zapati_kunde_array[$key]+=$hodnota;
			}

		}

			// pro zobrazeni zapati vyuiju jemn jednu funkci
			// zapati pro kunde
			test_pageoverflow($pdf,5,$cells_header);
			
			if($sum_zapati_kunde_array['abymin']!=0)
				$fac1k=$sum_zapati_kunde_array['kdmin']/$sum_zapati_kunde_array['abymin'];
			else
				$fac1k=0;
						
			if($sum_zapati_kunde_array['verb']!=0)
				$fac2k=$sum_zapati_kunde_array['kdmin']/$sum_zapati_kunde_array['verb'];
			else
				$fac2k=0;
				
			if($sum_zapati_kunde_array['aufgew']!=0)
				$ept=$sum_zapati_kunde_array['kdmin']*$preismin/$sum_zapati_kunde_array['aufgew'];
			else
				$ept=0;

			zapati_ex($pdf,$export,5,"Sum KD",array(255,230,230),$sum_zapati_kunde_array,$fac1k,$fac2k,$ept);
			
			// projedu pole a aktualizuju sumy pro zapati sestavy
			foreach($sum_zapati_sestava_array as $key=>$prvek)
			{
				$hodnota=$sum_zapati_kunde_array[$key];
				$sum_zapati_sestava_array[$key]+=$hodnota;
			}
	}
}

// zapati pro sestava
test_pageoverflow($pdf,5,$cells_header);
			
if($sum_zapati_sestava_array['abymin']!=0)
	$fac1s=$sum_zapati_sestava_array['kdmin']/$sum_zapati_sestava_array['abymin'];
else
	$fac1s=0;
						
if($sum_zapati_sestava_array['verb']!=0)
	$fac2s=$sum_zapati_sestava_array['kdmin']/$sum_zapati_sestava_array['verb'];
else
	$fac2s=0;
	
if($sum_zapati_sestava_array['aufgew']!=0)
	$ept=$sum_zapati_sestava_array['kdmin']*$preismin/$sum_zapati_sestava_array['aufgew'];
else
	$ept=0;

zapati_ex($pdf,$export,5,"Sum Ges",array(230,230,230),$sum_zapati_sestava_array,$fac1s,$fac2s,$ept);



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
