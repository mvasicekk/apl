<?
require_once '../db.php';

    $e = $_GET['e'];
    
    // zjistim zda mam hodnotu value ulozenou v databazi artiklu

    $apl = AplDB::getInstance();

    $persnrArray = NULL;
    if(strlen($e)>=2){
	$persnrArray = $apl->getPersonalArrayMatch($e,TRUE,1);
	$persnrArray1 = $persnrArray['rows'];
    }
    
    if($persnrArray1!==NULL){
	foreach ($persnrArray1 as $i=>$row){
	    $persnrArray1[$i]['formattedPersnr'] = sprintf("%03d - %s %s",$row['persnr'],$row['name'],$row['vorname']);
	}
    }

    $returnArray = array(
	'e'=>$e,
	'persnrArray'=>$persnrArray1,
	'pA'=>$persnrArray
    );
    echo json_encode($returnArray);

