<?php
session_start();
require_once '../security.php';
require_once '../db.php';

$doc_title = "D729";
$doc_subject = "D729 Report";
$doc_keywords = "D729";

$parameters=$_GET;

$export=$_GET['export'];
require_once('../tcpdf_new/tcpdf.php');

$pdf = new TCPDF('P','mm','A4',1);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

$pdf->SetHeaderData("", 0, "D729", "");
$pdf->setRechnungFoot(FALSE);
//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT+5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
//$pdf->SetFooterMargin(PDF_MARGIN_FOOTER+3);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER+6);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor
$pdf->SetProtection(array('extract'), $pdfpass, '', 1);

$pdf->setHeaderFont(Array("FreeSans", '', 9));
$pdf->setFooterFont(Array("FreeSans", '', 8));

$pdf->setLanguageArray($l); //set language items

//initialize document
//$pdf->AliasNbPages();
$pdf->SetFont("FreeSans", "", 8);

// prvni stranka
$pdf->AddPage();

$a = AplDB::getInstance();
$exportInfo = $a->getAuftragInfoArray($export);

if ($exportInfo !== NULL) {
    $items = array(
	"export" => $export,
	"bemerkung" => $exportInfo[0]['bemerkung'],
	"aufdat"=>$exportInfo[0]['aufdat'],
	"fertig_raw" =>$exportInfo[0]['fertig_raw'],
	"im_soll_datetime" =>$exportInfo[0]['im_soll_datetime'],
	"ex_soll_datetime" =>$exportInfo[0]['ex_soll_datetime'],
	"bestellnr" =>$exportInfo[0]['bestellnr'],
	"zielort_id" =>$exportInfo[0]['zielort_id'],
	"kunde" =>$exportInfo[0]['kunde'],
	"minpreis" =>$exportInfo[0]['minpreis'],
	"ausliefer_datum" =>$exportInfo[0]['ausliefer_datum'],
	"ex_soll_datum" =>$exportInfo[0]['ex_soll_datum'],
	"ex_soll_uhrzeit" =>$exportInfo[0]['ex_soll_uhrzeit'],
    );

    $style = array(
	'position' => '',
	'align' => 'L',
	'stretch' => false,
	'fitwidth' => true,
	'cellfitalign' => '',
	'border' => FALSE,
	'hpadding' => 'auto',
	'vpadding' => 'auto',
	'fgcolor' => array(0, 0, 0),
	'bgcolor' => false, //array(255,255,255),
	'text' => FALSE,
	'font' => 'helvetica',
	'fontsize' => 8,
	'stretchtext' => 4
    );

    foreach ($items as $name => $item) {
	strlen($item)>0?$text = $item:$text="NOTEXT";
	$x = $pdf->GetX();
	$y = $pdf->GetY();
	$pdf->Cell(0, 18, $name.':'.$text, '1', 1, 'L');
	$textLength = $pdf->GetStringWidth($name.' '.$text);
	
	$pdf->write1DBarcode($text, 'C128', $x + $textLength, $y, '', 18, 0.4, $style, 'N');
	
    }
    $itemsJoined = join('*', $items);
	$pdf->AddPage();
	$pdf->Cell(0, 5, $itemsJoined, 'LRBT', 0, 'L', 0);
	$pdf->write2DBarcode($itemsJoined, 'DATAMATRIX', 10, 45, 25, 25, $style, 'N');
//	$pdf->write2DBarcode($itemsJoined, 'QRCODE,H', 10+25+10, 45, 25, 25, $style, 'N');
	
    $pdf->AddPage();
    $lieferscheinRows = $a->getLieferscheinRows($export,999);
    $lfString="";
    if($lieferscheinRows!==NULL){
	foreach ($lieferscheinRows as $lR){
	    $rowsString="";
	    foreach ($lR as $fieldname=>$fieldvalue){
		$rowsString.="$fieldname:$fieldvalue;";
	    }
	    $pdf->Cell(0, 5, $rowsString, 'LRBT', 0, 'L', 0);
	    $x=$pdf->GetX();$y=$pdf->GetY();
	    $pdf->write2DBarcode($rowsString, 'DATAMATRIX', $x-25, $y+5, 25, 25, $style, 'N');
//	    $pdf->write2DBarcode($rowsString, 'QRCODE,H', $x-25-30, $y+5, 25, 25, $style, 'N');

	    $pdf->Ln(25);
	    $lfString.=$rowsString."*";
	}
//	$pdf->AddPage();
////	echo "delka exportu ve znacich:".strlen($rowsString);
////	AplDB::varDump($rowsString);
//	$style = array(
//	'border' => 2,
//	'vpadding' => 'auto',
//	'hpadding' => 'auto',
//	'fgcolor' => array(0,0,0),
//	'bgcolor' => false, //array(255,255,255)
//	'module_width' => 2, // width of a single module in points
//	'module_height' => 2 // height of a single module in points
//	);
//	
//	$compressed = gzcompress($rowsString);
	
//	$pdf->write2DBarcode($rowsString, 'DATAMATRIX', 10, 20, 100, 100, $style, 'N');
	
//	$pdf->AddPage();
//	$pdf->write2DBarcode($compressed, 'DATAMATRIX', 10, 20, 50, 50, $style, 'N');
    }
} else {
    $pdf->Cell(0, 18, "Exportinfo ERROR", '1', 1, 'L');
}
$pdf->Output();
?>
