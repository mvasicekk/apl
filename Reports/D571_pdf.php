<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D571";
$doc_subject = "D571 Report";
$doc_keywords = "D571";

// necham si vygenerovat XML

$parameters=$_GET;

$von = $_GET['datumvon'];
$bis = $_GET['datumbis'];
$alleTeile = FALSE;

if((strlen(trim($von))==0) && (strlen(trim($bis))==0)){
    $alleTeile=TRUE;
}
else{
    $datumvom=make_DB_datum($_GET['datumvon']);
    $datumbis=make_DB_datum($_GET['datumbis']);
}


$kunde=$_GET['kunde'];

$teillangsort = $_GET['sort'];

//echo 'teillangsort='.$teillangsort;

if(!strcmp($teillangsort,"TeilNr - Original"))
    $teillangsort=1;
else
    $teillangsort=0;

if($teillangsort!=0)
    $parameters['teillangsort']="Ja";
else
    $parameters['teillangsort']="Nein";



require_once('D571_xml.php');

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


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells = 
array(

"teilnr"
=> array ("popis"=>"","sirka"=>20,"ram"=>'TLBR',"align"=>"L","radek"=>0,"fill"=>0),

"teillang"
=> array ("maxchars"=>18,"popis"=>"","sirka"=>25,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>0),

"teilbez"
=> array ("maxchars"=>18,"popis"=>"","sirka"=>25,"ram"=>'BTR',"align"=>"L","radek"=>0,"fill"=>0),

"brgew"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"gew"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"spg"
=> array ("maxchars"=>15,"popis"=>"","sirka"=>7,"ram"=>'TBR',"align"=>"R","radek"=>0,"fill"=>0),

"doku_nr"
=> array ("maxchars"=>4,"popis"=>"","sirka"=>10,"ram"=>'BLR',"align"=>"L","radek"=>0,"fill"=>0),

"einlag_datum"
=> array ("maxchars"=>8,"popis"=>"","sirka"=>11,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"musterplatz"
=> array ("maxchars"=>15,"popis"=>"","sirka"=>22,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"freigabe_am"
=> array ("maxchars"=>8,"popis"=>"","sirka"=>11,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"freigabe_vom"
=> array ("maxchars"=>20,"popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"L","radek"=>1,"fill"=>0),
    
    
);

$cells_header = 

array(
"teilnr"
=> array ("popis"=>"\nTeilNr","sirka"=>20,"ram"=>'LRTB',"align"=>"L","radek"=>0,"fill"=>1),

"teillang"
=> array ("popis"=>"\nTeilNr - Original","sirka"=>25,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>1),

"teilbez"
=> array ("popis"=>"\nTeilbezeichnung","sirka"=>25,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"brgew"
=> array ("nf"=>array(2,',',' '),"popis"=>"Brutto\nGewicht","sirka"=>10,"ram"=>'RTB',"align"=>"R","radek"=>0,"fill"=>1),

"gew"
=> array ("nf"=>array(2,',',' '),"popis"=>"Netto\nGewicht","sirka"=>10,"ram"=>'RTB',"align"=>"R","radek"=>0,"fill"=>1),

"spg"
=> array ("popis"=>"Stk/\nGeh.","sirka"=>7,"ram"=>'RTB',"align"=>"R","radek"=>0,"fill"=>1),

"doku_nr"
=> array ("maxchars"=>4,"popis"=>"Doku\nNr","sirka"=>10,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>1),

"einlag_datum"
=> array ("maxchars"=>8,"popis"=>"Eingel.\nam","sirka"=>11,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>1),

"musterplatz"
=> array ("maxchars"=>15,"popis"=>"Muster\nplatz","sirka"=>22,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>1),

"freigabe_am"
=> array ("maxchars"=>8,"popis"=>"Freigabe\nam","sirka"=>11,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>1),

"freigabe_vom"
=> array ("maxchars"=>20,"popis"=>"Freigabe\nvom","sirka"=>0,"ram"=>'TBR',"align"=>"L","radek"=>1,"fill"=>1),
    
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
function pageheader($pdfobjekt,$pole,$headervyskaradku,$childNodes)
{
	$pdfobjekt->SetFont("FreeSans", "B", 10);
    $kdname  = getValueForNode($childNodes,"name1");
    $kdnr  = getValueForNode($childNodes,"kundenr");
    $kdUeberschrift = $kdnr." ".$kdname;

	$pdfobjekt->Cell(0,10,$kdUeberschrift,'0',1,'L',0);
	
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	//if($pdfobjekt->PageNo()==1)
	//{
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	//}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


/**
 *
 * @param TCPDF $pdfobjekt
 * @param type $pole
 * @param type $zahlavivyskaradku
 * @param type $rgb
 * @param type $funkce
 * @param type $nodelist
 * @param type $newTeil 
 */
function telo($pdfobjekt, $pole, $zahlavivyskaradku, $rgb, $funkce, $nodelist, $newTeil) {
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);
    // pujdu polem pro zahlavi a budu prohledavat predany nodelist
//    $teillang = getValueForNode($nodelist,"teillang");

    foreach ($pole as $nodename => $cell) {
	if (array_key_exists("nf", $cell)) {
	    $cellobsah =
		    number_format(getValueForNode($nodelist, $nodename), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
	} else {
	    $cellobsah = getValueForNode($nodelist, $nodename);
	}

	/**
	 * omezeni na pocet znaku
	 */
	if (array_key_exists("maxchars", $cell)) {
	    $maxchars = $cell['maxchars'];
	    if (strlen($cellobsah) > $maxchars) {
		$new = substr($cellobsah, 0, $maxchars) . "...";
		$cellobsah = $new;
	    }
	}
	//||$nodename="teillang"||$nodename="teilbez"||$nodename="gew"||$nodename="brgew"||$nodename="spg"
	$curY = $pdfobjekt->GetY();
	$startX = PDF_MARGIN_LEFT;
	$endX = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT;
	if(($newTeil==1)&&($nodename=="teilnr")){
	    $pdfobjekt->SetLineWidth(0.5);
	    $pdfobjekt->Line($startX,$curY,$endX,$curY);
	}

	$pdfobjekt->SetLineWidth(0.05);
	
	if(($newTeil==0)&&($nodename=="teilnr"||$nodename=="teillang"||$nodename=="teilbez"||$nodename=="gew"||$nodename=="brgew"||$nodename=="spg")){
	    $pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, "", '0', 0, 'L', $cell["fill"]);
	}
	else{
	    $pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, $cellobsah, $cell["ram"], $cell["radek"], $cell["align"], $cell["fill"]);	    
	}

    }
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 7);
}

function teil_no_doku($pdfobjekt, $pole, $zahlavivyskaradku, $rgb, $funkce, $nodelist) {
    global $cells;
    
    $pdfobjekt->SetFont("FreeSans", "", 6.5);
    $pdfobjekt->SetFillColor($rgb[0], $rgb[1], $rgb[2], 1);

    $nodes = array('teilnr','teillang','teilbez','brgew','gew','spg');
    
    foreach ($nodes as $nodename){
	$cell = $cells[$nodename];
	
	if (array_key_exists("nf", $cell)) {
	    $cellobsah =
		    number_format(getValueForNode($nodelist, $nodename), $cell["nf"][0], $cell["nf"][1], $cell["nf"][2]);
	} else {
	    $cellobsah = getValueForNode($nodelist, $nodename);
	}

	/**
	 * omezeni na pocet znaku
	 */
	if (array_key_exists("maxchars", $cell)) {
	    $maxchars = $cell['maxchars'];
	    if (strlen($cellobsah) > $maxchars) {
		$new = substr($cellobsah, 0, $maxchars) . "...";
		$cellobsah = $new;
	    }
	}

//	$cellobsah = getValueForNode($nodelist, $node);
	$pdfobjekt->Cell($cell["sirka"], $zahlavivyskaradku, $cellobsah, $cell["ram"], $cell["radek"], $cell["align"], $cell["fill"]);
    }

    $pdfobjekt->SetFont("FreeSans", "B", 6.5);
    $cellobsah = "KEINE TeileDoku vorhanden !!!";
    $pdfobjekt->Cell(0, $zahlavivyskaradku, $cellobsah, '1', 1, 'L', 0);
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 7);
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
function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$pocetDilu,$pocetDokumentu)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 7);
	$pdfobjekt->Cell(0,$vyskaradku,"Anzahl TeileNr: ".$pocetDilu.", Anzahl Dokumente: ".$pocetDokumentu,'B',0,'L',$fill);
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}



function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku,$childNodes)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku,$childNodes);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$beschriftung = "D571 Teiledokumentation";
if($alleTeile===TRUE) $beschriftung .= " - alle Teile";

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $beschriftung, $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-15, PDF_MARGIN_RIGHT);
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
//$pdf->Ln();
//$pdf->Ln();

dbConnect();
//$pocetpozic = 0;
//$pocetpozicSMustrem=0;
//$pocetpozicSFreigabe1=0;
//$pocetpozicSFreigabe2=0;

$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
	$pdf->AddPage();
	pageheader($pdf,$cells_header,4,$kundeChildNodes);
	$teile=$kunde->getElementsByTagName("teil");
	$newTeil = 1;
	$citacDilu = 0;
	$citacDokumentu = 0;
	foreach($teile as $teil)
	{
		$teilChilds=$teil->childNodes;
		$musterplatz = trim(getValueForNode($teilChilds,"musterplatz"));
		$dokumente = $teil->getElementsByTagName("dok");
		$dokumentenAnzahl = 0;
		foreach($dokumente as $dok)
		{
		    $dokChilds=$dok->childNodes;
		    telo($pdf,$cells,4,array(255,255,255),"",$dokChilds,$newTeil);
		    test_pageoverflow($pdf,4,$cells_header,4,$kundeChildNodes);
		    $newTeil = 0;
		    $dokumentenAnzahl++;
		    $citacDokumentu++;
		}
		$newTeil = 1;
		if(($dokumentenAnzahl==0)&&(floatval(getValueForNode($teilChilds, 'gew'))>0)){
		    // teil hat keine dokumente -> Felhlermaldung zeigen
		    teil_no_doku($pdf,$cells,4,array(255,255,255),"",$teilChilds);
		    test_pageoverflow($pdf,4,$cells_header,4,$kundeChildNodes);
		}
		$citacDilu++;
	}
}

test_pageoverflow($pdf,5,$cells_header,4,$kundeChildNodes);
zapati_sestava($pdf,5,array(200,200,255),$citacDilu,$citacDokumentu);
$pdf->Ln(10);
// legenda k typum dokumentu
$a = AplDB::getInstance();
$docTypenArray = $a->getDokuTypArray();
$pocetTypu = count($docTypenArray);
test_pageoverflow($pdf,($pocetTypu+1)*4,$cells_header,4,$kundeChildNodes);
if ($pocetTypu > 0) {
    
    $pdf->Cell(20, 4, "DokuNr", '1', 0, 'R', 0);
    $pdf->Cell(60, 4, "Beschreibung", '1', 1, 'L', 0);
    
    foreach ($docTypenArray as $key => $value) {
	$pdf->Cell(20, 4, $value['doku_nr'], '1', 0, 'R', 0);
	$pdf->Cell(60, 4, $value['doku_beschreibung'], '1', 0, 'L', 0);
	$pdf->Ln();
    }
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
