<?
session_start();
require_once '../db.php';
require_once '../sqldb.php';

$data = file_get_contents("php://input");
$o = json_decode($data);

$ffM = $o->ffM;


$a = AplDB::getInstance();
//$sqlDB = sqldb::getInstance();

$u = $_SESSION['user'];

$hodnoceni = intval($ffM->hodnoceni);
$id = $ffM->id;

if($hodnoceni<=9 && $hodnoceni>=0){
    $sql = "update hodnoceni_firemni set hodnoceni='$hodnoceni' where id='$id'";
    $ar = $a->query($sql);
}
$returnArray = array(
	'ar'=>$ar,
	'ffM'=>$ffM,
	'id'=>$id,
	'hodnoceni'=>$hodnoceni,
	'u'=>$u,
	'sql'=>$sql
    );
    
echo json_encode($returnArray);
