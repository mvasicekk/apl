<?
require_once './db.php';

    $inputData = $_GET;
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $daysBack = intval($inputData['daysBack']);
    if($daysBack<7){
	$daysBack = 7;
    }
    
    $apl = AplDB::getInstance();

    $returnArray = array(
	"inputData"=>$inputData,
	"leistungTablearray"=>$apl->getLeistungTable(),
	"graphTablearray"=>$apl->getLeistungTable($daysBack),
    );
    
    echo json_encode($returnArray);
?>

