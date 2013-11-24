<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S360";
$doc_subject = "S360 Report";
$doc_keywords = "S360";

// necham si vygenerovat XML

$parameters=$_GET;

$a = AplDB::getInstance();

$kundevon=$_GET['kundevon'];
$kundebis=$_GET['kundebis'];
$erhvon = $a->make_DB_datum($_GET['erhvon']);
$erhbis = $a->make_DB_datum($_GET['erhbis']);

$user = $_SESSION['user'];


require_once('S360_xml.php');

//exit;
// vytvorit string s popisem parametru
// parametry mam v XML souboru, tak je jen vytahnu
//parameters
$parameters=$domxml->getElementsByTagName("parameters");


foreach ($parameters as $param)
{
        // N1, N2, N3, N4
	$parametry=$param->childNodes;
	// v ramci parametru si prectu label a hodnotu
	//foreach($parametry as $parametr)
	//{
                // label, value
		//$parametr=$parametr->childNodes;
		//foreach($parametr as $par)
		//{
                //        if($par->nodeValue=="Erhalten am von")
                //                $label="Erhalten am";
                //        else if($par->nodeValue=="Erhalten am bis")
                //                $label=" - ";
                //        else if($par->nodeName=="label")
		//		$label=$par->nodeValue;
		//	else if($par->nodeName=="value")
		//		$value=$par->nodeValue;
		//}
                
                $value1 = $parametry->item(0)->lastChild->nodeValue;
                $value2 = $parametry->item(1)->lastChild->nodeValue;
                $value3 = $parametry->item(2)->lastChild->nodeValue;
                $value4 = $parametry->item(3)->lastChild->nodeValue;
                
                $params =   "Kunde von: "       . $value1 .
                            " Kunde bis: "      . $value2 .
                            " Erhalten am: "    . $value3 .
                            " - "               . $value4 ;
                
		//if(strtolower($label)!="password")
                //   if ($label = " - ");
		//	$params .= $label.": ".$value."  ";
	//}
}


// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2

$cellsMultiZeilen = array(
    
    "rekl_nr" => array(
	"sirka" => 10,
	"zeilen" => array(
	    "1" => array(
		"node" => "rekl_nr",
		"ram" => "LRT",
		"align" => "L",
		"fill" => 0,
	    ),
	    "2" => array(
		"node" => "",
		"ram" => "LR",
		"align" => "L",
		"fill" => 0,
	    ),
	    "3" => array(
		"node" => "",
		"ram" => "LRB",
		"align" => "L",
		"fill" => 0,
	    ),
	),
    ),
    
    "import" => array(
	"sirka" => 8,
	"zeilen" => array(
	    "1" => array(
		"node" => "import",
		"ram" => "LRT",
		"align" => "L",
		"fill" => 0,
	    ),
	    "2" => array(
		"node" => "export",
		"ram" => "LR",
		"align" => "L",
		"fill" => 0,
		"substr"=>array(0,4)
	    ),
	    "3" => array(
		"node" => "export",
		"ram" => "LRB",
		"align" => "L",
		"fill" => 0,
		"substr"=>array(4,4)
	    ),
	),
    ),
);


$cells = 
array(

"rekl_nr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),
    
"import" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),
    
"export" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),
    
"erhalten_am" 
=> array ("popis"=>"","sirka"=>12,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),
    
"erledigt_am" 
=> array ("popis"=>"","sirka"=>12,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),
    
"beschr_abweichung" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),

"beschr_ursache" 
=> array ("popis"=>"","sirka"=>30,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),    
    
"teil" 
=> array ("popis"=>"","sirka"=>12,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>0),

"gewicht" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>8,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"stk_expediert" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"stk_reklammiert" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"interne_bewertung" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
////    
"anerkannt_stk_ausschuss" 
    => array ("popis"=>"","sirka"=>6,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
    "anerkannt_gew_ausschuss" 
    => array ("popis"=>"","sirka"=>6,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),

    "anerkannt_wert_ausschuss" 
    => array ("popis"=>"","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),

////
"anerkannt_stk_nacharbeit" 
    => array ("popis"=>"","sirka"=>6,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),    
    
    "anerkannt_gew_nacharbeit" 
    => array ("popis"=>"","sirka"=>6,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
    "anerkannt_wert_nacharbeit" 
    => array ("popis"=>"","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
////
"anerkannt_stk_nein" 
    => array ("popis"=>"","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),    
    
    "anerkannt_gew_nein" 
    => array ("popis"=>"","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"andere_kosten" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"strafe_persnr" 
=> array ("popis"=>"","sirka"=>9,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"strafe_wert" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),
    
"erstellt" 
=> array ("popis"=>"","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>0),    

"Bemerkung" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'LRBT',"align"=>"R","radek"=>1,"fill"=>0), 
    
//"fill" 
//=> array ("popis"=>"","sirka"=>0,"ram"=>'LRBT',"align"=>"R","radek"=>1,"fill"=>0),

);

// first line of header 
$cells_header1 = 
array(
    
"dummy1" 
=> array ("popis"=>"Intern + Extern (I+E)","sirka"=>160,"ram"=>'RB',"align"=>"L","radek"=>0,"fill"=>0),

"dummy2" 
=> array ("popis"=>"Ausschuss anerkannt","sirka"=>19,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"dummy3" 
=> array ("popis"=>"Nacharbeit anerkannt","sirka"=>19,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

"dummy4" 
=> array ("popis"=>"Nicht anerkannt","sirka"=>14,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),    
    
"dummy5" 
=> array ("popis"=>"","sirka"=>8,"ram"=>'LRB',"align"=>"C","radek"=>0,"fill"=>0),   
    
"dummy6" 
=> array ("popis"=>"Q-Prämien-Abzüge","sirka"=>17,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),   
    
"dummy7" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'LB',"align"=>"L","radek"=>1,"fill"=>0),    
);

// second line of header
$cells_header2 =
array(
"rekl_nr" 
=> array ("popis"=>"\nRekl.-Nr.","sirka"=>10,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),

"import" 
=> array ("popis"=>"IM\nEX","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"export" 
=> array ("popis"=>"\nEX","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"erhalten_am" 
=> array ("popis"=>"\nErhalten am","sirka"=>12,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),
    
"erledigt_am" 
=> array ("popis"=>"\nErledigt am","sirka"=>12,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),    

"beschr_abweichung" 
=> array ("popis"=>"\nBeschreibung der Abweichung","sirka"=>30,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),

"beschr_ursache" 
=> array ("popis"=>"\nBeschreibung der Ursache","sirka"=>30,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),    
    
"teil" 
=> array ("popis"=>"\nTeil","sirka"=>12,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

"gewicht" 
=> array ("popis"=>"Gewicht\n[kg]","sirka"=>8,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),
    
"stk_expediert" 
=> array ("popis"=>"Stk\nexpediert","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),
    
"stk_reklammiert" 
=> array ("popis"=>"Stk\nreklamiert","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),
    
"interne_bewertung" 
=> array ("popis"=>"interne\nBewertung","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),

////
"anerkannt_stk_ausschuss" 
    => array ("popis"=>"\nStk","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
    "anerkannt_gew_ausschuss" 
    => array ("popis"=>"\nGew.","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
    "anerkannt_wert_ausschuss" 
    => array ("popis"=>"\nKosten","sirka"=>7,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

////    
"anerkannt_stk_nacharbeit" 
    => array ("popis"=>"\nStk","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
    "anerkannt_gew_nacharbeit" 
    => array ("popis"=>"\nGew.","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
    "anerkannt_wert_nacharbeit" 
    => array ("popis"=>"\nKosten","sirka"=>7,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

////    
"anerkannt_stk_nein" 
    => array ("popis"=>"\nStk","sirka"=>7,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
    "anerkannt_gew_nein" 
    => array ("popis"=>"\nGew.","sirka"=>7,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"andere_kosten" 
=> array ("popis"=>"Andere\nKosten","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

"strafe_persnr" 
=> array ("popis"=>"\nPers.-Nr.","sirka"=>9,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),    
 
"strafe_wert" 
=> array ("popis"=>"\nCZK","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),   
    
"erstellt" 
=> array ("popis"=>"\nErstellt","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1), 
    
"bemerkung" 
=> array ("popis"=>"\nBemerkung","sirka"=>0,"ram"=>'LRBT',"align"=>"L","radek"=>1,"fill"=>1),     
    
//"fill" 
//=> array ("popis"=>"\n","sirka"=>0,"ram"=>'LRBT',"align"=>"R","radek"=>1,"fill"=>1),

);


// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku,$popisek)
{
	
	//$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$pdfobjekt->Cell(0,$headervyskaradku,$popisek,'0',1,'L',0);
	$pdfobjekt->SetFillColor(255,255,200,1);
	
	$pdfobjekt->SetFont("FreeSans", "", 5);
	
        // foreach header line
        foreach($pole as $row)
	{
            // foreach cell in header line
            foreach($row as $cell)
            {
		$pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
            }
            $pdfobjekt->Ln(); // new line in header
        }
	$pdfobjekt->Ln();
        $pdfobjekt->Ln();
	//$pdfobjekt->Ln();
	$pdfobjekt->SetFont("FreeSans", "", 6);
}

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

/**
 *
 * @param TCPDF $pdfobjekt
 * @param type $pole
 * @param type $zahlavivyskaradku
 * @param type $rgb
 * @param type $funkce
 * @param type $nodelist 
 */
function teloMultiZeilen($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 5);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$x=$pdfobjekt->GetX();
	$y=$pdfobjekt->GetY();
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $key=>$cell)
	{
	    $zeilen = $cell["zeilen"];
	    $pdfobjekt->SetY($y);
	    foreach ($zeilen as $zeilenr=>$Zelle){
		$pdfobjekt->SetX($x);
		$nodeName = $Zelle["node"];
		$obsah = getValueForNode($nodelist, $nodeName);
		if(array_key_exists("substr", $Zelle)) $obsah = substr ($obsah, $Zelle["substr"][0], $Zelle["substr"][1]);
		if(array_key_exists("nf", $Zelle)) $obsah = number_format($obsah, $Zelle["nf"][0], $Zelle["nf"][1], $Zelle["nf"][2]);
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$Zelle["ram"],1,$Zelle["align"],$Zelle["fill"]);
	    }
	    $x += $cell["sirka"];
	}
	$pdfobjekt->SetFont("FreeSans", "", 7);
}

// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
	$pdfobjekt->SetFont("FreeSans", "", 5);
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
	$pdfobjekt->SetFont("FreeSans", "", 7);
}

// funkce ktera vrati hodnotu podle nodename
// predam ji nodelist a jmeno node ktereho hodnotu hledam
// zaokrouhlovani jde nahoru

function kopf_kunde($pdfobjekt,$vyskaradku,$rgb,$childnodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 7);

	$kundeNr = getValueForNode($childnodes,'kundenr');
	$pdfobjekt->Cell(0,$vyskaradku,"Kunde: ".$kundeNr,'LRBT',1,'L',$fill);

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function fuss_kunde($pdfobjekt,$vyskaradku,$rgb,$childnodes)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	// dummy
	$obsah="";
	$pdfobjekt->Cell(0,$vyskaradku,"",'LRTB',1,'L',$fill);
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku);
	}
}
				
function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		return 1;
	}
	else
		return 0;
}


require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S360 - Reklamationen (Sort. Kunde/Rekl.-Nr.)", $params);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT-5, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT-5);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 7));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();
// unnessesary? $pdf->SetFont("FreeSans", "", 6);

// prvni stranka
$pdf->AddPage();
pageheader($pdf, Array($cells_header1, $cells_header2), 5, "");
// zacinam po zakaznicich
$kunden=$domxml->getElementsByTagName("kunde");
foreach($kunden as $kunde)
{
    $kundeChildNodes = $kunde->childNodes;
    test_pageoverflow($pdf,5,Array($cells_header1, $cells_header2));
    kopf_kunde($pdf, 5, array(200,255,200), $kundeChildNodes);
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    foreach($reklamationen as $reklamation){
	$reklChilds = $reklamation->childNodes;
	test_pageoverflow($pdf,5,Array($cells_header1, $cells_header2));
//	telo($pdf,$cells,5,array(255,255,255),"",$reklChilds);
	teloMultiZeilen($pdf,$cellsMultiZeilen,4,array(255,255,255),"",$reklChilds);
    }
//    fuss_kunde($pdf, 5, array(200,255,200), $kundeChildNodes);
}
	

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
