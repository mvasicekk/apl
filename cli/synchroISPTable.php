#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';
require '/var/www/workspace/apl/sqldb.php';

$a = AplDB::getInstance();

$ucetniJednotka = "";
if ($argc > 1) {
    $ucetniJednotka = trim($argv[1]);
}

/**
 * key = isptable,value = apl table
 */
$tables = array(
    "STREDISK"=>array(
	"aplTable"=>"strediska_isp",
	"ispKey"=>"STREDISKO",
	"aplKey"=>"stredisko",
	"fieldsForSync"=>array(
	    "STREDISKO"=>"stredisko",
	    "STRE_NAZEV"=>"str_nazev",
	    "TEXT_1"=>"text1",
	    "TEXT_2"=>"text2",
	    "NAD_NOD"=>"str_parent"
	)
    ),
);


//$ucetniJednotka = 'FA5';
if ($ucetniJednotka == "") {
    echo "\n----- START synchroISPTable ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
    echo "\nneni zadana ucetni jednotka, koncim\n";
    exit();
}

echo "\n----- START synchroISPTable ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
$sqlDB = sqldb::getInstance($ucetniJednotka);

// budu prochazet definovane tabulky z Premiera
foreach ($tables as $tISP=>$tAPLArray){
  echo "\n ISP Table : $tISP";
  $tAPL = $tAPLArray['aplTable'];
  echo "\n APL Table : $tAPL";
  $resISP = $sqlDB->getResult("select * from $tISP");
  if($resISP!==NULL){
      foreach ($resISP as $rISP){
	  $allFieldsISP = array_keys($rISP);
	  $ispKey = $tAPLArray['ispKey'];
	  $ispKeyValue = trim($rISP[$ispKey]);
	  $aplKey = $tAPLArray['aplKey'];
	  //var_dump($ispKey);
	  // zkusim najit klic v tabulce z apl
	  $sql = "select `".$tAPLArray['aplKey']."` from `".$tAPLArray['aplTable']."` where `".$tAPLArray['aplKey']."`='".$ispKeyValue."'";
	  //var_dump($sql);
	  $rApl = $a->getQueryRows($sql);
	  if($rApl!==NULL){
	      //klic mam, porovnam obsahy poli
	      echo "\nKEY $ispKeyValue found in $tAPL, fields -> update";
	  }
	  else{
	      //klic nemam, vlozim novy radek
	      //echo "\nKEY $ispKeyValue NOT found in $tAPL, INSERT new ROW";
	      $sql_insert = "insert into `$tAPL`";
	      $sql_insert.=" (";
	      $counter=0;
	      foreach ($tAPLArray['fieldsForSync'] as $ispField=>$aplField){
		  if($counter>0){
		      $comma = ',';
		  }
		  else{
		      $comma = '';
		  }
		  $sql_insert.=" $comma `".$aplField."`";
		  $counter++;
	      }
	      $sql_insert.=" )";
	      $sql_insert.=" values";
	      $sql_insert.=" (";
	      $counter=0;
	      foreach ($tAPLArray['fieldsForSync'] as $ispField=>$aplField){
		  if($counter>0){
		      $comma = ',';
		  }
		  else{
		      $comma = '';
		  }
		  $value = iconv('windows-1250', 'UTF-8', trim($rISP[$ispField]));
		  $sql_insert.=" $comma '".$value."'";
		  $counter++;
	      }
	      $sql_insert.=" )";
	      $a->insert($sql_insert);
	      var_dump($sql_insert);
	  }
      }
  }
}

// a jeste obracene, projdu vsechny v apl a pokud nenajdu v isp, tak smazu
echo "\n delete from apltable";
foreach ($tables as $tISP=>$tAPLArray){
  echo "\n ISP Table : $tISP";
  $tAPL = $tAPLArray['aplTable'];
  echo "\n APL Table : $tAPL";
  $ispKey = $tAPLArray['ispKey'];
  $aplKey = $tAPLArray['aplKey'];
  $sql = "select `".$tAPLArray['aplKey']."` from `".$tAPLArray['aplTable']."`";
//  $sql = "select `".$tAPLArray['aplKey']."` from `".$tAPLArray['aplTable']."` where `".$tAPLArray['aplKey']."`='".$ispKeyValue."'";
  $resAPL = $a->getQueryRows($sql);
//  var_dump($resAPL);
  if($resAPL!==NULL){
      foreach ($resAPL as $aplRow){
	  $aplKeyValue = $aplRow[$aplKey];
	  $sql = "select ".$ispKey." from ".$tISP." where ".$ispKey."='".$aplKeyValue."'";
	  //var_dump($sql);
	  $resISP = $sqlDB->getResult($sql);
	  //var_dump($resISP);
	  if($resISP===NULL || count($resISP)==0){
	      echo "\n key $aplKeyValue not found in $tISP (ISP) -> delete from $tAPL (APL)";
	      $sql_delete = "delete from $tAPL where $aplKey='$aplKeyValue' limit 1";
	      $a->query($sql_delete);
//	      var_dump($sql_delete);
	  }
      }
  }
  
}

//$res = $sqlDB->getResult("select * from fl_PERSONAL_APL_view order by Z_CISLO,PP_CISLO");
//$persArray[$zCislo]['zPrijmeni'] = iconv('windows-1250', 'UTF-8', trim($r['Z_PRIJMENI']));

echo "\n----- KONEC synchroISPTable on :" . date('Y-m-d H:i:s') . " ----- \n";
