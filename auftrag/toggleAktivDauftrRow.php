<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$a = AplDB::getInstance();
$ar = 0;
$r = $o->r;
$id_dauftr = $r->id_dauftr;
$kz_aktiv = $r->kz_aktiv;
$ident = $a->get_user_pc();
$user = $ident;

$sql = "update dauftr set kz_aktiv='$kz_aktiv' where id_dauftr='$id_dauftr'";
$ar = $a->query($sql);
$returnArray = array(
    'ar' => $ar,
    'sql' => $sql,
    'id_dauftr'=>$id_dauftr,
    'kz_aktiv'=>$kz_aktiv
);

echo json_encode($returnArray);
