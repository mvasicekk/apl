<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$m = $o->m;



$a = AplDB::getInstance();
$u = $_SESSION['user'];

$persnr = $o->persnr;
$datum = date('Y-m-d');
$oe1 = $a->getPersOEInfo($persnr);
if($oe1!==NULL){
    $oe = $oe1['regeloe'];
}
else{
    $oe = NULL;
}

$invnr = trim(intval($m->CISLO));
$amnr = $invnr;
$amnr_typ = 2;	// v amnr nebude cislo skladove karty ale inventarni cislo
$ausgabe_stk = 1;
$comp_user_accessuser = $a->get_user_pc();

$sql = "insert into dambew (PersNr,Datum,oe,AMNr,amnr_typ,AusgabeStk,invnr,comp_user_accessuser,insert_stamp)";
$sql.=" values('$persnr','$datum','$oe','$amnr','$amnr_typ','$ausgabe_stk','$invnr','$comp_user_accessuser',NOW())";
$insertId = $a->insert($sql);

$returnArray = array(
    'idDel'=>$idDel,
    'delRows'=>$delRows,
    'm'=>$m,
    'insertId'=>$insertId,
    'persnr' => $persnr,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
