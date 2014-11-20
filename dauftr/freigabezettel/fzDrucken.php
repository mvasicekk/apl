<?php
require_once '../../security.php';
require '../../db.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$doc_title = "D64X";
$doc_subject = "D64X Report";
$doc_keywords = "D64X";

$pole = split(';', $_POST['pole']);

require('../../fpdf/transform.php');

$pdf = new PDF_Transform('P','mm','A4',1);
//$pdf = new TCPDF('L','mm','A4',1);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->AddPage();

$pdf->Cell(0, 20, 'pdf test', '1');

//if(strlen(trim($export))==0){
//
//if($format=="6x auf A4"){
//for($radek=0;$radek<3;$radek++)
//    for($sloupec=0;$sloupec<2;$sloupec++)
//        drawBehaelterBox($pdf, $radek, $sloupec,$popisek,$watermark);
//}
//
//if($format=="2x auf A4"){
//    drawBehaelterBoxA5($pdf, $radek, $sloupec, $popisek, $watermark,$palChildNodes,$teil);
//}
//}
//else
//{
//    // jdu po exportech
//    $exporte = $domxml->getElementsByTagName("export");
//    $citacPalet = 0;
//    foreach($exporte as $ex){
//        $exChildNodes = $ex->childNodes;
//        $importe = $ex->getElementsByTagName("import");
//        foreach($importe as $import){
//            $importChildNodes = $import->childNodes;
//            $paletten = $import->getElementsByTagName("pal");
//            foreach($paletten as $pal){
//                if($format=="6x auf A4"){
//                    $palChildNodes = $pal->childNodes;
//                    $zbytek = $citacPalet % 6;
//                    $radek = floor($zbytek/2);
//                    $sloupec = $zbytek % 2;
//                    if(($zbytek==0)&&($citacPalet>5)) $pdf->AddPage();
//                    drawBehaelterBoxChilds($pdf, $radek, $sloupec, $popisek, $watermark,$palChildNodes);
//                    $citacPalet++;
//                }
//                if($format=="2x auf A4"){
//                    $palChildNodes = $pal->childNodes;
////                    $radek = $citacPalet % 2;
//                    $radek = 0;
//                    $sloupec = 0;
////                    if($radek==0 && $citacPalet>1) $pdf->AddPage();
//                    if($citacPalet>0) $pdf->AddPage();
//                    drawBehaelterBoxChildsA5($pdf, $radek, $sloupec, $popisek, $watermark,$palChildNodes);
//                    $citacPalet++;
//                }
//            }
//        }
//    }
//}


//Close and output PDF document
$pdf->Output();

//-------------------------------------------------------------------------------------------------------------------------

//$apl = AplDB::getInstance();
//
//
//$id = $_POST['id'];
//$teil = $_POST['value'];
//
//$row = $apl->getVerpackungMenge($teil);
//
//if($row!=NULL){
//    $teil = $row['teil'];
//    $verpackungmenge = intval($row['verpackungmenge']);
//}
//else{
//    $teil = NULL;
//    $verpackungmenge = 0;
//}
//
//
//$value = array('pole'=>$pole);
//echo json_encode($value);


?>
