<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

dbConnect();

$doc_title = "S390";
$doc_subject = "S390 Report";
$doc_keywords = "S390";

// necham si vygenerovat XML

$parameters=$_GET;


$dil=$_GET['teil'];
$bewegungen = $_GET['bewegungen'];

if($bewegungen=="a")
    $bDetails = TRUE;

//exit();

$inventur = TRUE;
$stampVon = getLagerInventurDatum($dil);
if($stampVon=='2099-01-01 00:00:00'){
    $stampVon = $_GET['datumvon'];
    $inventur = FALSE;
}
    
$stampBis = date('Y-m-d H:i:s');


require_once('S390_xml.php');



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
"0D"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//"0S"
//=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"1R"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"2T"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"3P"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"4R"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"5K"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"5Q"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"6F"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"8E"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"Summe i.A."
=>array("sirka"=>32,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"XX"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"XY"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"8V"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"8X"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"9V"
=>array("sirka"=>14,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
"9R"
=>array("sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1)
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

function test_pageoverflow($pdfobjekt,$vysradku)
{
	global $cells;
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageHeader($pdfobjekt,5,$cells);
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


function pageHeader($pdf,$h,$cells){
    $pdf->SetFont("FreeSans", "B", 8);
    foreach ($cells as $index => $cell) {
    $pdf->Cell($cell['width'], $h, $cell['label'], '1', $cell['ln'], $cell['align']);
 }
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
				

require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

if($inventur===TRUE){
    $inventurDate = " von: ".$stampVon;
}
else{
    $inventurDate = " ( keine Inventur ) von: ".$stampVon;
}

$params = "Teil: ".$dil.$inventurDate." bis: ".$stampBis;

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S390 Lagerbestand - Teil - Datum", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
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
//$pdf->AddPage();
//pageheader($pdf,$cells_header,4);
//$pdf->Ln();
//$pdf->Ln();

//dbConnect();
$pdf->AddPage();
// vykreslim zahlavi vybranych skladu
$pdf->Cell(25,5,"",'BR',0,'L',1);
foreach($cells_header as $cell=>$obsah)
{
	$pdf->Cell($obsah["sirka"],5,$cell,$obsah["ram"],$obsah["radek"],$obsah["align"],$obsah["fill"]);
}

$seznamSkladu = $domxml->getElementsByTagName("lager");


$radky = array(
"inventurstk"
=>array("popis"=>"Inventurstk."),
"gutplus"
=>array("popis"=>"Beweg.plus"),
"gutminus"
=>array("popis"=>"Beweg.minus")
);

$popisradku = array("Inventur","Bew.plus","Bew.minus","Summe Teil");
$i=0;

foreach($cells_header as $cell=>$obsah)
{
	$soucetSkladu[$cell]=0;
}

foreach($radky as $radek=>$radekarray)
{
	// popisradku
	$pdf->Cell(25,5,$popisradku[$i++],'BR',0,'L',0);
	// pojedu po vybranych skladech a budu vypisovat hodnotu inventurstk
	foreach($cells_header as $cell=>$obsah)
	{
		$hodnota=getChildValueForLagerKz($seznamSkladu,$radek,$cell);
		$obsahBunky = number_format($hodnota,0,',',' ');
		$pdf->Cell($obsah["sirka"],5,$obsahBunky,$obsah["ram"],$obsah["radek"],$obsah["align"],0);
		
		if($radek=="gutminus")
			$soucetSkladu[$cell]-=$hodnota;
		else
			$soucetSkladu[$cell]+=$hodnota;
	}
}

foreach($cells_header as $cell=>$obsah)
{
	$soucetSkladu['Summe i.A.']+=$soucetSkladu[$cell];
	if($cell=='8E') break;
}

// radek se souctama

$pdf->SetFont("FreeSans", "B", 8);
//$pdf->SetFillColor(255,255,100,1);

$pdf->Cell(25,5,$popisradku[$i],'BR',0,'L',1);
foreach($cells_header as $cell=>$obsah)
{
	$obsahBunky = number_format($soucetSkladu[$cell],0,',',' ');
	$pdf->Cell($obsah["sirka"],5,$obsahBunky,$obsah["ram"],$obsah["radek"],$obsah["align"],1);
}

//$pdf->SetFillColor($pdf->prevFillColor[0],$pdf->prevFillColor[1],$pdf->prevFillColor[2],0);
// ted tabulku s prehledem ausschussu
//$pdf->SetLeftMargin(200);

$pdf->Ln();
$yStart = $pdf->GetY();

$pdf->Cell(85,5,"Ausschussuebersicht i.A.",'B',1,'L',1);
$pdf->Cell(15,5,"A-Art",'B',0,'L',1);
$pdf->Cell(20,5,"DLAGERBEW",'B',0,'L',1);
$pdf->Cell(35,5,"DLAGERSTK (Inventur)",'B',0,'L',1);
$pdf->Cell(15,5,"Summe ",'B',1,'L',1);

 
$ausschussy = array("A2","A4","A6");
$ausschussGesamt=0;

$pdf->SetFont("FreeSans", "", 8);
foreach($ausschussy as $ausschuss)
{
	$hodnotaplus=getChildValueForLagerKz($seznamSkladu,"aussplus",$ausschuss);
	$hodnotaminus=getChildValueForLagerKz($seznamSkladu,"aussminus",$ausschuss);
	$hodnotainventur=getChildValueForLagerKz($seznamSkladu,"inventurstk",$ausschuss);
	
	$pdf->Cell(15,5,$ausschuss,'0',0,'L',0);
	$obsahBunky = number_format($hodnotaplus-$hodnotaminus,0,',',' ');
	$pdf->Cell(20,5,$obsahBunky,'1',0,'R',0);
	$obsahBunky = number_format($hodnotainventur,0,',',' ');
	$pdf->Cell(35,5,$obsahBunky,'1',0,'R',0);
    $obsahBunky = number_format(($hodnotaplus-$hodnotaminus+$hodnotainventur),0,',',' ');
    $pdf->Cell(15,5,$obsahBunky,'1',1,'R',0);
	$ausschussGesamt+=($hodnotaplus-$hodnotaminus+$hodnotainventur);
}


// celkove soucty pro dil
$pdf->SetFont("FreeSans", "B", 8);
$pdf->Cell(15,5,"Ges.",'TB',0,'L',0);
$obsahBunky = number_format($ausschussGesamt,0,',',' ');
$pdf->Cell(55,5,"",'TB',0,'R',0);
$pdf->Cell(15,5,$obsahBunky,'TB',1,'R',0);

// prehled vyexportovanych ausschussu
$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();

$pdf->Cell(85,5,"Ausschussuebersicht EX",'B',1,'L',1);
$pdf->Cell(15,5,"A-Art",'B',0,'L',1);
$pdf->Cell(20,5,"DLAGERBEW",'B',0,'L',1);
$pdf->Cell(35,5,"DLAGERSTK (Inventur)",'B',0,'L',1);
$pdf->Cell(15,5,"Summe ",'B',1,'L',1);

$ausschussy = array("B2","B4","B6");
$ausschussGesamt=0;
$pdf->SetFont("FreeSans", "", 8);
foreach($ausschussy as $ausschuss)
{
	$hodnotaplus=getChildValueForLagerKz($seznamSkladu,"aussplus",$ausschuss);
	$hodnotaminus=getChildValueForLagerKz($seznamSkladu,"aussminus",$ausschuss);
	$hodnotainventur=getChildValueForLagerKz($seznamSkladu,"inventurstk",$ausschuss);
	
	$pdf->Cell(15,5,$ausschuss,'0',0,'L',0);
	$obsahBunky = number_format($hodnotaplus-$hodnotaminus,0,',',' ');
	$pdf->Cell(20,5,$obsahBunky,'1',0,'R',0);
	$obsahBunky = number_format($hodnotainventur,0,',',' ');
	$pdf->Cell(35,5,$obsahBunky,'1',0,'R',0);
    $obsahBunky = number_format(($hodnotaplus-$hodnotaminus+$hodnotainventur),0,',',' ');
    $pdf->Cell(15,5,$obsahBunky,'1',1,'R',0);
	$ausschussGesamt+=($hodnotaplus-$hodnotaminus+$hodnotainventur);
}

$pdf->SetFont("FreeSans", "B", 8);
$pdf->Cell(15,5,"Ges.",'TB',0,'L',0);
$obsahBunky = number_format($ausschussGesamt,0,',',' ');
$pdf->Cell(55,5,"",'TB',0,'R',0);
$pdf->Cell(15,5,$obsahBunky,'TB',1,'R',0);

// tabulka s prehledem definovanych skladu
$pdf->SetLeftMargin(200);
$pdf->SetY($yStart);
$pdf->Cell(0,5,"Lager Legende",'B',1,'L',1);
$pdf->SetFont("FreeSans", "", 8);
foreach($seznamSkladu as $sklad)
{
	$skladChildNodes = $sklad->childNodes;
	$obsahBunky=getValueForNode($skladChildNodes,"lagerkz");
	$pdf->Cell(15,5,$obsahBunky,'0',0,'L',0);
	$obsahBunky=getValueForNode($skladChildNodes,"beschreibung");
	$pdf->Cell(0,5,$obsahBunky,'0',1,'L',0);
}

if(($bDetails)&&($inventur===TRUE)){
  $vonVorInventur5 = date('Y-m-d H:i:s',strtotime("-5 day",  strtotime($stampVon)));
//  echo "stampvon:".$stampVon;
//  echo "vorinv:".$vonVorInventur5;
  $order = "lager_nach,auftrag_import,abgnr,date_stamp";
  $sqlVor = "select * from dlagerbew where (teil='$dil') and (date_stamp>'$vonVorInventur5') and  (date_stamp<'$stampVon') order by $order";
  $sqlNach = "select * from dlagerbew where (teil='$dil') and (date_stamp>'$stampVon') order by $order";
//  echo "sel vor: ".$sqlVor;
//  echo "sel nach: ".$sqlNach;
  $a = AplDB::getInstance();
  
  $rowVor = $a->getQueryRows($sqlVor);
  $rowNach = $a->getQueryRows($sqlNach);
  
  $cells = array(
      "teil"
      =>array("width"=>20,"label"=>"teil","align"=>"L","ln"=>0),
      "auftrag_import"
      =>array("width"=>25,"label"=>"auftrag_import","align"=>"L","ln"=>0),
      "pal_import"
      =>array("width"=>20,"label"=>"pal_import","align"=>"L","ln"=>0),
      "gut_stk"
      =>array("width"=>15,"label"=>"gut_stk","align"=>"R","ln"=>0),
      "auss_stk"
      =>array("width"=>15,"label"=>"auss_stk","align"=>"R","ln"=>0),
      "lager_von"
      =>array("width"=>20,"label"=>"lager_von","align"=>"L","ln"=>0),
      "lager_nach"
      =>array("width"=>20,"label"=>"lager_nach","align"=>"L","ln"=>0),
      "date_stamp"
      =>array("width"=>35,"label"=>"date_stamp","align"=>"L","ln"=>0),
      "comp_user_accessuser"
      =>array("width"=>45,"label"=>"user","align"=>"L","ln"=>0),
      "abgnr"
      =>array("width"=>0,"label"=>"abgnr","align"=>"R","ln"=>1),
  );
  
  $rowHeight = 4;
  $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
  $pdf->AddPage();
  pageHeader($pdf, 5,$cells);
  if ($rowVor !== NULL) {
      $pdf->Cell(0, $rowHeight, "Bewegungen 5 Tage vor der Inventur ( sort: $order )", '1', 1, 'L');
	foreach ($rowVor as $r) {
	    foreach ($cells as $index => $cell) {
		test_pageoverflow($pdf, $rowHeight);
		$pdf->SetFont("FreeSans", "", 7);
		$pdf->Cell($cell['width'], $rowHeight, $r[$index], '1', $cell['ln'], $cell['align']);
	    }
	}
	$pdf->Ln();
    }
    
   
   
   if ($rowNach !== NULL) {
       $pdf->Cell(0, $rowHeight, "Bewegungen nach der Inventur ( sort: $order )", '1', 1, 'L');
	foreach ($rowNach as $r) {
	    foreach ($cells as $index => $cell) {
		test_pageoverflow($pdf, $rowHeight);
		$pdf->SetFont("FreeSans", "", 7);
		$pdf->Cell($cell['width'], $rowHeight, $r[$index], '1', $cell['ln'], $cell['align']);
	    }
	}
    }
}
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
