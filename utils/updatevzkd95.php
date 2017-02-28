<?php
require_once '../db.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$apl = AplDB::getInstance();

$sql.= " select";
$sql.= "     dkopf.Teil,";
$sql.= "     dkopf.preis_stk_gut,";
$sql.= "     sum(dpos.`VZ-min-kunde`*0.45) as sum_vzkd";
$sql.= " from dkopf";
$sql.= " join dpos on dpos.Teil=dkopf.Teil";
$sql.= " where";
$sql.= "     kunde=195";
$sql.= "     and";
$sql.= "     dpos.`kz-druck`<>0";
$sql.= "     and";
$sql.= "     dpos.`TaetNr-Aby`<>1701";
$sql.= "     and";
$sql.= "     dpos.`TaetNr-Aby`<>95";
$sql.= " group by";
$sql.= "     dkopf.Teil";

$rs = $apl->getQueryRows($sql);
foreach ($rs as $r){
    $vzkd95 = $r['preis_stk_gut']/0.45 - $r['sum_vzkd'];
    $vzkd95Rnd = round($vzkd95, 4);
    $sumMit95 = $vzkd95Rnd*0.45 + $r['sum_vzkd']*0.45;
    echo "<h3>".$r['Teil']."</h3><br>";
    echo "preis_stk_gut = ".$r['preis_stk_gut']."<br>";
    echo "vzkd95 = ".$vzkd95."<br>";
    echo "vzkd95Rnd = ".$vzkd95Rnd."<br>";
    echo "sumMit95 = ".$sumMit95."<br>";
    $sql = "update dpos set `VZ-min-kunde`='$vzkd95Rnd' where Teil='".$r['Teil']."' and `TaetNr-Aby`=95";
    echo "sql = ".$sql."<hr>";
    //$apl->query($sql);
}
