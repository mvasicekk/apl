<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$f = $o->f;

$a = AplDB::getInstance();



$u = $_SESSION['user'];

$sql = "update hodnoceni_osobni_faktory set id_firma_faktor='".$f->id_firma_faktor."' where id='".$f->id."'";
$ar = $a->query($sql);
$returnArray = array(
	'ar'=>$ar,
	'f'=>$f,
	'u'=>$u
    );
    
echo json_encode($returnArray);
