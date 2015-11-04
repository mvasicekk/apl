<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "D811";
$doc_subject = "D811";
$doc_keywords = "D811";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$parameters=$_GET;
$von = $apl->make_DB_datum($_GET['terminvon'])." 00:00:00";
$bis = $apl->make_DB_datum($_GET['terminbis'])." 23:59:59";
$spedvon = $_GET['spedvon'];
$spedbis = $_GET['spedbis'];

require_once('D811_xml.php');

$lkwColors = array(
    array(255,204,204),
    array(255,229,204),
    array(255,255,204),
    array(229,255,204),
    array(204,255,229),
    array(204,229,255),
    array(229,204,255),
    array(255,204,229),
);

$cells = 
array(
'dummy1'=>array('sirka'=>50,'ram'=>'0','align'=>'L','radek'=>0,'fill'=>0),

"abgnr"=>array ("sirka"=>15,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"gutstk"=>array ("nf"=>array(0,',',' '),"sirka"=>15,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"auss2"=>array ("nf"=>array(0,',',' '),"sirka"=>10,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"auss4"=>array ("nf"=>array(0,',',' '),"sirka"=>10,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"auss6"=>array ("nf"=>array(0,',',' '),"sirka"=>10,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd"=>array ("nf"=>array(0,',',' '),"sirka"=>15,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"sumvzaby"=>array ("nf"=>array(0,',',' '),"sirka"=>15,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"sumverb"=>array ("nf"=>array(0,',',' '),"sirka"=>15,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),
    
"fac1"=>array ("nf"=>array(2,',',' '),"sirka"=>12,"ram"=>0,"align"=>"R","radek"=>0,"fill"=>0),

"preis"=>array ("format"=>array(6,''),"nf"=>array(4,',',' '),"sirka"=>0,"ram"=>0,"align"=>"R","radek"=>1,"fill"=>0)

);
//exit();

// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
$parameters=$domxml->getElementsByTagName("parameters");
//$paramnodes=$parameters->childNodes;

foreach ($parameters as $param)
{
	$parametry=$param->childNodes;
	//print_r($parametry);
	// v ramci parametru si prectu label a hodnotu
	//$params.=$parametry->nodeName.": ".$parametry->nodeValue."   ";
	foreach($parametry as $parametr)
	{
		//echo $parametr->nodeName;
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

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D811 - Rundlauf", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items
$pdf->SetFont("FreeSans", "", 9);
$pdf->SetAutoPageBreak(TRUE,15);

$pdf->setPrintHeader(TRUE);
$pdf->setPrintFooter(TRUE);

$pdf->AddPage();

$lkws = $domxml->getElementsByTagName("rundlauf");
$lkColor = 0;
$abDatumOld = "";
//AplDB::varDump($lkwColors[1]);
$lkwCounter = 0;

foreach ($lkws as $lkw){
    $lkwChilds = $lkw->childNodes;
    
    $abDatum = substr(getValueForNode($lkwChilds, 'ab_aby_soll_datetime'),0,10);
    if($abDatumOld!=$abDatum){
	$lkColor++;
	$abDatumOld = $abDatum;
	if($lkColor>count($lkwColors)-2){
	    $lkColor = 0;
	}
	if($lkwCounter>0){
	    $pdf->Ln(3);
	}
    }
    $c = $lkwColors[$lkColor];
//    AplDB::varDump($c);
    zahlavi_lkw($pdf, $cells, $lkwChilds, 5,$c);
    $imexs = $lkw->getElementsByTagName('pay');
    foreach($imexs as $imex){
	$imexChilds = $imex->childNodes;
	imex_radek($pdf, $cells, $imexChilds, 3);
    }
    zapati_lkw($pdf, $cells, $lkwChilds, 5,$c);
    $lkwCounter++;
}
$pdf->Output();

//------------------------------------------------------------------------------
// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
function getValueForNode($nodelist, $nodename) {
    $nodevalue = "";
    foreach ($nodelist as $node) {
	if ($node->nodeName == $nodename) {
	    $nodevalue = $node->nodeValue;
	    return $nodevalue;
	}
    }
    return $nodevalue;
}

/**
 * 
 * @param type $pdf
 * @param type $cells
 * @param type $childs
 * @param type $vyskaRadku
 * @param type $rgb
 */
function zahlavi_lkw($pdf, $cells, $childs, $vyskaRadku, $rgb) {

    
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

    $pdf->SetFont("FreeSans", "B", 8);
    $obsah = "".date('d.m.Y H:i',strtotime(getValueForNode($childs, 'ab_aby_soll_datetime')));
    $pdf->Cell(
	    45, $vyskaRadku, $obsah, 'LT', 0, // odradkovat
	    'L', 1
    );
    
    $pdf->SetFont("FreeSans", "", 8);
    $obsah = getValueForNode($childs, 'id');
    
    $pdf->Cell(
	    20, $vyskaRadku, $obsah, 'T', 0, // odradkovat
	    'L', 1
    );
    
    
    $pdf->SetFont("FreeSans", "", 8);
    $obsah = getValueForNode($childs, 'spediteurname');
    $pdf->Cell(
	    50, $vyskaRadku, $obsah, 'T', 0, // odradkovat
	    'L', 1
    );
    
    $obsah = getValueForNode($childs, 'bemerkung');
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->Cell(
	    0, $vyskaRadku, $obsah, 'RT', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->Ln();
}

/**
 * 
 * @param type $pdf
 * @param type $cells
 * @param type $childs
 * @param type $vyskaRadku
 * @param type $rgb
 */
function zapati_lkw($pdf, $cells, $childs, $vyskaRadku, $rgb) {

    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

    $obsah = getValueForNode($childs, 'preis');
    $obsah = number_format($obsah, 0, ',', ' ');
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $obsah = getValueForNode($childs, 'rabatt');
    $obsah = number_format($obsah, 2, ',', ' ')."%";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $obsah = getValueForNode($childs, 'betrag');
    $obsah = number_format($obsah, 2, ',', ' ');
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $obsah = getValueForNode($childs, 'rechnung');
    //$obsah = number_format($obsah, 2, ',', ' ')."%";
    $pdf->Cell(
	    0, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->Ln();
}

/**
 * 
 * @param type $pdf
 * @param type $cells
 * @param type $childs
 * @param type $vyskaRadku
 * @param type $rgb
 */
function imex_radek($pdf, $cells, $childs, $vyskaRadku) {

    $a = AplDB::getInstance();
    $pdf->SetFont("FreeSans", "", 7);
    

    $imex = getValueForNode($childs, 'imex');
    $auftragsnr = getValueForNode($childs, 'auftragsnr');

    $palArrayA = $a->getBehaelterInExport($auftragsnr);
    $palObsah = "";
    if($palArrayA!==NULL){
	foreach ($palArrayA as $pal){
	    $palSum[$pal['behname']]+=$pal['sum_stk'];
	    
	}
	foreach($palSum as $palName=>$stk){
	    $palObsah.= $stk."x ".$palName."  ";
	}
    }
    
    $obsah = $imex.$auftragsnr;
    
    if($imex=="E"){
	$pdf->SetFillColor(230, 255,230);
    }
    else{
	$pdf->SetFillColor(255, 230,230);
    }
    
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'L', 0, // odradkovat
	    'L', 1
    );
    
    $zielort = trim(getValueForNode($childs, 'zielort'));
    if(strlen($zielort)>0){
	$obsah = "--> ".$zielort;
    }
    else{
	$obsah = "";
    }
    
    $pdf->Cell(
	    50, $vyskaRadku, $obsah, '', 0, // odradkovat
	    'L', 0
    );

    $pdf->SetFont("FreeSans", "I", 7);
    if(strlen($zielort)>0){
	$obsah = $palObsah;
    }
    else{
	$obsah = "";
    }
    $pdf->Cell(
	    0, $vyskaRadku, $obsah, 'R', 0, // odradkovat
	    'R', 0
    );
    
    $pdf->Ln();
}
