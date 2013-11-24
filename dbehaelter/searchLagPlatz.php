<?
require_once '../db.php';

    $term = $_GET['term'];

    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $lagPlatzArray = $apl->getBehaelterLagerplatzArray(1,$term);
    $artikelArray = $lagPlatzArray;

    $artikelArrayNeu = array();

    if($artikelArray!==NULL){
        $id = 0;
        foreach ($artikelArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['platz_id'],'value'=>$artikel['platz_id']));
            $id++;
        }
    }

    if($artikelArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>
