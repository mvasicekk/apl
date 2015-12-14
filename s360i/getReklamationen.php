<?
require_once '../db.php';

    $inputData = $_GET;
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $reklamationen = $apl->getReklamationenArray();
    
    $returnArray = array(
	"inputData"=>$inputData,
	"reklamationen"=>$reklamationen,
    );
    
    echo json_encode($returnArray);
