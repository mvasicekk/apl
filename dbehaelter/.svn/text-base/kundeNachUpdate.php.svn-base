<?
require_once '../db.php';

    $id = $_POST['id'];
    $kunde = $_POST['value'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();


    $kundeInfoArray = $apl->getKundeInfoArray($kunde);


    echo json_encode(array(
                            'id'=>$id,
                            'kunde'=>$value,
                            'kundeInfoArray'=>$kundeInfoArray,
        ));
?>
