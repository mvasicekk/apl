<?
require_once '../db.php';

    $id = $_POST['id'];
    $stk = $_POST['value'];
    $kunde = $_POST['kunde'];
    $behaelterNr = $_POST['behaelternr'];
    $datum = $_POST['datum'];

    // rozebrat id, abych zjistil zustand_id a platz_id
    // id vypada takto inventur_stk_(zustand_id)_(platz_id)

    $zustand_id = substr($id, strlen('inventur_stk')+1, strpos($id, '_', strlen('inventur_stk')+1)-strlen('inventur_stk_'));
    $platz_id = substr($id, strlen('inventur_stk_')+strlen($zustand_id)+1);
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $datumDB = $apl->make_DB_datum($datum);
    if(strlen($datum)==0)
        $datumDB = NULL;

    $kundeInfoArray = $apl->getKundeInfoArray($kunde);
    if(strlen(trim($behaelterNr))>0){
        $artikelArray = $apl->getEinkArtikelArray($behaelterNr);
    }

    if(($kundeInfoArray!=NULL) && ($artikelArray!=NULL)){
        //upravim pocet kusu
        $returnValue = $apl->updateBehaelterInventurStk($behaelterNr,$kunde,$stk,$zustand_id,$platz_id,$datumDB);
    }


    echo json_encode(array(
                            'id'=>$id,
                            'kunde'=>$kunde,
                            'returnValue'=>$returnValue,
                            'zustand_id'=>$zustand_id,
                            'platz_id'=>$platz_id,
        ));
?>
