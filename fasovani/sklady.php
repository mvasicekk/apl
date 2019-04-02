<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 13.11.2017
 * Time: 7:49
 */

// Vyjedu si vsechny sklady, který mají v APLDB show_from = 1
session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();


$sql = "Select cislo, popis, poznamka, show_from from sez_skl_isp where show_from like '1' ";

$ret = $a->getQueryRows($sql);

$sql2 = "Select cislo, popis, poznamka, show_to from sez_skl_isp where show_to like '1' ";

$ret2 = $a->getQueryRows($sql2);

$retArray = array("from"=>$ret,"to"=>$ret2);

echo json_encode($retArray);