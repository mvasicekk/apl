<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$persnr = $o->persnr;
$von = $o->von;
$bis = $o->bis;

$a = AplDB::getInstance();

$u = $_SESSION['user'];

$faktorup = 0.3;
$faktordown = 0.15;
$premiepct = 10;
$persvon = $persnr;
$persbis = $persnr;

$von1 = date('d.m.Y',strtotime($von));
$bis1 = date('d.m.Y',strtotime($bis));

$hfPremieArray = $a->getHFPremieArray($von1, $bis1, $persvon, $persbis, $faktorup, $faktordown, $premiepct);


$returnArray = array(
    'u' => $u,
    'von'=>$von1,
    'bis'=>$bis1,
    'hfpremiearray' => $hfPremieArray,
    'sql' => $sql,
);

echo json_encode($returnArray);
