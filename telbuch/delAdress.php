<?
require_once '../db.php';

    $id = $_POST['id'];

    $apl = AplDB::getInstance();

    $adressId = substr($id, strpos($id, '_')+1);
    
    $ar = $apl->setAdressDeleted($adressId);
    
    echo json_encode(array(
                            'id'=>$id,
			    'adressId'=>$adressId,
			    'ar'=>$ar,
    ));
?>
