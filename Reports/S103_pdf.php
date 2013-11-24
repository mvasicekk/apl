<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S103";
$doc_subject = "S103 Report";
$doc_keywords = "S103";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];
$password = $_GET['password'];
$report = "S103";

// read reportprintparams
$apl = AplDB::getInstance();

$bWhereKategorien=FALSE;
$printKatArray = $apl->getReportPrintParam($report, $user, "printkat_",TRUE);
if($printKatArray!==NULL){
    $whereKategorien="";
    foreach ($printKatArray as $printKat){
	//zajimaji me jen parametry s hodnotou 1
	if($printKat['value']==1){
	    $param = $printKat['param'];
	    $katId = substr($param, strpos($param, '_')+1);
	    $whereKategorien.=" (adresyinkategorie.adresy_kategorie_id=$katId) or";
	}
    }
    if(strlen($whereKategorien)>0){
	$bWhereKategorien=TRUE;
	$whereKategorien = substr($whereKategorien, 0, strlen($whereKategorien)-2);
    }
}

// get columns to print
$columnsToPrintArray = $apl->getReportPrintParam($report, $user, "column_",TRUE);
$columnPrint = array('ln');
if($columnsToPrintArray!==NULL){
    foreach ($columnsToPrintArray as $column){
	//zajimaji me jen parametry s hodnotou 1
	if($column['value']==1){
	    $colName = $column['param'];
	    $colName = substr($colName, strpos($colName, '_')+1);
	    array_push($columnPrint, $colName);
	}
    }
}

// 
//echo $whereKategorien;
//exit;
//var_dump($columnPrint);
//exit;

//$fullAccess = testReportPassword("S102",$password,$user,0);

//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}


//$persvon = intval($_GET['persvon']);
//$persbis = intval($_GET['persbis']);
//$sort = $_GET['sort'];
//$austritt = $_GET['austritt'];

require_once('S103_xml.php');



// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
//$parameters=$domxml->getElementsByTagName("parameters");
//
//
//foreach ($parameters as $param)
//{
//	$parametry=$param->childNodes;
//	// v ramci parametru si prectu label a hodnotu
//	foreach($parametry as $parametr)
//	{
//		$parametr=$parametr->childNodes;
//		foreach($parametr as $par)
//		{
//			if($par->nodeName=="label")
//				$label=$par->nodeValue;
//			if($par->nodeName=="value")
//				$value=$par->nodeValue;
//		}
//		 if(strtolower($label)!="password")
//                    $params .= $label.": ".$value."  ";
//	}
//}


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells = 
array(
//	'suchbegriff'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//	'kdnr'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//	'code'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
	'firma'=> array ("sub"=>array(0,35),"popis"=>"Firma","sirka"=>35,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'ansprechpartner'=> array ("sub"=>array(0,25),"popis"=>"AnsprechPartner","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'fullname'=> array ("sub"=>array(0,21),"popis"=>"Kontakt","sirka"=>25,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'funktion'=> array ("sub"=>array(0,15),"popis"=>"Funkt.","sirka"=>15,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'geboren'=> array ("sub"=>array(0,8),"popis"=>"geboren","sirka"=>10,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'telefon'=> array ("sub"=>array(0,16),"popis"=>"tel","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'telefonprivat'=> array ("sub"=>array(0,16),"popis"=>"telpriv","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'fax'=> array ("sub"=>array(0,16),"popis"=>"fax","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'handy'=> array ("sub"=>array(0,16),"popis"=>"Mob","sirka"=>20,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'adr'=> array ("sub"=>array(0,35),"popis"=>"adr","sirka"=>36,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
//	'strasse'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
//	'ort'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
//	'plz'=> array ("popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
	'email'=> array ("sub"=>array(0,30),"popis"=>"email","sirka"=>31,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'sonstiges'=> array ("sub"=>array(0,30),"popis"=>"sonst","sirka"=>0,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
	'ln'=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"L","radek"=>1,"fill"=>0),	
);

$cells_header = 
array(
'persnr'=> array ("popis"=>"\nPersnr","sirka"=>$cells['persnr']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'name'=> array ("popis"=>"\nName","sirka"=>$cells['name']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'eintritt'=> array ("popis"=>"\nEintritt","sirka"=>$cells['eintritt']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'geboren'=> array ("popis"=>"Geburts-\ndatum","sirka"=>$cells['geboren']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'handy'=> array ("popis"=>"Telefon\n( Handy )","sirka"=>$cells['handy']['sirka'],"ram"=>'B',"align"=>"L","radek"=>1,"fill"=>1),
);

// podle poctu sloupcu spocitam kolik mista muzu k jednotlivym sloupcum pridat na ukor vynechanych sloupcu
// celkova sirka vsech sloupcu
$celkSirka = 0;
$celkZnaku = 0;
foreach ($cells as $cell){
    $celkSirka += $cell['sirka'];
    $celkZnaku += $cell['sub'][1];
}
// sirka tisknutych sloupcu
$tiskSirka = 0;
$tiskZnaku = 0;
foreach ($columnPrint as $column){
    $tiskSirka += $cells[$column]['sirka'];
    $tiskZnaku += $cells[$column]['sub'][1];
}
// kolik muzu rozdelit mezi ostatni
$rozdelit = $celkSirka-$tiskSirka;
$rozdelitZnaku = $celkZnaku - $tiskZnaku;

//var_dump($celkSirka);
//exit;

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
    global $columnPrint;
    global $tiskSirka;
    global $rozdelit;
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $name=>$cell)
	{
		if(in_array($name, $columnPrint)){
		    $width = $cell['sirka']+$cell['sirka']/$tiskSirka*$rozdelit;
		    $pdfobjekt->MyMultiCell($width,$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],1);
		}
	}
	$pdfobjekt->Ln();
//        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
    global $columnPrint;
    global $tiskSirka;
    global $rozdelit;
    global $tiskZnaku;
    global $rozdelitZnaku;
    
	$pdfobjekt->SetFont("FreeSans", "", 6);
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
		
		if(array_key_exists("sub", $cell)){
		    $maxlen = $cell['sub'][1];
		    if($cell['sirka']>0) $maxlen = $maxlen + $maxlen/$tiskZnaku*$rozdelitZnaku;
		    $suffix = strlen($cellobsah)>$maxlen?'...':'';
		    $cellobsah = substr($cellobsah, $cell['sub'][0], $maxlen).$suffix;
		}
		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
		if(in_array($nodename, $columnPrint)){
		    $width = $cell['sirka']+$cell['sirka']/$tiskSirka*$rozdelit;
		    $pdfobjekt->Cell($width,$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
		}
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
function zahlavi_schicht($pdfobjekt,$vyskaradku,$rgb,$schicht,$schichtfuehrer,$cells)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->Cell(0,$vyskaradku,$schicht." ".$schichtfuehrer,'0',1,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_schicht($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1,$fac2,$fac3)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(70,$vyskaradku,$popis,'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['anwesenheit'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$fac1;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac2;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac3;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
	
	//$pdfobjekt->Ln();
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
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S103 Telefonbuch", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-10, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT-10);
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
$pdf->SetFont("FreeSans", "", 6);


// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells,5);


$adresy=$domxml->getElementsByTagName("adresa");
foreach($adresy as $adresa)
{
    $adresaChilds = $adresa->childNodes;
    test_pageoverflow($pdf,5,$cells);
    telo($pdf,$cells,5,array(255,255,255),"",$adresaChilds);
}



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
