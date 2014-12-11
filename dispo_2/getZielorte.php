<?
require_once '../db.php';

    $term = $_GET['term'];
    $kunde = $_GET['kd'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $dokuArray = $apl->getZielorteArray($kunde,$term);

    $artikelArrayNeu = array();

    if($dokuArray!==NULL){
        $id = 0;
        foreach ($dokuArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['zielort'],'value'=>$artikel['id']));
            $id++;
        }
    }

    if($dokuArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);
    //$dokuArray = 55;
    //echo json_encode($dokuArray);

?>

