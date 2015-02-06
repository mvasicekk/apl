<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "E195";
$doc_subject = "E195 Export";
$doc_keywords = "E195";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$apl = AplDB::getInstance();

$datumvon = $apl->make_DB_datum($apl->validateDatum($_GET['datvon']));
$datumbis = $apl->make_DB_datum($apl->validateDatum($_GET['datbis']));

require_once('E195_xml.php');

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
'stamp' => array("popis" => "stamp                 ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'anftyp' => array("popis" => "Anf.Typ      ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'artikel' => array("popis" => "Artikel              ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'anzahl' => array("popis" => "Anzahl ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'user' => array("popis" => "zadal            ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'bemerkung' => array("popis" => "Bemerkung                              ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'abdatum' => array("popis" => "ab Datum  ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'status' => array("popis" => "Status                      ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'status_flag' => array("popis" => "status flag", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'lieferdatum' => array("popis" => "Lieferdatum   ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'erledigt' => array("popis" => "erledigt         ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'edit_stamp' => array("popis" => "edit stamp        ", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
'last_editor' => array("popis" => "letzt edit", "sirka" => 10, "ram" => 'B', "align" => "R", "radek" => 0, "fill" => 0),
);

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

		$cellobsah = strip_tags($cellobsah);
                $phpE->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
                $sloupec++;
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
							 ->setTitle("E195")
							 ->setSubject("E195")
							 ->setDescription("E195")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

// popisky sloupcu
$radek = 1;
$sloupec = 0;

foreach($cells as $cellname=>$ch){
    $popis = $ch['popis'];
//    $popis = $cellname;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}
$radek++;
$sloupec = 0;

//zalamovat text v urcitych sloupcich
$objPHPExcel->getActiveSheet()->getStyle("B1:B1000")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
$objPHPExcel->getActiveSheet()->getStyle("B1:B1000")->getAlignment()->setWrapText(TRUE);

$objPHPExcel->getActiveSheet()->getStyle("E1:E1000")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
$objPHPExcel->getActiveSheet()->getStyle("E1:E1000")->getAlignment()->setWrapText(TRUE);

$objPHPExcel->getActiveSheet()->getStyle("G1:G1000")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
$objPHPExcel->getActiveSheet()->getStyle("G1:G1000")->getAlignment()->setWrapText(TRUE);


// a ted data do sloupcu
$artikel=$domxml->getElementsByTagName("art");
foreach($artikel as $a)
{
    $aChilds = $a->childNodes;
    telo($objPHPExcel,$radek,$sloupec,$cells,$aChilds);
    $radek++;
}

$sloupecAktual = 'A';
$sloupecMax = $sloupecAktual;
    foreach ($cells as $cellname=>$ch) {
	$popis = $ch['popis'];
	$objPHPExcel->getActiveSheet()->getColumnDimension($sloupecAktual)->setWidth(strlen($popis)+2);
	$sloupecAktual = chr(ord($sloupecAktual)+1);
	$sloupecMax = $sloupecAktual;
}

$objPHPExcel->getActiveSheet()->getStyle("A1:$sloupecMax"."1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
$objPHPExcel->getActiveSheet()->getStyle("A1:$sloupecMax"."1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle("A1:$sloupecMax"."1")->getFill()->getStartColor()->setARGB('FFFFB3B3');



// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E195');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="E195.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;

?>
