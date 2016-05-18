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
for($i=0;$i<10;$i++){
    $cislo = 350000+$i;
    $oscislo = 104;
    $sklad = 1;
    $pocet_vydane = 1;
    $datum = date('Y-m-d');
    $poznamka = "$datum poradi v cyklu $i";
    $datum = date('Y-m-d');
    $sql = "INSERT INTO apl_am_pohyb ( cislo,sklad,pocet_vydane,oscislo,datum,poznamka )";
    $sql.= " values ( '$cislo','$sklad','$pocet_vydane','$oscislo','$datum','$poznamka')";
    $sqlDB->exec($sql);
}

//
//$sql = "insert into eink_anforderungen";
//$sql.=" (artikel,anzahl,[user],bemerkung,abdatum,anftyp,prio,status_flag)";
//$sql.=" values('testjr',1,'jr','','2016-04-25','a','a',0)";
//$sqlDB->exec($sql);
//
//echo "lastinsertid:".$sqlDB->getLastInsertId();


//$sqlDB = sqldb::getInstance();
$res = $sqlDB->getResult("select * from SKLAD");
if($res!==NULL){
    $headers = array_keys($res[0]);
    //var_dump($headers);
}

echo "<table border='1'>";
echo "<tr>";
foreach ($headers as $h){
    	echo "<th>";
	echo $h;
	echo "</th>";
    
}
echo "</tr>";
foreach ($res as $r){
    echo "<tr>";
    foreach ($r as $value){
	echo "<td>";
	echo $value;
	echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";

//
//$res = $sqlDB->getResult("select * from test1");
//echo "<pre>";
//var_dump($res);
//echo "</pre>";

