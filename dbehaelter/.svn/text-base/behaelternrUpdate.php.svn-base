<?
require_once '../db.php';

    $id = $_POST['id'];
    $behaelterNr = $_POST['value'];
    $kunde = $_POST['kunde'];
    $datum = $_POST['datum'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $datumDB = $apl->make_DB_datum($datum);
    if(strlen($datum)==0) $datumDB = NULL;

    $artikelArray = $apl->getEinkArtikelArray($behaelterNr);
    $kundeInfoArray = $apl->getKundeInfoArray($kunde);

        if(($kundeInfoArray!=NULL) && ($artikelArray!=NULL)){
        // vytahnu pocty kusu v inventure
        $stkArray = $apl->getBehaelterInventurStArray($behaelterNr,$kunde,$datumDB);
    }

    echo json_encode(array(
                            'id'=>$id,
                            'kundeInfoArray'=>$kundeInfoArray,
                            'artikelArray'=>$artikelArray,
                            'stkArray'=>$stkArray
                            ,'datumDB'=>$datumDB
        ));

?>
