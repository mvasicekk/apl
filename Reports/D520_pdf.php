<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D520";
$doc_subject = "D520 Report";
$doc_keywords = "D520";

// necham si vygenerovat XML

$parameters=$_GET;

$import=$_GET['import'];

require_once('D520_xml.php');


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

"abgnr"
=> array ("format"=>array(6,''),"popis"=>"","sirka"=>7,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),

"tat" 
=> array ("format"=>array(6,''),"popis"=>"","sirka"=>5,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"kzgut"
=> array ("format"=>array(6,''),"popis"=>"","sirka"=>3,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_d"
=> array ("trim"=>35,"format"=>array(6,''),"popis"=>"","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_t"
=> array ("trim"=>35,"format"=>array(6,''),"popis"=>"","sirka"=>40,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("format"=>array(6,''),"nf"=>array(4,',',' '),"popis"=>"","sirka"=>12,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("format"=>array(6,''),"nf"=>array(2,',',' '),"popis"=>"","sirka"=>12,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"sumimportstk"
=> array ("format"=>array(6,''),"nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"sumpreis"
=> array ("format"=>array(6,''),"nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd"
=> array ("format"=>array(6,''),"nf"=>array(4,',',' '),"popis"=>"","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzaby"
=> array ("format"=>array(6,''),"nf"=>array(2,',',' '),"popis"=>"","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

//"dummy"
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>18,"ram"=>'BR',"align"=>"R","radek"=>1,"fill"=>0),

"lager_von"
=> array ("format"=>array(6,'B'),"popis"=>"","sirka"=>5,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//
"lager_nach"
=> array ("format"=>array(6,'B'),"popis"=>"","sirka"=>5,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//
"bedarf_typ"
=> array ("format"=>array(6,'B'),"popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"R","radek"=>1,"fill"=>0)

);

$cells_header =
array(

"abgnr"
=> array ("format"=>array(6,''),"popis"=>"tatnr","sirka"=>7,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),

"tat"
=> array ("format"=>array(6,''),"popis"=>"kz","sirka"=>5,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"kzgut"
=> array ("format"=>array(6,''),"popis"=>"G","sirka"=>3,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_d"
=> array ("format"=>array(6,''),"popis"=>"bez D","sirka"=>40,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),

"tatbez_t"
=> array ("format"=>array(6,''),"popis"=>"bez T","sirka"=>40,"ram"=>'BR',"align"=>"L","radek"=>0,"fill"=>0),

"vzkd"
=> array ("format"=>array(6,''),"nf"=>array(4,',',' '),"popis"=>"vzkd","sirka"=>12,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby"
=> array ("format"=>array(6,''),"nf"=>array(2,',',' '),"popis"=>"vzaby","sirka"=>12,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"sumimportstk"
=> array ("format"=>array(6,''),"nf"=>array(0,',',' '),"popis"=>"IMStk","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"sumpreis"
=> array ("format"=>array(6,''),"nf"=>array(2,',',' '),"popis"=>"Preis","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzkd"
=> array ("format"=>array(6,''),"nf"=>array(4,',',' '),"popis"=>"Sum-vzkd","sirka"=>15,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),

"sumvzaby"
=> array ("format"=>array(6,''),"nf"=>array(2,',',' '),"popis"=>"Sum-vzaby","sirka"=>15,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),

//"dummy"
//=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>18,"ram"=>'BR',"align"=>"R","radek"=>1,"fill"=>0),

"lager_von"
=> array ("format"=>array(6,'B'),"popis"=>"","sirka"=>5,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//
"lager_nach"
=> array ("format"=>array(6,'B'),"popis"=>"","sirka"=>5,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//
"bedarf_typ"
=> array ("format"=>array(6,'B'),"popis"=>"","sirka"=>0,"ram"=>'BR',"align"=>"R","radek"=>1,"fill"=>0)

);


$sum_zapati_teil_array = array(	
								"vzkd"=>0,
								"vzaby"=>0,
                                "sumimportstk"=>0,
                                "sumpreis"=>0,
                                "sumvzkd"=>0,
                                "sumvzaby"=>0,
                                "sumteilgew"=>0
								);
global $sum_zapati_teil_array;


$sum_zapati_sestava_array = array(
//								"vzkd"=>0,
//								"vzaby"=>0,
                                "sumimportstk"=>0,
                                "sumpreis"=>0,
                                "sumvzkd"=>0,
                                "sumvzaby"=>0,
                                "sumteilgew"=>0
								);
global $sum_zapati_sestava_array;

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
 * vykresleni hlavicky na kazde strance
 * @param <type> $pdfobjekt
 * @param <type> $pole
 * @param <type> $headervyskaradku
 */
function pageheader($pdfobjekt,$pole,$headervyskaradku)
{
	$pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
	foreach($pole as $cell)
	{
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
	}
	$pdfobjekt->Ln();
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 6);
}


/**
 * zahlavi pro import
 * @param <type> $pdfobjekt
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 */
function zahlavi_import($pdfobjekt,$childs,$vyskaRadku,$rgb){

    $pdfobjekt->SetFont("FreeSans", "", 10);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    $pdfobjekt->Cell(0,$vyskaRadku," IMPORT: ".getValueForNode($childs,"importnr"),'1',1,'L',1);
}

/**
 * zahlavi pro dil
 * @param <type> $pdfobjekt
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 */
function zahlavi_teil($pdfobjekt,$cells,$childs,$vyskaRadku,$rgb){

    $pdfobjekt->SetFont("FreeSans", "B",8);

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    $pdfobjekt->Cell(
                        $cells['abgnr']['sirka']
                        +$cells['tat']['sirka']
                        +$cells['kzgut']['sirka']
                        +$cells['tatbez_d']['sirka']
                        +$cells['tatbez_t']['sirka']
                        ,
                        $vyskaRadku,
                        getValueForNode($childs,"teilnr")." ( ".getValueForNode($childs,"teilbez")." ) "."[ ".getValueForNode($childs,"teillang")." ]",
                        '1',
                        0,  // odradkovat
                        'L',
                        1
                       );

	$a = AplDB::getInstance();
	$teilnr = getValueForNode($childs, 'teilnr');
	// dokunrchange
	//$musterRow = $a->getTeilDokument($teilnr, 29, TRUE);
	$musterRow = $a->getTeilDokument($teilnr, AplDB::DOKUNR_MUSTER, TRUE);
	//$musterRow = $a->getTeilDokument($teilnr, 12, TRUE);
	if($musterRow===NULL)
	    $musterText = "Muster: ????";
	else
	    $musterText = $musterRow['doku_nr']." / ".$musterRow['einlag_datum']." / ".$musterRow['musterplatz'];

      $pdfobjekt->SetFont("FreeSans", "B", 6);
      $pdfobjekt->Cell(
                        $cells['vzkd']['sirka']
                        +$cells['vzaby']['sirka']
                        +$cells['sumimportstk']['sirka']
                        //+$cells['sumpreis']['sirka']
                        ,
                        $vyskaRadku,
                        $musterText,
                        '1',
                        0,  // odradkovat
                        'L',
                        1
                       );

      
      //brgew
      $pdfobjekt->Cell(
                        $cells['sumpreis']['sirka']
                        +$cells['sumvzkd']['sirka']
                        //+$cells['sumvzaby']['sirka']
                        ,
                        $vyskaRadku,
                                        "Brutto[kg] : ".number_format(getValueForNode($childs,"brgew"),3),
                        '1',
                        0,  // odradkovat
                        'L',
                        1
                       );
      //gew
      $pdfobjekt->Cell(
//                        $cells['vzkd']['sirka']
//                        +$cells['vzaby']['sirka']
//                        +$cells['sumimportstk']['sirka']
//                        +$cells['sumpreis']['sirka']
                        0
                        ,
                        $vyskaRadku,
                                        "Netto[kg]: ".number_format(getValueForNode($childs,"gew"),3),
                        '1',
                        1,  // odradkovat
                        'L',
                        1
                       );

}

/**
 * zapati pro dil
 * @param <type> $pdfobjekt
 * @param <type> $cells
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 * @param <type> $sumArray
 */
function zapati_teil($pdfobjekt,$cells,$childs,$vyskaRadku,$rgb,$sumArray){

    $pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    //dummy
    $pdfobjekt->Cell(
                        $cells['abgnr']['sirka']
                        +$cells['tat']['sirka']
                        +$cells['tatbez_d']['sirka']
                        //+$cells['tatbez_t']['sirka']
                        +$cells['kzgut']['sirka']
                        ,$vyskaRadku
                        ,getValueForNode($childs,"teilnr")." ( ".getValueForNode($childs,"teilbez")." ) "
                        ,'1'
                        ,0
                        ,'L'
                        ,1
                    );


    //sumteilgew
    $typ="sumteilgew";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells['tatbez_t']['sirka']
                        ,$vyskaRadku
                        ,"SumGew [kg]: ".$obsah
                        ,'LTBR'
                        ,0
                        ,'R'
                        ,1
                    );


    //vzkd
    $typ="vzkd";
    $obsah=number_format($sumArray[$typ],4,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TB'
                        ,0
                        ,'R'
                        ,1
                    );

    //vzaby
    $typ="vzaby";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TBR'
                        ,0
                        ,'R'
                        ,1
                    );

    //sumimportstk
    $typ="sumimportstk";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'1'
                        ,0
                        ,'R'
                        ,1
                    );

    //sumpreis
    $typ="sumpreis";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'1'
                        ,0
                        ,'R'
                        ,1
                    );

    //sumvzkd
    $typ="sumvzkd";
    $obsah=number_format($sumArray[$typ],4,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TB'
                        ,0
                        ,'R'
                        ,1
                    );
    //sumvzaby
    $typ="sumvzaby";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TBR'
                        ,0
                        ,'R'
                        ,1
                    );

    $pdfobjekt->Cell(0,$vyskaRadku,"",'1',1,'L',1);
    $pdfobjekt->Ln();
}

/**
 * zapati pro sestavu
 * @param <type> $pdfobjekt
 * @param <type> $cells
 * @param <type> $childs
 * @param <type> $vyskaRadku
 * @param <type> $rgb
 * @param <type> $sumArray 
 */
function zapati_sestava($pdfobjekt,$cells,$childs,$vyskaRadku,$rgb,$sumArray){

    $pdfobjekt->SetFont("FreeSans", "B", 6);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);

    //dummy
    $pdfobjekt->Cell(
                        $cells['abgnr']['sirka']
                        +$cells['tat']['sirka']
                        +$cells['tatbez_d']['sirka']
                        //+$cells['tatbez_t']['sirka']
                        +$cells['kzgut']['sirka']
                        ,$vyskaRadku
                        ,"Summe IMPORT ".getValueForNode($childs,"importnr")
                        ,'1'
                        ,0
                        ,'L'
                        ,1
                    );

    //sumteilgew
    $typ="sumteilgew";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells['tatbez_t']['sirka']
                        ,$vyskaRadku
                        ,"SumGew [kg]: ".$obsah
                        ,'LTBR'
                        ,0
                        ,'R'
                        ,1
                    );

    //vzkd
    $typ="vzkd";
    $obsah="";//number_format($sumArray[$typ],4,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TB'
                        ,0
                        ,'R'
                        ,1
                    );

    //vzaby
    $typ="vzaby";
    $obsah="";//number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TBR'
                        ,0
                        ,'R'
                        ,1
                    );

    //sumimportstk
    $typ="sumimportstk";
    $obsah=number_format($sumArray[$typ],0,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'1'
                        ,0
                        ,'R'
                        ,1
                    );

    //sumpreis
    $typ="sumpreis";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'1'
                        ,0
                        ,'R'
                        ,1
                    );

    //sumvzkd
    $typ="sumvzkd";
    $obsah=number_format($sumArray[$typ],4,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TB'
                        ,0
                        ,'R'
                        ,1
                    );
    //sumvzaby
    $typ="sumvzaby";
    $obsah=number_format($sumArray[$typ],2,',',' ');
    $pdfobjekt->Cell(
                        $cells[$typ]['sirka']
                        ,$vyskaRadku
                        ,$obsah
                        ,'TBR'
                        ,0
                        ,'R'
                        ,1
                    );

    $pdfobjekt->Cell(0,$vyskaRadku,"",'1',1,'L',1);
    $pdfobjekt->Ln();
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

        if(array_key_exists('trim', $cell)){
            $maxLength = $cell['trim'];
            if(strlen($cellobsah)>$maxLength) $cellobsah = substr ($cellobsah, 0, $maxLength).'...';
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
 *  funkce ktera vrati hodnotu podle nodename
 *  predam ji nodelist a jmeno node ktereho hodnotu hledam
 *
 * @param <type> $nodelist
 * @param <type> $nodename
 * @return <type>
 */
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



function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	}
}
				

function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		//pageheader($pdfobjekt,$cellhead,$vysradku);
		//$pdfobjekt->Ln();
		//$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D520 ARBEITSPLAN PRO AUFTRAG", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT-5);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 13));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

$pdf->AddPage();

$barvaZahlaviImport = array(200,255,200);
$barvaZahlaviTeil = array(200,200,255);
$barvaZapatiSestava = array(200,200,200);
$barvaTelo = array(255,255,255);
// zacinam po importech
// export pro mustertyp=29
$importy=$domxml->getElementsByTagName("import");
foreach($importy as $import)
{
	$importChilds = $import->childNodes;
	zahlavi_import($pdf,$importChilds,5,$barvaZahlaviImport);
	
	// ted jdu po dilech
	$dily=$import->getElementsByTagName("teil");

    foreach($dily as $dil){
        $dilChilds = $dil->childNodes;
        zahlavi_teil($pdf,$cells,$dilChilds,5,$barvaZahlaviTeil);
//        pageheader($pdf, $cells_header, 4);
        $positionen = $dil->getElementsByTagName("position");
        nuluj_sumy_pole($sum_zapati_teil_array);

        foreach($positionen as $pos)
        {
            $posChilds = $pos->childNodes;
            telo($pdf,$cells, 5,$barvaTelo ,"",$posChilds);
            // nascitam casy
            foreach($sum_zapati_teil_array as $key=>$prvek)
            {
    			$hodnota = getValueForNode($posChilds,$key);
    			$sum_zapati_teil_array[$key]+=$hodnota;
    		}
    	}
        zapati_teil($pdf,$cells,$dilChilds,5,$barvaZahlaviTeil,$sum_zapati_teil_array);

        foreach($sum_zapati_sestava_array as $key=>$prvek)
        {
           $hodnota=$sum_zapati_teil_array[$key];
           $sum_zapati_sestava_array[$key]+=$hodnota;
        }
    }
}

zapati_sestava($pdf,$cells,$importChilds,5,$barvaZapatiSestava,$sum_zapati_sestava_array);
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
