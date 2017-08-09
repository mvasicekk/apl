<?php

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();
$new = $o->newStat;
$pal = $o->pall;
//INSERT INTO table_name (column1, column2, column3, ...) VALUES (value1, value2, value3, ...);
if($new !== null  && $pal !== null){
$sql = "insert into dstator (id,stator,paleta,vyrazen_datum,bemerkung,warning,danger) values ('','$new','$pal','0000-00-00','','0','0')";
$a->query($sql);
}

$retArray = array(
  "a" => $newStat
);

?>
