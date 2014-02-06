<?php
session_start();
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S171";
$doc_subject = "S171 Report";
$doc_keywords = "S171";

// necham si vygenerovat XML
$parameters=$_GET;
$user = $_SESSION['user'];

$password = $_GET['password'];
//$qtyp = $_GET['qtyp'];
$persvon = $_GET['persvon'];
$persbis = $_GET['persbis'];

$fullAccess = testReportPassword("S170",$password,$user,0);

if(!$fullAccess)
{
    echo "Nemate povoleno zobrazeni teto sestavy / Sie sind nicht berechtigt dieses Report zu starten.";
    exit;
}

define(PERSNRWIDTH, 10);

require_once('S171_xml.php');

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
    );


$cells_header = 
array(
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


function person_zeile($pdfobjekt,$vyskaradku,$rgb,$persnr,$personQualifikationen,$qualArray,$qWidth){

        $pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;
        $pdfobjekt->SetFont("FreeSans", "B", 8);
        $pdfobjekt->Cell(PERSNRWIDTH,$vyskaradku,$persnr,'LRBT',0,'R',$fill);
        $name = $personQualifikationen['name'];
        $vorname = $personQualifikationen['vorname'];
        $name = $name." ".$vorname;
        $regelOE = $personQualifikationen['regeloe'];
	$univ = $personQualifikationen['univerzalista']=="0"?"":"UNI";

        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*3,$vyskaradku,$name,'LRBT',0,'L',$fill);

        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($qWidth/2,$vyskaradku,$regelOE,'LRBT',0,'L',$fill);

        $pdfobjekt->SetFont("FreeSans", "", 4.5);
        $pdfobjekt->Cell($qWidth/2,$vyskaradku,$univ,'LRBT',0,'L',$fill);

        //vytvorim si pole kvalifikaci pro konkretniho cloveka
        $persQArray = array();
        foreach ($personQualifikationen['qualifikationen'] as $idqual=>$personQualifikation){
            $id = $idqual;
            $soll = $personQualifikation['soll'];
            $ist = $personQualifikation['ist'];

            $persQArray[$id]['soll'] = $soll;
            $persQArray[$id]['ist'] = $ist;
        }
        // budu prochazet pole s moznyma kvalifikacema
        $pdfobjekt->SetFont("FreeSans", "", 7.5);
        foreach ($qualArray as $qualifikation){
            $idQual = $qualifikation['id'];
            // test , zda persQArray obsahuje klid id = $idQual
            if(array_key_exists($idQual, $persQArray)){
                $ist = $persQArray[$idQual]['ist']==0?'':$persQArray[$idQual]['ist'];
                $soll = $persQArray[$idQual]['soll']==0?'':$persQArray[$idQual]['soll'];
                $pdfobjekt->Cell($qWidth/2,$vyskaradku,$soll,'LRBT',0,'R',$fill);
                $pdfobjekt->Cell($qWidth/2,$vyskaradku,$ist,'LRBT',0,'R',$fill);
            }
            else
                $pdfobjekt->Cell($qWidth,$vyskaradku,'','LRBT',0,'R',$fill);
        }
        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'R',$fill);
}

function zahlavi_Qualifikationen($pdfobjekt,$vyskaradku,$rgb,$QArray,$sirkaQ){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        $statnrArray = array();
        $statnrNameArray = array();
        // zjistim kolik ruznych statnr obsahuje pole QArray
        foreach ($QArray as $qualifikation){
            $statnrArray[$qualifikation['statnr']] += 1;
            $statnrNameArray[$qualifikation['statnr']] = $qualifikation['qtbeschr'];
        }
        // nakreslim zahlavi pro statnr
        //misto pro persnr
        $pdfobjekt->SetFont("FreeSans", "B", 6);
        //$pdfobjekt->Cell(PERSNRWIDTH*4+$sirkaQ/2,$vyskaradku,'','LRT',0,'L',$fill);
	$pdfobjekt->Cell(PERSNRWIDTH*4+$sirkaQ,$vyskaradku,'','LRT',0,'L',$fill);
        foreach ($statnrArray as $statnr=>$pocetsloupcu){
            $obsah = $statnr;//.' - '.$statnrNameArray[$statnr];
            $pdfobjekt->Cell($sirkaQ*$pocetsloupcu,$vyskaradku,$obsah,'LRBT',0,'C',$fill);
        }
        $pdfobjekt->Ln();
        //misto pro persnr
        $pdfobjekt->SetFont("FreeSans", "B", 5);
        $pdfobjekt->Cell(PERSNRWIDTH*4+$sirkaQ,$vyskaradku,'','LR',0,'L',$fill);
        foreach ($QArray as $qualifikation){
            $id = $qualifikation['id'];
            $abkrz = $qualifikation['faeh_abkrz'];
            //$obsah = "( ".$id." ) ".$abkrz;
            // povoleny obsah sestavy

            $obsah = $abkrz;
            $pdfobjekt->Cell($sirkaQ,$vyskaradku,$obsah,'LRBT',0,'L',$fill);
        }
//        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
        $pdfobjekt->Ln();

        // druhy radek zahlavi s popiskama ist, soll
        $pdfobjekt->SetFont("FreeSans", "", 5);
        $pdfobjekt->Cell(PERSNRWIDTH*4+$sirkaQ/2,$vyskaradku,'Person','LRBT',0,'L',$fill);
	$pdfobjekt->Cell($sirkaQ/2,$vyskaradku,'Uni','LRBT',0,'L',$fill);
        foreach ($QArray as $qualifikation){
            $pdfobjekt->Cell($sirkaQ/2,$vyskaradku,'soll','LRBT',0,'R',$fill);
            $pdfobjekt->Cell($sirkaQ/2,$vyskaradku,'ist','LRBT',0,'R',$fill);
        }
//        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
        $pdfobjekt->Ln();
}

function zapati_Qualifikationen($pdfobjekt,$vyskaradku,$rgb,$QArray,$anzahlArrayAktuell,$anzahlArrayAll,$qWidth){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        // radek Anzahl Aktuell
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*4+$qWidth,$vyskaradku,'Anzahl(>=7) Aktuell','LRBT',0,'L',$fill);
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
            $pdfobjekt->Cell($qWidth/2,$vyskaradku,$obsahSoll,'LRBT',0,'R',$fill);
            $pdfobjekt->Cell($qWidth/2,$vyskaradku,$obsahIst,'LRBT',0,'R',$fill);
        }
//        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
        $pdfobjekt->Ln();

        // radek Anzahl Alle
        $pdfobjekt->SetFont("FreeSans", "", 7);
        $pdfobjekt->Cell(PERSNRWIDTH*4+$qWidth,$vyskaradku,'Anzahl(>=7) Alle','LRBT',0,'L',$fill);
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
            $pdfobjekt->Cell($qWidth/2,$vyskaradku,$obsahSoll,'LRBT',0,'R',$fill);
            $pdfobjekt->Cell($qWidth/2,$vyskaradku,$obsahIst,'LRBT',0,'R',$fill);
        }
//        $pdfobjekt->Cell(0,$vyskaradku,'','0',1,'L',$fill);
        $pdfobjekt->Ln();
}

/**
 *
 * @global type $apl
 * @param TCPDF $pdfobjekt
 * @param type $vyskaradku
 * @param type $rgb
 * @param type $anzahlArrayAktuell
 * @param type $anzahlArrayAll
 * @param type $anzahlArrayAll0
 * @param type $qWidth 
 */
function zapati_QualifikationenQTyp($pdfobjekt,$vyskaradku,$rgb,$anzahlArrayAktuell,$anzahlArrayAll,$anzahlArrayAll0,$qWidth){
	$pdfobjekt->SetFillColor($rgb[0],$rgb[1],$rgb[2],1);
	$fill=1;

        global $apl;
        
        $yStart = $pdfobjekt->GetY();
        
        $pdfobjekt->Ln();
        $pdfobjekt->Cell(PERSNRWIDTH * 4 + $qWidth / 2, $vyskaradku, "Anzahl(soll/ist>=7) Aktuell", 'LRBT', 0, 'L', $fill);
        $pdfobjekt->Cell($qWidth, $vyskaradku, 'soll:', 'LRBT', 0, 'L', $fill);
        $pdfobjekt->Cell($qWidth, $vyskaradku, 'ist:', 'LRBT', 1, 'L', $fill);
        
        $fill=0;
        foreach ($anzahlArrayAktuell as $statnr => $anzArray) {
            // radek Anzahl Aktuell
            $statnrBeschr = $apl->getQualifikationsTypenArray($statnr);
            $statnrBeschr = $statnrBeschr[0]['typ'];
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $obsah = $statnr.' - '.$statnrBeschr;
            $pdfobjekt->Cell(PERSNRWIDTH * 4 + $qWidth / 2, $vyskaradku, $obsah, 'LRBT', 0, 'L', $fill);
            $pdfobjekt->Cell($qWidth, $vyskaradku, $anzArray['soll'], 'RBT', 0, 'R', $fill);
            $pdfobjekt->Cell($qWidth, $vyskaradku, $anzArray['ist'], 'RBT', 0, 'R', $fill);
            $pdfobjekt->Ln();
        }

        $pdfobjekt->Ln();
        $fill=1;
        $pdfobjekt->Cell(PERSNRWIDTH * 4 + $qWidth / 2, $vyskaradku, "Anzahl(abs(soll/ist)>=7) Alle", 'LRBT', 0, 'L', $fill);
        $pdfobjekt->Cell($qWidth, $vyskaradku, 'soll:', 'LRBT', 0, 'L', $fill);
        $pdfobjekt->Cell($qWidth, $vyskaradku, 'ist:', 'LRBT', 1, 'L', $fill);
        
        $fill=0;
        foreach ($anzahlArrayAll as $statnr => $anzArray) {
            // radek Anzahl Aktuell
            $statnrBeschr = $apl->getQualifikationsTypenArray($statnr);
            $statnrBeschr = $statnrBeschr[0]['typ'];
            $obsah = $statnr.' - '.$statnrBeschr;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(PERSNRWIDTH * 4 + $qWidth / 2, $vyskaradku, $obsah, 'LRBT', 0, 'L', $fill);
            $pdfobjekt->Cell($qWidth, $vyskaradku, $anzArray['soll'], 'RBT', 0, 'R', $fill);
            $pdfobjekt->Cell($qWidth, $vyskaradku, $anzArray['ist'], 'RBT', 0, 'R', $fill);
            $pdfobjekt->Ln();
        }

        $pdfobjekt->SetY($yStart);$pdfobjekt->SetLeftMargin(100);
//        $pdfobjekt->Ln();
        $fill=1;
        $pdfobjekt->Cell(PERSNRWIDTH * 4 + $qWidth / 2, $vyskaradku, "Anzahl(soll/ist>=1) Alle", 'LRBT', 0, 'L', $fill);
        $pdfobjekt->Cell($qWidth, $vyskaradku, 'soll:', 'LRBT', 0, 'L', $fill);
        $pdfobjekt->Cell($qWidth, $vyskaradku, 'ist:', 'LRBT', 1, 'L', $fill);
        
        $fill=0;
        foreach ($anzahlArrayAll0 as $statnr => $anzArray) {
            // radek Anzahl Aktuell
            $statnrBeschr = $apl->getQualifikationsTypenArray($statnr);
            $statnrBeschr = $statnrBeschr[0]['typ'];
            $obsah = $statnr.' - '.$statnrBeschr;
            $pdfobjekt->SetFont("FreeSans", "", 7);
            $pdfobjekt->Cell(PERSNRWIDTH * 4 + $qWidth / 2, $vyskaradku, $obsah, 'LRBT', 0, 'L', $fill);
            $pdfobjekt->Cell($qWidth, $vyskaradku, $anzArray['soll'], 'RBT', 0, 'R', $fill);
            $pdfobjekt->Cell($qWidth, $vyskaradku, $anzArray['ist'], 'RBT', 0, 'R', $fill);
            $pdfobjekt->Ln();
        }

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

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S171 Personal Qualifikationen ( Q0011-Q0061 )", $params);
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
// spocitat pocet sloupcu, abyvedel jak mam udelat sirkoky jeden sloupec s kvalifikaci
$pocetSloupcu = 0;

$personalQualifikationen = array();
$anzQualifikationenQTypAktuell = array();
$anzQualifikationenQTypAll = array();
$anzQualifikationenQTypAll0 = array();
$anzQualifikationenQTypPersNrAktuell = array();
$anzQualifikationenQTypPersNrAll = array();
$anzQualifikationenQTypPersNrAll0 = array();

foreach($Q_typen as $qtyp){
    $Q_typ_Childs = $qtyp->childNodes;
    $typid = getValueForNode($Q_typ_Childs,'Q_typ_id');
    $typidstat = getValueForNode($Q_typ_Childs,'Q_typ_statnr');
    $QualifikationenArray = $apl->getQualifikationenProQTyp($typid);
    $pocetSloupcu += count($QualifikationenArray);
    $sirkaProKvalifikace = $pdf->getPageWidth()-(PERSNRWIDTH*4)-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT;
    $sirkaKvalifikace = $sirkaProKvalifikace / ($pocetSloupcu+1);
    $anzQualifikationenQTypAll[$typidstat]['ist']=0;
    $anzQualifikationenQTypAll0[$typidstat]['ist']=0;
    $anzQualifikationenQTypAktuell[$typidstat]['ist']=0;
    $anzQualifikationenQTypAll[$typidstat]['soll']=0;
    $anzQualifikationenQTypAll0[$typidstat]['soll']=0;
    $anzQualifikationenQTypAktuell[$typidstat]['soll']=0;

    // zaroven si pripravim pole s osobama a soucty kvalifikaci
    $personen = $qtyp->getElementsByTagName("person");
    foreach($personen as $person){
        $personChilds = $person->childNodes;
        $persnr = getValueForNode($personChilds, 'persnr');
        $personalQualifikationen[$persnr]['name'] = getValueForNode($personChilds, 'name');
        $personalQualifikationen[$persnr]['vorname'] = getValueForNode($personChilds, 'vorname');
        $personalQualifikationen[$persnr]['regeloe'] = getValueForNode($personChilds, 'regeloe');
	$personalQualifikationen[$persnr]['univerzalista'] = getValueForNode($personChilds, 'univerzalista');
        $personQualifikationen = $person->getElementsByTagName("faehigkeit");
        // aktualizovat pocty kvalifikaci
        $QTypCounted = FALSE;
        foreach ($personQualifikationen as $persQualifikation) {
            $persQualifikationChilds = $persQualifikation->childNodes;
            $id = getValueForNode($persQualifikationChilds, 'id_faehigkeit');
            $soll = getValueForNode($persQualifikationChilds, 'soll');
            $ist = getValueForNode($persQualifikationChilds, 'ist');
            $personalQualifikationen[$persnr]['qualifikationen'][$id]['soll'] = $soll;
            $personalQualifikationen[$persnr]['qualifikationen'][$id]['ist'] = $ist;

            if($soll>=1){
                if($anzQualifikationenQTypPersNrAll0[$typidstat][$persnr]['soll']==0)
                    $anzQualifikationenQTypAll0[$typidstat]['soll']+=1;
                $anzQualifikationenQTypPersNrAll0[$typidstat][$persnr]['soll']+=1;
            }

           if($ist>=1){
                if($anzQualifikationenQTypPersNrAll0[$typidstat][$persnr]['ist']==0)
                    $anzQualifikationenQTypAll0[$typidstat]['ist']+=1;
                $anzQualifikationenQTypPersNrAll0[$typidstat][$persnr]['ist']+=1;
            }

            if($soll>=7){
                $anzahlQualifikationenAktuell[$id]['soll'] += 1;
                if($anzQualifikationenQTypPersNrAktuell[$typidstat][$persnr]['soll']==0)
                    $anzQualifikationenQTypAktuell[$typidstat]['soll']+=1;
                $anzQualifikationenQTypPersNrAktuell[$typidstat][$persnr]['soll']+=1;
            }
            
            if($ist>=7){
                $anzahlQualifikationenAktuell[$id]['ist'] += 1;
                if($anzQualifikationenQTypPersNrAktuell[$typidstat][$persnr]['ist']==0)
                    $anzQualifikationenQTypAktuell[$typidstat]['ist']+=1;
                $anzQualifikationenQTypPersNrAktuell[$typidstat][$persnr]['ist']+=1;
            }
            
            if(abs($soll)>=7) {
                $anzahlQualifikationenAll[$id]['soll'] += 1;
                if($anzQualifikationenQTypPersNrAll[$typidstat][$persnr]['soll']==0)
                    $anzQualifikationenQTypAll[$typidstat]['soll']+=1;
                $anzQualifikationenQTypPersNrAll[$typidstat][$persnr]['soll']+=1;
            }
            
            if(abs($ist)>=7) {
                $anzahlQualifikationenAll[$id]['ist'] += 1;
                if($anzQualifikationenQTypPersNrAll[$typidstat][$persnr]['ist']==0)
                    $anzQualifikationenQTypAll[$typidstat]['ist']+=1;
                $anzQualifikationenQTypPersNrAll[$typidstat][$persnr]['ist']+=1;
            }
            
        }
    }

}

//sort($personalQualifikationen);
$osobniCisla = array_keys($personalQualifikationen);
sort($osobniCisla);
//foreach ($osobniCisla as $persnr){
//echo "<br>persnr = $persnr";
//}
//echo "<pre>";
//var_dump($personalQualifikationen);
//echo "</pre>";
//echo "<pre>";
//var_dump($anzQualifikationenQTypPersNrAktuell);
//echo "</pre>";


//echo "<pre>";
//var_dump($anzQualifikationenQTypAktuell);
//echo "</pre>";

//--------------------------------------------------------------------------------------------------------------
// zahlavi s vybranyma kvalifikacema
$qArray = array();
foreach($Q_typen as $qtyp){
    $Q_typ_Childs = $qtyp->childNodes;
    $typid = getValueForNode($Q_typ_Childs,'Q_typ_id');
    $statnr = getValueForNode($Q_typ_Childs,'Q_typ_statnr');
    $qtbeschr = getValueForNode($Q_typ_Childs,'Q_typ_beschreibung');

    $QualifikationenArray = $apl->getQualifikationenProQTyp($typid);
    foreach ($QualifikationenArray as $q){
        $q['statnr'] = $statnr;
        $q['qtbeschr'] = $qtbeschr;
        array_push($qArray, $q);
    }
}

//echo "<pre>";
//var_dump($qArray);
//echo "</pre>";

zahlavi_Qualifikationen($pdf,5,array(255,255,200),$qArray,$sirkaKvalifikace);

// a zacnu vypisovat vsechny persnr
foreach ($osobniCisla as $persnr){
    if(test_pageoverflow_noNewPage($pdf, 10)){
            $pdf->AddPage();
                        
            zahlavi_Qualifikationen($pdf,5,array(255,255,200),$qArray,$sirkaKvalifikace);
        }
    person_zeile($pdf,5,array(255,255,255),$persnr,$personalQualifikationen[$persnr],$qArray,$sirkaKvalifikace);
}
//riuh  fiihue  owijoiwj owfiuwhw
//woij   oijoij nd n
//
// a nakonec soucty pro kvalifikace
if(test_pageoverflow_noNewPage($pdf, 10)){
    $pdf->AddPage();
    zahlavi_Qualifikationen($pdf,5,array(255,255,200),$qArray,$sirkaKvalifikace);
}
zapati_Qualifikationen($pdf,5,array(255,255,200),$qArray,$anzahlQualifikationenAktuell,$anzahlQualifikationenAll,$sirkaKvalifikace);

$pdf->AddPage();
zapati_QualifikationenQTyp($pdf,5,array(240,255,240),$anzQualifikationenQTypAktuell,$anzQualifikationenQTypAll,$anzQualifikationenQTypAll0,$sirkaKvalifikace);

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+

?>
