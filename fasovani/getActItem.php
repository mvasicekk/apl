<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 13.11.2017
 * Time: 14:09
 */

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();


$date = date('Y-m-d', strtotime($o->date));

mysql_query('set names utf8');

//	id 	inv pers_komu	pers_kdo oprava <1 = oprava	reklamace <1 = rekl	poznamka 	stamp
$sql = "select id,inv,popis,pers_kdo,poznamka,vracena,pocet_ks,show_from,show_to,ks_vydano,reklamace from vydej where stamp like '%$date%' and Typ like '20'  order by stamp desc ";
$result = $a->getQueryRows($sql);
$retArray = array();
if($result !== null){
    foreach ($result as $v){
        $prs = $v['pers_kdo'];
        $retArray[] = array(
            "id"=> $v['id'],
            "pers" => $prs,
            "inv" => $v['inv'],
            "popis" => $v['popis'],
            "ks" => $v['pocet_ks'],
            "ks_vydano"=> $v['ks_vydano'],
            "sch" => $v['vracena'],
            "rekl" => $v['reklamace'],
            "poznamka"=> $v['poznamka'],
            "show_from" => $v['show_from'],
            "show_to" => $v['show_to']

        );

        //   echo json_encode($prs);
        // echo json_encode($holds);

    }

    echo json_encode($retArray);
}
//echo $date;

/*
$retArray = array("res" => $arr, "datum" => $date);
echo json_encode($retArray);
*/