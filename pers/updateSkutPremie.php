<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$id = intval($o->premie->skutId);
$betrag = intval($o->premie->skutBetrag);
$jm = $o->jm;
$persnr = $o->persnr;

$a = AplDB::getInstance();

$u = $_SESSION['user'];


if(intval($id)==0){
    // nemam v tabulce -> vlozim novy
    $datum = $jm.'-01';
    $id_premie = 8; // hf_reparaturen_premie
    $sql = "insert into dperspremie (persnr,datum,betrag,id_premie)";
    $sql.=" values('$persnr','$datum','$betrag','$id_premie')";
    $insertId = $a->insert($sql);
}
else{
    // update stavajiciho radku podle id
    $sql = "update dperspremie set betrag='$betrag' where id='$id'";
    $ar = $a->query($sql);
}
$returnArray = array(
    'insertid'=>$insertId,
    'ar'=>$ar,
    'id' => $id,
    'betrag' => $betrag,
    'jm' => $jm,
    'persnr' => $persnr,
    'u' => $u,
    'sql' => $sql,
);

echo json_encode($returnArray);
