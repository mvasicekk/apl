<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

    $apl = AplDB::getInstance();

    $id = $_POST['id'];
    $value = $_POST['value'];
    $repid = $_POST['repid'];
    $user = get_user_pc();

    $ar = $apl->updateReparaturKopf($repid,'bemerkung',$value);

    $returnArray = array(
        'id'=>$id,
        'value'=>$value,
        'repid'=>$repid,
        'ar'=>$ar,
    );

    echo json_encode($returnArray);

?>
