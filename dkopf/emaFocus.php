<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $value = $_POST['value'];
    
    $imaid = substr($id, strrpos($id, '_')+1);
    
    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();

    $iA = $apl->getIMAInfoArray($imaid);
    $imanr = $iA[0]['imanr'];
    $kunde = intval(substr($imanr, strpos($imanr, '_')+1,3));
    $lastEmaNr = $apl->getLastEMANr($kunde);
    $emaNrNew = sprintf("EMA_%03d_%04d",$kunde,$lastEmaNr+1);
    $newValue = $value;
    if(strlen(trim($value))==0) $newValue=$emaNrNew;
    $returnArray = array(
	'id'=>$id,
	'imaid'=>$imaid,
	'imanr'=>$imanr,
	'kunde'=>$kunde,
	'lastEmaNr'=>$lastEmaNr,
	'emaNrNew'=>$emaNrNew,
	'value'=>$value,
	'newValue'=>$newValue,
	'user'=>$user,
    );
    echo json_encode($returnArray);

?>

