<?php
session_start();
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "S355";
$doc_subject = "S355 Report";
$doc_keywords = "S355";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$behnr = $_GET['behnr'];
$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];
$bewvon = $_GET['bewvon'];
$bewbis = $_GET['bewbis'];

$apl = AplDB::getInstance();

$bewvonDB = $apl->make_DB_datum($bewvon);
$bewbisDB = $apl->make_DB_datum($bewbis);

require_once('S355_xml.php');

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
'behaelternr'=> array ("popis"=>"","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'datum'=> array ("popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'import'=> array ("popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'export'=> array ("popis"=>"","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
'stk'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'zustand_id'=> array ("popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
'user'=> array ("popis"=>"","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0),
);


$cells_header = 
array(
'behaelternr'=> array ("popis"=>"BehaelterNr","sirka"=>30,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'datum'=> array ("popis"=>"Datum","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'import'=> array ("popis"=>"Import","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'export'=> array ("popis"=>"Export","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
'stk'=> array ("popis"=>"Stk","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'zustand_id'=> array ("popis"=>"Zustand","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
'user'=> array ("popis"=>"Benutzer","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1),
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
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
//	$pdfobjekt->Ln();
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
			number_format(floatval(getValueForNode($nodelist,$nodename)), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}
		
		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
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


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
	}
}
				
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$invDatum,$kdKontoStand,$bewPlus,$bewMinus)
{
        global $cells;
        global $bewbis;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->Ln();
	// dummy
        $obsah = "Inventurdatum:";
        $pdfobjekt->Cell($cells['behaelternr']['sirka'],$vyskaradku,$obsah,'LBT',0,'L',$fill);

        $invdatumF = date('d.m.Y',strtotime($invDatum));
        $obsah = date('d.m.Y',strtotime($invDatum));
//        $obsah = substr($invDatum, 0, 10);
        $pdfobjekt->Cell($cells['datum']['sirka'],$vyskaradku,$obsah,'BTR',0,'L',$fill);

        //
        $obsah = "KD-Konto Inventurbestand : ";
        $pdfobjekt->Cell($cells['import']['sirka']+$cells['export']['sirka']+$cells['stk']['sirka'],$vyskaradku,$obsah,'LBT',0,'L',$fill);

        $obsah = number_format($kdKontoStand,0,',',' ');
        $pdfobjekt->Cell($cells['zustand_id']['sirka'],$vyskaradku,$obsah,'BTRL',1,'R',$fill);

        $pdfobjekt->Cell($cells['behaelternr']['sirka']+$cells['datum']['sirka'],$vyskaradku,'','0',0,'L',0);
        $obsah = "Bewegung Plus ( $invdatumF - $bewbis ): ";
        $pdfobjekt->Cell($cells['import']['sirka']+$cells['export']['sirka']+$cells['stk']['sirka'],$vyskaradku,$obsah,'LBT',0,'L',$fill);
        $obsah = number_format($bewPlus,0,',',' ');
        $pdfobjekt->Cell($cells['zustand_id']['sirka'],$vyskaradku,$obsah,'BTRL',1,'R',$fill);

        $pdfobjekt->Cell($cells['behaelternr']['sirka']+$cells['datum']['sirka'],$vyskaradku,'','0',0,'L',0);
        $obsah = "Bewegung Minus ( $invdatumF - $bewbis ): ";
        $pdfobjekt->Cell($cells['import']['sirka']+$cells['export']['sirka']+$cells['stk']['sirka'],$vyskaradku,$obsah,'LBT',0,'L',$fill);
        $obsah = number_format($bewMinus,0,',',' ');
        $pdfobjekt->Cell($cells['zustand_id']['sirka'],$vyskaradku,$obsah,'BTRL',1,'R',$fill);

        $pdfobjekt->SetFont("FreeSans", "B", 9);
        $pdfobjekt->Cell($cells['behaelternr']['sirka']+$cells['datum']['sirka'],$vyskaradku,'','0',0,'L',0);
        $obsah = "Bestand ( bis $bewbis ) : ";
        $pdfobjekt->Cell($cells['import']['sirka']+$cells['export']['sirka']+$cells['stk']['sirka'],$vyskaradku,$obsah,'LBT',0,'L',$fill);
        $obsah = number_format($kdKontoStand+$bewPlus-$bewMinus,0,',',' ');
        $pdfobjekt->Cell($cells['zustand_id']['sirka'],$vyskaradku,$obsah,'BTRL',0,'R',$fill);
//	$obsah=sprintf("Inventurdatum: %s, KD-Kont Bestand : %06d, Bewegung + : %06d, Bewegung - : %06d, Bestand : %06d",$invDatum,$kdKontoStand,$bewPlus,$bewMinus,$kdKontoStand+$bewPlus-$bewMinus);
//	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'1',0,'L',$fill);
        $pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S355 Behaelter Bewegung - Detail", $params);
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

$radky = $domxml->getElementsByTagName("row");
foreach($radky as $radek){
    $radekChilds = $radek->childNodes;
    test_pageoverflow($pdf,5,$cells_header);
    telo($pdf,$cells,5,array(255,255,255),"",$radekChilds);
}

$lastKDKontoInvDatumDB = $apl->getLastKDKontoInvDatumDB($behnr,$kundevon,$bewvonDB,$bewbisDB);
//echo "lastinvdatum: ".$lastKDKontoInvDatumDB;
$bewPlus = $apl->getBehaelterBewegungungPlus($behnr, $kundevon, $lastKDKontoInvDatumDB,$bewbisDB);
$bewMinus = $apl->getBehaelterBewegungungMinus($behnr, $kundevon, $lastKDKontoInvDatumDB,$bewbisDB);
$kdKontoBestand = $apl->getBehelterKDKontoStand($behnr, $kundevon, $lastKDKontoInvDatumDB);

if(($kundevon!='*') && (strlen($kundevon)>0))
zapati_sestava($pdf, 5, array(255,255,240), $lastKDKontoInvDatumDB, $kdKontoBestand, $bewPlus, $bewMinus);
//echo "<br>KDKonto inv: $kdKontoBestand, plus: $bewPlus, minus: $bewMinus";

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
