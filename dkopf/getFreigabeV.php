<?
require_once '../db.php';

    $term = $_GET['term'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $dokuArray = $apl->getFreigabeVom($term);

    $returnArray = array(
	'term'=>$term,
	'freigabevom'=>$dokuArray
    );
    
    echo json_encode($returnArray);
