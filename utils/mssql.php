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
//    $cislo = $i;
//    $popis = "$i eofie $i ".date('Y-m-d H:i:s');
//    $datum = date('Y-m-d');
//    $sqlDB->exec("INSERT INTO test1 ( cislo,popis,datum ) values ( $i,'$popis','$datum' )");
//}

$sql = "insert into eink_anforderungen";
$sql.=" (artikel,anzahl,[user],bemerkung,abdatum,anftyp,prio,status_flag)";
$sql.=" values('testjr',1,'jr','','2016-04-25','a','a',0)";
$sqlDB->exec($sql);

echo "lastinsertid:".$sqlDB->getLastInsertId();


//$sqlDB = sqldb::getInstance();
$res = $sqlDB->getResult("select * from eink_anforderungen order by id desc");
echo "<pre>";
var_dump($res);
echo "</pre>";


$res = $sqlDB->getResult("select * from test1");
echo "<pre>";
var_dump($res);
echo "</pre>";

