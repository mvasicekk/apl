<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "E140";
$doc_subject = "E140 MA Liste";
$doc_keywords = "E140";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];
$alle = $_GET['alle'];

$alle=='a'?$bAlle=TRUE:$bAlle=FALSE;

$sort = $_GET['sort'];

//$fullAccess = testReportPassword("S169",$password,$user,0);
//
//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}

$apl = AplDB::getInstance();


require_once('E140_xml.php');

$cells_header = array(
    'persnr'=>array('popis'=>'PersNr'),
    'name'=>array('popis'=>'Name'),
    'vorname'=>array('popis'=>'Vorname'),
    'eintritt'=>array('popis'=>'Eintritt'),
    'austritt'=>array('popis'=>'Austritt'),
    'geboren'=>array('popis'=>'geboren'),
    'handy'=>array('popis'=>'Handy'),
    'ort'=>array('popis'=>'Ort'),
    'strasse'=>array('popis'=>'strasse'),
    'status'=>array('popis'=>'DpersStatus'),
    'regeloe'=>array('popis'=>'RegelOE'),
);

$cells = array(
    'persnr'=>array('popis'=>'PersNr'),
    'name'=>array('popis'=>'Name'),
    'vorname'=>array('popis'=>'Vorname'),
    'eintritt'=>array('popis'=>'Eintritt'),
    'austritt'=>array('popis'=>'Austritt'),
    'geboren'=>array('popis'=>'geboren'),
    'handy'=>array('popis'=>'Handy'),
    'ort'=>array('popis'=>'Ort'),
    'strasse'=>array('popis'=>'strasse'),
    'status'=>array('popis'=>'DpersStatus'),
    'regeloe'=>array('popis'=>'RegelOE'),
//    'spol',
);
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
							 ->setTitle("E140")
							 ->setSubject("E140")
							 ->setDescription("E140")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

// popisky sloupcu
$radek = 2;
$sloupec = 1;

foreach($cells_header as $ch){
    $popis = $ch['popis'];
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}
$radek++;
$sloupec = 1;

// a ted data do sloupcu
$exporte=$domxml->getElementsByTagName("person");
foreach($exporte as $export)
{
    $exportChilds = $export->childNodes;
    telo($objPHPExcel,$radek,$sloupec,$cells,$exportChilds);
    $radek++;
}

// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E140');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="E140.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


//============================================================+
// END OF FILE                                                 
//============================================================+

?>
