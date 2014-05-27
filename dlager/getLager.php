<?
require_once '../db.php';

    $term = $_GET['term'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $dokuArray = $apl->getLagerArray($term);

    
    $artikelArrayNeu = array();

    if($dokuArray!==NULL){
        $id = 0;
        foreach ($dokuArray as $key=>$artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$key.":".$artikel,'value'=>$key));
            $id++;
        }
    }

    if($dokuArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>

