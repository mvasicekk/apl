<?php
session_start();
require_once "../fns_dotazy.php";

$doc_title = "S1XX1";
$doc_subject = "S1XX1 Report";
$doc_keywords = "S1XX1";

// necham si vygenerovat XML

$parameters=$_GET;
$datum=make_DB_datum($_GET['datum']);
$schicht = trim($_GET['schicht']);
$oe = trim($_GET['oe']);


require_once('S1XX1_xml.php');


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

"dummy"
=> array ("popis"=>"","sirka"=>60,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"anw_von"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"anw_bis"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stunden"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"pause1"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"pause2"
=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"schicht"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"tat"
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"essen"
=> array ("popis"=>"","sirka"=>0,"ram"=>'0',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(
"dummy" 
=> array ("popis"=>"PersNr,Name","sirka"=>60,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"anw_von"
=> array ("popis"=>"od","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"anw_bis"
=> array ("popis"=>"do","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"stunden"
=> array ("nf"=>array(1,',',' '),"popis"=>"hodiny","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"pause1"
=> array ("nf"=>array(1,',',' '),"popis"=>"pause1","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"pause2"
=> array ("nf"=>array(1,',',' '),"popis"=>"pause2","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"schicht"
=> array ("popis"=>"Sch","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),

"tat"
=> array ("popis"=>"tat","sirka"=>15,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),

"essen"
=> array ("popis"=>"essen","sirka"=>0,"ram"=>'B',"align"=>"R","radek"=>1,"fill"=>1),

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


/**
 *
 * @param TCPDF $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 * @param <type> $datumvon
 */
function pageheader($pdfobjekt,$pole,$headervyskaradku,$datumvon)
{
        $time = strtotime($datumvon);
        $denVSekundach = 60*60*24;
        $datumActual = date('d-m-Y', $time);

        // TODO predeat i parametr tagen, ted ho mam napevno nastaven na 6
        $tagen = 6;
        $persnrWidth = 7;
        $vyskaradku = $headervyskaradku / 2;
        $sirkaPersInfo = $persnrWidth+25+5;
        $sirkaDochazkoveBunky = ($pdfobjekt->getPageWidth()-$sirkaPersInfo-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT)/($tagen+1);

	$pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell($sirkaPersInfo, $vyskaradku, '');
        $pdfobjekt->SetFillColor(255,255,200,1);

        $pdfobjekt->SetLineWidth(0.5);
        for($i=0;$i<$tagen;$i++){
            $w = date('w', $time);
            if($w==6 || $w==0)
                $pdfobjekt->SetFillColor(200,200,200,1);
            else
                $pdfobjekt->SetFillColor(255,255,200,1);

            $pdfobjekt->Cell($sirkaDochazkoveBunky, $vyskaradku, $datumActual, 'TLR', 0, 'C', 1);
            $time += $denVSekundach;
            $datumActual = date('d-m-Y', $time);
        }
        // dat. nastupu
        $pdfobjekt->SetLineWidth(0.2);
        $pdfobjekt->SetFillColor(255,255,200,1);
        $pdfobjekt->Cell($sirkaDochazkoveBunky, $vyskaradku, 'dat. nastupu', 'TLR', 1, 'R', 1);

        $time = strtotime($datumvon);
        $dnyArray = array("nedele","pondeli","utery","streda","ctvrtek","patek","sobota");
        $pdfobjekt->Cell($sirkaPersInfo, $vyskaradku, '');
        $pdfobjekt->SetLineWidth(0.5);
        for($i=0;$i<$tagen;$i++){
            $w = date('w', $time);
            if($w==6 || $w==0)
                $pdfobjekt->SetFillColor(200,200,200,1);
            else
                $pdfobjekt->SetFillColor(255,255,200,1);

            $denVTydnu = $dnyArray[date('w', $time)];
            $pdfobjekt->Cell($sirkaDochazkoveBunky, $vyskaradku, $denVTydnu, 'BLR', 0, 'C', 1);
            $time += $denVSekundach;
        }
        // dat. ukonceni
        $pdfobjekt->SetLineWidth(0.2);
        $pdfobjekt->SetFillColor(255,255,200,1);
        $pdfobjekt->Cell($sirkaDochazkoveBunky, $vyskaradku, 'dat. ukonceni', 'TLR', 1, 'R', 1);

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
        $pdfobjekt->SetLineWidth(0.2);
}


// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
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
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
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
/**
 *
 * @param TCPDF $pdfobjekt
 * @param int $vyskaradku
 * @param array $rgb
 * @param nodelist $childs
 * @param int $tagen pocet dnu pro ktere budu kreslit policka, default = 6
 */
function radek_person($pdfobjekt,$vyskaradku,$rgb,$childs,$datvon,$tagen=6)
{

        $time = strtotime($datvon);
        $denVSekundach = 60*60*24;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $persnr = getValueForNode($childs, 'persnr');
        $name = getValueForNode($childs, 'name');
        $marke = getValueForNode($childs, 'marke');
        $ort = getValueForNode($childs, 'ort');
        $tel = getValueForNode($childs, 'tel');
        $eintritt = substr(getValueForNode($childs, 'eintritt'),0,10);
        $austritt = substr(getValueForNode($childs, 'austritt'),0,10);

        $persnrWidth = 7;
        $vyskaradku = $vyskaradku / 2;
        $sirkaPersInfo = $persnrWidth+25+5;
        $sirkaDochazkoveBunky = ($pdfobjekt->getPageWidth()-$sirkaPersInfo-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT)/($tagen+1);
        $tretinaBunky = $sirkaDochazkoveBunky/3;

        // 1. radek se jmenem, znamkou ....
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell($persnrWidth,$vyskaradku,$persnr,'T',0,'R',$fill);
        $pdfobjekt->Cell(25,$vyskaradku,$name,'T',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(5,$vyskaradku,$marke,'T',0,'R',$fill);
        for($i=0;$i<$tagen;$i++){
            // TODO jak nastavit sirku oramovani bunky ?
            //zapamatova pozici X
            $w = date('w', $time);
            if($w==6 || $w==0){
                $pdfobjekt->SetFillColor(200,200,200,1);
            }
            else{
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }

            $xOld = $pdfobjekt->GetX();
            $yOld = $pdfobjekt->GetY();
            $pdfobjekt->SetLineWidth(0.5);
            $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,'','LTR',0,'R',$fill);
            $xActual = $pdfobjekt->GetX();
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->Line($xOld, $yOld+$vyskaradku, $xActual, $yOld+$vyskaradku);
            $time += $denVSekundach;
        }
        // eintritt
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,$eintritt,'LTR',0,'R',$fill);
        $pdfobjekt->Ln();

        // 2.radek
        $time = strtotime($datvon);
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $pdfobjekt->Cell($persnrWidth,$vyskaradku,'','0',0,'R',$fill);
        $pdfobjekt->Cell(10,$vyskaradku,$ort,'0',0,'L',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,$tel,'0',0,'L',$fill);
        for($i=0;$i<$tagen;$i++){
            // TODO jak nastavit sirku oramovani bunky ?
            $w = date('w', $time);
            if($w==6 || $w==0){
                $pdfobjekt->SetFillColor(200,200,200,1);
            }
            else{
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }

            $xOld = $pdfobjekt->GetX();
            $yOld = $pdfobjekt->GetY();
            $pdfobjekt->SetLineWidth(0.5);
            $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,'','LBR',0,'R',$fill);
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->Line($xOld+2*$tretinaBunky, $yOld-$vyskaradku, $xOld+2*$tretinaBunky, $yOld+$vyskaradku);
            $time += $denVSekundach;
        }
        // austritt
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,$austritt,'LTRB',0,'R',$fill);
        $pdfobjekt->Ln();
}


function radek_person_empty($pdfobjekt,$vyskaradku,$rgb,$childs,$datvon,$tagen=6)
{

        $time = strtotime($datvon);
        $denVSekundach = 60*60*24;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $persnr = '';//getValueForNode($childs, 'persnr');
        $name = '';//getValueForNode($childs, 'name');
        $marke = '';//getValueForNode($childs, 'marke');
        $ort = '';//getValueForNode($childs, 'ort');
        $tel = '';//getValueForNode($childs, 'tel');
        $eintritt = '';//substr(getValueForNode($childs, 'eintritt'),0,10);
        $austritt = '';//substr(getValueForNode($childs, 'austritt'),0,10);

        $persnrWidth = 7;
        $vyskaradku = $vyskaradku / 2;
        $sirkaPersInfo = $persnrWidth+25+5;
        $sirkaDochazkoveBunky = ($pdfobjekt->getPageWidth()-$sirkaPersInfo-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT)/($tagen+1);
        $tretinaBunky = $sirkaDochazkoveBunky/3;

        // 1. radek se jmenem, znamkou ....
        $pdfobjekt->SetFont("FreeSans", "B", 7);
        $pdfobjekt->Cell($persnrWidth,$vyskaradku,$persnr,'T',0,'R',$fill);
        $pdfobjekt->Cell(25,$vyskaradku,$name,'T',0,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(5,$vyskaradku,$marke,'T',0,'R',$fill);
        for($i=0;$i<$tagen;$i++){
            // TODO jak nastavit sirku oramovani bunky ?
            //zapamatova pozici X
            $w = date('w', $time);
            if($w==6 || $w==0){
                $pdfobjekt->SetFillColor(200,200,200,1);
            }
            else{
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }

            $xOld = $pdfobjekt->GetX();
            $yOld = $pdfobjekt->GetY();
            $pdfobjekt->SetLineWidth(0.5);
            $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,'','LTR',0,'R',$fill);
            $xActual = $pdfobjekt->GetX();
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->Line($xOld, $yOld+$vyskaradku, $xActual, $yOld+$vyskaradku);
            $time += $denVSekundach;
        }
        // eintritt
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,$eintritt,'LTR',0,'R',$fill);
        $pdfobjekt->Ln();

        // 2.radek
        $time = strtotime($datvon);
        $pdfobjekt->SetFont("FreeSans", "", 6);
        $pdfobjekt->Cell($persnrWidth,$vyskaradku,'','0',0,'R',$fill);
        $pdfobjekt->Cell(10,$vyskaradku,$ort,'0',0,'L',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,$tel,'0',0,'L',$fill);
        for($i=0;$i<$tagen;$i++){
            // TODO jak nastavit sirku oramovani bunky ?
            $w = date('w', $time);
            if($w==6 || $w==0){
                $pdfobjekt->SetFillColor(200,200,200,1);
            }
            else{
                $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
            }

            $xOld = $pdfobjekt->GetX();
            $yOld = $pdfobjekt->GetY();
            $pdfobjekt->SetLineWidth(0.5);
            $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,'','LBR',0,'R',$fill);
            $pdfobjekt->SetLineWidth(0.2);
            $pdfobjekt->Line($xOld+2*$tretinaBunky, $yOld-$vyskaradku, $xOld+2*$tretinaBunky, $yOld+$vyskaradku);
            $time += $denVSekundach;
        }
        // austritt
        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
        $pdfobjekt->Cell($sirkaDochazkoveBunky,$vyskaradku,$austritt,'LTRB',0,'R',$fill);
        $pdfobjekt->Ln();
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead,$datumvon)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku,$datumvon);
	}
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S1XX1 dochazkovy list ".$params, $params);
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
$pdf->AddPage();
pageheader($pdf,$cells_header,10,$datum);


$personen=$domxml->getElementsByTagName("person");
foreach($personen as $person)
{
        $personChilds = $person->childNodes;
	test_pageoverflow($pdf,10,$cells_header,$datum);
	radek_person($pdf,10,array(255,255,255),$personChilds,$datum);
}

// a jeste par radku pradznych
$pocetPrazdnychRadku = 10;
for($i=0;$i<$pocetPrazdnychRadku;$i++)
{
	test_pageoverflow($pdf,10,$cells_header,$datum);
	radek_person_empty($pdf,10,array(255,255,255),$personChilds,$datum);
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
