<?
require_once '../db.php';

    $params = $_post['params'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $teileArray = NULL;
    if(strlen($params->t)>=3){
	$teileArray = $apl->getTeileNrArrayForKunde(NULL,$params->t);
    }
    

    $returnArray = array(
	't'=>$params->t,
	//'teileArray'=>$teileArray,
    );
    echo json_encode($returnArray);
    //$dokuArray = 55;
    //echo json_encode($dokuArray);

