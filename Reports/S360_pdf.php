<?php
require_once '../security.php';

//set_time_limit(20);

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
$reklnr = $_GET['reklnr'];

if($reklnr=='*') $reklnr="";
else $reklnr = strtr ($reklnr, '*', '%');

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
		$value5 = $parametry->item(4)->lastChild->nodeValue;
                
                $params =   "Kunde von: "       . $value1 .
                            " Kunde bis: "      . $value2 .
                            " Erhalten am: "    . $value3 .
                            " - "               . $value4 .
			    " ReklNr: "         . $value5 ;
                
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

    "rekl_nr" => array ("popis"=>"",
                        "sirka"=>10,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"rekl_nr",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"kd_rekl_nr",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0)
                            )
                        ),
    
    "import" => array ("popis"=>"",
                        "sirka"=>9,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"import",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,7)),
                            "2" => array(
                                "node"=>"export",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,7)),
                            "3" => array(
                                "node"=>"export",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(7,7))
                            )
                        ),
    "erhalten_am" => array ("popis"=>"",
                        "sirka"=>13,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"erhalten_am",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"erledigt_am",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0)
                            )
                        ),
    "beschr_abweichung" => array ("popis"=>"",
                        "sirka"=>33,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"beschr_abweichung",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,33)),
                            "2" => array(
                                "node"=>"beschr_abweichung",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(33,33)),
                            "3" => array(
                                "node"=>"beschr_abweichung",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(67,33))
                            )
                        ),
    "beschr_ursache" => array ("popis"=>"",
                        "sirka"=>33,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"beschr_ursache",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,33)),
                            "2" => array(
                                "node"=>"beschr_ursache",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(33,33)),
                            "3" => array(
                                "node"=>"beschr_ursache",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(67,33))
                            )
                        ),
    
    "beschr_beseitigung" => array ("popis"=>"",
                        "sirka"=>33,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"beschr_beseitigung",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,33)),
                            "2" => array(
                                "node"=>"beschr_beseitigung",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(33,33)),
                            "3" => array(
                                "node"=>"beschr_beseitigung",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(67,33))
                            )
                        ),
    
    "teil" => array ("popis"=>"",
                        "sirka"=>15,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"teil",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"giesstag",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0)
                            )
                        ),
    
    "gewicht" => array ("popis"=>"",
                        "sirka"=>10,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"gewicht",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0)
                            )
                        ),
    "stk_expediert" => array ("popis"=>"",
                        "sirka"=>11,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"stk_expediert",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"stk_reklammiert",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0)
                            )
                        ),
    "interne_bewertung" => array ("popis"=>"",
                        "sirka"=>7,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"interne_bewertung",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0)
                            )
                        ),
    "anerkannt_stk_ausschuss" => array ("popis"=>"",
                        "sirka"=>8,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"anerkannt_stk_ausschuss",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"anerkannt_stk_nacharbeit",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"anerkannt_stk_nein",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0)
                            )
                        ),
    "anerkannt_gew_ausschuss" => array ("popis"=>"",
                        "sirka"=>8,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"anerkannt_gew_ausschuss",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6)),
                            "2" => array(
                                "node"=>"anerkannt_gew_nacharbeit",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6)),
                            "3" => array(
                                "node"=>"anerkannt_gew_nein",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6))
                            )
                        ),
    "anerkannt_wert_ausschuss" => array ("popis"=>"",
                        "sirka"=>8,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"anerkannt_wert_ausschuss",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6)),
                            "2" => array(
                                "node"=>"anerkannt_wert_nacharbeit",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6)),
                            "3" => array(
                                "node"=>"",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6))
                            )
                        ),
    "andere_kosten" => array ("popis"=>"",
                        "sirka"=>8,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"andere_kosten",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6)),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6)),
                            "3" => array(
                                "node"=>"",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,6))
                            )
                        ),
    "strafe_persnr" => array ("popis"=>"",
                        "sirka"=>8,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"1strafe_persnr",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"2strafe_persnr",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"3strafe_persnr",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,8))
                            )
                        ),
    "strafe_wert" => array ("popis"=>"",
                        "sirka"=>7,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"1strafe_wert",
                                "ram"=>'LRT',
                                "align"=>"R",
                                "fill"=>0),
                            "2" => array(
                                "node"=>"2strafe_wert",
                                "ram"=>'LR',
                                "align"=>"R",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"3strafe_wert",
                                "ram"=>'LRB',
                                "align"=>"R",
                                "fill"=>0,
                                "substr"=>array(0,8))
                            )
                        ),
    "erstellt" => array ("popis"=>"",
                        "sirka"=>8,
                        "radek"=>0,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"erstellt",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>0,
                                "substr"=>array(0,7)),
                            "2" => array(
                                "node"=>"",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>0),
                            "3" => array(
                                "node"=>"",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>0)
                            )
                        ),
    "bemerkung" => array ("popis"=>"",
                        "sirka"=>0,
                        "radek"=>1,
                        "zeilen" => array(
                            "1" => array(
                                "node"=>"bemerkung",
                                "ram"=>'LRT',
                                "align"=>"L",
                                "fill"=>1,
                                "substr"=>array(0,63)),
                            "2" => array(
                                "node"=>"bemerkung",
                                "ram"=>'LR',
                                "align"=>"L",
                                "fill"=>1,
                                "substr"=>array(63,63)),
                            "3" => array(
                                "node"=>"bemerkung",
                                "ram"=>'LRB',
                                "align"=>"L",
                                "fill"=>1,
                                "substr"=>array(126,63))
                            )
                        )
);
/*
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
*/

// layout
$headerCellHightShort = 2; // hight of the 3 cells Ausschuss/Nacharbeit/Nicht

// fontsizes
$fontSizeLegend = 6;
$fontSizeClient = 8;
$fontSizeBody   = 6;


// a array with all the header lines.
$cells_header = array(
'1' =>
array(
    
"dummy1" 
=> array ("popis"=>"Intern + Extern (I+E)","sirka"=>174,"ram"=>'R',"align"=>"L","radek"=>0,"fill"=>0),

"dummy2" 
=> array ("popis"=>"Anerkannt","sirka"=>24,"ram"=>'LRTB',"align"=>"C","radek"=>0,"fill"=>1),
    
"dummy7" 
=> array ("popis"=>"","sirka"=>0,"ram"=>'L',"align"=>"L","radek"=>1,"fill"=>0),    
),
    
'2' =>
array(
    
"dummy1" 
=> array ("popis"=>"","sirka"=>174,"hight"=>$headerCellHightShort,"ram"=>'R',"align"=>"L","radek"=>0,"fill"=>0),

"dummy2" 
=> array ("popis"=>"Ausschuss","sirka"=>24,"hight"=>$headerCellHightShort,"ram"=>'LRT',"align"=>"C","radek"=>0,"fill"=>1),
    
"dummy7" 
=> array ("popis"=>"","sirka"=>0,"hight"=>$headerCellHightShort,"ram"=>'L',"align"=>"L","radek"=>1,"fill"=>0),    
),  
    
'3' =>
array(
    
"dummy1" 
=> array ("popis"=>"Wechselkurs Euro - CZK: ".str_replace(".", ",", $EUR_CZK),"sirka"=>174,"hight"=>$headerCellHightShort,"ram"=>'R',"align"=>"L","radek"=>0,"fill"=>0),

"dummy2" 
=> array ("popis"=>"Nacharbeit","sirka"=>24,"hight"=>$headerCellHightShort,"ram"=>'LR',"align"=>"C","radek"=>0,"fill"=>1),
    
"dummy7" 
=> array ("popis"=>"","sirka"=>0,"hight"=>$headerCellHightShort,"ram"=>'L',"align"=>"L","radek"=>1,"fill"=>0),    
),  
    
'4' =>
array(
    
"dummy1" 
=> array ("popis"=>"","sirka"=>174,"hight"=>$headerCellHightShort,"ram"=>'R',"align"=>"L","radek"=>0,"fill"=>0),

"dummy2" 
=> array ("popis"=>"Nicht","sirka"=>24,"hight"=>$headerCellHightShort,"ram"=>'LRB',"align"=>"C","radek"=>0,"fill"=>1),
    
"dummy7" 
=> array ("popis"=>"","sirka"=>0,"hight"=>$headerCellHightShort,"ram"=>'L',"align"=>"L","radek"=>1,"fill"=>0),    
),  


'5' =>
array(
"rekl_nr" 
=> array ("popis"=>"Rekl.-Nr.\nKd.Rekl.","sirka"=>10,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),

"import" 
=> array ("popis"=>"IM\nEX","sirka"=>9,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
//"export" 
//=> array ("popis"=>"\nEX","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"erhalten_am" 
=> array ("popis"=>"Erhalten am\nErledigt am","sirka"=>13,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),
    
//"erledigt_am" 
//=> array ("popis"=>"\nErledigt am","sirka"=>12,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),    

"beschr_abweichung" 
=> array ("popis"=>"\nBeschreibung der Abweichung","sirka"=>33,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),

"beschr_ursache" 
=> array ("popis"=>"\nBeschreibung der Ursache","sirka"=>33,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),    
  
"beschr_beseitigung" 
=> array ("popis"=>"\nArt der Beseitigung","sirka"=>33,"ram"=>'LRBT',"align"=>"L","radek"=>0,"fill"=>1),        
    
"teil" 
=> array ("popis"=>"Teil\nGießtag","sirka"=>15,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

"gewicht" 
=> array ("popis"=>"Gewicht\n[kg]","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),
    
"stk_expediert" 
=> array ("popis"=>"stk exped\nreklamiert","sirka"=>11,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),
    
//"stk_reklammiert" 
//=> array ("popis"=>"Stk\nreklamiert","sirka"=>10,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),
    
"interne_bewertung" 
=> array ("popis"=>"\nBew.","sirka"=>7,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1),

////
"anerkannt_stk_ausschuss" 
    => array ("popis"=>"\nStk","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"anerkannt_gew_ausschuss" 
=> array ("popis"=>"\nGew.","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"anerkannt_wert_ausschuss" 
=> array ("popis"=>"\nCZK","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

////    
//"anerkannt_stk_nacharbeit" 
//    => array ("popis"=>"\nStk","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
//    "anerkannt_gew_nacharbeit" 
//    => array ("popis"=>"\nGew.","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
//    "anerkannt_wert_nacharbeit" 
//    => array ("popis"=>"\nKosten","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

////    
//"anerkannt_stk_nein" 
//    => array ("popis"=>"\nStk","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
//    "anerkannt_gew_nein" 
//    => array ("popis"=>"\nGew.","sirka"=>6,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),
    
"andere_kosten" 
=> array ("popis"=>"Andere\nKosten","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),

"strafe_persnr" 
=> array ("popis"=>"Pers.\nNr.","sirka"=>8,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),    
     
"strafe_wert" 
=> array ("popis"=>"\nCZK","sirka"=>7,"ram"=>'LRBT',"align"=>"C","radek"=>0,"fill"=>1),   
    
"erstellt" 
=> array ("popis"=>"\nErstellt","sirka"=>8,"ram"=>'LRBT',"align"=>"R","radek"=>0,"fill"=>1), 
    
"bemerkung" 
=> array ("popis"=>"\nBemerkung","sirka"=>0,"ram"=>'LRBT',"align"=>"L","radek"=>1,"fill"=>1),     
    
//"fill" 
//=> array ("popis"=>"\n","sirka"=>0,"ram"=>'LRBT',"align"=>"R","radek"=>1,"fill"=>1),

)
);


// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt,$pole,$headervyskaradku,$popisek, $fontSize)
{
	
	//$pdfobjekt->SetFont("FreeSans", "B", 8);
	//$pdfobjekt->Cell(0,$headervyskaradku,$popisek,'0',1,'L',0);
	$pdfobjekt->SetFillColor(255,255,200,1);
	
	$pdfobjekt->SetFontSize($fontSize);
	
        // foreach header line
        foreach($pole as $row)
	{
            // foreach cell in header line
            foreach($row as $cell)
            {
                if (array_key_exists("hight", $cell))
                    $pdfobjekt->MyMultiCell($cell["sirka"],$cell["hight"],$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']); 
                else
                    $pdfobjekt->MyMultiCell($cell["sirka"],$headervyskaradku,$cell['popis'],$cell["ram"],$cell["align"],$cell['fill']);
            }
            $pdfobjekt->Ln(); // new line in header
        }
	$pdfobjekt->Ln();
//        $pdfobjekt->Ln();
	//$pdfobjekt->Ln();
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

function getClosestSpace($text, $searchString, $referenceValue)
{
    $offset = -1;
    $difference = 9999;
    // strpos ( string $haystack , mixed $needle [, int $offset = 0 ] )
    while ($result = strpos($text, $searchString, $offset+1))
    {
        if ($referenceValue - $result < $difference && $referenceValue - $result >= 0)            
        {    
            $difference = $referenceValue - $result;
        }
        else
        {
            
            return $offset;
        }
        $offset = $result;        
    }
    
    return false;
}

function SplittingObsah($pdfobjekt, $obsah, $cell, $lineNum, $Zelle)
{
    if (strpos($obsah,"\r\n"))
    {
        $SplitContent = split("\r\n", $obsah);
        if ($cell["zeilen"][$lineNum-1]["node"] == $Zelle["node"])
        {
            if ($cell["zeilen"][$lineNum-2]["node"] == $Zelle["node"])
            {
                $obsah = trim ($SplitContent[2]);
            }
            else
            {
                $obsah = trim ($SplitContent[1]);
            }
        }
        else
        {
            $obsah = trim ($SplitContent[0]);
        }
    }
    else
    {
        $obsah = trim (substr ($obsah, $Zelle["substr"][0], $Zelle["substr"][1]));
    }
                
    // just to be sure that none string which will be writen in the pdf is longer than the cell
    if ($cell["sirka"] > 1)
    {
        $stopCounter = 0;
        while ($pdfobjekt->GetStringWidth($obsah) > $cell["sirka"] - 1.5)
        {
             $stopCounter++;
             if ($stopCounter > 255)
             {
                  echo "Achtung! Endlosschleife!";
                  return;
             }
             // removes the last sign of a string
             $obsah = substr($obsah, 0, -1);                   
        }
    }
    
    return $obsah;
}

function Strafen($reklChilds, $numAndKind, $reklamationen2)
{
    $reklnr = getValueForNode($reklChilds, "rekl_nr");
    //echo $reklnr ." ...<br>";
    
    $index = substr($numAndKind, 0, 1) - 1;

    foreach ($reklamationen2 as $reklamation)
    {
        $rekl_childs = $reklamation->childNodes;
        
        if ($reklnr == getValueForNode($rekl_childs, "rekl_nr"))
        {
            foreach ($rekl_childs as $strafen)
            {
                if($strafen->nodeName=="strafen")
                {
                    $strafenNodes = $strafen->childNodes;
                    foreach($strafenNodes as $strafe)
                    {
                        $strafe_childs = $strafe->childNodes;
                        $arr[] = array (getValueForNode($strafe_childs, "persnr"),
                                            getValueForNode($strafe_childs, "vorschlag_betrag"),
                                            getValueForNode($strafe_childs, "betr"));
                    }
                }
            }
        }
    }
    
    if (strpos($numAndKind, "persnr"))
        $res = $arr[$index][0];
    else if (strpos($numAndKind, "wert"))
        $res = $arr[$index][1];
    else
        $res = "?";
    
    return $res;
}

function teloMultiZeilen($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist, $fontSize, $reklamationen2)
{
    $pdfobjekt->SetFontSize($fontSize);
    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
    
    $x = $pdfobjekt->GetX();
    $y = $pdfobjekt->GetY();
    
    foreach ($pole as $key=>$cell)
    {
        $zeilen = $cell["zeilen"];
        $pdfobjekt->SetY($y);
        
        // echo $key; // Rekl-Nr Import
        
        foreach($zeilen as $zeilenr=>$Zelle)
        {
            $lineNum = $zeilenr;
            $pdfobjekt->SetX($x);
            $nodeName = $Zelle["node"];
            
            if (strpos($nodeName, "strafe_persnr") == 1 || strpos($nodeName, "strafe_wert") == 1)
            {            
                $obsah = Strafen($nodelist, $nodeName, $reklamationen2);                
            }
            else
                $obsah = getValueForNode($nodelist, $nodeName); // contains content of xml-element
            
            
            
            // preparing the content to fit the cell in pdf
            if (array_key_exists("substr", $Zelle))
            {                   
                $obsah = SplittingObsah($pdfobjekt, $obsah, $cell, $lineNum, $Zelle);
            }
            
            if (array_key_exists("nf", $Zelle))
                $obsah = number_format($obsah, $Zelle["nf"][0], $Zelle["nf"][1], $Zelle["nf"][2]);
                        
            $pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$obsah,$Zelle["ram"],1,$Zelle["align"],$Zelle["fill"]);
        }
        $x += $cell["sirka"];
    }
}

// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist, $fontSize)
{
	$pdfobjekt->SetFontSize($fontSize);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
                // conversion of number_format innessesary? Provoces error: return value from getValueForNode doesnt fit double as parameter
		//if(array_key_exists("nf",$cell))
		//{
                //        if (getValueForNode($nodelist,$nodename) !== double)
                //        {
                //            echo getValueForNode($nodelist,$nodename) . "\n";
                //        }
		//	$cellobsah = 
		//	number_format(getValueForNode($nodelist,$nodename), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		//}
		//else
		//{
			$cellobsah=getValueForNode($nodelist,$nodename);
		//}
		$pdfobjekt->Cell($cell["sirka"],$zahlavivyskaradku,$cellobsah,$cell["ram"],$cell["radek"],$cell["align"],$cell["fill"]);
	}
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
        $kundeNa = getValueForNode($childnodes,'kunde_name');
	$pdfobjekt->Cell(0,$vyskaradku,"Kunde: ".$kundeNr." - ".$kundeNa,'LRBT',1,'L',$fill);
        $pdfobjekt->SetFont("FreeSans", "", 7);

}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getBewPow($kunde)
{
    $getBewPow=0;
    $reklamationen = $kunde->getElementsByTagName('reklamation');
    foreach ($reklamationen as $reklamation)
    {
        $reklChilds = $reklamation->childNodes;
        
        $getBewPow += Pow(getValueForNode($reklChilds, 'interne_bewertung'),2);
    }
    return $getBewPow;
}

function getCosts($kunde)
{
    $getCosts = 0;
    $reklamationen = $kunde->getElementsByTagName('reklamation');
    foreach ($reklamationen as $reklamation)
    {
        $reklChilds = $reklamation->childNodes;
        
        $getCosts += getValueForNode($reklChilds, 'anerkannt_wert_ausschuss');
        $getCosts += getValueForNode($reklChilds, 'anerkannt_wert_nacharbeit');
        $getCosts += getValueForNode($reklChilds, 'andere_kosten');
    }
    return $getCosts;
}

function getFees($kunde, $reklamationen2)
{
    $kundenr = getValueForNode($kunde->childNodes, "kundenr");
    
    $getFees = 0;
    
    foreach ($reklamationen2 as $reklamation)
    {
        $rekl_childs = $reklamation->childNodes;
        
        if (strpos(getValueForNode($rekl_childs, "rekl_nr"),$kundenr."."))
        {
            foreach ($rekl_childs as $strafen)
            {
                if($strafen->nodeName=="strafen")
                {
                    $strafenNodes = $strafen->childNodes;
                    foreach($strafenNodes as $strafe)
                    {
                        $strafe_childs = $strafe->childNodes;
                        $getFees += getValueForNode($strafe_childs, "vorschlag_betrag");
                    }
                }
            }
        }
    }
    return $getFees;
}

function fuss_kunde($pdfobjekt,$higth,$rgb,$kunde,$fontSizeClient,$reklamationen2)
{
	//$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	//$fill=1;
        //Cell(float w [, float h] [, string txt] [, mixed border] [, integer ln] [, string align] [, integer fill] [, mixed link])
	$pdfobjekt->SetFont("FreeSans", "B", 7);

	$sumBewPow = getBewPow($kunde);
        $costs = getCosts($kunde);
        $fees = getFees($kunde, $reklamationen2);
        $pdfobjekt->Cell( 167,$higth,"Total:"     ,'LRBT',0,'L',0);
        $pdfobjekt->Cell(   7,$higth,$sumBewPow   ,'LRBT',0,'R',0);
        $pdfobjekt->Cell(  16,$higth,""           ,'LRBT',0,'R',0);
        $pdfobjekt->Cell(  16,$higth,$costs       ,'LRBT',0,'R',0);
        $pdfobjekt->Cell(   8,$higth,""           ,'LRBT',0,'R',0);
        $pdfobjekt->Cell(   7,$higth,$fees        ,'LRBT',0,'R',0);
	$pdfobjekt->Cell(   0,$higth,""           ,'LRBT',1,'L',1);
        $pdfobjekt->SetFont("FreeSans", "", 7);
}

function fuss_total($pdfobjekt, $hight, $rgb, $kunde, $fontSizeClient, $domxml2, $domxml)
{
    $totalFees = 0;
    //calculation total of fees
    $amounts=$domxml2->getElementsByTagName("vorschlag_betrag");
    foreach($amounts as $amount)
    {
        $totalFees += $amount->nodeValue;
    }
    $totalCosts = 0;
    $group1=$domxml->getElementsByTagName("anerkannt_wert_ausschuss");
    $group2=$domxml->getElementsByTagName("anerkannt_wert_nacharbeit");
    $group3=$domxml->getElementsByTagName("andere_kosten");
    foreach($group1 as $member1)
        $totalCosts += $member1->nodeValue;
    foreach($group2 as $member2)
        $totalCosts += $member2->nodeValue;
    foreach($group3 as $member3)
        $totalCosts += $member3->nodeValue;
    $totalPow = 0;
    $group4=$domxml->getElementsByTagName("interne_bewertung");
    foreach($group4 as $member4)
        $totalPow += Pow($member4->nodeValue,2);
    
    $pdfobjekt->Ln(5);
    $pdfobjekt->Ln(5);
    $pdfobjekt->Cell( 167, 5,"Total alle Kunden:"     ,'LRBT',0,'L',0);
    $pdfobjekt->Cell(   7, 5,$totalPow    ,'LRBT',0,'R',0);
    $pdfobjekt->Cell(  16, 5,""           ,'LRBT',0,'R',0);
    $pdfobjekt->Cell(  16, 5,$totalCosts  ,'LRBT',0,'R',0);
    $pdfobjekt->Cell(   8, 5,""           ,'LRBT',0,'R',0);
    $pdfobjekt->Cell(   7, 5,$totalFees   ,'LRBT',0,'R',0);
    $pdfobjekt->Cell(   0, 5,""           ,'LRBT',1,'L',1);
    $pdfobjekt->SetFont("FreeSans", "", 7);
}

function test_pageoverflow($pdfobjekt,$vysradku,$pole)
{
    global $fontSizeLegend;
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt, $pole, 5, "", $fontSizeLegend);
//		pageheader($pdfobjekt,$cellhead,$vysradku);
		return TRUE;
	}
	return FALSE;
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
$pdf->SetMargins(PDF_MARGIN_LEFT-10, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT-10);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setHeaderFont(Array("FreeSans", '', 7));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setFont("FreeSans", '', 6);

$pdf->setLanguageArray($l); //set language items

//initialize document
$pdf->AliasNbPages();

// prvni stranka

// zacinam po zakaznicich
$kunden=$domxml->getElementsByTagName("kunde");
$reklamationen2 = $domxml2->getElementsByTagName("reklamation");

foreach($kunden as $kunde)
{
    // new Page for each client
    $pdf->AddPage();
    pageheader($pdf, $cells_header, 5, "", $fontSizeLegend);
    
    $kundeChildNodes = $kunde->childNodes;
//    test_pageoverflow($pdf,5,Array($cells_header1, $cells_header2));
    test_pageoverflow($pdf,5,$cells_header);
    kopf_kunde($pdf, 5, array(200,255,200), $kundeChildNodes,$fontSizeClient);
    $reklamationen = $kunde->getElementsByTagName("reklamation");
    
    foreach($reklamationen as $reklamation){
	$reklChilds = $reklamation->childNodes;
//	test_pageoverflow($pdf,9,Array($cells_header1, $cells_header2));
	$ns=test_pageoverflow($pdf,9,$cells_header);
	if($ns===TRUE) kopf_kunde($pdf, 5, array(200,255,200), $kundeChildNodes,$fontSizeClient);
	teloMultiZeilen($pdf,$cellsMultiZeilen,3,array(255,255,255),"",$reklChilds,$fontSizeBody, $reklamationen2);    
    }
    
    fuss_kunde($pdf, 5, array(200,255,200),$kunde,$fontSizeClient,$reklamationen2);
    
//    fuss_kunde($pdf, 5, array(200,255,200), $kundeChildNodes);
}

fuss_total($pdf, 5, array(200,255,200),$kunde,$fontSizeClient,$domxml2,$domxml);
	

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
