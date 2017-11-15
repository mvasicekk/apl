#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';
require '/var/www/workspace/apl/sqldb.php';

$ucetniJednotka = "";
if ($argc > 1) {
    $ucetniJednotka = trim($argv[1]);
}

//$ucetniJednotka = 'FA5';
if ($ucetniJednotka == "") {
    echo "----- START updateSeznamSkladuFromPremier ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
    echo "neni zadana ucetni jednotka, koncim\n";
    exit();
}


$sqlDB = sqldb::getInstance($ucetniJednotka);
$a = AplDB::getInstance();

echo "----- updateSeznamSkladuFromPremier on :" . date('Y-m-d H:i:s') . " ----- \n";



$sql = " select CISLO,POPIS,POZNAMKA";
$sql.= " from SEZ_SKL";
$sql.= " order by CISLO";
	
$res = $sqlDB->getResult($sql);

$skladyIsp = array();
$skladyApl = array();

// -----------------------------------------------------------------------------
// sklady v ISP
if ($res !== NULL) {
    foreach ($res as $r) {
	$cislo = intval(trim($r['CISLO']));
	$popis = iconv('windows-1250', 'UTF-8', trim($r['POPIS']));
	$poznamka = iconv('windows-1250', 'UTF-8', trim($r['POZNAMKA']));
	$skladyIsp[$cislo] = array("popis"=>$popis,"poznamka"=>$poznamka);
	//$radek = sprintf("%16d, %s, %s",$cislo,$popis,$poznamka);
	//echo "$radek\n";
    }
}
//AplDB::varDump($skladyIsp);
    
// -----------------------------------------------------------------------------
// sklady v APL
$res = $a->getQueryRows("select * from sez_skl_isp order by cislo");
if ($res !== NULL) {
    foreach ($res as $r) {
	$cislo = intval(trim($r['cislo']));
	$popis = trim($r['popis']);
	$poznamka = trim($r['poznamka']);
	$skladyApl[$cislo] = array("popis"=>$popis,"poznamka"=>$poznamka);
    }
}
//AplDB::varDump($skladyApl);

// test zda mam neco navic v ISP
foreach ($skladyIsp as $cisloIsp=>$skladIsp){
    //mam cisloIsp i v apl
    if(array_key_exists($cisloIsp, $skladyApl)){
	//TODO
	//mam, zkontroluju zmeny
	$popisApl = $skladyApl[$cisloIsp]['popis'];
	$popisIsp = $skladIsp['popis'];
	$poznamkaApl = $skladyApl[$cisloIsp]['poznamka'];
	$poznamkaIsp = $skladIsp['poznamka'];
	if($popisApl!=$popisIsp){
	    $updateSql = "update sez_skl_isp set popis='$popisIsp' where cislo='$cisloIsp' limit 1";
	    $ar = $a->query($updateSql);
	    echo "ar = $ar, $updateSql\n";
	}
	if($poznamkaApl!=$poznamkaIsp){
	    $updateSql = "update sez_skl_isp set poznamka='$poznamkaIsp' where cislo='$cisloIsp' limit 1";
	    $ar = $a->query($updateSql);
	    echo "ar = $ar, $updateSql\n";
	}
    }
    else{
	//nemam, pridam radek do apl
	$insertSql = "insert into sez_skl_isp (cislo,popis,poznamka) values('".$cisloIsp."','".$skladIsp['popis']."','".$skladIsp['poznamka']."')";
	$a->query($insertSql);
	//echo "vkladam $insertSql\n";
    }
}