<?
session_start();
require_once '../db.php';
require_once '../fns_dotazy.php';

    $id = $_POST['id'];
    $value = trim($_POST['value']);
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $reparaturID = substr($id, strlen('etinvnummer')+1, strpos($id, '_', strlen('etinvnummer')+1)-strlen('etinvnummer_'));
    $artnr = substr($id, strlen('etinvnummer_')+strlen($reparaturID)+1);
    $apl = AplDB::getInstance();

    $user = get_user_pc();
    $ar = $apl->updateReparaturPosInvnummer($reparaturID, $artnr, $value, $user);

    echo json_encode(array(
                            'id'=>$id,
                            'value'=>$value,
                            'artnr'=>$artnr,
                            'reparaturID'=>$reparaturID,
                            'ar'=>$ar,
        ));

?>
