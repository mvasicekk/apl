<?php
require_once './db.php';

$aplDB = AplDB::getInstance();

$oes = $aplDB->getOESForOEStatus('a');
$cvsoutputHeader = "";

$columnNames = array("poradi","OE","stamp");
foreach($columnNames as $columnName){
    $cvsoutputHeader .= '"'.$columnName.'",';
}
// osdtranim posledni carku
$cvsoutputHeader = substr($cvsoutputHeader, 0, strlen($cvsoutputHeader)-1);
$cvsoutputHeader .= "\r\n";

$cvsoutput .= $cvsoutputHeader;
$poradi = 1;
foreach($oes as $oe){
    $cvsoutput .= $poradi++.','.'"'.$oe.'",'.'"'.date('Y-m-d').'"'."\015\012";
}


//  //You cannot have the breaks in the same feed as the content.
  header("Content-type: application/vnd.ms-excel");
  header("Content-disposition: csv; filename=document_" . date("Ymd") .
".csv");
  print $cvsoutput;
  exit;
