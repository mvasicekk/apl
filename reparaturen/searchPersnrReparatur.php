<?
require_once '../db.php';

    $term = $_GET['term'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $persnrArray = $apl->getPersonalArray($term, TRUE, 1);

    $persnrArrayNeu = array();

    if($persnrArray!==NULL){
        $id = 0;
        foreach ($persnrArray as $pers){
            array_push($persnrArrayNeu, array('id'=>$id,'label'=>$pers['persnr'].": ".$pers['name'].' '.$pers['vorname'],'value'=>$pers['persnr']));
            $id++;
        }
    }

    if($persnrArray!==NULL)
        $returnArray = $persnrArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>
