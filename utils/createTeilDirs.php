<?php
session_start();
//require './fns_dotazy.php';
require '../db.php';

$apl = AplDB::getInstance();


//var_dump(AplDB::$DIRS_FOR_TEIL);

$kundeGdatPath = $apl->getKundeGdatPath(111);
$gdatPath = "/mnt/gdat/Dat/";
// seznam dilu
if ($kundeGdatPath !== NULL) {
    $teileArray = $apl->getTeileNrArrayForKunde(111);
    if ($teileArray !== NULL) {
	foreach ($teileArray as $row) {
	    $teilDir = $gdatPath . $kundeGdatPath . "/" . $row["teil"];
	    // test zda uz takova slozka existuje
	    $dirExists = file_exists($teilDir)?"exists":"not exists";
	    if($dirExists=="not exists"){
		// vytvorit slozku pro dil
		if(mkdir($teilDir)){
		    echo $teilDir." created<br>";
		}
		else{
		    echo $teilDir." could not be created<br>";
		}
	    }
	    if(file_exists($teilDir)){
		foreach (AplDB::$DIRS_FOR_TEIL as $dirForTeil){
		    $dirForTeilPath = $teilDir."/".$dirForTeil;
		    if(!file_exists($dirForTeilPath)){
			mkdir($dirForTeilPath);
			echo "$dirForTeilPath created<br>";
		    }
		    else{
			echo "$dirForTeilPath exists<br>";
		    }
		}
	    }
	}
    }
}


//echo $kundeGdatPath."<br>";
//if ($kundeGdatPath !== NULL) {
//    foreach (new DirectoryIterator('/mnt/gdat/Dat/' . $kundeGdatPath) as $file) {
//	// if the file is not this file, and does not start with a '.' or '..',
//	// then store it for later display
//	if ((!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF']))) {
//	    // if the element is a directory add to the file name "(Dir)"
//	    //echo ($file->isDir()) ? "(Dir) ".$file->getFilename() : $file->getFilename()."<br>";
//	    if (!$file->isDir()) {
//		echo "<a href='/gdat" . substr($file->getPath(), 13) . "/" . $file->getFilename() . "'>" . $file->getFilename() . "</a><br>";
//	    }
//	}
//    }
//}