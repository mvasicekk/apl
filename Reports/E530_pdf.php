<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "E310";
$doc_subject = "E310 Export";
$doc_keywords = "E310";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];


//$fullAccess = testReportPassword("S169",$password,$user,0);
//
//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}


$apl = AplDB::getInstance();

$datumvon = $apl->make_DB_datum($apl->validateDatum($_GET['datevon']));
$datumbis = $apl->make_DB_datum($apl->validateDatum($_GET['datebis']));

require_once('E530_xml.php');

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
            'artnr' => array("popis" => "artnr", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
            'artname' => array("popis" => "artikelname", "sirka" => 33, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'preis' => array("nf"=>array(2,'.',''),"popis" => "preis", "sirka" => 17, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'anzahl' => array("popis" => "anzahl", "sirka" => 17, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
);


$cells_header =
        array(
            'artnr' => array("popis" => "artnr", "sirka" => 25, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
            'artname' => array("popis" => "artikelname", "sirka" => 33, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'preis' => array("popis" => "preis", "sirka" => 17, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
            'anzahl' => array("popis" => "anzahl", "sirka" => 17, "ram" => 'B', "align" => "L", "radek" => 0, "fill" => 0),
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
							 ->setTitle("E530")
							 ->setSubject("E530")
							 ->setDescription("E530")
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
$teile=$domxml->getElementsByTagName("artikel");
foreach($teile as $teil)
{
    $teilChilds = $teil->childNodes;
    telo($objPHPExcel,$radek,$sloupec,$cells,$teilChilds);
    $radek++;
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth($cells_header['artnr']['sirka']);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth($cells_header['artname']['sirka']);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth($cells_header['preis']['sirka']);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth($cells_header['anzahl']['sirka']);

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E530');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="E530.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>
