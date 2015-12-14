<?
session_start();
require_once '../db.php';

    $teil = $_GET['teil'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();
    $tia = $apl->getTeilInfoArray($teil);
    
    $returnArray = array(
	"teil"=>$teil,
	"tia"=>$tia,
    );
    
    echo json_encode($returnArray);
