<?

session_start();
require_once '../db.php';

$data = file_get_contents("php://input");
$o = json_decode($data);


$premie = $o->premie;
$id = intval($o->premie->skutId);
$betrag = intval($o->premie->skutBetrag);
$jm = $o->jm;
$persnr = $o->persnr;
$lockchanged = $o->lockchanged;

$a = AplDB::getInstance();

$u = $_SESSION['user'];


if(intval($id)==0){
    // nemam v tabulce -> vlozim novy
    $datum = $jm.'-01';
    $id_premie = 8; // hf_reparaturen_premie
    $sql = "insert into dperspremie (persnr,datum,betrag,id_premie,last_edit)";
    $sql.=" values('$persnr','$datum','$betrag','$id_premie','$u')";
    $insertId = $a->insert($sql);
    $id = $insertId;
}
else{
    // update stavajiciho radku podle id
    $sql = "update dperspremie set betrag='$betrag',last_edit='$u' where id='$id'";
    $ar = $a->query($sql);
}

if($lockchanged){
    $locked = $premie->locked==TRUE?1:0;
    $sql = "update dperspremie set locked='$locked',last_edit='$u' where id='$id'";
    $arLock = $a->query($sql);
}
$returnArray = array(
    'arLock'=>$arLock,
    'lockchanged'=>$lockchanged,
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
