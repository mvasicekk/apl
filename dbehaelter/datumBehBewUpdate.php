<?
require_once '../db.php';

    $id = $_POST['id'];
    $datum = $_POST['value'];
    $im = $_POST['im'];
    $ex = $_POST['ex'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $datumDB = $apl->make_DB_datum($datum);
    $ar = $apl->updateBehBewDatum($im,$ex,$datumDB);
    

    echo json_encode(array(
                            'id'=>$id,
                            'ar'=>$ar,
        ));

?>
