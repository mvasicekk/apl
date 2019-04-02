<?php
require_once '../security.php';
//require_once "../fns_dotazy.php";
require_once "../db.php";

$doc_title = "F460";
$doc_subject = "F460";
$doc_keywords = "F460";

// necham si vygenerovat XML
$apl = AplDB::getInstance();

$data = file_get_contents("php://input");

$o = json_decode($data);

$sklad = $_GET['sklad'];

//$datum = date('m-d', strtotime($_GET['datum'] .' -4 day'));
$datum = date('Y-m-d', strtotime($_GET['datum']));
//$sklad = "6"
//$datum = "2017-11-30";
//echo $datum;
function test_pageoverflow_noheader($pdfobjekt, $vysradku) {
    // pokud bych prelezl s nasledujicim vystupem vysku stranky
    // tak vytvorim novou stranku i se zahlavim
    if (($pdfobjekt->GetY() + $vysradku) > ($pdfobjekt->getPageHeight() - $pdfobjekt->getBreakMargin())) {
        $pdfobjekt->AddPage();
        return TRUE;
    }
    return FALSE;
}
function pageHeaderMain($pdf, $rowHeight) {
    $pdf->SetFont("FreeSans", "", 7);
    $pdf->SetFillColor(255, 255, 230);
    $pdf->Cell( 15, $h = 0, $txt = 'INV', $border = 'LRTB', $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'L' );
    $pdf->Cell( 70, $h = 0, $txt = 'Popis', $border = 'LRTB', $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'L' );
    $pdf->Cell( 17, $h = 0, $txt = 'Požadavek', $border = 'LRTB', $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'L' );
    $pdf->Cell( 15, $h = 0, $txt = 'Kdo', $border = 'LRTB', $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'L' );
    $pdf->Cell( 15, $h = 0, $txt = 'Vydáno', $border = 'LRTB', $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'L' );
    $pdf->Cell( 50, $h = 0, $txt = 'Poznamka', $border = 'LRTB', $ln = 0, $align = 'L', $fill = true, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'L' );


}

//$datum2 = date('Y')."-".date('m-d', strtotime($_GET['datum'] .' -4 day'));
$datum2 = $datum;
require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$params="";
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "F460 - Fasování",$datum2);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP-10, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items
$pdf->SetFont("FreeSans", "", 9);
$pdf->SetAutoPageBreak(FALSE,15);

$pdf->setPrintHeader(TRUE);
$pdf->setPrintFooter(TRUE);
$pdf->setCellPaddings(3, 1, 3, 1);
//$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false);


// sklad
$sql = "Select cislo, popis, poznamka, show_from from sez_skl_isp where cislo like '$sklad' ";

$ret = $apl->getQueryRows($sql);
//AplDB::varDump($ret);

// data z vydeje
$sql1 = "select id,inv,popis,pers_kdo,poznamka,vracena,pocet_ks,show_from,show_to,ks_vydano,reklamace from vydej where DATE_FORMAT(stamp,'%Y-%m-%d')='$datum' and show_to like '$sklad' and Typ like '20'  order by stamp desc ";
$result = $apl->getQueryRows($sql1);
//AplDB::varDump($result);



// prvni stranka
$pdf->AddPage();
$pdf->SetFont("FreeSans", "B", 14);
$pdf->ln();
$pgw = $pdf->getPageWidth();
foreach ($ret as $r){
    $pdf->MultiCell($w,
        $h,
        $r['popis'],
        $border = 0,
        $align = 'C',
        $fill = false,
        $ln = 1,
        $x = '',
        $y = '',
        $reseth = true,
        $stretch = 0,
        $ishtml = false,
        $autopadding = true,
        $maxh = 0,
        $valign = 'T',
        $fitcell = false );
}
pageHeaderMain($pdf, $rowHeight);


$pdf->ln();
foreach ($result as $re){
    if (test_pageoverflow_noheader($pdf, $rowHeight)) {
        pageHeaderMain($pdf, $rowHeight);
        $pdf->Ln();
    }
    $pdf->MultiCell( 15, 8, $re['inv'], $border = 'LRTB', $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 15, $valign = 'T', $fitcell = false );
    $pdf->MultiCell( 70, 8, $re['popis'], $border = 'LRTB', $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 15, $valign = 'T', $fitcell = false );
    $pdf->MultiCell( 17, 8, $re['pocet_ks'], $border = 'LRTB', $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 15, $valign = 'T', $fitcell = false );
    $pdf->MultiCell( 15, 8, $re['pers_kdo'], $border = 'LRTB', $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 15, $valign = 'T', $fitcell = false );
    $pdf->MultiCell( 15, 8, $re['ks_vydano'], $border = 'LRTB', $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 15, $valign = 'T', $fitcell = false );
    $pdf->MultiCell( 50, 8, $re['poznamka'], $border = 'LRTB', $align = 'L', $fill = false, $ln = 0, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 15, $valign = 'T', $fitcell = false );

    $pdf->ln();
}


/*
MultiCell( $w, $h,
 $txt, $border = 0,
$align = 'J',
$fill = false, $ln = 1, $x = '', $y = '', $reseth = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false )


Cell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M' )
*/

$pdf->Output();