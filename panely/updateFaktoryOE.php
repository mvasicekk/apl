<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$foe = $o->foe;
$id = $o->id;
$oe = $o->oe;

$id_hodnoceni_faktory_oe = intval($foe->id_hodnoceni_faktory_oe);

$a = AplDB::getInstance();

if($id_hodnoceni_faktory_oe>0){
    // mel krizek -> smazu
    $sql = "delete from hodnoceni_faktory_oe where id='$id_hodnoceni_faktory_oe'";
    $a->query($sql);
    $id_hodnoceni_faktory_oe = 0;
}
else{
    // nemel krizek vlozim novy a vratim id
    $sql = "insert into hodnoceni_faktory_oe (id_faktor,oe) values('$id','$oe')";
    $id_hodnoceni_faktory_oe = $a->insert($sql);
}

$u = $_SESSION['user'];


$returnArray = array(
	'id_hodnoceni_faktory_oe'=>$id_hodnoceni_faktory_oe,
	'foe'=>$foe,
	'id'=>$id,
	'oe'=>$oe,
	'u'=>$u
    );
    
echo json_encode($returnArray);
