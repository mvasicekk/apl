<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

$ar = $a->getEinkaufAnforderungenArray();

echo json_encode($ar);