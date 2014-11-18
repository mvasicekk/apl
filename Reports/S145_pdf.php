<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S145";
$doc_subject = "S145 Report";
$doc_keywords = "S145";

// necham si vygenerovat XML

$parameters=$_GET;

$user = $_SESSION['user'];

$user = $_SESSION['user'];
$password = $_GET['password'];

$fullAccess = testReportPassword("S145",$password,$user,0);
//$fullAccess = TRUE;

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}


$apl = AplDB::getInstance();

$persvon = intval($_GET['persvon']);
$persbis = intval($_GET['persbis']);
$dobaneurcita = $_GET['dobaneurcita']=='a'?TRUE:FALSE;
$befrvon = $apl->make_DB_datum($_GET['befrvon']);
$befrbis = $apl->make_DB_datum($_GET['befrbis']);

$zkusdobaod = $apl->make_DB_datum($_GET['zkusdobaod']);
$zkusdobado = $apl->make_DB_datum($_GET['zkusdobado']);

$zkusdoba = $_GET['zkusdoba']=='a'?TRUE:FALSE;;
$roky2 = $_GET['roky2']=='a'?TRUE:FALSE;;

//echo $_GET['roky2'];
require_once('S145_xml.php');

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
'persnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'T',"align"=>"R","radek"=>0,"fill"=>0),
'name'=> array ("popis"=>"","sirka"=>33,"ram"=>'T',"align"=>"L","radek"=>0,"fill"=>0),
'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'T',"align"=>"BR","radek"=>1,"fill"=>0),
    );


$cells_header = 
array(
'persnr'=> array ("popis"=>"\nPersnr","sirka"=>$cells['persnr']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
'name'=> array ("popis"=>"\nName","sirka"=>$cells['name']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
'lf'=> array ("popis"=>"\n","sirka"=>$cells['lf']['sirka'],"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>1),
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
    global $cells;
    $fill = 1;
	$pdfobjekt->SetFont("FreeSans", "", 6);
	$pdfobjekt->SetFillColor(255,255,200,1);
        $vyskaradku = $headervyskaradku;
        $key = 'persnr';
        $pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,  'PersNr',$cells[$key]['ram'],0,$cells[$key]['align'],$fill);

        $key = 'name';
        $pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,  'Name',$cells[$key]['ram'],0,$cells[$key]['align'],$fill);

        $sirkaSmlouvy=25;
        $maxSmluvNaRadek = 9;
        for($i=0;$i<$maxSmluvNaRadek;$i++){
            $pdfobjekt->Cell($sirkaSmlouvy/2,$vyskaradku,  'von:','LTB',0,'L',$fill);
            $pdfobjekt->Cell($sirkaSmlouvy/2,$vyskaradku,  'bis:','TBR',0,'L',$fill);
        }

        $pdfobjekt->Cell(0,$vyskaradku,  '','LRTB',0,'L',$fill);
        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
	$pdfobjekt->SetFont("FreeSans", "", 8);
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
			number_format(floatval(getValueForNode($nodelist,$nodename)), $cell["nf"][0],$cell["nf"][1],$cell["nf"][2]);
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
function zahlavi_person($pdfobjekt,$vyskaradku,$rgb,$person)
{
    global $cells;
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "", 8);

        $personChilds = $person->childNodes;

        $key = 'persnr';
        $pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,  getValueForNode($personChilds, $key),$cells[$key]['ram'],0,$cells[$key]['align'],$fill);

        $key = 'name';
        $pdfobjekt->Cell($cells[$key]['sirka'],$vyskaradku,  getValueForNode($personChilds, $key),$cells[$key]['ram'],0,$cells[$key]['align'],$fill);

        //a projedu smlouvy
        $sirkaSmlouvy=25;
        $maxSmluvNaRadek = 8;
        $vertraege = $person->getElementsByTagName("vertrag");
        $citacSmluv = 0;
        $rgbNovaSmlouva = array(240,255,240);
        $rgbVerlang = array(240,240,255);
        $rgbError = array(255,240,240);
        $rgb = array(245,245,245);
        $rgbAktual = $rgb;
        $pdfobjekt->SetFont("FreeSans", "", 7);
        foreach($vertraege as $vertrag){
            if($citacSmluv>$maxSmluvNaRadek){
                $pdfobjekt->Ln();
                $pdfobjekt->SetFillColor(255,255,255);
                $pdfobjekt->Cell($cells['persnr']['sirka']
                                +$cells['name']['sirka'],
                            $vyskaradku,
                            '',
                            '0',
                            0,
                            'L',$fill);
                $citacSmluv=0;
            }
            $vertragChilds = $vertrag->childNodes;
            $eintritt = getValueForNode($vertragChilds, 'eintrittF');
            $va = intval(getValueForNode($vertragChilds, 'vertrag_anfang'));
            $verlang = intval(getValueForNode($vertragChilds, 'verlang'));
            $befristet = trim(getValueForNode($vertragChilds, 'befristet'));
            $austritt = trim(getValueForNode($vertragChilds, 'austritt'));

//            echo "<br>austritt=$austritt".strlen($austritt).",befristet=$befristet".strlen($befristet);

            $konecSmlouvy = '';
            if((strlen($austritt)>0)) $konecSmlouvy = $austritt;
            if((strlen($befristet)>0)) $konecSmlouvy = $befristet;
            if((strlen($austritt)>0) && (strlen($befristet)>0)) $konecSmlouvy = $austritt;

            if($va>0) $rgbAktual = $rgbNovaSmlouva;
            if($verlang>0) $rgbAktual = $rgbVerlang;
            if($va>0 && $verlang>0) $rgbAktual = $rgbError;
            
            $pdfobjekt->SetFillColor($rgbAktual[0],$rgbAktual[1],$rgbAktual[2]);
            $pdfobjekt->Cell($sirkaSmlouvy/2,$vyskaradku,  $eintritt,'LBT',0,'L',$fill);
            $pdfobjekt->Cell($sirkaSmlouvy/2,$vyskaradku,  $konecSmlouvy,'BTR',0,'L',$fill);
            $rgbAktual = $rgb;
            $pdfobjekt->SetFillColor($rgbAktual[0],$rgbAktual[1],$rgbAktual[2]);
            $citacSmluv++;
        }

        $pdfobjekt->Ln();
	$pdfobjekt->SetFillColor($prevFillColor[0],$prevFillColor[1],$prevFillColor[2]);
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		pageheader($pdfobjekt,$cellhead,5);
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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S145 Personal Vertrag", $params);
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


$personen=$domxml->getElementsByTagName("person");
foreach($personen as $person)
{
    $eintrittArray = array();
    $personChilds = $person->childNodes;
    $befristet_a_Stamp = strtotime(getValueForNode($personChilds, 'befristet_a'));
    test_pageoverflow($pdf,5,$cells_header);
    $personShow = TRUE;
    if($roky2===TRUE){
        $personShow=FALSE;
        //echo "<br>roky2";
        // test na dvouletou dobu urcitou
        $vertraege = $person->getElementsByTagName("vertrag");
        // projdu vsechny smlouvy a ulozim si pocatky novych smluv po pole
        foreach ($vertraege as $vertrag){
            $vertragChilds = $vertrag->childNodes;
            $verlang = intval(getValueForNode($vertragChilds, 'verlang'));
            if($verlang==0){
                $eintritt = getValueForNode($vertragChilds, 'eintritt');
                $eintrittStamp = strtotime($eintritt);
                array_push($eintrittArray, $eintrittStamp);
            }
        }
        // pokud mam vice eintrittu v poli, tak spocitam dny mezi nima
        if(count($eintrittArray)>1){
            $startEintritt = $eintrittArray[0];
            $vedleEintritt = $startEintritt;
            foreach ($eintrittArray as $eintrittStamp){
                $rozdilDny = ($eintrittStamp - $vedleEintritt)/60/60/24;
                if($rozdilDny>=180){
                    $startEintritt=$eintrittStamp;
                }
                $vedleEintritt = $eintrittStamp;
                //echo "rozdil = $rozdilDny";
            }
        }
        else{
            $startEintritt = $eintrittArray[0];
        }
        $pocetDnuOdNastupu = (time()-$startEintritt)/60/60/24;
        $sekundDoBefristet = ($befristet_a_Stamp-time());
        // od prvniho nastupu uz je tady bez # mesicu 2 roky a ma smlouvu na dobu urcitou
        if(($pocetDnuOdNastupu>=(2*365-3*30)) && ($sekundDoBefristet>0))
            $personShow = TRUE;
    }

    if($personShow===TRUE){
//        $startEintrittF = date('Y-m-d',$startEintritt);
//        echo "<br>".getValueForNode($personChilds, 'persnr')."startEintritt :".$startEintrittF." pocet dnu od nastupu=$pocetDnuOdNastupu,befristet_a_Stamp=$befristet_a_Stamp,stamp=".time();
        zahlavi_person($pdf, 5, array(255,255,255),$person);
    }
    
    //telo($pdf,$cells,5,array(255,255,255),"",$personChilds);
}



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
