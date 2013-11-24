<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

$apl = AplDB::getInstance();


$id = $_POST['id'];
$v = intval(trim($_POST['v']));
$ok=0;

if($v!=0){
    $ia=$apl->getAuftragInfoArray($v);
    if($ia!==NULL) $ok=1;
}
else
    $ok=1;
    
$returnArray = array(
    'id' => $id,
    'v' => $v,
    'ok'=>$ok,
);

echo json_encode($returnArray);

?>
