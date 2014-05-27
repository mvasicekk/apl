<?
require_once '../db.php';

    $term = $_GET['term'];
    $teil = $_GET['teil'];
    $im = $_GET['im'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    if(strlen(trim($teil))==0) $teil = NULL;
    $dokuArray = $apl->getPaletteMitAuftragTeil($term,$im,$teil);

    
    $artikelArrayNeu = array();

    if($dokuArray!==NULL){
        $id = 0;
        foreach ($dokuArray as $artikel){
            array_push($artikelArrayNeu, array('id'=>$id,'label'=>$artikel['pal'],'value'=>$artikel['pal']));
            $id++;
        }
    }

    if($dokuArray!==NULL)
        $returnArray = $artikelArrayNeu;
    else
        $returnArray = NULL;

    echo json_encode($returnArray);

?>

