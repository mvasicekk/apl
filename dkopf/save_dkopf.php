<?
session_start();
require "../fns_dotazy.php";
require '../db.php';

$apl = AplDB::getInstance();

dbConnect();
$teil = trim($_GET['teil']);
$kunde = trim($_GET['kunde']);
$teillang = trim($_GET['teillang']);
$bezeichnung = trim($_GET['bezeichnung']);
$gew = trim($_GET['gew']);
$brgew = trim($_GET['brgew']);
$wst = trim($_GET['wst']);
$fa = trim($_GET['fa']);
$vm = trim($_GET['vm']);
$spg = trim($_GET['spg']);
$status = trim($_GET['status']);
$bemerk = trim($_GET['bemerk']);
$art_guseisen = trim($_GET['art_guseisen']);

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: nocache');
header('Content-Type: text/xml');

// kontrola hodnot parametru
/////////////////////////////////////////////////////////////////////////////////////

mysql_query('set names utf8');
// limit 1 je pro jistotu, kdyby se pokazilo kriterium ve where

$sql = "update dkopf set ";
$sql.="`Kunde`='" . $kunde . "',";
$sql.="`Teilbez`='" . $bezeichnung . "',";
$sql.="`Gew`='" . $gew . "',";
$sql.="`BrGew`='" . $brgew . "',";
$sql.="`FA`='" . $fa . "',";
$sql.="`verpackungmenge`='" . $vm . "',";
$sql.="`stk_pro_gehaenge`='" . $spg . "',";
$sql.="`status`='" . $status . "',";
$sql.="`bemerk`='" . $bemerk . "',";
$sql.="`teillang`='" . $teillang . "',";
$sql.="`Art Guseisen`='" . $art_guseisen . "'";
$sql.=" where (Teil='" . $teil . "') limit 1";

$result = mysql_query($sql);
$affected_rows = mysql_affected_rows();
$mysqlError = mysql_error();

// vytvoreni slozek na Gdatu
$kundeGdatPath = $apl->getKundeGdatPath($kunde);
$gdatPath = "/mnt/gdat/Dat/";
if ($kundeGdatPath !== NULL) {
    $teilDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $teil;
    // test zda uz takova slozka existuje
    $dirExists = file_exists($teilDir) ? "exists" : "not exists";
    if ($dirExists == "not exists") {
	// vytvorit slozku pro dil
	mkdir($teilDir, 0777, TRUE);
    }
    if (file_exists($teilDir)) {
	foreach (AplDB::$DIRS_FOR_TEIL_FINAL as $dirForTeil) {
	    $dirForTeilPath = $teilDir . "/" . $dirForTeil;
	    if (!file_exists($dirForTeilPath)) {
		mkdir($dirForTeilPath);
	    }
	}
    }
}
    // 5. uroven Archived, in Arbeit
    $dirsWhereToMake5Level = array(
	"020" => "020 EMPB",
	"030" => "030 PPA",
	"040" => "040 GPA",
	"050" => "050 VPA",
    );
    $level5Dirs = array("010 Ausgearbeitet", "020 Archiv");

    if ($kundeGdatPath !== NULL) {
	array_push($teileArray, array("teil"=>$teil));
	if ($teileArray !== NULL) {
	    foreach ($teileArray as $row) {
		$teilDir = $gdatPath . $kundeGdatPath . "/200 Teile/" . $row["teil"];
		if (file_exists($teilDir)) {
		    foreach ($dirsWhereToMake5Level as $index => $dirForDoku) {
			$dirForTeilPath = $teilDir . "/" . $dirForDoku;
			foreach ($level5Dirs as $level5Dir) {
			    $level5DirPath = $dirForTeilPath . "/" . $level5Dir;
			    if (!file_exists($level5DirPath)) {
				mkdir($level5DirPath, 0777, TRUE);
			    }
			}
		    }
		}
	    }
	}
    }

    $output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
    $output .= '<response>';
    $output .= '<sql>';
    $output .= $sql;
    $output .= '</sql>';
    $output .= '<affectedrows>';
    $output .= $affected_rows;
    $output .= '</affectedrows>';
    $output .= '<mysqlerror>';
    $output .= $mysqlError;
    $output .= '</mysqlerror>';
    $output .= '</response>';
    echo $output;
?>

