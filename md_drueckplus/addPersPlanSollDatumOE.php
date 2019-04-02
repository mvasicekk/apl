<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);
$a = AplDB::getInstance();

$p = $o->p;
$o = $o->o;

$oe = $p->oe;
$datum = date('Y-m-d',strtotime($p->datum));
$persnr = $o->persnr;
$stunden = $o->regelarbzeit;

$u = $a->get_user_pc();

$insertSql = "insert into dzeitsoll (persnr,oe,stunden,datum,user) values('$persnr','$oe','$stunden','$datum','$u')";

$ar = $a->insert($insertSql);


$returnArray = array(
    'u' => $u,
    'ar'=>$ar,
    'insertSql'=>$insertSql,
    'p'=>$p,
    'o'=>$o,
    'datum'=>$datum,
    'persnr' => $persnr,
);

echo json_encode($returnArray);
