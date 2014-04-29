<?php
session_start();
//require './fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();

//var_dump(AplDB::$DIRS_FOR_TEIL);
$kunde = 355;
$kundeGdatPath = $apl->getKundeGdatPath($kunde);
$gdatPath = "/mnt/gdat/Dat/";

$teileArray = $apl->getTeileNrArrayForKunde($kunde);
$ppaPath = $gdatPath.'Aby 11 Kunden ab 19.02.2014/355/Putzanweisungen ab 10-07-19 neu';
$ppaArray = $apl->getFilesForPath($ppaPath);

foreach ($teileArray as $teil){
    $original = trim($teil['teillang']);
    
    echo "Teil: ".$teil['teil'].", original: ".$original;
    foreach ($ppaArray as $ppa){
	$pregout = preg_match("/$original/", $ppa['filename'], $matches);
	if($pregout>0){
	    $source = $ppaPath.'/'.$ppa['filename'];
	    $dest = $gdatPath.$kundeGdatPath."/200 Teile/".$teil['teil'].'/030 PPA/'.$ppa['filename'];
	    echo "<br>from: $source".", to: $dest";
	    //copy($source, $dest);
	}
	    
    }
    echo "<hr>";
}


// seznam dilu
//if ($kundeGdatPath !== NULL) {
//    $teileArray = $apl->getTeileNrArrayForKunde($kunde);
//    if ($teileArray !== NULL) {
//	foreach ($teileArray as $row) {
//	    $teilDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $row["teil"];
//	    // test zda uz takova slozka existuje
//	    $dirExists = file_exists($teilDir)?"exists":"not exists";
//	    if($dirExists=="not exists"){
//		// vytvorit slozku pro dil
//		if(mkdir($teilDir,0777,TRUE)){
//		    echo $teilDir." created<br>";
//		}
//		else{
//		    echo $teilDir." could not be created<br>";
//		}
//	    }
//	    if(file_exists($teilDir)){
//		foreach (AplDB::$DIRS_FOR_TEIL_FINAL as $dirForTeil){
//		    $dirForTeilPath = $teilDir."/".$dirForTeil;
//		    if(!file_exists($dirForTeilPath)){
//			mkdir($dirForTeilPath);
//			echo "$dirForTeilPath created<br>";
//		    }
//		    else{
//			echo "$dirForTeilPath exists<br>";
//		    }
//		}
//	    }
//	}
//    }
//}
//
//echo "<hr>";
//
//// 5. uroven Archived, in Arbeit
//$dirsWhereToMake5Level = array(	"020"=>"020 EMPB",
//	"030"=>"030 PPA",
//	"040"=>"040 GPA",
//	"050"=>"050 VPA",
//);
//
//$level5Dirs = array("010 Ausgearbeitet","020 Archiv");
//
//if ($kundeGdatPath !== NULL) {
//    $teileArray = $apl->getTeileNrArrayForKunde($kunde);
//    if ($teileArray !== NULL) {
//	foreach ($teileArray as $row) {
//	    $teilDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $row["teil"];
//	    if (file_exists($teilDir)) {
//		foreach ($dirsWhereToMake5Level as $index => $dirForDoku) {
//		    $dirForTeilPath = $teilDir . "/" . $dirForDoku;
//		    foreach ($level5Dirs as $level5Dir) {
//			$level5DirPath = $dirForTeilPath . "/" . $level5Dir;
//			if (!file_exists($level5DirPath)) {
//			    mkdir($level5DirPath, 0777, TRUE);
//			    echo "$level5DirPath created<br>";
//			} else {
//			    echo "$level5DirPath exists<br>";
//			}
//		    }
//		}
//	    }
//	}
//    }
//}
