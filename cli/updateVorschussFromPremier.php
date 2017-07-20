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
    echo "----- START updateVorschussFromPremier ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
    echo "neni zadana ucetni jednotka, koncim\n";
    exit();
}


$sqlDB = sqldb::getInstance($ucetniJednotka);
echo "----- updateVorschussFromPremier on :" . date('Y-m-d H:i:s') . " ----- \n";




$res = $sqlDB->getResult("select convert(varchar, DATUM, 120) as datum1,* from dbo.PUB_UCTO where (DOKLAD='PK' or DOKLAD='PK2') and ZKKOD between 1 and 99999 and MD=33101 and DATUM>'2017-06-30' order by ZKKOD,DATUM");
//$res = $sqlDB->getResult("select convert(varchar, DATUM, 120) as datum1,* from dbo.PUB_UCTO where (DOKLAD='PK' or DOKLAD='PK2') and ZKKOD between 1 and 99999 and MD=33101 order by ZKKOD,DATUM");

$suma = array();
$sumA = array();

if ($res !== NULL) {
    foreach ($res as $r) {
	$persnr = intval(trim($r['ZKKOD']));
	$datum = trim($r['DATUM']);
	$datum1 = substr(trim($r['datum1']),0,10);
	$mesic = intval(date('m',strtotime($datum1)));
	$popis = iconv('windows-1250', 'UTF-8', trim($r['POPIS']));
	$vystavil = iconv('windows-1250', 'UTF-8', trim($r['VYSTAVIL']));
	$castka = intval(trim($r['CASTKA']));
	$radek = sprintf("%6d dne: %s (%s / %s) castka: %8d",$persnr,$datum1,$popis,$vystavil,$castka);
	
	if(array_key_exists($persnr, $sumA)){
	    if(array_key_exists($mesic, $sumA[$persnr])){
		$sumA[$persnr][$mesic] += $castka;
	    }
	    else{
		$sumA[$persnr][$mesic] = $castka;
	    }
	}
	else{
	    $sumA[$persnr][$mesic] = $castka;
	}
	
	
	// bez nasledujiciho testu dostavam PHP notice, index not defined
	if(array_key_exists($mesic, $suma)){
	    $suma[$mesic] += $castka;
	}
	else{
	    $suma[$mesic] = $castka;
	}
	
	echo "$radek\n";
    }
    
    echo "======================================================================\n";
    echo "mesic           castka\n";
    echo "----------------------\n";
    foreach($suma as $mesic=>$castka){
	$radek = sprintf("%5d%17d\n",$mesic,$castka);
	echo $radek;
    }
    echo "======================================================================\n";
    echo "persnr          castka\n";
    echo "----------------------\n";
    foreach ($sumA as $persnr=>$s){
	$sumPers = 0;
	//echo "------------------------------------------------------------------\n";
	//echo "persnr: $persnr\n";
	foreach($s as $mesic=>$castka){
	    $radek = sprintf("%5d%16d\n",$mesic,$castka);
	    $sumPers += $castka;
	    //echo $radek;
	}
	$radek = sprintf("%6d%16d\n",$persnr,$sumPers);
	echo $radek;
	//echo "------------------------------------------------------------------\n";
    }
}

//var_dump($cislaSklady);

$a = AplDB::getInstance();


