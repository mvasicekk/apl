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
 * key = isptable (jmeno tabulky case-sensitive ,value = apl table name + params
 */
$tables = array(
    "STREDISK"=>array(				    //jmeno tabulky v Premieru
	"aplTable"=>"strediska_isp",		    // jmeno tabulky v apl databazi
	"ispSelect"=>"*,'$ucetniJednotka' as UJ",   //ktera pole vybiram z tabulky v Premieru
	"ispKey"=>"STREDISKO",			    //sloupec v Premieru podle ktereho porovnavam, zda mam radek v apl
	"bFilterUj"=>TRUE,			    // do vyberu z apl pridat i ucetni jednotku, napr. cisla zamestnancu jsou pres vice uc. jednotek duplicitni
	"bIspKeyGUID"=>FALSE,			    // pro prevod guid binary na string, guid z mssql musim prevest na string
	"aplKey"=>"stredisko",			    //sloupec v apl podle ktereho porovnavam, zda mam radek v premieru
	"fieldsForSync"=>array(			    //seznam poli, ktera synchronizuji "sloupec v Premier"=>"sloupec v apl"
	    "STREDISKO"=>"stredisko",		    //synchronizuju samozrejme i klic
	    "STRE_NAZEV"=>"str_nazev",		    //ostatni
	    "TEXT_1"=>"text1",			    //ostatni	
	    "TEXT_2"=>"text2",			    //ostatni
	    "UJ"=>"uj",				    //ucetni jednotka je dulezita kvuli duplicitam
	    "NAD_NOD"=>"str_parent"
	)
    ),
    "MZDY_POL"=>array(
	"aplTable"=>"mzdpol_isp",
	"ispSelect"=>"*,'$ucetniJednotka' as UJ",   //ktera pole vybiram z tabulky
	"ispKey"=>"KOD",
	"bIspKeyGUID"=>FALSE,
	"aplKey"=>"kod",
	"bFilterUj"=>TRUE,  // omezit vyber z apl podle ucetni jednotky
	"fieldsForSync"=>array(
	    "KOD"=>"kod",
	    "POPIS"=>"popis",
	    "KOD_NAD"=>"kod_nad",
	    "SAZBA"=>"sazba",
	    "UJ"=>"uj",
	    "KOD_BAZE"=>"kod_baze"
	)
    ),
    "PERS_HYS"=>array(
	"aplTable" => "pers_uvazky_isp",
	"ispSelect"=>"*,IIF(ISNULL(PLATNY_OD,'19000101')>'19000101',CONVERT(DATE,PLATNY_OD,120),NULL) as PLAT_OD,'$ucetniJednotka' as UJ",   //ktera pole vybiram z tabulky
	"ispKey" => "ID",
	"bIspKeyGUID"=>TRUE,
	"aplKey" => "id_isp",
	"bFilterUj"=>TRUE,  // omezit vyber z apl podle ucetni jednotky
	"fieldsForSync" => array(
	    "ROK" => "rok",
	    "MESIC" => "mesic",
	    "INTER" => "inter",
	    "FUNKCE" => "funkce",
	    "UJ"=>"uj",
	    //"PLATNY_OD" => "platny_od",
	    "PLAT_OD" => "platny_od",
	    "UVA_DOBA" => "uva_doba",
	    "UVA_DNY" => "uva_dny",
	    "UVA_HOD" => "uva_hod",
	    "TYP_UVA" => "typ_uva",
	    "TYP_MZDY" => "typ_mzdy",
	    "DOV_RNAR" => "dov_rnar",
	    "DOV_PNAR" => "dov_pnar",
	    "DOVD_RNA" => "dovd_rna",
	    "ID" => "id_isp",
	)
    ),
);


/**
 * 
 * @global type $a
 * @param type $keyName
 * @param type $keyValue
 * @param type $field
 * @param type $value
 * @param type $table
 */
function updateAplTableField($keyName,$keyValue, $field, $value, $table = '') {
    global $a;
    $sql = "update `$table` set `$field`='$value' where `$keyName`='$keyValue' limit 1";
    //echo "\n $sql";
    $ar = $a->query($sql);
    if ($ar > 0) {
	echo "\nUPDATEFIELD $field = $value for $keyName=$keyValue (ar=$ar),table = $table";
    }
}

//$ucetniJednotka = 'FA5';
if ($ucetniJednotka == "") {
    echo "\n----- START synchroISPTable ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
    echo "\nneni zadana ucetni jednotka, koncim\n";
    exit();
}

echo "\n----- START synchroISPTable ($ucetniJednotka) on :" . date('Y-m-d H:i:s') . " ----- \n";
$sqlDB = sqldb::getInstance($ucetniJednotka);

// budu prochazet definovane tabulky z Premiera, cela konfigurace pro synchro je ulozena v poli $tables
foreach ($tables as $tISP=>$tAPLArray){
  echo "\n ISP Table : $tISP";
  $tAPL = $tAPLArray['aplTable'];
  echo "\n APL Table : $tAPL";
  $ispSelect =$tAPLArray['ispSelect'];
  //echo "\nselect $ispSelect from $tISP";
  $ispSql = "select $ispSelect from $tISP";
  echo "\n$ispSql";
  //$resISP = $sqlDB->getResult("select * from $tISP");
  $resISP = $sqlDB->getResult($ispSql);
  if($resISP!==NULL){
      foreach ($resISP as $rISP){
	  $allFieldsISP = array_keys($rISP);
	  $ispKey = $tAPLArray['ispKey'];
	  $bIspKeyGUID = $tAPLArray['bIspKeyGUID'];
	  if($bIspKeyGUID===TRUE){
	      $ispKeyValue = mssql_guid_string($rISP[$ispKey]);
	  }
	  else{
	      $ispKeyValue = trim($rISP[$ispKey]);
	  }
	  
	  //echo "\nispKeyValue = $ispKeyValue";
	  
	  $aplKey = $tAPLArray['aplKey'];
	  //var_dump($ispKey);
	  // zkusim najit klic v tabulce z apl - mam radek v APL ?
	  $sql = "select * from `".$tAPLArray['aplTable']."` where `".$tAPLArray['aplKey']."`='".$ispKeyValue."'";
	  if(array_key_exists('bFilterUj', $tAPLArray)){
	      if($tAPLArray['bFilterUj']===TRUE){
		  // ----------------------------------------------------------------------
		  //filtrovat i podle ucetni jednotky
		  // v pripade, ze by samotny klic podle Premiera mohl byt v apl duplicitni
		  // ----------------------------------------------------------------------
		  $sql.=" and uj='$ucetniJednotka'";
	      }
	  }
	  //var_dump($sql);
	  $rApl = $a->getQueryRows($sql);
	  if($rApl!==NULL){
	      //klic mam, porovnam obsahy poli
	      //echo "\nKEY $ispKeyValue found in $tAPL, fields -> update";
	      //projdu jednotliva pole
	      foreach ($tAPLArray['fieldsForSync'] as $ispField=>$aplField){
		  if($ispField!=$ispKey){
		      $ispValue = iconv('windows-1250', 'UTF-8', trim($rISP[$ispField]));
		  }
		  else{
		      $ispValue = $ispKeyValue;
		  }
		  $aplValue = $rApl[0][$aplField];
		  if ($ispValue != $aplValue) {
		      updateAplTableField($aplKey, $ispKeyValue, $aplField, $ispValue, $tAPL);
		  }
		  //echo "\n ($ispField/$aplField) ispValue = $ispValue, aplValue = $aplValue";
	      }
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
		  if($ispField!=$ispKey){
		      $value = iconv('windows-1250', 'UTF-8', trim($rISP[$ispField]));
		      //echo "\n neklicklic $ispField";
		  }
		  else{
		      // klic nebudu prekodovavat
		      //$value = trim($rISP[$ispField]);
		      $value = $ispKeyValue;
		      //echo "\n klic $ispField";
		  }
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
  if(array_key_exists('bFilterUj', $tAPLArray)){
	      if($tAPLArray['bFilterUj']===TRUE){
		  //filtrovat i podle ucetni jednotky
		  $sql.=" and uj='$ucetniJednotka'";
	      }
    }
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
