<?php

require '../sqldb.php';

//foreach(PDO::getAvailableDrivers() as $driver)
//    {
//    echo $driver.'<br />';
//    }
    
$sqlDB = sqldb::getInstance();
//$res = $sqlDB->getResult("select * from eink_anforderungen");
//echo "<pre>";
//var_dump($res);
//echo "</pre>";
//
//for($i=0;$i<10;$i++){
//    $cislo = 350000+$i;
//    $oscislo = 104;
//    $sklad = 1;
//    $pocet_vydane = 1;
//    $datum = date('Y-m-d');
//    $poznamka = "$datum poradi v cyklu $i";
//    $datum = date('Y-m-d');
//    $sql = "INSERT INTO apl_am_pohyb ( cislo,sklad,pocet_vydane,oscislo,datum,poznamka )";
//    $sql.= " values ( '$cislo','$sklad','$pocet_vydane','$oscislo','$datum','$poznamka')";
//    $sqlDB->exec($sql);
//}

//
//$sql = "insert into eink_anforderungen";
//$sql.=" (artikel,anzahl,[user],bemerkung,abdatum,anftyp,prio,status_flag)";
//$sql.=" values('testjr',1,'jr','','2016-04-25','a','a',0)";
//$sqlDB->exec($sql);
//
//echo "lastinsertid:".$sqlDB->getLastInsertId();

$sql=" select SEZ_SKL.CISLO as cislo,SEZ_SKL.POPIS as popis from SEZ_SKL order by CISLO";
$res = $sqlDB->getResult($sql);

//var_dump($res);
if($res!==NULL){
    $skladyArray = array();
    foreach ($res as $r){
	$value = trim($r['popis']);
	echo iconv('windows-1250', 'UTF-8', $value);
	array_push($skladyArray, array('cislo'=>$r['cislo'],'popis'=>trim($r['popis'])));
    }
}

//var_dump($skladyArray);


//$sqlDB = sqldb::getInstance();
//$res = $sqlDB->getResult("select * from fl_SKLAD_APL_view");
//if($res!==NULL){
//    $headers = array_keys($res[0]);
//    //var_dump($headers);
//}
//
//echo "<table border='1'>";
//echo "<tr>";
//foreach ($headers as $h){
//    	echo "<th>";
//	echo $h;
//	echo "</th>";
//    
//}
//echo "</tr>";
//foreach ($res as $r){
//    echo "<tr>";
//    foreach ($r as $value){
//	echo "<td>";
//	echo $value;
//	echo "</td>";
//    }
//    echo "</tr>";
//}
//echo "</table>";

//
//$res = $sqlDB->getResult("select * from test1");
//echo "<pre>";
//var_dump($res);
//echo "</pre>";

