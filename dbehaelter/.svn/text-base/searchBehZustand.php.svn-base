<?
require_once '../db.php';

    $term = $_GET['term'];
    $zustand_typ = $_GET['zustand_typ'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $artikelArray = $apl->getBehZustandArray($term,$zustand_typ);

    $artikelArrayNeu = array();

    if($artikelArray!==NULL){
        $id = 0;
        foreach ($artikelArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['zustand_id'].":".$artikel['zustand_text'],'value'=>$artikel['zustand_id']));
            $id++;
        }
    }

    if($artikelArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>
