<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "E320";
$doc_subject = "E320 Export";
$doc_keywords = "E320";

// necham si vygenerovat XML

$parameters=$_GET;
$a = AplDB::getInstance();

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];

$von = $a->make_DB_datetime("00:00",$_GET['datevon']);
$bis = $a->make_DB_datetime("23:59",$_GET['datebis']);
$kdvon = $_GET['kundevon'];
$kdbis = $_GET['kundebis'];

//$fullAccess = testReportPassword("S169",$password,$user,0);
//
//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}


require_once('E320_xml.php');

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
            'kundenr' => array("popis" => "", "sirka" => 6, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'importnr' => array("popis" => "", "sirka" => 8, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'palnr' => array("popis" => "", "sirka" => 6, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'teil' => array("popis" => "", "sirka" => 12, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'verpackungmenge' => array("popis" => "", "sirka" => 6, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'stk_import' => array("popis" => "", "sirka" => 7, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'stk_export' => array("popis" => "", "sirka" => 7, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'abgnr' => array("popis" => "", "sirka" => 6, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'inserted' => array("popis" => "", "sirka" => 12, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'ausgeliefert' => array("popis" => "", "sirka" => 12, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'export' => array("popis" => "", "sirka" => 8, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'nach_lager' => array("popis" => "", "sirka" => 12, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'aus_lager' => array("popis" => "", "sirka" => 12, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
);


$cells_header =
        array(
            'kundenr' => array("popis" => "Kunde", "sirka" => $cells['kundenr']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'importnr' => array("popis" => "IM", "sirka" => $cells['importnr']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'palnr' => array("popis" => "Pal", "sirka" => $cells['palnr']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'teil' => array("popis" => "Teil", "sirka" => $cells['teil']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'verpackungmenge' => array("popis" => "VPA", "sirka" => $cells['verpackungmenge']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'stk_import' => array("popis" => "StkIM", "sirka" => $cells['stk_import']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'stk_export' => array("popis" => "StkEX", "sirka" => $cells['stk_import']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'abgnr' => array("popis" => "abgnr", "sirka" => $cells['abgnrnr']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'inserted' => array("popis" => "inserted", "sirka" => $cells['inserted']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
	    'ausgeliefert' => array("popis" => "ausgeliefert", "sirka" => $cells['ausgeliefert']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'export' => array("popis" => "EX", "sirka" => $cells['export']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'nach_lager' => array("popis" => "nach Lager", "sirka" => $cells['nach_lager']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
    	    'aus_lager' => array("popis" => "aus Lager", "sirka" => $cells['aus_lager']['sirka'], "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
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
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


// funkce pro vykresleni tela
function telo($phpE,$pocRadek,$pocSloupec,$pole,$nodelist)
{
        $radek = $pocRadek;
        $sloupec = $pocSloupec;
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

                $phpE->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
                $sloupec++;
		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
		//$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
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
				

date_default_timezone_set('Europe/Prague');

/** PHPExcel */
require_once '../Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$user = get_user_pc();
// Set properties
$objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy($user)
							 ->setTitle("E320")
							 ->setSubject("E320")
							 ->setDescription("E320")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

// popisky sloupcu
$radek = 2;
$sloupec = 0;

foreach($cells_header as $ch){
    $popis = $ch['popis'];
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}
$radek++;
$sloupec = 0;

// a ted data do sloupcu
$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde){
    $importe = $kunde->getElementsByTagName("import");
    foreach ($importe as $import) {
	$palety = $import->getElementsByTagName("pal");
	foreach ($palety as $pal) {
	    $palChilds = $pal->childNodes;
	    telo($objPHPExcel, $radek, $sloupec, $cells, $palChilds);
	    $radek++;
	}
    }
}

$sloupecAktual = 'A';
foreach ($cells_header as $ch) {
    $objPHPExcel->getActiveSheet()->getColumnDimension($sloupecAktual)->setWidth($ch['sirka']);
    $sloupecAktual = chr(ord($sloupecAktual)+1);
}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E320');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="E320.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>
