<?php
session_start();
require "./fns_dotazy.php";
require './db.php';
//
$apl = AplDB::getInstance();

//$persnrArray = array(104,122,2157);
//$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
//$jahr = 2010;
//foreach ($persnrArray as $persnr){
//    echo "<br>PersNr:$persnr";
//    $pmStundenE = $apl->getPlusMinusStunden(12, 2011, $persnr, '2010-12-31');
//    $pmStunden = $apl->getPlusMinusStunden(12, 2011, $persnr);
//    echo "<br>+-Stunden zu 31.12.2011:$pmStundenE, +-StundenGesamt zu 31.12.2011:$pmStunden";
//}

date_default_timezone_set('Europe/Prague');

/** PHPExcel */
require_once './Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

$user = get_user_pc();
// Set properties
//$objPHPExcel->getProperties()->setCreator($user)
//							 ->setLastModifiedBy($user)
//							 ->setTitle("pmStunden")
//							 ->setSubject("pmStunden")
//							 ->setDescription("pmStunden")
//							 ->setKeywords("office openxml php")
//							 ->setCategory("phpexcel");
//
//// popisky sloupcu
//$radek = 2;
//$sloupec = 1;
//
//$cells_header = array(
//    'persnr'=>array('popis'=>'persnr'),
//    'name'=>array('popis'=>'name'),
//    'pmStundenE'=>array('popis'=>'MehrStunden ab 1.1.2011'),
//    'pmStundenG'=>array('popis'=>'MehrStunden Gesamt'),
//);
//
//foreach($cells_header as $ch){
//    $popis = $ch['popis'];
//    $objPHPExcel->setActiveSheetIndex(0)
//            ->setCellValueByColumnAndRow($sloupec, $radek, $popis);
//    $sloupec++;
//}
//$radek++;
//$sloupec = 1;

$persnrArray = $apl->getPersnrFromEintritt('1990-01-01',TRUE);
foreach ($persnrArray as $persnr){
//   $sloupec = 1;
//   $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $persnr);
//   $sloupec++;
   $jmeno = '';
   $jmenoA =$apl->getPersonalArray($persnr);
   if($jmenoA!==NULL){
       $jmeno = $jmenoA[0]['name'].' '.$jmenoA[0]['vorname'];
   }
//   echo "<br>$jmeno";
   //$jmeno = mb_convert_encoding($jmeno, 'UTF-8');
//   $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $jmeno);
//   $sloupec++;
   $pmStundenE = $apl->getPlusMinusStunden(12, 2011, $persnr, '2010-12-31');
//   $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $pmStundenE);
//   $sloupec++;
   $pmStunden = $apl->getPlusMinusStunden(12, 2011, $persnr);
//   $objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($sloupec, $radek, $pmStunden);
//   $radek++;
   $vystup = sprintf("%d;%s;%f;%f<br>",$persnr,$jmeno,$pmStundenE,$pmStunden);
   echo "$vystup";
}

// Rename sheet
//$objPHPExcel->getActiveSheet()->setTitle('pmStunden');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
//$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel5)
//header('Content-Type: application/vnd.ms-excel');
//header('Content-Disposition: attachment;filename="pmStunden.xls"');
//header('Cache-Control: max-age=0');

//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//$objWriter->save('php://output');
exit;
