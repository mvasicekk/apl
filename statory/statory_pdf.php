<?php

require_once '../security.php';
require_once "../fns_dotazy.php";
require_once '../db.php';

$doc_title = "Statory";
$doc_subject = "Statory Report";
$doc_keywords = "Statory";

// necham si vygenerovat XML
$parameters = $_GET;
$a = AplDB::getInstance();
// nechci zobrazit parametry
// vynuluju promennou $params

//$stator = $_GET['stator'];
//$stator = "212y";

$params = "";
$dnyvTydnu = array("Po", "Ut", "St", "Ct", "Pa", "So", "Ne");

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P', 'mm', 'A4', 1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);


$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Seznam statorů ");
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP - 10, PDF_MARGIN_RIGHT);
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

//------------------------------------------------------------------------------



$sql = "select dstator.stator,dstator_pal.typ as typ, dstator.vyrazen_datum,dstator.bemerkung,dstator.warning,dstator.danger from dstator join dstator_pal on dstator_pal.id = dstator.paleta where vyrazen_datum like '0000-00-00'  ";
$res = $a->getQueryRows($sql);


//AplDB::varDump($aussArray);
//AplDB::varDump($val);



//***************************************************************************************************************************\\
$stkWidth = 20;
$rowHeight = 6;
$pgwidrh = $pdf->getPageWidth();
//***************************************************************************************************************************\\
$pdf->AddPage();

$pdf->SetFont("FreeSans", "", 8);
foreach ($res as $value) {
  //AplDB::varDump($value);
  $stator = $value['stator'];
  $typ = $value['typ'];
  //echo "stator stator";
  //('".$value['stator']."')
  $sql2 =  "select drueck.datum,drueck.TaetNr as tat,drueck.PersNr as pers,dstator.bemerkung as bem,dstator.warning as warn,dstator.danger as dang, drueck.drueck_id, dstator_pal.typ, dstator.vyrazen_datum from drueckplus join drueck on drueck.drueck_id=drueckplus.drueck_id join dstator on dstator.stator = drueckplus.et_invnummer join dstator_pal on dstator_pal.id = dstator.paleta where drueck.TaetNr like '8720' and drueckplus.et_invnummer like ('".$value['stator']."')";
  $aussArray = $a->getQueryRows($sql2);

  //AplDB::varDump($aussArray);
    $pdf->SetFillColor(255, 255, 230);
  $pdf->Cell('', $rowHeight, "Číslo statoru: ".$stator. " "."Typ:  ".$typ, '', 0, 'L', 1);

  $pdf->ln();
  if($aussArray !== null){
    foreach ($aussArray as $val) {
      $createDate = new DateTime($val['datum']);

        $strip = $createDate->format('Y-m-d');
        $pocetPrevinuti = count($strip);
        $pdf->Cell($stkWidth, $rowHeight, $strip, '', 0, 'C', 0);

      }
    }

  $pdf->ln();

}
//Close and output PDF document
// *************************************************************************************************** \\
// *************************************************************************************************** \\
$pdf->Ln();
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+
