<?
session_start();
require_once '../db.php';

$a = AplDB::getInstance();

if($_GET['import']=='*')
    $import = '*';
else
    $import = intval($_GET['import']);

if($_GET['plan']=='*')
    $plan = '*';
else
    $plan = intval($_GET['plan']);


$teil = strtr(trim($_GET['teil']),'*','%');

$ar = $a->getDauftrRows($import,$teil,$plan);

echo json_encode($ar);