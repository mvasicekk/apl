<?
require_once '../db.php';

    $id = $_POST['id'];
    $kundeKontoStk = intval($_POST['kundekontostk']);
    $kunde = $_POST['kunde'];
    $behaelterNr = $_POST['behaelternr'];
    $datum = $_POST['datum'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $datumDB = $apl->make_DB_datum($datum);
    if(strlen($datum)==0)
        $datumDB = NULL;

    $kundeInfoArray = $apl->getKundeInfoArray($kunde);
    if(strlen(trim($behaelterNr))>0){
        $artikelArray = $apl->getEinkArtikelArray($behaelterNr);
    }

    if(($kundeInfoArray!=NULL) && ($artikelArray!=NULL) && ($datumDB!=NULL)){
        //upravim pocet kusu
//        $returnValue = $apl->updateBehaelterKundeKontoStk($behaelterNr,$kunde,$kundeKontoStk,$datumDB);
        $stkArray = $apl->getBehaelterInventurStArray($behaelterNr,$kunde,$datumDB);
    }


    echo json_encode(array(
                            'id'=>$id,
                            'kunde'=>$kunde,
                            'kundeKontoStk'=>$kundeKontoStk,
                            'returnValue'=>$returnValue,
                            'stkArray'=>$stkArray
        ));
?>
