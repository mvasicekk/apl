<?php
session_start();
require("../libs/Smarty.class.php");
$smarty = new Smarty;

require_once '../db.php';

$a = AplDB::getInstance();

$u = $a->get_user_pc();
//$routeParams = $o->routeParams;
$datumDB = $_GET["datum"];
$oe = $_GET["oe"];
$view = "";




if(strtotime($datumDB)){
    $datumDB = date('Y-m-d',strtotime($datumDB));
    $datum = date('d.m.Y',strtotime($datumDB));
}
else{
    $datumDB = date('Y-m-d');
    $datum = date('d.m.Y');
}

$dny = array(
    "Neděle","Pondělí","Úterý","Středa","Čtvrtek","Pátek","Sobota","Neděle"
);

$jmenoDneE = date('l', strtotime($datumDB));
$weekDayIndex = date('w', strtotime($datumDB));
$jmenoDneCZ = $dny[$weekDayIndex];

if(strlen($oe)>0){
    $oeOriginal = $oe;
    $oe = strtr($oe, '*', '%');
    //$oe = $routeParams->oe;
}
else{
    $oe = "GF";
    $oeOriginal = $oe;
}

$oe= strtoupper($oe);

$sql = " select dzeitsoll.id,dzeitsoll.persnr,dzeitsoll.oe,sum(dzeitsoll.stunden) as stunden,dpers.`name`,dpers.vorname,dpers.dpersstatus,dpers.eintritt";
$sql.= " ,min(edata_access_events.dt) as edata_min";
$sql.= " ,max(edata_access_events.dt) as edata_max";
$sql.= " from dzeitsoll";
$sql.= " left join edata_access_events on edata_access_events.persnr=dzeitsoll.persnr and DATE_FORMAT(edata_access_events.dt,'%Y-%m-%d')=dzeitsoll.datum";
$sql.= " join dpers on dpers.persnr=dzeitsoll.persnr";
$sql.= " where";
$sql.= "     dzeitsoll.oe like '$oe'";
$sql.= "     and";
$sql.= "     dzeitsoll.datum='$datumDB'";
$sql.= "     and";
$sql.= "     dpers.dpersstatus='MA'";

$sql.= " group by dzeitsoll.persnr,dzeitsoll.oe";

$osoby = $a->getQueryRows($sql);
$i = 0;
foreach ($osoby as $o){
    if($o["edata_min"]===NULL){
	$osoby[$i]["panelclass"]="panel-warning";
    }
    else{
	$osoby[$i]["panelclass"]="panel-success";
	$osoby[$i]["edata_min"]= substr($o["edata_min"], 11,5);
	$osoby[$i]["edata_max"]= substr($o["edata_max"], 11,5);
    }
    
    $i++;
}
$osobypodleoe = array();


$returnArray = array(
    'u' => $u,
    'view'=>$view,
    'routeParams' => $routeParams,
    'datum'=>$datum,
    'oe'=>$oe,
    'oeOriginal'=>$oeOriginal,
    'sql'=>$sql,
    'osoby'=>$osoby,
    'osobypodleoe'=>$osobypodleoe,
    'jmenoDneE'=>$jmenoDneE,
    'jmenoDneCZ'=>$jmenoDneCZ,
);


//$osoby = array(array("persnr"=>1651),array("persnr"=>1651));
$smarty->assign("datum",$datum);
$smarty->assign('oe',$oe);
$smarty->assign('oeOriginal',$oeOriginal);
$smarty->assign('sql',$sql);
$smarty->assign('osoby',$osoby);
$smarty->assign('jmenoDneE',$jmenoDneE);
$smarty->assign('jmenoDneCZ',$jmenoDneCZ);
	
// varianta bez autorizace, pro screenly
$smarty->display("showplan.tpl");
