<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "D570";
$doc_subject = "D570 Report";
$doc_keywords = "D570";

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



require_once('D570_xml.php');


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
=> array ("popis"=>"","sirka"=>20,"ram"=>'LBR',"align"=>"L","radek"=>0,"fill"=>0),

"teillang"
=> array ("maxchars"=>18,"popis"=>"","sirka"=>25,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"teilbez"
=> array ("maxchars"=>18,"popis"=>"","sirka"=>25,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"gew"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"brgew"
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"mustervom"
=> array ("popis"=>"","sirka"=>13,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"spg"
=> array ("maxchars"=>15,"popis"=>"","sirka"=>7,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"musterplatz"
=> array ("maxchars"=>15,"popis"=>"","sirka"=>14,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"musterfreigabe1vom"
=> array ("popis"=>"","sirka"=>12,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"musterfreigabe1"
=> array ("maxchars"=>10,"popis"=>"","sirka"=>18,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"musterfreigabe2vom"
=> array ("popis"=>"","sirka"=>12,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"musterfreigabe2"
=> array ("maxchars"=>10,"popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"L","radek"=>1,"fill"=>0)

);

$cells_header = 

array(
"teilnr"
=> array ("popis"=>"\nTeilNr","sirka"=>20,"ram"=>'LRTB',"align"=>"L","radek"=>0,"fill"=>1),

"teillang"
=> array ("popis"=>"\nTeilNr - Original","sirka"=>25,"ram"=>'TBR',"align"=>"L","radek"=>0,"fill"=>1),

"teilbez"
=> array ("popis"=>"\nTeilbezeichnung","sirka"=>25,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"gew"
=> array ("nf"=>array(2,',',' '),"popis"=>"Netto\nGewicht","sirka"=>10,"ram"=>'RTB',"align"=>"R","radek"=>0,"fill"=>1),

"brgew"
=> array ("nf"=>array(2,',',' '),"popis"=>"Brutto\nGewicht","sirka"=>10,"ram"=>'RTB',"align"=>"R","radek"=>0,"fill"=>1),

"mustervom"
=> array ("popis"=>"Einlag-\ndatum","sirka"=>13,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"spg"
=> array ("popis"=>"Stk/\nGeh.","sirka"=>7,"ram"=>'RTB',"align"=>"R","radek"=>0,"fill"=>1),

"musterplatz"
=> array ("popis"=>"Lager-\nplatz","sirka"=>14,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"musterfreigabe1vom"
=> array ("popis"=>"Freigabe1\ndatum","sirka"=>12,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"musterfreigabe1"
=> array ("popis"=>"Freigabe1\nName","sirka"=>18,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"musterfreigabe2vom"
=> array ("popis"=>"Freigabe2\ndatum","sirka"=>12,"ram"=>'RTB',"align"=>"L","radek"=>0,"fill"=>1),

"musterfreigabe2"
=> array ("popis"=>"Freigabe2\nName","sirka"=>0,"ram"=>'RTB',"align"=>"L","radek"=>1,"fill"=>1)

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


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 6.5);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
//    $teillang = getValueForNode($nodelist,"teillang");

	foreach($pole as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}

//        if($nodename=="teilnr")
//            $cellobsah = $cellobsah."   ( $teillang )";

        /**
         * omezeni na pocet znaku
         */
        if(array_key_exists("maxchars", $cell)){
            $maxchars = $cell['maxchars'];
            if(strlen($cellobsah)>$maxchars){
                $new = substr($cellobsah, 0, $maxchars)."...";
                $cellobsah = $new;
            }
        }
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
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
function zapati_sestava($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$childNodes,$pocetPozic,$pocetMustru,$pocetFreigabe)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->SetFont("FreeSans", "B", 7);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(0,$vyskaradku,$popis." ".$pocetPozic.", davon mit Muster : $pocetMustru, mit Freigabe : $pocetFreigabe",'B',0,'L',$fill);
	
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

$beschriftung = "D570 Musterlager";
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
$pocetpozic = 0;
$pocetpozicSMustrem=0;
$pocetpozicSFreigabe1=0;
$pocetpozicSFreigabe2=0;

$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
	$pdf->AddPage();
	pageheader($pdf,$cells_header,4,$kundeChildNodes);

	
	$teile=$kunde->getElementsByTagName("teil");
	
	foreach($teile as $teil)
	{
		$teilChilds=$teil->childNodes;
		telo($pdf,$cells,4,array(255,255,255),"",$teilChilds);
		test_pageoverflow($pdf,4,$cells_header,4,$kundeChildNodes);
        $musterplatz = trim(getValueForNode($teilChilds,"musterplatz"));
        $freigabe1 = trim(getValueForNode($teilChilds,"musterfreigabe1vom"));
        $freigabe2 = trim(getValueForNode($teilChilds,"musterfreigabe2"));
        if(strlen($musterplatz)>0) $pocetpozicSMustrem++;
        if(strlen($freigabe1)>0) $pocetpozicSFreigabe1++;
        if(strlen($freigabe2)>0) $pocetpozicSFreigabe2++;
        $pocetpozicSFreigabe = $pocetpozicSFreigabe1;
        $pocetpozic++;

	}
}
test_pageoverflow($pdf,4,$cells_header,4,$kundeChildNodes);
zapati_sestava($pdf,$import,5,"Positionenanzahl ",array(200,200,255),$kundeChildNodes,$pocetpozic,$pocetpozicSMustrem,$pocetpozicSFreigabe);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
