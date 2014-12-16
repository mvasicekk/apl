<?
require_once './db.php';

    $inputData = $_GET;
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $returnArray = array(
	"inputData"=>$inputData,
	"leistungTablearray"=>$apl->getLeistungTable(),
    );
    
    echo json_encode($returnArray);
?>

