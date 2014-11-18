<?php
require_once '../security.php';
require_once "../fns_dotazy.php";

$doc_title = "T003";
$doc_subject = "T003 Report";
$doc_keywords = "T003";

// necham si vygenerovat XML

$parameters=$_GET;

$kunde = $_GET['kunde'];
$minpreis_alt = $_GET['preis_alt'];
$minpreis_neu = $_GET['preis_neu'];

//$fullAccess = testReportPassword("S169",$password,$user,0);
//
//if(!$fullAccess)
//{
//    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
//    exit;
//}


require_once('T003_xml.php');

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
    'abgnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'dtaetkz'=> array ("popis"=>"","sirka"=>10,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    'statnr'=> array ("popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>0),
    'vzkd_alt'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'vzkd_alt_ber'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'vzkd_neu'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'vzkd_neu_ber'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'preis_alt'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'preis_alt_ber'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'preis_neu'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'preis_neu_ber'=> array ("nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'koef_gew_alt'=> array ("nf"=>array(5,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'koef_gew_alt_ber'=> array ("nf"=>array(5,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'koef_brgew_alt'=> array ("nf"=>array(5,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'koef_gew_neu'=> array ("nf"=>array(5,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'koef_gew_neu_ber'=> array ("nf"=>array(5,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>0),
    'koef_brgew_neu'=> array ("nf"=>array(5,',',' '),"popis"=>"","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>0),
 );


$cells_header = 
array(
    'dummy'=> array ("popis"=>"\n","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>1),
    'abgnr'=> array ("popis"=>"\nabgnr","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'dtaetkz'=> array ("popis"=>"\nReKZ","sirka"=>10,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
    'statnr'=> array ("popis"=>"\nstatnr","sirka"=>15,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),
    'vzkd_alt'=> array ("popis"=>"vzkd\nalt","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'vzkd_alt_ber'=> array ("popis"=>"vzkd\nalt ber","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'vzkd_neu'=> array ("popis"=>"vzkd\nneu","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'vzkd_neu_ber'=> array ("popis"=>"vzkd\nneu ber","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'preis_alt'=> array ("popis"=>"preis\nalt","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'preis_alt_ber'=> array ("popis"=>"preis\nalt ber","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'preis_neu'=> array ("popis"=>"preis\nneu","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'preis_neu_ber'=> array ("popis"=>"preis\nneu ber","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'koef_gew_alt'=> array ("popis"=>"Preis/kg\nalt [Netto]","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'koef_gew_alt_ber'=> array ("popis"=>"Preis/kg\nalt ber[Netto]","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'koef_brgew_alt'=> array ("popis"=>"Preis/kg\nalt [Brutto]","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'koef_gew_neu'=> array ("popis"=>"Preis/kg\nneu [Netto]","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'koef_gew_neu_ber'=> array ("popis"=>"Preis/kg\nneu [Netto]","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'koef_brgew_neu'=> array ("popis"=>"Preis/kg\nneu [Brutto]","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),
    'dummy2'=> array ("popis"=>"\n","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1),
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
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


function detail($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$nodelist){
    global $cells;
    $pdfobjekt->SetFont("FreeSans", "", 8);
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->SetFillColor(255,200,200,1);
    // odsazeni
    $pdfobjekt->Cell(20,$zahlavivyskaradku,'','0',0,'L',0);

    $statnr = getValueForNode($nodelist, 'statnr');
    $abgnr = getValueForNode($nodelist, 'abgnr');

    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'abgnr';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);

    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'dtaetkz';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'statnr';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'vzkd_alt';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'vzkd_alt_ber';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0061')||(($statnr=='S0061')&&($abgnr<1000 || $abgnr>=2000))) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'vzkd_neu';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'vzkd_neu_ber';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0061')||(($statnr=='S0061')&&($abgnr<1000 || $abgnr>=2000))) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'preis_alt';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'preis_alt_ber';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0061')||(($statnr=='S0061')&&($abgnr<1000 || $abgnr>=2000))) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'preis_neu';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'preis_neu_ber';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0061')||(($statnr=='S0061')&&($abgnr<1000 || $abgnr>=2000))) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'koef_gew_alt';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0041')&&($statnr!='S0061')||((($statnr=='S0041')||($statnr=='S0061'))&&($abgnr>=2000))||($statnr=='S0061')&&($abgnr==59)) $cellobsah='';
    if(($statnr=='S0041')&&($abgnr<10)) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'koef_gew_alt_ber';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0041')&&($statnr!='S0061')||((($statnr=='S0041')||($statnr=='S0061'))&&($abgnr>=2000))||($statnr=='S0061')&&($abgnr==59)) $cellobsah='';
    if(($statnr!='S0061')||(($statnr=='S0061')&&($abgnr<1000 || $abgnr>=2000))) $cellobsah='';
    if(($statnr=='S0041')&&($abgnr<10)) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);

    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'koef_brgew_alt';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0041')&&($statnr!='S0061')||((($statnr=='S0041')||($statnr=='S0061'))&&($abgnr>=2000))||($statnr=='S0061')&&($abgnr==59)) $cellobsah='';
    if(($statnr=='S0041'||$statnr=='S0061')&&($abgnr>10)) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'koef_gew_neu';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0041')&&($statnr!='S0061')||((($statnr=='S0041')||($statnr=='S0061'))&&($abgnr>=2000))||($statnr=='S0061')&&($abgnr==59)) $cellobsah='';
    if(($statnr=='S0041')&&($abgnr<10)) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);
    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'koef_gew_neu_ber';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0041')&&($statnr!='S0061')||((($statnr=='S0041')||($statnr=='S0061'))&&($abgnr>=2000))||($statnr=='S0061')&&($abgnr==59)) $cellobsah='';
    if(($statnr!='S0061')||(($statnr=='S0061')&&($abgnr<1000 || $abgnr>=2000))) $cellobsah='';
    if(($statnr=='S0041')&&($abgnr<10)) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],0,$cell["align"],$cell["fill"]);

    //-------------------------------------------------------------------------------------------------------------
    $nodename = 'koef_brgew_neu';
    $cell = $cells[$nodename];
    if(array_key_exists("nf",$cell))
    {
        $cellobsah =
	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
    }
    else
    {
        $cellobsah=getValueForNode($nodelist,$nodename);
    }
    if(($statnr!='S0041')&&($statnr!='S0061')||((($statnr=='S0041')||($statnr=='S0061'))&&($abgnr>=2000))||($statnr=='S0061')&&($abgnr==59)) $cellobsah='';
    if(($statnr=='S0041'||$statnr=='S0061')&&($abgnr>10)) $cellobsah='';
    $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],1,$cell["align"],$cell["fill"]);

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
		
		//$cellobsah=iconv("windows-1250","UTF-8",$cellobsah);
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
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$childs)
{
        global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $teil = getValueForNode($childs, 'teil');
        $teilbez = getValueForNode($childs, 'teilbez');
        $gew = floatval(getValueForNode($childs, 'gew'));
        $brgew = floatval(getValueForNode($childs, 'brgew'));
        $apl_stamp = getValueForNode($childs, 'apl_stamp');
        $drueck_stamp = getValueForNode($childs, 'drueck_stamp');

        $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(20,$vyskaradku,$teil,'1',0,'L',$fill);
        $pdfobjekt->Cell(35,$vyskaradku,$teilbez,'1',0,'L',$fill);
        $pdfobjekt->Cell(25,$vyskaradku,  'NettoGew [kg]','LBT',0,'L',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,  number_format($gew,3),'RBT',0,'R',$fill);
        $pdfobjekt->Cell(25,$vyskaradku,  'BruttoGew [kg]','LBT',0,'L',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,  number_format($brgew,3),'RBT',0,'R',$fill);
        $pdfobjekt->Cell(30,$vyskaradku,  'APL Datum:','LBT',0,'L',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,  $apl_stamp,'RBT',0,'L',$fill);
        $pdfobjekt->Cell(30,$vyskaradku,  'Drueck Datum:','LBT',0,'L',$fill);
        $pdfobjekt->Cell(20,$vyskaradku,  $drueck_stamp,'RBT',0,'L',$fill);
        $pdfobjekt->Cell(0,$vyskaradku,  '','RBTL',1,'L',$fill);
//        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_schicht($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1,$fac2,$fac3)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(70,$vyskaradku,$popis,'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['anwesenheit'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$fac1;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac2;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac3;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
	
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,3.5);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
	}
}
				
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "T003 Minpreis aendern Stat", $params);
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
pageheader($pdf,$cells_header,5);


$teile=$domxml->getElementsByTagName("teil1");
foreach($teile as $teil)
{
    $teilChilds = $teil->childNodes;
    test_pageoverflow($pdf,5,$cells_header);
    zahlavi_teil($pdf, 5, array(230,230,230), $teilChilds);
    $taetigkeiten = $teil->getElementsByTagName("taetigkeit");
    foreach($taetigkeiten as $taetigkeit){
        $taetigkeitChilds = $taetigkeit->childNodes;
        test_pageoverflow($pdf,5,$cells_header);
        detail($pdf,$cells,5,array(255,255,255),$taetigkeitChilds);
    }
}



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
