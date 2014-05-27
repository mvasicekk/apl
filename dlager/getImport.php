<?
require_once '../db.php';

    $term = $_GET['term'];
    $teil = $_GET['teil'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    if(strlen(trim($teil))==0) $teil = NULL;
    $dokuArray = $apl->getImporteMitTeil($teil,$term);

    
    $artikelArrayNeu = array();

    if($dokuArray!==NULL){
        $id = 0;
        foreach ($dokuArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['auftragsnr'],'value'=>$artikel['auftragsnr']));
            $id++;
        }
    }

    if($dokuArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>

