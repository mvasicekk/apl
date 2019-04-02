<?php
/**
 * Created by PhpStorm.
 * User: mva
 * Date: 13.11.2017
 * Time: 15:36
 */


session_start();
require_once '../db.php';
require "../fns_dotazy.php";
dbConnect();
$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$ident = get_user_pc();
$user = substr($ident, strpos($ident, "/") + 1);

$sql = "select * from  dbenutzerroles where benutzername = '$user' and role_id Like '15' and role_id LIKE '1' ";
$ret = $a->getQueryRows($sql);

if($ret !== null){
    $rett = true;
}else{
    $rett = false;
}

echo json_encode($ret);