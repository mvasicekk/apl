#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';
require '/var/www/workspace/apl/sqldb.php';


echo "----- updateEinkArtikelFromPremier on :" . date('Y-m-d H:i:s') . " ----- \n";
$sqlDB = sqldb::getInstance();


// umisteni polozek ve skladech ------------------------------------------------
// 
//$sqlDB = sqldb::getInstance();
$cislaSklady = array();
$cislaCeny = array();

$res = $sqlDB->getResult("select cislo,sklad,cena_mj from fl_SKLAD_APL_view order by cislo,sklad");

if ($res !== NULL) {
    foreach ($res as $r) {
	$cislo = (trim($r['cislo']));
	$sklad = intval(trim($r['sklad']));
	$cena = floatval(trim($r['cena_mj']));
	if (!array_key_exists($cislo, $cislaSklady)) {
	    $cislaSklady[$cislo] = array();
	}
	array_push($cislaSklady[$cislo], $sklad);
	if ($sklad != 999) {
	    // v matricnim skladu nemam cenu
	    $cislaCeny[$cislo] = $cena;
	}
    }
}

//var_dump($cislaSklady);

$a = AplDB::getInstance();

//$a->query('truncate table `eink-artikel_sklad`');
// 1. projedu podle premiera
foreach ($cislaSklady as $cislo => $sklady) {
    $amnr = trim($cislo);
    $amnrInt = intval($amnr);
    $imported = "$amnr" == "$amnrInt" ? 'A' : 'N';
    if ($imported == 'A') {
	foreach ($sklady as $sklad) {
	    $sklad = intval($sklad);
	    // zkusim najit radek v apl
	    $rs1 = $a->getQueryRows("select amnr from `eink-artikel_sklad` where amnr='$amnrInt' and sklad='$sklad'");
	    if ($rs1 === NULL) {
		// tuto kombinaci nemam -> vlozim do db
		$sqlInsert = "insert into `eink-artikel_sklad` (amnr,sklad) values('$amnrInt','$sklad')";
		$insertId = $a->insert($sqlInsert);
		echo "vkladam novy: $sqlInsert ($insertId)\n";
	    }
	}
    } else {
	//echo "$amnr - vynechavam z importu\n";
    }
}


// 2. projdu apl tabulku smazu ty, ktere neexistuji v premieru
$rs2 = $a->getQueryRows("select amnr,sklad from `eink-artikel_sklad`");
if ($rs2 !== NULL) {
    foreach ($rs2 as $r) {
	$amnr = $r['amnr'];
	$sklad = $r['sklad'];
	if (array_key_exists($amnr, $cislaSklady)) {
	    $skladyArray = $cislaSklady[$amnr];
	    if (array_search($sklad, $skladyArray) === FALSE) {
		// nenasel jsem cislo skladu, mazu
		$sqlDelete = "delete from `eink-artikel_sklad` where amnr='$amnr' and sklad='$sklad'";
		$ar = $a->query($sqlDelete);
		echo "delete: $sqlDelete ($ar)\n";
	    }
	} else {
	    $sqlDelete = "delete from `eink-artikel_sklad` where amnr='$amnr' and sklad='$sklad'";
	    $ar = $a->query($sqlDelete);
	    echo "delete: $sqlDelete ($ar)\n";
	}
    }
}

$einkArtikel = "eink-artikel_test";
// seznam karet z matricniho skladu, sklad = 999
// 1 update starych + insert novych
$res = $sqlDB->getResult("select cislo,sklad,text,text_2,mj,sortiment,iden5,cena_mj from fl_SKLAD_APL_view where sklad=999 order by cislo");
foreach ($res as $r) {
    $amnr = $r['cislo'];
    $amnr = trim($amnr);
    $amnrInt = intval($amnr);

    $artName1 = iconv('windows-1250', 'UTF-8', trim($r['text']));
    $artName2 = iconv('windows-1250', 'UTF-8', trim($r['text_2']));
    $mj = iconv('windows-1250', 'UTF-8', trim($r['mj']));
    $sortiment = trim($r['sortiment']);
    $am_ausgabe = strlen(trim($r['iden5']) > 0) ? 1 : 0;
//    $cenaMj = floatval(trim($r['cena_mj']));
    if(array_key_exists($amnr, $cislaCeny)){
	$cenaMj = $cislaCeny[$amnr];
    }
    else{
	$cenaMj = 0;
    }
    

    // budu provadet jen pro cisla, ktera jsou "cisla", napr. nebudu updatovat polozky 350665-1 ...
    if ("$amnr" == "$amnrInt") {
	// mam ho v apl ?
	$rows = $a->getQueryRows("select `art-nr` as amnr from `$einkArtikel` where convert(`art-nr`,CHAR)='$amnr'");
	$onlyInsert = TRUE;
	if ($rows !== NULL) {
	    // smazu stary
	    $sqlDelete = "delete from `$einkArtikel` where convert(`art-nr`,CHAR)='$amnr'";
	    $ar = $a->query($sqlDelete);
	    //echo "delete: $sqlDelete ($ar)\n";
	    $onlyInsert = FALSE;
	}
	//vlozit do apl
	$sqlInsert = "insert into `$einkArtikel`";
	$sqlInsert.=" (`art-nr`,`art-name1`,`art-name2`,`mj`,`art-grp-nr`,`AM_Ausgabe`,`art-vr-preis`)";
	$sqlInsert.=" values('$amnrInt','$artName1','$artName2','$mj','$sortiment','$am_ausgabe','$cenaMj')";
	$insertId = $a->insert($sqlInsert);
	if ($onlyInsert === TRUE) {
	    echo "vkladam novy: $sqlInsert\n";
	}

	
	// mam ho v apl v zive eink-artikel ?
	$rows = $a->getQueryRows("select `art-nr` as amnr from `eink-artikel` where convert(`art-nr`,CHAR)='$amnr'");
	$onlyInsert = TRUE;
	if ($rows !== NULL) {
	    // smazu stary, ale ne v zive db
	    $sqlDelete = "delete from `$einkArtikel` where convert(`art-nr`,CHAR)='$amnr'";
	    //$ar = $a->query($sqlDelete);
	    //echo "delete: $sqlDelete ($ar)\n";
	    $onlyInsert = FALSE;
	} else {
	    //vlozit do apl
	    $sqlInsert = "insert into `eink-artikel`";
	    $sqlInsert.=" (`art-nr`,`art-name1`,`art-name2`,`mj`,`art-grp-nr`,`AM_Ausgabe`,`art-vr-preis`)";
	    $sqlInsert.=" values('$amnrInt','$artName1','$artName2','$mj','$sortiment','$am_ausgabe','$cenaMj')";
	    $insertId = $a->insert($sqlInsert);
	    if ($onlyInsert === TRUE) {
		echo "vkladam novy: $sqlInsert do eink-artikel \n";
	    }
	}
    }
}
// 2. smazani tech, ktere neexistuji v matricnim skladu, zatim ne

//2017-09-11, aktualizace ceny v zive db, protoze tam zatim nemnazu polozky, tak
// - pokud uz byla polozka zalozena v minulosti ale napr. s nulovou cenou, nebude se mi cena v zive db aktualizovat, proto takto
// - aktualizuju cenu, popisky a am_ausgabe priznak


// 2017-09-22 zakazan update popisku a cen protoze napr. problem s behaeltrama, nejsou nemecke popisy ...
$testArtikelArray = $a->getQueryRows("select `art-nr` as amnr,`art-name1` as name1,`art-name2` as name2,`mj`,`art-grp-nr`,`AM_Ausgabe`,`art-vr-preis` as preis from `eink-artikel_test`");
foreach ($testArtikelArray as $ar){
    //zkusim najit v zive db
    $amnr = $ar['amnr'];
    $preis = floatval($ar['preis']);
    $rows = $a->getQueryRows("select `art-nr` as amnr from `eink-artikel` where convert(`art-nr`,CHAR)='$amnr'");
    if($rows!=NULL){
	//nasel jsem
	// updatnu popisky a am_ausgabe
	//$a->query("update `eink-artikel` set `art-name1`='".$ar['name1']."',`art-name2`='".$ar['name2']."',`AM_Ausgabe`=".$ar['AM_Ausgabe']." where convert(`art-nr`,CHAR)='$amnr' limit 1");
	// pokus cena v premieru neni 0, updatnu i cenu v zive
	if($preis<>0){
	  //  $a->query("update `eink-artikel` set `art-vr-preis`='".$preis."' where convert(`art-nr`,CHAR)='$amnr' limit 1");
	}
    }
}