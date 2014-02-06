<?php
session_start();
require_once "../fns_dotazy.php";

dbConnect();

$doc_title = "S395";
$doc_subject = "S395 Report";
$doc_keywords = "S395";

// necham si vygenerovat XML

$parameters=$_GET;


$kd = trim($_GET['kunde']);
$teil = trim($_GET['teil']);
$zeitpunkt = trim($_GET['zeitpunkt']);
$datumvonDB = trim($_GET['datumvon']);

//$stampVon = getLagerInventurDatum($dil);
//$stampBis = date('Y-m-d H:i:s');



require_once('S395_xml.php');

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
=>array("sirka"=>22,"ram"=>'LBT',"align"=>"L","radek"=>0,"fill"=>1),
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
"Summe i.A."
=>array("sirka"=>29,"ram"=>'BTR',"align"=>"R","radek"=>0,"fill"=>1),
"XX"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"XY"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"8V"
=>array("sirka"=>10,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
"8X"
=>array("sirka"=>15,"ram"=>'BT',"align"=>"R","radek"=>0,"fill"=>1),
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

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S395 Lagerbestand - Teil - Datum (Bewegungen ab letzte Inv. bzw. $datumvonDB)", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-10, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
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

$pdf->AddPage();

$teile = $domxml->getElementsByTagName("teil");
foreach($teile as $teil){
    $teilChilds = $teil->childNodes;
    $teilnr = getValueForNode($teilChilds,"teilnr");
    $inventurError = getValueForNode($teilChilds,"inventur_error");
    $dlagerbewError = getValueForNode($teilChilds,"dlagerbew_error");

    test_pageoverflow_noheader($pdf, 5*5);
    // hlavicka
    $pdf->SetFont("FreeSans", "B", 8);
    foreach($cells_header as $klic=>$header){
        if($klic=='teilnr')
            $cell = $teilnr;
        else
            $cell = $klic;
        $pdf->Cell($header["sirka"],5,$cell,$header["ram"],$header["radek"],$header["align"],$header["fill"]);
    }

        //mam nejaky obsah skladu, tak jdu na kresleni obsahu skladu
        $sklady = $teil->getElementsByTagName("sklady");
        foreach($sklady as $s)
            $sklad = $s;
        $skladChilds = $sklad->childNodes;

        // 1. radek s inventurou
        $pdf->SetFont("FreeSans", "", 6);
        foreach($cells_header as $klic=>$header){
            if($klic=='teilnr'){
		if($inventurError=="NO_INVENTUR")
		    $cell = "keine Inventur";
		else
		    $cell = "I:".getValueForNode($teilChilds, 'inventurdatum');
            }
            else{
                    $nodeName = "inventur_".$klic;
                    $cell = getValueForNode($skladChilds, $nodeName);
                    $summeArray[$nodeName]=$cell;
            }

            $pdf->Cell($header["sirka"],5,$cell,$header["ram"],$header["radek"],$header["align"],0);
        }

        // 2. radek s BewPlus
        $pdf->SetFont("FreeSans", "", 6);
        foreach($cells_header as $klic=>$header){
            if($klic=='teilnr'){
                $cell = "Bew.plus";
            }
            else{
                    $nodeName = "plus_".$klic;
                    $cell = getValueForNode($skladChilds, $nodeName);
                    $summeArray[$nodeName]=$cell;
            }

            $pdf->Cell($header["sirka"],5,$cell,$header["ram"],$header["radek"],$header["align"],0);
        }

        // 3. radek s BewMinus
        $pdf->SetFont("FreeSans", "", 6);
        foreach($cells_header as $klic=>$header){
            if($klic=='teilnr'){
                $cell = "Bew.minus";
            }
            else{
                    $nodeName = "minus_".$klic;
                    $cell = getValueForNode($skladChilds, $nodeName);
                    $summeArray[$nodeName]=$cell;
            }

            $pdf->Cell($header["sirka"],5,$cell,$header["ram"],$header["radek"],$header["align"],0);
        }

        // 4. radek se souctem
        $pdf->SetFont("FreeSans", "B", 6);
        foreach($cells_header as $klic=>$header){
            if($klic=='teilnr'){
                $cell = "Sum.Teil";
            }
            elseif($klic=="Summe i.A."){
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
                $cell = number_format($cell, 0,'','');
            }
            else{
                    $inventurKlic = "inventur_".$klic;
                    $plusKlic = "plus_".$klic;
                    $minusKlic = "minus_".$klic;
                    $cell = $summeArray[$inventurKlic]+$summeArray[$plusKlic]-$summeArray[$minusKlic];
                    $summeArray[$klic]=$cell;
                    $cell = number_format($cell, 0,'','');
            }

            $pdf->Cell($header["sirka"],5,$cell,$header["ram"],$header["radek"],$header["align"],0);
        }
        $pdf->Ln();
        unset($summeArray);
}
// tabulka s prehledem definovanych skladu
$pdf->AddPage();
$pdf->Cell(0,5,"Lager Legende",'B',1,'L',1);
$pdf->SetFont("FreeSans", "", 8);
$seznamSkladu = $domxml->getElementsByTagName("lager");
foreach($seznamSkladu as $sklad)
{
	$skladChildNodes = $sklad->childNodes;
	$obsahBunky=getValueForNode($skladChildNodes,"kz");
	$pdf->Cell(15,5,$obsahBunky,'0',0,'L',0);
	$obsahBunky=getValueForNode($skladChildNodes,"beschreibung");
	$pdf->Cell(0,5,$obsahBunky,'0',1,'L',0);
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
