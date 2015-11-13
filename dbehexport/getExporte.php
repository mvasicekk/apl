<?
require_once '../db.php';

    $e = $_GET['e'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $teileArray = NULL;
    if(strlen($e)>=3){
	$exporteArray = $apl->getExporteMatch($e,TRUE);
    }
    
    if($exporteArray!==NULL){
	foreach ($exporteArray as $i=>$row){
	    $exporteArray[$i]['formattedExport'] = sprintf("%03d - %08d",$row['kunde'],$row['ex']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'exporteArray'=>$exporteArray,
    );
    echo json_encode($returnArray);

