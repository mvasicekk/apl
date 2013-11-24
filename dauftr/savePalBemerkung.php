<?
require_once '../db.php';

    $gid = $_POST['gid'];
    $id = $_POST['id'];
    $val = trim($_POST['value']);
    $apl = AplDB::getInstance();

    // vytahnu stavajici poznamku k palete
    
    $ar = $apl->saveGPalBemerkung($gid,$val);
    
    echo json_encode(array('ar'=>$ar,'id'=>$id));
?>