<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D230";
$doc_subject = "D230 Report";
$doc_keywords = "D230";

// necham si vygenerovat XML

$parameters=$_GET;

$palvon=$_GET['palvon'];
$palbis=$_GET['palbis'];
$auftragsnr=$_GET['auftragsnr'];

require_once('D230_xml.php');


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

"Teil" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"import" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"text" 
=> array ("popis"=>"","sirka"=>33,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"expstk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss2_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss4_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss6_stk_exp" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vypln" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0)

);


$cells_header = 
array(

"Teil" 
=> array ("popis"=>"Teil","sirka"=>25,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"Import" 
=> array ("popis"=>"Import","sirka"=>25,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"Leistung" 
=> array ("popis"=>"Leistung","sirka"=>33,"ram"=>'TB',"align"=>"L","radek"=>0,"fill"=>1),

"gutstk" 
=> array ("popis"=>"gute Stk","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"auss2" 
=> array ("popis"=>"auss2","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"auss4" 
=> array ("popis"=>"auss4","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),
	
"auss6" 
=> array ("popis"=>"auss6","sirka"=>10,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),
	
"bestellt" 
=> array ("popis"=>"Bestellt","sirka"=>15,"ram"=>'TB',"align"=>"R","radek"=>0,"fill"=>1),

"gew" 
=> array ("popis"=>"Gew","sirka"=>0,"ram"=>'TB',"align"=>"R","radek"=>1,"fill"=>1)

);


$sum_zapati_import_array = array(	
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"expstk"=>0,
								"gew_auss"=>0,
								);
global $sum_zapati_import_array;

$sum_zapati_sestava_array = array(	
								"auss2_stk_exp"=>0,
								"auss4_stk_exp"=>0,
								"auss6_stk_exp"=>0,
								"geliefert"=>0,
								"gew_auss"=>0,
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
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


// funkce pro vykresleni tela
function detaily($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist)
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
// zobraz_cinnosti($pdf,$taetigkeiten);
//
function zobraz_cinnosti($pdfobjekt,$taetigkeiten,$teil)
{
	// vytahnu si jeste vsechny zaskrtnute komentace pro dany dil, vrati mi asociativni pole
        $k2 = getTatAktivFromTeilDpos($teil,2,1);
        $k3 = getTatAktivFromTeilDpos($teil,3,1);
        $komentare = array();
        if(is_array($k2))
            $komentare = array_merge($komentare,$k2);
        if(is_array($k3))
            $komentare = array_merge($komentare,$k3);
	//$komentare = getTatAktivFromTeilDpos($teil,3,1);
	
		
	$x_pocatek=130;
	$y_pocatek=25;
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek);
	$pdfobjekt->Cell(10,3,"taetnr",'B',0,'L');
	$pdfobjekt->Cell(50,3,"Bezeichnung",'B',0,'L');
	$pdfobjekt->Cell(50,3,"oznaceni",'B',0,'L');
	$pdfobjekt->Cell(15,3,"ks/hod",'B',0,'R');
	$pdfobjekt->Cell(15,3,"min/stk",'B',1,'R');
	$pdfobjekt->SetX($x_pocatek);

	$pdfobjekt->SetFont("FreeSans", "", 7);
	
	// vypisu komentare
	if(is_array($komentare))
	{
		foreach($komentare as $komentar)
		{
			$pdfobjekt->Cell(10,3,$komentar["taetnr"],0,0,'L');
			$pdfobjekt->Cell(50,3,$komentar["tatbez_d"],0,0,'L');
			$pdfobjekt->Cell(50,3,$komentar["tatbez_t"],0,0,'L');

			$obsah=number_format($komentar["ks_hod"],0,',',' ');
			$pdfobjekt->Cell(15,3,$obsah,0,0,'R');
			$obsah=number_format($komentar["vzaby"],2,',',' ');
			$pdfobjekt->Cell(15,3,$obsah,0,1,'R');
			$pdfobjekt->SetX($x_pocatek);
		}
	}
	
	foreach($taetigkeiten as $taetigkeit)
	{
		$taetigkeitChildNodes = $taetigkeit->childNodes;
		$pdfobjekt->Cell(10,3,getValueForNode($taetigkeitChildNodes,"taetnr"),0,0,'L');
		$pdfobjekt->Cell(50,3,getValueForNode($taetigkeitChildNodes,"tatbez_d"),0,0,'L');
		$pdfobjekt->Cell(50,3,getValueForNode($taetigkeitChildNodes,"tatbez_t"),0,0,'L');

		$obsah=number_format(getValueForNode($taetigkeitChildNodes,"ks_hod"),0,',',' ');
		$pdfobjekt->Cell(15,3,$obsah,0,0,'R');
		$obsah=number_format(getValueForNode($taetigkeitChildNodes,"vzaby"),2,',',' ');
		$pdfobjekt->Cell(15,3,$obsah,0,1,'R');
		$pdfobjekt->SetX($x_pocatek);
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// zobraz_paletu($pdf,$paletteChildNodes,$importChildNodes);
function zobraz_paletu($pdfobjekt,$paletteChildNodes,$importChildNodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=0;
	$pdfobjekt->SetFont("FreeSans", "", 9);

	// hlavni tabulka ma 3 radky

	$x_pocatek=$pdfobjekt->GetX();
	$y_pocatek=$pdfobjekt->GetY();

	// pole pro zakaznika
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->Rect($x_pocatek,$y_pocatek-10,52,30);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10);
	$pdfobjekt->Write(5,"Kunde / zakaznik :");$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->Write(8,getValueForNode($importChildNodes,"kunde"));$pdfobjekt->Ln();
	$pdfobjekt->Write(8,substr(getValueForNode($importChildNodes,"name1"),0,25));$pdfobjekt->Ln();
	$y1 = $pdfobjekt->GetY();
	$pdfobjekt->Write(8,substr(getValueForNode($importChildNodes,"name2"),0,25));$pdfobjekt->Ln();

	// pole pro auftrag
	$pdfobjekt->Rect($x_pocatek+52+3,$y_pocatek-10,52,30);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->SetXY($x_pocatek+52+3,$y_pocatek-10);
	$pdfobjekt->Write(5,"Auftrag / dodavka / pal:");$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetX($x_pocatek+52+5);
	$pdfobjekt->SetFont("FreeSans", "B", 25);
	$pdfobjekt->Write(25,getValueForNode($importChildNodes,"auftragsnr")."/");
	$x1 = $pdfobjekt->GetX();
	$pdfobjekt->SetFont("FreeSans", "B", 18);
	$pdfobjekt->Write(25,getValueForNode($paletteChildNodes,"pal"));
	$pdfobjekt->SetFont("FreeSans", "B", 10);
	$pdfobjekt->SetXY($x1,$y1);
	$pdfobjekt->Write(8,getValueForNode($paletteChildNodes,"fremdpos"));$pdfobjekt->Ln();

	// pole pro cislo dilu
	$pdfobjekt->Rect($x_pocatek,$y_pocatek-10+30+3,52,24);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10+30+3);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->Write(5,"Teil / cislo dilu:");$pdfobjekt->Ln();
	$pdfobjekt->SetX($x_pocatek);
	$pdfobjekt->SetFont("FreeSans", "B", 20);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"teil"));$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"teilbez"));$pdfobjekt->Ln();

	// pole pro pocet kusu
	$pdfobjekt->Rect($x_pocatek+52+3,$y_pocatek-10+30+3,52,24);
	$pdfobjekt->SetFont("FreeSans", "BU", 10);
	$pdfobjekt->SetXY($x_pocatek+52+3,$y_pocatek-10+30+3);
	$pdfobjekt->Write(5,"Stuck / pocet kusu:");$pdfobjekt->Ln();
	$pdfobjekt->SetX($x_pocatek+52+5+15);
	$pdfobjekt->SetFont("FreeSans", "B", 18);
	$pdfobjekt->Write(10,getValueForNode($paletteChildNodes,"stk"));$pdfobjekt->Ln();
	$pdfobjekt->SetX($x_pocatek+52+5);
	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Write(5,"Druh litiny:".getValueForNode($paletteChildNodes,"artguseisen"));
	$pdfobjekt->SetX($x_pocatek+52+5+30);
	$pdfobjekt->SetFont("FreeSans", "B", 12);
	$pdfobjekt->Write(5,getValueForNode($paletteChildNodes,"gew")." kg");

	// pole pro muster
    	$a = AplDB::getInstance();
	$teilnr = getValueForNode($paletteChildNodes, 'teil');
	// dokunrchange
	//$musterRow = $a->getTeilDokument($teilnr, 29, TRUE);
	$musterRow = $a->getTeilDokument($teilnr, AplDB::DOKUNR_MUSTER, TRUE);
	if($musterRow===NULL)
	    $musterText = "Muster: ????";
	else
	    $musterText = $musterRow['doku_nr']."/".$musterRow['doku_beschreibung']."/".$musterRow['einlag_datum']."/".$musterRow['musterplatz']."/".$musterRow['freigabe_am']."/".$musterRow['freigabe_vom'];

	$pdfobjekt->SetFont("FreeSans", "", 7);
	$pdfobjekt->Rect($x_pocatek+52+3+52+3,$y_pocatek-10,150,5);
	$pdfobjekt->SetXY($x_pocatek+52+3+52+3,$y_pocatek-10);
	$pdfobjekt->Write(5,$musterText);
//	$pdfobjekt->Write(5,"Muster: ".getValueForNode($paletteChildNodes,"musterplatz"));
//	$pdfobjekt->Write(5,"      Eingelagert am: ".getValueForNode($paletteChildNodes,"mustervom"));
	$pdfobjekt->SetFont("FreeSans", "B", 9);
	$pdfobjekt->Write(5,"      Teil (original): ".getValueForNode($paletteChildNodes,"teillang"));
	
	$pdfobjekt->SetFont("FreeSans", "", 9);
	$pdfobjekt->SetXY($x_pocatek,$y_pocatek-10+30+3+24+3);	

	// tabulka pro zapis vykonu
	// hlavicka
	$pdfobjekt->MyMultiCell(25,8,"Datum",1,'L',0);
	$pdfobjekt->MyMultiCell(15,4,"Schicht\nsmena",1,'L',0);
	$pdfobjekt->MyMultiCell(20,4,"PersNr\nosobni cislo",1,'L',0);
	$pdfobjekt->MyMultiCell(90,4,"AFO - Nr.\ncislo operace",1,'L',0);
	$pdfobjekt->MyMultiCell(30,4,"Stuck / kus\nvporadku zmetky",1,'L',0);
	$pdfobjekt->MyMultiCell(40,4,"Arbeitszeit\nvon/od    bis/do",1,'C',0);
	$pdfobjekt->SetFont("FreeSans", "B", 15);
	$pdfobjekt->MyMultiCell(20,8,"Q",1,'C',0);
	$pdfobjekt->MultiCell(20,8,"SYS",1,'C',0);
	// radky tabulky
	$sirky = array(25,15,20,10,10,10,10,10,10,10,10,10,15,15,20,20,20,20);
	for($i=0;$i<9;$i++)
	{
		foreach($sirky as $sirka)
		{
			$pdfobjekt->MyMultiCell($sirka,10,"",1,'C',0);
		}
		$pdfobjekt->Ln();
	}

	// spodni ramecek s poznamkou
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	// posunu se o mm dolu
	$pdfobjekt->SetY($pdfobjekt->GetY()+2);
	$pdfobjekt->MyMultiCell(40,4,"Druhy zmetku\nAusschussart\n",1,'C',0);
	$pdfobjekt->MyMultiCell(110,4,"10(2) od zak. pred opracovanim / Kd. vor Bearbeitung\n20(4) od zak. po opracovani / Kd. nach Bearbeitung\n50(6) Aby / Aby",1,'L',0);
	// schovam si poziceXY
	$x=$pdfobjekt->GetX();
	$y=$pdfobjekt->GetY();
	$pdfobjekt->MyMultiCell(110,4,"Bemerkung\nPoznamka\n",1,'L',0);
	
	$codeText = getValueForNode($importChildNodes,"auftragsnr")."-".getValueForNode($paletteChildNodes,"teil")."-".getValueForNode($paletteChildNodes,"pal")."-".getValueForNode($paletteChildNodes,"stk");
	//$pdfobjekt->writeBarcode($x+20, $y+2, 80, 10-4, "C39", "", "", "", $codeText);
	//$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
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

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D230 Arbeitspapier - PRACOVNI PRUVODNI KARTA", $params);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D230 Arbeitspapier - PRACOVNI PRUVODNI KARTA", "");
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

		// ted jdu po cinnostech = tabulka s operacema
		$teil = getValueForNode($paletteChildNodes,"teil");
		$taetigkeiten = $palette->getElementsByTagName("taetigkeit");
		zobraz_cinnosti($pdf,$taetigkeiten,$teil);
	}
}


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
