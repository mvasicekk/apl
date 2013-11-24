<?
require_once '../db.php';

    $term = $_GET['term'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $artikelArray = $apl->getBehaelterArray($term, 1);

    $artikelArrayNeu = array();

    if($artikelArray!==NULL){
        $id = 0;
        foreach ($artikelArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['artnr'].":".$artikel['name1'],'value'=>$artikel['artnr']));
            $id++;
        }
    }

    if($artikelArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>
