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
$bSpediteur = $_GET['typ']=="Dispo"?FALSE:TRUE;
$kundevon = $_GET['kundevon'];
$kundebis = $_GET['kundebis'];

$password = $_GET['password'];

//$k = $_GET['kurs'];

// TODO kurs by mel byt podle ab_aby_soll_datetime - tj. podle datumu odjezdu kamionu
// TODO kurs by mel byt podle ab_aby_soll_datetime - tj. podle datumu odjezdu kamionu

//if($k=="aktuell"){
//    $kurs = number_format($apl->getKurs(date('Y-m-d'), 'EUR', 'CZK'), 2);
//    //$kurs = number_format($apl->getKurs(date('Y-m-d',  strtotime($bis)), 'EUR', 'CZK'), 2);
//}
//else
//    $kurs = number_format($apl->getKurs('2099-12-31', 'EUR', 'CZK'), 2);

$kurs = number_format($apl->getKurs(date('Y-m-d',strtotime($von)), 'EUR', 'CZK'), 3);

$access = TRUE;

if(!$bSpediteur){
    $access = $apl->testReportPassword("D811",$password,$_SESSION['user'],0);
}


if(!$access){
    echo "<h1>Nepovoleny pristup.</h1>";
    exit();
}


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

$sumFrachtLkw = array(
    'preis_czk'=>0,
    'kosten_eur'=>0,
    'betrag_eur'=>0
);

$sumFrachtBericht  = array(
    'preis_czk'=>0,
    'kosten_czk'=>0,
    'kosten_eur'=>0,
    'betrag_eur'=>0
);

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
		if(strtolower($label)!="password")
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

$kursVon = $kurs;
$kursBis = number_format($apl->getKurs(date('Y-m-d',strtotime($bis)), 'EUR', 'CZK'), 3);
if($kursVon!=$kursBis){
    // pokud mam dva ruzne kurzy pro von a bit, tak je zobrazim oba
    $kursBisStr = " ,zum ".date('d.m.Y',  strtotime($bis))." : $kursBis CZK/EUR";
}
else{
    $kursBisStr = "";
}


// pozorne
//$params="";
$kursSuffix = "\n(kurs zum ".date('d.m.Y',  strtotime($von))." : $kurs CZK/EUR".$kursBisStr." )";
if($bSpediteur){
    $kursSuffix="";
}


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D811 - Rundlauf", $params.$kursSuffix);
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
page_header($pdf, "", "", 5, array(255,255,230));

$lkws = $domxml->getElementsByTagName("rundlauf");
$lkColor = 0;
$abDatumOld = "";
//AplDB::varDump($lkwColors[1]);
$lkwCounter = 0;

foreach ($lkws as $lkw){
    $lkwChilds = $lkw->childNodes;
    
    $abDatum = substr(getValueForNode($lkwChilds, 'ab_aby_soll_datetime'),0,10);
    $abDateTime = getValueForNode($lkwChilds, 'ab_aby_soll_datetime');
    
    // kurs nastavim podle datumu ab_aby_soll_datetime
    $kurs = $apl->getKurs(date('Y-m-d',  strtotime($abDateTime)), 'EUR', 'CZK');

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
    //hlavicka a alespon 1 radek ?
    test_pageoverflow($pdf, 5+5, "");
    zahlavi_lkw($pdf, $cells, $lkwChilds, 5,$c);
    $imexs = $lkw->getElementsByTagName('pay');
    foreach($imexs as $imex){
	$imexChilds = $imex->childNodes;
	test_pageoverflow($pdf, 5, "");
	imex_radek($pdf, $cells, $imexChilds,$lkwChilds, 3);
    }
    test_pageoverflow($pdf, 5, "");
    zapati_lkw($pdf, $cells, $lkwChilds, 5,$c);
    $lkwCounter++;
}

if(!$bSpediteur){
    zapati_bericht($pdf, $cells, $lkwChilds, 5,array(255,255,255));
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

    global $sumFrachtLkw;

    $sumFrachtLkw['preis_czk']=0;
    $sumFrachtLkw['kosten_eur']=0;
    $sumFrachtLkw['betrag_eur']=0;
    
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

    $pdf->SetFont("FreeSans", "B", 8);
    $obsah = "".date('d.m.Y H:i',strtotime(getValueForNode($childs, 'ab_aby_soll_datetime')));
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LT', 0, // odradkovat
	    'L', 1
    );
    
    $pdf->SetFont("FreeSans", "", 8);
    $obsah = getValueForNode($childs, 'spediteurname')."( ".getValueForNode($childs, 'id')." )";
    $pdf->Cell(
	    70, $vyskaRadku, $obsah, 'T', 0, // odradkovat
	    'L', 1
    );
//    
//    
//    $pdf->SetFont("FreeSans", "", 8);
//    $obsah = getValueForNode($childs, 'spediteurname');
//    $pdf->Cell(
//	    50, $vyskaRadku, $obsah, 'T', 0, // odradkovat
//	    'L', 1
//    );
    
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
 * @param TCPDF $pdf
 * @param type $cells
 * @param type $childs
 * @param type $vyskaRadku
 * @param type $rgb
 */
function zapati_lkw($pdf, $cells, $childs, $vyskaRadku, $rgb) {

    global $sumFrachtLkw;
    global $sumFrachtBericht;
    global $kurs;
    global $bSpediteur;
    
    $pdf->SetFont("FreeSans", "B", 8);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

    $proforma = getValueForNode($childs, 'proforma');
    $obsah = $proforma;
    $pdf->SetFont("FreeSans", "B", 6);
    $pdf->Cell(
	    9, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->SetFont("FreeSans", "B", 7.5);
    $preisVereinbart = floatval(getValueForNode($childs, 'preis'));
    $obsah = getValueForNode($childs, 'preis');
    $obsah = number_format($obsah, 0, ',', ' ')."CZK";
    $pdf->Cell(
	    16, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    
    $pdf->SetFont("FreeSans", "B", 6);
    $rabatt = floatval(getValueForNode($childs, 'rabatt'));
    $obsah = getValueForNode($childs, 'rabatt');
    $obsah = number_format($obsah, 2, ',', ' ')."%";
    $pdf->Cell(
	    8, $vyskaRadku, $obsah, 'LB', 0, // odradkovat
	    'R', 1
    );
    
    $rabatt = floatval(getValueForNode($childs, 'rabatt'));
    $obsah = ($preisVereinbart - $preisVereinbart*$rabatt/100);
    $obsah = " -> ".number_format($obsah, 2, ',', ' ')."CZK";
    $pdf->Cell(
	    17, $vyskaRadku, $obsah, 'RB', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->SetFont("FreeSans", "B", 7.5);
    //kosten EUR
    $kostenCZK = ($preisVereinbart - $preisVereinbart*$rabatt/100);
    $sumFrachtBericht['kosten_czk']+=$kostenCZK;
    $kostenEUR = $kostenCZK / $kurs;
    $obsah = number_format($kostenEUR, 2, ',', ' ')."EUR";
    $ram = "LRB";
    if($bSpediteur){
	$obsah = "";
	$ram = "B";
    }
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, $ram, 0, // odradkovat
	    'R', 1
    );
    
    $obsah = $sumFrachtLkw['betrag_eur'];
    $obsah = number_format($obsah, 2, ',', ' ')."EUR";
    $ram = "LRB";
    if($bSpediteur){
	$obsah = "";
	$ram = "B";
    }
    $pdf->Cell(
	    20, $vyskaRadku, $obsah, $ram, 0, // odradkovat
	    'R', 1
    );
    
    $obsah = $sumFrachtLkw['betrag_eur'] - $kostenEUR;
    if($obsah<0){
	$pdf->setColorArray('text', array(255,0,0));
    }
    else{
	$pdf->setColorArray('text', array(0,0,0));
    }
    $obsah = number_format($obsah, 2, ',', ' ')."EUR";
    $ram = "LRB";
    if($bSpediteur){
	$obsah = "";
	$ram = "RB";
    }
    $pdf->Cell(
	    0, $vyskaRadku, $obsah, $ram, 0, // odradkovat
	    'R', 1
    );
    
    $sumFrachtBericht['preis_czk'] += $preisVereinbart;
    $sumFrachtBericht['kosten_eur'] += $kostenEUR;
    $sumFrachtBericht['betrag_eur'] += $sumFrachtLkw['betrag_eur'];
    
    $pdf->setColorArray('text', array(0,0,0));
    $pdf->Ln();
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		page_header($pdfobjekt, "", "", 5, array(255,255,230));
	}
}

/**
 * 
 * @param type $pdf
 * @param type $cells
 * @param type $childs
 * @param type $vyskaRadku
 * @param type $rgb
 */
function page_header($pdf, $cells, $childs, $vyskaRadku, $rgb) {

    global $bSpediteur;
    
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

    //hlavicka
    $obsah = "Preis vereinb.[CZK]";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRBT', 0, // odradkovat
	    'R', 1
    );
    $obsah = "Rabatt [%]";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRBT', 0, // odradkovat
	    'R', 1
    );
    
    
    $obsah = "Kosten [EUR]";
    $ram = "LRBT";
    if($bSpediteur){
	$obsah = "";
	$ram = "BT";
    }
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, $ram, 0, // odradkovat
	    'R', 1
    );
    
    $obsah = "Betrag [EUR]";
    $ram = "LRBT";
    if($bSpediteur){
	$obsah = "";
	$ram = "BT";
    }
    $pdf->Cell(
	    20, $vyskaRadku, $obsah, $ram, 0, // odradkovat
	    'R', 1
    );
    
    
    $obsah = "Betrag - Kosten [EUR]";
    $ram = "LRBT";
    
    if($bSpediteur){
	$obsah = "";
	$ram = "RBT";
    }

    $pdf->Cell(
	    0, $vyskaRadku, $obsah, $ram, 1, // odradkovat
	    'R', 1
    );
    
    //--------------------------------------------------------------------------
    
}
/**
 * 
 * @global array $sumFrachtBericht
 * @param type $pdf
 * @param type $cells
 * @param type $childs
 * @param type $vyskaRadku
 * @param type $rgb
 */
function zapati_bericht($pdf, $cells, $childs, $vyskaRadku, $rgb) {

    global $sumFrachtBericht;
    $pdf->Ln($vyskaRadku);
    
    $pdf->SetFont("FreeSans", "B", 7);
    $pdf->SetFillColor($rgb[0], $rgb[1], $rgb[2]);

    //hlavicka
    $obsah = "Preis vereinb.[CZK]";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRBT', 0, // odradkovat
	    'R', 1
    );
    $obsah = "Rabatt [%]";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRBT', 0, // odradkovat
	    'R', 1
    );
    $obsah = "Kosten [EUR]";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRBT', 0, // odradkovat
	    'R', 1
    );
    $obsah = "Betrag [EUR]";
    $pdf->Cell(
	    20, $vyskaRadku, $obsah, 'LRBT', 0, // odradkovat
	    'R', 1
    );
    $obsah = "Betrag - Kosten [EUR]";
    $pdf->Cell(
	    0, $vyskaRadku, $obsah, 'LRBT', 1, // odradkovat
	    'R', 1
    );
    
    //--------------------------------------------------------------------------
    
    $pdf->SetFont("FreeSans", "B", 8);
    $obsah = number_format($sumFrachtBericht['preis_czk'], 0, ',', ' ')."CZK";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->SetFont("FreeSans", "B", 6.5);
    if($sumFrachtBericht['preis_czk']!=0){
	$r = 100*(1-$sumFrachtBericht['kosten_czk']/$sumFrachtBericht['preis_czk']);
    }
    else{
	$r = 0;
    }
    
    $obsah = number_format($r, 2, ',', ' ')."%";
    $pdf->Cell(
	    8, $vyskaRadku, $obsah, 'LB', 0, // odradkovat
	    'R', 1
    );
    $obsah = " -> ".number_format($sumFrachtBericht['kosten_czk'], 0, ',', ' ')."CZK";
    $pdf->Cell(
	    17, $vyskaRadku, $obsah, 'RB', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->SetFont("FreeSans", "B", 8);
    //kosten EUR
    $obsah = number_format($sumFrachtBericht['kosten_eur'], 2, ',', ' ')."EUR";
    $pdf->Cell(
	    25, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $obsah = number_format($sumFrachtBericht['betrag_eur'], 2, ',', ' ')."EUR";
    $pdf->Cell(
	    20, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $obsah = $sumFrachtBericht['betrag_eur'] - $sumFrachtBericht['kosten_eur'];
    if($obsah<0){
	$pdf->setColorArray('text', array(255,0,0));
    }
    else{
	$pdf->setColorArray('text', array(0,0,0));
    }
    $obsah = number_format($obsah, 2, ',', ' ')."EUR";
    $pdf->Cell(
	    0, $vyskaRadku, $obsah, 'LRB', 0, // odradkovat
	    'R', 1
    );
    
    $pdf->setColorArray('text', array(0,0,0));
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
function imex_radek($pdf, $cells, $childs,$lkwchilds, $vyskaRadku) {

    global $kurs;
    global $sumFrachtLkw;
    global $bSpediteur;
    
    $a = AplDB::getInstance();
    $pdf->SetFont("FreeSans", "", 7);
    

    $imex = getValueForNode($childs, 'imex');
    $auftragsnr = getValueForNode($childs, 'auftragsnr');
    $frachtExp1 = $a->getFrachtForExport($auftragsnr);
    $palArrayA = $a->getBehaelterInExport($auftragsnr);
    $auftragInfo = $a->getAuftragInfoArray($auftragsnr);

    if($auftragInfo[0]['waehr_kz']=='CZK'){
	$frachtExp = $frachtExp1 / $kurs;
    }
    else{
	$frachtExp = $frachtExp1;
    }

    if($imex=='E'){
	$sumFrachtLkw['betrag_eur'] += $frachtExp;
    }
    
    
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
    $an_aby_soll_datetime = date('d.m.Y H:i',strtotime(getValueForNode($lkwchilds, 'an_aby_soll_datetime')));
    if(strlen($zielort)>0){
	$obsah = "--> ".$zielort;
    }
    else{
	$obsah = "--> Abydos am: ".$an_aby_soll_datetime;
    }
    
    $pdf->Cell(
	    50, $vyskaRadku, $obsah, '', 0, // odradkovat
	    'L', 0
    );

    if($imex=='E'){
	$obsah = number_format($frachtExp1,2,',',' ').$auftragInfo[0]['waehr_kz'];
	if($bSpediteur){
	    $obsah = "";
	}
	$pdf->Cell(
	    20, $vyskaRadku, $obsah, '', 0, // odradkovat
	    'R', 0
	);
    }
    
    
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
