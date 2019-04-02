<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 13.11.2017
 * Time: 8:45
 */

// Ziskam si data po vyberu skladu
// vytahnu si data z Premiera

/* RHB sklad */
/*SELECT * from SKLAD where SKLAD LIKE '1' and CISLO NOT LIKE '%8%' and CISLO not LIKE '%9%' */
/*Sklad Barev*/
/* SELECT * from SKLAD where SKLAD LIKE '2' */


session_start();
require_once '../db.php';
// premier
require_once '../sqldb.php';
$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();
$p = sqldb::getInstance();
mysql_query("set names utf8");
// nejprve potrebuji cislo skladu
$sklad = $o->sklad;
// test skladu
//$sklad = '1';

if($sklad === '1'){

    $sql = "SELECT  CISLO,TEXT  FROM SKLAD WHERE SKLAD LIKE '$sklad' and K_PLATNOST like '0' ";
    $res = $p->getResult($sql);

    if($res!==NULL){

        foreach ($res as $r){
            $value = trim($r['TEXT']);
            $txt = iconv('windows-1250', 'UTF-8', $value);
            $string = preg_replace('/\s+/', '', $r['CISLO']);
            $retArray[] = array("cislo"=>$string." ".$txt);
        }
    }

}else if ($sklad === '2'){

    $sql = "SELECT * from SKLAD where SKLAD LIKE $sklad ";
    $res = $p->getResult($sql);
    if($res!==NULL){

        foreach ($res as $r){
            $value = trim($r['TEXT']);
            $txt = iconv('windows-1250', 'UTF-8', $value);
            $string = preg_replace('/\s+/', '', $r['CISLO']);
            $retArray[] = array("cislo"=>$string." ".$txt);
        }
    }
}

echo json_encode($retArray);