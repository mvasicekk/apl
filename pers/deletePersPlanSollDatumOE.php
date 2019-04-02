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

$u = $a->get_user_pc();

// smazu vzdy jen jeden radek, pokud mam v jeden den oe vicekrat budu muset smazat vicekrat, uvidim v sume hodin pri refreshi
$deleteSql = "delete from dzeitsoll where persnr='$persnr' and oe='$oe' and datum='$datum' limit 1";

$ar = $a->query($deleteSql);


$returnArray = array(
    'u' => $u,
    'ar'=>$ar,
    'deleteSql'=>$deleteSql,
    'p'=>$p,
    'o'=>$o,
    'datum'=>$datum,
    'persnr' => $persnr,
);

echo json_encode($returnArray);
