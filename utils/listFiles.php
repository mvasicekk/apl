<?php
session_start();
//require './fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();


//var_dump(AplDB::$DIRS_FOR_TEIL);

$teil = '05063962';
$kunde = $apl->getKundeFromTeil($teil);
$kundeGdatPath = $apl->getKundeGdatPath($kunde);
$ppaDir='';
$att = 'ppa';

$gdatPath = "/mnt/gdat/Dat/";

    // seznam dilu
    if ($kundeGdatPath !== NULL) {
	$ppaDir = $gdatPath . $kundeGdatPath . "/" . $teil . "/" . AplDB::$DIRS_FOR_TEIL["003"];
	$docsArray = $apl->getFilesForPath($ppaDir,'/.*.(pdf|xls)$/');
    }

    
    $formDiv = "<div id='dokuform'>";
    $formDiv.="<table id='dokutable'>";
    $formDiv.="<tr><td style='font-size:x-small;' colspan='6'>".substr($ppaDir,10)."</td></tr>";
    if ($docsArray !== NULL) {
    $formDiv.="<tr>";
    $formDiv.="<td class='filetableheader' style='' colspan='4'>Datei / soubor</td>";
    $formDiv.="<td class='filetableheader' style='width:160px;'>Datum</td>";
    $formDiv.="<td class='filetableheader' style='width:120px;text-align:right;'>Size</td>";

    $formDiv.="</tr>";
    $i = 0;
    foreach ($docsArray as $doc) {
	    $trclass = $i++ % 2 == 0 ? 'sudy' : 'lichy';
	    $formDiv.="<tr class='$trclass'>";
	    $formDiv.="<td colspan='4'><a href='" . $doc['url'] . "'>" . $doc['filename'] . "</a></td>";
	    $formDiv.="<td>" . date('Y-m-d H:i:s', $doc['mtime']) . "</td>";
	    $formDiv.="<td style='text-align:right;'>" . number_format(floatval($doc['size']), 0, ',', ' ') . "</td>";
	    $formDiv.="</tr>";
    }
}
$formDiv.="</table>";
$formDiv.= "</div>";
    
echo $formDiv;

// seznam dilu
