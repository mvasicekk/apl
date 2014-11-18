<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "D810";
$doc_subject = "D810 Report";
$doc_keywords = "D810";

// necham si vygenerovat XML

$a = AplDB::getInstance();

$parameters=$_GET;

$kdvon=$_GET['kundevon'];
$kdbis=$_GET['kundebis'];

$terminvon=$_GET['terminvon'];
$terminvonDB = $a->make_DB_datetime('00:00', $terminvon);
$terminbis=$_GET['terminbis'];
$terminbisDB = $a->make_DB_datetime('23:59', $terminbis);

$spedvon = $_GET['spedvon'];
$spedbis = $_GET['spedbis'];

$typ = $_GET['typ'];

$k = $_GET['kurs'];

if($k=="aktuell")
    $kurs = number_format($a->getKurs(date('Y-m-d'), 'EUR', 'CZK'), 2);
else
    $kurs = number_format($a->getKurs('2099-12-31', 'EUR', 'CZK'), 2);

require_once('D810_xml.php');


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

$summeDispo = array(
    'preis' => 0,
    'kostencz' => 0,
    'betrag' => 0,
    'kostenEUR' => 0,
    'betrag_minus_kosten' => 0,
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

      $pdfobjekt->SetFont("FreeSans", "B", 6);
      $pdfobjekt->Cell(
                        $cells['vzkd']['sirka']
                        +$cells['vzaby']['sirka']
                        +$cells['sumimportstk']['sirka']
                        //+$cells['sumpreis']['sirka']
                        ,
                        $vyskaRadku,
                        "Muster : ".getValueForNode($childs,"musterplatz"),
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

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "D810 - Rundlauf - $typ", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT-5);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 11));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

//$barvaZahlaviImport = array(200,255,200);
//$barvaZahlaviTeil = array(200,200,255);
//$barvaZapatiSestava = array(200,200,200);
//$barvaTelo = array(255,255,255);
// zacinam po jizdach, ale musim vykreslovat po sloupcich

$fahrten = $domxml->getElementsByTagName("fahrt");
// nejdriv zjistit pocet jizd a zda se mi vejdou na sirku strany
$fahrtenCount = 0;
foreach ($fahrten as $fahrt){
    $fahrtenCount++;
}

$minSirka = 17;
$sumySirka = $minSirka;
$legendaLeftSirka = 47;

$sirkaStrany = $pdf->getPageWidth()-(PDF_MARGIN_LEFT-5+PDF_MARGIN_RIGHT-5);

$sirkaStranyFahrten = $sirkaStrany-$legendaLeftSirka;
$fahrtenNaStranu = round(($sirkaStranyFahrten / $minSirka)+0.5);
$pocetStran = round(($fahrtenCount / $fahrtenNaStranu)+0.5);
$sirkaFahrt = $minSirka;
$vyskaRadek = 5;

// a zacnu po strankach
$jizda = 0;
for ($strana = 1; $strana <= $pocetStran; $strana++) {
    $jizdaInfo = $fahrten->item($jizda);
    if ($jizdaInfo !== NULL) {
	$pdf->AddPage();
	// popisky vlevo
	$fill = 0;
	$left = $pdf->GetX() + $legendaLeftSirka;
	$top = $pdf->GetY();
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'EX', 'LBT', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'IM', 'LBT', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Abfahrt Abydos - Ort', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Abfahrt Abydos - Soll Tag', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Abfahrt Abydos - Soll Zeit', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Abfahrt Abydos - Ist Tag', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Abfahrt Abydos - Ist Zeit', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Proforma', 'TL', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Spediteur', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Fahrersname', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'LKW-Nr.', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Kunde - Ort', 'TL', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Kunde - Soll Tag', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Kunde - Soll Zeit', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Kunde - Ist Tag', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Kunde - Ist Zeit', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Abydos - Ort', 'TL', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Abydos - Soll Tag', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Abydos - Soll Zeit', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Abydos - Ist Tag', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Abydos - Ist Zeit', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Ankunft Abydos - Nutzlast', 'L', 1, 'L', $fill);
	$pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Bemerkung', 'TBL', 1, 'L', $fill);
	if ($typ == 'Dispo') {
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Preis vereinbart HZ', 'BL', 1, 'L', $fill);
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Rabatt [%]', 'BL', 1, 'L', $fill);
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Kosten [CZK]', 'BL', 1, 'L', $fill);
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Betrag [EUR]', 'BL', 1, 'L', $fill);
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Kosten [EUR] *1)', 'BL', 1, 'L', $fill);
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Betrag-Kosten [EUR]', 'BL', 1, 'L', $fill);
	    $pdf->Cell($legendaLeftSirka, $vyskaRadek, 'Rechnung', 'BL', 1, 'L', $fill);
	}
	//
	$pdf->SetY($top);
	// zobrazeni stranek
    }
    $gStranaJizda=0;
    for ($stranaJizda = 1; $stranaJizda <= $fahrtenNaStranu; $stranaJizda++) {
	$jizdaInfo = $fahrten->item($jizda);
	if ($jizdaInfo !== NULL) {
	    $gStranaJizda++;
	    $left = $legendaLeftSirka + ($stranaJizda - 1) * $minSirka;

	    $jizdaChilds = $jizdaInfo->childNodes;

	    $pdf->SetX($left);
	    $ex = getValueForNode($jizdaChilds, 'ex');
	    $pdf->Cell($minSirka, $vyskaRadek, $ex, '1', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'im');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, '1', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'ab_aby_ort');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = date('y-m-d', strtotime(getValueForNode($jizdaChilds, 'ab_aby_soll_datetime')));
	    if (intval(date('Y', strtotime(getValueForNode($jizdaChilds, 'ab_aby_soll_datetime')))) < 2013)
		$im = "";
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = date('H:i', strtotime(getValueForNode($jizdaChilds, 'ab_aby_soll_datetime')));
	    if (intval(date('Y', strtotime(getValueForNode($jizdaChilds, 'ab_aby_soll_datetime')))) < 2013)
		$im = "";
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'ab_aby_ist_datetime'));
	    $im = $t <= 0 ? "" : date('y-m-d', $t);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'ab_aby_ist_datetime'));
	    $im = $t <= 0 ? "" : date('H:i', $t);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'proforma');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TLR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'spedname');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'fahrername');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'lkw_kz');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'BLR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = substr(getValueForNode($jizdaChilds, 'an_kunde_ort'), 0, 12);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TLR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_kunde_soll_datetime'));
	    $im = $t <= 0 ? "" : date('y-m-d', $t);
	    if (intval(date('Y', strtotime(getValueForNode($jizdaChilds, 'an_kunde_soll_datetime')))) < 2013)
		$im = "";
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_kunde_soll_datetime'));
	    $im = $t <= 0 ? "" : date('H:i', $t);
	    if (intval(date('Y', strtotime(getValueForNode($jizdaChilds, 'an_kunde_soll_datetime')))) < 2013)
		$im = "";
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_kunde_ist_datetime'));
	    $im = $t <= 0 ? "" : date('y-m-d', $t);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_kunde_ist_datetime'));
	    $im = $t <= 0 ? "" : date('H:i', $t);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'an_aby_ort');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TLR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_aby_soll_datetime'));
	    $im = $t <= 0 ? "" : date('y-m-d', $t);
	    if (intval(date('Y', strtotime(getValueForNode($jizdaChilds, 'an_aby_soll_datetime')))) < 2013)
		$im = "";
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_aby_soll_datetime'));
	    $im = $t <= 0 ? "" : date('H:i', $t);
	    if (intval(date('Y', strtotime(getValueForNode($jizdaChilds, 'an_aby_soll_datetime')))) < 2013)
		$im = "";
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_aby_ist_datetime'));
	    $im = $t <= 0 ? "" : date('y-m-d', $t);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $t = strtotime(getValueForNode($jizdaChilds, 'an_aby_ist_datetime'));
	    $im = $t <= 0 ? "" : date('H:i', $t);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'LR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'an_aby_nutzlast');
	    $im = number_format($im, 1, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'BLR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = getValueForNode($jizdaChilds, 'bemerkung');
	    $pdf->SetFont("FreeSans", "", 6);
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'L', 0);
	    $pdf->SetFont("FreeSans", "", 8);


	    if ($typ == "Dispo") {
		$pdf->SetX($left);
		$im = floatval(getValueForNode($jizdaChilds, 'preis'));
		$summeDispo['preis']+=$im;
		$im = number_format($im, 2, ',', ' ');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);

		$pdf->SetX($left);
		$im = getValueForNode($jizdaChilds, 'rabatt');
		$im = number_format(floatval($im), 2, ',', ' ');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);

		$pdf->SetX($left);
		$im = floatval(getValueForNode($jizdaChilds, 'kostencz'));
		$summeDispo['kostencz']+=$im;
		$im = number_format($im, 2, ',', ' ');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);

		$pdf->SetX($left);
		$im = floatval(getValueForNode($jizdaChilds, 'betrag'));
		$summeDispo['betrag']+=$im;
		$im = number_format($im, 2, ',', ' ');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);

		$pdf->SetX($left);
		$im = floatval(getValueForNode($jizdaChilds, 'kostenEUR'));
		$summeDispo['kostenEUR']+=$im;
		$im = number_format($im, 2, ',', ' ');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);

		$pdf->SetX($left);
		$im = floatval(getValueForNode($jizdaChilds, 'betrag_minus_kosten'));
		$summeDispo['betrag_minus_kosten']+=$im;
		$im = number_format($im, 2, ',', ' ');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);


		$pdf->SetX($left);
		$im = getValueForNode($jizdaChilds, 'rechnung');
		$pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);
		
//		$pdf->Ln();$pdf->Ln();
//		$pdf->Cell(0, $vyskaRadek, "*1) ".$kurs." EUR/CZK", '0', 1, 'L', 0);
	    }

	    $jizda++;
	    $pdf->SetY($top);
	}
    }
}

if($typ=="Dispo"){
    
	    $gStranaJizda++;
	    $left = $legendaLeftSirka + ($gStranaJizda - 1) * $minSirka;
	    $pdf->SetX($left);
	    $pdf->Cell($minSirka, $vyskaRadek, "Summe", '1', 1, 'L', 0);
	    $pdf->SetX($left);
	    $pdf->Cell($minSirka, $vyskaRadek, "Zeitraum", '1', 1, 'L', 0);
	    for($i=0;$i<21;$i++){
		$pdf->SetX($left);
		$pdf->Cell($minSirka, $vyskaRadek, "", 'R', 1, 'R', 0);
	    }
	    $pdf->SetFont("FreeSans", "B", 7);
	    $pdf->SetX($left);
	    $im = number_format($summeDispo['preis'], 2, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);
	    
	    $pdf->SetX($left);
	    $rabatt = $summeDispo['preis']!=0?($summeDispo['preis']-$summeDispo['kostencz'])/$summeDispo['preis']*100:0;
	    $im = number_format($rabatt, 2, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);
	    
    	    $pdf->SetX($left);
	    $im = number_format($summeDispo['kostencz'], 2, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);

	    $pdf->SetX($left);
	    $im = number_format($summeDispo['betrag'], 2, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);
	    
	    $pdf->SetX($left);
	    $im = number_format($summeDispo['kostenEUR'], 2, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);
	    
	    $pdf->SetX($left);
	    $im = number_format($summeDispo['betrag_minus_kosten'], 2, ',', ' ');
	    $pdf->Cell($minSirka, $vyskaRadek, $im, 'TBLR', 1, 'R', 0);
	    
	    $pdf->Ln();$pdf->Ln();
	    $pdf->Cell(0, $vyskaRadek, "*1) ".$kurs." EUR/CZK", '0', 1, 'L', 0);
    }
//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
