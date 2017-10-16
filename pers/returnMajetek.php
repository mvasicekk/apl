<?
session_start();
require_once '../db.php';
require_once '../sqldb.php'; '';

$data = file_get_contents("php://input");
$o = json_decode($data);
$pa = $o->pa;

$sqlDB = sqldb::getInstance();
$a = AplDB::getInstance();

$persnr = $pa->PersNr;
$nowDate = date('Y-m-d');
$amnr = $pa->AMNr;
$invnr = $pa->invnr;
$oe = $pa->oe;


$ident = $a->get_user_pc();

if(($persnr>0) && ($invnr>0)){
    $sql = "insert into dambew (PersNr,Datum,oe,AMNr,RueckgabeStk,invnr,comp_user_accessuser,insert_stamp)";
    $sql.=" values ('$persnr','$nowDate','$oe','$amnr',1,'$invnr','$ident',NOW())";
    $insertId = $a->insert($sql);
}
$returnArray = array(
    'insertid'=>$insertId,
    'userRoles'=>$userRoles,
    'u'=>$u,
    'pa'=>$pa,
);

echo json_encode($returnArray);

