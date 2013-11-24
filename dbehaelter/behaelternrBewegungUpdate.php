<?
require_once '../db.php';

    $id = $_POST['id'];
    $behaelterNr = $_POST['value'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $artikelArray = $apl->getEinkArtikelArray($behaelterNr);
    

    echo json_encode(array(
                            'id'=>$id,
                            'artikelArray'=>$artikelArray,
        ));

?>
