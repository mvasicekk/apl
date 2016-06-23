<?php
require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "S367";
$doc_subject = "S367 Report";
$doc_keywords = "S367";

// necham si vygenerovat XML
$parameters=$_GET;

// vytahnu paramety z _GET ( z getparameters.php )

$von=make_DB_datum($_GET['von']);
$bis=make_DB_datum($_GET['bis']);
$kdvon = $_GET['kdvon'];
$kdbis = $_GET['kdbis'];

$dnyvTydnu = array("Po","Ut","St","Ct","Pa","So","Ne");

$a = AplDB::getInstance();

// nechci zobrazit parametry
// vynuluju promennou $params
$params="";

// pole s sirkama bunek v mm, poradi v poli urcuje i poradi sloupcu
// v tabulce
// "klic" => array ("popisek sloupce",sirka_sloupce_v_mm)
// klic je shodny se jmenem nodeName v XML souboru
// poradi urcuje predevsim poradu nodu v XML !!!!!
// nf = pokus pole obsahuje tento klic bude se cislo v teto bunce formatovat dle parametru v poli 0,1,2


function test_pageoverflow_noheader($pdfobjekt,$vysradku)
{
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if(($pdfobjekt->GetY()+$vysradku)>($pdfobjekt->getPageHeight()-$pdfobjekt->getBreakMargin()))
    {
        //$pdfobjekt->AddPage();
        return TRUE;
    }
    return FALSE;
}



/**
 *
 * @param TCPDF $pdf
 * @param type $datumWidth
 * @param type $headerHeight
 * @param type $kundeNrArray
 */
function pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis) {
    $pdf->SetFillColor(255, 255, 230);
    $pdf->SetFont("FreeSans", "B", $s);
    $pdf->Cell($mntWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    $pdf->Cell($bewWidth, $rowHeight, '', 'LRT', 0, 'R', 1);
    $pdf->Cell(2 * $stkWidth, $rowHeight, "Sum $kdvon-$kdbis", 'LRT', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
        $pdf->Cell(2 * $stkWidth, $rowHeight, "$kd", 'LRT', 0, 'C', 1);
    }
    $pdf->Ln();

    $pdf->Cell($mntWidth, $rowHeight, 'Mnt.', 'LRB', 0, 'R', 1);
    $pdf->Cell($bewWidth, $rowHeight, 'Bew.', 'LRB', 0, 'R', 1);
    $pdf->Cell($stkWidth, $rowHeight, "E", 'LRTB', 0, 'C', 1);
    $pdf->Cell($stkWidth, $rowHeight, "I", 'LRTB', 0, 'C', 1);

    foreach ($kundenNrArray as $kd => $v) {
        $pdf->Cell($stkWidth, $rowHeight, "E", 'LRTB', 0, 'C', 1);
        $pdf->Cell($stkWidth, $rowHeight, "I", 'LRTB', 0, 'C', 1);
    }
    $pdf->Ln();
}

$kundeVon = $kdvon;
$kundeBis = $kdbis;
$datVon = $von;
$datBis = $bis;
$kundenNrArray = array();
$jahrMonatArray = array();
$bewertungArray = array();
$summeKunden = array();
$monatSummen = array();
$monatSummenKunden = array();
$gesamtSummen = array();
$gesamtSummenKunden = array();

$sql.=" select";
$sql.="     YEAR(dreklamation.rekl_datum) as jahr,";
$sql.="     MONTH(dreklamation.rekl_datum) as monat,";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.interne_bewertung,";
$sql.="     dreklamation.rekl_nr";
$sql.=" from dreklamation";
$sql.=" where";
$sql.="     kunde between '$kundeVon' and '$kundeBis'";
$sql.="     and dreklamation.rekl_datum between '$von' and '$bis'";
$sql.=" group by";
$sql.="     YEAR(dreklamation.rekl_datum),";
$sql.="     MONTH(dreklamation.rekl_datum),";
$sql.="     dreklamation.kunde,";
$sql.="     dreklamation.interne_bewertung,";
$sql.="     dreklamation.rekl_nr";

$reklArray = $a->getQueryRows($sql);

//AplDB::varDump($reklArray);
if($reklArray!==NULL){
    foreach ($reklArray as $rekl){
        $jahrMonat = sprintf("%04d-%02d",$rekl['jahr'],$rekl['monat']);
        $internExtern = strtoupper(substr($rekl['rekl_nr'], 0,1))=='E'?'E':'I';
        $kunde = $rekl['kunde'];
        $bewertung = $rekl['interne_bewertung'];
        $anzeigeArray[$jahrMonat][$bewertung][$kunde][$internExtern] += 1;
        $summeKunden[$jahrMonat][$bewertung][$internExtern] += 1;
        $kundenNrArray[$kunde] += 1;
        $jahrMonatArray[$jahrMonat] += 1;
        $bewertungArray[$bewertung] += 1;
        $monatSummen[$jahrMonat][$kunde][$internExtern]['reklamation'] += 1;
        $monatSummenKunden[$jahrMonat][$internExtern]['reklamation'] += 1;
        $monatSummen[$jahrMonat][$kunde][$internExtern]['bewertung'] += $bewertung*$bewertung;
        $monatSummenKunden[$jahrMonat][$internExtern]['bewertung'] += $bewertung*$bewertung;
        $gesamtSummen[$kunde][$internExtern]['reklamation'] += 1;
        $gesamtSummenKunden[$internExtern]['reklamation'] += 1;
        $gesamtSummen[$kunde][$internExtern]['bewertung'] += $bewertung*$bewertung;
        $gesamtSummenKunden[$internExtern]['bewertung'] += $bewertung*$bewertung;
    }
}

//AplDB::varDump($anzeigeArray);

ksort($kundenNrArray);
ksort($jahrMonatArray,SORT_STRING);
krsort($bewertungArray);


//AplDB::varDump($bewertungArray);

//AplDB::varDump($kundenNrArray);
$pocetZakazniku = count($kundenNrArray);

//AplDB::varDump($anzeigeArray);

// *************************************************************************************************** \\

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('L','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params = "Kunde $kdvon - $kdbis, Datum ".$_GET['von']."-".$_GET['bis'].", lt. ".$laut;
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "S367 Hodnocení reklamací", $params);
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
//$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);
$pdf->SetLineWidth(0.1);
// prvni stranka
$pdf->AddPage();

// *************************************************************************************************** \\
// pocet mesicu
$pocetMesicu = count($jahrMonatArray);
//AplDB::varDump($pocetMesicu);
// *************************************************************************************************** \\
$mntWidth = 10;
$bewWidth = 15;
$stkWidthMax = 20;
$stkWidthBerechnet = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$kwWidth)/($pocetZakazniku*3);
$stkWidth = $stkWidthBerechnet>$stkWidthMax?$stkWidthMax:$stkWidthBerechnet;
$rowHeight = 4;
// *************************************************************************************************** \\
//mesice vypocet velikost Cell
$sWidthMax = 20;
$sWidthBerechnet = ($pdf->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-$kwWidth)/($pocetMesicu+2.5) ;
$sWidth = $sWidthBerechnet>$sWidthMax?$sWidthMax:$sWidthBerechnet;
// *************************************************************************************************** \\
$pdf->SetFillColor(255,255,230);
//nastavit sirku fontu tak aby se vesel nejdelsi text do bunky

for($s=12;$s>1;$s-=0.5){
    $strWidth = $pdf->GetStringWidth("XXXXXXXX","FreeSans","B",$s);
    if($strWidth<=$stkWidth)	break;
}

$fontSize = $s;
// *************************************************************************************************** \\


pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);


//$pdf->SetFont("FreeSans", "", $s-1);
foreach ($jahrMonatArray as $jm=>$v1){
    foreach ($bewertungArray as $bew=>$v2){
        if(array_key_exists($bew, $anzeigeArray[$jm])){
            if(test_pageoverflow_noheader($pdf, $rowHeight)){
                $pdf->AddPage();
                pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
            }
            $pdf->SetFont("FreeSans", "", $s-1);
            $pdf->Cell($mntWidth,$rowHeight,  substr($jm,5),'LRTB',0,'C',0);
            $pdf->Cell($bewWidth,$rowHeight,$bew,'LRTB',0,'R',0);

            //suma pro vsechny zakazniky
            $pdf->Cell($stkWidth,$rowHeight,$summeKunden[$jm][$bew]['E'],'LRTB',0,'R',0);
            $pdf->Cell($stkWidth,$rowHeight,$summeKunden[$jm][$bew]['I'],'LRTB',0,'R',0);


            //jednotlivy zakaznici
            foreach ($kundenNrArray as $kunde=>$v3){
                if(array_key_exists($kunde, $anzeigeArray[$jm][$bew])){
                    $pdf->Cell($stkWidth,$rowHeight,$anzeigeArray[$jm][$bew][$kunde]['E'],'LRTB',0,'R',0);
                    $pdf->Cell($stkWidth,$rowHeight,$anzeigeArray[$jm][$bew][$kunde]['I'],'LRTB',0,'R',0);
                }
                else{
                    $pdf->Cell($stkWidth,$rowHeight,'','LRTB',0,'R',0);
                    $pdf->Cell($stkWidth,$rowHeight,'','LRTB',0,'R',0);
                }
            }
            $pdf->Ln();
        }
    }


    // sumy pro mesic
    //summe reklamation
    if(test_pageoverflow_noheader($pdf, $rowHeight)){
        $pdf->AddPage();
        pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
    }
    $fill = 1;
    $pdf->SetFillColor(230, 230, 255);
    $pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'Summe Rekl.','LRTB',0,'L',$fill);
    //suma pro vsechny zakazniky
    $pdf->Cell($stkWidth,$rowHeight,$monatSummenKunden[$jm]['E']['reklamation'],'LRTB',0,'R',$fill);
    $pdf->Cell($stkWidth,$rowHeight,$monatSummenKunden[$jm]['I']['reklamation'],'LRTB',0,'R',$fill);

    //jednotlivy zakaznici
    foreach ($kundenNrArray as $kunde=>$v3){
        $pdf->Cell($stkWidth,$rowHeight,$monatSummen[$jm][$kunde]['E']['reklamation'],'LRTB',0,'R',$fill);
        $pdf->Cell($stkWidth,$rowHeight,$monatSummen[$jm][$kunde]['I']['reklamation'],'LRTB',0,'R',$fill);
    }
    $pdf->Ln();

    //summe Bew.Gesamt
    if(test_pageoverflow_noheader($pdf, $rowHeight)){
        $pdf->AddPage();
        pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
    }
    $fill = 1;
    $pdf->SetFillColor(230, 230, 255);
    $pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'Bew.Gesamt','LRTB',0,'L',$fill);
    //suma pro vsechny zakazniky
    $pdf->Cell($stkWidth,$rowHeight,$monatSummenKunden[$jm]['E']['bewertung'],'LRTB',0,'R',$fill);
    $pdf->Cell($stkWidth,$rowHeight,$monatSummenKunden[$jm]['I']['bewertung'],'LRTB',0,'R',$fill);

    //jednotlivy zakaznici
    foreach ($kundenNrArray as $kunde=>$v3){
        $pdf->Cell($stkWidth,$rowHeight,$monatSummen[$jm][$kunde]['E']['bewertung'],'LRTB',0,'R',$fill);
        $pdf->Cell($stkWidth,$rowHeight,$monatSummen[$jm][$kunde]['I']['bewertung'],'LRTB',0,'R',$fill);
    }
    $pdf->Ln();

    //summe Punktebewertung
    if(test_pageoverflow_noheader($pdf, $rowHeight)){
        $pdf->AddPage();
        pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
    }
    $fill = 1;
    $pdf->SetFillColor(230, 230, 255);
    $pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'Punktebewertung','LRTB',0,'L',$fill);
    //suma pro vsechny zakazniky
    $bewE = $a->getBewertungKriterium(100, "q_S367_rekl", $monatSummenKunden[$jm]['E']['bewertung'], 'bis', substr($jm,2), 1);
    $bewI = $a->getBewertungKriterium(100, "q_S367_rekl", $monatSummenKunden[$jm]['I']['bewertung'], 'bis', substr($jm,2), 1);
    $gesamtSummenKunden['E']['punkte'] += $bewE;
    $gesamtSummenKunden['I']['punkte'] += $bewI;

    $pdf->Cell($stkWidth,$rowHeight,$bewE,'LRTB',0,'R',$fill);
    $pdf->Cell($stkWidth,$rowHeight,$bewI,'LRTB',0,'R',$fill);

    //jednotlivy zakaznici
    foreach ($kundenNrArray as $kunde=>$v3){
        $pdf->Cell($stkWidth,$rowHeight,'-','LRTB',0,'R',$fill);
        $pdf->Cell($stkWidth,$rowHeight,'-','LRTB',0,'R',$fill);
    }
    $pdf->Ln();
    $pdf->SetFillColor(255, 255, 255);$fill = 0;
}

// sumy pro sestavu
//summe reklamation
// vzdy na nove strance
$pdf->AddPage();
pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
$fill = 1;
$pdf->SetFillColor(230, 255, 230);
$pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'Summe Rekl.','LRTB',0,'L',$fill);
//suma pro vsechny zakazniky
$pdf->Cell($stkWidth,$rowHeight,$gesamtSummenKunden['E']['reklamation'],'LRTB',0,'R',$fill);
$pdf->Cell($stkWidth,$rowHeight,$gesamtSummenKunden['I']['reklamation'],'LRTB',0,'R',$fill);

//jednotlivy zakaznici
foreach ($kundenNrArray as $kunde=>$v3){
    $pdf->Cell($stkWidth,$rowHeight,$gesamtSummen[$kunde]['E']['reklamation'],'LRTB',0,'R',$fill);
    $pdf->Cell($stkWidth,$rowHeight,$gesamtSummen[$kunde]['I']['reklamation'],'LRTB',0,'R',$fill);
}
$pdf->Ln();

//summe Bew.Gesamt
if(test_pageoverflow_noheader($pdf, $rowHeight)){
    $pdf->AddPage();
    pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
}
$fill = 1;
$pdf->SetFillColor(230, 255, 230);
$pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'AVG Bew.','LRTB',0,'L',$fill);
//suma pro vsechny zakazniky
$e = count($jahrMonatArray)!=0?$gesamtSummenKunden['E']['bewertung']/count($jahrMonatArray):0;
$e = number_format($e,0,',',' ');
$i = count($jahrMonatArray)!=0?$gesamtSummenKunden['I']['bewertung']/count($jahrMonatArray):0;
$i = number_format($i,0,',',' ');
$pdf->Cell($stkWidth,$rowHeight,$e,'LRTB',0,'R',$fill);
$pdf->Cell($stkWidth,$rowHeight,$i,'LRTB',0,'R',$fill);

//jednotlivy zakaznici
foreach ($kundenNrArray as $kunde=>$v3){
    $e = count($jahrMonatArray)!=0?$gesamtSummen[$kunde]['E']['bewertung']/count($jahrMonatArray):0;
    $e = number_format($e,0,',',' ');
    $i = count($jahrMonatArray)!=0?$gesamtSummen[$kunde]['I']['bewertung']/count($jahrMonatArray):0;
    $i = number_format($i,0,',',' ');
    $pdf->Cell($stkWidth,$rowHeight,$e,'LRTB',0,'R',$fill);
    $pdf->Cell($stkWidth,$rowHeight,$i,'LRTB',0,'R',$fill);
}
$pdf->Ln();

//summe Punktebewertung
if(test_pageoverflow_noheader($pdf, $rowHeight)){
    $pdf->AddPage();
    pageHeader($pdf, $s, $rowHeight, $mntWidth, $bewWidth, $stkWidth, $kundenNrArray, $kdvon, $kdbis);
}
$fill = 1;
$pdf->SetFillColor(230, 255, 230);
$pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'AVG Punktebew.','LRTB',0,'L',$fill);
//suma pro vsechny zakazniky
$e = count($jahrMonatArray)!=0?$gesamtSummenKunden['E']['punkte']/count($jahrMonatArray):0;
$e = number_format($e,0,',',' ');
$i = count($jahrMonatArray)!=0?$gesamtSummenKunden['I']['punkte']/count($jahrMonatArray):0;
$i = number_format($i,0,',',' ');

$pdf->Cell($stkWidth,$rowHeight,$e,'LRTB',0,'R',$fill);
$pdf->Cell($stkWidth,$rowHeight,$i,'LRTB',0,'R',$fill);

//jednotlivy zakaznici
foreach ($kundenNrArray as $kunde=>$v3){
    $pdf->Cell($stkWidth,$rowHeight,'-','LRTB',0,'R',$fill);
    $pdf->Cell($stkWidth,$rowHeight,'-','LRTB',0,'R',$fill);
}
$pdf->Ln();
$pdf->SetFillColor(255, 255, 255);$fill = 0;


//legenda s hranicema
$krRows = $a->getBewertungKriteriumInfo(100, "q_S367_rekl", substr($jm,2));
$pdf->Ln();

$pdf->Cell(3*$mntWidth,$rowHeight,'Grenzen','LRTB',1,'L',$fill);
foreach ($krRows as $kr){
    $znaminko = $kr['bis_von']=='bis'?'<=':'>';
    $pdf->Cell($mntWidth,$rowHeight,$znaminko,'LRTB',0,'C',$fill);
    $pdf->Cell($mntWidth,$rowHeight,$kr['grenze'],'LRTB',0,'R',$fill);
    $pdf->Cell($mntWidth,$rowHeight,$kr['bewertung'],'LRTB',0,'R',$fill);
    $lastGr = $kr['grenze'];
    $pdf->Ln();
}
$pdf->Cell($mntWidth,$rowHeight,'>','LRTB',0,'C',$fill);
$pdf->Cell($mntWidth,$rowHeight,$lastGr,'LRTB',0,'R',$fill);
$pdf->Cell($mntWidth,$rowHeight, '6','LRTB',0,'R',$fill);



$pdf->AddPage();

// *************************************************************************************************** \\
// *************************************************************************************************** \\
// *************************************************************************************************** \\
// Mesice
$pdf->SetFillColor(230, 230, 255); //blue
$pdf->Cell($mntWidth+$bewWidth,$rowHeight, '','LRT',0,'C',1);

$monat = array('0' => 'Januar','1' => 'Februar', '2' => 'März', '3' => 'April', '4' => 'Mai', '5' => 'Juni', '6' => 'Juli', '7' => 'August', '8' => 'September', '9' => 'Oktober', '10' => 'November', '11' => 'December');
$monat = array_merge($monat, $monat);
for($i = 0; $i < $pocetMesicu;$i++){
    $pdf->Cell($sWidth, $rowHeight, $monat[$i], 'LRTB', 0, 'C', 1);
}



// *************************************************************************************************** \\
//SUMA
$pdf->SetFillColor(212,208,208); //grey
$pdf->Cell($sWidth, $rowHeight, 'SUMA', 'LRTB', 0, 'C', 1);
//
$pdf->Ln();
// *************************************************************************************************** \\
// E | I
$pdf->SetFillColor(230, 230, 255); //blue
$pdf->Cell($mntWidth+$bewWidth,$rowHeight, '','LRB',0,'C',1);
$pdf->SetFillColor(230, 230, 255); //blue
foreach($jahrMonatArray as $jm=>$v2){
    $pdf->Cell($sWidth/2, $rowHeight, "E", 'LRTB', 0, 'C', 1);
    $pdf->Cell($sWidth/2, $rowHeight, "I", 'LRTB', 0, 'C', 1);
}
//SUMA
$pdf->SetFillColor(212,208,208);
$pdf->Cell($sWidth/2, $rowHeight, "E", 'LRTB', 0, 'C', 1);
$pdf->Cell($sWidth/2, $rowHeight, "I", 'LRTB', 0, 'C', 1);
//
$pdf->Ln();

// *************************************************************************************************** \\
//suma za mesic => Summe Rekl.

$pdf->Cell($mntWidth+$bewWidth,$rowHeight, 'Summe Rekl.','LRTB',0,'L',0);
foreach($jahrMonatArray as $jm=>$v3){
    $pdf->Cell($sWidth/2,$rowHeight,$monatSummenKunden[$jm]['E']['reklamation'],'LRTB',0,'R',$fill);
    $pdf->Cell($sWidth/2,$rowHeight,$monatSummenKunden[$jm]['I']['reklamation'],'LRTB',0,'R',$fill);
}

//Kompletni suma
$pdf->SetFillColor(212,208,208); //grey
$pdf->Cell($sWidth/2,$rowHeight,$gesamtSummenKunden['E']['reklamation'],'LRTB',0,'R',1);
$pdf->Cell($sWidth/2,$rowHeight,$gesamtSummenKunden['I']['reklamation'],'LRTB',0,'R',1);
//

$pdf->Ln();
// *************************************************************************************************** \\
//suma za mesic => Bew.Gesamt.

$pdf->Cell($mntWidth+$bewWidth,$rowHeight, 'Bew.Gesamt','LRTB',0,'L',0);
foreach($jahrMonatArray as $jm=>$v4){
    $pdf->Cell($sWidth/2,$rowHeight,$monatSummenKunden[$jm]['E']['bewertung'],'LRTB',0,'R',$fill);
    $pdf->Cell($sWidth/2,$rowHeight,$monatSummenKunden[$jm]['I']['bewertung'],'LRTB',0,'R',$fill);
}
//Kompletni suma
$pdf->SetFillColor(212,208,208); //grey
$e = count($jahrMonatArray)!=0?$gesamtSummenKunden['E']['bewertung']/count($jahrMonatArray):0;
$e = number_format($e,0,',',' ');
$i = count($jahrMonatArray)!=0?$gesamtSummenKunden['I']['bewertung']/count($jahrMonatArray):0;
$i = number_format($i,0,',',' ');
$pdf->Cell($sWidth/2,$rowHeight,$e,'LRTB',0,'R',1);
$pdf->Cell($sWidth/2,$rowHeight,$i,'LRTB',0,'R',1);

//
$pdf->Ln();
// *************************************************************************************************** \\
//suma za mesic => Punktebewertung


$pdf->Cell($mntWidth+$bewWidth,$rowHeight,  'Punktebewertung','LRTB',0,'L',$fill);
foreach($jahrMonatArray as $jm=>$v5){
    $bewE = $a->getBewertungKriterium(100, "q_S367_rekl", $monatSummenKunden[$jm]['E']['bewertung'], 'bis', substr($jm,2), 1);
    $bewI = $a->getBewertungKriterium(100, "q_S367_rekl", $monatSummenKunden[$jm]['I']['bewertung'], 'bis', substr($jm,2), 1);
    $gesamtSummenKunden['E']['punkte'] += $bewE;
    $gesamtSummenKunden['I']['punkte'] += $bewI;

    $pdf->Cell($sWidth/2,$rowHeight,$bewE,'LRTB',0,'R',0);
    $pdf->Cell($sWidth/2,$rowHeight,$bewI,'LRTB',0,'R',0);
}
//Kompletni suma
$pdf->SetFillColor(212,208,208); //grey
$e = count($jahrMonatArray)!=0?$gesamtSummenKunden['E']['punkte']/count($jahrMonatArray):0;
$e = number_format($e,0,',',' ');
$i = count($jahrMonatArray)!=0?$gesamtSummenKunden['I']['punkte']/count($jahrMonatArray):0;
$i = number_format($i,0,',',' ');

$pdf->Cell($sWidth/2,$rowHeight,$e,'LRTB',0,'R',1);
$pdf->Cell($sWidth/2,$rowHeight,$i,'LRTB',0,'R',1);


$pdf->AddPage();

// *************************************************************************************************** \\
// *************************************************************************************************** \\
// *************************************************************************************************** \\
include("../Classes/pChart/class/pData.class.php");
include("../Classes/pChart/class/pDraw.class.php");
include("../Classes/pChart/class/pImage.class.php");

//AplDB::varDump($kundenNrArray);

//AplDB::varDump($monatSummenKunden);
$monatGraphArray = array();
foreach ($jahrMonatArray as $jm=>$v){
    $bewE = $monatSummenKunden[$jm]['E']['bewertung'];
    array_push($monatGraphArray,$bewE);
}

$avg = round($gesamtSummenKunden['E']['bewertung']/count($jahrMonatArray));
array_push($monatGraphArray,$avg);

$myData = new pData();
$myData->addPoints($monatGraphArray, "bew");
//$myData->setSerieDescription("bew", "Bew");
$myData->setSerieOnAxis("bew", 0);

$xSour = array_keys($jahrMonatArray);
array_push($xSour, "AVG");
$myData->addPoints($xSour, "Absissa");
$myData->setAbscissa("Absissa");

$myData->setAxisPosition(0, AXIS_POSITION_LEFT);
$myData->setAxisName(0, "Bewertung");
$myData->setAxisUnit(0, "");


$imgWidth = 1500;
$imgHeight = 1000;
$myPicture = new pImage($imgWidth, $imgHeight, $myData);
$Settings = array("StartR" => 231, "StartG" => 231, "StartB" => 97, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 50);
//$myPicture->drawGradientArea(0,0,700,400,DIRECTION_VERTICAL,$Settings);

//$myPicture->drawRectangle(0, 0, 699, 699, array("R" => 0, "G" => 0, "B" => 0));

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Medium.ttf", "FontSize" => 20));
$TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
, "R" => 42, "G" => 18, "B" => 255);
$myPicture->drawText($imgWidth/2, 25, "Externí reklamace od zakazniku $kdvon - $kdbis, obdobi $von - $bis", $TextSettings);

$myPicture->setGraphArea(100, 50, $imgWidth-200, $imgHeight-50);
$myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14));

$Settings = array("Pos" => SCALE_POS_LEFTRIGHT
, "Mode" => SCALE_MODE_START0
, "LabelingMethod" => LABELING_ALL
, "GridR" => 255, "GridG" => 255, "GridB" => 255, "GridAlpha" => 50, "TickR" => 0, "TickG" => 0, "TickB" => 0, "TickAlpha" => 50, "LabelRotation" => 0, "CycleBackground" => 1, "DrawXLines" => 0,"DrawYLines" => 0, "DrawSubTicks" => 0, "SubTickR" => 255, "SubTickG" => 0, "SubTickB" => 0, "SubTickAlpha" => 50);
$myPicture->drawScale($Settings);

$Palette = array("0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>100),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>100),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>100),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>100),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>100),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>100),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>100));

$Config = array(
    "AroundZero" => 0,
    "DisplayPos"=>LABEL_POS_INSIDE,
    "DisplayValues"=>TRUE,
    "Gradient"=>TRUE,
    "OverrideColors"=>$Palette,
);
$myPicture->drawBarChart($Config);

//    $myPicture->drawLineChart(array('Weight'=>10,'Width'=>10));
//    $myPicture->drawPlotChart(array('Weight'=>10,'Width'=>10));

$Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14, "Margin" => 6, "Alpha" => 30, "BoxSize" => 10, "Style" => LEGEND_BOX
, "Mode" => LEGEND_HORIZONTAL
);
//$myPicture->drawLegend($imgWidth-200, 16, $Config);

//$myPicture->stroke();
//toto taky funguje

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

foreach ($krRows as $kr){
    $myPicture->drawThreshold($kr['grenze'], array(
            "R"=>255,
            "G"=>0,
            "B"=>0,
            "DrawBox"=>TRUE,
            "BoxR"=>80,
            "BoxG"=>80,
            "BoxB"=>80,
            "BoxAlpha"=>200,
            "Alpha"=>255,
            "NoMargin"=>TRUE,
            "CaptionAlign"=>CAPTION_RIGHT_BOTTOM,
            //"CaptionOffset"=>TRUE,
            "OffsetX"=>50,
            "Ticks"=>1,
            "WriteCaption"=>TRUE,
            "Caption"=>"do ".$kr['grenze']." bodů = hodnocení ".$kr['bewertung'],
            "CaptionAlpha"=>255,
        )
    );
}

$myPicture->Render("S367_graf.png");
//$pdf->AddPage();
$y = $pdf->GetY();
$pdf->Image("S367_graf.png", PDF_MARGIN_LEFT, $y + 10, 260, 160, 'PNG');
// rozdeleni na kvadranty na A4 na sirku
//    $pdf->Image("S367_graf.png", PDF_MARGIN_LEFT, $y + 10, 130, 80, 'PNG');
//    $pdf->Image("S367_graf.png", PDF_MARGIN_LEFT+10+130, $y + 10, 130, 80, 'PNG');
//    $pdf->Image("S367_graf.png", PDF_MARGIN_LEFT, $y + 10+80, 130, 80, 'PNG');
//    $pdf->Image("S367_graf.png", PDF_MARGIN_LEFT+10+130, $y + 10+80, 130, 80, 'PNG');
$pdf->AddPage();
// *************************************************************************************************** \\
// *************************************************************************************************** \\
// *************************************************************************************************** \\

$monatGraphArray = array();
foreach ($jahrMonatArray as $jm=>$v){
    $bewE = $monatSummenKunden[$jm]['I']['bewertung'];
    array_push($monatGraphArray,$bewE);
}

$avg = round($gesamtSummenKunden['I']['bewertung']/count($jahrMonatArray));
array_push($monatGraphArray,$avg);

$myData = new pData();
$myData->addPoints($monatGraphArray, "bew");
//$myData->setSerieDescription("bew", "Bew");
$myData->setSerieOnAxis("bew", 0);

$xSour = array_keys($jahrMonatArray);
array_push($xSour, "AVG");
$myData->addPoints($xSour, "Absissa");
$myData->setAbscissa("Absissa");

$myData->setAxisPosition(0, AXIS_POSITION_LEFT);
$myData->setAxisName(0, "Bewertung");
$myData->setAxisUnit(0, "");


$imgWidth = 1500;
$imgHeight = 1000;
$myPicture = new pImage($imgWidth, $imgHeight, $myData);
$Settings = array("StartR" => 231, "StartG" => 231, "StartB" => 97, "EndR" => 1, "EndG" => 138, "EndB" => 68, "Alpha" => 50);
//$myPicture->drawGradientArea(0,0,700,400,DIRECTION_VERTICAL,$Settings);

//$myPicture->drawRectangle(0, 0, 699, 699, array("R" => 0, "G" => 0, "B" => 0));

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Medium.ttf", "FontSize" => 20));
$TextSettings = array("Align" => TEXT_ALIGN_MIDDLEMIDDLE
, "R" => 42, "G" => 18, "B" => 255);
$myPicture->drawText($imgWidth/2, 25, "Interní reklamace od zakazniku $kdvon - $kdbis, obdobi $von - $bis", $TextSettings);

$myPicture->setGraphArea(100, 50, $imgWidth-200, $imgHeight-50);
$myPicture->setFontProperties(array("R" => 0, "G" => 0, "B" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14));

$Settings = array("Pos" => SCALE_POS_LEFTRIGHT
, "Mode" => SCALE_MODE_START0
, "LabelingMethod" => LABELING_ALL
, "GridR" => 255, "GridG" => 255, "GridB" => 255, "GridAlpha" => 50, "TickR" => 0, "TickG" => 0, "TickB" => 0, "TickAlpha" => 50, "LabelRotation" => 0, "CycleBackground" => 1, "DrawXLines" => 0,"DrawYLines" => 0, "DrawSubTicks" => 0, "SubTickR" => 255, "SubTickG" => 0, "SubTickB" => 0, "SubTickAlpha" => 50);
$myPicture->drawScale($Settings);

$Palette = array("0"=>array("R"=>188,"G"=>224,"B"=>46,"Alpha"=>100),
    "1"=>array("R"=>224,"G"=>100,"B"=>46,"Alpha"=>100),
    "2"=>array("R"=>224,"G"=>214,"B"=>46,"Alpha"=>100),
    "3"=>array("R"=>46,"G"=>151,"B"=>224,"Alpha"=>100),
    "4"=>array("R"=>176,"G"=>46,"B"=>224,"Alpha"=>100),
    "5"=>array("R"=>224,"G"=>46,"B"=>117,"Alpha"=>100),
    "6"=>array("R"=>92,"G"=>224,"B"=>46,"Alpha"=>100),
    "7"=>array("R"=>224,"G"=>176,"B"=>46,"Alpha"=>100));

$Config = array(
    "AroundZero" => 0,
    "DisplayPos"=>LABEL_POS_INSIDE,
    "DisplayValues"=>TRUE,
    "Gradient"=>TRUE,
    "OverrideColors"=>$Palette,
);
$myPicture->drawBarChart($Config);

//    $myPicture->drawLineChart(array('Weight'=>10,'Width'=>10));
//    $myPicture->drawPlotChart(array('Weight'=>10,'Width'=>10));

$Config = array("FontR" => 0, "FontG" => 0, "FontB" => 0, "FontName" => "../Classes/pChart/fonts/Roboto-Light.ttf", "FontSize" => 14, "Margin" => 6, "Alpha" => 30, "BoxSize" => 10, "Style" => LEGEND_BOX
, "Mode" => LEGEND_HORIZONTAL
);
//$myPicture->drawLegend($imgWidth-200, 16, $Config);

//$myPicture->stroke();
//toto taky funguje

$myPicture->setFontProperties(array("FontName" => "../Classes/pChart/fonts/Roboto-Bold.ttf","FontSize"=>15,"R"=>0,"G"=>0,"B"=>0));

foreach ($krRows as $kr){
    $myPicture->drawThreshold($kr['grenze'], array(
            "R"=>255,
            "G"=>0,
            "B"=>0,
            "DrawBox"=>TRUE,
            "BoxR"=>80,
            "BoxG"=>80,
            "BoxB"=>80,
            "BoxAlpha"=>200,
            "Alpha"=>255,
            "NoMargin"=>TRUE,
            "CaptionAlign"=>CAPTION_RIGHT_BOTTOM,
            //"CaptionOffset"=>TRUE,
            "OffsetX"=>50,
            "Ticks"=>1,
            "WriteCaption"=>TRUE,
            "Caption"=>"do ".$kr['grenze']." bodů = hodnocení ".$kr['bewertung'],
            "CaptionAlpha"=>255,
        )
    );
}

$myPicture->Render("S3671_graf.png");
//$pdf->AddPage();
$y = $pdf->GetY();
$pdf->Image("S3671_graf.png", PDF_MARGIN_LEFT, $y + 10, 260, 160, 'PNG');




//Close and output PDF document
// *************************************************************************************************** \\
// *************************************************************************************************** \\
$pdf->Ln();
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+
