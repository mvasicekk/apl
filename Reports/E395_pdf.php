<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

dbConnect();

$doc_title = "E395";
$doc_subject = "E395 Report";
$doc_keywords = "E395";

// necham si vygenerovat XML

$parameters=$_GET;


$kd = trim($_GET['kunde']);
$teil = trim($_GET['teil']);
$zeitpunkt = trim($_GET['zeitpunkt']);
$datumvonDB = trim($_GET['datumvon']);

if($teil=='*') $teil='';
//$stampVon = getLagerInventurDatum($dil);
//$stampBis = date('Y-m-d H:i:s');



require_once('E395_xml.php');

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
		$params .= $label.": ".$value."  ";
	}
}


$cells_header = 
array(
"teilnr"
=>array("sirka"=>22,"ram"=>'LBTR',"align"=>"L","radek"=>0,"fill"=>1),
"gew"
=>array("sirka"=>18,"ram"=>'LBTR',"align"=>"R","radek"=>0,"fill"=>1),
"vpe"
=>array("sirka"=>18,"ram"=>'LBTR',"align"=>"R","radek"=>0,"fill"=>1),    
"inv_datum"
=>array("sirka"=>18,"ram"=>'LBTR',"align"=>"R","radek"=>0,"fill"=>1),        
"0D"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
//"0S"
//=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"1R"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"2T"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"3P"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"4R"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"5K"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"5Q"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"6F"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"8E"
=>array("sirka"=>11,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"A2"
=>array("sirka"=>10,"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),
"A4"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"A6"
=>array("sirka"=>10,"ram"=>'BTR',"align"=>"R","radek"=>0,"fill"=>1),
"SumAuftr"
=>array("sirka"=>15,"ram"=>'BTR',"align"=>"R","radek"=>0,"fill"=>1),
"XX"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"XY"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"8V"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"8X"
=>array("sirka"=>12,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"B2"
=>array("sirka"=>10,"ram"=>'LBT',"align"=>"R","radek"=>0,"fill"=>1),
"B4"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"B6"
=>array("sirka"=>10,"ram"=>'BTR',"align"=>"R","radek"=>0,"fill"=>1),
"9V"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"9R"
=>array("sirka"=>0,"ram"=>'BRT',"align"=>"R","radek"=>1,"fill"=>1)
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

function getChildValueForLagerKz($skladyNodes,$childvalue,$lagerkz)
{
	$childValue=0;
	foreach($skladyNodes as $skladNode)
	{
		// jakou mam hodnotu v lagerkz
		$skladNodeChilds = $skladNode->childNodes;
		$lagerKzValue = getValueForNode($skladNodeChilds,"lagerkz");
		if($lagerKzValue==$lagerkz)
		{
			$childValue = getValueForNode($skladNodeChilds,$childvalue);
		}
	}
	
	return $childValue;
}
				
function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		return 1;
	}
	else
		return 0;
}

/** PHPExcel */
require_once '../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

$user = get_user_pc();
// Set properties
$objPHPExcel->getProperties()->setCreator($user)
							 ->setLastModifiedBy($user)
							 ->setTitle("E395")
							 ->setSubject("E395")
							 ->setDescription("E395")
							 ->setKeywords("office openxml php")
							 ->setCategory("phpexcel");

// popisky sloupcu
$radek = 1;
$sloupec = 0;

foreach($cells_header as $key=>$ch){
    $popis = $ch['popis'];
    $popis = $key;
    $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
    $sloupec++;
}
$radek++;
$sloupec = 0;

$a = AplDB::getInstance();

$teile = $domxml->getElementsByTagName("teil");
foreach($teile as $teil){
    $sloupec = 0;
    $teilChilds = $teil->childNodes;
    $teilnr = getValueForNode($teilChilds,"teilnr");
    $inventurError = getValueForNode($teilChilds,"inventur_error");
    $dlagerbewError = getValueForNode($teilChilds,"dlagerbew_error");

    //teilnr
    $cellobsah = "$teilnr ";
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
    $sloupec++;
    
    //gew
    $cellobsah = number_format ($a->getTeilGewicht ($teilnr), 2, '.', ' ');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
    $sloupec++;
    
    //vpe
    $vpo = $a->getVerpackungMenge($teilnr);
    $cellobsah = $vpo===NULL?'':$vpo['verpackungmenge'];
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
    $sloupec++;

    //invdatum
    if($inventurError=="NO_INVENTUR")
	$cellobsah = "keine Inventur";
    else
	$cellobsah = getValueForNode($teilChilds, 'inventurdatum');
    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
    $sloupec++;
    
    //sklady
    //mam nejaky obsah skladu, tak jdu na kresleni obsahu skladu
    $sklady = $teil->getElementsByTagName("sklady");
    foreach($sklady as $s)
	$sklad = $s;
    $skladChilds = $sklad->childNodes;

    // 1. radek s inventurou, jen plnim pole summeArray
    foreach($cells_header as $klic=>$header){
	if($klic=='teilnr'||$klic=='gew'||$klic=='vpe'||$klic=='inv_datum'||$klic=="SumAuftr"){
	}
        else{
	    $nodeName = "inventur_".$klic;
            $cell = getValueForNode($skladChilds, $nodeName);
            $summeArray[$nodeName]=  intval($cell);
//	    echo "$nodeName:$cell<br>";
        }
    }

    // 2. radek s BewPlus, jen plnim pole summeArray
    foreach($cells_header as $klic=>$header){
	if($klic=='teilnr'||$klic=='gew'||$klic=='vpe'||$klic=='inv_datum'||$klic=="SumAuftr"){
        }
        else{
            $nodeName = "plus_".$klic;
            $cell = getValueForNode($skladChilds, $nodeName);
            $summeArray[$nodeName]=intval($cell);
//	    echo "$nodeName:$cell<br>";
        }
    }

    // 3. radek s BewMinus, jen plnim pole summeArray
    foreach($cells_header as $klic=>$header){
        if($klic=='teilnr'||$klic=='gew'||$klic=='vpe'||$klic=='inv_datum'||$klic=="SumAuftr"){
        }
        else{
	    $nodeName = "minus_".$klic;
            $cell = getValueForNode($skladChilds, $nodeName);
            $summeArray[$nodeName]=$cell;
//	    echo "$nodeName:$cell<br>";
        }
    }

    // 4. radek se souctem
    foreach($cells_header as $klic=>$header){
            if($klic=='teilnr'||$klic=='gew'||$klic=='vpe'||$klic=='inv_datum'){
//		$sloupec++;
            }
            elseif($klic=="SumAuftr"){
                $cell = $summeArray['0D']+
                        $summeArray['0S']+
                        $summeArray['1R']+
                        $summeArray['2T']+
                        $summeArray['3P']+
                        $summeArray['4R']+
                        $summeArray['5K']+
                        $summeArray['5Q']+
                        $summeArray['6F']+
                        $summeArray['8E']+
                        $summeArray['A2']+
                        $summeArray['A4']+
                        $summeArray['A6'];
                $cellobsah = number_format($cell, 0,'','');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
		$sloupec++;
            }
            else{
                    $inventurKlic = "inventur_".$klic;
                    $plusKlic = "plus_".$klic;
                    $minusKlic = "minus_".$klic;
                    $cell = $summeArray[$inventurKlic]+$summeArray[$plusKlic]-$summeArray[$minusKlic];
                    $summeArray[$klic]=$cell;
                    $cellobsah = number_format($cell, 0,'','');
		    $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $cellobsah);
		    $sloupec++;
            }
    }
    $radek++;
    unset($summeArray);
}
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('E395');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="E395.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;