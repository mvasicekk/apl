<?php
session_start();
//require './fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();

//var_dump(AplDB::$DIRS_FOR_TEIL);
$kunde = 195;
$kundeGdatPath = $apl->getKundeGdatPath($kunde);
$gdatPath = "/mnt/gdat/Dat/";

$teileArray = $apl->getTeileNrArrayForKunde($kunde);
$filePath = $gdatPath.'Aby 11 Kunden ab 19.02.2014/355/Putzanweisungen ab 10-07-19 neu';

foreach ($teileArray as $teil){
    $original = trim($teil['teillang']);
    echo "Teil: ".$teil['teil'].", original: ".$original;
    $source = $filePath;
    $dest = $gdatPath.$kundeGdatPath."/200 Teile/".$teil['teil'].'/030 PPA/'.$ppa['filename'];
    echo "<br>from: $source".", to: $dest";
    //copy($source, $dest);
    echo "<hr>";
}
    

