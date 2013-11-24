<?
session_start();
require_once '../fns_dotazy.php';
require_once '../db.php';

    $id = $_POST['id'];

    // rozebrat id, abych zjistil zustand_id a platz_id
    // id vypada takto inventur_stk_(zustand_id)_(platz_id)

    $behbewid = substr($id, strrpos($id, '_')+1);

    $apl = AplDB::getInstance();

    $ident = get_user_pc();

    $apl->delBehInvId($behbewid);

    echo json_encode(array(
                            'id'=>$id,
                            'behbewid'=>$behbewid,
                            'ident'=>$ident,
        ));
?>
