<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S170";
$doc_subject = "S170 Report";
$doc_keywords = "S170";

// necham si vygenerovat XML
$parameters=$_GET;
$user = $_SESSION['user'];

$password = $_GET['password'];
$qtyp = $_GET['qtyp'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

$fullAccess = testReportPassword("S170",$password,$user,0);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}

define(PERSNRWIDTH, 10);
define(QWIDTH, 13);
define(ISTSOLLWIDTH,QWIDTH/2);

require_once('S170_xml.php');

$apl = AplDB::getInstance();


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
//'persnr'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//'name'=> array ("popis"=>"","sirka"=>33,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
//'eintritt'=> array ("popis"=>"","sirka"=>17,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
//'austritt'=> array ("popis"=>"","sirka"=>17,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
//'regelarbzeit'=> array ("nf"=>array(1,',',' '),"popis"=>"","sirka"=>10,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
//'regeloe'=> array ("popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>0),
//'alteroe'=> array ("popis"=>"","sirka"=>10,"ram"=>'RB',"align"=>"L","radek"=>0,"fill"=>0),
//'lohnfaktor'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>10,"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>0),
//'leistfaktor'=> array ("nf"=>array(2,',',' '),"popis"=>"","sirka"=>8,"ram"=>'RB',"align"=>"R","radek"=>0,"fill"=>0),
//'qpremie_akkord'=> array ("popis"=>"","sirka"=>8,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//'qpremie_zeit'=> array ("popis"=>"","sirka"=>8,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//'premie_za_vykon'=> array ("popis"=>"","sirka"=>8,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//'premie_za_3_mesice'=> array ("popis"=>"","sirka"=>8,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
////'premie_za_prasnost'=> array ("popis"=>"","sirka"=>8,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//'bewertung'=> array ("popis"=>"","sirka"=>8,"ram"=>'BR',"align"=>"R","radek"=>0,"fill"=>0),
//'regeltrans'=> array ("nf"=>array(0,',',' '),"popis"=>"","sirka"=>10,"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>0),
//'lf'=> array ("popis"=>"","sirka"=>0,"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>0),
    );


$cells_header = 
array(
//'persnr'=> array ("popis"=>"\nPersnr","sirka"=>$cells['persnr']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'name'=> array ("popis"=>"\nName","sirka"=>$cells['name']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
//'eintritt'=> array ("popis"=>"\nEintritt","sirka"=>$cells['eintritt']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
//'austritt'=> array ("popis"=>"\nAustritt","sirka"=>$cells['austritt']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
//'regelarbzeit'=> array ("nf"=>array(1,',',' '),"popis"=>"Regel-\narbzeit","sirka"=>$cells['regelarbzeit']['sirka'],"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),
//'regeloe'=> array ("popis"=>"Regel\nOE","sirka"=>$cells['regeloe']['sirka'],"ram"=>'B',"align"=>"L","radek"=>0,"fill"=>1),
//'alteroe'=> array ("popis"=>"Alter\nOE","sirka"=>$cells['alteroe']['sirka'],"ram"=>'RB',"align"=>"L","radek"=>0,"fill"=>1),
//'lohnfaktor'=> array ("nf"=>array(0,',',' '),"popis"=>"Std-\nLohn","sirka"=>$cells['lohnfaktor']['sirka'],"ram"=>'LB',"align"=>"R","radek"=>0,"fill"=>1),
//'leistfaktor'=> array ("nf"=>array(2,',',' '),"popis"=>"Leist-\nfaktor","sirka"=>$cells['leistfaktor']['sirka'],"ram"=>'RB',"align"=>"R","radek"=>0,"fill"=>1),
//'qpremie_akkord'=> array ("popis"=>"Q\nAkkord","sirka"=>$cells['qpremie_akkord']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'qpremie_zeit'=> array ("popis"=>"Q\nZeit","sirka"=>$cells['qpremie_zeit']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'premie_za_vykon'=> array ("popis"=>"\nLeist","sirka"=>$cells['premie_za_vykon']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'premie_za_3_mesice'=> array ("popis"=>"\nQTL","sirka"=>$cells['premie_za_3_mesice']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
////'premie_za_prasnost'=> array ("popis"=>"\nErsch.","sirka"=>$cells['premie_za_prasnost']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'bewertung'=> array ("popis"=>"\nBewert","sirka"=>$cells['bewertung']['sirka'],"ram"=>'RB',"align"=>"R","radek"=>0,"fill"=>1),
//'regeltrans'=> array ("nf"=>array(0,',',' '),"popis"=>"Trans-\nport","sirka"=>$cells['regeltrans']['sirka'],"ram"=>'B',"align"=>"R","radek"=>0,"fill"=>1),
//'lf'=> array ("popis"=>"\n","sirka"=>$cells['lf']['sirka'],"ram"=>'B',"align"=>"BR","radek"=>1,"fill"=>1),
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


function person_zeile($pdfobjekt,$vyskaradku,$rgb,$childs,$personQualifikationen,$qualArray){
    	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $persnr = getValueForNode($childs, 'persnr');
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->Cell(PERSNRWIDTH,$vyskaradku,$persnr,'LRBT',0,'R',$fill);
        $name = getValueForNode($childs, 'name');
        $vorname = getValueForNode($childs, 'vorname');
        $name = $name." ".$vorname;
        $regelOE = getValueForNode($childs, 'regeloe');

        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*3,$vyskaradku,$name,'LRBT',0,'L',$fill);

        $pdfobjekt->SetFont("FreeSans", "", 5.5);
        $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$regelOE,'LRBT',0,'L',$fill);

        //vytvorim si pole kvalifikaci pro konkretniho cloveka
        $persQArray = array();
        foreach ($personQualifikationen as $personQualifikation){
            $personQualifikationChilds = $personQualifikation->childNodes;
            $id = getValueForNode($personQualifikationChilds, 'id_faehigkeit');
            $soll = getValueForNode($personQualifikationChilds, 'soll');
            $ist = getValueForNode($personQualifikationChilds, 'ist');

            $persQArray[$id]['soll'] = $soll;
            $persQArray[$id]['ist'] = $ist;
        }
        // budu prochazet pole s moznyma kvalifikacema
        $pdfobjekt->SetFont("FreeSans", "", 8);
        foreach ($qualArray as $qualifikation){
            $idQual = $qualifikation['id'];
            // test , zda persQArray obsahuje klid id = $idQual
            if(array_key_exists($idQual, $persQArray)){
                $ist = $persQArray[$idQual]['ist']==0?'':$persQArray[$idQual]['ist'];
                $soll = $persQArray[$idQual]['soll']==0?'':$persQArray[$idQual]['soll'];
                $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$soll,'LRBT',0,'R',$fill);
                $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$ist,'LRBT',0,'R',$fill);
            }
            else
                $pdfobjekt->Cell(QWIDTH,$vyskaradku,'','LRBT',0,'R',$fill);
        }
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'R',$fill);
}

function zahlavi_Qualifikationen($pdfobjekt,$vyskaradku,$rgb,$QArray){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        //misto pro persnr
        $pdfobjekt->SetFont("FreeSans", "B", 6.5);
        $pdfobjekt->Cell(PERSNRWIDTH*4+ISTSOLLWIDTH,$vyskaradku,'','LRBT',0,'L',$fill);
        foreach ($QArray as $qualifikation){
            $id = $qualifikation['id'];
            $abkrz = $qualifikation['faeh_abkrz'];
            //$obsah = "( ".$id." ) ".$abkrz;
            // povoleny obsah sestavy

            $obsah = $abkrz;
            $pdfobjekt->Cell(QWIDTH,$vyskaradku,$obsah,'LRBT',0,'L',$fill);
        }
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);

        // druhy radek zahlavi s popiskama ist, soll
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*4+ISTSOLLWIDTH,$vyskaradku,'Person','LRBT',0,'L',$fill);
        foreach ($QArray as $qualifikation){
            $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,'soll','LRBT',0,'R',$fill);
            $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,'ist','LRBT',0,'R',$fill);
        }
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
}

function zapati_Qualifikationen($pdfobjekt,$vyskaradku,$rgb,$QArray,$anzahlArrayAktuell,$anzahlArrayAll){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        //misto pro persnr
//        $pdfobjekt->SetFont("FreeSans", "B", 6.5);
//        $pdfobjekt->Cell(PERSNRWIDTH*4+ISTSOLLWIDTH,$vyskaradku,'','LRBT',0,'L',$fill);
//        foreach ($QArray as $qualifikation){
//            $id = $qualifikation['id'];
//            $abkrz = $qualifikation['faeh_abkrz'];
//            //$obsah = "( ".$id." ) ".$abkrz;
//            // povoleny obsah sestavy
//
//            $obsah = $abkrz;
//            $pdfobjekt->Cell(QWIDTH,$vyskaradku,$obsah,'LRBT',0,'L',$fill);
//        }
//        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);

        // radek Anzahl Aktuell
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*4+ISTSOLLWIDTH,$vyskaradku,'Anzahl(>=7) Aktuell','LRBT',0,'L',$fill);
        foreach ($QArray as $qualifikation){
            $id = $qualifikation['id'];
            if(array_key_exists($id, $anzahlArrayAktuell)){
                $obsahIst = $anzahlArrayAktuell[$id]['ist'];
                $obsahSoll = $anzahlArrayAktuell[$id]['soll'];
            }
            else{
                $obsahIst = '';
                $obsahSoll = '';
            }
            $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$obsahSoll,'LRBT',0,'R',$fill);
            $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$obsahIst,'LRBT',0,'R',$fill);
        }
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);

        // radek Anzahl Alle
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*4+ISTSOLLWIDTH,$vyskaradku,'Anzahl(>=7) Alle','LRBT',0,'L',$fill);
        foreach ($QArray as $qualifikation){
            $id = $qualifikation['id'];
            if(array_key_exists($id, $anzahlArrayAll)){
                $obsahIst = $anzahlArrayAll[$id]['ist'];
                $obsahSoll = $anzahlArrayAll[$id]['soll'];
            }
            else{
                $obsahIst = '';
                $obsahSoll = '';
            }
            $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$obsahSoll,'LRBT',0,'R',$fill);
            $pdfobjekt->Cell(ISTSOLLWIDTH,$vyskaradku,$obsahIst,'LRBT',0,'R',$fill);
        }
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
}
function zahlavi_Q_typ($pdfobjekt,$vyskaradku,$rgb,$childs){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $beschreibung = getValueForNode($childs, 'Q_typ_beschreibung');
        $id = getValueForNode($childs, 'Q_typ_id');
        $statNr = getValueForNode($childs, 'Q_typ_statnr');
        $obsah = $beschreibung." ( ".$statNr." )";
        $pdfobjekt->SetFont("FreeSans", "B", 11);
        $pdfobjekt->Cell(0,$vyskaradku,$obsah,'LRBT',1,'L',$fill);
}

function test_pageoverflow($pdfobjekt,$vysradku,$cellhead)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		$pdfobjekt->AddPage();
		//pageheader($pdfobjekt,$cellhead,3.5);
		$pdfobjekt->Ln();
		$pdfobjekt->Ln();
	}
}
				
function test_pageoverflow_noNewPage($pdfobjekt,$vysradku)
{
	// pokud bych prelezl s nasledujicim vystupem vysku stranky
	// tak vytvorim novou stranku i se zahlavim
	if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
	{
		return TRUE;
	}
        else
                return FALSE;
}
				
require_once('../tcpdf/config/lang/eng.php');
require_once('../tcpdf/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S170 Personal Qualifikationen", $params);
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
//$pdf->AddPage();
//pageheader($pdf,$cells_header,5);


$Q_typen=$domxml->getElementsByTagName("Q_typ");
$pdf->AddPage();

foreach($Q_typen as $Q_typ)
{
    $anzahlQualifikationenAktuell = array();
    $anzahlQualifikationenAll = array();

    $Q_typ_Childs = $Q_typ->childNodes;
  
    $typid = getValueForNode($Q_typ_Childs,'Q_typ_id');
    $QualifikationenArray = $apl->getQualifikationenProQTyp($typid);
    if($QualifikationenArray!==NULL){
        zahlavi_Q_typ($pdf,10,array(200,255,200),$Q_typ_Childs);
        zahlavi_Qualifikationen($pdf,5,array(255,255,200),$QualifikationenArray);
    }
    $personen = $Q_typ->getElementsByTagName("person");
    foreach($personen as $person){
        $personChilds = $person->childNodes;
        
        $personQualifikationen = $person->getElementsByTagName("faehigkeit");
        if(test_pageoverflow_noNewPage($pdf, 5)){
            $pdf->AddPage();
            zahlavi_Q_typ($pdf,10,array(200,255,200),$Q_typ_Childs);
            zahlavi_Qualifikationen($pdf,5,array(255,255,200),$QualifikationenArray);
        }
        person_zeile($pdf,5,array(255,255,255),$personChilds,$personQualifikationen,$QualifikationenArray);
        // aktualizovat pocty kvalifikaci
        foreach ($personQualifikationen as $persQualifikation) {
            $persQualifikationChilds = $persQualifikation->childNodes;
            $id = getValueForNode($persQualifikationChilds, 'id_faehigkeit');
            $soll = getValueForNode($persQualifikationChilds, 'soll');
            $ist = getValueForNode($persQualifikationChilds, 'ist');
            if($soll>=7) $anzahlQualifikationenAktuell[$id]['soll'] += 1;
            if($ist>=7) $anzahlQualifikationenAktuell[$id]['ist'] += 1;
            if(abs($soll)>=7) $anzahlQualifikationenAll[$id]['soll'] += 1;
            if(abs($ist)>=7) $anzahlQualifikationenAll[$id]['ist'] += 1;
        }
    }
    if($QualifikationenArray!==NULL){
        if(test_pageoverflow_noNewPage($pdf, 10)){
            $pdf->AddPage();
            zahlavi_Q_typ($pdf,10,array(200,255,200),$Q_typ_Childs);
            zahlavi_Qualifikationen($pdf,5,array(255,255,200),$QualifikationenArray);
        }
        zapati_Qualifikationen($pdf,5,array(255,255,200),$QualifikationenArray,$anzahlQualifikationenAktuell,$anzahlQualifikationenAll);
    }
}



//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
