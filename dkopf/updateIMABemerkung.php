<?
 session_start();
?>

<?
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id=$_POST['id'];
    $value = trim($_POST['value']);

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $ar = 0;
    $user = get_user_pc();

    $imaid = substr($id, strrpos($id, '_')+1);
    $ar = $apl->updateIMAField('bemerkung',$value,$imaid);
    $returnArray = array(
	'ar'=>$ar,
	'id'=>$id,
	'imaid'=>$imaid,
	'value'=>$value,
	'user'=>$user,
    );
    echo json_encode($returnArray);

?>

