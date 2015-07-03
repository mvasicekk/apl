<?
require_once '../db.php';

    $teil = $_GET['teil'];
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $teileInfo = $apl->getTeilInfoArray($teil);
    $dposInfo = $apl->getDposInfo($teil, NULL);

    $returnArray = array(
	'teil'=>$teil,
	'dkopfInfo'=>$teileInfo,
	'dposInfo'=>$dposInfo,
    );

//    AplDB::varDump($returnArray);
    echo json_encode($returnArray);

?>

