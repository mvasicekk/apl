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

foreach ($res as $key => $value) {
  //AplDB::varDump($value);
  $sql1 = "select dreparaturpos.et_invnummer as drEtin,
           dreparaturkopf.id as drId, dreparaturkopf.bemerkung as drRepBemer ,dreparaturkopf.persnr_ma  as drPersMa,dreparatur_anlagen.anlage_bemerkung,
           dreparaturkopf.persnr_reparatur as drPersVon,  dreparaturkopf.invnummer,dreparaturkopf.datum as drDate
           from dreparaturpos
           join dreparaturkopf on dreparaturkopf.id = dreparaturpos.reparatur_id
           join dreparatur_geraete on  dreparatur_geraete.invnummer = dreparaturkopf.invnummer
           join dreparatur_anlagen on dreparatur_anlagen.anlage_id = dreparatur_geraete.anlage_id
           where dreparaturpos.et_invnummer
           like ('".$value['stator']."') order by dreparaturkopf.datum desc limit 1";
  $res1 = $a->getQueryRows($sql1);
  //AplDB::varDump($res1);
  $sql2 =  " select drueckplus.et_invnummer ,drueck.datum,drueck.TaetNr as tat,drueck.PersNr as pers,dstator.bemerkung as bem,dstator.warning as warn,
             dstator.danger as dang, drueck.drueck_id, dstator_pal.typ, dstator.vyrazen_datum from drueckplus
             join drueck on drueck.drueck_id=drueckplus.drueck_id
             join dstator on dstator.stator = drueckplus.et_invnummer
             join dstator_pal on dstator_pal.id = dstator.paleta
             where
             dstator.stator like ('".$value['stator']."')
             order by drueck.datum desc, drueck.TaetNr desc limit 1";
  $res2 = $a->getQueryRows($sql2);
  //AplDB::varDump($res2);
/*
  if($value !== null && $res1!==null){
    foreach ($res1 as $ke => $valu){
      if($valu !== null && $res2 !== null){
        foreach ($res2 as $k => $val) {
          if($valu['drDate'] >= $val['datum'] && $val['tat'] =='8740'){
            echo "v masine ". $value['stator']." ".$value['typ']." od: ". $valu['drDate']. "<br>";
          }else{
            echo $val['tat']." ".$val['datum']." ". $value['stator']." ".$value['typ']. "<br>";
          }
        }
    }
  }
}
*/
  if($res2!==null){
foreach ($res2 as $k => $val) {
  if($val!==null && $res1!==null){
  foreach ($res1 as $ke => $valu){

    if($valu['drDate'] >= $val['datum']){
      //echo  $value['stator'];
      echo "v masine ". $value['stator']." ".$value['typ']." od: ". $valu['drDate']. "<br>";
    }else{
      echo $val['tat']." ".$val['datum']." ". $value['stator']." ".$value['typ']. "<br>";
    }
  }
}
}
}

}


//***************************************************************************************************************************\\
$stkWidth = 20;
$rowHeight = 6;
$pgwidrh = $pdf->getPageWidth();
//***************************************************************************************************************************\\
$pdf->AddPage();

$pdf->SetFont("FreeSans", "", 8);

//Close and output PDF document
// *************************************************************************************************** \\
// *************************************************************************************************** \\
$pdf->Ln();
$pdf->Output();

//============================================================+
// END OF FILE
//============================================================+
