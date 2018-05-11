#!/usr/bin/php
<?php
require_once '/var/www/workspace/apl/db.php';

$a = AplDB::getInstance();

$sql = "select 
    datum,
    sum(if(kunden_stat_nr=1,if(auss_typ=4,(`stück`+`auss-stück`)*`vz-soll`,`stück`*`vz-soll`),0)) as pg1_vzkd
from drueck 
join dkopf using (teil) 
join dksd using (kunde) 
group by drueck.datum 
order by pg1_vzkd desc
limit 10
";

$rs = $a->getQueryRows($sql);
if($rs!==NULL){
    $a->query("delete from saved_daily_record");
    foreach ($rs as $r){
	//vlozit do tabulky s rekordama
	$sql_insert = "insert into saved_daily_record (datum,pg1_vzkd) values('".$r['datum']."','".$r['pg1_vzkd']."')";
	$a->insert($sql_insert);
    }
}
?>