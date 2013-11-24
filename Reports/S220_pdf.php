<?php
session_start();

$doc_title = "S220";
$doc_subject = "S220 Report";
$doc_keywords = "S220";

// necham si vygenerovat XML

$parameters=$_GET;
$auftragsnr=$_GET['auftragsnr'];
$teil = $_GET['teil'];

$teil = str_replace("*", "%", $teil);
if($teil=="%") $teil="";

require_once('S220_xml.php');

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

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cells_header = 
array(
"palette" 
=> array ("popis"=>"Palette","sirka"=>20,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"teil" 
=> array ("popis"=>"Teil","sirka"=>30,"ram"=>1,"align"=>"L","radek"=>0,"fill"=>1),
"taetnr" 
=> array ("popis"=>"Tat","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"stk" 
=> array ("popis"=>"Stk","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"auss2" 
=> array ("popis"=>"A(2)","sirka"=>10,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"auss4" 
=> array ("popis"=>"A(4)","sirka"=>10,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"auss6" 
=> array ("popis"=>"A(6)","sirka"=>10,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"sumvzkd" 
=> array ("popis"=>"VzKd","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"sumvzaby" 
=> array ("popis"=>"VzAby","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),
"sumverb" 
=> array ("popis"=>"Verb","sirka"=>15,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),

"fac1"
=> array ("popis"=>"vzkd/verb","sirka"=>12,"ram"=>1,"align"=>"R","radek"=>0,"fill"=>1),

"preis"
=> array ("popis"=>"Preis","sirka"=>0,"ram"=>1,"align"=>"R","radek"=>1,"fill"=>1),
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


$sum_zapati_paleta_array = array(
                                "preis"=>0,
                                "sumvzkd"=>0,
                                "sumvzaby"=>0,
                                "sumverb"=>0,
                                "auss2"=>0,
                                "auss4"=>0,
                                "auss6"=>0,
                                "stkimport"=>0
								);
global $sum_zapati_paleta_array;

$sum_zapati_import_array = array(
                                "preis"=>0,
                                "sumvzkd"=>0,
                                "sumvzaby"=>0,
                                "sumverb"=>0,
                                "auss2"=>0,
                                "auss4"=>0,
                                "auss6"=>0,
                                "stkimport"=>0
								);
global $sum_zapati_import_array;


// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->Cell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}

/**
 * funkce pro vykresleni tela
 * @param <type> $pdfobjekt
 * @param <type> $pole
 * @param <type> $zahlavivyskaradku
 * @param <type> $rgb
 * @param <type> $funkce
 * @param <type> $nodelist
 */
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

        if(array_key_exists("format", $cell)){
            // pokud mam zadan pro policko format, tak ho nastavim
            $pdfobjekt->SetFont("FreeSans", $cell['format'][1], $cell['format'][0]);
        }
        else{
            // nejaky default
            $pdfobjekt->SetFont("FreeSans", "", 8);
        }
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}

/**
 * zahlavi pro paletu
 * @param <type> $pdfobjekt
 * @param <type> $cells
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb 
 */
function zahlavi_paleta($pdfobjekt,$cells,$childs,$vyskaRadku,$rgb){

    $pdfobjekt->SetFont("FreeSans", "B",8);

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $pdfobjekt->Cell(
                        20,
                        $vyskaRadku,
                        "Pal:".getValueForNode($childs,"pal"),
                        'T',
                        0,  // odradkovat
                        'L',
                        1
                       );

    $pdfobjekt->Cell(
                        0,
                        $vyskaRadku,
                        getValueForNode($childs,"teil"),
                        'T',
                        1,  // odradkovat
                        'L',
                        1
                       );
}


/**
 * zapati pro paletu
 * @param <type> $pdfobjekt
 * @param <type> $cells
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 * @param <type> $sumArray 
 */
function zapati_paleta($pdfobjekt,$cells,$childs,$vyskaRadku,$rgb,$sumArray){

    $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    //palnr
    $pdfobjekt->Cell(
                        20
//                        +$cells['abgnr']['sirka']
                        ,$vyskaRadku
                        ,"Pal:".getValueForNode($childs,"pal")
                        ,'0'
                        ,0
                        ,'L'
                        ,1
                    );

//importstk
    $obsah=number_format($sumArray['stkimport'],0,',',' ');
    $pdfobjekt->Cell(
                        30
                        +$cells['abgnr']['sirka']
                        ,$vyskaRadku
                        ,"IMStk: ".$obsah
                        ,'0'
                        ,0
                        ,'L'
                        ,1
                    );

    //gutstk
    $pdfobjekt->Cell(
                        $cells['gutstk']['sirka']
                        ,$vyskaRadku
                        ,""
                        ,'0'
                        ,0
                        ,'L'
                        ,1
                    );

    $typ="auss2";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="auss4";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="auss6";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="sumvzkd";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="sumvzaby";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="sumverb";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="fac1";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,""//$obsah
                        ,'0'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="preis";
    $obsah="";//number_format($sumArray[$typ],4,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'0'
                        ,1
                        ,'R'
                        ,1
                    );

//   $pdfobjekt->Ln();
}


/**
 * zapati pro import
 * @param <type> $pdfobjekt
 * @param <type> $cells
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 * @param <type> $sumArray 
 */
function zapati_import($pdfobjekt,$cells,$childs,$vyskaRadku,$rgb,$sumArray){

    $pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    //palnr
    $pdfobjekt->Cell(
                        50
                        +$cells['abgnr']['sirka']
                        ,$vyskaRadku
                        ,"Summe Import: ".getValueForNode($childs,"auftragsnr")
                        ,'BT'
                        ,0
                        ,'L'
                        ,1
                    );

////importstk
//    $obsah=number_format($sumArray['stkimport'],0,',',' ');
//    $pdfobjekt->Cell(
//                        30
//                        +$cells['abgnr']['sirka']
//                        ,$vyskaRadku
//                        ,"IMStk: ".$obsah
//                        ,'0'
//                        ,0
//                        ,'L'
//                        ,1
//                    );

    //gutstk
    $pdfobjekt->Cell(
                        $cells['gutstk']['sirka']
                        ,$vyskaRadku
                        ,""
                        ,'BT'
                        ,0
                        ,'L'
                        ,1
                    );

    $typ="auss2";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="auss4";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="auss6";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="sumvzkd";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="sumvzaby";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="sumverb";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="fac1";
    if($sumArray['sumverb']<>0)
        $fac = $sumArray['sumvzkd']/$sumArray['sumverb'];
    else
        $fac = 0;
    $obsah=number_format($fac,2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah//$obsah
                        ,'BT'
                        ,0
                        ,'R'
                        ,1
                    );

    $typ="preis";
    $obsah="";//number_format($sumArray[$typ],4,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'BT'
                        ,1
                        ,'R'
                        ,1
                    );

//   $pdfobjekt->Ln();
}

function nuluj_sumy_pole(&$pole)
{
	foreach($pole as $key=>$prvek)
	{
		$pole[$key]=0;
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

// funkce pro vykresleni zapati
function zapati($pdfobjekt,$pole,$zapativyskaradku,$rgb,$funkce)
{
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	foreach($pole as $cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format($cell[$funkce], $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=$cell[$funkce];
		}

		$pdfobjekt->Cell($cell["sirka"],$zapativyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
}


function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,8);
	}
}
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

// posl

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S220 - Leistung Auftrag - Palette Import", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$barvaZahlaviPaleta = array(240,240,240);
$barvaZahlaviImport = array(240,240,255);

// prvni stranka
$pdf->AddPage();
pageheader($pdf,$cells_header,8);

// zacnu po importech
$importy = $domxml->getElementsByTagName("auftrag");
foreach($importy as $import){
    $importChilds = $import->childNodes;
    nuluj_sumy_pole($sum_zapati_import_array);
    // zahlavi pro import
    $palety = $import->getElementsByTagName('palette');
    foreach($palety as $paleta){
        $paletaChilds = $paleta->childNodes;
        // zahlavi pro paletu
        test_pageoverflow($pdf, 5, $cells_header);
        zahlavi_paleta($pdf, $cells, $paletaChilds, 5,$barvaZahlaviPaleta);
        nuluj_sumy_pole($sum_zapati_paleta_array);
        $taetigkeiten = $paleta->getElementsByTagName('taetigkeit');
        foreach ($taetigkeiten as $taetigkeit){
            $taetigkeitChilds = $taetigkeit->childNodes;
            test_pageoverflow($pdf, 3.5, $cells_header);
            telo($pdf,$cells,3.5,array(255,255,255),"",$taetigkeitChilds);
            foreach($sum_zapati_paleta_array as $key=>$prvek)
            {
    			$hodnota = getValueForNode($taetigkeitChilds,$key);
    			$sum_zapati_paleta_array[$key]+=$hodnota;
    		}
        }
        test_pageoverflow($pdf, 5, $cells_header);
        zapati_paleta($pdf, $cells, $paletaChilds, 5, $barvaZahlaviPaleta, $sum_zapati_paleta_array);
        foreach($sum_zapati_import_array as $key=>$prvek)
        {
           $hodnota=$sum_zapati_paleta_array[$key];
           $sum_zapati_import_array[$key]+=$hodnota;
        }

    }
    test_pageoverflow($pdf, 5, $cells_header);
    zapati_import($pdf, $cells, $importChilds, 5, $barvaZahlaviImport, $sum_zapati_import_array);
}

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
