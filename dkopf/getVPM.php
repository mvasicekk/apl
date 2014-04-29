<?
require_once '../db.php';

    $term = $_GET['term'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $dokuArray = $apl->getVPMArray($term);

    $artikelArrayNeu = array();

    if($dokuArray!==NULL){
        $id = 0;
        foreach ($dokuArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['verp'].":".$artikel['name1']." ".$artikel['name2']." ".$artikel['name3'],'value'=>$artikel['verp']));
            $id++;
        }
    }

    if($dokuArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>

