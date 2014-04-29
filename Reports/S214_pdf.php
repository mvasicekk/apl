<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S214";
$doc_subject = "S214 Report";
$doc_keywords = "S214";

// necham si vygenerovat XML

$parameters=$_GET;

$gepl_von="P".$_GET['gepl_von'];
$gepl_bis="P".$_GET['gepl_bis'];

$abgnrvon = $_GET['abgnrvon'];
$abgnrbis = $_GET['abgnrbis'];
$statnr = $_GET['statnr'];
$teil = $_GET['teil'];
$reporttyp = $_GET['reporttyp'];

if($reporttyp=='mit VzKd')
    $bVzkd = TRUE;
else
    $bVzkd = FALSE;

if($teil=='*' || strlen($teil)==0)
    $bTeilWhere = FALSE;
else
    $bTeilWhere = TRUE;

$teil = strtr($teil, '*', '%');

require_once('S214_xml.php');


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
=> array ("popis"=>"","sirka"=>35,"ram"=>'L',"align"=>"L","radek"=>0,"fill"=>0),

"kzgut"
=> array ("popis"=>"","sirka"=>5,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"abgnr" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"stk_drueck" 
=> array ("popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss2" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss4" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"auss6" 
=> array ("popis"=>"","sirka"=>10,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd_geplant" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>20,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzkd" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"vzaby" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"verb" 
=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>15,"ram"=>'0',"align"=>"R","radek"=>0,"fill"=>0),

"fac1" 
=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>0,"ram"=>'R',"align"=>"R","radek"=>1,"fill"=>0),

);

$cells_header = 
array(

"teil" 
=> array ("popis"=>"\nTeil","sirka"=>40,"ram"=>'1',"align"=>"L","radek"=>0,"fill"=>1),

"abgnr" 
=> array ("popis"=>"\nTat","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"stk_drueck" 
=> array ("popis"=>"\nStk","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"auss2" 
=> array ("popis"=>"Auss\n(2)","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"auss4" 
=> array ("popis"=>"Auss\n(4)","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"auss6" 
=> array ("popis"=>"Auss\n(6)","sirka"=>10,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd_geplant" 
=> array ("popis"=>"VzKd\ngepl.","sirka"=>20,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"vzkd" 
=> array ("popis"=>"\nvzkd","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"vzaby" 
=> array ("popis"=>"\nvzaby","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"verb" 
=> array ("popis"=>"\nverb","sirka"=>15,"ram"=>'1',"align"=>"R","radek"=>0,"fill"=>1),

"fac1" 
=> array ("popis"=>"vzkd/\nverb","sirka"=>0,"ram"=>'1',"align"=>"R","radek"=>1,"fill"=>1),

);



$sum_zapati_teil_array = array(	
								"auss2"=>0,
								"auss4"=>0,
								"auss6"=>0,
								"vzkd"=>0,
								"vzkd_geplant"=>0,
                                                                "vzaby_geplant"=>0,
								"vzaby"=>0,
								"verb"=>0,
								"gew_geplant"=>0
								);
global $sum_zapati_teil_array;

$sum_zapati_auftrag_array = array(	
								"auss2"=>0,
								"auss4"=>0,
								"auss6"=>0,
								"vzkd"=>0,
								"vzkd_geplant"=>0,
                                                                "vzaby_geplant"=>0,
								"vzaby"=>0,
								"verb"=>0,
								"gew_geplant"=>0
								);
global $sum_zapati_auftrag_array;

$sum_zapati_termin_array = array(	
								"auss2"=>0,
								"auss4"=>0,
								"auss6"=>0,
								"vzkd"=>0,
								"vzkd_geplant"=>0,
                                                                "vzaby_geplant"=>0,
								"vzaby"=>0,
								"verb"=>0,
								"gew_geplant"=>0
								);
global $sum_zapati_termin_array;

$sum_zapati_sestava_array = array(	
								"auss2"=>0,
								"auss4"=>0,
								"auss6"=>0,
								"vzkd"=>0,
								"vzkd_geplant"=>0,
                                                                "vzaby_geplant"=>0,
								"vzaby"=>0,
								"verb"=>0,
								"gew_geplant"=>0
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



// funkce pro vykresleni hlavicky na kazde strance
function pageheader($pdfobjekt, $pole, $headervyskaradku, $geplannt, $exdatum) {
    global $bVzkd;
    $pdfobjekt->SetFont("FreeSans", "B", 10);
    $pdfobjekt->Cell(0, 10, "geplant mit: " . $geplannt . " ( " . $exdatum . " )", '0', 1, 'L', 0);

    $pdfobjekt->SetFont("FreeSans", "", 6);
    $pdfobjekt->SetFillColor(255, 255, 200, 1);
    foreach ($pole as $key=>$cell) {
        if(!$bVzkd){
            if($key=='vzkd') $obsah="\n";
            else if($key=='fac1') $obsah="vzaby/\nverb";
            else if($key=='vzkd_geplant') $obsah ="VzAby\ngepl.";
            else
                $obsah = $cell['popis'];
        }
        else
            $obsah = $cell['popis'];
        $pdfobjekt->MyMultiCell($cell["sirka"], $headervyskaradku, $obsah, $cell["ram"], $cell["align"], $cell['fill']);
    }
//    $pdfobjekt->Ln();
    $pdfobjekt->Ln();
    $pdfobjekt->SetFillColor($prevFillColor[0], $prevFillColor[1], $prevFillColor[2]);
    $pdfobjekt->SetFont("FreeSans", "", 6);
}

// funkce pro vykresleni tela
function telo($pdfobjekt,$pole,$zahlavivyskaradku,$rgb,$funkce,$nodelist)
{
        global $bVzkd;
	$pdfobjekt->SetFont("FreeSans", "", 8);
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	// pujdu polem pro zahlavi a budu prohledavat predany nodelist
	foreach($pole as $nodename=>$cell)
	{
		if(array_key_exists("nf",$cell))
		{
			$cellobsah = 
			number_format(floatval(getValueForNode($nodelist,$nodename)), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
		}
		else
		{
			$cellobsah=getValueForNode($nodelist,$nodename);
		}

                if(!$bVzkd){
                    if($nodename=='vzkd' || $nodename=='fac1') $cellobsah='';
                    if($nodename=='vzkd_geplant'){
                        $cellobsah = number_format(floatval(getValueForNode($nodelist,'vzaby_geplant')), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
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
function zahlavi_auftrag($pdfobjekt,$vyskaradku,$rgb,$cells_header,$auftragsnr,$childs)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);

        $aufdat = getValueForNode($childs, 'aufdat');
	$pdfobjekt->Cell(0,$vyskaradku,"",'0',1,'L',0);
	$pdfobjekt->Cell(0,$vyskaradku,"IMPORT: ".$auftragsnr.' / '.$aufdat,'1',1,'L',$fill);
	
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_termin($pdfobjekt,$vyskaradku,$rgb,$cells_header,$termin,$exdatum)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	$pdfobjekt->SetFont("FreeSans", "B", 9);

	$pdfobjekt->Cell(0,$vyskaradku,"geplant mit: ".$termin." (".$exdatum." )",'1',1,'L',$fill);
	
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zahlavi_teil($pdfobjekt,$vyskaradku,$rgb,$cells_header,$teilnr,$gew,$muster_platz,$teillang,$abnr,$verpackungmenge)
{
	$a = AplDB::getInstance();
	// dokunrchange
	//$musterRow = $a->getTeilDokument($teilnr, 29, TRUE);
	$musterRow = $a->getTeilDokument($teilnr, AplDB::DOKUNR_MUSTER, TRUE);
	//$musterRow = $a->getTeilDokument($teilnr, 12, TRUE);
	if($musterRow===NULL)
	    $musterText = "";
	else
	    $musterText = $musterRow['doku_nr']."/".$musterRow['doku_beschreibung']."/".$musterRow['einlag_datum']."/".$musterRow['musterplatz']."/".$musterRow['freigabe_am']."/".$musterRow['freigabe_vom'];
	
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
	
	$pdfobjekt->SetFont("FreeSans", "B", 8);
	$pdfobjekt->Cell(50,$vyskaradku,"   ".$teilnr." / ".$teillang,'1',0,'L',$fill);
	
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$gew=number_format($gew,2,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$gew." kg/Stk ",'1',0,'R',$fill);
        $pdfobjekt->Cell(10,$vyskaradku," VE: ".$verpackungmenge,'1',0,'L',$fill);
	$pdfobjekt->Cell(15,$vyskaradku,"[".$abnr."]",'1',0,'L',$fill);
	$pdfobjekt->Cell(0,$vyskaradku,$musterText,'1',1,'L',$fill);
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_taetigkeit($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$tatnr)
{
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(95,$vyskaradku,$popis." ".$tatnr,'B',0,'L',$fill);
	
	
	$obsah=$pole['stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss_stk'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	//auss_typ
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	//vzkd_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	//vzaby_stk
	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
	
	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
	
	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);

	$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_teil($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$teilnr,$fac1,$stk_ursprung)
{
        global $bVzkd;

	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8.5);

	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

        $pdfobjekt->Cell(45,$vyskaradku,'','LB',0,'L',$fill);
	$pdfobjekt->Cell(5,$vyskaradku,"IST: ",'B',0,'R',$fill);
	

	$obsah=$stk_ursprung;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,"",'B',0,'R',$fill);
	
	$obsah=$pole['auss2'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss4'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss6'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";//$pole['vzkd_geplant'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);

        if($bVzkd){
            $obsah=$pole['vzkd'];
            $obsah=number_format($obsah,0,',',' ');
        }
        else{
            $obsah='';
        }
	
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);


            $obsah=$fac1;
            $obsah=number_format($obsah,2,',',' ');

	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'BR',1,'R',$fill);
	
	// druhy radek
        $pdfobjekt->Cell(45,$vyskaradku,'(Auftrag =)','BL',0,'L',$fill);
	$pdfobjekt->Cell(5,$vyskaradku,"SOLL: ",'B',0,'R',$fill);

      	$obsah=$stk_ursprung;
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,"(StkIM:".$obsah.")",'B',0,'R',$fill);


        $pdfobjekt->Cell(30,$vyskaradku,'','B',0,'L',$fill);
        


        if($bVzkd){
            $obsah=$pole['vzkd_geplant'];
            $obsah=number_format($obsah,0,',',' ');
        }
        else{
            $obsah=$pole['vzaby_geplant'];
            $obsah=number_format($obsah,0,',',' ');
        }

	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";
	$pdfobjekt->Cell(45,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['gew_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(13,$vyskaradku,$obsah.' kg','B',0,'R',$fill);

        $pdfobjekt->Cell(0,$vyskaradku,'','BR',1,'R',$fill);
	
	//$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_termin($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$terminnr,$fac1)
{
        global $bVzkd;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->SetFont("FreeSans", "B", 8.5);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(65,$vyskaradku,$popis." IST: ".$terminnr,'B',0,'L',$fill);
	
	
	$obsah=$pole['auss2'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss4'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss6'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";//$pole['vzkd_geplant'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
        if(!$bVzkd) $obsah = '';
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac1;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
	
	// druhy radek
	$pdfobjekt->Cell(95,$vyskaradku,$popis." SOLL: ".$terminnr,'B',0,'L',$fill);

	$obsah=$pole['vzkd_geplant'];
        if(!$bVzkd) $obsah=$pole['vzaby_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";
	$pdfobjekt->Cell(45,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['gew_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"Soll Gewicht: ".$obsah." kg",'B',1,'R',$fill);

    $rozdil = round($pole['vzkd_geplant'])-round($pole['vzkd']);
    if(!$bVzkd) $rozdil = round($pole['vzaby_geplant'])-round($pole['vzaby']);
    $obsah=number_format($rozdil,0,',',' ');

    if($bVzkd)
        $pdfobjekt->Cell(95,$vyskaradku,"VzKd(geplant-bearbeitet)",'B',0,'L',$fill);
    else
        $pdfobjekt->Cell(95,$vyskaradku,"VzAby(geplant-bearbeitet)",'B',0,'L',$fill);
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
    $pdfobjekt->Cell(0,$vyskaradku,"",'B',1,'R',$fill);

	//$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//function zapati_auftrag($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$auftragsnr)
//{
//	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
//	$fill=1;
//
//	// dummy
//	$obsah="";
//	//$obsah=number_format($obsah,0,',',' ');
//	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);
//
//	$pdfobjekt->Cell(95,$vyskaradku,$popis." ".$auftragsnr,'B',0,'L',$fill);
//
//
//	$obsah=$pole['stk'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
//
//	$obsah=$pole['auss_stk'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
//
//	//auss_typ
//	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
//
//	//vzkd_stk
//	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
//
//	$obsah=$pole['vzkd'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
//
//	//vzaby_stk
//	$pdfobjekt->Cell(10,$vyskaradku,"",'B',0,'L',$fill);
//
//	$obsah=$pole['vzaby'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);
//
//	$obsah=$pole['verb'];
//	$obsah=number_format($obsah,0,',',' ');
//	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
//
//	$pdfobjekt->Ln();
//
//	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
//}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function zapati_sestava($pdfobjekt,$node,$vyskaradku,$popis,$rgb,$pole,$fac1)
{
    global $bVzkd;


    $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

	$pdfobjekt->SetFont("FreeSans", "B", 8.5);
	// dummy
	$obsah="";
	//$obsah=number_format($obsah,0,',',' ');
	//$pdfobjekt->Cell(45,$vyskaradku,$obsah,'0',0,'R',0);

	$pdfobjekt->Cell(65,$vyskaradku,$popis." IST: ".$terminnr,'B',0,'L',$fill);
	
	
	$obsah=$pole['auss2'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss4'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['auss6'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(10,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";//$pole['vzkd_geplant'];
	//$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);

        $obsah=$pole['vzkd'];
	$obsah=number_format($obsah,0,',',' ');
        if(!$bVzkd) $obsah = '';
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['vzaby'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['verb'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(15,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$fac1;
	$obsah=number_format($obsah,2,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,$obsah,'B',1,'R',$fill);
	
	// druhy radek
	$pdfobjekt->Cell(95,$vyskaradku,$popis." SOLL: ".$terminnr,'B',0,'L',$fill);

        if($bVzkd)
            $obsah=$pole['vzkd_geplant'];
        else
            $obsah=$pole['vzaby_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah="";
	$pdfobjekt->Cell(45,$vyskaradku,$obsah,'B',0,'R',$fill);

	$obsah=$pole['gew_geplant'];
	$obsah=number_format($obsah,0,',',' ');
	$pdfobjekt->Cell(0,$vyskaradku,"Soll Gewicht: ".$obsah." kg",'B',1,'R',$fill);


        if($bVzkd)
            $rozdil = round($pole['vzkd_geplant'])-round($pole['vzkd']);
        else
            $rozdil = round($pole['vzaby_geplant'])-round($pole['vzaby']);
    $obsah=number_format($rozdil,0,',',' ');
        if($bVzkd)
            $pdfobjekt->Cell(95,$vyskaradku,"VzKd(geplant-bearbeitet)",'B',0,'L',$fill);
        else
            $pdfobjekt->Cell(95,$vyskaradku,"VzAby(geplant-bearbeitet)",'B',0,'L',$fill);
	$pdfobjekt->Cell(20,$vyskaradku,$obsah,'B',0,'R',$fill);
    $pdfobjekt->Cell(0,$vyskaradku,"",'B',1,'R',$fill);

	//$pdfobjekt->Ln();
	
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}



function test_pageoverflow($pdfobjekt,$testvysradku,$cellhead,$vysradku,$geplannt,$exdatum)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$testvysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,$vysradku,$geplannt,$exdatum);
//		$pdfobjekt->Ln();
//		$pdfobjekt->Ln();
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S214 Leistung Auftrag - geplant mit", $params);
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

// a ted pujdu po terminech
$terminy=$domxml->getElementsByTagName("geplant");
$citac=0;
foreach($terminy as $termin)
{
	$terminnr=$termin->getElementsByTagName("termin")->item(0)->nodeValue;
	$ex_datum=$termin->getElementsByTagName("ex_datum")->item(0)->nodeValue;
	
	$pdf->AddPage();
	pageheader($pdf,$cells_header,5,$terminnr,$ex_datum);
	nuluj_sumy_pole($sum_zapati_termin_array);
	$auftraege=$termin->getElementsByTagName("auftrag");
	foreach($auftraege as $auftrag)
	{
		$auftragsnr=$auftrag->getElementsByTagName("AuftragsNr")->item(0)->nodeValue;
                $auftragChilds = $auftrag->childNodes;
				
		$ow=test_pageoverflow($pdf,5,$cells_header,5,$terminnr,$ex_datum);
		zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr,$auftragChilds);
		
		nuluj_sumy_pole($sum_zapati_auftrag_array);
		$teile=$auftrag->getElementsByTagName("teil");
		foreach($teile as $teil)
		{
		
			$teilnr=$teil->getElementsByTagName("teilnr")->item(0)->nodeValue;
			$stk_ursprung=$teil->getElementsByTagName("stk_ursprung")->item(0)->nodeValue;
			$teillang=$teil->getElementsByTagName("teillang")->item(0)->nodeValue;
			$gew=$teil->getElementsByTagName("Gew")->item(0)->nodeValue;
			$abnr=$teil->getElementsByTagName("abnr")->item(0)->nodeValue;
			$muster_platz=$teil->getElementsByTagName("musterplatz")->item(0)->nodeValue;
                        $ve=$teil->getElementsByTagName("verpackungmenge")->item(0)->nodeValue;
			
			
			$ow=test_pageoverflow($pdf,10,$cells_header,5,$terminnr,$ex_datum);
			if($ow){
				zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr,$auftragChilds);
			}
			zahlavi_teil($pdf,5,array(255,255,255),$cells_header,$teilnr,$gew,$muster_platz,$teillang,$abnr,$ve);
			nuluj_sumy_pole($sum_zapati_teil_array);
			
			$taetigkeiten=$teil->getElementsByTagName("taetigkeit");
			
			foreach($taetigkeiten as $taetigkeit)
			{
				$taetigkeit_childs=$taetigkeit->childNodes;
				
				$ow=test_pageoverflow($pdf,4,$cells_header,5,$terminnr,$ex_datum);
				if($ow){
					zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr,$auftragChilds);
					zahlavi_teil($pdf,5,array(255,255,255),$cells_header,$teilnr,$gew,$muster_platz,$teillang,$abnr,$ve);
				}
				
				telo($pdf,$cells,4,array(255,255,255),"",$taetigkeit_childs);
				
				
				// projedu pole a aktualizuju sumy pro zapati teil
				foreach($sum_zapati_teil_array as $key=>$prvek)
				{
					$hodnota=$taetigkeit->getElementsByTagName($key)->item(0)->nodeValue;
					$sum_zapati_teil_array[$key]+=$hodnota;
				}
				
			}
			
			// zapisu hodnotu planovane hmotnosti
			$sum_zapati_teil_array['gew_geplant']=$teil->getElementsByTagName("gew_geplant")->item(0)->nodeValue;
		
			if($sum_zapati_teil_array['verb']!=0){
                            if($bVzkd)
				$fac1=$sum_zapati_teil_array['vzkd']/$sum_zapati_teil_array['verb'];
                            else
                                $fac1=$sum_zapati_teil_array['vzaby']/$sum_zapati_teil_array['verb'];
                        }
			else
				$fac1=0;
				
			if(test_pageoverflow($pdf,10,$cells_header,5,$terminnr,$ex_datum)) $pdf->Ln(5);

			zapati_teil($pdf,$taetigkeit,5,"Summe Teil",array(235,235,235),$sum_zapati_teil_array,$teilnr,$fac1,$stk_ursprung);
		
			
			// sumy pro zapati auftrag
			foreach($sum_zapati_auftrag_array as $key=>$prvek)
			{
				$hodnota=$sum_zapati_teil_array[$key];
				$sum_zapati_auftrag_array[$key]+=$hodnota;
			}
			
			
		}
		
		/*
		if(test_pageoverflow($pdf,5,$cells_header))
			zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr);
		
		zapati_teil($pdf,$pers,5,"Summe Teil",array(235,235,235),$sum_zapati_teil_array,$teilnr);
	
		// po dilu odstrankuju
		//$pdf->AddPage();
		//pageheader($pdf,$cells_header,5);
		//zahlavi_auftrag($pdf,5,array(255,255,200),$cells_header,$auftragsnr);
		*/
		// sumy pro zapati termin
		foreach($sum_zapati_termin_array as $key=>$prvek)
		{
			$hodnota=$sum_zapati_auftrag_array[$key];
			$sum_zapati_termin_array[$key]+=$hodnota;
		}
		
		
	}
	

	if($sum_zapati_termin_array['verb']!=0){
            if($bVzkd)
		$fac1=$sum_zapati_termin_array['vzkd']/$sum_zapati_termin_array['verb'];
            else
                $fac1=$sum_zapati_termin_array['vzaby']/$sum_zapati_termin_array['verb'];
        }
	else
		$fac1=0;

	test_pageoverflow($pdf,5,$cells_header,5,$terminnr,$ex_datum);
	zapati_termin($pdf,$pers,5,"Summe EX",array(255,255,100),$sum_zapati_termin_array,$terminnr,$fac1);
	
	foreach($sum_zapati_sestava_array as $key=>$prvek)
	{
		$hodnota=$sum_zapati_termin_array[$key];
		$sum_zapati_sestava_array[$key]+=$hodnota;
	}
	
	// po terminu odstrankuju
	//$pdf->AddPage();
	//pageheader($pdf,$cells_header,5,$terminnr,$ex_datum);

}


if($sum_zapati_sestava_array['verb']!=0){
    if($bVzkd)
	$fac1=$sum_zapati_sestava_array['vzkd']/$sum_zapati_sestava_array['verb'];
    else
        $fac1=$sum_zapati_sestava_array['vzaby']/$sum_zapati_sestava_array['verb'];
}
else
	$fac1=0;

test_pageoverflow($pdf,5,$cells_header,5,$terminnr,$ex_datum);
zapati_sestava($pdf,$import,5,"Summe Bericht",array(200,200,255),$sum_zapati_sestava_array,$fac1);


//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
