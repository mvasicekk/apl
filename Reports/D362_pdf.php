<?php
session_start();
require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "D362";
$doc_subject = "D362 Report";
$doc_keywords = "D362";

// necham si vygenerovat XML

$a = AplDB::getInstance();

$parameters=$_GET;

$date_von=$a->make_DB_datum($_GET['date_von']);
$date_bis=$a->make_DB_datum($_GET['date_bis']);
$kunde=$_GET['kunde'];

$reporttyp = $_GET['reporttyp'];
$datumtyp = $_GET['datumtyp'];

require_once('D362_xml.php');

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

"teil" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"auftragsnr" 
=> array ("popis"=>"","sirka"=>25,"ram"=>'0',"align"=>"L","radek"=>0,"fill"=>0),

"auss" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss_celkem" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gut_stk" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"procent" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"a50kg" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),

"gkg" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    
"lf" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0),


);


$summenTeilArray = array();
$summenKgTeilArray = array();
$summenKgBerichtArray = array();
$summenBerichtArray = array();

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
 * @param array $aussArtenArray
 * @param int $vyskaradku
 * @param array $rgb
 */
function pageHeader($pdfobjekt,$aussArtenArray,$vyskaradku,$rgb){
        global $cells;
        global $reporttyp;

    	$pdfobjekt->SetFont("FreeSans", "B", 8.2);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

        //teil
        $pdfobjekt->Cell($cells['teil']['sirka'],$vyskaradku,'Teil','0',0,'L',1);

        //auftragsnr
        $pdfobjekt->Cell($cells['auftragsnr']['sirka'],$vyskaradku,$reporttyp,'0',0,'L',1);

        //aussarten
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtenArray as $aussart){
            $pdfobjekt->Cell($sirka,$vyskaradku,$aussart,'0',0,'R',1);
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-count($aussArtenArray)*$sirka-$cells['auss_celkem']['sirka']-$cells['gut_stk']['sirka']-$cells['procent']['sirka'];
        $sirkavyplne-= $cells['a50kg']['sirka']+$cells['gkg']['sirka'];
        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','0',0,'L',1);

        //summeAuss
        $pdfobjekt->Cell($cells['auss_celkem']['sirka'],$vyskaradku,'Auss','0',0,'R',1);
        
        //GStk
        $pdfobjekt->Cell($cells['gut_stk']['sirka'],$vyskaradku,'GStk','0',0,'R',1);

        //%Auss
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,'%Auss','0',0,'R',1);

        //a50kg
        $pdfobjekt->Cell($cells['a50kg']['sirka'],$vyskaradku,'A50[kg]','0',0,'R',1);

        //gkg
        $pdfobjekt->Cell($cells['gkg']['sirka'],$vyskaradku,'Gut[kg]','0',0,'R',1);

        $pdfobjekt->Ln();
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function radek_auftrag($pdfobjekt,$vyskaradku,$rgb,$childs,$aussArtArray,$auftragNode,$teilChilds)
{
        global $cells;
        global $summenTeilArray;
        global $summenKgTeilArray;
        global $summenKgBerichtArray;
	global $summenBerichtArray;
        
        $aussartenNodes = $auftragNode->getElementsByTagName('aussart');
        $teilnr = getValueForNode($teilChilds, 'teilnr');
        $gew = floatval(getValueForNode($teilChilds, 'netto_gew'));

        $summeAuss = 0;
        $summeAuss50 = 0;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "", 8);

        // dummy for teil
        $pdfobjekt->Cell($cells['teil']['sirka'],$vyskaradku,'','0',0,'L',$fill);

        //auftrag
        $auftragsnr = getValueForNode($childs, 'auftragsnr');
	$pdfobjekt->Cell($cells['auftragsnr']['sirka'],$vyskaradku,$auftragsnr,'0',0,'L',$fill);

	//aussarten
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtArray as $aussart){
            $value = 0;
            foreach($aussartenNodes as $aussA){
                $aussAA = getValueForNode($aussA->childNodes, 'auss_art');
                if($aussAA==$aussart){
                    $value = intval (getValueForNode($aussA->childNodes, 'auss_stk'));
                    break;
                }
            }
            
            $summeAuss += $value;
            if($aussart==50) $summeAuss50 += $value;
            $summenTeilArray[$teilnr][$aussart] += $value;
	    $summenBerichtArray[$aussart] += $value;
	    $summenKgBerichtArray[$aussart] += ($value*$gew);
            $pdfobjekt->Cell($sirka,$vyskaradku,$value,'0',0,'R',$fill);
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-count($aussArtArray)*$sirka
                        -$cells['auss_celkem']['sirka']
                        -$cells['gut_stk']['sirka']
                        -$cells['procent']['sirka']
                        -$cells['a50kg']['sirka']
                        -$cells['gkg']['sirka'];
        
        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','0',0,'L',$fill);

        //summeAuss
        $pdfobjekt->Cell($cells['auss_celkem']['sirka'],$vyskaradku,$summeAuss,'1',0,'R',$fill);

        //GStk
        $gutStk = getValueForNode($childs, 'gut_stk');
        $summenTeilArray[$teilnr]['gutstk'] += intval($gutStk);
	$summenBerichtArray['gutstk'] += intval($gutStk);
        $pdfobjekt->Cell($cells['gut_stk']['sirka'],$vyskaradku,$gutStk,'1',0,'R',$fill);

        //%Auss
        $prozent = 0;
        if($gutStk!=0) $prozent = $summeAuss/$gutStk*100;
        if($prozent==0)
            $prozent = '';
        else
            $prozent = number_format($prozent,1,',',' ');
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,$prozent,'1',0,'R',$fill);

        //a50kg
        $obsah = $summeAuss50*$gew;
        $summenKgTeilArray[$teilnr]['a50kg']+=$obsah;
        $summenKgBerichtArray['a50kg']+=$obsah;
        $obsah = number_format($obsah, 0, ',', ' ');
//        $pdfobjekt->Cell($cells['a50kg']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        //gkg
        $obsah = $gutStk*$gew;
        $summenKgTeilArray[$teilnr]['gkg']+=$obsah;
        $summenKgBerichtArray['gkg']+=$obsah;

        $obsah = number_format($obsah, 0, ',', ' ');
//        $pdfobjekt->Cell($cells['gkg']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        $pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$vyskaradku,$rgb,$teilnr,$summenArray,$aussArtArray)
{
        global $cells;
        global $summenKgTeilArray;

        $summeAuss = 0;
        $summeProzent = 0;

        $prozentArray = array();
        $gutStk = $summenArray[$teilnr]['gutstk'];
        $a50kg = $summenKgTeilArray[$teilnr]['a50kg'];
        $gkg = $summenKgTeilArray[$teilnr]['gkg'];
        
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);

        // teil
        $pdfobjekt->Cell($cells['teil']['sirka']+$cells['auftragsnr']['sirka'],$vyskaradku,'Summe Teil ('.$teilnr.')','0',0,'L',$fill);

	//aussarten
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtArray as $aussart){
            $value = $summenArray[$teilnr][$aussart];
            $summeAuss += $value;
            $pdfobjekt->Cell($sirka,$vyskaradku,$value,'0',0,'R',$fill);
            if($gutStk!=0) $prozentArray[$aussart] = $value/$gutStk*100;
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-count($aussArtArray)*$sirka-$cells['auss_celkem']['sirka']-$cells['gut_stk']['sirka']-$cells['procent']['sirka'];
        $sirkavyplne-= $cells['a50kg']['sirka']+$cells['gkg']['sirka'];

        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','0',0,'L',$fill);

        //summeAuss
        $pdfobjekt->Cell($cells['auss_celkem']['sirka'],$vyskaradku,$summeAuss,'1',0,'R',$fill);

        //GStk
        $gutStk = $summenArray[$teilnr]['gutstk'];
        $pdfobjekt->Cell($cells['gut_stk']['sirka'],$vyskaradku,$gutStk,'1',0,'R',$fill);

        //%Auss
        $prozent = 0;
        if($gutStk!=0) $prozent = $summeAuss/$gutStk*100;
        $prozent = number_format($prozent,1,',',' ');
        $prozent = '';
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,$prozent,'1',0,'R',$fill);

        //a50kg
        $obsah = number_format($a50kg, 0, ',', ' ');
        $pdfobjekt->Cell($cells['a50kg']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);
        
        //gkg
        $obsah = number_format($gkg, 0, ',', ' ');
        $pdfobjekt->Cell($cells['gkg']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        
        $pdfobjekt->Ln();

        // a jeste radek s procentama
        // teil
        $pdfobjekt->Cell($cells['teil']['sirka']+$cells['auftragsnr']['sirka'],$vyskaradku,'Ausschuss in % von gut Stk','B',0,'L',$fill);

	//aussarten
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtArray as $aussart){
            $value = $prozentArray[$aussart];
            $summeProzent += $value;
//            if($value==0)
//                $value = '';
//            else
            $value = number_format($value,1,',',' ');
            $pdfobjekt->Cell($sirka,$vyskaradku,$value,'B',0,'R',$fill);
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-$cells['auss_celkem']['sirka']-count($aussArtArray)*$sirka;
        $sirkavyplne-= $cells['a50kg']['sirka']+$cells['gkg']['sirka'];
        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','B',0,'L',$fill);
        
        $summeProzent = number_format($summeProzent,1,',',' ');
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,$summeProzent,'1',0,'R',$fill);

        $pdfobjekt->Ln();
        $pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childs)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);

        $teilnr = getValueForNode($childs, 'teilnr');
        $gew = number_format(floatval(getValueForNode($childs, 'netto_gew')),1,',',' ');
	$pdfobjekt->Cell($cells['teil']['sirka'],$vyskaradku,$teilnr,'1',0,'L',$fill);
        $pdfobjekt->Cell($cells['auftragsnr']['sirka'],$vyskaradku,$gew.' kg','1',0,'R',$fill);
        $pdfobjekt->Cell(0,$vyskaradku,'','1',1,'R',$fill);
	
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
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



function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
		return 1;
	}
	else
		return 0;
}

function zapati_sestava($pdfobjekt,$vyskaradku,$rgb,$summenArray,$aussArtArray,$summenBerichtArray)
{
        global $cells;

        $a50kg = $summenArray['a50kg'];
        $gkg = $summenArray['gkg'];
        $gutStk = $summenBerichtArray['gutstk'];
	
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 8);

        // teil
        $pdfobjekt->Cell($cells['teil']['sirka']+$cells['auftragsnr']['sirka'],$vyskaradku,'Summe Bericht [Stk]','0',0,'L',$fill);

	//aussarten
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtArray as $aussart){
	    $value = $summenBerichtArray[$aussart];
	    $obsah = number_format($value,0);
	    $obsah = $value;
            $summeAuss += $value;
            $pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'0',0,'R',$fill);
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-count($aussArtArray)*$sirka-$cells['auss_celkem']['sirka']-$cells['gut_stk']['sirka']-$cells['procent']['sirka'];
        $sirkavyplne-= $cells['a50kg']['sirka']+$cells['gkg']['sirka'];

        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','0',0,'L',$fill);

        //summeAuss
        $pdfobjekt->Cell($cells['auss_celkem']['sirka'],$vyskaradku,$summeAuss,'0',0,'R',$fill);

        //GStk
        //$gutStk = $summenArray[$teilnr]['gutstk'];
        $pdfobjekt->Cell($cells['gut_stk']['sirka'],$vyskaradku,$gutStk,'0',0,'R',$fill);

        //%Auss
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,'','0',0,'R',$fill);

        //a50kg
        $obsah = number_format($a50kg, 0, ',', ' ');
        $pdfobjekt->Cell($cells['a50kg']['sirka'],$vyskaradku,'','0',0,'R',$fill);
        
        //gkg
        $obsah = number_format($gkg, 0, ',', ' ');
        $pdfobjekt->Cell($cells['gkg']['sirka'],$vyskaradku,'','0',0,'R',$fill);

        
        $pdfobjekt->Ln();

	// radek s hmotnostmi
        // teil
        $pdfobjekt->Cell($cells['teil']['sirka']+$cells['auftragsnr']['sirka'],$vyskaradku,'Gewicht [kg]','0',0,'L',$fill);

	//aussarten
	$summeAuss = 0;
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtArray as $aussart){
	    $value = $summenArray[$aussart];
	    $summeAuss += $value;
	    $obsah = number_format($value,0,',',' ');
            $pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'0',0,'R',$fill);
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-count($aussArtArray)*$sirka-$cells['auss_celkem']['sirka']-$cells['gut_stk']['sirka']-$cells['procent']['sirka'];
        $sirkavyplne-= $cells['a50kg']['sirka']+$cells['gkg']['sirka'];

        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','0',0,'L',$fill);

        //summeAuss
	$obsah = number_format($summeAuss,0,',',' ');
        $pdfobjekt->Cell($cells['auss_celkem']['sirka'],$vyskaradku,$obsah,'0',0,'R',$fill);

        //GStk
        //$gutStk = $summenArray[$teilnr]['gutstk'];
        $pdfobjekt->Cell($cells['gut_stk']['sirka'],$vyskaradku,'','0',0,'R',$fill);

        //%Auss
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,'','0',0,'R',$fill);

        //a50kg
        $obsah = number_format($a50kg, 0, ',', ' ');
        $pdfobjekt->Cell($cells['a50kg']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);
        
        //gkg
        $obsah = number_format($gkg, 0, ',', ' ');
        $pdfobjekt->Cell($cells['gkg']['sirka'],$vyskaradku,$obsah,'1',0,'R',$fill);

        
        $pdfobjekt->Ln();

        // a jeste radek s procentama
        // teil
        $pdfobjekt->Cell($cells['teil']['sirka']+$cells['auftragsnr']['sirka'],$vyskaradku,'A Gewicht in %','B',0,'L',$fill);
	$gewGesamt = $summeAuss + $gkg;
	//aussarten
	$value="";
        $sirka = $cells['auss']['sirka'];
        foreach ($aussArtArray as $aussart){
    	    $value = $summenArray[$aussart];
	    if($gewGesamt!=0){
		$value = ($value/$gewGesamt)*100;
		$obsah = number_format($value,2,',',' ');
	    }
	    else
		$obsah = "";
	    
            $pdfobjekt->Cell($sirka,$vyskaradku,$obsah,'B',0,'R',$fill);
        }

        // vypln k sume ausschussu
        $sirkaStranky = $pdfobjekt->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
        $sirkavyplne = $sirkaStranky-$cells['teil']['sirka']-$cells['auftragsnr']['sirka']-$cells['auss_celkem']['sirka']-count($aussArtArray)*$sirka;
        $sirkavyplne-= $cells['a50kg']['sirka']+$cells['gkg']['sirka'];
        $pdfobjekt->Cell($sirkavyplne,$vyskaradku,'','B',0,'L',$fill);
        
        if($gewGesamt!=0) $summeProzent = ($summeAuss/$gewGesamt)*100;
        $summeProzent = number_format($summeProzent,2,',',' ');
        $pdfobjekt->Cell($cells['procent']['sirka'],$vyskaradku,$summeProzent,'1',0,'R',$fill);

        $pdfobjekt->Ln();
        $pdfobjekt->Ln();

	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function test_pageoverflow_nopage($pdfobjekt,$testvysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D362 Teil/Auftrag Ausschussarten", $params);
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

// vytvorim si pole s moznyma auss_artama
$aussarten = $domxml->getElementsByTagName("aussart");
$aussArtenArray = array();
foreach($aussarten as $aussart){
    $aussArtChilds = $aussart->childNodes;
    $aussArt = getValueForNode($aussArtChilds, 'auss_art');
    if($aussArt>0) $aussArtenArray[$aussArt] +=1;
}

$aussArtenArrayKeys = array_keys($aussArtenArray);
sort($aussArtenArrayKeys);

$pdf->AddPage();
pageHeader($pdf,$aussArtenArrayKeys, 5, array(255,255,240));

//
//echo "<pre>";
//var_dump($aussArtenArrayKeys);
//echo "</pre>";

// a ted pujdu po produkt dilech
$teile=$domxml->getElementsByTagName("teil");
foreach($teile as $teil)
{
    $teilChilds = $teil->childNodes;
    if(test_pageoverflow_nopage($pdf, 5)){
        $pdf->AddPage();
        pageHeader($pdf,$aussArtenArrayKeys, 5, array(255,255,240));
    }
    zahlavi_teil($pdf, 5, array(255,255,255), $teilChilds);
    $auftrags = $teil->getElementsByTagName("auftrag");
    foreach($auftrags as $auftrag){
        $auftragChilds = $auftrag->childNodes;
        if(test_pageoverflow_nopage($pdf, 5)){
            $pdf->AddPage();
            pageHeader($pdf,$aussArtenArrayKeys, 5, array(255,255,240));
        }
        radek_auftrag($pdf, 5, array(255,255,255), $auftragChilds, $aussArtenArrayKeys,$auftrag,$teilChilds);
    }

    if(test_pageoverflow_nopage($pdf, 3*5)){
        $pdf->AddPage();
        pageHeader($pdf,$aussArtenArrayKeys, 5, array(255,255,240));
    }
    zapati_teil($pdf, 5, array(255,255,220), getValueForNode($teilChilds, 'teilnr'), $summenTeilArray, $aussArtenArrayKeys);
}

    if(test_pageoverflow_nopage($pdf, 3*5)){
        $pdf->AddPage();
        pageHeader($pdf,$aussArtenArrayKeys, 5, array(255,255,240));
    }
    zapati_sestava($pdf, 5, array(255,255,220), $summenKgBerichtArray, $aussArtenArrayKeys,$summenBerichtArray);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
